@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <tenant-configurations-pdf-guide
	            :type-user="{{ json_encode(auth()->user()->type) }}">
	        </tenant-configurations-pdf-guide>
        </div>
    </div>
@endsection
