@extends('layouts.app')

@section('title', 'تفاصيل الطلب #' . $order->id . ' - ' . config('app.name'))

@push('css')
    <style>
        .order-status-banner {
            background: linear-gradient(135deg, #111 0%, #333 100%);
            border-radius: 20px;
            padding: 40px;
            color: #fff;
            margin-bottom: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .status-pill-large {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .detail-card {
            border-radius: 20px !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03) !important;
            height: 100%;
        }

        .order-item-row {
            padding: 20px 0;
            border-bottom: 1px solid #f8f9fa;
            transition: background 0.2s;
        }

        .order-item-row:last-child {
            border-bottom: none;
        }

        .item-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .step-indicator {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .step-indicator::before {
            content: "";
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 100px;
        }

        .step-icon {
            width: 32px;
            height: 32px;
            background: #444;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            border: 2px solid #555;
        }

        .step-item.active .step-icon {
            background: #d4af37;
            border-color: #fff;
            color: #000;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.5);
        }

        .step-label {
            font-size: 0.7rem;
            font-weight: 700;
            opacity: 0.6;
        }

        .step-item.active .step-label {
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">الرئيسية</a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}"
                        class="text-decoration-none text-muted">طلباتي</a></li>
                <li class="breadcrumb-item active fw-bold text-dark">تفاصيل الطلب #{{ $order->id }}</li>
            </ol>
        </nav>

        <!-- Status Banner -->
        <div class="order-status-banner">
            <div class="row align-items-center g-4">
                <div class="col-md-7">
                    <span class="status-pill-large mb-3 d-inline-block">{{ strtoupper($order->status) }}</span>
                    <h1 class="fw-black mb-2">طلبك {{ $order->status === 'completed' ? 'تم بنجاح' : 'قيد التنفيذ' }}</h1>
                    <p class="mb-0 opacity-75">شكراً لتسوقك معنا. نحن نقدر ثقتك في ماركتنا ونعمل جاهدين لإيصال أناقتك في
                        أسرع وقت.</p>
                </div>
                <div class="col-md-5">
                    <div class="step-indicator">
                        @php $statuses = ['pending', 'processing', 'shipped', 'delivered', 'completed']; @endphp
                        @foreach ($statuses as $index => $s)
                            @php
                                $isActive = false;
                                $currentIdx = array_search($order->status, $statuses);
                                if ($currentIdx !== false && array_search($s, $statuses) <= $currentIdx) {
                                    $isActive = true;
                                }
                            @endphp
                            <div class="step-item {{ $isActive ? 'active' : '' }}">
                                <div class="step-icon">
                                    @if ($s == 'pending')
                                        <i class="bi bi-receipt"></i>
                                    @elseif($s == 'processing')
                                        <i class="bi bi-gear"></i>
                                    @elseif($s == 'shipped')
                                        <i class="bi bi-truck"></i>
                                    @elseif($s == 'delivered')
                                        <i class="bi bi-box-seam"></i>
                                    @elseif($s == 'completed')
                                        <i class="bi bi-check-lg"></i>
                                    @endif
                                </div>
                                <div class="step-label text-uppercase">{{ $s }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <!-- Order Items -->
            <div class="col-lg-8">
                <div class="card detail-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <h5 class="fw-black mb-0">المنتجات المطلوبة</h5>
                        <span class="badge bg-light text-dark fw-bold px-3 py-2 rounded-pill border">
                            {{ count(is_string($order->items) ? json_decode($order->items) : $order->items) }} منتجات
                        </span>
                    </div>

                    <div class="order-items-container">
                        @foreach (is_string($order->items) ? json_decode($order->items, true) : $order->items as $item)
                            @php
                                $item = is_array($item) ? (object) $item : $item;
                                $image = $item->image ?? $item->product_image ?? null;
                                // If image doesn't start with http or /, add storage path
                                if ($image && !str_starts_with($image, 'http') && !str_starts_with($image, '/')) {
                                    $image = '/storage/' . $image;
                                }
                                $image = $image ?? '/assets/img/placeholder.svg';
                            @endphp
                            <div class="order-item-row">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <img src="{{ $image }}" alt="{{ $item->name ?? $item->product_name ?? 'Product' }}"
                                            class="item-thumbnail" onerror="this.src='/assets/img/placeholder.svg'">
                                    </div>
                                    <div class="col">
                                        <h6 class="fw-bold mb-1">{{ data_get($item, 'name') ?? data_get($item, 'product_name') }}</h6>
                                        @if (!empty(data_get($item, 'options')))
                                            <div class="small text-muted">
                                                @foreach ((array) data_get($item, 'options', []) as $k => $v)
                                                    <span class="me-2">{{ $k }}: {{ $v }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="small fw-bold text-muted">الكمية: {{ data_get($item, 'quantity') }}</div>
                                    </div>
                                    <div class="col-auto text-end">
                                        <div class="fw-black text-dark">
                                            {{ number_format(data_get($item, 'price', data_get($item, 'unit_price', 0)) * session('currency_rate', 1), 2) }}
                                            <small>{{ session('currency_symbol', 'SAR') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                @php
                                    $orderSubtotal = $order->subtotal ?? ($order->total ? $order->total / 1.15 : 0);
                                    $orderTax      = $order->tax ?? ($order->total ? $order->total - $orderSubtotal - ($order->shipping_cost ?? 0) : 0);
                                    $orderShipping = $order->shipping_cost ?? 0;
                                    $orderGrand    = $order->total ?? $order->total_price ?? 0;
                                @endphp
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">المجموع الجزئي</span>
                                    <span
                                        class="fw-bold">{{ number_format($orderSubtotal * session('currency_rate', 1), 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">الضريبة (15%)</span>
                                    <span
                                        class="fw-bold">{{ number_format($orderTax * session('currency_rate', 1), 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-success">
                                    <span>رسوم الشحن</span>
                                    <span class="fw-bold">{{ $orderShipping > 0 ? number_format($orderShipping * session('currency_rate', 1), 2) . ' ' . session('currency_symbol', 'SAR') : 'مجاني' }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-black fs-5">الإجمالي الكلي</span>
                                    <div class="text-end">
                                        <h4 class="fw-black mb-0 text-gold">
                                            {{ number_format($orderGrand * session('currency_rate', 1), 2) }}</h4>
                                        <small class="fw-bold text-muted">{{ session('currency_symbol', 'SAR') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Sidebar -->
            <div class="col-lg-4">
                <!-- Customer Info -->
                <div class="card detail-card p-4 mb-4">
                    <h6 class="fw-black mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle text-muted"></i> معلومات العميل
                    </h6>
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="small text-muted text-uppercase mb-1 fw-bold">الاسم</div>
                        <div class="fw-bold text-dark">{{ $order->customer_name ?? auth()->user()->name }}</div>
                    </div>
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="small text-muted text-uppercase mb-1 fw-bold">البريد الإلكتروني</div>
                        <div class="fw-bold text-dark">{{ $order->customer_email ?? auth()->user()->email }}</div>
                    </div>
                    <div class="">
                        <div class="small text-muted text-uppercase mb-1 fw-bold">رقم الهاتف</div>
                        <div class="fw-bold text-dark">{{ $order->customer_phone ?? 'غير متوفر' }}</div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="card detail-card p-4 mb-4">
                    <h6 class="fw-black mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-muted"></i> عنوان الشحن
                    </h6>
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
                        }
                    @endphp
                    <p class="mb-0 text-muted leading-relaxed">
                        {{ $address ?? 'لا يوجد عنوان محدد' }}
                    </p>
                    <div class="mt-3 pt-3 border-top">
                        <div class="small text-muted text-uppercase mb-1 fw-bold">طريقة الشحن</div>
                        <div class="fw-bold text-dark">شحن قياسي (DHL / Aramex)</div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-grid gap-3">
                    <a href="{{ route('orders.invoice', $order->id) }}"
                        class="btn btn-dark py-3 rounded-4 fw-bold shadow-sm">
                        <i class="bi bi-printer me-2"></i> تحميل فاتورة PDF
                    </a>
                    @if (in_array($order->status, ['pending', 'processing']))
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" id="cancelOrderForm">
                            @csrf
                            <button type="button" class="btn btn-outline-danger w-100 py-3 rounded-4 fw-bold"
                                onclick="confirmCancelOrder()">
                                إلغاء الطلب
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('pages.contact') }}"
                        class="btn btn-link text-muted text-decoration-none small text-center fw-bold">
                        <i class="bi bi-chat-dots me-1"></i> هل تواجه مشكلة؟ تواصل معنا
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function confirmCancelOrder() {
            Swal.fire({
                title: "@lang('messages.confirm_cancel_order_title')",
                text: "@lang('messages.confirm_cancel_order_text')",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: "@lang('messages.confirm_cancel_order_btn')",
                cancelButtonText: "@lang('messages.go_back_btn')",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancelOrderForm').submit();
                }
            });
        }
    </script>
@endpush
