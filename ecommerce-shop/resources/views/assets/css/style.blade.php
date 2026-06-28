<style>
    /* الألوان الـ Soft الخاصة بك مع تدرج خفيف جداً */
    .bg-soft-blue {
        background: linear-gradient(135deg, #e0e7ff 0%, #cbd5e1 100%);
        color: #1e40af;
    }

    .bg-soft-green {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
    }

    .bg-soft-orange {
        background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
        color: #9a3412;
    }

    .bg-soft-cyan {
        background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);
        color: #155e75;
    }

    /* أيقونات ملونة بنفس درجات البطاقات */
    .text-blue {
        color: #3b82f6;
    }

    .text-green {
        color: #22c55e;
    }

    .text-orange {
        color: #f97316;
    }

    .text-cyan {
        color: #06b6d4;
    }

    /* تنسيق الدائرة (نفس حجم Argon لكن شفاف) */
    .icon-shape {
        width: 45px;
        height: 45px;
        background-color: rgba(255, 255, 255, 0.5);
        /* أبيض شفاف ليناسب الـ Soft */
        border-radius: 50%;
    }

    .card {
        border-radius: 15px !important;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        filter: brightness(0.98);
    }

    .counter {
        color: #0f172a;
        /* لون غامق للأرقام لسهولة القراءة */
    }

    .card-title {
        font-weight: 600;
        letter-spacing: 0.5px;
    }


    tbody tr:hover {
        background-color: #f8fafc;
        transition: background-color 0.2s;
    }

    .table thead {
        background-color: #f1f5f9;
    }

    .table th {
        color: #334155;
        font-weight: 600;
    }

    .table td {
        color: #475569;
    }

    body {
        font-family: "Cairo", sans-serif;
        background-color: #f4f7f6;
    }

    .sidebar {
        min-height: 100vh;
        background: #2c3e50;
        color: white;
        padding: 20px 0;
    }

    .sidebar a {
        color: #bdc3c7;
        text-decoration: none;
        padding: 12px 20px;
        display: block;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #34495e;
        color: white;
        border-right: 5px solid #3498db;
    }
</style>
