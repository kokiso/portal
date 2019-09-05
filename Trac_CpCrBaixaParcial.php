<?php
  session_start();
  if( isset($_POST["cpcrbaixaparcial"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");      
      require("classPhp/validaCampo.class.php"); 
      
      function fncPg($a, $b) {
       return $a["PDR_NOME"] > $b["PDR_NOME"];
      };

      function fncPt($a, $b) {
       return $a["PT_NOME"] > $b["PT_NOME"];
      };
      
      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["cpcrbaixaparcial"]);
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
        $rotina   = $lote[0]->rotina;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        /////////////////////////
        // Baixa
        /////////////////////////
        if( $lote[0]->rotina=="baixa" ){
          $sql="";
          $sql.="UPDATE VPAGAR"; 
          $sql.="   SET PGR_DATAPAGA='".$lote[0]->dtbaixa."'";
          $sql.="       ,PGR_CHEQUE='".$lote[0]->doctobaixa."'";
          $sql.="       ,PGR_VLRBAIXA='".$lote[0]->vlrsaldo."'";
          $sql.="       ,PGR_CODALT=6";
          $sql.="       ,PGR_CODUSR=".$_SESSION["usr_codigo"];
          $sql.=" WHERE PGR_LANCTO=".$lote[0]->lancto;
          array_push($arrUpdt,$sql);  
          /////////////////////////////////////////////////////////////////////////////////
          // Como os dados ja estaum no banco de dados apenas recupero para inserir o saldo
          /////////////////////////////////////////////////////////////////////////////////
          $sql="";
          $sql.="SELECT PGR_BLOQUEADO";
          $sql.=",PGR_CODBNC";          
          $sql.=",PGR_CODFVR";
          $sql.=",PGR_CODFC";
          $sql.=",PGR_CODTD";
          $sql.=",CONVERT(VARCHAR(10),PGR_VENCTO,127) AS PGR_VENCTO";                                        
          $sql.=",PGR_DOCTO";
          $sql.=",CONVERT(VARCHAR(10),PGR_DTDOCTO,127) AS PGR_DTDOCTO";                              
          $sql.=",PGR_CODPTT";
          $sql.=",PGR_MASTER";
          $sql.=",PGR_OBSERVACAO";
          $sql.=",PGR_PARCELA";
          $sql.=",PGR_CODPTP";
          $sql.=",PGR_INDICE";
          $sql.=",PGR_CODPT";
          $sql.=",PGR_VLREVENTO";
          $sql.=",PGR_VLRPARCELA";
          $sql.=",PGR_VLRLIQUIDO";
          $sql.=",PGR_CODCC";
          $sql.=",PGR_CODSNF";
          $sql.=",PGR_APR";
          $sql.=",PGR_CODEMP";
          $sql.=",PGR_CODFLL";
          $sql.=",PGR_LOTECNAB";
          $sql.=",PGR_VERDIREITO";
          $sql.=",PGR_CODALT";
          $sql.=",PGR_CODCMP";
          $sql.=",PGR_REG";
          $sql.=",PR.PR_CODCC";
          $sql.=",PR.PR_DEBCRE";
          $sql.=" FROM PAGAR"; 
          $sql.=" LEFT OUTER JOIN PAGARRATEIO PR ON PGR_LANCTO=PR.PR_LANCTO"; 
          $sql.=" WHERE PGR_LANCTO=".$lote[0]->lancto;
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          $tbl=$retCls["dados"][0];
          ////////////////////
          // Gravando na PAGAR
          ////////////////////  
          $lancto=$classe->generator("PAGAR"); 
          $sql="";
          $sql="INSERT INTO VPAGAR(";
          $sql.="PGR_LANCTO";
          $sql.=",PGR_BLOQUEADO";
          $sql.=",PGR_CODBNC";
          $sql.=",PGR_CODFVR";
          $sql.=",PGR_CODFC";
          $sql.=",PGR_CODTD";
          $sql.=",PGR_VENCTO";
          $sql.=",PGR_DOCTO";
          $sql.=",PGR_DTDOCTO";
          $sql.=",PGR_CODPTT";
          $sql.=",PGR_MASTER";
          $sql.=",PGR_OBSERVACAO";
          $sql.=",PGR_CODPTP";
          $sql.=",PGR_CODPT";
          $sql.=",PGR_VLREVENTO";
          $sql.=",PGR_VLRPARCELA";
          $sql.=",PGR_VLRDESCONTO";          
          $sql.=",PGR_CODCC";
          $sql.=",PGR_CODSNF";
          $sql.=",PGR_APR";
          $sql.=",PGR_CODEMP";
          $sql.=",PGR_CODFLL";
          $sql.=",PGR_VERDIREITO";
          $sql.=",PGR_CODCMP";
          $sql.=",PGR_REG";
          $sql.=",PGR_CODUSR) VALUES(";
          $sql.="'$lancto'";                          // LANCTO
          $sql.=",'" .$tbl["PGR_BLOQUEADO"]."'";      // BLOQUEADO
          $sql.=","  .$tbl["PGR_CODBNC"];             // CODBNC
          $sql.=","  .$tbl["PGR_CODFVR"];             // CODFVR
          $sql.=",'" .$tbl["PGR_CODFC"]."'";          // CODFC
          $sql.=",'" .$tbl["PGR_CODTD"]."'";          // CODTD
          $sql.=",'" .$tbl["PGR_VENCTO"]."'";         // VENCTO
          $sql.=",'" .$tbl["PGR_DOCTO"]."'";          // DOCTO
          $sql.=",'" .$tbl["PGR_DTDOCTO"]."'";        // DTDOCTO
          $sql.=",'" .$tbl["PGR_CODPTT"]."'";         // CODPTT
          $sql.=","  .$tbl["PGR_MASTER"];             // MASTER
          $sql.=",'" .$tbl["PGR_OBSERVACAO"]."'";     // OBSERVACAO
          $sql.=",'" .$tbl["PGR_CODPTP"]."'";         // CODPTP
          $sql.=","  .$tbl["PGR_CODPT"];              // CODPT
          $sql.=",'" .$tbl["PGR_VLREVENTO"]."'";      // VLREVENTO
          $sql.=",'" .$tbl["PGR_VLRPARCELA"]."'";     // VLRPARCELA
          $sql.=",'" .$lote[0]->vlrbaixa."'";         // VLRDESCONTO
          $sql.=",'" .$tbl["PGR_CODCC"]."'";          // CODCC
          $sql.=","  .$tbl["PGR_CODSNF"];             // CODSNF
          $sql.=",'" .$tbl["PGR_APR"]."'";            // APR
          $sql.=","  .$tbl["PGR_CODEMP"];             // CODEMP
          $sql.=","  .$tbl["PGR_CODFLL"];             // CODFLL
          $sql.=","  .$tbl["PGR_VERDIREITO"];         // VERDIREITO
          $sql.=","  .$tbl["PGR_CODCMP"];             // CODCMP
          $sql.=",'" .$tbl["PGR_REG"]."'";            // REG
          $sql.=","  .$_SESSION["usr_codigo"];        // CODUSR
          $sql.=")";
          array_push($arrUpdt,$sql);  
          //
          /////////////////////
          // Gravando na RATEIO
          /////////////////////
          $sql="";
          $sql.="INSERT INTO VRATEIO(";
          $sql.="RAT_LANCTO";                
          $sql.=",RAT_CODCC";
          $sql.=",RAT_DEBITO";
          $sql.=",RAT_CREDITO";
          $sql.=",RAT_CODEMP";
          $sql.=",RAT_CODCMP";
          $sql.=",RAT_CONTABIL";
          $sql.=",RAT_CODUSR) VALUES(";
          $sql.="'$lancto'";                                              // RAT_LANCTO
          $sql.=",'" .$tbl["PR_CODCC"]."'";                               // RAT_CODCC
          $sql.="," .( $tbl["PR_DEBCRE"]=="D" ? $lote[0]->vlrsaldo : 0 ); // RAT_DEBITO
          $sql.="," .( $tbl["PR_DEBCRE"]=="C" ? $lote[0]->vlrsaldo : 0 ); // RAT_CREDITO
          $sql.="," .$tbl["PGR_CODEMP"];                                  // RAT_CODEMP
          $sql.="," .$tbl["PGR_CODCMP"];                                  // RAT_CODCMP
          $sql.=",'S'";                                                   // RAT_CONTABIL
          $sql.=","  .$_SESSION["usr_codigo"];                            // RAT_CODUSR
          $sql.=")";
          array_push($arrUpdt,$sql);            
          $atuBd = true;
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
<script language="javascript" type="text/javascript"></script>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <title>Baixa parcial</title>
    <style id="meuCss">
    </style>  
    <!-- 
    -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <link rel="stylesheet" href="css/Acordeon.css">
    <script src="js/js2017.js"></script>
    <script src="js/clsTab2017.js"></script>        
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>    
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        /////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_CpCr.php
        /////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addAlt")).lote[0];
        localStorage.removeItem("addAlt");
        document.getElementById("edtCodBnc").value      = jsNmrs(pega.codbnc).emZero(4).ret();        
        document.getElementById("edtCodFc").value       = pega.codfc;        
        document.getElementById("edtCodFvr").value      = jsNmrs(pega.codfvr).emZero(4).ret();        
        document.getElementById("edtCodTd").value       = pega.codtd;        
        document.getElementById("edtDesBnc").value      = pega.desbnc;        
        document.getElementById("edtDesFc").value       = pega.desfc;
        document.getElementById("edtDesFvr").value      = pega.desfvr;        
        document.getElementById("edtDesTd").value       = pega.destd;
        document.getElementById("edtDocto").value       = pega.docto;
        document.getElementById("edtDtDocto").value     = jsDatas(pega.dtdocto).retDDMMYYYY();
        document.getElementById("edtLancto").value      = pega.lancto;        
        document.getElementById("edtObservacao").value  = pega.observacao;
        document.getElementById("edtVencto").value      = pega.vencto;        
        document.getElementById("edtVlrBaixa").value    = "0,00";         
        document.getElementById("edtVlrSaldo").value    = "0,00";
        document.getElementById("edtVlrParcela").value  = jsNmrs(pega.valor).abs().real().ret(); 
        document.getElementById("edtDoctoBaixa").value  = "BX"+pega.lancto;
        document.getElementById("edtDtBaixa").value     = jsDatas(0).retDDMMYYYY();
        

        var jsTab={
          "pai"         : "appAba"  //Onde vai ser appendado o html gerado
          ,"idStyle"    : "meuCss"  //id da tag style
          ,"opcExcluir" : "N"       //Se vai existir a opção excluir        
          ,"abas":[ 
             {"id":0  ,"nomeAba"      : "parcela" 
                      ,"labelAba"     : "Titulo"
                      ,"table"        : "tblTtl"
                      ,"widthTable"   : "45em" 
                      ,"heightTable"  : "18em"                     
                      ,"widthAba"     : "12em" 
                      ,"rolagemVert"  : true 
                      ,"somarCols":[3]
                      ,"head":[  { "labelCol"   : "LANCTO"     
                                  ,"width"      : "4em"  
                                  ,"fieldType"  : "int"
                                  ,"formato"    : "i3"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "FAVORECIDO"   
                                  ,"width"      : "10em"  
                                  ,"fieldType"  : "str"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "VENCTO"   
                                  ,"width"      : "6em"  
                                  ,"fieldType"  : "dat"
                                  ,"editar"     : "N"
                                  ,"classe"     : "edtData"}
                                ,{ "labelCol"   : "VALOR"    
                                  ,"width"      : "8em" 
                                  ,"fieldType"  : "flo"
                                  ,"editar"     : "N"
                                  ,"classe"     : "edtDireita"}
                              ]
             }
          ]
        };  
        minhaAba=new clsTab(jsTab);
        minhaAba.htmlTab();  

        
        minhaAba.novoRegistro("tblTtl",
          [ document.getElementById("edtLancto").value
            ,document.getElementById("edtDesFvr").value
            ,document.getElementById("edtVencto").value
            ,document.getElementById("edtVlrParcela").value
          ]
        );
        
        
        //
        document.getElementById("edtCodBnc").foco();
        
      });
      //
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var objPadF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var pega;                       // Recuperar localStorage;
      var minhaAba;
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      ///////////////////////////////
      //     AJUDA PARA BANCO      //
      ///////////////////////////////
      function bncFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bncF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtObservacao"
                      ,topo:100
                      ,tableBd:"BANCO"
                      ,fieldCod:"A.BNC_CODIGO"
                      ,fieldDes:"A.BNC_NOME"
                      ,fieldAtv:"A.BNC_ATIVO"
                      ,typeCod :"int" 
                      ,divWidth:"36%"
                      ,tbl:"tblBnc"}
        );
      };
      function RetF10tblBnc(arr){
        document.getElementById("edtCodBnc").value  = arr[0].CODIGO;
        document.getElementById("edtDesBnc").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodBnc").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codBncBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtObservacao"
                                  ,topo:100
                                  ,tableBd:"BANCO"
                                  ,fieldCod:"A.BNC_CODIGO"
                                  ,fieldDes:"A.BNC_NOME"
                                  ,fieldAtv:"A.BNC_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblBnc"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "BOL"  : ret[0].CODIGO                );
          document.getElementById("edtDesBnc").value  = ( ret.length == 0 ? "BOLETO"      : ret[0].DESCRICAO      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "BOL" : ret[0].CODIGO ));
        };
      };
      
      function fncGravar(){
        try{       
          //
          document.getElementById("edtCodBnc").value     = document.getElementById("edtCodBnc").value.soNumeros();
        
          msg = new clsMensagem("Erro");
          msg.floMaiorIgualZero("VALOR_PARCELA" , document.getElementById("edtVlrParcela").value  );
          msg.floMaiorIgualZero("VALOR_BAIXA"   , document.getElementById("edtVlrBaixa").value    );          
          msg.floMaiorIgualZero("VALOR_SALDO"   , document.getElementById("edtVlrSaldo").value    );          
          msg.intMaiorZero("COD_BANCO"          , document.getElementById("edtCodBnc").value      );
          msg.notNull("NOME_BANCO"              , document.getElementById("edtDesBnc").value      );
          //
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            clsJs = jsString("lote");
            clsJs.add("rotina"     , "baixa"                                         );
            clsJs.add("login"      , jsPub[0].usr_login                              );
            clsJs.add("codbnc"     , document.getElementById("edtCodBnc").value      );
            clsJs.add("lancto"     , jsNmrs("edtLancto").inteiro().ret()             );
            clsJs.add("vlrbaixa"   , jsNmrs("edtVlrBaixa").dolar().ret()             );
            clsJs.add("vlrsaldo"   , jsNmrs("edtVlrSaldo").dolar().ret()             );
            clsJs.add("dtbaixa"    , jsDatas("edtDtBaixa").retMMDDYYYY()             );
            clsJs.add("doctobaixa" , document.getElementById("edtDoctoBaixa").value  );
           
            ///////////////////////
            // Enviando para gravar
            ///////////////////////
            envPhp=clsJs.fim();

            fd = new FormData();
            fd.append("cpcrbaixaparcial" , envPhp);
            msg=requestPedido("Trac_CpCrBaixaParcial.php",fd); 
            retPhp=JSON.parse(msg);

            if( retPhp[0].retorno != "OK" ){
              gerarMensagemErro("cad",retPhp[0].erro,{cabec:"Aviso"});    
            } else {  
              /////////////////////////////////////////////////
              // Atualizando a grade
              /////////////////////////////////////////////////
              let el  = window.opener.document.getElementById("tblPgr");
              let tbl = el.getElementsByTagName("tbody")[0];
              let nl  = tbl.rows.length;
              for(let lin=0 ; (lin<nl) ; lin++){
                if( jsNmrs("edtLancto").inteiro().ret() == jsNmrs(tbl.rows[lin].cells[parseInt(pega.LANCTO)].innerHTML).inteiro().ret() ){
                  tbl.rows[lin].cells[parseInt(pega.VALOR)].innerHTML       = document.getElementById("edtVlrBaixa").value;
                  tbl.rows[lin].cells[parseInt(pega.BAIXA)].innerHTML       = document.getElementById("edtDtBaixa").value;
                  tbl.rows[lin].cells[parseInt(pega.DOCBAIXA)].innerHTML    = document.getElementById("edtDoctoBaixa").value;
                  break;
                };
              };  
              window.close();
            };  
          };  
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
      function fncSaldo(){
        let vlrParcela  = jsNmrs("edtVlrParcela").dolar().ret();
        let vlrBaixa    = jsNmrs("edtVlrBaixa").dolar().ret();
        document.getElementById("edtVlrSaldo").value=jsNmrs((vlrParcela-vlrBaixa)).real().ret();
        
      }
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Baixa parcial</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmPgr" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 550px;">
          <div class="campotexto campo100">
            <h2>Informe</h2>
          </div>
          <!--
          -->
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtCodFvr" type="text" disabled />
            <label class="campo_label" for="edtCodFvr">FAVORECIDO:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesFvr" type="text" disabled />
            <label class="campo_label" for="edtDesFvr">NOME</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtDocto" type="text" disabled />
            <label class="campo_label" for="edtDocto">DOCUMENTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtDtDocto" type="text" disabled />
            <label class="campo_label" for="edtDtDocto">EMISSÂO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtVencto" type="text" disabled />
            <label class="campo_label" for="edtVencto">VENCTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo edtDireita" id="edtVlrParcela" type="text" disabled />
            <label class="campo_label" for="edtVlrParcela">VALOR:</label>
          </div>
          <!--
          -->
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtCodTd" type="text" disabled />
            <label class="campo_label" for="edtCodTd">TD:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesTd" type="text" disabled />
            <label class="campo_label" for="edtDesTd">TIPO DOCTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtCodFc" type="text" disabled />
            <label class="campo_label" for="edtCodFc">FC:</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtDesFc" type="text" disabled />
            <label class="campo_label" for="edtDesFc">FORMA COBR:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtLancto" type="text" disabled />
            <label class="campo_label" for="edtLancto">LANCTO:</label>
          </div>
          <div class="campotexto campo100">
            <input class="campo_input_titulo" id="edtObservacao" type="text" disabled />
            <label class="campo_label" for="edtObservacao">OBSERVAÇÃO:</label>
          </div>
          <!--
          -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodBnc"
                                                onBlur="codBncBlur(this);" 
                                                onFocus="bncFocus(this);" 
                                                onClick="bncF10Click(this);"
                                                data-oldvalue="0000"
                                                autocomplete="off"
                                                maxlength="4"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodBnc">BANCO:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesBnc" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBnc">BANCO_NOME:</label>
          </div>
          <div class="campotexto campo12">
            <input class="campo_input input" id="edtDtBaixa" 
                                             placeholder="##/##/####" 
                                             data-oldvalue=""                                             
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtDtBaixa">DATA BAIXA:</label>
          </div>
          <div class="campotexto campo12">
            <input class="campo_input input" id="edtDoctoBaixa" 
                                             data-oldvalue=""
                                             type="text" 
                                             maxlength="12"/>
            <label class="campo_label campo_required" for="edtDoctoBaixa">DOCUMENTO:</label>
          </div>
          
          <div class="campotexto campo12">
            <input class="campo_input edtDireita" id="edtVlrBaixa" 
                                                  onBlur="fncCasaDecimal(this,2);fncSaldo();"            
                                                  maxlength="15" 
                                                  type="text" />
            <label class="campo_label campo_required" for="edtVlrBaixa">VLR BAIXA:</label>
          </div>
          <div class="campotexto campo12">
            <input class="campo_input_titulo edtDireita" id="edtVlrSaldo" 
                                                  onBlur="fncCasaDecimal(this,2);fncDescMulta();"            
                                                  maxlength="15" 
                                                  type="text" disabled />
            <label class="campo_label campo_required" for="edtVlrBaixa">SALDO:</label>
          </div>
          <div onClick="fncGravar();" class="btnImagemEsq bie12 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>          
          <div onClick="window.close();" class="btnImagemEsq bie12 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>          
          
          <div class="campotexto campo100">
            <div id="appAba" class="campotexto campo50" style="height:20em;float:left;position:relative;">
            </div>            
          </div>
        </div> 
      </form>
    </div>
  </body>
</html>