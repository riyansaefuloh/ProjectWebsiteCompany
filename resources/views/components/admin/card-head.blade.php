@props([
    'icon',
    'title',
    'subtitle' => null,

    // Rona kotak lambang. Bawaannya netral; bagian yang punya jati diri
    // sendiri — peringatan sertifikasi, grafik inquiry — mengirimkan miliknya.
    'tone'     => 'border-line bg-mist text-ink-muted',
])

<div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3 border-b border-line px-5 py-4 sm:px-6">

    <div class="flex min-w-0 items-start gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control border {{ $tone }}">
            <x-icon.admin :name="$icon" size="h-[18px] w-[18px]" />
        </span>

        <div class="min-w-0">
            <h2 class="text-admin-title text-heading">{{ $title }}</h2>

            @if($subtitle)
                <p class="mt-1 text-admin-label text-ink-muted">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    @if(! $slot->isEmpty())
        <div class="shrink-0 pt-0.5">{{ $slot }}</div>
    @endif
</div>
