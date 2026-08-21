@php
    /* Foto profil menempel pada bagiannya sendiri. Kunci pengaturan lama
       'about_image' tetap dibaca sebagai cadangan supaya foto yang sudah
       terlanjur diunggah tidak hilang. */
    $fotoProfil = \App\Support\IsiHalaman::gambar('profile')
        ?: ($settings['about_image'] ?? null);

    $aboutImage = !empty($fotoProfil)
        ? \Illuminate\Support\Facades\Storage::url($fotoProfil)
        : null;

    // ── Enam tonggak sejarah ─────────────────────────────────────────────
    $establishedYear = \App\Support\IsiHalaman::tahunBerdiri();
    $currentYear = (int) date('Y');

    $lastYear = max($currentYear, $establishedYear);

    $milestoneCount = 6;
    $milestones = [];

    for ($i = 0; $i < $milestoneCount; $i++) {
        $milestones[] = [
            'year'  => (int) round($establishedYear + $i * ($lastYear - $establishedYear) / ($milestoneCount - 1)),
            'title' => $isi('milestone_' . ($i + 1) . '_title', 'site.milestone_' . ($i + 1) . '_title'),
            'body'  => $isi('milestone_' . ($i + 1) . '_body',  'site.milestone_' . ($i + 1) . '_body'),
        ];
    }

    $trackInset = 100 / (2 * $milestoneCount);
    $trackSpan  = round(100 - (2 * $trackInset), 4);

    // Dirakit di sini lalu dipasang lewat direktif @style, BUKAN ditulis
    // langsung sebagai atribut style="...". Isi atribut style dibaca editor
    // sebagai CSS, dan penanda Blade di dalamnya ditandai merah sebagai galat
    // padahal keluarannya sah — galat palsu semacam itu menyamarkan galat
    // sungguhan di berkas yang sama. Direktif @style menghasilkan atribut yang
    // sama persis tanpa pernah terlihat sebagai CSS oleh editor.
    $railStyle = [sprintf('left: %.4f%%', $trackInset), sprintf('right: %.4f%%', $trackInset)];
    $fillStyle = [sprintf('left: %.4f%%', $trackInset)];
@endphp


