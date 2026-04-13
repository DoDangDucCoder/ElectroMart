<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
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
        return $this->collectDocuments()
            ->contains(fn (array $document) => ($document['path'] ?? null) === $path);
    }

    protected function managedDocumentRoot(): string
    {
        return 'documents';
    }
}
