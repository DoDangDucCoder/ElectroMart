<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ModernUIComponentsService
{
    private array $componentConfig = [];
    private string $publicPath;
    private string $resourcesPath;

    public function __construct()
    {
        $this->publicPath = public_path();
        $this->resourcesPath = resource_path();
        $this->initializeComponentConfig();
    }

    private function initializeComponentConfig(): void
    {
        $this->componentConfig = [
            'buttons' => [
                'variants' => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'],
                'sizes' => ['sm', 'md', 'lg'],
                'styles' => ['solid', 'outline', 'ghost', 'link'],
            ],
            'cards' => [
                'variants' => ['default', 'elevated', 'outlined', 'filled'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['header', 'footer', 'actions', 'media'],
            ],
            'forms' => [
                'input_types' => ['text', 'email', 'password', 'number', 'tel', 'url', 'search'],
                'validation_states' => ['default', 'valid', 'invalid'],
                'features' => ['floating_labels', 'help_text', 'icons', 'groups'],
            ],
            'navigation' => [
                'types' => ['navbar', 'sidebar', 'breadcrumb', 'pagination', 'tabs'],
                'responsive' => true,
                'accessibility' => true,
            ],
            'modals' => [
                'sizes' => ['sm', 'md', 'lg', 'xl'],
                'features' => ['backdrop', 'keyboard', 'focus_trap'],
            ],
            'alerts' => [
                'types' => ['success', 'info', 'warning', 'error'],
                'dismissible' => true,
                'auto_dismiss' => true,
            ],
            'tables' => [
                'features' => ['sorting', 'filtering', 'pagination', 'responsive'],
                'styles' => ['striped', 'bordered', 'hover'],
            ],
            'loaders' => [
                'types' => ['spinner', 'skeleton', 'progress', 'pulse'],
                'sizes' => ['sm', 'md', 'lg'],
            ],
        ];
    }

    public function generateModernComponents(): array
    {
        Log::info('Generating modern UI components...');

        $results = [
            'buttons' => $this->generateButtonComponents(),
            'cards' => $this->generateCardComponents(),
            'forms' => $this->generateFormComponents(),
            'navigation' => $this->generateNavigationComponents(),
            'modals' => $this->generateModalComponents(),
            'alerts' => $this->generateAlertComponents(),
            'tables' => $this->generateTableComponents(),
            'loaders' => $this->generateLoaderComponents(),
        ];

        Log::info('Modern UI components generation completed');
        return $results;
    }

    private function generateButtonComponents(): array
    {
        $css = "
/* Modern Button Components */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.5;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    border: 1px solid transparent;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
    position: relative;
    overflow: hidden;
}

.btn:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Button Variants */
.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
    border-color: #4e555b;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
}

.btn-success {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #1e7e34;
    border-color: #1c7430;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.btn-danger {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

/* Button Sizes */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.25rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
    border-radius: 0.5rem;
}

/* Button Styles */
.btn-outline {
    background-color: transparent;
    border-width: 2px;
}

.btn-outline.btn-primary {
    color: #007bff;
    border-color: #007bff;
}

.btn-outline.btn-primary:hover {
    color: #fff;
    background-color: #007bff;
}

.btn-ghost {
    background-color: transparent;
    border: none;
    color: inherit;
}

.btn-ghost:hover {
    background-color: rgba(0, 0, 0, 0.1);
}

.btn-link {
    background-color: transparent;
    border: none;
    color: #007bff;
    text-decoration: underline;
}

.btn-link:hover {
    color: #0056b3;
    text-decoration: none;
}

/* Button Groups */
.btn-group {
    position: relative;
    display: inline-flex;
    vertical-align: middle;
}

.btn-group > .btn {
    position: relative;
    flex: 1 1 auto;
}

.btn-group > .btn:not(:last-child) {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group > .btn:not(:first-child) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Icon Buttons */
.btn-icon {
    width: 2.5rem;
    height: 2.5rem;
    padding: 0;
    border-radius: 50%;
}

.btn-icon.btn-sm {
    width: 2rem;
    height: 2rem;
}

.btn-icon.btn-lg {
    width: 3rem;
    height: 3rem;
}

/* Loading State */
.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 1rem;
    height: 1rem;
    top: 50%;
    left: 50%;
    margin-left: -0.5rem;
    margin-top: -0.5rem;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
";

        $js = "
// Button Component JavaScript
class ButtonComponent {
    constructor(element) {
        this.element = element;
        this.init();
    }
    
    init() {
        this.addRippleEffect();
        this.addLoadingState();
    }
    
    addRippleEffect() {
        this.element.addEventListener('click', (e) => {
            const ripple = document.createElement('span');
            const rect = this.element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.element.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }
    
    addLoadingState() {
        // Add loading state functionality
        this.element.addEventListener('click', () => {
            if (this.element.dataset.loading === 'true') {
                this.element.classList.add('btn-loading');
                this.element.disabled = true;
            }
        });
    }
}

// Initialize all buttons
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn').forEach(btn => {
        new ButtonComponent(btn);
    });
});
";

        return [
            'css' => $css,
            'js' => $js,
            'html_examples' => $this->generateButtonHTMLExamples(),
        ];
    }

    private function generateCardComponents(): array
    {
        $css = "
/* Modern Card Components */
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.15s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card-header {
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    border-top-left-radius: calc(0.375rem - 1px);
    border-top-right-radius: calc(0.375rem - 1px);
}

.card-body {
    flex: 1 1 auto;
    padding: 1.25rem;
}

.card-footer {
    padding: 0.75rem 1.25rem;
    background-color: rgba(0, 0, 0, 0.03);
    border-top: 1px solid rgba(0, 0, 0, 0.125);
    border-bottom-left-radius: calc(0.375rem - 1px);
    border-bottom-right-radius: calc(0.375rem - 1px);
}

.card-title {
    margin-bottom: 0.5rem;
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.2;
}

.card-subtitle {
    margin-top: -0.25rem;
    margin-bottom: 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.card-text {
    margin-bottom: 1rem;
}

.card-img-top {
    width: 100%;
    border-top-left-radius: calc(0.375rem - 1px);
    border-top-right-radius: calc(0.375rem - 1px);
}

.card-img-bottom {
    width: 100%;
    border-bottom-left-radius: calc(0.375rem - 1px);
    border-bottom-right-radius: calc(0.375rem - 1px);
}

/* Card Variants */
.card-elevated {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card-outlined {
    border-width: 2px;
    box-shadow: none;
}

.card-filled {
    background-color: #f8f9fa;
    border: none;
}

/* Card Sizes */
.card-sm {
    max-width: 18rem;
}

.card-md {
    max-width: 24rem;
}

.card-lg {
    max-width: 32rem;
}

/* Card Actions */
.card-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.card-actions .btn {
    flex: 1;
}

/* Card Grid */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 1rem;
}

/* Card Hover Effects */
.card-hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2);
}

