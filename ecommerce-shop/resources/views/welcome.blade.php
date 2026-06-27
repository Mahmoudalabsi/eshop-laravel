@extends('eshop.layouts.admin')


@section('content')
    <div class="container mt-5 text-center">
    </div>

    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">مرحباً بك مجدداً!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-person-circle fs-1 text-primary"></i>
                    </div>
                    <h4>أهلاً بك، {{ auth()->user()->name }}</h4>
                    <p class="text-muted">يسعدنا رؤيتك اليوم في لوحة تحكم متجرك.</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal">ابدأ العمل</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                var myModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                myModal.show();
            @endif
        });
    </script>
@endsection
