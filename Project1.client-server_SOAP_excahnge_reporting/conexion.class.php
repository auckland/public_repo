<?php
/**
 * Conexión por socket entre PHP y COBOL
 * @access		public
 * @package		Conexion
 */
class Conexion {
	var $pDireccion;
	var $pPuerto;
	var $pMensaje;
	var $pRespuesta;
	var $pTimeout;
	var $pModo;
	var $pBuffer;
	var $pError;
	var $pFile2log = "/home/www/php-lib/logs/log";

	/**
	 * Constructor
	 * @param     [string]	$mensaje
	 * @param     [string]	$dirección
	 * @param     [integer]	$puerto
	 * @since     1.0
	 * @access    public
	 * @return    void
	 */
	function Conexion() {
		//Inicializo las propiedades
		$this->direccion( "localhost" );
		$this->puerto( 1234 );
		$this->pMensaje="";
		$this->pRespuesta="";
		$this->timeout(30);
		$this->pModo = 0;
		$this->pBuffer = 1024;//128;
		$this->pError = "";

		switch (func_num_args()) {
		case 1:
			$this->mensaje( func_get_arg(0) );
			$this->direccion( "" );
			$this->puerto( "" );
			break;
		case 2:
			$this->mensaje( func_get_arg(0) );
			$this->direccion( func_get_arg(1) );
			$this->puerto( "" );
			break;
		case 3:
			$this->mensaje( func_get_arg(0) );
			$this->direccion( func_get_arg(1) );
			$this->puerto( func_get_arg(2) );
			break;
		default:
			$this->mensaje( "" );
			$this->direccion( "" );
			$this->puerto( "" );
			break;
		}	// Switch
	}

	/**
	 * @param     [string]	$mensaje
	 * @param     [string]	$dirección
	 * @param     [integer]	$puerto
	 * @since     1.0
	 * @access    public
	 * @return    boolean
	 */
	function enviar() {
		$f_sPaquete = "";
		$f_sTmp = "";
		switch (func_num_args()) {
		case 0:
			break;
		case 1:
			$this->mensaje( func_get_arg(0) );
			break;
		case 2:
			$this->mensaje( func_get_arg(0) );
			$this->direccion( func_get_arg(1) );
			break;
		case 3:
			$this->mensaje( func_get_arg(0) );
			$this->direccion( func_get_arg(1) );
			$this->puerto( func_get_arg(2) );
			break;
		default:
			$this->raiseError( -4 , "Exceso de parámetros");
			return false; //Exceso de parámetros
			break;
		}	// Switch

		//Limpio lo que haya en la respuesta
		$this->respuesta("");

		if ($this->mensaje()=="") {
			$this->raiseError( -1 , "No está definido el mensaje.");
			return false;
		}

		if ($this->direccion()=="") {
			$this->raiseError( -2 , "No está definida la direccion");
			return false;
		}

		if ($this->puerto()==-1) {
			$this->raiseError( -3 , "No está definido el puerto");
			return false;
		}

		//Creo el socket TCP/IP
		$f_vFile = fsockopen ($this->direccion(), $this->puerto(), $errno, $errstr, $this->timeout() );
		if (!$f_vFile) {
			$this->raiseError( -6 , "Error de conexión #$errstr#$errno");
			return false;
		} else {
			fputs ($f_vFile,$this->mensaje() );
			socket_set_timeout($f_vFile, $this->timeout() );
			while (!feof($f_vFile)) {
				$f_sPaquete = fgets ($f_vFile,$this->pBuffer);
				if ($f_sPaquete=="") {	// no se leyó nada
					if (!feof($f_vFile)) {	//no es eof
						fclose ($f_vFile);
						$f_sTmp = "<!--".str_replace("\n" , "--><!--" , $f_sTmp)."-->";
						$this->raiseError(-8, "Paquete vacío. Proceso abortado($f_sTmp)");
						return false;
					}
				} else {
					$f_sTmp .= $f_sPaquete;
				}	//if
			}	//while

			//Cierro el socket
			fclose ($f_vFile);
		} //if
		$this->respuesta( $f_sTmp );

		return $this->respuesta(); 
	}	//enviar
	
	function log2File($p_sFile="") {
		$sLogFileName = $this->file2log();
		if (ereg("^[a-zA-Z0-9]+$", $p_sFile)) $sLogFileName .= ".".$p_sFile;
		$fp = fopen($sLogFileName,"a");
		fputs($fp, "/********************\n");
		fputs($fp, "* ".date("Y/m/d H:i:s")."\n");
		fputs($fp, $this->mensaje());
		fputs($fp, "\n-------------------\n");
		fputs($fp, $this->respuesta());
		fputs($fp, "\n*/\n");
	}

	/**
	 * @access    public
	 * @return    [string]
	 */
	function mensaje() {
		switch (func_num_args()) {
		case 1:
			$this->pMensaje=func_get_arg(0);
			break;
		default:
			return $this->pMensaje;
			break;
		} //switch
	}	//mensaje()

	/**
	 * @access		public
	 * @return		[string]
	 */
	function file2log() {
		switch (func_num_args()) {
		case 1:
			$this->pFile2log=func_get_arg(0);
			break;
		default:
			return $this->pFile2log;
			break;
		} //switch
	} // end func

	/**
	 * @access		public
	 * @return		[string]
	 */
	function respuesta() {
		switch (func_num_args()) {
		case 1:
			$this->pRespuesta=func_get_arg(0);
			break;
		default:
			return $this->pRespuesta;
			break;
		} //switch
	} // end func

	/**
	 * @access    public
	 * @return    [string]
	 */
	function direccion() {
		switch (func_num_args()) {
		case 1:
			$this->pDireccion = func_get_arg(0);
			break;
		default:
			return $this->pDireccion;
			break;
		}	//switch
	}	//direccion

	/**
	 * @access		public
	 * @return		[integer]
	 */
	function puerto() {
		switch (func_num_args()) {
		case 1:
			$this->pPuerto=func_get_arg(0);
			break;
		default:
			return $this->pPuerto;
			break;
		}	//switch
	}	//puerto()

	/**
	 * @access		public
	 * @return		integer	Retorna el valor de timeout si no se le enviaron parámetros
	 */
	function timeout( ) {
		switch (func_num_args()) {
		case 1:
			$this->pTimeout=func_get_arg(0);
			break;
		default:
			return $this->pTimeout;
			break;
		}	//switch
	}	//timeout

	function raiseError( $p_iErrorNum , $p_sErrorDesc ) {
		$this->pError = array( $p_iErrorNum => $p_sErrorDesc );
		return;
	} // end func

	/**
	 * @access		public
	 * @return		void
	 * @link		$pError
	 * @link		raiseError
	 * @link		verError
	 */
	function limpiarErrores() {
		$this->pError="";
		return;
	} // end func

	/**
	 * @access		public
	 * @return		array
	 * @link		$pError
	 * @link		limpiarErrores
	 * @link		raiseError
	 */
	function verError() {
		if ($this->pError=="")
			return array( "numero"=>0 , "descripcion"=>"" );
		else
			return array( "numero"=>key($this->pError) , "descripcion"=>$this->pError[key($this->pError)] );
	} // end func
}	//Class
?>
