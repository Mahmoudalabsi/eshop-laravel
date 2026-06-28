<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة التحكم | @yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --sidebar-bg: #111827;
            --sidebar-hover: #1f2937;
            --accent-color: #3b82f6; /* لون أزرق عصري بدلاً من الأحمر */
            --text-muted: #9ca3af;
            --main-bg: #f3f4f6;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--main-bg);
            color: #1f2937;
            overflow-x: hidden;
        }

        /* --- Sidebar Enhanced --- */
        .sidebar {
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        }

        .sidebar-brand {
            padding: 25px 20px;
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.03);
        }

        .nav-label {
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 20px 25px 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s ease;
            margin: 4px 15px;
            border-radius: 8px;
            font-weight: 500;
        }

        .sidebar a i {
            font-size: 1.2rem;
        }

        .sidebar a:hover {
            background-color: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar a.active {
            background-color: var(--accent-color);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        /* --- Main Content Area --- */
        .main-content {
            margin-right: 260px;
            min-height: 100vh;
            padding: 30px;
            transition: all 0.3s ease;
        }

        /* Top Navbar Style */
        .top-bar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        /* --- Cards & Components --- */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        /* --- Stats Widgets --- */
        .stat-card {
            padding: 20px;
            background: #fff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* --- Mobile Responsiveness --- */
        @media (max-width: 992px) {
            .sidebar {
                margin-right: -260px;
            }
            .sidebar.show {
                margin-right: 0;
            }
            .main-content {
                margin-right: 0;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
    </style>
    @stack('css')
</head>

<body>
    <div class="d-flex">
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand text-white">
                <i class="bi bi-gem fs-4 text-primary"></i>
                <span class="ms-2 fw-black fs-5">ELEGANCE ADMIN</span>
            </div>

            <div class="nav-label">الرئيسية</div>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> لوحة التحكم
            </a>

            <div class="nav-label">الإدارة</div>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-bag-heart"></i> المنتجات
            </a>
            <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> الطلبات
            </a>

            <div class="nav-label">الإعدادات</div>
            <a href="{{ route('admin.languages.index') }}" class="{{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                <i class="bi bi-translate"></i> اللغات
            </a>
            <a href="{{ route('currency.set', 'SAR') }}">
                <i class="bi bi-currency-dollar"></i> العملات
            </a>

            <div class="mt-auto p-3">
                <div class="bg-dark rounded-4 p-3 text-center">
                    <p class="small text-muted mb-2">تسجيل الدخول كـ:</p>
                    <h6 class="text-white mb-3">{{ auth()->user()->name ?? 'المدير العام' }}</h6>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger w-100 rounded-3">
                            <i class="bi bi-power me-1"></i> خروج
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="main-content w-100">
            <div class="top-bar">
                <button class="btn d-lg-none" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-3"></i>
                </button>

                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-light text-dark border p-2">
                        <i class="bi bi-calendar3 me-1"></i> {{ date('Y-m-d') }}
                    </span>
                    <div class="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Admin' }}&background=3b82f6&color=fff"
                             class="rounded-circle shadow-sm border" width="40" alt="avatar">
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-x-circle me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="container-fluid p-0 pt-2">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    @stack('js')
</body>
</html>
