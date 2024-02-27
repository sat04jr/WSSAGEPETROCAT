<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_locations_heptan.php';

$bodyRequest = file_get_contents("php://input");

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);

//Parametros de la descarga
$CodigoDescarga = $params->CodigoDescarga;
$CodigoCliente = $params->CodigoCliente;
$DomicilioDescarga = $params->DomicilioDescarga;
$CodigoMunicipioDescarga = $params->CodigoMunicipioDescarga;
$MunicipioDescarga = $params->MunicipioDescarga;
$CodigoPostalDescarga = $params->CodigoPostalDescarga;
$CodigoProvinciaDescarga = $params->CodigoProvinciaDescarga;
$CodigoVendedor = $params->CodigoVendedor;
$MetrosManguera = $params->MetrosManguera;
$IdDelegacion = $params->IdDelegacion;
$FechaCaducidadCAE = $params->FechaCaducidadCAE;
$CAE = $params->CAE;
$CodigoCanal = $params->CodigoCanal;
$CodigoComisionista = $params->CodigoComisionista;
$FormadePago = $params->FormadePago;
$NumeroPlazos = $params->NumeroPlazos;
$DiasPrimerPlazo = $params->DiasPrimerPlazo;
$DiasEntrePlazos = $params->DiasEntrePlazos;
$DiasFijos1 = $params->DiasFijos1;
$DiasFijos2 = $params->DiasFijos2;
$BIC = $params->BIC;
$IBAN = $params->IBAN;
$ReferenciaMandato = $params->ReferenciaMandato;
$Telefono = $params->Telefono;
$Telefono2 = $params->Telefono2;
$Fax = $params->Fax;
$EMail1 = $params->EMail1;
$PersonaClienteLc = $params->PersonaClienteLc;
$ClienteFinal = $params->ClienteFinal;
$CIM = $params->CIM;
$DomicilioFactura = $params->DomicilioFactura;
$CodigoPostalFactura = $params->CodigoPostalFactura;
$CodigoMunicipioFactura = $params->CodigoMunicipioFactura;
$MunicipioFactura = $params->MunicipioFactura;
$ProvinciaFactura = $params->ProvinciaFactura;
$CodigoAutonomiaFactura = $params->CodigoAutonomiaFactura;
$CodigoPaisFactura = $params->CodigoPaisFactura;
$FacturacionElectronica = $params->FacturacionElectronica;
$EnvioEFactura = $params->EnvioEFactura;
$EmailEnvioEFactura = $params->EmailEnvioEFactura;
$BLM_AAPPOficinaContable = $params->BLM_AAPPOficinaContable;
$BLM_AAPPOficinaContableNombre = $params->BLM_AAPPOficinaContableNombre;
$BLM_AAPPOrganoGestor = $params->BLM_AAPPOrganoGestor;
$BLM_AAPPOrganoGestorNombre = $params->BLM_AAPPOrganoGestorNombre;
$BLM_AAPPUnidadTramitadora = $params->BLM_AAPPUnidadTramitadora;
$BLM_AAPPUnidadTramitadoraNom = $params->BLM_AAPPUnidadTramitadoraNom;

$idDescarga = createGUID();

$data = "'".$CodigoDescarga."','".$CodigoCliente."','".$DomicilioDescarga."','".$CodigoMunicipioDescarga."','".$MunicipioDescarga."','".$CodigoPostalDescarga."','".$CodigoProvinciaDescarga."','".$CodigoVendedor."','".$MetrosManguera."','".$IdDelegacion."','".$FechaCaducidadCAE."','".$CAE."','".$CodigoCanal."','".$CodigoComisionista."','".$FormadePago."','".$NumeroPlazos."','".$DiasPrimerPlazo."','".$DiasEntrePlazos."','".$DiasFijos1."','".$DiasFijos2."','".$BIC."','".$IBAN."','".$ReferenciaMandato."','".$Telefono."','".$Telefono2."','".$Fax."','".$EMail1."','".$PersonaClienteLc."','".$ClienteFinal."','".$CIM."','".$DomicilioFactura."','".$CodigoPostalFactura."','".$CodigoMunicipioFactura."','".$MunicipioFactura."','".$ProvinciaFactura."','".$CodigoAutonomiaFactura."','".$CodigoPaisFactura."','".$FacturacionElectronica."','".$EnvioEFactura."','".$EmailEnvioEFactura."','".$BLM_AAPPOficinaContable."','".$BLM_AAPPOficinaContableNombre."','".$BLM_AAPPOrganoGestor."','".$BLM_AAPPOrganoGestorNombre."','".$BLM_AAPPUnidadTramitadora."','".$BLM_AAPPUnidadTramitadoraNom."','".$idDescarga."'";

$obj->entity = "WSHeptan_Descargas";
$obj->data = $data;

$result = $obj->post();

//Parametros de los depositos de la descarga
$Depositos = $params->Depositos;

$obj->depositosEntity = "WSHeptan_Depositos";

$depositosThereAre = count($Depositos);
$depositosInsert = 0;

foreach ($Depositos as $deposito) {

    $CodigoDeposito = $deposito->CodigoDeposito;
    $DomicilioDeposito = $deposito->DomicilioDeposito;
    $TipoDeposito = $deposito->TipoDeposito;
    $CodigoArticulo = $deposito->CodigoArticulo;
    $CapacidadDeposito = $deposito->CapacidadDeposito;
    $ObservacionesDeposito = $deposito->ObservacionesDeposito;
    $TipoDepositoCliente = $deposito->TipoDepositoCliente;
    $BLM_GoBonificado = $deposito->BLM_GoBonificado;

    $dataDepositos = "'".$CodigoDeposito."','".$DomicilioDeposito."','".$TipoDeposito."','".$CodigoArticulo."','" .$CapacidadDeposito."','".$ObservacionesDeposito."','".$TipoDepositoCliente."','".$BLM_GoBonificado."','".$idDescarga ."'";

    $obj->depositosData = $dataDepositos;

    $depositoResult = $obj->postDepositos();

    if ($depositoResult == 1) {
        $depositosInsert++;
    }
}

//Si result == 1 insert correcto
if ($result == 1) {
    if ($depositosInsert == $depositosThereAre) {
        print_json(200, 'Success');
    } else {
        print_json(200, 'Any Deposito fails');
    }
} else {
	print_json(400, 'Fail');
}

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_locations_heptan_class;
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
function print_json($status, $mensaje) {
	//print_r( $data);
	header("HTTP/1.1 $status $mensaje");
	header("Content-Type: application/json; charset=UTF-8");

	$response['Code'] = $status;
	$response['Description'] = $mensaje;
	
	echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
}
?>