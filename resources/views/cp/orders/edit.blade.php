@extends('statamic::layout')
@section('title', 'Edit Order')

@section('content')
    <publish-form
            title="{{ $values->title }}"
            action="{{ cp_route('orders.store') }}"
            :blueprint='@json($blueprint)'
            :meta='@json($meta)'
            :values='@json($values)'
    ></publish-form>
@endsection
