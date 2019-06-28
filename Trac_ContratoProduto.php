<?php
  session_start();
  if( isset($_POST["contratoproduto"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");                               
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["contratoproduto"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd  = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $arrUpdt  = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        if( $lote[0]->rotina=="detAuto" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("detAuto",$lote[0]->login,$lote[0]->codgmp);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        /////////////////////////
        // Gerar OS de instalacao
        /////////////////////////
        if( $lote[0]->rotina=="geraros" ){
          foreach ( $lote as $reg ){          
            ////////////////////////////////////////////////
            // Obrigatorio achar o registro para atualizacao
            ////////////////////////////////////////////////
            $sql ="SELECT CNTP_IDUNICO FROM CONTRATOPRODUTO WITH(NOLOCK)";
            $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else {                
              $sql="";
              $sql.="UPDATE VCONTRATOPRODUTO";
              $sql.="   SET CNTP_ACAO=".$reg->cntp_acao;
              $sql.="       ,CNTP_CODUSR=".$_SESSION["usr_codigo"];
              $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";  
              array_push($arrUpdt,$sql);                                    
              $atuBd = true;
            };  
          };
        };
        /////////////////////////
        // Nova OS
        /////////////////////////
        if( $lote[0]->rotina=="cados" ){
          foreach ( $lote as $reg ){          
            ////////////////////////////////////////////////
            // Obrigatorio achar o registro para atualizacao
            ////////////////////////////////////////////////
            $sql ="SELECT CNTP_IDUNICO FROM CONTRATOPRODUTO WITH(NOLOCK)";
            $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else { 
              $ref=$reg->cntp_codmsg;  
              $sql="INSERT INTO VORDEMSERVICO(";
              $sql.="OS_CODMSG";
              $sql.=",OS_CODCNTT";
              $sql.=",OS_CODGMP";          
              $sql.=",OS_CODPEI";
              $sql.=",OS_DTAGENDA";
              $sql.=",OS_CODVCL";
              $sql.=",OS_CODENTREGA";
              $sql.=",OS_CODINSTALA";
              $sql.=",OS_LOCAL";
              $sql.=",OS_ESTOQUE";              
              $sql.=",OS_CODUSR) VALUES(";
              $sql.="'$ref'";                         // OS_CODMSG
              $sql.=",".$reg->cntp_codcntt;           // OS_CODCNTT
              $sql.=",".$reg->cntp_codgmp;            // OS_CODGMP          
              $sql.=",".$reg->cntp_codpei;            // OS_CODPEI
              $sql.=",'".$reg->cntp_dtagenda."'";     // OS_DTAGENDA
              $sql.=",'".$reg->cntp_codvcl."'";       // OS_CODVCL
              $sql.=",".$reg->cntp_codentrega;        // OS_CODENTREGA
              $sql.=",".$reg->cntp_codinstala;        // OS_CODINSTALACAO
              $sql.=",'".$reg->cntp_local."'";        // OS_LOCAL
              $sql.=",'CLN'";                         // OS_ESTOQUE
              $sql.=",".$_SESSION["usr_codigo"];      // OS_CODUSR
              $sql.=")";              
              array_push($arrUpdt,$sql);                                    
              $atuBd = true;
            };
          };
        };
        //////////////////
        // Ativacao
        //////////////////
        if( $lote[0]->rotina=="ativa" ){
          ////////////////////////////////////////////////
          // Obrigatorio achar o registro para atualizacao
          ////////////////////////////////////////////////
          $sql ="SELECT CNTP_IDUNICO FROM CONTRATOPRODUTO WITH (NOLOCK)";
          $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_IDUNICO=".$lote[0]->cntp_idunico.") AND (CNTP_CODGMP=".$lote[0]->cntp_codgmp."))";
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
          } else {                
            $sql="";
            $sql.="UPDATE VCONTRATOPRODUTO";
            $sql.="   SET CNTP_DTATIVACAO='".$lote[0]->cntp_dtativacao."'";
            $sql.="       ,CNTP_LOCALINSTALACAO='".$lote[0]->cntp_localinstalacao."'";
            $sql.="       ,CNTP_ACAO=".$lote[0]->cntp_acao;
            $sql.="       ,CNTP_CODUSR=".$_SESSION["usr_codigo"];
            $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_CODGMP=".$lote[0]->cntp_codgmp."))";  
            array_push($arrUpdt,$sql);                                    
            $atuBd = true;
          };  
        };
        /////////////////////////
        // Codigo do correio
        /////////////////////////
        if( $lote[0]->rotina=="codcorreio" ){
          foreach ( $lote as $reg ){
            ////////////////////////////////////////////////
            // Obrigatorio achar o registro para atualizacao
            ////////////////////////////////////////////////
            $sql ="SELECT CNTP_IDUNICO FROM CONTRATOPRODUTO WITH (NOLOCK)";
            $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else {                
              $sql ="UPDATE VCONTRATOPRODUTO";
              $sql.="   SET CNTP_CODRASTREIO='".$reg->cntp_codrastreio."'";
              $sql.="       ,CNTP_DTENTREGA='".$reg->cntp_dtentrega."'";              
              $sql.="       ,CNTP_ACAO=0";              
              $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
              array_push($arrUpdt,$sql); 
              $atuBd = true;
            };    
          };
        };
        /////////////////////////
        // Modo entrega
        /////////////////////////
        if( $lote[0]->rotina=="modoentrega" ){
          foreach ( $lote as $reg ){
            ////////////////////////////////////////////////
            // Obrigatorio achar o registro para atualizacao
            ////////////////////////////////////////////////
            $sql ="SELECT CNTP_IDUNICO FROM CONTRATOPRODUTO WITH (NOLOCK)";
            $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else {                
              $sql ="UPDATE VCONTRATOPRODUTO";
              $sql.="   SET CNTP_MODOENTREGA='".$reg->cntp_modoentrega."'";
              $sql.="       ,CNTP_ACAO=4";              
              $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp.")";
              $sql.="   AND (CNTP_MODOENTREGA<>'".$reg->cntp_modoentrega."'))";
              array_push($arrUpdt,$sql); 
              $atuBd = true;
            };    
          };
        };
        /////////////////////////
        // Status entrega
        /////////////////////////
        if( $lote[0]->rotina=="statusentrega" ){
          foreach ( $lote as $reg ){
            ////////////////////////////////////////////////
            // Obrigatorio achar o registro para atualizacao
            ////////////////////////////////////////////////
            $sql ="SELECT CNTP_IDUNICO FROM CONTRATOPRODUTO WITH (NOLOCK)";
            $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else {                
              $sql ="UPDATE VCONTRATOPRODUTO";
              $sql.="   SET CNTP_STATUSENTREGA='".$reg->cntp_statusentrega."'";
              $sql.="       ,CNTP_ACAO=5";              
              $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
              array_push($arrUpdt,$sql); 
              $atuBd = true;
            };    
          };
        };
        /////////////////////////
        // Agendamento
        /////////////////////////
        if( $lote[0]->rotina=="agenda" ){
          foreach ( $lote as $reg ){
            ////////////////////////////////////////////////
            // Obrigatorio achar o registro para atualizacao
            ////////////////////////////////////////////////
            $sql ="SELECT CNTP_IDUNICO FROM CONTRATOPRODUTO WITH (NOLOCK)";
            $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else {                
              $sql ="UPDATE VCONTRATOPRODUTO";
              $sql.="   SET CNTP_CODENTREGA=".$reg->cntp_codentrega;
              $sql.="       ,CNTP_CODINSTALA=".$reg->cntp_codinstala;                
              $sql.="       ,CNTP_DTAGENDA=".($reg->cntp_dtagenda=="00/00/0000" ? "null" : "'".$reg->cntp_dtagenda."'");
              $sql.="       ,CNTP_CODPEI=".$reg->cntp_codpei;                              
              $sql.="       ,CNTP_ACAO=".$lote[0]->acao;              
              $sql.=" WHERE ((CNTP_CODCNTT=".$reg->cntp_codcntt.") AND (CNTP_IDUNICO=".$reg->cntp_idunico.") AND (CNTP_CODGMP=".$reg->cntp_codgmp."))";
              array_push($arrUpdt,$sql); 
              $atuBd = true;
            };    
          };
        };
        ///////////////////////////////////////////////
        //            COMPOSICAO DO AUTO             //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="hlpComposicao" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("hlpComposicao",$lote[0]->login,$lote[0]->codamp);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        //////////////////
        // Placa cadastrar
        //////////////////
        if( $lote[0]->rotina=="placacad" ){
          $sql="";
          $sql.="UPDATE VCONTRATOPRODUTO";
          $sql.="   SET CNTP_PLACACHASSI='".$lote[0]->cntp_placachassi."'";
          $sql.="       ,CNTP_ACAO=".$lote[0]->cntp_acao;
          $sql.="       ,CNTP_CODUSR=".$_SESSION["usr_codigo"];
          $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_CODGMP=".$lote[0]->cntp_codgmp."))";     
          array_push($arrUpdt,$sql);                                    
          $atuBd = true;
        };
        //////////////////
        // Placa retirar
        //////////////////
        if( $lote[0]->rotina=="placaexc" ){
          $sql="";
          $sql.="UPDATE VCONTRATOPRODUTO";
          $sql.="   SET CNTP_PLACACHASSI='".$lote[0]->cntp_placachassi."'";   // Esta para pode usar no trigger
          $sql.="       ,CNTP_ACAO=".$lote[0]->cntp_acao;
          $sql.="       ,CNTP_CODUSR=".$_SESSION["usr_codigo"];
          $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_CODGMP=".$lote[0]->cntp_codgmp."))";     
          array_push($arrUpdt,$sql);                                    
          $atuBd = true;
        };
        //////////
        // Empenho
        //////////
        if( $lote[0]->rotina=="empenhocad" ){
          $sql="";
          $sql.="UPDATE VCONTRATOPRODUTO";
          $sql.="   SET CNTP_CODGMP=".$lote[0]->cntp_codgmp;
          $sql.="       ,CNTP_ACAO=".$lote[0]->cntp_acao;
          $sql.="       ,CNTP_CODUSR=".$_SESSION["usr_codigo"];
          $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_IDUNICO=".$lote[0]->cntp_idunico."))";     
          array_push($arrUpdt,$sql);                                    
          $atuBd = true;
        };
        if( $lote[0]->rotina=="empenhoexc" ){
          $sql="";
          $sql.="UPDATE VCONTRATOPRODUTO";
          $sql.="   SET CNTP_CODGMP=0";
          $sql.="       ,CNTP_ACAO=".$lote[0]->cntp_acao;
          $sql.="       ,CNTP_CODUSR=".$_SESSION["usr_codigo"];
          $sql.=" WHERE ((CNTP_CODCNTT=".$lote[0]->cntp_codcntt.") AND (CNTP_IDUNICO=".$lote[0]->cntp_idunico."))";     
          array_push($arrUpdt,$sql);                                    
          $atuBd = true;
        };
        //  
        if( $lote[0]->rotina=="filtrar" ){        
          $sql="";
          $sql.="SELECT A.CNTP_CODCNTT AS CONTRATO";
          $sql.="      ,A.CNTP_IDUNICO AS IDUNICO";
          $sql.="      ,A.CNTP_IDSRV AS IDSRV";
          $sql.="      ,A.CNTP_CODGM AS CODGM";
          $sql.="      ,GM.GM_NOME AS REFERENTE";
          $sql.="      ,A.CNTP_CODGP AS GRP";
          $sql.="      ,A.CNTP_CODGMP AS CODGMP";
          $sql.="      ,A.CNTP_VALOR AS VALOR";
          $sql.="      ,CASE WHEN GMP.GMP_CODPE IS NULL THEN 'EST' ELSE GMP.GMP_CODPE END AS PE";
          $sql.="      ,CASE WHEN GMP.GMP_DTCONFIGURADO IS NULL THEN 'NAO' ELSE 'SIM' END AS CFG";          
          $sql.="      ,CONVERT(VARCHAR(10),GMP.GMP_DTEMPENHO,127) AS EMPENHO";          
          $sql.="      ,A.CNTP_MODOENTREGA AS ME";          
          $sql.="      ,A.CNTP_STATUSENTREGA AS SE";                    
          $sql.="      ,CASE WHEN A.CNTP_CODRASTREIO IS NULL THEN '' ELSE 'ok' END AS RE";                    
          $sql.="      ,CEE.CNTE_CEP AS CEPENTREGA";          
          $sql.="      ,CEI.CNTE_CEP AS CEPINSTALA";                    
          $sql.="      ,CONVERT(VARCHAR(10),A.CNTP_DTAGENDA,127) AS DTAGENDA";                    
          $sql.="      ,GMP.GMP_CODPEI AS CODFVR";          
          $sql.="      ,COALESCE(FVR.FVR_APELIDO,'') AS COLABORADOR";          
          $sql.="      ,A.CNTP_CODOS AS OS";                    
          $sql.="      ,A.CNTP_AGENDADO AS AC";          
          $sql.="      ,CASE WHEN ((GMP.GMP_PLACACHASSI IS NULL) OR (GMP.GMP_PLACACHASSI='NSA')) THEN '' ELSE GMP.GMP_PLACACHASSI END AS PLACACHASSI";
          $sql.="      ,CONVERT(VARCHAR(10),A.CNTP_DTATIVACAO,127) AS ATIVADO";          
          $sql.="      ,CASE WHEN GMP.GMP_NUMSERIE IS NULL THEN '' ELSE GMP.GMP_NUMSERIE END AS NUMSERIE";
          $sql.="      ,US.US_APELIDO";                    
          $sql.="      ,A.CNTP_CODENTREGA AS CODENTREGA";          
          $sql.="      ,A.CNTP_CODINSTALA AS CODINSTALA";
          $sql.="      ,CONCAT('<table class=''fpTable'' style=''width:250px;''>'";
          $sql.="       ,'<thead class=''fpThead''>'";    
          $sql.="       ,'<tr>'";
          $sql.="       ,'<th class=''fpTh'' style=''width:40%;''> CAMPO'";
          $sql.="       ,'</th>'";
          $sql.="       ,'<th class=''fpTh'' style=''width:60%;''> CONTEUDO'";
          $sql.="       ,'</th>'";
          $sql.="       ,'</tr>'";
          $sql.="       ,'</thead>'";          
          $sql.="       ,'<tbody>'";              
          $sql.="       ,'<tr>'";
          $sql.="       ,'<td class=''fpTd''>GRUPO</td>'";          
          $sql.="       ,'<td class=''fpTd''>',CNTP_CODGP,'</td>'";
          $sql.="       ,'</tr>'";          
          $sql.="       ,'<tr>'";
          $sql.="       ,'<td class=''fpTd''>CODIGO AUTO</td>'";          
          $sql.="       ,'<td class=''fpTd''>',CNTP_CODGMP,'</td>'";
          $sql.="       ,'</tr>'";          
          $sql.="       ,'<tr>'";
          $sql.="       ,'<td class=''fpTd''>VALOR</td>'";          
          $sql.="       ,'<td class=''fpTd''>',CNTP_VALOR,'</td>'";
          $sql.="       ,'</tr>'";          
          $sql.="       ,'<tr>'";          
          $sql.="       ,'<td class=''fpTd''>COD RASTREADOR</td>'";          
          $sql.="       ,'<td class=''fpTd''>',CNTP_CODRASTREIO,'</td>'";
          $sql.="       ,'</tr>'";          
          $sql.="       ,'<tr>'";          
          $sql.="       ,'<td class=''fpTd''>ENTREGA</td>'";          
          $sql.="       ,'<td class=''fpTd''>',CONVERT(VARCHAR(10),CNTP_DTENTREGA,103),'</td>'";
          $sql.="       ,'</tr>'";          
          $sql.="       ,'<tr>'";
          $sql.="       ,'<td class=''fpTd''>USUARIO</td>'";          
          $sql.="       ,'<td class=''fpTd''>',US_APELIDO,'</td>'";
          $sql.="       ,'</tr>'";          
          $sql.="       ,'</tbody>'";                       
          $sql.="       ,'</table>') AS POPOVER";
          $sql.="  FROM CONTRATOPRODUTO A WITH (NOLOCK)";                    
          $sql.="  LEFT OUTER JOIN GRUPOMODELO GM ON A.CNTP_CODGM=GM.GM_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.CNTP_CODUSR=US.US_CODIGO"; 
          $sql.="  LEFT OUTER JOIN GRUPOMODELOPRODUTO GMP ON A.CNTP_CODGMP=GMP.GMP_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON GMP.GMP_CODPEI=FVR.FVR_CODIGO";            // Tem que ser pelo auto pois este é unico
          $sql.="  LEFT OUTER JOIN CONTRATOENDERECO CEE ON A.CNTP_CODENTREGA=CEE.CNTE_CODIGO";
          $sql.="  LEFT OUTER JOIN CONTRATOENDERECO CEI ON A.CNTP_CODINSTALA=CEI.CNTE_CODIGO";          
          $sql.=" WHERE (A.CNTP_CODCNTT='".$lote[0]->codcntt."')"; 
//file_put_contents("aaa.xml",$sql);
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR"
                        ,"dados":""
                        ,"erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        ///////////////////////////////////////////////////////////////////
        // Atualizando o banco de dados se opcao de insert/updade/delete //
        ///////////////////////////////////////////////////////////////////
        //ANGELO KOKISO ALTERAÇÃO NO RETORNO 
        if( $atuBd ){
          if( count($arrUpdt) >0 ){
            $retCls=$classe->cmd($arrUpdt);
            if( $retCls['retorno']=="OK" ){ 
              $retorno='[{ "retorno":"OK"
                          ,"dados":"'.count($arrUpdt).' REGISTRO(s) ATUALIZADO(s)!"
                          ,"erro":""}]'; 
            } else {
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
            };  
          } else {
            $retorno='[{"retorno":"OK","dados":"","erro":"NENHUM REGISTRO CADASTRADO!"}]';
          };  
        };
        //FIM ALTERAÇÃO ANGELO
      };  
    } catch(Exception $e ){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]'; 
    };    
    echo $retorno;
    exit;
  };  

