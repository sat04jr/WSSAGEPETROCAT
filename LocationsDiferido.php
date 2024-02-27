<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_locations_diferido.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();
$WSMetodo="LocationsHeptan";
//Capturamos las variables
$params = json_decode($bodyRequest);
//Traza(" LOCATIONSHEPTANDIFERIDO", $bodyRequest);
$response = [];
$descargasData = [];

$descargasQuery="SELECT top 10 CONVERT(char(36), LineasPosicion) AS LineasPosicion
FROM            WSHeptan_Descargas
WHERE        (BLM_WSProcesado = 0) AND
                             ((SELECT        COUNT(*) AS Expr1
                                 FROM            WSHeptan_Depositos
                                 WHERE        (WSHeptan_Descargas.LineasPosicion = LineasPosicionDescarga) AND (BLM_WSProcesado = 0)) > 0)
ORDER BY BLM_WSFecha DESC ";
$descargas = mssql_query($descargasQuery);
while ($row = mssql_fetch_array($descargas, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $descargasData[]=$row2;
}

if (count($descargasData) > 0 ) {
    foreach ($descargasData as $descarga) {
		//sleep(1);
        $i=1;
		//echo var_dump($descarga);
        $DescargaPosicion= $descarga['LineasPosicion'];
		//echo $DescargaPosicion;
        $bd='Logic';
        $result=$obj->ejecutaprocedimiento($bd,$DescargaPosicion);
        $descargaprocesada= $obj->get($DescargaPosicion);
        if (count($descargaprocesada) > 1) {
            $response[] = [
                'ID' => $DescargaPosicion,
                'ResultCode' => 0,
                'ResultText' => 'Insert Success'
            ];
        } else {
            $response[] = [
                'ID' => $DescargaPosicion,
                'ResultCode' => 1,
                'ResultText' => 'Insert Fail. No se ha podido crear el deposito'
            ];
        }
//		$result->close();
    }
}


print_json($response);

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_locations_diferido_class;
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
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
    Traza(" LOCATIONSHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>