<x-layouts.public>
    <section class="pb-20 pt-14 md:pt-16 lg:pb-24 lg:pt-20">
        <div class="shell">

            <a href="{{ route('products.index') }}" class="link-arrow">
                <span class="rotate-180">
                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ __('site.page_products') }}
            </a>

            <div class="mt-8 grid items-start gap-6 lg:mt-10 lg:grid-cols-12 lg:gap-8">

                <div class="lg:col-span-6">
                    <p class="eyebrow">{{ __('site.offline_catalog') }}</p>

                    <h1 class="display mt-5 max-w-[16ch] text-[30px] sm:text-[36px] lg:text-[42px]">
                        {{ __('site.catalog_headline') }}
                    </h1>

                    <p class="lede mt-6 max-w-[50ch]">
                        {{ __('site.catalog_body', ['count' => $productCount, 'categories' => $categoryCount]) }}
                    </p>

                    <p class="mt-8 flex items-start gap-3 text-[13px] leading-relaxed text-ink-muted">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.4"/>
                            <path d="M8 4.4V8l2.4 1.6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('site.catalog_generated') }}
                    </p>
                </div>

                {{-- ── Formulir ────────────────────────────────────────────── --}}
                <div class="lg:col-span-5 lg:col-start-8">
                    <div class="card p-7 sm:p-9">

                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-corner bg-brand/10 text-brand"
                              aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                <path d="M13.4 2.8H6.8a2 2 0 0 0-2 2v14.4a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V8.6l-5.8-5.8Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M13.4 2.8v5.8h5.8" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </span>

                        <form method="POST" action="{{ route('download.catalog') }}" class="mt-6">
                            @csrf

                            <label for="catalog-email" class="field-label">{{ __('site.field_email') }} *</label>

                            <input id="catalog-email" type="email" name="email" required
                                   value="{{ old('email') }}"
                                   placeholder="{{ __('site.field_email_ph') }}"
                                   class="field @error('email') border-danger @enderror">

                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror

                            <button type="submit" class="btn btn-brand mt-6 w-full">
                                {{ __('site.download_pdf') }}
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 3v8m0 0L4.8 7.8M8 11l3.2-3.2M3 13h10" stroke="currentColor"
                                          stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <p class="mt-4 text-[12px] leading-relaxed text-ink-faint">
                                {{ __('site.catalog_privacy') }}
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
