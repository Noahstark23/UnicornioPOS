<?php
//CARRITO DE ENTRADAS DE PRODUCTOS
session_start();
$ObjetoCarrito   = json_decode($_POST['MiCarrito']);
if ($ObjetoCarrito->Codigo=="vaciar") {
    unset($_SESSION["CarritoPedido"]);
} else {
    if (isset($_SESSION['CarritoPedido'])) {
        $carrito=$_SESSION['CarritoPedido'];
        if (isset($ObjetoCarrito->Codigo)) {
            $txtCodigo = $ObjetoCarrito->Codigo;
            $producto= $ObjetoCarrito->Producto;
            $marcas = $ObjetoCarrito->Marcas;
            $codmarca = $ObjetoCarrito->Codmarca;
            $modelo = $ObjetoCarrito->Modelo;
            $codmodelo = $ObjetoCarrito->Codmodelo;
            $cantidad = $ObjetoCarrito->Cantidad;
            $opCantidad = $ObjetoCarrito->opCantidad;

            //array_search("whatisearchfor2", array_column(array_column($response, "types"), 0));

            $donde  = array_search($txtCodigo, array_column($carrito, 'txtCodigo')); //unico resultado
            //$apellidos = array_column($registros, 'apellido', 'id');
            //$keys = array_keys(array_column($userdb, 'uid'), 40489); //resultado multiple
            //$keys = array_keys(array_column($userdb, 'uid'), 40489);

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
                    "codmarca"=>$codmarca,
                    "modelo"=>$modelo,
                    "codmodelo"=>$codmodelo,
                    "cantidad"=>$cuanto
                );
            } else {
                $carrito[]=array(
                    "txtCodigo"=>$txtCodigo,
                    "producto"=>$producto,
                    "marcas"=>$marcas,
                    "codmarca"=>$codmarca,
                    "modelo"=>$modelo,
                    "codmodelo"=>$codmodelo,
                    "cantidad"=>$cantidad
                );
            }
        }
    } else {
        $txtCodigo = $ObjetoCarrito->Codigo;
        $producto = $ObjetoCarrito->Producto;
        $marcas = $ObjetoCarrito->Marcas;
        $codmarca = $ObjetoCarrito->Codmarca;
        $modelo = $ObjetoCarrito->Modelo;
        $codmodelo = $ObjetoCarrito->Codmodelo;
        $cantidad = $ObjetoCarrito->Cantidad;
        $carrito[] = array(
            "txtCodigo"=>$txtCodigo,
            "producto"=>$producto,
            "marcas"=>$marcas,
            "codmarca"=>$codmarca,
            "modelo"=>$modelo,
            "codmodelo"=>$codmodelo,
            "cantidad"=>$cantidad
        );
    }
    $carrito = array_values(
        array_filter($carrito, function($v) {
            return $v['cantidad'] > 0;
        })
    );
    $_SESSION['CarritoPedido'] = $carrito;
    echo json_encode($_SESSION['CarritoPedido']);
}
