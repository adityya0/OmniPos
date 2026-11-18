<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Tampilkan daftar staf toko (tim media & tim packing).
     */
    public function index(Request $request): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $staffList = User::where('supplier_id', $supplier->id)
            ->whereIn('role', ['tim_media', 'tim_packing', 'admin_supplier'])
            ->latest()
            ->paginate(15);

        return view('admin.staff.index', compact('supplier', 'staffList'));
    }

    /**
     * Tambah akun staf toko baru (Tim Media / Tim Packing).
     */
    public function store(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:tim_media,tim_packing'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'supplier_id' => $supplier->id,
        ]);

        return back()->with('success', "Akun staf {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Hapus akun staf toko.
     */
    public function destroy(User $staff): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier || $staff->supplier_id !== $supplier->id) {
            abort(403);
        }

        $staff->delete();

        return back()->with('success', "Akun staf {$staff->name} berhasil dihapus.");
    }
}
