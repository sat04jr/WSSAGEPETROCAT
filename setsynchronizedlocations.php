<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_locations.php';

$bodyRequest = file_get_contents("php://input");

// Variable que guarda la instancia de la clase generica
$obj = get_obj();
$obj->entity = 'BLM_DatosNif';
$obj->data = 'BLM_Heptan_sync = -1';

//Capturamos las variables
$params = json_decode($bodyRequest);

$codigoDescarga = $params->CodigoDescarga;
$code = $params->Code;
$descError = $params->DescriptionError;

$select = "BLM_DatosNif.BLM_CodigoClienteHeptan AS CodigoCliente";

$tablasQuery = "Clientes WITH (nolock) INNER JOIN Depositos WITH (nolock) ON 3 = Depositos.CodigoEmpresa AND Clientes.CodigoCliente = Depositos.CodigoCliente INNER JOIN BLM_DatosNif WITH (nolock) ON Clientes.CifDni = BLM_DatosNif.CifDni INNER JOIN ClientesConta WITH (nolock) ON Clientes.CodigoEmpresa = ClientesConta.CodigoEmpresa AND Clientes.CodigoContable = ClientesConta.CodigoCuenta LEFT OUTER JOIN Bancos WITH (nolock) ON SUBSTRING(Clientes.IBAN, 5, 4) = Bancos.CodigoBanco LEFT OUTER JOIN Domicilios AS DF ON Clientes.DomicilioFactura = DF.NumeroDomicilio AND Clientes.CodigoCliente = DF.CodigoCliente AND 'F' = DF.TipoDomicilio AND Clientes.CodigoEmpresa = DF.CodigoEmpresa LEFT OUTER JOIN Municipios AS MFAC ON MFAC.CodigoMunicipio = DF.CodigoMunicipio";

$where = " (BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (Depositos.CodigoEmpresa = 3) AND (Clientes.CodigoEmpresa = 1) AND clientes.CodigoCliente + CAST(Depositos.NumeroDeposito AS nvarchar) ='".$codigoDescarga."'";

$query="SELECT TOP 1 ".$select." FROM ".$tablasQuery." WHERE ".$where;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $data[]=$row2;
}

//print_r($data);die;

if(mssql_num_rows($registro)==0) {
    print_json(404, "No data found", null);
} else {
    $codigoCliente = $data[0]['CodigoCliente'];

    //Si viene Code == 0 actualizamos el cliente
    if ($code == 0) {
        $response = $obj->setSincronized($codigoCliente);
        if(isClientsynced($obj, $codigoCliente)) {
            print_json(200, 'Success');
        } else {
            print_json(400, 'Fail');
        }

    } else {
        print_json(200, 'Nothing to do');
    }
}

$obj = null;


// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_locations_class;
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
function isClientsynced($obj, $codigoCliente) {
    $status = $obj->getSyncStatus($codigoCliente);
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
}
?>