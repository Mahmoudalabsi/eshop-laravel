@extends('layouts.app')

@section('content')
    <div class="mb-5 text-center px-4">
        <h1 class="display-5 fw-bold text-dark mb-3">تسوق حسب القسم</h1>
        <p class="text-secondary mx-auto" style="max-width: 600px;">تصفح مجموعة واسعة من الأقسام وابحث عن ما تحتاجه بالضبط.
        </p>
    </div>

    <div class="row g-4 text-right" dir="rtl">
        @forelse($categories as $category)
            @php
                // تحويل البيانات لضمان عدم حدوث خطأ سواء كانت مصفوفة أو كائن
                $id = data_get($category, 'id');
                $name = data_get($category, 'name');
                $image = data_get($category, 'image');
                if ($image && !str_starts_with($image, 'http') && !str_starts_with($image, '/')) {
                    $image = asset('storage/' . $image);
                }
                $image = $image ?: asset('assets/img/placeholder.svg');
            @endphp

            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('categories.show', $id) }}" class="text-decoration-none group">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden transition-transform-up">
                        <div class="ratio ratio-1x1 bg-light">
                            <img src="{{ $image }}"
                                class="card-img-top object-fit-cover transition-transform" alt="{{ $name }}"
                                onerror="this.src='{{ asset('assets/img/placeholder.svg') }}'">
                        </div>

                        <div class="card-body text-center p-3">
                            <h5 class="card-title fw-bold text-dark mb-0 group-hover-primary">
                                {{ $name }}
                            </h5>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-secondary h5">لا توجد أقسام متاحة حالياً.</p>
            </div>
        @endforelse
    </div>

    <style>
        .rounded-4 {
            border-radius: 1rem !important;
        }

        /* تأثيرات الحركة عند تمرير الماوس */
        .transition-transform {
            transition: transform 0.5s ease;
        }

        .group:hover .transition-transform {
            transform: scale(1.1);
        }

        .transition-transform-up {
            transition: all 0.3s ease;
        }

        .group:hover .transition-transform-up {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .12) !important;
        }

        .group-hover-primary {
            transition: color 0.3s;
        }

        .group:hover .group-hover-primary {
            color: #0d6efd !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }
    </style>
@endsection
