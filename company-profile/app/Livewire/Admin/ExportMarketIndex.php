<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\ExportMarket;
use App\Services\TranslationService;

class ExportMarketIndex extends Component
{
    use WithPagination;

    public string $search = '';

    /*
     * Penyaring. Namanya diawali "selected" supaya tidak bentrok dengan isian
     * modal di bawah — $region sudah dipakai untuk menyunting satu negara, dan
     * satu properti tidak bisa merangkap dua peran.
     *
     * Nilai statusnya 'active'/'inactive', bukan '1'/'0': keduanya untai yang
     * benar di mata PHP, jadi when() di render() tidak perlu penjagaan khusus
     * seperti penyaring unggulan di halaman Produk.
     */
    public string $selectedStatus = '';
    public string $selectedRegion = '';

    public bool $showModal = false;
    public ?string $editingId = null;

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
        if (in_array($property, ['search', 'selectedStatus', 'selectedRegion'], true)) {
            $this->resetPage();
        }
    }

    // Form Fields (PRD Bab 9.1)
    public string $country_code = '';
    public string $region = 'Asia';
    public string $name_en = '';
    public string $name_id = '';
    public string $note_en = '';
    public string $note_id = '';
    public bool $is_active = true;
    public int $sort_order = 0;
    public string $activeTab = 'en';
    public bool $isTranslating = false;

    protected function rules(): array
    {
        return [
            'country_code' => 'required|string|size:2',
            'region'       => 'required|string|max:100',
            'name_en'      => 'required|string|max:100',
            'name_id'      => 'required|string|max:100',
            'note_en'      => 'nullable|string|max:500',
            'note_id'      => 'nullable|string|max:500',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer|min:0',
        ];
    }

    /*
     * Kantong galatnya ikut dikosongkan tiap kali modalnya dibuka.
     *
     * Kantong itu bertahan lintas permintaan: sekali percobaan simpan gagal,
     * pesan merahnya — beserta titik merah di sakelar bahasanya — masih
     * menempel saat modalnya dibuka lagi untuk negara yang lain, padahal
     * isiannya sudah benar. Yang terbaca pemakai: galat yang tidak bisa
     * dihilangkan.
     */
    public function create(): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->showModal = true;
    }

    public function autoTranslate(): void
    {
        if (empty(trim($this->name_id)) && empty(trim($this->note_id))) {
            session()->flash('error', 'Isi konten Bahasa Indonesia terlebih dahulu.');
            return;
        }

        $this->isTranslating = true;

        $translated = app(TranslationService::class)->translateMany([
            'name' => $this->name_id,
            'note' => $this->note_id,
        ]);

        if (!empty($translated['name'])) $this->name_en = $translated['name'];
        if (!empty($translated['note'])) $this->note_en = $translated['note'];

        $this->isTranslating = false;
        $this->activeTab = 'en';
    }

    public function edit(string $id): void
    {
        $this->resetValidation();

        $market = ExportMarket::with('translations')->findOrFail($id);
        $this->editingId = $market->id;
        $this->country_code = $market->country_code;
        $this->region = $market->region;
        $this->name_en = $market->getTranslation('name', 'en') ?? '';
        $this->name_id = $market->getTranslation('name', 'id') ?? '';
        $this->note_en = $market->getTranslation('note', 'en') ?? '';
        $this->note_id = $market->getTranslation('note', 'id') ?? '';
        $this->is_active = $market->is_active;
        $this->sort_order = $market->sort_order;
        $this->activeTab = 'en';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $market = $this->editingId 
            ? ExportMarket::findOrFail($this->editingId)
            : new ExportMarket();

        $market->country_code = strtoupper($this->country_code);
        $market->region = $this->region;
        $market->is_active = $this->is_active;
        $market->sort_order = $this->sort_order;
        $market->save();

        // Simpan Terjemahan (EN & ID)
        $market->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => $this->name_en, 'note' => $this->note_en]
        );
        $market->translations()->updateOrCreate(
            ['locale' => 'id'],
            ['name' => $this->name_id, 'note' => $this->note_id]
        );

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Export market saved successfully!');
    }

    public function delete(string $id): void
    {
        ExportMarket::findOrFail($id)->delete();
        session()->flash('message', 'Export market deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->country_code = '';
        $this->region = 'Asia';
        $this->name_en = '';
        $this->name_id = '';
        $this->note_en = '';
        $this->note_id = '';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->activeTab = 'en';
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        $markets = ExportMarket::with('translations')
            /*
             * Ketiga syarat pencariannya dikurung sendiri.
             *
             * when() tidak membungkus isinya dalam tanda kurung, jadi tanpa $b
             * ini SQL-nya jadi "(kode) OR (kawasan) OR nama AND is_active = ?"
             * — dan AND mengikat lebih erat daripada OR, sehingga penyaring
             * statusnya cuma berlaku untuk cabang terakhir.
             */
            ->when($this->search, function ($q) {
                $q->where(function ($b) {
                    $b->where('country_code', 'LIKE', "%{$this->search}%")
                      ->orWhere('region', 'LIKE', "%{$this->search}%")
                      ->orWhereHas('translations', function ($trans) {
                          $trans->where('name', 'LIKE', "%{$this->search}%");
                      });
                });
            })
            ->when($this->selectedStatus, function ($q) {
                $q->where('is_active', $this->selectedStatus === 'active');
            })
            ->when($this->selectedRegion, function ($q) {
                $q->where('region', $this->selectedRegion);
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.export-market-index', [
            'markets' => $markets,

            /*
             * Kawasan yang benar-benar ada datanya, bukan ketujuh pilihan tetap
             * di modalnya: menawarkan kawasan yang nol barisnya cuma memberi
             * jalan buntu.
             */
            'regions' => ExportMarket::query()
                ->select('region')->distinct()->orderBy('region')->pluck('region'),
        ]);
    }
}
