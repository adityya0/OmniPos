<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierDropshipper;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PartnerDropshipperController extends Controller
{
    /**
     * Tampilkan daftar seluruh mitra dropshipper toko.
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $search = $request->input('search');

        $query = SupplierDropshipper::with('dropshipper')
            ->where('supplier_id', $supplier->id);

        if ($search) {
            $query->whereHas('dropshipper', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $partners = $query->latest()->paginate(15)->withQueryString();

        $pendingDisconnectCount = SupplierDropshipper::where('supplier_id', $supplier->id)
            ->where('status', 'pending_disconnect')
            ->count();

        // Siapkan info kelayakan pelepasan untuk setiap mitra
        $partnerEligibility = [];
        foreach ($partners as $partner) {
            $partnerEligibility[$partner->id] = SupplierDropshipper::getDisconnectEligibility($partner->dropshipper_id, $supplier->id);
        }

        return view('admin.dropshippers.index', compact('supplier', 'partners', 'search', 'pendingDisconnectCount', 'partnerEligibility'));
    }

    /**
     * Update status persetujuan mitra (approved / rejected / pending).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,pending'],
        ]);

        $partner = SupplierDropshipper::where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->firstOrFail();

        $partner->status = $validated['status'];
        $partner->save();

        return back()->with('success', "Status kemitraan {$partner->dropshipper->name} berhasil diperbarui menjadi {$validated['status']}.");
    }

    /**
     * Setujui permohonan pelepasan kemitraan (Persetujuan Kedua Belah Pihak Lengkap).
     */
    public function approveDisconnect(Request $request, int $id): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403);
        }

        $partner = SupplierDropshipper::with('dropshipper')
            ->where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->firstOrFail();

        // Verifikasi kepatuhan: pastikan transaksi berjalan sudah benar-benar selesai
        $eligibility = SupplierDropshipper::getDisconnectEligibility($partner->dropshipper_id, $supplier->id);
        if (!$eligibility['is_orders_cleared']) {
            return back()->with('error', "Gagal menyetujui: Masih ada {$eligibility['active_orders_count']} pesanan aktif berjalan yang belum selesai/dibatalkan.");
        }

        $partnerName = $partner->dropshipper?->name ?? 'Dropshipper';

        // Resmi dilepas dari database
        $partner->delete();

        return back()->with('success', "Pelepasan kemitraan dengan [{$partnerName}] telah disetujui oleh kedua belah pihak. Hubungan kemitraan resmi berakhir.");
    }

    /**
     * Tolak permohonan pelepasan kemitraan.
     */
    public function rejectDisconnect(Request $request, int $id): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403);
        }

        $partner = SupplierDropshipper::with('dropshipper')
            ->where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->firstOrFail();

        $reason = $request->input('rejection_reason', 'Ditolak oleh pihak toko supplier.');

        $partner->update([
            'status' => 'approved',
            'disconnect_rejection_reason' => $reason,
        ]);

        $partnerName = $partner->dropshipper?->name ?? 'Dropshipper';

        return back()->with('success', "Permohonan pelepasan kemitraan dari [{$partnerName}] telah ditolak. Kemitraan tetap aktif.");
    }

    /**
     * Tambah mitra dropshipper baru secara manual.
     */
    public function store(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'wallet_balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
                'role' => 'dropshipper',
            ]
        );

        SupplierDropshipper::updateOrCreate(
            ['supplier_id' => $supplier->id, 'dropshipper_id' => $user->id],
            [
                'wallet_balance' => $validated['wallet_balance'] ?? 0,
                'status' => 'approved',
            ]
        );

        return back()->with('success', "Mitra dropshipper [{$user->name}] berhasil ditambahkan.");
    }
}
