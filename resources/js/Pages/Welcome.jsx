import React, { useState, useMemo, useEffect, useRef } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { 
  Store, 
  Package, 
  Truck, 
  Wallet, 
  ArrowRight, 
  CheckCircle2, 
  ShieldCheck, 
  Sparkles,
  Layers,
  ChevronRight,
  TrendingUp,
  BarChart3,
  Phone,
  Mail,
  MapPin,
  Clock,
  Search,
  ExternalLink,
  Zap,
  Star,
  Shield,
  ArrowUpRight,
  Check,
  Play,
  RefreshCw,
  Calculator,
  DollarSign,
  Boxes,
  Printer,
  ChevronDown,
  FileSpreadsheet,
  Tag,
  ScanLine,
  Sliders,
  RotateCcw,
  ShoppingBag,
  FileText,
  Smartphone,
  Send,
  BadgeCheck,
  Users
} from 'lucide-react';

export default function Welcome({ auth }) {
  // State interaktif
  const [activeTab, setActiveTab] = useState('catalog');
  const [activeFeatureCategory, setActiveFeatureCategory] = useState('pos');
  const [activeDockFeature, setActiveDockFeature] = useState('pos');
  const [hoveredDockFeature, setHoveredDockFeature] = useState(null);
  const [openNavDropdown, setOpenNavDropdown] = useState(null);
  const [dailyOrders, setDailyOrders] = useState(15);
  const [marginPerOrder, setMarginPerOrder] = useState(25000);
  const [activeArticleIndex, setActiveArticleIndex] = useState(0);
  const [openFaq, setOpenFaq] = useState(0);
  const [flowProgress, setFlowProgress] = useState(0);
  const [showNavbar, setShowNavbar] = useState(true);
  const lastScrollYRef = useRef(0);
  const caraKerjaRef = useRef(null);

  // Scroll-Linked Vertical Flow Animation (khusus di section Cara Kerja) & Smart Sticky Navbar
  useEffect(() => {
    let ticking = false;
    const updateScrollState = () => {
      const currentY = window.scrollY;
      if (caraKerjaRef.current) {
        const rect = caraKerjaRef.current.getBoundingClientRect();
        const vh = window.innerHeight || 800;
        const totalTravel = rect.height * 0.62;
        const traveled = vh * 0.72 - rect.top;
        const raw = totalTravel > 0 ? traveled / totalTravel : 0;
        setFlowProgress(Math.max(0, Math.min(1, raw)));
      }

      // Smart sticky navbar: hilang saat scroll turun, muncul saat scroll naik
      if (currentY < 80) {
        setShowNavbar(true);
      } else if (currentY > lastScrollYRef.current + 6) {
        setShowNavbar(false);
      } else if (currentY < lastScrollYRef.current - 6) {
        setShowNavbar(true);
      }
      lastScrollYRef.current = currentY;
      ticking = false;
    };

    const onScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(updateScrollState);
        ticking = true;
      }
    };

    updateScrollState();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    return () => {
      window.removeEventListener('scroll', onScroll);
      window.removeEventListener('resize', onScroll);
    };
  }, []);

  // Scroll Reveal Animations
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('opacity-100', 'translate-y-0');
            entry.target.classList.remove('opacity-0', 'translate-y-8');
          }
        });
      },
      { threshold: 0.05, rootMargin: '0px 0px 40px 0px' }
    );

    const elements = document.querySelectorAll('.scroll-reveal');
    elements.forEach((el) => {
      observer.observe(el);
    });

    return () => observer.disconnect();
  }, []);

  // Kalkulasi estimasi keuntungan
  const monthlyProfit = useMemo(() => {
    return dailyOrders * marginPerOrder * 30;
  }, [dailyOrders, marginPerOrder]);

  const yearlyProfit = useMemo(() => {
    return monthlyProfit * 12;
  }, [monthlyProfit]);

  // Mockup Produk dengan Foto Sample Nyata
  const sampleProducts = [
    {
      id: 1,
      name: 'Squishy Glitter Mainan Anak',
      category: 'Mainan Edukasi',
      image: '/images/product-squishy-glitter.jpg',
      cost: 24000,
      retailPrice: 48000,
      margin: 24000,
      marginPercent: 50,
      stock: 195,
      trending: true
    },
    {
      id: 2,
      name: 'Lego Robot Mini Transformer 4WD',
      category: 'Mainan & Gadget',
      image: '/images/product-lego-robot.jpg',
      cost: 25000,
      retailPrice: 50000,
      margin: 25000,
      marginPercent: 50,
      stock: 98,
      trending: true
    },
    {
      id: 3,
      name: 'Slime Galaxy Glow in The Dark',
      category: 'Mainan Anak',
      image: '/images/product-slime-galaxy.jpg',
      cost: 12000,
      retailPrice: 25000,
      margin: 13000,
      marginPercent: 52,
      stock: 100,
      trending: false
    }
  ];

  // Modul Fitur Unggulan Lengkap dengan Aksen Warna Tematik
  const featureCategories = [
    {
      id: 'pos',
      label: 'Kasir POS & Struk',
      icon: Store,
      themeColor: 'emerald',
      activeClass: 'bg-emerald-600 text-white shadow-[0_4px_14px_rgba(5,150,105,0.3)]',
      dotClass: 'bg-emerald-500',
      badgeBg: 'bg-emerald-100 text-emerald-800 border-emerald-200',
      badge: 'OFFLINE STORE',
      title: 'Kasir POS Kilat dengan Cetak Struk Bluetooth & Thermal',
      desc: 'Solusi kasir toko offline super cepat dengan dukungan scan barcode, multi-harga otomatis, dan cetak struk berlogo.',
      points: [
        'Scan Barcode kilat via barcode scanner USB atau keyboard dalam 0.2 detik.',
        'Mendukung Multi-Level Harga otomatis (Eceran, Grosir Lusin, hingga Grosir Dus).',
        'Cetak Struk Bluetooth & USB Thermal (ukuran 58mm & 80mm) lengkap dengan Logo Toko.',
        'Hitung kembalian instan dan mendukung Tunai, QRIS, serta Transfer Bank.'
      ]
    },
    {
      id: 'inventory',
      label: 'Inventori & Multi-Level',
      icon: Layers,
      themeColor: 'sky',
      activeClass: 'bg-sky-600 text-white shadow-[0_4px_14px_rgba(2,132,199,0.3)]',
      dotClass: 'bg-sky-500',
      badgeBg: 'bg-sky-100 text-sky-800 border-sky-200',
      badge: 'STOK OTOMATIS',
      title: 'Manajemen Stok Fisik Realtime & Level Harga Massal',
      desc: 'Kelola stok gudang toko fisik secara akurat dan atur tingkatan harga grosir serentak tanpa selisih barang.',
      points: [
        'Sekali ubah harga level kategori, otomatis tersinkron ke ratusan produk toko.',
        'Fitur Tambah Stok Cepat (Quick Restock) dan Restock Massal puluhan produk sekaligus.',
        'Import & Export produk praktis via format Excel/CSV dengan generator SKU instan.',
        'Stok fisik toko langsung terhubung realtime ke katalog dropshipper untuk mencegah overselling.'
      ]
    },
    {
      id: 'dropship',
      label: 'Katalog Dropshipper',
      icon: Package,
      themeColor: 'rose',
      activeClass: 'bg-rose-600 text-white shadow-[0_4px_14px_rgba(225,29,72,0.3)]',
      dotClass: 'bg-rose-500',
      badgeBg: 'bg-rose-100 text-rose-800 border-rose-200',
      badge: 'TANPA MODAL',
      title: 'Katalog Produk Siap Jual Tanpa Modal Stok',
      desc: 'Mitra dropshipper bebas memilih ratusan produk dari stok gudang toko fisik terverifikasi dengan keuntungan hingga 50%.',
      points: [
        'Akses foto produk resolusi tinggi, deskripsi lengkap, serta rincian berat untuk ongkir.',
        'Margin keuntungan transparan dan saran harga jual marketplace yang menguntungkan.',
        'Cukup input data pesanan dan nomor resi marketplace untuk langsung dikemas gudang.',
        'Bahan promosi siap pakai untuk Shopee, TikTok Shop, Tokopedia, dan WhatsApp.'
      ]
    },
    {
      id: 'packing',
      label: 'Antrean Packing & Resi',
      icon: Truck,
      themeColor: 'amber',
      activeClass: 'bg-amber-600 text-white shadow-[0_4px_14px_rgba(217,119,6,0.3)]',
      dotClass: 'bg-amber-500',
      badgeBg: 'bg-amber-100 text-amber-900 border-amber-200',
      badge: 'AUTO FULFILLMENT',
      title: 'Antrean Packing Gudang & Verifikasi Resi Kilat',
      desc: 'Alur pengemasan teratur dari pesanan masuk, verifikasi resi marketplace, hingga serah terima ke kurir ekspedisi.',
      points: [
        'Sistem Verifikasi Resi Marketplace otomatis sebelum pesanan diteruskan ke meja packing.',
        'Layar Antrean Live untuk tim gudang: Siap Dikemas, Sedang Dipacking, hingga Terkirim.',
        'Pembatalan pesanan aman di tahap manapun dengan pengembalian stok otomatis ke rak toko.',
        'Garansi SLA pengemasan cepat (< 24 jam) menjaga reputasi performa toko dropshipper.'
      ]
    },
    {
      id: 'finance',
      label: 'Laba Rugi & Keuangan',
      icon: BarChart3,
      themeColor: 'purple',
      activeClass: 'bg-purple-600 text-white shadow-[0_4px_14px_rgba(147,51,234,0.3)]',
      dotClass: 'bg-purple-500',
      badgeBg: 'bg-purple-100 text-purple-900 border-purple-200',
      badge: 'FINANSIAL',
      title: 'Pembagian Uang Transparan & Dompet Saldo Digital',
      desc: 'Pemisahan otomatis antara modal HPP supplier dan komisi bersih dropshipper tanpa repot hitung manual.',
      points: [
        'Bagi hasil otomatis: omset langsung terpisah antara modal supplier dan laba dropshipper.',
        'Dompet Saldo digital dengan pencairan dana instan ke berbagai rekening bank dan e-wallet.',
        'Grafik analisis omset harian dan mingguan untuk memantau kenaikan performa bisnis.',
        'Riwayat mutasi keuangan bersih, akurat, dan transparan tanpa potongan tersembunyi.'
      ]
    }
  ];

  const currentFeature = featureCategories.find(f => f.id === activeFeatureCategory) || featureCategories[0];

  const flowStages = [
    {
      step: 'Tahap 01',
      title: 'Kirim Resi',
      subtitle: 'Dropshipper input order & nomor resi',
      icon: Send,
      threshold: 0.0,
      activeBorder: 'border-sky-300 shadow-[0_8px_24px_rgba(14,165,233,0.12)]',
      iconBgClass: 'bg-sky-100 text-sky-700',
      badgeClass: 'bg-sky-600 text-white',
      subtitleClass: 'text-sky-800 font-medium',
    },
    {
      step: 'Tahap 02',
      title: 'Kemas Produk',
      subtitle: 'Gudang cek stok & packing rapi',
      icon: Package,
      threshold: 0.30,
      activeBorder: 'border-amber-300 shadow-[0_8px_24px_rgba(245,158,11,0.12)]',
      iconBgClass: 'bg-amber-100 text-amber-700',
      badgeClass: 'bg-amber-500 text-white',
      subtitleClass: 'text-amber-800 font-medium',
    },
    {
      step: 'Tahap 03',
      title: 'Kirim Produk',
      subtitle: 'Kurir pickup & antar ke pembeli',
      icon: Truck,
      threshold: 0.60,
      activeBorder: 'border-purple-300 shadow-[0_8px_24px_rgba(147,51,234,0.12)]',
      iconBgClass: 'bg-purple-100 text-purple-700',
      badgeClass: 'bg-purple-600 text-white',
      subtitleClass: 'text-purple-800 font-medium',
    },
    {
      step: 'Tahap 04',
      title: 'Dapat Uang',
      subtitle: 'Komisi laba langsung masuk saldo',
      icon: Wallet,
      threshold: 0.85,
      activeBorder: 'border-emerald-400 shadow-[0_8px_24px_rgba(16,185,129,0.16)]',
      iconBgClass: 'bg-emerald-100 text-emerald-700',
      badgeClass: 'bg-emerald-600 text-white',
      subtitleClass: 'text-emerald-800 font-semibold',
    },
  ];

  // Tahap aktif tertinggi berdasarkan posisi scroll saat ini
  const activeStageIndex = flowStages.reduce(
    (acc, stage, idx) => (flowProgress >= stage.threshold ? idx : acc),
    0
  );

  // Interactive Feature Dock (Hover untuk lihat nama di atas, Klik untuk tampilkan detail di bawah)
  const dockFeatures = [
    {
      id: 'pos',
      label: 'POS Online & Offline',
      badge: 'POPULER',
      badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
      icon: Store,
      iconBoxClass: 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-emerald-500/30',
      title: 'Point of Sales (POS) Online & Offline',
      desc: 'Kelola transaksi kasir toko fisik offline dan pesanan dropship online dalam satu layar kasir cepat dengan cetak struk thermal berlogo.',
      points: ['Scan barcode 0.2 detik & multi-harga grosir', 'Support printer thermal Bluetooth/USB 58mm & 80mm', 'Stok toko fisik langsung sinkron ke katalog online'],
      ctaText: 'Daftar Toko & Kasir POS',
      ctaHref: '/register/supplier'
    },
    {
      id: 'catalog',
      label: 'Katalog Produk',
      badge: 'TANPA STOK',
      badgeClass: 'bg-rose-50 text-rose-700 border-rose-200',
      icon: Package,
      iconBoxClass: 'bg-gradient-to-br from-rose-500 to-pink-600 shadow-rose-500/30',
      title: 'Katalog Produk Siap Jual',
      desc: 'Akses ratusan produk fisik dari gudang supplier terverifikasi lengkap dengan foto resolusi tinggi, rincian berat, dan margin laba hingga 50%.',
      points: ['Stok fisik asli tersinkron realtime', 'Foto HD & deskripsi siap promosi di marketplace', 'Tanpa minimal order dan tanpa modal stok awal'],
      ctaText: 'Lihat Katalog Gratis',
      ctaHref: '/register/dropshipper'
    },
    {
      id: 'tracker',
      label: 'Tracker Pengiriman',
      badge: 'REALTIME',
      badgeClass: 'bg-sky-50 text-sky-700 border-sky-200',
      icon: Truck,
      iconBoxClass: 'bg-gradient-to-br from-sky-500 to-blue-600 shadow-sky-500/30',
      title: 'Tracker Pengiriman & Resi Otomatis',
      desc: 'Pantau perjalanan setiap pesanan secara langsung mulai dari verifikasi resi, proses packing bubble wrap di gudang, hingga dijemput kurir.',
      points: ['Validasi resi otomatis Shopee, TikTok, Tokopedia', 'Status antrean live dari gudang supplier', 'Stok otomatis kembali jika pesanan dibatalkan'],
      ctaText: 'Coba Tracker Pengiriman',
      ctaHref: '/register/dropshipper'
    },
    {
      id: 'research',
      label: 'Riset Produk Laris',
      badge: 'TRENDING',
      badgeClass: 'bg-amber-50 text-amber-800 border-amber-200',
      icon: Search,
      iconBoxClass: 'bg-gradient-to-br from-amber-400 to-orange-500 shadow-amber-500/30',
      title: 'Riset Produk & Analisis Margin',
      desc: 'Temukan produk paling cepat laku dengan potensi keuntungan tertinggi dan pantau tren penjualan minggu ini untuk memaksimalkan etalase Anda.',
      points: ['Filter produk terlaris & margin tertinggi', 'Rekomendasi harga jual kompetitif di marketplace', 'Pantau tren kategori yang sedang naik daun'],
      ctaText: 'Mulai Riset Produk',
      ctaHref: '/register/dropshipper'
    },
    {
      id: 'shops',
      label: 'Direktori Toko Mitra',
      badge: 'SUPPLIER RESMI',
      badgeClass: 'bg-indigo-50 text-indigo-700 border-indigo-200',
      icon: Boxes,
      iconBoxClass: 'bg-gradient-to-br from-indigo-500 to-violet-600 shadow-indigo-500/30',
      title: 'Shop Library & Direktori Toko',
      desc: 'Jelajahi daftar toko fisik dan gudang supplier resmi yang tergabung di OmniPOS lengkap dengan lokasi pengiriman dan performa packing.',
      points: ['Pilihan toko grosir & produsen tangan pertama', 'Lokasi gudang tersebar di berbagai kota strategis', 'Rating kecepatan pengiriman & kelengkapan stok'],
      ctaText: 'Jelajahi Toko Supplier',
      ctaHref: '/register/dropshipper'
    },
    {
      id: 'dropshippers',
      label: 'Jaringan Dropshipper',
      badge: 'KOMUNITAS',
      badgeClass: 'bg-cyan-50 text-cyan-800 border-cyan-200',
      icon: Users,
      iconBoxClass: 'bg-gradient-to-br from-cyan-500 to-teal-600 shadow-cyan-500/30',
      title: 'Jaringan & Performa Dropshipper',
      desc: 'Lihat keaktifan komunitas mitra dropshipper OmniPOS, pencapaian pesanan harian, serta peringkat penjualan tertinggi setiap minggunya.',
      points: ['1.200+ mitra dropshipper aktif berjualan', 'Transparansi pencapaian omset & komisi cair', 'Ekosistem saling menguntungkan antara toko & reseller'],
      ctaText: 'Gabung Mitra Dropshipper',
      ctaHref: '/register/dropshipper'
    },
    {
      id: 'wallet',
      label: 'Dompet & Bagi Hasil',
      badge: 'OTOMATIS',
      badgeClass: 'bg-purple-50 text-purple-700 border-purple-200',
      icon: Wallet,
      iconBoxClass: 'bg-gradient-to-br from-purple-500 to-fuchsia-600 shadow-purple-500/30',
      title: 'Dompet Saldo & Bagi Hasil Otomatis',
      desc: 'Setiap penjualan langsung memisahkan bagian modal toko supplier dan komisi bersih dropshipper secara akurat tanpa rekap manual.',
      points: ['Pembagian uang otomatis & transparan per order', 'Pencairan saldo instan ke BCA, Mandiri, BRI, & E-Wallet', 'Riwayat mutasi & grafik laporan laba rugi lengkap'],
      ctaText: 'Buka Akun Gratis',
      ctaHref: '/register/dropshipper'
    }
  ];

  const currentDockFeature = dockFeatures.find((f) => f.id === activeDockFeature) || dockFeatures[0];

  const faqs = [
    {
      q: 'Apakah mitra dropshipper perlu membeli atau menyetok barang terlebih dahulu?',
      a: 'Sama sekali tidak! Semua stok fisik tersimpan aman di gudang toko fisik mitra kami. Dropshipper bebas memilih produk apa saja di katalog, memasarkannya di marketplace (Shopee, TikTok, Tokopedia), dan baru membayar harga modal ketika pembeli melakukan pesanan.'
    },
    {
      q: 'Bagaimana kasir offline toko dan sistem dropship terhubung secara realtime?',
      a: 'OmniPOS menggunakan satu pusat data stok terpadu. Ketika terjadi transaksi di meja kasir offline toko fisik, stok produk langsung berkurang seketika di katalog dropshipper, sehingga tidak akan pernah terjadi salah jual atau barang kosong.'
    },
    {
      q: 'Bagaimana alur pengiriman resi otomatis dari marketplace?',
      a: 'Ketika produk Anda laku di marketplace, Anda cukup memasukkan nomor resi (Awb) dan mengunggah label pengiriman PDF di platform OmniPOS. Admin toko akan menyetujui, dan tim gudang kami akan mencetak label, mempacking paket secara rapi dengan bubble wrap, lalu menyerahkannya ke kurir terkait.'
    },
    {
      q: 'Bagaimana jika ada pembeli yang membatalkan pesanan di marketplace?',
      a: 'Admin memiliki fitur pembatalan pesanan di tahap manapun. Jika pesanan dibatalkan sebelum kurir mengambil paket, sistem akan secara otomatis mengembalikan stok barang ke inventori toko secara aman.'
    },
    {
      q: 'Apakah printer struk kasir toko mendukung kustomisasi logo dan catatan?',
      a: 'Ya, 100%! Anda dapat mengunggah logo toko dalam bentuk bulat yang tersimpan permanen di struk kasir, memilih ukuran kertas printer 58mm (mini) atau 80mm (desktop), serta mengatur ucapan terima kasih atau catatan garansi di bagian bawah struk.'
    }
  ];

  return (
    <div className="min-h-screen w-full bg-[#fbfdfc] text-slate-800 font-sans selection:bg-emerald-500 selection:text-white antialiased overflow-x-clip">
      <Head title="OmniPOS - Platform Ekosistem POS Kasir & Jaringan Dropship" />

      {/* Decorative Harmonious Multi-Tonal Background Glows (Mint, Warm Peach, Lavender, Sky) */}
      <div className="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
        {/* Soft Sage/Mint Ambient Top */}
        <div className="absolute -top-40 left-1/2 -translate-x-1/2 w-[1100px] h-[550px] bg-gradient-to-b from-emerald-100/60 via-teal-50/50 to-transparent blur-3xl rounded-full" />
        {/* Warm Amber / Peach Ambient Upper Right */}
        <div className="absolute top-48 -right-36 w-[550px] h-[550px] bg-amber-100/40 blur-[130px] rounded-full" />
        {/* Soft Lavender / Purple Ambient Middle Left */}
        <div className="absolute top-[850px] -left-36 w-[550px] h-[550px] bg-purple-100/35 blur-[140px] rounded-full" />
        {/* Soft Sky Blue Ambient Lower Right */}
        <div className="absolute top-[1600px] -right-20 w-[600px] h-[600px] bg-sky-100/40 blur-[150px] rounded-full" />
        {/* Mint Green Ambient Bottom Center */}
        <div className="absolute bottom-24 left-1/4 w-[650px] h-[650px] bg-emerald-100/45 blur-[150px] rounded-full" />
      </div>

      {/* Header Smart Sticky Navigation (Fixed top, hilang saat scroll turun, muncul saat scroll naik) */}
      <header
        onMouseLeave={() => setOpenNavDropdown(null)}
        className={`fixed top-0 left-0 right-0 z-50 transition-transform duration-300 ease-out shadow-xs ${
          showNavbar ? 'translate-y-0' : '-translate-y-full'
        }`}
      >
        {/* Main Navbar Bar */}
        <div className="bg-white/95 backdrop-blur-md border-b border-slate-200/80 relative">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex items-center justify-between h-20">
            {/* Logo Brand */}
            <Link href="/" className="flex items-center gap-3.5 group">
              <div className="relative">
                <div className="absolute -inset-1 rounded-2xl bg-emerald-500/20 opacity-0 group-hover:opacity-100 transition duration-300 blur-xs" />
                <img 
                  src="/images/logo-omnipos.png" 
                  alt="OmniPOS" 
                  className="relative h-10 w-10 object-contain rounded-xl" 
                />
              </div>
              <div className="flex flex-col">
                <span className="text-2xl font-bold tracking-tight text-slate-900">
                  Omni<span className="text-emerald-600">POS</span>
                </span>
                <span className="text-[11px] text-slate-600 -mt-1 font-medium hidden sm:block">
                  POS Kasir &amp; Fulfillment Dropship
                </span>
              </div>
            </Link>

            {/* Simple 3-Item Nav Menu with Dropdowns */}
            <nav className="hidden lg:flex items-center space-x-2 type-nav text-slate-700">
              {/* 1. Dropdown Fitur */}
              <button
                type="button"
                onMouseEnter={() => setOpenNavDropdown('fitur')}
                onClick={() => setOpenNavDropdown(openNavDropdown === 'fitur' ? null : 'fitur')}
                className={`px-4 py-2 rounded-full transition flex items-center gap-1.5 cursor-pointer ${
                  openNavDropdown === 'fitur' ? 'text-emerald-700 bg-emerald-50' : 'hover:text-emerald-700 hover:bg-slate-100/70'
                }`}
              >
                <span>Fitur</span>
                <ChevronDown className={`w-4 h-4 transition-transform duration-200 ${openNavDropdown === 'fitur' ? 'rotate-180' : ''}`} />
              </button>

              {/* 2. Dropdown Panduan & Kontak */}
              <button
                type="button"
                onMouseEnter={() => setOpenNavDropdown('panduan')}
                onClick={() => setOpenNavDropdown(openNavDropdown === 'panduan' ? null : 'panduan')}
                className={`px-4 py-2 rounded-full transition flex items-center gap-1.5 cursor-pointer ${
                  openNavDropdown === 'panduan' ? 'text-emerald-700 bg-emerald-50' : 'hover:text-emerald-700 hover:bg-slate-100/70'
                }`}
              >
                <span>Panduan &amp; Kontak</span>
                <ChevronDown className={`w-4 h-4 transition-transform duration-200 ${openNavDropdown === 'panduan' ? 'rotate-180' : ''}`} />
              </button>

              {/* 3. Simulasi */}
              <a
                href="#simulasi"
                onMouseEnter={() => setOpenNavDropdown(null)}
                className="px-4 py-2 rounded-full hover:text-emerald-700 hover:bg-slate-100/70 transition"
              >
                Simulasi
              </a>
            </nav>

            {/* Auth Buttons */}
            <div className="flex items-center gap-3">
              {auth?.user ? (
                <div className="flex items-center gap-2.5">
                  <Link
                    href="/dashboard"
                    className="px-5 py-2.5 type-btn text-white bg-emerald-600 hover:bg-emerald-500 rounded-full shadow-[0_2px_10px_rgba(5,150,105,0.25)] transition-all hover:-translate-y-0.5 active:translate-y-0"
                  >
                    Buka Dashboard
                  </Link>
                  <button
                    onClick={() => router.post('/logout')}
                    className="px-3.5 py-2 type-btn text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-full transition"
                  >
                    Keluar
                  </button>
                </div>
              ) : (
                <div className="flex items-center gap-2.5">
                  <a
                    href="/login" target="_blank" rel="noopener noreferrer"
                    className="px-4 py-2 type-btn text-slate-600 hover:text-slate-900 rounded-full hover:bg-slate-100/80 transition"
                  >
                    Masuk
                  </a>
                  <a
                    href="/register/dropshipper" target="_blank" rel="noopener noreferrer"
                    className="px-5 py-2.5 type-btn text-white bg-emerald-600 hover:bg-emerald-500 rounded-full shadow-[0_2px_12px_rgba(5,150,105,0.28)] transition-all hover:-translate-y-0.5 active:translate-y-0"
                  >
                    Mulai Gratis
                  </a>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Mega Menu Dropdown: FITUR */}
        {openNavDropdown === 'fitur' && (
          <div className="bg-white border-t border-slate-100 shadow-2xl shadow-slate-900/10">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
              <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {/* Kiri: Daftar Fitur dengan Ikon Berwarna */}
                <div className="lg:col-span-8">
                  <div className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">
                    Fitur Utama OmniPOS
                  </div>
                  <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    {dockFeatures.map((feat) => {
                      const IconComp = feat.icon;
                      return (
                        <a
                          key={feat.id}
                          href="#hero-dock"
                          onClick={() => {
                            setActiveDockFeature(feat.id);
                            setOpenNavDropdown(null);
                          }}
                          className="p-3 rounded-2xl hover:bg-slate-50 transition flex items-start gap-3 group"
                        >
                          <div className={`w-10 h-10 rounded-xl text-white flex items-center justify-center shrink-0 shadow-sm ${feat.iconBoxClass} group-hover:scale-105 transition-transform`}>
                            <IconComp className="w-5 h-5" />
                          </div>
                          <div className="min-w-0">
                            <div className="flex items-center gap-1.5 flex-wrap">
                              <span className="text-sm font-semibold text-slate-900 group-hover:text-emerald-700 transition">
                                {feat.label}
                              </span>
                              <span className={`text-[9px] font-semibold uppercase px-1.5 py-0.5 rounded-full border ${feat.badgeClass}`}>
                                {feat.badge}
                              </span>
                            </div>
                            <p className="text-xs text-slate-500 mt-0.5 line-clamp-1">
                              {feat.title}
                            </p>
                          </div>
                        </a>
                      );
                    })}
                  </div>
                </div>

                {/* Kanan: Card Cara Kerja */}
                <div className="lg:col-span-4 lg:border-l lg:border-slate-100 lg:pl-8 flex flex-col justify-between">
                  <div>
                    <div className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">
                      Cara Kerja
                    </div>
                    <a
                      href="#cara-kerja"
                      onClick={() => setOpenNavDropdown(null)}
                      className="block rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 p-6 text-white shadow-lg hover:shadow-xl transition group"
                    >
                      <div className="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center mb-4">
                        <img src="/images/logo-omnipos.png" alt="OmniPOS" className="w-7 h-7 object-contain rounded-lg" />
                      </div>
                      <div className="text-base font-bold">Lihat alur kerja OmniPOS</div>
                      <p className="text-xs text-emerald-50/90 mt-1 leading-relaxed">
                        Pelajari bagaimana toko fisik dan dropshipper terhubung otomatis dari resi hingga pencairan laba.
                      </p>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Dropdown: PANDUAN & KONTAK */}
        {openNavDropdown === 'panduan' && (
          <div className="bg-white border-t border-slate-100 shadow-2xl shadow-slate-900/10">
            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a
                  href="#cara-kerja"
                  onClick={() => setOpenNavDropdown(null)}
                  className="p-4 rounded-2xl hover:bg-slate-50 border border-slate-100 transition group"
                >
                  <div className="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-3">
                    <Send className="w-4 h-4" />
                  </div>
                  <div className="text-sm font-semibold text-slate-900 group-hover:text-emerald-700">Cara Kerja</div>
                  <p className="text-xs text-slate-500 mt-1">Panduan langkah demi langkah untuk pemula.</p>
                </a>

                <a
                  href="#faq"
                  onClick={() => setOpenNavDropdown(null)}
                  className="p-4 rounded-2xl hover:bg-slate-50 border border-slate-100 transition group"
                >
                  <div className="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-3">
                    <Sparkles className="w-4 h-4" />
                  </div>
                  <div className="text-sm font-semibold text-slate-900 group-hover:text-emerald-700">Tanya Jawab (FAQ)</div>
                  <p className="text-xs text-slate-500 mt-1">Jawaban cepat seputar stok, resi, &amp; kasir.</p>
                </a>

                <a
                  href="https://wa.me/6283121782648?text=Halo%20Admin%20OmniPOS%2C%20saya%20ingin%20konsultasi%20layanan%20dropship%20dan%20toko"
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={() => setOpenNavDropdown(null)}
                  className="p-4 rounded-2xl hover:bg-emerald-50/70 border border-emerald-100 transition group"
                >
                  <div className="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-3">
                    <Phone className="w-4 h-4" />
                  </div>
                  <div className="text-sm font-semibold text-slate-900 group-hover:text-emerald-700">WhatsApp CS</div>
                  <p className="text-xs text-emerald-700 font-medium mt-1">+62 831-2178-2648</p>
                </a>

                <a
                  href="mailto:maulana88aditya@gmail.com"
                  onClick={() => setOpenNavDropdown(null)}
                  className="p-4 rounded-2xl hover:bg-slate-50 border border-slate-100 transition group"
                >
                  <div className="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center mb-3">
                    <Mail className="w-4 h-4" />
                  </div>
                  <div className="text-sm font-semibold text-slate-900 group-hover:text-emerald-700">Email Resmi</div>
                  <p className="text-xs text-slate-500 mt-1 truncate">maulana88aditya@gmail.com</p>
                </a>
              </div>
            </div>
          </div>
        )}
      </div>
    </header>

      {/* 2. Hero Section (Dua Kolom: Visual Dulu, Teks Singkat) */}
      <section className="pt-28 sm:pt-32 pb-12 sm:pb-14 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto scroll-reveal opacity-0 translate-y-8 transition-all duration-700 ease-out">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
          
          {/* Kolom Kiri: Teks Pendek & CTA */}
          <div className="lg:col-span-6 text-left">
            {/* Badge Kecil */}
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/80 type-badge mb-6">
              <Sparkles className="w-3.5 h-3.5 text-emerald-600" />
              <span>Platform dropship &amp; suplai toko</span>
            </div>

            {/* Headline Pendek (Maks 3 Baris, Sentence Case, Aksen Hijau 2 Kata) */}
            <h1 className="type-hero-h1">
              Mulai jualan online{' '}
              <span className="text-emerald-600">tanpa stok</span>{' '}
              barang &amp; modal besar
            </h1>

            {/* Sub-headline 1 Kalimat */}
            <p className="mt-5 type-body">
              Pilih produk supplier terpercaya, pasarkan di tokomu, pesanan dipackingkan gudang, dan komisi otomatis masuk ke saldo.
            </p>

            {/* 1 Tombol Utama + 1 Tombol Sekunder */}
            <div className="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
              <a
                href="/register/dropshipper" target="_blank" rel="noopener noreferrer"
                className="px-8 py-4 type-btn text-white bg-emerald-600 hover:bg-slate-900 rounded-full shadow-lg hover:shadow-emerald-900/25 transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center text-center cursor-pointer"
              >
                <span>Mulai Dropship</span>
              </a>

              <a
                href="#cara-kerja"
                className="px-7 py-4 type-btn text-slate-700 bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200/90 rounded-full shadow-xs transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center text-center cursor-pointer"
              >
                <span>Cara Kerja</span>
              </a>
            </div>
          </div>

          {/* Kolom Kanan: Mockup Dashboard OmniPOS + 3 Kartu Mengambang */}
          <div className="lg:col-span-6 relative">
            {/* Background Glow */}
            <div className="absolute -inset-4 bg-gradient-to-tr from-emerald-200/40 via-teal-100/30 to-green-100/20 rounded-[36px] blur-2xl -z-10" />

            {/* Frame Laptop / Browser Mockup */}
            <div className="relative rounded-[28px] bg-white border border-slate-200/90 shadow-[0_16px_40px_rgba(15,23,42,0.08)] overflow-hidden">
              {/* Window Header Dots & Address Bar */}
              <div className="h-10 px-4 bg-slate-50/90 border-b border-slate-200/80 flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <span className="w-2.5 h-2.5 rounded-full bg-rose-400" />
                  <span className="w-2.5 h-2.5 rounded-full bg-amber-400" />
                  <span className="w-2.5 h-2.5 rounded-full bg-emerald-400" />
                </div>
                <div className="px-3 py-1 rounded-full bg-white border border-slate-200 text-[10px] font-mono text-slate-400 flex items-center gap-1.5 shadow-2xs">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                  <span>app.omnipos.test/dropship</span>
                </div>
                <div className="w-10" />
              </div>

              {/* Inside Mockup Body */}
              <div className="p-5 sm:p-6 space-y-4 bg-slate-50/40">
                {/* Store Header & Saldo Dompet */}
                <div className="flex items-center justify-between p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-sm">
                      TB
                    </div>
                    <div>
                      <div className="flex items-center gap-1.5">
                        <span className="text-xs font-bold text-slate-900">Toko Berkah Toys</span>
                        <BadgeCheck className="w-3.5 h-3.5 text-emerald-600" />
                      </div>
                      <span className="text-[10px] text-slate-500 font-medium">Mitra Dropshipper Aktif</span>
                    </div>
                  </div>

                  <div className="text-right">
                    <span className="text-[10px] text-slate-400 font-medium block">Saldo Kasir / Komisi:</span>
                    <span className="text-sm font-bold text-emerald-700 font-mono">Rp 1.450.000</span>
                  </div>
                </div>

                {/* Featured Real Catalog Product Showcase (Gaya Visual E-Commerce Modern) */}
                <div className="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="text-[11px] font-bold text-slate-800 flex items-center gap-1.5">
                      <Sparkles className="w-3.5 h-3.5 text-amber-500" />
                      <span>Produk Winning Siap Dijual Hari Ini</span>
                    </span>
                    <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                      Gudang Ready
                    </span>
                  </div>

                  {/* 2 Sample Product Cards with Real Product Photos */}
                  <div className="grid grid-cols-2 gap-3">
                    {/* Product 1: Fashion / Pakaian */}
                    <div className="p-2.5 rounded-xl bg-slate-50/80 border border-slate-200/70 hover:border-emerald-300 transition group flex flex-col justify-between">
                      <div>
                        <div className="relative h-28 w-full rounded-lg overflow-hidden bg-white border border-slate-100">
                          <img 
                            src="/images/product-tshirt.jpg" 
                            alt="Kaos Oversize Streetwear" 
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                          />
                          <span className="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[9px] font-bold text-slate-700 shadow-2xs">
                            Fashion
                          </span>
                        </div>
                        <h5 className="text-[11px] font-bold text-slate-900 mt-2 line-clamp-1 group-hover:text-emerald-700 transition">
                          Kaos Oversize Cotton Combed
                        </h5>
                        <div className="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                          <span>Modal: Rp 35rb</span>
                          <span>Jual: Rp 85rb</span>
                        </div>
                      </div>
                      <div className="mt-2 pt-1.5 border-t border-slate-200/60 flex items-center justify-between">
                        <span className="text-[10px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">
                          +Rp 50.000 Laba
                        </span>
                        <span className="text-[9px] text-slate-400 font-medium">95 pcs</span>
                      </div>
                    </div>

                    {/* Product 2: Gadget / TWS */}
                    <div className="p-2.5 rounded-xl bg-slate-50/80 border border-slate-200/70 hover:border-emerald-300 transition group flex flex-col justify-between">
                      <div>
                        <div className="relative h-28 w-full rounded-lg overflow-hidden bg-white border border-slate-100">
                          <img 
                            src="/images/product-tws.jpg" 
                            alt="Wireless Earbuds TWS Pro" 
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                          />
                          <span className="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[9px] font-bold text-slate-700 shadow-2xs">
                            Gadget
                          </span>
                        </div>
                        <h5 className="text-[11px] font-bold text-slate-900 mt-2 line-clamp-1 group-hover:text-emerald-700 transition">
                          Wireless Earbuds TWS Pro Bass
                        </h5>
                        <div className="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                          <span>Modal: Rp 45rb</span>
                          <span>Jual: Rp 99rb</span>
                        </div>
                      </div>
                      <div className="mt-2 pt-1.5 border-t border-slate-200/60 flex items-center justify-between">
                        <span className="text-[10px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">
                          +Rp 54.000 Laba
                        </span>
                        <span className="text-[9px] text-slate-400 font-medium">120 pcs</span>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Mini Order Tracking Row */}
                <div className="p-3 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center justify-between text-xs">
                  <div className="flex items-center gap-2.5">
                    <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                      <Truck className="w-4 h-4" />
                    </div>
                    <div>
                      <div className="text-[11px] font-bold text-slate-900">Paket Diserahkan ke Kurir</div>
                      <div className="text-[10px] text-slate-400">Tujuan: Bandung, Jawa Barat</div>
                    </div>
                  </div>
                  <span className="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                    Otomatis Sinkron
                  </span>
                </div>
              </div>
            </div>

            {/* 3 Kartu Mengambang dengan Foto Produk Nyata & Aksen Warna Eye-Catching */}
            {/* Kartu 1: Order Terkirim dengan Foto Produk Nyata (Atas Kanan) - Rose/Coral Pop */}
            <div className="absolute -top-6 -right-3 sm:-right-6 bg-white/95 backdrop-blur-md border border-rose-200/90 shadow-[0_12px_32px_rgba(244,63,94,0.12)] rounded-2xl p-2.5 sm:p-3 flex items-center gap-3 z-20 hover:scale-105 transition-transform duration-200">
              <div className="w-11 h-11 rounded-xl overflow-hidden bg-rose-50 shrink-0 border border-rose-100 relative">
                <img src="/images/product-tshirt.jpg" alt="Kaos" className="w-full h-full object-cover" />
                <span className="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-rose-500 animate-ping" />
              </div>
              <div>
                <div className="flex items-center gap-1.5">
                  <div className="text-[11px] font-bold text-slate-800">Pesanan Siap Dikirim</div>
                  <span className="text-[9px] font-bold px-1.5 py-0.2 rounded bg-rose-100 text-rose-700">Laris</span>
                </div>
                <div className="text-[10px] text-emerald-700 font-semibold flex items-center gap-1 mt-0.5">
                  <CheckCircle2 className="w-3 h-3 text-emerald-600" />
                  <span>Komisi +Rp 50.000 Masuk</span>
                </div>
              </div>
            </div>

            {/* Kartu 2: Saldo Masuk (Bawah Kiri) - Purple/Violet Pop */}
            <div className="absolute -bottom-6 -left-3 sm:-left-6 bg-white/95 backdrop-blur-md border border-purple-200/90 shadow-[0_12px_32px_rgba(147,51,234,0.14)] rounded-2xl p-2.5 sm:p-3 flex items-center gap-3 z-20 hover:scale-105 transition-transform duration-200">
              <div className="w-11 h-11 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-600/30">
                <Wallet className="w-5 h-5" />
              </div>
              <div>
                <div className="flex items-center gap-1.5">
                  <div className="text-[11px] font-bold text-slate-900">Saldo +Rp 250.000</div>
                  <span className="text-[9px] font-bold px-1.5 py-0.2 rounded bg-purple-100 text-purple-700">Instan</span>
                </div>
                <div className="text-[10px] text-slate-500 font-medium">BCA, Mandiri, BRI &amp; E-Wallet</div>
              </div>
            </div>

            {/* Kartu 3: Smartwatch Ready (Tengah Kanan) - Amber/Orange Pop */}
            <div className="hidden sm:flex absolute top-1/2 -right-8 -translate-y-1/2 bg-white/95 backdrop-blur-md shadow-[0_12px_32px_rgba(245,158,11,0.12)] rounded-2xl p-2.5 items-center gap-2.5 z-20 border border-amber-200/90 text-xs hover:scale-105 transition-transform duration-200">
              <div className="w-9 h-9 rounded-lg overflow-hidden bg-amber-50 shrink-0 border border-amber-100">
                <img src="/images/product-smartwatch.jpg" alt="Smartwatch" className="w-full h-full object-cover" />
              </div>
              <div>
                <div className="text-[10px] font-bold text-slate-800 flex items-center gap-1.5">
                  <span className="px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 font-bold text-[9px]">⭐ 4.9</span>
                  <span>120 pcs Siap Jual</span>
                </div>
                <span className="text-[9px] text-amber-700 font-semibold">Gudang Toko Mitra</span>
              </div>
            </div>
          </div>
        </div>

        {/* Di Bawah Hero: Interactive Feature Dock (Ikon Besar, Berdempetan, Tanpa BG Wadah, Smooth Scale) */}
        <div id="hero-dock" className="mt-16 pt-8 scroll-mt-24">
          <div className="flex justify-center">
            <div className="inline-flex items-center gap-2 sm:gap-2.5 flex-wrap justify-center py-4">
              {dockFeatures.map((feat) => {
                const IconComp = feat.icon;
                const isSelected = activeDockFeature === feat.id;
                const isHovered = hoveredDockFeature === feat.id;
                const isFocused = isHovered || (hoveredDockFeature === null && isSelected);
                return (
                  <button
                    key={feat.id}
                    type="button"
                    onMouseEnter={() => setHoveredDockFeature(feat.id)}
                    onMouseLeave={() => setHoveredDockFeature(null)}
                    onClick={() => setActiveDockFeature(feat.id)}
                    aria-label={feat.label}
                    className={`relative focus:outline-none cursor-pointer transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] ${
                      isFocused ? 'z-20 mx-1.5 sm:mx-2' : 'z-10'
                    }`}
                  >
                    {/* Tooltip Nama Fitur di Atas Ikon */}
                    <div
                      className={`absolute -top-12 left-1/2 -translate-x-1/2 whitespace-nowrap px-3.5 py-1.5 rounded-xl bg-white border border-slate-200/90 shadow-lg text-xs font-bold text-slate-800 z-30 pointer-events-none transition-all duration-200 ${
                        isFocused ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-2 scale-95'
                      }`}
                    >
                      {feat.label}
                      <span className="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-white border-r border-b border-slate-200/90 rotate-45" />
                    </div>

                    {/* Ikon Aplikasi Besar Berwarna dengan Smooth Spring Scale */}
                    <div
                      className={`w-14 h-14 sm:w-16 sm:h-16 rounded-2xl text-white flex items-center justify-center transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] shadow-lg ${feat.iconBoxClass} ${
                        isFocused
                          ? '-translate-y-2.5 scale-125 shadow-2xl ring-2 ring-white/90'
                          : 'opacity-90 hover:opacity-100 hover:-translate-y-1'
                      }`}
                    >
                      <IconComp className="w-7 h-7 sm:w-8 sm:h-8" />
                    </div>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Penjelasan & Visual Preview Fitur Terpilih (Nge-blend dengan Background, Garis Tipis Ujung ke Ujung) */}
          <div className="mt-8 pt-10 border-t border-slate-200/80">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
              {/* Kiri: Penjelasan Fitur */}
              <div className="lg:col-span-5 space-y-5">
                {(() => {
                  const ActiveIcon = currentDockFeature.icon;
                  return (
                    <div className={`w-12 h-12 rounded-2xl text-white flex items-center justify-center shadow-lg ${currentDockFeature.iconBoxClass}`}>
                      <ActiveIcon className="w-6 h-6" />
                    </div>
                  );
                })()}

                <div>
                  <div className="flex items-center gap-2.5 flex-wrap">
                    <h3 className="type-h3">
                      {currentDockFeature.title}
                    </h3>
                    <span className={`type-badge px-2.5 py-0.5 rounded-full border ${currentDockFeature.badgeClass}`}>
                      {currentDockFeature.badge}
                    </span>
                  </div>
                  <p className="mt-2.5 type-body text-[15px]">
                    {currentDockFeature.desc}
                  </p>
                </div>

                <div className="space-y-2.5">
                  {currentDockFeature.points.map((pt, idx) => (
                    <div key={idx} className="flex items-center gap-2.5 text-xs sm:text-sm text-slate-700 font-medium">
                      <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                      <span>{pt}</span>
                    </div>
                  ))}
                </div>

                <div className="pt-2">
                  <a
                    href={currentDockFeature.ctaHref} target="_blank" rel="noopener noreferrer"
                    className="inline-flex items-center justify-center px-6 py-3 rounded-full bg-emerald-600 hover:bg-slate-900 text-white type-btn shadow-md transition-all duration-300 hover:scale-105 active:scale-95"
                  >
                    {currentDockFeature.ctaText}
                  </a>
                </div>
              </div>

              {/* Kanan: Visual Mockup Spesifik per Fitur */}
              <div className="lg:col-span-7">
                {activeDockFeature === 'pos' && (
                  <div className="space-y-3">
                    <div className="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 text-xs">
                      <div className="flex items-center gap-2.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" />
                        <span className="font-bold text-slate-900">Kasir Toko Fisik &amp; Order Online</span>
                      </div>
                      <span className="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                        Printer 58mm Siap
                      </span>
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <div className="p-3.5 rounded-xl bg-white border border-slate-200 space-y-2 text-xs">
                        <div className="flex justify-between font-semibold text-slate-700">
                          <span>Kaos Oversize x2</span>
                          <span>Rp 140.000</span>
                        </div>
                        <div className="flex justify-between font-semibold text-slate-700">
                          <span>Smartwatch Sport x1</span>
                          <span>Rp 149.000</span>
                        </div>
                        <div className="pt-2 border-t border-slate-100 flex justify-between font-bold text-emerald-700">
                          <span>Total Bayar</span>
                          <span>Rp 289.000</span>
                        </div>
                      </div>
                      <div className="p-3.5 rounded-xl bg-white border border-dashed border-emerald-300 text-center space-y-1.5 text-xs flex flex-col justify-center">
                        <div className="font-bold text-slate-900">STRUK THERMAL</div>
                        <div className="text-[11px] text-slate-500">Logo Toko + QRIS + Garansi</div>
                        <span className="inline-block mx-auto px-3 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold">
                          Cetak Instan
                        </span>
                      </div>
                    </div>
                  </div>
                )}

                {activeDockFeature === 'catalog' && (
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div className="p-3.5 rounded-xl bg-white border border-slate-200 space-y-2 text-xs">
                      <div className="flex items-center gap-3">
                        <img src="/images/product-tshirt.jpg" alt="Kaos" className="w-12 h-12 rounded-xl object-cover border border-slate-100" />
                        <div>
                          <div className="font-bold text-slate-900">Kaos Cotton Combed 30s</div>
                          <div className="text-[11px] text-slate-500">Modal: Rp 35.000</div>
                        </div>
                      </div>
                      <div className="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span className="text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded">+Rp 50rb Laba</span>
                        <span className="text-slate-500 font-semibold">Stok: 240 pcs</span>
                      </div>
                    </div>
                    <div className="p-3.5 rounded-xl bg-white border border-slate-200 space-y-2 text-xs">
                      <div className="flex items-center gap-3">
                        <img src="/images/product-smartwatch.jpg" alt="Smartwatch" className="w-12 h-12 rounded-xl object-cover border border-slate-100" />
                        <div>
                          <div className="font-bold text-slate-900">Smartwatch Sport LED</div>
                          <div className="text-[11px] text-slate-500">Modal: Rp 95.000</div>
                        </div>
                      </div>
                      <div className="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span className="text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded">+Rp 54rb Laba</span>
                        <span className="text-slate-500 font-semibold">Stok: 120 pcs</span>
                      </div>
                    </div>
                  </div>
                )}

                {activeDockFeature === 'tracker' && (
                  <div className="p-4 rounded-xl bg-white border border-slate-200 space-y-3 text-xs">
                    <div className="flex items-center justify-between pb-2 border-b border-slate-100">
                      <span className="font-bold text-slate-900">Status Pengiriman Paket</span>
                      <span className="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 font-bold text-[10px]">
                        J&amp;T Express • Terlacak
                      </span>
                    </div>
                    <div className="space-y-2.5">
                      <div className="flex items-center justify-between">
                        <span className="font-semibold text-slate-800">1. Resi Terverifikasi Sistem</span>
                        <span className="text-emerald-600 font-bold">Selesai</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <span className="font-semibold text-slate-800">2. Packing Bubble Wrap Gudang</span>
                        <span className="text-emerald-600 font-bold">Selesai</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <span className="font-semibold text-sky-700">3. Diserahkan ke Kurir Ekspedisi</span>
                        <span className="px-2 py-0.5 rounded bg-sky-50 text-sky-700 font-bold">Dalam Perjalanan</span>
                      </div>
                    </div>
                  </div>
                )}

                {activeDockFeature === 'research' && (
                  <div className="p-4 rounded-xl bg-white border border-slate-200 space-y-2.5 text-xs">
                    <div className="flex items-center justify-between pb-2 border-b border-slate-100">
                      <span className="font-bold text-slate-900">Produk Paling Laris Minggu Ini</span>
                      <span className="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">
                        Margin Tinggi
                      </span>
                    </div>
                    <div className="flex items-center justify-between p-2 rounded-lg bg-amber-50/50">
                      <span className="font-bold text-slate-800">1. Smartwatch Sport Anti Air</span>
                      <span className="font-semibold text-emerald-700">420 terjual • Laba 57%</span>
                    </div>
                    <div className="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                      <span className="font-bold text-slate-800">2. Kaos Oversize Combed 30s</span>
                      <span className="font-semibold text-emerald-700">385 terjual • Laba 58%</span>
                    </div>
                    <div className="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                      <span className="font-bold text-slate-800">3. Tas Ransel Laptop Waterproof</span>
                      <span className="font-semibold text-emerald-700">290 terjual • Laba 45%</span>
                    </div>
                  </div>
                )}

                {activeDockFeature === 'shops' && (
                  <div className="p-4 rounded-xl bg-white border border-slate-200 space-y-2.5 text-xs">
                    <div className="flex items-center justify-between pb-2 border-b border-slate-100">
                      <span className="font-bold text-slate-900">Direktori Toko &amp; Gudang Mitra</span>
                      <span className="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-bold text-[10px]">
                        Terverifikasi
                      </span>
                    </div>
                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-indigo-50/40 border border-indigo-100">
                      <div>
                        <div className="font-bold text-slate-900">Cita Laksana Toys</div>
                        <div className="text-[11px] text-slate-500">Garut, Jawa Barat • 180+ Produk</div>
                      </div>
                      <span className="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Packing &lt; 12 Jam</span>
                    </div>
                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                      <div>
                        <div className="font-bold text-slate-900">Sentra Apparel Bandung</div>
                        <div className="text-[11px] text-slate-500">Bandung, Jawa Barat • 240+ Produk</div>
                      </div>
                      <span className="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Grosir Resmi</span>
                    </div>
                  </div>
                )}

                {activeDockFeature === 'dropshippers' && (
                  <div className="p-4 rounded-xl bg-white border border-slate-200 space-y-2.5 text-xs">
                    <div className="flex items-center justify-between pb-2 border-b border-slate-100">
                      <span className="font-bold text-slate-900">Mitra Dropshipper Aktif</span>
                      <span className="px-2.5 py-0.5 rounded-full bg-cyan-100 text-cyan-800 font-bold text-[10px]">
                        1.200+ Mitra
                      </span>
                    </div>
                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-cyan-50/40 border border-cyan-100">
                      <div>
                        <div className="font-bold text-slate-900">Toko Berkah Online</div>
                        <div className="text-[11px] text-slate-500">Shopee &amp; TikTok • 312 Order/Bln</div>
                      </div>
                      <span className="font-semibold text-emerald-700">+Rp 9.400.000 Laba</span>
                    </div>
                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                      <div>
                        <div className="font-bold text-slate-900">Laris Jaya Store</div>
                        <div className="text-[11px] text-slate-500">Tokopedia &amp; WA • 218 Order/Bln</div>
                      </div>
                      <span className="font-semibold text-emerald-700">+Rp 6.850.000 Laba</span>
                    </div>
                  </div>
                )}

                {activeDockFeature === 'wallet' && (
                  <div className="p-4 rounded-xl bg-white border border-slate-200 space-y-2.5 text-xs">
                    <div className="flex items-center justify-between pb-2 border-b border-slate-100">
                      <span className="font-bold text-slate-900">Pembagian Uang Otomatis per Order</span>
                      <span className="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 font-bold text-[10px]">
                        Transparan
                      </span>
                    </div>
                    <div className="flex justify-between p-2 rounded-lg bg-slate-50">
                      <span className="text-slate-600">Pembayaran Pembeli</span>
                      <span className="font-bold text-slate-900">Rp 150.000</span>
                    </div>
                    <div className="flex justify-between p-2 rounded-lg bg-slate-50">
                      <span className="text-slate-600">Bagian Toko Supplier</span>
                      <span className="font-bold text-slate-700">Rp 95.000</span>
                    </div>
                    <div className="flex justify-between p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900">
                      <span className="font-bold">Komisi Masuk Dompet Anda</span>
                      <span className="font-bold text-emerald-700">+Rp 55.000</span>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 3. Section Edukatif: Apa Itu Dropship di OmniPOS? (Dropship.io Inspired Smooth Green UI) */}
      <section
        id="cara-kerja"
        ref={caraKerjaRef}
        className="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 bg-white border-y border-slate-100 relative overflow-hidden scroll-reveal opacity-0 translate-y-8 transition-all duration-700 ease-out"
      >
        <div className="max-w-6xl mx-auto relative z-10">
          {/* Clean Header */}
          <div className="max-w-2xl mx-auto text-center mb-10 sm:mb-12">
            <h2 className="type-h2 text-slate-900">
              Apa itu dropship di <span className="text-emerald-600">OmniPOS</span>?
            </h2>
            <p className="mt-2.5 text-sm sm:text-base text-slate-500 leading-relaxed">
              Kamu cukup fokus memasarkan produk supplier di tokomu — urusan stok fisik, kemas paket, hingga pengiriman kurir diselesaikan otomatis oleh gudang mitra.
            </p>
          </div>

          {/* Row 1: 3 Main Cards with Smooth Radial Green Glow & Vector Illustrations */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
            {/* Card 1: Handsfree / Otomatis Tanpa Stok (Calendar Illustration) */}
            <div className="rounded-3xl p-7 sm:p-8 border border-emerald-100/70 bg-[radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.13)_0%,rgba(236,253,245,0.55)_52%,#f8fcfa_100%)] flex flex-col justify-between min-h-[370px] transition-transform duration-300 hover:-translate-y-0.5">
              <div>
                <div className="flex items-center gap-2.5">
                  <svg className="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                  </svg>
                  <h3 className="text-base sm:text-[17px] font-bold text-slate-900 tracking-tight">
                    Otomatis &amp; Tanpa Ribet
                  </h3>
                </div>
                <p className="mt-3 text-xs sm:text-[13.5px] text-slate-500 leading-relaxed">
                  Terima pembaruan katalog produk terlaris dan stok gudang setiap minggu secara otomatis, langsung masuk ke dasbor kemitraan OmniPOS kamu.
                </p>
              </div>

              {/* Smooth 3D-style Calendar Vector Illustration */}
              <div className="mt-8 flex items-center justify-center py-2">
                <svg className="w-40 h-36 drop-shadow-[0_14px_24px_rgba(16,185,129,0.12)]" viewBox="0 0 180 160" fill="none">
                  <g transform="rotate(-6 90 85)">
                    {/* Outer Soft Frame */}
                    <rect x="26" y="26" width="128" height="114" rx="22" fill="#E2E8F0" fillOpacity="0.55" />
                    <rect x="22" y="22" width="128" height="114" rx="22" fill="#FFFFFF" stroke="#CBD5E1" strokeWidth="1.5" />
                    {/* Top Calendar Header Bar */}
                    <path d="M28 42C28 33.1634 35.1634 26 44 26H128C136.837 26 144 33.1634 144 42V54H28V42Z" fill="#CBD5E1" fillOpacity="0.65" />
                    {/* Calendar Rings */}
                    <rect x="52" y="13" width="10" height="24" rx="5" fill="#94A3B8" stroke="#FFFFFF" strokeWidth="2" />
                    <rect x="110" y="13" width="10" height="24" rx="5" fill="#94A3B8" stroke="#FFFFFF" strokeWidth="2" />
                    {/* Calendar Grid Dots */}
                    <rect x="38" y="66" width="16" height="12" rx="4" fill="#CBD5E1" />
                    <rect x="64" y="66" width="16" height="12" rx="4" fill="#CBD5E1" />
                    <rect x="90" y="66" width="16" height="12" rx="4" fill="#CBD5E1" />
                    <rect x="116" y="66" width="16" height="12" rx="4" fill="#CBD5E1" />

                    <rect x="38" y="86" width="16" height="12" rx="4" fill="#CBD5E1" />
                    <rect x="64" y="86" width="16" height="12" rx="4" fill="#CBD5E1" />
                    {/* Highlighted Emerald Active Day */}
                    <rect x="90" y="86" width="16" height="12" rx="4" fill="#10B981" />
                    <rect x="116" y="86" width="16" height="12" rx="4" fill="#CBD5E1" />

                    <rect x="38" y="106" width="16" height="12" rx="4" fill="#CBD5E1" />
                    <rect x="64" y="106" width="16" height="12" rx="4" fill="#CBD5E1" />
                    <rect x="90" y="106" width="16" height="12" rx="4" fill="#E2E8F0" />
                    <rect x="116" y="106" width="16" height="12" rx="4" fill="#E2E8F0" />
                  </g>
                </svg>
              </div>
            </div>

            {/* Card 2: Save time & money / Hemat Waktu & Modal (Clock + Cash + Coin Illustration) */}
            <div className="rounded-3xl p-7 sm:p-8 border border-emerald-100/70 bg-[radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.14)_0%,rgba(236,253,245,0.55)_52%,#f8fcfa_100%)] flex flex-col justify-between min-h-[370px] transition-transform duration-300 hover:-translate-y-0.5">
              <div>
                <div className="flex items-center gap-2.5">
                  <svg className="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15 1H9v2h6V1zm-4 13h2V8h-2v6zm8.03-6.61l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.07 4.74 14.12 4 12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.12-.74-4.07-1.97-5.61zM12 20c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z" />
                  </svg>
                  <h3 className="text-base sm:text-[17px] font-bold text-slate-900 tracking-tight">
                    Hemat Waktu &amp; Modal
                  </h3>
                </div>
                <p className="mt-3 text-xs sm:text-[13.5px] text-slate-500 leading-relaxed">
                  Pantau tren produk yang sedang laris, pergerakan stok gudang, dan peluang margin tebal tanpa perlu sewa gudang atau beli stok di awal.
                </p>
              </div>

              {/* Smooth Clock + Green Banknote + Gold Coin Vector Illustration */}
              <div className="mt-8 flex items-center justify-center py-2">
                <svg className="w-48 h-36 drop-shadow-[0_14px_24px_rgba(16,185,129,0.14)]" viewBox="0 0 210 160" fill="none">
                  {/* Tilted Green Banknote */}
                  <g transform="rotate(10 132 76)">
                    <rect x="82" y="34" width="102" height="64" rx="12" fill="#22C55E" stroke="#16A34A" strokeWidth="2" />
                    <rect x="90" y="42" width="86" height="48" rx="8" fill="#4ADE80" fillOpacity="0.35" stroke="#86EFAC" strokeWidth="1.2" />
                    <circle cx="133" cy="66" r="15" fill="#16A34A" fillOpacity="0.35" />
                    <text x="133" y="73" textAnchor="middle" fill="#FFFFFF" fontSize="20" fontWeight="800" fontFamily="sans-serif">$</text>
                  </g>
                  {/* Clock Circle on Left */}
                  <circle cx="74" cy="84" r="46" fill="#F1F5F9" stroke="#CBD5E1" strokeWidth="1.5" />
                  <circle cx="74" cy="84" r="38" fill="#FFFFFF" stroke="#94A3B8" strokeWidth="3" />
                  {/* Clock Ticks */}
                  <line x1="74" y1="51" x2="74" y2="56" stroke="#CBD5E1" strokeWidth="2.5" strokeLinecap="round" />
                  <line x1="74" y1="112" x2="74" y2="117" stroke="#CBD5E1" strokeWidth="2.5" strokeLinecap="round" />
                  <line x1="41" y1="84" x2="46" y2="84" stroke="#CBD5E1" strokeWidth="2.5" strokeLinecap="round" />
                  <line x1="102" y1="84" x2="107" y2="84" stroke="#CBD5E1" strokeWidth="2.5" strokeLinecap="round" />
                  {/* Clock Hands */}
                  <line x1="74" y1="84" x2="74" y2="62" stroke="#64748B" strokeWidth="3.5" strokeLinecap="round" />
                  <line x1="74" y1="84" x2="90" y2="96" stroke="#475569" strokeWidth="3.5" strokeLinecap="round" />
                  <circle cx="74" cy="84" r="4.5" fill="#334155" />
                  {/* Gold Coin on Bottom Right */}
                  <circle cx="152" cy="108" r="24" fill="#F59E0B" stroke="#FBBF24" strokeWidth="3" />
                  <circle cx="152" cy="108" r="18" fill="#D97706" fillOpacity="0.35" />
                  <text x="152" y="115" textAnchor="middle" fill="#FFFFFF" fontSize="19" fontWeight="800" fontFamily="sans-serif">$</text>
                </svg>
              </div>
            </div>

            {/* Card 3: One sale, bill paid / Satu Order Langsung Untung (Receipt Illustration) */}
            <div className="rounded-3xl p-7 sm:p-8 border border-emerald-100/70 bg-[radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.13)_0%,rgba(236,253,245,0.55)_52%,#f8fcfa_100%)] flex flex-col justify-between min-h-[370px] transition-transform duration-300 hover:-translate-y-0.5">
              <div>
                <div className="flex items-center gap-2.5">
                  <svg className="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z" />
                  </svg>
                  <h3 className="text-base sm:text-[17px] font-bold text-slate-900 tracking-tight">
                    Satu Order, Langsung Untung
                  </h3>
                </div>
                <p className="mt-3 text-xs sm:text-[13.5px] text-slate-500 leading-relaxed">
                  Begitu pembeli checkout di tokomu, selisih harga jual eceran dan harga grosir supplier langsung menjadi komisi bersih milikmu tanpa potongan.
                </p>
              </div>

              {/* Smooth Tilted Receipt / Invoice Vector Illustration */}
              <div className="mt-8 flex items-center justify-center py-2">
                <svg className="w-40 h-36 drop-shadow-[0_14px_24px_rgba(16,185,129,0.12)]" viewBox="0 0 180 160" fill="none">
                  <g transform="rotate(6 90 82)">
                    {/* Back Fold of Receipt */}
                    <path d="M46 22H128C134.627 22 140 27.3726 140 34V44H46V22Z" fill="#E2E8F0" />
                    {/* Main Receipt Body with Zigzag Bottom */}
                    <path
                      d="M42 28C42 22.4772 46.4772 18 52 18H124C129.523 18 134 22.4772 134 28V134L123 126L112 134L101 126L90 134L79 126L68 134L57 126L42 134V28Z"
                      fill="#FFFFFF"
                      stroke="#CBD5E1"
                      strokeWidth="1.5"
                    />
                    {/* Green Dollar / Profit Symbol */}
                    <text x="60" y="54" fill="#16A34A" fontSize="24" fontWeight="800" fontFamily="sans-serif">$</text>
                    <rect x="78" y="42" width="42" height="7" rx="3.5" fill="#94A3B8" />
                    {/* Receipt Line Items */}
                    <rect x="54" y="68" width="8" height="8" rx="2" fill="#94A3B8" />
                    <rect x="68" y="70" width="34" height="5" rx="2.5" fill="#CBD5E1" />
                    <rect x="108" y="70" width="14" height="5" rx="2.5" fill="#94A3B8" />

                    <rect x="54" y="84" width="8" height="8" rx="2" fill="#94A3B8" />
                    <rect x="68" y="86" width="30" height="5" rx="2.5" fill="#CBD5E1" />
                    <rect x="108" y="86" width="14" height="5" rx="2.5" fill="#94A3B8" />

                    <rect x="54" y="100" width="8" height="8" rx="2" fill="#10B981" />
                    <rect x="68" y="102" width="36" height="5" rx="2.5" fill="#CBD5E1" />
                    <rect x="108" y="102" width="14" height="5" rx="2.5" fill="#10B981" />
                  </g>
                </svg>
              </div>
            </div>
          </div>

          {/* Row 2: 3 Cards with Smooth Radial Green Glow & Vector Illustrations */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6 mt-5 sm:mt-6">
            {/* Bottom Card 1: Extensive insights / Transparansi Margin Lengkap (Rising Bar Chart Illustration) */}
            <div className="rounded-3xl p-7 sm:p-8 border border-emerald-100/70 bg-[radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.13)_0%,rgba(236,253,245,0.55)_52%,#f8fcfa_100%)] flex flex-col justify-between min-h-[370px] transition-transform duration-300 hover:-translate-y-0.5">
              <div>
                <div className="flex items-center gap-2.5">
                  <svg className="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11 2v20c-5.07-.5-9-4.79-9-10s3.93-9.5 9-10zm2.03 0v8.99H22c-.47-4.74-4.24-8.52-8.97-8.99zm0 11.01V22c4.74-.47 8.5-4.25 8.97-8.99h-8.97z" />
                  </svg>
                  <h3 className="text-base sm:text-[17px] font-bold text-slate-900 tracking-tight">
                    Transparansi Margin Lengkap
                  </h3>
                </div>
                <p className="mt-3 text-xs sm:text-[13.5px] text-slate-500 leading-relaxed">
                  Bangun fondasi toko online yang kuat dengan informasi harga modal, rekomendasi harga jual, dan estimasi keuntungan bersih di setiap produk.
                </p>
              </div>

              {/* Smooth 3D-style Rising Bar Chart Vector Illustration */}
              <div className="mt-8 flex items-center justify-center py-2">
                <svg className="w-44 h-36 drop-shadow-[0_14px_24px_rgba(16,185,129,0.12)]" viewBox="0 0 190 160" fill="none">
                  <g transform="rotate(-4 95 90)">
                    {/* Background Soft Ghost Bars */}
                    <rect x="26" y="78" width="22" height="54" rx="5" fill="#E2E8F0" fillOpacity="0.65" />
                    <rect x="54" y="60" width="22" height="72" rx="5" fill="#E2E8F0" fillOpacity="0.55" />
                    <rect x="82" y="42" width="22" height="90" rx="5" fill="#D1FAE5" fillOpacity="0.7" />
                    <rect x="110" y="24" width="22" height="108" rx="5" fill="#FEF3C7" fillOpacity="0.7" />
                    <rect x="136" y="14" width="22" height="118" rx="5" fill="#F1F5F9" stroke="#E2E8F0" strokeWidth="1" />

                    {/* Foreground Vibrant 4 Bars */}
                    <rect x="36" y="84" width="22" height="44" rx="5" fill="#94A3B8" stroke="#64748B" strokeWidth="1.2" />
                    <rect x="64" y="66" width="22" height="62" rx="5" fill="#0D9488" stroke="#0F766E" strokeWidth="1.2" />
                    <rect x="92" y="48" width="22" height="80" rx="5" fill="#22C55E" stroke="#16A34A" strokeWidth="1.2" />
                    <rect x="120" y="30" width="22" height="98" rx="5" fill="#F59E0B" stroke="#D97706" strokeWidth="1.2" />

                    {/* Base Pedestal Bars */}
                    <rect x="28" y="128" width="124" height="8" rx="4" fill="#CBD5E1" stroke="#94A3B8" strokeWidth="1" />
                    <rect x="18" y="138" width="144" height="8" rx="4" fill="#F1F5F9" stroke="#CBD5E1" strokeWidth="1" />
                  </g>
                </svg>
              </div>
            </div>

            {/* Bottom Card 2: Stay up to date / Stok & Resi Selalu Terupdate (Notification Bell Badge Illustration) */}
            <div className="rounded-3xl p-7 sm:p-8 border border-emerald-100/70 bg-[radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.13)_0%,rgba(236,253,245,0.55)_52%,#f8fcfa_100%)] flex flex-col justify-between min-h-[370px] transition-transform duration-300 hover:-translate-y-0.5">
              <div>
                <div className="flex items-center gap-2.5">
                  <svg className="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                  </svg>
                  <h3 className="text-base sm:text-[17px] font-bold text-slate-900 tracking-tight">
                    Stok &amp; Resi Selalu Terupdate
                  </h3>
                </div>
                <p className="mt-3 text-xs sm:text-[13.5px] text-slate-500 leading-relaxed">
                  Tidak perlu lagi tanya stok manual ke admin gudang. Semua sisa stok fisik dan status pengemasan resi diperbarui secara realtime.
                </p>
              </div>

              {/* Smooth Tilted Notification Card with Bell & Red Badge "1" Illustration */}
              <div className="mt-8 flex items-center justify-center py-2">
                <svg className="w-44 h-36 drop-shadow-[0_14px_24px_rgba(16,185,129,0.12)]" viewBox="0 0 190 160" fill="none">
                  <g transform="rotate(5 95 84)">
                    {/* Outer Translucent Frame */}
                    <rect x="20" y="34" width="150" height="96" rx="22" fill="#F1F5F9" fillOpacity="0.85" stroke="#E2E8F0" strokeWidth="1.5" />
                    {/* Inner White Card */}
                    <rect x="30" y="44" width="130" height="76" rx="16" fill="#FFFFFF" stroke="#CBD5E1" strokeWidth="1.5" />
                    {/* 3D Soft Bell Icon */}
                    <path d="M62 104C65.3137 104 68 101.314 68 98H56C56 101.314 58.6863 104 62 104Z" fill="#94A3B8" />
                    <path
                      d="M78 90V76C78 67.8 72.6 61 65 59.2V57C65 55.3 63.7 54 62 54C60.3 54 59 55.3 59 57V59.2C51.4 61 46 67.8 46 76V90L41 95V97H83V95L78 90Z"
                      fill="#E2E8F0"
                      stroke="#CBD5E1"
                      strokeWidth="1.2"
                    />
                    {/* Red Notification Badge "1" */}
                    <circle cx="80" cy="64" r="11" fill="#EF4444" stroke="#FFFFFF" strokeWidth="2" />
                    <text x="80" y="68" textAnchor="middle" fill="#FFFFFF" fontSize="12" fontWeight="800" fontFamily="sans-serif">1</text>
                    {/* Notification Text Lines */}
                    <rect x="96" y="76" width="46" height="6" rx="3" fill="#94A3B8" />
                    <rect x="96" y="88" width="32" height="6" rx="3" fill="#CBD5E1" />
                  </g>
                </svg>
              </div>
            </div>

            {/* Bottom Card 3: On the go / Pantau dari Mana Saja (Smartphone Illustration) */}
            <div className="rounded-3xl p-7 sm:p-8 border border-emerald-100/70 bg-[radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.13)_0%,rgba(236,253,245,0.55)_52%,#f8fcfa_100%)] flex flex-col justify-between min-h-[370px] transition-transform duration-300 hover:-translate-y-0.5">
              <div>
                <div className="flex items-center gap-2.5">
                  <svg className="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm2.19 12.19L6 18l3.81-8.19L18 6l-3.81 8.19z" />
                  </svg>
                  <h3 className="text-base sm:text-[17px] font-bold text-slate-900 tracking-tight">
                    Pantau dari Mana Saja
                  </h3>
                </div>
                <p className="mt-3 text-xs sm:text-[13.5px] text-slate-500 leading-relaxed">
                  Ada pesanan masuk saat kamu sedang di luar? Teruskan order dan unggah resi pengiriman langsung dari HP atau laptop kapan saja.
                </p>
              </div>

              {/* Smooth Tilted Smartphone with OmniPOS Green Emblem Illustration */}
              <div className="mt-8 flex items-center justify-center py-2">
                <svg className="w-40 h-36 drop-shadow-[0_14px_24px_rgba(16,185,129,0.12)]" viewBox="0 0 180 160" fill="none">
                  <g transform="rotate(-7 90 82)">
                    {/* Soft Outer Glass Case */}
                    <rect x="46" y="10" width="88" height="142" rx="20" fill="#F1F5F9" stroke="#E2E8F0" strokeWidth="1.5" />
                    {/* Phone Dark Bezel & White Screen */}
                    <rect x="54" y="22" width="72" height="118" rx="14" fill="#FFFFFF" stroke="#475569" strokeWidth="3.5" />
                    {/* Top Speaker Notch */}
                    <rect x="81" y="29" width="18" height="3.5" rx="1.75" fill="#475569" />
                    {/* Center Green Cube / Box Emblem */}
                    <g transform="translate(74, 62)">
                      <path
                        d="M16 2L29 9.5V24.5L16 32L3 24.5V9.5L16 2Z"
                        stroke="#10B981"
                        strokeWidth="2.6"
                        strokeLinejoin="round"
                        fill="#ECFDF5"
                      />
                      <path d="M3 9.5L16 17L29 9.5" stroke="#10B981" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" />
                      <path d="M16 17V32" stroke="#10B981" strokeWidth="2.4" strokeLinecap="round" />
                    </g>
                    {/* Bottom Pagination Dots */}
                    <circle cx="83" cy="126" r="2" fill="#CBD5E1" />
                    <circle cx="90" cy="126" r="2" fill="#475569" />
                    <circle cx="97" cy="126" r="2" fill="#CBD5E1" />
                  </g>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Visual Lifestyle Showcase: Model Orang Jualan & Dwi-Ekosistem (Latar Lembut & Aksen Kaya Warna) */}
      <section className="py-14 sm:py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#f3faf6] via-[#f9fdfa] to-[#edf7f2] border-b border-emerald-100/80 relative overflow-hidden scroll-reveal opacity-0 translate-y-8 transition-all duration-700 ease-out">
        {/* Soft Ambient Light Glows */}
        <div className="absolute top-1/2 -left-20 w-80 h-80 rounded-full bg-emerald-200/25 blur-3xl pointer-events-none -z-10" />
        <div className="absolute top-10 right-10 w-80 h-80 rounded-full bg-amber-100/30 blur-3xl pointer-events-none -z-10" />

        <div className="max-w-6xl mx-auto relative z-10">
          <div className="text-center max-w-2xl mx-auto mb-10">
            <span className="type-badge text-emerald-800 bg-emerald-100/90 px-3.5 py-1.5 rounded-full border border-emerald-200 shadow-2xs inline-block">
              Dua peluang bisnis, satu ekosistem
            </span>
            <h2 className="type-h2 mt-4">
              Jualan online <span className="text-emerald-600">lebih santai</span>, toko offline makin ramai
            </h2>
            <p className="mt-3 type-body mx-auto">
              Bisa jualan santai dari kamar tidur tanpa modal stok, atau jalankan toko fisik dengan bantuan puluhan dropshipper aktif.
            </p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-8 items-stretch mt-6">
            {/* Card 1: Mitra Dropshipper (Pop-out Badge di Atas Card, Foto Bersih Tanpa Tumpukan Teks) */}
            <div className="relative rounded-[32px] bg-white border border-emerald-200/90 shadow-[0_12px_36px_rgba(16,185,129,0.08)] flex flex-col justify-between group hover:border-emerald-400 transition-all duration-300">
              {/* Floating Realtime Sales Notification (Menonjol di Atas Garis Card, Tidak Menutupi Foto) */}
              <div className="absolute -top-5 left-6 bg-white/95 backdrop-blur-md border border-emerald-200/90 shadow-lg rounded-2xl px-3.5 py-2.5 flex items-center gap-3 z-20">
                <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                <div>
                  <div className="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Pesanan Shopee</div>
                  <div className="text-xs font-bold text-emerald-700 font-mono">+Rp 48.000 Laba Bersih</div>
                </div>
              </div>

              {/* Image Banner */}
              <div className="relative h-72 sm:h-80 w-full overflow-hidden rounded-t-[32px] bg-emerald-50">
                <img 
                  src="/images/model-rebahan-phone.jpg" 
                  alt="Mitra Dropshipper Jualan Santai dari Rumah" 
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-white via-white/10 to-transparent" />
              </div>

              {/* Content Details */}
              <div className="p-7 sm:p-9 flex-1 flex flex-col justify-between">
                <div>
                  <div className="mb-2">
                    <span className="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 text-[10px] font-semibold border border-rose-200 inline-block">
                      🔥 Paling Diminati
                    </span>
                  </div>
                  <h3 className="text-2xl sm:text-3xl font-bold text-slate-900 mt-2 tracking-tight leading-snug">
                    Penghasilan Mengalir dari Kos atau Rumah
                  </h3>
                  <p className="text-xs sm:text-sm text-slate-600 mt-3 leading-relaxed">
                    Pilih produk dari katalog stok realtime toko fisik, pasang di Shopee, TikTok Shop, atau WhatsApp. Saat ada pesanan, masukkan nomor resinya. Tim gudang kami yang mengemas bubble wrap dan menyerahkan ke kurir!
                  </p>
                </div>

                <div className="mt-8 pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                  <div className="text-xs text-slate-600 flex items-center gap-1.5">
                    <span>Potensi Margin:</span>
                    <strong className="text-amber-800 font-bold bg-amber-100/80 px-2.5 py-0.5 rounded-md border border-amber-200">
                      Hingga 50% / Item
                    </strong>
                  </div>
                  <a
                    href="/register/dropshipper" target="_blank" rel="noopener noreferrer"
                    className="px-7 py-3 rounded-full bg-emerald-600 hover:bg-slate-900 text-white text-sm font-bold shadow-md hover:shadow-emerald-900/25 transition-all duration-300 hover:scale-105 active:scale-95 inline-flex items-center justify-center cursor-pointer"
                  >
                    <span>Mulai Dropship</span>
                  </a>
                </div>
              </div>
            </div>

            {/* Card 2: Pemilik Toko / Gudang (Pop-out Badge di Atas Card, Foto Bersih Tanpa Tumpukan Teks) */}
            <div className="relative rounded-[32px] bg-slate-950 text-white border border-slate-800 shadow-[0_12px_36px_rgba(0,0,0,0.25)] flex flex-col justify-between group hover:border-emerald-500/50 transition-all duration-300">
              {/* Floating Live Transaction Badge (Menonjol di Atas Garis Card, Tidak Menutupi Foto) */}
              <div className="absolute -top-5 left-6 bg-slate-900/95 backdrop-blur-md border border-slate-700 text-white shadow-xl rounded-2xl px-3.5 py-2.5 flex items-center gap-3 z-20">
                <span className="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></span>
                <div>
                  <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Kasir Toko Fisik</div>
                  <div className="text-xs font-bold text-white font-mono">Rp 506.000 Terbayar</div>
                </div>
              </div>

              {/* Image Banner */}
              <div className="relative h-72 sm:h-80 w-full overflow-hidden rounded-t-[32px] bg-slate-900">
                <img 
                  src="/images/model-warehouse-packing.jpg" 
                  alt="Owner Toko Fisik & Tim Gudang Packing" 
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />
              </div>

              {/* Content Details */}
              <div className="p-7 sm:p-9 flex-1 flex flex-col justify-between">
                <div>
                  <div className="mb-2">
                    <span className="px-2.5 py-1 rounded-full bg-cyan-950/80 text-cyan-300 text-[10px] font-semibold border border-cyan-800 inline-block">
                      ⚡ Ekosistem POS
                    </span>
                  </div>
                  <h3 className="text-2xl sm:text-3xl font-bold text-white mt-2 tracking-tight leading-snug">
                    Kasir Toko Tetap Cepat, Puluhan Dropshipper Bantu Jualkan
                  </h3>
                  <p className="text-xs sm:text-sm text-slate-400 mt-3 leading-relaxed">
                    Operasional kasir offline toko fisik Anda berjalan lancar dengan pemindaian barcode cepat. Di saat yang sama, puluhan mitra dropshipper memasarkan stok mati rak Anda ke seluruh Indonesia secara aman tanpa takut selisih barang.
                  </p>
                </div>

                <div className="mt-8 pt-5 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
                  <div className="text-xs text-slate-400">
                    Antrean Packing: <strong className="text-emerald-400 font-bold bg-slate-900 px-2 py-0.5 rounded-md border border-slate-800">Otomatis Terorganisir</strong>
                  </div>
                  <a
                    href="/register/supplier" target="_blank" rel="noopener noreferrer"
                    className="px-7 py-3 rounded-full bg-white hover:bg-emerald-500 text-slate-900 hover:text-white text-sm font-bold shadow-md hover:shadow-emerald-500/30 transition-all duration-300 hover:scale-105 active:scale-95 inline-flex items-center justify-center cursor-pointer"
                  >
                    <span>Daftar Toko</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Interactive Live Preview Section (Simpel, Bersih, & Terpusat) */}
      <section id="preview" className="px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto pt-12 pb-16 scroll-reveal opacity-0 translate-y-8 transition-all duration-700 ease-out">
        <div className="rounded-3xl bg-white border border-slate-200/90 shadow-xl shadow-slate-200/50 overflow-hidden">
          {/* Header Bar: Tanpa Link Palsu, 3 Menu di Tengah */}
          <div className="px-6 py-5 bg-white border-b border-slate-100 flex justify-center">
            <div className="inline-flex items-center p-1 bg-slate-100/90 rounded-full border border-slate-200/70 text-xs flex-wrap justify-center gap-1">
              <button
                onClick={() => setActiveTab('catalog')}
                className={`px-5 py-2 rounded-full font-semibold transition-all duration-200 flex items-center gap-2 cursor-pointer ${
                  activeTab === 'catalog' 
                    ? 'bg-white text-emerald-700 shadow-sm' 
                    : 'text-slate-500 hover:text-slate-900'
                }`}
              >
                <Package className="w-3.5 h-3.5" />
                <span>Katalog Produk</span>
              </button>

              <button
                onClick={() => setActiveTab('pos')}
                className={`px-5 py-2 rounded-full font-semibold transition-all duration-200 flex items-center gap-2 cursor-pointer ${
                  activeTab === 'pos' 
                    ? 'bg-white text-emerald-700 shadow-sm' 
                    : 'text-slate-500 hover:text-slate-900'
                }`}
              >
                <Store className="w-3.5 h-3.5" />
                <span>Kasir POS Toko</span>
              </button>

              <button
                onClick={() => setActiveTab('packing')}
                className={`px-5 py-2 rounded-full font-semibold transition-all duration-200 flex items-center gap-2 cursor-pointer ${
                  activeTab === 'packing' 
                    ? 'bg-white text-emerald-700 shadow-sm' 
                    : 'text-slate-500 hover:text-slate-900'
                }`}
              >
                <Truck className="w-3.5 h-3.5" />
                <span>Antrean Packing</span>
              </button>
            </div>
          </div>

          {/* Canvas Content */}
          <div className="p-6 sm:p-10 bg-slate-50/40">
            {/* Ringkasan Statistik Simpel (Minimalis 1 Baris per Item) */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
              <div className="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                  <Package className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-[11px] text-slate-500 font-medium">Stok Toko</div>
                  <div className="text-base font-bold text-slate-900">681 Unit</div>
                </div>
              </div>

              <div className="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                  <DollarSign className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-[11px] text-slate-500 font-medium">Rata-rata Margin</div>
                  <div className="text-base font-bold text-emerald-600">45%</div>
                </div>
              </div>

              <div className="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                  <Clock className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-[11px] text-slate-500 font-medium">Kirim Hari Ini</div>
                  <div className="text-base font-bold text-slate-900">4 Paket</div>
                </div>
              </div>

              <div className="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                  <Printer className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-[11px] text-slate-500 font-medium">Printer Kasir</div>
                  <div className="text-base font-bold text-slate-900">58mm Siap</div>
                </div>
              </div>
            </div>

            {/* TAB 1: Katalog Produk Siap Jual */}
            {activeTab === 'catalog' && (
              <div className="space-y-8">
                {/* Header Katalog Ringkas */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                  <div>
                    <h3 className="type-h3">
                      Produk siap jual
                    </h3>
                    <p className="text-sm text-slate-500 mt-0.5">
                      Stok asli gudang supplier dengan margin langsung cair.
                    </p>
                  </div>

                  <a 
                    href="/register/dropshipper" target="_blank" rel="noopener noreferrer"
                    className="px-5 py-2.5 rounded-full bg-slate-900 hover:bg-emerald-600 text-white text-xs font-semibold transition-all duration-200 shadow-xs inline-flex items-center justify-center self-start sm:self-auto shrink-0"
                  >
                    <span>Lihat Semua</span>
                  </a>
                </div>

                {/* Grid 3 Produk dengan Desain Minimalis & Bersih */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                  {sampleProducts.map((p) => (
                    <div 
                      key={p.id}
                      className="group rounded-2xl bg-white border border-slate-200/80 hover:border-emerald-300 hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col justify-between"
                    >
                      {/* Foto Full-Bleed Minimalis */}
                      <div className="relative h-48 w-full overflow-hidden bg-slate-100">
                        <img 
                          src={p.image} 
                          alt={p.name} 
                          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        {p.trending && (
                          <span className="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-amber-500 text-white text-[10px] font-semibold shadow-sm">
                            Terlaris
                          </span>
                        )}
                        <span className="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-xs text-slate-700 text-[10px] font-semibold">
                          Stok {p.stock}
                        </span>
                      </div>

                      {/* Konten Minimalis */}
                      <div className="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                          <div className="text-[11px] font-medium text-slate-400 uppercase tracking-wider">
                            {p.category}
                          </div>
                          <h4 className="text-base font-semibold text-slate-900 mt-1 line-clamp-1 group-hover:text-emerald-700 transition">
                            {p.name}
                          </h4>
                          <div className="mt-3 flex items-baseline justify-between text-xs">
                            <span className="text-slate-500">
                              Modal <strong className="text-slate-800 font-semibold">Rp {p.cost.toLocaleString('id-ID')}</strong>
                            </span>
                            <span className="text-emerald-600 font-semibold">
                              +Rp {p.margin.toLocaleString('id-ID')} laba
                            </span>
                          </div>
                        </div>

                        <a
                          href="/register/dropshipper" target="_blank" rel="noopener noreferrer"
                          className="w-full py-2.5 px-4 rounded-full bg-slate-900 hover:bg-emerald-600 text-white text-xs font-semibold transition-colors text-center"
                        >
                          Jual Produk
                        </a>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* TAB 2: Kasir POS Offline */}
            {activeTab === 'pos' && (
              <div className="p-6 rounded-2xl bg-white border border-slate-200 space-y-5">
                <div className="flex items-center justify-between border-b border-slate-100 pb-4">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                      <Store className="w-5 h-5" />
                    </div>
                    <div>
                      <h4 className="text-base font-bold text-slate-900">Layar Kasir POS Kasir Toko Fisik Cepat</h4>
                      <p className="text-xs text-slate-500">Scan Barcode, Multi-Level Harga (Eceran &amp; Grosir), dan Cetak Struk Instan</p>
                    </div>
                  </div>
                  <span className="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    REALTIME POS
                  </span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div className="md:col-span-2 space-y-2.5">
                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                      <div className="flex items-center gap-2.5">
                        <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span className="font-bold text-slate-800">Lego Robot Mini Transformer</span>
                        <span className="text-[11px] text-slate-500 font-mono">x2 pcs (Grosir Lusin)</span>
                      </div>
                      <span className="font-mono font-bold text-slate-900">Rp 90.000</span>
                    </div>

                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                      <div className="flex items-center gap-2.5">
                        <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span className="font-bold text-slate-800">Squishy Glitter Mainan Anak</span>
                        <span className="text-[11px] text-slate-500 font-mono">x1 pcs (Eceran)</span>
                      </div>
                      <span className="font-mono font-bold text-slate-900">Rp 32.000</span>
                    </div>
                  </div>

                  <div className="p-5 rounded-xl bg-emerald-50/70 border border-emerald-200 flex flex-col justify-between">
                    <div>
                      <span className="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Total Tagihan Kasir:</span>
                      <div className="text-2xl font-bold text-emerald-800 mt-1">Rp 122.000</div>
                      <span className="text-[11px] text-emerald-600 mt-1 block">Metode: Tunai / QRIS / Transfer</span>
                    </div>

                    <button 
                      type="button" 
                      className="mt-4 w-full py-2.5 bg-emerald-600 text-white font-semibold rounded-full text-xs hover:bg-emerald-500 transition shadow-[0_2px_10px_rgba(5,150,105,0.25)]"
                    >
                      Bayar &amp; Cetak Struk
                    </button>
                  </div>
                </div>
              </div>
            )}

            {/* TAB 3: Antrean Packing */}
            {activeTab === 'packing' && (
              <div className="p-6 rounded-2xl bg-white border border-slate-200 space-y-5">
                <div className="flex items-center justify-between border-b border-slate-100 pb-4">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                      <Truck className="w-5 h-5" />
                    </div>
                    <div>
                      <h4 className="text-base font-bold text-slate-900">Antrean Packing Gudang &amp; Resi Pengiriman</h4>
                      <p className="text-xs text-slate-500">Pesanan dropshipper langsung masuk ke antrean gudang toko untuk approval &amp; packing</p>
                    </div>
                  </div>
                  <span className="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    LIVE DISPATCH
                  </span>
                </div>

                <div className="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                  <div className="flex items-center justify-between text-xs">
                    <div className="flex items-center gap-2">
                      <span className="font-mono font-bold text-slate-900">DPS-6S9QJ2LI</span>
                      <span className="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                        MENUNGGU APPROVAL
                      </span>
                    </div>
                    <span className="font-mono text-slate-600 text-[11px]">Resi: Jp2303843275 (J&amp;T)</span>
                  </div>

                  <div className="grid grid-cols-2 gap-2 text-xs border-y border-slate-200/80 py-2.5">
                    <div>
                      <span className="text-[10px] text-slate-400 font-bold block">Penerima:</span>
                      <div className="font-bold text-slate-800">Adi (Garut)</div>
                    </div>
                    <div className="text-right">
                      <span className="text-[10px] text-slate-400 font-bold block">Mitra Dropshipper:</span>
                      <div className="font-bold text-emerald-700">Toko Berkah Toys</div>
                    </div>
                  </div>

                  <div className="flex items-center justify-end gap-2 pt-1">
                    <button type="button" className="px-4 py-2 rounded-full bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-500 transition shadow-xs">
                      Approve &amp; Teruskan ke Gudang
                    </button>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </section>

      {/* Feature Deep Dive Section (Latar Gradien Lembut & Aksen Warna per Fitur) */}
      <section id="fitur" className="py-14 sm:py-16 bg-gradient-to-b from-[#f2f9f5] via-white to-[#edf7f2] border-y border-emerald-100/90 relative overflow-hidden scroll-reveal opacity-0 translate-y-8 transition-all duration-700 ease-out">
        {/* Soft Ambient Colorful Blobs for High-End Depth */}
        <div className="absolute top-1/4 -right-24 w-96 h-96 rounded-full bg-emerald-100/40 blur-3xl pointer-events-none -z-10" />
        <div className="absolute bottom-1/4 -left-24 w-96 h-96 rounded-full bg-teal-100/40 blur-3xl pointer-events-none -z-10" />

        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center max-w-2xl mx-auto mb-10">
            <span className="type-badge text-emerald-800 bg-emerald-100/90 border border-emerald-200 px-3.5 py-1.5 rounded-full shadow-2xs inline-block">
              Fitur &amp; solusi lengkap
            </span>
            <h2 className="type-h2 mt-4">
              Semua fitur yang Anda butuhkan untuk <span className="text-emerald-600">skala bisnis</span>
            </h2>
            <p className="mt-4 type-body mx-auto">
              Mulai dari kasir offline toko fisik, manajemen stok grosir, hingga sistem fulfillment otomatis untuk dropshipper online.
            </p>
          </div>

          {/* Interactive Feature Category Pills dengan Indikator Warna Tematik */}
          <div className="flex items-center justify-center gap-2.5 overflow-x-auto pb-4 no-scrollbar mb-12">
            {featureCategories.map((cat) => {
              const IconComp = cat.icon;
              const isActive = activeFeatureCategory === cat.id;
              return (
                <button
                  key={cat.id}
                  onClick={() => setActiveFeatureCategory(cat.id)}
                  className={`px-5 py-2.5 rounded-full text-xs font-semibold transition-all duration-200 flex items-center gap-2 whitespace-nowrap cursor-pointer ${
                    isActive
                      ? `${cat.activeClass} scale-[1.03]`
                      : 'bg-white text-slate-600 border border-slate-200/80 hover:border-slate-300 hover:bg-slate-50'
                  }`}
                >
                  <span className={`w-2 h-2 rounded-full ${isActive ? 'bg-white' : cat.dotClass}`} />
                  <IconComp className="w-3.5 h-3.5 stroke-[1.8]" />
                  <span>{cat.label}</span>
                </button>
              );
            })}
          </div>

          {/* Active Feature Showcase Box */}
          <div className="rounded-3xl bg-white border border-slate-200/90 p-8 sm:p-12 shadow-[0_16px_40px_rgba(15,23,42,0.06)]">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
              <div className="space-y-6">
                <span className={`text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-md border ${currentFeature.badgeBg}`}>
                  {currentFeature.badge}
                </span>
                <h3 className="text-2xl sm:text-3xl font-bold text-slate-900 leading-tight">
                  {currentFeature.title}
                </h3>
                <p className="text-sm sm:text-base text-slate-600 leading-relaxed">
                  {currentFeature.desc}
                </p>

                <div className="space-y-3.5 pt-2">
                  {currentFeature.points.map((pt, i) => (
                    <div key={i} className="flex items-start gap-3">
                      <div className="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                        <Check className="w-3.5 h-3.5 font-bold" />
                      </div>
                      <span className="text-xs sm:text-sm text-slate-700 font-medium leading-relaxed">
                        {pt}
                      </span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Dynamic Feature Visual Showcase (Berbeda-beda dan Spesifik per Fitur) */}
              <div className="p-6 sm:p-8 rounded-3xl bg-slate-50/80 border border-slate-200/90 relative overflow-hidden">
                {/* 1. VISUAL KASIR POS & STRUK */}
                {activeFeatureCategory === 'pos' && (
                  <div className="space-y-4">
                    {/* Header Status Kasir & Printer */}
                    <div className="flex items-center justify-between p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                      <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                          <Printer className="w-4 h-4" />
                        </div>
                        <div>
                          <div className="text-xs font-bold text-slate-900">Printer Struk Bluetooth</div>
                          <div className="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                            <span>Tersambung (58mm &amp; 80mm)</span>
                          </div>
                        </div>
                      </div>
                      <span className="px-2.5 py-1 rounded-full bg-slate-100 text-[10px] font-bold text-slate-600">
                        Scan Barcode Aktif
                      </span>
                    </div>

                    {/* Struk Kasir Visual Card */}
                    <div className="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3 font-mono text-xs">
                      <div className="text-center border-b border-dashed border-slate-200 pb-2.5 font-sans">
                        <div className="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center mx-auto text-xs">
                          OP
                        </div>
                        <div className="font-bold text-slate-900 text-xs mt-1">OMNIPOS STORE RESMI</div>
                        <div className="text-[10px] text-slate-400">Jl. Gudang Pusat No. 10 Jakarta</div>
                      </div>

                      <div className="space-y-1.5 text-[11px] text-slate-700">
                        <div className="flex justify-between">
                          <span>1x Squishy Glitter Mainan</span>
                          <span className="font-bold">Rp 32.000</span>
                        </div>
                        <div className="flex justify-between">
                          <span>2x Kaos Oversize Cotton</span>
                          <span className="font-bold">Rp 170.000</span>
                        </div>
                      </div>

                      <div className="border-t border-dashed border-slate-200 pt-2 space-y-1 text-[11px]">
                        <div className="flex justify-between font-bold text-slate-900">
                          <span>TOTAL BELANJA</span>
                          <span className="text-emerald-700 font-bold">Rp 202.000</span>
                        </div>
                        <div className="flex justify-between text-slate-500 text-[10px]">
                          <span>Metode: QRIS / Tunai</span>
                          <span className="text-emerald-600 font-bold">LUNAS</span>
                        </div>
                      </div>
                    </div>

                    {/* Badge Keunggulan Lugas */}
                    <div className="grid grid-cols-2 gap-2 text-center text-xs font-sans">
                      <div className="p-2.5 rounded-xl bg-emerald-100/70 text-emerald-800 font-bold border border-emerald-200">
                        Scan Kilat 0.2 Detik
                      </div>
                      <div className="p-2.5 rounded-xl bg-teal-100/70 text-teal-800 font-bold border border-teal-200">
                        Cetak Logo Toko Bulat
                      </div>
                    </div>
                  </div>
                )}

                {/* 2. VISUAL INVENTORI & MULTI-LEVEL */}
                {activeFeatureCategory === 'inventory' && (
                  <div className="space-y-4">
                    {/* Header Produk & Stok */}
                    <div className="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                      <div className="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                        <img src="/images/product-tshirt.jpg" alt="Kaos" className="w-full h-full object-cover" />
                      </div>
                      <div className="min-w-0 flex-1">
                        <div className="text-xs font-bold text-slate-900 truncate">Kaos Oversize Cotton Combed</div>
                        <div className="flex items-center gap-2 mt-0.5">
                          <span className="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold border border-emerald-100">
                            Stok Toko: 185 Unit
                          </span>
                          <span className="text-[10px] text-slate-400">Sinkron Realtime</span>
                        </div>
                      </div>
                    </div>

                    {/* Multi-Level Pricing Cards */}
                    <div className="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
                      <div className="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Tingkatan Harga Grosir Otomatis:
                      </div>

                      <div className="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div>
                          <span className="font-bold text-slate-800">Tingkat 1: Eceran</span>
                          <span className="text-[10px] text-slate-400 block">Jumlah 1 - 5 pcs</span>
                        </div>
                        <span className="font-bold text-slate-900">Rp 85.000 / pcs</span>
                      </div>

                      <div className="flex items-center justify-between p-2 rounded-xl bg-emerald-50/60 border border-emerald-200 text-xs">
                        <div>
                          <span className="font-bold text-emerald-900">Tingkat 2: Grosir Lusin</span>
                          <span className="text-[10px] text-emerald-700 block">Jumlah 6 - 23 pcs</span>
                        </div>
                        <span className="font-bold text-emerald-700">Rp 65.000 / pcs</span>
                      </div>

                      <div className="flex items-center justify-between p-2 rounded-xl bg-teal-50/60 border border-teal-200 text-xs">
                        <div>
                          <span className="font-bold text-teal-900">Tingkat 3: Grosir Dus</span>
                          <span className="text-[10px] text-teal-700 block">Jumlah ≥ 24 pcs</span>
                        </div>
                        <span className="font-bold text-teal-700">Rp 55.000 / pcs</span>
                      </div>
                    </div>

                    <div className="p-2.5 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between">
                      <span>Restock Massal Excel/CSV</span>
                      <span className="font-bold text-emerald-600">1-Klik Terupdate</span>
                    </div>
                  </div>
                )}

                {/* 3. VISUAL KATALOG DROPSHIPPER */}
                {activeFeatureCategory === 'dropship' && (
                  <div className="space-y-4">
                    <div className="flex items-center justify-between text-xs font-bold text-slate-800 px-1">
                      <span>Katalog Siap Jual (Tanpa Beli Stok)</span>
                      <span className="text-emerald-700 text-[10px] bg-emerald-100 px-2 py-0.5 rounded-full font-bold">1.200+ Pilihan</span>
                    </div>

                    {/* 2 Product Cards with HD Photos */}
                    <div className="grid grid-cols-2 gap-3">
                      <div className="p-2.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between">
                        <div>
                          <div className="h-28 w-full rounded-xl overflow-hidden bg-slate-100 relative">
                            <img src="/images/product-tshirt.jpg" alt="Kaos" className="w-full h-full object-cover" />
                            <span className="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-white/90 text-[9px] font-bold text-slate-800">
                              Fashion
                            </span>
                          </div>
                          <div className="text-[11px] font-bold text-slate-900 mt-2 truncate">Kaos Oversize Pria</div>
                          <div className="text-[10px] text-slate-500 mt-0.5">Modal: Rp 35.000</div>
                        </div>
                        <div className="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between">
                          <span className="text-[10px] font-semibold text-emerald-700">+Rp 50rb Laba</span>
                          <span className="text-[9px] px-2 py-0.5 rounded bg-emerald-600 text-white font-bold">Jual</span>
                        </div>
                      </div>

                      <div className="p-2.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between">
                        <div>
                          <div className="h-28 w-full rounded-xl overflow-hidden bg-slate-100 relative">
                            <img src="/images/product-tws.jpg" alt="TWS" className="w-full h-full object-cover" />
                            <span className="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-white/90 text-[9px] font-bold text-slate-800">
                              Gadget
                            </span>
                          </div>
                          <div className="text-[11px] font-bold text-slate-900 mt-2 truncate">TWS Wireless Earbuds</div>
                          <div className="text-[10px] text-slate-500 mt-0.5">Modal: Rp 45.000</div>
                        </div>
                        <div className="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between">
                          <span className="text-[10px] font-semibold text-emerald-700">+Rp 54rb Laba</span>
                          <span className="text-[9px] px-2 py-0.5 rounded bg-emerald-600 text-white font-bold">Jual</span>
                        </div>
                      </div>
                    </div>

                    <div className="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-center justify-between">
                      <span className="font-semibold">Foto HD &amp; Deskripsi Siap Promosi</span>
                      <span className="font-bold text-emerald-700">Tersedia</span>
                    </div>
                  </div>
                )}

                {/* 4. VISUAL ANTREAN PACKING & RESI */}
                {activeFeatureCategory === 'packing' && (
                  <div className="space-y-4">
                    <div className="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
                      <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                          <Truck className="w-4 h-4" />
                        </div>
                        <div>
                          <div className="text-xs font-bold text-slate-900">Alur Packing Gudang</div>
                          <div className="text-[10px] text-emerald-600 font-semibold">SLA Cepat &lt; 24 Jam</div>
                        </div>
                      </div>
                      <span className="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                        Bebas Salah Kirim
                      </span>
                    </div>

                    {/* 3 Step Alur Pengemasan */}
                    <div className="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3 text-xs">
                      <div className="flex items-center gap-3">
                        <div className="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0">
                          1
                        </div>
                        <div className="min-w-0 flex-1">
                          <div className="font-bold text-slate-900">Verifikasi Resi Marketplace</div>
                          <div className="text-[10px] text-slate-400">Nomor resi dicek sistem sebelum packing</div>
                        </div>
                        <span className="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Valid</span>
                      </div>

                      <div className="flex items-center gap-3">
                        <div className="w-6 h-6 rounded-full bg-amber-500 text-white font-bold flex items-center justify-center text-[10px] shrink-0">
                          2
                        </div>
                        <div className="min-w-0 flex-1">
                          <div className="font-bold text-slate-900">Meja Packing Gudang</div>
                          <div className="text-[10px] text-slate-400">Kemasan rapi standar bubble wrap</div>
                        </div>
                        <span className="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Dipacking</span>
                      </div>

                      <div className="flex items-center gap-3">
                        <div className="w-6 h-6 rounded-full bg-teal-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0">
                          3
                        </div>
                        <div className="min-w-0 flex-1">
                          <div className="font-bold text-slate-900">Serah Terima Kurir Ekspedisi</div>
                          <div className="text-[10px] text-slate-400">Scan pickup kurir &amp; resi aktif terlacak</div>
                        </div>
                        <span className="text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded">Selesai</span>
                      </div>
                    </div>

                    <div className="p-2.5 rounded-xl bg-slate-100 text-[11px] text-slate-600 flex items-center justify-between">
                      <span>Jika pesanan dibatalkan pembeli:</span>
                      <span className="font-bold text-emerald-700">Stok Otomatis Kembali</span>
                    </div>
                  </div>
                )}

                {/* 5. VISUAL LABA RUGI & KEUANGAN */}
                {activeFeatureCategory === 'finance' && (
                  <div className="space-y-4">
                    {/* Header Dompet Saldo */}
                    <div className="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
                      <div>
                        <div className="text-[10px] font-bold uppercase tracking-wider text-slate-400">Saldo Aktif Tersedia</div>
                        <div className="text-xl font-bold text-slate-900 mt-0.5 font-mono">Rp 4.850.000</div>
                      </div>
                      <a 
                        href="/register/dropshipper" target="_blank" rel="noopener noreferrer"
                        className="px-4 py-2 rounded-full bg-emerald-600 text-white text-xs font-bold shadow-sm hover:bg-emerald-500 transition"
                      >
                        Tarik Saldo
                      </a>
                    </div>

                    {/* Breakdown Bagi Hasil Transparan */}
                    <div className="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2.5 text-xs">
                      <div className="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Alur Bagi Hasil Otomatis per Pesanan:
                      </div>

                      <div className="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <span className="text-slate-600">Total Pembeli Bayar</span>
                        <span className="font-bold text-slate-900">Rp 150.000</span>
                      </div>

                      <div className="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <span className="text-slate-600">Bagian Toko Supplier (Modal HPP)</span>
                        <span className="font-bold text-slate-700">Rp 95.000</span>
                      </div>

                      <div className="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900">
                        <span className="font-bold">Komisi Bersih Dropshipper</span>
                        <span className="font-bold text-emerald-700 text-sm font-mono">+Rp 55.000</span>
                      </div>
                    </div>

                    <div className="p-2.5 rounded-xl bg-emerald-100/60 text-emerald-900 text-[11px] font-bold text-center border border-emerald-200">
                      Pembagian Uang Transparan &amp; Otomatis Cair
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Section Artikel & Literasi Jualan Online (Dropship.io Editorial Layout) */}
      <section id="simulasi" className="py-14 sm:py-20 px-4 sm:px-6 lg:px-8 bg-white border-t border-slate-100 scroll-reveal opacity-0 translate-y-8 transition-all duration-700 ease-out">
        <span id="kalkulator" className="sr-only">Artikel</span>
        <div className="max-w-6xl mx-auto">
          {/* Top Pill & Large Editorial Heading */}
          <div className="mb-8 sm:mb-10">
            <div className="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-xs mb-4">
              <span className="font-bold text-slate-900">4.8/5 Rating</span>
              <span className="text-slate-300">|</span>
              <span className="font-semibold text-emerald-600">1.200+ pembaca aktif</span>
            </div>
            <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
              Bacaan &amp; Artikel Terbaru
            </h2>
          </div>

          {(() => {
            const articles = [
              {
                date: '16 November 2026',
                readTime: '9 menit baca',
                title: 'Membedakan Sinyal Tren Iklan vs Permintaan Pasar yang Nyata',
                overlayTitle: 'Estimasi Penjualan & Akurasi Permintaan',
                image: 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1000&q=80',
                excerpt:
                  'Keramaian konten viral dan laju transaksi aktual menjawab dua hal yang berbeda; memahami pembacaan dua sinyal ini membantu penjual menguji produk sebelum menambah anggaran stok maupun promosi....',
                body: [
                  'Dalam perdagangan online modern, produk yang sering muncul di beranda media sosial belum tentu menghasilkan konversi pembelian yang stabil. Penjual yang matang selalu memisahkan antara metrik impresi sesaat dengan kecepatan barang keluar dari gudang dari minggu ke minggu.',
                  'Skema dropship memungkinkan penguji pasar memvalidasi permintaan tersebut tanpa harus mengendapkan modal pada ratusan unit barang di awal. Saat rasio pembelian berulang dan margin per unit sudah terbukti sehat, barulah skala pemasaran diperbesar secara terukur.'
                ]
              },
              {
                date: '12 November 2026',
                readTime: '14 menit baca',
                title: 'Analitik Etalase Marketplace: Metrik Konversi dan Pola Belanja',
                overlayTitle: 'Metrik Konversi & Kecepatan Pemenuhan Order',
                image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1000&q=80',
                excerpt:
                  'Enam indikator utama performa toko online, mulai dari rasio klik ke keranjang hingga ketepatan waktu pengemasan, membantu penjual mendiagnosis kesehatan etalase dan membaca permintaan kompetitor....',
                body: [
                  'Keputusan membeli di marketplace sangat dipengaruhi oleh tiga faktor utama: kejelasan visual produk, ketersediaan stok siap kirim, dan reputasi kecepatan pengiriman toko. Keterlambatan menyerahkan paket ke kurir sering kali menurunkan visibilitas produk di halaman pencarian.',
                  'Ketika alur penerimaan pesanan di toko online terhubung langsung dengan kesiapan stok fisik di rak gudang penyuplai, penjual dapat menjaga tingkat keberhasilan pengiriman tetap tinggi sembari fokus mengoptimalkan judul, foto, dan pelayanan pelanggan.'
                ]
              },
              {
                date: '6 November 2026',
                readTime: '11 menit baca',
                title: 'Manajemen Arus Kas Ritel: Menjaga Margin Tanpa Risiko Stok Mengendap',
                overlayTitle: 'Struktur Harga Grosir & Perputaran Kas',
                image: 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1000&q=80',
                excerpt:
                  'Banyak toko ritel pemula tersendat bukan karena kurangnya pembeli, melainkan karena modal kerja terkunci pada barang yang lambat berputar; memahami struktur harga bertingkat menjaga arus kas tetap cair....',
                body: [
                  'Perbedaan mendasar antara model stok mandiri dan pemenuhan berbasis mitra gudang terletak pada titik keluarnya kas. Pada ritel konvensional, biaya barang dibayar penuh sebelum ada kepastian pembeli, sehingga risiko barang tidak laku ditanggung sepenuhnya oleh penjual.',
                  'Dengan memanfaatkan selisih antara harga pemasok dan harga eceran pasar, setiap transaksi yang selesai langsung menyisakan laba bersih tanpa beban penyusutan barang maupun biaya sewa ruang penyimpanan.'
                ]
              }
            ];

            const activeArticle = articles[activeArticleIndex] || articles[0];

            return (
              <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                {/* Left Column: Soft Framed Featured Article Image & Reader */}
                <div className="lg:col-span-6">
                  <div className="p-3.5 sm:p-5 rounded-[28px] bg-slate-100/90 border border-slate-200/70">
                    <div className="relative rounded-2xl overflow-hidden aspect-[16/11] bg-slate-900">
                      <img
                        src={activeArticle.image}
                        alt={activeArticle.title}
                        className="w-full h-full object-cover opacity-85 transition-all duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/25 to-transparent" />

                      {/* Subtle Editorial Overlay Text (Matching Reference Image) */}
                      <div className="absolute inset-x-0 bottom-0 p-5 sm:p-7">
                        <span className="inline-block text-[11px] font-semibold uppercase tracking-wider text-emerald-300 mb-1.5">
                          {activeArticle.date} &bull; {activeArticle.readTime}
                        </span>
                        <h3 className="text-xl sm:text-2xl font-extrabold text-white leading-tight tracking-tight">
                          {activeArticle.overlayTitle}
                        </h3>
                      </div>
                    </div>

                    {/* Full Article Body Preview Underneath */}
                    <div className="mt-4 pt-4 border-t border-slate-200/80 px-1 space-y-2.5">
                      <h4 className="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                        {activeArticle.title}
                      </h4>
                      {activeArticle.body.map((paragraph, pIdx) => (
                        <p key={pIdx} className="text-xs sm:text-[13px] text-slate-600 leading-relaxed">
                          {paragraph}
                        </p>
                      ))}
                    </div>
                  </div>
                </div>

                {/* Right Column: Clean Stacked Article List */}
                <div className="lg:col-span-6 flex flex-col justify-between space-y-7 py-1">
                  {articles.map((article, idx) => {
                    const isSelected = idx === activeArticleIndex;
                    return (
                      <article
                        key={idx}
                        onClick={() => setActiveArticleIndex(idx)}
                        className={`group cursor-pointer rounded-2xl transition-colors duration-200 ${
                          isSelected ? 'bg-emerald-50/40 -mx-3 px-3 py-2.5' : 'hover:bg-slate-50/70 -mx-3 px-3 py-2.5'
                        }`}
                      >
                        <div className="text-xs font-medium text-slate-400">
                          {article.date} &bull; {article.readTime}
                        </div>
                        <h3
                          className={`mt-1.5 text-base sm:text-[18px] font-bold leading-snug tracking-tight transition-colors ${
                            isSelected ? 'text-emerald-700' : 'text-slate-900 group-hover:text-emerald-600'
                          }`}
                        >
                          {article.title}
                        </h3>
                        <p className="mt-2 text-xs sm:text-[13.5px] text-slate-500 leading-relaxed">
                          {article.excerpt}
                        </p>
                      </article>
                    );
                  })}
                </div>
              </div>
            );
          })()}
        </div>
      </section>

      {/* FAQ Accordion Section (Latar Bernuansa Sage / Mint Halus) */}
      <section id="faq" className="py-14 sm:py-16 bg-gradient-to-b from-[#f4fbf7] to-slate-50/90 border-t border-emerald-100/90 scroll-reveal opacity-0 translate-y-8 transition-all duration-700 ease-out">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-10">
            <h2 className="type-h2">
              Pertanyaan yang <span className="text-emerald-600">sering diajukan</span>
            </h2>
            <p className="mt-3 type-body mx-auto text-[#64748B]">
              Segala hal penting yang perlu Anda ketahui mengenai sistem POS kasir dan kemitraan dropship.
            </p>
          </div>

          <div className="space-y-4">
            {faqs.map((faq, idx) => {
              const isOpen = openFaq === idx;
              return (
                <div 
                  key={idx}
                  className="rounded-2xl bg-white border border-slate-200/90 shadow-xs overflow-hidden transition"
                >
                  <button
                    onClick={() => setOpenFaq(isOpen ? -1 : idx)}
                    className="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4 font-semibold text-sm sm:text-base text-slate-800 hover:text-emerald-700 transition cursor-pointer"
                  >
                    <span>{faq.q}</span>
                    <ChevronDown className={`w-4 h-4 text-emerald-600 shrink-0 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`} />
                  </button>

                  {isOpen && (
                    <div className="px-6 pb-6 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-4">
                      {faq.a}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Gradient CTA + Footer Ala Modern SaaS (Versi Hijau Emerald - Teal OmniPOS) */}
      <footer className="bg-gradient-to-b from-emerald-400 via-emerald-600 to-emerald-900 text-emerald-50 text-xs relative overflow-hidden">
        {/* Soft Radiant Top Glow */}
        <div className="absolute -top-24 left-1/2 -translate-x-1/2 w-[900px] h-[420px] bg-white/20 blur-3xl rounded-full pointer-events-none" />
        <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-teal-400/15 rounded-full blur-3xl pointer-events-none" />

        {/* Centered Big CTA Banner inside Top Gradient */}
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 text-center relative z-10">
          <div className="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md border border-white/35 shadow-xl flex items-center justify-center mx-auto mb-6">
            <img
              src="/images/logo-omnipos.png"
              alt="OmniPOS"
              className="h-10 w-10 object-contain rounded-xl"
            />
          </div>
          <h2 className="type-h2 !text-white">
            Siap mulai jualan?
          </h2>
          <p className="mt-3 type-body !text-emerald-50/90 mx-auto">
            Daftar gratis sekarang dan mulai jual ratusan produk pilihan langsung dari gudang supplier terpercaya.
          </p>
          <div className="mt-7 flex flex-wrap items-center justify-center gap-3">
            <a
              href="/register/dropshipper" target="_blank" rel="noopener noreferrer"
              className="px-8 py-3.5 rounded-full bg-white hover:bg-slate-900 text-emerald-900 hover:text-white type-btn shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 active:scale-95"
            >
              Mulai Gratis Sekarang
            </a>
            <a
              href="/register/supplier" target="_blank" rel="noopener noreferrer"
              className="px-7 py-3.5 rounded-full bg-white/15 hover:bg-white/25 text-white border border-white/30 backdrop-blur-xs type-btn transition-all duration-300 hover:scale-105 active:scale-95"
            >
              Daftarkan Toko
            </a>
          </div>
        </div>

        {/* Footer Links Columns Seamlessly Blended */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12 border-t border-white/15 relative z-10">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
            <div className="lg:col-span-2 space-y-5">
              <Link href="/" className="inline-flex items-center gap-3">
                <img 
                  src="/images/logo-omnipos.png" 
                  alt="OmniPOS" 
                  className="h-10 w-10 object-contain rounded-xl shadow-xs bg-white/90 p-0.5" 
                />
                <span className="text-2xl font-bold tracking-tight text-white">
                  Omni<span className="text-emerald-200">POS</span>
                </span>
              </Link>
              
              <p className="text-xs sm:text-sm text-emerald-50/85 leading-relaxed max-w-sm">
                Ekosistem platform cerdas menghubungkan kasir toko fisik offline dengan jaringan kemitraan dropship online realtime tanpa modal stok.
              </p>

              <div className="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white/15 backdrop-blur-xs border border-white/25 text-white text-xs font-semibold">
                <span className="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                <span>Seluruh Layanan Normal &amp; Stok Sinkron (99.9% Uptime)</span>
              </div>
            </div>

            <div>
              <h4 className="text-xs font-semibold uppercase tracking-wider text-white mb-5">Fitur Unggulan</h4>
              <ul className="space-y-3">
                <li className="flex items-center gap-2">
                  <a href="#fitur" className="hover:text-white text-emerald-50/85 transition">POS Kasir Toko</a>
                  <span className="px-1.5 py-0.5 rounded bg-white text-emerald-800 text-[9px] font-bold uppercase">Populer</span>
                </li>
                <li className="flex items-center gap-2">
                  <a href="#preview" className="hover:text-white text-emerald-50/85 transition">Katalog Dropship</a>
                  <span className="px-1.5 py-0.5 rounded bg-white text-emerald-800 text-[9px] font-bold uppercase">Realtime</span>
                </li>
                <li><a href="#fitur" className="hover:text-white text-emerald-50/85 transition">Tracker Pengiriman</a></li>
                <li className="flex items-center gap-2">
                  <a href="#fitur" className="hover:text-white text-emerald-50/85 transition">Direktori Toko Mitra</a>
                  <span className="px-1.5 py-0.5 rounded bg-white text-emerald-800 text-[9px] font-bold uppercase">Baru</span>
                </li>
                <li><a href="#simulasi" className="hover:text-white text-emerald-50/85 transition">Simulasi Keuntungan</a></li>
              </ul>
            </div>

            <div>
              <h4 className="text-xs font-semibold uppercase tracking-wider text-white mb-5">Akses &amp; Kemitraan</h4>
              <ul className="space-y-3">
                <li><a href="/register/dropshipper" target="_blank" rel="noopener noreferrer" className="text-white hover:text-emerald-200 font-semibold transition">Daftar Mitra Dropshipper</a></li>
                <li><a href="/register/supplier" target="_blank" rel="noopener noreferrer" className="hover:text-white text-emerald-50/85 transition">Daftar Toko / Supplier</a></li>
                <li><a href="/login" target="_blank" rel="noopener noreferrer" className="hover:text-white text-emerald-50/85 transition">Masuk Akun Pengguna</a></li>
                <li><span className="text-emerald-100/70">Garansi Retur Barang Aman</span></li>
              </ul>
            </div>

            <div>
              <h4 className="text-xs font-semibold uppercase tracking-wider text-white mb-5">Kontak &amp; Customer Service</h4>
              <div className="space-y-3 text-xs leading-relaxed text-emerald-50/85">
                <a 
                  href="https://wa.me/6283121782648?text=Halo%20Admin%20OmniPOS%2C%20saya%20ingin%20konsultasi%20layanan%20dropship%20dan%20toko" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="flex items-center gap-2 text-white hover:text-emerald-200 font-bold transition"
                >
                  <Phone className="w-3.5 h-3.5" />
                  <span>WhatsApp: +62 831-2178-2648</span>
                </a>
                <a 
                  href="mailto:maulana88aditya@gmail.com" 
                  className="flex items-center gap-2 hover:text-white transition"
                >
                  <Mail className="w-3.5 h-3.5" />
                  <span>maulana88aditya@gmail.com</span>
                </a>
                <p>Gudang Toko: Cita Laksana Toys, Garut &amp; DKI Jakarta, Indonesia</p>
                <p>Jam Kerja CS: 08.00 - 21.00 WIB (Setiap Hari)</p>
              </div>

              {/* Sosmed Footer */}
              <div className="mt-4 pt-3 border-t border-white/15 flex items-center gap-3 text-emerald-50/80 text-xs">
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" className="hover:text-white transition" title="Instagram">
                  Instagram
                </a>
                <span>&bull;</span>
                <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" className="hover:text-white transition" title="TikTok">
                  TikTok
                </a>
                <span>&bull;</span>
                <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" className="hover:text-white transition" title="YouTube">
                  YouTube
                </a>
              </div>
            </div>
          </div>

          <div className="mt-14 pt-8 border-t border-white/15 flex flex-col sm:flex-row items-center justify-between gap-4 text-emerald-100/80 text-xs">
            <div>&copy; {new Date().getFullYear()} <strong className="text-white">OmniPOS</strong> Indonesia. Hak Cipta Dilindungi Undang-Undang.</div>
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-emerald-200" />
              <span className="text-white font-semibold">Enkripsi Data Transaksi Aman</span>
            </div>
          </div>
        </div>
      </footer>

      {/* Floating WhatsApp Consultation Widget */}
      <div className="fixed bottom-6 right-6 z-50 flex items-center gap-3">
        {/* Tooltip Card (Desktop) */}
        <a
          href="https://wa.me/6283121782648?text=Halo%20Admin%20OmniPOS%2C%20saya%20ingin%20konsultasi%20layanan%20dropship%20dan%20kasir%20toko"
          target="_blank"
          rel="noopener noreferrer"
          className="hidden sm:flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-white/95 backdrop-blur-md border border-emerald-200/90 shadow-xl hover:shadow-2xl text-xs font-semibold text-slate-800 hover:border-emerald-400 transition-all duration-300 hover:scale-105 group"
        >
          <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" />
          <div className="text-left">
            <div className="text-[10px] text-slate-400 font-medium leading-none">Ada Pertanyaan?</div>
            <div className="text-xs font-bold text-slate-900 group-hover:text-emerald-700 transition mt-0.5">
              Konsultasi WhatsApp
            </div>
          </div>
        </a>

        {/* WhatsApp Round Button */}
        <a
          href="https://wa.me/6283121782648?text=Halo%20Admin%20OmniPOS%2C%20saya%20ingin%20konsultasi%20layanan%20dropship%20dan%20kasir%20toko"
          target="_blank"
          rel="noopener noreferrer"
          title="Chat WhatsApp CS OmniPOS (+62 831-2178-2648)"
          className="w-14 h-14 rounded-full bg-gradient-to-tr from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-500 text-white flex items-center justify-center shadow-[0_8px_25px_rgba(16,185,129,0.45)] hover:shadow-[0_12px_30px_rgba(16,185,129,0.6)] hover:scale-110 active:scale-95 transition-all duration-300 relative group cursor-pointer"
        >
          {/* Pulsing Outer Ring */}
          <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-30"></span>

          {/* WhatsApp SVG Icon */}
          <svg className="w-7 h-7 fill-current relative z-10" viewBox="0 0 24 24">
            <path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.83.498 3.548 1.365 5.024L2 22l5.084-1.332A9.975 9.975 0 0012.03 22c5.536 0 10.03-4.495 10.03-10.031S17.567 2 12.031 2zm0 18.337c-1.571 0-3.044-.438-4.307-1.2l-.308-.184-3.2.839.854-3.12-.202-.321A8.256 8.256 0 013.725 12.03c0-4.58 3.726-8.306 8.306-8.306 4.58 0 8.306 3.726 8.306 8.306s-3.726 8.307-8.306 8.307zm4.551-6.223c-.249-.125-1.476-.728-1.705-.811-.229-.083-.395-.125-.562.125-.166.249-.645.811-.79 1.019-.146.208-.291.229-.541.104-.249-.125-1.053-.388-2.006-1.238-.742-.662-1.243-1.48-1.389-1.729-.146-.249-.016-.384.109-.508.113-.112.249-.291.374-.437.125-.145.166-.249.249-.415.083-.166.042-.312-.021-.437-.062-.125-.562-1.353-.77-1.853-.203-.487-.41-.42-.562-.428l-.479-.008c-.166 0-.437.062-.666.312-.229.249-.874.854-.874 2.082 0 1.228.895 2.414 1.02 2.581.125.166 1.76 2.687 4.264 3.769.596.258 1.061.412 1.424.527.599.19 1.144.163 1.575.099.48-.072 1.476-.603 1.684-1.186.208-.583.208-1.082.146-1.186-.063-.104-.229-.166-.478-.291z" />
          </svg>
        </a>
      </div>
    </div>
  );
}
