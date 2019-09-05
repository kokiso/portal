<?php
function arrayToCsv(array &$fields, $delimiter) {
    $enclosure = '"';
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');

    $output = array();
    foreach ( $fields as $field ) {
        // Enclose fields containing $delimiter, $enclosure or whitespace
        if ( preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
            $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        }
        else {
            $output[] = $field;
        }
    }
    return implode( $delimiter, $output );
};
function downloadCsv($array){
	// Convert array to CSV string
		array_unshift($array,array(
        'id','nome_favorecido','nome_produto','auto','modelo_auto','status','data_agendamento'));
		$csv = "";
		foreach($array as $row) {
		    $csv .= arrayToCsv($row, ';') . "\n";
		}

		//Output csv file
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=estoque_natura.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		print $csv;
};
require("classPhp/conectaSqlServer.class.php");
$classe   = new conectaBd();
$classe->conecta('a1');
$sql = '';	
	if(!isset($_POST['fvr']) && !isset($_POST['exp'])){
		$array = array("data"=>'');
		$sql = "select REPLICATE('0', 4 - LEN(fvr.fvr_codigo ))+ RTrim(fvr.fvr_codigo) as fvr_codigo 
				,fvr.fvr_nome
				,cdd.cdd_nome
				,pe.pe_nome 
				,b.cntt_qtdauto
				,(select count(*) from grupomodeloproduto where gmp_codpei = a.gmp_codpei and gmp_status = 1) as  qtd_estoque
				,(select count(*) from grupomodeloproduto where gmp_codpei = a.gmp_codpei and gmp_status = 2) as  qtd_uso
				,(select count(*) from grupomodeloproduto where gmp_codpei = a.gmp_codpei ) as  qtd_total
				from GRUPOMODELOPRODUTO a
				LEFT OUTER JOIN CONTRATO b ON a.gmp_codcntt = b.cntt_codigo
				left outer join favorecido fvr on a.gmp_codpei = fvr.fvr_codigo
				LEFT OUTER JOIN cidade cdd ON fvr.fvr_codcdd = cdd.cdd_codigo
				left outer join pontoestoqueind pei on fvr.fvr_codigo = pei.pei_codfvr
				left outer join pontoestoque pe on pei.PEI_CODPE= pe.PE_CODIGO
				WHERE fvr.fvr_nome IS NOT NULL
				AND (a.gmp_codgp = 'AUT' OR a.gmp_codpei = 1)
				group by fvr.fvr_codigo,fvr.fvr_nome,b.cntt_qtdauto,a.gmp_codpei,pe.pe_nome,cdd.cdd_nome
				order by fvr.fvr_codigo";
		$classe->msgSelect(false);
		$retCls=$classe->selectDatatables($sql);
		if( $retCls['retorno'] != "OK" ){
			trigger_error("Something is wrong!", E_USER_ERROR);  
		} else {
			$array['data'] = $retCls['dados'];
			echo json_encode($array,true);
		};  
	}
	else if($_POST['exp'] = 1 && !isset($_POST['fvr'])){
		$sql = "select DISTINCT REPLICATE('0', 6 - LEN(GMP.GMP_CODIGO))+ RTrim(GMP.GMP_CODIGO) id
            ,COALESCE(fvr.fvr_nome,'SEM FAVORECIDO') as nome_favorecido              
			,GM.GM_NOME as nome_produto
			,REPLICATE('0', 6 - LEN(GMP.GMP_CODAUT))+ RTrim(GMP.GMP_CODAUT) as auto
			,(select gm_nome from grupomodelo where gm_codigo = GMPP.GMP_CODGM) as modelo_auto
			,CASE WHEN GMP.GMP_STATUS = 1 THEN 'ESTOQUE' WHEN GMP.GMP_STATUS = 2 THEN 'INSTALADO' WHEN GMP.GMP_STATUS = 3 THEN 'MANUTENCAO' 
			WHEN GMP.GMP_STATUS = 4 THEN 'RESERVADO' END  as status
			,COALESCE(CONVERT(VARCHAR(10),CNTP.CNTP_DTAGENDA,103),'NAO AGENDADO') as data_agendamento
			FROM GRUPOMODELOPRODUTO GMP
			LEFT OUTER JOIN FAVORECIDO fvr ON GMP.GMP_CODPEI = fvr.FVR_CODIGO
			LEFT OUTER JOIN GRUPOMODELOPRODUTO GMPP ON GMP.GMP_CODAUT = GMPP.GMP_CODIGO
			LEFT OUTER JOIN GRUPOMODELO GM ON GMP.GMP_CODGM = GM.GM_CODIGO
			LEFT OUTER JOIN CONTRATOPRODUTO CNTP ON GMP.GMP_CODAUT = CNTP.CNTP_CODGMP
			WHERE GMP.GMP_CODGP <> 'AUT'";
		$retCls=$classe->select($sql);
		if( $retCls['retorno'] != "OK" ){
			trigger_error("Something is wrong!", E_USER_ERROR);  
		} 

		downloadCsv($retCls['dados']);

	}
	else if(isset($_POST['fvr']))		
	{	
		$fvr_codigo = $_POST['fvr'];
		$sql = "select DISTINCT REPLICATE('0', 6 - LEN(GMP.GMP_CODIGO))+ RTrim(GMP.GMP_CODIGO) id             
				,GM.GM_NOME as modelo_auto
				,REPLICATE('0', 6 - LEN(GMPP.GMP_CODIGO))+ RTrim(GMPP.GMP_CODIGO) as produto_id
				,(select gm_nome from grupomodelo where gm_codigo = GMPP.GMP_CODGM) as nome_produto
				,CASE WHEN GMP.GMP_DTCONFIGURADO IS NULL THEN 'NAO CONFIGURADO' ELSE 'CONFIGURADO' END  as configuracao
				,CASE WHEN GMP.GMP_STATUS = 1 THEN 'ESTOQUE' WHEN GMP.GMP_STATUS = 2 THEN 'INSTALADO' WHEN GMP.GMP_STATUS = 3 THEN 'MANUTENCAO' 
				WHEN GMP.GMP_STATUS = 4 THEN 'RESERVADO' END  as status
				,COALESCE(CONVERT(VARCHAR(10),CNTP.CNTP_DTAGENDA,103),'NÃO AGENDADO') as data_agendamento
				FROM GRUPOMODELOPRODUTO GMP (NOLOCK)
                LEFT OUTER JOIN GRUPOMODELO GM ON GMP.GMP_CODGM = GM.GM_CODIGO
				LEFT OUTER JOIN GRUPOMODELOPRODUTO GMPP ON GMP.GMP_CODIGO = GMPP.GMP_CODAUT AND GMPP.GMP_CODGP <> 'AUT' and GMPP.GMP_CODGP = GM.GM_GPSERIEOBRIGATORIO
				LEFT OUTER JOIN CONTRATOPRODUTO CNTP ON GMP.GMP_CODAUT = CNTP.CNTP_CODGMP
				WHERE GMP.GMP_CODGP = 'AUT'
				AND GMP.GMP_CODPEI = ".$fvr_codigo.";";
		$retCls=$classe->selectDatatables($sql);
		if( $retCls['retorno'] != "OK" ){
			trigger_error("Something is wrong!", E_USER_ERROR);  
		} else {
			echo json_encode($retCls['dados'],true);
		} 
	}

?>