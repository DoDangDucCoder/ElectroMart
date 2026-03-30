        {{-- Admin content shell and footer extracted from admin_layout.blade.php. --}}
        <!--sidebar end-->
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                @yield('admin_content')
            </section>
            <!-- footer -->
            <div class="footer">
                <div class="wthree-copyright">
                    <p>&copy; <span id="year"></span> Your Company Name. All Rights Reserved.</p>
                </div>
            </div>

            <script>
                document.getElementById('year').textContent = new Date().getFullYear();
            </script>
            <!-- / footer -->
        </section>
        <!--main content end-->
