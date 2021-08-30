@extends('tenant.layouts.app')

@section('content')
    <tenant-login-page :user='{{ $user }}' :configuration='{{ $config }}'></tenant-login-page>
@endsection
