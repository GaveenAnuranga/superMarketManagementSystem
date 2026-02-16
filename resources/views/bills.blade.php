<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bill Details - Supermarket</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; text-align: center; }
        h2 { color: #555; margin: 30px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #2196F3; }
        .bill-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .bill-info p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2196F3; color: white; }
        tr:hover { background: #f5f5f5; }
        .total-row { font-weight: bold; font-size: 18px; background: #fff3e0; }
        .history-item { padding: 10px; margin: 5px 0; background: #f9f9f9; border-radius: 4px; cursor: pointer; }
        .history-item:hover { background: #e3f2fd; }
        .history-item.active { background: #2196F3; color: white; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #2196F3; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('selling') }}" class="back-link">← Back to Selling</a>
        <h1>Bill Details</h1>
        
        <div class="bill-info">
            <p><strong>Bill ID:</strong> #{{ $bill->id }}</p>
            <p><strong>Customer Email:</strong> {{ $bill->customer_email }}</p>
            <p><strong>Date:</strong> {{ $bill->created_at->format('F d, Y h:i A') }}</p>
            @if($bill->updated_at != $bill->created_at)
                <p><strong>Last Updated:</strong> {{ $bill->updated_at->format('F d, Y h:i A') }}</p>
            @endif
        </div>

        <h2>Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billItems as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>LKR {{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>LKR {{ number_format($item['total'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td>LKR {{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <h2>Bill History</h2>
        @foreach($billHistory as $historyBill)
            <a href="{{ route('bills.show', $historyBill->id) }}" style="text-decoration: none; color: inherit;">
                <div class="history-item {{ $historyBill->id == $bill->id ? 'active' : '' }}">
                    <strong>Bill #{{ $historyBill->id }}</strong> - {{ $historyBill->created_at->format('M d, Y h:i A') }}
                </div>
            </a>
        @endforeach
    </div>
</body>
</html>
