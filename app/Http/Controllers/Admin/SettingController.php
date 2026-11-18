<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Tampilan Pusat Pengaturan & Profil Toko / Kasir.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $supplier = $user->activeSupplier();

        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $activeTab = $request->query('tab', 'pos');

        return view('admin.settings.index', compact('user', 'supplier', 'activeTab'));
    }

    /**
     * Memperbarui pengaturan mode tampilan harga kasir POS.
     */
    public function updatePosPricingMode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pos_pricing_mode' => 'required|in:global_filter,card_buttons',
        ]);

        $supplier = Auth::user()->activeSupplier();
        if ($supplier) {
            $supplier->update([
                'pos_pricing_mode' => $validated['pos_pricing_mode'],
            ]);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'pos'])
            ->with('status', 'pos-pricing-mode-updated')
            ->with('message', 'Mode tampilan kasir POS berhasil disimpan.');
    }

    /**
     * Memperbarui profil identitas toko supplier.
     */
    public function updateStoreProfile(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'shipping_origin_city' => 'nullable|string|max:100',
            'warehouse_address' => 'nullable|string|max:1000',
            'operating_hours' => 'nullable|string|max:255',
            'cut_off_time' => 'nullable|string|max:100',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'store'])
            ->with('status', 'store-profile-updated')
            ->with('message', 'Profil toko berhasil diperbarui.');
    }
}

