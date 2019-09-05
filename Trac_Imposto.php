<?php
  session_start();
  if( isset($_POST["imposto"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["imposto"]);
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
        //    Dados para JavaScript IMPOSTO        //
        /////////////////////////////////////////////
        if( $rotina=="selectImp" ){
          $sql="";
          $sql.="SELECT A.IMP_UFDE";
          $sql.="       ,E.EST_NOME";
          $sql.="       ,A.IMP_UFPARA";
          $sql.="       ,E.EST_NOME";
          $sql.="       ,A.IMP_CODNCM";
          $sql.="       ,N.NCM_NOME";
          $sql.="       ,A.IMP_CODCTG";
          $sql.="       ,C.CTG_NOME";
          $sql.="       ,CASE WHEN A.IMP_ENTSAI='S' THEN 'SAI' ELSE 'ENT' END AS IMP_ENTSAI";
          $sql.="       ,A.IMP_CODNO";
          $sql.="       ,O.NO_NOME";
          $sql.="       ,A.IMP_CFOP";
          $sql.="       ,F.CFO_NOME";
          $sql.="       ,A.IMP_CSTICMS";
          $sql.="       ,I.ICM_NOME";
          $sql.="       ,A.IMP_ALIQICMS";
          $sql.="       ,A.IMP_REDUCAOBC";
          $sql.="       ,A.IMP_CSTIPI";
          $sql.="       ,P.IPI_NOME";
          $sql.="       ,A.IMP_ALIQIPI";
          $sql.="       ,A.IMP_CSTPIS";
          $sql.="       ,T.PIS_NOME";
          $sql.="       ,A.IMP_ALIQPIS";
          $sql.="       ,A.IMP_CSTCOFINS";
          $sql.="       ,A.IMP_ALIQCOFINS";
          $sql.="       ,A.IMP_ALIQST";
          $sql.="       ,CASE WHEN A.IMP_ALTERANFP='S' THEN 'SIM' ELSE 'NAO' END AS IMP_ALTERANFP";
          $sql.="       ,A.IMP_CODFLL";
          $sql.="       ,L.FLL_APELIDO";
          $sql.="       ,A.IMP_CODEMP";
          $sql.="       ,M.EMP_APELIDO";
          $sql.="       ,CASE WHEN A.IMP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS IMP_ATIVO";
          $sql.="       ,CASE WHEN A.IMP_REG='P' THEN 'PUB' WHEN A.IMP_REG='S' THEN 'SIS' ELSE 'ADM' END AS IMP_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.IMP_CODUSR";
          $sql.="  FROM IMPOSTO A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.IMP_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN ESTADO E ON A.IMP_UFDE=E.EST_CODIGO";
          $sql.="  LEFT OUTER JOIN NCM N ON A.IMP_CODNCM=N.NCM_CODIGO";
          $sql.="  LEFT OUTER JOIN CATEGORIA C ON A.IMP_CODCTG=C.CTG_CODIGO";
          $sql.="  LEFT OUTER JOIN NATUREZAOPERACAO O ON A.IMP_CODNO=O.NO_CODIGO";
          $sql.="  LEFT OUTER JOIN CFOP F ON A.IMP_CFOP=F.CFO_CODIGO";
          $sql.="  LEFT OUTER JOIN CSTICMS I ON (A.IMP_CSTICMS=I.ICM_CODIGO AND A.IMP_ENTSAI=I.ICM_ENTSAI)";
          $sql.="  LEFT OUTER JOIN CSTIPI P ON A.IMP_CSTIPI=P.IPI_CODIGO";
          $sql.="  LEFT OUTER JOIN CSTPIS T ON A.IMP_CSTPIS=T.PIS_CODIGO";
          $sql.="  LEFT OUTER JOIN FILIAL L ON A.IMP_CODFLL=L.FLL_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA M ON A.IMP_CODEMP=M.EMP_CODIGO";
          $sql.="  WHERE ((IMP_CODEMP=".$lote[0]->codemp.")";
          $sql.="   AND ((IMP_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
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
          $vldCampo = new validaCampo("VIMPOSTO",0);
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
        // Atualizando o imposto de dados se opcao de insert/updade/delete //
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
    <title>Imposto</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="js/parseTable.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        ///////////////////////////////////////
        //   Objeto clsTable2017 IMPOSTO     //
        ///////////////////////////////////////
        jsImp={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"IMP_UFDE" 
                      ,"pk"             : "S"            
                      ,"labelCol"       : "UFDE"
                      ,"obj"            : "edtUfDe"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "18"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z"
                      ,"newRecord"      : [jsPub[0].emp_codest,"this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "EST_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesUfDe"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"IMP_UFPARA" 
                      ,"pk"             : "S"            
                      ,"labelCol"       : "UFPARA"
                      ,"obj"            : "edtCodUfPara"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "18"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z"
                      ,"newRecord"      : [jsPub[0].emp_codest,"this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "EST_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesUfPara"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":5  ,"field"          :"IMP_CODNCM" 
                      ,"pk"             : "S"            
                      ,"labelCol"       : "NCM"
                      ,"obj"            : "edtCodNcm"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "18"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,10]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "NCM_NOME"   
                      ,"labelCol"       : "NCM"
                      ,"obj"            : "edtDesNcm"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|.|,|/| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":7  ,"field"          :"IMP_CODCTG" 
                      ,"pk"             : "S"            
                      ,"labelCol"       : "CODCTG"
                      ,"obj"            : "edtCodCtg"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z"
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "CTG_NOME"   
                      ,"labelCol"       : "CTG"
                      ,"obj"            : "edtDesCtg"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "IMP_ENTSAI"  
                      ,"pk"             : "S"            
                      ,"labelCol"       : "ES" 
                      ,"obj"            : "cbEntSai"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":10 ,"field"          :"IMP_CODNO" 
                      ,"pk"             : "S"            
                      ,"labelCol"       : "CODNO"
                      ,"obj"            : "edtCodNo"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "12"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,2]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":11 ,"field"          : "NO_NOME"   
                      ,"labelCol"       : "NO"
                      ,"obj"            : "edtDesNo"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":12 ,"field"          :"IMP_CFOP" 
                      ,"labelCol"       : "CFOP"
                      ,"obj"            : "edtCodCfo"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "12"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,5]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":13 ,"field"          : "CFO_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDesCfo"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,50]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|.| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":14 ,"field"          :"IMP_CSTICMS" 
                      ,"labelCol"       : "CSTICMS"
                      ,"obj"            : "edtCodIcm"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "ICMS_NOME"   
                      ,"labelCol"       : "ICMS"
                      ,"obj"            : "edtDesIcm"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|.| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":16 ,"field"          : "IMP_ALIQICMS"  
                      ,"labelCol"       : "ALIQICMS%" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtAliqIcms"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":17 ,"field"          : "IMP_REDUCAOBC"  
                      ,"labelCol"       : "REDUCAOBC" 
                      ,"obj"            : "edtReducaoAbc"
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":18 ,"field"          :"IMP_CSTIPI" 
                      ,"labelCol"       : "CSTIPI"
                      ,"obj"            : "edtCodIpi"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":19 ,"field"          : "IPI_NOME"   
                      ,"labelCol"       : "IPI"
                      ,"obj"            : "edtDesIpi"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
            ,{"id":20 ,"field"          : "IMP_ALIQIPI"  
                      ,"labelCol"       : "ALIQIPI%" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtAliqIpi"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          :"IMP_CSTPIS" 
                      ,"labelCol"       : "CSTPIS"
                      ,"obj"            : "edtCodPis"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Código do CSTPIS"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":22 ,"field"          : "PIS_NOME"   
                      ,"labelCol"       : "PIS"
                      ,"obj"            : "edtDesPis"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|.|-|,|/| "
                      ,"ajudaCampo"     : ["Descrição do PIS."]
                      ,"padrao":0}
            ,{"id":23 ,"field"          : "IMP_ALIQPIS"  
                      ,"labelCol"       : "ALIQPIS%" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtAliqPis"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}  
            ,{"id":24 ,"field"          :"IMP_CSTCOFINS" 
                      ,"labelCol"       : "CSTCOFINS"
                      ,"obj"            : "edtCodCofins"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Código do CSTCOFINS."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":25 ,"field"          : "IMP_ALIQCOFINS"  
                      ,"labelCol"       : "ALIQCOFINS%" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtAliqCofins"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":26 ,"field"          : "IMP_ALIQST"  
                      ,"labelCol"       : "ALIQST%" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtAliqSt"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":27 ,"field"          : "IMP_ALTERANFP"  
                      ,"labelCol"       : "ALTERANFP" 
                      ,"obj"            : "cbAlteraNfp"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "17"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["N","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":28 ,"field"          :"IMP_CODFLL" 
                      ,"labelCol"       : "FILIAL"
                      ,"obj"            : "edtCodFll"
                      ,"pk"             : "S"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Código da Cidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":29 ,"field"          : "FLL_APELIDO"   
                      ,"labelCol"       : "FILIAL"
                      ,"obj"            : "edtDesFll"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"padrao":0}
           ,{"id":30  ,"field"          : "IMP_CODEMP" 
                      ,"pk"             : "S"
                      ,"labelCol"       : "EMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"importaExcel"   : "N"
                      ,"padrao":7}					  
            ,{"id":31 ,"field"          : "EMP_APELIDO"   
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
            ,{"id":32 ,"field"          : "IMP_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":33 ,"field"          : "IMP_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":34 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":35 ,"field"          : "IMP_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":36 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objImp.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"IMPOSTO - Detalhe do registro"
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
          ,"div"            : "frmImp"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaImp"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmImp"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblImp"              // Nome da table
          ,"prefixo"        : "imp"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VIMPOSTO"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPIMPOSTO"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "IMP_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "IMP_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "IMP_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "90em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "IMPOSTO"             // Titulo do relatório
          ,"relOrientacao"  : "R"                   // Paisagem ou retrato
          ,"relFonte"       : "7"                   // Fonte do relatório
          ,"foco"           : ["edtUfDe"
                              ,"edtCodCfo"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objImp.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objImp.AjudaSisAtivo(jsImp);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objImp.detalhe();"]
                               //,["Gerar excel"                          ,"fa-file-excel-o"  ,"objImp.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objImp.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objImp.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objImp.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objImp.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objImp.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "IMPOSTO[23]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objImp === undefined ){  
          objImp=new clsTable2017("objImp");
        };  
        objImp.montarHtmlCE2017(jsImp); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsImp.form).style.width=jsImp.width;
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"UFDE"         ,"labelCol":"UFDE"          ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":1  ,"field":"UFPARA"       ,"labelCol":"UFPARA"        ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":2  ,"field":"NCM"          ,"labelCol":"NCM"           ,"tamGrd":"10em"     ,"tamImp":"20" }	
            ,{"id":3  ,"field":"CODCTG"       ,"labelCol":"CATEGORIA"     ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":4  ,"field":"ES"           ,"labelCol":"ENTSAI"        ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":5  ,"field":"CODNO"        ,"labelCol":"NAT_OPERACAO"  ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":6  ,"field":"CFOP"         ,"labelCol":"CFOP"          ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":7  ,"field":"CSTICMS"      ,"labelCol":"CST_ICMS"      ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":8  ,"field":"ALIQICMS%"    ,"labelCol":"ALIQ_ICMS"     ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":9  ,"field":"REDUCAOBC"    ,"labelCol":"REDUCAO_BC"    ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":10 ,"field":"CSTIPI"       ,"labelCol":"CST_IPI"       ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":11 ,"field":"ALIQIPI%"     ,"labelCol":"ALIQ_IPI"      ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":12 ,"field":"CSTPIS"       ,"labelCol":"CST_PIS"       ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":13 ,"field":"ALIQPIS%"     ,"labelCol":"ALIQ_PIS"      ,"tamGrd":"6em"     ,"tamImp":"20"  }
            ,{"id":14 ,"field":"CSTCOFINS"    ,"labelCol":"CST_COFINS"    ,"tamGrd":"6em"     ,"tamImp":"20"  }
            ,{"id":15 ,"field":"ALIQCOFINS%"  ,"labelCol":"ALIQ_COFINS"   ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":16 ,"field":"ALIQST%"      ,"labelCol":"ALIQ_ST"       ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":17 ,"field":"ALTERANFP"    ,"labelCol":"ALTERAR_NF"    ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":18 ,"field":"FILIAL"       ,"labelCol":"FILIAL"        ,"tamGrd":"6em"     ,"tamImp":"20"  }	
            ,{"id":19 ,"field":"ERRO"         ,"labelCol":"ERRO"          ,"tamGrd":"45em"    ,"tamImp":"100" }            
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[19].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
      var objImp;                     // Obrigatório para instanciar o JS TFormaCob
      var jsImp;                      // Obj principal da classe clsTable2017
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
      var intCodDir = parseInt(jsPub[0].usr_d23);
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
        clsJs.add("rotina"      , "selectImp"                               );
        clsJs.add("login"       , jsPub[0].usr_login                        );
        clsJs.add("ativo"       , atv                                       );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("imposto" , clsJs.fim());
        msg     = requestPedido("Trac_Imposto.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsImp.registros=objImp.addIdUnico(retPhp[0]["dados"]);
          objImp.ordenaJSon(jsImp.indiceTable,false);  
          objImp.montarBody2017();
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
            clsJs.add("titulo"      , objImp.trazCampoExcel(jsImp));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("imposto"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Imposto.php",fd); 
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
            gerarMensagemErro("IMP",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
        };          
      };
      //////////////////////////////
      //  AJUDA PARA ESTADO       //
      //////////////////////////////
      function estFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function estF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodUfPara"
                      ,topo:100
                      ,tableBd:"ESTADO"
                      ,fieldCod:"A.EST_CODIGO"
                      ,fieldDes:"A.EST_NOME"
                      ,fieldAtv:"A.EST_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblEst"}
        );
      };
      function RetF10tblEst(arr){
        document.getElementById("edtUfDe").value  = arr[0].CODIGO;
        document.getElementById("edtDesUfDe").value  = arr[0].DESCRICAO;
        document.getElementById("edtUfDe").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodEstBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodUfPara"
                                  ,topo:100
                                  ,tableBd:"ESTADO"
                                  ,fieldCod:"A.EST_CODIGO"
                                  ,fieldDes:"A.EST_NOME"
                                  ,fieldAtv:"A.EST_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblEst"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "SP"  : ret[0].CODIGO             );
          document.getElementById("edtDesUfDe").value  = ( ret.length == 0 ? "SAO PAULO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "SP" : ret[0].CODIGO )  );
        };
      };
      //////////////////////////////
      //  AJUDA PARA ESTADO       //
      //////////////////////////////
      function ufpFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ufpF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodNcm"
                      ,topo:100
                      ,tableBd:"ESTADO"
                      ,fieldCod:"A.EST_CODIGO"
                      ,fieldDes:"A.EST_NOME"
                      ,fieldAtv:"A.EST_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblUfp"}
        );
      };
      function RetF10tblUfp(arr){
        document.getElementById("edtCodUfPara").value  = arr[0].CODIGO;
        document.getElementById("edtDesUfPara").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodUfPara").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodUfpBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodNcm"
                                  ,topo:100
                                  ,tableBd:"ESTADO"
                                  ,fieldCod:"A.EST_CODIGO"
                                  ,fieldDes:"A.EST_NOME"
                                  ,fieldAtv:"A.EST_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblUfp"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "SP"  : ret[0].CODIGO             );
          document.getElementById("edtDesUfPara").value  = ( ret.length == 0 ? "SAO PAULO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "SP" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////
      //  AJUDA PARA NCM     //
      /////////////////////////
      function ncmFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ncmF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodCtg"
                      ,topo:100
                      ,tableBd:"NCM"
                      ,fieldCod:"A.NCM_CODIGO"
                      ,fieldDes:"A.NCM_NOME"
                      ,fieldAtv:"A.NCM_ATIVO"
                      ,typeCod :"str" 
                      ,tamColCodigo:"8em"
                      ,tamColNome:"28em"
                      ,tbl:"tblNcm"}
        );
      };
      function RetF10tblNcm(arr){
        document.getElementById("edtCodNcm").value  = arr[0].CODIGO;
        document.getElementById("edtDesNcm").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodNcm").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodNcmBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.value;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodCtg"
                                  ,topo:100
                                  ,tableBd:"NCM"
                                  ,fieldCod:"A.NCM_CODIGO"
                                  ,fieldDes:"A.NCM_NOME"
                                  ,fieldAtv:"A.NCM_ATIVO"
                                  ,typeCod :"str" 
                                  ,tamColCodigo:"8em"
                                  ,tamColNome:"28em"
                                  ,tbl:"tblNcm"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "3820.00.00"  : ret[0].CODIGO             );
          document.getElementById("edtDesNcm").value  = ( ret.length == 0 ? "ADITIVO RADIADOR"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "3820.00.00" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //  AJUDA PARA CATEGORIA     //
      ///////////////////////////////
      function ctgFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ctgF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodNo"
                      ,topo:100
                      ,tableBd:"CATEGORIA"
                      ,fieldCod:"A.CTG_CODIGO"
                      ,fieldDes:"A.CTG_NOME"
                      ,fieldAtv:"A.CTG_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblCtg"}
        );
      };
      function RetF10tblCtg(arr){
        document.getElementById("edtCodCtg").value  = arr[0].CODIGO;
        document.getElementById("edtDesCtg").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCtg").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCtgBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodNo"
                                  ,topo:100
                                  ,tableBd:"CATEGORIA"
                                  ,fieldCod:"A.CTG_CODIGO"
                                  ,fieldDes:"A.CTG_NOME"
                                  ,fieldAtv:"A.CTG_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblCtg"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "NOR"  : ret[0].CODIGO             );
          document.getElementById("edtDesCtg").value  = ( ret.length == 0 ? "NORMAL"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "NOR" : ret[0].CODIGO )  );
        };
      };
      //////////////////////////////////
      //  AJUDA PARA NATUREZAOPERACAO //
      //////////////////////////////////
      function noFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function noF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodCfo"
                      ,topo:100
                      ,tableBd:"NATUREZAOPERACAO"
                      ,fieldCod:"A.NO_CODIGO"
                      ,fieldDes:"A.NO_NOME"
                      ,fieldAtv:"A.NO_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblNo"}
        );
      };
      function RetF10tblNo(arr){
        document.getElementById("edtCodNo").value  = arr[0].CODIGO;
        document.getElementById("edtDesNo").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodNo").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodNoBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodCfo"
                                  ,topo:100
                                  ,tableBd:"NATUREZAOPERACAO"
                                  ,fieldCod:"A.NO_CODIGO"
                                  ,fieldDes:"A.NO_NOME"
                                  ,fieldAtv:"A.NO_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblNo"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "1"  : ret[0].CODIGO             );
          document.getElementById("edtDesNo").value  = ( ret.length == 0 ? "VENDA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "1" : ret[0].CODIGO )  );
        };
      };
      /////////////////////
      //  AJUDA PARA CFO //
      /////////////////////
      function cfoFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function cfoF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodIcm"
                      ,topo:100
                      ,tableBd:"CFOP"
                      ,fieldCod:"A.CFO_CODIGO"
                      ,fieldDes:"A.CFO_NOME"
                      ,fieldAtv:"A.CFO_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblCfo"
                      ,where:" AND A.CFO_ENTSAI = '" +document.getElementById("cbEntSai").value +"'"
                    }
        );
      };
      function RetF10tblCfo(arr){
        document.getElementById("edtCodCfo").value  = arr[0].CODIGO;
        document.getElementById("edtDesCfo").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCfo").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCfoBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodIcm"
                                  ,topo:100
                                  ,tableBd:"CFOP"
                                  ,fieldCod:"A.CFO_CODIGO"
                                  ,fieldDes:"A.CFO_NOME"
                                  ,fieldAtv:"A.CFO_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblCfo"
                                  ,where:" AND A.CFO_ENTSAI = '" +document.getElementById("cbEntSai").value +"'"
                                }
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "5.102"  : ret[0].CODIGO             );
          document.getElementById("edtDesCfo").value  = ( ret.length == 0 ? "VENDA DE MERC.ADQ.DE TERC.DENTRO ESTADO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "5.102" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////
      //  AJUDA PARA CSTICMS //
      /////////////////////////
      function icmFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function icmF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtAliqIcms"
                      ,topo:100
                      ,tableBd:"CSTICMS"
                      ,fieldCod:"A.ICM_CODIGO"
                      ,fieldDes:"A.ICM_NOME"
                      ,fieldAtv:"A.ICM_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblIcm"
                      ,where:" AND A.ICM_ENTSAI = '" +document.getElementById("cbEntSai").value +"'"
                    }
        );
      };
      function RetF10tblIcm(arr){
        document.getElementById("edtCodIcm").value  = arr[0].CODIGO;
        document.getElementById("edtDesIcm").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodIcm").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodIcmBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtAliqIcms"
                                  ,topo:100
                                  ,tableBd:"CSTICMS"
                                  ,fieldCod:"A.ICM_CODIGO"
                                  ,fieldDes:"A.ICM_NOME"
                                  ,fieldAtv:"A.ICM_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblIcm"
                                  ,where:" AND A.ICM_ENTSAI = '" +document.getElementById("cbEntSai").value +"'"
                                }
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "00"  : ret[0].CODIGO             );
          document.getElementById("edtDesIcm").value  = ( ret.length == 0 ? "TRIBUTADA INTEGRALMENTE"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "00" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////
      //  AJUDA PARA CSTIPI  //
      /////////////////////////
      function ipiFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function ipiF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtAliqIpi"
                      ,topo:100
                      ,tableBd:"CSTIPI"
                      ,fieldCod:"A.IPI_CODIGO"
                      ,fieldDes:"A.IPI_NOME"
                      ,fieldAtv:"A.IPI_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblIpi"
                      ,where:" AND A.IPI_ENTSAI = '" +document.getElementById("cbEntSai").value +"'"
                    }
        );
      };
      function RetF10tblIpi(arr){
        document.getElementById("edtCodIpi").value  = arr[0].CODIGO;
        document.getElementById("edtDesIpi").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodIpi").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodIpiBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtAliqIpi"
                                  ,topo:100
                                  ,tableBd:"CSTIPI"
                                  ,fieldCod:"A.IPI_CODIGO"
                                  ,fieldDes:"A.IPI_NOME"
                                  ,fieldAtv:"A.IPI_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblIpi"
                                  ,where:" AND A.ICM_ENTSAI = '" +document.getElementById("cbEntSai").value +"'"
                                }
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "00"  : ret[0].CODIGO             );
          document.getElementById("edtDesIpi").value  = ( ret.length == 0 ? "ENTRADA RECUP CREDITO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "00" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////
      //  AJUDA PARA CSTPIS  //
      /////////////////////////
      function pisFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function pisF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtAliqPis"
                      ,topo:100
                      ,tableBd:"CSTPIS"
                      ,fieldCod:"A.PIS_CODIGO"
                      ,fieldDes:"A.PIS_NOME"
                      ,fieldAtv:"A.PIS_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblPis"}
        );
      };
      function RetF10tblPis(arr){
        document.getElementById("edtCodPis").value  = arr[0].CODIGO;
        document.getElementById("edtDesPis").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodPis").setAttribute("data-oldvalue",arr[0].CODIGO);
        // POPULA OS CAMPOS CSTCOFINS
        document.getElementById("edtCodCofins").value  = document.getElementById("edtCodPis").value;
      };
      function CodPisBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtAliqPis"
                                  ,topo:100
                                  ,tableBd:"CSTPIS"
                                  ,fieldCod:"A.PIS_CODIGO"
                                  ,fieldDes:"A.PIS_NOME"
                                  ,fieldAtv:"A.PIS_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblPis"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "49"  : ret[0].CODIGO             );
          document.getElementById("edtDesPis").value  = ( ret.length == 0 ? "OUTRAS OPERAC SAIDA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "49" : ret[0].CODIGO )  );
		  //
     	  document.getElementById("edtCodCofins").value  = document.getElementById(obj.id).value;
        };
      };
      /////////////////////////
      //  AJUDA PARA FILIAL  //
      /////////////////////////
      function fllFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fllF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"cbAtivo"
                      ,topo:100
                      ,tableBd:"FILIAL"
                      ,fieldCod:"A.FLL_CODIGO"
                      ,fieldDes:"A.FLL_APELIDO"
                      ,fieldAtv:"A.FLL_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblFll"}
        );
      };
      function RetF10tblFll(arr){
        document.getElementById("edtCodFll").value  = arr[0].CODIGO;
        document.getElementById("edtDesFll").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFll").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodFllBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbAtivo"
                                  ,topo:100
                                  ,tableBd:"FILIAL"
                                  ,fieldCod:"A.FLL_CODIGO"
                                  ,fieldDes:"A.FLL_APELIDO"
                                  ,fieldAtv:"A.FLL_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblFll"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "1001"  : ret[0].CODIGO             );
          document.getElementById("edtDesFll").value  = ( ret.length == 0 ? "TOTALTRAC LTDA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "1001" : ret[0].CODIGO )  );
        };
      };
      //////////////////////////
      //  AJUDA PARA EMPRESA  //
      //////////////////////////
      function empFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function empF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"cbAtivo"
                      ,topo:100
                      ,tableBd:"EMPRESA"
                      ,fieldCod:"A.EMP_CODIGO"
                      ,fieldDes:"A.EMP_APELIDO"
                      ,fieldAtv:"A.EMP_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblEmp"}
        );
      };
      function RetF10tblEmp(arr){
        document.getElementById("edtCodEmp").value  = arr[0].CODIGO;
        document.getElementById("edtDesEmp").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodEmp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodEmpBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbAtivo"
                                  ,topo:100
                                  ,tableBd:"EMPRESA"
                                  ,fieldCod:"A.EMP_CODIGO"
                                  ,fieldDes:"A.EMP_APELIDO"
                                  ,fieldAtv:"A.EMP_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblEmp"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "1"  : ret[0].CODIGO             );
          document.getElementById("edtDesEmp").value  = ( ret.length == 0 ? "TOTALTRAC LTDA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "1" : ret[0].CODIGO )  );
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
              name="frmImp" 
              id="frmImp" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Imposto<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 380px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtUfDe"
                                                    onBlur="CodEstBlur(this);" 
                                                    onFocus="estFocus(this);" 
                                                    onClick="estF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="2"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtUfDe">UFDE:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesUfDe" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesUfDe">ESTADO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodUfPara"
                                                    onBlur="CodUfpBlur(this);" 
                                                    onFocus="ufpFocus(this);" 
                                                    onClick="ufpF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="2"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodUfPara">UFPARA:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesUfPara" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesUfPara">ESTADO:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbEntSai" id="cbEntSai">
                  <option value="E">ENT</option>
                  <option value="S">SAI</option>
                </select>
                <label class="campo_label campo_required" for="cbEntSai">ENTRADA/SAIDA</label>
              </div>
              
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodNcm"
                                                    placeholder="####.##.##"                 
                                                    onkeyup="mascaraNumero('####.##.##',this,event,'dig')"
                                                    onBlur="CodNcmBlur(this);" 
                                                    onFocus="ncmFocus(this);" 
                                                    onClick="ncmF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="10"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodNcm">NCM:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesNcm" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesNcm">NOME_NCM:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodCtg"
                                                    onBlur="CodCtgBlur(this);" 
                                                    onFocus="ctgFocus(this);" 
                                                    onClick="ctgF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCtg">CATEG:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesCtg" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCtg">NOME_CATEGORIA:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodNo"
                                                    onBlur="CodNoBlur(this);" 
                                                    onFocus="noFocus(this);" 
                                                    onClick="noF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodNo">NO:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesNo" type="text"/>
                <label class="campo_label campo_required" for="edtDesNo">N0ME-NAT OPERACAO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodCfo"
                                                    placeholder="#.###"                 
                                                    onkeyup="mascaraNumero('#.###',this,event,'dig')"
                                                    onBlur="CodCfoBlur(this);" 
                                                    onFocus="cfoFocus(this);" 
                                                    onClick="cfoF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCfo">CFOP:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesCfo" type="text"/>
                <label class="campo_label campo_required" for="edtDesCfo">CFO:</label>
              </div>
              
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodIcm"
                                                    onBlur="CodIcmBlur(this);" 
                                                    onFocus="icmFocus(this);" 
                                                    onClick="icmF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="5"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodIcm">CST_ICMS:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input_titulo input" id="edtDesIcm" type="text"/>
                <label class="campo_label campo_required" for="edtDesIcm">ICMS:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtAliqIcms" maxlength="6" type="text"/>
                <label class="campo_label" for="edtAliqIcms">ALIQICMS:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtReducaoAbc" maxlength="15" type="text"/>
                <label class="campo_label" for="edtReducaoAbc">REDUCAOBC:</label>
              </div>

              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodIpi"
                                                    onBlur="CodIpiBlur(this);" 
                                                    onFocus="ipiFocus(this);" 
                                                    onClick="ipiF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodIpi">CST_IPI:</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input_titulo input" id="edtDesIpi" type="text"/>
                <label class="campo_label campo_required" for="edtDesIpi">IPI:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtAliqIpi" maxlength="6" type="text"/>
                <label class="campo_label" for="edtAliqIpi">ALIQIPI%:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodPis"
                                                    onBlur="CodPisBlur(this);" 
                                                    onFocus="pisFocus(this);" 
                                                    onClick="pisF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodPis">CSTPIS:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesPis" type="text"/>
                <label class="campo_label campo_required" for="edtDesPis">PIS:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtAliqPis" maxlength="6" type="text" />
                <label class="campo_label" for="edtAliqPis">ALIQPIS%:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtAliqCofins" maxlength="6" type="text" />
                <label class="campo_label" for="edtAliqCofins">ALIQCOFINS%:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtAliqSt" maxlength="6" type="text" />
                <label class="campo_label" for="edtAliqSt">ALIQST%:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" name="cbAlteraNfp" id="cbAlteraNfp">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAlteraNfp">ALTERANFP</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodFll"
                                                    onBlur="CodFllBlur(this);" 
                                                    onFocus="fllFocus(this);" 
                                                    onClick="fllF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodFll">CODFLL:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo input" id="edtDesFll" type="text"/>
                <label class="campo_label campo_required" for="edtDesFll">FILIAL:</label>
              </div>
        
              <div class="campotexto campo15">
                <input class="campo_input_titulo input" id="edtDesEmp" type="text"/>
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
              <input id="edtCodCofins" type="text" />
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