<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ExportMarket;

class ExportMarketIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?string $editingId = null;

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

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
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

    public function render()
    {
        $markets = ExportMarket::with('translations')
            ->when($this->search, function ($q) {
                $q->where('country_code', 'LIKE', "%{$this->search}%")
                  ->orWhere('region', 'LIKE', "%{$this->search}%")
                  ->orWhereHas('translations', function ($trans) {
                      $trans->where('name', 'LIKE', "%{$this->search}%");
                  });
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.export-market-index', [
            'markets' => $markets,
        ]);
    }
}
