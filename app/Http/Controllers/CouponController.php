<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Coupon\StoreCouponRequest;
use App\Models\Coupon;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

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
    }
    public function insert_coupon()
    {
        return view('admin.coupon.insert_coupon');
    }
    public function delete_coupon($coupon_id)
    {
        if (! Schema::hasTable((new Coupon())->getTable())) {
            return Redirect::to('list-coupon')->with('message', 'Chưa có bảng mã giảm giá trên môi trường này');
        }

        $coupon = Coupon::find($coupon_id);
        if ($coupon) {
            $coupon->delete();
        }
        Session::put('message', 'Xóa mã giảm giá thành công');
        return Redirect::to('list-coupon');
    }
    public function list_coupon()
    {
        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');
        if (! Schema::hasTable((new Coupon())->getTable())) {
            $coupon = new LengthAwarePaginator([], 0, 5, 1, [
                'path' => request()->url(),
            ]);

            return view('admin.coupon.list_coupon')->with(compact('coupon', 'today'));
        }

        $coupon = Coupon::orderby('coupon_id', 'DESC')->paginate(5);
        return view('admin.coupon.list_coupon')->with(compact('coupon', 'today'));
    }
    public function insert_coupon_code(StoreCouponRequest $request)
    {
        if (! Schema::hasTable((new Coupon())->getTable())) {
            return Redirect::to('insert-coupon')->with('message', 'Chưa có bảng mã giảm giá trên môi trường này');
        }

        $data = $request->validated();

        $coupon = new Coupon;

        $coupon->coupon_name = $data['coupon_name'];
        $coupon->coupon_date_start = $data['coupon_date_start'];
        $coupon->coupon_date_end = $data['coupon_date_end'];
        $coupon->coupon_number = $data['coupon_number'];
        $coupon->coupon_code = $data['coupon_code'];
        $coupon->coupon_time = $data['coupon_time'];
        $coupon->coupon_condition = $data['coupon_condition'];
        $coupon->save();

        Session::put('message', 'Thêm mã giảm giá thành công');
        return Redirect::to('insert-coupon');
    }
}
