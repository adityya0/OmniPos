<?php

namespace App\Http\Controllers\Dropshipper;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Katalog produk dengan data stok realtime untuk dropshipper.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Ambil supplier yang terhubung atau semua supplier aktif
        $suppliers = Supplier::withCount('products')->get();
        $categories = Category::all();
        $connectedSupplierIds = $user->dropshipSuppliers()->pluck('suppliers.id')->toArray();
        $connectedCount = count($connectedSupplierIds);

        $query = Product::with(['supplier', 'category'])
            ->where('is_active', true);

        // Jika dropshipper memilih filter 'only_connected' atau default jika belum memilih filter tertentu
        if ($request->boolean('connected_only', false) && !empty($connectedSupplierIds)) {
            $query->whereIn('supplier_id', $connectedSupplierIds);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(16)->withQueryString();

        return view('dropshipper.catalog', compact('products', 'suppliers', 'categories', 'connectedSupplierIds', 'connectedCount'));
    }
}
