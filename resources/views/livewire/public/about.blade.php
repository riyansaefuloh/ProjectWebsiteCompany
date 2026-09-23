@php
    $fotoProfil = \App\Support\IsiHalaman::gambar('profile')
        ?: ($settings['about_image'] ?? null);

    $aboutImage = !empty($fotoProfil)
        ? \Illuminate\Support\Facades\Storage::url($fotoProfil)
        : null;

    // Enam tonggak sejarah
    $establishedYear = \App\Support\IsiHalaman::tahunBerdiri();
    $currentYear = (int) date('Y');

    $lastYear = max($currentYear, $establishedYear);

    $milestoneCount = 6;
    $milestones = [];

    $tahunDiketik = \App\Support\IsiHalaman::opsi('profile');
    $gambarTonggak = \App\Support\IsiHalaman::gambarTonggak('profile');

    for ($i = 0; $i < $milestoneCount; $i++) {
        $sendiri = $tahunDiketik['milestone_' . ($i + 1) . '_year'] ?? null;
        $berkas  = $gambarTonggak['milestone_' . ($i + 1) . '_image'] ?? null;

        $milestones[] = [
            'year'  => filled($sendiri)
                ? (int) $sendiri
                : (int) round($establishedYear + $i * ($lastYear - $establishedYear) / ($milestoneCount - 1)),
            'title' => $isi('milestone_' . ($i + 1) . '_title', 'site.milestone_' . ($i + 1) . '_title'),
            'body'  => $isi('milestone_' . ($i + 1) . '_body',  'site.milestone_' . ($i + 1) . '_body'),
            'image' => $berkas ? \Illuminate\Support\Facades\Storage::url($berkas) : null,
        ];
    }

    $trackInset = 100 / (2 * $milestoneCount);
    $trackSpan  = round(100 - (2 * $trackInset), 4);
    $fillBase   = $trackInset;
@endphp

