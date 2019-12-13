<?php
  session_start();
  if( isset($_POST["cidade"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["cidade"]);
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
        $rotina   = $lote[0]->rotina;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        /////////////////////////////////////////
        //    Dados para JavaScript CIDADE     //
        /////////////////////////////////////////
        if( $rotina=="selectCdd" ){
          $sql="";
          $sql.="SELECT A.CDD_CODIGO";
          $sql.="       ,A.CDD_NOME";
          $sql.="       ,A.CDD_CODEST";
          $sql.="       ,E.EST_NOME";
          $sql.="       ,A.CDD_DDD";                    
          $sql.="       ,A.CDD_LATITUDE";
          $sql.="       ,A.CDD_LONGITUDE";
          $sql.="       ,CASE WHEN A.CDD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CDD_ATIVO";
          $sql.="       ,CASE WHEN A.CDD_REG='P' THEN 'PUB' WHEN A.CDD_REG='S' THEN 'SIS' ELSE 'ADM' END AS CDD_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.CDD_CODUSR";
          $sql.="  FROM CIDADE A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.CDD_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN ESTADO E ON A.CDD_CODEST=E.EST_CODIGO";
          if( $lote[0]->codest=="**" ){
            $sql.="  WHERE ((CDD_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."'))";                 
          } else {
            $sql.="  WHERE (((CDD_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')) AND (A.CDD_CODEST='".$lote[0]->codest."'))";  
          }
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
        if( $rotina=="impExcel" ){
          ////////////////////////////////////////////////////////////////////////
          // Enviando para a class todas as colunas com as checagens necessaria //
          // Nome da tabela e numeros de erros(se existir)                      //
          // Modelo do JSON estah na classe                                     //
          ////////////////////////////////////////////////////////////////////////
          $matriz   = (array)$lote[0]->titulo;
          $vldCampo = new validaCampo("VCIDADE",0);
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
    <title>Cidade</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        encherComboUf("cbCodEst");
        //////////////////////////////////////
        //   Objeto clsTable2017 CIDADE     //
        //////////////////////////////////////
        jsCdd={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"CDD_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [7,7] 
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo da cidade conforme IBGE. Este campo é único e deve tem o formato 9999999"
                                            ,"Campo deve ser utilizado no cadastro de cliente/operacao/motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "CDD_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "60"
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"ajudaCampo"     : ["Nome da cidade."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"CDD_CODEST" 
                      ,"labelCol"       : "UF"
                      ,"obj"            : "edtCodEst"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"newRecord"      : ["SP","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [  "Codigo da Regiao. Registro deve existir na tabela de regiao e tem o formato AA"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "EST_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "ESTADO"
                      ,"obj"            : "edtDesEst"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["SAO PAULO","this","this"]
                      ,"digitosMinMax"  : [1,20]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do estado para esta cidade."]
                      ,"padrao":0}
            ,{"id":5  ,"field"          :"CDD_DDD" 
                      ,"labelCol"       : "DDD"
                      ,"obj"            : "edtDdd"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "12"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"                                      
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"ajudaCampo"     : [  "DDD da cidade. Este campo é único e deve tem o formato inteiro"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          :"CDD_LATITUDE" 
                      ,"labelCol"       : "LATITUDE"
                      ,"obj"            : "edtLatitude"
                      ,"inputDisabled"  : true                      
                      ,"fieldType"      : "flo8" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"newRecord"      : ["0.00000000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Latitude da cidade. Registro deve ter o formato 9.99999999"
                                            ,"Informação para ponto de partida"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":7  ,"field"          :"CDD_LONGITUDE" 
                      ,"labelCol"       : "LONGITUDE"
                      ,"obj"            : "edtLongitude"
                      ,"inputDisabled"  : true
                      ,"fieldType"      : "flo8" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"newRecord"      : ["0.00000000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Longitude da cidade. Registro deve ter o formato 9.99999999"
                                            ,"Informação para ponto de chegada"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "CDD_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"padrao":2}                                        
            ,{"id":9  ,"field"          : "CDD_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":10 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"tamImp"         : "0"                      
                      ,"padrao":4}                
            ,{"id":11 ,"field"          : "CDD_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":12 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objCdd.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"CIDADE - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"             }
            ,{"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"  }
            ,{"texto":"Excluir"   ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"            }
            ,{"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"      }        
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"            }                        
            ,{"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"            }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmCdd"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCdd"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCdd"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblCdd"              // Nome da table
          ,"prefixo"        : "cdd"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VCIDADE"             // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPCIDADE"           // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "CDD_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "CDD_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "CDD_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "108em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "CIDADE"              // Titulo do relatório
          ,"relOrientacao"  : "R"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtCodigo"
                              ,"edtDescricao"
                              ,"btnConfirmar"]      // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"     // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "DESCRICAO"           // Indice inicial da table
          ,"tamBotao"       : "12"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"              // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objCdd.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objCdd.AjudaSisAtivo(jsCdd);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objCdd.detalhe();"]
                               //,["Gerar excel"                            ,"fa-file-excel-o"  ,"objCdd.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objCdd.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objCdd.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objCdd.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objCdd.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objCdd.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "CIDADE[29]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objCdd === undefined ){  
          objCdd=new clsTable2017("objCdd");
        };  
        objCdd.montarHtmlCE2017(jsCdd); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        $doc(jsCdd.form).style.width=jsCdd.width;
        $doc("divSobreTable").style.width=jsCdd.width;
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"CODIGO"     ,"labelCol":"CODIGO"    ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":1  ,"field":"DESCRICAO"  ,"labelCol":"DESCRICAO" ,"tamGrd":"32em"    ,"tamImp":"100"}
            ,{"id":2  ,"field":"CODEST"     ,"labelCol":"CODEST"    ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":3  ,"field":"DDD"        ,"labelCol":"DDD"       ,"tamGrd":"4em"     ,"tamImp":"10"}
            ,{"id":4  ,"field":"ERRO"       ,"labelCol":"ERRO"      ,"tamGrd":"45em"    ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[4].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
          ,"relTitulo"      : "Importação região"       // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"indiceTable"    : "TAG"                     // Indice inicial da table
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"formName"       : "frmExc"                  // Nome do formulario para opção de impressão 
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
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
      var objCdd;                     // Obrigatório para instanciar o JS TFormaCob
      var jsCdd;                      // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS EstadoF10
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d29);
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
        clsJs.add("rotina"      , "selectCdd"                               );
        clsJs.add("login"       , jsPub[0].usr_login                        );
        clsJs.add("ativo"       , atv                                       );
        clsJs.add("codest"      , document.getElementById("cbCodEst").value );        
        fd = new FormData();
        fd.append("cidade" , clsJs.fim());
        msg     = requestPedido("Trac_Cidade.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsCdd.registros=objCdd.addIdUnico(retPhp[0]["dados"]);
          objCdd.ordenaJSon(jsCdd.indiceTable,false);  
          objCdd.montarBody2017();
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
            clsJs.add("rotina"      , "impExcel"                  );
            clsJs.add("login"       , jsPub[0].usr_login          );
            clsJs.add("titulo"      , objCdd.trazCampoExcel(jsCdd));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("cidade"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Cidade.php",fd); 
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
            gerarMensagemErro("CDD",retPhp[0].erro,{cabec:"Aviso"});    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
        };          
      };
      /////////////////////////
      //  AJUDA PARA ESTADO  //
      /////////////////////////
      function estFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function estF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtDdd"
                      ,topo:100
                      ,tableBd:"ESTADO"
                      ,fieldCod:"A.EST_CODIGO"
                      ,fieldDes:"A.EST_NOME"
                      ,fieldAtv:"A.EST_ATIVO"
                      ,typeCod :"str" }
        );
      };
      function RetF10tblPad(arr){
        document.getElementById("edtCodEst").value  = arr[0].CODIGO;
        document.getElementById("edtDesEst").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodEst").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codEstBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtDdd"
                                  ,topo:100
                                  ,tableBd:"ESTADO"
                                  ,fieldCod:"A.EST_CODIGO"
                                  ,fieldDes:"A.EST_NOME"
                                  ,fieldAtv:"A.EST_ATIVO"
                                  ,typeCod :"str" }
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "0000"  : jsNmrs(ret[0].CODIGO).emZero(4).ret() );
          document.getElementById("edtDesEst").value  = ( ret.length == 0 ? ""      : ret[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO )         );
        };
      };
      function calcLatLgn(){
        
        var ajax = new XMLHttpRequest();
				var json; 
        var end=jsStr("edtDescricao").lower().ret()+","+jsStr("edtCodEst").lower().ret()+"&format=json";
				
        ajax.onreadystatechange = function() {
          if(ajax.readyState == 4 && ajax.status == 200) {
            json = JSON.parse(ajax.responseText);		
						document.getElementById("edtLatitude").value=jsNmrs(json[0].lat).dec(8).real().ret();
            document.getElementById("edtLongitude").value=jsNmrs(json[0].lon).dec(8).real().ret();
          }
        };
        ajax.open('GET', 
          "https://nominatim.openstreetmap.org/search?q="+end, true);
        ajax.send();
      };
    </script>
  </head>
  <body>
    <div id="divSobreTable" class="divSobreTable">    
      <div class="campotexto campo25" style="margin-top:3px;margin-left:3px;">      
        <select class="campo_input_combo" id="cbCodEst">
        </select>
        <label class="campo_label campo_required" for="cbCodEst">UF</label>
      </div>
      <div class="campo10" style="float:left;margin-left:5px;">            
        <div id="btnFiltrar" onClick="btnFiltrarClick('S');" class="btnImagemEsq bie100 bieAzul bieRight" style="margin-top:3px;"><i class="fa fa-filter"> Filtrar</i></div>
      </div>
      <div class="_campotexto campo100" style="margin-top:1.7em;height:3em;">
        <label class="solicitadoFiltro"></label>
      </div>              
    </div>  
    <div class="divTelaCheia">
      <div id="divRotina" class="conteudo">  
        <div id="divTopoInicio">
        </div>
        <form method="post" 
              name="frmCdd" 
              id="frmCdd" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Cidade<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 160px; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodigo" maxlength="7" type="text" OnKeyPress="return mascaraInteiro(event);" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="20" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodEst"
                                                    onBlur="codEstBlur(this);" 
                                                    onFocus="estFocus(this);" 
                                                    onClick="estF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodEst">UF:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesEst" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesEst">NOME_UF</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input" id="edtDdd" maxlength="4" type="text" OnKeyPress="return mascaraInteiro(event);" />
                <label class="campo_label campo_required" for="edtDdd">DDD</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo edtDireita" id="edtLatitude" type="text" disabled />
                <label class="campo_label campo_required" for="edtLatitude">LATITUDE:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo edtDireita" id="edtLongitude" type="text" disabled />
                <label class="campo_label campo_required" for="edtLongitude">LONGITUDE:</label>
              </div>
              <div class="campo15" style="float:left;height:20px;"> 
                <div onClick="calcLatLgn();" id="btnLatLon" class="btnImagemEsq bie100 bieAzul"><i class="fa fa-map-marker"> LatLng</i></div>              
              </div>
             <div class="campotexto campo10">
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
              <div id="btnConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
              <div id="btnCancelar"  class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
            </div>
          </div>
        </form>
      </div>
      <!-- Importar excel -->
      <div id="divExcel" class="divTopoExcel">
      </div>
      <!-- Fim Importar excel -->
    </div>       
  </body>
</html>