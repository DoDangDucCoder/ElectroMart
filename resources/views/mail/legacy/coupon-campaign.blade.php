<x-mail::message>
# Ưu đãi dành cho {{ $mailData->audienceLabel }}

{{ $mailData->shopName }} gửi tới bạn một mã ưu đãi. Khi mua hàng, bạn chỉ cần sao chép mã bên dưới và nhập vào ô mã giảm giá ở bước thanh toán.

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

Lưu ý: mã chỉ được áp dụng khi còn hiệu lực, còn lượt sử dụng và đơn hàng thỏa điều kiện khuyến mãi của hệ thống.

<x-slot:subcopy>
Email này được gửi tự động từ {{ $mailData->shopName }} để thông báo mã ưu đãi cho {{ $mailData->audienceLabel }}. Vui lòng không trả lời trực tiếp email này.
</x-slot:subcopy>
</x-mail::message>
