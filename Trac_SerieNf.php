<?php
  session_start();
  if( isset($_POST["serienf"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["serienf"]);
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
        //    Dados para JavaScript SERIENF       //
        ///////////////////////////////////////////
        if( $rotina=="selectSnf" ){
          $sql="";
          $sql.="SELECT A.SNF_CODIGO";
          $sql.="       ,A.SNF_SERIE";
          $sql.="       ,CASE WHEN A.SNF_ENTSAI='S' THEN 'SAI' ELSE 'ENT' END AS SNF_ENTSAI";
          $sql.="       ,A.SNF_CODTD";
          $sql.="       ,T.TD_NOME";
          $sql.="       ,CASE WHEN A.SNF_INFORMARNF='S' THEN 'SIM' ELSE 'NAO' END AS SNF_INFORMARNF";
          $sql.="       ,A.SNF_NFINICIO";
          $sql.="       ,A.SNF_NFFIM";
          $sql.="       ,A.SNF_MODELO";
          $sql.="       ,CASE WHEN A.SNF_LIVRO='S' THEN 'SIM' ELSE 'NAO' END AS SNF_LIVRO";          
          $sql.="       ,CASE WHEN A.SNF_ENVIO='P' THEN CAST('PREFEITURA' AS VARCHAR(10))";
          $sql.="             WHEN A.SNF_ENVIO='S' THEN CAST('SEFAZ' AS VARCHAR(5))";
          $sql.="             WHEN A.SNF_ENVIO='N' THEN CAST('NENHUM' AS VARCHAR(6)) END AS NFS_ENVIO";          
          $sql.="       ,A.SNF_CODFLL";          
          $sql.="       ,A.SNF_CODEMP";
          $sql.="       ,E.EMP_APELIDO";
          $sql.="       ,A.SNF_IDF";          
          $sql.="       ,CASE WHEN A.SNF_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS SNF_ATIVO";
          $sql.="       ,CASE WHEN A.SNF_REG='P' THEN 'PUB' WHEN A.SNF_REG='S' THEN 'SIS' ELSE 'ADM' END AS SNF_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.SNF_CODUSR";
          $sql.="  FROM SERIENF A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.SNF_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO T ON A.SNF_CODTD=T.TD_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA E ON A.SNF_CODEMP=E.EMP_CODIGO";
          $sql.="  WHERE ((SNF_CODEMP=".$lote[0]->codemp.")";
          $sql.="   AND (TD_SERIENF='".$lote[0]->ps."')";
          $sql.="   AND ((SNF_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
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
          $vldCampo = new validaCampo("VSERIENF",0);
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
    <title>SerieNf</title>
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
        if( jsPub[0].emp_fllunica=="S" ){
          jsCmpAtivo("edtCodFll").remove("campo_input").add("campo_input_titulo").disabled(true);
        };
        //////////////////////////////////////////
        //   Objeto clsTable2017 SERIENF        //
        //////////////////////////////////////////
        jsSnf={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          : "SNF_CODIGO" 
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
                      ,"ajudaCampo"     : [  "Codigo do serienf. Este campo é único e deve tem o formato 9999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"padrao":0}
           ,{"id":2   ,"field"          : "SNF_SERIE"   
                      ,"insUpDel"       : ["S","N","N"]                       
                      ,"labelCol"       : "SERIE"
                      ,"obj"            : "edtSerie"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "12"
                      ,"digitosMinMax"  : [1,4]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|-| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Descrição da serienf."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "SNF_ENTSAI" 
                      ,"insUpDel"       : ["S","N","N"]            
                      ,"labelCol"       : "ES" 
                      ,"obj"            : "cbEntSai"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "10"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["E","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":4  ,"field"          : "SNF_CODTD" 
                      ,"insUpDel"       : ["S","N","N"]            
                      ,"labelCol"       : "TD"
                      ,"obj"            : "edtCodTd"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "12"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"newRecord"      : ["NFS","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Tipo de documento"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "TD_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesTd"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["NF SERVICO","this","this"]
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Nome da ContaContabil."]
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "SNF_INFORMARNF"  
                      ,"labelCol"       : "INFORMARNF" 
                      ,"obj"            : "cbInformarNf"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "20"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["N","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":7  ,"field"          : "SNF_NFINICIO"  
                      ,"labelCol"       : "NFINICIO" 
                      ,"newRecord"      : ["000000","this","this"]
                      ,"insUpDel"       : ["S","N","N"]                      
                      ,"obj"            : "edtNfInicio"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "16"                      
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]                      
                      ,"validar"        : ["intMaiorZero"]
                      ,"digitosMinMax"  : [1,10]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":8  ,"field"          : "SNF_NFFIM"  
                      ,"labelCol"       : "NFFIM" 
                      ,"newRecord"      : ["000000","this","this"]
                      ,"obj"            : "edtNfFim"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "16"                      
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"digitosMinMax"  : [1,10]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":9  ,"field"          : "SNF_MODELO"   
                      ,"labelCol"       : "MODELO"
                      ,"obj"            : "edtModelo"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,5]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"field"         : "SNF_LIVRO"   
                      ,"labelCol"       : "LIVRO"
                      ,"obj"            : "cbLivro"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"tipo"           : "cb"
                      ,"newRecord"      : ["S","this","this"]
                      ,"ajudaCampo"     : ["Direito para opção..."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":11 ,"field"         : "SNF_ENVIO"   
                      ,"labelCol"       : "ENVIO"
                      ,"obj"            : "cbEnvio"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"
                      ,"tipo"           : "cb"
                      ,"newRecord"      : ["N","this","this"]
                      ,"ajudaCampo"     : ["Direito para opção..."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":12 ,"field"          : "SNF_CODFLL" 
                      ,"labelCol"       : "FILIAL"  
                      ,"obj"            : "edtCodFll"  
                      ,"newRecord"      : [jsPub[0].emp_codfll,"this","this"]                      
                      ,"tamGrd"         : "4em"                      
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"inputDisabled"  : (jsPub[0].emp_fllunica=="S" ? true : false )
                      ,"padrao":7}					  
            ,{"id":13 ,"field"          : "SNF_CODEMP" 
                      ,"pk"             : "S"
                      ,"labelCol"       : "CODEMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"padrao":7}					  
            ,{"id":14 ,"field"          : "EMP_APELIDO"   
                      ,"labelCol"       : "EMPRESA"
                      ,"obj"            : "edtDesEmp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [jsPub[0].emp_apelido,"this","this"]
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "SNF_IDF"   
                      ,"labelCol"       : "IDF"
                      ,"obj"            : "edtIdf"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "25"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":16 ,"field"          : "SNF_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":17 ,"field"          : "SNF_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":18 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":19 ,"field"          : "SNF_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":20 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objSnf.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"SERIENF - Detalhe do registro"
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
          ,"div"            : "frmSnf"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaSnf"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmSnf"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblSnf"              // Nome da table
          ,"prefixo"        : "snf"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VSERIENF"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPSERIENF"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "SNF_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "SNF_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "SNF_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "106em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "SERIENF"             // Titulo do relatório
          ,"relOrientacao"  : "R"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtSerie"
                              ,"cbInformarNf"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objSnf.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objSnf.AjudaSisAtivo(jsSnf);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objSnf.detalhe();"]
                               //,["Gerar excel"                          ,"fa-file-excel-o"  ,"objSnf.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objSnf.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objSnf.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objSnf.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objSnf.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objSnf.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "SERIENF[22]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objSnf === undefined ){  
          objSnf=new clsTable2017("objSnf");
        };  
        objSnf.montarHtmlCE2017(jsSnf); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsSnf.form).style.width=jsSnf.width;
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"SERIE"      ,"labelCol":"SERIE"       ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":1  ,"field":"ES"         ,"labelCol":"ENT_SAI"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":2  ,"field":"CODTD"      ,"labelCol":"TIPO_DOCTO"  ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":3  ,"field":"INFORMARNF" ,"labelCol":"IFORMAR_NF"  ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":4  ,"field":"NFINICIO"   ,"labelCol":"NF_INICIO"   ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":5  ,"field":"NFFIM"      ,"labelCol":"NF_FIM"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":6  ,"field":"IDF"        ,"labelCol":"NUM_IDF"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":7  ,"field":"MODELO"     ,"labelCol":"MODELO"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":8  ,"field":"ERRO"       ,"labelCol":"ERRO"        ,"tamGrd":"45em"    ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[8].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
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
      var objSnf;                     // Obrigatório para instanciar o JS TFormaCob
      var jsSnf;                      // Obj principal da classe clsTable2017
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
      var intCodDir = parseInt( jsPub[0].usr_d22 );
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
        clsJs.add("rotina"      , "selectSnf"                       );
        clsJs.add("login"       , jsPub[0].usr_login                );
        clsJs.add("ativo"       , atv                               );
        clsJs.add("codemp"      , jsPub[0].emp_codigo               );
        clsJs.add("ps"          , localStorage.getItem("prodServ")  );
        fd = new FormData();
        fd.append("serienf" , clsJs.fim());
        msg     = requestPedido("Trac_SerieNf.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsSnf.registros=objSnf.addIdUnico(retPhp[0]["dados"]);
          objSnf.ordenaJSon(jsSnf.indiceTable,false);  
          objSnf.montarBody2017();
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
            clsJs.add("titulo"      , objSnf.trazCampoExcel(jsSnf));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("serienf"   , envPhp              );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_SerieNf.php",fd); 
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
            gerarMensagemErro("SNF",retPhp[0].erro,"AVISO");    
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
                      ,foco:"cbInformarNf"
                      ,topo:100
                      ,tableBd:"TIPODOCUMENTO"
                      ,fieldCod:"A.TD_CODIGO"
                      ,fieldDes:"A.TD_NOME"
                      ,fieldAtv:"A.TD_ATIVO"
                      ,typeCod :"str"
                      ,where:" AND A.TD_SERIENF = '" +localStorage.getItem("prodServ")+"'"                      
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
                                  ,foco:"cbInformarNf"
                                  ,topo:100
                                  ,tableBd:"TIPODOCUMENTO"
                                  ,fieldCod:"A.TD_CODIGO"
                                  ,fieldDes:"A.TD_NOME"
                                  ,fieldAtv:"A.TD_ATIVO"
                                  ,typeCod :"str" 
                                  ,where:" AND A.TD_SERIENF = '" +localStorage.getItem("prodServ")+"'"                                  
                                  ,tbl:"tblTd"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "FAT"  : ret[0].CODIGO             );
          document.getElementById("edtDesTd").value  = ( ret.length == 0 ? "FATURA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "FAT" : ret[0].CODIGO )  );
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
              name="frmSnf" 
              id="frmSnf" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Serie NF<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 180px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input" id="edtSerie" type="text" maxlength="4" />
                <label class="campo_label campo_required" for="edtSerie">SERIE:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" name="cbEntSai" id="cbEntSai">
                  <option value="E">ENT</option>
                  <option value="S">SAI</option>
                </select>
                <label class="campo_label campo_required" for="cbEntSai">ENTRADA/SAIDA:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodTd"
                                                    onBlur="CodTdBlur(this);" 
                                                    onFocus="tdFocus(this);" 
                                                    onClick="tdF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="6"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodTd">TIPO DOC:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input_titulo input" id="edtDesTd" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesTd">NOME_TIPODOCUMENTO:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" name="cbInformarNf" id="cbInformarNf">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbInformarNf">INFORMAR NF:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input_titulo input" id="edtNfInicio" 
                                                        OnKeyPress="return mascaraInteiro(event);"
                                                        type="text" 
                                                        maxlength="6" />
                <label class="campo_label campo_required" for="edtNfInicio">NF INICIO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input_titulo input" id="edtNfFim" 
                                                        OnKeyPress="return mascaraInteiro(event);"
                                                        type="text" 
                                                        maxlength="6" />
                <label class="campo_label campo_required" for="edtNfFim">NF FIM:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtIdf" type="text" maxlength="20" />
                <label class="campo_label campo_required" for="edtIdf">IDF:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input" id="edtModelo" type="text" maxlength="5" />
                <label class="campo_label campo_required" for="edtModelo">MODELO:</label>
              </div>
              
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbLivro">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbLivro">LIVRO FISCAL:</label>
              </div>
              
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbEnvio">
                  <option value="P">PREFEITURA</option>
                  <option value="S">SEFAZ</option>
                  <option value="N">NENHUM</option>
                </select>
                <label class="campo_label campo_required" for="cbEnvio">ENVIO:</label>
              </div>
              
              <div class="campotexto campo10">
                <input class="campo_input input" id="edtCodFll" 
                                                 OnKeyPress="return mascaraInteiro(event);" 
                                                 type="text" 
                                                 maxlength="4"/>
                <label class="campo_label campo_required" for="edtCodFll">FILIAL:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo input" id="edtDesEmp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesEmp">EMPRESA:</label>
              </div>
             <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbAtivo">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">ATIVO</label>
              </div>
              <div class="campotexto campo15">
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
                <input id="edtCodEmp" type="text" />
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