@extends('eshop.layouts.admin')
@include('assets.css.style')

@section("title","إدارة العروض والخصومات")

@section('content')
    {{-- رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2">إدارة العروض والخصومات</h1>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
            data-bs-target="#offerModal" onclick="resetOfferForm()"> {{-- تأكد أنها resetOfferForm وليس prepareModal --}}
            <i class="bi bi-plus-circle"></i> إضافة عرض جديد
        </button>
    </div>

    {{-- جدول العروض --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th>اسم العرض</th>
                        <th>الخصم</th>
                        <th>النطاق (Scope)</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="offersTableBody">
                    @forelse($offers as $offer)
                        <tr>
                            <td class="fw-bold">{{ $offer->name }}</td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger">
                                    {{ $offer->discount_value }}{{ $offer->type == 'percentage' ? '%' : ' د.أ' }}
                                </span>
                            </td>
                            <td><span class="badge bg-info text-dark">{{ $offer->scope }}</span></td>
                            <td><small>{{ $offer->starts_at }}</small></td>
                            <td><small>{{ $offer->ends_at }}</small></td>
                            <td>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input"
                                        onchange="toggleOfferStatus({{ $offer->id }}, this)"
                                        {{ $offer->status ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick='editOffer(@json($offer))'>
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteOffer({{ $offer->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">لا توجد عروض حالياً</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="modalTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-end border-0 shadow-lg">
            <div class="modal-header text-white" id="modalHeader">
                <h5 class="modal-title" id="modalTitle">إعداد عرض جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="offerForm">
                    @csrf
                    {{-- حقول مخفية للتحكم في نوع العملية --}}
                    <input type="hidden" name="offer_id" id="o_id">
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">اسم العرض</label>
                                <input type="text" name="name" id="o_name" class="form-control"
                                    placeholder="مثلاً: خصومات الصيف" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">نوع الخصم</label>
                                <select name="type" id="o_type" class="form-select">
                                    <option value="percentage">نسبة مئوية (%)</option>
                                    <option value="fixed">مبلغ ثابت</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">القيمة</label>
                                <input type="number" name="discount_value" id="o_discount" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">نطاق التطبيق (Scope)</label>
                                <select name="scope" id="o_scope" class="form-select" onchange="handleScopeChange()">
                                    <option value="all">كامل المتجر</option>
                                    <option value="category">قسم رئيسي كامل</option>
                                    <option value="subcategory">قسم فرعي محدد</option>
                                    <option value="product">منتجات محددة</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="targetContainer" style="display: none;">
                                <label class="form-label fw-bold" id="targetLabel">اختر الهدف</label>
                                <select name="target_id" id="o_target_id" class="form-select">
                                    {{-- يتم تعبئته عبر JS --}}
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">تاريخ البدء</label>
                                <input type="datetime-local" name="starts_at" id="o_starts_at" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-danger">تاريخ الانتهاء</label>
                                <input type="datetime-local" name="ends_at" id="o_ends_at" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">تفعيل العرض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- تم استبدال extends بـ include لأنها الطريقة الصحيحة لاستدعاء ملفات فرعية داخل الـ section --}}
    @include('assets.js.offers')
@endsection
