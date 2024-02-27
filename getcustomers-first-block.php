<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_clientes.php';

$bodyRequest = file_get_contents("php://input");

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
$first = $_GET['First'];
$block = $_GET['Block'];
$cifDni = $_GET['CifDni'];

$select = "WITH OrderedClientes AS (
SELECT ROW_NUMBER() OVER (ORDER BY BLM_DatosNif.CifDni) AS RowNumber, Clientes.CodigoCliente, BLM_DatosNif.CAE, BLM_DatosNif.FechaCaducidadCAE, Clientes.SiglaNacion, BLM_DatosNif.CifDni, Clientes.CifEuropeo, Clientes.FechaAlta, Clientes.CodigoContable, Clientes.RazonSocial, Clientes.Nombre, 
                         LEFT(ClientesProveedores.CodigoSigla + ' ' + ClientesProveedores.ViaPublica + ' ' + ClientesProveedores.Numero1 + ' ' + CASE WHEN clientesproveedores.Numero2 <> ' ' THEN '-' + clientesproveedores.Numero2
                          WHEN clientesproveedores.Numero2 = ' ' THEN ' ' END + ClientesProveedores.Escalera + ' ' + ClientesProveedores.Piso + ' ' + ClientesProveedores.Puerta + ' ' + ClientesProveedores.Letra, 40) 
                         AS DomicilioFiscal, ClientesProveedores.CodigoPostal AS CodigoPostalFiscal, ClientesProveedores.CodigoMunicipio AS CodigoMunicipioFiscal, MF.Municipio AS MunicipioFiscal, 
                         ClientesProveedores.Provincia AS ProvinciaFiscal, MF.CodigoAutonomia AS CodigoAutonomiaFiscal, ClientesProveedores.CodigoNacion AS CodigoNacionFiscal, Clientes.FormadePago, 
                         Clientes.ReferenciaMandato, Clientes.IBAN, Bancos.BIC, Clientes.IndicadorIva, Clientes.ObservacionesCliente, Clientes.AgruparAlbaranes, Clientes.Telefono, Clientes.Telefono2, Clientes.Fax, Clientes.EMail1, 
                         Clientes.BajaEmpresaLc, Clientes.FechaBajaLc, Clientes.CodigoMotivoBajaClienteLc, BLM_DatosNif.CodigoTipoClienteLc, Clientes.PersonaClienteLc, Clientes.ClienteFinal, Clientes.CIM, 
                         Clientes.FacturacionElectronica, Clientes.FechaAlta_, Clientes.EnvioEFactura, Clientes.EmailEnvioEFactura, Clientes.Garantia, Clientes.VencimientoGarantia, 
                         Clientes.PeriodoFacturacion, Clientes.FacturaBase, ClientesConta.NumeroPlazos, ClientesConta.DiasPrimerPlazo, ClientesConta.DiasEntrePlazos, ClientesConta.DiasFijos1, ClientesConta.DiasFijos2, 
                         RiesgosPetrocat.BLM_EmailFidelCat, RiesgosPetrocat.BLM_MovilFidelcat, RiesgosPetrocat.BLM_FechaAltaFidelcat, RiesgosPetrocat.BLM_FechaBajaFidelcat, RiesgosPetrocat.BLM_CodigoTarjetaPuntos, 
                         RiesgosPetrocat.BLM_PuntosClub, RiesgosPetrocat.BLM_NoInteresado, DF.Domicilio AS DomicilioFactura, DF.CodigoPostal AS CodigoPostalFactura, DF.CodigoMunicipio AS CodigoMunicipioFactura, 
                         DF.Municipio AS MunicipioFactura, DF.Provincia AS ProvinciaFactura, MFAC.CodigoAutonomia, MFAC.CodigoNacion AS CodigoPaisFactura";

$tablasQuery = "BLM_DatosNif INNER JOIN
                         Clientes ON 1 = Clientes.CodigoEmpresa AND BLM_DatosNif.BLM_CodigoClienteHeptan = Clientes.CodigoCliente AND BLM_DatosNif.CifDni = Clientes.CifDni LEFT OUTER JOIN
                         Domicilios AS DF ON Clientes.DomicilioFactura = DF.NumeroDomicilio AND Clientes.CodigoCliente = DF.CodigoCliente AND 'F' = DF.TipoDomicilio AND 
                         Clientes.CodigoEmpresa = DF.CodigoEmpresa LEFT OUTER JOIN
                         RiesgosPetrocat ON BLM_DatosNif.CifDni = RiesgosPetrocat.CifDni LEFT OUTER JOIN
                         Bancos ON SUBSTRING(Clientes.IBAN, 5, 4) = Bancos.CodigoBanco LEFT OUTER JOIN
                         ClientesProveedores ON Clientes.SiglaNacion = ClientesProveedores.SiglaNacion AND Clientes.CifDni = ClientesProveedores.CifDni LEFT OUTER JOIN
                         ClientesConta ON Clientes.CodigoEmpresa = ClientesConta.CodigoEmpresa AND Clientes.CodigoCliente = ClientesConta.CodigoClienteProveedor LEFT OUTER JOIN
                         Municipios AS MF ON MF.CodigoMunicipio = ClientesProveedores.CodigoMunicipio LEFT OUTER JOIN
                         Municipios AS MFAC ON MFAC.CodigoMunicipio = DF.CodigoMunicipio";

$where = " (BLM_DatosNif.BLM_CodigoClienteHeptan <> '')";
$top = "";
$offset = "";
$query2 = " SELECT * FROM OrderedClientes";

//Montamos el where de la query

//Si viene DIF devolvemos este usuario sin usar mas parametros.
if ($cifDni != '') {
    $where .= " AND (BLM_DatosNif.CifDni = \"".$cifDni."\")";
} else {
	//Si no viene CIF comprobamos el resto de parámetros.
    	if ($heptanStatus == 'Pending') {
    		$where .= " AND (BLM_DatosNif.BLM_Heptan_sync = 0)";
	}

	if ($first != '' && $block != '') {
    	$from = intval($first);
    	$to = intval($block) + intval($first);
	$query2 .= " WHERE RowNumber between ".$from." and ".$to;
	}
}

$where .= ")";

$query=$select." FROM ".$tablasQuery." WHERE ".$where.$query2;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	$row2 = array_map('utf8_encode', $row); 
	$data[]=$row2;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
	// Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($cifDni)) {
		print_json(404, "Fail", null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, "Fail", null);
	}
// Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
} else {
	//Pasamos la fecha a formato correcto
	$data = dateTransform($data);

	// Imprime la informacion solicitada
	print_json(200, "Success", $data);
}

//Cerramos la conexion.
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

//Transforma a formato correcto la fecha
function dateTransform($data) {
    foreach ($data as $key => $cliente) {
	$strDate = strtotime($cliente['FechaAlta']);
	$badDate = date('d/m/Y H:i', $strDate );
        $data[$key]['FechaAlta'] = $badDate;
    }
	return $data;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje, $data) {
	//print_r( $data);
	header("HTTP/1.1 $status $mensaje");
	header("Content-Type: application/json; charset=UTF-8");
	
	$response['clientes'] = $data;
	$response['Code'] = $status;
	$response['Description'] = $mensaje;
	
	echo json_encode($response, JSON_UNESCAPED_UNICODE); //, JSON_PRETTY_PRINT);
}
?>