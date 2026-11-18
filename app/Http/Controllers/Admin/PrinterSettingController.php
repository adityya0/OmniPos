<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PrinterSettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan printer & kustomisasi struk.
     */
    public function index(): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        return view('admin.printer.index', compact('supplier'));
    }

    /**
     * Simpan kustomisasi struk & printer ke database toko.
     */
    public function update(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'receipt_header_title' => ['nullable', 'string', 'max:255'],
            'warehouse_address' => ['nullable', 'string', 'max:500'],
            'shipping_origin_city' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'operating_hours' => ['nullable', 'string', 'max:100'],
            'cut_off_time' => ['nullable', 'string', 'max:100'],
            'packing_sla' => ['nullable', 'string', 'max:100'],
            'supported_couriers' => ['nullable', 'string', 'max:255'],
            'packing_standard' => ['nullable', 'string', 'max:255'],
            'return_policy' => ['nullable', 'string', 'max:1000'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'receipt_icon' => ['nullable', 'string', 'max:50'],
            'receipt_paper_size' => ['required', 'in:58mm,80mm'],
            'receipt_footer_notes' => ['nullable', 'string', 'max:1000'],
            'receipt_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        // Tangani upload logo struk jika ada
        if ($request->hasFile('receipt_logo')) {
            if ($supplier->receipt_logo && Storage::disk('public')->exists($supplier->receipt_logo)) {
                Storage::disk('public')->delete($supplier->receipt_logo);
            }
            $path = $request->file('receipt_logo')->store('receipt_logos', 'public');
            $supplier->receipt_logo = $path;
        } elseif ($request->boolean('remove_logo')) {
            if ($supplier->receipt_logo && Storage::disk('public')->exists($supplier->receipt_logo)) {
                Storage::disk('public')->delete($supplier->receipt_logo);
            }
            $supplier->receipt_logo = null;
        }

        // Simpan boolean toggles & profil transparansi
        $supplier->store_name = $validated['store_name'];
        $supplier->receipt_header_title = $validated['receipt_header_title'] ?? null;
        $supplier->warehouse_address = $validated['warehouse_address'] ?? null;
        $supplier->shipping_origin_city = $validated['shipping_origin_city'] ?? $supplier->shipping_origin_city;
        $supplier->contact_phone = $validated['contact_phone'] ?? null;
        $supplier->operating_hours = $validated['operating_hours'] ?? $supplier->operating_hours;
        $supplier->cut_off_time = $validated['cut_off_time'] ?? $supplier->cut_off_time;
        $supplier->packing_sla = $validated['packing_sla'] ?? $supplier->packing_sla;
        $supplier->supported_couriers = $validated['supported_couriers'] ?? $supplier->supported_couriers;
        $supplier->packing_standard = $validated['packing_standard'] ?? $supplier->packing_standard;
        $supplier->return_policy = $validated['return_policy'] ?? $supplier->return_policy;
        $supplier->pic_name = $validated['pic_name'] ?? $supplier->pic_name;
        $supplier->email = $validated['email'] ?? null;
        $supplier->receipt_icon = $validated['receipt_icon'] ?? 'shopping-bag';
        $supplier->receipt_paper_size = $validated['receipt_paper_size'] ?? '58mm';
        $supplier->receipt_footer_notes = $validated['receipt_footer_notes'] ?? null;

        $supplier->receipt_show_logo = $request->boolean('receipt_show_logo');
        $supplier->receipt_show_header = $request->boolean('receipt_show_header');
        $supplier->receipt_show_address = $request->boolean('receipt_show_address');
        $supplier->receipt_show_phone = $request->boolean('receipt_show_phone');
        $supplier->receipt_show_email = $request->boolean('receipt_show_email');
        $supplier->receipt_show_invoice = $request->boolean('receipt_show_invoice');
        $supplier->receipt_show_date = $request->boolean('receipt_show_date');
        $supplier->receipt_show_cashier = $request->boolean('receipt_show_cashier');
        $supplier->receipt_show_customer = $request->boolean('receipt_show_customer');
        $supplier->receipt_show_subtotal = $request->boolean('receipt_show_subtotal');
        $supplier->receipt_show_discount = $request->boolean('receipt_show_discount');
        $supplier->receipt_show_payment_method = $request->boolean('receipt_show_payment_method');
        $supplier->receipt_show_change = $request->boolean('receipt_show_change');
        $supplier->receipt_show_footer = $request->boolean('receipt_show_footer');

        $supplier->save();

        return redirect()->route('admin.printer.index')
            ->with('status', 'Pengaturan printer dan kustomisasi struk berhasil disimpan!');
    }
}
