<?php

namespace App\Http\Controllers;

use App\Support\StorefrontCustomerAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Two\InvalidStateException;
use Socialite;
use App\Models\Post;
use App\Infrastructure\Persistence\Eloquent\Analytics\AnalyticsDaily;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product as StorefrontProduct;
use App\Infrastructure\Persistence\Eloquent\Cms\Video as StorefrontVideo;
use App\Infrastructure\Persistence\Eloquent\Order\Order as StorefrontOrder;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer as StorefrontCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Http\Requests;
use App\Application\AI\Services\ChatInboxService;
use App\Application\Order\Services\LegacyOrderWorkflowService;
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

        if ($this->analyticsDailyTableReady()) {
            $visitor_count = (int) AnalyticsDaily::query()
                ->whereDate('date', $now)
                ->sum('visits');
            $visitor_last_month_count = (int) AnalyticsDaily::query()
                ->whereBetween('date', [$early_last_month, $end_of_last_month])
                ->sum('visits');
            $visitor_this_month_count = (int) AnalyticsDaily::query()
                ->whereBetween('date', [$early_this_month, $now])
                ->sum('visits');
            $visitor_year_count = (int) AnalyticsDaily::query()
                ->whereBetween('date', [$oneyears, $now])
                ->sum('visits');
            $visitors_total = (int) AnalyticsDaily::query()->sum('visits');
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
        if (! $this->analyticsDailyTableReady()) {
            return response()->json([]);
        }

        $sub60days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateString();

        $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

        return response()->json($this->dashboardChartData($sub60days, $now));
    }

    public function dashboard_filter(Request $request)
    {
        if (! $this->analyticsDailyTableReady()) {
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

            [$fromDate, $toDate] = [$sub7days, $now];
        } elseif ($dashboardValue == 'thangtruoc') {

            [$fromDate, $toDate] = [$dau_thangtruoc, $cuoi_thangtruoc];
        } elseif ($dashboardValue == 'thangnay') {

            [$fromDate, $toDate] = [$dauthangnay, $now];
        } elseif ($dashboardValue == 'thang9') {

            [$fromDate, $toDate] = [$dauthang9, $cuoithang9];
        } else {
            [$fromDate, $toDate] = [$sub365days, $now];
        }

        return response()->json($this->dashboardChartData($fromDate, $toDate));
    }
    public function filter_by_date(Request $request)
    {
        if (! $this->analyticsDailyTableReady()) {
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

        return response()->json($this->dashboardChartData($from_date, $to_date));
    }
    public function order_date(Request $request)
    {
        $order_date = $this->normalizeAdminDateInput($request->query('date'));

        if (! $order_date) {
            return view('admin.order_date')->with(['order' => collect()]);
        }

        $order = app(LegacyOrderWorkflowService::class)->ordersForDate($order_date);

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

    protected function analyticsDailyTableReady(): bool
    {
        return Schema::hasTable((new AnalyticsDaily())->getTable());
    }

    protected function dashboardChartData(string $fromDate, string $toDate): array
    {
        return AnalyticsDaily::query()
            ->whereBetween('date', [$fromDate, $toDate])
            ->orderBy('date', 'ASC')
            ->get()
            ->map(fn (AnalyticsDaily $daily): array => [
                'period' => $daily->date?->toDateString(),
                'order' => (int) $daily->orders_count,
                'sales' => (float) $daily->sales,
                'profit' => (float) $daily->profit,
                'quantity' => (int) $daily->items_sold,
            ])
            ->all();
    }

    protected function dashboardProductCount(): int
    {
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
        if (Schema::hasTable((new StorefrontOrder())->getTable())) {
            return StorefrontOrder::count();
        }

        return 0;
    }

    protected function dashboardVideoCount(): int
    {
        if (Schema::hasTable((new StorefrontVideo())->getTable())) {
            return StorefrontVideo::count();
        }

        return 0;
    }

    protected function dashboardCustomerCount(): int
    {
        if (Schema::hasTable((new StorefrontCustomer())->getTable())) {
            return StorefrontCustomer::count();
        }

        return 0;
    }

    protected function dashboardProductViews()
    {
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

        $email = trim((string) $email);
        if ($email === '') {
            $email = $provider . '-' . $providerUserId . '@social.shopw.local';
        }

        $customer = StorefrontCustomer::query()
            ->where('meta->social_auth->provider', $provider)
            ->where('meta->social_auth->provider_user_id', $providerUserId)
            ->first();

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
