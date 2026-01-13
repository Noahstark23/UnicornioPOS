<?php
//CARRITO DE ENTRADAS DE PRODUCTOS
session_start();
$ObjetoCarrito   = json_decode($_POST['MiCarrito']);
if ($ObjetoCarrito->Codigo=="vaciar") {
    unset($_SESSION["CarritoCompra"]);
} else {
    if (isset($_SESSION['CarritoCompra'])) {
        $carrito=$_SESSION['CarritoCompra'];
        if (isset($ObjetoCarrito->Codigo)) {
            $txtCodigo = $ObjetoCarrito->Codigo;
            $producto= $ObjetoCarrito->Producto;
            $marcas = $ObjetoCarrito->Marcas;
            $modelos = $ObjetoCarrito->Modelos;
            $precio = $ObjetoCarrito->Precio;
            $precio2 = $ObjetoCarrito->Precio2;
            $precio3 = $ObjetoCarrito->Precio3;
            $precio4 = $ObjetoCarrito->Precio4;
            $descproductofact = $ObjetoCarrito->DescproductoFact;
            $descproducto = $ObjetoCarrito->Descproducto;
            $ivaproducto = $ObjetoCarrito->Ivaproducto;
            $precioconiva = $ObjetoCarrito->Precioconiva;
            $lote = $ObjetoCarrito->Lote;
            $fechaelaboracion = $ObjetoCarrito->Fechaelaboracion;
            $fechaexpiracion = $ObjetoCarrito->Fechaexpiracion;
            $fechaexpiracion2 = $ObjetoCarrito->Fechaexpiracion2;
            $fechaexpiracion3 = $ObjetoCarrito->Fechaexpiracion3;
            $optimo = $ObjetoCarrito->Optimo;
            $medio = $ObjetoCarrito->Medio;
            $minimo = $ObjetoCarrito->Minimo;
            $cantidad = $ObjetoCarrito->Cantidad;
            $opCantidad = $ObjetoCarrito->opCantidad;

            //array_search("whatisearchfor2", array_column(array_column($response, "types"), 0));

            $donde  = array_search($txtCodigo, array_column($carrito, 'txtCodigo'));

            if ($donde !== FALSE) {
                if ($opCantidad === '=') {
                    $cuanto = $cantidad;
                } else {
                    $cuanto = $carrito[$donde]['cantidad'] + $cantidad;
                }
                $carrito[$donde] = array(
                    "txtCodigo"=>$txtCodigo,
                    "producto"=>$producto,
                    "marcas"=>$marcas,
                    "modelos"=>$modelos,
                    "precio"=>$precio,
                    "precio2"=>$precio2,
                    "precio3"=>$precio3,
                    "precio4"=>$precio4,
                    "descproductofact"=>$descproductofact,
                    "descproducto"=>$descproducto,
                    "ivaproducto"=>$ivaproducto,
                    "precioconiva"=>$precioconiva,
                    "lote"=>$lote,
                    "fechaelaboracion"=>$fechaelaboracion,
                    "fechaexpiracion"=>$fechaexpiracion,
                    "fechaexpiracion2"=>$fechaexpiracion2,
                    "fechaexpiracion3"=>$fechaexpiracion3,
                    "optimo"=>$optimo,
                    "medio"=>$medio,
                    "minimo"=>$minimo,
                    "cantidad"=>$cuanto
                );
            } else {
                $carrito[]=array(
                    "txtCodigo"=>$txtCodigo,
                    "producto"=>$producto,
                    "marcas"=>$marcas,
                    "modelos"=>$modelos,
                    "precio"=>$precio,
                    "precio2"=>$precio2,
                    "precio3"=>$precio3,
                    "precio4"=>$precio4,
                    "descproductofact"=>$descproductofact,
                    "descproducto"=>$descproducto,
                    "ivaproducto"=>$ivaproducto,
                    "precioconiva"=>$precioconiva,
                    "lote"=>$lote,
                    "fechaelaboracion"=>$fechaelaboracion,
                    "fechaexpiracion"=>$fechaexpiracion,
                    "fechaexpiracion2"=>$fechaexpiracion2,
                    "fechaexpiracion3"=>$fechaexpiracion3,
                    "optimo"=>$optimo,
                    "medio"=>$medio,
                    "minimo"=>$minimo,
                    "cantidad"=>$cantidad
                );
            }
        }
    } else {
        $txtCodigo = $ObjetoCarrito->Codigo;
        $producto = $ObjetoCarrito->Producto;
        $marcas = $ObjetoCarrito->Marcas;
        $modelos = $ObjetoCarrito->Modelos;
        $precio = $ObjetoCarrito->Precio;
        $precio2 = $ObjetoCarrito->Precio2;
        $precio3 = $ObjetoCarrito->Precio3;
        $precio4 = $ObjetoCarrito->Precio4;
        $descproductofact = $ObjetoCarrito->DescproductoFact;
        $descproducto = $ObjetoCarrito->Descproducto;
        $ivaproducto = $ObjetoCarrito->Ivaproducto;
        $precioconiva = $ObjetoCarrito->Precioconiva;
        $lote = $ObjetoCarrito->Lote;
        $fechaelaboracion = $ObjetoCarrito->Fechaelaboracion;
        $fechaexpiracion = $ObjetoCarrito->Fechaexpiracion;
        $fechaexpiracion2 = $ObjetoCarrito->Fechaexpiracion2;
        $fechaexpiracion3 = $ObjetoCarrito->Fechaexpiracion3;
        $optimo = $ObjetoCarrito->Optimo;
        $medio = $ObjetoCarrito->Medio;
        $minimo = $ObjetoCarrito->Minimo;
        $cantidad = $ObjetoCarrito->Cantidad;
        $carrito[] = array(
            "txtCodigo"=>$txtCodigo,
            "producto"=>$producto,
            "marcas"=>$marcas,
            "modelos"=>$modelos,
            "precio"=>$precio,
            "precio2"=>$precio2,
            "precio3"=>$precio3,
            "precio4"=>$precio4,
            "descproductofact"=>$descproductofact,
            "descproducto"=>$descproducto,
            "ivaproducto"=>$ivaproducto,
            "precioconiva"=>$precioconiva,
            "lote"=>$lote,
            "fechaelaboracion"=>$fechaelaboracion,
            "fechaexpiracion"=>$fechaexpiracion,
            "fechaexpiracion2"=>$fechaexpiracion2,
            "fechaexpiracion3"=>$fechaexpiracion3,
            "optimo"=>$optimo,
            "medio"=>$medio,
            "minimo"=>$minimo,
            "cantidad"=>$cantidad
        );
    }
    $carrito = array_values(
        array_filter($carrito, function($v) {
            return $v['cantidad'] > 0;
        })
    );
    $_SESSION['CarritoCompra'] = $carrito;
    echo json_encode($_SESSION['CarritoCompra']);
}
