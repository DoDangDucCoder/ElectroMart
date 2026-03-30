<table class="table table-hover">
    <thead>
        <tr>
            <th>{{ $nameLabel }}</th>
            <th>{{ $imageLabel }}</th>
            <th>{{ $linkLabel }}</th>
            <th>Quản lý</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($icons as $icon)
            <tr>
                <td>{{ $icon->name }}</td>
                <td>
                    <img
                        height="{{ $imageHeight }}"
                        width="{{ $imageWidth }}"
                        src="{{ url('uploads/icons/' . $icon->image) }}"
                    >
                </td>
                <td>{{ $icon->link }}</td>
                <td>
                    <button id="{{ $icon->id_icons }}" class="btn {{ $buttonClass }}" onclick="delete_icons(this.id)">{{ $buttonLabel }}</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <div class="g-admin-empty-state">Chưa có dữ liệu nào.</div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
