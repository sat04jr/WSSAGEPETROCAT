<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_invoice.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
//$first = $_GET['First'];
$block = $_GET['Block'];
$ejercicioFactura = $_GET['EjercicioFactura'];
$serieFactura = $_GET['SerieFactura'];
$numeroFactura = $_GET['NumeroFactura'];

$select = "ResumenCliente.IdDelegacion, ResumenCliente.EjercicioFactura, ResumenCliente.SerieFactura, ResumenCliente.NumeroFactura, ResumenCliente.FechaFactura, ResumenCliente.SiglaNacion, ResumenCliente.CifDni, 
	ResumenCliente.RazonSocial, ResumenCliente.Domicilio, ResumenCliente.CodigoPostal, ResumenCliente.CodigoMunicipio, ResumenCliente.Municipio, 
	CASE WHEN ResumenCliente.FormadePago IN ('CR','CC') THEN ResumenCliente.FormadePago ELSE CASE WHEN Clientes.PeriodoFacturacion='CC' THEN 'CC' ELSE 'CR' END END AS FormadePago, ResumenCliente.NumeroPlazos, 
	CASE WHEN ResumenCliente.DiasPrimerPlazo>599 THEN 0 ELSE ResumenCliente.DiasPrimerPlazo END AS DiasPrimerPlazo, ResumenCliente.DiasEntrePlazos, ResumenCliente.DiasFijos1, ResumenCliente.DiasFijos2, ResumenCliente.IndicadorIva, ResumenCliente.IvaIncluido, ResumenCliente.ObservacionesCliente, 
	ResumenCliente.ObservacionesFactura, ResumenCliente.BaseImponible, ResumenCliente.TotalIva, ResumenCliente.ImporteLiquido, ResumenCliente.EjercicioFacturaOriginal, ResumenCliente.SerieFacturaOriginal, 
	ResumenCliente.NumeroFacturaOriginal, ResumenCliente.CodigoMotivoAbonoLc, ResumenCliente.FacturacionElectronica, ResumenCliente.BLM_ExentoIIEE, ResumenCliente.BLM_ExentoIVA, 
	CASE WHEN BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '00000000-0000-0000-0000-000000000000') THEN CONVERT(char(36), IdMandatoUnico) ELSE CONVERT(char(36), BLM_Heptan_IdMandato) END AS IdMandato ";

$tablasQuery = "BLM_DatosNif WITH (nolock) INNER JOIN ResumenCliente WITH (nolock) ON BLM_DatosNif.CifDni = ResumenCliente.CifDni INNER JOIN 
	Delegaciones WITH (nolock) ON ResumenCliente.IdDelegacion = Delegaciones.IdDelegacion AND 1 = Delegaciones.CodigoEmpresa AND - 1 = Delegaciones.BLM_DPMobility LEFT OUTER JOIN 
	Mandatos WITH (nolock) ON ResumenCliente.ReferenciaMandato = Mandatos.ReferenciaMandato AND Mandatos.CodigoEmpresa = 1
	INNER JOIN Clientes WITH (nolock) ON Clientes.CodigoEmpresa = 1 AND Clientes.CodigoCliente = ResumenCliente.CodigoCliente ";

$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '' AND ResumenCliente.CodigoEmpresa = 3) AND ResumenCliente.SerieFactura<>'FACREC' AND (ResumenCliente.FechaFactura >= CONVERT(DATETIME, 
                         '2020-07-01 00:00:00', 102))";
	/*AND (SELECT COUNT(*) FROM CarteraEfectos WITH (nolock) 
	WHERE CodigoEmpresa=1 AND Ejercicio=ResumenCliente.EjercicioFactura AND SerieFactura=ResumenCliente.SerieFactura AND Factura=ResumenCliente.NumeroFactura AND StatusBorrado=0)>0";*/
$top = "";
$top2 = "";
$top3 = "";

