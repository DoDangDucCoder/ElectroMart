@extends('layouts.home-tech')

@php
    $supportTitle = trim($__env->yieldContent('support_title', ''));
    $supportMetaDesc = trim($__env->yieldContent('support_meta_desc', $supportTitle));
    $supportMetaKeywords = trim($__env->yieldContent('support_meta_keywords', 'DangDuc, ho tro, chinh sach'));
    $supportCanonical = trim($__env->yieldContent('support_canonical', ''));

    $meta_desc = $meta_desc ?? $supportMetaDesc;
    $meta_keywords = $meta_keywords ?? $supportMetaKeywords;
    $meta_title = $meta_title ?? ($supportTitle !== '' ? $supportTitle.' - DangDuc' : 'DangDuc');
    $url_canonical = $url_canonical ?? ($supportCanonical !== '' ? url($supportCanonical) : url()->current());
@endphp

@section('body_class', trim('policy-page '.$__env->yieldContent('support_page_class', '')))


@section('content')
    <div class="pp-wrap">
        @if ($supportTitle !== '')
            @include('partials.storefront.breadcrumb', [
                'class' => 'pp-breadcrumb',
                'separator' => '›',
                'items' => [
                    [
                        'label' => $supportTitle,
                        'current' => true,
                    ],
                ],
            ])
        @endif

        @yield('support_content')
    </div>
@endsection
