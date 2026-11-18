<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupplierAccess
{
    /**
     * Pastikan hanya akun pemilik toko supplier atau staf toko yang dapat mengakses rute /admin/*.
     * Jika akun dropshipper mengakses, alihkan langsung ke portal katalog dropshipper.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isDropshipper()) {
            return redirect()->route('dropshipper.catalog.index');
        }

        if ($user && !$user->activeSupplier()) {
            abort(403, 'Akses ini khusus untuk pemilik toko atau staf toko terdaftar.');
        }

        return $next($request);
    }
}

