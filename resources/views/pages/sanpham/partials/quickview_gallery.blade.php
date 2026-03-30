@if ($mediaItems->isNotEmpty())
    @foreach ($mediaItems as $media)
        @php
            $imagePath = $media->file_path ?? $fallbackPath;
        @endphp
        @if ($imagePath)
            <p><img width="100%" src="{{ asset($imagePath) }}"></p>
        @endif
    @endforeach
@else
    <p><img width="100%" src="{{ $fallbackImage }}"></p>
@endif
