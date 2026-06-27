@extends('layouts.app')

@section('title', 'السلة - ' . config('app.name'))

@push('css')
    <style>
        .cart-card {
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .cart-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .quantity-control {
            background: #f8fafc;
            border-radius: 50px;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .quantity-input {
            width: 45px;
            border: none;
            background: transparent;
            text-align: center;
            font-weight: 700;
            outline: none;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .quantity-btn:hover {
            background: #000;
            color: #fff;
        }

        .summary-card {
            background: #fff;
            position: sticky;
            top: 100px;
            border-radius: 20px !important;
            border: none !important;
        }

        .product-img-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            width: 100px;
            height: 100px;
            flex-shrink: 0;
        }

        .product-img-wrapper img {
            transition: transform 0.5s ease;
        }

        .cart-card:hover .product-img-wrapper img {
            transform: scale(1.1);
        }

        .remove-btn {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fffafa;
            color: #ef4444;
            border: 1px solid #fee2e2;
            transition: all 0.2s;
        }

        .remove-btn:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        .empty-cart-illustration {
            max-width: 300px;
            opacity: 0.8;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="fw-black mb-1" style="font-size: 2.5rem;">@lang('messages.your_cart')</h1>
                <p class="text-muted mb-0">@lang('messages.you_have_products') <span class="fw-bold text-dark">{{ count($cart ?? []) }}</span>
                    @lang('messages.products_in_cart')
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none text-muted">@lang('messages.home')</a></li>
                    <li class="breadcrumb-item active fw-bold text-dark">@lang('messages.cart')</li>
                </ol>
            </nav>
        </div>

        @if (empty($cart))
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 150px; height: 150px;">
                        <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-3">@lang('messages.cart_empty_title')</h2>
                <p class="text-muted mb-4 fs-5">@lang('messages.cart_empty_desc')</p>
                <a href="{{ route('products.index') }}" class="btn btn-dark btn-lg rounded-pill px-5 shadow-sm">
                    @lang('messages.start_shopping') <i class="bi bi-arrow-left ms-2"></i>
                </a>
            </div>
        @else
            <div class="row g-5">
                <div class="col-lg-8" id="cartItemsContainer">
                    <div class="d-flex flex-column gap-4">
                        @foreach ($cart as $itemId => $item)
                            <div class="card cart-card border-0 shadow-sm rounded-4 overflow-hidden animate__animated"
                                id="item-{{ $itemId }}">
                                <div class="card-body p-3 p-md-4">
                                    <div class="row align-items-center g-3">
                                        @php
                                            $cartImage = $item['image'] ?? null;
                                            if ($cartImage && !str_starts_with($cartImage, 'http') && !str_starts_with($cartImage, '/')) {
                                                $cartImage = '/storage/' . $cartImage;
                                            }
                                            $cartImage = $cartImage ?? '/assets/img/placeholder.svg';
                                        @endphp
                                        <div class="col-auto">
                                            <div class="product-img-wrapper shadow-sm">
                                                <img src="{{ $cartImage }}" alt="{{ $item['name'] }}"
                                                    class="w-100 h-100 object-fit-cover"
                                                    onerror="this.src='/assets/img/placeholder.svg'">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex flex-column h-100">
                                                <div class="mb-2">
                                                    <h5 class="fw-black mb-1 text-dark">{{ $item['name'] }}</h5>
                                                    @if (!empty($item['options']))
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            @foreach ($item['options'] as $key => $value)
                                                                <span
                                                                    class="badge bg-light text-muted border py-1 px-2 fw-normal">{{ $key }}:
                                                                    {{ $value }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-gold fw-bold mb-0">
                                                    {{ number_format($item['discounted_price'] ?? $item['price']) }}
                                                    <span
                                                        class="extra-small">{{ session('currency_symbol', 'ر.س') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-auto">
                                            <div class="d-flex align-items-center justify-content-between gap-4">
                                                <form action="{{ route('cart.update') }}" method="POST"
                                                    class="d-inline quantity-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="id" value="{{ $itemId }}">
                                                    <div class="quantity-control">
                                                        <button type="button" class="quantity-btn btn-minus"><i
                                                                class="bi bi-dash"></i></button>
                                                        <input type="number" name="quantity"
                                                            value="{{ $item['quantity'] }}" min="1"
                                                            class="quantity-input" readonly>
                                                        <button type="button" class="quantity-btn btn-plus"><i
                                                                class="bi bi-plus"></i></button>
                                                    </div>
                                                </form>

                                                <div class="text-end" style="min-width: 100px;">
                                                    <div class="small text-muted mb-0">@lang('messages.item_total')</div>
                                                    <div class="fw-black fs-5">
                                                        <span class="item-total" id="item-total-{{ $itemId }}">
                                                            {{ number_format(($item['discounted_price'] ?? $item['price']) * $item['quantity']) }}
                                                        </span>
                                                        <span class="small">{{ session('currency_symbol', 'ر.س') }}</span>
                                                    </div>
                                                </div>

                                                <form action="{{ route('cart.remove', $itemId) }}" method="POST"
                                                    class="d-inline remove-item-form" data-id="{{ $itemId }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="remove-btn" title="حذف">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <a href="{{ route('products.index') }}"
                            class="btn btn-link text-decoration-none text-dark fw-bold">
                            <i class="bi bi-arrow-right me-1"></i> @lang('messages.back_to_store')
                        </a>
                        <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" id="clearCartForm">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4"
                                onclick="confirmClearCart()">
                                <i class="bi bi-eraser-fill me-1"></i> @lang('messages.clear_cart')
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card summary-card shadow-lg p-3">
                        <div class="card-body">
                            <h4 class="fw-black mb-4">@lang('messages.cart')</h4>

                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>@lang('messages.subtotal')</span>
                                <span class="fw-bold text-dark"><span
                                        id="summarySubtotal">{{ number_format($total) }}</span>
                                    {{ session('currency_symbol', 'ر.س') }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>@lang('messages.tax_vat')</span>
                                <span class="fw-bold text-dark"><span
                                        id="summaryTax">{{ number_format($total * 0.15) }}</span>
                                    {{ session('currency_symbol', 'ر.س') }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>@lang('messages.shipping_fees')</span>
                                <span class="text-success fw-bold">@lang('messages.free')</span>
                            </div>

                            <div class="bg-light p-3 rounded-4 mb-4 mt-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-5 text-dark">@lang('messages.final_total')</span>
                                    <div class="text-end">
                                        <h3 class="fw-black mb-0 text-dark" id="summaryFinalTotal">
                                            {{ number_format($total * 1.15) }}</h3>
                                        <span class="small fw-bold">{{ session('currency_symbol', 'ر.س') }}</span>
                                    </div>
                                </div>
                            </div>

                            @auth
                                <a href="{{ route('checkout.index') }}" class="btn btn-checkout btn-dark w-100 fs-5 mb-3">
                                    @lang('messages.proceed_checkout') <i class="bi bi-shield-lock-fill ms-2"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-checkout btn-dark w-100 fs-5 mb-3">
                                    @lang('messages.login_to_buy') <i class="bi bi-box-arrow-in-left ms-2"></i>
                                </a>
                            @endauth

                            <div class="text-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg"
                                    height="15" alt="Visa" class="mx-2 opacity-50">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                                    height="20" alt="Mastercard" class="mx-2 opacity-50">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" height="18"
                                    alt="PayPal" class="mx-2 opacity-50">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 border-start border-4 border-gold p-4 bg-white shadow-sm rounded-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi bi-truck fs-3 text-gold"></i>
                            <div>
                                <h6 class="fw-black mb-0">@lang('messages.fast_free_shipping')</h6>
                                <p class="small text-muted mb-0">@lang('messages.fast_free_shipping_desc')</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-arrow-repeat fs-3 text-gold"></i>
                            <div>
                                <h6 class="fw-black mb-0">@lang('messages.easy_return')</h6>
                                <p class="small text-muted mb-0">@lang('messages.easy_return_desc')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityForms = document.querySelectorAll('.quantity-form');
            const currencySymbol = "{{ session('currency_symbol', 'ر.س') }}";

            // Update quantity via AJAX
            quantityForms.forEach(form => {
                const minusBtn = form.querySelector('.btn-minus');
                const plusBtn = form.querySelector('.btn-plus');
                const input = form.querySelector('.quantity-input');
                const itemId = form.querySelector('input[name="id"]').value;

                const updateCart = async (newVal) => {
                    input.value = newVal;
                    try {
                        const response = await fetch("{{ route('cart.update') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                id: itemId,
                                quantity: newVal,
                                _method: 'PATCH'
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            updateSummary(data);
                            const itemTotalEl = document.getElementById(`item-total-${itemId}`);
                            if (itemTotalEl) {
                                itemTotalEl.innerText = Number(data.item_total).toLocaleString();
                            }
                        } else {
                            Swal.fire('خطأ', data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Update error:', error);
                    }
                };

                minusBtn.addEventListener('click', () => {
                    if (input.value > 1) {
                        updateCart(parseInt(input.value) - 1);
                    }
                });

                plusBtn.addEventListener('click', () => {
                    updateCart(parseInt(input.value) + 1);
                });
            });

            // Remove item via AJAX
            document.querySelectorAll('.remove-item-form').forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const itemId = form.dataset.id;
                    const card = document.getElementById(`item-${itemId}`);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();
                        if (data.success) {
                            // Animate removal
                            card.style.transform = 'translateX(100px)';
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.remove();
                                updateSummary(data);

                                // If cart becomes empty
                                if (data.count === 0) {
                                    location.reload(); // Refresh to show empty state
                                }
                            }, 300);

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            });
                        }
                    } catch (error) {
                        console.error('Remove error:', error);
                    }
                });
            });

            function updateSummary(data) {
                // Update Badge in header
                const cartBadge = document.getElementById('cartBadge');
                if (cartBadge) {
                    if (data.count > 0) {
                        cartBadge.classList.remove('d-none');
                    } else {
                        cartBadge.classList.add('d-none');
                    }
                }

                // Update Summary Card
                const subtotalEl = document.getElementById('summarySubtotal');
                const taxEl = document.getElementById('summaryTax');
                const finalTotalEl = document.getElementById('summaryFinalTotal');
                const itemCountEl = document.querySelector('p.text-muted span.text-dark');

                if (subtotalEl) subtotalEl.innerText = Number(data.total).toLocaleString();
                if (taxEl) taxEl.innerText = Number(data.tax).toLocaleString();
                if (finalTotalEl) finalTotalEl.innerText = Number(data.final_total).toLocaleString();
                if (itemCountEl) itemCountEl.innerText = data.count;
            }
        });

        // Confirm clear cart with SweetAlert
        function confirmClearCart() {
            Swal.fire({
                title: "@lang('messages.confirm_clear_cart_title')",
                text: "@lang('messages.confirm_clear_cart_text')",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: "@lang('messages.confirm_clear_cart_btn')",
                cancelButtonText: "@lang('messages.cancel_btn')",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('clearCartForm').submit();
                }
            });
        }
    </script>
@endpush
