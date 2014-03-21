<?php
#/usr/bin/php
#==============================================================================#
# Author: Zalyubovskiy (C) 2012                                     -----------#
# Input:-----------------------------------------------------------------------#
#        WSDLAUTH, CERT, PRIVATEKEY, PASSPHRASE, SERVICE, URLAUTH--------------#
# Output:----------------------------------------------------------------------#
#        ./result/[UNIX_TIME].xml: the authorization ticket by WSAA.           #
#==============================================================================#
require_once("./classes/cls_recdir.php");
//Standalone x Sergio
//require_once("./includes/const.inc.php");

session_start();
$sSessId = session_id();

#==============================================================================
#Sergio comment:
#Campo opcional. Indica el DN del WSAA, En caso de utilizarse, deber  ser
#"cn=wsaa,o=afip,c=ar,serialNumber=CUIT xxxxx" para el ambiente de producci¢n y
#"cn=wsaahomo,o=afip,c=ar,serialNumber=CUIT xxxxx" para el ambiente de
#homologaci¢n.
#==============================================================================  
function CreateTRA($SERVICE) {
  global $sSessId;
  $TRA = new SimpleXMLElement(
    '<?xml version="1.0" encoding="UTF-8"?>' .
    '<loginTicketRequest version="1.0">'.
    '</loginTicketRequest>');
  $TRA->addChild('header');
  $TRA->header->addChild('source', 'C=AR, O='.XMLO.', SERIALNUMBER=CUIT 30xxxxxxxx59, CN='.XMLCN);
  $TRA->header->addChild('destination', 'CN='.$SERVICE.', O=AFIP, C=AR, SERIALNUMBER=CUIT 30xxxxxxxx59');
  $TRA->header->addChild('uniqueId', date('U'));
  $TRA->header->addChild('generationTime', date('c',date('U')-60));
  $TRA->header->addChild('expirationTime', date('c',date('U')+60));
  $TRA->addChild('service', 'wscoc');
  $TRA->asXML(TMPDIR . $sSessId . '_TRA.xml');
}

#==============================================================================
# This functions makes the PKCS#7 signature using TRA as input file, CERT and
# PRIVATEKEY to sign. Generates an intermediate file and finally trims the 
# MIME heading leaving the final CMS required by WSAA.
function SignTRA() {
  global $sSessId;
  $STATUS=openssl_pkcs7_sign(TMPDIR . $sSessId . "_TRA.xml", TMPDIR . $sSessId . "_TRA.tmp", "file://".CERT,
    array("file://".PRIVATEKEY, PASSPHRASE),
    array(),
    !PKCS7_DETACHED
    );
  if (!$STATUS) {
  	exit("SOAP_ERROR: ERROR al generar la PKCS#7 signatura\n");
  }
  $inf=fopen(TMPDIR . $sSessId . "_TRA.tmp", "r");
  $i=0;
  $CMS="";
  while (!feof($inf)) {
     $buffer=fgets($inf);
     if ( $i++ >= 4 ) {$CMS.=$buffer;}
  }
  fclose($inf);
  unlink(TMPDIR . $sSessId . "_TRA.xml");
  unlink(TMPDIR . $sSessId . "_TRA.tmp");
  return $CMS;
}

function CallWSAA($CMS) {
  global $sSessId;
  $client=new SoapClient(WSDLAUTH, array(
          'proxy_host'     => PROXY_HOST,
          'proxy_port'     => PROXY_PORT,
          'soap_version'   => SOAP_1_2,
          'location'       => URLAUTH,
          'trace'          => 1,
          'exceptions'     => 0
          )); 
  $results=$client->loginCms(array('in0'=>$CMS));
  $sClientLastReq = $client->__getLastRequest();
  $sClientLastResp = $client->__getLastResponse();
  if (is_soap_fault($results)) {
	  exit("SOAP_ERROR: ".$results->faultcode."\n".$results->faultstring."\n");
  }
  return $results->loginCmsReturn;
}

