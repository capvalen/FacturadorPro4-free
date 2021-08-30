<article class="auth__image" style="background-image: url({{ $login->image }})">
    @if ($useLoginGlobal)
        @if ($login->logo ?? false)
            @if ($login->position_logo != 'none')
            <img class="auth__logo {{ $login->position_logo }}" src="{{ $login->logo }}" alt="Logo" />
            @endif
        @endif
    @else
        @if($company->logo)
            <img class="auth__logo {{ $login->position_logo }}" src="{{ asset('storage/uploads/logos/' . $company->logo) }}" alt="Logo" />
        @else
            <img class="auth__logo {{ $login->position_logo }}" src="{{asset('logo/700x300.jpg')}}" alt="Logo" />
        @endif
    @endif
</article>
