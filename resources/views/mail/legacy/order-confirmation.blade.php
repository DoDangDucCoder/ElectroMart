@php
    $tableRows = collect($mailData->items)
        ->map(function ($item) {
            return '| ' . str_replace(["\r", "\n", '|'], [' ', ' ', '\|'], e((string) $item->productName))
                . ' | ' . number_format($item->productPrice, 0, ',', '.') . 'đ'
                . ' | ' . $item->quantity
                . ' | ' . number_format($item->lineTotal(), 0, ',', '.') . 'đ |';
        })
        ->implode("\n");
@endphp

<x-mail::message>
# Xác nhận đơn hàng

Chào **{{ $mailData->customerName }}**, hệ thống đã ghi nhận đơn hàng của bạn và đang chuẩn bị xử lý trong thời gian sớm nhất.

<x-mail::panel>
**Mã đơn hàng**  
{{ $mailData->orderCode }}

**Mã khuyến mãi**  
{{ $mailData->couponCode }}

**Phí vận chuyển**  
{{ number_format($mailData->feeShip, 0, ',', '.') }}đ

**Thanh toán**  
{{ $mailData->paymentLabel }}
</x-mail::panel>

## Thông tin nhận hàng

- **Người nhận:** {{ $mailData->shippingName }}
- **Email:** {{ $mailData->shippingEmail }}
- **Số điện thoại:** {{ $mailData->shippingPhone }}
- **Địa chỉ:** {{ $mailData->shippingAddress }}
- **Ghi chú:** {{ $mailData->shippingNotes }}

## Sản phẩm đã đặt

<x-mail::table>
| Sản phẩm | Giá bán | SL | Thành tiền |
| :-- | --: | --: | --: |
{!! $tableRows !!}
</x-mail::table>

<x-mail::panel>
**Tổng thanh toán dự kiến**  
{{ number_format($mailData->grandTotal(), 0, ',', '.') }}đ
</x-mail::panel>

Bạn có thể xem lại đơn hàng tại [lịch sử đơn hàng]({{ $mailData->historyUrl }}) hoặc truy cập [{{ $mailData->shopUrl }}]({{ $mailData->shopUrl }}) khi cần hỗ trợ thêm.

<x-slot:subcopy>
Hotline hỗ trợ: {{ $mailData->supportPhone }}.
</x-slot:subcopy>
</x-mail::message>
