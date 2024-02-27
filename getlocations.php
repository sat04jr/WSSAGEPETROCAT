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

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
$block = $_GET['Block'];
$codigoCliente = $_GET['CodigoCliente'];
$codigoDescarga = $_GET['CodigoDescarga'];

$select = "Clientes.CodigoCliente + CAST(Depositos.NumeroDeposito AS nvarchar) AS CodigoDescarga, BLM_DatosNif.BLM_CodigoClienteHeptan AS CodigoCliente, Depositos.DomicilioDeposito AS DomicilioDescarga, Depositos.CodigoMunicipio AS CodigoMunicipioDescarga, Depositos.Municipio AS MunicipioDescarga, Depositos.CodigoPostal AS CodigoPostalDescarga, Depositos.CodigoProvincia AS CodigoProvinciaDescarga, Depositos.CodigoEmpresa AS CodigoVendedor, Depositos.MetrosManguera, Depositos.IdDelegacion, Depositos.FechaCaducidadCAE, Depositos.CAE, Depositos.CodigoCanal, Clientes.CodigoComisionista, Clientes.FormadePago, ClientesConta.NumeroPlazos, ClientesConta.DiasPrimerPlazo, ClientesConta.DiasEntrePlazos, ClientesConta.DiasFijos1, ClientesConta.DiasFijos2, ISNULL(Bancos.BIC, '') AS BIC, Clientes.IBAN, Clientes.ReferenciaMandato, Clientes.Telefono, Clientes.Telefono2, Clientes.Fax, Clientes.EMail1, Clientes.PersonaClienteLc, Clientes.ClienteFinal, Clientes.CIM, DF.Domicilio AS DomicilioFactura, DF.CodigoPostal AS CodigoPostalFactura, DF.CodigoMunicipio AS CodigoMunicipioFactura, DF.Municipio AS MunicipioFactura, DF.Provincia AS ProvinciaFactura, MFAC.CodigoAutonomia AS CodigoAutonomiaFactura, MFAC.CodigoNacion AS CodigoPaisFactura, Clientes.FacturacionElectronica, Clientes.EnvioEFactura, Clientes.EmailEnvioEFactura, Clientes.BLM_AAPPOficinaContable, Clientes.BLM_AAPPOficinaContableNombre, Clientes.BLM_AAPPOrganoGestor, Clientes.BLM_AAPPOrganoGestorNombre, Clientes.BLM_AAPPUnidadTramitadora, Clientes.BLM_AAPPUnidadTramitadoraNom";

$tablasQuery = "Clientes WITH (nolock) INNER JOIN Depositos WITH (nolock) ON 3 = Depositos.CodigoEmpresa AND Clientes.CodigoCliente = Depositos.CodigoCliente INNER JOIN BLM_DatosNif WITH (nolock) ON Clientes.CifDni = BLM_DatosNif.CifDni INNER JOIN ClientesConta WITH (nolock) ON Clientes.CodigoEmpresa = ClientesConta.CodigoEmpresa AND Clientes.CodigoContable = ClientesConta.CodigoCuenta LEFT OUTER JOIN Bancos WITH (nolock) ON SUBSTRING(Clientes.IBAN, 5, 4) = Bancos.CodigoBanco LEFT OUTER JOIN Domicilios AS DF ON Clientes.DomicilioFactura = DF.NumeroDomicilio AND Clientes.CodigoCliente = DF.CodigoCliente AND 'F' = DF.TipoDomicilio AND Clientes.CodigoEmpresa = DF.CodigoEmpresa LEFT OUTER JOIN Municipios AS MFAC ON MFAC.CodigoMunicipio = DF.CodigoMunicipio";

$where = " (BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (Depositos.CodigoEmpresa = 3) AND (Clientes.CodigoEmpresa = 1)";
$top = "";

//Montamos el where de la query
if ($codigoCliente != '') {
    $where .= " AND BLM_DatosNif.BLM_CodigoClienteHeptan = '".$codigoCliente."'";
}

if ($codigoDescarga != '') {
    $where .= " AND clientes.CodigoCliente + CAST(Depositos.NumeroDeposito AS nvarchar) ='".$codigoDescarga."'";
}

if ($block != null && $block > 0) {
    $top = "TOP ".intVal($block)." ";
}

if ($heptanStatus == 'Pending') {
    $where .= " AND (BLM_DatosNif.BLM_Heptan_sync = 0)";
}

$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where;
//echo $query;die;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
    $row2 = array_map('utf8_encode', $row);
    $data[]=$row2;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
    print_json(200, "No data found. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
    //Pasamos la fecha a formato correcto
    $data = dateTransform($data);

    //Sacamos los depositos de cada descarga
    $data = groupDepositos($data, $tablasQuery);

    // Imprime la informacion solicitada
    print_json(200, "Success", $data);
}

//Cerramos la conexion.
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

//Transforma a formato correcto la fecha
function dateTransform($data) {
    foreach ($data as $key => $descarga) {
        if($descarga['FechaCaducidadCAE'] != '') {
            $strDate = strtotime($descarga['FechaCaducidadCAE']);
            $badDate = date('d/m/Y H:i', $strDate );
            $data[$key]['FechaCaducidadCAE'] = $badDate;
        }

    }
    return $data;
}

//Agrupa depositos en descargas
function groupDepositos($data, $tablasQuery) {
    $depositosSelect = "Clientes.CodigoCliente + CAST(Depositos.NumeroDeposito AS nvarchar) AS CodigoDeposito, Depositos.DomicilioDeposito as DomicilioDeposito, Depositos.TipoDeposito, Depositos.CodigoArticulo, Depositos.CapacidadDeposito, Depositos.ObservacionesDeposito, Depositos.TipoDepositoCliente, Depositos.BLM_GoBonificado";

    foreach ($data as $key => $descarga) {
        $depositosWhere = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '') AND (Depositos.CodigoEmpresa = 3) AND (Clientes.CodigoEmpresa = 1) AND Clientes.CodigoCliente = '".$descarga['CodigoCliente']."' AND Clientes.CodigoCliente + CAST(Depositos.NumeroDeposito AS nvarchar) = '".$descarga['CodigoDescarga']."'";

        $depositosQuery="SELECT ".$depositosSelect." FROM ".$tablasQuery." WHERE ".$depositosWhere;

        $depositos=mssql_query($depositosQuery);
        while ($row = mssql_fetch_array($depositos, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $data[$key]['Depositos'][]=$row2;
        }

    }
    return $data;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje, $data) {
    //print_r( $data);
    header("HTTP/1.1 $status $mensaje");
    header("Content-Type: application/json; charset=UTF-8");

    $response['Descargas'] = $data;
    $response['Code'] = $status;
    $response['Description'] = $mensaje;

    echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
}
?>