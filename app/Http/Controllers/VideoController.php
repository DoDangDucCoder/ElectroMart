<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Cms\Services\BlogCategoryService;
use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Media\DeleteVideoRequest;
use App\Http\Requests\Admin\Legacy\Media\StoreVideoRequest;
use App\Http\Requests\Admin\Legacy\Media\UpdateVideoImageRequest;
use App\Http\Requests\Admin\Legacy\Media\UpdateVideoRequest;
use App\Infrastructure\Persistence\Eloquent\Cms\Video as ModernVideo;
use App\Models\Video as LegacyVideo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
        private readonly BlogCategoryService $blogCategoryService,
        private readonly PublicUploadService $publicUploadService,
    ) {
    }

    public function video()
    {
        return view('admin.video.list_video');
    }

    public function insert_video(StoreVideoRequest $request)
    {
        $data = $request->validated();

        $imagePath = $request->file('file')
            ? $this->publicUploadService->storeSluggedImage($request->file('file'), 'uploads/videos')
            : null;

        if ($this->usesModernVideoTable()) {
            ModernVideo::create([
                'title' => $data['video_title'] ?? '',
                'description' => $data['video_desc'] ?? null,
                'slug' => $data['video_slug'] ?? Str::slug($data['video_title'] ?? Str::random(8)),
                'video_url' => $data['video_link'] ?? '',
                'thumbnail_path' => $imagePath,
                'is_active' => true,
                'display_order' => (int) (ModernVideo::max('display_order') ?? 0) + 1,
            ]);

            return response()->noContent();
        }

        if (! $this->usesLegacyVideoTable()) {
            return response()->json(['message' => 'Chưa có nơi lưu video trên môi trường này.'], 422);
        }

        $video = new LegacyVideo();
        $video->video_title = $data['video_title'] ?? '';
        $video->video_desc = $data['video_desc'] ?? null;
        $video->video_link = $this->extractYoutubeId($data['video_link'] ?? '');
        $video->video_slug = $data['video_slug'] ?? Str::slug($data['video_title'] ?? Str::random(8));
        $video->video_image = $imagePath ? basename($imagePath) : null;

        if ($this->legacyVideoStatusColumnExists()) {
            $video->setAttribute('video_status', 1);
        }

        $video->save();

        return response()->noContent();
    }

    public function delete_video(DeleteVideoRequest $request)
    {
        $validated = $request->validated();
        $videoId = (int) $validated['video_id'];

        if ($this->usesModernVideoTable()) {
            $video = ModernVideo::find($videoId);

            if (! $video) {
                return response()->json(['message' => 'Không tìm thấy video cần xóa.'], 404);
            }

            $this->publicUploadService->deletePublicFile($video->thumbnail_path);
            $video->delete();

            return response()->noContent();
        }

        if (! $this->usesLegacyVideoTable()) {
            return response()->json(['message' => 'Chưa có nơi lưu video trên môi trường này.'], 422);
        }

        $video = LegacyVideo::find($videoId);
        if (! $video) {
            return response()->json(['message' => 'Không tìm thấy video cần xóa.'], 404);
        }

        $this->publicUploadService->deletePublicFile($video->video_image ? 'uploads/videos/' . $video->video_image : null);
        $video->delete();

        return response()->noContent();
    }

    public function update_video(UpdateVideoRequest $request)
    {
        $validated = $request->validated();

        $videoId = (int) $validated['video_id'];
        $videoEdit = trim((string) $validated['video_edit']);
        $videoCheck = (string) $validated['video_check'];

        if ($this->usesModernVideoTable()) {
            $video = ModernVideo::find($videoId);

            if (! $video) {
                return response()->json(['message' => 'Không tìm thấy video cần cập nhật.'], 404);
            }

            if ($videoCheck === 'video_title') {
                $video->title = $videoEdit;
            } elseif ($videoCheck === 'video_desc') {
                $video->description = $videoEdit;
            } elseif ($videoCheck === 'video_link') {
                $video->video_url = $videoEdit;
            } elseif ($videoCheck === 'video_slug') {
                $video->slug = Str::slug($videoEdit) ?: $video->slug;
            }

            $video->save();

            return response()->noContent();
        }

        if (! $this->usesLegacyVideoTable()) {
            return response()->json(['message' => 'Chưa có nơi lưu video trên môi trường này.'], 422);
        }

        $video = LegacyVideo::find($videoId);

        if (! $video) {
            return response()->json(['message' => 'Không tìm thấy video cần cập nhật.'], 404);
        }

        if ($videoCheck === 'video_title') {
            $video->video_title = $videoEdit;
        } elseif ($videoCheck === 'video_desc') {
            $video->video_desc = $videoEdit;
        } elseif ($videoCheck === 'video_link') {
            $video->video_link = $this->extractYoutubeId($videoEdit);
        } else {
            $video->video_slug = Str::slug($videoEdit) ?: $video->video_slug;
        }

        $video->save();

        return response()->noContent();
    }

    public function update_video_image(UpdateVideoImageRequest $request)
    {
        $validated = $request->validated();

        $videoId = (int) $validated['video_id'];
        $imagePath = $this->publicUploadService->storeSluggedImage($request->file('file'), 'uploads/videos');

        if (! $imagePath) {
            return response()->json(['message' => 'Không thể lưu hình ảnh video mới.'], 422);
        }

        if ($this->usesModernVideoTable()) {
            $video = ModernVideo::find($videoId);

            if (! $video) {
                $this->publicUploadService->deletePublicFile($imagePath);

                return response()->json(['message' => 'Không tìm thấy video cần cập nhật hình ảnh.'], 404);
            }

            $this->publicUploadService->deletePublicFile($video->thumbnail_path);
            $video->thumbnail_path = $imagePath;
            $video->save();

            return response()->noContent();
        }

        if (! $this->usesLegacyVideoTable()) {
            $this->publicUploadService->deletePublicFile($imagePath);

            return response()->json(['message' => 'Chưa có nơi lưu video trên môi trường này.'], 422);
        }

        $video = LegacyVideo::find($videoId);

        if (! $video) {
            $this->publicUploadService->deletePublicFile($imagePath);

            return response()->json(['message' => 'Không tìm thấy video cần cập nhật hình ảnh.'], 404);
        }

        $this->publicUploadService->deletePublicFile($video->video_image ? 'uploads/videos/' . $video->video_image : null);
        $video->video_image = basename($imagePath);
        $video->save();

        return response()->noContent();
    }

    public function select_video(Request $request)
    {
        $videos = $this->fetchAdminVideos();

        return response()->view('admin.video.partials.select_video_table', [
            'videos' => $videos,
            'extractYoutubeId' => fn (string $value): string => $this->extractYoutubeId($value),
            'videoThumbnailUrl' => fn ($video): string => $this->videoThumbnailUrl($video),
        ]);
    }

    public function video_shop(Request $request)
    {
        return view('pages.video.video', [
            'category' => $this->categoryService->listAll(['is_active' => true]),
            'brand' => $this->brandService->listAll(['is_active' => true]),
            'all_video' => $this->paginateStorefrontVideos(6),
            'meta_desc' => 'Video công nghệ, review và hướng dẫn mua sắm',
            'meta_keywords' => 'video cong nghe, review, huong dan mua sam',
            'meta_title' => 'Video cửa hàng',
            'url_canonical' => $request->url(),
            'slider' => $this->legacyActiveSliders(),
            'category_post' => $this->blogCategoryService->listAll(['is_active' => true]),
        ]);
    }

    public function watch_video(Request $request)
    {
        $video = $this->findStorefrontVideoById((int) $request->input('video_id'));

        if (! $video) {
            return response()->json([
                'video_title' => 'Video không tồn tại',
                'video_desc' => 'Không tìm thấy video phù hợp.',
                'video_link' => '',
            ], 404);
        }

        $title = $video->video_title ?? $video->title ?? '';
        $description = $video->video_desc ?? $video->description ?? '';
        $videoUrl = $video->video_link ?? $video->video_url ?? '';
        $youtubeId = $this->extractYoutubeId($videoUrl);

        return response()->json([
            'video_title' => $title,
            'video_desc' => $description,
            'video_link' => $youtubeId
                ? '<video id="my_yt_video" class="vlite-js" data-youtube-id="' . e($youtubeId) . '"></video>'
                : '',
        ]);
    }

    protected function paginateStorefrontVideos(int $perPage): LengthAwarePaginator
    {
        if ($this->usesModernVideoTable()) {
            return ModernVideo::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->paginate($perPage);
        }

        if ($this->usesLegacyVideoTable()) {
            return $this->legacyVideoQuery(onlyActive: true)
                ->orderBy('video_id', 'DESC')
                ->paginate($perPage);
        }

        return new LengthAwarePaginator([], 0, $perPage, 1, [
            'path' => request()->url(),
            'pageName' => 'page',
        ]);
    }

    protected function fetchAdminVideos()
    {
        if ($this->usesModernVideoTable()) {
            return ModernVideo::query()
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get();
        }

        if ($this->usesLegacyVideoTable()) {
            return $this->legacyVideoQuery()
                ->orderBy('video_id', 'DESC')
                ->get();
        }

        return collect();
    }

    protected function findStorefrontVideoById(int $id): LegacyVideo|ModernVideo|null
    {
        if ($this->usesModernVideoTable()) {
            return ModernVideo::query()
                ->where('is_active', true)
                ->whereKey($id)
                ->first();
        }

        if ($this->usesLegacyVideoTable()) {
            return $this->legacyVideoQuery(onlyActive: true)
                ->whereKey($id)
                ->first();
        }

        return null;
    }

    protected function usesModernVideoTable(): bool
    {
        return Schema::hasTable('videos');
    }

    protected function usesLegacyVideoTable(): bool
    {
        return Schema::hasTable((new LegacyVideo())->getTable());
    }

    protected function legacyVideoStatusColumnExists(): bool
    {
        $table = (new LegacyVideo())->getTable();

        return Schema::hasTable($table) && Schema::hasColumn($table, 'video_status');
    }

    protected function legacyVideoQuery(bool $onlyActive = false)
    {
        $query = LegacyVideo::query();

        if ($onlyActive && $this->legacyVideoStatusColumnExists()) {
            $query->where('video_status', 1);
        }

        return $query;
    }

    protected function legacyActiveSliders()
    {
        if (! Schema::hasTable('tbl_slider')) {
            return collect();
        }

        return \App\Models\Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
    }

    protected function extractYoutubeId(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (! str_contains($value, 'http')) {
            return trim($value, '/');
        }

        $host = (string) parse_url($value, PHP_URL_HOST);
        $path = trim((string) parse_url($value, PHP_URL_PATH), '/');
        $query = [];
        parse_str((string) parse_url($value, PHP_URL_QUERY), $query);

        if (isset($query['v']) && $query['v'] !== '') {
            return (string) $query['v'];
        }

        if (str_contains($host, 'youtu.be')) {
            return $path;
        }

        if ($path !== '' && str_contains($host, 'youtube.com')) {
            $segments = explode('/', $path);

            return (string) end($segments);
        }

        return $value;
    }

    protected function videoThumbnailUrl($video): string
    {
        $thumbnailPath = $video->thumbnail_path ?? null;
        if ($thumbnailPath && File::exists(public_path($thumbnailPath))) {
            return asset($thumbnailPath);
        }

        $legacyImage = $video->video_image ?? null;
        if ($legacyImage && File::exists(public_path('uploads/videos/' . $legacyImage))) {
            return asset('uploads/videos/' . $legacyImage);
        }

        return asset('uploads/product/no-image.svg');
    }
}
