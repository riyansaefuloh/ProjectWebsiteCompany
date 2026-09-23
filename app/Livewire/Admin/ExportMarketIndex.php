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
    public string $selectedStatus = '';
    public string $selectedRegion = '';

    public bool $showModal = false;
    public ?string $editingId = null;

    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'selectedStatus', 'selectedRegion'], true)) {
            $this->resetPage();
        }
    }

    public string $country_code = '';
    public string $region = 'Asia';
    public string $name_en = '';
    public string $name_id = '';
    public string $note_en = '';
    public string $note_id = '';
    public string $status = 'active';
    public int $sort_order = 0;
    public string $activeTab = 'en';
    public bool $isTranslating = false;
    public ?string $galatTerjemah = null;

    protected function rules(): array
    {
        return [
            'country_code' => 'required|string|size:2',
            'region'       => 'required|string|max:100',
            'name_en'      => 'required|string|max:100',
            'name_id'      => 'required|string|max:100',
            'note_en'      => 'nullable|string|max:500',
            'note_id'      => 'nullable|string|max:500',
            'status'       => 'required|in:active,inactive',
            'sort_order'   => 'integer|min:0',
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->galatTerjemah = null;
        $this->resetForm();
        $this->showModal = true;
    }

    public function autoTranslate(): void
    {
        $this->galatTerjemah = null;

        if (empty(trim($this->name_id)) && empty(trim($this->note_id))) {
            $this->galatTerjemah = 'Isi dulu nama atau catatan pasarnya dalam Bahasa Indonesia.';
            return;
        }

        $this->isTranslating = true;

        $layanan    = app(TranslationService::class);
        $translated = $layanan->translateMany([
            'name' => $this->name_id,
            'note' => $this->note_id,
        ]);

        if (!empty($translated['name'])) $this->name_en = $translated['name'];
        if (!empty($translated['note'])) $this->note_en = $translated['note'];

        if ($layanan->sebabGagal) {
            $this->galatTerjemah = $layanan->sebabGagal;
        }

        $this->isTranslating = false;
        $this->activeTab = 'en';
    }

    public function edit(string $id): void
    {
        $this->resetValidation();
        $this->galatTerjemah = null;

        $market = ExportMarket::with('translations')->findOrFail($id);
        $this->editingId = $market->id;
        $this->country_code = $market->country_code;
        $this->region = $market->region;
        $this->name_en = $market->getTranslation('name', 'en') ?? '';
        $this->name_id = $market->getTranslation('name', 'id') ?? '';
        $this->note_en = $market->getTranslation('note', 'en') ?? '';
        $this->note_id = $market->getTranslation('note', 'id') ?? '';
        $this->status = $market->is_active ? 'active' : 'inactive';
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
        $market->is_active = $this->status === 'active';
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
        $this->status = 'active';
        $this->sort_order = 0;
        $this->activeTab = 'en';
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $markets = ExportMarket::with('translations')
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
            'regions' => ExportMarket::query()
                ->select('region')->distinct()->orderBy('region')->pluck('region'),
        ]);
    }
}
