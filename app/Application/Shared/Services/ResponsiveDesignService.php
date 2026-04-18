<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ResponsiveDesignService
{
    private array $breakpoints = [];
    private array $responsiveConfig = [];
    private string $publicPath;

    public function __construct()
    {
        $this->publicPath = public_path();
        $this->initializeBreakpoints();
        $this->initializeResponsiveConfig();
    }

    private function initializeBreakpoints(): void
    {
        $this->breakpoints = [
            'xs' => ['min' => 0, 'max' => 575, 'name' => 'Extra Small'],
            'sm' => ['min' => 576, 'max' => 767, 'name' => 'Small'],
            'md' => ['min' => 768, 'max' => 991, 'name' => 'Medium'],
            'lg' => ['min' => 992, 'max' => 1199, 'name' => 'Large'],
            'xl' => ['min' => 1200, 'max' => 1399, 'name' => 'Extra Large'],
            'xxl' => ['min' => 1400, 'max' => null, 'name' => 'Extra Extra Large'],
        ];
    }

    private function initializeResponsiveConfig(): void
    {
        $this->responsiveConfig = [
            'images' => [
                'sizes' => [
                    'xs' => 320,
                    'sm' => 576,
                    'md' => 768,
                    'lg' => 992,
                    'xl' => 1200,
                    'xxl' => 1920,
                ],
                'formats' => ['webp', 'jpg', 'png'],
                'quality' => 85,
            ],
            'typography' => [
                'scaling' => true,
                'fluid_typography' => true,
                'base_font_size' => 16,
            ],
            'layout' => [
                'container_max_widths' => [
                    'sm' => 540,
                    'md' => 720,
                    'lg' => 960,
                    'xl' => 1140,
                    'xxl' => 1320,
                ],
                'grid_columns' => 12,
                'gutters' => [
                    'xs' => 15,
                    'sm' => 20,
                    'md' => 30,
                    'lg' => 40,
                ],
            ],
            'components' => [
                'responsive_navigation' => true,
                'mobile_first' => true,
                'touch_friendly' => true,
            ],
        ];
    }

    public function generateResponsiveCSS(?string $outputDirectory = null): array
    {
        Log::info('Generating responsive CSS...');

        $results = [];
        $targetDirectory = $outputDirectory ?: $this->publicPath . '/frontend/css';
        
        try {
            // Generate base responsive CSS
            $baseCSS = $this->generateBaseResponsiveCSS();
            $this->saveCSSFile('responsive-base.css', $baseCSS, $targetDirectory);
            $results['base_css'] = 'Generated';

            // Generate breakpoint-specific CSS
            foreach ($this->breakpoints as $breakpoint => $config) {
                $breakpointCSS = $this->generateBreakpointCSS($breakpoint, $config);
                $this->saveCSSFile("responsive-{$breakpoint}.css", $breakpointCSS, $targetDirectory);
                $results["breakpoint_{$breakpoint}"] = 'Generated';
            }

            // Generate responsive utilities
            $utilitiesCSS = $this->generateResponsiveUtilities();
            $this->saveCSSFile('responsive-utilities.css', $utilitiesCSS, $targetDirectory);
            $results['utilities'] = 'Generated';

            // Generate responsive grid system
            $gridCSS = $this->generateResponsiveGrid();
            $this->saveCSSFile('responsive-grid.css', $gridCSS, $targetDirectory);
            $results['grid'] = 'Generated';

        } catch (\Exception $e) {
            Log::error('Failed to generate responsive CSS', [
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function generateBaseResponsiveCSS(): string
    {
        $css = "
/* Responsive Base Styles */
* {
    box-sizing: border-box;
}

html {
    font-size: {$this->responsiveConfig['typography']['base_font_size']}px;
    -webkit-text-size-adjust: 100%;
    -ms-text-size-adjust: 100%;
}

body {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
}

/* Responsive Images */
img {
    max-width: 100%;
    height: auto;
    display: block;
}

/* Responsive Tables */
table {
    width: 100%;
    border-collapse: collapse;
    overflow-x: auto;
    display: block;
    white-space: nowrap;
}

/* Responsive Videos */
video, iframe {
    max-width: 100%;
    height: auto;
}

/* Touch-friendly buttons */
button, .btn {
    min-height: 44px;
    min-width: 44px;
    touch-action: manipulation;
}

/* Responsive Typography */
h1 { font-size: clamp(1.5rem, 4vw, 3rem); }
h2 { font-size: clamp(1.25rem, 3vw, 2.5rem); }
h3 { font-size: clamp(1.125rem, 2.5vw, 2rem); }
h4 { font-size: clamp(1rem, 2vw, 1.5rem); }
h5 { font-size: clamp(0.875rem, 1.5vw, 1.25rem); }
h6 { font-size: clamp(0.75rem, 1.25vw, 1rem); }

p { font-size: clamp(0.875rem, 1.5vw, 1rem); }

/* Responsive Spacing */
.container {
    width: 100%;
    padding-left: 15px;
    padding-right: 15px;
    margin-left: auto;
    margin-right: auto;
}

/* Responsive Navigation */
.navbar {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 1rem;
}

.navbar-nav {
    display: flex;
    flex-direction: column;
    padding-left: 0;
    margin-bottom: 0;
    list-style: none;
}

@media (min-width: 768px) {
    .navbar-nav {
        flex-direction: row;
    }
}

/* Mobile-first responsive design */
@media (max-width: 575.98px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .navbar-nav {
        width: 100%;
        margin-top: 1rem;
    }
}
";

        return $css;
    }

    private function generateBreakpointCSS(string $breakpoint, array $config): string
    {
        $minWidth = $config['min'];
        $maxWidth = $config['max'];
        $name = $config['name'];

        $css = "
/* {$name} Breakpoint ({$minWidth}px" . ($maxWidth ? " - {$maxWidth}px" : "+") . ") */

@media (min-width: {$minWidth}px)" . ($maxWidth ? " and (max-width: {$maxWidth}px)" : "") . " {
    .container {
        max-width: " . ($this->responsiveConfig['layout']['container_max_widths'][$breakpoint] ?? '100%') . "px;
    }
    
    /* Typography scaling */
    html {
        font-size: " . $this->calculateResponsiveFontSize($breakpoint) . "px;
    }
    
    /* Spacing adjustments */
    .py-{$breakpoint}-1 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
    .py-{$breakpoint}-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
    .py-{$breakpoint}-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .py-{$breakpoint}-4 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
    .py-{$breakpoint}-5 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
    
    /* Margin adjustments */
    .my-{$breakpoint}-1 { margin-top: 0.25rem !important; margin-bottom: 0.25rem !important; }
    .my-{$breakpoint}-2 { margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
    .my-{$breakpoint}-3 { margin-top: 1rem !important; margin-bottom: 1rem !important; }
    .my-{$breakpoint}-4 { margin-top: 1.5rem !important; margin-bottom: 1.5rem !important; }
    .my-{$breakpoint}-5 { margin-top: 3rem !important; margin-bottom: 3rem !important; }
    
    /* Display utilities */
    .d-{$breakpoint}-none { display: none !important; }
    .d-{$breakpoint}-block { display: block !important; }
    .d-{$breakpoint}-flex { display: flex !important; }
    .d-{$breakpoint}-inline { display: inline !important; }
    .d-{$breakpoint}-inline-block { display: inline-block !important; }
    
    /* Flexbox utilities */
    .flex-{$breakpoint}-row { flex-direction: row !important; }
    .flex-{$breakpoint}-column { flex-direction: column !important; }
    .flex-{$breakpoint}-wrap { flex-wrap: wrap !important; }
    .flex-{$breakpoint}-nowrap { flex-wrap: nowrap !important; }
    
    /* Justify content */
    .justify-{$breakpoint}-start { justify-content: flex-start !important; }
    .justify-{$breakpoint}-end { justify-content: flex-end !important; }
    .justify-{$breakpoint}-center { justify-content: center !important; }
    .justify-{$breakpoint}-between { justify-content: space-between !important; }
    .justify-{$breakpoint}-around { justify-content: space-around !important; }
    
    /* Align items */
    .align-{$breakpoint}-start { align-items: flex-start !important; }
    .align-{$breakpoint}-end { align-items: flex-end !important; }
    .align-{$breakpoint}-center { align-items: center !important; }
    .align-{$breakpoint}-baseline { align-items: baseline !important; }
    .align-{$breakpoint}-stretch { align-items: stretch !important; }
    
    /* Text alignment */
    .text-{$breakpoint}-left { text-align: left !important; }
    .text-{$breakpoint}-right { text-align: right !important; }
    .text-{$breakpoint}-center { text-align: center !important; }
    .text-{$breakpoint}-justify { text-align: justify !important; }
    
    /* Width utilities */
    .w-{$breakpoint}-25 { width: 25% !important; }
    .w-{$breakpoint}-50 { width: 50% !important; }
    .w-{$breakpoint}-75 { width: 75% !important; }
    .w-{$breakpoint}-100 { width: 100% !important; }
    .w-{$breakpoint}-auto { width: auto !important; }
    
    /* Height utilities */
    .h-{$breakpoint}-25 { height: 25% !important; }
    .h-{$breakpoint}-50 { height: 50% !important; }
    .h-{$breakpoint}-75 { height: 75% !important; }
    .h-{$breakpoint}-100 { height: 100% !important; }
    .h-{$breakpoint}-auto { height: auto !important; }
}
";

        return $css;
    }

    private function generateResponsiveUtilities(): string
    {
        $css = "
/* Responsive Utilities */

/* Responsive visibility */
.d-xs-none { display: none !important; }
.d-sm-none { display: none !important; }
.d-md-none { display: none !important; }
.d-lg-none { display: none !important; }
.d-xl-none { display: none !important; }
.d-xxl-none { display: none !important; }

/* Responsive spacing */
.m-0 { margin: 0 !important; }
.m-1 { margin: 0.25rem !important; }
.m-2 { margin: 0.5rem !important; }
.m-3 { margin: 1rem !important; }
.m-4 { margin: 1.5rem !important; }
.m-5 { margin: 3rem !important; }
.m-auto { margin: auto !important; }

.p-0 { padding: 0 !important; }
.p-1 { padding: 0.25rem !important; }
.p-2 { padding: 0.5rem !important; }
.p-3 { padding: 1rem !important; }
.p-4 { padding: 1.5rem !important; }
.p-5 { padding: 3rem !important; }

/* Responsive borders */
.border { border: 1px solid #dee2e6 !important; }
.border-0 { border: 0 !important; }
.border-top { border-top: 1px solid #dee2e6 !important; }
.border-right { border-right: 1px solid #dee2e6 !important; }
.border-bottom { border-bottom: 1px solid #dee2e6 !important; }
.border-left { border-left: 1px solid #dee2e6 !important; }

/* Responsive shadows */
.shadow-sm { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; }
.shadow { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
.shadow-lg { box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important; }
.shadow-none { box-shadow: none !important; }

/* Responsive overflow */
.overflow-auto { overflow: auto !important; }
.overflow-hidden { overflow: hidden !important; }
.overflow-visible { overflow: visible !important; }
.overflow-scroll { overflow: scroll !important; }

/* Responsive position */
.position-static { position: static !important; }
.position-relative { position: relative !important; }
.position-absolute { position: absolute !important; }
.position-fixed { position: fixed !important; }
.position-sticky { position: sticky !important; }

/* Responsive z-index */
.z-0 { z-index: 0 !important; }
.z-1 { z-index: 1 !important; }
.z-2 { z-index: 2 !important; }
.z-3 { z-index: 3 !important; }
.z-4 { z-index: 4 !important; }
.z-5 { z-index: 5 !important; }

/* Responsive opacity */
.opacity-0 { opacity: 0 !important; }
.opacity-25 { opacity: 0.25 !important; }
.opacity-50 { opacity: 0.5 !important; }
.opacity-75 { opacity: 0.75 !important; }
.opacity-100 { opacity: 1 !important; }

/* Responsive cursor */
.cursor-pointer { cursor: pointer !important; }
.cursor-default { cursor: default !important; }
.cursor-move { cursor: move !important; }
.cursor-not-allowed { cursor: not-allowed !important; }

/* Responsive user select */
.user-select-none { user-select: none !important; }
.user-select-auto { user-select: auto !important; }
.user-select-all { user-select: all !important; }

/* Responsive pointer events */
.pointer-events-none { pointer-events: none !important; }
.pointer-events-auto { pointer-events: auto !important; }
";

        return $css;
    }

    private function generateResponsiveGrid(): string
    {
        $css = "
/* Responsive Grid System */

.row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -15px;
    margin-right: -15px;
}

.col {
    flex: 1 0 0%;
    padding-left: 15px;
    padding-right: 15px;
}

";

        // Generate column classes for each breakpoint
        foreach ($this->breakpoints as $breakpoint => $config) {
            $css .= "
/* {$breakpoint} breakpoint columns */
";

            for ($i = 1; $i <= $this->responsiveConfig['layout']['grid_columns']; $i++) {
                $width = ($i / $this->responsiveConfig['layout']['grid_columns']) * 100;
                
                $css .= "
.col-{$breakpoint}-{$i} {
    flex: 0 0 {$width}%;
    max-width: {$width}%;
    padding-left: 15px;
    padding-right: 15px;
}
";
            }

            // Generate offset classes
            for ($i = 1; $i < $this->responsiveConfig['layout']['grid_columns']; $i++) {
                $offset = ($i / $this->responsiveConfig['layout']['grid_columns']) * 100;
                
                $css .= "
.offset-{$breakpoint}-{$i} {
    margin-left: {$offset}%;
}
";
            }
        }

        return $css;
    }

    private function calculateResponsiveFontSize(string $breakpoint): int
    {
        $baseSize = $this->responsiveConfig['typography']['base_font_size'];
        $multipliers = [
            'xs' => 0.875,
            'sm' => 0.9,
            'md' => 1,
            'lg' => 1.1,
            'xl' => 1.2,
            'xxl' => 1.3,
        ];

        return (int) ($baseSize * ($multipliers[$breakpoint] ?? 1));
    }

    private function saveCSSFile(string $filename, string $content, ?string $targetDirectory = null): void
    {
        $cssPath = $targetDirectory ?: $this->publicPath . '/frontend/css';
        
        if (!is_dir($cssPath)) {
            File::makeDirectory($cssPath, 0755, true);
        }

        File::put($cssPath . '/' . $filename, $content);
    }

    public function generateResponsiveImages(string $imagePath): array
    {
        $results = [];
        
        if (!is_file($imagePath)) {
            return ['error' => 'Image file not found'];
        }

        $imageInfo = pathinfo($imagePath);
        $baseName = $imageInfo['filename'];
        $extension = $imageInfo['extension'];
        $directory = $imageInfo['dirname'];

        try {
            foreach ($this->responsiveConfig['images']['sizes'] as $breakpoint => $size) {
                $responsivePath = $directory . '/' . $baseName . "_{$breakpoint}_{$size}w.{$extension}";

                if (! $this->supportsResponsiveImageGeneration()) {
                    $results[$breakpoint] = [
                        'size' => $size,
                        'path' => $responsivePath,
                        'created' => false,
                        'status' => 'skipped',
                    ];
                    continue;
                }

                $results[$breakpoint] = [
                    'size' => $size,
                    'path' => $responsivePath,
                    'created' => true,
                    'status' => 'created',
                ];
            }

            // Generate WebP versions
            foreach ($this->responsiveConfig['images']['sizes'] as $breakpoint => $size) {
                $webpPath = $directory . '/' . $baseName . "_{$breakpoint}_{$size}w.webp";

                if (! $this->supportsResponsiveImageGeneration()) {
                    $results["{$breakpoint}_webp"] = [
                        'size' => $size,
                        'path' => $webpPath,
                        'format' => 'webp',
                        'created' => false,
                        'status' => 'skipped',
                    ];
                    continue;
                }

                $results["{$breakpoint}_webp"] = [
                    'size' => $size,
                    'path' => $webpPath,
                    'format' => 'webp',
                    'created' => true,
                    'status' => 'created',
                ];
            }

        } catch (\Exception $e) {
            Log::error('Failed to generate responsive images', [
                'image' => $imagePath,
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function supportsResponsiveImageGeneration(): bool
    {
        return false;
    }

    public function generateResponsiveHTML(string $template, array $data = []): string
    {
        $html = view($template, $data)->render();
        
        // Add responsive meta tags
        $html = $this->addResponsiveMetaTags($html);
        
        // Add responsive images
        $html = $this->addResponsiveImages($html);
        
        // Add responsive navigation
        $html = $this->addResponsiveNavigation($html);
        
        return $html;
    }

    private function addResponsiveMetaTags(string $html): string
    {
        $metaTags = '
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#ffffff">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
';

        $html = str_replace('</head>', "{$metaTags}</head>", $html);
        
        return $html;
    }

    private function addResponsiveImages(string $html): string
    {
        // Convert img tags to responsive picture elements
        $html = preg_replace_callback(
            '/<img([^>]+)src=["\']([^"\']+)["\']([^>]*)>/',
            function ($matches) {
                $attributes = $matches[1];
                $src = $matches[2];
                $additionalAttributes = $matches[3];
                
                // Generate responsive image HTML
                $pictureHtml = $this->generatePictureElement($src, $attributes . $additionalAttributes);
                
                return $pictureHtml;
            },
            $html
        );
        
        return $html;
    }

    private function generatePictureElement(string $src, string $attributes): string
    {
        $imageInfo = pathinfo($src);
        $baseName = $imageInfo['filename'];
        $extension = $imageInfo['extension'];
        $directory = $imageInfo['dirname'];

        $sources = '';
        foreach ($this->responsiveConfig['images']['sizes'] as $breakpoint => $size) {
            $webpSrc = $directory . '/' . $baseName . "_{$breakpoint}_{$size}w.webp";
            $jpgSrc = $directory . '/' . $baseName . "_{$breakpoint}_{$size}w.{$extension}";
            
            $sources .= "<source media=\"(min-width: {$this->breakpoints[$breakpoint]['min']}px)\" srcset=\"{$webpSrc}\" type=\"image/webp\">";
            $sources .= "<source media=\"(min-width: {$this->breakpoints[$breakpoint]['min']}px)\" srcset=\"{$jpgSrc}\">";
        }

        $fallbackSrc = $src;
        
        return "<picture>{$sources}<img{$attributes}src=\"{$fallbackSrc}\" loading=\"lazy\"></picture>";
    }

    private function addResponsiveNavigation(string $html): string
    {
        // Add mobile navigation toggle
        $mobileNav = '
<button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
</button>
';

        $html = str_replace('<nav class="navbar">', "<nav class=\"navbar\">{$mobileNav}", $html);
        
        return $html;
    }

    public function getResponsiveStatistics(): array
    {
        return [
            'breakpoints' => $this->breakpoints,
            'config' => $this->responsiveConfig,
            'total_breakpoints' => count($this->breakpoints),
            'grid_columns' => $this->responsiveConfig['layout']['grid_columns'],
            'image_sizes' => count($this->responsiveConfig['images']['sizes']),
        ];
    }

    public function validateResponsiveDesign(?string $cssPath = null): array
    {
        $issues = [];

        // Check if responsive CSS files exist
        $cssPath = $cssPath ?: $this->publicPath . '/frontend/css';
        $requiredFiles = [
            'responsive-base.css',
            'responsive-utilities.css',
            'responsive-grid.css',
        ];

        foreach ($requiredFiles as $file) {
            if (!File::exists($cssPath . '/' . $file)) {
                $issues[] = "Missing responsive CSS file: {$file}";
            }
        }

        // Check breakpoint configuration
        foreach ($this->breakpoints as $breakpoint => $config) {
            if (!isset($config['min']) || !isset($config['max'])) {
                $issues[] = "Invalid breakpoint configuration for: {$breakpoint}";
            }
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
        ];
    }
}
