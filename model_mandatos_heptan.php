<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/heptan_db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_mandatos_heptan_class extends heptan_db_model {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  public $descargasEntity;
  // Almacena la informacion que sera enviada a la Base de datos
  public $data;
  public $descargasData;

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
/*	  return sprintf(
          "INSERT INTO %s (ReferenciaMandato,TipoAdeudo,CodigoCliente,CodigoDeposito,PersonaPago,IBAN,BIC,NombreAcreedor,IdAcreedor,DomicilioAcreedor,CodigoPostalAcreedor,MunicipioAcreedor,NacionAcreedor,TipoDePago,LugarFirma,FechaFirma,DescripcionMandato,StatusProcesado,StatusBajaLc,RemesaHabitual,BLM_Autonomo,LineasPosicion,IdDescarga,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado,BLM_Heptan_IdMandato) VALUES (%s) ",
          $this->entity,
          $this->data);*/

      $result = $this->set_query(sprintf(
          "INSERT INTO %s (ReferenciaMandato,TipoAdeudo,CodigoCliente,CodigoDeposito,PersonaPago,IBAN,BIC,NombreAcreedor,IdAcreedor,DomicilioAcreedor,CodigoPostalAcreedor,MunicipioAcreedor,NacionAcreedor,TipoDePago,LugarFirma,FechaFirma,DescripcionMandato,StatusProcesado,StatusBajaLc,RemesaHabitual,BLM_Autonomo,BLM_MandatoAdjunto,LineasPosicion,IdDescarga,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado,BLM_Heptan_IdMandato) VALUES (%s) ",
          $this->entity,
          $this->data));
/*      $result = $this->set_query(sprintf(
          "INSERT INTO %s (ReferenciaMandato,TipoAdeudo,CodigoCliente,CodigoDeposito,PersonaPago,IBAN,BIC,NombreAcreedor,IdAcreedor,DomicilioAcreedor,CodigoPostalAcreedor,MunicipioAcreedor,NacionAcreedor,TipoDePago,LugarFirma,FechaFirma,DescripcionMandato,StatusProcesado,StatusBajaLc,RemesaHabitual,BLM_Autonomo,LineasPosicion,IdDescarga,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado,BLM_Heptan_IdMandato) VALUES (%s) ",
          $this->entity,
          $this->data));	*/	  

   return $result;
  }
  
  function postDescargas() {
	  // return sprintf("INSERT INTO %s (IdMandato, IdDescarga, BLM_IdLineasPosicion) VALUES (%s)",
             // $this->descargasEntity,
             // $this->descargasData);
         return $this->set_query(sprintf("INSERT INTO %s (IdMandato, IdDescarga, BLM_IdLineasPosicion) VALUES (%s)",
             $this->descargasEntity,
             $this->descargasData));
     }

  
 }
?>