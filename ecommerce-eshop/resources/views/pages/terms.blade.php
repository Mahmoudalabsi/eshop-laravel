@extends('layouts.app')

@section('title', __('messages.terms_of_use'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-5">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                class="text-decoration-none text-muted">@lang('messages.home')</a></li>
                        <li class="breadcrumb-item active fw-bold text-dark">@lang('messages.terms_of_use')</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white p-5 text-center position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-100"
                            style="background: radial-gradient(circle at 90% 10%, rgba(212, 175, 55, 0.1) 0%, transparent 50%);">
                        </div>
                        <h1 class="fw-black mb-3">@lang('messages.terms_of_use')</h1>
                        <p class="mb-0 opacity-75">الموافقة على الشروط لاستخدام خدماتنا</p>
                    </div>
                    <div class="card-body p-5">
                        <div class="terms-content" style="line-height: 2;">
                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-file-text me-2"></i> 1. قبول الشروط</h4>
                                <p class="text-muted">باستخدامك لموقع Elegance Fashion، فإنك توافق على الالتزام بشروط
                                    الاستخدام هذه. إذا كنت لا توافق على أي جزء من هذه الشروط، فيرجى عدم استخدام الموقع.</p>
                            </section>

                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-person-check me-2"></i> 2. حساب المستخدم
                                </h4>
                                <p class="text-muted">عند إنشاء حساب لدينا، يجب عليك تزويدنا بمعلومات دقيقة وكاملة. أنت
                                    مسؤول عن الحفاظ على سرية حسابك وكلمة المرور الخاصة بك.</p>
                            </section>

                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-cart-check me-2"></i> 3. الطلبات والأسعار
                                </h4>
                                <p class="text-muted">نحن نحتفظ بالحق في رفض أو إلغاء أي طلب لأي سبب. أسعار منتجاتنا عرضة
                                    للتغيير دون إشعار مسبق. نسعى دائماً لضمان دقة الأسعار والأوصاف.</p>
                            </section>

                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-truck me-2"></i> 4. الشحن والإرجاع</h4>
                                <p class="text-muted">تخضع جميع عمليات الشحن والإرجاع لسياستنا الخاصة. يرجى مراجعة صفحة
                                    الشحن والإرجاع لمزيد من التفاصيل حول الفترات الزمنية والرسوم.</p>
                            </section>

                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-award me-2"></i> 5. الملكية الفكرية</h4>
                                <p class="text-muted">جميع المحتويات الموجودة على هذا الموقع، بما في ذلك النصوص والرسومات
                                    والشعارات والصور، هي ملك لـ Elegance Fashion ومحمية بموجب قوانين الملكية الفكرية.</p>
                            </section>

                            <section>
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-exclamation-triangle me-2"></i> 6. حدود
                                    المسؤولية</h4>
                                <p class="text-muted">لا تتحمل Elegance Fashion أية مسؤولية عن أي أضرار مباشرة أو غير مباشرة
                                    تنشأ عن استخدامك للموقع أو عدم القدرة على استخدامه.</p>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .text-gold {
            color: #d4af37 !important;
        }

        section h4 {
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 15px;
        }
    </style>
@endpush
