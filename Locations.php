<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_locations.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
$block = $_GET['Block'];
//$codigoCliente = $_GET['CodigoCliente'];
//$codigoDescarga = $_GET['CodigoDescarga'];
$cifDni = $_GET['CifDni'];
$id = $_GET['ID'];

$select = "CONVERT(char(36), Heptan_Descargas.BLM_IdDescarga) AS ID, BLM_DatosNif.CifDni, Heptan_Descargas.DomicilioDescarga, Heptan_Descargas.CodigoMunicipioDescarga, 
                         Heptan_Descargas.MunicipioDescarga, Heptan_Descargas.CodigoPostalDescarga, Heptan_Descargas.CodigoProvinciaDescarga, Heptan_Descargas.CodigoVendedor, Heptan_Descargas.MetrosManguera, 
                         Heptan_Descargas.IdDelegacion, Heptan_Descargas.FechaCaducidadCAE, Heptan_Descargas.CAE, Heptan_Descargas.CodigoCanal, Heptan_Descargas.CodigoComisionista, Heptan_Descargas.FormadePago, 
                         Heptan_Descargas.NumeroPlazos, CASE WHEN Heptan_Descargas.DiasPrimerPlazo>90 THEN 90 ELSE Heptan_Descargas.DiasPrimerPlazo END DiasPrimerPlazo, Heptan_Descargas.DiasEntrePlazos, Heptan_Descargas.DiasFijos1, Heptan_Descargas.DiasFijos2, ISNULL(Bancos.BIC, '') AS BIC, 
                         Heptan_Descargas.IBAN, Heptan_Descargas.ReferenciaMandato, Heptan_Descargas.Telefono, Heptan_Descargas.Telefono2, Heptan_Descargas.Fax, Heptan_Descargas.EMail1, Heptan_Descargas.PersonaClienteLc, Heptan_Descargas.ClienteFinal, Heptan_Descargas.CIM, Heptan_Descargas.BLM_DomicilioFactura AS DomicilioFactura, 
                         Heptan_Descargas.CodigoPostalFactura AS CodigoPostalFactura, Heptan_Descargas.CodigoMunicipioFactura AS CodigoMunicipioFactura, Heptan_Descargas.MunicipioFactura AS MunicipioFactura, Heptan_Descargas.ProvinciaFactura AS ProvinciaFactura, 
                         Heptan_Descargas.CodigoAutonomiaFactura AS CodigoAutonomiaFactura, Heptan_Descargas.CodigoPaisFactura AS CodigoPaisFactura, Clientes.FacturacionElectronica, Clientes.EnvioEFactura, Clientes.EmailEnvioEFactura, 
                         Clientes.BLM_AAPPOficinaContable, Clientes.BLM_AAPPOficinaContableNombre, Clientes.BLM_AAPPOrganoGestor, Clientes.BLM_AAPPOrganoGestorNombre, Clientes.BLM_AAPPUnidadTramitadora, 
                         Clientes.BLM_AAPPUnidadTramitadoraNom,Heptan_Descargas.Descripcion,Heptan_Descargas.ObservacionesDescarga,Heptan_Descargas.PeriodoFacturacion";

$tablasQuery = "Clientes WITH (nolock) INNER JOIN
                         BLM_DatosNif WITH (nolock) ON Clientes.CifDni = BLM_DatosNif.CifDni INNER JOIN
                         ClientesConta WITH (nolock) ON Clientes.CodigoEmpresa = ClientesConta.CodigoEmpresa AND Clientes.CodigoContable = ClientesConta.CodigoCuenta INNER JOIN
                         Delegaciones WITH (nolock) ON 1 = Delegaciones.CodigoEmpresa AND - 1 = Delegaciones.BLM_DPMobility INNER JOIN
                         Heptan_Descargas WITH (nolock) ON Delegaciones.IdDelegacion = Heptan_Descargas.IdDelegacion AND Clientes.CodigoCliente = Heptan_Descargas.CodigoCliente INNER JOIN
                         Depositos WITH (nolock) ON Heptan_Descargas.BLM_IdDescarga = Depositos.BLM_Heptan_IdDescargaPadre LEFT OUTER JOIN
                         Bancos WITH (nolock) ON SUBSTRING(Heptan_Descargas.IBAN, 5, 4) = Bancos.CodigoBanco LEFT OUTER JOIN
                         Domicilios AS DF WITH (nolock) ON Clientes.DomicilioFactura = DF.NumeroDomicilio AND Clientes.CodigoCliente = DF.CodigoCliente AND 'F' = DF.TipoDomicilio AND 
                         Clientes.CodigoEmpresa = DF.CodigoEmpresa LEFT OUTER JOIN
                         Municipios AS MFAC WITH (nolock) ON MFAC.CodigoMunicipio = DF.CodigoMunicipio";

$where = "(Clientes.CodigoEmpresa = 1)";
$top = "";

