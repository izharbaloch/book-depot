@section('title', 'Authors')
@section('page_title', 'Author Management')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Authors ({{ $authors->total() }})</h3>
            @unless($showForm)
                <button type="button" wire:click="openCreate" class="btn-primary" style="padding:.6rem 1.4rem;font-size:.72rem">+ Add Author</button>
            @endunless
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search authors…"
                class="form-input" style="flex:1;min-width:200px" />
        </div>

        @if (session('error'))
            <div class="alert alert-error" style="margin-top:0;margin-bottom:1.5rem">{{ session('error') }}</div>
        @endif

        @if ($showForm)
            <div class="admin-card" style="border-color:var(--text);margin-bottom:1.5rem">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">{{ $editingId ? 'Edit Author' : 'Add New Author' }}</h3>
                </div>
                <form wire:submit.prevent="save">
                    <div class="form-grid" style="margin-bottom:1rem">
                        <div class="form-group full">
                            <label>Author Name *</label>
                            <input type="text" wire:model="name" placeholder="e.g. Mark Twain" autofocus />
                            @error('name') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group full">
                            <label>Bio <span style="font-size:.72rem;color:var(--text-muted)">(optional)</span></label>
                            <textarea wire:model="bio" rows="3"
                                style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.9rem;resize:vertical"></textarea>
                        </div>
                        <div class="form-group">
                            <label style="display:flex;align-items:center;gap:.75rem;font-size:.85rem;cursor:pointer">
                                <input type="checkbox" wire:model="is_active" style="accent-color:var(--text);width:16px;height:16px" />
                                Active
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
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($authors as $author)
                    <tr wire:key="author-{{ $author->id }}">
                        <td style="font-weight:500">{{ $author->name }}</td>
                        <td>{{ $author->products_count }}</td>
                        <td>
                            <span class="badge {{ $author->is_active ? 'badge-active' : 'badge-hidden' }}">
                                {{ $author->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:.5rem">
                                <button type="button" wire:click="openEdit({{ $author->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">Edit</button>
                                <button type="button" wire:click="toggleActive({{ $author->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">
                                    {{ $author->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button type="button" wire:click="delete({{ $author->id }})" wire:confirm="Delete this author?" class="btn-danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;padding:3rem;color:var(--text-muted)">No authors found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $authors->links() }}</div>
    </div>
</div>
