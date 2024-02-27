<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_saleOrder_heptanJR.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" SALEORDERHEPTAN", $bodyRequest);

$response = [];

foreach ($params as $saleOrder) {
    //Parametros de la saleOrder
    $IdPedido = $saleOrder->IdPedido;
    $IdPedidoOriginal = $saleOrder->IdPedido;
    $IdDelegacion = $saleOrder->IdDelegacion;
    $EjercicioPedido = $saleOrder->EjercicioPedido;
    $SeriePedido = $saleOrder->SeriePedido;
    $NumeroPedido = $saleOrder->NumeroPedido;
    $Fecha = $saleOrder->Fecha;
    $CifDni = $saleOrder->CifDni;
    $IdDescarga = $saleOrder->idDescarga;
    $IdDeposito = $saleOrder->idDeposito;
    $CodigoArticulo = $saleOrder->CodigoArticulo;
    $UnidadesPedidas = $saleOrder->UnidadesPedidas;
    $FechaSuministro = $saleOrder->FechaSuministro;
    $Precio = $saleOrder->Precio;
    $DescuentoClienteBases = $saleOrder->DescuentoClienteBases;
    $Riesgo = $saleOrder->Riesgo;
    $Estado = $saleOrder->Estado;
    $CodigoCamion = $saleOrder->CodigoCamion;
    $CodigoConductor = $saleOrder->CodigoConductor;
    $CodigoCamionBase = $saleOrder->CodigoCamion;
    if (intval($CodigoCamionBase)>99){
        $CodigoCamionBase=0;
    }
    $CodigoConductorBase = $saleOrder->CodigoConductor;
    $ClavePeticion = $saleOrder->ClavePeticion;
    $ClaveAutorizacion = $saleOrder->ClaveAutorizacion;
    $ObservacionesPedido = utf8_decode(str_replace("'","''",$saleOrder->ObservacionesPedido));
    $PrecioOfertado = $saleOrder->PrecioOfertado;
    $N_Autorizacion = $saleOrder->N_Autorizacion;
    $CodigoCanal = $saleOrder->CodigoCanal;
    $BLM_PedidoWeb = $saleOrder->BLM_PedidoWeb;
    $BLM_NombreAgenteWeb = utf8_decode($saleOrder->BLM_NombreAgenteWeb);
    $BLM_UsuarioWeb = $saleOrder->BLM_UsuarioWeb;
    $BLM_PedidoCapturado = $saleOrder->BLM_PedidoCapturado;
    $BLM_EstadoPedido = $saleOrder->BLM_EstadoPedido;
    $BLM_AditivoExcelent = $saleOrder->BLM_AditivoExcelent;
    $BLM_PrecioAditivoExcelentUnit = $saleOrder->BLM_PrecioAditivoExcelentUnit;
    $BLM_FINCOM = $saleOrder->BLM_FINCOM;
    $BLM_ImportePagadoB2C = $saleOrder->BLM_ImportePagadoB2C;
    $BLM_CodigoOperacionB2C = $saleOrder->BLM_CodigoOperacionB2C;
    $CodiPromocional = $saleOrder->CodiPromocional;
    $BLM_Descuento = $saleOrder->BLM_Descuento;
    $DocumentoPDFAdjunto = '';
    $SuPedido = utf8_decode(str_replace("'","''",$saleOrder->SuPedido));
    $BLM_NumeroContratoFincom = $saleOrder->BLM_NumeroContratoFincom;
    $BLM_NumeroPlazosFincom = $saleOrder->BLM_NumeroPlazosFincom;
    $PedidoWhatsapp = $saleOrder->PedidoWhatsapp;
    $ObservacionesAutomaticas = utf8_decode(str_replace("'","''",$saleOrder->ObservacionesAutomaticas));
    $ObservacionesBase = utf8_decode(str_replace("'","''",$saleOrder->ObservacionesBase));
    $ObservacionesCentral = utf8_decode(str_replace("'","''",$saleOrder->ObservacionesCentral));
    $EstadoPeticion = $saleOrder->EstadoPeticion;
    $RiesgoDisponible = $saleOrder->RiesgoDisponible;
    
    if ($IdDescarga=="") {
        $IdDescarga = "00000000-0000-0000-0000-000000000000";
    }
    if ($IdDeposito=="") {
        $IdDeposito = "00000000-0000-0000-0000-000000000000";
    }
    
    //Cast de fechas para insert correcto
    $Fecha = castDateToInsert($Fecha);
    $FechaSuministro = castDateToInsert($FechaSuministro);
    
    $obj->entity = "PedidoClienteBases";
    
    //CONTROLAR SI EXISTE EL PEDIDO
    $seguirbuscando=1;
    if ($IdPedido == "") {
        $IdPedido = '';
        $pedidosQuery="SELECT CONVERT(char(36), BLM_IdPedido) AS BLM_IdPedido FROM PedidoClienteBases WITH (nolock)
						 where CodigoEmpresa=3 AND EjercicioPedido = ".$EjercicioPedido."
								 AND SeriePedido = '".$SeriePedido."' AND NumeroPedido=".$NumeroPedido;
        $pedidos=mssql_query($pedidosQuery);
        while ($row = mssql_fetch_array($pedidos, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $IdPedido = $row2['BLM_IdPedido'];
        }
        
        if (!$IdPedido=='') {
            if ($IdPedido=='00000000-0000-0000-0000-000000000000') {
                $IdPedido="";
                $results[] = [
                    'IdPedido' => $IdPedido,
                    'ResultCode' => 1,
                    'ResultText' => 'Ya existe el pedido anterior a Heptan. '
                ];
                $seguirbuscando=0;
            } else {
                $results[] = [
                    'IdPedido' => $IdPedido,
                    'ResultCode' => 0,
                    'ResultText' => 'Ya existe el pedido. '
                ];
                $seguirbuscando=0;
            }
        }
    }
    if ($seguirbuscando==1){
        $CodigoCliente = '';
        $clientesQuery="Select BLM_CodigoClienteHeptan from BLM_DatosNif where CifDni = '" .$CifDni ."'";
        $clientes=mssql_query($clientesQuery);
        while ($row = mssql_fetch_array($clientes, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $CodigoCliente = $row2['BLM_CodigoClienteHeptan'];
        }
        
        //CONTROLAR QUE EXISTE IDDEPOSITO Y IDDESCARGA
        $ExisteDeposito = '';
        $depositosQuery="SELECT Depositos.BLM_Heptan_IdDeposito FROM Depositos WITH (nolock) INNER JOIN
							 Heptan_Descargas WITH (nolock) ON Depositos.BLM_Heptan_IdDescargaPadre = Heptan_Descargas.BLM_IdDescarga
						 where Depositos.BLM_Heptan_IdDeposito = '".$IdDeposito."'
								 AND Depositos.BLM_Heptan_IdDescargaPadre = '".$IdDescarga."'";
        $depositos=mssql_query($depositosQuery);
        while ($row = mssql_fetch_array($depositos, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $ExisteDeposito = $row2['BLM_Heptan_IdDeposito'];
        }
        
        if ($ExisteDeposito=='') {
            //FORZAR ACTUALIZACION DEPOSITO
            $LineasPosicionDescarga='';
            $depositosQuery="SELECT TOP 1 CONVERT(char(36), LineasPosicionDescarga) AS LineasPosicionDescarga FROM WSHeptan.DBO.WSHeptan_Depositos
                WHERE BLM_IdDeposito='".$IdDeposito."'
                ORDER BY BLM_WSFecha DESC";
            $depositos=mssql_query($depositosQuery);
            while ($row = mssql_fetch_array($depositos, MSSQL_ASSOC)){
                $row2 = array_map('utf8_encode', $row);
                $LineasPosicionDescarga = $row2['LineasPosicionDescarga'];
            }
            if ($LineasPosicionDescarga<>'') {
                $descargasQuery="UPDATE WSHeptan.DBO.WSHeptan_Descargas SET BLM_WSProcesado=0
                WHERE LineasPosicion='".$LineasPosicionDescarga."'";
                $descargas=mssql_query($descargasQuery);
                $depositosQuery="UPDATE WSHeptan.DBO.WSHeptan_Depositos SET BLM_WSProcesado=0
                WHERE LineasPosicionDescarga='".$LineasPosicionDescarga."'";
                $descargas=mssql_query($depositosQuery);
                $bd='Logic';
                $result=$obj->ejecutaprocedimiento($bd,$LineasPosicionDescarga);
                
                //CONTROLAR QUE EXISTE IDDEPOSITO Y IDDESCARGA
                $ExisteDeposito = '';
                $depositosQuery="SELECT Depositos.BLM_Heptan_IdDeposito FROM Depositos WITH (nolock) INNER JOIN
							 Heptan_Descargas WITH (nolock) ON Depositos.BLM_Heptan_IdDescargaPadre = Heptan_Descargas.BLM_IdDescarga
						 where Depositos.BLM_Heptan_IdDeposito = '".$IdDeposito."'
								 AND Depositos.BLM_Heptan_IdDescargaPadre = '".$IdDescarga."'";
                $depositos=mssql_query($depositosQuery);
                while ($row = mssql_fetch_array($depositos, MSSQL_ASSOC)){
                    $row2 = array_map('utf8_encode', $row);
                    $ExisteDeposito = $row2['BLM_Heptan_IdDeposito'];
                }
            }
            
        }
            
        if ($ExisteDeposito=='') {
            $results[] = [
                'IdPedido' => $IdPedidoOriginal,
                'ResultCode' => 1,
                'ResultText' => 'No existe el deposito. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
            ];
        } else {
            if ($IdPedido == "") {
                //TODO : ObservacionesAutomaticas, observacionesBase, ObservacionesCentral, EstadoPeticion, RiesgoDisponible, IdDeposito, IdDescarga, CodigoCliente (CifDni)
                $IdPedido = createGUID();
                $data = "3, CONVERT(uniqueidentifier, '".$IdPedido."'),
				'".$IdDelegacion."',
				'".$EjercicioPedido."',
				'".$SeriePedido."',
				'".$NumeroPedido."',
				'".$CodigoCliente."',
				'".$Fecha."',
				'".$CodigoArticulo."',
				".$UnidadesPedidas.",
				'".$FechaSuministro."',
				".$Precio.",
				".$DescuentoClienteBases.",
				".$Riesgo.",
				'".$Estado."',
				'".$CodigoCamion."',
				'".$CodigoConductor."',
				'".$CodigoCamionBase."',
				'".$CodigoConductorBase."',
				'".$ClavePeticion."',
				'".$ClaveAutorizacion."',
				'".$ObservacionesPedido."',
				".$PrecioOfertado.",
				'".$N_Autorizacion."',
				'".$CodigoCanal."',
				'".$BLM_PedidoWeb."',
				'".$BLM_NombreAgenteWeb."',
				'".$BLM_UsuarioWeb."',
				'".$BLM_PedidoCapturado."',
				'".$BLM_EstadoPedido."',
				'".$BLM_AditivoExcelent."',
				".$BLM_PrecioAditivoExcelentUnit.",
				'".$BLM_FINCOM."',
				".$BLM_ImportePagadoB2C.",
				'".$BLM_CodigoOperacionB2C."',
				'".$CodiPromocional."',
				".$BLM_Descuento.",
				'".$DocumentoPDFAdjunto."',
				'".$SuPedido."',
				'".$BLM_NumeroContratoFincom."',
				'".$BLM_NumeroPlazosFincom."',
				CONVERT(uniqueidentifier, '".$IdDeposito."'),
				CONVERT(uniqueidentifier, '".$IdDescarga."'),
				'".$PedidoWhatsapp."',
				-1";
                $obj->data = $data;
                $result = $obj->post();
            } else {
                $data = "IdDelegacion = '".$IdDelegacion."',
				EjercicioPedido = '".$EjercicioPedido."',
				SeriePedido = '".$SeriePedido."',
				NumeroPedido = '".$NumeroPedido."',
				CodigoCliente = '".$CodigoCliente."',
				Fecha = '".$Fecha."',
				CodigoArticulo = '".$CodigoArticulo."',
				UnidadesPedidas = ".$UnidadesPedidas.",
				FechaSuministro = '".$FechaSuministro."',
				Precio = ".$Precio.",
				DescuentoClienteBases = ".$DescuentoClienteBases.",
				Riesgo = ".$Riesgo.",
				Estado = '".$Estado."',
				CodigoCamion = '".$CodigoCamion."',
				CodigoConductor = '".$CodigoConductor."',
				CodigoCamionBase = '".$CodigoCamionBase."',
				CodigoConductorBase = '".$CodigoConductorBase."',
				ClavePeticion = '".$ClavePeticion."',
				ClaveAutorizacion = '".$ClaveAutorizacion."',
				ObservacionesPedido = '".$ObservacionesPedido."',
				PrecioOfertado = ".$PrecioOfertado.",
				N_Autorizacion = '".$N_Autorizacion."',
				CodigoCanal = '".$CodigoCanal."',
				BLM_PedidoWeb = '".$BLM_PedidoWeb."',
				BLM_NombreAgenteWeb = '".$BLM_NombreAgenteWeb."',
				BLM_UsuarioWeb = '".$BLM_UsuarioWeb."',
				BLM_PedidoCapturado = '".$BLM_PedidoCapturado."',
				BLM_EstadoPedido = '".$BLM_EstadoPedido."',
				BLM_AditivoExcelent = '".$BLM_AditivoExcelent."',
				BLM_PrecioAditivoExcelentUnit = ".$BLM_PrecioAditivoExcelentUnit.",
				BLM_FINCOM = '".$BLM_FINCOM."',
				BLM_ImportePagadoB2C = ".$BLM_ImportePagadoB2C.",
				BLM_CodigoOperacionB2C = '".$BLM_CodigoOperacionB2C."',
				CodiPromocional = '".$CodiPromocional."',
				BLM_Descuento = ".$BLM_Descuento.",
				DocumentoPDFAdjunto = '".$DocumentoPDFAdjunto."',
				SuPedido = '".$SuPedido."',
				BLM_NumeroContratoFincom = '".$BLM_NumeroContratoFincom."',
				BLM_NumeroPlazosFincom = '".$BLM_NumeroPlazosFincom."',
				BLM_IdDeposito = CONVERT(uniqueidentifier, '".$IdDeposito."'),
				BLM_IdDescarga = CONVERT(uniqueidentifier, '".$IdDescarga."'),
				PedidoWhatsapp = '".$PedidoWhatsapp."',
				BLM_Heptan_sync = -1";
                
                $obj->data = $data;
                $result = $obj->put($IdPedido);
            }
            //echo $result;die;
            //echo $result;die;
            //if ($result == 1) {
            if ($result == 1) {
                //crear autorizacion
                if (!$N_Autorizacion==0) {
                    $tautorizacion = 0;
                    $autorizacionesQuery="Select count(*) as tautorizacion from AutorizacionesBases WHERE CodigoEmpresa = 3.
						AND IdDelegacion = '".$IdDelegacion."'
						AND N_Autorizacion = '".$N_Autorizacion."'";;
                    $autorizaciones=mssql_query($autorizacionesQuery);
                    while ($row = mssql_fetch_array($autorizaciones, MSSQL_ASSOC)){
                        $row2 = array_map('utf8_encode', $row);
                        $tautorizacion = $row2['tautorizacion'];
                    }
                    $obj->entity = "AutorizacionesBases";
                    if ($tautorizacion==0) {
                        $data = " 3,
						 '".$IdDelegacion."',
						 '".$N_Autorizacion."',
						 '".$Fecha."',
						 'Pedido: ".$EjercicioPedido.".".$SeriePedido.".".$NumeroPedido."',
						 '".$ClavePeticion."',
						 '".$ClaveAutorizacion."',
						 '".$ObservacionesAutomaticas."',
						 '".$ObservacionesBase."',
						 '".$ObservacionesCentral."',
						 '".$EstadoPeticion."',
						 '".$RiesgoDisponible."'";
                        $obj->data = $data;
                        $resultautorizacion = $obj->CrearAutorizacion();
                    }
                }
                $results[] = [
                    'IdPedido' => $IdPedido,
                    'ResultCode' => 0,
                    'ResultText' => 'Insert Success'
                ];
            } else {
                $results[] = [
                    'IdPedido' => $IdPedidoOriginal,
                    'ResultCode' => 1,
                    'ResultText' => 'Insert Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
                ];
            }
        }
    }
}

print_json($results);

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
    $object = new model_saleOrder_heptanJR_class;
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
    Traza(" SALEORDERHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>