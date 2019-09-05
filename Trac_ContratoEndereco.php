<?php
  session_start();
  if( isset($_POST["contratoendereco"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/consultaCep.class.php");
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["contratoendereco"]);
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
        /////////////////////////////////////////////////
        //    Dados para JavaScript CONTRATOENDERECO   //
        /////////////////////////////////////////////////
        if( $rotina=="selectCntE" ){
          $sql="";
          $sql.="SELECT A.CNTE_CODIGO";
          $sql.="       ,A.CNTE_CEP";          
          $sql.="       ,A.CNTE_CODFVR";
          $sql.="       ,FVR.FVR_NOME";          
          $sql.="       ,A.CNTE_CODCDD";
          $sql.="       ,C.CDD_NOME";
          $sql.="       ,C.CDD_CODEST";          
          $sql.="       ,A.CNTE_CODLGR";          
          $sql.="       ,A.CNTE_ENDERECO";
          $sql.="       ,A.CNTE_NUMERO";
          $sql.="       ,A.CNTE_BAIRRO";
          $sql.="       ,A.CNTE_FONE";
          $sql.="       ,A.CNTE_EMAIL";          
          $sql.="       ,A.CNTE_COMPLEMENTO";
          $sql.="       ,CASE WHEN A.CNTE_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CNTE_ATIVO";
          $sql.="       ,CASE WHEN A.CNTE_REG='P' THEN 'PUB' WHEN A.CNTE_REG='S' THEN 'SIS' ELSE 'ADM' END AS CNTE_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.CNTE_CODUSR";
          $sql.="       ,A.CNTE_LATITUDE";          
          $sql.="       ,A.CNTE_LONGITUDE";                    
          $sql.="  FROM CONTRATOENDERECO A";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.CNTE_CODFVR=FVR.FVR_CODIGO";          
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.CNTE_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN CIDADE C ON A.CNTE_CODCDD=C.CDD_CODIGO";
          $sql.="  WHERE (CNTE_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')";
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
          $vldCampo = new validaCampo("CONTRATOENDERECO",0);
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
        //////////////////////////////////////////////////////////////////////////////
        // Atualizando o contratoendereco de dados se opcao de insert/updade/delete //
        //////////////////////////////////////////////////////////////////////////////
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
    <title>Contrato endereco</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaCidadeF10.js"></script>    
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        //////////////////////////////////////////////
        //   Objeto clsTable2017 CONTRATOENDERECO   //
        //////////////////////////////////////////////
        jsCntE={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"CNTE_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"autoIncremento" : "S"                      
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0","this","this"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"digitosMinMax"  : [1,6] 
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "ID. Este campo é único e deve tem o formato 999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "CNTE_CEP"   
                      ,"labelCol"       : "CEP"
                      ,"obj"            : "edtCep"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [8,8]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"CNTE_CODFVR" 
                      ,"labelCol"       : "CODFVR"
                      ,"obj"            : "edtCodFvr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [1,4] 
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do favorecido. Este campo é único e deve tem o formato 999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "FVR_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "FAVORECIDO"
                      ,"obj"            : "edtDesFvr"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "50"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,60]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Nome do favorecido."]
                      ,"padrao":0}
            ,{"id":5  ,"field"          :"CNTE_CODCDD" 
                      ,"labelCol"       : "CODCDD"
                      ,"obj"            : "edtCodCdd"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["3550308","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,7]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"ajudaDetalhe"   : "Codigo da cidade conforme IBGE"
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "CDD_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesCdd"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "40"
                      ,"newRecord"      : ["SAO PAULO","this","this"]
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "CDD_CODEST"   
                      ,"labelCol"       : "UF"
                      ,"obj"            : "edtCodEst"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "40"
                      ,"newRecord"      : ["SP","this","this"]
                      ,"digitosMinMax"  : [2,3]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z"
                      ,"ajudaCampo"     : ["Uf da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "CNTE_CODLGR" 
                      ,"labelCol"       : "CODLGR"
                      ,"obj"            : "edtCodLgr"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["RUA","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":9  ,"field"         : "CNTE_ENDERECO"   
                      ,"labelCol"       : "ENDERECO"
                      ,"obj"            : "edtEndereco"
                      ,"tamGrd"         : "50em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"field"         : "CNTE_NUMERO"   
                      ,"labelCol"       : "NUMERO"
                      ,"obj"            : "edtNumero"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [1,10]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":11 ,"field"         : "CNTE_BAIRRO"   
                      ,"labelCol"       : "BAIRRO"
                      ,"obj"            : "edtBairro"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":12 ,"field"         : "CNTE_FONE"   
                      ,"labelCol"       : "FONE"
                      ,"obj"            : "edtFone"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,10]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|-"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"field"         : "CNTE_EMAIL"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "EMAIL"
                      ,"obj"            : "edtEmail"
                      ,"tamGrd"         : "40em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosValidos" : "a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|0!1|2|3|4|5|6|7|8|9|@|_|.|-"
                      ,"formato"        : ["lowercase","removeacentos","tiraaspas","alltrim"]
                      ,"ajudaCampo"     : ["Email do favorecido.","Para vendedor este campo liga o favorecido ao usuario que se logou no sistema"]
                      ,"validar"        : ["podeNull"]
                      ,"digitosMinMax"  : [0,60]
                      ,"padrao":0}
            ,{"id":14 ,"field"          : "CNTE_COMPLEMENTO" 
                      ,"labelCol"       : "COMPLEMENTO"
                      ,"obj"            : "edtComplemento"
                      ,"tamGrd"         : "40em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [2,60]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "CNTE_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":16 ,"field"          : "CNTE_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":17  ,"field"         : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":18  ,"field"         : "CNTE_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}               
            ,{"id":19 ,"field"             : "CNTE_LATITUDE"
                      ,"labelCol"          : "LATITUDE"
                      ,"fieldType"         : "flo8"
                      ,"formato"           : ["f8"]
                      ,"tipo"              : "edt"
                      ,"obj"               : "edtLatitude"
                      ,"newRecord"         : ["0.00000000","this","this"]
                      ,"insUpDel"          : ["S","S","N"]
                      ,"pk"                : "N"
                      ,"validar"           : ["notnull"]
                      ,"tamGrd"            : "8em"
                      ,"tamImp"            : "20"
                      ,"importaExcel"      : "S"
                      ,"excel"             : "S"
                      ,"hint"              : "S"
                      ,"ordenaColuna"      : "S"
                      ,"inputDisabled"     : true
                      ,"ajudaCampo"        : ["latitude do favorecido."]
                      ,"padrao"            : 0}
            ,{"id":20 ,"field"             : "CNTE_LONGITUDE"
                      ,"labelCol"          : "LONGITUDE"
                      ,"fieldType"         : "flo8"
                      ,"formato"           : ["f8"]             
                      ,"tipo"              : "edt"
                      ,"obj"               : "edtLongitude"
                      ,"newRecord"         : ["0.00000000","this","this"]
                      ,"insUpDel"          : ["S","S","N"]
                      ,"pk"                : "N"
                      ,"validar"           : ["notnull"]
                      ,"tamGrd"            : "8em"
                      ,"tamImp"            : "20"
                      ,"importaExcel"      : "S"
                      ,"excel"             : "S"
                      ,"hint"              : "S"
                      ,"ordenaColuna"      : "S"
                      ,"inputDisabled"     : true
                      ,"ajudaCampo"        : ["longitude do favorecido."]
                      ,"padrao"            : 0}
            ,{"id":21 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objCntE.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"CONTRATOENDERECO - Detalhe do registro"
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
          ,"registros"      : []                      // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"          // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"           // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmCntE"               // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCntE"            // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCntE"               // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"         // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblCntE"               // Nome da table
          ,"prefixo"        : "CntE"                  // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "CONTRATOENDERECO"      // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPCONTRATOENDERECO"   // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "CNTE_ATIVO"            // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "CNTE_REG"              // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "CNTE_CODUSR"           // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"           // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "108em"                 // Tamanho da table
          ,"height"         : "58em"                  // Altura da table
          ,"tableLeft"      : "sim"                   // Se tiver menu esquerdo
          ,"relTitulo"      : "CONTRATOENDERECO"      // Titulo do relatório
          ,"relOrientacao"  : "P"                     // Paisagem ou retrato
          ,"relFonte"       : "7"                     // Fonte do relatório
          ,"foco"           : ["edtCep"
                              ,"edtCep"
                              ,"btnConfirmar"]        // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"       // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "FAVORECIDO"            // Indice inicial da table
          ,"tamBotao"       : "12"                    // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"                // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objCntE.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objCntE.AjudaSisAtivo(jsCntE);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objCntE.detalhe();"]
                               //,["Gerar excel"                            ,"fa-file-excel-o"  ,"objCntE.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objCntE.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objCntE.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objCntE.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objCntE.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objCntE.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "CONTRATOENDERECO[33]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objCntE === undefined ){  
          objCntE=new clsTable2017("objCntE");
        };  
        objCntE.montarHtmlCE2017(jsCntE); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsCntE.form).style.width=jsCntE.width;
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
      var objCntE;                    // Obrigatório para instanciar o JS TFormaCob
      var jsCntE;                     // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS BancoF10
      var objCddF10;                  // Obrigatório para instanciar o JS CidadeF10      
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var chkds;                      // Guarda todos registros checados na table       
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d33);
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
        clsJs.add("rotina"      , "selectCntE"                                );
        clsJs.add("login"       , jsPub[0].usr_login                          );
        clsJs.add("ativo"       , atv                                         );
        clsJs.add("codCntE"      , document.getElementById("edtCodigo").value );    
        fd = new FormData();
        fd.append("contratoendereco" , clsJs.fim());
        msg     = requestPedido("Trac_ContratoEndereco.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsCntE.registros=objCntE.addIdUnico(retPhp[0]["dados"]);
          objCntE.ordenaJSon(jsCntE.indiceTable,false);  
          objCntE.montarBody2017();
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
            clsJs.add("titulo"      , objCntE.trazCampoExcel(jsCntE));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("contratoendereco"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_ContratoEndereco.php",fd); 
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
      /*
      ////////////////////////////
      //  AJUDA PARA FAVORECIDO //
      ////////////////////////////
      function fvrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fvrF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCep"
                      ,topo:100
                      ,tableBd:"FAVORECIDO"
                      ,fieldCod:"A.FVR_CODIGO"
                      ,fieldDes:"A.FVR_NOME"
                      ,fieldAtv:"A.FVR_ATIVO"
                      ,typeCod :"int" 
                      ,divWidth:"42%"
                      ,tbl:"tblFvr"}
        );
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value  = arr[0].CODIGO;
        document.getElementById("edtDesFvr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codFvrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCep"
                                  ,topo:100
                                  ,tableBd:"FAVORECIDO"
                                  ,fieldCod:"A.FVR_CODIGO"
                                  ,fieldDes:"A.FVR_NOME"
                                  ,fieldAtv:"A.FVR_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblFvr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "000"  : ret[0].CODIGO                  );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? "NAO SE APLICA"      : ret[0].DESCRICAO );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "000" : ret[0].CODIGO )  );
        };
      };
      */
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
        //document.getElementById("edtDesLgr").value  = arr[0].DESCRICAO;
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
          //document.getElementById("edtDesLgr").value  = ( ret.length == 0 ? "RUA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "RUA" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////
      //  AJUDA PARA CIDADE     //
      ////////////////////////////
      function cddFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function cddF10Click(obj){ 
        fCidadeF10(0,obj.id,"edtCodLgr",100,{ativo:"S"});
      };
      function RetF10tblCdd(arr){
        document.getElementById("edtCodCdd").value  = arr[0].CODIGO;
        document.getElementById("edtDesCdd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodEst").value  = arr[0].UF;
        document.getElementById("edtCodCdd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCddBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.value;
        if( elOld != elNew ){
          var ret = fCidadeF10(1,obj.id,"edtCodLgr",100,{codcdd:obj.id,ativo:"S"});                    
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "0000000" : ret[0].CODIGO     );
          document.getElementById("edtDesCdd").value  = ( ret.length == 0 ? ""        : ret[0].DESCRICAO  );
          document.getElementById("edtCodEst").value  = ( ret.length == 0 ? ""        : ret[0].UF         );          
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "3550308" : ret[0].CODIGO )  );
        };
      };
      ////////////////////
      // On exit do CEP //
      ////////////////////
      function cepFocus(obj){
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value);         
      };
      //
      function cepBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = document.getElementById(obj.id).value;
        if( (elOld != elNew) && (elNew !="") ){
          clsJs   = jsString("lote");
          clsJs.add("rotina"  , "rotinaCep"         );
          clsJs.add("cep"     , obj.value  );
          clsJs.add("login"   , jsPub[0].usr_login  );
          fd = new FormData();
          fd.append("contratoendereco" , clsJs.fim()); 
          msg = requestPedido("Trac_ContratoEndereco.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
          } else {
            document.getElementById("edtEndereco").value  = retPhp[0]["dados"][0]["endereco"];
            document.getElementById("edtBairro").value    = retPhp[0]["dados"][0]["bairro"];
            document.getElementById("edtCodCdd").value    = retPhp[0]["dados"][0]["codcdd"];
            document.getElementById("edtDesCdd").value    = retPhp[0]["dados"][0]["cidade"];
            document.getElementById("edtCodLgr").value    = retPhp[0]["dados"][0]["codtl"];
            document.getElementById("edtCodEst").value    = retPhp[0]["dados"][0]["uf"];
            if( retPhp[0]["dados"][0]["lat"] != 0 )
              document.getElementById("edtLatitude").value=retPhp[0]["dados"][0]["lat"];
            if( retPhp[0]["dados"][0]["lon"] != 0 )
              document.getElementById("edtLongitude").value=retPhp[0]["dados"][0]["lon"];
            ////////////////////////////////////////////////////////////////////////////
            // Para quando for sair do campo não executar o select validando o codigo //
            ////////////////////////////////////////////////////////////////////////////
            document.getElementById("edtCodCdd").setAttribute("data-oldvalue",retPhp[0]["dados"][0]["codcdd"]);
          };  
        };  
      };
      /////////////////////////////////////////////////
      // Este aqui tenta atualizar a latitude/longitude
      /////////////////////////////////////////////////
      function cepAtualiza(){
        clsJs   = jsString("lote");
        clsJs.add("rotina"  , "rotinaCep"           );
        clsJs.add("cep"     , $doc("edtCep").value  );
        clsJs.add("login"   , jsPub[0].usr_login    );
        fd = new FormData();
        fd.append("contratoendereco" , clsJs.fim()); 
        msg = requestPedido("Trac_ContratoEndereco.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno != "OK" ){
          gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});
        } else {
          if( retPhp[0]["dados"][0]["lat"] != 0 )
            document.getElementById("edtLatitude").value=retPhp[0]["dados"][0]["lat"];
          if( retPhp[0]["dados"][0]["lon"] != 0 )
            document.getElementById("edtLongitude").value=retPhp[0]["dados"][0]["lon"];
        };  
      };
      //
      function horCadastrarClick(){
        try{
          chkds=objCntE.gerarJson("1").gerar();
          objCntE.cadastrar(0);
          $doc("edtCodFvr").value=chkds[0].CODFVR;
          $doc("edtDesFvr").value=chkds[0].FAVORECIDO;
          jsCmpAtivo("edtCodFvr").remove("inputF10").add("inputF10Titulo").disabled(true);            
        } catch(e){
          gerarMensagemErro("exc",e,"AVISO");          
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
              name="frmCntE" 
              id="frmCntE" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Endereço alternativo Entrega/Instalação<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 250px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input_titulo" id="edtCodigo" maxlength="4" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <!--
              <div class="campotexto campo10">
                <input class="campo_input_titulo" id="edtCodFvr"
                                                    OnKeyPress="return mascaraInteiro(event);"
                                                    onBlur="codFvrBlur(this);" 
                                                    onFocus="fvrFocus(this);" 
                                                    onClick="fvrF10Click(this);"
                                                    data-oldvalue="0000" 
                                                    autocomplete="off"
                                                    type="text" 
                                                    disabled />
                <label class="campo_label campo_required" for="edtCodFvr">FAVORECIDO:</label>
              </div>
              -->
              <div class="campotexto campo10">
                <input class="campo_input_titulo" id="edtCodFvr"
                                                    type="text" 
                                                    disabled />
                <label class="campo_label" for="edtCodFvr">FAVORECIDO:</label>
              </div>
              
              <div class="campotexto campo70">
                <input class="campo_input_titulo input" id="edtDesFvr" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesFvr">NOME_FAVORECIDO</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input"  name="edtCep" id="edtCep" type="text" 
                       onFocus="cepFocus(this);" 
                       onBlur="cepBlur(this);"
                       OnKeyPress="return mascaraInteiro(event);" maxlength="8" />
                <label class="campo_label campo_required" for="edtCep">CEP</label>
              </div>
              <div class="campotexto campo10">
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
              <div class="campotexto campo25">
                <input class="campo_input_titulo input" id="edtDesCdd" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCdd">CIDADE:</label>
              </div>
              <div class="campotexto campo05">
                <input class="campo_input_titulo input" id="edtCodEst" type="text" disabled />
                <label class="campo_label campo_required" for="edtCodEst">UF:</label>
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
                <label class="campo_label campo_required" for="edtCodLgr">LOGR:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input" id="edtEndereco" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtEndereco">ENDERECO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input" id="edtNumero" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtNumero">NUMERO:</label>
              </div>
              <div class="campotexto campo70">
                <input class="campo_input" id="edtComplemento" type="text" maxlength="60" />
                <label class="campo_label" for="edtComplemento">COMPLEMENTO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtBairro" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtBairro">BAIRRO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtFone" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtFone">FONE:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtEmail" type="text" maxlength="60" />
                <label class="campo_label" for="edtEmail">EMAIL:</label>
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
              <div class="campotexto campo20">
                <input class="campo_input_titulo" disabled id="edtUsuario" type="text" />
                <label class="campo_label campo_required" for="edtUsuario">USUARIO</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtLatitude" type="text" />
                <label class="campo_label campo_required" for="edtLatitude">LATITUDE</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtLongitude" type="text" />
                <label class="campo_label campo_required" for="edtLongitude">LONGITUDE</label>
              </div>
              <div class="campo10" style="float:left;height:20px;">            
                <div onClick="funcMapa();" id="btnLatLon" class="btnImagemEsq bie100 bieAzul"><i class="fa fa-map-marker"> Mapa</i></div>
              </div>              
              <div class="campo10" style="float:left;height:20px;">            
                <div onClick="cepAtualiza();" id="btnAtualiza" class="btnImagemEsq bie100 bieAzul"><i class="fa fa-search"> Atualiza</i></div>
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