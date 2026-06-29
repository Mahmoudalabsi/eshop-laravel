@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('css')
    <style>
        .product-show-container {
            padding-top: 50px;
        }

        .main-image-wrapper {
            position: sticky;
            top: 100px;
            background: #fff;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            cursor: zoom-in;
            z-index: 100;
        }

        .zoom-lens {
            position: absolute;
            border: 1px solid #d4d4d4;
            width: 180px;
            height: 180px;
            border-radius: 8px;
            background-repeat: no-repeat;
            pointer-events: none;
            display: none;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .thumb-strip {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            padding-bottom: 15px;
            overflow-x: auto;
        }

        .thumb-btn {
            width: 90px;
            height: 90px;
            border-radius: 15px;
            border: 2px solid transparent;
            overflow: hidden;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.3s;
            padding: 0;
            background: #fff;
        }

        .thumb-btn.active {
            border-color: #000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-category-tag {
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #d4af37;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: block;
        }

        .price-tag-large {
            font-size: 2.2rem;
            font-weight: 900;
            color: #000;
            letter-spacing: -1px;
        }

        .product-name-display {
            font-size: 2rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .old-price-tag {
            font-size: 1.2rem;
            color: #adb5bd;
            text-decoration: line-through;
            margin-left: 10px;
        }

        .selection-label {
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 15px;
            color: #444;
            display: block;
        }

        .color-pill {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #dee2e6;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
        }

        .color-pill.active {
            box-shadow: 0 0 0 2px #000;
        }

        .size-box {
            min-width: 55px;
            height: 55px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
        }

        .size-box:hover {
            border-color: #000;
        }

        .size-box.active {
            background: #000;
            color: #fff;
            border-color: #000;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .action-button-group {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .btn-buy-now {
            background: #000;
            color: #fff;
            border: none;
            padding: 18px 40px;
            border-radius: 15px;
            font-weight: 800;
            flex-grow: 2;
            transition: all 0.3s;
        }

        .btn-buy-now:hover {
            background: #222;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-wishlist-large {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #000;
            transition: all 0.3s;
        }

        .btn-wishlist-large:hover {
            background: #fffafa;
            color: #ef4444;
            border-color: #fee2e2;
        }

        .trust-badge-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 50px;
            padding: 30px;
            background: #fcfcfc;
            border-radius: 20px;
            border: 1px solid #f1f1f1;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .trust-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d4af37;
            font-size: 1.2rem;
        }

        .rating-star {
            cursor: pointer;
            font-size: 1.5rem;
            color: #e2e8f0;
            transition: all 0.2s;
        }

        .rating-star:hover,
        .rating-star:hover~.rating-star,
        .rating-input input:checked~label {
            color: #f59e0b;
        }

        .rating-input {
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .offer-ribbon {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: #fff;
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.8rem;
            z-index: 10;
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        }

        .qty-input-modern {
            background: #f8fafc;
            border-radius: 15px;
            padding: 10px;
            width: 130px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #e2e8f0;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            border: none;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #000;
            color: #fff;
        }

        /* No-image placeholder styling */
        .image-placeholder-premium {
            background: linear-gradient(145deg, #f8fafc, #f1f5f9);
            width: 100%;
            height: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            border: 2px dashed #e2e8f0;
            margin: 0;
            border-radius: 30px;
        }

        .image-placeholder-premium i {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
@endpush

@section('content')
    @php
        $totalStock = data_get($product, 'stock_status.total_qty', 0);
    @endphp
    <div class="container product-show-container pb-5">
        <div class="row g-5">
            <!-- Gallery Column -->
            <div class="col-lg-7">
                <div class="main-image-wrapper">
                    @if (isset($product->old_price) && $product->old_price > $product->price)
                        <div class="offer-ribbon">خصم
                            {{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%</div>
                    @endif

                    @php
                        $mainImage = $product->image;
                        if ($mainImage && !str_starts_with($mainImage, 'http') && !str_starts_with($mainImage, '/')) {
                            $mainImage = '/storage/' . $mainImage;
                        }
                    @endphp
                    <div id="imageContainer" onmousemove="zoomImage(event)" onmouseleave="hideZoom()">
                        @if ($mainImage)
                            <img src="{{ $mainImage }}" alt="{{ $product->name }}" id="mainImage"
                                class="img-fluid w-100 object-fit-contain rounded-4"
                                style="height: 600px; background: #fff;"
                                onerror="this.style.display='none'; document.getElementById('mainImagePlaceholder').classList.remove('d-none');">
                        @endif
                        <div id="zoomResult" class="zoom-lens"></div>

                        <div class="image-placeholder-premium {{ $product->image ? 'd-none' : '' }}"
                            id="mainImagePlaceholder">
                            <i class="bi bi-image"></i>
                            <h5 class="fw-bold">صورة المنتج غير متوفرة حالياً</h5>
                            <p class="small opacity-75">نعمل على توفير الصور بأفضل جودة</p>
                        </div>
                    </div>
                </div>

                @if (isset($product->images) && count($product->images) > 0)
                    <div class="thumb-strip thumb-scroll">
                        @foreach ($product->images as $img)
                            @php
                                $imgUrl = is_object($img) ? ($img->image_path ?? $img->image ?? null) : (is_array($img) ? ($img['image_path'] ?? $img['image'] ?? null) : $img);
                                if ($imgUrl && !str_starts_with($imgUrl, 'http') && !str_starts_with($imgUrl, '/')) {
                                    $imgUrl = '/storage/' . $imgUrl;
                                }
                            @endphp
                            @if ($imgUrl)
                                <div class="thumb-btn {{ $loop->first ? 'active' : '' }}"
                                    onclick="updateGallery('{{ $imgUrl }}', this)">
                                    <img src="{{ $imgUrl }}" class="w-100 h-100 object-fit-cover"
                                        onerror="this.style.display='none'; this.parentElement.style.display='none';">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Info Column -->
            <div class="col-lg-5">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                class="text-decoration-none text-muted small">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}"
                                class="text-decoration-none text-muted small">المتجر</a></li>
                        <li class="breadcrumb-item active text-dark fw-bold small">{{ $product->name }}</li>
                    </ol>
                </nav>

                <span class="product-category-tag">{{ $product->subcategory->category->name ?? 'مجموعة حصرية' }}</span>
                <h1 class="product-name-display mb-3">{{ $product->name }}</h1>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="text-warning">
                        @php $rating = (float) data_get($product, 'average_rating', 0); @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($rating))
                                <i class="bi bi-star-fill"></i>
                            @elseif ($i == ceil($rating) && $rating - floor($rating) > 0)
                                <i class="bi bi-star-half"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted small fw-bold">({{ data_get($product, 'reviews_count', 0) }} تقييم
                        حقيقي)</span>
                    <span class="mx-2 text-silver opacity-50">|</span>
                    <span class="text-success small fw-bold">
                        @if ($totalStock > 0)
                            <i class="bi bi-patch-check-fill me-1"></i> متوفر في المخزون
                        @else
                            <i class="bi bi-x-circle-fill me-1 text-danger"></i> غير متوفر
                        @endif
                    </span>
                </div>

                <div class="d-flex align-items-baseline mb-5">
                    <span
                        class="price-tag-large">{{ number_format($product->price * session('currency_rate', 1), 2) }}</span>
                    <span class="ms-1 fw-bold text-dark">{{ session('currency_symbol', 'ر.س') }}</span>
                    @if (isset($product->old_price) && $product->old_price > $product->price)
                        <span
                            class="old-price-tag">{{ number_format($product->old_price * session('currency_rate', 1), 2) }}</span>
                    @endif
                </div>

                <p class="text-muted mb-5 fs-6 leading-relaxed" style="opacity: 0.8;">{{ $product->description }}</p>

                @php
                    $options = collect($product->attributes ?? []);
                    $availableColors = $options->pluck('color')->filter()->unique();
                    $availableSizes = $options->pluck('size')->filter()->unique();

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
                @endphp

                <form action="{{ route('cart.add', $product->id) }}" method="POST" id="addToCartForm">
                    @csrf

                    <!-- Color Selector -->
                    @if ($availableColors->isNotEmpty())
                        <div class="mb-5">
                            <span class="selection-label">الألوان المتاحة</span>
                            <div class="d-flex gap-3">
                                @foreach ($availableColors as $colorName)
                                    <label class="color-pill"
                                        style="background: {{ $colorMap[$colorName] ?? ($colorName[0] === '#' ? $colorName : '#ccc') }};"
                                        title="{{ $colorName }}">
                                        <input type="radio" name="color" value="{{ $colorName }}" class="d-none"
                                            {{ $loop->first ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Size Selector -->
                    @if ($availableSizes->isNotEmpty())
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="selection-label m-0">المقاسات المتوفرة</span>
                                @php
                                    $sizeGuide = \App\Models\SizeGuide::find(data_get($product, 'subcategory.category.size_guide_id'));
                                @endphp
                                @if ($sizeGuide)
                                    <a href="#" class="text-dark small fw-bold text-decoration-underline"
                                        data-bs-toggle="modal" data-bs-target="#sizeGuideModal">جدول القياسات</a>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @foreach ($availableSizes as $size)
                                    <label class="size-box">
                                        <input type="radio" name="size" value="{{ $size }}" class="d-none"
                                            {{ $loop->first ? 'checked' : '' }}>
                                        {{ $size }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Group -->
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-center gap-4">
                            <div class="qty-input-modern">
                                <button type="button" class="qty-btn" onclick="updateQty(-1)"><i
                                        class="bi bi-dash"></i></button>
                                <input type="number" name="quantity" id="productQty" value="{{ $totalStock > 0 ? 1 : 0 }}"
                                    min="{{ $totalStock > 0 ? 1 : 0 }}" max="{{ $totalStock }}"
                                    class="border-0 bg-transparent text-center fw-bold w-100" readonly>
                                <button type="button" class="qty-btn" onclick="updateQty(1)"><i
                                        class="bi bi-plus"></i></button>
                            </div>
                            <div class="text-danger small fw-bold">
                                @if ($totalStock > 0)
                                    <i class="bi bi-lightning-fill"></i> بقي فقط {{ $totalStock }} قطعة في المخزون!
                                @else
                                    <span class="text-muted"><i class="bi bi-exclamation-circle me-1"></i> نفذت الكمية
                                        حالياً</span>
                                @endif
                            </div>
                        </div>

                        <div class="action-button-group">
                            <button type="submit" class="btn-buy-now" {{ $totalStock <= 0 ? 'disabled' : '' }}>
                                @if ($totalStock > 0)
                                    إضافة إلى حقيبة التسوق <i class="bi bi-bag-check-fill ms-2"></i>
                                @else
                                    نفذت الكمية <i class="bi bi-x-circle ms-2"></i>
                                @endif
                            </button>
                            <button type="button" class="btn-wishlist-large"
                                onclick="toggleWishlist(event, {{ $product->id }})"
                                id="wishlist-btn-{{ $product->id }}">
                                <i class="bi bi-heart fs-4"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="trust-badge-container">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-truck"></i></div>
                        <div>
                            <div class="fw-bold small">شحن مجاني</div>
                            <div class="text-muted extra-small">فوق 500 ريال</div>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-shield-lock"></i></div>
                        <div>
                            <div class="fw-bold small">دفع آمن</div>
                            <div class="text-muted extra-small">تشفير 100%</div>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <div>
                            <div class="fw-bold small">استرجاع سهل</div>
                            <div class="text-muted extra-small">خلال 30 يوم</div>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-patch-check"></i></div>
                        <div>
                            <div class="fw-bold small">جودة مضمونة</div>
                            <div class="text-muted extra-small">فحص دقيق للقطع</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Reviews Section -->
        <div class="mt-5 pt-5 border-top">
            <div class="row">
                <!-- Reviews List -->
                <div class="col-lg-7 border-start">
                    <h3 class="fw-black mb-4">آراء العملاء</h3>
                    <div class="reviews-list">
                        @forelse(($product->reviews ?? collect([])) as $review)
                            @php
                                $reviewUser = $review->user ?? null;
                                $reviewUserName = $reviewUser ? $reviewUser->name : 'عميل';
                                $reviewRating = (int) ($review->rating ?? 5);
                                $reviewDate = $review->created_at ? $review->created_at->format('Y/m/d') : '';
                                $reviewComment = $review->comment ?? ($review->content ?? '');
                            @endphp
                            <div class="p-4 rounded-4 bg-light mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-bold">{{ $reviewUserName }}</div>
                                    <div class="text-warning small">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $reviewRating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-muted extra-small mb-2">{{ $reviewDate }}</div>
                                <p class="mb-0 fs-6">{{ $reviewComment }}</p>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="opacity-25 mb-3"><i class="bi bi-chat-left-quote fs-1"></i></div>
                                <p class="text-muted">لا يوجد تقييمات لهذا المنتج بعد. كن أول من يضيف رأيه!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Submit Review Form -->
                <div class="col-lg-5 ps-lg-5 pt-5 pt-lg-0">
                    <div class="p-4 rounded-4" style="background: #fcfcfc; border: 1px solid #f1f1f1;">
                        <h4 class="fw-black mb-3">أضف تقييمك</h4>
                        @auth
                            <form action="{{ route('products.review', $product->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">التقييم</label>
                                    <div class="rating-input d-flex gap-2">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" value="{{ $i }}"
                                                id="star{{ $i }}" class="d-none" {{ $i == 5 ? 'checked' : '' }}>
                                            <label for="star{{ $i }}" class="rating-star"><i
                                                    class="bi bi-star-fill"></i></label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">رأيك الشخصي</label>
                                    <textarea name="comment" class="form-control rounded-3 border-0 bg-white" rows="4"
                                        placeholder="اكتب رأيك هنا بكل صراحة..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-3 fw-bold">إرسال
                                    التقييم</button>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted small">يرجى تسجيل الدخول لتتمكن من إضافة تقييمك.</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm px-4 rounded-pill">تسجيل
                                    الدخول</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-5 pt-5 border-top">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-black mb-0">منتجات قد تنال إعجابك</h2>
                <a href="{{ route('products.index') }}" class="btn btn-link text-dark text-decoration-none fw-bold">عرض
                    الكل <i class="bi bi-chevron-left small ms-1"></i></a>
            </div>
            <div class="row g-4">
                @foreach ($relatedProducts->take(4) as $suggested)
                    <div class="col-6 col-md-3">
                        <x-product-card :product="$suggested" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($sizeGuide ?? null)
        <!-- Size Guide Modal (single, dynamic from DB) -->
        <div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-black">{{ $sizeGuide->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="table-responsive">
                            {!! $sizeGuide->content ?? '<p class="text-muted text-center">لا توجد بيانات قياسات متوفرة.</p>' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script>
        // Store variants data for dynamic updates (attributes loaded via Product model)
        const productVariants = @json(collect($product->attributes ?? [])->map(function($a) { return is_array($a) ? $a : ['color' => $a->color ?? null, 'size' => $a->size ?? null, 'qty' => (int) ($a->qty ?? 0)]; })->values());

        function updateGallery(src, btn) {
            const main = document.getElementById('mainImage');
            main.style.opacity = '0';
            setTimeout(() => {
                main.src = src;
                main.style.opacity = '1';
            }, 200);

            document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        function zoomImage(e) {
            const img = document.getElementById('mainImage');
            const result = document.getElementById('zoomResult');
            if (!img || !result) return;

            result.style.display = "block";

            const rect = img.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const lensWidth = result.offsetWidth;
            const lensHeight = result.offsetHeight;

            result.style.left = (x - lensWidth / 2) + "px";
            result.style.top = (y - lensHeight / 2) + "px";

            const zoomLevel = 2.5;
            result.style.backgroundImage = `url('${img.src}')`;
            result.style.backgroundSize = `${img.width * zoomLevel}px ${img.height * zoomLevel}px`;

            const posX = (x * zoomLevel) - lensWidth / 2;
            const posY = (y * zoomLevel) - lensHeight / 2;
            result.style.backgroundPosition = `-${posX}px -${posY}px`;
        }

        function hideZoom() {
            const res = document.getElementById('zoomResult');
            if (res) res.style.display = "none";
        }

        function updateQty(amt) {
            const input = document.getElementById('productQty');
            const max = parseInt(input.getAttribute('max')) || 0;
            let current = parseInt(input.value) || 0;
            let next = current + amt;
            if (next >= 1 && next <= max) input.value = next;
        }

        function syncVariants() {
            const selectedColor = document.querySelector('input[name="color"]:checked')?.value;
            const selectedSizeInput = document.querySelector('input[name="size"]:checked');
            const selectedSize = selectedSizeInput?.value;

            // 0. Fast path: products without variants (no attributes table rows)
            //    — rely on totalStock from the Product model directly.
            if (!productVariants || productVariants.length === 0) {
                const stockDisplay = document.querySelector('.text-danger.small.fw-bold');
                const qtyInput = document.getElementById('productQty');
                const buyBtn = document.querySelector('.btn-buy-now');
                const totalStock = parseInt(qtyInput?.getAttribute('max') || '0', 10);

                if (buyBtn) {
                    if (totalStock > 0) {
                        buyBtn.disabled = false;
                        buyBtn.innerHTML = 'إضافة إلى حقيبة التسوق <i class="bi bi-bag-check-fill ms-2"></i>';
                        if (stockDisplay) {
                            stockDisplay.innerHTML = `<i class="bi bi-lightning-fill"></i> بقي فقط ${totalStock} قطعة في المخزون!`;
                            stockDisplay.className = 'text-danger small fw-bold';
                        }
                    } else {
                        buyBtn.disabled = true;
                        buyBtn.innerHTML = 'نفذت الكمية <i class="bi bi-x-circle ms-2"></i>';
                        if (stockDisplay) {
                            stockDisplay.innerHTML = `<span class="text-muted"><i class="bi bi-exclamation-circle me-1"></i> نفذت الكمية حالياً</span>`;
                        }
                    }
                }
                return;
            }

            // 1. Update Sizes availability based on selected color
            if (selectedColor) {
                const availableSizesForColor = productVariants
                    .filter(v => v.color === selectedColor && v.qty > 0)
                    .map(v => v.size);

                document.querySelectorAll('.size-box').forEach(box => {
                    const input = box.querySelector('input');
                    const sizeValue = input.value;
                    const isAvailable = availableSizesForColor.includes(sizeValue);

                    if (isAvailable) {
                        box.classList.remove('opacity-25', 'pe-none');
                        box.style.textDecoration = 'none';
                    } else {
                        box.classList.add('opacity-25', 'pe-none');
                        box.style.textDecoration = 'line-through';
                        // If current size becomes unavailable, uncheck it
                        if (input.checked) {
                            input.checked = false;
                            box.classList.remove('active');
                        }
                    }
                });

                // Auto-select first available size if none selected
                const checkedSize = document.querySelector('input[name="size"]:checked');
                if (!checkedSize) {
                    const firstAvailable = Array.from(document.querySelectorAll('.size-box:not(.pe-none) input'))[0];
                    if (firstAvailable) {
                        firstAvailable.checked = true;
                        firstAvailable.closest('.size-box').classList.add('active');
                    }
                }
            }

            // 2. Update Stock Info based on selection
            const activeSize = document.querySelector('input[name="size"]:checked')?.value;
            const variant = productVariants.find(v => v.color === selectedColor && v.size === activeSize);
            const stockDisplay = document.querySelector('.text-danger.small.fw-bold');
            const qtyInput = document.getElementById('productQty');
            const buyBtn = document.querySelector('.btn-buy-now');

            if (variant) {
                const qty = parseInt(variant.qty);
                qtyInput.setAttribute('max', qty);
                if (parseInt(qtyInput.value) > qty) qtyInput.value = qty || (qty > 0 ? 1 : 0);
                if (parseInt(qtyInput.value) < 1 && qty > 0) qtyInput.value = 1;

                if (qty > 0) {
                    stockDisplay.innerHTML = `<i class="bi bi-lightning-fill"></i> بقي فقط ${qty} قطعة في المخزون!`;
                    stockDisplay.className = 'text-danger small fw-bold';
                    buyBtn.disabled = false;
                    buyBtn.innerHTML = 'إضافة إلى حقيبة التسوق <i class="bi bi-bag-check-fill ms-2"></i>';
                } else {
                    stockDisplay.innerHTML =
                        `<span class="text-muted"><i class="bi bi-exclamation-circle me-1"></i> نفذت الكمية حالياً</span>`;
                    buyBtn.disabled = true;
                    buyBtn.innerHTML = 'نفذت الكمية <i class="bi bi-x-circle ms-2"></i>';
                }
            } else if (selectedColor && !selectedSize) {
                // Color picked but no size yet — don't disable button, just hint
                stockDisplay.innerHTML = `<span class="text-muted">اختر المقاس لرؤية التوفر</span>`;
                buyBtn.disabled = false;
            } else {
                // Should not happen with current logic but as fallback
                stockDisplay.innerHTML = `<span class="text-muted">اختر المعطيات لرؤية التوفر</span>`;
                buyBtn.disabled = true;
            }
        }

        // Handle selection highlight & sync
        document.querySelectorAll('.size-box input').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('.size-box').forEach(b => b.classList.remove('active'));
                if (this.checked) this.closest('.size-box').classList.add('active');
                syncVariants();
            });
        });

        document.querySelectorAll('.color-pill input').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('.color-pill').forEach(b => b.classList.remove('active'));
                if (this.checked) this.closest('.color-pill').classList.add('active');
                syncVariants();
            });
        });

        // Initialize state
        document.addEventListener('DOMContentLoaded', () => {
            // First ensure labels match initial checked inputs
            document.querySelectorAll('input:checked').forEach(input => {
                const label = input.closest('.size-box') || input.closest('.color-pill');
                if (label) label.classList.add('active');
            });
            syncVariants();

            // Handle form submission via AJAX
            document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
                e.preventDefault();

                const btn = this.querySelector('.btn-buy-now');
                const originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الإضافة...';

                const formData = new FormData(this);
                const data = {};
                formData.forEach((value, key) => data[key] = value);

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(res => {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;

                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });

                            // Update cart counter in header (the dot)
                            const cartBadge = document.getElementById('cartBadge');
                            if (cartBadge) {
                                cartBadge.classList.remove('d-none');
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: res.message
                            });
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                        console.error(err);
                    });
            });
        });
    </script>
@endpush
