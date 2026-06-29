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

    document.addEventListener('DOMContentLoaded', function() {

        @if (session('success'))
            var welcomeModalElem = document.getElementById('welcomeModal');
            if (welcomeModalElem) {
                var myModal = new bootstrap.Modal(welcomeModalElem);
                myModal.show();
            }
        @endif

        animateCounters();

        fetchLatestOrders();

        const categoryData = {!! json_encode($categoryData) !!};
        if (categoryData) {
            renderCategoryChart(categoryData);
        }

        // Load sales data asynchronously (non-blocking)
        const sLabels = {!! json_encode($salesLabels ?? []) !!};
        if (sLabels.length > 0) {
            renderModernChart(sLabels, {!! json_encode($salesCounts ?? []) !!});
            // Fetch real data and update chart after page renders
            fetchSalesData(sLabels);
        }
    });

    let mainChartInstance = null;

    function animateCounters() {
        const counters = document.querySelectorAll('.counter');
        const speed = 200;
        counters.forEach(counter => {
            counter.innerText = '0';

            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;

                const inc = Math.max(1, target / speed);

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 50);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        });
    }

    async function fetchLatestOrders() {
        const tbody = document.getElementById('latestOrdersTableBody');

        try {
            const response = await fetch("{{ route('orders.json') }}");
            const result = await response.json();

            // نأخذ أحدث 5 طلبات فقط
            const latestOrders = result.data.slice(0, 5);

            if (latestOrders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-muted">لا توجد طلبات مسجلة</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            latestOrders.forEach(order => {
                const status = getStatusBadge(order.status);
                const orderDate = new Date(order.created_at).toLocaleDateString('ar-EG', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">#${order.id}</td>
                        <td>${order.user ? order.user.name : 'عميل مجهول'}</td>
                        <td class="fw-bold text-success">${parseFloat(order.total_price || order.total || 0).toFixed(2)} ر.س</td>
                        <td><span class="badge ${status.class}">${status.text}</span></td>
                        <td class="small text-muted">${orderDate}</td>
                    </tr>`;
            });
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-danger">حدث خطأ أثناء تحميل البيانات</td></tr>';
            swalError('فشل تحميل بيانات الطلبات الأخيرة');
        }
    }

    // Load sales data asynchronously after page load
    async function fetchSalesData(labels) {
        try {
            const response = await fetch("{{ route('admin.dashboard.sales') }}");
            const data = await response.json();
            if (data.salesCounts) {
                renderModernChart(labels, data.salesCounts);
            }
        } catch (error) {
            console.error('Failed to load sales data:', error);
        }
    }

    function getStatusBadge(status) {
        const map = {
            'pending': {
                text: 'قيد الانتظار',
                class: 'border border-warning text-warning bg-light' // إطار ولون خفيف
            },
            'processing': {
                text: 'جاري التنفيذ',
                class: 'border border-info text-info bg-light'
            },
            'completed': {
                text: 'مكتمل',
                class: 'border border-success text-success bg-light'
            },
            'cancelled': {
                text: 'ملغي',
                class: 'border border-danger text-danger bg-light'
            }
        };
        return map[status] || {
            text: 'غير معروف',
            class: 'bg-secondary text-danger bg-light'
        };
    }

    let categoryChartInstance = null;
    let mainDashboardChartInstance = null;

    function getChartColors() {
        const isDark = document.body.classList.contains('dark-mode');
        return {
            font: isDark ? '#cbd5e1' : '#64748b', // لون الخط (رمادي فاتح للدارك / رمادي غامق للايت)
            grid: isDark ? '#334155' : '#f1f5f9', // لون خطوط الشبكة
            border: isDark ? '#1e293b' : '#ffffff' // لون حدود الدوائر
        };
    }
    // 1. رسم المخطط الدائري (توزيع الأقسام)
    function renderCategoryChart(data) {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        // تدمير الرسم القديم إذا كان موجوداً
        if (categoryChartInstance) {
            categoryChartInstance.destroy();
        }

        categoryChartInstance = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.counts,
                    backgroundColor: ['#5e72e4', '#2dce89', '#11cdef', '#fb6340', '#f5365c'],
                    hoverOffset: 15,
                    borderWidth: 5,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                family: 'Cairo',
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. رسم المخطط المساحي (الطلبات والنشاط)
    function renderModernChart(labels, dataValues) {
        const ctxElement = document.getElementById('mainDashboardChart');
        if (!ctxElement) return;

        const ctx = ctxElement.getContext('2d');

        if (mainDashboardChartInstance) {
            mainDashboardChartInstance.destroy();
        }

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(94, 114, 228, 0.3)');
        gradient.addColorStop(1, 'rgba(94, 114, 228, 0)');

        mainDashboardChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'الطلبات',
                    data: dataValues,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#5e72e4',
                    borderWidth: 4,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#5e72e4',
                    pointBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: 'Cairo',
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Cairo',
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    }
</script>
