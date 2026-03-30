<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 padding-right">

                @yield('content_category')

            </div>

            @if (View::hasSection('sidebar'))
                @yield('sidebar')

                <div class="col-sm-9 padding-right">

                    @yield('content')

                </div>
            @else
                <div class="col-sm-12 padding-right">

                    @yield('content')

                </div>
            @endif
            <style type="text/css">
                h3.doitac {
                    text-align: center;
                    font-size: 20px;
                    text-transform: uppercase;
                    margin: 20px;
                    font-weight: bold;
                }

                h4.doitac_name {
                    text-align: center;
                    font-size: 13px;
                }

                .item img {
                    height: 100px;
                }

                button.owl-prev {
                    font-size: 45px !important;

                }

                button.owl-next {
                    font-size: 45px !important;

                }
            </style>
            {{--   <div class="col-md-12">
                <h3 class="doitac">Đối tác của chúng tôi</h3>
                <div class="owl-carousel owl-theme">
                    @foreach ($icons_doitac as $key => $doitac)
                    <div class="item" style="padding-left:0 !important; ">
                        <a target="_blank" href="{{$doitac->link}}"><p><img width="100%" src="{{asset('uploads/icons/'.$doitac->image)}}"></p>
                        <h4 class="doitac_name">{{$doitac->name}}</h4></a>
                    </div>
                    @endforeach
                </div>         
            </div> --}}

        </div>
    </div>
</section>
