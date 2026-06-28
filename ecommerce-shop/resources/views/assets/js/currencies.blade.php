<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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

    async function loadCurrencies() {
        try {
            const response = await fetch('/admin/currencies-json');
            const data = await response.json();
            const tbody = document.getElementById('currenciesTableBody');
            tbody.innerHTML = '';

            data.forEach(currency => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold">${currency.name}</td>
                    <td><span class="badge bg-primary">${currency.code}</span></td>
                    <td><span class="badge bg-secondary">${currency.symbol}</span></td>
                    <td>${currency.exchange_rate}</td>
                    <td>
                        ${currency.is_default ? 
                            '<span class="badge bg-success">أساسية</span>' : 
                            '<span class="badge bg-light text-dark">ثانوية</span>'}
                    </td>
                    <td>
                        <div class="form-check form-switch d-flex justify-content-center">
                            <input type="checkbox" class="form-check-input" 
                                onchange="toggleCurrencyStatus(${currency.id}, this)" 
                                ${currency.status ? 'checked' : ''}>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-sm btn-outline-info" onclick='editCurrency(${JSON.stringify(currency)})'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            ${!currency.is_default ? `
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCurrency(${currency.id})">
                                <i class="bi bi-trash"></i>
                            </button>` : ''}
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            swalError('فشل تحميل العملات');
        }
    }

    function resetCurrencyForm() {
        document.getElementById('currencyForm').reset();
        document.getElementById('c_id').value = '';
        document.getElementById('modalTitle').innerText = 'إضافة عملة جديدة';
        document.getElementById('submitBtn').innerText = 'حفظ العملة';
    }

    function editCurrency(currency) {
        document.getElementById('c_id').value = currency.id;
        document.getElementById('c_name').value = currency.name;
        document.getElementById('c_code').value = currency.code;
        document.getElementById('c_symbol').value = currency.symbol;
        document.getElementById('c_exchange_rate').value = currency.exchange_rate;
        document.getElementById('c_is_default').checked = currency.is_default;

        document.getElementById('modalTitle').innerText = 'تعديل عملة: ' + currency.name;
        document.getElementById('submitBtn').innerText = 'تحديث';

        const modal = new bootstrap.Modal(document.getElementById('currencyModal'));
        modal.show();
    }

    document.getElementById('currencyForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('c_id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/admin/currencies-update/${id}` : '/admin/currencies-store';

        const formData = new FormData(this);
        const data = {};
        formData.forEach((value, key) => data[key] = value);
        data.is_default = document.getElementById('c_is_default').checked ? 1 : 0;

        try {
            const response = await fetch(url, {
                method: method === 'PUT' ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                swalSuccess();
                bootstrap.Modal.getInstance(document.getElementById('currencyModal')).hide();
                loadCurrencies();
            } else {
                const err = await response.json();
                swalError(err.message);
            }
        } catch (error) {
            swalError('خطأ في الاتصال');
        }
    });

    async function toggleCurrencyStatus(id, element) {
        try {
            const response = await fetch(`/admin/currencies-status/${id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                swalSuccess('تم تغيير الحالة');
            } else {
                element.checked = !element.checked;
                swalError();
            }
        } catch (error) {
            element.checked = !element.checked;
            swalError();
        }
    }

    async function deleteCurrency(id) {
        if (!confirm('هل أنت متأكد من حذف هذه العملة؟')) return;
        try {
            const response = await fetch(`/admin/currencies-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                swalSuccess('تم الحذف');
                loadCurrencies();
            } else {
                const err = await response.json();
                swalError(err.message);
            }
        } catch (error) {
            swalError();
        }
    }

    document.addEventListener('DOMContentLoaded', loadCurrencies);
</script>
