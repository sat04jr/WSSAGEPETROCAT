<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('UTC');
//date.timezone = 'Europe/Madrid';

// Se incluye el archivo que contiene la clase generica
include 'model_clientes_heptan.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" CUSTOMERSHEPTAN", $bodyRequest);

$results = [];

foreach ($params as $client) {
    $CodigoCliente = str_replace("'","''",$client->CodigoCliente);
    $SiglaNacion = str_replace("'","''",$client->SiglaNacion);
    $CifDni = str_replace("'","''",$client->CifDni);
    $CifEuropeo = str_replace("'","''",$client->CifEuropeo);
    $FechaAlta = $client->FechaAlta;
    $CodigoContable = str_replace("'","''",$client->CodigoContable);
    $RazonSocial = utf8_decode(str_replace("'","''",$client->RazonSocial));
    $Nombre = utf8_decode(str_replace("'","''",$client->Nombre));
    $DomicilioFiscal = utf8_decode(str_replace("'","''",$client->DomicilioFiscal));
    $CodigoPostalFiscal = str_replace("'","''",$client->CodigoPostalFiscal);
    $CodigoMunicipioFiscal = str_replace("'","''",$client->CodigoMunicipioFiscal);
    $MunicipioFiscal = utf8_decode(str_replace("'","''",$client->MunicipioFiscal));
    $ProvinciaFiscal = utf8_decode(str_replace("'","''",$client->ProvinciaFiscal));
    $FormadePago = str_replace("'","''",$client->FormadePago);
    $IBAN = str_replace("'","''",$client->IBAN);
    $BIC = str_replace("'","''",$client->BIC);
    $ReferenciaMandato = str_replace("'","''",$client->ReferenciaMandato);
    $IndicadorIva = $client->IndicadorIva;
    $ObservacionesCliente = utf8_decode(str_replace("'","''",$client->ObservacionesCliente));
    $AgruparAlbaranes = $client->AgruparAlbaranes;
    $Telefono = str_replace("'","''",$client->Telefono);
    $Telefono2 = str_replace("'","''",$client->Telefono2);
    $Fax = str_replace("'","''",$client->Fax);
    $EMail1 = str_replace("'","''",$client->EMail1);
    $BajaEmpresaLc = $client->BajaEmpresaLc;
    $FechaBajaLc = $client->FechaBajaLc;
    $CodigoMotivoBajaClienteLc = str_replace("'","''",$client->CodigoMotivoBajaClienteLc);
    $CodigoTipoClienteLc = $client->CodigoTipoClienteLc;
    $PersonaClienteLc = utf8_decode(str_replace("'","''",$client->PersonaClienteLc));
    $EnvioEFactura = $client->EnvioEFactura;
    $EmailEnvioEFactura = str_replace("'","''",$client->EmailEnvioEFactura);
    $PeriodoFacturacion = $client->PeriodoFacturacion;
    $FacturaBase = $client->FacturaBase;
    $NumeroPlazos = $client->NumeroPlazos;
    $DiasPrimerPlazo = $client->DiasPrimerPlazo;
    $DiasEntrePlazos = $client->DiasEntrePlazos;
    $DiasFijos1 = $client->DiasFijos1;
    $DiasFijos2 = $client->DiasFijos2;
    $BLM_EmailFidelCat = str_replace("'","''",$client->BLM_EmailFidelCat);
    $BLM_MovilFidelcat = str_replace("'","''",$client->BLM_MovilFidelcat);
    $BLM_FechaAltaFidelcat = $client->BLM_FechaAltaFidelcat;
    $BLM_FechaBajaFidelcat = $client->BLM_FechaBajaFidelcat;
    $BLM_CodigoTarjetaPuntos = str_replace("'","''",$client->BLM_CodigoTarjetaPuntos);
    $BLM_PuntosClub = $client->BLM_PuntosClub;
    $BLM_NoInteresado = $client->BLM_NoInteresado;
    $DomicilioFactura = utf8_decode(str_replace("'","''",$client->DomicilioFactura));
    $CodigoPostalFactura = str_replace("'","''",$client->CodigoPostalFactura);
    $CodigoMunicipioFactura = str_replace("'","''",$client->CodigoMunicipioFactura);
    $MunicipioFactura = utf8_decode(str_replace("'","''",$client->MunicipioFactura));
    $ProvinciaFactura = utf8_decode(str_replace("'","''",$client->ProvinciaFactura));
    $BLM_WSMetodo = "SetCustomers";
    $BLM_WSProcesado = "0";
    $NContratoFINCOM = $client->NContratoFINCOM;
    if ($AgruparAlbaranes!==0) {
        $AgruparAlbaranes=-1;
    }
	if ($BajaEmpresaLc!==0) {
		$BajaEmpresaLc=-1;
	}
	if ($EnvioEFactura!==0) {
		$EnvioEFactura=-1;
	}
	//Cast de fechas para insert correcto.
	$FechaAlta = castTimestampToInsert($FechaAlta);
	$FechaBajaLc = castTimestampToInsert($FechaBajaLc);
	$BLM_FechaAltaFidelcat = castTimestampToInsert($BLM_FechaAltaFidelcat);
	$BLM_FechaBajaFidelcat = castTimestampToInsert($BLM_FechaBajaFidelcat);
	
    $data = "'".$CodigoCliente."','".$SiglaNacion."','".$CifDni."','".$CifEuropeo."','".$FechaAlta."','".$CodigoContable."','".$RazonSocial."','".$Nombre."','".$DomicilioFiscal."','".$CodigoPostalFiscal."','".$CodigoMunicipioFiscal."','".$MunicipioFiscal."','".$ProvinciaFiscal."','".$FormadePago."','".$IBAN."','".$BIC."','".$ReferenciaMandato."','".$IndicadorIva."','".$ObservacionesCliente."','".$AgruparAlbaranes."','".$Telefono."','".$Telefono2."','".$Fax."','".$EMail1."','".$BajaEmpresaLc."','".$FechaBajaLc."','".$CodigoMotivoBajaClienteLc."','".$CodigoTipoClienteLc."','".$PersonaClienteLc."','".$EnvioEFactura."','".$EmailEnvioEFactura."','".$PeriodoFacturacion."','".$FacturaBase."','".$NumeroPlazos."','".$DiasPrimerPlazo."','".$DiasEntrePlazos."','".$DiasFijos1."','".$DiasFijos2."','".$BLM_EmailFidelCat."','".$BLM_MovilFidelcat."','".$BLM_FechaAltaFidelcat."','".$BLM_FechaBajaFidelcat."','".$BLM_CodigoTarjetaPuntos."','".$BLM_PuntosClub."','".$BLM_NoInteresado."','".$DomicilioFactura."','".$CodigoPostalFactura."','".$CodigoMunicipioFactura."','".$MunicipioFactura."','".$ProvinciaFactura."','".$BLM_WSMetodo."','".$BLM_WSProcesado."','".$NContratoFINCOM."'";
	// echo $data;die;
    $obj->entity = "WSHeptan_Clientes";
    $obj->data = $data;

    $result = $obj->post();
// echo $result;die;
//Si result == 1 insert correcto
    if ($result == 1) {
        $results[] = [
            'CifDni' => $CifDni,
            'ResultCode' => 0,
            'ResultText' => 'Insert Success'
        ];
    } else {
        $results[] = [
            'CifDni' => $CifDni,
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
	$object = new model_clientes_heptan_class;
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
function castTimestampToInsert($fecha) {
	if($fecha != null) {		
		$dateTime = DateTime::createFromFormat('d/m/Y H:i:s', $fecha);
		$returnFecha = $dateTime->format('m/d/Y H:i:s');
	} else {
		$returnFecha = $fecha;
	}
	
	return $returnFecha;
}

//parsea la fecha para que la acepte el insert de la base de datos (m/d/Y) con explode
// function castDateTimeToInsert($fecha) {
	// if($fecha != null) {
		// $arrayFecha = explode("/",$fecha);
		// $returnFecha = $arrayFecha[1]."/".$arrayFecha[0]."/".$arrayFecha[2];
	// } else {
		// $returnFecha = $fecha;
	// }
	
	// return $returnFecha;
// }

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($response) {
	//print_r( $data);
	header("HTTP/1.1");
	header("Content-Type: application/json; charset=UTF-8");

	echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
    Traza(" CUSTOMERSHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>
