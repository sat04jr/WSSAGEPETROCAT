<?php 
  // Incluimos el archivo de configuraci�n el cual posee las credenciales de conexi�n
  include 'configreal.php';
  ini_set('mssql.charset','UTF-8');
  // Se crea la clase de conexi�n y ejecuci�n de consultas
  class db_modelreal {

    // Variable de conexion
    public $conn;

    // La funci�n constructora crea y abre la conexi�n al momento de instanciar esta clase
    function __construct() {
      //$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); // Los parametros de la funcion mysqli() son las constantes previamente declaradas en el archivo config.php
	  $this->conn =  mssql_connect(DB_HOST, DB_USER, DB_PASS);
	  mssql_select_db(DB_NAME, $this->conn);
    }

    // Funcion para obtener un array de resultados
    // Solo se usara para las consultas de tipo SELECT
    function get_query($sql) {
      // Lee la cadena SQL recibida y ejecuta la consulta
      $result = mssql_query($sql,$this->conn);

      // Hace el rrecorrido por el array de datos y lo guarda en la variable $rows
      while ($rows[] = mssql_fetch_assoc($result));
	  
      // Retorna el resultado obtenido
      return $rows;
    }

    // Funcion para hacer cambios dentro de la base de datos
    // Solo se usara para las consultas de tipo INSERT, UPDATE Y DELETE
    function set_query($sql) {
      // Lee la cadena SQL recibida y ejecuta la consulta

      $result = mssql_query($sql,$this->conn);

      // Retorna el resultado
      return $result;

    }

    // La funci�n destructora cierra la conexi�n previamente abierta en el constructor
    function __destruct() {
      mssql_close($this->conn);
    }
  }
?>