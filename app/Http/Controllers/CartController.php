<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Checkout\Services\CartService;
use App\Application\Checkout\Services\CouponService;
use App\Application\Checkout\Services\ShippingService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Http\Requests\Storefront\Cart\AddCartAjaxRequest;
use App\Http\Requests\Storefront\Cart\ApplyCouponRequest;
use App\Http\Requests\Storefront\Cart\RemoveCartItemRequest;
use App\Http\Requests\Storefront\Cart\SaveCartRequest;
use App\Http\Requests\Storefront\Cart\UpdateCartQuantityRequest;
use App\Http\Requests\Storefront\Cart\UpdateCartRequest;
use App\Http\Requests\Storefront\Cart\UpdateQuickCartRequest;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
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

    public function show_cart_menu(): Response
    {
        $count = $this->cartService->count();

        return $this->renderCartPartial('pages.cart.partials.cart_badge', compact('count'));
    }

    public function hover_cart(): Response
    {
        $items = $this->cartService->items();

        return $this->renderCartPartial('pages.cart.partials.hover_cart', compact('items'));
    }

    public function check_coupon(ApplyCouponRequest $request)
    {
        $code = trim((string) $request->validated('coupon'));

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

    public function add_cart_ajax(AddCartAjaxRequest $request): JsonResponse
    {
        $validated = $request->validated();

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

    public function show_quick_cart(): Response
    {
        $items = $this->cartService->items();
        $coupon = $this->couponService->current();
        $fee = $this->shippingService->getFee();
        $totals = $this->cartService->totals($coupon, $fee);

        return $this->renderCartPartial('pages.cart.partials.quick_cart', compact('items', 'totals'));
    }

    public function delete_product(Request $request, string $sessionId)
    {
        $this->cartService->removeBySessionId($sessionId);

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng.']);
        }

        return Redirect::back()->with('message', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function cart_session(): Response
    {
        $items = $this->cartService->items();

        return $this->renderCartPartial('pages.cart.partials.cart_session_inputs', compact('items'));
    }

    public function remove_item(RemoveCartItemRequest $request): JsonResponse
    {
        $this->cartService->removeByProductId((int) $request->validated('id'));

        return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng.']);
    }

    public function update_cart(UpdateCartRequest $request)
    {
        $quantities = (array) $request->validated('cart_qty');
        $messages = $this->cartService->updateQuantities($quantities);
        $message = implode('<br>', $messages);

        return Redirect::back()->with('message', $message ?: 'Đã cập nhật giỏ hàng.');
    }

    public function update_quick_cart(UpdateQuickCartRequest $request): JsonResponse
    {
        $validated = $request->validated();

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

    public function save_cart(SaveCartRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->cartService->addItem((int) $validated['productid_hidden'], (int) $validated['qty']);
        } catch (RuntimeException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::to('/gio-hang')->with('message', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function show_cart(): Response
    {
        $count = $this->cartService->count();

        return $this->renderCartPartial('pages.cart.partials.cart_badge', compact('count'));
    }

    public function delete_to_cart(Request $request, string $sessionId)
    {
        return $this->delete_product($request, $sessionId);
    }

    public function update_cart_quantity(UpdateCartQuantityRequest $request)
    {
        $validated = $request->validated();

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
            'slider' => $this->legacyActiveSliders(),
            'category' => $this->categoryService->listAll(['is_active' => true]),
            'brand' => $this->brandService->listAll(['is_active' => true]),
        ];
    }

    protected function legacyActiveSliders()
    {
        if (! Schema::hasTable('tbl_slider')) {
            return collect();
        }

        return Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
    }

    protected function renderCartPartial(string $view, array $data = []): Response
    {
        return response()->view($view, $data);
    }
}
