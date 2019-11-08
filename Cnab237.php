<?php
session_start();
  if( isset($_POST["cnab237"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/selectRepetido.class.php");       						                      
      $vldr       = new validaJson();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["cnab"]);
      $data       = "";
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
    		$sql    = 
		    "SELECT '109' AS BAN_CARTEIRA
		              ,'*' AS LAY_VARIACAOCARTEIRA
		              ,'12345678' AS BAN_CONVENIO LAY_CONVENIO
		              ,'C' as QUEMEMITEBOLETO
		              ,1 AS LAY_FAIXAUSADO
		              ,BNC_CONTA AS BAN_CONTA
		              ,BNC_CONTADV AS BAN_DVCONTA
		              ,BNC_AGENCIA AS BAN_AGENCIA
		              ,EMP_NOME AS NUCRAZAOSOCIAL
		              ,EMP_CNPJ AS NUCCNPJ
		              ,BNC_CODIGO
		              ,BNC_CODBC
		        FROM BANCO
		        LEFT OUTER JOIN EMPRESA ON EMP_CODIGO=BNC_CODEMP
		     WHERE BNC_CODIGO='".$_GET['banco'];
		  $classe->msgSelect(false);
		  $retCls=$classe->selectAssoc($sql);
			if( $retCls['retorno']=="OK" ){	
			        $erro = "ok";            
			        $tbl  = $retCls["dados"];
			        foreach( $tbl as $row ){
		           		$carteira     = $row["BAN_CARTEIRA"];
						$convenio     = $row["BAN_CONVENIO"];
				        $quememite    = $row["QUEMEMITEBOLETO"];
				    	$nossonumero  = $row["BAN_SEQBOLETO"];
						$conta        = $row["BAN_CONTA"];
						$contadv      = $row["BAN_DVCONTA"];
						$agencia      = $row["BAN_AGENCIA"];
						$empresa      = $row["NUCRAZAOSOCIAL"];
						$empresacnpj  = $row["NUCCNPJ"];
			        };
			    }
		    else {
	      		$retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
	  		}
		  //$row          = fbselect($sql);
		  $linha        = 1;
		  //**
		  //** HEADER DO ARQUIVO
		  $arquivo = '0'                                                                // 001   CODIGO DO REGISTRO       001/001
		    . '1'                                                                       // 002   CODIGO DA REMESSA        002 002
		    . 'REMESSA'                                                                 // 003   LITERAL DA REMESSA       003 009
		    . '01'                                                                      // 004   CODIGO DO SERVICO        010 011
		    . str_pad('COBRANCA', 15, ' ')                                              // 005   LITERAL DO SERVICO       012 026
		    . str_pad($convenio, 20, '0', STR_PAD_LEFT)                                 // 006 CÓDIGO DA EMPRESA          027 046
		    . str_pad(substr($empresa,0,30), 30, ' ')                                   // 006   NOME DO CONVENIADO       047 076
		    . '237'                                                                     // 007   ID. DO BANCO             077 079
		    . str_pad('BRADESCO', 15, ' ')                                              // 008   NOME DO BANCO            080 094
		    . date('dmy')                                                               // 009   DATA_PROCESSAMENTO       095 100
		    . str_pad('', 8, ' ')                                                       // 010   BRANCOS                  101 108
		    . 'MX'                                                                      // 011   ID. ARQUIVO              109 110
		    . str_pad('1', 7, '0', STR_PAD_LEFT)                                        // 012   SEQ. ARQUIVO             111 117
		    . str_pad('', 277, ' ')                                                     // 013   BRANCOS                  118 394
		    . str_pad($linha, 6, '0', STR_PAD_LEFT)                                     // 014   NRO DE SEQUENCIA         395 400
		    . "\r\n";
		  //**
		  //**
		  //** BUSCA OS TÍTULOS QUE COMPOE O ARQUIVO
		  $sql =
		    "SELECT PGR_LANCTO GUIA
		              ,'01' ENV_CODCI1
		              ,'' ENV_CODCI2
		              ,PGR_DOCTO DOCTO
		              ,PGR_DTDOCTO DTDOCTO
		              ,PGR_VENCTO VENCIM
		              ,ABS(PGR_VLRPARCELA) VALOR
		              ,0 ENV_MULTA
		              ,0 ENV_JUROS
		              ,0 ENV_ABATIMENTO
		              ,0 ENV_DIASPROTESTO
		              ,'' ENV_MSG01
		              ,'' ENV_MSG02
		              ,'' ENV_MSG03
		              ,'' ENV_MSG04
		              ,FVR_CNPJCPF CGC
		              ,FVR_NOME DESCLI
		              ,FVR_ENDERECO AS ENDERECO
		              ,FVR_BAIRRO BAIRRO
		              ,FVR_CEP CEP
		              ,CDD_NOME CIDADE
		              ,CDD_CODEST CODEST
		              ,PGR_LANCTO ENV_NOSSONUMERO
		              ,FVR_FISJUR CLI_FISJUR
		          FROM PAGAR E
		          LEFT OUTER JOIN FAVORECIDO ON FVR_CODIGO=PGR_CODFVR
		          LEFT OUTER JOIN CIDADE ON CDD_CODIGO=FVR_CODCDD
		      WHERE PGR_LANCTO IN (".$_GET['lista'].")";    
		  $dados      = sqlsrv_query($_SESSION['conn'],$sql);
		  $ultimo = 0;
		  while ($row = sqlsrv_fetch_object($dados)) {
		    //**
		    //** PREPARANDO ALGUMAS INFORMAÇÕES PARA GERAR O REGISTRO
		    $linha++;
		    $guia          = str_pad($row->MOV_ID, 8, '0', STR_PAD_LEFT);
		    if (($row->ENV_NOSSONUMERO=='') or (floatval($row->ENV_NOSSONUMERO)==0)){
		    	$sql    = 'select (cast(ban_seqboleto as integer)+1) as nossonumero from BANCO where ban_codigo='.$row->MOV_BANCOFLUXO;
		  		$classe->msgSelect(false);
			  		$retCls=$classe->selectAssoc($sql);
				if( $retCls['retorno']=="OK" ){
				        $erro = "ok";            
				        $tbl  = $retCls["dados"];
				        foreach( $tbl as $row ){
			           		$aux = $row['nossonumero'];
				        };
				    }
			    else {
		      		$retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
		  		}
		      	$nossonumero = str_pad($aux->NOSSONUMERO, 8, '0',STR_PAD_LEFT);

	  	      	$sql = 'update BANCO set ban_seqboleto=(cast(ban_seqboleto as integer)+1) where BNC_CODIGO='.$row->MOV_BANCOFLUXO;
	  	      	$sql .= ""
	  	      	$sql .= "update PAGAR set mov_nossonumero='".$nossonumero."' where mov_id=".$row->MOV_ID
  	      		array_push($arrUpdt,$sql);            
       		 	$atuBd = true;
		        if( $atuBd ){
		          if( count($arrUpdt) >0 ){
		            $retCls=$classe->cmd($arrUpdt);
		            if( $retCls['retorno']=="OK" ){
		              $retorno='[{ "retorno":"OK"
		                          ,"dados":""
		                          ,"erro":"'.count($arrUpdt).' REGISTRO(s) ATUALIZADO(s)!"}]'; 
		            } else {
		              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
		            };  
		          } else {
		            $retorno='[{"retorno":"OK","dados":"","erro":"NENHUM REGISTRO CADASTRADO!"}]';
		          };  
		        };
		      
		    } else
		      $nossonumero    = str_pad($row->ENV_NOSSONUMERO, 8, '0',STR_PAD_LEFT);
		    $nossonumerodv = modulo_11($nossonumero);
		    $tipofornecedor   = $row->FIN_TIPO=='1' ? '01' : '02';
		    $cnpj             = str_pad($row->CNPJ_CPF, 14, '0', STR_PAD_LEFT);
		    $valormulta       = (($row->MOV_LIQUIDO * $row->MULTA) / 100);
		    $valorjuros       = (($row->MOV_LIQUIDO * $row->JUROS) / 100);
		    $valordesconto    = 0;
		    $dtlimitedesconto = '000000';
		    //**
		    //** GERANDO A LINHA DO TITULO
		    $arquivo .=
		      '1'                                                                       // 001   CODIGO DO REGISTRO       001 001
		      .'00000'                                                                  // 002   AGENCIA                  002 006  (debito em conta)
		      .'0'                                                                      // 003   DIG. AG/CC (DAC)         007 007  (debito em conta)
		      .'00000'                                                                  // 004   RAZÃO DA CONTA           008 012
		      .'0000000'                                                                // 006   NRO DA CONTA CORR        013 019  (debito em conta)
		      .'0'                                                                      // 007   DIG DA CONTA CORR        020 020  (debito em conta)
		      .'0'                                                                      // 008   ZERO                     021 021  (identificação da empresa)
		      .str_pad($carteira,3,'0',STR_PAD_LEFT)                                    // 009   CARTEIRA                 022 024  (identificação da empresa)
		      .str_pad($agencia,5,'0',STR_PAD_LEFT)                                     // 010   AGENCIA                  025 029  (identificação da empresa)
		      .str_pad($conta,7,'0',STR_PAD_LEFT)                                       // 011   CONTA                    030 036  (identificação da empresa)
		      .str_pad($contadv,1,' ')                                                  // 012   DV CONTA                 037 037  (identificação da empresa)
		      .str_pad($guia,25,' ')                                                    // 013   USO DA EMPRESA           038 062
		      .'000'                                                                    // 014   CÓDIGO DO BANCO          063 065  (somente DDA)
		      .($row->MULTA>0?'2':'0')                                                  // 015   2=%, 1=R$                066 066
		      .($row->MULTA>0?str_pad(str_replace('.','',($row->MULTA*100)),4,'0',STR_PAD_LEFT):'0000')// 016   % DESC.                  067 070
		      .str_pad(intval($nossonumero),11,'0',STR_PAD_LEFT)                        // 017   Nosso número             071 081
		      .str_pad($nossonumerodv,1,' ')                                            // 018   Digito Nosso número      082 082
		      .'0000000000'                                                             // 019   Desconto Bonif./Dia      083 092
		      .$quememite                                                               // 020   1=Banco emite, 2=Não     093 093
		      .' '                                                                      // 021   S=Registra cobrança      094 094
		      .str_pad('',10,' ')                                                       // 022   Brancos                  095 104
		      .' '                                                                      // 023   Rateio                   105 105 (Preencher com R, somente se a empresa participa de RATEIO)
		      .' '                                                                      // 024   0=Não emite aviso débito 106 106
		      .'  '                                                                     // 025   Brancos                  107 108
		      .'01'                                                                     // 026   Códigos da ocorrência    109 110
		      .str_pad(substr($row->MOV_DOCTO,0,10),10,' ')                             // 027   Número do documento      111 120
		      .date('dmy',strtotime($row->MOV_DTVENCTO))                                // 028    Vencto                  121 126
		      .str_pad(($row->MOV_LIQUIDO*100),13,0,STR_PAD_LEFT)                       // 029   Valor                    127 139
		      .'000'                                                                    // 030   Banco                    140 142
		      .'00000'                                                                  // 031   Agencia                  143 147
		      .'01'                                                                     // 022   Tipo do Docto            148 149
		      .'N'                                                                      // 023   Aceite                   150 150
		      .date('dmy')                                                              // 024    DT.EMISSÃO              151 156
		      .str_pad($row->CODCI1,2,' ')                                              // 025   1a INSTRUCAO             157 158
		      .str_pad($row->CODCI2,2,' ')                                              // 026   2a INSTRUCAO             159 160
		      .str_pad($valormulta,13,'0',STR_PAD_LEFT)                                 // 027   VALOR JUROS              161 173
		      .$dtlimitedesconto                                                        // 028   DESCONTO ATÉ D/M/A       174 179
		      .str_pad($valordesconto,13,'0',STR_PAD_LEFT)                              // 029   VALOR DESCONTO           180 192
		      .'0000000000000'                                                          // 030   VALOR IOF                193 205
		      .'0000000000000'                                                          // 031   VALOR ABATIMENTO         206 218
		      .$tipofornecedor                                                          // 032   COD.INSCR.SACADO         219 220
		      .$cnpj                                                                    // 033   SACADO - CNPJ/CPF        221 234
		      .str_pad(substr($row->FIN_RAZAOSOCIAL,0,30),30,' ')                       // 034   SACADO - NOME            235 264
		      .str_pad('',10,' ')                                                       // 035   BAIRRO                   265 274
		      .str_pad(substr($row->ENDERECO,0,40),40,' ')                              // 036   SACADO - ENDERECO        275 314
		      .str_pad(substr($row->FIN_BAIRRO,0,12),12,' ')                            // 037   BAIRRO                   315 326
		      .str_pad(str_replace('-','',$row->FIN_CEP),8,' ')                         // 038   SACADO - CEP             327 334
		      .str_pad(substr($row->FIN_CIDADE,0,15),15,' ')                            // 039   SACADO - CIDADE          335 349
		      .str_pad($row->FIN_UF,2,' ')                                              // 040   SACADO - UF              350 351
		      .str_pad('',30,' ')                                                       // 041   NOME SACADOR/AVALISTA    352 381
		      .str_pad('',4,' ')                                                        // 042   BRANCOS                  382 385
		      .'000000'                                                                 // 043   DATA DE MORA             386 391
		      .str_pad($row->DIASPROTESTO,2,'0',STR_PAD_LEFT)                           // 044   QTDADE DE DIAS PRA PROT. 392 393
		      .' '                                                                      // 045   BRANCO                   394 394
		      .str_pad($linha,6,'0',STR_PAD_LEFT)                                       // 046   SEQ. DE REGISTRO         395 400
		      . "\r\n";
		    //**
		    //** INSERINDO OBSERVACAO NO TÍTULO (SOMENTE EM CASO DE MULTA)
		    if ($row->BAN_MSGBOLETO != '') {
		      $msgboleto = explode(';',str_replace(array("\r\n","\r","\n")," ",utf8_decode($row->BAN_MSGBOLETO)));
		      $linha++;
		      
		      $arquivo.= '2';                                                                   // 001  Tipo do registro (6)
		      $arquivo.= str_pad(((count($msgboleto)>0)?$msgboleto[0]:''), 80, ' ');
		      $arquivo.= str_pad(((count($msgboleto)>1)?$msgboleto[1]:''), 80, ' ');
		      $arquivo.= str_pad(((count($msgboleto)>2)?$msgboleto[2]:''), 80, ' ');
		      $arquivo.= str_pad(((count($msgboleto)>3)?$msgboleto[3]:''), 80, ' ');
		      $arquivo.= str_pad('', 45, ' ');                                                  // 008  brancos
		      $arquivo.= str_pad($carteira,3,'0',STR_PAD_LEFT);                                 
		      $arquivo.= str_pad($agencia,5,'0',STR_PAD_LEFT);                                  
		      $arquivo.= str_pad($conta,7,STR_PAD_LEFT);                                        
		      $arquivo.= str_pad($contadv,1,' ');                                                              
		      $arquivo.= '00000000000';
		      $arquivo.= '0';
		      $arquivo.= str_pad($linha,6,'0',STR_PAD_LEFT);                                    
		      $arquivo.= "\r\n";
		    }
		  }
		  //**
		  //** RODAPE DO ARQUIVO
		  $linha++;
		  $arquivo .=
		    '9'                                                                        // 001   CODIGO DO REGISTRO       001 001
		    . str_pad('', 393, ' ')                                                    // 002   BRANCOS                  002 394
		    . str_pad($linha, 6, '0', STR_PAD_LEFT)                                    // 003   NUMERO DE SEQUENCIA       395 400
		    . "\r\n";
		  //**
		  //** DEVOLVE A STRING COM O ARQUIVO
		  header('Content-type: text/plain');
		  header('Content-Length: ' . strlen($arquivo));
		  header('Content-Disposition: attachment; filename="cobranca237.txt"');
		  echo $arquivo;

      };	
      	} catch(Exception $e ){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]'; 
    };    
    echo $retorno;
    exit;
  };  
  //**
  //**
  function modulo_11($num, $base = 9, $r = 0) {
    $soma = 0;
    $fator = 2;
    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
      // pega cada numero isoladamente
      $numeros[$i] = substr($num, $i - 1, 1);
      // Efetua multiplicacao do numero pelo falor
      $parcial[$i] = $numeros[$i] * $fator;
      // Soma dos digitos
      $soma += $parcial[$i];
      if ($fator == $base) {
        // restaura fator de multiplicacao para 2
        $fator = 1;
      }
      $fator++;
    }
    /* Calculo do modulo 11 */
    if ($r == 0) {
      $soma *= 10;
      $digito = $soma % 11;
      if ($digito == 10) {
        $digito = 0;
      }
      return $digito;
    } elseif ($r == 1) {
      $resto = $soma % 11;
      return $resto;
    }
  }
  //**
  //**
  //** PEGANDO OS DADOS DO LAYOUT
 //  $sql    = 
 //    "SELECT '109' AS BAN_CARTEIRA
 //              ,'*' AS LAY_VARIACAOCARTEIRA
 //              ,'12345678' AS BAN_CONVENIO LAY_CONVENIO
 //              ,'C' as QUEMEMITEBOLETO
 //              ,1 AS LAY_FAIXAUSADO
 //              ,BNC_CONTA AS BAN_CONTA
 //              ,BNC_CONTADV AS BAN_DVCONTA
 //              ,BNC_AGENCIA AS BAN_AGENCIA
 //              ,EMP_NOME AS NUCRAZAOSOCIAL
 //              ,EMP_CNPJ AS NUCCNPJ
 //              ,BNC_CODIGO
 //              ,BNC_CODBC
 //        FROM BANCO
 //        LEFT OUTER JOIN EMPRESA ON EMP_CODIGO=BNC_CODEMP
 //     WHERE BNC_CODIGO='".$_GET['banco'];
 //  $classe->msgSelect(false);
 //  $retCls=$classe->selectAssoc($sql);
	// if( $retCls['retorno']=="OK" ){
	//         $erro = "ok";            
	//         $tbl  = $retCls["dados"];
	//         foreach( $tbl as $row ){
 //           		$carteira     = $row["BAN_CARTEIRA"];
	// 			$convenio     = $row["BAN_CONVENIO"];
	// 	        $quememite    = $row["QUEMEMITEBOLETO"];
	// 	    	$nossonumero  = $row["BAN_SEQBOLETO"];
	// 			$conta        = $row["BAN_CONTA"];
	// 			$contadv      = $row["BAN_DVCONTA"];
	// 			$agencia      = $row["BAN_AGENCIA"];
	// 			$empresa      = $row["NUCRAZAOSOCIAL"];
	// 			$empresacnpj  = $row["NUCCNPJ"];
	//         };
	//     }
	//     else {
 //      		$retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
 //  		}
 //  //$row          = fbselect($sql);
 //  $linha        = 1;
 //  //**
 //  //** HEADER DO ARQUIVO
 //  $arquivo = '0'                                                                // 001   CODIGO DO REGISTRO       001/001
 //    . '1'                                                                       // 002   CODIGO DA REMESSA        002 002
 //    . 'REMESSA'                                                                 // 003   LITERAL DA REMESSA       003 009
 //    . '01'                                                                      // 004   CODIGO DO SERVICO        010 011
 //    . str_pad('COBRANCA', 15, ' ')                                              // 005   LITERAL DO SERVICO       012 026
 //    . str_pad($convenio, 20, '0', STR_PAD_LEFT)                                 // 006 CÓDIGO DA EMPRESA          027 046
 //    . str_pad(substr($empresa,0,30), 30, ' ')                                   // 006   NOME DO CONVENIADO       047 076
 //    . '237'                                                                     // 007   ID. DO BANCO             077 079
 //    . str_pad('BRADESCO', 15, ' ')                                              // 008   NOME DO BANCO            080 094
 //    . date('dmy')                                                               // 009   DATA_PROCESSAMENTO       095 100
 //    . str_pad('', 8, ' ')                                                       // 010   BRANCOS                  101 108
 //    . 'MX'                                                                      // 011   ID. ARQUIVO              109 110
 //    . str_pad('1', 7, '0', STR_PAD_LEFT)                                        // 012   SEQ. ARQUIVO             111 117
 //    . str_pad('', 277, ' ')                                                     // 013   BRANCOS                  118 394
 //    . str_pad($linha, 6, '0', STR_PAD_LEFT)                                     // 014   NRO DE SEQUENCIA         395 400
 //    . "\r\n";
 //  //**
 //  //**
 //  //** BUSCA OS TÍTULOS QUE COMPOE O ARQUIVO
 //  $sql =
 //    "SELECT PGR_LANCTO GUIA
 //              ,'01' ENV_CODCI1
 //              ,'' ENV_CODCI2
 //              ,PGR_DOCTO DOCTO
 //              ,PGR_DTDOCTO DTDOCTO
 //              ,PGR_VENCTO VENCIM
 //              ,ABS(PGR_VLRPARCELA) VALOR
 //              ,0 ENV_MULTA
 //              ,0 ENV_JUROS
 //              ,0 ENV_ABATIMENTO
 //              ,0 ENV_DIASPROTESTO
 //              ,'' ENV_MSG01
 //              ,'' ENV_MSG02
 //              ,'' ENV_MSG03
 //              ,'' ENV_MSG04
 //              ,FVR_CNPJCPF CGC
 //              ,FVR_NOME DESCLI
 //              ,FVR_ENDERECO AS ENDERECO
 //              ,FVR_BAIRRO BAIRRO
 //              ,FVR_CEP CEP
 //              ,CDD_NOME CIDADE
 //              ,CDD_CODEST CODEST
 //              ,PGR_LANCTO ENV_NOSSONUMERO
 //              ,FVR_FISJUR CLI_FISJUR
 //          FROM PAGAR E
 //          LEFT OUTER JOIN FAVORECIDO ON FVR_CODIGO=PGR_CODFVR
 //          LEFT OUTER JOIN CIDADE ON CDD_CODIGO=FVR_CODCDD
 //      WHERE PGR_LANCTO IN (".$_GET['lista'].")";    
 //  $dados      = sqlsrv_query($_SESSION['conn'],$sql);
 //  $ultimo = 0;
 //  while ($row = sqlsrv_fetch_object($dados)) {
 //    //**
 //    //** PREPARANDO ALGUMAS INFORMAÇÕES PARA GERAR O REGISTRO
 //    $linha++;
 //    $guia          = str_pad($row->MOV_ID, 8, '0', STR_PAD_LEFT);
 //    if (($row->MOV_NOSSONUMERO=='') or (floatval($row->MOV_NOSSONUMERO)==0)){
 //      $aux = fbselect('select (cast(ban_seqboleto as integer)+1) as nossonumero from finbanco where ban_codigo='.$row->MOV_BANCOFLUXO);
 //      fbselect('update BANCO set ban_seqboleto=(cast(ban_seqboleto as integer)+1) where BNC_CODIGO='.$row->MOV_BANCOFLUXO);
 //      $nossonumero = str_pad($aux->NOSSONUMERO, 8, '0',STR_PAD_LEFT);
 //      fbselect("update finmovto set mov_nossonumero='".$nossonumero."' where mov_id=".$row->MOV_ID);
      
 //    } else
 //      $nossonumero    = str_pad($row->MOV_NOSSONUMERO, 8, '0',STR_PAD_LEFT);
 //    $nossonumerodv = modulo_11($nossonumero);
 //    $tipofornecedor   = $row->FIN_TIPO=='1' ? '01' : '02';
 //    $cnpj             = str_pad($row->CNPJ_CPF, 14, '0', STR_PAD_LEFT);
 //    $valormulta       = (($row->MOV_LIQUIDO * $row->MULTA) / 100);
 //    $valorjuros       = (($row->MOV_LIQUIDO * $row->JUROS) / 100);
 //    $valordesconto    = 0;
 //    $dtlimitedesconto = '000000';
 //    //**
 //    //** GERANDO A LINHA DO TITULO
 //    $arquivo .=
 //      '1'                                                                       // 001   CODIGO DO REGISTRO       001 001
 //      .'00000'                                                                  // 002   AGENCIA                  002 006  (debito em conta)
 //      .'0'                                                                      // 003   DIG. AG/CC (DAC)         007 007  (debito em conta)
 //      .'00000'                                                                  // 004   RAZÃO DA CONTA           008 012
 //      .'0000000'                                                                // 006   NRO DA CONTA CORR        013 019  (debito em conta)
 //      .'0'                                                                      // 007   DIG DA CONTA CORR        020 020  (debito em conta)
 //      .'0'                                                                      // 008   ZERO                     021 021  (identificação da empresa)
 //      .str_pad($carteira,3,'0',STR_PAD_LEFT)                                    // 009   CARTEIRA                 022 024  (identificação da empresa)
 //      .str_pad($agencia,5,'0',STR_PAD_LEFT)                                     // 010   AGENCIA                  025 029  (identificação da empresa)
 //      .str_pad($conta,7,'0',STR_PAD_LEFT)                                       // 011   CONTA                    030 036  (identificação da empresa)
 //      .str_pad($contadv,1,' ')                                                  // 012   DV CONTA                 037 037  (identificação da empresa)
 //      .str_pad($guia,25,' ')                                                    // 013   USO DA EMPRESA           038 062
 //      .'000'                                                                    // 014   CÓDIGO DO BANCO          063 065  (somente DDA)
 //      .($row->MULTA>0?'2':'0')                                                  // 015   2=%, 1=R$                066 066
 //      .($row->MULTA>0?str_pad(str_replace('.','',($row->MULTA*100)),4,'0',STR_PAD_LEFT):'0000')// 016   % DESC.                  067 070
 //      .str_pad(intval($nossonumero),11,'0',STR_PAD_LEFT)                        // 017   Nosso número             071 081
 //      .str_pad($nossonumerodv,1,' ')                                            // 018   Digito Nosso número      082 082
 //      .'0000000000'                                                             // 019   Desconto Bonif./Dia      083 092
 //      .$quememite                                                               // 020   1=Banco emite, 2=Não     093 093
 //      .' '                                                                      // 021   S=Registra cobrança      094 094
 //      .str_pad('',10,' ')                                                       // 022   Brancos                  095 104
 //      .' '                                                                      // 023   Rateio                   105 105 (Preencher com R, somente se a empresa participa de RATEIO)
 //      .' '                                                                      // 024   0=Não emite aviso débito 106 106
 //      .'  '                                                                     // 025   Brancos                  107 108
 //      .'01'                                                                     // 026   Códigos da ocorrência    109 110
 //      .str_pad(substr($row->MOV_DOCTO,0,10),10,' ')                             // 027   Número do documento      111 120
 //      .date('dmy',strtotime($row->MOV_DTVENCTO))                                // 028    Vencto                  121 126
 //      .str_pad(($row->MOV_LIQUIDO*100),13,0,STR_PAD_LEFT)                       // 029   Valor                    127 139
 //      .'000'                                                                    // 030   Banco                    140 142
 //      .'00000'                                                                  // 031   Agencia                  143 147
 //      .'01'                                                                     // 022   Tipo do Docto            148 149
 //      .'N'                                                                      // 023   Aceite                   150 150
 //      .date('dmy')                                                              // 024    DT.EMISSÃO              151 156
 //      .str_pad($row->CODCI1,2,' ')                                              // 025   1a INSTRUCAO             157 158
 //      .str_pad($row->CODCI2,2,' ')                                              // 026   2a INSTRUCAO             159 160
 //      .str_pad($valormulta,13,'0',STR_PAD_LEFT)                                 // 027   VALOR JUROS              161 173
 //      .$dtlimitedesconto                                                        // 028   DESCONTO ATÉ D/M/A       174 179
 //      .str_pad($valordesconto,13,'0',STR_PAD_LEFT)                              // 029   VALOR DESCONTO           180 192
 //      .'0000000000000'                                                          // 030   VALOR IOF                193 205
 //      .'0000000000000'                                                          // 031   VALOR ABATIMENTO         206 218
 //      .$tipofornecedor                                                          // 032   COD.INSCR.SACADO         219 220
 //      .$cnpj                                                                    // 033   SACADO - CNPJ/CPF        221 234
 //      .str_pad(substr($row->FIN_RAZAOSOCIAL,0,30),30,' ')                       // 034   SACADO - NOME            235 264
 //      .str_pad('',10,' ')                                                       // 035   BAIRRO                   265 274
 //      .str_pad(substr($row->ENDERECO,0,40),40,' ')                              // 036   SACADO - ENDERECO        275 314
 //      .str_pad(substr($row->FIN_BAIRRO,0,12),12,' ')                            // 037   BAIRRO                   315 326
 //      .str_pad(str_replace('-','',$row->FIN_CEP),8,' ')                         // 038   SACADO - CEP             327 334
 //      .str_pad(substr($row->FIN_CIDADE,0,15),15,' ')                            // 039   SACADO - CIDADE          335 349
 //      .str_pad($row->FIN_UF,2,' ')                                              // 040   SACADO - UF              350 351
 //      .str_pad('',30,' ')                                                       // 041   NOME SACADOR/AVALISTA    352 381
 //      .str_pad('',4,' ')                                                        // 042   BRANCOS                  382 385
 //      .'000000'                                                                 // 043   DATA DE MORA             386 391
 //      .str_pad($row->DIASPROTESTO,2,'0',STR_PAD_LEFT)                           // 044   QTDADE DE DIAS PRA PROT. 392 393
 //      .' '                                                                      // 045   BRANCO                   394 394
 //      .str_pad($linha,6,'0',STR_PAD_LEFT)                                       // 046   SEQ. DE REGISTRO         395 400
 //      . "\r\n";
 //    //**
 //    //** INSERINDO OBSERVACAO NO TÍTULO (SOMENTE EM CASO DE MULTA)
 //    if ($row->BAN_MSGBOLETO != '') {
 //      $msgboleto = explode(';',str_replace(array("\r\n","\r","\n")," ",utf8_decode($row->BAN_MSGBOLETO)));
 //      $linha++;
      
 //      $arquivo.= '2';                                                                   // 001  Tipo do registro (6)
 //      $arquivo.= str_pad(((count($msgboleto)>0)?$msgboleto[0]:''), 80, ' ');
 //      $arquivo.= str_pad(((count($msgboleto)>1)?$msgboleto[1]:''), 80, ' ');
 //      $arquivo.= str_pad(((count($msgboleto)>2)?$msgboleto[2]:''), 80, ' ');
 //      $arquivo.= str_pad(((count($msgboleto)>3)?$msgboleto[3]:''), 80, ' ');
 //      $arquivo.= str_pad('', 45, ' ');                                                  // 008  brancos
 //      $arquivo.= str_pad($carteira,3,'0',STR_PAD_LEFT);                                 
 //      $arquivo.= str_pad($agencia,5,'0',STR_PAD_LEFT);                                  
 //      $arquivo.= str_pad($conta,7,STR_PAD_LEFT);                                        
 //      $arquivo.= str_pad($contadv,1,' ');                                                              
 //      $arquivo.= '00000000000';
 //      $arquivo.= '0';
 //      $arquivo.= str_pad($linha,6,'0',STR_PAD_LEFT);                                    
 //      $arquivo.= "\r\n";
 //    }
 //  }
 //  //**
 //  //** RODAPE DO ARQUIVO
 //  $linha++;
 //  $arquivo .=
 //    '9'                                                                        // 001   CODIGO DO REGISTRO       001 001
 //    . str_pad('', 393, ' ')                                                    // 002   BRANCOS                  002 394
 //    . str_pad($linha, 6, '0', STR_PAD_LEFT)                                    // 003   NUMERO DE SEQUENCIA       395 400
 //    . "\r\n";
 //  //**
 //  //** DEVOLVE A STRING COM O ARQUIVO
 //  header('Content-type: text/plain');
 //  header('Content-Length: ' . strlen($arquivo));
 //  header('Content-Disposition: attachment; filename="cobranca237.txt"');
 //  echo $arquivo;