function StartAuth () {
  global $SERVICE;

  CreateTRA($SERVICE);
  $CMS=SignTRA();
  $TA=CallWSAA($CMS);
  #Zalyser:
  #Extraemos SIGN y TOKEN:
  $xmlObject = new SimpleXMLElement($TA);
  $sSign = $xmlObject->children()->credentials->sign;
  $sToken = $xmlObject->children()->credentials->token;
  $iUnixFromTime = strtotime($xmlObject->children()->header->generationTime);
  $iUnixToTime = strtotime($xmlObject->children()->header->expirationTime);
  if (!file_put_contents(RESDIR . $iUnixToTime . ".xml", $TA)) {
    return array("SOAP_ERROR: Fallo la escritura de TA - " . $iUnixToTime);
  } else {
    return array(1, $sSign, $sToken, realpath("./") . "/" . RESDIR . $iUnixToTime . ".xml");
  }
}

function GetLastAuthFile() {
  $iFilesLastVal = 0;
  $vRecDirXML = new RecursiveDir;
  $vRecDirXML->g_sRootDir = realpath(getcwd()) . "/" . RESDIR;
  $vRecDirXML->g_sExt = "xml|XML";//solo pipe
  //Traemos los archivos en la carpeta
  $vFilesListXML = $vRecDirXML->f_ReturnVals();
  $vFilesListXMLReb = array();
  if ( is_array($vFilesListXML) && count($vFilesListXML) ) {
    foreach ($vFilesListXML as $sFileXML) {
      $vFilesListXMLReb[] = substr(basename($sFileXML), 0, strlen(basename($sFileXML))-4 );
    }
  }
  sort($vFilesListXMLReb, SORT_NUMERIC);
  if ( is_array($vFilesListXMLReb) && count($vFilesListXMLReb) ) {
    $iFilesLastVal = $vFilesListXMLReb[count($vFilesListXMLReb)-1];
  }
  if ( strlen($iFilesLastVal) > 1 ) {
    return $iFilesLastVal; 
  } else {
    return 0;
  }
}

/*
*
* Llamado desde CheckAuth()
* input: nombre del archivo XML
*
**/
function GetXMLData($sXMLFile) {
	if ( !is_file($sXMLFile) ) {
		exit("SOAP_ERROR: Fallo apertura de XML autorizado");
	}
  $sXMLhandle = fopen($sXMLFile, "rb");
  $sXMLcontent = '';
  while (!feof($sXMLhandle)) {
   $sXMLcontent .= fread($sXMLhandle, 8192);
  }
  fclose($sXMLhandle);
  return $sXMLcontent;
}


/*
* Devuelve: Sign, Token, TA
*
**/
function CheckAuth() {
  $iFilesLastVal = GetLastAuthFile();
  $iMaxTime = mktime(date("H"), date("i")+1, date("s"), date("m"), date("d"), date("Y"));
  $vRecDirXML = new RecursiveDir;
  $vRecDirXML->g_sRootDir = realpath(getcwd()) . "/" . RESDIR;
  $vRecDirXML->g_sExt = "xml|XML";//separado por pipes
  $vFilesListXML = $vRecDirXML->f_ReturnVals();
  if ($iFilesLastVal <= $iMaxTime) {//Generamos una nueva Authorizacion
    if ( is_array($vFilesListXML) && count($vFilesListXML) ) {
      foreach ($vFilesListXML as $sFileXML) {
        @unlink($sFileXML);
      }
    }
    $vAuthRet = StartAuth();
    if ($vAuthRet[0] !== 1) {
    	exit($vAuthRet[0]);
    } else {
    	//Devolvemos Sign y el Token
    	return array($vAuthRet[1], $vAuthRet[2], $vAuthRet[3]);
    }
  } else {//Levantamos autorizaciones existentes
     if ( is_array($vFilesListXML) && count($vFilesListXML) ) {
       foreach ($vFilesListXML as $sFileXML) {
         $vFilesListXMLReb[substr(basename($sFileXML), 0, strlen(basename($sFileXML))-4 )] = $sFileXML;
       }
       ksort($vFilesListXMLReb);
       $xmlObject = new SimpleXMLElement( GetXMLData( end($vFilesListXMLReb) ) );
       $sSign = $xmlObject->children()->credentials->sign;
       $sToken = $xmlObject->children()->credentials->token;
       //Devolvemos Sign y el Token
       return array($sSign, $sToken, end($vFilesListXMLReb));
     } else {
       return "SOAP_ERROR: Fallo la lectura de TA";
     }    
  }
}
?>