.card-hover-scale:hover {
    transform: scale(1.02);
}

.card-hover-glow:hover {
    box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
}
";

        return [
            'css' => $css,
            'html_examples' => $this->generateCardHTMLExamples(),
        ];
    }

    private function generateFormComponents(): array
    {
        $css = "
/* Modern Form Components */
.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: inline-block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #495057;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control::placeholder {
    color: #6c757d;
    opacity: 1;
}

/* Form Control Sizes */
.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.25rem;
}

.form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1.125rem;
    border-radius: 0.5rem;
}

/* Form Validation States */
.form-control.is-valid {
    border-color: #28a745;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.44 1.44 2.5-2.5.94.94-3.44 3.44z'/%3e%3c/svg%3e\");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4 1.4-1.4'/%3e%3c/svg%3e\");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.valid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #28a745;
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #dc3545;
}

.form-control.is-valid ~ .valid-feedback,
.form-control.is-invalid ~ .invalid-feedback {
    display: block;
}

/* Floating Labels */
.form-floating {
    position: relative;
}

.form-floating > .form-control {
    height: calc(3.5rem + 2px);
    line-height: 1.25;
}

.form-floating > .form-control::placeholder {
    color: transparent;
}

.form-floating > .form-control:focus,
.form-floating > .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
}

