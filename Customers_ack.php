<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_clientes.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();
$obj->entity = 'BLM_DatosNif';
$obj->data = 'BLM_Heptan_sync = -1';

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" CUSTOMERS_ACK", $bodyRequest);
$results = true;

foreach ($params as $client) {
    $CifDni = $client->CifDni;
    $ResultCode = $client->ResultCode;
    $ResultText = $client->ResultText;

    //Si viene Code == 0 actualizamos el cliente
    if ($ResultCode == 0) {
        $response = $obj->setSincronized($CifDni);
        if(!isClientsynced($obj, $CifDni)) {
            $results = false;
        }
    }
}

if ($results) {
    print_json(200, 'Ok');
} else {
    print_json(400, 'Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']);
}

$obj = null;


// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_clientes_class;
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

//Comprueba que efectivamente el estado de sincronizacion de un usuario se encuentra a -1
function isClientsynced($obj, $CifDni) {
    $status = $obj->getSyncStatus($CifDni);
    $sync = 0;
    if (count($status) > 1) {
        $sync = $status[0]['BLM_Heptan_sync'];
    }

    if ($sync == -1) {
        return true;
    } else {
        return false;
    }

}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje) {
    //print_r( $data);
    header("HTTP/1.1 $status $mensaje");
    header("Content-Type: application/json; charset=UTF-8");

    $response['Code'] = $status;
    $response['Description'] = $mensaje;

    echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
    Traza(" CUSTOMERS_ACK_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>