<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class XSSProtectionMiddleware
{
    private array $trustedHtmlFields = [
        'info_fanpage',
        'info_map',
    ];

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
                    'error' => 'Phát hiện dữ liệu không hợp lệ',
                    'message' => 'Yêu cầu của bạn chứa nội dung có nguy cơ gây hại.',
                ], 400);
            }

            return redirect()->back()->withErrors([
                'security' => 'Phát hiện dữ liệu không hợp lệ. Vui lòng kiểm tra lại và thử tiếp.',
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
            if (is_string($value) && $this->checkStringForXSS($value, (string) $key)) {
                return true;
            }

            if (is_array($value) && $this->checkArrayForXSS($value, (string) $key)) {
                return true;
            }
        }

        return false;
    }

    private function checkStringForXSS(string $value, ?string $key = null): bool
    {
        foreach ($this->dangerousPatterns as $pattern) {
            if ($this->isTrustedHtmlField($key) && str_contains($pattern, '<iframe')) {
                continue;
            }

            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    private function checkArrayForXSS(array $array, ?string $parentKey = null): bool
    {
        foreach ($array as $key => $value) {
            $fieldKey = is_string($key) ? $key : $parentKey;

            if (is_string($value) && $this->checkStringForXSS($value, $fieldKey)) {
                return true;
            }

            if (is_array($value) && $this->checkArrayForXSS($value, $fieldKey)) {
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
                $sanitized[$key] = $this->isTrustedHtmlField((string) $key)
                    ? str_replace(chr(0), '', $value)
                    : $this->sanitizeString($value);
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

    private function isTrustedHtmlField(?string $key): bool
    {
        return $key !== null && in_array($key, $this->trustedHtmlFields, true);
    }

    private function sanitizeLogData(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = substr($value, 0, 100).(strlen($value) > 100 ? '...' : '');
            } elseif (is_array($value)) {
                $sanitized[$key] = '[Array]';
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
