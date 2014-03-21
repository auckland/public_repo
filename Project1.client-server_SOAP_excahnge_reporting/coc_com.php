#!/usr/bin/php -q

<?php
include("/home/www/php-lib/includes/constantes.inc.php");
include("/home/www/php-lib/includes/configuracion.inc.php");
include(k_sPathClass."conexion.class.php");
include(k_sPathClass."mensaje.class.php");

$sUsuario = "coc";
$g_sUsuario = $sUsuario;
$sAccion = "";

$vCnn	= New Conexion;
$vMsg	= New Mensaje;
$vCnn->direccion(k_sDireccion);
$vCnn->puerto(k_iPuerto);
$vCnn->timeout(60);
$vMsg->idUsuario($sUsuario);
$vMsg->codSuc(k_sCodSuc);
$vMsg->ipAddress(getenv("REMOTE_ADDR"));
$sAddr = k_sDireccion;
$iPort = k_iPuerto;
//SOLICITUD
$vTitlesSolicitud = array("CUITBANCO", "CUITCLIENTE", "CODIGOMONEDA", "COTIZACION", "IMPORTE", "CUITREPRE", "CONCEPTO");
//INFORMAR
$vTitlesInformar = array("CUITBANCO", "SOLICITUD", "ESTADO");
//ANULACION
$vTitlesAnulacion = array("CUITBANCO", "CUITCLIENTE", "COC");
$vData = trim($argv[5]);

function cleanUp($sText) {
	$vPatterns = array(
									'/Ã¡/',
									'/Ã©/',
									'/í©/',
									'/Ã³/',
									'/íº/',
									'/Ãº/',
									'/Ã±/',
									'/í‘/',
									'/Ã\\­/'
									);
	$vReplacements = array(
									'a',
									'e',
									'e',
									'o',
									'u',
									'u',
									'n',
									'N',
									'i'
									);
	/*$vReplacements = array(
									'á',
									'é',
									'é',
									'ó',
									'ú',
									'ú',
									'ñ',
									'Ñ',
									'í'
									);*/
	return preg_replace($vPatterns, $vReplacements, $sText);
}

function Usage() {
 return true;
}

//ver coc_transact.php para el formato de respuesta en cada caso
function processResponseSolicitud($vMsgInput) {
	switch( strtoupper($vMsgInput["resultado"]) ) {
  	case "A":
  		$vArrRetAcceptData = array("resultado", "codigoSolicitud", "coc", "estadoSolicitud", "cuit");
  		foreach ($vArrRetAcceptData as $sValue) {
  			$iIndex = $sValue == "resultado" ? 0 : 1;
  			$vLine[$iIndex][$sValue] = $vMsgInput[$sValue];
  		}
  		//Agregamos otro pipe al final para otro tipo de operaciones
  		$vLine[0]["OPERACION"] = "";
  	break;
  	case "O":
  		$vArrRetObservData = array("resultado", "codigoSolicitud", "estadoSolicitud", "cuit", "codigo", "descripcion");
  		foreach ($vArrRetObservData as $sValue) {
  			$iIndex = $sValue == "resultado" ? 0 : 1;
  			if ($sValue == "codigoSolicitud") {
  				$sPutMsg = sprintf("%010d", $vMsgInput[$sValue]);
  			} else if ($sValue == "descripcion") {
  				$sPutMsg = cleanUp( substr($vMsgInput[$sValue], 0, 99) );
  			} else {
  				$sPutMsg = $vMsgInput[$sValue];
  			}
  			$vLine[$iIndex][$sValue] = $sPutMsg;
  		}
  		//Agregamos otro pipe al final para otro tipo de operaciones
  		$vLine[0]["OPERACION"] = "";
  	break;
  	case "E":
  	case "R":
  	default:
  		$vArrRetErrData = array("resultado", "codigo", "descripcion", "cuit");
  		foreach ($vArrRetErrData as $sValue) {
  			$iIndex = $sValue == "resultado" ? 0 : 1;
  			$vLine[$iIndex][$sValue] = ($sValue == "descripcion") ? cleanUp( substr($vMsgInput[$sValue], 0, 99) ) : $vMsgInput[$sValue];
  		}
  		//Agregamos otro pipe al final para otro tipo de operaciones
  		$vLine[0]["OPERACION"] = "";
	}
	ksort($vLine);
	return $vLine;
}

function processResponseInformar($vMsgInput) {
	switch( strtoupper($vMsgInput["resultado"]) ) {
  	case "A":
  		$vArrRetAcceptData = array("resultado", "codigoSolicitud", "estadoSolicitud");
  		foreach ($vArrRetAcceptData as $sValue) {
  			$iIndex = $sValue == "resultado" ? 0 : 1;
  			if ($sValue == "codigoSolicitud") {
  				$sPutMsg = sprintf("%010d", $vMsgInput[$sValue]);
  			} else if ($sValue == "descripcion") {
  				$sPutMsg = cleanUp( substr($vMsgInput[$sValue], 0, 99) );
  			} else {
  				$sPutMsg = $vMsgInput[$sValue];
  			}
  			$vLine[$iIndex][$sValue] = $sPutMsg;
  		}
  		$vLine[0]["OPERACION"] = "I";
  	break;
  	case "E":
  	case "R":
  	default:
  		$vArrRetErrData = array("codigoSolicitud", "resultado", "codigo", "descripcion");
  		foreach ($vArrRetErrData as $sValue) {
  			$iIndex = $sValue == "resultado" ? 0 : 1;
  			if ($sValue == "codigoSolicitud") {
  				$sPutMsg = sprintf("%010d", $vMsgInput[$sValue]);
  			} else if ($sValue == "descripcion") {
  				$sPutMsg = cleanUp( substr($vMsgInput[$sValue], 0, 99) );
  			} else {
  				$sPutMsg = $vMsgInput[$sValue];
  			}
  			$vLine[$iIndex][$sValue] = $sPutMsg;
  		}
  		$vLine[0]["OPERACION"] = "I";
	}
	ksort($vLine);
	return $vLine;
}

