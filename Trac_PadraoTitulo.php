<?php
  session_start();
  if( isset($_POST["padraotitulo"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["padraotitulo"]);
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
        ///////////////////////////////////////////
        //    Dados para JavaScript PADRAOTITULO //
        ///////////////////////////////////////////
        if( $rotina=="selectPt" ){
          $sql="";
          $sql.="SELECT PT_CODIGO";
          $sql.="       ,PT_NOME";
          $sql.="       ,PT_CODTD";
          $sql.="       ,TD_NOME";
          $sql.="       ,PT_CODFC";
          $sql.="       ,FC_NOME";
          $sql.="       ,CASE WHEN A.PT_DEBCRE='D' THEN 'DEB' ELSE 'CRE' END AS PT_DEBCRE";
          $sql.="       ,PT_CODCC";
          $sql.="       ,CC_NOME";
          $sql.="       ,PT_CODPDR";
          $sql.="       ,PDR_NOME";
          $sql.="       ,CASE WHEN A.PT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PT_ATIVO";
          $sql.="       ,CASE WHEN A.PT_REG='P' THEN 'PUB' WHEN A.PT_REG='S' THEN 'SIS' ELSE 'ADM' END AS PT_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.PT_CODUSR";
          $sql.="  FROM PADRAOTITULO A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.PT_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO TD ON A.PT_CODTD=TD.TD_CODIGO";
          $sql.="  LEFT OUTER JOIN FORMACOBRANCA FC ON A.PT_CODFC=FC.FC_CODIGO";
          $sql.="  LEFT OUTER JOIN CONTACONTABIL CC ON A.PT_CODCC=CC.CC_CODIGO";
          $sql.="  LEFT OUTER JOIN PADRAO PDR ON A.PT_CODPDR=PDR.PDR_CODIGO";
          $sql.="  WHERE ((PT_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."'))";                 
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
          $vldCampo = new validaCampo("VPADRAOTITULO",0);
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
        // Atualizando o padraotitulo de dados se opcao de insert/updade/delete //
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
    <title>Padrao Titulo</title>
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
        ////////////////////////////////////////
        //   Objeto clsTable2017 PADRAOTITULO //
        ////////////////////////////////////////
        jsPt={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"PT_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                      
                      ,"pk"             : "S"
                      ,"autoIncremento" : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do PadraoTitulo. Este campo é único e deve tem o formato 9999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "PT_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "28em"
                      ,"tamImp"         : "115"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.|-|,| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Descricao do PadraoTitulo."]
                      ,"importaExcel"   : "S"
                      ,"truncate"       : "S"                      
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"PT_CODTD" 
                      ,"labelCol"       : "TD"
                      ,"obj"            : "edtCodTd"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Código do Tipo de Documento."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "TD_NOME"   
                      ,"labelCol"       : "DOC"
                      ,"obj"            : "edtDesTd"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.|-| "
                      ,"ajudaCampo"     : ["Nome do Tipo de Documento"]
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "PT_CODFC" 
                      ,"labelCol"       : "FC"
                      ,"obj"            : "edtCodFc"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"pk"             : "N"            
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Código da Forma de Cobrança."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "FC_NOME"   
                      ,"labelCol"       : "FC"
                      ,"obj"            : "edtDesFc"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.|-| "
                      ,"ajudaCampo"     : ["Nome da Forma de Cobrança"]
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "PT_DEBCRE"  
                      ,"labelCol"       : "DC" 
                      ,"obj"            : "cbDebCre"
                      ,"pk"             : "N"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"contido"        : ["C","D"]
                      ,"tamImp"         : "10"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["I","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,10]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":8  ,"field"          : "PT_CODCC" 
                      ,"labelCol"       : "CONTABIL"
                      ,"obj"            : "edtCodCc"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"pk"             : "N"            
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Código da ContaContabil."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "CC_NOME"   
                      ,"labelCol"       : "CC"
                      ,"obj"            : "edtDesCc"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"ajudaCampo"     : ["Nome da ContaContabil."]
                      ,"padrao":0}
            ,{"id":10 ,"field"          :"PT_CODPDR" 
                      ,"labelCol"       : "CODPDR"
                      ,"obj"            : "edtCodPdr"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"pk"             : "N"            
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i2"]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["00","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,2]
                      ,"ajudaCampo"     : [ "Código da Padrao."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":11 ,"field"          : "PDR_NOME"   
                      ,"labelCol"       : "PADRAO"
                      ,"obj"            : "edtDesPdr"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.|-| "
                      ,"ajudaCampo"     : ["Nome da Padrao."]
                      ,"truncate"       : "S"
                      ,"padrao":0}
            ,{"id":12 ,"field"          : "PT_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":13 ,"field"          : "PT_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":14 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":15 ,"field"          : "PT_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":16 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objPt.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"PADRAOTITULO - Detalhe do registro"
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
          ,"div"            : "frmPt"               // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaPt"            // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmPt"               // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblPt"               // Nome da table
          ,"prefixo"        : "pt"                  // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VPADRAOTITULO"       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPPADRAOTITULO"     // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "PT_ATIVO"            // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "PT_REG"              // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "PT_CODUSR"           // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "110em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "PADRAOTITULO"        // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtDescricao"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objPt.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objPt.AjudaSisAtivo(jsPt);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objPt.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objPt.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objPt.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objPt.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objPt.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objPt.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "PADRAOTITULO[10]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objPt === undefined ){  
          objPt=new clsTable2017("objPt");
        };  
        objPt.montarHtmlCE2017(jsPt); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsPt.form).style.width="100em";
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"DESCRICAO"  ,"labelCol":"DESCRICAO"       ,"tamGrd":"32em"    ,"tamImp":"100"}
            ,{"id":1  ,"field":"TD"         ,"labelCol":"TIPO_DOCTO"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":2  ,"field":"FC"         ,"labelCol":"FORMA_COBRANCA"  ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":3  ,"field":"DC"         ,"labelCol":"DEB_CRE"         ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":4  ,"field":"CONTABIL"   ,"labelCol":"CONTABIL"        ,"tamGrd":"12em"    ,"tamImp":"20"}
            ,{"id":5  ,"field":"CODPDR"     ,"labelCol":"COD_PADRAO"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":6  ,"field":"ERRO"       ,"labelCol":"ERRO"            ,"tamGrd":"45em"    ,"tamImp":"100"}            
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[6].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
      var objPt;                     // Obrigatório para instanciar o JS TFormaCob
      var jsPt;                      // Obj principal da classe clsTable2017
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
      var intCodDir = parseInt(jsPub[0].usr_d10);
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
        clsJs.add("rotina"      , "selectPt"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("padraotitulo" , clsJs.fim());
        msg     = requestPedido("Trac_PadraoTitulo.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsPt.registros=objPt.addIdUnico(retPhp[0]["dados"]);
          objPt.ordenaJSon(jsPt.indiceTable,false);  
          objPt.montarBody2017();
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
            clsJs.add("titulo"      , objPt.trazCampoExcel(jsPt));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("padraotitulo"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_PadraoTitulo.php",fd); 
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
            gerarMensagemErro("PT",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
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
                      ,tbl:"tblTd"}
        );
      };
      function RetF10tblTd(arr){
        document.getElementById("edtCodTd").value  = arr[0].CODIGO;
        document.getElementById("edtDesTd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodTd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodTdBlur(obj){
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
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "FAT"  : ret[0].CODIGO             );
          document.getElementById("edtDesTd").value  = ( ret.length == 0 ? "FATURA"      : ret[0].DESCRICAO  );
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
                      ,foco:"cbDebCre"
                      ,topo:100
                      ,tableBd:"FORMACOBRANCA"
                      ,fieldCod:"A.FC_CODIGO"
                      ,fieldDes:"A.FC_NOME"
                      ,fieldAtv:"A.FC_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblFc"}
        );
      };
      function RetF10tblFc(arr){
        document.getElementById("edtCodFc").value  = arr[0].CODIGO;
        document.getElementById("edtDesFc").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFc").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodFcBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbDebCre"
                                  ,topo:100
                                  ,tableBd:"FORMACOBRANCA"
                                  ,fieldCod:"A.FC_CODIGO"
                                  ,fieldDes:"A.FC_NOME"
                                  ,fieldAtv:"A.FC_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblFc"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "BOL"  : ret[0].CODIGO             );
          document.getElementById("edtDesFc").value  = ( ret.length == 0 ? "BOLETO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "BOL" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //  AJUDA PARA CONTACONTABIL //
      ///////////////////////////////
      function ccFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ccF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod       : obj.id
                      ,foco         : "edtCodPdr"
                      ,topo         : 100
                      ,tableBd      : "CONTACONTABIL"
                      ,fieldCod     : "A.CC_CODIGO"
                      ,fieldDes     : "A.CC_NOME"
                      ,fieldAtv     : "A.CC_ATIVO"
                      ,typeCod      : "str" 
                      ,tamColCodigo : "10em"
                      ,tamColNome   : "26em"
                      ,where        : " AND A.CC_F10= 'S'"
                      ,tbl          : "tblCc"
                    }
        );
      };
      function RetF10tblCc(arr){
        document.getElementById("edtCodCc").value  = arr[0].CODIGO;
        document.getElementById("edtDesCc").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCc").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCcBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodPdr"
                                  ,topo:100
                                  ,tableBd:"CONTACONTABIL"
                                  ,fieldCod:"A.CC_CODIGO"
                                  ,fieldDes:"A.CC_NOME"
                                  ,fieldAtv:"A.CC_ATIVO"
                                  ,typeCod :"str"
                                  ,where:" AND A.CC_F10= 'S'"                                  
                                  ,tbl:"tblCc"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "1.01.01.02.0001"  : ret[0].CODIGO             );
          document.getElementById("edtDesCc").value  = ( ret.length == 0 ? "BANCO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "1.01.01.02.0001" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////
      //  AJUDA PARA PADRAO //
      ////////////////////////
      function pdrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function pdrF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"cbAtivo"
                      ,topo:100
                      ,tableBd:"PADRAO"
                      ,fieldCod:"A.PDR_CODIGO"
                      ,fieldDes:"A.PDR_NOME"
                      ,fieldAtv:"A.PDR_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblPdr"}
        );
      };
      function RetF10tblPdr(arr){
        document.getElementById("edtCodPdr").value  = arr[0].CODIGO;
        document.getElementById("edtDesPdr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodPdr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodPdrBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbAtivo"
                                  ,topo:100
                                  ,tableBd:"PADRAO"
                                  ,fieldCod:"A.PDR_CODIGO"
                                  ,fieldDes:"A.PDR_NOME"
                                  ,fieldAtv:"A.PDR_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblPdr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "12"  : ret[0].CODIGO             );
          document.getElementById("edtDesPdr").value  = ( ret.length == 0 ? "OUTRAS"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "12" : ret[0].CODIGO )  );
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
              name="frmPt" 
              id="frmPt" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Padrão-titulo<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>
          <div style="height: 200px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodTd"
                                                    onBlur="CodTdBlur(this);" 
                                                    onFocus="tdFocus(this);" 
                                                    onClick="tdF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodTd">TD:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input_titulo input" id="edtDesTd" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesTd">TIPO DOCTO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodFc"
                                                    onBlur="CodFcBlur(this);" 
                                                    onFocus="fcFocus(this);" 
                                                    onClick="fcF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodFc">FC:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesFc" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesFc">FORMA COBR:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" name="cbDebCre" id="cbDebCre">
                  <option value="C">CRE</option>
                  <option value="D">DEB</option>
                  <option value="I">INFORME</option>
                </select>
                <label class="campo_label campo_required" for="cbRetido">DEBCRE:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodCc"
                                                    onBlur="CodCcBlur(this);" 
                                                    onFocus="ccFocus(this);" 
                                                    onClick="ccF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="7"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCc">CONTABIL:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesCc" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCc">NOME_CONTABIL:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodPdr"
                                                    onBlur="CodPdrBlur(this);" 
                                                    onFocus="pdrFocus(this);" 
                                                    onClick="pdrF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="7"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodPdr">PADRAO:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesPdr" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesPdr">NOME_PADRAO:</label>
              </div>
             <div class="campotexto campo20">
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
              <div class="campotexto campo20">
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
      <div id="divExcel" class="divTopoExcel">
      </div>
    </div>       
  </body>
</html>