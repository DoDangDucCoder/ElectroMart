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
use App\Support\AssetUrl;
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

		return $this->galleryValidationError('gal_id', 'Không tìm thấy ảnh trong thư viện cần cập nhật.');
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

		return redirect()
			->back()
			->withInput()
			->with('error', 'Không tìm thấy sản phẩm để thêm thư viện ảnh.');
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

		return $this->galleryValidationError('gal_id', 'Không tìm thấy ảnh trong thư viện cần xóa.');
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

		return $this->galleryValidationError('gal_id', 'Không tìm thấy ảnh trong thư viện cần cập nhật.');
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

		return response('<div class="g-admin-empty-state">Không tìm thấy sản phẩm để tải thư viện ảnh.</div>');
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
			'gallery_image_url' => AssetUrl::productVariant($media->file_path, 'thumb'),
		];
	}
}
