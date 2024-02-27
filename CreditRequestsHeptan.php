<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: POST");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_CreditRequests_heptan.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" CREDITREQUESTSHEPTAN", $bodyRequest);

$results = [];

foreach ($params as $riesgo) {
    //Parametros del riesgo

    $IDRiesgo = $riesgo->IDRiesgo;
    $CifDni = str_replace("'","''",$riesgo->CifDni);
    $RiesgoCC = $riesgo->RiesgoCC;
    $NumeroCC = "";
	$RiesgoPetrocat = 0;
	$RiesgoAval = 0;
	$RiesgoCCConcedido = 0;
	$FechaAprobacionCC = null;
	$FechaVencimientoCC = null;
	$RiesgoPetrocatConcedido = 0;
    $FechaVencimientoAval = null;
	$FechaSolicitud = $riesgo->FechaSolicitud;
	$RazonRiesgoCCCero = "";
    $TipoRiesgo = 1;
	$StatusSolicitudRiesgo = "P";
	$EMailSolicitante = str_replace("'","''",$riesgo->EMailSolicitante);
	$DiasPrimerPlazo = $riesgo->DiasPrimerPlazo;
	$DiasFijos1 = $riesgo->DiasFijos1;
	$FormadePago = str_replace("'","''",$riesgo->FormadePago);
	$RazonSocial = str_replace("'","''",$riesgo->RazonSocial);
	$Domicilio = str_replace("'","''",$riesgo->Domicilio);
	$Codigopostal = str_replace("'","''",$riesgo->Codigopostal);
	$Municipio = str_replace("'","''",$riesgo->Municipio);
	$Provincia = str_replace("'","''",$riesgo->Provincia);
	$Telefono = str_replace("'","''",$riesgo->Telefono);
    $BLM_WSMetodo = "CreditRequestsHeptan";
    $BLM_WSFecha = date('m-d-Y H:i');
    $BLM_WSProcesado = "0";
	
	//Cast de fechas para insert correcto
	$FechaAprobacionCC = castDateToInsert($FechaAprobacionCC);
	$FechaVencimientoCC = castDateToInsert($FechaVencimientoCC);
	$FechaSolicitud = castDateToInsert($FechaSolicitud);
	$FechaVencimientoAval = castDateToInsert($FechaVencimientoAval);

    if ($IDRiesgo == '') {
        //Generar el GUID y asignarlo a $IDRiesgo
        $IDRiesgo = createGUID();
    }

    $data = "CONVERT(uniqueidentifier, '".$IDRiesgo."'),'".$CifDni."','".$RiesgoCC."','".$NumeroCC."','".$RiesgoPetrocat."','".$RiesgoAval."','".$RiesgoCCConcedido."','".$FechaAprobacionCC."','".$FechaVencimientoCC."','".$RiesgoPetrocatConcedido."','".$FechaVencimientoAval."','".$FechaSolicitud."','".$RazonRiesgoCCCero."','".$TipoRiesgo."','".$StatusSolicitudRiesgo."','".$EMailSolicitante."','".$DiasPrimerPlazo."','".$DiasFijos1."','".$FormadePago."','".$RazonSocial."','".$Domicilio."','".$Codigopostal."','".$Municipio."','".$Provincia."','".$Telefono."','".$BLM_WSMetodo."','".$BLM_WSFecha."','".$BLM_WSProcesado."'";
//Traza(" CREDITREQUESTSHEPTAN", $data);

    $obj->entity = "WSHeptan_Riesgo";
    $obj->data = $data;

    $result = $obj->post();
//	echo $result;die;

    if ($result == 1) {
        $results[] = [
            'IDRiesgo' => $IDRiesgo,
            'ResultCode' => 0,
            'ResultText' => 'Insert Success'
        ];
    } elseif ($result == 2) {
        $results[] = [
            'IDRiesgo' => $IDRiesgo,
            'ResultCode' => 2,
            'ResultText' => 'Duplicate Entry. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
        ];
    } else {
        $results[] = [
            'IDRiesgo' => '',
            'ResultCode' => 1,
            'ResultText' => 'SOLICITUD YA REGISTRADA PARA EL MISMO NIF, FECHA E IMPORTE. '
        ];
    }
}

print_json($results);


//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_riesgos_heptan_class;
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

//parsea la fecha para que la acepte el insert de la base de datos (m/d/Y)
function castDateToInsert($fecha) {
	if($fecha != null) {		
		$dateTime = DateTime::createFromFormat('d/m/Y', $fecha);
		$returnFecha = $dateTime->format('m/d/Y');
	} else {
		$returnFecha = $fecha;
	}
	
	return $returnFecha;
}

function createGUID() {

    // Create a token
    $token      = $_SERVER['HTTP_HOST'];
    $token     .= $_SERVER['REQUEST_URI'];
    $token     .= uniqid(rand(), true);

    // GUID is 128-bit hex
    $hash        = strtoupper(md5($token));

    // Create formatted GUID
    $guid        = '';

    // GUID format is XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX for readability
    $guid .= substr($hash,  0,  8) .
        '-' .
        substr($hash,  8,  4) .
        '-' .
        substr($hash, 12,  4) .
        '-' .
        substr($hash, 16,  4) .
        '-' .
        substr($hash, 20, 12);

    return $guid;

}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($response) {
    //print_r( $data);
    header("HTTP/1.1");
    //header("Content-Type: application/json; charset=UTF-8");

    echo json_encode($response, JSON_PRETTY_PRINT); //, JSON_UNESCAPED_UNICODE);
    Traza(" CREDITREQUESTSTHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>