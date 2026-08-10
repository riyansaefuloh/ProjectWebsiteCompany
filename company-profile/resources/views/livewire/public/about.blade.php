<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>About Us</h1>
        <p>Discover our story and mission.</p>
        <div class="frontend-task">
            [FRONTEND TASK: Berikan styling Hero Banner khusus halaman About Us]
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border: 1px solid #ddd; border-radius: 8px;">
        @if($page)
            <div class="frontend-task" style="margin-bottom: 20px;">
                [FRONTEND TASK: Styling elemen typography (h1, h2, p, ul) yang dirender dari TinyMCE backend ini. Jangan lupa gunakan Tailwind typography (prose) jika menggunakan Tailwind.]
            </div>
            
            <div>
                {!! $page->translated_content !!}
            </div>
        @else
            <div style="padding: 40px; text-align: center; border: 1px dashed #ccc; color: #666;">
                Halaman "About Us" belum dibuat di CMS. Silakan buat halaman statis dengan slug "about-us".
            </div>
        @endif
    </div>
</div>
