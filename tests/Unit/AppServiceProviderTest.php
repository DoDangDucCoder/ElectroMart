<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_http_client_response_supports_from_psr_response_macro(): void
    {
        $response = HttpClientResponse::fromPsrResponse(
            new PsrResponse(202, ['X-Test' => 'yes'], 'compatibility-ok')
        );

        $this->assertInstanceOf(HttpClientResponse::class, $response);
        $this->assertSame(202, $response->status());
        $this->assertSame('compatibility-ok', $response->body());
        $this->assertSame('yes', $response->header('X-Test'));
    }

    public function test_tinker_runtime_paths_are_redirected_to_storage_framework(): void
    {
        $provider = new AppServiceProvider($this->app);
        $method = new \ReflectionMethod($provider, 'configureConsoleRuntimeEnvironment');
        $method->setAccessible(true);

        $originalArgv = $_SERVER['argv'] ?? null;
        $originalEnv = [];

        foreach (['APPDATA', 'LOCALAPPDATA', 'XDG_CONFIG_HOME', 'XDG_DATA_HOME'] as $key) {
            $originalEnv[$key] = getenv($key);
        }

        try {
            $_SERVER['argv'] = ['artisan', 'tinker'];

            $method->invoke($provider);

            $expectedPath = storage_path('framework');

            $this->assertSame($expectedPath, getenv('APPDATA'));
            $this->assertSame($expectedPath, getenv('LOCALAPPDATA'));
            $this->assertSame($expectedPath, getenv('XDG_CONFIG_HOME'));
            $this->assertSame($expectedPath, getenv('XDG_DATA_HOME'));
        } finally {
            if ($originalArgv === null) {
                unset($_SERVER['argv']);
            } else {
                $_SERVER['argv'] = $originalArgv;
            }

            foreach ($originalEnv as $key => $value) {
                if ($value === false) {
                    putenv($key);
                    unset($_ENV[$key], $_SERVER[$key]);
                } else {
                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}
