# PRD — Website Company Profile Eksportir

**Judul Proyek:** Company Profile Website (Siap Pakai) untuk Perusahaan Eksportir
**Versi Dokumen:** 1.0
**Tanggal:** 14 Juli 2026
**Status:** Draft — Ready for Development
**Stack Utama:** Laravel 13 · Livewire 4 · PostgreSQL 16 · TailwindCSS

---

## 1. Ringkasan Eksekutif

Website ini adalah *company profile* profesional yang ditujukan untuk perusahaan eksportir Indonesia yang menjual produk ke pasar internasional. Berbeda dengan company profile biasa, fokus utamanya adalah **membangun kepercayaan buyer luar negeri** dan **mengkonversi kunjungan menjadi inquiry (RFQ — Request for Quotation)**.

Tiga tujuan bisnis inti:

1. **Kredibilitas** — menampilkan legalitas, sertifikasi (ISO, HACCP, Halal, dll.), kapasitas produksi, dan rekam jejak ekspor sehingga buyer internasional percaya.
2. **Katalog Ekspor** — memaparkan produk lengkap dengan spesifikasi ekspor (HS Code, MOQ, packaging, kapasitas suplai) yang dibutuhkan importir.
3. **Konversi** — menyediakan jalur inquiry/RFQ, kontak langsung (WhatsApp Business, email), dan katalog PDF yang dapat diunduh.

Website bersifat **multibahasa** (minimal Bahasa Indonesia + Inggris) karena target audiens utamanya adalah buyer/importir global, dan dilengkapi **panel admin (CMS)** agar seluruh konten dapat dikelola tanpa developer.

---

## 2. Tujuan & Sasaran (Goals & Objectives)

| Kode | Tujuan | Metrik Keberhasilan |
|------|--------|---------------------|
| G-1 | Meningkatkan kepercayaan buyer internasional | Halaman sertifikasi & legalitas tampil lengkap; skor kredibilitas divalidasi tim sales |
| G-2 | Menghasilkan inquiry/RFQ berkualitas | ≥ 20 inquiry terverifikasi / bulan setelah 3 bulan live |
| G-3 | Menampilkan katalog produk ekspor lengkap | 100% produk memiliki HS Code, MOQ, dan packaging |
| G-4 | Kemudahan pengelolaan konten mandiri | Admin non-teknis dapat CRUD konten tanpa bantuan developer |
| G-5 | Performa & SEO internasional | Lighthouse ≥ 90 (Performance, SEO); indexed di Google untuk keyword produk + "exporter" |

---

## 3. Persona & Target Pengguna

### 3.1 Pengguna Eksternal (Publik)

**Persona A — Importir/Buyer Internasional**
Mencari supplier terpercaya. Ingin cepat menilai: apakah perusahaan legal, punya sertifikasi, kapasitas cukup, dan produk sesuai spesifikasi. Butuh HS Code, MOQ, dan cara kontak cepat. Mayoritas berbahasa Inggris.

**Persona B — Trading Company / Agen Lokal**
Mencari produk untuk direkspor atau disuplai. Fokus pada harga indikatif, kapasitas, dan kemudahan negosiasi.

**Persona C — Mitra / Investor / Media**
Menilai profil perusahaan, visi-misi, penghargaan, dan liputan.

### 3.2 Pengguna Internal (Admin)

**Persona D — Admin Marketing/CMS**
Mengelola konten: produk, berita, sertifikat, banner, halaman statis. Non-teknis.

**Persona E — Sales/Export Manager**
Menerima, menindaklanjuti, dan mengubah status inquiry/RFQ. Mengekspor data inquiry.

**Persona F — Super Admin**
Mengelola user, role, izin, dan pengaturan global website.

---

## 4. Ruang Lingkup (Scope)

### 4.1 In-Scope

- Website publik multibahasa (ID/EN, extensible ke bahasa lain).
- Panel admin (CMS) berbasis Livewire.
- Katalog produk dengan spesifikasi ekspor.
- Modul sertifikasi & legalitas.
- Modul pasar ekspor (negara tujuan) + peta.
- Sistem inquiry/RFQ dengan notifikasi email + WhatsApp deep-link.
- Blog/berita & galeri.
- Katalog PDF unduhan.
- SEO on-page, sitemap, structured data.
- Manajemen user, role, & permission.

