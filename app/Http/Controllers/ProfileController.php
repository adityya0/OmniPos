<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $data = [
            'user' => $user,
        ];

        if ($user->isDropshipper()) {
            $connectedSuppliers = $user->dropshipSuppliers()
                ->withCount('products')
                ->with('owner')
                ->get();

            $totalOrders = \App\Models\Order::where('user_id', $user->id)
                ->where('transaction_type', 'dropship')
                ->count();

            $completedOrders = \App\Models\Order::where('user_id', $user->id)
                ->where('transaction_type', 'dropship')
                ->where('status', 'selesai')
                ->count();

            $activeOrders = \App\Models\Order::where('user_id', $user->id)
                ->where('transaction_type', 'dropship')
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();

            $totalSpend = \App\Models\Order::where('user_id', $user->id)
                ->where('transaction_type', 'dropship')
                ->where('payment_status', 'paid')
                ->sum('final_amount');

            $data = array_merge($data, [
                'connectedSuppliers' => $connectedSuppliers,
                'connectedCount' => $connectedSuppliers->count(),
                'maxAllowed' => 5,
                'totalOrders' => $totalOrders,
                'completedOrders' => $completedOrders,
                'activeOrders' => $activeOrders,
                'totalSpend' => $totalSpend,
            ]);
            return view('dropshipper.profile', $data);
        }

        if ($user->activeSupplier()) {
            return redirect()->route('admin.settings.index', ['tab' => 'profile']);
        }

        return view('profile.edit', $data);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($request->user()->activeSupplier()) {
            return Redirect::route('admin.settings.index', ['tab' => 'profile'])->with('status', 'profile-updated');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
