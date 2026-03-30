<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\SocialCustomers;
use Socialite;
use App\Models\Product;
use App\Models\Video;
use App\Models\Customer;
use App\Models\Post;
use App\Models\Statistic;
use App\Models\Visitors;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product as StorefrontProduct;
use App\Infrastructure\Persistence\Eloquent\Order\Order as StorefrontOrder;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer as StorefrontCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

use App\Models\Order;
use App\Http\Requests;
use App\Application\AI\Services\ChatInboxService;
use App\Support\AdminPermissions;

class AdminController extends Controller
{
    public function login_customer_google()
    {
        config(['services.google.redirect' => env('GOOGLE_CLIENT_URL')]);
        return Socialite::driver('google')->redirect();
    }
    public function callback_customer_google()
    {

        config(['services.google.redirect' => env('GOOGLE_CLIENT_URL')]);

        $users = Socialite::driver('google')->stateless()->user();

        $authUser = $this->findOrCreateCustomer($users, 'google');

        if ($authUser) {
            $account_name = Customer::where('customer_id', $authUser->user)->first();
            Session::put('customer_id', $account_name->customer_id);
            Session::put('customer_picture', $account_name->customer_picture);
            Session::put('customer_name', $account_name->customer_name);
        } elseif ($customer_new) {
            $account_name = Customer::where('customer_id', $authUser->user)->first();
            Session::put('customer_id', $account_name->customer_id);
            Session::put('customer_picture', $account_name->customer_picture);
            Session::put('customer_name', $account_name->customer_name);
        }

        return redirect('/dang-nhap')->with('message', 'Đăng nhập bằng tài khoản google <span style="color:red">' . $account_name->customer_email . '</span> thành công');
    }
    public function findOrCreateCustomer($users, $provider)
    {
        $authUser = SocialCustomers::where('provider_user_id', $users->id)->first();
        if ($authUser) {
            return $authUser;
        } else {
            $customer_new = new SocialCustomers([
                'provider_user_id' => $users->id,
                'provider_user_email' => $users->email,
                'provider' => strtoupper($provider)
            ]);

            $customer = Customer::where('customer_email', $users->email)->first();

            if (!$customer) {

                $customer = Customer::create([
                    'customer_name' => $users->name,
                    'customer_picture' => $users->avatar,
                    'customer_email' => $users->email,
                    'customer_password' => '',
                    'customer_phone' => ''
                ]);
            }

            $customer_new->customer()->associate($customer);

            $customer_new->save();
            return $customer_new;
        }
    }
    public function login_facebook_customer()
    {
        config(['services.facebook.redirect' => env('FACEBOOK_CLIENT_REDIRECT')]);
        return Socialite::driver('facebook')->redirect();
    }
    public function callback_facebook_customer()
    {
        config(['services.facebook.redirect' => env('FACEBOOK_CLIENT_REDIRECT')]);
        $provider = Socialite::driver('facebook')->user();

        $account = SocialCustomers::where('provider', 'facebook')->where('provider_user_id', $provider->getId())->first();

        if ($account != NULL) {

            $account_name = Customer::where('customer_id', $account->user)->first();
            Session::put('customer_id', $account_name->customer_id);
            Session::put('customer_name', $account_name->customer_name);

            return redirect('/dang-nhap')->with('message', 'Đăng nhập bằng tài khoản facebook <span style="color:red">' . $account_name->customer_email . '</span> thành công');
        } elseif ($account == NULL) {
            $customer_login = new SocialCustomers([
                'provider_user_id' => $provider->getId(),
                'provider_user_email' => $provider->getEmail(),
                'provider' => 'facebook'
            ]);

            $customer = Customer::where('customer_email', $provider->getEmail())->first();

            if (!$customer) {
                $customer = Customer::create([
                    'customer_name' => $provider->getName(),
                    'customer_email' => $provider->getEmail(),
                    'customer_picture' => '',
                    'customer_password' => '',
                    'customer_phone' => ''
                ]);
            }
            $customer_login->customer()->associate($customer);
            $customer_login->save();

            $account_new = Customer::where('customer_id', $customer_login->user)->first();
            Session::put('customer_id', $account_new->customer_id);
            Session::put('customer_name', $account_new->customer_name);


            return redirect('/dang-nhap')->with('message', 'Đăng nhập bằng tài khoản facebook <span style="color:red">' . $account_new->customer_email . '</span> thành công');
        }
    }
    public function show_dashboard(Request $request)
    {
        $user_ip_address = $request->ip();

        $early_last_month = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->startOfMonth()->toDateString();

        $end_of_last_month = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->endOfMonth()->toDateString();

        $early_this_month = Carbon::now('Asia/Ho_Chi_Minh')->startOfMonth()->toDateString();

        $oneyears = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateString();

        $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

        $visitor_count = 0;
        $visitor_last_month_count = 0;
        $visitor_this_month_count = 0;
        $visitor_year_count = 0;
        $visitors_total = 0;

        if (Schema::hasTable((new Visitors())->getTable())) {
            $visitor_last_month_count = Visitors::whereBetween('date_visitor', [$early_last_month, $end_of_last_month])->count();
            $visitor_this_month_count = Visitors::whereBetween('date_visitor', [$early_this_month, $now])->count();
            $visitor_year_count = Visitors::whereBetween('date_visitor', [$oneyears, $now])->count();
            $visitors_total = Visitors::count();
            $visitor_count = Visitors::where('ip_address', $user_ip_address)->count();

            if ($visitor_count < 1) {
                $visitor = new Visitors();
                $visitor->ip_address = $user_ip_address;
                $visitor->date_visitor = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
                $visitor->save();
                $visitor_count = 1;
                $visitors_total++;
            }
        }

        $product = $this->dashboardProductCount();
        $post = $this->dashboardPostCount();
        $order = $this->dashboardOrderCount();
        $video = $this->dashboardVideoCount();
        $customer = $this->dashboardCustomerCount();

        $product_views = $this->dashboardProductViews();
        $post_views = $this->dashboardPostViews();
        $dashboard_totals = [
            'products' => $product,
            'posts' => $post,
            'orders' => $order,
            'videos' => $video,
            'customers' => $customer,
        ];
        $chat_inbox_stats = null;

        if (
            $request->user('web')
            && $request->user('web')->can(AdminPermissions::MANAGE_SYSTEM)
            && Schema::hasTable('chat_conversations')
        ) {
            $chat_inbox_stats = app(ChatInboxService::class)->sidebarStats($request->user('web'));
        }

        return view('admin.dashboard')->with(compact(
            'visitors_total',
            'visitor_count',
            'visitor_last_month_count',
            'visitor_this_month_count',
            'visitor_year_count',
            'product',
            'post',
            'order',
            'video',
            'customer',
            'product_views',
            'post_views',
            'dashboard_totals',
            'chat_inbox_stats'
        ));
    }

