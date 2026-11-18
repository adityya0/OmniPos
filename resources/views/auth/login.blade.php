<x-guest-layout :split="true" title="Masuk">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        {{-- Kolom Kiri: Form Masuk Simpel & Bersih --}}
        <div class="w-full lg:w-1/2 bg-white flex flex-col justify-center px-6 sm:px-14 lg:px-16 xl:px-24 py-12">
            <div class="w-full max-w-md mx-auto">
                {{-- Logo OmniPOS --}}
                <a href="/" class="inline-flex items-center gap-2.5 group">
                    <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-9 h-9 object-contain rounded-xl group-hover:scale-105 transition-transform">
                    <span class="font-bold text-xl tracking-tight text-slate-900">Omni<span class="text-emerald-600">POS</span></span>
                </a>

                {{-- Heading Singkat & Jelas --}}
                <div class="mt-8">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                        Masuk ke OmniPOS
                    </h1>
                    <p class="mt-2 text-sm text-slate-500">
                        Kelola toko dan pesanan dropship dalam satu akun.
                    </p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mt-5" :status="session('status')" />

                {{-- Form Login --}}
                <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Alamat email"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    {{-- Password dengan Toggle Mata --}}
                    <div x-data="{ show: false }">
                        <div class="relative">
                            <input
                                id="password"
                                x-bind:type="show ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Kata sandi"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-11 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                            />
                            <button
                                type="button"
                                @click="show = !show"
                                tabindex="-1"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
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
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    {{-- Ingat Saya & Lupa Sandi --}}
                    <div class="flex items-center justify-between text-xs">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" name="remember">
                            <span class="ms-2 text-slate-600">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-slate-500 hover:text-emerald-700 font-medium transition">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    {{-- Tombol Masuk --}}
                    <button
                        type="submit"
                        class="w-full py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-slate-900 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition-all duration-200 cursor-pointer"
                    >
                        Masuk
                    </button>
                </form>

                {{-- Link Daftar Minimal --}}
                <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
                    <span>Belum punya akun?</span>
                    <div class="mt-2 flex items-center justify-center gap-3">
                        <a href="{{ route('register.dropshipper') }}" class="font-semibold text-emerald-600 hover:text-emerald-700">
                            Daftar Dropshipper
                        </a>
                        <span class="text-slate-300">&bull;</span>
                        <a href="{{ route('register.supplier') }}" class="font-semibold text-slate-700 hover:text-emerald-700">
                            Daftar Toko
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Trust Rating & Testimoni Mitra --}}
        <x-auth-split-showcase />
    </div>
</x-guest-layout>
