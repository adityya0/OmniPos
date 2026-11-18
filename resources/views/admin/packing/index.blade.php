<x-app-layout title="Antrean Packing">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-lg text-slate-800">
                    {{ $isTimPacking ? 'Workspace Tim Packing' : 'Antrean Packing & Approval' }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $isTimPacking ? 'Proses pesanan siap dikemas.' : 'Persetujuan pesanan & pantau operasional gudang.' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-xl">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                    {{ Auth::user()->name }} ({{ Auth::user()->role }})
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50 min-h-full">
        <div class="w-full">
            
            {{-- Flash Alert --}}
            @if (session('success'))
                <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 ">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2 ">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- React Application Mount Point --}}
            <div id="packing-react-root"
                 data-props="{{ json_encode([
                     'orders' => $orders->items(),
                     'counts' => [
                         'menunggu_approval' => $countMenungguApproval,
                         'menunggu_packing' => $countMenungguPacking,
                         'dipacking' => $countDipacking,
                         'dikirim' => $countSelesai,
                     ],
                     'statusFilter' => $statusFilter,
                     'isTimPacking' => $isTimPacking,
                     'csrfToken' => csrf_token(),
                     'routes' => [
                         'approve' => route('admin.packing.approve', ':id'),
                         'reject' => route('admin.packing.reject', ':id'),
                         'status' => route('admin.packing.status', ':id'),
                         'staff' => route('admin.staff.index'),
                     ],
                     'user' => [
                         'name' => Auth::user()->name,
                         'role' => Auth::user()->role,
                     ]
                 ]) }}">
            </div>

            @if ($orders->hasPages())
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        @viteReactRefresh
        @vite(['resources/js/packing-app.jsx'])
    @endpush
</x-app-layout>
