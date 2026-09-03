{{-- ══════════════════════════════════════════════════════════════════
     MODAL KELOLA INQUIRY
     ══════════════════════════════════════════════════════════════════ --}}
@php
    $sebutanStatus = $this->sebutanStatus();
    $salesUsers    = $this->penggunaSales();
@endphp

    @if($showModal && $selectedInquiry)
        @php
            $pilihanStatus = collect($sebutanStatus)
                ->map(fn ($sebutan, $kunci) => ['nilai' => $kunci, 'label' => $sebutan])
                ->values()->all();

            $pilihanSales = $salesUsers
                ->map(fn ($u) => ['nilai' => $u->id, 'label' => $u->name])
                ->values()->all();

            $produkModal = $selectedInquiry->product?->translated_name;
            
            $telepon = preg_replace('/[^\d+]/', '', (string) $selectedInquiry->phone);

            $nomorWa = preg_replace('/\D/', '', (string) $selectedInquiry->phone);
            
            $perihal = 'Re: Your inquiry'
                . ($selectedInquiry->product?->translated_name
                    ? ' - ' . $selectedInquiry->product->translated_name : '');

            $tautanSurel = 'mailto:' . $selectedInquiry->email
                . '?subject=' . rawurlencode($perihal);

        @endphp

        
        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.tutupModal()"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal">
            
            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.tutupModal()"></div>

            <div class="relative flex max-h-[90vh] w-full max-w-[1000px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                {{-- Kepala --}}
                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-5">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <x-admin.avatar :name="$selectedInquiry->name" size="xl" />

                        <div class="min-w-0">
                            <h2 id="judul-modal" class="truncate text-admin-display text-ink">
                                {{ $selectedInquiry->name }}
                            </h2>
                            
                            <div class="mt-1.5 flex flex-wrap items-center gap-x-2.5 gap-y-1
                                        text-admin-label text-ink-muted">
                                <x-admin.country :code="$selectedInquiry->country_code" size="sm" />
                                <span aria-hidden="true">·</span>
                                <span class="whitespace-nowrap">
                                    Masuk {{ $selectedInquiry->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-3">
                        <x-admin.status-pill :status="$selectedInquiry->status" />

                        <button type="button" wire:click="tutupModal"
                                aria-label="Tutup"
                                class="-mr-1 shrink-0 rounded-control p-1.5 text-ink-faint
                                       transition-colors hover:bg-mist hover:text-ink">
                            <x-icon.admin name="close" size="h-4 w-4" />
                        </button>
                    </div>
                </div>

                {{-- Dua kolom --}}
                <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                            lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                    {{-- ══ KIRI ══ --}}
                    <div class="admin-scroll min-h-0 space-y-4 bg-mist/40 p-6
                                lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                        {{-- Kartu 1: profil pembeli --}}
                        <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                            <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                             rounded-control bg-brand-wash text-brand">
                                    <x-icon.admin name="user" size="h-[18px] w-[18px]" />
                                </span>

                                <div class="min-w-0">
                                    <h3 class="text-admin-title text-ink">Informasi Pembeli</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">
                                        Informasi kontak dan perusahaan pembeli.
                                    </p>
                                </div>
                            </div>
                            
                            <dl class="space-y-3.5 p-5">
                                <x-admin.datum label="Nama Lengkap" :value="$selectedInquiry->name" />

                                <x-admin.datum label="Perusahaan" :value="$selectedInquiry->company"
                                               kosong="Perorangan" />
                                
                                <x-admin.datum label="Negara Asal">
                                    <x-admin.country :code="$selectedInquiry->country_code" />
                                </x-admin.datum>

                                <x-admin.datum label="Email">
                                    
                                    <a href="mailto:{{ $selectedInquiry->email }}"
                                       class="block break-all text-admin-body leading-5 text-brand
                                              underline-offset-4 hover:underline">{{ $selectedInquiry->email }}</a>
                                </x-admin.datum>

                                <x-admin.datum label="Nomor Telepon / WhatsApp">
                                    @if(filled($telepon))
                                        <a href="tel:{{ $telepon }}"
                                           class="block break-all text-admin-body leading-5 text-brand
                                                  underline-offset-4 hover:underline">{{ $selectedInquiry->phone }}</a>
                                    @else
                                        <span class="text-admin-body leading-5 text-ink-faint">Tidak diisi</span>
                                    @endif
                                </x-admin.datum>
                            </dl>
                        </section>

                        {{-- Kartu 2: rincian permintaan --}}
                        <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                            <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                             rounded-control bg-brand-wash text-brand">
                                    <x-icon.admin name="page" size="h-[18px] w-[18px]" />
                                </span>

                                <div class="min-w-0">
                                    <h3 class="text-admin-title text-ink">Spesifikasi Permintaan</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">
                                        Detail produk, volume, dan kebutuhan pembeli.
                                    </p>
                                </div>
                            </div>

                            <dl class="space-y-3.5 p-5">
                                <x-admin.datum label="Produk" :value="$produkModal"
                                               kosong="Tanpa produk tertentu" />

                                <x-admin.datum label="Estimasi volume" :value="$selectedInquiry->volume" />

                                <x-admin.datum label="Incoterms" :value="$selectedInquiry->incoterms" />
                                
                                <x-admin.datum label="Pesan / Catatan Tambahan" :value="$selectedInquiry->message"
                                               kosong="Pembeli tidak menuliskan pesan."
                                               :blok="true" />
                            </dl>
                        </section>
                    </div>

                    {{-- ══ KANAN ══ --}}
                    <div class="admin-scroll min-h-0 border-t border-line bg-mist/40 p-6
                                lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                        {{-- Kartu 1: penanganan --}}
                        <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                            <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                             rounded-control bg-brand-wash text-brand">
                                    <x-icon.admin name="manage" size="h-[18px] w-[18px]" />
                                </span>

                                <div class="min-w-0">
                                    <h3 class="text-admin-title text-ink">Penanganan Inquiry</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">
                                        Kelola status dan tindak lanjut inquiry.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="space-y-4 p-5">
                                <div>
                                    <label class="block text-admin-label text-ink-faint">
                                        Status inquiry
                                    </label>
                                    
                                    <x-admin.select model="status" :value="$status" class="mt-2"
                                                    label="Status inquiry" placeholder="Baru"
                                                    :nullable="false" :options="$pilihanStatus" />
                                </div>

                                <div>
                                    <label class="block text-admin-label text-ink-faint">
                                        Ditangani oleh
                                    </label>
                                    
                                    <x-admin.select model="assigned_to" :value="$assigned_to" class="mt-2"
                                                    label="Ditangani oleh" placeholder="Belum ditugaskan"
                                                    :options="$pilihanSales" />
                                </div>

                                <div>
                                    <label for="catatan-internal"
                                           class="block text-admin-label text-ink-faint">
                                        Catatan internal
                                    </label>

                                    <textarea wire:model="internal_note" id="catatan-internal" rows="5"
                                              placeholder="Hasil percakapan, harga yang ditawarkan, langkah berikutnya…"
                                              class="admin-control mt-2 resize-none leading-relaxed"></textarea>

                                    <p class="mt-2 flex items-start gap-1.5 text-admin-caption text-ink-faint">
                                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M8 7.4v3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                            <circle cx="8" cy="5.2" r="0.75" fill="currentColor"/>
                                        </svg>
                                        Hanya terlihat oleh tim, tidak pernah dikirim ke pembeli.
                                    </p>
                                </div>
                            </div>
                        </section>

                        {{-- Kartu 2: balas cepat --}}
                        <section class="mt-4 overflow-hidden rounded-corner border border-line bg-canvas">
                            <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                             rounded-control bg-brand-wash text-brand">
                                    <x-icon.admin name="send" size="h-[18px] w-[18px]" />
                                </span>

                                <div class="min-w-0">
                                    <h3 class="text-admin-title text-ink">Hubungi Pembeli</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">
                                        Pilih cara cepat untuk menghubungi pembeli.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="space-y-2.5 p-5">
                                
                                <a href="{{ $tautanSurel }}" target="_blank" rel="noopener"
                                   class="group flex items-center gap-3 rounded-control border border-line
                                          bg-canvas px-3.5 py-3 transition-colors
                                          hover:border-brand hover:bg-brand-wash">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="mail" size="h-[18px] w-[18px]" />
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span class="block text-admin-strong text-ink">Balas lewat email</span>
                                        <span class="block truncate text-admin-caption text-ink-faint"
                                              title="{{ $selectedInquiry->email }}">{{ $selectedInquiry->email }}</span>
                                    </span>
                                    
                                    <x-icon.admin name="external" size="h-4 w-4"
                                                  class="shrink-0 text-ink-faint transition-colors
                                                         group-hover:text-brand" />
                                </a>

                                @if(filled($nomorWa))
                                    <a href="https://wa.me/{{ $nomorWa }}" target="_blank" rel="noopener"
                                       class="group flex items-center gap-3 rounded-control border border-line
                                              bg-canvas px-3.5 py-3 transition-colors
                                              hover:border-whatsapp hover:bg-whatsapp/5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-whatsapp/10 text-whatsapp">
                                            <x-icon.admin name="whatsapp" size="h-[18px] w-[18px]" />
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <span class="block text-admin-strong text-ink">Chat lewat WhatsApp</span>
                                            <span class="block truncate text-admin-caption text-ink-faint">
                                                {{ $selectedInquiry->phone }}
                                            </span>
                                        </span>

                                        <x-icon.admin name="external" size="h-4 w-4"
                                                      class="shrink-0 text-ink-faint transition-colors
                                                             group-hover:text-whatsapp" />
                                    </a>
                                @else
                                    
                                    <div aria-disabled="true"
                                         class="flex cursor-not-allowed items-center gap-3 rounded-control
                                                border border-dashed border-line bg-mist/60 px-3.5 py-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-mist-deep text-ink-faint">
                                            <x-icon.admin name="whatsapp" size="h-[18px] w-[18px]" />
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <span class="block text-admin-strong text-ink-muted">Chat lewat WhatsApp</span>
                                            <span class="block text-admin-caption text-ink-faint">
                                                Pembeli tidak mengisi nomor telepon.
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </section>
                    </div>
                </div>

                {{-- ── Kaki ────────────────────────────────────────────── --}}
                <div class="flex shrink-0 items-center justify-end gap-2 border-t border-line px-6 py-4">
                    <button type="button" wire:click="tutupModal"
                            class="admin-btn admin-btn-quiet">
                        Batal
                    </button>

                    <button type="button" wire:click="updateStatus" wire:loading.attr="disabled"
                            wire:target="updateStatus"
                            class="admin-btn admin-btn-brand disabled:opacity-60">
                        <svg wire:loading wire:target="updateStatus"
                             class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        Simpan perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif
