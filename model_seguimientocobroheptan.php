<?php
// Se incluye el archivo de conexion de base de datos
include 'core/db_model.php';

// Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
// Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
// Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
class model_seguimientocobroheptan_class extends db_model {
    // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
    public $entity;
    // Almacena la informacion que sera enviada a la Base de datos
    public $data;
    
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
    function CrearTarea() {

/*echo sprintf("INSERT INTO %s (CodigoEmpresa, IdDelegacion, CodigoCliente, AccionPosicionLc, ComercialAsignadoLc, FechaInicialLc, HoraInicioPrevista,RazonSocial, PersonaClienteLc, Telefono, Telefono2, Telefono3, Fax, EMail1, EMail2,
                         TextoLlamadaLc, CodigoTemaAdministrativoLc) VALUES (%s)",
            $this->entity,
            $this->data);die;*/
        return $this->set_query(sprintf("INSERT INTO %s (CodigoEmpresa, IdDelegacion, CodigoCliente, AccionPosicionLc, ComercialAsignadoLc, FechaInicialLc, HoraInicioPrevista,RazonSocial, PersonaClienteLc, Telefono, Telefono2, Telefono3, Fax, EMail1, EMail2,
                         TextoLlamadaLc, CodigoTemaAdministrativoLc) VALUES (%s)",
            $this->entity,
            $this->data));
        
    }
    
    function CrearSeguimiento() {
		/*echo sprintf("INSERT INTO %s (CodigoEmpresa, IdDelegacion, CodigoCliente, AccionPunteroLc, CodigoEmpleadoLc, FechaFinalLc, 
			HoraInicioPrevista, ObservacionesRespuestaLc,
            SeguimientoPosicionLc, StatusRespuestaLc) VALUES (%s)",
            $this->entity,
            $this->data);die;*/
        return $this->set_query(sprintf("INSERT INTO %s (CodigoEmpresa, IdDelegacion, CodigoCliente, AccionPunteroLc, CodigoEmpleadoLc, FechaFinalLc, 
			HoraInicioPrevista, ObservacionesRespuestaLc,
            SeguimientoPosicionLc, StatusRespuestaLc) VALUES (%s)",
            $this->entity,
            $this->data));
        
    }
    
    // Esta funcion sera llamada al momento de usar el metodo PUT
    function putefecto($IdEfecto) {
	/*	echo sprintf("
    UPDATE
     %s
    SET
     %s
    WHERE
     MovCartera = CONVERT(uniqueidentifier, '%s')",
            $this->entity,
            $this->data,
            $IdEfecto
            );die;*/
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
        
}
?>