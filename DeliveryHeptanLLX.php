<?php
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: POST");
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Se incluye el archivo que contiene la clase generica
include 'model_deliveriesLLX.php';
include 'core/lib.php';

$bodyRequest = file_get_contents("php://input");

//error_reporting(E_ALL ^ E_WARNING);
error_reporting(0);

// Variable que guarda la instancia de la clase generica
$obj = get_obj();

$obj->entityCabecera = "CabeceraAlbaranCliente";
$obj->entityLineas = "LineasAlbaranCliente";

//Capturamos las variables
$params = json_decode($bodyRequest);
Traza(" DELIVERYHEPTAN", $bodyRequest);

$response = [];

foreach ($params as $delivery) {
    //Parametros de la delivery
	
	//CabeceraAlbaranCliente
    $IdAlbaran = $delivery->IdAlbaran;
	$IdDelegacion = $delivery->IdDelegacion;
	$EjercicioAlbaran = $delivery->EjercicioAlbaran;
	$SerieAlbaran = $delivery->SerieAlbaran;
	$NumeroAlbaran = $delivery->NumeroAlbaran;
	$FechaAlbaran = $delivery->FechaAlbaran;
	$SiglaNacion = $delivery->SiglaNacion;
	$CifDni = $delivery->CifDni;
	$CifEuropeo = $delivery->CifEuropeo;
	$RazonSocial = utf8_decode(str_replace("'","''",$delivery->RazonSocial));
	$RazonSocialEnvios = utf8_decode(str_replace("'","''",$delivery->RazonSocialEnvios));
	$Nombre = utf8_decode(str_replace("'","''",$delivery->Nombre));
	$NombreEnvios = utf8_decode(str_replace("'","''",$delivery->NombreEnvios));
	$Domicilio = utf8_decode(str_replace("'","''",$delivery->Domicilio));
	$DomicilioEnvios = utf8_decode(str_replace("'","''",$delivery->DomicilioEnvios));
	$CodigoPostal = $delivery->CodigoPostal;
	$CodigoPostalEnvios = $delivery->CodigoPostalEnvios;
	$CodigoMunicipio = $delivery->CodigoMunicipio;
	$CodigoMunicipioEnvios = $delivery->CodigoMunicipioEnvios;
	$Municipio = utf8_decode(str_replace("'","''",$delivery->Municipio));
	$MunicipioEnvios = utf8_decode(str_replace("'","''",$delivery->MunicipioEnvios));
	$CodigoProvincia = $delivery->CodigoProvincia;
	$CodigoProvinciaEnvios = $delivery->CodigoProvinciaEnvios;
	$Provincia = utf8_decode(str_replace("'","''",$delivery->Provincia));
	$ProvinciaEnvios = utf8_decode(str_replace("'","''",$delivery->ProvinciaEnvios));
	$TelefonoEnvios = utf8_decode($delivery->TelefonoEnvios);
	$FaxEnvios = utf8_decode($delivery->FaxEnvios);
	$FormadePago = $delivery->FormadePago;
	$NumeroPlazos = $delivery->NumeroPlazos;
	$DiasPrimerPlazo = $delivery->DiasPrimerPlazo;
	$DiasEntrePlazos = $delivery->DiasEntrePlazos;
	$DiasFijos1 = $delivery->DiasFijos1;
	$DiasFijos2 = $delivery->DiasFijos2;
	$DiasFijos3 = 0;
	$IndicadorIva = $delivery->IndicadorIva;
	$IvaIncluido = $delivery->IvaIncluido;
	$GrupoIva = $delivery->GrupoIva;
	$CodigoComisionista = $delivery->CodigoComisionista;
	$CodigoCanal = $delivery->CodigoCanal;
	$Bloqueo = $delivery->Bloqueo;
	$ObservacionesCliente = utf8_decode(str_replace("'","''",$delivery->ObservacionesCliente));
	$ObservacionesFactura = utf8_decode(str_replace("'","''",$delivery->ObservacionesFactura));
	$ImporteBruto = $delivery->ImporteBruto;
	$ImporteNetoLineas = $delivery->ImporteNetoLineas;
	$BaseImponible = $delivery->BaseImponible;
	$TotalIva = $delivery->TotalIva;
	$CodigoTipoClienteLc = $delivery->CodigoTipoClienteLc;
	$CodigoMotivoAbonoLc = $delivery->CodigoMotivoAbonoLc;
	$TipoNuevaFra = $delivery->TipoNuevaFra;
	$ClienteFinal = $delivery->ClienteFinal;
	$IdPedido = $delivery->IdPedido;
	$FechaFactura = $delivery->FechaFactura;
	$EjercicioFactura = $delivery->EjercicioFactura;
	$SerieFactura = $delivery->SerieFactura;
	$NumeroFactura = $delivery->NumeroFactura;
	$CodigoIdioma_ = "";
	$ImporteLiquido = $delivery->ImporteLiquido;
	$EjercicioFacturaOriginal = $delivery->EjercicioFacturaOriginal;
	$SerieFacturaOriginal = $delivery->SerieFacturaOriginal;
	$NumeroFacturaOriginal = $delivery->NumeroFacturaOriginal;
	$IdAlbaranOriginal = $delivery->IdAlbaranOriginal;
	$ReferenciaMandato = $delivery->ReferenciaMandato;
	$SuPedido = utf8_decode($delivery->SuPedido);
	$BLM_Metalico = $delivery->BLM_Metalico;
	$TipoDomicilioFactura = $delivery->DomicilioFactura;
	$AgruparAlbaranes = $delivery->AgruparAlbaranes;
	$Nacion=utf8_decode("ESPAÑA");
	$AgrupacionFactura = $delivery->AgrupacionFactura;
	
	//IdAlbaran,IdPedido y IdFacturaOriginal no se graban en CabeceraAlbaranCliente porque no existen.
	
	//Invertir el valor de campo bloqueo
	if ($Bloqueo==1) {
	    $Bloqueo=-1;
	}
	//Cast de las fechas para el insert
	if ($FechaFactura=="") {
	    $FechaFactura=$FechaAlbaran;
	}
	$FechaAlbaran = castDateToInsert($FechaAlbaran);
	$FechaFactura = castDateToInsert($FechaFactura);
	
	
	//------------------------------------------------------------------
	//LineasAlbaranCliente
	$CodigoArticulo = $delivery->CodigoArticulo;
	$CodigoAlmacen = $delivery->CodigoAlmacen;
	$DescripcionArticulo = $delivery->DescripcionArticulo;
	$CodigoIva = $delivery->CodigoIva;
	$Unidades = $delivery->Unidades;
	$Precio = $delivery->Precio;
	$PrecioRebaje = $delivery->PrecioRebaje;
	$Iva = $delivery->{'%Iva'};
	$FechaSuministro = $delivery->FechaSuministro;
	$IdDescarga = $delivery->IdDescarga;
	$IdDeposito = $delivery->IdDeposito;
	$UnidadesPedido = $delivery->UnidadesPedido;
	$PeriodoFacturacion = $delivery->PeriodoFacturacion;
	$PrecioOfertado = $delivery->PrecioOfertado;
	$BLM_PedidoWeb = $delivery->BLM_PedidoWeb;
	$BLM_AditivoExcelent = $delivery->BLM_AditivoExcelent;
	$BLM_PrecioAditivoExcelentUnit = $delivery->BLM_PrecioAditivoExcelentUnit;
	$BLM_AditivoExcelentChofer = $delivery->BLM_AditivoExcelentChofer;
	$BLM_LitrosPedido = $delivery->BLM_LitrosPedido;
	$BLM_FINCOM = $delivery->BLM_FINCOM;
	$BLM_ImporteCobroMetalico = $delivery->BLM_ImporteCobroMetalico;
	$IdAlbaranCarburante = $delivery->IdAlbaranCarburante;
	$BLM_ImportePagadoB2C = $delivery->BLM_ImportePagadoB2C;
	$BLM_CodigoOperacionB2C = $delivery->BLM_CodigoOperacionB2C;
	$CodiPromocional = $delivery->CodiPromocional;
	$CodigoTipoEfecto = $delivery->CodigoTipoEfecto;
	if ($BLM_AditivoExcelentChofer>0) {
		$BLM_AditivoExcelentChofer=0;
	}
	$numerolineas = 1;
	if ($IdAlbaran=='') {
	    $tiporesult="Insert";
	}else{
	    $tiporesult="Update";	    
	}
	//IdDescarga, IdDeposito y IdAlbaranCarburante no se graban en LineasAlbaranCliente porque no existen.
	$data="";
	$CodigoCliente="";
    $query="SELECT BLM_CodigoClienteHeptan FROM BLM_DatosNif WHERE CifDni='".$CifDni."' AND BLM_CodigoClienteHeptan<>''";
    //	echo $query;die;
    $registro = mssql_query($query);
    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
        $row2 = array_map('utf8_encode', $row);
        $data[]=$row2;
    }
    // Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
    if(mssql_num_rows($registro) > 0) {
        $CodigoCliente = $data[0]["BLM_CodigoClienteHeptan"];
    }
    
    //DATOS CLIENTES	    
    $data="";
    $query="SELECT AgruparAlbaranes, CodigoContable FROM Clientes WHERE CodigoEmpresa=1 AND CodigoCliente='".$CodigoCliente."' AND CodigoCliente<>''";
    //	 echo $query;die;
    $registro = mssql_query($query);
    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
        $row2 = array_map('utf8_encode', $row);
        $data[]=$row2;
    }
    // Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
   	if(mssql_num_rows($registro) > 0) {
   	    $CodigoContable = $data[0]["CodigoContable"];
   	}
