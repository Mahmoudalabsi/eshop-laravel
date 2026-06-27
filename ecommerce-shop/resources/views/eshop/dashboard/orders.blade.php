@extends('eshop.layouts.admin')
@section( "title","إدارة الطلبات")

@section('content')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2">إدارة طلبات العملاء</h1>
        <div class="d-flex gap-2 align-items-center" style="min-width: 300px;">
            <div class="d-flex gap-2 align-items-center" style="min-width: 400px;">
                <button onclick="forceRefresh()" id="refreshBtn" class="btn btn-outline-dark shadow-sm">
                    <i class="bi bi-arrow-clockwise" id="refreshIcon"></i> <i class="fa fa-refresh" aria-hidden="true"></i>
                </button>
                <input type="text" id="orderSearchInput" class="form-control shadow-sm text-end" placeholder="🔍Search">
            </div>

        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody"></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content text-end border-0 shadow-lg">
                <div class="modal-header bg-primary text-white flex-row-reverse">
                    <h5 class="modal-title">تفاصيل الطلب <span id="modalOrderId"></span></h5>
                    <button type="button" class="btn-close btn-close-white ms-0 me-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="orderDetailsContent">
                        <div class="text-center py-3" id="orderDetailsLoading">{{ __('Loading...') }}</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('assets.js.orders')
