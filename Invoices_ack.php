<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: POST");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_invoice.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
//error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();
$obj->entity = 'ResumenCliente';
$obj->data = 'BLM_Heptan_sync = -1';

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" INVOICES_ACK", $bodyRequest);
$results = true;

foreach ($params as $invoice) {
    $ejercicioFactura = $invoice->EjercicioFactura;
	$serieFactura = $invoice->SerieFactura;
	$numeroFactura = $invoice->NumeroFactura;
	$CodigoEmpresa=3;
	if ($serieFactura=="VA"){
		$CodigoEmpresa=2;
	}
    $ResultCode = $invoice->ResultCode;
    $ResultText = $invoice->ResultText;

    //Si viene Code == 0 actualizamos el cliente
    if ($ResultCode == 0 || $ResultCode==-102 || $ResultCode==-107) {
    //if ($ResultCode == 0 || $ResultCode==-102) {
        $response = $obj->setSincronized($CodigoEmpresa,$ejercicioFactura, $serieFactura, $numeroFactura);
        if(!isInvoicesynced($obj, $CodigoEmpresa,$ejercicioFactura, $serieFactura, $numeroFactura)) {
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
    $object = new model_invoice_class;
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
function isInvoicesynced($obj, $CodigoEmpresa,$ejercicioFactura, $serieFactura, $numeroFactura) {
    $status = $obj->getSyncStatus($CodigoEmpresa,$ejercicioFactura, $serieFactura, $numeroFactura);
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
    Traza(" INVOICES_ACK_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>