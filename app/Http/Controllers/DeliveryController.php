<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\NumericInputNormalizer;
use App\Http\Requests\Admin\Legacy\Delivery\SelectDeliveryRequest;
use App\Http\Requests\Admin\Legacy\Delivery\StoreDeliveryRequest;
use App\Http\Requests\Admin\Legacy\Delivery\UpdateDeliveryRequest;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamProvince;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamWard;
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
			->with(['city', 'province', 'ward', 'vietnamProvince', 'vietnamWard'])
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
			'vietnam_province_code' => $data['province_code'] ?? null,
			'vietnam_ward_code' => $data['ward_code'] ?? null,
		]);
		$fee_ship->city_id = null;
		$fee_ship->province_id = null;
		$fee_ship->ward_id = null;
		$fee_ship->amount = $this->numericInputNormalizer->digitsOnly($data['fee_ship']);
		$fee_ship->currency = 'VND';
		$fee_ship->save();

		return response()->noContent();
	}
	public function delivery()
	{
		$provinces = $this->vietnamProvincesTableExists()
			? VietnamProvince::query()
				->orderBy('name')
				->get()
				->map(fn (VietnamProvince $province) => $this->mapVietnamProvinceToOptionShape($province))
			: collect();

		return view('admin.delivery.add_delivery')->with(compact('provinces'));
	}
	public function select_delivery(SelectDeliveryRequest $request)
	{
		$data = $request->validated();

		if (empty($data['ma_id'])) {
			return response()->view('admin.delivery.partials.select_options', [
				'placeholder' => '---Để trống nếu dùng phí theo tỉnh hoặc mặc định---',
				'options' => collect(),
				'valueField' => 'code',
				'labelField' => 'name',
			]);
		}

		if (! $this->vietnamWardsTableExists()) {
			return response()->view('admin.delivery.partials.select_options', [
				'placeholder' => '---Không có dữ liệu xã/phường/đặc khu---',
				'options' => collect(),
				'valueField' => 'code',
				'labelField' => 'name',
			]);
		}

		return response()->view('admin.delivery.partials.select_options', [
			'placeholder' => '---Chọn xã/phường/đặc khu---',
			'options' => VietnamWard::query()
				->where('province_code', $data['ma_id'])
				->orderBy('name')
				->get()
				->map(fn (VietnamWard $ward) => $this->mapVietnamWardToOptionShape($ward)),
			'valueField' => 'code',
			'labelField' => 'name',
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

	private function vietnamProvincesTableExists(): bool
	{
		return Schema::hasTable((new VietnamProvince())->getTable());
	}

	private function vietnamWardsTableExists(): bool
	{
		return Schema::hasTable((new VietnamWard())->getTable());
	}

	private function mapShippingFeeToLegacyShape(ShippingFee $fee): object
	{
		$vietnamProvinceCode = trim((string) $fee->vietnam_province_code);
		$vietnamWardCode = trim((string) $fee->vietnam_ward_code);
		$scopeLabel = 'Mặc định toàn quốc';

		if ($vietnamProvinceCode !== '' && $vietnamWardCode !== '') {
			$scopeLabel = 'Theo xã/phường';
		} elseif ($vietnamProvinceCode !== '') {
			$scopeLabel = 'Theo tỉnh/thành phố';
		}

		return (object) [
			'fee_id' => $fee->id,
			'fee_matp' => $fee->city_id,
			'fee_maqh' => $fee->province_id,
			'fee_xaid' => $fee->ward_id,
			'fee_feeship' => (float) $fee->amount,
			'city_name' => $fee->vietnamProvince?->name ?? $fee->city?->name,
			'province_name' => $fee->province?->name,
			'ward_name' => $fee->vietnamWard?->name ?? $fee->ward?->name,
			'scope_label' => $scopeLabel,
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

	private function mapVietnamProvinceToOptionShape(VietnamProvince $province): object
	{
		return (object) [
			'code' => $province->code,
			'name' => $province->name,
		];
	}

	private function mapVietnamWardToOptionShape(VietnamWard $ward): object
	{
		return (object) [
			'code' => $ward->code,
			'name' => $ward->name,
		];
	}
}
