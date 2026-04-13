<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class DatabaseRuntime
{
    public static function activateLocalFallbacksIfNeeded(): bool
    {
        if (! app()->environment('local') || self::unavailable()) {
            return self::unavailable();
        }

        try {
            DB::connection(config('database.default'))->getPdo();

            return false;
        } catch (Throwable $e) {
            if (! self::causedByConnectionFailure($e)) {
                return false;
            }

            self::markUnavailable();

            if (config('session.driver') === 'database') {
                config(['session.driver' => 'file']);
            }

            if (config('cache.default') === 'database') {
                config(['cache.default' => 'file']);
            }

            if (config('queue.default') === 'database') {
                config(['queue.default' => 'sync']);
            }

            return true;
        }
    }

    public static function unavailable(): bool
    {
        return (bool) config('app.runtime.database_unavailable', false);
    }

    public static function markUnavailable(?string $message = null): void
    {
        config([
            'app.runtime.database_unavailable' => true,
            'app.runtime.database_unavailable_message' => $message ?: self::defaultUnavailableMessage(),
        ]);
    }

    public static function unavailableMessage(): string
    {
        return (string) config('app.runtime.database_unavailable_message', self::defaultUnavailableMessage());
    }

    public static function causedByConnectionFailure(Throwable $e): bool
    {
        $keywords = [
            'sqlstate[hy000] [2002]',
            'actively refused it',
            'connection refused',
            'server has gone away',
            'php_network_getaddresses',
            'getaddrinfo',
            'no such host is known',
            'could not find driver',
            'unable to open database file',
            'access denied for user',
        ];

        for ($exception = $e; $exception !== null; $exception = $exception->getPrevious()) {
            $message = Str::lower($exception->getMessage());

            if (Str::contains($message, $keywords)) {
                return true;
            }
        }

        return false;
    }

    protected static function defaultUnavailableMessage(): string
    {
        return 'Không thể kết nối tới cơ sở dữ liệu. Hãy kiểm tra MySQL và cấu hình DB trong file .env rồi tải lại trang.';
    }
}
