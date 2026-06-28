<script>
    let allGuides = [];

    // --- توابع الواجهة الجديدة ---

    function toggleGuideInputs() {
        const type = document.getElementById('guideType').value;
        document.querySelectorAll('.guide-type-section').forEach(el => el.classList.add('d-none'));

        if (type === 'clothing') {
            document.getElementById('clothingInputs').classList.remove('d-none');
        } else if (type === 'shoes') {
            document.getElementById('shoesInputs').classList.remove('d-none');
        } else {
            document.getElementById('customInputs').classList.remove('d-none');
        }
    }

    function addClothingRow(label = '', value = '') {
        const container = document.getElementById('clothingRows');
        const rowId = Date.now() + Math.random();

        const rowHtml = `
            <div class="row g-2 mb-2 align-items-center clothing-row" id="row-${rowId}">
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm measurement-label" placeholder="نوع القياس (مثلاً: الصدر)" value="${label}">
                </div>
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm measurement-value" placeholder="القيمة (مثلاً: 90-95 سم)" value="${value}">
                </div>
                <div class="col-2 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById('row-${rowId}').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>`;

        container.insertAdjacentHTML('beforeend', rowHtml);
    }

    function generateFinalHtml() {
        const type = document.getElementById('guideType').value;
        let html = '';
        let sourceData = {
            type: type,
            data: {}
        };

        if (type === 'clothing') {
            const rows = document.querySelectorAll('.clothing-row');
            let tableBody = '';
            let dataRows = [];

            rows.forEach(row => {
                const label = row.querySelector('.measurement-label').value.trim();
                const val = row.querySelector('.measurement-value').value.trim();
                if (label || val) {
                    tableBody +=
                        `<tr><td class="fw-bold bg-light" style="width: 40%">${label}</td><td>${val}</td></tr>`;
                    dataRows.push({
                        label: label,
                        value: val
                    });
                }
            });

            html = `<table class="table table-bordered text-end mb-0"><tbody>${tableBody}</tbody></table>`;
            sourceData.data = dataRows;

        } else if (type === 'shoes') {
            const sizesStr = document.getElementById('shoeSizes').value.trim();
            const sizes = sizesStr.split(',').map(s => s.trim()).filter(s => s);

            let sizesHtml = sizes.map(s =>
                `<span class="badge border text-dark p-2 m-1" style="min-width: 45px; font-size: 1rem;">${s}</span>`
                ).join('');
            html = `<div class="d-flex flex-wrap justify-content-center p-2 bg-light rounded">${sizesHtml}</div>`;
            sourceData.data = sizes;

        } else {
            html = document.getElementById('rawHtmlContent').value;
            sourceData.data = html;
        }

        // تخزين البيانات الأصلية في تعليق HTML لسهولة التعديل لاحقاً
        const metaComment = `<!--META_DATA:${JSON.stringify(sourceData)}-->`;
        document.getElementById('guideContent').value = metaComment + html;
        return true;
    }

    // --- التوابع الأساسية ---

    async function loadGuides() {
        const tbody = document.getElementById('guidesTableBody');
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="py-5 text-center">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">جاري تحميل الأدلة...</div>
                </td>
            </tr>`;

        try {
            const res = await fetch("{{ route('size-guides.json') }}");
            const result = await res.json();
            allGuides = Array.isArray(result) ? result : (result.data ? result.data : []);
            renderGuides(allGuides);
        } catch (e) {
            console.error("Error loading guides:", e);
            tbody.innerHTML =
                '<tr><td colspan="4" class="text-danger py-4 text-center">فشل في تحميل البيانات</td></tr>';
        }
    }

    function renderGuides(guides) {
        const tbody = document.getElementById('guidesTableBody');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (guides.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="4" class="py-4 text-muted text-center">لا توجد أدلة مقاسات حالياً</td></tr>';
            return;
        }

        guides.forEach(guide => {
            // إزالة التعليق البرمجي للعرض في الجدول
            const contentPreview = guide.content ? guide.content.replace(/<!--META_DATA:.*?-->/, '') : '';
            const strippedPreview = contentPreview.length > 80 ? contentPreview.substring(0, 80) + '...' :
                contentPreview;

            tbody.innerHTML += `
            <tr>
                <td>${guide.id}</td>
                <td class="fw-bold text-end px-4">${guide.name}</td>
                <td class="text-muted small text-end">${escapeHtml(strippedPreview)}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-warning m-1" title="تعديل" onclick='prepareGuideEdit(${JSON.stringify(guide).replace(/'/g, "&apos;")})'>
                            <i class="bi bi-pencil"></i> تعديل
                        </button>
                        <button class="btn btn-sm btn-outline-danger m-1" title="حذف" onclick="deleteGuide(${guide.id})">
                            <i class="bi bi-trash"></i> حذف
                        </button>
                    </div>
                </td>
            </tr>`;
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function openGuideModal() {
        const form = document.getElementById('guideForm');
        if (form) form.reset();

        document.getElementById('guideId').value = '';
        document.getElementById('clothingRows').innerHTML = '';
        document.getElementById('shoeSizes').value = '';
        document.getElementById('rawHtmlContent').value = '';
        document.getElementById('guideType').value = 'clothing';

        addClothingRow(); // إضافة صف فارغ افتراضياً للملابس
        toggleGuideInputs();

        document.getElementById('guideModalTitle').innerText = 'إضافة دليل جديد';
        document.getElementById('guideModalHeader').className = 'modal-header bg-primary text-white';
        const btn = document.getElementById('guideSaveBtn');
        btn.className = 'btn btn-primary w-100';
        btn.innerText = 'حفظ الدليل';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('guideModal')).show();
    }

    function prepareGuideEdit(guide) {
        document.getElementById('guideId').value = guide.id;
        document.getElementById('guideName').value = guide.name;

        // محاولة استخراج البيانات الأصلية من التعليق
        const metaMatch = guide.content.match(/<!--META_DATA:(.*?)-->/);
        let meta = null;
        if (metaMatch && metaMatch[1]) {
            try {
                meta = JSON.parse(metaMatch[1]);
            } catch (e) {}
        }

        document.getElementById('clothingRows').innerHTML = '';
        document.getElementById('shoeSizes').value = '';
        document.getElementById('rawHtmlContent').value = '';

        if (meta) {
            document.getElementById('guideType').value = meta.type;
            if (meta.type === 'clothing') {
                meta.data.forEach(row => addClothingRow(row.label, row.value));
            } else if (meta.type === 'shoes') {
                document.getElementById('shoeSizes').value = Array.isArray(meta.data) ? meta.data.join(', ') : '';
            } else {
                document.getElementById('rawHtmlContent').value = meta.data || '';
            }
        } else {
            // إذا لم يوجد ميتا، نعتبره مخصصاً (HTML قديم)
            document.getElementById('guideType').value = 'custom';
            document.getElementById('rawHtmlContent').value = guide.content;
        }

        toggleGuideInputs();

        document.getElementById('guideModalTitle').innerText = 'تعديل الدليل: ' + guide.name;
        document.getElementById('guideModalHeader').className = 'modal-header bg-warning text-dark';
        const btn = document.getElementById('guideSaveBtn');
        btn.className = 'btn btn-warning w-100';
        btn.innerText = 'تحديث الدليل';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('guideModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const guideForm = document.getElementById('guideForm');
        if (guideForm) {
            guideForm.onsubmit = async function(e) {
                e.preventDefault();

                // توليد الـ HTML والبيانات السورس
                generateFinalHtml();

                const id = document.getElementById('guideId').value;
                const btn = document.getElementById('guideSaveBtn');
                const url = id ? `/admin/size-guides-update/${id}` : "/admin/size-guides-store";

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

                try {
                    const formData = new FormData(this);
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
                        bootstrap.Modal.getInstance(document.getElementById('guideModal')).hide();
                        loadGuides();
                        Swal.fire({
                            icon: 'success',
                            title: 'نجاح',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: result.message || 'خطأ في الحفظ'
                        });
                    }
                } catch (error) {
                    console.error("Save error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ في الاتصال بالسيرفر'
                    });
                } finally {
                    btn.disabled = false;
                    btn.innerText = id ? 'تحديث الدليل' : 'حفظ الدليل';
                }
            };
        }

        loadGuides();
        const searchInput = document.getElementById('guideSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const filtered = allGuides.filter(g => g.name.toLowerCase().includes(term));
                renderGuides(filtered);
            });
        }
    });

    async function deleteGuide(id) {
        const confirmed = await Swal.fire({
            title: 'تأكيد الحذف',
            text: 'هل أنت متأكد من حذف هذا الدليل؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        });
        if (!confirmed.isConfirmed) return;

        try {
            const res = await fetch(`/admin/size-guides-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (res.ok) {
                loadGuides();
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحذف',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'فشل حذف الدليل'
                });
            }
        } catch (error) {
            console.error("Delete error:", error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء الاتصال بالسيرفر'
            });
        }
    }
</script>
