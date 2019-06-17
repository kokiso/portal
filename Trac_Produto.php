<?php
  session_start();
  if( isset($_POST["produto"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["produto"]);
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
        //    Dados para JavaScript PRODUTO      //
        ///////////////////////////////////////////
        if( $rotina=="selectPrd" ){
          $sql="SELECT 
                    PRD_CODIGO
                    ,PRD_NOME
                    ,PRD_CODNCM
                    ,NCM_NOME
                    ,CASE WHEN PRD_ST='S' THEN 'SIM' ELSE 'NAO' END PRD_ST
                    ,PRD_ALIQICMS
                    ,PRD_REDUCAOBC
                    ,CASE WHEN PRD_IPI='S' THEN 'SIM' ELSE 'NAO' END PRD_IPI
                    ,PRD_ALIQIPI
                    ,PRD_CSTIPI
                    ,IPI_NOME
                    ,PRD_CODEMB
                    ,EMB_NOME
                    ,PRD_VLRVENDA
                    ,PRD_CODPO
                    ,PO_NOME
                    ,PRD_CODBARRAS
                    ,PRD_PESOBRUTO
                    ,PRD_PESOLIQUIDO
                    ,CONVERT(VARCHAR(10),PRD_DTCADASTRO,127) AS PRD_DTCADASTRO
                    ,CASE WHEN PRD_ATIVO='S' THEN 'SIM' ELSE 'NAO' END PRD_ATIVO
                    ,CASE WHEN PRD_REG='P' THEN 'PUB' WHEN PRD_REG='S' THEN 'SIS' ELSE 'ADM' END PRD_REG
                    ,PRD_CODEMP
                    ,EMP_APELIDO
                    ,US_APELIDO
                    ,PRD_CODUSR
                  FROM PRODUTO
                  LEFT OUTER JOIN EMPRESA ON PRD_CODEMP=EMP_CODIGO
                  LEFT OUTER JOIN USUARIOSISTEMA ON PRD_CODUSR=US_CODIGO
                  LEFT OUTER JOIN EMBALAGEM ON EMB_CODIGO=PRD_CODEMB
                  LEFT OUTER JOIN PRODUTOORIGEM ON PO_CODIGO=PRD_CODPO
                  LEFT OUTER JOIN NCM ON NCM_CODIGO=PRD_CODNCM
                  LEFT OUTER JOIN CSTIPI ON IPI_CODIGO=PRD_CSTIPI
                  WHERE ((PRD_CODEMP=".$lote[0]->codemp.")
                  AND ((PRD_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
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
          $vldCampo = new validaCampo("VPRODUTO",0);
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
  } else {
    require("classPhp/conectaSqlServer.class.php");
    $classe   = new conectaBd();
    $classe->conecta($_SESSION['login']);
  }
?>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <title>Produto</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
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
        var hoje = new Date();
        var dia = (hoje.getDate()<10?"0":"")+hoje.getDate();
        var mes = ((hoje.getMonth()+1)<10?"0":"")+(hoje.getMonth()+1);
        hoje = dia+"/"+mes+"/"+hoje.getFullYear();
        //////////////////////////////////////////
        //   Objeto clsTable2017 PRODUTO        //
        //////////////////////////////////////////
        jsPrd={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"PRD_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "35"
                      ,"digitosMinMax"  : [1,12] // angelo kokiso aumento de 2 digito maximo
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|_|-|."
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : [  "Codigo do produto. Este campo é único."]
                      ,"importaExcel"   : "S"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"padrao":0}
           ,{"id":2   ,"field"          : "PRD_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "118"
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.|-|,| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Descrição do produto."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "PRD_CODNCM"  
                      ,"labelCol"       : "NCM" 
                      ,"obj"            : "edtNcm"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [10,10]
                      ,"ajudaCampo"     : [ "NCM da mercadoria"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":4  ,"field"          : "NCM_NOME"  
                      ,"labelCol"       : "DESCR_NCM" 
                      ,"obj"            : "edtNcmNome"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Descrição do NCM da mercadoria"]
                      ,"importaExcel"   : "N"                                                                
                      ,"insUpDel"       : ["N","N","N"]
                      ,"padrao":0}                     
            ,{"id":5  ,"field"          : "PRD_ST"  
                      ,"labelCol"       : "ST" 
                      ,"obj"            : "cbSt"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "10"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["N","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Se este produto tem substituição tributária"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":6  ,"field"          : "PRD_ALIQICMS"  
                      ,"labelCol"       : "%ICMS" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtIcmsAliq"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Aliquota de ICMS padrão para este produto"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":7  ,"field"          : "PRD_REDUCAOBC"  
                      ,"labelCol"       : "%RED_BASE" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtIcmsReducaoBc"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Redução da base de ICMS"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":8  ,"field"          : "PRD_IPI"  
                      ,"labelCol"       : "IPI" 
                      ,"obj"            : "cbIpi"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["N","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Se este produto tem IPI"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":9  ,"field"          : "PRD_ALIQIPI"  
                      ,"labelCol"       : "%IPI" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtIpiAliq"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Aliquota de IPI padrão para este produto"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":10 ,"field"          : "PRD_CSTIPI"  
                      ,"labelCol"       : "IPI_CST" 
                      ,"obj"            : "edtIpiCst"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Descrição do IPI da mercadoria"]
                      ,"importaExcel"   : "N"                                                                
                      ,"padrao":0}                     
            ,{"id":11 ,"field"          : "IPI_NOME"  
                      ,"labelCol"       : "DESCR_CSTIPI" 
                      ,"obj"            : "edtIpiNome"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Descrição do IPI da mercadoria"]
                      ,"importaExcel"   : "N"                                                                
                      ,"insUpDel"       : ["N","N","N"]
                      ,"padrao":0}                     
            ,{"id":12 ,"field"          : "PRD_CODEMB"  
                      ,"labelCol"       : "EMBALAGEM" 
                      ,"obj"            : "edtCodemb"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,10]
                      ,"ajudaCampo"     : [ "Embalagem da mercadoria"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":13 ,"field"          : "EMB_NOME"  
                      ,"labelCol"       : "DESCR_EMBALAGEM" 
                      ,"obj"            : "edtEmbNome"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Descrição da embalagem da mercadoria"]
                      ,"importaExcel"   : "N"                                                                
                      ,"insUpDel"       : ["N","N","N"]
                      ,"padrao":0}                     
            ,{"id":14 ,"field"          : "PRD_VLRVENDA"  
                      ,"labelCol"       : "VENDA" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrVenda"
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,10]
                      ,"ajudaCampo"     : [ "Valor de venda"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":15 ,"field"          : "PRD_CODPO"  
                      ,"labelCol"       : "ORIGEM" 
                      ,"obj"            : "edtOrigem"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Origem da mercadoria"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":16 ,"field"          : "PO_NOME"  
                      ,"labelCol"       : "DESCR_ORIGEM" 
                      ,"obj"            : "edtPoNome"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Descrição da origem da mercadoria"]
                      ,"importaExcel"   : "N"                                                                
                      ,"insUpDel"       : ["N","N","N"]
                      ,"padrao":0}                     
            ,{"id":17 ,"field"          : "PRD_CODBARRAS"  
                      ,"labelCol"       : "EAN" 
                      ,"obj"            : "edtEan"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "0"     
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["podeNull"]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|"
                      ,"digitosMinMax"  : [0,13]
                      ,"ajudaCampo"     : [ "Codigo de barras da mercadoria"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":18 ,"field"          : "PRD_PESOBRUTO"  
                      ,"labelCol"       : "PBRUTO" 
                      ,"newRecord"      : ["0,000","this","this"]
                      ,"obj"            : "edtPBruto"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Peso bruto"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":19 ,"field"          : "PRD_PESOLIQUIDO"  
                      ,"labelCol"       : "PLIQUIDO" 
                      ,"newRecord"      : ["0,000","this","this"]
                      ,"obj"            : "edtPLiquido"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Peso liquido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":20 ,"field"          : "PRD_DTCADASTRO"  
                      ,"labelCol"       : "DTCADASTRO" 
                      ,"obj"            : "edtDtCadastro"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "25"     
                      ,"fieldType"      : "dat"
                      ,"newRecord"      : [hoje,"this","this"]
                      ,"ajudaCampo"     : [ "Data em que a mercadoria foi cadastradada"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"importaExcel"   : "N" 
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          : "PRD_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":22 ,"field"          : "PRD_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":23 ,"field"          : "PRD_CODEMP" 
                      ,"pk"             : "S"
                      ,"labelCol"       : "CODEMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"padrao":7}					  
            ,{"id":24 ,"field"          : "EMP_APELIDO"   
                      ,"labelCol"       : "EMPRESA"
                      ,"obj"            : "edtDesEmp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [jsPub[0].emp_apelido,"this","this"]
                      ,"ajudaCampo"     : ["Nome da EMpresa."]
                      ,"padrao":0}
            ,{"id":25  ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":26  ,"field"          : "PRD_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":27 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objPrd.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"PRODUTOS - Detalhe do registro"
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
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmPrd"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaPrd"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmPrd"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblPrd"              // Nome da table
          ,"prefixo"        : "prd"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VPRODUTO"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPPRODUTO"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "PRD_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "PRD_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "PRD_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "104em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "PRODUTO"             // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objPrd.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objPrd.AjudaSisAtivo(jsPrd);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objPrd.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objPrd.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objPrd.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objPrd.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objPrd.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objPrd.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "PRODUTO[04]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objPrd === undefined ){  
          objPrd=new clsTable2017("objPrd");
        };  
        objPrd.montarHtmlCE2017(jsPrd); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsPrd.form).style.width=jsPrd.width;
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"CODIGO"     ,"labelCol":"CODIGO"      ,"tamGrd":"10em"     ,"tamImp":"20"} 
            ,{"id":0  ,"field":"DESCRICAO"  ,"labelCol":"DESCRICAO"   ,"tamGrd":"30em"     ,"tamImp":"20"}
            ,{"id":1  ,"field":"NCM"        ,"labelCol":"NCM"         ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":2  ,"field":"ST"         ,"labelCol":"ST"          ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":3  ,"field":"%ICMS"      ,"labelCol":"$IMS"        ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":4  ,"field":"%RED_BASE"  ,"labelCol":"%RED_BASE"   ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":5  ,"field":"IPI"        ,"labelCol":"IPI"         ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":6  ,"field":"%IPI"       ,"labelCol":"%IPI"        ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":7  ,"field":"IPI_CST"    ,"labelCol":"IPI_CST"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":8  ,"field":"EMBALAGEM"  ,"labelCol":"EMBALAGEM"   ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":9  ,"field":"VENDA"      ,"labelCol":"VENDA"       ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":10 ,"field":"ORIGEM"     ,"labelCol":"ORIGEM"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":11 ,"field":"EAN"        ,"labelCol":"EAN"         ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":12 ,"field":"PBRUTO"     ,"labelCol":"PBRUTO"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":13 ,"field":"PLIQUIDO"   ,"labelCol":"PLIQUIDO"    ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":14 ,"field":"ATIVO"      ,"labelCol":"ATIVO"       ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":14 ,"field":"REG"        ,"labelCol":"REG"         ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":15 ,"field":"ERRO"       ,"labelCol":"ERRO"        ,"tamGrd":"45em"    ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[15].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
          ,"relTitulo"      : "Importação PRODUTOS"     // Titulo do relatório
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
      var objPrd;                     // Obrigatório para instanciar o JS TFormaCob
      var jsPrd;                      // Obj principal da classe clsTable2017
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
      var intCodDir = parseInt(jsPub[0].usr_d04);
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
        clsJs.add("rotina"      , "selectPrd"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("produto" , clsJs.fim());
        msg     = requestPedido("Trac_Produto.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsPrd.registros=objPrd.addIdUnico(retPhp[0]["dados"]);
          objPrd.ordenaJSon(jsPrd.indiceTable,false);  
          objPrd.montarBody2017();
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
            clsJs.add("titulo"      , objPrd.trazCampoExcel(jsPrd));  
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("produto"   , envPhp              );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Produto.php",fd); 
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
            gerarMensagemErro("PRD",retPhp[0].erro,{cabec:"Aviso"});    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
        };          
      };

      ///////////////////////////
      //  AJUDA PARA EMBALAGEM //
      ///////////////////////////
      function embFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function embF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:obj.id
                      ,topo:100
                      ,tableBd:"EMBALAGEM"
                      ,fieldCod:"A.EMB_CODIGO"
                      ,fieldDes:"A.EMB_NOME"
                      ,fieldAtv:"A.EMB_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblEmb"}
        );
      };
      function RetF10tblEmb(arr){
        document.getElementById("edtCodemb").value  = arr[0].CODIGO;
        document.getElementById("edtEmbNome").value = arr[0].DESCRICAO;
        document.getElementById("edtCodemb").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function embBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbIpi"
                                  ,topo:100
                                  ,tableBd:"EMBALAGEM"
                                  ,fieldCod:"A.EMB_CODIGO"
                                  ,fieldDes:"A.EMB_NOME"
                                  ,fieldAtv:"A.EMB_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblEmb"}
          );
          document.getElementById(obj.id).value         = ( ret.length == 0 ? ""              : ret[0].CODIGO     );
          document.getElementById("edtEmbNome").value   = ( ret.length == 0 ? ""              : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )   );
        };
      };
      ///////////////////////
      // FIM F10 EMBALAGEM //
      ///////////////////////

      /////////////////////
      //  AJUDA PARA NCM //
      /////////////////////
      function ncmFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ncmF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:obj.id
                      ,topo:100
                      ,tableBd:"NCM"
                      ,fieldCod:"A.NCM_CODIGO"
                      ,fieldDes:"A.NCM_NOME"
                      ,fieldAtv:"A.NCM_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblNcm"}
        );
      };
      function RetF10tblNcm(arr){
        document.getElementById("edtNcm").value       = arr[0].CODIGO;
        document.getElementById("edtNcmNome").value   = arr[0].DESCRICAO;
        document.getElementById("edtNcm").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function ncmBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtIcmsAliq"
                                  ,topo:100
                                  ,tableBd:"NCM"
                                  ,fieldCod:"A.NCM_CODIGO"
                                  ,fieldDes:"A.NCM_NOME"
                                  ,fieldAtv:"A.NCM_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblNcm"}
          );
          document.getElementById(obj.id).value         = ( ret.length == 0 ? ""              : ret[0].CODIGO     );
          document.getElementById("edtNcmNome").value   = ( ret.length == 0 ? ""              : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )   );
        };
      };
      /////////////////
      // FIM F10 NCM //
      /////////////////

      /////////////////////
      //  AJUDA PARA IPI //
      /////////////////////
      function ipiFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ipiF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:obj.id
                      ,topo:100
                      ,tableBd:"CSTIPI"
                      ,fieldCod:"A.IPI_CODIGO"
                      ,fieldDes:"A.IPI_NOME"
                      ,fieldAtv:"A.IPI_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblIpi"}
        );
      };
      function RetF10tblIpi(arr){
        document.getElementById("edtIpiCst").value    = arr[0].CODIGO;
        document.getElementById("edtIpiNome").value   = arr[0].DESCRICAO;
        document.getElementById("edtIpiCst").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function ipiBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtIpiAliq"
                                  ,topo:100
                                  ,tableBd:"CSTIPI"
                                  ,fieldCod:"A.IPI_CODIGO"
                                  ,fieldDes:"A.IPI_NOME"
                                  ,fieldAtv:"A.IPI_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblIpi"}
          );
          document.getElementById(obj.id).value         = ( ret.length == 0 ? ""              : ret[0].CODIGO     );
          document.getElementById("edtIpiNome").value   = ( ret.length == 0 ? ""              : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )   );
        };
      };
      /////////////////
      // FIM F10 IPI //
      /////////////////

      ////////////////////////
      //  AJUDA PARA ORIGEM //
      ////////////////////////
      function origemFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function origemF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:obj.id
                      ,topo:100
                      ,tableBd:"PRODUTOORIGEM"
                      ,fieldCod:"A.PO_CODIGO"
                      ,fieldDes:"A.PO_NOME"
                      ,fieldAtv:"A.PO_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblOrigem"}
        );
      };
      function RetF10tblOrigem(arr){
        document.getElementById("edtOrigem").value    = arr[0].CODIGO;
        document.getElementById("edtPoNome").value   = arr[0].DESCRICAO;
        document.getElementById("edtOrigem").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function origemBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtEan"
                                  ,topo:100
                                  ,tableBd:"PRODUTOORIGEM"
                                  ,fieldCod:"A.PO_CODIGO"
                                  ,fieldDes:"A.PO_NOME"
                                  ,fieldAtv:"A.PO_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblOrigem"}
          );
          document.getElementById(obj.id).value         = ( ret.length == 0 ? ""              : ret[0].CODIGO     );
          document.getElementById("edtPoNome").value    = ( ret.length == 0 ? ""              : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )   );
        };
      };
      ////////////////////
      // FIM F10 ORIGEM //
      ////////////////////

      function btnConfirmarClick(){
        try{
          /////////////////////////////////////////////
          // Existes checks que olham para estes campos
          /////////////////////////////////////////////
          msg="ok";
          if( (document.getElementById("cbIpi").value=="S") && (jsNmrs("edtIpiAliq").dolar().ret()==0) )      
            msg="PARA IPI=S FAVOR INFORMAR ALIQUOTA!";
          if( (document.getElementById("cbIpi").value=="N") && (jsNmrs("edtIpiAliq").dolar().ret()>0) )      
            msg="PARA IPI=N FAVOR INFORMAR ALIQUOTA ZERO!";
          
          if( msg != "ok" ){
            throw msg;
          } else {  
            objPrd.gravar(true);
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
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
              name="frmPrd" 
              id="frmPrd" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Produto<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 230px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodigo" 
                                           placeholder="##.##.#####"                 
                                           maxlength="32" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo70">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtVlrVenda" type="text" onBlur="fncCasaDecimal(this,2);" maxlength="12"  />
                <label class="campo_label campo_required" for="edtVlrVenda">Valor Venda:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbSt">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbSt">TEM ST</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtNcm"
                                                    onBlur="ncmBlur(this);" 
                                                    onFocus="ncmFocus(this);" 
                                                    onClick="ncmF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="10"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtNcm">NCM:</label>
              </div>
              <div class="campotexto campo45">
                <input class="campo_input" id="edtNcmNome" type="text" />
                <label class="campo_label campo_required" for="edtNcmNome">NCM Descricao:</label>
              </div>
              <div class="campotexto campo12">
                <input class="campo_input edtDireita" id="edtIcmsAliq" type="text" onBlur="fncCasaDecimal(this,2);" maxlength="6" />
                <label class="campo_label campo_required" for="edtIcmsAliq">%ICMS:</label>
              </div>
              <div class="campotexto campo12">
                <input class="campo_input edtDireita" id="edtIcmsReducaoBc" type="text" onBlur="fncCasaDecimal(this,2);" maxlength="6"  />
                <label class="campo_label campo_required" for="edtIcmsReducaoBc">%Base ICMS:</label>
              </div>
              <div class="campotexto campo15">
              <input class="campo_input inputF10" id="edtCodemb"
                                                    onBlur="embBlur(this);" 
                                                    onFocus="embFocus(this);" 
                                                    onClick="embF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodemb">EMBALAGEM:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtEmbNome" type="text" />
                <label class="campo_label campo_required" for="edtEmbNome">EMBALAGEM DESCRICAO:</label>
              </div>
              <div class="campotexto campo10">
                <select class="campo_input_combo" id="cbIpi">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbIpi">TEM IPI</label>
              </div>
              <div class="campotexto campo12">
                <input class="campo_input edtDireita" id="edtIpiAliq" type="text" onBlur="fncCasaDecimal(this,2);" maxlength="6"  />
                <label class="campo_label campo_required" for="edtIpiAliq">%IPI:</label>
              </div>
              <div class="campotexto campo12">
                <input class="campo_input inputF10" id="edtIpiCst"
                                                    onBlur="ipiBlur(this);" 
                                                    onFocus="ipiFocus(this);" 
                                                    onClick="ipiF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="2"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtIpiCst">CST IPI:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtIpiNome" type="text" />
                <label class="campo_label campo_required" for="edtIpiNome">CST IPI DESCRICAO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtOrigem"
                                                    onBlur="origemBlur(this);" 
                                                    onFocus="origemFocus(this);" 
                                                    onClick="origemF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="1"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtOrigem">ORIGEM:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input" id="edtPoNome" type="text" />
                <label class="campo_label campo_required" for="edtPoNome">ORIGEM DESCRICAO:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtEan" type="text" maxlength="20" />
                <label class="campo_label campo_required" for="edtEan">EAN:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtPBruto" type="text" onBlur="fncCasaDecimal(this,3);" maxlength="6"  />
                <label class="campo_label campo_required" for="edtPBruto">PESO BRUTO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtPLiquido" type="text" onBlur="fncCasaDecimal(this,3);" maxlength="6"  />
                <label class="campo_label campo_required" for="edtPLiquido">PESO LIQUIDO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input_titulo" disabled id="edtDtCadastro" type="text" />
                <label class="campo_label campo_required" for="edtDtCadastro">Cadastro</label>
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
              <div class="campotexto campo20">
                <input class="campo_input_titulo input" id="edtDesEmp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesEmp">EMPRESA:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo" disabled id="edtUsuario" type="text" />
                <label class="campo_label campo_required" for="edtUsuario">USUARIO</label>
              </div>
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
                <input id="edtCodEmp" type="text" />
              </div>
              <div onClick="btnConfirmarClick();" id="btnConfirmar" class="btnImagemEsq bie12 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
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