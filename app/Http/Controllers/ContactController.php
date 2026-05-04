<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Application\Shared\Services\PublicUploadService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Admin\Legacy\Media\StoreContactInfoRequest;
use App\Http\Requests\Admin\Legacy\Media\StoreIconRequest;
use App\Http\Requests\Admin\Legacy\Media\UpdateContactInfoRequest;
use App\Http\Requests\Storefront\Contact\StoreContactMessageRequest;
use App\Infrastructure\Persistence\Eloquent\Cms\ContactMessage;
use App\Infrastructure\Persistence\Eloquent\Cms\SiteContact;
use App\Infrastructure\Persistence\Eloquent\Cms\SiteLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class ContactController extends Controller
{
    use LegacyStorefrontContext;

    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
        private readonly BlogCategoryService $blogCategoryService,
        private readonly PublicUploadService $publicUploadService,
    ) {
    }

    public function add_doitac(StoreIconRequest $request)
    {
        if (! $this->siteLinksTableExists()) {
            return response()->json(['message' => 'Bảng icon chưa sẵn sàng.'], 422);
        }

        $data = $request->validated();
        $get_image = $request->file('file');
        $imagePath = $get_image
            ? 'uploads/icons/'.$this->publicUploadService->storeLegacyImage($get_image, 'uploads/icons')
            : null;

        SiteLink::create([
            'name' => $data['name'],
            'category' => 'partner',
            'url' => $data['link'] ?: '#',
            'icon_path' => $imagePath,
            'display_order' => (int) (SiteLink::max('display_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return response()->noContent();
    }
    public function add_nut(StoreIconRequest $request)
    {
        if (! $this->siteLinksTableExists()) {
            return response()->json(['message' => 'Bảng icon chưa sẵn sàng.'], 422);
        }

        $data = $request->validated();
        $get_image = $request->file('file');
        $imagePath = $get_image
            ? 'uploads/icons/'.$this->publicUploadService->storeLegacyImage($get_image, 'uploads/icons')
            : null;

        SiteLink::create([
            'name' => $data['name'],
            'category' => 'social',
            'url' => $data['link'] ?: '#',
            'icon_path' => $imagePath,
            'display_order' => (int) (SiteLink::max('display_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return response()->noContent();
    }
    public function delete_icons(Request $request)
    {
        if (! $this->siteLinksTableExists()) {
            return response()->noContent();
        }

        $icons = SiteLink::find($request->integer('id'));

        if ($icons) {
            $this->publicUploadService->deletePublicFile($icons->icon_path);
            $icons->delete();
        }

        return response()->noContent();
    }

    public function list_doitac()
    {
        if (! $this->siteLinksTableExists()) {
            return response('<div class="g-admin-empty-state">Chưa có bảng đối tác/icons trên môi trường này.</div>');
        }

        $icons = SiteLink::query()
            ->where('category', 'partner')
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (SiteLink $link) => $this->mapSiteLinkToLegacyIconShape($link));

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
        if (! $this->siteLinksTableExists()) {
            return response('<div class="g-admin-empty-state">Chưa có bảng đối tác/icons trên môi trường này.</div>');
        }

        $icons = SiteLink::query()
            ->where('category', 'social')
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (SiteLink $link) => $this->mapSiteLinkToLegacyIconShape($link));

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
        $contact = $this->siteContactCollection();

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

    public function submitContact(StoreContactMessageRequest $request)
    {
        if (! Schema::hasTable((new ContactMessage())->getTable())) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Hệ thống chưa sẵn sàng nhận liên hệ. Vui lòng thử lại sau.');
        }

        $data = $request->validated();

        ContactMessage::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return Redirect::back()->with('message', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
    }

    public function information()
    {
        $contact = $this->siteContactCollection();

        return view('admin.information.add_information')->with(compact('contact'));
    }
    public function update_info(UpdateContactInfoRequest $request, $info_id)
    {
        if (! $this->siteContactsTableExists()) {
            return Redirect::to('/information')->with('message', 'Bảng thông tin website chưa sẵn sàng.');
        }

        $data = $request->validated();

        $contact = SiteContact::find($info_id);
        if (! $contact) {
            return Redirect::to('/information')->with('message', 'Không tìm thấy thông tin website cần cập nhật');
        }

        $this->fillSiteContactFromRequest($contact, $data, $request);

        return Redirect::to('/information')->with('message', 'Cập nhật thông tin website thành công');
    }
    public function save_info(StoreContactInfoRequest $request)
    {
        if (! $this->siteContactsTableExists()) {
            return Redirect::to('/information')->with('message', 'Bảng thông tin website chưa sẵn sàng.');
        }

        $data = $request->validated();

        $contact = SiteContact::query()->orderBy('id')->first() ?? new SiteContact();
        $this->fillSiteContactFromRequest($contact, $data, $request);

        return Redirect::to('/information')->with('message', 'Cập nhật thông tin website thành công');
    }

    protected function siteLinksTableExists(): bool
    {
        return Schema::hasTable((new SiteLink())->getTable());
    }

    protected function siteContactsTableExists(): bool
    {
        return Schema::hasTable((new SiteContact())->getTable());
    }

    protected function siteContactCollection()
    {
        if (! $this->siteContactsTableExists()) {
            return collect();
        }

        return SiteContact::query()
            ->orderBy('id')
            ->limit(1)
            ->get()
            ->map(fn (SiteContact $contact) => $this->mapSiteContactToLegacyShape($contact));
    }

    protected function fillSiteContactFromRequest(SiteContact $contact, array $data, Request $request): void
    {
        $contact->company_name = config('services.storefront_brand.name', 'ShopW');
        $contact->about = $data['info_contact'];
        $contact->map_embed = $data['info_map'] ?? null;
        $contact->social_links = [
            'fanpage_embed' => $data['info_fanpage'] ?? null,
        ];
        $contact->meta = array_filter([
            'slogan_logo' => $data['slogan_logo'] ?? null,
        ], static fn ($value) => $value !== null && $value !== '');

        $get_image = $request->file('info_image');
        if ($get_image) {
            $this->publicUploadService->deletePublicFile($contact->logo_path);
            $contact->logo_path = 'uploads/contact/'.$this->publicUploadService->storeLegacyImage($get_image, 'uploads/contact');
        }

        $contact->save();
    }

    protected function mapSiteContactToLegacyShape(SiteContact $contact): object
    {
        $socialLinks = is_array($contact->social_links) ? $contact->social_links : [];
        $meta = is_array($contact->meta) ? $contact->meta : [];
        $logoPath = trim((string) $contact->logo_path);

        return (object) [
            'info_id' => $contact->id,
            'info_contact' => $contact->about,
            'info_logo' => $logoPath !== '' ? basename($logoPath) : null,
            'slogan_logo' => $meta['slogan_logo'] ?? null,
            'info_map' => $contact->map_embed,
            'info_fanpage' => $socialLinks['fanpage_embed'] ?? null,
        ];
    }

    protected function mapSiteLinkToLegacyIconShape(SiteLink $link): object
    {
        $iconPath = trim((string) $link->icon_path);

        return (object) [
            'id_icons' => $link->id,
            'name' => $link->name,
            'link' => $link->url,
            'category' => $link->category,
            'image' => $iconPath !== '' ? basename($iconPath) : null,
        ];
    }

}
