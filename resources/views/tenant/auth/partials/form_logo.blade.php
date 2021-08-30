@if ($useLoginGlobal)
    @if ($login->logo ?? false)
        @if ($login->show_logo_in_form)
            <img class="auth__logo-form" src="{{ $login->logo }}" alt="Logo formulario" />
        @endif
    @endif
@else
    @if ($login->show_logo_in_form)
        @if($company->logo)
        <img class="auth__logo-form" src="{{ asset('storage/uploads/logos/' . $company->logo) }}" alt="Logo formulario" width="120" />
        @else
        <img class="auth__logo-form" src="{{asset('logo/700x300.jpg')}}" alt="Logo formulario" width="120" />
        @endif
    @endif
@endif
<br>
