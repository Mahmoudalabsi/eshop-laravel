@extends('eshop.layouts.admin') {{-- تأكد من اسم الليوت الخاص بك --}}
@section('title', 'الأقسام الفرعية')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
                        <h1 class="h2">إدارة الأقسام الفرعية</h1>

                        <div class="d-flex gap-3 align-items-center">
                            <div style="min-width: 300px;">
                                <input type="text" id="subSearchInput" class="form-control shadow-sm text-end"
                                    placeholder="🔍 ابحث باسم القسم الفرعي أو الرئيسي..." oninput="applySubSearchOnly()">
                            </div>

                            <button type="button" class="btn btn-primary shadow-sm d-flex align-items-center gap-2"
                                onclick="openSubModal()">
                                <i class="bi bi-plus-circle"></i> إضافة قسم فرعي
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center border" id="subcategoriesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%;">الرقم</th>
                                        <th style="width: 25%;">اسم القسم الفرعي</th>
                                        <th style="width: 25%;">القسم الرئيسي</th>
                                        <th style="width: 15%;">الحالة</th>
                                        <th style="width: 15%;">عدد المنتجات</th>
                                        <th style="width: 10%;">العمليات</th>
                                    </tr>
                                </thead>
                                <tbody id="subcategoriesTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSubModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="addSubForm">
                    @csrf
                    <input type="hidden" id="subId" name="id">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="subModalTitle">إضافة قسم فرعي</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-end">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم القسم الفرعي</label>
                            <input type="text" name="name" id="subNameInput" class="form-control" required
                                placeholder="مثلاً: قمصان">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر القسم الرئيسي</label>
                            <select name="category_id" id="mainCategorySelect" class="form-select shadow-sm" required>
                                <option value="">اختر القسم الرئيسي...</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" id="subSaveBtn" class="btn btn-primary">حفظ القسم</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- مودال عرض منتجات القسم الفرعي --}}
    <div class="modal fade" id="subProductsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-dark">
                    <h5 class="modal-title fw-bold" id="subProductsTitle">منتجات القسم الفرعي</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-end">
                    <div id="subProductsLoading" class="text-center py-4 d-none">
                        <div class="spinner-border text-info" role="status"></div>
                    </div>
                    <div id="subProductsContent"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('assets.js.subcategories')
