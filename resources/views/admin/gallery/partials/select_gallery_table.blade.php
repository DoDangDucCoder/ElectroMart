<form>
    @csrf
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Thứ tự</th>
                <th>Tên hình ảnh</th>
                <th>Hình ảnh</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($gallery as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td contenteditable class="edit_gal_name" data-gal_id="{{ $item->gallery_id }}">{{ $item->gallery_name }}</td>
                    <td>
                        <img src="{{ $item->gallery_image_url }}" class="img-thumbnail" width="120" height="120">
                        <input type="file" class="file_image" style="width:40%" data-gal_id="{{ $item->gallery_id }}" id="file-{{ $item->gallery_id }}" name="file" accept="image/*" />
                    </td>
                    <td>
                        <button type="button" data-gal_id="{{ $item->gallery_id }}" class="btn btn-xs btn-danger delete-gallery">Xóa</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sản phẩm chưa thư viện ảnh</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</form>
