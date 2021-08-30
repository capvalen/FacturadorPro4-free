@extends('tenant.layouts.app_pos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}"/>
@endpush

@section('content')
    <tenant-pos-index  ></tenant-pos-index>
@endsection

@push('scripts')
    <script></script>
@endpush
