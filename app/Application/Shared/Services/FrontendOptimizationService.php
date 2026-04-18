<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class FrontendOptimizationService
{
    private array $optimizationConfig = [];
    private array $assetManifest = [];
    private string $publicPath;
    private string $resourcesPath;

    public function __construct()
    {
        $this->publicPath = public_path();
        $this->resourcesPath = resource_path();
        $this->initializeOptimizationConfig();
    }

    private function initializeOptimizationConfig(): void
    {
        $this->optimizationConfig = [
            'css' => [
                'minify' => true,
                'combine' => true,
                'purge' => true,
                'critical_path' => true,
            ],
            'js' => [
                'minify' => true,
                'combine' => true,
                'tree_shaking' => true,
                'code_splitting' => true,
            ],
            'images' => [
                'optimize' => true,
                'webp' => true,
                'lazy_loading' => true,
                'responsive' => true,
            ],
            'fonts' => [
                'preload' => true,
                'subset' => true,
                'woff2' => true,
            ],
            'caching' => [
                'browser_cache' => true,
                'cdn' => false,
                'versioning' => true,
            ],
        ];
    }

    public function optimizeAssets(): array
    {
        Log::info('Starting frontend asset optimization...');

        $this->cleanupGeneratedArtifacts();

        $results = [
            'css' => $this->optimizeCSS(),
            'js' => $this->optimizeJS(),
            'images' => $this->optimizeImages(),
            'fonts' => $this->optimizeFonts(),
            'manifest' => $this->generateAssetManifest(),
        ];

        Log::info('Frontend asset optimization completed');
        return $results;
    }

    public function cleanupGeneratedArtifacts(): array
    {
        return [
            'css' => $this->cleanupDirectoryArtifacts(
                $this->publicPath . '/frontend/css',
                fn (string $path): bool => $this->shouldDeleteGeneratedCssArtifact($path)
            ),
            'js' => $this->cleanupDirectoryArtifacts(
                $this->publicPath . '/frontend/js',
                fn (string $path): bool => $this->shouldDeleteGeneratedJsArtifact($path)
            ),
        ];
    }

    private function optimizeCSS(): array
    {
        $results = [];
        $cssPath = $this->publicPath . '/frontend/css';
        
        if (!is_dir($cssPath)) {
            return ['error' => 'CSS directory not found'];
        }

        $cssFiles = collect(File::glob($cssPath . '/*.css'))
            ->reject(fn (string $file) => ! $this->isProcessableAsset($file, 'css'))
            ->values()
            ->all();
        
        foreach ($cssFiles as $file) {
            $filename = basename($file);
            
            try {
                // Minify CSS
                if ($this->optimizationConfig['css']['minify']) {
                    $minified = $this->minifyCSS(File::get($file));
                    $minifiedPath = str_replace('.css', '.min.css', $file);
                    File::put($minifiedPath, $minified);
                    $results[$filename]['minified'] = 'Success';
                }

                // Purge unused CSS
                if ($this->optimizationConfig['css']['purge']) {
                    $purged = $this->purgeCSS(File::get($file));
                    $purgedPath = str_replace('.css', '.purged.css', $file);
                    File::put($purgedPath, $purged);
                    $results[$filename]['purged'] = 'Success';
                }

                // Generate critical CSS
                if ($this->optimizationConfig['css']['critical_path']) {
                    $critical = $this->generateCriticalCSS(File::get($file));
                    $criticalPath = str_replace('.css', '.critical.css', $file);
                    File::put($criticalPath, $critical);
                    $results[$filename]['critical'] = 'Success';
                }

            } catch (\Exception $e) {
                $results[$filename]['error'] = $e->getMessage();
                Log::error('CSS optimization failed', [
                    'file' => $filename,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    private function optimizeJS(): array
    {
        $results = [];
        $jsPath = $this->publicPath . '/frontend/js';
        
        if (!is_dir($jsPath)) {
            return ['error' => 'JS directory not found'];
        }

        $jsFiles = collect(File::glob($jsPath . '/*.js'))
            ->reject(fn (string $file) => ! $this->isProcessableAsset($file, 'js'))
            ->values()
            ->all();
        
        foreach ($jsFiles as $file) {
            $filename = basename($file);
            
            try {
                // Minify JS
                if ($this->optimizationConfig['js']['minify']) {
                    $minified = $this->minifyJS(File::get($file));
                    $minifiedPath = str_replace('.js', '.min.js', $file);
                    File::put($minifiedPath, $minified);
                    $results[$filename]['minified'] = 'Success';
                }

                // Tree shaking (simplified)
                if ($this->optimizationConfig['js']['tree_shaking']) {
                    $shaken = $this->treeShakeJS(File::get($file));
                    $shakenPath = str_replace('.js', '.shaken.js', $file);
                    File::put($shakenPath, $shaken);
                    $results[$filename]['tree_shaken'] = 'Success';
                }

            } catch (\Exception $e) {
                $results[$filename]['error'] = $e->getMessage();
                Log::error('JS optimization failed', [
                    'file' => $filename,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    private function optimizeImages(): array
    {
        $results = [];
        $imagePath = $this->publicPath . '/uploads';
        
        if (!is_dir($imagePath)) {
            return ['error' => 'Images directory not found'];
        }

        $imageFiles = collect($this->findFilesByExtensions($imagePath, ['jpg', 'jpeg', 'png', 'gif']))
            ->reject(fn (string $file) => ! $this->isProcessableAsset($file, 'images'))
            ->values()
            ->all();
        
        foreach ($imageFiles as $file) {
            $filename = basename($file);
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            try {
                // Optimize image
                if ($this->optimizationConfig['images']['optimize']) {
                    if ($this->canOptimizeImages()) {
                        $optimized = $this->optimizeImage($file);
                        $results[$filename]['optimized'] = $optimized ? 'Success' : 'Failed';
                    } else {
                        $results[$filename]['optimized'] = 'Skipped';
                    }
                }

                // Convert to WebP
                if ($this->optimizationConfig['images']['webp'] && in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    if ($this->canConvertImagesToWebp()) {
                        $webpPath = str_replace('.' . $extension, '.webp', $file);
                        $webpCreated = $this->convertToWebP($file, $webpPath);
                        $results[$filename]['webp'] = $webpCreated ? 'Success' : 'Failed';
                    } else {
                        $results[$filename]['webp'] = 'Skipped';
                    }
                }

                // Generate responsive images
                if ($this->optimizationConfig['images']['responsive']) {
                    if ($this->canGenerateResponsiveImages()) {
                        $responsive = $this->generateResponsiveImages($file);
                        $results[$filename]['responsive'] = $responsive ? 'Success' : 'Failed';
                    } else {
                        $results[$filename]['responsive'] = 'Skipped';
                    }
                }

            } catch (\Exception $e) {
                $results[$filename]['error'] = $e->getMessage();
                Log::error('Image optimization failed', [
                    'file' => $filename,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    private function optimizeFonts(): array
    {
        $results = [];
        $fontPath = $this->publicPath . '/frontend/fonts';
        
        if (!is_dir($fontPath)) {
            return ['error' => 'Fonts directory not found'];
        }

        $fontFiles = collect($this->findFilesByExtensions($fontPath, ['woff', 'ttf', 'otf']))
            ->reject(fn (string $file) => ! $this->isProcessableAsset($file, 'fonts'))
            ->values()
            ->all();
        
        foreach ($fontFiles as $file) {
            $filename = basename($file);
            
            try {
                // Generate font preload
                if ($this->optimizationConfig['fonts']['preload']) {
                    if ($this->canGenerateFontPreload()) {
                        $preload = $this->generateFontPreload($file);
                        $results[$filename]['preload'] = $preload ? 'Success' : 'Failed';
                    } else {
                        $results[$filename]['preload'] = 'Skipped';
                    }
                }

                // Convert to WOFF2
                if ($this->optimizationConfig['fonts']['woff2']) {
                    if ($this->canConvertFontsToWoff2()) {
                        $woff2Path = str_replace(['.woff', '.ttf', '.otf'], '.woff2', $file);
                        $woff2Created = $this->convertToWOFF2($file, $woff2Path);
                        $results[$filename]['woff2'] = $woff2Created ? 'Success' : 'Failed';
                    } else {
                        $results[$filename]['woff2'] = 'Skipped';
                    }
                }

            } catch (\Exception $e) {
                $results[$filename]['error'] = $e->getMessage();
                Log::error('Font optimization failed', [
                    'file' => $filename,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    private function minifyCSS(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        $css = str_replace(['; ', ' {', '{ ', ' }', '} ', ': ', ' ,', ', '], [';', '{', '{', '}', '}', ':', ',', ','], $css);
        
        return trim($css);
    }

    private function minifyJS(string $js): string
    {
        // Remove single-line comments
        $js = preg_replace('/\/\/.*$/m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('/\/\*.*?\*\//s', '', $js);
        
        // Remove unnecessary whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        $js = str_replace([' = ', ' + ', ' - ', ' * ', ' / ', ' == ', ' != ', ' && ', ' || '], 
                         ['=', '+', '-', '*', '/', '==', '!=', '&&', '||'], $js);
        
        return trim($js);
    }

    private function purgeCSS(string $css): string
    {
        // This is a simplified CSS purging
        // In production, use tools like PurgeCSS
        
        // Remove unused selectors (simplified approach)
        $usedSelectors = $this->getUsedCSSSelectors();
        
        $lines = explode("\n", $css);
        $purgedLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '@') === 0) {
                $purgedLines[] = $line;
                continue;
            }
            
            // Check if selector is used
            if (preg_match('/^[^{]+{/', $line, $matches)) {
                $selector = trim($matches[0], ' {');
                if ($this->isSelectorUsed($selector, $usedSelectors)) {
                    $purgedLines[] = $line;
                }
            }
        }
        
        return implode("\n", $purgedLines);
    }

    private function isSelectorUsed(string $selector, array $usedSelectors): bool
    {
        $parts = preg_split('/[\s>+~,:]+/', trim($selector)) ?: [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part === '' || str_starts_with($part, '@')) {
                continue;
            }

            if (str_starts_with($part, '.')) {
                $className = ltrim($part, '.#');
                if (in_array($className, $usedSelectors, true)) {
                    return true;
                }

                continue;
            }

            if (preg_match('/^[a-z][a-z0-9-]*$/i', $part) === 1) {
                return true;
            }
        }

        return false;
    }

    private function generateCriticalCSS(string $css): string
    {
        // Extract critical CSS for above-the-fold content
        $criticalSelectors = [
            'body', 'html', 'header', 'nav', 'main', 'h1', 'h2', 'h3',
            '.navbar', '.header', '.hero', '.banner', '.logo'
        ];
        
        $lines = explode("\n", $css);
        $criticalLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            foreach ($criticalSelectors as $selector) {
                if (strpos($line, $selector) !== false) {
                    $criticalLines[] = $line;
                    break;
                }
            }
        }
        
        return implode("\n", $criticalLines);
    }

    private function treeShakeJS(string $js): string
    {
        // Simplified tree shaking - remove unused functions
        $usedFunctions = $this->getUsedJSFunctions();
        
        $lines = explode("\n", $js);
        $shakenLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Check if function is used
            if (preg_match('/function\s+(\w+)/', $line, $matches)) {
                $functionName = $matches[1];
                if (in_array($functionName, $usedFunctions)) {
                    $shakenLines[] = $line;
                }
            } else {
                $shakenLines[] = $line;
            }
        }
        
        return implode("\n", $shakenLines);
    }

    private function optimizeImage(string $filePath): bool
    {
        // This would use image optimization libraries in production
        // For now, just return true as a placeholder
        return true;
    }

    private function convertToWebP(string $sourcePath, string $webpPath): bool
    {
        // This would use image conversion libraries in production
        // For now, just return true as a placeholder
        return true;
    }

    private function generateResponsiveImages(string $filePath): bool
    {
        // Generate multiple sizes for responsive images
        $sizes = [320, 640, 768, 1024, 1200, 1920];
        
        foreach ($sizes as $size) {
            $responsivePath = str_replace('.', "_{$size}.", $filePath);
            // In production, resize image to $size width
        }
        
        return true;
    }

    private function canOptimizeImages(): bool
    {
        return false;
    }

    private function canConvertImagesToWebp(): bool
    {
        return false;
    }

    private function canGenerateResponsiveImages(): bool
    {
        return false;
    }

    private function canGenerateFontPreload(): bool
    {
        return false;
    }

    private function canConvertFontsToWoff2(): bool
    {
        return false;
    }

    private function generateFontPreload(string $fontPath): bool
    {
        // Generate preload link for font
        $fontName = basename($fontPath);
        $preloadPath = str_replace('.woff', '.preload.html', $fontPath);
        
        $preloadHtml = "<link rel=\"preload\" href=\"/frontend/fonts/{$fontName}\" as=\"font\" type=\"font/woff2\" crossorigin>";
        
        File::put($preloadPath, $preloadHtml);
        return true;
    }

    private function convertToWOFF2(string $sourcePath, string $woff2Path): bool
    {
        // This would use font conversion libraries in production
        return true;
    }

    private function getUsedCSSSelectors(): array
    {
        // Scan blade templates for used CSS classes
        $templates = $this->findFilesByExtensions($this->resourcesPath . '/views', ['blade.php']);
        $selectors = [];
        
        foreach ($templates as $template) {
            $content = File::get($template);
            
            // Extract class names
            preg_match_all('/class=["\']([^"\']+)["\']/', $content, $matches);
            foreach ($matches[1] as $classString) {
                $classes = explode(' ', $classString);
                $selectors = array_merge($selectors, $classes);
            }
        }
        
        return array_unique($selectors);
    }

    private function getUsedJSFunctions(): array
    {
        // Scan blade templates for used JavaScript functions
        $templates = $this->findFilesByExtensions($this->resourcesPath . '/views', ['blade.php']);
        $functions = [];
        
        foreach ($templates as $template) {
            $content = File::get($template);
            
            // Extract function calls
            preg_match_all('/(\w+)\s*\(/', $content, $matches);
            $functions = array_merge($functions, $matches[1]);
        }
        
        return array_unique($functions);
    }

    private function generateAssetManifest(): array
    {
        $manifest = [];
        
        // CSS files
        $cssFiles = collect(File::glob($this->publicPath . '/frontend/css/*.css'))
            ->filter(fn (string $file) => $this->isProcessableAsset($file, 'css'))
            ->values()
            ->all();
        foreach ($cssFiles as $file) {
            $manifest['css'][] = [
                'file' => basename($file),
                'size' => File::size($file),
                'modified' => File::lastModified($file),
            ];
        }
        
        // JS files
        $jsFiles = collect(File::glob($this->publicPath . '/frontend/js/*.js'))
            ->filter(fn (string $file) => $this->isProcessableAsset($file, 'js'))
            ->values()
            ->all();
        foreach ($jsFiles as $file) {
            $manifest['js'][] = [
                'file' => basename($file),
                'size' => File::size($file),
                'modified' => File::lastModified($file),
            ];
        }
        
        // Images
        $imageFiles = collect($this->findFilesByExtensions($this->publicPath . '/uploads', ['jpg', 'jpeg', 'png', 'gif']))
            ->filter(fn (string $file) => $this->isProcessableAsset($file, 'images'))
            ->values()
            ->all();
        foreach ($imageFiles as $file) {
            $manifest['images'][] = [
                'file' => basename($file),
                'path' => str_replace('\\', '/', str_replace($this->publicPath, '', $file)),
                'size' => File::size($file),
                'modified' => File::lastModified($file),
            ];
        }
        
        // Save manifest
        $manifestPath = $this->publicPath . '/mix-manifest.json';
        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        
        return $manifest;
    }

    public function generateOptimizedHTML(string $template, array $data = []): string
    {
        $html = view($template, $data)->render();
        
        // Minify HTML
        $html = $this->minifyHTML($html);
        
        // Add critical CSS inline
        $html = $this->inlineCriticalCSS($html);
        
        // Add preload hints
        $html = $this->addPreloadHints($html);
        
        // Add lazy loading attributes
        $html = $this->addLazyLoading($html);
        
        return $html;
    }

    private function minifyHTML(string $html): string
    {
        // Remove HTML comments
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        
        // Remove unnecessary whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace(['> ', ' <'], ['>', '<'], $html);
        
        return trim($html);
    }

    private function inlineCriticalCSS(string $html): string
    {
        $criticalCSS = $this->getCriticalCSS();
        
        if ($criticalCSS) {
            $styleTag = "<style>{$criticalCSS}</style>";
            $html = str_replace('</head>', "{$styleTag}</head>", $html);
        }
        
        return $html;
    }

    private function addPreloadHints(string $html): string
    {
        $preloadHints = [
            '<link rel="preload" href="/frontend/css/app.css" as="style">',
            '<link rel="preload" href="/frontend/js/app.js" as="script">',
        ];
        
        $hints = implode("\n", $preloadHints);
        $html = str_replace('</head>', "{$hints}</head>", $html);
        
        return $html;
    }

    private function addLazyLoading(string $html): string
    {
        // Add loading="lazy" to images
        $html = preg_replace('/<img([^>]+)src=/', '<img$1loading="lazy" src=', $html);
        
        return $html;
    }

    private function getCriticalCSS(): string
    {
        $criticalPath = $this->publicPath . '/frontend/css/app.critical.css';
        
        if (File::exists($criticalPath)) {
            return File::get($criticalPath);
        }
        
        return '';
    }

    public function getOptimizationStatistics(): array
    {
        return [
            'config' => $this->optimizationConfig,
            'asset_manifest' => $this->assetManifest,
            'optimization_enabled' => [
                'css_minify' => $this->optimizationConfig['css']['minify'],
                'js_minify' => $this->optimizationConfig['js']['minify'],
                'image_optimize' => $this->optimizationConfig['images']['optimize'],
                'font_preload' => $this->optimizationConfig['fonts']['preload'],
            ],
        ];
    }

    private function findFilesByExtensions(string $directory, array $extensions): array
    {
        if (! File::isDirectory($directory)) {
            return [];
        }

        $normalizedExtensions = array_map(
            static fn (string $extension) => ltrim(strtolower($extension), '.'),
            $extensions
        );

        return collect(File::allFiles($directory))
            ->map(static fn ($file) => $file->getPathname())
            ->filter(function (string $path) use ($normalizedExtensions) {
                $filename = strtolower(basename($path));

                foreach ($normalizedExtensions as $extension) {
                    if (str_ends_with($filename, '.' . $extension)) {
                        return true;
                    }
                }

                return false;
            })
            ->values()
            ->all();
    }

    private function isGeneratedOptimizationArtifact(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        foreach (['.min', '.purged', '.critical', '.shaken'] as $suffix) {
            if (str_ends_with($filename, $suffix)) {
                return true;
            }
        }

        return false;
    }

    private function cleanupDirectoryArtifacts(string $directory, callable $shouldDelete): array
    {
        if (! File::isDirectory($directory)) {
            return [];
        }

        $deleted = [];

        foreach (File::files($directory) as $file) {
            $path = $file->getPathname();

            if (! $shouldDelete($path)) {
                continue;
            }

            File::delete($path);
            $deleted[] = basename($path);
        }

        return $deleted;
    }

    private function shouldDeleteGeneratedCssArtifact(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        if (str_contains($filename, '.critical') || str_contains($filename, '.purged')) {
            return true;
        }

        return substr_count($filename, '.min') > 1;
    }

    private function shouldDeleteGeneratedJsArtifact(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        if (str_contains($filename, '.shaken')) {
            return true;
        }

        return substr_count($filename, '.min') > 1;
    }

    private function isProcessableAsset(string $path, string $type): bool
    {
        if ($this->isGeneratedOptimizationArtifact($path)) {
            return false;
        }

        $basename = strtolower(basename($path));

        if ($type === 'fonts' && str_ends_with($basename, '.woff2')) {
            return false;
        }

        if ($type === 'images' && preg_match('/_\d+\.(jpg|jpeg|png|gif)$/', $basename) === 1) {
            return false;
        }

        return true;
    }
}
