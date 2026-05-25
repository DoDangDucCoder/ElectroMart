@php
    $cardClass = $cardClass ?? '';
    $title = $title ?? 'ĐĂNG KÝ NHẬN TIN';
    $description = $description ?? 'Nhận ngay thông tin khuyến mãi và bài viết mới nhất từ DangDuc.';
    $formClass = $formClass ?? '';
    $method = $method ?? null;
    $action = $action ?? null;
    $onsubmit = $onsubmit ?? null;
    $noteTag = $noteTag ?? 'small';
    $note = $note ?? 'Chúng tôi cam kết không gửi thư rác và bảo mật thông tin của bạn.';
@endphp

<section class="{{ trim($cardClass . ' dd-newsletter-card') }}">
    <h3>{{ $title }}</h3>
    <p class="dd-newsletter-card__copy">{{ $description }}</p>
    <form
        @if ($action) action="{{ $action }}" @endif
        @if ($method) method="{{ $method }}" @endif
        @if ($onsubmit) onsubmit="{{ $onsubmit }}" @endif
        class="{{ trim($formClass . ' dd-newsletter-card__form') }}">
        @if (($withCsrf ?? false) === true)
            @csrf
        @endif
        <input type="email" name="email" placeholder="Nhập email của bạn" required>
        <button type="submit" aria-label="Đăng ký nhận tin">
            <i class="fa fa-envelope" aria-hidden="true"></i>
        </button>
    </form>
    <{{ $noteTag }} class="{{ trim(($noteClass ?? '') . ' dd-newsletter-card__note') }}">
        <i class="fa fa-shield" aria-hidden="true"></i>
        {{ $note }}
    </{{ $noteTag }}>
</section>
