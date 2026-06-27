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

    console.log("User script loaded and initializing...");

    let allUsers = [];
    const currentUserId = {{ auth()->id() }};
    // 1. وظيفة جلب البيانات
    async function loadUsers() {
        console.log("Attempting to fetch users..."); // سيظهر في الـ Console
        const tbody = document.getElementById('usersList');
        if (!tbody) return;

        tbody.innerHTML =
            '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> جاري تحميل البيانات...</td></tr>';

        const fetchUrl = "{{ url('admin/users-json') }}";

        try {
            const response = await fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log("Response status:", response.status);

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const result = await response.json();

            if (result.status === 'success') {
                allUsers = result.data;
            } else if (Array.isArray(result)) {
                allUsers = result;
            } else {
                allUsers = result.data || [];
            }

            renderUsers(allUsers);

        } catch (error) {
            console.error("Fetch Error Detail:", error);
            tbody.innerHTML = `<tr><td colspan="5" class="text-danger text-center">
                فشل تحميل البيانات. <br>
                <small>${error.message}</small>
            </td></tr>`;
        }
    }

    // 2. وظيفة عرض البيانات في الجدول
    function renderUsers(users) {
        const tbody = document.getElementById('usersList');
        const userCount = document.getElementById('userCount');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (userCount) userCount.innerText = users.length;

        if (!users || users.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="6" class="text-center py-4 text-muted">لا يوجد مستخدمون حالياً</td></tr>';
            return;
        }

        users.forEach(user => {
            const isAdmin = user.role === 'admin';
            const badgeClass = isAdmin ? 'bg-danger-subtle text-danger border-danger-subtle' :
                'bg-primary-subtle text-primary border-primary-subtle';
            const badgeText = isAdmin ? 'مدير' : 'مشتري';
            const date = user.created_at ? new Date(user.created_at).toLocaleDateString('en-CA') : '---';

            const isChecked = user.status == 1 ? 'checked' : '';

            const isDisabled = (user.id == 1 || user.id == currentUserId) ? 'disabled' : '';
            let statusHtml = '';
            if (isAdmin) {
                statusHtml = `
                <div class="form-check form-switch d-flex justify-content-center">
                    <input class="form-check-input status-switch" type="checkbox"
                        ${isChecked} ${isDisabled}
                        style="cursor: ${isDisabled ? 'not-allowed' : 'pointer'}"
                        onchange="toggleUserStatus(${user.id}, this)">
                </div>`;
            } else {
                statusHtml = '<span class="text-muted small">---</span>';
            }

            tbody.innerHTML += `
        <tr class="align-middle">
            <td class="fw-bold text-dark-mode-white">${user.name}</td>
            <td class="text-secondary-emphasis">${user.email}</td>
            <td>${statusHtml}</td>
            <td><span class="badge ${badgeClass} border px-3 py-2 rounded-pill shadow-sm">${badgeText}</span></td>
            <td class="text-muted small">${date}</td>
            <td>
                <div class="d-flex justify-content-center gap-2">
                    <button onclick="updateRole(${user.id})" class="btn btn-sm ${isAdmin ? 'btn-outline-info' : 'btn-outline-warning'} shadow-sm">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                    <button onclick="deleteUser(${user.id})" class="btn btn-sm btn-outline-danger shadow-sm" ${isDisabled}>
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
        });
    }

    //


    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            const filtered = allUsers.filter(user =>
                (user.name && user.name.toLowerCase().includes(term)) ||
                (user.email && user.email.toLowerCase().includes(term))
            );
            renderUsers(filtered);
        });
    }

    // تشغيل الدالة عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', () => {
        console.log("DOM Fully Loaded");
        loadUsers();
    });
    // 4. تحديث الرتبة
    async function updateRole(id) {
        try {
            if (typeof Swal !== 'undefined') {
                const confirmed = await Swal.fire({
                    title: 'تأكيد التحديث',
                    text: 'هل تريد تغيير صلاحيات هذا المستخدم؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، غير',
                    cancelButtonText: 'إلغاء'
                });
                if (!confirmed.isConfirmed) return;
            } else {
                if (!confirm('هل تريد تغيير صلاحيات هذا المستخدم؟')) return;
            }

            const response = await fetch(`{{ url('admin/users/update-role') }}/${id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (result.status === 'success') {
                swalSuccess('تم تحديث الصلاحيات بنجاح');
                loadUsers();
            } else {
                swalError(result.message || 'فشل التحديث');
            }
        } catch (e) {
            swalError('خطأ في الاتصال');
        }
    }

    // 5. حذف مستخدم
    async function deleteUser(id) {
        try {
            if (typeof Swal !== 'undefined') {
                const confirmed = await Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد من حذف هذا المستخدم؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                });
                if (!confirmed.isConfirmed) return;
            } else {
                if (!confirm('هل أنت متأكد من الحذف؟')) return;
            }

            const response = await fetch(`{{ url('admin/users-delete') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                swalSuccess('تم الحذف بنجاح');
                loadUsers();
            } else {
                swalError('فشل الحذف');
            }
        } catch (e) {
            swalError('خطأ في الحذف');
        }
    }

    // نغلف الكود لضمان تحميل الصفحة بالكامل
    document.addEventListener('DOMContentLoaded', function() {

        document.addEventListener('submit', async function(e) {
            if (e.target && e.target.id === 'addUserForm') {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');

                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

                try {
                    const response = await fetch("/admin/users-store", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            // جلب التوكن من حقل @csrf التابع للفورم نفسه
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok) {
                        const modalEl = document.getElementById('addUserModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap
                            .Modal(modalEl);
                        modalInstance.hide();

                        form.reset(); // تنظيف الحقول

                        // تحديث الجدول (تأكد أن دالة loadUsers معرفة لديك)
                        if (typeof loadUsers === "function") loadUsers();

                        swalSuccess('تمت إضافة العضو بنجاح');
                    } else {
                        // عرض رسائل الخطأ من Laravel (مثل الإيميل مكرر)
                        swalError(result.message || 'هناك خطأ في البيانات المدخلة');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    swalError('خطأ في الاتصال بالسيرفر');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'حفظ البيانات';
                }
            }
        });
    });
    async function toggleUserStatus(id, element) {
        const statusValue = element.checked ? 1 : 0;

        try {
            const response = await fetch(`{{ url('admin/users/update-status') }}/${id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: statusValue
                }) // إرسال الحالة هنا
            });

            const result = await response.json();

            if (result.status !== 'success') {
                element.checked = !element.checked; // إرجاع الزر لوضعه الأصلي عند الخطأ
                swalError(result.message);
            }
        } catch (e) {
            element.checked = !element.checked;
            swalError('حدث خطأ في الاتصال بالسيرفر');
        }
    }
</script>
