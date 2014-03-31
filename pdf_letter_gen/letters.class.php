<?php
 /**
   * Class LetterToGen
   * Warn: Linux/UNIX only. Otherwise use system call or exec
   * 
   * @package    Letters
   * @subpackage MAIN
   * @author     Sergio Z <sergio@domain.com>
   */
class LetterToGen {
	var $_path = "/usr/u/spool/letters/";
	var $_dirTemplates = "/home/www/php-lib/sys/letters/";
	var $_fileLetter = "check_letters";
	var $_errors = array();
	var $_branch = "";
	var $_codBranch = "";
	var $_cityBranch = "";
	var $_branchAddress="";
	var $_sex="";
	var $_fullName="";
	var $_address="";
	var $_postCode="";
	var $_suburb="";
	var $_accountNumber="";
	var $_rejectDate="";
	var $_checkNumber="";
	var $_currencyType="";
	var $_total="";
	var $_reasonReject="";
	var $_signerName="";
	var $_commNumber="";
	var $_commDate="";
	var $_rejectOrder="";
	var $_printerDev="";
	var $_fineTotal="";

	function LetterToGen($p_sFile, $p_sPrinter) {
		$this->_printerDev = $p_sPrinter.".host.com.ar";
	}

	function runProcess() {
		if ($this->Validation()) {
			$oTemplate = new templateEngine($this->dirTemplates());
			$oTemplate->define(array("letter"=>$this->fileLetter.".tpl"));
			$oTemplate->assign("BRANCH", $this->branch());
			$oTemplate->assign("BRANCH_ADDRESS", $this->branchAddress());
			$oTemplate->assign("BRANCH_CITY", $this->cityBranch());
			$vCurrDate = getdate();
			$oTemplate->assign("DATE_CURRENT", $vCurrDate["mday"]."/".$vCurrDate["mon"]."/".$vCurrDate["year"]);
			$oTemplate->assign("MR_MRS", $this->sex());
			$oTemplate->assign("FULL_NAME", $this->fullName());
			$oTemplate->assign("ADDRESS", $this->address());
			$oTemplate->assign("PC", $this->postCode());
			$oTemplate->assign("SUBURB", $this->suburb());
			$oTemplate->assign("ACCNUM", $this->accountNumber());
			$oTemplate->assign("REJ_DATE", $this->rejectDate());
			$oTemplate->assign("CHECKNUM", $this->checkNumber());
			$oTemplate->assign("CURRENCY", $this->currencyType());
			$oTemplate->assign("TOTAL", $this->total());
			$oTemplate->assign("REJ_REASON", $this->reasonReject());
			$oTemplate->assign("SIGN_NAME", $this->signerName());
			$oTemplate->assign("COMM_NUM", $this->commNumber());
			$oTemplate->assign("COMM_DATE", $this->commDate());
			$oTemplate->assign("REJ_ORDER", $this->rejectOrder());
			$oTemplate->assign("FINE", $this->fineTotal());
			$oTemplate->parse (MAIN, "letter");
			$sContent = $oTemplate->fetch("MAIN");
			/**
			* Requires: formatDate
			*/
			$sLetterName = $this->_path.$this->commNumber().formatDate($this->commDate()).$this->_fileLetter.".htm";
			$this->fwrite_r($sLetterName, $sContent, "w")
			//Direct call system
			$sCmd = `./HTMLprint.sh $this->_printerDev $sLetterName && rm -f $sLetterName | at now + 0 minutes > /dev/null 2>&1`;
			return true;
		} else {
			return false; //echo $this->_errors;
		}
	}

 /**
   * Set Dir Templates
   * 
   * 
   * @package    Templates
   * @subpackage Layers
   * @author     Sergio Z <sergio@domain.com>
   */
	function fwrite_r($sFilename, $sContent, $sMode) {
		if (is_writable($sFilename)) {
			if (!$vHandle = fopen($sFilename, $sMode)) {
				 $this->newError("Error: Cannot open file ($sFilename)");
				 exit;
			}
			if (fwrite($vHandle, $sContent) === FALSE) {
				$this->newError("Error: Cannot write to file ($sFilename)");
				exit;
			}
			fclose($vHandle);
		} else {
			$this->newError("Error: The file $sFilename is not writable");
		}
	}
   
	function dirTemplates() {
		if (func_num_args()==1)
			$this->_dirTemplates = func_get_arg(0);
		else
			return $this->_dirTemplates;
	}

	function branch() {
		if (func_num_args()==1)
			$this->_branch = func_get_arg(0);
		else
			return $this->_branch;
	}

	function branchAddress() {
		if (func_num_args()==1)
			$this->_branchAddress = func_get_arg(0);
		else
			return $this->_branchAddress;
	}

	function codBranch() {
		if (func_num_args()==1)
			$this->_codBranch = func_get_arg(0);
		else
			return $this->_codBranch;
	}

	function sex() {
		if (func_num_args()==1)
			$this->_sex = func_get_arg(0);
		else
			return $this->_sex;
	}

	function fullName() {
		if (func_num_args()==1)
			$this->_fullName = func_get_arg(0);
		else
			return $this->_fullName;
	}

	function address() {
		if (func_num_args()==1)
			$this->_address = func_get_arg(0);
		else
			return $this->_address;
	}

	function postCode() {
		if (func_num_args()==1)
			$this->_postCode = func_get_arg(0);
		else
			return $this->_postCode;
	}

