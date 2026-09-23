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
    use WithFileUploads;

    // Form fields
    public $isOpen = false;
    public $page_id;
    public $slug;
    public $status = 'draft';
    
    // Translation fields (id and en)
    public $label_id, $title_id, $content_id;
    public $label_en, $title_en, $content_en;
    public bool $isTranslating = false;

    public ?string $galatTerjemah = null;

    // Tab bahasa aktif di modal
    public string $activeTab = 'en';

    // Susunan bagian beranda
    public array $home_sections = [];

    // Daftar bawaan bagian beranda
    private const BAGIAN_BAWAAN = [
        ['id' => 'hero',           'name' => 'Hero Slider',    'active' => true, 'order' => 1],
        ['id' => 'about',          'name' => 'About Us',       'active' => true, 'order' => 2],
        ['id' => 'products',       'name' => 'Our Products',   'active' => true, 'order' => 3],
        ['id' => 'export-markets', 'name' => 'Export Markets', 'active' => true, 'order' => 4],
        ['id' => 'certifications', 'name' => 'Certifications', 'active' => true, 'order' => 5],
        ['id' => 'news',           'name' => 'Latest News',    'active' => true, 'order' => 6],
        ['id' => 'contact',        'name' => 'Contact Us',     'active' => true, 'order' => 7],
    ];

    // ── ISI TIAP BAGIAN BERANDA ──────────────────────────────────────────

    public const CATATAN_TEKANAN =
        'Kata yang diapit bintang — *seperti ini* — digambar dengan huruf serif miring, sebagai penekanan di dalam judul.';

    public const BIDANG_BAGIAN = [
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

    // Pengaturan non-teks tiap bagian beranda
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

    // ── HALAMAN PUBLIK ───────────────────────────────────────────────────

    // Daftar semua halaman publik
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
        // Footer menempel di semua halaman, tidak punya rute sendiri
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

    // ── HALAMAN PROFILE ──────────────────────────────────────────────────

    // Susunan bawaan bagian halaman Profile
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

            // Garis waktu history & tonggak
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

    // Pengaturan non-teks halaman publik
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

    // Pengaturan non-teks bagian Profile
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
                               . 'berjalan. Dikosongkan berarti garis waktunya memakai tahun berjalan.',
                ],
                ...self::tahunTonggak(),
            ],
        ];
    }

    // Bangkitkan keenam kolom tahun tonggak
    private static function tahunTonggak(): array
    {
        $keluar = [];

        for ($i = 1; $i <= 6; $i++) {
            $keluar[] = [
                'nama'         => 'milestone_' . $i . '_year',
                'label'        => 'Tonggak ' . $i . ' — tahun',
                'jenis'        => 'angka',
                'min'          => 1900,
                'max'          => (int) date('Y') + 20,
                'bawaan'       => '',
                'boleh_kosong' => true,
                'catatan'      => $i === 1
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

    // Halaman yang punya foto sendiri
    public const HALAMAN_BERFOTO = [];

    // Bagian yang punya foto sendiri
    public const BAGIAN_BERFOTO = ['hero', 'contact'];

    // Keterangan foto tiap bagian
    public const CATATAN_FOTO = [
        'hero'    => 'Melebar penuh di bawah teks hero, sebaiknya 1920×1080 piksel. '
                   . 'Dikosongkan berarti tempatnya digambar sebagai kotak penanda.',
        'contact' => 'Latar kartu ajakan di bawah beranda, ditumpuk lapisan hijau gelap '
                   . 'supaya teksnya tetap terbaca. Dikosongkan berarti latarnya hijau polos.',

        'profil'  => 'Foto di sisi kanan bagian profil, tegak atau persegi. '
                   . 'Dikosongkan berarti tempatnya digambar sebagai kotak penanda.',
    ];

    // Isi semua halaman publik
    public array $halaman_publik = [];

    // Susunan bagian halaman Profile
    public array $profile_sections = [];

    // 'bagian' beranda, 'halaman' publik, atau 'profil' bagian Profile
    public string $jenisDibuka = 'bagian';

    // Bagian/halaman yang sedang dibuka; null = tertutup
    public ?string $bagianDibuka = null;

    // isiBagian[locale][nama] — nilai di modal
    public array $isiBagian = [];

    // opsiBagian[nama] — pengaturan non-teks di modal
    public array $opsiBagian = [];

    public $gambarBagian;
    public ?string $gambarBagianLama = null;

    // Gambar per tonggak sejarah
    public array $gambarTonggak = [];
    public array $gambarTonggakLama = [];

    public function mount(): void
    {
        $nilai = Setting::pluck('value', 'key');

        $isiHalaman = json_decode($nilai[\App\Support\IsiHalaman::KUNCI] ?? '', true);
        $this->halaman_publik = is_array($isiHalaman) ? $isiHalaman : [];

        $this->muatSusunanProfil();

        $tersimpan = Setting::where('key', 'home_sections')->value('value');
        $bagian = $tersimpan ? json_decode($tersimpan, true) : null;

        if (! is_array($bagian) || $bagian === []) {
            $this->home_sections = self::BAGIAN_BAWAAN;
            $this->simpanBagian();
            $this->pindahkanFotoHeroLama($nilai);

            return;
        }

        $this->home_sections = $bagian;

        // Damaikan: buang yang tidak dikenal, sisipkan yang baru
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

        if ($berubah) {
            $this->simpanBagian();
        }

        $this->pindahkanFotoHeroLama($nilai);
    }

    // Migrasi satu kali: foto hero/ajakan lama → bagian JSON, hapus kunci lama
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

    // Migrasi satu kali: isi halaman about-us → bagian Profil
    private function pindahkanIsiAboutLama(): void
    {
        $sudah = $this->halaman_publik['profile']['isi'] ?? [];

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

    // Migrasi satu kali: foto profil lama → bagian Profile, hapus kunci lama
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

    // ── MODAL ISI BAGIAN ─────────────────────────────────────────────────

    // ── SUSUNAN HALAMAN PROFILE ──────────────────────────────────────────

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

    private function sumberIsi(string $jenis, string $id): array
    {
        if ($jenis === 'bagian') {
            $index = $this->cariBagian($id);

            return $index === null ? [] : $this->home_sections[$index];
        }

        if ($jenis === 'profil') {
            return $this->halaman_publik['profile'] ?? [];
        }

        return $this->halaman_publik[$id] ?? [];
    }

    // Saring hanya bidang bertipe teks (bukan 'opsi' atau 'gambar')
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
                $bolehKosong = ($opsi['sumber'] ?? null) === 'setting'
                    || ! empty($opsi['boleh_kosong']);

                $wajib = $bolehKosong ? 'nullable' : 'required';

                $aturan['opsiBagian.' . $opsi['nama']] =
                    $wajib . '|integer|min:' . $opsi['min'] . '|max:' . $opsi['max'];
                $sebutan['opsiBagian.' . $opsi['nama']] = mb_strtolower($opsi['label']);
            }

            // Opsi teks selalu nullable
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

        // Saring isian kosong

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

        // Bagian Profile berbagi satu kantong — gabung, jangan timpa
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

            $bersih = array_filter($kantong, fn ($n) => $n !== []);
        }

        $this->tulisIsi('isi', $bersih);

        // Opsi yang sama dengan bawaan tidak perlu disimpan
        $opsi = [];

        foreach ($this->skemaOpsi($this->jenisDibuka, $this->bagianDibuka) as $skema) {
            $nilai = $this->opsiBagian[$skema['nama']] ?? null;

            // Opsi dengan sumber=setting disimpan ke tabel settings
            if (($skema['sumber'] ?? null) === 'setting') {
                Setting::updateOrCreate(['key' => $skema['kunci']], ['value' => $nilai]);

                continue;
            }

            if ($nilai === null || (string) $nilai === (string) $skema['bawaan']) {
                continue;
            }

            $opsi[$skema['nama']] = $skema['jenis'] === 'angka' ? (int) $nilai : trim((string) $nilai);
        }

        // Opsi Profile digabung juga (satu kantong untuk semua bagian)
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

        // Gambar tonggak digabung, bukan ditimpa
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

    // Hapus foto bagian (berkas di disk tidak ikut dihapus)
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

    // Hapus gambar tonggak (berkas di disk tidak ikut dihapus)
    public function hapusGambarTonggak(string $nama): void
    {
        if ($this->bagianDibuka === null) {
            return;
        }

        unset($this->gambarTonggakLama[$nama], $this->gambarTonggak[$nama]);

        $this->tulisIsi('milestone_images', $this->gambarTonggakLama === [] ? null : $this->gambarTonggakLama);

        $this->simpanSumber();
    }

    // Tulis satu kunci ke tempat penyimpanan aktif; null = hapus kunci
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

    private function simpanSumber(): void
    {
        if ($this->jenisDibuka === 'bagian') {
            $this->simpanBagian();

            return;
        }

        // Bagian Profile menumpang di kantong 'profile', sertakan susunannya
        if ($this->jenisDibuka === 'profil') {
            $this->halaman_publik['profile']['sections'] = $this->profile_sections;
        }

        $this->simpanHalaman();
    }

    private function simpanHalaman(): void
    {
        // Buang halaman yang seluruh isinya kosong
        $bersih = array_filter(
            $this->halaman_publik,
            fn ($h) => array_filter($h, fn ($v) => filled($v)) !== []
        );

        $this->halaman_publik = $bersih;

        Setting::updateOrCreate(
            ['key' => \App\Support\IsiHalaman::KUNCI],
            ['value' => json_encode((object) $bersih)]
        );

        // Kosongkan cache IsiHalaman agar halaman publik langsung memuat yang baru
        \App\Support\IsiHalaman::lupakan();
    }

    // Baris daftar bagian yang sedang terbentang: 'home', 'profile', atau ''
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

    #[Layout('components.layouts.app')]
    public function render()
    {
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
