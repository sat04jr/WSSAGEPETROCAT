<?php 
 // Permite la conexion desde cualquier origen
 header("Access-Control-Allow-Origin: *");
 // Permite la ejecucion de los metodos
 header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");  
 // Se incluye el archivo que contiene la clase generica
 include 'model.php';

 // Se toma la URL solicitada y se guarda en un array de datos
 // Por ejemplo si la URL solicitada es http://localhost/api/usuario
 // $_SERVER['REQUEST_URI'] imprime "/api/usuario"
 // La funcion explode() crea un array de la URL de la siguiente forma
 /*
  Array
  (
      [0] => 
      [1] => api
      [2] => usuario
  )
 */
 // Por ejemplo si la URL solicitada es http://localhost/api/usuario/1
 // $_SERVER['REQUEST_URI'] imprime "/api/usuario/1"
 // La funcion explode() crea un array de la URL de la siguiente forma
 /*
  Array
  (
      [0] => 
      [1] => api
      [2] => usuario
      [3] => 1
  )
 */
 // Esto nos ayuda a identificar cuando se esta solicitando la URL general o un elemento especifico
 $array = explode("/", $_SERVER['REQUEST_URI']);

 // Obtener el cuerpo de la solicitud HTTP
 // En nuestro caso, el cuerpo solo sera enviado en peticiones de tipo POST y PUT, en el cual enviaremos el objeto JSON a registrar o modificar
 $bodyRequest = file_get_contents("php://input");

 /* Este ciclo rrecorre el array previamente creado y si hay algun valor en blanco lo elimina del array
    Esto con el fin de controlar cuando la URL se enviar en estilo http://localhost/api/usuario/
    Si bien, se esta haciendo uso del "/" al final, no se esta enviando ningun parametro de Id
    Sin embargo, el array se crea de la siguiente forma
 
  Array
  (
      [0] => 
      [1] => api
      [2] => usuario
      [3] => 
  )

  Ya que la ultima pocision esta vacia, si lo permitieramos asi, nos arrojaria un error ya que no haria la
  Solicitud de manera correcta con un dato que esta vacio, por lo que si la URL es enviada del forma, se asume
  que se esta realizando una solicitud general al estilo http://localhost/api/usuario
 */
 foreach ($array as $key => $value) {
  if(empty($value)) {
   unset($array[$key]);
  }
 }

 /* Analiza la ultima pocision del array creado previamente, si el valor analizado es mayor que 0
    significa que el caracter enviado es un numero, por lo tanto, reconocemos que la solicitud se esta 
    haciendo a un Id especifico de tipo http://localhost/api/usuario/1, pero de no ser mayor que 0, reconocemos que el ultimo elemento del array
    es solo el nombre de la entidad, por lo tanto, reconocemos que se esta haciendo una solicitud general
    de tipo http://localhost/api/usuario
 */
 if(end($array)>0) {
  // De ser el valor numerico, crea dos variables que contienen el Id solicitado y la entidad solicitada
  $id = $array[count($array)];
  $entity = $array[count($array) - 1];
 } else {
  // De ser el valor de tipo string, solo crea la variable de la entidad solicitada
  $entity = $array[count($array)];
 }

 // Variable que guarda la instancia de la clase generica
 $obj = get_obj();

 // Se pasa a la entidad el valor de la entidad con la que actualmente se esta trabajando
 $obj->entity = $entity;

 // Analiza el metodo usado actualmente de los cuatro disponibles: GET, POST, PUT, DELETE
 switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
   // Acciones del Metodo GET
   // Si la variable Id existe, solicita al modelo el elemento especifico
   if(isset($id)) {
    $data = $obj->get($id);
   // Si no existe, solicita todos los elementos
   } else {
    $data = $obj->get();
   }
   
   // Elimina el ultimo elemento del array $data, ya que usualmente, suele traer dos elementos, uno con la informacion, y otro NULL el cual no necesitamos
   array_pop($data);

   // Si la cantidad de elementos que trae el array de $data es igual a 0 entra en este condicional
   if(count($data)==0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
    if(isset($id)) {
     print_json(404, "Not Found", null);
    // Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que msotrar
    } else {
     print_json(204, "Not Content", null);
    }
   // Si la cantidad de elementos del array de $data es mayor que 0 entra en este condicional
   } else {
    // Imprime la informacion solicitada
    print_json(200, "OK", $data);
   }
   
   break;
  case 'POST':
   // Acciones del Metodo POST
   
   /* Analiza si existe la variable Id, ya que la URL solicita por POST solo puede ser de estilo
      http://localhost/api/usuario no habria por que existir un Id ya que se esta registrando un 
      nuevo elemento y el Id es autogenerado, si el Id no existe, entra en esta condicional */
   if(!isset($id)) {
    // Decodifica el cuerpo de la solicitud y lo guarda en un array de PHP
    $array = json_decode($bodyRequest, true);

    // Renderiza la informacion obtenida que luego sera guardada en la Base de datos
    $obj->data = renderizeData(array_keys($array), array_values($array));

    // Ejecuta la funcion post() que se encuentra en la clase generica
    $data = $obj->post();

    // Si la respuesta es correcta o es igual a true entra en este condicional
    if($data) {
     // Si la Id generada es diferente de 0 se creo el elemento y entra aqui
     if($obj->conn->insert_id != 0) {
      // Se consulta la Id autogenerada para hacer un callBack
      $data = $obj->get($obj->conn->insert_id);

      // Si la variable $data es igual a 0, significa que el elemento no ha sido creado como se suponia
      if(count($data)==0) {
       
       print_json(201, false, null);
      // Si la variable $data es diferente de 0, el elemento ha sido creado y manda la siguiente respuesta
      } else {
       array_pop($data);
       print_json(201, "Created", $data);
      }
      
     // Si el Id generada es igual a 0, el elemento no ha sido creado y manda la siguiente respuesta
     } else {
      print_json(201, false, null);

     }
    // Si la respuesta es false, se supone que el elemento no ha sido registrado, y entra en este condicional
    } else {
     print_json(201, false, null);
    }
   // En tal caso de que exista la variable Id, imprimira el mensaje del que el metodo solicitado no es correcto
   } else {
    print_json(405, "Method Not Allowed", null);
   }
   


   break;
  case 'PUT':
   // Acciones del Metodo PUT
   if(isset($id)) {
    // Consulta primeramente que en realidad exista un elemeto con el Id antes de modificar
    $info = $obj->get($id);
    array_pop($info);

    // Si la info recibida es diferente de 0, el elemento existe, por lo tanto procede a modificar 
    if(count($info)!=0) {
     $array = json_decode($bodyRequest, true);

     $obj->data = renderizeData(array_keys($array), array_values($array));

     $obj->Id = $id;
     $data = $obj->put();

     if($data) {
      $data = $obj->get($id);

      if(count($data)==0) {
       print_json(200, false, null);
      } else {
       array_pop($data);
       print_json(200, "OK", $data);
      }

     } else {
      print_json(200, false, null);
     }
    // Si la info recibida es igual a 0, el elemento no existe y no hay nada para modificar
    } else {
     print_json(404, "Not Found", null);
    }
    
   } else {
    print_json(405, "Method Not Allowed", null);
   }

   break;
  case 'DELETE':
   if(isset($id)) {

    $info = $obj->get($id);

    if(count($info)==0) {
     print_json(404, "Not Found", null);
    } else {
     $obj->Id = $id;
     $data = $obj->delete();

     if($data) {
      array_pop($info);
      if(count($info)==0) {
       print_json(404, "Not Found", null);
      } else {
       print_json(200, "OK", $info);
      }
      
     } else {
      print_json(200, false, null);
     }
    }

   } else {
    print_json(405, "Method Not Allowed", null);
   }
   break;
  
  default:
   // Acciones cuando el metodo no se permite
   // En caso de que el Metodo Solicitado no sea ninguno de los cuatro disponible, envia la siguiente respuesta
   print_json(405, "Method Not Allowed", null);
   break;
 }

 // ---------------------- Funciones controladoras ------------------------------- //

 // Esta funcion crea la instancia de la clase generica y la retorna
 function get_obj() {
  $object = new generic_class;
  return $object;
 }

 // Esta funcion renderiza la informacion que sera enviada a la base de datos
 function renderizeData($keys, $values) {

  switch ($_SERVER['REQUEST_METHOD']) {
   case 'POST':
    # code...
     foreach ($keys as $key => $value) {
      if($key == count($keys) - 1) {
       $str = $str . $value . ") VALUES (";

       foreach ($values as $key => $value) {
        if($key == count($values) - 1) {
         $str = $str . "'" . $value . "')";
        } else {
         $str = $str . "'" . $value . "',";
        }
        
       }
      } else {
       if($key == 0) {
        $str = $str . "(" . $value . ",";
       } else {
        $str = $str . $value . ",";
       }
       
      }
     }

     return $str;
    break;
   case 'PUT':
    foreach ($keys as $key => $value) {
     if($key == count($keys) - 1) {
      $str = $str . $value . "='" . $values[$key] . "'"; 
     } else {
      $str = $str . $value . "='" . $values[$key] . "',"; 
     }
    }
    return $str;
    break;
  }
  


 }

 // Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
 function print_json($status, $mensaje, $data) {
 #print_r( $data);
  header("HTTP/1.1 $status $mensaje");
  header("Content-Type: application/json; charset=UTF-8");

  $response['statusCode'] = $status;
  $response['statusMessage'] = $mensaje;
  $response['data'] = $data;

  echo json_encode($response); //, JSON_PRETTY_PRINT);
 }
?>