<?php 
 // Declarar la interfaz 'iModel'
 // Define cada una de las funciones que el model.php debe especificar
 interface iModel
 {
     // GET : Solicitar un elemento
     public function get();
     // POST : Publicar un nuevo elemento
     public function post();
     // PUT: Modificar un elemento
     public function put();
     // DELETE: Eliminar un elemento
     public function delete();
 }
?>