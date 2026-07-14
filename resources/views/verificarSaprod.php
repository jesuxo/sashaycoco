
<?php //verificarSaprod.php

$link = mysql_connect("localhost","root","HAYque123.") or die(mysql_error());
mysql_select_db("Sisdato",$link) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
date_default_timezone_set('UTC');


  $empresa_usuario  =  " SELECT a.* 
		   			   FROM empresa a 
					   WHERE  web_url <> '' and web_sucursal >0
				     ";

$vectorcheckinsert = [];
$textimpmir        = [];


function crearTablaPreciosEspeciales($linkms, $myDB) {
    $sqlVerificar = "SELECT COUNT(*) as existe FROM sysobjects WHERE name = 'saprod_precios_especiales' AND xtype = 'U'";
    $result = mssql_query($sqlVerificar, $linkms);
    $row = mssql_fetch_array($result);

    if ($row['existe'] == 0) {
        $sqlCreate = "
            CREATE TABLE [{$myDB}].[dbo].[saprod_precios_especiales] (
                id INT IDENTITY(1,1) PRIMARY KEY,
                codprod VARCHAR(50) NOT NULL,
                grupo_id INT NOT NULL,
                grupo_nombre VARCHAR(100) NULL,
                precio_especial DECIMAL(24,10) NOT NULL,
                tasa_cambio DECIMAL(10,2) NOT NULL,
                fecha_asignacion DATETIME NOT NULL,
                fecha_actualizacion DATETIME NOT NULL,
                activo TINYINT DEFAULT 1,
                CONSTRAINT UQ_saprod_precios_especiales UNIQUE (codprod, grupo_id)
            );
            
            CREATE INDEX IX_saprod_precios_especiales_codprod 
                ON [{$myDB}].[dbo].[saprod_precios_especiales] (codprod);
            
            CREATE INDEX IX_saprod_precios_especiales_grupo 
                ON [{$myDB}].[dbo].[saprod_precios_especiales] (grupo_id);
        ";

        mssql_query($sqlCreate, $linkms) or die("Error creando tabla: " . mssql_get_last_message());

        return true;
    }
    return false;
}

$empusu   = mysql_query($empresa_usuario,$link ) or die(mysql_error());
 
