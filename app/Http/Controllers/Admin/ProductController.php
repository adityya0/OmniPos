<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar inventori produk toko supplier.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $supplier = $user->activeSupplier();

        if (!$supplier) {
            abort(403, 'Anda belum terhubung dengan toko supplier.');
        }

        $query = Product::with(['category', 'tiers'])->where('supplier_id', $supplier->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filter === 'low_stock') {
            $query->where('stock', '<=', 5);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('supplier_id', $supplier->id)->orWhereNull('supplier_id')->get();

        // Hitung kuantitas stok yang sedang dibooking oleh pesanan online dropship (belum selesai/dikirim)
        $pendingDropshipMap = \App\Models\OrderItem::whereHas('order', function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id)
                ->where('transaction_type', 'dropship')
                ->whereIn('status', ['menunggu_approval', 'menunggu_packing', 'dipacking']);
        })
            ->selectRaw('product_id, SUM(quantity) as total_pending')
            ->groupBy('product_id')
            ->pluck('total_pending', 'product_id');

        $totalPendingDropship = (int) $pendingDropshipMap->sum();
        $totalStock = (int) Product::where('supplier_id', $supplier->id)->sum('stock');

        $stats = [
            'total_products' => Product::where('supplier_id', $supplier->id)->count(),
            'total_stock' => $totalStock,
            'total_pending_dropship' => $totalPendingDropship,
            'total_warehouse_stock' => $totalStock + $totalPendingDropship,
            'low_stock' => Product::where('supplier_id', $supplier->id)->where('stock', '<=', 5)->count(),
        ];

        // Lampirkan informasi stok multi-kanal ke tiap produk
        $products->getCollection()->transform(function ($product) use ($pendingDropshipMap) {
            $product->pending_dropship_qty = (int) ($pendingDropshipMap[$product->id] ?? 0);
            $product->total_physical_stock = $product->stock + $product->pending_dropship_qty;
            $product->available_dropship_stock = max(0, $product->stock - ($product->safety_stock ?? 0));
            return $product;
        });

        // Ambil tingkatan level harga yang aktif di toko supplier ini
        $configuredTiers = \App\Models\ProductTier::whereHas('product', fn($q) => $q->where('supplier_id', $supplier->id))
            ->select('tier_level', 'name', 'unit_multiplier')
            ->groupBy('tier_level', 'name', 'unit_multiplier')
            ->orderBy('tier_level')
            ->get()
            ->unique('tier_level')
            ->values();

        if ($configuredTiers->isEmpty()) {
            $configuredTiers = collect([
                (object)['tier_level' => 1, 'name' => 'Eceran', 'unit_multiplier' => 1],
                (object)['tier_level' => 2, 'name' => 'Grosir', 'unit_multiplier' => 12],
                (object)['tier_level' => 3, 'name' => 'Grosir Besar', 'unit_multiplier' => 72],
            ]);
        }

        return view('admin.products.index', compact('supplier', 'products', 'categories', 'stats', 'configuredTiers'));
    }

    /**
     * Form tambah produk baru (Tim Media & Admin).
     */
    public function create(): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $categories = Category::where('supplier_id', $supplier->id)->orWhereNull('supplier_id')->get();

        return view('admin.products.create', compact('supplier', 'categories'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'new_category_name' => ['nullable', 'string', 'max:100'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'length' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'dropship_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'marketing_assets_url' => ['nullable', 'url', 'max:1000'],
            'tiers' => ['nullable', 'array', 'min:1'],
            'tiers.*.level' => ['nullable', 'integer'],
            'tiers.*.name' => ['required_with:tiers', 'string', 'max:100'],
            'tiers.*.multiplier' => ['required_with:tiers', 'integer', 'min:1'],
            'tiers.*.price' => ['required_with:tiers', 'numeric', 'min:0'],
        ]);

        $categoryId = $validated['category_id'] ?? null;

        // Jika user membuat kategori baru langsung di form
        if (!empty($validated['new_category_name'])) {
            $catSlug = \Illuminate\Support\Str::slug($validated['new_category_name']);
            $cat = Category::firstOrCreate(
                ['supplier_id' => $supplier->id, 'slug' => $catSlug],
                ['name' => trim($validated['new_category_name'])]
            );
            $categoryId = $cat->id;
        }

        if (!$categoryId) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori produk wajib dipilih atau dibuat baru.']);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $baseSellingPrice = 0;
        if (!empty($request->tiers) && isset($request->tiers[0]['price'])) {
            $baseSellingPrice = floatval($request->tiers[0]['price']);
        } elseif (isset($validated['selling_price'])) {
            $baseSellingPrice = floatval($validated['selling_price']);
        }

        $product = Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $categoryId,
            'sku' => strtoupper($validated['sku']),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'length' => $validated['length'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'cost_price' => $validated['cost_price'],
            'selling_price' => $baseSellingPrice,
            'dropship_price' => $validated['dropship_price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'marketing_assets_url' => $validated['marketing_assets_url'] ?? null,
            'is_active' => true,
        ]);

        // Simpan tingkatan level harga (Tiers)
        if (!empty($request->tiers) && is_array($request->tiers)) {
            foreach ($request->tiers as $index => $t) {
                $product->tiers()->create([
                    'tier_level' => $index + 1,
                    'name' => trim($t['name']) ?: 'Level ' . ($index + 1),
                    'unit_multiplier' => max(1, intval($t['multiplier'] ?? 1)),
                    'price' => floatval($t['price'] ?? 0),
                ]);
            }
        } else {
            // Default 3 level jika tidak ada input tiers
            $product->tiers()->createMany([
                ['tier_level' => 1, 'name' => 'Eceran', 'unit_multiplier' => 1, 'price' => $baseSellingPrice],
                ['tier_level' => 2, 'name' => 'Grosir', 'unit_multiplier' => 12, 'price' => max(0, round($baseSellingPrice * 12 * 0.9, -3))],
                ['tier_level' => 3, 'name' => 'Grosir Besar', 'unit_multiplier' => 72, 'price' => max(0, round($baseSellingPrice * 72 * 0.8, -3))],
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan ke inventori dengan multi-level harga.');
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product): View
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier || $product->supplier_id !== $supplier->id) {
            abort(403);
        }

        $product->load('tiers');
        $categories = Category::where('supplier_id', $supplier->id)->orWhereNull('supplier_id')->get();

        return view('admin.products.edit', compact('supplier', 'product', 'categories'));
    }

    /**
     * Perbarui data produk.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier || $product->supplier_id !== $supplier->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'description' => ['nullable', 'string'],
            'length' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'dropship_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'marketing_assets_url' => ['nullable', 'url', 'max:1000'],
            'tiers' => ['nullable', 'array', 'min:1'],
            'tiers.*.level' => ['nullable', 'integer'],
            'tiers.*.name' => ['required_with:tiers', 'string', 'max:100'],
            'tiers.*.multiplier' => ['required_with:tiers', 'integer', 'min:1'],
            'tiers.*.price' => ['required_with:tiers', 'numeric', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $baseSellingPrice = $product->selling_price;
        if (!empty($request->tiers) && isset($request->tiers[0]['price'])) {
            $baseSellingPrice = floatval($request->tiers[0]['price']);
        } elseif (isset($validated['selling_price'])) {
            $baseSellingPrice = floatval($validated['selling_price']);
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'sku' => strtoupper($validated['sku']),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'length' => $validated['length'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'cost_price' => $validated['cost_price'],
            'selling_price' => $baseSellingPrice,
            'dropship_price' => $validated['dropship_price'],
            'stock' => $validated['stock'],
            'marketing_assets_url' => $validated['marketing_assets_url'] ?? null,
        ]);

        // Perbarui tingkatan level harga (Tiers)
        if (!empty($request->tiers) && is_array($request->tiers)) {
            $product->tiers()->delete();
            foreach ($request->tiers as $index => $t) {
                $product->tiers()->create([
                    'tier_level' => $index + 1,
                    'name' => trim($t['name']) ?: 'Level ' . ($index + 1),
                    'unit_multiplier' => max(1, intval($t['multiplier'] ?? 1)),
                    'price' => floatval($t['price'] ?? 0),
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Data produk & multi-level harga berhasil diperbarui.');
    }

    /**
     * Hapus produk.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier || $product->supplier_id !== $supplier->id) {
            abort(403);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus dari inventori.');
    }

    /**
     * Hapus massal produk yang dipilih (Bulk Delete).
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ], [
            'ids.required' => 'Pilih setidaknya satu produk yang ingin dihapus.',
        ]);

        $products = Product::where('supplier_id', $supplier->id)
            ->whereIn('id', $validated['ids'])
            ->get();

        $count = 0;
        foreach ($products as $product) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            $count++;
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Berhasil menghapus {$count} produk terpilih dari inventori.");
    }

    /**
     * Restock massal produk yang dipilih (Bulk Restock).
     */
    public function bulkRestock(Request $request): JsonResponse|RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'additional_stock' => ['required', 'integer', 'min:1'],
        ], [
            'ids.required' => 'Pilih setidaknya satu produk yang ingin direstock.',
            'additional_stock.min' => 'Jumlah tambahan stok minimal 1 unit.',
        ]);

        $add = $validated['additional_stock'];
        $count = Product::where('supplier_id', $supplier->id)
            ->whereIn('id', $validated['ids'])
            ->increment('stock', $add);

        // Ambil stok terbaru produk yang direstock
        $updatedProducts = Product::where('supplier_id', $supplier->id)
            ->whereIn('id', $validated['ids'])
            ->pluck('stock', 'id');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil menambah {$add} unit stok untuk {$count} produk terpilih.",
                'updated_stocks' => $updatedProducts,
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Berhasil menambah {$add} unit stok untuk {$count} produk terpilih.");
    }

    /**
     * Unduh file template CSV / Excel untuk import produk massal.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_produk.csv"',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM agar Excel di Windows/Mac otomatis mengenali encoding dengan tepat
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom format template
            fputcsv($handle, [
                'nama_produk',
                'kategori',
                'hpp_modal',
                'harga_jual_pos',
                'harga_dropship',
                'stok',
                'berat_gram',
                'panjang_cm',
                'lebar_cm',
                'tinggi_cm',
                'deskripsi'
            ]);

            // Baris contoh untuk panduan pengguna
            fputcsv($handle, [
                'Kemeja Katun Pria Polos Lengan Panjang',
                'Pakaian Pria',
                '45000',
                '75000',
                '60000',
                '25',
                '250',
                '20',
                '15',
                '3',
                'Bahan katun premium, adem dan nyaman digunakan'
            ]);

            fputcsv($handle, [
                'Termos Stainless Steel 500ml Tahan Panas',
                'Peralatan Rumah',
                '30000',
                '55000',
                '42000',
                '50',
                '300',
                '8',
                '8',
                '25',
                'Menjaga suhu panas dan dingin hingga 12 jam'
            ]);

            fputcsv($handle, [
                'Case Silikon Transparan Anti Shock',
                'Aksesoris Handphone',
                '10000',
                '25000',
                '18000',
                '100',
                '50',
                '15',
                '8',
                '1',
                'Bahan TPU tebal, melindungi dari benturan'
            ]);

            fclose($handle);
        }, 'template_import_produk.csv', $headers);
    }

    /**
     * Import produk massal dari file CSV / Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'file.required' => 'Silakan pilih file Excel / CSV yang akan diunggah.',
            'file.mimes' => 'File harus berformat CSV (.csv).',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $content = file_get_contents($filePath);
        if (empty(trim($content))) {
            return back()->withErrors(['file' => 'File CSV kosong atau tidak memiliki data.']);
        }

        // Deteksi delimiter apakah titik koma (;) atau koma (,)
        $firstLine = strtok($content, "\r\n");
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return back()->withErrors(['file' => 'Gagal membaca file CSV.']);
        }

        // Hapus UTF-8 BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['file' => 'Header kolom tidak ditemukan pada file CSV.']);
        }

        // Normalisasi nama kolom header
        $headerMap = [];
        foreach ($header as $index => $colName) {
            $normalized = strtolower(trim($colName));
            $normalized = str_replace([' ', '-'], '_', $normalized);
            $normalized = preg_replace('/[^a-z0-9_]/', '', $normalized);
            $headerMap[$normalized] = $index;
        }

        $importedCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Lewati baris kosong
                if (empty(array_filter($row, fn($val) => trim((string)$val) !== ''))) {
                    continue;
                }

                $getValue = function($key, $default = null) use ($headerMap, $row) {
                    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                        $val = trim((string)$row[$headerMap[$key]]);
                        return $val !== '' ? $val : $default;
                    }
                    return $default;
                };

                $name = $getValue('nama_produk') ?? $getValue('nama') ?? $getValue('name');
                if (!$name) {
                    continue;
                }

                $catName = $getValue('kategori') ?? $getValue('category') ?? 'Umum';
                $catSlug = \Illuminate\Support\Str::slug($catName);
                $category = Category::firstOrCreate(
                    ['supplier_id' => $supplier->id, 'slug' => $catSlug],
                    ['name' => $catName]
                );

                $cleanNumber = function($val, $default = 0) {
                    if ($val === null) return $default;
                    $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '.', (string)$val));
                    return is_numeric($clean) ? (float)$clean : $default;
                };

                $costPrice = $cleanNumber($getValue('hpp_modal') ?? $getValue('hpp') ?? $getValue('modal') ?? $getValue('cost_price'), 0);
                $sellingPrice = $cleanNumber($getValue('harga_jual_pos') ?? $getValue('harga_jual') ?? $getValue('harga') ?? $getValue('selling_price'), 0);
                $dropshipPrice = $cleanNumber($getValue('harga_dropship') ?? $getValue('dropship') ?? $getValue('dropship_price'), $sellingPrice);
                $stock = (int) $cleanNumber($getValue('stok') ?? $getValue('stock') ?? $getValue('qty'), 0);
                $weight = $cleanNumber($getValue('berat_gram') ?? $getValue('berat') ?? $getValue('weight'), 100);
                $length = $cleanNumber($getValue('panjang_cm') ?? $getValue('panjang') ?? $getValue('length'), 10);
                $width = $cleanNumber($getValue('lebar_cm') ?? $getValue('lebar') ?? $getValue('width'), 10);
                $height = $cleanNumber($getValue('tinggi_cm') ?? $getValue('tinggi') ?? $getValue('height'), 10);
                $description = $getValue('deskripsi') ?? $getValue('description');

                $sku = Product::generateSku($name, $category->name);

                Product::create([
                    'supplier_id' => $supplier->id,
                    'category_id' => $category->id,
                    'sku' => $sku,
                    'name' => $name,
                    'description' => $description,
                    'length' => max(0.1, $length),
                    'width' => max(0.1, $width),
                    'height' => max(0.1, $height),
                    'weight' => max(1, $weight),
                    'cost_price' => $costPrice,
                    'selling_price' => $sellingPrice,
                    'dropship_price' => $dropshipPrice,
                    'stock' => max(0, $stock),
                    'image' => null, // Opsional / default placeholder
                    'is_active' => true,
                ]);

                $importedCount++;
            }

            DB::commit();
            fclose($handle);

            if ($importedCount === 0) {
                return back()->withErrors(['file' => 'Tidak ada baris data produk yang valid di dalam file.']);
            }

            return redirect()->route('admin.products.index')
                ->with('success', "Berhasil mengimpor {$importedCount} produk baru ke dalam inventori!");
        } catch (\Throwable $e) {
            DB::rollBack();
            if (is_resource($handle)) {
                fclose($handle);
            }
            return back()->withErrors(['file' => 'Terjadi kesalahan saat memproses file CSV: ' . $e->getMessage()]);
        }
    }

    /**
     * Endpoint API / AJAX untuk membuat SKU otomatis unik dari nama & kategori.
     */
    public function generateSku(Request $request): JsonResponse
    {
        $name = $request->query('name', '');
        $categoryId = $request->query('category_id');

        $categoryName = null;
        if ($categoryId) {
            $category = Category::find($categoryId);
            $categoryName = $category?->name;
        }

        $sku = Product::generateSku($name, $categoryName);

        return response()->json(['sku' => $sku]);
    }

    /**
     * Endpoint API untuk membuat kategori baru secara instan dari form produk.
     */
    public function storeCategory(Request $request): JsonResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier tidak ditemukan.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $name = trim($validated['name']);
        $slug = \Illuminate\Support\Str::slug($name);

        $category = Category::firstOrCreate(
            ['supplier_id' => $supplier->id, 'slug' => $slug],
            ['name' => $name]
        );

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Kategori berhasil dibuat!'
        ]);
    }

    /**
     * Tambah stok cepat langsung dari tabel produk.
     */
    public function quickRestock(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'additional_stock' => ['required', 'integer', 'min:1'],
            'safety_stock' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::where('supplier_id', $supplier->id)->findOrFail($id);
        $product->increment('stock', $validated['additional_stock']);
        if ($request->has('safety_stock') && $request->safety_stock !== null) {
            $product->safety_stock = (int) $request->safety_stock;
            $product->save();
        }
        $product->refresh();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Stok {$product->name} berhasil ditambah sebanyak {$validated['additional_stock']} unit.",
                'product_id' => $product->id,
                'new_stock' => $product->stock,
                'safety_stock' => $product->safety_stock,
            ]);
        }

        return back()->with('success', "Stok {$product->name} berhasil ditambah sebanyak {$validated['additional_stock']} unit (Stok sekarang: {$product->stock}).");
    }

    /**
     * Update massal nama dan satuan tingkatan level harga untuk seluruh produk toko.
     */
    public function bulkUpdateTiers(Request $request): RedirectResponse
    {
        $supplier = Auth::user()->activeSupplier();
        if (!$supplier) {
            abort(403, 'Akses toko supplier tidak ditemukan.');
        }

        $validated = $request->validate([
            'tiers' => ['required', 'array', 'min:1'],
            'tiers.*.tier_level' => ['required', 'integer', 'min:1'],
            'tiers.*.name' => ['required', 'string', 'max:100'],
            'tiers.*.unit_multiplier' => ['required', 'integer', 'min:1'],
            'recalculate_prices' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($supplier, $validated) {
            $products = Product::where('supplier_id', $supplier->id)->get();
            $submittedLevels = collect($validated['tiers'])->pluck('tier_level')->map(fn($v) => (int)$v)->all();

            foreach ($validated['tiers'] as $tierInput) {
                $level = (int)$tierInput['tier_level'];
                $name = trim($tierInput['name']);
                $multiplier = max(1, (int)$tierInput['unit_multiplier']);

                foreach ($products as $product) {
                    $tier = \App\Models\ProductTier::where('product_id', $product->id)
                        ->where('tier_level', $level)
                        ->first();

                    if ($tier) {
                        $oldMultiplier = (int)$tier->unit_multiplier;
                        $tier->name = $name;
                        $tier->unit_multiplier = $multiplier;

                        if (!empty($validated['recalculate_prices']) && $oldMultiplier !== $multiplier && $oldMultiplier > 0) {
                            $pricePerUnit = (float)$tier->price / $oldMultiplier;
                            $tier->price = max(0, round($pricePerUnit * $multiplier, -3));
                        }
                        $tier->save();
                    } else {
                        // Level baru dibuat massal untuk produk ini
                        $basePrice = (float)$product->selling_price;
                        $discountFactor = $level === 1 ? 1.0 : ($level === 2 ? 0.9 : 0.8);
                        $newPrice = max(0, round($basePrice * $multiplier * $discountFactor, -3));

                        \App\Models\ProductTier::create([
                            'product_id' => $product->id,
                            'tier_level' => $level,
                            'name' => $name,
                            'unit_multiplier' => $multiplier,
                            'price' => $newPrice,
                        ]);
                    }
                }
            }

            // Hapus level yang dihapus dari form (hanya level > 1, level 1 wajib ada)
            \App\Models\ProductTier::whereHas('product', fn($q) => $q->where('supplier_id', $supplier->id))
                ->whereNotIn('tier_level', $submittedLevels)
                ->where('tier_level', '>', 1)
                ->delete();
        });

        $productCount = Product::where('supplier_id', $supplier->id)->count();

        return redirect()->route('admin.products.index')
            ->with('success', "Kategori tingkatan level berhasil diperbarui dan diterapkan ke seluruh {$productCount} produk toko!");
    }
}
