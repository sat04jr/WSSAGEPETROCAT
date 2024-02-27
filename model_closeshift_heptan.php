<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/heptan_db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_closeshift_heptan_class extends heptan_db_model {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  public $closeshiftEntity;
  // Almacena la informacion que sera enviada a la Base de datos
  public $data;
  public $closeshiftData;

  // Esta funcion sera llamada al momento de usar el metodo POST
  function post() {
/*   return sprintf(
          "INSERT INTO %s (TipoMov,CargoAbono,CodigoCuenta,ImporteAsiento,Ejercicio,CodigoEmpresa,EmpresaOrigen,CodigoUsuario,FechaAsiento,Contrapartida,Comentario,CodigoCanal,IdDelegacion,Asiento,CodigoDiario,StatusAcumulacion,TipoEntrada,CodigoConcepto,CodigoDepartamento,CodigoProyecto,CodigoSeccion,DocumentoConta,TipoDocumento,Metalico347,NumeroPeriodo,LineasPosicion,BLM_Heptan_IdMovimiento,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado) VALUES (%s) ",
          $this->entity,
          $this->data);*/
      $result = $this->set_query(sprintf(
          "INSERT INTO %s (TipoMov,CargoAbono,CodigoCuenta,ImporteAsiento,Ejercicio,CodigoEmpresa,EmpresaOrigen,CodigoUsuario,FechaAsiento,Contrapartida,Comentario,CodigoCanal,IdDelegacion,Asiento,CodigoDiario,StatusAcumulacion,TipoEntrada,CodigoConcepto,CodigoDepartamento,CodigoProyecto,CodigoSeccion,DocumentoConta,TipoDocumento,Metalico347,NumeroPeriodo,LineasPosicion,BLM_Heptan_IdMovimiento,BLM_WSMetodo,BLM_WSFecha,BLM_WSProcesado) VALUES (%s) ",
          $this->entity,
          $this->data));

   return $result;
  }
  
   
 }
?>