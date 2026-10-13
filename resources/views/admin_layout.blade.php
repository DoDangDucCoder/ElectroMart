<!DOCTYPE html>

@include('partials.admin.head')

<body class="g-admin-shell-modern @yield('admin_body_class')">
    <section id="container" class="g-admin-shell" data-admin-shell>
        @include('partials.admin.header')
        @include('partials.admin.sidebar')
        @include('partials.admin.content-shell')
    </section>
    @include('partials.admin.scripts-stack')
    <script src="{{ asset('backend/js/stat-card-filter.js') }}"></script>
</body>

</html>
