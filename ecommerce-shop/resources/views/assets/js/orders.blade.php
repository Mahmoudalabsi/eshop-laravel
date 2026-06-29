<script>
    const currentLang = document.documentElement.lang || 'ar';
    
    const translations = {
        ar: {
            loading: 'جاري تحميل الطلبات...',
            errorLoading: 'خطأ في تحميل البيانات',
            noOrders: 'لا توجد طلبات للعرض',
            unknownCustomer: 'عميل غير معروف',
            product: 'المنتج',
            specifications: 'المواصفات',
            quantity: 'الكمية',
            price: 'السعر',
            total: 'الإجمالي',
            productNotAvailable: 'المنتج غير متوفر',
            noDetails: 'لا توجد تفاصيل لهذا الطلب',
            totalInvoice: 'إجمالي الفاتورة:',
            pending: 'قيد الانتظار',
            processing: 'قيد التجهيز',
            shipped: 'تم الشحن',
            delivered: 'تم التسليم',
            completed: 'مكتمل',
            cancelled: 'ملغى',
            view: 'عرض',
            close: 'إغلاق',
            loadingDetails: 'جاري تحميل التفاصيل...',
            orderStatusUpdated: 'تم تحديث حالة الطلب بنجاح',
            orderStatusFailed: 'فشل في تحديث حالة الطلب',
            connectionError: 'خطأ في الاتصال'
        },
        en: {
            loading: 'Loading orders...',
            errorLoading: 'Error loading data',
            noOrders: 'No orders to display',
            unknownCustomer: 'Unknown customer',
            product: 'Product',
            specifications: 'Specifications',
            quantity: 'Quantity',
            price: 'Price',
            total: 'Total',
            productNotAvailable: 'Product not available',
            noDetails: 'No details available for this order',
            totalInvoice: 'Total Invoice:',
            pending: 'Pending',
            processing: 'Processing',
            shipped: 'Shipped',
            delivered: 'Delivered',
            completed: 'Completed',
            cancelled: 'Cancelled',
            view: 'View',
            close: 'Close',
            loadingDetails: 'Loading details...',
            orderStatusUpdated: 'Order status updated successfully',
            orderStatusFailed: 'Failed to update order status',
            connectionError: 'Connection error'
        }
    };

    const t = translations[currentLang] || translations.ar;

    function swalSuccess(message = t.orderStatusUpdated) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: currentLang === 'ar' ? 'نجاح' : 'Success',
                text: message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    function swalError(message = t.connectionError) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: currentLang === 'ar' ? 'خطأ' : 'Error',
                text: message
            });
        } else {
            alert(message);
        }
    }

    let allOrders = []; // Store all orders for search and real-time filtering

    // 1. Fetch orders from server
    async function fetchOrders() {
        const tbody = document.getElementById('ordersTableBody');
        if (!tbody) return;

        // Simple loading effect
        tbody.innerHTML =
            '<tr><td colspan="6" class="py-5 text-center"><div class="spinner-border text-primary"></div> ' + t.loading + '</td></tr>';

        try {
            const response = await fetch("{{ route('orders.json') }}?t=" + new Date().getTime());
            const result = await response.json();

            // Verify data fetched correctly
            allOrders = result.data || [];
            renderOrders(allOrders);
        } catch (error) {
            console.error("Error fetching data:", error);
            tbody.innerHTML =
                '<tr><td colspan="6" class="py-4 text-danger text-center">' + t.errorLoading + '</td></tr>';
        }
    }

    // 2. Display data in table
    function renderOrders(orders) {
        const tbody = document.getElementById('ordersTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="py-4 text-muted text-center">' + t.noOrders + '</td></tr>';
            return;
        }

        orders.forEach(order => {
            const statusBadge = getStatusBadge(order.status);
            const date = new Date(order.created_at).toLocaleDateString(currentLang === 'ar' ? 'ar-EG' : 'en-US');

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold">#${order.id}</td>
                    <td>${order.user ? order.user.name : '<span class="text-muted">' + t.unknownCustomer + '</span>'}</td>
                    <td class="text-success fw-bold">${parseFloat(order.total_price || order.total || 0).toFixed(2)} ${currentLang === 'ar' ? 'ر.س' : 'SAR'}</td>
                    <td><span class="badge ${statusBadge.class} rounded-pill px-3">${statusBadge.text}</span></td>
                    <td class="small text-muted">${date}</td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-primary"
                                    onclick="viewOrderDetails(${order.id})">
                                <i class="bi bi-eye"></i> ${t.view}
                            </button>
                            <select class="form-select form-select-sm w-auto shadow-sm" onchange="changeStatus(${order.id}, this.value)">
                                <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>${t.pending}</option>
                                <option value="processing" ${order.status === 'processing' ? 'selected' : ''}>${t.processing}</option>
                                <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''}>${t.shipped || 'تم الشحن'}</option>
                                <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>${t.delivered || 'تم التسليم'}</option>
                                <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>${t.completed}</option>
                                <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>${t.cancelled}</option>
                            </select>
                        </div>
                    </td>
                </tr>`;
        });
    }

    // 3. Real-time search engine
    function applyOrderSearch() {
        const searchInput = document.getElementById('orderSearchInput');
        if (!searchInput) return;

        const term = searchInput.value.toLowerCase();
        const filtered = allOrders.filter(order => {
            const customerName = order.user ? order.user.name.toLowerCase() : '';
            return (
                order.id.toString().includes(term) ||
                customerName.includes(term)
            );
        });
        renderOrders(filtered);
    }

    // 4. Display order details in modal
    function viewOrderDetails(id) {
        const order = allOrders.find(o => o.id == id);
        if (!order) return;

        document.getElementById('modalOrderId').innerText = '#' + order.id;

        const loadingEl = document.getElementById('orderDetailsLoading');
        if (loadingEl) {
            loadingEl.innerText = t.loadingDetails;
        }

        let itemsHtml = `
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>${t.product}</th>
                        <th>${t.specifications}</th>
                        <th>${t.quantity}</th>
                        <th>${t.price}</th>
                        <th>${t.total}</th>
                    </tr>
                </thead>
                <tbody>`;

        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const price = parseFloat(item.price || item.unit_price || 0);
                const total = price * item.quantity;
                const currency = currentLang === 'ar' ? 'ر.س' : 'SAR';
                itemsHtml += `
                <tr>
                    <td class="fw-bold">${item.product ? item.product.name : t.productNotAvailable}</td>
                    <td>
                        <span class="badge bg-light text-dark border">${item.size || '---'}</span>
                        <span class="badge" style="background-color: ${item.color}; border: 1px solid #ccc; width: 15px; height: 15px; display: inline-block; border-radius: 50%;" title="${item.color}"></span>
                    </td>
                    <td><span class="badge bg-dark">${item.quantity}</span></td>
                    <td>${price.toFixed(2)} ${currency}</td>
                    <td class="fw-bold">${total.toFixed(2)} ${currency}</td>
                </tr>`;
            });
        } else {
            itemsHtml += `<tr><td colspan="5" class="text-center">${t.noDetails}</td></tr>`;
        }

        itemsHtml += `
                </tbody>
                <tfoot class="table-light">
                    <tr class="table-primary fw-bold">
                        <td colspan="4" class="text-end fs-5">${t.totalInvoice}</td>
                        <td class="fs-5 text-primary">${parseFloat(order.total_price || order.total || 0).toFixed(2)} ${currentLang === 'ar' ? 'ر.س' : 'SAR'}</td>
                    </tr>
                </tfoot>
            </table>
        </div>`;

        document.getElementById('orderDetailsContent').innerHTML = itemsHtml;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('viewOrderModal')).show();
    }
    // 5. Change order status
    async function changeStatus(id, status) {
        try {
            const response = await fetch(`/admin/orders-status/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status
                })
            });

            if (response.ok) {
                fetchOrders(); // Reload data to update table
                swalSuccess('Order status updated successfully');
            } else {
                swalError('Failed to update order status');
            }
        } catch (e) {
            console.error("Status update error:", e);
            swalError('Connection error');
        }
    }

    // 6. Update data (Force Refresh)
    async function forceRefresh() {
        const btn = document.getElementById('refreshBtn');
        const icon = document.getElementById('refreshIcon');

        icon.style.transition = "transform 0.6s ease";
        icon.style.transform = "rotate(360deg)";
        btn.disabled = true;

        await fetchOrders();

        setTimeout(() => {
            icon.style.transform = "rotate(0deg)";
            icon.style.transition = "none";
            btn.disabled = false;
        }, 600);
    }

    // 7. Formatting helpers
    function getStatusBadge(status) {
        const maps = {
            'pending': {
                text: t.pending,
                class: 'bg-warning text-dark'
            },
            'processing': {
                text: t.processing,
                class: 'bg-info text-white'
            },
            'shipped': {
                text: t.shipped,
                class: 'bg-primary text-white'
            },
            'delivered': {
                text: t.delivered,
                class: 'bg-success text-white'
            },
            'completed': {
                text: t.completed,
                class: 'bg-success text-white'
            },
            'cancelled': {
                text: t.cancelled,
                class: 'bg-danger text-white'
            }
        };
        return maps[status] || {
            text: status,
            class: 'bg-secondary'
        };
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', () => {
        fetchOrders();
        setInterval(fetchOrders, 30000);
        // Enable real-time search
        const searchInput = document.getElementById('orderSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', applyOrderSearch);
        }
    });
</script>