/*    $AgruparAlbaranes=0;
    if(mssql_num_rows($registro) > 0) {
        $AgruparAlbaranes = $data[0]["AgruparAlbaranes"];
        if ($AgruparAlbaranes!==0) {
            $AgruparAlbaranes=-1;
        }
    }
  */  
    //MANDATOS
    $IBAN='';
	if ($ReferenciaMandato<>'') {
    	$data="";
    	$query="SELECT IBAN FROM Mandatos WHERE CodigoEmpresa=1 AND ReferenciaMandato='".$ReferenciaMandato."'";
    	//echo $query;die;
    	$registro = mssql_query($query);
    	while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
    	    $row2 = array_map('utf8_encode', $row);
    	    $data[]=$row2;
    	}
    	// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
    	if(mssql_num_rows($registro) > 0) {
    	    $IBAN = $data[0]["IBAN"];
    	}
    	
	}
	$data="";
	$query="SELECT CodigoFamilia,CodigoSubFamilia,CodigoArancelario,CodigoDefinicion_ FROM Articulos WHERE CodigoEmpresa=3 AND CodigoArticulo='".$CodigoArticulo."'";
	//	 echo $query;die;
	$registro = mssql_query($query);
	while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	    $row2 = array_map('utf8_encode', $row);
	    $data[]=$row2;
	}
	// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
	$CodigoFamilia = '';
	$CodigoSubFamilia = '';
	$CodigoArancelario = '';
	$CodigoDefinicion = '';
	if(mssql_num_rows($registro) > 0) {
	    $CodigoFamilia = $data[0]["CodigoFamilia"];
	    $CodigoSubFamilia = $data[0]["CodigoSubFamilia"];
	    $CodigoArancelario = $data[0]["CodigoArancelario"];
	    $CodigoDefinicion = $data[0]["CodigoDefinicion_"];
	}
	
	//DOMILICIO FACTURA
	$data="";
	$DomicilioFactura = '';
	$CodigoPostalFactura = '';
	$CodigoMunicipioFactura= '';
	$MunicipioFactura= '';
	$ProvinciaFactura = '';
	$CodigoAutonomiaFactura = 0;
	$CodigoPaisFactura = '';
	if ($TipoDomicilioFactura==0) {
    	$query="SELECT Domicilio, CodigoPostal, CodigoMunicipio, Municipio, 
            CodigoProvincia, Provincia, CodigoNacion FROM Domicilios WHERE CodigoEmpresa=1 AND CodigoCliente='".$CodigoCliente."' AND NumeroDomicilio=0 AND TipoDomicilio='F'";
    		 //echo $query;die;
    	$registro = mssql_query($query);
    	while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
    	    $row2 = array_map('utf8_encode', $row);
    	    $data[]=$row2;
    	}
    	// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
    	if(mssql_num_rows($registro) > 0) {
    	    $DomicilioFactura = utf8_decode(str_replace("'","''",$data[0]["Domicilio"]));
    	    $CodigoPostalFactura = $data[0]["CodigoPostal"];
    	    $CodigoMunicipioFactura= $data[0]["CodigoMunicipio"];
    	    $MunicipioFactura= utf8_decode(str_replace("'","''",$data[0]["Municipio"]));
    	    $ProvinciaFactura = utf8_decode(str_replace("'","''",$data[0]["Provincia"]));
    	    $CodigoAutonomiaFactura = 0;
    	    $CodigoPaisFactura = $data[0]["CodigoNacion"];
    	}
	}else{
	    $query="SELECT BLM_DomicilioFactura, CodigoPostalFactura, CodigoMunicipioFactura, MunicipioFactura, ProvinciaFactura, 
                         CodigoAutonomiaFactura, CodigoPaisFactura FROM Heptan_Descargas WHERE BLM_IdDescarga='".$IdDescarga."'";
	    //	 echo $query;die;
	    $registro = mssql_query($query);
	    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	        $row2 = array_map('utf8_encode', $row);
	        $data[]=$row2;
	    }
	    // Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
	    if(mssql_num_rows($registro) > 0) {
	        $DomicilioFactura = utf8_decode(str_replace("'","''",$data[0]["BLM_DomicilioFactura"]));
	        $CodigoPostalFactura = $data[0]["CodigoPostalFactura"];
	        $CodigoMunicipioFactura= $data[0]["CodigoMunicipioFactura"];
	        $MunicipioFactura= utf8_decode(str_replace("'","''",$data[0]["MunicipioFactura"]));
	        $ProvinciaFactura = utf8_decode(str_replace("'","''",$data[0]["ProvinciaFactura"]));
	        $CodigoAutonomiaFactura = $data[0]["CodigoAutonomiaFactura"];
	        $CodigoPaisFactura = $data[0]["CodigoPaisFactura"];
	    }
	    
	}
	$data="";
	$EjercicioAlbaranCarburante = 0;
	$SerieAlbaranCarburante = '';
	$NumeroAlbaranCarburante = 0;
	if ($IdAlbaranCarburante<>"") {
	    $query="SELECT EjercicioAlbaran, SerieAlbaran, NumeroAlbaran FROM LineasAlbaranCliente WHERE CodigoEmpresa=3 and LineasPosicion='".$IdAlbaranCarburante."'";
	    //	 echo $query;die;
	    $registro = mssql_query($query);
	    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	        $row2 = array_map('utf8_encode', $row);
	        $data[]=$row2;
	    }
	    // Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
	    if(mssql_num_rows($registro) > 0) {
	        $EjercicioAlbaranCarburante = $data[0]["EjercicioAlbaran"];
	        $SerieAlbaranCarburante = $data[0]["SerieAlbaran"];
	        $NumeroAlbaranCarburante = $data[0]["NumeroAlbaran"];
	    }
	}
	$data="";
	$EjercicioPedido = 0;
	$SeriePedido = '';
	$NumeroPedido = 0;
	if ($IdPedido<>"") {
	    $query="SELECT EjercicioPedido, SeriePedido, NumeroPedido FROM PedidoClienteBases WHERE CodigoEmpresa=3 and BLM_IdPedido='".$IdPedido."'";
	    //echo $query;die;
	    $registro = mssql_query($query);
	    while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
	        $row2 = array_map('utf8_encode', $row);
	        $data[]=$row2;
	    }
	    // Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
	    if(mssql_num_rows($registro) > 0) {
	        $EjercicioPedido = $data[0]["EjercicioPedido"];
	        $SeriePedido = $data[0]["SeriePedido"];
	        $NumeroPedido = $data[0]["NumeroPedido"];
	    }
	}
	//Cast de las fechas para el insert
	$FechaSuministro = castDateToInsert($FechaSuministro);
	
	//Campos Comunes
	$CodigoEmpresa = 3;
	$Orden = 5;
	//$EjercicioAlbaran= 2018;
	//$SerieAlbaran = "HEPTAN";
	if ($IdDeposito=='') {
	    $IdDeposito='00000000-0000-0000-0000-000000000000';
	}
	if ($IdDescarga=='') {
	    $IdDescarga='00000000-0000-0000-0000-000000000000';
	}
	//AGRUPACION FACTURA
	if ($AgrupacionFactura=="C") {
	    $AgrupacionHeptan=$CodigoCliente;
	} else {
	    $AgrupacionHeptan=$IdDescarga;	    
	}
	//Comprobar si viene el GUID
	if ($IdAlbaran == "") {
	    //Numero del siguiente albaran
		//$SiguienteNumeroAlbaran = getNextAlbaran($obj);
	    //COMPROBAR SI EXISTE EL ALBARAN Y BORRARLO SI ESTA SIN FACTURAR
	    /*$query="SELECT EjercicioAlbaran, SerieAlbaran, NumeroAlbaran,StatusFacturado FROM CabeceraAlbaranCliente WHERE CodigoEmpresa=3 and EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran;
	    //	 echo $query;die;
	    $registro = mssql_query($query);
	    if(mssql_num_rows($registro) > 0) {
	        if ($data[0]["StatusFacturado"]==0) {
	            //BORRAR CABECERA
	            $query="DELETE FROM CabeceraAlbaranCliente WHERE CodigoEmpresa=3 and EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran;
	            $registro = mssql_query($query);
	            //BORRAR LINEAS
	            $query="DELETE FROM LineasAlbaranCliente WHERE CodigoEmpresa=3 and EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran;
	            $registro = mssql_query($query);
	        }
	    }else{
	        //BORRAR LINEAS
	        $query="DELETE FROM LineasAlbaranCliente WHERE CodigoEmpresa=3 and EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran;
	        $registro = mssql_query($query);
	    }*/
	    $IdAlbaran = createGUID();
	    $dataCabecera = $CodigoEmpresa.",".$EjercicioAlbaran.",'".$SerieAlbaran."',".$NumeroAlbaran.",".$numerolineas.",'".$CodigoCliente."','".$CodigoCliente."','".$IdDelegacion."',".$FechaAlbaran.",'".$SiglaNacion."','".$CifDni."','".$CifEuropeo."','".$RazonSocial."','".$RazonSocialEnvios."','".$Nombre."','".$NombreEnvios."','".$Domicilio."','".$DomicilioEnvios."','".$DomicilioEnvios."','".$CodigoPostal."','".$CodigoPostalEnvios."','".$CodigoMunicipio."','".$CodigoMunicipioEnvios."','".$Municipio."','".$MunicipioEnvios."','".$CodigoProvincia."','".$CodigoProvinciaEnvios."','".$Provincia."','".$ProvinciaEnvios."','".$Nacion."','".$Nacion."','".$TelefonoEnvios."','".$FaxEnvios."','".$FormadePago."','".$NumeroPlazos."','".$DiasPrimerPlazo."','".$DiasEntrePlazos."','".$DiasFijos1."','".$DiasFijos2."','".$DiasFijos3."','".$IndicadorIva."','".$IvaIncluido."','".$GrupoIva."','".$CodigoComisionista."','".$CodigoCanal."','".$Bloqueo."','".$ObservacionesCliente."','".$ObservacionesFactura."','".$ImporteBruto."','".$ImporteNetoLineas."','".$BaseImponible."','".$BaseImponible."','".$TotalIva."','".$TotalIva."','".$CodigoTipoClienteLc."','".$CodigoMotivoAbonoLc."','".$TipoNuevaFra."','".$ClienteFinal."',".$FechaFactura.",'".$EjercicioFactura."','".$SerieFactura."','".$NumeroFactura."','".$CodigoIdioma_."','".$ImporteLiquido."','".$ReferenciaMandato."','".$SuPedido."','".$BLM_Metalico."','".$CodigoTipoEfecto."','".$IBAN."','".$DomicilioFactura."','".$CodigoPostalFactura."','".$CodigoMunicipioFactura."','".$MunicipioFactura."','".$ProvinciaFactura."','".$CodigoAutonomiaFactura."','".$CodigoPaisFactura."','".$EjercicioFacturaOriginal."','".$SerieFacturaOriginal."','".$NumeroFacturaOriginal."','".$AgruparAlbaranes."','".$PeriodoFacturacion."','".$AgrupacionHeptan."'";

		$dataLineas = $CodigoEmpresa.",".$EjercicioAlbaran.",'".$SerieAlbaran."',".$NumeroAlbaran.",".$Orden.",CONVERT(uniqueidentifier, '".$IdAlbaran."'),'".$CodigoCliente."','".$CodigoArticulo."','".$CodigoAlmacen."','".$DescripcionArticulo."','".$CodigoIva."','".$Unidades."','".$Unidades."','".$Precio."','".$PrecioRebaje."','".$Iva."','".$ImporteBruto."','".$ImporteBruto."','".$BaseImponible."','".$BaseImponible."','".$BaseImponible."','".$TotalIva."','".$TotalIva."','".$ImporteLiquido."','".$IdDelegacion."',".$FechaSuministro.",'".$UnidadesPedido."','".$PeriodoFacturacion."','".$PrecioOfertado."','".$BLM_PedidoWeb."','".$BLM_AditivoExcelent."','".$BLM_PrecioAditivoExcelentUnit."','".$BLM_AditivoExcelentChofer."','".$BLM_LitrosPedido."','".$BLM_FINCOM."','".$BLM_ImporteCobroMetalico."','".$BLM_ImportePagadoB2C."','".$BLM_CodigoOperacionB2C."','".$CodiPromocional."','".$CodigoFamilia."','".$CodigoSubFamilia."','".$CodigoArancelario."','".$CodigoDefinicion."','".$EjercicioFactura."','".$SerieFactura."','".$NumeroFactura."',CONVERT(uniqueidentifier, '".$IdDeposito."'),CONVERT(uniqueidentifier, '".$IdDescarga."'), '".$EjercicioAlbaranCarburante."', '".$SerieAlbaranCarburante."', '".$NumeroAlbaranCarburante."', '".$EjercicioPedido."', '".$SeriePedido."', '".$NumeroPedido."',".$FechaAlbaran.",'".$IvaIncluido."'";
		
		$obj->dataCabecera = $dataCabecera;
		$obj->dataLineas = $dataLineas;
		
		$resultCabecera = $obj->postCabecera();
		if ($resultCabecera==1) {

			$query="SELECT EjercicioAlbaran, SerieAlbaran, NumeroAlbaran, CONVERT(char(36), LineasPosicion) as IdAlbaran FROM LineasAlbaranCliente WHERE CodigoEmpresa=3 and EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran." AND Orden=".$Orden;
			//	 echo $query;die;
			$registro = mssql_query($query);
			while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
				$row2 = array_map('utf8_encode', $row);
				$data[]=$row2;
			}
			// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
			if(mssql_num_rows($registro) > 0) {
				$IdAlbaran = $data[0]["IdAlbaran"];
			}else{		
				$resultLinea = $obj->postLineas();
				if ($resultLinea==0) {
					$IdAlbaran="";
					$resultCabecera=0;
					//BORRAR CABECERA
					$query="DELETE FROM CabeceraAlbaranCliente WHERE CodigoEmpresa=3 and EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran;
					$registro = mssql_query($query);
				}
			}
		} else {
			$resultLinea=0;
			$IdAlbaran="";
		}
	} else {
	    $NumeroAlbaranResult = $obj->getLinea($IdAlbaran);
		// echo json_encode($NumeroAlbaranResult);die;
		if (count($NumeroAlbaranResult) > 0 && $NumeroAlbaranResult[0] && array_key_exists('NumeroAlbaran', $NumeroAlbaranResult[0])) {
			$NumeroAlbaran = $NumeroAlbaranResult[0]['NumeroAlbaran'];
			//CONTROL ALBARAN FACTURADO
			    //DATOS CLIENTES	    
			$data="";
			$query="SELECT StatusFacturado FROM CabeceraAlbaranCliente WHERE CodigoEmpresa=3 AND EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran;
				// echo $query;die;
			$registro = mssql_query($query);
			while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
				$row2 = array_map('utf8_encode', $row);
				$data[]=$row2;
			}
			// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
			if(mssql_num_rows($registro) > 0) {
				$Albaranfacturado = $data[0]["StatusFacturado"];
				//echo $Albaranfacturado;die;
				if($Albaranfacturado==-1) {
					 $results[] = [
					'IdAlbaran' => $IdAlbaran,
					'ResultCode' => 1,
					'ResultText' => ''.$tiporesult.' Fail. Albaran facturado'];
					continue;
					//print_json($results);
					//$obj = null;
					//die;
				}
			}
			$query="SELECT LineasPosicion FROM LineasAlbaranCliente WHERE CodigoEmpresa=3 AND LineasPosicion='".$IdAlbaran."' 
				AND EjercicioAlbaran=".$EjercicioAlbaran." AND SerieAlbaran='".$SerieAlbaran."' AND NumeroAlbaran=".$NumeroAlbaran;
			$registro = mssql_query($query);
			while ($row = mssql_fetch_array($registro, MSSQL_ASSOC)){
				$row2 = array_map('utf8_encode', $row);
				$data[]=$row2;
			}
			// Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
			if(mssql_num_rows($registro) == 0) {
				 $results[] = [
				'IdAlbaran' => $IdAlbaran,
				'ResultCode' => 1,
				'ResultText' => ''.$tiporesult.' Fail. Albaran sin linea'];
				continue;
			}

			$dataLineas = "
            CodigoEmpresa = '".$CodigoEmpresa."',
            EjercicioAlbaran = '".$EjercicioAlbaran."',
            SerieAlbaran = '".$SerieAlbaran."',
            Numeroalbaran = '".$NumeroAlbaran."',
            Orden = '".$Orden."',
            CodigoCliente = '".$CodigoCliente."',
            CodigoArticulo = '".$CodigoArticulo."',
			CodigoAlmacen = '".$CodigoAlmacen."',
			DescripcionArticulo = '".$DescripcionArticulo."',
			CodigoIva = '".$CodigoIva."',
			Unidades = '".$Unidades."',
			Unidades2_ = '".$Unidades."',
			Precio = '".$Precio."',
			PrecioRebaje = '".$PrecioRebaje."',
			[%Iva] = '".$Iva."',
			ImporteBruto = '".$ImporteBruto."',
			ImporteNeto = '".$ImporteBruto."',
			BaseComision = '".$BaseImponible."',
			BaseImponible = '".$BaseImponible."',
			BaseIva = '".$BaseImponible."',
			CuotaIva = '".$TotalIva."',
			TotalIva = '".$TotalIva."',
			ImporteLiquido = '".$ImporteLiquido."',
			CodigoCanal = '".$CodigoCanal."',
			FechaSuministro = ".$FechaSuministro.",
			UnidadesPedido = '".$UnidadesPedido."',
			PeriodoFacturacion = '".$PeriodoFacturacion."',
			PrecioOfertado = '".$PrecioOfertado."',
			BLM_PedidoWeb = '".$BLM_PedidoWeb."',
			BLM_AditivoExcelent = '".$BLM_AditivoExcelent."',
			BLM_PrecioAditivoExcelentUnit = '".$BLM_PrecioAditivoExcelentUnit."',
			BLM_AditivoExcelentChofer = '".$BLM_AditivoExcelentChofer."',
			BLM_LitrosPedido = '".$BLM_LitrosPedido."',
			BLM_FINCOM = '".$BLM_FINCOM."',
			BLM_ImporteCobroMetalico = '".$BLM_ImporteCobroMetalico."',
			BLM_ImportePagadoB2C = '".$BLM_ImportePagadoB2C."',
			BLM_CodigoOperacionB2C = '".$BLM_CodigoOperacionB2C."',
			CodiPromocional = '".$CodiPromocional."',
			CodigoFamilia = '".$CodigoFamilia."',
			CodigoSubFamilia = '".$CodigoSubFamilia."',
			CodigoArancelario = '".$CodigoArancelario."',
			CodigoDefinicion_ = '".$CodigoDefinicion."',
			EjercicioFactura = '".$EjercicioFactura."',
			SerieFactura = '".$SerieFactura."',
			NumeroFactura = '".$NumeroFactura."',
			BLM_Heptan_IdDeposito = CONVERT(uniqueidentifier, '".$IdDeposito."'),
			BLM_Heptan_IdDescarga = CONVERT(uniqueidentifier, '".$IdDescarga."'),
			EjercicioAlbaranCarburante = '".$EjercicioAlbaranCarburante."',
			SerieAlbaranCarburante = '".$SerieAlbaranCarburante."',
			NumeroAlbaranCarburante = '".$NumeroAlbaranCarburante."',
			EjercicioPedido = '".$EjercicioPedido."',
			SeriePedido = '".$SeriePedido."',
			NumeroPedido = '".$NumeroPedido."',
			FechaAlbaran = ".$FechaAlbaran.",
			IvaIncluido = '".$IvaIncluido."'";
			
			$obj->dataLineas = $dataLineas;
			$resultLinea = $obj->putLinea($IdAlbaran);
			
			$dataCabecera = "
            CodigoEmpresa = '".$CodigoEmpresa."',
            EjercicioAlbaran = '".$EjercicioAlbaran."',
            SerieAlbaran = '".$SerieAlbaran."',
            NumeroAlbaran = '".$NumeroAlbaran."',
            NumeroLineas = '".$numerolineas."',
            CodigoCliente = '".$CodigoCliente."',
            CodigoContable = '".$CodigoContable."',
            IdDelegacion = '".$IdDelegacion."',
			FechaAlbaran = ".$FechaAlbaran.",
			SiglaNacion = '".$SiglaNacion."',
			CifDni = '".$CifDni."',
			CifEuropeo = '".$CifEuropeo."',
			RazonSocial = '".$RazonSocial."',
			RazonSocialEnvios = '".$RazonSocialEnvios."',
			Nombre = '".$Nombre."',
			NombreEnvios = '".$NombreEnvios."',
			Domicilio = '".$Domicilio."',
			DomicilioEnvios = '".$DomicilioEnvios."',
			ViaPublicaEnvios = '".$DomicilioEnvios."',
			CodigoPostal = '".$CodigoPostal."',
			CodigoPostalEnvios = '".$CodigoPostalEnvios."',
			CodigoMunicipio = '".$CodigoMunicipio."',
			CodigoMunicipioEnvios = '".$CodigoMunicipioEnvios."',
			Municipio = '".$Municipio."',
			MunicipioEnvios = '".$MunicipioEnvios."',
			CodigoProvincia = '".$CodigoProvincia."',
			CodigoProvinciaEnvios = '".$CodigoProvinciaEnvios."',
			Provincia = '".$Provincia."',
			ProvinciaEnvios = '".$ProvinciaEnvios."',
			Nacion = '".$Nacion."',
			NacionEnvios = '".$Nacion."',
			TelefonoEnvios = '".$TelefonoEnvios."',
			FaxEnvios = '".$FaxEnvios."',
			FormadePago = '".$FormadePago."',
			NumeroPlazos = '".$NumeroPlazos."',
			DiasPrimerPlazo = '".$DiasPrimerPlazo."',
			DiasEntrePlazos = '".$DiasEntrePlazos."',
			DiasFijos1 = '".$DiasFijos1."',
			DiasFijos2 = '".$DiasFijos2."',
			DiasFijos3 = '".$DiasFijos3."',
			IndicadorIva = '".$IndicadorIva."',
			IvaIncluido = '".$IvaIncluido."',
			GrupoIva = '".$GrupoIva."',
			CodigoComisionista = '".$CodigoComisionista."',
			CodigoCanal = '".$CodigoCanal."',
			Bloqueo = '".$Bloqueo."',
			ObservacionesCliente = '".$ObservacionesCliente."',
			ObservacionesFactura = '".$ObservacionesFactura."',
			ImporteBruto = '".$ImporteBruto."',
			ImporteNetoLineas = '".$ImporteNetoLineas."',
			BaseImponible = '".$BaseImponible."',
			BaseComision = '".$BaseImponible."',
			TotalIva = '".$TotalIva."',
			TotalCuotaIva = '".$TotalIva."',
			CodigoTipoClienteLc = '".$CodigoTipoClienteLc."',
			CodigoMotivoAbonoLc = '".$CodigoMotivoAbonoLc."',
			TipoNuevaFra = '".$TipoNuevaFra."',
			ClienteFinal = '".$ClienteFinal."',
			FechaFactura = ".$FechaFactura.",
			EjercicioFactura = '".$EjercicioFactura."',
			SerieFactura = '".$SerieFactura."',
			NumeroFactura = '".$NumeroFactura."',
			CodigoIdioma_ = '".$CodigoIdioma_."',
			ImporteLiquido = '".$ImporteLiquido."',
			ReferenciaMandato = '".$ReferenciaMandato."',
			SuPedido = '".$SuPedido."',
			BLM_Metalico = '".$BLM_Metalico."',
			CodigoTipoEfecto = '".$CodigoTipoEfecto."',
			IBAN = '".$IBAN."',
			BLM_DomicilioFactura = '".$DomicilioFactura."',
			BLM_CodigoPostalFactura = '".$CodigoPostalFactura."',
			BLM_CodigoMunicipioFactura = '".$CodigoMunicipioFactura."',
			BLM_MunicipioFactura = '".$MunicipioFactura."',
			BLM_ProvinciaFactura = '".$ProvinciaFactura."',
			BLM_CodigoAutonomiaFactura = '".$CodigoAutonomiaFactura."',
			BLM_CodigoPaisFactura = '".$CodigoPaisFactura."',
			EjercicioFacturaOriginal = '".$EjercicioFacturaOriginal."',
			SerieFacturaOriginal = '".$SerieFacturaOriginal."',
			NumeroFacturaOriginal = '".$NumeroFacturaOriginal."',
			AgruparAlbaranes = '".$AgruparAlbaranes."',
			PeriodoFacturacion = '".$PeriodoFacturacion."',
			BLM_AgrupacionHeptan = '".$AgrupacionHeptan."'";
			
			$obj->dataCabecera = $dataCabecera;
			$resultCabecera = $obj->putCabecera($CodigoEmpresa, $EjercicioAlbaran, $SerieAlbaran, $NumeroAlbaran);
			
		} else {
			$resultCabecera = 0;
			$resultLinea = 0;
		}
	}
	if ($resultCabecera == 1 && $resultLinea == 1) {
		if ($IdAlbaran== null) {
			$IdAlbaran="" ;
		}
		$results[] = [
				'IdAlbaran' => $IdAlbaran,
				'ResultCode' => 0,
				'ResultText' => ''.$tiporesult.' Success'            
			];
    } else {
        if ($IdAlbaran== null) {
            $IdAlbaran="" ;
        }
        $results[] = [
            'IdAlbaran' => $IdAlbaran,
            'ResultCode' => 1,
            'ResultText' => ''.$tiporesult.' Fail. ' .error_get_last()['message'] .'. ' .error_get_last()['file'] .'. ' .error_get_last()['line']
        ];
    }
}

