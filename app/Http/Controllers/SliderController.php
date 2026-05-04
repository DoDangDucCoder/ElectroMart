<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Media\StoreSliderRequest;
use App\Http\Requests\Admin\Legacy\Media\UpdateSliderRequest;
use App\Infrastructure\Persistence\Eloquent\Cms\Banner;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class SliderController extends Controller
{
    public function __construct(
        private readonly PublicUploadService $publicUploadService,
    ) {
    }

    public function manage_slider()
    {
        $all_slide = $this->sliderTableExists()
            ? Banner::query()
                ->where('placement', 'homepage')
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->paginate(6)
            : $this->emptyPaginator(6);

        $all_slide->getCollection()->transform(
            fn (Banner $banner) => $this->mapBannerToSliderShape($banner)
        );

        return view('admin.slider.list_slider')->with(compact('all_slide'));
    }

    public function add_slider()
    {
        return view('admin.slider.add_slider');
    }

    public function edit_slider($slide_id)
    {
        if (! $this->sliderTableExists()) {
            return Redirect::to('manage-slider')->with('error', 'Bảng trình chiếu chưa được cấu hình.');
        }

        $slider = Banner::find($slide_id);
        if (! $slider) {
            return Redirect::to('manage-slider')->with('error', 'Không tìm thấy mục trình chiếu cần sửa.');
        }

        return view('admin.slider.edit_slider', [
            'slider' => $slider,
        ]);
    }

    public function unactive_slide($slide_id)
    {
        if (! $this->sliderTableExists()) {
            return Redirect::to('manage-slider')->with('error', 'Bảng trình chiếu chưa được cấu hình.');
        }

        $slider = Banner::find($slide_id);
        if (! $slider) {
            return Redirect::to('manage-slider')->with('error', 'Không tìm thấy mục trình chiếu cần cập nhật.');
        }

        $slider->is_active = false;
        $slider->save();

        Session::put('message', 'Đã ẩn trình chiếu thành công');

        return Redirect::to('manage-slider');
    }

    public function active_slide($slide_id)
    {
        if (! $this->sliderTableExists()) {
            return Redirect::to('manage-slider')->with('error', 'Bảng trình chiếu chưa được cấu hình.');
        }

        $slider = Banner::find($slide_id);
        if (! $slider) {
            return Redirect::to('manage-slider')->with('error', 'Không tìm thấy mục trình chiếu cần cập nhật.');
        }

        $slider->is_active = true;
        $slider->save();

        Session::put('message', 'Đã bật hiển thị trình chiếu thành công');

        return Redirect::to('manage-slider');
    }

    public function insert_slider(StoreSliderRequest $request)
    {
        if (! $this->sliderTableExists()) {
            return Redirect::to('add-slider')
                ->withInput()
                ->with('error', 'Bảng trình chiếu chưa được cấu hình.');
        }

        $data = $request->validated();

        $image = $request->file('slider_image');
        $new_image = $this->publicUploadService->storeLegacyImage($image, 'uploads/slider');

        $tone = $data['slider_tone'] ?? 'auto';

        Banner::create([
            'title' => $data['slider_name'],
            'image_path' => 'uploads/slider/'.$new_image,
            'link_url' => $data['slider_link_url'] ?? null,
            'placement' => 'homepage',
            'display_order' => $this->displayOrderFromRequest($data),
            'is_active' => ((int) $data['slider_status']) === 1,
            'meta' => [
                'slider_alt' => $data['slider_alt'] ?? null,
                'slider_desc' => $data['slider_desc'] ?? null,
                'slider_tone' => $tone === 'auto' ? null : $tone,
            ],
        ]);

        Session::put('message', 'Thêm trình chiếu thành công');

        return Redirect::to('add-slider');
    }

    public function update_slider(UpdateSliderRequest $request, $slide_id)
    {
        if (! $this->sliderTableExists()) {
            return Redirect::to('manage-slider')
                ->withInput()
                ->with('error', 'Bảng trình chiếu chưa được cấu hình.');
        }

        $slider = Banner::find($slide_id);
        if (! $slider) {
            return Redirect::to('manage-slider')->with('error', 'Không tìm thấy mục trình chiếu cần cập nhật.');
        }

        $data = $request->validated();
        $tone = $data['slider_tone'] ?? 'auto';
        $oldImagePath = $slider->image_path;

        $slider->title = $data['slider_name'];
        $slider->link_url = $data['slider_link_url'] ?? null;
        $slider->display_order = $this->displayOrderFromRequest($data, (int) $slider->display_order);
        $slider->is_active = ((int) $data['slider_status']) === 1;
        $slider->meta = [
            'slider_alt' => $data['slider_alt'] ?? null,
            'slider_desc' => $data['slider_desc'] ?? null,
            'slider_tone' => $tone === 'auto' ? null : $tone,
        ];

        if ($request->hasFile('slider_image')) {
            $newImage = $this->publicUploadService->storeLegacyImage($request->file('slider_image'), 'uploads/slider');
            $slider->image_path = 'uploads/slider/'.$newImage;
        }

        $slider->save();

        if ($request->hasFile('slider_image')) {
            $this->publicUploadService->deletePublicFile($oldImagePath);
        }

        Session::put('message', 'Cập nhật trình chiếu thành công');

        return Redirect::to('manage-slider');
    }

    public function delete_slide($slide_id)
    {
        if (! $this->sliderTableExists()) {
            return redirect()->back()->with('error', 'Bảng trình chiếu chưa được cấu hình.');
        }

        $slider = Banner::find($slide_id);
        if (! $slider) {
            return redirect()->back()->with('error', 'Không tìm thấy mục trình chiếu cần xóa.');
        }

        $this->publicUploadService->deletePublicFile($slider->image_path);
        $slider->delete();

        Session::put('message', 'Xóa trình chiếu thành công');

        return redirect()->back();
    }

    protected function sliderTableExists(): bool
    {
        return Schema::hasTable((new Banner())->getTable());
    }

    protected function mapBannerToSliderShape(Banner $banner): object
    {
        $meta = is_array($banner->meta) ? $banner->meta : [];
        $imagePath = trim((string) $banner->image_path);

        return (object) [
            'slider_id' => $banner->id,
            'slider_name' => $banner->title,
            'slider_alt' => $meta['slider_alt'] ?? $banner->title,
            'slider_image' => $imagePath !== '' ? basename($imagePath) : null,
            'slider_status' => $banner->is_active ? 1 : 0,
            'slider_link_url' => $banner->link_url,
            'slider_display_order' => (int) $banner->display_order,
            'slider_desc' => $meta['legacy_description']
                ?? $meta['slider_desc']
                ?? $meta['description']
                ?? null,
            'slider_tone' => $meta['slider_tone'] ?? null,
        ];
    }

    protected function displayOrderFromRequest(array $data, ?int $fallback = null): int
    {
        if (($data['slider_display_order'] ?? null) !== null && $data['slider_display_order'] !== '') {
            return (int) $data['slider_display_order'];
        }

        return $fallback ?? ((int) (Banner::max('display_order') ?? 0) + 1);
    }

    protected function emptyPaginator(int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, 1, [
            'path' => request()->url(),
            'pageName' => 'page',
        ]);
    }
}
