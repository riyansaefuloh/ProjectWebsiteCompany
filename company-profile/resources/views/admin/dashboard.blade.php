<div style="font-family: sans-serif; padding: 30px; max-width: 1000px; margin: 0 auto;">

    {{-- ================================================== --}}
    {{-- HEADER --}}
    {{-- ================================================== --}}
    <h2>🏠 CMS Admin Dashboard</h2>
    <p>Logged in as: <strong>{{ auth()->user()->name }}</strong>
        ({{ auth()->user()->roles->pluck('name')->implode(', ') }})</p>
    <hr style="margin: 20px 0;">


    {{-- ================================================== --}}
    {{-- ⚠️ SECTION: ALERT SERTIFIKAT KEDALUWARSA (PRD 8.4) --}}
    {{-- ================================================== --}}
    @if($expiredCerts->isNotEmpty() || $expiringSoonCerts->isNotEmpty())
    <div style="margin-bottom: 24px;">

        {{-- Sertifikat yang SUDAH expired --}}
        @if($expiredCerts->isNotEmpty())
        <div style="background: #fef2f2; border: 1px solid #fca5a5; border-left: 5px solid #ef4444; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
            <strong style="color: #dc2626;">🔴 PERINGATAN — {{ $expiredCerts->count() }} Sertifikat Sudah Kedaluwarsa!</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach($expiredCerts as $cert)
                    <li style="margin-bottom: 4px; color: #991b1b;">
                        <strong>{{ $cert->translations->first()?->name ?? $cert->slug }}</strong>
                        — Expired: {{ $cert->expires_at->format('d M Y') }}
                        ({{ $cert->expires_at->diffForHumans() }})
                        @can('manage certifications')
                            <a href="{{ route('admin.certifications.index') }}" style="margin-left: 8px; color: #dc2626; font-size: 12px;">[Kelola →]</a>
                        @endcan
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Sertifikat yang HAMPIR expired (≤ 90 hari) --}}
        @if($expiringSoonCerts->isNotEmpty())
        <div style="background: #fffbeb; border: 1px solid #fcd34d; border-left: 5px solid #f59e0b; border-radius: 8px; padding: 16px;">
            <strong style="color: #d97706;">🟡 PERHATIAN — {{ $expiringSoonCerts->count() }} Sertifikat Akan Segera Kedaluwarsa</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach($expiringSoonCerts as $cert)
                    @php
                        $daysLeft = now()->diffInDays($cert->expires_at);
                        $color = $daysLeft <= 30 ? '#d97706' : '#92400e';
                    @endphp
                    <li style="margin-bottom: 4px; color: {{ $color }};">
                        <strong>{{ $cert->translations->first()?->name ?? $cert->slug }}</strong>
                        — Expired: {{ $cert->expires_at->format('d M Y') }}
                        <span style="background: #fef3c7; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                            {{ $daysLeft }} hari lagi
                        </span>
                        @can('manage certifications')
                            <a href="{{ route('admin.certifications.index') }}" style="margin-left: 8px; color: #d97706; font-size: 12px;">[Kelola →]</a>
                        @endcan
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>
    @endif


    {{-- ================================================== --}}
    {{-- 📊 SECTION: STATISTIK RINGKASAN --}}
    {{-- ================================================== --}}
    <h3>📊 Statistik Ringkasan</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px;">

        @can('view inquiries')
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #1d4ed8;">{{ $totalInquiries }}</div>
            <div style="color: #3b82f6; font-size: 13px; margin-top: 4px;">Total Inquiry</div>
        </div>
        <div style="background: #fef9c3; border: 1px solid #fde68a; border-radius: 8px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #ca8a04;">{{ $newInquiries }}</div>
            <div style="color: #b45309; font-size: 13px; margin-top: 4px;">Inquiry Baru</div>
        </div>
        <div style="background: #ede9fe; border: 1px solid #c4b5fd; border-radius: 8px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #7c3aed;">{{ $processingInquiries }}</div>
            <div style="color: #6d28d9; font-size: 13px; margin-top: 4px;">Diproses</div>
        </div>
        <div style="background: #dcfce7; border: 1px solid #86efac; border-radius: 8px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #15803d;">{{ $closedInquiries }}</div>
            <div style="color: #16a34a; font-size: 13px; margin-top: 4px;">Selesai</div>
        </div>
        @endcan

        @can('manage products')
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #166534;">{{ $totalProducts }}</div>
            <div style="color: #15803d; font-size: 13px; margin-top: 4px;">Produk Aktif</div>
        </div>
        <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #c2410c;">{{ $draftProducts }}</div>
            <div style="color: #ea580c; font-size: 13px; margin-top: 4px;">Produk Draft</div>
        </div>
        @endcan

        @can('manage news')
        <div style="background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; color: #6d28d9;">{{ $totalNews }}</div>
            <div style="color: #7c3aed; font-size: 13px; margin-top: 4px;">Berita Aktif</div>
        </div>
        @endcan

    </div>


    {{-- ================================================== --}}
    {{-- 📥 SECTION: INQUIRY TERBARU --}}
    {{-- ================================================== --}}
    @can('view inquiries')
    @if($latestInquiries->isNotEmpty())
    <h3>📥 Inquiry Terbaru</h3>
    <div style="overflow-x: auto; margin-bottom: 28px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Nama</th>
                    <th style="padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Perusahaan</th>
                    <th style="padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Negara</th>
                    <th style="padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Produk</th>
                    <th style="padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Status</th>
                    <th style="padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestInquiries as $inquiry)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 10px 12px;">{{ $inquiry->name }}</td>
                    <td style="padding: 10px 12px;">{{ $inquiry->company }}</td>
                    <td style="padding: 10px 12px;">{{ strtoupper($inquiry->country_code) }}</td>
                    <td style="padding: 10px 12px;">{{ $inquiry->product?->translations->first()?->name ?? '—' }}</td>
                    <td style="padding: 10px 12px;">
                        @php
                            $statusColors = [
                                'new'        => '#3b82f6',
                                'processing' => '#f59e0b',
                                'quoted'     => '#8b5cf6',
                                'closed'     => '#22c55e',
                                'rejected'   => '#ef4444',
                            ];
                            $color = $statusColors[$inquiry->status] ?? '#6b7280';
                        @endphp
                        <span style="background: {{ $color }}20; color: {{ $color }}; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            {{ ucfirst($inquiry->status) }}
                        </span>
                    </td>
                    <td style="padding: 10px 12px; color: #6b7280; font-size: 12px;">
                        {{ $inquiry->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 10px;">
            <a href="{{ route('admin.inquiries.index') }}" style="color: #3b82f6; font-size: 14px;">Lihat semua inquiry →</a>
        </div>
    </div>
    @endif
    @endcan


    {{-- ================================================== --}}
    {{-- 🧭 SECTION: MENU NAVIGASI ADMIN --}}
    {{-- ================================================== --}}
    <h3>🧭 Menu Admin</h3>
    <ul style="line-height: 2.2; font-size: 16px;">
        @can('manage products')
            <li><a href="{{ route('admin.products.index') }}">📦 Export Products Management</a></li>
            <li><a href="{{ route('admin.categories.index') }}">🏷️ Product Categories Management</a></li>
        @endcan

        @can('manage certifications')
            <li><a href="{{ route('admin.certifications.index') }}">📜 Certifications & Expiry Alert</a></li>
        @endcan

        @can('manage export markets')
            <li><a href="{{ route('admin.export-markets.index') }}">🌍 Export Target Countries</a></li>
        @endcan

        @can('manage news')
            <li><a href="{{ route('admin.news.index') }}">📰 News & Articles Management</a></li>
        @endcan

        @can('manage pages')
            <li><a href="{{ route('admin.pages.index') }}">📄 Static Pages (About, Privacy, etc.)</a></li>
        @endcan

        @can('manage galleries')
            <li><a href="{{ route('admin.galleries.index') }}">🖼️ Photo Galleries Management</a></li>
        @endcan

        @can('view inquiries')
            <li><a href="{{ route('admin.inquiries.index') }}">📥 Buyer Inquiries (RFQ) Management</a></li>
        @endcan

        @can('manage downloads')
            <li><a href="{{ route('admin.downloads.index') }}">📂 PDF Brochures & Downloads Management</a></li>
        @endcan

        @can('manage users')
            <li><a href="{{ route('admin.users.index') }}">👥 Admin Users & Role Permissions</a></li>
        @endcan

        @can('manage global settings')
            <li><a href="{{ route('admin.settings.index') }}">⚙️ Global Website Settings</a></li>
        @endcan
    </ul>


    {{-- ================================================== --}}
    {{-- 📊 SECTION: GRAFIK INQUIRY (PRD Bab 8.1)          --}}
    {{-- ================================================== --}}
    @can('view inquiries')
    <h3>📊 Grafik Inquiry</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px;">

        {{-- Grafik Inquiry Per Bulan (Bar Chart) --}}
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
            <h4 style="margin: 0 0 16px 0; color: #374151;">📅 Inquiry Per Bulan (12 Bulan Terakhir)</h4>
            <canvas id="chartInquiryPerMonth" height="200"></canvas>
        </div>

        {{-- Grafik Inquiry Per Negara (Doughnut Chart) --}}
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
            <h4 style="margin: 0 0 16px 0; color: #374151;">🌍 Top 10 Negara Pengirim Inquiry</h4>
            <canvas id="chartInquiryPerCountry" height="200"></canvas>
        </div>

    </div>

    {{-- Load Chart.js dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Data dari DashboardController (PHP → JSON)
        const monthLabels  = @json($chartMonthLabels);
        const monthData    = @json($chartMonthData);
        const countryLabels = @json($chartCountryLabels);
        const countryData  = @json($chartCountryData);

        // Palet warna untuk chart
        const colors = [
            '#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6',
            '#ec4899','#14b8a6','#f97316','#6366f1','#84cc16'
        ];

        // Bar Chart — Inquiry Per Bulan
        new Chart(document.getElementById('chartInquiryPerMonth'), {
            type: 'bar',
            data: {
                labels: monthLabels.length > 0 ? monthLabels : ['Belum ada data'],
                datasets: [{
                    label: 'Jumlah Inquiry',
                    data: monthData.length > 0 ? monthData : [0],
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // Doughnut Chart — Inquiry Per Negara
        new Chart(document.getElementById('chartInquiryPerCountry'), {
            type: 'doughnut',
            data: {
                labels: countryLabels.length > 0 ? countryLabels : ['Belum ada data'],
                datasets: [{
                    data: countryData.length > 0 ? countryData : [1],
                    backgroundColor: colors,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    </script>
    @endcan


    <br>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Logout</button>
    </form>

</div>
