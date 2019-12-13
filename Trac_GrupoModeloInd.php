<?php
  session_start();
  if( isset($_POST["grupomodeloind"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");       						                  
      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["grupomodeloind"]);
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
        ///////////////////////////////////////////////
        //    Janela de ajuda mostrando o auto       //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="ajudaauto" ){
          $sql="";
          $sql.="SELECT A.GMP_CODIGO AS TRAC";
          $sql.="      ,GM.GM_NOME AS MODELO";          
          $sql.="      ,A.GMP_CODGP AS GRP";
          $sql.="      ,A.GMP_CODPE AS PE";
          $sql.="      ,COALESCE(FVR.FVR_APELIDO,'...') AS RESPONSAVEL";
          $sql.="      ,A.GMP_NUMSERIE AS SERIE";                    
          $sql.="      ,CONVERT(VARCHAR(10),A.GMP_DTCONFIGURADO,127) AS CONFIGURADO";          
          $sql.="      ,A.GMP_PLACACHASSI AS PLACA_CHASSI";
          $sql.="      ,A.GMP_CODGML AS LOTE";
          $sql.="      ,US.US_APELIDO AS USUARIO";          
          $sql.="  FROM GRUPOMODELOPRODUTO A";          
          $sql.="  LEFT OUTER JOIN GRUPOMODELO GM ON A.GMP_CODGM=GM.GM_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.GMP_CODPEI=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOMODELOLOTE GML ON A.GMP_CODGML=GML.GML_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.GMP_CODUSR=US.US_CODIGO";          
          $sql.=" WHERE (A.GMP_CODIGO='".$lote[0]->codamp."')";  
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR"
                        ,"dados":""
                        ,"erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };  
        ///////////////////////////////////////////////
        //            Transferindo para SUCATA       //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="sucata" ){
          foreach ( $lote as $reg ){
            $sql="";
            $sql.="UPDATE VGRUPOMODELOPRODUTO";
            $sql.="   SET GMP_CODPE='".$reg->gmp_codpe."'";
            $sql.="       ,GMP_ACAO=".$reg->gmp_acao;
            $sql.="       ,GMP_CODUSR=".$_SESSION["usr_codigo"];
            $sql.=" WHERE (GMP_CODIGO=".$reg->gmp_codigo.")";  
            array_push($arrUpdt,$sql);            
            $atuBd = true;
          };
        };
        ///////////////////////////////////////////////
        //                  Transferencia            //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="transferencia" ){
          foreach ( $lote as $reg ){
            $sql="UPDATE VGRUPOMODELOPRODUTO SET GMP_CODPE='".$reg->codpe."',GMP_CODPEI=".$reg->codfvr.",GMP_CODUSR=".$_SESSION["usr_codigo"]." WHERE GMP_CODIGO=".$reg->codgmp; 
            array_push($arrUpdt,$sql);            
            $atuBd = true;
          };
        };
        if( $lote[0]->rotina=="filtrar" ){        
          $sql="";
          $sql.="SELECT A.GMP_CODIGO AS COD_TRAC";
          $sql.="      ,GM.GM_NOME AS MODELO";          
          $sql.="      ,A.GMP_CODGP AS GRP";
          $sql.="      ,A.GMP_CODPE AS PE";
          $sql.="      ,CASE WHEN GMP_CODAUT>0 THEN CONCAT('AUTO ',GMP_CODAUT) ELSE COALESCE(FVR.FVR_APELIDO,'...') END AS RESPONSAVEL";
          $sql.="      ,A.GMP_CODAUT AS AUTO";
          $sql.="      ,A.GMP_NUMSERIE AS SERIE";
          $sql.="      ,A.GMP_SINCARD AS SINCARD";
          $sql.="      ,A.GMP_OPERADORA AS OPERADORA";
          $sql.="      ,A.GMP_FONE AS FONE";
          $sql.="      ,A.GMP_CONTRATO AS CONTRATO";
          $sql.="      ,A.GMP_CODGML AS LOTE";
          $sql.="      ,CASE WHEN A.GMP_STATUS = 1 THEN 'ESTOQUE' WHEN A.GMP_STATUS = 2 THEN 'INSTALADO' WHEN A.GMP_STATUS = 3 THEN 'MANUTENCAO' END AS STATUS";
          $sql.="      ,PE.PE_SUCATA AS SUCATA";
          $sql.="      ,US.US_APELIDO";          
          $sql.="  FROM GRUPOMODELOPRODUTO A";          
          $sql.="  LEFT OUTER JOIN GRUPOMODELO GM ON A.GMP_CODGM=GM.GM_CODIGO";
          $sql.="  LEFT OUTER JOIN PONTOESTOQUE PE ON A.GMP_CODPE=PE.PE_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.GMP_CODPEI=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOMODELOLOTE GML ON A.GMP_CODGML=GML.GML_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.GMP_CODUSR=US.US_CODIGO";          
          $sql.=" WHERE (A.GMP_CODGM='".$lote[0]->codgm."')";          
          $sql.="   AND (GML.GML_DATA>='".$lote[0]->data."')";
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
    <script src="js/clsTab2017.js"></script>        
    <script src="js/jsTable2017.js"></script>
    <script src="js/jsTbl.js"></script>    
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaPontoEstoqueIndF10.js"></script>            
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        document.getElementById("edtDataIni").value  = jsDatas(-30).retDDMMYYYY(); 
        ////////////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_GrupoModelo.php
        ////////////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addInd")).lote[0];
        //localStorage.removeItem("addInd");
        ///////////////////////////////////
        // Guardando os modelos para filtro
        ///////////////////////////////////
        let tblGm=JSON.parse(localStorage.getItem("addTbl")).lote;
        let ceOpt;
        let contador=0;
        tblGm.forEach(function(reg){
          ceOpt 	= document.createElement("option");        
          ceOpt.value = reg.cod;
          ceOpt.text  = reg.des;
          if( jsNmrs(pega.codgm).inteiro().ret()==jsNmrs(reg.cod).inteiro().ret() ){
            ceOpt.setAttribute("selected","selected");
          };
          document.getElementById("cbModelo").appendChild(ceOpt);
        });
        //
        //
        jsGmi={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "TRAC"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "MODELO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "80"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "GRP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "PE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"funcCor"        : "(objCell.innerHTML=='SUC'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"                      
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "RESPONSAVEL"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"truncate"       : "S"                                            
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "AUTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]
                      ,"funcCor"        : "(objCell.innerHTML!='000000'  ? objCell.classList.add('corAzul') : objCell.classList.remove('corAzul'))"    
                      ,"func"           : "abrirAutoClick(this.innerHTML);"
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "SERIE"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"funcCor"        : "(objCell.innerHTML=='NSA'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "SINCARD"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"funcCor"        : "(objCell.innerHTML=='NSA'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "OPERADORA"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "20"
                      ,"funcCor"        : "(objCell.innerHTML=='NSA'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "FONE"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"funcCor"        : "(objCell.innerHTML=='NSA'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "CONTRATO"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"funcCor"        : "(objCell.innerHTML=='NSA'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "LOTE"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "STATUS"
                      ,"fieldType"      : "str"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "SUCATA"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
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
             {"texto":"Sucata"        ,"name":"horSucata"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-trash-o"     }  
            ,{"texto":"Excel"         ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }  
            ,{"texto":"Imprimir"      ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"        }                    
            ,{"texto":"Fechar"        ,"name":"horFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"       }
          ] 

          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmGmi"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaGmi"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmGmi"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnGmi"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblGmi"                  // Nome da table
          ,"prefixo"        : "Gmi"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "132em"                   // Tamanho da table
          ,"height"         : "60em"                    // Altura da table
          ,"nChecks"        : true                      // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                   // Se tiver menu esquerdo
          ,"relTitulo"      : "ESTOQUE"                 // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"formPassoPasso" : "*"                       // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "GRUPO"                   // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
        }; 
        if( objGmi === undefined ){  
          objGmi=new clsTable2017("objGmi");
        };  
        objGmi.montarHtmlCE2017(jsGmi);
        $doc("dPaifrmGmi").style.float="none";
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsGmi,["PE","SERIE","SUCATA","TRAC","USUARIO"]);
        btnFiltrarClick();
      });
      //
      var objGmi;                     // Obrigatório para instanciar o JS Semestral
      var jsGmi;                      // Obj principal da classe clsTable2017
      var objPadF10;                  // Obrigatório para instanciar o JS PadraoF10            
      var objPeiF10;                  // Obrigatório para instanciar o JS FabricanteF10                  
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var clsErro;                    // Classe para erros 
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var pega;                       // Recuperar localStorage      
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario                  
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      
      function abrirAutoClick(codamp){
        if( codamp>0 ){
          clsJs   = jsString("lote");  
          clsJs.add("rotina"  , "ajudaauto"         );
          clsJs.add("login"   , jsPub[0].usr_login  );
          clsJs.add("codamp"  , codamp              );
          fd = new FormData();
          fd.append("grupomodeloind" , clsJs.fim());
          msg     = requestPedido("Trac_GrupoModeloInd.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            let clsCode = new concatStr();  
            clsCode.concat("<div id='dPaiChk' class='divContainerTable' style='height: 26.2em; width: 51em;border:none'>");
            clsCode.concat("<table id='tblChk' class='fpTable' style='width:100%;'>");
            clsCode.concat("  <thead class='fpThead'>");
            clsCode.concat("    <tr>");
            clsCode.concat("      <th class='fpTh' style='width:30%'>CAMPO</th>");
            clsCode.concat("      <th class='fpTh' style='width:70%'>DESCRICAO</th>");
            clsCode.concat("    </tr>");
            clsCode.concat("  </thead>");
            clsCode.concat("  <tbody id='tbody_tblChk'>");
            //////////////////////
            // Preenchendo a table
            //////////////////////  
            let arr=[];
            arr.push({cod:"AUTO"          ,des:retPhp[0]["dados"][0]["TRAC"]}         );
            arr.push({cod:"MODELO"        ,des:retPhp[0]["dados"][0]["MODELO"]}       );
            arr.push({cod:"GRUPO"         ,des:retPhp[0]["dados"][0]["GRP"]}          );
            arr.push({cod:"ESTOQUE"       ,des:retPhp[0]["dados"][0]["PE"]}           );
            arr.push({cod:"RESPONSAVEL"   ,des:retPhp[0]["dados"][0]["RESPONSAVEL"]}  );
            arr.push({cod:"SERIE"         ,des:retPhp[0]["dados"][0]["SERIE"]}        );
            arr.push({cod:"CONFIGURADO"   ,des:retPhp[0]["dados"][0]["CONFIGURADO"]}  );
            arr.push({cod:"PLACA_CHASSI"  ,des:retPhp[0]["dados"][0]["PLACA_CHASSI"]} );
            arr.push({cod:"USUARIO"       ,des:retPhp[0]["dados"][0]["USUARIO"]}      );
            arr.forEach(function(reg){
              clsCode.concat("    <tr class='fpBodyTr'>");
              clsCode.concat("      <td class='fpTd textoCentro'>"+reg.cod+"</td>");
              clsCode.concat("      <td class='fpTd'>"+reg.des+"</td>");
              clsCode.concat("    </tr>");
            });
            //////  
            // Fim
            //////
            clsCode.concat("  </tbody>");        
            clsCode.concat("</table>");
            clsCode.concat("</div>"); 
            janelaDialogo(
              { height          : "37em"
                ,body           : "16em"
                ,left           : "300px"
                ,top            : "60px"
                ,tituloBarra    : "Ajuda para auto"
                ,code           : clsCode.fim()
                ,width          : "53em"
                ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
              }
            );  
          };
        }; 
      };  

      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "filtrar"                                 );
        clsJs.add("login"   , jsPub[0].usr_login                        );
        clsJs.add("data"    , jsDatas("edtDataIni").retMMDDYYYY()       );
        clsJs.add("codgm"   , document.getElementById("cbModelo").value );
        fd = new FormData();
        fd.append("grupomodeloind" , clsJs.fim());
        msg     = requestPedido("Trac_GrupoModeloInd.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsGmi.registros=objGmi.addIdUnico(retPhp[0]["dados"]);
          objGmi.ordenaJSon(jsGmi.indiceTable,false);  
          objGmi.montarBody2017();
          $doc("spnQtos").innerHTML=(retPhp[0]["dados"]).length;
        };  
      };
      ////////////////////////
      // Transformar em sucata
      ////////////////////////
      function horSucataClick(){
        try{
          //clsChecados = objGmi.gerarJson("n");
          //chkds       = clsChecados.gerar();
          
          ////////////////////////////////
          // Preparando para enviar ao Php
          ////////////////////////////////
          chkds=objGmi.gerarJson("n").gerar();                    
          msg         = "ok";
          clsJs       = jsString("lote");
          chkds.forEach(function(reg){
            if( reg.SUCATA != "S" )
              throw "PONTO DE ESTOQUE "+reg.PE+" NÃO ACEITA TRANSFERÊNCIA PARA SUCATA!";
            if( reg.PE == "SUC" )
              throw "PRODUTO "+reg.TRAC+" JA ESTA NO PONTO DE ESTOQUE SUCATA!";
            
            clsJs.add("rotina"      , "sucata"            );              
            clsJs.add("login"       , jsPub[0].usr_login  );
            clsJs.add("gmp_acao"    , 2                   );                                      
            clsJs.add("gmp_codigo"  , reg.TRAC            );            
            clsJs.add("gmp_codpe"   , "SUC"               );              
          });
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("grupomodeloind" , clsJs.fim());
          msg=requestPedido("Trac_GrupoModeloInd.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("gmi",retPhp[0].erro,{cabec:"Aviso"});
          } else {  
            /////////////////////////////////////////////////
            // Atualizando a grade
            /////////////////////////////////////////////////
            let tbl = tblGmi.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
            if( nl>0 ){
              for(let lin=0 ; (lin<nl) ; lin++){
                chkds.forEach(function(reg){
                  if( jsNmrs(reg.TRAC).inteiro().ret() == jsNmrs(tbl.rows[lin].cells[objCol.TRAC].innerHTML).inteiro().ret() ){
                    tbl.rows[lin].cells[objCol.PE].innerHTML="SUC";
                    tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                    tbl.rows[lin].cells[objCol.PE].classList.add("corAlterado");
                    /////////////////////////////////////////////////
                    // Atualizando a grade em Trac_GrupoModelo.php //
                    /////////////////////////////////////////////////
                    let el  = window.opener.document.getElementById("tblGm");
                    el.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                      if( parseInt(cbModelo.value)==jsNmrs(row.cells[pega.CODIGO].innerHTML).inteiro().ret() ){
                        row.cells[pega.ESTOQUE].innerHTML = ( jsNmrs(row.cells[pega.ESTOQUE].innerHTML).inteiro().ret() - 1 );
                        row.cells[pega.SUC].innerHTML     = ( jsNmrs(row.cells[pega.SUC].innerHTML).inteiro().ret() + 1 ); 
                      };
                    });    
                  };
                });  
              };    
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      //////////////////////////////
      //  AJUDA PARA PONTOESTOQUE //
      //////////////////////////////
      function peFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function peF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodPei"
                      ,topo:100
                      ,tableBd:"PONTOESTOQUE"
                      ,fieldCod:"A.PE_CODIGO"
                      ,fieldDes:"A.PE_NOME"
                      ,fieldAtv:"A.PE_ATIVO"
                      ,typeCod :"str" 
                      ,divWidth:"35%"
                      ,tamColNome:"32em"
                      ,where: "AND (A.PE_CODIGO NOT IN('SUC','EST'))"
                      ,tbl:"tblPe"}
        );
      };
      function RetF10tblPe(arr){
        document.getElementById("edtCodPe").value  = arr[0].CODIGO;
        document.getElementById("edtDesPe").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodPe").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodPeBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodPei"
                                  ,topo:100
                                  ,tableBd:"PONTOESTOQUE"
                                  ,fieldCod:"A.PE_CODIGO"
                                  ,fieldDes:"A.PE_NOME"
                                  ,fieldAtv:"A.PE_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblPe"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO             );
          document.getElementById("edtDesPe").value  = ( ret.length == 0 ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////////////////////////
      //  AJUDA PARA PONTO DE ESTOQUE INDIVIDUAL //
      /////////////////////////////////////////////
      function peiFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function peiF10Click(obj){ 
        fPontoEstoqueIndF10(0,obj.id,"trConfirmar",100,{codpe: document.getElementById("edtCodPe").value,ativo:"S" } ); 
      };
      function RetF10tblPei(arr){
        document.getElementById("edtCodPei").value      = arr[0].CODIGO;
        document.getElementById("edtDesPei").value      = arr[0].DESCRICAO;
        document.getElementById("edtCodPei").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codPeiBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fPontoEstoqueIndF10(1,obj.id,"trConfirmar",100,
            {codfvr  : elNew
             ,codpe  : document.getElementById("edtCodPe").value
             ,ativo  : "S"} 
            ); 
          //  
          document.getElementById(obj.id).value           = ( arr.length == 0 ? "0000"            : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          document.getElementById("edtDesPei").value      = ( arr.length == 0 ? "*"               : arr[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )                       );
        };
      };
      //////////////////////
      // Fechar formulario
      //////////////////////
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
              <span id="spnTitulo" class="infoBoxText">Individual</span>
              <span id="spnQtos" class="infoBoxNumber"></span>
            </div>
          </div>
        </div>  
        <div class="campotexto campo10" style="margin-top:2px;">
          <input class="campo_input" id="edtDataIni" 
                                     placeholder="##/##/####"                 
                                     OnKeyUp="mascaraNumero('##/##/####',this,event,'dig')"
                                     maxlength="10" type="text" />
          <label class="campo_label" for="edtDataIni">A PARTIR</label>
        </div>
        <div class="campotexto campo25" style="margin-top:3px;margin-left:3px;">
          <select class="campo_input_combo" id="cbModelo">
          </select>
          <label class="campo_label campo_required" for="cbModelo">MODELO</label>
        </div>
        <div onClick="btnFiltrarClick();"    class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;float:left;"><i class="fa fa-folder-open"> Abrir</i></div>                
      </div>
      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <section>
        <section id="sctnGmi">
        </section>  
      </section>
      <form method="post" class="center" id="frmGmi" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
      </form>
    </div>
    <!--
    Buscando o historico do auto
    -->
    <section id="collapseSectionProduto" class="section-collapse" style="margin-top:-1em;">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a class="btn btn-default disabled">Buscar</a>
            <button id="abreProduto" class="btn btn-primary" 
                                     data-toggle="collapse" 
                                     data-target="#evtAbreProduto" 
                                     aria-expanded="true" 
                                     aria-controls="evtAbreProduto" 
                                     type="button">Historico produto</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreProduto" aria-expanded="false" role="presentation">
          <div id="cllServico" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblAuto" class="alert-info">Mostrando historico do produto</label>
          </div>
        </div>
      </div>
    </section>
    
    <script>
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                                                PopUp                                                       //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreProduto  = new Collapse($doc('abreProduto'));
      abreProduto.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreProduto = document.getElementById('evtAbreProduto');
      collapseAbreProduto.addEventListener('show.bs.collapse', function(el){ 
        try{
          chkds=objGmi.gerarJson("1").gerar();
          if( chkds[0].TRAC == 0 )
            throw "AUTO NÃO LOCALIZADO PARA HISTORICO!";  
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "detAuto"           );  // Detalhe do auto
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("codgmp"      , chkds[0].TRAC     );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contratoproduto" , clsJs.fim());
          msg=requestPedido("Trac_ContratoProduto.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            $doc("cllServico").innerHTML=retPhp[0]["dados"];
            $doc("lblAuto").innerHTML="Mostrando historico do produto <b>"+chkds[0].TRAC+"</b>";
            abreProduto.status="ok";
          };  
        }catch(e){
          abreProduto.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbreProduto.addEventListener('shown.bs.collapse', function(){ 
        if( abreProduto.status=="err" )
          abreProduto.hide();
      },false);
    </script>
  </body>
</html>