//Montamos el where de la query
if ($cifDni != '') {
    $where .= " AND BLM_DatosNif.CifDni = '".$cifDni."'";
}

if ($id != '') {
//    $where .= " AND Deposito.BLM_Heptan_IdDescarga ='".$id."'";
    $where .= " AND Heptan_Descargas.BLM_IdDescarga = CONVERT(uniqueidentifier, '".$id."')";
}

if ($block != null && $block > 0) {
    $top = "TOP ".intVal($block)." ";
}

if ($heptanStatus == 'Pending') {
    $where .= " AND (Depositos.BLM_Heptan_sync = 0)";
}

$query="SELECT DISTINCT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $data[]=$row2;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro) == 0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
    print_json(400, 1, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
	//Sacamos los depositos de cada descarga
    $data = groupDepositos($data, $tablasQuery);
	
    //Pasamos la fecha a formato correcto
    $data = dateTransform($data);

    // Imprime la informacion solicitada
    print_json(200, 0,"Success", $data);
}

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_locations_class;
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

//Transforma a formato correcto la fecha y los numeros de descarga
function dateTransform($data) {
    foreach ($data as $key => $descarga) {
        if($descarga['FechaCaducidadCAE'] != '') {
            $strDate = strtotime($descarga['FechaCaducidadCAE']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaCaducidadCAE'] = $badDate;
        }

		//CodigoVendedor
		if($descarga['CodigoVendedor'] != '') {
			$data[$key]['CodigoVendedor'] = intVal($descarga['CodigoVendedor']);	
		}
		else
			$data[$key]['CodigoVendedor'] = 3;
		
		//MetrosManguera
		if($descarga['MetrosManguera'] != '') {
			$data[$key]['MetrosManguera'] = intVal($descarga['MetrosManguera']);	
		}
		else
			$data[$key]['MetrosManguera'] = 0;
		
		//CodigoComisionista
		if($descarga['CodigoComisionista'] != '') {
			$data[$key]['CodigoComisionista'] = intVal($descarga['CodigoComisionista']);	
		}
		else
			$data[$key]['CodigoComisionista'] = 0;
		
		//NumeroPlazos
		if($descarga['NumeroPlazos'] != '') {
			$data[$key]['NumeroPlazos'] = intVal($descarga['NumeroPlazos']);	
		}
		else
			$data[$key]['NumeroPlazos'] = 0;
		
		//DiasPrimerPlazo
		if($descarga['DiasPrimerPlazo'] != '') {
			$data[$key]['DiasPrimerPlazo'] = intVal($descarga['DiasPrimerPlazo']);	
		}
		else
			$data[$key]['DiasPrimerPlazo'] = 0;
		
		//DiasEntrePlazos
		if($descarga['DiasEntrePlazos'] != '') {
			$data[$key]['DiasEntrePlazos'] = intVal($descarga['DiasEntrePlazos']);	
		}
		else
			$data[$key]['DiasEntrePlazos'] = 0;
		
		//DiasFijos1
		if($descarga['DiasFijos1'] != '') {
			$data[$key]['DiasFijos1'] = intVal($descarga['DiasFijos1']);	
		}
		else
			$data[$key]['DiasFijos1'] = 0;
		
		//DiasFijos2
		if($descarga['DiasFijos2'] != '') {
			$data[$key]['DiasFijos2'] = intVal($descarga['DiasFijos2']);	
		}
		else
			$data[$key]['DiasFijos2'] = 0;
		
		//ClienteFinal
		if($descarga['ClienteFinal'] != '') {
			$data[$key]['ClienteFinal'] = intVal($descarga['ClienteFinal']);	
		}
		else
			$data[$key]['ClienteFinal'] = 0;
		
		//FacturacionElectronica
		if($descarga['FacturacionElectronica'] != '') {
			$data[$key]['FacturacionElectronica'] = intVal($descarga['FacturacionElectronica']);	
		}
		else
			$data[$key]['FacturacionElectronica'] = 0;
		
		//EnvioEFactura
		if($descarga['EnvioEFactura'] != '') {
			$data[$key]['EnvioEFactura'] = intVal($descarga['EnvioEFactura']);	
		}
		else
			$data[$key]['EnvioEFactura'] = 0;
		
		if(count($descarga['Depositos']) > 0) {
			$data[$key]['Depositos'] = dateTransformDepositos($descarga['Depositos']);
		}
		
		//CodigoPaisFactura
		if($descarga['CodigoPaisFactura'] == '') {
			$data[$key]['CodigoPaisFactura'] = '108';	
		}
		
		//FormadePago
		if(($descarga['FormadePago'] != 'CR')&&($descarga['FormadePago'] != 'CC')) {
			$data[$key]['FormadePago'] = 'CR';	
		}
		
    }
    return $data;
}

//Transforma a formato correcto la fecha y los numeros de deposito
function dateTransformDepositos($data) {
    foreach ($data as $key => $deposito) {
		//CapacidadDeposito
		if($deposito['CapacidadDeposito'] != '') {
			$data[$key]['CapacidadDeposito'] = intVal($deposito['CapacidadDeposito']);	
		}
		
		//TipoDepositoCliente
		if($deposito['TipoDepositoCliente'] != '') {
			$data[$key]['TipoDepositoCliente'] = intVal($deposito['TipoDepositoCliente']);	
		}
		
		//BLM_GoBonificado
		if($deposito['BLM_GoBonificado'] != '') {
			$data[$key]['BLM_GoBonificado'] = intVal($deposito['BLM_GoBonificado']);	
		}
    }
    return $data;
}

//Agrupa depositos en descargas
function groupDepositos($data, $tablasQuery) {
    $depositosSelect = "CONVERT(char(36), Depositos.BLM_Heptan_IdDeposito) as ID, Depositos.DomicilioDeposito as DomicilioDeposito, Depositos.TipoDeposito, Depositos.CodigoArticulo, Depositos.CapacidadDeposito, Depositos.ObservacionesDeposito, Depositos.TipoDepositoCliente, Depositos.BLM_GoBonificado";
    $tablasQuery="BLM_PR120_ConversionArticulos INNER JOIN
                         Delegaciones INNER JOIN
                         Clientes WITH (nolock) INNER JOIN
                         BLM_DatosNif WITH (nolock) ON Clientes.CifDni = BLM_DatosNif.CifDni INNER JOIN
                         ClientesConta WITH (nolock) ON Clientes.CodigoEmpresa = ClientesConta.CodigoEmpresa AND Clientes.CodigoContable = ClientesConta.CodigoCuenta ON 1 = Delegaciones.CodigoEmpresa AND 
                         - 1 = Delegaciones.BLM_DPMobility ON 2 = BLM_PR120_ConversionArticulos.CodigoEmpresa AND - 1 = BLM_PR120_ConversionArticulos.BLM_EnvioHeptan INNER JOIN
                         Depositos WITH (nolock) ON BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = Depositos.CodigoArticulo INNER JOIN
                         Heptan_Descargas ON Clientes.CodigoCliente = Heptan_Descargas.CodigoCliente AND Depositos.BLM_Heptan_IdDescargaPadre = Heptan_Descargas.BLM_IdDescarga AND 
                         Delegaciones.IdDelegacion = Heptan_Descargas.IdDelegacion LEFT OUTER JOIN
                         Bancos WITH (nolock) ON SUBSTRING(Clientes.IBAN, 5, 4) = Bancos.CodigoBanco LEFT OUTER JOIN
                         Domicilios AS DF ON Clientes.DomicilioFactura = DF.NumeroDomicilio AND Clientes.CodigoCliente = DF.CodigoCliente AND 'F' = DF.TipoDomicilio AND 
                         Clientes.CodigoEmpresa = DF.CodigoEmpresa LEFT OUTER JOIN
                         Municipios AS MFAC ON MFAC.CodigoMunicipio = DF.CodigoMunicipio";
    foreach ($data as $key => $descarga) {
        $data[$key]['Depositos'] = [];
//        $depositosWhere = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (Depositos.CodigoEmpresa = 3) AND (Clientes.CodigoEmpresa = 1) AND Clientes.CodigoCliente = '".$descarga['CifDni']."' AND Depositos.BLM_Heptan_IdDescarga = CONVERT(uniqueidentifier, '".$descarga['ID']."')";
        $depositosWhere = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (Clientes.CodigoEmpresa = 1) AND Heptan_Descargas.BLM_IdDescarga = CONVERT(uniqueidentifier, '".$descarga['ID']."')
                        AND Depositos.BLM_Heptan_IdDeposito IS NOT NULL";

        $depositosQuery="SELECT ".$depositosSelect." FROM ".$tablasQuery." WHERE ".$depositosWhere;

        $depositos=mssql_query($depositosQuery);
        while ($row = mssql_fetch_array($depositos, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $data[$key]['Depositos'][] = $row2;
        }

    }
    return $data;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $responseCode, $mensaje, $data) {
    //print_r( $data);
    //header("HTTP/1.1 $status $mensaje");
    //header("Content-Type: application/json; charset=UTF-8");

    //$responseIn['Code'] = $responseCode;
    //$responseIn['Description'] = $mensaje;

    //$response['Descargas'] = $data;
    //$response['response'] = $responseIn;


    //echo json_encode($response, JSON_UNESCAPED_SLASHES); //, JSON_PRETTY_PRINT);
	header("HTTP/1.1 $status $mensaje");
    header("Content-Type: application/json; charset=UTF-8");

	$response['Descargas'] = $data;
	$response['Code'] = $status;
	$response['Description'] = $mensaje;

	echo json_encode($response, JSON_UNESCAPED_SLASHES); //JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
}
?>