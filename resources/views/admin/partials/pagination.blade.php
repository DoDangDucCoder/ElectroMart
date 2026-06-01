@props([
    'paginator',
    'label' => 'mục',
    'perPageOptions' => [10, 15, 25, 50],
])

@once
    <style>
        .adm-pagination {
            min-height: 72px;
            padding: 16px 18px;
            display: grid;
            grid-template-columns: minmax(220px, 1fr) auto auto;
            gap: 18px;
            align-items: center;
            border-top: 1px solid #f1f5f9;
        }

        .adm-pagination .info {
            justify-content: flex-start;
            border: 0;
            padding: 0;
            height: auto;
            font-size: 12px;
            font-weight: 500;
            color: var(--adm-muted);
            white-space: nowrap;
        }

        .adm-pagination .pages {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .adm-page-link,
        .adm-per-page-select {
            min-width: 34px;
            height: 34px;
            border: 1px solid var(--adm-border);
            border-radius: 7px;
            background: #fff;
            color: var(--adm-text);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
        }

        .adm-page-link.active {
            color: #fff;
            border-color: var(--adm-blue);
            background: var(--adm-blue);
            box-shadow: 0 8px 16px rgba(59, 130, 246, .22);
        }

        .adm-page-link.disabled {
            color: #cbd5e1;
            pointer-events: none;
            background: #fff;
        }

        .adm-page-ellipsis {
            color: var(--adm-muted);
            font-size: 12px;
            font-weight: 700;
            padding: 0 4px;
        }

        .adm-per-page-form {
            justify-self: end;
            margin: 0;
        }

        .adm-per-page-select {
            min-width: 92px;
            padding-right: 28px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E") right 10px center/10px no-repeat;
        }

        .g-admin-chat-sidebar .adm-pagination {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .g-admin-chat-sidebar .adm-pagination .info,
        .g-admin-chat-sidebar .adm-per-page-form {
            justify-self: center;
        }

        @media (max-width: 1500px) {
            .adm-pagination {
                grid-template-columns: 1fr;
            }

            .adm-pagination .info,
            .adm-per-page-form {
                justify-self: center;
            }
        }
    </style>
@endonce

@php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $visiblePages = collect([1, 2, 3, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage])
        ->filter(fn ($page) => $page >= 1 && $page <= $lastPage)
        ->unique()
        ->sort()
        ->values();
    $perPage = (int) request('per_page', $paginator->perPage());
    $query = request()->except(['page']);
    $pageUrl = fn (int $page): string => $paginator->appends($query)->url($page);
@endphp

<div class="adm-pagination">
    <span class="info">
        @if ($paginator->total() > 0)
            Hiển thị {{ number_format($paginator->firstItem(), 0, ',', '.') }} đến {{ number_format($paginator->lastItem(), 0, ',', '.') }} của {{ number_format($paginator->total(), 0, ',', '.') }} {{ $label }}
        @else
            Hiển thị 0 {{ $label }}
        @endif
    </span>

    <div class="pages">
        <a class="adm-page-link {{ $currentPage <= 1 ? 'disabled' : '' }}" href="{{ $currentPage > 1 ? $pageUrl($currentPage - 1) : '#' }}" aria-label="Trang trước">
            <i class="fa fa-angle-left"></i>
        </a>

        @foreach ($visiblePages as $index => $page)
            @if ($index > 0 && $page > $visiblePages[$index - 1] + 1)
                <span class="adm-page-ellipsis">...</span>
            @endif
            <a class="adm-page-link {{ $page === $currentPage ? 'active' : '' }}" href="{{ $pageUrl($page) }}">{{ $page }}</a>
        @endforeach

        <a class="adm-page-link {{ $currentPage >= $lastPage ? 'disabled' : '' }}" href="{{ $currentPage < $lastPage ? $pageUrl($currentPage + 1) : '#' }}" aria-label="Trang sau">
            <i class="fa fa-angle-right"></i>
        </a>
    </div>

    <form class="adm-per-page-form" method="GET" action="{{ url()->current() }}">
        @foreach (request()->except(['page', 'per_page']) as $name => $value)
            @if (is_array($value))
                @foreach ($value as $item)
                    <input type="hidden" name="{{ $name }}[]" value="{{ $item }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endif
        @endforeach
        <select class="adm-per-page-select" name="per_page" onchange="this.form.submit()" aria-label="Số mục mỗi trang">
            @foreach ($perPageOptions as $option)
                <option value="{{ $option }}" @selected($perPage === (int) $option)>{{ $option }} / trang</option>
            @endforeach
        </select>
    </form>
</div>
