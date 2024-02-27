<?php
// Se incluye el archivo de conexion de base de datos
include 'core/heptan_db_model.php';

// Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
// Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
// Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
class model_locations_diferido_class extends heptan_db_model {
    // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
    public $entity;
    public $depositosEntity;
    // Almacena la informacion que sera enviada a la Base de datos
    public $data;
    public $depositosData;
    
   function ejecutaprocedimiento($bd,$LineasPosicionDescarga) {
	   /*echo sprintf("
                DECLARE @RC int
                DECLARE @LineasPosicion uniqueidentifier='$LineasPosicionDescarga'
                DECLARE @MensajeRetorno varchar(255)
                DECLARE @Retorno smallint
                EXEC @RC=Logic.dbo.WSHEPTAN_DescargasUPDATE @LineasPosicion,@MensajeRetorno OUTPUT,@Retorno OUTPUT"
            )."\n";*/
        return $this->set_query(sprintf("
                DECLARE @RC int
                DECLARE @LineasPosicion uniqueidentifier='$LineasPosicionDescarga'
                DECLARE @MensajeRetorno varchar(255)
                DECLARE @Retorno smallint
                EXEC @RC=Logic.dbo.WSHEPTAN_DescargasUPDATE @LineasPosicion,@MensajeRetorno OUTPUT,@Retorno OUTPUT"
            ));
        
    }
    
    function get($DescargaPosicion) {
        return $this->get_query("select LineasPosicion FROM WSHeptan_Descargas 
            WHERE (WSHeptan_Descargas.LineasPosicion = '$DescargaPosicion') AND (BLM_WSProcesado = -1)");
    }
}
?>