<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/db_modelreal.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_fuelsJR_class extends db_modelreal {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  // Almacena la informacion que sera enviada a la Base de datos
  public $data;

//Marca el cliente como sincronizado
    function setSincronized($CodigoArticulo, $Fecha, $Tarifa) {
/*        echo sprintf("UPDATE %s SET TarifaPrecio.%s FROM %s INNER JOIN BLM_PR120_ConversionArticulos ON (TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa 
AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo) WHERE BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = '%s' AND TarifaPrecio.FechaInicio = '%s' AND TarifaPrecio.Tarifa = '%s'",
            $this->entity,
            $this->data,
            $this->entity,
            $CodigoArticulo,
            $Fecha,
            $Tarifa
        );*/
        return $this->set_query(sprintf("UPDATE %s SET TarifaPrecio.%s FROM %s INNER JOIN BLM_PR120_ConversionArticulos 
		ON (TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo) 
		WHERE BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = '%s' AND TarifaPrecio.FechaInicio = '%s' AND TarifaPrecio.Tarifa = '%s'",
            $this->entity,
            $this->data,
            $this->entity,
            $CodigoArticulo,
            $Fecha,
            $Tarifa
        ));

        // return $this->get_query("SELECT TarifaPrecio.*
// FROM TarifaPrecio 
// WHERE TarifaPrecio.Tarifa like 768
// AND TarifaPrecio.CodigoArticulo IN (SELECT BLM_PR120_ConversionArticulos.CodigoArticulo FROM BLM_PR120_ConversionArticulos WHERE BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = '11528')");

        // return $this->get_query("SELECT TarifaPrecio.*
// FROM TarifaPrecio 
// WHERE TarifaPrecio.FechaInicio = '16/05/2017' AND TarifaPrecio.Tarifa like '%768%'
// AND TarifaPrecio.CodigoArticulo IN (SELECT BLM_PR120_ConversionArticulos.CodigoArticulo FROM BLM_PR120_ConversionArticulos WHERE BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = '11528')");

//        return $this->set_query("UPDATE TarifaPrecio SET TarifaPrecio.BLM_Heptan_sync = -1 FROM TarifaPrecio INNER JOIN BLM_PR120_ConversionArticulos ON (TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo) WHERE RTRIM(BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase) = '11528' AND TarifaPrecio.FechaInicio = '16/05/2017' AND RTRIM(TarifaPrecio.Tarifa) = '768'");
    }
//devuelve heptanSyncStatus
function getSyncStatus($CodigoArticulo, $Fecha, $Tarifa) {
	/*echo sprintf("select BLM_Heptan_sync FROM %s WITH (nolock) 
		INNER JOIN BLM_PR120_ConversionArticulos WITH (nolock) ON (TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo) WHERE BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = '%s' AND TarifaPrecio.FechaInicio = '%s' AND TarifaPrecio.Tarifa = '%s'",
        $this->entity,
        $CodigoArticulo,
        $Fecha,
        $Tarifa
        );*/
	return $this->get_query(sprintf("select BLM_Heptan_sync FROM %s WITH (nolock) 
		INNER JOIN BLM_PR120_ConversionArticulos WITH (nolock) ON (TarifaPrecio.CodigoEmpresa = BLM_PR120_ConversionArticulos.CodigoEmpresa AND TarifaPrecio.CodigoArticulo = BLM_PR120_ConversionArticulos.CodigoArticulo) WHERE BLM_PR120_ConversionArticulos.BLM_CodigoArticuloBase = '%s' AND TarifaPrecio.FechaInicio = '%s' AND TarifaPrecio.Tarifa = '%s'",
        $this->entity,
        $CodigoArticulo,
        $Fecha,
        $Tarifa
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