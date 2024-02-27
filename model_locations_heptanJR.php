<?php
// Se incluye el archivo de conexion de base de datos
include 'core/heptan_db_model.php';

// Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
// Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
// Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
class model_locations_heptanJR_class extends heptan_db_model {
    // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
    public $entity;
    public $depositosEntity;
    // Almacena la informacion que sera enviada a la Base de datos
    public $data;
    public $depositosData;
 
	function get($idDeposito) {
		//echo "select BLM_Heptan_IdDeposito FROM JUPITER.Logic.dbo.Depositos WHERE BLM_Heptan_IdDeposito = '$idDeposito'";
		return $this->get_query("select BLM_Heptan_IdDeposito FROM JUPITER.Logic.dbo.Depositos WHERE BLM_Heptan_IdDeposito = '$idDeposito'");
	}
	   
    function getUltimaDescarga($idDescarga) {
        $response = $this->set_query(sprintf("SELECT TOP 1 CONVERT(char(36), LineasPosicion) as LineasPosicion FROM WSHeptan_Descargas where BLM_IdDescarga = '%s' ORDER BY BLM_WSFecha DESC",
            $idDescarga));
        
        while ($row = mssql_fetch_array($response, MSSQL_ASSOC)){
            $row2 = array_map('utf8_encode', $row);
            $Campo=$row['LineasPosicion'];
        }
        return $Campo;
    }
    // Esta funcion sera llamada al momento de usar el metodo POST
    function post() {
		/*echo sprintf("INSERT INTO %s (CodigoCliente,DomicilioDescarga,CodigoMunicipioDescarga,MunicipioDescarga,CodigoPostalDescarga,CodigoProvinciaDescarga,CodigoVendedor,MetrosManguera,IdDelegacion,FechaCaducidadCAE,CAE,CodigoCanal,CodigoComisionista,FormadePago,NumeroPlazos,DiasPrimerPlazo,DiasEntrePlazos,DiasFijos1,DiasFijos2,BIC,IBAN,ReferenciaMandato,Telefono,Telefono2,Fax,EMail1,PersonaClienteLc,ClienteFinal,CIM,DomicilioFactura, CodigoPostalFactura,CodigoMunicipioFactura,MunicipioFactura,ProvinciaFactura,CodigoAutonomiaFactura,CodigoPaisFactura,FacturacionElectronica,EnvioEFactura,EmailEnvioEFactura,BLM_AAPPOficinaContable,BLM_AAPPOficinaContableNombre,BLM_AAPPOrganoGestor,BLM_AAPPOrganoGestorNombre,BLM_AAPPUnidadTramitadora,BLM_AAPPUnidadTramitadoraNom, BLM_IdDescarga,LineasPosicion,Descripcion,ObservacionesDescarga,PeriodoFacturacion,BLM_WSMetodo) VALUES (%s)",
            $this->entity,
            $this->data);die;*/
        return $this->set_query(sprintf("INSERT INTO %s (CodigoCliente,DomicilioDescarga,CodigoMunicipioDescarga,MunicipioDescarga,CodigoPostalDescarga,CodigoProvinciaDescarga,CodigoVendedor,MetrosManguera,IdDelegacion,FechaCaducidadCAE,CAE,CodigoCanal,CodigoComisionista,FormadePago,NumeroPlazos,DiasPrimerPlazo,DiasEntrePlazos,DiasFijos1,DiasFijos2,BIC,IBAN,ReferenciaMandato,Telefono,Telefono2,Fax,EMail1,PersonaClienteLc,ClienteFinal,CIM,DomicilioFactura, CodigoPostalFactura,CodigoMunicipioFactura,MunicipioFactura,ProvinciaFactura,CodigoAutonomiaFactura,CodigoPaisFactura,FacturacionElectronica,EnvioEFactura,EmailEnvioEFactura,BLM_AAPPOficinaContable,BLM_AAPPOficinaContableNombre,BLM_AAPPOrganoGestor,BLM_AAPPOrganoGestorNombre,BLM_AAPPUnidadTramitadora,BLM_AAPPUnidadTramitadoraNom, BLM_IdDescarga,LineasPosicion,Descripcion,ObservacionesDescarga,PeriodoFacturacion,BLM_WSMetodo) VALUES (%s)",
            $this->entity,
            $this->data));
    }
    
    function postDepositos() {
        return $this->set_query(sprintf("INSERT INTO %s (DomicilioDeposito,TipoDeposito,CodigoArticulo,CapacidadDeposito,ObservacionesDeposito,TipoDepositoCliente,BLM_GoBonificado, BLM_IdDescarga, BLM_IdDeposito, LineasPosicionDescarga,BLM_WSMetodo) VALUES (%s)",
            $this->depositosEntity,
            $this->depositosData));
    }

    function ejecutaprocedimiento($bd,$LineasPosicionDescarga) {
        return $this->set_query(sprintf("
                DECLARE @RC int
                DECLARE @LineasPosicion uniqueidentifier='$LineasPosicionDescarga'
                DECLARE @MensajeRetorno varchar(255)
                DECLARE @Retorno smallint
                EXEC @RC=[Logic].[dbo].[WSHEPTAN_DescargasUPDATE] @LineasPosicion,@MensajeRetorno OUTPUT,@Retorno OUTPUT"
            ));
        
    }
    
}
?>