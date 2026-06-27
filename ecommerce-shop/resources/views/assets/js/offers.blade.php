<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    function swalWarning(message = 'تحذير') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: message
            });
        } else {
            alert(message);
        }
    }

    // Store data passed from the server
    const storeData = {
        category: @json($categories),
        subcategory: @json($subcategories),
        product: @json($products)
    };

    /**
     * Unified function to control target visibility and options based on selected scope
     */
    function handleScopeChange() {
        const scope = document.getElementById('o_scope').value;
        const targetSelect = document.getElementById('o_target_id');
        const targetContainer = document.getElementById('targetContainer');
        const targetLabel = document.getElementById('targetLabel');

        targetSelect.innerHTML = '<option value="">اختر من القائمة...</option>';
        if (scope === 'all' || scope === '') {
            targetContainer.style.display = 'none';
            targetSelect.required = false;
            return;
        }
        targetContainer.style.display = 'block';
        targetSelect.required = true;

        const labels = {
            'category': 'القسم الرئيسي',
            'subcategory': 'القسم الفرعي',
            'product': 'المنتج'
        };
        targetLabel.innerText = 'اختر ' + labels[scope];

        const list = storeData[scope];
        if (list) {
            list.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.text = item.name;
                targetSelect.appendChild(option);
            });
        }
    }
        /**
         * Open edit modal and populate fields
         */
     function editOffer(offer) {
    if (offer.status == 1) {
        swalWarning('لا يمكن تعديل عرض نشط. يرجى إيقافه أولاً.');
        return;
    }

    const modalElement = document.getElementById('offerModal');

    // 1. Update modal appearance for edit mode
    document.getElementById('modalTitle').innerText = 'تعديل العرض: ' + offer.name;
    document.getElementById('modalHeader').className = 'modal-header text-white bg-info'; // different color for edit mode
    document.getElementById('submitBtn').innerText = 'تحديث البيانات';
    document.getElementById('submitBtn').className = 'btn btn-info px-4';

    // 2. Populate form fields
    document.getElementById('o_id').value = offer.id;
    document.getElementById('formMethod').value = 'PUT'; // important for Laravel
    document.getElementById('o_name').value = offer.name;
    document.getElementById('o_type').value = offer.type;
    document.getElementById('o_discount').value = offer.discount_value;
    document.getElementById('o_scope').value = offer.scope;

    // Handle date fields
    if (offer.starts_at) document.getElementById('o_starts_at').value = offer.starts_at.replace(' ', 'T').substring(0, 16);
    if (offer.ends_at) document.getElementById('o_ends_at').value = offer.ends_at.replace(' ', 'T').substring(0, 16);

    // Update dropdowns based on scope
    handleScopeChange();
    if (offer.scope !== 'all') {
        // wait briefly for DOM options to be ready
        setTimeout(() => {
            document.getElementById('o_target_id').value = offer.target_id;
        }, 50);
    }

    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalInstance.show();
}    /**
    * Reset form for add mode
    */
  function resetOfferForm() {
    const form = document.getElementById('offerForm');
    form.reset();

    // 1. Reset modal appearance for add mode
    document.getElementById('modalTitle').innerText = 'إعداد عرض جديد';
    document.getElementById('modalHeader').className = 'modal-header text-white bg-primary';
    document.getElementById('submitBtn').innerText = 'تفعيل العرض';
    document.getElementById('submitBtn').className = 'btn btn-primary px-4';

    // 2. Clear hidden fields
    document.getElementById('o_id').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('targetContainer').style.display = 'none';
}

    /**
     * معالجة إرسال النموذج (إضافة أو تعديل)
     */
    document.addEventListener('DOMContentLoaded', function() {
        const offerForm = document.getElementById('offerForm');
        if (offerForm) {
            offerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                let btn = document.getElementById('submitBtn');
                btn.disabled = true;

                const offerId = document.getElementById('o_id').value;
                // Determine URL based on presence of ID (update vs create)
                const url = offerId ? `/admin/offers-update/${offerId}` :
                    "{{ url('admin/offers-store') }}";

                try {
                    const response = await fetch(url, {
                        method: 'POST', // use POST with FormData; _method controls intent
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: new FormData(this)
                    });

                    if (response.ok) {
                        swalSuccess('تمت العملية بنجاح');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        const result = await response.json();
                        swalError(result.message || 'خطأ في معالجة البيانات');
                    }
                } catch (error) {
                    swalError('حدث خطأ في الاتصال بالسيرفر');
                } finally {
                    btn.disabled = false;
                }
            });
        }
    });

    /**
     * حذف العرض
     */
    async function deleteOffer(id) {
        try {
            if (typeof Swal !== 'undefined') {
                const confirmed = await Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد؟ سيتم حذف العرض وإعادة الأسعار الأصلية.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                });
                if (!confirmed.isConfirmed) return;
            } else {
                if (!confirm('هل أنت متأكد؟ سيتم حذف العرض وإعادة الأسعار الأصلية.')) return;
            }

            const response = await fetch(`/admin/offers-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                swalSuccess('تم الحذف بنجاح');
                setTimeout(() => location.reload(), 1500);
            } else {
                swalError('فشل الحذف');
            }
        } catch (error) {
            swalError('خطأ في الاتصال');
        }
    }

    /**
     * تغيير حالة العرض (نشط / غير نشط)
     */
    async function toggleOfferStatus(id, element) {
        element.disabled = true;
        const isChecked = element.checked ? 1 : 0;

        try {
            const response = await fetch(`/admin/offers-status/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: isChecked,
                    _method: 'PATCH'
                })
            });

            if (response.ok) {
                swalSuccess('تم التحديث بنجاح');
                setTimeout(() => location.reload(), 1500);
            } else {
                element.checked = !element.checked;
                const result = await response.json();
                swalError(result.message || 'فشل تحديث الحالة');
            }
        } catch (error) {
            element.checked = !element.checked;
            swalError('خطأ في الاتصال');
        } finally {
            element.disabled = false;
        }
    }
</script>
