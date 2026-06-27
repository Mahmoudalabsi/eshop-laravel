@extends('eshop.layouts.admin')

@section('title', 'إدارة العملات')

@section('content')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2">إدارة العملات وأسعار الصرف</h1>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
            data-bs-target="#currencyModal" onclick="resetCurrencyForm()">
            <i class="bi bi-plus-circle"></i> إضافة عملة جديدة
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th>اسم العملة</th>
                        <th>الكود (ISO)</th>
                        <th>الرمز</th>
                        <th>سعر الصرف (مقابل العملة الأساسية)</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="currenciesTableBody">
                    {{-- يتم التعبئة عبر JS --}}
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="currencyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-end">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">إضافة عملة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="currencyForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="c_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم العملة</label>
                            <input type="text" id="c_name" name="name" class="form-control"
                                placeholder="مثلاً: ريال سعودي" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الكود (Code)</label>
                                <input type="text" id="c_code" name="code" class="form-control" placeholder="SAR"
                                    maxlength="3" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الرمز (Symbol)</label>
                                <input type="text" id="c_symbol" name="symbol" class="form-control" placeholder="ر.س"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">سعر الصرف</label>
                            <input type="number" id="c_exchange_rate" name="exchange_rate" class="form-control"
                                step="0.000001" required>
                            <small class="text-muted">بالنسبة للعملة الأساسية (الأساسية = 1.0)</small>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="c_is_default" name="is_default">
                            <label class="form-check-label fw-bold me-4" for="c_is_default">تعيين كعملة أساسية
                                للمتجر</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('assets.js.currencies')
@endsection
