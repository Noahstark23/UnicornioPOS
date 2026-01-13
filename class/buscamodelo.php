<?php
	include('class.consultas.php');
	$filtro    = $_GET["term"];
	$Json      = new Json;
	$modelo  = $Json->BuscaModelo($filtro);
	echo  json_encode($modelo);
	
?>  