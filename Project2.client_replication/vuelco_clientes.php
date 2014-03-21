<?php
include("/home/www/php-lib/class/Template.class.php");
define("MAX_CANT_CARACTERES_LINEA", 500);
define("MAX_CANT_CARACTERES_COPY", 100);
define("DEFAULT_TIPOTITULAR", "028");
define("DEFAULT_ACTECON", "000900");
define("ARCHIVO_EXITOS", "/home/www/tmp/clientes__ok.txt");
define("ARCHIVO_ERRORES", "/home/www/tmp/clientes__errores.txt");
define("ARCHIVO_ALERTAS", "/home/www/tmp/clientes__alertas.txt");
define("USUARIO_ALTA", "vuelco");

	class Registro {
		var $_nombre = "";
		var $_tipo = "";
		var $_ancho = 0;
		var $_descripcion = "";
		var $_data = "";
	}

	$vCateImp = array(	"001"	=> "General"
					,	"002"	=> "Preferencial"
					,	"003"	=> "Exento"
					,	"004"	=> "Monotributista"
					);

	$sPathArchivoClientes = "/home/www/tmp";
//	$sArchivoClientes = "CLIVUEL.DAT";
	$sArchivoClientes = "CLIENTES_PRS.TXT";
	$sCopyClientes = "clivuel_pfis.cpy";

	function abrirArchivo(&$vArchivo, $sArchivo) {
		return ($vArchivo = fopen($sArchivo, "r"));
	}

	function cerrarArchivo(&$vArchivo) {
		fclose($vArchivo);
	}

	function okCliente($sCliente, $iNumeroCliente) {
		$sCmd = "echo \"".$sCliente."->".$iNumeroCliente."\">>".ARCHIVO_EXITOS;
		`$sCmd`;
	}

	function alertaCliente($sCliente, $sAlerta) {
		$sCmd = "echo \"".$sCliente."|".$sAlerta."\">>".ARCHIVO_ALERTAS;
		`$sCmd`;
	}

	function errorCliente($sCliente, $sError) {
		$sCmd = "echo \"".$sCliente."|".$sError."\">>".ARCHIVO_ERRORES;
		`$sCmd`;
	}

	function procesarResultado($vResultado, &$sError) {
		$sError = "";
		if ($vResultado[1]=="2|CODIGOALTA|MENSAJE|") {
			ereg("^3\|(.*)\|(.*)\|$", $vResultado[2], $vMatches);
			$sError = $vMatches[1];
			return true;
		} else {
			for ($w=1;$w<sizeof($vResultado)-1;$w++) {
				if (ereg("^8\|(.*)\|([0-9])\|$", $vResultado[$w], $vMatches)) {
					if ($vMatches[2]!="0") {
						$sError .= $vMatches[1]."|";
					}
				} else {
					$sError .= "Mensaje de retorno inválido - ".$vResultado[1]."|";
				}
			}
			return false;
		}
	}

	function parsearCopy(&$vCopy, $sArchivoCopy) {
		$vCopy = array();
		$sCmd = ">".ARCHIVO_ERRORES;
		$sCmdResult = `$sCmd`;
		$sCmd = ">".ARCHIVO_EXITOS;
		$sCmdResult = `$sCmd`;
		if (abrirArchivo($vArchivoCopy, $sArchivoCopy)) {
			while ($sBuffer = fgets($vArchivoCopy, MAX_CANT_CARACTERES_COPY)) {
				if (ereg("^[^0-9]*([0-9]+)\ +([a-z0-9-]+)\ +pic\ ([^.]+).(.+)\*>(.*)$", $sBuffer, $vMatches)) {
					$vReg = new Registro;
					$vReg->_nombre = $vMatches[2];
					$vReg->_tipo = substr($vMatches[3], 0, 1)=="x"?"string":"integer";
					if (ereg("\(([0-9]+)\)", $vMatches[3], $vSubMatches))
						$vReg->_ancho = $vSubMatches[1];
					else
						$vReg->_ancho = strlen(trim($vMatches[3]));
					$vCopy[] = $vReg;
				}
			}
			cerrarArchivo($vArchivoCopy);
			return true;
		} else {
			return false;
		}
	}

	function prepararAlta($vCopy, $vValores, &$sMensajeAlta, &$sError) {
print_r($vValores);
		GLOBAL $vCateImp;
		$bOk = true;
		$vMensaje = new FastTemplate("/home/www/tmp/");
		$vMensaje->define(array("mensaje"=>"alta_cliente.msg"));
		$vMensaje->no_strict();
		foreach ($vCopy as $key=>$vReg) {
			switch ($vReg->_nombre) {
			case "cli-numero":
				$vMensaje->assign("IDCLIENTE", $vValores[$key]);
				break;
			case "cli-cuit":
				$vMensaje->assign("NROCUIT", $vValores[$key]);
				break;
			case "cli-nombre":
				$vMensaje->assign("NOMBRE", trim($vValores[$key]));
				break;
			case "cli-apellido":
				$vMensaje->assign("APELLIDO", trim($vValores[$key]));
				break;
			case "cli-fecnac":
				$vMensaje->assign("FECHANAC", $vValores[$key]);
				break;
			case "cli-tipdoc":
				$vMensaje->assign("TIPODOC", $vValores[$key]);
				break;
			case "cli-nrodoc":
				$vMensaje->assign("NRODOC", $vValores[$key]);
				break;
			case "cli-sexo":
				$vMensaje->assign("SEXO", substr($vValores[$key], 0, 1));
				break;
			case "cli-estciv":
				$vMensaje->assign("ESTADOCIV", $vValores[$key]);
				break;
			case "cli-profe":
				$vMensaje->assign("PROFESION", $vValores[$key]);
				break;
			case "cli-calle":
				$vMensaje->assign("CALLE", $vValores[$key]);
				break;
			case "cli-nropuer":
				$vMensaje->assign("NUMERO", $vValores[$key]);
				break;
			case "cli-piso":
				$vMensaje->assign("PISO", $vValores[$key]);
				break;
			case "cli-depto":
				$vMensaje->assign("DEPTO", $vValores[$key]);
				break;
			case "cli-postal":
				$vMensaje->assign("CODPOST", trim($vValores[$key]));
				break;
			case "cli-local":
				$vMensaje->assign("LOCALIDAD", $vValores[$key]);
				break;
			case "cli-pcia":
				$vMensaje->assign("PROVINCIA", $vValores[$key]);
				break;
			case "cli-telef":
				$vMensaje->assign("TELEFPART", $vValores[$key]);
				break;
			case "cli-nacional":
				$vMensaje->assign("NACIONALIDAD", $vValores[$key]);
				$vMensaje->assign("PAIS", $vValores[$key]);
				break;
			case "cli-pais":
				$vMensaje->assign("PAIS", $vValores[$key]);
				break;
//			case "cli-secfin":
//				$vMensaje->assign("SECTORFIN", $vValores[$key]);
//				break;
//			case "cli-sitlabor":
//				$vMensaje->assign("SITLABORAL", $vValores[$key]);
//				break;
//			case "cli-clasif":
//				$vMensaje->assign("CLASIFICACION", $vValores[$key]);
//				break;
			case "cli-estdeu":
				$vMensaje->assign("ESTADODEUDA", $vValores[$key]);
				break;
			case "cli-25413":
				$bOk = false;
				foreach ($vCateImp as $iCate => $sCate)
					if (strtoupper(trim($sCate))==strtoupper(trim($vValores[$key]))) {
						$vMensaje->assign("CATEIMP", $iCate);
						$bOk = true;
					}
				break;
			case "cli-gananc":
				$vMensaje->assign("CATEGAN", $vValores[$key]);
				break;
			case "cli-iva":
				$vMensaje->assign("TIPOIVA", $vValores[$key]);
				break;
//			case "cli-oficial":
//				$vMensaje->assign("OFIAG", $vValores[$key]);
//				break;
			case "cli-tipope":
				$vMensaje->assign("TIPOPER", $vValores[$key]);
				break;
//			case "cli-respais":
//				$vMensaje->assign("RESIDENCIA", substr($vValores[$key], 0, 1));
//				break;
//			case "cli-impempres":
//				$vMensaje->assign("IMPEMPRE", substr($vValores[$key], 0, 1));
//				break;
//			case "cli-siter":
//				$vMensaje->assign("SITER", substr($vValores[$key], 0, 1));
//				break;
//			case "cli-invcalif":
//				$vMensaje->assign("INVCALIF", substr($vValores[$key], 0, 1));
//				break;
//			case "cli-emple":
//				$vMensaje->assign("EMPLEADOR", substr($vValores[$key], 0, 1));
//				break;
			case "out-tipoviv":
			case "emp-nom":
			case "emp-tipo":
			case "emp-calle":
			case "emp-nro":
			case "emp-piso":
			case "emp-depto":
			case "emp-cod-post":
			case "emp-localidad":
			case "emp-pcia":
			case "emp-telefonos":
			case "emp-legajo":
			case "emp-fecingres":
			case "emp-sueldo":
				break;
			default:
				$bOk = false;
				$sError = "Referencia inválida: ".$vValores[$key];
			}
		}
		$vMensaje->assign("USUARIO_ALTA", USUARIO_ALTA);
		$vMensaje->assign("TIPOTIT", DEFAULT_TIPOTITULAR);
		$vMensaje->assign("ACTECON", DEFAULT_ACTECON);
		$vMensaje->assign("OFIAG", "200");
		$vMensaje->assign("RESIDENCIA", "S");
		$vMensaje->assign("SECTORFIN", "1");
		$vMensaje->assign("CLASIFICACION", "1");
		$vMensaje->assign("SITLABORAL", "1");
		$vMensaje->assign("IMPEMPRE", "N");
		$vMensaje->assign("INVCALIF", "N");
		$vMensaje->assign("SITER", "S");
		$vMensaje->assign("EMPLEADOR", "N");
		$vMensaje->assign("PERFCONS", "00");
		$vMensaje->assign("CATEIMP", "001");
		$vMensaje->assign("CATEGAN", "00");
		if ($bOk) {
			$vMensaje->parse("MAIN", "mensaje");
			$sMensajeAlta = $vMensaje->fetch("MAIN");
			$sMensajeAlta = str_replace("\"", "", $sMensajeAlta);
			$sMensajeAlta = str_replace("`", "", $sMensajeAlta);
			return true;
		} else {
			return false;
		}
	}


