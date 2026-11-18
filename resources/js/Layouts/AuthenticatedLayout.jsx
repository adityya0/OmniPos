import React, { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import {
  LayoutDashboard,
  ShoppingCart,
  Package,
  Boxes,
  Users,
  Wallet,
  UserCheck,
  Printer,
  FileText,
  LogOut,
  ChevronLeft,
  Menu,
  X,
  CheckCircle2,
  AlertCircle,
  Store,
  Settings,
  Search,
  Bell,
  HelpCircle,
  Mail,
  TrendingUp
} from 'lucide-react';

export default function AuthenticatedLayout({ header, children, flush = true }) {
  const { auth, flash } = usePage().props;
  const user = auth?.user || {};
  const [collapsed, setCollapsed] = useState(true);
  const [sidebarTip, setSidebarTip] = useState({ text: '', top: 0, left: 0 });
  const [mobileOpen, setMobileOpen] = useState(false);

  const showSidebarTip = (e, label) => {
    if (!collapsed) return;
    const rect = e.currentTarget.getBoundingClientRect();
    setSidebarTip({
      text: label,
      top: rect.top + rect.height / 2,
      left: rect.right + 10,
    });
  };

  const hideSidebarTip = () => {
    setSidebarTip((prev) => (prev.text ? { ...prev, text: '' } : prev));
  };
  const [searchQuery, setSearchQuery] = useState('');
  const [desktopSearchOpen, setDesktopSearchOpen] = useState(false);
  const [showNotif, setShowNotif] = useState(false);
  const [mobileSearchOpen, setMobileSearchOpen] = useState(false);

  const isTimPacking = user.role === 'tim_packing';
  const isDropshipper = user.role === 'dropshipper';
  const isAdmin = user.role === 'admin_supplier' || user.role === 'super_admin' || (!isTimPacking && !isDropshipper);

  const currentPath = window.location.pathname;

  const handleLogout = (e) => {
    e.preventDefault();
    router.post('/logout');
  };

  const handleSearchSubmit = (e) => {
    if (e.key === 'Enter' && searchQuery.trim()) {
      if (isDropshipper) {
        router.get('/dropshipper/catalog', { search: searchQuery.trim() });
      } else {
        router.get('/admin/products', { search: searchQuery.trim() });
      }
    }
  };

  useEffect(() => {
    const handleKeyDown = (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setDesktopSearchOpen(true);
        setTimeout(() => document.getElementById('omnipos-global-search')?.focus(), 20);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  const firstInitial = user?.name ? user.name.trim().charAt(0).toUpperCase() : 'N';

  return (
    <div className="flex h-screen bg-[#F1F5F9] overflow-hidden font-sans text-slate-800 p-0 gap-0">
      {/* Mobile Floating Rounded Navbar (Border Radius 20px, Floating Elevation) */}
      <div className="lg:hidden fixed top-3 inset-x-3 z-50 bg-white/95 backdrop-blur-md border border-slate-200  rounded-[22px] px-4 py-2.5 flex items-center justify-between">
        <Link href="/dashboard" className="flex items-center gap-2.5">
          <img src="/images/logo-omnipos.png" alt="OmniPOS" className="w-7 h-7 object-contain" />
          <span className="font-bold text-sm tracking-tight text-slate-900">
            Omni<span className="text-emerald-500">POS</span>
          </span>
        </Link>

        <div className="flex items-center gap-1.5">
          {/* Quick Search Toggle */}
          <button
            onClick={() => setMobileSearchOpen(!mobileSearchOpen)}
            className="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200/60 flex items-center justify-center text-slate-500 hover:text-slate-800 transition"
            title="Cari"
          >
            <Search className="w-4 h-4 stroke-[1.8]" />
          </button>

          {/* Notifikasi */}
          <button
            onClick={() => setShowNotif(!showNotif)}
            className="relative w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200/60 flex items-center justify-center text-slate-500 hover:text-slate-800 transition"
            title="Notifikasi"
          >
            <Bell className="w-4 h-4 stroke-[1.8]" />
            <span className="absolute top-1 right-1 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
          </button>

          {/* Avatar Profile */}
          <a
            href="/profile"
            className="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center border border-emerald-200/80 mx-0.5"
          >
            {firstInitial}
          </a>

          {/* Hamburger Menu Toggle */}
          <button
            onClick={() => setMobileOpen(!mobileOpen)}
            className="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200/60 flex items-center justify-center text-slate-600 hover:text-slate-900 transition focus:outline-none"
            title="Buka Navigasi"
          >
            {mobileOpen ? <X className="w-4 h-4 stroke-[2]" /> : <Menu className="w-4 h-4 stroke-[2]" />}
          </button>
        </div>
      </div>

      {/* Mobile Search Bar Dropdown */}
      {mobileSearchOpen && (
        <div className="lg:hidden fixed top-16 inset-x-3 z-50 bg-white rounded-2xl border border-slate-200 shadow-[0_8px_30px_rgba(0,0,0,0.08)] p-3">
          <div className="relative flex items-center">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 stroke-[1.8]" />
            <input
              type="text"
              autoFocus
              placeholder={isDropshipper ? "Cari katalog produk, SKU..." : "Cari produk kasir, antrean, SKU..."}
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              onKeyDown={handleSearchSubmit}
              className="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700"
            />
          </div>
        </div>
      )}

      {/* Mobile Backdrop */}
      {mobileOpen && (
        <div
          onClick={() => setMobileOpen(false)}
          className="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden transition-opacity"
        />
      )}

      {/* Floating Hover Bubble Menu Sidebar (Putih Outline Abu-Hijau, Paling Atas z-[9999]) */}
      {collapsed && sidebarTip.text && (
        <div
          style={{
            top: `${sidebarTip.top}px`,
            left: `${sidebarTip.left}px`,
            transform: 'translateY(-50%)',
          }}
          className="hidden lg:block fixed z-[9999] pointer-events-none px-3 py-1.5 rounded-xl bg-white border border-emerald-300/90 ring-1 ring-slate-200/70 text-xs font-semibold text-slate-700 whitespace-nowrap"
        >
          {sidebarTip.text}
        </div>
      )}

      {/* Sidebar: Detached, Pure White Card with Soft Elevation & Rounded Corners */}
      <aside
        style={{ viewTransitionName: 'app-sidebar' }}
        className={`
          fixed inset-y-0 left-0 z-50 lg:static flex flex-col justify-between bg-white text-slate-700
          border-r border-slate-200 overflow-hidden transition-all duration-300 ease-in-out
          ${mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
          ${collapsed ? 'lg:w-[76px]' : 'lg:w-[250px] w-64'}
        `}
      >
        {/* Top Branding & Menu */}
        <div className="flex flex-col flex-1 overflow-y-auto no-scrollbar">
          {/* Brand Logo (saat terbuka) / Tombol Buka Sidebar >> (saat tertutup) */}
          <div className={`h-16 border-b border-slate-200 flex items-center shrink-0 ${collapsed ? 'justify-center px-2' : 'justify-between px-5'}`}>
            {!collapsed ? (
              <>
                <Link href="/dashboard" className="flex items-center gap-3 overflow-hidden group">
                  <img src="/images/logo-omnipos.png" alt="OmniPOS" className="w-8 h-8 object-contain shrink-0 group-hover:scale-105 transition-transform" />
                  <span className="font-bold text-base tracking-tight text-slate-900 whitespace-nowrap">
                    Omni<span className="text-emerald-500">POS</span>
                  </span>
                </Link>
                <button
                  onClick={() => setCollapsed(true)}
                  title="Tutup Sidebar"
                  className="hidden lg:flex p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                >
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                  </svg>
                </button>
              </>
            ) : (
              <button
                onClick={() => { setCollapsed(false); hideSidebarTip(); }}
                onMouseEnter={(e) => showSidebarTip(e, 'Buka Sidebar')}
                onMouseLeave={hideSidebarTip}
                className="p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50/50 transition cursor-pointer"
              >
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
              </button>
            )}
          </div>

          {/* Nav Links */}
          <nav className="p-3 space-y-1">
            {/* Header Kategori: MENU UTAMA */}
            {!collapsed && (
              <div className="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-2 pb-1.5">
                Menu Utama
              </div>
            )}

            {/* Dashboard */}
            <Link
              href="/dashboard"
              onMouseEnter={(e) => showSidebarTip(e, 'Dashboard')}
              onMouseLeave={hideSidebarTip}
              className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                collapsed ? 'justify-center px-0' : 'px-3.5'
              } ${
                currentPath === '/dashboard'
                  ? 'bg-emerald-600 text-white font-medium '
                  : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
              }`}
            >
              <LayoutDashboard className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath === '/dashboard' ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
              {!collapsed && <span>Dashboard</span>}
            </Link>

            {/* Tim Packing View */}
            {isTimPacking && (
              <Link
                href="/admin/packing"
                onMouseEnter={(e) => showSidebarTip(e, 'Antrean Packing')}
                onMouseLeave={hideSidebarTip}
                className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                  collapsed ? 'justify-center px-0' : 'px-3.5'
                } ${
                  currentPath.startsWith('/admin/packing')
                    ? 'bg-emerald-600 text-white font-medium '
                    : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                }`}
              >
                <Boxes className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/packing') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                {!collapsed && <span>Antrean Packing</span>}
              </Link>
            )}

            {/* Admin Toko Menu */}
            {isAdmin && (
              <>
                {!collapsed && (
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                    Kasir & Gudang
                  </div>
                )}

                <a
                  href="/admin/pos"
                  onMouseEnter={(e) => showSidebarTip(e, 'POS Kasir Toko')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/pos')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <ShoppingCart className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/pos') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>POS Kasir Toko</span>}
                </a>

                <a
                  href="/admin/products"
                  onMouseEnter={(e) => showSidebarTip(e, 'Produk & Stok')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/products')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Package className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/products') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Produk & Stok</span>}
                </a>

                <a
                  href="/admin/stock-analytics"
                  onMouseEnter={(e) => showSidebarTip(e, 'Analisis Stok')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/stock-analytics')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <TrendingUp className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/stock-analytics') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Analisis Stok</span>}
                </a>

                <a
                  href="/admin/reports"
                  onMouseEnter={(e) => showSidebarTip(e, 'Laporan Penjualan')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/reports')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <FileText className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/reports') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Laporan Penjualan</span>}
                </a>

                {!collapsed && (
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                    Kemitraan & Dropship
                  </div>
                )}

                <Link
                  href="/admin/packing"
                  onMouseEnter={(e) => showSidebarTip(e, 'Antrean Packing')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/packing')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Boxes className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/packing') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Antrean Packing</span>}
                </Link>

                <a
                  href="/admin/dropshippers"
                  onMouseEnter={(e) => showSidebarTip(e, 'Mitra Dropshipper')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/dropshippers')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Users className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/dropshippers') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Mitra Dropshipper</span>}
                </a>

                <a
                  href="/admin/dropship-finances"
                  onMouseEnter={(e) => showSidebarTip(e, 'Keuangan Dropship')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/dropship-finances')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Wallet className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/dropship-finances') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Keuangan Dropship</span>}
                </a>

                <a
                  href="/admin/staff"
                  onMouseEnter={(e) => showSidebarTip(e, 'Kelola Staf Toko')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/staff')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <UserCheck className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/staff') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Kelola Staf Toko</span>}
                </a>

                {!collapsed && (
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                    Perangkat
                  </div>
                )}

                <a
                  href="/admin/printer"
                  onMouseEnter={(e) => showSidebarTip(e, 'Pengaturan Struk')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/admin/printer')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Printer className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/admin/printer') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Pengaturan Struk</span>}
                </a>
              </>
            )}

            {/* Dropshipper Menu */}
            {isDropshipper && (
              <>
                {!collapsed && (
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                    Kemitraan Dropship
                  </div>
                )}

                <a
                  href="/dropshipper/suppliers"
                  onMouseEnter={(e) => showSidebarTip(e, 'Pilih Penyuplai')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/dropshipper/suppliers')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Store className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/dropshipper/suppliers') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Pilih Penyuplai</span>}
                </a>

                <a
                  href="/dropshipper/catalog"
                  onMouseEnter={(e) => showSidebarTip(e, 'Katalog Produk')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/dropshipper/catalog')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Package className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/dropshipper/catalog') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Katalog Produk</span>}
                </a>

                <a
                  href="/dropshipper/orders"
                  onMouseEnter={(e) => showSidebarTip(e, 'Pesanan Saya')}
                  onMouseLeave={hideSidebarTip}
                  className={`flex items-center gap-3 py-2.5 rounded-2xl text-xs transition duration-150 group ${
                    collapsed ? 'justify-center px-0' : 'px-3.5'
                  } ${
                    currentPath.startsWith('/dropshipper/orders')
                      ? 'bg-emerald-600 text-white font-medium '
                      : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal'
                  }`}
                >
                  <Boxes className={`w-4 h-4 shrink-0 stroke-[1.8] ${currentPath.startsWith('/dropshipper/orders') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700'}`} />
                  {!collapsed && <span>Pesanan Saya</span>}
                </a>
              </>
            )}
          </nav>
        </div>

        {/* Pengaturan di bagian paling bawah sidebar (tanpa info akun login) */}
        <div className="p-3 border-t border-slate-100 bg-white">
          <a
            href={isAdmin ? '/admin/settings' : isDropshipper ? '/dropshipper/profile' : '/profile'}
            onMouseEnter={(e) => showSidebarTip(e, 'Pengaturan')}
            onMouseLeave={hideSidebarTip}
            className={`flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs transition duration-150 group ${
              currentPath.startsWith('/admin/settings') || currentPath.startsWith('/profile') || currentPath.startsWith('/dropshipper/profile')
                ? 'bg-emerald-600 text-white font-medium'
                : 'text-slate-600 hover:text-emerald-800 hover:bg-emerald-50/40 font-medium'
            } ${collapsed ? 'justify-center px-0' : ''}`}
          >
            <Settings
              className={`w-4 h-4 shrink-0 stroke-[1.8] ${
                currentPath.startsWith('/admin/settings') || currentPath.startsWith('/profile') || currentPath.startsWith('/dropshipper/profile')
                  ? 'text-white'
                  : 'text-slate-400 group-hover:text-emerald-700'
              }`}
            />
            {!collapsed && <span>Pengaturan</span>}
          </a>
        </div>
      </aside>

      {/* Main Content Area: Pure Clean White Cards floating on #F4F6F8 */}
      <div className="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        {/* Top Seamless Navbar (Desktop) */}
        <div className="hidden lg:block shrink-0">
          <div className="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between gap-4">
            {/* Left: Clean Page Title */}
            <div className="flex items-center gap-3 min-w-0">
              <h2 className="font-bold text-base sm:text-lg text-slate-900 truncate">
                {currentPath === '/dashboard'
                  ? 'Dashboard'
                  : currentPath.startsWith('/admin/packing')
                  ? 'Antrean Packing'
                  : 'OmniPOS'}
              </h2>
            </div>

            {/* Right: Expandable Search Icon + Round Icon Buttons (?, ✉, 🔔, Profile Avatar) */}
            <div className="flex items-center gap-2 shrink-0">
              <div className="flex items-center gap-1.5">
                {desktopSearchOpen && (
                  <input
                    id="omnipos-global-search"
                    type="text"
                    autoFocus
                    placeholder={isDropshipper ? "Cari katalog, SKU..." : "Cari produk, SKU..."}
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    onKeyDown={(e) => {
                      if (e.key === 'Escape') setDesktopSearchOpen(false);
                      handleSearchSubmit(e);
                    }}
                    onBlur={() => { if (!searchQuery) setDesktopSearchOpen(false); }}
                    className="w-48 xl:w-56 px-3.5 py-1.5 text-xs bg-slate-50/80 focus:bg-white text-slate-700 placeholder-slate-400 rounded-full border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-normal"
                  />
                )}
                <button
                  type="button"
                  onClick={() => setDesktopSearchOpen(!desktopSearchOpen)}
                  title="Cari (Ctrl+K)"
                  className={`w-9 h-9 rounded-full border flex items-center justify-center transition cursor-pointer ${
                    desktopSearchOpen
                      ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
                      : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-500 hover:text-slate-800'
                  }`}
                >
                  <Search className="w-4 h-4 stroke-[1.8]" />
                </button>
              </div>
              <a
                href="https://wa.me/6281234567890"
                target="_blank"
                rel="noopener noreferrer"
                title="Bantuan & Dukungan"
                className="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition "
              >
                <HelpCircle className="w-4 h-4 stroke-[1.8]" />
              </a>

              <a
                href={isAdmin ? "/admin/dropshippers" : "/dropshipper/profile"}
                title="Pesan & Kemitraan"
                className="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition "
              >
                <Mail className="w-4 h-4 stroke-[1.8]" />
              </a>

              <div className="relative">
                <button
                  onClick={() => setShowNotif(!showNotif)}
                  title="Notifikasi"
                  className="relative w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition "
                >
                  <Bell className="w-4 h-4 stroke-[1.8]" />
                  <span className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                </button>

                {showNotif && (
                  <div className="absolute right-0 mt-2 w-80 bg-white rounded-2xl border border-slate-200 shadow-[0_8px_30px_rgba(0,0,0,0.08)] p-3 z-50">
                    <div className="flex items-center justify-between pb-2 border-b border-slate-100 px-1">
                      <span className="font-semibold text-xs text-slate-800">Notifikasi</span>
                      <span className="text-[10px] text-emerald-600 font-medium">Sistem Live</span>
                    </div>
                    <div className="py-2 text-xs text-slate-500 space-y-2">
                      <div className="p-2 rounded-xl bg-slate-50 hover:bg-slate-100/80 transition flex items-start gap-2">
                        <span className="w-2 h-2 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
                        <div>
                          <p className="text-slate-700 font-medium text-[11px]">Sistem POS & Stok Sinkron</p>
                          <p className="text-[10px] text-slate-400 mt-0.5">Semua data transaksi terhubung otomatis.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                )}
              </div>

              <div className="flex items-center pl-1 border-l border-slate-200/60 ml-1">
                <a
                  href="/profile"
                  title={user?.name || 'Profil Akun'}
                  className="w-9 h-9 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-center border border-emerald-200/80 transition "
                >
                  {firstInitial}
                </a>
              </div>
            </div>
          </div>
        </div>

        {/* Scrollable Page Header + Body */}
        <main
          style={{ viewTransitionName: 'app-main' }}
          className={flush ? 'flex-1 overflow-y-auto pt-16 lg:pt-0 bg-white' : 'flex-1 overflow-y-auto px-4 sm:px-6 lg:px-6 pt-20 lg:pt-5 pb-8 space-y-6'}
        >
          {header && (
            <header
              style={{ viewTransitionName: 'app-header' }}
              className={flush ? 'px-5 sm:px-6 py-3.5 border-b border-slate-200 bg-white' : ''}
            >
              {header}
            </header>
          )}
          {/* Flash Messages */}
          {flash?.success && (
            <div className="p-4 rounded-[18px] bg-white border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5 ">
              <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
              <span>{flash.success}</span>
            </div>
          )}
          {flash?.error && (
            <div className="p-4 rounded-[18px] bg-white border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5 ">
              <AlertCircle className="w-4 h-4 text-rose-600 shrink-0" />
              <span>{flash.error}</span>
            </div>
          )}

          <div key={usePage().url} className="page-transition">
            {children}
          </div>
        </main>
      </div>
    </div>
  );
}
