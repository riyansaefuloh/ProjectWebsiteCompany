<div style="font-family: sans-serif; padding: 20px;">
    <h2>Export Markets & Target Countries Management</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <input type="text" wire:model.live="search" placeholder="Search country, ISO code, region..." style="padding: 8px; width: 300px;">
        <button wire:click="create" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Add Export Target Country
        </button>
    </div>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th>ISO Code</th>
                <th>Country Name (EN)</th>
                <th>Region</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($markets as $market)
                <tr>
                    <td><code>{{ $market->country_code }}</code></td>
                    <td><strong>{{ $market->translated_name }}</strong></td>
                    <td>{{ $market->region }}</td>
                    <td>
                        <span style="color: {{ $market->is_active ? 'green' : 'red' }}; font-weight: bold;">
                            {{ $market->is_active ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </td>
                    <td>
                        <button wire:click="edit('{{ $market->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $market->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No export markets found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $markets->links() }}
    </div>

    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 450px;">
                <h3>{{ $editingId ? 'Edit Export Market' : 'Add Target Country' }}</h3>
                <form wire:submit.prevent="save">
                    <div style="margin-bottom: 10px;">
                        <label>ISO Country Code (2-letter e.g. US, DE, JP) *</label>
                        <input type="text" wire:model="country_code" maxlength="2" style="width: 100%; padding: 6px;" required>
                    </div>
                    <!-- Tabs for Language -->
                    @php $activeTab = $activeTab ?? 'en'; @endphp
                    <div style="margin-bottom: 15px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                        <div style="display: flex; background: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
                            <button type="button" wire:click="$set('activeTab', 'en')" style="flex: 1; padding: 10px; border: none; background: {{ $activeTab === 'en' ? 'white' : 'transparent' }}; border-bottom: {{ $activeTab === 'en' ? '2px solid #2563eb' : 'none' }}; cursor: pointer; font-weight: {{ $activeTab === 'en' ? 'bold' : 'normal' }};">English (EN)</button>
                            <button type="button" wire:click="$set('activeTab', 'id')" style="flex: 1; padding: 10px; border: none; background: {{ $activeTab === 'id' ? 'white' : 'transparent' }}; border-bottom: {{ $activeTab === 'id' ? '2px solid #2563eb' : 'none' }}; cursor: pointer; font-weight: {{ $activeTab === 'id' ? 'bold' : 'normal' }};">Indonesia (ID)</button>
                        </div>
                        <div style="padding: 15px; background: white;">
                            <!-- English Tab -->
                            <div style="display: {{ $activeTab === 'en' ? 'block' : 'none' }};">
                                <div style="margin-bottom: 10px;">
                                    <label>Country Name (EN) *</label>
                                    <input type="text" wire:model="name_en" style="width: 100%; padding: 6px;">
                                </div>
                                <div>
                                    <label>Note / Catatan (EN)</label>
                                    <textarea wire:model="note_en" rows="3" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>
                            <!-- Indonesia Tab -->
                            <div style="display: {{ $activeTab === 'id' ? 'block' : 'none' }};">
                                <div style="margin-bottom: 10px;">
                                    <label>Nama Negara (ID) *</label>
                                    <input type="text" wire:model="name_id" style="width: 100%; padding: 6px;">
                                </div>
                                <div>
                                    <label>Note / Catatan (ID)</label>
                                    <textarea wire:model="note_id" rows="3" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Region *</label>
                        <select wire:model="region" style="width: 100%; padding: 6px;" required>
                            <option value="Asia">Asia</option>
                            <option value="Europe">Europe</option>
                            <option value="North America">North America</option>
                            <option value="South America">South America</option>
                            <option value="Africa">Africa</option>
                            <option value="Australia/Oceania">Australia/Oceania</option>
                            <option value="Middle East">Middle East</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label>
                            <input type="checkbox" wire:model="is_active">
                            <strong>Active Export Market</strong>
                        </label>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Market</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
