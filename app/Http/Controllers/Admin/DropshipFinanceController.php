<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupplierDropshipper;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DropshipFinanceController extends Controller
{
    /**
     * Halaman Indeks Keuangan & Saldo Dropshipper.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        // Ambil relasi dropshipper toko
        $relations = SupplierDropshipper::with('dropshipper')
            ->where('supplier_id', $supplier->id)
            ->get();

        // Hitung agregat per dropshipper dari orders (transaction_type = 'dropship')
        $dropshipperStats = $relations->map(function ($rel) use ($supplier) {
            $user = $rel->dropshipper;

            $ordersQuery = Order::where('supplier_id', $supplier->id)
                ->where('user_id', $user->id)
                ->where('transaction_type', 'dropship');

            $totalOrders = (clone $ordersQuery)->count();
            // Total akumulasi modal barang yang diambil (total final_amount / modal dropship yang harus dibayar)
            $totalModalBarang = (clone $ordersQuery)->sum('final_amount');
            // Total biaya yang belum lunas
            $totalUnpaid = (clone $ordersQuery)->where('payment_status', 'pending')->sum('final_amount');
            
            $walletBalance = (float) $rel->wallet_balance;

            // Status hutang / keuangan
            $financialStatus = 'Lancar';
            if ($walletBalance < 0 || $totalUnpaid > 0) {
                $financialStatus = 'Ada Tagihan';
            } elseif ($walletBalance > 0) {
                $financialStatus = 'Surplus Saldo';
            }

            return (object) [
                'relation_id' => $rel->id,
                'dropshipper_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status_mitra' => $rel->status,
                'total_orders' => $totalOrders,
                'total_modal_barang' => $totalModalBarang,
                'total_unpaid' => $totalUnpaid,
                'wallet_balance' => $walletBalance,
                'financial_status' => $financialStatus,
            ];
        });

        // Summary box
        $totalMitra = $dropshipperStats->count();
        $totalAkumulasiModal = $dropshipperStats->sum('total_modal_barang');
        $totalSaldoDeposit = $dropshipperStats->sum(fn($s) => max(0, $s->wallet_balance));
        $totalPiutangTertunda = $dropshipperStats->sum(fn($s) => $s->wallet_balance < 0 ? abs($s->wallet_balance) : $s->total_unpaid);

        return view('admin.dropship-finances.index', compact(
            'supplier',
            'dropshipperStats',
            'totalMitra',
            'totalAkumulasiModal',
            'totalSaldoDeposit',
            'totalPiutangTertunda'
        ));
    }

    /**
     * Halaman Detail per Dropshipper (admin/dropship-finances/{id}).
     */
    public function show(Request $request, int $id): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $dropshipper = User::where('id', $id)->firstOrFail();

        $relation = SupplierDropshipper::where('supplier_id', $supplier->id)
            ->where('dropshipper_id', $dropshipper->id)
            ->first();

        if (!$relation) {
            // Jika belum ada record pivot, buatkan record default
            $relation = SupplierDropshipper::create([
                'supplier_id' => $supplier->id,
                'dropshipper_id' => $dropshipper->id,
                'wallet_balance' => 0.00,
                'status' => 'approved',
            ]);
        }

        // Ambil daftar transaksi dropshipper ini
        $orders = Order::with('items')
            ->where('supplier_id', $supplier->id)
            ->where('user_id', $dropshipper->id)
            ->where('transaction_type', 'dropship')
            ->latest()
            ->paginate(15);

        // Ringkasan
        $allOrdersQuery = Order::where('supplier_id', $supplier->id)
            ->where('user_id', $dropshipper->id)
            ->where('transaction_type', 'dropship');

        $totalResiTerkirim = (clone $allOrdersQuery)->whereNotNull('shipping_receipt_number')->count();
        $totalBiayaModal = (clone $allOrdersQuery)->sum('final_amount');
        $walletBalance = (float) $relation->wallet_balance;

        return view('admin.dropship-finances.show', compact(
            'supplier',
            'dropshipper',
            'relation',
            'orders',
            'totalResiTerkirim',
            'totalBiayaModal',
            'walletBalance'
        ));
    }

    /**
     * Top Up / Tambah Saldo Manual.
     */
    public function topUp(Request $request, int $id): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $relation = SupplierDropshipper::where('supplier_id', $supplier->id)
            ->where('dropshipper_id', $id)
            ->firstOrFail();

        $relation->increment('wallet_balance', $validated['amount']);

        return back()->with('success', "Saldo deposit berhasil ditambahkan sebesar Rp " . number_format($validated['amount'], 0, ',', '.') . ".");
    }

    /**
     * Tandai pesanan / tagihan dropshipper sebagai Lunas.
     */
    public function markAsPaid(Request $request, int $id): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $orderId = $request->input('order_id');

        if ($orderId) {
            // Tandai 1 pesanan spesifik
            Order::where('id', $orderId)
                ->where('supplier_id', $supplier->id)
                ->where('user_id', $id)
                ->where('transaction_type', 'dropship')
                ->update(['payment_status' => 'paid']);

            return back()->with('success', 'Transaksi pesanan berhasil ditandai Lunas.');
        }

        // Tandai semua pesanan pending dropshipper ini sebagai Lunas
        $count = Order::where('supplier_id', $supplier->id)
            ->where('user_id', $id)
            ->where('transaction_type', 'dropship')
            ->where('payment_status', 'pending')
            ->update(['payment_status' => 'paid']);

        // Jika saldo minus, reset saldo menjadi 0
        $relation = SupplierDropshipper::where('supplier_id', $supplier->id)
            ->where('dropshipper_id', $id)
            ->first();
        if ($relation && $relation->wallet_balance < 0) {
            $relation->wallet_balance = 0;
            $relation->save();
        }

        return back()->with('success', "Seluruh tagihan ({$count} pesanan) berhasil ditandai Lunas.");
    }
}