    public function days_order()
    {
        if (! Schema::hasTable((new Statistic())->getTable())) {
            return response()->json([]);
        }

        $sub60days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateString();

        $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

        $get = Statistic::whereBetween('order_date', [$sub60days, $now])->orderBy('order_date', 'ASC')->get();


        $chart_data = [];

        foreach ($get as $key => $val) {
            $chart_data[] = array(
                'period' => $val->order_date,
                'order' => $val->total_order,
                'sales' => $val->sales,
                'profit' => $val->profit,
                'quantity' => $val->quantity
            );
        }

        return response()->json($chart_data);
    }

    public function dashboard_filter(Request $request)
    {
        if (! Schema::hasTable((new Statistic())->getTable())) {
            return response()->json([]);
        }

        $data = $request->all();

        // $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
        // $tomorrow = Carbon::now('Asia/Ho_Chi_Minh')->addDay()->format('d-m-Y H:i:s');
        // $lastWeek = Carbon::now('Asia/Ho_Chi_Minh')->subWeek()->format('d-m-Y H:i:s');
        // $sub15days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(15)->format('d-m-Y H:i:s');
        // $sub30days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(30)->format('d-m-Y H:i:s');

        $dauthangnay = Carbon::now('Asia/Ho_Chi_Minh')->startOfMonth()->toDateString();
        $dau_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->startOfMonth()->toDateString();
        $cuoi_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->endOfMonth()->toDateString();



        $sub7days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(7)->toDateString();
        $sub365days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateString();

        $dauthang9 = Carbon::now('Asia/Ho_Chi_Minh')->subMonth(2)->startOfMonth()->toDateString();
        $cuoithang9 = Carbon::now('Asia/Ho_Chi_Minh')->subMonth(2)->endOfMonth()->toDateString();


        $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

        if ($data['dashboard_value'] == '7ngay') {

            $get = Statistic::whereBetween('order_date', [$sub7days, $now])->orderBy('order_date', 'ASC')->get();
        } elseif ($data['dashboard_value'] == 'thangtruoc') {

            $get = Statistic::whereBetween('order_date', [$dau_thangtruoc, $cuoi_thangtruoc])->orderBy('order_date', 'ASC')->get();
        } elseif ($data['dashboard_value'] == 'thangnay') {

            $get = Statistic::whereBetween('order_date', [$dauthangnay, $now])->orderBy('order_date', 'ASC')->get();
        } elseif ($data['dashboard_value'] == 'thang9') {

            $get = Statistic::whereBetween('order_date', [$dauthang9, $cuoithang9])->orderBy('order_date', 'ASC')->get();
        } else {
            $get = Statistic::whereBetween('order_date', [$sub365days, $now])->orderBy('order_date', 'ASC')->get();
        }


        $chart_data = [];

        foreach ($get as $key => $val) {
            $chart_data[] = array(
                'period' => $val->order_date,
                'order' => $val->total_order,
                'sales' => $val->sales,
                'profit' => $val->profit,
                'quantity' => $val->quantity
            );
        }

        return response()->json($chart_data);
    }
    public function filter_by_date(Request $request)
    {
        if (! Schema::hasTable((new Statistic())->getTable())) {
            return response()->json([]);
        }

        $data = $request->all();

        $from_date = $data['from_date'];
        $to_date = $data['to_date'];

        $get = Statistic::whereBetween('order_date', [$from_date, $to_date])->orderBy('order_date', 'ASC')->get();


        $chart_data = [];

        foreach ($get as $key => $val) {
            $chart_data[] = array(

                'period' => $val->order_date,
                'order' => $val->total_order,
                'sales' => $val->sales,
                'profit' => $val->profit,
                'quantity' => $val->quantity
            );
        }

        return response()->json($chart_data);
    }
    public function order_date(Request $request)
    {
        $order_date = $request->query('date');

        if (! $order_date) {
            return view('admin.order_date')->with(['order' => collect()]);
        }

        if (! Schema::hasTable((new Order())->getTable())) {
            $order = collect();
            return view('admin.order_date')->with(compact('order'));
        }

        $order = Order::where('order_date', $order_date)->orderby('created_at', 'DESC')->get();
        return view('admin.order_date')->with(compact('order'));
    }

