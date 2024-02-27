<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_receipts.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
$block = $_GET['Block'];
$cifDni = $_GET['CifDni'];

//ACTUALIZAR EFECTOS BONIFICACION
$query="UPDATE CarteraEfectos SET
		CarteraEfectos.Ejercicio=CarteraEfectos.BLM_Ejercicio20c, 
		CarteraEfectos.SerieFactura=CarteraEfectos.BLM_Serie20c, 
		CarteraEfectos.Factura=CarteraEfectos.BLM_Factura20c
		FROM            CarteraEfectos INNER JOIN
                         ResumenCliente ON CarteraEfectos.EmpresaOrigen = ResumenCliente.CodigoEmpresa AND CarteraEfectos.BLM_Ejercicio20c = ResumenCliente.EjercicioFactura AND 
                         CarteraEfectos.BLM_Serie20c = ResumenCliente.SerieFactura AND CarteraEfectos.BLM_Factura20c = ResumenCliente.NumeroFactura
						 AND ResumenCliente.BLM_Heptan_sync=-1
		WHERE        (CarteraEfectos.CodigoTipoEfecto = 23) AND (CarteraEfectos.Factura = 0) AND (CarteraEfectos.BLM_Factura20c <> 0)";
$registro = mssql_query($query);

$select = "CONVERT(char(36), CarteraEfectos.MovCartera) as IdEfecto,
CarteraEfectos.IdDelegacion,
CarteraEfectos.Prevision,
CarteraEfectos.Bloqueo,
CASE WHEN CarteraEfectos.StatusBorrado=99 THEN 0 ELSE CarteraEfectos.StatusBorrado END  StatusBorrado,
CarteraEfectos.StatusRemesado,
CarteraEfectos.StatusImpagado,
CarteraEfectos.Ejercicio,
CarteraEfectos.SerieFactura,
CarteraEfectos.Factura,
CarteraEfectos.Comentario,
CarteraEfectos.NumeroOrdenEfecto,
BLM_DatosNif.CifDni,
CarteraEfectos.CodigoTipoEfecto,
CarteraEfectos.ReferenciaMandato,
CarteraEfectos.IBAN,
CarteraEfectos.FechaEmision,
CarteraEfectos.FechaFactura,
CarteraEfectos.FechaRemesa,
CarteraEfectos.FechaVencimiento,
case when CarteraEfectos.StatusBorrado=-1 and CarteraEfectos.FechaCobroEfecto_ is null then CarteraEfectos.FechaVencimiento else CarteraEfectos.FechaCobroEfecto_ END FechaCobroEfecto_,
CarteraEfectos.ImporteEfecto,
CarteraEfectos.ImportePendiente,
CarteraEfectos.Remesable,
CarteraEfectos.Contrapartida,
CarteraEfectos.IdCobrador ";

$tablasQuery = "CarteraEfectos WITH (nolock) INNER JOIN
                         Clientes WITH (nolock) ON CarteraEfectos.CodigoEmpresa = Clientes.CodigoEmpresa AND CarteraEfectos.CodigoClienteProveedor = Clientes.CodigoCliente INNER JOIN
                         BLM_DatosNif WITH (nolock) ON Clientes.CifDni = BLM_DatosNif.CifDni INNER JOIN
                         Delegaciones WITH (nolock) ON CarteraEfectos.CodigoEmpresa = Delegaciones.CodigoEmpresa AND CarteraEfectos.IdDelegacion = Delegaciones.IdDelegacion AND - 1 = Delegaciones.BLM_DPMobility 
						 LEFT OUTER JOIN
                         ResumenCliente ON CarteraEfectos.EmpresaOrigen = ResumenCliente.CodigoEmpresa AND CarteraEfectos.Ejercicio = ResumenCliente.EjercicioFactura AND 
                         CarteraEfectos.SerieFactura = ResumenCliente.SerieFactura AND CarteraEfectos.Factura = ResumenCliente.NumeroFactura						" ;

