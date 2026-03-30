<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\AdminPolicy;
use App\Policies\APIPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\FilePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PolicyDefaultsTest extends TestCase
{
    #[DataProvider('policyProvider')]
    public function test_placeholder_policies_deny_by_default(string $policyClass): void
    {
        $policy = new $policyClass();
        $user = User::factory()->make();
        $model = new \stdClass();

        $this->assertFalse($policy->viewAny($user));
        $this->assertFalse($policy->view($user, $model));
        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->update($user, $model));
        $this->assertFalse($policy->delete($user, $model));
    }

    public static function policyProvider(): array
    {
        return [
            [AdminPolicy::class],
            [APIPolicy::class],
            [CustomerPolicy::class],
            [FilePolicy::class],
            [OrderPolicy::class],
            [PaymentPolicy::class],
            [ProductPolicy::class],
            [UserPolicy::class],
        ];
    }
}
