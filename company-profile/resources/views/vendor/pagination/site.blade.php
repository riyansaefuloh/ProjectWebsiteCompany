@if($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
         class="flex items-center justify-center gap-2">

        {{-- Sebelumnya --}}
        @if($paginator->onFirstPage())
            <span aria-disabled="true"
                  class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink-faint opacity-40">
                <svg class="h-4 w-4 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @else
            <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
                    rel="prev" aria-label="{{ __('pagination.previous') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line-strong
                           text-ink transition-colors hover:border-brand hover:text-brand">
                <svg class="h-4 w-4 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        @endif

        {{-- Nomor halaman --}}
        <div class="hidden items-center gap-1.5 sm:flex">
            @foreach($elements as $element)
                @if(is_string($element))
                    <span class="px-2 text-[14px] text-ink-faint">{{ $element }}</span>
                @endif

                @if(is_array($element))
                    @foreach($element as $page => $url)
                        @if($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-brand px-3 text-[14px] font-bold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }})"
                                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-full px-3 text-[14px]
                                           font-semibold text-ink-muted transition-colors hover:bg-mist hover:text-ink">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Penanda halaman untuk ponsel, menggantikan deretan nomor --}}
        <span class="px-3 text-[14px] text-ink-muted sm:hidden">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- Berikutnya --}}
        @if($paginator->hasMorePages())
            <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
                    rel="next" aria-label="{{ __('pagination.next') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line-strong
                           text-ink transition-colors hover:border-brand hover:text-brand">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        @else
            <span aria-disabled="true"
                  class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink-faint opacity-40">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @endif
    </nav>
@endif