//PRINCIPAL
	$iCliente = 0;
	$iClienteDesde = $argv[1];
	if (abrirArchivo($vArchivo, $sPathArchivoClientes."/".$sArchivoClientes)) {
		if (parsearCopy($vCopy, $sPathArchivoClientes."/".$sCopyClientes)) {
			$sPattern = "^";
			foreach ($vCopy as $vReg) $sPattern .= "(.{".$vReg->_ancho."})";
			$sPattern .= "$";
			while ($sBuffer = fgets($vArchivo, MAX_CANT_CARACTERES_LINEA)) {
				$iCliente++;
				if ($iCliente>=$iClienteDesde) {
					unset($vMatches);
					$iAcumuladorAncho = 0;
					foreach ($vCopy as $vReg) {
						$vMatches[] = trim(substr($sBuffer, $iAcumuladorAncho, $vReg->_ancho));
						$iAcumuladorAncho += $vReg->_ancho;
					}
//foreach ($vCopy as $key=>$vReg) $vReg->_data = $vMatches[$key];
					if (prepararAlta($vCopy, $vMatches, $sMensajeAlta, $sError)) {
						$iCUIT = $vMatches[0];
						$sCmd = "echo \"1|CLDOV000101|001|".USUARIO_ALTA."|10.100.1.40|\n2|CUITCUILCDI|CODIGOALTAD|\n3|".$iCUIT."|ACPF|\n".chr(5)."\"|nc localhost 1234 2>&1";
echo $sCmd;
						$sCmdResult = `$sCmd`;
echo $sCmdResult;
						$vArr = explode(chr(10), $sCmdResult);
						if (sizeof($vArr)==4) {
							$vSubArr = explode("|", $vArr[2]);
							if (sizeof($vSubArr)==3) {
								if ($vSubArr[1]=="") {
									$sCmd = "echo \"".$sMensajeAlta."\"|nc localhost 1234 2>&1";
						$sCmdResult = `$sCmd`;
									$vResultado = explode("\n", $sCmdResult);
echo "\n";
print_r($vResultado);
									if (procesarResultado($vResultado, $sError)) {
//Sin Error
										okCliente($iCliente, $sError."(".$iCUIT.")");
									} else {
//Hubo Errores
										errorCliente($iCliente, $sError."(".$iCUIT.")");
									}
								} else {
									alertaCliente($iCliente, "Ya existia un cliente con ese CUIT|(".$iCUIT.")");
								}
							} else {
								errorCliente($iCliente, "Error al consultar el CUIT|(".$iCUIT.")");
							}
						} else {
							errorCliente($iCliente, "Error al consultar el CUIT|(".$iCUIT.")");
						}
					} else {
						errorCliente($iCliente, $sError."(".$iCUIT.")");
					}
				}
			}
			cerrarArchivo($vArchivo);
		} else {
echo "ERROR copy inválido";
		}
	} else {
echo "ERROR al abrir archivo";
	}



?>
