@extends('tenant.layouts.auth')

@section('content')
<section class="auth">
    @include('tenant.auth.partials.side_left')
    <article class="auth__form">
        <form class="form-material" id="loginform" method="POST" action="{{ route('password.update') }}">
            @include('tenant.auth.partials.form_logo')
            <h1 class="auth__title">Bienvenido a<br>{{ $company->trade_name }}</h1>
            <p>Su nueva contraseña debe ser diferente de las contraseñas utilizadas anteriormente</p>
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email" class="col-form-label text-md-right">Correo electrónico</label>

                <div class="">
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                        name="email" value="{{ $email ?? old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-form-label text-md-right">Contraseña</label>

                <div class="">
                    <input id="password" type="password"
                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                        required>

                    @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="password-confirm" class="col-form-label text-md-right">Confirmar contraseña</label>

                <div class="">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                        required>
                </div>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-signin btn-block">REINICIAR CONTRASEÑA</button>
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
