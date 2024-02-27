<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_saleOrder.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
$block = $_GET['Block'];
$cifDni = $_GET['CifDni'];

$select = "CONVERT(char(36), PedidoClienteBases.BLM_IdPedido) as IdPedido,
PedidoClienteBases.IdDelegacion,
PedidoClienteBases.EjercicioPedido,
PedidoClienteBases.SeriePedido,
PedidoClienteBases.NumeroPedido,
PedidoClienteBases.Fecha,
BLM_DatosNif.CifDni,
CONVERT(char(36), PedidoClienteBases.BLM_IdDescarga) as idDescarga,
CONVERT(char(36), PedidoClienteBases.BLM_IdDeposito) as idDeposito,
PedidoClienteBases.CodigoArticulo,
PedidoClienteBases.UnidadesPedidas,
PedidoClienteBases.FechaSuministro,
PedidoClienteBases.Precio,
PedidoClienteBases.DescuentoClienteBases,
PedidoClienteBases.Riesgo,
PedidoClienteBases.Estado,
PedidoClienteBases.CodigoCamion,
PedidoClienteBases.CodigoConductor,
PedidoClienteBases.ClavePeticion,
PedidoClienteBases.ClaveAutorizacion,
PedidoClienteBases.ObservacionesPedido,
PedidoClienteBases.PrecioOfertado,
PedidoClienteBases.N_Autorizacion,
Depositos.CodigoCanal,
PedidoClienteBases.BLM_PedidoWeb,
PedidoClienteBases.BLM_NombreAgenteWeb,
PedidoClienteBases.BLM_UsuarioWeb,
PedidoClienteBases.BLM_PedidoCapturado,
PedidoClienteBases.BLM_EstadoPedido,
PedidoClienteBases.BLM_AditivoExcelent,
PedidoClienteBases.BLM_PrecioAditivoExcelentUnit,
PedidoClienteBases.BLM_FINCOM,
PedidoClienteBases.BLM_ImportePagadoB2C,
PedidoClienteBases.BLM_CodigoOperacionB2C,
PedidoClienteBases.CodiPromocional,
PedidoClienteBases.BLM_Descuento,
PedidoClienteBases.SuPedido,
PedidoClienteBases.BLM_NumeroContratoFincom,
PedidoClienteBases.BLM_NumeroPlazosFincom,
PedidoClienteBases.PedidoWhatsapp,
AutorizacionesBases.ObservacionesAutomaticas,
AutorizacionesBases.ObservacionesBase,
AutorizacionesBases.ObservacionesCentral,
AutorizacionesBases.EstadoPeticion,
AutorizacionesBases.RiesgoDisponible";

//$tablasQuery = "PedidoClienteBases INNER JOIN BLM_DatosNif ON PedidoClienteBases.CodigoCliente = BLM_DatosNif.BLM_CodigoClienteHeptan INNER JOIN Delegaciones ON 1 = Delegaciones.CodigoEmpresa AND PedidoClienteBases.IdDelegacion = Delegaciones.IdDelegacion AND -1 = Delegaciones.BLM_DPMobility LEFT OUTER JOIN Depositos ON 3 = Depositos.CodigoEmpresa AND PedidoClienteBases.CodigoCliente = Depositos.CodigoCliente AND PedidoClienteBases.NumeroDeposito = Depositos.NumeroDeposito";
$tablasQuery = "PedidoClienteBases WITH (nolock) INNER JOIN BLM_DatosNif WITH (nolock) ON PedidoClienteBases.CodigoCliente = BLM_DatosNif.BLM_CodigoClienteHeptan INNER JOIN 
Delegaciones WITH (nolock) ON 1 = Delegaciones.CodigoEmpresa AND PedidoClienteBases.IdDelegacion = Delegaciones.IdDelegacion AND - 1 = Delegaciones.BLM_DPMobility LEFT OUTER JOIN 
AutorizacionesBases WITH (nolock) ON PedidoClienteBases.CodigoEmpresa = AutorizacionesBases.CodigoEmpresa AND PedidoClienteBases.IdDelegacion = AutorizacionesBases.IdDelegacion AND  PedidoClienteBases.N_Autorizacion = AutorizacionesBases.N_Autorizacion LEFT OUTER JOIN 
Depositos WITH (nolock) ON 3 = Depositos.CodigoEmpresa AND PedidoClienteBases.BLM_IdDeposito = Depositos.BLM_Heptan_IdDeposito";

