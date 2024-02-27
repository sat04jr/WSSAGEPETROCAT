<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/heptan_db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_riesgos_heptan_class extends heptan_db_model {
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
//      $result = $this->set_query(sprintf(
//          "BEGIN TRY 
//       INSERT INTO %s (CifDni,RiesgoCC,NumeroCC,FechaAprobacion,RiesgoMaximo,FechaVencimiento1,FechaSolicitud,TipoRiesgo,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado,BLM_Heptan_IdRiesgo) VALUES (%s) 
//       END TRY 
//       BEGIN CATCH
//       PRINT '2'
//       END CATCH",
//          $this->entity,
//          $this->data));

//      $result = sprintf(
//       "INSERT INTO %s (BLM_Heptan_IdRiesgo,CifDni,RiesgoCC,NumeroCC,RiesgoPetrocat,RiesgoAval,RiesgoCCConcedido,FechaAprobacionCC,FechaVencimientoCC,RiesgoPetrocatConcedido,FechaVencimientoAval,FechaSolicitud,RazonRiesgoCCCero,TipoRiesgo,StatusSolicitudRiesgo,EMailSolicitante,DiasPrimerPlazo,DiasFijos1,FormadePago,RazonSocial,Domicilio,Codigopostal,Municipio,Provincia,Telefono,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado) VALUES (%s) ",
//          $this->entity,
//          $this->data);
/*echo sprintf(
       "INSERT INTO %s (BLM_Heptan_IdRiesgo,CifDni,RiesgoCC,NumeroCC,RiesgoPetrocat,RiesgoAval,RiesgoCCConcedido,FechaAprobacionCC,FechaVencimientoCC,RiesgoPetrocatConcedido,FechaVencimientoAval,FechaSolicitud,RazonRiesgoCCCero,TipoRiesgo,StatusSolicitudRiesgo,EMailSolicitante,DiasPrimerPlazo,DiasFijos1,FormadePago,RazonSocial,Domicilio,Codigopostal,Municipio,Provincia,Telefono,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado) VALUES (%s) ",
          $this->entity,
          $this->data);	*/	  
      $result = $this->set_query(sprintf(
       "INSERT INTO %s (BLM_Heptan_IdRiesgo,CifDni,RiesgoCC,NumeroCC,RiesgoPetrocat,RiesgoAval,RiesgoCCConcedido,FechaAprobacionCC,FechaVencimientoCC,RiesgoPetrocatConcedido,FechaVencimientoAval,FechaSolicitud,RazonRiesgoCCCero,TipoRiesgo,StatusSolicitudRiesgo,EMailSolicitante,DiasPrimerPlazo,DiasFijos1,FormadePago,RazonSocial,Domicilio,Codigopostal,Municipio,Provincia,Telefono,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado) VALUES (%s) ",
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