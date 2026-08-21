@props([
    'name',
    'size' => 'h-[18px] w-[18px]',
])

{{--
    Ikon panel admin.

    Dikumpulkan di satu berkas, bukan ditempel langsung di tata letak: dua belas
    menu berarti dua belas SVG, dan kalau semuanya ditulis di tempatnya masing-
    masing, tata letaknya jadi tiga kali lebih panjang dari isinya yang
    sebenarnya — dan tebal garis antar-ikon cepat berbeda-beda tanpa ada yang
    menyadarinya.

    Semua digambar pada kanvas 20×20 dengan tebal garis 1,5 supaya bobotnya
    seragam saat berdiri berderet.
--}}
@switch($name)

    @case('dashboard')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.2 8.6 10 3.2l6.8 5.4v7.2a1.2 1.2 0 0 1-1.2 1.2H4.4a1.2 1.2 0 0 1-1.2-1.2z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M7.8 17V11h4.4v6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('inquiry')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3 5.6a1.6 1.6 0 0 1 1.6-1.6h10.8A1.6 1.6 0 0 1 17 5.6v6.8a1.6 1.6 0 0 1-1.6 1.6H7.6L4 17z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('product')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M10 2.8 16.6 6v8L10 17.2 3.4 14V6z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M3.4 6 10 9.4 16.6 6M10 9.4v7.8" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('category')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="3" y="3" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
            <rect x="10.8" y="3" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
            <rect x="3" y="10.8" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
            <rect x="10.8" y="10.8" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
        </svg>
        @break

    @case('certification')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="10" cy="8" r="4.6" stroke="currentColor" stroke-width="1.5"/>
            <path d="M7 12.2 6 17.4l4-2 4 2-1-5.2" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('market')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
            <path d="M3 10h14M10 3c1.9 2 2.9 4.4 2.9 7s-1 5-2.9 7c-1.9-2-2.9-4.4-2.9-7s1-5 2.9-7Z"
                  stroke="currentColor" stroke-width="1.5"/>
        </svg>
        @break

    @case('news')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M4 4.6A1.6 1.6 0 0 1 5.6 3h8.8A1.6 1.6 0 0 1 16 4.6v10.8a1.6 1.6 0 0 1-1.6 1.6H5.6A1.6 1.6 0 0 1 4 15.4z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M7 7h6M7 10h6M7 13h3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('gallery')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="2.8" y="4.2" width="14.4" height="11.6" rx="1.8" stroke="currentColor" stroke-width="1.5"/>
            <path d="m4 13.4 3.6-3.4 3 2.6 2.6-2.4L16 12.8" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="7.4" cy="7.8" r="1.1" stroke="currentColor" stroke-width="1.4"/>
        </svg>
        @break

    @case('page')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11.4 2.8H5.8A1.6 1.6 0 0 0 4.2 4.4v11.2a1.6 1.6 0 0 0 1.6 1.6h8.4a1.6 1.6 0 0 0 1.6-1.6V7z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M11.4 2.8V7h4.4" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('download')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M10 3.4v8.2m0 0L6.8 8.4M10 11.6l3.2-3.2" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3.6 13v2.2a1.6 1.6 0 0 0 1.6 1.6h9.6a1.6 1.6 0 0 0 1.6-1.6V13"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('settings')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="10" cy="10" r="2.6" stroke="currentColor" stroke-width="1.5"/>
            <path d="M15.9 12.2a1.3 1.3 0 0 0 .26 1.44l.05.05a1.6 1.6 0 1 1-2.26 2.26l-.05-.05a1.3 1.3 0 0 0-1.44-.26 1.3 1.3 0 0 0-.79 1.19v.13a1.6 1.6 0 0 1-3.2 0v-.07a1.3 1.3 0 0 0-.85-1.19 1.3 1.3 0 0 0-1.44.26l-.05.05a1.6 1.6 0 1 1-2.26-2.26l.05-.05a1.3 1.3 0 0 0 .26-1.44 1.3 1.3 0 0 0-1.19-.79H2.8a1.6 1.6 0 0 1 0-3.2h.07a1.3 1.3 0 0 0 1.19-.85 1.3 1.3 0 0 0-.26-1.44l-.05-.05A1.6 1.6 0 1 1 6.01 3.7l.5.05a1.3 1.3 0 0 0 1.44.26h.06a1.3 1.3 0 0 0 .79-1.19V2.8a1.6 1.6 0 0 1 3.2 0v.07a1.3 1.3 0 0 0 .79 1.19 1.3 1.3 0 0 0 1.44-.26l.05-.05a1.6 1.6 0 1 1 2.26 2.26l-.5.05a1.3 1.3 0 0 0-.26 1.44v.06a1.3 1.3 0 0 0 1.19.79h.13a1.6 1.6 0 0 1 0 3.2h-.07a1.3 1.3 0 0 0-1.19.79Z"
                  stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
        </svg>
        @break

    @case('users')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="8" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2.6 16.4c0-2.8 2.4-4.6 5.4-4.6s5.4 1.8 5.4 4.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 4.4a3 3 0 0 1 0 5.4M15.4 11.9c1.4.6 2.4 1.9 2.4 3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('chart')
        {{-- Garis naik di dalam sumbu, bukan diagram batang: yang digambarkan
             kartu ini memang perubahan sepanjang waktu, bukan perbandingan
             antar-kategori. --}}
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.4 2.8v12.4a1.6 1.6 0 0 0 1.6 1.6h11.6" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round"/>
            <path d="m6.2 12.4 3-3.4 2.6 2 3.6-4.6" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('bell')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M5.4 8.4a4.6 4.6 0 0 1 9.2 0c0 3.4 1.2 4.6 1.2 4.6H4.2s1.2-1.2 1.2-4.6Z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M8.4 15.4a1.8 1.8 0 0 0 3.2 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('logout')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M8 17H5a1.6 1.6 0 0 1-1.6-1.6V4.6A1.6 1.6 0 0 1 5 3h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M12.6 13.4 16 10l-3.4-3.4M16 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('external')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11 3.6h5.4V9M16.4 3.6 9.2 10.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15.2 12v3.4a1.6 1.6 0 0 1-1.6 1.6H4.6A1.6 1.6 0 0 1 3 15.4V6.4a1.6 1.6 0 0 1 1.6-1.6H8"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('search')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="8.8" cy="8.8" r="5.2" stroke="currentColor" stroke-width="1.5"/>
            <path d="m12.6 12.6 3.8 3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('filter')
        {{-- Corong, bukan tiga garis geser: yang dilakukan barisan kendali ini
             memang menyaring baris, bukan mengatur nilai. --}}
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.4 4.4h13.2l-5 5.8v5l-3.2 1.6v-6.6z" stroke="currentColor"
                  stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('manage')
        {{-- Dua tuas geser, bukan pensil. Pensil menjanjikan "ubah tulisannya";
             yang sebenarnya dibuka tombol ini adalah panel untuk menyetel
             status, menugaskan sales, dan menulis catatan internal — mengatur
             perjalanan sebuah inquiry, bukan menyunting isinya. --}}
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.4 6.8h4.2M11.4 6.8h5.2M3.4 13.2h5.2M12.4 13.2h4.2"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="9.5" cy="6.8" r="1.9" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="10.5" cy="13.2" r="1.9" stroke="currentColor" stroke-width="1.5"/>
        </svg>
        @break

    @case('edit')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M13.2 3.6a1.7 1.7 0 0 1 2.4 2.4l-8 8-3.2.8.8-3.2z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('trash')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.6 5.6h12.8M8 5.6V4.2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1.4"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M5.4 5.6l.7 9.4a1.6 1.6 0 0 0 1.6 1.5h4.6a1.6 1.6 0 0 0 1.6-1.5l.7-9.4"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M8.4 8.6v4.8M11.6 8.6v4.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        @break

    @case('star')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="m10 2.8 2.24 4.54 5.01.73-3.62 3.53.85 4.99L10 14.24l-4.48 2.35.85-4.99L2.75 8.07l5.01-.73z"/>
        </svg>
        @break

    @case('pdf')
        {{-- Lembar dengan sudut terlipat: bentuk yang dikenali orang sebagai
             "berkas", dan lipatannya yang membedakannya dari ikon halaman. --}}
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11.4 2.8H6a1.6 1.6 0 0 0-1.6 1.6v11.2A1.6 1.6 0 0 0 6 17.2h8a1.6 1.6 0 0 0 1.6-1.6V7z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M11.4 2.8V7h4.2" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M7.4 11.4h5.2M7.4 14h3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        @break

    @case('mail')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="2.6" y="4.4" width="14.8" height="11.2" rx="1.8" stroke="currentColor" stroke-width="1.5"/>
            <path d="m3.4 5.8 5.7 4.3a1.5 1.5 0 0 0 1.8 0l5.7-4.3" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('whatsapp')
        {{-- Lambang WhatsApp digambar apa adanya, bukan diganti ikon telepon
             biasa: yang dikenali orang dari tombol ini justru bentuk itu, dan
             gagang telepon polos bisa disangka panggilan suara. --}}
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10.02 2.4a7.55 7.55 0 0 0-6.4 11.54l-1.2 4.4 4.5-1.18A7.55 7.55 0 1 0 10.02 2.4m0 1.38a6.17 6.17 0 0 1 4.9 9.92 6.17 6.17 0 0 1-7.85 1.67l-.32-.18-2.67.7.71-2.6-.2-.33a6.17 6.17 0 0 1 5.43-9.18"/>
            <path d="M7.62 6.2c-.16-.36-.33-.37-.48-.38h-.4a.79.79 0 0 0-.57.27 2.4 2.4 0 0 0-.75 1.78c0 1.05.77 2.07.87 2.21.11.14 1.48 2.37 3.65 3.23 1.8.71 2.17.57 2.56.53.4-.03 1.27-.51 1.44-1.01.18-.5.18-.93.13-1.02-.05-.09-.2-.14-.4-.25-.22-.1-1.28-.63-1.48-.7-.2-.07-.34-.11-.48.11-.14.21-.55.7-.68.84-.12.15-.25.16-.46.06-.22-.11-.91-.34-1.74-1.07-.64-.57-1.08-1.28-1.2-1.5-.13-.21-.02-.33.09-.44.1-.1.22-.25.32-.38.11-.13.15-.22.22-.36.08-.15.04-.27-.01-.38-.06-.11-.48-1.17-.65-1.6"/>
        </svg>
        @break

    @case('close')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="m5.6 5.6 8.8 8.8M14.4 5.6l-8.8 8.8" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('panel')
        {{-- Ikon lipat sidebar: bidang dengan satu sisi terisi, bukan tanda
             panah. Panah menjanjikan "pindah ke sana"; yang terjadi sebenarnya
             adalah sidebar-nya menyempit. --}}
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="2.8" y="3.6" width="14.4" height="12.8" rx="2" stroke="currentColor" stroke-width="1.5"/>
            <path d="M8 3.6v12.8" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2.8 5.6a2 2 0 0 1 2-2H8v12.8H4.8a2 2 0 0 1-2-2z" fill="currentColor" opacity="0.28"/>
        </svg>
        @break

@endswitch
