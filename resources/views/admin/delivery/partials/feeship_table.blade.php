<div class="table-responsive">
    <table class="table table-striped b-t b-light g-admin-data-table">
        <thead>
            <tr>
                <th>Tỉnh/Thành phố</th>
                <th>Xã/Phường/Đặc khu</th>
                <th>Phí ship</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($feeship as $fee)
                <tr>
                    <td>{{ $fee->city_name ?? 'Chưa cấu hình' }}</td>
                    <td>{{ $fee->ward_name ?? 'Chưa cấu hình' }}</td>
                    <td contenteditable data-feeship_id="{{ $fee->fee_id }}" class="fee_feeship_edit">
                        {{ number_format($fee->fee_feeship, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        <div class="g-admin-empty-state">
                            Chưa có dòng phí ship nào. Hãy thêm cấu hình đầu tiên ở biểu mẫu bên trên.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
