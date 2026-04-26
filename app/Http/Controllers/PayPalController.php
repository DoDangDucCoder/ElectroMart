<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Throwable;

class PayPalController extends Controller
{
    private const PAYPAL_CLIENT_CLASS = 'Srmklive\\PayPal\\Services\\PayPal';

     /**
     * create transaction.
     *
     * @return \Illuminate\Http\Response
     */
   

    /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function processTransaction(Request $request)
    {
        $total = (float) Session::get('total_payment', 0);

        if ($total <= 0) {
            return $this->checkoutError('Không tìm thấy tổng tiền thanh toán PayPal.');
        }

        try {
            $provider = $this->paypalClient();

            if (! $provider) {
                return $this->checkoutError('Thanh toán PayPal hiện chưa được cấu hình.');
            }

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('successTransaction'),
                    "cancel_url" => route('cancelTransaction'),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => number_format($total, 2, '.', '')
                        ]
                    ]
                ]
            ]);
        } catch (Throwable $exception) {
            Log::warning('PayPal order creation failed', ['error' => $exception->getMessage()]);

            return $this->checkoutError('Không thể kết nối PayPal. Vui lòng thử lại sau.');
        }

        if (isset($response['id']) && $response['id'] != null) {

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            return $this->checkoutError('Lỗi giao dịch.');

        } else {
            return $this->checkoutError($response['message'] ?? 'Lỗi giao dịch.');
        }
    }

    /**
     * success transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function successTransaction(Request $request)
    {
        $token = trim((string) $request->query('token', ''));

        if ($token === '') {
            return $this->checkoutError('Thiếu mã xác nhận giao dịch PayPal.');
        }

        try {
            $provider = $this->paypalClient();

            if (! $provider) {
                return $this->checkoutError('Thanh toán PayPal hiện chưa được cấu hình.');
            }

            $response = $provider->capturePaymentOrder($token);
        } catch (Throwable $exception) {
            Log::warning('PayPal payment capture failed', ['error' => $exception->getMessage()]);

            return $this->checkoutError('Không thể xác nhận giao dịch PayPal. Vui lòng thử lại sau.');
        }

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            Session::put('success_paypal', true);
            return redirect()
                ->route('checkout')
                ->with('success', 'Giao dịch thành công.');
        } else {
            return $this->checkoutError($response['message'] ?? 'Lỗi giao dịch.');
        }
    }

    /**
     * cancel transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelTransaction(Request $request)
    {
        return $this->checkoutError('Bạn đã hủy giao dịch PayPal.');
    }

    protected function paypalClient(): ?object
    {
        $clientClass = self::PAYPAL_CLIENT_CLASS;

        if (! class_exists($clientClass)) {
            return null;
        }

        $provider = new $clientClass();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        return $provider;
    }

    protected function checkoutError(string $message): RedirectResponse
    {
        return redirect()
            ->route('checkout')
            ->with('error', $message);
    }
}
