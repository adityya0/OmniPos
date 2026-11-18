<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $supplier = $user->activeSupplier();

        if (!$supplier) {
            abort(403, 'Akses ditolak. Akun Anda tidak terhubung dengan toko supplier.');
        }

        $days = in_array((int) $request->input('days', 14), [7, 14, 30], true)
            ? (int) $request->input('days', 14)
            : 14;

        $leadTimeDays = max(1, (int) ($supplier->stock_lead_time_days ?? 3));
        $targetDays = max($leadTimeDays + 1, (int) ($supplier->stock_target_days ?? 14));
        $startDate = now()->subDays($days - 1)->startOfDay();

        // Total terjual per produk dalam rentang hari (POS + Dropship aktif/selesai)
        $salesByProduct = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.supplier_id', $supplier->id)
            ->where('orders.status', '!=', 'dibatalkan')
            ->where('orders.created_at', '>=', $startDate)
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw("SUM(CASE WHEN orders.transaction_type = 'pos_offline' THEN order_items.quantity ELSE 0 END) as pos_sold"),
                DB::raw("SUM(CASE WHEN orders.transaction_type = 'dropship' THEN order_items.quantity ELSE 0 END) as dropship_sold")
            )
            ->groupBy('order_items.product_id')
            ->get()
            ->keyBy('product_id');

        $products = Product::with('category')
            ->where('supplier_id', $supplier->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Cari kecepatan tertinggi untuk klasifikasi Fast Moving yang proporsional
        $maxVelocity = 0.0;
        foreach ($products as $p) {
            $sold = (int) ($salesByProduct[$p->id]->total_sold ?? 0);
            $vel = $sold / $days;
            if ($vel > $maxVelocity) {
                $maxVelocity = $vel;
            }
        }

        $analyzedProducts = $products->map(function (Product $product) use ($salesByProduct, $days, $leadTimeDays, $targetDays, $maxVelocity) {
            $salesRow = $salesByProduct->get($product->id);
            $soldQty = (int) ($salesRow->total_sold ?? 0);
            $posSold = (int) ($salesRow->pos_sold ?? 0);
            $dropshipSold = (int) ($salesRow->dropship_sold ?? 0);

            $dailyVelocity = round($soldQty / $days, 2);
            $safetyStock = (int) ($product->safety_stock ?? 0);
            $availableOnline = max(0, (int) $product->stock - $safetyStock);

            $daysRemaining = $dailyVelocity > 0
                ? round($product->stock / $dailyVelocity, 1)
                : null;

            $reorderPoint = (int) ceil(($dailyVelocity * $leadTimeDays) + $safetyStock);
            $desiredStock = (int) ceil(($dailyVelocity * $targetDays) + $safetyStock);

            // Klasifikasi pergerakan barang
            if ($soldQty === 0 || $dailyVelocity < 0.2) {
                $movement = 'slow';
            } elseif ($dailyVelocity >= 1.0 || ($maxVelocity > 0 && $dailyVelocity >= $maxVelocity * 0.6)) {
                $movement = 'fast';
            } else {
                $movement = 'normal';
            }

            // Status peringatan restock dinamis berbasis Lead Time & Safety Stock
            if ($product->stock <= $safetyStock || ($daysRemaining !== null && $daysRemaining <= $leadTimeDays)) {
                $restockStatus = 'critical';
            } elseif ($product->stock <= $reorderPoint) {
                $restockStatus = 'warning';
            } else {
                $restockStatus = 'safe';
            }

            $needsRestock = in_array($restockStatus, ['critical', 'warning'], true);
            $suggestedRestock = $needsRestock
                ? max(0, $desiredStock - (int) $product->stock)
                : 0;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'image_url' => $product->image_url,
                'category_name' => $product->category?->name ?? '-',
                'stock' => (int) $product->stock,
                'safety_stock' => $safetyStock,
                'available_online' => $availableOnline,
                'cost_price' => (float) $product->cost_price,
                'sold_qty' => $soldQty,
                'pos_sold' => $posSold,
                'dropship_sold' => $dropshipSold,
                'daily_velocity' => $dailyVelocity,
                'days_remaining' => $daysRemaining,
                'reorder_point' => $reorderPoint,
                'movement' => $movement,
                'restock_status' => $restockStatus,
                'needs_restock' => $needsRestock,
                'suggested_restock' => $suggestedRestock,
                'estimated_cost' => $suggestedRestock * (float) $product->cost_price,
            ];
        })->sortBy(function ($item) {
            // Prioritaskan produk kritis/warning dan perputaran tercepat di atas
            $priority = match ($item['restock_status']) {
                'critical' => 0,
                'warning' => 1,
                default => 2,
            };
            return [$priority, -$item['daily_velocity'], $item['stock']];
        })->values();

        // KPI Ringkas
        $needsRestockCount = $analyzedProducts->where('needs_restock', true)->count();
        $fastMovingCount = $analyzedProducts->where('movement', 'fast')->count();
        $slowMovingCount = $analyzedProducts->where('movement', 'slow')->count();
        $estimatedRestockCost = (float) $analyzedProducts->where('needs_restock', true)->sum('estimated_cost');

        // Data Grafik 1: Tren Barang Keluar Harian (POS vs Dropship)
        $dailyTrendRows = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.supplier_id', $supplier->id)
            ->where('orders.status', '!=', 'dibatalkan')
            ->where('orders.created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(orders.created_at) as sale_date'),
                DB::raw("SUM(CASE WHEN orders.transaction_type = 'pos_offline' THEN order_items.quantity ELSE 0 END) as pos_qty"),
                DB::raw("SUM(CASE WHEN orders.transaction_type = 'dropship' THEN order_items.quantity ELSE 0 END) as dropship_qty")
            )
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        $trendLabels = [];
        $trendPos = [];
        $trendDropship = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $key = $d->format('Y-m-d');
            $trendLabels[] = $d->format('d M');
            $trendPos[] = (int) ($dailyTrendRows[$key]->pos_qty ?? 0);
            $trendDropship[] = (int) ($dailyTrendRows[$key]->dropship_qty ?? 0);
        }

        // Data Grafik 2: Perbandingan Produk (Top Terjual / Fast vs Slow)
        $topProductsForChart = $analyzedProducts
            ->sortByDesc('sold_qty')
            ->take(7)
            ->values();

        return view('admin.stock-analytics.index', [
            'supplier' => $supplier,
            'days' => $days,
            'leadTimeDays' => $leadTimeDays,
            'targetDays' => $targetDays,
            'products' => $analyzedProducts,
            'stats' => [
                'needs_restock' => $needsRestockCount,
                'fast_moving' => $fastMovingCount,
                'slow_moving' => $slowMovingCount,
                'estimated_cost' => $estimatedRestockCost,
            ],
            'trendChart' => [
                'labels' => $trendLabels,
                'pos' => $trendPos,
                'dropship' => $trendDropship,
            ],
            'velocityChart' => [
                'labels' => $topProductsForChart->map(fn ($p) => \Illuminate\Support\Str::limit($p['name'], 22))->values(),
                'sold' => $topProductsForChart->pluck('sold_qty')->values(),
                'stock' => $topProductsForChart->pluck('stock')->values(),
            ],
        ]);
    }

    public function updateParameters(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403);
        }

        $validated = $request->validate([
            'stock_lead_time_days' => ['required', 'integer', 'min:1', 'max:60'],
            'stock_target_days' => ['required', 'integer', 'min:2', 'max:90'],
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('admin.stock-analytics.index', $request->only('days'))
            ->with('success', 'Parameter prediksi restock berhasil diperbarui.');
    }
}
