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

        // --- 1. Global variables ---
        let allProducts = [];
        let sortDirection = true;
        let variantIndex = 0;

        // --- 2. Initialize on DOMContentLoaded ---
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Products script initialized...");

            // Handle form submit (create / update)
            const productForm = document.getElementById('productForm');
            if (productForm) {
                productForm.onsubmit = handleProductSubmit;
            }

            // Search and filter controls
            const searchInput = document.getElementById('productSearchInput');
            const categoryFilter = document.getElementById('filterCategory');
            if (searchInput) searchInput.addEventListener('input', applyFilters);
            if (categoryFilter) categoryFilter.addEventListener('change', applyFilters);

            // Preview gallery images on selection
            const galleryInput = document.getElementById('gallery_input');
            if (galleryInput) {
                galleryInput.addEventListener('change', handleGalleryPreview);
            }

            // Load initial products data
            if (document.getElementById('productsTableBody')) {
                loadProducts();
            }
            let cropper;
            let croppedBlob = null;

            // Listen for main image selection
            const mainImageInput = document.getElementById('main_image_input');
            if (mainImageInput) {
                mainImageInput.addEventListener('change', function(e) {
                    const files = e.target.files;
                    if (files && files.length > 0) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const imageToCrop = document.getElementById('imageToCrop');
                            imageToCrop.src = event.target.result;

                            // Open crop modal
                            const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
                            cropModal.show();
                        };
                        reader.readAsDataURL(files[0]);
                    }
                });
            }

            // Initialize Cropper when modal opens
            const cropModalEl = document.getElementById('cropModal');
            if (cropModalEl) {
                cropModalEl.addEventListener('shown.bs.modal', function() {
                    cropper = new Cropper(document.getElementById('imageToCrop'), {
                        aspectRatio: 1, // مربع
                        viewMode: 1,
                    });
                });

                cropModalEl.addEventListener('hidden.bs.modal', function() {
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                });
            }

            // Execute crop on button click
            document.getElementById('cropButton').addEventListener('click', function() {
                const canvas = cropper.getCroppedCanvas({
                    width: 600,
                    height: 600
                });
                canvas.toBlob(function(blob) {
                    croppedBlob = blob;
                    // Update preview in main modal
                    document.getElementById('current_main_img').src = canvas.toDataURL();
                    // Close crop modal
                    bootstrap.Modal.getInstance(document.getElementById('cropModal')).hide();
                }, 'image/jpeg');
            });
        });

        // --- 3. Core functions (CRUD & Fetch) ---

        async function loadProducts() {
            const tbody = document.getElementById('productsTableBody');
            if (!tbody) return;

            tbody.innerHTML =
                '<tr><td colspan="8" class="py-5 text-center"><div class="spinner-border text-primary"></div></td></tr>';

            try {
                const res = await fetch("{{ url('admin/products-json') }}");
                const result = await res.json();
                allProducts = result.data || result;
                renderProducts(allProducts);
            } catch (error) {
                console.error("Fetch Error:", error);
                tbody.innerHTML =
                    `<tr><td colspan="8" class="py-4 text-danger text-center">Load error: ${error.message}</td></tr>`;
            }
        }

        // Handle form submission (create/update)
        async function handleProductSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const id = document.getElementById('productId').value;
            const url = id ? `{{ url('admin/products-update') }}/${id}` : `{{ url('admin/products-store') }}`;
            const formData = new FormData(this);

            if (btn) btn.disabled = true;

            // Replace main image with cropped version if available
            if (croppedBlob) {
                formData.set('image', croppedBlob, 'product_main.png');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json' // Request JSON response
                    },
                    body: formData
                });

                // Read raw text first to avoid crash if response is not JSON
                const rawText = await res.text();
                let result;

                try {
                    result = JSON.parse(rawText);
                } catch (e) {
                    console.error("Response is not JSON:", rawText);
                    // If 200 status but response not JSON, operation likely succeeded
                    if (res.ok) {
                        swalSuccess('تمت العملية بنجاح (ملاحظة: رد غير متوقع)');
                        setTimeout(() => location.reload(), 1500);
                        return;
                    }
                    throw new Error('Invalid response from server');
                }

                if (res.ok) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('productModal')).hide();
                    loadProducts();
                    if (typeof resetImagePreview === "function") resetImagePreview();
                    swalSuccess('تمت العملية بنجاح');
                } else {
                    // Display validation errors from Laravel
                    let errorMessage = result.message || 'Failed to save';
                    if (result.errors) {
                        errorMessage = Object.values(result.errors).flat().join('\n');
                    }
                    swalError('Error: ' + errorMessage);
                }
            } catch (error) {
                console.error("Error Detail:", error);
                swalError('Error connecting to server or processing data');
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        function getStockClass(qty) {
            if (qty <= 0) return 'bg-danger';
            if (qty <= 5) return 'bg-warning text-dark';
            return 'bg-success';
        }
        // --- 4. Rendering functions ---

        function renderProducts(productsList) {
            const tbody = document.getElementById('productsTableBody');
            if (!tbody) return;
            tbody.innerHTML = '';

            if (productsList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="py-4 text-muted text-center">No products currently</td></tr>';
                return;
            }

            productsList.forEach(p => {
                const categoryFromSub = p.subcategory && p.subcategory.category ? p.subcategory.category : null;
                const isCategoryDisabled = (p.category && p.category.status == 0) || (categoryFromSub && categoryFromSub.status == 0);
                const isProductDisabled = p.status == 0;
                const img = getImageUrl(p.image);
                const avgRating = p.reviews_avg_rating ? parseFloat(p.reviews_avg_rating).toFixed(1) : '0.0';

                const statusBtn = p.status == 1 ?
                    `<button class="btn btn-sm btn-success w-100" onclick="toggleProductStatus(${p.id}, 1)"><i class="bi bi-toggle-on"></i> Active</button>` :
                    `<button class="btn btn-sm btn-outline-danger w-100" onclick="toggleProductStatus(${p.id}, 0)"><i class="bi bi-toggle-off"></i> Inactive</button>`;

                let priceHtml = `<span class="text-success fw-bold">${parseFloat(p.price).toFixed(2)}$</span>`;
                if (p.old_price > 0) {
                    priceHtml +=
                        `<br><small class="text-danger text-decoration-line-through">${parseFloat(p.old_price).toFixed(2)}$</small>`;
                }

                const productJson = JSON.stringify(p).replace(/'/g, "&apos;");

                tbody.innerHTML += `
                    <tr class="align-middle ${isCategoryDisabled || isProductDisabled ? 'bg-light opacity-75' : ''}">
                        <td><img src="${img}" width="45" height="45" class="rounded shadow-sm" style="object-fit: cover;"></td>
                        <td class="fw-bold">${p.name} ${isCategoryDisabled ? '<br><span class="badge bg-danger" style="font-size:0.7rem">Category disabled</span>' : ''}</td>
                        <td><span class="badge ${getStockClass(p.total_stock)}">${p.total_stock} units</span></td>
                        <td><div class="text-warning fw-bold" onclick="showReviews(${p.id}, '${p.name.replace(/'/g, "\\'")}')" style="cursor:pointer"><i class="bi bi-star-fill"></i> ${avgRating}</div></td>
                        <td><span class="badge bg-info-subtle text-info border border-info">${p.subcategory ? (categoryFromSub ? categoryFromSub.name + ' → ' : '') + p.subcategory.name : 'General'}</span></td>
                        <td>${statusBtn}</td>
                        <td>${priceHtml}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick='viewProduct(${productJson})'><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning" onclick='editProduct(${productJson})'><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id})"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
            });
        }


        async function deleteExtraImage(productId, path, btnElement) {
            try {
                if (typeof Swal !== 'undefined') {
                    const confirmed = await Swal.fire({
                        title: 'Confirm Delete',
                        text: 'Are you sure you want to delete this image?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel'
                    });
                    if (!confirmed.isConfirmed) return;
                } else {
                    if (!confirm('Are you sure you want to delete this image?')) return;
                }

                const res = await fetch(`{{ url('admin/products/delete-image') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: productId,
                        path: path,
                        type: 'extra'
                    })
                });

                if (res.ok) {
                    // Delete row from table immediately
                    btnElement.closest('tr').remove();
                    swalSuccess('Image deleted successfully');
                }
            } catch (e) {
                swalError('Error deleting image');
            }
        }
        // --- 5. Modal control (add / edit / view) ---

        function prepareModal(mode, product = null) {
            const modalEl = document.getElementById('productModal');
            const header = document.getElementById('modalHeader');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('productForm');

            // Fix warning: remove attribute causing focus conflict
            modalEl.removeAttribute('aria-hidden');

            if (form) form.reset();

            croppedBlob = null;
            document.getElementById('productId').value = '';
            const container = document.getElementById('size-container');
            container.innerHTML = '';
            variantIndex = 0;

            if (mode === 'add') {
                // Format add mode (blue)
                document.getElementById('modalTitle').innerText = 'Add New Product';
                submitBtn.innerText = 'Save Product';
                submitBtn.className = 'btn btn-primary px-4 shadow-sm'; // لون أزرق
                header.className = 'modal-header bg-primary text-white'; // رأس أزرق

                document.getElementById('edit_gallery_section').style.display = 'none';
                // Reset all fields for add mode
                const shortDescEl = document.getElementById('p_short_desc');
                if (shortDescEl) shortDescEl.value = '';
                const featuredEl = document.getElementById('p_featured');
                if (featuredEl) featuredEl.checked = false;
                addSizeField();
            } else {
                // Format edit mode (orange/yellow)
                document.getElementById('modalTitle').innerText = 'Edit Product: ' + (product.name || '');
                submitBtn.innerText = 'Update Data';
                submitBtn.className = 'btn btn-warning px-4 shadow-sm text-dark'; // لون أصفر/برتقالي
                header.className = 'modal-header bg-warning text-dark'; // رأس أصفر

                document.getElementById('edit_gallery_section').style.display = 'block';

                const items = product.attributes || product.variants || [];
                if (items.length > 0) {
                    items.forEach(v => addSizeField(v.color, v.size, v.qty));
                } else {
                    addSizeField();
                }
            }
        }

        function editProduct(product) {
            prepareModal('edit', product);

            document.getElementById('productId').value = product.id;
            document.getElementById('p_name').value = product.name;
            document.getElementById('p_price').value = product.price;
            document.getElementById('p_old_price').value = product.old_price || '';

            const subSelect = document.getElementById('prod_subcategory');
            if (subSelect) subSelect.value = product.subcategory_id;

            const descEl = document.getElementById('p_desc');
            if (descEl) descEl.value = product.description || '';

            const shortDescEl = document.getElementById('p_short_desc');
            if (shortDescEl) shortDescEl.value = product.short_description || '';

            const featuredEl = document.getElementById('p_featured');
            if (featuredEl) featuredEl.checked = !!product.is_featured;

            const currentImg = document.getElementById('current_main_img');
            if (currentImg) currentImg.src = getImageUrl(product.image);

            renderEditGallery(product);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('productModal')).show();
        }

        function viewProduct(product) {
            // 1. Basic data
            document.getElementById('v_name').innerText = product.name;
            document.getElementById('v_price').innerText = parseFloat(product.price).toFixed(2) + '$';
            document.getElementById('v_image').src = getImageUrl(product.image);

            // 2. Image gallery (side by side)
            const galleryContainer = document.getElementById('v_gallery_display');
            if (galleryContainer) {
                galleryContainer.innerHTML = '';

                if (product.images && product.images.length > 0) {
                    // Add flex container to align images side by side
                    let imagesHtml = '<div class="d-flex flex-wrap gap-2">';
                    product.images.forEach(img => {
                        imagesHtml += `
                        <div class="position-relative" style="width: 80px; height: 80px;">
                            <img src="${getImageUrl(img.image_path)}"
                                class="rounded border shadow-sm w-100 h-100"
                                style="object-fit: cover; cursor: pointer;"
                                onclick="window.open(this.src, '_blank')">
                        </div>`;
                    });
                    imagesHtml += '</div>';
                    galleryContainer.innerHTML = imagesHtml;
                } else {
                    galleryContainer.innerHTML = '<p class="text-muted small px-2">No additional images</p>';
                }
            }

            // 3. Sizes/variants
            const sizesContainer = document.getElementById('v_sizes');
            sizesContainer.innerHTML = '';
            (product.attributes || []).forEach(item => {
                sizesContainer.innerHTML += `
                <div class="border rounded p-2 text-center bg-light" style="min-width: 80px;">
                    <small>${item.color || '-'}</small><br>
                    <strong>${item.size}</strong><br>
                    <span class="badge bg-dark">${item.qty}</span>
                </div>`;
            });

            bootstrap.Modal.getOrCreateInstance(document.getElementById('viewProductModal')).show();
        }

        function addSizeField(color = '', size = '', qty = 1) {
            const container = document.getElementById('size-container');
            const index = variantIndex++;
            const html = `
                <div class="row g-2 mb-2 align-items-center p-2 border rounded bg-white variant-row" id="v-row-${index}">
                    <div class="col-md-4"><input type="text" name="variants[${index}][color]" class="form-control form-control-sm" placeholder="Color" value="${color}"></div>
                    <div class="col-md-3"><input type="text" name="variants[${index}][size]" class="form-control form-control-sm text-center" placeholder="Size" value="${size}" required></div>
                    <div class="col-md-3"><input type="number" name="variants[${index}][qty]" class="form-control form-control-sm text-center" placeholder="Quantity" value="${qty}" min="0"></div>
                    <div class="col-md-2"><button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(${index})"><i class="bi bi-trash"></i></button></div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        function quickAddSize(sizeValue) {
            addSizeField('', sizeValue, 1);

            const container = document.getElementById('size-container');
            const lastRow = container.lastElementChild;

            lastRow.style.backgroundColor = '#e8f0fe';
            setTimeout(() => {
                lastRow.style.backgroundColor = 'white';
            }, 1000);

            const modalEl = document.getElementById('productModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        function removeVariantRow(index) {
            const row = document.getElementById(`v-row-${index}`);
            if (row) row.remove();
        }

        // --- 7. Helper functions ---

        function applyFilters() {
            const search = document.getElementById('productSearchInput').value.toLowerCase();
            const cat = document.getElementById('filterCategory').value;
            const filtered = allProducts.filter(p =>
                p.name.toLowerCase().includes(search) && (cat === 'all' || (p.subcategory && p.subcategory.name ===
                    cat))
            );
            renderProducts(filtered);
        }

        async function toggleProductStatus(id, currentStatus) {
            const newStatus = currentStatus == 1 ? 0 : 1;
            try {
                const res = await fetch(`{{ url('admin/products-status') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                });

                const result = await res.json();

                if (res.ok) {
                    loadProducts();
                    swalSuccess(newStatus === 1 ? 'تم تفعيل المنتج' : 'تم تعطيل المنتج');
                } else if (res.status === 422) {
                    swalError(result.message || 'لا يمكن إكمال العملية');
                } else {
                    swalError('خطأ في العملية');
                }
            } catch (e) {
                swalError('خطأ في الاتصال');
            }
        }

        function handleGalleryPreview(e) {
            const preview = document.getElementById('gallery_preview');
            if (!preview) return;
            preview.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.className = 'img-thumbnail m-1';
                    img.style = 'width: 60px; height: 60px; object-fit: cover;';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        function renderEditGallery(product) {
            const table = document.getElementById('edit_gallery_table');
            if (!table) return;
            table.innerHTML = '';
            (product.images || []).forEach(img => {
                table.innerHTML += `
                    <tr>
                        <td><img src="${getImageUrl(img.image_path)}" width="50" class="rounded"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteExtraImage(${product.id}, '${img.image_path}', this)"><i class="bi bi-trash"></i></button></td>
                    </tr>`;
            });
        }
        // --- Sort products method ---
        function sortProducts(column) {
            sortDirection = !sortDirection;

            allProducts.sort((a, b) => {
                let valA = a[column];
                let valB = b[column];

                // 1. Handle rating, price, and stock as numbers
                if (column === 'reviews_avg_rating' || column === 'price' || column === 'total_stock') {
                    valA = parseFloat(valA) || 0; // Convert to number, null becomes 0
                    valB = parseFloat(valB) || 0;

                    return sortDirection ? valA - valB : valB - valA;
                }

                // 2. Handle text (name, category)
                valA = (valA || "").toString().toLowerCase();
                valB = (valB || "").toString().toLowerCase();

                if (valA < valB) return sortDirection ? -1 : 1;
                if (valA > valB) return sortDirection ? 1 : -1;
                return 0;
            });

            renderProducts(allProducts);
            updateSortIcons(column);
        }

        function updateSortIcons(column) {
            const ths = document.querySelectorAll('th[onclick]');
            ths.forEach(th => {
                const icon = th.querySelector('i');
                if (th.getAttribute('onclick').includes(column)) {
                    icon.className = sortDirection ? 'bi bi-arrow-up small' : 'bi bi-arrow-down small';
                    icon.classList.remove('text-muted');
                } else {
                    icon.className = 'bi bi-arrow-down-up small text-muted';
                }
            });
        }
        async function showReviews(id, name) {
            document.getElementById('reviewProductName').innerText = name;
            const tbody = document.getElementById('reviewsTableBody');
            tbody.innerHTML =
                '<tr><td colspan="5" class="py-4 text-center"><div class="spinner-border text-primary"></div></td></tr>';

            bootstrap.Modal.getOrCreateInstance(document.getElementById('reviewsModal')).show();

            try {
                const res = await fetch(`{{ url('admin/products') }}/${id}/reviews`);
                const reviews = await res.json();

                tbody.innerHTML = '';
                if (reviews.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="py-4 text-muted">لا توجد تقييمات لهذا المنتج بعد</td></tr>';
                    return;
                }

                reviews.forEach(rev => {
                    const stars = '<i class="bi bi-star-fill text-warning"></i>'.repeat(rev.rating) +
                        '<i class="bi bi-star text-muted"></i>'.repeat(5 - rev.rating);

                    const date = rev.created_at ? new Date(rev.created_at).toLocaleDateString('ar-SA') : '-';

                    tbody.innerHTML += `
                        <tr>
                            <td>${rev.user ? rev.user.name : 'Unknown User'}</td>
                            <td>${stars} (${rev.rating})</td>
                            <td>${rev.comment || '-'}</td>
                            <td>${date}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteReview(${rev.id}, ${id}, '${name}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-danger">خطأ في جلب التقييمات</td></tr>';
            }
        }

        async function deleteReview(reviewId, productId, productName) {
            if (!confirm('هل أنت متأكد من حذف هذا التقييم؟')) return;

            try {
                const res = await fetch(`{{ url('admin/reviews') }}/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (res.ok) {
                    swalSuccess('تم حذف التقييم');
                    showReviews(productId, productName);
                    loadProducts();
                } else {
                    swalError('حدث خطأ أثناء الحذف');
                }
            } catch (e) {
                swalError('خطأ في الاتصال');
            }
        }

        async function deleteProduct(id) {
            if (!confirm('هل أنت متأكد من حذف هذا المنتج نهائياً؟')) return;

            try {
                const res = await fetch(`{{ url('admin/products-delete') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await res.json();
                if (res.ok) {
                    swalSuccess(result.message || 'تم حذف المنتج');
                    loadProducts();
                } else {
                    swalError(result.message || 'خطأ في الحذف');
                }
            } catch (e) {
                swalError('خطأ في الاتصال');
            }
        }
    </script>
