<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Daftar penyaring yang sedang menyala — pola yang sama dengan halaman
         * Inquiry, Produk, Kategori, dan Sertifikasi.
         */
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push(['label' => 'Cari', 'nilai' => $search, 'props' => ['search']]);
        }

        if (filled($selectedStatus)) {
            $penyaringAktif->push([
                'label' => 'Status',
                'nilai' => $selectedStatus === 'active' ? 'Aktif' : 'Nonaktif',
                'props' => ['selectedStatus'],
            ]);
        }

        if (filled($selectedRegion)) {
            $penyaringAktif->push([
                'label' => 'Kawasan', 'nilai' => $selectedRegion,
                'props' => ['selectedRegion'],
            ]);
        }

        $bersihkan = function (array $props) {
            $akhir = array_pop($props);

            return collect($props)->map(fn ($x) => "\$wire.\$set('{$x}', '', false);")->implode(' ')
                 . " \$wire.\$set('{$akhir}', '');";
        };
    @endphp


    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="font-ui text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[26px]">
                Pasar Ekspor
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Negara tujuan ekspor yang ditampilkan di peta jangkauan situs publik.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah negara
        </button>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         PESAN SETELAH TERSIMPAN
         ══════════════════════════════════════════════════════════════════ --}}
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

            <p class="min-w-0 flex-1 pt-1 text-[13px] font-semibold text-brand-deep">
                {{ session('message') }}
            </p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-brand/70 transition-colors hover:bg-brand/10 hover:text-brand-deep">
                <x-icon.admin name="close" size="h-4 w-4" />
            </button>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════
         PENYARING
         ══════════════════════════════════════════════════════════════════ --}}
    {{-- overflow-visible supaya menu turun penyaringnya tidak terpotong —
         .card membawa overflow-hidden. --}}
    <div class="card mb-6 overflow-visible">

        {{-- Tiga kendali, jadi pencarian mengambil separuh baris dan dua
             penyaring membagi separuh sisanya. --}}
        <div class="grid gap-4 p-5 lg:grid-cols-4">

            <div class="relative lg:col-span-2">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-pasar"
                       aria-label="Cari negara tujuan"
                       placeholder="Cari nama negara, kode ISO, atau kawasan…"
                       class="admin-control pl-11 pr-10">

                <span wire:loading wire:target="search"
                      class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.25"/>
                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>

            <x-admin.select model="selectedStatus" :value="$selectedStatus"
                            label="Saring menurut status" placeholder="Semua status"
                            :options="[
                                ['nilai' => 'active',   'label' => 'Aktif'],
                                ['nilai' => 'inactive', 'label' => 'Nonaktif'],
                            ]" />

            <x-admin.select model="selectedRegion" :value="$selectedRegion"
                            label="Saring menurut kawasan" placeholder="Semua kawasan"
                            :options="$regions->map(fn ($r) => ['nilai' => $r, 'label' => $r])->all()" />
        </div>

        @if($penyaringAktif->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 rounded-b-corner border-t border-line
                        bg-mist/60 px-5 py-3">
                <span class="mr-1 inline-flex shrink-0 items-center gap-1.5 text-[11px] font-bold
                             uppercase tracking-[0.08em] text-ink-faint">
                    <x-icon.admin name="filter" size="h-3.5 w-3.5" />
                    Disaring
                </span>

                @foreach($penyaringAktif as $f)
                    <span class="inline-flex max-w-full items-center gap-1.5 rounded-full border border-line
                                 bg-canvas py-1 pl-3 pr-1.5 text-[12px] text-ink-muted">
                        <span class="min-w-0 truncate">
                            {{ $f['label'] }}: <span class="font-semibold text-ink">{{ $f['nilai'] }}</span>
                        </span>

                        <button type="button" x-on:click="{{ $bersihkan($f['props']) }}"
                                aria-label="Hapus penyaring {{ $f['label'] }}"
                                class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full
                                       text-ink-faint transition-colors hover:bg-mist-deep hover:text-ink">
                            <x-icon.admin name="close" size="h-3 w-3" />
                        </button>
                    </span>
                @endforeach

                @if($penyaringAktif->count() > 1)
                    <button type="button"
                            x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedRegion']) }}"
                            class="ml-auto shrink-0 text-[12px] font-semibold text-brand underline-offset-4 hover:underline">
                        Hapus semua
                    </button>
                @endif
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         TABEL
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="card">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                    <x-icon.admin name="market" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar negara tujuan</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut menurut nomor urutan tampilnya di situs publik.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($markets->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'negara' }}
            </span>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, selectedStatus, selectedRegion, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        $kolom = [
                            ['label' => 'Negara', 'lebar' => 'w-[46%]', 'rata' => 'text-left'],
                            ['label' => 'Urutan', 'lebar' => 'w-[14%]', 'rata' => 'text-left'],
                            ['label' => 'Status', 'lebar' => 'w-[16%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',   'lebar' => 'w-[24%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    {{-- Lebar minimalnya ikut turun begitu kolom catatan hilang.
                         Kalau dibiarkan 900px, empat kolom ini melar dan kolom
                         Aksi terlempar jauh dari Status. --}}
                    <table class="w-full min-w-[720px] table-fixed">
                        @if($markets->isNotEmpty())
                            <thead>
                                <tr class="border-b border-line bg-mist/60">
                                    @foreach($kolom as $i => $k)
                                        <th @class([
                                            'py-3 text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint',
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
                            @forelse($markets as $market)
                                @php
                                    $kode  = strtoupper($market->country_code);
                                    $nama  = $market->translated_name ?: ($kode ?: 'Tanpa nama');
                                    $aktif = (bool) $market->is_active;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Negara. Garis hijau di tepi kiri menandai pasar yang
                                         masih aktif — penanda kedua di samping pilnya, supaya
                                         negara yang dimatikan langsung terlihat berbeda dari
                                         ujung mata. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $aktif,
                                        'border-transparent' => ! $aktif,
                                    ])>
                                        <div class="flex items-center gap-3">
                                            {{-- Kode ISO-nya dipakai sebagai penanda barisnya.
                                                 Bendera sengaja tidak dipakai: proyek ini tidak
                                                 menyimpan berkasnya, dan emoji bendera tidak
                                                 tergambar sama sekali di Windows. --}}
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                         rounded-control border border-line bg-mist
                                                         text-[13px] font-bold tracking-[0.04em] text-ink-muted"
                                                  title="Kode ISO: {{ $kode }}">{{ $kode ?: '??' }}</span>

                                            <div class="min-w-0">
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $nama }}">{{ $nama }}</span>

                                                {{-- Kawasan jadi baris kedua, bukan kolomnya
                                                     sendiri: ia keterangan negaranya, dan
                                                     ruang yang hemat itu jatuh ke catatan
                                                     pasar yang isinya jauh lebih panjang. --}}
                                                <span class="mt-0.5 block truncate text-[12px] text-ink-faint"
                                                      title="Kawasan: {{ $market->region }}">{{ $market->region }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-control
                                                     bg-mist px-2 text-[13px] font-semibold tabular-nums text-ink-muted"
                                              title="Urutan tampil: {{ $market->sort_order }}">{{ $market->sort_order }}</span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$aktif ? 'active' : 'inactive'" />
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="edit('{{ $market->id }}')"
                                                    title="Ubah {{ $nama }}"
                                                    aria-label="Ubah {{ $nama }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

                                            {{-- Penegasannya menyebut akibatnya, bukan sekadar
                                                 "yakin?": negara ini hilang dari peta jangkauan
                                                 di situs publik. --}}
                                            <button type="button" wire:click="delete('{{ $market->id }}')"
                                                    wire:confirm="Hapus {{ $nama }} dari daftar pasar ekspor? Terjemahan dan catatan pasarnya ikut terhapus, dan negara ini hilang dari peta jangkauan di situs publik."
                                                    title="Hapus {{ $nama }}"
                                                    aria-label="Hapus {{ $nama }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-danger hover:bg-danger hover:text-white">
                                                <x-icon.admin name="trash" size="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="{{ count($kolom) }}" class="px-6 py-16 text-center">
                                        <span class="mx-auto flex h-12 w-12 items-center justify-center
                                                     rounded-full bg-mist text-ink-faint">
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'market'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada negara yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba kosongkan kata pencariannya, atau kembalikan
                                                status dan kawasannya ke "semua".
                                            </p>

                                            <button type="button"
                                                    x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedRegion']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada negara tujuan
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Negara yang ditambahkan di sini muncul sebagai peta
                                                jangkauan ekspor di situs publik.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah negara pertama
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="border-t border-line px-6 py-4">
            {{ $markets->links('vendor.pagination.admin', ['satuan' => 'negara']) }}
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TAMBAH / UBAH NEGARA
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
        @php
            /* Titik merah di sakelar bahasa: menandai tab mana yang isian
               wajibnya belum beres, supaya galat di tab tersembunyi tidak
               berujung tombol Simpan yang seakan tidak bereaksi. */
            $galatEn = $errors->hasAny(['name_en', 'note_en']);
            $galatId = $errors->hasAny(['name_id', 'note_id']);

            /* Nama baku negaranya menurut kode ISO yang sedang diketik.
               Ini cuma cermin — yang tersimpan tetap nama terjemahan yang
               diketik sendiri, bukan nilai ini. */
            $kodeKetik = strtoupper(trim($country_code));
            $namaIso   = config('countries', [])[$kodeKetik] ?? null;

            $kawasan = ['Asia', 'Europe', 'North America', 'South America',
                        'Africa', 'Australia/Oceania', 'Middle East'];
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-pasar">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            {{-- 900px, selebar modal kategori: jumlah isiannya sebanding, dan
                 panel selebar modal produk cuma menyisakan rongga kosong. --}}
            <div class="relative flex max-h-[90vh] w-full max-w-[900px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="market" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-pasar"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $editingId ? 'Ubah negara tujuan' : 'Tambah negara tujuan' }}
                                </h2>
                                <p class="mt-0.5 text-[12px] text-ink-muted">
                                    Isian bertanda <span class="font-bold text-brand">*</span> wajib diisi,
                                    termasuk nama di kedua bahasa.
                                </p>
                            </div>
                        </div>

                        <button type="button" wire:click="$set('showModal', false)"
                                aria-label="Tutup"
                                class="-mr-1 shrink-0 rounded-control p-1.5 text-ink-faint
                                       transition-colors hover:bg-mist hover:text-ink">
                            <x-icon.admin name="close" size="h-4 w-4" />
                        </button>
                    </div>

                    {{-- ── Dua kolom ───────────────────────────────────── --}}
                    <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                                lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                        {{-- ══ KIRI ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 p-6
                                    lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                            <section class="rounded-corner border border-line bg-canvas p-5">

                                {{-- Sakelar bahasa di kepala kartu: ia mengatur dua
                                     isian sekaligus — nama dan catatan — bukan
                                     menempel di salah satunya. --}}
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                        Informasi negara
                                    </h3>

                                    <div class="inline-flex rounded-control border border-line bg-mist p-1">
                                        @foreach(['en' => 'English', 'id' => 'Indonesia'] as $kode => $sebutan)
                                            <button type="button" wire:click="$set('activeTab', '{{ $kode }}')"
                                                    @class([
                                                        'inline-flex items-center gap-1.5 rounded-[5px] px-3 py-1.5
                                                         text-[12px] font-semibold transition-colors',
                                                        'bg-canvas text-ink shadow-[0_1px_2px_rgba(26,29,27,0.08)]'
                                                            => $activeTab === $kode,
                                                        'text-ink-muted hover:text-ink' => $activeTab !== $kode,
                                                    ])>
                                                {{ $sebutan }}

                                                @if(($kode === 'en' && $galatEn) || ($kode === 'id' && $galatId))
                                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-danger"
                                                          title="Ada isian yang perlu diperbaiki di sini"></span>
                                                @endif
                                            </button>
                                        @endforeach
                                        <div class="ml-2 border-l border-line pl-2 flex items-center">
                                            <button type="button" wire:click="autoTranslate" wire:loading.attr="disabled" wire:target="autoTranslate"
                                                title="Terjemahkan ID ke EN otomatis"
                                                class="inline-flex items-center gap-1.5 rounded-[5px] px-3 py-1.5 text-[12px] font-semibold bg-brand/10 text-brand hover:bg-brand/20 transition-colors">
                                                <span wire:loading.remove wire:target="autoTranslate">🌐 Auto EN</span>
                                                <span wire:loading wire:target="autoTranslate">⏳ ...</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">

                                    {{-- Nama negara — mengikuti tab. Keduanya tetap ada
                                         di DOM, yang tidak aktif disembunyikan: isian
                                         yang diketik lalu elemennya lenyap membuat
                                         Livewire kehilangan nilainya. --}}
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">
                                            Nama negara <span class="text-brand">*</span>
                                        </label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model="name_en"
                                                   aria-label="Nama negara dalam bahasa Inggris"
                                                   placeholder="mis. Germany"
                                                   class="admin-control">
                                            @error('name_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="name_id"
                                                   aria-label="Nama negara dalam bahasa Indonesia"
                                                   placeholder="mis. Jerman"
                                                   class="admin-control">
                                            @error('name_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Catatan pasar — mengikuti tab. Kolomnya sengaja
                                         tidak lagi tampil di tabel daftar, jadi di sinilah
                                         satu-satunya tempat isinya dibaca utuh. --}}
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label class="text-[12px] font-semibold text-ink-faint">Catatan pasar</label>

                                            {{-- Batasnya disebut di depan, bukan menunggu
                                                 galat muncul sesudah menekan Simpan. --}}
                                            <span class="text-[12px] text-ink-faint">Maksimal 500 karakter</span>
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <textarea wire:model="note_en" rows="8"
                                                      aria-label="Catatan pasar dalam bahasa Inggris"
                                                      placeholder="Syarat kepatuhan, dokumen wajib, atau catatan bea masuk…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('note_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <textarea wire:model="note_id" rows="8"
                                                      aria-label="Catatan pasar dalam bahasa Indonesia"
                                                      placeholder="Syarat kepatuhan, dokumen wajib, atau catatan bea masuk…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('note_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Dibaca tim sales saat menyiapkan penawaran; tidak tampil
                                            di tabel daftar negara.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: identitas negara ──────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Identitas negara
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="pasar-kode" class="block text-[12px] font-semibold text-ink-faint">
                                            Kode ISO <span class="text-brand">*</span>
                                        </label>

                                        {{-- .live, satu-satunya di modal ini: cerminan nama
                                             negaranya di bawah baru berguna kalau ia menyusul
                                             sambil mengetik. Dua huruf, jadi paling banter
                                             dua permintaan. --}}
                                        <input type="text" wire:model.live.debounce.400ms="country_code"
                                               id="pasar-kode" maxlength="2" autocomplete="off"
                                               placeholder="DE"
                                               class="admin-control mt-2 w-28 font-semibold uppercase
                                                      tracking-[0.12em] placeholder:tracking-normal">

                                        @error('country_code')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror

                                        {{-- Cerminan kodenya. Salah ketik kode dua huruf itu
                                             gampang dan akibatnya senyap — negara yang salah
                                             muncul di peta jangkauan situs publik. --}}
                                        <p class="mt-2 flex items-start gap-1.5 text-[12px] leading-relaxed">
                                            @if($namaIso)
                                                <span class="text-ink-faint">Terbaca sebagai</span>
                                                <span class="font-semibold text-ink">{{ $namaIso }}</span>
                                            @elseif(strlen($kodeKetik) === 2)
                                                <span class="text-danger">
                                                    Kode <span class="font-semibold">{{ $kodeKetik }}</span>
                                                    tidak ada di daftar ISO.
                                                </span>
                                            @else
                                                <span class="text-ink-faint">
                                                    Dua huruf sesuai ISO 3166-1, mis. US, DE, JP.
                                                </span>
                                            @endif
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">
                                            Kawasan <span class="text-brand">*</span>
                                        </label>

                                        {{-- :nullable="false" — tiap negara pasti berada di
                                             salah satu kawasan; pilihan kosong di puncaknya
                                             cuma jadi jawaban yang tidak sah.

                                             Daftarnya ketujuh kawasan tetap, BUKAN yang ada
                                             datanya seperti di bilah penyaring: di sini kita
                                             sedang menambah kawasan, bukan menyaringnya. --}}
                                        <x-admin.select model="region" :value="$region" class="mt-2"
                                                        label="Kawasan negara" :nullable="false"
                                                        :options="collect($kawasan)
                                                            ->map(fn ($k) => ['nilai' => $k, 'label' => $k])->all()" />

                                        @error('region')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            {{-- ── Kartu: penerbitan ────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Penerbitan
                                </h3>

                                <div class="space-y-4">

                                    {{-- Sakelar dari kotak centang asli yang disembunyikan,
                                         pola yang sama dengan "Produk unggulan" di modal
                                         produk. is_active itu boolean, jadi menu pilih tidak
                                         dipakai di sini: x-admin.select selalu mengirim untai,
                                         dan untai yang jatuh ke properti bertipe bool
                                         melempar galat tipe.

                                         Seluruh rona digerakkan peer-checked dan has-[:checked]
                                         di CSS, bukan oleh nilai di sisi PHP: wire:model di
                                         sini bersifat tunda, jadi nilai di server baru
                                         menyusul pada permintaan berikutnya. --}}
                                    <label class="flex cursor-pointer items-start justify-between gap-4
                                                  rounded-control border border-line p-3.5 transition-colors
                                                  hover:border-line-strong
                                                  has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">

                                        <span class="min-w-0">
                                            <span class="block text-[13px] font-semibold text-ink">Pasar aktif</span>
                                            <span class="mt-0.5 block text-[12px] leading-relaxed text-ink-muted">
                                                Hanya negara aktif yang muncul di peta jangkauan
                                                situs publik.
                                            </span>
                                        </span>

                                        <span class="relative mt-0.5 inline-flex shrink-0 items-center">
                                            {{-- @checked() supaya keadaannya sudah benar di
                                                 gambar pertama. wire:model sendiri tidak
                                                 menuliskan atribut itu — ia baru menyetel
                                                 sifat .checked sesudah Livewire hidup, jadi
                                                 tanpa ini sakelarnya sempat tergambar mati
                                                 untuk negara yang sebenarnya aktif. --}}
                                            <input type="checkbox" role="switch" wire:model="is_active"
                                                   @checked($is_active)
                                                   class="peer sr-only">

                                            {{-- Jalurnya --}}
                                            <span class="block h-6 w-11 rounded-full bg-mist-deep transition-colors
                                                         peer-checked:bg-brand
                                                         peer-focus-visible:ring-2 peer-focus-visible:ring-brand
                                                         peer-focus-visible:ring-offset-2"></span>

                                            {{-- Bulatannya --}}
                                            <span class="pointer-events-none absolute left-0.5 block h-5 w-5 rounded-full
                                                         bg-white shadow-[0_1px_3px_rgba(26,29,27,0.28)]
                                                         transition-transform peer-checked:translate-x-5"></span>
                                        </span>
                                    </label>

                                    <div>
                                        <label for="pasar-urutan" class="block text-[12px] font-semibold text-ink-faint">
                                            Urutan tampil
                                        </label>

                                        <input type="number" wire:model="sort_order" id="pasar-urutan"
                                               min="0" step="1" class="admin-control mt-2">

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Angka lebih kecil tampil lebih dulu di situs publik.
                                        </p>

                                        @error('sort_order')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    {{-- ── Kaki ────────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-center justify-end gap-2 border-t border-line px-6 py-4">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="admin-btn admin-btn-quiet">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                                class="admin-btn admin-btn-brand disabled:opacity-60">
                            <svg wire:loading wire:target="save"
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            {{ $editingId ? 'Simpan perubahan' : 'Simpan negara' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
