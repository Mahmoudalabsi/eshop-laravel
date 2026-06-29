@extends('layouts.app')

@section('content')
    <header class="hero-premium position-relative mb-5 overflow-hidden rounded-5 shadow-2xl">
        <div class="hero-overlay"></div>
        <div class="container position-relative z-3">
            <div class="row align-items-center min-vh-75 py-5">
                <div class="col-lg-6 text-white text-end py-5" data-aos="fade-left">
                    <span class="badge px-3 py-2 rounded-pill fw-bold mb-4 glass-badge">
                        {{ __('messages.winter_collection') }}
                    </span>
                    <h1 class="display-1 fw-black mb-4 main-title">
                        {!! __('messages.hero_title') !!}
                    </h1>
                    <p class="lead mb-5 opacity-75 text-light lh-lg">
                        {{ __('messages.hero_subtitle') }}
                    </p>
                    <div class="d-flex gap-3 justify-content-start flex-row-reverse">
                        <a href="{{ route('products.index') }}"
                            class="btn btn-luxury-primary btn-lg px-5 rounded-pill shadow-lg">
                            اكتشفي المجموعة
                        </a>
                        <a href="{{ route('offers.index') }}" class="btn btn-luxury-outline btn-lg px-5 rounded-pill">
                            آخر العروض
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-image-container">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=800&auto=format&fit=crop"
                            alt="Luxury Fashion" class="img-fluid floating-img shadow-2xl">
                        <div class="experience-card glass-morphism p-4 rounded-4">
                            <h3 class="fw-black mb-0">+10k</h3>
                            <p class="small mb-0 opacity-75">عميلة سعيدة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="categories-section py-5">
        <div class="container text-end">
            <h2 class="mb-4">تسوّقي حسب القسم</h2>
            <div class="row g-4">
                @foreach ($categories->take(4) as $category)
                    <div class="col-md-3 col-6">
                        <a href="{{ route('categories.show', $category->id) }}"
                            class="category-card-link text-decoration-none">
                            <div class="category-card position-relative overflow-hidden rounded-4 shadow-sm h-100">
                                <div class="ratio ratio-1x1">
                                    @php
                                        $homeCatImage = data_get($category, 'image');
                                        if ($homeCatImage && !str_starts_with($homeCatImage, 'http') && !str_starts_with($homeCatImage, '/')) {
                                            $homeCatImage = asset('storage/' . $homeCatImage);
                                        } else {
                                            $homeCatImage = $homeCatImage ?? asset('assets/img/placeholder.svg');
                                        }
                                    @endphp
                                    <img src="{{ $homeCatImage }}"
                                        class="object-fit-cover w-100 h-100 transition-transform"
                                        alt="{{ data_get($category, 'name', 'Category') }}"
                                        onerror="this.src='{{ asset('assets/img/placeholder.svg') }}'">
                                </div>
                                <div class="category-overlay d-flex align-items-end p-3 w-100"
                                    style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                                    <h5 class="text-white fw-bold mb-0">{{ $category->name }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-padding bg-light py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold mb-3">{{ __('messages.best_selling') }}</h2>
                <div class="d-flex justify-content-center">
                    <div class="divider-custom"></div>
                </div>
            </div>

            <div class="row g-4">
                @forelse ($topRated as $p)
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <x-product-card :product="$p" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state-lux">
                            <i class="bi bi-stars fs-1 mb-3 d-block text-gold"></i>
                            <p class="lead fw-bold">{{ __('messages.stay_tuned') }}</p>
                            <span class="text-muted small">{{ __('messages.we_choose_best') }}</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @if (!empty($offers) && count($offers) > 0)
    <section class="py-5 offers-home-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
                <div>
                    <span class="badge bg-gold text-dark mb-2 px-3 py-2 rounded-pill fw-bold">عروض حصرية</span>
                    <h2 class="display-5 fw-bold mb-0">آخر العروض المميزة</h2>
                </div>
                <a href="{{ route('offers.index') }}" class="btn btn-link text-decoration-none text-dark fw-bold">
                    عرض الكل <i class="bi bi-arrow-left small ms-1"></i>
                </a>
            </div>
            <div class="row g-4">
                @foreach ($offers as $p)
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <x-product-card :product="$p" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="feature-card glass-morphism p-4 text-center h-100">
                        <div class="icon-wrap mb-3 mx-auto">🚚</div>
                        <h5 class="fw-bold">{{ __('messages.fast_shipping') }}</h5>
                        <p class="small text-muted mb-0">{{ __('messages.fast_shipping_desc') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card glass-morphism p-4 text-center h-100">
                        <div class="icon-wrap mb-3 mx-auto">💳</div>
                        <h5 class="fw-bold">{{ __('messages.secure_payment') }}</h5>
                        <p class="small text-muted mb-0">{{ __('messages.secure_payment_desc') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card glass-morphism p-4 text-center h-100">
                        <div class="icon-wrap mb-3 mx-auto">✨</div>
                        <h5 class="fw-bold">{{ __('messages.quality_guarantee') }}</h5>
                        <p class="small text-muted mb-0">{{ __('messages.quality_guarantee_desc') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card glass-morphism p-4 text-center h-100">
                        <div class="icon-wrap mb-3 mx-auto">🎧</div>
                        <h5 class="fw-bold">{{ __('messages.support_24_7') }}</h5>
                        <p class="small text-muted mb-0">{{ __('messages.support_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* 1. المتغيرات والأسس (Root & Foundations) */
        :root {
            --lux-black: #0a0a0b;
            --lux-gold: #c5a059;
            --lux-gold-dark: #8e6d2f;
            --lux-gold-light: #ecd08c;
            --accent-gradient: linear-gradient(135deg, var(--lux-gold) 0%, var(--lux-gold-dark) 100%);
            --transition-smooth: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            --shadow-premium: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* 2. قسم الهيرو (Hero Section) */
        .hero-premium {
            background: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            min-height: 85vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(197, 160, 89, 0.1);
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to left,
                    rgba(10, 10, 11, 0.9) 10%,
                    rgba(10, 10, 11, 0.6) 50%,
                    transparent 100%);
            z-index: 1;
        }

        /* 3. العناصر المرئية المتحركة (Visual Effects) */
        .floating-img {
            border-radius: 40px;
            filter: drop-shadow(0 30px 60px rgba(0, 0, 0, 0.4));
            transition: var(--transition-smooth);
        }

        .hero-image-container:hover .floating-img {
            transform: perspective(1000px) rotateY(-5deg) translateY(-10px);
        }

        .experience-card {
            border-left: 4px solid var(--lux-gold);
            animation: float-soft 4s ease-in-out infinite;
        }

        @keyframes float-soft {

            0%,
            100% {
                transform: translateY(0) rotate(3deg);
            }

            50% {
                transform: translateY(-15px) rotate(5deg);
            }
        }

        /* 4. العناوين والنصوص (Typography) */
        .main-title {
            letter-spacing: -2px;
            line-height: 1.1;
            filter: drop-shadow(0 10px 10px rgba(0, 0, 0, 0.3));
        }

        .text-gradient {
            background: linear-gradient(45deg,
                    var(--lux-gold-dark) 25%,
                    var(--lux-gold-light) 50%,
                    var(--lux-gold-dark) 75%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        /* 5. الأزرار (Buttons) */
        .btn-luxury-primary {
            background: var(--accent-gradient);
            border: none;
            padding: 16px 45px;
            position: relative;
            z-index: 2;
            overflow: hidden;
            color: white !important;
            transition: var(--transition-smooth);
        }

        .btn-luxury-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .btn-luxury-primary:hover::before {
            left: 100%;
        }

        .btn-luxury-primary:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 30px rgba(142, 109, 47, 0.4);
        }

        /* 6. كروت الأقسام (Categories) */
        .category-circle-item {
            display: block;
            transition: var(--transition-smooth);
        }

        .category-img-wrap {
            width: 140px;
            height: 140px;
            padding: 8px;
            background: white;
            border-radius: 50%;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(197, 160, 89, 0.1);
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .category-img-wrap img {
            transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .category-circle-item:hover .category-img-wrap {
            transform: translateY(-12px);
            border-color: var(--lux-gold);
            box-shadow: 0 20px 40px rgba(197, 160, 89, 0.25);
        }

        .category-circle-item:hover img {
            transform: scale(1.1);
        }

        /* 7. كروت المميزات (Feature Cards) */
        .feature-card {
            border-radius: 30px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            transition: var(--transition-smooth);
            height: 100%;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-15px);
            border-color: rgba(197, 160, 89, 0.3);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.08);
        }

        .icon-wrap {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(197, 160, 89, 0.1);
            border-radius: 20px;
            margin: 0 auto 20px;
            transition: var(--transition-smooth);
        }

        .feature-card:hover .icon-wrap {
            background: var(--accent-gradient);
            transform: scale(1.1) rotate(10deg);
            color: white;
        }

        /* 8. أدوات عامة (Utilities) */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .view-all-link {
            position: relative;
            padding-bottom: 8px;
            color: var(--lux-black);
            text-decoration: none;
        }

        .view-all-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 40px;
            height: 2px;
            background: var(--accent-gradient);
            transition: var(--transition-smooth);
        }

        .view-all-link:hover::after {
            width: 100%;
        }

        /* 9. الاستجابة (Responsive) */
        @media (max-width: 991.98px) {
            .hero-premium {
                min-height: 60vh;
                text-align: center !important;
            }

            .hero-overlay {
                background: radial-gradient(circle at center, rgba(10, 10, 11, 0.7) 0%, var(--lux-black) 100%);
            }

            .main-title {
                font-size: 2.5rem !important;
            }

            .d-flex.gap-3 {
                justify-content: center !important;
            }

            .category-img-wrap {
                width: 100px;
                height: 100px;
            }
        }
    </style>
@endsection
