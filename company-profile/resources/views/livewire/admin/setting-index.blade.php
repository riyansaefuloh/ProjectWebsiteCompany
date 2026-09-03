<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Zona waktu yang masuk akal untuk eksportir Indonesia. Nilai yang
         * sedang tersimpan selalu ikut disertakan meski di luar daftar —
         * kalau tidak, menu pilihnya akan menampilkan pilihan pertama dan
         * diam-diam mengganti zona waktunya begitu disimpan.
         */
        $zona = collect([
            'Asia/Jakarta'  => 'Asia/Jakarta — WIB',
            'Asia/Makassar' => 'Asia/Makassar — WITA',
            'Asia/Jayapura' => 'Asia/Jayapura — WIT',
            'UTC'           => 'UTC',
        ]);

        if (filled($timezone) && ! $zona->has($timezone)) {
            $zona = $zona->prepend($timezone, $timezone);
        }

        /*
         * Perbandingannya memakai ANGKANYA saja, sama seperti yang dilakukan
         * kaki situs: "+62 812-3456-7890" dan "6281234567890" itu nomor yang
         * sama, cuma beda cara menulisnya.
         */
        $angka = fn ($n) => preg_replace('/\D+/', '', (string) $n);

        $nomorKembar = filled($company_phone)
            && $angka($company_phone) === $angka($whatsapp_number);
    @endphp

    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <form wire:submit.prevent="save">

        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-admin-display text-ink">
                    Pengaturan
                </h1>
                {{-- Yang diatur di sini identitas dan sambungan situs, bukan
                     isi halamannya. Isi halaman publik diatur di menu Halaman. --}}
                <p class="mt-1.5 text-admin-body text-ink-muted">
                    Identitas perusahaan, logo, kontak, tautan sosial, dan integrasi.
                </p>
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:target="save, logo, favicon"
                    class="admin-btn admin-btn-brand shrink-0 disabled:opacity-60">
                <svg wire:loading wire:target="save"
                     class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                    <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                Simpan perubahan
            </button>
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             PESAN SETELAH TERSIMPAN
             ══════════════════════════════════════════════════════════════ --}}
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

        {{-- ══════════════════════════════════════════════════════════════
             DUA KOLOM
             ══════════════════════════════════════════════════════════════ --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ══ KIRI ══ --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- ── Identitas perusahaan ─────────────────────────── --}}
                <section class="card">
                    <div class="flex items-center gap-2.5 border-b border-line px-6 py-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                     rounded-control bg-brand-wash text-brand">
                            <x-icon.admin name="panel" size="h-[18px] w-[18px]" />
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-admin-title text-ink">Identitas perusahaan</h2>
                            <p class="mt-0.5 text-admin-label text-ink-muted">Nama, alamat, dan letak di peta.</p>
                        </div>
                    </div>

                    <div class="p-6">

                        <div class="space-y-4">
                            <div>
                                <label for="set-nama" class="block text-admin-label text-ink-faint">
                                    Nama perusahaan <span class="text-brand">*</span>
                                </label>

                                <input type="text" wire:model="company_name" id="set-nama"
                                       class="admin-control mt-2">

                                @error('company_name')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="set-alamat" class="block text-admin-label text-ink-faint">
                                    Alamat <span class="text-brand">*</span>
                                </label>

                                <textarea wire:model="company_address" id="set-alamat" rows="3"
                                          class="admin-control mt-2 resize-none leading-relaxed"></textarea>

                                @error('company_address')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="set-peta" class="block text-admin-label text-ink-faint">
                                    Tautan sematan Google Maps
                                </label>

                                <input type="url" wire:model="google_map_url" id="set-peta"
                                       placeholder="https://www.google.com/maps/embed?…"
                                       class="admin-control mt-2">

                                {{-- Yang dibutuhkan alamat SEMATAN, bukan
                                     tautan dari bilah alamat — yang salah
                                     membuat peta di halaman kontak kosong
                                     tanpa pesan apa pun. --}}
                                <p class="mt-2 text-admin-label text-ink-faint">
                                    Ambil dari Google Maps → Bagikan → Sematkan peta → salin
                                    bagian <span class="font-semibold text-ink-muted">src</span>-nya.
                                    Diawali <span class="font-semibold text-ink-muted">https://www.google.com/maps/embed</span>.
                                </p>

                                @error('google_map_url')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── Kontak ───────────────────────────────────────── --}}
                <section class="card">
                    <div class="flex items-center gap-2.5 border-b border-line px-6 py-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                     rounded-control bg-brand-wash text-brand">
                            <x-icon.admin name="mail" size="h-[18px] w-[18px]" />
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-admin-title text-ink">Kontak</h2>
                            <p class="mt-0.5 text-admin-label text-ink-muted">Email dan nomor yang tampil di situs publik.</p>
                        </div>
                    </div>

                    <div class="p-6">

                        <div class="space-y-4">
                            <div>
                                <label for="set-email" class="block text-admin-label text-ink-faint">
                                    Email kontak <span class="text-brand">*</span>
                                </label>

                                <input type="email" wire:model="contact_email" id="set-email"
                                       class="admin-control mt-2">

                                @error('contact_email')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror

                            </div>

                            {{-- Dua nomor berdampingan: keduanya tampil di kaki situs
                                 publik, dan yang membedakannya cuma cara menghubungi. --}}
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="set-wa" class="block text-admin-label text-ink-faint">
                                        Nomor WhatsApp <span class="text-brand">*</span>
                                    </label>

                                    <input type="text" wire:model.live.debounce.500ms="whatsapp_number" id="set-wa"
                                           placeholder="6281234567890"
                                           class="admin-control mt-2">

                                    <p class="mt-2 text-admin-label text-ink-faint">
                                        Format internasional tanpa spasi — inilah yang dirangkai
                                        jadi tautan wa.me.
                                    </p>

                                    @error('whatsapp_number')
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="set-telepon" class="block text-admin-label text-ink-faint">
                                        Nomor telepon
                                    </label>

                                    <input type="text" wire:model.live.debounce.500ms="company_phone" id="set-telepon"
                                           placeholder="+62 21 1234 5678"
                                           class="admin-control mt-2">

                                    <p class="mt-2 text-admin-label text-ink-faint">
                                        Nomor yang ditelepon biasa. Dikosongkan berarti kaki situs
                                        hanya menampilkan WhatsApp.
                                    </p>

                                    @error('company_phone')
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kaki situs menyembunyikan telepon yang
                                 angkanya sama persis dengan WhatsApp: nomor
                                 yang sama dua kali terbaca seperti salah
                                 ketik. --}}
                            @if($nomorKembar)
                                <div class="flex items-start gap-2.5 rounded-control border border-status-new/30
                                            bg-status-new/5 px-3.5 py-2.5">
                                    <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center
                                                 rounded-full bg-status-new text-white">
                                        <svg class="h-2.5 w-2.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M8 4.4v4.4M8 11.4v.2" stroke="currentColor"
                                                  stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </span>

                                    <p class="min-w-0 text-admin-label text-ink-muted">
                                        Kedua nomornya sama. Kaki situs publik akan menampilkan
                                        <span class="font-semibold text-ink">satu nomor saja</span> —
                                        beda keduanya supaya WhatsApp dan telepon tampil berdampingan.
                                    </p>
                                </div>
                            @endif

                            {{-- ── Jam operasional — sudah lama digambar kaki
                                 situs dan halaman kontak, tapi belum pernah
                                 punya isiannya di panel. ── --}}
                            <div class="border-t border-line pt-4">
                                <span class="block text-admin-label text-ink-faint">Jam operasional</span>

                                <div class="mt-2 grid gap-4 sm:grid-cols-3">
                                    @foreach([
                                        ['prop' => 'hours_weekday',  'id' => 'set-jam-1', 'label' => 'Senin–Jumat', 'contoh' => '08.00 – 17.00 WIB'],
                                        ['prop' => 'hours_saturday', 'id' => 'set-jam-2', 'label' => 'Sabtu',       'contoh' => '08.00 – 13.00 WIB'],
                                        ['prop' => 'hours_sunday',   'id' => 'set-jam-3', 'label' => 'Minggu',      'contoh' => 'Tutup'],
                                    ] as $jam)
                                        <div>
                                            <label for="{{ $jam['id'] }}" class="block text-admin-label text-ink-muted">
                                                {{ $jam['label'] }}
                                            </label>

                                            <input type="text" wire:model="{{ $jam['prop'] }}" id="{{ $jam['id'] }}"
                                                   placeholder="{{ $jam['contoh'] }}"
                                                   class="admin-control mt-1.5">

                                            @error($jam['prop'])
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>

                                <p class="mt-2 text-admin-label text-ink-faint">
                                    Baris yang dikosongkan tidak digambar. Kalau ketiganya kosong,
                                    blok jam operasional tidak muncul sama sekali di situs publik.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── Tautan sosial ────────────────────────────────── --}}
                <section class="card">
                    <div class="flex items-center gap-2.5 border-b border-line px-6 py-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                     rounded-control bg-brand-wash text-brand">
                            <x-icon.admin name="external" size="h-[18px] w-[18px]" />
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-admin-title text-ink">Tautan sosial</h2>
                            <p class="mt-0.5 text-admin-label text-ink-muted">Ikon yang digambar di kaki situs.</p>
                        </div>
                    </div>

                    <div class="p-6">

                        <div class="grid gap-4 sm:grid-cols-3">
                            @foreach([
                                ['prop' => 'facebook_url',  'id' => 'set-fb', 'label' => 'Facebook',  'contoh' => 'https://facebook.com/…'],
                                ['prop' => 'instagram_url', 'id' => 'set-ig', 'label' => 'Instagram', 'contoh' => 'https://instagram.com/…'],
                                ['prop' => 'linkedin_url',  'id' => 'set-li', 'label' => 'LinkedIn',  'contoh' => 'https://linkedin.com/company/…'],
                            ] as $sosial)
                                <div>
                                    <label for="{{ $sosial['id'] }}" class="block text-admin-label text-ink-faint">
                                        {{ $sosial['label'] }}
                                    </label>

                                    <input type="url" wire:model="{{ $sosial['prop'] }}" id="{{ $sosial['id'] }}"
                                           placeholder="{{ $sosial['contoh'] }}"
                                           class="admin-control mt-2">

                                    @error($sosial['prop'])
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <p class="mt-3 text-admin-label text-ink-faint">
                            Dikosongkan berarti ikonnya tidak digambar di kaki situs publik.
                        </p>
                    </div>
                </section>
            </div>

            {{-- ══ KANAN ══ --}}
            <div class="space-y-6">

                {{-- ── Logo & ikon ──────────────────────────────────── --}}
                <section class="card">
                    <div class="flex items-center gap-2.5 border-b border-line px-6 py-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                     rounded-control bg-brand-wash text-brand">
                            <x-icon.admin name="gallery" size="h-[18px] w-[18px]" />
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-admin-title text-ink">Logo &amp; ikon</h2>
                            <p class="mt-0.5 text-admin-label text-ink-muted">Lambang situs dan ikon tab peramban.</p>
                        </div>
                    </div>

                    <div class="p-6">

                        <div class="space-y-5">
                            @foreach([
                                ['prop' => 'logo',    'id' => 'set-logo',    'lama' => $existing_logo,
                                 'label' => 'Logo',   'catatan' => 'PNG atau SVG berlatar tembus, maksimal 2 MB.'],
                                ['prop' => 'favicon', 'id' => 'set-favicon', 'lama' => $existing_favicon,
                                 'label' => 'Favicon','catatan' => 'Persegi, minimal 64×64 piksel, maksimal 1 MB.'],
                            ] as $berkas)
                                <div>
                                    <span class="block text-admin-label text-ink-faint">{{ $berkas['label'] }}</span>

                                    @php
                                        /* Alamatnya dicek benar-benar ada di disk, bukan cuma
                                           kolomnya terisi: <img> beralamat mati menggambar ikon
                                           rusak, dan itu terbaca sebagai logonya yang rusak. */
                                        $adaLama = filled($berkas['lama'])
                                            && \Illuminate\Support\Facades\Storage::disk('public')->exists($berkas['lama']);
                                    @endphp

                                    {{-- Ubin PERSEGI, sama dengan modal
                                         Produk, Kategori, Berita, Galeri, dan
                                         Unduhan. Logo memang melintang, tapi
                                         object-contain membuatnya duduk utuh
                                         di dalamnya. --}}
                                    <div class="mt-2 grid grid-cols-2 gap-3">
                                        @if($adaLama)
                                            <div class="group/ubin relative aspect-square overflow-hidden
                                                        rounded-control border border-line bg-mist/40">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($berkas['lama']) }}"
                                                     alt="" class="block h-full w-full object-contain p-3">

                                                {{-- Tombol hapus bersembunyi
                                                     di lapisan gelap yang
                                                     naik saat kursor lewat,
                                                     sama dengan ubin gambar
                                                     di halaman lain. --}}
                                                <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                            from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                            group-hover/ubin:opacity-100 group-focus-within/ubin:opacity-100">
                                                    <x-admin.confirm-delete metode="deleteFile"
                                                                            :id="$berkas['prop']"
                                                                            label="Hapus {{ mb_strtolower($berkas['label']) }}"
                                                                            judul="Hapus {{ mb_strtolower($berkas['label']) }}?"
                                                                            tombol="Ya, hapus"
                                                                            ikon="h-3.5 w-3.5"
                                                                            kelas="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                                                   justify-center rounded-control bg-white/90 text-danger
                                                                                   transition-colors hover:bg-white">
                                                        Berkasnya terhapus dari penyimpanan, dan situs publik kembali tampil tanpa {{ mb_strtolower($berkas['label']) }}.
                                                    </x-admin.confirm-delete>
                                                </div>
                                            </div>
                                        @elseif(filled($berkas['lama']))
                                            {{-- Tercatat di basis data, hilang di disk. --}}
                                            <div class="flex aspect-square flex-col items-center justify-center gap-2
                                                        rounded-control border border-dashed border-status-rejected/40
                                                        bg-status-rejected/5 px-3 text-center text-status-rejected">
                                                <x-icon.admin name="gallery" size="h-6 w-6" />

                                                <span class="text-admin-caption font-semibold">Berkas hilang</span>
                                            </div>
                                        @endif

                                        {{-- Berkas yang baru dipilih tapi belum tersimpan.
                                             temporaryUrl() dibungkus try: ia melempar galat
                                             untuk berkas yang bukan gambar. --}}
                                        @if($this->{$berkas['prop']})
                                            @php
                                                try {
                                                    $pratinjau = $this->{$berkas['prop']}->temporaryUrl();
                                                } catch (\Throwable $e) {
                                                    $pratinjau = null;
                                                }
                                            @endphp

                                            <div class="relative aspect-square overflow-hidden rounded-control
                                                        border border-dashed border-brand/50 bg-brand-wash">
                                                @if($pratinjau)
                                                    <img src="{{ $pratinjau }}" alt=""
                                                         class="block h-full w-full object-contain p-3">
                                                @else
                                                    <span class="flex h-full w-full items-center justify-center px-3
                                                                 text-center text-admin-caption text-ink-muted">
                                                        {{ $this->{$berkas['prop']}->getClientOriginalName() }}
                                                    </span>
                                                @endif

                                                <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                             text-admin-caption font-semibold text-white">Baru</span>
                                            </div>
                                        @endif

                                        <x-admin.upload-tile model="{{ $berkas['prop'] }}"
                                                             id="{{ $berkas['id'] }}"
                                                             judul="{{ $adaLama ? 'Ganti ' : 'Pilih ' }}{{ mb_strtolower($berkas['label']) }}"
                                                             label="{{ $adaLama ? 'Ganti' : 'Pilih' }} {{ mb_strtolower($berkas['label']) }}" />
                                    </div>

                                    <p class="mt-2 text-admin-label text-ink-faint">{{ $berkas['catatan'] }}</p>

                                    @error($berkas['prop'])
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- ── Integrasi ────────────────────────────────────── --}}
                <section class="card">
                    <div class="flex items-center gap-2.5 border-b border-line px-6 py-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                     rounded-control bg-brand-wash text-brand">
                            <x-icon.admin name="chart" size="h-[18px] w-[18px]" />
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-admin-title text-ink">Integrasi</h2>
                            <p class="mt-0.5 text-admin-label text-ink-muted">Pelacakan dan zona waktu.</p>
                        </div>
                    </div>

                    <div class="p-6">

                        <div class="space-y-4">
                            <div>
                                <label for="set-ga" class="block text-admin-label text-ink-faint">
                                    ID Google Analytics
                                </label>

                                <input type="text" wire:model="google_analytics_id" id="set-ga"
                                       placeholder="G-XXXXXXXXXX"
                                       class="admin-control mt-2 font-mono">

                                <p class="mt-2 text-admin-label text-ink-faint">
                                    Dikosongkan berarti pelacakannya tidak dipasang sama sekali.
                                </p>

                                @error('google_analytics_id')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-admin-label text-ink-faint">
                                    Zona waktu <span class="text-brand">*</span>
                                </label>

                                {{-- :nullable="false" — situs selalu berada di satu zona
                                     waktu; kekosongan bukan jawaban yang sah. --}}
                                <x-admin.select model="timezone" :value="$timezone" class="mt-2"
                                                label="Zona waktu situs" :nullable="false"
                                                :options="$zona->map(fn ($label, $nilai) => [
                                                    'nilai' => $nilai, 'label' => $label,
                                                ])->values()->all()" />

                                <p class="mt-2 text-admin-label text-ink-faint">
                                    Menentukan cap waktu inquiry dan tanggal terbit berita.
                                </p>

                                @error('timezone')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
