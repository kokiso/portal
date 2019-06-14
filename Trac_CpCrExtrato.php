<?php
  session_start();
  if( isset($_POST["extrato"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");      
      require("classPhp/validaCampo.class.php"); 
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["extrato"]);
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
        if( $rotina=="filtrar" ){        
          $sql="";
          $sql.="SELECT A.EXT_CODIGO AS SEQ";
          $sql.="       ,CONVERT(VARCHAR(10),A.EXT_DATA,127) AS DATA";                    
          $sql.="       ,A.EXT_LANCTO AS LANCTO";          
          $sql.="       ,PGR.PGR_DOCTO AS DOCTO";
          $sql.="       ,PGR.PGR_CODPTP AS TP";
          $sql.="       ,FVR.FVR_APELIDO AS FAVORECIDO";          
          $sql.="      ,CONVERT(VARCHAR(10),PGR.PGR_DATAPAGA,127) AS BAIXA";                              
          $sql.="       ,A.EXT_DEBITO AS DEBITO";
          $sql.="       ,A.EXT_CREDITO AS CREDITO";          
          $sql.="       ,A.EXT_SALDO AS SALDO";          
          $sql.="  FROM EXTRATO A"; 
          $sql.="  LEFT OUTER JOIN PAGAR PGR ON A.EXT_LANCTO=PGR.PGR_LANCTO";          
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON PGR.PGR_CODFVR=FVR.FVR_CODIGO";
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
<script language="javascript" type="text/javascript"></script>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <title>Cadastro Titulo financeiro</title>
    <style id="meuCss">
    </style>  
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <link rel="stylesheet" href="css/Acordeon.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="js/jsCopiaDoc2017.js"></script>
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        pega=JSON.parse(localStorage.getItem("addAlt")).lote[0];
        //localStorage.removeItem("addAlt");
        document.getElementById("edtCodBnc").value    = pega.codbnc;
        document.getElementById("edtDesBnc").value    = pega.desbnc;
        document.getElementById("edtData").value  = jsDatas(-1).retDDMMYYYY();
        document.getElementById("edtData").foco();

        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsExt={
          "titulo":[
             {"id":0  ,"labelCol"       : "SEQ"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":1  ,"labelCol"       : "DATA"
                      ,"fieldType"      : "str"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "LANCTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "DOCTO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
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
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "BAIXA"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "DEBITO"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "9em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "CREDITO"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "9em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "SALDO"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "9em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"        :"CD"         
                      ,"obj"           : "imgPP"
                      ,"tamGrd"       : "5em"
                      ,"tipo"         : "img"
                      ,"fieldType"    : "img"
                      ,"func"         : "copiaDocumento(this.parentNode.parentNode.cells[2].innerHTML);"
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
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmExt"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaExt"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmExt"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divGrade"                // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblExt"                  // Nome da table
          ,"prefixo"        : "ext"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "EXTRATO"                 // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "103em"                   // Tamanho da table
          ,"height"         : "56em"                    // Altura da table
          ,"nChecks"        : true                      // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "EXTRATO"                 // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"foco"           : ["edtData"
                              ,"edtData"
                              ,"btnConfirmar"]          // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "*"                       // Enderço da pagina PASSO A PASSO
          //,"indiceTable"    : "SEQ"                     // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objExt === undefined ){  
          objExt=new clsTable2017("objExt");
        };  
        objExt.montarHtmlCE2017(jsExt); 
      });
      //
      var objExt;                     // Obrigatório para instanciar o JS Extrato
      var jsExt;                      // Obj principal da classe clsTable2017
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var pega;                       // Recuperar localStorage;      
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d06);      
      //
      //
      ////////////////////////////////
      // Buscando as operacoes padroes
      ////////////////////////////////
      function fncFiltrar(){  
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "filtrar"                         );
        clsJs.add("login"   , jsPub[0].usr_login                );
        clsJs.add("data"    , jsDatas("edtData").retMMDDYYYY()  );
        fd = new FormData();
        fd.append("extrato" , clsJs.fim());
        msg     = requestPedido("Trac_CpCrExtrato.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          jsExt.registros=objExt.addIdUnico(retPhp[0]["dados"]);
          objExt.montarBody2017();
        }
      };
      function fncImprimir(){
        //////////////////////////////////////////////
        // INICIA A IMPRESSAO DA COPIA DE DOCUMENTO //
        //////////////////////////////////////////////
        let rel = new relatorio();
        rel.tamFonte(9);
        rel.iniciar();
        rel.traco();
        rel.pulaLinha(1);
        rel.corFundo("cinzaclaro",9,190);    
        rel.cell(28,"Extrato bancario:"   ,{borda:0,negrito:true});
        rel.cell(110,document.getElementById("edtDesBnc").value  ,{negrito:false});
        rel.cell(18,"A partir de:",{negrito:true});
        rel.cell(20,document.getElementById("edtData").value,{negrito:false});
        rel.pulaLinha(10);
        rel.traco();
        rel.pulaLinha(1);
        rel.tamFonte(7);
        rel.cell(12,"SEQ"   ,{borda:0,negrito:true,align:"L"});
        rel.cell(18,"DATA");
        rel.cell(15,"LANCTO");
        rel.cell(25,"DOCTO");
        rel.cell(10,"TP");
        rel.cell(25,"FAVORECIDO");
        rel.cell(20,"BAIXA");
        rel.cell(20,"DEBITO",{align:"R"});
        rel.cell(20,"CREDITO");
        rel.cell(20,"SALDO");


        clsChecados = objExt.gerarJson("n");
        clsChecados.temColChk(false);
        msg = clsChecados.gerar();
        tamC=msg.length;
        let zebra=false;
        rel.align("L");
        for( let lin=0;lin<tamC;lin++ ){
          zebra=(lin % 2 ? true : false );  
          if( zebra ){
            rel.pulaLinha(6);
            rel.setX(10);
            rel.corFundo("cinzaclaro",3,190);    
          } else {
            rel.pulaLinha(6);
            rel.setX(10);
            rel.corFundo("branco",3,190);    
          }
          rel.pulaLinha(-6);          
          
          
          rel.cell(12,msg[lin].SEQ,{emZero:6,moeda:false,align:"L",negrito:false,pulaLinha:3});
          rel.cell(18,msg[lin].DATA);
          rel.cell(15,msg[lin].LANCTO,{emZero:6});
          rel.cell(25,msg[lin].DOCTO);
          rel.cell(10,msg[lin].TP);
          rel.cell(25,msg[lin].FAVORECIDO);
          rel.cell(20,msg[lin].BAIXA);
          rel.cell(20,msg[lin].DEBITO,{moeda:true,align:"R"});
          rel.cell(20,msg[lin].CREDITO);
          rel.cell(20,msg[lin].SALDO);
        }
        envPhp=rel.fim();
        //console.log(envPhp);
        ///////////////////////////////////////////////////
        // PREPARANDO UM FORMULARIO PARA VER O RELATORIO //
        ///////////////////////////////////////////////////
        document.getElementById('sql').value=envPhp;
        document.getElementsByTagName('form')[0].submit();           
      };
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Extrato bancario</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>
      <form method="post" class="center" id="frmExt" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 630px;padding-top:5px;">
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtCodBnc" type="text" disabled />
            <label class="campo_label campo_required" for="edtCodBnc">BANCO:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesBnc" type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBnc">BANCO_NOME:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtData" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtData">A PARTIR DE:</label>
          </div>
          <div onClick="fncImprimir();" class="btnImagemEsq bie10 bieAzul bieRight"><i class="fa fa-print"> Imprimir</i></div>
          <div onClick="objExt.excel();" class="btnImagemEsq bie10 bieAzul bieRight"><i class="fa fa-file-excel-o"> Excel</i></div>
          <div onClick="fncFiltrar();" class="btnImagemEsq bie10 bieAzul bieRight"><i class="fa fa-check"> Filtrar</i></div>                    
          <div class="campotexto campo100" style="margin-left:-20px;">
            <div id="divGrade" ></div>    
          </div>  
        </div> 
      </form>
    </div>
  </body>
</html>