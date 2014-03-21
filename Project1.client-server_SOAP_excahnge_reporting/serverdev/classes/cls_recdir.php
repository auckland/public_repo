<?php
class RecursiveDir {//	20071001	zalyser
			var $g_sRootDir;
			var $g_vDenyDirs;
			var $g_sExt;
			
			function f_SetearVariables() {
				/*especificamos root en el documento que lo incluye o
				listamos los directorios a denegar*/
				$this->g_sRootDir = !$this->g_sRootDir ? "." : $this->g_sRootDir;
				$this->g_vDenyDirs = !$this->g_vDenyDirs ? array() : $this->g_vDenyDirs;
				$this->g_sExt = !$this->g_sExt ? "\w+" : $this->g_sExt;
			}
			
			function f_RecursiveList($sDir) {
					$this->f_SetearVariables();
			        $vDirTree = array();
			        $vDirs = array(array($sDir, &$vDirTree));
			       
			        for($i = 0; $i < count($vDirs); ++$i) {
			        		//if ( !preg_match("/(~|!)+.*/i", $vDirs[$i][0]) ) {//no ~[dir_name] o !!![dir_name]
			                $sResultDir = opendir($vDirs[$i][0]);
			                $vDirTier =& $vDirs[$i][1];
			                while( $sFile = readdir($sResultDir) ) {
			                				//listar todos directorios que NO se permite mostrar dentro de array $this->g_vDenyDirs
			                        if ( $sFile != '.' && $sFile != '..' && !in_array($sFile, $this->g_vDenyDirs) ) {
			                        //if ( $sFile != '.' && $sFile != '..') {
			                                ////$sDirPath = $vDirs[$i][0] . "/" . $sFile;
			                                $sDirPath = $vDirs[$i][0] . $sFile;
			                                if ( is_dir($sDirPath) ) {
			                                	//print $sDirPath."<br>";
			                                        $vDirTier[$sFile] = array();
			                                        $vDirs[] = array($sDirPath, &$vDirTier[$sFile]);
			                                } else {
			                                        //$vDirTier[$sFile] = filesize($sDirPath);
			                                        if (preg_match("/.*\.(".$this->g_sExt.")$/i", $sFile)) {
			                                        		$vDirTier[$sFile] = $sDirPath;
			                                        }
			                                }
			                        }
			                }
			                
			          //}
			                
			        }
			       
			        return $vDirTree;
			}
			
			function f_FlattenArray($sValue, $Key, &$vArray) {
			    if ( !is_array($sValue) ) {
			        array_push($vArray, $sValue);
			    } else {
			        array_walk($sValue,  array($this, 'f_FlattenArray'), &$vArray);
			    } 
			}
		
			function f_ReturnVals() {
				$vDirsArr = $this->f_RecursiveList($this->g_sRootDir);
				$vNuevoArr = array();
				array_walk($vDirsArr, array($this, 'f_FlattenArray'), &$vNuevoArr);
				return $vNuevoArr;				
			}
}//END: RecursiveDir
?>

