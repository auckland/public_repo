<?php
  /**
   * Author: Sergio Zalyubovskiy (C) 2012
   * @param ConsultarCOCRequestType $parameters
   * @return ConsultarCOCResponseType
   */
class DummyResponseType {
  public $dummyReturn; // DummyReturnType
}

class DummyReturnType {
  public $appserver; // string
  public $authserver; // string
  public $dbserver; // string
}

class AuthRequestType {
  public $token; // string
  public $sign; // string
  public $cuitRepresentada; // CuitSimpleType
}

class ConsultarMonedasRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarMonedasReturnType {
  public $arrayMonedas; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class CodigoDescripcionType {
  public $codigo; // short
  public $descripcion; // string
}

class ArrayCodigosDescripcionesStringType {
  public $codigoDescripcionString; // CodigoDescripcionStringType
}

class CodigoDescripcionStringType {
  public $codigo; // string
  public $descripcion; // string
}

class ConsultarTiposDocumentosRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarTiposDocumentoReturnType {
  public $arrayTiposDocumento; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ArrayCodigosDescripcionesType {
  public $codigoDescripcion; // CodigoDescripcionType
}

class ConsultarTiposEstadoSolicitudRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarTiposEstadoSolicitudReturnType {
  public $arrayTiposEstadoSolicitud; // ArrayCodigosDescripcionesStringType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class AnularCOCRequestType {
  public $authRequest; // AuthRequestType
  public $coc; // COCSimpleType
  public $cuitComprador; // CuitSimpleType
  public $tndTurExtComprador; // TipoNumeroDocType
}

class AnularCOCReturnType {
  public $coc; // COCSimpleType
  public $estadoSolicitud; // EstadoSolicitudSimpleType
  public $resultado; // ResultadoSimpleType
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ResultadoSimpleType {
  const A = 'A';
  const O = 'O';
  const R = 'R';
  const E = 'E';
}

class ConsultarCOCRequestType {
  public $authRequest; // AuthRequestType
  public $coc; // COCSimpleType
}

class ConsultarCOCReturnType {
  public $detalleSolicitud; // DetalleSolicitudType
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class COCSimpleType {
}

class DetalleSolicitudType {
  public $codigoSolicitud; // long
  public $fechaSolicitud; // dateTime
  public $coc; // COCSimpleType
  public $fechaEmisionCOC; // dateTime
  public $estadoSolicitud; // EstadoSolicitudSimpleType
  public $fechaEstado; // dateTime
  public $detalleCUITComprador; // DetalleCUITType
  public $detalleTurExtComprador; // DetalleTurExtType
  public $codigoMoneda; // short
  public $cotizacionMoneda; // CotizacionMonedaSimpleType
  public $montoPesos; // MontoSimpleType
  public $detalleCUITRepresentante; // DetalleCUITType
  public $codigoDestino; // short
  public $djai; // DJAISimpleType
  public $codigoExcepcionDJAI; // short
  public $djas; // DJASSimpleType
  public $codigoExcepcionDJAS; // short
  public $arrayInconsistencias; // ArrayCodigosDescripcionesType
}

class EstadoSolicitudSimpleType {
  const OT = 'OT';
  const CO = 'CO';
  const DC = 'DC';
  const DB = 'DB';
  const AN = 'AN';
  const CA = 'CA';
  const RE = 'RE';
}

class MontoSimpleType {
}

class ConsultarSolicitudesCompraDivisasRequestType {
  public $authRequest; // AuthRequestType
  public $cuitComprador; // CuitSimpleType
  public $tndTurExtComprador; // TipoNumeroDocType
  public $estadoSolicitud; // EstadoSolicitudSimpleType
  public $fechaEmisionDesde; // date
  public $fechaEmisionHasta; // date
}

class ConsultarSolicitudesCompraDivisasReturnType {
  public $arrayDetallesSolicitudes; // ArrayDetallesSolicitudesType
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ArrayDetallesSolicitudesType {
  public $detalleSolicitudes; // DetalleSolicitudType
}

class InformarSolicitudCompraDivisaRequestType {
  public $authRequest; // AuthRequestType
  public $codigoSolicitud; // long
  public $nuevoEstado; // NuevoEstadoSimpleType
}

class InformarSolicitudCompraDivisaReturnType {
  public $codigoSolicitud; // long
  public $estadoSolicitud; // EstadoSolicitudSimpleType
  public $coc; // COCSimpleType
  public $fechaEmisionCOC; // dateTime
  public $resultado; // ResultadoSimpleType
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class GenerarSolicitudCompraDivisaRequestType {
  public $authRequest; // AuthRequestType
  public $cuitComprador; // CuitSimpleType
  public $codigoMoneda; // short
  public $cotizacionMoneda; // CotizacionMonedaSimpleType
  public $montoPesos; // MontoSimpleType
  public $cuitRepresentante; // CuitSimpleType
  public $codigoDestino; // short
  public $djai; // DJAISimpleType
  public $codigoExcepcionDJAI; // short
  public $djas; // DJASSimpleType
  public $codigoExcepcionDJAS; // short
}

class CuitSimpleType {
}

class NuevoEstadoSimpleType {
  const CO = 'CO';
  const DC = 'DC';
  const DB = 'DB';
}

class GenerarSolicitudCompraDivisaReturnType {
  public $detalleSolicitud; // DetalleSolicitudType
  public $resultado; // ResultadoSimpleType
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ConsultarCUITRequestType {
  public $authRequest; // AuthRequestType
  public $tipoNumeroDoc; // TipoNumeroDocType
}

class ConsultarCUITReturnType {
  public $tipoNumeroDoc; // TipoNumeroDocType
  public $arrayDetallesCUIT; // ArrayDetallesCUITType
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class GenerarSolicitudCompraDivisaResponseType {
  public $generarSolicitudCompraDivisaReturn; // GenerarSolicitudCompraDivisaReturnType
}

class InformarSolicitudCompraDivisaResponseType {
  public $informarSolicitudCompraDivisaReturn; // InformarSolicitudCompraDivisaReturnType
}

class DetalleCUITType {
  public $cuit; // CuitSimpleType
  public $denominacion; // string
}

class ArrayDetallesCUITType {
  public $detalleCUIT; // DetalleCUITType
}

class ConsultarCOCResponseType {
  public $consultarCOCReturn; // ConsultarCOCReturnType
}

class ConsultarSolicitudesCompraDivisasResponseType {
  public $consultarSolicitudesCompraDivisasReturn; // ConsultarSolicitudesCompraDivisasReturnType
}

class AnularCOCResponseType {
  public $anularCOCReturn; // AnularCOCReturnType
}

class ConsultarCUITResponseType {
  public $consultarCUITReturn; // ConsultarCUITReturnType
}

class TipoNumeroDocType {
  public $tipoDoc; // short
  public $numeroDoc; // NumeroDocSimpleType
}

class ConsultarDestinosCompraRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarDestinosCompraReturnType {
  public $arrayDestinos; // ArrayDestinosType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class DestinosType {
  public $tipoDestino; // TipoDestinoSimpleType
  public $arrayCodigosDescripciones; // ArrayCodigosDescripcionesType
}

class ArrayDestinosType {
  public $destinos; // DestinosType
}

class TipoDestinoSimpleType {
  const ME = 'ME';
  const SE = 'SE';
  const RE = 'RE';
  const CA = 'CA';
  const OT = 'OT';
}

class ConsultarDestinosCompraResponseType {
  public $consultarDestinosCompraReturn; // ConsultarDestinosCompraReturnType
}

class ConsultarTiposEstadoSolicitudResponseType {
  public $consultarTiposEstadoSolicitudReturn; // ConsultarTiposEstadoSolicitudReturnType
}

class ConsultarMonedasResponseType {
  public $consultarMonedasReturn; // ConsultarMonedasReturnType
}

class ConsultarTiposDocumentoResponseType {
  public $consultarTiposDocumentoReturn; // ConsultarTiposDocumentoReturnType
}

class CotizacionMonedaSimpleType {
}

class ConsultarSolicitudCompraDivisaRequestType {
  public $authRequest; // AuthRequestType
  public $codigoSolicitud; // long
}

class ConsultarSolicitudCompraDivisaResponseType {
  public $consultarSolicitudCompraDivisaReturn; // ConsultarSolicitudCompraDivisaReturnType
}

class ConsultarSolicitudCompraDivisaReturnType {
  public $detalleSolicitud; // DetalleSolicitudType
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class GenerarSolicitudCompraDivisaTurExtRequestType {
  public $authRequest; // AuthRequestType
  public $detalleTurExtComprador; // DetalleTurExtType
  public $codigoMoneda; // short
  public $cotizacionMoneda; // CotizacionMonedaSimpleType
  public $montoPesos; // MontoSimpleType
  public $cuitRepresentante; // CuitSimpleType
}

class DetalleTurExtType {
  public $tipoNumeroDoc; // TipoNumeroDocType
  public $apellidoNombre; // ApellidoNombreSimpleType
}

class ApellidoNombreSimpleType {
}

class GenerarSolicitudCompraDivisaTurExtResponseType {
  public $generarSolicitudCompraDivisaTurExtReturn; // GenerarSolicitudCompraDivisaReturnType
}

class NumeroDocSimpleType {
}

class DJAISimpleType {
}

class ConsultarMotivosExcepcionDJAIRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarMotivosExcepcionDJAIResponseType {
  public $consultarMotivosExcepcionDJAIReturn; // ConsultarMotivosExcepcionDJAIReturnType
}

class ConsultarMotivosExcepcionDJAIReturnType {
  public $arrayMotivosExcepcion; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ConsultarDestinosCompraDJAIRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarDestinosCompraDJAIResponseType {
  public $consultarDestinosCompraDJAIReturn; // ConsultarDestinosCompraDJAIReturnType
}

class ConsultarDestinosCompraDJAIReturnType {
  public $arrayCodigosDescripciones; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ConsultarDJAIRequestType {
  public $authRequest; // AuthRequestType
  public $djai; // DJAISimpleType
  public $cuit; // CuitSimpleType
}

class ConsultarDJAIResponseType {
  public $consultarDJAIReturn; // ConsultarDJAIReturnType
}

class ConsultarDJAIReturnType {
  public $djai; // DJAISimpleType
  public $cuit; // CuitSimpleType
  public $montoFOB; // MontoSimpleType
  public $codigoMoneda; // short
  public $estadoDJAI; // string
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ConsultarMotivosExcepcionDJASRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarDestinosCompraDJASRequestType {
  public $authRequest; // AuthRequestType
}

class ConsultarMotivosExcepcionDJASResponseType {
  public $consultarMotivosExcepcionDJASReturn; // ConsultarMotivosExcepcionDJASReturnType
}

class ConsultarMotivosExcepcionDJASReturnType {
  public $arrayMotivosExcepcion; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ConsultarDestinosCompraDJASResponseType {
  public $consultarDestinosCompraDJASReturn; // ConsultarDestinosCompraDJASReturnType
}

class ConsultarDestinosCompraDJASReturnType {
  public $arrayCodigosDescripciones; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}

class ConsultarDJASRequestType {
  public $authRequest; // AuthRequestType
  public $djas; // DJASSimpleType
  public $cuit; // CuitSimpleType
}

class DJASSimpleType {
}

class ConsultarDJASResponseType {
  public $consultarDJASReturn; // ConsultarDJASReturnType
}

class ConsultarDJASReturnType {
  public $djas; // DJASSimpleType
  public $cuit; // CuitSimpleType
  public $monto; // MontoSimpleType
  public $codigoMoneda; // short
  public $estadoDJAS; // string
  public $arrayErrores; // ArrayCodigosDescripcionesType
  public $arrayErroresFormato; // ArrayCodigosDescripcionesStringType
}


/**
 * COCService class
 * 
 *  
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class COCService extends SoapClient {

  private static $classmap = array(
                                    'DummyResponseType' => 'DummyResponseType',
                                    'DummyReturnType' => 'DummyReturnType',
                                    'AuthRequestType' => 'AuthRequestType',
                                    'ConsultarMonedasRequestType' => 'ConsultarMonedasRequestType',
                                    'ConsultarMonedasReturnType' => 'ConsultarMonedasReturnType',
                                    'CodigoDescripcionType' => 'CodigoDescripcionType',
                                    'ArrayCodigosDescripcionesStringType' => 'ArrayCodigosDescripcionesStringType',
                                    'CodigoDescripcionStringType' => 'CodigoDescripcionStringType',
                                    'ConsultarTiposDocumentosRequestType' => 'ConsultarTiposDocumentosRequestType',
                                    'ConsultarTiposDocumentoReturnType' => 'ConsultarTiposDocumentoReturnType',
                                    'ArrayCodigosDescripcionesType' => 'ArrayCodigosDescripcionesType',
                                    'ConsultarTiposEstadoSolicitudRequestType' => 'ConsultarTiposEstadoSolicitudRequestType',
                                    'ConsultarTiposEstadoSolicitudReturnType' => 'ConsultarTiposEstadoSolicitudReturnType',
                                    'AnularCOCRequestType' => 'AnularCOCRequestType',
                                    'AnularCOCReturnType' => 'AnularCOCReturnType',
                                    'ResultadoSimpleType' => 'ResultadoSimpleType',
                                    'ConsultarCOCRequestType' => 'ConsultarCOCRequestType',
                                    'ConsultarCOCReturnType' => 'ConsultarCOCReturnType',
                                    'COCSimpleType' => 'COCSimpleType',
                                    'DetalleSolicitudType' => 'DetalleSolicitudType',
                                    'EstadoSolicitudSimpleType' => 'EstadoSolicitudSimpleType',
                                    'MontoSimpleType' => 'MontoSimpleType',
                                    'ConsultarSolicitudesCompraDivisasRequestType' => 'ConsultarSolicitudesCompraDivisasRequestType',
                                    'ConsultarSolicitudesCompraDivisasReturnType' => 'ConsultarSolicitudesCompraDivisasReturnType',
                                    'ArrayDetallesSolicitudesType' => 'ArrayDetallesSolicitudesType',
                                    'InformarSolicitudCompraDivisaRequestType' => 'InformarSolicitudCompraDivisaRequestType',
                                    'InformarSolicitudCompraDivisaReturnType' => 'InformarSolicitudCompraDivisaReturnType',
                                    'GenerarSolicitudCompraDivisaRequestType' => 'GenerarSolicitudCompraDivisaRequestType',
                                    'CuitSimpleType' => 'CuitSimpleType',
                                    'NuevoEstadoSimpleType' => 'NuevoEstadoSimpleType',
                                    'GenerarSolicitudCompraDivisaReturnType' => 'GenerarSolicitudCompraDivisaReturnType',
                                    'ConsultarCUITRequestType' => 'ConsultarCUITRequestType',
                                    'ConsultarCUITReturnType' => 'ConsultarCUITReturnType',
                                    'GenerarSolicitudCompraDivisaResponseType' => 'GenerarSolicitudCompraDivisaResponseType',
                                    'InformarSolicitudCompraDivisaResponseType' => 'InformarSolicitudCompraDivisaResponseType',
                                    'DetalleCUITType' => 'DetalleCUITType',
                                    'ArrayDetallesCUITType' => 'ArrayDetallesCUITType',
                                    'ConsultarCOCResponseType' => 'ConsultarCOCResponseType',
                                    'ConsultarSolicitudesCompraDivisasResponseType' => 'ConsultarSolicitudesCompraDivisasResponseType',
                                    'AnularCOCResponseType' => 'AnularCOCResponseType',
                                    'ConsultarCUITResponseType' => 'ConsultarCUITResponseType',
                                    'TipoNumeroDocType' => 'TipoNumeroDocType',
                                    'ConsultarDestinosCompraRequestType' => 'ConsultarDestinosCompraRequestType',
                                    'ConsultarDestinosCompraReturnType' => 'ConsultarDestinosCompraReturnType',
                                    'DestinosType' => 'DestinosType',
                                    'ArrayDestinosType' => 'ArrayDestinosType',
                                    'TipoDestinoSimpleType' => 'TipoDestinoSimpleType',
                                    'ConsultarDestinosCompraResponseType' => 'ConsultarDestinosCompraResponseType',
                                    'ConsultarTiposEstadoSolicitudResponseType' => 'ConsultarTiposEstadoSolicitudResponseType',
                                    'ConsultarMonedasResponseType' => 'ConsultarMonedasResponseType',
                                    'ConsultarTiposDocumentoResponseType' => 'ConsultarTiposDocumentoResponseType',
                                    'CotizacionMonedaSimpleType' => 'CotizacionMonedaSimpleType',
                                    'ConsultarSolicitudCompraDivisaRequestType' => 'ConsultarSolicitudCompraDivisaRequestType',
                                    'ConsultarSolicitudCompraDivisaResponseType' => 'ConsultarSolicitudCompraDivisaResponseType',
                                    'ConsultarSolicitudCompraDivisaReturnType' => 'ConsultarSolicitudCompraDivisaReturnType',
                                    'GenerarSolicitudCompraDivisaTurExtRequestType' => 'GenerarSolicitudCompraDivisaTurExtRequestType',
                                    'DetalleTurExtType' => 'DetalleTurExtType',
                                    'ApellidoNombreSimpleType' => 'ApellidoNombreSimpleType',
                                    'GenerarSolicitudCompraDivisaTurExtResponseType' => 'GenerarSolicitudCompraDivisaTurExtResponseType',
                                    'NumeroDocSimpleType' => 'NumeroDocSimpleType',
                                    'DJAISimpleType' => 'DJAISimpleType',
                                    'ConsultarMotivosExcepcionDJAIRequestType' => 'ConsultarMotivosExcepcionDJAIRequestType',
                                    'ConsultarMotivosExcepcionDJAIResponseType' => 'ConsultarMotivosExcepcionDJAIResponseType',
                                    'ConsultarMotivosExcepcionDJAIReturnType' => 'ConsultarMotivosExcepcionDJAIReturnType',
                                    'ConsultarDestinosCompraDJAIRequestType' => 'ConsultarDestinosCompraDJAIRequestType',
                                    'ConsultarDestinosCompraDJAIResponseType' => 'ConsultarDestinosCompraDJAIResponseType',
                                    'ConsultarDestinosCompraDJAIReturnType' => 'ConsultarDestinosCompraDJAIReturnType',
                                    'ConsultarDJAIRequestType' => 'ConsultarDJAIRequestType',
                                    'ConsultarDJAIResponseType' => 'ConsultarDJAIResponseType',
                                    'ConsultarDJAIReturnType' => 'ConsultarDJAIReturnType',
                                    'ConsultarMotivosExcepcionDJASRequestType' => 'ConsultarMotivosExcepcionDJASRequestType',
                                    'ConsultarDestinosCompraDJASRequestType' => 'ConsultarDestinosCompraDJASRequestType',
                                    'ConsultarMotivosExcepcionDJASResponseType' => 'ConsultarMotivosExcepcionDJASResponseType',
                                    'ConsultarMotivosExcepcionDJASReturnType' => 'ConsultarMotivosExcepcionDJASReturnType',
                                    'ConsultarDestinosCompraDJASResponseType' => 'ConsultarDestinosCompraDJASResponseType',
                                    'ConsultarDestinosCompraDJASReturnType' => 'ConsultarDestinosCompraDJASReturnType',
                                    'ConsultarDJASRequestType' => 'ConsultarDJASRequestType',
                                    'DJASSimpleType' => 'DJASSimpleType',
                                    'ConsultarDJASResponseType' => 'ConsultarDJASResponseType',
                                    'ConsultarDJASReturnType' => 'ConsultarDJASReturnType',
                                   );

  public function COCService($wsdl = "https://xxxxxxx.afip.gov.ar/wscoc/COCService?wsdl", $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options);
  }

  /**
   *  
   *
   * @param  
   * @return DummyResponseType
   */
  public function dummy() {
    return $this->__soapCall('dummy', array(),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GenerarSolicitudCompraDivisaRequestType $parameters
   * @return GenerarSolicitudCompraDivisaResponseType
   */
  public function generarSolicitudCompraDivisa(GenerarSolicitudCompraDivisaRequestType $parameters) {
    return $this->__soapCall('generarSolicitudCompraDivisa', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param InformarSolicitudCompraDivisaRequestType $parameters
   * @return InformarSolicitudCompraDivisaResponseType
   */
  public function informarSolicitudCompraDivisa(InformarSolicitudCompraDivisaRequestType $parameters) {
    return $this->__soapCall('informarSolicitudCompraDivisa', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarCOCRequestType $parameters
   * @return ConsultarCOCResponseType
   */
  public function consultarCOC(ConsultarCOCRequestType $parameters) {
    return $this->__soapCall('consultarCOC', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarSolicitudesCompraDivisasRequestType $parameters
   * @return ConsultarSolicitudesCompraDivisasResponseType
   */
  public function consultarSolicitudesCompraDivisas(ConsultarSolicitudesCompraDivisasRequestType $parameters) {
    return $this->__soapCall('consultarSolicitudesCompraDivisas', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param AnularCOCRequestType $parameters
   * @return AnularCOCResponseType
   */
  public function anularCOC(AnularCOCRequestType $parameters) {
    return $this->__soapCall('anularCOC', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarTiposDocumentosRequestType $parameters
   * @return ConsultarTiposDocumentoResponseType
   */
  public function consultarTiposDocumento(ConsultarTiposDocumentosRequestType $parameters) {
    return $this->__soapCall('consultarTiposDocumento', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarMonedasRequestType $parameters
   * @return ConsultarMonedasResponseType
   */
  public function consultarMonedas(ConsultarMonedasRequestType $parameters) {
    return $this->__soapCall('consultarMonedas', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarTiposEstadoSolicitudRequestType $parameters
   * @return ConsultarTiposEstadoSolicitudResponseType
   */
  public function consultarTiposEstadoSolicitud(ConsultarTiposEstadoSolicitudRequestType $parameters) {
    return $this->__soapCall('consultarTiposEstadoSolicitud', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarCUITRequestType $parameters
   * @return ConsultarCUITResponseType
   */
  public function consultarCUIT(ConsultarCUITRequestType $parameters) {
    return $this->__soapCall('consultarCUIT', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarDestinosCompraRequestType $parameters
   * @return ConsultarDestinosCompraResponseType
   */
  public function consultarDestinosCompra(ConsultarDestinosCompraRequestType $parameters) {
    return $this->__soapCall('consultarDestinosCompra', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarSolicitudCompraDivisaRequestType $parameters
   * @return ConsultarSolicitudCompraDivisaResponseType
   */
  public function consultarSolicitudCompraDivisa(ConsultarSolicitudCompraDivisaRequestType $parameters) {
    return $this->__soapCall('consultarSolicitudCompraDivisa', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GenerarSolicitudCompraDivisaTurExtRequestType $parameters
   * @return GenerarSolicitudCompraDivisaTurExtResponseType
   */
  public function generarSolicitudCompraDivisaTurExt(GenerarSolicitudCompraDivisaTurExtRequestType $parameters) {
    return $this->__soapCall('generarSolicitudCompraDivisaTurExt', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarMotivosExcepcionDJAIRequestType $parameters
   * @return ConsultarMotivosExcepcionDJAIResponseType
   */
  public function consultarMotivosExcepcionDJAI(ConsultarMotivosExcepcionDJAIRequestType $parameters) {
    return $this->__soapCall('consultarMotivosExcepcionDJAI', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarDestinosCompraDJAIRequestType $parameters
   * @return ConsultarDestinosCompraDJAIResponseType
   */
  public function consultarDestinosCompraDJAI(ConsultarDestinosCompraDJAIRequestType $parameters) {
    return $this->__soapCall('consultarDestinosCompraDJAI', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarDJAIRequestType $parameters
   * @return ConsultarDJAIResponseType
   */
  public function consultarDJAI(ConsultarDJAIRequestType $parameters) {
    return $this->__soapCall('consultarDJAI', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarMotivosExcepcionDJASRequestType $parameters
   * @return ConsultarMotivosExcepcionDJASResponseType
   */
  public function consultarMotivosExcepcionDJAS(ConsultarMotivosExcepcionDJASRequestType $parameters) {
    return $this->__soapCall('consultarMotivosExcepcionDJAS', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarDestinosCompraDJASRequestType $parameters
   * @return ConsultarDestinosCompraDJASResponseType
   */
  public function consultarDestinosCompraDJAS(ConsultarDestinosCompraDJASRequestType $parameters) {
    return $this->__soapCall('consultarDestinosCompraDJAS', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ConsultarDJASRequestType $parameters
   * @return ConsultarDJASResponseType
   */
  public function consultarDJAS(ConsultarDJASRequestType $parameters) {
    return $this->__soapCall('consultarDJAS', array($parameters),       array(
            'uri' => 'http://ar.gob.afip.wscoc/COCService/',
            'soapaction' => ''
           )
      );
  }

}

?>
