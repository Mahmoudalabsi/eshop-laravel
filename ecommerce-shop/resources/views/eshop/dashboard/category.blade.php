@extends('eshop.layouts.admin')

@section('title', 'الاقسام الرئيسية')

@section('content')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2">إدارة الأقسام</h1>
        <div class="d-flex gap-3 align-items-center">
            <div style="min-width: 250px;">
                <input type="text" id="categorySearchInput" class="form-control shadow-sm text-end"
                    placeholder="🔍 ابحث باسم القسم أو الوصف...">
            </div>
            <button type="button" class="btn btn-primary shadow-sm" onclick="openModal('add')">
                <i class="bi bi-plus-circle"></i> إضافة قسم جديد
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">اسم القسم</th>
                            <th width="10%">الحالة</th>
                            <th width="20%">الوصف</th>
                            <th width="10%">المنتجات</th>
                            <th width="15%">دليل المقاسات</th>
                            <th width="25%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody">
                        {{-- سيتم التعبئة بواسطة JS --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- مودال الإضافة والتعديل --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" id="categoryId">
                    <div class="modal-header" id="modalHeader">
                        <h5 class="modal-title" id="modalTitle">إضافة قسم جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-end">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">اسم القسم</label>
                            <input type="text" name="name" id="catName" class="form-control shadow-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">وصف القسم</label>
                            <textarea name="description" id="catDesc" class="form-control shadow-sm" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">دليل المقاسات المرتبط</label>
                            <select name="size_guide_id" id="catSizeGuide" class="form-select shadow-sm">
                                <option value="">بدون دليل (إلغاء الربط)</option>
                                {{-- سيتم تعبئته لاحقاً --}}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="saveBtn" class="btn btn-primary w-100">حفظ القسم</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- مودال عرض المنتجات --}}
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-info text-dark">
                    <h5 class="modal-title fw-bold" id="quickViewTitle">منتجات القسم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-end">
                    <div id="modalLoading" class="text-center py-4 d-none">
                        <div class="spinner-border text-info" role="status"></div>
                    </div>
                    <div id="modalTableBody"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('assets.js.categories')
