<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Receipt {{ $sale->sale_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            width: 300px;
            margin: 1rem auto;
            font-size: 13px;
            color: #000;
        }
        .center { text-align: center; }
        .shop-name { font-size: 18px; font-weight: bold; letter-spacing: .1em; }
        hr { border: none; border-top: 1px dashed #000; margin: .6rem 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: .15rem 0; vertical-align: top; }
        .right { text-align: right; }
        .totals td { padding: .2rem 0; }
        .totals .label { color: #333; }
        .grand-total { font-weight: bold; font-size: 15px; }
        .footer { margin-top: 1rem; text-align: center; font-size: 12px; }
        @media print {
            body { margin: 0 auto; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <div class="shop-name">{{ config('app.name') }}</div>
        <div>Book Depot &middot; Retail Counter</div>
        <div>Receipt #{{ $sale->sale_number }}</div>
        <div>{{ $sale->created_at->format('M j, Y g:i A') }}</div>
    </div>

    <hr>

    <div>
        Cashier: {{ $sale->cashier->name ?? '—' }}<br>
        Customer: {{ $sale->customer->name ?? 'Walk-in Customer' }}
    </div>

    <hr>

    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="3">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x {{ number_format($item->price, 2) }}</td>
                <td class="right" colspan="2">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </table>

    <hr>

    <table class="totals">
        <tr>
            <td class="label">Subtotal</td>
            <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Discount</td>
            <td class="right">-{{ number_format($sale->discount, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total</td>
            <td class="right">{{ number_format($sale->total, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Payment ({{ str_replace('_', ' ', ucfirst($sale->payment_method)) }})</td>
            <td class="right">{{ number_format($sale->amount_paid, 2) }}</td>
        </tr>
        @if ($sale->payment_method === 'cash')
            <tr>
                <td class="label">Change</td>
                <td class="right">{{ number_format($sale->change_amount, 2) }}</td>
            </tr>
        @endif
    </table>

    <hr>

    <div class="footer">
        Thank you for shopping with us!<br>
        Please keep this receipt for any exchanges.
    </div>

    <div class="no-print center" style="margin-top:1rem">
        <button onclick="window.print()">Print Again</button>
    </div>
</body>
</html>
