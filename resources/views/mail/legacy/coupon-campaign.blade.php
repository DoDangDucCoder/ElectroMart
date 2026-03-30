<x-mail::message>
# Ưu đãi dành cho {{ $mailData->audienceLabel }}

{{ $mailData->shopName }} gửi tới bạn một mã giảm giá để áp dụng cho đơn hàng tiếp theo trên website.

<x-mail::panel>
**Mức ưu đãi**  
{{ $mailData->discountLabel() }}

**Mã áp dụng**  
`{{ $mailData->couponCode }}`
</x-mail::panel>

<x-mail::table>
| Hạng mục | Thông tin |
| :-- | :-- |
| Số lượng mã còn lại | {{ $mailData->couponTimeLabel() }} |
| Thời gian hiệu lực | {{ $mailData->activeWindowLabel() }} |
</x-mail::table>

<x-mail::button :url="$mailData->loginUrl">
Đăng nhập để sử dụng mã
</x-mail::button>

Hoặc truy cập [{{ $mailData->shopUrl }}]({{ $mailData->shopUrl }}) để tiếp tục mua sắm và áp dụng mã ưu đãi ở bước thanh toán.

<x-slot:subcopy>
Email này được gửi tự động từ {{ $mailData->shopName }} dành cho {{ $mailData->audienceLabel }}.
</x-slot:subcopy>
</x-mail::message>
