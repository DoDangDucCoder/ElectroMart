<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatePost;
use App\Models\Slider;
use App\Models\Icons;
use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;

class HomeController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly ProductService $productService,
        private readonly BrandService $brandService
    ) {
    }

    public function error_page()
    {
        return view('errors.404');
    }

    public function load_more_product(Request $request)
    {
        $lastId = $request->integer('id') ?: null;
        $products = $this->productService->loadMoreProducts($lastId, 6);

        if ($products->isEmpty()) {
            return response('
                <div id="load_more">
                    <button type="button" name="load_more_button" class="btn btn-default form-control">Dữ liệu đang cập nhật thêm...</button>
                </div>
            ');
        }

        $output = '';
        foreach ($products as $product) {
            $productUrl = url('chi-tiet/' . $product->product_slug);
            $imagePath = $product->product_image ? asset($product->product_image) : asset('frontend/images/no-image.png');
            $priceFormatted = number_format($product->product_price, 0, ',', '.') . 'VND';

            $output .= '
                <div class="col-sm-4">
                    <div class="product-image-wrapper">
                        <div class="single-products">
                            <div class="productinfo text-center">
                                <input type="hidden" id="wishlist_productname' . $product->product_id . '" value="' . e($product->product_name) . '" class="cart_product_name_' . $product->product_id . '">
                                <input type="hidden" id="wishlist_productprice' . $product->product_id . '" value="' . $priceFormatted . '">
                                <input type="hidden" value="' . $product->product_image . '" class="cart_product_image_' . $product->product_id . '">
                                <input type="hidden" value="' . $product->product_price . '" class="cart_product_price_' . $product->product_id . '">
                                <input type="hidden" value="' . $product->product_quantity . '" class="cart_product_quantity_' . $product->product_id . '">
                                <input type="hidden" value="1" class="cart_product_qty_' . $product->product_id . '">
                                <a id="wishlist_producturl' . $product->product_id . '" href="' . $productUrl . '">
                                    <img id="wishlist_productimage' . $product->product_id . '" src="' . $imagePath . '" alt="' . e($product->product_name) . '" />
                                    <p class="tieude-product_name">' . e($product->product_name) . '</p>
                                    <h2>' . $priceFormatted . '</h2>
                                </a>

                                <input type="hidden" class="cart_product_id_attribute" value="0">
                                <input type="hidden" class="product_id" value="' . $product->product_id . '">
                                <button class="btn btn-default home_cart_' . $product->product_id . '" id="' . $product->product_id . '" onclick="Addtocart(this.id);">
                                    <i class="fa fa-shopping-cart"></i>Thêm giỏ hàng
                                </button>
                                <button style="display:none" class="btn btn-danger rm_home_cart_' . $product->product_id . '" id="' . $product->product_id . '" onclick="Deletecart(this.id);">
                                    <i class="fa fa-shopping-cart"></i>Bỏ đã thêm
                                </button>
                                <input type="button" data-toggle="modal" data-target="#xemnhanh" onclick="XemNhanh(this.id);" value="Xem nhanh" class="btn btn-default" id="' . $product->product_id . '" name="add-to-cart">
                            </div>
                        </div>
                        <div class="choose">
                            <ul class="nav nav-pills nav-justified">
                                <li>
                                    <i class="fa fa-plus-square"></i>
                                    <button class="button_wishlist" id="' . $product->product_id . '" onclick="add_wistlist(this.id);"><span>Yêu thích</span></button>
                                </li>
                                <li><a style="cursor: pointer;" onclick="add_compare(' . $product->product_id . ')"><i class="fa fa-plus-square"></i>So sánh</a></li>
                            </ul>
                        </div>
                    </div>
                </div>';
        }

        $lastLoadedId = $products->last()->product_id;
        $output .= '
            <div id="load_more">
                <button type="button" name="load_more_button" class="btn btn-primary form-control" data-id="' . $lastLoadedId . '" id="load_more_button">
                    Load thêm sản phẩm
                </button>
            </div>
        ';

        return response($output);
    }

    public function index(Request $request)
    {
        $icons = Icons::orderBy('id_icons', 'DESC')->get();
        $category_post = CatePost::orderBy('cate_post_id', 'DESC')->get();
        $slider = Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();

        $meta_desc = "Chuyên bán những phụ kiện ,thiết bị game";
        $meta_keywords = "thiet bi game,phu kien game,game phu kien,game giai tri";
        $meta_title = "Laravel 11 web bán hàng";
        $url_canonical = $request->url();

        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);
        $all_product = $this->productService->getRandomProducts(6);
        $all_product->appends($request->query());
        $cate_pro_tabs = $this->categoryService->listRootActive();

        return view('home', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'all_product' => $all_product,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'meta_title' => $meta_title,
            'url_canonical' => $url_canonical,
            'slider' => $slider,
            'category_post' => $category_post,
            'cate_pro_tabs' => $cate_pro_tabs,
            'icons' => $icons,
        ]);
    }

    public function yeu_thich(Request $request)
    {
        $category_post = CatePost::orderBy('cate_post_id', 'DESC')->get();
        $slider = Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();

        $meta_desc = "Yêu thích";
        $meta_keywords = "Yêu thích";
        $meta_title = "Yêu thích";
        $url_canonical = $request->url();

        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);
        $all_product = $this->productService->getRandomProducts(6);
        $all_product->appends($request->query());

        return view('pages.yeuthich.yeuthich', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'all_product' => $all_product,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'meta_title' => $meta_title,
            'url_canonical' => $url_canonical,
            'slider' => $slider,
            'category_post' => $category_post,
        ]);
    }

    public function search(Request $request)
    {
        $category_post = CatePost::orderBy('cate_post_id', 'DESC')->get();
        $slider = Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();

        $meta_desc = "Tìm kiếm sản phẩm";
        $meta_keywords = "Tìm kiếm sản phẩm";
        $meta_title = "Tìm kiếm sản phẩm";
        $url_canonical = $request->url();
        $keywords = $request->get('keywords_submit');

        $cate_product = $this->categoryService->listActive();
        $brand_product = $this->brandService->listAll(['is_active' => true]);

        $search_product = collect();
        if ($keywords) {
            $search_product = $this->productService->searchProducts($keywords);
        }

        return view('pages.sanpham.search', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'search_product' => $search_product,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'meta_title' => $meta_title,
            'url_canonical' => $url_canonical,
            'slider' => $slider,
            'category_post' => $category_post,
        ]);
    }

    public function autocomplete_ajax(Request $request)
    {
        $keyword = $request->input('query');

        if (! $keyword) {
            return response('');
        }

        $product = $this->productService->searchProducts($keyword, 8);

        if ($product->isEmpty()) {
            return response('');
        }

        $output = '<ul class="dropdown-menu" style="display:block; position:absolute">';
        foreach ($product as $item) {
            $output .= '<li class="li_search_ajax"><a href="#">' . e($item->product_name) . '</a></li>';
        }
        $output .= '</ul>';

        return response($output);
    }
}
