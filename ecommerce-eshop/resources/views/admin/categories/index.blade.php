@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">إدارة الأقسام (ربط المقاسات)</h2>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">القسم</th>
                            <th class="py-3">دليل المقاسات المرتبط</th>
                            <th class="py-3 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-4 fw-bold">
                                    @php
                                        $catImage = $category->image ?? null;
                                        if ($catImage && !str_starts_with($catImage, 'http') && !str_starts_with($catImage, '/')) {
                                            $catImage = '/storage/' . $catImage;
                                        }
                                        $catImage = $catImage ?? '/assets/img/placeholder.svg';
                                    @endphp
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $catImage }}" class="rounded-3"
                                            style="width: 40px; height: 40px; object-fit: cover;"
                                            onerror="this.src='/assets/img/placeholder.svg'">
                                        {{ $category->name }}
                                    </div>
                                </td>
                                <td>
                                    @if (isset($category->size_guide))
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                            {{ $category->size_guide['name'] }}
                                        </span>
                                    @else
                                        <span class="text-muted small">لا يوجد دليل مرتبط</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="btn btn-sm btn-dark rounded-pill px-3">
                                        <i class="bi bi-link-45deg me-1"></i> ربط مقاييس
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
