<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_saleOrder_class extends db_model {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  // Almacena la informacion que sera enviada a la Base de datos
  public $data;

//Marca el cliente como sincronizado
    function setSincronized($id) {
        /*echo sprintf("UPDATE %s SET %s WHERE BLM_IdPedido = CONVERT(uniqueidentifier, '%s') AND BLM_Heptan_Sync=0",
            $this->entity,
            $this->data,
            $id
            )."\n";*/
        return $this->set_query(sprintf("UPDATE %s SET %s WHERE BLM_IdPedido = CONVERT(uniqueidentifier, '%s') AND BLM_Heptan_Sync=0",
            $this->entity,
            $this->data,
            $id
        ));
    }

//devuelve heptanSyncStatus
function getSyncStatus($id) {
    /*echo sprintf("select BLM_Heptan_sync FROM %s WHERE BLM_IdPedido = CONVERT(uniqueidentifier, '%s')",
        $this->entity,
        $id
        )."\n";*/
	return $this->get_query(sprintf("select BLM_Heptan_sync FROM %s WHERE BLM_IdPedido = CONVERT(uniqueidentifier, '%s')",
            $this->entity,
            $id
        ));
}
  
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

   return $this->set_query(sprintf("
    INSERT INTO 
     %s
     %s",
     $this->entity,
     $this->data
     
    )
   );

   
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