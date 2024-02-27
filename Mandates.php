<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_mandatos.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
//$first = $_GET['First'];
$block = $_GET['Block'];
$cifDni = $_GET['CifDni'];
$IDMandato = $_GET['IdMandato'];

//$select = "CASE WHEN BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '00000000-0000-0000-0000-000000000000') THEN CONVERT(char(36), IdMandatoUnico) ELSE CONVERT(char(36), BLM_Heptan_IdMandato) END as IdMandato, Mandatos.ReferenciaMandato, Mandatos.TipoAdeudo, BLM_DatosNif.CifDni as CifDni, '' as IdDeposito, Mandatos.PersonaPago, Mandatos.IBAN, Mandatos.BIC, Mandatos.NombreAcreedor, Mandatos.IdAcreedor, Mandatos.DomicilioAcreedor, Mandatos.CodigoPostalAcreedor, Mandatos.MunicipioAcreedor, Mandatos.NacionAcreedor, Mandatos.TipoDePago, Mandatos.LugarFirma, Mandatos.FechaFirma, Mandatos.DescripcionMandato, Mandatos.StatusProcesado, Mandatos.StatusBajaLc, Mandatos.RemesaHabitual, Mandatos.BLM_Autonomo";
//$tablasQuery = "BLM_DatosNif INNER JOIN Mandatos ON BLM_DatosNif.BLM_CodigoClienteHeptan = Mandatos.CodigoCliente";
//$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '')";

$select = "CASE WHEN BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '00000000-0000-0000-0000-000000000000') 
THEN CONVERT(char(36), IdMandatoUnico) ELSE CONVERT(char(36), BLM_Heptan_IdMandato) END AS IdMandato, 
BLM_SolicitudesMandatos.BLM_StatusSolicitudMandato AS StatusSolicitudMandato, Mandatos.StatusBajaLc, BLM_SolicitudesMandatos.BLM_Observaciones AS Observaciones ";
$tablasQuery = "BLM_DatosNif INNER JOIN Mandatos ON BLM_DatosNif.BLM_CodigoClienteHeptan = Mandatos.CodigoCliente INNER JOIN BLM_SolicitudesMandatos ON Mandatos.ReferenciaMandato = BLM_SolicitudesMandatos.ReferenciaMandato ";
$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '')";
$select2 = "CASE WHEN BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '00000000-0000-0000-0000-000000000000') THEN CONVERT(char(36), IdMandatoUnico) ELSE CONVERT(char(36), BLM_Heptan_IdMandato) 
                         END AS IdMandato, 'A' AS StatusSolicitudMandato, Mandatos.StatusBajaLc, '' AS Observaciones ";
$tablasQuery2 = "Mandatos INNER JOIN
                         Clientes ON Mandatos.CodigoEmpresa = Clientes.CodigoEmpresa AND Mandatos.CodigoCliente = Clientes.CodigoCliente INNER JOIN
                         BLM_DatosNif ON Clientes.CifDni = BLM_DatosNif.CifDni LEFT OUTER JOIN
                         BLM_SolicitudesMandatos ON Mandatos.ReferenciaMandato = BLM_SolicitudesMandatos.ReferenciaMandato";
$where2 = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND BLM_SolicitudesMandatos.ReferenciaMandato IS NULL AND Mandatos.IBAN<>''";
$top = "";

//Montamos el where de la query

