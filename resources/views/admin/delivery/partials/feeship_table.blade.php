<div class="table-responsive">
    <table class="table table-striped b-t b-light g-admin-data-table">
        <thead>
            <tr>
                <th>Mức áp dụng</th>
                <th>Tỉnh/Thành phố</th>
                <th>Xã/Phường/Đặc khu</th>
                <th>Phí vận chuyển</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($feeship as $fee)
                <tr>
                    <td><span class="label label-info">{{ $fee->scope_label }}</span></td>
                    <td>{{ $fee->city_name ?? 'Toàn quốc' }}</td>
                    <td>{{ $fee->ward_name ?? 'Tất cả xã/phường' }}</td>
                    <td contenteditable data-feeship_id="{{ $fee->fee_id }}" class="fee_feeship_edit">
                        {{ number_format($fee->fee_feeship, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="g-admin-empty-state">
                            Chưa có dòng phí vận chuyển nào. Hãy thêm cấu hình đầu tiên ở biểu mẫu bên trên.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
