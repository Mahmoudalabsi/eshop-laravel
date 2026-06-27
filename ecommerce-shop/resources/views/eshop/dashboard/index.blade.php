@extends('eshop.layouts.admin')
@push('css')
    @include('assets.css.style')
@endpush

@section("title","الرئيسية")

@section('content')
    <div class="pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2">نظرة عامة على المتجر</h1>
    </div>

    <div class="row text-end">
        {{-- بطاقة الأقسام --}}
        <div class="col-md-3 mb-4">
            <div class="card bg-soft-blue shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">إجمالي الأقسام</h6>
                            <h2 class="mb-0 fw-bold counter" data-target="{{ $categoriesCount }}">0</h2>
                        </div>
                        <i class="bi bi-tags-fill fs-1 opacity-25"></i>
                    </div>
                    <a href="{{ route('categories.index') }}"
                        class="text-reset text-decoration-none mt-3 d-block small opacity-75">مشاهدة التفاصيل ←</a>
                </div>
            </div>
        </div>

        {{-- بطاقة المنتجات --}}
        <div class="col-md-3 mb-4">
            <div class="card bg-soft-green shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">إجمالي المنتجات</h6>
                            <h2 class="mb-0 fw-bold counter" data-target="{{ $productsCount }}">0</h2>
                        </div>
                        <i class="bi bi-box-seam fs-1 opacity-25"></i>
                    </div>
                    <a href="{{ route('products.index') }}"
                        class="text-reset text-decoration-none mt-3 d-block small opacity-75">مشاهدة التفاصيل ←</a>
                </div>
            </div>
        </div>

        {{-- بطاقة المستخدمين --}}
        <div class="col-md-3 mb-4">
            <div class="card bg-soft-orange shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">الأعضاء</h6>
                            <h2 class="mb-0 fw-bold counter" data-target="{{ $usersCount }}">0</h2>
                        </div>
                        <i class="bi bi-people-fill fs-1 opacity-25"></i>
                    </div>
                    <a href="{{ route('users.index') }}"
                        class="text-reset text-decoration-none mt-3 d-block small opacity-75">إدارة الأعضاء ←</a>
                </div>
            </div>
        </div>

        {{-- بطاقة إجمالي الطلبات --}}
        <div class="col-md-3 mb-4">
            <div class="card bg-soft-cyan shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">إجمالي الطلبات</h6>
                            <h2 class="mb-0 fw-bold counter" data-target="{{ $ordersCount }}">0</h2>
                        </div>
                        <i class="bi bi-cart-check-fill fs-1 opacity-25"></i>
                    </div>
                    <a href="{{ route('orders.index') }}"
                        class="text-reset text-decoration-none mt-3 d-block small opacity-75">مشاهدة الطلبات ←</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 text-end">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center py-3">
                    <h4>آخر الطلبات المستلمة</h4>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary px-3">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody id="latestOrdersTableBody">
                                <tr>
                                    <td colspan="5" class="py-4 text-center">
                                        <div class="spinner-border spinner-border-sm text-primary"></div> جاري تحميل أحدث
                                        الطلبات...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">توزيع المنتجات على الأقسام</div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-secondary">إحصائيات المبيعات والنشاط</h5>
                </div>
                <div class="card-body">
                    <canvas id="mainDashboardChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row mt-4">
        <div class="col-lg-4 mb-lg-0 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="fw-bold mb-0 text-secondary">توزيع المنتجات على الأقسام</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="fw-bold mb-0 text-secondary">إحصائيات المبيعات والنشاط</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold text-muted">نشاط الطلبات</span> خلال الفترة الأخيرة
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="mainDashboardChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- مودال الترحيب --}}
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">مرحباً بك مجدداً!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <i class="bi bi-person-circle fs-1 text-primary mb-3 d-block"></i>
                    <h4>أهلاً بك، {{ auth()->user()->name }}</h4>
                    <p class="text-muted">يسعدنا رؤيتك اليوم في لوحة تحكم متجرك.</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal">ابدأ العمل</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('assets.js.dashboard')
@endpush
