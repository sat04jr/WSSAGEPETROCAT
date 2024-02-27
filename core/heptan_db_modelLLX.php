<?php
// Incluimos el archivo de configuraci�n el cual posee las credenciales de conexi�n
  include 'configHeptan.php';
  ini_set('mssql.charset','UTF-8');
  // Se crea la clase de conexi�n y ejecuci�n de consultas
  class heptan_db_model {

    // Variable de conexion
    public $conn;
	public $conn2;
	public $errors;

    // La funci�n constructora crea y abre la conexi�n al momento de instanciar esta clase
    function __construct() {
      //$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); // Los parametros de la funcion mysqli() son las constantes previamente declaradas en el archivo config.php
	  $this->conn =  mssql_connect(DB_HOST, DB_USER, DB_PASS);
	  mssql_select_db(DB_NAME, $this->conn);
	  $connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASS);
	  $this->conn2 = sqlsrv_connect (DB_HOST, $connectionInfo);
	  if( $this->conn2 === false ) {
		die( print_r( sqlsrv_errors(), true));
		}
	  $this->errors = null;
    }

    // Funcion para obtener un array de resultados
    // Solo se usara para las consultas de tipo SELECT
    function get_query($sql) {
      // Lee la cadena SQL recibida y ejecuta la consulta
      $result = mssql_query($sql,$this->conn);

      // Hace el rrecorrido por el array de datos y lo guarda en la variable $rows
      while ($rows[] = mssql_fetch_assoc($result));

      // Cierra la consulta
      mssql_close($this->conn);
	  
      // Retorna el resultado obtenido
      return $rows;
    }

    // Funcion para hacer cambios dentro de la base de datos
    // Solo se usara para las consultas de tipo INSERT, UPDATE Y DELETE
    function set_query($sql) {
      // Lee la cadena SQL recibida y ejecuta la consulta
	  //echo $sql;
      $result = sqlsrv_query($this->conn2,$sql);
	  //$result = mssql_query($sql, $this->conn);
	  //echo $sql;
	  
	  if( ($errors = sqlsrv_errors() ) != null) {
        foreach( $errors as $error ) {
            $this->errors =  $this->errors .utf8_decode($error['message']);
        }
	  }		
	  else{
		$errors = null;
	}
		//$this->errors = mssql_get_last_message();

      // Retorna el resultado
	  //echo $this->errors;

      return $result;

    }

    // La funci�n destructora cierra la conexi�n previamente abierta en el constructor
    function __destruct() {
      mssql_close($this->conn);
    }
  }
?>