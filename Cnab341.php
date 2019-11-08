<?php
session_start();
  if( isset($_POST["cnab341"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/selectRepetido.class.php");                                           

      $vldr       = new validaJson();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["cnab"]);
      $data       = "";
      $arrUpdt  = [];
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        //**
        //**
        //** PEGANDO OS DADOS DO LAYOUT
        $sql    = 
          "SELECT '109' LAY_CARTEIRA
                    ,'*' LAY_VARIACAOCARTEIRA
                    ,'12345678' LAY_CONVENIO
                    ,'C' LAY_QUEMEMITEBOLETO
                    ,1 LAY_FAIXAUSADO
                    ,BNC_CONTA CONTA
                    ,BNC_CONTADV BAN_CONTADV
                    ,BNC_AGENCIA BAN_AGENCIA
                    ,EMP_NOME
                    ,EMP_CNPJ EMP_CGC
                    ,BNC_CODIGO LAY_ID
                    ,BNC_CODBC LAY_SIGLA
              FROM BANCO
              LEFT OUTER JOIN EMP_CODIGO=BNC_CODEMP
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
            $linha        = 1;  
              };
          }
        else {
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
        }
        $carteira     = $row->BAN_CARTEIRA;
        $convenio     = $row->BAN_CONVENIO;
        $quememite    = $row->QUEMEMITEBOLETO;
        $nossonumero  = $row->BAN_SEQBOLETO;
        $conta        = $row->BAN_CONTA;
        $contadv      = $row->BAN_DVCONTA;
        $agencia      = $row->BAN_AGENCIA;
        $empresa      = $row->NUCRAZAOSOCIAL;
        $empresacnpj  = $row->NUCCNPJ;
        $linha        = 1;
        //**
        //** HEADER DO ARQUIVO
        $arquivo = 
            '0'                                                                             // 001   CODIGO DO REGISTRO        001/001
            .'1'                                                                            // 002   CODIGO DA REMESSA         002 002
            .str_pad('REMESSA',7)                                                           // 003   LITERAL DA REMESSA        003 009
            .'01'                                                                           // 004   CODIGO DO SERVICO         010 011
            .str_pad('COBRANCA',15)                                                         // 005   LITERAL DO SERVICO        012 026
            .str_pad(intval($agencia),4,'0',STR_PAD_LEFT)                                   // 006   AGENCIA                   027 030
            .'00'                                                                           // 007   COMPL. REGISTRO           031 032
            .str_pad(intval($conta),5,'0',STR_PAD_LEFT)                                     // 008   NRO DA CONTA CORR         033 037
            .$contadv                                                                       // 009   DIG. AG/CC (DAC)          038 038
            .str_pad('',8,' ')                                                              // 010   BRANCOS                   039 046
            .str_pad(substr($empresa,0,30),30,' ')                                                       // 011   NOME DO CONVENIADO        047 076
            .'341'                                                                          // 012   ID. DO BANCO              077 079
            .str_pad('BANCO ITAU SA',15,' ')                                                // 013   NOME DO BANCO             080 094
            .date('dmy')                                                                    // 014   DATA_PROCESSAMENTO        095 100
            .str_pad('',294,' ')                                                            // 015   FILLER                    101 394
            .str_pad($linha,6,'0',STR_PAD_LEFT)                                             // 016   NRO DE SEQUENCIA          395 400
            ."\r\n";
        //**
        //** BUSCA OS TÍTULOS QUE COMPOE O ARQUIVO
        $sql =
          "SELECT MOV_ID
                ,'01' CODCI1
                ,'00' CODCI2
                ,PGR_DOCTO
                ,PGR_DTDOCTO
                ,PGR_VENCTO
                ,0 MULTA
                ,0 JUROS
                ,0 ABATIMENTO
                ,0 DIASPROTESTO
                ,CAST(SUBSTRING(BAN_MSGBOLETO FROM 1 FOR 1000) AS VARCHAR(1000)) AS BAN_MSGBOLETO
                ,FVR_CNPJCPF 
                ,FVR_NOME
                ,FVR_ENDERECO
                ,FVR_BAIRRO BAIRRO
                ,FVR_CEP CEP
                ,CDD_NOME CIDADE
                ,CDD_CODEST CODEST
                ,PGR_LANCTO ENV_NOSSONUMERO
                ,FVR_FISJUR CLI_FISJUR
                ,MOV_BANCOFLUXO
            FROM PAGAR
            LEFT OUTER JOIN FAVORECIDO ON FIN_CODIGO=MOV_FAVORECIDO
            LEFT OUTER JOIN BANCO ON BAN_CODIGO=MOV_BANCOFLUXO
            WHERE MOV_ID IN (".$_GET['lista'].")";    
        $dados      = qlsrv_query($_SESSION['conn'],$sql);
        $ultimo     = 0;
        while ($row = sqlsrv_fetch_object($dados)) {
          $linha++;
          $tipofornecedor   = $row->FIN_TIPO=='1' ? '01' : '02';
          $valormulta       = (($row->MOV_LIQUIDO * $row->MULTA) / 100);
          $valorjuros       = (($row->MOV_LIQUIDO * $row->JUROS) / 100);
          if (($row->MOV_NOSSONUMERO=='') or (floatval($row->MOV_NOSSONUMERO)==0))
         {   $sql    = 'select (cast(ban_seqboleto as integer)+1) as nossonumero from BANCO where ban_codigo='.$row->MOV_BANCOFLUXO;
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
            $nossonumero    = str_pad($row->MOV_NOSSONUMERO, 8, '0',STR_PAD_LEFT);
          $guia             = str_pad($row->MOV_ID, 9, '0',STR_PAD_LEFT);
          $valordesconto    = 0;
          $dtlimitedesconto = '000000';
          $tipologradouro   = $row->ENDERECO;
          $tipologradouro   = substr(substr($tipologradouro,0,strpos($tipologradouro,' ')),0,10);
          $endereco         = $row->ENDERECO;
          if ($tipologradouro!='')
            $endereco = str_replace($tipologradouro,'',$endereco);
          //**
          //** GERANDO A LINHA DO TITULO
          $arquivo .=  
            '1'                                                                       // 001   CODIGO DO REGISTRO       001 001
            .'02'                                                                     // 002   TIPO DE INSCR.           002 003
            .str_pad($empresacnpj,14,' ')                                             // 003   CNPJ DA EMPRESA          004 017
            .str_pad(intval($agencia),4,'0',STR_PAD_LEFT)                             // 004   AGENCIA                  018 021
            .'00'                                                                     // 005   COMPL. REGISTRO          022 023
            .str_pad(intval($conta),5,'0',STR_PAD_LEFT)                               // 006   NRO DA CONTA CORR        024 028
            .str_pad($contadv,1,' ')                                                  // 007   DIG. AG/CC (DAC)         029 029
            .str_pad('',4,' ')                                                        // 008   COMPL. REGISTRO          030 033
            .'0000'                                                                   // 009   FIXO 0000 P/ REMESSA     034 037
            .str_pad($guia,25,' ')                                                    // 010   INF. DO CLIENTE 2        038 062
            .str_pad($nossonumero,8,' ')                                              // 011   INF. DO CLIENTE 2        063 070
            .str_pad(0,13,'0',STR_PAD_LEFT)                                           // 012   QTD. MOEDA 0=REAL        071 083
            .str_pad($carteira,3,'0')                                                 // 013   CODIGO DA CARTEIRA       084 086
            .str_pad($convenio,21,' ')                                                // 014   CODIGO DO CONVENIO       087 107
            .'I'                                                                      // 015   COD. DA CARTEIRA         108 108
            .'01'                                                                     // 016   IDENT. DA OCORRENC.      109 110 
            .str_pad($row->MOV_DOCTO,10,' ')                                          // 017   ID. DO TÍTULO NA EMPRES. 111 120
            .date('dmy',strtotime($row->MOV_DTVENCTO))                                // 018    VENCTO                  121 126
            .str_pad($row->MOV_LIQUIDO*100,13,'0',STR_PAD_LEFT)                       // 019   VALOR                    127 139
            .'341'                                                                    // 020   CÓDIGO DO BANCO          140 142
            .'00000'                                                                  // 021   CÓDIGO DA AGENCIA        143 147
            .'01'                                                                     // 022   ESPÉCIE DO TÍTULO        148 149
            .'N'                                                                      // 023   ACEITE                   150 150
            .date('dmy')                                                              // 024    DT.EMISSÃO              151 156
            .str_pad($row->CODCI1,2,' ')                                              // 025   1a INSTRUCAO             157 158
            .str_pad($row->CODCI2,2,' ')                                              // 026   2a INSTRUCAO             159 160
            .str_pad(($valormulta*100),13,'0',STR_PAD_LEFT)                           // 027   VALOR JUROS              161 173
            .$dtlimitedesconto                                                        // 028   DESCONTO ATÉ D/M/A       174 179
            .str_pad(($valormulta*100),13,'0',STR_PAD_LEFT)                           // 029   VALOR DESCONTO           180 192
            .'0000000000000'                                                          // 030   VALOR IOF                193 205
            .str_pad(($row->ABATIMENTO*100),13,'0',STR_PAD_LEFT)                      // 031   VALOR ABATIMENTO         206 218
            .$tipofornecedor                                                          // 032   COD.INSCR.SACADO         219 220
            .str_pad($row->CNPJ_CPF,14,'0',STR_PAD_LEFT)                              // 033   SACADO - CNPJ/CPF        221 234
            .str_pad(substr($row->FIN_RAZAOSOCIAL,0,30),30,' ')                       // 034   SACADO - NOME            235 264
            .str_pad($tipologradouro,10,' ')                                          // 035   BAIRRO                   265 274
            .str_pad(substr($endereco,0,40),40,' ')                                   // 036   SACADO - ENDERECO        275 314
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
            ."\r\n";
          //**
          //************************************************************
          //** INSERINDO OBSERVACAO NO TÍTULO (SOMENTE EM CASO DE MULTA)
          //************************************************************
          if ($valormulta>0){
            $linha++;
            $arquivo.=
               '6'                                                                    // 001  Tipo do registro (6)
              .'2'                                                                    // 002  Código do Layout (2)
              .str_pad('APOS '.date('d/m/Y',strtotime($row->MOV_DTVENCTO)).' COBRAR MULTA DE R$ '.str_replace('.',',',$valormulta),69,' ')          // 003  Instrução do bloqueto
              .str_pad('',69,' ')                                                     // 004  Instrução do bloqueto
              .str_pad('',69,' ')                                                     // 005  Instrução do bloqueto
              .str_pad('',69,' ')                                                     // 006  Instrução do bloqueto
              .str_pad('',69,' ')                                                     // 007  Instrução do bloqueto
              .str_pad('',47,' ')                                                     // 008  brancos
              .str_pad($linha,6,'0',STR_PAD_LEFT)                                     // 009  Seq do Registro
              ."\r\n";
          }
        }
        //**
        //** RODAPE DO ARQUIVO
        $linha++;
        $arquivo .=
           '9'                                                                        // 001   CODIGO DO REGISTRO       001 001
          .str_pad('',393,' ')                                                        // 002   BRANCOS                  002 394
          .str_pad($linha,6,'0',STR_PAD_LEFT)                                         // 003   NUMERO DE SEQUENCIA       395 400
          ."\r\n";
        //**
        //**
        //** DEVOLVE A STRING COM O ARQUIVO
        header('Content-type: text/plain');
        header( 'Content-Length: ' . strlen( $arquivo ) ); 
        header('Content-Disposition: attachment; filename="cobranca341.txt"');
        echo $arquivo;
      };  
        } catch(Exception $e ){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]'; 
    };    
    echo $retorno;
    exit;
  };
 