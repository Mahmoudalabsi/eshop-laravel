@extends('layouts.app')

@section('title', __('messages.checkout') . ' - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.breadcrumb_home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">{{ __('messages.breadcrumb_cart') }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.checkout') }}</li>
        </ol>
    </nav>

    <h1 class="h1 fw-bold mb-4">💳 {{ __('messages.complete_order') }}</h1>

    <div class="row g-4">
        <div class="col-lg-8">
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf

                <!-- البيانات الشخصية -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-person-circle"></i> {{ __('messages.personal_data') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.full_name') }} *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                       name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.email_address') }} *</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                       name="customer_email" value="{{ old('customer_email', auth()->user()->email) }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.phone_number') }} *</label>
                                <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror"
                                       name="customer_phone" value="{{ old('customer_phone') }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- عنوان الشحن -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-geo-alt"></i> {{ __('messages.shipping_address') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('messages.address') }} *</label>
                                <input type="text" class="form-control @error('shipping_address') is-invalid @enderror"
                                       name="shipping_address" value="{{ old('shipping_address') }}" required>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.city') }} *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                       name="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.postal_code') }} *</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                       name="postal_code" value="{{ old('postal_code') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- طريقة الدفع -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-credit-card"></i> {{ __('messages.payment_method') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="credit_card"
                                   value="credit_card" checked required>
                            <label class="form-check-label" for="credit_card">
                                <strong>{{ __('messages.credit_card') }}</strong>
                                <span class="text-muted small">({{ __('messages.credit_card_types') }})</span>
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="debit_card"
                                   value="debit_card" required>
                            <label class="form-check-label" for="debit_card">
                                <strong>{{ __('messages.debit_card') }}</strong>
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash"
                                   value="cash_on_delivery" required>
                            <label class="form-check-label" for="cash">
                                <strong>{{ __('messages.cash_on_delivery') }}</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ملاحظات -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-chat-dots"></i> {{ __('messages.additional_notes') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3"
                                  placeholder="{{ __('messages.notes_placeholder') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                    <i class="bi bi-check-circle"></i> {{ __('messages.confirm_order') }}
                </button>
            </form>
        </div>

        <!-- ملخص الطلب -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="bi bi-receipt"></i> {{ __('messages.order_summary') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold mb-3">{{ __('messages.products') }}:</h6>
                        @foreach ($cart as $id => $item)
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom small">
                                <span>{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                                <span>{{ number_format(($item['discounted_price'] ?? $item['price']) * $item['quantity']) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.subtotal_label') }}:</span>
                            <span class="fw-bold">{{ number_format($cartTotal) }} {{ session('currency_symbol', 'ر.س') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.tax_label') }}:</span>
                            <span class="fw-bold">{{ number_format($tax) }} {{ session('currency_symbol', 'ر.س') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>{{ __('messages.shipping_label') }}:</span>
                            <span class="fw-bold text-success">{{ __('messages.free') }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <strong>{{ __('messages.total_label') }}:</strong>
                        <strong class="price-current" style="font-size: 1.2rem;">
                            {{ number_format($cartTotal * 1.15) }} {{ session('currency_symbol', 'ر.س') }}
                        </strong>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-2">
                        <small class="d-block mb-2">
                            <i class="bi bi-shield-check text-success"></i> {{ __('messages.secure_100_percent') }}
                        </small>
                        <small class="d-block mb-2">
                            <i class="bi bi-lock text-info"></i> {{ __('messages.ssl_encryption') }}
                        </small>
                        <small class="d-block">
                            <i class="bi bi-check-circle text-warning"></i> {{ __('messages.satisfaction_guarantee') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
