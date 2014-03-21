<?php
/**
* Class created to establish a socket connection from PHP Back-End. 
* Virtual Authentification to .htaccess generated request included.
* Copyright - Sergio Zalyubovskiy -- 2007/09/10
**/
class SocketConn {
		function SocketConn($sDireccion="", $iPuerto=80, $sRuta="", $sUsuario="", $sPassword="", $iTimeout=30) {
			$this->sDireccion = $sDireccion ;
			$this->iPuerto = $iPuerto;
			$this->sRuta = $sRuta;
			$this->sUsuario = $sUsuario;
			$this->sPassword = $sPassword;
			$this->iTimeout = $iTimeout;
		}
	
		function f_SockConn() {
			switch($this->iPuerto){
				case 443:
					$this->sDireccion = "ssl://".$this->sDireccion;
				break;
				case 636:
					$this->sDireccion = "tls://".$this->sDireccion;
				break;
			}
			$vSocketConn = fsockopen($this->sDireccion, $this->iPuerto, $iErrNo, $sErrStr, $this->iTimeout);
			if ($vSocketConn) {
				socket_set_timeout($vSocketConn, $this->iTimeout);
				if ($this->sRuta) {
					$sSockHeader  = "GET ".$this->sRuta." HTTP/1.0\r\n";
					$sSockHeader .= "Host: ".$this->sDireccion."\r\n";
					$sSockHeader .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.3) Gecko/20060426 Firefox/1.5.0.3\r\n";
					$sSockHeader .= "Accept: */*\r\n";
					$sSockHeader .= "Accept-Language: es-ar,es,en-us,es;q=0.5\r\n";
					$sSockHeader .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
					$sSockHeader .= "Keep-Alive: 300\r\n";
					$sSockHeader .= "Connection: Keep-Alive\r\n";

					if ($this->sUsuario) {
			     	$sSockHeader .= "Authorization: Basic ".base64_encode($this->sUsuario.":".$this->sPassword)."\r\n";
			    }
					$sSockHeader .= "\r\n";
					fputs( $vSocketConn, $sSockHeader, strlen($sSockHeader) );
					$sRespuesta = "";
					while ( !feof($vSocketConn) ) {
						$sRespuesta .= fgets($vSocketConn, 4096);
					}
					if ( substr_count($sRespuesta, "200 OK") > 0 ) {//link status
						fclose($vSocketConn);
							return $sRespuesta;
					} else if (strlen($sRespuesta) < 15) {//la respuesta de error no estandar
			    	fclose($vSocketConn);
			    		return -1;
					} else {//la respuesta de error estandar
						fclose($vSocketConn);
			    	  return substr($sRespuesta, 9, 3);
					}
				}
			} else {
				return 0;
			}
		}
		
		function f_ConexionContenido() {
			$vSocketRes = $this->f_SockConn();
			//strip headers
      $iPos	= strpos($vSocketRes, "\r\n\r\n");
      $vSocketRes = substr($vSocketRes, $iPos + 4);
			return $vSocketRes;
		}
		
		function f_CapturaLinks() {
			$sHtml = $this->f_ConexionContenido();
			$vPattern = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";//por zalyser
			if(preg_match_all("/$vPattern/siU", $sHtml, $vMatch)) {
				# $vMatch[2] = array de urls links
				# $vMatch[3] = array de textos incluyendo HTML
				return $vMatch[2];
			}
		}

}//END: SocketConn

function df_Array( $vArray = array() ) {
	if ( is_array($vArray) && count($vArray) > 0 ) {
		return true;
	} else {
		return false;
	}
}

$sDireccion = "10.100.1.40";//"ssl://", "tls://"
$iPuerto = 80;
$sRuta = "/inhabilitados/bcra/archivos.htm";
$sUsuario = "sergio";
$sPassword = "test";
define("DIRECCION", $sDireccion); 
define("RUTA", $sRuta);
define("PUERTO", $iPuerto);
$iTimeout = 30;

$vSocketObj = new SocketConn(	DIRECCION
				 										, PUERTO
				 										, RUTA
				 										, $sUsuario
				 										, $sPassword
				 										, $iTimeout);
if ( eregi('<([^>]|\n)*>', $vSocketObj->f_ConexionContenido()) ) {//html
	$vLinks = $vSocketObj->f_CapturaLinks();
} else {
	print "Contenido remoto no es HTML.<br>";
}
if ( df_Array($vLinks) ) {
	return true;
} else {
	print "No hay archivos para descargar.<br>";
}
?>