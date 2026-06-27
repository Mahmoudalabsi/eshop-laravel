@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-50">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h1 class="h3 fw-black text-dark mb-2">{{ __('messages.reset_password_title') }}</h1>
                            <p class="text-muted">{{ __('messages.enter_email_for_reset') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success rounded-4 mb-4">
                                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-4 mb-4">
                                <ul class="mb-0 small list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold small text-secondary">{{ __('messages.email_address') }}</label>
                                <input type="email"
                                    class="form-control form-control-lg bg-light border-0 rounded-pill px-4" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="example@mail.com" required
                                    autofocus dir="ltr">
                            </div>

                            <div class="d-grid gap-3 mt-5">
                                <button type="submit"
                                    class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm transition-transform">
                                    {{ __('messages.send_reset_link') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-light text-dark border-0 rounded-pill fw-bold">
                                    <i class="bi bi-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-2"></i>{{ __('messages.back_to_login') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            box-shadow: none;
            background-color: #fff !important;
            border: 1px solid #0d6efd !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .transition-transform:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection
