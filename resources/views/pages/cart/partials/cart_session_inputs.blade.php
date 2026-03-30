@foreach ($items as $item)
    <input type="hidden" class="cart_id" value="{{ $item['product_id'] }}">
@endforeach
