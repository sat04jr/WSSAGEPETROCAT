<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_controlriesgo.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$cifDni = $_GET['CifDni'];

$stmt = mssql_init('BLM_PR119_RiesgosPorNIFAFCLass');

mssql_bind($stmt, '@CifDni', $cifDni, SQLVARCHAR, false, false, 13);
mssql_bind($stmt, '@Impagados', $impagados, SQLINT4, true);
mssql_bind($stmt, '@Vencidos', $vencidos, SQLFLT8, true);
mssql_bind($stmt, '@RiesgoMaximo', $riesgomaximo, SQLFLT8, true);
mssql_bind($stmt, '@SaldoCartera', $saldocartera, SQLFLT8, true);
mssql_bind($stmt, '@Albaranes', $albaranes, SQLFLT8, true);
mssql_bind($stmt, '@Pedidos', $pedidos, SQLFLT8, true);
mssql_bind($stmt, '@Saldo', $saldo, SQLFLT8, true);
mssql_bind($stmt, '@Disponible', $disponible, SQLFLT8, true);
mssql_bind($stmt, '@MensajeRetorno', $mensajeretorno, SQLVARCHAR, true, false, 255);
mssql_bind($stmt, '@Retorno', $retorno, SQLBIT, true);
mssql_bind($stmt, '@Impagados', $impagados, SQLFLT8, true);
mssql_bind($stmt, 'RETVAL', $p_salida, SQLINT4);

mssql_execute($stmt);

if ($impagados == null)
	$impagados = 0;
if ($vencidos == null)
	$vencidos = 0;
if ($riesgomaximo == null)
	$riesgomaximo = 0;
if ($saldocartera == null)
	$saldocartera = 0;
if ($albaranes == null)
	$albaranes = 0;
if ($pedidos == null)
	$pedidos = 0;
if ($disponible == null)
	$disponible = 0;
$data['ControlRiesgo']['Impagados'] = $impagados;
$data['ControlRiesgo']['Vencidos'] = $vencidos;
$data['ControlRiesgo']['RiesgoMaximo'] = $riesgomaximo;
$data['ControlRiesgo']['SaldoCartera'] = $saldocartera;
$data['ControlRiesgo']['Albaranes'] = $albaranes;
$data['ControlRiesgo']['Pedidos'] = $pedidos;
$data['ControlRiesgo']['Disponible'] = $disponible;
$data['ControlRiesgo']['Mensaje'] = $mensajeretorno;
if ($mensajeretorno == 'No se encuentra este nif') {
	//$data['Response']['Code'] = 400;
	//$data['Response']['Description'] = 'Fail';
	$data['Code'] = 400;
	$data['Description'] = 'Fail';
}
else{
	$data['Code'] = 200;
	$data['Description'] = 'Success';
}





	// echo json_encode($registro);die;
	// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional

//if(mssql_num_rows($stmt) == 0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
//print_json(400, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
//} else {
    //Pasamos la fecha a formato correcto
//$data = dateTransform($data);
//print json_encode($data);
echo json_encode($data, JSON_UNESCAPED_SLASHES); //JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);

mssql_free_statement($stmt);
    // Imprime la informacion solicitada
//print_json(200, "Success", $data);
//}

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_controlRiesgo_class;
    return $object;
}
?>