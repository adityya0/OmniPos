<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard analitik operasional admin supplier.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $user = Auth::user();

        // Jika tim packing, arahkan langsung ke workspace packing
        if ($user->isTimPacking()) {
            return redirect()->route('admin.packing.index');
        }

        $supplier = $user->activeSupplier();

        // Jika user adalah mitra dropshipper
        if ($user->isDropshipper()) {
            $connectedSupplierIds = $user->dropshipSuppliers()->pluck('suppliers.id')->toArray();
            $connectedCount = count($connectedSupplierIds);

            $availableProductsCount = Product::whereIn('supplier_id', $connectedSupplierIds)
                ->where('is_active', true)
                ->count();

            $totalOrdersCount = Order::where('user_id', $user->id)
                ->where('transaction_type', 'dropship')
                ->count();

            $activeOrdersCount = Order::where('user_id', $user->id)
                ->where('transaction_type', 'dropship')
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();

            $recentOrders = Order::with('supplier')
                ->where('user_id', $user->id)
                ->where('transaction_type', 'dropship')
                ->latest()
                ->take(5)
                ->get();

            return Inertia::render('Dashboard', [
                'user' => $user,
                'supplier' => null,
                'dropshipperStats' => [
                    'connectedSuppliersCount' => $connectedCount,
                    'maxAllowedSuppliers' => 5,
                    'availableProductsCount' => $availableProductsCount,
                    'totalOrdersCount' => $totalOrdersCount,
                    'activeOrdersCount' => $activeOrdersCount,
                ],
                'recentDropshipOrders' => $recentOrders,
            ]);
        }

        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        // 1. $todayPosSales: Total rupiah transaksi POS hari ini (pos_sales)
        $todayPosSales = Order::where('supplier_id', $supplier->id)
            ->where('transaction_type', 'pos_offline')
            ->where('payment_status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('final_amount');

        // 2. $pendingPackingCount: Jumlah pesanan dropship dengan status 'menunggu_packing' (orders)
        $pendingPackingCount = Order::where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship')
            ->where('status', 'menunggu_packing')
            ->count();

        // 3. $lowStockCount: Jumlah produk dengan stock <= safety_stock (products)
        $lowStockCount = Product::where('supplier_id', $supplier->id)
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'safety_stock')
                  ->orWhere('stock', '<=', 5);
            })
            ->count();

        // 4. $activeDropshippersCount: Jumlah mitra di supplier_dropshipper dengan status 'approved'
        $activeDropshippersCount = DB::table('supplier_dropshipper')
            ->where('supplier_id', $supplier->id)
            ->where('status', 'approved')
            ->count();

        // 5. $urgentOrders: 5 pesanan berstatus 'menunggu_packing' yang diurutkan dari marketplace_deadline terdekat
        $urgentOrders = Order::with(['items', 'dropshipper'])
            ->where('supplier_id', $supplier->id)
            ->where('transaction_type', 'dropship')
            ->where('status', 'menunggu_packing')
            ->orderByRaw('CASE WHEN marketplace_deadline IS NULL THEN 1 ELSE 0 END, marketplace_deadline ASC')
            ->take(5)
            ->get();

        // 6. $lowStockProducts: 5 produk dengan stok terendah
        $lowStockProducts = Product::where('supplier_id', $supplier->id)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // 7. $recentPosSales: 5 transaksi kasir terakhir
        $recentPosSales = Order::with(['items', 'user'])
            ->where('supplier_id', $supplier->id)
            ->where('transaction_type', 'pos_offline')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'user' => $user,
            'supplier' => $supplier,
            'todayPosSales' => (float) $todayPosSales,
            'pendingPackingCount' => $pendingPackingCount,
            'lowStockCount' => $lowStockCount,
            'activeDropshippersCount' => $activeDropshippersCount,
            'urgentOrders' => $urgentOrders,
            'lowStockProducts' => $lowStockProducts,
            'recentPosSales' => $recentPosSales,
        ]);
    }
}
