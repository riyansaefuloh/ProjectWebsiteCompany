<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>{{ __('site.page_export_markets') }}</h1>
        <p>{{ __('site.page_export_markets_sub') }}</p>
        <div class="frontend-task">
            [FRONTEND TASK: Styling Hero Banner untuk halaman Export Markets]
        </div>
    </div>

    <div style="max-width: 1000px; margin: 0 auto;">
        <!-- jsVectorMap CSS & JS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
        <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

        <div style="margin-bottom: 30px;">
            <div id="export-map" style="width: 100%; height: 500px; background: #e2e8f0; border: 1px solid #cbd5e1; border-radius: 8px;"></div>
        </div>

        <script>
            document.addEventListener('livewire:initialized', () => {
                let exportCountries = @json($exportMarkets->pluck('country_code'));
                
                new jsVectorMap({
                    selector: '#export-map',
                    map: 'world',
                    zoomOnScroll: false,
                    regionStyle: {
                        initial: {
                            fill: '#cbd5e1', // Default grey
                            stroke: '#ffffff',
                            strokeWidth: 0.5,
                            fillOpacity: 1
                        },
                        hover: {
                            fill: '#94a3b8'
                        },
                        selected: {
                            fill: '#2563eb' // Blue for active export markets
                        }
                    },
                    selectedRegions: exportCountries
                });
            });
        </script>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">{{ __('site.market_list') }}</h3>
            <ul style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 0; list-style: none;">
                @forelse($exportMarkets as $market)
                    <li style="border: 1px solid #eee; padding: 10px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <strong>{{ $market->translated_name }}</strong>
                            <span style="background: #2563eb; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-family: monospace;">{{ $market->country_code }}</span>
                        </div>
                        <div style="font-size: 12px; color: #666;">
                            {{ __('site.region') }}: {{ $market->region }}
                        </div>
                        @if($market->translated_note)
                            <div style="font-size: 12px; color: #444; margin-top: 5px; background: #f8fafc; padding: 5px;">
                                <em>{{ $market->translated_note }}</em>
                            </div>
                        @endif
                    </li>
                @empty
                    <li style="grid-column: span 3; padding: 20px; text-align: center; color: #666;">
                        {{ __('site.no_export_markets') }}
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
