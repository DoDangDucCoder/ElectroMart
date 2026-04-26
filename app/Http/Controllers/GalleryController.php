<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Products\DeleteGalleryRequest;
use App\Http\Requests\Admin\Legacy\Products\SelectGalleryRequest;
use App\Http\Requests\Admin\Legacy\Products\StoreGalleryRequest;
use App\Http\Requests\Admin\Legacy\Products\UpdateGalleryImageRequest;
use App\Http\Requests\Admin\Legacy\Products\UpdateGalleryNameRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductMedia;
use App\Models\Gallery;
use App\Support\AssetUrl;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;

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

		if ($media = $this->findModernMedia((int) $data['gal_id'])) {
			$meta = is_array($media->meta) ? $media->meta : [];
			$meta['gallery_name'] = $data['gal_text'];
			$media->meta = $meta;
			$media->save();

			return response()->noContent();
		}

		if (! Schema::hasTable((new Gallery())->getTable())) {
			return response()->json([
				'message' => 'Bảng thư viện ảnh chưa sẵn sàng trên môi trường này.',
			], 422);
		}

		$gallery = Gallery::find($data['gal_id']);
		if (! $gallery) {
			return $this->galleryValidationError('gal_id', 'Không tìm thấy ảnh trong thư viện cần cập nhật.');
		}

		$gallery->gallery_name = $data['gal_text'];
		$gallery->save();

		return response()->noContent();
	}
	public function insert_gallery(StoreGalleryRequest $request, $pro_id)
	{
		$data = $request->validated();
		$images = (array) $request->file('file', []);
		$product = Product::query()->find((int) $data['pro_id']);

		if ($product) {
			$nextOrder = (int) ProductMedia::query()
				->where('product_id', $product->id)
				->max('display_order');

			foreach ($images as $image) {
				$imagePath = $this->publicUploadService->storeSluggedImage($image, 'uploads/gallery');
				$nextOrder++;

				ProductMedia::create([
					'product_id' => $product->id,
					'disk' => 'public',
					'collection' => 'gallery',
					'file_path' => $imagePath,
					'display_order' => $nextOrder,
					'meta' => [
						'gallery_name' => basename($imagePath),
						'source' => 'admin_gallery',
					],
				]);
			}

			return redirect()->back()->with('message', 'Thêm thư viện ảnh thành công');
		}

		if (! Schema::hasTable((new Gallery())->getTable())) {
			return redirect()
				->back()
				->withInput()
				->with('error', 'Bảng thư viện ảnh chưa sẵn sàng trên môi trường này.');
		}

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

		if ($media = $this->findModernMedia((int) $data['gal_id'])) {
			$this->publicUploadService->deletePublicFile(
				AssetUrl::isAbsoluteUrl($media->file_path) ? null : $media->file_path
			);
			$media->delete();

			return response()->noContent();
		}

		if (! Schema::hasTable((new Gallery())->getTable())) {
			return response()->json([
				'message' => 'Bảng thư viện ảnh chưa sẵn sàng trên môi trường này.',
			], 422);
		}

		$gallery = Gallery::find($data['gal_id']);
		if (! $gallery) {
			return $this->galleryValidationError('gal_id', 'Không tìm thấy ảnh trong thư viện cần xóa.');
		}

		$this->publicUploadService->deletePublicFile('uploads/gallery/' . $gallery->gallery_image);
		$gallery->delete();

		return response()->noContent();
	}
	public function update_gallery(UpdateGalleryImageRequest $request)
	{
		$data = $request->validated();

		if ($media = $this->findModernMedia((int) $data['gal_id'])) {
			$this->publicUploadService->deletePublicFile(
				AssetUrl::isAbsoluteUrl($media->file_path) ? null : $media->file_path
			);
			$imagePath = $this->publicUploadService->storeSluggedImage($request->file('file'), 'uploads/gallery');
			$meta = is_array($media->meta) ? $media->meta : [];
			$meta['gallery_name'] = basename($imagePath);
			$media->file_path = $imagePath;
			$media->meta = $meta;
			$media->save();

			return response()->noContent();
		}

		if (! Schema::hasTable((new Gallery())->getTable())) {
			return response()->json([
				'message' => 'Bảng thư viện ảnh chưa sẵn sàng trên môi trường này.',
			], 422);
		}

		$gallery = Gallery::find($data['gal_id']);
		if (! $gallery) {
			return $this->galleryValidationError('gal_id', 'Không tìm thấy ảnh trong thư viện cần cập nhật.');
		}

		$this->publicUploadService->deletePublicFile('uploads/gallery/' . $gallery->gallery_image);
		$gallery->gallery_image = $this->publicUploadService->storeLegacyImage($request->file('file'), 'uploads/gallery');
		$gallery->save();

		return response()->noContent();
	}
	public function select_gallery(SelectGalleryRequest $request)
	{
		$productId = (int) $request->validated('pro_id');
		$product = Product::query()->find($productId);

		if ($product) {
			$gallery = ProductMedia::query()
				->where('product_id', $product->id)
				->orderBy('display_order')
				->orderBy('id')
				->get()
				->map(fn (ProductMedia $media): object => $this->modernMediaRecord($media));

			return response()->view('admin.gallery.partials.select_gallery_table', compact('gallery'));
		}

		if (! Schema::hasTable((new Gallery())->getTable())) {
			return response('<div class="g-admin-empty-state">Chưa có bảng thư viện ảnh trên môi trường này.</div>');
		}

		$gallery = Gallery::where('product_id', $productId)
			->get()
			->map(fn (Gallery $gallery): object => $this->legacyGalleryRecord($gallery));

		return response()->view('admin.gallery.partials.select_gallery_table', compact('gallery'));
	}

	private function findModernMedia(int $id): ?ProductMedia
	{
		return Schema::hasTable((new ProductMedia())->getTable())
			? ProductMedia::query()->find($id)
			: null;
	}

	private function galleryValidationError(string $field, string $message)
	{
		return response()->json([
			'message' => $message,
			'errors' => [
				$field => [$message],
			],
		], 422);
	}

	private function modernMediaRecord(ProductMedia $media): object
	{
		$name = data_get($media->meta, 'gallery_name') ?: basename((string) $media->file_path);

		return (object) [
			'gallery_id' => $media->id,
			'gallery_name' => $name,
			'gallery_image_url' => AssetUrl::product($media->file_path),
		];
	}

	private function legacyGalleryRecord(Gallery $gallery): object
	{
		return (object) [
			'gallery_id' => $gallery->gallery_id,
			'gallery_name' => $gallery->gallery_name,
			'gallery_image_url' => AssetUrl::resolve(
				$gallery->gallery_image ? 'uploads/gallery/' . $gallery->gallery_image : null,
				AssetUrl::PRODUCT_FALLBACK
			),
		];
	}
}
