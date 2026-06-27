<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        const productModal = document.getElementById('viewProductModal');

        if (productModal) {
            productModal.addEventListener('hide.bs.modal', function() {
                productModal.setAttribute('inert', '');
                productModal.removeAttribute('aria-hidden');
            });

            productModal.addEventListener('shown.bs.modal', function() {
                productModal.removeAttribute('inert');
            });
        }
    });

    // إعداد تنبيهات الـ Toast (الصغيرة التي تظهر في الزاوية)
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Global utility function to handle image URLs
    // Returns proper URL for both local storage images and external HTTP URLs
    function getImageUrl(imagePath) {
        if (!imagePath) return '/assets/img/no-image.png';
        if (imagePath.startsWith('http')) return imagePath; // External URL - return as is
        return `/storage/${imagePath}`; // Local storage - add /storage/ prefix
    }

    // Comprehensive function to handle server responses
    const Notify = {
        success: (msg) => Toast.fire({
            icon: 'success',
            title: msg
        }),
        error: (msg) => Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: msg,
            confirmButtonColor: '#3085d6'
        }),
        confirm: async (title, text) => {
            return await Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            });
        }
    };

    // Auto-show Laravel session messages
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                html: `
                    <ul class="text-end list-unstyled mb-0">
                        @foreach ($errors->all() as $error)
                            <li class="mb-1">• {{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonColor: '#d33'
            });
        @endif
    });
</script>
