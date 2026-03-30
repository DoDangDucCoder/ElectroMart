<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Products\DeleteGalleryRequest;
use App\Http\Requests\Admin\Legacy\Products\SelectGalleryRequest;
use App\Http\Requests\Admin\Legacy\Products\StoreGalleryRequest;
use App\Http\Requests\Admin\Legacy\Products\UpdateGalleryImageRequest;
use App\Http\Requests\Admin\Legacy\Products\UpdateGalleryNameRequest;
use App\Models\Gallery;
use Illuminate\Support\Facades\Session;

class GalleryController extends Controller
{
	public function __construct(
		private readonly PublicUploadService $publicUploadService,
	) {
	}
	public function add_gallery($product_id)
	{
		$pro_id = $product_id;
		return view('admin.gallery.add_gallery')->with(compact('pro_id'));
	}
	public function update_gallery_name(UpdateGalleryNameRequest $request)
	{
		$data = $request->validated();
		$gallery = Gallery::find($data['gal_id']);
		if ($gallery) {
			$gallery->gallery_name = $data['gal_text'];
			$gallery->save();
		}

		return response()->noContent();
	}
	public function insert_gallery(StoreGalleryRequest $request, $pro_id)
	{
		$data = $request->validated();
		$images = (array) $request->file('file', []);

		foreach ($images as $image) {
				$new_image = $this->publicUploadService->storeLegacyImage($image, 'uploads/gallery');
				$gallery = new Gallery();
				$gallery->gallery_name = $new_image;
				$gallery->gallery_image = $new_image;
				$gallery->product_id = $data['pro_id'];
				$gallery->save();
		}
		Session::put('message', 'Thêm thư viện ảnh thành công');
		return redirect()->back();
	}
	public function delete_gallery(DeleteGalleryRequest $request)
	{
		$data = $request->validated();
		$gallery = Gallery::find($data['gal_id']);
		if ($gallery) {
			$this->publicUploadService->deletePublicFile('uploads/gallery/' . $gallery->gallery_image);
			$gallery->delete();
		}

		return response()->noContent();
	}
	public function update_gallery(UpdateGalleryImageRequest $request)
	{
		$data = $request->validated();
		$gallery = Gallery::find($data['gal_id']);
		if (! $gallery) {
			return response()->noContent();
		}

		$this->publicUploadService->deletePublicFile('uploads/gallery/' . $gallery->gallery_image);
		$gallery->gallery_image = $this->publicUploadService->storeLegacyImage($request->file('file'), 'uploads/gallery');
		$gallery->save();

		return response()->noContent();
	}
	public function select_gallery(SelectGalleryRequest $request)
	{
		$gallery = Gallery::where('product_id', $request->validated('pro_id'))->get();

		return response()->view('admin.gallery.partials.select_gallery_table', compact('gallery'));
	}
}
