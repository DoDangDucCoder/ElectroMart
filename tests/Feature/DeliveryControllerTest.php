<?php

namespace Tests\Feature;

use App\Http\Controllers\DeliveryController;
use App\Http\Requests\Admin\Legacy\Delivery\UpdateDeliveryRequest;
use App\Models\Feeship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class DeliveryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function test_update_delivery_returns_clear_error_when_record_disappears_after_validation(): void
    {
        if (! Schema::hasTable('tbl_feeship')) {
            Schema::create('tbl_feeship', function (Blueprint $table) {
                $table->increments('fee_id');
                $table->integer('fee_matp');
                $table->integer('fee_maqh');
                $table->integer('fee_xaid');
                $table->bigInteger('fee_feeship')->default(0);
            });
        }

        Feeship::create([
            'fee_matp' => 1,
            'fee_maqh' => 10,
            'fee_xaid' => 100,
            'fee_feeship' => 30000,
        ]);

        $request = \Mockery::mock(UpdateDeliveryRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'feeship_id' => 999999,
            'fee_value' => '45000',
        ]);

        $response = app(DeliveryController::class)->update_delivery($request);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Không tìm thấy cấu hình phí vận chuyển cần cập nhật.', json_decode($response->getContent(), true)['message']);
    }
}