<div>
    {{-- ══════════════════════════════════════════════════════════════════
         Bagian halaman ini digambar menurut urutan dan tampil-tidaknya yang
         diatur di panel: Halaman → Susunan halaman Profile. Bagian yang
         dimatikan tetap tersimpan isinya, ia cuma tidak digambar.
         ══════════════════════════════════════════════════════════════════ --}}
    @foreach($profilSections as $profilSection)
        @switch($profilSection['id'])
            @case('profil')
        {{-- ══════════════════════════════════════════════════════════════════
             PROFIL
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="pb-20 pt-14 md:pt-16 lg:pb-24 lg:pt-20">
            <div class="shell">

                <div class="grid gap-x-12 gap-y-8 lg:grid-cols-12">

                    <div class="lg:col-span-7">
                        {{-- Label kecil dan paragraf di kanan dulu diambil dari halaman
                             statis beralamat /page/about-us. Keduanya kini isian bagian
                             Profil; baris lamanya tetap dibaca sebagai cadangan supaya
                             tidak ada yang kosong selama masa perpindahan. --}}
                        <p class="eyebrow">
                            {{ $isi('eyebrow', 'site.nav_about', [], $page?->translated_title) }}
                        </p>

                        <h1 class="display mt-5 max-w-[20ch] text-[30px] sm:text-[36px] lg:text-[44px]">
                            {{ $isi('headline', 'site.about_headline') }}
                        </h1>
                    </div>

                    <div class="lg:col-span-5 lg:self-end">
                        @php
                            $profilBody = $isi('body', 'site.about_empty', [], $page?->translated_content);
                        @endphp

                        @if($profilBody !== strip_tags($profilBody))
                            <div class="rich max-w-[46ch]">{!! $profilBody !!}</div>
                        @else
                            <p class="lede max-w-[46ch]">{{ $profilBody }}</p>
                        @endif
                    </div>
                </div>

                {{-- ── Foto profil ────────────────────────────────────────────── --}}
                <div class="mt-12 overflow-hidden rounded-panel bg-mist-deep lg:mt-16">
                    @if($aboutImage)
                        <img src="{{ $aboutImage }}" alt="" aria-hidden="true" fetchpriority="high"
                             class="aspect-[4/3] w-full object-cover sm:aspect-[16/7]">
                    @else
                        <x-site.image-placeholder class="aspect-[4/3] w-full sm:aspect-[16/7]" icon="h-14 w-14" />
                    @endif
                </div>
            </div>
        </section>
                @break

            @case('vision_mission')
        {{-- ══════════════════════════════════════════════════════════════════
             VISI & MISI
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="section border-t border-line">
            <div class="shell">

                <div class="max-w-[46rem]">
                    <p class="eyebrow">{{ $isi('vm_eyebrow', 'site.vision_mission_eyebrow') }}</p>
                    <h2 class="display mt-5 max-w-[22ch] text-[28px] sm:text-[34px] lg:text-[40px]">
                        {{ $isi('vm_title', 'site.vision_mission_title') }}
                    </h2>
                </div>

                <div class="mt-12 grid items-start gap-6 lg:mt-14 lg:grid-cols-12 lg:gap-8">

                    {{-- ── VISI ───────────────────────────────────────────────── --}}
                    <div class="lg:sticky lg:top-[92px] lg:col-span-5">
                        <div class="rounded-panel bg-forest p-8 sm:p-10">
                            <p class="eyebrow eyebrow-invert">{{ $isi('vision_label', 'site.vision_label') }}</p>

                            <p class="mt-6 font-display text-[19px] font-extrabold leading-[1.45] tracking-[-0.01em] text-white sm:text-[21px]">
                                {{ $isi('vision_body', 'site.vision_body') }}
                            </p>
                        </div>
                    </div>

                    {{-- ── MISI ───────────────────────────────────────────────── --}}
                    <div class="lg:col-span-7">
                        <p class="eyebrow">{{ $isi('mission_label', 'site.mission_label') }}</p>

                        @php $missionCount = 3; @endphp

                        <ol class="mt-6 list-none divide-y divide-line border-y border-line">
                            @for($i = 1; $i <= $missionCount; $i++)
                                <li class="flex items-start gap-5 py-6">
                                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                                 bg-brand/10 text-[13px] font-bold text-brand"
                                          aria-hidden="true">
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    <div class="min-w-0 pt-1">
                                        <h3 class="font-display text-[16px] font-extrabold leading-snug tracking-[-0.01em] text-ink sm:text-[17px]">
                                            {{ $isi('mission_' . $i . '_title', 'site.mission_' . $i . '_title') }}
                                        </h3>
                                        <p class="mt-2 text-[14px] leading-relaxed text-ink-muted">
                                            {{ $isi('mission_' . $i . '_body', 'site.mission_' . $i . '_body') }}
                                        </p>
                                    </div>
                                </li>
                            @endfor
                        </ol>
                    </div>
                </div>
            </div>
        </section>
                @break

            @case('values')
        {{-- ══════════════════════════════════════════════════════════════════
             CORE VALUES
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="section border-t border-line">
            <div class="shell">

                @php
                    $values = [
                        ['icon' => 'integrity',      'title' => $isi('value_1_title', 'site.value_1_title'), 'body' => $isi('value_1_body', 'site.value_1_body')],
                        ['icon' => 'consistency',    'title' => $isi('value_2_title', 'site.value_2_title'), 'body' => $isi('value_2_body', 'site.value_2_body')],
                        ['icon' => 'sustainability', 'title' => $isi('value_3_title', 'site.value_3_title'), 'body' => $isi('value_3_body', 'site.value_3_body')],
                        ['icon' => 'partnership',    'title' => $isi('value_4_title', 'site.value_4_title'), 'body' => $isi('value_4_body', 'site.value_4_body')],
                    ];
                @endphp

                <div class="max-w-[46rem]">
                    <p class="eyebrow">{{ $isi('values_eyebrow', 'site.values_eyebrow') }}</p>
                    <h2 class="display mt-5 max-w-[22ch] text-[28px] sm:text-[34px] lg:text-[40px]">
                        {{ $isi('values_title', 'site.values_title') }}
                    </h2>
                </div>

                <ul class="mt-12 grid gap-px border-y border-line bg-line sm:grid-cols-2 lg:mt-14 lg:grid-cols-4">
                    @foreach($values as $value)
                        <li class="bg-canvas px-6 py-8 sm:px-7">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand/10 text-brand">
                                <x-icon.value :name="$value['icon']" />
                            </span>

                            <h3 class="mt-6 font-display text-[17px] font-extrabold leading-snug tracking-[-0.01em] text-ink">
                                {{ $value['title'] }}
                            </h3>
                            <p class="mt-2.5 text-[13px] leading-relaxed text-ink-muted">
                                {{ $value['body'] }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
                @break

            @case('history')
        {{-- ══════════════════════════════════════════════════════════════════
             SEJARAH
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="section border-t border-line"
                 x-data="{
                     active: 0,
                     total: {{ count($milestones) }},
                     go(dir) {
                         /* Dijepit di kedua ujung, tidak memutar balik ke awal:
                            ini garis waktu, dan melompat dari tonggak terakhir
                            kembali ke tahun berdiri membaca seperti kekeliruan. */
                         this.active = Math.min(Math.max(this.active + dir, 0), this.total - 1);
                     }
                 }">
            <div class="shell">

                <p class="eyebrow">{{ $isi('history_eyebrow', 'site.history_eyebrow') }}</p>

                <h2 class="display mt-5 max-w-[20ch] text-[28px] sm:text-[34px] lg:text-[40px]">
                    {{ $isi('history_title', 'site.history_title') }}
                    <span class="text-brand">{{ $isi('history_title_accent', 'site.history_title_accent') }}</span>
                </h2>

                {{-- ── Panel tonggak ──────────────────────────────────────────── --}}
                <div class="mt-12 min-h-[240px] sm:min-h-[220px] lg:mt-14">
                    @foreach($milestones as $index => $milestone)
                        <div x-show="active === {{ $index }}"
                             @if($index > 0) x-cloak @endif
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="grid gap-x-12 gap-y-6 lg:grid-cols-12">

                            <div class="lg:col-span-5">
                                <p class="font-display text-[64px] font-extrabold leading-none tracking-[-0.04em] text-ink sm:text-[80px] lg:text-[96px]">
                                    {{ $milestone['year'] }}
                                </p>
                            </div>

                            <div class="lg:col-span-6 lg:col-start-7 lg:self-end">
                                <h3 class="font-display text-[19px] font-extrabold leading-snug tracking-[-0.02em] text-ink sm:text-[22px]">
                                    {{ $milestone['title'] }}
                                </h3>
                                <p class="lede mt-3 max-w-[48ch]">{{ $milestone['body'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ── Rel waktu ──────────────────────────────────────────────── --}}
                <div class="relative mt-10 lg:mt-12">

                    <div class="absolute top-[7px] h-px bg-line"
                         @style($railStyle)
                         aria-hidden="true"></div>

                    <div class="absolute top-[7px] h-px bg-brand transition-all duration-300"
                         @style($fillStyle)
                         x-bind:style="`width: ${(active / (total - 1)) * {{ $trackSpan }}}%`"
                         aria-hidden="true"></div>

                    <ul class="relative grid grid-cols-6">
                        @foreach($milestones as $index => $milestone)
                            <li class="flex flex-col items-center">
                                <button type="button"
                                        x-on:click="active = {{ $index }}"
                                        x-bind:aria-current="active === {{ $index }} ? 'step' : 'false'"
                                        class="group flex flex-col items-center gap-3 pt-0">
                                    <span class="sr-only">{{ $milestone['year'] }} — {{ $milestone['title'] }}</span>

                                    <span x-bind:class="active === {{ $index }}
                                            ? 'h-3.5 w-3.5 bg-brand'
                                            : 'h-2 w-2 bg-line-strong group-hover:bg-ink-faint'"
                                          class="rounded-full transition-all duration-300"
                                          aria-hidden="true"></span>

                                    <span x-bind:class="active === {{ $index }} ? 'text-ink' : 'text-ink-faint'"
                                          class="text-[11px] font-bold transition-colors sm:text-[12px]"
                                          aria-hidden="true">
                                        {{ $milestone['year'] }}
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-10 flex items-center justify-center gap-3">
                    <button type="button" x-on:click="go(-1)" x-bind:disabled="active === 0"
                            x-bind:class="active === 0 ? 'opacity-30' : 'hover:border-brand hover:text-brand'"
                            aria-label="{{ $isi('history_eyebrow', 'site.history_eyebrow') }}"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line-strong
                                   text-ink transition-colors disabled:cursor-not-allowed">
                        <svg class="h-4 w-4 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button type="button" x-on:click="go(1)" x-bind:disabled="active === total - 1"
                            x-bind:class="active === total - 1 ? 'opacity-30' : 'hover:border-brand hover:text-brand'"
                            aria-label="{{ $isi('history_eyebrow', 'site.history_eyebrow') }}"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line-strong
                                   text-ink transition-colors disabled:cursor-not-allowed">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>
                @break

            @case('certification')
        {{-- ══════════════════════════════════════════════════════════════════
             KARTU MENUJU HALAMAN SERTIFIKASI
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="pb-20 pt-16 lg:pb-24 lg:pt-20">
            <div class="shell">
                <div class="rounded-panel border border-line bg-mist p-8 sm:p-10 lg:p-12">
                    <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">

                        <div class="lg:col-span-6">
                            <p class="eyebrow">{{ $isi('cert_eyebrow', 'site.certifications') }}</p>
                            <h2 class="display mt-5 max-w-[18ch] text-[24px] sm:text-[28px] lg:text-[32px]">
                                {{ $isi('cert_title', 'site.cert_card_title') }}
                            </h2>

                            <a href="{{ route('certifications.index') }}" class="btn btn-outline btn-arrow mt-8">
                                {{ $isi('cert_cta', 'site.cta_view_certifications') }}
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>

                        <div class="lg:col-span-5 lg:col-start-8">
                            <p class="lede max-w-[46ch]">{{ $isi('cert_body', 'site.cert_card_body') }}</p>

                            @if($certifications->isNotEmpty())
                                <ul class="mt-6 flex flex-wrap gap-2">
                                    @foreach($certifications as $cert)
                                        <li class="chip">{{ $cert->translated_name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
                @break

        @endswitch
    @endforeach
</div>
