@extends('tenant.layouts.auth')

@section('content')
<section class="auth">
    @include('tenant.auth.partials.side_left')
    <article class="auth__form">
        <form class="form-material" id="loginform" method="POST" action="{{ route('password.email') }}">
            @include('tenant.auth.partials.form_logo')
            <h1 class="auth__title">Bienvenido a<br>{{ $company->trade_name }}</h1>
            <p>Ingrese su correo electrónico y le enviaremos instrucciones para restablecer su contraseña</p>
            @if (session('status'))
                <br>
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            @csrf

            <div class="form-group">
                <div class="">
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required placeholder="Correo electrónico">

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-group text-center">
                <button class="btn btn-signin btn-block" type="submit">ENVIAR LINK</button>
                <br>
                <a href="{{ url('login') }}" class="btn btn-link">
                    <i class="fa fa-arrow-left mr-2"></i> Regresar al login
                </a>
            </div>
            @include('tenant.auth.partials.socials')
        </form>
    </article>
</section>
@endsection
