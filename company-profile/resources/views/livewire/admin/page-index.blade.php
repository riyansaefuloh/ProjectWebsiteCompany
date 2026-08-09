<div style="font-family: sans-serif; padding: 20px;">
    <h2>Manage Static Pages</h2>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <button wire:click="create" style="padding: 10px 15px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Create New Page
        </button>
        <input type="text" wire:model.live="search" placeholder="Search pages..." style="padding: 8px; margin-left: 10px;">
    </div>

    @if($isOpen)
        <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #d1d5db;">
            <h3>{{ $page_id ? 'Edit Page' : 'Create Page' }}</h3>
            <form wire:submit.prevent="store">
                
                <div style="margin-bottom: 15px;">
                    <label><strong>Title (English) *</strong></label>
                    <input type="text" wire:model="title_en" style="width: 100%; padding: 8px;" required>
                    @error('title_en') <span style="color:red">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom: 15px;" wire:ignore>
                    <label><strong>Content (English)</strong></label>
                    <textarea id="tinymce_en" wire:model="content_en" rows="5" style="width: 100%; padding: 8px;"></textarea>
                </div>

                <hr style="margin: 20px 0;">

                <div style="margin-bottom: 15px;">
                    <label><strong>Title (Indonesian) *</strong></label>
                    <input type="text" wire:model="title_id" style="width: 100%; padding: 8px;" required>
                    @error('title_id') <span style="color:red">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom: 10px;" wire:ignore>
                    <label>Isi Halaman (ID)</label>
                    <textarea id="tinymce_id" wire:model="content_id" rows="5" style="width: 100%; padding: 6px;"></textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <label><strong>Status *</strong></label>
                    <select wire:model="status" style="width: 100%; padding: 8px;" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>

                <button type="submit" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Save Page
                </button>
                <button type="button" wire:click="closeModal" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                    Cancel
                </button>
            </form>
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr style="background: #e5e7eb; text-align: left;">
                <th style="padding: 10px; border: 1px solid #d1d5db;">Title (EN)</th>
                <th style="padding: 10px; border: 1px solid #d1d5db;">Slug</th>
                <th style="padding: 10px; border: 1px solid #d1d5db;">Status</th>
                <th style="padding: 10px; border: 1px solid #d1d5db;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
                <tr>
                    <td style="padding: 10px; border: 1px solid #d1d5db;">{{ $page->getTranslation('title', 'en') }}</td>
                    <td style="padding: 10px; border: 1px solid #d1d5db;">{{ $page->slug }}</td>
                    <td style="padding: 10px; border: 1px solid #d1d5db;">{{ ucfirst($page->status) }}</td>
                    <td style="padding: 10px; border: 1px solid #d1d5db;">
                        <button wire:click="edit('{{ $page->id }}')" style="padding: 5px 10px; background: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $page->id }}')" onclick="return confirm('Are you sure?')" style="padding: 5px 10px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding: 10px; border: 1px solid #d1d5db; text-align: center;">No pages found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $pages->links() }}
    </div>

    @if($isOpen)
        <!-- Initialize TinyMCE -->
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            function initTinyMCE() {
                if (typeof tinymce === 'undefined') return;
                tinymce.remove();
                tinymce.init({
                    selector: '#tinymce_en, #tinymce_id',
                    height: 300,
                    menubar: false,
                    plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'preview', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'table', 'help', 'wordcount'],
                    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    setup: function (editor) {
                        editor.on('change', function (e) {
                            let content = editor.getContent();
                            let modelName = editor.id === 'tinymce_en' ? 'content_en' : 'content_id';
                            @this.set(modelName, content);
                        });
                    }
                });
            }
            setTimeout(() => initTinyMCE(), 100);
        </script>
    @endif
</div>
