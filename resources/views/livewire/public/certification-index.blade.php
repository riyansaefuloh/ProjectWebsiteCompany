<div>
    
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            @php $sertBody = $isi('body', 'site.page_certifications_sub'); @endphp

            <div class="mx-auto max-w-[44rem] text-center">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.certifications') }}</p>

                <h1 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_certifications')) !!}
                </h1>

                @if($sertBody !== strip_tags($sertBody))
                    <div class="rich mx-auto mt-5 max-w-[56ch]">{!! $sertBody !!}</div>
                @else
                    <p class="lede mx-auto mt-5 max-w-[56ch] text-site-body">{{ $sertBody }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="pb-20 lg:pb-24">
        <div class="shell">
            @if($certifications->isNotEmpty())
                <h2 class="display text-site-h3 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.all_certifications')) !!}
                </h2>

                <ul class="mt-8 grid auto-rows-fr gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($certifications as $cert)
                        @php
                            $logo = $cert->getFirstMediaUrl('logos', 'thumb')
                                 ?: $cert->getFirstMediaUrl('logos');
                        @endphp

                        <li class="card group flex h-full min-h-[148px] flex-col items-center justify-center
                                   p-5 text-center lg:min-h-[160px]">

                            <span class="flex h-10 w-[116px] shrink-0 items-center justify-center">
                                @if($logo)
                                    <img src="{{ $logo }}" alt="{{ $cert->translated_name }}" loading="lazy"
                                         class="max-h-full max-w-full object-contain transition duration-300
                                                lg:grayscale lg:opacity-70
                                                lg:group-hover:opacity-100 lg:group-hover:grayscale-0">
                                @else
                                    <span class="font-site-display text-site-small font-bold leading-snug text-site-forest">
                                        {{ $cert->translated_name }}
                                    </span>
                                @endif
                            </span>

                            <div class="grid grid-rows-[1fr] transition-all duration-300 ease-out
                                        lg:grid-rows-[0fr] lg:opacity-0
                                        lg:group-hover:grid-rows-[1fr] lg:group-hover:opacity-100
                                        lg:group-focus-within:grid-rows-[1fr] lg:group-focus-within:opacity-100">
                                <div class="overflow-hidden">
                                    <p class="mt-3 font-site-display font-bold leading-snug tracking-[-0.01em]
                                              text-site-forest text-site-body">
                                        {{ $cert->translated_name }}
                                    </p>

                                    @if($cert->issuer)
                                        <p class="mt-1 text-ink-muted text-site-small">
                                            {{ __('site.issued_by') }} {{ $cert->issuer }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center text-site-body">
                    {{ $isi('empty', 'site.no_certifications') }}
                </p>
            @endif
        </div>
    </section>
</div>
