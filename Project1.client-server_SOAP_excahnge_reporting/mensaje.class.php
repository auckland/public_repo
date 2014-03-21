<?php
/**
 * Utilizado para crear, enviar y recibir mensajes.
 *
 * Utilizada para crear los mensajes que luego serán
 * serán enviados a Cobol mediante TCP/IP.
 *
 * Versiones Anteriores:
 * 1.0.3 : Modificado el protocolo para soportar Mensajes con más de 1
 * 		   linea tipo 8.
 * 1.0.2 : Agregado el método reset() para reiniciar el Mensaje.
 * 1.0.1 : Corregida regexLinea para que pueda ser procesada por PHPDOC
 * 1.0 : Primera Versión
 * @author			ZS
 * @version			1.0.3
 * @access			public
 * @package			Conexion
 */
Class Mensaje {
	var $cabecera;
	var $grupos;
	var $grupoIndex = 0;
    var $codReq;
    var $codSuc;
	/**
     * @access    privado
     * @link      idUsuario
    */
    var $idUsuario;
    var $ipAddress;
    var $flagDebug = false;
	/**
     * @access    privado
     * @link	  getError
    */
    var $error;
    var $nroError;
    var $SD = "|";
    /**
     * @access    privado
    */
    var $NL = "\n";
    var $FM;
    /**
     * @access    privado
    */
    var $caractValidos;
    /**
     * @access    privado
    */
    var $regexCabecera;
    /**
     * @access    privado
    */
    var $regexLinea;

    /**
     * @access    publico
     * @return    void
     */
    function Mensaje() {
        global $REMOTE_ADDR;

        $this->ipAddress($REMOTE_ADDR);
		$this->FM = chr(5);
        $this->regexCabecera = "^1(8+|(2(3)+)+)9?".$this->FM."$";
		$this->regexLinea = "^\\$this->SD((.)*\\$this->SD)+$";
    	$this->caractValidos = array(1,2,3,8,9,chr(5));
        return;
    } //Function

    /**
     * @access    publico
     * @return    string
     * @link $error
     */
    function getError() {

        return $this->error;

    } //Function

    /**
     * @access    publico
     * @return    string
     * @link $nroError
     */
    function getNvlError() {

        return $this->nvlError;

    } //Function

    /**
     * @access    publico
     * @return    [void][string]
     */
    function codReq() {

        switch (func_num_args()) {
            case 0:
                return $this->codReq;
                break;
            default:
                $this->codReq = func_get_arg(0);
                $this->cabecera[0] = &$this->codReq;
                break;
        }// Fin Switch
    } //Function

    /**
     * @access    publico
     * @return    [void][string]
     */
    function codSuc() {
        switch (func_num_args()) {
            case 0:
                return $this->codSuc;
                break;
            default:
                $this->codSuc = func_get_arg(0);
                $this->cabecera[1] = &$this->codSuc;
                break;
        }// Fin Switch
    } //Function

    /**
     * @access    publico
     * @return    [void][string]
     */
    function idUsuario() {
        switch (func_num_args()) {
            case 0:
                return $this->idUsuario;
                break;
            default:
                $this->idUsuario = func_get_arg(0);
                $this->cabecera[2] = &$this->idUsuario;
                break;
        }// Fin Switch
    } //Function

    /**
     * @access    publico
     * @return    [void][string]
     */
    function ipAddress() {
        switch (func_num_args()) {
            case 0:
                return $this->idUsuario;
                break;
            default:
                $this->ipAddress = func_get_arg(0);
                $this->cabecera[3] = &$this->ipAddress;
                break;
        }// Fin Switch
    } //Function

    /**
     * @access    publico
     * @return    [void]
     * @link unsetDebug
     */
    function setDebug() {

        $this->flagDebug = true;

    } //Function

    /**
     * @access    publico
     * @return    [void]
     */
    function unsetDebug() {

        $this->flagDebug = false;

    } //Function

    /**
     * @param	  [void][string] $SD
     * @access    publico
     * @return    [void][string]
     * @link $SD
     */
    function SD() {
        switch (func_num_args()) {
            case 0:
                return $this->SD;
                break;
            default:
                $this->SD = func_get_arg(0);
                break;
        }//Switch
    } //Function

    /**
     * @param	  [void][string] $NL
     * @access    publico
     * @return    [void][string]
     * @link 	  $NL
     */
    function NL() {
        switch (func_num_args()) {
            case 0:
                return $this->NL;
                break;
            default:
                $this->NL = func_get_arg(0);
                break;
        }//Switch
    } //Function

    /**
     * @param	  [void][string] $FM
     * @access    publico
     * @return    [void][string]
     * @see		  $FM
     */
    function FM() {
        switch (func_num_args()) {
            case 0:
                return $this->FM;
                break;
            default:
                $this->FM = func_get_arg(0);
                break;
        }// Fin Switch
    } //Function

    /**
     * @access    publico
     * @return    [void]
     * @link 	  proximoGrupo
     */
    function resetIndice() {
        $this->grupoIndex = 0;
        return;
    } //Function

    /**
     * @access    publico
     * @return    [void]
     */
    function reset() {
        $this->grupos = array();
		$this->resetIndice();
        return;
    } //Function

    /**
     * @access    publico
     * @return    [void]
     * @link 	  $grupoIndex
     */
    function grupoNuevo() {

        if (func_num_args() <= 0) {
            $this->error = "Es necesario indicar nombres de columnas de datos.";
            return false;
        }
        if(!is_array(func_get_arg(0)))
            $f_vArgs = func_get_args();
        else
            $f_vArgs = func_get_arg(0);
        $f_vGrupo = new Grupo;
        for ($ii=0; $ii<count($f_vArgs); $ii++)
            $f_vGrupo->ligar($ii, $f_vArgs[$ii]);
        $this->grupos[$this->grupoIndex++] = $f_vGrupo;

        return true;
    } //Function

    /**
     * @param	  Array $Valores
     * @since     1.0
     * @access    publico
     * @return    [void]
     */
    function valorFilas() {
        if (func_num_args() <= 0) {
            $this->error = "Es necesario indicar datos para las columnas.";
            return false;
        }
        if(!is_array(func_get_arg(0)))
            $f_vArgs = func_get_args();
        else
            $f_vArgs = func_get_arg(0);
        $f_vGrupo = &$this->grupos[$this->grupoIndex - 1];
        for ($jj = 0; $jj < $f_vGrupo->columnas(); $jj++)
            $f_vGrupo->insertar($jj, $f_vArgs[$jj]);

        return;
    } //Function

    /**
     * @access    publico
     * @return    array
     */
    function grupos() {

        return $this->grupos;

    } //Function

    /**
     * @access    publico
     * @return    [Object][boolean]
     */
    function obtenerGrupo($p_iNro) {

        if ($p_iNro >= $this->cantGrupos()) {
            $this->error = "Se intenta acceder a un grupo inexistente.";
            return false;
        }
        return $this->grupos[$p_iNro];

    } //Function

    /**
     * @access    publico
     * @return    [Object][boolean]
     */
    function proximoGrupo() {

        if ($this->grupoIndex >= $this->cantGrupos())
            return false;

        return $this->grupos[$this->grupoIndex++];

    } //Function

    /**
     * @access    publico
     * @return    integer
     */
    function cantGrupos() {

        return count($this->grupos);

    } //Function

    /**
     * @access    privado
     * @return    string
     * @link crearMensaje
     */
    function renderCabecera() {

        $f_sMensaje = "1".$this->SD;
        ksort($this->cabecera);
        foreach ($this->cabecera as $f_sMiembro) {
            $f_sMensaje.= $f_sMiembro . $this->SD();
        }
        return $f_sMensaje.$this->NL();

    } //Function

    /**
     * @access    privado
     * @return    string
     * @link 	  setDebug
     * @link 	  unsetDebug
     */
    function renderDebug() {

		if(!$this->flagDebug) return;
        $f_sMensaje = "9".$this->SD().date("YmdHis00").$this->SD();
        return $f_sMensaje.$this->NL();
    } //Function

    /**
     * @access    publico
     * @return    string
     */
    function crearMensaje() {

        $f_sMensaje = $this->renderCabecera();
        foreach ($this->grupos as $grp)
            $f_sMensaje .= $grp->renderGrupo($this->SD, $this->NL);
        $f_sMensaje.= $this->renderDebug();
        return $f_sMensaje.$this->FM;
    } //Function

    /**
     * @access    publico
     * @return    boolean
     */
    function validaMensaje() {

        switch (func_num_args()) {
            case 0: $f_sMensaje = $this->crearMensaje();
                    break;
            default:$f_sMensaje = func_get_arg(0);
                    break;
        }
        $f_sMensaje = explode($this->NL(), $f_sMensaje);
        for($ii=0; $ii<count($f_sMensaje)-1; $ii++) {
            $f_sLinea = $f_sMensaje[$ii];
            $f_sPrimerCaract = substr($f_sLinea,0,strcspn($f_sLinea, $this->SD()));
/** REMOVIDO EL CONTROL DE LARGO DE LINEA.
 *             if (strlen($f_sLinea)>255)
                return false;
 */
            if (!in_array($f_sPrimerCaract, $this->caractValidos))
                return false;
            $f_sCabecera.= $f_sPrimerCaract;
            if (!ereg($this->regexLinea, strstr($f_sLinea, $this->SD()))){
                return false;
            }
        }
        $f_sCabecera.= array_pop($f_sMensaje);
        return ereg($this->regexCabecera, $f_sCabecera);
    }

    /**
     * @access    publico
     * @return    Object
     */
    function desdeCadena($p_sCadena) {

        $f_vRespuesta = New Mensaje;
        if (!$f_vRespuesta->validaMensaje($p_sCadena)) //Validamos que el mensaje esté bien formado
            return false;
        $f_sMensaje = explode($f_vRespuesta->NL(),$p_sCadena);
        foreach ($f_sMensaje as $f_sLinea) {
            $array = explode($f_vRespuesta->SD(), $f_sLinea); // Separo en componentes de Linea
            switch (substr($f_sLinea,0,strcspn($f_sLinea, $f_vRespuesta->SD()))) {
                case 1:
                    $f_vRespuesta->codReq($array[1]);
                    $f_vRespuesta->codSuc($array[2]);
                    $f_vRespuesta->idUsuario($array[3]);
                    $f_vRespuesta->ipAddress($array[4]);
                    break;
                case 2:
                    $array = array_slice($array, 1, count($array)-2);
                    $f_vRespuesta->grupoNuevo($array);
                    break;
                case 3:
                    $array = array_slice($array, 1, count($array)-2);
                    $f_vRespuesta->valorFilas($array);
                    break;
                case 8:
                    if(empty($f_vRespuesta->error)){
                    	$f_vRespuesta->error = $array[1];
                    } else {
						$f_vRespuesta->error .= "<BR>".$array[1];
					}
                    if(empty($f_vRespuesta->nvlError))
                    	$f_vRespuesta->nvlError = $array[2];
                    break;
                default: break;
            }
        }

        return $f_vRespuesta;
    } //Function

}//Class

