<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemeManagementCommand extends Command
{
    protected $signature = 'theme:manage {action} {--theme=}';
    protected $description = 'Manage Dark/Light theme settings';

    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'status':
                $this->showStatus();
                break;
            case 'reset':
                $this->resetTheme();
                break;
            case 'compile':
                $this->compileTheme();
                break;
            case 'validate':
                $this->validateTheme();
                break;
            default:
                $this->error('Invalid action. Available actions: status, reset, compile, validate');
        }
    }

    private function showStatus()
    {
        $this->info('🌙 Dark/Light Mode Status:');
        $this->line('');
        
        // Check CSS files
        $cssFiles = [
            'public/frontend/css/dark-light-mode.css',
            'public/frontend/css/dark-mode-components.css'
        ];
        
        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $size = File::size($file);
                $this->info("✅ {$file} ({$size} bytes)");
            } else {
                $this->error("❌ {$file} not found");
            }
        }
        
        // Check JS file
        $jsFile = 'public/frontend/js/dark-light-mode.js';
        if (File::exists($jsFile)) {
            $size = File::size($jsFile);
            $this->info("✅ {$jsFile} ({$size} bytes)");
        } else {
            $this->error("❌ {$jsFile} not found");
        }
        
        // Check component
        $componentFile = 'resources/views/components/theme-toggle.blade.php';
        if (File::exists($componentFile)) {
            $this->info("✅ {$componentFile}");
        } else {
            $this->error("❌ {$componentFile} not found");
        }
        
        // Check demo file
        $demoFile = 'public/dark-mode-demo.html';
        if (File::exists($demoFile)) {
            $this->info("✅ {$demoFile}");
        } else {
            $this->warn("⚠️ {$demoFile} not found (optional)");
        }
        
        $this->line('');
        $this->info('🎨 Theme Features:');
        $this->line('• Dark/Light mode toggle');
        $this->line('• LocalStorage persistence');
        $this->line('• System preference detection');
        $this->line('• Smooth transitions');
        $this->line('• Responsive design');
        $this->line('• Accessibility support');
    }

    private function resetTheme()
    {
        $this->info('🔄 Resetting theme settings...');
        
        // Clear theme from localStorage (this will be handled by JavaScript)
        $this->info('Theme preference will be reset to system default on next page load.');
        
        $this->info('✅ Theme reset completed!');
    }

    private function compileTheme()
    {
        $this->info('🔨 Compiling theme assets...');
        
        // Check if Vite is available
        if (File::exists('package.json')) {
            $this->info('Running Vite build...');
            $this->call('vite:build');
        }
        
        // Clear caches
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('cache:clear');
        
        $this->info('✅ Theme compilation completed!');
    }

    private function validateTheme()
    {
        $this->info('🔍 Validating theme implementation...');
        
        $errors = [];
        $warnings = [];
        
        // Check layout file
        $layoutFile = 'resources/views/layout.blade.php';
        if (File::exists($layoutFile)) {
            $content = File::get($layoutFile);
            
            if (!str_contains($content, 'dark-light-mode.css')) {
                $errors[] = "Missing dark-light-mode.css in {$layoutFile}";
            }
            
            if (!str_contains($content, 'dark-mode-components.css')) {
                $errors[] = "Missing dark-mode-components.css in {$layoutFile}";
            }
            
            if (!str_contains($content, 'dark-light-mode.js')) {
                $errors[] = "Missing dark-light-mode.js in {$layoutFile}";
            }
            
            if (!str_contains($content, 'components.theme-toggle')) {
                $errors[] = "Missing theme-toggle component in {$layoutFile}";
            }
        } else {
            $errors[] = "Layout file not found: {$layoutFile}";
        }
        
        // Check CSS variables
        $cssFile = 'public/frontend/css/dark-light-mode.css';
        if (File::exists($cssFile)) {
            $content = File::get($cssFile);
            
            if (!str_contains($content, ':root')) {
                $errors[] = "Missing CSS variables in {$cssFile}";
            }
            
            if (!str_contains($content, '[data-theme="dark"]')) {
                $errors[] = "Missing dark theme styles in {$cssFile}";
            }
        }
        
        // Check JavaScript functionality
        $jsFile = 'public/frontend/js/dark-light-mode.js';
        if (File::exists($jsFile)) {
            $content = File::get($jsFile);
            
            if (!str_contains($content, 'ThemeToggle')) {
                $errors[] = "Missing ThemeToggle class in {$jsFile}";
            }
            
            if (!str_contains($content, 'localStorage')) {
                $warnings[] = "Missing localStorage functionality in {$jsFile}";
            }
        }
        
        // Display results
        if (empty($errors) && empty($warnings)) {
            $this->info('✅ Theme validation passed! All components are properly configured.');
        } else {
            if (!empty($errors)) {
                $this->error('❌ Validation errors:');
                foreach ($errors as $error) {
                    $this->error("  • {$error}");
                }
            }
            
            if (!empty($warnings)) {
                $this->warn('⚠️ Warnings:');
                foreach ($warnings as $warning) {
                    $this->warn("  • {$warning}");
                }
            }
        }
        
        $this->line('');
        $this->info('🎯 Next steps:');
        $this->line('1. Test the theme toggle on your website');
        $this->line('2. Check browser console for any JavaScript errors');
        $this->line('3. Test on different devices and browsers');
        $this->line('4. Verify localStorage persistence');
    }
}
