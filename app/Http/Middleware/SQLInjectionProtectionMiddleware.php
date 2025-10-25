<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SQLInjectionProtectionMiddleware
{
    private array $sqlPatterns = [
        '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i',
        '/(\b(OR|AND)\s+\d+\s*=\s*\d+)/i',
        '/(\b(OR|AND)\s+[\'"]\w+[\'"]\s*=\s*[\'"]\w+[\'"])/i',
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\b(SELECT|INSERT|UPDATE|DELETE)\s+.*\bFROM\b)/i',
        '/(\bDROP\s+\b(TABLE|DATABASE|INDEX)\b)/i',
        '/(\bCREATE\s+\b(TABLE|DATABASE|INDEX)\b)/i',
        '/(\bALTER\s+\b(TABLE|DATABASE)\b)/i',
        '/(\bEXEC\s*\()/i',
        '/(\bSCRIPT\b)/i',
        '/(\b(WAITFOR|DELAY)\s+\d+)/i',
        '/(\b(BENCHMARK|SLEEP)\s*\()/i',
        '/(\b(LOAD_FILE|INTO\s+OUTFILE)\s*\()/i',
        '/(\b(INFORMATION_SCHEMA|SYSOBJECTS|SYSUSERS)\b)/i',
        '/(\b(CHAR|ASCII|SUBSTRING|LENGTH)\s*\()/i',
        '/(\b(CONCAT|GROUP_CONCAT)\s*\()/i',
        '/(\b(HAVING|ORDER\s+BY)\s+\d+)/i',
        '/(\b(ROWNUM|LIMIT)\s+\d+)/i',
        '/(\b(CAST|CONVERT)\s*\()/i',
        '/(\b(ISNULL|IFNULL|COALESCE)\s*\()/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->containsSQLInjection($request)) {
            Log::critical('SQL injection attempt detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $this->sanitizeLogData($request->all()),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Invalid request detected',
                    'message' => 'Your request contains potentially harmful content.'
                ], 400);
            }

            return redirect()->back()->withErrors([
                'security' => 'Invalid request detected. Please check your input and try again.'
            ]);
        }

        return $next($request);
    }

    private function containsSQLInjection(Request $request): bool
    {
        $input = $request->all();
        
        foreach ($input as $key => $value) {
            if (is_string($value) && $this->checkStringForSQLInjection($value)) {
                return true;
            }
            
            if (is_array($value) && $this->checkArrayForSQLInjection($value)) {
                return true;
            }
        }

        return false;
    }

    private function checkStringForSQLInjection(string $value): bool
    {
        foreach ($this->sqlPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }

    private function checkArrayForSQLInjection(array $array): bool
    {
        foreach ($array as $value) {
            if (is_string($value) && $this->checkStringForSQLInjection($value)) {
                return true;
            }
            
            if (is_array($value) && $this->checkArrayForSQLInjection($value)) {
                return true;
            }
        }
        return false;
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
