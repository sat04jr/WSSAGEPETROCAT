<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_purchaseOrder.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
$block = $_GET['Block'];
$IdCompra = $_GET['IdCompra'];

$select = "CONVERT(char(36), PedidoCliente.BLM_MovCompraBase) as IdCompra,  ComprasBases.IdDelegacion AS CodigoCanal, Clientes.CifDni, ComprasBases.CodigoArticulo, PedidoCliente.DescripcionArticulo,
                         CASE WHEN PedidoCliente.UnidadesReales =0 THEN ComprasBases.Litros ELSE PedidoCliente.UnidadesReales END AS UnidadesReales, Proveedores.BLM_CodigoProveedorHeptan as CodigoProveedor, PedidoCliente.RazonSocialProveedor, PedidoCliente.FechaPedido, PedidoCliente.FechaSuministro, PedidoCliente.BLM_Observaciones,
                         CASE WHEN PedidoCliente.UnidadesReales =0 THEN ComprasBases.Litros ELSE PedidoCliente.UnidadesReales END  AS BLM_Unidades15G, PedidoCliente.NumeroCargaAutorizada, 
						'' AS ARC,PedidoCliente.CodigoCentroCarga,
                        CentrosCarga.DescripcionCentroCarga AS DescripcionCentro,
                        CONVERT(char(36),ISNULL(PedidoClienteBases.BLM_IdPedido,'00000000-0000-0000-0000-000000000000')) AS IdPedidoCapturado ";
/*$select = "CONVERT(char(36), PedidoCliente.BLM_MovCompraBase) as IdCompra,  ComprasBases.IdDelegacion AS CodigoCanal, Clientes.CifDni, BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase AS CodigoArticulo, PedidoCliente.DescripcionArticulo,
                         PedidoCliente.UnidadesReales, PedidoCliente.CodigoProveedor, PedidoCliente.RazonSocialProveedor, PedidoCliente.FechaPedido, PedidoCliente.FechaSuministro, PedidoCliente.BLM_Observaciones,
                         PedidoCliente.BLM_Unidades15G, PedidoCliente.NumeroCargaAutorizada, ComprasBases.BLM_PR185_Arc as ARC,PedidoCliente.CodigoCentroCarga,
                        CentrosCarga.DescripcionCentroCarga AS DescripcionCentro,PedidoCliente.HorarioEntrega as Horario,PedidoCliente.BLM_ObservacionesBases as ObservacionesBases,
                        CONVERT(char(36),ISNULL(PedidoClienteBases.BLM_IdPedido,'00000000-0000-0000-0000-000000000000')) AS IdPedidoCapturado ";
*/
$tablasQuery = "PedidoCliente WITH (NOLOCK) INNER JOIN
                         Clientes WITH (NOLOCK) ON PedidoCliente.CodigoCliente = Clientes.CodigoCliente AND 1 = Clientes.CodigoEmpresa INNER JOIN
                         BLM_DatosNif WITH (NOLOCK) ON Clientes.CifDni = BLM_DatosNif.CifDni INNER JOIN
                         BLM_SO373_TraducArticulos WITH (NOLOCK) ON PedidoCliente.CodigoArticulo = BLM_SO373_TraducArticulos.CodigoArticuloLogistica INNER JOIN
                         ComprasBases WITH (NOLOCK) ON 3 = ComprasBases.CodigoEmpresa AND PedidoCliente.BLM_MovCompraBase = ComprasBases.BLM_IDCompraBases LEFT OUTER JOIN
                         PedidoClienteBases WITH (NOLOCK) ON PedidoCliente.EjercicioPedidoBase = PedidoClienteBases.EjercicioPedido AND PedidoCliente.SeriePedidoBase = PedidoClienteBases.SeriePedido AND 
                         PedidoCliente.NumeroPedidoBase = PedidoClienteBases.NumeroPedido LEFT OUTER JOIN
                         CentrosCarga WITH (NOLOCK) ON PedidoCliente.CodigoEmpresa = CentrosCarga.CodigoEmpresa AND PedidoCliente.CodigoCentroCarga = CentrosCarga.CodigoCentroCarga INNER JOIN
                         Proveedores WITH (NOLOCK) ON Proveedores.CodigoEmpresa = 1 AND Proveedores.CodigoProveedor=PedidoCliente.CodigoProveedor ";

