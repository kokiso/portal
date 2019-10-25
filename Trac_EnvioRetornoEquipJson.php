<?php
require("classPhp/conectaSqlServer.class.php");
$classe   = new conectaBd();
$classe->conecta('a2');
$sql = '';	
	if(!isset($_POST['fvr']) && !isset($_POST['exp'])){
		$array = array("data"=>'');
		$sql = "select *
		FROM ";
		$classe->msgSelect(false);
		$retCls=$classe->selectDatatables($sql);
		if( $retCls['retorno'] != "OK" ){
			trigger_error("Something is wrong!", E_USER_ERROR);  
		} else {
			$array['data'] = $retCls['dados'];
			echo json_encode($array,true);
		};  
	}
?>