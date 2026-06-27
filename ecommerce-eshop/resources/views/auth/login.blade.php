@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-50">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h1 class="h3 fw-black text-dark mb-2">{{ __('messages.welcome_back') }}</h1>
                            <p class="text-muted">{{ __('messages.login_to_continue') }}</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-4 mb-4">
                                <ul class="mb-0 small list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold small text-secondary">{{ __('messages.email_address') }}</label>
                                <input type="email"
                                    class="form-control form-control-lg bg-light border-0 rounded-pill px-4" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="example@mail.com" required
                                    autofocus dir="ltr">
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="password" class="form-label fw-bold small text-secondary mb-0">{{ __('messages.password') }}</label>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none text-primary small fw-bold">{{ __('messages.forgot_password') }}</a>
                                </div>
                                <input type="password"
                                    class="form-control form-control-lg bg-light border-0 rounded-pill px-4" id="password"
                                    name="password" placeholder="••••••••" required dir="ltr">
                            </div>

                            <div class="d-grid gap-3 mt-5">
                                <button type="submit"
                                    class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm transition-transform">
                                    {{ __('messages.login_btn') }}
                                </button>
                                <a href="{{ route('register') }}" class="btn btn-outline-light text-dark border-0 rounded-pill fw-bold">
                                    {{ __('messages.no_account') }} <span class="text-primary">{{ __('messages.create_account') }}</span>
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
