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

class ReportController extends Controller
{
    /**
     * Halaman laporan akumulasi penjualan, riwayat transaksi, dan analisis laba rugi.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $period = $request->query('period', 'this_month');
        $channel = $request->query('channel', 'all');
        if (!in_array($channel, ['all', 'pos', 'dropship'])) {
            $channel = 'all';
        }
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Query dasar periode toko
        $basePeriodQuery = Order::with(['items', 'user'])
            ->where('supplier_id', $supplier->id)
            ->where('payment_status', 'paid');

        // Filter rentang tanggal kustom (jika diisi, mengutamakan tanggal kustom)
        if ($startDate && $endDate) {
            $period = 'custom';
            $basePeriodQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($startDate) {
            $period = 'custom';
            $basePeriodQuery->whereDate('created_at', Carbon::parse($startDate));
        } else {
            // Filter rentang waktu preset
            switch ($period) {
                case 'today':
                    $basePeriodQuery->whereDate('created_at', Carbon::today());
                    break;
                case '7days':
                    $basePeriodQuery->where('created_at', '>=', Carbon::now()->subDays(7));
                    break;
                case 'this_month':
                    $basePeriodQuery->whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'all':
                default:
                    break;
            }
        }

        // ================= METRIK KHUSUS OWNER: POS KASIR OFFLINE TOKO =================
        $posOrders = (clone $basePeriodQuery)->where('transaction_type', 'pos')->get();
        $posTotalSales = (float) $posOrders->sum('final_amount');
        $posTotalCogs = (float) $posOrders->sum('total_cost');
        $posNetProfit = (float) $posOrders->sum('net_profit');
        $posTotalTransactions = $posOrders->count();
        $posTotalDiscounts = (float) $posOrders->sum('discount');
        $posCashSales = (float) $posOrders->where('payment_method', 'cash')->sum('final_amount');
        $posQrisSales = (float) $posOrders->where('payment_method', 'qris')->sum('final_amount');
        $posTransferSales = (float) $posOrders->where('payment_method', 'transfer')->sum('final_amount');
        $posAvgOrderValue = $posTotalTransactions > 0 ? ($posTotalSales / $posTotalTransactions) : 0;

        $posMetrics = [
            'total_sales'        => $posTotalSales,
            'total_cogs'         => $posTotalCogs,
            'net_profit'         => $posNetProfit,
            'total_transactions' => $posTotalTransactions,
            'total_discounts'    => $posTotalDiscounts,
            'cash_sales'         => $posCashSales,
            'qris_sales'         => $posQrisSales,
            'transfer_sales'     => $posTransferSales,
            'avg_order_value'    => $posAvgOrderValue,
            'margin_pct'         => $posTotalSales > 0 ? round(($posNetProfit / $posTotalSales) * 100, 1) : 0,
        ];

        // ================= METRIK DROPSHIP ONLINE =================
        $dropshipOrders = (clone $basePeriodQuery)->where('transaction_type', 'dropship')->get();
        $dropshipTotalSales = (float) $dropshipOrders->sum('final_amount');
        $dropshipTotalCogs = (float) $dropshipOrders->sum('total_cost');
        $dropshipNetProfit = (float) $dropshipOrders->sum('net_profit');
        $dropshipTotalTransactions = $dropshipOrders->count();

        $dropshipMetrics = [
            'total_sales'        => $dropshipTotalSales,
            'total_cogs'         => $dropshipTotalCogs,
            'net_profit'         => $dropshipNetProfit,
            'total_transactions' => $dropshipTotalTransactions,
            'margin_pct'         => $dropshipTotalSales > 0 ? round(($dropshipNetProfit / $dropshipTotalSales) * 100, 1) : 0,
        ];

        // Produk kasir terlaris untuk ringkasan rekap kasir owner
        $posOrderIds = $posOrders->pluck('id');
        $topPosProducts = OrderItem::whereIn('order_id', $posOrderIds)
            ->select('product_name', 'product_sku', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name', 'product_sku')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Query utama (difilter kanal jika dipilih)
        $query = clone $basePeriodQuery;
        if ($channel === 'pos') {
            $query->where('transaction_type', 'pos');
        } elseif ($channel === 'dropship') {
            $query->where('transaction_type', 'dropship');
        }

        // Search invoice or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Summary aggregations untuk query aktif
        $clone = clone $query;
        $totalSales = $clone->sum('final_amount');
        $totalCost = $clone->sum('total_cost');
        $netProfit = $clone->sum('net_profit');
        $totalTransactions = $clone->count();

        // Total items sold
        $orderIds = (clone $query)->pluck('id');
        $totalUnitsSold = OrderItem::whereIn('order_id', $orderIds)->sum('quantity');

        // Paginated orders for transaction history table
        $orders = $query->latest()->paginate(15)->withQueryString();

        // Breakdown Laba Rugi per Produk
        $productBreakdown = OrderItem::whereIn('order_id', $orderIds)
            ->select(
                'product_name',
                'product_sku',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('SUM(cost_price * quantity) as total_cogs'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->groupBy('product_name', 'product_sku')
            ->orderByDesc('total_profit')
            ->take(10)
            ->get();

        // 1. Data Tren Omset & Laba untuk Grafik Interaktif (Kenaikan & Penurunan)
        $allPeriodOrders = (clone $query)->orderBy('created_at')->get(['created_at', 'final_amount', 'total_cost', 'net_profit']);

        $isSingleDay = ($period === 'today') || ($startDate && $endDate && $startDate === $endDate);

        if ($isSingleDay) {
            $trendGrouped = $allPeriodOrders->groupBy(function ($o) {
                return $o->created_at->format('H:00');
            });
        } else {
            $trendGrouped = $allPeriodOrders->groupBy(function ($o) {
                return $o->created_at->format('d M');
            });
        }

        $salesTrend = [
            'labels' => [],
            'sales'  => [],
            'cogs'   => [],
            'profit' => [],
        ];

        foreach ($trendGrouped as $timeKey => $items) {
            $salesTrend['labels'][] = $timeKey;
            $salesTrend['sales'][]  = (float) $items->sum('final_amount');
            $salesTrend['cogs'][]   = (float) $items->sum('total_cost');
            $salesTrend['profit'][] = (float) $items->sum('net_profit');
        }

        // 2. Data Grafik Analisis Laba Rugi per Produk
        $productChartData = [
            'labels'   => [],
            'profits'  => [],
            'sales'    => [],
            'cogs'     => [],
            'qtys'     => [],
            'margins'  => [],
        ];

        foreach ($productBreakdown as $prod) {
            $shortName = \Illuminate\Support\Str::limit($prod->product_name, 22);
            $productChartData['labels'][]  = $shortName;
            $productChartData['profits'][] = (float) $prod->total_profit;
            $productChartData['sales'][]   = (float) $prod->total_sales;
            $productChartData['cogs'][]    = (float) $prod->total_cogs;
            $productChartData['qtys'][]    = (int) $prod->total_qty;
            $marginPct = $prod->total_sales > 0 ? round(($prod->total_profit / $prod->total_sales) * 100, 1) : 0;
            $productChartData['margins'][] = $marginPct;
        }

        return view('admin.reports.index', compact(
            'supplier',
            'period',
            'channel',
            'startDate',
            'endDate',
            'orders',
            'totalSales',
            'totalCost',
            'netProfit',
            'totalTransactions',
            'totalUnitsSold',
            'productBreakdown',
            'salesTrend',
            'productChartData',
            'posMetrics',
            'dropshipMetrics',
            'topPosProducts'
        ));
    }

    /**
     * Hapus satu transaksi invoice dan kembalikan stok barang ke inventori.
     */
    public function destroyOrder(Order $order): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier || $order->supplier_id !== $supplier->id) {
            abort(403, 'Akses transaksi tidak diizinkan.');
        }

        DB::beginTransaction();
        try {
            $invoiceNumber = $order->invoice_number;

            // Kembalikan kuantitas barang yang terjual ke stok produk
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            // Hapus order (order_items otomatis terhapus via cascade)
            $order->delete();

            DB::commit();

            return redirect()->route('admin.reports.index', ['tab' => 'transactions'])
                ->with('success', "Invoice {$invoiceNumber} berhasil dihapus dan stok barang telah dikembalikan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus massal beberapa invoice transaksi sekaligus (Bulk Delete) dan kembalikan stoknya.
     */
    public function bulkDestroyOrders(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ], [
            'ids.required' => 'Pilih setidaknya satu invoice transaksi yang ingin dihapus.',
        ]);

        $orders = Order::with('items')
            ->where('supplier_id', $supplier->id)
            ->whereIn('id', $validated['ids'])
            ->get();

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }

                $order->delete();
                $count++;
            }

            DB::commit();

            return redirect()->route('admin.reports.index', ['tab' => 'transactions'])
                ->with('success', "Berhasil menghapus {$count} invoice transaksi dan stok barang telah dikembalikan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus invoice: ' . $e->getMessage()]);
        }
    }
}
