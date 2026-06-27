@php
    $catCardImage = $category->image ?? null;
    if ($catCardImage && !str_starts_with($catCardImage, 'http') && !str_starts_with($catCardImage, '/')) {
        $catCardImage = asset('storage/' . $catCardImage);
    }
    $catCardImage = $catCardImage ?? asset('assets/img/placeholder.svg');
@endphp
<a href="{{ route('categories.show', $category->id) }}"
    class="card border-0 shadow-sm h-100 text-decoration-none group overflow-hidden rounded-4 text-end">
    <div class="ratio ratio-1x1 overflow-hidden">
        <img src="{{ $catCardImage }}"
            onerror="this.src='{{ asset('assets/img/placeholder.svg') }}'" alt="{{ $category->name }}"
            class="object-fit-cover transition-transform">
    </div>
    <div class="card-img-overlay d-flex flex-column justify-content-end p-3 bg-gradient-bottom">
        <h3 class="h5 fw-bold text-white mb-1">{{ $category->name }}</h3>
        <p class="text-white-50 small mb-0">{{ $category->products_count ?? 0 }} منتج</p>
    </div>
</a>

<style>
    .bg-gradient-bottom {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    }

    .group:hover img {
        transform: scale(1.1);
        transition: transform 0.5s ease;
    }
</style>
