<?php
  session_start();
  if( isset($_POST["cadnfs"]) ){
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
      $retCls   = $vldr->validarJs($_POST["cadnfs"]);
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
        /////////////////////////////////////////////////////
        // Buscando a aliquota se for do simples
        /////////////////////////////////////////////////////

        if( $rotina=="buscaAliquota" ){
          $sql="";
          $sql.="SELECT FMS_ALIQUOTA FROM FECHAMESSIMPLES WHERE FMS_CODMES=".$lote[0]->codcmp." AND FMS_CODEMP=".$_SESSION["emp_codigo"];
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          $retorno='[{"retorno":"OK"
                     ,"qtos": '.$retCls["qtos"].'
                     ,"tblFms":'.json_encode($retCls["dados"][0]).'
                     ,"erro":""}]'; 
        };
        /////////////////////////////////////////////////////
        // Buscando a serie da NF e parametros complementares
        /////////////////////////////////////////////////////
        if( $rotina=="buscaSnf" ){
          $sql="";
          $sql.="SELECT A.SNF_CODIGO,A.SNF_INFORMARNF,A.SNF_NFFIM,A.SNF_CODTD,TD.TD_NOME,A.SNF_ENTSAI";
          $sql.="  FROM SERIENF A";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO TD ON TD.TD_CODIGO=A.SNF_CODTD";
          $sql.=" WHERE ((A.SNF_ENTSAI='".$lote[0]->entsai."')"; 
          $sql.="   AND (A.SNF_CODTD='".$lote[0]->codtd."')";
          $sql.="   AND (A.SNF_CODFLL=".$lote[0]->codfll.")";
          $sql.="   AND (A.SNF_ATIVO='S'))"; 
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          $retorno='[{"retorno":"OK"
                     ,"tblSnf":'.json_encode($retCls["dados"][0]).'
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
    <script src="tabelaTrac/f10/tabelaFavorecidoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaServicoF10.js"></script>    
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        //buscaPadrao(); 
        /////////////////////////////////////
        // Prototype para preencher campos //
        /////////////////////////////////////
        document.getElementById("frmNfs").newRecord("data-newrecord");
        document.getElementById("cbOpcao").focus();
        
        //document.getElementById("edtCodFll").value    = jsNmrs(jsPub[0].emp_codfll).emZero(4).ret();
        //document.getElementById("edtDtDocto").value   = jsDatas(0).retDDMMYYYY();
        //document.getElementById("edtCodBnc").value    = jsNmrs(jsPub[0].emp_codbnc).emZero(4).ret();
        //document.getElementById("edtDesBnc").value    = jsPub[0].emp_desbnc;
        /*
        document.getElementById("edtVlrEvento").value = "0,00";
        document.getElementById("edtNumPar").value    = "01";
        document.getElementById("edtIntervalo").value = "30";
        document.getElementById("edtCodFvr").value    = "0000";
        document.getElementById("edtCodCdd").value    = "*";
        document.getElementById("edtNumNf").value     = "000000";        
        document.getElementById("edtCodFvr").foco();
        */
        
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
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var objPadF10;                  // Obrigatório para instanciar o JS PadraoF10      
      var objFvrF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var objSrvF10;                  // Obrigatório para instanciar o JS ServicoF10      
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
        fFavorecidoF10(0,obj.id,"edtCodSrv",100); 
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value    = arr[0].CODIGO;
        document.getElementById("edtDesFvr").value    = arr[0].DESCRICAO;
        document.getElementById("edtCodCdd").value    = arr[0].CODCDD;
        document.getElementById("edtFisJur").value    = arr[0].FJ;
        document.getElementById("edtCategoria").value = arr[0].CATEGORIA;
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codFvrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fFavorecidoF10(1,obj.id,"edtCodSrv",100); 
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "0000"          : ret[0].CODIGO         );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? "*"             : ret[0].DESCRICAO      );
          document.getElementById("edtCodCdd").value  = ( ret.length == 0 ? "*"             : ret[0].CODCDD         );
          document.getElementById("edtFisJur").value  = ( ret.length == 0 ? "*"             : ret[0].FJ             );          
          document.getElementById("edtCategoria").value  = ( ret.length == 0 ? "*"          : ret[0].CATEGORIA      );                    
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "000" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////
      //  AJUDA PARA SERVICO    //
      ////////////////////////////
      function srvFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function srvF10Click(obj){ 
        fServicoF10(0,obj.id,"edtCodFc",100
          ,{
            entsai      : (document.getElementById("edtCodPtp").value=="CP" ? "E" : "S")
            ,codcdd     : document.getElementById("edtCodCdd").value
            ,codemp     : jsPub[0].emp_codigo
            ,categoria  : document.getElementById("edtCategoria").value
            ,fisjur     : document.getElementById("edtFisJur").value
          }
        ); 
      };
      function RetF10tblSrv(arr){
        document.getElementById("edtCodSrv").value      = arr[0].CODIGO;
        document.getElementById("edtDesSrv").value      = arr[0].DESCRICAO;
        document.getElementById("edtInssSN").value      = arr[0].INSS_SN;
        document.getElementById("edtAliqInss").value    = arr[0].INSS_ALIQ;
        document.getElementById("edtInssBC").value      = arr[0].INSS_BASECALC;
        document.getElementById("edtIrrfSN").value      = arr[0].IRRF_SN;
        document.getElementById("edtAliqIrrf").value    = arr[0].IRRF_ALIQ;
        document.getElementById("edtPisSN").value       = arr[0].PIS_SN;
        document.getElementById("edtPisAliq").value     = arr[0].PIS_ALIQ;
        document.getElementById("edtCofinsSN").value    = arr[0].COFINS_SN;
        document.getElementById("edtCofinsAliq").value  = arr[0].COFINS_ALIQ;
        document.getElementById("edtCsllSN").value      = arr[0].CSLL_SN;
        document.getElementById("edtCsllAliq").value    = arr[0].CSLL_ALIQ;
        document.getElementById("edtIssSN").value       = arr[0].ISS_SN;
        document.getElementById("edtCodCc").value       = arr[0].CODCC;
        document.getElementById("edtAliqIss").value     = arr[0].ISS_ALIQ;
        document.getElementById("edtIssRet").value      = arr[0].RETIDO_SN;
        document.getElementById("edtObservacao").value  = arr[0].DESCRICAO;        
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
        document.getElementById("edtAliqIss").setAttribute("data-oldvalue",arr[0].ISS_ALIQ);
        //////////////////////////////////////////////////////////////////////////////////////
        // Guardando a aliquota se o usuario informar retido "N" e depois retornar para "S" //
        //////////////////////////////////////////////////////////////////////////////////////
        document.getElementById("edtAliqIss").setAttribute("data-oldvalue",arr[0].ISS_ALIQ);        
        
        
        
      };
      function codSrvBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fServicoF10(1,obj.id,"edtCodFc",100
            ,{
              entsai      : (document.getElementById("edtCodPtp").value=="CP" ? "E" : "S")
              ,codcdd     : document.getElementById("edtCodCdd").value
              ,codemp     : jsPub[0].emp_codigo
              ,categoria  : document.getElementById("edtCategoria").value
              ,fisjur     : document.getElementById("edtFisJur").value
            }
          ); 
          //  
          document.getElementById(obj.id).value           = ( arr.length == 0 ? "0000"          : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          document.getElementById("edtDesSrv").value      = ( arr.length == 0 ? "*"             : arr[0].DESCRICAO                      );
          document.getElementById("edtInssSN").value      = ( arr.length == 0 ? "*"             : arr[0].INSS_SN                        );
          document.getElementById("edtAliqInss").value    = ( arr.length == 0 ? "0,00"          : arr[0].INSS_ALIQ                      );
          document.getElementById("edtInssBC").value      = ( arr.length == 0 ? "0,00"          : arr[0].INSS_BASECALC                  );
          document.getElementById("edtIrrfSN").value      = ( arr.length == 0 ? "*"             : arr[0].IRRF_SN                        );
          document.getElementById("edtAliqIrrf").value    = ( arr.length == 0 ? "0,00"          : arr[0].IRRF_ALIQ                      );
          document.getElementById("edtPisSN").value       = ( arr.length == 0 ? "*"             : arr[0].PIS_SN                         );
          document.getElementById("edtPisAliq").value     = ( arr.length == 0 ? "0,00"          : arr[0].PIS_ALIQ                       );
          document.getElementById("edtCofinsSN").value    = ( arr.length == 0 ? "*"             : arr[0].COFINS_SN                      );
          document.getElementById("edtCofinsAliq").value  = ( arr.length == 0 ? "0,00"          : arr[0].COFINS_ALIQ                    );
          document.getElementById("edtCsllSN").value      = ( arr.length == 0 ? "*"             : arr[0].CSLL_SN                        );
          document.getElementById("edtCsllAliq").value    = ( arr.length == 0 ? "0,00"          : arr[0].CSLL_ALIQ                      );
          document.getElementById("edtIssSN").value       = ( arr.length == 0 ? "*"             : arr[0].ISS_SN                         );
          document.getElementById("edtCodCc").value       = ( arr.length == 0 ? "*"             : arr[0].CODCC                          );
          document.getElementById("edtAliqIss").value     = ( arr.length == 0 ? "0,00"          : arr[0].ISS_ALIQ                       );
          document.getElementById("edtIssRet").value      = ( arr.length == 0 ? "*"             : arr[0].RETIDO_SN                      );
          document.getElementById("edtObservacao").value  = document.getElementById("edtDesSrv").value;
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )  );
          document.getElementById("edtAliqIss").setAttribute("data-oldvalue",arr[0].ISS_ALIQ);
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
          document.getElementById(obj.id).value      = ( ret.length == 0 ? "FAT" : ret[0].CODIGO                    );
          document.getElementById("edtDesTd").value  = ( ret.length == 0 ? "*"   : ret[0].DESCRICAO                 );
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
          document.getElementById("edtDesFc").value = ( ret.length == 0 ? "*"      : ret[0].DESCRICAO               );
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
          document.getElementById("edtDesBnc").value  = ( ret.length == 0 ? "*" : ret[0].DESCRICAO                );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "BOL" : ret[0].CODIGO ));
        };
      };
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
          gerarMensagemErro("catch",e.message,"Erro");
        };  
      };
      //
      function buscarBd(opc){
        try{          
          clsJs=jsString("lote");            
          clsJs.add("login"   , jsPub[0].usr_login                                              );
          clsJs.add("rotina"  , "buscaSnf"                                                      );
          clsJs.add("codtd"   , document.getElementById("cbOpcao").value                        );
          clsJs.add("codfll"  , document.getElementById("edtCodFll").value                      );
          clsJs.add("entsai"  , (document.getElementById("edtCodPtp").value=="CP" ? "E" : "S")  );
          
          var fd = new FormData();
          fd.append("cadnfs" , clsJs.fim());
          msg=requestPedido("Trac_NfsCadTitulo.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            document.getElementById("edtCodTd").value       = retPhp[0].tblSnf["SNF_CODTD"];
            document.getElementById("edtDesTd").value       = retPhp[0].tblSnf["TD_NOME"];
            document.getElementById("edtCodSnf").value      = retPhp[0].tblSnf["SNF_CODIGO"];
            document.getElementById("edtEntSai").value      = retPhp[0].tblSnf["SNF_ENTSAI"];
            document.getElementById("edtNumNf").value       = jsNmrs(retPhp[0].tblSnf["SNF_NFFIM"]).emZero(6).ret();
            document.getElementById("edtInformarNf").value  = retPhp[0].tblSnf["SNF_INFORMARNF"];
            
            jsCmpAtivo("edtCodTd").remove("campo_input inputF10").add("campo_input_titulo").disabled(true);
            if( retPhp[0].tblSnf["SNF_INFORMARNF"]=="N" )
              jsCmpAtivo("edtNumNf").remove("campo_input inputF10").add("campo_input_titulo").disabled(true);  
            
          };  
        }catch(e){
          gerarMensagemErro("catch",e.message,"Erro");
        };
      };
      function numNfBlur(){ 
        document.getElementById("edtNumNf").value=document.getElementById("edtNumNf").value.replace(/\D/g,""); 
      };
      function issRetBlur(obj){
        obj.value=obj.value.toUpperCase();
        if( jsPub[0].emp_codetf !="S" ){
          if( obj.value=="S" ){
            var elem = document.getElementById("edtAliqIss");
            document.getElementById("edtAliqIss").value=jsNmrs(elem.getAttribute("data-oldvalue")).real().ret();
            jsCmpAtivo("edtAliqIss").remove("campo_input_titulo").add("campo_input").disabled(false).foco("edtAliqIss");
          } else {
            document.getElementById("edtAliqIss").value="0,00";  
            jsCmpAtivo("edtAliqIss").remove("campo_input").add("campo_input_titulo").disabled(true).foco("edtVlrEvento");
          }  
        };
        document.getElementById("edtAliqIss").value=(obj.value=="S" ? document.getElementById("edtAliqIss").getAttribute("data-oldvalue") : "0,00");
        calculaImposto();          
      };
      ///////////////////////////////////////////////
      // Se a empresa for do simples busco a aliquota
      ///////////////////////////////////////////////
      function dtDoctoBlur(){
        document.getElementById("edtCodCmp").value=jsDatas("edtDtDocto").retYYYYMM();
        if( jsPub[0].emp_codetf=="S" ){
          try{          
            clsJs=jsString("lote");            
            clsJs.add("login"   , jsPub[0].usr_login                         );
            clsJs.add("rotina"  , "buscaAliquota"                            );
            clsJs.add("codcmp"  , document.getElementById("edtCodCmp").value );
            
            var fd = new FormData();
            fd.append("cadnfs" , clsJs.fim());
            msg=requestPedido("Trac_NfsCadTitulo.php",fd); 
            retPhp=JSON.parse(msg);
            if( retPhp[0].retorno=="OK" ){
              if( retPhp[0].qtos==1 ){
                document.getElementById("edtAliqIss").value=jsNmrs(retPhp[0].tblFms["FMS_ALIQUOTA"]).real().ret();
                document.getElementById("edtAliqIss").setAttribute("data-oldvalue",jsNmrs(retPhp[0].tblFms["FMS_ALIQUOTA"]).real().ret());
                jsCmpAtivo("edtAliqIss").remove("campo_input input").add("campo_input_titulo").disabled(true);                  
              } else {
                jsCmpAtivo("edtAliqIss").remove("campo_input_titulo").add("campo_input input").disabled(true);                    
              }
            };  
          }catch(e){
            gerarMensagemErro("catch",e.message,"Erro");
          };
        };
        document.getElementById("edtVencto").value=jsDatas("edtDtDocto").retSomarDias(30).retDDMMYYYY(); 
      };
      function calculaImposto(){
        try{
          var floValor      = 0;
          var floAliqIss    = 0;
          var floVlrIss     = 0;
          var floAliqIrrf   = 0;
          var floVlrIrrf    = 0;
          var floAliqInss   = 0;
          var floVlrInss    = 0;
          var floAliqPis    = 0;
          var floBcPis      = 0;
          var floVlrPis     = 0;
          var floAliqCofins = 0;
          var floVlrCofins  = 0;
          var floAliqCsll   = 0;
          var floVlrCsll    = 0;
          //
          floValor      = jsNmrs("edtVlrEvento").dolar().ret();
          floAliqIss    = jsNmrs("edtAliqIss").dolar().ret();
          
          var clsNrm    = jsNmrs(floValor);
          floVlrIss     = clsNrm.percentual(floAliqIss).dolar().ret();
          if( document.getElementById("edtIssRet").value=="N" )
            floVlrIss     = 0;  
          floAliqIrrf   = jsNmrs("edtAliqIrrf").dolar().ret();
          floVlrIrrf    = clsNrm.percentual(floAliqIrrf).dolar().ret();
          if(floVlrIrrf<10){
            floAliqIrrf   = 0;
            floVlrIrrf    = 0;
          };
          floAliqInss   = jsNmrs("edtAliqInss").dolar().ret();
          floVlrInss    = clsNrm.percentual(floAliqInss).dolar().ret();
          
          floBcPis      = floValor;
          floAliqPis    = jsNmrs("edtPisAliq").dolar().ret();
          floVlrPis     = clsNrm.percentual(floAliqPis).dolar().ret();
          
          floAliqCofins = jsNmrs("edtCofinsAliq").dolar().ret();
          floVlrCofins  = clsNrm.percentual(floAliqCofins).dolar().ret();
          
          floAliqCsll   = jsNmrs("edtCsllAliq").dolar().ret();
          floVlrCsll    = clsNrm.percentual(floAliqCsll).dolar().ret();
          //--
          if( floValor>0 ){
            document.getElementById("edtValorIss").value     = jsNmrs(floVlrIss).real().ret();
            document.getElementById("edtValorIrrf").value    = jsNmrs(floVlrIrrf).real().ret();
            document.getElementById("edtValorInss").value    = jsNmrs(floVlrInss).real().ret();
            document.getElementById("edtPisBc").value        = jsNmrs(floBcPis).real().ret();          
            document.getElementById("edtValorPis").value     = jsNmrs(floVlrPis).real().ret();          
            document.getElementById("edtValorCofins").value  = jsNmrs(floVlrCofins).real().ret();
            document.getElementById("edtValorCsll").value    = jsNmrs(floVlrCsll).real().ret();
            document.getElementById("edtVlrLiquido").value   = jsNmrs(floValor-floVlrIss-floVlrIrrf-floVlrInss-floVlrPis-floVlrCofins-floVlrCsll).real().ret();
            document.getElementById("edtValorRetido").value  = jsNmrs(floVlrIss+floVlrIrrf+floVlrInss+floVlrPis+floVlrCofins+floVlrCsll).real().ret();
          }; 
        } catch(e){
          gerarMensagemErro("catch",e.message,"Erro");          
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
                ,debcre : ( document.getElementById("edtCodPtp").value=="CR" ? "C" : "D" )
              }  
            )
            totGrd+=jsNmrs(tbl.rows[lin].cells[2].innerHTML).dec(2).dolar().ret();
          };  
        };
        //
        //
        try{  
          document.getElementById("edtCodFvr").value     = document.getElementById("edtCodFvr").value.soNumeros();
          document.getElementById("edtNumNf").value      = document.getElementById("edtNumNf").value.soNumeros();
          document.getElementById("edtCodCdd").value      = document.getElementById("edtCodCdd").value.soNumeros();
          document.getElementById("edtCodTd").value      = jsStr("edtCodTd").upper().alltrim().ret();    
          document.getElementById("edtCodFc").value      = jsStr("edtCodFc").upper().alltrim().ret();    
          document.getElementById("edtCodBnc").value     = document.getElementById("edtCodBnc").value.soNumeros();
          document.getElementById("edtCodSrv").value     = document.getElementById("edtCodSrv").value.soNumeros();
          document.getElementById("edtObservacao").value = jsStr("edtObservacao").upper().tamMax(120).ret();           
          document.getElementById("edtIntervalo").value  = document.getElementById("edtIntervalo").value.soNumeros();
          document.getElementById("edtNumPar").value     = document.getElementById("edtNumPar").value.soNumeros();
        
          msg = new clsMensagem("Erro");
          msg.intMaiorZero("COD_FAVORECIDO"     , document.getElementById("edtCodFvr").value                  );
          msg.diferente("NOME_FAVORECIDO"       , document.getElementById("edtDesFvr").value,"*"              );
          msg.intMaiorZero("COD_SERVICO"        , document.getElementById("edtCodSrv").value                  );
          msg.tamFixo("COD_CIDADE"              , document.getElementById("edtCodCdd").value,7                );
          msg.diferente("NOME_SERVICO"          , document.getElementById("edtDesSrv").value,"*"              );
          msg.intMaiorZero("NF"                 , document.getElementById("edtNumNf").value                   );
          msg.notNull("EMISSAO"                 , document.getElementById("edtDtDocto").value                 );
          msg.floMaiorZero("VALOR_EVENTO"       , document.getElementById("edtVlrEvento").value               );
          msg.notNull("COD_TIPODOCUMENTO"       , document.getElementById("edtCodTd").value                   );
          msg.diferente("NOME_TIPODOCUMENTO"    , document.getElementById("edtDesTd").value,"*"               );
          msg.notNull("COD_FORMACOBRANCA"       , document.getElementById("edtCodFc").value                   );
          msg.diferente("NOME_FORMACOBRANCA"    , document.getElementById("edtDesFc").value,"*"               );
          msg.intMaiorZero("COD_BANCO"          , document.getElementById("edtCodBnc").value                  );
          msg.diferente("NOME_BANCO"            , document.getElementById("edtDesBnc").value,"*"              );
          msg.intMaiorZero("COD_FILIAL"         , document.getElementById("edtCodFll").value                  );
          msg.notNull("COD_TIPO"                , document.getElementById("edtCodPtp").value                  );
          msg.intMaiorZero("COMPETENCIA"        , document.getElementById("edtCodCmp").value                  );
          msg.notNull("CONTA_CONTABIL"          , document.getElementById("edtCodCc").value                   );
          msg.notNull("OBSERVACAO"              , document.getElementById("edtObservacao").value              );
          msg.intMaiorZero("PARCELA"            , document.getElementById("edtNumPar").value                  );
          msg.notNull("VENCTO"                  , document.getElementById("edtVencto").value                  );
          msg.intMaiorZero("INTERVALO"          , document.getElementById("edtIntervalo").value               );
          msg.contido("TIPO"                    , document.getElementById("edtCodPtp").value,["CP","CR"]      );
          msg.contido("FISICA_JURIDICA"         , document.getElementById("edtFisJur").value,["F","J"]        );
          msg.notNull("CATEGORIA"               , document.getElementById("edtCategoria").value               );
          msg.intMaiorZero("SERIE_NF"           , document.getElementById("edtCodSnf").value                  );
          msg.contido("INSS_SN"                 , document.getElementById("edtInssSN").value,["S","N"]        );
          msg.contido("IRRF_SN"                 , document.getElementById("edtIrrfSN").value,["S","N"]        );
          msg.contido("PIS_SN"                  , document.getElementById("edtPisSN").value,["S","N"]         );
          msg.contido("COFINS_SN"               , document.getElementById("edtCofinsSN").value,["S","N"]      );
          msg.contido("CSLL_SN"                 , document.getElementById("edtCsllSN").value,["S","N"]        );          
          msg.contido("ISS_SN"                  , document.getElementById("edtIssSN").value,["S","N"]         );
          msg.floMaiorIgualZero("INSS_BC"       , document.getElementById("edtInssBC").value                  );
          msg.floMaiorIgualZero("PIS_ALIQ"      , document.getElementById("edtPisAliq").value                 );
          msg.floMaiorIgualZero("COFINS_ALIQ"   , document.getElementById("edtCofinsAliq").value              );
          msg.floMaiorIgualZero("CSLL_ALIQ"     , document.getElementById("edtCsllAliq").value                );
          msg.floMaiorIgualZero("PIS_BC"        , document.getElementById("edtPisBc").value                   );
          msg.contido("ENTRADA_SAIDA"           , document.getElementById("edtEntSai").value,["E","S"]        );          
          msg.contido("INFORMAR_NF"             , document.getElementById("edtInformarNf").value,["S","N"]    );
          msg.contido("OPCAO"                   , document.getElementById("cbOpcao").value,["NFS","REC","RPS"]);          
          //
          if( jsNmrs(totGrd).dec(2).real().ret() != jsNmrs("edtVlrEvento").dec(2).real().ret() )
            msg.add("CAMPO<b> VALOR EVENTO "+jsNmrs("edtVlrEvento").dec(2).dolar().ret()+ "</b>DIVERGE DO TOTAL GRADE PARCELAMENTO "+totGrd+"!");           
          //
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            ////////////////////////////////
            // Armazenando para envio ao Php
            ////////////////////////////////
            let clsRat = jsString("rateio");
            clsRat.principal(false);
            
            let clsDup = jsString("duplicata");
            clsDup.principal(false);

            let clsNfs = jsString("servico");
            clsNfs.principal(false);
            clsNfs.add("numnf"        , document.getElementById("edtNumNf").value       );
            clsNfs.add("codsnf"       , document.getElementById("edtCodSnf").value      );
            clsNfs.add("vlrretencao"  , jsNmrs("edtValorRetido").dolar().ret()          );
            //clsNfs.add("livro"        , "S"                                             );
            clsNfs.add("codvnd"       , 0                                               );
            clsNfs.add("codcdd"       , document.getElementById("edtCodCdd").value      );
            clsNfs.add("contrato"     , 0                                               );
            clsNfs.add("entsai"       , document.getElementById("edtEntSai").value      );
            clsNfs.add("codsrv"       , document.getElementById("edtCodSrv").value      );
            clsNfs.add("aliqinss"     , jsNmrs("edtAliqInss").dolar().ret()             );
            clsNfs.add("bcinss"       , jsNmrs("edtVlrEvento").dolar().ret()            );
            clsNfs.add("vlrinss"      , jsNmrs("edtValorInss").dolar().ret()            );            
            clsNfs.add("aliqirrf"     , jsNmrs("edtAliqIrrf").dolar().ret()             );
            clsNfs.add("vlrirrf"      , jsNmrs("edtValorInss").dolar().ret()            );            
            clsNfs.add("aliqpis"      , jsNmrs("edtPisAliq").dolar().ret()              );
            clsNfs.add("vlrpis"       , jsNmrs("edtValorPis").dolar().ret()             );            
            clsNfs.add("aliqcofins"   , jsNmrs("edtCofinsAliq").dolar().ret()           );
            clsNfs.add("vlrcofins"    , jsNmrs("edtValorCofins").dolar().ret()          );            
            clsNfs.add("aliqcsll"     , jsNmrs("edtCsllAliq").dolar().ret()             );
            clsNfs.add("vlrcsll"      , jsNmrs("edtValorCsll").dolar().ret()            );            
            clsNfs.add("aliqiss"      , jsNmrs("edtAliqIss").dolar().ret()              );
            clsNfs.add("vlriss"       , jsNmrs("edtValorIss").dolar().ret()             );            
            clsNfs.add("informarnf"   , document.getElementById("edtInformarNf").value  );    
            clsNfs.add("opcao"        , document.getElementById("cbOpcao").value        );    
            let servico = clsNfs.fim();            
            
            
            arrPrcl.forEach(function(reg){
              clsRat.add("parcela"    , reg.parc                                  );
              clsRat.add("codcc"      , document.getElementById("edtCodCc").value );
              clsRat.add("debito"     , (reg.debcre=="D" ? reg.valor : 0)         );
              clsRat.add("credito"    , (reg.debcre=="C" ? reg.valor : 0)         );

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
            clsFin.add("verdireito"         , 27                                              );            
            clsFin.add("codbnc"             , document.getElementById("edtCodBnc").value      );
            clsFin.add("codcc"              , "NULL"                                          );  //Se NULL o trigger faz    
            clsFin.add("codcmp"             , document.getElementById("edtCodCmp").value      );  //Competencia contabil          
            clsFin.add("codfvr"             , document.getElementById("edtCodFvr").value      );
            clsFin.add("codfc"              , document.getElementById("edtCodFc").value       );
            clsFin.add("codtd"              , document.getElementById("edtCodTd").value       );
            clsFin.add("codfll"             , document.getElementById("edtCodFll").value      );
            clsFin.add("codptt"             , "F"                                             );            
            clsFin.add("docto"              , "NFS"+jsNmrs("edtNumNf").emZero(6).ret()        );
            clsFin.add("dtdocto"            , jsDatas("edtDtDocto").retMMDDYYYY()             );
            clsFin.add("lancto"             , 0                                               );  //Se maior que zero eh rotina de alteracao            
            clsFin.add("observacao"         , document.getElementById("edtObservacao").value  );
            clsFin.add("codpt"              , 0                                               );
            clsFin.add("codptp"             , document.getElementById("edtCodPtp").value      );
            clsFin.add("vlrdesconto"        , 0                                               );
            clsFin.add("vlrevento"          , jsNmrs("edtVlrEvento").dolar().ret()            );
            clsFin.add("vlrmulta"           , 0                                               );
            clsFin.add("vlrretencao"        , 0                                               );
            clsFin.add("vlrpis"             , 0                                               );
            clsFin.add("vlrcofins"          , 0                                               );
            clsFin.add("vlrcsll"            , 0                                               );
            clsFin.add("temnfp"             , "N"                                             );
            clsFin.add("temnfs"             , "S"                                             );            
            clsFin.add("DUPLICATA"          , duplicata                                       );
            clsFin.add("RATEIO"             , rateio                                          );
            clsFin.add("SERVICO"            , servico                                         );
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
              gerarMensagemErro("cad",retPhp[0].erro,"AVISO","edtCodFvr");  
              document.getElementById("frmNfs").newRecord("data-newrecord");
              document.getElementById("cbOpcao").focus();
              minhaAba.limparTable("tblPrcl");
            };
          };  
        } catch(e){
          gerarMensagemErro("nfs",e,"Erro");          
        };  
      };
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Cadastro NF serviço</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmNfs" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 550px;">
          <div class="campotexto campo100"  style="margin-top:4px;">
            <div class="campotexto campo10">
              <h2>Selecione</h2>
            </div>  
            <div class="campotexto campo20">
              <select class="campo_input_combo" id="cbOpcao"
                                                OnChange="document.getElementById('lblNumNf').innerHTML=document.getElementById('cbOpcao').options[document.getElementById('cbOpcao').selectedIndex].text;" >
                <option value="NFS">NOTA FISCAL</option>
                <option value="REC">RECIBO</option>
                <!--<option value="RPS">RPS</option>-->
              </select>
              <label class="campo_label campo_required" for="cbOpcao">INFORME:</label>
            </div>  
            
            

            <div class="campotexto campo10">
              <input class="campo_input input" id="edtCodFll" 
                                               OnKeyPress="return mascaraInteiro(event);" 
                                               data-newrecord="eval jsNmrs(jsPub[0].emp_codfll).emZero(4).ret()"
                                               type="text" 
                                               maxlength="4"/>
              <label class="campo_label campo_required" for="edtCodFll">FILIAL:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo" id="edtCodCdd" 
                                                data-newrecord="*******"
                                                type="text" 
                                                maxlength="7" disabled />
              <label class="campo_label campo_required" for="edtCodCdd">MUNICIPIO:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo input" id="edtVenctoContrato"
                                                      data-newrecord="**/**/****"              
                                                      type="text" disabled />
              <label class="campo_label" for="edtVenctoContrato">VENCTO:</label>
            </div>
            
            <div class="campotexto campo15">
              <input class="campo_input_titulo input" id="edtContrato"
                                                      data-newrecord="000000"              
                                                      type="text" disabled />
              <label class="campo_label" for="edtContrato">LANCTO CONTRATO:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo input" id="edtCodPtp" 
                                                      data-newrecord="CR"
                                                      type="text" disabled />
              <label class="campo_label campo_required" for="edtCodPtp">TIPO:</label>
            </div>
            <div class="campotexto campo15">
              <input class="campo_input_titulo input" id="edtCodCc" 
                                                      data-newrecord="*"              
                                                      type="text" disabled />
              <label class="campo_label campo_required" for="edtCodCc">CONTABIL:</label>
            </div>
          </div>

          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodFvr"
                                                OnKeyPress="return mascaraInteiro(event);"
                                                onBlur="codFvrBlur(this);" 
                                                onFocus="buscarBd('FVR');fvrFocus(this);" 
                                                onClick="fvrF10Click(this);"
                                                data-newrecord="0000"                                                
                                                data-oldvalue="0000" 
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodFvr">FAVORECIDO:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesFvr" 
                                                    data-newrecord="*"            
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesFvr">NOME</label>
          </div>
          
          
          
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodSrv"
                                                OnKeyPress="return mascaraInteiro(event);"
                                                onBlur="codSrvBlur(this);" 
                                                onFocus="srvFocus(this);" 
                                                onClick="srvF10Click(this);"
                                                data-newrecord="0000"
                                                data-oldvalue="0000" 
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodSrv">SERVICO:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesSrv" 
                                                    data-newrecord="*"
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesSrv">SERVICO_NOME</label>
          </div>
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
            <input class="campo_input_titulo input" id="edtDesTd" 
                                                    data-newrecord="*"
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesTd">TIPO DOCTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodFc"
                                                onBlur="codFcBlur(this);" 
                                                onFocus="fcFocus(this);" 
                                                onClick="fcF10Click(this);"
                                                data-newrecord="BOL"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="3"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodFc">FC:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesFc" 
                                                    data-newrecord="BOLETO"            
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesFc">FORMA COBR:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodBnc"
                                                onBlur="codBncBlur(this);" 
                                                onFocus="bncFocus(this);" 
                                                onClick="bncF10Click(this);"
                                                data-newrecord="eval jsNmrs(jsPub[0].emp_codbnc).emZero(4).ret()"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="4"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodBnc">BANCO:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesBnc" 
                                                    data-newrecord="eval jsPub[0].emp_desbnc"  
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBnc">BANCO_NOME:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input input" id="edtObservacao" type="text" maxlength="120"/>
            <label class="campo_label campo_required" for="edtObservacao">OBSERVAÇÃO:</label>
          </div>

          <div class="campotexto campo10">
            <input class="campo_input input" id="edtNumNf" 
                                              OnKeyPress="return mascaraInteiro(event);"
                                              onBlur="numNfBlur();"                                               
                                              type="text" 
                                              data-newrecord="000000"
                                              maxlength="6"/>
            <label id="lblNumNf" class="campo_label campo_required" for="edtCodNumNf">NOTA FISCAL:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtDtDocto" 
                                             onBlur="dtDoctoBlur();"                                                           
                                             placeholder="##/##/####"             
                                             data-newrecord="nrHoje"            
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtDtDocto">EMISSÂO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtCodCmp" 
                                              data-newrecord="eval jsDatas('edtDtDocto').retYYYYMM();"            
                                              type="text" 
                                              maxlength="6" disabled />
            <label class="campo_label" for="edtCodCmp">COMPETENCIA:</label>
          </div>
          
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtIssRet"
                                             onBlur="issRetBlur(this);"  
                                             data-newrecord="N" 
                                             type="text" maxlength="1"/>
            <label class="campo_label" for="edtIssRet">ISS RETIDO:</label>
          </div>
          <div class="campotexto campo05">
            <!--
            Para presumido obrigatorio calcular ISSFAT 
            Guardo a aliquota em data-aliqIssFat
            -->
            <input class="campo_input input edtDireita" id              = "edtAliqIss"
                                                        onBlur          = "calculaImposto();"  
                                                        data-oldvalue   = "0,00"  
                                                        data-newrecord  = "0,00" 
                                                        data-aliqIssFat = "0,00"  
                                                        type="text" />
            <label class="campo_label" for="edtAliqIss">%ISS</label>
          </div>        
          <div class="campotexto campo15">
            <input class="campo_input input edtDireita" id="edtVlrEvento"
                                                        onBlur="calculaImposto();fncCasaDecimal(this,2)"  
                                                        data-newrecord="0,00" type="text" />
            <label class="campo_label campo_required" for="edtVlrEvento">VALOR TOTAL:</label>
          </div>
          <div class="campotexto campo05">
            <input class="campo_input_titulo input edtDireita" id="edtAliqIrrf" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtAliqIrrf">%IRRF</label>
          </div>
          <div class="campotexto campo05">
            <input class="campo_input_titulo input edtDireita" id="edtAliqInss" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtAliqInss">%INSS</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input_titulo input edtDireita" id="edtVlrLiquido" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtVlrLiquido">VALOR LIQUIDO:</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input_titulo input edtDireita" id="edtValorIss" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtValorIss">VALOR ISS:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input edtDireita" id="edtValorIrrf" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtValorIrrf">VALOR IRRF:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input edtDireita" id="edtValorInss" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtValorInss">VALOR INSS:</label>
          </div>  
          <div class="campotexto campo10">
            <input class="campo_input_titulo input edtDireita" id="edtValorPis" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtValorPis">VALOR PIS:</label>
          </div>    
          <div class="campotexto campo10">
            <input class="campo_input_titulo input edtDireita" id="edtValorCofins" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtValorCofins">VALOR COFINS:</label>
          </div>            
          <div class="campotexto campo10">
            <input class="campo_input_titulo input edtDireita" id="edtValorCsll" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtValorCsll">VALOR CSLL:</label>
          </div> 
          <div class="campotexto campo10">
            <input class="campo_input_titulo input edtDireita" id="edtValorRetido" 
                                                               data-newrecord="0,00" type="text" disabled />
            <label class="campo_label" for="edtValorRetido">VALOR RETIDO:</label>
          </div> 
          
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtNumPar"
                                             data-newrecord="01"            
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
                                             data-newrecord="30"
                                             OnBlur="fncCalculaParcelamento();"
                                             type="text" 
                                             maxlength="2"/>
            <label class="campo_label campo_required" for="edtIntervalo">INTERVALO (Dias) </label>
          </div>
          <div class="inactive">
            <!--<input id="edtDebCre" type="text" />-->
            <!-- Tem que checar estes dois campos antes de gravar -->
            <input id="edtEntSai" data-newrecord="*" type="text" />
            <input id="edtFisJur" data-newrecord="*" type="text" />
            <input id="edtCategoria" data-newrecord="*" type="text" />
            <input id="edtCodSnf" data-newrecord="0" type="text" />
            <input id="edtInssSN" data-newrecord="*" type="text" />
            <input id="edtIrrfSN" data-newrecord="*" type="text" />
            <input id="edtPisSN" data-newrecord="*" type="text" />
            <input id="edtCofinsSN" data-newrecord="*" type="text" />
            <input id="edtCsllSN" data-newrecord="*" type="text" />
            <input id="edtIssSN" data-newrecord="*" type="text" />
            <input id="edtInssBC" data-newrecord="0,00" type="text" />
            <input id="edtPisAliq" data-newrecord="0,00" type="text" />
            <input id="edtCofinsAliq" data-newrecord="0,00" type="text" />
            <input id="edtCsllAliq" data-newrecord="0,00" type="text" />
            <input id="edtPisBc" data-newrecord="0,00" type="text" />
            <input id="edtInformarNf" data-newrecord="*" type="text" />
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