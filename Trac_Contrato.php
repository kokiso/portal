<?php
  session_start();
  if( isset($_POST["contrato"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/dataCompetencia.class.php");
      require("classPhp/selectRepetido.class.php");       						      

      $clsCompet  = new dataCompetencia();    
      $vldr       = new validaJSon();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["contrato"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $strExcel = "*"; 
        $arrUpdt  = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        //
        //
        if( $lote[0]->rotina=="gravavlr" ){
          $sql ="UPDATE CONTRATO SET";
          $sql.=" CNTT_VLRNOSHOW=".$lote[0]->cntp_vlrnoshow;
          $sql.=",CNTT_VLRIMPRODUTIVEL=".$lote[0]->cntp_vlrimprodutivel;
          $sql.=",CNTT_VLRINSTALA=".$lote[0]->cntp_vlrinstala;
          $sql.=",CNTT_VLRDESISTALA=".$lote[0]->cntp_vlrdesistala;
          $sql.=",CNTT_VLRREINSTALA=".$lote[0]->cntp_vlrreinstala;
          $sql.=",CNTT_VLRMANUTENCAO=".$lote[0]->cntp_vlrmanutencao;
          $sql.=",CNTT_VLRREVISAO=".$lote[0]->cntp_vlrrevisao;
          $sql.=" WHERE (CNTT_CODIGO=".$lote[0]->codcntt.")";
          array_push($arrUpdt,$sql);                                    
          $atuBd = true;
        };  
        if( $lote[0]->rotina=="vlrOs" ){
          $sql="";          
          $sql.="SELECT CNTT_VLRNOSHOW,CNTT_VLRIMPRODUTIVEL,CNTT_VLRINSTALA,CNTT_VLRDESISTALA,CNTT_VLRREINSTALA,CNTT_VLRMANUTENCAO,CNTT_VLRREVISAO";
          $sql.="  FROM CONTRATO";
          $sql.=" WHERE (CNTT_CODIGO=".$lote[0]->codcntt.")";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
        };  
        if( $lote[0]->rotina=="umaplaca" ){
          $sql="SELECT A.VCL_CODCNTT FROM VEICULO A WITH(NOLOCK) WHERE VCL_CODIGO='".$lote[0]->placa."'";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          $retorno='[{"retorno":"OK","dados":'.$retCls["dados"][0]["VCL_CODCNTT"].',"erro":""}]'; 
        }    
        if( $lote[0]->rotina=="hlpPlaca" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("hlpPlaca",$lote[0]->login,$lote[0]->codcntt);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        if( $lote[0]->rotina=="hlpOs" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("hlpOs",$lote[0]->login,$lote[0]->codcntt);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        if( $lote[0]->rotina=="hlpEndereco" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("hlpEndereco",$lote[0]->login,$lote[0]->codfvr);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        if( $lote[0]->rotina=="hlpTerceiro" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("hlpTerceiro",$lote[0]->login,$lote[0]->array);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        if( $lote[0]->rotina=="hlpComposicao" ){
          $sql="";          
          $sql.="SELECT A.CNTP_CODCNTT AS CONTRATO";
          $sql.="       ,GM.GM_NOME AS REFERENTE";
          $sql.="       ,CASE WHEN A.CNTP_MENSAL='M' THEN 'MENSAL' ELSE 'PONTUAL' END AS COBRANCA";          
          $sql.="  FROM CONTRATOPRODUTO A";
          $sql.="  LEFT OUTER JOIN GRUPOMODELO GM ON A.CNTP_CODGM=GM.GM_CODIGO";
          $sql.=" WHERE (A.CNTP_CODCNTT=".$lote[0]->codcntt.") AND (A.CNTP_IDUNICO=CNTP_IDSRV)";
          $sql.=" UNION ALL ";
          $sql.="SELECT A.CNTM_CODCNTT AS CONTRATO";
          $sql.="       ,SRV.SRV_NOME AS REFERENTE";
          $sql.="       ,CASE WHEN A.CNTM_MENSAL='M' THEN 'MENSAL' ELSE 'PONTUAL' END AS COBRANCA";                    
          $sql.="  FROM CONTRATOMENSAL A";
          $sql.="  LEFT OUTER JOIN SERVICO SRV ON A.CNTM_CODSRV=SRV.SRV_CODIGO";
          $sql.=" WHERE (A.CNTM_CODCNTT=".$lote[0]->codcntt.")";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
        };  
        /////////////////////////
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="filtrar" ){        
          $sql="";
          $sql.="SELECT A.CNTT_CODIGO AS CODIGO";
          $sql.="       ,CNTT_TIPO AS TP";          
          $sql.="       ,CONVERT(VARCHAR(10),A.CNTT_EMISSAO,127) AS EMISSAO";                                        
          $sql.="       ,CONVERT(VARCHAR(10),A.CNTT_DTINICIO,127) AS INICIO";                                          
          $sql.="       ,CONVERT(VARCHAR(10),A.CNTT_DTFIM,127) AS FIM";                                          
          $sql.="       ,CNTT_CODFVR";
          $sql.="       ,FVR.FVR_APELIDO AS CLIENTE";                    
          //$sql.="       ,CNTT_CODVND";
          //$sql.="       ,CNTT_CODIND";
          $sql.="       ,(CNTT_VLRMENSAL / CNTT_MESES) AS CVLRMENSALPARC";
          $sql.="       ,CNTT_VLRMENSAL";
          $sql.="       ,CNTT_VLRPONTUAL";
          $sql.="       ,CNTT_QTDAUTO";
          $sql.="       ,CNTT_QTDEMPENHO";
          $sql.="       ,CNTT_QTDENVIADO";                    
          $sql.="       ,CNTT_QTDAGENDA"; 
          $sql.="       ,CNTT_QTDOS"; 
          $sql.="       ,CNTT_QTDPLACA";
          $sql.="       ,CNTT_QTDATIVADO";
          //$sql.="       ,CNTT_EMAIL";
          //$sql.="       ,CNTT_LANCTOINI";
          //$sql.="       ,CNTT_LANCTOFIM";
          //$sql.="       ,CNTT_CODBNC";
          //$sql.="       ,CNTT_CODFC";
          //$sql.="       ,CNTT_CODUSR";
          $sql.="       ,CASE WHEN A.CNTT_ATIVO='S' THEN 'ATIVO' ELSE 'INATIVO' END AS CNTT_ATIVO";          
          $sql.="       ,CNTT_FIDELIDADE";
          $sql.="       ,CNTT_INSTPROPRIA";
          $sql.="       ,US.US_APELIDO";      
          $sql.="      ,CONCAT('<table class=''fpTable'' style=''width:250px;''>'";
          $sql.="       ,'<thead class=''fpThead''>'";		
          $sql.="      	,'<tr>'";
          $sql.="      	,'<th class=''fpTh'' style=''width:50%;''> CAMPO'";
          $sql.="      	,'</th>'";
          $sql.="      	,'<th class=''fpTh'' style=''width:50%;''> CONTEUDO'";
          $sql.="      	,'</th>'";
          $sql.="      	,'</tr>'";
          $sql.="      	,'</thead>'";          
          $sql.="       ,'<tbody>'";		          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>VLR NOSHOW</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_VLRNOSHOW,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>VLR IMPRODUTIVEL</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_VLRIMPRODUTIVEL,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>VLR INSTALACAO</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_VLRINSTALA,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>VLR DESISTALACAO</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_VLRDESISTALA,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>VLR MANUTUTENCAO</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_VLRMANUTENCAO,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>VLR REVISAO</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_VLRREVISAO,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>FIDELIDADE</td>'";          
          $sql.="      	,'<td class=''fpTd''>',CNTT_FIDELIDADE,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>MESES</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_MESES,'</td>'";
          $sql.="      	,'</tr>'";          
          $sql.="      	,'<tr>'";
          $sql.="      	,'<td class=''fpTd''>COBRANCA DIA</td>'";          
          $sql.="      	,'<td class=''fpTd textoDireita''>',CNTT_DIA,'</td>'";
          $sql.="      	,'</tr>'";          
          
          $sql.="       ,'</tbody>'";		                    
          $sql.="      	,'</table>') AS POPOVER";
          $sql.="  FROM CONTRATO A";           
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.CNTT_CODFVR=FVR.FVR_CODIGO";          
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON US.US_CODIGO = A.CNTT_CODUSR"; 
          $sql.="  WHERE (A.CNTT_ATIVO IN('S','N'))"; 
          ///////////////////
          // coddir        //
          // 0-SEM DIREITO //
          // 1-DO VENDEDOR //
          // 2-TODOS       //
          ///////////////////
          /*
          if( $lote[0]->coddir == 1 ){
            $sql.=" AND (A.PDD_EMAIL='".$lote[0]->email."')";
          };  
          */
          /*
          if( $lote[0]->status <> 0 ){
            $sql.=" AND (A.CNTT_STATUS=".$lote[0]->status.")";  
          }; 
          */
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
      };  
      ///////////////////////////////////////////////////////////////////
      // Atualizando o banco de dados se opcao de insert/updade/delete //
      ///////////////////////////////////////////////////////////////////
      if( $atuBd ){
        if( count($arrUpdt) >0 ){
          $retCls=$classe->cmd($arrUpdt);
          if( $retCls['retorno']=="OK" ){
            $retorno='[{"retorno":"OK","dados":"","erro":"'.count($arrUpdt).' REGISTRO(s) ATUALIZADO(s)!"}]'; 
          } else {
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          };  
        } else {
          $retorno='[{"retorno":"OK","dados":"","erro":"NENHUM REGISTRO CADASTRADO!"}]';
        };  
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
    <script src="js/jsBiblioteca.js"></script>
    <!--<script src="js/jsCopiaDoc2017.js"></script>-->
    <script>      
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsCntt={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "CODIGO"
                      ,"fieldType"      : "int"
                      ,"obj"            : "edtCnttCod"
                      ,"formato"        : ["i6"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "TP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "<b>V</b>enda, <b>L</b>ocação ou <b>D</b>emostração"                          
                      ,"popoverLabelCol": "Tipo"                      
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "EMISSAO"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "INICIO"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Data da primeira ativação"                          
                      ,"popoverLabelCol": "Inicio do contrato"                      
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "FIM"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Ano/Mês( YYYYMM ) da última cobrança"                          
                      ,"popoverLabelCol": "Termino do contrato"                      
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "CODFVR"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      //,"align"          : "center"    
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "CLIENTE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "35"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":8  ,"field"          :"CVLRMENSALPARC"
                      ,"labelCol"       :'VLRMENSALPARCIAL'
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "0"
                      ,"sepMilhar"      : true                      
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":9  
                      ,"labelCol"       :'VLRMENSAL'
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"sepMilhar"      : true                      
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":10  ,"labelCol"       : "VLRPONTUAL"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "0em"
                      ,"sepMilhar"      : true                      
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "AUTOS"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "EMPENHO"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "ENV"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"popoverTitle"   : "Total de autos enviados e recebidos pelo responsavel"                          
                      ,"popoverLabelCol": "Autos enviados"                      
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "AGENDA"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "OS"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"    
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"
                      ,"popoverTitle"   : "Total de OSs pendentes"                          
                      ,"popoverLabelCol": "Ordem serviço"                      
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":16 ,"labelCol"       : "PLACAS"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":17 ,"labelCol"       : "ATIVACAO"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":18 ,"labelCol"       : "STATUS"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":19 ,"labelCol"       : "FID"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "12"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Fidelidade"
                      ,"padrao":0}
            ,{"id":20 ,"labelCol"       : "IP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Instalação própria"
                      ,"padrao":0}
            ,{"id":21 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":22 ,"labelCol"       : "POP"         
                      ,"obj"            : "imgPP"
                      ,"tamGrd"         : "5em"
                      ,"fieldType"      : "popover"
                      ,"popoverTitle"   : "Pop up de campos relacionados a este registro"                      
                      ,"padrao":0}
            ,{"id":23 ,"labelCol"       : "SERV"         
                      ,"obj"            : "imgPP"
                      ,"tamGrd"         : "5em"
                      ,"tipo"           : "img"
                      ,"fieldType"      : "img"
                      ,"func"           : "cnttServicoClick(this.parentNode.parentNode.cells[1].innerHTML);"
                      ,"tagI"           : "fa fa-clone"
                      ,"popoverTitle"   : "Serviços relacionados a este auto"
                      ,"padrao":0}
            ,{"id":24 ,"labelCol"       : "END"         
                      ,"obj"            : "imgPP"
                      ,"tamGrd"         : "5em"
                      ,"tipo"           : "img"
                      ,"fieldType"      : "img"
                      ,"func"           : "fncEndereco(this.parentNode.parentNode.cells[5].innerHTML);"
                      ,"tagI"           : "fa fa-clone"
                      ,"popoverTitle"   : "Endereços relacionados a este auto"                      
                      ,"padrao":0}
            ,{"id":25 ,"labelCol"       : "TERC"         
                      ,"obj"            : "imgPP"
                      ,"tamGrd"         : "5em"
                      ,"tipo"           : "img"
                      ,"fieldType"      : "img"
                      ,"func"           : "fncTerceiro(this.parentNode.parentNode.cells[5].innerHTML,0);"
                      ,"tagI"           : "fa fa-clone"
                      ,"popoverTitle"   : "Relação de terceiros por KM"                      
                      ,"padrao":0}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"300px" 
              ,"label"          :"RELACIONAMENTO - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Alterar"         ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"   }
            ,{"texto":"Excluir"         ,"name":"horExcluir"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-minus"             
                                        ,"popover":{title:"Excluir",texto:"Esta opção exclui o contrato do sistema em definitivo.<hr>Não existe maneira de recuperar o registro",aviso:"warning"}} 
            ,{"texto":"Empenho"         ,"name":"horEmpenho"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-eye-slash"      
                                        ,"popover":{title:"Empenho",texto:"Opção para empenhar individualmente cada auto pelo seu número de serie<hr>"
                                                                          +"O auto obrigatoriamente deve estar em estoque"}} 
            ,{"texto":"Agenda"          ,"name":"horAgenda"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-calendar"
                                        ,"popover":{title:"Agenda",texto:"Opção para informar endereço entrega/instalação/data/colaborador<hr>"
                                                                          +"O auto obrigatoriamente deve estar empenhado"}} 
            ,{"texto":"OS"              ,"name":"horGerarOs"       ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-wrench"
                                        ,"popover":{title:"Ordem serviço",texto:"Gera OS para auto selecionado"}} 
            ,{"texto":"Placa"           ,"name":"horPlaca"      ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-truck"
                                        ,"popover":{title:"Placa/Ativação",texto:"Opção para informar a <b>placa</b> e <b>data de ativação</b>"}} 
            ,{"texto":"Copia contrato"  ,"name":"horCopiaPed"   ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-print"          }                                    
            ,{"texto":"Fechar"          ,"name":"horFechar"     ,"onClick":"6"  ,"enabled":true ,"imagem":"fa fa-close"          }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)              
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmCntt"                 // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCntt"              // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCntt"                 // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnCntt"                // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblCntt"                 // Nome da table
          ,"prefixo"        : "Me"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VCONTRATO"               // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "140em"                   // Tamanho da table
          ,"height"         : "58em"                    // Altura da table
          ,"nChecks"        : false                     // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "CONTRATO"                // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"indiceTable"    : "CODIGO"                  // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[40]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objCntt === undefined ){  
          objCntt=new clsTable2017("objCntt");
        }; 
        objCntt.montarHtmlCE2017(jsCntt); 
        $doc("dPaifrmCntt").style.float="none";
        ///////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_ContratoProduto.php
        // esta garante o chkds[0].?????? e objCol
        ///////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsCntt,["AGENDA","ATIVACAO" ,"STATUS" ,"AUTOS","CLIENTE","CODIGO" ,"EMISSAO"  ,"EMPENHO","ENV"
                                             ,"FID"   ,"FIM"      ,"INICIO" ,"IP"   ,"OS"     ,"PLACAS" ,"USUARIO","VLRMENSAL","VLRPONTUAL"]);
        //                                     
        btnFiltrarClick($doc("cbStatus").getAttribute("data-indice"));
        adicionarDataToggle(); // Mostra popover para campos na grade com grade dentro
      });
      var objCntt;                    // Obrigatório para instanciar o JS TFormaCob
      var jsCntt;                     // Obj principal da classe clsTable2017
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var chkds;                      // Guarda todos registros checados na table 
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario      
      var ppvUmaPlaca;                // Abrir popover somente com click
      var evtUmaPlaca;                // Eventos
      var abrePlaca;
      var abreOs;      
      /*
      var btnModal2;
      var externalModalContent;
      var secondModalContent;
      var modalInitJS2;
      */
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d40);
      /////////////////////////////////////////////////
      // Filtrando os registros para grade de contratos
      /////////////////////////////////////////////////
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"                                   );
        clsJs.add("login"       , jsPub[0].usr_login                          );
        clsJs.add("email"       , jsPub[0].usr_email                          );        
        clsJs.add("status"      , $doc("cbStatus").getAttribute("data-indice"));                
        fd = new FormData();
        fd.append("contrato" , clsJs.fim());
        msg     = requestPedido("Trac_Contrato.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsCntt.registros=objCntt.addIdUnico(retPhp[0]["dados"]);
          objCntt.ordenaJSon(jsCntt.indiceTable,false);  
          objCntt.montarBody2017();
          $doc("spnTotCtt").innerHTML=jsNmrs((retPhp[0]["dados"]).length).emZero(4).ret();;
        };  
      };
      /////////////////////////////////////////////
      // Chamando o formulario de itens do contrato
      /////////////////////////////////////////////
      function horEmpenhoClick(){
        qualRotina("EMPENHO",false);
      };  
      function horAgendaClick(){
        qualRotina("AGENDA",true);
      };  
      function horPlacaClick(){
        qualRotina("PLACA",false);
      };  
      function horGerarOsClick(){
        qualRotina("OS",true);
      };  
      function qualRotina(rot,nChecks){
        try{
          chkds=objCntt.gerarJson("1").gerar();
          switch(rot){
            case "AGENDA":
              if( parseInt(chkds[0].EMPENHO)==0 )
                throw "Pedido "+chkds[0].CODIGO+" deve ter empenho para informação de agenda!";
            break;      
            case "PLACA":
              if( parseInt(chkds[0].EMPENHO)==0 )
                throw "Pedido "+chkds[0].CODIGO+" deve ter empenho para informação de placa!";
            break;      
            
          };  
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("codCntt"       , parseInt(chkds[0].CODIGO) );
          clsJs.add("codFvr"        , parseInt(chkds[0].CODFVR) );          
          clsJs.add("nChecks"       , nChecks                   );          
          clsJs.add("qualRotina"    , rot                       );
          /////////////////////////////////////////////////////////////////
          // Passando as colunas que vou precisas para atualizar esta table
          /////////////////////////////////////////////////////////////////
          for(let key in objCol) { 
            clsJs.add(key, parseInt(objCol[key]) );          
          };            
          objEnvio=clsJs.fim();
          localStorage.setItem("addInd",objEnvio);
          window.open("Trac_ContratoProduto.php");
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Aviso"});  
        };
      };  
      /////////////////////////////////////////////
      // Produto/Servico de um contrato 
      /////////////////////////////////////////////
      function cnttServicoClick(codcntt){
        try{          
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "hlpComposicao"     );
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("codcntt"     , codcntt             );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contrato" , clsJs.fim());
          msg=requestPedido("Trac_Contrato.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            let tbl=retPhp[0].dados;
            let clsCode = new concatStr();  
            clsCode.concat("<div id='dPaiChk' class='divContainerTable' style='height: 21.2em; width: 65em;border:none'>" );
            clsCode.concat("<table id='tblChk' class='fpTable' style='width:100%;'>"                                      );
            clsCode.concat("  <thead class='fpThead'>"                                                                    );
            clsCode.concat("    <tr>"                                                                                     );
            clsCode.concat("      <th class='fpTh' style='width:15%'>CONTRATO</th>"                                       );
            clsCode.concat("      <th class='fpTh' style='width:70%'>PRODUTO/SERVICO</th>"                                );
            clsCode.concat("      <th class='fpTh' style='width:15%'>COBRANCA</th>"                                       );          
            clsCode.concat("    </tr>"                                                                                    );
            clsCode.concat("  </thead>"                                                                                   );
            clsCode.concat("  <tbody id='tbody_tblChk'>"                                                                  );

            tbl.forEach(function(reg){
              clsCode.concat("    <tr class='fpBodyTr'>");
              clsCode.concat("      <td class='fpTd textoCentro'>"+jsNmrs(reg.CONTRATO).emZero(6).ret()+"</td>");
              clsCode.concat("      <td class='fpTd'>"+reg.REFERENTE+"</td>");
              clsCode.concat("      <td class='fpTd textoCentro'>"+reg.COBRANCA+"</td>");            
              clsCode.concat("    </tr>");
            });
            ////////////////////////
            // Fim do HTML
            ////////////////////////
            clsCode.concat("  </tbody>");        
            clsCode.concat("</table>");
            clsCode.concat("</div>"); 
            clsCode.concat("<div id='alertCom' class='alert alert-info alert-dismissible fade in' role='alert' style='font-size:1.5em;margin-bottom:5px;text-align:center;'>");
            clsCode.concat("Mostrando a <b>composição</b> completa do auto.");
            clsCode.concat("</div>");          
            janelaDialogo(
              { height          : "32em"
                ,body           : "16em"
                ,left           : "300px"
                ,top            : "60px"
                ,tituloBarra    : "Composição contrato"
                ,code           : clsCode.fim()
                ,width          : "67em"
                ,fontSizeTitulo : "1.8em"
              }
            );
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function fncEndereco(str){
				if( jsNmrs(str).inteiro().ret()>0 ){
                      
					try{          
						clsJs   = jsString("lote");  
						clsJs.add("rotina"  , "hlpEndereco"               );
						clsJs.add("login"   , jsPub[0].usr_login          );
						clsJs.add("codfvr"  , jsNmrs(str).inteiro().ret() );
						fd = new FormData();
						fd.append("contrato" , clsJs.fim());
						msg     = requestPedido("Trac_Contrato.php",fd); 
						retPhp  = JSON.parse(msg);
						if( retPhp[0].retorno == "OK" ){
							janelaDialogo(
								{ height          : "36em"
									,body           : "16em"
									,left           : "300px"
									,top            : "60px"
									,tituloBarra    : "Endereco"
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
      function fncTerceiro(str,qual){
				if( jsNmrs(str).inteiro().ret()>0 ){
					try{          
						clsJs   = jsString("lote");  
						clsJs.add("rotina"  , "hlpTerceiro"                     );
						clsJs.add("login"   , jsPub[0].usr_login                );
						clsJs.add("array"   , jsNmrs(str).inteiro().ret()+"|0"  );  // Array para informar que eh para pegar o codigo do favorecido
						fd = new FormData();
						fd.append("contrato" , clsJs.fim());
						msg     = requestPedido("Trac_Contrato.php",fd); 
						retPhp  = JSON.parse(msg);
						if( retPhp[0].retorno == "OK" ){
							janelaDialogo(
								{ height          : "49em"
									,body           : "16em"
									,left           : "300px"
									,top            : "60px"
									,tituloBarra    : "Terceiros"
									,code           : retPhp[0]["dados"]
									,width          : "80em"
									,fontSizeTitulo : "1.8em"
								}
							);
              adicionarDataToggle();
						};  
					}catch(e){
						gerarMensagemErro('catch',e.message,{cabec:"Erro"});
					};
				};
      };
      function fncUmaPlaca(){
        try{  
          $doc("edtUmaPlaca").value = jsConverte("#edtUmaPlaca").upper().alltrim();        
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "umaplaca"                );
          clsJs.add("login"       , jsPub[0].usr_login        );
          clsJs.add("placa"       , $doc("edtUmaPlaca").value );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contrato" , clsJs.fim());
          msg=requestPedido("Trac_Contrato.php",fd); 
          retPhp=JSON.parse(msg);
          $doc("edtUmaPlacaCntt").value="000000";
          if( retPhp[0].retorno=="OK" ){
            $doc("edtUmaPlacaCntt").value=jsConverte(retPhp[0].dados).emZero(6);  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };  
      function gravaVlr(){
        try{
          ///////////////////////////////////////////////////////////////////////////
          // Classe para montar envio para o Php
          ///////////////////////////////////////////////////////////////////////////    
          clsJs = jsString("lote");                
          clsJs.add("rotina"                , "gravavlr"                                );              
          clsJs.add("login"                 , jsPub[0].usr_login                        );
          clsJs.add("codcntt"               , chkds[0].CODIGO                           );          
          clsJs.add("cntp_vlrnoshow"        , jsConverte("#edtVlrNoShow").dolar()       );
          clsJs.add("cntp_vlrimprodutivel"  , jsConverte("#edtVlrImprodutivel").dolar() );
          clsJs.add("cntp_vlrinstala"       , jsConverte("#edtVlrInstala").dolar()      );
          clsJs.add("cntp_vlrdesistala"     , jsConverte("#edtVlrDesistala").dolar()    );
          clsJs.add("cntp_vlrreinstala"     , jsConverte("#edtVlrReinstala").dolar()    );
          clsJs.add("cntp_vlrmanutencao"    , jsConverte("#edtVlrManutencao").dolar()   );
          clsJs.add("cntp_vlrrevisao"       , jsConverte("#edtVlrRevisao").dolar()      );
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("contrato" , clsJs.fim());
          msg=requestPedido("Trac_Contrato.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});            
          } else {  
            modalInitJS.hide();            
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
        
      }  
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral" style="border-right:none;">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="objCntt.imprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela.">
            <i class="indFa fa-print"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="objCntt.excel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela.">
            <i class="indFa fa-file-excel-o"></i>
          </div>
          
          <div id="divBlCntt" class="divBarraLateral" 
                              data-toggle="modal" data-target="#modalCntt"                              
                              data-content="Valor padrão para OS parametrizados neste contrato.">
            <i class="indFa fa-edit"></i>
          </div>
          <div id="modalCntt" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCnttLabel" aria-hidden="true">
            <div class="modal-dialog" style="width:25%;" >
              <div class="modal-content">

              </div>
            </div>
          </div>
        </section>
      </aside>
      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-newspaper-o" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">Contrato</span>
              <span id="spnTotCtt" class="infoBoxNumber"></span>
            </div>
          </div>
        </div>        
        <!----------------------------------------------------------------
        Para alterar o texto do filtro chamar a function alteraLabelFiltro
        ----------------------------------------------------------------->
        <section id="collapseSectionCombo" class="section-combo" data-tamanho="200">
          <div id="divStatus" class="btn-group" style="padding-top:8px;left:5px;">
            <button id="cbStatus" data-indice="0" type="button" class="btn btn-default disabled" style="width:160px;">Status | Todos</button>
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
              <li><a data-status="0">Todos</a></li>
              <li><a data-status="1">Ativos</a></li>
              <li><a data-status="2">Inativos</a></li>
            </ul>
          </div>
        </section>
        <section id="collapseCorreio" class="section-combo" data-tamanho="210" style="margin-left:2px;">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Procurar uma placa</a>
              <button type="button" id="popoverUmaPlaca" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group'>
                                        <label for='edtUmaPlaca' class='control-label'>Placa</label>
                                        <input type='text' class='form-control' id='edtUmaPlaca' placeholder='informe' />
                                      </div>
                                      <div class='form-group'>
                                        <label for='edtUmaPlacaCntt' class='control-label'>Contrato</label>
                                        <input type='text' class='form-control' id='edtUmaPlacaCntt' placeholder='informe' disabled />
                                      </div>
                                      <div class='form-group'>
                                        <button onClick='fncUmaPlaca();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>
              </button>              
            </span>
          </div>
        </section>  
        <!---->  
      </div>
      <section>
        <section id="sctnCntt">
        </section>  
      </section>
      
      <form method="post"
            name="frmCntt"
            id="frmCntt"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <div class="frmTituloManutencao">Contrato<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>              
        <div style="height: 290px; overflow-y: auto;">   
          <input type="hidden" id="sql" name="sql"/> 
          <div class="inactive">
            <input id="edtTerceiroTbl" value="*" type="text" />
          </div>
        </div>
      </form>  
    </div>
    <!--
    Buscando as placas deste contrato
    -->
    <section id="collapseSectionPlaca" class="section-collapse">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a class="btn btn-default disabled">Deste contrato</a>
            <button id="abrePlaca"  class="btn btn-primary" 
                                    data-toggle="collapse" 
                                    data-target="#evtAbrePlaca" 
                                    aria-expanded="true" 
                                    aria-controls="evtAbrePlaca" 
                                    type="button">Placas</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbrePlaca" aria-expanded="false" role="presentation">
          <div id="cllServico" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblPlaca" class="alert-info">Mostrando todas as <b>placas</b> do contrato</label>
          </div>          
        </div>
      </div>
    </section>
    
    <section id="collapseSectionOs" class="section-collapse" style="margin-left:1px;">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <!--<a class="btn btn-default disabled">Deste contrato</a>-->
            <button id="abreOs" class="btn btn-primary" 
                                data-toggle="collapse" 
                                data-target="#evtAbreOs" 
                                aria-expanded="true" 
                                aria-controls="evtAbreOs" 
                                type="button">OSs</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreOs" aria-expanded="false" role="presentation">
          <div id="cllOs" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblOs" class="alert-info">Mostrando todas as <b>OSs</b> do contrato</label>
          </div>          
        </div>
      </div>
    </section>
    <script>
      ////////////////////////////////////////////////////////////////
      // Pegando a lista de filtro para troca de status conforme opcao
      ////////////////////////////////////////////////////////////////
      alteraLabelFiltro.call(document.getElementById('divStatus'),'data-status','Status | ','btnFiltrarClick()');
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abrePlaca  = new Collapse($doc('abrePlaca'));
      abrePlaca.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbrePlaca = document.getElementById('evtAbrePlaca');
      collapseAbrePlaca.addEventListener('show.bs.collapse', function(el){ 
        try{
          chkds=objCntt.gerarJson("1").gerar();
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "hlpPlaca"          );
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("codcntt"     , chkds[0].CODIGO     );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contrato" , clsJs.fim());
          msg=requestPedido("Trac_Contrato.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            $doc("cllServico").innerHTML=retPhp[0]["dados"];
            $doc("lblPlaca").innerHTML="Mostrando todas as <b>placas</b> do contrato <b>"+chkds[0].CODIGO+"</b>";
            abrePlaca.status="ok";
          };  
        }catch(e){
          abrePlaca.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbrePlaca.addEventListener('shown.bs.collapse', function(){ 
        if( abrePlaca.status=="err" )
          abrePlaca.hide();
      },false);
      //
      //
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreOs  = new Collapse($doc('abreOs'));
      abreOs.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreOs = document.getElementById('evtAbreOs');
      collapseAbreOs.addEventListener('show.bs.collapse', function(el){ 
        try{
          chkds=objCntt.gerarJson("1").gerar();
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "hlpOs"             );
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("codcntt"     , chkds[0].CODIGO     );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contrato" , clsJs.fim());
          msg=requestPedido("Trac_Contrato.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            $doc("cllOs").innerHTML=retPhp[0]["dados"];
            $doc("lblOs").innerHTML="Mostrando todas as <b>OSs</b> do contrato <b>"+chkds[0].CODIGO+"</b>";
            abreOs.status="ok";
          };  
        }catch(e){
          abreOs.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbreOs.addEventListener('shown.bs.collapse', function(){ 
        if( abreOs.status=="err" )
          abreOs.hide();
      },false);
      //
      //
      /////////////////////
      // Procurar uma placa
      /////////////////////
      ppvUmaPlaca = new Popover('#popoverUmaPlaca',{ trigger: 'click'} );      
      evtUmaPlaca = document.getElementById('popoverUmaPlaca');
      evtUmaPlaca.status="ok";
      //////////////////////////////////////////////
      // show.bs.popover(quando o metodo eh chamado)
      //////////////////////////////////////////////
      evtUmaPlaca.addEventListener('show.bs.popover', function(event){
      },false);  
      //////////////////////////////////////////////////
      // shown.bs.popover(quando o metodo eh completado)
      //////////////////////////////////////////////////
      evtUmaPlaca.addEventListener('shown.bs.popover', function(event){
        $doc("edtUmaPlaca").foco();        
      }, false);
      //
      //
      ///////////////////////////////////////////////////
      // Formulario para alteracao de valores de contrato
      ///////////////////////////////////////////////////
      var modalCntt = document.getElementById('modalCntt');
      var btnModal  = document.getElementById('divBlCntt');
      
      let arrObj=["edtVlrNoShow"  ,"edtVlrImprodutivel" ,"edtVlrInstala"    ,"edtVlrDesistala","edtVlrReinstala","edtVlrManutencao","edtVlrRevisao"];
      let arrLbl=["Valor NoShow"  ,"Valor Improdutivel" ,"Valor Instalação" ,"Valor Desistalação","Valor Reinstalação","Valor Manutenção","Valor Revisao"];
      
      
      let clsStr = new concatStr();
      clsStr.concat('<div class="modal-header">'                                                            );
      clsStr.concat(  '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'        );
      clsStr.concat(    '<span aria-hidden="true">×</span>'                                                 );
      clsStr.concat(  '</button>'                                                                           );
      clsStr.concat(  '<h4 class="modal-title" id="modalCnttLabel">VALORES PARA CONTRATO</h4>'              );
      clsStr.concat('</div>'                                                                                );
      clsStr.concat('<div class="modal-body">'                                                              );
      for(let lin=0;lin<7;lin++){
        clsStr.concat(  '<div class="form-group campo50" style="float:left;">'                              );
        clsStr.concat(    '<label for="'+arrObj[lin]+'" class="control-label">'+arrLbl[lin]+'</label>'      );
        clsStr.concat(    '<input type="text"'                                                              );
        clsStr.concat(           'id="'+arrObj[lin]+'"'                                                     );    
        clsStr.concat(           'class="form-control edtDireita"'                                          );
        clsStr.concat(           'onBlur="fncCasaDecimal(this,2);" />'                                      );
        clsStr.concat(  '</div>'                                                                            );
      };    
      clsStr.concat(  '<div class="form-group campo50" style="float:left;height:53px"></div>'               );
      clsStr.concat('</div>'                                                                                );            
      clsStr.concat('<div class="modal-footer">'                                                            );
      clsStr.concat(  '<button type="button" class="btn btn-primary" onClick="gravaVlr();">Gravar</button>'                       );
      clsStr.concat(  '<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>'  );
      clsStr.concat(  '</div>'                                                                              );
      clsStr.concat('</div>'                                                                                );      
      //////////////////////////  
      // Inicilializando o modal
      //////////////////////////
      var modalInitJS = new Modal(modalCntt, {
        content: sql=clsStr.fim(),
        backdrop: 'static'
      });
      btnModal.addEventListener('click',function(e){
        try{
          chkds=objCntt.gerarJson("1").gerar();
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "vlrOs"             );
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("codcntt"     , chkds[0].CODIGO     );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contrato" , clsJs.fim());
          msg=requestPedido("Trac_Contrato.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            let tbl=retPhp[0].dados[0];
            $doc("edtVlrNoShow").value        = tbl.CNTT_VLRNOSHOW;
            $doc("edtVlrImprodutivel").value  = tbl.CNTT_VLRIMPRODUTIVEL;
            $doc("edtVlrInstala").value       = tbl.CNTT_VLRINSTALA
            $doc("edtVlrDesistala").value     = tbl.CNTT_VLRDESISTALA
            $doc("edtVlrReinstala").value     = tbl.CNTT_VLRREINSTALA
            $doc("edtVlrManutencao").value    = tbl.CNTT_VLRMANUTENCAO
            $doc("edtVlrRevisao").value       = tbl.CNTT_VLRREVISAO
          };  
          modalInitJS.show();
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Aviso"});  
        };
      },false);
      //////////////////////////
      // Instanciando os eventos
      //////////////////////////
      modalCntt.addEventListener('show.bs.modal', function(event){
      }, false);      
      modalCntt.addEventListener('shown.bs.modal', function(event){
        $doc("edtVlrNoShow").foco();
      }, false);      
      //
      //
    </script>
  </body>
</html>