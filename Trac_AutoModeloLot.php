<?php
  session_start();
  if( isset($_POST["automodelolot"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["automodelolot"]);
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

        if( $lote[0]->rotina=="exclote" ){
          $contador = 0;
          $entrada  = 0;
          $erro ="ok";
          $sql  ="";
          $sql.="SELECT AMP_CODIGO,AMP_CODPE FROM AUTOMODELOPRODUTO";          
          $sql.=" WHERE ((AMP_CODIGO BETWEEN ".$lote[0]->codini." AND ".$lote[0]->codfim.") AND (AMP_CODAML=".$lote[0]->lote."))";
          $classe->msgSelect(true);
          $retCls = $classe->selectAssoc($sql);
          
          if( $retCls["qtos"]==0 ){                  
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO PARA EXCLUSÃO"}]';            
          } else {
            $tbl    = $retCls["dados"];
            ///////////////////////////////////////////////////////////////////////////////
            // Para excluir um lote o produto deve estar no ponto de estoque "EST[Estoque]"
            // A variavel $contador tem que ser o campo AML_ESTOQUE( A qtdade que entrou )
            ///////////////////////////////////////////////////////////////////////////////
            foreach ( $tbl as $reg ){
              if( $reg["AMP_CODPE"]=="EST" ){
                $sql  ="DELETE FROM VAUTOMODELOPRODUTO WHERE AMP_CODIGO=".$reg["AMP_CODIGO"];  
                array_push($arrUpdt,$sql);            
                $contador++;
                $entrada=$lote[0]->entrada;
              } else {
                $erro=" PRODUTO ".$reg["AMP_CODIGO"]." DEVE ESTAR NO PONTO DE ESTOQUE [EST]"; 
              };
            };  
            if( $erro=="ok" ){
              if( $contador != $entrada ){
                $erro="TOTAL DE ITENS A SEREM EXCLUIDOS ".$entrada." DIVERGE DO TOTAL SELECIONADO ".$contador;
              };
            };
            ////////////////////
            // Fim das checagens
            ////////////////////
            if( $erro=="ok" ){          
              $sql="DELETE FROM VAUTOMODELOLOTE WHERE AML_CODIGO=".$lote[0]->lote;
              array_push($arrUpdt,$sql);            
              $atuBd = true;
            };
          };
        };
        //
        //
        if( $lote[0]->rotina=="filtrar" ){
          $sql="";
          $sql.="SELECT A.GML_CODIGO";
          $sql.="      ,GM.GM_NOME";
          $sql.="      ,A.GML_ENTRADA";
          $sql.="      ,CONVERT(VARCHAR(10),A.GML_DATA,127) AS GML_DATA";                    
          $sql.="      ,US.US_APELIDO";  
          $sql.="      ,A.GML_CODGMPINI";
          $sql.="      ,A.GML_CODGMPFIM";          
          $sql.="      ,A.GML_CODGM";                    
          $sql.="  FROM GRUPOMODELOLOTE A";          
          $sql.="  LEFT OUTER JOIN GRUPOMODELO GM ON A.GML_CODGM=GM.GM_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON US.US_CODIGO = A.GML_CODUSR";          
          $sql.=" WHERE ((GML_DATA>='".$lote[0]->data."') AND (GM.GM_CODGP='AUT'))";
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR"
                        ,"contador":"'.$contador.'"
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
                          ,"contador":"'.$contador.'"
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
<script language="javascript" type="text/javascript"></script>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <title>Entrada em estoque</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/clsTab2017.js"></script>        
    <script src="js/jsTable2017.js"></script>
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        document.getElementById("edtDataIni").value  = jsDatas(-30).retDDMMYYYY();      
        ////////////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_GrupoModelo.php
        ////////////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addLote")).lote[0];
        localStorage.removeItem("addLote");
        jsAml={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "LOTE"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "MODELO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "100"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "ENTRADA"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "DATA"
                      ,"fieldType"      : "dat"
                      ,"align"          : "center"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "18"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "INI"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "FIM"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "CODAM"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
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
             {"texto":"Excel"         ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }  
            ,{"texto":"Imprimir"      ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"        }                    
            ,{"texto":"Fechar"        ,"name":"horFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"       }
          ] 

          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmAml"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaAml"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmAml"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnAml"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblAml"                  // Nome da table
          ,"prefixo"        : "Aml"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "absolute"
          ,"width"          : "80em"                    // Tamanho da table
          ,"height"         : "65em"                    // Altura da table
          ,"nChecks"        : false                     // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "350px"                   // Se tiver menu esquerdo
          ,"relTitulo"      : "LOTES IMPORTADOS"        // Titulo do relatório
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"formPassoPasso" : "*"                       // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "GRUPO"                   // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
        }; 
        if( objAml === undefined ){  
          objAml=new clsTable2017("objAml");
        };  
        objAml.montarHtmlCE2017(jsAml);
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsAml,["ENTRADA","LOTE","INI","FIM"]);
      });
      //
      var objAml;                     // Obrigatório para instanciar o JS Semestral
      var jsAml;                      // Obj principal da classe clsTable2017
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
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
      //
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "filtrar"                           );
        clsJs.add("login"   , jsPub[0].usr_login                  );
        clsJs.add("data"    , jsDatas("edtDataIni").retMMDDYYYY() );
        fd = new FormData();
        fd.append("automodelolot" , clsJs.fim());
        msg     = requestPedido("Trac_AutoModeloLot.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsAml.registros=objAml.addIdUnico(retPhp[0]["dados"]);
          objAml.ordenaJSon(jsAml.indiceTable,false);  
          objAml.montarBody2017();
        };  
      };
      //////////////////////
      // Exclusao de um lote
      //////////////////////
      function horExcLotClick(){
        try{
          chkds=objAmi.gerarJson("1").gerar();
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("rotina"  , "exclote"           );              
          clsJs.add("login"   , jsPub[0].usr_login  );              
          clsJs.add("lote"    , chkds[0].LOTE       );
          clsJs.add("entrada" , chkds[0].ENTRADA    );            
          clsJs.add("codini"  , chkds[0].INI        );            
          clsJs.add("codfim"  , chkds[0].FIM        );   

          var fd = new FormData();
          fd.append("automodelolot" , clsJs.fim());
          msg=requestPedido("Trac_AutoModeloLot.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno!="OK" ){
            gerarMensagemErro("aml",retPhp[0].erro,{cabec:"Aviso"});                  
          } else {  
            ///////////////////////////////////
            // Atualizando a grade desta rotina
            ///////////////////////////////////
            tblAml.apagaChecados();
            /////////////////////////////////////////////////
            // Atualizando a grade em Trac_GrupoModelo.php
            /////////////////////////////////////////////////
            let el  = window.opener.document.getElementById("tblAm");
            let tbl = el.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
                
            for(let lin=0 ; (lin<nl) ; lin++){
              if( jsNmrs(chkds[0].CODAM).inteiro().ret() == jsNmrs(tbl.rows[lin].cells[parseInt(pega.colCodigo)].innerHTML).inteiro().ret() ){
                let estoque=( jsNmrs(tbl.rows[lin].cells[parseInt(pega.colEstoque)].innerHTML).inteiro().ret() - jsNmrs(retPhp[0].contador).inteiro().ret() );
                tbl.rows[lin].cells[parseInt(pega.colEstoque)].innerHTML = jsNmrs(estoque).emZero(4).ret(); 
                break;  
              };
            }; 
            window.close();              
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
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
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:30%;float:left;"><h2 style="text-align:center;">Lotes importados</h2></div>
        <div class="teEsquerda"></div>
        <div class="campotexto campo10" style="margin-top:2px;">
          <input class="campo_input" id="edtDataIni" 
                                     placeholder="##/##/####"                 
                                     OnKeyUp="mascaraNumero('##/##/####',this,event,'dig')"
                                     maxlength="10" type="text" />
          <label class="campo_label" for="edtDataIni">A PARTIR</label>
        </div>
        <div onClick="btnFiltrarClick();"    class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-folder-open"> Abrir</i></div>                
      </div>
      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <section>
        <section id="sctnAml" style="margin-left:100px;">
        </section>  
      </section>
      <form method="post" class="center" id="frmAml" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
      </form>
    </div>
  </body>
</html>