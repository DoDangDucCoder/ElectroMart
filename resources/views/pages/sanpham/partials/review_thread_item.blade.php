@php
    $avatar = $isReply ? url('frontend/images/businessman.jpg') : url('frontend/images/batman-icon.png');
    $authorColor = $isReply ? 'color:blue;' : 'color:green;';
    $wrapperStyle = $isReply ? 'margin:5px 40px;' : '';
@endphp

<div class="row style_comment" style="{{ $wrapperStyle }}">
    <div class="col-md-2">
        <img width="100%" src="{{ $avatar }}" class="img img-responsive img-thumbnail">
    </div>
    <div class="col-md-10">
        <p style="{{ $authorColor }}">{{ '@' . $review->comment_name }}</p>
        @if (! $isReply)
            <p style="color:#000;">{{ $review->comment_date }}</p>
        @endif
        <p>{{ $review->comment }}</p>
    </div>
</div>
<p></p>

@unless ($isReply)
    @foreach ($review->children as $reply)
        @include('pages.sanpham.partials.review_thread_item', [
            'review' => $reply,
            'isReply' => true,
        ])
    @endforeach
@endunless
