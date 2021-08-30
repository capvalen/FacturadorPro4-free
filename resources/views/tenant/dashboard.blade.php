@extends('tenant.layouts.app')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title" id="1">1. Title will be here</h4>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="/clients">Clientes</a>

                    You are logged in!
                </div>
            </div>
        </div>
    </div>

@endsection
