@extends('tenant.layouts.app')

@section('content')
    <tenant-documentary-processes :processes='@json($processes)'></tenant-documentary-processes>
@endsection
