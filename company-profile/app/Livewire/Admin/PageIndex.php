<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use App\Services\TranslationService;
use Illuminate\Support\Str;

class PageIndex extends Component
{
    // Tanpa WithPagination: daftar halamannya tidak lagi berpaginasi.
    use WithFileUploads;

    // Form fields
    public $isOpen = false;
    public $page_id;
    public $slug;
    public $status = 'draft';
    
    // Translation fields (id and en)
    /* Label = kata kecil di atas judul halaman publik. Ikut diterjemahkan,
       karena ia dibaca pengunjung sama seperti judulnya. */
    public $label_id, $title_id, $content_id;
    public $label_en, $title_en, $content_en;
    public bool $isTranslating = false;

    /*
     * Sebab kegagalan terjemahan, digambar di bawah kolom bahasanya.
     *
     * Bukan session()->flash('error', ...). Tombol Terjemahkan tidak memuat
     * ulang halaman, jadi kantong flash baru terbaca pada penggambaran
     * BERIKUTNYA — yang bisa terjadi di halaman lain, atau tidak terjadi
     * sama sekali sampai orangnya menekan sesuatu yang lain. Sebelum ini
     * tombolnya gagal tanpa mengatakan apa pun.
     */
    public ?string $galatTerjemah = null;

    /*
     * Bahasa yang sedang ditampilkan di modalnya. Kedua terjemahan tetap ada
     * di DOM; yang tidak aktif cuma disembunyikan, karena isian yang diketik
     * lalu elemennya lenyap membuat Livewire kehilangan nilainya.
     */
    public string $activeTab = 'en';

    /*
     * Susunan beranda.
     *
     * Beranda bukan baris di tabel 'pages' — ia dirakit dari bagian-bagian
     * tetap (hero, produk, sertifikasi, dan seterusnya) yang urutan dan
     * tampil-tidaknya disimpan sebagai satu larik JSON di kunci pengaturan
     * 'home_sections'. Karena itu bentuknya kartu tersendiri, bukan baris tabel.
     *
     * Tempatnya di sini, bukan di Pengaturan: yang diatur adalah isi halaman
     * publik, sama seperti halaman statis di tabel bawah.
     */
    public array $home_sections = [];

    /*
     * Daftar bawaan, dipakai kalau kuncinya belum pernah ada isinya sama
     * sekali. Urutannya sama dengan urutan bagian di beranda saat ini.
     */
    /*
     * Bagian yang BENAR-BENAR digambar beranda — tujuh, tidak lebih.
     *
     * Daftar ini harus sama dengan cabang @switch di blade beranda publik.
     * Yang ada di sini tapi tidak ada di sana jatuh ke cabang @default, dan
     * cabang itu menggambar kotak bergaris putus-putus bertuliskan
     * "[FRONTEND TASK: ...]" — ke pengunjung sungguhan, bukan ke panel.
     *
     * 'gallery' dan 'downloads' pernah ada di sini padahal berandanya tidak
     * pernah punya keduanya. Tersimpan mati, jadi tidak terlihat; tapi
     * sakelarnya ada di panel, dan satu ketukan memisahkan situs publik dari
     * tulisan itu.
     */
    private const BAGIAN_BAWAAN = [
        ['id' => 'hero',           'name' => 'Hero Slider',    'active' => true, 'order' => 1],
        ['id' => 'about',          'name' => 'About Us',       'active' => true, 'order' => 2],
        ['id' => 'products',       'name' => 'Our Products',   'active' => true, 'order' => 3],
        ['id' => 'export-markets', 'name' => 'Export Markets', 'active' => true, 'order' => 4],
        ['id' => 'certifications', 'name' => 'Certifications', 'active' => true, 'order' => 5],
        ['id' => 'news',           'name' => 'Latest News',    'active' => true, 'order' => 6],
        ['id' => 'contact',        'name' => 'Contact Us',     'active' => true, 'order' => 7],
    ];

    /* ══════════════════════════════════════════════════════════════════════
       ISI TIAP BAGIAN BERANDA
       ══════════════════════════════════════════════════════════════════════

       Teksnya menumpang di dalam larik JSON yang sama dengan urutannya, di
       bawah kunci 'isi', dipisah per bahasa:

           { "id": "hero", "active": true, "order": 1,
             "image": "settings/hero-xxx.jpg",
             "isi": { "en": { "title": "…", "body": "…" },
                      "id": { "title": "…", "body": "…" } } }

       Isian yang DIKOSONGKAN jatuh ke teks bawaan di berkas bahasa site.php.
       Itu disengaja: beranda tidak boleh pernah tergambar hampa hanya karena
       satu kolom belum diisi, dan bawaan yang sudah diterjemahkan lebih baik
       daripada ruang kosong.

       (Jangan menulis alamat berkas berpola bintang-garis-miring di dalam
       komentar blok seperti ini — urutan itu menutup komentarnya lebih awal
       dan sisa berkasnya berhenti terbaca sebagai komentar.)
    */

    /**
     * Isian apa saja yang dimiliki tiap bagian.
     *
     * Ditulis sebagai data, bukan sebagai borang yang diketik satu-satu, supaya
     * menambah bagian berikutnya cukup menambah satu entri di sini.
     *
     * 'jenis': 'teks' kolom sebaris, 'kaya' penyunting teks kaya.
     * 'bawaan': kunci lang yang dipakai kalau isiannya kosong — ditampilkan
     * sebagai placeholder supaya jelas apa yang akan muncul bila dibiarkan.
     */
    /** Keterangan konvensi penekanan, dipakai di tiap medan judul besar. */
    public const CATATAN_TEKANAN =
        'Kata yang diapit bintang — *seperti ini* — digambar dengan huruf serif miring, sebagai penekanan di dalam judul.';

    public const BIDANG_BAGIAN = [
        /*
         * Urutan medan mengikuti urutan yang TERGAMBAR di hero, dari atas ke
         * bawah: label kecil, judul besar, deskripsi, lalu dua tombol. Kolom
         * yang disusun mengikuti hasilnya bisa diperiksa sambil diisi; yang
         * disusun mengikuti sejarah penambahannya tidak.
         *
         * 'years_label' dibuang — kartu "Angka pengalaman" yang dulu berdiri di
         * atas foto hero sudah tidak digambar lagi, dan kolom yang tidak
         * menghasilkan apa pun di halaman adalah janji yang tidak ditepati.
         */
        'hero' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'descriptor', 'label' => 'Label', 'jenis' => 'teks', 'bawaan' => 'site.hero_descriptor'],
            ['nama' => 'title',         'label' => 'Judul besar',        'jenis' => 'teks',  'bawaan' => 'site.hero_title', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',          'label' => 'Deskripsi',          'jenis' => 'kaya',  'bawaan' => 'site.hero_body'],
            ['kelompok' => 'Tombol', 'nama' => 'cta_primary',   'label' => 'Tombol utama',       'jenis' => 'teks',  'bawaan' => 'site.cta_request_quote'],
            ['nama' => 'cta_secondary', 'label' => 'Tombol kedua',       'jenis' => 'teks',  'bawaan' => 'site.cta_explore_products'],
        ],

        'products' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.home_section_products'],
            ['nama' => 'title',   'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.products_title', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',    'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.products_body'],
            ['kelompok' => 'Daftar produk', 'nama' => 'cta',     'label' => 'Label tombol',              'jenis' => 'teks', 'bawaan' => 'site.cta_explore_products'],
            ['nama' => 'view_label', 'label' => 'Label tautan tiap produk', 'jenis' => 'teks', 'bawaan' => 'site.view_details'],
            ['nama' => 'empty',   'label' => 'Teks saat belum ada produk unggulan', 'jenis' => 'teks', 'bawaan' => 'site.no_featured_products'],
        ],

