import React, { useState, useMemo } from 'react';
import { createRoot } from 'react-dom/client';
import { 
  Package, 
  Clock, 
  CheckCircle2, 
  Truck, 
  Search, 
  Copy, 
  Check, 
  ExternalLink, 
  User, 
  Store, 
  AlertCircle,
  XCircle,
  PlayCircle,
  Send,
  Boxes,
  Users
} from 'lucide-react';

function PackingApp({ initialData }) {
  const { 
    orders: rawOrders = [], 
    counts = {}, 
    statusFilter: initialFilter = 'menunggu_packing',
    isTimPacking = false,
    csrfToken = '',
    routes = {},
    user = {}
  } = initialData;

  const [activeTab, setActiveTab] = useState(initialFilter);
  const [searchQuery, setSearchQuery] = useState('');
  const [copiedResi, setCopiedResi] = useState(null);
  const [submittingId, setSubmittingId] = useState(null);

  // Copy resi handler
  const copyToClipboard = (text, id) => {
    if (!text) return;
    navigator.clipboard.writeText(text);
    setCopiedResi(id);
    setTimeout(() => setCopiedResi(null), 2000);
  };

  // Client-side instant search & filter
  const filteredOrders = useMemo(() => {
    return rawOrders.filter(order => {
      // Tab filter
      if (activeTab !== 'semua' && order.status !== activeTab) {
        return false;
      }
      // Search query filter
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

  // Tab definitions
  const tabs = [
    ...(!isTimPacking ? [{
      id: 'menunggu_approval',
      label: 'Menunggu Approval',
      count: counts.menunggu_approval || 0,
      badgeColor: 'bg-rose-100 text-rose-700',
      activeColor: 'bg-rose-600 text-white shadow-rose-200'
    }] : []),
    {
      id: 'menunggu_packing',
      label: 'Siap Dikemas',
      count: counts.menunggu_packing || 0,
      badgeColor: 'bg-amber-100 text-amber-700',
      activeColor: 'bg-amber-500 text-white shadow-amber-200'
    },
    {
      id: 'dipacking',
      label: 'Sedang Dipacking',
      count: counts.dipacking || 0,
      badgeColor: 'bg-emerald-100 text-emerald-700',
      activeColor: 'bg-emerald-600 text-white shadow-emerald-200'
    },
    {
      id: 'dikirim',
      label: 'Terkirim / Selesai',
      count: counts.dikirim || 0,
      badgeColor: 'bg-slate-100 text-slate-700',
      activeColor: 'bg-slate-900 text-white shadow-slate-200'
    },
    {
      id: 'semua',
      label: 'Semua Status',
      count: rawOrders.length,
      badgeColor: 'bg-slate-100 text-slate-700',
      activeColor: 'bg-slate-800 text-white shadow-slate-200'
    }
  ];

  return (
    <div className="space-y-6">
      {/* Top Header & Actions */}
      <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div className="flex items-center gap-2">
            <span className="px-2.5 py-1 rounded-lg bg-green-50 border border-green-200 text-green-700 text-[11px] font-black uppercase tracking-wider flex items-center gap-1.5">
              <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
              React Powered UI
            </span>
            <span className="text-xs text-slate-400 font-medium">Realtime Operasional Toko</span>
          </div>
          <h1 className="text-2xl font-black text-slate-900 mt-1">
            {isTimPacking ? 'Workspace Tim Packing' : 'Antrean Packing & Approval'}
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            {isTimPacking 
              ? 'Pantau pesanan yang sudah disetujui, kemas barang, dan update resi secara cepat.' 
              : 'Verifikasi approval dropshipper (Hold) dan pantau alur packing staf gudang.'}
          </p>
        </div>

        <div className="flex items-center gap-2">
          {isTimPacking ? (
            <div className="flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200/80 rounded-2xl text-xs font-bold text-amber-800">
              <Boxes className="w-4 h-4 text-amber-600" />
              <span>Aktor Packing: <strong>{user?.name || 'Staf'}</strong></span>
            </div>
          ) : (
            <a 
              href={routes.staff}
              className="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs"
            >
              <Users className="w-4 h-4 text-slate-300" />
              <span>Kelola Tim Packing</span>
            </a>
          )}
        </div>
      </div>

      {/* Top Stats Cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {!isTimPacking && (
          <div className="bg-white rounded-2xl p-4 border border-rose-100 shadow-xs flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
              <Clock className="w-5 h-5" />
            </div>
            <div>
              <div className="text-2xl font-black text-rose-600 leading-none">{counts.menunggu_approval || 0}</div>
              <div className="text-[10px] font-bold text-rose-700 uppercase tracking-wide mt-1">Menunggu Approval</div>
            </div>
          </div>
        )}

        <div className="bg-white rounded-2xl p-4 border border-amber-100 shadow-xs flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <Package className="w-5 h-5" />
          </div>
          <div>
            <div className="text-2xl font-black text-amber-600 leading-none">{counts.menunggu_packing || 0}</div>
            <div className="text-[10px] font-bold text-amber-700 uppercase tracking-wide mt-1">Siap Dikemas</div>
          </div>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-emerald-100 shadow-xs flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <Boxes className="w-5 h-5" />
          </div>
          <div>
            <div className="text-2xl font-black text-emerald-600 leading-none">{counts.dipacking || 0}</div>
            <div className="text-[10px] font-bold text-emerald-700 uppercase tracking-wide mt-1">Sedang Dipacking</div>
          </div>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center shrink-0">
            <Truck className="w-5 h-5" />
          </div>
          <div>
            <div className="text-2xl font-black text-slate-800 leading-none">{counts.dikirim || 0}</div>
            <div className="text-[10px] font-bold text-slate-500 uppercase tracking-wide mt-1">Terkirim / Selesai</div>
          </div>
        </div>
      </div>

      {/* Control Bar: Tabs & Instant Search */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-3">
        {/* Tabs */}
        <div className="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar">
          {tabs.map(tab => {
            const isActive = activeTab === tab.id;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer ${
                  isActive 
                    ? `${tab.activeColor} shadow-sm` 
                    : 'bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-50'
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

        {/* Live Search Input */}
        <div className="relative min-w-[260px]">
          <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Cari order, dropshipper, resi..."
            className="w-full pl-9 pr-4 py-2 bg-white rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-xs"
          />
          {searchQuery && (
            <button 
              onClick={() => setSearchQuery('')}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
            >
              ✕
            </button>
          )}
        </div>
      </div>

      {/* Orders Grid */}
      {filteredOrders.length === 0 ? (
        <div className="bg-white rounded-3xl border border-slate-200/80 p-12 text-center">
          <div className="w-14 h-14 mx-auto rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 mb-3">
            <Package className="w-7 h-7" />
          </div>
          <h3 className="font-bold text-slate-800 text-sm">Tidak Ada Pesanan Ditemukan</h3>
          <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
            {searchQuery 
              ? `Tidak ada hasil yang cocok dengan kata kunci "${searchQuery}".`
              : (isTimPacking 
                  ? 'Pesanan akan muncul setelah Admin Toko menyetujui (Approve) pesanan dropship.' 
                  : 'Belum ada pesanan pada kategori status ini.')}
          </p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {filteredOrders.map(order => {
            const isHold = order.status === 'menunggu_approval';
            const isReady = order.status === 'menunggu_packing';
            const isPacking = order.status === 'dipacking';
            const isSent = ['dikirim', 'selesai'].includes(order.status);

            const borderAccent = isHold ? 'border-t-rose-500' : (isReady ? 'border-t-amber-500' : (isPacking ? 'border-t-emerald-500' : 'border-t-slate-400'));

            return (
              <div 
                key={order.id} 
                className={`bg-white rounded-2xl border border-slate-200/80 border-t-4 ${borderAccent} shadow-xs hover:shadow-md transition flex flex-col justify-between overflow-hidden`}
              >
                {/* Header Card */}
                <div className="p-4 border-b border-slate-100 flex items-start justify-between gap-2">
                  <div>
                    <span className="font-mono font-black text-xs text-slate-900 tracking-tight">
                      {order.order_code || order.invoice_number}
                    </span>
                    <div className="text-[10px] text-slate-400 mt-0.5">
                      {new Date(order.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </div>
                  </div>

                  {/* Status Badge */}
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
                  </div>
                </div>

                {/* Body Content */}
                <div className="p-4 space-y-3 flex-1">
                  {/* Customer & Dropshipper */}
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

                  {/* Product Items */}
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
                          className="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-green-600 hover:border-green-300 transition"
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
                </div>

                {/* Footer Action Buttons */}
                <div className="p-4 bg-slate-50/50 border-t border-slate-100">
                  {/* HOLD / APPROVAL ACTIONS (Admin Only) */}
                  {isHold && (
                    <div className="flex items-center gap-2">
                      {!isTimPacking ? (
                        <>
                          <form 
                            method="POST" 
                            action={routes.approve.replace(':id', order.id)}
                            className="flex-1"
                            onSubmit={() => setSubmittingId(order.id)}
                          >
                            <input type="hidden" name="_token" value={csrfToken} />
                            <button
                              type="submit"
                              disabled={submittingId === order.id}
                              className="w-full py-2.5 rounded-xl bg-green-600 hover:bg-green-500 active:scale-95 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm shadow-green-500/20"
                            >
                              <CheckCircle2 className="w-4 h-4" />
                              <span>Approve & Teruskan</span>
                            </button>
                          </form>

                          <form 
                            method="POST" 
                            action={routes.reject.replace(':id', order.id)}
                            onSubmit={(e) => {
                              if (!confirm('Tolak pesanan ini dan kembalikan stok?')) e.preventDefault();
                            }}
                          >
                            <input type="hidden" name="_token" value={csrfToken} />
                            <button
                              type="submit"
                              className="px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition"
                            >
                              <XCircle className="w-4 h-4" />
                            </button>
                          </form>
                        </>
                      ) : (
                        <div className="w-full text-center py-2 text-xs text-slate-400 italic">
                          Menunggu Approval Admin
                        </div>
                      )}
                    </div>
                  )}

                  {/* READY FOR PACKING -> START PACKING */}
                  {isReady && (
                    <form 
                      method="POST" 
                      action={routes.status.replace(':id', order.id)}
                      onSubmit={() => setSubmittingId(order.id)}
                    >
                      <input type="hidden" name="_token" value={csrfToken} />
                      <input type="hidden" name="_method" value="PATCH" />
                      <input type="hidden" name="status" value="dipacking" />
                      <button
                        type="submit"
                        disabled={submittingId === order.id}
                        className="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm shadow-emerald-500/20 cursor-pointer"
                      >
                        <PlayCircle className="w-4 h-4" />
                        <span>Mulai Packing</span>
                      </button>
                    </form>
                  )}

                  {/* PACKING -> MARK AS SENT */}
                  {isPacking && (
                    <form 
                      method="POST" 
                      action={routes.status.replace(':id', order.id)}
                      onSubmit={() => setSubmittingId(order.id)}
                    >
                      <input type="hidden" name="_token" value={csrfToken} />
                      <input type="hidden" name="_method" value="PATCH" />
                      <input type="hidden" name="status" value="dikirim" />
                      <button
                        type="submit"
                        disabled={submittingId === order.id}
                        className="w-full py-2.5 rounded-xl bg-green-600 hover:bg-green-500 active:scale-95 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm shadow-green-500/20 cursor-pointer"
                      >
                        <Send className="w-4 h-4" />
                        <span>Tandai Dikirim</span>
                      </button>
                    </form>
                  )}

                  {/* SENT -> COMPLETED */}
                  {isSent && (
                    <div className="w-full py-2 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs text-center flex items-center justify-center gap-1.5">
                      <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                      <span>Pesanan Telah Dikirim</span>
                    </div>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}

// Mount React Root
const rootElement = document.getElementById('packing-react-root');
if (rootElement) {
  const props = JSON.parse(rootElement.getAttribute('data-props') || '{}');
  createRoot(rootElement).render(<PackingApp initialData={props} />);
}

