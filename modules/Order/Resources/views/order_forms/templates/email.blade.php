<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de orden de pedido</title>
    <style>
        body {
            color: #000;
        }
        ul {
            list-style: none;
        }
    </style>
</head>
<body>
<p>Estimad@: 

    {{ $order_form->customer->name }}
  
    , informamos que su orden de pedido ha sido emitida exitosamente.</p>

<ul>

</ul>
</body>
</html>