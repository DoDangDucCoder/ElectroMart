@php
    $items = collect($items ?? [])->filter(fn ($item) => ! empty($item['label']))->values();
    $rootLabel = $rootLabel ?? 'Trang chủ';
    $rootUrl = $rootUrl ?? url('/');
    $separator = $separator ?? '/';
    $wrapperClass = trim('storefront-breadcrumb ' . ($class ?? ''));
    $separatorClass = trim('storefront-breadcrumb-sep sep ' . ($separatorClass ?? ''));
    $currentClass = trim('storefront-breadcrumb-current current ' . ($currentClass ?? ''));
@endphp

<nav class="{{ $wrapperClass }}" aria-label="breadcrumb">
    <a href="{{ $rootUrl }}">{{ $rootLabel }}</a>

    @foreach ($items as $item)
        @php
            $isCurrent = (bool) ($item['current'] ?? $loop->last);
            $itemUrl = $item['url'] ?? null;
        @endphp
        <span class="{{ $separatorClass }}" aria-hidden="true">{{ $separator }}</span>

        @if (! $isCurrent && $itemUrl)
            <a href="{{ $itemUrl }}">{{ $item['label'] }}</a>
        @else
            <span class="{{ $currentClass }}" aria-current="page">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
