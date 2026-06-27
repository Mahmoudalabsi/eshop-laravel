@extends('layouts.app')

@section('title', __('messages.contact_us'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-5">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                class="text-decoration-none text-muted">@lang('messages.home')</a></li>
                        <li class="breadcrumb-item active fw-bold text-dark">@lang('messages.contact_us')</li>
                    </ol>
                </nav>

                <div class="row g-4">
                    <!-- Contact Info Cards -->
                    <div class="col-lg-4">
                        <div class="contact-info-card h-100">
                            <!-- Decorative background icon -->
                            <div class="position-absolute top-0 end-0 p-4 opacity-10">
                                <i class="bi bi-envelope-paper" style="font-size: 8rem;"></i>
                            </div>

                            <h3 class="fw-black mb-4">@lang('messages.contact_us')</h3>
                            <p class="text-muted small mb-5">نحن هنا للإجابة على جميع استفساراتكم ومساعدتكم في اختيار
                                الأفضل.</p>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">@lang('messages.location')</small>
                                    <span class="fw-bold">الرياض، المملكة العربية السعودية</span>
                                </div>
                            </div>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">@lang('messages.phone')</small>
                                    <span class="fw-bold" dir="ltr">+966 500 000 000</span>
                                </div>
                            </div>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">@lang('messages.email')</small>
                                    <span class="fw-bold">support@elegance-fashion.com</span>
                                </div>
                            </div>

                            <div class="mt-5 pt-4">
                                <h6 class="text-uppercase small fw-bold text-muted mb-4">@lang('messages.follow_us')</h6>
                                <div class="d-flex gap-3">
                                    <a href="#" class="social-icon-box bg-white bg-opacity-10 border-0"><i
                                            class="bi bi-facebook"></i></a>
                                    <a href="#" class="social-icon-box bg-white bg-opacity-10 border-0"><i
                                            class="bi bi-instagram"></i></a>
                                    <a href="#" class="social-icon-box bg-white bg-opacity-10 border-0"><i
                                            class="bi bi-twitter-x"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="col-lg-8">
                        <div class="contact-form-card">
                            <h2 class="fw-black mb-2 text-dark">أرسل لنا رسالة</h2>
                            <p class="text-muted mb-5">يرجى ملء النموذج أدناه وسيقوم فريقنا بالرد عليك في أقرب وقت ممكن.</p>

                            <form action="{{ route('pages.contact.submit') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label
                                            class="form-label small fw-bold text-uppercase text-muted">@lang('messages.name')</label>
                                        <input type="text" name="name" class="form-control" placeholder="أحمد علي"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            class="form-label small fw-bold text-uppercase text-muted">@lang('messages.email')</label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="email@example.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase text-muted">الموضوع</label>
                                        <input type="text" name="subject" class="form-control" placeholder="بخصوص..."
                                            required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase text-muted">الرسالة</label>
                                        <textarea name="message" rows="5" class="form-control" placeholder="اكتب رسالتك بالتفصيل هنا..." required></textarea>
                                    </div>
                                    <div class="col-12 mt-5">
                                        <button type="submit"
                                            class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-sm">
                                            إرسال الرسالة <i class="bi bi-send ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
