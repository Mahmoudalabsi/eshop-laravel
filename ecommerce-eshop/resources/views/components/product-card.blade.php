@props(['product'])

@php
    $productImage = data_get($product, 'image');
    if ($productImage && !str_starts_with($productImage, 'http') && !str_starts_with($productImage, '/')) {
        $productImage = '/storage/' . $productImage;
    }
    $productImage = $productImage ?: asset('assets/img/placeholder.svg');
@endphp

<div class="card h-100 border-0 rounded-3 product-card group">
    <!-- Image Container -->
    <div class="position-relative bg-light rounded-3 m-2 overflow-hidden image-wrapper">
        <div class="ratio ratio-1x1">
            <img src="{{ $productImage }}"
                onerror="this.src='{{ asset('assets/img/placeholder.svg') }}'" alt="{{ data_get($product, 'name') }}"
                class="object-fit-contain p-3 transition-transform group-hover-scale w-100 h-100">
        </div>

        <!-- Badges -->
        @php $isAvailable = data_get($product, 'stock_status.available'); @endphp
        @if (isset($product->old_price) && $product->old_price > $product->price)
            <span class="badge bg-danger position-absolute top-0 start-0 m-3 rounded-pill px-2 py-1 shadow-sm small">
                {{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
                {{ __('messages.discount') }}
            </span>
        @endif

        @if (!$isAvailable)
            <span
                class="badge bg-secondary position-absolute top-0 start-0 m-3 mt-5 rounded-pill px-2 py-1 shadow-sm small">
                {{ __('messages.out_of_stock') ?? 'نفذت الكمية' }}
            </span>
        @endif

        <!-- Wishlist Button -->
        <button
            class="btn btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-sm border-0 d-flex align-items-center justify-content-center wishlist-btn"
            style="width: 32px; height: 32px; z-index: 5; position: relative;"
            onclick="toggleWishlist(event, {{ data_get($product, 'id') }})"
            id="wishlist-btn-{{ data_get($product, 'id') }}">
            <i class="bi bi-heart" style="font-size: 0.9rem;"></i>
        </button>

        <script>
            // Ensure functions are global and only defined once
            if (typeof addToCartAjax !== 'function') {
                window.addToCartAjax = function(event, productId) {
                    event.preventDefault();
                    event.stopPropagation();

                    const btn = event.currentTarget;
                    const originalContent = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    fetch(`{{ url('cart/add') }}/${productId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                quantity: 1
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            btn.disabled = false;
                            btn.innerHTML = originalContent;

                            if (data.success) {
                                Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                }).fire({
                                    icon: 'success',
                                    title: data.message
                                });

                                // Optional: Update cart counter in header if it exists
                                const cartBadge = document.querySelector('.bi-bag + .badge');
                                if (cartBadge) {
                                    cartBadge.style.display = 'block';
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                            console.error('Error:', error);
                        });
                };
            }

            if (typeof toggleWishlist !== 'function') {
                window.toggleWishlist = function(event, productId) {
                    event.preventDefault();
                    event.stopPropagation();

                    const btn = document.getElementById(`wishlist-btn-${productId}`);
                    const icon = btn.querySelector('i');

                    fetch("{{ route('wishlist.toggle') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(response => {
                            if (response.status === 401) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'عذراً',
                                    text: 'يرجى تسجيل الدخول لتتمكن من إضافة المنتجات للمفضلة',
                                    confirmButtonText: 'تسجيل الدخول',
                                    showCancelButton: true,
                                    cancelButtonText: 'إلغاء'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('login') }}";
                                    }
                                });
                                throw new Error('Unauthorized');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.added) {
                                icon.classList.remove('bi-heart');
                                icon.classList.add('bi-heart-fill');
                                btn.classList.add('active');

                                Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                }).fire({
                                    icon: 'success',
                                    title: 'تمت الإضافة للمفضلة'
                                });
                            } else {
                                icon.classList.remove('bi-heart-fill');
                                icon.classList.add('bi-heart');
                                btn.classList.remove('active');

                                Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                }).fire({
                                    icon: 'info',
                                    title: 'تمت الإزالة من المفضلة'
                                });
                            }
                        })
                        .catch(error => {
                            if (error.message !== 'Unauthorized') {
                                console.error('Error toggling wishlist:', error);
                            }
                        });
                };
            }
        </script>

        <!-- Quick Add (Slide Up) -->
        <div class="action-overlay position-absolute bottom-0 start-0 w-100 p-2" style="z-index: 5;">
            @if ($isAvailable)
                <button type="button" onclick="addToCartAjax(event, {{ data_get($product, 'id') }})"
                    class="btn btn-dark w-100 rounded-3 py-2 fw-medium shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-bag-plus"></i> {{ __('messages.add_to_cart') }}
                </button>
            @else
                <button type="button" disabled
                    class="btn btn-secondary w-100 rounded-3 py-2 fw-medium shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-x-circle"></i> {{ __('messages.out_of_stock') ?? 'نفذت الكمية' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body px-3 pb-3 pt-1">
        <!-- Category & Rating -->
        <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="text-muted text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                {{ data_get($product, 'subcategory.category.name', __('messages.category')) }}
            </small>
            <div class="d-flex text-warning" style="font-size: 0.7rem;">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-half"></i>
                <span class="text-muted ms-1" style="font-size: 0.65rem;">(4.5)</span>
            </div>
        </div>

        <!-- Title -->
        <h3 class="h6 fw-bold mb-1">
            <a href="{{ route('products.show', data_get($product, 'id')) }}"
                class="text-dark text-decoration-none text-truncate d-block hover-primary stretched-link"
                title="{{ data_get($product, 'name') }}">
                {{ data_get($product, 'name') }}
            </a>
        </h3>

        <!-- Price -->
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="fw-black h6 mb-0 text-dark">
                {{ number_format($product->price * session('currency_rate', 1), 2) }}
                <small style="font-size: 0.7em;">{{ session('currency_symbol', 'ر.س') }}</small>
            </span>
            @if (isset($product->old_price) && $product->old_price > $product->price)
                <del class="text-muted small text-decoration-line-through decoration-danger"
                    style="font-size: 0.75rem;">
                    {{ number_format($product->old_price * session('currency_rate', 1), 2) }}
                </del>
            @endif
        </div>
    </div>
</div>

<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .image-wrapper {
        position: relative;
    }

    .group-hover-scale {
        transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .product-card:hover .group-hover-scale {
        transform: scale(1.08);
    }

    /* Action Overlay Animation */
    .action-overlay {
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 0.3s ease;
        opacity: 0;
    }

    .product-card:hover .action-overlay {
        transform: translateY(0);
        opacity: 1;
    }

    .hover-primary:hover {
        color: #555 !important;
        /* Slightly lighter black */
    }

    .wishlist-btn {
        opacity: 0;
        transition: opacity 0.2s ease, transform 0.2s ease;
        transform: translateX(10px);
    }

    .product-card:hover .wishlist-btn {
        opacity: 1;
        transform: translateX(0);
    }

    .wishlist-btn:hover {
        background-color: #dc3545 !important;
        color: white !important;
    }
</style>
