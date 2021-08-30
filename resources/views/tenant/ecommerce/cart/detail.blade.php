@extends('tenant.layouts.layout_ecommerce_cart.index')
@section('content')

<div class="row" id="app">
    <div class="col-lg-8">
        <div class="cart-table-container">
            <table class="table table-cart">
                <thead>
                    <tr>
                        <th class="product-col">Producto</th>
                        <th class="price-col">Precio</th>
                        <th class="qty-col">Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in records" class="product-row">
                        <td class="product-col">
                            <figure class="product-image-container">
                                <a href="#" class="product-image">
                                    <img :src=" '/storage/uploads/items/' + row.image" alt="product">
                                </a>
                            </figure>
                            <h2 class="product-title">
                                <a href="#">@{{ row.name }}</a>
                            </h2>
                        </td>
                        <td>S/ @{{ row.sale_unit_price }}</td>
                        <td>
                            <input class="vertical-quantity form-control input_quantity" :data-product="row.id"
                                type="text">
                        </td>
                        <td>@{{ row.sub_total }}</td>
                        <td>
                            <button type="button" @click="deleteItem(row.id, index)"
                                class="btn btn-outline-danger btn-sm"><i class="icon-cancel"></i></button>
                        </td>
                    </tr>

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="4" class="clearfix">
                            <div class="float-left">
                                <a href="/ecommerce" class="btn btn-outline-secondary">Continar Comprando</a>
                            </div><!-- End .float-left -->

                            <div class="float-right">
                                <a href="#" @click="clearShoppingCart"
                                    class="btn btn-outline-secondary btn-clear-cart">Limpiar Carrito</a>
                                <!--<a href="#" class="btn btn-outline-secondary btn-update-cart">Update Shopping Cart</a> -->
                            </div><!-- End .float-right -->
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div><!-- End .cart-table-container -->
    </div><!-- End .col-lg-8 -->

    <div class="col-lg-4">
        <div class="cart-summary">
            <h3>Resumen</h3>
            <table class="table table-totals">
                <tbody>
                    <tr>
                        <td>Subtotal</td>
                        <td>S/ @{{summary.subtotal}}</td>
                    </tr>

                    <tr>
                        <td>IGV</td>
                        <td>S/ @{{summary.tax}}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Orden Total</td>
                        <td>S/ @{{summary.total}}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="checkout-methods text-center">

                @guest
                <a href="{{route('tenant_ecommerce_login')}}" class="btn btn-block btn-sm btn-primary login-link">Pagar
                    con VISA</a>
                <a href="{{route('tenant_ecommerce_login')}}" class="btn btn-block btn-sm btn-primary login-link">Pagar
                    con EFECTIVO</a>
                <a style="margin-left:15%" href="{{route('tenant_ecommerce_login')}}"
                    class="btn btn-block btn-sm login-link">
                    <img src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" alt="">
                </a>

                @else
                <button class="btn btn-block btn-sm btn-primary" onclick="execCulqi()"> Pagar con VISA </button>

                <button @click="payment_cash.clicked = !payment_cash.clicked" class="btn btn-block btn-sm btn-primary">
                    Pagar con EFECTIVO </button>
                <div v-show="payment_cash.clicked" style="margin: 3%" class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input readonly placeholder="0.0" v-model="payment_cash.amount" type="text"
                            onkeypress="return isNumberKey(event)" maxlength="14" class="form-control"
                            aria-label="Amount">
                        <button @click="paymentCash" class="btn btn-success">OK!</button>
                    </div>
                </div>

                <form class="btn btn-block btn-sm " action="https://www.paypal.com/cgi-bin/webscr" method="post"
                    target="_blank">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="cristian.ballon@gmail.com">
                    <input type="hidden" name="lc" value="AL">
                    <input type="hidden" name="item_name" value="compras ecommerce">
                    <input type="hidden" name="item_number" value="0001">
                    <input type="hidden" name="button_subtype" value="services">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="currency_code" value="PEN">
                    <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
                    <input type="image" src="https://simple-membership-plugin.com/wp-content/uploads/2018/09/paypal-smart-payment-button-for-simple-membership.jpg" border="0"
                        name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1"
                        height="1">
                </form>



                @endguest

            </div><!-- End .checkout-methods -->
        </div><!-- End .cart-summary -->
    </div><!-- End .col-lg-4 -->


    <div class="modal fade" id="modal_ask_document" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="exampleModalCenterTitle">Generar Comprobante Electronico</h2>

                </div>
                <div class="modal-body">
                    <h3>La Transacción de se realizó correctamente.</h3>
                    <h4>¿ Desea generar un comprobante y enviarlo a su email ? </h4> <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="checkDocument('6')">SI, FACTURA</button>
                    <button type="button" class="btn btn-primary" @click="checkDocument('1')">SI, BOLETA
                        ELECTRONICA</button>
                    <button type="button" class="btn btn-secondary" @click="redirectHome" data-dismiss="modal">No,
                        NINGUNA</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_identity_document" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Datos Generales para el Comprobante</h3>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label class="control-label">Tipo Doc. Identidad <span class="text-danger">*</span></label>
                            <select class="form-control" :disabled="formIdentity.identity_document_type_id == '6'"
                                v-model="formIdentity.identity_document_type_id">
                                <option v-for="option in identity_document_types" :value="option.id"
                                    :label="option.description"></option>
                            </select>

                        </div>
                        <div class="form-group">
                            <label class="control-label">Número <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" v-model="formIdentity.number"
                                    :maxlength="maxLength" aria-label="Recipient's username"
                                    aria-describedby="button-addon2">
                                <div class="input-group-append">

                                    <button :disabled="!formIdentity.number" @click.prevent="searchCustomer"
                                        class="btn btn-outline-secondary" type="button" id="button-addon2">

                                        <template v-if="formIdentity.identity_document_type_id === '6'">
                                            <i class="icon-search"></i> <span>SUNAT</span>
                                        </template>
                                        <template v-if="formIdentity.identity_document_type_id === '1'">
                                            <i class="icon-search"></i> <span>RENIEC</span>
                                        </template>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                    <div v-show="response_search.message" class="alert"
                        :class="{'alert-danger' : !response_search.success, 'alert-success': response_search.success}"
                        role="alert">
                        @{{ response_search.message }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCELAR</button>
                    <button type="button" class="btn btn-primary" @click="sendDocument"
                        v-show="formIdentity.validate">ENVIAR</button>
                </div>
            </div>
        </div>
    </div>
</div><!-- End .row -->

<input type="hidden" id="total_amount" data-total="0.0">

@endsection

@push('scripts')
<script src="https://checkout.culqi.com/js/v3"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.31.1/dist/sweetalert2.all.min.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>


<script type="text/javascript">
    var app_cart = new Vue({
        el: '#app',
        data: {
            payment_cash: {
                amount: '',
                clicked: false
            },
            response_search: {},
            loading_search: false,
            identity_document_types: [{
                id: '1',
                description: 'DNI'
            }, {
                id: '6',
                description: 'RUC'
            }],
            formIdentity: {
                identity_document_type_id: '6'
            },
            records: [],
            records_old: [],
            order_generated: {},
            summary: {
                subtotal: '0.0',
                tax: '0.0',
                total: '0.0'
            },
            form_document: {},
            user: {},
            typeDocumentSelected: ''
        },
        computed: {
            maxLength: function () {
                if (this.formIdentity.identity_document_type_id === '6') {
                    return 11
                }
                if (this.formIdentity.identity_document_type_id === '1') {
                    return 8
                }
            }
        },
        mounted() {

            let contex = this
            $(".input_quantity").change(function (e) {
                let value = parseFloat($(this).val())
                let id = $(this).data('product')
                let row = contex.records.find(x => x.id == id)
                row.sub_total = (parseFloat(row.sale_unit_price) * value).toFixed(2)
                row.cantidad = value
                contex.calculateSummary()
            });
            this.calculateSummary()
        },
        created() {
            let array = localStorage.getItem('products_cart');
            array = JSON.parse(array)
            if (array) {
                this.records = array.map(function (item) {
                    let obj = item
                    obj.cantidad = 1
                    obj.sub_total = parseFloat(item.sale_unit_price).toFixed(2)
                    return obj
                })
            }
            this.initForm();
        },
        methods: {
            getFormPaymentCash() {
                let precio = Math.round(Number(this.summary.total) * 100).toFixed(2);
                let precio_culqi = Number(this.summary.total)
                return {
                    producto: 'Compras Ecommerce Facturador Pro',
                    precio: precio,
                    precio_culqi: precio_culqi,
                    customer: this.form_document.datos_del_cliente_o_receptor,
                    items: this.records
                }
            },
            async paymentCash() {

                swal({
                    title: "Estamos generando el Pago.",
                    text: `Por favor no cierre esta ventana hasta que el proceso termine.`,
                    focusConfirm: false,
                    onOpen: () => {
                        Swal.showLoading()
                    }
                });

                let url_finally = '{{ route("tenant_ecommerce_payment_cash")}}';
                let response = await axios.post(url_finally, this.getFormPaymentCash(), this
                    .getHeaderConfig())
                if (response.data.success) {

                    this.clearShoppingCart()
                    swal({
                        title: "Gracias por su pago!",
                        text: "En breve le enviaremos un correo electronico con los detalles de su compra.",
                        type: "success"
                    }).then((x) => {
                        askedDocument(response.data.order);
                    })
                } else {
                    swal("Pago No realizado", 'Sucedio algo inesperado.', "error");
                }

            },
            redirectHome() {
                window.location = "{{ route('tenant.ecommerce.index') }}";
            },
            async searchCustomer() {
                this.response_search = {
                    succes: false,
                    message: ''
                }
                let identity_document_type_name = ''
                if (this.formIdentity.identity_document_type_id === '6') {
                    identity_document_type_name = 'ruc'
                }
                if (this.formIdentity.identity_document_type_id === '1') {
                    identity_document_type_name = 'dni'
                }

                let response = await axios.get(
                    `/services/${identity_document_type_name}/${this.formIdentity.number}`)

                if (response.data.success) {
                    this.response_search.success = response.data.success
                    this.response_search.message = 'Datos Encontrados'
                    // let data = response.data.data
                    this.formIdentity.validate = true
                    this.form_document.datos_del_cliente_o_receptor.codigo_tipo_documento_identidad = this
                        .formIdentity.identity_document_type_id
                    this.form_document.datos_del_cliente_o_receptor.numero_documento = this.formIdentity
                        .number
                    /* this.form.name = data.name
                     this.form.trade_name = data.trade_name
                     this.form.address = data.address
                     this.form.department_id = data.department_id
                     this.form.province_id = data.province_id
                     this.form.district_id = data.district_id
                     this.form.phone = data.phone*/
                } else {
                    this.response_search.success = response.data.success
                    this.response_search.message = response.data.message

                    this.form_document.datos_del_cliente_o_receptor.codigo_tipo_documento_identidad = "0"
                    this.form_document.datos_del_cliente_o_receptor.numero_documento = "0"
                }

            },
            getHeaderConfig() {
                let token = this.user.api_token
                let axiosConfig = {
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`
                    }
                };
                return axiosConfig;
            },
            checkDocument(typeDocument) {
                this.formIdentity.identity_document_type_id = typeDocument
                //this.typeDocumentSelected = typeDocument
                let total = this.summary.total

                if (total > 700 || typeDocument === '6') {
                    let tipoDocumento = this.user.identity_document_type_id
                    let number = this.user.number

                    if (!tipoDocumento || !number || number.length !== 11) {
                        $('#modal_identity_document').modal('show');
                    } else {
                        this.form_document.datos_del_cliente_o_receptor.codigo_tipo_documento_identidad =
                            tipoDocumento
                        this.form_document.datos_del_cliente_o_receptor.numero_documento = number
                        this.sendDocument()
                    }

                } else {
                    this.sendDocument()
                }

            },
            finallyProcess(form) {
                let url_finally = '{{ route("tenant_ecommerce_transaction_finally")}}';
                axios.post(url_finally, form, this.getHeaderConfig())
                    .then(response => {
                        console.log('transaccion finalizada correctamente')
                        swal({
                            title: "Gracias por su pago!",
                            text: "La Transacción de su compra se finalizó correctamente. El Comprobante y detalle de su compra se envió a su correo.",
                            type: "success"
                        }).then((x) => {
                            this.redirectHome()
                        })
                    })
                    .catch(error => {
                        console.log(error)
                        console.log('error al finalizar la transaccion')
                    });

            },
            sendDocument() {

                $('#modal_ask_document').modal('hide');
                $('#modal_identity_document').modal('hide');

                swal({
                    title: "Estamos enviando el Comprobante a su Email",
                    text: `Por favor no cierre esta ventana hasta que el proceso termine.`,
                    focusConfirm: false,
                    onOpen: () => {
                        Swal.showLoading()
                    }
                });

                axios.post('/api/documents', this.getDocument(), this.getHeaderConfig())
                    .then(response => {
                        console.log('documento generado correctamente')
                        this.finallyProcess(this.getDataFinally(response.data))
                    })
                    .catch(error => {
                        console.log(error)
                        console.log('error al generar documento')
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Sucedió un error al generar el comprobante electronico!',
                        }).then((x) => {
                            window.location = "{{ route('tenant.ecommerce.index') }}";
                        })

                    });

            },
            getDataFinally(document) {
                return {
                    document_external_id: document.data.external_id,
                    number_document: document.number,
                    orderId: this.order_generated.id,
                    product: 'Compras Ecommerce Facturador Pro',
                    precio_culqi: this.summary.total,
                    identity_document_type_id: this.formIdentity.identity_document_type_id,
                    number: this.formIdentity.number,

                }
            },
            getDocument() {
                this.form_document.items = this.getItemsDocument()
                this.form_document.totales = this.getTotales()

                if (this.formIdentity.identity_document_type_id === '6') {
                    this.form_document.serie_documento = 'F001'
                    this.form_document.codigo_tipo_documento = '01'
                }
                if (this.formIdentity.identity_document_type_id === '1') {
                    this.form_document.serie_documento = 'B001'
                    this.form_document.codigo_tipo_documento = '03'
                }
                return this.form_document
            },
            getTotales() {
                return {
                    "total_exportacion": 0.00,
                    "total_operaciones_gravadas": 100.00,
                    "total_operaciones_inafectas": 0.00,
                    "total_operaciones_exoneradas": 0.00,
                    "total_operaciones_gratuitas": 0.00,
                    "total_igv": 18.00,
                    "total_impuestos": 18.00,
                    "total_valor": 100,
                    "total_venta": 118
                }
            },
            getItemsDocument() {
                return this.records_old.map((item) => {
                    return {
                        "codigo_interno": "0",
                        "descripcion": item.description,
                        "codigo_producto_sunat": "0",
                        "unidad_de_medida": "NIU",
                        "cantidad": item.cantidad,
                        "valor_unitario": 50,
                        "codigo_tipo_precio": "01",
                        "precio_unitario": item.sale_unit_price,
                        "codigo_tipo_afectacion_igv": "10",
                        "total_base_igv": 100.00,
                        "porcentaje_igv": 18,
                        "total_igv": 18,
                        "total_impuestos": 18,
                        "total_valor_item": 100,
                        "total_item": 118
                    }
                })
            },
            initForm() {
                this.user = JSON.parse('{!! json_encode( Auth::user() ) !!}')
                if(!this.user){
                    return false
                }

                this.form_document = {
                    "acciones": {
                        "enviar_email": true,
                        "formato_pdf": "a4"
                    },
                    "serie_documento": "",
                    "numero_documento": "#",
                    "fecha_de_emision": moment().format('YYYY-MM-DD'),
                    "hora_de_emision": moment().format('HH:mm:ss'),
                    "codigo_tipo_operacion": "0101",
                    "codigo_tipo_documento": "01",
                    "codigo_tipo_moneda": "PEN",
                    "fecha_de_vencimiento": moment().format('YYYY-MM-DD'),
                    "numero_orden_de_compra": "000001",
                    "datos_del_cliente_o_receptor": {
                        "codigo_tipo_documento_identidad": "0",
                        "numero_documento": "0",
                        "apellidos_y_nombres_o_razon_social": this.user.name,
                        "codigo_pais": "PE",
                        "ubigeo": "150101",
                        "direccion": "",
                        "correo_electronico": this.user.email,
                        "telefono": ""
                    },
                    "totales": {},
                    "items": [],
                    "informacion_adicional": "Forma de pago:Efectivo|Caja: 1"
                }

            },
            deleteItem(id, index) {
                //remove en fronted
                this.records.splice(index, 1)
                //set remove en localstorage
                let array = localStorage.getItem('products_cart');
                array = JSON.parse(array);
                let indexFound = array.findIndex(x => x.id == id)
                array.splice(indexFound, 1);
                localStorage.setItem('products_cart', JSON.stringify(array));

                this.calculateSummary()


            },
            clearShoppingCart() {
                this.records_old = this.records
                this.records = []
                localStorage.setItem('products_cart', JSON.stringify([]))
                this.calculateSummary()
            },
            calculateSummary() {
                let subtotal = 0.00
                this.records.forEach(function (item) {
                    //console.log(item)
                    subtotal += parseFloat(item.sub_total)
                })

                this.summary.subtotal = subtotal.toFixed(2)
                let tax = (subtotal * 0.18)
                this.summary.tax = tax.toFixed(2)
                this.summary.total = (subtotal + tax).toFixed(2)
                $("#total_amount").data('total', this.summary.total);

                this.payment_cash.amount = this.summary.total
            }
        }
    })

</script>

<script>
    Culqi.publicKey = 'pk_test_is5j26CmbQPQ6gFX';
    Culqi.options({
        installments: true
    });

    async function askedDocument(order) {
        app_cart.order_generated = order
        $('#modal_ask_document').modal('show')
    }

    function execCulqi() {

        let precio = Math.round((Number($("#total_amount").data('total')) * 100).toFixed(2));
        if (precio > 0) {
            Culqi.settings({
                title: "Productos Ecommerce",
                currency: 'PEN',
                description: 'Compras Ecommerce Facturador Pro',
                amount: precio
            });
            Culqi.open();
        }
    }


    function culqi() {
        if (Culqi.token) {

            swal({
                title: "Estamos hablando con su banco",
                text: `Por favor no cierre esta ventana hasta que el proceso termine.`,
                focusConfirm: false,
                onOpen: () => {
                    Swal.showLoading()
                }
            });

            let precio = Math.round((Number($("#total_amount").data('total')).toFixed(2) * 100));
            let precio_culqi = Number($("#total_amount").data('total')).toFixed(2);

            var url = "/culqi";
            var token = Culqi.token.id;
            var email = Culqi.token.email;
            var installments = Culqi.token.metadata.installments;
            var data = {
                producto: 'Compras Ecommerce Facturador Pro',
                precio: precio,
                precio_culqi: precio_culqi,
                token: token,
                email: email,
                installments: installments,
                customer: JSON.stringify(getCustomer()),
                items: JSON.stringify(getItems())
            }

            $.ajaxajax({
                url: "{{route('tenant_ecommerce_culqui')}}",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                dataType: 'JSON',
                success: function (data) {
                    if (data.success == true) {
                        app_cart.clearShoppingCart();
                        swal({
                            title: "Gracias por su pago!",
                            text: "En breve le enviaremos un correo electronico con los detalles de su compra.",
                            type: "success"
                        }).then((x) => {

                            askedDocument(data.order);
                            //window.location = "{{ route('tenant.ecommerce.index') }}";
                        })
                    } else {
                        const message = data.message
                        swal("Pago No realizado", message, "error");
                    }
                },
                error: function (error_data) {
                    swal("Pago No realizado", error_data, "error");
                }
            });

        } else {
            console.log(Culqi.error);
            swal("Pago No realizado", Culqi.error.user_message, "error");
        }
    };

    function getCustomer() {
        let user = JSON.parse('{!! json_encode( Auth::user() ) !!}')
        return {
            "codigo_tipo_documento_identidad": "0",
            "numero_documento": "0",
            "apellidos_y_nombres_o_razon_social": user.name,
            "codigo_pais": "PE",
            "ubigeo": "150101",
            "direccion": "",
            "correo_electronico": user.email,
            "telefono": ""
        }
    }

    function getItems() {
        return app_cart.records
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

</script>

@endpush
