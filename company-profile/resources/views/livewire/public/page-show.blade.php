<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1 style="margin:0;">{{ $page->translated_title }}</h1>
    </div>

    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 50px;">
        <div class="frontend-task" style="margin-bottom: 20px;">
            [FRONTEND TASK: Styling elemen typography (h1, h2, p, ul) yang dirender dari TinyMCE backend ini. Jangan lupa gunakan Tailwind typography (prose) jika menggunakan Tailwind.]
        </div>
        
        <div>
            {!! $page->translated_content !!}
        </div>
    </div>
</div>
