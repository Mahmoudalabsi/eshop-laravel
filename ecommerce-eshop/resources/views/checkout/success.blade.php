@extends('layouts.app')

@section('title', __('messages.order_confirmed') . ' - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <!-- Success Icon -->
            <div style="font-size: 4rem; margin-bottom: 2rem; animation: bounce 1s infinite;">
                ✅
            </div>

            <h1 class="h2 fw-bold mb-3">{{ __('messages.thank_you_message') }}</h1>

            <p class="lead text-muted mb-4">
                {{ __('messages.order_created_message') }} <strong>{{ $order->order_number }}</strong> {{ __('messages.order_processing_soon') }}
            </p>

            <!-- Order Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <small class="text-muted d-block">{{ __('messages.total') }}</small>
                            <h3 class="fw-bold price-current">
                                {{ number_format($order->total) }} {{ $order->currency_code }}
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('messages.order_items_count') }}</small>
                            <h3 class="fw-bold">{{ count($order->items) }} {{ __('messages.item') }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title fw-bold mb-0">{{ __('messages.next_steps') }}</h5>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-flush text-start">
                        <li class="list-group-item d-flex gap-3">
                            <span class="badge bg-primary rounded-pill">1</span>
                            <div>
                                <strong>{{ __('messages.order_confirmation') }}</strong>
                                <p class="text-muted small mb-0">{{ __('messages.confirmation_email_desc') }}</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex gap-3">
                            <span class="badge bg-primary rounded-pill">2</span>
                            <div>
                                <strong>{{ __('messages.order_processing') }}</strong>
                                <p class="text-muted small mb-0">{{ __('messages.processing_desc') }}</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex gap-3">
                            <span class="badge bg-primary rounded-pill">3</span>
                            <div>
                                <strong>{{ __('messages.shipping') }}</strong>
                                <p class="text-muted small mb-0">{{ __('messages.shipping_desc') }}</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex gap-3">
                            <span class="badge bg-primary rounded-pill">4</span>
                            <div>
                                <strong>{{ __('messages.delivery') }}</strong>
                                <p class="text-muted small mb-0">{{ __('messages.delivery_desc') }}</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2 mb-3">
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-eye"></i> {{ __('messages.view_order_details') }}
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-shop"></i> {{ __('messages.continue_shopping') }}
                </a>
            </div>

            <!-- Contact Info -->
            <div class="alert alert-info mt-4">
                <small>
                    <strong>{{ __('messages.need_help') }}</strong><br>
                    {{ __('messages.contact_us_text') }} <a href="tel:+966123456789">+966123456789</a> {{ __('messages.or') }}
                    <a href="mailto:support@example.com">support@example.com</a>
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #e0e0e0;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endsection