$select2 = "ResumenCliente.IdDelegacion, ResumenCliente.EjercicioFactura, ResumenCliente.SerieFactura, ResumenCliente.NumeroFactura, ResumenCliente.FechaFactura, ResumenCliente.SiglaNacion, ResumenCliente.CifDni, 
ResumenCliente.RazonSocial, ResumenCliente.Domicilio, ResumenCliente.CodigoPostal, ResumenCliente.CodigoMunicipio, ResumenCliente.Municipio, 
CASE WHEN ResumenCliente.FormadePago IN ('CR','CC') THEN ResumenCliente.FormadePago ELSE CASE WHEN Clientes.PeriodoFacturacion='CC' THEN 'CC' ELSE 'CR' END END AS FormadePago, ResumenCliente.NumeroPlazos, 
CASE WHEN ResumenCliente.DiasPrimerPlazo>599 THEN 0 ELSE ResumenCliente.DiasPrimerPlazo END AS DiasPrimerPlazo, ResumenCliente.DiasEntrePlazos, ResumenCliente.DiasFijos1, ResumenCliente.DiasFijos2, ResumenCliente.IndicadorIva, ResumenCliente.IvaIncluido, ResumenCliente.ObservacionesCliente, 
ResumenCliente.ObservacionesFactura, ResumenCliente.BaseImponible, ResumenCliente.TotalIva, ResumenCliente.ImporteLiquido, ResumenCliente.EjercicioFacturaOriginal, ResumenCliente.SerieFacturaOriginal, 
ResumenCliente.NumeroFacturaOriginal, ResumenCliente.CodigoMotivoAbonoLc, ResumenCliente.FacturacionElectronica, ResumenCliente.BLM_ExentoIIEE, ResumenCliente.BLM_ExentoIVA, 
CASE WHEN BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '00000000-0000-0000-0000-000000000000') THEN CONVERT(char(36), IdMandatoUnico) ELSE CONVERT(char(36), BLM_Heptan_IdMandato) END AS IdMandato ";

$tablasQuery2 = "BLM_DatosNif WITH (nolock) INNER JOIN ResumenCliente WITH (nolock) ON BLM_DatosNif.CifDni = ResumenCliente.CifDni INNER JOIN 
	Delegaciones WITH (nolock) ON ResumenCliente.IdDelegacion = Delegaciones.IdDelegacion AND 1 = Delegaciones.CodigoEmpresa AND - 1 = Delegaciones.BLM_DPMobility LEFT OUTER JOIN 
	Mandatos WITH (nolock) ON ResumenCliente.ReferenciaMandato = Mandatos.ReferenciaMandato AND Mandatos.CodigoEmpresa = 1 
	INNER JOIN Clientes WITH (nolock) ON Clientes.CodigoEmpresa = 1 AND Clientes.CodigoCliente = ResumenCliente.CodigoCliente ";

$where2 = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '' AND ResumenCliente.CodigoEmpresa = 3) AND ResumenCliente.SerieFactura<>'FACREC' AND (ResumenCliente.FechaFactura < CONVERT(DATETIME, 
                         '2020-07-01 00:00:00', 102)) AND ResumenCliente.RecibidoEdi_=-1";
						 
//FACTURAS VA
$select3 = "ResumenCliente.IdDelegacion, ResumenCliente.EjercicioFactura, ResumenCliente.SerieFactura, ResumenCliente.NumeroFactura, ResumenCliente.FechaFactura, ResumenCliente.SiglaNacion, ResumenCliente.CifDni, 
	ResumenCliente.RazonSocial, ResumenCliente.Domicilio, ResumenCliente.CodigoPostal, ResumenCliente.CodigoMunicipio, ResumenCliente.Municipio, 
	CASE WHEN ResumenCliente.FormadePago IN ('CR','CC') THEN ResumenCliente.FormadePago ELSE CASE WHEN Clientes.PeriodoFacturacion='CC' THEN 'CC' ELSE 'CR' END END AS FormadePago, ResumenCliente.NumeroPlazos, 
	CASE WHEN ResumenCliente.DiasPrimerPlazo>599 THEN 0 ELSE ResumenCliente.DiasPrimerPlazo END AS DiasPrimerPlazo, ResumenCliente.DiasEntrePlazos, ResumenCliente.DiasFijos1, ResumenCliente.DiasFijos2, ResumenCliente.IndicadorIva, ResumenCliente.IvaIncluido, ResumenCliente.ObservacionesCliente, 
	ResumenCliente.ObservacionesFactura, ResumenCliente.BaseImponible, ResumenCliente.TotalIva, ResumenCliente.ImporteLiquido, ResumenCliente.EjercicioFacturaOriginal, ResumenCliente.SerieFacturaOriginal, 
	ResumenCliente.NumeroFacturaOriginal, ResumenCliente.CodigoMotivoAbonoLc, ResumenCliente.FacturacionElectronica, ResumenCliente.BLM_ExentoIIEE, ResumenCliente.BLM_ExentoIVA, 
	CASE WHEN BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '00000000-0000-0000-0000-000000000000') THEN CONVERT(char(36), IdMandatoUnico) ELSE CONVERT(char(36), BLM_Heptan_IdMandato) END AS IdMandato ";

