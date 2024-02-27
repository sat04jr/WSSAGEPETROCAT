<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_cartera_heptan_class extends db_model {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  public $carteraEntity;
  
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
        /*echo sprintf("INSERT INTO %s (MovCartera, IdDelegacion, Prevision, Bloqueo, StatusBorrado, StatusRemesado, StatusImpagado, Ejercicio, SerieFactura, Factura, Comentario, NumeroOrdenEfecto, CodigoClienteProveedor, CodigoCuenta, CodigoTipoEfecto, ReferenciaMandato, IBAN, FechaEmision, FechaFactura, FechaRemesa, FechaVencimiento, FechaCobroEfecto_, ImporteEfecto, ImportePendiente, ImporteCobrado, Remesable, Contrapartida, IdCobrador, NumEfectoDivision_, FechaDivision,MovPosicion) VALUES (%s)",
        $this->entity,
        $this->data);*/
        return $this->set_query(sprintf("INSERT INTO %s (CodigoEmpresa, MovCartera, IdDelegacion, Prevision, Bloqueo, StatusBorrado, StatusRemesado, StatusImpagado, Ejercicio, SerieFactura, Factura, Comentario, NumeroOrdenEfecto, CodigoClienteProveedor, CodigoCuenta, CodigoCanal, ClaseEfecto, CodigoComisionista, TipoEfecto, CodigoTipoEfecto, ReferenciaMandato, IBAN, FechaEmision, FechaFactura, FechaRemesa, FechaVencimiento, FechaCobroEfecto_, ImporteEfecto, ImportePendiente, ImporteCobrado, Remesable, Contrapartida, IdCobrador, NumEfectoDivision_, FechaDivision,MovPosicion,BLM_Heptan_sync) VALUES (%s)",
            $this->entity,
            $this->data));

    }

  // Esta funcion sera llamada al momento de usar el metodo PUT
  function put($IdEfecto) {
/*	  return sprintf("
    UPDATE 
     %s 
    SET 
     %s 
    WHERE 
     MovCartera = CONVERT(uniqueidentifier, '%s')", 
     $this->entity,
     $this->data, 
     $IdEfecto
    );
*/
   return $this->set_query(sprintf("
    UPDATE 
     %s 
    SET 
     %s 
    WHERE 
     MovCartera = CONVERT(uniqueidentifier, '%s')", 
     $this->entity,
     $this->data, 
     $IdEfecto
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