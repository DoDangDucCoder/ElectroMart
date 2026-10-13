@php
    $currentSort = $filters['sort'] ?? $default ?? '';
    $isAsc = $currentSort === $asc;
    $isDesc = $currentSort === $desc;
    $nextSort = $isDesc ? $asc : $desc;
    $icon = $isAsc ? 'fa-sort-asc' : ($isDesc ? 'fa-sort-desc' : 'fa-sort');
    $activeClass = ($isAsc || $isDesc) ? 'sort-active' : '';
@endphp
<a class="adm-th-sort {{ $activeClass }}" href="{{ request()->fullUrlWithQuery(['sort' => $nextSort, 'page' => 1]) }}">{{ $label }}&nbsp;<i class="fa {{ $icon }}"></i></a>
