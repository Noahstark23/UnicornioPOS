<?php
	include('class.consultas.php');
	$filtro    = $_GET["term"];
	$Json      = new Json;
	$marca  = $Json->BuscaMarca($filtro);
	echo  json_encode($marca);
	
?>  