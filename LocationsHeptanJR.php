<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_locations_heptanJR.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();
$WSMetodo="LocationsHeptan";
//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" LOCATIONSHEPTAN", $bodyRequest);
$response = [];

foreach ($params as $descarga) {
    //Parametros de la descarga
    $idDescarga = $descarga->ID;
    $CodigoCliente = str_replace("'","''",$descarga->CifDni);
    $DomicilioDescarga = utf8_decode(str_replace("'","''",$descarga->DomicilioDescarga));
    $CodigoMunicipioDescarga = $descarga->CodigoMunicipioDescarga;
    $MunicipioDescarga = utf8_decode(str_replace("'","''",$descarga->MunicipioDescarga));
    $CodigoPostalDescarga = $descarga->CodigoPostalDescarga;
    $CodigoProvinciaDescarga = str_replace("'","''",$descarga->CodigoProvinciaDescarga);
    $CodigoVendedor = $descarga->CodigoVendedor;
    $MetrosManguera = $descarga->MetrosManguera;
    $IdDelegacion = $descarga->IdDelegacion;
    $FechaCaducidadCAE = $descarga->FechaCaducidadCAE;
    $CAE = $descarga->CAE;
    $CodigoCanal = $descarga->CodigoCanal;
    $CodigoComisionista = $descarga->CodigoComisionista;
    $FormadePago = $descarga->FormadePago;
    $NumeroPlazos = $descarga->NumeroPlazos;
    $DiasPrimerPlazo = $descarga->DiasPrimerPlazo;
    $DiasEntrePlazos = $descarga->DiasEntrePlazos;
    $DiasFijos1 = $descarga->DiasFijos1;
    $DiasFijos2 = $descarga->DiasFijos2;
    $BIC = $descarga->BIC;
    $IBAN = $descarga->IBAN;
    $ReferenciaMandato = $descarga->ReferenciaMandato;
    $Telefono = utf8_decode($descarga->Telefono);
    $Telefono2 = utf8_decode($descarga->Telefono2);
    $Fax = utf8_decode($descarga->Fax);
    $EMail1 = utf8_decode(str_replace("'","''",$descarga->EMail1));
    $PersonaClienteLc = utf8_decode(str_replace("'","''",$descarga->PersonaClienteLc));
    $ClienteFinal = $descarga->ClienteFinal;
    $CIM = $descarga->CIM;
    $DomicilioFactura = utf8_decode(str_replace("'","''",$descarga->DomicilioFactura));
    $CodigoPostalFactura = $descarga->CodigoPostalFactura;
    $CodigoMunicipioFactura = $descarga->CodigoMunicipioFactura;
    $MunicipioFactura = utf8_decode(str_replace("'","''",$descarga->MunicipioFactura));
    $ProvinciaFactura = utf8_decode(str_replace("'","''",$descarga->ProvinciaFactura));
    $CodigoAutonomiaFactura = $descarga->CodigoAutonomiaFactura;
    $CodigoPaisFactura = $descarga->CodigoPaisFactura;
    $FacturacionElectronica = $descarga->FacturacionElectronica;
    $EnvioEFactura = $descarga->EnvioEFactura;
    $EmailEnvioEFactura = utf8_decode(str_replace("'","''",$descarga->EmailEnvioEFactura));
    $BLM_AAPPOficinaContable = utf8_decode(str_replace("'","''",$descarga->BLM_AAPPOficinaContable));
    $BLM_AAPPOficinaContableNombre = utf8_decode(str_replace("'","''",$descarga->BLM_AAPPOficinaContableNombre));
    $BLM_AAPPOrganoGestor = utf8_decode(str_replace("'","''",$descarga->BLM_AAPPOrganoGestor));
    $BLM_AAPPOrganoGestorNombre = utf8_decode(str_replace("'","''",$descarga->BLM_AAPPOrganoGestorNombre));
    $BLM_AAPPUnidadTramitadora = utf8_decode(str_replace("'","''",$descarga->BLM_AAPPUnidadTramitadora));
    $BLM_AAPPUnidadTramitadoraNom = utf8_decode(str_replace("'","''",$descarga->BLM_AAPPUnidadTramitadoraNom));
    $descripcion = utf8_decode(str_replace("'","''",$descarga->Descripcion));
    $ObservacionesDescarga = utf8_decode(str_replace("'","''",$descarga->ObservacionesDescarga));
    $PeriodoFacturacion = $descarga->PeriodoFacturacion;
    //Cast de fechas para insert correcto.
    //FechaCaducidadCAE
    $FechaCaducidadCAE = castDateToInsert($FechaCaducidadCAE);
    
    if ($idDescarga == "") {
        $idDescarga = createGUID();
    }
	if ($EnvioEFactura!==0) {
		$EnvioEFactura=-1;
	}
	if ($CodigoPaisFactura==0) {
		$CodigoPaisFactura=108;
	}
	
    $LineasPosicionDescarga = createGUID();
    
    $data = "'".$CodigoCliente."','".$DomicilioDescarga."','".$CodigoMunicipioDescarga."','".$MunicipioDescarga."','".$CodigoPostalDescarga."','".$CodigoProvinciaDescarga."','".$CodigoVendedor."','".$MetrosManguera."','".$IdDelegacion."','".$FechaCaducidadCAE."','".$CAE."','".$CodigoCanal."','".$CodigoComisionista."','".$FormadePago."','".$NumeroPlazos."','".$DiasPrimerPlazo."','".$DiasEntrePlazos."','".$DiasFijos1."','".$DiasFijos2."','".$BIC."','".$IBAN."','".$ReferenciaMandato."','".$Telefono."','".$Telefono2."','".$Fax."','".$EMail1."','".$PersonaClienteLc."','".$ClienteFinal."','".$CIM."','".$DomicilioFactura."','".$CodigoPostalFactura."','".$CodigoMunicipioFactura."','".$MunicipioFactura."','".$ProvinciaFactura."','".$CodigoAutonomiaFactura."','".$CodigoPaisFactura."','".$FacturacionElectronica."','".$EnvioEFactura."','".$EmailEnvioEFactura."','".$BLM_AAPPOficinaContable."','".$BLM_AAPPOficinaContableNombre."','".$BLM_AAPPOrganoGestor."','".$BLM_AAPPOrganoGestorNombre."','".$BLM_AAPPUnidadTramitadora."','".$BLM_AAPPUnidadTramitadoraNom."',CONVERT(uniqueidentifier, '".$idDescarga."'),CONVERT(uniqueidentifier, '".$LineasPosicionDescarga."'),'".$descripcion."','".$ObservacionesDescarga."','".$PeriodoFacturacion."','".$WSMetodo."'";
    
    $obj->entity = "WSHeptan_Descargas";
    $obj->data = $data;
    
    $result = $obj->post();
    /*$obj->entity = "WSHeptan_Descargas";
    $LineasPosicionDescarga = $obj->getUltimaDescarga($idDescarga);*/
    //$LineasPosicionDescarga = $result->LineasPosicionDescarga;
    //Parametros de los depositos de la descarga
    $DepositosID=array();
    $Depositos = $descarga->Depositos;
    
    $obj->depositosEntity = "WSHeptan_Depositos";
    
    $depositosThereAre = count($Depositos);
    $depositosInsert = 0;
    
    foreach ($Depositos as $deposito) {
        
        $idDeposito = $deposito->ID;
        $DomicilioDeposito = utf8_decode(str_replace("'","''",$deposito->DomicilioDeposito));
        $TipoDeposito = $deposito->TipoDeposito;
        $CodigoArticulo = $deposito->CodigoArticulo;
        $CapacidadDeposito = $deposito->CapacidadDeposito;
        $ObservacionesDeposito = utf8_decode(str_replace("'","''",$deposito->ObservacionesDeposito));
        $TipoDepositoCliente = $deposito->TipoDepositoCliente;
        $BLM_GoBonificado = $deposito->BLM_GoBonificado;
        
        if ($idDeposito == "") {
            $idDeposito = createGUID();
        }
        
        $dataDepositos = "'".$DomicilioDeposito."','".$TipoDeposito."','".$CodigoArticulo."','" .$CapacidadDeposito."','".$ObservacionesDeposito."','".$TipoDepositoCliente."','".$BLM_GoBonificado."',CONVERT(uniqueidentifier, '".$idDescarga."'),CONVERT(uniqueidentifier, '".$idDeposito."'),CONVERT(uniqueidentifier, '".$LineasPosicionDescarga."'), '".$WSMetodo."'";
        
        $obj->depositosData = $dataDepositos;
        
        $depositoResult = $obj->postDepositos();
        
        if ($depositoResult == 1) {
            $depositosInsert++;
            array_push($DepositosID, $idDeposito);
        }
    }
    //json depositosid
    if (isset($jsonDepositos)){
        unset($jsonDepositos);
    }
    foreach ($DepositosID as $valor) {
        $jsonDepositos[]=[
            'ID' => $valor
        ];
    }
    
    if ($result == 1) {
        if ($depositosInsert == $depositosThereAre) {
            if (isset($jsonDepositos)){
                $bd='Logic';
                $result=$obj->ejecutaprocedimiento($bd,$LineasPosicionDescarga);
                if ($result == 1) {
					$existedeposito= $obj->get($idDeposito);
					if (count($existedeposito) > 1) {
						$response[] = [
							'ID' => $idDescarga,
							'Depositos' => $jsonDepositos,
							'ResultCode' => 0,
							'ResultText' => 'Insert Success'
						];
					} else {
						$response[] = [
							'ID' => $idDescarga,
							'ResultCode' => 1,
							'ResultText' => 'Insert Fail. No se ha podido crear el deposito'
						];
					}
                }else{
                    $response[] = [
                        'ID' => $idDescarga,
                        'ResultCode' => 1,
                        'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
                    ];
                }
            }else{
                $bd='Logic';
                $result=$obj->ejecutaprocedimiento($bd,$LineasPosicionDescarga);
                if ($result == 1) {
                    $response[] = [
                        'ID' => $idDescarga,
                        'ResultCode' => 0,
                        'ResultText' => 'Insert Success'
                    ];
                }else{
                    $response[] = [
                        'ID' => $idDescarga,
                        'ResultCode' => 1,
                        'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
                    ];
                }
            }
        } else {
            $response[] = [
                'ID' => $idDescarga,
                'ResultCode' => 1,
                'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
            ];
        }
    } else {
        $response[] = [
            'ID' => $idDescarga,
            'ResultCode' => 1,
            'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
        ];
    }
}

print_json($response);

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_locations_heptanJR_class;
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
    header("Content-Type: application/json; charset=UTF-8");
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
    Traza(" LOCATIONSHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>