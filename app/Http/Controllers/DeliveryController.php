<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\NumericInputNormalizer;
use App\Http\Requests\Admin\Legacy\Delivery\SelectDeliveryRequest;
use App\Http\Requests\Admin\Legacy\Delivery\StoreDeliveryRequest;
use App\Http\Requests\Admin\Legacy\Delivery\UpdateDeliveryRequest;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;
use Illuminate\Support\Facades\Schema;

class DeliveryController extends Controller
{
	public function __construct(
		private readonly NumericInputNormalizer $numericInputNormalizer,
	) {
	}

	// public function __construct()
	// {
	// 	$this->middleware('permission:add delivery', ['only' => ['delivery']]);
	// }
	public function update_delivery(UpdateDeliveryRequest $request)
	{
		if (! $this->shippingFeesTableExists()) {
			return response()->noContent();
		}

		$data = $request->validated();

		$fee_ship = ShippingFee::find($data['feeship_id']);
		if (! $fee_ship) {
			return response()->json(['message' => 'Không tìm thấy cấu hình phí vận chuyển cần cập nhật.'], 404);
		}

		$fee_value = $this->numericInputNormalizer->digitsOnly($data['fee_value']);
		$fee_ship->amount = $fee_value;
		$fee_ship->save();

		return response()->noContent();
	}
	public function select_feeship()
	{
		if (! $this->shippingFeesTableExists()) {
			return response('<div class="g-admin-empty-state">Chưa có cấu hình phí vận chuyển nào trên môi trường này.</div>');
		}

		$feeship = ShippingFee::query()
			->with(['city', 'province', 'ward'])
			->orderByDesc('id')
			->get()
			->map(fn (ShippingFee $fee) => $this->mapShippingFeeToLegacyShape($fee));

		return response()->view('admin.delivery.partials.feeship_table', compact('feeship'));
	}
	public function insert_delivery(StoreDeliveryRequest $request)
	{
		if (! $this->shippingFeesTableExists()) {
			return response()->noContent();
		}

		$data = $request->validated();

		$fee_ship = ShippingFee::firstOrNew([
			'city_id' => $data['city'],
			'province_id' => $data['province'],
			'ward_id' => $data['wards'],
		]);
		$fee_ship->amount = $this->numericInputNormalizer->digitsOnly($data['fee_ship']);
		$fee_ship->currency = 'VND';
		$fee_ship->save();

		return response()->noContent();
	}
	public function delivery()
	{
		$city = $this->locationsTableExists()
			? Location::query()
				->where('type', 'city')
				->where('is_active', true)
				->orderBy('display_order')
				->orderBy('id')
				->get()
				->map(fn (Location $location) => $this->mapLocationToLegacyCityShape($location))
			: collect();

		return view('admin.delivery.add_delivery')->with(compact('city'));
	}
	public function select_delivery(SelectDeliveryRequest $request)
	{
		$data = $request->validated();

		if ($data['action'] === 'city') {
			if (! $this->locationsTableExists()) {
				return response()->view('admin.delivery.partials.select_options', [
					'placeholder' => '---Không có dữ liệu quận huyện---',
					'options' => collect(),
					'valueField' => 'maqh',
					'labelField' => 'name_quanhuyen',
				]);
			}

			return response()->view('admin.delivery.partials.select_options', [
				'placeholder' => '---Chọn quận huyện---',
				'options' => Location::query()
					->where('type', 'province')
					->where('is_active', true)
					->where('parent_id', $data['ma_id'])
					->orderBy('display_order')
					->orderBy('id')
					->get()
					->map(fn (Location $location) => $this->mapLocationToLegacyProvinceShape($location)),
				'valueField' => 'maqh',
				'labelField' => 'name_quanhuyen',
			]);
		}

		if (! $this->locationsTableExists()) {
			return response()->view('admin.delivery.partials.select_options', [
				'placeholder' => '---Không có dữ liệu xã phường---',
				'options' => collect(),
				'valueField' => 'xaid',
				'labelField' => 'name_xaphuong',
			]);
		}

		return response()->view('admin.delivery.partials.select_options', [
			'placeholder' => '---Chọn xã phường---',
			'options' => Location::query()
				->where('type', 'ward')
				->where('is_active', true)
				->where('parent_id', $data['ma_id'])
				->orderBy('display_order')
				->orderBy('id')
				->get()
				->map(fn (Location $location) => $this->mapLocationToLegacyWardShape($location)),
			'valueField' => 'xaid',
			'labelField' => 'name_xaphuong',
		]);
	}

	private function locationsTableExists(): bool
	{
		return Schema::hasTable((new Location())->getTable());
	}

	private function shippingFeesTableExists(): bool
	{
		return Schema::hasTable((new ShippingFee())->getTable());
	}

	private function mapShippingFeeToLegacyShape(ShippingFee $fee): object
	{
		return (object) [
			'fee_id' => $fee->id,
			'fee_matp' => $fee->city_id,
			'fee_maqh' => $fee->province_id,
			'fee_xaid' => $fee->ward_id,
			'fee_feeship' => (float) $fee->amount,
			'city_name' => $fee->city?->name,
			'province_name' => $fee->province?->name,
			'ward_name' => $fee->ward?->name,
		];
	}

	private function mapLocationToLegacyCityShape(Location $location): object
	{
		return (object) [
			'matp' => $location->id,
			'name_city' => $location->name,
		];
	}

	private function mapLocationToLegacyProvinceShape(Location $location): object
	{
		return (object) [
			'maqh' => $location->id,
			'name_quanhuyen' => $location->name,
		];
	}

	private function mapLocationToLegacyWardShape(Location $location): object
	{
		return (object) [
			'xaid' => $location->id,
			'name_xaphuong' => $location->name,
		];
	}
}