?>
<!DOCTYPE html>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <script language="javascript" type="text/javascript"></script>
  <head>
    <meta charset="utf-8">
    <title>Contrato</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaGrupoModeloProdutoF10.js"></script>        
    <script src="tabelaTrac/f10/tabelaEnderecoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaColaboradorF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaPlacaF10.js"></script>        
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        ////////////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_Contrato.php
        ////////////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addInd")).lote[0];
        $doc("spnTitulo").innerHTML=pega.qualRotina;
        $doc("spnCodCntt").innerHTML=jsNmrs(pega.codCntt).emZero(4).ret();
        //localStorage.removeItem("addInd"); //voltarrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr
        
        jsCntI={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"tamGrd"         : "0em"             
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "CONTRATO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]
                      ,"align"          : "center"  
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "IDUNICO"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "IDSRV"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "CODGM"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "REFERENTE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "80"
                      ,"truncate"       : "S"                                            
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "GRP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "CODGMP"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "VALOR" 
                      ,"fieldType"      : "flo2"            
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "PE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"  
                      ,"popoverTitle"   : "Ponto de estoque"    
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "CFG"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"  
                      ,"popoverLabelCol": "Configurado"
                      ,"popoverTitle"   : "Para empenho auto deve estar configurado"
                      ,"funcCor"        : "(objCell.innerHTML=='NAO' ? objCell.classList.add('corFonteAlterado') : objCell.classList.remove('corFonteAlterado'))"                      
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "EMPENHO"
                      ,"fieldType"      : "dat"
                      ,"align"          : "center"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "16"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "ME"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"  
                      ,"funcCor"        : "switch (objCell.innerHTML) { case 'COR':objCell.classList.add('corAzul');break; case 'TRA':objCell.classList.add('corVerde');break; case 'MAO':objCell.classList.add('corAlterado');break;};"                      
                      ,"popoverLabelCol": "Modalidade da entregra"
                      ,"popoverTitle"   : "Correio, Transportadora ou em mãos"
                      //,"popoverCell"    : "(ceContext.data=='COR' ? 'Correio' : (ceContext.data=='TRA' ? 'Transportadora' : 'Em mãos'))"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "SE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"  
                      ,"funcCor"        : "(objCell.innerHTML!='ENT' ? objCell.classList.add('corFonteAlterado') : objCell.classList.remove('corFonteAlterado'))"
                      ,"popoverLabelCol": "Status da entrega"
                      ,"popoverTitle"   : "Aguardando, Despachado,Entregue ou Transito"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "RE"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"                      
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"                   
                      ,"popoverTitle"   : "Para ver o codigo passar mouse na coluna POP"                          
                      ,"popoverLabelCol": "Rastreamento/Entrega"                      
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "ENTREGA"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"                      
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"                   
                      ,"popoverTitle"   : "Se informado o campo se refere ao CEP. Valido apenas para auto com status <b>ENT</b>regue - (coluna SE)"                          
                      ,"popoverLabelCol": "Endereço de entrega"                      
                      ,"padrao":0}
            ,{"id":16 ,"labelCol"       : "INSTALA"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"                      
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"                      
                      ,"popoverTitle"   : "Endereço de instalação. Se informado o campo se refere ao CEP"                                                
                      ,"padrao":0}
            ,{"id":17 ,"labelCol"       : "AGENDA"
                      ,"fieldType"      : "dat"
                      ,"align"          : "center"                      
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"                      
                      ,"popoverTitle"   : "Data agendada para instalação do auto"                          
                      ,"popoverLabelCol": "Agendamento"                      
                      ,"padrao":0}
            ,{"id":18  ,"labelCol"      : "CODPEI"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":19 ,"labelCol"       : "COLABORADOR"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":20  ,"labelCol"      : "OS"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"formato"        : ["i6"]                      
                      ,"popoverTitle"   : "Numero da OS referente instalação"                          
                      ,"popoverLabelCol": "Ordem serviço"                      
                      ,"padrao":0}
            ,{"id":21 ,"labelCol"       : "AC"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"                   
                      ,"popoverTitle"   : "Para <b>AGENDAMENTO COMPLETO</b> deve ser informado<hr>Endereco de entrega e instalação<br>Data para instalação<br>Colaborador"                          
                      ,"padrao":0}
            ,{"id":22 ,"labelCol"       : "PLACA_CHASSI"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":23 ,"labelCol"       : "ATIVADO"
                      ,"fieldType"      : "dat"
                      ,"align"          : "center"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":24 ,"labelCol"       : "SERIE"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"truncate"       : "S"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":25 ,"labelCol"       : "USUARIO" 
                      ,"fieldType"      : "str"            
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"excel"          : "S"                      
                      ,"padrao":0} 
            ,{"id":26 ,"labelCol"       : "CODENTREGA"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":27 ,"labelCol"       : "CODINSTALA"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":28 ,"labelCol"       : "POP"         
                      ,"obj"            : "imgPP"
                      ,"tamGrd"         : "5em"
                      ,"fieldType"      : "popover"
                      ,"popoverTitle"   : "Pop up de campos relacionados a este registro"                      
                      ,"padrao":0}
            ,{"id":29 ,"labelCol"       : "COMP"         
                      ,"obj"            : "imgPP"
                      ,"tamGrd"         : "5em"
                      ,"tipo"           : "img"
                      ,"fieldType"      : "img"
                      ,"func"           : "horComposicaoClick(this.parentNode.parentNode.cells[7].innerHTML);"
                      ,"popoverTitle"   : "Composicao do auto"                                                
                      ,"tagI"           : "fa fa-clone"
                      ,"padrao":0}
                      
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"300px" 
              ,"label"          :"Grupo de favorecido"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Empenho"           ,"name":"horEmpenhoCad"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-repeat"}               
            ,{"texto":"Empenho"           ,"name":"horEmpenhoExc"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-rotate-left"}
            ,{"texto":"Modo entrega"      ,"name":"horModoEntrega"  ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-envelope-o"
                                          ,"popover":{title:"Modo de entregra",texto:"Selecione itens da grade para informar como será entregue o auto(correio/transportadora/em mãos)"}} 
            ,{"texto":"Status entrega"    ,"name":"horStatusEntrega","onClick":"7"  ,"enabled":true ,"imagem":"fa fa-envelope-o"
                                          ,"popover":{title:"Status da entregra",texto:"Selecione itens da grade para informar o status da entrega<br>Aguardando, despachado, transito ou entregue"}} 
            ,{"texto":"Agendamento"       ,"name":"horAgenda"       ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-calendar"
                                          ,"popover":{title:"Opção para informar",texto:"<b>-</b>Endereco de entrega<br><b>-</b>Endereço de instalacao<br><b>-</b>Data de agendamento<br><b>-</b>Colaborador responsavel pela instalação"}}                                           
            ,{"texto":"OS"                ,"name":"horGerarOs"       ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-wrench"
                                          ,"popover":{title:"Opção para gerar OS",texto:"Gera automaticamente uma OS de instalação para cada auto"}} 
            ,{"texto":"Placa"             ,"name":"horPlacaCad"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-truck"
                                          ,"popover":{title:"Adicionar placa",texto:"Adiciona uma nova <b>placa</b> para este contrato"}}             
            ,{"texto":"Placa"             ,"name":"horPlacaExc"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-truck"            
                                          ,"popover":{title:"Remover placa",texto:"Remove a <b>placa</b> selecionada do contrato",aviso:"warning"}}             
            ,{"texto":"Marcar"            ,"name":"horMarcar"       ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-check"                        
                                          ,"popover":{title:"Marcar/Desmarcar",texto:"Inverte todas as linhas da grade pela coluna OPC(marcado)"}}
            ,{"texto":"Excel"             ,"name":"horExcel"        ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"} 
            ,{"texto":"Imprimir"          ,"name":"horImprimir"     ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"}                    
            ,{"texto":"Fechar"            ,"name":"horFechar"       ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"}
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)    
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmCntI"                 // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCntI"              // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCntI"                 // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnCntI"                // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblCntI"                 // Nome da table
          ,"prefixo"        : "CntI"                    // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "130em"                   // Tamanho da table
          ,"height"         : "60em"                    // Altura da table
          ,"nChecks"        : (pega.nChecks=="false" ? false : true)              // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                   // Se tiver menu esquerdo
          ,"relTitulo"      : "ITEM CONTRATO"           // Titulo do relatório
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"indiceTable"    : "GRUPO"                   // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
        }; 
        if( objCntI === undefined ){  
          objCntI=new clsTable2017("objCntI");
        };  
        objCntI.montarHtmlCE2017(jsCntI);
        $doc("dPaifrmCntI").style.float="none";
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsCntI,[ "AC"         ,"CFG"  ,"CODENTREGA" ,"CODPEI"   ,"CODINSTALA" ,"CODGM"    ,"CODGMP" ,"ATIVADO"  ,"COLABORADOR"
                                              ,"CONTRATO"   ,"RE"   ,"AGENDA"     ,"EMPENHO"  ,"ENTREGA"  ,"GRP"    ,"IDUNICO"  ,"IDSRV"      
                                              ,"INSTALA"    ,"ME"   ,"OS"         ,"PE"       ,"PLACA_CHASSI" 
                                              ,"REFERENTE"  ,"SE"   ,"SERIE"      ,"USUARIO"  ,"VALOR" ]);
        switch( pega.qualRotina ){
          case 'EMPENHO':
            $doc("horPlacaCad").style.display="none";
            $doc("horPlacaExc").style.display="none";
            $doc("horAgenda").style.display="none";
            $doc("horMarcar").style.display="none";
            $doc("horModoEntrega").style.display="none";
            $doc("horStatusEntrega").style.display="none";
            $doc("horGerarOs").style.display="none";            
            $doc("horEmpenhoExc").style.color="red";
            break;
          case 'AGENDA':
            $doc("horPlacaCad").style.display="none";
            $doc("horPlacaExc").style.display="none";
            $doc("horEmpenhoCad").style.display="none";
            $doc("horEmpenhoExc").style.display="none";
            $doc("collapseCorreio").style.display="block";            
            break;
          case 'OS':
            $doc("horEmpenhoExc").style.display="none";         
            $doc("horEmpenhoCad").style.display="none";                     
            $doc("horPlacaCad").style.display="none";
            $doc("horPlacaExc").style.display="none";
            //$doc("horAgenda").style.display="none";
            $doc("horMarcar").style.display="none";
            $doc("horModoEntrega").style.display="none";
            $doc("horStatusEntrega").style.display="none";
            $doc("horGerarOs").style.display="none"; 
            $doc("spnAgenda").innerHTML="Nova OS";
            break;
            
          case 'PLACA':
            $doc("horEmpenhoCad").style.display="none";
            $doc("horEmpenhoExc").style.display="none";
            $doc("horAgenda").style.display="none";
            $doc("horMarcar").style.display="none";     
            $doc("horModoEntrega").style.display="none";  
            $doc("horStatusEntrega").style.display="none";      
            $doc("horGerarOs").style.display="none";                        
            $doc("collapseAtiva").style.display="block";            
            $doc("horPlacaExc").style.color="red";        
            break;
        };  
        jsCntI.relTitulo="ITEM CONTRATO "+jsNmrs(pega.codCntt).emZero(6).ret();
        btnFiltrarClick();
        adicionarDataToggle();  // Mostra popover para campos na grade com grade dentro
      });
      //
      var objCntI;                    // Obrigatório para instanciar o JS Semestral
      var jsCntI;                     // Obj principal da classe clsTable2017
      var objGmpF10                   // Obrigatório para instanciar JS GrupoModeloProduto      
      var objCntEF10                  // Obrigatório para instanciar JS GrupoModeloProduto      
      var objColF10                   // Obrigatório para instanciar JS Colaborador
      var objPlcF10                   // Obrigatório para instanciar JS Placa
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var clsErro;                    // Classe para erros 
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var pega;                       // Recuperar localStorage      
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario            
      //var oEnd;                       // Para saber se estou informando um endereco de entrega ou de instalacao
      var abrePlaca;      
      var ppvCorreio;                 // Abrir popover somente com click(Codigo rastreamento)
      var evtCorreio;                 // Eventos
      var ppvAtiva;                   // Abrir popover somente com click(Data de ativacao)
      var evtAtiva;                   // Eventos
      
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      /////////////////////////////////////////////////////////
      // Funcao geral para atualizar grade em Trac_Contrato.php
      /////////////////////////////////////////////////////////
      function atualizaGradeContrato(contrato,colCntt,colAltera,qtos){
        if( qtos != 0 ){
          this.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
            if( parseInt(contrato)==jsNmrs(row.cells[colCntt].innerHTML).inteiro().ret() ){
              row.cells[colAltera].innerHTML=( jsNmrs(row.cells[colAltera].innerHTML).inteiro().ret() + qtos );
            };
          });
        };
      };  
      function horMarcarClick(){            
        objCntI.marcarDesmarcar();            
      };
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "filtrar"                         );
        clsJs.add("login"   , jsPub[0].usr_login                );
        clsJs.add("codcntt" , pega.codCntt                      );
        fd = new FormData();
        fd.append("contratoproduto" , clsJs.fim());
        msg     = requestPedido("Trac_ContratoProduto.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsCntI.registros=objCntI.addIdUnico(retPhp[0]["dados"]);
          objCntI.ordenaJSon(jsCntI.indiceTable,false);  
          objCntI.montarBody2017();
        };  
      };
      /////////////////////////////////////////////
      //      AJUDA PARA ENDERECOENTREGA         //
      /////////////////////////////////////////////
      function entFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",$doc(obj.id).value); 
      };
      function entF10Click(obj){ 
        fEnderecoF10(0,"nsa","edtCodIns",100
          ,{codfvr:pega.codFvr
            ,ativo:"S" 
            ,divWidth:"76em"
            ,tblWidth:"74em"
            ,tbl:"tblEnt"
        }); 
      };
      function RetF10tblEnt(arr){
        $doc("edtCodEnt").value      = arr[0].CODIGO;
        $doc("edtDesEnt").value      = arr[0].ENDERECO;
        $doc("edtCddEnt").value      = arr[0].CIDADE;        
        $doc("edtCepEnt").value      = arr[0].CEP;                
        $doc("edtCodEnt").setAttribute("data-oldvalue",arr[0].CODIGO);
        if( jsNmrs("edtCodIns").inteiro().ret()==0 ){
          $doc("edtCodIns").value      = $doc("edtCodEnt").value;
          $doc("edtDesIns").value      = $doc("edtDesEnt").value;
          $doc("edtCddIns").value      = $doc("edtCddEnt").value;        
          $doc("edtCepIns").value      = $doc("edtCepEnt").value;                
          $doc("edtCodIns").setAttribute("data-oldvalue",arr[0].CODIGO);
        };
      };
      function codEntBlur(obj){
        var elOld = jsNmrs($doc(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fEnderecoF10(1,"nsa","edtCodIns",100
            ,{ codfvr:pega.codFvr
              ,ativo:"S" 
              ,divWidth:"76em"
              ,tblWidth:"74em"
              ,where:" AND CNTE_CODIGO="+elNew 
              ,tbl:"tblEnt"
          }); 
          $doc(obj.id).value           = ( arr.length == 0 ? "0000"            : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          $doc("edtDesEnt").value      = ( arr.length == 0 ? "*"               : arr[0].ENDERECO                       );
          $doc("edtCddEnt").value      = ( arr.length == 0 ? "*"               : arr[0].CIDADE                         );
          $doc("edtCepEnt").value      = ( arr.length == 0 ? "*"               : arr[0].CEP                            );
          $doc(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )                       );
          if( jsNmrs("edtCodIns").inteiro().ret()==0 ){
            $doc("edtCodIns").value      = $doc("edtCodEnt").value;
            $doc("edtDesIns").value      = $doc("edtDesEnt").value;
            $doc("edtCddIns").value      = $doc("edtCddEnt").value;        
            $doc("edtCepIns").value      = $doc("edtCepEnt").value;                
            $doc("edtCodIns").setAttribute("data-oldvalue",arr[0].CODIGO);
          };
        };
      };
      /////////////////////////////////////////////
      //      AJUDA PARA INSTALACAO              //
      /////////////////////////////////////////////
      function insFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",$doc(obj.id).value); 
      };
      function insF10Click(obj){ 
        fEnderecoF10(0,"nsa","edtData",100
          ,{codfvr:pega.codFvr
            ,divWidth:"76em"
            ,tblWidth:"74em"
            ,tbl:"tblIns"
        }); 
      };
      function RetF10tblIns(arr){
        $doc("edtCodIns").value      = arr[0].CODIGO;
        $doc("edtDesIns").value      = arr[0].ENDERECO;
        $doc("edtCddIns").value      = arr[0].CIDADE;        
        $doc("edtCepIns").value      = arr[0].CEP;                
        $doc("edtCodIns").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codInsBlur(obj){
        var elOld = jsNmrs($doc(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fEnderecoF10(1,"nsa","edtData",100
            ,{ codfvr:pega.codFvr
              ,ativo:"S" 
              ,divWidth:"76em"
              ,tblWidth:"74em"
              ,where:" AND CNTE_CODIGO="+elNew 
              ,tbl:"tblEnt"
          }); 
          $doc("edtCodIns").value      = ( arr.length == 0 ? "0000"            : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          $doc("edtDesIns").value      = ( arr.length == 0 ? ""                : arr[0].ENDERECO );
          $doc("edtCddIns").value      = ( arr.length == 0 ? ""                : arr[0].CIDADE );
          $doc("edtCepIns").value      = ( arr.length == 0 ? ""                : arr[0].CEP );
          $doc("edtCodIns").setAttribute("data-oldvalue",$doc("edtCodIns").value);
        };
      };
      ///////////////////////////////
      // Empenhar auto em um contrato
      ///////////////////////////////
      function horEmpenhoCadClick(){
        try{
          chkds=objCntI.gerarJson("1").gerar();
          msg         = "ok";
          clsJs       = jsString("lote");

          if( chkds[0].EMPENHO != "" )
            throw "AUTO JA EMPENHADO!";
          //if( chkds[0].CFG == "NAO" )
          //  throw "AUTO DEVE ESTAR CONFIGURADO!";
          
          fGrupoModeloProdutoF10(0,"nsa","null",100
            ,{codgm:parseInt(chkds[0].CODGM)
              ,codaut:0
              ,codpe:"EST"
              ,codaut:0 
              ,divWidth:"76em"
              ,tblWidth:"74em"
          }); 
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function RetF10tblGmp(arr){
        ///////////////////////////////////////////////////////////////////////////
        // Classe para montar envio para o Php
        // Colocando o CODGMP em todos os itens devido relacionamento EMPENHO/SERIE
        ///////////////////////////////////////////////////////////////////////////    
        clsJs = jsString("lote");        
        clsJs.add("rotina"        , "empenhocad"                        );              
        clsJs.add("login"         , jsPub[0].usr_login                  );
        clsJs.add("gmp_dtempenho" , jsDatas(0).retMMDDYYYY()            );            
        clsJs.add("cntp_codcntt"  , chkds[0].CONTRATO                   );
        clsJs.add("cntp_idunico"  , chkds[0].IDUNICO                    );
        clsJs.add("cntp_idsrv"    , chkds[0].IDSRV                      );
        clsJs.add("cntp_codgmp"   , parseInt(arr[0].CODIGO)             );  //Vem do retorno de F10
        clsJs.add("cntp_acao"     , 1                                   );  //Ver trigger para valor de acao
        //////////////////////
        // Enviando para o Php
        //////////////////////    
        var fd = new FormData();
        fd.append("contratoproduto" , clsJs.fim());
        msg=requestPedido("Trac_ContratoProduto.php",fd); 

        retPhp=JSON.parse(msg);
        if( retPhp[0].retorno != "OK" ){
          gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
        } else {  
          /////////////////////////////////////////////////
          // Atualizando a grade deste formulario
          /////////////////////////////////////////////////
          tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
            if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(chkds[0].IDUNICO).inteiro().ret() ){
              row.cells[objCol.EMPENHO].innerHTML     = jsDatas(0).retDDMMYYYY();
              row.cells[objCol.SERIE].innerHTML       = arr[0].SERIE;              
              row.cells[objCol.CFG].innerHTML         = arr[0].CFG;              
              row.cells[objCol.COLABORADOR].innerHTML = 'TRAC';              
              row.cells[objCol.CODGMP].innerHTML      = arr[0].CODIGO;
              if( arr[0].CFG=="SIM" )
                row.cells[objCol.CFG].classList.remove("corFonteAlterado");  
              row.cells[objCol.PE].innerHTML="EMP";              
            };
          }); 
          atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                    ,pega.codCntt       // codigo do contrato
                                    ,pega.CODIGO        // coluna da tabela de contrato
                                    ,pega.EMPENHO       // coluna a ser atualizada
                                    ,1                  // total a ser atualizado
          );                          
          tblCntI.retiraChecked()
        };  
      };
      ///////////////////////////////
      // Retirar empenho
      ///////////////////////////////
      function horEmpenhoExcClick(){
        try{
          chkds=objCntI.gerarJson("1").gerar();
          msg         = "ok";
          clsJs       = jsString("lote");
          if( coalesce(chkds[0].EMPENHO,"") == "" )
            throw "AUTO SEM EMPENHO!";
          if( coalesce(chkds[0].PLACA,"") != "" )
            throw "AUTO COM PLACA!";
          if( coalesce(chkds[0].ATIVADO,"") != "" )
            throw "AUTO ATIVADO!";
          if( coalesce(chkds[0].SE,"") == "ENT" )
            throw "AUTO COM STATUS DE ENTREGUE!";

          /*
          if( (chkds[0].ENTREGA).length > 0 )
            throw "AUTO COM DATA DE ENTREGA!";  
          if( (chkds[0].INSTALA).length > 0 )
            throw "AUTO COM DATA DE INSTALACAO!";              
          if( (chkds[0].AGENDA).length > 0 )
            throw "AUTO COM AGENDA!";              
          */
          if( (coalesce(chkds[0].ENTREGA,"") != "") || (coalesce(chkds[0].INSTALA,"") != "") || (coalesce(chkds[0].AGENDA,"") != "") )
            throw "AUTO COM AGENDA DE INSTALAÇÃO!";
          ///////////////////////////////////////////////////////////////////////////
          // Classe para montar envio para o Php
          // Colocando o CODGMP em todos os itens devido relacionamento EMPENHO/SERIE
          ///////////////////////////////////////////////////////////////////////////    
          clsJs = jsString("lote");        
          clsJs.add("rotina"        , "empenhoexc"                        );              
          clsJs.add("login"         , jsPub[0].usr_login                  );
          clsJs.add("cntp_codcntt"  , chkds[0].CONTRATO                   );
          clsJs.add("cntp_idunico"  , chkds[0].IDUNICO                    );
          clsJs.add("cntp_idsrv"    , chkds[0].IDSRV                      );
          clsJs.add("cntp_codgmp"   , chkds[0].CODGMP                     );
          clsJs.add("cntp_acao"     , -1                                  );  //Ver trigger para valor de acao
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("contratoproduto" , clsJs.fim());
          msg=requestPedido("Trac_ContratoProduto.php",fd); 

          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
          } else {  
            /////////////////////////////////////////////////
            // Atualizando a grade deste formulario
            /////////////////////////////////////////////////
            tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(chkds[0].IDUNICO).inteiro().ret() ){
                row.cells[objCol.EMPENHO].innerHTML     = "";
                row.cells[objCol.SERIE].innerHTML       = "";
                row.cells[objCol.COLABORADOR].innerHTML = "";
                row.cells[objCol.PE].innerHTML          = "EST";                              
              };
            }); 
            atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                      ,pega.codCntt       // codigo do contrato
                                      ,pega.CODIGO        // coluna da tabela de contrato
                                      ,pega.EMPENHO       // coluna a ser atualizada
                                      ,-1                 // total a ser atualizado
            );                          
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////
      // Informar placa
      /////////////////
      function horPlacaCadClick(){
        try{
          chkds=objCntI.gerarJson("1").gerar();
          msg         = "ok";
          clsJs       = jsString("lote");

          if( chkds[0].EMPENHO == "" )
            throw "AUTO SEM EMPENHO!";
          if( jsConverte(chkds[0].OS).inteiro()>0 )
            throw "PLACA DEVE SER INFORMADA ATRAVÉS DA OS "+chkds[0].OS;
          
          fPlacaF10(0,"nsa","null",100
            ,{codcntt: pega.codCntt
              ,divWidth:"66em"
              ,tblWidth:"64em"
              ,where:" WHERE ((A.CNTP_CODCNTT="+pega.codCntt+") AND (VCL.VCL_CODCNTT=0))"
          }); 
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function RetF10tblPlc(arr){
        ///////////////////////////////////////////////////////////////////////////
        // Classe para montar envio para o Php
        // Colocando o CODGMP em todos os itens devido relacionamento EMPENHO/SERIE
        ///////////////////////////////////////////////////////////////////////////  
        clsJs = jsString("lote");        
        clsJs.add("rotina"            , "placacad"                          );              
        clsJs.add("login"             , jsPub[0].usr_login                  );
        clsJs.add("cntp_placachassi"  , arr[0].PLACA                        );        
        clsJs.add("cntp_codcntt"      , chkds[0].CONTRATO                   );
        clsJs.add("cntp_codgmp"       , chkds[0].CODGMP                     );  //Vem do retorno de F10
        clsJs.add("cntp_acao"         , 3                                   );  //Ver trigger para valor de acao
        //////////////////////
        // Enviando para o Php
        //////////////////////    
        var fd = new FormData();
        fd.append("contratoproduto" , clsJs.fim());
        msg=requestPedido("Trac_ContratoProduto.php",fd); 

        retPhp=JSON.parse(msg);
        if( retPhp[0].retorno != "OK" ){
          gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
        } else {  
          /////////////////////////////////////////////////
          // Atualizando a grade deste formulario
          /////////////////////////////////////////////////
          tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
            if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(chkds[0].IDUNICO).inteiro().ret() ){
              row.cells[objCol.PLACA_CHASSI].innerHTML=arr[0].PLACA;              
            };
          }); 
          atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                    ,pega.codCntt       // codigo do contrato
                                    ,pega.CODIGO        // coluna da tabela de contrato
                                    ,pega.PLACAS        // coluna a ser atualizada
                                    ,1                  // total a ser atualizado
          );                          
          tblCntI.retiraChecked()
        };  
      };
      /////////////////
      // Retirar placa
      /////////////////
      function horPlacaExcClick(){
        try{
          chkds = objCntI.gerarJson("1").gerar();
          clsJs = jsString("lote");

          if( chkds[0].PLACA_CHASSI == "" )
            throw "AUTO SEM PLACA!";
          if( chkds[0].ATIVADO != "" )
            throw "AUTO ATIVADO!";
          

          clsJs = jsString("lote");        
          clsJs.add("rotina"            , "placaexc"                          );              
          clsJs.add("login"             , jsPub[0].usr_login                  );
          clsJs.add("cntp_placachassi"  , chkds[0].PLACA_CHASSI               );  // Passando a placa para pode usar no trigger        
          clsJs.add("cntp_codcntt"      , chkds[0].CONTRATO                   );
          clsJs.add("cntp_codgmp"       , chkds[0].CODGMP                     );
          clsJs.add("cntp_acao"         , -3                                  );
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("contratoproduto" , clsJs.fim());
          msg=requestPedido("Trac_ContratoProduto.php",fd); 

          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
          } else {  
            /////////////////////////////////////////////////
            // Atualizando a grade deste formulario
            /////////////////////////////////////////////////
            tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(chkds[0].IDUNICO).inteiro().ret() ){
                row.cells[objCol.PLACA_CHASSI].innerHTML="";              
              };
            }); 
            atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                      ,pega.codCntt       // codigo do contrato
                                      ,pega.CODIGO        // coluna da tabela de contrato
                                      ,pega.PLACAS        // coluna a ser atualizada
                                      ,-1                 // total a ser atualizado
            );                          
            tblCntI.retiraChecked()
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////
      // Agendar
      /////////////////
      function horAgendaClick(){
        ///////////////////////////////////////
        // Aqui pode ser agendamento ou nova OS
        ///////////////////////////////////////
        let rotina=pega.qualRotina;
        //
        try{        
          ///////////////////////////////////////////////////////////////////
          // Checagem basica, qdo gravar checo novamente validando as colunas
          ///////////////////////////////////////////////////////////////////
          chkds=objCntI.gerarJson("n").gerar();
          clsJs       = jsString("lote");
          chkds.forEach(function(reg){
            if( reg.EMPENHO == "" )
              throw "AUTO SEM EMPENHO NÃO ACEITA AGENDAMENTO!";
            if( reg.SE != "ENT" )
              throw "AUTO DEVE ESTA COM STATUS DE ENTREGUE PARA AGENDAMENTO!";
            if( reg.RE == "" )
              throw "OBRIGATORIO INFORMAR UMA PREVISÃO DE ENTREGA!";
            if( (rotina=="OS") && (jsConverte(reg.PLACA_CHASSI).coalesce("")=="") )
              throw "PARA NOVA OS OBRIGATORIO PLACA!";
          });
          $doc("agendacad").style.display="block";
          $doc("edtCodEnt").value = "0000";
          $doc("edtDesEnt").value = "";          
          $doc("edtCddEnt").value = "";                    
          $doc("edtCepEnt").value = "";                    
          $doc("edtCodIns").value = "0000";          
          $doc("edtDesIns").value = "";          
          $doc("edtCddIns").value = "";                    
          $doc("edtCepIns").value = "";              
          $doc("ageCodCol").value = "0000";                    
          $doc("ageDesCol").value = "";                    
          $doc("ageApeCol").value = "";
          if( rotina=="AGENDA" ){
            $doc("divReferente").style.display="none";
            $doc("divLocal").style.display="none";            
            $doc("divTitulo").innerHTML="Agendamento";
          };  
          if( rotina=="OS" ){
            $doc("cbCodMsg").value="19";
            $doc("divTitulo").innerHTML="Nova OS";
          };  
          $doc("edtCodEnt").foco();
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ////////////////////////
      // Confirmar agendamento
      ////////////////////////
      function ageConfirmarClick(){
        ///////////////////////////////////////
        // Aqui pode ser agendamento ou nova OS
        ///////////////////////////////////////
        let rotina=pega.qualRotina;
        let qtasOs=0;
        //
        try{
          ///////////////////////////////////////////////////////////////////////////
          // Classe para montar envio para o Php
          // Colocando o CODGMP em todos os itens devido relacionamento EMPENHO/SERIE
          ///////////////////////////////////////////////////////////////////////////    
          clsJs = jsString("lote");                
          chkds.forEach(function(reg){
            clsJs.add("rotina"          , (rotina=="AGENDA" ? "agenda" : "cados")               );              
            clsJs.add("login"           , jsPub[0].usr_login                                    );
            clsJs.add("acao"            , 2                                                     );
            clsJs.add("cntp_codgmp"     , parseInt(reg.CODGMP)                                  );          
            clsJs.add("cntp_idunico"    , parseInt(reg.IDUNICO)                                 );                    
            clsJs.add("cntp_codcntt"    , parseInt(reg.CONTRATO)                                );           
            clsJs.add("cntp_codentrega" , jsNmrs("edtCodEnt").inteiro().ret()                   );             
            clsJs.add("cntp_codinstala" , jsNmrs("edtCodIns").inteiro().ret()                   );             
            clsJs.add("cntp_codpei"     , jsNmrs("ageCodCol").inteiro().ret()                   );  //Colaborador(com quem esta o auto)                         
            clsJs.add("cntp_dtagenda"   , jsDatas("edtData").retMMDDYYYY()                      ); 
            clsJs.add("cntp_codmsg"     , parseInt($doc("cbCodMsg").value)                      );
            clsJs.add("cntp_local"      , $doc("cbLocal").value                                 );            
            clsJs.add("cntp_codvcl"     , (reg.PLACA_CHASSI=="" ? "NSA0000" : reg.PLACA_CHASSI) );            
            qtasOs++;
          });
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("contratoproduto" , clsJs.fim());
          msg=requestPedido("Trac_ContratoProduto.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
          } else {  
            if( rotina=="AGENDA" ){
              /////////////////////////////////////////////////
              // Atualizando a grade deste formulario
              /////////////////////////////////////////////////
              let oldAgendaCompleta = "N";
              let newAgendaCompleta = "N";
              let qtosAgendados     = 0;
              tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                chkds.forEach(function(reg){
                  if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(reg.IDUNICO).inteiro().ret() ){ 
                    //////////////////////////////////////////////////////////////////
                    // Para o agendamento ser completo deve ter os 4 campos intormados
                    // Olhando aqui se estava completo antes de atualizar
                    //////////////////////////////////////////////////////////////////
                    if( (row.cells[objCol.ENTREGA].innerHTML !="" ) && (row.cells[objCol.INSTALA].innerHTML !="" ) && (row.cells[objCol.AGENDA].innerHTML != "") 
                                                                    && (row.cells[objCol.COLABORADOR].innerHTML != "") ){
                      oldAgendaCompleta="S";
                    };
                    //////////////////////
                    // Atualizando a grade
                    //////////////////////
                    row.cells[objCol.ENTREGA].innerHTML     = $doc("edtCepEnt").value;
                    row.cells[objCol.INSTALA].innerHTML     = $doc("edtCepIns").value;
                    row.cells[objCol.AGENDA].innerHTML    = ( $doc("edtData").value=="00/00/0000" ? "" : $doc("edtData").value );
                    row.cells[objCol.COLABORADOR].innerHTML = $doc("ageApeCol").value;
                    //////////////////////////////////////////////////////////////////
                    // Para o agendamento ser completo deve ter os 4 campos intormados
                    // Olhando aqui como ficou depois da atualizacao
                    //////////////////////////////////////////////////////////////////
                    if( (row.cells[objCol.ENTREGA].innerHTML !="" ) && (row.cells[objCol.INSTALA].innerHTML !="" ) && (row.cells[objCol.AGENDA].innerHTML != "") 
                                                                    && (row.cells[objCol.COLABORADOR].innerHTML != "") ){
                      newAgendaCompleta="S";
                    };
                    ///////////////////////////////////////////////////////////////////////
                    // Olhando aqui se vou incrementar o contador no formulario de contrato
                    ///////////////////////////////////////////////////////////////////////
                    if( newAgendaCompleta != oldAgendaCompleta ){
                      if( (newAgendaCompleta=="S") && (oldAgendaCompleta=="N") ){
                        qtosAgendados++;
                      };  
                      if( (newAgendaCompleta=="N") && (oldAgendaCompleta=="S") ){
                        qtosAgendados--;
                      };  
                    };  
                  };
                });  
              });
              tblCntI.retiraChecked()
              atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                        ,pega.codCntt       // codigo do contrato
                                        ,pega.CODIGO        // coluna da tabela de contrato
                                        ,pega.AGENDA        // coluna a ser atualizada
                                        ,qtosAgendados      // total a ser atualizado
              );                          
            };
            //////////////////
            // Se for gerar OS
            //////////////////
            if( rotina=="OS" ){
              tblCntI.retiraChecked();
              atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                        ,pega.codCntt   // codigo do contrato
                                        ,pega.CODIGO    // coluna da tabela de contrato
                                        ,pega.OS        // coluna a ser atualizada
                                        ,qtasOs         // total a ser atualizado
              );                          
            };  
            $doc('agendacad').style.display='none';                        
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };  
      /////////////////////////////////////////////
      // Composicao do auto
      /////////////////////////////////////////////
      function horComposicaoClick(codamp){
        if( jsNmrs(codamp).inteiro().ret()>0 ){
          try{          
            clsJs   = jsString("lote");  
            clsJs.add("rotina"  , "hlpComposicao"     );
            clsJs.add("login"   , jsPub[0].usr_login  );
            clsJs.add("codamp"  , codamp              );
            fd = new FormData();
            fd.append("automodeloind" , clsJs.fim());
            msg     = requestPedido("Trac_AutoModeloInd.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno == "OK" ){
              janelaDialogo(
                { height          : "36em"
                  ,body           : "16em"
                  ,left           : "300px"
                  ,top            : "60px"
                  ,tituloBarra    : "Composicao do auto "+codamp
                  ,code           : retPhp[0]["dados"]
                  ,width          : "80em"
                  ,fontSizeTitulo : "1.8em"
                }
              );  
            };  
          }catch(e){
            gerarMensagemErro('catch',e.message,{cabec:"Erro"});
          };
        };
      };
      ///////////////////////////////
      // Colaborador para agenda
      ///////////////////////////////
      function colF10Click(obj){
        try{
          if( parseInt($doc("edtCodIns").value)<=0 )
            throw "FAVOR INFORMAR UM CODIGO DE INSTALACAO!";  
          fColaboradorF10(0,"nsa","null",100
            ,{codins:parseInt($doc("edtCodIns").value)
              ,divWidth:"76em"
              ,tblWidth:"74em"
          }); 
          
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function RetF10tblCol(arr){
        $doc("ageCodCol").value      = arr[0].CODIGO;
        $doc("ageDesCol").value      = arr[0].COLABORADOR;
        $doc("ageApeCol").value      = arr[0].APELIDO;
        $doc("ageCodCol").setAttribute("data-oldvalue",arr[0].CODIGO);
        
        if( pega.qualRotina=="OS" )
          $doc("cbCodMsg").focus();        
      };
      function codColBlur(obj){
        var elOld = jsNmrs($doc(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fColaboradorF10(1,"nsa","null",100
            ,{codins:parseInt($doc("edtCodIns").value)
              ,where:" AND PEI_CODFVR="+elNew 
              ,divWidth:"76em"
              ,tblWidth:"74em"
          }); 
          $doc("ageCodCol").value      = ( arr.length == 0 ? "0000"            : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          $doc("ageDesCol").value      = ( arr.length == 0 ? ""                : arr[0].COLABORADOR );
          $doc("ageApeCol").value      = ( arr.length == 0 ? ""                : arr[0].APELIDO );
          $doc("ageCodCol").setAttribute("data-oldvalue",$doc("ageCodCol").value);
        };
      };
      ///////////////////////////////
      // Modo de entrega
      ///////////////////////////////
      function horModoEntregaClick(){
        try{
          $doc("edtModoEntrega").value="*";  
          chkds=objCntI.gerarJson("n").gerar();
          ///////////////////
          // Simples checagem
          ///////////////////
          chkds.forEach(function(reg){
            if( reg.EMPENHO == "" )
              throw "AUTO SEM EMPENHO NÃO ACEITA AGENDAMENTO!";
          });
          ///////////////////
          // Montando a grade    
          ///////////////////
          let clsCode = new concatStr();  
          clsCode.concat("<div id='dPaiChk' class='divContainerTable' style='height: 12.2em; width: 41em;border:none'>");
          clsCode.concat("<table id='tblChk' class='fpTable' style='width:100%;'>");
          clsCode.concat("  <thead class='fpThead'>");
          clsCode.concat("    <tr>");
          clsCode.concat("      <th class='fpTh' style='width:20%'>CODIGO</th>");
          clsCode.concat("      <th class='fpTh' style='width:60%'>DESCRICAO</th>");
          clsCode.concat("      <th class='fpTh' style='width:20%'>SIM</th>");        
          clsCode.concat("    </tr>");
          clsCode.concat("  </thead>");
          clsCode.concat("  <tbody id='tbody_tblChk'>");
          let arr=[];
          arr.push({cod:"COR",des:"CORREIO"        ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
          arr.push({cod:"TRA",des:"TRANSPORTADORA" ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
          arr.push({cod:"MAO",des:"EM MÃOS"        ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});          
          arr.forEach(function(reg){
            clsCode.concat("    <tr class='fpBodyTr'>");
            clsCode.concat("      <td class='fpTd textoCentro'>"+reg.cod+"</td>");
            clsCode.concat("      <td class='fpTd'>"+reg.des+"</td>");
            clsCode.concat("      <td class='fpTd textoCentro'>");
            clsCode.concat("        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheckModo((elTr.rowIndex-1));'>");
            clsCode.concat("          <i id='img"+reg.cod+"' data-value='"+reg.sn+"' class='"+reg.fa+"' style='margin-left:10px;font-size:1.5em;color:"+reg.cor+";'></i>");
            clsCode.concat("        </div>");
            clsCode.concat("      </td>");
            clsCode.concat("    </tr>");
          });
          //////  
          // Fim
          //////
          clsCode.concat("  </tbody>");        
          clsCode.concat("</table>");
          clsCode.concat("</div>"); 
          clsCode.concat("<div id='btnConfirmar' onClick='fncModoEntregaRet();' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'> Ok</i></div>");        
          janelaDialogo(
            { height          : "23em"
              ,body           : "16em"
              ,left           : "500px"
              ,top            : "60px"
              ,tituloBarra    : "Modo de entrega"
              ,code           : clsCode.fim()
              ,width          : "43em"
              ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ///////////////////////////////////////////
      // Marcando e desmarcando os itens da table
      ///////////////////////////////////////////
      function fncCheckModo(pLin){
        let elImg;
        tblChk.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {  
          elImg = "img"+row.cells[0].innerHTML;
          if( indexTr==pLin ){
            jsCmpAtivo(elImg).remove("fa-thumbs-o-down").add("fa-thumbs-o-up").cor("blue");
            $doc("edtModoEntrega").value=row.cells[0].innerHTML;
          } else {
            jsCmpAtivo(elImg).remove("fa-thumbs-o-up").add("fa-thumbs-o-down").cor("red");
          };
        });
      };
      ///////////////////////////////////////
      // Atualizando o banco de dados e grade
      ///////////////////////////////////////
      function fncModoEntregaRet(){
        try{  
          if( ["COR","MAO","TRA"].indexOf($doc("edtModoEntrega").value) == -1 )
            throw "MODALIDADE DE ENTREGA "+$doc("edtModoEntrega").value+" INVALIDA!";            
        
          clsJs = jsString("lote");                
          chkds.forEach(function(reg){
            clsJs.add("rotina"          , "modoentrega"                 );              
            clsJs.add("login"           , jsPub[0].usr_login            );
            clsJs.add("acao"            , 4                             );            
            clsJs.add("cntp_codgmp"     , parseInt(reg.CODGMP)          );          
            clsJs.add("cntp_idunico"    , parseInt(reg.IDUNICO)         );                    
            clsJs.add("cntp_codcntt"    , parseInt(reg.CONTRATO)        );           
            clsJs.add("cntp_modoentrega", $doc("edtModoEntrega").value  );                       
          });
          fd.append("contratoproduto" , clsJs.fim());
          msg     = requestPedido("Trac_ContratoProduto.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});  
          } else { 
            ///////////////////////////////////////////////////
            // StatusEntrega para atualizar a grade de contrato
            ///////////////////////////////////////////////////
            let oldStatusEntrega;
            let newStatusEntrega;
            let qtsStatusEntrega=0;
            //
            //
            tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              chkds.forEach(function(reg){    
                if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(reg.IDUNICO).inteiro().ret() ){
                  if( row.cells[objCol.ME].innerHTML != $doc("edtModoEntrega").value ){
                    oldStatusEntrega=row.cells[objCol.SE].innerHTML;
                    row.cells[objCol.ME].innerHTML=$doc("edtModoEntrega").value;
                    switch($doc("edtModoEntrega").value){
                      case "COR":
                        row.cells[objCol.ME].classList.remove("corFonteAlterado");
                        row.cells[objCol.ME].classList.remove("corVerde");
                        row.cells[objCol.ME].classList.add("corAzul");  
                        row.cells[objCol.SE].innerHTML="AGU";  
                        row.cells[objCol.SE].classList.add("corFonteAlterado");  
                        break;
                      case "MAO":
                        row.cells[objCol.ME].classList.remove("corFonteAlterado");
                        row.cells[objCol.ME].classList.remove("corAzul");
                        row.cells[objCol.ME].classList.add("corAlterado");  
                        row.cells[objCol.SE].innerHTML="ENT";  
                        row.cells[objCol.SE].classList.remove("corFonteAlterado");  
                        break;
                      case "TRA":
                        row.cells[objCol.ME].classList.remove("corAzul");
                        row.cells[objCol.ME].classList.remove("corFonteAlterado");
                        row.cells[objCol.ME].classList.add("corVerde");
                        row.cells[objCol.SE].innerHTML="AGU";  
                        row.cells[objCol.SE].classList.add("corFonteAlterado");  
                        break;
                    };  
                    newStatusEntrega=row.cells[objCol.SE].innerHTML;
                    if( (oldStatusEntrega=="ENT") && (newStatusEntrega!="ENT") )
                      qtsStatusEntrega-=1;
                    if( (oldStatusEntrega!="ENT") && (newStatusEntrega=="ENT") )
                      qtsStatusEntrega+=1;
                    return false;    
                  };
                }; 
              });
            });    
            atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                      ,pega.codCntt       // codigo do contrato
                                      ,pega.CODIGO        // coluna da tabela de contrato
                                      ,pega.ENV           // coluna a ser atualizada
                                      ,qtsStatusEntrega   // total a ser atualizado
            );                          
            janelaFechar();
            tblCntI.retiraChecked();
          }; 
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ///////////////////////////////
      // Status de entrega
      ///////////////////////////////
      function horStatusEntregaClick(){
        try{
          $doc("edtStatusEntrega").value="*";  
          chkds=objCntI.gerarJson("n").gerar();
          ///////////////////
          // Simples checagem
          ///////////////////
          chkds.forEach(function(reg){
            if( reg.EMPENHO == "" )
              throw "AUTO SEM EMPENHO NÃO ACEITA AGENDAMENTO!";
          });
          ////////////////////////////////////////////////
          // Olhando aqui o modo de entrega
          // Se for correio tem que ser despachado
          // Se for transportadora tem que ser em transito
          ////////////////////////////////////////////////
          let qtosCor=0;
          let qtosMao=0;
          let qtosTra=0;
          chkds.forEach(function(reg){
            switch( reg.ME ){
              case "COR": qtosCor=1;break;
              case "MAO": qtosMao=1;break;
              case "TRA": qtosTra=1;break;
            };  
          });
          //if( qtosMao>0 )
          //  throw "OPÇÃO MODE DE ENTREGA em mãos INVALIDA PARA STATUS DA ENTREGA";
          if( (qtosCor+qtosTra+qtosMao) != 1 )
            throw "SELECIONAR APENAS UM MODO DE ENTREGA";
          //
          //
          let clsCode = new concatStr();  
          clsCode.concat("<div id='dPaiChk' class='divContainerTable' style='height: 15.2em; width: 41em;border:none'>");
          clsCode.concat("<table id='tblChk' class='fpTable' style='width:100%;'>");
          clsCode.concat("  <thead class='fpThead'>");
          clsCode.concat("    <tr>");
          clsCode.concat("      <th class='fpTh' style='width:20%'>CODIGO</th>");
          clsCode.concat("      <th class='fpTh' style='width:60%'>DESCRICAO</th>");
          clsCode.concat("      <th class='fpTh' style='width:20%'>SIM</th>");        
          clsCode.concat("    </tr>");
          clsCode.concat("  </thead>");
          clsCode.concat("  <tbody id='tbody_tblChk'>");
          let arr=[];
          arr.push({cod:"AGU",des:"AGUARDANDO" ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
          if( qtosCor>0 )
            arr.push({cod:"DES",des:"DESPACHADO" ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
          if( qtosTra>0 )          
            arr.push({cod:"TRA",des:"TRANSITO"   ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});          
          arr.push({cod:"ENT",des:"ENTREGUE"   ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});                    
          arr.forEach(function(reg){
            clsCode.concat("    <tr class='fpBodyTr'>");
            clsCode.concat("      <td class='fpTd textoCentro'>"+reg.cod+"</td>");
            clsCode.concat("      <td class='fpTd'>"+reg.des+"</td>");
            clsCode.concat("      <td class='fpTd textoCentro'>");
            clsCode.concat("        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheckStatus((elTr.rowIndex-1));'>");
            clsCode.concat("          <i id='img"+reg.cod+"' data-value='"+reg.sn+"' class='"+reg.fa+"' style='margin-left:10px;font-size:1.5em;color:"+reg.cor+";'></i>");
            clsCode.concat("        </div>");
            clsCode.concat("      </td>");
            clsCode.concat("    </tr>");
          });
          //////  
          // Fim
          //////
          clsCode.concat("  </tbody>");        
          clsCode.concat("</table>");
          clsCode.concat("</div>"); 
          clsCode.concat("<div id='btnConfirmar' onClick='fncStatusEntregaRet();' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'> Ok</i></div>");        
          janelaDialogo(
            { height          : "26em"
              ,body           : "16em"
              ,left           : "500px"
              ,top            : "60px"
              ,tituloBarra    : "Status da entrega"
              ,code           : clsCode.fim()
              ,width          : "43em"
              ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ///////////////////////////////////////////
      // Marcando e desmarcando os itens da table
      ///////////////////////////////////////////
      function fncCheckStatus(pLin){
        let elImg;
        tblChk.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {  
          elImg = "img"+row.cells[0].innerHTML;
          if( indexTr==pLin ){
            jsCmpAtivo(elImg).remove("fa-thumbs-o-down").add("fa-thumbs-o-up").cor("blue");
            $doc("edtStatusEntrega").value=row.cells[0].innerHTML;
          } else {
            jsCmpAtivo(elImg).remove("fa-thumbs-o-up").add("fa-thumbs-o-down").cor("red");
          };
        });
      };
      ///////////////////////////////////////
      // Atualizando o banco de dados e grade
      ///////////////////////////////////////
      function fncStatusEntregaRet(){
        try{  
          if( ["AGU","DES","ENT","TRA"].indexOf($doc("edtStatusEntrega").value) == -1 )
            throw "STATUS DA ENTREGA "+$doc("edtStatusEntrega").value+" INVALIDA!";            
        
          clsJs = jsString("lote");                
          chkds.forEach(function(reg){
            clsJs.add("rotina"              , "statusentrega"                     );              
            clsJs.add("login"               , jsPub[0].usr_login                  );
            clsJs.add("acao"                , 5                                   );
            clsJs.add("cntp_codgmp"         , parseInt(reg.CODGMP)                );          
            clsJs.add("cntp_idunico"        , parseInt(reg.IDUNICO)               );                    
            clsJs.add("cntp_codcntt"        , parseInt(reg.CONTRATO)              );           
            clsJs.add("cntp_statusentrega"  , $doc("edtStatusEntrega").value      );                       
          });
          fd.append("contratoproduto" , clsJs.fim());
          msg     = requestPedido("Trac_ContratoProduto.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});  
          } else {  
            ///////////////////////////////////////////////////
            // StatusEntrega para atualizar a grade de contrato
            ///////////////////////////////////////////////////
            let oldStatusEntrega;
            let newStatusEntrega;
            let qtsStatusEntrega=0;
            //
            //
            tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              chkds.forEach(function(reg){    
                if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(reg.IDUNICO).inteiro().ret() ){
                  oldStatusEntrega=row.cells[objCol.SE].innerHTML;
                  row.cells[objCol.SE].innerHTML=$doc("edtStatusEntrega").value;
                  if( $doc("edtStatusEntrega").value=="ENT" ){
                    row.cells[objCol.SE].classList.remove("corFonteAlterado");
                  } else {
                    row.cells[objCol.SE].classList.add("corFonteAlterado");  
                  };  
                  newStatusEntrega=row.cells[objCol.SE].innerHTML;
                  if( (oldStatusEntrega=="ENT") && (newStatusEntrega!="ENT") )
                    qtsStatusEntrega-=1;
                  if( (oldStatusEntrega!="ENT") && (newStatusEntrega=="ENT") )
                    qtsStatusEntrega+=1;
                  return false;    
                }; 
              });
            });    
            atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                      ,pega.codCntt       // codigo do contrato
                                      ,pega.CODIGO        // coluna da tabela de contrato
                                      ,pega.ENV           // coluna a ser atualizada
                                      ,qtsStatusEntrega   // total a ser atualizado
            );                          
            janelaFechar();
            tblCntI.retiraChecked();
          }; 
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      function fncCorreio(){
        try{
          if( $doc("edtCodRastreio").value=="" )
            throw "CODIGO DE RASTREIO INVALIDO!"; 

          msg = new clsMensagem("Erro"); 
          msg.dataValida("vencto", $doc("edtDtEntrega").value );
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            chkds=objCntI.gerarJson("n").gerar();
            clsJs = jsString("lote");                
            chkds.forEach(function(reg){
              //////////////////////////
              // Trigger olha este campo
              //////////////////////////
              //if( reg.ME == "MAO" )
              //  throw "ACEITO APENAS PARA A MODALIDADE DE ENTREGA CORREIO/TRANSPORTADORA!";                
              //
              //    
              clsJs.add("rotina"          , "codcorreio"                          );             
              clsJs.add("login"           , jsPub[0].usr_login                    );
              clsJs.add("cntp_codgmp"     , parseInt(reg.CODGMP)                  );
              clsJs.add("cntp_idunico"    , parseInt(reg.IDUNICO)                 );
              clsJs.add("cntp_codcntt"    , parseInt(reg.CONTRATO)                );
              clsJs.add("cntp_codrastreio", $doc("edtCodRastreio").value          );
              clsJs.add("cntp_dtentrega"  , jsDatas("edtDtEntrega").retMMDDYYYY() );
            });
            fd.append("contratoproduto" , clsJs.fim());
            msg     = requestPedido("Trac_ContratoProduto.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});  
            } else {  
              tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                chkds.forEach(function(reg){
                  if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(reg.IDUNICO).inteiro().ret() ){
                    row.cells[objCol.RE].innerHTML='ok';
                    return false;
                  };
                });    
              });    
              ppvCorreio.hide();
              $doc("edtCodRastreio").value="";
              tblCntI.retiraChecked()
            }; 
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      
      function fncAtiva(){
        try{
          msg = new clsMensagem("Erro"); 
          msg.dataValida("Data", $doc("edtDtAtiva").value );
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            chkds=objCntI.gerarJson("1").gerar();
            clsJs = jsString("lote");                
            clsJs.add("rotina"          , "ativa"                               );             
            clsJs.add("login"           , jsPub[0].usr_login                    );
            clsJs.add("cntp_acao"       , 6                                     );              
            clsJs.add("cntp_codgmp"     , parseInt(chkds[0].CODGMP)             );
            clsJs.add("cntp_idunico"    , parseInt(chkds[0].IDUNICO)            );
            clsJs.add("cntp_codcntt"    , parseInt(chkds[0].CONTRATO)           );
            clsJs.add("cntp_dtativacao" , jsDatas("edtDtAtiva").retMMDDYYYY()   );
            clsJs.add("cntp_localinstalacao", $doc("edtLocalInsta").value       );
            fd.append("contratoproduto" , clsJs.fim());
            msg     = requestPedido("Trac_ContratoProduto.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});  
            } else {  
              tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(chkds[0].IDUNICO).inteiro().ret() ){
                  console.log(row.cells);
                  row.cells[objCol.ATIVADO].innerHTML=$doc("edtDtAtiva").value;
                  return false;
                };
              });    
              let tbl=window.opener.document.getElementById("tblCntt");
              tbl.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                
                if( parseInt(pega.codCntt)==jsNmrs(row.cells[pega.CODIGO].innerHTML).inteiro().ret() ){
                  if( row.cells[pega.INICIO].innerHTML=="" )
                    row.cells[pega.INICIO].innerHTML=$doc("edtDtAtiva").value;
                  row.cells[pega.ATIVACAO].innerHTML=( jsNmrs(row.cells[pega.ATIVACAO].innerHTML).inteiro().ret() + 1 );
                };
              });
              ppvAtiva.hide();
              tblCntI.retiraChecked()
            }; 
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ///////////
      // Gerar OS
      ///////////  
      function horGerarOsClick(){
        
        try{
          ///////////////////////////////////////////////////////////////////////////
          // Classe para montar envio para o Php
          // Colocando o CODGMP em todos os itens devido relacionamento EMPENHO/SERIE
          ///////////////////////////////////////////////////////////////////////////    
          chkds=objCntI.gerarJson("n").gerar();          
          // Checagem basica
          chkds.forEach(function(reg){
            if( parseInt(reg.OS) > 0 )
              throw "AUTO JA POSSUI OS DE INSTALAÇÃO";
            if( reg.AGENDA=="" )
              throw "AUTO SEM DATA DE AGENDAMENTO";
            
            if( pega.IP=="S" )
              throw "CONTRATO CADASTRADO COMO INSTALAÇÃO PRÓPRIA";  
          });  
          clsJs = jsString("lote");                
          chkds.forEach(function(reg){
            clsJs.add("rotina"          , "geraros"                           );              
            clsJs.add("login"           , jsPub[0].usr_login                  );
            clsJs.add("cntp_acao"       , 7                                   );
            clsJs.add("cntp_codgmp"     , parseInt(reg.CODGMP)                );          
            clsJs.add("cntp_idunico"    , parseInt(reg.IDUNICO)               );                    
            clsJs.add("cntp_codcntt"    , parseInt(reg.CONTRATO)              );           
          });
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("contratoproduto" , clsJs.fim());
          msg=requestPedido("Trac_ContratoProduto.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
          } else {  
            /////////////////////////////////////////////////
            // Atualizando a grade deste formulario
            /////////////////////////////////////////////////
            let qtasOs=0;
            tblCntI.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              chkds.forEach(function(reg){
                if( jsNmrs(row.cells[objCol.IDUNICO].innerHTML).inteiro().ret()  == jsNmrs(reg.IDUNICO).inteiro().ret() ){ 
                  row.cells[objCol.OS].innerHTML = jsConverte(row.cells[objCol.CODGMP].innerHTML).emZero(6);
                  qtasOs++;
                };
              });  
            });
            tblCntI.retiraChecked();
            atualizaGradeContrato.call(window.opener.document.getElementById("tblCntt")
                                      ,pega.codCntt   // codigo do contrato
                                      ,pega.CODIGO    // coluna da tabela de contrato
                                      ,pega.OS        // coluna a ser atualizada
                                      ,qtasOs         // total a ser atualizado
            );                          
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };  
      function horFecharClick(){
        window.close();
      };

    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span id="spnTitulo" class="infoBoxText">Empenho</span>
              <span id="spnCodCntt" class="infoBoxNumber"></span>
            </div>
          </div>
        </div>        

        <section id="collapseCorreio" class="section-combo" data-tamanho="230" style="display:none;">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Rastreamento/Entrega</a>
              <button type="button" id="popoverCorreio" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group'>
                                        <label for='edtCodRastreio' class='control-label'>Codigo rastreio</label>
                                        <input type='text' class='form-control' id='edtCodRastreio' placeholder='informe' />
                                      </div>
                                      <div class='form-group'>
                                        <label for='edtDtEntrega' class='control-label'>Previsão entrega</label>
                                        <input type='text' class='form-control' id='edtDtEntrega' placeholder='##/##/####' onkeyup=mascaraNumero('##/##/####',this,event,'dig') />
                                      </div>                                        
                                      <div class='form-group'>
                                        <button onClick='fncCorreio();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>
              </button>              
            </span>
          </div>
        </section>  

        <section id="collapseAtiva" class="section-combo" data-tamanho="180" style="display:none;">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Data ativação</a>
              <button type="button" id="popoverAtiva" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group'>
                                        <label for='edtDtAtiva' class='control-label'>Ativado em</label>
                                        <input type='text' class='form-control' disabled id='edtDtAtiva' placeholder='##/##/####' onkeyup=mascaraNumero('##/##/####',this,event,'dig') />
                                      </div>
                                      <div class='form-group'>
                                        <label for='edtLocalInsta' class='control-label'>Local Instalacao</label>
                                        <input type='textarea' class='form-control' id='edtLocalInsta'/>
                                      </div>                                        
                                      <div class='form-group'>
                                        <button onClick='fncAtiva();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>        
              </button>
            </span>
          </div>
        </section>  
      </div>
      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <section>
        <section id="sctnCntI">
        </section>  
      </section>
      
      <form method="post" class="center" id="frmCntI" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:0em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="inactive">
          <input id="ageApeCol" value="*" type="text" />        <!-- Apelido do colaborador -->
          <input id="edtModoEntrega" value="*" type="text" />   <!-- Codigo do modo de entrega -->        
          <input id="edtStatusEntrega" value="*" type="text" /> <!-- Codigo do status da entrega -->                
        </div>
      </form>
    </div>
    <!--
    Buscando o historico do auto
    -->
    <section id="collapseSectionAuto" class="section-collapse">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a class="btn btn-default disabled">Buscar</a>
            <button id="abreAuto" class="btn btn-primary" 
                                  data-toggle="collapse" 
                                  data-target="#evtAbreAuto" 
                                  aria-expanded="true" 
                                  aria-controls="evtAbreAuto" 
                                  type="button">Historico auto</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreAuto" aria-expanded="false" role="presentation">
          <div id="cllServico" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblAuto" class="alert-info">Mostrando historico do auto</label>
          </div>
        </div>
      </div>
    </section>
    
    <div id="agendacad" class="frmTable" style="display:none; width:90em; margin-left:11em;margin-top:5.5em;position:absolute;">
      <div id="divTitulo" class="frmTituloManutencao">Agendamento<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>
      <div style="height: 230px; overflow-y: auto;">
        <div class="campotexto campo100">
          <!-- Endereco de entrega -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodEnt"
                                                onBlur="codEntBlur(this);" 
                                                onFocus="entFocus(this);" 
                                                onClick="entF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodEnt">ENTREGA:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesEnt" type="text" disabled /><label class="campo_label campo_required" for="edtDesEnt">ENDERECO ENTREGA</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtCddEnt" type="text" disabled /><label class="campo_label campo_required" for="edtCddEnt">CIDADE</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCepEnt" type="text" disabled /><label class="campo_label campo_required" for="edtCepEnt">CEP</label>
          </div>
          <!-- Endereco de instalacao -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodIns"
                                                onBlur="codInsBlur(this);" 
                                                onFocus="insFocus(this);" 
                                                onClick="insF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodIns">INSTALA:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesIns" type="text" disabled /><label class="campo_label campo_required" for="edtDesIns">ENDERECO INSTALACAO</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtCddIns" type="text" disabled /><label class="campo_label campo_required" for="edtCddIns">CIDADE</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCepIns" type="text" disabled /><label class="campo_label campo_required" for="edtCepIns">CEP</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtData" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             value="00/00/0000"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtData">DATA VISITA:</label>
          </div>
          <!--
                 -->
          <!-- Colaborador -->
          <div class="campotexto campo15">
            <input class="campo_input inputF10" id="ageCodCol"
                                                onClick="colF10Click(this);"
                                                onBlur="codColBlur(this);"                                                 
                                                data-oldvalue="0000"
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="ageCodCol">COLABORADOR:</label>
          </div>
          <div class="campotexto campo70">
            <input class="campo_input_titulo input" id="ageDesCol" type="text" disabled /><label class="campo_label campo_required" for="ageDesCol">NOME COLABORADOR</label>
          </div>
          <div id="divReferente" class="campotexto campo20">
            <select class="campo_input_combo" id="cbCodMsg">
              <option value="18">DESISTALACAO</option>
              <option value="19">MANUTENCAO</option>
              <option value="20">REINSTALACAO</option>
              <option value="17">REVISAO</option>
            </select>
            <label class="campo_label" for="cbCodMsg">OS REFERENTE:</label>
          </div>
          <div id="divLocal" class="campotexto campo20">
            <select id="cbLocal" class="campo_input_combo" /> 
              <option value="C" selected >CLIENTE</option>
              <option value="I">INTERNO</option>
            </select>
            <label class="campo_label" for="cbLocal">LOCAL SERVIÇO:</label>
          </div>
          
          <!--
          -->
          <div onClick="ageConfirmarClick();" id="ageConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
          <div onClick="document.getElementById('agendacad').style.display='none';" id="ageCancelar" class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
          <div class="campotexto campo100">
            <label id="lblCadAge" class="labelMensagem" for="edtUsuario">Para agendamento ser completo todos os campos devem ser informados</label>
          </div>
        </div>  
      </div>
    </div>
    
    <script>
      //
      //
      ///////////////////
      // Correio
      ///////////////////
      ppvCorreio = new Popover('#popoverCorreio',{ trigger: 'click'} );      
      evtCorreio = document.getElementById('popoverCorreio');
      evtCorreio.status="ok";
      //////////////////////////////////////////////
      // show.bs.popover(quando o metodo eh chamado)
      //////////////////////////////////////////////
      evtCorreio.addEventListener('show.bs.popover', function(event){
        try{
          chkds=objCntI.gerarJson("n").gerar();
          let qtdCor=0;
          let qtdTra=0;
          chkds.forEach(function(reg){
            if( reg.ME=="COR" ) qtdCor++;
            if( reg.ME=="TRA" ) qtdTra++;
          });
          if( (qtdCor>0) && (qtdTra>0) )
            throw "FAVOR SELECIONAR CORREIO OU TRANSPORTADORA!";                
          evtCorreio.status="ok";            
          
          if( qtdCor>0 )
            evtCorreio.status="cor";  
        }catch(e){
          evtCorreio.status="err";
          gerarMensagemErro("catch",e,"Erro");
        };
      },false);  
      //////////////////////////////////////////////////
      // shown.bs.popover(quando o metodo eh completado)
      //////////////////////////////////////////////////
      evtCorreio.addEventListener('shown.bs.popover', function(event){
        if( evtCorreio.status=="err" ){
          ppvCorreio.hide();
        } else {    
          if( evtCorreio.status=="cor" ){
            $doc("edtCodRastreio").foco();
          } else {
            $doc("edtCodRastreio").value="NSA";
            jsCmpAtivo("edtCodRastreio").add("campo_input_titulo").disabled(true);
            $doc("edtDtEntrega").foco();
          }
        };
      }, false);
      //
      //
      ///////////////////
      // Data de ativacao
      ///////////////////
      ppvAtiva = new Popover('#popoverAtiva',{ trigger: 'click'} );      
      evtAtiva = document.getElementById('popoverAtiva');
      evtAtiva.status="ok";
      
      evtAtiva.addEventListener('show.bs.popover', function(event){
        try{
          chkds=objCntI.gerarJson("1").gerar();
          chkds.forEach(function(reg){
            if( reg.PLACA_CHASSI=="" )
              throw "AUTO SEM PLACA!";  
            if( reg.ATIVADO !="" )
              throw "AUTO COM DATA DE ATIVAÇÃO!";  
          });
          evtAtiva.status="ok";            
        }catch(e){
          evtAtiva.status="err";
          gerarMensagemErro("catch",e,"Erro");
        };
        //$doc("edtDtAtiva").value=jsDatas(0).retDDMMYYYY();
      },false);  
      evtAtiva.addEventListener('shown.bs.popover', function(event){
        if( evtAtiva.status=="err" ){
          ppvAtiva.hide();
        } else {    
          $doc("edtDtAtiva").value=jsDatas(0).retDDMMYYYY();
        };
      },false);
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                                                PopUp                                                       //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreAuto  = new Collapse($doc('abreAuto'));
      abreAuto.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreAuto = document.getElementById('evtAbreAuto');
      collapseAbreAuto.addEventListener('show.bs.collapse', function(el){ 
        try{
          chkds=objCntI.gerarJson("1").gerar();
          if( chkds[0].CODGMP == 0 )
            throw "AUTO NÃO LOCALIZADO PARA HISTORICO!";  
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "detAuto"           );  // Detalhe do auto
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("codgmp"      , chkds[0].CODGMP     );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contratoproduto" , clsJs.fim());
          msg=requestPedido("Trac_ContratoProduto.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            $doc("cllServico").innerHTML=retPhp[0]["dados"];
            $doc("lblAuto").innerHTML="Mostrando historico do auto <b>"+chkds[0].CODGMP+"</b>";
            abreAuto.status="ok";
          };  
        }catch(e){
          abreAuto.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbreAuto.addEventListener('shown.bs.collapse', function(){ 
        if( abreAuto.status=="err" )
          abreAuto.hide();
      },false);
    </script>
  </body>
</html>



         
         
         
         
         
         
