@section('title', 'Publishers')
@section('page_title', 'Publisher Management')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Publishers ({{ $publishers->total() }})</h3>
            @unless($showForm)
                <button type="button" wire:click="openCreate" class="btn-primary" style="padding:.6rem 1.4rem;font-size:.72rem">+ Add Publisher</button>
            @endunless
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search publishers…"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem;flex:1;min-width:200px" />
        </div>

        @if (session('error'))
            <div style="background:#8b1a1a22;border:1px solid #8b1a1a;padding:.85rem 1.25rem;margin-bottom:1.5rem;font-size:.85rem;color:#8b1a1a">{{ session('error') }}</div>
        @endif

        @if ($showForm)
            <div class="admin-card" style="border-color:var(--text);margin-bottom:1.5rem">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">{{ $editingId ? 'Edit Publisher' : 'Add New Publisher' }}</h3>
                </div>
                <form wire:submit.prevent="save">
                    <div class="form-grid" style="margin-bottom:1rem">
                        <div class="form-group full">
                            <label>Publisher Name *</label>
                            <input type="text" wire:model="name" placeholder="e.g. Oxford University Press" autofocus />
                            @error('name') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
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
                @forelse ($publishers as $publisher)
                    <tr wire:key="publisher-{{ $publisher->id }}">
                        <td style="font-weight:500">{{ $publisher->name }}</td>
                        <td>{{ $publisher->products_count }}</td>
                        <td>
                            <span style="font-size:.72rem;padding:.2rem .55rem;border:1px solid;
                                border-color:{{ $publisher->is_active ? '#1a5c2c' : '#8b1a1a' }};
                                color:{{ $publisher->is_active ? '#1a5c2c' : '#8b1a1a' }}">
                                {{ $publisher->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:.5rem">
                                <button type="button" wire:click="openEdit({{ $publisher->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">Edit</button>
                                <button type="button" wire:click="delete({{ $publisher->id }})" wire:confirm="Delete this publisher?"
                                    style="padding:.35rem .75rem;font-size:.7rem;border:1px solid #8b1a1a;color:#8b1a1a;background:none;cursor:pointer;font-family:inherit">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;padding:3rem;color:var(--text-muted)">No publishers found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $publishers->links() }}</div>
    </div>
</div>
