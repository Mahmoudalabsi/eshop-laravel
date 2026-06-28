@extends('layouts.app')

@section('title', __('messages.profile'))

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        .cropper-container {
            max-height: 400px !important;
        }

        .img-container {
            text-align: center;
            width: 100%;
            max-height: 400px;
        }

        .img-container img {
            max-width: 100%;
        }

        .fw-black {
            font-weight: 900;
        }

        .bg-gold-soft {
            background-color: #fcf4d4;
        }

        .stat-card-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 1.25rem !important;
        }

        .stat-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .stat-card-dark {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            border: none !important;
        }

        .stat-icon-box {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card-dark .stat-icon-box {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
        }

        .stat-card-dark .stat-label {
            color: rgba(255, 255, 255, 0.7);
        }

        .currency-symbol {
            font-size: 0.9rem;
            font-weight: 600;
            margin-inline-start: 4px;
            opacity: 0.7;
        }

        .avatar-circle {
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .profile-side-card:hover .avatar-circle {
            transform: scale(1.05);
        }

        .transition-hover {
            transition: all 0.2s ease;
            cursor: default;
        }

        .transition-hover:hover {
            background-color: #f8f9fa !important;
            transform: translateX(5px);
        }

        [dir="rtl"] .transition-hover:hover {
            transform: translateX(-5px);
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-5">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"
                        class="text-decoration-none text-muted">@lang('messages.home')</a></li>
                <li class="breadcrumb-item active fw-bold text-dark">@lang('messages.profile')</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4 profile-side-card">
                    <div class="position-relative d-inline-block mx-auto mb-4">
                        @php
                            $profileImage = data_get($user, 'profile_image');
                            if ($profileImage && !str_starts_with($profileImage, 'http') && !str_starts_with($profileImage, '/')) {
                                $profileImage = '/storage/' . $profileImage;
                            }
                        @endphp
                        <div class="rounded-circle bg-gold-soft d-flex align-items-center justify-content-center mx-auto shadow-sm avatar-circle overflow-hidden"
                            style="width: 120px; height: 120px;">
                            @if ($profileImage)
                                <img src="{{ $profileImage }}" alt="Profile"
                                    class="w-100 h-100 object-fit-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <i class="bi bi-person-fill text-dark d-none" style="font-size: 3.5rem;"></i>
                            @else
                                <i class="bi bi-person-fill text-dark" style="font-size: 3.5rem;"></i>
                            @endif
                        </div>
                        <label for="profile_image_input"
                            class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle d-flex align-items-center justify-content-center cursor-pointer shadow-lg"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                        <input type="file" id="profile_image_input" name="profile_image" accept="image/*" class="d-none"
                            form="profile-form">
                    </div>
                    <h3 class="fw-black mb-1 text-dark">{{ data_get($user, 'name') }}</h3>
                    <p class="text-muted mb-4 small">{{ data_get($user, 'email') }}</p>
                    <div class="d-flex justify-content-center mb-4">
                        <span class="badge bg-gold text-dark rounded-pill px-4 py-2 fw-bold text-uppercase"
                            style="font-size: 0.7rem;">@lang('messages.' . strtolower(data_get($user, 'role', 'user')))</span>
                    </div>
                    <div class="info-list text-start mt-2">
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center gap-3">
                            <i class="bi bi-calendar-check text-primary fs-5"></i>
                            <div>
                                <small class="text-muted d-block">@lang('messages.member_since')</small>
                                <span class="fw-bold small">{{ $stats['member_since'] }}</span>
                            </div>
                        </div>
                        <a href="{{ route('orders.index') }}"
                            class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-box-seam"></i> @lang('messages.my_orders')
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats & Form -->
            <div class="col-lg-8">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <a href="{{ route('orders.index') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm rounded-4 p-4 stat-card-modern stat-card-dark h-100">
                                <div class="stat-icon-box mb-3"><i class="bi bi-bag-check-fill"></i></div>
                                <h2 class="stat-number mb-1 text-white">{{ $stats['total_orders'] }}</h2>
                                <p class="stat-label mb-0 small">@lang('messages.total_purchases')</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('wishlist.index') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm rounded-4 p-4 stat-card-modern bg-white h-100 shadow-hover">
                                <div class="stat-icon-box mb-3 text-danger bg-danger bg-opacity-10"><i
                                        class="bi bi-heart-fill"></i></div>
                                <h2 class="stat-number mb-1 text-dark">{{ $stats['wishlist_count'] }}</h2>
                                <p class="stat-label mb-0 text-muted small">@lang('messages.wishlist')</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 stat-card-modern bg-white h-100">
                            <div class="stat-icon-box mb-3 text-gold bg-gold bg-opacity-10"><i class="bi bi-wallet2"></i>
                            </div>
                            <h2 class="stat-number mb-1 text-dark">
                                {{ number_format($stats['total_spent'] * session('currency_rate', 1), 2) }} <span
                                    class="currency-symbol">{{ session('currency_symbol', 'SAR') }}</span></h2>
                            <p class="stat-label mb-0 text-muted small">@lang('messages.total_spent')</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4 d-flex align-items-center gap-3">
                        <div class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-sliders text-white fs-5"></i>
                        </div>
                        <h5 class="fw-black mb-0">@lang('messages.personal_info')</h5>
                    </div>
                    <div class="card-body p-4 bg-light bg-opacity-50">
                        <form id="profile-form" action="{{ route('profile.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase">@lang('messages.name')</label>
                                    <input type="text" name="name"
                                        class="form-control py-3 border-0 shadow-sm rounded-3"
                                        value="{{ data_get($user, 'name', '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase">@lang('messages.email')</label>
                                    <input type="email" name="email"
                                        class="form-control py-3 border-0 shadow-sm rounded-3"
                                        value="{{ data_get($user, 'email', '') }}" required>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit"
                                        class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-sm hover-lift">
                                        <i class="bi bi-check2-circle me-2"></i> @lang('messages.save_changes')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-clock-history text-primary fs-5"></i>
                            </div>
                            <h5 class="fw-black mb-0">@lang('messages.recent_orders')</h5>
                        </div>
                        <a href="{{ route('orders.index') }}"
                            class="btn btn-link text-primary text-decoration-none fw-bold small">@lang('messages.view_all')</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3 small text-muted">@lang('messages.order_id')</th>
                                        <th class="py-3 small text-muted">@lang('messages.date')</th>
                                        <th class="py-3 small text-muted">@lang('messages.status')</th>
                                        <th class="px-4 py-3 small text-muted text-end">@lang('messages.total')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders ?? [] as $order)
                                        <tr>
                                            <td class="px-4 fw-bold">#{{ data_get($order, 'id') }}</td>
                                            <td class="text-muted small">
                                                {{ \Carbon\Carbon::parse(data_get($order, 'created_at'))->format('Y-m-d') }}
                                            </td>
                                            <td><span
                                                    class="badge rounded-pill bg-dark bg-opacity-10 text-dark px-3 py-2">{{ strtoupper(data_get($order, 'status')) }}</span>
                                            </td>
                                            <td class="text-end px-4 fw-black">
                                                {{ number_format(data_get($order, 'total_price', 0) * session('currency_rate', 1), 2) }}
                                                <small>{{ session('currency_symbol', 'SAR') }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted opacity-50">
                                                @lang('messages.no_recent_orders')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropping Modal -->
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true" style="z-index: 1100;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-black">تعديل صورة الملف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="img-container bg-light rounded-3 overflow-hidden">
                        <img id="imageToCrop" src="" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-dark rounded-pill px-4" id="cropAndSave">حفظ الصورة</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper;
            const imageToCrop = document.getElementById('imageToCrop');
            const profileImageInput = document.getElementById('profile_image_input');
            const cropperModalEl = document.getElementById('cropperModal');
            let cropperModal;
            if (cropperModalEl) cropperModal = new bootstrap.Modal(cropperModalEl);

            profileImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: '@lang('messages.image_too_large')'
                        });
                        this.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        imageToCrop.src = event.target.result;
                        if (cropperModal) cropperModal.show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            cropperModalEl.addEventListener('shown.bs.modal', function() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                });
            });

            cropperModalEl.addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            });

            document.getElementById('cropAndSave').addEventListener('click', function() {
                if (!cropper) return;
                cropper.getCroppedCanvas({
                    width: 400,
                    height: 400
                }).toBlob((blob) => {
                    const file = new File([blob], 'profile.jpg', {
                        type: 'image/jpeg'
                    });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    profileImageInput.files = dt.files;
                    if (cropperModal) cropperModal.hide();
                    Swal.fire({
                        title: 'جاري الحفظ...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('profile-form').submit();
                }, 'image/jpeg', 0.9);
            });
        });
    </script>
@endpush
