@props([
    'name',
    'value' => '',
    'options' => [],
    'placeholder' => '',
    'required'    => false,
    'invalid'     => false,
    'ambangCari' => 12,
])

@php
    $id       = 'pilih-' . $name;
    $terpilih = collect($options)->firstWhere('nilai', $value);
    $pakaiCari = count($options) > $ambangCari;
@endphp

{{-- ── MENU PILIH ────────────────────────────────────────────────────────── --}}
<div x-data="{
        buka: false,
        nilai: @js($value),
        label: @js($terpilih['label'] ?? ''),
        cari: '',

        pilih(n, l) {
            this.nilai = n;
            this.label = l;
            this.tutup();

            this.$refs.sumber.value = n;
            this.$refs.sumber.dispatchEvent(new Event('input', { bubbles: true }));
        },

        tutup() {
            this.buka = false;
            this.cari = '';
            this.$refs.tombol.focus();
        },

        geser(arah) {
            const butir = [...this.$refs.daftar.querySelectorAll('[role=option]')]
                .filter((b) => b.offsetParent !== null);

            if (! butir.length) return;

            const kini = butir.indexOf(document.activeElement);
            const tuju = kini < 0
                ? (arah > 0 ? 0 : butir.length - 1)
                : (kini + arah + butir.length) % butir.length;

            butir[tuju].focus();
        },
     }"
     x-on:keydown.escape.prevent.stop="buka && tutup()"
     x-on:click.outside="buka = false"
     class="relative mt-2">

    <input type="text" x-ref="sumber" wire:model="{{ $name }}" tabindex="-1"
           aria-hidden="true" class="sr-only" @if($required) required @endif>

    <button type="button" x-ref="tombol" id="{{ $id }}"
            x-on:click="buka = ! buka"
            x-on:keydown.arrow-down.prevent="buka = true; $nextTick(() => geser(1))"
            x-on:keydown.arrow-up.prevent="buka = true; $nextTick(() => geser(-1))"
            role="combobox" aria-haspopup="listbox"
            x-bind:aria-expanded="buka ? 'true' : 'false'"
            aria-controls="{{ $id }}-daftar"
            {{ $attributes->merge([
                'class' => 'flex h-12 w-full items-center justify-between gap-3 rounded-full border '
                         . 'bg-site-brand-deep px-5 text-left font-site-body text-site-body '
                         . 'transition-colors focus:outline-none focus:border-site-gilt '
                         . ($invalid ? 'border-danger' : 'border-white/10'),
            ]) }}>
        <span class="truncate" x-bind:class="label ? 'text-white' : 'text-white/55'"
              x-text="label || @js($placeholder)"></span>

        <svg class="h-3.5 w-3.5 shrink-0 text-site-gilt transition-transform duration-200"
             x-bind:class="buka && 'rotate-180'"
             viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="m4 6 4 4 4-4" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div x-show="buka" x-cloak x-transition.opacity.duration.150ms
         id="{{ $id }}-daftar" role="listbox"
         aria-labelledby="{{ $id }}"
         class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-20 overflow-hidden
                rounded-[22px] border border-white/10 bg-site-brand-deep
                shadow-[0_24px_48px_-16px_rgba(11,13,12,0.65)]">

        @if($pakaiCari)
            
            <div class="border-b border-white/10 p-2">
                <input type="text" x-model="cari"
                       x-on:keydown.arrow-down.prevent="geser(1)"
                       x-on:keydown.enter.prevent
                       placeholder="{{ __('site.search_placeholder') }}"
                       class="h-10 w-full rounded-full border border-white/10 bg-site-forest px-4
                              font-site-body text-site-small text-white placeholder:text-white/55
                              transition-colors focus:border-site-gilt focus:outline-none">
            </div>
        @endif

        <div x-ref="daftar" class="max-h-64 overflow-y-auto overscroll-contain p-1.5">
            @foreach($options as $opsi)
                <button type="button" role="option" tabindex="-1"
                        x-bind:aria-selected="nilai === @js($opsi['nilai']) ? 'true' : 'false'"
                        @if($pakaiCari)
                            x-show="! cari || @js(mb_strtolower($opsi['label'])).includes(cari.toLowerCase())"
                        @endif
                        x-on:click="pilih(@js($opsi['nilai']), @js($opsi['label']))"
                        x-on:keydown.enter.prevent="pilih(@js($opsi['nilai']), @js($opsi['label']))"
                        x-on:keydown.space.prevent="pilih(@js($opsi['nilai']), @js($opsi['label']))"
                        x-on:keydown.arrow-down.prevent="geser(1)"
                        x-on:keydown.arrow-up.prevent="geser(-1)"
                        x-bind:class="nilai === @js($opsi['nilai'])
                            ? 'bg-site-gilt text-site-forest'
                            : 'text-white/85 hover:bg-white/10 hover:text-white focus:bg-white/10 focus:text-white'"
                        class="block w-full truncate rounded-full px-4 py-2.5 text-left
                               font-site-body text-site-small transition-colors focus:outline-none">
                    {{ $opsi['label'] }}
                </button>
            @endforeach
        </div>
    </div>
</div>
