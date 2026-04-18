<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\NumericInputNormalizer;
use App\Http\Requests\Admin\Legacy\Delivery\SelectDeliveryRequest;
use App\Http\Requests\Admin\Legacy\Delivery\StoreDeliveryRequest;
use App\Http\Requests\Admin\Legacy\Delivery\UpdateDeliveryRequest;
use App\Models\City;
use App\Models\Province;
use App\Models\Wards;
use App\Models\Feeship;
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
		if (! Schema::hasTable((new Feeship())->getTable())) {
			return response()->noContent();
		}

		$data = $request->validated();

		$fee_ship = Feeship::find($data['feeship_id']);
		if (! $fee_ship) {
			return response()->json(['message' => 'Không tìm thấy cấu hình phí vận chuyển cần cập nhật.'], 404);
		}

		$fee_value = $this->numericInputNormalizer->digitsOnly($data['fee_value']);
		$fee_ship->fee_feeship = $fee_value;
		$fee_ship->save();

		return response()->noContent();
	}
	public function select_feeship()
	{
		if (! Schema::hasTable((new Feeship())->getTable())) {
			return response('<div class="g-admin-empty-state">Chưa có cấu hình phí vận chuyển nào trên môi trường này.</div>');
		}

		$feeship = Feeship::orderby('fee_id', 'DESC')->get()->map(function (Feeship $fee) {
			$fee->city_name = $this->resolveLocationName(
				Schema::hasTable((new City())->getTable()),
				City::class,
				'matp',
				$fee->fee_matp,
				'name_city'
			);
			$fee->province_name = $this->resolveLocationName(
				Schema::hasTable((new Province())->getTable()),
				Province::class,
				'maqh',
				$fee->fee_maqh,
				'name_quanhuyen'
			);
			$fee->ward_name = $this->resolveLocationName(
				Schema::hasTable((new Wards())->getTable()),
				Wards::class,
				'xaid',
				$fee->fee_xaid,
				'name_xaphuong'
			);

			return $fee;
		});

		return response()->view('admin.delivery.partials.feeship_table', compact('feeship'));
	}
	public function insert_delivery(StoreDeliveryRequest $request)
	{
		if (! Schema::hasTable((new Feeship())->getTable())) {
			return response()->noContent();
		}

		$data = $request->validated();

		$fee_ship = Feeship::firstOrNew([
			'fee_matp' => $data['city'],
			'fee_maqh' => $data['province'],
			'fee_xaid' => $data['wards'],
		]);
		$fee_ship->fee_feeship = $this->numericInputNormalizer->digitsOnly($data['fee_ship']);
		$fee_ship->save();

		return response()->noContent();
	}
	public function delivery()
	{
		$city = Schema::hasTable((new City())->getTable())
			? City::orderby('matp', 'ASC')->get()
			: collect();

		return view('admin.delivery.add_delivery')->with(compact('city'));
	}
	public function select_delivery(SelectDeliveryRequest $request)
	{
		$data = $request->validated();

		if ($data['action'] === 'city') {
			if (! Schema::hasTable((new Province())->getTable())) {
				return response()->view('admin.delivery.partials.select_options', [
					'placeholder' => '---Không có dữ liệu quận huyện---',
					'options' => collect(),
					'valueField' => 'maqh',
					'labelField' => 'name_quanhuyen',
				]);
			}

			return response()->view('admin.delivery.partials.select_options', [
				'placeholder' => '---Chọn quận huyện---',
				'options' => Province::where('matp', $data['ma_id'])->orderby('maqh', 'ASC')->get(),
				'valueField' => 'maqh',
				'labelField' => 'name_quanhuyen',
			]);
		}

		if (! Schema::hasTable((new Wards())->getTable())) {
			return response()->view('admin.delivery.partials.select_options', [
				'placeholder' => '---Không có dữ liệu xã phường---',
				'options' => collect(),
				'valueField' => 'xaid',
				'labelField' => 'name_xaphuong',
			]);
		}

		return response()->view('admin.delivery.partials.select_options', [
			'placeholder' => '---Chọn xã phường---',
			'options' => Wards::where('maqh', $data['ma_id'])->orderby('xaid', 'ASC')->get(),
			'valueField' => 'xaid',
			'labelField' => 'name_xaphuong',
		]);
	}

	private function resolveLocationName(
		bool $tableExists,
		string $modelClass,
		string $keyColumn,
		int|string|null $keyValue,
		string $labelColumn
	): ?string {
		if (! $tableExists || $keyValue === null) {
			return null;
		}

		return $modelClass::query()
			->where($keyColumn, $keyValue)
			->value($labelColumn);
	}
}
