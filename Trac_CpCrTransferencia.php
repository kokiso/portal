<?php
  session_start();
  if( isset($_POST["transferencia"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      //require("classPhp/removeAcento.class.php");
      //require("classPhp/validaCampo.class.php"); 
      
      function fncPg($a, $b) {
       return $a["PDR_NOME"] > $b["PDR_NOME"];
      };

      function fncPt($a, $b) {
       return $a["PT_NOME"] > $b["PT_NOME"];
      };
      
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["transferencia"]);
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
        if( $rotina=="gravartransf" ){
          /////////////////////////////////////////////
          // BUSCANDO O FAVORECIDO PELO BANCO DEBITO
          ///////////////////////////////////////////// 
          $erro="ok";
          $sql="";
          $sql.="SELECT PT_CODIGO,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC";
          $sql.="  FROM PADRAOTITULO";
          $sql.="  LEFT OUTER JOIN PADRAO PDR ON PT_CODPDR=PDR.PDR_CODIGO";
          $sql.=" WHERE PDR.PDR_CODPTT='T'";
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"] != 2 ){        
            $erro="OBRIGATÓRIO DOIS LANCTO DE TRANSFERÊNCIA EM PADRAOTITULO!";
          } else {
            $tblPt=$retCls["dados"];
            foreach ( $tblPt as $pt ){
              if( $pt["PT_DEBCRE"]=="D" ){
                $codPtDeb = $pt["PT_CODIGO"];
                $codFcDeb = $pt["PT_CODFC"];  
                $codTdDeb = $pt["PT_CODTD"];
                $codCcDeb = $pt["PT_CODCC"];  
              } else {
                $codPtCre = $pt["PT_CODIGO"];
                $codFcCre = $pt["PT_CODFC"];  
                $codTdCre = $pt["PT_CODTD"];
                $codCcCre = $pt["PT_CODCC"];  
              };
            };
            if( !isset($codPtDeb) )
              $erro="NÃO LOCALIZADO TRANSFERENCIA A DEBITO EM PADRAOTITULO!";
            if( !isset($codPtCre) )
              $erro="NÃO LOCALIZADO TRANSFERENCIA A CREDITO EM PADRAOTITULO!";
          };
          if( $erro != "ok"){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';  
          } else {
            /////////////////////////////////////////////
            // Gravando na PAGAR A TRANSFERENCIA A DEBITO
            /////////////////////////////////////////////  
            $lancto=$classe->generator("PAGAR"); 
            $docto    = "TD".str_pad($lancto, 6, "0", STR_PAD_LEFT);
            
            $sql="";
            $sql="INSERT INTO VPAGAR(";
            $sql.="PGR_LANCTO";
            $sql.=",PGR_BLOQUEADO";
            $sql.=",PGR_CHEQUE";          
            $sql.=",PGR_CODBNC";
            $sql.=",PGR_CODFVR";
            $sql.=",PGR_CODFC";
            $sql.=",PGR_CODTD";
            $sql.=",PGR_VENCTO";
            $sql.=",PGR_DATAPAGA";          
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
            $sql.=",'N'";                               // BLOQUEADO
            $sql.=",'" .$docto."'";                     // CHEQUE
            $sql.=","  .$lote[0]->codbncdeb;            // CODBNC
            $sql.=","  .$lote[0]->codfvrdeb;            // CODFVR
            $sql.=",'" .$codFcDeb."'";                  // CODFC
            $sql.=",'" .$codTdDeb."'";                  // CODTD
            $sql.=",'" .$lote[0]->dtdocto."'";          // VENCTO
            $sql.=",'" .$lote[0]->dtdocto."'";          // DATAPAGA
            $sql.=",'" .$docto."'";                     // DOCTO
            $sql.=",'" .$lote[0]->dtdocto."'";          // DTDOCTO
            $sql.=",'T'";                               // CODPTT
            $sql.=","  .$lancto;                        // MASTER
            $sql.=",'" .$lote[0]->observacao."'";       // OBSERVACAO
            $sql.=",'CP'";                              // CODPTP
            $sql.=",".$codPtDeb;                        // CODPT
            $sql.=",'" .$lote[0]->vlrevento."'";        // VLREVENTO
            $sql.=",'" .$lote[0]->vlrevento."'";        // VLRPARCELA
            $sql.=",0";                                 // VLRDESCONTO
            $sql.=",null";                              // CODCC
            $sql.=",0";                                 // CODSNF
            $sql.=",'S'";                               // APR
            $sql.=","  .$_SESSION["emp_codigo"];        // CODEMP
            $sql.=","  .$lote[0]->codfll;               // CODFLL
            $sql.=",28";                                // VERDIREITO
            $sql.=","  .$lote[0]->codcmp;               // CODCMP
            $sql.=",'P'";                               // REG
            $sql.=","  .$_SESSION["usr_codigo"];        // CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);  
            //
            ////////////////////////////////////////////
            // Gravando na RATEIO TRANSFERENCIA A DEBITO
            ////////////////////////////////////////////
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
            $sql.="'$lancto'";                      // RAT_LANCTO
            $sql.=",'" .$codCcDeb."'";              // RAT_CODCC
            $sql.="," .$lote[0]->vlrevento;         // RAT_DEBITO
            $sql.=",0";                             // RAT_CREDITO
            $sql.="," .$_SESSION["emp_codigo"];     // RAT_CODEMP
            $sql.="," .$lote[0]->codcmp;            // RAT_CODCMP
            $sql.=",'S'";                           // RAT_CONTABIL
            $sql.=","  .$_SESSION["usr_codigo"];    // RAT_CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);            
            //
            //  
            /////////////////////////////////////////////
            // BUSCANDO O FAVORECIDO PELO BANCO CREDITO
            /////////////////////////////////////////////  
            $sql="SELECT BNC_CODFVR FROM BANCO WHERE BNC_CODIGO=".$lote[0]->codbnccre;
            $classe->msgSelect(false);
            $retCls=$classe->selectAssoc($sql);
            $codfvr=$retCls["dados"][0]["BNC_CODFVR"];
            /////////////////////////////////////////////
            // Gravando na PAGAR A TRANSFERENCIA A DEBITO
            /////////////////////////////////////////////  
            $lancto=$classe->generator("PAGAR"); 
            $docto    = "TC".str_pad($lancto, 6, "0", STR_PAD_LEFT);
            
            $sql="";
            $sql="INSERT INTO VPAGAR(";
            $sql.="PGR_LANCTO";
            $sql.=",PGR_BLOQUEADO";
            $sql.=",PGR_CHEQUE";          
            $sql.=",PGR_CODBNC";
            $sql.=",PGR_CODFVR";
            $sql.=",PGR_CODFC";
            $sql.=",PGR_CODTD";
            $sql.=",PGR_VENCTO";
            $sql.=",PGR_DATAPAGA";          
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
            $sql.=",'N'";                               // BLOQUEADO
            $sql.=",'" .$docto."'";                     // CHEQUE
            $sql.=","  .$lote[0]->codbncdeb;            // CODBNC
            $sql.=","  .$lote[0]->codfvrcre;            // CODFVR
            $sql.=",'" .$codFcCre."'";                  // CODFC
            $sql.=",'" .$codTdCre."'";                  // CODTD
            $sql.=",'" .$lote[0]->dtdocto."'";          // VENCTO
            $sql.=",'" .$lote[0]->dtdocto."'";          // DATAPAGA
            $sql.=",'" .$docto."'";                     // DOCTO
            $sql.=",'" .$lote[0]->dtdocto."'";          // DTDOCTO
            $sql.=",'T'";                               // CODPTT
            $sql.=","  .$lancto;                        // MASTER
            $sql.=",'" .$lote[0]->observacao."'";       // OBSERVACAO
            $sql.=",'CR'";                              // CODPTP
            $sql.=",".$codPtCre;                        // CODPT
            $sql.=",'" .$lote[0]->vlrevento."'";        // VLREVENTO
            $sql.=",'" .$lote[0]->vlrevento."'";        // VLRPARCELA
            $sql.=",0";                                 // VLRDESCONTO
            $sql.=",null";                              // CODCC
            $sql.=",0";                                 // CODSNF
            $sql.=",'S'";                               // APR
            $sql.=","  .$_SESSION["emp_codigo"];        // CODEMP
            $sql.=","  .$lote[0]->codfll;               // CODFLL
            $sql.=",28";                                // VERDIREITO
            $sql.=","  .$lote[0]->codcmp;               // CODCMP
            $sql.=",'P'";                               // REG
            $sql.=","  .$_SESSION["usr_codigo"];        // CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);  
            //
            ////////////////////////////////////////////
            // Gravando na RATEIO TRANSFERENCIA A DEBITO
            ////////////////////////////////////////////
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
            $sql.="'$lancto'";                      // RAT_LANCTO
            $sql.=",'" .$codCcCre."'";              // RAT_CODCC
            $sql.=",0";                             // RAT_DEBITO          
            $sql.="," .$lote[0]->vlrevento;         // RAT_CREDITO
            $sql.="," .$_SESSION["emp_codigo"];     // RAT_CODEMP
            $sql.="," .$lote[0]->codcmp;            // RAT_CODCMP
            $sql.=",'S'";                           // RAT_CONTABIL
            $sql.=","  .$_SESSION["usr_codigo"];    // RAT_CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);            
            $atuBd = true;
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
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaBancoF10.js"></script>        
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        pega=JSON.parse(localStorage.getItem("addAlt")).lote[0];
        localStorage.removeItem("addAlt");
        document.getElementById("edtCodFll").value    = jsNmrs(jsPub[0].emp_codfll).emZero(4).ret();
        document.getElementById("edtDtDocto").value   = jsDatas(0).retDDMMYYYY();
        document.getElementById("edtDocto").value     = "TD000000";
        document.getElementById("edtVlrEvento").value = "0,00";
        document.getElementById("edtCodBncDeb").value = "0000";
        document.getElementById("edtDesBncDeb").value = "";
        document.getElementById("edtCodBncCre").value = "0000";
        document.getElementById("edtDesBncCre").value = "";
        document.getElementById("edtObservacao").value = "TRANSFERENCIA ENTRE CONTAS";
        document.getElementById("edtDtDocto").focus();
        
        if( jsPub[0].emp_fllunica=="S" ){
          jsCmpAtivo("edtCodFll").remove("campo_input").add("campo_input_titulo").disabled(true);
        };
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
      var objBncF10;                  // Obrigatório para instanciar o JS BancoF10      
      var pega;                       // Recuperar localStorage;     
      var debcre;  
      //var minhaAba;
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
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
        fd.append("transferencia" , clsJs.fim());
        msg     = requestPedido("Trac_CpCrTransferencia.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          objGlobal.tblPg=retPhp[0]["tblPg"];
          objGlobal.tblPt=retPhp[0]["tblPt"];
        }
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
          document.getElementById("edtObservacao").value  = arrFilter[0].PT_NOME;
        };
      };
      ///////////////////////////////
      //  AJUDA PARA BANCO DEBITO  //
      ///////////////////////////////
      function bncDebFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bncDebF10Click(obj){ 
        fBancoF10(0,obj.id,"edtCodBncCre",100); 
      };
      function RetF10tblBnc(arr){
        if( debcre=="debito" ){
          document.getElementById("edtCodBncDeb").value  = arr[0].CODIGO;
          document.getElementById("edtDesBncDeb").value  = arr[0].DESCRICAO;
          document.getElementById("edtCodFvrDeb").value  = arr[0].CODFVR;
          document.getElementById("edtCodBncDeb").setAttribute("data-oldvalue",arr[0].CODIGO);
        } else {
          document.getElementById("edtCodBncCre").value  = arr[0].CODIGO;
          document.getElementById("edtDesBncCre").value  = arr[0].DESCRICAO;
          document.getElementById("edtCodFvrCre").value  = arr[0].CODFVR;
          document.getElementById("edtCodBncCre").setAttribute("data-oldvalue",arr[0].CODIGO);
        ;}
      };
      function codBncDebBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fBancoF10(1,obj.id,"edtCodBncCre",100); 
          document.getElementById(obj.id).value         = ( ret.length == 0 ? "0000"     : ret[0].CODIGO            );
          document.getElementById("edtDesBncDeb").value = ( ret.length == 0 ? ""         : ret[0].DESCRICAO         );
          document.getElementById("edtCodFvrDeb").value = ( ret.length == 0 ? "0000"     : ret[0].CODFVR            );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "BOL" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //  AJUDA PARA BANCO CREDITO //
      ///////////////////////////////
      function bncCreFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bncCreF10Click(obj){ 
        fBancoF10(0,obj.id,"edtObservacao",100); 
      };
      function codBncCreBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fBancoF10(1,obj.id,"edtObservacao",100); 
          document.getElementById(obj.id).value           = ( ret.length == 0 ? "0000"  : ret[0].CODIGO     );
          document.getElementById("edtDesBncCre").value   = ( ret.length == 0 ? ""      : ret[0].DESCRICAO  );
          document.getElementById("edtCodFvrCre").value   = ( ret.length == 0 ? "0000"  : ret[0].CODFVR     );          
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "BOL" : ret[0].CODIGO ));
        };
      };
      
      function fncGravar(){
        try{  
          document.getElementById("edtCodBncDeb").value     = document.getElementById("edtCodBncDeb").value.soNumeros();
          document.getElementById("edtCodBncCre").value     = document.getElementById("edtCodBncCre").value.soNumeros();
          document.getElementById("edtObservacao").value = jsStr("edtObservacao").upper().tamMax(120).ret();           
        
          msg = new clsMensagem("Erro");
          msg.notNull("DOCTO"                       , document.getElementById("edtDocto").value             );
          msg.notNull("EMISSAO"                     , document.getElementById("edtDtDocto").value           );
          msg.floMaiorIgualZero("VALOR_EVENTO"      , document.getElementById("edtVlrEvento").value         );
          msg.intMaiorZero("COD_BANCO_DEBITO"       , document.getElementById("edtCodBncDeb").value         );
          msg.intMaiorZero("COD_BANCO_CREDITO"      , document.getElementById("edtCodBncCre").value         );
          msg.intMaiorZero("COD_FAVORECIDO_DEBITO"  , document.getElementById("edtCodFvrDeb").value         );          
          msg.intMaiorZero("COD_FAVORECIDO_CREDITO" , document.getElementById("edtCodFvrCre").value         );     
          msg.diferente("BANCO_DEBITO"              , jsNmrs("edtCodBncDeb").inteiro().ret(),jsNmrs("edtCodBncCre").inteiro().ret()   );          
          msg.notNull("NOME_BANCO_DEBITO"           , document.getElementById("edtDesBncDeb").value         );
          msg.intMaiorZero("COD_FILIAL"             , document.getElementById("edtCodFll").value            );
          msg.notNull("OBSERVACAO"                  , document.getElementById("edtObservacao").value        );
          msg.notNull("VENCTO"                      , document.getElementById("edtDtDocto").value           );
          //
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            //////////////////////////////////////////////////
            // Armazenando para envio ao Php o lancto a debito
            //////////////////////////////////////////////////
            let clsFin = jsString("lote");
            clsFin.add("login"              , jsPub[0].usr_login  );
            clsFin.add("rotina"             , "gravartransf"      );
            ///////////////////////////////////////////////////////////////////////////////////
            // verdireito
            // Como vem de NFP/NFS/CONTRATO/TARIFA/TRANSF aqui informo qual direito vou olhar
            // pois um usuario pode lancar contrato mas naum NFProduto
            ///////////////////////////////////////////////////////////////////////////////////
            clsFin.add("codbncdeb"          , document.getElementById("edtCodBncDeb").value   );
            clsFin.add("codfvrdeb"          , document.getElementById("edtCodFvrDeb").value   );
            clsFin.add("codbnccre"          , document.getElementById("edtCodBncCre").value   );
            clsFin.add("codfvrcre"          , document.getElementById("edtCodFvrCre").value   );
            clsFin.add("codcmp"             , jsDatas("edtDtDocto").retYYYYMM()               );  //Competencia contabil          
            clsFin.add("codfll"             , document.getElementById("edtCodFll").value      );
            clsFin.add("docto"              , document.getElementById("edtDocto").value       );
            clsFin.add("dtdocto"            , jsDatas("edtDtDocto").retMMDDYYYY()             );
            clsFin.add("lancto"             , 0                                               );  //Se maior que zero eh rotina de alteracao            
            clsFin.add("observacao"         , document.getElementById("edtObservacao").value  );
            clsFin.add("vlrdesconto"        , 0                                               );
            clsFin.add("vlrevento"          , jsNmrs("edtVlrEvento").dolar().ret()            );
            clsFin.add("vlrmulta"           , 0                                               );
            clsFin.add("vlrretencao"        , 0                                               );
            clsFin.add("vlrpis"             , 0                                               );
            clsFin.add("vlrcofins"          , 0                                               );
            clsFin.add("vlrcsll"            , 0                                               );
            clsFin.add("temNfp"             , "N"                                             );
            clsFin.add("temNfs"             , "N"                                             );            
            ///////////////////////
            // Enviando para gravar
            ///////////////////////
            envPhp=clsFin.fim();  
            fd = new FormData();
            fd.append("transferencia",envPhp);
            msg     = requestPedido("Trac_CpCrTransferencia.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              throw retPhp[0].erro;
            } else {  
              gerarMensagemErro("cad",retPhp[0].erro,{cabec:"Aviso",foco:"edtDtDocto"});
              document.getElementById("edtVlrEvento").value = "0,00";
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
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Transferência entre contas</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmPgr" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 250px;">
          <div class="campotexto campo100">
            <h2>Selecione opção para debito</h2>
          </div>
          <!--
          -->
          <div class="campotexto campo15">
            <input class="campo_input_titulo" id="edtDocto" type="text" maxlength="12" disabled />
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
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtCodFll" 
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             type="text" 
                                             maxlength="4"/>
            <label class="campo_label campo_required" for="edtCodFll">FILIAL:</label>
          </div>
          <div class="campotexto campo50"></div>
          <!--
          Banco debito
          -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodBncDeb"
                                                onBlur="codBncDebBlur(this);" 
                                                onFocus="bncDebFocus(this);debcre='debito';" 
                                                onClick="bncDebF10Click(this);"
                                                data-oldvalue="0000"
                                                autocomplete="off"
                                                maxlength="4"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodBncDeb">BANCO-DEB:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesBncDeb" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBncDeb">BANCO_DEBITO_NOME:</label>
          </div>
          <!--
          Banco credito
          -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodBncCre"
                                                onBlur="codBncCreBlur(this);" 
                                                onFocus="bncCreFocus(this);debcre='credito';" 
                                                onClick="bncCreF10Click(this);"
                                                data-oldvalue="0000"
                                                autocomplete="off"
                                                maxlength="4"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodBncCre">BANCO-CRE:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesBncCre" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBncCre">BANCO_CREDITO_NOME:</label>
          </div>
          <!--
          -->
          <div class="campotexto campo100">
            <input class="campo_input input" id="edtObservacao" type="text" maxlength="120"/>
            <label class="campo_label campo_required" for="edtObservacao">OBSERVAÇÃO:</label>
          </div>
          <div class="inactive">
            <input id="edtCodFvrDeb" type="text" />
            <input id="edtCodFvrCre" type="text" />
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