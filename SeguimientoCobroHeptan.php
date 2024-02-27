<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_seguimientocobroheptan.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

error_reporting(E_ALL ^ E_WARNING);
//error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" SEGUIMIENTOCOBRO", $bodyRequest);

$response = [];

$FechaActual=date("Y-m-d H:i");
//echo $FechaActual;
$horas=date("H" , strtotime($FechaActual));
$minutos=date("i" , strtotime($FechaActual));
$FechaInicialLc=date("m/d/Y" , strtotime($FechaActual));
$horas=$horas/24.00;
$minutos=$minutos/24.00/60.00;
//echo $horas."   ".$minutos;die;
$HoraInicioPrevista=$horas+$minutos;
//echo $FechaInicialLc."    ".$HoraInicioPrevista;die;
foreach ($params as $Seguimiento) {
    //Parametros de la Seguimiento
    $IdEfecto = $Seguimiento->IdEfecto;
    $Fecha = $Seguimiento->Fecha;
    $Comentario = utf8_decode(str_replace("'","''",$Seguimiento->Comentario));
    
    //Cast de fechas para insert correcto
    $Fecha = castDateToInsert($Fecha);
    //$FechaInicialLc = castDateToInsert($FechaInicialLc);
	//echo "pasa";
    //CONTROLAR SI EXISTE LA ACCION ADMINISTRATIVA
    $accionencontrada=1;
    $AccionPosicion = 0;
    $MovCartera='';
    $TextoLlamadaLc="";
    $efectosQuery="SELECT CONVERT(char(36), MovCartera) AS MovCartera, BLM_AccionPosicionLc, CodigoClienteProveedor, IdDelegacion,NumeroEfecto,ImporteEfecto,StatusImpagado
        FROM CarteraEfectos WITH (nolock)
		where CodigoEmpresa=1 AND MovCartera = CONVERT(char(36), '".$IdEfecto."')";
	//echo $efectosQuery;die;
    $efectos=mssql_query($efectosQuery);
    while ($row = mssql_fetch_array($efectos, MSSQL_ASSOC)){
        $row2 = array_map('utf8_encode', $row);
        $MovCartera = $row2['MovCartera'];
        $IdDelegacion = $row2['IdDelegacion'];
        $CodigoCliente = $row2['CodigoClienteProveedor'];
        $AccionPosicion = $row2['BLM_AccionPosicionLc'];
        $TextoLlamadaLc = "NumeroEfecto: ".$row2['NumeroEfecto']." / ImporteEfecto ".$row2['ImporteEfecto'];
        if ($row2['StatusImpagado']==0) {
            $TextoLlamadaLc.=" ::: DEUDA VENCIDA";
        } else {
            $TextoLlamadaLc.=" ::: EFECTO IMPAGADO";
        }
    }
    
    if ($MovCartera=='') {
        $results[] = [
            'IdEfecto' => $IdEfecto,
            'ResultCode' => 1,
            'ResultText' => 'No existe el efecto. '
        ];
        $accionencontrada=0;
    } else {
        if ($AccionPosicion==0) {
            //CREAR ACCION ADMINISTRATIVA
            $AccionPosicionLc=0;
            $AccionesQuery="SELECT      CodigoEmpresa, MAX(AccionPosicionLc) AS AccionPosicionLc
                FROM         LcAccionesAdministrativas 
                GROUP BY CodigoEmpresa
                HAVING CodigoEmpresa=1";
            $Acciones=mssql_query($AccionesQuery);
            while ($row = mssql_fetch_array($Acciones, MSSQL_ASSOC)){
                $row2 = array_map('utf8_encode', $row);
                $AccionPosicionLc = $row2['AccionPosicionLc']+1;
            }
            if ($AccionPosicionLc==0) {
                $results[] = [
                    'IdEfecto' => $IdEfecto,
                    'ResultCode' => 1,
                    'ResultText' => 'Error contador tarea administrativa. '
                ];
                $accionencontrada=0;
            } else {
                //NUEVA ACCION
                //CARGA CLIENTE
                $clientesQuery="Select CodigoCliente,ComercialAsignadoLc, EMail1, EMail2, Fax, PersonaClienteLc, RazonSocial, Telefono, Telefono2, Telefono3
                    from Clientes where CodigoEmpresa=1 AND CodigoCliente= '" .$CodigoCliente ."'";
                $clientes=mssql_query($clientesQuery);
                while ($row = mssql_fetch_array($clientes, MSSQL_ASSOC)){
                    $row2 = array_map('utf8_encode', $row);
                    $ComercialAsignadoLc = $row2['ComercialAsignadoLc'];
                    $EMail1 = utf8_decode(str_replace("'","''",$row2['EMail1']));
                    $EMail2 = utf8_decode(str_replace("'","''",$row2['EMail2']));
                    $Fax = utf8_decode(str_replace("'","''",$row2['Fax']));
                    $PersonaClienteLc = utf8_decode(str_replace("'","''",$row2['PersonaClienteLc']));
                    $RazonSocial = utf8_decode(str_replace("'","''",$row2['RazonSocial']));
                    $Telefono = utf8_decode(str_replace("'","''",$row2['Telefono']));
                    $Telefono2 = utf8_decode(str_replace("'","''",$row2['Telefono2']));
                    $Telefono3 = utf8_decode(str_replace("'","''",$row2['Telefono3']));
                }
                $obj->entity = "LcAccionesAdministrativas";
                $data = " 1,
					 '".$IdDelegacion."',
					 '".$CodigoCliente."',
					 ".$AccionPosicionLc.",
					 ".$ComercialAsignadoLc.",
					 '".$FechaInicialLc."',
					 ".$HoraInicioPrevista.",
					 '".$RazonSocial."',
					 '".$PersonaClienteLc."',
					 '".$Telefono."',
					 '".$Telefono2."',
					 '".$Telefono3."',
					 '".$Fax."',
					 '".$EMail1."',
					 '".$EMail2."',
					 '".$TextoLlamadaLc."',
					 'IMPAGADOS'";
                $obj->data = $data;
                $resulttarea = $obj->CrearTarea();
                if ($resulttarea==1) {
                    //ACTUALIZAR EFECTO
                    $obj->entity = "CarteraEfectos";
                    $data = "BLM_AccionPosicionLc = ".$AccionPosicionLc;
					$obj->data = $data;
                    $result = $obj->putefecto($IdEfecto);
                    $accionencontrada=1;
                } else {
		            $results[] = [
						'IdEfecto' => $IdEfecto,
						'ResultCode' => 1,
						'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
					];            

                    $accionencontrada=0;
                }
            }
        } else {
            $accionencontrada=1;
        }
    }
    
    if ($accionencontrada==1){
        //CREAR SEGUIMIENTO
        //
        $PosicionSeguimiento=0;
        $AccionesQuery="SELECT  TOP 1    SeguimientoPosicionLc
                FROM         LcAccionesAdministrativasSeguimiento
                WHERE CodigoEmpresa=1 AND IdDelegacion='".$IdDelegacion."' AND CodigoCliente='".$CodigoCliente."' AND AccionPunteroLc=".$AccionPosicionLc;
        $Acciones=mssql_query($AccionesQuery);
        while ($row = mssql_fetch_array($Acciones, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $PosicionSeguimiento = $row2['SeguimientoPosicionLc'];
        }
        $PosicionSeguimiento=$PosicionSeguimiento+10;
        
        $obj->entity = "LcAccionesAdministrativasSeguimiento";
        $data = " 1,
					 '".$IdDelegacion."',
					 '".$CodigoCliente."',
					 ".$AccionPosicionLc.",
					 '',
					 '".$Fecha."',
					 ".$HoraInicioPrevista.",
					 '".$Comentario."',
					 ".$PosicionSeguimiento.",
					 0";
        $obj->data = $data;
        $resultseguimiento = $obj->CrearSeguimiento();
        if ($resultseguimiento==1) {
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
}

print_json($results);

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_seguimientocobroheptan_class;
    return $object;
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
    Traza(" SEGUIMIENTOCOBRO_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>