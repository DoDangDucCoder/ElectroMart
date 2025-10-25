<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class XSSProtectionMiddleware
{
    private array $dangerousPatterns = [
        '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
        '/javascript:/i',
        '/on\w+\s*=/i',
        '/<iframe\b[^>]*>/i',
        '/<object\b[^>]*>/i',
        '/<embed\b[^>]*>/i',
        '/<link\b[^>]*>/i',
        '/<meta\b[^>]*>/i',
        '/<style\b[^>]*>/i',
        '/expression\s*\(/i',
        '/vbscript:/i',
        '/data:text\/html/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Check for XSS patterns in input data
        if ($this->containsXSSPatterns($request)) {
            Log::warning('XSS attack attempt detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $this->sanitizeLogData($request->all()),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Invalid input detected',
                    'message' => 'Your request contains potentially harmful content.'
                ], 400);
            }

            return redirect()->back()->withErrors([
                'security' => 'Invalid input detected. Please check your data and try again.'
            ]);
        }

        // Sanitize input data
        $this->sanitizeInput($request);

        return $next($request);
    }

    private function containsXSSPatterns(Request $request): bool
    {
        $input = $request->all();
        
        foreach ($input as $key => $value) {
            if (is_string($value) && $this->checkStringForXSS($value)) {
                return true;
            }
            
            if (is_array($value) && $this->checkArrayForXSS($value)) {
                return true;
            }
        }

        return false;
    }

    private function checkStringForXSS(string $value): bool
    {
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    private function checkArrayForXSS(array $array): bool
    {
        foreach ($array as $value) {
            if (is_string($value) && $this->checkStringForXSS($value)) {
                return true;
            }
            
            if (is_array($value) && $this->checkArrayForXSS($value)) {
                return true;
            }
        }

        return false;
    }

    private function sanitizeInput(Request $request): void
    {
        $input = $request->all();
        $sanitized = $this->sanitizeArray($input);
        
        $request->replace($sanitized);
    }

    private function sanitizeArray(array $array): array
    {
        $sanitized = [];
        
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    private function sanitizeString(string $value): string
    {
        // Remove null bytes
        $value = str_replace(chr(0), '', $value);
        
        // Convert special characters to HTML entities
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $value;
    }

    private function sanitizeLogData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = substr($value, 0, 100) . (strlen($value) > 100 ? '...' : '');
            } elseif (is_array($value)) {
                $sanitized[$key] = '[Array]';
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
