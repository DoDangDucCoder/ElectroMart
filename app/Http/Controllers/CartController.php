<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Checkout\Services\CartService;
use App\Application\Checkout\Services\CouponService;
use App\Application\Checkout\Services\ShippingService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly ShippingService $shippingService,
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
        private readonly BlogCategoryService $blogCategoryService,
    ) {
    }

    public function show_cart_menu(): string
    {
        $count = $this->cartService->count();

        return '<span class="badges">' . $count . '</span>';
    }

    public function hover_cart(): string
    {
        $items = $this->cartService->items();
        $output = '';

        if ($items->isEmpty()) {
            return $output;
        }

        $output .= '<ul class="hover-cart">';
        foreach ($items as $item) {
            $image = $item['product_image'] ? asset($item['product_image']) : asset('frontend/images/no-image.png');
            $output .= '<li>
                    <a href="#">
                        <img src="' . $image . '">
                        <p>' . e($item['product_name']) . '</p>
                        <p>' . number_format($item['product_price'], 0, ',', '.') . ' VND</p>
                        <p>Số lượng: ' . $item['product_qty'] . '</p>
                    </a>
                    <p>
                        <a style="text-align:center;font-size:20px" class="delele-hover-cart" href="' . url('/del-product/' . $item['session_id']) . '">
                            <i class="fa fa-times"></i>
                        </a>
                    </p>
                </li>';
        }
        $output .= '</ul>';

        return $output;
    }

    public function check_coupon(Request $request)
    {
        $request->validate([
            'coupon' => ['required', 'string', 'max:50'],
        ]);

        $code = trim($request->input('coupon'));

        try {
            $this->couponService->apply(
                $code,
                $this->cartService->subtotal(),
                Session::get('customer_id')
            );
        } catch (RuntimeException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back()->with('message', 'Áp dụng mã giảm giá thành công.');
    }

    public function gio_hang(Request $request)
    {
        return view('pages.cart.cart_ajax', array_merge(
            $this->catalogContext(),
            $this->cartContext(),
            [
                'meta_desc' => 'Giỏ hàng của bạn',
                'meta_keywords' => 'Giỏ hàng',
                'meta_title' => 'Giỏ hàng',
                'url_canonical' => $request->url(),
            ]
        ));
    }

    public function add_cart_ajax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_product' => ['required', 'integer'],
            'id_attribute' => ['nullable', 'integer'],
            'cart_product_qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $productId = (int) $validated['id_product'];
        $attributeId = (int) ($validated['id_attribute'] ?? 0) ?: null;
        $quantity = max(1, (int) ($validated['cart_product_qty'] ?? 1));

        try {
            $item = $this->cartService->addItem($productId, $quantity, $attributeId);
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
            'cart_count' => $this->cartService->count(),
            'cart_items' => $this->cartService->items()->values(),
            'cart_totals' => $this->cartService->totals(
                $this->couponService->current(),
                $this->shippingService->getFee()
            ),
            'item' => $item,
        ]);
    }

    public function show_quick_cart(): string
    {
        $items = $this->cartService->items();
        $coupon = $this->couponService->current();
        $fee = $this->shippingService->getFee();
        $totals = $this->cartService->totals($coupon, $fee);

        $html = '
            <form>
                ' . csrf_field() . '
                <table class="table table-condensed">
                    <thead>
                        <tr class="cart_menu">
                            <td class="image">Hình ảnh</td>
                            <td class="description">Tên sản phẩm</td>
                            <td class="description">Thuộc tính</td>
                            <td class="price">Giá</td>
                            <td class="quantity">Số lượng</td>
                            <td class="total">Thành tiền</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>';

        if ($items->isEmpty()) {
            $html .= '<tr><td colspan="7" class="text-center">Giỏ hàng trống.</td></tr>';
        } else {
            foreach ($items as $item) {
                $image = $item['product_image'] ? asset($item['product_image']) : asset('frontend/images/no-image.png');
                $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];

                $html .= '<tr>
                        <td><img src="' . $image . '" alt="' . e($item['product_name']) . '" width="80"></td>
                        <td>' . e($item['product_name']) . '</td>
                        <td>' . e($item['product_attribute'] ?? '-') . '</td>
                        <td>' . number_format($item['product_price'], 0, ',', '.') . ' VND</td>
                        <td><input type="number" class="cart_quantity_input" name="cart_qty[' . $item['session_id'] . ']" value="' . $item['product_qty'] . '" min="1"></td>
                        <td>' . number_format($subtotal, 0, ',', '.') . ' VND</td>
                        <td><a href="' . url('/del-product/' . $item['session_id']) . '" class="btn btn-danger btn-sm">Xóa</a></td>
                    </tr>';
            }

            $html .= '<tr class="cart_timing">
                    <td colspan="7">
                        <strong>Tạm tính:</strong> ' . number_format($totals['subtotal'], 0, ',', '.') . ' VND<br>
                        <strong>Giảm giá:</strong> -' . number_format($totals['discount'], 0, ',', '.') . ' VND<br>
                        <strong>Phí vận chuyển:</strong> ' . number_format($totals['shipping'], 0, ',', '.') . ' VND<br>
                        <strong>Tổng:</strong> ' . number_format($totals['total'], 0, ',', '.') . ' VND
                    </td>
                </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </form>';

        return $html;
    }

    public function delete_product(string $sessionId)
    {
        $this->cartService->removeBySessionId($sessionId);

        return Redirect::back()->with('message', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function cart_session(): string
    {
        $items = $this->cartService->items();
        $output = '';

        foreach ($items as $item) {
            $output .= '<input type="hidden" class="cart_id" value="' . $item['product_id'] . '">';
        }

        return $output;
    }

    public function remove_item(Request $request): JsonResponse
    {
        $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $this->cartService->removeByProductId((int) $request->input('id'));

        return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng.']);
    }

    public function update_cart(Request $request)
    {
        $quantities = (array) $request->input('cart_qty', []);
        $messages = $this->cartService->updateQuantities($quantities);
        $message = implode('<br>', $messages);

        return Redirect::back()->with('message', $message ?: 'Đã cập nhật giỏ hàng.');
    }

    public function update_quick_cart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->cartService->updateQuantity($validated['session_id'], (int) $validated['quantity']);

        return response()->json(['message' => 'Đã cập nhật số lượng.']);
    }

    public function delete_all_product()
    {
        $this->cartService->clear();
        $this->couponService->clear();
        $this->shippingService->clearFee();

        return Redirect::back()->with('message', 'Đã xóa giỏ hàng.');
    }

    public function save_cart(Request $request)
    {
        $validated = $request->validate([
            'productid_hidden' => ['required', 'integer'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->cartService->addItem((int) $validated['productid_hidden'], (int) $validated['qty']);
        } catch (RuntimeException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::to('/gio-hang')->with('message', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function show_cart()
    {
        return Redirect::to('/gio-hang');
    }

    public function delete_to_cart(string $sessionId)
    {
        return $this->delete_product($sessionId);
    }

    public function update_cart_quantity(Request $request)
    {
        $validated = $request->validate([
            'rowId_cart' => ['required', 'string'],
            'cart_quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->cartService->updateQuantity($validated['rowId_cart'], (int) $validated['cart_quantity']);

        return Redirect::to('/gio-hang')->with('message', 'Đã cập nhật giỏ hàng.');
    }

    protected function cartContext(): array
    {
        $coupon = $this->couponService->current();
        $shippingFee = $this->shippingService->getFee();

        return [
            'cartItems' => $this->cartService->items(),
            'cartTotals' => $this->cartService->totals($coupon, $shippingFee),
            'coupon' => $coupon,
            'shipping_fee' => $shippingFee,
        ];
    }

    protected function catalogContext(): array
    {
        return [
            'category_post' => $this->blogCategoryService->listAll(['is_active' => true]),
            'slider' => Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get(),
            'category' => $this->categoryService->listAll(['is_active' => true]),
            'brand' => $this->brandService->listAll(['is_active' => true]),
        ];
    }
}
