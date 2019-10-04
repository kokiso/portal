<?php
  session_start();
  if( isset($_POST["cpcraltdocto"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
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
      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["cpcraltdocto"]);
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
        //
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
    <title>Alteracao de docto</title>
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
    <script src="tabelaTrac/f10/tabelaBancoF10.js"></script>        
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>    
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        /////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_CpCr.php
        /////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addAlt")).lote[0];
        //localStorage.removeItem("addAlt");
        document.getElementById("edtCodBnc").value      = jsNmrs(pega.codbnc).emZero(4).ret();        
        document.getElementById("edtCodCc").value       = pega.codcc;                
        document.getElementById("edtCodCmp").value      = pega.codcmp;                        
        document.getElementById("edtCodFc").value       = pega.codfc;        
        document.getElementById("edtCodFll").value      = jsNmrs(pega.codfll).emZero(4).ret();
        document.getElementById("edtCodFvr").value      = jsNmrs(pega.codfvr).emZero(4).ret();        
        document.getElementById("edtCodPt").value       = jsNmrs(pega.codpt).emZero(4).ret();        
        document.getElementById("edtCodPtp").value      = pega.codptp;
        document.getElementById("edtCodPtt").value      = pega.codptt;
        document.getElementById("edtCodTd").value       = pega.codtd;        
        document.getElementById("edtDebCre").value      = pega.debcre;        
        document.getElementById("edtDesBnc").value      = pega.desbnc;        
        document.getElementById("edtDesFc").value       = pega.desfc;
        document.getElementById("edtDesFvr").value      = pega.desfvr;        
        document.getElementById("edtDesTd").value       = pega.destd;
        document.getElementById("edtDocto").value       = pega.docto;
        document.getElementById("edtDtDocto").value     = jsDatas(pega.dtdocto).retDDMMYYYY();
        document.getElementById("edtMaster").value      = pega.master;
        document.getElementById("edtIntervalo").value   = "01";      
        document.getElementById("edtLancto").value      = pega.lancto;        
        document.getElementById("edtNumPar").value      = pega.numpar
        document.getElementById("edtQtasParc").value    = "01";
        document.getElementById("edtObservacao").value  = pega.observacao;
        document.getElementById("edtPrimVencto").value  = pega.vencto;                
        document.getElementById("edtVencto").value      = pega.vencto;        
        document.getElementById("edtVlrDesconto").value = "0,00";         
        document.getElementById("edtVlrParcela").value  = jsNmrs(pega.valor).abs().real().ret(); 
        document.getElementById("edtVlrMulta").value    = "0,00";
        document.getElementById("edtVlrEvento").value   = jsNmrs(pega.vlrevento).abs().real().ret(); 
        /////////////////////////////////////////////////////////////////////
        // Guardando valores para ver se houve alteracao
        // Manter ordem pois olha antes de gravar se algum campo foi alterado
        /////////////////////////////////////////////////////////////////////
        document.getElementById("edtCodBnc").setAttribute("data-oldvalue",pega.codbnc);        
        document.getElementById("edtCodFc").setAttribute("data-oldvalue",pega.codfc);        
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",pega.codfvr);      
        document.getElementById("edtCodTd").setAttribute("data-oldvalue",pega.codtd);        
        document.getElementById("edtDocto").setAttribute("data-oldvalue",pega.docto);        
        document.getElementById("edtDtDocto").setAttribute("data-oldvalue",pega.dtdocto);        
        document.getElementById("edtObservacao").setAttribute("data-oldvalue",pega.observacao);                
        document.getElementById("edtVencto").setAttribute("data-oldvalue",pega.vencto);                
        document.getElementById("edtVlrParcela").setAttribute("data-oldvalue",jsNmrs(pega.valor).abs().real().ret());
        //
        document.getElementById("edtCodFvr").foco();
        
        var jsTab={
          "pai"         : "appAba"  //Onde vai ser appendado o html gerado
          ,"idStyle"    : "meuCss"  //id da tag style
          ,"opcExcluir" : "N"       //Se vai existir a opção excluir        
          ,"abas":[ 
             {"id":0  ,"nomeAba"      : "parcela" 
                      ,"labelAba"     : "Parcelamento"
                      ,"table"        : "tblPrcl"
                      ,"widthTable"   : "45em" 
                      ,"heightTable"  : "18em"                     
                      ,"widthAba"     : "12em" 
                      ,"rolagemVert"  : true 
                      ,"somarCols":[2]
                      ,"head":[  { "labelCol"   : "PARC"     
                                  ,"width"      : "4em"  
                                  ,"fieldType"  : "int"
                                  ,"formato"    : "i3"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "VENCTO"   
                                  ,"width"      : "10em"  
                                  ,"fieldType"  : "dat"
                                  ,"editar"     : "S"
                                  ,"classe"     : "edtData"}
                                ,{ "labelCol"   : "VALOR"    
                                  ,"width"      : "12em" 
                                  ,"fieldType"  : "flo"
                                  ,"editar"     : "S"
                                  ,"classe"     : "edtDireita"}
                              ]
             }
          ]
        };  
        minhaAba=new clsTab(jsTab);
        minhaAba.htmlTab();  
      });
      //
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var objPadF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var pega;                       // Recuperar localStorage
      var objBncF10;                  // Obrigatório para instanciar o JS BancoF10      
      var objFcF10;                   // Obrigatório para instanciar o       
      var minhaAba;
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      ////////////////////////////
      //  AJUDA PARA FAVORECIDO //
      ////////////////////////////
      function fvrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fvrF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtDocto"
                      ,topo:100
                      ,tableBd:"FAVORECIDO"
                      ,fieldCod:"A.FVR_CODIGO"
                      ,fieldDes:"A.FVR_NOME"
                      ,fieldAtv:"A.FVR_ATIVO"
                      ,typeCod :"int" 
                      ,divWidth:"36%"
                      ,tbl:"tblFvr"}
        );
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value  = arr[0].CODIGO;
        document.getElementById("edtDesFvr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codFvrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtDocto"
                                  ,topo:100
                                  ,tableBd:"FAVORECIDO"
                                  ,fieldCod:"A.FVR_CODIGO"
                                  ,fieldDes:"A.FVR_NOME"
                                  ,fieldAtv:"A.FVR_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblFvr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "000"  : ret[0].CODIGO                  );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? "NAO SE APLICA"      : ret[0].DESCRICAO );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "000" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //  AJUDA PARA TIPODOCUMENTO //
      ///////////////////////////////
      function tdFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function tdF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodFc"
                      ,topo:100
                      ,tableBd:"TIPODOCUMENTO"
                      ,fieldCod:"A.TD_CODIGO"
                      ,fieldDes:"A.TD_NOME"
                      ,fieldAtv:"A.TD_ATIVO"
                      ,typeCod :"str" 
                      ,divWidth:"36%"
                      ,tbl:"tblTd"}
        );
      };
      function RetF10tblTd(arr){
        document.getElementById("edtCodTd").value  = arr[0].CODIGO;
        document.getElementById("edtDesTd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodTd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codTdBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodFc"
                                  ,topo:100
                                  ,tableBd:"TIPODOCUMENTO"
                                  ,fieldCod:"A.TD_CODIGO"
                                  ,fieldDes:"A.TD_NOME"
                                  ,fieldAtv:"A.TD_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblTd"}
          );
          document.getElementById(obj.id).value      = ( ret.length == 0 ? "FAT"  : ret[0].CODIGO                   );
          document.getElementById("edtDesTd").value  = ( ret.length == 0 ? "FATURA"      : ret[0].DESCRICAO         );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "FAT" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //  AJUDA PARA FORMACOBRANCA //
      ///////////////////////////////
      function fcFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fcF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodBnc"
                      ,topo:100
                      ,tableBd:"FORMACOBRANCA"
                      ,fieldCod:"A.FC_CODIGO"
                      ,fieldDes:"A.FC_NOME"
                      ,fieldAtv:"A.FC_ATIVO"
                      ,typeCod :"str" 
                      ,divWidth:"36%"
                      ,tbl:"tblFc"}
        );
      };
      function RetF10tblFc(arr){
        document.getElementById("edtCodFc").value  = arr[0].CODIGO;
        document.getElementById("edtDesFc").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFc").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codFcBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodBnc"
                                  ,topo:100
                                  ,tableBd:"FORMACOBRANCA"
                                  ,fieldCod:"A.FC_CODIGO"
                                  ,fieldDes:"A.FC_NOME"
                                  ,fieldAtv:"A.FC_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblFc"}
          );
          document.getElementById(obj.id).value     = ( ret.length == 0 ? "BOL"  : ret[0].CODIGO                    );
          document.getElementById("edtDesFc").value = ( ret.length == 0 ? "BOLETO"      : ret[0].DESCRICAO          );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "BOL" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //     AJUDA PARA BANCO      //
      ///////////////////////////////
      function bncFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bncF10Click(obj){ 
        fBancoF10(0,obj.id,"edtObservacao",100,{codemp: jsPub[0].emp_codigo,ativo:"S" } ); 
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
          let arr = fBancoF10(1,obj.id,"edtObservacao",100,
            {codbnc  : elNew
             ,codemp : jsPub[0].emp_codigo
             ,ativo  : "S"} 
          ); 
          document.getElementById(obj.id).value       = ( arr.length == 0 ? "0000"  : jsConverte(arr[0].CODIGO).emZero(4) );
          document.getElementById("edtDesBnc").value  = ( arr.length == 0 ? "*"     : arr[0].DESCRICAO                    );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )       );
        };
      };
      function fncCalculaParcelamento(){
        try{    
          msg = new clsMensagem("Erro");
          //////////////////////////
          // Checagem basica      //
          //////////////////////////
          msg.floMaiorZero("VALOR TOTAL"  , document.getElementById("edtVlrParcela").value );           
          msg.intMaiorZero("INTERVALO"    , document.getElementById("edtIntervalo").value );          
          msg.intMaiorZero("PARCELAS"     , document.getElementById("edtQtasParc").value    );                    
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            minhaAba.limparTable("tblPrcl");
            let np        = jsNmrs("edtQtasParc").inteiro().ret();
            let intervalo = jsNmrs("edtIntervalo").inteiro().ret();
            let vlrParcela= jsNmrs("edtVlrParcela").dolar().ret();
            let vencto    = document.getElementById("edtVencto").value;
            if( np==1 ){
              minhaAba.novoRegistro("tblPrcl",
                [ "01" 
                  ,document.getElementById("edtVencto").value
                  ,document.getElementById("edtVlrParcela").value
                ]
              );
            } else {
              let np        = jsNmrs("edtQtasParc").inteiro().ret();
              let intervalo = 0;
              let vlrParcela= jsNmrs("edtVlrParcela").dolar().ret();
              let vlrParc   = jsNmrs(vlrParcela/np).dolar().ret();
              for( let lin=0; lin < np; lin++ ){
                minhaAba.novoRegistro("tblPrcl",
                  [ (lin+1) 
                    ,document.getElementById("edtVencto").value
                    ,( ((lin+1)<np) ? jsNmrs(vlrParc).dec(2).real().ret() : jsNmrs(vlrParcela).dec(2).real().ret() )
                  ]
                );
                vlrParcela = (vlrParcela-vlrParc);
                intervalo = (intervalo+jsNmrs("edtIntervalo").inteiro().ret());
                document.getElementById("edtVencto").value=jsDatas("edtVencto").retSomarDias(intervalo).retDDMMYYYY();
              };  
            }; 
          };    
        } catch(e){
          gerarMensagemErro("catch",e.message,{cabec:"Erro"});
        };  
      };
      function fncGravar(){
        try{       
          ///////////////////////////////////////////
          // Olhando aqui se algum campo foi alterado
          ///////////////////////////////////////////
          /*
          let alterado=0;
          if( jsNmrs("edtCodBnc").inteiro().ret() != jsNmrs(document.getElementById("edtCodBnc").getAttribute("data-oldvalue")).inteiro().ret() )
            alterado++;
          if( document.getElementById("edtCodFc").value != document.getElementById("edtCodFc").getAttribute("data-oldvalue") )
            alterado++;  
          if( jsNmrs("edtCodFvr").inteiro().ret() != jsNmrs(document.getElementById("edtCodFvr").getAttribute("data-oldvalue")).inteiro().ret() )
            alterado++;
          if( document.getElementById("edtCodTd").value != document.getElementById("edtCodTd").getAttribute("data-oldvalue") )
            alterado++;  
          if( document.getElementById("edtDocto").value != document.getElementById("edtDocto").getAttribute("data-oldvalue") )
            alterado++;  
          if( document.getElementById("edtDtDocto").value != document.getElementById("edtDtDocto").getAttribute("data-oldvalue") )
            alterado++;  
          if( document.getElementById("edtObservacao").value != document.getElementById("edtObservacao").getAttribute("data-oldvalue") )
            alterado++;  
          if( document.getElementById("edtVencto").value != document.getElementById("edtVencto").getAttribute("data-oldvalue") )
            alterado++;  
          if( jsNmrs("edtVlrParcela").dolar().ret() != jsNmrs(document.getElementById("edtVlrParcela").getAttribute("data-oldvalue")).dolar().ret() )
            alterado++;
          if( alterado==0 )
            throw "Nenhum campo alterado!"; 
          */
          //////////////////////////////////////////////
          // Um documento naum pode ter desconto e multa
          //////////////////////////////////////////////
          if( (jsNmrs("edtVlrDesconto").dolar().ret()>0) && (jsNmrs("edtVlrMulta").dolar().ret()>0) )
            throw "Um documento não pode ter desconto e multa!"; 
          //
          //
          /////////////////////////////////////////////////////////////////////////////////
          // Transformar em vetor para naum ter que andar novamente qdo criar json para php
          /////////////////////////////////////////////////////////////////////////////////
          let tbl     = tblPrcl.getElementsByTagName("tbody")[0];
          let nl      = tbl.rows.length;
          let totGrd  = 0;
          let arrPrcl = [];
          if( nl>0 ){
            for(var lin=0 ; (lin<nl) ; lin++){  
              arrPrcl.push(
                { parc    : jsNmrs(tbl.rows[lin].cells[0].innerHTML).inteiro().ret()
                  ,vencto : jsDatas(tbl.rows[lin].cells[1].innerHTML).retMMDDYYYY()
                  ,valor  : jsNmrs(tbl.rows[lin].cells[2].innerHTML).dec(2).dolar().ret()
                  ,debcre : document.getElementById("edtDebCre").value
                }  
              )
              totGrd+=jsNmrs(tbl.rows[lin].cells[2].innerHTML).dec(2).dolar().ret();
            };  
          };
          //
          //
          document.getElementById("edtCodFvr").value     = document.getElementById("edtCodFvr").value.soNumeros();
          document.getElementById("edtDocto").value      = jsStr("edtDocto").upper().alltrim().ret();
          document.getElementById("edtCodTd").value      = jsStr("edtCodTd").upper().alltrim().ret();    
          document.getElementById("edtCodFc").value      = jsStr("edtCodFc").upper().alltrim().ret();    
          document.getElementById("edtCodBnc").value     = document.getElementById("edtCodBnc").value.soNumeros();
          document.getElementById("edtObservacao").value = jsStr("edtObservacao").upper().tamMax(120).ret();           
          document.getElementById("edtIntervalo").value  = document.getElementById("edtIntervalo").value.soNumeros();
          document.getElementById("edtQtasParc").value   = document.getElementById("edtQtasParc").value.soNumeros();
        
          msg = new clsMensagem("Erro");
          msg.intMaiorZero("COD_FAVORECIDO"     , document.getElementById("edtCodFvr").value            );
          msg.notNull("NOME_FAVORECIDO"         , document.getElementById("edtDesFvr").value            );
          msg.notNull("DOCTO"                   , document.getElementById("edtDocto").value             );
          msg.notNull("EMISSAO"                 , document.getElementById("edtDtDocto").value           );
          msg.floMaiorIgualZero("VALOR_PARCELA" , document.getElementById("edtVlrParcela").value        );
          msg.floMaiorIgualZero("VALOR_DESCONTO", document.getElementById("edtVlrDesconto").value       );          
          msg.floMaiorIgualZero("VALOR_MULTA"   , document.getElementById("edtVlrMulta").value          );                    
          msg.notNull("COD_TIPODOCUMENTO"       , document.getElementById("edtCodTd").value             );
          msg.notNull("NOME_TIPODOCUMENTO"      , document.getElementById("edtDesTd").value             );
          msg.notNull("COD_FORMACOBRANCA"       , document.getElementById("edtCodFc").value             );
          msg.notNull("NOME_FORMACOBRANCA"      , document.getElementById("edtDesFc").value             );
          msg.intMaiorZero("COD_BANCO"          , document.getElementById("edtCodBnc").value            );
          msg.notNull("NOME_BANCO"              , document.getElementById("edtDesBnc").value            );
          msg.intMaiorZero("COD_FILIAL"         , document.getElementById("edtCodFll").value            );
          msg.notNull("COD_TIPO"                , document.getElementById("edtCodPtp").value            );
          msg.intMaiorZero("OPERACAO"           , document.getElementById("edtCodPt").value             );
          msg.notNull("CONTA_CONTABIL"          , document.getElementById("edtCodCc").value             );
          msg.notNull("OBSERVACAO"              , document.getElementById("edtObservacao").value        );
          msg.intMaiorZero("PARCELA"            , document.getElementById("edtQtasParc").value          );
          msg.notNull("VENCTO"                  , document.getElementById("edtVencto").value            );
          msg.intMaiorZero("INTERVALO"          , document.getElementById("edtIntervalo").value         );
          msg.contido("DEBITO_CREDITO"          , document.getElementById("edtDebCre").value,["D","C"]  );
          //
          if( jsNmrs(totGrd).dec(2).real().ret() != jsNmrs("edtVlrParcela").dec(2).real().ret() )
            msg.add("CAMPO<b> VALOR EVENTO "+jsNmrs("edtVlrParcela").dec(2).dolar().ret()+ "</b>DIVERGE DO TOTAL GRADE PARCELAMENTO "+totGrd+"!");           
          //
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            // Armazenando para envio ao Php e contando, se soh um registro nem envia para rotina
            let numReg=0;
            let clsRat = jsString("rateio");
            clsRat.principal(false);
            
            let clsDup = jsString("duplicata");
            clsDup.principal(false);
            
            arrPrcl.forEach(function(reg){
              clsRat.add("parcela"          , reg.parc                                  );
              clsRat.add("codcc"            , document.getElementById("edtCodCc").value );
              clsRat.add("debito"           , (reg.debcre=="D" ? reg.valor : 0)         );
              clsRat.add("credito"          , (reg.debcre=="C" ? reg.valor : 0)         );
              clsRat.add("comparaVlrEvento" , "S"                                       );

              clsDup.add("parcela"    , reg.parc    );
              clsDup.add("vencto"     , reg.vencto  );  
              clsDup.add("vlrparcela" , reg.valor   );              
              
              numReg++;
              
            });  
            let rateio    = clsRat.fim();
            let duplicata = clsDup.fim();
            
            let clsFin = jsString("lote");
            clsFin.add("login"              , jsPub[0].usr_login  );
            clsFin.add("gravartitulo"       , document.getElementById("edtCodBnc").value      );
            ///////////////////////////////////////////////////////////////////////////////////
            // verdireito
            // Como vem de NFP/NFS/CONTRATO/TARIFA/TRANSF aqui informo qual direito vou olhar
            // pois um usuario pode lancar contrato mas naum NFProduto
            ///////////////////////////////////////////////////////////////////////////////////
            clsFin.add("verdireito"         , 28                                              );            
            clsFin.add("codalt"             , 8                                               );  //Ver codigos trigger BI            
            clsFin.add("codbnc"             , document.getElementById("edtCodBnc").value      );
            clsFin.add("codcc"              , "NULL"                                          );  //Se NULL o trigger faz    
            clsFin.add("codcmp"             , document.getElementById("edtCodCmp").value      );  //Referencia para trigger gravar contas de desc/multa           
            clsFin.add("codfvr"             , document.getElementById("edtCodFvr").value      );
            clsFin.add("codfc"              , document.getElementById("edtCodFc").value       );
            clsFin.add("codtd"              , document.getElementById("edtCodTd").value       );
            clsFin.add("codfll"             , document.getElementById("edtCodFll").value      );
            clsFin.add("codptt"             , document.getElementById("edtCodPtt").value      );            
            clsFin.add("docto"              , document.getElementById("edtDocto").value       );
            clsFin.add("dtdocto"            , jsDatas("edtDtDocto").retMMDDYYYY()             );
            clsFin.add("lancto"             , jsNmrs("edtLancto").inteiro().ret()             );  //Se maior que zero eh rotina de alteracao
            clsFin.add("master"             , document.getElementById("edtMaster").value      );            
            clsFin.add("numpar"             , document.getElementById("edtNumPar").value      );
            clsFin.add("observacao"         , document.getElementById("edtObservacao").value  );
            clsFin.add("codpt"              , document.getElementById("edtCodPt").value       );
            clsFin.add("codptp"             , document.getElementById("edtCodPtp").value      );
            clsFin.add("vencto"             , jsDatas("edtVencto").retMMDDYYYY()              );            
            clsFin.add("vlrdesconto"        , jsNmrs("edtVlrDesconto").dolar().ret()          );
            clsFin.add("vlrparcela"         , jsNmrs("edtVlrParcela").dolar().ret()           );
            clsFin.add("vlrevento"          , jsNmrs("edtVlrEvento").dolar().ret()            );            
            clsFin.add("vlrmulta"           , jsNmrs("edtVlrMulta").dolar().ret()             );
            clsFin.add("vlrretencao"        , 0                                               );
            clsFin.add("vlrpis"             , 0                                               );
            clsFin.add("vlrcofins"          , 0                                               );
            clsFin.add("vlrcsll"            , 0                                               );
            clsFin.add("temNfp"             , "N"                                             );
            clsFin.add("temNfs"             , "N"                                             );            
            clsFin.add("DUPLICATA"          , duplicata                                       );
            clsFin.add("RATEIO"             , rateio                                          );
            ///////////////////////
            // Enviando para gravar
            ///////////////////////
            envPhp=clsFin.fim();  
            fd = new FormData();
            fd.append("gravar"  , envPhp              );
            msg     = requestPedido("classPhp/GravaFinanceiro.php",fd); 
            retPhp  = JSON.parse(msg);
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
                  tbl.rows[lin].cells[parseInt(pega.DOCTO)].innerHTML       = document.getElementById("edtDocto").value;
                  tbl.rows[lin].cells[parseInt(pega.VENCTO)].innerHTML      = document.getElementById("edtVencto").value;
                  tbl.rows[lin].cells[parseInt(pega.FAVORECIDO)].innerHTML  = document.getElementById("edtDesFvr").value;
                  tbl.rows[lin].cells[parseInt(pega.TD)].innerHTML          = document.getElementById("edtCodTd").value;
                  tbl.rows[lin].cells[parseInt(pega.DESTD)].innerHTML       = document.getElementById("edtDesTd").value;
                  tbl.rows[lin].cells[parseInt(pega.FC)].innerHTML          = document.getElementById("edtCodFc").value;
                  tbl.rows[lin].cells[parseInt(pega.DESFC)].innerHTML       = document.getElementById("edtDesFc").value;
                  tbl.rows[lin].cells[parseInt(pega.VALOR)].innerHTML       = document.getElementById("edtVlrParcela").value;
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
      function fncDescMulta(){
        let vlrParcela  = jsNmrs(document.getElementById("edtVlrParcela").getAttribute("data-oldvalue")).dolar().ret();
        let vlrDesconto = jsNmrs("edtVlrDesconto").dolar().ret();
        let vlrMulta    = jsNmrs("edtVlrMulta").dolar().ret();
        document.getElementById("edtVlrParcela").value=jsNmrs((vlrParcela-vlrDesconto+vlrMulta)).real().ret();
        
      }
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Alteração titulo financeiro</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmPgr" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 550px;">
          <div class="campotexto campo100">
            <h2>Dados para alteração</h2>
          </div>
          <!--
          -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodFvr"
                                                OnKeyPress="return mascaraInteiro(event);"
                                                onBlur="codFvrBlur(this);" 
                                                onFocus="fvrFocus(this);" 
                                                onClick="fvrF10Click(this);"
                                                data-oldvalue="0000" 
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodFvr">FAVORECIDO:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesFvr" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesFvr">NOME</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtDocto" 
                                             data-oldvalue=""
                                             type="text" 
                                             maxlength="12"/>
            <label class="campo_label campo_required" for="edtDocto">DOCUMENTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtDtDocto" 
                                             placeholder="##/##/####" 
                                             data-oldvalue=""                                             
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtDtDocto">EMISSÂO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtVencto" 
                                             placeholder="##/##/####"
                                             data-oldvalue=""                                             
                                             onBlur="document.getElementById('edtPrimVencto').value=this.value"
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtVencto">VENCTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo edtDireita" id="edtVlrParcela" 
                                                  onBlur="fncCasaDecimal(this,2);"            
                                                  maxlength="15" 
                                                  data-oldvalue="0,00"
                                                  type="text"
                                                  disabled />
            <label class="campo_label campo_required" for="edtVlrParcela">VALOR:</label>
          </div>
          <!--
          -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodTd"
                                                onBlur="codTdBlur(this);" 
                                                onFocus="tdFocus(this);" 
                                                onClick="tdF10Click(this);"
                                                data-oldvalue="***"
                                                autocomplete="off"
                                                maxlength="3"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodTd">TD:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesTd" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesTd">TIPO DOCTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodFc"
                                                onBlur="codFcBlur(this);" 
                                                onFocus="fcFocus(this);" 
                                                onClick="fcF10Click(this);"
                                                data-oldvalue="***"
                                                autocomplete="off"
                                                maxlength="3"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodFc">FC:</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtDesFc" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesFc">FORMA COBR:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtLancto" 
                                             type="text" 
                                             maxlength="6"
                                             disabled />
            <label class="campo_label campo_required" for="edtLancto">LANCTO:</label>
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
          <div class="campotexto campo05">
            <input class="campo_input_titulo input" id="edtCodFll" 
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             type="text" 
                                             maxlength="4"
                                             disabled />
            <label class="campo_label" for="edtCodFll">FILIAL:</label>
          </div>
          <div class="campotexto campo05">
            <input class="campo_input_titulo input" id="edtCodPtp" type="text" disabled />
            <label class="campo_label" for="edtCodPtp">TIPO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCodPt" type="text" disabled />
            <label class="campo_label campo_required" for="edtCodPt">OPERAÇÃO:</label>
          </div>
          <div class="campotexto campo20">
            <input class="campo_input_titulo input" id="edtCodCc" type="text" disabled />
            <label class="campo_label campo_required" for="edtCodCc">CONTABIL:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCodCmp" type="text" disabled />
            <label class="campo_label" for="edtCodPtp">COMPETENCIA</label>
          </div>
          
          <div class="campotexto campo100">
            <input class="campo_input input" id="edtObservacao" 
                                             data-oldvalue=""
                                             type="text" 
                                             maxlength="120"/>
            <label class="campo_label campo_required" for="edtObservacao">OBSERVAÇÃO:</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input edtDireita" id="edtVlrDesconto" 
                                                  onBlur="fncCasaDecimal(this,2);fncDescMulta();"            
                                                  maxlength="15" 
                                                  type="text" />
            <label class="campo_label campo_required" for="edtVlrDesconto">DESCONTO:</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input edtDireita" id="edtVlrMulta" 
                                                  onBlur="fncCasaDecimal(this,2);fncDescMulta();"            
                                                  maxlength="15" 
                                                  type="text" />
            <label class="campo_label campo_required" for="edtVlrMulta">MULTA:</label>
          </div>
          
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtQtasParc" 
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             type="text" 
                                             maxlength="2"/>
            <label class="campo_label campo_required" for="edtQtasParc">PARCELA(s)</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtPrimVencto" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtPrimVencto">PRIMEIRO VENCTO:</label>
          </div>

          <div class="campotexto campo15">
            <input class="campo_input input" id="edtIntervalo" 
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             OnBlur="fncCalculaParcelamento();"
                                             type="text" 
                                             maxlength="2"/>
            <label class="campo_label campo_required" for="edtIntervalo">INTERVALO (Dias) </label>
          </div>
          <div class="inactive">
            <input id="edtDebCre" type="text" />
            <input id="edtNumPar" type="text" />
            <input id="edtMaster" type="text" />
            <input id="edtCodPtt" type="text" />
            <input id="edtVlrEvento" type="text" />
            
          </div>
          <div onClick="window.close();" class="btnImagemEsq bie12 bieRed"><i class="fa fa-reply"> Cancelar</i></div>
          <div onClick="fncGravar();" class="btnImagemEsq bie12 bieAzul"><i class="fa fa-check"> Confirmar</i></div>          
          
          <div class="campotexto campo100">
            <div id="appAba" class="campotexto campo50" style="height:20em;float:left;position:relative;">
            </div>            
          </div>
        </div> 
      </form>
    </div>
  </body>
</html>