        'export-markets' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.home_section_export_markets'],
            [
                'nama'    => 'title',
                'label'   => 'Judul',
                'jenis'   => 'teks',
                'bawaan'  => 'site.markets_title',
                'catatan' => 'Tulis :count di tempat yang ingin diisi jumlah negara tujuan. '
                           . 'Tanpa itu, judulnya tidak akan menyebut angka sama sekali. '
                           . self::CATATAN_TEKANAN,
            ],
            ['nama' => 'body',  'label' => 'Deskripsi',        'jenis' => 'kaya', 'bawaan' => 'site.markets_body'],
            ['kelompok' => 'Daftar negara', 'nama' => 'cta',   'label' => 'Label tombol',     'jenis' => 'teks', 'bawaan' => 'site.cta_explore_markets'],
            ['nama' => 'empty', 'label' => 'Teks saat belum ada negara tujuan', 'jenis' => 'teks', 'bawaan' => 'site.no_export_markets'],
        ],

        'about' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.pillars_eyebrow'],
            ['nama' => 'title',   'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.pillars_title', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',    'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.pillars_body'],

            ['kelompok' => 'Empat kartu pilar', 'nama' => 'pillar_1_title', 'label' => 'Kartu 1 — judul',     'jenis' => 'teks',    'bawaan' => 'site.pillar_1_title'],
            ['nama' => 'pillar_1_body',  'label' => 'Kartu 1 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.pillar_1_body'],
            ['nama' => 'pillar_2_title', 'label' => 'Kartu 2 — judul',      'jenis' => 'teks',    'bawaan' => 'site.pillar_2_title'],
            ['nama' => 'pillar_2_body',  'label' => 'Kartu 2 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.pillar_2_body'],
            ['nama' => 'pillar_3_title', 'label' => 'Kartu 3 — judul',      'jenis' => 'teks',    'bawaan' => 'site.pillar_3_title'],
            ['nama' => 'pillar_3_body',  'label' => 'Kartu 3 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.pillar_3_body'],
            ['nama' => 'pillar_4_title', 'label' => 'Kartu 4 — judul',      'jenis' => 'teks',    'bawaan' => 'site.pillar_4_title'],
            ['nama' => 'pillar_4_body',  'label' => 'Kartu 4 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.pillar_4_body'],
        ],

        'news' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow',     'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.news_eyebrow'],
            ['nama' => 'title',       'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.news_title', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',        'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.news_body'],
            ['kelompok' => 'Daftar artikel', 'nama' => 'read_label',  'label' => 'Label tautan tiap artikel', 'jenis' => 'teks', 'bawaan' => 'site.read_article'],
            ['nama' => 'cta',   'label' => 'Label tombol ke halaman berita', 'jenis' => 'teks', 'bawaan' => 'site.cta_see_more_news'],
            ['nama' => 'empty', 'label' => 'Teks saat belum ada artikel', 'jenis' => 'teks', 'bawaan' => 'site.no_news_found'],
        ],

        'contact' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'title',        'label' => 'Judul',                'jenis' => 'teks', 'bawaan' => 'site.cta_title', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',         'label' => 'Deskripsi',            'jenis' => 'kaya', 'bawaan' => 'site.cta_body'],
            ['kelompok' => 'Tombol', 'nama' => 'cta_primary',  'label' => 'Label tombol utama',   'jenis' => 'teks', 'bawaan' => 'site.cta_request_quote'],
            [
                'nama'    => 'cta_whatsapp',
                'label'   => 'Label tombol WhatsApp',
                'jenis'   => 'teks',
                'bawaan'  => 'site.cta_whatsapp',
                'catatan' => 'Tombolnya hanya digambar kalau nomor WhatsApp sudah diisi di Pengaturan.',
            ],
        ],
    ];

    /**
     * Pengaturan bagian yang BUKAN teks, jadi tidak punya versi per bahasa.
     *
     * Angka seperti "berapa produk ditampilkan" sama saja di bahasa mana pun.
     * Menyimpannya bersama teks per bahasa berarti angka yang sama tertulis
     * dua kali dan bisa berbeda — sesuatu yang tidak masuk akal untuk dijawab.
     */
    public const OPSI_BAGIAN = [
        'products' => [
            [
                'nama'    => 'jumlah',
                'label'   => 'Jumlah produk ditampilkan',
                'jenis'   => 'angka',
                'min'     => 1,
                'max'     => 12,
                'bawaan'  => 6,
                'catatan' => 'Diambil dari produk yang ditandai unggulan dan berstatus terbit, '
                           . 'urut menurut urutan di menu Produk.',
            ],
        ],
    ];

    /* ══════════════════════════════════════════════════════════════════════
       HALAMAN PUBLIK SELAIN BERANDA
       ══════════════════════════════════════════════════════════════════════

       Kedelapan halaman ini punya kepala yang bentuknya seragam — label kecil,
       judul, lalu deskripsi — dan sisanya dirakit dari data. Yang bisa
       disunting karena itu kepalanya, teks keadaan kosongnya, dan beberapa
       label yang memang kalimat, bukan kendali borang.

       Label isian borang (nama, email, negara, dan seterusnya) sengaja TIDAK
       dibuka: ia bagian dari cara halaman itu bekerja, bukan pesan yang ingin
       disampaikan perusahaan, dan seluruhnya sudah diterjemahkan.
    */
    /**
     * Kesepuluh halaman publik dalam SATU senarai, urut seperti pengunjung
     * menemuinya — beranda dulu, footer terakhir.
     *
     * 'jenis' menentukan apa yang dibuka barisnya:
     *   'susunan' — halaman yang dirakit dari bagian-bagian yang bisa
     *               diurutkan dan dimatikan (Beranda, Profile).
     *   'isi'     — halaman berkepala satu; yang bisa ditulis kepalanya.
     */
    public const DAFTAR_HALAMAN = [
        ['id' => 'home',           'nama' => 'Home',           'jenis' => 'susunan', 'rute' => 'home'],
        ['id' => 'profile',        'nama' => 'Profile',        'jenis' => 'susunan', 'rute' => 'about'],
        ['id' => 'certifications', 'nama' => 'Certifications', 'jenis' => 'isi',     'rute' => 'certifications.index'],
        ['id' => 'products',       'nama' => 'Products',       'jenis' => 'isi',     'rute' => 'products.index'],
        ['id' => 'export-markets', 'nama' => 'Export Markets', 'jenis' => 'isi',     'rute' => 'export-markets.index'],
        ['id' => 'news',           'nama' => 'News',           'jenis' => 'isi',     'rute' => 'news.index'],
        ['id' => 'gallery',        'nama' => 'Gallery',        'jenis' => 'isi',     'rute' => 'gallery.index'],
        ['id' => 'downloads',      'nama' => 'Downloads',      'jenis' => 'isi',     'rute' => 'downloads.index'],
        ['id' => 'contact',        'nama' => 'Contact Us',     'jenis' => 'isi',     'rute' => 'inquiry.index'],

        /* Footer tidak punya alamatnya sendiri — ia menempel di semua
           halaman. Barisnya karena itu tidak menawarkan "Lihat halaman". */
        ['id' => 'footer',         'nama' => 'Footer',         'jenis' => 'isi',     'rute' => null],
    ];

    public const HALAMAN_PUBLIK = [
        ['id' => 'certifications', 'nama' => 'Certifications', 'rute' => 'certifications.index'],
        ['id' => 'products',       'nama' => 'Products',       'rute' => 'products.index'],
        ['id' => 'export-markets', 'nama' => 'Export Markets', 'rute' => 'export-markets.index'],
        ['id' => 'news',           'nama' => 'News',           'rute' => 'news.index'],
        ['id' => 'gallery',        'nama' => 'Gallery',        'rute' => 'gallery.index'],
        ['id' => 'downloads',      'nama' => 'Downloads',      'rute' => 'downloads.index'],
        ['id' => 'contact',        'nama' => 'Contact Us',     'rute' => 'inquiry.index'],
    ];

    /* ══════════════════════════════════════════════════════════════════════
       HALAMAN PROFILE

       Profile bukan halaman berkepala-satu seperti tujuh lainnya: ia tersusun
       dari lima bagian yang berdiri sendiri, dan tiap bagian punya judul serta
       isinya masing-masing. Karena itu ia dapat kartu sendiri di panel, dengan
       urutan dan sakelar tampil — sama seperti susunan beranda.

       Isinya tetap menumpang di satu tempat, 'page_contents' → 'profile' →
       'isi', dengan nama bidang yang datar. Yang dipecah cuma cara
       menyuntingnya; memecah penyimpanannya juga hanya akan membuat isi yang
       sama tersebar di lima tempat tanpa alasan.
    */
    public const PROFIL_BAWAAN = [
        ['id' => 'profil',          'name' => 'Profil',              'active' => true, 'order' => 1],
        ['id' => 'vision_mission',  'name' => 'Visi & Misi',         'active' => true, 'order' => 2],
        ['id' => 'values',          'name' => 'Nilai',               'active' => true, 'order' => 3],
        ['id' => 'history',         'name' => 'Sejarah',             'active' => true, 'order' => 4],
        ['id' => 'certification',   'name' => 'Kartu Sertifikasi',   'active' => true, 'order' => 5],
    ];

    public const BIDANG_PROFIL = [
        'profil' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow',  'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.nav_about'],
            ['nama' => 'headline', 'label' => 'Judul halaman',            'jenis' => 'teks', 'bawaan' => 'site.about_headline', 'catatan' => self::CATATAN_TEKANAN],
            [
                'nama'    => 'body',
                'label'   => 'Deskripsi',
                'jenis'   => 'kaya',
                'bawaan'  => 'site.about_empty',
                'catatan' => 'Paragraf di kolom kanan, sebelah judul. Isi ini dulu ditulis '
                           . 'sebagai halaman statis beralamat /page/about-us.',
            ],
        ],

        'vision_mission' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'vm_eyebrow',    'label' => 'Label kecil',  'jenis' => 'teks',    'bawaan' => 'site.vision_mission_eyebrow'],
            ['nama' => 'vm_title',      'label' => 'Judul',        'jenis' => 'teks',    'bawaan' => 'site.vision_mission_title', 'catatan' => self::CATATAN_TEKANAN],
            ['kelompok' => 'Visi', 'nama' => 'vision_label',  'label' => 'Sebutan visi', 'jenis' => 'teks',    'bawaan' => 'site.vision_label'],
            ['nama' => 'vision_body',   'label' => 'Isi visi',     'jenis' => 'panjang', 'bawaan' => 'site.vision_body'],
            ['kelompok' => 'Misi', 'nama' => 'mission_label', 'label' => 'Sebutan misi', 'jenis' => 'teks',    'bawaan' => 'site.mission_label'],
            ['nama' => 'mission_1_title', 'label' => 'Misi 1 — judul', 'jenis' => 'teks', 'bawaan' => 'site.mission_1_title'],
            ['nama' => 'mission_1_body',  'label' => 'Misi 1 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.mission_1_body'],
            ['nama' => 'mission_2_title', 'label' => 'Misi 2 — judul',      'jenis' => 'teks',    'bawaan' => 'site.mission_2_title'],
            ['nama' => 'mission_2_body',  'label' => 'Misi 2 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.mission_2_body'],
            ['nama' => 'mission_3_title', 'label' => 'Misi 3 — judul',      'jenis' => 'teks',    'bawaan' => 'site.mission_3_title'],
            ['nama' => 'mission_3_body',  'label' => 'Misi 3 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.mission_3_body'],
        ],

        'values' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'values_eyebrow', 'label' => 'Label kecil', 'jenis' => 'teks', 'bawaan' => 'site.values_eyebrow'],
            ['nama' => 'values_title',   'label' => 'Judul',       'jenis' => 'teks', 'bawaan' => 'site.values_title', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'values_body',    'label' => 'Deskripsi',   'jenis' => 'kaya', 'bawaan' => 'site.values_body'],
            ['kelompok' => 'Empat kartu nilai', 'nama' => 'value_1_title', 'label' => 'Nilai 1 — judul', 'jenis' => 'teks', 'bawaan' => 'site.value_1_title'],
            ['nama' => 'value_1_body',  'label' => 'Nilai 1 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.value_1_body'],
            ['nama' => 'value_2_title', 'label' => 'Nilai 2 — judul',      'jenis' => 'teks',    'bawaan' => 'site.value_2_title'],
            ['nama' => 'value_2_body',  'label' => 'Nilai 2 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.value_2_body'],
            ['nama' => 'value_3_title', 'label' => 'Nilai 3 — judul',      'jenis' => 'teks',    'bawaan' => 'site.value_3_title'],
            ['nama' => 'value_3_body',  'label' => 'Nilai 3 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.value_3_body'],
            ['nama' => 'value_4_title', 'label' => 'Nilai 4 — judul',      'jenis' => 'teks',    'bawaan' => 'site.value_4_title'],
            ['nama' => 'value_4_body',  'label' => 'Nilai 4 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.value_4_body'],
        ],

        'history' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'history_eyebrow', 'label' => 'Label kecil', 'jenis' => 'teks', 'bawaan' => 'site.history_eyebrow'],
            ['nama' => 'history_title',        'label' => 'Judul',                   'jenis' => 'teks', 'bawaan' => 'site.history_title', 'catatan' => self::CATATAN_TEKANAN],

            /*
             * Garis waktu: tahun berdiri, lalu keenam tonggak sebagai rangkai
             * tahun–judul–keterangan.
             *
             * 'jenis' => 'opsi' berarti slot ini diisi oleh skema opsi bernama
             * sama di opsiProfil(). Yang dinyatakan di sini hanya DI MANA ia
             * muncul; label, batas angkanya, dan tempat simpannya tetap satu di
             * sana — bukan disalin ke sini untuk lama-lama berbeda.
             *
             * Angka tahun dulu terlempar ke kartu "Pengaturan" tersendiri,
             * terpisah dari judul tonggak yang justru ia beri tahun. Mengisi
             * satu tonggak berarti melompat antara dua kartu.
             */
            ['kelompok' => 'Garis waktu', 'nama' => 'established_year', 'jenis' => 'opsi'],

            ['nama' => 'milestone_1_year',  'jenis' => 'opsi'],
            ['nama' => 'milestone_1_title', 'label' => 'Tonggak 1 — judul',      'jenis' => 'teks',    'bawaan' => 'site.milestone_1_title'],
            ['nama' => 'milestone_1_body',  'label' => 'Tonggak 1 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.milestone_1_body'],
            ['nama' => 'milestone_1_image', 'label' => 'Tonggak 1 — gambar', 'jenis' => 'gambar'],

            ['nama' => 'milestone_2_year',  'jenis' => 'opsi'],
            ['nama' => 'milestone_2_title', 'label' => 'Tonggak 2 — judul',      'jenis' => 'teks',    'bawaan' => 'site.milestone_2_title'],
            ['nama' => 'milestone_2_body',  'label' => 'Tonggak 2 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.milestone_2_body'],
            ['nama' => 'milestone_2_image', 'label' => 'Tonggak 2 — gambar', 'jenis' => 'gambar'],

            ['nama' => 'milestone_3_year',  'jenis' => 'opsi'],
            ['nama' => 'milestone_3_title', 'label' => 'Tonggak 3 — judul',      'jenis' => 'teks',    'bawaan' => 'site.milestone_3_title'],
            ['nama' => 'milestone_3_body',  'label' => 'Tonggak 3 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.milestone_3_body'],
            ['nama' => 'milestone_3_image', 'label' => 'Tonggak 3 — gambar', 'jenis' => 'gambar'],

            ['nama' => 'milestone_4_year',  'jenis' => 'opsi'],
            ['nama' => 'milestone_4_title', 'label' => 'Tonggak 4 — judul',      'jenis' => 'teks',    'bawaan' => 'site.milestone_4_title'],
            ['nama' => 'milestone_4_body',  'label' => 'Tonggak 4 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.milestone_4_body'],
            ['nama' => 'milestone_4_image', 'label' => 'Tonggak 4 — gambar', 'jenis' => 'gambar'],

            ['nama' => 'milestone_5_year',  'jenis' => 'opsi'],
            ['nama' => 'milestone_5_title', 'label' => 'Tonggak 5 — judul',      'jenis' => 'teks',    'bawaan' => 'site.milestone_5_title'],
            ['nama' => 'milestone_5_body',  'label' => 'Tonggak 5 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.milestone_5_body'],
            ['nama' => 'milestone_5_image', 'label' => 'Tonggak 5 — gambar', 'jenis' => 'gambar'],

            ['nama' => 'milestone_6_year',  'jenis' => 'opsi'],
            ['nama' => 'milestone_6_title', 'label' => 'Tonggak 6 — judul',      'jenis' => 'teks',    'bawaan' => 'site.milestone_6_title'],
            ['nama' => 'milestone_6_body',  'label' => 'Tonggak 6 — keterangan', 'jenis' => 'panjang', 'bawaan' => 'site.milestone_6_body'],
            ['nama' => 'milestone_6_image', 'label' => 'Tonggak 6 — gambar', 'jenis' => 'gambar'],
        ],

        'certification' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'cert_eyebrow', 'label' => 'Label kecil',  'jenis' => 'teks',    'bawaan' => 'site.certifications'],
            ['nama' => 'cert_title',   'label' => 'Judul',        'jenis' => 'teks',    'bawaan' => 'site.cert_card_title', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'cert_body',    'label' => 'Keterangan',   'jenis' => 'panjang', 'bawaan' => 'site.cert_card_body'],
            ['kelompok' => 'Tombol', 'nama' => 'cert_cta',     'label' => 'Label tombol', 'jenis' => 'teks',    'bawaan' => 'site.cta_view_certifications'],
        ],
    ];

    /** Bagian Profile yang punya foto sendiri. */
    public const PROFIL_BERFOTO = ['profil'];

    /**
     * Pengaturan bukan-teks tiap bagian Profile.
     *
     * Metode, bukan konstanta, karena batas atasnya tahun berjalan — dan
     * konstanta tidak bisa memanggil date().
     *
     * 'sumber' => 'setting' menandai bahwa nilainya TIDAK disimpan di dalam
     * JSON isi halaman, melainkan tetap di kunci pengaturannya sendiri. Tahun
     * berdiri dibaca dua tempat yang berbeda — garis waktu di halaman Profile
     * dan angka pengalaman di hero beranda — jadi memindahkannya ke dalam isi
     * halaman Profile akan membuat beranda bergantung pada isi halaman lain.
     * Yang berpindah cuma tempat menyuntingnya.
     */
    /**
     * Pengaturan bukan-teks milik HALAMAN publik.
     *
     * Bentuknya sama dengan opsiProfil(): yang menentukan label, jenis, dan
     * tempat simpannya cuma satu tempat — di sini — sementara BIDANG_HALAMAN
     * cuma menyatakan DI MANA ia muncul di antara kolom teksnya.
     */
    public static function opsiHalaman(): array
    {
        return [
            'gallery' => [
                [
                    'nama'    => 'video_url',
                    'label'   => 'Alamat video YouTube',
                    'jenis'   => 'teks',
                    'bawaan'  => '',
                    'catatan' => 'Tempelkan alamat videonya — bentuk apa pun boleh: '
                               . 'youtube.com/watch?v=…, youtu.be/…, atau Shorts. '
                               . 'Videonya tergambar di halaman Galeri, tepat di bawah '
                               . 'deskripsi. Dikosongkan berarti bloknya tidak digambar '
                               . 'sama sekali — bukan kotak kosong.',
                ],
            ],
        ];
    }

    public static function opsiProfil(): array
    {
        return [
            'history' => [
                [
                    'nama'    => 'established_year',
                    'label'   => 'Tahun berdiri',
                    'jenis'   => 'angka',
                    'sumber'  => 'setting',
                    'kunci'   => 'established_year',
                    'min'     => 1900,
                    'max'     => (int) date('Y'),
                    'bawaan'  => '',
                    'catatan' => 'Titik awal garis waktu di bawah. Tonggak yang tahunnya '
                               . 'dikosongkan dihitung sendiri dari sini sampai tahun '
                               . 'berjalan. Angka ini juga dipakai menghitung "tahun '
                               . 'pengalaman" di beranda. Dikosongkan berarti garis waktunya '
                               . 'memakai tahun berjalan — dan tampak seolah perusahaan ini '
                               . 'baru berdiri tahun ini.',
                ],

                /*
                 * Tahun tiap tonggak — pengaturan, bukan teks per bahasa.
                 *
                 * "2015" sama saja dibaca dalam bahasa mana pun. Menaruhnya di
                 * kartu teks berarti ia diketik dua kali dan bisa berbeda antara
                 * Indonesia dan English — sesuatu yang tidak masuk akal untuk
                 * dijawab, dan yang kalah selalu pembacanya.
                 *
                 * Boleh kosong, dan kosong bukan berarti hampa: tonggak yang
                 * tahunnya tidak diisi kembali dihitung sendiri dari tahun
                 * berdiri sampai tahun berjalan, persis seperti sebelum kolom
                 * ini ada. Jadi yang sudah berjalan tidak berubah apa-apa
                 * sampai ada yang benar-benar diketik.
                 */
                ...self::tahunTonggak(),
            ],
        ];
    }

    /**
     * Keenam kolom tahun tonggak.
     *
     * Dibangkitkan, bukan ditulis enam kali: keenamnya hanya berbeda pada
     * nomornya, dan enam salinan yang nyaris sama adalah enam tempat yang harus
     * diperbaiki tiap kali ada perubahan.
     */
    private static function tahunTonggak(): array
    {
        $keluar = [];

        for ($i = 1; $i <= 6; $i++) {
            $keluar[] = [
                'nama'   => 'milestone_' . $i . '_year',
                'label'  => 'Tonggak ' . $i . ' — tahun',
                'jenis'  => 'angka',
                /*
                 * Batas atasnya longgar, bukan tahun berjalan: garis waktu boleh
                 * memuat tonggak yang direncanakan. Letak titiknya toh tidak
                 * bergantung pada tahunnya — keenamnya berjarak sama rata
                 * menurut urutan, dan tahunnya cuma tulisan di bawah titik.
                 */
                'min'    => 1900,
                'max'    => (int) date('Y') + 20,
                'bawaan' => '',

                /* Kosong berarti "hitung sendiri", jadi tidak boleh dipaksa
                   diisi — lihat keterangan di kolom Tahun berdiri. */
                'boleh_kosong' => true,

                'catatan' => $i === 1
                    ? 'Dikosongkan berarti tahunnya dihitung sendiri dari Tahun berdiri '
                      . 'sampai tahun berjalan, seperti sebelumnya. Boleh diisi sebagian.'
                    : null,
            ];
        }

        return $keluar;
    }

    public const BIDANG_HALAMAN = [
        'certifications' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.certifications'],
            ['nama' => 'title',   'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.page_certifications', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',    'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.page_certifications_sub'],
            ['kelompok' => 'Daftar sertifikat', 'nama' => 'empty',   'label' => 'Teks saat belum ada sertifikat', 'jenis' => 'teks', 'bawaan' => 'site.no_certifications'],
        ],

        'products' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.home_section_products'],
            ['nama' => 'title',   'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.page_products', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',    'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.page_products_sub'],
            ['kelompok' => 'Daftar produk', 'nama' => 'view_label', 'label' => 'Label tautan tiap produk', 'jenis' => 'teks', 'bawaan' => 'site.view_details'],
            ['nama' => 'empty',   'label' => 'Teks saat tidak ada produk yang cocok', 'jenis' => 'teks', 'bawaan' => 'site.no_products_found'],

            /*
             * SATU label tombol, bukan judul dan keterangan kartu.
             *
             * Kartu katalognya sudah tidak ada di halaman publik — yang tersisa
             * sebuah tombol. Dua kolom yang mengisi judul dan keterangan kartu itu
             * jadi kolom yang bisa diisi tapi tidak muncul di mana pun, dan itu
             * lebih buruk daripada tidak ada kolomnya: yang mengisinya akan
             * mencari-cari hasilnya di halaman yang tidak pernah berubah.
             */
            ['kelompok' => 'Tombol katalog', 'nama' => 'catalog_cta', 'label' => 'Label tombol', 'jenis' => 'teks', 'bawaan' => 'site.download_pdf'],
        ],

        'export-markets' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.home_section_export_markets'],
            ['nama' => 'title',   'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.page_export_markets', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',    'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.page_export_markets_sub'],
            ['kelompok' => 'Daftar negara', 'nama' => 'empty',   'label' => 'Teks saat belum ada negara tujuan', 'jenis' => 'teks', 'bawaan' => 'site.no_export_markets'],
        ],

        'news' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow',  'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.news_eyebrow'],
            ['nama' => 'title',    'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.page_news', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',     'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.page_news_sub'],
            ['kelompok' => 'Daftar artikel', 'nama' => 'featured', 'label' => 'Label artikel unggulan',    'jenis' => 'teks', 'bawaan' => 'site.featured_article'],
            ['nama' => 'read_label', 'label' => 'Label tautan tiap artikel', 'jenis' => 'teks', 'bawaan' => 'site.read_article'],
            ['nama' => 'empty',    'label' => 'Teks saat tidak ada artikel yang cocok', 'jenis' => 'teks', 'bawaan' => 'site.no_news_found'],
        ],

        'gallery' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.nav_gallery'],
            ['nama' => 'title',   'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.page_gallery', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',    'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.page_gallery_sub'],

            /*
             * Video sorotan: satu alamat YouTube, bukan teks per bahasa.
             *
             * Alamatnya sama dibaca dalam bahasa mana pun — menaruhnya di kartu
             * teks berarti ia diketik dua kali dan bisa berbeda antara Indonesia
             * dan English, sesuatu yang tidak masuk akal untuk dijawab.
             */
            ['kelompok' => 'Video sorotan', 'nama' => 'video_url', 'jenis' => 'opsi'],

            ['kelompok' => 'Daftar album', 'nama' => 'empty',   'label' => 'Teks saat belum ada isi galeri', 'jenis' => 'teks', 'bawaan' => 'site.no_gallery_items'],
        ],

        'downloads' => [
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.nav_downloads'],
            ['nama' => 'title',   'label' => 'Judul',                     'jenis' => 'teks', 'bawaan' => 'site.page_downloads', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'body',    'label' => 'Deskripsi',                 'jenis' => 'kaya', 'bawaan' => 'site.page_downloads_sub'],
            ['kelompok' => 'Daftar berkas', 'nama' => 'empty',   'label' => 'Teks saat belum ada berkas', 'jenis' => 'teks', 'bawaan' => 'site.no_downloads'],
            [
                'nama'    => 'gated_note',
                'label'   => 'Keterangan berkas bergerbang email',
                'jenis'   => 'panjang',
                'bawaan'  => 'site.download_gated_note',
                'catatan' => 'Muncul di berkas yang menuntut email sebelum bisa diunduh.',
            ],
        ],

        /*
         * Footer bukan halaman, tapi ia tergambar di SETIAP halaman — dan
         * satu-satunya kalimatnya sendiri selama ini tidak bisa disentuh dari
         * panel. Sisanya memang bukan tulisan: alamat, kontak, jam, dan
         * tautan sosial datang dari Pengaturan, dan label navigasinya
         * mengikuti nama halamannya masing-masing.
         */
        'footer' => [
            ['kelompok' => 'Ajakan', 'nama' => 'headline', 'label' => 'Ajakan besar di kepala footer', 'jenis' => 'panjang', 'bawaan' => 'site.footer_headline', 'catatan' => self::CATATAN_TEKANAN],
            [
                'nama'    => 'body',
                'label'   => 'Deskripsi di atas tombol',
                'jenis'   => 'panjang',
                'bawaan'  => 'site.footer_body',
                'catatan' => 'Dua sampai tiga baris, berdiri tepat di atas tombol Minta Penawaran.',
            ],
        ],

        'contact' => [
            /*
             * Label kecil di atas judul — kolom yang selama ini TIDAK ada di
             * halaman ini saja, sementara enam halaman publik lain punya.
             * Akibatnya kepala halaman Kontak berdiri tanpa penanda golongan,
             * dan yang menyuntingnya dari panel tidak punya cara menambahkannya.
             */
            ['kelompok' => 'Judul & deskripsi', 'nama' => 'eyebrow', 'label' => 'Label kecil di atas judul', 'jenis' => 'teks', 'bawaan' => 'site.nav_contact'],
            ['nama' => 'headline',   'label' => 'Judul halaman', 'jenis' => 'teks', 'bawaan' => 'site.inquiry_headline', 'catatan' => self::CATATAN_TEKANAN],
            ['nama' => 'intro',      'label' => 'Deskripsi',     'jenis' => 'kaya', 'bawaan' => 'site.inquiry_intro'],
            ['kelompok' => 'Peta', 'nama' => 'map_title',  'label' => 'Judul peta',    'jenis' => 'teks', 'bawaan' => 'site.find_us'],

            ['kelompok' => 'Formulir', 'nama' => 'form_title', 'label' => 'Judul formulir', 'jenis' => 'teks', 'bawaan' => 'site.inquiry_form_title'],
            ['nama' => 'form_intro', 'label' => 'Keterangan di bawah judul formulir', 'jenis' => 'panjang', 'bawaan' => 'site.inquiry_form_intro'],

            ['kelompok' => 'Setelah terkirim', 'nama' => 'success_title', 'label' => 'Judul', 'jenis' => 'teks', 'bawaan' => 'site.inquiry_success'],
            ['nama' => 'success_body', 'label' => 'Keterangan',            'jenis' => 'panjang', 'bawaan' => 'site.inquiry_thank_you'],
            ['nama' => 'send_another', 'label' => 'Label kirim lagi',      'jenis' => 'teks',    'bawaan' => 'site.send_another'],
        ],
    ];

    /** Halaman yang punya foto sendiri. */
    public const HALAMAN_BERFOTO = [];

    /** Bagian yang punya foto sendiri. */
    public const BAGIAN_BERFOTO = ['hero', 'contact'];

    /** Keterangan foto tiap bagian, supaya ukurannya jelas sebelum diunggah. */
    public const CATATAN_FOTO = [
        'hero'    => 'Melebar penuh di bawah teks hero, sebaiknya 1920×1080 piksel. '
                   . 'Dikosongkan berarti tempatnya digambar sebagai kotak penanda.',
        'contact' => 'Latar kartu ajakan di bawah beranda, ditumpuk lapisan hijau gelap '
                   . 'supaya teksnya tetap terbaca. Dikosongkan berarti latarnya hijau polos.',

        'profil'  => 'Foto di sisi kanan bagian profil, tegak atau persegi. '
                   . 'Dikosongkan berarti tempatnya digambar sebagai kotak penanda.',
    ];

    /**
     * Isi kedelapan halaman publik, dibaca dari kunci pengaturan 'page_contents'.
     *
     * Berdiri terpisah dari home_sections: halaman tidak punya urutan atau
     * sakelar tampil untuk ditumpangi, jadi menumpangkannya di sana hanya
     * membuat satu larik memikul dua urusan yang berbeda.
     */
    public array $halaman_publik = [];

    /**
     * Susunan bagian halaman Profile.
     *
     * Menumpang di 'page_contents' → 'profile' → 'sections', bersebelahan
     * dengan isinya. Bentuknya sama dengan susunan beranda: id, nama, aktif,
     * urutan.
     */
    public array $profile_sections = [];

    /** 'bagian' beranda, 'halaman' publik, atau 'profil' bagian halaman Profile. */
    public string $jenisDibuka = 'bagian';

    /** Bagian atau halaman yang sedang dibuka isinya; null berarti tertutup. */
    public ?string $bagianDibuka = null;

    /** isiBagian[locale][nama] — nilai yang sedang disunting di modal. */
    public array $isiBagian = [];

    /** opsiBagian[nama] — pengaturan bukan-teks yang sedang disunting. */
    public array $opsiBagian = [];

    public $gambarBagian;
    public ?string $gambarBagianLama = null;

    /*
     * Gambar per tonggak sejarah, berkunci nama slotnya (milestone_1_image, …).
     *
     * Terpisah dari $gambarBagian karena keduanya menjawab hal yang berbeda:
     * yang satu foto MILIK BAGIAN — satu untuk seluruh halaman Profile — yang
     * satu lagi gambar milik satu baris di dalam bagian. Menumpangkannya pada
     * kunci 'image' yang sama akan membuat gambar tonggak keenam menimpa foto
     * halamannya.
     */
    public array $gambarTonggak = [];
    public array $gambarTonggakLama = [];

    public function mount(): void
    {
        $nilai = Setting::pluck('value', 'key');


        /*
         * json_decode() mengembalikan null untuk JSON yang rusak — diperiksa
         * supaya satu baris pengaturan yang cacat tidak mematikan seluruh
         * halaman ini, yang justru satu-satunya tempat memperbaikinya.
         */
        $isiHalaman = json_decode($nilai[\App\Support\IsiHalaman::KUNCI] ?? '', true);
        $this->halaman_publik = is_array($isiHalaman) ? $isiHalaman : [];

        $this->muatSusunanProfil();

        $tersimpan = Setting::where('key', 'home_sections')->value('value');
        $bagian = $tersimpan ? json_decode($tersimpan, true) : null;

        /*
         * json_decode() mengembalikan null untuk JSON yang rusak. Tanpa
         * pemeriksaan is_array(), halaman ini akan mati dengan galat foreach
         * atas null — dan satu-satunya cara memperbaikinya lewat basis data.
         */
        if (! is_array($bagian) || $bagian === []) {
            $this->home_sections = self::BAGIAN_BAWAAN;
            $this->simpanBagian();
            $this->pindahkanFotoHeroLama($nilai);

            return;
        }

        $this->home_sections = $bagian;

        /*
         * Susunan tersimpan didamaikan dengan BAGIAN_BAWAAN — dua arah, karena
         * keduanya sama-sama pernah terjadi:
         *
         *   BUANG  bagian tersimpan yang tidak dikenal lagi. Beranda publik
         *          hanya bisa menggambar id yang ada di cabang @switch
         *          bladenya; yang di luar itu jatuh ke @default, dan @default
         *          menggambar kotak "[FRONTEND TASK: ...]" — ke pengunjung
         *          sungguhan, bukan ke panel. Selama bagiannya masih duduk di
         *          JSON, sakelarnya masih ada di panel: satu ketukan dari
         *          menerbitkan tulisan itu. ('gallery' dan 'downloads' persis
         *          begitu — tersimpan mati, jadi tidak pernah terlihat.)
         *
         *   SISIP  bagian bawaan yang belum ada di JSON lama. Tanpa ini,
         *          bagian yang ditambahkan sesudah susunannya pernah tersimpan
         *          tidak akan pernah muncul di daftar — tidak bisa diurutkan,
         *          tidak bisa dimatikan.
         *
         * Dikerjakan dalam satu jalan, bukan dua cabang yang saling mendahului:
         * susunan yang sekaligus kelebihan DAN kekurangan bagian harus selesai
         * dalam sekali mount, bukan separuh sekarang separuh nanti.
         */
        $dikenal = array_column(self::BAGIAN_BAWAAN, 'id');
        $bersih  = array_values(array_filter(
            $this->home_sections,
            fn ($b) => in_array($b['id'] ?? null, $dikenal, true)
        ));

        $berubah = count($bersih) !== count($this->home_sections);
        $this->home_sections = $bersih;

        $adaSekarang = array_column($this->home_sections, 'id');

        foreach (self::BAGIAN_BAWAAN as $b) {
            if (in_array($b['id'], $adaSekarang, true)) {
                continue;
            }

            $b['order'] = count($this->home_sections) + 1;
            $this->home_sections[] = $b;
            $berubah = true;
        }

        $this->urutkanBagian();

        /*
         * Hanya ditulis kalau memang berubah — mount terjadi pada tiap muat
         * halaman, dan menulis ulang nilai yang sama setiap kali cuma
         * membebani basis data tanpa mengubah apa pun.
         */
        if ($berubah) {
            $this->simpanBagian();
        }

        $this->pindahkanFotoHeroLama($nilai);
    }

    /**
     * MEMINDAHKAN foto yang dulu tersimpan sebagai pengaturan lepas — sekali,
     * lalu kunci lamanya dibuang.
     *
     * Foto hero dan foto ajakan kini menempel pada bagiannya sendiri, bukan
     * pada kunci 'hero_image' / 'cta_image' yang berdiri terpisah.
     *
     * Kunci lamanya DULU sengaja dibiarkan, supaya alamat aslinya masih ada
     * kalau perpindahannya keliru. Itu ternyata membuat foto tidak bisa
     * dihapus sama sekali:
     *
     *   1. Bagiannya dikosongkan dari panel — kuncinya benar-benar dibuang.
     *   2. Beranda membaca $gambarBagian['hero'] ?? $settings['hero_image'],
     *      lalu jatuh ke kunci lama dan menggambar foto LAIN yang tidak
     *      pernah dihapus siapa pun.
     *   3. Pada mount berikutnya, pemindahan di bawah melihat bagiannya
     *      kosong dan kunci lamanya terisi — lalu MENYALINNYA KEMBALI.
     *
     * Jadi ada dua jalan yang masing-masing menghidupkan ulang foto yang sudah
     * dihapus, dan tidak satu pun meninggalkan jejak yang bisa dilihat dari
     * panel. Yang menyembuhkan keduanya cuma satu hal: sesudah dipindahkan,
     * kunci lamanya HARUS hilang. Tidak ada lagi yang menulisnya — dicek: tidak
     * satu pun berkas di app/ atau database/ menyetel ketiga kunci ini — jadi
     * begitu dibuang, ia tidak akan kembali.
     *
     * Kuncinya dibuang juga ketika bagiannya SUDAH punya foto sendiri: nilai
     * lama itu sudah tergantikan, dan membiarkannya cuma menyisakan jebakan
     * yang sama untuk penghapusan berikutnya.
     */
    private function pindahkanFotoHeroLama($nilai): void
    {
        $perlu = false;
        $bekas = [];

        foreach (['hero' => 'hero_image', 'contact' => 'cta_image'] as $bagian => $kunciLama) {
            $lama = $nilai[$kunciLama] ?? null;

            if (! filled($lama)) {
                continue;
            }

            $index = $this->cariBagian($bagian);

            /* Bagiannya belum ada di susunan — kunci lamanya JANGAN dibuang
               dulu, karena belum ada tempat untuk memindahkannya. */
            if ($index === null) {
                continue;
            }

            if (! filled($this->home_sections[$index]['image'] ?? null)) {
                $this->home_sections[$index]['image'] = $lama;
                $perlu = true;
            }

            $bekas[] = $kunciLama;
        }

        if ($perlu) {
            $this->simpanBagian();
        }

        if ($bekas !== []) {
            Setting::whereIn('key', $bekas)->delete();
        }

        $this->pindahkanFotoProfilLama($nilai);
        $this->pindahkanIsiAboutLama();
    }

    /**
     * Memindahkan foto Tentang Kami yang dulu tersimpan sebagai pengaturan lepas.
     *
     * Alasannya sama dengan foto hero dan foto ajakan: satu foto tidak boleh
     * punya dua tempat pengaturan yang bisa berbeda isinya. Kunci lamanya
     * sengaja TIDAK dihapus, supaya perpindahan ini masih bisa dikembalikan.
     */
    /**
     * Memindahkan isi halaman statis 'about-us' ke bagian Profil.
     *
     * Halaman itu memasok tiga hal di halaman Profile: label kecil di atas
     * judul, satu paragraf di kolom kanan, dan deskripsi meta SEO. Ia tidak
     * pernah tampil sebagai halaman tersendiri — kaki situs pun sengaja
     * mengecualikannya — jadi keberadaannya di tabel Daftar halaman menyesatkan:
     * ia tampak seperti halaman biasa yang bisa dihapus, padahal menghapusnya
     * ikut mengosongkan tiga tempat itu.
     *
     * Isinya karena itu dipindahkan sekali ke bagian Profil, tempat seluruh isi
     * halaman Profile yang lain sudah berada. Baris aslinya TIDAK ikut dihapus
     * di sini: memindahkan dan menghapus dalam satu langkah tidak menyisakan
     * cara memeriksa hasil pemindahannya lebih dulu.
     */
    private function pindahkanIsiAboutLama(): void
    {
        $sudah = $this->halaman_publik['profile']['isi'] ?? [];

        // Sudah pernah dipindahkan, atau isian itu sudah ditulis sendiri.
        foreach (['en', 'id'] as $bahasa) {
            if (filled($sudah[$bahasa]['body'] ?? null) || filled($sudah[$bahasa]['eyebrow'] ?? null)) {
                return;
            }
        }

        $halaman = Page::where('slug', 'about-us')->with('translations')->first();

        if (! $halaman) {
            return;
        }

        $adaYangDipindah = false;

        foreach ($halaman->translations as $terjemahan) {
            if (! in_array($terjemahan->locale, ['en', 'id'], true)) {
                continue;
            }

            foreach (['eyebrow' => $terjemahan->title, 'body' => $terjemahan->content] as $nama => $isi) {
                if (filled($isi)) {
                    $sudah[$terjemahan->locale][$nama] = $isi;
                    $adaYangDipindah = true;
                }
            }
        }

        if (! $adaYangDipindah) {
            return;
        }

        $this->halaman_publik['profile']['isi'] = $sudah;
        $this->simpanHalaman();
    }

    /** Sama seperti di atas, dan karena alasan yang sama kunci lamanya dibuang. */
    private function pindahkanFotoProfilLama($nilai): void
    {
        $lama = $nilai['about_image'] ?? null;

        if (! filled($lama)) {
            return;
        }

        if (! filled($this->halaman_publik['profile']['image'] ?? null)) {
            $this->halaman_publik['profile']['image'] = $lama;
            $this->simpanHalaman();
        }

        Setting::where('key', 'about_image')->delete();
    }

    /* ══════════════════════════════════════════════════════════════════════
       MODAL ISI BAGIAN
       ══════════════════════════════════════════════════════════════════════ */

    /**
     * Membuka isi satu bagian untuk disunting.
     *
     * Nilainya disalin ke $isiBagian, bukan disunting langsung di dalam
     * $home_sections. Kalau disunting di tempat, menekan Batal tidak
     * mengembalikan apa pun — perubahannya sudah terlanjur menempel di larik
     * yang juga dipakai daftar di belakang modalnya.
     */
    /* ══════════════════════════════════════════════════════════════════════
       SUSUNAN HALAMAN PROFILE
       ══════════════════════════════════════════════════════════════════════ */

    /**
     * Menyiapkan susunan bagian Profile.
     *
     * Bagian yang ditambahkan sesudah susunannya pernah tersimpan tidak ada di
     * dalam JSON lama, jadi ia tidak akan pernah muncul di daftar — tidak bisa
     * diurutkan, tidak bisa dimatikan. Yang hilang disisipkan di ujung, bukan
     * diam-diam dilewati.
     */
    private function muatSusunanProfil(): void
    {
        $tersimpan = $this->halaman_publik['profile']['sections'] ?? null;

        if (! is_array($tersimpan) || $tersimpan === []) {
            $this->profile_sections = self::PROFIL_BAWAAN;

            return;
        }

        $this->profile_sections = $tersimpan;

        $ada = array_column($this->profile_sections, 'id');

        foreach (self::PROFIL_BAWAAN as $b) {
            if (! in_array($b['id'], $ada, true)) {
                $b['order'] = count($this->profile_sections) + 1;
                $this->profile_sections[] = $b;
            }
        }

        usort($this->profile_sections, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
    }

    public function ubahIsiProfil(string $id): void
    {
        $this->bukaIsi('profil', $id);
    }

    public function toggleProfilActive(string $id): void
    {
        $index = $this->cariProfil($id);

        if ($index === null) {
            return;
        }

        $this->profile_sections[$index]['active'] = ! $this->profile_sections[$index]['active'];
        $this->simpanSusunanProfil();
    }

    public function moveProfilUp(string $id): void
    {
        $this->geserProfil($id, -1);
    }

    public function moveProfilDown(string $id): void
    {
        $this->geserProfil($id, 1);
    }

    /**
     * Menukar satu bagian Profile dengan tetangganya.
     *
     * Yang ditukar POSISINYA di dalam larik, bukan angka 'order'-nya —
     * alasannya sama dengan susunan beranda: menukar angkanya saja
     * meninggalkan lariknya dalam urutan lama sampai diurutkan ulang.
     */
    private function geserProfil(string $id, int $arah): void
    {
        $index  = $this->cariProfil($id);
        $tujuan = $index === null ? null : $index + $arah;

        if ($index === null || $tujuan < 0 || $tujuan > count($this->profile_sections) - 1) {
            return;
        }

        [$this->profile_sections[$index], $this->profile_sections[$tujuan]]
            = [$this->profile_sections[$tujuan], $this->profile_sections[$index]];

        $this->simpanSusunanProfil();
    }

    private function cariProfil(string $id): ?int
    {
        foreach ($this->profile_sections as $index => $bagian) {
            if ($bagian['id'] === $id) {
                return $index;
            }
        }

        return null;
    }

    private function simpanSusunanProfil(): void
    {
        foreach ($this->profile_sections as $index => &$bagian) {
            $bagian['order'] = $index + 1;
        }
        unset($bagian);

        $this->halaman_publik['profile']['sections'] = $this->profile_sections;

        $this->simpanHalaman();
    }
    public function ubahIsiBagian(string $id): void
    {
        $this->bukaIsi('bagian', $id);
    }

    /**
     * Membuka isi satu halaman publik.
     *
     * Modal yang sama dipakai untuk bagian beranda dan halaman: bentuk isinya
     * sama persis — teks per bahasa, kadang foto — dan dua modal kembar hanya
     * berarti dua tempat yang harus diperbaiki tiap kali ada perubahan.
     */
    public function ubahIsiHalaman(string $id): void
    {
        $this->bukaIsi('halaman', $id);
    }

    private function bukaIsi(string $jenis, string $id): void
    {
        $this->resetValidation();

        $bidang = match ($jenis) {
            'bagian' => self::BIDANG_BAGIAN[$id] ?? null,
            'profil' => self::BIDANG_PROFIL[$id] ?? null,
            default  => self::BIDANG_HALAMAN[$id] ?? null,
        };

        if ($bidang === null) {
            return;
        }

        if ($jenis === 'bagian' && $this->cariBagian($id) === null) {
            return;
        }

        $sumber    = $this->sumberIsi($jenis, $id);
        $tersimpan = $sumber['isi'] ?? [];

        $this->isiBagian = [];

        foreach (['en', 'id'] as $bahasa) {
            foreach (self::bidangTeks($bidang) as $b) {
                $this->isiBagian[$bahasa][$b['nama']] =
                    (string) ($tersimpan[$bahasa][$b['nama']] ?? '');
            }
        }

        $opsiTersimpan = $sumber['opsi'] ?? [];

        $this->opsiBagian = [];

        foreach ($this->skemaOpsi($jenis, $id) as $opsi) {
            $this->opsiBagian[$opsi['nama']] = ($opsi['sumber'] ?? null) === 'setting'
                ? (string) (Setting::where('key', $opsi['kunci'])->value('value') ?? $opsi['bawaan'])
                : (string) ($opsiTersimpan[$opsi['nama']] ?? $opsi['bawaan']);
        }

        $this->gambarBagian     = null;
        $this->gambarBagianLama = $sumber['image'] ?? null;
        $this->gambarTonggak    = [];
        $this->gambarTonggakLama = $sumber['milestone_images'] ?? [];
        $this->activeTab        = 'en';
        $this->jenisDibuka      = $jenis;
        $this->bagianDibuka     = $id;
    }

    /** Isi yang tersimpan untuk satu bagian atau halaman. */
    private function sumberIsi(string $jenis, string $id): array
    {
        if ($jenis === 'bagian') {
            $index = $this->cariBagian($id);

            return $index === null ? [] : $this->home_sections[$index];
        }

        /*
         * Seluruh bagian Profile berbagi satu kantong isi yang sama; yang
         * memisahkannya cuma daftar bidang tiap bagian. Fotonya pun satu untuk
         * halaman itu, dan hanya bagian 'profil' yang menawarkannya.
         */
        if ($jenis === 'profil') {
            return $this->halaman_publik['profile'] ?? [];
        }

        return $this->halaman_publik[$id] ?? [];
    }

    /**
     * Medan yang benar-benar teks per bahasa.
     *
     * Daftar medan boleh memuat slot ber-'jenis' => 'opsi' atau 'gambar' —
     * penanda tempat bagi isian bukan-teks yang ingin berdiri di tengah teks,
     * seperti tahun dan gambar tiap tonggak sejarah. Keduanya tidak punya isi
     * per bahasa, dan memperlakukannya sebagai teks akan membuat panel
     * menyediakan dua kotak untuk satu angka — atau dua unggahan untuk satu
     * gambar.
     *
     * @param  array<int, array<string, mixed>>  $bidang
     * @return array<int, array<string, mixed>>
     */
    public static function bidangTeks(array $bidang): array
    {
        return array_values(array_filter(
            $bidang,
            fn ($b) => ! in_array($b['jenis'] ?? null, ['opsi', 'gambar'], true)
        ));
    }

    private function skemaOpsi(string $jenis, string $id): array
    {
        return match ($jenis) {
            'bagian'  => self::OPSI_BAGIAN[$id] ?? [],
            'profil'  => self::opsiProfil()[$id] ?? [],
            'halaman' => self::opsiHalaman()[$id] ?? [],
            default   => [],
        };
    }

    public function tutupIsiBagian(): void
    {
        $this->resetValidation();
        $this->jenisDibuka      = 'bagian';
        $this->bagianDibuka     = null;
        $this->isiBagian        = [];
        $this->opsiBagian       = [];
        $this->gambarBagian     = null;
        $this->gambarBagianLama = null;
        $this->gambarTonggak    = [];
        $this->gambarTonggakLama = [];
    }

    public function simpanIsiBagian(): void
    {
        if ($this->bagianDibuka === null) {
            return;
        }

        /*
         * Batas angkanya diambil dari skema, bukan ditulis ulang di sini.
         * Kalau batasnya ditulis dua kali, kotak isian dan pemeriksaannya bisa
         * lama-lama berbeda — dan yang kalah selalu pemakainya.
         */
        $aturan = [
            'gambarBagian'    => 'nullable|image|max:4096',
            'gambarTonggak.*' => 'nullable|image|max:4096',
        ];
        $sebutan = [
            'gambarBagian'    => 'foto bagian',
            'gambarTonggak.*' => 'gambar tonggak',
        ];

        foreach ($this->skemaOpsi($this->jenisDibuka, $this->bagianDibuka) as $opsi) {
            if ($opsi['jenis'] === 'angka') {
                /*
                 * Boleh kosong kalau skemanya bilang begitu — dan yang bersumber
                 * pengaturan selalu begitu: bawaannya memang untai kosong, dan
                 * pembacanya sudah punya cadangan.
                 *
                 * 'boleh_kosong' ditulis terpisah dari 'sumber' karena keduanya
                 * menjawab pertanyaan yang berbeda: yang satu di mana nilainya
                 * disimpan, yang satu lagi apakah ia wajib diisi. Tahun tonggak
                 * disimpan di dalam isi halaman, tapi tetap boleh dikosongkan.
                 */
                $bolehKosong = ($opsi['sumber'] ?? null) === 'setting'
                    || ! empty($opsi['boleh_kosong']);

                $wajib = $bolehKosong ? 'nullable' : 'required';

                $aturan['opsiBagian.' . $opsi['nama']] =
                    $wajib . '|integer|min:' . $opsi['min'] . '|max:' . $opsi['max'];
                $sebutan['opsiBagian.' . $opsi['nama']] = mb_strtolower($opsi['label']);
            }

            /*
             * Opsi bertipe teks selalu boleh kosong: mengosongkannya berarti
             * bagian yang memakainya tidak digambar sama sekali, dan itu
             * jawaban yang sah.
             */
            if ($opsi['jenis'] === 'teks') {
                $aturan['opsiBagian.' . $opsi['nama']] = 'nullable|string|max:255';
                $sebutan['opsiBagian.' . $opsi['nama']] = mb_strtolower($opsi['label']);
            }
        }

        $this->validate($aturan, [], $sebutan);

        if ($this->jenisDibuka === 'bagian' && $this->cariBagian($this->bagianDibuka) === null) {
            $this->tutupIsiBagian();

            return;
        }

        /*
         * Isian kosong DIBUANG, bukan disimpan sebagai untai kosong.
         *
         * Bedanya terasa saat membaca: kunci yang tidak ada berarti "pakai
         * bawaan", sedangkan untai kosong yang tersimpan tidak bisa dibedakan
         * dari "sengaja dikosongkan". Membuangnya juga menjaga JSON-nya tetap
         * ramping — ia menumpang di satu baris pengaturan.
         */
        $bersih = [];

        foreach ($this->isiBagian as $bahasa => $nilai) {
            foreach ($nilai as $nama => $isi) {
                $isi = is_string($isi) ? trim($isi) : $isi;

                // Penyunting teks kaya yang kosong tetap menghasilkan <p><br></p>.
                if ($isi === '' || $isi === '<p><br></p>') {
                    continue;
                }

                $bersih[$bahasa][$nama] = $isi;
            }
        }

        /*
         * Kelima bagian halaman Profile BERBAGI satu kantong isi yang sama.
         *
         * Karena itu isinya digabung, bukan ditimpa: $bersih hanya memuat
         * bidang milik bagian yang sedang dibuka, dan menulisnya apa adanya
         * akan menghapus isi keempat bagian lainnya — tersimpan rapi, lalu
         * lenyap begitu bagian berikutnya disimpan.
         *
         * Bidang milik bagian ini dibuang lebih dulu dari kantongnya supaya
         * isian yang sengaja DIKOSONGKAN benar-benar hilang, bukan tertinggal
         * karena penggabungan.
         */
        if ($this->jenisDibuka === 'profil') {
            $milikBagian = array_column(
                self::bidangTeks(self::BIDANG_PROFIL[$this->bagianDibuka] ?? []),
                'nama'
            );
            $kantong     = $this->halaman_publik['profile']['isi'] ?? [];

            foreach ($kantong as $bahasa => $nilai) {
                $kantong[$bahasa] = array_diff_key($nilai, array_flip($milikBagian));
            }

            foreach ($bersih as $bahasa => $nilai) {
                $kantong[$bahasa] = array_merge($kantong[$bahasa] ?? [], $nilai);
            }

            // Bahasa yang jadi kosong seluruhnya tidak perlu ikut tersimpan.
            $bersih = array_filter($kantong, fn ($n) => $n !== []);
        }

        $this->tulisIsi('isi', $bersih);

        /*
         * Opsi yang nilainya sama dengan bawaannya tidak ikut disimpan, dengan
         * alasan yang sama seperti teks kosong: yang tidak tercatat berarti
         * "ikut bawaan", dan bawaannya boleh berubah tanpa perlu menyunting
         * ulang tiap bagian satu-satu.
         */
        $opsi = [];

        foreach ($this->skemaOpsi($this->jenisDibuka, $this->bagianDibuka) as $skema) {
            $nilai = $this->opsiBagian[$skema['nama']] ?? null;

            /*
             * Yang bersumber pengaturan ditulis ke kunci pengaturannya sendiri,
             * bukan ke JSON isi halaman — termasuk saat dikosongkan, supaya
             * mengosongkannya benar-benar berpengaruh.
             */
            if (($skema['sumber'] ?? null) === 'setting') {
                Setting::updateOrCreate(['key' => $skema['kunci']], ['value' => $nilai]);

                continue;
            }

            if ($nilai === null || (string) $nilai === (string) $skema['bawaan']) {
                continue;
            }

            $opsi[$skema['nama']] = $skema['jenis'] === 'angka' ? (int) $nilai : trim((string) $nilai);
        }

        /*
         * Opsi digabung untuk Profile, dengan alasan yang sama seperti teksnya:
         * kelima bagiannya berbagi SATU kantong.
         *
         * Menulisnya apa adanya berarti menyimpan bagian Nilai — yang tidak
         * punya opsi sama sekali — akan menghapus tahun tonggak milik bagian
         * Sejarah. Tersimpan rapi, lalu lenyap begitu bagian lain disimpan,
         * tanpa satu pun tanda di layar.
         *
         * Yang dibuang lebih dulu cuma opsi milik bagian yang sedang dibuka,
         * supaya angka yang sengaja DIKOSONGKAN benar-benar hilang.
         */
        if ($this->jenisDibuka === 'profil') {
            $milikBagian = array_column($this->skemaOpsi('profil', $this->bagianDibuka), 'nama');
            $kantong     = $this->halaman_publik['profile']['opsi'] ?? [];

            $opsi = array_merge(
                array_diff_key($kantong, array_flip($milikBagian)),
                $opsi
            );
        }

        $this->tulisIsi('opsi', $opsi === [] ? null : $opsi);

        if ($this->gambarBagian) {
            $this->tulisIsi('image', $this->gambarBagian->store('settings', 'public'));
        }

        /*
         * Gambar tonggak DIGABUNG dengan yang sudah tersimpan, bukan ditimpa:
         * yang diunggah kali ini hanya satu atau dua, dan menulisnya apa adanya
         * akan menghapus gambar tonggak lain yang tidak disentuh.
         */
        $tonggak = $this->gambarTonggakLama;

        foreach ($this->gambarTonggak as $nama => $berkas) {
            if ($berkas) {
                $tonggak[$nama] = $berkas->store('settings', 'public');
            }
        }

        $this->tulisIsi('milestone_images', $tonggak === [] ? null : $tonggak);

        $this->simpanSumber();

        session()->flash('message', match ($this->jenisDibuka) {
            'bagian' => 'Isi bagian beranda tersimpan.',
            'profil' => 'Isi bagian halaman Profile tersimpan.',
            default  => 'Isi halaman tersimpan.',
        });
        $this->tutupIsiBagian();
    }

    /**
     * Membuang foto satu bagian.
     *
     * Berkasnya di disk TIDAK ikut dihapus. Alamat yang sama bisa saja masih
     * dirujuk dari tempat lain, dan berkas yatim jauh lebih murah daripada
     * gambar yang mendadak hilang di halaman yang sedang tayang.
     */
    public function hapusGambarBagian(): void
    {
        if ($this->bagianDibuka === null) {
            return;
        }

        if ($this->jenisDibuka === 'bagian' && $this->cariBagian($this->bagianDibuka) === null) {
            return;
        }

        $this->tulisIsi('image', null);

        $this->gambarBagianLama = null;
        $this->gambarBagian     = null;

        $this->simpanSumber();
    }

    /**
     * Membuang gambar satu tonggak.
     *
     * Berkasnya di disk TIDAK ikut dihapus, dengan alasan yang sama seperti
     * foto bagian: alamat yang sama bisa masih dirujuk dari tempat lain, dan
     * berkas yatim jauh lebih murah daripada gambar yang mendadak hilang di
     * halaman yang sedang tayang.
     */
    public function hapusGambarTonggak(string $nama): void
    {
        if ($this->bagianDibuka === null) {
            return;
        }

        unset($this->gambarTonggakLama[$nama], $this->gambarTonggak[$nama]);

        $this->tulisIsi('milestone_images', $this->gambarTonggakLama === [] ? null : $this->gambarTonggakLama);

        $this->simpanSumber();
    }

    /**
     * Menulis satu kunci ke tempat penyimpanan yang sedang dibuka.
     *
     * Nilai null berarti kuncinya DIBUANG, bukan disimpan sebagai null: yang
     * tidak tercatat berarti "ikut bawaan", dan null yang tersimpan tidak bisa
     * dibedakan dari itu saat dibaca kembali.
     */
    private function tulisIsi(string $kunci, $nilai): void
    {
        if ($this->jenisDibuka === 'bagian') {
            $index = $this->cariBagian($this->bagianDibuka);

            if ($index === null) {
                return;
            }

            if ($nilai === null) {
                unset($this->home_sections[$index][$kunci]);
            } else {
                $this->home_sections[$index][$kunci] = $nilai;
            }

            return;
        }

        $halaman = $this->jenisDibuka === 'profil' ? 'profile' : $this->bagianDibuka;

        if ($nilai === null) {
            unset($this->halaman_publik[$halaman][$kunci]);
        } else {
            $this->halaman_publik[$halaman][$kunci] = $nilai;
        }
    }

    /** Menyimpan tempat penyimpanan yang sedang dibuka ke pengaturan. */
    private function simpanSumber(): void
    {
        if ($this->jenisDibuka === 'bagian') {
            $this->simpanBagian();

            return;
        }

        /* Bagian Profile menumpang di kantong isi halaman 'profile', jadi
           susunannya ikut ditulis ulang supaya tidak tertimpa. */
        if ($this->jenisDibuka === 'profil') {
            $this->halaman_publik['profile']['sections'] = $this->profile_sections;
        }

        $this->simpanHalaman();
    }

    private function simpanHalaman(): void
    {
        /* Halaman yang seluruh isinya kosong dibuang dari JSON-nya, supaya
           kunci pengaturan ini tidak lama-lama penuh entri hampa. */
        $bersih = array_filter(
            $this->halaman_publik,
            fn ($h) => array_filter($h, fn ($v) => filled($v)) !== []
        );

        $this->halaman_publik = $bersih;

        Setting::updateOrCreate(
            ['key' => \App\Support\IsiHalaman::KUNCI],
            ['value' => json_encode((object) $bersih)]
        );

        /* Situs publik mengingat isinya sekali per permintaan; tanpa ini,
           penggambaran berikutnya dalam permintaan yang sama masih memakai
           isi yang lama. */
        \App\Support\IsiHalaman::lupakan();
    }

    /**
     * Menyalakan atau mematikan satu bagian beranda.
     *
     * Dipanggil lewat wire:click, bukan wire:model: nilainya hidup di dalam
     * larik JSON, jadi tidak ada satu properti pun yang bisa diikat langsung.
     */
    /**
     * Baris mana yang daftar bagiannya sedang terbentang: 'home', 'profile',
     * atau kosong.
     *
     * Dibentangkan DI TEMPAT, bukan dibuka sebagai jendela. Menyunting satu
     * bagian sudah memakai jendela sendiri, dan jendela di atas jendela
     * memaksa orang menutup dua kali untuk kembali ke daftarnya.
     */
    public string $susunanDibuka = '';

    public function bukaSusunan(string $id): void
    {
        $this->susunanDibuka = $this->susunanDibuka === $id ? '' : $id;
    }

    public function toggleSectionActive(string $id): void
    {
        $index = $this->cariBagian($id);

        if ($index === null) {
            return;
        }

        $this->home_sections[$index]['active'] = ! $this->home_sections[$index]['active'];
        $this->simpanBagian();
    }

    public function moveSectionUp(string $id): void
    {
        $this->geserBagian($id, -1);
    }

    public function moveSectionDown(string $id): void
    {
        $this->geserBagian($id, 1);
    }

    /**
     * Menukar satu bagian dengan tetangganya.
     *
     * Yang ditukar POSISINYA di dalam larik, bukan angka 'order'-nya. Menukar
     * angkanya saja meninggalkan lariknya dalam urutan lama sampai diurutkan
     * ulang, dan itu pernah membuat tombol naik/turun tampak melompati satu
     * baris. simpanBagian() yang menomori ulang sesudahnya.
     */
    private function geserBagian(string $id, int $arah): void
    {
        $index = $this->cariBagian($id);
        $tujuan = $index === null ? null : $index + $arah;

        if ($index === null || $tujuan < 0 || $tujuan > count($this->home_sections) - 1) {
            return;
        }

        [$this->home_sections[$index], $this->home_sections[$tujuan]]
            = [$this->home_sections[$tujuan], $this->home_sections[$index]];

        $this->simpanBagian();
    }

    private function cariBagian(string $id): ?int
    {
        foreach ($this->home_sections as $index => $bagian) {
            if ($bagian['id'] === $id) {
                return $index;
            }
        }

        return null;
    }

    private function urutkanBagian(): void
    {
        usort($this->home_sections, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
    }

    /**
     * Menyimpan susunannya sendiri, tanpa menunggu tombol Simpan mana pun.
     *
     * Nomornya dirapikan jadi 1,2,3… lebih dulu supaya di dalam JSON tidak
     * tertinggal angka berlubang atau kembar sesudah beberapa kali digeser.
     */
    private function simpanBagian(): void
    {
        foreach ($this->home_sections as $index => &$bagian) {
            $bagian['order'] = $index + 1;
        }
        unset($bagian);

        Setting::updateOrCreate(
            ['key' => 'home_sections'],
            ['value' => json_encode($this->home_sections)]
        );
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        /*
         * Halaman statis ikut masuk ke kartu Halaman publik, berjajar dengan
         * ketujuh halaman tetap.
         *
         * Tidak lagi berupa tabel berpaginasi dengan pencarian dan penyaring:
         * isinya cuma segelintir baris, dan tiga kendali untuk menyaring satu
         * halaman lebih banyak menyita perhatian daripada menolong. Diambil
         * seluruhnya, urut menurut alamatnya supaya letaknya tidak berpindah
         * tiap kali salah satunya disunting.
         */
        $pages = Page::with('translations')->orderBy('slug')->get();

        return view('livewire.admin.page-index', [
            'pages' => $pages,
        ]);
    }

    public function create()
    {
        $this->resetValidation();
        $this->galatTerjemah = null;
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function autoTranslate(): void
    {
        $this->galatTerjemah = null;

        if (empty(trim((string) $this->title_id)) && empty(trim((string) $this->content_id))) {
            $this->galatTerjemah = 'Isi dulu judul atau isi halamannya dalam Bahasa Indonesia.';
            return;
        }

        $this->isTranslating = true;

        $layanan = app(TranslationService::class);

        /* Label ikut dikirim HANYA kalau diisi. Untai kosong yang dikirim ke
           layanan terjemahan menghabiskan satu putaran untuk mengembalikan
           kosong juga. */
        $bahan = [
            'title'   => (string) $this->title_id,
            'content' => (string) $this->content_id,
        ];

        if (filled($this->label_id)) {
            $bahan['label'] = (string) $this->label_id;
        }

        $translated = $layanan->translateMany($bahan);

        if (!empty($translated['label']))   $this->label_en   = $translated['label'];
        if (!empty($translated['title']))   $this->title_en   = $translated['title'];
        if (!empty($translated['content'])) {
            $this->content_en = $translated['content'];
        }

        if ($layanan->sebabGagal) {
            $this->galatTerjemah = $layanan->sebabGagal;
        }

        $this->isTranslating = false;
    }

    public function autoTranslateSection(): void
    {
        $this->galatTerjemah = null;

        if (empty($this->isiBagian['id'])) {
            $this->galatTerjemah = 'Isi dulu bagiannya dalam Bahasa Indonesia.';
            return;
        }

        $this->isTranslating = true;
        
        $textsToTranslate = [];
        $keysMap = [];
        
        foreach ($this->isiBagian['id'] as $key => $content) {
            $strContent = is_string($content) ? trim($content) : $content;
            if (filled($strContent) && $strContent !== '<p><br></p>') {
                $textsToTranslate[$key] = (string)$content;
                $keysMap[] = $key;
            }
        }
        
        if (!empty($textsToTranslate)) {
            $layanan    = app(TranslationService::class);
            $translated = $layanan->translateMany($textsToTranslate);

            foreach ($keysMap as $key) {
                if (!empty($translated[$key])) {
                    $this->isiBagian['en'][$key] = $translated[$key];
                }
            }

            if ($layanan->sebabGagal) {
                $this->galatTerjemah = $layanan->sebabGagal;
            }
        }
        
        $this->isTranslating = false;
    }

    public function store()
    {
        $this->validate([
            'label_en' => 'nullable|string|max:60',
            'label_id' => 'nullable|string|max:60',
            'title_en' => 'required|string|max:255',
            'title_id' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        $slug = Str::slug($this->title_en);

        $page = Page::updateOrCreate(['id' => $this->page_id], [
            'slug' => $slug,
            'status' => $this->status,
        ]);

        /* Label kosong disimpan sebagai NULL, bukan untai kosong: halaman
           publik memutuskan menggambar labelnya atau tidak dari isian ini, dan
           '' yang tersimpan tetap "ada" bagi filled(). */
        // Save English translation
        PageTranslation::updateOrCreate(
            ['page_id' => $page->id, 'locale' => 'en'],
            [
                'label'   => filled($this->label_en) ? trim($this->label_en) : null,
                'title'   => $this->title_en,
                'content' => $this->content_en,
            ]
        );

        // Save Indonesian translation
        PageTranslation::updateOrCreate(
            ['page_id' => $page->id, 'locale' => 'id'],
            [
                'label'   => filled($this->label_id) ? trim($this->label_id) : null,
                'title'   => $this->title_id,
                'content' => $this->content_id,
            ]
        );

        session()->flash('message', 
            $this->page_id ? 'Page Updated Successfully.' : 'Page Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->galatTerjemah = null;

        $page = Page::with('translations')->findOrFail($id);
        $this->page_id = $id;
        $this->slug = $page->slug;
        $this->status = $page->status;

        $this->label_en = $page->getTranslation('label', 'en');
        $this->title_en = $page->getTranslation('title', 'en');
        $this->content_en = $page->getTranslation('content', 'en');
        
        $this->label_id = $page->getTranslation('label', 'id');
        $this->title_id = $page->getTranslation('title', 'id');
        $this->content_id = $page->getTranslation('content', 'id');
        $this->activeTab = 'en';

        $this->isOpen = true;
    }

    public function delete($id)
    {
        Page::find($id)->delete();
        session()->flash('message', 'Page Deleted Successfully.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->page_id = null;
        $this->slug = '';
        $this->status = 'draft';
        $this->label_id = '';
        $this->title_id = '';
        $this->content_id = '';
        $this->label_en = '';
        $this->title_en = '';
        $this->content_en = '';
        $this->activeTab = 'en';
    }
}
