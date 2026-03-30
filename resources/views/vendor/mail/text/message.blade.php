@php
    $brandName = config('app.name') !== 'Laravel' ? config('app.name') : 'Laravel Shop';
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
