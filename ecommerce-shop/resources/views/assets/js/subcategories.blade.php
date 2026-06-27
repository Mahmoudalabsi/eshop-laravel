<script>
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

    let allSubcategories = []; // لتخزين كافة الأقسام الفرعية للبحث والفلترة

    // 1. تحميل الأقسام الفرعية من السيرفر
    async function loadSubcategories() {
        const tbody = document.getElementById('subcategoriesTableBody');
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="py-5 text-center">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">جاري تحميل الأقسام الفرعية...</div>
                </td>
            </tr>`;

        try {
            const res = await fetch("/admin/get-subcategories"); // المسار الذي عرفناه في web.php
            const result = await res.json();

            allSubcategories = result.data ? result.data : (Array.isArray(result) ? result : []);
            renderSubcategories(allSubcategories);
        } catch (e) {
            console.error("Error loading subcategories:", e);
            tbody.innerHTML =
                '<tr><td colspan="6" class="text-danger py-4 text-center">فشل في تحميل البيانات</td></tr>';
        }
    }

    // 2. وظيفة الرسم (Render) في الجدول
    function renderSubcategories(subcategories) {
        const tbody = document.getElementById('subcategoriesTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (subcategories.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="py-4 text-muted text-center">لا توجد نتائج مطابقة</td></tr>';
            return;
        }

        subcategories.forEach(sub => {
            const isChecked = sub.status == 1 ? 'checked' : '';
            // جلب اسم القسم الرئيسي المرتبط
            const parentCategoryName = sub.category ? sub.category.name :
                '<span class="text-danger">غير مرتبط</span>';

            tbody.innerHTML += `
            <tr id="sub-row-${sub.id}">
                <td>${sub.id}</td>
                <td class="fw-bold">${sub.name}</td>
                <td>
                    <span class="badge bg-light text-primary border border-primary-subtle px-3">
                        <i class="bi bi-folder2-open me-1"></i> ${parentCategoryName}
                    </span>
                </td>
                <td>
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox"
                            ${isChecked}
                            onchange="toggleSubStatus(${sub.id}, this)"
                            style="cursor: pointer;">
                    </div>
                </td>
                <td><span class="badge bg-info text-dark rounded-pill">${sub.products_count || 0} منتج</span></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-info m-1" title="عرض المنتجات" onclick="viewSubProducts(${sub.id}, '${sub.name}')">
                        <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning m-1" title="تعديل" onclick='prepareSubEdit(${JSON.stringify(sub)})'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger m-1" title="حذف" onclick="deleteSubcategory(${sub.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
    }

    // 3. محرك البحث اللحظي
    function applySubSearch() {
        const searchInput = document.getElementById('subSearchInput');
        if (!searchInput) return;

        const term = searchInput.value.toLowerCase();
        const filtered = allSubcategories.filter(sub => {
            return (
                sub.name.toLowerCase().includes(term) ||
                (sub.category && sub.category.name.toLowerCase().includes(term)) ||
                sub.id.toString().includes(term)
            );
        });
        renderSubcategories(filtered);
    }
    document.addEventListener('DOMContentLoaded', function() {
        const subForm = document.getElementById('addSubForm');

        if (subForm) {
            subForm.onsubmit = async function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('subSaveBtn');
                const formData = new FormData(this);
                const id = document.getElementById('subId').value;

                // تحديد المسار بناءً على ما إذا كانت العملية إضافة أم تعديل
                const url = id ? `/admin/subcategory-update/${id}` : "/admin/subcategory-store";

                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await res.json();

                    if (res.ok) {
                        swalSuccess(result.message || 'تمت العملية بنجاح');
                        bootstrap.Modal.getInstance(document.getElementById('addSubModal')).hide();
                        loadSubcategories(); // دالة تحديث الجدول
                        this.reset();
                    } else {
                        swalError("خطأ: " + (result.message || "فشل في حفظ البيانات"));
                    }
                } catch (error) {
                    console.error("Error:", error);
                    swalError("حدث خطأ في الاتصال بالسيرفر");
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'حفظ القسم';
                }
            };
        }
    });

    function openSubModal() {
        // 1. تفريغ الفورم باستخدام المعرف الصحيح addSubForm
        const form = document.getElementById('addSubForm');
        if (form) form.reset();

        // 2. تفريغ الحقل المخفي subId
        const subIdInput = document.getElementById('subId');
        if (subIdInput) subIdInput.value = '';

        // 3. تحديث النصوص (العنوان والزر)
        const title = document.getElementById('subModalTitle');
        if (title) title.innerText = 'إضافة قسم فرعي جديد';

        const saveBtn = document.getElementById('subSaveBtn');
        if (saveBtn) saveBtn.innerText = 'حفظ القسم';

        // 4. إظهار المودال باستخدام المعرف addSubModal
        const modalElem = document.getElementById('addSubModal');
        if (modalElem) {
            bootstrap.Modal.getOrCreateInstance(modalElem).show();
        } else {
            console.error("خطأ: المودال addSubModal غير موجود في الصفحة");
        }
    }

    function prepareSubEdit(sub) {
        // تحديث عناوين المودال
        document.getElementById('subModalTitle').innerText = 'تعديل القسم: ' + sub.name;
        document.getElementById('subSaveBtn').innerText = 'تحديث البيانات';

        // ملء الحقول (تأكد أن الـ IDs تطابق الـ HTML)
        document.getElementById('subId').value = sub.id;

        // الحقل في الـ HTML اسمه subNameInput وليس subName
        const nameInput = document.getElementById('subNameInput');
        if (nameInput) nameInput.value = sub.name;

        // السلكت في الـ HTML اسمه mainCategorySelect وليس mainCategoryId
        const selectCat = document.getElementById('mainCategorySelect');
        if (selectCat) selectCat.value = sub.category_id;

        // إظهار المودال addSubModal
        const modalElem = document.getElementById('addSubModal');
        bootstrap.Modal.getOrCreateInstance(modalElem).show();
    }
    // 5. حفظ أو تحديث
    // 5. حفظ أو تحديث الأقسام الفرعية
    // استخدمنا addSubForm بدلاً من subcategoryForm
    const subForm = document.getElementById('addSubForm');

    if (subForm) {
        subForm.onsubmit = async function(e) {
            e.preventDefault();

            const id = document.getElementById('subId').value;
            const btn = document.getElementById('subSaveBtn');

            // المسارات الصحيحة بناءً على ملف web.php الخاص بك
            const url = id ? `/admin/subcategory-update/${id}` : "/admin/subcategory-store";

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

            try {
                // تحويل البيانات من الفورم
                const formData = new FormData(this);

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        // الأفضل جلب التوكن من الميتا تاغ لضمان عدم حدوث خطأ 419
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: formData // إرسال FormData مباشرة (لا تستخدم JSON.stringify هنا)
                });

                const result = await res.json();

                if (res.ok) {
                    // إغلاق المودال بطريقة Bootstrap 5 الصحيحة
                    const modalElem = document.getElementById('addSubModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElem);
                    if (modalInstance) modalInstance.hide();

                    loadSubcategories(); // إعادة تحميل الجدول
                    swalSuccess(result.message || 'تمت العملية بنجاح');
                    this.reset();
                } else {
                    // معالجة أخطاء التحقق (Validation Errors)
                    if (result.errors) {
                        let msg = Object.values(result.errors).flat().join('\n');
                        swalError('خطأ في البيانات:\n' + msg);
                    } else {
                        swalError('فشل الحفظ: ' + (result.message || 'حدث خطأ غير معروف'));
                    }
                }
            } catch (error) {
                console.error("Save error:", error);
                swalError('تعذر الاتصال بالسيرفر. تأكد من أن المسار صحيح في ملف الروابط');
            } finally {
                btn.disabled = false;
                btn.innerText = id ? 'تحديث البيانات' : 'حفظ القسم';
            }
        };
    }
    async function viewSubProducts(subId, subName) {
        document.getElementById('subProductsTitle').innerText = "منتجات: " + subName;
        const content = document.getElementById('subProductsContent');
        const loader = document.getElementById('subProductsLoading');

        content.innerHTML = '';
        loader.classList.remove('d-none');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('subProductsModal')).show();

        try {
            const res = await fetch(`/admin/subcategories/${subId}/products`);
            const result = await res.json();
            const products = result.data || result;

            loader.classList.add('d-none');

            if (products.length > 0) {
                let html = `
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle border text-center">
                        <thead class="table-light">
                            <tr>
                                <th>الصورة</th>
                                <th class="text-end">المنتج</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>`;

                products.forEach(p => {
                    // تعريف المسار الأساسي للصورة الافتراضية (تأكد من وجودها في public/assets/img/no-image.png)
                    const defaultImg = '/assets/img/no-image.png';

                    // إذا كانت الصورة موجودة في قاعدة البيانات نضع مسار storage، وإلا نضع الصورة الافتراضية فوراً
                    const imagePath = getImageUrl(p.image);

                    html += `
                <tr>
                    <td>
                        <img src="${imagePath}"
                             width="45"
                             height="45"
                             class="rounded border shadow-sm"
                             style="object-fit: cover;"
                             onerror="this.onerror=null; this.src='${defaultImg}';">
                    </td>
                    <td class="small fw-bold text-end">${p.name}</td>
                    <td class="text-primary fw-bold">${p.price} $</td>
                    <td>
                        ${p.status == 1
                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2">نشط</span>'
                            : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">معطل</span>'}
                    </td>
                </tr>`;
                });

                html += `</tbody></table></div>`;
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-2 text-muted">لا توجد منتجات مرتبطة بهذا القسم حالياً.</p>
                </div>`;
            }
        } catch (e) {
            console.error("Error:", e);
            content.innerHTML =
                '<div class="alert alert-danger small m-3">حدث خطأ أثناء تحميل بيانات المنتجات، يرجى المحاولة لاحقاً.</div>';
        }
    }
    async function toggleSubStatus(id, element) {
        const statusValue = element.checked ? 1 : 0;

        try {
            const response = await fetch(`/admin/subcategory-status/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // تأكد أن هذا السطر يولد Token فعلي
                },
                body: JSON.stringify({
                    status: statusValue
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'فشل في تحديث البيانات');
            }

            console.log(result.message);

        } catch (error) {
            // إعادة الزر لحالته السابقة عند الفشل
            element.checked = !element.checked;
            swalError(error.message);
        }
    } // 7. الحذف
    async function deleteSubcategory(id) {
        try {
            if (typeof Swal !== 'undefined') {
                const confirmed = await Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'سيتم حذف القسم وجميع ارتباطاته بالمنتجات، هل أنت متأكد؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                });
                if (!confirmed.isConfirmed) return;
            } else {
                if (!confirm('سيتم حذف القسم وجميع ارتباطاته بالمنتجات، هل أنت متأكد؟')) return;
            }

            const res = await fetch(`/admin/subcategory-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (res.ok) {
                swalSuccess('تم الحذف بنجاح');
                loadSubcategories();
            }
        } catch (error) {
            console.error("Delete error:", error);
            swalError('حدث خطأ أثناء الحذف');
        }
    }

    // تشغيل عند التحميل
    document.addEventListener('DOMContentLoaded', () => {
        loadSubcategories();
        const searchInput = document.getElementById('subSearchInput');
        if (searchInput) searchInput.addEventListener('input', applySubSearch);
    });

    function applySubSearchOnly() {
        const searchEl = document.getElementById('subSearchInput');

        if (!searchEl) return;

        const term = searchEl.value.toLowerCase().trim();

        const filtered = allSubcategories.filter(sub => {
            // البحث في اسم القسم الفرعي
            const subName = sub.name ? sub.name.toLowerCase() : '';

            // البحث في اسم القسم الرئيسي المرتبط (category.name)
            const parentName = (sub.category && sub.category.name) ? sub.category.name.toLowerCase() : '';

            // البحث في الرقم التسلسلي
            const id = sub.id ? sub.id.toString() : '';

            return subName.includes(term) || parentName.includes(term) || id.includes(term);
        });

        renderSubcategories(filtered);
    }
</script>