$where = "(((BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (CarteraEfectos.Prevision = 'C') AND (YEAR(CarteraEfectos.FechaEmision) >= 2018) 
AND CarteraEfectos.SerieFactura like '6%' 
AND CarteraEfectos.Factura <> 0 
and CarteraEfectos.FechaEmision<'07/01/2020'
--AND CarteraEfectos.CodigoTipoEfecto<>23
AND ISNULL(ResumenCliente.BLM_Heptan_sync, - 1) = - 1)
or 
((BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (CarteraEfectos.Prevision = 'C') AND (YEAR(CarteraEfectos.FechaEmision) >= 2018) 
AND CarteraEfectos.SerieFactura like '6%' 

--AND CarteraEfectos.CodigoTipoEfecto<>23
AND CarteraEfectos.Factura <> 0 
and CarteraEfectos.FechaEmision>='07/01/2020'
AND ISNULL(ResumenCliente.BLM_Heptan_sync, - 1) = - 1)
or 
((BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (CarteraEfectos.Prevision = 'C') AND (YEAR(CarteraEfectos.FechaEmision) >= 2018) 
AND CarteraEfectos.IdDelegacion  like '6%'
AND CarteraEfectos.SerieFactura = 'VA' 
--AND CarteraEfectos.CodigoTipoEfecto<>23

AND CarteraEfectos.Factura <> 0 
and CarteraEfectos.FechaEmision>='07/01/2020'
AND ISNULL(ResumenCliente.BLM_Heptan_sync, - 1) = - 1)) AND CarteraEfectos.ImporteEfecto>0";
$top = "";

//Montamos el where de la query
if ($cifDni != '') {
    $where .= " AND BLM_DatosNif.CifDni = '".$cifDni."'";
}

if ($block != null && $block > 0) {
    $top = "TOP ".intVal($block)." ";
}

if ($heptanStatus == 'Pending') {
    $where .= " AND (CarteraEfectos.BLM_Heptan_sync = 0)";
}

$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where ."ORDER BY FechaEmision, IdEfecto";
//echo $query;die;
$registro = mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $data[]=$row2;
}
// echo json_encode($registro);die;
// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro) == 0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($cifDni)) {
		print_json(200, "No data found." .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
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
    $object = new model_cartera_class;
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
    foreach ($data as $key => $cartera) {
		//GUID IdEfecto
		//$data[$key]['IdEfecto'] = createGUID();
		
		//FechaEmision
		if($cartera['FechaEmision'] != '') {
            $strDate = strtotime($cartera['FechaEmision']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaEmision'] = $badDate;
        }
		
		//FechaFactura
        if($cartera['FechaFactura'] != '') {
            $strDate = strtotime($cartera['FechaFactura']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaFactura'] = $badDate;
        }
		
		//FechaRemesa
        if($cartera['FechaRemesa'] != '') {
            $strDate = strtotime($cartera['FechaRemesa']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaRemesa'] = $badDate;
        }
		
		//FechaVencimiento
        if($cartera['FechaVencimiento'] != '') {
            $strDate = strtotime($cartera['FechaVencimiento']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaVencimiento'] = $badDate;
        }
		
		//FechaCobroEfecto
        if($cartera['FechaCobroEfecto_'] != '') {
            $strDate = strtotime($cartera['FechaCobroEfecto_']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaCobroEfecto_'] = $badDate;
        }

		//Bloqueo
		if($cartera['Bloqueo'] != '') {
			$data[$key]['Bloqueo'] = intVal($cartera['Bloqueo']);	
		}
		else
			$data[$key]['Bloqueo'] = 0;
		
		//StatusBorrado
		if($cartera['StatusBorrado'] != '') {
			$data[$key]['StatusBorrado'] = intVal($cartera['StatusBorrado']);	
		}
		else
			$data[$key]['StatusBorrado'] = 0;
		
		//StatusRemesado
		if($cartera['StatusRemesado'] != '') {
			$data[$key]['StatusRemesado'] = intVal($cartera['StatusRemesado']);	
		}
		else
			$data[$key]['StatusRemesado'] = 0;
		
		//StatusImpagado
		if($cartera['StatusImpagado'] != '') {
			$data[$key]['StatusImpagado'] = intVal($cartera['StatusImpagado']);	
		}
		else
			$data[$key]['StatusImpagado'] = 0;
		
		//Ejercicio
		if($cartera['Ejercicio'] != '') {
			$data[$key]['Ejercicio'] = intVal($cartera['Ejercicio']);	
		}
		else
			$data[$key]['Ejercicio'] = 0;
		
		//Factura
		if($cartera['Factura'] != '') {
			$data[$key]['Factura'] = intVal($cartera['Factura']);	
		}
		else
			$data[$key]['Factura'] = 0;
		
		//NumeroOrdenEfecto
		if($cartera['NumeroOrdenEfecto'] != '') {
			$data[$key]['NumeroOrdenEfecto'] = intVal($cartera['NumeroOrdenEfecto']);	
		}
		else
			$data[$key]['NumeroOrdenEfecto'] = 0;
		
		//CodigoTipoEfecto
		if($cartera['CodigoTipoEfecto'] != '') {
			$data[$key]['CodigoTipoEfecto'] = intVal($cartera['CodigoTipoEfecto']);	
		}
		else
			$data[$key]['CodigoTipoEfecto'] = 0;
		
		//Remesable
		if($cartera['Remesable'] != '') {
			$data[$key]['Remesable'] = intVal($cartera['Remesable']);	
		}
		else
			$data[$key]['Remesable'] = 0;
		
		//ImporteEfecto (double)
		if($cartera['ImporteEfecto'] != '') {
			$data[$key]['ImporteEfecto'] = doubleVal($cartera['ImporteEfecto']);	
		}
		else
			$data[$key]['ImporteEfecto'] = 0;

		//ImportePendiente (double)
		if($cartera['ImportePendiente'] != '') {
			$data[$key]['ImportePendiente'] = doubleVal($cartera['ImportePendiente']);	
		}
		else
			$data[$key]['ImportePendiente'] = 0;		
    }
    return $data;
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
function print_json($status, $mensaje, $data) {
    //print_r( $data);
    header("HTTP/1.1 $status $mensaje");
    header("Content-Type: application/json; charset=UTF-8");

	$response['Cartera'] = $data;
	$response['Code'] = $status;
	$response['Description'] = $mensaje;

	echo json_encode($response, JSON_UNESCAPED_SLASHES); //JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
}
?>