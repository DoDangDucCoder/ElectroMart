<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnPayMockController extends Controller
{
    public function selectMethod(Request $request)
    {
        $amount = $request->input('amount', 0);
        return view('pages.vnpay_mock.method', compact('amount'));
    }

    public function ncb(Request $request)
    {
        $amount = $request->input('amount', 0);
        return view('pages.vnpay_mock.ncb', compact('amount'));
    }

    public function otp(Request $request)
    {
        $amount = $request->input('amount', 0);
        return view('pages.vnpay_mock.otp', compact('amount'));
    }
}