.form-floating > .form-label {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    padding: 1rem 0.75rem;
    pointer-events: none;
    border: 1px solid transparent;
    transform-origin: 0 0;
    transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
}

.form-floating > .form-control:focus ~ .form-label,
.form-floating > .form-control:not(:placeholder-shown) ~ .form-label {
    opacity: 0.65;
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
}

/* Input Groups */
.input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.input-group > .form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
}

.input-group-text {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    text-align: center;
    white-space: nowrap;
    background-color: #e9ecef;
    border: 1px solid #ced4da;
}

.input-group-text:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.input-group-text:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

/* Checkboxes and Radios */
.form-check {
    display: block;
    min-height: 1.5rem;
    padding-left: 1.5em;
    margin-bottom: 0.125rem;
}

.form-check-input {
    width: 1em;
    height: 1em;
    margin-top: 0.25em;
    vertical-align: top;
    background-color: #fff;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    border: 1px solid rgba(0, 0, 0, 0.25);
    appearance: none;
    color-adjust: exact;
}

.form-check-input[type='checkbox'] {
    border-radius: 0.25em;
}

.form-check-input[type='radio'] {
    border-radius: 50%;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-input:checked[type='checkbox'] {
    background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e\");
}

.form-check-input:checked[type='radio'] {
    background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='2' fill='%23fff'/%3e%3c/svg%3e\");
}

.form-check-label {
    color: #495057;
    cursor: pointer;
}
";

        return [
            'css' => $css,
            'html_examples' => $this->generateFormHTMLExamples(),
        ];
    }

    private function generateNavigationComponents(): array
    {
        $css = "
/* Modern Navigation Components */
.navbar {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 1rem;
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
}

.navbar-brand {
    padding-top: 0.3125rem;
    padding-bottom: 0.3125rem;
    margin-right: 1rem;
    font-size: 1.25rem;
    font-weight: 500;
    color: rgba(0, 0, 0, 0.9);
    text-decoration: none;
}

.navbar-nav {
    display: flex;
    flex-direction: column;
    padding-left: 0;
    margin-bottom: 0;
    list-style: none;
}

.navbar-nav .nav-link {
    padding-right: 0;
    padding-left: 0;
    color: rgba(0, 0, 0, 0.55);
    text-decoration: none;
    transition: color 0.15s ease-in-out;
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link:focus {
    color: rgba(0, 0, 0, 0.7);
}

.navbar-nav .nav-link.active {
    color: rgba(0, 0, 0, 0.9);
}

.navbar-toggler {
    padding: 0.25rem 0.75rem;
    font-size: 1.25rem;
    line-height: 1;
    background-color: transparent;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.375rem;
    transition: box-shadow 0.15s ease-in-out;
}

.navbar-toggler:hover {
    text-decoration: none;
}

.navbar-toggler:focus {
    text-decoration: none;
    outline: 0;
    box-shadow: 0 0 0 0.2rem;
}

.navbar-toggler-icon {
    display: inline-block;
    width: 1.5em;
    height: 1.5em;
    vertical-align: middle;
    background-repeat: no-repeat;
    background-position: center;
    background-size: 100%;
}

/* Responsive Navigation */
@media (min-width: 768px) {
    .navbar-nav {
        flex-direction: row;
    }
    
    .navbar-nav .nav-link {
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }
    
    .navbar-toggler {
        display: none;
    }
}

/* Sidebar Navigation */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 250px;
    background-color: #f8f9fa;
    border-right: 1px solid #dee2e6;
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    z-index: 1000;
}

.sidebar.show {
    transform: translateX(0);
}

.sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.sidebar-nav {
    padding: 1rem 0;
}

.sidebar-nav .nav-link {
    display: block;
    padding: 0.75rem 1rem;
    color: #495057;
    text-decoration: none;
    transition: background-color 0.15s ease-in-out;
}

.sidebar-nav .nav-link:hover {
    background-color: #e9ecef;
}

.sidebar-nav .nav-link.active {
    background-color: #007bff;
    color: #fff;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    list-style: none;
    background-color: #e9ecef;
    border-radius: 0.375rem;
}

.breadcrumb-item {
    display: flex;
}

.breadcrumb-item + .breadcrumb-item {
    padding-left: 0.5rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    display: inline-block;
    padding-right: 0.5rem;
    color: #6c757d;
    content: '/';
}

.breadcrumb-item.active {
    color: #6c757d;
}

/* Pagination */
.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: 0.375rem;
}

