@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <tenant-configurations-pdf
	            :type-user="{{ json_encode(auth()->user()->type) }}"
                :establishments="{{ json_encode($establishments) }}">
	        </tenant-configurations-pdf>
        </div>
    </div>
@endsection
