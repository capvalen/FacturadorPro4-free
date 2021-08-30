@extends('tenant.layouts.app')

@section('content')

    <tenant-purchase-quotations-form :id="{{ json_encode($id) }}"></tenant-purchase-quotations-form>

@endsection