<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');
set_time_limit(90);

include 'model_prueba.php';
include 'core/lib.php';

$obj = get_obj();


$d='';
$DomicilioFactura = utf8_decode(str_replace("'","''",$d));
echo "***".$DomicilioFactura."***";
	    $dataCabecera = "'".$DomicilioFactura."'";

		$obj->entityCabecera = "CabeceraAlbaranCliente";
		
		$obj->dataCabecera = $dataCabecera;
		
		$resultCabecera = $obj->postCabecera();

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_prueba_class;
	return $object;
}

?>