<?php
  session_start();
  if( isset($_POST["cpcrdefinitivo"]) ){
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
      $retCls   = $vldr->validarJs($_POST["cpcrdefinitivo"]);
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
        // Em definitivo
        /////////////////////////
        if( $lote[0]->rotina=="definitivo" ){
          foreach ( $lote as $reg ){
            $sql ="UPDATE VPAGAR"; 
            $sql.="   SET PGR_VENCTO='".$reg->vencto."'";
            $sql.="       ,PGR_CODBNC='".$reg->codbnc."'";
            $sql.="       ,PGR_CODCMP='".$reg->codcmp."'";
            $sql.="       ,PGR_CODUSR=".$_SESSION["usr_codigo"];
            $sql.=" WHERE PGR_LANCTO=".$reg->lancto;
            array_push($arrUpdt,$sql);  
            /////////////////////////////////////////////////////////////////////////////////
            // Como os dados ja estaum no banco de dados apenas recupero para inserir o saldo
            /////////////////////////////////////////////////////////////////////////////////
            $sql ="SELECT PGR_BLOQUEADO";
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
            $sql.=" FROM PAGAR WITH(NOLOCK)"; 
            $sql.=" LEFT OUTER JOIN PAGARRATEIO PR ON PGR_LANCTO=PR.PR_LANCTO"; 
            $sql.=" WHERE PGR_LANCTO=".$reg->lancto;
            $classe->msgSelect(false);
            $retCls=$classe->selectAssoc($sql);
            $tbl=$retCls["dados"][0];
            ////////////////////
            // Gravando na PAGAR
            ////////////////////
            $codptp=
              (($tbl["PGR_CODPTP"] == "PP") ? "CP" : 
              (($tbl["PGR_CODPTP"] == "MP") ? "CP" : 
              (($tbl["PGR_CODPTP"] == "PR") ? "CR" :       
              (($tbl["PGR_CODPTP"] == "MR") ? "CP" :  "*" ))) );
            
            $lancto=$classe->generator("PAGAR"); 
            $sql ="INSERT INTO VPAGAR(";
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
            $sql.=","  .$reg->codbnc;                   // CODBNC
            $sql.=","  .$tbl["PGR_CODFVR"];             // CODFVR
            $sql.=",'" .$tbl["PGR_CODFC"]."'";          // CODFC
            $sql.=",'" .$tbl["PGR_CODTD"]."'";          // CODTD
            $sql.=",'" .$reg->vencto."'";               // VENCTO
            $sql.=",'" .$reg->docto."'";                // DOCTO
            $sql.=",'" .date('Y-m-d')."'";              // DTDOCTO
            $sql.=",'P'";                               // CODPTT
            $sql.=","  .$lancto;                        // MASTER
            $sql.=",'" .$tbl["PGR_OBSERVACAO"]."'";     // OBSERVACAO
            $sql.=",'" .$codptp."'";                    // CODPTP
            $sql.=","  .$tbl["PGR_CODPT"];              // CODPT
            $sql.=",'" .abs($reg->valor)."'";           // VLREVENTO
            $sql.=",'" .abs($reg->valor)."'";           // VLRPARCELA
            $sql.=",0";                                 // VLRDESCONTO
            $sql.=",'" .$tbl["PGR_CODCC"]."'";          // CODCC
            $sql.=","  .$tbl["PGR_CODSNF"];             // CODSNF
            $sql.=",'" .$tbl["PGR_APR"]."'";            // APR
            $sql.=","  .$tbl["PGR_CODEMP"];             // CODEMP
            $sql.=","  .$tbl["PGR_CODFLL"];             // CODFLL
            $sql.=","  .$tbl["PGR_VERDIREITO"];         // VERDIREITO
            $sql.=","  .$reg->codcmp;                   // CODCMP
            $sql.=",'" .$tbl["PGR_REG"]."'";            // REG
            $sql.=","  .$_SESSION["usr_codigo"];        // CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);  
            //
            /////////////////////
            // Gravando na RATEIO
            /////////////////////
            $sql ="INSERT INTO VRATEIO(";
            $sql.="RAT_LANCTO";                
            $sql.=",RAT_CODCC";
            $sql.=",RAT_DEBITO";
            $sql.=",RAT_CREDITO";
            $sql.=",RAT_CODEMP";
            $sql.=",RAT_CODCMP";
            $sql.=",RAT_CONTABIL";
            $sql.=",RAT_CODUSR) VALUES(";
            $sql.="'$lancto'";                                                // RAT_LANCTO
            $sql.=",'" .$tbl["PR_CODCC"]."'";                                 // RAT_CODCC
            $sql.="," .( $tbl["PR_DEBCRE"]=="D" ? abs($reg->valor) : 0 );     // RAT_DEBITO
            $sql.="," .( $tbl["PR_DEBCRE"]=="C" ? abs($reg->valor) : 0 );     // RAT_CREDITO
            $sql.="," .$tbl["PGR_CODEMP"];                                    // RAT_CODEMP
            $sql.="," .$reg->codcmp;                                          // RAT_CODCMP
            $sql.=",'S'";                                                     // RAT_CONTABIL
            $sql.=","  .$_SESSION["usr_codigo"];                              // RAT_CODUSR
            $sql.=")";
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
    <title>Em definitivo</title>
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
        //localStorage.removeItem("addAlt");
        $doc("edtCodBnc").value      = jsConverte(pega[0].codbnc).emZero(4);      
        $doc("edtDesBnc").value      = pega[0].desbnc;        
        $doc("edtDocto").value       = pega[0].docto;
        $doc("edtVencto").value      = pega[0].vencto;
        $doc("edtCodBnc").foco();
        
        var jsTab={
          "pai"         : "appAba"  //Onde vai ser appendado o html gerado
          ,"idStyle"    : "meuCss"  //id da tag style
          ,"opcExcluir" : "N"       //Se vai existir a opção excluir        
          ,"abas":[ 
             {"id":0  ,"nomeAba"      : "parcela" 
                      ,"labelAba"     : "Titulo"
                      ,"table"        : "tblTtl"
                      ,"widthTable"   : "85em" 
                      ,"heightTable"  : "38em"                     
                      ,"widthAba"     : "12em" 
                      ,"rolagemVert"  : true 
                      ,"somarCols":[7]
                      ,"head":[  
                                { "labelCol"   : "OPC"     
                                  ,"width"      : "5em"
                                  ,"fieldType"  : "chk"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "LANCTO"     
                                  ,"width"      : "5em"  
                                  ,"fieldType"  : "int"
                                  ,"formato"    : "i3"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "FAVORECIDO"   
                                  ,"width"      : "13em"  
                                  ,"fieldType"  : "str"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "DOCTO"   
                                  ,"width"      : "10em"  
                                  ,"fieldType"  : "str"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "VENCTO"   
                                  ,"width"      : "6em"  
                                  ,"fieldType"  : "dat"
                                  ,"editar"     : "N"
                                  ,"classe"     : "edtData"}
                                ,{ "labelCol"   : "CODBNC"   
                                  ,"width"      : "0em"  
                                  ,"fieldType"  : "str"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
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
              ,reg.docto
              ,reg.vencto
              ,reg.codbnc              
              ,reg.desbnc
              ,jsConverte(reg.valor).abs() //reg.valor
            ]
          );
//debugger;          
          //reg.valor=jsConverte(reg.valor).abs();  
          //vlrTotal+=jsConverte(reg.valor).dolar(true);//          jsNmrs(reg.valor).abs().dolar().ret(); 
        });    
        //$doc("edtVlrEvento").value = jsConverte(vlrTotal.toString()).real()  ;//jsNmrs(vlrTotal).abs().real().ret();         
        $doc("edtVlrEvento").value="0,00";
        //
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
      var pega;                       // Recuperar localStorage;
      var minhaAba;
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      //////////////////////////////////////////////////////////////
      //     MANTENDO UM PADRAO PARA COLUNA DA GRADE DE TITULOS   //
      //////////////////////////////////////////////////////////////
      var objGrade={ colOpc : 0
                     ,colLancto  : 1
                     ,colDesFvr  : 2
                     ,colDocto   : 3
                     ,colVencto  : 4
                     ,colCodBnc  : 5
                     ,colDesBnc  : 6
                     ,colValor   : 7
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
                      ,foco:"edtDocto"
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
                                  ,foco:"edtDocto"
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
          clsJs = jsString("lote");
          let el  = document.getElementById("tblTtl").getElementsByTagName("tbody")[0]; 
          let nl  = el.rows.length;
          for(let lin = 0; lin < nl; lin++){
            clsJs.add("rotina"    , "definitivo"                                                                );
            clsJs.add("login"     , jsPub[0].usr_login                                                          );
            clsJs.add("lancto"    , el.rows[lin].cells[objGrade.colLancto].innerHTML                            );
            clsJs.add("codbnc"    , el.rows[lin].cells[objGrade.colCodBnc].innerHTML                            );
            clsJs.add("docto"     , el.rows[lin].cells[objGrade.colDocto].innerHTML                             );
            clsJs.add("vencto"    , jsDatas(el.rows[lin].cells[objGrade.colVencto].innerHTML).retMMDDYYYY()     );
            clsJs.add("valor"     , jsNmrs(el.rows[lin].cells[objGrade.colValor].innerHTML).abs().dolar().ret() );            
            clsJs.add("codcmp"    , jsDatas(0).retYYYYMM()                                                      );
          };
          ///////////////////////
          // Enviando para gravar
          ///////////////////////
          envPhp=clsJs.fim();
          fd = new FormData();
          fd.append("cpcrdefinitivo" , envPhp);
          msg=requestPedido("Trac_CpCrDefinitivo.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cad",retPhp[0].erro,{cabec:"Aviso"});    
          } else {  
            window.close();
          };  
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
      
      function fncGrade(){
        let el  = document.getElementById("tblTtl").getElementsByTagName("tbody")[0]; 
        let nl  = el.rows.length;
        
        for(let lin = 0; lin < nl; lin++){
          if( el.rows[lin].cells[0].children[0].checked==true ){ 
            el.rows[lin].cells[objGrade.colDocto].innerHTML  = jsConverte("#edtDocto").upper();
            el.rows[lin].cells[objGrade.colVencto].innerHTML = $doc("edtVencto").value;
            el.rows[lin].cells[objGrade.colCodBnc].innerHTML = $doc("edtCodBnc").value;
            el.rows[lin].cells[objGrade.colDesBnc].innerHTML = $doc("edtDesBnc").value;
            if( jsConverte("#edtVlrEvento").dolar(true)>0 )
              el.rows[lin].cells[objGrade.colValor].innerHTML  = $doc("edtVlrEvento").value;
          };  
        };
      };
      
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 id="h2Rotina" style="text-align:center;">Previsão em definitivo</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmPgr" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 550px;">
          <div class="campotexto campo100">
            <h2>Informe - Dados para alteração indivual na grade( OPC selecionado )</h2>
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
            <input class="campo_input input" id="edtVencto" 
                                             placeholder="##/##/####" 
                                             data-oldvalue=""                                             
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtVencto">VENCTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtDocto" 
                                             data-oldvalue=""
                                             type="text" 
                                             maxlength="12"/>
            <label class="campo_label campo_required" for="edtDocto">DOCUMENTO:</label>
          </div>
          
          <div class="campotexto campo15">
            <input class="campo_input edtDireita" id="edtVlrEvento" 
                                                  onBlur="fncCasaDecimal(this,2);"            
                                                  maxlength="15" 
                                                  type="text" />
            <label class="campo_label campo_required" for="edtVlrEvento">VALOR:</label>
          </div>
          <div onClick="fncGravar();" class="btnImagemEsq bie12 bieAzul bieRight"><i id="imgOk" class="fa fa-check"> Gravar</i></div>          
          <div onClick="fncGrade();" class="btnImagemEsq bie15 bieAzul bieRight"><i id="imgGrade" class="fa fa-save"> Grade</i></div>                    
          <div onClick="window.close();" class="btnImagemEsq bie12 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>          
          
          <div class="campotexto campo100">
            <div id="appAba" class="campotexto campo85" style="height:20em;float:left;position:relative;">
            </div>            
          </div>
        </div> 
      </form>
    </div>
  </body>
</html>