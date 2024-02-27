<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_fuelsv2.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
$block = $_GET['Block'];
//$codigoArticulo = $_GET['CodigoArticulo'];
//$fecha = $_GET['Fecha'];
$tipoTarifa = $_GET['TipoTarifa'];

//por defecto es 0
if ($tipoTarifa != 0 && $tipoTarifa != 1 && $tipoTarifa != 2) {
	print_json(400, 1, "Error TipoTarifa. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	return;
}

//if ($fecha == null || $fecha == '') {
//    print_json(400, 1, "Faltan parámetros",null);
//    die;
//}

//$fecha = str_replace("/", "-", $fecha);
//
//$fechaTime = strtotime($fecha);
//if ($fechaTime) {
//    $fechaString = date('Ymd',$fechaTime);
//} else {
//    print_json(400, 1, "Formato fecha incorrecto",null);
//    die;
//}

$fechaString = date ('m/d/Y');
$fechaString= date('m/d/Y', strtotime($fechaString . ' +1 day'));

$where = "(Year(FechaInicio) >= 2017)";

if ($heptanStatus == 'Pending') {
    $where = "(TarifaPrecio.BLM_Heptan_sync = 0 AND Year(FechaInicio) >= 2017)";
}

switch ($tipoTarifa) {
    case 2:
        $selectTarifas1 = "TarifaPrecio.FechaInicio AS Fecha, Tarifas.Tarifa AS CodigoTarifa, 2 AS TipoTarifa, BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase AS CodigoArticulo";
        $fromTarifas1 = "TarifaPrecio INNER JOIN Comisionistas ON 1 = Comisionistas.CodigoEmpresa AND TarifaPrecio.Tarifa = Comisionistas.BLM_TarifaPrecioMinimo AND Comisionistas.BLM_TarifaPrecioMinimo <> '0' INNER JOIN Tarifas ON Comisionistas.BLM_TarifaPrecioMinimo = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1 INNER JOIN Delegaciones ON CAST(Comisionistas.CodigoComisionista AS varchar(10)) = Delegaciones.IdDelegacion AND Comisionistas.CodigoEmpresa = Delegaciones.CodigoEmpresa AND -1 = Delegaciones.BLM_DPMobility";
        break;
    case 0:
        $selectTarifas2 = "TarifaPrecio.FechaInicio AS Fecha, Tarifas.Tarifa AS CodigoTarifa, 0 AS TipoTarifa, BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase AS CodigoArticulo";
        $fromTarifas2 = "BLM_PR266_TarifasBases INNER JOIN Tarifas ON BLM_PR266_TarifasBases.Tarifa = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN TarifaPrecio ON Tarifas.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND Tarifas.Tarifa = TarifaPrecio.Tarifa INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1 INNER JOIN Delegaciones ON CAST(BLM_PR266_TarifasBases.CodigoComisionista AS varchar(10)) = Delegaciones.IdDelegacion AND 1 = Delegaciones.CodigoEmpresa AND -1 = Delegaciones.BLM_DPMobility";
        $where .= " AND FechaInicio <= '".$fechaString."' AND Year(FechaInicio) >= 2017";
		break;
    case 1:
        $selectTarifas3 = "TarifaPrecio.FechaInicio AS Fecha, Tarifas.Tarifa AS CodigoTarifa, 1 AS TipoTarifa,  BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase as CodigoArticulo";
        //$fromTarifas3 = "BLM_PV_MailClientes INNER JOIN BLM_PV_TarifasClienteFinal ON BLM_PV_MailClientes.MovPosicion = BLM_PV_TarifasClienteFinal.MovOrigen INNER JOIN Tarifas ON BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN TarifaPrecio ON BLM_PV_TarifasClienteFinal.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = TarifaPrecio.Tarifa INNER JOIN BLM_DatosNif ON BLM_PV_MailClientes.CifDni = BLM_DatosNif.CifDni inner join  BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo";
		$fromTarifas3 = "BLM_PV_TarifasClienteFinal INNER JOIN Tarifas ON BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa AND -1 = Tarifas.BLM_TarifaCliHeptan INNER JOIN TarifaPrecio ON BLM_PV_TarifasClienteFinal.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = TarifaPrecio.Tarifa inner join  BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1";
        $where .= " AND FechaInicio <= '".$fechaString."' AND Year(FechaInicio) >= 2017";
		break;
    default:
		print_json(400, 1, "Error TipoTarifa. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
		return;
        break;
}

//$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '')";
$top = "";

//Montamos el where de la query

//Si viene DIF devolvemos este usuario sin usar mas parametros.
//if ($codigoArticulo != '') {
//    $where .= " AND (BLM_CodigoArticuloBase = '".$codigoArticulo."')";
//}

if ($block != null && $block > 0) {
//    $parseBlock1 = floor($block/ 3);
//    switch ($block%3) {
//        case 0:
//            $parseBlock2 = $parseBlock1;
//            $parseBlock3 = $parseBlock1;
//            break;
//        case 1:
//            $parseBlock2 = $parseBlock1;
//            $parseBlock3 = $parseBlock1 + 1;
//            break;
//        case 2:
//            $parseBlock2 = $parseBlock1 + 1;
//            $parseBlock3 = $parseBlock1 + 1;
//            break;
//    }
//    $top1 = "TOP ".intVal($parseBlock1)." ";
//    $top2 = "TOP ".intVal($parseBlock2)." ";
//    $top3 = "TOP ".intVal($parseBlock3)." ";
    $top = "TOP ".intVal($block);
}

switch ($tipoTarifa) {
    case 2:
        $query="SELECT DISTINCT ".$top.$selectTarifas1." FROM ".$fromTarifas1." WHERE ".$where;
        break;
    case 0:
        $query="SELECT DISTINCT ".$top.$selectTarifas2." FROM ".$fromTarifas2." WHERE ".$where;
        break;
    case 1:
        $query="SELECT DISTINCT ".$top.$selectTarifas3." FROM ".$fromTarifas3." WHERE ".$where;
        break;
    default:
        break;
}
$registro=mssql_query($query);

while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	$row2 = array_map('utf8_encode', $row); 
	$data[]=$row2;
}

//Detalles si Hastaunidades es > 0

//    TarifaPrecio.fechaInicio >= fecha1ªQuery,
//    TarifaPrecio.fechaFinal < fecha1ªquery,
//    Tarifas.Tarifa = Tarifa1ªQuery,
//    BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = CodigoArticulo1ªQuery

$detallesQuery1 = "SELECT distinct TarifaPrecio.FechaInicio AS Fecha, Tarifas.Tarifa AS CodigoTarifa, 2 AS TipoTarifa, BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase AS CodigoArticulo, TarifaPrecio.HastaUnidades1, TarifaPrecio.Precio1, TarifaPrecio.HastaUnidades2, TarifaPrecio.Precio2, TarifaPrecio.HastaUnidades3, TarifaPrecio.Precio3, TarifaPrecio.HastaUnidades4, TarifaPrecio.Precio4, TarifaPrecio.HastaUnidades5, TarifaPrecio.Precio5, TarifaPrecio.HastaUnidades6, TarifaPrecio.Precio6, TarifaPrecio.HastaUnidades7, TarifaPrecio.Precio7, TarifaPrecio.HastaUnidades8, TarifaPrecio.Precio8, TarifaPrecio.HastaUnidades9, TarifaPrecio.Precio9, TarifaPrecio.HastaUnidades10, TarifaPrecio.Precio10 FROM TarifaPrecio INNER JOIN Comisionistas ON 1 = Comisionistas.CodigoEmpresa AND TarifaPrecio.Tarifa = Comisionistas.BLM_TarifaPrecioMinimo AND Comisionistas.BLM_TarifaPrecioMinimo <> '0' INNER JOIN Tarifas ON Comisionistas.BLM_TarifaPrecioMinimo = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1 INNER JOIN Delegaciones ON CAST(Comisionistas.CodigoComisionista AS varchar(10)) = Delegaciones.IdDelegacion AND Comisionistas.CodigoEmpresa = Delegaciones.CodigoEmpresa";

$detallesQuery2 = "SELECT distinct TarifaPrecio.FechaInicio AS Fecha, Tarifas.Tarifa AS CodigoTarifa, 0 AS TipoTarifa, BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase AS CodigoArticulo, TarifaPrecio.HastaUnidades1, TarifaPrecio.Precio1, TarifaPrecio.HastaUnidades2, TarifaPrecio.Precio2, TarifaPrecio.HastaUnidades3, TarifaPrecio.Precio3, TarifaPrecio.HastaUnidades4, TarifaPrecio.Precio4, TarifaPrecio.HastaUnidades5, TarifaPrecio.Precio5, TarifaPrecio.HastaUnidades6, TarifaPrecio.Precio6, TarifaPrecio.HastaUnidades7, TarifaPrecio.Precio7, TarifaPrecio.HastaUnidades8, TarifaPrecio.Precio8, TarifaPrecio.HastaUnidades9, TarifaPrecio.Precio9, TarifaPrecio.HastaUnidades10, TarifaPrecio.Precio10 FROM BLM_PR266_TarifasBases INNER JOIN Tarifas ON BLM_PR266_TarifasBases.Tarifa = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN TarifaPrecio ON Tarifas.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND Tarifas.Tarifa = TarifaPrecio.Tarifa INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1 INNER JOIN Delegaciones ON CAST(BLM_PR266_TarifasBases.CodigoComisionista AS varchar(10)) = Delegaciones.IdDelegacion AND 1 = Delegaciones.CodigoEmpresa";

//$detallesQuery3 = "SELECT distinct TarifaPrecio.FechaInicio AS Fecha, Tarifas.Tarifa AS CodigoTarifa, 1 AS TipoTarifa, BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase as CodigoArticulo, TarifaPrecio.HastaUnidades1, TarifaPrecio.Precio1, TarifaPrecio.HastaUnidades2, TarifaPrecio.Precio2, TarifaPrecio.HastaUnidades3, TarifaPrecio.Precio3, TarifaPrecio.HastaUnidades4, TarifaPrecio.Precio4, TarifaPrecio.HastaUnidades5, TarifaPrecio.Precio5, TarifaPrecio.HastaUnidades6, TarifaPrecio.Precio6, TarifaPrecio.HastaUnidades7, TarifaPrecio.Precio7, TarifaPrecio.HastaUnidades8, TarifaPrecio.Precio8, TarifaPrecio.HastaUnidades9, TarifaPrecio.Precio9, TarifaPrecio.HastaUnidades10, TarifaPrecio.Precio10 FROM BLM_PV_MailClientes INNER JOIN BLM_PV_TarifasClienteFinal ON BLM_PV_MailClientes.MovPosicion = BLM_PV_TarifasClienteFinal.MovOrigen INNER JOIN Tarifas ON BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN TarifaPrecio ON BLM_PV_TarifasClienteFinal.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = TarifaPrecio.Tarifa INNER JOIN BLM_DatosNif ON BLM_PV_MailClientes.CifDni = BLM_DatosNif.CifDni INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo";

$detallesQuery3 = "SELECT distinct TarifaPrecio.FechaInicio AS Fecha, Tarifas.Tarifa AS CodigoTarifa, 1 AS TipoTarifa, BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase as CodigoArticulo, TarifaPrecio.HastaUnidades1, TarifaPrecio.Precio1, TarifaPrecio.HastaUnidades2, TarifaPrecio.Precio2, TarifaPrecio.HastaUnidades3, TarifaPrecio.Precio3, TarifaPrecio.HastaUnidades4, TarifaPrecio.Precio4, TarifaPrecio.HastaUnidades5, TarifaPrecio.Precio5, TarifaPrecio.HastaUnidades6, TarifaPrecio.Precio6, TarifaPrecio.HastaUnidades7, TarifaPrecio.Precio7, TarifaPrecio.HastaUnidades8, TarifaPrecio.Precio8, TarifaPrecio.HastaUnidades9, TarifaPrecio.Precio9, TarifaPrecio.HastaUnidades10, TarifaPrecio.Precio10 FROM BLM_PV_TarifasClienteFinal INNER JOIN Tarifas ON BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa AND -1 = Tarifas.BLM_TarifaCliHeptan INNER JOIN TarifaPrecio ON BLM_PV_TarifasClienteFinal.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = TarifaPrecio.Tarifa INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1";

//$whereDelegaciones = Tarifas.Tarifa = Tarifa1ªQuery;

$delegacionesQuery1 = "SELECT DISTINCT Delegaciones.IdDelegacion, Tarifas.Tarifa FROM TarifaPrecio INNER JOIN Comisionistas ON 1 = Comisionistas.CodigoEmpresa AND TarifaPrecio.Tarifa = Comisionistas.BLM_TarifaPrecioMinimo AND Comisionistas.BLM_TarifaPrecioMinimo <> '0' INNER JOIN Tarifas ON Comisionistas.BLM_TarifaPrecioMinimo = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1 INNER JOIN Delegaciones ON CAST(Comisionistas.CodigoComisionista AS varchar(10)) = Delegaciones.IdDelegacion AND Comisionistas.CodigoEmpresa = Delegaciones.CodigoEmpresa AND -1 = Delegaciones.BLM_DPMobility";

$delegacionesQuery2 = "SELECT DISTINCT Delegaciones.IdDelegacion, Tarifas.Tarifa FROM BLM_PR266_TarifasBases INNER JOIN Tarifas ON BLM_PR266_TarifasBases.Tarifa = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN TarifaPrecio ON Tarifas.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND Tarifas.Tarifa = TarifaPrecio.Tarifa INNER JOIN BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1 INNER JOIN Delegaciones ON CAST(BLM_PR266_TarifasBases.CodigoComisionista AS varchar(10)) = Delegaciones.IdDelegacion AND 1 = Delegaciones.CodigoEmpresa AND -1 = Delegaciones.BLM_DPMobility";

//$whereConsumidores =  BLM_CodigoTarifa1 = Tarifa1ªQuery;

$consumidoresQuery1 = "SELECT DISTINCT BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1, BLM_PV_MailClientes.CifDni FROM BLM_PV_MailClientes INNER JOIN BLM_PV_TarifasClienteFinal ON BLM_PV_MailClientes.MovPosicion = BLM_PV_TarifasClienteFinal.MovOrigen INNER JOIN Tarifas ON BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = Tarifas.Tarifa AND 2 = Tarifas.CodigoEmpresa INNER JOIN TarifaPrecio ON BLM_PV_TarifasClienteFinal.CodigoEmpresa = TarifaPrecio.CodigoEmpresa AND BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = TarifaPrecio.Tarifa INNER JOIN BLM_DatosNif ON BLM_PV_MailClientes.CifDni = BLM_DatosNif.CifDni INNER JOIN  BLM_PR120_ConversionArticulos ON TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo AND BLM_PR120_ConversionArticulos.BLM_EnvioHeptan = - 1";

if(mssql_num_rows($registro) > 0) {
    foreach ($data as $key => $tarifa) {
        $detallesData = [];
        $delegacionesData = [];
        $consumidoresData = [];
		
		//DELEGACIONES
        $delegacionesWhere = "Tarifas.Tarifa = '".$tarifa['CodigoTarifa']."'";
		
		if ($tipoTarifa == 2 || $tipoTarifa == 0) {
			switch ($tipoTarifa) {
				case 2:
					$delegacionesQuery = $delegacionesQuery1." WHERE ".$delegacionesWhere;
					break;
				case 0:
					$delegacionesQuery = $delegacionesQuery2." WHERE ".$delegacionesWhere;
					break;
				case 1:
					$delegacionesQuery = $delegacionesQuery2." WHERE 1 = 2";
					break;
				default:
					break;
			}

			$delegaciones = mssql_query($delegacionesQuery);
			while ($row = mssql_fetch_array($delegaciones, MSSQL_ASSOC)){
				$row2 = array_map('utf8_encode', $row);
				$delegacionesData[]= ['IdDelegacion' => $row2['IdDelegacion']];
			}

			$data[$key]['Delegaciones'] = $delegacionesData;
		}

        //DETALLES
        $detallesWhere = "TarifaPrecio.FechaInicio = '".$tarifa['Fecha']."' AND Tarifas.Tarifa = '".$tarifa['CodigoTarifa']."' AND BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = '".$tarifa['CodigoArticulo']."'";

		switch ($tipoTarifa) {
			case 2:
				$detallesQuery = $detallesQuery1." WHERE ".$detallesWhere;
				break;
			case 0:
				$detallesQuery = $detallesQuery2." WHERE ".$detallesWhere;
				//echo $detallesQuery;
				//die;
				break;
			case 1:
				$detallesQuery = $detallesQuery3." WHERE ".$detallesWhere;
				break;
			default:
				break;
		}

        $detalles = mssql_query($detallesQuery);
        while ($row = mssql_fetch_array($detalles, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $detallesData[]=$row2;
        }
        if (count($detallesData) > 0 ) {
            foreach ($detallesData as $detalle) {
                for ($i=1;$i<=10;$i++) {
                    if ($detalle['HastaUnidades'.$i] > 0) {
                        $data[$key]['Detalles'][] = [
                            'HastaUnidades' => $detalle['HastaUnidades'.$i],
                            'Precio' => $detalle['Precio'.$i]
                        ];
                    }
                }
            }
        } else {
            $data[$key]['Detalles'] = $detallesData;
        }

        //CONSUMIDORES
        // $consumidoresWhere = "BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1 = '".$tarifa['CodigoTarifa']."'";
        // $consumidoresOrderBy = "BLM_PV_TarifasClienteFinal.BLM_CodigoTarifa1, BLM_PV_MailClientes.CifDni";

        // $consumidoresQuery = $consumidoresQuery1." WHERE ".$consumidoresWhere." ORDER BY ".$consumidoresOrderBy;

        // $consumidores = mssql_query($consumidoresQuery);
        // while ($row = mssql_fetch_array($consumidores, MSSQL_ASSOC)){
            // $row2 = array_map('utf8_encode', $row);
            // $consumidoresData[]=['CIFDNI' => $row2['CifDni']];
        // }

        // $data[$key]['Consumidores'] = $consumidoresData;
    }

}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
	// Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($fechaString)) {
		print_json(200, 1, "No data found. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, 1, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	}
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
	//Pasamos la fecha a formato correcto
	$data = dateTransform($data);

	// Imprime la informacion solicitada
	print_json(200, 0, "Success", $data);
}

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_fuelsv2_class;
	return $object;
}

// Esta funcion renderiza la informacion que sera enviada a la base de datos
function renderizeData($keys, $values) {
    $str = "";
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
    foreach ($data as $key => $tarifa) {
        //FechaAlta
        if($tarifa['Fecha'] != '') {
            $strDate = strtotime($tarifa['Fecha']);
            $badDate = date('d/m/Y', $strDate );
                $data[$key]['Fecha'] = $badDate;
        }
		
		//CodigoTarifa
		if($tarifa['CodigoTarifa'] != '') {
			$data[$key]['CodigoTarifa'] = intVal($tarifa['CodigoTarifa']);	
		}
		
		//TipoTarifa
		if($tarifa['TipoTarifa'] != '') {
			$data[$key]['TipoTarifa'] = intVal($tarifa['TipoTarifa']);	
		}
		
		if(count($tarifa['Detalles']) > 0) {
			$data[$key]['Detalles'] = dateTransformDetalles($tarifa['Detalles']);
		}
    }
	return $data;
}

function dateTransformDetalles($data) {
    foreach ($data as $key => $detalle) {	
		//HastaUnidades (double)
		if($detalle['HastaUnidades'] != '') {
			$data[$key]['HastaUnidades'] = doubleVal($detalle['HastaUnidades']);	
		}
		
		//Precio (double)
		if($detalle['Precio'] != '') {
			$data[$key]['Precio'] = doubleVal($detalle['Precio']);	
		}
    }
	return $data;
}


// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $queryStatus, $mensaje, $data) {
	//print_r( $data);
	header("HTTP/1.1 $status $mensaje");
	header("Content-Type: application/json; charset=UTF-8");

    $responseIn['Code'] = $queryStatus;
    $responseIn['Description'] = $mensaje;
	
	$response['Tarifas'] = $data;
    $response['response'] = $responseIn;

	
	echo json_encode($response, JSON_UNESCAPED_SLASHES); //, JSON_PRETTY_PRINT);
}
?>