$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '' AND PedidoClienteBases.EjercicioPedido >= 2018 AND PedidoClienteBases.Estado = 0)
	 AND PedidoClienteBases.BLM_IdPedido<>'00000000-0000-0000-0000-000000000000'";
$top = "";

//Montamos el where de la query
if ($cifDni != '') {
    $where .= " AND BLM_DatosNif.CifDni = '".$cifDni."'";
}

if ($block != null && $block > 0) {
    $top = "TOP ".intVal($block)." ";
}

if ($heptanStatus == 'Pending') {
    $where .= " AND (PedidoClienteBases.BLM_Heptan_sync = 0)";
}

$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where  ."ORDER BY PedidoClienteBases.CodigoEmpresa,PedidoClienteBases.EjercicioPedido,PedidoClienteBases.SeriePedido,PedidoClienteBases.NumeroPedido";
// echo $query;die;
$registro = mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $data[]=$row2;
}
// echo json_encode($registro);die;
// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro) == 0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
    print_json(200, "No data found. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
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
    $object = new model_saleOrder_class;
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
    foreach ($data as $key => $saleOrder) {
		//Fecha
		if($saleOrder['Fecha'] != '') {
            $strDate = strtotime($saleOrder['Fecha']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['Fecha'] = $badDate;
        }
		
		//FechaSuministro
        if($saleOrder['FechaSuministro'] != '') {
            $strDate = strtotime($saleOrder['FechaSuministro']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaSuministro'] = $badDate;
        }
		
		//EjercicioPedido
		if($saleOrder['EjercicioPedido'] != '') {
			$data[$key]['EjercicioPedido'] = intVal($saleOrder['EjercicioPedido']);	
		}
		else
			$data[$key]['EjercicioPedido'] = 0;
		
		//NumeroPedido
		if($saleOrder['NumeroPedido'] != '') {
			$data[$key]['NumeroPedido'] = intVal($saleOrder['NumeroPedido']);	
		}
		else
			$data[$key]['NumeroPedido'] = 0;
		
		//UnidadesPedidas (double)
		if($saleOrder['UnidadesPedidas'] != '') {
			$data[$key]['UnidadesPedidas'] = doubleVal($saleOrder['UnidadesPedidas']);	
		}
		else
			$data[$key]['UnidadesPedidas'] = 0;
		
		//Precio
		if($saleOrder['Precio'] != '') {
		    $data[$key]['Precio'] = doubleVal($saleOrder['Precio']);	
		}
		else
			$data[$key]['Precio'] = 0;
		
		//DescuentoClienteBases
		if($saleOrder['DescuentoClienteBases'] != '') {
		    $data[$key]['DescuentoClienteBases'] = doubleVal($saleOrder['DescuentoClienteBases']);	
		}
		else
			$data[$key]['DescuentoClienteBases'] = 0;
		
		//Riesgo
		if($saleOrder['Riesgo'] != '') {
		    $data[$key]['Riesgo'] = doubleVal($saleOrder['Riesgo']);	
		}
		else
			$data[$key]['Riesgo'] = 0;
		
		//Estado
		if($saleOrder['Estado'] != '') {
			$data[$key]['Estado'] = intVal($saleOrder['Estado']);	
		}
		else
			$data[$key]['Estado'] = 0;
		
		//PrecioOfertado (double)
		if($saleOrder['PrecioOfertado'] != '') {
			$data[$key]['PrecioOfertado'] = doubleVal($saleOrder['PrecioOfertado']);	
		}
		else
			$data[$key]['PrecioOfertado'] = 0;
		
		//N_Autorizacion
		if($saleOrder['N_Autorizacion'] != '') {
			$data[$key]['N_Autorizacion'] = intVal($saleOrder['N_Autorizacion']);	
		}
		else
			$data[$key]['N_Autorizacion'] = 0;
		
		//BLM_PedidoWeb
		if($saleOrder['BLM_PedidoWeb'] != '') {
			$data[$key]['BLM_PedidoWeb'] = intVal($saleOrder['BLM_PedidoWeb']);	
		}
		else
			$data[$key]['BLM_PedidoWeb'] = 0;
		
		//BLM_PedidoCapturado
		if($saleOrder['BLM_PedidoCapturado'] != '') {
			$data[$key]['BLM_PedidoCapturado'] = intVal($saleOrder['BLM_PedidoCapturado']);	
		}
		else
			$data[$key]['BLM_PedidoCapturado'] = 0;
		
		//BLM_AditivoExcelent
		if($saleOrder['BLM_AditivoExcelent'] != '') {
			$data[$key]['BLM_AditivoExcelent'] = intVal($saleOrder['BLM_AditivoExcelent']);	
		}
		else
			$data[$key]['BLM_AditivoExcelent'] = 0;
		
		//BLM_PrecioAditivoExcelentUnit (double)
		if($saleOrder['BLM_PrecioAditivoExcelentUnit'] != '') {
			$data[$key]['BLM_PrecioAditivoExcelentUnit'] = doubleVal($saleOrder['BLM_PrecioAditivoExcelentUnit']);	
		}
		else
			$data[$key]['BLM_PrecioAditivoExcelentUnit'] = 0;
		
		//BLM_FINCOM
		if($saleOrder['BLM_FINCOM'] != '') {
			$data[$key]['BLM_FINCOM'] = intVal($saleOrder['BLM_FINCOM']);	
		}
		else
			$data[$key]['BLM_FINCOM'] = 0;
		
		//BLM_ImportePagadoB2C (double)
		if($saleOrder['BLM_ImportePagadoB2C'] != '') {
			$data[$key]['BLM_ImportePagadoB2C'] = doubleVal($saleOrder['BLM_ImportePagadoB2C']);	
		}
		else
			$data[$key]['BLM_ImportePagadoB2C'] = 0;
		
		//BLM_Descuento (double)
		if($saleOrder['BLM_Descuento'] != '') {
			$data[$key]['BLM_Descuento'] = doubleVal($saleOrder['BLM_Descuento']);	
		}
		else
			$data[$key]['BLM_Descuento'] = 0;
		
		
		//BLM_NumeroPlazosFincom
		if($saleOrder['BLM_NumeroPlazosFincom'] != '') {
			$data[$key]['BLM_NumeroPlazosFincom'] = intVal($saleOrder['BLM_NumeroPlazosFincom']);	
		}
		else
			$data[$key]['BLM_NumeroPlazosFincom'] = 0;
		
		//PedidoWhatsapp
		if($saleOrder['PedidoWhatsapp'] != '') {
			$data[$key]['PedidoWhatsapp'] = intVal($saleOrder['PedidoWhatsapp']);	
		}
		else
			$data[$key]['PedidoWhatsapp'] = 0;
		
		//EstadoPeticion
		if($saleOrder['EstadoPeticion'] != '') {
			$data[$key]['EstadoPeticion'] = intVal($saleOrder['EstadoPeticion']);	
		}
		else
			$data[$key]['EstadoPeticion'] = 0;
		
		//RiesgoDisponible (double)
		if($saleOrder['RiesgoDisponible'] != '') {
			$data[$key]['RiesgoDisponible'] = doubleVal($saleOrder['RiesgoDisponible']);	
		}
		else
			$data[$key]['RiesgoDisponible'] = 0;
    }
    return $data;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje, $data) {
    //print_r( $data);
    header("HTTP/1.1 $status $mensaje");
    header("Content-Type: application/json; charset=UTF-8");

	$response['Pedidos'] = $data;
	$response['Code'] = $status;
	$response['Description'] = $mensaje;


	echo json_encode($response, JSON_UNESCAPED_SLASHES); //JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
}
?>