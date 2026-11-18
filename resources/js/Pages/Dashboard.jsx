import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
  Package, 
  AlertTriangle, 
  Users, 
  Clock, 
  ShoppingCart,
  Boxes,
  Store,
  Copy,
  Check
} from 'lucide-react';

export default function Dashboard({
  user = {},
  supplier = null,
  todayPosSales = 0,
  pendingPackingCount = 0,
  lowStockCount = 0,
  activeDropshippersCount = 0,
  urgentOrders = [],
  lowStockProducts = [],
  recentPosSales = [],
  dropshipperStats = {
    connectedSuppliersCount: 0,
    maxAllowedSuppliers: 5,
    availableProductsCount: 0,
    totalOrdersCount: 0,
    activeOrdersCount: 0,
  },
  recentDropshipOrders = []
}) {
  const [copiedResi, setCopiedResi] = useState(null);

  const formatRupiah = (val) => {
    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
  };

  const copyToClipboard = (text) => {
    if (!text) return;
    navigator.clipboard.writeText(text);
    setCopiedResi(text);
    setTimeout(() => setCopiedResi(null), 2000);
  };

  const isDropshipper = user.role === 'dropshipper';

  return (
    <AuthenticatedLayout flush={true}>
      <Head title="Dashboard" />

      {isDropshipper ? (
        /* ================= TAMPILAN MITRA DROPSHIPPER ================= */
        <div className="bg-white">
          {/* 4 Attached Outline Stat Cells for Dropshipper (No Gap) */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 border-b border-slate-200 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Penyuplai Terhubung</span>
                <div className="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70">
                  <Store className="w-4 h-4 stroke-[1.8]" />
                </div>
              </div>
              <div className="text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {dropshipperStats.connectedSuppliersCount} <span className="text-xs font-normal text-slate-400">Toko</span>
              </div>
              <div className="mt-3">
                <a href="/dropshipper/suppliers" className="text-xs font-medium text-emerald-600 hover:underline">
                  Kelola kemitraan
                </a>
              </div>
            </div>

            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Produk Siap Jual</span>
                <div className="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-200/70">
                  <Package className="w-4 h-4 stroke-[1.8]" />
                </div>
              </div>
              <div className="text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {dropshipperStats.availableProductsCount} <span className="text-xs font-normal text-slate-400">Item</span>
              </div>
              <div className="mt-3">
                <a href="/dropshipper/catalog" className="text-xs font-medium text-emerald-600 hover:underline">
                  Buka katalog
                </a>
              </div>
            </div>

            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pesanan Aktif</span>
                <div className="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-200/70">
                  <Boxes className="w-4 h-4 stroke-[1.8]" />
                </div>
              </div>
              <div className="text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {dropshipperStats.activeOrdersCount}
              </div>
              <div className="mt-3">
                <a href="/dropshipper/orders" className="text-xs font-medium text-amber-600 hover:underline">
                  Pantau pesanan
                </a>
              </div>
            </div>

            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Transaksi</span>
                <div className="w-9 h-9 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center border border-cyan-200/70">
                  <ShoppingCart className="w-4 h-4 stroke-[1.8]" />
                </div>
              </div>
              <div className="text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {dropshipperStats.totalOrdersCount}
              </div>
              <div className="mt-3 text-xs text-slate-400 font-medium">
                Semua waktu
              </div>
            </div>
          </div>

          {/* Recent Dropship Orders */}
          <div className="p-5 sm:p-6">
            <div className="flex items-center justify-between pb-4 border-b border-slate-100">
              <div>
                <h3 className="font-bold text-sm sm:text-base text-slate-900">Pesanan Dropship Terbaru</h3>
                <p className="text-xs text-slate-400 mt-0.5">Status pesanan dan resi pengiriman oleh tim packing.</p>
              </div>
              <a href="/dropshipper/orders" className="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                Lihat Semua
              </a>
            </div>

            {recentDropshipOrders.length === 0 ? (
              <div className="py-14 text-center text-xs text-slate-400">
                Belum ada transaksi. Silakan buka katalog produk untuk membuat pesanan.
              </div>
            ) : (
              <div className="divide-y divide-slate-100 mt-2">
                {recentDropshipOrders.map((order) => (
                  <div key={order.id} className="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 px-2 transition">
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-mono font-semibold text-xs text-slate-800">{order.invoice_number || order.order_code}</span>
                        <span className="text-[10px] px-2.5 py-0.5 rounded-full font-medium bg-slate-100 text-slate-600 border border-slate-200">
                          {order.supplier?.store_name || 'Toko Supplier'}
                        </span>
                      </div>
                      <div className="text-xs text-slate-400 mt-1">
                        Penerima: <span className="text-slate-700 font-medium">{order.customer_name}</span> &bull; Resi: <span className="font-mono text-slate-600">{order.shipping_receipt_number || '-'}</span>
                      </div>
                    </div>
                    <div className="flex sm:flex-col items-center sm:items-end justify-between">
                      <div className="font-bold text-sm text-slate-900">{formatRupiah(order.final_amount)}</div>
                      <span className="text-[10px] font-medium px-2.5 py-0.5 rounded-full mt-1 bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">
                        {order.status}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      ) : (
        /* ================= TAMPILAN ADMIN SUPPLIER & OWNER ================= */
        <div className="bg-white">
          {/* ================= 1. GRID 4 ATTACHED OUTLINE STAT CELLS (NO GAP) ================= */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 border-b border-slate-200 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
            {/* Stat 1: Penjualan Hari Ini */}
            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                  Penjualan Hari Ini
                </span>
                <div className="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 font-bold text-xs flex items-center justify-center border border-emerald-200/70">
                  Rp
                </div>
              </div>
              <div className="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {formatRupiah(todayPosSales)}
              </div>
              <div className="mt-3 flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>POS Kasir Offline &bull; Hari ini</span>
              </div>
            </div>

            {/* Stat 2: Menunggu Packing */}
            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                  Menunggu Packing
                </span>
                <div className="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-200/70">
                  <Boxes className="w-4 h-4 stroke-[1.8]" />
                </div>
              </div>
              <div className="text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {pendingPackingCount}
              </div>
              <div className="mt-3 text-xs font-medium text-slate-400">
                {pendingPackingCount > 0 ? (
                  <span className="text-amber-600 font-semibold">Perlu dikemas segera</span>
                ) : (
                  <span>✓ Semua pesanan terkemas</span>
                )}
              </div>
            </div>

            {/* Stat 3: Alert Stok Kritis */}
            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                  Alert Stok Kritis
                </span>
                <div className="w-9 h-9 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-200/70">
                  <AlertTriangle className="w-4 h-4 stroke-[1.8]" />
                </div>
              </div>
              <div className="text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {lowStockCount}
              </div>
              <div className="mt-3 text-xs font-medium text-slate-400">
                {lowStockCount > 0 ? (
                  <span className="text-rose-600 font-semibold">⚠️ Stok &le; safety stock</span>
                ) : (
                  <span className="text-emerald-600 font-medium">✓ Stok inventori aman</span>
                )}
              </div>
            </div>

            {/* Stat 4: Mitra Dropshipper */}
            <div className="p-5 flex flex-col justify-between">
              <div className="flex items-center justify-between">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                  Mitra Dropshipper
                </span>
                <div className="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70">
                  <Users className="w-4 h-4 stroke-[1.8]" />
                </div>
              </div>
              <div className="text-3xl font-bold text-slate-900 tracking-tight mt-3">
                {activeDropshippersCount}
              </div>
              <div className="mt-3 text-xs font-medium text-slate-400">
                <span>Terkoneksi aktif ke toko</span>
              </div>
            </div>
          </div>

          {/* ================= 2. SECTION 2 KOLOM ATTACHED: PESANAN MENDESAK & PRODUK STOK MENIPIS ================= */}
          <div className="grid grid-cols-1 lg:grid-cols-2 border-b border-slate-200 divide-y lg:divide-y-0 lg:divide-x divide-slate-200">
            {/* Kolom Kiri: Pesanan Mendesak (Deadline) */}
            <div className="min-w-0 p-5 sm:p-6 flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between pb-4 border-b border-slate-100">
                  <div>
                    <h3 className="font-bold text-sm sm:text-base text-slate-900">Pesanan Mendesak (Deadline)</h3>
                    <p className="text-xs text-slate-400 mt-0.5">Urutan prioritas pengiriman marketplace</p>
                  </div>
                  <Link
                    href="/admin/packing"
                    className="px-3.5 py-1 rounded-full text-xs font-medium text-slate-500 hover:text-slate-800 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition"
                  >
                    Semua
                  </Link>
                </div>

                {urgentOrders.length === 0 ? (
                  <div className="py-16 sm:py-20 flex flex-col items-center justify-center text-center">
                    <div className="w-14 h-14 rounded-full bg-slate-50 border border-slate-100/80 flex items-center justify-center text-slate-300 mb-3">
                      <Clock className="w-6 h-6 stroke-[1.3]" />
                    </div>
                    <div className="font-semibold text-sm text-slate-700">
                      Tidak ada pesanan mendesak saat ini.
                    </div>
                    <p className="text-xs text-slate-400 mt-1 max-w-xs">
                      Semua pesanan dropshipper telah dikemas dan diserahkan ke kurir pengiriman.
                    </p>
                  </div>
                ) : (
                  <div className="divide-y divide-slate-100 mt-2">
                    {urgentOrders.map((order) => (
                      <div key={order.id} className="py-3.5 flex items-center justify-between text-xs hover:bg-slate-50/50 px-2 transition">
                        <div className="min-w-0 pr-2">
                          <div className="flex items-center gap-2">
                            <span className="font-mono font-semibold text-xs text-slate-900">
                              {order.order_code || order.invoice_number}
                            </span>
                            {order.shipping_receipt_number && (
                              <button
                                type="button"
                                onClick={() => copyToClipboard(order.shipping_receipt_number)}
                                title="Salin No Resi"
                                className="inline-flex items-center gap-1 text-[11px] font-mono text-slate-500 hover:text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md transition"
                              >
                                <span>{order.shipping_receipt_number}</span>
                                {copiedResi === order.shipping_receipt_number ? (
                                  <Check className="w-3 h-3 text-emerald-600" />
                                ) : (
                                  <Copy className="w-3 h-3 text-slate-400" />
                                )}
                              </button>
                            )}
                          </div>
                          <div className="text-slate-400 text-xs mt-1 truncate">
                            {order.customer_name} &bull; <span className="text-slate-600 font-medium">{order.dropshipper?.name || 'Mitra Dropshipper'}</span>
                          </div>
                        </div>

                        <span className="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200/70 text-xs font-medium shrink-0">
                          Siap Kemas
                        </span>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>

            {/* Kolom Kanan: Produk Stok Menipis */}
            <div className="min-w-0 p-5 sm:p-6 flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between pb-4 border-b border-slate-100">
                  <div>
                    <h3 className="font-bold text-sm sm:text-base text-slate-900">Produk Stok Menipis</h3>
                    <p className="text-xs text-slate-400 mt-0.5">Perlu restock segera</p>
                  </div>
                  <a
                    href="/admin/products"
                    className="px-3.5 py-1 rounded-full text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 transition"
                  >
                    Restock
                  </a>
                </div>

                {lowStockProducts.length === 0 ? (
                  <div className="py-16 text-center text-xs text-slate-400">
                    Semua stok produk berada di atas batas safety stock.
                  </div>
                ) : (
                  <div className="space-y-3.5 mt-4">
                    {lowStockProducts.map((prod) => (
                      <div key={prod.id} className="flex items-center justify-between gap-3 text-xs py-1">
                        <div className="min-w-0 flex-1">
                          <div className="font-medium text-slate-800 text-sm truncate" title={prod.name}>
                            {prod.name}
                          </div>
                          <div className="text-xs text-slate-400 font-mono mt-0.5">
                            SKU: {prod.sku || '-'}
                          </div>
                        </div>

                        <span className="px-3 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-600 border border-rose-100/80 shrink-0">
                          Sisa: {prod.stock}
                        </span>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* ================= 3. SECTION BAWAH ATTACHED: RIWAYAT TRANSAKSI KASIR ================= */}
          <div className="p-5 sm:p-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
              <div>
                <h3 className="font-bold text-sm sm:text-base text-slate-900">Riwayat Transaksi Kasir Offline Hari Ini</h3>
                <p className="text-xs text-slate-400 mt-0.5">5 transaksi penjualan kasir POS toko terbaru</p>
              </div>

              <a
                href="/admin/reports"
                className="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition"
              >
                Buka Laporan Akumulasi
              </a>
            </div>

            <div className="overflow-x-auto mt-2">
              <table className="w-full text-left text-xs text-slate-700">
                <thead className="text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-100">
                  <tr>
                    <th className="py-3 px-3 font-semibold">No. Invoice</th>
                    <th className="py-3 px-3 font-semibold">Waktu</th>
                    <th className="py-3 px-3 font-semibold">Kasir</th>
                    <th className="py-3 px-3 font-semibold">Pelanggan</th>
                    <th className="py-3 px-3 font-semibold">Item Produk</th>
                    <th className="py-3 px-3 font-semibold">Metode</th>
                    <th className="py-3 px-3 font-semibold text-right">Total Belanja</th>
                    <th className="py-3 px-3 font-semibold text-right">Laba Bersih</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100/80">
                  {recentPosSales.length === 0 ? (
                    <tr>
                      <td colSpan="8" className="py-12 text-center text-slate-400">
                        Belum ada transaksi kasir tercatat hari ini.
                      </td>
                    </tr>
                  ) : (
                    recentPosSales.map((sale) => (
                      <tr key={sale.id} className="hover:bg-slate-50/50 transition">
                        <td className="py-3.5 px-3 font-mono font-medium text-slate-900">
                          {sale.invoice_number}
                        </td>
                        <td className="py-3.5 px-3 text-slate-500">
                          {new Date(sale.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB
                        </td>
                        <td className="py-3.5 px-3 font-medium text-slate-800">
                          {sale.user?.name || 'Kasir'}
                        </td>
                        <td className="py-3.5 px-3 text-slate-600">
                          {sale.customer_name || 'Pelanggan Offline'}
                        </td>
                        <td className="py-3.5 px-3 text-slate-500 max-w-[200px] truncate">
                          {sale.items?.map((it) => `${it.quantity}x ${it.product_name}`).join(', ') || '-'}
                        </td>
                        <td className="py-3.5 px-3">
                          <span className="px-2.5 py-0.5 rounded-full text-[11px] font-medium uppercase bg-slate-100 text-slate-600 border border-slate-200">
                            {sale.payment_method}
                          </span>
                        </td>
                        <td className="py-3.5 px-3 text-right font-semibold text-slate-900">
                          {formatRupiah(sale.final_amount)}
                        </td>
                        <td className="py-3.5 px-3 text-right font-semibold text-emerald-600">
                          +{formatRupiah(sale.net_profit)}
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}
    </AuthenticatedLayout>
  );
}