### 4.2 Out-of-Scope (Fase 1)

- Transaksi/pembayaran online (e-commerce) — website ini *lead-generation*, bukan toko.
- Login untuk buyer / portal B2B (dipertimbangkan Fase 2).
- Integrasi ERP/akuntansi.
- Live chat real-time (cukup WhatsApp deep-link di Fase 1).

---

## 5. Arsitektur & Tech Stack

| Lapisan | Teknologi | Catatan |
|---------|-----------|---------|
| Framework | **Laravel 13** | PHP 8.3+ |
| Interaktivitas | **Livewire 4** | Komponen dinamis (filter katalog, form inquiry, admin CRUD) |
| Database | **PostgreSQL 16** | Primary key ULID |
| Styling | TailwindCSS 4 | + komponen custom |
| Media | `spatie/laravel-medialibrary` | Auto-convert **WebP**, responsive images, konversi thumbnail |
| Otorisasi | `spatie/laravel-permission` | Role & permission berbasis DB |
| Notifikasi UI | **goey-toast** | Feedback aksi admin & form publik |
| Multibahasa | `mcamara/laravel-localization` + tabel translation | Route ter-lokalisasi (`/en/…`, `/id/…`) |
| Sitemap | `spatie/laravel-sitemap` | Generate otomatis |
| SEO/Meta | `artesaos/seotools` atau custom meta manager | Meta per halaman & produk |
| PDF | `barryvdh/laravel-dompdf` | Generate katalog/company profile PDF |
| Queue/Email | Laravel Queue + Mailable | Notifikasi inquiry async |
| Search | PostgreSQL Full-Text Search (`tsvector`) | Pencarian produk & berita |

### 5.1 Konvensi Teknis

- **Primary Key:** `ULID` di semua tabel utama.
- **URL:** *slug-based* untuk produk, kategori, berita, halaman (`/products/{slug}`).
- **UI:** Bahasa Indonesia untuk panel admin; publik mengikuti locale (ID/EN). **Penamaan kode & database dalam Bahasa Inggris.**
- **Nilai moneter:** `decimal(15,2)` (mis. harga indikatif FOB).
- **Media:** semua gambar dikonversi otomatis ke WebP via medialibrary.
- **Notifikasi:** goey-toast untuk sukses/gagal aksi.

---

## 6. Struktur Situs (Sitemap Publik)

```
/ (Beranda)
├── /about (Tentang Kami)
│   ├── Profil, Visi-Misi, Sejarah
│   ├── /about/certifications (Sertifikasi & Legalitas)
│   ├── /about/capacity (Kapasitas Produksi & Fasilitas)
│   └── /about/team (Manajemen — opsional)
├── /products (Katalog Produk)
│   ├── /products/category/{slug} (per kategori)
│   └── /products/{slug} (detail produk)
├── /export-markets (Pasar Ekspor / Negara Tujuan)
├── /gallery (Galeri Foto/Video)
├── /news (Berita & Artikel)
│   └── /news/{slug}
├── /downloads (Katalog & Brosur PDF)
├── /contact (Kontak & Inquiry)
├── /inquiry (Form RFQ khusus — bisa modal juga)
└── /sitemap.xml, /robots.txt
```

---

## 7. Kebutuhan Fungsional — Halaman Publik

### 7.1 Beranda (Home)

**Tujuan:** kesan pertama kredibel + arahkan ke katalog & inquiry.

Komponen:
- **Hero** — headline value proposition + CTA ("Request a Quote", "View Catalog"), background image/video (medialibrary).
- **Trust bar** — logo sertifikasi (ISO, HACCP, Halal, dll.) + statistik (tahun berdiri, negara tujuan, kapasitas/bulan).
- **Produk unggulan** — carousel produk (`featured = true`).
- **Kategori produk** — grid kategori.
- **Peta pasar ekspor** — visualisasi negara tujuan.
- **Kenapa memilih kami** — poin keunggulan.
- **Testimoni/klien** (opsional).
- **Berita terbaru** — 3 artikel.
- **CTA penutup** — banner inquiry + kontak WhatsApp.

Semua section dapat dikelola dari admin (aktif/nonaktif, urutan).

