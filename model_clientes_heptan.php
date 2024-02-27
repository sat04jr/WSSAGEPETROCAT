<?php
// Se incluye el archivo de conexion de base de datos
include 'core/heptan_db_model.php';
date_default_timezone_set('UTC');

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_clientes_heptan_class extends heptan_db_model {
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
  function post() {
	  
//	     return sprintf("INSERT INTO %s (CodigoCliente,SiglaNacion,CifDni,CifEuropeo,FechaAlta,CodigoContable,RazonSocial,Nombre,DomicilioFiscal,CodigoPostalFiscal,CodigoMunicipioFiscal,MunicipioFiscal,ProvinciaFiscal,FormadePago,IBAN,BIC,IndicadorIva,ObservacionesCliente,AgruparAlbaranes,Telefono,Telefono2,Fax,EMail1,BajaEmpresaLc,FechaBajaLc,CodigoMotivoBajaClienteLc,CodigoTipoClienteLc,PersonaClienteLc,EnvioEFactura,EmailEnvioEFactura,PeriodoFacturacion,FacturaBase,NumeroPlazos,DiasPrimerPlazo,DiasEntrePlazos,DiasFijos1,DiasFijos2,BLM_EmailFidelCat,BLM_MovilFidelcat,BLM_FechaAltaFidelcat,BLM_FechaBajaFidelcat,BLM_CodigoTarjetaPuntos,BLM_PuntosClub,BLM_NoInteresado,DomicilioFactura,CodigoPostalFactura,CodigoMunicipioFactura,MunicipioFactura,ProvinciaFactura,BLM_WSMetodo, BLM_WSProcesado) VALUES (%s)",
//           $this->entity,
//           $this->data);
   return $this->set_query(sprintf("INSERT INTO %s (CodigoCliente,SiglaNacion,CifDni,CifEuropeo,FechaAlta,CodigoContable,RazonSocial,Nombre,DomicilioFiscal,CodigoPostalFiscal,CodigoMunicipioFiscal,MunicipioFiscal,ProvinciaFiscal,FormadePago,IBAN,BIC,ReferenciaMandato,IndicadorIva,ObservacionesCliente,AgruparAlbaranes,Telefono,Telefono2,Fax,EMail1,BajaEmpresaLc,FechaBajaLc,CodigoMotivoBajaClienteLc,CodigoTipoClienteLc,PersonaClienteLc,EnvioEFactura,EmailEnvioEFactura,PeriodoFacturacion,FacturaBase,NumeroPlazos,DiasPrimerPlazo,DiasEntrePlazos,DiasFijos1,DiasFijos2,BLM_EmailFidelCat,BLM_MovilFidelcat,BLM_FechaAltaFidelcat,BLM_FechaBajaFidelcat,BLM_CodigoTarjetaPuntos,BLM_PuntosClub,BLM_NoInteresado,DomicilioFactura,CodigoPostalFactura,CodigoMunicipioFactura,MunicipioFactura,ProvinciaFactura,BLM_WSMetodo, BLM_WSProcesado, BLM_NumeroContratoFincom) VALUES (%s)",
           $this->entity,
           $this->data));


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
