@php
    $brandName = (string) config('services.storefront_brand.name', '');

    if ($brandName === '') {
        $appName = (string) config('app.name', 'ShopW');
        $brandName = $appName !== 'Laravel' ? $appName : 'ShopW';
    }
@endphp

<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ $brandName }}
</x-mail::header>
</x-slot:header>

{{ $slot }}

@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ $brandName }}. Email hệ thống, vui lòng không trả lời trực tiếp.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
