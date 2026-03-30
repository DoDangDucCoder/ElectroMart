<x-mail::message>
# Đặt lại mật khẩu

Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản **{{ $mailData->email }}**.

<x-mail::panel>
Yêu cầu này được tạo từ hệ thống {{ $mailData->shopName }}. Nếu đây không phải là bạn, hãy bỏ qua email này.
</x-mail::panel>

<x-mail::button :url="$mailData->resetUrl">
Đặt lại mật khẩu
</x-mail::button>

Nếu nút không hoạt động, hãy sao chép liên kết sau vào trình duyệt:

{{ $mailData->resetUrl }}

<x-slot:subcopy>
Liên kết đặt lại mật khẩu có hiệu lực trong phạm vi token hiện tại của tài khoản.
</x-slot:subcopy>
</x-mail::message>
