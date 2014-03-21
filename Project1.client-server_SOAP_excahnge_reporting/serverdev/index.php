<?php
session_start();
$sSessId = session_id();

$SERVICE = trim($_GET['SERVICE']) ? strtolower( trim($_GET['SERVICE']) ) : strtolower( trim($_POST['SERVICE']) );
if ($SERVICE !== "wsaa" && $SERVICE !== "wsaahomo") {
	$SERVICE = "wsaahomo";
}

include("./includes/const.inc.php");
include("./wsaa_auth.php");
if ($SERVICE == "wsaahomo") {
	require_once("./classes/coc_srvtest.php");
} else if ($SERVICE == "wsaa") {
	require_once("./classes/coc_srvprod.php");
}
include("./coc_transact.php");

	if(session_id() == "") {
	  session_start();
	}
	switch ( strtoupper($_GET["OPERACION"]) ) {
		case "SOLICITUD":
			$sCuitBanco = $_GET["CUITBANCO"];
			$sCuitComprador = $_GET["CUITCLIENTE"];
			$sCodigoMoneda = $_GET["CODIGOMONEDA"];
			$sCotizacionMoneda = $_GET["COTIZACION"]/1000000;
			$sMontoPesos = $_GET["IMPORTE"]/100;
			$sCuitRepresentante = $_GET["CUITREPRE"];
			$sCodigoDestino = $_GET["CONCEPTO"];
			$vTransactResult = generarSolicitudCompraDivisaCOC($vClient, $sToken, $sSign, $sCuitBanco, 
																			$sCuitComprador, 
																			$sCodigoMoneda, 
																			$sCotizacionMoneda, 
																			$sMontoPesos, 
																			$sCuitRepresentante, 
																			$sCodigoDestino);
			//print_r($vTransactResult);
			//exit;
			$sRetMsg = base64_encode( serialize($vTransactResult) );
			echo $sRetMsg;
		break;
		case "INFORMAR":
			$sCuitBanco = $_GET["CUITBANCO"];
			$sCodigoSolicitud = $_GET["SOLICITUD"];
			$sNuevoEstado = $_GET["ESTADO"];
			$vInformResult = informarSolicitudCompraDivisaCOC($vClient, $sToken, $sSign, $sCuitBanco, 
																			$sCodigoSolicitud, 
																			$sNuevoEstado);
			$sRetMsg = base64_encode( serialize($vInformResult) );
			echo $sRetMsg;
		break;
		case "ANULACION":
			$sCuitBanco = $_GET["CUITBANCO"];
			$sCuitComprador = $_GET["CUITCLIENTE"];
			$sCOC = $_GET["COC"];
			print_r(anularcocCOC($vClient, $sToken, $sSign, $sCuitBanco, 
																			$sCOC, 
																			$sCuitComprador));
		break;
	}

?>