$where = " (BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (YEAR(PedidoCliente.FechaSuministro) >= 2020) AND 
                         (PedidoCliente.BLM_MovCompraBase <> '00000000-0000-0000-0000-000000000000') 
						 AND PEDIDOCLIENTE.NumeroCargaAutorizada<>0
						 AND CASE WHEN PedidoCliente.UnidadesReales =0 THEN ComprasBases.Litros ELSE PedidoCliente.UnidadesReales END >0 AND PedidoCliente.CodigoProveedor<>'4006000615'";
$select2 = "CONVERT(char(36), ComprasBases.BLM_IDCompraBases) AS IdCompra, ComprasBases.IdDelegacion AS CodigoCanal, Clientes.CifDni, PedidoClienteBases.CodigoArticulo, PCA.DescripcionArticulo, 
                         CASE WHEN PedidoCliente.UnidadesReales =0 THEN ComprasBases.Litros ELSE PedidoCliente.UnidadesReales END AS UnidadesReales, Proveedores.BLM_CodigoProveedorHeptan AS CodigoProveedor, 
                         PCA.RazonSocialProveedor, PCA.FechaPedido, PCA.FechaSuministro, PCA.BLM_Observaciones, 
						 CASE WHEN PedidoCliente.UnidadesReales =0 THEN ComprasBases.Litros ELSE PedidoCliente.UnidadesReales END AS BLM_Unidades15G, PCA.NumeroCargaAutorizada, '' AS ARC, 
                         PCA.CodigoCentroCarga, CentrosCarga.DescripcionCentroCarga AS DescripcionCentro, CONVERT(char(36), ISNULL(PedidoClienteBases.BLM_IdPedido, '00000000-0000-0000-0000-000000000000')) 
                         AS IdPedidoCapturado";
$tablasQuery2 = "CentrosCarga WITH (NOLOCK) RIGHT OUTER JOIN
                         ComprasBases WITH (NOLOCK) INNER JOIN
                         PedidoCliente WITH (NOLOCK) ON 3 = ComprasBases.CodigoEmpresa INNER JOIN
                         PedidoClienteBases WITH (NOLOCK) ON PedidoClienteBases.BLM_IdPedido = ComprasBases.BLM_IdPedidoVenta AND PedidoClienteBases.EjercicioPedido = PedidoCliente.EjercicioPedidoBase AND 
                         PedidoClienteBases.SeriePedido = PedidoCliente.SeriePedidoBase AND PedidoClienteBases.NumeroPedido = PedidoCliente.NumeroPedidoBase INNER JOIN
                         Proveedores WITH (NOLOCK) ON 1 = Proveedores.CodigoEmpresa INNER JOIN
                         PedidoCliente AS PCA WITH (NOLOCK) INNER JOIN
                         BLM_DatosNif WITH (NOLOCK) INNER JOIN
                         Clientes WITH (NOLOCK) ON BLM_DatosNif.CifDni = Clientes.CifDni ON PCA.CodigoCliente = Clientes.CodigoCliente AND 1 = Clientes.CodigoEmpresa ON 
                         Proveedores.CodigoProveedor = PCA.CodigoProveedor AND PedidoCliente.NumeroPedido = PCA.NumeroPedidoAsociado ON CentrosCarga.CodigoEmpresa = PCA.CodigoEmpresa AND 
                         CentrosCarga.CodigoCentroCarga = PCA.CodigoCentroCarga ";