### 7.2 Tentang Kami (About)

- Narasi profil, visi & misi, sejarah (timeline).
- Nilai perusahaan.
- Data legal singkat (NIB, badan usaha) — tanpa dokumen sensitif publik.
- Link ke sub-halaman sertifikasi & kapasitas.

### 7.3 Sertifikasi & Legalitas

**Krusial untuk eksportir.** Menampilkan daftar sertifikat:

| Field | Keterangan |
|-------|-----------|
| Nama sertifikat | ISO 9001, ISO 22000, HACCP, Halal MUI, BPOM, dsb. |
| Lembaga penerbit | |
| Nomor sertifikat | (opsional publik) |
| Masa berlaku | tanggal berlaku s.d. |
| Logo/gambar | medialibrary (WebP) |
| File PDF | unduhan opsional |

### 7.4 Kapasitas Produksi & Fasilitas

- Kapasitas produksi (per bulan/tahun) per produk.
- Foto fasilitas/pabrik/gudang (galeri).
- Proses produksi/quality control (opsional infografis).
- Lokasi fasilitas + peta.

### 7.5 Katalog Produk

**Halaman daftar:**
- Filter Livewire real-time: kategori, ketersediaan, negara asal bahan baku (opsional).
- Pencarian (PostgreSQL FTS).
- Sort: terbaru, nama, unggulan.
- Kartu produk: gambar WebP, nama, kategori, HS Code, MOQ, badge "Featured".
- Pagination.

**Halaman detail produk** — field spesifikasi ekspor:

| Field | Contoh |
|-------|--------|
| Nama produk (ID/EN) | Kopi Arabika Gayo Green Bean |
| Slug | kopi-arabika-gayo |
| Kategori | Kopi & Rempah |
| **HS Code** | 0901.11.10 |
| Deskripsi (ID/EN) | rich text |
| Spesifikasi | grade, moisture, ukuran, warna, dsb. (key-value dinamis) |
| **MOQ** (Minimum Order Quantity) | 1 x 20ft container |
| Kapasitas suplai | 50 ton/bulan |
| Packaging | jute bag 60kg / vacuum / custom |
| Origin | Aceh, Indonesia |
| Harga indikatif (opsional) | FOB / CIF — `decimal(15,2)`, mata uang USD |
| Incoterms | FOB, CIF, CFR |
| Galeri gambar | multiple, WebP |
| Sertifikasi terkait | relasi ke sertifikat |
| Brosur PDF | unduhan |
| CTA | tombol "Request Quote" → prefilled inquiry produk ini |

### 7.6 Pasar Ekspor (Export Markets)

- Daftar negara tujuan + peta dunia interaktif (highlight negara).
- Statistik: jumlah negara, volume, region.
- Opsional: testimoni buyer per region.

### 7.7 Galeri

- Album foto & video (fasilitas, produk, pameran/trade show).
- Grid + lightbox. Video via embed (YouTube/Vimeo) atau upload.

### 7.8 Berita & Artikel (News/Blog)

- Untuk SEO & update aktivitas (pameran, sertifikasi baru, ekspor perdana).
- Kategori/tag, penulis, tanggal, cover WebP, rich content, related posts.
- FTS pencarian.

### 7.9 Downloads

- Katalog produk PDF, company profile PDF, brosur.
- Opsional gate: minta email sebelum unduh (lead capture).

### 7.10 Kontak & Inquiry (RFQ)

**Form Inquiry (Livewire):**

| Field | Wajib | Catatan |
|-------|-------|---------|
| Nama | ✓ | |
| Perusahaan | ✓ | |
| Email | ✓ | validasi |
| Negara | ✓ | dropdown ISO countries |
| Telepon/WhatsApp | | intl format |
| Produk diminati | | relasi produk (prefilled dari detail produk) |
| Perkiraan volume | | |
| Incoterms preferensi | | |
| Pesan | ✓ | |
| reCAPTCHA / honeypot | ✓ | anti-spam |

Setelah submit:
- Simpan ke DB (`inquiries`).
- Kirim email notifikasi ke sales (queue).
- Auto-reply email ke buyer (multibahasa).
- Tampilkan goey-toast sukses.
- Opsi tombol "Chat via WhatsApp" (deep-link `wa.me` prefilled).