<div>
    @foreach($profilSections as $profilSection)
        @switch($profilSection['id'])
            @case('profil')
        {{-- Section: Profil --}}
        <section class="pb-20 pt-14 md:pt-16 lg:pb-24 lg:pt-20">
            <div class="shell">

                <div class="grid gap-x-12 gap-y-8 lg:grid-cols-12">

                    <div class="lg:col-span-7">
                        
                        <p class="eyebrow">
                            {{ $isi('eyebrow', 'site.nav_about', [], $page?->translated_title) }}
                        </p>

                        <h1 class="display mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                            {!! \App\Support\Judul::sorot($isi('headline', 'site.about_headline')) !!}
                        </h1>
                    </div>

                    <div class="lg:col-span-5 lg:self-end">
                        @php
                            $profilBody = $isi('body', 'site.about_empty', [], $page?->translated_content);
                        @endphp

                        @if($profilBody !== strip_tags($profilBody))
                            <div class="rich max-w-[46ch]">{!! $profilBody !!}</div>
                        @else
                            <p class="lede max-w-[46ch] text-site-body">{{ $profilBody }}</p>
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
        
        <section class="section border-t border-line">
            <div class="shell">

                <div class="max-w-[46rem]">
                    <p class="eyebrow">{{ $isi('vm_eyebrow', 'site.vision_mission_eyebrow') }}</p>
                    <h2 class="display mt-5 max-w-[22ch] text-site-h2 text-site-forest">
                        {!! \App\Support\Judul::sorot($isi('vm_title', 'site.vision_mission_title')) !!}
                    </h2>
                </div>

                {{-- Section: Visi & Misi --}}
                <div class="mt-12 grid items-stretch gap-6 lg:mt-14 lg:grid-cols-12 lg:gap-8">

                    {{-- Visi --}}
                    <div class="flex flex-col lg:col-span-6">
                        <p class="eyebrow">{{ $isi('vision_label', 'site.vision_label') }}</p>

                        <div class="relative mt-5 flex flex-1 flex-col justify-center overflow-hidden rounded-panel
                                    bg-site-forest p-8 text-center sm:p-10">

                            <p class="relative mx-auto max-w-[30ch] font-site-display font-bold leading-[1.5] tracking-[-0.01em] text-white text-site-title">
                                {{ $isi('vision_body', 'site.vision_body') }}
                            </p>
                        </div>
                    </div>

                    {{-- Misi --}}
                    <div class="lg:col-span-6">
                        <p class="eyebrow">{{ $isi('mission_label', 'site.mission_label') }}</p>

                        @php $missionCount = 3; @endphp

                        <ol class="mt-5 list-none divide-y divide-line border-y border-line">
                            @for($i = 1; $i <= $missionCount; $i++)
                                <li class="flex items-start gap-3.5 py-4">
                                    <span aria-hidden="true" data-hias
                                          class="w-[2.2rem] shrink-0 select-none pt-px font-site-display
                                                 text-[26px] font-bold leading-none tracking-[-0.04em]
                                                 tabular-nums text-ink/[0.18]">
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}.
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="font-site-display font-bold leading-snug tracking-[-0.01em] text-site-forest text-site-lede">
                                            {{ $isi('mission_' . $i . '_title', 'site.mission_' . $i . '_title') }}
                                        </h3>
                                        <p class="mt-2 max-w-[52ch] leading-relaxed text-ink-muted text-site-body">
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
        
        <section class="section border-t border-line">
            <div class="shell">

                @php
                    
                    $values = collect(['integrity', 'quality', 'partnership', 'responsibility'])
                        ->map(fn ($ikon, $i) => [
                            'icon'  => $ikon,
                            'title' => $isi('value_' . ($i + 1) . '_title', 'site.value_' . ($i + 1) . '_title'),
                            'body'  => $isi('value_' . ($i + 1) . '_body',  'site.value_' . ($i + 1) . '_body'),
                        ])
                        ->all();

                    $nilaiBody = $isi('values_body', 'site.values_body');
                @endphp

                {{-- Section: Core Values --}}
                <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                    <div class="lg:col-span-6">
                        <p class="eyebrow">{{ $isi('values_eyebrow', 'site.values_eyebrow') }}</p>
                        <h2 class="display mt-5 max-w-[16ch] text-site-h2 text-site-forest">
                            {!! \App\Support\Judul::sorot($isi('values_title', 'site.values_title')) !!}
                        </h2>
                    </div>

                    <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                        @if($nilaiBody !== strip_tags($nilaiBody))
                            <div class="rich max-w-[46ch]">{!! $nilaiBody !!}</div>
                        @else
                            <p class="lede max-w-[46ch] text-site-body">{{ $nilaiBody }}</p>
                        @endif
                    </div>
                </div>

                {{-- List Values --}}
                <ul class="mt-12 -mx-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-6 pb-2
                           [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:-mx-8 sm:px-8
                           lg:mx-0 lg:mt-14 lg:overflow-visible lg:px-0">
                    @foreach($values as $value)
                        <li class="group w-[78%] shrink-0 snap-start sm:w-[calc(50%-0.625rem)]
                                   lg:w-auto lg:min-w-0 lg:shrink lg:basis-0 lg:grow
                                   lg:transition-[flex-grow] lg:duration-500 lg:ease-out
                                   lg:hover:grow-[2] lg:focus-within:grow-[2]">
                            <div class="card relative flex h-full min-h-[256px] flex-col justify-end p-6
                                        transition-colors duration-300 hover:border-forest hover:bg-forest
                                        sm:min-h-[288px] lg:min-h-[332px]">

                                <span aria-hidden="true" data-hias
                                      class="pointer-events-none absolute left-6 top-3 select-none
                                             font-site-display text-[76px] font-bold leading-none tracking-[-0.04em]
                                             text-ink/[0.07] transition-colors duration-300
                                             group-hover:text-white/[0.13]">
                                    {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.
                                </span>

                                <span class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center
                                             rounded-full bg-site-forest text-site-gilt
                                             transition-colors duration-300
                                             group-hover:bg-site-gilt group-hover:text-site-forest">
                                    <x-icon.value :name="$value['icon']" size="h-5 w-5" />
                                </span>

                                <h3 class="relative mt-5 font-site-display font-bold leading-snug tracking-[-0.01em]
                                           text-site-forest transition-colors duration-300 group-hover:text-white text-site-title">
                                    {{ $value['title'] }}
                                </h3>

                                <div class="relative grid grid-rows-[1fr] transition-all duration-500 ease-out
                                            lg:grid-rows-[0fr] lg:opacity-0
                                            lg:group-hover:grid-rows-[1fr] lg:group-hover:opacity-100
                                            lg:group-focus-within:grid-rows-[1fr] lg:group-focus-within:opacity-100">
                                    <p class="overflow-hidden leading-relaxed text-ink-muted transition-colors duration-300
                                              group-hover:text-white/70 text-site-body">
                                        <span class="mt-2.5 block">{{ $value['body'] }}</span>
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
                @break

            @case('history')
        {{-- Section: Sejarah / Timeline --}}
        <section class="section border-t border-line"
                 x-data="{
                     active: 0,
                     total: {{ count($milestones) }},
                     go(dir) {
                         this.active = Math.min(Math.max(this.active + dir, 0), this.total - 1);
                     }
                 }">
            <div class="shell">

                <p class="eyebrow">{{ $isi('history_eyebrow', 'site.history_eyebrow') }}</p>

                <h2 class="display mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('history_title', 'site.history_title')) !!}
                </h2>

                {{-- Timeline Navigation --}}
                <div class="relative mt-12 lg:mt-14">

                    <div class="absolute inset-x-0 top-[7px] h-px bg-line" aria-hidden="true"></div>

                    <div class="absolute left-0 top-[7px] h-px bg-brand
                                transition-[width] duration-200 ease-out"
                         x-bind:style="`width: ${ {{ $fillBase }} + (active / (total - 1)) * {{ $trackSpan }} }%`"
                         aria-hidden="true"></div>

                    <ul class="relative grid grid-cols-6">
                        @foreach($milestones as $index => $milestone)
                            <li class="flex flex-col items-center">
                                <button type="button"
                                        x-on:click="active = {{ $index }}"
                                        x-bind:aria-current="active === {{ $index }} ? 'step' : 'false'"
                                        class="group flex flex-col items-center gap-3 pt-0">
                                    <span class="sr-only">{{ $milestone['year'] }} — {{ $milestone['title'] }}</span>

                                    <span class="flex h-3.5 w-3.5 items-center justify-center" aria-hidden="true">
                                        <span x-bind:class="active === {{ $index }}
                                                ? 'scale-100 bg-brand'
                                                : ({{ $index }} < active
                                                    ? 'scale-[0.57] bg-brand'
                                                    : 'scale-[0.57] bg-line-strong group-hover:bg-ink-faint')"
                                              class="block h-3.5 w-3.5 rounded-full
                                                     transition-[transform,background-color] duration-200 ease-out"></span>
                                    </span>

                                    <span x-bind:class="active === {{ $index }} ? 'text-ink' : 'text-ink-faint'"
                                          class="font-semibold transition-colors duration-200 text-site-micro"
                                          aria-hidden="true">
                                        {{ $milestone['year'] }}
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Panel Milestone --}}
                <div class="mt-10 min-h-[240px] sm:min-h-[220px] lg:mt-12">
                    @foreach($milestones as $index => $milestone)
                        <div x-show="active === {{ $index }}"
                             @if($index > 0) x-cloak @endif
                             x-transition:enter="transition ease-out duration-[180ms]"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="grid gap-x-12 gap-y-6 lg:grid-cols-12">

                            @if($milestone['image'])
                                <div class="lg:col-span-5">
                                    <div class="max-w-[420px] overflow-hidden rounded-corner border border-line
                                                bg-site-paper lg:h-full">
                                        <img src="{{ $milestone['image'] }}" alt="{{ $milestone['title'] }}"
                                             loading="lazy"
                                             class="aspect-[2/1] w-full object-cover lg:aspect-auto lg:h-full">
                                    </div>
                                </div>
                            @endif

                            <div @class([
                                'lg:col-span-6 lg:col-start-7' => $milestone['image'],
                                'lg:col-span-8' => ! $milestone['image'],
                            ])>
                                <p class="font-site-display font-bold leading-none tracking-[-0.04em] text-site-forest text-site-metric-xl">
                                    {{ $milestone['year'] }}
                                </p>

                                <h3 class="mt-5 font-site-display font-bold leading-snug tracking-[-0.02em] text-site-forest text-site-h3">
                                    {{ $milestone['title'] }}
                                </h3>
                                <p class="lede mt-3 max-w-[48ch] text-site-body">{{ $milestone['body'] }}</p>
                            </div>
                        </div>
                    @endforeach
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
        {{-- Section: Sertifikasi --}}
        <section class="pb-20 pt-16 lg:pb-24 lg:pt-20">
            <div class="shell">
                <div class="rounded-panel border border-line bg-site-paper p-8 sm:p-10 lg:p-12">

                    <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                        <div class="lg:col-span-6">
                            <p class="eyebrow">{{ $isi('cert_eyebrow', 'site.certifications') }}</p>
                            <h2 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                                {!! \App\Support\Judul::sorot($isi('cert_title', 'site.cert_card_title')) !!}
                            </h2>
                        </div>

                        <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                            <p class="lede max-w-[46ch] text-site-body">{{ $isi('cert_body', 'site.cert_card_body') }}</p>

                            <a href="{{ route('certifications.index') }}"
                               class="group mt-7 inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                      bg-site-forest text-white
                                      font-site-body text-site-small font-semibold whitespace-nowrap
                                      transition-colors duration-300 hover:bg-site-brand-deep">
                                {{ $isi('cert_cta', 'site.cta_view_certifications') }}
                                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                             bg-site-gilt text-site-forest transition-transform duration-200
                                             group-hover:translate-x-0.5">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
                @break

        @endswitch
    @endforeach
</div>
