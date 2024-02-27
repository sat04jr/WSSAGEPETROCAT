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

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
//$first = $_GET['First'];
$block = $_GET['Block'];
$cifDni = $_GET['CifDni'];

$select = "Mandatos.ReferenciaMandato, Mandatos.TipoAdeudo, Mandatos.CodigoCliente, '' as CodigoDeposito, Mandatos.PersonaPago, Mandatos.IBAN, Mandatos.BIC, Mandatos.NombreAcreedor, Mandatos.IdAcreedor, Mandatos.DomicilioAcreedor, Mandatos.CodigoPostalAcreedor, Mandatos.MunicipioAcreedor, Mandatos.NacionAcreedor, Mandatos.TipoDePago, Mandatos.LugarFirma, Mandatos.FechaFirma, Mandatos.DescripcionMandato, Mandatos.StatusProcesado, Mandatos.StatusBajaLc, Mandatos.RemesaHabitual, Mandatos.BLM_Autonomo";

$tablasQuery = "BLM_DatosNif INNER JOIN Mandatos ON BLM_DatosNif.BLM_CodigoClienteHeptan = Mandatos.CodigoCliente";

$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '')";
$top = "";

//Montamos el where de la query

//Si viene DIF devolvemos este usuario sin usar mas parametros.
if ($cifDni != '') {
    $where .= " AND (BLM_DatosNif.CifDni = '".$cifDni."')";
} else {
	if ($block != null && $block > 0) {
		$top = "TOP ".intVal($block)." ";
	}

	//Si no viene CIF comprobamos el resto de par�metros.
    if ($heptanStatus == 'Pending') {
   		$where .= " AND (BLM_DatosNif.BLM_Heptan_sync = 0)";
	}

	
}

$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where;
//echo $query;die;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	$row2 = array_map('utf8_encode', $row); 
	$data[]=$row2;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
	// Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($cifDni)) {
		print_json(200, "No data found", null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, "Fail", null);
	}
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
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

//Transforma a formato correcto la fecha
function dateTransform($data) {
    foreach ($data as $key => $cliente) {
	//FechaFirma
	if($cliente['FechaFirma'] != '') {
		$strDate = strtotime($cliente['FechaFirma']);
		$badDate = date('d/m/Y H:i', $strDate );
        	$data[$key]['FechaFirma'] = $badDate;
	}
	
    }
	return $data;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje, $data) {
	//print_r( $data);
	header("HTTP/1.1 $status $mensaje");
	header("Content-Type: application/json; charset=UTF-8");
	
	$response['Mandatos'] = $data;
	$response['Code'] = $status;
	$response['Description'] = $mensaje;
	
	echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
}
?>