Halaman kontak juga memuat: alamat kantor, peta, email, telepon, jam kerja (timezone WIB + GMT), social media, WhatsApp Business.

---

## 8. Kebutuhan Fungsional — Panel Admin (CMS)

Akses `/admin`, dilindungi auth + role. Dibangun dengan Livewire 4.

### 8.1 Dashboard
- Ringkasan: total inquiry (baru/diproses/selesai), produk, kunjungan (opsional analytics), inquiry terbaru.
- Grafik inquiry per bulan / per negara.

### 8.2 Manajemen Produk
- CRUD produk + spesifikasi dinamis (key-value repeater).
- Upload multi-gambar (medialibrary, WebP), set cover, reorder.
- Toggle: featured, published/draft.
- Relasi kategori & sertifikat.
- Terjemahan field (ID/EN) dalam satu form (tab bahasa).

### 8.3 Manajemen Kategori
- CRUD kategori, slug otomatis, icon/gambar, urutan.

### 8.4 Manajemen Sertifikasi
- CRUD sertifikat: nama, penerbit, nomor, masa berlaku, logo, PDF.
- Peringatan sertifikat mendekati kedaluwarsa (dashboard alert).

### 8.5 Manajemen Pasar Ekspor
- CRUD negara tujuan (kode ISO, region, catatan).

### 8.6 Manajemen Berita
- CRUD artikel, kategori/tag, editor rich text, cover, jadwal publish, SEO meta per artikel.

### 8.7 Manajemen Galeri
- CRUD album & media, video embed.

### 8.8 Manajemen Inquiry (RFQ)
- List + filter (status, negara, tanggal, produk).
- Detail inquiry, ubah status (Baru → Diproses → Ditawar → Selesai/Ditolak), catatan internal.
- Export CSV/Excel.
- Assign ke sales (opsional).

### 8.9 Manajemen Halaman & Konten Statis
- Edit konten About, Visi-Misi, hero, section beranda (aktif/urutan).
- Pengaturan global: logo, favicon, kontak, social, WA number, alamat, timezone, GA/GTM ID, warna brand.

### 8.10 Manajemen Downloads
- Upload katalog/brosur PDF, toggle gate email.

### 8.11 Manajemen User & Role
- CRUD user, assign role (`spatie/laravel-permission`).
- Role default: Super Admin, Admin CMS, Sales.

### 8.12 Multibahasa
- Kelola terjemahan konten (ID/EN), tambah bahasa baru.

---

## 9. Model Data (PostgreSQL — ULID)

> Penamaan tabel & kolom **Bahasa Inggris**. Semua tabel utama menggunakan `ulid` sebagai PK, `timestamps`, dan `soft deletes` bila relevan.

### 9.1 Tabel Inti

**`products`**
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | ulid (PK) | |
| category_id | ulid (FK) | |
| slug | string, unique | |
| hs_code | string | |
| moq | string | Minimum Order Quantity |
| supply_capacity | string | |
| packaging | string | |
| origin | string | |
| indicative_price | decimal(15,2), null | harga FOB/CIF |
| currency | string(3) | default 'USD' |
| incoterms | string | mis. "FOB,CIF" |
| is_featured | boolean | default false |
| status | enum | draft/published |
| sort_order | integer | |
| timestamps, deleted_at | | |

**`product_translations`**
| id (ulid) | product_id (FK) | locale (id/en) | name | description (text) | UNIQUE(product_id, locale) |

**`product_specifications`** (spesifikasi dinamis)
| id (ulid) | product_id (FK) | spec_key | spec_value | locale | sort_order |

**`categories`**
| id (ulid) | slug (unique) | icon | sort_order | status | timestamps |

**`category_translations`**
| id | category_id | locale | name | description |

**`certifications`**
| id (ulid) | slug | issuer | certificate_number (null) | issued_at | expires_at | file_path (null) | status | sort_order |

**`certification_translations`**
| id | certification_id | locale | name | description |

**`product_certification`** (pivot)
| product_id | certification_id |

**`export_markets`**
| id (ulid) | country_code (ISO-2) | region | is_active | sort_order |

**`export_market_translations`**
| id | export_market_id | locale | name | note |

