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
         * Semua tampilan publik — kaki situs, meta halaman, dan formulir
         * inquiry — kini membaca 'contact_email' lebih dulu, yaitu isian di
         * halaman ini, dan baru jatuh ke 'company_email' kalau kosong.
         *
         * 'company_email' masih menyimpan alamat lamanya. Selama isinya beda,
         * itu alamat cadangan yang akan muncul begitu isian di atas dikosongkan
         * — perlu disebut, karena dari panel kunci itu tidak kelihatan.
         */
        $emailBentrok = filled($emailSitus) && $emailSitus !== $contact_email;

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
                <h1 class="font-ui text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[26px]">
                    Pengaturan
                </h1>
                {{-- Yang diatur di sini identitas dan sambungan situs, bukan
                     isi halamannya. Isi halaman publik diatur di menu Halaman. --}}
                <p class="mt-1.5 text-[13px] text-ink-muted">
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

                <p class="min-w-0 flex-1 pt-1 text-[13px] font-semibold text-brand-deep">
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
                <section class="card p-6">
                    <h2 class="mb-5 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                        Identitas perusahaan
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="set-nama" class="block text-[12px] font-semibold text-ink-faint">
                                Nama perusahaan <span class="text-brand">*</span>
                            </label>

                            <input type="text" wire:model="company_name" id="set-nama"
                                   class="admin-control mt-2">

                            @error('company_name')
                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="set-alamat" class="block text-[12px] font-semibold text-ink-faint">
                                Alamat <span class="text-brand">*</span>
                            </label>

                            <textarea wire:model="company_address" id="set-alamat" rows="3"
                                      class="admin-control mt-2 resize-none leading-relaxed"></textarea>

                            @error('company_address')
                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="set-peta" class="block text-[12px] font-semibold text-ink-faint">
                                Tautan sematan Google Maps
                            </label>

                            <input type="url" wire:model="google_map_url" id="set-peta"
                                   placeholder="https://www.google.com/maps/embed?…"
                                   class="admin-control mt-2">

                            {{-- Yang dibutuhkan alamat SEMATAN, bukan tautan
                                 biasa dari bilah alamat. Menempelkan yang salah
                                 membuat peta di halaman kontak kosong tanpa
                                 pesan apa pun. --}}
                            <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                Ambil dari Google Maps → Bagikan → Sematkan peta → salin
                                bagian <span class="font-semibold text-ink-muted">src</span>-nya.
                                Diawali <span class="font-semibold text-ink-muted">https://www.google.com/maps/embed</span>.
                            </p>

                            @error('google_map_url')
                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ── Kontak ───────────────────────────────────────── --}}
                <section class="card p-6">
                    <h2 class="mb-5 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                        Kontak
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="set-email" class="block text-[12px] font-semibold text-ink-faint">
                                Email kontak <span class="text-brand">*</span>
                            </label>

                            <input type="email" wire:model="contact_email" id="set-email"
                                   class="admin-control mt-2">

                            @error('contact_email')
                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                            @enderror

                            {{-- Keadaan yang perlu disebut, bukan disembunyikan:
                                 kunci lama masih menyimpan alamat yang lain, dan
                                 alamat itu yang dipakai kalau isian di atas kosong. --}}
                            @if($emailBentrok)
                                <div class="mt-2 flex items-start gap-2.5 rounded-control
                                            border border-status-new/30 bg-status-new/5 px-3.5 py-2.5">
                                    <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center
                                                 rounded-full bg-status-new text-white">
                                        <svg class="h-2.5 w-2.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M8 4.4v4.4M8 11.4v.2" stroke="currentColor"
                                                  stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </span>

                                    <p class="min-w-0 text-[12px] leading-relaxed text-ink-muted">
                                        Seluruh situs publik sekarang memakai isian di atas.
                                        Tapi kunci lama
                                        <span class="font-semibold text-ink">company_email</span>
                                        masih menyimpan
                                        <span class="font-semibold text-ink">{{ $emailSitus }}</span>,
                                        dan alamat itulah yang muncul kalau isian di atas
                                        dikosongkan.
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Dua nomor berdampingan: keduanya tampil di kaki situs
                             publik, dan yang membedakannya cuma cara menghubungi. --}}
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="set-wa" class="block text-[12px] font-semibold text-ink-faint">
                                    Nomor WhatsApp <span class="text-brand">*</span>
                                </label>

                                <input type="text" wire:model.live.debounce.500ms="whatsapp_number" id="set-wa"
                                       placeholder="6281234567890"
                                       class="admin-control mt-2">

                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                    Format internasional tanpa spasi — inilah yang dirangkai
                                    jadi tautan wa.me.
                                </p>

                                @error('whatsapp_number')
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="set-telepon" class="block text-[12px] font-semibold text-ink-faint">
                                    Nomor telepon
                                </label>

                                <input type="text" wire:model.live.debounce.500ms="company_phone" id="set-telepon"
                                       placeholder="+62 21 1234 5678"
                                       class="admin-control mt-2">

                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                    Nomor yang ditelepon biasa. Dikosongkan berarti kaki situs
                                    hanya menampilkan WhatsApp.
                                </p>

                                @error('company_phone')
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Kaki situs menyembunyikan telepon yang angkanya sama
                             persis dengan WhatsApp — menampilkan nomor yang sama
                             dua kali cuma membuat pembaca mengira salah satunya
                             salah ketik. Perlu disebut, karena dari panel ini
                             kedua isiannya tampak terisi normal. --}}
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

                                <p class="min-w-0 text-[12px] leading-relaxed text-ink-muted">
                                    Kedua nomornya sama. Kaki situs publik akan menampilkan
                                    <span class="font-semibold text-ink">satu nomor saja</span> —
                                    beda keduanya supaya WhatsApp dan telepon tampil berdampingan.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- ── Tautan sosial ────────────────────────────────── --}}
                <section class="card p-6">
                    <h2 class="mb-5 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                        Tautan sosial
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach([
                            ['prop' => 'facebook_url',  'id' => 'set-fb', 'label' => 'Facebook',  'contoh' => 'https://facebook.com/…'],
                            ['prop' => 'instagram_url', 'id' => 'set-ig', 'label' => 'Instagram', 'contoh' => 'https://instagram.com/…'],
                            ['prop' => 'linkedin_url',  'id' => 'set-li', 'label' => 'LinkedIn',  'contoh' => 'https://linkedin.com/company/…'],
                        ] as $sosial)
                            <div>
                                <label for="{{ $sosial['id'] }}" class="block text-[12px] font-semibold text-ink-faint">
                                    {{ $sosial['label'] }}
                                </label>

                                <input type="url" wire:model="{{ $sosial['prop'] }}" id="{{ $sosial['id'] }}"
                                       placeholder="{{ $sosial['contoh'] }}"
                                       class="admin-control mt-2">

                                @error($sosial['prop'])
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                        Dikosongkan berarti ikonnya tidak digambar di kaki situs publik.
                    </p>
                </section>
            </div>

            {{-- ══ KANAN ══ --}}
            <div class="space-y-6">

                {{-- ── Logo & ikon ──────────────────────────────────── --}}
                <section class="card p-6">
                    <h2 class="mb-5 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                        Logo &amp; ikon
                    </h2>

                    <div class="space-y-5">
                        @foreach([
                            ['prop' => 'logo',    'id' => 'set-logo',    'lama' => $existing_logo,
                             'label' => 'Logo',   'nisbah' => 'aspect-[3/1]', 'catatan' => 'PNG atau SVG berlatar tembus, maksimal 2 MB.'],
                            ['prop' => 'favicon', 'id' => 'set-favicon', 'lama' => $existing_favicon,
                             'label' => 'Favicon','nisbah' => 'aspect-square', 'catatan' => 'Persegi, minimal 64×64 piksel, maksimal 1 MB.'],
                        ] as $berkas)
                            <div>
                                <span class="block text-[12px] font-semibold text-ink-faint">{{ $berkas['label'] }}</span>

                                @php
                                    /* Alamatnya dicek benar-benar ada di disk, bukan cuma
                                       kolomnya terisi: <img> beralamat mati menggambar ikon
                                       rusak, dan itu terbaca sebagai logonya yang rusak. */
                                    $adaLama = filled($berkas['lama'])
                                        && \Illuminate\Support\Facades\Storage::disk('public')->exists($berkas['lama']);
                                @endphp

                                <div class="mt-2 grid grid-cols-2 gap-3">
                                    @if($adaLama)
                                        <div class="{{ $berkas['nisbah'] }} flex items-center justify-center
                                                    overflow-hidden rounded-control border border-line bg-mist/40 p-2">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($berkas['lama']) }}"
                                                 alt="" class="max-h-full max-w-full object-contain">
                                        </div>
                                    @elseif(filled($berkas['lama']))
                                        <div class="{{ $berkas['nisbah'] }} flex flex-col items-center justify-center
                                                    gap-1 rounded-control border border-status-rejected/30
                                                    bg-status-rejected/5 px-2 text-center">
                                            <span class="text-[11px] font-bold text-status-rejected">Berkas hilang</span>
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

                                        <div class="relative {{ $berkas['nisbah'] }} flex items-center justify-center
                                                    overflow-hidden rounded-control border border-dashed
                                                    border-brand/50 bg-brand-wash p-2">
                                            @if($pratinjau)
                                                <img src="{{ $pratinjau }}" alt=""
                                                     class="max-h-full max-w-full object-contain">
                                            @else
                                                <span class="px-2 text-center text-[11px] leading-snug text-ink-muted">
                                                    {{ $this->{$berkas['prop']}->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-1.5 top-1.5 rounded-full bg-brand px-1.5
                                                         text-[10px] font-bold text-white">Baru</span>
                                        </div>
                                    @endif

                                    <label title="{{ $adaLama ? 'Ganti ' . $berkas['label'] : 'Pilih ' . $berkas['label'] }}"
                                           class="{{ $berkas['nisbah'] }} flex cursor-pointer items-center justify-center
                                                  rounded-control border-2 border-dashed border-line-strong
                                                  bg-mist/40 text-ink-faint transition-colors
                                                  hover:border-brand hover:bg-brand-wash hover:text-brand
                                                  focus-within:border-brand focus-within:text-brand">

                                        <input type="file" wire:model="{{ $berkas['prop'] }}" id="{{ $berkas['id'] }}"
                                               accept="image/*"
                                               aria-label="{{ $adaLama ? 'Ganti ' . $berkas['label'] : 'Pilih ' . $berkas['label'] }}"
                                               class="sr-only">

                                        <span wire:loading.remove wire:target="{{ $berkas['prop'] }}">
                                            <svg class="h-7 w-7" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                                <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                      stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>

                                        <svg wire:loading wire:target="{{ $berkas['prop'] }}"
                                             class="h-6 w-6 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </label>
                                </div>

                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">{{ $berkas['catatan'] }}</p>

                                @error($berkas['prop'])
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- ── Integrasi ────────────────────────────────────── --}}
                <section class="card p-6">
                    <h2 class="mb-5 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                        Integrasi
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="set-ga" class="block text-[12px] font-semibold text-ink-faint">
                                ID Google Analytics
                            </label>

                            <input type="text" wire:model="google_analytics_id" id="set-ga"
                                   placeholder="G-XXXXXXXXXX"
                                   class="admin-control mt-2 font-mono">

                            <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                Dikosongkan berarti pelacakannya tidak dipasang sama sekali.
                            </p>

                            @error('google_analytics_id')
                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-ink-faint">
                                Zona waktu <span class="text-brand">*</span>
                            </label>

                            {{-- :nullable="false" — situs selalu berada di satu zona
                                 waktu; kekosongan bukan jawaban yang sah. --}}
                            <x-admin.select model="timezone" :value="$timezone" class="mt-2"
                                            label="Zona waktu situs" :nullable="false"
                                            :options="$zona->map(fn ($label, $nilai) => [
                                                'nilai' => $nilai, 'label' => $label,
                                            ])->values()->all()" />

                            <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                Menentukan cap waktu inquiry dan tanggal terbit berita.
                            </p>

                            @error('timezone')
                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
