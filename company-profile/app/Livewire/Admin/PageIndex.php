<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use Illuminate\Support\Str;

class PageIndex extends Component
{
    use WithPagination;

    public $search = '';

    /*
     * Penyaring status. Namanya selectedStatus, bukan status, karena $status
     * di bawah sudah dipakai sebagai isian modalnya — satu properti tidak bisa
     * merangkap dua peran: menyunting halaman bakal ikut menyaring tabelnya.
     */
    public string $selectedStatus = '';

    // Form fields
    public $isOpen = false;
    public $page_id;
    public $slug;
    public $status = 'draft';
    
    // Translation fields (id and en)
    public $title_id, $content_id;
    public $title_en, $content_en;

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
    private const BAGIAN_BAWAAN = [
        ['id' => 'hero',           'name' => 'Hero Slider',          'active' => true, 'order' => 1],
        ['id' => 'about',          'name' => 'About Us',             'active' => true, 'order' => 2],
        ['id' => 'products',       'name' => 'Our Products',         'active' => true, 'order' => 3],
        ['id' => 'export-markets', 'name' => 'Export Markets',       'active' => true, 'order' => 4],
        ['id' => 'certifications', 'name' => 'Certifications',       'active' => true, 'order' => 5],
        ['id' => 'gallery',        'name' => 'Gallery',              'active' => true, 'order' => 6],
        ['id' => 'downloads',      'name' => 'Catalogs & Downloads', 'active' => true, 'order' => 7],
        ['id' => 'news',           'name' => 'Latest News',          'active' => true, 'order' => 8],
        ['id' => 'contact',        'name' => 'Contact Us',           'active' => true, 'order' => 9],
    ];

    public function mount(): void
    {
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

            return;
        }

        $this->home_sections = $bagian;

        /*
         * Bagian yang ditambahkan sesudah susunannya pernah tersimpan tidak
         * ada di dalam JSON lama, jadi ia tidak akan pernah muncul di daftar
         * ini — tidak bisa diurutkan, tidak bisa dimatikan. Yang hilang
         * disisipkan di ujung, bukan diam-diam dilewati.
         */
        $adaSekarang = array_column($this->home_sections, 'id');
        $tertinggal = array_filter(
            self::BAGIAN_BAWAAN,
            fn ($b) => ! in_array($b['id'], $adaSekarang, true)
        );

        if ($tertinggal !== []) {
            foreach ($tertinggal as $b) {
                $b['order'] = count($this->home_sections) + 1;
                $this->home_sections[] = $b;
            }

            $this->simpanBagian();

            return;
        }

        $this->urutkanBagian();
    }

    /**
     * Menyalakan atau mematikan satu bagian beranda.
     *
     * Dipanggil lewat wire:click, bukan wire:model: nilainya hidup di dalam
     * larik JSON, jadi tidak ada satu properti pun yang bisa diikat langsung.
     */
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
         * Pencariannya menjangkau judul terjemahannya, bukan cuma slug.
         *
         * Slug dirangkai dari judul Inggris, jadi mencari "Kebijakan" — judul
         * Indonesia halaman privacy-policy — dulu tidak menemukan apa pun.
         * Yang diingat pemakai judulnya, bukan alamatnya.
         *
         * Syaratnya dikurung: when() tidak membungkus isinya dalam tanda
         * kurung, jadi tanpa $b ini penyaring status di bawah cuma berlaku
         * untuk cabang terakhirnya.
         */
        $pages = Page::with('translations')
            ->when($this->search, function ($q) {
                $q->where(function ($b) {
                    $b->where('slug', 'like', '%' . $this->search . '%')
                      ->orWhereHas('translations', function ($t) {
                          $t->where('title', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->selectedStatus, function ($q) {
                $q->where('status', $this->selectedStatus);
            })
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.admin.page-index', [
            'pages' => $pages
        ]);
    }

    /**
     * Kembali ke halaman satu tiap kali penyaringnya diubah.
     *
     * Tanpa ini, menyaring saat sedang berada di halaman jauh meninggalkan
     * nomor halamannya apa adanya — dan halaman 20 dari hasil yang cuma 3
     * halaman menggambar tabel kosong beserta kalimat "tidak ada yang cocok",
     * padahal hasilnya ada, cuma tidak di halaman itu.
     */
    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'selectedStatus'], true)) {
            $this->resetPage();
        }
    }

    public function create()
    {
        $this->resetValidation();
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
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
            ['title' => $this->title_en, 'content' => $this->content_en]
        );

        // Save Indonesian translation
        PageTranslation::updateOrCreate(
            ['page_id' => $page->id, 'locale' => 'id'],
            ['title' => $this->title_id, 'content' => $this->content_id]
        );

        session()->flash('message', 
            $this->page_id ? 'Page Updated Successfully.' : 'Page Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetValidation();

        $page = Page::with('translations')->findOrFail($id);
        $this->page_id = $id;
        $this->slug = $page->slug;
        $this->status = $page->status;

        $this->title_en = $page->getTranslation('title', 'en');
        $this->content_en = $page->getTranslation('content', 'en');
        
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
        $this->title_id = '';
        $this->content_id = '';
        $this->title_en = '';
        $this->content_en = '';
        $this->activeTab = 'en';
    }
}
