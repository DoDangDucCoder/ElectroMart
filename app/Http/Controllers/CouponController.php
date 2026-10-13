<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Coupon\StoreCouponRequest;
use App\Http\Requests\Admin\Legacy\Coupon\UpdateCouponRequest;
use App\Application\Checkout\Services\CouponService;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon as MarketingCoupon;
use App\Support\CsvExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponService $couponService,
    ) {
    }
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

            $this->couponService->clear(true, 'customer_unset');
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
    public function list_coupon(Request $request)
    {
        $today = Carbon::now('Asia/Ho_Chi_Minh');
        $filters = $this->couponFilters($request);

        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            $coupon = new LengthAwarePaginator([], 0, $filters['per_page'], 1, [
                'path' => request()->url(),
            ]);
            $stats = ['total' => 0, 'active' => 0, 'valid' => 0, 'expired' => 0];

            return view('admin.coupon.list_coupon')->with(compact('coupon', 'today', 'filters', 'stats'));
        }

        $baseQuery = MarketingCoupon::query();
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('is_active', true)->count(),
            'valid' => (clone $baseQuery)->whereDate('expires_at', '>=', $today->toDateString())->count(),
                'expired' => (clone $baseQuery)->whereDate('expires_at', '<', $today->toDateString())->count(),
        ];

        $couponQuery = $this->couponQuery($filters, $today);

        $coupon = $couponQuery
            ->paginate($filters['per_page'])
            ->appends($request->query())
            ->through(fn (MarketingCoupon $coupon): object => $this->legacyCouponRecord($coupon));

        return view('admin.coupon.list_coupon')->with(compact('coupon', 'today', 'filters', 'stats'));
    }

    public function export_coupon(Request $request): StreamedResponse
    {
        $today = Carbon::now('Asia/Ho_Chi_Minh');

        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            return CsvExport::download('ma-giam-gia-' . now()->format('Ymd-His') . '.csv', [
                'Tên mã',
                'Mã giảm giá',
                'Loại giảm',
                'Giá trị',
                'Số lượt',
                'Bắt đầu',
                'Kết thúc',
                'Trạng thái',
                'Hạn dùng',
            ], []);
        }

        $rows = $this->couponQuery($this->couponFilters($request), $today)
            ->limit(5000)
            ->get()
            ->map(static function (MarketingCoupon $coupon) use ($today): array {
                return [
                    $coupon->name,
                    $coupon->code,
                    $coupon->type === 'percent' ? 'Giảm theo %' : 'Giảm theo tiền',
                    (float) $coupon->value,
                    (int) ($coupon->usage_limit ?? 0),
                    $coupon->starts_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? '',
                    $coupon->expires_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? '',
                    $coupon->is_active ? 'Đang kích hoạt' : 'Đã khóa',
                    $coupon->expires_at && $coupon->expires_at->timezone('Asia/Ho_Chi_Minh')->startOfDay()->gte($today)
                        ? 'Còn hạn'
                        : 'Đã hết hạn',
                ];
            });

        return CsvExport::download('ma-giam-gia-' . now()->format('Ymd-His') . '.csv', [
            'Tên mã',
            'Mã giảm giá',
            'Loại giảm',
            'Giá trị',
            'Số lượt',
            'Bắt đầu',
            'Kết thúc',
            'Trạng thái',
            'Hạn dùng',
        ], $rows);
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

    private function couponFilters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'status' => (string) $request->query('status', 'all'),
            'type' => (string) $request->query('type', 'all'),
            'validity' => (string) $request->query('validity', 'all'),
            'sort' => (string) $request->query('sort', 'newest'),
            'per_page' => max(1, min((int) $request->integer('per_page', 10), 50)),
        ];
    }

    private function couponQuery(array $filters, Carbon $today)
    {
        $couponQuery = MarketingCoupon::query()
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($nested) use ($filters) {
                    $nested->where('name', 'like', '%'.$filters['q'].'%')
                        ->orWhere('code', 'like', '%'.$filters['q'].'%');
                });
            })
            ->when($filters['status'] !== 'all', function ($query) use ($filters) {
                $query->where('is_active', $filters['status'] === 'active');
            })
            ->when($filters['type'] !== 'all', fn ($query) => $query->where('type', $filters['type']))
            ->when($filters['validity'] === 'valid', fn ($query) => $query->whereDate('expires_at', '>=', $today->toDateString()))
            ->when($filters['validity'] === 'expired', fn ($query) => $query->whereDate('expires_at', '<', $today->toDateString()));

        match ($filters['sort']) {
            'oldest' => $couponQuery->orderBy('id'),
            'expiry_asc' => $couponQuery->orderBy('expires_at')->orderByDesc('id'),
            'expiry_desc' => $couponQuery->orderByDesc('expires_at')->orderByDesc('id'),
            'usage_asc' => $couponQuery->orderBy('usage_limit')->orderByDesc('id'),
            'usage_desc' => $couponQuery->orderByDesc('usage_limit')->orderByDesc('id'),
            'value_asc' => $couponQuery->orderByRaw('CAST(value AS DECIMAL(10,2)) ASC')->orderByDesc('id'),
            'value_desc' => $couponQuery->orderByRaw('CAST(value AS DECIMAL(10,2)) DESC')->orderByDesc('id'),
            default => $couponQuery->orderByDesc('id'),
        };

        return $couponQuery;
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
