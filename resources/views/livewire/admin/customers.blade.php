@section('title', 'Customers')
@section('page_title', 'Customers')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Customers ({{ $customers->total() }})</h3>
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search name, phone, email…"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem;flex:1;min-width:200px" />
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Online Orders</th>
                    <th>POS Sales</th>
                    <th>Total Spent</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr wire:key="customer-{{ $customer->id }}">
                        <td style="font-weight:500">{{ $customer->name }}</td>
                        <td style="color:var(--text-muted);font-size:.82rem">{{ $customer->phone }} @if($customer->phone && $customer->email) &middot; @endif {{ $customer->email }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>{{ $customer->sales_count }}</td>
                        <td>${{ number_format(($customer->orders_sum_total ?? 0) + ($customer->sales_sum_total ?? 0), 2) }}</td>
                        <td>
                            <button type="button" wire:click="toggleExpand({{ $customer->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">
                                {{ $expandedId === $customer->id ? 'Hide History' : 'View History' }}
                            </button>
                        </td>
                    </tr>
                    @if ($expandedId === $customer->id && $expanded)
                        <tr>
                            <td colspan="6" style="background:var(--bg-alt);padding:1rem 1.5rem">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                                    <div>
                                        <h4 style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.5rem;color:var(--text-muted)">Recent Online Orders</h4>
                                        @forelse ($expanded->orders as $order)
                                            <div style="font-size:.82rem;padding:.35rem 0;border-bottom:1px solid var(--border)">
                                                {{ $order->order_number }} — ${{ number_format($order->total, 2) }} — {{ ucfirst($order->status) }}
                                            </div>
                                        @empty
                                            <p style="font-size:.8rem;color:var(--text-muted)">No online orders yet.</p>
                                        @endforelse
                                    </div>
                                    <div>
                                        <h4 style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.5rem;color:var(--text-muted)">Recent POS Sales</h4>
                                        @forelse ($expanded->sales as $sale)
                                            <div style="font-size:.82rem;padding:.35rem 0;border-bottom:1px solid var(--border)">
                                                {{ $sale->sale_number }} — ${{ number_format($sale->total, 2) }}
                                            </div>
                                        @empty
                                            <p style="font-size:.8rem;color:var(--text-muted)">No POS sales yet.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted)">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $customers->links() }}</div>
    </div>
</div>
