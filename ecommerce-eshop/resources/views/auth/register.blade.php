@extends('layouts.app')

@section('title', 'إنشاء حساب - ' . config('app.name'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
            <div class="col-md-7 col-lg-6">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden auth-card">
                    <div class="auth-card-glow"></div>
                    <div class="card-body p-5 position-relative">
                        <div class="text-center mb-5">
                            <div class="auth-logo mb-3">ELEGANCE<span>FASHION</span></div>
                            <h1 class="h3 fw-black text-dark mb-2">{{ __('messages.create_account_title') }}</h1>
                            <p class="text-muted">{{ __('messages.join_us') }}</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-4 mb-4 border-0">
                                <ul class="mb-0 small list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold small text-secondary">{{ __('messages.full_name') }}</label>
                                <input type="text"
                                    class="form-control form-control-lg auth-input" id="name"
                                    name="name" value="{{ old('name') }}" placeholder="{{ __('messages.full_name') }}" required autofocus>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold small text-secondary">{{ __('messages.email_address') }}</label>
                                <input type="email"
                                    class="form-control form-control-lg auth-input" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="example@mail.com" required dir="ltr">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="form-label fw-bold small text-secondary">{{ __('messages.password') }}</label>
                                    <input type="password"
                                        class="form-control form-control-lg auth-input" id="password"
                                        name="password" placeholder="••••••••" required dir="ltr">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="password_confirmation" class="form-label fw-bold small text-secondary">{{ __('messages.confirm_password') }}</label>
                                    <input type="password"
                                        class="form-control form-control-lg auth-input" id="password_confirmation"
                                        name="password_confirmation" placeholder="••••••••" required dir="ltr">
                                </div>
                            </div>

                            <div class="d-grid gap-3 mt-4">
                                <button type="submit"
                                    class="btn btn-luxury-primary btn-lg rounded-pill fw-bold shadow-sm">
                                    {{ __('messages.register_btn') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill fw-bold py-3">
                                    {{ __('messages.already_have_account') }} <span class="auth-link">{{ __('messages.login') }}</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .auth-card {
            position: relative;
            background: #fff;
        }

        .auth-card-glow {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(197, 160, 89, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-logo {
            font-weight: 800;
            letter-spacing: 2px;
            font-size: 1.3rem;
            text-transform: uppercase;
        }

        .auth-logo span {
            background: linear-gradient(135deg, #c5a059, #8e6d2f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            margin-right: 4px;
        }

        .auth-input {
            background-color: #f8fafc !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 14px !important;
            padding: 0.85rem 1.25rem !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .auth-input:focus {
            background-color: #fff !important;
            border-color: #c5a059 !important;
            box-shadow: 0 0 0 4px rgba(197, 160, 89, 0.1) !important;
            outline: none;
        }

        .auth-link {
            color: #8e6d2f !important;
        }

        .auth-link:hover {
            color: #c5a059 !important;
        }
    </style>
@endsection
