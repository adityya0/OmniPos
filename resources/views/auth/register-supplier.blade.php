<x-guest-layout :split="true" title="Daftar Toko Supplier">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        {{-- Kolom Kiri: Form Daftar Toko / Supplier Simpel & Bersih --}}
        <div class="w-full lg:w-1/2 bg-white flex flex-col justify-center px-6 sm:px-14 lg:px-16 xl:px-24 py-12">
            <div class="w-full max-w-md mx-auto">
                {{-- Logo OmniPOS --}}
                <a href="/" class="inline-flex items-center gap-2.5 group">
                    <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-9 h-9 object-contain rounded-xl group-hover:scale-105 transition-transform">
                    <span class="font-bold text-xl tracking-tight text-slate-900">Omni<span class="text-emerald-600">POS</span></span>
                </a>

                {{-- Heading Singkat --}}
                <div class="mt-8">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                        Daftar Akun Supplier / Toko
                    </h1>
                    <p class="mt-2 text-sm text-slate-500">
                        Kelola kasir POS, stok gudang, dan distribusi dropshipper.
                    </p>
                </div>

                {{-- Toggle Jenis Akun --}}
                <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-xl mt-6 text-xs font-medium text-center">
                    <a href="{{ route('register.dropshipper') }}" class="py-2 px-3 text-slate-500 hover:text-slate-900 rounded-lg transition">
                        Dropshipper
                    </a>
                    <span class="py-2 px-3 bg-white text-emerald-700 rounded-lg shadow-2xs font-semibold">
                        Supplier / Toko
                    </span>
                </div>

                @php
                    $initialStep = ($errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('password_confirmation')) ? 2 : 1;
                @endphp

                {{-- Multi-Step Form Wrapper --}}
                <div class="mt-6" x-data="{
                    step: {{ $initialStep }},
                    storeName: '{{ old('store_name', '') }}',
                    goToStep2() {
                        const storeInput = document.getElementById('store_name');
                        const phoneInput = document.getElementById('contact_phone');
                        const addressInput = document.getElementById('warehouse_address');

                        if (!storeInput.checkValidity()) {
                            storeInput.reportValidity();
                            return;
                        }
                        if (!phoneInput.checkValidity()) {
                            phoneInput.reportValidity();
                            return;
                        }
                        if (!addressInput.checkValidity()) {
                            addressInput.reportValidity();
                            return;
                        }
                        this.step = 2;
                    }
                }">
                    {{-- Step Progress Indicator --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                            <button type="button" @click="step = 1" class="flex items-center gap-2 focus:outline-none cursor-pointer">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition duration-200"
                                      :class="step === 1 ? 'bg-emerald-600 text-white ring-4 ring-emerald-50' : 'bg-emerald-100 text-emerald-700'">
                                    <template x-if="step > 1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                    <template x-if="step === 1">
                                        <span>1</span>
                                    </template>
                                </span>
                                <span :class="step === 1 ? 'text-slate-900 font-semibold' : 'text-slate-500'">Info Toko</span>
                            </button>

                            <div class="flex-1 mx-3 h-0.5 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-emerald-600 transition-all duration-300"
                                     :style="step === 2 ? 'width: 100%' : 'width: 0%'"></div>
                            </div>

                            <button type="button" @click="goToStep2()" class="flex items-center gap-2 focus:outline-none cursor-pointer">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition duration-200"
                                      :class="step === 2 ? 'bg-emerald-600 text-white ring-4 ring-emerald-50' : 'bg-slate-100 text-slate-400'">
                                    2
                                </span>
                                <span :class="step === 2 ? 'text-slate-900 font-semibold' : 'text-slate-400'">Akun Pemilik</span>
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('register.supplier') }}">
                        @csrf

                        {{-- TAHAP 1: INFO TOKO & GUDANG --}}
                        <div x-show="step === 1"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-4">

                            <div>
                                <input
                                    id="store_name"
                                    type="text"
                                    name="store_name"
                                    x-model="storeName"
                                    value="{{ old('store_name') }}"
                                    required
                                    autofocus
                                    placeholder="Nama toko / brand"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                />
                                <x-input-error :messages="$errors->get('store_name')" class="mt-1" />
                            </div>

                            <div>
                                <input
                                    id="contact_phone"
                                    type="text"
                                    name="contact_phone"
                                    value="{{ old('contact_phone') }}"
                                    required
                                    placeholder="No. WhatsApp toko (08xxxxxxxxxx)"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                />
                                <x-input-error :messages="$errors->get('contact_phone')" class="mt-1" />
                            </div>

                            <div>
                                <textarea
                                    id="warehouse_address"
                                    name="warehouse_address"
                                    rows="3"
                                    required
                                    placeholder="Alamat lengkap lokasi gudang atau pengiriman"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                >{{ old('warehouse_address') }}</textarea>
                                <x-input-error :messages="$errors->get('warehouse_address')" class="mt-1" />
                            </div>

                            <div class="pt-2">
                                <button
                                    type="button"
                                    @click="goToStep2()"
                                    class="w-full py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-slate-900 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition-all duration-200 cursor-pointer"
                                >
                                    Lanjut
                                </button>
                            </div>
                        </div>

                        {{-- TAHAP 2: INFO AKUN PEMILIK --}}
                        <div x-show="step === 2"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-4">

                            <div class="flex items-center justify-between px-3.5 py-2.5 bg-emerald-50/70 border border-emerald-200/70 rounded-xl text-xs">
                                <div class="flex items-center gap-1.5 truncate">
                                    <span class="text-slate-500">Toko:</span>
                                    <span class="font-semibold text-emerald-900 truncate" x-text="storeName || 'Toko Baru'"></span>
                                </div>
                                <button type="button" @click="step = 1" class="text-emerald-700 hover:text-emerald-900 font-semibold underline shrink-0 ml-2 cursor-pointer">
                                    Ubah
                                </button>
                            </div>

                            <div>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    autocomplete="name"
                                    placeholder="Nama lengkap pemilik"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>

                            <div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="username"
                                    placeholder="Alamat email login"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div x-data="{ show: false }">
                                    <div class="relative">
                                        <input
                                            id="password"
                                            x-bind:type="show ? 'text' : 'password'"
                                            name="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Kata sandi (min. 8)"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                        />
                                        <button
                                            type="button"
                                            @click="show = !show"
                                            tabindex="-1"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                                            aria-label="Lihat / sembunyikan kata sandi"
                                        >
                                            <template x-if="!show">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </template>
                                            <template x-if="show">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                                </div>

                                <div x-data="{ show: false }">
                                    <div class="relative">
                                        <input
                                            id="password_confirmation"
                                            x-bind:type="show ? 'text' : 'password'"
                                            name="password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Ulangi sandi"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                        />
                                        <button
                                            type="button"
                                            @click="show = !show"
                                            tabindex="-1"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                                            aria-label="Lihat / sembunyikan konfirmasi sandi"
                                        >
                                            <template x-if="!show">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </template>
                                            <template x-if="show">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                                </div>
                            </div>

                            <div class="pt-2 flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="step = 1"
                                    class="w-1/3 py-3.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition cursor-pointer"
                                >
                                    Kembali
                                </button>

                                <button
                                    type="submit"
                                    class="w-2/3 py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-slate-900 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition-all duration-200 cursor-pointer"
                                >
                                    Daftar Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer link --}}
                <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
                    <span>Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="ml-1 font-semibold text-emerald-600 hover:text-emerald-700">
                        Masuk di sini
                    </a>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Trust Rating & Testimoni Mitra --}}
        <x-auth-split-showcase />
    </div>
</x-guest-layout>
