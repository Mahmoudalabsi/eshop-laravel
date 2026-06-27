<script>
    let allCategories = []; // لتخزين كافة الأقسام للبحث والفلترة

    // SweetAlert helpers with fallback to native alert
    function swalSuccess(message = 'تمت العملية بنجاح') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'نجاح',
                text: message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    function swalError(message = 'حدث خطأ') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: message
            });
        } else {
            alert(message);
        }
    }

    // 1. تحميل الأقسام من السيرفر
    async function loadCategories() {
        const tbody = document.getElementById('categoriesTableBody');
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-5 text-center">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">جاري تحميل الأقسام...</div>
                </td>
            </tr>`;

        try {
            // Load size guides first to populate dropdowns
            await loadSizeGuidesForSelect();

            const res = await fetch("{{ route('categories.json') }}");
            const result = await res.json();

            // التعامل مع البيانات سواء كانت داخل result.data أو مصفوفة مباشرة
            allCategories = result.data ? result.data : (Array.isArray(result) ? result : []);

            renderCategories(allCategories);
        } catch (e) {
            console.error("Error loading categories:", e);
            tbody.innerHTML =
                '<tr><td colspan="7" class="text-danger py-4 text-center">فشل في تحميل البيانات، تأكد من الاتصال بالسيرفر</td></tr>';
        }
    }

    async function loadSizeGuidesForSelect() {
        const select = document.getElementById('catSizeGuide');
        if (!select) return;

        try {
            const res = await fetch("{{ route('size-guides.json') }}");
            const guides = await res.json();

            let html = '<option value="">بدون دليل (إلغاء الربط)</option>';
            guides.forEach(g => {
                html += `<option value="${g.id}">${g.name}</option>`;
            });
            select.innerHTML = html;
        } catch (e) {
            console.error("Error loading size guides for select:", e);
        }
    }

    function renderCategories(categories) {
        const tbody = document.getElementById('categoriesTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (categories.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="7" class="py-4 text-muted text-center">لا توجد نتائج مطابقة لبحثك</td></tr>';
            return;
        }

        categories.forEach(cat => {
            const description = cat.description ?
                (cat.description.length > 50 ? cat.description.substring(0, 50) + '...' : cat.description) :
                '---';

            const isChecked = cat.status == 1 ? 'checked' : '';
            const sizeGuideName = cat.size_guide ? cat.size_guide.name :
                '<span class="text-muted small">---</span>';

            tbody.innerHTML += `
            <tr>
                <td>${cat.id}</td>
                <td class="fw-bold">${cat.name}</td>
                <td>
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox"
                            ${isChecked}
                            onchange="toggleCategoryStatus(${cat.id}, this)"
                            style="cursor: pointer;">
                    </div>
                </td>
                <td class="text-muted small text-end">${description}</td>
                <td><span class="badge bg-info text-dark rounded-pill">${cat.products_count || 0} منتج</span></td>
                <td>${sizeGuideName}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary m-1" title="عرض الأقسام الفرعية" onclick="viewSubcategories(${cat.id}, '${cat.name}')">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning m-1" title="تعديل" onclick='prepareEdit(${JSON.stringify(cat).replace(/'/g, "&apos;")})'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger m-1" title="حذف" onclick="deleteCategory(${cat.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
    }

    // 3. عرض الأقسام الفرعية (Quick View)
    async function viewSubcategories(id, name) {
        document.getElementById('quickViewTitle').innerText = "الأقسام الفرعية لـ: " + name;
        const tableBody = document.getElementById('modalTableBody');
        const loadingSpinner = document.getElementById('modalLoading');

        tableBody.innerHTML = '';
        loadingSpinner.classList.remove('d-none');

        bootstrap.Modal.getOrCreateInstance(document.getElementById('quickViewModal')).show();

        try {
            const res = await fetch(`/admin/categories/${id}/subcategories`);

            if (!res.ok) throw new Error('Route not found');

            const result = await res.json();
            const data = result.data ? result.data : result;

            loadingSpinner.classList.add('d-none');

            if (data && data.length > 0) {
                let tableHtml = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle border text-center shadow-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-end">اسم القسم الفرعي</th>
                                <th>عدد المنتجات</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>`;

                data.forEach(sub => {
                    tableHtml += `
                    <tr>
                        <td>${sub.id}</td>
                        <td class="text-end fw-bold">${sub.name}</td>
                        <td><span class="badge bg-info text-dark">${sub.products_count || 0} منتج</span></td>
                        <td>
                            ${sub.status == 1
                                ? '<span class="text-success small"><i class="bi bi-check-circle-fill"></i> نشط</span>'
                                : '<span class="text-danger small"><i class="bi bi-x-circle-fill"></i> معطل</span>'}
                        </td>
                    </tr>`;
                });

                tableHtml += `</tbody></table></div>`;
                tableBody.innerHTML = tableHtml;
            } else {
                tableBody.innerHTML =
                    '<div class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> لا توجد أقسام فرعية لهذا القسم حالياً.</div>';
            }
        } catch (error) {
            console.error("Fetch error:", error);
            loadingSpinner.classList.add('d-none');
            tableBody.innerHTML =
                '<div class="alert alert-danger shadow-sm">خطأ في جلب بيانات الأقسام الفرعية. تأكد من إعداد الـ Route والـ Controller بشكل صحيح.</div>';
        }
    }

    // 4. محرك البحث اللحظي
    function applyCategorySearch() {
        const searchInput = document.getElementById('categorySearchInput');
        if (!searchInput) return;

        const term = searchInput.value.toLowerCase();
        const filtered = allCategories.filter(cat => {
            return (
                cat.name.toLowerCase().includes(term) ||
                (cat.description && cat.description.toLowerCase().includes(term)) ||
                cat.id.toString().includes(term)
            );
        });
        renderCategories(filtered);
    }
    document.addEventListener('DOMContentLoaded', function() {
        const categoryForm = document.getElementById('categoryForm');

        if (categoryForm) {
            categoryForm.onsubmit = async function(e) {
                e.preventDefault();

                const id = document.getElementById('categoryId').value;
                const btn = document.getElementById('saveBtn');

                // تحديد الرابط: إذا وجد ID فهو تحديث، وإلا فهو إضافة
                const url = id ? `/admin/categories-update/${id}` : "/admin/categories-store";

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

                try {
                    const formData = new FormData(this);
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            // جلب التوكن من الـ Meta tag لضمان عدم انتهاء الجلسة
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await res.json();

                    if (res.ok) {
                        // إغلاق المودال وتحديث الجدول
                        const modalEl = document.getElementById('categoryModal');
                        bootstrap.Modal.getInstance(modalEl).hide();

                        loadCategories(); // استدعاء دالة التحديث الموجودة في كودك
                        swalSuccess('تمت العملية بنجاح');
                    } else {
                        swalError(result.message || 'خطأ في البيانات المدخلة');
                    }
                } catch (error) {
                    console.error("Save error:", error);
                    swalError('حدث خطأ في الاتصال بالسيرفر');
                } finally {
                    btn.disabled = false;
                    btn.innerText = id ? 'تحديث البيانات' : 'حفظ القسم';
                }
            };
        }
    });
    // 5. التحكم في مودال الإضافة
    function openModal() {
        const form = document.getElementById('categoryForm');
        if (form) form.reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('modalTitle').innerText = 'إضافة قسم جديد';
        document.getElementById('modalHeader').className = 'modal-header bg-primary text-white';
        const btn = document.getElementById('saveBtn');
        btn.className = 'btn btn-primary w-100';
        btn.innerText = 'حفظ القسم';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal')).show();
    }

    // 6. التحضير للتعديل
    function prepareEdit(cat) {
        document.getElementById('modalTitle').innerText = 'تعديل القسم: ' + cat.name;
        document.getElementById('modalHeader').className = 'modal-header bg-warning text-dark';
        const btn = document.getElementById('saveBtn');
        btn.className = 'btn btn-warning w-100';
        btn.innerText = 'تحديث البيانات';

        document.getElementById('categoryId').value = cat.id;
        document.getElementById('catName').value = cat.name;
        document.getElementById('catDesc').value = cat.description || '';
        document.getElementById('catSizeGuide').value = cat.size_guide_id || '';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal')).show();
    }

    // 7. حفظ أو تحديث القسم
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.onsubmit = async function(e) {
            e.preventDefault();
            const id = document.getElementById('categoryId').value;
            const btn = document.getElementById('saveBtn');

            // تأكد أن هذه الروابط مطابقة تماماً لملف الـ Routes
            const url = id ? `/admin/categories-update/${id}` : "/admin/categories-store";

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

            try {
                const formData = new FormData(this);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json' // هذا يخبر لارافل بأننا نريد رد JSON
                    },
                    body: formData
                });

                const result = await res.json();

                if (res.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                    loadCategories();
                    swalSuccess('تمت العملية بنجاح');
                } else {
                    // إظهار رسالة الخطأ القادمة من الكنترولر (Validation)
                    swalError(result.message || 'خطأ في البيانات المدخلة');
                }
            } catch (error) {
                console.error("Save error:", error);
                swalError('حدث خطأ في الاتصال بالسيرفر، تأكد من الروابط في ملف web.php');
            } finally {
                btn.disabled = false;
                btn.innerText = id ? 'تحديث البيانات' : 'حفظ القسم';
            }
        };
    }

    // 8. الحذف
    async function deleteCategory(id) {
        try {
            if (typeof Swal !== 'undefined') {
                const confirmed = await Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد من حذف هذا القسم نهائياً؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                });
                if (!confirmed.isConfirmed) return;
            } else {
                if (!confirm('هل أنت متأكد من حذف هذا القسم نهائياً؟')) return;
            }

            const res = await fetch(`/admin/categories-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (res.ok) loadCategories();
            else swalError('فشل الحذف، قد يكون القسم مرتبطاً بمنتجات');
        } catch (error) {
            console.error("Delete error:", error);
            swalError('حدث خطأ أثناء حذف القسم');
        }
    }

    // 9. تغيير الحالة
    async function toggleCategoryStatus(id, element) {
        const statusValue = element.checked ? 1 : 0;
        try {
            const response = await fetch(`/admin/categories/update-status/${id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: statusValue
                })
            });

            if (!response.ok) throw new Error('فشل التحديث');
        } catch (error) {
            element.checked = !element.checked;
            swalError('حدث خطأ أثناء تحديث حالة القسم');
        }
    }

    // تشغيل عند التحميل
    document.addEventListener('DOMContentLoaded', () => {
        loadCategories();
        const searchInput = document.getElementById('categorySearchInput');
        if (searchInput) searchInput.addEventListener('input', applyCategorySearch);
    });
</script>
