@extends('layouts.app')

@section('title', __('messages.privacy_policy'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-5">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                class="text-decoration-none text-muted">@lang('messages.home')</a></li>
                        <li class="breadcrumb-item active fw-bold text-dark">@lang('messages.privacy_policy')</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white p-5 text-center position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-100"
                            style="background: radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.1) 0%, transparent 50%);">
                        </div>
                        <h1 class="fw-black mb-3">@lang('messages.privacy_policy')</h1>
                        <p class="mb-0 opacity-75">آخر تحديث: {{ date('Y-m-d') }}</p>
                    </div>
                    <div class="card-body p-5">
                        <div class="privacy-content" style="line-height: 2;">
                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-shield-check me-2"></i> 1. جمع المعلومات
                                </h4>
                                <p class="text-muted">نحن نجمع المعلومات التي تقدمها لنا مباشرة عند إنشاء حساب، أو تقديم
                                    طلب، أو الاشتراك في نشرتنا الإخبارية. قد تشمل هذه المعلومات اسمك، وعنوان بريدك
                                    الإلكتروني، وعنوان الشحن، ورقم الهاتف، وتفاصيل الدفع.</p>
                            </section>

                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-eye me-2"></i> 2. كيف نستخدم معلوماتك
                                </h4>
                                <p class="text-muted">نحن نستخدم المعلومات التي نجمعها لتقديم طلباتك ومعالجتها، وللتواصل معك
                                    بشأن طلبك، ولأغراض التسويق (إذا وافقت على ذلك)، ولتحسين خدماتنا وتجربة المستخدم الخاصة
                                    بك.</p>
                            </section>

                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-lock me-2"></i> 3. حماية البيانات</h4>
                                <p class="text-muted">نحن نتخذ إجراءات أمنية صارمة لحماية معلوماتك الشخصية من الوصول غير
                                    المصرح به أو التغيير أو الإفصاح أو الإتلاف. يتم تشفير جميع تفاصيل الدفع باستخدام
                                    بروتوكولات الأمان القياسية (SSL).</p>
                            </section>

                            <section class="mb-5">
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-people me-2"></i> 4. المشاركة مع أطراف
                                    ثالثة</h4>
                                <p class="text-muted">نحن لا نبيع أو نؤجر معلوماتك الشخصية لأطراف ثالثة. قد نشارك معلوماتك
                                    مع شركاء الخدمة الموثوق بهم الذين يساعدوننا في تشغيل موقعنا، أو إجراء أعمالنا، أو خدمتك،
                                    طالما وافقت تلك الأطراف على الحفاظ على سرية هذه المعلومات.</p>
                            </section>

                            <section>
                                <h4 class="fw-bold mb-4 text-gold"><i class="bi bi-chat-dots me-2"></i> 5. اتصل بنا</h4>
                                <p class="text-muted">إذا كانت لديك أي أسئلة حول سياسة الخصوصية هذه، يرجى الاتصال بنا عبر
                                    صفحة <a href="{{ route('pages.contact') }}"
                                        class="text-gold fw-bold text-decoration-none">اتصل بنا</a>.</p>
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

        .card {
            transition: transform 0.3s ease;
        }

        section h4 {
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 15px;
        }
    </style>
@endpush
