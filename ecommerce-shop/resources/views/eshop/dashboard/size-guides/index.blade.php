@extends('eshop.layouts.admin')

@section('title', 'أدلة المقاسات')

@push('css')
    <style>
        .clothing-row .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .clothing-row .btn-outline-danger {
            border-radius: 8px;
        }

        .guide-type-section {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px dashed #ced4da;
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2">إدارة أدلة المقاسات</h1>
        <div class="d-flex gap-3 align-items-center">
            <div style="min-width: 250px;">
                <input type="text" id="guideSearchInput" class="form-control shadow-sm text-end"
                    placeholder="🔍 ابحث باسم الدليل...">
            </div>
            <button type="button" class="btn btn-primary shadow-sm" onclick="openGuideModal('add')">
                <i class="bi bi-plus-circle"></i> إضافة دليل جديد
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
                            <th width="30%">اسم الدليل</th>
                            <th width="40%">المحتوى (HTML)</th>
                            <th width="25%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="guidesTableBody">
                        {{-- سيتم التعبئة بواسطة JS --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- مودال الإضافة والتعديل --}}
    <div class="modal fade" id="guideModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="guideForm">
                    @csrf
                    <input type="hidden" id="guideId">
                    <div class="modal-header" id="guideModalHeader">
                        <h5 class="modal-title" id="guideModalTitle">إضافة دليل جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-end">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">اسم الدليل (مثلاً: ملابس داخلية، أحذية)</label>
                            <input type="text" name="name" id="guideName" class="form-control shadow-sm" required
                                autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">نوع الدليل</label>
                            <select id="guideType" class="form-select shadow-sm" onchange="toggleGuideInputs()">
                                <option value="clothing">ملابس (نوع القياس و القيمة)</option>
                                <option value="shoes">أحذية (مقاسات فقط)</option>
                                <option value="custom">مخصص (HTML)</option>
                            </select>
                        </div>

                        {{-- واجهة الملابس --}}
                        <div id="clothingInputs" class="guide-type-section">
                            <label class="form-label fw-bold small d-block mb-2">جدول القياسات للملابس</label>
                            <div id="clothingRows">
                                {{-- سيتم إضافة الصفوف هنا --}}
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addClothingRow()">
                                <i class="bi bi-plus"></i> إضافة نوع قياس جديد
                            </button>
                        </div>

                        {{-- واجهة الأحذية --}}
                        <div id="shoesInputs" class="guide-type-section d-none">
                            <label class="form-label fw-bold small d-block mb-2">مقاسات الأحذية المتوفرة</label>
                            <input type="text" id="shoeSizes" class="form-control shadow-sm"
                                placeholder="مثال: 38, 39, 40, 41">
                            <div class="form-text mt-1 small">أدخل المقاسات مفصولة بفاصلة (,)</div>
                        </div>

                        {{-- الواجهة المخصصة --}}
                        <div id="customInputs" class="guide-type-section d-none">
                            <label class="form-label fw-bold small text-uppercase">محتوى مخصص (HTML)</label>
                            <textarea id="rawHtmlContent" rows="10" class="form-control shadow-sm font-monospace"
                                placeholder="أدخل كود HTML للجدول هنا..."></textarea>
                        </div>

                        <textarea name="content" id="guideContent" class="d-none"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="guideSaveBtn" class="btn btn-primary w-100">حفظ الدليل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@include('assets.js.size-guides')
