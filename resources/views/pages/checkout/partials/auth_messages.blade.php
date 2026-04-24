@if (session()->has('message'))
    <div class="alert alert-success">
        {!! session()->get('message') !!}
    </div>
@elseif(session()->has('error'))
    <div class="alert alert-danger">
        {!! session()->get('error') !!}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled" style="margin: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
