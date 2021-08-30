@extends('tenant.layouts.app')

@section('content')
    <tenant-account-payment-index></tenant-account-payment-index>
@endsection


@push('scripts')
<script src="https://checkout.culqi.com/js/v3"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.31.1/dist/sweetalert2.all.min.js"></script>

<script>
    Culqi.publicKey = "{{$token_public_culqui}}";
    Culqi.options({
        installments: true
    });

    var price_culqi_payment_account = 0;
    var price_payment_account = 0;

    var id_payment_account = null;



    function execCulqi(id, payment) {

        id_payment_account  = id
        price_culqi_payment_account =  Math.round( Number(payment).toFixed(2))
       
        price_payment_account = Math.round((Number(payment).toFixed(2)) * 100)

        Culqi.settings({
            title: "Pago de Cuenta Facturador",
            currency: 'PEN',
            description: 'Pago programado facturador',
            amount: price_payment_account
        });

        Culqi.open();

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

            var token = Culqi.token.id;
            var email = Culqi.token.email;
            var installments = Culqi.token.metadata.installments;
            let items = [{ description: 'Pago programado facturador', cantidad: '1', unit_type_id: 'NIU' }]
            var data = {
                producto: 'Pago Progamado Cuenta Facturador Pro',
                precio: price_payment_account,
                precio_culqi: price_culqi_payment_account,
                token: token,
                email: email,
                installments: installments,
                id_payment_account: id_payment_account,
                items: items
            }

            $.ajax({
                url: "{{route('tenant.account.payment_culqui')}}",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                dataType: 'JSON',
                success: function (data) {

                    if (data.success == true) {
                        swal({
                            title: "Gracias por su pago!",
                            text: "En breve le enviaremos un correo electronico con los detalles de su compra.",
                            type: "success"
                        }).then((x) => {
                            location.reload();
                        })
                    } else {
                        swal({
                            title: "Pago No realizado!",
                            text: data.message,
                            type: "error"
                        }).then((x) => {
                            location.reload();
                        })
                    }
                },
                error: function (error_data) {
                    swal({
                            title: "Pago No realizado!",
                            text: "Tuvimos un problema al procesar el pago.",
                            type: "error"
                        }).then((x) => {
                            location.reload();
                        })
                }
            });

        } else {
            console.log(Culqi.error);
            swal("Pago No realizado", Culqi.error.user_message, "error");
        }
    };

</script>
@endpush
