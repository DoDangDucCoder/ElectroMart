<?php

namespace App\Http\Controllers;

use App\Support\StorefrontCustomerAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Two\InvalidStateException;
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
use Illuminate\Support\Str;

use App\Models\Order;
use App\Http\Requests;
use App\Application\AI\Services\ChatInboxService;
use App\Support\AdminPermissions;

class AdminController extends Controller
{
    public function login_customer_google(Request $request)
    {
        StorefrontCustomerAuth::rememberIntendedDestination($request, 'checkout');

        return Socialite::driver('google')->redirect();
    }
    public function callback_customer_google(Request $request)
    {
        if ($this->invalidSocialCallbackRequest($request)) {
            return $this->failedSocialCustomerLoginRedirect('google');
        }

        try {
            $users = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return $this->failedSocialCustomerLoginRedirect('google');
        }

        $customer = $this->findOrCreateStorefrontSocialCustomer(
            'google',
            $this->socialUserId($users),
            $this->socialUserEmail($users),
            $this->socialUserName($users),
            $this->socialUserAvatar($users)
        );

        Session::forget(['customer_name', 'customer_picture']);
        Session::put('customer_id', $customer->id);
        StorefrontCustomerAuth::storeDisplaySession(
            $customer->name,
            $customer->email,
            $this->socialUserAvatar($users)
        );

        $redirectUrl = StorefrontCustomerAuth::pullIntendedUrl('checkout');
        $redirectKey = StorefrontCustomerAuth::pullIntendedRedirect('checkout');

        return redirect($redirectUrl ?: StorefrontCustomerAuth::destinationUrl($redirectKey, 'checkout'))
            ->with('message', 'Đăng nhập bằng tài khoản Google thành công.');
    }

    public function login_facebook_customer(Request $request)
    {
        StorefrontCustomerAuth::rememberIntendedDestination($request, 'checkout');

        return Socialite::driver('facebook')->redirect();
    }
    public function callback_facebook_customer(Request $request)
    {
        if ($this->invalidSocialCallbackRequest($request)) {
            return $this->failedSocialCustomerLoginRedirect('facebook');
        }

        try {
            $provider = Socialite::driver('facebook')->user();
        } catch (InvalidStateException) {
            return $this->failedSocialCustomerLoginRedirect('facebook');
        }

        $customer = $this->findOrCreateStorefrontSocialCustomer(
            'facebook',
            $this->socialUserId($provider),
            $this->socialUserEmail($provider),
            $this->socialUserName($provider),
            $this->socialUserAvatar($provider)
        );

        Session::forget(['customer_name', 'customer_picture']);
        Session::put('customer_id', $customer->id);
        StorefrontCustomerAuth::storeDisplaySession(
            $customer->name,
            $customer->email,
            $this->socialUserAvatar($provider)
        );

        $redirectUrl = StorefrontCustomerAuth::pullIntendedUrl('checkout');
        $redirectKey = StorefrontCustomerAuth::pullIntendedRedirect('checkout');

        return redirect($redirectUrl ?: StorefrontCustomerAuth::destinationUrl($redirectKey, 'checkout'))
            ->with('message', 'Đăng nhập bằng tài khoản Facebook thành công.');
    }

    protected function invalidSocialCallbackRequest(Request $request): bool
    {
        return $request->filled('error')
            || ! $request->filled('code')
            || ! $request->filled('state');
    }