.page-link {
    position: relative;
    display: block;
    padding: 0.5rem 0.75rem;
    margin-left: -1px;
    line-height: 1.25;
    color: #007bff;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #dee2e6;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.page-link:hover {
    z-index: 2;
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-link:focus {
    z-index: 3;
    color: #0056b3;
    background-color: #e9ecef;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.page-item:first-child .page-link {
    margin-left: 0;
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.page-item:last-child .page-link {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Tabs */
.nav-tabs {
    border-bottom: 1px solid #dee2e6;
}

.nav-tabs .nav-link {
    margin-bottom: -1px;
    background: none;
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.nav-tabs .nav-link:hover,
.nav-tabs .nav-link:focus {
    border-color: #e9ecef #e9ecef #dee2e6;
    isolation: isolate;
}

.nav-tabs .nav-link.disabled {
    color: #6c757d;
    background-color: transparent;
    border-color: transparent;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.tab-content {
    padding: 1rem 0;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}
";

        return [
            'css' => $css,
            'html_examples' => $this->generateNavigationHTMLExamples(),
        ];
    }

    private function generateModalComponents(): array
    {
        $css = "
/* Modern Modal Components */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1055;
    display: none;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
}

.modal.show {
    display: block;
}

.modal-dialog {
    position: relative;
    width: auto;
    margin: 0.5rem;
    pointer-events: none;
}

.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 0.375rem;
    outline: 0;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100vw;
    height: 100vh;
    background-color: #000;
}

.modal-backdrop.fade {
    opacity: 0;
}

.modal-backdrop.show {
    opacity: 0.5;
}

.modal-header {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1rem;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: calc(0.375rem - 1px);
    border-top-right-radius: calc(0.375rem - 1px);
}

.modal-title {
    margin-bottom: 0;
    line-height: 1.5;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
}

.modal-footer {
    display: flex;
    flex-wrap: wrap;
    flex-shrink: 0;
    align-items: center;
    justify-content: flex-end;
    padding: 0.75rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: calc(0.375rem - 1px);
    border-bottom-left-radius: calc(0.375rem - 1px);
}

.modal-footer > * {
    margin: 0.25rem;
}

/* Modal Sizes */
.modal-sm {
    max-width: 300px;
}

.modal-md {
    max-width: 500px;
}

.modal-lg {
    max-width: 800px;
}

.modal-xl {
    max-width: 1140px;
}

/* Responsive Modal */
@media (min-width: 576px) {
    .modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
}

.modal-sm {
    max-width: 300px;
}
}

@media (min-width: 992px) {
.modal-lg,
.modal-xl {
    max-width: 800px;
}
}

@media (min-width: 1200px) {
.modal-xl {
    max-width: 1140px;
}
}

/* Modal Animation */
@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal.show .modal-dialog {
    animation: modalFadeIn 0.3s ease-out;
}
";

        return [
            'css' => $css,
            'html_examples' => $this->generateModalHTMLExamples(),
        ];
    }

    private function generateAlertComponents(): array
    {
        $css = "
/* Modern Alert Components */
.alert {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.375rem;
}

.alert-heading {
    color: inherit;
}

.alert-link {
    font-weight: 700;
}

.alert-dismissible {
    padding-right: 3rem;
}

.alert-dismissible .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    z-index: 2;
    padding: 1.25rem 1rem;
}

/* Alert Variants */
.alert-primary {
    color: #084298;
    background-color: #cff4fc;
    border-color: #b6effb;
}

.alert-primary .alert-link {
    color: #06357a;
}

.alert-secondary {
    color: #41464b;
    background-color: #e2e3e5;
    border-color: #d3d6d8;
}

.alert-secondary .alert-link {
    color: #34383c;
}

.alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.alert-success .alert-link {
    color: #0c4128;
}

.alert-danger {
    color: #842029;
    background-color: #f8d7da;
    border-color: #f5c2c7;
}

.alert-danger .alert-link {
    color: #6a1a21;
}

.alert-warning {
    color: #664d03;
    background-color: #fff3cd;
    border-color: #ffecb5;
}

.alert-warning .alert-link {
    color: #523e02;
}

.alert-info {
    color: #055160;
    background-color: #cff4fc;
    border-color: #b6effb;
}

.alert-info .alert-link {
    color: #04414d;
}

.alert-light {
    color: #636464;
    background-color: #fefefe;
    border-color: #fdfdfe;
}

.alert-light .alert-link {
    color: #4f5050;
}

.alert-dark {
    color: #141619;
    background-color: #d3d3d4;
    border-color: #bcbebf;
}

.alert-dark .alert-link {
    color: #101214;
}

/* Close Button */
.btn-close {
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: 0.25em 0.25em;
    color: #000;
    background: transparent url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='m.5 3.5 3 3-3 3 1 1 3-3 3 3 1-1-3-3 3-3-1-1-3 3-3-3z'/%3e%3c/svg%3e\") center/1em auto no-repeat;
    border: 0;
    border-radius: 0.375rem;
    opacity: 0.5;
}

.btn-close:hover {
    color: #000;
    text-decoration: none;
    opacity: 0.75;
}

.btn-close:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    opacity: 1;
}

