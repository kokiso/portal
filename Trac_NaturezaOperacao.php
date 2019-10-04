<?php
  session_start();
  if( isset($_POST["naturezaoperacao"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");       

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["naturezaoperacao"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $strExcel = "*"; 
        $arrUpdt  = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        ////////////////////////////////////////////////
        //   Dados para JavaScript NATUREZAOPERACAO   //
        ////////////////////////////////////////////////
        if( $lote[0]->rotina=="selectNo" ){
          $sql= "SELECT NO_CODIGO";
          $sql.="       ,NO_NOME";
          $sql.="       ,CASE WHEN A.NO_FINNFE='1' THEN CAST('NFe_NORMAL' AS VARCHAR(10))";
          $sql.="             WHEN A.NO_FINNFE='2' THEN CAST('NFe_COMPLEM' AS VARCHAR(11))";
          $sql.="             WHEN A.NO_FINNFE='3' THEN CAST('NFe_AJUSTE' AS VARCHAR(10))";
          $sql.="             WHEN A.NO_FINNFE='4' THEN CAST('DEVOL_RETORNO' AS VARCHAR(13)) END AS NO_FINNFE";
          $sql.="       ,NO_CODPTP";
          $sql.="       ,NO_CODPT";
          $sql.="       ,PT_NOME";                    
          $sql.="       ,CASE WHEN A.NO_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS NO_ATIVO";
          $sql.="       ,CASE WHEN A.NO_REG='P' THEN 'PUB' WHEN A.NO_REG='S' THEN 'SIS' ELSE 'ADM' END AS NO_REG";
          $sql.="       ,US_APELIDO";
          $sql.="       ,NO_CODUSR";
          $sql.="  FROM NATUREZAOPERACAO A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN PADRAOTITULO PT ON A.NO_CODPT=PT.PT_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.NO_CODUSR=U.US_CODIGO";
          $sql.=" WHERE (NO_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')";                 
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        ////////////////////////////////////
        //        Importar excel          //
        ////////////////////////////////////
        if( $lote[0]->rotina=="impExcel" ){
          ////////////////////////////////////////////////////////////////////////
          // Enviando para a class todas as colunas com as checagens necessaria //
          // Nome da tabela e numeros de erros(se existir)                      //
          // Modelo do JSON estah na classe                                     //
          ////////////////////////////////////////////////////////////////////////
          $matriz   = (array)$lote[0]->titulo;
          $vldCampo = new validaCampo("VNATUREZAOPERACAO",0);
          //////////////////////////////////////////////////////////////////////////////////////
          // Abrindo o excel e pegando as colunas obrigatorias                                //
          // A coluna deve ter o campo labelCol do js acima - as que tenha vlrDefault <>"nsa" //
          //////////////////////////////////////////////////////////////////////////////////////  
          $data       = [];
          $arrCabec   = [];          
          $strExcel   = "S";                                                // Se S mostra na grade e importa, se N só mostra na grade    
          $dom        = DOMDocument::load($_FILES["arquivo"]["tmp_name"]);  // Abre o arquivo completo
          $rows       = $dom->getElementsByTagName("Row");                  // Retorna um array de todas as linhas
          $tamR       = $rows->length;                                      // tamanho do array rows
          //////////////////////////////////////////////////////////////////////////////////////
          // Correndo o excel                                                                 //
          // Pego o cabecalho do excel na linha 0 para comparar com o js acima campo labelCol // 
          //////////////////////////////////////////////////////////////////////////////////////
          for ($linR = 0; $linR < $tamR; $linR ++){
            $cells = $rows->item($linR)->getElementsByTagName("Cell");
            if( $linR==0 ){
              foreach($cells as $cell){
                array_push($arrCabec,strtoupper( $cell->nodeValue ));
              };  
              continue;      
            };  
            /////////////////////////////////////////////////////////////////////////
            // Correndo coluna a coluna e validando pelo campo validar do js acima //
            // Para cada nova linha obrigatorio passar o JSON matriz original      //
            /////////////////////////////////////////////////////////////////////////
            $vldCampo->fncMatriz($matriz);
            $coluna=0;
            foreach($cells as $cell){
              $vldCampo->fncValidar($arrCabec[$coluna],$cell->nodeValue);
              $coluna++;
            };
            /////////////////////////////////////////////////////////////////////////////////////////
            // fncLinhaTable -> pega a linha atual e adiciona no array $data para mostrar na table //
            /////////////////////////////////////////////////////////////////////////////////////////
            $retData=$vldCampo->fncLinhaTable();
            array_push($data,$retData);
            //
            /////////////////////////////////
            // Recuperando a instrucao Sql //
            /////////////////////////////////
            array_push($arrUpdt,$vldCampo->fncLinhaInsert());
          };
          $atuBd=($vldCampo->fncNumeroErro()==0 ? true : false );
          if( $atuBd==false ){
            $primeiroErro="ERRO(s) ENCONTRADOS";
            if( isset($vldCampo->fncArrErro()[0][0]) ){
              $primeiroErro   = $vldCampo->fncArrErro()[0][0];
              $tot            = (count($data[0])-1);
              $data[0][$tot]  = $primeiroErro;
            };  
            $retorno='[{"retorno":"OK","dados":'.json_encode($data).',"erro":"'.$primeiroErro.'"}]';     
          };  
        };  
        ///////////////////////////////////////////////////////////////////
        // Atualizando o banco de dados se opcao de insert/updade/delete //
        ///////////////////////////////////////////////////////////////////
        if( $atuBd ){
          if( count($arrUpdt) >0 ){
            $retCls=$classe->cmd($arrUpdt);
            if( $retCls['retorno']=="OK" ){
              $retorno='[{"retorno":"OK","dados":'.json_encode($data).',"erro":"'.count($arrUpdt).' REGISTRO(s) ATUALIZADO(s)!"}]'; 
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
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <title>Natureza Operacao</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoTituloF10.js"></script>        
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        /////////////////////////////////////////////
        //         Objeto clsTable2017 NATUREZAOPERACAO       //
        /////////////////////////////////////////////
        jsNo={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"NO_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [1,2] 
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do naturezaoperacao conforme definição empresa. Este campo é único e deve tem o formato AAA"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "NO_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "85"
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"ajudaCampo"     : ["Nome do naturezaoperacao com até 30 caracteres."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "NO_FINNFE"  
                      ,"labelCol"       : "FINNFE" 
                      ,"newRecord"      : ["1","this","this"]
                      ,"obj"            : "cbFinNfe"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":4  ,"field"          : "NO_CODPTP"  
                      ,"labelCol"       : "TIPO" 
                      ,"newRecord"      : ["CR","this","this"]
                      ,"obj"            : "cbCodPtp"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "30"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,2]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"funcCor"        : "( objCell.innerHTML=='CP'  ? objCell.classList.add('corFonteAlterado') : objCell.classList.remove('corFontAlterado'))"
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}   
            ,{"id":5  ,"field"          : "NO_CODPT" 
                      ,"labelCol"       : "CODPT"
                      ,"obj"            : "edtCodPt"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [  "Codigo da Operacao padrao. Registro deve existir na tabela PadraoTitulo e tem o formato 9999"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "PT_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "OPERACAO_PADRAO "
                      ,"obj"            : "edtDesPt"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "0"
                      //,"newRecord"      : ["","this","this"]
                      ,"truncate"       : true
                      ,"digitosMinMax"  : [1,60]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição de OPERACAO PADRAO para esta natureza."]
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "NO_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"padrao":2}                                        
            ,{"id":8  ,"field"          : "NO_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":9  ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario" 
                      ,"padrao":4}                
            ,{"id":10 ,"field"          : "NO_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":11 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP"        
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objNo.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"300px" 
              ,"label"          :"NATUREZAOPERACAO - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"             }
            ,{"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"  }
            ,{"texto":"Excluir"   ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"            }
            ,{"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"      }  
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"             }                    
            ,{"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"            }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"            // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"             // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmNo"                   // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaNo"                // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmNo"                   // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblNo"                   // Nome da table
          ,"prefixo"        : "no"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VNATUREZAOPERACAO"       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPNATUREZAOPERACAO"     // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "NO_ATIVO"                // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "NO_REG"                  // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "NO_CODUSR"               // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"             // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "108em"                    // Tamanho da table
          ,"height"         : "58em"                    // Altura da table
          ,"tableLeft"      : "sim"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "NATUREZAOPERACAO"        // Titulo do relatório
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"foco"           : ["edtCodigo"
                              ,"edtDescricao"
                              ,"btnConfirmar"]          // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "DESCRICAO"               // Indice inicial da table
          ,"tamBotao"       : "20"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["7em","20em"]                                
          ,"labelMenuTable" : "Opções"                  // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objNo.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objNo.AjudaSisAtivo(jsNo);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objNo.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objNo.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objNo.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objNo.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objNo.altRegSistema("+jsPub[0].usr_d31+");"] 
                               ,["Número de registros em tela"            ,"fa-info"          ,"objNo.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "NATUREZAOPERACAO[14]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objNo === undefined ){  
          objNo=new clsTable2017("objNo");
        };  
        objNo.montarHtmlCE2017(jsNo); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsNo.form).style.width=jsNo.width;
        //
        //
        //////////////////////////////////////////////
        // Montando a table para importar xls       //
        //////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"CODIGO"     ,"labelCol":"CODIGO"    ,"tamGrd":"6em"  ,"tamImp":"20"}
            ,{"id":1  ,"field":"DESCRICAO"  ,"labelCol":"DESCRICAO" ,"tamGrd":"32em" ,"tamImp":"100"}
            ,{"id":2  ,"field":"ERRO"       ,"labelCol":"ERRO"      ,"tamGrd":"35em" ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"       }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"       }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[2].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                                
          ,"div"            : "frmExc"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaExc"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmExc"                  // Onde vai ser gerado o fieldSet                     
          ,"divModal"       : "divTopoInicioE"          // Nome da div que vai fazer o show modal
          ,"tbl"            : "tblExc"                  // Nome da table
          ,"prefixo"        : "exc"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"width"          : "90em"                    // Tamanho da table
          ,"height"         : "48em"                    // Altura da table
          ,"tableLeft"      : "sim"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "Importação nat peracao"  // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"indiceTable"    : "TAG"                     // Indice inicial da table
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"formName"       : "frmExc"                  // Nome do formulario para opção de impressão 
          ,"tamBotao"       : "20"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
        }; 
        if( objExc === undefined ){          
          objExc=new clsTable2017("objCon");
        };  
        objExc.montarHtmlCE2017(jsExc);
        //
        //  
        btnFiltrarClick("S");  
      });
      //
      var objNo;                      // Obrigatório para instanciar o JS NaturezaOpe
      var jsNo;                       // Obj principal da classe clsTable2017
      var objPtF10;                   // Obrigatório para instanciar o JS PadraoTitulo
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d14);
      function funcRetornar(intOpc){
        document.getElementById("divRotina").style.display  = (intOpc==0 ? "block" : "none" );        
        document.getElementById("divExcel").style.display   = (intOpc==1 ? "block" : "none" );
      };
      function fExcel(){
        if( intCodDir<2 ){
          clsErro     = new clsMensagem("Erro");
          clsErro.add("USUARIO SEM DIREITO DE CADASTRAR NESTA TABELA DO BANCO DE DADOS");            
          if( clsErro.ListaErr() != "" ){
            clsErro.Show();
          }
        } else {  
          funcRetornar(1);  
        }  
      };
      function excFecharClick(){
        funcRetornar(0);  
      };
      ////////////////////////////
      // Filtrando os registros //
      ////////////////////////////
      function btnFiltrarClick(atv) { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "selectNo"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        fd = new FormData();
        fd.append("naturezaoperacao" , clsJs.fim());
        msg     = requestPedido("Trac_NaturezaOperacao.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsNo.registros=objNo.addIdUnico(retPhp[0]["dados"]);
          objNo.ordenaJSon(jsNo.indiceTable,false);  
          objNo.montarBody2017();
        };  
      };
      ////////////////////
      // Importar excel //
      ////////////////////
      function btnAbrirExcelClick(){
        try{
          clsErro = new clsMensagem("Erro");
          clsErro.notNull("ARQUIVO"       ,edtArquivo.value);
          if( clsErro.ListaErr() != "" ){
            clsErro.Show();
          } else {
            clsJs   = jsString("lote");  
            clsJs.add("rotina"      , "impExcel"                    );
            clsJs.add("login"       , jsPub[0].usr_login            );
            clsJs.add("titulo"      , objNo.trazCampoExcel(jsNo)  );
            envPhp=clsJs.fim();
            fd = new FormData();
            fd.append("naturezaoperacao"   , envPhp              );
            fd.append("arquivo" , edtArquivo.files[0] );
            msg     = requestPedido("Trac_NaturezaOperacao.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno == "OK" ){
              //////////////////////////////////////////////////////////////////////////////////
              // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
              // Campo obrigatório se existir rotina de manutenção na table devido Json       //
              // Esta rotina não tem manutenção via classe clsTable2017                       //
              // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
              //////////////////////////////////////////////////////////////////////////////////
              jsExc.registros=retPhp[0]["dados"];
              objExc.montarBody2017();
            };  
            /////////////////////////////////////////////////////////////////////////////////////////
            // Mesmo se der erro mostro o erro, se der ok mostro a qtdade de registros atualizados //
            // dlgCancelar fecha a caixa de informacao de data                                     //
            /////////////////////////////////////////////////////////////////////////////////////////
            gerarMensagemErro("NO",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
        };          
      };
      ///////////////////////////////
      //   AJUDA PARA PADRAOTITULO //
      ///////////////////////////////
      function ptFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ptF10Click(obj){ 
        fPadraoTituloF10(0,obj.id,"cbAtivo",100,{debcre: ($doc("cbCodPtp").value=="CR" ? "C" : "D" ), ativo:"S" } ); 
      };
      function RetF10tblPt(arr){
        $doc("edtCodPt").value  = arr[0].CODIGO;
        $doc("edtDesPt").value  = arr[0].DESCRICAO;
        $doc("edtCodPt").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codPtBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          let arr = fPadraoTituloF10(1,obj.id,"cbAtivo",100,
            { codpt  : elNew
             ,debcre: ($doc("cbCodPtp").value=="CR" ? "C" : "D" )
             ,ativo  : "S"} 
          ); 
          $doc(obj.id).value       = ( arr.length == 0 ? "0000"  : jsConverte(arr[0].CODIGO).emZero(4)  );   
          $doc("edtDesPt").value  = ( arr.length == 0 ? "*"     : arr[0].DESCRICAO                      );
          $doc(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )        );
        };
      };
      
    </script>
  </head>
  <body>
    <div class="divTelaCheia">
      <div id="divRotina" class="conteudo">  
        <div id="divTopoInicio">
        </div>
        <form method="post" 
              name="frmNo" 
              id="frmNo" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Natureza Operação<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 140px; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" type="text" maxlength="2" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO</label>
              </div>
              <div class="campotexto campo25">
                <select class="campo_input_combo" id="cbFinNfe">
                  <option value="1">NFe_NORMAL</option>
                  <option value="2">NFe_COMPLEM</option>
                  <option value="3">NFe_AJUSTE</option>
                  <option value="4">DEVOL_RETORNO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">FINALIDADE NFE</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbCodPtp">
                  <option value="CP">A PAGAR</option>
                  <option value="CR">A RECEBER</option>
                </select>
                <label class="campo_label campo_required" for="cbCodPtp">TIPO</label>
              </div>
              
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodPt"
                                                    OnKeyPress="return mascaraInteiro(event);"
                                                    onBlur="codPtBlur(this);" 
                                                    onFocus="ptFocus(this);" 
                                                    onClick="ptF10Click(this);"
                                                    data-newrecord="0000"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="4"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodPt">PADRAO:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesPt" 
                                                        data-newrecord="*"  
                                                        type="text" disabled />
                <label class="campo_label campo_required" for="edtDesPt">PADRAO_NOME:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbAtivo">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">ATIVO</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbReg">
                  <option value="P">PUBLICO</option>               
                </select>
                <label class="campo_label campo_required" for="cbReg">REG</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo" disabled id="edtUsuario" type="text" />
                <label class="campo_label campo_required" for="edtUsuario">USUARIO</label>
              </div>
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
              </div>
              <div id="btnConfirmar" class="btnImagemEsq bie12 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
              <div id="btnCancelar"  class="btnImagemEsq bie12 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
            </div>
          </div>
        </form>
      </div>
      <!--Importar excel -->
      <div id="divExcel" class="divTopoExcel">
      </div>
      <!--Fim Importar excel -->
    </div>       
  </body>
</html>