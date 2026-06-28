@extends('layouts.app')

@section('title', __('messages.wishlist') . ' - ' . config('app.name'))

@push('css')
    <style>
        .wishlist-header {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
            padding: 60px 0;
            margin-bottom: 40px;
            border-bottom: 1px solid #f1f1f1;
        }

        .empty-wishlist-card {
            background: #fff;
            border-radius: 30px;
            padding: 80px 40px;
            border: 1px dashed #dee2e6;
        }

        .heart-pulse {
            animation: heartBeat 1.5s infinite;
            color: #ef4444;
            display: inline-block;
        }

        @keyframes heartBeat {
            0% {
                transform: scale(1);
            }

            14% {
                transform: scale(1.1);
            }

            28% {
                transform: scale(1);
            }

            42% {
                transform: scale(1.1);
            }

            70% {
                transform: scale(1);
            }
        }

        .wishlist-count-badge {
            background: #000;
            color: #fff;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            vertical-align: middle;
            margin-inline-start: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="wishlist-header">
        <div class="container text-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-3">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none text-muted small text-uppercase fw-bold">الرئيسية</a></li>
                    <li class="breadcrumb-item active text-dark small text-uppercase fw-bold" aria-current="page">المفضلة</li>
                </ol>
            </nav>
            <h1 class="display-4 fw-black mb-0">قائمة الأمنيات
                @if ($products->count() > 0)
                    <span class="wishlist-count-badge">{{ $products->count() }}</span>
                @endif
            </h1>
            <p class="text-muted mt-3 mb-0 fs-5">جميع المنتجات التي وقعت في حبها، مجموعة في مكان واحد.</p>
        </div>
    </div>

    <div class="container pb-5">
        @if ($products->isEmpty())
            <div class="empty-wishlist-card text-center shadow-sm mx-auto" style="max-width: 700px;">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 120px; height: 120px;">
                        <i class="bi bi-heart heart-pulse" style="font-size: 3.5rem;"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-3 text-dark">قائمة أمنياتك فارغة</h2>
                <p class="text-muted mb-5 fs-6 mx-auto" style="max-width: 450px;">
                    يبدو أنك لم تضف أي منتج لمفضلتك بعد. تصفح أحدث المجموعات وأضف لمسة من الجمال لقائمتك.
                </p>
                <a href="{{ route('products.index') }}"
                    class="btn btn-dark btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg hover-lift">
                    اكتشف المجموعات الجديدة <i class="bi bi-arrow-left ms-2"></i>
                </a>
            </div>
        @else
            <div class="row g-4 g-lg-5">
                @foreach ($products as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            <div class="mt-5 pt-5 border-top text-center">
                <p class="text-muted small mb-4">هل ترغب في مشاركة هذه القائمة مع شخص ما؟ (قريباً)</p>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-outline-dark rounded-pill px-4 py-2 small fw-bold opacity-50" disabled>
                        <i class="bi bi-share me-1"></i> مشاركة القائمة
                    </button>
                </div>
            </div>
        @endif
    </div>
@endsection