.btn-close:disabled,
.btn-close.disabled {
    pointer-events: none;
    user-select: none;
    opacity: 0.25;
}

/* Alert Animations */
@keyframes alertSlideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.alert {
    animation: alertSlideIn 0.3s ease-out;
}

/* Toast Alerts */
.alert-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    min-width: 300px;
    max-width: 500px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
";

        return [
            'css' => $css,
            'html_examples' => $this->generateAlertHTMLExamples(),
        ];
    }

    private function generateTableComponents(): array
    {
        $css = "
/* Modern Table Components */
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    vertical-align: top;
    border-color: #dee2e6;
}

.table > :not(caption) > * > * {
    padding: 0.5rem 0.5rem;
    background-color: var(--bs-table-bg);
    border-bottom-width: 1px;
}

.table > tbody {
    vertical-align: inherit;
}

.table > thead {
    vertical-align: bottom;
}

.table > :not(:first-child) {
    border-top: 2px solid currentColor;
}

/* Table Variants */
.table-striped > tbody > tr:nth-of-type(odd) > td,
.table-striped > tbody > tr:nth-of-type(odd) > th {
    --bs-table-accent-bg: rgba(0, 0, 0, 0.05);
    color: var(--bs-table-color);
}

.table-hover > tbody > tr:hover > td,
.table-hover > tbody > tr:hover > th {
    --bs-table-accent-bg: rgba(0, 0, 0, 0.075);
    color: var(--bs-table-color);
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered > :not(caption) > * {
    border-width: 0 1px;
}

.table-bordered > :not(caption) > * > * {
    border-width: 0 1px;
}

.table-borderless > :not(caption) > * > * {
    border-bottom-width: 0;
}

.table-borderless > :not(:first-child) {
    border-top-width: 0;
}

/* Table Sizes */
.table-sm > :not(caption) > * > * {
    padding: 0.25rem 0.25rem;
}

.table-lg > :not(caption) > * > * {
    padding: 0.75rem 0.75rem;
}

/* Responsive Table */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive-sm {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive-md {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive-lg {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive-xl {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Table Sorting */
.table-sortable th {
    cursor: pointer;
    user-select: none;
    position: relative;
}

.table-sortable th:hover {
    background-color: #f8f9fa;
}

.table-sortable th.sort-asc::after {
    content: '↑';
    position: absolute;
    right: 8px;
    color: #007bff;
}

.table-sortable th.sort-desc::after {
    content: '↓';
    position: absolute;
    right: 8px;
    color: #007bff;
}

/* Table Filtering */
.table-filter {
    margin-bottom: 1rem;
}

.table-filter input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

/* Table Pagination */
.table-pagination {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
}

/* Modern Table Styles */
.table-modern {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-modern thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}

.table-modern tbody tr {
    transition: background-color 0.15s ease-in-out;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
}

.table-modern tbody tr:last-child td {
    border-bottom: none;
}
";

        return [
            'css' => $css,
            'html_examples' => $this->generateTableHTMLExamples(),
        ];
    }

    private function generateLoaderComponents(): array
    {
        $css = "
/* Modern Loader Components */
.spinner {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    vertical-align: text-bottom;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border 0.75s linear infinite;
}

.spinner-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.125em;
}

.spinner-lg {
    width: 3rem;
    height: 3rem;
    border-width: 0.375em;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}

/* Skeleton Loader */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

.skeleton-text {
    height: 1rem;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
}

.skeleton-text:last-child {
    width: 60%;
}

.skeleton-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
}

.skeleton-button {
    height: 2.5rem;
    width: 6rem;
    border-radius: 0.375rem;
}

/* Progress Bar */
.progress {
    display: flex;
    height: 1rem;
    overflow: hidden;
    font-size: 0.75rem;
    background-color: #e9ecef;
    border-radius: 0.375rem;
}

.progress-bar {
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    background-color: #0d6efd;
    transition: width 0.6s ease;
}

.progress-bar-striped {
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-size: 1rem 1rem;
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% {
        background-position-x: 1rem;
    }
}

/* Pulse Loader */
.pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Loading States */
.loading {
    position: relative;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 1rem;
    height: 1rem;
    margin: -0.5rem 0 0 -0.5rem;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spinner-border 0.75s linear infinite;
}

/* Overlay Loader */
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loader-overlay .spinner {
    width: 3rem;
    height: 3rem;
    border-width: 0.375em;
    color: #007bff;
}

/* Button Loading State */
.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 1rem;
    height: 1rem;
    top: 50%;
    left: 50%;
    margin-left: -0.5rem;
    margin-top: -0.5rem;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spinner-border 1s linear infinite;
}

/* Card Loading State */
.card-loading {
    position: relative;
    overflow: hidden;
}

.card-loading::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: card-loading 1.5s infinite;
}

@keyframes card-loading {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}
";

        return [
            'css' => $css,
            'html_examples' => $this->generateLoaderHTMLExamples(),
        ];
    }

    private function generateButtonHTMLExamples(): array
    {
        return [
            'basic' => '<button class="btn btn-primary">Primary Button</button>',
            'variants' => '
                <button class="btn btn-primary">Primary</button>
                <button class="btn btn-secondary">Secondary</button>
                <button class="btn btn-success">Success</button>
                <button class="btn btn-danger">Danger</button>
            ',
            'sizes' => '
                <button class="btn btn-primary btn-sm">Small</button>
                <button class="btn btn-primary">Medium</button>
                <button class="btn btn-primary btn-lg">Large</button>
            ',
            'outline' => '<button class="btn btn-outline btn-primary">Outline Button</button>',
            'icon' => '<button class="btn btn-primary btn-icon"><i class="fas fa-heart"></i></button>',
            'loading' => '<button class="btn btn-primary btn-loading">Loading...</button>',
        ];
    }

    private function generateCardHTMLExamples(): array
    {
        return [
            'basic' => '
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Card Title</h5>
                        <p class="card-text">Card content goes here.</p>
                    </div>
                </div>
            ',
            'with_header_footer' => '
                <div class="card">
                    <div class="card-header">Card Header</div>
                    <div class="card-body">
                        <h5 class="card-title">Card Title</h5>
                        <p class="card-text">Card content goes here.</p>
                    </div>
                    <div class="card-footer">Card Footer</div>
                </div>
            ',
            'with_image' => '
                <div class="card">
                    <img src="image.jpg" class="card-img-top" alt="Card image">
                    <div class="card-body">
                        <h5 class="card-title">Card Title</h5>
                        <p class="card-text">Card content goes here.</p>
                    </div>
                </div>
            ',
        ];
    }

    private function generateFormHTMLExamples(): array
    {
        return [
            'basic_input' => '
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Enter email">
                </div>
            ',
            'floating_label' => '
                <div class="form-floating">
                    <input type="email" class="form-control" id="floatingEmail" placeholder="Email">
                    <label for="floatingEmail">Email</label>
                </div>
            ',
            'input_group' => '
                <div class="input-group">
                    <span class="input-group-text">@</span>
                    <input type="text" class="form-control" placeholder="Username">
                </div>
            ',
            'checkbox' => '
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check1">
                    <label class="form-check-label" for="check1">Checkbox label</label>
                </div>
            ',
        ];
    }

    private function generateNavigationHTMLExamples(): array
    {
        return [
            'navbar' => '
                <nav class="navbar">
                    <a class="navbar-brand" href="#">Brand</a>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">About</a>
                        </li>
                    </ul>
                </nav>
            ',
            'breadcrumb' => '
                <nav class="breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Home</a></div>
                    <div class="breadcrumb-item"><a href="#">Library</a></div>
                    <div class="breadcrumb-item active">Data</div>
                </nav>
            ',
            'pagination' => '
                <nav class="pagination">
                    <div class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </div>
                    <div class="page-item active">
                        <span class="page-link">1</span>
                    </div>
                    <div class="page-item">
                        <a class="page-link" href="#">2</a>
                    </div>
                    <div class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </div>
                </nav>
            ',
        ];
    }

    private function generateModalHTMLExamples(): array
    {
        return [
            'basic' => '
                <div class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Modal Title</h5>
                                <button type="button" class="btn-close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Modal body text goes here.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary">Close</button>
                                <button type="button" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            ',
        ];
    }

    private function generateAlertHTMLExamples(): array
    {
        return [
            'basic' => '<div class="alert alert-primary">Primary alert message</div>',
            'dismissible' => '
                <div class="alert alert-warning alert-dismissible">
                    <strong>Warning!</strong> This alert can be dismissed.
                    <button type="button" class="btn-close"></button>
                </div>
            ',
            'variants' => '
                <div class="alert alert-success">Success alert</div>
                <div class="alert alert-danger">Danger alert</div>
                <div class="alert alert-warning">Warning alert</div>
                <div class="alert alert-info">Info alert</div>
            ',
        ];
    }

    private function generateTableHTMLExamples(): array
    {
        return [
            'basic' => '
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>john@example.com</td>
                            <td>Admin</td>
                        </tr>
                    </tbody>
                </table>
            ',
            'striped' => '<table class="table table-striped">...</table>',
            'hover' => '<table class="table table-hover">...</table>',
            'bordered' => '<table class="table table-bordered">...</table>',
        ];
    }

    private function generateLoaderHTMLExamples(): array
    {
        return [
            'spinner' => '<div class="spinner"></div>',
            'skeleton' => '
                <div class="skeleton">
                    <div class="skeleton-avatar"></div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-button"></div>
                </div>
            ',
            'progress' => '
                <div class="progress">
                    <div class="progress-bar" style="width: 25%"></div>
                </div>
            ',
            'overlay' => '
                <div class="loader-overlay">
                    <div class="spinner"></div>
                </div>
            ',
        ];
    }

    public function getComponentStatistics(): array
    {
        return [
            'config' => $this->componentConfig,
            'total_components' => count($this->componentConfig),
            'component_types' => array_keys($this->componentConfig),
        ];
    }
}
