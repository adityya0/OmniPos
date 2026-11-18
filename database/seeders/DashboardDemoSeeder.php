<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardDemoSeeder extends Seeder
{
    /**
     * Seed realistic operational data for final project presentation.
     */
    public function run(): void
    {
        $supplier = Supplier::first();
        if (!$supplier) {
            return;
        }

        // 1. Buat beberapa produk dengan stok menipis (low stock) agar Alert Stok Kritis aktif
        $category = Category::where('supplier_id', $supplier->id)->first();
        $categoryId = $category?->id;

        $lowStockData = [
            [
                'name' => 'Lego Robot Mini Transformer',
                'sku' => 'TOY-LEG-103',
                'image' => 'products/product-lego-robot.jpg',
                'stock' => 2,
                'safety_stock' => 10,
                'cost_price' => 25000,
                'selling_price' => 50000,
                'dropship_price' => 38000,
            ],
            [
                'name' => 'Mobil Remote Control Offroad 4WD',
                'sku' => 'TOY-RCO-104',
                'image' => 'products/product-rc-car.jpg',
                'stock' => 4,
                'safety_stock' => 8,
                'cost_price' => 85000,
                'selling_price' => 160000,
                'dropship_price' => 125000,
            ],
            [
                'name' => 'Slime Galaxy Glow in The Dark',
                'sku' => 'TOY-SLM-105',
                'image' => 'products/product-slime-galaxy.jpg',
                'stock' => 0,
                'safety_stock' => 15,
                'cost_price' => 12000,
                'selling_price' => 25000,
                'dropship_price' => 18000,
            ],
        ];

        foreach ($lowStockData as $item) {
            Product::firstOrCreate(
                ['supplier_id' => $supplier->id, 'sku' => $item['sku']],
                [
                    'category_id' => $categoryId,
                    'name' => $item['name'],
                    'stock' => $item['stock'],
                    'safety_stock' => $item['safety_stock'],
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                    'dropship_price' => $item['dropship_price'],
                    'length' => 15,
                    'width' => 10,
                    'height' => 5,
                    'weight' => 250,
                    'is_active' => true,
                ]
            );
        }

        // 2. Buat mitra dropshipper terdaftar
        $dropshippers = [
            [
                'name' => 'Toko Berkah Toys',
                'email' => 'berkahtoys@dropshipper.test',
            ],
            [
                'name' => 'Kidszone Store Official',
                'email' => 'kidszone@dropshipper.test',
            ],
            [
                'name' => 'Dunia Mainan Anak',
                'email' => 'duniamainan@dropshipper.test',
            ],
        ];

        $dropshipperUsers = [];
        foreach ($dropshippers as $ds) {
            $user = User::firstOrCreate(
                ['email' => $ds['email']],
                [
                    'name' => $ds['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'dropshipper',
                ]
            );

            DB::table('supplier_dropshipper')->updateOrInsert(
                ['supplier_id' => $supplier->id, 'dropshipper_id' => $user->id],
                [
                    'wallet_balance' => 750000.00,
                    'status' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $dropshipperUsers[] = $user;
        }

        // 3. Buat pesanan dropship mendesak dengan batas SLA (marketplace_deadline)
        $urgentOrdersData = [
            [
                'order_code' => 'DRP-SHP-20261025-01',
                'shipping_receipt_number' => 'SPXID0482910482',
                'deadline' => Carbon::now()->addHours(2), // Sisa 2 Jam (SLA Mendesak)
                'dropshipper' => $dropshipperUsers[0],
                'customer_name' => 'Siti Rahmawati (Shopee)',
                'amount' => 125000,
                'cost' => 85000,
            ],
            [
                'order_code' => 'DRP-TTK-20261025-02',
                'shipping_receipt_number' => 'JX99201948291',
                'deadline' => Carbon::now()->addHours(5), // Sisa 5 Jam
                'dropshipper' => $dropshipperUsers[1],
                'customer_name' => 'Budi Santoso (TikTok Shop)',
                'amount' => 76000,
                'cost' => 50000,
            ],
            [
                'order_code' => 'DRP-TKP-20261025-03',
                'shipping_receipt_number' => 'TKP0192838471',
                'deadline' => Carbon::now()->addHours(11), // Sisa 11 Jam
                'dropshipper' => $dropshipperUsers[2],
                'customer_name' => 'Dewi Lestari (Tokopedia)',
                'amount' => 50000,
                'cost' => 32000,
            ],
            [
                'order_code' => 'DRP-SHP-20261025-04',
                'shipping_receipt_number' => 'SPXID0987654321',
                'deadline' => Carbon::now()->addDay(),
                'dropshipper' => $dropshipperUsers[0],
                'customer_name' => 'Ahmad Fauzi (Shopee)',
                'amount' => 100000,
                'cost' => 64000,
            ],
        ];

        foreach ($urgentOrdersData as $idx => $uo) {
            $invNum = 'INV-DRP-' . date('Ymd') . '-' . str_pad((string)($idx + 1), 3, '0', STR_PAD_LEFT);
            $order = Order::firstOrCreate(
                ['order_code' => $uo['order_code']],
                [
                    'invoice_number' => $invNum,
                    'shipping_receipt_number' => $uo['shipping_receipt_number'],
                    'supplier_id' => $supplier->id,
                    'user_id' => $uo['dropshipper']->id,
                    'transaction_type' => 'dropship',
                    'customer_name' => $uo['customer_name'],
                    'status' => 'menunggu_packing',
                    'marketplace_deadline' => $uo['deadline'],
                    'payment_method' => 'transfer',
                    'payment_status' => 'paid',
                    'total_cost' => $uo['cost'],
                    'subtotal' => $uo['amount'],
                    'discount' => 0,
                    'final_amount' => $uo['amount'],
                    'net_profit' => $uo['amount'] - $uo['cost'],
                ]
            );

            // Item pesanan
            if ($order->wasRecentlyCreated) {
                $p = Product::where('supplier_id', $supplier->id)->first();
                if ($p) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $p->id,
                        'product_name' => $p->name,
                        'product_sku' => $p->sku,
                        'cost_price' => $p->cost_price,
                        'unit_price' => $p->dropship_price,
                        'quantity' => 2,
                        'subtotal' => $uo['amount'],
                        'profit' => $uo['amount'] - $uo['cost'],
                    ]);
                }
            }
        }
    }
}