$tablasQuery3 = "BLM_DatosNif WITH (nolock) INNER JOIN ResumenCliente WITH (nolock) ON BLM_DatosNif.CifDni = ResumenCliente.CifDni INNER JOIN 
	Delegaciones WITH (nolock) ON ResumenCliente.IdDelegacion = Delegaciones.IdDelegacion AND 1 = Delegaciones.CodigoEmpresa AND - 1 = Delegaciones.BLM_DPMobility LEFT OUTER JOIN 
	Mandatos WITH (nolock) ON ResumenCliente.ReferenciaMandato = Mandatos.ReferenciaMandato AND Mandatos.CodigoEmpresa = 1
	INNER JOIN Clientes WITH (nolock) ON Clientes.CodigoEmpresa = 1 AND Clientes.CodigoCliente = ResumenCliente.CodigoCliente ";

$where3 = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '' AND ResumenCliente.CodigoEmpresa = 2) AND ResumenCliente.SerieFactura='VA' AND (ResumenCliente.FechaFactura >= CONVERT(DATETIME, 
                         '2020-07-01 00:00:00', 102))";

//Si viene IDMandato devolvemos este Mandato sin usar mas parametros.
if ($ejercicioFactura) {
    $where .= " AND (ResumenCliente.EjercicioFactura = " .$ejercicioFactura .") ";
    $where2 .= " AND (ResumenCliente.EjercicioFactura = " .$ejercicioFactura .") ";
    $where3 .= " AND (ResumenCliente.EjercicioFactura = " .$ejercicioFactura .") ";
} 
if ($serieFactura) {
    $where .= " AND (ResumenCliente.SerieFactura = '" .$serieFactura ."') ";
    $where2 .= " AND (ResumenCliente.SerieFactura = '" .$serieFactura ."') ";
    $where3 .= " AND (ResumenCliente.SerieFactura = '" .$serieFactura ."') ";
} 
if ($numeroFactura) {
    $where .= " AND (ResumenCliente.NumeroFactura = " .$numeroFactura .") ";
    $where2 .= " AND (ResumenCliente.NumeroFactura = " .$numeroFactura .") ";
    $where3 .= " AND (ResumenCliente.NumeroFactura = " .$numeroFactura .") ";
} 
else {
    //Si viene CIF devolvemos este usuario sin usar mas parametros.
    if ($block != null && $block > 0) {
        $top = "TOP ".intVal($block)." ";
        $top2 = "TOP ".intVal($block)." ";
        $top3 = "TOP ".intVal($block)." ";
    }

    //Si no viene CIF comprobamos el resto de parametros.
    if ($heptanStatus == 'Pending') {
        $where .= " AND (ResumenCliente.BLM_Heptan_sync = 0)";
        $where2 .= " AND (ResumenCliente.BLM_Heptan_sync = 0)";
        $where3 .= " AND (ResumenCliente.BLM_Heptan_sync = 0)";
    }
}

