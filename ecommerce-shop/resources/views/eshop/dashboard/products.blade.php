@extends('eshop.layouts.admin')
@push('css')
    @include('assets.css.style')
@endpush
@section("title"," المنتجات والتقييمات ")

@section('content')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom text-end">
        <h1 class="h2">إدارة المنتجات والتقييمات</h1>
        <div class="d-flex gap-2">
            <select id="filterCategory" class="form-select shadow-sm" style="width: 180px;">
                <option value="all">جميع الأقسام</option>
                @foreach ($subcategories as $cat)
                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="text" id="productSearchInput" class="form-control shadow-sm text-end"
                placeholder="🔎 ابحث باسم المنتج..." style="width: 250px;">

            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#productModal" onclick="prepareModal('add')">
                <i class="bi bi-plus-circle"></i> إضافة منتج
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th>الصورة</th>
                        <th onclick="sortProducts('name')" style="cursor:pointer">الاسم <i
                                class="bi bi-arrow-down-up small"></i></th>
                        <th onclick="sortProducts('total_stock')" style="cursor:pointer">المخزن <i
                                class="bi bi-arrow-down-up small"></i></th>
                        <th onclick="sortProducts('reviews_avg_rating')" style="cursor:pointer">
                            التقييم العام <i class="bi bi-arrow-down-up small text-muted"></i>
                        </th>
                        <th>القسم</th>
                        <th>الحالة</th>
                        <th onclick="sortProducts('price')" style="cursor:pointer">السعر <i
                                class="bi bi-arrow-down-up small"></i></th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    {{-- البيانات سيتم جلبها بواسطة JS --}}
                </tbody>
            </table>
        </div>
    </div>

<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg text-end">
                <div class="modal-header text-white" id="modalHeader">
                    <h5 class="modal-title" id="modalTitle">إضافة منتج</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="productForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="productId" name="id">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">اسم المنتج</label>
                                <input type="text" name="name" id="p_name" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">القسم</label>
                                <select name="subcategory_id" id="prod_subcategory" class="form-select shadow-sm" required>
                                    <option value="">-- اختر القسم الفرعي --</option>
                                    @foreach ($subcategories as $sub)
                                        <option value="{{ $sub->id }}">
                                            {{ $sub->category->name }} -> {{ $sub->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success">السعر الحالي ($)</label>
                                <input type="number" step="0.01" name="price" id="p_price"
                                    class="form-control border-success fw-bold shadow-sm" required>
                            </div>
                            <div class="col-md-6 mb-3" id="old_price_container">
                                <label class="form-label fw-bold text-muted">السعر القديم (اتركه فارغاً لإلغاء الخصم)</label>
                                <input type="number" step="0.01" name="old_price" id="p_old_price"
                                    class="form-control shadow-sm bg-light" placeholder="مثال: 2400">
                            </div>
                        </div>
                        <div class="row mb-4 p-3 bg-light rounded border">
                            <div class="col-md-4 text-center">
                                <label class="form-label fw-bold d-block">الصورة الرئيسية الحالية</label>
                                <img id="current_main_img" src="{{ asset('assets/img/no-image.png') }}"
                                    class="img-thumbnail mb-2" style="height: 100px; width: 100px; object-fit: cover;">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">تغيير الصورة الرئيسية</label>
                                <input type="file" name="image" class="form-control" id="main_image_input"
                                    accept="image/*">
                                <small class="text-muted">رفع صورة هنا سيستبدل الصورة الحالية للمنتج</small>
                            </div>
                        </div>

                        <div id="edit_gallery_section" class="mb-3 p-3 border rounded bg-white" style="display: none;">
                            <label class="form-label fw-bold d-block mb-3 text-primary">
                                <i class="bi bi-images"></i> معرض الصور الإضافية
                            </label>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle text-center border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الصورة</th>
                                            <th>الإجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit_gallery_table"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mb-3 p-3 border rounded bg-white">
                            <label class="form-label fw-bold text-primary">
                                <i class="bi bi-images"></i> إضافة صور للمعرض (متعدد)
                            </label>
                            <input type="file" name="images[]" id="gallery_input" class="form-control shadow-sm"
                                multiple accept="image/*">
                            <div id="gallery_preview" class="d-flex flex-wrap gap-2 mt-2">
                            </div>
                            <small class="text-muted small">يمكنك اختيار عدة صور في وقت واحد (Ctrl + Click)</small>
                        </div>
                        <div class="mb-3 p-2 border rounded bg-light">
                            <label class="d-block mb-2 small fw-bold text-muted">إضافة سريعة لمقاسات الملابس:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-dark"
                                    onclick="quickAddSize('S')">S</button>
                                <button type="button" class="btn btn-sm btn-outline-dark"
                                    onclick="quickAddSize('M')">M</button>
                                <button type="button" class="btn btn-sm btn-outline-dark"
                                    onclick="quickAddSize('L')">L</button>
                                <button type="button" class="btn btn-sm btn-outline-dark"
                                    onclick="quickAddSize('XL')">XL</button>
                                <button type="button" class="btn btn-sm btn-outline-dark"
                                    onclick="quickAddSize('XXL')">XXL</button>
                                <button type="button" class="btn btn-sm btn-outline-dark"
                                    onclick="quickAddSize('3XL')">3XL</button>
                                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm"
                                    onclick="addSizeField()">
                                    <i class="bi bi-plus-circle"></i> سطر مخصص
                                </button>
                            </div>
                        </div>

                        <div id="size-container"></div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">وصف مختصر (اختياري - يظهر في بطاقة المنتج)</label>
                            <textarea name="short_description" id="p_short_desc" class="form-control shadow-sm" rows="2" maxlength="150" placeholder="ملخص قصير يظهر في قوائم المنتجات..."></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">الوصف الكامل</label>
                            <textarea name="description" id="p_desc" class="form-control shadow-sm" rows="3"></textarea>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="p_featured" value="1">
                            <label class="form-check-label fw-bold" for="p_featured">
                                <i class="bi bi-star-fill text-warning"></i> منتج مميز (يظهر في الصفحة الرئيسية)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn px-4 text-white" id="submitBtn">حفظ البيانات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg text-end">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-info-circle"></i> تفاصيل المنتج</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5 text-center border-start">
                            <img id="v_image" src="" class="img-fluid rounded shadow-sm mb-3"
                                style="max-height: 280px; object-fit: cover;">

                            <h6 class="fw-bold border-bottom pb-2 small text-secondary mt-3">معرض الصور (عرض فقط)</h6>
                            <div class="table-responsive border rounded bg-white">
                                <table class="table table-sm align-middle mb-0 text-center">
                                    <tbody id="v_gallery_display"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h3 id="v_name" class="fw-bold text-dark mb-1"></h3>
                            <span id="v_category" class="badge bg-info-subtle text-info border border-info mb-3"></span>

                            <div class="p-3 bg-light rounded mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <h4 class="text-success fw-bold mb-0" id="v_price"></h4>
                                    <del class="text-danger small" id="v_old_price"></del>
                                </div>
                            </div>

                            <h6><strong>الوصف:</strong></h6>
                            <p id="v_desc" class="text-muted small"></p>

                            <hr>
                            <h6><strong>الأحجام والمخزون المتوفر:</strong></h6>
                            <div id="v_sizes" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg text-end">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">سجل تقييمات: <span id="reviewProductName" class="text-info"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>التقييم</th>
                                    <th>التعليق</th>
                                    <th>التاريخ</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody id="reviewsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="img-container">
                        <img id="imageToCrop" src="" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="cropButton">قص وحفظ</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('assets.js.products')
