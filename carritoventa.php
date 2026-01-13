<?php
//CARRITO DE ENTRADAS DE PRODUCTOS
session_start();
$ObjetoCarrito   = json_decode($_POST['MiCarrito']);
if ($ObjetoCarrito->Codigo=="vaciar") {
    unset($_SESSION["CarritoVenta"]);
} else {
    if (isset($_SESSION['CarritoVenta'])) {
        $carrito=$_SESSION['CarritoVenta'];
        if (isset($ObjetoCarrito->Codigo)) {
            $txtCodigo = $ObjetoCarrito->Codigo;
            $producto= $ObjetoCarrito->Producto;
            $marcas = $ObjetoCarrito->Marcas;
            $modelos = $ObjetoCarrito->Modelos;
            $precio = $ObjetoCarrito->Precio;
            $precio2 = $ObjetoCarrito->Precio2;
            $descproducto = $ObjetoCarrito->Descproducto;
            $ivaproducto = $ObjetoCarrito->Ivaproducto;
            $existencia = $ObjetoCarrito->Existencia;
            $precioconiva = $ObjetoCarrito->Precioconiva;
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
                    "descproducto"=>$descproducto,
                    "ivaproducto"=>$ivaproducto,
                    "existencia"=>$existencia,
                    "precioconiva"=>$precioconiva,
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
                    "descproducto"=>$descproducto,
                    "ivaproducto"=>$ivaproducto,
                    "existencia"=>$existencia,
                    "precioconiva"=>$precioconiva,
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
        $descproducto = $ObjetoCarrito->Descproducto;
        $ivaproducto = $ObjetoCarrito->Ivaproducto;
        $existencia = $ObjetoCarrito->Existencia;
        $precioconiva = $ObjetoCarrito->Precioconiva;
        $cantidad = $ObjetoCarrito->Cantidad;
        $carrito[] = array(
            "txtCodigo"=>$txtCodigo,
            "producto"=>$producto,
            "marcas"=>$marcas,
            "modelos"=>$modelos,
            "precio"=>$precio,
            "precio2"=>$precio2,
            "descproducto"=>$descproducto,
            "ivaproducto"=>$ivaproducto,
            "existencia"=>$existencia,
            "precioconiva"=>$precioconiva,
            "cantidad"=>$cantidad
        );
    }
    $carrito = array_values(
        array_filter($carrito, function($v) {
            return $v['cantidad'] > 0;
        })
    );
    $_SESSION['CarritoVenta'] = $carrito;
    echo json_encode($_SESSION['CarritoVenta']);
}
