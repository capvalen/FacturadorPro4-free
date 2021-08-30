@extends('tenant.layouts.app')

@section('content')
    <tenant-documentary-offices :offices='@json($offices)'></tenant-documentary-offices>
@endsection
