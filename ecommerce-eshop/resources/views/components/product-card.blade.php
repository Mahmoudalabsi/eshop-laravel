@props(['product'])

@php
    $productImage = data_get($product, 'image');
    if ($productImage && !str_starts_with($productImage, 'http') && !str_starts_with($productImage, '/')) {
        $productImage = '/storage/' . $productImage;
    }
    $productImage = $productImage ?: asset('assets/img/placeholder.svg');

    $hasOldPrice = isset($product->old_price) && $product->old_price > $product->price;
    $discountPct = $hasOldPrice ? round((($product->old_price - $product->price) / $product->old_price) * 100) : 0;
    $isAvailable = (int) data_get($product, 'total_stock', 1) > 0;
    $avgRating = round((float) (data_get($product, 'average_rating') ?: 4.5), 1);
    $reviewsCount = (int) data_get($product, 'reviews_count', 0);
@endphp

<div class="card h-100 border-0 rounded-4 product-card group shadow-sm">
    <!-- Image Container -->
    <div class="position-relative bg-light rounded-top-4 overflow-hidden image-wrapper">
        <div class="ratio ratio-1x1">
            <img src="{{ $productImage }}"
                onerror="this.src='{{ asset('assets/img/placeholder.svg') }}'"
                alt="{{ data_get($product, 'name') }}"
                class="object-fit-cover w-100 h-100 group-hover-scale">
        </div>

        <!-- Badges -->
        @if ($hasOldPrice)
            <span class="badge bg-danger position-absolute top-0 start-0 m-3 rounded-pill px-3 py-2 shadow-sm small fw-bold">
                -{{ $discountPct }}%
            </span>
        @endif

        @if (!$isAvailable)
            <span class="badge bg-secondary position-absolute top-0 start-0 m-3 {{ $hasOldPrice ? 'mt-5' : '' }} rounded-pill px-3 py-2 shadow-sm small fw-bold">
                نفذت الكمية
            </span>
        @endif

        @if (data_get($product, 'is_featured'))
            <span class="badge bg-gold text-dark position-absolute top-0 end-0 m-3 rounded-pill px-2 py-1 shadow-sm small fw-bold" style="font-size:0.65rem;">
                <i class="bi bi-star-fill"></i>
            </span>
        @endif

        <!-- Wishlist Button (positioned absolutely, no conflicting inline style) -->
        <button
            type="button"
            class="btn btn-light rounded-circle position-absolute shadow-sm border-0 d-flex align-items-center justify-content-center wishlist-btn"
            style="width: 38px; height: 38px; bottom: 80px; end: 12px; z-index: 5;"
            data-product-id="{{ data_get($product, 'id') }}"
            aria-label="إضافة للمفضلة"
            onclick="toggleWishlist(event, {{ data_get($product, 'id') }})">
            <i class="bi bi-heart" style="font-size: 1rem;"></i>
        </button>

        <!-- Quick Add (slide up) -->
        <div class="action-overlay position-absolute bottom-0 start-0 w-100 p-2" style="z-index: 4;">
            @if ($isAvailable)
                <button type="button"
                    class="btn btn-dark w-100 rounded-3 py-2 fw-medium shadow-sm d-flex align-items-center justify-content-center gap-2"
                    onclick="addToCartAjax(event, {{ data_get($product, 'id') }})">
                    <i class="bi bi-bag-plus"></i> {{ __('messages.add_to_cart') }}
                </button>
            @else
                <button type="button" disabled
                    class="btn btn-secondary w-100 rounded-3 py-2 fw-medium shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-x-circle"></i> نفذت الكمية
                </button>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body px-3 pb-3 pt-2">
        <!-- Category & Rating -->
        <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="product-cat-label">
                {{ data_get($product, 'subcategory.category.name', __('messages.category')) }}
            </small>
            <div class="d-flex align-items-center product-rating">
                <i class="bi bi-star-fill"></i>
                <span class="ms-1">{{ $avgRating }}</span>
                @if ($reviewsCount > 0)
                    <span class="ms-1 text-muted" style="font-size:0.65rem;">({{ $reviewsCount }})</span>
                @endif
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
            @if ($hasOldPrice)
                <del class="text-muted small text-decoration-line-through" style="font-size: 0.75rem;">
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
        border-radius: 1rem !important;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12) !important;
    }
    .image-wrapper {
        position: relative;
        background: #f8fafc;
    }
    .group-hover-scale {
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .product-card:hover .group-hover-scale {
        transform: scale(1.08);
    }
    /* Quick Add overlay slide-up */
    .action-overlay {
        transform: translateY(110%);
        transition: transform 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 0.3s ease;
        opacity: 0;
    }
    .product-card:hover .action-overlay {
        transform: translateY(0);
        opacity: 1;
    }
    .hover-primary:hover {
        color: #c5a059 !important;
    }
    .wishlist-btn {
        opacity: 0;
        transition: opacity 0.25s ease, transform 0.25s ease, background-color 0.2s ease, color 0.2s ease;
        transform: translateY(6px);
        background: rgba(255,255,255,0.95) !important;
        backdrop-filter: blur(6px);
        color: #0f172a !important;
        inset-inline-end: 12px !important;
    }
    .product-card:hover .wishlist-btn {
        opacity: 1;
        transform: translateY(0);
    }
    .wishlist-btn:hover {
        background: #dc3545 !important;
        color: #fff !important;
    }
    .wishlist-btn.active {
        background: #dc3545 !important;
        color: #fff !important;
    }
    .wishlist-btn.active i {
        color: #fff !important;
    }
    .product-cat-label {
        color: #94a3b8;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        font-weight: 600;
    }
    .product-rating {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .product-rating i {
        color: #d97706;
        font-size: 0.68rem;
    }
</style>
