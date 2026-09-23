<div class="mx-auto max-w-[1400px]">

    @php
        $zona = collect([
            'Asia/Jakarta'  => 'Asia/Jakarta — WIB',
            'Asia/Makassar' => 'Asia/Makassar — WITA',
            'Asia/Jayapura' => 'Asia/Jayapura — WIT',
            'UTC'           => 'UTC',
        ]);

        if (filled($timezone) && ! $zona->has($timezone)) {
            $zona = $zona->prepend($timezone, $timezone);
        }

    @endphp

    {{-- ── KEPALA HALAMAN ────────────────────────────────────────────── --}}
    <form wire:submit.prevent="save">

        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-admin-display text-heading">
                    Pengaturan
                </h1>
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

        {{-- ── PESAN SETELAH TERSIMPAN ───────────────────────────────────── --}}
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

        {{-- ── PENGATURAN UTAMA ────────────────────────────────────────── --}}
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
                            <h2 class="text-admin-title text-heading">Identitas perusahaan</h2>
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
                            <h2 class="text-admin-title text-heading">Kontak</h2>
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

                            <div>
                                <label for="set-wa" class="block text-admin-label text-ink-faint">
                                    Nomor WhatsApp <span class="text-brand">*</span>
                                </label>

                                <input type="text" wire:model="whatsapp_number" id="set-wa"
                                       placeholder="6281234567890"
                                       class="admin-control mt-2 sm:max-w-xs">
                                
                                @error('whatsapp_number')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="border-t border-line pt-4">
                                <label for="set-jam" class="block text-admin-label text-ink-faint">
                                    Jam operasional
                                </label>

                                <input type="text" wire:model="hours_weekly" id="set-jam"
                                       placeholder="08.00 – 17.00 WIB"
                                       class="admin-control mt-2 sm:max-w-xs">

                                <p class="admin-hint">
                                    Berlaku Senin sampai Sabtu.
                                </p>

                                @error('hours_weekly')
                                    <p class="mt-1.5 text-admin-label text-danger">{{ $message }}</p>
                                @enderror
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
                            <h2 class="text-admin-title text-heading">Tautan sosial</h2>
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
                            <h2 class="text-admin-title text-heading">Logo &amp; ikon</h2>
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
                                        $adaLama = filled($berkas['lama'])
                                            && \Illuminate\Support\Facades\Storage::disk('public')->exists($berkas['lama']);
                                    @endphp

                                    <div class="mt-2 grid grid-cols-2 gap-3">
                                        @if($adaLama)
                                            <div class="group/ubin relative aspect-square overflow-hidden
                                                        rounded-control border border-line bg-mist/40">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($berkas['lama']) }}"
                                                     alt="" class="block h-full w-full object-contain p-3">

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

                                    <p class="admin-hint">{{ $berkas['catatan'] }}</p>

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
                            <h2 class="text-admin-title text-heading">Integrasi</h2>
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
                                
                                @error('google_analytics_id')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-admin-label text-ink-faint">
                                    Zona waktu <span class="text-brand">*</span>
                                </label>

                                <x-admin.select model="timezone" :value="$timezone" class="mt-2"
                                                label="Zona waktu situs" :nullable="false"
                                                :options="$zona->map(fn ($label, $nilai) => [
                                                    'nilai' => $nilai, 'label' => $label,
                                                ])->values()->all()" />
                                
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