/**
 * Grupos
 * @access			public
 * @package			Conexion
 */
Class Grupo {
    /**
     * @since     1.1
     * @access    privado
     * @link	  obtenerFila
    */
    var $indice = 0;
    var $error = "";
    var $columna = array();
    var $valor = array();

    /**
     * Devuelve el Error interno del Mensaje.
     * @since     1.0
     * @access    publico
     * @return    string
     * @link $error
     */
    function getError() {

        return $this->error;

    } //Function

	/**
	* @param	 integer	$p_iNumero
    * @param	 string		$p_sNombre
	* @since     1.0
	* @access    privado
	* @return    void
	*/
    function ligar($p_iNumero, $p_sNombre) {
        $this->columna[$p_iNumero] = $p_sNombre;
        $this->$p_sNombre = chr(32);
    return;
    }//Function

    /**
     * @access    privado
     * @return    void
     * @link 	  $error
     */
    function insertar($p_iNumero, $p_sValor) {

        $this->valor[$p_iNumero][] = $p_sValor;
        $f_sVariable = $this->columna[$p_iNumero];
        if (ord($this->{$f_sVariable}) == 32)
        {
            $this->$f_sVariable = $p_sValor;
        } else {
            if (is_array($this->$f_sVariable))
                $this->{$f_sVariable}[] = $p_sValor;
            else {
                $f_vAux = $this->$f_sVariable;
                $this->$f_sVariable = array();
                $this->{$f_sVariable}[] = $f_vAux;
                $this->{$f_sVariable}[] = $p_sValor;
            }
        }

    	return;
    } //Function


	/**
	 * @access    publico
	 * @return    void
	 */
    function columnas() {

        return count($this->columna);

    } //Function

	/**
	 * @access    publico
	 * @return    void
	 */
    function filas() {

        return count($this->valor[0]);

    } //Function

    /**
     * @access    publico
     * @return    void
     */
    function resetFilas()
    {
		$this->indice = 0;
        return;
    } // Fin Function

    /**
     * Retorna un Array Asociativo con el contenido de la Fila Actual.
     */
    function obtenerFila()
    {
		if($this->indice < $this->filas())
        {
        	foreach ($this->columna as $f_iColumna => $f_sNombre)
			{
            	$f_sArray[$f_sNombre] = $this->valor[$f_iColumna][$this->indice];

			}
			$this->indice++;
            return $f_sArray;
        }
        else
        	return false;
    } //Function

	/**
	 * Retorna el Mensaje de un Grupo
	 */
    function renderGrupo($p_vSD, $p_vNL) {

        // Creacion de la cabecera del Grupo(Linea tipo 2)
        $f_sMensaje = "2".$p_vSD;
        foreach ($this->columna as $f_sNombre) {
            $f_sMensaje.= $f_sNombre.$p_vSD;
        }
        $f_sMensaje.= $p_vNL;
        //Creacion del cuerpo del grupo (Lineas tipo 3)
        for ($ii=0; $ii<$this->filas(); $ii++) {
            $f_sMensaje .= "3".$p_vSD;
            for ($jj=0; $jj<$this->columnas(); $jj++) {
                $f_sMensaje.= $this->valor[$jj][$ii].$p_vSD;
            }
            $f_sMensaje.= $p_vNL;
        }
        return $f_sMensaje;

    } //Function
}//Class
?>
