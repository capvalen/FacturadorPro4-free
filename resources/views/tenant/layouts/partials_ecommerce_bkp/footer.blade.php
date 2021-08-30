<style>
    .vl {
        border-left: 2px solid black;
        height: 100%;
        margin-left: 30%;
    }

</style>

<div class="footer-middle">
    <div class="container">
        <div class="footer-ribbon">
            Contáctanos
        </div><!-- End .footer-ribbon -->
        <div class="row">
            <div class="col-lg-4">
                <div class="widget">
                    <h4 class="widget-title">Ubicación</h4>
                    <ul class="contact-info">
                        <li>
                            <span class="contact-info-label">Dirección:</span> Jr. Trujillo 123 Lima, Peru
                        </li>
                        <li>
                            <span class="contact-info-label">Teléfono:</span> <a href="tel:">(01) 456-7890</a>
                        </li>
                    </ul>
                </div><!-- End .widget -->
            </div><!-- End .col-lg-3 -->


            <div class="col-md-4">
                <div class="widget">
                    <h4 class="widget-title">Enlaces de interés</h4>
                    <div class="row">
                        <div class="col-sm-6 col-md-5">
                            <ul class="links">
                                <li><a href="{{ route("tenant.ecommerce.index") }}">Inicio</a></li>
                                <li><a href="#">Más vendidos</a></li>
                                <li><a href="#">Populares</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-6 col-md-5">
                            <ul class="links">
                                <li><a href="{{ route('tenant_detail_cart') }}">Ver Carrito</a></li>
                                <li><a href="#">Ver Perfil</a></li>
                                @guest
                                <li><a href="{{route('tenant_ecommerce_login')}}" class="login-link">Login</a></li>
                                @else
                                <li><a role="menuitem" href="{{ route('logout') }}" class="login-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Salir
                                </a></li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="widget">
                    <h4 class="widget-title">Redes Sociales</h4>

                    <div class="social-icons">
                        <a href="#" class="social-icon" target="_blank"><i class="icon-facebook"></i></a>
                        <a href="#" class="social-icon" target="_blank"><i class="icon-twitter"></i></a>
                        <a href="#" class="social-icon" target="_blank"><i class="icon-linkedin"></i></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="container">
    <div class="footer-bottom">
        <p class="footer-copyright">Facturador Pro 3. &copy; 2019. Todos los Derechos Reservados</p>

        <img src="{{ asset('porto-ecommerce/assets/images/payments.png') }}" alt="payment methods"
            class="footer-payments">
    </div><!-- End .footer-bottom -->
</div><!-- End .container -->

<div class="modal fade" id="moda-succes-add-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!--<div class="modal-header ">
                  <h5 class="modal-title" id="exampleModalLabel"></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>-->
            <div class="modal-body">

                <div class="alert alert-success" role="alert">
                    <i class="icon-ok"></i> Tu producto se agregó al carrito
                </div>
                <div class="row">
                    <div id="product_added_image" class="col-md-4">


                    </div>
                    <div class="col-md-8">
                        <div id="product_added" class="product-single-details">

                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('tenant_detail_cart') }}" class="btn btn-primary text-white">Ir a Carrito</a>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Seguir Comprando</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-already-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div style="font-size: 2em;" class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation"></i> Tu Producto ya está agregado al carrito.
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('tenant_detail_cart') }}" class="btn btn-primary text-white">Ir al Carrito</a>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Seguir Comprando</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="login_register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content"  style="height:600px">

            <div class="modal-body">

                <div class="container-fluid ">
                    <br> <br> <br> <br>
                    <div class="row">
                        <div class="col-md-5 ">
                            <h4 class="title mb-2">Login</h4>

                            <div id="msg_login" class="alert alert-danger" role="alert">
                                Usuario o Contraseña Incorrectos.
                            </div>

                            <form action="#" id="form_login">
                                <div class="form-group">
                                    <label for="email">Correo Electronico:</label>
                                    <input type="email" required class="form-control" id="email"
                                        placeholder="Enter email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Contraseña:</label>
                                    <input type="password" required class="form-control" id="pwd"
                                        placeholder="Enter password" name="password">
                                </div>

                                <button type="submit" class="btn btn-primary">Ingresar</button>
                            </form>
                        </div>
                        <div class="col-md-1 text-center">
                            <div class="vl"></div>
                        </div>
                        <div class="col-md-5">
                            <h4 class="title mb-2">Nuevo Registro</h4>
                            <div id="msg_register" class="alert alert-danger" role="alert">
                                <p id="msg_register_p"></p>
                            </div>

                            <form autocomplete="off" action="#" id="form_register">
                                <div class="form-group">
                                    <label for="email">Nombres:</label>
                                    <input type="text" required autocomplete="off" class="form-control" id="name_reg"
                                        placeholder="Enter name" name="name">
                                </div>
                                <div class="form-group">
                                    <label for="email">Correo Electronico:</label>
                                    <input type="email" required autocomplete="off" class="form-control" id="email_reg"
                                        placeholder="Enter email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Contraseña:</label>
                                    <input type="password" required autocomplete="off" class="form-control" id="pwd_reg"
                                        placeholder="Ingrese contraseña" name="pswd">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Repita la Contraseña:</label>
                                    <input type="password" required autocomplete="off" class="form-control"
                                        id="pwd_repeat_reg" placeholder="Repita contraseña" name="pswd">
                                </div>

                                <button type="submit" class="btn btn-primary">Registrarse</button>
                            </form>


                        </div>

                    </div>
                </div>



            </div>

        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript" src="{{ asset('porto-ecommerce/assets/js/cart.js') }}"></script>
<script type="text/javascript">
    matchPassword();
    submitLogin();
    submitRegister();


    function matchPassword() {
        var password = document.getElementById("pwd_reg"),
            confirm_password = document.getElementById("pwd_repeat_reg");

        function validatePassword() {
            if (password.value != confirm_password.value) {
                confirm_password.setCustomValidity("El Password no coincide.");
            } else {
                confirm_password.setCustomValidity('');
            }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    }

    function submitLogin() {
        $('#msg_login').hide();

        $('#form_login').submit(function (e) {
            e.preventDefault()
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('tenant_ecommerce_login')}}",
                data: $(this).serialize(),
                success: function (data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        $('#msg_login').show();
                    }
                },
                error: function (error_data) {
                    console.log(error_data)
                }
            });
        })

    }

    function submitRegister() {
        $('#msg_register').hide();

        $('#form_register').submit(function (e) {
            e.preventDefault()
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('tenant_ecommerce_store_user')}}",
                data: $(this).serialize(),
                success: function (data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        $('#msg_register').show();
                        $('#msg_register_p').text(data.message)
                    }
                },
                error: function (error_data) {
                    console.log(error_data)
                }
            });
        })
    }

</script>
@endpush
