<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/heptan_db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_delivery_heptan_class extends heptan_db_model {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  // Almacena la informacion que sera enviada a la Base de datos
  public $data;

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
      $result = $this->set_query(sprintf(
          "BEGIN TRY 
       INSERT INTO %s (IdAlbaran,IdDelegacion,FechaAlbaran,SiglaNacion,CifDni,CifEuropeo,RazonSocial,RazonSocialEnvios,Nombre,NombreEnvios,Domicilio,DomicilioEnvios,CodigoPostal,CodigoPostalEnvios,CodigoMunicipio,CodigoMunicipioEnvios,Municipio,MunicipioEnvios,CodigoProvincia,CodigoProvinciaEnvios,Provincia,ProvinciaEnvios,TelefonoEnvios,FaxEnvios,FormadePago,NumeroPlazos,DiasPrimerPlazo,DiasEntrePlazos,DiasFijos1,DiasFijos2,DiasFijos3,IndicadorIva,IvaIncluido,GrupoIva,CodigoComisionista,CodigoCanal,Bloqueo,ObservacionesCliente,ObservacionesFactura,ImporteBruto,ImporteNetoLineas,BaseImponible,TotalIva,CodigoTipoClienteLc,CodigoMotivoAbonoLc,TipoNuevaFra,ClienteFinal,IdPedido,FechaFactura,EjercicioFactura,SerieFactura,NumeroFactura,CodigoIdioma_,ImporteLiquido,IdFacturaOriginal,ReferenciaMandato,SuPedido,BLM_Metalico,CodigoArticulo,CodigoAlmacen,DescripcionArticulo,CodigoIva,Unidades,Precio,PrecioRebaje,%Iva,FechaSuministro,IdDescarga,IdDeposito,UnidadesPedido,PeriodoFacturacion,PrecioOfertado,BLM_PedidoWeb,BLM_AditivoExcelent,BLM_PrecioAditivoExcelentUnit,BLM_AditivoExcelentChofer,BLM_LitrosPedido,BLM_FINCOM,BLM_ImporteCobroMetalico,IdAlbaranCarburante,BLM_ImportePagadoB2C,BLM_CodigoOperacionB2C,CodiPromocional) VALUES (%s) 
       END TRY 
       BEGIN CATCH
       PRINT '2'
       END CATCH",
          $this->entity,
          $this->data));

   return $result;
  }

  // Esta funcion sera llamada al momento de usar el metodo PUT
  function put() {
   return $this->set_query(sprintf("
    UPDATE 
     %s 
    SET 
     %s 
    WHERE 
     Id = %d",
     $this->entity,
     $this->data, 
     $this->Id
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
 }
?>