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
	if(isset($_POST['f10'])){
		$array = array("data"=>'');
		$sql = "SELECT A.GMP_CODIGO AS CODIGO,GM.GM_NOME AS DESCRICAO,A.GMP_NUMSERIE AS SERIE
			,GMP_SINCARD AS SINCARD
			,CASE WHEN A.GMP_DTCONFIGURADO IS NULL THEN 'NAO' ELSE 'SIM' END AS CFG
			FROM GRUPOMODELOPRODUTO A
			LEFT OUTER JOIN GRUPOMODELO GM ON A.GMP_CODGM=GM.GM_CODIGO
			WHERE A.GMP_CODPE = 'EST'";
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