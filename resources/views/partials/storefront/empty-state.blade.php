@php
    $title = $title ?? 'Chưa có dữ liệu';
    $message = $message ?? 'Nội dung sẽ xuất hiện tại đây khi có dữ liệu phù hợp.';
    $icon = $icon ?? 'fa fa-inbox';
    $compact = $compact ?? false;
    $center = $center ?? true;
    $shellClass = trim('shopw-empty-state' . ($compact ? ' shopw-empty-state--compact' : '') . ($center ? ' shopw-empty-state--center' : '') . ' ' . ($class ?? ''));
@endphp

<div class="{{ $shellClass }}">
    <div class="shopw-empty-state__icon" aria-hidden="true">
        <i class="{{ $icon }}"></i>
    </div>
    <div class="shopw-empty-state__body">
        <h3 class="shopw-empty-state__title">{{ $title }}</h3>
        <p class="shopw-empty-state__message">{{ $message }}</p>

        @if (!empty($actionText) && !empty($actionUrl))
            <a href="{{ $actionUrl }}" class="shopw-empty-state__action">
                {{ $actionText }}
            </a>
        @endif
    </div>
</div>

