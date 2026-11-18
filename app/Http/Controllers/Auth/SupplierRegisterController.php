<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class SupplierRegisterController extends Controller
{
    /**
     * Display supplier registration view.
     */
    public function create(): View
    {
        return view('auth.register-supplier');
    }

    /**
     * Handle incoming supplier registration with database transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'store_name' => ['required', 'string', 'max:255'],
            'warehouse_address' => ['required', 'string', 'max:1000'],
            'contact_phone' => ['required', 'string', 'max:30'],
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin_supplier',
            ]);

            $supplier = Supplier::create([
                'owner_id' => $user->id,
                'store_name' => $request->store_name,
                'warehouse_address' => $request->warehouse_address,
                'contact_phone' => $request->contact_phone,
            ]);

            $user->update([
                'supplier_id' => $supplier->id,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