**`inquiries`**
| id (ulid) | name | company | email | country_code | phone (null) | product_id (FK, null) | volume (null) | incoterms (null) | message (text) | status (enum: new/processing/quoted/closed/rejected) | assigned_to (FK users, null) | internal_note (text, null) | ip_address | timestamps |

**`news`**
| id (ulid) | slug (unique) | author_id (FK) | cover | published_at | status | timestamps |

**`news_translations`**
| id | news_id | locale | title | excerpt | content (text) | meta_title | meta_description |

**`news_category`** + `news_tag` (relasi many-to-many, opsional).

**`galleries`** (album) & **`gallery_items`** (media/video embed).

**`downloads`**
| id (ulid) | title | file_path | require_email (bool) | download_count | sort_order |

**`pages`** / **`settings`**
- `pages`: konten statis (about, dsb.) dengan translation.
- `settings`: key-value global (logo, kontak, WA, GA ID, brand color, dll.).

**Auth & Otorisasi** — tabel bawaan Laravel + `spatie/laravel-permission` (`roles`, `permissions`, `model_has_roles`, dst.).

**Media** — `media` (spatie medialibrary), polymorphic ke products, news, galleries, certifications.

### 9.2 Relasi Ringkas
- `Category` 1—N `Product`
- `Product` N—N `Certification`
- `Product` 1—N `ProductSpecification`
- `Product` 1—0/N `Inquiry`
- Semua entitas konten 1—N `*_translation`
- `User` N—N `Role` (spatie)

---

## 10. Kebutuhan Non-Fungsional

### 10.1 Multibahasa (i18n)
- Default: `id` & `en`. Route ter-lokalisasi (`/en/products`, `/id/produk` atau prefix `/en`).
- `hreflang` tags untuk SEO internasional.
- Language switcher di header, simpan preferensi (session/cookie).
- Konten dinamis via tabel `*_translation`; teks statis via file lang.

### 10.2 SEO
- Meta title/description per halaman & produk (editable admin).
- Open Graph & Twitter Card.
- **Structured Data (JSON-LD):** `Organization`, `Product`, `BreadcrumbList`, `Article`.
- `sitemap.xml` otomatis (multibahasa) + `robots.txt`.
- URL slug bersih, canonical, `hreflang`.
- Lazy-load + WebP + responsive images untuk Core Web Vitals.

### 10.3 Performa
- Lighthouse ≥ 90 (Performance & SEO).
- Cache: config, route, view; response cache untuk halaman publik.
- Eager loading (hindari N+1), index DB pada kolom FK, slug, status.
- CDN untuk aset & media (opsional).
- Queue untuk email/notifikasi.

### 10.4 Keamanan
- HTTPS wajib.
- Proteksi CSRF (bawaan Laravel), XSS escaping, prepared statements (Eloquent).
- Rate limiting pada form inquiry & login.
- reCAPTCHA/honeypot anti-spam.
- Validasi & sanitasi upload (tipe & ukuran file).
- Role-based access di admin (`spatie/laravel-permission`).
- Backup DB terjadwal.
- Log aktivitas admin (opsional `spatie/laravel-activitylog`).

### 10.5 Aksesibilitas & Responsif
- Mobile-first, responsif penuh (buyer sering akses via mobile).
- Kontras warna & alt text gambar (WCAG AA sedapat mungkin).

### 10.6 Analytics & Tracking
- Google Analytics 4 / GTM (ID via settings).
- Event tracking: klik "Request Quote", submit inquiry, unduh katalog, klik WhatsApp.

### 10.7 Integrasi
- **WhatsApp Business** — deep-link `wa.me` prefilled.
- **Email** — SMTP/API (Mailgun/SES/SMTP), notifikasi + auto-reply.
- **Peta** — Google Maps / Leaflet untuk lokasi & pasar ekspor.
- **reCAPTCHA v3**.

---

## 11. Alur Utama (Key Flows)

**Alur Buyer → Inquiry:**
1. Buyer masuk (organik/iklan) → beranda (EN).
2. Lihat trust bar & sertifikasi → percaya.
3. Buka katalog → filter kategori → detail produk (HS Code, MOQ, packaging).
4. Klik "Request Quote" → form inquiry prefilled produk.
5. Submit → toast sukses + auto-reply email + opsi WhatsApp.
6. Sales terima notifikasi → tindak lanjut di admin → ubah status.

