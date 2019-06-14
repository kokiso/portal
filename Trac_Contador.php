<?php
  session_start();
  if( isset($_POST["contador"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");
      require("classPhp/consultaCep.class.php");            

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["contador"]);
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
        /////////////////////////////////////////////
        //    Dados para JavaScript CONTADOR       //
        /////////////////////////////////////////////
        if( $rotina=="selectCnt" ){
          $sql="";
          $sql.="SELECT A.CNT_CODIGO";
          $sql.="       ,A.CNT_NOME";
          $sql.="       ,A.CNT_CRC";
          $sql.="       ,A.CNT_CPF";
          $sql.="       ,A.CNT_CODQC";
          $sql.="       ,Q.QC_NOME";
          $sql.="       ,A.CNT_CODCDD";
          $sql.="       ,C.CDD_NOME";
          $sql.="       ,A.CNT_CNPJ";
          $sql.="       ,A.CNT_CODLGR";
          $sql.="       ,A.CNT_ENDERECO";
          $sql.="       ,A.CNT_NUMERO";
          $sql.="       ,A.CNT_CEP";
          $sql.="       ,A.CNT_BAIRRO";
          $sql.="       ,A.CNT_FONE";
          $sql.="       ,A.CNT_EMAIL";
          $sql.="       ,A.CNT_SUFRAMA";
          $sql.="       ,CASE WHEN A.CNT_CODINCTRIB='1' THEN CAST('NAO CUMULATIVO' AS VARCHAR(14))";
          $sql.="             WHEN A.CNT_CODINCTRIB='2' THEN CAST('CUMULATIVO' AS VARCHAR(10))";
          $sql.="             WHEN A.CNT_CODINCTRIB='3' THEN CAST('AMBOS' AS VARCHAR(5)) END AS CNT_CODINCTRIB";
          $sql.="       ,CASE WHEN CNT_INDAPROCRED='1' THEN CAST('APROPRIACAO DIRETA' AS VARCHAR(18))";
          $sql.="             WHEN CNT_INDAPROCRED='2' THEN CAST('RATEIO PROPORCIONAL' AS VARCHAR(19))";
          $sql.="        END AS CNT_INDAPROCRED";
          $sql.="       ,CASE WHEN CNT_CODTIPOCONT='1' THEN CAST('BASICA' AS VARCHAR(6))";
          $sql.="             WHEN CNT_CODTIPOCONT='2' THEN CAST('DIFERENCIADA' AS VARCHAR(12))";
          $sql.="        END AS CNT_CODTIPOCONT";
          $sql.="       ,CASE WHEN CNT_INDREGCUM='1' THEN CAST('REGIME DE CAIXA' AS VARCHAR(15))";
          $sql.="             WHEN CNT_INDREGCUM='2' THEN CAST('CONSOLIDADO/COMPETENCIA' AS VARCHAR(23))";
          $sql.="             WHEN CNT_INDREGCUM='3' THEN CAST('DETALHADO/COMPETENCIA' AS VARCHAR(21))";
          $sql.="        END AS CNT_INDREGCUM";
          $sql.="       ,A.CNT_CODRECPIS";
          $sql.="       ,A.CNT_CODRECCOFINS";
          $sql.="       ,CASE WHEN CNT_INDNATPJ='00' THEN CAST('PJ' AS VARCHAR(2))";
          $sql.="             WHEN CNT_INDNATPJ='01' THEN CAST('COOPERATIVA' AS VARCHAR(11))";
          $sql.="             WHEN CNT_INDNATPJ='02' THEN CAST('SUJEITO PIS/PASEP' AS VARCHAR(17))";
          $sql.="        END AS CNT_INDNATPJ";
          $sql.="       ,CASE WHEN CNT_INDATIV='0' THEN CAST('INDUSTRIA' AS VARCHAR(9))";
          $sql.="             WHEN CNT_INDATIV='1' THEN CAST('PRESTADOR' AS VARCHAR(9))";
          $sql.="             WHEN CNT_INDATIV='2' THEN CAST('COMERCIO' AS VARCHAR(8))";
          $sql.="             WHEN CNT_INDATIV='3' THEN CAST('FINANCEIRA' AS VARCHAR(10))";
          $sql.="             WHEN CNT_INDATIV='4' THEN CAST('IMOBILIARIA' AS VARCHAR(11))";
          $sql.="             WHEN CNT_INDATIV='9' THEN CAST('OUTRAS' AS VARCHAR(6))";
          $sql.="        END AS CNT_INDATIV";
          $sql.="       ,A.CNT_CODEMP";
          $sql.="       ,E.EMP_APELIDO";
          $sql.="       ,CASE WHEN A.CNT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS CNT_ATIVO";
          $sql.="       ,CASE WHEN A.CNT_REG='P' THEN 'PUB' WHEN A.CNT_REG='S' THEN 'SIS' ELSE 'ADM' END AS CNT_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.CNT_CODUSR";
          $sql.="  FROM CONTADOR A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.CNT_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN CIDADE C ON A.CNT_CODCDD=C.CDD_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA E ON A.CNT_CODEMP=E.EMP_CODIGO";
          $sql.="  LEFT OUTER JOIN QUALIFICACAOCONT Q ON A.CNT_CODQC=Q.QC_CODIGO";
          $sql.="  WHERE ((CNT_CODEMP=".$lote[0]->codemp.")";
          $sql.="   AND ((CNT_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
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
        /*
        if( $rotina=="impExcel" ){
          ////////////////////////////////////////////////////////////////////////
          // Enviando para a class todas as colunas com as checagens necessaria //
          // Nome da tabela e numeros de erros(se existir)                      //
          // Modelo do JSON estah na classe                                     //
          ////////////////////////////////////////////////////////////////////////
          $matriz   = (array)$lote[0]->titulo;
          $vldCampo = new validaCampo("VCONTADOR",0);
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
        */
        ///////////////////////////////////////////////////////////////////
        // Atualizando o contador de dados se opcao de insert/updade/delete //
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
    <title>Contador</title>
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
        //   Objeto clsTable2017 CONTADOR       //
        //////////////////////////////////////////
        jsCnt={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          : "CNT_CODIGO" 
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
                      ,"ajudaCampo"     : [  "Codigo do contador. Este campo é único e deve tem o formato 999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "CNT_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtNome"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "33em"
                      ,"tamImp"         : "100"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.|/| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]
                      ,"ajudaCampo"     : ["Nome do Contador."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "CNT_CRC" 
                      ,"labelCol"       : "CRC"
                      ,"obj"            : "edtCrc"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "25"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|.|-"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "CNT_CPF" 
                      ,"labelCol"       : "CPF"
                      ,"obj"            : "edtCpf"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,14]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "CNT_CODQC" 
                      ,"labelCol"       : "CODQC"
                      ,"obj"            : "edtCodQc"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "QC_NOME"   
                      ,"labelCol"       : "QCNOME"
                      ,"obj"            : "edtDesQc"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,70]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "CNT_CODCDD" 
                      ,"labelCol"       : "CODCDD"
                      ,"obj"            : "edtCodCdd"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : [jsPub[0].emp_codcdd,"this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,7]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "CDD_NOME"   
                      ,"labelCol"       : "CIDADE"
                      ,"obj"            : "edtDesCdd"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [jsPub[0].emp_descdd,"this","this"]
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "CNT_CNPJ" 
                      ,"labelCol"       : "CNPJ"
                      ,"obj"            : "edtCnpj"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,14]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "CNT_CODLGR" 
                      ,"labelCol"       : "CODLGR"
                      ,"obj"            : "edtCodLgr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["RUA","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,5]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":11 ,"field"          : "CNT_ENDERECO"   
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
            ,{"id":12 ,"field"          : "CNT_NUMERO"   
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
            ,{"id":13 ,"field"          : "CNT_CEP"   
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
            ,{"id":14 ,"field"          : "CNT_BAIRRO"   
                      ,"labelCol"       : "BAIRRO"
                      ,"obj"            : "edtBairro"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "CNT_FONE"   
                      ,"labelCol"       : "FONE"
                      ,"obj"            : "edtFone"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"digitosMinMax"  : [3,10]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|-"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":16 ,"field"          : "CNT_EMAIL" 
                      ,"labelCol"       : "EMAIL"
                      ,"obj"            : "edtEmail"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|0!1|2|3|4|5|6|7|8|9|@|_|.|-"
                      ,"formato"        : ["lowercase","removeacentos","tiraaspas","alltrim"]
                      ,"ajudaCampo"     : ["Email do contador."]
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,60]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":17 ,"field"          : "CNT_SUFRAMA"  
                      ,"labelCol"       : "SUFRAMA" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "edtSuframa"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["podeNull"]
                      ,"digitosMinMax"  : [0,9]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}   
            ,{"id":18 ,"field"          : "CNT_CODINCTRIB"  
                      ,"labelCol"       : "CODINCTRIB" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "cbCodIncTrib"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":19 ,"field"          : "CNT_INDAPROCRED"  
                      ,"labelCol"       : "INDAPROCRED" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "cbIndProc"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":20 ,"field"          : "CNT_CODTIPOCONT"  
                      ,"labelCol"       : "CODTIPOCONT" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "cbCodTipoCont"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          : "CNT_INDREGCUM"  
                      ,"labelCol"       : "INDREGCUM" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "cbIndRegCum"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":22 ,"field"          : "CNT_CODRECPIS"  
                      ,"labelCol"       : "CODRECPIS" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "edtCodRecPis"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":23 ,"field"          : "CNT_CODRECCOFINS"  
                      ,"labelCol"       : "CODRECCOFINS" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "edtCodRecCofins"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":24 ,"field"          : "CNT_INDNATPJ"  
                      ,"labelCol"       : "INDNATPJ" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "edtIndNatPj"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,2]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":25 ,"field"          : "CNT_INDATIV"  
                      ,"labelCol"       : "INDATIV" 
                      ,"newRecord"      : ["","this","this"]
                      ,"obj"            : "edtIndAtiv"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":26 ,"field"          : "CNT_CODEMP" 
                      ,"pk"             : "N"
                      ,"labelCol"       : "CODEMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"padrao":7}					  
            ,{"id":27 ,"field"          : "EMP_APELIDO"   
                      ,"labelCol"       : "EMPRESA"
                      ,"obj"            : "edtDesEmp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [jsPub[0].emp_apelido,"this","this"]
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":28 ,"field"          : "CNT_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":29 ,"field"          : "CNT_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":30 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":31  ,"field"          : "CNT_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":32 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objCnt.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"CONTADOR - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"            }
            ,{"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o" }
            ,{"texto":"Excluir"   ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"           }
            ,{"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"     }        
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"           }                        
            ,{"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"           }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmCnt"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCnt"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCnt"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblCnt"              // Nome da table
          ,"prefixo"        : "cnt"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VCONTADOR"           // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPCONTADOR"         // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "CNT_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "CNT_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "CNT_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "110em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "CONTADOR"            // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "7"                   // Fonte do relatório
          ,"foco"           : ["edtNome"
                              ,"edtNome"
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
                               //,["Importar planilha excel"              ,"fa-file-excel-o"  ,"fExcel()"]
                               //,["Modelo planilha excel"                ,"fa-file-excel-o"  ,"objCnt.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objCnt.AjudaSisAtivo(jsCnt);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objCnt.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objCnt.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objCnt.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objCnt.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objCnt.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objCnt.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "CONTADOR[03]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objCnt === undefined ){  
          objCnt=new clsTable2017("objCnt");
        };  
        objCnt.montarHtmlCE2017(jsCnt); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsCnt.form).style.width=jsCnt.width;
        btnFiltrarClick("S");  
      });
      //
      var objCnt;                     // Obrigatório para instanciar o JS TFormaCob
      var jsCnt;                      // Obj principal da classe clsTable2017
      //var objExc;                   // Obrigatório para instanciar o JS Importar excel
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
      var intCodDir = parseInt(jsPub[0].usr_d03);
      function funcRetornar(intOpc){
        document.getElementById("divRotina").style.display  = (intOpc==0 ? "block" : "none" );        
        document.getElementById("divExcel").style.display   = (intOpc==1 ? "block" : "none" );
      };
      /*
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
      */
      function excFecharClick(){
        funcRetornar(0);  
      };
      ////////////////////////////
      // Filtrando os registros //
      ////////////////////////////
      function btnFiltrarClick(atv) { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "selectCnt"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("contador" , clsJs.fim());
        msg     = requestPedido("Trac_Contador.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsCnt.registros=objCnt.addIdUnico(retPhp[0]["dados"]);
          objCnt.ordenaJSon(jsCnt.indiceTable,false);  
          objCnt.montarBody2017();
        }; 
      };
      ////////////////////
      // Importar excel //
      ////////////////////
      /*
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
            clsJs.add("titulo"      , objCnt.trazCampoExcel(jsCnt));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("contador"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Contador.php",fd); 
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
            gerarMensagemErro("CNT",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
        };          
      };
      */
      //////////////////////////////////
      //  AJUDA PARA QUALIFICACAOCONT //
      //////////////////////////////////
      function qcFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function qcF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtFone"
                      ,topo:100
                      ,tableBd:"QUALIFICACAOCONT"
                      ,fieldCod:"A.QC_CODIGO"
                      ,fieldDes:"A.QC_NOME"
                      ,fieldAtv:"A.QC_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblQc"}
        );
      };
      function RetF10tblQc(arr){
        document.getElementById("edtCodQc").value  = arr[0].CODIGO;
        document.getElementById("edtDesQc").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodQc").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodQcBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtFone"
                                  ,topo:100
                                  ,tableBd:"QUALIFICACAOCONT"
                                  ,fieldCod:"A.QC_CODIGO"
                                  ,fieldDes:"A.QC_NOME"
                                  ,fieldAtv:"A.QC_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblQc"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "999"  : ret[0].CODIGO             );
          document.getElementById("edtDesQc").value  = ( ret.length == 0 ? "OUTROS"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "999" : ret[0].CODIGO )  );
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
                      ,foco:"edtCnpj"
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
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCnpj"
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
      //  AJUDA PARA LOGRADOURO   //
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
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "RUA" : ret[0].CODIGO )  );
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
          fd.append("contador" , clsJs.fim()); 
          msg = requestPedido("Trac_Contador.php",fd); 
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
              name="frmCnt" 
              id="frmCnt" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Contador<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 390px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo05">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">COD.:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtNome" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtNome">CONTADOR:</label>
              </div>
              
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCrc" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtCrc">CRC:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCpf" 
                                           OnKeyPress="return mascaraInteiro(event);"                                                         
                                           type="text" 
                                           maxlength="14" />
                <label class="campo_label campo_required" for="edtCpf">CPF:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCnpj" 
                                           OnKeyPress="return mascaraInteiro(event);"                 
                                           type="text" 
                                           maxlength="14" />
                <label class="campo_label campo_required" for="edtCnpj">CNPJ:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input"  name="edtCep" id="edtCep" type="text" 
                       onFocus="cepFocus(this);" 
                       onBlur="cepBlur(this);"
                       OnKeyPress="return mascaraInteiro(event);" 
                       maxlength="8" />
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
              <div class="campotexto campo40">
                <input class="campo_input" id="edtEndereco" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtEndereco">ENDERECO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtNumero" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtNumero">NUMERO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtBairro" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtBairro">BAIRRO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodQc"
                                                    onBlur="CodQcBlur(this);" 
                                                    onFocus="qcFocus(this);" 
                                                    onClick="qcF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="4"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodQc">QUALIFIC:</label>
              </div>
              <div class="campotexto campo45">
                <input class="campo_input_titulo input" id="edtDesQc" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesQc">NOME_QUALIFICACAO:</label>
              </div>
              
              <div class="campotexto campo15">
                <input class="campo_input" id="edtFone" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtFone">FONE:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtEmail" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtEmail">EMAIL:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtSuframa" type="text" maxlength="9" />
                <label class="campo_label" for="edtSuframa">SUFRAMA:</label>
              </div>
              <div class="campotexto campo35">
                <select class="campo_input_combo" id="cbCodIncTrib">
                  <option value="1">NAO CUMULATIVO</option>
                  <option value="2">CUMULATIVO</option>
                  <option value="3">AMBOS</option>
                </select>
                <label class="campo_label" for="cbCodIncTrib">COD INCIDENCIA TRIBUT</label>
              </div>
              <div class="campotexto campo50">
                <select class="campo_input_combo" id="cbIndProc">
                  <option value="1">APROPRIACAO DIRETA</option>
                  <option value="2">RATEIO PROPORCIONAL</option>
                </select>
                <label class="campo_label" for="cbIndProc">INDICADOR APROPR CREDITOS:</label>
              </div>
              <div class="campotexto campo50">
                <select class="campo_input_combo" id="cbCodTipoCont">
                  <option value="1">BASICA</option>
                  <option value="2">DIFERENCIADA</option>
                </select>
                <label class="campo_label" for="cbCodTipoCont">INDICADOR CONTRIB APURADA:</label>
              </div>
              <div class="campotexto campo50">
                <select class="campo_input_combo" id="cbIndRegCum">
                  <option value="1">REGIME DE CAIXA</option>
                  <option value="2">CONSOLIDADO/COMPETENCIA</option>
                  <option value="9">DETALHADO/COMPETENCIA</option>
                </select>
                <label class="campo_label" for="cbIndRegCum">INDICADOR CRITERIO ESCRITUR:</label>
              </div>
              <div class="campotexto campo50">
                <select class="campo_input_combo" id="edtIndNatPj">
                  <option value="00">PJ</option>
                  <option value="01">COOPERATIVA</option>
                  <option value="02">SUJEITO PIS/PASEP</option>
                </select>
                <label class="campo_label" for="edtIndNatPj">INDICADOR NATUREZA PESSOA FJ:</label>
              </div>
              <div class="campotexto campo50">
                <select class="campo_input_combo" id="edtIndAtiv">
                  <option value="0">INDUSTRIA</option>
                  <option value="1">PRESTADOR</option>
                  <option value="2">COMERCIO</option>
                  <option value="3">FINANCEIRA</option>
                  <option value="4">IMOBILIARIA</option>
                  <option value="9">OUTRAS</option>
                </select>
                <label class="campo_label" for="edtIndAtiv">INDICADOR ATIVIDADE:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodRecPis" type="text" maxlength="1" />
                <label class="campo_label" for="edtCodRecPis">COD REC PIS:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodRecCofins" type="text" maxlength="1" />
                <label class="campo_label" for="edtCodRecCofins">COD REC COFINS:</label>
              </div>
              <div class="campotexto campo20">
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
                <input id="edtCodEmp" type="text" />
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