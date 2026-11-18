<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierDropshipper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MasterAdminController extends Controller
{
    public const MASTER_URL = 'master-control-x99';
    public const MASTER_PIN = 'adminpos12345';

    /**
     * Tampilkan form login Master Key
     */
    public function showLogin()
    {
        if (session('is_master_admin')) {
            return redirect('/' . self::MASTER_URL . '/dashboard');
        }
        return view('master.login');
    }

    /**
     * Verifikasi Master Key / PIN
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'secret_key' => 'required',
        ]);

        if ($request->secret_key === self::MASTER_PIN) {
            session(['is_master_admin' => true]);
            return redirect('/' . self::MASTER_URL . '/dashboard')->with('success', 'Akses Master Diterima.');
        }

        return back()->with('error', 'PIN Rahasia Master Tidak Cocok!');
    }

    /**
     * Logout dari Master Hub
     */
    public function logout()
    {
        session()->forget('is_master_admin');
        return redirect('/' . self::MASTER_URL)->with('success', 'Berhasil keluar dari Master Control.');
    }

    /**
     * Dashboard Utama Master Admin
     */
    public function index(Request $request)
    {
        if (!session('is_master_admin')) {
            return redirect('/' . self::MASTER_URL);
        }

        $search = $request->query('q');

        // Ambil semua Supplier beserta info Toko & Pemilik
        $supplierQuery = Supplier::with(['owner', 'dropshippers', 'products']);
        if ($search) {
            $supplierQuery->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        $suppliers = $supplierQuery->latest()->get();

        // Ambil semua Dropshipper beserta info Supplier yang terhubung
        $dropshipperQuery = User::where('role', 'dropshipper')
            ->with(['dropshipSuppliers', 'supplierDropshipperRelations']);
        if ($search) {
            $dropshipperQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $dropshippers = $dropshipperQuery->latest()->get();

        // Ambil akun staf atau akun lainnya
        $otherUsers = User::whereNotIn('role', ['dropshipper'])
            ->whereDoesntHave('ownedSupplier')
            ->get();

        return view('master.index', [
            'suppliers' => $suppliers,
            'dropshippers' => $dropshippers,
            'otherUsers' => $otherUsers,
            'allSuppliersList' => Supplier::orderBy('store_name')->get(),
            'search' => $search,
            'masterUrl' => self::MASTER_URL,
        ]);
    }

    /**
     * 1-Click Login As (Impersonate)
     */
    public function loginAs($id)
    {
        if (!session('is_master_admin')) {
            return redirect('/' . self::MASTER_URL);
        }

        $user = User::findOrFail($id);

        // Simpan penanda di session
        session([
            'impersonating_from_master' => true,
            'master_user_name' => $user->name,
            'master_user_role' => $user->role,
        ]);

        Auth::login($user);

        // Arahkan ke rute yang sesuai dengan perannya
        if ($user->role === 'dropshipper') {
            return redirect()->route('dropshipper.catalog.index')
                ->with('success', "⚡ Masuk sebagai Dropshipper: {$user->name}");
        }

        // Supplier / Admin Toko
        return redirect()->route('admin.products.index')
            ->with('success', "⚡ Masuk sebagai {$user->name} ({$user->role})");
    }

    /**
     * Keluar dari akun user dan kembali ke Master Dashboard
     */
    public function exitImpersonate()
    {
        Auth::logout();
        session()->forget('impersonating_from_master');
        session()->forget('master_user_name');
        session()->forget('master_user_role');

        return redirect('/' . self::MASTER_URL . '/dashboard')
            ->with('success', 'Kembali ke Master Hub.');
    }

    /**
     * Ganti / Reset Password Akun Tertentu
     */
    public function resetPassword(Request $request, $id)
    {
        if (!session('is_master_admin')) {
            return redirect('/' . self::MASTER_URL);
        }

        $request->validate([
            'new_password' => 'required|min:4',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', "Password akun [{$user->name}] ({$user->email}) berhasil diubah menjadi: {$request->new_password}");
    }

    /**
     * Hubungkan Dropshipper ke Supplier (Maksimal 5)
     */
    public function linkSupplier(Request $request)
    {
        if (!session('is_master_admin')) {
            return redirect('/' . self::MASTER_URL);
        }

        $request->validate([
            'dropshipper_id' => 'required|exists:users,id',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $dropshipper = User::findOrFail($request->dropshipper_id);

        // Cek kuota maksimal 5 supplier
        $currentCount = $dropshipper->dropshipSuppliers()->count();
        if ($currentCount >= 5) {
            return back()->with('error', "Gagal! Dropshipper [{$dropshipper->name}] sudah mencapai batas maksimal 5 supplier.");
        }

        // Cek apakah sudah pernah terhubung
        if ($dropshipper->dropshipSuppliers()->where('suppliers.id', $request->supplier_id)->exists()) {
            return back()->with('error', "Dropshipper sudah terhubung dengan supplier ini sebelumnya.");
        }

        SupplierDropshipper::create([
            'supplier_id' => $request->supplier_id,
            'dropshipper_id' => $dropshipper->id,
            'wallet_balance' => 0,
            'status' => 'approved',
        ]);

        return back()->with('success', "Berhasil menghubungkan Dropshipper [{$dropshipper->name}] ke Supplier!");
    }

    /**
     * Lepas Kemitraan Supplier dari Dropshipper
     */
    public function unlinkSupplier(Request $request)
    {
        if (!session('is_master_admin')) {
            return redirect('/' . self::MASTER_URL);
        }

        $request->validate([
            'dropshipper_id' => 'required|exists:users,id',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        SupplierDropshipper::where('dropshipper_id', $request->dropshipper_id)
            ->where('supplier_id', $request->supplier_id)
            ->delete();

        return back()->with('success', "Kemitraan berhasil dilepas.");
    }

    /**
     * Buat Akun Baru Cepat dari Master
     */
    public function storeUser(Request $request)
    {
        if (!session('is_master_admin')) {
            return redirect('/' . self::MASTER_URL);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin_supplier,dropshipper',
            'password' => 'required|min:4',
            'store_name' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Jika Supplier, buat entri Toko Supplier
        if ($request->role === 'admin_supplier') {
            Supplier::create([
                'owner_id' => $user->id,
                'store_name' => $request->store_name ?: ($request->name . ' Store'),
                'email' => $request->email,
                'warehouse_address' => 'Gudang Pusat',
                'contact_phone' => '-',
                'receipt_header_title' => $request->store_name ?: $request->name,
            ]);
        }

        return back()->with('success', "Akun [{$user->name}] ({$user->role}) berhasil dibuat!");
    }
}