**Alur Admin → Publish Produk:**
1. Login admin → menu Produk → Tambah.
2. Isi field ID + EN (tab), spesifikasi, HS Code, MOQ, upload gambar (auto WebP).
3. Relasikan kategori & sertifikat → set featured → publish.
4. Produk tampil di katalog + masuk sitemap.

---

## 12. Peran & Hak Akses (Permission Matrix)

| Fitur | Super Admin | Admin CMS | Sales |
|-------|:-----------:|:---------:|:-----:|
| Kelola produk/kategori/berita/galeri | ✓ | ✓ | — |
| Kelola sertifikasi & pasar ekspor | ✓ | ✓ | — |
| Kelola downloads & halaman | ✓ | ✓ | — |
| Lihat & kelola inquiry | ✓ | lihat | ✓ |
| Export inquiry | ✓ | — | ✓ |
| Kelola user & role | ✓ | — | — |
| Pengaturan global | ✓ | sebagian | — |

---

## 13. Milestone & Estimasi

| Fase | Deliverable | Estimasi |
|------|-------------|----------|
| M0 | Setup proyek, auth, roles, schema, seeding, layout dasar | 1 minggu |
| M1 | Panel admin: produk, kategori, sertifikasi, media | 2 minggu |
| M2 | Halaman publik: beranda, about, katalog, detail produk (multibahasa) | 2 minggu |
| M3 | Inquiry/RFQ + notifikasi + WhatsApp, pasar ekspor, galeri | 1,5 minggu |
| M4 | Berita, downloads, halaman statis, settings | 1 minggu |
| M5 | SEO, structured data, sitemap, performa, hardening | 1 minggu |
| M6 | QA, UAT, konten, deploy, dokumentasi | 1 minggu |

**Total estimasi kasar:** ~9,5 minggu (dapat menyesuaikan kompleksitas & konten).

---

## 14. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Website tampil sempurna & responsif di desktop/tablet/mobile.
- [ ] Bahasa ID & EN berfungsi penuh; language switcher + hreflang benar.
- [ ] Semua produk memiliki HS Code, MOQ, packaging, dan minimal 1 gambar WebP.
- [ ] Form inquiry menyimpan data, mengirim notifikasi & auto-reply, dan menampilkan goey-toast.
- [ ] Admin non-teknis dapat CRUD seluruh konten tanpa developer.
- [ ] Role & permission bekerja sesuai matrix.
- [ ] Sitemap.xml, robots.txt, meta, dan JSON-LD tergenerate benar.
- [ ] Lighthouse ≥ 90 (Performance & SEO) di halaman utama & detail produk.
- [ ] Tidak ada N+1 query pada halaman katalog & detail.
- [ ] Semua gambar dikonversi WebP otomatis.
- [ ] Backup DB & HTTPS aktif di produksi.

---

## 15. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| Konten (foto/produk) belum siap | Delay peluncuran | Siapkan seeder + placeholder; paralelkan pengumpulan konten |
| Spam pada form inquiry | Data kotor, beban sales | reCAPTCHA v3 + honeypot + rate limit |
| Terjemahan EN tidak akurat | Kredibilitas turun | Review oleh penutur/penerjemah profesional |
| Sertifikat kedaluwarsa tidak terpantau | Legalitas dipertanyakan | Alert kedaluwarsa di dashboard |
| Performa turun karena media besar | SEO & UX buruk | WebP + responsive + lazy-load + CDN |

---

## 16. Peluang Pengembangan (Fase 2+)

- Portal B2B (login buyer, harga khusus, riwayat inquiry).
- Live chat / chatbot AI untuk tanya-jawab produk (RAG dari katalog).
- Integrasi ERP/inventory untuk stok & kapasitas real-time.
- Multi-currency & kalkulator estimasi (FOB/CIF).
- Bahasa tambahan (Arab, Mandarin) sesuai pasar tujuan.
- Sistem quotation otomatis + PDF penawaran.

---

*Dokumen ini merupakan spesifikasi kebutuhan (PRD) dan dapat disesuaikan sebelum & selama pengembangan berdasarkan kesepakatan dengan pemangku kepentingan.*
