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

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

//Capturamos las variables
$heptanStatus = $_GET['HeptanStatus'];
//$first = $_GET['First'];
$block = $_GET['Block'];
$cifDni = $_GET['CifDni'];

$select = "Clientes.CodigoCliente, Clientes.SiglaNacion, BLM_DatosNif.CifDni, Clientes.CifEuropeo, Clientes.FechaAlta, Clientes.CodigoContable, ClientesProveedores.ClienteProveedor AS RazonSocial, Clientes.Nombre, 
                         LEFT(ClientesProveedores.CodigoSigla + ' ' + ClientesProveedores.ViaPublica + ' ' + ClientesProveedores.Numero1 + ' ' + CASE WHEN clientesproveedores.Numero2 <> ' ' THEN '-' + clientesproveedores.Numero2
                          WHEN clientesproveedores.Numero2 = ' ' THEN ' ' END + ClientesProveedores.Escalera + ' ' + ClientesProveedores.Piso + ' ' + ClientesProveedores.Puerta + ' ' + ClientesProveedores.Letra, 40) 
                         AS DomicilioFiscal, ClientesProveedores.CodigoPostal AS CodigoPostalFiscal, ClientesProveedores.CodigoMunicipio AS CodigoMunicipioFiscal, ClientesProveedores.Municipio AS MunicipioFiscal, 
                         ClientesProveedores.Provincia AS ProvinciaFiscal, CASE WHEN Clientes.PeriodoFacturacion = 'CC' THEN 'CC' ELSE 'CR' END AS FormadePago, isnull(Mandatos.IBAN,'') AS IBAN, 
						 isnull(Mandatos.ReferenciaMandato,'') AS ReferenciaMandato, ISNULL(Bancos.BIC,'') AS BIC, 
                         Clientes.IndicadorIva, Clientes.ObservacionesCliente, Clientes.AgruparAlbaranes, Clientes.Telefono, Clientes.Telefono2, Clientes.Fax, Clientes.EMail1, Clientes.BajaEmpresaLc, Clientes.FechaBajaLc, 
                         Clientes.CodigoMotivoBajaClienteLc, CASE WHEN LcTiposCliente.BLM_CodigoTipoClienteHeptan = '' OR
                         LcTiposCliente.BLM_CodigoTipoClienteHeptan IS NULL THEN '10' ELSE LcTiposCliente.BLM_CodigoTipoClienteHeptan END AS CodigoTipoClienteLc, Clientes.PersonaClienteLc, Clientes.EnvioEFactura, 
                         Clientes.EmailEnvioEFactura, Clientes.PeriodoFacturacion, Clientes.FacturaBase, ClientesConta.NumeroPlazos, 
                         CASE WHEN ClientesConta.DiasPrimerPlazo > 90 THEN 90 ELSE ClientesConta.DiasPrimerPlazo END AS DiasPrimerPlazo, ClientesConta.DiasEntrePlazos, ClientesConta.DiasFijos1, ClientesConta.DiasFijos2, 
                         RiesgosPetrocat.BLM_EmailFidelCat, RiesgosPetrocat.BLM_MovilFidelcat, RiesgosPetrocat.BLM_FechaAltaFidelcat, RiesgosPetrocat.BLM_FechaBajaFidelcat, RiesgosPetrocat.BLM_CodigoTarjetaPuntos, 
                         RiesgosPetrocat.BLM_PuntosClub, RiesgosPetrocat.BLM_NoInteresado, DF.Domicilio AS DomicilioFactura, DF.CodigoPostal AS CodigoPostalFactura, DF.CodigoMunicipio AS CodigoMunicipioFactura, 
                         DF.Municipio AS MunicipioFactura, DF.Provincia AS ProvinciaFactura, Clientes.BLM_NumeroContratoFincom AS NContratoFINCOM";

$tablasQuery = "BLM_DatosNif WITH (nolock) INNER JOIN
                         Clientes WITH (nolock) ON 1 = Clientes.CodigoEmpresa AND BLM_DatosNif.BLM_CodigoClienteHeptan = Clientes.CodigoCliente AND BLM_DatosNif.CifDni = Clientes.CifDni LEFT OUTER JOIN
                         Mandatos WITH (nolock) ON Clientes.CodigoEmpresa = Mandatos.CodigoEmpresa AND Clientes.ReferenciaMandato = Mandatos.ReferenciaMandato LEFT OUTER JOIN
                         LcTiposCliente WITH (nolock) ON 1 = LcTiposCliente.CodigoEmpresa AND Clientes.CodigoTipoClienteLc = LcTiposCliente.CodigoTipoClienteLc LEFT OUTER JOIN
                         Domicilios AS DF WITH (nolock) ON Clientes.DomicilioFactura = DF.NumeroDomicilio AND Clientes.CodigoCliente = DF.CodigoCliente AND 'F' = DF.TipoDomicilio AND 
                         Clientes.CodigoEmpresa = DF.CodigoEmpresa LEFT OUTER JOIN
                         RiesgosPetrocat WITH (nolock) ON BLM_DatosNif.CifDni = RiesgosPetrocat.CifDni LEFT OUTER JOIN
                         Bancos WITH (nolock) ON SUBSTRING(Mandatos.IBAN, 5, 4) = Bancos.CodigoBanco LEFT OUTER JOIN
                         ClientesProveedores WITH (nolock) ON Clientes.SiglaNacion = ClientesProveedores.SiglaNacion AND Clientes.CifDni = ClientesProveedores.CifDni LEFT OUTER JOIN
                         ClientesConta WITH (nolock) ON Clientes.CodigoEmpresa = ClientesConta.CodigoEmpresa AND Clientes.CodigoCliente = ClientesConta.CodigoClienteProveedor LEFT OUTER JOIN
                         Municipios AS MF WITH (nolock) ON MF.CodigoMunicipio = ClientesProveedores.CodigoMunicipio LEFT OUTER JOIN
                         Municipios AS MFAC WITH (nolock) ON MFAC.CodigoMunicipio = DF.CodigoMunicipio";

