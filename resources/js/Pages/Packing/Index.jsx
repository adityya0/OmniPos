import React, { useState, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
  Package, 
  Clock, 
  CheckCircle2, 
  Truck, 
  Search, 
  Copy, 
  Check, 
  ExternalLink, 
  PlayCircle,
  Send,
  XCircle,
  Boxes,
  Users,
  Ban,
  AlertTriangle,
  X,
  RotateCcw
} from 'lucide-react';

export default function PackingIndex({
  orders = [],
  counts = {},
  statusFilter: initialFilter = 'menunggu_packing',
  isTimPacking = false,
  supplier = {}
}) {
  const [activeTab, setActiveTab] = useState(initialFilter);
  const [searchQuery, setSearchQuery] = useState('');
  const [copiedResi, setCopiedResi] = useState(null);
  const [loadingId, setLoadingId] = useState(null);

  // State Modal Pembatalan
  const [cancelModalOpen, setCancelModalOpen] = useState(false);
  const [cancelTargetOrder, setCancelTargetOrder] = useState(null);
  const [cancelReason, setCancelReason] = useState('');
  const [isSubmittingCancel, setIsSubmittingCancel] = useState(false);

  const rawOrders = orders.data || orders;

  const copyToClipboard = (text, id) => {
    if (!text) return;
    navigator.clipboard.writeText(text);
    setCopiedResi(id);
    setTimeout(() => setCopiedResi(null), 2000);
  };

  const filteredOrders = useMemo(() => {
    return rawOrders.filter(order => {
      if (activeTab !== 'semua') {
        if (activeTab === 'dikirim') {
          if (order.status !== 'dikirim' && order.status !== 'selesai') return false;
        } else if (order.status !== activeTab) {
          return false;
        }
      }
      if (!searchQuery.trim()) return true;
      const q = searchQuery.toLowerCase();
      const code = (order.order_code || order.invoice_number || '').toLowerCase();
      const customer = (order.customer_name || '').toLowerCase();
      const dropshipper = (order.dropshipper?.name || '').toLowerCase();
      const resi = (order.shipping_receipt_number || '').toLowerCase();
      const courier = (order.courier_name || '').toLowerCase();
      const items = (order.items || []).map(i => i.product_name).join(' ').toLowerCase();

      return code.includes(q) || customer.includes(q) || dropshipper.includes(q) || resi.includes(q) || courier.includes(q) || items.includes(q);
    });
  }, [rawOrders, activeTab, searchQuery]);

  // Actions with Inertia router (smooth SPA transition)
  const handleApprove = (orderId) => {
    setLoadingId(orderId);
    router.post(`/admin/packing/${orderId}/approve`, {}, {
      preserveScroll: true,
      onFinish: () => setLoadingId(null)
    });
  };

  const openCancelModal = (order) => {
    setCancelTargetOrder(order);
    setCancelReason('Permintaan pembeli / dropshipper');
    setCancelModalOpen(true);
  };

  const closeCancelModal = () => {
    if (isSubmittingCancel) return;
    setCancelModalOpen(false);
    setCancelTargetOrder(null);
    setCancelReason('');
  };

  const submitCancelOrder = () => {
    if (!cancelTargetOrder) return;
    if (!cancelReason.trim() || cancelReason.trim().length < 5) {
      alert('Mohon isi alasan pembatalan minimal 5 karakter.');
      return;
    }

    setIsSubmittingCancel(true);
    router.post(`/admin/packing/${cancelTargetOrder.id}/cancel`, {
      cancel_reason: cancelReason.trim()
    }, {
      preserveScroll: true,
      onFinish: () => {
        setIsSubmittingCancel(false);
        closeCancelModal();
      }
    });
  };

  const handleUpdateStatus = (orderId, newStatus) => {
    setLoadingId(orderId);
    router.patch(`/admin/packing/${orderId}/status`, { status: newStatus }, {
      preserveScroll: true,
      onFinish: () => setLoadingId(null)
    });
  };

  const cancelReasonPresets = [
    'Permintaan pembeli / dropshipper',
    'Pembeli membatalkan pesanan di marketplace',
    'Stok fisik rusak / barang tidak layak kirim',
    'Alamat pengiriman tidak lengkap / tidak valid',
    'Salah pilih varian barang oleh dropshipper'
  ];

  const tabs = [
    ...(!isTimPacking ? [{
      id: 'menunggu_approval',
      label: 'Approval',
      count: counts.menunggu_approval || 0,
      badgeColor: 'bg-rose-100 text-rose-700',
      activeColor: 'bg-rose-600 text-white shadow-rose-200'
    }] : []),
    {
      id: 'menunggu_packing',
      label: 'Siap Kemas',
      count: counts.menunggu_packing || 0,
      badgeColor: 'bg-amber-100 text-amber-700',
      activeColor: 'bg-amber-500 text-white shadow-amber-200'
    },
    {
      id: 'dipacking',
      label: 'Dipacking',
      count: counts.dipacking || 0,
      badgeColor: 'bg-emerald-100 text-emerald-700',
      activeColor: 'bg-emerald-600 text-white shadow-emerald-200'
    },
    {
      id: 'dikirim',
      label: 'Terkirim',
      count: counts.dikirim || 0,
      badgeColor: 'bg-slate-100 text-slate-700',
      activeColor: 'bg-slate-900 text-white shadow-slate-200'
    },
    ...(!isTimPacking ? [{
      id: 'dibatalkan',
      label: 'Dibatalkan',
      count: counts.dibatalkan || 0,
      badgeColor: 'bg-rose-100 text-rose-700',
      activeColor: 'bg-rose-700 text-white shadow-rose-200'
    }] : []),
    {
      id: 'semua',
      label: 'Semua',
      count: counts.semua !== undefined ? counts.semua : rawOrders.length,
      badgeColor: 'bg-slate-100 text-slate-700',
      activeColor: 'bg-slate-800 text-white shadow-slate-200'
    }
  ];

  return (
    <AuthenticatedLayout
      header={
        <div className="flex items-center justify-between">
          <div>
            <h2 className="font-black text-lg text-slate-900">
              {isTimPacking ? 'Antrean Packing' : 'Antrean Packing & Approval'}
            </h2>
            <p className="text-xs text-slate-400 mt-0.5">
              Kelola pesanan dan resi pengiriman.
            </p>
          </div>
          <div className="flex items-center gap-2">
            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-xl">
              <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
              Live
            </span>
          </div>
        </div>
      }
    >
      <Head title="Antrean Packing" />

      <div className="bg-white">
        {/* Top Summary Bar - Attached Outline Cells (No Gap) */}
        <div className="grid grid-cols-2 lg:grid-cols-4 border-b border-slate-200 divide-x divide-y lg:divide-y-0 divide-slate-200 bg-white">
          {!isTimPacking && (
            <div 
              onClick={() => setActiveTab('menunggu_approval')}
              className={`p-4 sm:p-5 flex items-center gap-3 cursor-pointer transition hover:bg-slate-50 ${
                activeTab === 'menunggu_approval' ? 'bg-rose-50/40' : 'bg-white'
              }`}
            >
              <div className="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/70 flex items-center justify-center shrink-0">
                <Clock className="w-5 h-5" />
              </div>
              <div>
                <div className="text-2xl font-black text-rose-600 leading-none">{counts.menunggu_approval || 0}</div>
                <div className="text-[10px] font-bold text-rose-700 uppercase tracking-wide mt-1">Menunggu Approval</div>
              </div>
            </div>
          )}

          <div 
            onClick={() => setActiveTab('menunggu_packing')}
            className={`p-4 sm:p-5 flex items-center gap-3 cursor-pointer transition hover:bg-slate-50 ${
              activeTab === 'menunggu_packing' ? 'bg-amber-50/40' : 'bg-white'
            }`}
          >
            <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/70 flex items-center justify-center shrink-0">
              <Package className="w-5 h-5" />
            </div>
            <div>
              <div className="text-2xl font-black text-amber-600 leading-none">{counts.menunggu_packing || 0}</div>
              <div className="text-[10px] font-bold text-amber-700 uppercase tracking-wide mt-1">Siap Dikemas</div>
            </div>
          </div>

          <div 
            onClick={() => setActiveTab('dipacking')}
            className={`p-4 sm:p-5 flex items-center gap-3 cursor-pointer transition hover:bg-slate-50 ${
              activeTab === 'dipacking' ? 'bg-emerald-50/40' : 'bg-white'
            }`}
          >
            <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/70 flex items-center justify-center shrink-0">
              <Boxes className="w-5 h-5" />
            </div>
            <div>
              <div className="text-2xl font-black text-emerald-600 leading-none">{counts.dipacking || 0}</div>
              <div className="text-[10px] font-bold text-emerald-700 uppercase tracking-wide mt-1">Sedang Dipacking</div>
            </div>
          </div>

          <div 
            onClick={() => setActiveTab('dikirim')}
            className={`p-4 sm:p-5 flex items-center gap-3 cursor-pointer transition hover:bg-slate-50 ${
              activeTab === 'dikirim' ? 'bg-slate-100/60' : 'bg-white'
            }`}
          >
            <div className="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
              <Truck className="w-5 h-5" />
            </div>
            <div>
              <div className="text-2xl font-black text-slate-800 leading-none">{counts.dikirim || 0}</div>
              <div className="text-[10px] font-bold text-slate-500 uppercase tracking-wide mt-1">Terkirim / Selesai</div>
            </div>
          </div>
        </div>

        {/* Tab & Instant Search Control */}
        <div className="px-5 sm:px-6 py-3.5 border-b border-slate-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-3">
          <div className="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar">
            {tabs.map(tab => {
              const isActive = activeTab === tab.id;
              return (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer ${
                    isActive 
                      ? `${tab.activeColor}` 
                      : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'
                  }`}
                >
                  <span>{tab.label}</span>
                  <span className={`px-2 py-0.5 rounded-full text-[10px] font-extrabold ${
                    isActive ? 'bg-white/20 text-white' : tab.badgeColor
                  }`}>
                    {tab.count}
                  </span>
                </button>
              );
            })}
          </div>

          <div className="relative min-w-[260px]">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari order, dropshipper, resi..."
              className="w-full pl-9 pr-4 py-2 bg-slate-50/70 focus:bg-white rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            />
            {searchQuery && (
              <button 
                onClick={() => setSearchQuery('')}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer"
              >
                ✕
              </button>
            )}
          </div>
        </div>

        {/* Order Cells Grid (Attached Outline, No Gap) */}
        {filteredOrders.length === 0 ? (
          <div className="bg-white border-b border-slate-200 p-14 text-center">
            <div className="w-14 h-14 mx-auto rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
              <Package className="w-7 h-7" />
            </div>
            <h3 className="font-bold text-slate-800 text-sm">Tidak Ada Pesanan Ditemukan</h3>
            <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
              {searchQuery 
                ? `Tidak ada hasil pencarian untuk "${searchQuery}".`
                : (isTimPacking 
                    ? 'Pesanan muncul setelah Admin menyetujui pesanan dropship.' 
                    : 'Belum ada pesanan pada status ini.')}
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 bg-white">
            {filteredOrders.map(order => {
              const isHold = order.status === 'menunggu_approval';
              const isReady = order.status === 'menunggu_packing';
              const isPacking = order.status === 'dipacking';
              const isSent = ['dikirim', 'selesai'].includes(order.status);
              const isCancelled = order.status === 'dibatalkan';

              let borderAccent = 'border-t-slate-400';
              if (isHold) borderAccent = 'border-t-rose-500';
              else if (isReady) borderAccent = 'border-t-amber-500';
              else if (isPacking) borderAccent = 'border-t-emerald-500';
              else if (isCancelled) borderAccent = 'border-t-rose-700';

              return (
                <div 
                  key={order.id} 
                  className={`bg-white border-b border-r border-slate-200 border-t-2 ${borderAccent} hover:bg-slate-50/40 transition flex flex-col justify-between overflow-hidden`}
                >
                  {/* Card Header */}
                  <div className="p-4 border-b border-slate-100 flex items-start justify-between gap-2">
                    <div>
                      <span className="font-mono font-black text-xs text-slate-900 tracking-tight">
                        {order.order_code || order.invoice_number}
                      </span>
                      <div className="text-[10px] text-slate-400 mt-0.5">
                        {new Date(order.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </div>
                    </div>

                    <div>
                      {isHold && (
                        <span className="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 border border-rose-200 animate-pulse">
                          HOLD
                        </span>
                      )}
                      {isReady && (
                        <span className="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 border border-amber-200">
                          SIAP KEMAS
                        </span>
                      )}
                      {isPacking && (
                        <span className="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200">
                          DIPACKING
                        </span>
                      )}
                      {isSent && (
                        <span className="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                          TERKIRIM
                        </span>
                      )}
                      {isCancelled && (
                        <span className="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                          DIBATALKAN
                        </span>
                      )}
                    </div>
                  </div>

                  {/* Body Content */}
                  <div className="p-4 space-y-3 flex-1">
                    <div className="grid grid-cols-2 gap-2 text-xs">
                      <div>
                        <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Penerima</div>
                        <div className="font-bold text-slate-800 truncate mt-0.5">{order.customer_name || 'Pembeli'}</div>
                      </div>
                      <div className="text-right">
                        <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Dropshipper</div>
                        <div className="font-bold text-green-700 truncate mt-0.5">{order.dropshipper?.name || 'Mitra'}</div>
                      </div>
                    </div>

                    {/* Items */}
                    <div className="space-y-1.5">
                      {(order.items || []).map((item, idx) => (
                        <div key={idx} className="p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs flex items-center justify-between gap-2">
                          <span className="font-semibold text-slate-700 truncate" title={item.product_name}>
                            {item.product_name}
                          </span>
                          <span className="px-2 py-0.5 rounded-lg bg-green-100 text-green-800 text-[10px] font-black shrink-0">
                            {item.quantity} pcs
                          </span>
                        </div>
                      ))}
                    </div>

                    {/* Resi Box */}
                    <div className="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-2">
                      <div className="min-w-0">
                        <div className="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                          {order.courier_name || 'Kurir Ekspedisi'}
                        </div>
                        <div className="font-mono text-xs font-bold text-slate-800 truncate mt-0.5">
                          {order.shipping_receipt_number || 'Belum ada resi'}
                        </div>
                      </div>

                      <div className="flex items-center gap-1 shrink-0">
                        {order.shipping_receipt_number && (
                          <button
                            onClick={() => copyToClipboard(order.shipping_receipt_number, order.id)}
                            title="Salin Nomor Resi"
                            className="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-green-600 hover:border-green-300 transition cursor-pointer"
                          >
                            {copiedResi === order.id ? (
                              <Check className="w-3.5 h-3.5 text-green-600" />
                            ) : (
                              <Copy className="w-3.5 h-3.5" />
                            )}
                          </button>
                        )}

                        {order.shipping_label_file && (
                          <a
                            href={`/storage/${order.shipping_label_file}`}
                            target="_blank"
                            rel="noreferrer"
                            title="Buka Label Pengiriman"
                            className="px-2 py-1 rounded-lg bg-green-600 hover:bg-green-500 text-white text-[10px] font-bold inline-flex items-center gap-1 transition"
                          >
                            <span>Label</span>
                            <ExternalLink className="w-3 h-3" />
                          </a>
                        )}
                      </div>
                    </div>

                    {/* Info Jika Dibatalkan */}
                    {isCancelled && (
                      <div className="p-3 rounded-xl bg-rose-50 border border-rose-200 space-y-1.5">
                        <div className="flex items-center justify-between text-[10px] font-bold">
                          <span className="text-rose-800 flex items-center gap-1">
                            <Ban className="w-3 h-3 text-rose-600" />
                            Dibatalkan {order.cancel_stage ? `saat [${order.cancel_stage}]` : ''}
                          </span>
                          <span className="text-rose-600 font-semibold uppercase">
                            oleh {order.cancelled_by || 'Admin'}
                          </span>
                        </div>
                        {order.cancel_reason && (
                          <p className="text-[11px] text-rose-700 italic bg-white/70 p-2 rounded-lg border border-rose-100">
                            "{order.cancel_reason}"
                          </p>
                        )}
                      </div>
                    )}
                  </div>

                  {/* Action Footer */}
                  <div className="p-4 bg-slate-50/50 border-t border-slate-100">
                    {/* Status: Menunggu Approval */}
                    {isHold && (
                      <div className="space-y-2">
                        {!isTimPacking ? (
                          <>
                            <div className="flex items-center gap-2">
                              <button
                                onClick={() => handleApprove(order.id)}
                                disabled={loadingId === order.id}
                                className="flex-1 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 active:scale-95 text-white font-bold text-xs transition text-center  cursor-pointer disabled:opacity-50"
                              >
                                <span>{loadingId === order.id ? 'Memproses...' : 'Approve & Teruskan'}</span>
                              </button>

                              <button
                                onClick={() => openCancelModal(order)}
                                title="Batalkan pesanan ini"
                                className="px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer flex items-center gap-1"
                              >
                                <Ban className="w-3.5 h-3.5 text-rose-600" />
                                <span>Batal</span>
                              </button>
                            </div>
                          </>
                        ) : (
                          <div className="w-full text-center py-2 text-xs text-slate-400 italic">
                            Menunggu Approval Admin
                          </div>
                        )}
                      </div>
                    )}

                    {/* Status: Menunggu Packing (Siap Kemas) */}
                    {isReady && (
                      <div className="flex items-center gap-2">
                        <button
                          onClick={() => handleUpdateStatus(order.id, 'dipacking')}
                          disabled={loadingId === order.id}
                          className="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-xs transition text-center  cursor-pointer disabled:opacity-50"
                        >
                          <span>{loadingId === order.id ? 'Mengubah...' : 'Mulai Packing'}</span>
                        </button>

                        {!isTimPacking && (
                          <button
                            onClick={() => openCancelModal(order)}
                            title="Batalkan pesanan tahap kemas"
                            className="px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer flex items-center gap-1"
                          >
                            <Ban className="w-3.5 h-3.5 text-rose-600" />
                            <span>Batal</span>
                          </button>
                        )}
                      </div>
                    )}

                    {/* Status: Sedang Dipacking */}
                    {isPacking && (
                      <div className="flex items-center gap-2">
                        <button
                          onClick={() => handleUpdateStatus(order.id, 'dikirim')}
                          disabled={loadingId === order.id}
                          className="flex-1 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 active:scale-95 text-white font-bold text-xs transition text-center  cursor-pointer disabled:opacity-50"
                        >
                          <span>{loadingId === order.id ? 'Menyimpan...' : 'Tandai Dikirim'}</span>
                        </button>

                        {!isTimPacking && (
                          <button
                            onClick={() => openCancelModal(order)}
                            title="Batalkan pesanan saat sedang dipacking"
                            className="px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer flex items-center gap-1"
                          >
                            <Ban className="w-3.5 h-3.5 text-rose-600" />
                            <span>Batal</span>
                          </button>
                        )}
                      </div>
                    )}

                    {/* Status: Terkirim / Selesai */}
                    {isSent && (
                      <div className="flex items-center justify-between gap-2">
                        <div className="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                          <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                          <span>Telah Diserahkan ke Kurir</span>
                        </div>

                        {!isTimPacking && (
                          <button
                            onClick={() => openCancelModal(order)}
                            className="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200 transition cursor-pointer flex items-center gap-1"
                            title="Batalkan pesanan ini (misal retur / pembeli cancel di kurir)"
                          >
                            <Ban className="w-3 h-3 text-rose-600" />
                            <span>Batalkan</span>
                          </button>
                        )}
                      </div>
                    )}

                    {/* Status: Dibatalkan */}
                    {isCancelled && (
                      <div className="w-full py-2 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs text-center flex items-center justify-center gap-1.5">
                        <Ban className="w-3.5 h-3.5 text-slate-400" />
                        <span>Pesanan Telah Dibatalkan</span>
                      </div>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>

      {/* Modal Dialog Pembatalan Pesanan */}
      {cancelModalOpen && cancelTargetOrder && (
        <div className="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-in fade-in zoom-in-95">
            {/* Header Modal */}
            <div className="p-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                  <Ban className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="font-black text-sm text-slate-900">
                    Batalkan Pesanan #{cancelTargetOrder.order_code || cancelTargetOrder.invoice_number}
                  </h3>
                  <p className="text-[11px] text-slate-500 mt-0.5">
                    Penerima: <span className="font-bold text-slate-700">{cancelTargetOrder.customer_name}</span> • Mitra: <span className="font-bold text-green-700">{cancelTargetOrder.dropshipper?.name || 'Dropshipper'}</span>
                  </p>
                </div>
              </div>
              <button
                onClick={closeCancelModal}
                disabled={isSubmittingCancel}
                className="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            {/* Content Modal */}
            <div className="p-5 space-y-4">
              {/* Status Notice Box */}
              <div className="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-3">
                <AlertTriangle className="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                <div className="text-xs text-amber-900 space-y-1">
                  <div className="font-bold">
                    Tahap Saat Ini: <span className="uppercase underline">{cancelTargetOrder.status.replace('_', ' ')}</span>
                  </div>
                  {['menunggu_approval', 'menunggu_packing', 'dipacking'].includes(cancelTargetOrder.status) ? (
                    <p className="text-[11px] text-amber-800">
                      ✅ <strong>Stok produk akan otomatis dikembalikan</strong> ke stok inventori toko setelah pesanan dibatalkan.
                    </p>
                  ) : (
                    <p className="text-[11px] text-amber-800">
                      ⚠️ Pesanan sudah berstatus terkirim di kurir. Stok fisik tidak dikembalikan otomatis sampai paket retur tiba di gudang.
                    </p>
                  )}
                </div>
              </div>

              {/* Quick Preset Buttons */}
              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1.5">
                  Pilih Alasan Cepat:
                </label>
                <div className="flex flex-wrap gap-1.5">
                  {cancelReasonPresets.map((preset, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => setCancelReason(preset)}
                      className={`text-[11px] px-2.5 py-1.5 rounded-xl border transition text-left cursor-pointer ${
                        cancelReason === preset
                          ? 'bg-rose-600 text-white border-rose-600 font-bold '
                          : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                      }`}
                    >
                      {preset}
                    </button>
                  ))}
                </div>
              </div>

              {/* Textarea Reason */}
              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1.5">
                  Alasan Pembatalan (Detail): <span className="text-rose-500">*</span>
                </label>
                <textarea
                  rows={3}
                  value={cancelReason}
                  onChange={(e) => setCancelReason(e.target.value)}
                  placeholder="Tuliskan alasan pembatalan pesanan..."
                  className="w-full text-xs rounded-2xl border-slate-300 focus:border-rose-500 focus:ring-rose-500 placeholder-slate-400 p-3"
                ></textarea>
                <p className="text-[10px] text-slate-400 mt-1">
                  Alasan ini akan tersimpan di riwayat pesanan dan dapat dilihat oleh tim serta mitra dropshipper.
                </p>
              </div>
            </div>

            {/* Footer Modal */}
            <div className="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
              <button
                type="button"
                onClick={closeCancelModal}
                disabled={isSubmittingCancel}
                className="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition cursor-pointer"
              >
                Batal
              </button>
              <button
                type="button"
                onClick={submitCancelOrder}
                disabled={isSubmittingCancel || !cancelReason.trim() || cancelReason.trim().length < 5}
                className="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs transition  flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
              >
                <Ban className="w-3.5 h-3.5" />
                <span>{isSubmittingCancel ? 'Membatalkan...' : 'Ya, Batalkan Pesanan'}</span>
              </button>
            </div>
          </div>
        </div>
      )}
    </AuthenticatedLayout>
  );
}
