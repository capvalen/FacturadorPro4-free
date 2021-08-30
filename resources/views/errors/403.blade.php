<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="sidebar-light sidebar-left-big-icons">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <title>Acceso denegado</title>
 

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('porto-light/vendor/bootstrap/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/animate/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/font-awesome/css/fontawesome-all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/css/theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/css/custom.css') }}" /> 

    <style>
        .body-web {
            height: 100vh;
        }
        #main-wrapper {
            height: 100%;
        }
        .row {
            height: 100%;
            justify-content: center;
            align-items: center;
        }
        .card {
            margin: 0;
        }
        .text-e{
            font-size:25px !important;
        }
    </style>
</head>
<body class="body-web"> 
    <section class="body-error error-outside">
        <div class="center-error">

            <div class="row">
                <div class="col-md-12">
                    <div class="main-error">
                        <h2 class="error-code text-dark text-center font-weight-semibold m-0">403 <i class="fas fa-lock"></i></h2>
                        <p class="error-explanation text-center text-e"><strong>Acceso denegado:</strong> Su cuenta está inactiva, comuníquese con el administrador</p>
                    </div>
                </div> 
            </div>
        </div>
    </section>  
</body>
</html>
