@extends('layouts.app')

@section('title', 'لوحة التحكم - ' . config('app.name'))

@push('css')
<style>
    .admin-stat-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .admin-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
        border-color: rgba(197, 160, 89, 0.3);
    }
    .admin-stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #c5a059, #8e6d2f);
    }
    .admin-stat-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(197,160,89,0.12), rgba(197,160,89,0.04));
        color: #c5a059;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .admin-stat-value {
        font-size: 1.9rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }
    .admin-stat-label {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .admin-section-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #f1f5f9;
        padding: 1.5rem;
        height: 100%;
    }
    .admin-section-card h5 {
        color: #0f172a;
        font-weight: 800;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-row-item {
        padding: 0.6rem 0;
        border-bottom: 1px dashed #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
    }
    .admin-row-item:last-child { border-bottom: none; }
    .admin-quick-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        border-radius: 0.75rem;
        background: #f8fafc;
        color: #0f172a;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .admin-quick-link:hover {
        background: #0f172a;
        color: #fff;
        border-color: #c5a059;
    }
    .admin-quick-link i {
        color: #c5a059;
        font-size: 1.2rem;
    }
    .admin-quick-link:hover i { color: #fff; }
    .admin-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-processing { background: #dbeafe; color: #1e40af; }
    .badge-delivered { background: #d1fae5; color: #065f46; }
    .badge-completed { background: #e0e7ff; color: #3730a3; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 2rem; color:#0f172a;">
                <i class="bi bi-speedometer2 me-2 text-gold"></i> لوحة التحكم
            </h1>
            <p class="text-muted mb-0">مرحباً {{ auth()->user()->name }} — نظرة عامة على المتجر</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('home') }}" class="btn btn-outline-dark rounded-pill px-4">
                <i class="bi bi-shop me-1"></i> عرض المتجر
            </a>
            <a href="{{ route('logout') }}" class="btn btn-outline-danger rounded-pill px-4" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-1"></i> خروج
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="admin-stat-icon"><i class="bi bi-people-fill"></i></div>
                    <span class="admin-badge badge-paid">+{{ $stats['customers'] }}</span>
                </div>
                <div class="admin-stat-value">{{ $stats['users'] }}</div>
                <div class="admin-stat-label mt-1">المستخدمون</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="admin-stat-icon"><i class="bi bi-bag-check-fill"></i></div>
                </div>
                <div class="admin-stat-value">{{ $stats['products'] }}</div>
                <div class="admin-stat-label mt-1">المنتجات</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="admin-stat-icon"><i class="bi bi-receipt"></i></div>
                </div>
                <div class="admin-stat-value">{{ $stats['orders'] }}</div>
                <div class="admin-stat-label mt-1">الطلبات</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="admin-stat-icon"><i class="bi bi-tags-fill"></i></div>
                </div>
                <div class="admin-stat-value">{{ $stats['categories'] }}</div>
                <div class="admin-stat-label mt-1">الأقسام</div>
            </div>
        </div>
    </div>

    <!-- Secondary stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="admin-section-card text-center">
                <div class="admin-stat-value text-warning">{{ $stats['offers'] }}</div>
                <div class="admin-stat-label mt-1">العروض النشطة</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="admin-section-card text-center">
                <div class="admin-stat-value text-info">{{ $stats['reviews'] }}</div>
                <div class="admin-stat-label mt-1">التقييمات</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="admin-section-card text-center">
                <div class="admin-stat-value text-success">{{ $stats['currencies'] }}</div>
                <div class="admin-stat-label mt-1">العملات</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="admin-section-card text-center">
                <div class="admin-stat-value" style="color:#a855f7;">{{ $stats['languages'] }}</div>
                <div class="admin-stat-label mt-1">اللغات</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Recent Orders -->
        <div class="col-lg-6">
            <div class="admin-section-card">
                <h5><i class="bi bi-clock-history me-2 text-gold"></i>أحدث الطلبات</h5>
                @forelse ($recentOrders as $order)
                    <div class="admin-row-item">
                        <div>
                            <div class="fw-bold">{{ $order->customer_name }}</div>
                            <small class="text-muted">{{ $order->order_number }} • {{ $order->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ number_format($order->total) }} ر.س</div>
                            <span class="admin-badge badge-{{ $order->status }}">{{ $order->status }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3 mb-0">لا توجد طلبات بعد</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-lg-6">
            <div class="admin-section-card">
                <h5><i class="bi bi-box-seam me-2 text-gold"></i>أحدث المنتجات</h5>
                @forelse ($recentProducts as $product)
                    <div class="admin-row-item">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $product->image ?? asset('assets/img/placeholder.svg') }}" alt="" style="width:36px; height:36px; border-radius:8px; object-fit:cover;">
                            <div>
                                <div class="fw-bold text-truncate" style="max-width:200px;">{{ $product->name }}</div>
                                <small class="text-muted">{{ data_get($product, 'subcategory.category.name', '—') }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-gold">{{ number_format($product->price) }} ر.س</div>
                            <small class="text-muted">مخزون: {{ $product->total_stock }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3 mb-0">لا توجد منتجات بعد</p>
                @endforelse
            </div>
        </div>
    </div>

    @if ($lowStockProducts->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="admin-section-card">
                <h5 class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>منتجات بمخزون منخفض</h5>
                <div class="row g-2">
                    @foreach ($lowStockProducts as $p)
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#fef2f2;">
                                <img src="{{ $p->image ?? asset('assets/img/placeholder.svg') }}" style="width:32px; height:32px; border-radius:6px; object-fit:cover;" alt="">
                                <div class="flex-grow-1">
                                    <div class="fw-bold small text-truncate">{{ $p->name }}</div>
                                    <small class="text-danger">باقٍ: {{ $p->total_stock }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Links -->
    <div class="row g-3">
        <div class="col-12">
            <h4 class="fw-bold mb-3" style="color:#0f172a;">روابط سريعة</h4>
            <div class="row g-2">
                <div class="col-md-3 col-6">
                    <a href="{{ route('admin.categories.index') }}" class="admin-quick-link">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <span>إدارة الأقسام</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('admin.languages.index') }}" class="admin-quick-link">
                        <i class="bi bi-translate"></i>
                        <span>إدارة اللغات</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('admin.size-guides.index') }}" class="admin-quick-link">
                        <i class="bi bi-rulers"></i>
                        <span>أدلة المقاسات</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('products.index') }}" class="admin-quick-link">
                        <i class="bi bi-bag-fill"></i>
                        <span>تصفح المنتجات</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
