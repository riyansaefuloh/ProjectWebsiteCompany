@props([
    'label',
    'value'  => null,
    'kosong' => 'Tidak diisi',
    'blok'   => false,   // isian panjang yang boleh membungkus ke banyak baris
])

@php
    /*
     * Satu keterangan yang hanya bisa dibaca, digambar berbingkai.
     *
     * Bingkainya bukan hiasan. Di layar yang separuhnya bisa diubah dan
     * separuhnya tidak, keterangan tanpa bingkai berdiri dengan tinggi
     * seadanya menurut panjang isinya — dan deretan nilai yang tepi bawahnya
     * tidak pernah sejajar terbaca berantakan meski isinya benar semua.
     * Dengan bingkai, semuanya duduk di garis yang sama.
     *
     * Isiannya berlatar putih dengan garis tipis — bentuk yang sama dengan
     * kolom isian sungguhan. Yang membedakannya dari kendali di kolom kanan
     * bukan warna latarnya, melainkan permukaan yang menaunginya: kotak
     * bacaan duduk di kartu putih, kendali yang bisa diubah duduk di panel
     * abu. cursor-default menutup sisanya — kursor yang tidak berubah jadi
     * garis tegak saat melewatinya sudah cukup mengatakan "ini tidak
     * diketik".
     *
     * min-h 42px = tinggi .admin-control (teks 13px, leading 20px, bantalan
     * tegak 2×10px, garis 2×1px). Angkanya disamakan supaya baris keterangan
     * di kiri berbaris rata dengan kendali di kanan.
     */
@endphp

{{-- flex-col + flex-1 + h-full: petak menyamakan tinggi sel-sel sebaris,
     tapi kotak di dalamnya tidak ikut tinggi itu dengan sendirinya. Tanpa
     ketiganya, "Email" yang membungkus dua baris berdiri lebih tinggi dari
     "Negara" di sebelahnya, dan tepi bawah keduanya tidak sejajar — persis
     kerapian yang dicari bingkai ini. --}}
<div {{ $attributes->class(['flex min-w-0 flex-col']) }}>
    {{-- Huruf normal, bukan kapital — dan begitu kapitalnya lepas, jarak
         antar-hurufnya ikut dilepas: renggang 0,08em memang dipasang untuk
         menolong deretan huruf kapital yang saling berdempetan, tapi pada
         huruf normal ia justru mencerai-beraikan katanya.

         Ukurannya naik 11 → 12px karena huruf normal punya tinggi badan yang
         lebih pendek dari huruf kapital, jadi pada ukuran yang sama ia
         terbaca lebih kecil. --}}
    <dt class="text-[12px] font-semibold text-ink-faint">{{ $label }}</dt>

    <dd class="mt-1.5 flex-1">
        <div @class([
            'h-full cursor-default rounded-control border border-line bg-canvas px-3.5 py-2.5',
            'flex min-h-[42px] items-center' => ! $blok,
            'min-h-[112px]'                  => $blok,
        ])>
            @if($slot->isNotEmpty())
                {{ $slot }}
            @elseif(filled($value))
                <span @class([
                    'min-w-0 text-[13px] leading-5 text-ink',
                    'break-words'                                             => ! $blok,
                    'block whitespace-pre-line leading-relaxed text-ink-muted' => $blok,
                ])>{{ $value }}</span>
            @else
                <span class="text-[13px] leading-5 text-ink-faint">{{ $kosong }}</span>
            @endif
        </div>
    </dd>
</div>
