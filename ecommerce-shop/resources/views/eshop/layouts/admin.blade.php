<!DOCTYPE html>
<html lang="{{ $selectedLanguage->code ?? app()->getLocale() }}" dir="{{ $selectedLanguage->direction ?? 'rtl' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ app()->getLocale() }}">
    <title>لوحة التحكم | @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon_io/apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon_io/favicon.ico') }}">

    <!-- Preconnect to external domains for faster loading -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Critical CSS: Bootstrap and Icons -->
    @if(($selectedLanguage->direction ?? 'rtl') == 'rtl')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Google Fonts with font-display=swap for better LCP -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Local stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/darkmode.css') }}">

    <!-- Non-critical external CSS loaded async -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" media="print"
        onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    </noscript>

    @stack('css')

    <!-- Modern admin theme - loaded LAST to override legacy inline styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/modern-admin.css') }}">
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <nav class="navbar navbar-dark bg-dark d-md-none shadow-sm">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1 fs-6">✨ Elegance Dash</span>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#sidebarMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>

            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-flex flex-column sidebar collapse shadow">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">✨ Elegance</h4>
                    <button id="theme-toggle" class="btn btn-outline-light btn-sm rounded-circle" type="button" title="تبديل الوضع">
                        <i id="theme-icon" class="bi bi-moon-fill"></i>
                    </button>
                </div>

                @auth
                <div class="sidebar-section-label">الرئيسية</div>
                    <a href="{{ route('admin.dashboard') }}" class="{{ Request::is('admin') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> لوحة التحكم
                    </a>

                <div class="sidebar-section-label">المتجر</div>
                    <a href="{{ route('categories.index') }}"
                        class="{{ Request::is('admin/categories*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> الأقسام الرئيسية
                    </a>
                    <a href="{{ route('subcategories.index') }}"
                        class="{{ Request::is('admin/subcategories*') ? 'active' : '' }}">
                        <i class="bi bi-tag"></i> الأقسام الفرعية
                    </a>
                    <a href="{{ route('products.index') }}" class="{{ Request::is('admin/products*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> المنتجات
                    </a>
                    <a href="{{ route('offers.index') }}" class="{{ Request::is('admin/offers*') ? 'active' : '' }}">
                        <i class="bi bi-percent"></i> العروض
                    </a>
                    <a href="{{ route('orders.index') }}" class="{{ Request::is('admin/orders*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check"></i> الطلبات
                    </a>
                    <a href="{{ route('size-guides.index') }}"
                        class="{{ Request::is('admin/size-guides*') ? 'active' : '' }}">
                        <i class="bi bi-rulers"></i> أدلة المقاسات
                    </a>

                <div class="sidebar-section-label">الإعدادات</div>
                    <a href="{{ route('users.index') }}" class="{{ Request::is('admin/users*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> الأعضاء
                    </a>
                    <a href="{{ route('currencies.index') }}"
                        class="{{ Request::is('admin/currencies*') ? 'active' : '' }}">
                        <i class="bi bi-currency-exchange"></i> العملات
                    </a>
                    <a href="{{ route('languages.index') }}"
                        class="{{ Request::is('admin/languages*') ? 'active' : '' }}">
                        <i class="bi bi-translate"></i> اللغات
                    </a>
                @endauth

                @auth
                    <div class="mt-auto px-3 sidebar-user-block">
                        <div class="sidebar-user-card">
                            <div class="sidebar-user-avatar">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="sidebar-user-info">
                                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                                <div class="sidebar-user-role">مدير المتجر</div>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                            </button>
                        </form>
                    </div>
                @endauth

                @guest
                    <div class="mt-auto px-3 pb-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light w-100 mb-2">دخول</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-warning w-100">تسجيل</a>
                    </div>
                @endguest
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 p-md-4">

                <div class="container-fluid">
                    {{-- Alerts are now handled by SweetAlert2 in JavaScript below --}}
                </div>

                <div class="px-3">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @include('assets.js.main')
    <script defer src="{{ asset('assets/js/darkmode.js') }}"></script>
    @stack('js')
</body>

</html>
