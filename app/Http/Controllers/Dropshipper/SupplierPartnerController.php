<?php

namespace App\Http\Controllers\Dropshipper;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierDropshipper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupplierPartnerController extends Controller
{
    public const MAX_SUPPLIERS = 5;

    /**
     * Tampilkan halaman pemilihan toko penyuplai untuk dropshipper (Maksimal 5 toko).
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isDropshipper()) {
            if ($user->activeSupplier()) {
                return redirect()->route('admin.dropshippers.index')
                    ->with('warning', 'Halaman Pilih Penyuplai khusus untuk akun Dropshipper. Anda dialihkan ke Kelola Mitra Dropshipper toko Anda.');
            }
            abort(403, 'Akses ini khusus untuk akun Dropshipper.');
        }

        $search = $request->query('q');

        // Toko yang sudah terhubung dengan dropshipper
        $connectedSuppliers = $user->dropshipSuppliers()
            ->withCount('products')
            ->get();

        $connectedIds = $connectedSuppliers->pluck('id')->toArray();
        $connectedCount = count($connectedIds);

        // Hitung kelayakan & status 3 syarat pemutusan kemitraan untuk setiap toko terhubung
        $eligibilityMap = [];
        foreach ($connectedSuppliers as $cs) {
            $eligibilityMap[$cs->id] = SupplierDropshipper::getDisconnectEligibility($user->id, $cs->id);
        }

        // Cari semua supplier yang tersedia di platform
        $suppliersQuery = Supplier::withCount('products')->with('owner');

        if ($search) {
            $suppliersQuery->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('warehouse_address', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allSuppliers = $suppliersQuery->latest()->get();

        return view('dropshipper.suppliers.index', [
            'connectedSuppliers' => $connectedSuppliers,
            'allSuppliers' => $allSuppliers,
            'connectedIds' => $connectedIds,
            'connectedCount' => $connectedCount,
            'eligibilityMap' => $eligibilityMap,
            'maxAllowed' => self::MAX_SUPPLIERS,
            'search' => $search,
        ]);
    }

    /**
     * Hubungkan toko sebagai penyuplai dropshipper (Cek kuota maksimal 5).
     */
    public function connect(Request $request, Supplier $supplier): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isDropshipper()) {
            abort(403);
        }

        // Cek apakah sudah terhubung sebelumnya
        $isAlreadyConnected = SupplierDropshipper::where('dropshipper_id', $user->id)
            ->where('supplier_id', $supplier->id)
            ->exists();

        if ($isAlreadyConnected) {
            return back()->with('error', "Toko [{$supplier->store_name}] sudah terhubung dengan akun Anda.");
        }

        // Cek kuota maksimal 5 toko
        $currentCount = $user->dropshipSuppliers()->count();
        if ($currentCount >= self::MAX_SUPPLIERS) {
            return back()->with('error', "Anda telah mencapai batas kemitraan aktif (maksimal 5 toko penyuplai). Silakan lepas kemitraan salah satu toko terlebih dahulu jika ingin menambah toko baru.");
        }

        SupplierDropshipper::create([
            'supplier_id' => $supplier->id,
            'dropshipper_id' => $user->id,
            'wallet_balance' => 0,
            'status' => 'approved',
        ]);

        return back()->with('success', "Sukses! Toko [{$supplier->store_name}] berhasil ditambahkan ke daftar penyuplai Anda.");
    }

    /**
     * Ajukan permohonan pemutusan kemitraan (wajib memenuhi 3 syarat).
     */
    public function requestDisconnect(Request $request, Supplier $supplier): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isDropshipper()) {
            abort(403);
        }

        $eligibility = SupplierDropshipper::getDisconnectEligibility($user->id, $supplier->id);

        if (!$eligibility['can_request']) {
            return back()->with('error', 'Tidak dapat mengajukan pelepasan: ' . implode(' ', $eligibility['errors']));
        }

        $relation = SupplierDropshipper::where('dropshipper_id', $user->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        $reason = $request->input('reason') ?: 'Permohonan lepas kemitraan diajukan oleh Dropshipper.';

        $relation->update([
            'status' => 'pending_disconnect',
            'disconnect_requested_at' => now(),
            'disconnect_requested_by' => 'dropshipper',
            'disconnect_reason' => $reason,
            'disconnect_rejection_reason' => null,
        ]);

        return back()->with('success', "Permohonan pelepasan kemitraan dengan toko [{$supplier->store_name}] berhasil diajukan! Menunggu persetujuan pihak toko.");
    }

    /**
     * Batalkan pengajuan pemutusan kemitraan yang sedang pending.
     */
    public function cancelDisconnect(Request $request, Supplier $supplier): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isDropshipper()) {
            abort(403);
        }

        $relation = SupplierDropshipper::where('dropshipper_id', $user->id)
            ->where('supplier_id', $supplier->id)
            ->where('status', 'pending_disconnect')
            ->firstOrFail();

        $relation->update([
            'status' => 'approved',
            'disconnect_requested_at' => null,
            'disconnect_requested_by' => null,
            'disconnect_reason' => null,
        ]);

        return back()->with('success', "Pengajuan pelepasan kemitraan dengan [{$supplier->store_name}] berhasil dibatalkan. Kemitraan tetap aktif.");
    }

    /**
     * Endpoint fallback lepas kemitraan (dialihkan ke requestDisconnect agar syarat selalu tervalidasi).
     */
    public function disconnect(Request $request, Supplier $supplier): RedirectResponse
    {
        return $this->requestDisconnect($request, $supplier);
    }
}
