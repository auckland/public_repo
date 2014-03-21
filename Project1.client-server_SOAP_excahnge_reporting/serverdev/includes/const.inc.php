<?php
ini_set("soap.wsdl_cache_enabled", "0");

/*$SERVICE = trim($_GET['SERVICE']) ? strtolower( trim($_GET['SERVICE']) ) : strtolower( trim($_POST['SERVICE']) );
if ($SERVICE !== "wsaaxx" && $SERVICE !== "wsaaxxxx") {
	$SERVICE = "wsaaxxxx";
}*/

define("TMPDIR", 'tmp/');
define("RESDIR", 'result/');
define("CERTDIR", 'cert/');
define("WSDLAUTH", "https://" . $SERVICE . ".afip.gov.ar/ws/services/LoginCms?wsdl");     # The WSDL corresponding to WSAA
define("URLAUTH", "https://" . $SERVICE . ".afip.gov.ar/ws/services/LoginCms");
define("CERT", CERTDIR . "bcoind.crt");       # The X.509 certificate in PEM format
define("PRIVATEKEY", CERTDIR . "clave.key"); # The private key correspoding to CERT (PEM)
define("PASSPHRASE", ""); # The passphrase (if any) to sign
define("PROXY_HOST", ""); # Proxy IP, to reach the Internet //10.1.101.2
define("PROXY_PORT", "");            # Proxy TCP port //3128
define("XMLCN", 'Banco Industrial');
define("XMLO", 'Banco Industrial S.A.');
//Debug
define("LOGXMLS", 1);
define("ARCHIVO_LOG", "soap.log");
$sCRLF = chr(13).chr(10);

if ($SERVICE == "wsaaxxxx") {
	$URL = "https://xxxx.afip.gov.ar/wscoc/COCService";
	$WSDL = "https://xxxx.afip.gov.ar/wscoc/COCService?wsdl";
} else if ($SERVICE == "wsaaxxx") {
	$URL = "https://xxxjava.afip.gob.ar/wscoc2/COCService";
	$WSDL = "https://xxxjava.afip.gob.ar/wscoc2/COCService?wsdl";
}	else {
	unset($SERVICE);
}

if (!file_exists(CERT)) {
	exit("SOAP_ERROR: Fallo la apertura de Certificado (CRT) ".CERT."\n");
}
if (!file_exists(PRIVATEKEY)) {
	exit("SOAP_ERROR: Fallo la apertura de Clave (KEY) ".PRIVATEKEY."\n");
}
//if (!file_exists(WSDL)) {exit("Failed to open ".WSDL."\n");}
//if (!is_readable(WSDL)) {exit("Failed to open ".WSDL."\n");}
if (!$SERVICE) {
	ShowUsage(); 
	exit(1);
}

function ShowUsage() {
  print "SOAP_ERROR:<br>"
      . "Uso  : [URL]/?SERVICE=wsaahomo<br>"
      . "donde: 'service' debe ser el service name del WS de negocio.<br>"
      . "Ej.  : wsaahomo<br>";
}
?>