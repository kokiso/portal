<?php
  session_start();
  if( isset($_POST["cpcr"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/dataCompetencia.class.php");      

      $clsCompet  = new dataCompetencia();    
      $vldr       = new validaJSon();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["cpcr"]);
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
        /////////////////////////
        // Registro do sistema
        /////////////////////////
        if( $lote[0]->rotina=="regsistema" ){
          $objReg = $lote[0]->REGISTRO;
          foreach ( $objReg as $reg ){
            $sql="UPDATE VPAGAR SET PGR_REG='S',PGR_CODUSR=".$_SESSION["usr_codigo"]." WHERE PGR_LANCTO=".$reg->lancto;
            array_push($arrUpdt,$sql);            
          };
          $atuBd = true;
        };
        /////////////////////////
        // Copia de documento
        /////////////////////////
        if( $lote[0]->rotina=="copiadocto" ){
          $sql="";
          $sql.="SELECT PGR.PGR_MASTER";
          $sql.="       ,PGR.PGR_LANCTO";
          $sql.="       ,PGR.PGR_CODPTP";
          $sql.="       ,PTP.PTP_NOME";          
          $sql.="       ,PGR.PGR_DOCTO";
          $sql.="       ,PGR.PGR_CODFVR";
          $sql.="       ,FVR.FVR_APELIDO";
          $sql.="       ,FVR.FVR_NOME";
          $sql.="       ,FVR.FVR_CNPJCPF";
          $sql.="       ,FVR.FVR_FISJUR";
          $sql.="       ,PGR.PGR_CODPTT";
          $sql.="       ,PTT.PTT_NOME";
          $sql.="       ,PGR.PGR_PARCELA";
          $sql.="       ,TD.TD_NOME";
          $sql.="       ,FC.FC_NOME";
          $sql.="       ,CONVERT(VARCHAR(10),PGR.PGR_DTDOCTO,127) AS PGR_DTDOCTO";                    
          $sql.="       ,CONVERT(VARCHAR(10),PGR.PGR_VENCTO,127) AS PGR_VENCTO";                              
          $sql.="       ,CONVERT(VARCHAR(10),PGR.PGR_DTMOVTO,127) AS PGR_DTMOVTO";                              
          $sql.="       ,PGR.PGR_CODBNC";
          $sql.="       ,BNC.BNC_NOME";
          $sql.="       ,PGR.PGR_VLREVENTO";
          $sql.="       ,PGR.PGR_VLRPARCELA";          
          $sql.="       ,PGR.PGR_VLRMULTA";
          $sql.="       ,PGR.PGR_VLRDESCONTO";
          $sql.="       ,PGR.PGR_VLRRETENCAO";
          $sql.="       ,PGR.PGR_VLRLIQUIDO";
          $sql.="       ,PGR.PGR_VLRCOFINS";
          $sql.="       ,PGR.PGR_VLRPIS";
          $sql.="       ,PGR.PGR_VLRCSLL";
          $sql.="       ,PGR.PGR_BLOQUEADO";
          $sql.="       ,US.US_APELIDO";
          $sql.="       ,PGR.PGR_CODCC";
          $sql.="       ,PGR.PGR_OBSERVACAO";
          $sql.="       ,PGR.PGR_CHEQUE";
          $sql.="       ,COALESCE(CONVERT(VARCHAR(10),PGR.PGR_DATAPAGA,127),'') AS PGR_DATAPAGA";                              
          $sql.="       ,PGR.PGR_LOTECNAB";
          $sql.="       ,PM.PM_PARCELA";
          $sql.="       ,PM.PM_LANCTOINI";
          $sql.="       ,PM.PM_LANCTOFIM";
          $sql.="       ,PR.PR_CODINI";
          $sql.="       ,PR.PR_CODFIM";
          $sql.="       ,CC.CC_NOME";
          $sql.="  FROM PAGAR PGR";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON FVR.FVR_CODIGO = PGR.PGR_CODFVR";
          $sql.="  LEFT OUTER JOIN BANCO BNC ON BNC.BNC_CODIGO = PGR.PGR_CODBNC";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO TD ON TD.TD_CODIGO = PGR.PGR_CODTD";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON US.US_CODIGO = PGR.PGR_CODUSR";
          $sql.="  LEFT OUTER JOIN FORMACOBRANCA FC ON FC.FC_CODIGO = PGR.PGR_CODFC";
          $sql.="  LEFT OUTER JOIN PAGARTITULO PTT ON PTT.PTT_CODIGO = PGR.PGR_CODPTT";
          $sql.="  LEFT OUTER JOIN PAGARTIPO PTP ON PTP.PTP_CODIGO = PGR.PGR_CODPTP";          
          $sql.="  LEFT OUTER JOIN PAGARMASTER PM ON PM.PM_MASTER = PGR.PGR_MASTER";
          $sql.="  LEFT OUTER JOIN PAGARRATEIO PR ON PR.PR_LANCTO = PGR.PGR_LANCTO";
          $sql.="  LEFT OUTER JOIN CONTACONTABIL CC ON CC.CC_CODIGO = PGR.PGR_CODCC";
          $sql.=" WHERE PGR.PGR_LANCTO=".$lote[0]->lancto;
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $tblPgr=$retCls["dados"][0];
            //////////////////////
            // Montando o contabil
            //////////////////////
            $sql="";          
            $sql.="SELECT RAT.RAT_LANCTO";
            $sql.="       ,RAT.RAT_CODCMP";
            $sql.="       ,RAT.RAT_CODCC";
            $sql.="       ,RAT.RAT_CONTABIL";
            $sql.="       ,CC.CC_NOME";          
            $sql.="       ,RAT.RAT_DEBITO";
            $sql.="       ,RAT.RAT_CREDITO";
            $sql.="  FROM RATEIO RAT";
            $sql.="  LEFT OUTER JOIN CONTACONTABIL CC ON CC.CC_CODIGO=RAT.RAT_CODCC";
            $sql.=" WHERE (RAT.RAT_CODIGO BETWEEN ".$tblPgr["PR_CODINI"]." AND ".$tblPgr["PR_CODFIM"].")";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            $tblRat=$retCls["dados"];
            ////////////////////////////////////
            // Montando a contrapartida contabil
            ////////////////////////////////////
            if( $tblPgr["PGR_DATAPAGA"]!="" ){
              $clsCompet->montaRetorno($tblPgr["PGR_DATAPAGA"],"yyyy-mm-dd");
              $codcmp=$clsCompet->getData('yyyymm');
            } else {
              $codcmp=$tblRat[0]["RAT_CODCMP"];
            };
            
            array_push( $tblRat,[
              "RAT_LANCTO"    =>  $lote[0]->lancto
              ,"RAT_CODCMP"   =>  $codcmp
              ,"RAT_CODCC"    =>  $tblPgr["PGR_CODCC"] 
              ,"RAT_CONTABIL" =>  "S"
              ,"CC_NOME"      =>  $tblPgr["CC_NOME"] 
              ,"RAT_DEBITO"   =>  ($tblRat[0]["RAT_DEBITO"]>0   ? 0 : $tblPgr["PGR_VLRLIQUIDO"])
              ,"RAT_CREDITO"  =>  ($tblRat[0]["RAT_CREDITO"]>0  ? 0 : $tblPgr["PGR_VLRLIQUIDO"])
            ]);
            //////////////////////////
            // Montando o parcelamento
            //////////////////////////
            $sql="";          
            $sql.="SELECT PGR_LANCTO,CONVERT(VARCHAR(10),PGR_VENCTO,127) AS PGR_VENCTO,PGR_VLRLIQUIDO,COALESCE(CONVERT(VARCHAR(10),PGR_DATAPAGA,127),'') AS PGR_DATAPAGA FROM PAGAR";
            $sql.=" WHERE (PGR_LANCTO BETWEEN ".$tblPgr["PM_LANCTOINI"]." AND ".$tblPgr["PM_LANCTOFIM"].")";
            $sql.="   AND (PGR_MASTER=".$tblPgr["PGR_MASTER"].")";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            $tblPar=$retCls["dados"];
            $retorno='[{"retorno":"OK"
                        ,"tblPgr":'.json_encode($tblPgr).'
                        ,"tblRat":'.json_encode($tblRat).'                        
                        ,"tblPar":'.json_encode($tblPar).'                                                
                        ,"erro":""}]'; 
          };  
        };  
        /////////////////////////
        // Bloqueio
        /////////////////////////
        if( $lote[0]->rotina=="bloqueio" ){
          $sql="UPDATE PAGAR SET PGR_BLOQUEADO='".$lote[0]->bq."',PGR_CODUSR=".$_SESSION["usr_codigo"]." WHERE PGR_LANCTO=".$lote[0]->lancto;
          array_push($arrUpdt,$sql);            
          $atuBd = true;
        };  
        /////////////////////////
        // Alterando emissao
        /////////////////////////
        if( $lote[0]->rotina=="alteraemissao" ){
          $objReg = $lote[0]->REGISTRO;
          foreach ( $objReg as $reg ){
            $sql="UPDATE PAGAR SET PGR_DTDOCTO='".$lote[0]->emissao."',PGR_CODUSR=".$_SESSION["usr_codigo"]." WHERE PGR_LANCTO=".$reg->lancto;
            array_push($arrUpdt,$sql);            
          };
          $atuBd = true;
        };  
        /////////////////////////
        // Alterando vencimento
        /////////////////////////
        if( $lote[0]->rotina=="alteravencto" ){
          $objReg = $lote[0]->REGISTRO;
          foreach ( $objReg as $reg ){
            $sql="UPDATE PAGAR SET PGR_VENCTO='".$lote[0]->vencto."',PGR_CODUSR=".$_SESSION["usr_codigo"]." WHERE PGR_LANCTO=".$reg->lancto;
            array_push($arrUpdt,$sql);            
          };
          $atuBd = true;
        };  
        //
        //
        /////////////////////////
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="filtrar" ){        
          $sql="";
          $sql.="SELECT A.PGR_LANCTO AS LANCTO";
          $sql.="       ,A.PGR_DOCTO AS DOCTO";
          $sql.="       ,CONVERT(VARCHAR(10),A.PGR_DTDOCTO,127) AS PGR_DTDOCTO";          
          $sql.="       ,A.PGR_CODPTP AS TP";
          $sql.="       ,FVR.FVR_APELIDO AS FAVORECIDO";          
          $sql.="       ,A.PGR_CODFC AS FC";
          $sql.="       ,A.PGR_CODTD AS TD";
          $sql.="       ,CONVERT(VARCHAR(10),A.PGR_VENCTO,127) AS VENCTO";                    
          $sql.="       ,(A.PGR_VLRLIQUIDO*A.PGR_INDICE) AS VALOR";          
          $sql.="       ,BNC.BNC_NOME AS BANCO";                    
          $sql.="      ,CONVERT(VARCHAR(10),A.PGR_DATAPAGA,127) AS BAIXA";                    
          $sql.="       ,A.PGR_CHEQUE AS DOCTOBAIXA";
          $sql.="       ,A.PGR_BLOQUEADO AS BLQ";
          $sql.="       ,A.PGR_LOTECNAB AS CNAB";
          $sql.="       ,CASE WHEN A.PGR_REG='P' THEN 'PUB' WHEN A.PGR_REG='S' THEN 'SIS' ELSE 'ADM' END AS REG";          
          $sql.="       ,USR.USR_APELIDO AS USUARIO";
          $sql.="       ,A.PGR_CODFVR AS CODFVR";       //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,A.PGR_CODBNC AS CODBNC";       //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,A.PGR_OBSERVACAO AS OBS";      //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,TD.TD_NOME AS DESTD";          //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,FC.FC_NOME AS DESFC";          //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,A.PGR_CODFLL AS CODFLL";       //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,A.PGR_CODPT AS CODPT";         //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,PT.PT_CODCC AS CODCC";         //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,PT.PT_DEBCRE AS DC";           //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,A.PGR_MASTER AS MASTER";       //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,A.PGR_CODPTT AS CODPTT";       //Coluna naum utlizada na grade-apenas para alteracao docto  
          $sql.="       ,A.PGR_CODCMP AS CODCMP";       //Coluna naum utlizada na grade-apenas para alteracao docto
          $sql.="       ,A.PGR_VLREVENTO AS VLREVENTO"; //Coluna naum utlizada na grade-apenas para alteracao docto          
          $sql.="  FROM PAGAR A"; 
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PGR_CODFVR=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN BANCO BNC ON A.PGR_CODBNC=BNC.BNC_CODIGO";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO TD ON A.PGR_CODTD=TD.TD_CODIGO";
          $sql.="  LEFT OUTER JOIN FORMACOBRANCA FC ON A.PGR_CODFC=FC.FC_CODIGO";
          $sql.="  LEFT OUTER JOIN PADRAOTITULO PT ON A.PGR_CODPT=PT.PT_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIO USR ON A.PGR_CODUSR=USR.USR_CODIGO";
          $sql.=" WHERE (PGR_VENCTO BETWEEN '".$lote[0]->dtini."' AND '".$lote[0]->dtfim."')";           
          $sql.="   AND (PGR_CODPTP IN(".$lote[0]->codptp."))";
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
    <title>CPCR</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="js/jsBiblioteca.js"></script>
    <script src="js/jsCopiaDoc2017.js"></script>            
    <script>      
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        document.getElementById("edtDataIni").value  = jsDatas(0).retDDMMYYYY();      
        document.getElementById("edtDataFim").value  = jsDatas(0).retDDMMYYYY();      
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsPgr={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "LANCTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "DOCTO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "DTDOCTO"
                      ,"fieldType"      : "str"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "TP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "FAVORECIDO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "FC"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "TD"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "8"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "VENCTO"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "BANCO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : "S"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "BAIXA"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "DOCBAIXA"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "BQ"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "10"
                      ,"align"          : "center"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"funcCor"        : "(objCell.innerHTML=='S' ? objCell.classList.add('corAlterado') : objCell.classList.remove('corAlterado'))"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "CNAB"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                       
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "REG"    
                      ,"labelCol"       : "REG"     
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"align"          : "center"
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":16 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":17 ,"labelCol"       : "CODFVR","fieldType":"str","padrao":0}
            ,{"id":18 ,"labelCol"       : "CODBNC","fieldType":"str","padrao":0}
            ,{"id":19 ,"labelCol"       : "OBS"   ,"fieldType":"str","padrao":0}
            ,{"id":20 ,"labelCol"       : "DESTD" ,"fieldType":"str","padrao":0}
            ,{"id":21 ,"labelCol"       : "DESFC" ,"fieldType":"str","padrao":0}
            ,{"id":22 ,"labelCol"       : "CODFLL","fieldType":"str","padrao":0}
            ,{"id":23 ,"labelCol"       : "CODPT" ,"fieldType":"str","padrao":0}            
            ,{"id":24 ,"labelCol"       : "CODCC" ,"fieldType":"str","padrao":0}
            ,{"id":25 ,"labelCol"       : "DC"    ,"fieldType":"str","padrao":0}            
            ,{"id":26 ,"labelCol"       : "MASTER","fieldType":"str","padrao":0}
            ,{"id":27 ,"labelCol"       : "CODPTT","fieldType":"str","padrao":0}
            ,{"id":28 ,"labelCol"       : "CODCMP","fieldType":"str","padrao":0}            
            ,{"id":29 ,"labelCol"       : "VLREVENTO","fieldType":"flo2","padrao":0}            
            ,{"id":30 ,"labelCol"        :"CD"         
                      ,"obj"           : "imgPP"
                      ,"tamGrd"       : "5em"
                      ,"tipo"         : "img"
                      ,"fieldType"    : "img"
                      ,"func"         : "copiaDocumento(this.parentNode.parentNode.cells[1].innerHTML);"
                      ,"tagI"         : "fa fa-print copiaDoc"
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
             {"texto":"Novo lancto"     ,"name":"horNovoLancto" ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-plus"             }
            ,{"texto":"Alterar Vencto"  ,"name":"horAltVencto"  ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-calendar"         }
            ,{"texto":"Alterar Emissao" ,"name":"horAltEmissao" ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-calendar"         }
            ,{"texto":"Alterar Lancto"  ,"name":"horAltLancto"  ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-edit"             }        
            ,{"texto":"Baixa total"     ,"name":"horBaixaTot"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-sort-amount-asc"  }
            ,{"texto":"Baixa parcial"   ,"name":"horBaixaParc"  ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-code"             }        
            ,{"texto":"Excluir baixa"   ,"name":"horExcBaixa"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-sort-amount-asc"  }
            ,{"texto":"Definitivo"      ,"name":"horDefinitivo" ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-sort-amount-asc"  }            
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "switch (ceTr.cells[4].innerHTML){case 'CP' : ceTr.style.color='red';break; case 'CR' : ceTr.style.color='black';break; default:ceTr.style.color='blue';break;}"          
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmPgr"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaPgr"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmPgr"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnPgr"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblPgr"                  // Nome da table
          ,"prefixo"        : "Me"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VPAGAR"                  // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "absolute"
          ,"width"          : "135em"                   // Tamanho da table
          ,"height"         : "65em"                    // Altura da table
          ,"nChecks"        : true                      // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "CPCR"                    // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"foco"           : ["edtDataIni"
                              ,"edtDataIni"
                              ,"btnConfirmar"]          // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "VENCTO"                  // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objPgr === undefined ){  
          objPgr=new clsTable2017("objPgr");
        };  
        objPgr.montarHtmlCE2017(jsPgr); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsPgr,["BAIXA"  ,"BANCO"  ,"BQ"     ,"CODBNC" ,"CODCC"    ,"CODCMP" ,"CODFLL" ,"CODFVR"   ,"CODPT"      
                                            ,"CODPTT" ,"D\C"    ,"DESFC"  ,"DESTD"  ,"DOCBAIXA" ,"DOCTO"  ,"DTDOCTO"  ,"FAVORECIDO" 
                                            ,"FC"     ,"LANCTO" ,"MASTER" ,"OBS"    ,"REG"      ,"TD"       ,"TP"       ,"USUARIO"
                                            ,"VENCTO" ,"VALOR"  ,"VLREVENTO"]);
        btnFiltrarClick();
      });
      var objPgr;                     // Obrigatório para instanciar o JS TFormaCob
      var jsPgr;                      // Obj principal da classe clsTable2017
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d05);
      var clsChecados;                // Classe para montar Json
      var chkds;                      // Guarda todos registros checados na table 
      var filtroTipo ="CP_CR";        // Buscar os tipos para filtro
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario                  
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"           );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("dtini"       , jsDatas("edtDataIni").retMMDDYYYY() );
        clsJs.add("dtfim"       , jsDatas("edtDataFim").retMMDDYYYY() );
        clsJs.add("codptp"      , "'"+filtroTipo.replaceAll("_","','")+"'" );
        fd = new FormData();
        fd.append("cpcr" , clsJs.fim());
        msg     = requestPedido("Trac_CpCr.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsPgr.registros=objPgr.addIdUnico(retPhp[0]["dados"]);
          objPgr.ordenaJSon(jsPgr.indiceTable,false);  
          objPgr.montarBody2017();
        };  
      };
      /////////////////////////////////
      //         Alterar vencto      //
      /////////////////////////////////
      function horAltVenctoClick(){
        try{
          clsChecados = objPgr.gerarJson("n");
          chkds       = clsChecados.gerar();
          msg         = "ok";
          
          chkds.forEach(function(reg){
            if( reg.BAIXA != "" )
              throw "Lancto "+reg.LANCTO+" com data de baixa!"; 
          });
          ///////////////////////////////////////////////////////////////
          // Se der tudo certo abro a janela para informacao de nova data
          ///////////////////////////////////////////////////////////////  
          let mascaraData="placeholder='##/##/####' maxlength='10' OnKeyUp=mascaraNumero('##/##/####',this,event,'dig')";
          let clsCode = new concatStr();  
          clsCode.concat("<div class='campotexto campo100'></div>");          
          clsCode.concat("<div class='campotexto campo20'></div>");
          clsCode.concat("<div class='campotexto campo40'>");
          clsCode.concat(  "<input class='campo_input' id='vencto' "+mascaraData+"  type='text' />");
          clsCode.concat(  "<label class='campo_label campo_required' for='edtData'>Informe novo vencimento</label>");
          clsCode.concat("</div>");
          clsCode.concat("<div id='btnConfirmar' onClick='janelaVencto();' class='btnImagemEsq bie15 bieAzul bie'><i class='fa fa-check'>Ok</i></div>");
          janelaDialogo(
            { height        : "22em"
              ,body         : "10em"
              ,left         : "320px"
              ,top          : "20px"
              ,tituloBarra  : "Manutenção"
              ,code         : clsCode.fim()
              ,width        : "50em"
              ,foco         : "vencto"
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      }; 
      function janelaVencto(){  
        try{          
          document.getElementById("vencto").value = jsStr("vencto").upper().alltrim().ret();
          msg = new clsMensagem("Erro"); 
          msg.dataValida("vencto", document.getElementById("vencto").value );
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            /////////////////////////////////////////////////
            // Armazenando para envio ao Php clsReg=Registros
            /////////////////////////////////////////////////
            let clsReg = jsString("registro");
            clsReg.principal(false);
            chkds.forEach(function(reg){
              clsReg.add("lancto", reg.LANCTO);             
              if( jsDatas(reg.DTDOCTO).retDIAS()>jsDatas("vencto").retDIAS() )
                throw "DATA DO DOCTO "+reg.DTDOCTO+" NÃO PODE SER MAIOR QUE O VENCIMENTO!"; 
            });
            let registro = clsReg.fim();
            //////////////////////
            // Enviando para o Php
            //////////////////////
            clsJs=jsString("lote");            
            clsJs.add("rotina"    , "alteravencto"                  );              
            clsJs.add("login"     ,jsPub[0].usr_login               );              
            clsJs.add("vencto"   , jsDatas("vencto").retMMDDYYYY() );              
            clsJs.add("REGISTRO" , registro                        );

            var fd = new FormData();
            fd.append("cpcr" , clsJs.fim());
            msg=requestPedido("Trac_CpCr.php",fd); 
            retPhp=JSON.parse(msg);
            if( retPhp[0].retorno=="OK" ){
              /////////////////////////////////////////////////
              // Atualizando a grade
              /////////////////////////////////////////////////
              let tbl = tblPgr.getElementsByTagName("tbody")[0];
              let nl  = tbl.rows.length;
              if( nl>0 ){
                for(let lin=0 ; (lin<nl) ; lin++){
                  chkds.forEach(function(reg){
                    if( reg.LANCTO == tbl.rows[lin].cells[objCol.LANCTO].innerHTML ){
                      tbl.rows[lin].cells[objCol.VENCTO].innerHTML=document.getElementById("vencto").value;
                      tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                    };
                  });  
                };    
              };  
              janelaFechar();
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////////////////////
      //         Alterar emissao     //
      /////////////////////////////////
      function horAltEmissaoClick(){
        try{
          clsChecados = objPgr.gerarJson("n");
          chkds       = clsChecados.gerar();
          msg         = "ok";
          
          chkds.forEach(function(reg){
            if( reg.BAIXA != "" )
              throw "Lancto "+reg.LANCTO+" com data de baixa!"; 
          });
          ///////////////////////////////////////////////////////////////
          // Se der tudo certo abro a janela para informacao de nova data
          ///////////////////////////////////////////////////////////////  
          let mascaraData="placeholder='##/##/####' maxlength='10' OnKeyUp=mascaraNumero('##/##/####',this,event,'dig')";
          let clsCode = new concatStr();  
          clsCode.concat("<div class='campotexto campo100'></div>");          
          clsCode.concat("<div class='campotexto campo20'></div>");
          clsCode.concat("<div class='campotexto campo40'>");
          clsCode.concat(  "<input class='campo_input' id='emissao' "+mascaraData+"  type='text' />");
          clsCode.concat(  "<label class='campo_label campo_required' for='emissao'>Informe nova emissão</label>");
          clsCode.concat("</div>");
          clsCode.concat("<div id='btnConfirmar' onClick='janelaEmissao();' class='btnImagemEsq bie15 bieAzul bie'><i class='fa fa-check'>Ok</i></div>");
          janelaDialogo(
            { height        : "22em"
              ,body         : "10em"
              ,left         : "320px"
              ,top          : "20px"
              ,tituloBarra  : "Manutenção"
              ,code         : clsCode.fim()
              ,width        : "50em"
              ,foco         : "emissao"
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };  
      function janelaEmissao(){
        try{     
          document.getElementById("emissao").value = jsStr("emissao").upper().alltrim().ret();
          msg = new clsMensagem("Erro"); 
          msg.dataValida("emissao", document.getElementById("emissao").value );
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            /////////////////////////////////////////////////
            // Armazenando para envio ao Php clsReg=Registros
            /////////////////////////////////////////////////
            let clsReg = jsString("registro");
            clsReg.principal(false);
            chkds.forEach(function(reg){
              clsReg.add("lancto", reg.LANCTO);             
              if( jsDatas("emissao").retDIAS()>jsDatas(reg.VENCTO).retDIAS() )
                throw "DATA DO VENCTO "+reg.VENCTO+" NÃO PODE SER MENOR QUE A EMISSÃO!"; 
            });
            let registro = clsReg.fim();
            //////////////////////
            // Enviando para o Php
            //////////////////////
            clsJs=jsString("lote");            
            clsJs.add("rotina"    , "alteraemissao"                 );              
            clsJs.add("login"     ,jsPub[0].usr_login               );              
            clsJs.add("emissao"   , jsDatas("emissao").retMMDDYYYY() );              
            clsJs.add("REGISTRO" , registro                         );
            var fd = new FormData();
            fd.append("cpcr" , clsJs.fim());
            msg=requestPedido("Trac_CpCr.php",fd); 
            retPhp=JSON.parse(msg);
            if( retPhp[0].retorno=="OK" ){
              /////////////////////////////////////////////////
              // Atualizando a grade
              /////////////////////////////////////////////////
              let tbl = tblPgr.getElementsByTagName("tbody")[0];
              let nl  = tbl.rows.length;
              if( nl>0 ){
                for(let lin=0 ; (lin<nl) ; lin++){
                  chkds.forEach(function(reg){
                    if( reg.LANCTO == tbl.rows[lin].cells[objCol.LANCTO].innerHTML ){
                      tbl.rows[lin].cells[objCol.DTDOCTO].innerHTML=document.getElementById("emissao").value;
                      tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                    };
                  });  
                };    
              };  
              janelaFechar();
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////////////////////
      //         Alterar docto       //
      /////////////////////////////////
      function horAltLanctoClick(){
        try{
          clsChecados = objPgr.gerarJson("1");
          chkds       = clsChecados.gerar();
          
          chkds.forEach(function(reg){
            if( reg.BAIXA != "" )
              throw "Lancto "+reg.LANCTO+" com data de baixa!"; 
            if( reg.BQ == "S" )
              throw "Lancto bloqueado "+reg.LANCTO+" não pode ser alterado!"; 
            if( jsNmrs(reg.CNAB).inteiro().ret()>0 )
              throw "Lancto "+reg.LANCTO+" com arquivo CNAB não pode ser alterado!"; 
            
          });
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para enviar ao formulario de alteracao
          //////////////////////////////////////////////////////////////
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("codbnc"      , chkds[0].CODBNC     );
          clsJs.add("codcc"       , chkds[0].CODCC      );
          clsJs.add("codcmp"      , chkds[0].CODCMP     );            
          clsJs.add("codfc"       , chkds[0].FC         );
          clsJs.add("codfll"      , chkds[0].CODFLL     );
          clsJs.add("codfvr"      , chkds[0].CODFVR     );
          clsJs.add("codtd"       , chkds[0].TD         );
          clsJs.add("codptt"      , chkds[0].CODPTT     );
          clsJs.add("debcre"      , chkds[0].DC         );    //Se lancto em PADRAOTITULO vai a debito ou credito
          clsJs.add("desbnc"      , chkds[0].BANCO      );          
          clsJs.add("desfc"       , chkds[0].DESFC      );
          clsJs.add("desfvr"      , chkds[0].FAVORECIDO );
          clsJs.add("destd"       , chkds[0].DESTD      );
          clsJs.add("docto"       , chkds[0].DOCTO      );          
          clsJs.add("dtdocto"     , chkds[0].DTDOCTO    );
          clsJs.add("lancto"      , chkds[0].LANCTO     );
          clsJs.add("master"      , chkds[0].MASTER     );
          clsJs.add("observacao"  , chkds[0].OBS        );
          clsJs.add("vencto"      , chkds[0].VENCTO     );
          clsJs.add("valor"       , chkds[0].VALOR      );
          clsJs.add("vlrevento"   , chkds[0].VLREVENTO  );            
          clsJs.add("codptp"      , chkds[0].TP         );
          clsJs.add("codpt"       , chkds[0].CODPT      );          
          /////////////////////////////////////////////////////////////////
          // Passando as colunas que vou precisas para atualizar esta table
          /////////////////////////////////////////////////////////////////
          for(let key in objCol) { 
            clsJs.add(key, objCol[key] );          
          };            
          objEnvio=clsJs.fim();  
          localStorage.setItem("addAlt",objEnvio);
          window.open("Trac_CpCrAltDocto.php");
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function horNovoLanctoClick(){
        window.open("Trac_CpCrCadTitulo.php");
      };
      function fncBloquear(str){
        try{
          clsChecados = objPgr.gerarJson("1");
          chkds       = clsChecados.gerar();
          
          chkds.forEach(function(reg){
            if( reg.BAIXA != "" )
              throw "Lancto "+reg.LANCTO+" com data de baixa!"; 
            if( reg.BQ == str )
              throw "Lancto "+reg.LANCTO+" verifique status!"; 
            if( reg.TP != "CP" )
              throw "Lancto "+reg.LANCTO+" aceito apenas contas a pagar!"; 
          });
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para enviar ao formulario de alteracao
          //////////////////////////////////////////////////////////////
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("rotina"  , "bloqueio"          );              
          clsJs.add("login"   , jsPub[0].usr_login  );              
          clsJs.add("lancto"  , chkds[0].LANCTO     );
          clsJs.add("bq"      , str                 );
          var fd = new FormData();
          fd.append("cpcr" , clsJs.fim());
          msg=requestPedido("Trac_CpCr.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            /////////////////////////////////////////////////
            // Atualizando a grade
            /////////////////////////////////////////////////
            let tbl = tblPgr.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
            if( nl>0 ){
              for(let lin=0 ; (lin<nl) ; lin++){
                if( chkds[0].LANCTO == tbl.rows[lin].cells[objCol.LANCTO].innerHTML ){
                  tbl.rows[lin].cells[objCol.BQ].innerHTML=str;
                  tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                  if( str=="S" )
                    tbl.rows[lin].cells[objCol.BQ].classList.add("corAlterado");
                  else    
                    tbl.rows[lin].cells[objCol.BQ].classList.remove("corAlterado");  
                };
              };    
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////////////////////
      //        baixa parcial        //
      /////////////////////////////////
      function horBaixaParcClick(){
        if( parseInt(jsPub[0].usr_d34) !=4  ){
          gerarMensagemErro("dir","USUARIO NÃO POSSUI DIREITO 34 PARA ESTA ROTINA!",{cabec:"Aviso"});  
        } else {
          try{
            clsChecados = objPgr.gerarJson("1");
            chkds       = clsChecados.gerar();
            
            chkds.forEach(function(reg){
              if( reg.BAIXA != "" )
                throw "Lancto "+reg.LANCTO+" com data de baixa!"; 
              if( reg.BQ == "S" )
                throw "Lancto bloqueado "+reg.LANCTO+" não pode ser baixado!"; 
              if( jsNmrs(reg.CNAB).inteiro().ret()>0 )
                throw "Lancto "+reg.LANCTO+" com arquivo CNAB não pode ser baixado!"; 
              if( ["CP","CR"].indexOf(reg.TP) == -1 )
                throw "Lancto "+reg.LANCTO+" aceito apenas CP ou CR!"; 
            });
            //////////////////////////////////////////////////////////////
            // Preparando um objeto para enviar ao formulario de alteracao
            //////////////////////////////////////////////////////////////
            let objEnvio;          
            clsJs=jsString("lote");
            clsJs.add("codbnc"      , chkds[0].CODBNC     );
            clsJs.add("codfc"       , chkds[0].FC         );
            clsJs.add("codfvr"      , chkds[0].CODFVR     );
            clsJs.add("codtd"       , chkds[0].TD         );
            clsJs.add("desbnc"      , chkds[0].BANCO      );          
            clsJs.add("desfc"       , chkds[0].DESFC      );
            clsJs.add("desfvr"      , chkds[0].FAVORECIDO );
            clsJs.add("destd"       , chkds[0].DESTD      );
            clsJs.add("docto"       , chkds[0].DOCTO      );          
            clsJs.add("dtdocto"     , chkds[0].DTDOCTO    );
            clsJs.add("lancto"      , chkds[0].LANCTO     );
            clsJs.add("observacao"  , chkds[0].OBS        );
            clsJs.add("vencto"      , chkds[0].VENCTO     );
            clsJs.add("valor"       , chkds[0].VALOR      );
            clsJs.add("vlrevento"   , chkds[0].VLREVENTO  );                          
            /////////////////////////////////////////////////////////////////
            // Passando as colunas que vou precisas para atualizar esta table
            /////////////////////////////////////////////////////////////////
            for(let key in objCol) { 
              clsJs.add(key, objCol[key] );          
            };            
            objEnvio=clsJs.fim();          
            localStorage.setItem("addAlt",objEnvio);
            window.open("Trac_CpCrBaixaParcial.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});
          };
        };  
      };
      /////////////////////////////////
      //        baixa total          //
      /////////////////////////////////
      function horBaixaTotClick(){
        fncBaixaExcluir("baixatotal")    
      };  
      /////////////////////////////////
      //        excluir baixa        //
      /////////////////////////////////
      function horExcBaixaClick(){
        fncBaixaExcluir("excluirbaixa")    
      };  
      /////////////////////////////////
      //        baixa total          //
      //        excluir baixa        //      
      /////////////////////////////////
      function fncBaixaExcluir(parametro){
        if( parseInt(jsPub[0].usr_d34) !=4  ){
          gerarMensagemErro("dir","USUARIO NÃO POSSUI DIREITO 34 PARA ESTA ROTINA!",{cabec:"Aviso"});  
        } else {
          try{
            clsChecados = objPgr.gerarJson("n");
            chkds       = clsChecados.gerar();
            
            if(parametro=="baixatotal"){
              chkds.forEach(function(reg){
                if( reg.BAIXA != "" )
                  throw "Lancto "+reg.LANCTO+" com data de baixa!"; 
                if( reg.BQ == "S" )
                  throw "Lancto bloqueado "+reg.LANCTO+" não pode ser baixado!"; 
                if( jsNmrs(reg.CNAB).inteiro().ret()>0 )
                  throw "Lancto "+reg.LANCTO+" com arquivo CNAB não pode ser baixado!"; 
                if( ["CP","CR"].indexOf(reg.TP) == -1 )
                  throw "Lancto "+reg.LANCTO+" aceito apenas CP ou CR!"; 
              });            
            };
            
            if(parametro=="excluirbaixa"){
              chkds.forEach(function(reg){
                if( reg.BAIXA == "" )
                  throw "Lancto "+reg.LANCTO+" sem data de baixa!"; 
                if( reg.BQ == "S" )
                  throw "Lancto bloqueado "+reg.LANCTO+" não pode ser baixado!"; 
                if( jsNmrs(reg.CNAB).inteiro().ret()>0 )
                  throw "Lancto "+reg.LANCTO+" com arquivo CNAB não pode ser baixado!"; 
                if( ["CP","CR"].indexOf(reg.TP) == -1 )
                  throw "Lancto "+reg.LANCTO+" aceito apenas CP ou CR!"; 
              });            
            };
            //////////////////////////////////////////////////////////////
            // Preparando um objeto para enviar ao formulario de alteracao
            //////////////////////////////////////////////////////////////
            let objEnvio;          
            clsJs=jsString("lote");
            chkds.forEach(function(reg){
              clsJs.add("codbnc"      , reg.CODBNC      );
              clsJs.add("desbnc"      , reg.BANCO       );          
              clsJs.add("desfvr"      , reg.FAVORECIDO  );
              clsJs.add("lancto"      , reg.LANCTO      );
              clsJs.add("vencto"      , reg.VENCTO      );
              clsJs.add("valor"       , reg.VALOR       );
              clsJs.add("rotina"      , parametro       );
              ///////////////////////////////////////////////////////////////////////////
              // Como pode ser multiplores registros mando tb as colunas para cada lancto
              ///////////////////////////////////////////////////////////////////////////
              for(let key in objCol) { 
                clsJs.add(key, objCol[key] );          
              };            
            });  
            /////////////////////////////////////////////////////////////////
            // Passando as colunas que vou precisas para atualizar esta table
            /////////////////////////////////////////////////////////////////
            objEnvio=clsJs.fim();          
            localStorage.setItem("addAlt",objEnvio);
            window.open("Trac_CpCrBaixaTotal.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});
          };
        };  
      };
      
      function horDefinitivoClick(){
        if( parseInt(jsPub[0].usr_d34) !=4  ){
          gerarMensagemErro("dir","USUARIO NÃO POSSUI DIREITO 34 PARA ESTA ROTINA!",{cabec:"Aviso"});  
        } else {
          try{
            clsChecados = objPgr.gerarJson("n");
            chkds       = clsChecados.gerar();
            

            chkds.forEach(function(reg){
              if( reg.BAIXA != "" )
                throw "Lancto "+reg.LANCTO+" com data de baixa!"; 
              if( reg.BQ == "S" )
                throw "Lancto bloqueado "+reg.LANCTO+" não pode ser baixado!"; 
              if( jsNmrs(reg.CNAB).inteiro().ret()>0 )
                throw "Lancto "+reg.LANCTO+" com arquivo CNAB não pode ser baixado!"; 
              if( ["PP","MP","PR","MR"].indexOf(reg.TP) == -1 )
                throw "Lancto "+reg.LANCTO+" aceito apenas PP/PR/MP ou MR!"; 
            });            
            //////////////////////////////////////////////////////////////
            // Preparando um objeto para enviar ao formulario de alteracao
            //////////////////////////////////////////////////////////////
            let objEnvio;          
            clsJs=jsString("lote");
            chkds.forEach(function(reg){
              clsJs.add("docto"       , chkds[0].DOCTO );                          
              clsJs.add("codbnc"      , reg.CODBNC     );
              clsJs.add("desbnc"      , reg.BANCO      );          
              clsJs.add("desfvr"      , reg.FAVORECIDO );
              clsJs.add("lancto"      , reg.LANCTO     );
              clsJs.add("vencto"      , reg.VENCTO     );
              clsJs.add("valor"       , reg.VALOR      );
              ///////////////////////////////////////////////////////////////////////////
              // Como pode ser multiplores registros mando tb as colunas para cada lancto
              ///////////////////////////////////////////////////////////////////////////
              for(let key in objCol) { 
                clsJs.add(key, objCol[key] );          
              };            
            });  
            objEnvio=clsJs.fim();          
            localStorage.setItem("addAlt",objEnvio);
            window.open("Trac_CpCrDefinitivo.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});
          };
        };  
      };
      function fncRegSistema(){
        try{    
          clsChecados = objPgr.gerarJson("n");
          chkds       = clsChecados.gerar();
          /////////////////////////////////////////////////
          // Armazenando para envio ao Php clsReg=Registros
          /////////////////////////////////////////////////
          let clsReg = jsString("registro");
          clsReg.principal(false);
          chkds.forEach(function(reg){
            clsReg.add("lancto", reg.LANCTO);             
            if( reg.REG=="SIS" )
              throw "LANCTO "+reg.LANCTO+" JA ESTA COM STATUS DO SISTEMA!"; 
            if( ["CP","CR","LE","DT"].indexOf(reg.TP) == -1 )
              throw "Lancto "+reg.LANCTO+" aceito apenas CP/CR/LE ou DT!"; 
            
          });
          let registro = clsReg.fim();
          //////////////////////
          // Enviando para o Php
          //////////////////////
          clsJs=jsString("lote");            
          clsJs.add("rotina"    , "regsistema"      );              
          clsJs.add("login"     ,jsPub[0].usr_login );              
          clsJs.add("REGISTRO" , registro           );

          var fd = new FormData();
          fd.append("cpcr" , clsJs.fim());
          msg=requestPedido("Trac_CpCr.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            /////////////////////////////////////////////////
            // Atualizando a grade
            /////////////////////////////////////////////////
            let tbl = tblPgr.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
            if( nl>0 ){
              for(let lin=0 ; (lin<nl) ; lin++){
                chkds.forEach(function(reg){
                  if( reg.LANCTO == tbl.rows[lin].cells[objCol.LANCTO].innerHTML ){
                    tbl.rows[lin].cells[objCol.REG].innerHTML="SIS";
                    tbl.rows[lin].cells[objCol.REG].classList.add("corAzul");
                    tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                  };
                });  
              };    
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      
      function fncTipos(){
        let clsCode = new concatStr();  
        clsCode.concat("<div id='dPaiChk' class='divContainerTable' style='height: 31.2em; width: 41em;border:none'>");
        clsCode.concat("<table id='tblChk' class='fpTable' style='width:100%;'>");
        clsCode.concat("  <thead class='fpThead'>");
        clsCode.concat("    <tr>");
        clsCode.concat("      <th class='fpTh' style='width:20%'>CODIGO</th>");
        clsCode.concat("      <th class='fpTh' style='width:60%'>DESCRICAO</th>");
        clsCode.concat("      <th class='fpTh' style='width:20%'>SIM</th>");        
        clsCode.concat("    </tr>");
        clsCode.concat("  </thead>");
        clsCode.concat("  <tbody id='tbody_tblChk'>");
        //////////////////////
        // Preenchendo a table
        //////////////////////  
        let arr=[];
        arr.push({cod:"CP",des:"CONTAS A PAGAS"     ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"CR",des:"CONTAS A RECEBER"   ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"PP",des:"PREVISAO A PAGAS"   ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"PR",des:"PREVISAO A RECEBER" ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"MP",des:"MENSAL A PAGAS"     ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"MR",des:"MENSAL A RECEBER"   ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"DT",des:"DESCONTO TOTAL"     ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"LE",des:"LANCTO EXTRA"       ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        arr.push({cod:"EX",des:"EXCLUIDO"           ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        //////////////////////////////////////////////////////////
        // Atualizando a grade com a ultima selecao
        //////////////////////////////////////////////////////////
        let splt=filtroTipo.split("_");  
        splt.forEach( function(sp){
          arr.forEach( function(ar){
            if( ar.cod==sp ){
              ar.sn   = "S";
              ar.fa   = "fa fa-thumbs-o-up";
              ar.cor  = "blue";
            }
          });
        });

        arr.forEach(function(reg){
          clsCode.concat("    <tr class='fpBodyTr'>");
          clsCode.concat("      <td class='fpTd textoCentro'>"+reg.cod+"</td>");
          clsCode.concat("      <td class='fpTd'>"+reg.des+"</td>");
          clsCode.concat("      <td class='fpTd textoCentro'>");
          clsCode.concat("        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheck((elTr.rowIndex-1));'>");
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
        clsCode.concat("<div id='btnConfirmar' onClick='fncJanelaRet();' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'> Ok</i></div>");        
        janelaDialogo(
          { height          : "42em"
            ,body           : "16em"
            ,left           : "500px"
            ,top            : "60px"
            ,tituloBarra    : "Selecione"
            ,code           : clsCode.fim()
            ,width          : "43em"
            ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
          }
        );  
      };
      ///////////////////////////////////////////
      // Marcando e desmarcando os itens da table
      ///////////////////////////////////////////
      function fncCheck(pLin){
        let tbl   = tblChk.getElementsByTagName("tbody")[0];
        let elImg = "img"+tbl.rows[pLin].cells[0].innerHTML;
        let sn    = document.getElementById(elImg).getAttribute("data-value")
        if( sn=="N" ){
          jsCmpAtivo(elImg).remove("fa-thumbs-o-down").add("fa-thumbs-o-up").cor("blue");
          document.getElementById(elImg).setAttribute("data-value","S"); 
        } else {
          jsCmpAtivo(elImg).remove("fa-thumbs-o-up").add("fa-thumbs-o-down").cor("red");
          document.getElementById(elImg).setAttribute("data-value","N"); 
        }
      };
      ///////////////////////////////////////////
      // Recuperando os itens marcados na table
      ///////////////////////////////////////////
      function fncJanelaRet(){
        try{              
          let tbl = tblChk.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          let elImg;
          if( nl>0 ){
            filtroTipo="";
            for(let lin=0 ; (lin<nl) ; lin++){
              elImg="img"+tbl.rows[lin].cells[0].innerHTML;
              
              if( document.getElementById(elImg).getAttribute("data-value") == "S" ){
                filtroTipo+=( filtroTipo=="" ? tbl.rows[lin].cells[0].innerHTML  : "_".concat(tbl.rows[lin].cells[0].innerHTML) );
              };
            };
            janelaFechar();
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
    </script>
  </head>
  
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="objPgr.imprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela."><i class="indFa fa-print"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="objPgr.excel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela."><i class="indFa fa-file-excel-o"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="fncBloquear('S');"
                              data-dismissible="false" 
                              data-toggle="popover" 
                              data-title="Bloquear pagamento <span class='badge badge-warning'>CUIDADO</span>" 
                              data-placement="right" 
                              data-content="Esta opção bloqueia o titulo financeiro de contas a pagar para pagamento."><i class="indFa fa-thumbs-o-down"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="fncBloquear('N');"
                              data-dismissible="false" 
                              data-toggle="popover" 
                              data-title="Desloquear pagamento" 
                              data-placement="right" 
                              data-content="Esta opção retira o bloqueia do titulo financeiro de contas a pagar para pagamento."><i class="indFa fa-thumbs-o-up"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="fncRegSistema();"
                              data-dismissible="false" 
                              data-toggle="popover" 
                              data-title="Registro do sistema <span class='badge badge-warning'>CUIDADO</span>" 
                              data-placement="right" 
                              data-content="Esta opção transforma o registro para o sistema. Este não poderá mais ser alterado e nem excluido"><i class="indFa fa-key"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="fncTipos();"
                              data-dismissible="false" 
                              data-toggle="popover" 
                              data-title="Filtro" 
                              data-placement="right" 
                              data-content="Abre a opção de tipos financeiros para filtro."><i class="indFa fa-edit"></i>
          </div>
        </section>
      </aside>

      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <a href="#" class="indLabel"><div id="tituloMenu">Financeiro</div></a>
        
        <div id="indDivInforme" class="indTopoInicio80">
          <div class="campotexto campo12" style="margin-top:2px;">
            <select class="campo_input_combo" id="cbOpcao">
              <option value="V" selected>VENCTO</option>            
              <option value="B">BAIXA</option>
            </select>
            <label class="campo_label campo_required" for="cbOpcao">OPÇÃO</label>
          </div>
        
          <div class="campotexto campo10" style="margin-top:2px;">
            <input class="campo_input" id="edtDataIni" 
                                       value="01/10/2018"
                                       placeholder="##/##/####"                 
                                       OnKeyUp="mascaraNumero('##/##/####',this,event,'dig')"
                                       maxlength="10" type="text" />
            <label class="campo_label" for="edtDataIni">DE:</label>
          </div>
          <div class="campotexto campo10" style="margin-top:2px;">
            <input class="campo_input" id="edtDataFim" 
                                       value=""
                                       placeholder="##/##/####"                 
                                       OnKeyUp="mascaraNumero('##/##/####',this,event,'dig')"
                                       maxlength="10" type="text" />
            <label class="campo_label" for="edtDataFim">ATÉ:</label>
          </div>
          <div id="btnFiltrar" onClick="btnFiltrarClick();" class="btnImagemEsq bie10 bieAzul" style="margin-top:2px;"><i class="fa fa-check"> Filtrar</i></div>
        </div>
      </div>
      <section>
        <section id="sctnPgr">
        </section>  
      </section>
      <form method="post"
            name="frmAlv"
            id="frmAlv"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/>      
      </form>  
    </div>
  </body>
</html>


<!-- 
<div class="btn-group" style="margin-bottom:10px">
  <button id="makeMeDropdown" class="btn btn-default disabled" disabled="true">All done</button>
  <div class="dropdown btn-group open">
    <button id="formDropdown" type="button" class="btn btn-primary dropdown-toggle" 
                                            data-toggle="dropdown" 
                                            aria-haspopup="true" 
                                            role="button" 
                                            aria-expanded="true" 
                                            tabindex="0">Login 2
      <span class="caret"></span>
    </button>
    <form class="form-vertical dropdown-menu">
      <div class="form-group">
        <label for="inputEmail3" class="control-label">Email</label>
        <div class="">
          <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
        </div>
      </div>
      <div class="form-group">
        <label for="inputPassword3" class="control-label">Password</label>
        <div class="">
          <input type="password" class="form-control" id="inputPassword3" placeholder="Password">
        </div>
      </div>
      <div class="form-group">
        <div class="">
          <div class="checkbox"><label><input type="checkbox"> Remember me</label></div>
        </div>
      </div>
      <div class="form-group">
        <div class="">
          <button type="submit" class="btn btn-default">Sign in</button>
        </div>
      </div>
    </form>
  </div>
</div> -->