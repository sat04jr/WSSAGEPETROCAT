<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: POST");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_closeshift_heptan.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
//error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" CLOSESHIFTHEPTAN", $bodyRequest);

$results = [];

	$BLM_WSMetodo = "CloseShiftHeptan";
    $BLM_WSFecha = date('m-d-Y H:i');
    $BLM_WSProcesado = "0";

foreach ($params as $closeshift) {
    //Parametros del mandato
	
    $TipoMov = 0;
	$CargoAbono = $closeshift->CargoAbono;
    $CodigoCuenta = str_replace("'","''",$closeshift->CodigoCuenta);
    $ImporteAsiento = $closeshift->ImporteAsiento;
	$Ejercicio = 0;
	$CodigoEmpresa = 1;
	$EmpresaOrigen = 3;
	$CodigoUsuario = 0;
    $FechaAsiento = $closeshift->FechaAsiento;
    $Contrapartida = str_replace("'","''",$closeshift->Contrapartida);
    $Comentario = utf8_decode(str_replace("'","''",$closeshift->Comentario));
    $CodigoCanal = str_replace("'","''",$closeshift->CodigoCanal);
    $IdDelegacion = str_replace("'","''",$closeshift->IdDelegacion);
	/*$Año = $closeshift->Año;
	$SerieFactura = $closeshift->SerieFactura;
	$NumeroFactura = $closeshift->NumeroFactura;
	$FechaFactura = $closeshift->FechaFactura;
	$TipoFactura = $closeshift->TipoFactura;
	$CifDni = $closeshift->CifDni;
	$RazonSocial = str_replace("'","''",$closeshift->RazonSocial);*/
    $Asiento = $closeshift->Asiento;
    $CodigoDiario = $closeshift->CodigoDiario;
	$StatusAcumulacion = -1;
	$TipoEntrada = "CA";
    $CodigoConcepto = $closeshift->CodigoConcepto;
	$CodigoDepartamento = "";
	$CodigoProyecto = "";
	$CodigoSeccion = "";
    $DocumentoConta = str_replace("'","''",$closeshift->DocumentoConta);
    $TipoDocumento = str_replace("'","''",$closeshift->TipoDocumento);
    $Metalico347 = $closeshift->Metalico347;
    $NumeroPeriodo = $closeshift->NumeroPeriodo;
	$IDCloseShift = createGUID();
	
	//Cast de fechas para insert correcto
	$FechaAsiento = castDateToInsert($FechaAsiento);

	$LineasPosicion = $IDCloseShift;
	
    $data = "'".$TipoMov."','".$CargoAbono."','".$CodigoCuenta."','".$ImporteAsiento."','".$Ejercicio."','".$CodigoEmpresa."','".$EmpresaOrigen."','".$CodigoUsuario."','".$FechaAsiento."','".$Contrapartida."','".$Comentario."','".$CodigoCanal."','".$IdDelegacion."','".$Asiento."','".$CodigoDiario."','".$StatusAcumulacion."','".$TipoEntrada."','".$CodigoConcepto."','".$CodigoDepartamento."','".$CodigoProyecto."','".$CodigoSeccion."','".$DocumentoConta."','".$TipoDocumento."','".$Metalico347."','".$NumeroPeriodo."',CONVERT(uniqueidentifier,'".$LineasPosicion."'),"."CONVERT(uniqueidentifier,'".$IDCloseShift."'),'".$BLM_WSMetodo."','".$BLM_WSFecha."','".$BLM_WSProcesado."'";


    $obj->entity = "WSHeptan_Movimientos";
    $obj->data = $data;

    $result = $obj->post();
	//echo $result;die;
	//Parametros de las descargas del mandato
/*    $Descargas = $mandate->Descargas;

    $obj->descargasEntity = "WSHeptan_MandatosDescargas";

    $descargasThereAre = count($Descargas);
    $descargasInsert = 0;

    foreach ($Descargas as $descarga) {

        $idDescarga = $descarga->IdDescarga;
		
		if ($idDescarga == "") {
			$idDescarga = createGUID();
		}

        $dataDescargas = "CONVERT(uniqueidentifier, '".$IDMandato."'),CONVERT(uniqueidentifier, '".$idDescarga."'), CONVERT(uniqueidentifier, '".$IDMandato."')";

        $obj->descargasData = $dataDescargas;

        $descargasResult = $obj->postdescargas();

        if ($descargasResult == 1) {
            $descargasInsert++;
        }
    }
*/
    if ($result == 1) {
            $results[] = [
				'IdMovimiento' => $IDCloseShift,
				'ResultCode' => 0,
				'ResultText' => 'Insert Success'
			];
    } else {
        $results[] = [
            'IdMandato' => $IDCloseShift,
            'ResultCode' => 1,
            'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
        ];
    }
}

print_json($results);


//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_closeshift_heptan_class;
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
	header("HTTP/1.1 ");

    //header("Content-Type: application/json; charset=UTF-8");

    echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
    Traza(" CLOSESHIFTHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>