<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Document\StoreDocumentRequest;
use App\Infrastructure\Persistence\Eloquent\Cms\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DocumentController extends Controller
{
    public function download_document($path, $name)
    {
        $path = $this->guardManagedDocumentPath($path);
        $disk = $this->documentDisk();

        if (! $disk->exists($path)) {
            abort(404);
        }

        $mimeType = $this->safeMimeType($path);
        $rawData = $disk->get($path);
        $downloadName = basename((string) $name) ?: basename($path);

        return response($rawData, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', "attachment; filename=\"{$downloadName}\"");
    }

    public function delete_document($path)
    {
        $path = $this->normalizeManagedDocumentPath($path);
        $disk = $this->documentDisk();

        if (! $this->managedDocumentExists($path)) {
            return redirect()->back()->with('error', 'Không tìm thấy tài liệu cần xóa.');
        }

        $disk->delete($path);
        $this->deleteDocumentRowsByPath($path);

        return redirect()->back()->with('message', 'Xóa tài liệu thành công');
    }

    public function store_document(StoreDocumentRequest $request)
    {
        if (! $this->documentsTableExists()) {
            return Redirect::to('read_data')
                ->withInput()
                ->with('error', 'Bảng tài liệu chưa được cấu hình.');
        }

        $file = $request->file('document');
        $path = $this->storeUploadedDocument($file);

        Document::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => $this->documentDiskName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
            'category' => $request->validated('category') ?: null,
            'meta' => [
                'uploaded_by' => $request->user('web')?->id,
                'stored_name' => basename($path),
            ],
        ]);

        return Redirect::to('read_data')->with('message', 'Tải tài liệu lên thành công');
    }

    public function destroy_document(Document $document)
    {
        if ($document->disk !== $this->documentDiskName()) {
            return redirect()->back()->with('error', 'Tài liệu không thuộc disk hiện tại.');
        }

        $path = $this->normalizeManagedDocumentPath($document->path);
        if ($this->pathIsInsideManagedRoot($path)) {
            $this->documentDisk()->delete($path);
        }

        $document->delete();

        return redirect()->back()->with('message', 'Xóa tài liệu thành công');
    }

    public function read_data()
    {
        $contents = $this->collectDocuments();

        return view('admin.document.read')->with([
            'contents' => $contents,
            'documentDiskLabel' => strtoupper($this->documentDiskName()),
            'documentDiskType' => $this->documentDiskType(),
        ]);
    }

    protected function documentDisk()
    {
        return Storage::disk($this->documentDiskName());
    }

    protected function documentDiskName(): string
    {
        $cloudDisk = config('filesystems.cloud');

        if (is_string($cloudDisk) && config("filesystems.disks.{$cloudDisk}")) {
            return $cloudDisk;
        }

        if (config('filesystems.disks.public')) {
            return 'public';
        }

        return config('filesystems.default', 'local');
    }

    protected function documentDiskType(): string
    {
        return config('filesystems.disks.' . $this->documentDiskName() . '.driver', 'unknown');
    }

    protected function collectDocuments(): Collection
    {
        $this->syncStorageDocumentsToDatabase();

        if ($this->documentsTableExists()) {
            return Document::query()
                ->where('disk', $this->documentDiskName())
                ->where('path', 'like', $this->managedDocumentRoot().'/%')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Document $document) => $this->mapDocumentRecord($document))
                ->values();
        }

        try {
            $disk = $this->documentDisk();
            $root = $this->managedDocumentRoot();

            return collect($disk->allFiles($root))
                ->map(function (string $path) use ($disk) {
                    $mimeType = $this->safeMimeType($path);
                    $size = $this->safeSize($path);
                    $lastModified = $this->safeLastModified($path);

                    return [
                        'name' => basename($path),
                        'path' => $path,
                        'basename' => pathinfo($path, PATHINFO_FILENAME),
                        'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                        'type' => str_starts_with($mimeType, 'image/') ? 'image' : 'file',
                        'mimetype' => $mimeType,
                        'size' => $size,
                        'size_label' => $this->formatBytes($size),
                        'last_modified' => $lastModified,
                        'last_modified_label' => $lastModified ? date('d/m/Y H:i', $lastModified) : 'Chưa xác định',
                        'download_url' => url('download_document', ['path' => $path, 'name' => basename($path)]),
                        'delete_url' => url('delete_document/' . $path),
                        'record_id' => null,
                        'category' => null,
                        'preview_url' => $this->safeUrl($path),
                    ];
                })
                ->sortBy('name')
                ->values();
        } catch (Throwable) {
            return collect();
        }
    }

    protected function safeMimeType(string $path): string
    {
        try {
            return $this->documentDisk()->mimeType($path) ?: 'application/octet-stream';
        } catch (Throwable) {
            return 'application/octet-stream';
        }
    }

    protected function safeSize(string $path): int
    {
        try {
            return (int) ($this->documentDisk()->size($path) ?? 0);
        } catch (Throwable) {
            return 0;
        }
    }

    protected function safeLastModified(string $path): ?int
    {
        try {
            return $this->documentDisk()->lastModified($path);
        } catch (Throwable) {
            return null;
        }
    }

    protected function safeUrl(string $path): ?string
    {
        try {
            return $this->documentDisk()->url($path);
        } catch (Throwable) {
            return null;
        }
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 1, '.', ',') . ' ' . $units[$power];
    }

    protected function guardManagedDocumentPath(string $path): string
    {
        $normalizedPath = $this->normalizeManagedDocumentPath($path);

        if (! $this->managedDocumentExists($normalizedPath)) {
            abort(404);
        }

        return $normalizedPath;
    }

    protected function normalizeManagedDocumentPath(string $path): string
    {
        $normalizedPath = str_replace('\\', '/', rawurldecode($path));
        $normalizedPath = ltrim(trim($normalizedPath), '/');

        if ($normalizedPath === '' || str_contains($normalizedPath, "\0")) {
            abort(404);
        }

        foreach (explode('/', $normalizedPath) as $segment) {
            if ($segment === '..') {
                abort(404);
            }
        }

        return $normalizedPath;
    }

    protected function managedDocumentExists(string $path): bool
    {
        return $this->pathIsInsideManagedRoot($path) && $this->documentDisk()->exists($path);
    }

    protected function managedDocumentRoot(): string
    {
        return 'documents';
    }

    protected function documentsTableExists(): bool
    {
        return Schema::hasTable((new Document())->getTable());
    }

    protected function storeUploadedDocument($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'document';
        $fileName = $baseName.'-'.Str::random(8).'.'.$extension;
        $path = $this->managedDocumentRoot().'/'.$fileName;

        $this->documentDisk()->putFileAs($this->managedDocumentRoot(), $file, $fileName);

        return $path;
    }

    protected function syncStorageDocumentsToDatabase(): void
    {
        if (! $this->documentsTableExists()) {
            return;
        }

        try {
            $disk = $this->documentDisk();

            foreach ($disk->allFiles($this->managedDocumentRoot()) as $path) {
                Document::query()->firstOrCreate(
                    [
                        'disk' => $this->documentDiskName(),
                        'path' => $path,
                    ],
                    [
                        'name' => basename($path),
                        'mime_type' => $this->safeMimeType($path),
                        'size' => $this->safeSize($path),
                        'category' => null,
                        'meta' => [
                            'source' => 'storage_sync',
                        ],
                    ]
                );
            }
        } catch (Throwable) {
        }
    }

    protected function mapDocumentRecord(Document $document): array
    {
        $path = $document->path;
        $mimeType = $document->mime_type ?: $this->safeMimeType($path);
        $lastModified = $this->safeLastModified($path);

        return [
            'record_id' => $document->id,
            'name' => $document->name,
            'path' => $path,
            'basename' => pathinfo($path, PATHINFO_FILENAME),
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            'type' => str_starts_with($mimeType, 'image/') ? 'image' : 'file',
            'mimetype' => $mimeType,
            'size' => (int) $document->size,
            'size_label' => $this->formatBytes((int) $document->size),
            'category' => $document->category,
            'last_modified' => $lastModified,
            'last_modified_label' => $document->updated_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i')
                ?? ($lastModified ? date('d/m/Y H:i', $lastModified) : 'Chưa xác định'),
            'download_url' => url('download_document', ['path' => $path, 'name' => $document->name]),
            'delete_url' => route('admin.documents.destroy', $document),
            'preview_url' => $this->safeUrl($path),
        ];
    }

    protected function deleteDocumentRowsByPath(string $path): void
    {
        if (! $this->documentsTableExists()) {
            return;
        }

        Document::query()
            ->where('disk', $this->documentDiskName())
            ->where('path', $path)
            ->delete();
    }

    protected function pathIsInsideManagedRoot(string $path): bool
    {
        return str_starts_with($path, $this->managedDocumentRoot().'/');
    }
}
