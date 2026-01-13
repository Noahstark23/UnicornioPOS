<?php
	include('class.consultas.php');
	$filtro    = $_GET["term"];
	$Json      = new Json;
	$producto  = $Json->BuscaProductoC($filtro);
	echo  json_encode($producto);
	
?>  