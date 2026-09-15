@section('title', 'Categories')
@section('page_title', 'Category Management')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Categories ({{ $categories->total() }})</h3>
            @unless($showForm)
                <button type="button" wire:click="openCreate" class="btn-primary" style="padding:.6rem 1.4rem;font-size:.72rem">+ Add Category</button>
            @endunless
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search categories…"
                class="form-input" style="flex:1;min-width:200px" />
        </div>

        @if (session('error'))
            <div class="alert alert-error" style="margin-top:0;margin-bottom:1.5rem">{{ session('error') }}</div>
        @endif

        @if ($showForm)
            <div class="admin-card" style="border-color:var(--text);margin-bottom:1.5rem">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">{{ $editingId ? 'Edit Category' : 'Add New Category' }}</h3>
                </div>
                <form wire:submit.prevent="save">
                    <div class="form-grid" style="margin-bottom:1rem">
                        <div class="form-group full">
                            <label>Category Name *</label>
                            <input type="text" wire:model="name" placeholder="e.g. Islamic Books" autofocus />
                            @error('name') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Parent Category <span style="font-size:.72rem;color:var(--text-muted)">(optional)</span></label>
                            <select wire:model="parent_id" class="filter-select">
                                <option value="">None (top-level)</option>
                                @foreach ($allCategories as $cat)
                                    @if ($cat->id !== $editingId)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" wire:model="sort_order" min="0" />
                        </div>
                        <div class="form-group full">
                            <label>Description</label>
                            <textarea wire:model="description" rows="2"
                                style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.9rem;resize:vertical"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Image <span style="font-size:.72rem;color:var(--text-muted)">(optional)</span></label>
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" style="width:80px;height:80px;object-fit:cover;border:1px solid var(--border);margin-bottom:.5rem" />
                            @elseif ($currentImage)
                                <img src="{{ asset('storage/' . $currentImage) }}" style="width:80px;height:80px;object-fit:cover;border:1px solid var(--border);margin-bottom:.5rem" />
                            @endif
                            <input type="file" wire:model="image" accept="image/*"
                                style="width:100%;padding:.5rem;border:1px solid var(--border);background:var(--bg);font-family:inherit;font-size:.8rem" />
                        </div>
                        <div class="form-group" style="justify-content:flex-end">
                            <label style="display:flex;align-items:center;gap:.75rem;font-size:.85rem;cursor:pointer">
                                <input type="checkbox" wire:model="is_active" style="accent-color:var(--text);width:16px;height:16px" />
                                Active (visible in shop)
                            </label>
                        </div>
                    </div>
                    <div style="display:flex;gap:.75rem;max-width:400px">
                        <button type="submit" class="btn-primary" style="flex:1">{{ $editingId ? 'Update' : 'Create' }}</button>
                        <button type="button" wire:click="cancel" class="btn-ghost" style="flex:1">Cancel</button>
                    </div>
                </form>
            </div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Products</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr wire:key="category-{{ $category->id }}">
                        <td style="font-weight:500">{{ $category->name }}</td>
                        <td style="color:var(--text-muted)">{{ $category->parent?->name ?? '—' }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td style="color:var(--text-muted)">{{ $category->sort_order }}</td>
                        <td>
                            <span class="badge {{ $category->is_active ? 'badge-active' : 'badge-hidden' }}">
                                {{ $category->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:.5rem">
                                <button type="button" wire:click="openEdit({{ $category->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">Edit</button>
                                <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Delete this category?" class="btn-danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted)">No categories found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $categories->links() }}</div>
    </div>
</div>
