<?php
/**
* Class created to encrypt and decrypt input data without use of any encryption libraries
* Copyright - Sergio Zalyubovskiy -- 2008
**/

class SZencoder {
	var $iHashLen = 32;
	var $iMultiplier = 32;
	var $iStep = 3;
	var $vAlphabetic = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	
	//Encoding patterns
	var $vSymArrayEnc = array("/(MT)(.+)/", "/(MD)(.+)/", "/(Mj)(.+)/", "/(Mz)(.+)/", "/(NT)(.+)/", "/(ND)(.+)/", "/(Nj)(.+)/", "/(Nz)(.+)/", "/(OT)(.+)/", "/(OD)(.+)/");
	var $vNumericEnc = array("0$2", "1$2", "2$2", "3$2", "4$2", "5$2", "6$2", "7$2", "8$2", "9$2");
	
	//Decoding patterns
	var $vSymArrayDec = array("MT$1", "MD$1", "Mj$1", "Mz$1", "NT$1", "ND$1", "Nj$1", "Nz$1", "OT$1", "OD$1");
	var $vNumericDec = array("/0(\w{2})/", "/1(\w{2})/", "/2(\w{2})/", "/3(\w{2})/", "/4(\w{2})/", "/5(\w{2})/", "/6(\w{2})/", "/7(\w{2})/", "/8(\w{2})/", "/9(\w{2})/");
	
	function setHashLen($iLen) {
		$this->iHashLen = $iLen;
	}
	
	function fRndHashGen() {
		$iC = 0;
		while($iC < $this->iHashLen) {
			$iStrRand = rand(0, count($this->vAlphabetic)-1);
			$sRndStrElt = rand(0, 1) ? strtoupper($this->vAlphabetic[$iStrRand]) : $this->vAlphabetic[$iStrRand];
			$sHash .= rand(0, 1) ? $sRndStrElt : rand(0, 9);
			$iC++;
		}
		return $sHash;
	}
	
	function setMultiplier($iMultipConst) {
		if ($iMultipConst <= 36 && $iMultipConst%4 == 0) {
			$this->iMultiplier = $iMultipConst;
		}
	}
	
	function str_split($sInputStr, $iLen = 1) {
  	$vStrArray = !function_exists('str_split') ? explode( "\r\n", chunk_split($sInputStr, $iLen) ) : str_split($sInputStr, $iLen);
	 	return $vStrArray;
	}
	
	function fStringEncoder($sStringInput) {
		$sString = $this->fEncodeInt($sStringInput);
		if ( strlen($sString) > 0 ) {
			$vStrArr = $this->str_split($sString, 3);
			foreach($vStrArr as $iKey => $iValue) {
				if ( trim($iValue) !== "" ) $sStrEncRec .= preg_replace( $this->vSymArrayEnc, $this->vNumericEnc, base64_encode($iValue) );
			}
			return $sStrEncRec;
		}	
	}
	
	function fStringDecoder($sStringInput) {
		if ( strlen($sStringInput) > 0 ) {
			$vStrArr = $this->str_split($sStringInput, 3);
			foreach($vStrArr as $iKey => $sValue) {
				$sStrDec .= base64_decode(  preg_replace($this->vNumericDec, $this->vSymArrayDec, $sValue)  );
			}
			$sStrDecRec = $this->fDecodeInt($sStrDec);
			return $sStrDecRec;
		}
	}

	function fEncodeInt($sString) {
		if ( strlen($sString) > 0 ) {
			$vEncString = array();
			for($i = 0; $i < strlen($sString); $i++) {
				$sFCode = ord( substr($sString, $i, 1) );
				$vEncString[] = sprintf("%0".$this->iStep."d", $sFCode*$this->iMultiplier);
			}
		}
		if ( is_array($vEncString) && count($vEncString) ) {
			$sEncString = "";
			foreach($vEncString as $iKey => $iStr) {
				$sEncString .= $iStr;
			}
		}
		return $sEncString;
	}
	
	function fDecodeInt($sString) {
		if ( strlen($sString) > 0 ) {
			$vDecString = array();
			for($i = 0; $i < strlen($sString);) {
				$vDecString[] = is_numeric(substr($sString, $i, $this->iStep)) ? chr(  substr($sString, $i, $this->iStep)/$this->iMultiplier  ) : 0;
				$i = $i + $this->iStep;
			}
		}
		if ( is_array($vDecString) && count($vDecString) ) {
			$sDecString = "";
			foreach($vDecString as $iKey => $sStr) {
				$sDecString .= $sStr;
			}
		}
		return $sDecString;
	}
}//class

$sStringInput = "Knudsen's 5-round impossible differential works for any Feistel cipher where the round function is invertible.";
$iMultiplier = $_GET["M"] ? intval($_GET["M"]) : 32;
$vEncObj = new SZencoder();
$vEncObj->setMultiplier($iMultiplier);
$sEncStr = $vEncObj->fStringEncoder($sStringInput);
$sDecStr = $vEncObj->fStringDecoder($sEncStr);
$vEncObj->setHashLen(58);
?>