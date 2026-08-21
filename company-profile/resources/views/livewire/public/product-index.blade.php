@php
    $hasFilters = filled($search) || filled($category);
@endphp

<div>
    {{-- ══════════════════════════════════════════════════════════════════
         HEADER HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-7">
                    <p class="eyebrow">{{ $isi('eyebrow', 'site.home_section_products') }}</p>
                    <h1 class="display mt-5 max-w-[16ch] text-[32px] sm:text-[38px] lg:text-[46px]">
                        {{ $isi('title', 'site.page_products') }}
                    </h1>
                    <p class="lede mt-6 max-w-[52ch]">{{ $isi('body', 'site.page_products_sub') }}</p>
                </div>

                {{-- ── Unduhan katalog PDF ────────────────────────────────── --}}
                <div class="lg:col-span-4 lg:col-start-9 lg:self-end">
                    <div class="rounded-corner border border-line bg-mist p-6">
                        <p class="font-display text-[16px] font-extrabold tracking-[-0.01em] text-ink">
                            {{ $isi('catalog_title', 'site.offline_catalog') }}
                        </p>
                        <p class="mt-2 text-[13px] leading-relaxed text-ink-muted">
                            {{ $isi('catalog_body', 'site.offline_catalog_sub') }}
                        </p>
                        <a href="{{ route('download.catalog.form') }}" class="btn btn-brand btn-sm mt-5">
                            {{ __('site.download_pdf') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section class="sticky top-[76px] z-30 border-y border-line bg-canvas/95 py-4 backdrop-blur">
        <div class="shell">
            {{-- Baris atas: pencarian + urutan. --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                <div class="relative flex-1">
                    <label for="product-search" class="sr-only">{{ __('site.search_products') }}</label>
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="7.2" cy="7.2" r="4.8" stroke="currentColor" stroke-width="1.5"/>
                        <path d="m10.8 10.8 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>

                    <input id="product-search" type="search"
                           wire:model.live.debounce.300ms="search"
                           placeholder="{{ __('site.search_placeholder') }}"
                           class="field mt-0 rounded-full pl-11 pr-4">
                </div>

                <div class="shrink-0">
                    <label for="product-sort" class="sr-only">{{ __('site.sort_by') }}</label>
                    <select id="product-sort" wire:model.live="sort"
                            class="field field-select mt-0 w-full rounded-full sm:w-[13rem]">
                        <option value="featured">{{ __('site.sort_featured') }}</option>
                        <option value="newest">{{ __('site.sort_newest') }}</option>
                        <option value="name_asc">{{ __('site.sort_name_asc') }}</option>
                        <option value="name_desc">{{ __('site.sort_name_desc') }}</option>
                    </select>
                </div>
            </div>

            {{-- ── Baris bawah: kategori sebagai deretan pill ──────────────── --}}
            <fieldset class="mt-3 flex flex-wrap items-center gap-2">
                <legend class="sr-only">{{ __('site.category') }}</legend>

                <button type="button" wire:click="selectCategory('')"
                        aria-pressed="{{ $category === '' ? 'true' : 'false' }}"
                        @class([
                            'inline-flex items-center gap-1.5 rounded-full border px-4 py-2 text-[13px] font-bold transition-colors',
                            'border-brand bg-brand text-white' => $category === '',
                            'border-line text-ink-muted hover:border-line-strong hover:text-ink' => $category !== '',
                        ])>
                    {{ __('site.all_categories') }}
                </button>

                @foreach($categories as $cat)
                    @php $isActive = $category === $cat->slug; @endphp

                    <button type="button" wire:click="selectCategory('{{ $cat->slug }}')"
                            aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                            @class([
                                'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-[13px] font-bold transition-colors',
                                'border-brand bg-brand text-white' => $isActive,
                                'border-line text-ink-muted hover:border-line-strong hover:text-ink' => ! $isActive,
                            ])>
                        {{ $cat->translated_name }}

                        <span @class([
                            'text-[12px] font-semibold',
                            'text-white/60' => $isActive,
                            'text-ink-faint' => ! $isActive,
                        ])>{{ $cat->products_count }}</span>
                    </button>
                @endforeach
            </fieldset>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         HASIL
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 pt-8 lg:pb-24">
        <div class="shell">

            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-[14px] text-ink-muted" aria-live="polite">
                    {{ trans_choice('site.products_count', $products->total(), ['count' => $products->total()]) }}
                </p>

                @if($hasFilters)
                    <button type="button" wire:click="resetFilters"
                            class="inline-flex items-center gap-2 text-[13px] font-bold text-brand transition-colors hover:text-brand-deep">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        {{ __('site.reset_filters') }}
                    </button>
                @endif
            </div>

            <div wire:loading.class="opacity-40" class="transition-opacity duration-200">
                @if($products->isNotEmpty())
                    <ul class="mt-8 grid auto-rows-fr gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($products as $product)
                            <li class="flex" wire:key="product-{{ $product->id }}">
                                <x-site.product-card :product="$product" class="w-full" />
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-12">
                        {{ $products->links('vendor.pagination.site') }}
                    </div>
                @else
                    <div class="mt-8 rounded-corner border border-dashed border-line px-6 py-20 text-center">
                        <p class="font-display text-[18px] font-extrabold text-ink">
                            {{ $isi('empty', 'site.no_products_found') }}
                        </p>

                        @if($hasFilters)
                            <button type="button" wire:click="resetFilters" class="btn btn-outline mt-6">
                                {{ __('site.reset_filters') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
