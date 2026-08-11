<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Product Catalog</title>
</head>
<body style="font-family:sans-serif;max-width:400px;margin:60px auto;padding:20px">
    <h2>Download Export Product Catalog</h2>
    <p>Masukkan email Anda untuk mengunduh katalog produk kami.</p>
    
    <form method="POST" action="{{ route('download.catalog') }}">
        @csrf
        <label>Email <span style="color:red">*</span></label><br>
        <input type="email" name="email" required 
            style="width:100%;padding:8px;margin:8px 0 16px;border:1px solid #ccc;border-radius:4px">
        <button type="submit" 
            style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;width:100%">
            📥 Download Katalog PDF
        </button>
    </form>

    <div class="frontend-task" style="margin-top:20px; padding:10px; background:#f8d7da; color:#721c24; border-radius:5px; font-size:12px;">
        <strong>[FRONTEND TASK]</strong> Styling form download katalog ini agar terlihat profesional! Anda juga bisa menggabungkannya dengan layout utama jika diperlukan.
    </div>
</body>
</html>
