@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
            <div class="mb-3 mb-md-0">
                <h1 class="fw-bold mb-1" style="font-size: 2.5rem; letter-spacing: -0.5px;">
                    {{ __('messages.all_collections') }}</h1>
                <p class="text-muted mb-0">{{ $products->total() ?? 0 }} {{ __('messages.products_found') }}</p>
            </div>

            <div class="d-flex gap-3 align-items-center">
                <button class="btn btn-outline-dark rounded-0 d-lg-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#sidebarFilter">
                    <i class="bi bi-funnel"></i> {{ __('messages.filters') }}
                </button>

                <div class="dropdown">
                    <button
                        class="btn btn-white border rounded-0 px-4 py-2 d-flex align-items-center justify-content-between gap-3 dropdown-toggle"
                        type="button" data-bs-toggle="dropdown">
                        <span>{{ __('messages.sort_by') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-0 mt-1">
                        <li><a class="dropdown-item py-2"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">{{ __('messages.newest') }}</a>
                        </li>
                        <li><a class="dropdown-item py-2"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">الأقدم</a></li>
                        <li><a class="dropdown-item py-2"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}">{{ __('messages.price_low') }}</a>
                        </li>
                        <li><a class="dropdown-item py-2"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}">{{ __('messages.price_high') }}</a>
                        </li>
                        <li><a class="dropdown-item py-2"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">الاسم (أ-ي)</a></li>
                        <li><a class="dropdown-item py-2"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">الاسم (ي-أ)</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 collapse d-lg-block mb-5" id="sidebarFilter">
                <div class="pe-lg-4 sidebar-filter-container">
                    <!-- Featured Section -->
                    <div class="mb-5">
                        <div class="filter-header d-flex align-items-center mb-4">
                            <h6 class="fw-black text-uppercase m-0 small-title">المجموعات</h6>
                            <div class="flex-grow-1 ms-3 border-bottom opacity-10"></div>
                        </div>
                        <ul class="list-unstyled custom-filter-list">
                            <li class="mb-3">
                                <a href="{{ route('products.index') }}"
                                    class="filter-link-modern {{ !request('category_id') && !request('offers') ? 'active' : '' }}">
                                    <i class="bi bi-grid-fill me-2"></i> {{ __('messages.all_products') }}
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="{{ request()->fullUrlWithQuery(['offers' => request('offers') ? null : 1]) }}"
                                    class="filter-link-modern {{ request('offers') ? 'active-offer' : '' }}">
                                    <i class="bi bi-patch-check-fill me-2 text-gold"></i> عروض حصرية
                                    @if (request('offers'))
                                        <span
                                            class="ms-auto badge bg-danger rounded-circle p-1 d-flex align-items-center justify-content-center"
                                            style="width: 22px; height: 22px;">
                                            <i class="bi bi-x small text-white"></i>
                                        </span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Categories -->
                    <div class="mb-5">
                        <div class="filter-header d-flex align-items-center mb-4">
                            <h6 class="fw-black text-uppercase m-0 small-title">{{ __('messages.category') }}</h6>
                            <div class="flex-grow-1 ms-3 border-bottom opacity-10"></div>
                        </div>
                        <ul class="list-unstyled custom-filter-list">
                            @foreach (app(\App\Services\CategoryService::class)->getAll() as $cat)
                                <li class="mb-2">
                                    <a href="{{ request()->fullUrlWithQuery(['category_id' => $cat->id]) }}"
                                        class="filter-link-modern {{ request('category_id') == $cat->id || (isset($category) && $category->id == $cat->id) ? 'active' : '' }}">
                                        {{ $cat->name }}
                                        <i class="bi bi-chevron-left ms-auto opacity-25"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-5">
                        <div class="filter-header d-flex align-items-center mb-4">
                            <h6 class="fw-black text-uppercase m-0 small-title">{{ __('messages.price_range') }}</h6>
                            <div class="flex-grow-1 ms-3 border-bottom opacity-10"></div>
                        </div>
                        <div class="px-2">
                            @php
                                $currentRate = (float) session('currency_rate', 1);
                                $symbol = (string) session('currency_symbol', 'ر.س');
                                $maxPrice = (float) request('max_price', 5000);
                            @endphp
                            <input type="range" class="form-range premium-range" min="0"
                                max="{{ 5000 * $currentRate }}" step="{{ 50 * $currentRate }}" id="priceRange"
                                name="max_price" value="{{ $maxPrice * $currentRate }}">
                            <div class="d-flex justify-content-between mt-3">
                                <span class="badge bg-light text-dark fw-bold border p-2">0</span>
                                <span class="badge bg-dark text-white fw-bold p-2"
                                    id="priceValue">{{ number_format($maxPrice * $currentRate, 0) }}
                                    {{ $symbol }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="mb-5">
                        <div class="filter-header d-flex align-items-center mb-4">
                            <h6 class="fw-black text-uppercase m-0 small-title">{{ __('messages.color') }}</h6>
                            <div class="flex-grow-1 ms-3 border-bottom opacity-10"></div>
                        </div>
                        <div class="d-flex gap-3 flex-wrap px-1">
                            @foreach ($filters['colors'] ?? [] as $color)
                                @php
                                    $colorMap = [
                                        'أسود' => '#000000',
                                        'أبيض' => '#ffffff',
                                        'كحلي' => '#1e3a8a',
                                        'بيج' => '#f5f5dc',
                                        'أحمر' => '#ef4444',
                                        'أزرق' => '#3b82f6',
                                        'أخضر' => '#10b981',
                                        'رمادي' => '#6b7280',
                                    ];
                                    $hex = $colorMap[$color] ?? ($color[0] === '#' ? $color : '#ccc');
                                @endphp
                                <a href="#"
                                    class="color-checkbox shadow-sm {{ request('color') == $color ? 'active' : '' }}"
                                    style="background-color: {{ $hex }}; border: {{ strtolower($hex) == '#ffffff' ? '1px solid #ddd' : 'none' }}"
                                    title="{{ $color }}">
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sizes -->
                    <div class="mb-4">
                        <div class="filter-header d-flex align-items-center mb-4">
                            <h6 class="fw-black text-uppercase m-0 small-title">{{ __('messages.size') }}</h6>
                            <div class="flex-grow-1 ms-3 border-bottom opacity-10"></div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap px-1">
                            @foreach ($filters['sizes'] ?? [] as $size)
                                <a href="#" class="premium-size-box {{ request('size') == $size ? 'active' : '' }}">
                                    {{ $size }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="row g-4">
                    @forelse($products as $product)
                        <div class="col-6 col-md-4">
                            <x-product-card :product="$product" />
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="bg-light rounded-3 p-5">
                                <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block"></i>
                                <h5>{{ __('messages.no_products_found_msg') }}</h5>
                                <p class="text-muted">{{ __('messages.adjust_filters_msg') }}</p>
                                <a href="{{ route('products.index') }}"
                                    class="btn btn-dark rounded-0 px-4 mt-2">{{ __('messages.clear_filters') }}</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styles for Luxe Threads Look */
        .small-title {
            font-size: 0.75rem;
            letter-spacing: 1.2px;
            color: #111;
        }

        .filter-link {
            text-decoration: none;
            transition: color 0.2s;
            display: block;
        }

        .filter-link:hover {
            color: #000 !important;
        }

        /* Custom Range Slider */
        .custom-range::-webkit-slider-thumb {
            background: #000;
        }

        .custom-range::-moz-range-thumb {
            background: #000;
        }

        /* Color Checkboxes */
        .color-checkbox {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .color-checkbox:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .color-checkbox.active {
            transform: scale(1.4);
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #000;
            z-index: 2;
        }

        /* Size Boxes */
        .premium-size-box {
            min-width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f1f1f1;
            color: #555;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            background: #fff;
            transition: all 0.2s ease;
            padding: 0 10px;
        }

        .premium-size-box:hover {
            border-color: #000;
            color: #000;
        }

        .premium-size-box.active {
            background: #000;
            border-color: #000 !important;
            color: #fff !important;
        }

        /* Range Slider */
        .premium-range::-webkit-slider-thumb {
            background: #000;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .premium-range::-moz-range-thumb {
            background: #000;
            border: 2px solid #fff;
        }
    </style>
    <div id="loadingOverlay"
        style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; justify-content:center; align-items:center;">
        <div class="spinner-border text-dark"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const range = document.getElementById('priceRange');
            const value = document.getElementById('priceValue');
            const currencySymbol = '{{ session('currency_symbol', 'ر.س') }}';
            const currencyRate = {{ session('currency_rate', 1) }};

            if (range && value) {
                range.addEventListener('input', function() {
                    value.textContent = Math.round(this.value).toLocaleString() + ' ' + currencySymbol;
                    debouncedFetch();
                });
            }

            // Hook up filter clicks
            document.querySelectorAll('.filter-link-modern, .color-checkbox, .premium-size-box').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Handle active classes
                    if (this.classList.contains('filter-link-modern')) {
                        document.querySelectorAll('.filter-link-modern').forEach(l => l.classList
                            .remove('active'));
                        this.classList.add('active');
                    } else if (this.classList.contains('color-checkbox')) {
                        const wasActive = this.classList.contains('active');
                        document.querySelectorAll('.color-checkbox').forEach(c => c.classList
                            .remove('active'));
                        if (!wasActive) this.classList.add('active');
                    } else if (this.classList.contains('premium-size-box')) {
                        const wasActive = this.classList.contains('active');
                        document.querySelectorAll('.premium-size-box').forEach(s => s.classList
                            .remove('active'));
                        if (!wasActive) this.classList.add('active');
                    }

                    fetchProducts();
                });
            });
        });

        let debounceTimer;

        function debouncedFetch() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchProducts();
            }, 500); // Wait 500ms after last move
        }

        function fetchProducts(page = 1) {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'flex';

            const url = new URL(window.location.origin + window.location.pathname);

            // Get active category
            const activeCat = document.querySelector('.filter-link-modern.active');
            if (activeCat && activeCat.href.includes('category_id=')) {
                const catId = new URL(activeCat.href).searchParams.get('category_id');
                if (catId) url.searchParams.set('category_id', catId);
            }
            if (activeCat && activeCat.href.includes('offers=1')) {
                url.searchParams.set('offers', '1');
            }

            // Price range
            const range = document.getElementById('priceRange');
            const currencyRate = {{ session('currency_rate', 1) }};
            const basePrice = range.value / currencyRate;
            url.searchParams.set('max_price', Math.round(basePrice));

            // Color
            const activeColor = document.querySelector('.color-checkbox.active');
            if (activeColor) {
                const colorName = activeColor.getAttribute('title');
                if (colorName) url.searchParams.set('color', colorName);
            }

            // Size
            const activeSize = document.querySelector('.premium-size-box.active');
            if (activeSize) {
                url.searchParams.set('size', activeSize.innerText.trim());
            }

            // Page
            url.searchParams.set('page', page);

            fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    document.querySelector('.col-lg-9 .row.g-4').innerHTML = data.html;
                    document.querySelector('.mt-5.d-flex.justify-content-center').innerHTML = data.pagination;
                    document.querySelector('.text-muted.mb-0').innerText = data.total +
                        ' {{ __('messages.products_found') }}';

                    // Update browser URL without reloading
                    window.history.pushState({}, '', url.toString());

                    // Re-bind pagination clicks
                    bindPagination();

                    overlay.style.display = 'none';
                })
                .catch(err => {
                    console.error(err);
                    overlay.style.display = 'none';
                });
        }

        function bindPagination() {
            document.querySelectorAll('.mt-5 .pagination a').forEach(a => {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = new URL(this.href).searchParams.get('page');
                    fetchProducts(page);
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        }

        // Init pagination binding
        document.addEventListener('DOMContentLoaded', bindPagination);
    </script>
@endsection