    protected function failedSocialCustomerLoginRedirect(string $provider): RedirectResponse
    {
        $redirectKey = StorefrontCustomerAuth::pullIntendedRedirect('checkout');
        StorefrontCustomerAuth::forgetIntendedRedirect();
        $providerLabel = ucfirst(strtolower($provider));

        return redirect(StorefrontCustomerAuth::destinationUrl('login') . '?' . http_build_query([
            'redirect' => $redirectKey,
        ]))->with('error', "Không thể hoàn tất đăng nhập bằng {$providerLabel}. Vui lòng thử lại.");
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
            $visitorRecordedToday = Visitors::query()
                ->where('ip_address', $user_ip_address)
                ->where('date_visitor', $now)
                ->exists();

            if (! $visitorRecordedToday) {
                $visitor = new Visitors();
                $visitor->ip_address = $user_ip_address;
                $visitor->date_visitor = $now;
                $visitor->save();
            }

            $visitor_last_month_count = Visitors::whereBetween('date_visitor', [$early_last_month, $end_of_last_month])->count();
            $visitor_this_month_count = Visitors::whereBetween('date_visitor', [$early_this_month, $now])->count();
            $visitor_year_count = Visitors::whereBetween('date_visitor', [$oneyears, $now])->count();
            $visitors_total = Visitors::count();
            $visitor_count = 1;
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
            && app(ChatInboxService::class)->tablesReady()
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
        $dashboardValue = (string) $request->input('dashboard_value', '365ngayqua');

        if ($dashboardValue == '7ngay') {

            $get = Statistic::whereBetween('order_date', [$sub7days, $now])->orderBy('order_date', 'ASC')->get();
        } elseif ($dashboardValue == 'thangtruoc') {

            $get = Statistic::whereBetween('order_date', [$dau_thangtruoc, $cuoi_thangtruoc])->orderBy('order_date', 'ASC')->get();
        } elseif ($dashboardValue == 'thangnay') {

            $get = Statistic::whereBetween('order_date', [$dauthangnay, $now])->orderBy('order_date', 'ASC')->get();
        } elseif ($dashboardValue == 'thang9') {

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

        $from_date = $this->normalizeAdminDateInput($request->input('from_date'));
        $to_date = $this->normalizeAdminDateInput($request->input('to_date'));

        if (! $from_date || ! $to_date) {
            return response()->json([]);
        }

        if ($from_date > $to_date) {
            [$from_date, $to_date] = [$to_date, $from_date];
        }

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
        $order_date = $this->normalizeAdminDateInput($request->query('date'));

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

    protected function normalizeAdminDateInput(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value, 'Asia/Ho_Chi_Minh')->toDateString();
            } catch (\Throwable) {
            }
        }

        try {
            return Carbon::parse($value, 'Asia/Ho_Chi_Minh')->toDateString();
        } catch (\Throwable) {
            return null;
        }
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

    protected function findOrCreateStorefrontSocialCustomer(
        string $provider,
        string $providerUserId,
        ?string $email,
        ?string $name,
        ?string $avatar
    ): StorefrontCustomer {
        $provider = strtolower($provider);
        $socialAccount = SocialCustomers::query()
            ->whereRaw('LOWER(provider) = ?', [$provider])
            ->where('provider_user_id', $providerUserId)
            ->first();

        $email = trim((string) $email);
        if ($email === '') {
            $email = $provider . '-' . $providerUserId . '@social.shopw.local';
        }

        $customer = $socialAccount
            ? StorefrontCustomer::query()->find($socialAccount->user)
            : null;

        if (! $customer) {
            $customer = StorefrontCustomer::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name ?: 'Khách hàng ' . ucfirst($provider),
                    'password' => bcrypt(Str::random(32)),
                    'phone' => null,
                    'is_active' => true,
                    'meta' => [
                        'social_auth' => [
                            'provider' => $provider,
                            'provider_user_id' => $providerUserId,
                            'avatar' => $avatar,
                        ],
                    ],
                ]
            );
        }

        $meta = (array) ($customer->meta ?? []);
        $meta['social_auth'] = array_filter([
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
            'avatar' => $avatar,
        ], fn ($value) => $value !== null && $value !== '');

        $customer->forceFill([
            'name' => $customer->name ?: ($name ?: 'Khách hàng ' . ucfirst($provider)),
            'email' => $customer->email ?: $email,
            'is_active' => true,
            'meta' => $meta,
        ])->save();

        $socialAccount = $socialAccount ?: new SocialCustomers();
        $socialAccount->provider_user_id = $providerUserId;
        $socialAccount->provider_user_email = $email;
        $socialAccount->provider = $provider;
        $socialAccount->user = $customer->id;
        $socialAccount->save();

        return $customer;
    }

    protected function socialUserId(object $providerUser): string
    {
        return (string) (
            $providerUser->getId()
            ?? $providerUser->id
            ?? ''
        );
    }

    protected function socialUserEmail(object $providerUser): ?string
    {
        return $providerUser->getEmail()
            ?? $providerUser->email
            ?? null;
    }

    protected function socialUserName(object $providerUser): ?string
    {
        return $providerUser->getName()
            ?? $providerUser->name
            ?? null;
    }

    protected function socialUserAvatar(object $providerUser): ?string
    {
        return $providerUser->getAvatar()
            ?? $providerUser->avatar
            ?? null;
    }
}