//Si viene IDMandato devolvemos este Mandato sin usar mas parametros.
if ($IDMandato) {
    $where .= " AND (Mandatos.BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '".$IDMandato."') OR Mandatos.IdMandatoUnico = CONVERT(uniqueidentifier, '".$IDMandato."'))";
    $where2 .= " AND (Mandatos.BLM_Heptan_IdMandato = CONVERT(uniqueidentifier, '".$IDMandato."') OR Mandatos.IdMandatoUnico = CONVERT(uniqueidentifier, '".$IDMandato."'))";
} else {
    //Si viene CIF devolvemos este usuario sin usar mas parametros.
    if ($cifDni != '') {
        $where .= " AND (BLM_DatosNif.CifDni = '" . $cifDni . "')";
        $where2 .= " AND (BLM_DatosNif.CifDni = '" . $cifDni . "')";
    }else {
		$where .= " AND (BLM_SolicitudesMandatos.BLM_StatusSolicitudMandato = 'A' OR BLM_SolicitudesMandatos.BLM_StatusSolicitudMandato = 'R')";
	}

    if ($block != null && $block > 0) {
        $top = "TOP ".intVal($block)." ";
        $top2 = "TOP ".intVal($block)." ";
    }

    //Si no viene CIF comprobamos el resto de parametros.
    if (($heptanStatus == 'Pending') AND ($cifDni == ''))  {
        $where .= " AND (Mandatos.BLM_Heptan_sync = 0)";
        $where2 .= " AND (Mandatos.BLM_Heptan_sync = 0)";
    }
}

$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where;
$query .=" UNION ALL ";
$query .="SELECT ".$top2.$select2." FROM ".$tablasQuery2." WHERE ".$where2;

//echo $query;die;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	$row2 = array_map('utf8_encode', $row); 
	$data[]=$row2;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
	// Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($IDMandato)) {
		print_json(200, "No data found. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	}
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
	//Sacamos todas las descargas de cada mandato
	//$data = groupDescargas($data);

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
	$object = new model_mandatos_class;
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
    foreach ($data as $key => $mandato) {
/*		//FechaFirma
		if($mandato['FechaFirma'] != '') {
			$strDate = strtotime($mandato['FechaFirma']);
			$badDate = date('d/m/Y', $strDate );
				$data[$key]['FechaFirma'] = $badDate;
		}
	
		//TipoAdeudo
		if($mandato['TipoAdeudo'] != '') {
			$data[$key]['TipoAdeudo'] = intVal($mandato['TipoAdeudo']);	
		}
		else
			$data[$key]['TipoAdeudo'] = null;
		
		//TipoDePago
		if($mandato['TipoDePago'] != '') {
			$data[$key]['TipoDePago'] = intVal($mandato['TipoDePago']);	
		}
		else
			$data[$key]['TipoDePago'] = null;
		
		//StatusProcesado
		if($mandato['StatusProcesado'] != '') {
			$data[$key]['StatusProcesado'] = intVal($mandato['StatusProcesado']);	
		}
		else
			$data[$key]['StatusProcesado'] = null;
		
		//StatusBajaLc
		if($mandato['StatusBajaLc'] != '') {
			$data[$key]['StatusBajaLc'] = intVal($mandato['StatusBajaLc']);	
		}
		else
			$data[$key]['StatusBajaLc'] = null;
*/
		//StatusBajaLc
		
		if($mandato['StatusSolicitudMandato'] == null) {
			$data[$key]['StatusSolicitudMandato'] = '';	
		}
		
		if($mandato['Observaciones'] == null) {
			$data[$key]['Observaciones'] = '';	
		}
    }
	return $data;
}

function groupDescargas($data) {

    foreach ($data as $key => $mandatos) {
        $data[$key]['Descargas'] = [];

        $descargasQuery="Select distinct CONVERT(char(36), BLM_Heptan_IdDescarga) as IdDescarga from depositos where codigocliente in (select codigocliente from clientes where referenciamandato = '".$mandatos['ReferenciaMandato']."')";

        $descargas=mssql_query($descargasQuery);
        while ($row = mssql_fetch_array($descargas, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $data[$key]['Descargas'][] = $row2;
        }

    }
    return $data;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje, $data) {
	//print_r( $data);
	header("HTTP/1.1 $status $mensaje");
	header("Content-Type: application/json; charset=UTF-8");

    $response2['Code'] = $status;
    $response2['Description'] = $mensaje;

	$response['Mandatos'] = $data;
	$response['response'] = $response2;

	
	echo json_encode($response, JSON_UNESCAPED_SLASHES); //, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
}
?>