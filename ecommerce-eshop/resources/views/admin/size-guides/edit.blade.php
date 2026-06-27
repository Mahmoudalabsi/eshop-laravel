@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <a href="{{ route('admin.size-guides.index') }}"
                        class="btn btn-outline-dark rounded-circle p-2 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <h2 class="fw-bold mb-0">تعديل دليل المقاسات</h2>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <form action="{{ route('admin.size-guides.update', $guide['id']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">اسم الدليل</label>
                            <input type="text" name="name" class="form-control py-3 border-0 bg-light rounded-3"
                                value="{{ $guide['name'] }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">محتوى جدول المقاسات
                                (HTML)</label>
                            <textarea name="content" rows="15" class="form-control p-3 border-0 bg-light rounded-3 font-monospace" required>{{ $guide['content'] }}</textarea>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm">
                                <i class="bi bi-check2-circle me-2"></i> تحديث الدليل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
