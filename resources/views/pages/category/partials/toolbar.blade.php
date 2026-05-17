<div class="cat-x__toolbar">
    <div class="cat-x__count">
        Hiển thị {{ $fromIndex }}-{{ $toIndex }} trong {{ number_format($productCount) }} sản phẩm
    </div>

    <div class="cat-x__view-toggle" role="tablist" aria-label="Chế độ hiển thị">
        <button type="button" class="is-active" data-cat-view="grid" aria-label="Lưới">
            <i class="fa fa-th"></i>
        </button>
        <button type="button" data-cat-view="list" aria-label="Danh sách">
            <i class="fa fa-list"></i>
        </button>
    </div>

    <div class="cat-x__sort-wrap">
        <span>Sắp xếp:</span>
        <select id="cat-x-sort" onchange="if(this.value){window.location.href=this.value;}">
            @foreach (($sortLabels ?? []) as $sortKey => $sortLabel)
                @php
                    $sortUrl = $sortKey === 'none'
                        ? request()->fullUrlWithoutQuery(['sort_by'])
                        : request()->fullUrlWithQuery(['sort_by' => $sortKey]);
                @endphp
                <option value="{{ $sortUrl }}" {{ $selected_sort === $sortKey ? 'selected' : '' }}>
                    {{ $sortLabel }}
                </option>
            @endforeach
        </select>
    </div>
</div>
