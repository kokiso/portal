<?php
  session_start();
  if( isset($_POST["cnabretorno"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["cnabretorno"]);
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
        /////////////////////////////////////////////
        //    Dados para JavaScript CNABRETORNO    //
        /////////////////////////////////////////////
        if( $rotina=="selectCr" ){
          $sql ="SELECT A.CR_CODBCD";
          $sql.="       ,B.BCD_NOME";
          $sql.="       ,A.CR_CODIGO";          
          $sql.="       ,A.CR_NOME";
          $sql.="       ,CASE WHEN A.CR_EXECUTA='AGE' THEN CAST('AGENDADO' AS VARCHAR(8))";
          $sql.="             WHEN A.CR_EXECUTA='BAI' THEN CAST('BAIXAR' AS VARCHAR(6))";
          $sql.="             WHEN A.CR_EXECUTA='REC' THEN CAST('RECUSADO' AS VARCHAR(8))";
          $sql.="             WHEN A.CR_EXECUTA='IA'  THEN CAST('INSTR ACATADA' AS VARCHAR(13))";
          $sql.="             WHEN A.CR_EXECUTA='INA' THEN CAST('INSTR NAO ACATADA' AS VARCHAR(17))";
          $sql.="             WHEN A.CR_EXECUTA='BM'  THEN CAST('BAIXA MANUAL' AS VARCHAR(12))";
          $sql.="             WHEN A.CR_EXECUTA='PRO' THEN CAST('PROTESTO' AS VARCHAR(8)) END AS CR_EXECUTA";
          $sql.="       ,CASE WHEN A.CR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CR_ATIVO";
          $sql.="       ,CASE WHEN A.CR_REG='P' THEN 'PUB' WHEN A.CR_REG='S' THEN 'SIS' ELSE 'ADM' END AS CR_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.CR_CODUSR";
          $sql.="  FROM CNABRETORNO A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.CR_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN BANCOCODIGO B ON A.CR_CODBCD=B.BCD_CODIGO";
          $sql.="  WHERE (CR_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')";
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
          $vldCampo = new validaCampo("VCNABRETORNO",0);
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
    <title>Cnab Retorno</title>
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
        //////////////////////////////////////////
        //   Objeto clsTable2017 CNABRETORNO    //
        //////////////////////////////////////////
        jsCr={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"CR_CODBCD" 
                      ,"labelCol"       : "BCO"
                      ,"obj"            : "edtCodBcd"
                      ,"pk"             : "S"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "8"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,4]
                      ,"ajudaCampo"     : [  "Codigo do Pagar Tipo. Registro deve existir na tabela Pagar Tipo e tem o formato AA"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "BCD_NOME"   
                      ,"insUpDel"       : ["","N","N"]
                      ,"labelCol"       : "BANCO"
                      ,"obj"            : "edtDesBcd"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["NAO SE APLICA","this","this"]
                      ,"digitosMinMax"  : [1,40]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do Banco para esta CnabRetorno."]
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"CR_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [2,15] 
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do Retorno Cnab conforme IBGE. Este campo é único e deve tem o formato 9.99.99.99.9999"
                                            ,"Campo deve ser utilizado no cadastro de cliente/operacao/motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "CR_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "22em"
                      ,"tamImp"         : "80"
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"ajudaCampo"     : ["Nome do Retorno CNAB."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "CR_EXECUTA"   
                      ,"labelCol"       : "EXECUTA"
                      ,"obj"            : "cbExecuta"
                      ,"tamGrd"         : "7em"
                      ,"tipo"           : "cb"                      
                      ,"tamImp"         : "45"
                      ,"newRecord"      : ["AGE","this","this"]
                      ,"digitosMinMax"  : [2,3]
                      ,"contido"        : ["AGE","BAI","REC","IA","INA","BM","PRO-PROTESTO"]
                      ,"ajudaCampo"     : ["Tipo de Retorno CNAB."]
                      ,"importaExcel"   : "S"
                      ,"truncate"       : "S"                      
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "CR_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"padrao":2}                                        
            ,{"id":7  ,"field"          : "CR_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":8  ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":9  ,"field"          : "CR_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":10 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objCr.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"CNABRETORNO - Detalhe do registro"
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
          ,"div"            : "frmCr"               // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCr"            // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCr"               // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblCr"               // Nome da table
          ,"prefixo"        : "cr"                  // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VCNABRETORNO"        // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPCNABRETORNO"      // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "CR_ATIVO"            // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "CR_REG"              // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "CR_CODUSR"           // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "98em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "CNABRETORNO"         // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "7"                   // Fonte do relatório
          ,"foco"           : ["edtCodBcd"
                              ,"cbDescricao"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objCr.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objCr.AjudaSisAtivo(jsCr);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objCr.detalhe();"]
                               //,["Gerar excel"                            ,"fa-file-excel-o"  ,"objCr.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objCr.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objCr.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objCr.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objCr.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objCr.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "CNABRETORNO[30]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objCr === undefined ){  
          objCr=new clsTable2017("objCr");
        };  
        objCr.montarHtmlCE2017(jsCr); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsCr.form).style.width=jsCr.width;
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
      var objCr;                      // Obrigatório para instanciar o JS TFormaCob
      var jsCr;                       // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS BancoF10
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d30);
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
        clsJs.add("rotina"      , "selectCr"                               );
        clsJs.add("login"       , jsPub[0].usr_login                        );
        clsJs.add("ativo"       , atv                                       );
        clsJs.add("codCr"       , document.getElementById("edtCodBcd").value );    
        fd = new FormData();
        fd.append("cnabretorno" , clsJs.fim());
        msg     = requestPedido("Trac_CnabRetorno.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsCr.registros=objCr.addIdUnico(retPhp[0]["dados"]);
          objCr.ordenaJSon(jsCr.indiceTable,false);  
          objCr.montarBody2017();
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
            clsJs.add("titulo"      , objCr.trazCampoExcel(jsCr));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("cnabretorno"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_CnabRetorno.php",fd); 
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
            gerarMensagemErro("ERR",retPhp[0].erro,{cabec:"Aviso"});    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
        };          
      };
      /////////////////////////////
      //  AJUDA PARA BANCOCODIGO //
      /////////////////////////////
      function bcdFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bcdF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodigo"
                      ,topo:100
                      ,tableBd:"BANCOCODIGO"
                      ,fieldCod:"A.BCD_CODIGO"
                      ,fieldDes:"A.BCD_NOME"
                      ,fieldAtv:"A.BCD_ATIVO"
                      ,typeCod :"str" 
					  ,tbl:"tblBcd"}
        );
      };
      function RetF10tblBcd(arr){
        document.getElementById("edtCodBcd").value  = arr[0].CODIGO;
        document.getElementById("edtDesBcd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodBcd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodBcdBlur(obj){
        //var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        //var elNew = jsNmrs(obj.id).inteiro().ret();
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodigo"
                                  ,topo:100
                                  ,tableBd:"BANCOCODIGO"
                                  ,fieldCod:"A.BCD_CODIGO"
                                  ,fieldDes:"A.BCD_NOME"
                                  ,fieldAtv:"A.BCD_ATIVO"
                                  ,typeCod :"str" 
								  ,tbl:"tblBcd"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "000"  : ret[0].CODIGO             );
          document.getElementById("edtDesBcd").value  = ( ret.length == 0 ? "NAO SE APLICA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "000" : ret[0].CODIGO )  );
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
              name="frmCr" 
              id="frmCr" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Cnab instrução<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 200px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodBcd"
                                                    onBlur="CodBcdBlur(this);" 
                                                    onFocus="bcdFocus(this);" 
                                                    onClick="bcdF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodBcd">BCO:</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input_titulo input" id="edtDesBcd" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesBcd">NOME_BANCO</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo100">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="30" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO</label>
              </div>
              
              <div class="campotexto campo25">
                <select class="campo_input_combo" id="cbExecuta">
                  <option value="AGE">AGENDADO</option>
                  <option value="BAI">BAIXAR</option>
                  <option value="REC">RECUSADO</option>
                  <option value="IA">INSTR ACATADA</option>
                  <option value="INA">INSTR NAO ACATADA</option>
                  <option value="BM">BAIXA MANUAL</option>
                  <option value="PRO">PROTESTO</option>
                </select>
                <label class="campo_label campo_required" for="cbExecuta">AÇÃO</label>
              </div>
             <div class="campotexto campo25">
                <select class="campo_input_combo" id="cbAtivo">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">ATIVO</label>
              </div>
              <div class="campotexto campo25">
                <select class="campo_input_combo" id="cbReg">
                  <option value="P">PUBLICO</option>               
                </select>
                <label class="campo_label campo_required" for="cbReg">REG</label>
              </div>
              <div class="campotexto campo25">
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
      <div id="divExcel" class="divTopoExcel">
      </div>
    </div>       
  </body>
</html>