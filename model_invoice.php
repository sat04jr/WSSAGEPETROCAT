<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_invoice_class extends db_model {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  // Almacena la informacion que sera enviada a la Base de datos
  public $data;

//Marca el mandato como sincronizado
    function setSincronized($CodigoEmpresa,$EjercicioFactura, $SerieFactura, $NumeroFactura) {
        return $this->set_query(sprintf("UPDATE %s SET %s WHERE CodigoEmpresa = '%s' AND EjercicioFactura = '%s' AND SerieFactura = '%s' AND NumeroFactura = '%s'",
            $this->entity,
            $this->data,
			$CodigoEmpresa,
            $EjercicioFactura,
            $SerieFactura,
			$NumeroFactura
        ));
    }

//devuelve heptanSyncStatus
function getSyncStatus($CodigoEmpresa,$EjercicioFactura, $SerieFactura, $NumeroFactura) {
	return $this->get_query(sprintf("SELECT BLM_Heptan_sync FROM %s WHERE CodigoEmpresa = '%s' AND EjercicioFactura = '%s' AND SerieFactura = '%s' AND NumeroFactura = '%s'",
        $this->entity,
		$CodigoEmpresa,
        $EjercicioFactura,
        $SerieFactura,
		$NumeroFactura
        ));
}
  
  // Esta funcion se activara al utilizar el metodo GET
  // Envia por defecto el parametro Id cuyo valor sera 0 hasta que se modifique
  function get($EjercicioFactura = 0, $SerieFactura, $NumeroFactura) {
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
      EjercicioFactura = '%s' AND SerieFactura = '%s' AND NumeroFactura = '%s'", 
      $this->entity, 
      $EjercicioFactura,
	  $SerieFactura,
	  $NumeroFactura
      )
     );
   }
  }

  // Esta funcion sera llamada al momento de usar el metodo POST

 }
?>