<?php
/* 
*	Author: Zalyubovskiy (C) 2012
**/
$vDatos = CheckAuth();
$sSign = $vDatos[0];
$sToken = $vDatos[1];


$vClient = new soapClient($WSDL,
  array('soap_version' => SOAP_1_1,
        'location'     => $URL,
        'proxy_host'   => PROXY_HOST,
        'proxy_port'   => PROXY_PORT,
        'exceptions'   => 0,
        'features'     => SOAP_USE_XSI_ARRAY_TYPE + SOAP_SINGLE_ELEMENT_ARRAYS,
        'trace'        => 1));

function objectToArray($object) {
	if( !is_object($object) && !is_array($object) ) {
		return $object;
	}
	if( is_object($object) ) {
		$object = get_object_vars($object);
	}
	return array_map('objectToArray', $object);
}


function flatten_array($array, $preserve_keys = 0, &$out = array()) {
    foreach($array as $key => $child)
        if(is_array($child))
            $out = flatten_array($child, $preserve_keys, $out);
        elseif($preserve_keys + is_string($key) > 1)
            $out[$key] = $child;
        else
            $out[] = $child;
    return $out;
}


function LogXMLs ($client) {
	global $sSessId;
	if (LOGXMLS) {
    file_put_contents(TMPDIR . $sSessId . "_Request.xml", $client->__getLastRequest());
    file_put_contents(TMPDIR . $sSessId . "_Response.xml", $client->__getLastResponse());
  }
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


function logSOAP($sMsj = "No se puede grabar los logs") {
    global $sCRLF;
    $sMsj = "\n/********************\n*" . date("Y-m-d H:i:s") . "=" . $sMsj . "\n" . $sCRLF . "\n*/\n";
    grabarArchivo( realpath("logs") . "/" . ARCHIVO_LOG, $sMsj );
}


function CheckErrors($vResults, $method) {
	global $sCRLF;
  if (is_soap_fault($vResults)) {
  	$sResultadoTxt = "|";
  	if ( is_array(objectToArray($vResults)) ) {
  		$vResultsFlat = flatten_array( objectToArray($vResults) );
  		foreach($vResultsFlat as $sKey => $sValue) {
  			$sResultadoTxt .= "$sKey:$sValue;";
  		}
  		$sResultadoTxt .= "|\n";
  	}
  	$sSOAPmsg = sprintf("SOAP_ERROR: %s\nFaultString: %s\n", $vResults->faultcode, $vResults->faultstring);
  	logSOAP($sResultadoTxt . $sSOAPmsg);
  	echo $sSOAPmsg;
    exit(1);
  }
}


function dummy($vClient) {
  $vResults = $vClient->dummy();
  LogXMLs($vClient);
  CheckErrors($vClient, 'dummy');
  printf("Appserver: %s\nDbserver: %s\nAuthserver: %s\n",
    $vResults->dummyReturn->appserver,
    $vResults->dummyReturn->dbserver,
    $vResults->dummyReturn->authserver);
}
////dummy($vClient);


function consultarMonedasCOC($vClient, $sToken, $sSign, $sCuit) {
  $vResults = $vClient->consultarMonedas(
		array('authRequest'=>array('token' => $sToken,
		                       'sign' => $sSign,
		                       'cuitRepresentada' => $sCuit)));
    CheckErrors($vResults, 'consultarMonedasCOC', $vClient);
    LogXMLs($vClient);
    $vRetMonedas = $vResults->consultarMonedasReturn->arrayMonedas->codigoDescripcion;
		if ( is_array($vRetMonedas) ) {
			foreach($vRetMonedas as $vMonedasObj) {
				$vListadoMonedas[$vMonedasObj->codigo] = $vMonedasObj->descripcion;
			}
		}
		if ( is_array($vListadoMonedas) && count($vListadoMonedas) ) {
			ksort($vListadoMonedas);
			return $vListadoMonedas;
		}
}

function consultarCuitCOC($vClient, $sToken, $sSign, $sCuit, $sTipoDoc, $iNroDoc) {
  $vResults = $vClient->consultarCUIT(
		array(
					'authRequest'=>array('token' => $sToken,
		      		                 'sign' => $sSign,
		          		             'cuitRepresentada' => $sCuit),
					'tipoNumeroDoc'=>array('tipoDoc' => $sTipoDoc,
		              			         'numeroDoc' => $iNroDoc)
		      )	);
    CheckErrors($vResults, 'consultarCuitCOC', $vClient);
    LogXMLs($vClient);
    $vConsCuitRet = $vResults->consultarCUITReturn->tipoNumeroDoc;
    $vConsCuitDetRet = $vResults->consultarCUITReturn->arrayDetallesCUIT->detalleCUIT;
    $vConsCuitErrRet = $vResults->consultarCUITReturn->arrayErrores->codigoDescripcion;    
    if ( is_object($vConsCuitRet) ) {
			foreach($vConsCuitRet as $vConsCuitObjKey => $vConsCuitObjVal) {
				//tipoDoc, numeroDoc
				$vListadoCuitRet[$vConsCuitObjKey] = $vConsCuitObjVal;
			}
    }
    if ( is_object($vConsCuitDetRet) ) {
			foreach($vConsCuitDetRet as $vConsCuitDetObjKey => $vConsCuitDetObjVal) {
				//cuit, denominacion
				$vListadoCuitDetRet[$vConsCuitDetObjKey] = $vConsCuitDetObjVal;
			}
    } else if ( is_array($vConsCuitDetRet) ) {//Array (consulta multiple)
			foreach($vConsCuitDetRet as $vConsCuitDetObj) {
				//cuit, denominacion
				$vListadoCuitDetRet[$vConsCuitDetObj->cuit] = $vConsCuitDetObj->denominacion;
			}
    }
    if ( is_object($vConsCuitErrRet) ) {
    	$vListadoCuitDetRet[$vConsCuitErrRet->codigo] = $vConsCuitErrRet->descripcion;
			foreach($vConsCuitErrRet as $vConsCuitErrObjKey => $vConsCuitErrObjVal) {
				//codigo, descripcion
				$vListadoCuitErrRet[$vConsCuitErrObjKey] = $vConsCuitErrObjVal;
			}
    }
		if ( is_array($vListadoCuitRet) && count($vListadoCuitRet) ) {
			if ( is_array($vListadoCuitDetRet) && count($vListadoCuitDetRet) ) {
				$vListadoCuitRet = array_merge($vListadoCuitRet, array('detalleCUIT' => $vListadoCuitDetRet));
			}
			if ( is_array($vListadoCuitErrRet) && count($vListadoCuitErrRet) ) {
				$vListadoCuitRet = array_merge($vListadoCuitRet, array('arrayErrores' => $vListadoCuitErrRet));
			}
			//ksort($vListadoCuitRet);
			return $vListadoCuitRet;
		}
}


function consultarTiposDocumentoCOC($vClient, $sToken, $sSign, $sCuit) {
  $vResults = $vClient->consultarTiposDocumento(
		array('authRequest'=>array('token' => $sToken,
		                       'sign' => $sSign,
		                       'cuitRepresentada' => $sCuit)));
    CheckErrors($vResults, 'consultarTiposDocumentoCOC', $vClient);
    LogXMLs($vClient);
    $vRetTiposDocumento = $vResults->consultarTiposDocumentoReturn->arrayTiposDocumento->codigoDescripcion;
		if ( is_array($vRetTiposDocumento) ) {
			foreach($vRetTiposDocumento as $vDocumentosObj) {
				$vListadoTiposDocumento[$vDocumentosObj->codigo] = $vDocumentosObj->descripcion;
			}
		}
		if ( is_array($vListadoTiposDocumento) && count($vListadoTiposDocumento) ) {
			ksort($vListadoTiposDocumento);
			return $vListadoTiposDocumento;
		}
}


function consultarDestinosCompraSimpleCOC($vClient, $sToken, $sSign, $sCuit) {
  $vResults = $vClient->consultarDestinosCompra(
		array('authRequest'=>array('token' => $sToken,
		                       'sign' => $sSign,
		                       'cuitRepresentada' => $sCuit)));
    CheckErrors($vResults, 'consultarDestinosCompraSimpleCOC', $vClient);
    LogXMLs($vClient);
    $vRetDestinosCompraSimple = $vResults->consultarDestinosCompraReturn->arrayDestinos->destinos;
		if ( is_array($vRetDestinosCompraSimple) ) {
			foreach($vRetDestinosCompraSimple as $vDestinosSimpleObj) {
				//$vListadoDestinosSimple[$vDestinosSimpleObj->tipoDestino] = $vDestinosSimpleObj->tipoDestino;
				if ( is_array($vDestinosSimpleObj->arrayCodigosDescripciones->codigoDescripcion) ) {
						foreach($vDestinosSimpleObj->arrayCodigosDescripciones->codigoDescripcion as $vDestinosDef) {
								$vListadoDestinosSimple[$vDestinosSimpleObj->tipoDestino][$vDestinosDef->codigo] = $vDestinosDef->descripcion;
						}
				}
			}
		}
		if ( is_array($vListadoDestinosSimple) && count($vListadoDestinosSimple) ) {
			return $vListadoDestinosSimple;
		}
}

function consultarDestinosCompraSimpleDJAICOC($vClient, $sToken, $sSign, $sCuit) {
  $vResults = $vClient->consultarDestinosCompraDJAI(
		array('authRequest'=>array('token' => $sToken,
		                       'sign' => $sSign,
		                       'cuitRepresentada' => $sCuit)));
    CheckErrors($vResults, 'consultarDestinosCompraSimpleDJAICOC', $vClient);
    LogXMLs($vClient);
    $vRetDestinosCompraSimpleDJAI = $vResults->consultarDestinosCompraDJAIReturn->arrayCodigosDescripciones->codigoDescripcion;
		if ( is_array($vRetDestinosCompraSimpleDJAI) ) {
			foreach($vRetDestinosCompraSimpleDJAI as $vDestinosSimpleDJAIObj) {
				$vListadoDestinosSimpleDJAI[$vDestinosSimpleDJAIObj->codigo] = $vDestinosSimpleDJAIObj->descripcion;
			}
		}
		if ( is_array($vListadoDestinosSimpleDJAI) && count($vListadoDestinosSimpleDJAI) ) {
			return $vListadoDestinosSimpleDJAI;
		}
}

function generarSolicitudCompraDivisaCOC($vClient, $sToken, $sSign, $sCuit, 
                                         $cuitComprador, 
                                         $codigoMoneda, 
                                         $cotizacionMoneda, 
                                         $montoPesos, 
                                         $cuitRepresentante, 
                                         $codigoDestino) {
	  if ( intval($cuitRepresentante) > 0 ) {
		  $vResults = $vClient->generarSolicitudCompraDivisa(
				array(
							'authRequest'=>array('token' => $sToken,
			  	    		                 'sign' => $sSign,
			   		       		             'cuitRepresentada' => $sCuit),
			      	    		             'cuitComprador' => $cuitComprador,
			        	  		             'codigoMoneda' => $codigoMoneda,
			          			             'cotizacionMoneda' => $cotizacionMoneda,
			          			             'montoPesos' => $montoPesos,
			          			             'cuitRepresentante' => $cuitRepresentante,
			          		  	           'codigoDestino' => $codigoDestino
			      )	);
		} else {
		  $vResults = $vClient->generarSolicitudCompraDivisa(
				array(
							'authRequest'=>array('token' => $sToken,
			  	    		                 'sign' => $sSign,
			   		       		             'cuitRepresentada' => $sCuit),
			      	    		             'cuitComprador' => $cuitComprador,
			        	  		             'codigoMoneda' => $codigoMoneda,
			          			             'cotizacionMoneda' => $cotizacionMoneda,
			          			             'montoPesos' => $montoPesos,
			          		  	           'codigoDestino' => $codigoDestino
			      )	);
		}
    CheckErrors($vResults, 'generarSolicitudCompraDivisaCOC', $vClient);
    LogXMLs($vClient);
    $vRetSolicitudCompraDivisaReturn = objectToArray($vResults->generarSolicitudCompraDivisaReturn);
    $vRetSolicitudCompraDivisaFlat = flatten_array($vRetSolicitudCompraDivisaReturn, 1);
    switch ( strtoupper($vRetSolicitudCompraDivisaFlat["resultado"]) ) {
			case "A":
				$vArrRetAcceptData = array("resultado", "codigoSolicitud", "coc", "estadoSolicitud", "cuit");
				foreach ($vArrRetAcceptData as $sId) {
					$vListadoSolicitudCompraDivisa[$sId] = $vRetSolicitudCompraDivisaFlat[$sId];
				}
			break;
			case "O":
				$vArrRetObservData = array("resultado", "codigoSolicitud", "estadoSolicitud", "cuit", "codigo", "descripcion");
				foreach ($vArrRetObservData as $sId) {
					$vListadoSolicitudCompraDivisa[$sId] = $vRetSolicitudCompraDivisaFlat[$sId];
				}
			break;
    	case "E":
    	case "R":
    	default:
    		$vArrRetErrData = array("resultado", "codigo", "descripcion", "cuit");
				if ( array_key_exists("arrayErrores", $vRetSolicitudCompraDivisaReturn) ) {
					foreach ($vArrRetErrData as $sId) {
						if ($sId == "cuit") {
							$vListadoSolicitudCompraDivisa[$sId] = $cuitComprador;
						} else {
							$vListadoSolicitudCompraDivisa[$sId] = $vRetSolicitudCompraDivisaFlat[$sId];
						}
					}
				} else if ( array_key_exists("arrayErroresFormato", $vRetSolicitudCompraDivisaReturn) ) {
					foreach ($vArrRetErrData as $sId) {
						if ($sId == "cuit") {
							$vListadoSolicitudCompraDivisa[$sId] = $cuitComprador;
						} else {
							$vListadoSolicitudCompraDivisa[$sId] = $vRetSolicitudCompraDivisaFlat[$sId];
						}
					}
				} else if ( array_key_exists("arrayInconsistencias", $vRetSolicitudCompraDivisaReturn["detalleSolicitud"]) ) {
					foreach ($vArrRetErrData as $sId) {
						if ($sId == "cuit") {
							$vListadoSolicitudCompraDivisa[$sId] = $cuitComprador;
						} else {
							$vListadoSolicitudCompraDivisa[$sId] = $vRetSolicitudCompraDivisaFlat[$sId];
						}
					}
				} else {
					$vListadoSolicitudCompraDivisa["resultado"] = $vRetSolicitudCompraDivisaFlat["resultado"];
					$vListadoSolicitudCompraDivisa["cuit"] = $cuitComprador;
					$vListadoSolicitudCompraDivisa["codigo"] = "0";
					$vListadoSolicitudCompraDivisa["descripcion"] = "Error desconocido";
				}
  	}  	
  	if ( is_array($vListadoSolicitudCompraDivisa) && count($vListadoSolicitudCompraDivisa) ) {
  		return $vListadoSolicitudCompraDivisa;
  	}
}

//CO: Confirmado DC: Denegado por el Cliente DB: Denegado por la entidad
function informarSolicitudCompraDivisaCOC($vClient, $sToken, $sSign, $sCuit, $sCodigoSolicitud, $sNuevoEstado) {
	  $vResults = $vClient->informarSolicitudCompraDivisa(
			array(
						'authRequest'=>array('token' => $sToken,
		  	    		                 'sign' => $sSign,
		   		       		             'cuitRepresentada' => $sCuit),
																 'codigoSolicitud' => $sCodigoSolicitud,
			              			       'nuevoEstado' => $sNuevoEstado
		      )	);
    CheckErrors($vResults, 'informarSolicitudCompraDivisaCOC', $vClient);
    LogXMLs($vClient);
		$vRetInformarSolicitudCompraDivisaReturn = objectToArray($vResults->informarSolicitudCompraDivisaReturn);
		$vRetInformarSolicitudCompraDivisaFlat = flatten_array($vRetInformarSolicitudCompraDivisaReturn, 1);
    switch ( strtoupper($vRetInformarSolicitudCompraDivisaFlat["resultado"]) ) {
			case "A":
				$vArrRetAcceptData = array("resultado", "codigoSolicitud", "estadoSolicitud");
				foreach ($vArrRetAcceptData as $sId) {
					$vListadoInformarCompraDivisa[$sId] = $vRetInformarSolicitudCompraDivisaFlat[$sId];
				}
			break;
    	case "E":
    	case "R":
    	default:
    		$vArrRetErrData = array("codigoSolicitud", "resultado", "codigo", "descripcion");
    		if ( array_key_exists("arrayErrores", $vRetInformarSolicitudCompraDivisaReturn) ) {
					foreach ($vArrRetErrData as $sId) {
						$vListadoInformarCompraDivisa[$sId] = $vRetInformarSolicitudCompraDivisaFlat[$sId];
					}
				} else if ( array_key_exists("arrayErroresFormato", $vRetInformarSolicitudCompraDivisaReturn) ) {
					foreach ($vArrRetErrData as $sId) {
						$vListadoInformarCompraDivisa[$sId] = $vRetInformarSolicitudCompraDivisaFlat[$sId];
					}
				} else {
					$vListadoInformarCompraDivisa["codigoSolicitud"] = $vRetInformarSolicitudCompraDivisaFlat["codigoSolicitud"];
					$vListadoInformarCompraDivisa["resultado"] = $vRetInformarSolicitudCompraDivisaFlat["resultado"];
					$vListadoInformarCompraDivisa["codigo"] = "0";
					$vListadoInformarCompraDivisa["descripcion"] = "Error desconocido";
				}
  	}  	
  	if ( is_array($vListadoInformarCompraDivisa) && count($vListadoInformarCompraDivisa) ) {
  		return $vListadoInformarCompraDivisa;
  	}
}

function anularcocCOC($vClient, $sToken, $sSign, $sCuit, $sCOC, $cuitComprador, $tndTurExtComprador="") {
	  $vResults = $vClient->anularCOC(
			array(
						'authRequest'=>array('token' => $sToken,
		  	    		                 'sign' => $sSign,
		   		       		             'cuitRepresentada' => $sCuit),
																 'coc' => $sCOC,
			              			       'cuitComprador' => $cuitComprador
		      )	);
    CheckErrors($vResults, 'anularcocCOC', $vClient);
    LogXMLs($vClient);
	return $vResults;
}
?>