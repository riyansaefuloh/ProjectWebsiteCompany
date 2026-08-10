<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>Our Certifications</h1>
        <p>Recognitions of our commitment to quality and excellence.</p>
        <div class="frontend-task">
            [FRONTEND TASK: Berikan styling Hero Banner khusus halaman Certifications]
        </div>
    </div>

    <div style="max-width: 1000px; margin: 0 auto;">
        <div class="frontend-task" style="margin-bottom: 30px;">
            [FRONTEND TASK: Buat Grid yang estetis untuk menampilkan logo/emblem sertifikat beserta namanya]
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            @forelse($certifications as $cert)
                <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
                    @if($cert->getFirstMediaUrl('logos', 'webp'))
                        <img src="{{ $cert->getFirstMediaUrl('logos', 'webp') }}" alt="{{ $cert->translated_name }}" style="max-width: 100px; height: 100px; object-fit: contain; margin-bottom: 15px;">
                    @else
                        <div style="width: 100px; height: 100px; background: #eee; margin: 0 auto 15px auto; display: flex; align-items: center; justify-content: center; border-radius: 50%;">No Logo</div>
                    @endif
                    <h3 style="margin: 0; font-size: 16px;">{{ $cert->translated_name }}</h3>
                </div>
            @empty
                <div style="grid-column: span 4; padding: 40px; text-align: center; border: 1px dashed #ccc; color: #666;">
                    Belum ada sertifikasi yang ditambahkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
