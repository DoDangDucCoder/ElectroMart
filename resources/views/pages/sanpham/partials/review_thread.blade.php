@if ($threads->isEmpty())
    <div class="style_comment">
        <div class="row">
            <div class="col-md-12">
                @include('partials.storefront.empty-state', [
                    'title' => 'Chưa có bình luận nào',
                    'message' => 'Hãy trở thành người đầu tiên chia sẻ trải nghiệm của bạn về sản phẩm này.',
                    'icon' => 'fa fa-comment-o',
                    'compact' => true,
                    'class' => 'shopw-review-empty',
                ])
            </div>
        </div>
    </div>
@else
    @foreach ($threads as $review)
        @include('pages.sanpham.partials.review_thread_item', [
            'review' => $review,
            'isReply' => false,
        ])
    @endforeach
@endif

