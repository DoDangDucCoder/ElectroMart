<input type="hidden" value="{{ $product->product_id }}" class="cart_product_id_{{ $product->product_id }}">
<input type="hidden" value="{{ $product->product_name }}" class="cart_product_name_{{ $product->product_id }}">
<input type="hidden" value="{{ $product->product_quantity }}" class="cart_product_quantity_{{ $product->product_id }}">
<input type="hidden" value="{{ $product->product_image }}" class="cart_product_image_{{ $product->product_id }}">
<input type="hidden" value="{{ $product->final_price }}" class="cart_product_price_{{ $product->product_id }}">
<input type="hidden" value="1" class="cart_product_qty_{{ $product->product_id }}">