	function suburb() {
		if (func_num_args()==1)
			$this->_suburb = func_get_arg(0);
		else
			return $this->_suburb;
	}

	function accountNumber() {
		if (func_num_args()==1)
			$this->_accountNumber = func_get_arg(0);
		else
			return $this->_accountNumber;
	}

	function rejectDate() {
		if (func_num_args()==1)
			$this->_rejectDate = func_get_arg(0);
		else
			return $this->_rejectDate;
	}

	function checkNumber() {
		if (func_num_args()==1)
			$this->_checkNumber = func_get_arg(0);
		else
			return $this->_checkNumber;
	}

	function currencyType() {
		if (func_num_args()==1)
			$this->_currencyType = func_get_arg(0);
		else
			return $this->_currencyType;
	}

	function total() {
		if (func_num_args()==1)
			$this->_total = func_get_arg(0);
		else
			return $this->_total;
	}

	function reasonReject() {
		if (func_num_args()==1)
			$this->_reasonReject = func_get_arg(0);
		else
			return $this->_reasonReject;
	}

	function signerName() {
		if (func_num_args()==1)
			$this->_signerName = func_get_arg(0);
		else
			return $this->_signerName;
	}

	function commNumber() {
		if (func_num_args()==1)
			$this->_commNumber = func_get_arg(0);
		else
			return $this->_commNumber;
	}

	function commDate() {
		if (func_num_args()==1)
			$this->_commDate = func_get_arg(0);
		else
			return $this->_commDate;
	}

	function rejectOrder() {
		if (func_num_args()==1)
			$this->_rejectOrder = func_get_arg(0);
		else
			return $this->_rejectOrder;
	}

	function fineTotal() {
		if (func_num_args()==1)
			$this->_fineTotal = func_get_arg(0);
		else
			return $this->_fineTotal;
	}

	function errores() {
		return $this->_errores;
	}

	function validar() {
		$bReturn = true;
		if ($this->branch()=="") {
			$this->newError("Error: Variable \"branch\" is not defined.");
			$bReturn = false;
		}

		if ($this->branchAddress()=="") {
			$this->newError("Error: Variable \"branchAddress\" is not defined.");
			$bReturn = false;
		}

		if ($this->sex()=="") {
			$this->newError("Error: Variable \"sex\" is not defined.");
			$bReturn = false;
		}

		if ($this->fullName()=="") {
			$this->newError("Error: Variable \"fullName\" is not defined.");
			$bReturn = false;
		}

		if ($this->address()=="") {
			$this->newError("Error: Variable \"address\" is not defined.");
			$bReturn = false;
		}

		if ($this->postCode()=="") {
			$this->newError("Error: Variable \"postCode\" is not defined.");
			$bReturn = false;
		}

		if ($this->suburb()=="") {
			$this->newError("Error: Variable \"suburb\" is not defined.");
			$bReturn = false;
		}

		if ($this->accountNumber()=="") {
			$this->newError("Error: Variable \"accountNumber\" is not defined.");
			$bReturn = false;
		}

		if ($this->rejectDate()=="") {
			$this->newError("Error: Variable \"rejectDate\" is not defined.");
			$bReturn = false;
		}

		if ($this->checkNumber()=="") {
			$this->newError("Error: Variable \"checkNumber\" is not defined.");
			$bReturn = false;
		}

		if ($this->currencyType()=="") {
			$this->newError("Error: Variable \"currencyType\" is not defined.");
			$bReturn = false;
		}

		if ($this->total()=="") {
			$this->newError("Error: Variable \"total\" is not defined.");
			$bReturn = false;
		}

		if ($this->reasonReject()=="") {
			$this->newError("Error: Variable \"reasonReject\" is not defined.");
			$bReturn = false;
		}

		if ($this->signerName()=="") {
			$this->newError("Error: Variable \"signerName\" is not defined.");
			$bReturn = false;
		}

		if ($this->commNumber()=="") {
			$this->newError("Error: Variable \"commNumber\" is not defined.");
			$bReturn = false;
		}

		if ($this->commDate()=="") {
			$this->newError("Error: Variable \"commDate\" is not defined.");
			$bReturn = false;
		}

		if ($this->rejectOrder()=="") {
			$this->newError("Error: Variable \"rejectOrder\" is not defined.");
			$bReturn = false;
		}

		if ($this->fineTotal()=="") {
			$this->newError("Error: Variable \"fineTotal\" is not defined.");
			$bReturn = false;
		}

		return $bReturn;
	}
 /**
   * Errors
   * 
   * @package    Errors
   */
	function newError($p_sMensaje) {
		$this->_errors[] = $p_sMensaje;
	}

	function cityBranch() {
		switch ($this->_codBranch) {
		case "000":
		case "001":
		case "002":
		case "026":
		case "030":
			return "Buenos Aires";
		case "003":
		case "013":
			return "Azul";
		case "014":
			return "Cachari";
		case "020":
			return "Tandil";
		case "021":
			return "Balcarce";
		case "022":
		case "036":
			return "Mar del Plata";
		case "023":
		case "037":
			return "La Plata";
		case "024":
			return "Jun’n";
		case "025":
			return "Bahía Blanca";
		case "026":
			return "Las Heras";
		case "027":
		case "032":
			return "C—rdoba";
		case "031":
			return "Salta";
		case "033":
			return "Rosario";
		case "034":
			return "Mendoza";
		case "035":
			return "Tucum‡n";
		}
	}

} //Class
?>
