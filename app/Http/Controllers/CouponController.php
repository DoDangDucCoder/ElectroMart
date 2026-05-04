<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Coupon\StoreCouponRequest;
use App\Http\Requests\Admin\Legacy\Coupon\UpdateCouponRequest;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon as MarketingCoupon;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware('permission:add coupon', ['only' => ['insert_coupon_code', 'insert_coupon']]);

    //     $this->middleware('permission:delete coupon', ['only' => ['delete_coupon']]);
    //     $this->middleware('permission:list coupon', ['only' => ['list_coupon']]);
    // }
    public function unset_coupon()
    {
        $coupon = Session::get('coupon');
        if ($coupon == true) {

            Session::forget('coupon');
            return redirect()->back()->with('message', 'Xóa mã khuyến mãi thành công');
        }

        return redirect()->back()->with('error', 'Không có mã khuyến mãi nào để xóa.');
    }
    public function insert_coupon()
    {
        return view('admin.coupon.insert_coupon');
    }

    public function edit_coupon($coupon_id)
    {
        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            return Redirect::to('list-coupon')->with('error', 'Chưa có bảng mã giảm giá trên môi trường này');
        }

        $coupon = MarketingCoupon::find($coupon_id);
        if (! $coupon) {
            return Redirect::to('list-coupon')->with('error', 'Không tìm thấy mã giảm giá cần sửa.');
        }

        return view('admin.coupon.edit_coupon', [
            'coupon' => $coupon,
            'legacyCoupon' => $this->legacyCouponRecord($coupon),
        ]);
    }

    public function update_coupon(UpdateCouponRequest $request, $coupon_id)
    {
        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            return Redirect::to('list-coupon')->with('error', 'Chưa có bảng mã giảm giá trên môi trường này');
        }

        $coupon = MarketingCoupon::find($coupon_id);
        if (! $coupon) {
            return Redirect::to('list-coupon')->with('error', 'Không tìm thấy mã giảm giá cần cập nhật.');
        }

        $coupon->fill($this->couponPayloadFromRequest($request->validated()));
        $coupon->save();

        Session::put('message', 'Cập nhật mã giảm giá thành công');

        return Redirect::to('list-coupon');
    }

    public function delete_coupon($coupon_id)
    {
        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            return Redirect::to('list-coupon')->with('error', 'Chưa có bảng mã giảm giá trên môi trường này');
        }

        $coupon = MarketingCoupon::find($coupon_id);
        if (! $coupon) {
            return Redirect::to('list-coupon')->with('error', 'Không tìm thấy mã giảm giá cần xóa.');
        }

        $coupon->delete();

        Session::put('message', 'Xóa mã giảm giá thành công');

        return Redirect::to('list-coupon');
    }
    public function list_coupon()
    {
        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');
        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            $coupon = new LengthAwarePaginator([], 0, 5, 1, [
                'path' => request()->url(),
            ]);

            return view('admin.coupon.list_coupon')->with(compact('coupon', 'today'));
        }

        $coupon = MarketingCoupon::query()
            ->orderByDesc('id')
            ->paginate(5)
            ->through(fn (MarketingCoupon $coupon): object => $this->legacyCouponRecord($coupon));

        return view('admin.coupon.list_coupon')->with(compact('coupon', 'today'));
    }
    public function insert_coupon_code(StoreCouponRequest $request)
    {
        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            return Redirect::to('insert-coupon')->with('error', 'Chưa có bảng mã giảm giá trên môi trường này');
        }

        $data = $request->validated();
        $code = trim((string) $data['coupon_code']);

        if (MarketingCoupon::query()->where('code', $code)->exists()) {
            return Redirect::to('insert-coupon')
                ->withInput()
                ->with('error', 'Mã giảm giá này đã tồn tại.');
        }

        MarketingCoupon::create($this->couponPayloadFromRequest($data, ['is_active' => true]));

        Session::put('message', 'Thêm mã giảm giá thành công');
        return Redirect::to('insert-coupon');
    }

    private function couponPayloadFromRequest(array $data, array $defaults = []): array
    {
        return array_merge([
            'name' => $data['coupon_name'],
            'code' => trim((string) $data['coupon_code']),
            'type' => $this->modernCouponType((int) $data['coupon_condition']),
            'value' => (float) $data['coupon_number'],
            'min_order_total' => $this->nullableFloat($data['min_order_total'] ?? null),
            'max_discount' => $this->nullableFloat($data['max_discount'] ?? null),
            'usage_limit' => (int) $data['coupon_time'],
            'usage_per_customer' => $this->nullableInt($data['usage_per_customer'] ?? null),
            'starts_at' => Carbon::createFromFormat('d/m/Y', $data['coupon_date_start'])->startOfDay(),
            'expires_at' => Carbon::createFromFormat('d/m/Y', $data['coupon_date_end'])->endOfDay(),
            'is_stackable' => (bool) ($data['is_stackable'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
            'meta' => [
                'source' => 'admin_coupon_form',
            ],
        ], $defaults);
    }

    private function legacyCouponRecord(MarketingCoupon $coupon): object
    {
        return (object) [
            'coupon_id' => $coupon->id,
            'coupon_name' => $coupon->name,
            'coupon_date_start' => $coupon->starts_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? '',
            'coupon_date_end' => $coupon->expires_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? '',
            'coupon_number' => (float) $coupon->value,
            'coupon_code' => $coupon->code,
            'coupon_time' => (int) ($coupon->usage_limit ?? 0),
            'coupon_condition' => $coupon->type === 'percent' ? 1 : 2,
            'coupon_status' => $coupon->is_active ? 1 : 0,
        ];
    }

    private function modernCouponType(int $condition): string
    {
        return $condition === 1 ? 'percent' : 'fixed';
    }

    private function nullableFloat(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }
}
