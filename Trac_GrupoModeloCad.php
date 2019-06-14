<?php
  session_start();
  if( isset($_POST["grupomodelocad"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["grupomodelocad"]);
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
        ///////////////////////////////////////////////
        //     Entrada em estoque GRUPOMODELOPRODUTO      //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="cadgmp" ){
          $contador = 0;
          $gmplIni   = 0;
          $gmpFim   = 0;
          
          $codgml=$classe->generator("GRUPOMODELOLOTE");
          foreach ( $lote as $reg ){
            $codgmp=$classe->generator("GRUPOMODELOPRODUTO");          
            
            //////////////////////////////////////////////////////////////////////////
            // Guardando o codigo inicial/final para montar um lote(GRUPOMODELOPRODUTOLOTE)
            //////////////////////////////////////////////////////////////////////////
            if( $contador==0 ){
              $gmpIni = $codgmp;
              $gmpFim = $codgmp;        // Se tiver soh um item no lote                  
              $codgm  = $reg->codgm;
            } else {
              $gmpFim=$codgmp;                
            };
            //
            $sql="";
            $sql.="INSERT INTO VGRUPOMODELOPRODUTO("; 
            $sql.="    GMP_CODIGO";
            $sql.="   ,GMP_CODCNTT";            
            $sql.="   ,GMP_CODGM";            
            $sql.="   ,GMP_CODGP";
            $sql.="   ,GMP_CODPE";
            $sql.="   ,GMP_CODPEI";
            $sql.="   ,GMP_CODFBR";
            $sql.="   ,GMP_NUMSERIE";
            $sql.="   ,GMP_SINCARD";
            $sql.="   ,GMP_OPERADORA";
            $sql.="   ,GMP_FONE";
            $sql.="   ,GMP_CONTRATO";
            $sql.="   ,GMP_CODGML";
            $sql.="   ,GMP_PLACACHASSI";
            $sql.="   ,GMP_COMPOSICAO";
            $sql.="   ,GMP_CODUSR) VALUES(";            
            $sql.="'$codgmp'";                    // GMP_CODIGO
            $sql.=",0";                           // GMP_CODCNTT";            
            $sql.=","  .$reg->codgm;              // GMP_CODGM
            $sql.=",'" .$reg->codgp."'";          // GMP_CODGP
            $sql.=",'" .$reg->codpe."'";          // GMP_CODPE
            $sql.=","  .$reg->codpei;             // GMP_CODPEI
            $sql.=","  .$reg->codfbr;             // GMP_CODFBR
            $sql.=",'" .$reg->numserie."'";       // GMP_NUMSERIE
            $sql.=",'" .$reg->sincard."'";        // GMP_SINCARD
            $sql.=",'" .$reg->operadora."'";      // GMP_OPERADORA
            $sql.=",'" .$reg->fone."'";           // GMP_FONE
            $sql.=",'" .$reg->contrato."'";       // GMP_CONTRATO
            $sql.=","  .$codgml;                  // GMP_CODGML(GRUPOMODELOLOTE)
            $sql.=",'NSA0000'";                   // GMP_PLACACHASSI";
            $sql.=",0";                           // GMP_COMPOSICAO";
            $sql.=","  .$_SESSION["usr_codigo"];  // GMP_CODUSR                      
            $sql.=")";
            array_push($arrUpdt,$sql);            
            $contador++;
          };
          
          $sql="";
          $sql.="INSERT INTO GRUPOMODELOLOTE("; 
          $sql.="    GML_CODIGO";
          $sql.="   ,GML_CODGM";                      
          $sql.="   ,GML_DATA";            
          $sql.="   ,GML_ENTRADA";
          $sql.="   ,GML_CODGMPINI";
          $sql.="   ,GML_CODGMPFIM";
          $sql.="   ,GML_CODUSR) VALUES(";
          $sql.="'$codgml'";                    // GML_CODIGO
          $sql.=","  .$codgm;                   // GML_CODGM
          $sql.=",'".date('m/d/Y')."'";         // GML_DATA
          $sql.=","  .$contador;                // GML_ENTRADA
          $sql.=","  .$gmpIni;                  // GML_CODGMPINI
          $sql.=","  .$gmpFim;                  // GML_CODGMPFIM
          $sql.=","  .$_SESSION["usr_codigo"];  // GML_CODUSR          
          $sql.=")";
          array_push($arrUpdt,$sql);            
          $atuBd = true;
        };    
        //file_put_contents("aaa.xml",print_r($arrUpdt,true));
        ////////////////////////////////////
        //        Importar excel          //
        ////////////////////////////////////
        if( $lote[0]->rotina=="impExcel" ){
          //////////////////////////////////////////////////////////////////////////////////////
          // Abrindo o excel e pegando as colunas obrigatorias                                //
          //////////////////////////////////////////////////////////////////////////////////////  
          $data       = [];
          $strExcel   = "S";                                                // Se S mostra na grade e importa, se N só mostra na grade    
          $dom        = DOMDocument::load($_FILES["arquivo"]["tmp_name"]);  // Abre o arquivo completo
          $rows       = $dom->getElementsByTagName("Row");                  // Retorna um array de todas as linhas
          $tamR       = $rows->length;                                      // tamanho do array rows
          //////////////////////////////////////////////////////////////////////////////////////
          // Correndo o excel                                                                 //
          // Pego o cabecalho do excel na linha 0 para comparar com o js acima campo labelCol // 
          //////////////////////////////////////////////////////////////////////////////////////
          for ($linR = 1; $linR < $tamR; $linR ++){
            $cells = $rows->item($linR)->getElementsByTagName("Cell");
            $linha   = [];
            foreach($cells as $cell){
              array_push($linha,strtoupper( $cell->nodeValue ));
            };
            array_push($data,$linha);
          };
          $retorno='[{"retorno":"OK","dados":'.json_encode($data).',"erro":"OK"}]';              
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
        ////////////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_GrupoModelo.php
        ////////////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addAlt")).lote[0];
        //localStorage.removeItem("addAlt");
        document.getElementById("descricao").innerHTML=pega.descricao;
        ///////////////////////////////////////////////////////////////////////
        // Estes saum para se selecionar cadastro indidual, faz somente uma vez
        // Tem que ser na ordem dos input devido foco
        ///////////////////////////////////////////////////////////////////////  
        if( pega.serie=="NAO" ){
          jsCmpAtivo("edtNumSerie").remove("campo_input").add("campo_input_titulo").disabled(true);
          document.getElementById("edtNumSerie").value="NSA";
          document.getElementById("edtNumSerie").setAttribute("data-oldvalue","NSA"); 
        } else {
          jsCmpAtivo("lblNumSerie").add("campo_required");  
          pega.foco=( pega.foco=="nsa" ? "edtNumSerie" : pega.foco );          
        }; 
        if( pega.sincard=="NAO" ){
          jsCmpAtivo("edtSincard").remove("campo_input").add("campo_input_titulo").disabled(true);
          document.getElementById("edtSincard").value="NSA";
          document.getElementById("edtSincard").setAttribute("data-oldvalue","NSA");
        } else {
          jsCmpAtivo("lblSincard").add("campo_required");  
          pega.foco=( pega.foco=="nsa" ? "edtSincard" : pega.foco );          
        }; 
        if( pega.operadora=="NAO" ){
          jsCmpAtivo("edtOperadora").remove("campo_input").add("campo_input_titulo").disabled(true);
          document.getElementById("edtOperadora").value="NSA";
          document.getElementById("edtOperadora").setAttribute("data-oldvalue","NSA");
        } else {
          jsCmpAtivo("lblOperadora").add("campo_required");  
          pega.foco=( pega.foco=="nsa" ? "edtOperadora" : pega.foco );          
        }; 
        if( pega.fone=="NAO" ){
          jsCmpAtivo("edtFone").remove("campo_input").add("campo_input_titulo").disabled(true);
          document.getElementById("edtFone").value="NSA";
          document.getElementById("edtFone").setAttribute("data-oldvalue","NSA");
        } else {
          jsCmpAtivo("lblFone").add("campo_required");  
          pega.foco=( pega.foco=="nsa" ? "edtFone" : pega.foco );                    
        }; 
        if( pega.contrato=="NAO" ){
          jsCmpAtivo("edtContrato").remove("campo_input").add("campo_input_titulo").disabled(true);
          document.getElementById("edtContrato").value="NSA";
          document.getElementById("edtContrato").setAttribute("data-oldvalue","NSA");
        } else {
          jsCmpAtivo("lblContrato").add("campo_required");
          pega.foco=( pega.foco=="nsa" ? "edtContrato" : pega.foco );  
        }; 
        //
        //
        jsGmc={
          "titulo":[
             {"id":0  ,"labelCol"       : "FABRICANTE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":1  ,"labelCol"       : "MODELO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "SERIE"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "SINCARD"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "OPERADORA"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "FONE"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "CONTRATO"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
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
             {"texto":"Individual"  ,"name":"horInd"        ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-plus"        }          
            ,{"texto":"Gravar"      ,"name":"horGravar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-save"        }          
            ,{"texto":"Excel"       ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }  
            ,{"texto":"Imprimir"    ,"name":"horImprimir"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-print"        }                    
            ,{"texto":"Fechar"      ,"name":"horFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"       }
          ] 

          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmGmc"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaGmc"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmGmc"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnGmc"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblGmc"                  // Nome da table
          ,"prefixo"        : "Gmc"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "absolute"
          ,"width"          : "110em"                   // Tamanho da table
          ,"height"         : "65em"                    // Altura da table
          ,"nChecks"        : true                      // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "190px"                   // Se tiver menu esquerdo
          ,"relTitulo"      : "GRUPO"                     // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"formPassoPasso" : "*"                       // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "GRUPO"                   // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
        }; 
        if( objGmc === undefined ){  
          objGmc=new clsTable2017("objGmc");
        };  
        objGmc.montarHtmlCE2017(jsGmc)
      });
      //
      var objGmc;                     // Obrigatório para instanciar o JS Semestral
      var jsGmc;                      // Obj principal da classe clsTable2017
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var clsErro;                    // Classe para erros 
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var pega;                       // Recuperar localStorage      
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      //
      function fncAbrir(){
        try{
          clsErro = new clsMensagem("Erro");
          clsErro.notNull("ARQUIVO"       ,edtArquivo.value);
          if( clsErro.ListaErr() != "" ){
            clsErro.Show();
          } else {
            clsJs   = jsString("lote");  
            clsJs.add("rotina"  , "impExcel"          );
            clsJs.add("login"   , jsPub[0].usr_login  );
            envPhp=clsJs.fim();
            fd = new FormData();
            fd.append("grupomodelocad"  , envPhp              );
            fd.append("arquivo"         , edtArquivo.files[0] );
            msg     = requestPedido("Trac_GrupoModeloCad.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno == "OK" ){
              //////////////////////////////////////////////////////////////////////////////////
              // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
              // Campo obrigatório se existir rotina de manutenção na table devido Json       //
              // Esta rotina não tem manutenção via classe clsTable2017                       //
              // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
              //////////////////////////////////////////////////////////////////////////////////
              jsGmc.registros=retPhp[0]["dados"];
              objGmc.montarBody2017();
            };  
            /////////////////////////////////////////////////////////////////////////////////////////
            // Mesmo se der erro mostro o erro, se der ok mostro a qtdade de registros atualizados //
            // dlgCancelar fecha a caixa de informacao de data                                     //
            /////////////////////////////////////////////////////////////////////////////////////////
            //gerarMensagemErro("BCD",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
        };          
      };  
      function horFecharClick(){
        window.close();
      };
      function horGravarClick(){
        try{
          /////////////////////////////////////////
          // Instanciando classe para enviar ao Php
          /////////////////////////////////////////
          clsJs=jsString("lote");            
          //
          /////////////////////////////////////////////////////////////////////////
          // Checagem basica se naum existe duplicidade de serie/sincard no arquivo
          /////////////////////////////////////////////////////////////////////////
          let arrDuplicidade = new Array();
          //  
          clsChecados = objGmc.gerarJson();
          clsChecados.retornarQtos("n");
          clsChecados.temColChk(false);
          chkds  = clsChecados.gerar();
          
          msg="ok";        
          chkds.forEach(function(reg){
            ///////////////////////////////////////////////////
            // Checando o fabricante(vem da selecao do modelo )
            ///////////////////////////////////////////////////
            if( pega.fabricante != reg.FABRICANTE ) 
              msg="FABRICANTE INFORMADO NA PLANILHA DIVERGE DO SELECIONADO PARA ENTRADA EM ESTOQUE!";
            ///////////////////////////////////////////////////
            // Checando o modelo(vem da selecao do modelo )
            ///////////////////////////////////////////////////
            if( jsNmrs(pega.codgm).inteiro().ret() != jsNmrs(reg.MODELO).inteiro().ret() ) 
              msg="MODELO INFORMADO NA PLANILHA DIVERGE DO SELECIONADO PARA ENTRADA EM ESTOQUE!";
            ///////////////////////////////////////////////////
            // Checando a informacao do contrato
            ///////////////////////////////////////////////////
            if( (pega.contrato=="NAO") && (reg.CONTRATO != "NSA") )
              msg="CONTRATO DEVE SER 'NSA' PARA ESTE MODELO!";
            if( (pega.contrato=="SIM") && (reg.CONTRATO == "NSA") )
              msg="CONTRATO DEVE SER INFORMADO PARA ESTE MODELO!";
            ///////////////////////////////////////////////////
            // Checando a informacao do fone
            ///////////////////////////////////////////////////
            if( (pega.fone=="NAO") && (reg.FONE != "NSA") )
              msg="FONE DEVE SER 'NSA' PARA ESTE MODELO!";
            if( (pega.fone=="SIM") && (reg.FONE == "NSA") )
              msg="FONE DEVE SER INFORMADO PARA ESTE MODELO!";
            ///////////////////////////////////////////////////
            // Checando a informacao da operadora
            ///////////////////////////////////////////////////
            if( (pega.operadora=="NAO") && (reg.OPERADORA != "NSA") )
              msg="OPERADORA DEVE SER 'NSA' PARA ESTE MODELO!";
            if( (pega.operadora=="SIM") && (reg.OPERADORA == "NSA") )
              msg="OPERADORA DEVE SER INFORMADO PARA ESTE MODELO!";
            ///////////////////////////////////////////////////
            // Checando a informacao da serie
            ///////////////////////////////////////////////////
            if( (pega.serie=="NAO") && (reg.SERIE != "NSA") )
              msg="SERIE DEVE SER 'NSA' PARA ESTE MODELO!";
            if( (pega.serie=="SIM") && (reg.SERIE == "NSA") )
              msg="SERIE DEVE SER INFORMADO PARA ESTE MODELO!";
            // Duplicidade de serie/sincard
            if( reg.SERIE != "NSA" ){
              if( arrDuplicidade.indexOf("SERIE"+reg.SERIE) != -1 ){
                msg="SERIE "+reg.SERIE+" DUPLICADA NO ARQUIVO!";  
              } else {
                arrDuplicidade.push("SERIE"+reg.SERIE);  
              };
            };
            ///////////////////////////////////////////////////
            // Checando a informacao do sincard
            ///////////////////////////////////////////////////
            if( (pega.sincard=="NAO") && (reg.SINCARD != "NSA") )
              msg="SINCARD DEVE SER 'NSA' PARA ESTE MODELO!";
            if( (pega.sincard=="SIM") && (reg.SINCARD == "NSA") )
              msg="SINCARD DEVE SER INFORMADO PARA ESTE MODELO!";
            // Duplicidade de serie/sincard
            if( reg.SINCARD != "NSA" ){
              if( arrDuplicidade.indexOf("SINCARD"+reg.SINCARD) != -1 ){
                msg="SINCARD "+reg.SINCARD+" DUPLICADA NO ARQUIVO!";  
              } else {
                arrDuplicidade.push("SINCARD"+reg.SINCARD);  
              };
            };
            //
            //
            clsJs.add("rotina"    , "cadgmp"            );              
            clsJs.add("login"     , jsPub[0].usr_login  );           
            clsJs.add("codgm"     , reg.MODELO          );                     
            clsJs.add("codgp"     , pega.codgp          );           
            clsJs.add("codpe"     , "EST"               );
            clsJs.add("codpei"    , 1                   );
            clsJs.add("codfbr"    , pega.codfbr         );
            clsJs.add("numserie"  , reg.SERIE           );
            clsJs.add("sincard"   , reg.SINCARD         );
            clsJs.add("operadora" , reg.OPERADORA       );
            clsJs.add("fone"      , reg.FONE            );          
            clsJs.add("contrato"  , reg.CONTRATO        );
            
            if( msg != "ok" ){          
              throw msg;
            };
          });
          
          var fd = new FormData();
          fd.append("grupomodelocad" , clsJs.fim());
          msg=requestPedido("Trac_GrupoModeloCad.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            /////////////////////////////////////////////////
            // Atualizando a grade em Trac_GrupoModelo.php
            /////////////////////////////////////////////////
            let el  = window.opener.document.getElementById("tblGm");
            let tbl = el.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
                
            for(let lin=0 ; (lin<nl) ; lin++){
              if( jsNmrs(pega.codgm).inteiro().ret() == jsNmrs(tbl.rows[lin].cells[parseInt(pega.colCodigo)].innerHTML).inteiro().ret() ){
                let estoque=( jsNmrs(tbl.rows[lin].cells[parseInt(pega.colEstoque)].innerHTML).inteiro().ret() + jsNmrs(retPhp[0].contador).inteiro().ret() );
                tbl.rows[lin].cells[parseInt(pega.colEstoque)].innerHTML = jsNmrs(estoque).emZero(4).ret(); 
                break;  
              };
            };  
            window.close();
          } else {
            gerarMensagemErro("USU",retPhp[0].erro,{cabec:"Erro"});  
          };
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
      function horIndClick(){
        document.getElementById("individual").style.display="block"; 
        document.getElementById(pega.foco).foco();
        document.getElementById("edtNumSerie").value  = document.getElementById("edtNumSerie").getAttribute("data-oldvalue");
        document.getElementById("edtSincard").value   = document.getElementById("edtSincard").getAttribute("data-oldvalue");
        document.getElementById("edtOperadora").value = document.getElementById("edtOperadora").getAttribute("data-oldvalue");
        document.getElementById("edtFone").value      = document.getElementById("edtFone").getAttribute("data-oldvalue");
        document.getElementById("edtContrato").value  = document.getElementById("edtContrato").getAttribute("data-oldvalue");
      };
      function indConfirmarClick(){
        jsGmc.registros.push([
          pega.fabricante
          ,pega.codgm
          ,document.getElementById("edtNumSerie").value
          ,document.getElementById("edtSincard").value
          ,document.getElementById("edtOperadora").value
          ,document.getElementById("edtFone").value
          ,document.getElementById("edtContrato").value
        ]);
        objGmc.montarBody2017();
        document.getElementById("individual").style.display="none";
      };
      
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:10%;float:left;"><h2 style="text-align:center;">Entrada produto</h2></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:30%;float:left;"><h2 id="descricao" style="text-align:center;"></h2></div>        
        <div class="teEsquerda"></div>    
        <div class="custom_file_upload" style="font-size:12px;width:20%;float:left;">
          <input type="text" class="file" name="file_info" id="file_info">
          <div class="file_upload">
            <input type="file" id="edtArquivo" name="edtArquivo" onChange="document.getElementById('file_info').value=this.files[0].name;">
          </div>
        </div>
        <div onClick="fncAbrir();"    class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-folder-open"> Abrir</i></div>                
      </div>
      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <section>
        <section id="sctnGmc" style="margin-left:100px;">
        </section>  
      </section>
      <form method="post" class="center" id="frmGmc" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
      </form>
    </div>
    
    <!--
    Individual
    -->  
    <div id="individual" class="frmTable" style="display:none; width:70em; margin-left:19em;margin-top:5.5em;position:absolute;">
      <div class="frmTituloManutencao">Individual<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>
      <div style="height: 150px; overflow-y: auto;">
        <input type="hidden" id="sql" name="sql">
        <div class="campotexto campo50">
          <input class="campo_input" id="edtNumSerie" data-oldvalue="" type="text" maxlength="20" />
          <label class="campo_label" id="lblNumSerie" for="edtNumSerie">SERIE:</label>
        </div>
        <div class="campotexto campo50">
          <input class="campo_input" id="edtSincard" data-oldvalue="" type="text" maxlength="20" />
          <label class="campo_label" id="lblSincard"  for="edtSincard">SINCARD:</label>
        </div>
        <div class="campotexto campo50">
          <input class="campo_input" id="edtOperadora" data-oldvalue="" type="text" maxlength="15" />
          <label class="campo_label" id="lblOperadora"  for="edtOperadora">OPERADORA:</label>
        </div>
        <div class="campotexto campo50">
          <input class="campo_input" id="edtFone" data-oldvalue="" type="text" maxlength="15" />
          <label class="campo_label" id="lblFone"  for="edtFone">FONE:</label>
        </div>
        <div class="campotexto campo50">
          <input class="campo_input" id="edtContrato" data-oldvalue="" type="text" maxlength="10" />
          <label class="campo_label" id="lblContrato"  for="edtContrato">CONTRATO:</label>
        </div>
        
        <div onClick="indConfirmarClick();" id="indConfirmar" class="btnImagemEsq bie20 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
        <div onClick="document.getElementById('individual').style.display='none';" id="trCancelar" class="btnImagemEsq bie20 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
      </div>
    </div>
    <!--
    Fim individual
    -->  
  </body>
</html>