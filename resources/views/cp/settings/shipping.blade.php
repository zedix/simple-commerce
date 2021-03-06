@extends('statamic::layout')
@section('title', 'Shipping Zones')

@section('content')
    <breadcrumbs :crumbs='@json($crumbs)'></breadcrumbs>

    <div class="content">
        <h1 class="mb">Shipping</h1>
    </div>

    <shipping-zone-settings
            index-endpoint="{{ cp_route('shipping-zones.index') }}"
            store-endpoint="{{ cp_route('shipping-zones.store') }}"
            initial-blueprint='@json($blueprint)'
            initial-meta='@json($meta)'
            initial-values='@json($values)'
    ></shipping-zone-settings>
@endsection
