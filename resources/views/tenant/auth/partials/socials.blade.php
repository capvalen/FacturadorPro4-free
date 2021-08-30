@if ($login->show_socials)
<div class="auth__social">
    @if ($login->facebook)
    <a href="{{ $login->facebook }}">
        <i class="fab fa-facebook-f"></i>
    </a>
    @endif
    @if ($login->twitter)
    <a href="{{ $login->twitter }}">
        <i class="fab fa-twitter"></i>
    </a>
    @endif
    @if ($login->instagram)
    <a href="{{ $login->instagram }}">
        <i class="fab fa-instagram"></i>
    </a>
    @endif
    @if ($login->linkedin)
    <a href="{{ $login->linkedin }}">
        <i class="fab fa-linkedin"></i>
    </a>
    @endif
</div>
@endif
