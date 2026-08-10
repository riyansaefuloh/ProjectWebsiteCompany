<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>Global Export Markets</h1>
        <p>Our footprint across the globe.</p>
        <div class="frontend-task">
            [FRONTEND TASK: Styling Hero Banner untuk halaman Export Markets]
        </div>
    </div>

    <div style="max-width: 1000px; margin: 0 auto;">
        <div class="frontend-task" style="margin-bottom: 30px;">
            [FRONTEND TASK: Area ini harus diganti dengan Interactive Vector Map (seperti jVectorMap atau amCharts). Gunakan variabel $exportMarkets->pluck('country_code') untuk men-highlight negara-negara yang ada di daftar bawah ini.]
        </div>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">Market List (Data Source for Map)</h3>
            <ul style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 0; list-style: none;">
                @forelse($exportMarkets as $market)
                    <li style="border: 1px solid #eee; padding: 10px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <strong>{{ $market->translated_name }}</strong>
                            <span style="background: #2563eb; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-family: monospace;">{{ $market->country_code }}</span>
                        </div>
                        <div style="font-size: 12px; color: #666;">
                            Region: {{ $market->region }}
                        </div>
                        @if($market->translated_note)
                            <div style="font-size: 12px; color: #444; margin-top: 5px; background: #f8fafc; padding: 5px;">
                                <em>{{ $market->translated_note }}</em>
                            </div>
                        @endif
                    </li>
                @empty
                    <li style="grid-column: span 3; padding: 20px; text-align: center; color: #666;">
                        No export markets recorded yet.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
