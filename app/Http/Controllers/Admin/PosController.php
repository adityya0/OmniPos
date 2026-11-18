<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Tampilan utama aplikasi Point of Sales (POS Kasir).
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        // Hanya tampilkan kategori yang memiliki produk aktif
        $categories = Category::whereHas('products', function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id)->where('is_active', true);
        })->orderBy('name')->get();

        // Hitung kuantitas pesanan online dropship yang sedang berstatus antrean aktif (menunggu approval, siap kemas, atau dipacking)
        $pendingQuantities = OrderItem::whereHas('order', function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id)
                ->where('transaction_type', 'dropship')
                ->whereIn('status', ['menunggu_approval', 'menunggu_packing', 'dipacking']);
        })
            ->selectRaw('product_id, SUM(quantity) as total_pending')
            ->groupBy('product_id')
            ->pluck('total_pending', 'product_id');

        $products = Product::with(['category', 'tiers'])
            ->where('supplier_id', $supplier->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($product) use ($pendingQuantities) {
                $product->pending_online_qty = (int) ($pendingQuantities[$product->id] ?? 0);
                $product->total_physical_stock = $product->stock + $product->pending_online_qty;
                return $product;
            });

        return view('admin.pos.index', compact('supplier', 'categories', 'products'));
    }

    /**
     * Memproses transaksi POS offline, validasi stok, dan kurangi stok realtime.
     */
    public function checkout(Request $request): JsonResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Toko supplier tidak ditemukan.'], 403);
        }

        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cash,transfer,qris'],
            'cash_amount' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.tier_name' => ['nullable', 'string', 'max:100'],
            'items.*.unit_multiplier' => ['nullable', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $supplier) {
                $subtotal = 0;
                $totalCost = 0;
                $itemsToCreate = [];

                foreach ($validated['items'] as $itemData) {
                    $product = Product::where('id', $itemData['product_id'])
                        ->where('supplier_id', $supplier->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $multiplier = max(1, intval($itemData['unit_multiplier'] ?? 1));
                    $qty = intval($itemData['quantity']);
                    $totalPhysicalNeeded = $qty * $multiplier;

                    if ($product->stock < $totalPhysicalNeeded) {
                        $tierInfo = !empty($itemData['tier_name']) ? " ({$itemData['tier_name']})" : "";
                        throw new \Exception("Stok tidak mencukupi untuk {$product->name}{$tierInfo}. Sisa stok fisik: {$product->stock} (dibutuhkan {$totalPhysicalNeeded}).");
                    }

                    $unitPrice = isset($itemData['unit_price']) && floatval($itemData['unit_price']) > 0
                        ? floatval($itemData['unit_price'])
                        : floatval($product->selling_price * $multiplier);

                    $itemSubtotal = $unitPrice * $qty;
                    $itemCost = ($product->cost_price * $multiplier) * $qty;
                    $itemProfit = $itemSubtotal - $itemCost;

                    $subtotal += $itemSubtotal;
                    $totalCost += $itemCost;

                    // Kurangi stok fisik gudang toko sesuai total unit fisik yang terjual
                    $product->decrement('stock', $totalPhysicalNeeded);

                    $tierName = !empty($itemData['tier_name']) ? $itemData['tier_name'] : 'Eceran';
                    $productDisplayName = $multiplier > 1 || !empty($itemData['tier_name'])
                        ? "{$product->name} [{$tierName}]"
                        : $product->name;

                    $itemsToCreate[] = [
                        'product_id' => $product->id,
                        'product_name' => $productDisplayName,
                        'product_sku' => $product->sku,
                        'tier_name' => $tierName,
                        'unit_multiplier' => $multiplier,
                        'cost_price' => $product->cost_price * $multiplier,
                        'unit_price' => $unitPrice,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'profit' => $itemProfit,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $discount = floatval($validated['discount'] ?? 0);
                $finalAmount = max(0, $subtotal - $discount);
                $netProfit = $finalAmount - $totalCost;

                $cashAmount = floatval($validated['cash_amount'] ?? 0);
                $changeAmount = 0;
                if ($validated['payment_method'] === 'cash') {
                    if ($cashAmount < $finalAmount) {
                        throw new \Exception("Uang tunai (Rp " . number_format($cashAmount, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($finalAmount, 0, ',', '.') . ")");
                    }
                    $changeAmount = $cashAmount - $finalAmount;
                } else {
                    $cashAmount = $finalAmount;
                }

                $invoiceNumber = Order::generateInvoiceNumber();

                $order = Order::create([
                    'invoice_number' => $invoiceNumber,
                    'supplier_id' => $supplier->id,
                    'user_id' => Auth::id(),
                    'transaction_type' => 'pos_offline',
                    'customer_name' => $validated['customer_name'] ?: 'Pelanggan Offline',
                    'total_cost' => $totalCost,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'final_amount' => $finalAmount,
                    'net_profit' => $netProfit,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'paid',
                    'cash_amount' => $cashAmount,
                    'change_amount' => $changeAmount,
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($itemsToCreate as &$item) {
                    $item['order_id'] = $order->id;
                }
                OrderItem::insert($itemsToCreate);

                return $order->load('items');
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi POS berhasil disimpan dan stok telah diperbarui.',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Memperbarui mode tampilan harga kasir POS (global_filter vs card_buttons).
     */
    public function updatePricingMode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pos_pricing_mode' => 'required|in:global_filter,card_buttons',
        ]);

        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Toko supplier tidak ditemukan.',
            ], 403);
        }

        $supplier->update([
            'pos_pricing_mode' => $validated['pos_pricing_mode'],
        ]);

        return response()->json([
            'success' => true,
            'pos_pricing_mode' => $supplier->pos_pricing_mode,
            'message' => 'Mode tampilan harga kasir berhasil diubah.',
        ]);
    }
}
