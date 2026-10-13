{{-- Admin head assets and meta extracted from admin_layout.blade.php. --}}
<head>
    <title>CMS SHOP | Quản trị</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords"
        content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preload" href="{{ asset('backend/fonts/fontawesome-webfont.woff2?v=4.6.3') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('backend/css/app.css') }}" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/vendor/jquery-ui/jquery-ui.min.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('backend/css/morris.css') }}">


    <link rel="stylesheet" href="{{ asset('backend/css/jquery.dataTables.min.css') }}" type="text/css" />
    <style>
        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc,
        table.dataTable thead .sorting_asc_disabled,
        table.dataTable thead .sorting_desc_disabled {
            background-image: none !important;
        }
        .adm-th-sort{color:inherit;text-decoration:none;display:inline-flex;align-items:center;gap:3px;white-space:nowrap;transition:color .15s}
        .adm-th-sort:hover{color:#4361ee;text-decoration:none}
        .adm-th-sort .fa-sort{opacity:.28;font-size:11px}
        .adm-th-sort.sort-active .fa{opacity:1;color:#4361ee;font-size:11px}
    </style>

    <link rel="icon" href="{{ asset('frontend/images/logo-mail.png') }}" type="image/gif" sizes="32x32">
    <!-- calendar -->


    <!-- //calendar -->
    <script src="{{ asset('backend/js/app.js') }}?v={{ filemtime(public_path('backend/js/app.js')) }}"></script>

    <script src="{{ asset('backend/js/raphael-min.js') }}"></script>

    <script src="{{ asset('backend/js/morris.js') }}"></script>

    <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>

</head>
