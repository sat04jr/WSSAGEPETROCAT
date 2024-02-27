<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/db_model.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecuci�n para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)
 // Esta clase implementa la interfaz iModel (Enmascara cada una de las funciones declaradas)
 class model_deliveries_class extends db_model {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entityCabecera;
  public $entityLineas;
  // Almacena la informacion que sera enviada a la Base de datos
  public $dataCabecera;
  public $dataLineas;

/*	function getNextNumeroAlbaran() {
		// return sprintf("BEGIN TRY 
		// SELECT MAX(NumeroAlbaran)+1 FROM %s WHERE CodigoEmpresa = 3 AND EjercicioAlbaran = 2018 AND SerieAlbaran = 'HEPTAN'
		// END TRY
		// BEGIN CATCH
		// PRINT 0
		// END CATCH",
			// $this->entityCabecera
			// );
		return $this->get_query(sprintf("BEGIN TRY 
		SELECT MAX(NumeroAlbaran)+1 as nextAlbaran FROM %s WHERE CodigoEmpresa = 3 AND EjercicioAlbaran = 2018 AND SerieAlbaran = 'HEPTAN'
		END TRY
		BEGIN CATCH
		PRINT 0
		END CATCH",
			$this->entityCabecera
			));
	}
*/	
	function getLinea($IdAlbaran) {
		return $this->get_query(sprintf("BEGIN TRY 
		SELECT NumeroAlbaran FROM %s WHERE LineasPosicion = CONVERT(uniqueidentifier, '%s')
		END TRY
		BEGIN CATCH
		PRINT 1
		END CATCH",
			$this->entityLineas,
			$IdAlbaran
			));
	}

  // Esta funcion sera llamada al momento de usar el metodo POST
  function postCabecera() {
/*	  echo sprintf(          
          "INSERT INTO %s (CodigoEmpresa,EjercicioAlbaran,SerieAlbaran,NumeroAlbaran,NumeroLineas,CodigoCliente,CodigoContable,IdDelegacion,FechaAlbaran,SiglaNacion,CifDni,CifEuropeo,
            RazonSocial,RazonSocialEnvios,Nombre,NombreEnvios,Domicilio,DomicilioEnvios,ViaPublicaEnvios,CodigoPostal,CodigoPostalEnvios,CodigoMunicipio,CodigoMunicipioEnvios,
            Municipio,MunicipioEnvios,CodigoProvincia,CodigoProvinciaEnvios,Provincia,ProvinciaEnvios,Nacion,NacionEnvios,TelefonoEnvios,FaxEnvios,FormadePago,NumeroPlazos,
            DiasPrimerPlazo,DiasEntrePlazos,DiasFijos1,DiasFijos2,DiasFijos3,IndicadorIva,IvaIncluido,GrupoIva,CodigoComisionista,CodigoCanal,Bloqueo,ObservacionesCliente,
            ObservacionesFactura,ImporteBruto,ImporteNetoLineas,BaseImponible,BaseComision,TotalIva,TotalCuotaIva,CodigoTipoClienteLc,CodigoMotivoAbonoLc,TipoNuevaFra,ClienteFinal,
            FechaFactura,EjercicioFactura,SerieFactura,NumeroFactura,CodigoIdioma_,ImporteLiquido,ReferenciaMandato,SuPedido,BLM_Metalico,CodigoTipoEfecto,IBAN, 
            BLM_DomicilioFactura, BLM_CodigoPostalFactura, BLM_CodigoMunicipioFactura, BLM_MunicipioFactura, BLM_ProvinciaFactura, BLM_CodigoAutonomiaFactura, BLM_CodigoPaisFactura, 
            EjercicioFacturaOriginal, SerieFacturaOriginal, NumeroFacturaOriginal, AgruparAlbaranes,PeriodoFacturacion,BLM_AgrupacionHeptan) VALUES (%s) ",
          $this->entityCabecera,
          $this->dataCabecera);
*/          
//      die;

      $result = $this->set_query(sprintf(          
          "INSERT INTO %s (CodigoEmpresa,EjercicioAlbaran,SerieAlbaran,NumeroAlbaran,NumeroLineas,CodigoCliente,CodigoContable,IdDelegacion,FechaAlbaran,SiglaNacion,CifDni,CifEuropeo,
            RazonSocial,RazonSocialEnvios,Nombre,NombreEnvios,Domicilio,DomicilioEnvios,ViaPublicaEnvios,CodigoPostal,CodigoPostalEnvios,CodigoMunicipio,CodigoMunicipioEnvios,
            Municipio,MunicipioEnvios,CodigoProvincia,CodigoProvinciaEnvios,Provincia,ProvinciaEnvios,Nacion,NacionEnvios,TelefonoEnvios,FaxEnvios,FormadePago,NumeroPlazos,
            DiasPrimerPlazo,DiasEntrePlazos,DiasFijos1,DiasFijos2,DiasFijos3,IndicadorIva,IvaIncluido,GrupoIva,CodigoComisionista,CodigoCanal,Bloqueo,ObservacionesCliente,
            ObservacionesFactura,ImporteBruto,ImporteNetoLineas,BaseImponible,BaseComision,TotalIva,TotalCuotaIva,CodigoTipoClienteLc,CodigoMotivoAbonoLc,TipoNuevaFra,ClienteFinal,
            FechaFactura,EjercicioFactura,SerieFactura,NumeroFactura,CodigoIdioma_,ImporteLiquido,ReferenciaMandato,SuPedido,BLM_Metalico,CodigoTipoEfecto,IBAN, 
            BLM_DomicilioFactura, BLM_CodigoPostalFactura, BLM_CodigoMunicipioFactura, BLM_MunicipioFactura, BLM_ProvinciaFactura, BLM_CodigoAutonomiaFactura, BLM_CodigoPaisFactura, 
            EjercicioFacturaOriginal, SerieFacturaOriginal, NumeroFacturaOriginal, AgruparAlbaranes,PeriodoFacturacion,BLM_AgrupacionHeptan) VALUES (%s) ",
          $this->entityCabecera,
          $this->dataCabecera));

   return $result;
  }
  
  function postLineas() {
//      echo "INSERT INTO ".$this->entityLineas." (CodigoEmpresa,EjercicioAlbaran,SerieAlbaran,NumeroAlbaran,Orden,LineasPosicion,CodigoCliente,CodigoArticulo,CodigoAlmacen,DescripcionArticulo,CodigoIva,Unidades,Unidades2_,Precio,PrecioRebaje,[%Iva],ImporteBruto,ImporteNeto,BaseComision,BaseImponible,BaseIva,CuotaIva,TotalIva,ImporteLiquido,CodigoCanal,FechaSuministro,UnidadesPedido,PeriodoFacturacion,PrecioOfertado,BLM_PedidoWeb,BLM_AditivoExcelent,BLM_PrecioAditivoExcelentUnit,BLM_AditivoExcelentChofer,BLM_LitrosPedido,BLM_FINCOM,BLM_ImporteCobroMetalico,BLM_ImportePagadoB2C,BLM_CodigoOperacionB2C,CodiPromocional,CodigoFamilia,CodigoSubFamilia,CodigoArancelario,CodigoDefinicion_,EjercicioFactura,SerieFactura,NumeroFactura, BLM_Heptan_IdDeposito,BLM_Heptan_IdDescarga, EjercicioAlbaranCarburante,SerieAlbaranCarburante,NumeroAlbaranCarburante) VALUES (".$this->dataLineas.") ";
//		die;
      $result = $this->set_query("INSERT INTO ".$this->entityLineas." (CodigoEmpresa,EjercicioAlbaran,SerieAlbaran,NumeroAlbaran,Orden,LineasPosicion,CodigoCliente,CodigoArticulo,CodigoAlmacen,DescripcionArticulo,CodigoIva,Unidades,Unidades2_,Precio,PrecioRebaje,[%Iva],ImporteBruto,ImporteNeto,BaseComision,BaseImponible,BaseIva,CuotaIva,TotalIva,ImporteLiquido,CodigoCanal,FechaSuministro,UnidadesPedido,PeriodoFacturacion,PrecioOfertado,BLM_PedidoWeb,BLM_AditivoExcelent,BLM_PrecioAditivoExcelentUnit,BLM_AditivoExcelentChofer,BLM_LitrosPedido,BLM_FINCOM,BLM_ImporteCobroMetalico,BLM_ImportePagadoB2C,BLM_CodigoOperacionB2C,CodiPromocional,CodigoFamilia,CodigoSubFamilia,CodigoArancelario,CodigoDefinicion_,EjercicioFactura,SerieFactura,NumeroFactura, BLM_Heptan_IdDeposito,BLM_Heptan_IdDescarga, EjercicioAlbaranCarburante,SerieAlbaranCarburante,NumeroAlbaranCarburante, EjercicioPedido, SeriePedido, NumeroPedido, FechaAlbaran, IvaIncluido) VALUES (".$this->dataLineas.") ");

   return $result;
  }

  // Esta funcion sera llamada al momento de usar el metodo PUT
  function putCabecera($CodigoEmpresa, $EjercicioAlbaran, $SerieAlbaran, $NumeroAlbaran) {
    /*  echo sprintf("
    UPDATE
     %s
    SET
     %s
    WHERE
     CodigoEmpresa = %d AND EjercicioAlbaran = %d AND SerieAlbaran = '%s' AND NumeroAlbaran = %d",
          $this->entityCabecera,
          $this->dataCabecera,
          $CodigoEmpresa,
          $EjercicioAlbaran,
          $SerieAlbaran,
          $NumeroAlbaran
          );
          */
   return $this->set_query(sprintf("
    UPDATE 
     %s 
    SET 
     %s 
    WHERE 
     CodigoEmpresa = %d AND EjercicioAlbaran = %d AND SerieAlbaran = '%s' AND NumeroAlbaran = %d", 
     $this->entityCabecera,
     $this->dataCabecera, 
     $CodigoEmpresa, 
	 $EjercicioAlbaran, 
	 $SerieAlbaran, 
	 $NumeroAlbaran
    )
   );

  }
  
  function putLinea($IdAlbaran) {
   //   echo "UPDATE ".$this->entityLineas." SET ".$this->dataLineas." WHERE LineasPosicion = CONVERT(uniqueidentifier, '".$IdAlbaran."')";
   return $this->set_query("UPDATE ".$this->entityLineas." SET ".$this->dataLineas." WHERE LineasPosicion = CONVERT(uniqueidentifier, '".$IdAlbaran."')");
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