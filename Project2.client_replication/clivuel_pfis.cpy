       01  reg-sale.
	   05  cli-cuit      pic 9(11).      *> NUMERO DE CUIT
	   05  cli-apellido  pic x(30).      *> APELLIDO CLIENTE
	   05  cli-nombre    pic x(30).      *> NOMBRE CLIENTE
	   05  cli-fecnac    pic 9(8).       *> FECHA NACIMIENTO AAAAMMDD
 	   05  cli-tipdoc    pic 9.          *> CODIGO TIPO DOCUMENTO
	   05  cli-nrodoc    pic 9(11).      *> NRO DE DOCUMENTO
	   05  cli-sexo      pic x.          *> "F" FEMENINO. "M" MASCULINO
	   05  cli-estciv    pic 99.         *> CODIGO ESTADO CIVIL
	   05  cli-profe     pic 99.         *> CODIGO PROFESION
 	   05  cli-calle     pic x(60).      *> CALLE DOMICILIO
	   05  cli-nropuer   pic x(8).       *> NUMERO DE PUERTA
	   05  cli-piso      pic xx.         *> PISO
	   05  cli-depto     pic x(4).       *> DEPARTAMENTO
	   05  cli-postal    pic x(8).       *> CODIGO POSTAL
	   05  cli-local     pic x(30).      *> LOCALIDAD 
	   05  cli-pcia      pic 99.         *> CODIGO PROVINCIA
	   05  cli-telef     pic x(40).      *> TELEFONO
	   05  cli-nacional  pic 999.        *> CODIGO NACIONALIDAD
	   05  out-tipoviv   pic x.          *> TIPOVIVIENDA
	   05  cli-iva       pic 9.          *> CODIGO SITUACION IVA
	   05  emp-nom       pic x(30).      *> CODIGO SITUACION LABORAL
	   05  emp-tipo      pic 9.          *> CODIGO CLASIFICACION
	   05  emp-calle     pic x(60).      *> CODIGO ESTADO DE DEUDA
	   05  emp-nro       pic x(8).       *> CATEGORIA LEY 25413
	   05  emp-piso      pic x(2).       *> CODIGO IMP A LAS GANANCIAS
	   05  emp-depto     pic x(4).       *> CODIGO SITUACION IVA
	   05  emp-cod-post  pic x(8).       *> OFICIAL ASIGNADO
 	   05  emp-localidad pic x(30).      *> TIPO DE OPERATORIA
	   05  emp-pcia      pic 99.         *> RESIDENCIA EN EL PAIS "SI" "NO" 
	   05  emp-telefonos pic x(40).      *> IMPUESTO EMPRESARIO "SI" "NO"
	   05  emp-legajo    pic x(16).      *> SITER "SI" "NO"
	   05  emp-fecingres pic 9(8).       *> INVERSOR CALIFICADO "SI" "NO"
	   05  emp-sueldo    pic 99.         *> EMPLEADOR "SI" "NO"
	   
