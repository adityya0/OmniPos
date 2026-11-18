<?php

namespace App\Http\Controllers\Dropshipper;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Tampilkan riwayat pesanan dropshipper.
     */
    public function index(): View
    {
        $user = Auth::user();

        $orders = Order::with(['supplier', 'items.product'])
            ->where('user_id', $user->id)
            ->where('transaction_type', 'dropship')
            ->latest()
            ->paginate(15);

        return view('dropshipper.orders.index', compact('orders'));
    }

    /**
     * Simpan pesanan dropship baru dari dropshipper (Wajib Resi & Approval Admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'courier_name' => ['required', 'string', 'max:100'],
            'shipping_receipt_number' => ['required', 'string', 'max:100'],
            'shipping_label_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'marketplace_deadline' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'shipping_receipt_number.required' => 'Nomor resi wajib diisi! Tim packing tidak dapat memproses pesanan tanpa nomor resi.',
            'courier_name.required' => 'Ekspedisi / kurir wajib dipilih.',
            'product_id.required' => 'Produk wajib dipilih.',
            'quantity.min' => 'Jumlah pesanan minimal 1.',
        ]);

        $product = Product::with('supplier')->findOrFail($validated['product_id']);

        // Cegah pesanan jika kemitraan sedang dalam proses pelepasan
        $relation = \App\Models\SupplierDropshipper::where('dropshipper_id', $user->id)
            ->where('supplier_id', $product->supplier_id)
            ->first();

        if ($relation && $relation->status === 'pending_disconnect') {
            return back()->with('error', "Tidak dapat membuat pesanan: Anda sedang dalam proses pengajuan pelepasan kemitraan dengan toko [{$product->supplier?->store_name}]. Batalkan permohonan terlebih dahulu jika ingin memesan kembali.");
        }

        // Pastikan stok mencukupi
        if ($product->stock < $validated['quantity']) {
            return back()->with('error', "Stok produk {$product->name} tidak mencukupi (Sisa stok: {$product->stock}).");
        }

        // Upload file label resi jika ada
        $labelPath = null;
        if ($request->hasFile('shipping_label_file')) {
            $labelPath = $request->file('shipping_label_file')->store('shipping_labels', 'public');
        }

        DB::beginTransaction();
        try {
            // Generate Invoice & Order Code
            $invoiceNumber = Order::generateInvoiceNumber();
            $orderCode = 'DPS-' . strtoupper(Str::random(8));

            $unitCost = $product->cost_price ?: ($product->selling_price * 0.7);
            $unitDropship = $product->dropship_price ?: $product->selling_price;
            $quantity = $validated['quantity'];

            $totalCost = $unitCost * $quantity;
            $totalAmount = $unitDropship * $quantity;
            $netProfit = max(0, $totalAmount - $totalCost);

            // Buat order dengan status: 'menunggu_approval' (HOLD oleh Admin Toko)
            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'order_code' => $orderCode,
                'supplier_id' => $product->supplier_id,
                'user_id' => $user->id,
                'transaction_type' => 'dropship',
                'customer_name' => $validated['customer_name'],
                'shipping_receipt_number' => $validated['shipping_receipt_number'],
                'courier_name' => $validated['courier_name'],
                'shipping_label_file' => $labelPath,
                'status' => 'menunggu_approval', // HOLD STATUS OLEH ADMIN TOKO
                'payment_status' => 'paid',
                'payment_method' => 'transfer',
                'total_cost' => $totalCost,
                'subtotal' => $totalAmount,
                'discount' => 0,
                'final_amount' => $totalAmount,
                'net_profit' => $netProfit,
                'marketplace_deadline' => $validated['marketplace_deadline'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Buat Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku ?: 'SKU-' . $product->id,
                'cost_price' => $unitCost,
                'unit_price' => $unitDropship,
                'quantity' => $quantity,
                'subtotal' => $totalAmount,
                'profit' => $netProfit,
            ]);

            // Kurangi stok produk secara realtime
            $product->decrement('stock', $quantity);

            DB::commit();

            return redirect()->route('dropshipper.orders.index')
                ->with('success', "Pesanan {$order->order_code} berhasil dibuat dengan No. Resi: {$order->shipping_receipt_number}! Status: Menunggu Approval Admin Toko sebelum diteruskan ke Tim Packing.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan pesanan oleh dropshipper sendiri (jika belum dikemas/dikirim).
     * Stok produk otomatis dikembalikan ke inventori toko supplier secara realtime.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();

        // Pastikan order ini milik dropshipper yang sedang login
        if ($order->user_id !== $user->id) {
            abort(403, 'Akses pesanan tidak diizinkan.');
        }

        // Hanya boleh dibatalkan jika masih di tahap menunggu_approval atau menunggu_packing
        if (!in_array($order->status, ['menunggu_approval', 'menunggu_packing'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah dalam proses pengemasan atau telah dikirim ke kurir.');
        }

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = $validated['cancel_reason'] ?? 'Dibatalkan oleh dropshipper (Permintaan pembeli marketplace)';

        DB::transaction(function () use ($order, $reason) {
            // Kembalikan kuantitas stok ke produk toko supplier
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->status = 'dibatalkan';
            $order->cancel_reason = $reason;
            $order->cancelled_by = 'dropshipper';
            $order->cancel_stage = 'Mandiri Dropshipper';
            $order->save();
        });

        return back()->with('success', "Pesanan {$order->order_code} berhasil dibatalkan. Stok otomatis dikembalikan ke toko supplier.");
    }
}

