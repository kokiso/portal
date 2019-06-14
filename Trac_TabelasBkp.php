<?php
  session_start();
  if( isset($_POST["bkp"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["bkp"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $ql       = "";
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $rotina   = $lote[0]->rotina;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        ////////////////////////////////////////////
        //          Dados para JavaScript AGENDA  //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpAge" ){
          $sql="SELECT AGE_ID"
          ."           ,CAST(AGE_DATA AS VARCHAR(10)) AS AGE_DATA"
          ."           ,CASE WHEN A.AGE_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.AGE_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.AGE_ACAO='E' THEN 'EXC' END AS AGE_ACAO"
          ."          ,AGE_CODIGO"
          ."          ,AGE_CODAT"
          ."          ,AGE_RESPONSAVEL"
          ."          ,CONVERT(VARCHAR(10),A.AGE_VENCTO,127) AS AGE_VENCTO"
          ."          ,CONVERT(VARCHAR(10),A.AGE_DTCADASTRO,127) AS AGE_DTCADASTRO"
          ."          ,AGE_CADASTROU"
          ."          ,CONVERT(VARCHAR(10),A.AGE_DTBAIXA,127) AS AGE_DTBAIXA"
          ."          ,AGE_CODEMP"
          ."          ,CASE WHEN A.AGE_REG='P' THEN 'PUB' WHEN A.AGE_REG='S' THEN 'SIS' ELSE 'ADM' END AS AGE_REG"
          ."          ,US_APELIDO"
          ."     FROM BKPAGENDA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.AGE_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        }  
        //////////////////////////////////////////////////
        //          Dados para JavaScript AGENDATAREFA  //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpAt" ){
          $sql="SELECT AT_ID"
          ."           ,CAST(AT_DATA AS VARCHAR(10)) AS AT_DATA"
          ."           ,CASE WHEN A.AT_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.AT_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.AT_ACAO='E' THEN 'EXC' END AS AT_ACAO"
          ."          ,AT_CODIGO"
          ."          ,AT_NOME"
          ."          ,CASE WHEN A.AT_REG='P' THEN 'PUB' WHEN A.AT_REG='S' THEN 'SIS' ELSE 'ADM' END AS AT_REG"
          ."          ,CASE WHEN A.AT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS AT_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPAGENDATAREFA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.AT_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //   Dados para JavaScript ALIQUOTASIMPLES    //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpAs" ){
          $sql="SELECT AS_ID"
          ."           ,CAST(AS_DATA AS VARCHAR(10)) AS AS_DATA"
          ."           ,CASE WHEN A.AS_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.AS_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.AS_ACAO='E' THEN 'EXC' END AS AS_ACAO"
          ."          ,AS_ANEXO"
          ."          ,AS_ITEM"               
          ."          ,AS_CODEMP"
          ."          ,AS_VLRINI"
          ."          ,AS_VLRFIM"
          ."          ,AS_ALIQUOTA"
          ."          ,AS_IRPJ"
          ."          ,AS_CSLL"
          ."          ,AS_COFINS"
          ."          ,AS_PIS"
          ."          ,AS_CPP"
          ."          ,AS_ICMS"
          ."          ,AS_IPI"
          ."          ,AS_ISS"
          ."          ,CASE WHEN A.AS_REG='P' THEN 'PUB' WHEN A.AS_REG='S' THEN 'SIS' ELSE 'ADM' END AS AS_REG"
          ."          ,US_APELIDO"
          ."     FROM BKPALIQUOTASIMPLES A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.AS_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////
        //     Dados para JavaScript BALANCO    //
        //////////////////////////////////////////
        if( $rotina=="selectBkpBln" ){
          $sql="SELECT BLN_ID"
          ."           ,CAST(BLN_DATA AS VARCHAR(10)) AS BLN_DATA"
          ."           ,CASE WHEN A.BLN_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.BLN_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.BLN_ACAO='E' THEN 'EXC' END AS BLN_ACAO"
          ."          ,BLN_CODIGO"
          ."          ,BLN_NOME"
          ."          ,BLN_CODSPD"               
          ."          ,CASE WHEN A.BLN_REG='P' THEN 'PUB' WHEN A.BLN_REG='S' THEN 'SIS' ELSE 'ADM' END AS BLN_REG"
          ."          ,CASE WHEN A.BLN_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS BLN_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPBALANCO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.BLN_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////
        //     Dados para JavaScript BANCO      //
        //////////////////////////////////////////
        if( $rotina=="selectBkpBnc" ){
          $sql="SELECT BNC_ID"
          ."           ,CAST(BNC_DATA AS VARCHAR(10)) AS BNC_DATA"
          ."           ,CASE WHEN A.BNC_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.BNC_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.BNC_ACAO='E' THEN 'EXC' END AS BNC_ACAO"
          ."          ,BNC_CODIGO"
          ."          ,BNC_CODEMP"
          ."          ,BNC_NOME"
          ."          ,BNC_CODFVR"
          ."          ,BNC_CODCC"
          ."          ,CASE WHEN A.BNC_ENTRAFLUXO='S' THEN 'SIM' ELSE 'NAO' END AS BNC_ENTRAFLUXO"
          ."          ,BNC_CODBST"
          ."          ,CASE WHEN A.BNC_PADRAOFLUXO='S' THEN 'SIM' ELSE 'NAO' END AS BNC_PADRAOFLUXO"
          ."          ,BNC_CODBCD"
          ."          ,BNC_AGENCIA"
          ."          ,BNC_AGENCIADV"
          ."          ,BNC_CONTA"
          ."          ,BNC_CONTADV"
          ."          ,CASE WHEN A.BNC_REG='P' THEN 'PUB' WHEN A.BNC_REG='S' THEN 'SIS' ELSE 'ADM' END AS BNC_REG"
          ."          ,CASE WHEN A.BNC_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS BNC_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPBANCO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.BNC_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //       Dados para JavaScript BANCOCODIGO      //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpBcd" ){
          $sql="SELECT BCD_ID"
          ."           ,CAST(BCD_DATA AS VARCHAR(10)) AS BCD_DATA"
          ."           ,CASE WHEN A.BCD_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.BCD_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.BCD_ACAO='E' THEN 'EXC' END AS BCD_ACAO"
          ."          ,BCD_CODIGO"
          ."          ,BCD_NOME"
          ."          ,CASE WHEN A.BCD_REG='P' THEN 'PUB' WHEN A.BCD_REG='S' THEN 'SIS' ELSE 'ADM' END AS BCD_REG"
          ."          ,CASE WHEN A.BCD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS BCD_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPBANCOCODIGO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.BCD_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //          Dados para JavaScript BANCOSTATUS   //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpBst" ){
          $sql="SELECT BST_ID"
          ."           ,CAST(BST_DATA AS VARCHAR(10)) AS BST_DATA"
          ."           ,CASE WHEN A.BST_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.BST_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.BST_ACAO='E' THEN 'EXC' END AS BST_ACAO"
          ."          ,BST_CODIGO"
          ."          ,BST_NOME"
          ."          ,CASE WHEN A.BST_REG='P' THEN 'PUB' WHEN A.BST_REG='S' THEN 'SIS' ELSE 'ADM' END AS BST_REG"
          ."          ,CASE WHEN A.BST_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS BST_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPBANCOSTATUS A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.BST_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //          Dados para JavaScript CARGO       //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpCrg" ){
          $sql="SELECT CRG_ID"
          ."           ,CAST(CRG_DATA AS VARCHAR(10)) AS CRG_DATA"
          ."           ,CASE WHEN A.CRG_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CRG_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CRG_ACAO='E' THEN 'EXC' END AS CRG_ACAO"
          ."          ,CRG_CODIGO"
          ."          ,CRG_NOME"
          ."          ,CASE WHEN A.CRG_REG='P' THEN 'PUB' WHEN A.CRG_REG='S' THEN 'SIS' ELSE 'ADM' END AS CRG_REG"
          ."          ,CASE WHEN A.CRG_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CRG_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCARGO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CRG_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////
        //   Dados para JavaScript CATEGORIA   //
        /////////////////////////////////////////
        if( $rotina=="selectBkpCtg" ){
          $sql="SELECT CTG_ID"
          ."           ,CAST(CTG_DATA AS VARCHAR(10)) AS CTG_DATA"
          ."           ,CASE WHEN A.CTG_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CTG_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CTG_ACAO='E' THEN 'EXC' END AS CTG_ACAO"
          ."          ,CTG_CODIGO"
          ."          ,CTG_NOME"
          ."          ,CTG_FISJUR"          
          ."          ,CASE WHEN A.CTG_REG='P' THEN 'PUB' WHEN A.CTG_REG='S' THEN 'SIS' ELSE 'ADM' END AS CTG_REG"
          ."          ,CASE WHEN A.CTG_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CTG_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCATEGORIA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CTG_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //          Dados para JavaScript CFOP        //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpCfo" ){
          $sql="SELECT CFO_ID"
          ."           ,CAST(CFO_DATA AS VARCHAR(10)) AS CFO_DATA"
          ."           ,CASE WHEN A.CFO_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CFO_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CFO_ACAO='E' THEN 'EXC' END AS CFO_ACAO"
          ."          ,CFO_CODIGO"
          ."          ,CFO_NOME"
          ."          ,CASE WHEN A.CFO_ENTSAI='E' THEN 'ENT' ELSE 'SAI' END AS CFO_ENTSAI"
          ."          ,CASE WHEN A.CFO_RELCOMPRA='S' THEN 'SIM' ELSE 'NAO' END AS CFO_RELCOMPRA"
          ."          ,CASE WHEN A.CFO_RELVENDA='S' THEN 'SIM' ELSE 'NAO' END AS CFO_RELVENDA"
          ."          ,CASE WHEN A.CFO_REG='P' THEN 'PUB' WHEN A.CFO_REG='S' THEN 'SIS' ELSE 'ADM' END AS CFO_REG"
          ."          ,CASE WHEN A.CFO_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CFO_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCFOP A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CFO_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////
        //     Dados para JavaScript CIDADE    //
        /////////////////////////////////////////
        if( $rotina=="selectBkpCdd" ){
          $sql="SELECT CDD_ID"
          ."           ,CAST(CDD_DATA AS VARCHAR(10)) AS CDD_DATA"
          ."           ,CASE WHEN A.CDD_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CDD_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CDD_ACAO='E' THEN 'EXC' END AS CDD_ACAO"
          ."          ,CDD_CODIGO"
          ."          ,CDD_NOME"
          ."          ,CDD_CODEST"
          ."          ,CDD_DDD"               
          ."          ,CDD_LATITUDE"
          ."          ,CDD_LONGITUDE"          
          ."          ,CASE WHEN A.CDD_REG='P' THEN 'PUB' WHEN A.CDD_REG='S' THEN 'SIS' ELSE 'ADM' END AS CDD_REG"
          ."          ,CASE WHEN A.CDD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CDD_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCIDADE A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CDD_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //     Dados para JavaScript CNABERRO         //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpErr" ){
          $sql="SELECT ERR_ID"
          ."           ,CAST(ERR_DATA AS VARCHAR(10)) AS ERR_DATA"
          ."           ,CASE WHEN A.ERR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.ERR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.ERR_ACAO='E' THEN 'EXC' END AS ERR_ACAO"
          ."          ,ERR_CODBCD"
          ."          ,ERR_CODPTP"
          ."          ,ERR_CODIGO"               
          ."          ,ERR_NOME"               
          ."          ,ERR_EXECUTA"               
          ."          ,CASE WHEN A.ERR_REG='P' THEN 'PUB' WHEN A.ERR_REG='S' THEN 'SIS' ELSE 'ADM' END AS ERR_REG"
          ."          ,CASE WHEN A.ERR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS ERR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCNABERRO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.ERR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //     Dados para JavaScript CNABINSTRUCAO    //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpCi" ){
          $sql="SELECT CI_ID"
          ."           ,CAST(CI_DATA AS VARCHAR(10)) AS CI_DATA"
          ."           ,CASE WHEN A.CI_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CI_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CI_ACAO='E' THEN 'EXC' END AS CI_ACAO"
          ."          ,CI_CODIGO"
          ."          ,CI_CODBCD"               
          ."          ,CI_NOME"
          ."          ,CASE WHEN A.CI_REG='P' THEN 'PUB' WHEN A.CI_REG='S' THEN 'SIS' ELSE 'ADM' END AS CI_REG"
          ."          ,CASE WHEN A.CI_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CI_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCNABINSTRUCAO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CI_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //     Dados para JavaScript CNABRETORNO      //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpCr" ){
          $sql="SELECT CR_ID"
          ."           ,CAST(CR_DATA AS VARCHAR(10)) AS CR_DATA"
          ."           ,CASE WHEN A.CR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CR_ACAO='E' THEN 'EXC' END AS CR_ACAO"
          ."          ,CR_CODIGO"
          ."          ,CR_CODBCD"               
          ."          ,CR_NOME"
          ."          ,CR_EXECUTA"
          ."          ,CASE WHEN A.CR_REG='P' THEN 'PUB' WHEN A.CR_REG='S' THEN 'SIS' ELSE 'ADM' END AS CR_REG"
          ."          ,CASE WHEN A.CR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCNABRETORNO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //     Dados para JavaScript COMPETENCIA      //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpCmp" ){
          $sql="SELECT CMP_ID"
          ."           ,CAST(CMP_DATA AS VARCHAR(10)) AS CMP_DATA"
          ."           ,CASE WHEN A.CMP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CMP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CMP_ACAO='E' THEN 'EXC' END AS CMP_ACAO"
          ."          ,CMP_CODIGO"
          ."          ,CMP_CODEMP"
          ."          ,CMP_NOME"               
          ."          ,CASE WHEN A.CMP_FECHAMENTO='S' THEN 'SIM' ELSE 'NAO' END AS CMP_FECHAMENTO"
          ."          ,CASE WHEN A.CMP_REG='P' THEN 'PUB' WHEN A.CMP_REG='S' THEN 'SIS' ELSE 'ADM' END AS CMP_REG"
          ."          ,CASE WHEN A.CMP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CMP_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCOMPETENCIA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CMP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //     Dados para JavaScript CONTACONTABIL    //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpCc" ){
          $sql="SELECT CC_ID"
          ."           ,CAST(CC_DATA AS VARCHAR(10)) AS CC_DATA"
          ."           ,CASE WHEN A.CC_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CC_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CC_ACAO='E' THEN 'EXC' END AS CC_ACAO"
          ."          ,CC_CODIGO"
          ."          ,CC_NOME"
          ."          ,CASE WHEN A.CC_LANCTO='S' THEN 'SIM' ELSE 'NAO' END AS CC_LANCTO"
          ."          ,CC_CODCTR"               
          ."          ,CC_F10"                         
          ."          ,CASE WHEN A.CC_REG='P' THEN 'PUB' WHEN A.CC_REG='S' THEN 'SIS' ELSE 'ADM' END AS CC_REG"
          ."          ,CASE WHEN A.CC_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CC_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCONTACONTABIL A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CC_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //     Dados para JavaScript CONTADOR         //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpCnt" ){
          $sql="SELECT CNT_ID"
          ."           ,CAST(CNT_DATA AS VARCHAR(10)) AS CNT_DATA"
          ."           ,CASE WHEN A.CNT_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CNT_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CNT_ACAO='E' THEN 'EXC' END AS CNT_ACAO"
          ."          ,CNT_CODIGO"
          ."          ,CNT_CODEMP"
          ."          ,CNT_CRC"               
          ."          ,CNT_CPF"               
          ."          ,CNT_CODQC"               
          ."          ,CNT_CODCDD"               
          ."          ,CNT_CNPJ"               
          ."          ,CNT_NOME"               
          ."          ,CNT_CEP"               
          ."          ,CNT_CODLGR"               
          ."          ,CNT_ENDERECO"               
          ."          ,CNT_NUMERO"               
          ."          ,CNT_FONE"               
          ."          ,CNT_EMAIL"               
          ."          ,CNT_BAIRRO"               
          ."          ,CNT_SUFRAMA"               
          ."          ,CNT_CODINCTRIB"               
          ."          ,CNT_INDAPROCRED"               
          ."          ,CNT_CODTIPOCONT"               
          ."          ,CNT_INDREGCUM"               
          ."          ,CNT_CODRECPIS"               
          ."          ,CNT_CODRECCOFINS"               
          ."          ,CNT_INDNATPJ"               
          ."          ,CNT_INDATIV"               
          ."          ,CASE WHEN A.CNT_REG='P' THEN 'PUB' WHEN A.CNT_REG='S' THEN 'SIS' ELSE 'ADM' END AS CNT_REG"
          ."          ,CASE WHEN A.CNT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CNT_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCONTADOR A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CNT_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };        
        //////////////////////////////////////////
        //   Dados para JavaScript CONTARESUMO  //
        //////////////////////////////////////////
        if( $rotina=="selectBkpCtr" ){
          $sql="SELECT CTR_ID"
          ."           ,CAST(CTR_DATA AS VARCHAR(10)) AS CTR_DATA"
          ."           ,CASE WHEN A.CTR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.CTR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.CTR_ACAO='E' THEN 'EXC' END AS CTR_ACAO"
          ."          ,CTR_CODIGO"
          ."          ,CTR_NOME"
          ."          ,CASE WHEN A.CTR_REG='P' THEN 'PUB' WHEN A.CTR_REG='S' THEN 'SIS' ELSE 'ADM' END AS CTR_REG"
          ."          ,CASE WHEN A.CTR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CTR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCONTARESUMO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.CTR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //          Dados para JavaScript CSTICMS     //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpIcm" ){
          $sql="SELECT ICM_ID"
          ."           ,CAST(ICM_DATA AS VARCHAR(10)) AS ICM_DATA"
          ."           ,CASE WHEN A.ICM_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.ICM_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.ICM_ACAO='E' THEN 'EXC' END AS ICM_ACAO"
          ."          ,ICM_CODIGO"
          ."          ,CASE WHEN A.ICM_ENTSAI='E' THEN 'ENT' ELSE 'SAI' END AS ICM_ENTSAI"
          ."          ,ICM_NOME"
          ."          ,CASE WHEN A.ICM_SNALIQ='S' THEN 'SIM' ELSE 'NAO' END AS ICM_SNALIQ"
          ."          ,ICM_PCISENTAS"
          ."          ,ICM_PCOUTRAS"
          ."          ,CASE WHEN A.ICM_REDUCAOBC='S' THEN 'SIM' ELSE 'NAO' END AS ICM_REDUCAOBC"
          ."          ,CASE WHEN A.ICM_REG='P' THEN 'PUB' WHEN A.ICM_REG='S' THEN 'SIS' ELSE 'ADM' END AS ICM_REG"
          ."          ,CASE WHEN A.ICM_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS ICM_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCSTICMS A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.ICM_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //          Dados para JavaScript CSTIPI      //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpIpi" ){
          $sql="SELECT IPI_ID"
          ."           ,CAST(IPI_DATA AS VARCHAR(10)) AS IPI_DATA"
          ."           ,CASE WHEN A.IPI_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.IPI_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.IPI_ACAO='E' THEN 'EXC' END AS IPI_ACAO"
          ."          ,IPI_CODIGO"
          ."          ,CASE WHEN A.IPI_ENTSAI='E' THEN 'ENT' ELSE 'SAI' END AS IPI_ENTSAI"
          ."          ,IPI_NOME"
          ."          ,CASE WHEN A.IPI_SNALIQ='S' THEN 'SIM' ELSE 'NAO' END AS IPI_SNALIQ"
          ."          ,IPI_PCISENTAS"
          ."          ,IPI_PCOUTRAS"
          ."          ,CASE WHEN A.IPI_REG='P' THEN 'PUB' WHEN A.IPI_REG='S' THEN 'SIS' ELSE 'ADM' END AS IPI_REG"
          ."          ,CASE WHEN A.IPI_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS IPI_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCSTIPI A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.IPI_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //          Dados para JavaScript CSTPIS      //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpPis" ){
          $sql="SELECT PIS_ID"
          ."           ,CAST(PIS_DATA AS VARCHAR(10)) AS PIS_DATA"
          ."           ,CASE WHEN A.PIS_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PIS_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PIS_ACAO='E' THEN 'EXC' END AS PIS_ACAO"
          ."          ,PIS_CODIGO"
          ."          ,CASE WHEN A.PIS_ENTSAI='E' THEN 'ENT' ELSE 'SAI' END AS PIS_ENTSAI"
          ."          ,PIS_NOME"
          ."          ,CASE WHEN A.PIS_SNALIQ='S' THEN 'SIM' ELSE 'NAO' END AS PIS_SNALIQ"
          ."          ,PIS_PCISENTAS"
          ."          ,PIS_PCOUTRAS"
          ."          ,CASE WHEN A.PIS_REG='P' THEN 'PUB' WHEN A.PIS_REG='S' THEN 'SIS' ELSE 'ADM' END AS PIS_REG"
          ."          ,CASE WHEN A.PIS_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PIS_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCSTPIS A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PIS_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ///////////////////////////////////////////////////
        //          Dados para JavaScript CSTSIMPLES     //
        ///////////////////////////////////////////////////
        if( $rotina=="selectBkpSn" ){
          $sql="SELECT SN_ID"
          ."           ,CAST(SN_DATA AS VARCHAR(10)) AS SN_DATA"
          ."           ,CASE WHEN A.SN_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.SN_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.SN_ACAO='E' THEN 'EXC' END AS SN_ACAO"
          ."          ,SN_CODIGO"
          ."          ,CASE WHEN A.SN_ENTSAI='E' THEN 'ENT' ELSE 'SAI' END AS SN_ENTSAI"
          ."          ,SN_NOME"
          ."          ,CASE WHEN A.SN_SNALIQ='S' THEN 'SIM' ELSE 'NAO' END AS SN_SNALIQ"
          ."          ,SN_PCISENTAS"
          ."          ,SN_PCOUTRAS"
          ."          ,CASE WHEN A.SN_REDUCAOBC='S' THEN 'SIM' ELSE 'NAO' END AS SN_REDUCAOBC"
          ."          ,CASE WHEN A.SN_REG='P' THEN 'PUB' WHEN A.SN_REG='S' THEN 'SIS' ELSE 'ADM' END AS SN_REG"
          ."          ,CASE WHEN A.SN_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS SN_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPCSTSIMPLES A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.SN_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //          Dados para JavaScript EMAIL         //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpEma" ){
          $sql="SELECT EMA_ID"
          ."           ,CAST(EMA_DATA AS VARCHAR(10)) AS EMA_DATA"
          ."           ,CASE WHEN A.EMA_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.EMA_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.EMA_ACAO='E' THEN 'EXC' END AS EMA_ACAO"
          ."          ,EMA_CODIGO"
          ."          ,EMA_NOME"
          ."          ,EMA_EMAIL"          
          ."          ,CASE WHEN A.EMA_REG='P' THEN 'PUB' WHEN A.EMA_REG='S' THEN 'SIS' ELSE 'ADM' END AS EMA_REG"
          ."          ,CASE WHEN A.EMA_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS EMA_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPEMAIL A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.EMA_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //          Dados para JavaScript EMBALAGEM    //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpEmb" ){
          $sql="SELECT EMB_ID"
          ."           ,CAST(EMB_DATA AS VARCHAR(10)) AS EMB_DATA"
          ."           ,CASE WHEN A.EMB_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.EMB_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.EMB_ACAO='E' THEN 'EXC' END AS EMB_ACAO"
          ."          ,EMB_CODIGO"
          ."          ,EMB_NOME"
          ."          ,CASE WHEN A.EMB_REG='P' THEN 'PUB' WHEN A.EMB_REG='S' THEN 'SIS' ELSE 'ADM' END AS EMB_REG"
          ."          ,CASE WHEN A.EMB_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS EMB_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPEMBALAGEM A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.EMB_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //          Dados para JavaScript EMPRESA       //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpEmp" ){
          $sql="SELECT EMP_ID"
          ."           ,CAST(EMP_DATA AS VARCHAR(10)) AS EMP_DATA"
          ."           ,CASE WHEN A.EMP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.EMP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.EMP_ACAO='E' THEN 'EXC' END AS EMP_ACAO"
          ."          ,EMP_CODIGO"
          ."          ,EMP_NOME"
          ."          ,EMP_APELIDO"
          ."          ,EMP_CNPJ"
          ."          ,EMP_INSESTAD"
          ."          ,EMP_CODCDD"
          ."          ,EMP_CODLGR"
          ."          ,EMP_ENDERECO"
          ."          ,EMP_NUMERO"
          ."          ,EMP_CEP"
          ."          ,EMP_BAIRRO"
          ."          ,EMP_FONE"
          ."          ,EMP_CODETF"
          ."          ,EMP_ALIQCOFINS"
          ."          ,EMP_ALIQPIS"
          ."          ,EMP_ALIQCSLL"
          ."          ,EMP_ALIQIRRF"
          ."          ,EMP_BCPRESUMIDO"
          ."          ,EMP_ALIQIRPRESUMIDO"
          ."          ,EMP_ALIQCSLLPRESUMIDO"
          ."          ,EMP_ANEXOSIMPLES"
          ."          ,EMP_CODETP"
          ."          ,EMP_CODERM"
          ."          ,EMP_SMTPUSERNAME"
          ."          ,EMP_SMTPPASSWORD"
          ."          ,EMP_SMTPHOST"
          ."          ,EMP_SMTPPORT"
          ."          ,EMP_CERTPATH"
          ."          ,EMP_CERTSENHA"
          ."          ,CONVERT(VARCHAR(10),A.EMP_CERTVALIDADE,127) AS EMP_CERTVALIDADE"		  
          ."          ,EMP_PRODHOMOL"
          ."          ,EMP_CONTINGENCIA"
          ."          ,EMP_CODERT"
          ."          ,CASE WHEN A.EMP_REG='P' THEN 'PUB' WHEN A.EMP_REG='S' THEN 'SIS' ELSE 'ADM' END AS EMP_REG"
          ."          ,CASE WHEN A.EMP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS EMP_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPEMPRESA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.EMP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //      Dados para JavaScript EMPRESARAMO       //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpErm" ){
          $sql="SELECT ERM_ID"
          ."           ,CAST(ERM_DATA AS VARCHAR(10)) AS ERM_DATA"
          ."           ,CASE WHEN A.ERM_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.ERM_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.ERM_ACAO='E' THEN 'EXC' END AS ERM_ACAO"
          ."          ,ERM_CODIGO"
          ."          ,ERM_NOME"
          ."          ,CASE WHEN A.ERM_REG='P' THEN 'PUB' WHEN A.ERM_REG='S' THEN 'SIS' ELSE 'ADM' END AS ERM_REG"
          ."          ,CASE WHEN A.ERM_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS ERM_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPEMPRESARAMO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.ERM_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript EMPRESAREGTRIB  //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpErt" ){
          $sql="SELECT ERT_ID"
          ."           ,CAST(ERT_DATA AS VARCHAR(10)) AS ERT_DATA"
          ."           ,CASE WHEN A.ERT_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.ERT_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.ERT_ACAO='E' THEN 'EXC' END AS ERT_ACAO"
          ."          ,ERT_CODIGO"
          ."          ,ERT_NOME"
          ."          ,CASE WHEN A.ERT_REG='P' THEN 'PUB' WHEN A.ERT_REG='S' THEN 'SIS' ELSE 'ADM' END AS ERT_REG"
          ."          ,CASE WHEN A.ERT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS ERT_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPEMPRESAREGTRIB A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.ERT_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript EMPRESATIPO     //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpEtp" ){
          $sql="SELECT ETP_ID"
          ."           ,CAST(ETP_DATA AS VARCHAR(10)) AS ETP_DATA"
          ."           ,CASE WHEN A.ETP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.ETP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.ETP_ACAO='E' THEN 'EXC' END AS ETP_ACAO"
          ."          ,ETP_CODIGO"
          ."          ,ETP_NOME"
          ."          ,CASE WHEN A.ETP_REG='P' THEN 'PUB' WHEN A.ETP_REG='S' THEN 'SIS' ELSE 'ADM' END AS ETP_REG"
          ."          ,CASE WHEN A.ETP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS ETP_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPEMPRESATIPO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.ETP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript EMPRESATRIBFED  //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpEtf" ){
          $sql="SELECT ETF_ID"
          ."           ,CAST(ETF_DATA AS VARCHAR(10)) AS ETF_DATA"
          ."           ,CASE WHEN A.ETF_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.ETF_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.ETF_ACAO='E' THEN 'EXC' END AS ETF_ACAO"
          ."          ,ETF_CODIGO"
          ."          ,ETF_NOME"
          ."          ,CASE WHEN A.ETF_REG='P' THEN 'PUB' WHEN A.ETF_REG='S' THEN 'SIS' ELSE 'ADM' END AS ETF_REG"
          ."          ,CASE WHEN A.ETF_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS ETF_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPEMPRESATRIBFED A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.ETF_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////
        //     Dados para JavaScript ESTADO    //
        /////////////////////////////////////////
        if( $rotina=="selectBkpEst" ){
          $sql="SELECT EST_ID"
          ."           ,CAST(EST_DATA AS VARCHAR(10)) AS EST_DATA"
          ."           ,CASE WHEN A.EST_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.EST_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.EST_ACAO='E' THEN 'EXC' END AS EST_ACAO"
          ."          ,EST_CODIGO"
          ."          ,EST_NOME"
          ."          ,EST_ALIQICMS"          
          ."          ,EST_CODREG"          
          ."          ,CASE WHEN A.EST_REG='P' THEN 'PUB' WHEN A.EST_REG='S' THEN 'SIS' ELSE 'ADM' END AS EST_REG"
          ."          ,CASE WHEN A.EST_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS EST_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPESTADO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.EST_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////
        //     Dados para JavaScript FABRICANTE //
        //////////////////////////////////////////
        if( $rotina=="selectBkpFbr" ){
          $sql="SELECT FBR_ID"
          ."           ,CAST(FBR_DATA AS VARCHAR(10)) AS FBR_DATA"
          ."           ,CASE WHEN A.FBR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.FBR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.FBR_ACAO='E' THEN 'EXC' END AS FBR_ACAO"
          ."          ,FBR_CODFVR"
          ."          ,FBR_CODGP"
          ."          ,CASE WHEN A.FBR_REG='P' THEN 'PUB' WHEN A.FBR_REG='S' THEN 'SIS' ELSE 'ADM' END AS FBR_REG"
          ."          ,CASE WHEN A.FBR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS FBR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPFABRICANTE A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.FBR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };        
        //////////////////////////////////////////
        //     Dados para JavaScript FAVORECIDO //
        //////////////////////////////////////////
        if( $rotina=="selectBkpFvr" ){
          $sql="SELECT FVR_ID"
          ."           ,CAST(FVR_DATA AS VARCHAR(10)) AS FVR_DATA"
          ."           ,CASE WHEN A.FVR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.FVR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.FVR_ACAO='E' THEN 'EXC' END AS FVR_ACAO"
          ."          ,FVR_CODIGO"
          ."          ,FVR_NOME"
          ."          ,FVR_APELIDO"
          ."          ,FVR_BAIRRO"
          ."          ,FVR_CNPJCPF"
          ."          ,FVR_CEP"
          ."          ,FVR_CODCDD"
          ."          ,CONVERT(VARCHAR(10),A.FVR_DTCADASTRO,127) AS FVR_DTCADASTRO"		  
          ."          ,FVR_FISJUR"
          ."          ,FVR_INSMUNIC"
          ."          ,FVR_CONTATO"
          ."          ,FVR_ENDERECO"
          ."          ,FVR_FONE"
          ."          ,FVR_INS"
          ."          ,FVR_CTAATIVO"
          ."          ,FVR_CTAPASSIVO"
          ."          ,FVR_CADMUNIC"
          ."          ,FVR_EMAIL"
          ."          ,FVR_CODCTG"
          ."          ,FVR_SENHA"
          ."          ,FVR_COMPLEMENTO"
          ."          ,FVR_NUMERO"
          ."          ,FVR_CODLGR"
          ."          ,FVR_GFCP"
          ."          ,FVR_GFCR"
          ."          ,CASE WHEN A.FVR_REG='P' THEN 'PUB' WHEN A.FVR_REG='S' THEN 'SIS' ELSE 'ADM' END AS FVR_REG"
          ."          ,CASE WHEN A.FVR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS FVR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPFAVORECIDO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.FVR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////
        //     Dados para JavaScript FERIADO      //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpFrd" ){
          $sql="SELECT FRD_ID"
          ."           ,CAST(FRD_DATA AS VARCHAR(10)) AS FRD_DATA"
          ."           ,CASE WHEN A.FRD_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.FRD_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.FRD_ACAO='E' THEN 'EXC' END AS FRD_ACAO"
          ."          ,CONVERT(VARCHAR(10),A.FRD_CODIGO,127) AS FRD_CODIGO"
          ."          ,FRD_CODEMP"
          ."          ,FRD_NOME"
          ."          ,FRD_PAGAR"
          ."          ,FRD_RECEBER"          
          ."          ,CASE WHEN A.FRD_REG='P' THEN 'PUB' WHEN A.FRD_REG='S' THEN 'SIS' ELSE 'ADM' END AS FRD_REG"
          ."          ,CASE WHEN A.FRD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS FRD_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPFERIADO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.FRD_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////
        //     Dados para JavaScript FILIAL       //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpFll" ){
          $sql="SELECT FLL_ID"
          ."           ,CAST(FLL_DATA AS VARCHAR(10)) AS FLL_DATA"
          ."           ,CASE WHEN A.FLL_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.FLL_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.FLL_ACAO='E' THEN 'EXC' END AS FLL_ACAO"
          ."          ,FLL_CODIGO"               
          ."          ,FLL_NOME"               
          ."          ,FLL_APELIDO"               
          ."          ,FLL_BAIRRO"               
          ."          ,FLL_CEP"               
          ."          ,FLL_CNPJ"               
          ."          ,FLL_CODCDD"               
          ."          ,FLL_CODLGR"
          ."          ,FLL_ENDERECO"               
          ."          ,FLL_NUMERO"               
          ."          ,FLL_FONE"               
          ."          ,FLL_INSESTAD"               
          ."          ,FLL_INSMUNIC"               
          ."          ,FLL_CODEMP"               
          ."          ,CASE WHEN A.FLL_REG='P' THEN 'PUB' WHEN A.FLL_REG='S' THEN 'SIS' ELSE 'ADM' END AS FLL_REG"
          ."          ,CASE WHEN A.FLL_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS FLL_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPFILIAL A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.FLL_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////////////////
        //          Dados para JavaScript FORMACOBRANCA //
        /////////////////////////////////////////////////////
        if( $rotina=="selectBkpFc" ){
          $sql="SELECT FC_ID"
          ."           ,CAST(FC_DATA AS VARCHAR(10)) AS FC_DATA"
          ."           ,CASE WHEN A.FC_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.FC_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.FC_ACAO='E' THEN 'EXC' END AS FC_ACAO"
          ."          ,FC_CODIGO"
          ."          ,FC_NOME"
          ."          ,CASE WHEN A.FC_REG='P' THEN 'PUB' WHEN A.FC_REG='S' THEN 'SIS' ELSE 'ADM' END AS FC_REG"
          ."          ,CASE WHEN A.FC_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS FC_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPFORMACOBRANCA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.FC_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript GRUPOFAVORECIDO //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpGf" ){
          $sql="SELECT GF_ID"
          ."           ,CAST(GF_DATA AS VARCHAR(10)) AS GF_DATA"
          ."           ,CASE WHEN A.GF_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.GF_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.GF_ACAO='E' THEN 'EXC' END AS GF_ACAO"
          ."          ,GF_CODIGO"
          ."          ,GF_NOME"
          ."          ,CASE WHEN A.GF_REG='P' THEN 'PUB' WHEN A.GF_REG='S' THEN 'SIS' ELSE 'ADM' END AS GF_REG"
          ."          ,CASE WHEN A.GF_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS GF_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPGRUPOFAVORECIDO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.GF_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript GRUPOMODELO     //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpGm" ){
          $sql="SELECT GM_ID"
          ."           ,CAST(GM_DATA AS VARCHAR(10)) AS GM_DATA"
          ."           ,CASE WHEN A.GM_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.GM_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.GM_ACAO='E' THEN 'EXC' END AS GN_ACAO"
          ."           ,GM_CODIGO"
          ."           ,GM_CODFBR"      
          ."           ,GM_NOME"
          ."           ,GM_CODGP"
          ."           ,GM_ESTOQUE"
          ."           ,GM_ESTOQUEMINIMO"
          ."           ,GM_ESTOQUESUCATA"
          ."           ,GM_ESTOQUEAUTO"          
          ."           ,GM_NUMSERIE"
          ."           ,GM_SINCARD"
          ."           ,GM_OPERADORA"
          ."           ,GM_FONE"
          ."           ,GM_VENDA"
          ."           ,GM_LOCACAO"      
          ."           ,GM_CONTRATO"
          ."           ,GM_GPOBRIGATORIO"
          ."           ,GM_GMOBRIGATORIO"      
          ."           ,GM_GPACEITO"
          ."           ,GM_GMACEITO"      
          ."           ,GM_VALORVISTA"
          ."           ,GM_VALORPRAZO"
          ."           ,GM_VALORMINIMO"  
          ."           ,GM_VLRNOSHOW"
          ."           ,GM_VLRIMPRODUTIVEL"
          ."           ,GM_VLRINSTALA"
          ."           ,GM_VLRDESISTALA"
          ."           ,GM_VLRREINSTALA"          
          ."           ,GM_VLRMANUTENCAO"
          ."           ,GM_VLRREVISAO"
          ."           ,GM_FIRMWARE"
          ."           ,GM_ATIVO"
          ."           ,GM_REG"
          ."           ,US_APELIDO"
          ."     FROM BKPGRUPOMODELO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.GM_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //          Dados para JavaScript GRUPOPRODUTO  //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpGp" ){
          $sql="SELECT GP_ID"
          ."           ,CAST(GP_DATA AS VARCHAR(10)) AS GP_DATA"
          ."           ,CASE WHEN A.GP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.GP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.GP_ACAO='E' THEN 'EXC' END AS GP_ACAO"
          ."          ,GP_CODIGO"
          ."          ,GP_NOME"
          ."          ,GP_ORDEMSERVICO"					          
          ."          ,CASE WHEN A.GP_REG='P' THEN 'PUB' WHEN A.GP_REG='S' THEN 'SIS' ELSE 'ADM' END AS GP_REG"
          ."          ,CASE WHEN A.GP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS GP_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPGRUPOPRODUTO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.GP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript IMPOSTO         //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpImp" ){
          $sql="SELECT IMP_ID"
          ."           ,CAST(IMP_DATA AS VARCHAR(10)) AS IMP_DATA"
          ."           ,CASE WHEN A.IMP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.IMP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.IMP_ACAO='E' THEN 'EXC' END AS IMP_ACAO"
          ."          ,IMP_UFDE"
          ."          ,IMP_UFPARA"
          ."          ,IMP_CODNCM"
          ."          ,IMP_CODCTG"
          ."          ,CASE WHEN IMP_ENTSAI='S' THEN 'SAI' ELSE 'ENT' END AS IMP_ENTSAI"
          ."          ,IMP_CODNO"
          ."          ,IMP_CFOP"
          ."          ,IMP_CSTICMS"
          ."          ,IMP_ALIQICMS"
          ."          ,IMP_REDUCAOBC"
          ."          ,IMP_CSTIPI"
          ."          ,IMP_ALIQIPI"
          ."          ,IMP_CSTPIS"
          ."          ,IMP_ALIQPIS"
          ."          ,IMP_CSTCOFINS"
          ."          ,IMP_ALIQCOFINS"
          ."          ,IMP_ALIQST"
          ."          ,CASE WHEN IMP_ALTERANFP='S' THEN 'SIM' ELSE 'NAO' END AS IMP_ALTERANFP"
          ."          ,IMP_CODEMP"
          ."          ,IMP_CODFLL"
          ."          ,CASE WHEN A.IMP_REG='P' THEN 'PUB' WHEN A.IMP_REG='S' THEN 'SIS' ELSE 'ADM' END AS IMP_REG"
          ."          ,CASE WHEN A.IMP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS IMP_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPIMPOSTO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.IMP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////
        //    Dados para JavaScript LOGRADOURO //
        /////////////////////////////////////////
        if( $rotina=="selectBkpLgr" ){
          $sql="SELECT LGR_ID"
          ."           ,CAST(LGR_DATA AS VARCHAR(10)) AS LGR_DATA"
          ."           ,CASE WHEN A.LGR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.LGR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.LGR_ACAO='E' THEN 'EXC' END AS LGR_ACAO"
          ."          ,LGR_CODIGO"
          ."          ,LGR_NOME"
          ."          ,CASE WHEN A.LGR_REG='P' THEN 'PUB' WHEN A.LGR_REG='S' THEN 'SIS' ELSE 'ADM' END AS LGR_REG"
          ."          ,CASE WHEN A.LGR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS LGR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPLOGRADOURO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.LGR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //          Dados para JavaScript MOEDA       //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpMoe" ){
          $sql="SELECT MOE_ID"
          ."           ,CAST(MOE_DATA AS VARCHAR(10)) AS MOE_DATA"
          ."           ,CASE WHEN A.MOE_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.MOE_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.MOE_ACAO='E' THEN 'EXC' END AS MOE_ACAO"
          ."          ,MOE_CODIGO"
          ."          ,MOE_NOME"
          ."          ,CASE WHEN A.MOE_REG='P' THEN 'PUB' WHEN A.MOE_REG='S' THEN 'SIS' ELSE 'ADM' END AS MOE_REG"
          ."          ,CASE WHEN A.MOE_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS MOE_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPMOEDA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.MOE_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript NCM             //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpNcm" ){
          $sql="SELECT NCM_ID"
          ."           ,CAST(NCM_DATA AS VARCHAR(10)) AS NCM_DATA"
          ."           ,CASE WHEN A.NCM_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.NCM_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.NCM_ACAO='E' THEN 'EXC' END AS NCM_ACAO"
          ."          ,NCM_CODIGO"
          ."          ,NCM_NOME"
          ."          ,CASE WHEN A.NCM_REG='P' THEN 'PUB' WHEN A.NCM_REG='S' THEN 'SIS' ELSE 'ADM' END AS NCM_REG"
          ."          ,CASE WHEN A.NCM_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS NCM_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPNCM A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.NCM_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////////////////
        //          Dados para JavaScript NATUREZAOPERACAO //
        /////////////////////////////////////////////////////
        if( $rotina=="selectBkpNo" ){
          $sql="SELECT NO_ID"
          ."           ,CAST(NO_DATA AS VARCHAR(10)) AS NO_DATA"
          ."           ,CASE WHEN A.NO_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.NO_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.NO_ACAO='E' THEN 'EXC' END AS NO_ACAO"
          ."          ,NO_CODIGO"
          ."          ,NO_NOME"
          ."          ,NO_FINNFE"          
          ."          ,CASE WHEN A.NO_REG='P' THEN 'PUB' WHEN A.NO_REG='S' THEN 'SIS' ELSE 'ADM' END AS NO_REG"
          ."          ,CASE WHEN A.NO_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS NO_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPNATUREZAOPERACAO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.NO_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //         Dados para JavaScript OPERADORA        //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpOpe" ){
          $sql="SELECT OPE_ID"
          ."           ,CAST(OPE_DATA AS VARCHAR(10)) AS OPE_DATA"
          ."           ,CASE WHEN A.OPE_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.OPE_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.OPE_ACAO='E' THEN 'EXC' END AS OPE_ACAO"
          ."          ,OPE_CODFVR"
          ."          ,CASE WHEN A.OPE_REG='P' THEN 'PUB' WHEN A.OPE_REG='S' THEN 'SIS' ELSE 'ADM' END AS OPE_REG"
          ."          ,CASE WHEN A.OPE_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS OPE_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPOPERADORA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.OPE_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////
        //    Dados para JavaScript PADRAO  //
        //////////////////////////////////////
        if( $rotina=="selectBkpPdr" ){
          $sql="SELECT PDR_ID"
          ."           ,CAST(PDR_DATA AS VARCHAR(10)) AS PDR_DATA"
          ."           ,CASE WHEN A.PDR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PDR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PDR_ACAO='E' THEN 'EXC' END AS PDR_ACAO"
          ."          ,PDR_CODIGO"
          ."          ,PDR_NOME"
          ."          ,PDR_CODPTT"          
          ."          ,CASE WHEN A.PDR_REG='P' THEN 'PUB' WHEN A.PDR_REG='S' THEN 'SIS' ELSE 'ADM' END AS PDR_REG"
          ."          ,CASE WHEN A.PDR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PDR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPADRAO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PDR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ///////////////////////////////////////////
        //    Dados para JavaScript PADRAOGRUPO  //
        ///////////////////////////////////////////
        if( $rotina=="selectBkpPg" ){
          $sql="SELECT PG_ID"
          ."           ,CAST(PG_DATA AS VARCHAR(10)) AS PG_DATA"
          ."           ,CASE WHEN A.PG_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PG_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PG_ACAO='E' THEN 'EXC' END AS PG_ACAO"
          ."          ,PG_CODPDR"
          ."          ,PG_CODPTP"
          ."          ,CASE WHEN A.PG_REG='P' THEN 'PUB' WHEN A.PG_REG='S' THEN 'SIS' ELSE 'ADM' END AS PG_REG"
          ."          ,CASE WHEN A.PG_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PG_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPADRAOGRUPO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PG_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////
        //    Dados para JavaScript PADRAOTITULO  //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpPt" ){
          $sql="SELECT PT_ID"
          ."           ,CAST(PT_DATA AS VARCHAR(10)) AS PT_DATA"
          ."           ,CASE WHEN A.PT_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PT_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PT_ACAO='E' THEN 'EXC' END AS PT_ACAO"
          ."          ,PT_CODIGO"
          ."          ,PT_NOME"
          ."          ,PT_CODTD"
          ."          ,PT_CODFC"
          ."          ,CASE WHEN A.PT_DEBCRE='C' THEN 'CD' ELSE 'DB' END AS PT_DEBCRE"
          ."          ,PT_CODCC"
          ."          ,PT_CODPDR"
          ."          ,CASE WHEN A.PT_REG='P' THEN 'PUB' WHEN A.PT_REG='S' THEN 'SIS' ELSE 'ADM' END AS PT_REG"
          ."          ,CASE WHEN A.PT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PT_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPADRAOTITULO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PT_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////
        //    Dados para JavaScript PAIS    //
        //////////////////////////////////////
        if( $rotina=="selectBkpPai" ){
          $sql="SELECT PAI_ID"
          ."           ,CAST(PAI_DATA AS VARCHAR(10)) AS PAI_DATA"
          ."           ,CASE WHEN A.PAI_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PAI_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PAI_ACAO='E' THEN 'EXC' END AS PAI_ACAO"
          ."          ,PAI_CODIGO"
          ."          ,PAI_NOME"
          ."          ,PAI_DDI"          
          ."          ,CASE WHEN A.PAI_REG='P' THEN 'PUB' WHEN A.PAI_REG='S' THEN 'SIS' ELSE 'ADM' END AS PAI_REG"
          ."          ,CASE WHEN A.PAI_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PAI_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPAIS A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PAI_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////
        //    Dados para JavaScript PAGARTIPO   //
        //////////////////////////////////////////
        if( $rotina=="selectBkpPtp" ){
          $sql="SELECT PTP_ID"
          ."           ,CAST(PTP_DATA AS VARCHAR(10)) AS PTP_DATA"
          ."           ,CASE WHEN A.PTP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PTP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PTP_ACAO='E' THEN 'EXC' END AS PTP_ACAO"
          ."          ,PTP_CODIGO"
          ."          ,PTP_NOME"
          ."          ,PTP_VALOR"
          ."          ,CASE WHEN A.PTP_CNAB='S' THEN 'SIM' ELSE 'NAO' END AS PTP_CNAB"
          ."          ,CASE WHEN A.PTP_CONTABIL='S' THEN 'SIM' ELSE 'NAO' END AS PTP_CONTABIL"          
          ."          ,CASE WHEN A.PTP_REG='P' THEN 'PUB' WHEN A.PTP_REG='S' THEN 'SIS' ELSE 'ADM' END AS PTP_REG"
          ."          ,CASE WHEN A.PTP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PTP_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPAGARTIPO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PTP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////
        //    Dados para JavaScript PAGARTITULO //
        //////////////////////////////////////////
        if( $rotina=="selectBkpPtt" ){
          $sql="SELECT PTT_ID"
          ."           ,CAST(PTT_DATA AS VARCHAR(10)) AS PTT_DATA"
          ."           ,CASE WHEN A.PTT_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PTT_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PTT_ACAO='E' THEN 'EXC' END AS PTT_ACAO"
          ."          ,PTT_CODIGO"
          ."          ,PTT_NOME"
          ."          ,CASE WHEN A.PTT_REG='P' THEN 'PUB' WHEN A.PTT_REG='S' THEN 'SIS' ELSE 'ADM' END AS PTT_REG"
          ."          ,CASE WHEN A.PTT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PTT_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPAGARTITULO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PTT_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //          Dados para JavaScript PONTOESTOQUE  //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpPe" ){
          $sql="SELECT PE_ID"
          ."           ,CAST(PE_DATA AS VARCHAR(10)) AS PE_DATA"
          ."           ,CASE WHEN A.PE_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PE_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PE_ACAO='E' THEN 'EXC' END AS PE_ACAO"
          ."          ,PE_CODIGO"
          ."          ,PE_NOME"
          ."          ,PE_SUCATA"          
          ."          ,CASE WHEN A.PE_REG='P' THEN 'PUB' WHEN A.PE_REG='S' THEN 'SIS' ELSE 'ADM' END AS PE_REG"
          ."          ,CASE WHEN A.PE_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PE_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPONTOESTOQUE A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PE_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////
        //          Dados para JavaScript PRODUTO //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpPrd" ){
          $sql="SELECT PRD_ID"
          ."           ,CAST(PRD_DATA AS VARCHAR(10)) AS PRD_DATA"
          ."           ,CASE WHEN A.PRD_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PRD_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PRD_ACAO='E' THEN 'EXC' END AS PRD_ACAO"
          ."          ,PRD_CODIGO"
          ."          ,PRD_NOME"
          ."          ,PRD_CODNCM"
          ."          ,PRD_ST"
          ."          ,PRD_ALIQICMS"
          ."          ,PRD_REDUCAOBC"
          ."          ,PRD_IPI"
          ."          ,PRD_ALIQIPI"
          ."          ,PRD_CSTIPI"
          ."          ,PRD_CODEMB"
          ."          ,PRD_VLRVENDA"
          ."          ,PRD_CODPO"
          ."          ,PRD_CODBARRAS"
          ."          ,PRD_PESOBRUTO"
          ."          ,PRD_PESOLIQUIDO"
          ."          ,PRD_CODEMP"
          ."          ,CASE WHEN A.PRD_REG='P' THEN 'PUB' WHEN A.PRD_REG='S' THEN 'SIS' ELSE 'ADM' END AS PRD_REG"
          ."          ,CASE WHEN A.PRD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PRD_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPRODUTO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PRD_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };  
        ////////////////////////////////////////////
        //    Dados para JavaScript PRODUTOORIGEM //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpPo" ){
          $sql="SELECT PO_ID"
          ."           ,CAST(PO_DATA AS VARCHAR(10)) AS PO_DATA"
          ."           ,CASE WHEN A.PO_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.PO_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.PO_ACAO='E' THEN 'EXC' END AS PO_ACAO"
          ."          ,PO_CODIGO"
          ."          ,PO_NOME"
          ."          ,CASE WHEN A.PO_REG='P' THEN 'PUB' WHEN A.PO_REG='S' THEN 'SIS' ELSE 'ADM' END AS PO_REG"
          ."          ,CASE WHEN A.PO_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PO_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPPRODUTOORIGEM A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.PO_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////////////////
        //       Dados para JavaScript QUALIFICACAOCONT    //
        /////////////////////////////////////////////////////
        if( $rotina=="selectBkpQc" ){
          $sql="SELECT QC_ID"
          ."           ,CAST(QC_DATA AS VARCHAR(10)) AS QC_DATA"
          ."           ,CASE WHEN A.QC_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.QC_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.QC_ACAO='E' THEN 'EXC' END AS QC_ACAO"
          ."          ,QC_CODIGO"
          ."          ,QC_NOME"
          ."          ,CASE WHEN A.QC_REG='P' THEN 'PUB' WHEN A.QC_REG='S' THEN 'SIS' ELSE 'ADM' END AS QC_REG"
          ."          ,CASE WHEN A.QC_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS QC_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPQUALIFICACAOCONT A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.QC_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////
        //     Dados para JavaScript REGIAO    //
        /////////////////////////////////////////
        if( $rotina=="selectBkpReg" ){
          $sql="SELECT REG_ID"
          ."           ,CAST(REG_DATA AS VARCHAR(10)) AS REG_DATA"
          ."           ,CASE WHEN A.REG_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.REG_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.REG_ACAO='E' THEN 'EXC' END AS REG_ACAO"
          ."          ,REG_CODIGO"
          ."          ,REG_NOME"
          ."          ,REG_CODPAI"          
          ."          ,CASE WHEN A.REG_REG='P' THEN 'PUB' WHEN A.REG_REG='S' THEN 'SIS' ELSE 'ADM' END AS REG_REG"
          ."          ,CASE WHEN A.REG_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS REG_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPREGIAO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.REG_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////
        //          Dados para JavaScript SERIENF //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpSnf" ){
          $sql="SELECT SNF_ID"
          ."           ,CAST(SNF_DATA AS VARCHAR(10)) AS SNF_DATA"
          ."           ,CASE WHEN A.SNF_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.SNF_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.SNF_ACAO='E' THEN 'EXC' END AS SNF_ACAO"
          ."          ,SNF_CODIGO"
          ."          ,SNF_SERIE"
          ."          ,CASE WHEN A.SNF_ENTSAI='S' THEN 'SAI' ELSE 'ENT' END AS SNF_ENTSAI"
          ."          ,SNF_CODTD"
          ."          ,CASE WHEN A.SNF_INFORMARNF='S' THEN 'SIM' ELSE 'NAO' END AS SNF_INFORMARNF"
          ."          ,SNF_NFINICIO"
          ."          ,SNF_NFFIM"
          ."          ,SNF_IDF"
          ."          ,SNF_MODELO"
          ."          ,SNF_LIVRO"          
          ."          ,SNF_ENVIO"          
          ."          ,SNF_CODFLL"          
          ."          ,SNF_CODEMP"
          ."          ,CASE WHEN A.SNF_REG='P' THEN 'PUB' WHEN A.SNF_REG='S' THEN 'SIS' ELSE 'ADM' END AS SNF_REG"
          ."          ,CASE WHEN A.SNF_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS SNF_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPSERIENF A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.SNF_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////
        //          Dados para JavaScript SERVICO //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpSrv" ){
          $sql="SELECT SRV_ID"
          ."           ,CAST(SRV_DATA AS VARCHAR(10)) AS SRV_DATA"
          ."           ,CASE WHEN A.SRV_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.SRV_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.SRV_ACAO='E' THEN 'EXC' END AS SRV_ACAO"
          ."          ,SRV_CODIGO"
          ."          ,SRV_NOME"
          ."          ,CASE WHEN A.SRV_ENTSAI='S' THEN 'SAI' ELSE 'ENT' END AS SRV_ENTSAI"
          ."          ,CASE WHEN A.SRV_INSS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_INSS"
          ."          ,SRV_INSSALIQ"
          ."          ,SRV_INSSBASECALC"
          ."          ,CASE WHEN A.SRV_IRRF='S' THEN 'SIM' ELSE 'NAO' END AS SRV_IRRF"
          ."          ,SRV_IRRFALIQ"
          ."          ,CASE WHEN A.SRV_PIS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_PIS"
          ."          ,SRV_PISALIQ"
          ."          ,CASE WHEN A.SRV_COFINS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_COFINS"
          ."          ,SRV_COFINSALIQ"
          ."          ,CASE WHEN A.SRV_CSLL='S' THEN 'SIM' ELSE 'NAO' END AS SRV_CSLL"
          ."          ,SRV_CSLLALIQ"
          ."          ,CASE WHEN A.SRV_ISS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_ISS"
          ."          ,SRV_CODCC"
          ."          ,SRV_CODSPR"
          ."          ,SRV_CODEMP"
          ."          ,CASE WHEN A.SRV_PODEVENDA='S' THEN 'SIM' ELSE 'NAO' END AS SRV_PODEVENDA"          
          ."          ,CASE WHEN A.SRV_PODELOCACAO='S' THEN 'SIM' ELSE 'NAO' END AS SRV_PODELOCACAO"                    
          ."          ,CASE WHEN A.SRV_REG='P' THEN 'PUB' WHEN A.SRV_REG='S' THEN 'SIS' ELSE 'ADM' END AS SRV_REG"
          ."          ,CASE WHEN A.SRV_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS SRV_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPSERVICO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.SRV_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };  
        //////////////////////////////////////////////////////
        //          Dados para JavaScript SERVICOPREFEITURA //
        //////////////////////////////////////////////////////
        if( $rotina=="selectBkpSpr" ){
          $sql="SELECT SPR_ID"
          ."           ,CAST(SPR_DATA AS VARCHAR(10)) AS SPR_DATA"
          ."           ,CASE WHEN A.SPR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.SPR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.SPR_ACAO='E' THEN 'EXC' END AS SPR_ACAO"
          ."          ,SPR_CODIGO"
          ."          ,SPR_CODCDD"
          ."          ,SPR_NOME"
          ."          ,SPR_CODFEDERAL"
          ."          ,SPR_ALIQUOTA"
          ."          ,CASE WHEN A.SPR_RETIDO='S' THEN 'SIM' ELSE 'NAO' END AS SPR_RETIDO"
          ."          ,CASE WHEN A.SPR_REG='P' THEN 'PUB' WHEN A.SPR_REG='S' THEN 'SIS' ELSE 'ADM' END AS SPR_REG"
          ."          ,CASE WHEN A.SPR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS SPR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPSERVICOPREFEITURA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.SPR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////
        //     Dados para JavaScript SPED      //
        /////////////////////////////////////////
        if( $rotina=="selectBkpSpd" ){
          $sql="SELECT SPD_ID"
          ."           ,CAST(SPD_DATA AS VARCHAR(10)) AS SPD_DATA"
          ."           ,CASE WHEN A.SPD_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.SPD_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.SPD_ACAO='E' THEN 'EXC' END AS SPD_ACAO"
          ."          ,SPD_CODIGO"
          ."          ,SPD_NOME"
          ."          ,CASE WHEN A.SPD_REG='P' THEN 'PUB' WHEN A.SPD_REG='S' THEN 'SIS' ELSE 'ADM' END AS SPD_REG"
          ."          ,CASE WHEN A.SPD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS SPD_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPSPED A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.SPD_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        //////////////////////////////////////////////////
        //     Dados para JavaScript TIPODOCUMENTO      //
        //////////////////////////////////////////////////
        if( $rotina=="selectBkpTd" ){
          $sql="SELECT TD_ID"
          ."           ,CAST(TD_DATA AS VARCHAR(10)) AS TD_DATA"
          ."           ,CASE WHEN A.TD_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.TD_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.TD_ACAO='E' THEN 'EXC' END AS TD_ACAO"
          ."          ,TD_CODIGO"
          ."          ,TD_NOME"
          ."          ,TD_SERIENF"          
          ."          ,CASE WHEN A.TD_REG='P' THEN 'PUB' WHEN A.TD_REG='S' THEN 'SIS' ELSE 'ADM' END AS TD_REG"
          ."          ,CASE WHEN A.TD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS TD_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPTIPODOCUMENTO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.TD_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //          Dados para JavaScript TRANSPORTADORA  //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpTrn" ){
          $sql="SELECT TRN_ID"
          ."           ,CAST(TRN_DATA AS VARCHAR(10)) AS TRN_DATA"
          ."           ,CASE WHEN A.TRN_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.TRN_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.TRN_ACAO='E' THEN 'EXC' END AS TRN_ACAO"
          ."          ,TRN_CODFVR"
          ."          ,TRN_CODEMP"
          ."          ,CASE WHEN A.TRN_REG='P' THEN 'PUB' WHEN A.TRN_REG='S' THEN 'SIS' ELSE 'ADM' END AS TRN_REG"
          ."          ,CASE WHEN A.TRN_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS TRN_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPTRANSPORTADORA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.TRN_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        /////////////////////////////////////////
        //    Dados para JavaScript USUARIO    //
        /////////////////////////////////////////
        if( $rotina=="selectBkpUsr" ){
          $sql="SELECT A.USR_ID"
          ."           ,CAST(A.USR_DATA AS VARCHAR(10)) AS USR_DATA"
          ."           ,CASE WHEN A.USR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.USR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.USR_ACAO='E' THEN 'EXC' END AS UP_ACAO"
          ."           ,A.USR_CODIGO"
          ."           ,A.USR_CPF"
          ."           ,A.USR_APELIDO"
          ."           ,A.USR_CODUP"
          ."           ,A.USR_CODCRG"          
          ."           ,A.USR_EMAIL"
          ."           ,A.USR_FECHAMENTO"
          ."           ,CASE WHEN A.USR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS USR_ATIVO"
          ."           ,CASE WHEN A.USR_REG='P' THEN 'PUB' WHEN A.USR_REG='S' THEN 'SIS' ELSE 'ADM' END AS USR_REG"
          ."           ,U.US_APELIDO"
          ."           ,A.USR_ADMPUB"
          ."     FROM BKPUSUARIO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.USR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////
        // Dados para JavaScript USUARIOEMPRESA   //
        ////////////////////////////////////////////
        if( $rotina=="selectBkpUe" ){
          $sql="SELECT A.UE_ID"
          ."           ,CAST(A.UE_DATA AS VARCHAR(10)) AS UE_DATA"
          ."           ,CASE WHEN A.UE_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.UE_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.UE_ACAO='E' THEN 'EXC' END AS UE_ACAO"
          ."           ,A.UE_CODUSR"
          ."           ,A.UE_CODEMP"          
          ."           ,CASE WHEN A.UE_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS UE_ATIVO"
          ."           ,CASE WHEN A.UE_REG='P' THEN 'PUB' WHEN A.UE_REG='S' THEN 'SIS' ELSE 'ADM' END AS UE_REG"
          ."           ,U.US_APELIDO"
          ."     FROM BKPUSUARIOEMPRESA A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.SIS_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };        
        /////////////////////////////////////////
        // Dados para JavaScript USUARIOPERFIL //
        /////////////////////////////////////////
        if( $rotina=="selectBkpUp" ){
          $sql="SELECT A.UP_ID"
          ."           ,CAST(A.UP_DATA AS VARCHAR(10)) AS UP_DATA"
          ."           ,CASE WHEN A.UP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.UP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.UP_ACAO='E' THEN 'EXC' END AS UP_ACAO"
          ."          ,A.UP_CODIGO"
          ."          ,A.UP_NOME"
          ."          ,A.UP_D01"
          ."          ,A.UP_D02"
          ."          ,A.UP_D03"
          ."          ,A.UP_D04"
          ."          ,A.UP_D05"
          ."          ,A.UP_D06"
          ."          ,A.UP_D07"
          ."          ,A.UP_D08"
          ."          ,A.UP_D09"
          ."          ,A.UP_D10"
          ."          ,A.UP_D11"
          ."          ,A.UP_D12"
          ."          ,A.UP_D13"
          ."          ,A.UP_D14"
          ."          ,A.UP_D15"
          ."          ,A.UP_D16"
          ."          ,A.UP_D17"
          ."          ,A.UP_D18"
          ."          ,A.UP_D19"
          ."          ,A.UP_D20"
          ."          ,A.UP_D21"
          ."          ,A.UP_D22"
          ."          ,A.UP_D23"
          ."          ,A.UP_D24"
          ."          ,A.UP_D25"
          ."          ,A.UP_D26"
          ."          ,A.UP_D27"
          ."          ,A.UP_D28"
          ."          ,A.UP_D29"
          ."          ,A.UP_D30"
          ."          ,A.UP_D31"
          ."          ,A.UP_D32"
          ."          ,A.UP_D33"
          ."          ,A.UP_D34"
          ."          ,A.UP_D35"
          ."          ,A.UP_D36"
          ."          ,A.UP_D37"
          ."          ,A.UP_D38"
          ."          ,A.UP_D39"
          ."          ,A.UP_D40"
          ."          ,A.UP_D41"
          ."          ,A.UP_D42"
          ."          ,A.UP_D43"
          ."          ,A.UP_D44"
          ."          ,A.UP_D45"
          ."          ,A.UP_D46"
          ."          ,A.UP_D47"
          ."          ,A.UP_D48"
          ."          ,A.UP_D49"
          ."          ,A.UP_D50"
          ."          ,CASE WHEN A.UP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS UP_ATIVO"
          ."          ,CASE WHEN A.UP_REG='P' THEN 'PUB' WHEN A.UP_REG='S' THEN 'SIS' ELSE 'ADM' END AS UP_REG"
          ."          ,U.US_APELIDO"
          ."          ,A.UP_CODUSR"
          ."     FROM BKPUSUARIOPERFIL A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.UP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //         Dados para JavaScript VEICULO          //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpVcl" ){
          $sql="SELECT VCL_ID"
          ."           ,CAST(VCL_DATA AS VARCHAR(10)) AS VCL_DATA"
          ."           ,CASE WHEN A.VCL_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.VCL_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.VCL_ACAO='E' THEN 'EXC' END AS VCL_ACAO"
          ."          ,VCL_CODIGO" 
          ."          ,VCL_CODFVR"          
          ."          ,VCL_CODVCR"
          ."          ,VCL_CODVTP"
          ."          ,VCL_CODVMD"
          ."          ,VCL_ANO"          
          ."          ,VCL_CODCNTT"                    
          ."          ,CASE WHEN A.VCL_REG='P' THEN 'PUB' WHEN A.VCL_REG='S' THEN 'SIS' ELSE 'ADM' END AS VCL_REG"
          ."          ,CASE WHEN A.VCL_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS VCL_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPVEICULO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.VCL_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ///////////////////////////////////////////
        //    Dados para JavaScript VEICULOCOR   //
        ///////////////////////////////////////////
        if( $rotina=="selectBkpVeiculoCor" ){
          $sql="SELECT VCR_ID"
          ."           ,CAST(VCR_DATA AS VARCHAR(10)) AS VCR_DATA"
          ."           ,CASE WHEN A.VCR_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.VCR_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.VCR_ACAO='E' THEN 'EXC' END AS VCR_ACAO"
          ."          ,VCR_CODIGO"
          ."          ,VCR_NOME"
          ."          ,CASE WHEN A.VCR_REG='P' THEN 'PUB' WHEN A.VCR_REG='S' THEN 'SIS' ELSE 'ADM' END AS VCR_REG"
          ."          ,CASE WHEN A.VCR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS VCR_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPVEICULOCOR A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.VCR_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //  Dados para JavaScript VEICULOFABRICANTE   //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpVfb" ){
          $sql="SELECT VFB_ID"
          ."           ,CAST(VFB_DATA AS VARCHAR(10)) AS VFB_DATA"
          ."           ,CASE WHEN A.VFB_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.VFB_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.VFB_ACAO='E' THEN 'EXC' END AS VFB_ACAO"
          ."          ,VFB_CODIGO"
          ."          ,VFB_NOME"
          ."          ,CASE WHEN A.VFB_REG='P' THEN 'PUB' WHEN A.VFB_REG='S' THEN 'SIS' ELSE 'ADM' END AS VFB_REG"
          ."          ,CASE WHEN A.VFB_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS VFB_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPVEICULOFABRICANTE A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.VFB_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //  Dados para JavaScript VEICULOMODELO       //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpVmd" ){
          $sql="SELECT VMD_ID"
          ."           ,CAST(VMD_DATA AS VARCHAR(10)) AS VMD_DATA"
          ."           ,CASE WHEN A.VMD_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.VMD_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.VMD_ACAO='E' THEN 'EXC' END AS VMD_ACAO"
          ."          ,VMD_CODIGO"
          ."          ,VMD_NOME"
          ."          ,VMD_CODVFB"
          ."          ,CASE WHEN A.VMD_REG='P' THEN 'PUB' WHEN A.VMD_REG='S' THEN 'SIS' ELSE 'ADM' END AS VMD_REG"
          ."          ,CASE WHEN A.VMD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS VMD_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPVEICULOMODELO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.VMD_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////
        //  Dados para JavaScript VEICULOTIPO         //
        ////////////////////////////////////////////////
        if( $rotina=="selectBkpVtp" ){
          $sql="SELECT VTP_ID"
          ."           ,CAST(VTP_DATA AS VARCHAR(10)) AS VTP_DATA"
          ."           ,CASE WHEN A.VTP_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.VTP_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.VTP_ACAO='E' THEN 'EXC' END AS VTP_ACAO"
          ."          ,VTP_CODIGO"
          ."          ,VTP_NOME"
          ."          ,CASE WHEN A.VTP_REG='P' THEN 'PUB' WHEN A.VTP_REG='S' THEN 'SIS' ELSE 'ADM' END AS VTP_REG"
          ."          ,CASE WHEN A.VTP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS VTP_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPVEICULOTIPO A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.VTP_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        ////////////////////////////////////////////////////
        //         Dados para JavaScript VENDEDOR         //
        ////////////////////////////////////////////////////
        if( $rotina=="selectBkpVnd" ){
          $sql="SELECT VND_ID"
          ."           ,CAST(VND_DATA AS VARCHAR(10)) AS VND_DATA"
          ."           ,CASE WHEN A.VND_ACAO='I' THEN 'INC'" 
          ."                 WHEN A.VND_ACAO='A' THEN 'ALT'" 
          ."                 WHEN A.VND_ACAO='E' THEN 'EXC' END AS VND_ACAO"
          ."          ,VND_CODFVR"
          ."          ,VND_CODEMP"
          ."          ,VND_CODLGN"          
          ."          ,CASE WHEN A.VND_REG='P' THEN 'PUB' WHEN A.VND_REG='S' THEN 'SIS' ELSE 'ADM' END AS VND_REG"
          ."          ,CASE WHEN A.VND_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS VND_ATIVO"
          ."          ,US_APELIDO"
          ."     FROM BKPVENDEDOR A" 
          ."     LEFT OUTER JOIN USUARIOSISTEMA U ON A.VND_CODUSR=U.US_CODIGO"
          .$lote[0]->where;
        };
        if( $sql != "" ){  
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
      };
    } catch(Exception $e ){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]'; 
    };    
    echo $retorno;
    exit;
  };  
?>
