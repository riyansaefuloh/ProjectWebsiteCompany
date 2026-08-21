@php
    /*
     * Penomoran halaman untuk panel admin.
     *
     * Dipisah dari milik situs publik (site.blade.php) karena tugasnya
     * berbeda. Di situs publik, penomoran itu ajakan menjelajah: tombolnya
     * besar, bulat, dan berdiri di tengah. Di panel admin ia alat kerja —
     * ditempel di kaki tabel, rata kiri-kanan bersama keterangan
     * "menampilkan 1–10 dari 354", karena yang pertama ditanya seorang sales
     * bukan "halaman berapa" melainkan "masih ada berapa lagi".
     *
     * ── Deret nomornya dihitung sendiri, bukan memakai $elements ──────────
     *
     * Jendela bawaan Laravel melebar mengikuti onEachSide, dan dengan 36
     * halaman ia menggambar sepuluh nomor berderet sebelum titik-titiknya.
     * Sepuluh nomor bukan sekadar ramai — ia menipu: mata membaca deretan
     * panjang itu sebagai "banyak sekali halaman", padahal informasinya sudah
     * ada di kalimat sebelah kiri.
     *
     * Yang digambar sekarang: lima halaman pertama, titik-titik, dua halaman
     * terakhir. Begitu halaman yang dibuka bergeser ke tengah, jendelanya
     * ikut bergeser supaya halaman yang sedang dibuka selalu terlihat.
     */
    $kini  = $paginator->currentPage();
    $akhir = $paginator->lastPage();

    // Nomor yang wajib ada: dua pertama, dua terakhir, dan jendela di sekitar
    // halaman yang sedang dibuka.
    $wajib = collect([1, 2, $akhir - 1, $akhir]);

    $wajib = match (true) {
        $kini <= 5           => $wajib->concat(range(1, 5)),
        $kini >= $akhir - 4  => $wajib->concat(range($akhir - 4, $akhir)),
        default              => $wajib->concat([$kini - 1, $kini, $kini + 1]),
    };

    $nomor = $wajib
        ->filter(fn ($n) => $n >= 1 && $n <= $akhir)
        ->unique()->sort()->values();

    /*
     * Titik-titik disisipkan di tiap lompatan — kecuali lompatan yang cuma
     * menyembunyikan SATU halaman. Di situ nomornya sendiri lebih pendek
     * daripada titik-titiknya, dan "…" yang menutupi satu halaman saja
     * membuat pembacanya mengira ada banyak yang tersembunyi.
     */
    $deret   = collect();
    $sebelum = 0;

    foreach ($nomor as $n) {
        if ($n - $sebelum === 2) {
            $deret->push($sebelum + 1);
        } elseif ($n - $sebelum > 2) {
            $deret->push('…');
        }

        $deret->push($n);
        $sebelum = $n;
    }

    // Halaman kecil tidak perlu disaring sama sekali.
    if ($akhir <= 8) {
        $deret = collect(range(1, $akhir));
    }
@endphp

{{-- Tetap ditampilkan meski cuma satu halaman: keterangan jumlahnya sendiri
     yang berguna, dan kaki tabel yang muncul-hilang saat penyaring diubah
     membuat tinggi kartunya melompat. --}}
<nav role="navigation" aria-label="Penomoran halaman"
     class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3">

    {{-- $satuan: kata benda yang dihitung, dikirim halaman pemakainya lewat
         links('vendor.pagination.admin', ['satuan' => 'produk']). Tanpa ini
         kaki tabel produk berbunyi "dari 6 inquiry". --}}
    <p class="text-[12px] text-ink-muted">
        @if($paginator->total() > 0)
            Menampilkan
            <span class="font-semibold tabular-nums text-ink">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold tabular-nums text-ink">{{ number_format($paginator->total()) }}</span>
            {{ $satuan ?? 'baris' }}
        @else
            Tidak ada baris untuk ditampilkan
        @endif
    </p>

    @if($paginator->hasPages())
        <div class="flex items-center gap-1">

            {{-- ── Sebelumnya ──────────────────────────────────────────────
                 Berlabel kata, bukan panah telanjang. Panah sendirian di kaki
                 tabel mudah tertukar dengan "urutkan"; kata PREV tidak. --}}
            @if($paginator->onFirstPage())
                <span aria-disabled="true"
                      class="inline-flex cursor-default items-center gap-2 rounded-control px-2.5 py-2
                             text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint opacity-45">
                    <svg class="h-3.5 w-3.5 shrink-0 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9.5 4.5 13 8l-3.5 3.5" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="hidden sm:inline">Prev</span>
                </span>
            @else
                <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
                        rel="prev" aria-label="Halaman sebelumnya"
                        class="group inline-flex items-center gap-2 rounded-control px-2.5 py-2
                               text-[11px] font-bold uppercase tracking-[0.08em] text-ink-muted
                               transition-colors hover:bg-mist hover:text-ink">
                    <svg class="h-3.5 w-3.5 shrink-0 rotate-180 transition-transform group-hover:-translate-x-0.5"
                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9.5 4.5 13 8l-3.5 3.5" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="hidden sm:inline">Prev</span>
                </button>
            @endif

            {{-- ── Nomor halaman ───────────────────────────────────────────
                 Disembunyikan di layar sempit, digantikan penanda "3 / 36"
                 supaya barisnya tidak pernah membungkus. --}}
            <div class="mx-1 hidden items-center gap-1 sm:flex">
                @foreach($deret as $n)
                    @if($n === '…')
                        <span class="w-6 select-none text-center text-[13px] text-ink-faint"
                              aria-hidden="true">…</span>
                    @elseif($n == $kini)
                        <span aria-current="page"
                              class="inline-flex h-8 w-8 items-center justify-center rounded-full
                                     bg-brand text-[13px] font-bold tabular-nums text-white">{{ $n }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $n }})"
                                aria-label="Halaman {{ $n }}"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full
                                       text-[13px] font-semibold tabular-nums text-ink-muted
                                       transition-colors hover:bg-mist hover:text-ink">{{ $n }}</button>
                    @endif
                @endforeach
            </div>

            <span class="px-2 text-[13px] tabular-nums text-ink-muted sm:hidden">
                {{ $kini }} / {{ $akhir }}
            </span>

            {{-- ── Berikutnya ──────────────────────────────────────────────── --}}
            @if($paginator->hasMorePages())
                <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
                        rel="next" aria-label="Halaman berikutnya"
                        class="group inline-flex items-center gap-2 rounded-control px-2.5 py-2
                               text-[11px] font-bold uppercase tracking-[0.08em] text-ink-muted
                               transition-colors hover:bg-mist hover:text-ink">
                    <span class="hidden sm:inline">Next</span>
                    <svg class="h-3.5 w-3.5 shrink-0 transition-transform group-hover:translate-x-0.5"
                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9.5 4.5 13 8l-3.5 3.5" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @else
                <span aria-disabled="true"
                      class="inline-flex cursor-default items-center gap-2 rounded-control px-2.5 py-2
                             text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint opacity-45">
                    <span class="hidden sm:inline">Next</span>
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9.5 4.5 13 8l-3.5 3.5" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            @endif
        </div>
    @endif
</nav>
