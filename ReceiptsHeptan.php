<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_receipts_heptan.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" RECEIPTSRHEPTAN", $bodyRequest);

$response = [];

foreach ($params as $efecto) {
    //Parametros del efecto
    $IdEfecto = $efecto->IdEfecto;
	$IdDelegacion = str_replace("'","''",$efecto->IdDelegacion);
	$Prevision = str_replace("'","''",$efecto->Prevision);
	$Bloqueo = $efecto->Bloqueo;
	$StatusBorrado = $efecto->StatusBorrado;
	$StatusRemesado = $efecto->StatusRemesado;
	$StatusImpagado = $efecto->StatusImpagado;
	$Ejercicio = $efecto->Ejercicio;
	$SerieFactura = str_replace("'","''",$efecto->SerieFactura);
	$Factura = $efecto->Factura;
	$Comentario = utf8_decode(str_replace("'","''",$efecto->Comentario));
	$NumeroOrden = $efecto->NumeroOrdenEfecto;
	$CifDni = str_replace("'","''",$efecto->CifDni);
	$Formadepago = $efecto->CodigoTipoEfecto;
	$ReferenciaMandato = str_replace("'","''",$efecto->ReferenciaMandato);
	$IBAN = str_replace("'","''",$efecto->IBAN);
	$FechaEmision = $efecto->FechaEmision;
	$FechaFactura = $efecto->FechaFactura;
	$FechaRemesa = $efecto->FechaRemesa;
	$FechaVencimiento = $efecto->FechaVencimiento;
	$FechaCobroEfecto_ = $efecto->FechaCobroEfecto_;
	$ImporteEfecto = $efecto->ImporteEfecto;
	$ImportePendiente = $efecto->ImportePendiente;
	$ImporteCobrado = $efecto->ImporteCobrado;
	$Remesable = $efecto->Remesable;
	$Contrapartida = str_replace("'","''",$efecto->Contrapartida);
	$IdCobrador = str_replace("'","''",$efecto->IdCobrador);
	$IdEfectoDivision = str_replace("'","''",$efecto->IdEfectoDivision);
	$FechaDivision = str_replace("'","''",$efecto->FechaDivision);
	
	//Cast de fechas para insert correcto
	$FechaEmision = castDateToInsert($FechaEmision);
	$FechaFactura = castDateToInsert($FechaFactura);
	$FechaRemesa = castDateToInsert($FechaRemesa);
	$FechaVencimiento = castDateToInsert($FechaVencimiento);
	$FechaCobroEfecto_ = castDateToInsert($FechaCobroEfecto_);
	$FechaDivision = castDateToInsert($FechaDivision);
	
	if (strlen($IdEfectoDivision) == 36){
		$SqlIdEfectoDivision = "CONVERT(uniqueidentifier, '".$IdEfectoDivision."')";
	}else{
		$SqlIdEfectoDivision = "CONVERT(uniqueidentifier, '00000000-0000-0000-0000-000000000000')";
	}
		
		

    $obj->entity = "CarteraEfectos";
	$CodigoClienteProveedor = "";
	$registro = mssql_query("SELECT BLM_CodigoClienteHeptan FROM BLM_DatosNif WHERE CifDni = '" .$CifDni ."'");
	while ($row = mssql_fetch_array($registro, MSSQL_ASSOC))
		$CodigoClienteProveedor =  $row['BLM_CodigoClienteHeptan'];


	if ($IdEfecto == "") {
		$IdEfecto = createGUID();
		
		//SI EFECTO DIVISION BUSCAR MOVPOSICION EFECTO ORIGINAL
		if (strlen($IdEfectoDivision) == 36){
		    $MovPosicion = "";
		    $CodigoCanal = "";
		    $ClaseEfecto = "";
		    $TipoEfecto = "";
		    $CodigoComisionista = "";
		    $registro = mssql_query("SELECT CONVERT(char(36), MovPosicion) as MovPosicion, CodigoCanal, ClaseEfecto, TipoEfecto, CodigoComisionista FROM CarteraEfectos WHERE MovCartera = '" .$IdEfectoDivision ."'");
		    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
		        $row2 = array_map('utf8_encode', $row);
		        $data1[]=$row2;
		    }
		    // Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
		    if(mssql_num_rows($registro) > 0) {
		        $MovPosicion = $data1[0]["MovPosicion"];
		        $CodigoCanal = $data1[0]["CodigoCanal"];
		        $ClaseEfecto = $data1[0]["ClaseEfecto"];
		        $TipoEfecto = utf8_decode(str_replace("'","''",$data1[0]["TipoEfecto"]));
		        $CodigoComisionista = $data1[0]["CodigoComisionista"];
		    }
		    
		    if ($MovPosicion=="") {
		        //EFECTO ORIGINAL NO ENCONTRADO
		        $results[] = [
		            'IdEfecto' => $IdEfecto,
		            'ResultCode' => 1,
		            'ResultText' => 'Insert Fail. IdEfectoDivision incorrecto: ' .$IdEfectoDivision .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
		        ];
		        print_json($results);
		        
		        //Cerramos la conexion.
		        $obj = null;
		        die;
		    }
		        
		} else {
		    $MovPosicion = createGUID();
		}
		$data = "1,CONVERT(uniqueidentifier, '".$IdEfecto."'),
		'".$IdDelegacion."','".
		$Prevision."',".
		$Bloqueo.",".
		$StatusBorrado.",".
		$StatusRemesado.",".
		$StatusImpagado.",".
		$Ejercicio.",'".
		$SerieFactura."',".
		$Factura.",'".
		$Comentario."',".
		$NumeroOrden.",'".
		$CodigoClienteProveedor."','".
		$CodigoClienteProveedor."','".
		$CodigoCanal."','".
		$ClaseEfecto."','".
		$CodigoComisionista."','".
		$TipoEfecto."',".
		$Formadepago.",'".
		$ReferenciaMandato."','".
		$IBAN."','".
		$FechaEmision."','".
		$FechaFactura."','".
		$FechaRemesa."','".
		$FechaVencimiento."','".
		$FechaCobroEfecto_."',".
		$ImporteEfecto.",".
		$ImportePendiente.",".
		$ImporteCobrado.",".
		$Remesable.",'".
		$Contrapartida."','".
		$IdCobrador."',".
		$SqlIdEfectoDivision.",'".
		$FechaDivision."',
        CONVERT(uniqueidentifier, '".$MovPosicion."'),
        -1";
		
		$obj->data = $data;
		$result = $obj->post();
	} else {
		$data = "IdDelegacion = '".$IdDelegacion."',
		Prevision = '".$Prevision."',
		Bloqueo = '".$Bloqueo."',
		StatusBorrado = '".$StatusBorrado."',
		StatusRemesado = '".$StatusRemesado."',
		StatusImpagado = '".$StatusImpagado."',
		Ejercicio = '".$Ejercicio."',
		SerieFactura = '".$SerieFactura."',
		Factura = '".$Factura."',
		Comentario = '".$Comentario."',
		NumeroOrdenEfecto = '".$NumeroOrden."',
		CodigoClienteProveedor = '".$CodigoClienteProveedor."',
		CodigoTipoEfecto = '".$Formadepago."',
		ReferenciaMandato = '".$ReferenciaMandato."',
		IBAN = '".$IBAN."',
		FechaEmision = '".$FechaEmision."',
		FechaFactura = '".$FechaFactura."',
		FechaRemesa = '".$FechaRemesa."',
		FechaVencimiento = '".$FechaVencimiento."',
		FechaCobroEfecto_ = '".$FechaCobroEfecto_."',
		ImporteEfecto = '".$ImporteEfecto."',
		ImportePendiente = '".$ImportePendiente."',
		ImporteCobrado = '".$ImporteCobrado."',
		Remesable = '".$Remesable."',
		Contrapartida = '".$Contrapartida."',
		IdCobrador = '".$IdCobrador."',
		NumEfectoDivision_ = ".$SqlIdEfectoDivision.",
		FechaDivision = '".$FechaDivision."',
        BLM_Heptan_sync=-1";
			
		$obj->data = $data;
		$result = $obj->put($IdEfecto);
	}
	//echo $result;die;
 //echo $result;die;
if ($result == 1) {
        $results[] = [
            'IdEfecto' => $IdEfecto,
            'ResultCode' => 0,
            'ResultText' => 'Insert Success'
        ];
    } else {
        $results[] = [
            'IdEfecto' => $IdEfecto,
            'ResultCode' => 1,
            'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
        ];
    }
}

print_json($results);

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_cartera_heptan_class;
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

    echo json_encode($response, JSON_UNESCAPED_SLASHES); //JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
    Traza(" RECEIPTSHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>