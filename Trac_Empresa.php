<?php
  session_start();
  if( isset($_POST["empresa"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/consultaCep.class.php");      
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["empresa"]);
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
        //    Dados para JavaScript EMPRESA        //
        /////////////////////////////////////////////
        if( $rotina=="selectEmp" ){
          $sql="";
          $sql.="SELECT A.EMP_CODIGO";
          $sql.="       ,A.EMP_NOME";
          $sql.="       ,A.EMP_APELIDO";
          $sql.="       ,A.EMP_CNPJ";
          $sql.="       ,A.EMP_INSESTAD";
          $sql.="       ,A.EMP_CODCDD";
          $sql.="       ,C.CDD_NOME";
          $sql.="       ,A.EMP_CODLGR";
          $sql.="       ,A.EMP_ENDERECO";
          $sql.="       ,A.EMP_NUMERO";
          $sql.="       ,A.EMP_CEP";
          $sql.="       ,A.EMP_BAIRRO";
          $sql.="       ,A.EMP_FONE";
          $sql.="       ,A.EMP_CODETF";
          $sql.="       ,E.ETF_NOME";
          $sql.="       ,A.EMP_ALIQCOFINS";
          $sql.="       ,A.EMP_ALIQPIS";
          $sql.="       ,A.EMP_ALIQCSLL";
          $sql.="       ,A.EMP_BCPRESUMIDO";
          $sql.="       ,A.EMP_ALIQIRPRESUMIDO";
          $sql.="       ,A.EMP_ALIQCSLLPRESUMIDO";
          $sql.="       ,A.EMP_ALIQIRRF";
          $sql.="       ,A.EMP_ANEXOSIMPLES";
          $sql.="       ,A.EMP_CODETP";
          $sql.="       ,T.ETP_NOME";
          $sql.="       ,A.EMP_CODERM";
          $sql.="       ,M.ERM_NOME";
          $sql.="       ,A.EMP_CODERT";
          $sql.="       ,R.ERT_NOME";
          $sql.="       ,A.EMP_SMTPUSERNAME";
          $sql.="       ,A.EMP_SMTPPASSWORD";
          $sql.="       ,A.EMP_SMTPHOST";
          $sql.="       ,A.EMP_SMTPPORT";
          $sql.="       ,A.EMP_CERTPATH";
          $sql.="       ,A.EMP_CERTSENHA";
          $sql.="       ,CONVERT(VARCHAR(10),A.EMP_CERTVALIDADE,127) AS EMP_CERTVALIDADE";		  
          $sql.="       ,CASE WHEN A.EMP_PRODHOMOL='P' THEN CAST('PRODUCAO' AS VARCHAR(8)) ELSE CAST('HOMOLOG' AS VARCHAR(7)) END AS EMP_PRODHOMOL"; 
          $sql.="       ,CASE WHEN A.EMP_CONTINGENCIA='S' THEN 'SIM' ELSE 'NAO' END AS EMP_CONTINGENCIA";          
          $sql.="       ,CASE WHEN A.EMP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS EMP_ATIVO";
          $sql.="       ,CASE WHEN A.EMP_REG='P' THEN 'PUB' WHEN A.EMP_REG='S' THEN 'SIS' ELSE 'ADM' END AS EMP_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.EMP_CODUSR";
          $sql.="  FROM EMPRESA A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.EMP_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN CIDADE C ON A.EMP_CODCDD=C.CDD_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESATRIBFED E ON A.EMP_CODETF=E.ETF_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESATIPO T ON A.EMP_CODETP=T.ETP_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESARAMO M ON A.EMP_CODERM=M.ERM_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESAREGTRIB R ON A.EMP_CODERT=R.ERT_CODIGO";
          $sql.="  WHERE (EMP_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')";
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
          $vldCampo = new validaCampo("VEMPRESA",0);
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
        // Atualizando o empresa de dados se opcao de insert/updade/delete //
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
    <title>Empresa</title>
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
        //   Objeto clsTable2017 EMPRESA        //
        //////////////////////////////////////////
        jsEmp={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"EMP_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                      
                      ,"pk"             : "S"
                      ,"autoIncremento" : "S"
                      ,"newRecord"      : ["0","this","this"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do empresa. Este campo é único e deve tem o formato 999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "EMP_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "25em"
                      ,"tamImp"         : "80"
                      ,"digitosMinMax"  : [3,40]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]
                      ,"ajudaCampo"     : ["Descrição do empresa."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "EMP_APELIDO"   
                      ,"labelCol"       : "APELIDO"
                      ,"obj"            : "edtApelido"
                      ,"tamGrd"         : "11em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Descrição do apelido do empresa."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"field"          :"EMP_CNPJ" 
                      ,"labelCol"       : "CNPJ"
                      ,"obj"            : "edtCnpj"
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
            ,{"id":5  ,"field"          :"EMP_INSESTAD" 
                      ,"labelCol"       : "IE"
                      ,"obj"            : "edtInsEstad"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "25"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|.|-"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,9]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          :"EMP_CODCDD" 
                      ,"labelCol"       : "CODCDD"
                      ,"obj"            : "edtCodCdd"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,7]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "CDD_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesCdd"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "EMP_CODLGR" 
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
            ,{"id":9  ,"field"         : "EMP_ENDERECO"   
                      ,"labelCol"       : "ENDERECO"
                      ,"obj"            : "edtEndereco"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "EMP_NUMERO"   
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
            ,{"id":11 ,"field"          : "EMP_CEP"   
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
            ,{"id":12 ,"field"         : "EMP_BAIRRO"   
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
            ,{"id":13 ,"field"          : "EMP_FONE"   
                      ,"labelCol"       : "FONE"
                      ,"obj"            : "edtFone"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "15"
                      ,"digitosMinMax"  : [3,10]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|-"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"field"          : "EMP_CODETF" 
                      ,"labelCol"       : "CODETF"
                      ,"obj"            : "edtCodEtf"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "ETF_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesEtf"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["SIMPLES","this","this"]
                      ,"digitosMinMax"  : [1,20]
                      ,"validar"        : ["notnull"]                      
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]
                      ,"ajudaCampo"     : ["Descrição do Logradouro para este empresa."]
                      ,"padrao":0}
            ,{"id":16 ,"field"          : "EMP_ALIQCOFINS"  
                      ,"labelCol"       : "ALIQCOFINS" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtCofins"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":17 ,"field"          : "EMP_ALIQPIS"  
                      ,"labelCol"       : "ALIQPIS" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtPis"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":18 ,"field"          : "EMP_ALIQCSLL"  
                      ,"labelCol"       : "ALIQCSLL" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtSll"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":19 ,"field"          : "EMP_BCPRESUMIDO"  
                      ,"labelCol"       : "BCPRESUMIDO" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtBcPresumido"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":20 ,"field"          : "EMP_ALIQIRPRESUMIDO"  
                      ,"labelCol"       : "ALIQIRPRESUMIDO" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtIrPresumido"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          : "EMP_ALIQCSLLPRESUMIDO"  
                      ,"labelCol"       : "ALIQCSLLPRESUMIDO" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtSllPresumido"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":22 ,"field"          : "EMP_ALIQIRRF"  
                      ,"labelCol"       : "ALIQIRRF" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtIrrf"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":23 ,"field"          : "EMP_ANEXOSIMPLES"  
                      ,"labelCol"       : "ANEXOSIMPLES" 
                      ,"newRecord"      : ["00","this","this"]
                      ,"obj"            : "edtAnexoSimples"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i2"]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"digitosMinMax"  : [2,2]
                      //,"validar"        : ["podeNull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":24 ,"field"          : "EMP_CODETP" 
                      ,"labelCol"       : "CODETP"
                      ,"obj"            : "edtCodEtp"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["SER","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":25 ,"field"          : "ETP_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesEtp"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["SERVICO","this","this"]
                      ,"digitosMinMax"  : [1,20]
                      ,"validar"        : ["notnull"]                      
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Descrição da EmpresaTipo para esta empresa."]
                      ,"padrao":0}
            ,{"id":26 ,"field"          : "EMP_CODERM" 
                      ,"labelCol"       : "CODERM"
                      ,"obj"            : "edtCodErm"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["SER","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":27 ,"field"          : "ERM_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesErm"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["SERVICO","this","this"]
                      ,"digitosMinMax"  : [1,25]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição da EmpresaTipo para esta empresa."]
                      ,"padrao":0}
            ,{"id":28 ,"field"          : "EMP_CODERT" 
                      ,"labelCol"       : "CODERT"
                      ,"obj"            : "edtCodErt"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["1","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":29 ,"field"          : "ERT_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesErt"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["SIMPLES NAC","this","this"]
                      ,"digitosMinMax"  : [1,25]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição da EmpresaTipo para esta empresa."]
                      ,"padrao":0}
            ,{"id":30 ,"field"          : "EMP_SMTPUSERNAME" 
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "SMTPUSERNAME"
                      ,"obj"            : "edtSmtpUser"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["podeNull"]
                      ,"digitosMinMax"  : [0,60]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":31 ,"field"          : "EMP_SMTPPASSWORD" 
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "SMTPPASSWORD"
                      ,"obj"            : "edtSmtpPwd"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["podeNull"]
                      ,"digitosMinMax"  : [0,30]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":32 ,"field"          : "EMP_SMTPHOST"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "SMTPHOST"
                      ,"obj"            : "edtSmtpHost"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["podeNull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"digitosMinMax"  : [0,30]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":33 ,"field"          : "EMP_SMTPPORT"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "SMTPPORT"
                      ,"obj"            : "edtSmtpPort"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"digitosMinMax"  : [0,4]
                      ,"validar"        : ["podeNull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":34 ,"field"          : "EMP_CERTPATH"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "CERTPATH"
                      ,"obj"            : "edtPath"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["lowercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"digitosMinMax"  : [0,100]
                      ,"validar"        : ["podeNull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":35 ,"field"          : "EMP_CERTSENHA"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "CERTSENHA"
                      ,"obj"            : "edtCertSenha"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [0,20]
                      ,"validar"        : ["podeNull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":36 ,"field"          : "EMP_CERTVALIDADE"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "CERTVALIDADE"
                      ,"obj"            : "edtDtCertValidade"
                      ,"fieldType"      : "dat"                                    
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "00"
                      ,"newRecord"      : ["01/01/1900","this","this"]
                      ,"validar"        : ["podeNull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":37  ,"field"         : "EMP_PRODHOMOL"   
                      ,"labelCol"       : "PRODHOMOL"
                      ,"obj"            : "cbProdHomol"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"tipo"           : "cb"
                      ,"newRecord"      : ["P","this","this"]
                      ,"ajudaCampo"     : ["Direito para opção..."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":38 ,"field"          : "EMP_CONTINGENCIA"   
                      ,"labelCol"       : "CONTINGENCIA"
                      ,"obj"            : "cbContingencia"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "00"
                      ,"tipo"           : "cb"
                      ,"newRecord"      : ["S","this","this"]
                      ,"ajudaCampo"     : ["Direito para opção..."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":39 ,"field"          : "EMP_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":40 ,"field"          : "EMP_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":41 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":42 ,"field"          : "EMP_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":43 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objEmp.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"EMPRESA - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":" Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"             }
            ,{"texto":" Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"  }
            ,{"texto":" Excluir"   ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-trash-o"            }
            ,{"texto":" Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"      }        
            ,{"texto":" Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"  }                       
           // ,// {"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"            }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmEmp"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaEmp"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmEmp"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblEmp"              // Nome da table
          ,"prefixo"        : "emp"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VEMPRESA"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPEMPRESA"       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "EMP_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "EMP_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "EMP_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"max-width"      : "105em"                   // Tamanho máximo da table //Angelo kokiso - mudança no tamanho da tabela
          ,"width"          : "min-content"                    // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "EMPRESA"          // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
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
                               //,["Importar planilha excel"              ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objEmp.AjudaSisAtivo(jsEmp);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objEmp.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objEmp.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objEmp.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objEmp.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objEmp.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objEmp.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "EMPRESA[03]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objEmp === undefined ){  
          objEmp=new clsTable2017("objEmp");
        };  
        objEmp.montarHtmlCE2017(jsEmp); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsEmp.form).style.width=jsEmp.width;
        btnFiltrarClick("S");  
      });
      //
      var objEmp;                     // Obrigatório para instanciar o JS TFormaCob
      var jsEmp;                      // Obj principal da classe clsTable2017
      //var objExc;                     // Obrigatório para instanciar o JS Importar excel
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
        clsJs.add("rotina"      , "selectEmp"                               );
        clsJs.add("login"       , jsPub[0].usr_login                        );
        clsJs.add("ativo"       , atv                                       );
        clsJs.add("codEmp"      , document.getElementById("edtCodigo").value );    
        fd = new FormData();
        fd.append("empresa" , clsJs.fim());
        msg     = requestPedido("Trac_Empresa.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsEmp.registros=objEmp.addIdUnico(retPhp[0]["dados"]);
          objEmp.ordenaJSon(jsEmp.indiceTable,false);  
          objEmp.montarBody2017();
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
            clsJs.add("titulo"      , objEmp.trazCampoExcel(jsEmp));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("empresa"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Empresa.php",fd); 
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
            gerarMensagemErro("EMP",retPhp[0].erro,{cabec:"Aviso"});    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
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
      ////////////////////////////
      //  AJUDA PARA CIDADE     //
      ////////////////////////////
      function cddFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function cddF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtFone"
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
                                  ,foco:"edtFone"
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
      ////////////////////////////////
      //  AJUDA PARA EMPRESATRIBFED //
      ////////////////////////////////
      function etfFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function etfF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCofins"
                      ,topo:100
                      ,tableBd:"EMPRESATRIBFED"
                      ,fieldCod:"A.ETF_CODIGO"
                      ,fieldDes:"A.ETF_NOME"
                      ,fieldAtv:"A.ETF_ATIVO"
                      ,typeCod :"str" 
					  ,tbl:"tblEtf"}
        );
      };
      function RetF10tblEtf(arr){
        document.getElementById("edtCodEtf").value  = arr[0].CODIGO;
        document.getElementById("edtDesEtf").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodEtf").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodEtfBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCofins"
                                  ,topo:100
                                  ,tableBd:"EMPRESATRIBFED"
                                  ,fieldCod:"A.ETF_CODIGO"
                                  ,fieldDes:"A.ETF_NOME"
                                  ,fieldAtv:"A.ETF_ATIVO"
                                  ,typeCod :"str" 
								  ,tbl:"tblEtf"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "S"  : ret[0].CODIGO             );
          document.getElementById("edtDesEtf").value  = ( ret.length == 0 ? "SIMPLES"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "S" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////////
      //  AJUDA PARA EMPRESATIPO    //
      ////////////////////////////////
      function etpFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function etpF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodErm"
                      ,topo:100
                      ,tableBd:"EMPRESATIPO"
                      ,fieldCod:"A.ETP_CODIGO"
                      ,fieldDes:"A.ETP_NOME"
                      ,fieldAtv:"A.ETP_ATIVO"
                      ,typeCod :"str" 
					  ,tbl:"tblEtp"}
        );
      };
      function RetF10tblEtp(arr){
        document.getElementById("edtCodEtp").value  = arr[0].CODIGO;
        document.getElementById("edtDesEtp").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodEtp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodEtpBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodErm"
                                  ,topo:100
                                  ,tableBd:"EMPRESATIPO"
                                  ,fieldCod:"A.ETP_CODIGO"
                                  ,fieldDes:"A.ETP_NOME"
                                  ,fieldAtv:"A.ETP_ATIVO"
                                  ,typeCod :"str" 
								  ,tbl:"tblEtp"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "SER"  : ret[0].CODIGO             );
          document.getElementById("edtDesEtf").value  = ( ret.length == 0 ? "SERVICO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "SER" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////////
      //  AJUDA PARA EMPRESARAMO    //
      ////////////////////////////////
      function ermFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ermF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodErt"
                      ,topo:100
                      ,tableBd:"EMPRESARAMO"
                      ,fieldCod:"A.ERM_CODIGO"
                      ,fieldDes:"A.ERM_NOME"
                      ,fieldAtv:"A.ERM_ATIVO"
                      ,typeCod :"str" 
					  ,tbl:"tblErm"}
        );
      };
      function RetF10tblErm(arr){
        document.getElementById("edtCodErm").value  = arr[0].CODIGO;
        document.getElementById("edtDesErm").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodErm").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodErmBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodErt"
                                  ,topo:100
                                  ,tableBd:"EMPRESARAMO"
                                  ,fieldCod:"A.ERM_CODIGO"
                                  ,fieldDes:"A.ERM_NOME"
                                  ,fieldAtv:"A.ERM_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblErm"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "SER"  : ret[0].CODIGO             );
          document.getElementById("edtDesErm").value  = ( ret.length == 0 ? "SERVICO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "SER" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////////
      //  AJUDA PARA EMPRESAREGTRIB    //
      ////////////////////////////////
      function ertFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ertF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtSmtpUser"
                      ,topo:100
                      ,tableBd:"EMPRESAREGTRIB"
                      ,fieldCod:"A.ERT_CODIGO"
                      ,fieldDes:"A.ERT_NOME"
                      ,fieldAtv:"A.ERT_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblErt"}
        );
      };
      function RetF10tblErt(arr){
        document.getElementById("edtCodErt").value  = arr[0].CODIGO;
        document.getElementById("edtDesErt").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodErt").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodErtBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtSmtpUser"
                                  ,topo:100
                                  ,tableBd:"EMPRESAREGTRIB"
                                  ,fieldCod:"A.ERT_CODIGO"
                                  ,fieldDes:"A.ERT_NOME"
                                  ,fieldAtv:"A.ERT_ATIVO"
                                  ,typeCod :"str" 
								  ,tbl:"tblErt"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "1"  : ret[0].CODIGO             );
          document.getElementById("edtDesErt").value  = ( ret.length == 0 ? "SIMPLES NAC"      : ret[0].DESCRICAO  );
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
              name="frmEmp" 
              id="frmEmp" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Empresa<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 470px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="40" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO:</label>
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
              <div class="campotexto campo20">
                <input class="campo_input" id="edtInsEstad" 
                                           type="text" 
                                           OnKeyPress="return mascaraInteiro(event);" 
                                           maxlength="9" />
                <label class="campo_label" for="edtInsEstad">IE:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input"  name="edtCep" id="edtCep" type="text" 
                       onFocus="cepFocus(this);" 
                       onBlur="cepBlur(this);"
                       OnKeyPress="return mascaraInteiro(event);" 
                       maxlength="8" />
                <label class="campo_label campo_required" for="edtCep">CEP</label>
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
              <div class="campotexto campo40">
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
                <label class="campo_label campo_required" for="edtCodLgr">CODLGR:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtEndereco" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtEndereco">ENDERECO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtNumero" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtNumero">NUMERO:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtBairro" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtBairro">BAIRRO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtFone" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtFone">FONE:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodEtf"
                                                    onBlur="CodEtfBlur(this);" 
                                                    onFocus="etfFocus(this);" 
                                                    onClick="etfF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodEtf">TRIB FED:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesEtf" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesEtf">NOME:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtCofins" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtCofins">%COFINS</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtPis" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtPis">%PIS</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtSll" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtSll">%CSLL</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtBcPresumido" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtBcPresumido">%BC PRESUM</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtIrPresumido" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtIrPresumido">%IR PRESUM</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtSllPresumido" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtSllPresumido">%CSLL PRESUM</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtIrrf" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtIrrf">%IRRF</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtAnexoSimples" 
                                                      type="text" 
                                                      onBlur="mascaraInteiro(this);"
                                                      maxlength="2" />
                <label class="campo_label" for="edtAnexoSimples">ANEXOSIMPLES</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodEtp"
                                                    onBlur="CodEtpBlur(this);" 
                                                    onFocus="etpFocus(this);" 
                                                    onClick="etpF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodEtp">TIPO:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesEtp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesEtp">NOME:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodErm"
                                                    onBlur="CodErmBlur(this);" 
                                                    onFocus="ermFocus(this);" 
                                                    onClick="ermF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodErm">RAMO:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesErm" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesErm">NOME:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodErt"
                                                    onBlur="CodErtBlur(this);" 
                                                    onFocus="ertFocus(this);" 
                                                    onClick="ertF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodErt">REG TRIBUT:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesErt" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesErt">NOME:</label>
              </div>
              <div class="campotexto campo50">
                      <input class="campo_input" id="edtSmtpUser" type="text" maxlength="60" />
                      <label class="campo_label" for="edtSmtpUser">SMTPUSERNAME:</label>
                    </div>
              <div class="campotexto campo50">
                      <input class="campo_input" id="edtSmtpPwd" type="text" maxlength="30" />
                      <label class="campo_label" for="edtSmtpPwd">SMTPPASSWORD:</label>
                    </div>
              <div class="campotexto campo30">
                      <input class="campo_input" id="edtSmtpHost" type="text" maxlength="30" />
                      <label class="campo_label" for="edtSmtpHost">SMTPHOST:</label>
                    </div>
              <div class="campotexto campo15">
                      <input class="campo_input" id="edtSmtpPort" type="text" maxlength="4" />
                      <label class="campo_label" for="edtSmtpPort">SMTPPORT:</label>
                    </div>
              <div class="campotexto campo15">
                      <input class="campo_input" id="edtPath" type="text" maxlength="100" />
                      <label class="campo_label" for="edtPath">CERTPATH:</label>
                    </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtCertSenha" type="text" maxlength="20" />
                <label class="campo_label" for="edtCertSenha">CERTSENHA:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtDtCertValidade" 
                                           type="text" 
                                           placeholder="##/##/####" 
                                           onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                           maxlength="10" />
                <label class="campo_label" for="edtDtCertValidade">CERTVALIDADE:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbProdHomol">
                  <option value="P">PRODUCAO</option>
                  <option value="H">HOMOLOG</option>
                </select>
                <label class="campo_label campo_required" for="cbProdHomol">PRODHOMOL:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbContingencia">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbContingencia">CONTINGENCIA:</label>
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