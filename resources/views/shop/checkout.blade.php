@extends('layouts.app')
@section('title', 'Checkout — Book Depot')

@section('content')
    <section class="checkout-section">
        <div class="container">
            <div style="padding:7rem 0 2rem">
                <div class="checkout-steps">
                    <span class="step active" id="step1Label">1 Information</span>
                    <span class="step-sep">›</span>
                    <span class="step" id="step2Label">2 Payment</span>
                    <span class="step-sep">›</span>
                    <span class="step" id="step3Label">3 Confirm</span>
                </div>
            </div>

            <div class="checkout-layout">
                {{-- ── Form ── --}}
                <div class="checkout-form-wrap">

                    {{-- Step 1 --}}
                    <div class="checkout-step" id="checkoutStep1">
                        <h2 class="checkout-step-title">Billing Details</h2>
                        <form id="billingForm">
                            @csrf
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input name="first_name"
                                        value="{{ auth()->user()->name ? explode(' ', auth()->user()->name)[0] : '' }}"
                                        required placeholder="John" />
                                </div>
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input name="last_name"
                                        value="{{ auth()->user()->name ? explode(' ', auth()->user()->name)[1] ?? '' : '' }}"
                                        required placeholder="Doe" />
                                </div>
                                <div class="form-group full">
                                    <label>Email *</label>
                                    <input type="email" name="email" value="{{ auth()->user()->email }}" required />
                                </div>
                                <div class="form-group full">
                                    <label>Phone</label>
                                    <input type="tel" name="phone" value="{{ auth()->user()->phone }}"
                                        placeholder="+1 (555) 000-0000" />
                                </div>
                                <div class="form-group full">
                                    <label>Street Address *</label>
                                    <input name="address" required placeholder="123 Fashion Street" />
                                </div>
                                <div class="form-group">
                                    <label>City *</label>
                                    <input name="city" required placeholder="New York" />
                                </div>
                                <div class="form-group">
                                    <label>State</label>
                                    <input name="state" placeholder="NY" />
                                </div>
                                <div class="form-group">
                                    <label>ZIP Code *</label>
                                    <input name="zip_code" required placeholder="10001" />
                                </div>
                                <div class="form-group">
                                    <label>Country *</label>
                                    <select name="country">
                                        <option>United States</option>
                                        <option>United Kingdom</option>
                                        <option>Canada</option>
                                        <option>Australia</option>
                                        <option>Pakistan</option>
                                    </select>
                                </div>
                                <div class="form-group full">
                                    <label>Order Notes (optional)</label>
                                    <textarea name="notes" rows="2"
                                        style="padding:.75rem 1rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.9rem;resize:vertical"
                                        placeholder="Special delivery instructions…"></textarea>
                                </div>
                            </div>
                            <button type="button" class="btn-primary" onclick="goToPayment()">Continue to Payment
                                →</button>
                        </form>
                    </div>

                    {{-- Step 2 --}}
                    <div class="checkout-step hidden" id="checkoutStep2">
                        <h2 class="checkout-step-title">Payment Method</h2>
                        <form action="{{ route('checkout.place') }}" method="POST" id="orderForm"
                            onsubmit="this.querySelector('button[type=submit]').disabled = true">
                            @csrf
                            {{-- Hidden billing fields (populated by JS) --}}
                            <input type="hidden" name="first_name" id="hFirstName" />
                            <input type="hidden" name="last_name" id="hLastName" />
                            <input type="hidden" name="email" id="hEmail" />
                            <input type="hidden" name="phone" id="hPhone" />
                            <input type="hidden" name="address" id="hAddress" />
                            <input type="hidden" name="city" id="hCity" />
                            <input type="hidden" name="state" id="hState" />
                            <input type="hidden" name="zip_code" id="hZip" />
                            <input type="hidden" name="country" id="hCountry" />
                            <input type="hidden" name="notes" id="hNotes" />
                            <input type="hidden" name="payment_method" id="hPayment" value="cod" />

                            <div class="payment-methods">
                                <label class="payment-method-card active" onclick="selectPayment('cod',this)">
                                    <input type="radio" name="_pm" value="cod" checked />
                                    <div class="pm-info">
                                        <span class="pm-name">Cash on Delivery</span>
                                        <span style="font-size:.8rem;opacity:.6">Pay when you receive</span>
                                    </div>
                                </label>
                                <label class="payment-method-card" onclick="selectPayment('stripe',this)">
                                    <input type="radio" name="_pm" value="stripe" />
                                    <div class="pm-info">
                                        <span class="pm-name">Credit / Debit Card</span>
                                        <div class="pm-icons">
                                            <span class="payment-icon"
                                                style="padding:.2rem .4rem;font-size:.6rem">VISA</span>
                                            <span class="payment-icon"
                                                style="padding:.2rem .4rem;font-size:.6rem">MC</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="payment-method-card" onclick="selectPayment('paypal',this)">
                                    <input type="radio" name="_pm" value="paypal" />
                                    <div class="pm-info">
                                        <span class="pm-name">PayPal</span>
                                        <span style="font-weight:700;font-size:.9rem;opacity:.7">PayPal</span>
                                    </div>
                                </label>
                            </div>

                            {{-- Stripe card fields --}}
                            <div id="cardFields" class="card-fields hidden">
                                <div class="form-group full" style="margin-bottom:1rem">
                                    <label>Card Number</label>
                                    <div class="stripe-field">
                                        <input placeholder="1234 5678 9012 3456" oninput="fmtCard(this)" />
                                        <svg width="20" height="20" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24"
                                            style="margin-right:1rem;color:var(--text-muted)">
                                            <rect x="1" y="4" width="22" height="16" rx="2" />
                                            <line x1="1" y1="10" x2="23" y2="10" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="form-grid" style="margin-bottom:1rem">
                                    <div class="form-group"><label>Expiry</label><input placeholder="MM / YY"
                                            oninput="fmtExp(this)" /></div>
                                    <div class="form-group"><label>CVV</label><input placeholder="•••" maxlength="4"
                                            type="password" /></div>
                                </div>
                                <div class="form-group full" style="margin-bottom:1rem"><label>Name on Card</label><input
                                        placeholder="John Doe" /></div>
                                <div class="stripe-badge">
                                    <svg width="14" height="14" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0110 0v4" />
                                    </svg>
                                    Secured by <strong>Stripe</strong>
                                </div>
                            </div>

                            <div style="display:flex;gap:1rem;margin-top:1.5rem">
                                <button type="button" class="btn-ghost" onclick="goBack()">← Back</button>
                                <button type="submit" class="btn-primary">Place Order →</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Order Summary ── --}}
                <aside class="checkout-summary">
                    <h3>Order Summary</h3>
                    <div class="checkout-items-list">
                        @foreach ($cart->items as $item)
                            <div class="checkout-item">
                                <div class="checkout-item-img">
                                    @if ($item->product->image)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                            style="width:100%;height:100%;object-fit:cover" />
                                    @endif
                                    <span class="checkout-item-badge">{{ $item->quantity }}</span>
                                </div>
                                <div class="checkout-item-details">
                                    <div class="checkout-item-name">{{ $item->product->name }}</div>
                                    <div class="checkout-item-meta">Qty: {{ $item->quantity }}</div>
                                </div>
                                <div class="checkout-item-price">${{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="summary-line"><span>Subtotal</span><span>${{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    <div class="summary-line">
                        <span>Shipping</span><span>{{ $cart->shipping > 0 ? '$' . number_format($cart->shipping, 2) : 'Free' }}</span>
                    </div>
                    <div class="summary-line"><span>Tax (8%)</span><span>${{ number_format($cart->tax, 2) }}</span></div>
                    <div class="summary-total"><span>Total</span><span>${{ number_format($cart->total, 2) }}</span></div>
                </aside>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            function goToPayment() {
                const f = document.getElementById('billingForm');
                const inputs = f.querySelectorAll('[required]');
                let valid = true;
                inputs.forEach(i => {
                    if (!i.value.trim()) {
                        i.style.borderColor = '#c0392b';
                        valid = false;
                    } else i.style.borderColor = '';
                });
                if (!valid) {
                    showToast('Please fill in all required fields', 'error');
                    return;
                }

                // Copy billing to hidden fields
                ['FirstName', 'LastName', 'Email', 'Phone', 'Address', 'City', 'State', 'Zip', 'Country', 'Notes'].forEach(
                k => {
                    const src = f.querySelector('[name="' + k.toLowerCase().replace('zip', 'zip_code') + '"]') ||
                        f.querySelector('[name="' + k.toLowerCase() + '"]');
                    const dst = document.getElementById('h' + k);
                    if (src && dst) dst.value = src.value;
                });
                // manual fixes
                document.getElementById('hFirstName').value = f.querySelector('[name="first_name"]').value;
                document.getElementById('hLastName').value = f.querySelector('[name="last_name"]').value;
                document.getElementById('hEmail').value = f.querySelector('[name="email"]').value;
                document.getElementById('hPhone').value = f.querySelector('[name="phone"]')?.value || '';
                document.getElementById('hAddress').value = f.querySelector('[name="address"]').value;
                document.getElementById('hCity').value = f.querySelector('[name="city"]').value;
                document.getElementById('hState').value = f.querySelector('[name="state"]')?.value || '';
                document.getElementById('hZip').value = f.querySelector('[name="zip_code"]').value;
                document.getElementById('hCountry').value = f.querySelector('[name="country"]').value;
                document.getElementById('hNotes').value = f.querySelector('[name="notes"]')?.value || '';

                document.getElementById('checkoutStep1').classList.add('hidden');
                document.getElementById('checkoutStep2').classList.remove('hidden');
                document.getElementById('step2Label').classList.add('active');
                window.scrollTo(0, 0);
            }

            function goBack() {
                document.getElementById('checkoutStep2').classList.add('hidden');
                document.getElementById('checkoutStep1').classList.remove('hidden');
                document.getElementById('step2Label').classList.remove('active');
            }

            function selectPayment(type, label) {
                document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
                label.classList.add('active');
                document.getElementById('hPayment').value = type;
                document.getElementById('cardFields').classList.toggle('hidden', type !== 'stripe');
            }

            function fmtCard(i) {
                let v = i.value.replace(/\D/g, '').substring(0, 16);
                i.value = v.replace(/(.{4})/g, '$1 ').trim();
            }

            function fmtExp(i) {
                let v = i.value.replace(/\D/g, '').substring(0, 4);
                if (v.length >= 2) v = v.substring(0, 2) + ' / ' + v.substring(2);
                i.value = v;
            }
        </script>
    @endpush
@endsection
