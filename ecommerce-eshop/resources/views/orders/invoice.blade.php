<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة رقم #{{ data_get($order, 'id') }}</title>
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
            padding: 40px;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th,
        table td {
            border: 1px solid #eee;
            padding: 12px;
            text-align: right;
        }

        table th {
            background: #f9f9f9;
        }

        .totals {
            text-align: left;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 0.9rem;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-top: 15px;
            transition: background 0.3s ease;
        }

        .back-btn:hover {
            background: #555;
        }

        @media print {
            .back-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>فاتورة شراء</h1>
                <p>رقم الطلب: #{{ $order->id ?? 'N/A' }}</p>
                <p>التاريخ: {{ is_string($order->created_at) ? date('Y/m/d', strtotime($order->created_at)) : ($order->created_at?->format('Y/m/d') ?? date('Y/m/d')) }}</p>
            </div>
            <div style="text-align: left;">
                <h2>ELEGANCE FASHION</h2>
                <p>المتجر الإلكتروني للأناقة</p>
            </div>
        </div>

        @php
            $address = $order->shipping_address ?? $order->address ?? null;
            if ($address && is_string($address)) {
                $decoded = json_decode($address, true);
                if ($decoded && json_last_error() === JSON_ERROR_NONE) {
                    $addressParts = [];
                    if (!empty($decoded['address'])) $addressParts[] = $decoded['address'];
                    if (!empty($decoded['city'])) $addressParts[] = $decoded['city'];
                    if (!empty($decoded['postal_code'])) $addressParts[] = $decoded['postal_code'];
                    $address = implode(', ', $addressParts);
                }
            } elseif (is_array($address)) {
                $address = implode(', ', $address);
            }
        @endphp
        <div class="info-section">
            <div>
                <h3>إلى:</h3>
                <p><strong>{{ $order->customer_name ?? 'بدون اسم' }}</strong></p>
                <p>{{ $address ?? 'لا يوجد عنوان' }}</p>
                <p>{{ $order->customer_phone ?? $order->phone ?? 'بدون رقم' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th style="text-align: center;">الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items ?? [] as $item)
                    @php
                        $item = is_array($item) ? (object) $item : $item;
                        $qty = $item->quantity ?? 1;
                        $price = $item->unit_price ?? $item->price ?? 0;
                        $total = $item->total_price ?? ($qty * $price);
                    @endphp
                    <tr>
                        <td>{{ $item->product_name ?? $item->product?->name ?? 'منتج بدون اسم' }}</td>
                        <td style="text-align: center;">{{ $qty }}</td>
                        <td>{{ number_format($price, 2) }}</td>
                        <td>{{ number_format($total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">لا توجد عناصر في الطلب</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            <p><strong>المجموع الكلي: {{ number_format(data_get($order, 'total_price', 0), 2) }} SAR</strong></p>
        </div>

        <div class="footer">
            <p>شكراً لشرائك من Elegance Fashion!</p>
            <p>هذه الفاتورة تم إصدارها آلياً ولا تحتاج لختم.</p>
            <div style="margin-top: 20px;">
                <a href="{{ route('orders.show', $order->id ?? 0) }}" class="back-btn">← العودة لصفحة الطلب</a>
            </div>
        </div>
    </div>
    <script>
        // Don't auto-print if user clicked back button
        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('no_print')) {
            window.print();
        }
    </script>
</body>

</html>
