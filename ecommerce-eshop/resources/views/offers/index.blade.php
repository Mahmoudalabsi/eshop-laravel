@extends('layouts.app')

@push('css')
    <style>
        /* تحسينات التصميم العام */
        .rounded-4 {
            border-radius: var(--radius-lg) !important;
        }

        .rounded-5 {
            border-radius: 2rem !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        /* ستايل السلايدر (Bootstrap Carousel) */
        .carousel-item {
            height: 450px;
        }

        .carousel-item img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .carousel-caption {
            background: rgba(0, 0, 0, 0.6);
            border-radius: var(--radius-lg);
            padding: 2rem;
            bottom: 20%;
            backdrop-filter: blur(8px);
        }

        /* ستايل بطاقة المنتج */
        .product-card {
            transition: var(--transition);
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 24px 48px rgba(99, 102, 241, 0.15) !important;
            border-color: rgba(99, 102, 241, 0.2);
        }

        .badge-discount {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
            background: linear-gradient(135deg, var(--danger-color) 0%, #ff6b6b 100%);
        }
    </style>
@endpush

@section('content')
    <div class="container py-4 text-end" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

        <div class="container py-5 text-end" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

            <!-- Section: Active Campaigns (Real API Data) -->
            @if ($campaigns->isNotEmpty())
                <div class="mb-5">
                    <h2 class="h1 fw-black mb-4" style="color: var(--dark-color);">
                        {{ __('messages.exclusive_offers') ?? 'عروض حصرية منتظرة' }}
                        <span class="ms-2">✨</span>
                    </h2>
                    <div class="row g-4">
                        @foreach ($campaigns as $camp)
                            <div class="col-md-4">
                                <div
                                    class="promotion-card p-4 rounded-5 h-100 shadow-sm border-0 position-relative overflow-hidden">
                                    <div class="position-relative z-2 text-white">

                                        <h3 class="fw-black mb-2">{{ data_get($camp, 'name') }}</h3>
                                        <p class="h2 fw-black text-gold mb-4">
                                            @if (data_get($camp, 'type') === 'percentage')
                                                {{ data_get($camp, 'discount_value') }}%
                                            @else
                                                {{ data_get($camp, 'discount_value') }} <span
                                                    class="h6">{{ $selectedCurrency->symbol ?? 'ر.س' }}</span>
                                            @endif
                                            <span class="h6 text-white opacity-50 ms-1">@lang('messages.off')</span>
                                        </p>
                                        <div class="mb-4">
                                            <div class="small opacity-75 mb-2">
                                                <i class="bi bi-clock-history me-1"></i>
                                                @lang('messages.ending_in'):
                                            </div>
                                            <div class="d-flex gap-2 timer-container"
                                                data-endtime="{{ data_get($camp, 'ends_at') }}">
                                                <div class="timer-segment">
                                                    <span class="d-block h5 mb-0 days">00</span>
                                                    <span class="timer-label">@lang('messages.days')</span>
                                                </div>
                                                <div class="timer-divider mt-1">:</div>
                                                <div class="timer-segment">
                                                    <span class="d-block h5 mb-0 hours">00</span>
                                                    <span class="timer-label">@lang('messages.hours')</span>
                                                </div>
                                                <div class="timer-divider mt-1">:</div>
                                                <div class="timer-segment">
                                                    <span class="d-block h5 mb-0 minutes">00</span>
                                                    <span class="timer-label">@lang('messages.minutes')</span>
                                                </div>
                                                <div class="timer-divider mt-1">:</div>
                                                <div class="timer-segment">
                                                    <span class="d-block h5 mb-0 seconds">00</span>
                                                    <span class="timer-label">@lang('messages.seconds')</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('products.index') }}"
                                            class="btn btn-outline-light rounded-pill px-4 btn-sm">
                                            @lang('messages.shop_now')
                                        </a>
                                    </div>
                                    <!-- Decorative Circles -->
                                    <div class="position-absolute"
                                        style="width: 200px; height: 200px; background: rgba(212, 175, 55, 0.1); border-radius: 50%; bottom: -50px; left: -50px; filter: blur(40px);">
                                    </div>
                                    <div class="position-absolute"
                                        style="width: 150px; height: 150px; background: rgba(44, 62, 80, 0.3); border-radius: 50%; top: -30px; right: -30px; filter: blur(30px);">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Section: Today's High Discounts -->
            <div class="mb-5 p-5 shadow-sm rounded-5 overflow-hidden position-relative"
                style="background: #fff; border: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box-lux"
                            style="background: #fff0f0; color: #ff4d4d; width: 50px; height: 50px; border-radius: 12px; display:flex; align-items:center; justify-content:center; font-size: 1.5rem;">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <h2 class="h2 fw-black m-0" style="color: var(--dark-color);">{{ __('messages.hour_offers') }}
                            </h2>
                            <p class="text-muted small m-0">
                                @lang('messages.ending_soon_desc')</p>
                        </div>
                    </div>
                    <div class="fw-bold px-4 py-2 rounded-pill shadow-sm bg-white border"
                        style="color: var(--danger-color); font-family: monospace; font-size: 1.2rem;">
                        @lang('messages.ending_in'): <span id="countdown-timer">00:00:00</span>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach ($products->take(4) as $p)
                        <div class="col-6 col-md-3">
                            <x-product-card :product="$p" />
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Section: All Offer Products -->
            <div class="d-flex flex-column mb-4">
                <h2 class="h2 fw-black mb-1" style="color: var(--dark-color);">{{ __('messages.all_offer_products') }}</h2>
                <div style="width: 60px; height: 4px; background: var(--accent-color); border-radius: 2px;"></div>
            </div>

            <div class="row g-4">
                @forelse ($products as $p)
                    <div class="col-6 col-md-4 col-lg-3">
                        <x-product-card :product="$p" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state-lux">
                            <i class="bi bi-gift fs-1 mb-3 text-gold"></i>
                            <p class="h4" style="color: var(--text-muted);">{{ __('messages.no_offers') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            @endif

        </div>
    @endsection

    @push('js')
        <script>
            function updateCountdowns() {
                const timers = document.querySelectorAll('.timer-container');

                timers.forEach(timer => {
                    const endTimeStr = timer.getAttribute('data-endtime');
                    if (!endTimeStr) return;

                    const endTime = new Date(endTimeStr).getTime();
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance < 0) {
                        timer.innerHTML = '<span class="text-danger fw-bold">EXPIRED</span>';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    timer.querySelector('.days').innerText = String(days).padStart(2, '0');
                    timer.querySelector('.hours').innerText = String(hours).padStart(2, '0');
                    timer.querySelector('.minutes').innerText = String(minutes).padStart(2, '0');
                    timer.querySelector('.seconds').innerText = String(seconds).padStart(2, '0');
                });

                // Update the main Hour Offers countdown if it exists
                const mainCountdown = document.getElementById('countdown-timer');
                if (mainCountdown) {
                    const now = new Date();
                    const tomorrow = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
                    const distance = tomorrow - now;

                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    mainCountdown.innerText =
                        String(hours).padStart(2, '0') + ":" +
                        String(minutes).padStart(2, '0') + ":" +
                        String(seconds).padStart(2, '0');
                }
            }

            setInterval(updateCountdowns, 1000);
            updateCountdowns();
        </script>
    @endpush

    @push('css')
        <style>
            .timer-segment {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 5px 10px;
                border-radius: 12px;
                min-width: 50px;
                text-align: center;
            }

            .timer-label {
                font-size: 0.65rem;
                display: block;
                text-transform: uppercase;
                opacity: 0.8;
                letter-spacing: 1px;
            }

            .timer-divider {
                font-size: 1.5rem;
                font-weight: bold;
                color: var(--gold);
                opacity: 0.5;
            }

            .fw-black {
                font-weight: 900;
            }

            .bg-gold {
                background-color: #d4af37 !important;
            }
        </style>
    @endpush