$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where ;
$query.=" UNION ALL ";
$query.="SELECT ".$top2.$select2." FROM ".$tablasQuery2." WHERE ".$where2 ;
$query.=" UNION ALL ";
$query.="SELECT ".$top3.$select3." FROM ".$tablasQuery3." WHERE ".$where3 ;
//." ORDER BY ResumenCliente.EjercicioFactura, ResumenCliente.SerieFactura, ResumenCliente.NumeroFactura";
//echo $query;die;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	$row2 = array_map('utf8_encode', $row); 
	$data[]=$row2;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
	// Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($numeroFactura)) {
		print_json(200, "No data found. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	}
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {

	//Sacamos los albaranes de cada factura
    $data = groupAlbaranes($data, $tablasQuery);
	//Pasamos la fecha a formato correcto
	$data = dateTransform($data);

	// Imprime la informacion solicitada
	print_json(200, "Success", $data);
}

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_invoice_class;
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

//Transforma a formato correcto la fecha y los numeros
function dateTransform($data) {
    foreach ($data as $key => $factura) {
        if($factura['FechaFactura'] != '') {
            $strDate = strtotime($factura['FechaFactura']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaFactura'] = $badDate;
        }
		
		if($factura['EjercicioFactura'] != '') {
			$data[$key]['EjercicioFactura'] = intVal($factura['EjercicioFactura']);	
		}
		else
			$data[$key]['EjercicioFactura'] = 0;
		
		if($factura['NumeroFactura'] != '') {
			$data[$key]['NumeroFactura'] = intVal($factura['NumeroFactura']);	
		}
		else
			$data[$key]['NumeroFactura'] = 3;
		
		if($factura['NumeroPlazos'] != '') {
			$data[$key]['NumeroPlazos'] = intVal($factura['NumeroPlazos']);	
		}
		else
			$data[$key]['NumeroPlazos'] = 0;
		
		if($factura['DiasPrimerPlazo'] != '') {
			$data[$key]['DiasPrimerPlazo'] = intVal($factura['DiasPrimerPlazo']);	
		}
		else
			$data[$key]['DiasPrimerPlazo'] = 0;
		
		if($factura['DiasEntrePlazos'] != '') {
			$data[$key]['DiasEntrePlazos'] = intVal($factura['DiasEntrePlazos']);	
		}
		else
			$data[$key]['DiasEntrePlazos'] = 0;
		
		if($factura['DiasFijos1'] != '') {
			$data[$key]['DiasFijos1'] = intVal($factura['DiasFijos1']);	
		}
		else
			$data[$key]['DiasFijos1'] = 0;
		
		if($factura['DiasFijos2'] != '') {
			$data[$key]['DiasFijos2'] = intVal($factura['DiasFijos2']);	
		}
		else
			$data[$key]['DiasFijos2'] = 0;
		
		if($factura['IvaIncluido'] != '') {
			$data[$key]['IvaIncluido'] = intVal($factura['IvaIncluido']);	
		}
		else
			$data[$key]['IvaIncluido'] = 0;
		
		if($factura['BaseImponible'] != '') {
			$data[$key]['BaseImponible'] = doubleVal($factura['BaseImponible']);	
		}
		else
			$data[$key]['BaseImponible'] = 0;
		
		if($factura['TotalIva'] != '') {
			$data[$key]['TotalIva'] = doubleVal($factura['TotalIva']);	
		}
		else
			$data[$key]['TotalIva'] = 0;
		
		if($factura['ImporteLiquido'] != '') {
			$data[$key]['ImporteLiquido'] = doubleVal($factura['ImporteLiquido']);	
		}
		else
			$data[$key]['ImporteLiquido'] = 0;
		
		if($factura['EjercicioFacturaOriginal'] != '') {
			$data[$key]['EjercicioFacturaOriginal'] = doubleVal($factura['EjercicioFacturaOriginal']);	
		}
		else
			$data[$key]['EjercicioFacturaOriginal'] = 0;
		
		if($factura['NumeroFacturaOriginal'] != '') {
			$data[$key]['NumeroFacturaOriginal'] = doubleVal($factura['NumeroFacturaOriginal']);	
		}
		else
			$data[$key]['NumeroFacturaOriginal'] = 0;
		
		if($factura['FacturacionElectronica'] != '') {
			$data[$key]['FacturacionElectronica'] = intVal($factura['FacturacionElectronica']);	
		}
		else
			$data[$key]['FacturacionElectronica'] = 0;
		
		if($factura['BLM_ExentoIIEE'] != '') {
			$data[$key]['BLM_ExentoIIEE'] = intVal($factura['BLM_ExentoIIEE']);	
		}
		else
			$data[$key]['BLM_ExentoIIEE'] = 0;
		
		if($factura['BLM_ExentoIVA'] != '') {
			$data[$key]['BLM_ExentoIVA'] = intVal($factura['BLM_ExentoIVA']);	
		}
		else
			$data[$key]['BLM_ExentoIVA'] = 0;
    }
	return $data;
}

//Agrupa albaranes en facturas
function groupAlbaranes($data, $tablasQuery) {
    $albaranesSelect = "CONVERT(char(36),LineasPosicion) AS IdAlbaran ";
    $tablaQuery="LineasAlbaranCliente ";
    foreach ($data as $key => $factura) {
        $data[$key]['Albaranes'] = [];
		$CodigoEmpresa=3;
		if ($factura['SerieFactura']=="VA"){
			$CodigoEmpresa=2;
		}
        $albaranesWhere = "CodigoEmpresa=".$CodigoEmpresa." AND EjercicioFactura = " .$factura['EjercicioFactura'] ." AND SerieFactura = '" .$factura['SerieFactura'] ."' AND NumeroFactura = " .$factura['NumeroFactura']."
			AND SerieFactura<>'VA'" ;

        $albaranesQuery="SELECT ".$albaranesSelect." FROM ".$tablaQuery." WHERE ".$albaranesWhere;

        $albaranes=mssql_query($albaranesQuery);
        while ($row = mssql_fetch_array($albaranes, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $data[$key]['Albaranes'][] = $row2;
        }

    }
    return $data;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje, $data) {
	//print_r( $data);
	header("HTTP/1.1 $status $mensaje");
	header("Content-Type: application/json; charset=UTF-8");

    //$response2['Code'] = $status;
    //$response2['Description'] = $mensaje;

	$response['Facturas'] = $data;
    $response['Code'] = $status;
    $response['Description'] = $mensaje;
	//$response['response'] = $response2;

	
	echo json_encode($response, JSON_UNESCAPED_SLASHES); //, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
}
?>