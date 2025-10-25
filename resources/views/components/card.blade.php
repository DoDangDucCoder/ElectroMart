<!-- Modern Card Component -->
@props(['hover' => false, 'padding' => 'default'])

@php
$baseClasses = 'bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200 dark:border-gray-700 overflow-hidden';
$hoverClasses = $hover ? 'transition-all duration-300 hover:shadow-medium hover:-translate-y-1' : '';
$paddingClasses = [
    'none' => '',
    'sm' => 'p-4',
    'default' => 'p-6',
    'lg' => 'p-8',
];
@endphp

<div {{ $attributes->merge(['class' => $baseClasses . ' ' . $hoverClasses . ' ' . $paddingClasses[$padding]]) }}>
    {{ $slot }}
</div>
