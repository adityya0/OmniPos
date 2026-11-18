<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PackingQueueController extends Controller
{
    /**
     * Tampilkan antrean pesanan dropship yang menunggu packing & dikirim.
     * Jika role adalah 'tim_packing', hanya tampilkan pesanan yang SUDAH DI-APPROVE admin.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $supplier = $user->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $isTimPacking = $user->isTimPacking();

        // Hitung badge
        $countMenungguApproval = Order::where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship')
            ->where('status', 'menunggu_approval')
            ->count();

        $countMenungguPacking = Order::where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship')
            ->where('status', 'menunggu_packing')
            ->count();

        $countDipacking = Order::where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship')
            ->where('status', 'dipacking')
            ->count();

        $countSelesai = Order::where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship')
            ->whereIn('status', ['dikirim', 'selesai'])
            ->count();

        $countDibatalkan = Order::where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship')
            ->where('status', 'dibatalkan')
            ->count();

        $countSemua = $countMenungguApproval + $countMenungguPacking + $countDipacking + $countSelesai + $countDibatalkan;
        if ($isTimPacking) {
            $countSemua -= $countMenungguApproval;
        }

        // Default filter: jika ada pesanan menunggu approval dan user adalah admin, utamakan 'menunggu_approval' agar langsung terlihat
        if ($request->filled('status')) {
            $statusFilter = $request->input('status');
        } else {
            $statusFilter = (!$isTimPacking && $countMenungguApproval > 0) ? 'menunggu_approval' : 'menunggu_packing';
        }

        // Tim packing TIDAK BOLEH melihat menunggu_approval
        if ($isTimPacking && $statusFilter === 'menunggu_approval') {
            $statusFilter = 'menunggu_packing';
        }

        $query = Order::with(['items.product', 'dropshipper'])
            ->where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship');

        // Proteksi: Tim packing hanya melihat pesanan yang sudah di-approve
        if ($isTimPacking) {
            $query->where('status', '!=', 'menunggu_approval');
        }

        // Ambil semua antrean pesanan dropship toko agar tab status dan live-search di frontend responsif instan
        $orders = $query->orderByRaw('CASE WHEN marketplace_deadline IS NULL THEN 1 ELSE 0 END, marketplace_deadline ASC')
            ->latest('id')
            ->get();

        return Inertia::render('Packing/Index', [
            'supplier' => $supplier,
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'isTimPacking' => $isTimPacking,
            'counts' => [
                'menunggu_approval' => $countMenungguApproval,
                'menunggu_packing'  => $countMenungguPacking,
                'dipacking'         => $countDipacking,
                'dikirim'           => $countSelesai,
                'dibatalkan'        => $countDibatalkan,
                'semua'             => $countSemua,
            ],
        ]);
    }

    /**
     * Admin Toko: Setujui Pesanan Dropship (Hold -> Menunggu Packing).
     * Setelah disetujui, pesanan baru akan muncul di layar Tim Packing.
     */
    public function approve(Request $request, Order $order): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.packing.index');
        }

        $user = Auth::user();
        $supplier = $user->activeSupplier();
        if (!$supplier || $order->supplier_id !== $supplier->id) {
            abort(403);
        }

        if ($user->isTimPacking()) {
            abort(403, 'Aktor packing tidak memiliki wewenang approval pesanan.');
        }

        // Cek apakah dropshipper sudah mengisi resi
        if (empty($order->shipping_receipt_number)) {
            return back()->with('error', 'Pesanan tidak dapat disetujui karena dropshipper belum melampirkan nomor resi!');
        }

        $order->status = 'menunggu_packing';
        $order->save();

        return redirect()->route('admin.packing.index', ['status' => 'menunggu_packing'])
            ->with('success', "Pesanan {$order->order_code} berhasil disetujui! Kini tampil di antrean Tim Packing.");
    }

    /**
     * Admin Toko: Batalkan Pesanan Dropship di tahap manapun (Stok dikembalikan jika belum dikirim).
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.packing.index');
        }

        $user = Auth::user();
        $supplier = $user->activeSupplier();
        if (!$supplier || $order->supplier_id !== $supplier->id) {
            abort(403);
        }

        if ($user->isTimPacking()) {
            abort(403, 'Tim packing tidak memiliki wewenang pembatalan pesanan.');
        }

        // Tidak boleh batalkan pesanan yang sudah dibatalkan
        if ($order->status === 'dibatalkan') {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        $validated = $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'cancel_reason.required' => 'Alasan pembatalan wajib diisi.',
            'cancel_reason.min'      => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        $stageLabel = match ($order->status) {
            'menunggu_approval' => 'Menunggu Approval',
            'menunggu_packing'  => 'Siap Kemas',
            'dipacking'         => 'Sedang Dipacking',
            'dikirim'           => 'Sudah Dikirim',
            'selesai'           => 'Selesai',
            default             => $order->status,
        };

        $stockRestored = in_array($order->status, [
            'menunggu_approval', 'menunggu_packing', 'dipacking'
        ]);

        DB::transaction(function () use ($order, $validated, $user, $stageLabel, $stockRestored) {
            // Kembalikan stok jika belum benar-benar dikirim ke kurir
            if ($stockRestored) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $order->status        = 'dibatalkan';
            $order->cancel_reason = $validated['cancel_reason'];
            $order->cancelled_by  = 'admin';
            $order->cancel_stage  = $stageLabel;
            $order->save();
        });

        $stockNote = $stockRestored 
            ? '. Stok produk telah dikembalikan ke inventori.' 
            : ' (stok fisik tidak dikembalikan otomatis karena pesanan sudah diserahkan ke kurir).';

        return redirect()->route('admin.packing.index')
            ->with('success', "Pesanan {$order->order_code} berhasil dibatalkan dari tahap \"{$stageLabel}\"{$stockNote}");
    }

    /**
     * Admin Toko: Tolak Pesanan Dropship di tahap menunggu_approval saja (shortcut tanpa alasan wajib).
     * @deprecated Gunakan cancel() untuk pembatalan universal.
     */
    public function reject(Request $request, Order $order): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.packing.index');
        }

        $user = Auth::user();
        $supplier = $user->activeSupplier();
        if (!$supplier || $order->supplier_id !== $supplier->id) {
            abort(403);
        }

        if ($user->isTimPacking()) {
            abort(403, 'Aktor packing tidak memiliki wewenang pembatalan pesanan.');
        }

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($order, $validated) {
            if (in_array($order->status, ['menunggu_approval', 'menunggu_packing'])) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $order->status       = 'dibatalkan';
            $order->cancel_reason = $validated['cancel_reason'] ?? 'Ditolak oleh admin toko.';
            $order->cancelled_by  = 'admin';
            $order->cancel_stage  = 'Menunggu Approval';
            $order->save();
        });

        return redirect()->route('admin.packing.index')
            ->with('success', "Pesanan {$order->order_code} ditolak/dibatalkan. Stok telah dikembalikan.");
    }

    /**
     * Tim Packing / Admin: Update status packing pesanan (e.g., dipacking, dikirim, selesai).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.packing.index');
        }

        $supplier = Auth::user()->activeSupplier();
        if (!$supplier || $order->supplier_id !== $supplier->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:menunggu_packing,dipacking,dikirim,selesai,dibatalkan'],
            'shipping_receipt_number' => ['nullable', 'string', 'max:100'],
        ]);

        // Proteksi: Tim packing tidak boleh mengirim pesanan tanpa nomor resi
        $receiptNumber = ($validated['shipping_receipt_number'] ?? null) ?: $order->shipping_receipt_number;
        if (in_array($validated['status'], ['dipacking', 'dikirim', 'selesai']) && empty($receiptNumber)) {
            return back()->with('error', 'Gagal update status! Pesanan ini belum memiliki nomor resi.');
        }

        $order->status = $validated['status'];
        if (!empty($validated['shipping_receipt_number'])) {
            $order->shipping_receipt_number = $validated['shipping_receipt_number'];
        }
        $order->save();

        return redirect()->route('admin.packing.index', ['status' => $order->status])
            ->with('success', "Status pesanan {$order->order_code} diperbarui → " . strtoupper($order->status) . ".");
    }
}