$where2 = " (ComprasBases.BLM_IdPedidoVenta <> '00000000-0000-0000-0000-000000000000') AND (PedidoCliente.BLM_MovCompraBase <> '00000000-0000-0000-0000-000000000000') AND (YEAR(PCA.FechaSuministro) 
                         >= 2020) AND (BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (CASE WHEN PCA.UnidadesReales = 0 THEN ComprasBases.Litros ELSE PCA.UnidadesReales END > 0)";
						 
$select3 = "CONVERT(char(36), ComprasBases.BLM_IDCompraBases) AS IdCompra, ComprasBases.IdDelegacion AS CodigoCanal, Clientes.CifDni, ComprasBases.CodigoArticulo, PCA.DescripcionArticulo, 
                         CASE WHEN PCA.UnidadesReales =0 THEN ComprasBases.Litros ELSE PCA.UnidadesReales END AS UnidadesReales, Proveedores.BLM_CodigoProveedorHeptan AS CodigoProveedor, 
                         pedidocliente.RazonSocialProveedor, PCA.FechaPedido, PCA.FechaSuministro, PCA.BLM_Observaciones, 
						 CASE WHEN PCA.UnidadesReales =0 THEN ComprasBases.Litros ELSE PCA.UnidadesReales END AS BLM_Unidades15G, pedidocliente.NumeroCargaAutorizada, '' AS ARC, 
                         PCA.CodigoCentroCarga, CentrosCarga.DescripcionCentroCarga AS DescripcionCentro, CONVERT(char(36), '00000000-0000-0000-0000-000000000000') 
                         AS IdPedidoCapturado";
$tablasQuery3 = "CentrosCarga WITH (NOLOCK) RIGHT OUTER JOIN
                         ComprasBases WITH (NOLOCK) INNER JOIN
                         PedidoCliente WITH (NOLOCK) ON 3 = ComprasBases.CodigoEmpresa AND ComprasBases.BLM_IDCompraBases = PedidoCliente.BLM_MovCompraBase INNER JOIN
                         Proveedores WITH (NOLOCK) ON 1 = Proveedores.CodigoEmpresa INNER JOIN
                         PedidoCliente AS PCA WITH (NOLOCK) INNER JOIN
                         BLM_DatosNif WITH (NOLOCK) INNER JOIN
                         Clientes WITH (NOLOCK) ON BLM_DatosNif.CifDni = Clientes.CifDni ON PCA.CodigoCliente = Clientes.CodigoCliente AND 1 = Clientes.CodigoEmpresa ON 
                         Proveedores.CodigoProveedor = pedidocliente.CodigoProveedor AND PedidoCliente.NumeroPedido = PCA.NumeroPedidoAsociado ON CentrosCarga.CodigoEmpresa = PCA.CodigoEmpresa AND 
                         CentrosCarga.CodigoCentroCarga = PCA.CodigoCentroCarga ";

$where3 = " (PedidoCliente.BLM_MovCompraBase <> '00000000-0000-0000-0000-000000000000') AND (ComprasBases.BLM_IdPedidoVenta = '00000000-0000-0000-0000-000000000000') AND (YEAR(PCA.FechaSuministro) 
                         >= 2020) AND (BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (CASE WHEN PCA.UnidadesReales = 0 THEN ComprasBases.Litros ELSE PCA.UnidadesReales END > 0)";
						 
$top = "";

//Montamos el where de la query
if ($IdCompra != '') {
    $where .= " AND PedidoCliente.BLM_MovCompraBase = '".$IdCompra."'";
    $where2 .= " AND ComprasBases.BLM_IdCompraBases = '".$IdCompra."'";
    $where3 .= " AND ComprasBases.BLM_IdCompraBases = '".$IdCompra."'";
}else{

    if ($block != null && $block > 0) {
        $top = "TOP ".intVal($block)." ";
    }
    
    if ($heptanStatus == 'Pending') {
        $where .= " AND (PedidoCliente.BLM_Heptan_sync = 0)";
        $where2 .= " AND (PedidoCliente.BLM_Heptan_sync = 0)";
//        $where3 .= " AND (PCA.BLM_Heptan_sync = 0)";
        $where3 .= " AND (PedidoCliente.BLM_Heptan_sync = 0)";
    }
}
unset($date);
unset($data1);
$registros1 = 0;
$query1="Select count(*) as registros FROM ".$tablasQuery." WHERE ".$where;
$rows1=mssql_query($query1);
while ($row = mssql_fetch_array($rows1, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $registros1 = $row2['registros'];
}
if ($registros1>0) {
    $query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where ; // ." ORDER BY PedidoCliente.NumeroPedido";
    $registro = mssql_query($query);
    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
        $row2 = array_map('utf8_encode', $row);
        $data1[]=$row2;
    }
	if (isset($data)){
		$data=array_merge_recursive($data,$data1);
	} else {
		$data=$data1;
	}
}
unset($data2);
$registros2 = 0;
$query2="Select count(*) as registros FROM ".$tablasQuery2." WHERE ".$where2;
$rows2=mssql_query($query2);
while ($row = mssql_fetch_array($rows2, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $registros2 = $row2['registros'];
}
if ($registros2>0) {    
    $query="SELECT ".$top.$select2." FROM ".$tablasQuery2." WHERE ".$where2 ; // ." ORDER BY PedidoCliente.NumeroPedido";
    $registro = mssql_query($query);
    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
        $row2 = array_map('utf8_encode', $row);
        $data2[]=$row2;
    }
	if (isset($data)){
		$data=array_merge_recursive($data,$data2);
	} else {
		$data=$data2;
	}
}
unset($data3);
$registros3 = 0;
$query3="Select count(*) as registros FROM ".$tablasQuery3." WHERE ".$where3;
$rows3=mssql_query($query3);
while ($row = mssql_fetch_array($rows3, MSSQL_ASSOC)){
    $row3 = array_map('utf8_encode', $row);
    $registros3 = $row3['registros'];
}
if ($registros3>0) {
    
    $query="SELECT ".$top.$select3." FROM ".$tablasQuery3." WHERE ".$where3 ; // ." ORDER BY PedidoCliente.NumeroPedido";
    $registro = mssql_query($query);
    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
        $row3 = array_map('utf8_encode', $row);
        $data3[]=$row3;
    }
	if (isset($data)){
		$data=array_merge_recursive($data,$data3);
	} else {
		$data=$data3;
	}
}
// echo json_encode($registro);die;
// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if($registros1+$registros2+$registros3 == 0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
    print_json(200, "No data found. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
//	unset($data);
/*    if ($registros1>0) {
        $data=array_merge_recursive($data1);
		//$data=$data1;
    }
    if ($registros2>0) {
        $data=array_merge_recursive($data2);
    }
//echo var_dump($data);	
    if ($registros3>0) {
        $data=array_merge_recursive($data3);
    }
	*/
/*	if ($registros1==0) {
        $data1[]="";
    }
    if ($registros2==0) {
//        $data2[]="";
    }
//echo var_dump($data);	
    if ($registros3==0) {
//        $data3[]="";
    }
//echo var_dump($data1);	die;
    $data=array_merge_recursive($data1,$data2,$data3);
*/	
//echo var_dump($data);	die;
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
    $object = new model_purchaseOrder_class;
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
    foreach ($data as $key => $purchaseOrder) {
		//FechaPedido
		if($purchaseOrder['FechaPedido'] != '') {
            $strDate = strtotime($purchaseOrder['FechaPedido']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaPedido'] = $badDate;
        }
		
		//FechaSuministro
        if($purchaseOrder['FechaSuministro'] != '') {
            $strDate = strtotime($purchaseOrder['FechaSuministro']);
            $badDate = date('d/m/Y', $strDate );
            $data[$key]['FechaSuministro'] = $badDate;
        }
		
		//NumeroPedido
		/*if($purchaseOrder['NumeroPedido'] != '') {
			$data[$key]['NumeroPedido'] = intVal($purchaseOrder['NumeroPedido']);	
		}
		else
			$data[$key]['NumeroPedido'] = 0;
		*/
		//Unidades (double)
		if($purchaseOrder['UnidadesReales'] != '') {
			$data[$key]['UnidadesReales'] = doubleVal($purchaseOrder['UnidadesReales']);	
		}
		else
			$data[$key]['UnidadesReales'] = 0;
		
		//NumeroCargaAutorizada
		if($purchaseOrder['NumeroCargaAutorizada'] != '') {
			$data[$key]['NumeroCargaAutorizada'] = intVal($purchaseOrder['NumeroCargaAutorizada']);	
		}
		else
			$data[$key]['NumeroCargaAutorizada'] = 0;
		
		//BLM_Unidades15G
		if($purchaseOrder['BLM_Unidades15G'] != '') {
			$data[$key]['BLM_Unidades15G'] = doubleVal($purchaseOrder['BLM_Unidades15G']);	
		}
		else
			$data[$key]['BLM_Unidades15G'] = 0;
		
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