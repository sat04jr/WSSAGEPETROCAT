<?php
// Se incluye el archivo de conexion de base de datos
include 'core/db_model.php';

// Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
// Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
// Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
class model_saleOrder_heptanJR_class extends db_model {
    // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
    public $entity;
    public $saleOrderEntity;
    // Almacena la informacion que sera enviada a la Base de datos
    public $data;
    public $saleOrderData;
    
    // Esta funcion se activara al utilizar el metodo GET
    // Envia por defecto el parametro Id cuyo valor sera 0 hasta que se modifique
    function get($id = 0) {
        /*
         * Si el valor del parametro Id es igual a 0, se solicitaran todos los elementos
         * ya que no se ha solicitado un elemento especifico
         */
        if($id == 0) {
            return $this->get_query(sprintf("
     SELECT
      *
     FROM
      %s",
                $this->entity
                )
                );
            // Si el valor del parametro Id es diferente a 0, se solicitara solo y unicamente el elemento cuyo Id sea igual al parametro recibido
        } else {
            return $this->get_query(sprintf("
     SELECT
      *
     FROM
      %s
     WHERE
      Id = %d",
                $this->entity,
                $id
                )
                );
        }
    }
    
    // Esta funcion sera llamada al momento de usar el metodo POST
    function post() {
        /*echo sprintf("INSERT INTO %s (CodigoEmpresa,BLM_IdPedido,IdDelegacion,EjercicioPedido,SeriePedido,NumeroPedido,CodigoCliente,Fecha,CodigoArticulo,UnidadesPedidas,FechaSuministro,Precio,DescuentoClienteBases,Riesgo,Estado,CodigoCamion,CodigoConductor,CodigoCamionBase,CodigoConductorBase,ClavePeticion,ClaveAutorizacion,ObservacionesPedido,PrecioOfertado,N_Autorizacion,CodigoCanal,BLM_PedidoWeb,BLM_NombreAgenteWeb,BLM_UsuarioWeb,BLM_PedidoCapturado,BLM_EstadoPedido,BLM_AditivoExcelent,BLM_PrecioAditivoExcelentUnit,BLM_FINCOM,BLM_ImportePagadoB2C,BLM_CodigoOperacionB2C,CodiPromocional,BLM_Descuento,DocumentoPDFAdjunto,SuPedido,BLM_NumeroContratoFincom,BLM_NumeroPlazosFincom,BLM_IdDeposito,BLM_IdDescarga,PedidoWhatsapp,BLM_Heptan_sync) VALUES (%s)",
         $this->entity,
         $this->data);die;*/
        return $this->set_query(sprintf("INSERT INTO %s (CodigoEmpresa,BLM_IdPedido,IdDelegacion,EjercicioPedido,SeriePedido,NumeroPedido,CodigoCliente,Fecha,CodigoArticulo,UnidadesPedidas,FechaSuministro,Precio,DescuentoClienteBases,Riesgo,Estado,CodigoCamion,CodigoConductor,CodigoCamionBase,CodigoConductorBase,ClavePeticion,ClaveAutorizacion,ObservacionesPedido,PrecioOfertado,N_Autorizacion,CodigoCanal,BLM_PedidoWeb,BLM_NombreAgenteWeb,BLM_UsuarioWeb,BLM_PedidoCapturado,BLM_EstadoPedido,BLM_AditivoExcelent,BLM_PrecioAditivoExcelentUnit,BLM_FINCOM,BLM_ImportePagadoB2C,BLM_CodigoOperacionB2C,CodiPromocional,BLM_Descuento,DocumentoPDFAdjunto,SuPedido,BLM_NumeroContratoFincom,BLM_NumeroPlazosFincom,BLM_IdDeposito,BLM_IdDescarga,PedidoWhatsapp,BLM_Heptan_sync) VALUES (%s)",
            $this->entity,
            $this->data));
        
    }
    
    // Esta funcion sera llamada al momento de usar el metodo PUT
    function put($IdPedido) {
        //	  return sprintf("
        //    UPDATE
        //     %s
        //    SET
        //     %s
        //    WHERE
        //     BLM_IdPedido = CONVERT(uniqueidentifier, '%s')",
        //     $this->entity,
        //     $this->data,
        //     $IdPedido
        //    );
        return $this->set_query(sprintf("
    UPDATE
     %s
    SET
     %s
    WHERE
     BLM_IdPedido = CONVERT(uniqueidentifier, '%s')",
            $this->entity,
            $this->data,
            $IdPedido
            )
            );
        
    }
    
    // Esta funcion sera llamada al momento de usar el metodo DELETE
    function delete() {
        return $this->set_query(sprintf("
    DELETE FROM
     %s
    WHERE
     Id = %d",
            
            $this->entity,
            $this->Id
            )
            );
        
    }
    
    // Esta funcion sera llamada al momento de usar el metodo POST
    function CrearAutorizacion() {
        /*echo sprintf("INSERT INTO %s (CodigoEmpresa, IdDelegacion, N_Autorizacion, Fecha, OrigenMovimiento, ClavePeticion, ClaveAutorizacion, ObservacionesAutomaticas, ObservacionesBase, ObservacionesCentral, EstadoPeticion,
         RiesgoDisponible) VALUES (%s)",
         $this->entity,
         $this->data);*/
        return $this->set_query(sprintf("INSERT INTO %s (CodigoEmpresa, IdDelegacion, N_Autorizacion, Fecha, OrigenMovimiento, ClavePeticion, ClaveAutorizacion, ObservacionesAutomaticas, ObservacionesBase, ObservacionesCentral, EstadoPeticion,
                         RiesgoDisponible) VALUES (%s)",
            $this->entity,
            $this->data));
        
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