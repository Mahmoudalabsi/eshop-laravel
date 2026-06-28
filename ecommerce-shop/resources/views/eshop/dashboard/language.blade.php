@extends('eshop.layouts.admin')

@section('title', 'إدارة اللغات')

@section('content')
    <div class="dashboard-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-primary">إدارة اللغات</h1>
            <p class="text-muted small mt-2">تحكم في لغات المتجر، أضف لغات جديدة أو عدل اللغات الحالية.</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm d-flex align-items-center gap-2" id="btnAdd">
            <i class="bi bi-plus-lg"></i> إضافة لغة
        </button>
    </div>

    <!-- Languages Card -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase small fw-bold">الاسم</th>
                            <th class="py-3 px-4 text-secondary text-uppercase small fw-bold">الكود</th>
                            <th class="py-3 px-4 text-secondary text-uppercase small fw-bold">العلم</th>
                            <th class="py-3 px-4 text-secondary text-uppercase small fw-bold">الاتجاه</th>
                            <th class="py-3 px-4 text-center text-secondary text-uppercase small fw-bold">الحالة</th>
                            <th class="py-3 px-4 text-center text-secondary text-uppercase small fw-bold">الافتراضية</th>
                            <th class="py-3 px-4 text-center text-secondary text-uppercase small fw-bold">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($languages as $lang)
                            <tr data-id="{{ $lang->id }}" class="border-bottom">
                                <td class="px-4 fw-bold text-dark">{{ $lang->name }}</td>
                                <td class="px-4"><span
                                        class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ $lang->code }}</span>
                                </td>
                                <td class="px-4 fs-5">{{ $lang->flag }}</td>
                                <td class="px-4">
                                    <span
                                        class="badge {{ $lang->direction === 'rtl' ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning' }} rounded-pill px-3">
                                        {{ $lang->direction === 'rtl' ? 'RTL' : 'LTR' }}
                                    </span>
                                </td>
                                <td class="text-center px-4">
                                    <span
                                        class="badge {{ $lang->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3">
                                        {{ $lang->status ? 'مفعّل' : 'معطّل' }}
                                    </span>
                                </td>
                                <td class="text-center px-4">
                                    @if ($lang->is_default)
                                        <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                    @else
                                        <i class="bi bi-dash-circle text-muted fs-5"></i>
                                    @endif
                                </td>
                                <td class="text-center px-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-light text-primary btn-edit shadow-sm border"
                                            data-lang='@json($lang)' title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-light {{ $lang->status ? 'text-warning' : 'text-success' }} btn-toggle-status shadow-sm border"
                                            data-id="{{ $lang->id }}" data-status="{{ $lang->status }}"
                                            title="{{ $lang->status ? 'تعطيل' : 'تفعيل' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                        @if (!$lang->is_default)
                                            <button class="btn btn-sm btn-light text-danger btn-delete shadow-sm border"
                                                data-id="{{ $lang->id }}" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-light rounded-circle p-4 mb-3">
                                            <i class="bi bi-translate fs-1 text-muted"></i>
                                        </div>
                                        <h5 class="text-muted">لا توجد لغات مضافة</h5>
                                        <p class="text-muted small">قم بإضافة لغة جديدة للبدء</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Language Modal -->
    <div class="modal fade" id="languageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-bottom-0 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">إضافة لغة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="languageForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="lang_id">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">اسم اللغة</label>
                            <input type="text" id="lang_name" name="name"
                                class="form-control form-control-lg bg-light border-0" placeholder="مثلاً: العربية"
                                required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">الكود</label>
                                <input type="text" id="lang_code" name="code"
                                    class="form-control form-control-lg bg-light border-0" placeholder="AR" maxlength="3"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">العلم
                                    (اختياري)</label>
                                <input type="text" id="lang_flag" name="flag"
                                    class="form-control form-control-lg bg-light border-0" placeholder="🇸🇦"
                                    maxlength="2">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">الإتجاه</label>
                            <select id="lang_direction" name="direction"
                                class="form-select form-select-lg bg-light border-0" required>
                                <option value="rtl">من اليمين لليسار (RTL)</option>
                                <option value="ltr">من اليسار لليمين (LTR)</option>
                            </select>
                        </div>

                        <div class="form-check form-switch ps-0">
                            <label class="form-check-label fw-bold text-dark ms-2" for="lang_is_default">اجعلها اللغة
                                الافتراضية</label>
                            <input class="form-check-input float-end" type="checkbox" id="lang_is_default"
                                name="is_default">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-light btn-lg px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('languageModal');
            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('languageForm');

            // CSRF setup
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const headers = {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };

            // فتح النموذج للإضافة
            document.getElementById('btnAdd').addEventListener('click', () => {
                form.reset();
                document.getElementById('lang_id').value = '';
                document.getElementById('modalTitle').textContent = 'إضافة لغة جديدة';
                modal.show();
            });

            // فتح النموذج للتعديل
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', () => {
                    const lang = JSON.parse(btn.getAttribute('data-lang'));
                    document.getElementById('lang_id').value = lang.id;
                    document.getElementById('lang_name').value = lang.name;
                    document.getElementById('lang_code').value = lang.code;
                    document.getElementById('lang_flag').value = lang.flag || '';
                    document.getElementById('lang_direction').value = lang.direction;
                    document.getElementById('lang_is_default').checked = !!lang.is_default;
                    document.getElementById('modalTitle').textContent = 'تعديل اللغة';
                    modal.show();
                });
            });

            // حفظ اللغة
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('lang_id').value;
                const url = id ? `/admin/languages/${id}` : '/admin/languages';
                const method = id ? 'PUT' : 'POST';

                const data = {
                    name: document.getElementById('lang_name').value,
                    code: document.getElementById('lang_code').value,
                    flag: document.getElementById('lang_flag').value,
                    direction: document.getElementById('lang_direction').value,
                    is_default: document.getElementById('lang_is_default').checked ? 1 : 0
                };

                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: headers,
                        body: JSON.stringify(data)
                    });

                    const result = await res.json();
                    if (!res.ok) throw new Error(result.message || 'حدث خطأ غير متوقع');

                    // Toast or Reload
                    location.reload();
                } catch (err) {
                    alert(err.message);
                    console.error(err);
                }
            });

            // تبديل الحالة
            document.querySelectorAll('.btn-toggle-status').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-id');
                    const currentStatus = btn.getAttribute('data-status') == '1';
                    const newStatus = !currentStatus;

                    try {
                        const res = await fetch(`/admin/languages/${id}/status`, {
                            method: 'POST',
                            headers: headers,
                            body: JSON.stringify({
                                status: newStatus ? 1 : 0
                            })
                        });

                        if (!res.ok) {
                            const result = await res.json();
                            throw new Error(result.message || 'فشل تبديل الحالة');
                        }
                        location.reload();
                    } catch (err) {
                        alert(err.message);
                        console.error(err);
                    }
                });
            });

            // حذف اللغة
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', async () => {
                    if (!confirm('هل أنت متأكد من حذف هذه اللغة؟')) return;

                    const id = btn.getAttribute('data-id');
                    try {
                        const res = await fetch(`/admin/languages/${id}`, {
                            method: 'DELETE',
                            headers: headers
                        });

                        if (!res.ok) {
                            const result = await res.json();
                            throw new Error(result.message || 'فشل الحذف');
                        }
                        location.reload();
                    } catch (err) {
                        alert(err.message);
                        console.error(err);
                    }
                });
            });
        });
    </script>
@endpush
