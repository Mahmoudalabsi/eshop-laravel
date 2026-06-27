@extends('eshop.layouts.admin')
@section("title","إدارة المستخدمين")

@section('content')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2 page-title">إدارة جميع الأعضاء</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill"></i> إضافة مستخدم جديد
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 text-end section-card">
        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="mb-0 fw-bold text-dark-mode-white">قائمة المستخدمين <span id="userCount"
                    class="badge bg-primary ms-2">0</span></h6>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" id="userSearchInput"
                        class="form-control form-control-sm shadow-sm text-end bg-input-dark"
                        placeholder="🔍 ابحث بالاسم، البريد، أو الرتبة...">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center custom-table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الحالة</th>
                        <th>الرتبة</th>
                        <th>تاريخ التسجيل</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="usersList">
                    {{-- Filled by JavaScript (rendered dynamically) --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add User Modal --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg text-end">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title">إضافة عضو جديد للنظام</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addUserForm">
                    @csrf
                    <div class="modal-body p-4 bg-modal-dark">
                        <div class="mb-3">
                            <label class="form-label fw-bold">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control shadow-sm bg-input-dark" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control shadow-sm bg-input-dark" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">كلمة المرور</label>
                                <input type="password" name="password" class="form-control shadow-sm bg-input-dark"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">تأكيد الكلمة</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control shadow-sm bg-input-dark" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">صلاحية المستخدم</label>
                            <select name="role" class="form-select shadow-sm bg-input-dark">
                                <option value="user">مشتري (User)</option>
                                <option value="admin">مدير (Admin)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light-dark border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary px-4">حفظ البيانات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@include('assets.js.users')
