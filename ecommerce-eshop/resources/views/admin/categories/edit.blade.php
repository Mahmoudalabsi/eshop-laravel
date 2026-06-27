@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <a href="{{ route('admin.categories.index') }}"
                        class="btn btn-outline-dark rounded-circle p-2 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <h2 class="fw-bold mb-0">ربط دليل مقاسات بقسم</h2>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                    <div class="mb-4">
                        <h5 class="text-muted small text-uppercase fw-bold mb-2">القسم المحدد</h5>
                        <h3 class="fw-black text-dark">{{ $category->name }}</h3>
                    </div>

                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4 text-start">
                            <label class="form-label fw-bold text-muted small text-uppercase">اختر دليل المقاسات</label>
                            <select name="size_guide_id" class="form-select py-3 border-0 bg-light rounded-3 shadow-none">
                                <option value="">بدون دليل (إلغاء الربط)</option>
                                @foreach ($guides as $guide)
                                    <option value="{{ $guide['id'] }}"
                                        {{ isset($category->size_guide_id) && $category->size_guide_id == $guide['id'] ? 'selected' : '' }}>
                                        {{ $guide['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm">
                                <i class="bi bi-link-45deg me-2"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
