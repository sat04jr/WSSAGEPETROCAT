<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_mandatos_heptan.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" MANDATESHEPTAN", $bodyRequest);

$results = [];

foreach ($params as $mandate) {
    //Parametros del mandato
    $IDMandato = $mandate->IdMandato;
    $ReferenciaMandato = str_replace("'","''",$mandate->ReferenciaMandato);
    $TipoAdeudo = $mandate->TipoAdeudo;
    $CodigoCliente = str_replace("'","''",$mandate->CifDni);
    $CodigoDeposito = $mandate->IdDeposito;
    $PersonaPago = '';
    $IBAN = str_replace("'","''",$mandate->IBAN);
    $BIC = str_replace("'","''",$mandate->BIC);
    $NombreAcreedor = utf8_decode(str_replace("'","''",$mandate->NombreAcreedor));
    $IdAcreedor = $mandate->IdAcreedor;
    $DomicilioAcreedor = utf8_decode(str_replace("'","''",$mandate->DomicilioAcreedor));
    $CodigoPostalAcreedor = str_replace("'","''",$mandate->CodigoPostalAcreedor);
    $MunicipioAcreedor = utf8_decode(str_replace("'","''",$mandate->MunicipioAcreedor));
    $NacionAcreedor = utf8_decode(str_replace("'","''",$mandate->NacionAcreedor));
    $TipoDePago = $mandate->TipoDePago;
    $LugarFirma = utf8_decode(str_replace("'","''",$mandate->LugarFirma));
    $FechaFirma = $mandate->FechaFirma;
    $DescripcionMandato = utf8_decode(str_replace("'","''",$mandate->DescripcionMandato));
    $StatusProcesado = 0;
    $StatusBajaLc = $mandate->StatusBajaLc;
    $RemesaHabitual = '';
    $BLM_Autonomo = $mandate->BLM_Autonomo;
	$IdDescarga = $mandate->IdDescarga;
    $RutaAdjunto = utf8_decode(str_replace("'","''",$mandate->RutaAdjunto));
    $BLM_WSMetodo = "MandatesHeptan";
    $BLM_WSFecha = date('Y-m-d H:i');
    $BLM_WSProcesado = "0";
	
	//Cast de fechas para insert correcto
	$FechaFirma = castDateToInsert($FechaFirma);

    if ($IDMandato == '') {
        //Generar el GUID y asignarlo a $IDMandato
        $IDMandato = createGUID();
    }
	$LineasPosicion = createGUID();
	
	if (strlen($IdDescarga) < 5){
		$IdDescarga = '00000000-0000-0000-0000-000000000000';
	}

//    $data = "'".$ReferenciaMandato."','".$TipoAdeudo."','".$CodigoCliente."','".$CodigoDeposito."','".$PersonaPago."','".$IBAN."','".$BIC."','".$NombreAcreedor."','".$IdAcreedor."','".$DomicilioAcreedor."','".$CodigoPostalAcreedor."','".$MunicipioAcreedor."','".$NacionAcreedor."','".$TipoDePago."','".$LugarFirma."','".$FechaFirma."','".$DescripcionMandato."','".$StatusProcesado."','".$StatusBajaLc."','".$RemesaHabitual."','".$BLM_Autonomo."',CONVERT(uniqueidentifier,'".$LineasPosicion."'),"."CONVERT(uniqueidentifier,'".$IdDescarga."'),'".$BLM_WSMetodo."','".$BLM_WSFecha."','".$BLM_WSProcesado."',CONVERT(uniqueidentifier,'".$IDMandato."')";
    $data = "'".$ReferenciaMandato."','".$TipoAdeudo."','".$CodigoCliente."','".$CodigoDeposito."','".$PersonaPago."','".$IBAN."','".$BIC."','".$NombreAcreedor."','".$IdAcreedor."','".$DomicilioAcreedor."','".$CodigoPostalAcreedor."','".$MunicipioAcreedor."','".$NacionAcreedor."','".$TipoDePago."','".$LugarFirma."','".$FechaFirma."','".$DescripcionMandato."','".$StatusProcesado."','".$StatusBajaLc."','".$RemesaHabitual."','".$BLM_Autonomo."','".$RutaAdjunto."',CONVERT(uniqueidentifier,'".$LineasPosicion."'),"."CONVERT(uniqueidentifier,'".$IdDescarga."'),'".$BLM_WSMetodo."','".$BLM_WSFecha."','".$BLM_WSProcesado."',CONVERT(uniqueidentifier,'".$IDMandato."')";


    $obj->entity = "WSHeptan_Mandatos";
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
				'IdMandato' => $IDMandato,
				'ResultCode' => 0,
				'ResultText' => 'Insert Success'
			];
    } else {
        $results[] = [
            'IdMandato' => $IDMandato,
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
    Traza(" MANDATESHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>