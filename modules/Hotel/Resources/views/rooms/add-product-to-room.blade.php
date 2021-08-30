@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-rent-add-product :products='@json($products)' :rent='@json($rent)' :configuration='@json($configuration)'></tenant-hotel-rent-add-product>
@endsection
