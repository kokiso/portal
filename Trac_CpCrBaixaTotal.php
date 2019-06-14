<?php
  session_start();
  if( isset($_POST["cpcrbaixatotal"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");      
      require("classPhp/validaCampo.class.php"); 
      /*
      function fncPg($a, $b) {
       return $a["PDR_NOME"] > $b["PDR_NOME"];
      };

      function fncPt($a, $b) {
       return $a["PT_NOME"] > $b["PT_NOME"];
      };
      */
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["cpcrbaixatotal"]);
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
        if( $lote[0]->rotina=="baixatotal" ){
          foreach ( $lote as $reg ){
            $sql="";
            $sql.="UPDATE VPAGAR"; 
            $sql.="   SET PGR_DATAPAGA='".$reg->dtbaixa."'";
            $sql.="       ,PGR_CHEQUE='".$reg->doctobaixa."'";
            $sql.="       ,PGR_CODBNC='".$lote[0]->codbnc."'";
            $sql.="       ,PGR_CODUSR=".$_SESSION["usr_codigo"];
            $sql.=" WHERE PGR_LANCTO=".$reg->lancto;
            array_push($arrUpdt,$sql);              
          };
          $atuBd = true;
        };  
        /////////////////////////
        // Excluir baixa
        /////////////////////////
        if( $lote[0]->rotina=="excluirbaixa" ){
          foreach ( $lote as $reg ){
            $sql="";
            $sql.="UPDATE VPAGAR"; 
            $sql.="   SET PGR_DATAPAGA=NULL";
            $sql.="       ,PGR_CHEQUE=NULL";
            $sql.="       ,PGR_CODUSR=".$_SESSION["usr_codigo"];
            $sql.=" WHERE PGR_LANCTO=".$reg->lancto;
            array_push($arrUpdt,$sql);              
          };
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
        pega=JSON.parse(localStorage.getItem("addAlt")).lote;
        localStorage.removeItem("addAlt");
        document.getElementById("edtCodBnc").value      = jsNmrs(pega[0].codbnc).emZero(4).ret();        
        document.getElementById("edtDesBnc").value      = pega[0].desbnc;        
        document.getElementById("edtDoctoBaixa").value  = "BX"+pega[0].lancto;
        document.getElementById("edtDtBaixa").value     = jsDatas(0).retDDMMYYYY();
        switch( pega[0].rotina ){
          case "baixatotal":
            document.getElementById("h2Rotina").innerHTML="BAIXA TOTAL";
            document.getElementById("imgOk").innerHTML="Baixar";            
            document.getElementById("edtCodBnc").foco();        
            break;
          case "excluirbaixa":
            document.getElementById("h2Rotina").innerHTML="EXCLUIR BAIXA";
            document.getElementById("imgOk").innerHTML="Excluir";
            jsCmpAtivo("edtCodBnc").remove("campo_input inputF10").add("campo_input_titulo").disabled(true);
            jsCmpAtivo("edtDtBaixa").remove("campo_input").add("campo_input_titulo").disabled(true);
            jsCmpAtivo("edtDoctoBaixa").remove("campo_input").add("campo_input_titulo").disabled(true);
            break;
        };

        var jsTab={
          "pai"         : "appAba"  //Onde vai ser appendado o html gerado
          ,"idStyle"    : "meuCss"  //id da tag style
          ,"opcExcluir" : "N"       //Se vai existir a opção excluir        
          ,"abas":[ 
             {"id":0  ,"nomeAba"      : "parcela" 
                      ,"labelAba"     : "Titulo"
                      ,"table"        : "tblTtl"
                      ,"widthTable"   : "75em" 
                      ,"heightTable"  : "38em"                     
                      ,"widthAba"     : "12em" 
                      ,"rolagemVert"  : true 
                      ,"somarCols":[4]
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
                                ,{ "labelCol"   : "BANCO"   
                                  ,"width"      : "20em"  
                                  ,"fieldType"  : "str"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
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
        let vlrTotal=0;
        pega.forEach(function(reg){
          minhaAba.novoRegistro("tblTtl",
            [ reg.lancto
              ,reg.desfvr
              ,reg.vencto
              ,reg.desbnc
              ,reg.valor
            ]
          );
          vlrTotal+=jsNmrs(reg.valor).abs().dolar().ret(); 
        });    
        document.getElementById("edtVlrBaixa").value = jsNmrs(vlrTotal).abs().real().ret();;         
        //
      });
      //
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
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
                      ,foco:"edtDoctoBaixa"
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
                                  ,foco:"edtDoctoBaixa"
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
          msg.floMaiorZero("VALOR_BAIXA"  , document.getElementById("edtVlrBaixa").value    );          
          msg.intMaiorZero("COD_BANCO"    , document.getElementById("edtCodBnc").value      );
          msg.notNull("NOME_BANCO"        , document.getElementById("edtDesBnc").value      );
          //
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            clsJs = jsString("lote");
            pega.forEach(function(reg){
              clsJs.add("rotina"     , reg.rotina                                      );
              clsJs.add("login"      , jsPub[0].usr_login                              );
              clsJs.add("lancto"     , reg.lancto                                      );
              clsJs.add("codbnc"     , document.getElementById("edtCodBnc").value      );
              clsJs.add("dtbaixa"    , jsDatas("edtDtBaixa").retMMDDYYYY()             );
              clsJs.add("doctobaixa" , document.getElementById("edtDoctoBaixa").value  );
            });  
            ///////////////////////
            // Enviando para gravar
            ///////////////////////
            envPhp=clsJs.fim();
            fd = new FormData();
            fd.append("cpcrbaixatotal" , envPhp);
            msg=requestPedido("Trac_CpCrBaixaTotal.php",fd); 
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
  
              if( pega[0].rotina=="excluirbaixa"){
                document.getElementById("edtDtBaixa").value     = "";
                document.getElementById("edtDoctoBaixa").value  = "";
              };  
              
              for(let lin=0 ; (lin<nl) ; lin++){
                pega.forEach(function(reg){
                  if( jsNmrs(reg.lancto).inteiro().ret() == jsNmrs(tbl.rows[lin].cells[parseInt(reg.LANCTO)].innerHTML).inteiro().ret() ){
                    tbl.rows[lin].cells[parseInt(reg.BAIXA)].innerHTML    = document.getElementById("edtDtBaixa").value;
                    tbl.rows[lin].cells[parseInt(reg.DOCBAIXA)].innerHTML = document.getElementById("edtDoctoBaixa").value;
                    tbl.rows[lin].cells[parseInt(reg.BANCO)].innerHTML    = document.getElementById("edtDesBnc").value;
                  };
                });
              };  
              window.close();
            };  
          };  
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 id="h2Rotina" style="text-align:center;">Baixa total</h2></div>
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
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesBnc" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBnc">BANCO_NOME:</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtDtBaixa" 
                                             placeholder="##/##/####" 
                                             data-oldvalue=""                                             
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtDtBaixa">DATA BAIXA:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtDoctoBaixa" 
                                             data-oldvalue=""
                                             type="text" 
                                             maxlength="12"/>
            <label class="campo_label campo_required" for="edtDoctoBaixa">DOCUMENTO:</label>
          </div>
          
          <div class="campotexto campo15">
            <input class="campo_input_titulo edtDireita" id="edtVlrBaixa" 
                                                  onBlur="fncCasaDecimal(this,2);"            
                                                  maxlength="15" 
                                                  type="text" />
            <label class="campo_label campo_required" for="edtVlrBaixa">VLR BAIXA:</label>
          </div>
          <div onClick="fncGravar();" class="btnImagemEsq bie12 bieAzul bieRight"><i id="imgOk" class="fa fa-check"> Baixar</i></div>          
          <div onClick="window.close();" class="btnImagemEsq bie12 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>          
          
          <div class="campotexto campo100">
            <div id="appAba" class="campotexto campo75" style="height:20em;float:left;position:relative;">
            </div>            
          </div>
        </div> 
      </form>
    </div>
  </body>
</html>