$where = "(BLM_DatosNif.BLM_CodigoClienteHeptan <> '' )";
$top = "";

//Montamos el where de la query

//Si viene DIF devolvemos este usuario sin usar mas parametros.
if ($cifDni != '') {
    $where .= " AND (BLM_DatosNif.CifDni = '".$cifDni."')";
} else {
	if ($block != null && $block > 0) {
		$top = "TOP ".intVal($block)." ";
	}

	//Si no viene CIF comprobamos el resto de par�metros.
    	if ($heptanStatus == 'Pending') {
    		$where .= " AND (BLM_DatosNif.BLM_Heptan_sync = 0)";
	}

	
}

$query="SELECT ".$top.$select." FROM ".$tablasQuery." WHERE ".$where ." ORDER BY BLM_DatosNif.CifDni";
//echo $query;die;

$registro=mssql_query($query);
while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	$row2 = array_map('utf8_encode', $row); 
	$data[]=$row2;
}

// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
if(mssql_num_rows($registro)==0) {
	// Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
	if(isset($cifDni)) {
		print_json(200, "No data found." .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
	// Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
	} else {
		print_json(400, "Fail. " .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line'], null);
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

//Transforma a formato correcto la fecha y los numeros
function dateTransform($data) {
    foreach ($data as $key => $cliente) {
		//FechaAlta
		if($cliente['FechaAlta'] != '') {
			$strDate = strtotime($cliente['FechaAlta']);
			$badDate = date('d/m/Y H:i:s', $strDate );
				$data[$key]['FechaAlta'] = $badDate;
		}
		
		//FechaBajaLc
		if($cliente['FechaBajaLc'] != '') {
			$strDate = strtotime($cliente['FechaBajaLc']);
			$badDate = date('d/m/Y H:i:s', $strDate );
				$data[$key]['FechaBajaLc'] = $badDate;
		}

		//BLM_FechaAltaFidelcat
		if($cliente['BLM_FechaAltaFidelcat'] != '') {
			$strDate = strtotime($cliente['BLM_FechaAltaFidelcat']);
			$badDate = date('d/m/Y H:i:s', $strDate );
				$data[$key]['BLM_FechaAltaFidelcat'] = $badDate;
		}

		//BLM_FechaBajaFidelcat
		if($cliente['BLM_FechaBajaFidelcat'] != '') {
			$strDate = strtotime($cliente['BLM_FechaBajaFidelcat']);
			$badDate = date('d/m/Y H:i:s', $strDate );
				$data[$key]['BLM_FechaBajaFidelcat'] = $badDate;
		}

		//AgruparAlbaranes
		if($cliente['AgruparAlbaranes'] != '') {
			$data[$key]['AgruparAlbaranes'] = intVal($cliente['AgruparAlbaranes']);	
		}
		else
			$data[$key]['AgruparAlbaranes'] = 0;
		
		//BajaEmpresaLc
		if($cliente['BajaEmpresaLc'] != '') {
			$data[$key]['BajaEmpresaLc'] = intVal($cliente['BajaEmpresaLc']);	
		}
		else
			$data[$key]['BajaEmpresaLc'] = 0;
		
		//EnvioEFactura
		if($cliente['EnvioEFactura'] != '') {
			$data[$key]['EnvioEFactura'] = intVal($cliente['EnvioEFactura']);	
		}
		else
			$data[$key]['EnvioEFactura'] = 0;
		
		//NumeroPlazos
		if($cliente['NumeroPlazos'] != '') {
			$data[$key]['NumeroPlazos'] = intVal($cliente['NumeroPlazos']);	
		}
		else
			$data[$key]['NumeroPlazos'] = 0;
		
		//DiasPrimerPlazo
		if($cliente['DiasPrimerPlazo'] != '') {
			$data[$key]['DiasPrimerPlazo'] = intVal($cliente['DiasPrimerPlazo']);	
		}
		else
			$data[$key]['DiasPrimerPlazo'] = 0;
		
		//DiasEntrePlazos
		if($cliente['DiasEntrePlazos'] != '') {
			$data[$key]['DiasEntrePlazos'] = intVal($cliente['DiasEntrePlazos']);	
		}
		else
			$data[$key]['DiasEntrePlazos'] = 0;
		
		//BLM_PuntosClub
		if($cliente['BLM_PuntosClub'] != '') {
			$data[$key]['BLM_PuntosClub'] = intVal($cliente['BLM_PuntosClub']);	
		}
		else
			$data[$key]['BLM_PuntosClub'] = 0;
		
		//BLM_NoInteresado
		if($cliente['BLM_NoInteresado'] != '') {
			$data[$key]['BLM_NoInteresado'] = intVal($cliente['BLM_NoInteresado']);	
		}
		else
			$data[$key]['BLM_NoInteresado'] = 0;
		
		//DiasFijos1
		if($cliente['DiasFijos1'] != '') {
			$data[$key]['DiasFijos1'] = intVal($cliente['DiasFijos1']);	
		}
		else
			$data[$key]['DiasFijos1'] = 0;
		
		//DiasFijos2
		if($cliente['DiasFijos2'] != '') {
			$data[$key]['DiasFijos2'] = intVal($cliente['DiasFijos2']);	
		}
		else
			$data[$key]['DiasFijos2'] = 0;
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
	
	echo json_encode($response, JSON_UNESCAPED_SLASHES); //, JSON_PRETTY_PRINT);
	// echo str_replace("\/", "/", $resultado);
}
?>