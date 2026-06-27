@php
    // جلب اللغات المفعلة من قاعدة البيانات
    $languages = \App\Models\Language::where('status', 1)->get();
    // جلب العملات المفعلة
    $currencies = \App\Models\Currency::where('status', 1)->get();
    // تحديد اللغة الحالية
    $currentLocale = app()->getLocale();
    $currentLang = $languages->where('code', $currentLocale)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $currentLang->direction ?? 'rtl' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Elegance Fashion'))</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon_io/apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon_io/favicon.ico') }}">
    @if (($currentLang->direction ?? 'rtl') == 'rtl')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @endif
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Critical CSS: Bootstrap and Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons and Font Awesome (deferred loading) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </noscript>
    <!-- Custom CSS -->
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">

    <link rel="stylesheet" href="/assets/css/style.css">
    @stack('css')
</head>

<body class="bg-light text-dark">
    <!-- Modern Vogue Marketing Banner -->
    <div class="marketing-banner-animated py-2 overflow-hidden" style="height: 45px;">
        <div class="marquee-wrapper d-flex align-items-center h-100 container-fluid">
            <div class="marquee-content d-flex align-items-center" id="offers-ticker">
                <div class="d-inline-flex align-items-center mx-4">
                    <span class="badge bg-gold text-dark me-2 px-2 py-1 small fw-black shadow-sm"
                        style="font-size: 0.65rem;">@lang('messages.limited')</span>
                    <span class="fw-medium">
                        {{ __('messages.marketing_offer') ?? '✨ عروض حصرية: تميزي بأرقى التصاميم لهذا الموسم ✨' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="min-vh-100 d-flex flex-column">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm"
            style="z-index: 1050; min-height: 80px;">
            <div class="container-fluid px-lg-5">
                <!-- 1. Logo (Left) -->
                <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('home') }}"
                    style="letter-spacing: 1.5px;">
                    <span class="text-dark">ELEGANCE</span><span class="text-gold ms-1">FASHION</span>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navContent">
                    <i class="bi bi-list fs-1"></i>
                </button>

                <!-- 2. Navigation Links (Center) -->
                <div class="collapse navbar-collapse justify-content-center" id="navContent">
                    <ul class="navbar-nav mb-2 mb-lg-0 fw-bold gap-lg-4 text-uppercase" style="font-size: 0.85rem;">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active fw-black' : '' }}"
                                href="{{ route('home') }}"
                                title="{{ __('messages.home') }}">{{ __('messages.home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active fw-black' : '' }}"
                                href="{{ route('products.index') }}"
                                title="{{ __('messages.products') }}">{{ __('messages.products') }}</a>
                        </li>
                        @foreach (\App\Models\Category::take(3)->get() as $navCat)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('categories.show', $navCat->id) }}"
                                    title="{{ $navCat->name }}">{{ $navCat->name }}</a>
                            </li>
                        @endforeach
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('offers.index') ? 'active fw-black' : '' }}"
                                href="{{ route('offers.index') }}"
                                title="{{ __('messages.offers') }}">{{ __('messages.offers') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- 3. Icons (Right) -->
                <div class="d-flex align-items-center gap-4">
                    <!-- Language Selector -->
                    <div class="dropdown d-none d-md-block">
                        <button
                            class="btn btn-link text-dark p-0 border-0 shadow-none text-decoration-none dropdown-toggle no-caret fw-bold small"
                            type="button" data-bs-toggle="dropdown"
                            title="{{ __('messages.change_language') ?? 'تغير اللغة' }}">
                            <i class="bi bi-globe2 me-1"></i>
                            {{ strtoupper($selectedLanguage->code ?? app()->getLocale()) }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            @foreach ($languages ?? [] as $lang)
                                <li>
                                    <a class="dropdown-item py-2 {{ app()->getLocale() == $lang->code ? 'bg-light fw-bold' : '' }}"
                                        href="{{ route('language.set', $lang->code) }}"
                                        title="{{ $lang->name }}">
                                        {{ $lang->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Currency Selector -->
                    <div class="dropdown d-none d-md-block">
                        <button
                            class="btn btn-link text-dark p-0 border-0 shadow-none text-decoration-none dropdown-toggle no-caret fw-bold small"
                            type="button" data-bs-toggle="dropdown"
                            title="{{ __('messages.currency') ?? 'العملة' }}">
                            {{ $selectedCurrency->code ?? 'SAR' }} ({{ $selectedCurrency->symbol ?? 'ر.س' }})
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            @foreach ($currencies ?? [] as $curr)
                                <li>
                                    <a class="dropdown-item py-2 {{ session('currency') == $curr->code ? 'bg-light fw-bold' : '' }}"
                                        href="{{ route('currency.set', $curr->code) }}"
                                        title="{{ $curr->code }} ({{ $curr->symbol }})">
                                        {{ $curr->code }} ({{ $curr->symbol }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Search Icon -->
                    <button class="btn btn-link text-dark p-0 border-0 shadow-none d-none d-sm-block" type="button"
                        data-bs-toggle="collapse" data-bs-target="#searchPanel"
                        title="{{ __('messages.search') ?? 'بحث' }}">
                        <i class="bi bi-search fs-5"></i>
                    </button>

                    <!-- User Icon -->
                    @auth
                        @php
                            $sessionUser = Session::get('user');
                            $navProfileImage = data_get($sessionUser, 'profile_image') ?? auth()->user()->profile_image ?? null;
                            if ($navProfileImage && !str_starts_with($navProfileImage, 'http') && !str_starts_with($navProfileImage, '/')) {
                                $navProfileImage = '/storage/' . $navProfileImage;
                            }
                        @endphp
                        <div class="dropdown">
                            <a href="#" class="text-dark p-0 text-decoration-none d-flex align-items-center"
                                data-bs-toggle="dropdown"
                                title="{{ __('messages.my_account') ?? 'حسابي' }}">
                                @if ($navProfileImage)
                                    <img src="{{ $navProfileImage }}" alt="Profile"
                                        class="rounded-circle shadow-sm border"
                                        style="width: 32px; height: 32px; object-fit: cover;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                    <i class="bi bi-person-circle fs-4 d-none"></i>
                                @else
                                    <i class="bi bi-person-circle fs-4"></i>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 rounded-4 px-2 py-3 user-dropdown-menu"
                                style="min-width: 220px; z-index: 1100;">
                                <li>
                                    <h6 class="dropdown-header text-muted small fw-bold mb-2">@lang('messages.my_account')</h6>
                                </li>
                                <li><a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-2"
                                        href="{{ route('profile.index') }}"
                                        title="{{ __('messages.profile') }}"><i class="bi bi-person fs-5"></i>
                                        @lang('messages.profile')</a></li>
                                <li><a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-2"
                                        href="{{ route('wishlist.index') }}"
                                        title="{{ __('messages.wishlist') }}"><i class="bi bi-heart fs-5"></i>
                                        @lang('messages.wishlist')</a></li>
                                <li><a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-2"
                                        href="{{ route('orders.index') }}"
                                        title="{{ __('messages.my_orders') }}"><i class="bi bi-box fs-5"></i>
                                        @lang('messages.my_orders')</a></li>

                                <li>
                                    <hr class="dropdown-divider mx-2">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button
                                            class="dropdown-item text-danger py-2 px-3 rounded-3 d-flex align-items-center gap-2 w-100"
                                            type="submit">
                                            <i class="bi bi-box-arrow-right fs-5"></i> @lang('messages.logout')
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-dark p-0 text-decoration-none"
                            title="{{ __('messages.login') ?? 'تسجيل الدخول' }}">
                            <i class="bi bi-person fs-4"></i>
                        </a>
                    @endauth

                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="text-dark p-0 text-decoration-none position-relative"
                        title="{{ __('messages.cart') ?? 'السلة' }}">
                        <i class="bi bi-bag fs-4"></i>
                        <span id="cartBadge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-dark p-1 {{ \Session::has('cart') && count(\Session::get('cart')) > 0 ? '' : 'd-none' }}"
                            style="font-size: 0.5rem; width: 8px; height: 8px;"></span>
                    </a>
                </div>
            </div>

            <!-- Collapsible Search Panel -->
            <div class="collapse w-100 position-absolute top-100 start-0 bg-white border-bottom p-4 shadow-lg"
                id="searchPanel" style="z-index: 1050;">
                <div class="container">
                    <form action="{{ route('products.search') }}" method="GET" class="position-relative mx-auto"
                        style="max-width: 600px;">
                        <input type="text" name="q" autocomplete="off" id="searchInput"
                            class="form-control border-0 border-bottom rounded-0 fs-3 text-center py-2"
                            placeholder="ابحث عن منتجاتك..." style="box-shadow: none !important;">
                        <button
                            class="btn btn-dark rounded-circle position-absolute top-50 end-0 translate-middle-y me-2"
                            type="submit" style="width: 40px; height: 40px; transform: scale(0.8);"><i
                                class="bi bi-search"></i></button>
                    </form>
                    <div id="liveSearchResults" class="mt-4 mx-auto" style="max-width: 700px;"></div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content flex-grow-1 py-4">
            <!-- Alerts handled by SweetAlert2 -->


            <!-- Content -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row g-5">
                    <!-- Brand Section -->
                    <div class="col-lg-4 col-md-6">
                        <a class="navbar-brand fw-bold mb-4" href="{{ route('home') }}"
                            style="letter-spacing: 1.5px;">
                            <span class="text-white">ELEGANCE</span><span class="text-gold ms-1">FASHION</span>
                        </a>
                        <p class="small text-muted mb-4 pe-lg-5" style="line-height: 1.8;">
                            {{ __('messages.about_store_desc') }}
                        </p>
                        <div class="d-flex gap-2">
                            <a href="#" class="social-icon-box">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="social-icon-box">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="social-icon-box">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="#" class="social-icon-box">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 col-6">
                        <h5 class="text-uppercase">{{ __('messages.quick_links') }}</h5>
                        <div class="d-flex flex-column mt-2">
                            <a href="{{ route('home') }}" class="footer-link"><i
                                    class="bi bi-chevron-left small"></i>{{ __('messages.home') }}</a>
                            <a href="{{ route('products.index') }}" class="footer-link"><i
                                    class="bi bi-chevron-left small"></i>{{ __('messages.products') }}</a>
                            <a href="{{ route('offers.index') }}" class="footer-link"><i
                                    class="bi bi-chevron-left small"></i>{{ __('messages.offers') }}</a>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="col-lg-2 col-md-6 col-6">
                        <h5 class="text-uppercase">{{ __('messages.services') }}</h5>
                        <div class="d-flex flex-column mt-2">
                            <a href="{{ route('pages.privacy') }}" class="footer-link"><i
                                    class="bi bi-chevron-left small"></i>{{ __('messages.privacy_policy') }}</a>
                            <a href="{{ route('pages.terms') }}" class="footer-link"><i
                                    class="bi bi-chevron-left small"></i>{{ __('messages.terms_of_use') }}</a>
                            <a href="{{ route('pages.contact') }}" class="footer-link"><i
                                    class="bi bi-chevron-left small"></i>{{ __('messages.contact_us') }}</a>
                        </div>
                    </div>

                    <!-- Newsletter -->
                    <div class="col-lg-4 col-md-6">
                        <h5 class="text-uppercase">اشترك في النشرة البريدية</h5>
                        <p class="small text-muted mb-4">كن أول من يعرف عن أحدث العروض والمنتجات الحصرية.</p>
                        <form class="footer-newsletter">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="بريدك الإلكتروني">
                                <button class="btn btn-gold" type="button">اشتراك</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div
                    class="footer-copyright d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <p class="mb-3 mb-md-0">
                        &copy; {{ date('Y') }} <span class="navbar-brand fs-6 p-0 m-0"><span
                                class="text-gold fw-bold">ELEGANCE</span> <span
                                class="text-gold fw-bold">FASHION</span></span>. جميع الحقوق محفوظة.
                    </p>
                    <div class="d-flex gap-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg"
                            height="15" alt="Visa" style="filter: grayscale(1) brightness(2);">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                            height="20" alt="Mastercard" style="filter: grayscale(1) brightness(2);">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" height="18"
                            alt="PayPal" style="filter: grayscale(1) brightness(2);">
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS (deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

    <!-- jQuery (deferred) -->
    <script defer src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            // Modern SweetAlert notifications
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if ($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: "{{ $errors->first() }}"
                });
            @endif

            // CSRF token for AJAX requests
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Simple and robust sticky navbar
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 150) {
                    navbar.classList.add('sticky-navbar', 'shadow-sm');
                } else {
                    navbar.classList.remove('sticky-navbar', 'shadow-sm');
                }
            });

            // Fetch Offers Ticker
            fetch("{{ route('api.offers.ticker') }}")
                .then(response => response.json())
                .then(result => {
                    if (result.status && result.data && result.data.length > 0) {
                        const tickerElement = document.getElementById('offers-ticker');

                        // Create structured items for each offer
                        const items = result.data.map(text => `
                            <div class="d-inline-flex align-items-center mx-5">
                                <span class="badge bg-gold text-dark me-2 px-2 py-1 small fw-black shadow-sm" style="font-size: 0.65rem;">@lang('messages.limited')</span>
                                <span class="fw-medium">${text}</span>
                            </div>
                        `).join('');

                        tickerElement.innerHTML = items;
                    }
                })
                .catch(error => console.error('Error fetching ticker:', error));

            // Live Search Implementation
            const searchInput = document.getElementById('searchInput');
            const resultsContainer = document.getElementById('liveSearchResults');
            let debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length === 0) {
                    resultsContainer.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('products.search') }}?q=${encodeURIComponent(query)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(products => {
                            if (products.length > 0) {
                                resultsContainer.innerHTML = `
                                <div class="d-flex flex-column gap-2">
                                    ${products.map(product => `
                                                                    <a href="/products/${product.id}" class="text-decoration-none text-dark w-100">
                                                                        <div class="card border-0 bg-light rounded-3 search-item-card transition">
                                                                            <div class="card-body p-2 d-flex align-items-center gap-3">
                                                                                <img src="${product.image_url || '/assets/img/placeholder.svg'}"
                                                                                     class="rounded-2 object-fit-cover"
                                                                                     style="width: 55px; height: 55px;">
                                                                                <div class="flex-grow-1 overflow-hidden">
                                                                                    <h6 class="mb-0 text-truncate small fw-bold text-dark">${product.name}</h6>
                                                                                    <span class="text-gold small fw-bold">${product.price_formatted || (product.price + ' SAR')}</span>
                                                                                </div>
                                                                                <i class="bi bi-chevron-left text-muted opacity-50 small ms-auto"></i>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                `).join('')}
                                </div>
                            `;
                            } else {
                                resultsContainer.innerHTML =
                                    '<div class="text-center text-muted p-4">لا توجد نتائج تطابق بحثك</div>';
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            });

            // Clear search when panel hides
            const searchPanel = document.getElementById('searchPanel');
            searchPanel.addEventListener('hidden.bs.collapse', function() {
                searchInput.value = '';
                resultsContainer.innerHTML = '';
            });
        });
    </script>

    @stack('js')
</body>

</html>
