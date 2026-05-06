@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $breadcrumbItems = $breadcrumbItems ?? [];
    $headerClass = trim('storefront-page-header ' . ($class ?? ''));
    $titleClass = trim('storefront-page-title ' . ($titleClass ?? ''));
    $subtitleClass = trim('storefront-page-subtitle ' . ($subtitleClass ?? ''));
@endphp

<header class="{{ $headerClass }}">
    @include('partials.storefront.breadcrumb', [
        'items' => $breadcrumbItems,
        'class' => $breadcrumbClass ?? '',
        'separator' => $separator ?? '/',
        'separatorClass' => $separatorClass ?? '',
        'currentClass' => $currentClass ?? '',
        'rootLabel' => $rootLabel ?? 'Trang chủ',
        'rootUrl' => $rootUrl ?? url('/'),
    ])

    @if ($title !== '')
        <div class="storefront-page-heading">
            <h1 class="{{ $titleClass }}">{{ $title }}</h1>
            @if ($subtitle)
                <p class="{{ $subtitleClass }}">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
</header>
