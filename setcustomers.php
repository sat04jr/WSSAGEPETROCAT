<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_clientes_heptan.php';

$bodyRequest = file_get_contents("php://input");

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);

$CodigoCliente = $params->CodigoCliente;
$SiglaNacion = $params->SiglaNacion;
$CifDni = $params->CifDni;
$CifEuropeo = $params->CifEuropeo;
$FechaAlta = $params->FechaAlta;
$CodigoContable = $params->CodigoContable;
$RazonSocial = $params->RazonSocial;
$Nombre = $params->Nombre;
$DomicilioFiscal = $params->DomicilioFiscal;
$CodigoPostalFiscal = $params->CodigoPostalFiscal;
$CodigoMunicipioFiscal = $params->CodigoMunicipioFiscal;
$MunicipioFiscal = $params->MunicipioFiscal;
$ProvinciaFiscal = $params->ProvinciaFiscal;
$FormadePago = $params->FormadePago;
$IBAN = $params->IBAN;
$BIC = $params->BIC;
$IndicadorIva = $params->IndicadorIva;
$ObservacionesCliente = $params->ObservacionesCliente;
$AgruparAlbaranes = $params->AgruparAlbaranes;
if ($AgruparAlbaranes!==0) {
    $AgruparAlbaranes=-1;
}
$Telefono = $params->Telefono;
$Telefono2 = $params->Telefono2;
$Fax = $params->Fax;
$EMail1 = $params->EMail1;
$BajaEmpresaLc = $params->BajaEmpresaLc;
$FechaBajaLc = $params->FechaBajaLc;
$CodigoMotivoBajaClienteLc = $params->CodigoMotivoBajaClienteLc;
$CodigoTipoClienteLc = $params->CodigoTipoClienteLc;
$PersonaClienteLc = $params->PersonaClienteLc;
$EnvioEFactura = $params->EnvioEFactura;
$EmailEnvioEFactura = $params->EmailEnvioEFactura;
$PeriodoFacturacion = $params->PeriodoFacturacion;
$FacturaBase = $params->FacturaBase;
$NumeroPlazos = $params->NumeroPlazos;
$DiasPrimerPlazo = $params->DiasPrimerPlazo;
$DiasEntrePlazos = $params->DiasEntrePlazos;
$DiasFijos1 = $params->DiasFijos1;
$DiasFijos2 = $params->DiasFijos2;
$BLM_EmailFidelCat = $params->BLM_EmailFidelCat;
$BLM_MovilFidelcat = $params->BLM_MovilFidelcat;
$BLM_FechaAltaFidelcat = $params->BLM_FechaAltaFidelcat;
$BLM_FechaBajaFidelcat = $params->BLM_FechaBajaFidelcat;
$BLM_CodigoTarjetaPuntos = $params->BLM_CodigoTarjetaPuntos;
$BLM_PuntosClub = $params->BLM_PuntosClub;
$BLM_NoInteresado = $params->BLM_NoInteresado;
$DomicilioFactura = $params->DomicilioFactura;
$CodigoPostalFactura = $params->CodigoPostalFactura;
$CodigoMunicipioFactura = $params->CodigoMunicipioFactura;
$MunicipioFactura = $params->MunicipioFactura;
$ProvinciaFactura = $params->ProvinciaFactura;
$BLM_WSMetodo = "SetCustomers";
$BLM_WSProcesado = "0";

$data = "'".$CodigoCliente."','".$SiglaNacion."','".$CifDni."','".$CifEuropeo."','".$FechaAlta."','".$CodigoContable."','".$RazonSocial."','".$Nombre."','".$DomicilioFiscal."','".$CodigoPostalFiscal."','".$CodigoMunicipioFiscal."','".$MunicipioFiscal."','".$ProvinciaFiscal."','".$FormadePago."','".$IBAN."','".$BIC."','".$IndicadorIva."','".$ObservacionesCliente."','".$AgruparAlbaranes."','".$Telefono."','".$Telefono2."','".$Fax."','".$EMail1."','".$BajaEmpresaLc."','".$FechaBajaLc."','".$CodigoMotivoBajaClienteLc."','".$CodigoTipoClienteLc."','".$PersonaClienteLc."','".$EnvioEFactura."','".$EmailEnvioEFactura."','".$PeriodoFacturacion."','".$FacturaBase."','".$NumeroPlazos."','".$DiasPrimerPlazo."','".$DiasEntrePlazos."','".$DiasFijos1."','".$DiasFijos2."','".$BLM_EmailFidelCat."','".$BLM_MovilFidelcat."','".$BLM_FechaAltaFidelcat."','".$BLM_FechaBajaFidelcat."','".$BLM_CodigoTarjetaPuntos."','".$BLM_PuntosClub."','".$BLM_NoInteresado."','".$DomicilioFactura."','".$CodigoPostalFactura."','".$CodigoMunicipioFactura."','".$MunicipioFactura."','".$ProvinciaFactura."','".$BLM_WSMetodo."','".$BLM_WSProcesado."'";

$obj->entity = "WSHeptan_Clientes";
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