    protected function dashboardProductCount(): int
    {
        if (Schema::hasTable((new Product())->getTable())) {
            return Product::count();
        }

        if (Schema::hasTable((new StorefrontProduct())->getTable())) {
            return StorefrontProduct::count();
        }

        return 0;
    }

    protected function dashboardPostCount(): int
    {
        if (Schema::hasTable((new Post())->getTable())) {
            return Post::count();
        }

        return 0;
    }

    protected function dashboardOrderCount(): int
    {
        if (Schema::hasTable((new Order())->getTable())) {
            return Order::count();
        }

        if (Schema::hasTable((new StorefrontOrder())->getTable())) {
            return StorefrontOrder::count();
        }

        return 0;
    }

    protected function dashboardVideoCount(): int
    {
        if (Schema::hasTable((new Video())->getTable())) {
            return Video::count();
        }

        if (Schema::hasTable('videos')) {
            return (int) DB::table('videos')->count();
        }

        return 0;
    }

    protected function dashboardCustomerCount(): int
    {
        if (Schema::hasTable((new Customer())->getTable())) {
            return Customer::count();
        }

        if (Schema::hasTable((new StorefrontCustomer())->getTable())) {
            return StorefrontCustomer::count();
        }

        return 0;
    }

    protected function dashboardProductViews()
    {
        if (Schema::hasTable((new Product())->getTable())) {
            return Product::orderByDesc('product_views')->take(20)->get();
        }

        if (Schema::hasTable((new StorefrontProduct())->getTable())) {
            return StorefrontProduct::orderByDesc('views_count')->take(20)->get();
        }

        return collect();
    }

    protected function dashboardPostViews()
    {
        if (Schema::hasTable((new Post())->getTable())) {
            return Post::orderByDesc('views_count')->take(20)->get();
        }

        return collect();
    }
}
