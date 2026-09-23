<div class="mx-auto max-w-[1400px]">

    @php
        $sebutanStatus = $this->sebutanStatus();

        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push([
                'label' => 'Cari', 'nilai' => $search, 'props' => ['search'],
            ]);
        }

        if (filled($selectedStatus)) {
            $penyaringAktif->push([
                'label' => 'Status',
                'nilai' => $sebutanStatus[$selectedStatus] ?? $selectedStatus,
                'props' => ['selectedStatus'],
            ]);
        }

        if (filled($selectedProduct)) {
            $namaProduk = $selectedProduct === 'general'
                ? 'Tanpa produk tertentu'
                : (optional($products->firstWhere('id', $selectedProduct))->translated_name ?: 'Produk terpilih');

            $penyaringAktif->push([
                'label' => 'Produk', 'nilai' => $namaProduk, 'props' => ['selectedProduct'],
            ]);
        }

        if (filled($dateFrom) || filled($dateTo)) {
            $awal  = filled($dateFrom) ? \Carbon\Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') : 'awal';
            $akhir = filled($dateTo)   ? \Carbon\Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y')   : 'sekarang';

            $penyaringAktif->push([
                'label' => 'Rentang', 'nilai' => $awal . ' – ' . $akhir,
                'props' => ['dateFrom', 'dateTo'],
            ]);
        }
        
        $bersihkan = function (array $props) {
            $akhir = array_pop($props);

            return collect($props)->map(fn ($p) => "\$wire.\$set('{$p}', '', false);")->implode(' ')
                 . " \$wire.\$set('{$akhir}', '');";
        };
    @endphp

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-admin-display text-heading">
                Inquiry
            </h1>
            <p class="mt-1.5 text-admin-body text-ink-muted">
                Kelola dan pantau seluruh inquiry yang masuk dari calon pembeli.
            </p>
        </div>

        @can('export inquiries')
            
            <a href="{{ route('admin.inquiries.export') }}" target="_blank" rel="noopener"
               class="admin-btn admin-btn-brand shrink-0">
                <x-icon.admin name="download" size="h-4 w-4" />
                Ekspor CSV
            </a>
        @endcan
    </div>

    @if(session()->has('message'))
        
        <div x-data="{ tampil: true }" x-show="tampil" x-collapse
             class="mb-6 flex items-start gap-3 rounded-corner border border-brand/25 bg-brand-wash px-5 py-4"
             role="status">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand text-white">
                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="m4 8.4 2.8 2.8L12 5.6" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>

            <p class="min-w-0 flex-1 pt-1 text-admin-strong text-brand-deep">
                {{ session('message') }}
            </p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-brand/70 transition-colors hover:bg-brand/10 hover:text-brand-deep">
                <x-icon.admin name="close" size="h-4 w-4" />
            </button>
        </div>
    @endif

    <div class="card overflow-visible">
        
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="inquiry" size="h-[18px] w-[18px]" />
                </span>

                <div>
                    <h2 class="text-admin-title text-heading">Daftar inquiry</h2>
                    <p class="mt-0.5 text-admin-label text-ink-muted">
                        Menampilkan seluruh inquiry yang masuk beserta rincian pembeli dan status penanganannya.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-admin-label font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($inquiries->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'inquiry' }}
            </span>
        </div>

        <div class="px-5 pt-5">
            <div class="rounded-corner border border-line">

            <div class="grid gap-x-4 gap-y-4 p-5 sm:grid-cols-2 lg:grid-cols-3">

                {{-- Pencarian --}}
                <div class="relative sm:col-span-2 lg:col-span-3">
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                        <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                    </span>

                    <input type="search" wire:model.live="search" id="cari-inquiry"
                           aria-label="Cari inquiry"
                           placeholder="Cari nama pembeli, perusahaan, email, atau kode negara…"
                           class="admin-control pl-11 pr-10">

                    <span wire:loading wire:target="search"
                          class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.25"/>
                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                </div>

                {{-- Status --}}
                <x-admin.select model="selectedStatus" :value="$selectedStatus"
                                label="Saring menurut status" placeholder="Semua status"
                                :options="collect($sebutanStatus)
                                    ->map(fn ($sebutan, $kunci) => ['nilai' => $kunci, 'label' => $sebutan])
                                    ->values()->all()" />

                {{-- Produk --}}
                <x-admin.select model="selectedProduct" :value="$selectedProduct"
                                label="Saring menurut produk" placeholder="Semua produk"
                                :options="collect([['nilai' => 'general', 'label' => 'Tanpa produk tertentu']])
                                    ->concat($products->map(fn ($p) => [
                                        'nilai' => $p->id,
                                        'label' => ($n = $p->translated_name) ? $n : $p->slug,
                                    ]))->all()" />

                {{-- Rentang tanggal. --}}
                <div class="admin-control-group sm:col-span-2 lg:col-span-1"
                     title="Saring menurut tanggal inquiry masuk">
                    <input type="date" wire:model.live="dateFrom" aria-label="Inquiry masuk sejak tanggal"
                           max="{{ $dateTo ?: '' }}" class="admin-control-date">

                    <span class="shrink-0 text-admin-body text-ink-faint" aria-hidden="true">–</span>

                    <input type="date" wire:model.live="dateTo" aria-label="Inquiry masuk sampai tanggal"
                           min="{{ $dateFrom ?: '' }}" class="admin-control-date">
                </div>
            </div>

            {{-- Penyaring yang sedang menyala --}}
            @if($penyaringAktif->isNotEmpty())
                
                <div class="flex flex-wrap items-center gap-2 rounded-b-corner border-t border-line
                            bg-mist/60 px-5 py-3">
                    <span class="mr-1 inline-flex shrink-0 items-center gap-1.5
                                 text-admin-overline uppercase text-ink-faint">
                        <x-icon.admin name="filter" size="h-3.5 w-3.5" />
                        Disaring
                    </span>

                    @foreach($penyaringAktif as $p)
                        <span class="inline-flex max-w-full items-center gap-1.5 rounded-full border border-line
                                     bg-canvas py-1 pl-3 pr-1.5 text-admin-label text-ink-muted">
                            <span class="min-w-0 truncate">
                                {{ $p['label'] }}: <span class="font-semibold text-ink">{{ $p['nilai'] }}</span>
                            </span>

                            <button type="button" x-on:click="{{ $bersihkan($p['props']) }}"
                                    aria-label="Hapus penyaring {{ $p['label'] }}"
                                    class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full
                                           text-ink-faint transition-colors hover:bg-mist-deep hover:text-ink">
                                <x-icon.admin name="close" size="h-3 w-3" />
                            </button>
                        </span>
                    @endforeach

                    @if($penyaringAktif->count() > 1)
                        <button type="button"
                                x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedProduct', 'dateFrom', 'dateTo']) }}"
                                class="ml-auto shrink-0 text-admin-label font-semibold text-brand underline-offset-4 hover:underline">
                            Hapus semua
                        </button>
                    @endif
                </div>
            @endif
            </div>
        </div>

        {{-- Isi --}}
        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, selectedStatus, selectedProduct, dateFrom, dateTo, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        $kolom = [
                            ['label' => 'Pembeli',    'lebar' => 'w-[21%]', 'rata' => 'text-left'],
                            ['label' => 'Perusahaan', 'lebar' => 'w-[20%]', 'rata' => 'text-left'],
                            ['label' => 'Produk',     'lebar' => 'w-[18%]', 'rata' => 'text-left'],
                            ['label' => 'Status',     'lebar' => 'w-[10%]', 'rata' => 'text-left'],
                            ['label' => 'Ditangani Oleh',  'lebar' => 'w-[13%]', 'rata' => 'text-left'],
                            ['label' => 'Masuk',      'lebar' => 'w-[11%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',       'lebar' => 'w-[7%]',  'rata' => 'text-right'],
                        ];
                    @endphp
                    
                    <table class="w-full min-w-[1040px] table-fixed">
                        
                        @if($inquiries->isNotEmpty())
                            <thead>
                                <tr class="border-b border-line bg-mist/60">
                                    @foreach($kolom as $i => $k)
                                        <th @class([
                                            'py-3 text-admin-overline uppercase text-ink-faint',
                                            $k['lebar'], $k['rata'],
                                            'pl-5 pr-3' => $i === 0,
                                            'px-3'      => $i > 0 && $i < count($kolom) - 1,
                                            'pl-3 pr-5' => $i === count($kolom) - 1,
                                        ])>{{ $k['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                        @endif

                        <tbody>
                            @forelse($inquiries as $inq)
                                @php
                                    $sales  = $inq->assignedSales;
                                    $produk = $inq->product?->translated_name;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">
                                    
                                    <td class="py-4 pl-5 pr-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            <x-admin.avatar :name="$inq->name" size="sm" />

                                            <div class="min-w-0">
                                                <span class="block truncate text-admin-strong text-ink"
                                                      title="{{ $inq->name }}">{{ $inq->name }}</span>
                                                <span class="mt-0.5 block truncate text-admin-caption text-ink-faint"
                                                      title="{{ $inq->email }}">{{ $inq->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-3 py-4 align-middle">
                                        
                                        <span @class([
                                            'block truncate',
                                            'text-admin-strong text-ink'     => filled($inq->company),
                                            'text-admin-body text-ink-faint' => blank($inq->company),
                                        ]) title="{{ $inq->company }}">{{ $inq->company ?: 'Perorangan' }}</span>

                                        <x-admin.country :code="$inq->country_code" size="sm" class="mt-1" />
                                    </td>

                                    {{-- Produk + volume yang diminta --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'block truncate',
                                            'text-admin-strong text-ink'     => filled($produk),
                                            'text-admin-body text-ink-faint' => blank($produk),
                                        ]) title="{{ $produk }}">{{ $produk ?: 'Tanpa produk tertentu' }}</span>

                                        @if(filled($inq->volume))
                                            <span class="mt-0.5 block truncate text-admin-caption text-ink-faint"
                                                  title="Volume diminta: {{ $inq->volume }}">{{ $inq->volume }}</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$inq->status" />
                                    </td>

                                    {{-- Ditangani siapa --}}
                                    <td class="px-3 py-4 align-middle">
                                        @if($sales)
                                            <span class="flex items-center gap-2">
                                                <x-admin.avatar :name="$sales->name" size="sm" />
                                                <span class="min-w-0 truncate text-admin-body text-ink-muted"
                                                      title="{{ $sales->name }}">{{ $sales->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-admin-body text-ink-faint" title="Belum ditugaskan">&mdash;</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-3 py-4 align-middle">
                                        <time datetime="{{ $inq->created_at->toIso8601String() }}" class="block">
                                            <span class="block text-admin-body tabular-nums text-ink-muted">
                                                {{ $inq->created_at->locale('id')->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-admin-caption tabular-nums text-ink-faint">
                                                {{ $inq->created_at->format('H:i') }}
                                            </span>
                                        </time>
                                    </td>
                                    
                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <button type="button" wire:click="viewDetails('{{ $inq->id }}')"
                                                title="Kelola inquiry dari {{ $inq->name }}"
                                                aria-label="Kelola inquiry dari {{ $inq->name }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                       border border-line bg-canvas text-ink-muted transition-colors
                                                       hover:border-brand hover:bg-brand hover:text-white">
                                            <x-icon.admin name="manage" size="h-4 w-4" />
                                        </button>
                                    </td>
                                </tr>

                            @empty
                                
                                <tr>
                                    <td colspan="{{ count($kolom) }}" class="px-6 py-16 text-center">
                                        <span class="mx-auto flex h-12 w-12 items-center justify-center
                                                     rounded-full bg-mist text-ink-faint">
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'inquiry'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-admin-title text-heading">
                                                Tidak ada inquiry yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
                                                Coba longgarkan penyaringnya — misalnya lebarkan rentang tanggal
                                                atau kosongkan kata pencarian.
                                            </p>

                                            <button type="button"
                                                    x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedProduct', 'dateFrom', 'dateTo']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus semua penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-admin-title text-heading">
                                                Belum ada inquiry yang masuk
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
                                                Permintaan penawaran dari formulir kontak di situs publik
                                                akan muncul di sini begitu terkirim.
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kaki: penomoran halaman --}}
        <div class="border-t border-line px-6 py-4">
            {{ $inquiries->links('vendor.pagination.admin', ['satuan' => 'inquiry']) }}
        </div>
    </div>

    @include('livewire.admin.partials.inquiry-modal')

</div>
