<?php
  session_start();
  if( isset($_POST["cnab"]) ){
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
        $arrUpdt  = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        ///////////////////////
        // Alterando  a empresa
        ///////////////////////
        if( $lote[0]->rotina=="excarquivo" ){
          $valor      = -1;          
          if( ($lote[0]->lanctoini==0) and ($lote[0]->lanctofim==0) ){
            $valor = 0;  
          } else {  
            $valor=0;
            $sql ="SELECT PGR_LOTECNAB";
            $sql.="       ,PGR_VLRLIQUIDO";          
            $sql.="  FROM PAGAR ";
            $sql.=" WHERE (PGR_LANCTO BETWEEN '".$lote[0]->lanctoini."' AND '".$lote[0]->lanctofim."')";          
            $sql.="   AND (PGR_LOTECNAB=".$lote[0]->codcnab.")";
            $classe->msgSelect(false);
            $retCls=$classe->selectAssoc($sql);
            if( $retCls['retorno']=="OK" ){
              $tbl  = $retCls["dados"];
              foreach( $tbl as $cmp ){
                if( $cmp["PGR_LOTECNAB"]>0 )
                $valor+=$cmp["PGR_VLRLIQUIDO"];
              };
            };  
          };    
          if( $valor==0 ){
            array_push($arrUpdt,"DELETE FROM CNAB WHERE CNB_CODIGO=".$lote[0]->codcnab); 
            $atuBd = true;
          } else {
            $retorno='[{"retorno":"ERR","dados":"","erro":"ARQUIVO CNAB COM TITULOS ANEXADO!"}]';                
          };    
        };  
        ///////////////////////
        // Alterando  a empresa
        ///////////////////////
        if( $lote[0]->rotina=="altEmpresa" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("altEmpresa",$lote[0]->login);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"script":'.$retSql["script"].',"erro":"'.$retSql["erro"].'"}]';
        };  
        ////////////////////////////
        // Excluindo titulos ao cnab
        ////////////////////////////
        if( $lote[0]->rotina=="delcnab" ){
          $codbnc     = $lote[0]->codbnc;
          $codcnab    = $lote[0]->codcnab;
          $lanctoini  = $lote[0]->lanctoini;
          $lanctofim  = $lote[0]->lanctofim;
          $valor      = 0;
          
          $objReg = $lote[0]->REGISTRO;
          $in="";    
          foreach ( $objReg as $reg ){
            $in.=(int)$reg->lancto.",";
          };  
          $in  =substr($in,0,(strlen($in)-1));	
          $sql ="SELECT PGR_CODPTP";
          $sql.="       ,CONVERT(VARCHAR(10),PGR_DATAPAGA,127) AS PGR_DATAPAGA";
          $sql.="       ,PGR_LOTECNAB";
          $sql.="       ,PGR_LANCTO";          
          $sql.="       ,PGR_VLRLIQUIDO";          
          $sql.="  FROM PAGAR A";
          $sql.=" WHERE (PGR_LANCTO BETWEEN '".$lote[0]->lanctoini."' AND '".$lote[0]->lanctofim."')";          
          $sql.="   AND (PGR_LANCTO IN(".$in."))";
          $sql.="   AND (PGR_LOTECNAB=".$lote[0]->codcnab.")";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);

          if( $retCls['retorno']=="OK" ){
            $erro = "ok";            
            $tbl  = $retCls["dados"];
            foreach( $tbl as $cmp ){
              $sql="UPDATE VPAGAR SET PGR_LOTECNAB=0,PGR_CODUSR=".$_SESSION["usr_codigo"]." WHERE PGR_LANCTO=".$cmp["PGR_LANCTO"];
              $valor+=$cmp["PGR_VLRLIQUIDO"];
              array_push($arrUpdt,$sql);
            };
            ///////////////////////////////////////////////////////////////////
            // Valor total recebido do JS deve ser o mesmo valor do foreach Php
            ///////////////////////////////////////////////////////////////////
            if( $lote[0]->vlrtotal <> $valor ){
              $erro="VALOR RECEBIDO ".$lote[0]->vlrtotal." DIVERGE DO VALOR SERVIDOR ".$valor;
            };  
            if( $erro=="ok" ){
              $sql="UPDATE CNAB SET CNB_VLREXCLUIDO=(CNB_VLREXCLUIDO+".$valor.") WHERE CNB_CODIGO=".$codcnab;    
              array_push($arrUpdt,$sql);
              $atuBd = true;
            } else {
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';    
            };
          };  
        };  
        ////////////////////////////
        // Adicionar titulos ao cnab
        ////////////////////////////
        if( $lote[0]->rotina=="addcnab" ){
          $codbnc     = $lote[0]->codbnc;
          $codcnab    = $lote[0]->codcnab;
          $lanctoini  = $lote[0]->lanctoini;
          $lanctofim  = $lote[0]->lanctofim;
          $valor      = 0;
          
          $objReg = $lote[0]->REGISTRO;
          $in="";    
          foreach ( $objReg as $reg ){
            if( ($lanctoini==0) and ($lanctofim==0) ){
              $lanctoini=$reg->lancto;
              $lanctofim=$reg->lancto;
            } else {
              if( $lanctoini>$reg->lancto )
                $lanctoini=$reg->lancto;
              if( $lanctofim<$reg->lancto )
                $lanctofim=$reg->lancto;
            };  
            $in.=(int)$reg->lancto.",";
          };  
          $in  =substr($in,0,(strlen($in)-1));	
          $sql ="SELECT PGR_CODPTP";
          $sql.="       ,CONVERT(VARCHAR(10),PGR_DATAPAGA,127) AS PGR_DATAPAGA";
          $sql.="       ,PGR_LOTECNAB";
          $sql.="       ,PGR_LANCTO";          
          $sql.="       ,PGR_VLRLIQUIDO";          
          $sql.="  FROM PAGAR A";
          $sql.=" WHERE (PGR_LANCTO IN(".$in."))";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno']=="OK" ){
            $tbl  = $retCls["dados"];
            $erro = "ok";
            foreach( $tbl as $cmp ){
              if( $cmp["PGR_CODPTP"] <> "CR" )    $erro="LANCTO ".$cmp["PGR_LANCTO"]." ACEITO APENAS CR";
              if( $cmp["PGR_DATAPAGA"] <> "" )    $erro="LANCTO ".$cmp["PGR_LANCTO"]." ACEITO APENAS TITULO SEM QUITACAO";
              if( (int)$cmp["PGR_LOTECNAB"] > 0 ) $erro="LANCTO ".$cmp["PGR_LANCTO"]." JA ANEXADO AO UM ARQUIVO CNAB";
              if( $erro=="ok" ){
                $sql="UPDATE VPAGAR SET PGR_CODBNC=".$codbnc.",PGR_LOTECNAB=".$codcnab.",PGR_CODUSR=".$_SESSION["usr_codigo"]." WHERE PGR_LANCTO=".$cmp["PGR_LANCTO"];
                $valor+=$cmp["PGR_VLRLIQUIDO"];
                array_push($arrUpdt,$sql);
              };
            };
            ///////////////////////////////////////////////////////////////////
            // Valor total recebido do JS deve ser o mesmo valor do foreach Php
            ///////////////////////////////////////////////////////////////////
            if( $lote[0]->vlrtotal <> $valor ){
              $erro="VALOR RECEBIDO ".$lote[0]->vlrtotal." DIVERGE DO VALOR SERVIDOR ".$valor;
            };  
            if( $erro=="ok" ){
              $sql="UPDATE CNAB SET CNB_VLRARQUIVO=(CNB_VLRARQUIVO+".$valor."),CNB_LANCTOINI=".$lanctoini.",CNB_LANCTOFIM=".$lanctofim." WHERE CNB_CODIGO=".$codcnab;    
              array_push($arrUpdt,$sql);
              
              $data='[{"qtos":"'.count($tbl).'"
                      ,"ini":"'.$lanctoini.'"
                      ,"fim":"'.$lanctofim.'"
                      ,"erro":""}]';
                      
              $atuBd = true;
            } else {
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';    
            };
          };  
        };  
        /////////////////////////
        // Novo arquivo
        /////////////////////////
        if( $lote[0]->rotina=="novoarquivo" ){
          $codbnc=$lote[0]->codbnc;
          $sql ="INSERT INTO CNAB(";
          $sql.="CNB_CODBNC";
          $sql.=",CNB_ATIVO) VALUES(";
          $sql.="'$codbnc'";                          // CNB_CODBNC
          $sql.=",'S'";                               // CNB_ATIVO
          $sql.=")";
          array_push($arrUpdt,$sql);  
          $atuBd = true;          
        };  
        /////////////////////////
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="buscabanco" ){
          $sql ="SELECT BNC_CODIGO";
          $sql.="       ,BNC_NOME";
          $sql.="  FROM BANCO WITH(NOLOCK)";
          $sql.=" WHERE (BNC_CODEMP=".$_SESSION["emp_codigo"].")"; 
          $sql.="   AND (BNC_CNAB='S')";
          $sql.="   AND (BNC_ATIVO='S')";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno']=="OK" ){
            if( $retCls["qtos"]==0 ){
              $retorno='[{"retorno":"ZERO","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
            } else { 
              $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
            };  
          };
        };  
        //  
        //
        if( $lote[0]->rotina=="filtrar" ){
          $sql ="SELECT A.CNB_CODIGO";
          $sql.="       ,A.CNB_CODBNC";
          $sql.="       ,BNC.BNC_NOME";          
          $sql.="       ,BNC.BNC_CODBCD";
          $sql.="       ,CONVERT(VARCHAR(10),A.CNB_DTCADASTRO,127) AS CNB_DTCADASTRO";
          $sql.="       ,A.CNB_VLRARQUIVO";
          $sql.="       ,A.CNB_VLREXCLUIDO";
          $sql.="       ,A.CNB_VLRBAIXADO";
          $sql.="       ,A.CNB_ARQUIVO";
          $sql.="       ,CONVERT(VARCHAR(10),A.CNB_DTARQUIVO,127) AS CNB_DTARQUIVO";
          //$sql.="       ,A.CNB_ATIVO";
          $sql.="       ,CASE WHEN A.CNB_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CNB_ATIVO";          
          $sql.="       ,A.CNB_LANCTOINI";
          $sql.="       ,A.CNB_LANCTOFIM";
          $sql.="  FROM CNAB A";
          $sql.="  LEFT OUTER JOIN BANCO BNC ON A.CNB_CODBNC=BNC.BNC_CODIGO";
          $sql.=" WHERE (A.CNB_CODBNC=".$lote[0]->codbnc.")";                    
          $sql.="   AND (A.CNB_ATIVO='S')";  
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno']=="OK" ){
            if( $retCls["qtos"]==0 ){
              $retorno='[{"retorno":"ZERO","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
            } else { 
              $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
            };  
          };
        }; 
        ////////////////////////////////////////////////
        //             Adicionar lancamentos          //
        ////////////////////////////////////////////////
        if( $lote[0]->rotina=="cnabadd" ){
          $sql= "SELECT A.PGR_LANCTO";
          $sql.="       ,A.PGR_DOCTO AS DOCTO";
          $sql.="       ,FVR.FVR_APELIDO";
          $sql.="       ,A.PGR_CODFC AS FC";
          $sql.="       ,A.PGR_CODTD AS TD";
          $sql.="       ,SUBSTRING(dbo.fnc_Data(A.PGR_VENCTO),1,10) AS VENCTO";
          $sql.="       ,(A.PGR_VLRLIQUIDO*A.PGR_INDICE) AS VALOR";
          $sql.="       ,SUBSTRING(BNC.BNC_NOME,1,30) AS BANCO_FINANCEIRO";                    
          $sql.="  FROM PAGAR A";
          $sql.="  LEFT OUTER JOIN BANCO BNC ON A.PGR_CODBNC=BNC.BNC_CODIGO";          
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PGR_CODFVR=FVR.FVR_CODIGO";          
          $sql.=" WHERE (PGR_VENCTO BETWEEN '".$lote[0]->dataini."' AND '".$lote[0]->datafim."')";
          $sql.="   AND (A.PGR_CODPTP='CR')";
          $sql.="   AND (A.PGR_DATAPAGA IS NULL)";
          $sql.="   AND (A.PGR_LOTECNAB=0)";          
          $sql.="   AND (PGR_CODEMP=".$_SESSION["emp_codigo"].")";     
          $sql.=" ORDER BY PGR_VENCTO"; 
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        ////////////////////////////////////////////////
        //              Remove lancamentos            //
        ////////////////////////////////////////////////
        if( $lote[0]->rotina=="cnabdel" ){
          $sql= "SELECT A.PGR_LANCTO";
          $sql.="       ,A.PGR_DOCTO AS DOCTO";
          $sql.="       ,FVR.FVR_APELIDO";
          $sql.="       ,A.PGR_CODFC AS FC";
          $sql.="       ,A.PGR_CODTD AS TD";
          $sql.="       ,SUBSTRING(dbo.fnc_Data(A.PGR_VENCTO),1,10) AS VENCTO";
          $sql.="       ,(A.PGR_VLRLIQUIDO*A.PGR_INDICE) AS VALOR";
          $sql.="       ,SUBSTRING(BNC.BNC_NOME,1,30) AS BANCO_FINANCEIRO";                    
          $sql.="  FROM PAGAR A";
          $sql.="  LEFT OUTER JOIN BANCO BNC ON A.PGR_CODBNC=BNC.BNC_CODIGO";          
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PGR_CODFVR=FVR.FVR_CODIGO";          
          $sql.=" WHERE (PGR_LANCTO BETWEEN '".$lote[0]->lanctoini."' AND '".$lote[0]->lanctofim."')";
          $sql.="   AND (A.PGR_LOTECNAB=".$lote[0]->codcnab.")";          
          $sql.="   AND (PGR_CODEMP=".$_SESSION["emp_codigo"].")";     
          $sql.=" ORDER BY PGR_VENCTO"; 
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
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
            ///////////////////////////////////////////////////////
            // Aqui retorno o padrao se nao tiver preenchido o data
            ///////////////////////////////////////////////////////
            if( $data=="" ){
              $retorno='[{"retorno":"OK","dados":"","erro":"'.count($arrUpdt).' REGISTRO(s) ATUALIZADO(s)!"}]'; 
            } else {
              $retorno='[{"retorno":"OK","dados":'.json_encode($data).',"erro":""}]';   
            };  
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
    <title>Cnab</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="js/jsCopiaDoc2017.js"></script>            
    <script>      
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        $doc("spnEmpApelido").innerHTML=jsPub[0].emp_apelido;
        buscaBanco();
        document.getElementById("cbBanco").focus();
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsCnb={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "CODIGO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"tamGrd"         : "4em"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "CODBNC"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "BANCO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "30em"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "COD"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "EMISSAO"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "VLREXCLUIDO"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "VLRBAIXA"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "ARQUIVO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"padrao":0}
            ,{"id":10,"labelCol"       : "DTARQUIVO"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "ATIVO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "INI"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"tamGrd"         : "5em"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "FIM"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"tamGrd"         : "5em"
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
             {"texto":"Cadastrar" 		,"name":"horCadastrar"      ,"onClick":"7"  ,"enabled":true	,"imagem":"fa fa-plus"          
                                      ,"popover":{title:"Ajuda",texto:"Adiciona um novo arquivo para seleção de titulos"}   }
            ,{"texto":"Excluir" 		  ,"name":"horExcluir"        ,"onClick":"7"  ,"enabled":true	,"imagem":"fa fa-minus"          
                                      ,"popover":{title:"Ajuda",texto:"Excluir arquivo se não tiver nenhum titulo anexado"} } 						
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmCnb"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCnb"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCnb"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnCnb"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblCnb"                  // Nome da table
          ,"prefixo"        : "cnb"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "130em"                   // Tamanho da table
          ,"height"         : "40em"                    // Altura da table
          ,"nChecks"        : false                     // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "CNAB"                    // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "*"                       // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objCnb === undefined ){  
          objCnb=new clsTable2017("objCnb");
        };  
        /////////////////////////////////////////////////
        //        Objeto clsTable2017 TITULOS          //
        /////////////////////////////////////////////////
        jsAdd={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"tamGrd"         : "0em"             
                      ,"padrao":1}            
            ,{"id":1  ,"labelCol"       : "LANCTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "DOCTO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "FAVORECIDO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "40"											
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "FC"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "10"											
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "TD"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "10"											
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "VENCTO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"											
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2"                       
                      ,"somarImp"       : "S"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "BANCO_FINANCEIRO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "50"
                      ,"padrao":0}
                      
          ]
          , 
          "botoesH":[
             {"texto":"Adicionar" 	,"name":"horAddCnab"      ,"onClick":"7"  ,"enabled":true	,"imagem":"fa fa-plus"          
                                    ,"popover":{title:"Ajuda",texto:"Adiciona titulos selecionados ao arquivo CNAB"}	    } 						
            ,{"texto":"Remover" 	  ,"name":"horDelCnab"      ,"onClick":"7"  ,"enabled":true	,"imagem":"fa fa-plus"          
                                    ,"popover":{title:"Ajuda",texto:"Remove titulos selecionados do arquivo CNAB"}	    } 						
            ,{"texto":"Excel"       ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Imprimir"    ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"        }                    
          ] 
          ,"registros"      : []                   // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                 // Opção para gerar ajuda no formato popUp(Hint)          
          ,"checarTags"     : "S"                  // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmAdd"             // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaAdd"          // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmAdd"             // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "cllAdd"             // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "cllAdd"             // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)          
          ,"tbl"            : "tblAdd"             // Nome da table
          ,"prefixo"        : "ch"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"position"       : "relative"          
          ,"width"          : "90em"               // Tamanho da table
          ,"height"         : "60em"               // Altura da table
          ,"nChecks"        : true                 // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "sim"                // Se tiver menu esquerdo
          ,"relTitulo"      : "TITULOS"            // Titulo do relatório
          ,"relOrientacao"  : "R"                  // Paisagem ou retrato
          ,"relFonte"       : "7"                  // Fonte do relatório
          ,"indiceTable"    : "*"                  // Indice inicial da table
          ,"tamBotao"       : "12"                 // Tamanho botoes defalt 12 [12/25/50/75/100]
        }; 
        if( objAdd === undefined ){  
          objAdd=new clsTable2017("objAdd");
        }; 
        if( objDel === undefined ){  
          objDel=new clsTable2017("objDel");
        }; 
        /////////////////////////////
        // Criando uma copia de jsAdd
        /////////////////////////////
        jsDel=JSON.parse(JSON.stringify(jsAdd));  // Este eh para naum copiar por referencia( senaum altera o original )
        jsDel.div             = "frmDel";
        jsDel.divFieldSet     = "tabelaDel";
        jsDel.form            = "frmDel";
        jsDel.divModal        = "cllDel";
        jsDel.divModalDentro  = "cllDel";
        jsDel.tbl             = "tblDel";
        jsDel.botoesH[0].enabled=false
        jsAdd.botoesH[1].enabled=false
        //
        //
        objCnb.montarHtmlCE2017(jsCnb); 
        objAdd.montarHtmlCE2017(jsAdd);
        objDel.montarHtmlCE2017(jsDel);        
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsCnb,["ARQUIVO","ATIVO","BANCO","COD","CODIGO","DTARQUIVO","EMISSAO","FIM","INI","VALOR","VLRBAIXA","VLREXCLUIDO"]);
      });
      var objCnb;                      // Obrigatório para instanciar o JS TFormaCob
      var jsCnb;                       // Obj principal da classe clsTable2017
      var objAdd;                     // Obrigatório para instanciar o JS TFormaCob
      var jsAdd;                      // Obj principal da classe clsTable2017
      var objDel;                     // Obrigatório para instanciar o JS TFormaCob
      var jsDel;                      // Obj principal da classe clsTable2017
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario      
      var arrCol;                     // Converter os nomes das coluna na table      
      var clsChecados;                // Classe para montar Json
      var chkds;                      // Guarda todos registros checados na table 
      var contMsg   = 0;              // contador para mensagens      
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d05);
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado
      //
      ////////////////////////////////
      // Buscando os banco
      ////////////////////////////////
      function buscaBanco(){  
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "buscabanco"        );
        clsJs.add("login"       , jsPub[0].usr_login  );
        fd = new FormData();
        fd.append("cnab" , clsJs.fim());
        msg     = requestPedido(arqLocal,fd); 
        retPhp  = JSON.parse(msg);
        if( ["OK","ZERO"].indexOf(retPhp[0].retorno) != -1 ){
          $doc("cbBanco").innerHTML="";
          let ceOpt;
          ceOpt       = document.createElement("option");
          ceOpt.value = "0";
          ceOpt.text  = "INFORME";
          document.getElementById("cbBanco").appendChild(ceOpt);
          if( retPhp[0].retorno != "ZERO" ){
            let tbl=retPhp[0]["dados"];
            //
            tbl.forEach(function(campo){
              ceOpt       = document.createElement("option");
              ceOpt.value = campo.BNC_CODIGO;
              ceOpt.text  = campo.BNC_NOME
              document.getElementById("cbBanco").appendChild(ceOpt);
            });
          };
        };
      };
      ////////////////////
      // Filtrar registros
      ////////////////////
      function btnFiltrarClick() { 
        try{
          if( parseInt($doc("cbBanco").value)==0 )
            throw "FAVOR SELECIONAR UM BANCO VALIDO!";
          
          clsJs   = jsString("lote");  
          clsJs.add("rotina"      , "filtrar"                         );
          clsJs.add("login"       , jsPub[0].usr_login                );
          clsJs.add("codemp"      , jsPub[0].emp_codigo               );
          clsJs.add("codbnc"      , jsConverte("#cbBanco").inteiro()  );
          fd = new FormData();
          fd.append("cnab" , clsJs.fim());
          msg     = requestPedido(arqLocal,fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            //////////////////////////////////////////////////////////////////////////////////
            // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
            // Campo obrigatório se existir rotina de manutenção na table devido Json       //
            // Esta rotina não tem manutenção via classe clsTable2017                       //
            // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
            //////////////////////////////////////////////////////////////////////////////////
            jsCnb.registros=objCnb.addIdUnico(retPhp[0]["dados"]);
            objCnb.montarBody2017();
          };  
          if( retPhp[0].retorno == "ZERO" )
            gerarMensagemErro("catch","NENHUM REGISTRO LOCALIZADO PARA ESTA OPCAO",{cabec:"Aviso"});          
          
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ////////////////////
      // Novo arquivo
      ////////////////////
      function horCadastrarClick() { 
        try{
          if( parseInt($doc("cbBanco").value)==0 )
            throw "FAVOR SELECIONAR UM BANCO VALIDO!";
          
          clsJs   = jsString("lote");  
          clsJs.add("rotina"      , "novoarquivo"                     );
          clsJs.add("login"       , jsPub[0].usr_login                );
          clsJs.add("codemp"      , jsPub[0].emp_codigo               );
          clsJs.add("codbnc"      , jsConverte("#cbBanco").inteiro()  );
          fd = new FormData();
          fd.append("cnab" , clsJs.fim());
          msg     = requestPedido(arqLocal,fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            btnFiltrarClick();
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      //////////////////////////////////////
      // Adicionando titulos ao arquivo CNAB
      //////////////////////////////////////
      function horAddCnabClick(){
        try{
          if( parseInt($doc("cbBanco").getAttribute("data-codcnab"))<=0 )
            throw "CODIGO DO CNAB INVALIDO PARA ADICIONAR TITULOS FINANCEIROS!";
          
          clsChecados = objAdd.gerarJson("n");
          chkds       = clsChecados.gerar();
          /////////////////////////////
          // Pegando somente os lanctos
          /////////////////////////////
          let clsReg = jsString("registro");
          let vlrTotal=0;
          clsReg.principal(false);
          chkds.forEach(function(reg){
            clsReg.add("lancto",reg.LANCTO                    );
            clsReg.add("valor",jsConverte(reg.VALOR).dolar()  );
            vlrTotal+=jsConverte(reg.VALOR).dolar(true);
          });
          let registro = clsReg.fim();          
          
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para adicionar titulos ao CNAB
          // codCnab usado tb no forEach
          //////////////////////////////////////////////////////////////
          let codCnab=parseInt($doc("cbBanco").getAttribute("data-codcnab"));
          clsJs=jsString("lote");
          chkds.forEach(function(reg){
            clsJs.add("rotina"      , "addcnab"                                                 );            
            clsJs.add("login"       , jsPub[0].usr_login                                        );            
            clsJs.add("codbnc"      , jsConverte("#cbBanco").inteiro()                          );
            clsJs.add("codcnab"     , codCnab                                                   );          
            clsJs.add("lanctoini"   , parseInt($doc("cbBanco").getAttribute("data-lanctoini"))  );                      
            clsJs.add("lanctofim"   , parseInt($doc("cbBanco").getAttribute("data-lanctofim"))  );
            clsJs.add("vlrtotal"    , vlrTotal                                                  );  //Vlr total enviado deve ser igual total foreach php
            clsJs.add("REGISTRO"    , registro                                                  );
          });  
          ///////////////////////////////////////////
          // Atualizando os data para proxima chamada
          ///////////////////////////////////////////
          $doc("cbBanco").getAttribute("data-codcnab",0   );
          $doc("cbBanco").getAttribute("data-lanctoini",0 );
          $doc("cbBanco").getAttribute("data-lanctofim",0 );
          /////////////////////////////////////////////////////////////////
          // Passando as colunas que vou precisas para atualizar esta table
          /////////////////////////////////////////////////////////////////
          fd = new FormData();
          fd.append("cnab" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno!="OK" ){
            gerarMensagemErro('catch',retPhp[0].erro,{cabec:"Aviso"});  
          } else {  
            msg=JSON.parse(retPhp[0]["dados"]);
            /////////////////////////////////////////////////////////////////////    
            // Se der tudo certo atualizo a grade e fecho os titulos para selecao
            /////////////////////////////////////////////////////////////////////
            tblCnb.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              if( codCnab==jsConverte(row.cells[objCol.CODIGO].innerHTML).inteiro() ){  
                row.cells[objCol.VALOR].innerHTML = jsConverte((jsConverte(row.cells[objCol.VALOR].innerHTML).dolar(true) + vlrTotal).toFixed(2)).real();
                row.cells[objCol.INI].innerHTML = jsConverte(msg[0]["ini"]).emZero(6);
                row.cells[objCol.FIM].innerHTML = jsConverte(msg[0]["fim"]).emZero(6);
              };
            });    
            abreAdd.hide();            
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };  
      //////////////////////////////////////
      // Adicionando titulos ao arquivo CNAB
      //////////////////////////////////////
      function horDelCnabClick(){
        try{
          if( parseInt($doc("cbBanco").getAttribute("data-codcnab"))<=0 )
            throw "CODIGO DO CNAB INVALIDO PARA ADICIONAR TITULOS FINANCEIROS!";
          
          clsChecados = objDel.gerarJson("n");
          chkds       = clsChecados.gerar();
          /////////////////////////////
          // Pegando somente os lanctos
          /////////////////////////////
          let clsReg = jsString("registro");
          let vlrTotal=0;
          clsReg.principal(false);
          chkds.forEach(function(reg){
            clsReg.add("lancto",reg.LANCTO                    );
            clsReg.add("valor",jsConverte(reg.VALOR).dolar()  );
            vlrTotal+=jsConverte(reg.VALOR).dolar(true);
          });
          let registro = clsReg.fim();          
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para adicionar titulos ao CNAB
          // codCnab usado tb no forEach
          //////////////////////////////////////////////////////////////
          let codCnab=parseInt($doc("cbBanco").getAttribute("data-codcnab"));
          clsJs=jsString("lote");
          chkds.forEach(function(reg){
            clsJs.add("rotina"      , "delcnab"                                                 );
            clsJs.add("login"       , jsPub[0].usr_login                                        );
            clsJs.add("codbnc"      , jsConverte("#cbBanco").inteiro()                          );
            clsJs.add("lanctoini"   , parseInt($doc("cbBanco").getAttribute("data-lanctoini"))  );                      
            clsJs.add("lanctofim"   , parseInt($doc("cbBanco").getAttribute("data-lanctofim"))  );
            clsJs.add("codcnab"     , codCnab                                                   );
            clsJs.add("vlrtotal"    , vlrTotal                                                  );  //Vlr total enviado deve ser igual total foreach php
            clsJs.add("REGISTRO"    , registro                                                  );
          });  
          /////////////////////////////////////////////////////////////////
          // Passando as colunas que vou precisas para atualizar esta table
          /////////////////////////////////////////////////////////////////
          fd = new FormData();
          fd.append("cnab" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno!="OK" ){
            gerarMensagemErro('catch',retPhp[0].erro,{cabec:"Aviso"});  
          } else {  
            /////////////////////////////////////////////////////////////////////    
            // Se der tudo certo atualizo a grade e fecho os titulos para selecao
            /////////////////////////////////////////////////////////////////////
            tblCnb.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              if( codCnab==jsConverte(row.cells[objCol.CODIGO].innerHTML).inteiro() ){  
                row.cells[objCol.VLREXCLUIDO].innerHTML = jsConverte((jsConverte(row.cells[objCol.VLREXCLUIDO].innerHTML).dolar(true) + vlrTotal).toFixed(2)).real();
              };
            });    
            abreDel.hide();            
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      //////////////////////////////////////
      // Excluindo um arquivo
      //////////////////////////////////////
      function horExcluirClick(){
        try{
          clsChecados = objCnb.gerarJson("1");
          chkds       = clsChecados.gerar();
          clsJs=jsString("lote");
          chkds.forEach(function(reg){
            clsJs.add("rotina"      , "excarquivo"        );
            clsJs.add("login"       , jsPub[0].usr_login  );
            clsJs.add("lanctoini"   , chkds[0].INI        );                      
            clsJs.add("lanctofim"   , chkds[0].FIM        );
            clsJs.add("codcnab"     , chkds[0].CODIGO     );
          });  
          /////////////////////////////////////////////////////////////////
          // Passando as colunas que vou precisas para atualizar esta table
          /////////////////////////////////////////////////////////////////
          fd = new FormData();
          fd.append("cnab" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno!="OK" ){
            gerarMensagemErro('catch',retPhp[0].erro,{cabec:"Aviso"});  
          } else {  
            msg=0;
            tblCnb.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr,tam) {     
              if(row.cells[0].children[0].checked){
                jsCnb.registros.splice(msg,1);
              };
              msg++;
            });
            tblCnb.apagaChecados();
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      //////////////////
      // Alterar empresa
      //////////////////
      function altEmpresa(){
        try{          
          clsJs   = jsString("lote");  
          clsJs.add("rotina"  , "altEmpresa"                );
          clsJs.add("login"   , jsPub[0].usr_login          );
          fd = new FormData();
          fd.append("cnab"    , clsJs.fim());
          msg     = requestPedido(arqLocal,fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            janelaDialogo(
              { height          : "25em"
                ,body           : "16em"
                ,left           : "500px"
                ,top            : "60px"
                ,tituloBarra    : "Alterar empresa"
                ,width          : "43em"
                ,fontSizeTitulo : "1.8em"             //  padrao 2em que esta no css
                ,code           : retPhp[0]["dados"]  //  clsCode.fim()
              }
            );  
            let scr = document.createElement('script');
            scr.innerHTML = retPhp[0]["script"];
            document.getElementsByTagName('body')[0].appendChild(scr);        
          };
        }catch(e){
          gerarMensagemErro('catch',e.message,{cabec:"Erro"});
        };
      };  
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="fncImprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela."><i class="indFa fa-print"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="fncExcel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela."><i class="indFa fa-file-excel-o"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="altEmpresa();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Alterar empresa."><i class="indFa fa-spinner"></i>
          </div>
        </section>
      </aside>

      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;margin-bottom:0px;height:50px;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">Cnab</span>
              <span id="spnEmpApelido" class="infoBoxLabel"></span>
            </div>
          </div>
        </div>  

        <div id="indDivInforme" class="indTopoInicio80" style="padding-top:2px;">
          <div class="campotexto campo25">
            <select class="campo_input_combo" 
                    data-codcnab="0" 
                    data-lanctoini="0" 
                    data-lanctofim="0" 
                    id="cbBanco">
            </select>
            <label class="campo_label campo_required" for="cbBanco">BANCO</label>
          </div>
          <div id="btnFiltrar" onClick="btnFiltrarClick();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-check"> Filtrar</i></div>
          <div id="btnFechar" onClick="window.close();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-close"> Fechar</i></div>
        </div>
      </div>
      <section>
        <section id="sctnCnb">
        </section>  
      </section>
      <form method="post"
            name="frmCnb"
            id="frmCnb"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/>      
      </form>  
    </div>
    <!--
    Buscando o historico do OS
    -->
    <section id="collapseSectionAdd" class="section-collapse">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a id="aLabel" class="btn btn-default disabled">Títulos</a>
            <button id="abreAdd"  class="btn btn-primary" 
                                  data-toggle="collapse" 
                                  data-target="#evtAbreAdd" 
                                  aria-expanded="true" 
                                  aria-controls="evtAbreAdd" 
                                  type="button">Adicionar</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreAdd" aria-expanded="false" role="presentation">
          <div id="cllAdd" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblAdd" class="alert-info">Adicionar titulo</label>
          </div>
        </div>
      </div>
    </section>
    
    <section id="collapseSectionDel" class="section-collapse" style="margin-left:1px;">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <!--<a class="btn btn-default disabled">Deste contrato</a>-->
            <button id="abreDel" class="btn btn-primary" 
                                data-toggle="collapse" 
                                data-target="#evtAbreDel" 
                                aria-expanded="true" 
                                aria-controls="evtAbreDel" 
                                type="button">Adicionados</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreDel" aria-expanded="false" role="presentation">
          <div id="cllDel" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblDel" class="alert-info">Mostrando todas os titulo adicionados ao arquivo</label>
          </div>          
        </div>
      </div>
    </section>
    <script>
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                                     PopUp ADD ( Adicionar titulos )                                        //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreAdd  = new Collapse($doc('abreAdd'));
      abreAdd.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreAdd = document.getElementById('evtAbreAdd');
      collapseAbreAdd.addEventListener('show.bs.collapse', function(el){ 
        if( document.getElementById('evtAbreDel').getAttribute("aria-expanded")=="false" ){
          try{
            chkds=objCnb.gerarJson("1").gerar();
            clsJs=jsString("lote");                      
            clsJs.add("rotina"      , "cnabadd"                                   );  // Detalhe dos lanctos
            clsJs.add("login"       , jsPub[0].usr_login                          );
            clsJs.add("dataini"     , jsDatas(2).retMMDDYYYY()                    );  // Venctos com +2 dias de hoje
            clsJs.add("datafim"     , jsDatas(30).retMMDDYYYY()                   );  // Venctos ateh
            /////////////////////////////////////////////////////////
            // Guardando aqui o codigo do CNAB para adicionar/excluir
            /////////////////////////////////////////////////////////
            $doc("cbBanco").setAttribute("data-codcnab",chkds[0].CODIGO);
            $doc("cbBanco").setAttribute("data-lanctoini",chkds[0].INI);
            $doc("cbBanco").setAttribute("data-lanctofim",chkds[0].FIM);
            //////////////////////
            // Enviando para o Php
            //////////////////////
            var fd = new FormData();
            fd.append("cnab" , clsJs.fim());
            msg=requestPedido(arqLocal,fd); 
            retPhp=JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});              
              abreAdd.hide();
            } else {  
              jsAdd.registros=objAdd.addIdUnico(retPhp[0]["dados"]);
              objAdd.montarBody2017();
              $doc("lblAdd").innerHTML="Mostrando lançamentos vencto de <b>"+jsDatas(2).retDDMMYYYY()+"</b>  até <b>"+jsDatas(30).retDDMMYYYY()+"</b> - CNAB <b>"+chkds[0].BANCO+"</b>";
              $doc("cllAdd").style.height = (parseInt((document.getElementById("dPaifrmAdd").style.height).slice(0,-2))+2)+"em";
              abreAdd.status="ok";
            };  
          }catch(e){
            abreAdd.status="err";
            gerarMensagemErro("catch",e,{cabec:"Erro"});
          };
        } else {
          abreAdd.status="err";  
        };  
      },false);
      collapseAbreAdd.addEventListener('shown.bs.collapse', function(){ 
        if( abreAdd.status=="err" )
          abreAdd.hide();
      },false);
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                            PopUp DEL ( ver titulos adicionados e poder remover )                           //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreDel  = new Collapse($doc('abreDel'));
      abreDel.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreDel = document.getElementById('evtAbreDel');
      collapseAbreDel.addEventListener('show.bs.collapse', function(el){ 
        if( document.getElementById('evtAbreAdd').getAttribute("aria-expanded")=="false" ){      
          try{
            chkds=objCnb.gerarJson("1").gerar();
            clsJs=jsString("lote");                      
            clsJs.add("rotina"      , "cnabdel"                                   );  // Detalhe dos lanctos
            clsJs.add("login"       , jsPub[0].usr_login                          );
            clsJs.add("codcnab"     , parseInt(chkds[0].CODIGO)                   );
            clsJs.add("lanctoini"   , parseInt(chkds[0].INI)                      );  // Primeiro lancto
            clsJs.add("lanctofim"   , parseInt(chkds[0].FIM)                      );  // Ultimo lancto
            /////////////////////////////////////////////////////////
            // Guardando aqui o codigo do CNAB para adicionar/excluir
            /////////////////////////////////////////////////////////
            $doc("cbBanco").setAttribute("data-codcnab",chkds[0].CODIGO);
            $doc("cbBanco").setAttribute("data-lanctoini",chkds[0].INI);
            $doc("cbBanco").setAttribute("data-lanctofim",chkds[0].FIM);
            //////////////////////
            // Enviando para o Php
            //////////////////////
            var fd = new FormData();
            fd.append("cnab" , clsJs.fim());
            msg=requestPedido(arqLocal,fd); 
            retPhp=JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});              
              abreDel.hide();
            } else {  
              jsDel.registros=objDel.addIdUnico(retPhp[0]["dados"]);
              objDel.montarBody2017();
              $doc("lblDel").innerHTML="Mostrando lançamentos adicionados";
              $doc("cllDel").style.height = (parseInt((document.getElementById("dPaifrmDel").style.height).slice(0,-2))+2)+"em";
              abreDel.status="ok";
            };  
          }catch(e){
            abreDel.status="err";
            gerarMensagemErro("catch",e,{cabec:"Erro"});
          };
        } else {
          abreDel.status="err";  
        }  
      },false);
      collapseAbreDel.addEventListener('shown.bs.collapse', function(){ 
        if( abreDel.status=="err" )
          abreDel.hide();
      },false);
    </script>
  </body>
</html>