print_json($results);

//Cerramos la conexion.
$obj = null;

// ---------------------- Funciones controladoras ------------------------------- //

// Esta funcion crea la instancia de la clase generica y la retorna
function get_obj() {
	$object = new model_deliveries_class;
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

/*function getNextAlbaran($obj) {
    $number = $obj->getNextNumeroAlbaran();
    $next = 0;
    if (count($number) > 1) {
        $next = $number[0]['nextAlbaran'];
    }

    if ($next == null) {
        return 1;
    } else {
        return $next;
    }

}
*/
//parsea la fecha para que la acepte el insert de la base de datos (m/d/Y)
function castDateToInsert($fecha) {
	if($fecha != null) {
	    $returnFecha = "CONVERT(DATETIME, '" . $fecha . "', 102)";
		$dateTime = DateTime::createFromFormat('d/m/Y', $fecha);
		//$returnFecha = $dateTime->format('Y/m/d');
		$returnFecha = "CONVERT(DATETIME, '" . $dateTime->format('Y/m/d') . "', 102)";
	} else {
		$returnFecha = $fecha;
	}
	
	return $returnFecha;
}

//parsea la fecha para que la acepte el insert de la base de datos (m/d/Y)
// function castDateTimeToInsert($fecha) {
	// if($fecha != null) {
		// $arrayFecha = explode("/",$fecha);
		// $returnFecha = $arrayFecha[1]."/".$arrayFecha[0]."/".$arrayFecha[2];
	// } else {
		// $returnFecha = $fecha;
	// }
	
	// return $returnFecha;
// }

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
    Traza(" DELIVERYHEPTAN_RESPUESTA", json_encode($response, JSON_UNESCAPED_UNICODE));
}
?>