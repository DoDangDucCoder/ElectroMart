<form>
    @csrf
    <table class="table table-striped b-t b-light">
        <thead>
            <tr>
                <th>Thứ tự</th>
                <th>Tên video</th>
                <th>Đường dẫn tĩnh (slug)</th>
                <th>Hình ảnh video</th>
                <th>Link</th>
                <th>Mô tả</th>
                <th>Demo video</th>
                <th style="width:80px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($videos as $video)
                @php
                    $videoId = (int) ($video->video_id ?? $video->id);
                    $videoTitle = $video->video_title ?? $video->title ?? '';
                    $videoSlug = $video->video_slug ?? $video->slug ?? '';
                    $videoDesc = $video->video_desc ?? $video->description ?? '';
                    $videoLink = $video->video_link ?? $video->video_url ?? '';
                    $youtubeId = $extractYoutubeId($videoLink);
                    $imageUrl = $videoThumbnailUrl($video);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td contenteditable data-video_id="{{ $videoId }}" data-video_type="video_title" class="video_edit" id="video_title_{{ $videoId }}">{{ $videoTitle }}</td>
                    <td contenteditable data-video_id="{{ $videoId }}" data-video_type="video_slug" class="video_edit" id="video_slug_{{ $videoId }}">{{ $videoSlug }}</td>
                    <td>
                        <img src="{{ $imageUrl }}" class="img-thumbnail" width="80" height="80">
                        <input type="file" class="file_img_video" data-video_id="{{ $videoId }}" id="file-video-{{ $videoId }}" name="file" accept="image/*" />
                    </td>
                    <td contenteditable data-video_id="{{ $videoId }}" data-video_type="video_link" class="video_edit" id="video_link_{{ $videoId }}">{{ $videoLink }}</td>
                    <td contenteditable data-video_id="{{ $videoId }}" data-video_type="video_desc" class="video_edit" id="video_desc_{{ $videoId }}">{{ $videoDesc }}</td>
                    <td>
                        @if ($youtubeId !== '')
                            <iframe width="200" height="200" src="https://www.youtube.com/embed/{{ $youtubeId }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        @else
                            <div class="g-admin-empty-state g-admin-video-preview-empty">
                                Chưa có link xem trước hợp lệ.
                            </div>
                        @endif
                    </td>
                    <td>
                        <button type="button" data-video_id="{{ $videoId }}" class="btn btn-xs btn-danger btn-delete-video">Xóa video</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Chưa có video nào hết</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</form>
