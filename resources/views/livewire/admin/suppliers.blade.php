@section('title', 'Suppliers')
@section('page_title', 'Supplier Management')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Suppliers ({{ $suppliers->total() }})</h3>
            @unless($showForm)
                <button type="button" wire:click="openCreate" class="btn-primary" style="padding:.6rem 1.4rem;font-size:.72rem">+ Add Supplier</button>
            @endunless
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search name, phone, email…"
                class="form-input" style="flex:1;min-width:200px" />
        </div>

        @if (session('error'))
            <div class="alert alert-error" style="margin-top:0;margin-bottom:1.5rem">{{ session('error') }}</div>
        @endif

        @if ($showForm)
            <div class="admin-card" style="border-color:var(--text);margin-bottom:1.5rem">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">{{ $editingId ? 'Edit Supplier' : 'Add New Supplier' }}</h3>
                </div>
                <form wire:submit.prevent="save">
                    <div class="form-grid" style="margin-bottom:1rem">
                        <div class="form-group full">
                            <label>Supplier Name *</label>
                            <input type="text" wire:model="name" placeholder="e.g. Ferozsons Distributors" autofocus />
                            @error('name') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" wire:model="phone" />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" wire:model="email" />
                            @error('email') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group full">
                            <label>Address</label>
                            <input type="text" wire:model="address" />
                        </div>
                        <div class="form-group full">
                            <label>Notes</label>
                            <textarea wire:model="notes" rows="2"
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
                    <th>Contact</th>
                    <th>Purchases</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr wire:key="supplier-{{ $supplier->id }}">
                        <td style="font-weight:500">{{ $supplier->name }}</td>
                        <td style="color:var(--text-muted);font-size:.82rem">{{ $supplier->phone }} @if($supplier->phone && $supplier->email) &middot; @endif {{ $supplier->email }}</td>
                        <td>{{ $supplier->purchases_count }}</td>
                        <td>
                            <span class="badge {{ $supplier->is_active ? 'badge-active' : 'badge-hidden' }}">
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:.5rem">
                                <button type="button" wire:click="openEdit({{ $supplier->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">Edit</button>
                                <button type="button" wire:click="delete({{ $supplier->id }})" wire:confirm="Delete this supplier?" class="btn-danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted)">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $suppliers->links() }}</div>
    </div>
</div>
