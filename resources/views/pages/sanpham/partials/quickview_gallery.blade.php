@if ($mediaItems->isNotEmpty())
    @foreach ($mediaItems as $media)
        @php
            $imagePath = $media->file_path ?? $fallbackPath;
            $imageUrl = \App\Support\AssetUrl::product($imagePath);
            $imageAlt = \App\Support\StorefrontLabel::neutralizeSource($media->gallery_name ?? 'Hình ảnh sản phẩm');
        @endphp
        @if ($imagePath)
            <p><img width="100%" src="{{ $imageUrl }}" alt="{{ $imageAlt }}"></p>
        @endif
    @endforeach
@else
    <p><img width="100%" src="{{ $fallbackImage }}" alt="Hình ảnh sản phẩm"></p>
@endif
