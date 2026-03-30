<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Media\StoreContactInfoRequest;
use App\Http\Requests\Admin\Legacy\Media\StoreIconRequest;
use App\Http\Requests\Admin\Legacy\Media\UpdateContactInfoRequest;
use App\Models\Contact;
use App\Models\Icons;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class ContactController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
        private readonly BlogCategoryService $blogCategoryService,
        private readonly PublicUploadService $publicUploadService,
    ) {
    }

    public function add_doitac(StoreIconRequest $request)
    {
        if (! Schema::hasTable((new Icons())->getTable())) {
            return response()->json(['message' => 'Bảng icon chưa sẵn sàng.'], 422);
        }

        $data = $request->validated();

        $icons = new Icons();
        $get_image = $request->file('file');

        //them hinh anh
        if ($get_image) {
            $icons->image = $this->publicUploadService->storeLegacyImage($get_image, 'uploads/icons');
        }
        $icons->name = $data['name'];
        $icons->link = $data['link'] ?? null;
        $icons->category = 'doitac';

        $icons->save();

        return response()->noContent();
    }
    public function add_nut(StoreIconRequest $request)
    {
        if (! Schema::hasTable((new Icons())->getTable())) {
            return response()->json(['message' => 'Bảng icon chưa sẵn sàng.'], 422);
        }

        $data = $request->validated();

        $icons = new Icons();
        $get_image = $request->file('file');

        //them hinh anh
        if ($get_image) {
            $icons->image = $this->publicUploadService->storeLegacyImage($get_image, 'uploads/icons');
        }
        $icons->name = $data['name'];
        $icons->link = $data['link'] ?? null;
        $icons->category = 'icons';

        $icons->save();

        return response()->noContent();
    }
    public function delete_icons(Request $request)
    {
        $icons = Icons::find($request->integer('id'));

        if ($icons) {
            $icons->delete();
        }

        return response()->noContent();
    }

    public function list_doitac()
    {
        if (! Schema::hasTable((new Icons())->getTable())) {
            return response('<div class="g-admin-empty-state">Chưa có bảng đối tác/icons trên môi trường này.</div>');
        }

        $icons = Icons::where('category', 'doitac')->orderBy('id_icons', 'DESC')->get();

        return response()->view('admin.information.partials.icons_table', [
            'icons' => $icons,
            'nameLabel' => 'Tên đối tác',
            'imageLabel' => 'Hình ảnh đối tác',
            'linkLabel' => 'Link đối tác',
            'imageHeight' => 80,
            'imageWidth' => 150,
            'buttonClass' => 'btn-warning',
            'buttonLabel' => 'Xóa đối tác',
        ]);
    }
    public function list_nut()
    {
        if (! Schema::hasTable((new Icons())->getTable())) {
            return response('<div class="g-admin-empty-state">Chưa có bảng đối tác/icons trên môi trường này.</div>');
        }

        $icons = Icons::where('category', 'icons')->orderBy('id_icons', 'DESC')->get();

        return response()->view('admin.information.partials.icons_table', [
            'icons' => $icons,
            'nameLabel' => 'Tên nút',
            'imageLabel' => 'Hình ảnh',
            'linkLabel' => 'Link',
            'imageHeight' => 32,
            'imageWidth' => 32,
            'buttonClass' => 'btn-danger',
            'buttonLabel' => 'Xóa',
        ]);
    }
    public function lien_he(Request $request)
    {
        $contact = Schema::hasTable((new Contact())->getTable())
            ? Contact::where('info_id', 1)->get()
            : collect();

        return view('pages.lienhe.contact', [
            'category' => $this->categoryService->listAll(['is_active' => true]),
            'brand' => $this->brandService->listAll(['is_active' => true]),
            'meta_desc' => 'Liên hệ',
            'meta_keywords' => 'Liên hệ',
            'meta_title' => 'Liên hệ chúng tôi',
            'url_canonical' => $request->url(),
            'slider' => $this->legacyActiveSliders(),
            'category_post' => $this->blogCategoryService->listAll(['is_active' => true]),
            'contact' => $contact,
        ]);
    }
    public function information()
    {
        $contact = Schema::hasTable((new Contact())->getTable())
            ? Contact::where('info_id', 1)->get()
            : collect();

        return view('admin.information.add_information')->with(compact('contact'));
    }
    public function update_info(UpdateContactInfoRequest $request, $info_id)
    {
        if (! Schema::hasTable((new Contact())->getTable())) {
            return redirect()->back()->with('message', 'Bảng thông tin website chưa sẵn sàng.');
        }

        $data = $request->validated();

        $contact = Contact::find($info_id);
        if (! $contact) {
            return redirect()->back()->with('message', 'Không tìm thấy thông tin website cần cập nhật');
        }

        $contact->info_contact = $data['info_contact'];
        $contact->slogan_logo = $data['slogan_logo'] ?? null;
        $contact->info_map = $data['info_map'] ?? null;
        $contact->info_fanpage = $data['info_fanpage'] ?? null;
        $get_image = $request->file('info_image');
        if ($get_image) {
            $this->publicUploadService->deletePublicFile('uploads/contact/' . $contact->info_logo);
            $contact->info_logo = $this->publicUploadService->storeLegacyImage($get_image, 'uploads/contact');
        }

        $contact->save();
        return redirect()->back()->with('message', 'Cập nhật thông tin website thành công');
    }
    public function save_info(StoreContactInfoRequest $request)
    {
        if (! Schema::hasTable((new Contact())->getTable())) {
            return redirect()->back()->with('message', 'Bảng thông tin website chưa sẵn sàng.');
        }

        $data = $request->validated();

        $contact = new Contact();
        $contact->info_contact = $data['info_contact'];
        $contact->info_map = $data['info_map'] ?? null;
        $contact->info_fanpage = $data['info_fanpage'] ?? null;
        $get_image = $request->file('info_image');
        if ($get_image) {
            $contact->info_logo = $this->publicUploadService->storeLegacyImage($get_image, 'uploads/contact');
        }

        $contact->save();
        return redirect()->back()->with('message', 'Cập nhật thông tin website thành công');
    }

    protected function legacyActiveSliders()
    {
        if (! Schema::hasTable('tbl_slider')) {
            return collect();
        }

        return Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
    }
}
