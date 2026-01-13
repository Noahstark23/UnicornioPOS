<?php
	include('class.consultas.php');
	$filtro    = $_GET["term"];
	$Json      = new Json;
	$clientes  = $Json->BuscaClientes($filtro);
	echo  json_encode($clientes);
	
?>  