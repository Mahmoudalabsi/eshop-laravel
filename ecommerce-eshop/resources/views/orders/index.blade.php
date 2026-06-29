@extends('layouts.app')

@section('title', 'طلباتي - ' . config('app.name'))

@push('css')
    <style>
        .order-card {
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .order-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07) !important;
            transform: translateY(-2px);
        }

        .order-header {
            background: #fcfcfc;
            border-bottom: 1px solid #f1f1f1;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fffbe6;
            color: #d48806;
            border: 1px solid #ffe58f;
        }

        .status-processing {
            background: #e6f7ff;
            color: #096dd9;
            border: 1px solid #91d5ff;
        }

        .status-completed {
            background: #f6ffed;
            color: #389e0d;
            border: 1px solid #b7eb8f;
        }

        .status-shipped {
            background: #e6f7ff;
            color: #096dd9;
            border: 1px solid #91d5ff;
        }

        .status-delivered {
            background: #f6ffed;
            color: #389e0d;
            border: 1px solid #b7eb8f;
        }

        .status-cancelled {
            background: #fff1f0;
            color: #cf1322;
            border: 1px solid #ffa39e;
        }

        .order-item-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .order-item-list {
            display: flex;
            gap: 10px;
            padding: 15px 0;
        }

        .btn-action {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 20px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .empty-orders {
            padding: 80px 0;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="fw-black mb-1" style="font-size: 2.5rem;">طلباتي</h1>
                <p class="text-muted mb-0">تابع حالات طلباتك وتاريخ مشترياتك بكل سهولة</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none text-muted">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('profile.index') }}"
                            class="text-decoration-none text-muted">الملف الشخصي</a></li>
                    <li class="breadcrumb-item active fw-bold text-dark">قائمة الطلبات</li>
                </ol>
            </nav>
        </div>

        @forelse($orders ?? [] as $order)
            <div class="card order-card shadow-sm rounded-4 mb-4">
                <div class="card-header order-header p-3 p-md-4">
                    <div class="row align-items-center g-3">
                        <div class="col-6 col-md-3">
                            <div class="small text-muted mb-1 text-uppercase fw-bold"
                                style="font-size: 0.65rem; letter-spacing: 1px;">رقم الطلب</div>
                            <div class="fw-black text-dark">#{{ data_get($order, 'id') }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small text-muted mb-1 text-uppercase fw-bold"
                                style="font-size: 0.65rem; letter-spacing: 1px;">تاريخ الطلب</div>
                            <div class="fw-bold text-dark">
                                {{ \Carbon\Carbon::parse(data_get($order, 'created_at'))->format('Y-m-d') }}
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small text-muted mb-1 text-uppercase fw-bold"
                                style="font-size: 0.65rem; letter-spacing: 1px;">إجمالي المبلغ</div>
                            <div class="fw-black text-gold">
                                {{ number_format((data_get($order, 'total_price') ?: data_get($order, 'total', 0)) * session('currency_rate', 1), 2) }}
                                <small>{{ session('currency_symbol', 'SAR') }}</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-md-end">
                            <span class="status-badge status-{{ strtolower(data_get($order, 'status', 'pending')) }}">
                                {{ strtoupper(data_get($order, 'status', 'pending')) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                        <div class="order-item-list">
                            @php
                                // Check if items is available as a collection or needs parsing
                                $items = is_string(data_get($order, 'items'))
                                    ? json_decode(data_get($order, 'items'))
                                    : data_get($order, 'items');
                                $count = count((array) $items);
                            @endphp

                            @foreach (collect($items)->take(4) as $index => $item)
                                @php
                                    $itemImage = data_get($item, 'image');
                                    if ($itemImage && !str_starts_with($itemImage, 'http') && !str_starts_with($itemImage, '/')) {
                                        $itemImage = '/storage/' . $itemImage;
                                    }
                                    $itemImage = $itemImage ?? '/assets/img/placeholder.svg';
                                @endphp
                                <img src="{{ $itemImage }}" alt="item"
                                    class="order-item-thumb shadow-sm"
                                    onerror="this.src='/assets/img/placeholder.svg'">
                            @endforeach

                            @if ($count > 4)
                                <div
                                    class="order-item-thumb bg-light d-flex align-items-center justify-content-center text-muted fw-bold">
                                    +{{ $count - 4 }}
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0 justify-content-end">
                            <a href="{{ route('orders.show', data_get($order, 'id')) }}"
                                class="btn btn-dark btn-action shadow-sm">
                                <i class="bi bi-eye me-1"></i> تفاصيل الطلب
                            </a>
                            <a href="{{ route('orders.invoice', data_get($order, 'id')) }}"
                                class="btn btn-outline-dark btn-action border-light-subtle shadow-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i> الفاتورة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-orders text-center">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 130px; height: 130px;">
                        <i class="bi bi-box-seam text-muted" style="font-size: 3.5rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-3">ليس لديك طلبات سابقة</h3>
                <p class="text-muted mb-4 opacity-75">ابدأ التسوق الآن واملأ خزانة ملابسك بأحدث صيحات الموضة.</p>
                <a href="{{ route('products.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-sm">
                    تسوّق الآن <i class="bi bi-bag-plus ms-2"></i>
                </a>
            </div>
        @endforelse
    </div>
@endsection
