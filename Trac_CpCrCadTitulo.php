<?php
  session_start();
  if( isset($_POST["cadtit"]) ){
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
      $retCls   = $vldr->validarJs($_POST["cadtit"]);
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
        //
        if( $lote[0]->rotina=="buscapadrao" ){
          $sql ="SELECT A.PG_CODPDR";
          $sql.="       ,A.PG_CODPTP";
          $sql.="       ,PDR.PDR_NOME";          
          $sql.="       ,A.PG_INDICE"; 
          $sql.="  FROM PADRAOGRUPO A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN PADRAO PDR ON A.PG_CODPDR=PDR.PDR_CODIGO AND PDR.PDR_ATIVO='S'";          
          $sql.=" WHERE ((PDR.PDR_CODPTT='L') AND (A.PG_ATIVO='S'))";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          $tblPg=$retCls["dados"];
          usort($tblPg,"fncPg");
          //
          $sql ="SELECT A.PT_CODIGO";
          $sql.="       ,A.PT_NOME";
          $sql.="       ,A.PT_CODTD";
          $sql.="       ,TD.TD_NOME";          
          $sql.="       ,A.PT_CODFC";
          $sql.="       ,FC.FC_NOME";          
          $sql.="       ,A.PT_DEBCRE";
          $sql.="       ,A.PT_CODCC";
          $sql.="       ,A.PT_CODPDR";
          $sql.="       ,PG.PG_INDICE AS PT_INDICE";          
          $sql.="  FROM PADRAOTITULO A WITH(NOLOCK)"; 
          $sql.="  LEFT OUTER JOIN PADRAOGRUPO PG ON A.PT_CODPDR=PG.PG_CODPDR AND PG.PG_ATIVO='S'";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO TD ON A.PT_CODTD=TD.TD_CODIGO AND TD.TD_ATIVO='S'";          
          $sql.="  LEFT OUTER JOIN FORMACOBRANCA FC ON A.PT_CODFC=FC.FC_CODIGO AND FC.FC_ATIVO='S'";                    
          $sql.=" WHERE (A.PT_ATIVO='S')";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          $tblPt=$retCls["dados"];
          usort($tblPt,"fncPt");          
          //    
          $retorno='[{"retorno":"OK"
                     ,"tblPg":'.json_encode($tblPg).'
                     ,"tblPt":'.json_encode($tblPt).'                       
                     ,"erro":""}]'; 
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
    <title>Cadastro Titulo financeiro</title>
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
    <script src="tabelaTrac/f10/tabelaBancoF10.js"></script>    
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        buscaPadrao(); 
        document.getElementById("edtCodFll").value    = jsConverte(jsPub[0].emp_codfll).emZero(4);
        document.getElementById("edtDtDocto").value   = jsDatas(0).retDDMMYYYY();
        document.getElementById("edtVlrEvento").value = "0,00";
        document.getElementById("edtNumPar").value    = "01";
        document.getElementById("edtIntervalo").value = "30";
        document.getElementById("edtCodFvr").value    = "0000";
        document.getElementById("edtCodBnc").value    = jsConverte(jsPub[0].emp_codbnc).emZero(4);
        document.getElementById("edtDesBnc").value    = jsPub[0].emp_desbnc;
        document.getElementById("cbCodPtp").focus();
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Aqui estou informando se vou usar as funcoes de retorno do F10 deste arquivo(false-padrao) ou do arquivo que contem a classe AjudaF10(true)
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        localStorage.setItem("f10Bnc",true);
        //
        if( jsPub[0].emp_fllunica=="S" ){
          jsCmpAtivo("edtCodFll").remove("campo_input").add("campo_input_titulo").disabled(true);
        };
        
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
      //var clsChecados;                // Classe para montar Json  
      //var chkds;                      // Guarda todos registros checados na table 
      //var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var objPadF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var objBncF10;                  // Obrigatório para instanciar o JS BancoF10
      var minhaAba;
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado
      ////////////////////////////////////////////////////
      // Objeto global para uso em toda rotina de cadastro
      ////////////////////////////////////////////////////
      var objGlobal={ tblPg   : ""    // Tabela PAGARGRUPO
                      ,tblPt  : ""    // Tabela PAGARTITULO
                    };
      //
      //
      ////////////////////////////////
      // Buscando as operacoes padroes
      ////////////////////////////////
      function buscaPadrao(){  
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "buscapadrao"       );
        clsJs.add("login"       , jsPub[0].usr_login  );
        fd = new FormData();
        fd.append("cadtit" , clsJs.fim());
        msg     = requestPedido(arqLocal,fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          objGlobal.tblPg=retPhp[0]["tblPg"];
          objGlobal.tblPt=retPhp[0]["tblPt"];
        }
      };
      function fncEncherPg(el,ondeApp,ceOpt=""){
        if( el.value != "**" ){
          document.getElementById("edtCodPtp").value=el.value;
          ////////////////////////////////////////////////////////
          // Filtrando somente as colunas recebidas como parametro
          ////////////////////////////////////////////////////////        
          let arrFilter=objGlobal.tblPg.filter(function(coluna){
            return coluna.PG_CODPTP==el.value;
          });
          document.getElementById(ondeApp).innerHTML="";
          ceOpt       = document.createElement("option");
          ceOpt.value = "**";
          ceOpt.text  = "INFORME";
          document.getElementById(ondeApp).appendChild(ceOpt);
          //
          arrFilter.forEach(function(campo){
            ceOpt       = document.createElement("option");
            ceOpt.value = campo.PG_INDICE;
            ceOpt.text  = campo.PDR_NOME
            document.getElementById(ondeApp).appendChild(ceOpt);
          });
        };
      };
      function fncEncherPt(el,ondeApp,ceOpt=""){
        if( el.value != "**" ){
          ////////////////////////////////////////////////////////
          // Filtrando somente as colunas recebidas como parametro
          ////////////////////////////////////////////////////////        
          let arrFilter=objGlobal.tblPt.filter(function(coluna){
            return coluna.PT_INDICE==el.value;
          });
          document.getElementById(ondeApp).innerHTML="";
          document.getElementById(ondeApp).innerHTML="";
          ceOpt       = document.createElement("option");
          ceOpt.value = "**";
          ceOpt.text  = "INFORME";
          document.getElementById(ondeApp).appendChild(ceOpt);
          //
          arrFilter.forEach(function(campo){
            ceOpt       = document.createElement("option");
            ceOpt.value = campo.PT_CODIGO;
            ceOpt.text  = campo.PT_NOME
            document.getElementById(ondeApp).appendChild(ceOpt);
          });
          //-- Preenche os campos do formulario conforme PADRAOTITULO
          fncCamposFormulario(document.getElementById("cbCodPt").value)
        };
      };
      function fncCamposFormulario(vlr){
        if( vlr != "**" ){        
          ////////////////////////////////////////////////////////
          // Filtrando somente as colunas recebidas como parametro
          // Aqui eh chave primaria, traz apenas uma linha
          ////////////////////////////////////////////////////////        
          let arrFilter=objGlobal.tblPt.filter(function(coluna){
            return coluna.PT_CODIGO==vlr;
          });
          document.getElementById("edtCodTd").value       = arrFilter[0].PT_CODTD;
          document.getElementById("edtDesTd").value       = arrFilter[0].TD_NOME;        
          document.getElementById("edtCodFc").value       = arrFilter[0].PT_CODFC;
          document.getElementById("edtDesFc").value       = arrFilter[0].FC_NOME;
          document.getElementById("edtCodCc").value       = arrFilter[0].PT_CODCC;
          document.getElementById("edtCodPt").value       = jsNmrs(arrFilter[0].PT_CODIGO).emZero(4).ret();
          document.getElementById("edtObservacao").value  = arrFilter[0].PT_NOME;
          document.getElementById("edtDebCre").value      = arrFilter[0].PT_DEBCRE;          
        };
      };
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
      //
      function fncCalculaParcelamento(){
        try{    
          msg = new clsMensagem("Erro");
          //////////////////////////
          // Checagem basica      //
          //////////////////////////
          msg.floMaiorZero("VALOR TOTAL"  , document.getElementById("edtVlrEvento").value );           
          msg.intMaiorZero("INTERVALO"    , document.getElementById("edtIntervalo").value );          
          msg.intMaiorZero("PARCELAS"     , document.getElementById("edtNumPar").value    );                    
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            minhaAba.limparTable("tblPrcl");
            let np        = jsNmrs("edtNumPar").inteiro().ret();
            let intervalo = jsNmrs("edtIntervalo").inteiro().ret();
            let vlrEvento = jsNmrs("edtVlrEvento").dolar().ret();
            let vencto    = document.getElementById("edtVencto").value;
            if( np==1 ){
              minhaAba.novoRegistro("tblPrcl",
                [ "01" 
                  ,document.getElementById("edtVencto").value
                  ,document.getElementById("edtVlrEvento").value
                ]
              );
            } else {
              let np        = jsNmrs("edtNumPar").inteiro().ret();
              let intervalo = jsNmrs("edtIntervalo").inteiro().ret();
              let vlrEvento = jsNmrs("edtVlrEvento").dolar().ret();
              let vlrParc   = jsNmrs(vlrEvento/np).dolar().ret();
              document.getElementById("edtVencto").setAttribute("data-oldvalue",document.getElementById("edtVencto").value); 
              
              for( let lin=0; lin < np; lin++ ){                
                minhaAba.novoRegistro("tblPrcl",
                  [ (lin+1) 
                    ,document.getElementById("edtVencto").value
                    ,( ((lin+1)<np) ? jsNmrs(vlrParc).dec(2).real().ret() : jsNmrs(vlrEvento).dec(2).real().ret() )
                  ]
                );
                vlrEvento = (vlrEvento-vlrParc);
                document.getElementById("edtVencto").value=jsDatas("edtVencto").retSomarDias(intervalo).retDDMMYYYY();
              };  
              document.getElementById("edtVencto").value=document.getElementById("edtVencto").getAttribute("data-oldvalue");
            }; 
          };    
        } catch(e){
          gerarMensagemErro("catch",e.message,{cabec:"Erro"});
        };  
      };
      function fncGravar(){
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
        try{  
          document.getElementById("edtCodFvr").value     = document.getElementById("edtCodFvr").value.soNumeros();
          document.getElementById("edtDocto").value      = jsStr("edtDocto").upper().alltrim().ret();
          document.getElementById("edtCodTd").value      = jsStr("edtCodTd").upper().alltrim().ret();    
          document.getElementById("edtCodFc").value      = jsStr("edtCodFc").upper().alltrim().ret();    
          document.getElementById("edtCodBnc").value     = document.getElementById("edtCodBnc").value.soNumeros();
          document.getElementById("edtObservacao").value = jsStr("edtObservacao").upper().tamMax(120).ret();           
          document.getElementById("edtIntervalo").value  = document.getElementById("edtIntervalo").value.soNumeros();
          document.getElementById("edtNumPar").value     = document.getElementById("edtNumPar").value.soNumeros();
        
          msg = new clsMensagem("Erro");
          msg.diferente("TIPO"                  , document.getElementById("cbCodPtp").value,"**"        );
          msg.diferente("GRUPO"                 , document.getElementById("cbCodPg").value,"**"         );
          msg.diferente("LANCTO_FINANC"         , document.getElementById("cbCodPt").value,"**"         );
          msg.intMaiorZero("COD_FAVORECIDO"     , document.getElementById("edtCodFvr").value            );
          msg.notNull("NOME_FAVORECIDO"         , document.getElementById("edtDesFvr").value            );
          msg.notNull("DOCTO"                   , document.getElementById("edtDocto").value             );
          msg.notNull("EMISSAO"                 , document.getElementById("edtDtDocto").value           );
          msg.floMaiorIgualZero("VALOR_EVENTO"  , document.getElementById("edtVlrEvento").value         );
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
          msg.intMaiorZero("PARCELA"            , document.getElementById("edtNumPar").value            );
          msg.notNull("VENCTO"                  , document.getElementById("edtVencto").value            );
          msg.intMaiorZero("INTERVALO"          , document.getElementById("edtIntervalo").value         );
          msg.contido("DEBITO_CREDITO"          , document.getElementById("edtDebCre").value,["D","C"]  );
          //
          if( jsNmrs(totGrd).dec(2).real().ret() != jsNmrs("edtVlrEvento").dec(2).real().ret() )
            msg.add("CAMPO<b> VALOR EVENTO "+jsNmrs("edtVlrEvento").dec(2).dolar().ret()+ "</b>DIVERGE DO TOTAL GRADE PARCELAMENTO "+totGrd+"!");           
          //
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            // Armazenando para envio ao Php
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
            clsFin.add("codbnc"             , document.getElementById("edtCodBnc").value      );
            clsFin.add("codcc"              , "NULL"                                          );  //Se NULL o trigger faz    
            clsFin.add("codcmp"             , jsDatas("edtDtDocto").retYYYYMM()               );  //Competencia contabil          
            clsFin.add("codfvr"             , document.getElementById("edtCodFvr").value      );
            clsFin.add("codfc"              , document.getElementById("edtCodFc").value       );
            clsFin.add("codtd"              , document.getElementById("edtCodTd").value       );
            clsFin.add("codfll"             , document.getElementById("edtCodFll").value      );
            clsFin.add("codptt"             , "L"                                             );            
            clsFin.add("docto"              , document.getElementById("edtDocto").value       );
            clsFin.add("dtdocto"            , jsDatas("edtDtDocto").retMMDDYYYY()             );
            clsFin.add("lancto"             , 0                                               );  //Se maior que zero eh rotina de alteracao            
            clsFin.add("observacao"         , document.getElementById("edtObservacao").value  );
            clsFin.add("codpt"              , document.getElementById("edtCodPt").value       );
            clsFin.add("codptp"             , document.getElementById("edtCodPtp").value      );
            clsFin.add("vlrdesconto"        , 0                                               );
            clsFin.add("vlrevento"          , jsNmrs("edtVlrEvento").dolar().ret()            );
            clsFin.add("vlrmulta"           , 0                                               );
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
            fd.append("gravar",envPhp);
            msg     = requestPedido("classPhp/GravaFinanceiro.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              throw retPhp[0].erro;
            } else {  
              gerarMensagemErro("cad",retPhp[0].erro,{cabec:"Aviso",foco:"cbCodPtp"});  
              document.getElementById("edtVlrEvento").value = "0,00";
              document.getElementById("edtNumPar").value    = "01";
              document.getElementById("edtIntervalo").value = "30";
              document.getElementById("edtCodFvr").value    = "0000";
              document.getElementById("edtDesFvr").value    = "";
              document.getElementById("edtDocto").value     = "";
              minhaAba.limparTable("tblPrcl");
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
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Novo titulo financeiro</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmPgr" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 550px;">
          <div class="campotexto campo100">
            <h2>Selecione opção</h2>
          </div>
          <div class="campotexto campo15">
            <select onChange="fncEncherPg(this,'cbCodPg');" class="campo_input_combo" id="cbCodPtp">
              <option value="**">INFORME</option>
              <option value="CP">CONTAS PAGAR</option>
              <option value="CR">CONTAS RECEBER</option>
              <option value="PP">PREVISAO PAGAR</option>
              <option value="PR">PREVISAO RECEBER</option>
              <option value="MP">MENSAL PAGAR</option>
              <option value="MR">MENSAL RECEBER</option>                  
            </select>
            <label class="campo_label campo_required" for="cbCodPtp">TIPO</label>
          </div>
          <div class="campotexto campo35">
            <select onChange="fncEncherPt(this,'cbCodPt');" class="campo_input_combo" id="cbCodPg">
              <option value="**">INFORME</option>            
            </select>
            <label class="campo_label campo_required" for="cbCodPg">GRUPO</label>
          </div>
          <div class="campotexto campo50">
            <select onChange="fncCamposFormulario(this.value)" class="campo_input_combo" id="cbCodPt">
              <option value="**">INFORME</option>            
            </select>
            <label class="campo_label campo_required" for="cbCodPt">LANCTO FINANCEIO</label>
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
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtDocto" type="text" maxlength="12"/>
            <label class="campo_label campo_required" for="edtDocto">DOCUMENTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtDtDocto" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtDtDocto">EMISSÂO:</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input edtDireita" id="edtVlrEvento" 
                                                  onBlur="fncCasaDecimal(this,2);"            
                                                  maxlength="15" 
                                                  type="text"/>
            <label class="campo_label campo_required" for="edtVlrEvento">VALOR TOTAL:</label>
          </div>
          <!--
          -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodTd"
                                                onBlur="codTdBlur(this);" 
                                                onFocus="tdFocus(this);" 
                                                onClick="tdF10Click(this);"
                                                data-oldvalue=""
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
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="3"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodFc">FC:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesFc" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesFc">FORMA COBR:</label>
          </div>
          <!--
          -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodBnc"
                                                onBlur="codBncBlur(this);" 
                                                onFocus="bncFocus(this);" 
                                                onClick="bncF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="4"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodBnc">BANCO:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesBnc" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBnc">BANCO_NOME:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtCodFll" 
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             type="text" 
                                             maxlength="4"/>
            <label class="campo_label campo_required" for="edtCodFll">FILIAL:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCodPtp" type="text" disabled />
            <label class="campo_label campo_required" for="edtCodPtp">TIPO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCodPt" type="text" disabled />
            <label class="campo_label campo_required" for="edtCodPt">OPERAÇÃO:</label>
          </div>
          <div class="campotexto campo20">
            <input class="campo_input_titulo input" id="edtCodCc" type="text" disabled />
            <label class="campo_label campo_required" for="edtCodCc">CONTABIL:</label>
          </div>
          
          <div class="campotexto campo100">
            <input class="campo_input input" id="edtObservacao" type="text" maxlength="120"/>
            <label class="campo_label campo_required" for="edtObservacao">OBSERVAÇÃO:</label>
          </div>
          
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtNumPar" 
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             type="text" 
                                             maxlength="2"/>
            <label class="campo_label campo_required" for="edtNumPar">PARCELA(s)</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtVencto" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             data-oldvalue=""
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtVencto">PRIMEIRO VENCTO:</label>
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