while($lista     = mysql_fetch_array($empusu)){  

	  $web_url   = $lista['web_url'];
	  $codubic   = $lista['short'];
	  
	  $myServer  = $lista['myServer'];
	  $myUser    = $lista['myUser'] ;
	  $myPass    = $lista['myPass'];
      $myDB      = $lista['myDBServer'];
	  
	  $web_suc             = (isset($lista['web_sucursal'])    )? $lista['web_sucursal']    :''; 
	  $web_depo            = (isset($lista['web_depositoppal']))? $lista['web_depositoppal']:'';  
	  $anchoprod           = (isset($lista['anchoprod'])       )? $lista['anchoprod']       :40;
	  $productosbs         = (isset($lista['productosbs'])     )? $lista['productosbs']     :0;
	  $precioenpesos       = (isset($lista['precioenpesos'])   )? $lista['precioenpesos']   :0;
	  $prodsoloexento      = (isset($lista['prodsoloexento'])  )? $lista['prodsoloexento']  :0; 
	  $prodcolor           = (isset($lista['prodcolor'])       )? $lista['prodcolor']       :0;  
	  $manejocostopromedio = (isset($lista['manejocostopromedio']))? $lista['manejocostopromedio']:0;
	  $correlativoprod     = (isset($lista['correlativoprod']) )? $lista['correlativoprod'] :0; 
	  $sistemaoptico       = (isset($lista['sistemaoptico'])   )? $lista['sistemaoptico'] :0;  
	    
	  
	  $vectorcheckinsert   = [];
	  ini_set('mssql.charset', 'UTF-8');
	  $linkms = mssql_connect($myServer, $myUser, $myPass) or die("No se ha podido conectar al servidor");
	  mssql_select_db($myDB, $linkms);

      crearTablaPreciosEspeciales($linkms, $myDB);
	  
	  $pesoxdolar = 0;
	  if($precioenpesos == 1){
	    $sqlcurso = "select   pesoxdolar 
   				     from $companydb.dbo.saconf
                     where valporc>0 or pesoxdolar >0";
	  	$resporc    = mssql_query($sqlcurso, $linkms) or die(mssql_get_last_message());
	   	$listms     = mssql_fetch_array($resporc); 
	   	$pesoxdolar = $listms['pesoxdolar'];
	  }
	  
	  
	  $sqlcheckdepo = "SELECT top 1 codubic 
					   FROM  $myDB.dbo.sadepo
					   WHERE codubic = '$web_depo'
						  ";  
	  $redepo = mssql_query($sqlcheckdepo,$linkms)or die(mssql_get_last_message());
	  if($listacheck=mssql_fetch_array($redepo)){
		  		$codubic = $web_depo;
	  }else{
		  		$sqlcheckdepo = "SELECT top 1 codubic 
	  				   FROM  $myDB.dbo.sadepo
					   WHERE codubic = '$codubic'
							  ";
			   $redepo = mssql_query($sqlcheckdepo,$linkms)or die(mssql_get_last_message());
			   if($listacheck=mssql_fetch_array($redepo)){
				  
			  
			  }else{
				  $codubic = '';
			  }
	  }
 
 
if($web_url and $codubic !=''){
         $lines = 0;
		 $response = file_get_contents($web_url.'/saprod/sync/list', false, stream_context_create([
			'http' => [
				'method' => 'POST',
				'header'  => "Content-type: application/x-www-form-urlencoded",
				'content' => http_build_query([
				  'dato' => '$xjsSwN5xEX4nY2U@', 'sucursal' => '300'.$web_suc
				])
			]
		]));  
		 
		$response  = json_decode($response);

		
		$update = '';
		if(isset($response->success) and $response->success){
			
			$newproductos = $response->newproductos;

			$insert = '';
			$productos = array();

			if(isset($newproductos) and count($newproductos)>0){
				foreach ($newproductos as $newproducto){
				 
					$peso              = ($newproducto->peso)?             $newproducto->peso              : 0;
					$fijo			   = ($newproducto->fijo)?             $newproducto->fijo              : 0;
					$marca             = ($newproducto->marca)?            $newproducto->marca             : ''; 
					$activo            = ($newproducto->activo)?           $newproducto->activo            : 0;
					$refere            = ($newproducto->refere)?           $newproducto->refere            : '';
					$unidad            = ($newproducto->unidad)?           $newproducto->unidad            : '';

                    if(isset($newproducto->costact) and $newproducto->costact >0)
                        $costact           = $newproducto->costact;
                    if(isset($newproducto->costpro) and $newproducto->costpro >0)
                        $costpro           = $newproducto->costpro;
					
					if($productosbs  == 1){ //  preciodpro preciodant
                        $preciod           = ($newproducto->preciod)?          $newproducto->preciod           : 0;
						$preciod2          = ($newproducto->preciod2)?         $newproducto->preciod2          : 0;
						$costod            = ($newproducto->costod)?           $newproducto->costod            : 0;
						$costod2           = ($newproducto->costod2)?          $newproducto->costod2           : 0;
						$costod3           = ($newproducto->costod3)?          $newproducto->costod3           : 0;
					}else{
						$preciod           = ($newproducto->preciod)?          $newproducto->preciod           : 0;
						$preciod2          = ($newproducto->preciod2)?         $newproducto->preciod2          : 0; 
						$costod            = ($newproducto->costod)?           $newproducto->costod            : 0;
						$costod2           = ($newproducto->costod2)?          $newproducto->costod2           : 0;
						$costod3           = ($newproducto->costod3)?          $newproducto->costod3           : 0;
					}
					
					if($manejocostopromedio  == 1){ //  preciodpro preciodant
						$preciodpro    = ($newproducto->preciodpro)? $newproducto->preciodpro : 0;
						$preciodant    = ($newproducto->preciodant)? $newproducto->preciodant : 0;
					}
					
					if($sistemaoptico  == 1){ // sistemaoptico
						$ojotal        = ($newproducto->ojo     )? $newproducto->ojo      : ''; 
						$materialtal   = ($newproducto->material)? $newproducto->material : ''; 
					}
					 
					
					$codinst           = ($newproducto->codinst)?          $newproducto->codinst           : '';
					$codprod           = ($newproducto->codprod)?          substr($newproducto->codprod,0,15)           : ''; 
					$volumen           = ($newproducto->volumen)?          $newproducto->volumen           : 0;
					$descrip           = ($newproducto->descrip)?          substr($newproducto->descrip,0,$anchoprod)         : '';
					$descrip2          = ($newproducto->descrip2)?         $newproducto->descrip2          : '';
					$descrip3          = ($newproducto->descrip3)?         $newproducto->descrip3          : ''; 
					$descrip4          = ($newproducto->descrip4)?         $newproducto->descrip4          : ''; 
					$esexento          = ($newproducto->esexento)?         $newproducto->esexento          : 0;
					$consignacion      = ($newproducto->consignacion)?     $newproducto->consignacion      : 0;					
					if($prodsoloexento )$esexento=1;
					$exdecimal         = ($newproducto->exdecimal)?        $newproducto->exdecimal         : 0;
					$cantxempaq        = ($newproducto->cantxempaq)?       $newproducto->cantxempaq        : 0;
					$observaciones     = ($newproducto->observaciones)?    $newproducto->observaciones     : '';
					
                    $preciodolarfijo   = 0; 
					if( $precioenpesos and isset($newproducto->preciodolarfijo) and $newproducto->preciodolarfijo!=''){
					    $preciodolarfijo   = (isset($newproducto->preciodolarfijo) and $newproducto->preciodolarfijo)?       $newproducto->preciodolarfijo   : 0;
                    }
					
					$correlativo = 1;
					if( $correlativoprod and isset($newproducto->correlativo) and $newproducto->correlativo != ''){
					    $correlativo   = (isset($newproducto->correlativo) and $newproducto->correlativo)? $newproducto->correlativo : 1;
                    }
					
					
					$colorprodcolor = '';
					if( $prodcolor and isset($newproducto->color) and $newproducto->color != ''){
					    $colorprodcolor   = (isset($newproducto->color) and $newproducto->color)? $newproducto->color : '';
                    }
				 
					$sqlcheck = "SELECT top 1 codprod 
								   FROM  $myDB.dbo.saprod
								   WHERE codprod = '$codprod'
								";
					$rescheck = mssql_query($sqlcheck,$linkms)or die(mssql_get_last_message());
 
                    $sqlcheck = "SELECT top 1 descomp 
								 FROM  $myDB.dbo.sainsta
								 WHERE codinst = '$codinst'
								";
                    $resinst  = mssql_query($sqlcheck,$linkms)or die(mssql_get_last_message());
                    $descomp  = 0;

                    if($listainst=mssql_fetch_array($resinst)){
                        $descomp = $listainst['descomp'];
                    }
				
					if(!$listacheck=mssql_fetch_array($rescheck)){
						 
						   
						 $preciodolarfijodata =  $preciodolarfijofield = ''; 
						 
						 if($precioenpesos  == 1 ){
						 
						 	if(!$preciodolarfijo) $preciodolarfijo = 0;
						 
						  	$prepesos1 = $costod  * $pesoxdolar;
							$prepesos2 = $costod2 * $pesoxdolar;
							$prepesos3 = $costod3 * $pesoxdolar;
							
							$preciodolarfijodata    = " , preciodolarfijo, prepesos1, prepesos2, prepesos3 ";
							$preciodolarfijofield   =" , $preciodolarfijo, $prepesos1, $prepesos2, $prepesos3  ";
						}

						$correlativoproddata =  $correlativoprodfield = '';
						
						if($correlativoprod == 1 ) {
                            $correlativoproddata = " , correlativo  ";
                            $correlativoprodfield = " , $correlativo   ";
                        }

                        $costactdata =  $costactfield = '';
                        if($costact > 0 ){
                            $costactdata    = " , costact  ";
                            $costactfield   =" , $costact   ";
                        }

                        $costprodata =  $costprofield = '';
                        if($costpro > 0 ){
                            $costprodata    = " , costpro  ";
                            $costprofield   =" , $costpro   ";
                        }

						
						$preciodprodata = $preciodprofield = '';
						
						 if($manejocostopromedio  == 1){  
							$preciodprodata    = " , preciodpro , preciodant";
							$preciodprofield   =" , $preciod , 0";
						}
						
						$ojodata = $ojofield = '';
						
						if($sistemaoptico  == 1){
						    $ojodata    = " , ojo, material";
							$ojofield   =" , '$ojotal', '$materialtal' ";
						}
						 
						
						 $colorfield   = $colordata = '' ;
						if( $prodcolor and isset($colorprodcolor) and $colorprodcolor != ''){
							$colorfield    = " , color "; 
							$colordata     = " ,  '$colorprodcolor' "; 
						}
						
						if(!isset($vectorcheckinsert[$codprod])){
						  	$vectorcheckinsert[$codprod] =1;
							
						  $insert .= "
						  IF NOT EXISTS(SELECT CODPROD FROM  $myDB.dbo.saprod WITH (NOLOCK)  WHERE (CodProd='$codprod')) BEGIN 
								
						  	 insert into $myDB.dbo.saprod( descomp, codprod, descrip, descrip2, descrip3, descrip4, marca, refere, codinst, activo, esexento, consignacion, exdecimal, cantxempaq, volumen, peso, unidad,  preciod, preciod2, costod, costod2, costod3, fijo $preciodprodata $ojodata  $preciodolarfijodata $correlativoproddata $colorfield   $costprodata $costactdata )
								values ($descomp, '$codprod', '$descrip', '$descrip2', '$descrip3', '$descrip4', '$marca', '$refere', $codinst,  $activo, $esexento, $consignacion, $exdecimal, $cantxempaq, $volumen, $peso, '$unidad',  $preciod, $preciod2, $costod, $costod2, $costod3, $fijo $preciodprofield $ojofield  $preciodolarfijofield $correlativoprodfield  $colordata $costprofield $costactfield);
						  END 	 
								";
                          $textimpmir[$lines]['codprod'] = $codprod;
                          $textimpmir[$lines]['descrip'] = $descrip;
                          $textimpmir[$lines]['actions'] = "Creado";
                          $lines ++;
					 	}
							if(!$descomp){	
								
								$sqlcheckubic = "SELECT top 1 codprod 
												 FROM  $myDB.dbo.saexis
												 WHERE codprod = '$codprod' and codubic = '$codubic'
											";
								$rescheckubic = mssql_query($sqlcheckubic,$linkms)or die(mssql_get_last_message());
							 
							
								if(!$listacheckubic=mssql_fetch_array($rescheckubic)){
									$insert .= "
									 IF NOT EXISTS(SELECT CODPROD FROM  $myDB.dbo.saexis WITH (NOLOCK)  WHERE (CodProd='$codprod') and codubic = '$codubic') BEGIN    
										insert into [$myDB].[dbo].[saexis]   (codprod,existen,codubic) values ('$codprod',0,'$codubic');
									end
									 IF NOT EXISTS(SELECT CODPROD FROM  $myDB.dbo.newsaexis WITH (NOLOCK)  WHERE (CodProd='$codprod') and codubic = '$codubic') BEGIN   
										insert into [$myDB].[dbo].[newsaexis](codprod,existen,codubic) values ('$codprod',0,'$codubic'); 
									end
									"; 
								}
							} 
						
					}
                    else{
					
						 $preciodprodata = '';
						 if($manejocostopromedio == 1){ //  preciodpro preciodant
							$preciodprodata    = " , preciodpro = $preciodpro , preciodant = $preciodant"; 
						 }
						 
						 $ojodata = '';
						 if($sistemaoptico  == 1){
							 $ojodata    = " , ojo = '$ojotal', material = '$materialtal'"; 
						 } 
						 
						 $precioddata = ''; 
						  if($preciod >0){  
							$precioddata    = " ,  preciod = $preciod "; 
						 }
						 
						  $preciod2data = '';
						  if($preciod2 >0){  
							$preciod2data    = " ,  preciod2 = $preciod2 "; 
						 }
						 
						  $costoddata = '';
						  if($costod >0){  
							$costoddata    = " ,  costod = $costod "; 
						 }
						 
						  $costod2data = '';
						  if($costod2 >0){  
							$costod2data    = " ,  costod2 = $costod2 "; 
						 }
						 
						  $costod3data = '';
						  if($costod3 >0){  
							$costod3data    = " ,  costod3 = $costod3 "; 
						 }

                            $costactdata =  '';
                            if($costact > 0 ){
                                $costactdata    = " , costact = $costact  ";

                            }

                            $costprodata =    '';
                            if($costpro > 0 ){
                                $costprodata    = " , costpro = $costpro  ";
                            }

						  
						 $preciodolarfijofield = '';
						 if($precioenpesos  == 1  ){
						 	$prepesos1 = $costod  * $pesoxdolar;
							$prepesos2 = $costod2 * $pesoxdolar;
							$prepesos3 = $costod3 * $pesoxdolar;
							
							if(!$preciodolarfijo) $preciodolarfijo = 0;
							 
							$preciodolarfijofield   =" , preciodolarfijo = $preciodolarfijo,  prepesos1= $prepesos1, prepesos2= $prepesos2, prepesos3=$prepesos3  ";
							 
						 }
						 
						$correlativoprodfield = ''; 
						if($correlativoprod == 1 ){ 
							$correlativoprodfield   =" ,correlativo =  $correlativo   ";
						}
						  
						$wheredata = ""; 
						if($productosbs  == 1 and  $productosbs !='' ){
							$wheredata = " and prodbs = 0";
						}
						  
					    $colorfield   = '' ;
						if( $prodcolor and isset($colorprodcolor) and $colorprodcolor != ''){
							$colorfield    = " , color = '$colorprodcolor' "; 
						}
						  
						  
						   $insert .= " update $myDB.dbo.saprod set  descrip = '$descrip', descrip2 = '$descrip2', descomp = $descomp,
                              		   descrip3 = '$descrip3',descrip4 = '$descrip4', marca = '$marca', refere = '$refere', 
                              		   codinst =  $codinst,   activo = $activo, consignacion= $consignacion,
                              		   esexento =  $esexento, exdecimal = $exdecimal, cantxempaq =  $cantxempaq, 
                              		   volumen=  $volumen, peso= $peso , updated= getdate() , unidad =  '$unidad'   
                              		   $preciodprodata $ojodata $precioddata   $preciod2data  	$costoddata   $costod2data  $costod3data    $preciodolarfijofield  $correlativoprodfield $colorfield  $costactdata $costprodata
								 where codprod = '$codprod'  $wheredata
								";

                            $insert .= "
									 IF NOT EXISTS(SELECT CODPROD FROM  $myDB.dbo.saexis WITH (NOLOCK)  WHERE (CodProd='$codprod') and codubic = '$codubic') BEGIN    
										insert into [$myDB].[dbo].[saexis]   (codprod,existen,codubic) values ('$codprod',0,'$codubic');
									end
									 IF NOT EXISTS(SELECT CODPROD FROM  $myDB.dbo.newsaexis WITH (NOLOCK)  WHERE (CodProd='$codprod') and codubic = '$codubic') BEGIN   
										insert into [$myDB].[dbo].[newsaexis](codprod,existen,codubic) values ('$codprod',0,'$codubic'); 
									end
									";

                        $textimpmir[$lines]['codprod'] = $codprod;
                        $textimpmir[$lines]['descrip'] = $descrip;
                        $textimpmir[$lines]['actions'] = "Actualizado";
                        $lines ++;

						 
					}

                    $vectaux['codprod'] = $codprod;
                    array_push( $productos, $vectaux);


                    $tienePrecioEspecial = isset($newproducto->tiene_precio_especial) and $newproducto->tiene_precio_especial;
                    $precioEspecial      = isset($newproducto->precio_especial) ? $newproducto->precio_especial : 0;

                    // SI TIENE PRECIO ESPECIAL, SOBREESCRIBIR costod3
                    if ($tienePrecioEspecial and $precioEspecial > 0) {
                        $costod3 = $precioEspecial;
                        $textimpmir[$lines]['precio_especial'] = $precioEspecial;
                        $textimpmir[$lines]['grupo_id']        = isset($newproducto->grupo_descuento_id)? $newproducto->grupo_descuento_id : '';
                    }

                    // También guardar en la tabla de precios especiales para referencia
                    if ($tienePrecioEspecial and $precioEspecial > 0 and isset($newproducto->tasa_cambio_usd) and $newproducto->tasa_cambio_usd > 0) {
                        $precioEspecial = $precioEspecial / $newproducto->tasa_cambio_usd;
                        $sqlPrecioEspecial = "
                                        IF EXISTS (SELECT 1 FROM {$myDB}.dbo.saprod_precios_especiales 
                                                   WHERE codprod = '{$codprod}')
                                        BEGIN
                                            UPDATE {$myDB}.dbo.saprod_precios_especiales
                                            SET precio_especial = {$precioEspecial},
                                                tasa_cambio = {$newproducto->tasa_cambio_usd},
                                                fecha_actualizacion = GETDATE()
                                            WHERE codprod = '{$codprod}'
                                        END
                                        ELSE
                                        BEGIN
                                            INSERT INTO {$myDB}.dbo.saprod_precios_especiales
                                                (codprod, grupo_id, precio_especial, tasa_cambio, fecha_asignacion, fecha_actualizacion, activo)
                                            VALUES
                                                ('{$codprod}', {$newproducto->grupo_descuento_id}, {$precioEspecial}, 
                                                 {$newproducto->tasa_cambio_usd}, GETDATE(), GETDATE(), 1)
                                        END
                                    ";
                        mssql_query($sqlPrecioEspecial, $linkms);
                    }
						 
				}

				$array = $productos;
				$array = json_encode($array);

				$response = file_get_contents($web_url.'/saprod/sync/saprodsucursal', false, stream_context_create([
					'http' => [
						'method' => 'POST',
						'header'  => "Content-type: application/x-www-form-urlencoded",
						'content' => http_build_query([
							'productos' => $array , 'dato' => '$xjsSwN5xEX4nY2U@', 'sucursal' => '300'.$web_suc,
						])
					]
				]));

				$response  = json_decode($response);
				$update = '';
               
                if(isset($response->success) and $response->success) {

					mssql_query($insert, $linkms) or die(mssql_get_last_message()."<br>".$insert);
                    ?>
                    <table style="width: 92%; margin: auto" width="92%" border="0">
                        <?
                        if(isset($textimpmir)){
                            foreach ($textimpmir as $line) {?>
                                <tr>
                                    <td width="10%" align="left"><? echo $line['codprod']?></td>
                                    <td width="80%" align="left"><? echo $line['descrip']?></td>
                                    <td width="10%" align="left"><? echo $line['actions']?></td>
                                </tr>
                        <?  }
                        }
                        ?>
                    </table>

                    <?php
				}
			}


            $newservicios = (isset($response->newservicios))? $response->newservicios: null;

            $insert = '';
            $servicios = array();

            if(isset($newservicios) and count($newservicios)>0){
                $insert = '';
                foreach ($newservicios as $newservicio){

                        $activo            = ($newservicio->activo)?           $newservicio->activo                : 0;
                        $codinst           = ($newservicio->codinst)?          $newservicio->codinst               : '';
                        $codserv           = ($newservicio->codserv)?          strtoupper($newservicio->codserv)   : '';
                        $descrip           = ($newservicio->descrip)?          strtoupper($newservicio->descrip  ) : '';
                        $usaserv           = ($newservicio->usaserv    > 0)?        $newservicio->usaserv          : 0;
                        $esexento          = ($newservicio->esexento   > 0)?        $newservicio->esexento         : 0;
                        $esdecimal         = ($newservicio->esdecimal  > 0)?        $newservicio->esdecimal        : 0;
                        $preciodolarfijo   = ($newservicio->preciodolarfijo > 0 )?  $newservicio->preciodolarfijo  : 0;


                        $sqlcheck = "SELECT top 1 codserv 
									 FROM  $myDB.dbo.saserv
									 WHERE codserv = '$codserv'
									";
                        $rescheck = mssql_query($sqlcheck,$linkms)or die(mssql_get_last_message());


                        if(!$listacheck=mssql_fetch_array($rescheck)){


                            $insert .= " insert into $myDB.dbo.saserv( codserv,   descrip,   codinst,   activo, esexento,   esdecimal,   preciodolarfijo, usaserv)
															  values ('$codserv', '$descrip', $codinst, $activo, $esexento, $esdecimal, $preciodolarfijo, $usaserv); 
									 
									";


                        }else{

                            $insert .= " update $myDB.dbo.saserv set  
										   descrip     = '$descrip',  
										   codinst     = $codinst,   
										   activo      = $activo, 
										   esexento    = $esexento, 
										   esdecimal   = $esdecimal, 
										   preciodolarfijo = $preciodolarfijo,  
										   usaserv     =  $usaserv 
										 where codserv = '$codserv'
									";
                        }
                        $vectaux['codserv'] = $codserv;
                        array_push( $servicios, $vectaux);

                    }

                $array = $servicios;
                $array = json_encode($array);


                $response = file_get_contents($web_url.'/saserv/sync/saservsucursal', false, stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header'  => "Content-type: application/x-www-form-urlencoded",
                            'content' => http_build_query([
                                'servicios' => $array , 'dato' => '$xjsSwN5xEX4nY2U@', 'sucursal' => '300'.$web_suc,
                            ])
                        ]
                ]));

                $response  = json_decode($response);

                if(isset($response->success) and $response->success) {
                    mssql_query($insert, $linkms) or die(mssql_get_last_message()."<br>".$insert);
                }

            }

        }
	 

	mssql_close($linkms);
}

 
}	


?>