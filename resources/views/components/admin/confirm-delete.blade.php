@props([
    
    'metode' => 'delete',

    'id' => null,

    'judul'  => 'Hapus?',
    'tombol' => 'Ya, hapus',

    // Dibacakan pembaca layar dan muncul saat kursor berhenti di tombolnya.
    'label'  => 'Hapus',

    'kelas' => null,
    'ikon'  => 'h-4 w-4',

    'nama' => null,
])

@php
    
@endphp

<div x-data="{ buka: false }" class="contents">

    <button type="button" x-on:click="buka = true"
            title="{{ $label }}" aria-label="{{ $label }}"
            {{ $attributes->class([$kelas ?: 'inline-flex h-8 w-8 shrink-0 items-center justify-center
                                              rounded-control border border-line bg-canvas text-ink-muted
                                              transition-colors hover:border-danger hover:bg-danger
                                              hover:text-white']) }}>
        <x-icon.admin name="trash" :size="$ikon" />
    </button>

    <template x-if="buka">
        <div class="modal-open fixed inset-0 z-[110] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-on:keydown.escape.window="buka = false"
             role="alertdialog" aria-modal="true">

            <div class="absolute inset-0" aria-hidden="true" x-on:click="buka = false"></div>

            <div class="relative w-full max-w-[440px] overflow-hidden rounded-corner border border-line
                        bg-canvas shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <div class="flex items-start gap-3 p-5">
                    
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                 rounded-control bg-danger/10 text-danger">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M8 2.4 14.4 13.2H1.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                            <path d="M8 6.6v2.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <circle cx="8" cy="11.4" r="0.85" fill="currentColor"/>
                        </svg>
                    </span>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-admin-title text-heading">{{ $judul }}</h2>

                        <p class="mt-1 text-admin-body text-ink-muted">{{ $slot }}</p>

                        @if(filled($nama))
                            <p class="mt-3 truncate rounded-control border border-line bg-mist
                                      px-3.5 py-2.5 text-admin-strong text-ink"
                               title="{{ $nama }}">{{ $nama }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-line px-5 py-3.5">
                    <button type="button" x-on:click="buka = false"
                            class="admin-btn admin-btn-quiet">
                        Batal
                    </button>

                    <button type="button" x-on:click="buka = false; $wire.{{ $metode }}({{ collect(is_array($id) ? $id : array_filter([$id], 'filled'))->map(fn ($x) => "'" . $x . "'")->implode(', ') }})"
                            class="admin-btn admin-btn-danger">
                        {{ $tombol }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
