@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatches-create :order_form_id="{{ json_encode($order_form_id) }}"></tenant-dispatches-create>
@endsection
