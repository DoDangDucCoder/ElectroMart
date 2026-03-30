<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Media\StoreSliderRequest;
use App\Models\Slider;
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
            ? Slider::orderBy('slider_id', 'DESC')->paginate(6)
            : $this->emptyPaginator(6);

        return view('admin.slider.list_slider')->with(compact('all_slide'));
    }

    public function add_slider()
    {
        return view('admin.slider.add_slider');
    }

    public function unactive_slide($slide_id)
    {
        if (! $this->sliderTableExists()) {
            Session::put('message', 'Bảng slider chưa được cấu hình.');

            return Redirect::to('manage-slider');
        }

        $slider = Slider::find($slide_id);
        if ($slider) {
            $slider->slider_status = 0;
            $slider->save();
        }

        Session::put('message', 'Không kích hoạt slider thành công');

        return Redirect::to('manage-slider');
    }

    public function active_slide($slide_id)
    {
        if (! $this->sliderTableExists()) {
            Session::put('message', 'Bảng slider chưa được cấu hình.');

            return Redirect::to('manage-slider');
        }

        $slider = Slider::find($slide_id);
        if ($slider) {
            $slider->slider_status = 1;
            $slider->save();
        }

        Session::put('message', 'Kích hoạt slider thành công');

        return Redirect::to('manage-slider');
    }

    public function insert_slider(StoreSliderRequest $request)
    {
        if (! $this->sliderTableExists()) {
            Session::put('message', 'Bảng slider chưa được cấu hình.');

            return Redirect::to('add-slider');
        }

        $data = $request->validated();

        $image = $request->file('slider_image');
        $new_image = $this->publicUploadService->storeLegacyImage($image, 'uploads/slider');

        $slider = new Slider();
        $slider->slider_name = $data['slider_name'];
        $slider->slider_image = $new_image;
        $slider->slider_status = $data['slider_status'];
        $slider->slider_desc = $data['slider_desc'] ?? null;

        if (Schema::hasColumn((new Slider())->getTable(), 'slider_alt')) {
            $slider->slider_alt = $data['slider_alt'] ?? null;
        }

        if (Schema::hasColumn((new Slider())->getTable(), 'slider_tone')) {
            $tone = $data['slider_tone'] ?? 'auto';
            $slider->slider_tone = $tone === 'auto' ? null : $tone;
        }

        $slider->save();

        Session::put('message', 'Thêm slider thành công');

        return Redirect::to('add-slider');
    }

    public function delete_slide($slide_id)
    {
        if (! $this->sliderTableExists()) {
            Session::put('message', 'Bảng slider chưa được cấu hình.');

            return redirect()->back();
        }

        $slider = Slider::find($slide_id);
        if ($slider) {
            $this->publicUploadService->deletePublicFile('uploads/slider/' . $slider->slider_image);
            $slider->delete();
        }

        Session::put('message', 'Xóa slider thành công');

        return redirect()->back();
    }

    protected function sliderTableExists(): bool
    {
        return Schema::hasTable((new Slider())->getTable());
    }

    protected function emptyPaginator(int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, 1, [
            'path' => request()->url(),
            'pageName' => 'page',
        ]);
    }
}
