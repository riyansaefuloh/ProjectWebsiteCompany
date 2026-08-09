<div style="font-family: sans-serif; padding: 20px;">
    <h2>News & Articles Management</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <input type="text" wire:model.live="search" placeholder="Search news title or content..." style="padding: 8px; width: 300px;">
        <button wire:click="create" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Write New Article
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
                <th>Title (EN)</th>
                <th>Author</th>
                <th>Published At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($newsList as $news)
                <tr>
                    <td><strong>{{ $news->translated_title }}</strong></td>
                    <td>{{ $news->author ? $news->author->name : 'System' }}</td>
                    <td>{{ $news->published_at ? $news->published_at->format('d M Y H:i') : '-' }}</td>
                    <td>
                        <span style="color: {{ $news->status === 'published' ? 'green' : 'gray' }}; font-weight: bold;">
                            {{ strtoupper($news->status) }}
                        </span>
                    </td>
                    <td>
                        <button wire:click="edit('{{ $news->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $news->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No articles found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $newsList->links() }}
    </div>

    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; padding: 20px;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 900px; max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0;">{{ $editingId ? 'Edit Article' : 'Write New Article' }}</h3>
                    <button wire:click="$set('showModal', false)" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
                </div>
                
                <form wire:submit.prevent="save">
                    <!-- General Settings -->
                    <div style="display: flex; gap: 15px; margin-bottom: 20px; padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
                        <div style="flex: 1;">
                            <label><strong>Category</strong></label>
                            <select wire:model="news_category_id" style="width: 100%; padding: 6px; margin-top: 5px;">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label><strong>Status</strong></label>
                            <select wire:model="status" style="width: 100%; padding: 6px; margin-top: 5px;">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label><strong>Published Schedule</strong></label>
                            <input type="datetime-local" wire:model="published_at" style="width: 100%; padding: 6px; margin-top: 5px;">
                        </div>
                    </div>

                    <!-- Tags -->
                    <div style="margin-bottom: 20px;">
                        <label><strong>Tags:</strong></label>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
                            @foreach($tags as $tag)
                                <label style="display: flex; align-items: center; gap: 5px;">
                                    <input type="checkbox" wire:model="selectedTags" value="{{ $tag->id }}"> {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cover Image -->
                    <div style="margin-bottom: 20px; border: 1px solid #e5e7eb; padding: 10px; border-radius: 6px;">
                        <label><strong>Cover Image (Auto WebP)</strong></label>
                        @if($editingId && $existingCoverUrl)
                            <div style="margin-top: 5px; margin-bottom: 10px;">
                                <img src="{{ $existingCoverUrl }}" style="height: 100px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px;"><br>
                                <button type="button" wire:click="deleteCover" wire:confirm="Delete this cover?" style="font-size: 11px; background: #ef4444; color: white; border: none; padding: 4px 8px; cursor: pointer; border-radius: 4px; margin-top: 5px;">Delete Cover</button>
                            </div>
                        @endif
                        <input type="file" wire:model="coverFile" accept="image/*" style="width: 100%; padding: 6px; margin-top: 5px;">
                        <div wire:loading wire:target="coverFile" style="font-size: 12px; color: #2563eb;">Uploading...</div>
                        @error('coverFile') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tabs for Language Content -->
                    <div style="margin-bottom: 20px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                        <div style="display: flex; background: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
                            <button type="button" wire:click="$set('activeTab', 'en')" style="flex: 1; padding: 10px; border: none; background: {{ $activeTab === 'en' ? 'white' : 'transparent' }}; border-bottom: {{ $activeTab === 'en' ? '2px solid #2563eb' : 'none' }}; cursor: pointer; font-weight: {{ $activeTab === 'en' ? 'bold' : 'normal' }};">English (EN)</button>
                            <button type="button" wire:click="$set('activeTab', 'id')" style="flex: 1; padding: 10px; border: none; background: {{ $activeTab === 'id' ? 'white' : 'transparent' }}; border-bottom: {{ $activeTab === 'id' ? '2px solid #2563eb' : 'none' }}; cursor: pointer; font-weight: {{ $activeTab === 'id' ? 'bold' : 'normal' }};">Indonesia (ID)</button>
                        </div>
                        
                        <div style="padding: 15px; background: white;">
                            <!-- English Tab -->
                            <div style="display: {{ $activeTab === 'en' ? 'block' : 'none' }};">
                                <div style="margin-bottom: 10px;">
                                    <label>Article Title (EN) *</label>
                                    <input type="text" wire:model="title_en" style="width: 100%; padding: 6px;">
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <label>Excerpt / Ringkasan (EN)</label>
                                    <textarea wire:model="excerpt_en" rows="2" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                                <div style="margin-bottom: 15px;" wire:ignore>
                                    <label>Content (EN) *</label>
                                    <textarea id="tinymce_en" wire:model="content_en" rows="10" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                                
                                <h4 style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">SEO Meta (EN)</h4>
                                <div style="margin-bottom: 10px;">
                                    <label>Meta Title (EN)</label>
                                    <input type="text" wire:model="meta_title_en" style="width: 100%; padding: 6px;">
                                </div>
                                <div>
                                    <label>Meta Description (EN)</label>
                                    <textarea wire:model="meta_description_en" rows="2" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>

                            <!-- Indonesia Tab -->
                            <div style="display: {{ $activeTab === 'id' ? 'block' : 'none' }};">
                                <div style="margin-bottom: 10px;">
                                    <label>Judul Artikel (ID) *</label>
                                    <input type="text" wire:model="title_id" style="width: 100%; padding: 6px;">
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <label>Excerpt / Ringkasan (ID)</label>
                                    <textarea wire:model="excerpt_id" rows="2" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                                <div style="margin-bottom: 15px;" wire:ignore>
                                    <label>Isi Berita (ID) *</label>
                                    <textarea id="tinymce_id" wire:model="content_id" rows="10" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                                
                                <h4 style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">SEO Meta (ID)</h4>
                                <div style="margin-bottom: 10px;">
                                    <label>Meta Title (ID)</label>
                                    <input type="text" wire:model="meta_title_id" style="width: 100%; padding: 6px;">
                                </div>
                                <div>
                                    <label>Meta Description (ID)</label>
                                    <textarea wire:model="meta_description_id" rows="2" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Save Article</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Initialize TinyMCE -->
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            document.addEventListener('livewire:navigated', () => {
                initTinyMCE();
            });
            document.addEventListener('DOMContentLoaded', () => {
                initTinyMCE();
            });

            function initTinyMCE() {
                if (typeof tinymce === 'undefined') return;
                
                tinymce.remove(); // clear existing instances

                tinymce.init({
                    selector: '#tinymce_en, #tinymce_id',
                    height: 300,
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                    setup: function (editor) {
                        editor.on('change', function (e) {
                            let content = editor.getContent();
                            let modelName = editor.id === 'tinymce_en' ? 'content_en' : 'content_id';
                            @this.set(modelName, content);
                        });
                    }
                });
            }
        </script>
    @endif
</div>
