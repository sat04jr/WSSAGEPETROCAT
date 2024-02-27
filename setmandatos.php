<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_mandatos_heptan.php';

$bodyRequest = file_get_contents("php://input");

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);

//Parametros del mandato
$ReferenciaMandato = $params->ReferenciaMandato;
$TipoAdeudo = $params->TipoAdeudo;
$CodigoCliente = $params->CodigoCliente;
$CodigoDeposito = $params->CodigoDeposito;
$PersonaPago = $params->PersonaPago;
$IBAN = $params->IBAN;
$BIC = $params->BIC;
$NombreAcreedor = $params->NombreAcreedor;
$IdAcreedor = $params->IdAcreedor;
$DomicilioAcreedor = $params->DomicilioAcreedor;
$CodigoPostalAcreedor = $params->CodigoPostalAcreedor;
$MunicipioAcreedor = $params->MunicipioAcreedor;
$NacionAcreedor = $params->NacionAcreedor;
$TipoDePago = $params->TipoDePago;
$LugarFirma = $params->LugarFirma;
$FechaFirma = $params->FechaFirma;
$DescripcionMandato = $params->DescripcionMandato;
$StatusProcesado = $params->StatusProcesado;
$StatusBajaLc = $params->StatusBajaLc;
$RemesaHabitual = $params->RemesaHabitual;
$BLM_Autonomo = $params->BLM_Autonomo;
$BLM_WSMetodo = "SetMandatos";
$BLM_WSProcesado = "0";

$data = "'".$ReferenciaMandato."','".$TipoAdeudo."','".$CodigoCliente."','".$CodigoDeposito."','".$PersonaPago."','".$IBAN."','".$BIC."','".$NombreAcreedor."','".$IdAcreedor."','".$DomicilioAcreedor."','".$CodigoPostalAcreedor."','".$MunicipioAcreedor."','".$NacionAcreedor."','".$TipoDePago."','".$LugarFirma."','".$FechaFirma."','".$DescripcionMandato."','".$StatusProcesado."','".$StatusBajaLc."','".$RemesaHabitual."','".$BLM_Autonomo."','".$BLM_WSMetodo."','".$BLM_WSProcesado."'";

$obj->entity = "WSHeptan_Mandatos";
$obj->data = $data;

$result = $obj->post();

//Si result == 1 insert correcto
if ($result == 1) {
    print_json(200, 'Success');
} else {
	print_json(400, 'Fail');
}

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_mandatos_heptan_class;
	return $object;
}

// Esta funcion renderiza la informacion que sera enviada a la base de datos
function renderizeData($keys, $values) {
	
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST':
			# code...
			foreach ($keys as $key => $value) {
				if($key == count($keys) - 1) {
					$str = $str . $value . ") VALUES (";
					
					foreach ($values as $key => $value) {
						if($key == count($values) - 1) {
							$str = $str . "'" . $value . "')";
						} else {
							$str = $str . "'" . $value . "',";
						}
						
					}
				} else {
					if($key == 0) {
						$str = $str . "(" . $value . ",";
					} else {
						$str = $str . $value . ",";
					}
					
				}
			}
			
			return $str;
			break;
		case 'PUT':
			foreach ($keys as $key => $value) {
				if($key == count($keys) - 1) {
					$str = $str . $value . "='" . $values[$key] . "'";
				} else {
					$str = $str . $value . "='" . $values[$key] . "',";
				}
			}
			return $str;
			break;
	}
	
	
	
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje) {
	//print_r( $data);
	header("HTTP/1.1 $status $mensaje");
	header("Content-Type: application/json; charset=UTF-8");

	$response['Code'] = $status;
	$response['Description'] = $mensaje;
	
	echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
}
?>