function grabarArchivo($sArchivoTarget, $sTexto="", $sMode='a+') {
    $sAh = fopen($sArchivoTarget, $sMode);
    if ($sAh === false) {
        return false;
    }
    if (  ( strlen($sTexto) > 0 ) && ( fwrite($sAh, $sTexto) === FALSE )  ) {
        return false;
    }
    fclose($sAh);
    return true;
}

function logMsj($sMsj = "No se puede grabar los logs") {
    global $sCRLF;
    $sMsj = "\n/********************\n*" . date("Y-m-d H:i:s") . "\n" . $sMsj . "\n" . $sCRLF . "\n*/\n";
    grabarArchivo( "/usr/u/tmp/log.coc", $sMsj );
}

if ( $argc !== 6 && $argc !== 7 ) {
	//echo Usage();
	exit;
}

//SOLICITUD | INFORMAR | ANULACION
if (  !preg_match( '/\\A(?:\\|(\\d{11})\\|(\\d{11})\\|(\\d{3})\\|(\\d{10})\\|(\\d{15})\\|(\\d{0,11})\\|(\\d{3})\\|)\\z/', trim($argv[5]) ) &&
			!preg_match( '/\\A(?:\\|(\\d{11})\\|(\\d+)\\|(\\w{2})\\|)\\z/', trim($argv[5]) ) &&
			!preg_match( '/\\A(?:\\|(\\d{11})\\|(\\d{11})\\|(\\d{12})\\|)\\z/', trim($argv[5]) )
  ) {
	//echo Usage();
	exit;
}

switch ( strtoupper(trim($argv[4])) ) {
	case "SOLICITUD":
		$vTitles = $vTitlesSolicitud;
		$sAccion = "enviar_consulta";
	break;
	case "INFORMAR":
		$vTitles = $vTitlesInformar;
		$sAccion = "enviar_consulta";
	break;
	case "ANULACION":
		$vTitles = $vTitlesAnulacion;
		$sAccion = "enviar_consulta";
	break;
}


if ($sAccion == "enviar_consulta") {
	$vDataStripped = substr($vData, strpos($vData, "|")+1, strrpos($vData, "|")-1);
	$vTransArgs = preg_split("/\|/", $vDataStripped);
	$vVariables = "?OPERACION=" . trim($argv[4]);
	foreach($vTitles as $iKey => $iValue) {
		$vVariables .= ($iKey < count($vTitles)?"&":"") . $vTitles[$iKey] . "=" . $vTransArgs[$iKey];
	}
	$vVariables .= trim($argv[6]) !== "" ? "&SERVICE=".$argv[6] : "";
	$vHandle = fopen(  ("http".( intval($argv[2])==443?"s":"" )."://".$argv[1].$argv[3].$vVariables), "rb"  );
	$sContents = '';
	while (!feof($vHandle)) {
	 $sContents .= fread($vHandle, 8192);
	}
	fclose($vHandle);
	$vMsgInput = unserialize(  base64_decode( trim($sContents) )  );
	if ( is_array($vMsgInput) && array_key_exists("resultado", $vMsgInput) ) {
		switch(  strtoupper( trim($argv[4]) )  ) {
			case "SOLICITUD":
				$vResponse = processResponseSolicitud($vMsgInput);
				$sAccion = "llamar_cobol";
			break;
			case "INFORMAR":
				$vResponse = processResponseInformar($vMsgInput);
				$sAccion = "llamar_cobol";
			break;
		}
	}
}


if ($sAccion == "llamar_cobol") {
	$vMsg->codReq("TESCAMB0101");
	foreach ($vResponse as $iKey => $vValue) {
		if ($iKey == 0) {
			unset($vArrHeaders, $vArrValues);
			foreach ($vResponse[0] as $sKey => $sValue) {
				$vArrHeaders[] = strtoupper($sKey);
				$vArrValues[] = $sValue;
			}
			$vMsg->grupoNuevo($vArrHeaders);
			$vMsg->valorFilas($vArrValues);
		} else {
			unset($vArrHeaders, $vArrValues);
			foreach ($vResponse[1] as $sKey => $sValue) {
				$vArrHeaders[] = strtoupper($sKey);
				$vArrValues[] = $sValue;
			}
			$vMsg->grupoNuevo($vArrHeaders);
			$vMsg->valorFilas($vArrValues);
		}
	}
	$vCnn->enviar( $vMsg->crearMensaje() );
  logMsj($vMsg->crearMensaje());
  if (!$vRs = Mensaje::desdeCadena($vCnn->respuesta())) {
      exit(is_object($vRs) ? $vRs->getError() : "Error 2: El mensaje esta mal formado");
      return;
  }
  switch ($vRs->getNvlError()) {
	  case "1":
	  case "3":
	      $sMensaje = " Error: ".$vRs->getError();
	      exit($sMensaje);
	      break;
	  case "2":
	      exit(is_object($vRs) ? $vRs->getError() : "Error 2: El mensaje esta mal formado");
	      return;
	      break;
	  case "0":
	      $sMensaje = $sMensaje ? ($sMensaje . "\n".$vRs->getError()) : $vRs->getError();
	      exit($sMensaje);
	      break;
	  default:
	  		//echo "Enviado\n";
				exit(0);
	}
}//Accion==llamar_cobol
?>
