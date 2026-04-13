<!DOCTYPE html>

@include('partials.admin.head')

<body>
    <section id="container" class="g-admin-shell" data-admin-shell>
        @include('partials.admin.header')
        @include('partials.admin.sidebar')
        @include('partials.admin.content-shell')
    </section>
    @include('partials.admin.scripts-stack')
</body>

</html>
