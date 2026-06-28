@extends('layouts.app')

@section('title', 'تسجيل الدخول - ' . config('app.name'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden auth-card">
                    <div class="auth-card-glow"></div>
                    <div class="card-body p-5 position-relative">
                        <div class="text-center mb-5">
                            <div class="auth-logo mb-3">ELEGANCE<span>FASHION</span></div>
                            <h1 class="h3 fw-black text-dark mb-2">{{ __('messages.welcome_back') }}</h1>
                            <p class="text-muted">{{ __('messages.login_to_continue') }}</p>
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

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold small text-secondary">{{ __('messages.email_address') }}</label>
                                <input type="email"
                                    class="form-control form-control-lg auth-input" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="example@mail.com" required
                                    autofocus dir="ltr">
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="password" class="form-label fw-bold small text-secondary mb-0">{{ __('messages.password') }}</label>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none auth-link small fw-bold">{{ __('messages.forgot_password') }}</a>
                                </div>
                                <input type="password"
                                    class="form-control form-control-lg auth-input" id="password"
                                    name="password" placeholder="••••••••" required dir="ltr">
                            </div>

                            <div class="d-grid gap-3 mt-5">
                                <button type="submit"
                                    class="btn btn-luxury-primary btn-lg rounded-pill fw-bold shadow-sm">
                                    {{ __('messages.login_btn') }}
                                </button>
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary rounded-pill fw-bold py-3">
                                    {{ __('messages.no_account') }} <span class="auth-link">{{ __('messages.create_account') }}</span>
                                </a>
                            </div>
                        </form>

                        <div class="mt-5 p-3 bg-light rounded-4 text-center small">
                            <i class="bi bi-shield-lock-fill me-1 text-gold"></i>
                            <strong>حساب تجريبي للأدمن:</strong><br>
                            <span class="text-muted" dir="ltr">admin@elegance.com / admin123</span>
                        </div>
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
            right: -100px;
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
