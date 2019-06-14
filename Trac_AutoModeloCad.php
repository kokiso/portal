<?php
  session_start();
  if( isset($_POST["automodelocad"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      //require("classPhp/selectRepetido.class.php");      
      //require("classPhp/validaCampo.class.php"); 
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
      $retCls   = $vldr->validarJs($_POST["automodelocad"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $arrUpdt  = []; 
        $contador = 0;
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $rotina   = $lote[0]->rotina;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        ///////////////////////////////////////////////
        //     Entrada em estoque AUTOMODELOPRODUTO  //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="cadamp" ){
          ////////////////////////////////////////////////////////////////////////////////////////////
          // Antes de cadastrar cada produto tem que estar em estoque e naum pode estar em nenhum auto
          ////////////////////////////////////////////////////////////////////////////////////////////
          $continua = true;
          $erro     = "ok";   // Retornando o erro para a aplicacao se existir
          foreach ( $lote as $reg ){          
            $sql="SELECT GMP_CODIGO,GMP_CODPE,GMP_CODPEI,GMP_CODAUT FROM GRUPOMODELOPRODUTO WHERE GMP_CODIGO=".$reg->codgmp;
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( $retCls["qtos"]==0 ){        
              $erro="Não localizado produto ".$reg->codgmp." para este lançamento!";  
              $continua=false;
              break;
            } else {
              if( $retCls["dados"][0]["GMP_CODPE"] != "EST" ){
                $erro="Produto ".$reg->codgmp." deve estar no estoque interno, flag atual ".$retCls["dados"][0]["GMP_CODPE"]."!";  
                $continua=false;
                break;
              };
              if( $retCls["dados"][0]["GMP_CODPEI"] <> 1 ){
                $erro="Produto ".$reg->codgmp." ja esta alocado em um cliente/colaborador!";  
                $continua=false;
                break;
              };
              if( $retCls["dados"][0]["GMP_CODAUT"] <> 0 ){
                $erro="Produto ".$reg->codgmp." ja esta alocado no auto ".$retCls["dados"][0]["GMP_CODAUT"]."!";  
                $continua=false;
                break;
              };
            };  
          };
          //
          //
          //////////////////////////////////////////////
          // Pegar o primeiro e ultimo produto para lote
          //////////////////////////////////////////////
          if( $continua==false ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';              
          } else {  
            $ampIni   = 0;
            $ampFim   = 0;
            
            $codaml=$classe->generator("GRUPOMODELOLOTE");
            foreach ( $lote as $reg ){
              if( $reg->novoitem=="S" ){
                ////////////////////////////////////
                // Buscando o codigo do novo produto
                ////////////////////////////////////  
                $codamp=$classe->generator("GRUPOMODELOPRODUTO");  
                /////////////////////////////////////////////////////
                // Aqui preciso pegar o numero de serie do rastreador
                // $ns = numero_serie
                /////////////////////////////////////////////////////
                $nsItem         = $reg->item;
                $nsSerie        = "NSA";
                $qtosComposicao = 0;  // Qtdade de produtos que compoem o auto
                foreach( $lote as $ns ){
                  if( $ns->item==$nsItem ){
                    if( $ns->codgp=="RST" ){
                      $nsSerie = $ns->serie;
                    };      
                    $qtosComposicao++;
                  };  
                };
                //
                //
                $sql="";
                $sql.="INSERT INTO VGRUPOMODELOPRODUTO("; 
                $sql.="GMP_CODIGO";
                $sql.=",GMP_CODCNTT";                
                $sql.=",GMP_CODGM";
                $sql.=",GMP_CODGP";
                $sql.=",GMP_CODPE";
                $sql.=",GMP_CODPEI";
                $sql.=",GMP_CODFBR";                
                $sql.=",GMP_NUMSERIE";
                $sql.=",GMP_SINCARD";
                $sql.=",GMP_OPERADORA";
                $sql.=",GMP_FONE";
                $sql.=",GMP_CONTRATO";
                $sql.=",GMP_CODGML";                
                $sql.=",GMP_PLACACHASSI";
                $sql.=",GMP_COMPOSICAO";
                $sql.=",GMP_CODUSR) VALUES(";
                $sql.="'$codamp'";                    // GMP_CODIGO
                $sql.=",0";                           // GMP_CODCNTT
                $sql.=","  .$reg->codam;              // GMP_CODAM
                $sql.=",'AUT'";                       // GMP_CODGP
                $sql.=",'EST'";                       // GMP_CODPE
                $sql.=",0";                           // GMP_CODPEI
                $sql.=",0";                           // GMP_CODFBR                
                $sql.=",'" .$nsSerie."'";             // GMP_NUMSERIE - Serie do rastreador
                $sql.=",'NSA'";                       // GMP_SINCARD
                $sql.=",'NSA'";                       // GMP_OPERADORA
                $sql.=",'NSA'";                       // GMP_FONE
                $sql.=",'NSA'";                       // GMP_CONTRATO
                $sql.=","  .$codaml;                  // GMP_CODAML                
                $sql.=",'NSA0000'";                   // GMP_PLACACHASSI
                $sql.=","  .$qtosComposicao;          // GMP_COMPOSICAO              
                $sql.=","  .$_SESSION["usr_codigo"];  // GMP_CODUSR                      
                $sql.=")";
                array_push($arrUpdt,$sql);            
                //////////////////////////////////////////////////////////////////////////
                // Guardando o codigo inicial/final para montar um lote(AUTOMODELOLOTE)
                //////////////////////////////////////////////////////////////////////////
                if( $contador==0 ){
                  $ampIni = $codamp;
                  $ampFim = $codamp;        // Se tiver soh um item no lote
                  $codam  = $reg->codam;
                } else {
                  $ampFim=$codamp;                
                };
                $contador++;              
              };
              //////////////////////////////////////////
              // Trigger atualiza estoque em GRUPOMODELO
              //////////////////////////////////////////
              $sql="";
              $sql.="INSERT INTO VAUTOCOMPOSICAO(";
              $sql.="AC_CODAMP";   
              $sql.=",AC_CODGMP";            
              $sql.=",AC_STATUS";            
              $sql.=",AC_CODUSR) VALUES(";
              $sql.="'$codamp'";                    // AC_CODAMP
              $sql.=","  .$reg->codgmp;             // AC_CODGMP
              $sql.=",'ATV'";                       // AC_STATUS
              $sql.=","  .$_SESSION["usr_codigo"];  // AC_CODUSR                      
              $sql.=")";
              array_push($arrUpdt,$sql);            
            };
            $sql="";
            $sql.="INSERT INTO VGRUPOMODELOLOTE("; 
            $sql.="    GML_CODIGO";
            $sql.="   ,GML_CODGM";                      
            $sql.="   ,GML_DATA";            
            $sql.="   ,GML_ENTRADA";
            $sql.="   ,GML_CODGMPINI";
            $sql.="   ,GML_CODGMPFIM";
            $sql.="   ,GML_CODUSR) VALUES(";
            $sql.="'$codaml'";                    // GML_CODIGO
            $sql.=","  .$codam;                   // GML_CODGM
            $sql.=",'".date('m/d/Y')."'";         // GML_DATA
            $sql.=","  .$contador;                // GML_ENTRADA
            $sql.=","  .$ampIni;                  // GML_CODAMPINI
            $sql.=","  .$ampFim;                  // GML_CODAMPFIM
            $sql.=","  .$_SESSION["usr_codigo"];  // GML_CODUSR          
            $sql.=")";
            array_push($arrUpdt,$sql);            
            $atuBd = true;
          };
        };  
      };
      ///////////////////////////////////////////////////////////////////
      // Atualizando o banco de dados se opcao de insert/updade/delete //
      ///////////////////////////////////////////////////////////////////
//file_put_contents("aaa.xml",print_r($arrUpdt,true));      
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
    <!--<script src="js/clsTab2017.js"></script>-->
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaGrupoModeloProdutoF10.js"></script>    
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        document.getElementById("edtUnidades").foco();
        /////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_CpCr.php
        /////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addAlt")).lote[0];
        console.log(pega);
        //localStorage.removeItem("addAlt");      voltarrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr
        document.getElementById("edtCodAm").value   = pega.codam;        
        document.getElementById("edtDesAm").value   = pega.desam;
        document.getElementById("edtUnidades").value   = "001";
        jsAm={
          "titulo":[
             {"id":0  ,"labelCol"       : "LINHA"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"align"          : "center"                                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":1  ,"labelCol"       : "ITEM"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                                            
                      ,"tamGrd"         : "0em"
                      //,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
                      
            ,{"id":2  ,"labelCol"       : "GRUPO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "STATUS"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "COD"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                                            
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "PRODUTO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "SERIE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "SINCARD"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "OPC"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "OBR"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "UNIDADE"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                                            
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
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
          , 
          "botoesH":[
             {"texto":"Cadastrar"   ,"name":"horCadastrar"  ,"onClick":"7"  ,"enabled":true   ,"imagem":"fa fa-plus"          }          
            ,{"texto":"Imprimir"    ,"name":"mopImprimir"   ,"onClick":"3"  ,"enabled":true   ,"imagem":"fa fa-print"         }                                
            ,{"texto":"Excel"       ,"name":"mopExcel"      ,"onClick":"5"  ,"enabled":true   ,"imagem":"fa fa-file-excel-o"  }        
            ,{"texto":"Fechar"      ,"name":"horFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"           }            
          ] 

          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"            // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"             // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmAm"                   // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaAm"                // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmAm"                   // Onde vai ser gerado o fieldSet       
          ,"divModalDentro" : "sctnAm"                  // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblAm"                   // Nome da table
          ,"prefixo"        : "Am"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"nChecks"        : false          
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "94em"                    // Tamanho da table
          ,"height"         : "45em"                    // Altura da table
          ,"tableLeft"      : "2px"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "MOVIMENTO->EVENTO"       // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objAm === undefined ){  
          objAm=new clsTable2017("objAm");
        };
        objAm.montarHtmlCE2017(jsAm); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsAm,["COD","GRUPO","ITEM","LINHA","OBR","PRODUTO","SERIE","SINCARD","UNIDADE"]);
      });
      //
      var objAm;                      // Obrigatório para instanciar o JS MOVTOOPERADORPAI
      var jsAm;                       // Obj principal da classe clsTable2017
      var objGmpF10                   // Obrigatório para instanciar JS GrupoModeloProduto
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var objPadF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var pega;                       // Recuperar localStorage;
      var objCol;                     // Olhando as colunas da table que vou precisar para atualizar a mesma(table)  
      var linTable;                   // Linha da table que vai ser atualizada apos selecao do F10
      var pubTbl;                     // element table para nao ter que instanciar nas chamadas
      var pubRows;                    // numero de linha na table para nao ter que instanciar nas chamadas
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario                  
      var minhaAba;
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d36);
      function fncGerar(){
        try{                  
          let qtos=jsNmrs("edtUnidades").inteiro().ret();
          if( qtos<=0 )
            throw "Favor informar Unidade(s) maior que zero!";   
          
          let spltObrigatorio=[];
          let spltAceito=[];
          /////////////////////////////////
          // Pegando os grupos obrigatorios
          /////////////////////////////////  
          if( pega.gpobrigatorio != "NSA" )
            spltObrigatorio=(pega.gpobrigatorio).split("_");
          /////////////////////////////////
          // Pegando os grupos aceitos
          /////////////////////////////////  
          if( pega.gpaceito != "NSA" )
            spltAceito=(pega.gpaceito).split("_");
          ///////////////////////////////////////////////////
          // tamSpan é para fazer um rowspan para cada item
          //////////////////////////////////////////////////  
          let tamSpan=spltObrigatorio.length+spltAceito.length;            
          //
          //  
          ////////////////////////////////////
          // Montando o array que vai na table
          ////////////////////////////////////  
          let arr=[];
          let indice=1;
          for( let lin=1;lin<=qtos;lin++ ){
            let snSpan="*";              
            spltObrigatorio.forEach( function(reg){        
              snSpan=(snSpan=="*" ? "S" : "N");
              arr.push({item:lin
                        ,codgp:reg     
                        ,pk:indice 
                        ,fa:"fa fa-circle" ,cor:"red"
                        ,obrigatorio:"S"
                        ,modelo:(pega.gmobrigatorio).replaceAll("_",",")
                        ,span:snSpan
              });
              indice++;
            });
            
            spltAceito.forEach( function(reg){ 
              snSpan=(snSpan=="*" ? "S" : "N");              
              arr.push({item:lin
                        ,codgp:reg     
                        ,pk:indice 
                        ,fa:"fa fa-circle-o" ,cor:"green"
                        ,obrigatorio:"N"           
                        ,modelo:(pega.gmaceito).replaceAll("_",",")
                        ,span:snSpan                          
              });
              indice++;              
            });
          };
          let clsCode = new concatStr(); 
          let func;  
          let trCor;
          arr.forEach(function(reg){
            func  = "onClick=fncCheck("+reg.pk+",'"+reg.codgp+"','"+reg.modelo+"')";
            trCor = ( (reg.item % 2) ?  "#F5F5F5" :  "white" );
            
            clsCode.concat("    <tr class='fpBodyTr' style='background-color:"+trCor+";' />");
            clsCode.concat("      <td class='fpTd colunaOculta'>"+reg.pk+"</td>");            
            clsCode.concat("      <td class='fpTd colunaOculta'>"+reg.item+"</td>");                          
            clsCode.concat("      <td class='fpTd'>"+reg.codgp+"</td>");
            clsCode.concat("      <td class='fpTd textoCentro'>");
            clsCode.concat("        <div width='100%' height='100%' "+func+">");
            clsCode.concat("          <i id='img"+reg.pk+"' class='"+reg.fa+"' style='margin-left:10px;font-size:1.5em;color:"+reg.cor+";'></i>");
            clsCode.concat("        </div>");
            clsCode.concat("      </td>");
            clsCode.concat("      <td class='fpTd'></td>");            
            clsCode.concat("      <td class='fpTd'></td>");                        
            clsCode.concat("      <td class='fpTd'></td>");            
            clsCode.concat("      <td class='fpTd'></td>");                        
            clsCode.concat("      <td class='fpTd textoCentro'>");
            clsCode.concat("        <div width='100%' height='100%' onClick=funcLimpar("+reg.pk+");>");
            clsCode.concat("          <i class='fa fa-edit' style='margin-left:10px;font-size:1.5em;color:'red';'></i>");
            clsCode.concat("        </div>");
            clsCode.concat("      </td>");
            clsCode.concat("      <td class='fpTd colunaOculta'>"+reg.obrigatorio+"</td>");
            clsCode.concat("      <td class='fpTd colunaOculta'>"+reg.modelo+"</td>");              
            if( reg.span=="S" ){
              clsCode.concat("      <td rowspan='"+tamSpan+"' class='fpTd textoCentro'>"+jsNmrs(reg.item).emZero(3).ret()+"</td>");  
            }  
            clsCode.concat("    </tr>");
          });
          document.getElementById("tbody_tblAm").innerHTML=clsCode.fim();
          pubTbl  = tblAm.getElementsByTagName("tbody")[0];
          pubRows = pubTbl.rows.length; 
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function fncCheck(pLin,codgp,codgm){
        ///////////////////////////////////////
        // Retirando do select produtos em tela
        ///////////////////////////////////////
        let sqlIn="";
        for(let lin=0 ; (lin<pubRows) ; lin++){                
          if( pubTbl.rows[lin].cells[objCol.COD].innerHTML != "" )
            sqlIn+=jsNmrs(pubTbl.rows[lin].cells[objCol.COD].innerHTML).inteiro().ret()+",";    
        };
        if( sqlIn != "" )
          sqlIn=sqlIn.slice(0,-1);
        ////////////////////////////////////
        // Quando fechar atualizo esta linha
        ////////////////////////////////////
        linTable=pLin;
        fGrupoModeloProdutoF10(0,"nsa","null",100
          ,{ codgp: codgp
            ,codpe:"EST"
            ,codaut:0 
            ,divWidth:"76em"
            ,tblWidth:"74em"
            ,where: (sqlIn != "" ? " {AND} (A.GMP_CODGM IN("+codgm+")) AND (A.GMP_CODIGO NOT IN("+sqlIn+"))" : "{AND} (A.GMP_CODGM IN("+codgm+"))" )
        }); 
      };
      function RetF10tblGmp(arr){
        let elImg;        
        for(let lin=0 ; (lin<pubRows) ; lin++){        
          if( jsNmrs(linTable).inteiro().ret() == jsNmrs(pubTbl.rows[lin].cells[objCol.LINHA].innerHTML).inteiro().ret() ){   
            elImg = "img"+jsNmrs(pubTbl.rows[lin].cells[objCol.LINHA].innerHTML).inteiro().ret();
            pubTbl.rows[lin].cells[objCol.COD].innerHTML     = arr[0].CODIGO;
            pubTbl.rows[lin].cells[objCol.PRODUTO].innerHTML = arr[0].DESCRICAO;
            pubTbl.rows[lin].cells[objCol.SERIE].innerHTML   = arr[0].SERIE;
            pubTbl.rows[lin].cells[objCol.SINCARD].innerHTML = arr[0].SINCARD;            
            document.getElementById(elImg).style.color= "green";
            break;
          };
        };
      };
      function funcLimpar(ind){
        let elImg;        
        for(let lin=0 ; (lin<pubRows) ; lin++){        
          if( jsNmrs(ind).inteiro().ret() == jsNmrs(pubTbl.rows[lin].cells[objCol.LINHA].innerHTML).inteiro().ret() ){        
            elImg = "img"+jsNmrs(pubTbl.rows[lin].cells[objCol.LINHA].innerHTML).inteiro().ret();                 
            pubTbl.rows[lin].cells[objCol.COD].innerHTML     = "";
            pubTbl.rows[lin].cells[objCol.PRODUTO].innerHTML = "";
            pubTbl.rows[lin].cells[objCol.SERIE].innerHTML   = "";
            pubTbl.rows[lin].cells[objCol.SINCARD].innerHTML = "";
            if( pubTbl.rows[lin].cells[objCol.OBR].innerHTML=="S" )
              document.getElementById(elImg).style.color= "red";
            break;
          };
        };
      };
      function horCadastrarClick(){
        try{       
          ////////////////////////////////////////////////
          // O modelo tem que ser valido na hora de gravar
          ////////////////////////////////////////////////  
          let codAm=jsNmrs("edtCodAm").inteiro().ret();
          if( codAm<=0 )
            throw "Favor informar modelo valido!";   
          //
          //////////////////////////////////////////////////////////////////
          // Checagem basica se naum existe duplicidade de codigo de produto
          //////////////////////////////////////////////////////////////////
          let arrDuplicidade = [];
          //
          clsJs = jsString("lote");
          let unidade   = 1;          
          let novoItem  = "N";
          for(let lin=0 ; (lin<pubRows) ; lin++){
            //////////////////////////////////////////////////////
            // Se o campo for obrigatorio e naum informado produto
            //////////////////////////////////////////////////////
            if( (pubTbl.rows[lin].cells[objCol.OBR].innerHTML=="S") && (pubTbl.rows[lin].cells[objCol.COD].innerHTML=="") )
              throw "GRUPO "+pubTbl.rows[lin].cells[objCol.GRUPO].innerHTML+" OBRIGATORIO PRODUTO!";
            //  
            //
            if(pubTbl.rows[lin].cells[objCol.COD].innerHTML != "" ){  
              /////////////////////////////////////////////////////////////////////////////////////////////
              // Como naum tenho a referencia da coluna unidade pois esta com rowspam pego o novo item aqui
              /////////////////////////////////////////////////////////////////////////////////////////////
              if( jsNmrs(pubTbl.rows[lin].cells[objCol.ITEM].innerHTML).inteiro().ret()==unidade ){    
                unidade++;
                novoItem="S";
              } else {
                novoItem="N";  
              }
              //
              //
              clsJs.add("rotina"     , "cadamp"                                         );
              clsJs.add("login"      , jsPub[0].usr_login                               );
              clsJs.add("codam"      , jsNmrs("edtCodAm").inteiro().ret()               );
              clsJs.add("codgmp"     , pubTbl.rows[lin].cells[objCol.COD].innerHTML     );
              clsJs.add("serie"      , pubTbl.rows[lin].cells[objCol.SERIE].innerHTML   );
              clsJs.add("codgp"      , pubTbl.rows[lin].cells[objCol.GRUPO].innerHTML   );
              clsJs.add("novoitem"   , novoItem                                         );
              clsJs.add("item"       , pubTbl.rows[lin].cells[objCol.ITEM].innerHTML    );
              //////////////////////////////////////////////////////////////////////////////////////////////////////
              // O select de produtos tira os ja cadastrados mas naum custa nada checar novamente se tem duplicidade
              //////////////////////////////////////////////////////////////////////////////////////////////////////
              if( pubTbl.rows[lin].cells[objCol.COD].innerHTML != "" ){
                if( arrDuplicidade.indexOf(pubTbl.rows[lin].cells[objCol.COD].innerHTML) != -1 ){
                  msg="PRODUTO "+pubTbl.rows[lin].cells[objCol.COD].innerHTML+" DUPLICADA NA GRADE!";  
                } else {
                  arrDuplicidade.push(pubTbl.rows[lin].cells[objCol.COD].innerHTML);  
                };
              };
            };
          };  
          ///////////////////////
          // Enviando para gravar
          ///////////////////////
          envPhp=clsJs.fim();
          fd = new FormData();
          fd.append("automodelocad" , envPhp);
          msg=requestPedido("Trac_AutoModeloCad.php",fd); 
          retPhp=JSON.parse(msg);

          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cad",retPhp[0].erro,{cabec:"Aviso"});    
          } else {  
            /////////////////////////////////////////////////
            // Atualizando a grade em Trac_AutoModelo.php  //
            /////////////////////////////////////////////////
            let el  = window.opener.document.getElementById("tblAm");
            let tbl = el.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
                
            for(let lin=0 ; (lin<nl) ; lin++){
              if( jsNmrs(pega.codam).inteiro().ret() == jsNmrs(tbl.rows[lin].cells[parseInt(pega.colCodigo)].innerHTML).inteiro().ret() ){
                let estoque=( jsNmrs(tbl.rows[lin].cells[parseInt(pega.colEstoque)].innerHTML).inteiro().ret() + jsNmrs(retPhp[0].contador).inteiro().ret() );
                tbl.rows[lin].cells[parseInt(pega.colEstoque)].innerHTML = jsNmrs(estoque).emZero(4).ret(); 
                break;  
              };
            };  
            window.close();
          };
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
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
        <div style="font-size:12px;width:50%;float:left;"><h2 id="h2Rotina" style="text-align:center;">Cadastrar auto</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmAm" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 550px;">
          <div class="campotexto campo100">
            <h2>Informe</h2>
          </div>
          
          <div class="campotexto campo100">
            <div class="campotexto campo10">
              <input class="campo_input_titulo" id="edtCodAm" type="text" disabled />
              <label class="campo_label">MODELO:</label>            
            </div>
            <div class="campotexto campo40">
              <input class="campo_input_titulo" id="edtDesAm" type="text" disabled />
              <label class="campo_label">MODELO_NOME:</label>                        
            </div>
            <div class="campotexto campo10">
              <input class="campo_input input" id="edtUnidades" 
                                               OnKeyPress="return mascaraInteiro(event);" 
                                               OnBlur="this.value=jsNmrs(this.id).emZero(3).ret();"
                                               type="text" 
                                               maxlength="3"/>
              <label class="campo_label campo_required" for="edtUnidades">Unidade(s)</label>
            </div>
            <div onClick="fncGerar();" class="btnImagemEsq bie12 bieAzul"><i id="imgOk" class="fa fa-check"> Gerar</i></div>          
          </div>
          <div id="sctnAm">
          </div>  
        </div> 
      </form>
    </div>
  </body>
</html>