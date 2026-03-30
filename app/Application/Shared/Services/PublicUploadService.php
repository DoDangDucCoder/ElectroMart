<?php

namespace App\Application\Shared\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublicUploadService
{
    public function storeLegacyImage(UploadedFile $file, string $directory): string
    {
        $fullDirectory = $this->ensureDirectory($directory);
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $baseName = $baseName !== '' ? $baseName : 'file';
        $filename = $baseName . rand(0, 99) . '.' . $file->getClientOriginalExtension();

        $file->move($fullDirectory, $filename);

        return $filename;
    }

    public function storeSluggedImage(UploadedFile $file, string $directory): string
    {
        $relativeDirectory = trim($directory, '/\\');
        $fullDirectory = $this->ensureDirectory($relativeDirectory);
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $baseName = $baseName !== '' ? $baseName : 'file';
        $filename = $baseName . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();

        $file->move($fullDirectory, $filename);

        return $relativeDirectory . '/' . $filename;
    }

    public function deletePublicFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $normalizedPath = ltrim($path, '/\\');
        if ($normalizedPath === '') {
            return;
        }

        $fullPath = public_path($normalizedPath);

        if (File::exists($fullPath) && ! File::isDirectory($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function ensureDirectory(string $directory): string
    {
        $relativeDirectory = trim($directory, '/\\');
        $fullDirectory = public_path($relativeDirectory);

        if (! File::exists($fullDirectory)) {
            File::makeDirectory($fullDirectory, 0755, true);
        }

        return $fullDirectory;
    }
}
