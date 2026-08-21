<div class="mx-auto max-w-[1400px]">

    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6">
        <h1 class="font-ui text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[26px]">
            Kategori &amp; Tag Berita
        </h1>
        <p class="mt-1.5 text-[13px] text-ink-muted">
            Penggolongan artikel. Keduanya dipakai di modal berita dan sebagai penyaring di situs publik.
        </p>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         PESAN
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

            <p class="min-w-0 flex-1 pt-1 text-[13px] font-semibold text-brand-deep">{{ session('message') }}</p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-brand/70 transition-colors hover:bg-brand/10 hover:text-brand-deep">
                <x-icon.admin name="close" size="h-4 w-4" />
            </button>
        </div>
    @endif

    {{-- Penolakan hapus digambar beda warna dari pesan berhasil: keduanya
         muncul di tempat yang sama, dan warna yang sama membuat penolakan
         terbaca sebagai keberhasilan. --}}
    @if(session()->has('galat'))
        <div x-data="{ tampil: true }" x-show="tampil" x-collapse
             class="mb-6 flex items-start gap-3 rounded-corner border border-status-rejected/30
                    bg-status-rejected/5 px-5 py-4"
             role="alert">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-status-rejected text-white">
                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M8 4.4v4.4M8 11.4v.2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>

            <p class="min-w-0 flex-1 pt-1 text-[13px] font-semibold text-status-rejected">{{ session('galat') }}</p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-status-rejected/70 transition-colors
                           hover:bg-status-rejected/10">
                <x-icon.admin name="close" size="h-4 w-4" />
            </button>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════
         DUA DAFTAR BERDAMPINGAN
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid gap-6 lg:grid-cols-2">

        @foreach([
            [
                'jenis'   => 'kategori',
                'judul'   => 'Kategori berita',
                'catatan' => 'Satu berita memiliki satu kategori.',
                'ikon'    => 'category',
                'daftar'  => $kategori,
                'prop'    => 'kategoriBaru',
                'aksi'    => 'tambahKategori',
                'contoh'  => 'mis. Kabar Ekspor',
                'kosong'  => 'Belum ada kategori. Tambahkan satu supaya berita bisa digolongkan.',
            ],
            [
                'jenis'   => 'tag',
                'judul'   => 'Tag berita',
                'catatan' => 'Satu berita boleh memiliki banyak tag.',
                'ikon'    => 'news',
                'daftar'  => $tag,
                'prop'    => 'tagBaru',
                'aksi'    => 'tambahTag',
                'contoh'  => 'mis. kopi arabika',
                'kosong'  => 'Belum ada tag. Selama kosong, pemilih tag di modal berita tidak menampilkan apa pun.',
            ],
        ] as $panel)

            <section class="card flex flex-col">

                {{-- ── Kepala kartu ────────────────────────────────────── --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                            <x-icon.admin :name="$panel['ikon']" size="h-4 w-4" />
                        </span>

                        <div>
                            <h2 class="font-ui text-[15px] font-semibold text-ink">{{ $panel['judul'] }}</h2>
                            <p class="mt-0.5 text-[12px] text-ink-muted">{{ $panel['catatan'] }}</p>
                        </div>
                    </div>

                    <span class="shrink-0 rounded-full border border-line bg-mist px-3 py-1
                                 text-[12px] font-semibold tabular-nums text-ink-muted">
                        {{ $panel['daftar']->count() }}
                    </span>
                </div>

                {{-- ── Tambah ──────────────────────────────────────────── --}}
                <form wire:submit.prevent="{{ $panel['aksi'] }}" class="border-b border-line px-5 py-4">
                    <div class="flex gap-2">
                        <input type="text" wire:model="{{ $panel['prop'] }}"
                               aria-label="Nama {{ strtolower($panel['judul']) }} baru"
                               placeholder="{{ $panel['contoh'] }}"
                               class="admin-control">

                        <button type="submit" class="admin-btn admin-btn-brand shrink-0">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                      stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            Tambah
                        </button>
                    </div>

                    @error($panel['prop'])
                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                    @enderror
                </form>

                {{-- ── Daftar ──────────────────────────────────────────── --}}
                <div class="flex-1 p-5">
                    @forelse($panel['daftar'] as $baris)
                        @php($kunci = $panel['jenis'] . ':' . $baris->id)

                        <div @class([
                            'flex flex-wrap items-center gap-3 rounded-control px-3 py-2.5',
                            'bg-brand-wash' => $sedangUbah === $kunci,
                            'hover:bg-mist/50' => $sedangUbah !== $kunci,
                        ])>
                            @if($sedangUbah === $kunci)
                                {{-- Disunting di tempat. Kolomnya menggantikan
                                     namanya, jadi tinggi barisnya tidak berubah
                                     dan baris di bawahnya tidak melompat. --}}
                                <form wire:submit.prevent="simpanUbah" class="flex min-w-0 flex-1 flex-wrap gap-2">
                                    <input type="text" wire:model="namaUbah" autofocus
                                           aria-label="Nama baru" class="admin-control min-w-0 flex-1">

                                    <button type="submit" class="admin-btn admin-btn-brand shrink-0">Simpan</button>

                                    <button type="button" wire:click="batalUbah" class="admin-btn shrink-0">Batal</button>
                                </form>
                            @else
                                <span class="min-w-0 flex-1 truncate text-[13px] font-semibold text-ink"
                                      title="{{ $baris->name }}">{{ $baris->name }}</span>

                                <span class="shrink-0 font-mono text-[11px] text-ink-faint"
                                      title="Slug: {{ $baris->slug }}">/{{ $baris->slug }}</span>

                                <span class="shrink-0 rounded-full border border-line px-2 py-0.5
                                             text-[11px] tabular-nums text-ink-muted"
                                      title="{{ $baris->news_count }} berita memakai ini">
                                    {{ $baris->news_count }}
                                </span>

                                <div class="flex shrink-0 items-center gap-1.5">
                                    <button type="button"
                                            wire:click="ubah('{{ $panel['jenis'] }}', '{{ $baris->id }}')"
                                            title="Ubah nama {{ $baris->name }}"
                                            aria-label="Ubah nama {{ $baris->name }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                   border border-line bg-canvas text-ink-muted transition-colors
                                                   hover:border-brand hover:text-brand">
                                        <x-icon.admin name="edit" size="h-4 w-4" />
                                    </button>

                                    <button type="button"
                                            wire:click="hapus('{{ $panel['jenis'] }}', '{{ $baris->id }}')"
                                            wire:confirm="Hapus &quot;{{ $baris->name }}&quot;?"
                                            title="Hapus {{ $baris->name }}"
                                            aria-label="Hapus {{ $baris->name }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                   border border-line bg-canvas text-ink-muted transition-colors
                                                   hover:border-status-rejected hover:text-status-rejected">
                                        <x-icon.admin name="trash" size="h-4 w-4" />
                                    </button>
                                </div>
                            @endif
                        </div>

                        @error('namaUbah')
                            @if($sedangUbah === $kunci)
                                <span class="mt-1 block px-3 text-[12px] text-danger">{{ $message }}</span>
                            @endif
                        @enderror
                    @empty
                        <p class="px-3 py-8 text-center text-[13px] leading-relaxed text-ink-faint">
                            {{ $panel['kosong'] }}
                        </p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</div>
