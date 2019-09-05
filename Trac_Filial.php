<?php
  session_start();
  if( isset($_POST["filial"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/consultaCep.class.php");            
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["filial"]);
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
        ///////////////////////////////////////////////
        // Buscando CEP para complemento de cadastro //
        ///////////////////////////////////////////////
        if( $rotina=="rotinaCep" ){
          $clsCep  = new consultaCep();
          $retorno=$clsCep->buscaCep($lote[0]->cep);
        };
        /////////////////////////////////////////
        //    Dados para JavaScript FILIAL     //
        /////////////////////////////////////////
        if( $rotina=="selectFll" ){
          $sql="";
          $sql.="SELECT A.FLL_CODIGO";
          $sql.="       ,A.FLL_NOME";
          $sql.="       ,A.FLL_APELIDO";
          $sql.="       ,A.FLL_BAIRRO";
          $sql.="       ,A.FLL_CEP";
          $sql.="       ,A.FLL_CNPJ";
          $sql.="       ,A.FLL_CODCDD";
          $sql.="       ,C.CDD_NOME";
          $sql.="       ,A.FLL_CODLGR";
          $sql.="       ,A.FLL_ENDERECO";
          $sql.="       ,A.FLL_NUMERO";
          $sql.="       ,A.FLL_FONE";
          $sql.="       ,A.FLL_INSESTAD";
          $sql.="       ,A.FLL_INSMUNIC";
          $sql.="       ,A.FLL_CODEMP";
          $sql.="       ,E.EMP_APELIDO";
          $sql.="       ,CASE WHEN A.FLL_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS FLL_ATIVO";
          $sql.="       ,CASE WHEN A.FLL_REG='P' THEN 'PUB' WHEN A.FLL_REG='S' THEN 'SIS' ELSE 'ADM' END AS FLL_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.FLL_CODUSR";
          $sql.="  FROM FILIAL A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.FLL_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN CIDADE C ON A.FLL_CODCDD=C.CDD_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA E ON A.FLL_CODEMP=E.EMP_CODIGO";
          $sql.=" WHERE ((FLL_CODEMP=".$lote[0]->codemp.")";
          $sql.="   AND ((FLL_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        /*
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
          $vldCampo = new validaCampo("VFILIAL",0);
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
          if( $atuBd==false )
            $retorno='[{"retorno":"OK","dados":'.json_encode($data).',"erro":"ERRO(s) ENCONTRADOS"}]';     
        };  
        */
        ///////////////////////////////////////////////////////////////////
        // Atualizando o filial de dados se opcao de insert/updade/delete //
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
    <title>Filial</title>
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
        //////////////////////////////////////
        //   Objeto clsTable2017 FILIAL     //
        //////////////////////////////////////
        jsFll={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"FLL_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"digitosMinMax"  : [1,4] 
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do filial. Este campo é único e deve tem o formato 999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "FLL_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,40]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Descrição do filial."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "FLL_APELIDO"   
                      ,"labelCol"       : "APELIDO"
                      ,"obj"            : "edtApelido"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Descrição do apelido do filial."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "FLL_BAIRRO"   
                      ,"labelCol"       : "BAIRRO"
                      ,"obj"            : "edtBairro"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                                            
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "FLL_CEP"   
                      ,"labelCol"       : "CEP"
                      ,"obj"            : "edtCep"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [8,8]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":6  ,"field"          :"FLL_CNPJ" 
                      ,"labelCol"       : "CNPJ"
                      ,"obj"            : "edtCnpj"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "11em"
                      ,"tamImp"         : "25"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,14]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":7  ,"field"          :"FLL_CODCDD" 
                      ,"labelCol"       : "CODCDD"
                      ,"obj"            : "edtCodCdd"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,7]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "CDD_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesCdd"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,30]
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":9 ,"field"          : "FLL_CODLGR" 
                      ,"labelCol"       : "CODLGR"
                      ,"obj"            : "edtCodLgr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["RUA","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                                            
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "FLL_ENDERECO"   
                      ,"labelCol"       : "ENDERECO"
                      ,"obj"            : "edtEndereco"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":11 ,"field"          : "FLL_NUMERO"   
                      ,"labelCol"       : "NUMERO"
                      ,"obj"            : "edtNumero"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [1,10]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":12 ,"field"          : "FLL_FONE"   
                      ,"labelCol"       : "FONE"
                      ,"obj"            : "edtFone"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "15"
                      ,"digitosMinMax"  : [3,10]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"field"          : "FLL_INSESTAD"   
                      ,"labelCol"       : "INS"
                      ,"obj"            : "edtInsEstad"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [0,19]
                      ,"validar"        : ["podeNull"]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|N|S|A"
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"field"          : "FLL_INSMUNIC"   
                      ,"labelCol"       : "INSMUNIC"
                      ,"obj"            : "edtInsMunic"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [0,20]
                      ,"validar"        : ["podeNull"]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|N|S|A"
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "FLL_CODEMP" 
                      ,"pk"             : "S"
                      ,"labelCol"       : "CODEMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"padrao":7}  
            ,{"id":16  ,"field"         : "EMP_APELIDO"   
                      ,"labelCol"       : "EMPRESA"
                      ,"obj"            : "edtDesEmp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [jsPub[0].emp_apelido,"this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Nome da empresa."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":17 ,"field"          : "FLL_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":18 ,"field"          : "FLL_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":19 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":20 ,"field"          : "FLL_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":22 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objFll.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"FILIAL - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar" ,"name":"horCadastrar"  ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-plus"             }
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
          ,"div"            : "frmFll"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaFll"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmFll"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblFll"              // Nome da table
          ,"prefixo"        : "fll"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VFILIAL"             // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPFILIAL"           // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "FLL_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "FLL_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "FLL_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "97em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "FILIAL"              // Titulo do relatório
          ,"relOrientacao"  : "R"                   // Paisagem ou retrato
          ,"relFonte"       : "7"                   // Fonte do relatório
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
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objFll.AjudaSisAtivo(jsFll);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objFll.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objFll.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objFll.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objFll.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objFll.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objFll.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "FILIAL[03]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objFll === undefined ){  
          objFll=new clsTable2017("objFll");
        };  
        objFll.montarHtmlCE2017(jsFll); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsFll,["CODIGO"]);
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsFll.form).style.width=jsFll.width;
        btnFiltrarClick("S");  
      });
      //
      var objFll;                     // Obrigatório para instanciar o JS TFormaCob
      var jsFll;                      // Obj principal da classe clsTable2017
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS BancoF10
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario                  
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d03);
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
        clsJs.add("rotina"      , "selectFll"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("filial" , clsJs.fim());
        msg     = requestPedido("Trac_Filial.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsFll.registros=objFll.addIdUnico(retPhp[0]["dados"]);
          objFll.ordenaJSon(jsFll.indiceTable,false);  
          objFll.montarBody2017();
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
            clsJs.add("titulo"      , objFll.trazCampoExcel(jsFll));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("filial"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Filial.php",fd); 
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
            gerarMensagemErro("FLL",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
        };          
      };
      function horCadastrarClick(){
        try{
          let codFll=0;
          let tbl = tblFll.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          if( nl>0 ){
            for(let lin=0 ; (lin<nl) ; lin++){  
              if( jsNmrs(tbl.rows[lin].cells[objCol.CODIGO].innerHTML).inteiro().ret()>codFll )
                codFll=jsNmrs(tbl.rows[lin].cells[objCol.CODIGO].innerHTML).inteiro().ret();
            };
            codFll++;
          };
          jsFll.titulo[objCol.CODIGO].newRecord[0]=codFll.toString();
          objFll.cadastrar(0);
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
        };          
      };
      ////////////////////////////
      //  AJUDA PARA CIDADE     //
      ////////////////////////////
      function cddFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function cddF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodLgr"
                      ,topo:100
                      ,tableBd:"CIDADE"
                      ,fieldCod:"A.CDD_CODIGO"
                      ,fieldDes:"A.CDD_NOME"
                      ,fieldAtv:"A.CDD_ATIVO"
                      ,typeCod :"str" 
					  ,tbl:"tblCdd"}
        );
      };
      function RetF10tblCdd(arr){
        document.getElementById("edtCodCdd").value  = arr[0].CODIGO;
        document.getElementById("edtDesCdd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCdd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCddBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.value;
        //var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        //var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodLgr"
                                  ,topo:100
                                  ,tableBd:"CIDADE"
                                  ,fieldCod:"A.CDD_CODIGO"
                                  ,fieldDes:"A.CDD_NOME"
                                  ,fieldAtv:"A.CDD_ATIVO"
                                  ,typeCod :"str" 
								  ,tbl:"tblCdd"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "3550308"  : ret[0].CODIGO             );
          document.getElementById("edtDesCdd").value  = ( ret.length == 0 ? "SAO PAULO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "3550308" : ret[0].CODIGO )  );
        };
      };
      //////////////////////////////
      //  AJUDA PARA LOGRADOURO  //
      //////////////////////////////
      function lgrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function lgrF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtEndereco"
                      ,topo:100
                      ,tableBd:"LOGRADOURO"
                      ,fieldCod:"A.LGR_CODIGO"
                      ,fieldDes:"A.LGR_NOME"
                      ,fieldAtv:"A.LGR_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblLgr"}
        );
      };
      function RetF10tblLgr(arr){
        document.getElementById("edtCodLgr").value  = arr[0].CODIGO;
        document.getElementById("edtDesLgr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodLgr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodLgrBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtEndereco"
                                  ,topo:100
                                  ,tableBd:"LOGRADOURO"
                                  ,fieldCod:"A.LGR_CODIGO"
                                  ,fieldDes:"A.LGR_NOME"
                                  ,fieldAtv:"A.LGR_ATIVO"
                                  ,typeCod :"str" 
								  ,tbl:"tblLgr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "RUA"  : ret[0].CODIGO             );
          document.getElementById("edtDesLgr").value  = ( ret.length == 0 ? "RUA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "RUA" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////
      //  AJUDA PARA EMPRESA    //
      ////////////////////////////
      function empFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function empF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtDescricao"
                      ,topo:100
                      ,tableBd:"EMPRESA"
                      ,fieldCod:"A.EMP_CODIGO"
                      ,fieldDes:"A.EMP_APELIDO"
                      ,fieldAtv:"A.EMP_ATIVO"
                      ,typeCod :"int" 
					  ,tbl:"tblEmp"}
        );
      };
      function RetF10tblEmp(arr){
        document.getElementById("edtCodEmp").value  = arr[0].CODIGO;
        document.getElementById("edtDesEmp").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodEmp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodEmpBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtDescricao"
                                  ,topo:100
                                  ,tableBd:"EMPRESA"
                                  ,fieldCod:"A.EMP_CODIGO"
                                  ,fieldDes:"A.EMP_APELIDO"
                                  ,fieldAtv:"A.EMP_ATIVO"
                                  ,typeCod :"int" 
								  ,tbl:"tblEmp"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "1"  : ret[0].CODIGO             );
          document.getElementById("edtDesEmp").value  = ( ret.length == 0 ? "TOTALTRAC"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "1" : ret[0].CODIGO )  );
        };
      };
      ////////////////////
      // On exit do CEP //
      ////////////////////
      function cepFocus(obj){
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value);         
      };
      function cepBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = document.getElementById(obj.id).value;
        if( elOld != elNew ){
          clsJs   = jsString("lote");
          clsJs.add("rotina"  , "rotinaCep"         );
          clsJs.add("cep"     , obj.value           );
          clsJs.add("login"   , jsPub[0].usr_login  );
          fd = new FormData();
          fd.append("empresa" , clsJs.fim()); 
          msg = requestPedido("Trac_Empresa.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            retPhp=retPhp[0].dados[0];
            for (var key in retPhp ) {
              switch( key ){
                case "bairro"   : document.getElementById("edtBairro").value=retPhp[key];break;
                case "cidade"   : document.getElementById("edtDesCdd").value=retPhp[key];break;
                case "codcdd"   : 
                  document.getElementById("edtCodCdd").value=retPhp[key];
                  document.getElementById("edtCodCdd").setAttribute("data-oldvalue",retPhp[key]);
                  break;
                case "endereco" : document.getElementById("edtEndereco").value=retPhp[key];break;
                case "codtl"    : 
                  document.getElementById("edtCodLgr").value=retPhp[key];
                  document.getElementById("edtCodLgr").setAttribute("data-oldvalue",retPhp[key]);
                  break;
              };  
            };
          };  
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
              name="frmFll" 
              id="frmFll" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Filial<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 240px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="40" />
                <label class="campo_label campo_required" for="edtDescricao">NOME:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtApelido" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtApelido">APELIDO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCnpj" 
                                           type="text" 
                                           OnKeyPress="return mascaraInteiro(event);" 
                                           maxlength="14" />
                <label class="campo_label campo_required" for="edtCnpj">CNPJ:</label>
              </div>
              
              <div class="campotexto campo10">
                <input class="campo_input"  name="edtCep" id="edtCep" type="text" 
                       onFocus="cepFocus(this);" 
                       onBlur="cepBlur(this);"
                       OnKeyPress="return mascaraInteiro(event);" 
                       maxlength="8" />
                <label class="campo_label campo_required" for="edtCep">CEP:</label>
              </div>
              
              <div class="campotexto campo25">
                <input class="campo_input" id="edtBairro" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtBairro">BAIRRO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodCdd"
                                                    onBlur="CodCddBlur(this);" 
                                                    onFocus="cddFocus(this);" 
                                                    onClick="cddF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="7"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCdd">CODCDD:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesCdd" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCdd">CIDADE:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodLgr"
                                                    onBlur="CodLgrBlur(this);" 
                                                    onFocus="lgrFocus(this);" 
                                                    onClick="lgrF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="5"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodLgr">LOGRAD:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtEndereco" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtEndereco">ENDERECO:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtNumero" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtNumero">NUMERO:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtFone" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtFone">FONE:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtInsEstad" 
                                           type="text" 
                                           OnKeyPress="return mascaraInteiro(event);" 
                                           maxlength="19" />
                <label class="campo_label" for="edtInsEstad">INSESTAD:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtInsMunic" 
                                           type="text" 
                                           OnKeyPress="return mascaraInteiro(event);"                                            
                                           maxlength="20" />
                <label class="campo_label" for="edtInsMunic">INSMUIC:</label>
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
              <div class="campotexto campo25">
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