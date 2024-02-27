<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_CreditCustomers.php';

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

$select = "RiesgosPetrocat.CifDni, RiesgosPetrocat.NumeroCC, RiesgosPetrocat.ImporteAval AS RiesgoAval, RiesgosPetrocat.RiesgoCC AS RiesgoCCConcedido, RiesgosPetrocat.FechaAprobacion AS FechaAprobacionCC, RiesgosPetrocat.FechaVencimiento1 as FechaVencimientoCC,  RiesgosPetrocat.RiesgoMaximo AS RiesgoPetrocatConcedido, riesgospetrocat.fechaaval as FechaVencimientoAval, RiesgosPetrocat.BLM_RAZONRiesgoCCCero AS RazonRiesgoCCCero ";

$tablasQuery = "BLM_DatosNif INNER JOIN RiesgosPetrocat ON BLM_DatosNif.CifDni = RiesgosPetrocat.CifDni ";

$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND RiesgosPetrocat.CifDni<>''";
$top = "";

//Montamos el where de la query

//Si viene IDRiesgo devolvemos este Mandato sin usar mas parametros.
    //Si viene CIF devolvemos este usuario sin usar mas parametros.
    if ($cifDni != '') {
        $where .= " AND (BLM_DatosNif.CifDni = '" . $cifDni . "')";
    }else{
		if ($heptanStatus == 'Pending') {
        $where .= " AND (RiesgosPetrocat.BLM_Heptan_sync = 0)";
		}
	}

    if ($block != null && $block > 0) {
        $top = "TOP ".intVal($block)." ";
    }




$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where ."ORDER BY RiesgosPetrocat.CifDni";
//echo $query;die;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	 $row2 = array_map('utf8_encode', $row); 
	 $data[]=$row2;
	//$data[]=$row;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
	// Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($IDRiesgo)) {
		print_json(200, "No data found. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	}
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
	//Pasamos la fecha a formato correcto
	//echo json_encode($data, JSON_UNESCAPED_SLASHES);die;
	$data = dateTransform($data);

	// Imprime la informacion solicitada
	print_json(200, "Success", $data);
}

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_creditcustomers_class;
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
    foreach ($data as $key => $riesgos) {
		//FechaAprobacionCC
        if($riesgos['FechaAprobacionCC'] != '') {
            $strDate = strtotime($riesgos['FechaAprobacionCC']);
            $badDate = date('d/m/Y', $strDate );
                $data[$key]['FechaAprobacionCC'] = $badDate;
        }
		
		//FechaVencimientoCC
		//DEVUELVE UN FORMATO INCORRECTO JUN 08 1984
        if($riesgos['FechaVencimientoCC'] != '') {
            $strDate = strtotime($riesgos['FechaVencimientoCC']);
            $badDate = date('d/m/Y', $strDate );
                $data[$key]['FechaVencimientoCC'] = $badDate;
        }
		
		//FechaVencimientoAval
		//DEVUELVE UN FORMATO INCORRECTO JUN 08 1984
        if($riesgos['FechaVencimientoAval'] != '') {
            $strDate = strtotime($riesgos['FechaVencimientoAval']);
            $badDate = date('d/m/Y', $strDate );
                $data[$key]['FechaVencimientoAval'] = $badDate;
        }
		
		//RiesgoAval (double)
		if($riesgos['RiesgoAval'] != '') {
			$data[$key]['RiesgoAval'] = doubleVal($riesgos['RiesgoAval']);	
		}
		else
			if($riesgos['RiesgoAval'] != 0) {
				$data[$key]['RiesgoAval'] = 0;
			}
			
		//RiesgoCCConcedido (double)
		if($riesgos['RiesgoCCConcedido'] != '') {
			$data[$key]['RiesgoCCConcedido'] = doubleVal($riesgos['RiesgoCCConcedido']);	
		}
		else
			if($riesgos['RiesgoCCConcedido'] != 0) {
				$data[$key]['RiesgoCCConcedido'] = 0;
			}
			
		//RiesgoPetrocatConcedido (double)
		if($riesgos['RiesgoPetrocatConcedido'] != '') {
			$data[$key]['RiesgoPetrocatConcedido'] = doubleVal($riesgos['RiesgoPetrocatConcedido']);	
		}
		else
			if($riesgos['RiesgoPetrocatConcedido'] != 0) {
				$data[$key]['RiesgoPetrocatConcedido'] = 0;
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

	$response['RiesgosClientes'] = $data;
	$response['response'] = $response2;

	
	echo json_encode($response, JSON_UNESCAPED_SLASHES); //, JSON_PRETTY_PRINT);
}
?>