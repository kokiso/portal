<?php
  session_start();
  if( isset($_POST["favorecido"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["favorecido"]);
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
        //    Dados para JavaScript FAVORECIDO     //
        /////////////////////////////////////////////
        if( $rotina=="selectFvr" ){
          $sql="";
          $sql.="SELECT A.FVR_CODIGO";
          $sql.="       ,A.FVR_NOME";
          $sql.="       ,A.FVR_APELIDO";
          $sql.="       ,A.FVR_CNPJCPF";
          $sql.="       ,A.FVR_CODCDD";
          $sql.="       ,C.CDD_NOME";
          $sql.="       ,C.CDD_CODEST";
          $sql.="       ,CONVERT(VARCHAR(10),A.FVR_DTCADASTRO,127) AS FVR_DTCADASTRO";      
          $sql.="       ,CASE WHEN A.FVR_FISJUR='F' THEN 'FIS' ELSE 'JUR' END AS FVR_FISJUR";
          $sql.="       ,COALESCE(A.FVR_INSMUNIC,'NSA') AS FVR_INSMUNIC";
          $sql.="       ,A.FVR_CONTATO";
          $sql.="       ,A.FVR_ENDERECO";
          $sql.="       ,A.FVR_NUMERO";
          $sql.="       ,A.FVR_CEP";
          $sql.="       ,A.FVR_BAIRRO";
          $sql.="       ,A.FVR_FONE";
          $sql.="       ,A.FVR_INS";
          $sql.="       ,A.FVR_CTAATIVO";
          $sql.="       ,A.FVR_CTAPASSIVO";
          $sql.="       ,A.FVR_CADMUNIC";
          $sql.="       ,A.FVR_EMAIL";
          $sql.="       ,A.FVR_GFCP";               // Angelo Kokiso , adição dos parametros de grupo de favorecido
          $sql.="       ,GFP.GF_NOME AS GRUPOCP";  // Angelo Kokiso , adição dos parametros de grupo de favorecido
          $sql.="       ,A.FVR_GFCR";              // Angelo Kokiso , adição dos parametros de grupo de favorecido
          $sql.="       ,GFR.GF_NOME AS GRUPOCR";  // Angelo Kokiso , adição dos parametros de grupo de favorecido
          $sql.="       ,A.FVR_CODCTG";
          $sql.="       ,G.CTG_NOME";
          $sql.="       ,A.FVR_SENHA";
          $sql.="       ,A.FVR_COMPLEMENTO";
          $sql.="       ,A.FVR_CODLGR";
          $sql.="       ,CASE WHEN A.FVR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS FVR_ATIVO";
          $sql.="       ,CASE WHEN A.FVR_REG='P' THEN 'PUB' WHEN A.FVR_REG='S' THEN 'SIS' ELSE 'ADM' END AS FVR_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.FVR_CODUSR";
          $sql.="       ,A.FVR_LATITUDE";          
          $sql.="       ,A.FVR_LONGITUDE";                    
          $sql.="  FROM FAVORECIDO A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.FVR_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN CIDADE C ON A.FVR_CODCDD=C.CDD_CODIGO";
          $sql.="  LEFT OUTER JOIN CATEGORIA G ON A.FVR_CODCTG=G.CTG_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOFAVORECIDO GFP ON A.FVR_GFCP=GFP.GF_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOFAVORECIDO GFR ON A.FVR_GFCR=GFR.GF_CODIGO";
          $sql.="  LEFT OUTER JOIN LOGRADOURO L ON A.FVR_CODLGR=L.LGR_CODIGO";
          $sql.="  WHERE (FVR_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')";
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
          $vldCampo = new validaCampo("VFAVORECIDO",0);
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
        // Atualizando o favorecido de dados se opcao de insert/updade/delete //
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
    <title>Favorecido</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
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
        //////////////////////////////////////////
        //   Objeto clsTable2017 FAVORECIDO     //
        //////////////////////////////////////////
        jsFvr={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"FVR_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"pk"             : "S"
                      ,"autoIncremento" : "S"                      
                      ,"newRecord"      : ["0","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [1,6] 
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do favorecido. Este campo é único e deve tem o formato 999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "FVR_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "40em"
                      ,"tamImp"         : "100"
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.|-| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : ["Descrição do favorecido."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "FVR_APELIDO"   
                      ,"labelCol"       : "APELIDO"
                      ,"obj"            : "edtApelido"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [3,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : ["Descrição do apelido do favorecido."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"field"          :"FVR_CNPJCPF" 
                      ,"labelCol"       : "CNPJCPF"
                      ,"obj"            : "edtCnpjCpf"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "11em"
                      ,"tamImp"         : "25"
                      ,"fieldType"      : "str"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|N|S|A" //Angelo Kokiso - adição do NSA
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,14]
                      ,"ajudaCampo"     : [ "Cnpj se pessoa fisica ou CPF se pessoa fisica"]
                      ,"ajudaDetalhe"   : "Cnpj se pessoa fisica ou CPF se pessoa fisica"
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":5  ,"field"          :"FVR_CODCDD" 
                      ,"labelCol"       : "CODCDD"
                      ,"obj"            : "edtCodCdd"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
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
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "40"
                      ,"newRecord"      : ["SAO PAULO","this","this"]
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"ajudaCampo"     : ["Nome da Cidade."]
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
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "FVR_DTCADASTRO"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "DATA"
                      ,"obj"            : "edtDtCadastro"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "dat"
                      ,"newRecord"      : [jsDatas(0).retDDMMYYYY(),"this","this"]
                      ,"digitosMinMax"  : [1,10]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "FVR_FISJUR"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "FISJUR"
                      ,"obj"            : "cbFisJur"
                      ,"tamGrd"         : "4em"
                      ,"tipo"           : "cb"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["J","this","this"]
                      ,"digitosMinMax"  : [1,1]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                      
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "FVR_INSMUNIC"   
                      ,"labelCol"       : "INSMUNIC"
                      ,"obj"            : "edtInsMunic"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [3,20]
                      ,"newRecord"      : ["NSA","this","this"]                      
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":11 ,"field"          : "FVR_CONTATO"   
                      ,"labelCol"       : "CONTATO"
                      ,"obj"            : "edtContato"
                      ,"newRecord"      : ["NSA","this","this"]                                            
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,40]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":12 ,"field"         : "FVR_ENDERECO"   
                      ,"labelCol"       : "ENDERECO"
                      ,"obj"            : "edtEndereco"
                      ,"tamGrd"         : "50em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [1,60] // ANGELO KOKISO MUDANÇA POR RUAS DE 1/2 DIGITOS ( Rua 1, Rua 2, Rua 3, Rua 4 , Rua 10, Rua 11)
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"field"         : "FVR_NUMERO"   
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
            ,{"id":14 ,"field"          : "FVR_CEP"   
                      ,"labelCol"       : "CEP"
                      ,"obj"            : "edtCep"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,8]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|N|S|A"
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"field"         : "FVR_BAIRRO"   
                      ,"labelCol"       : "BAIRRO"
                      ,"obj"            : "edtBairro"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,40]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":16 ,"field"         : "FVR_FONE"   
                      ,"labelCol"       : "FONE"
                      ,"obj"            : "edtFone"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,10]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|-|N|S|A" //Angelo Kokiso - adição do NSA
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":17 ,"field"         : "FVR_INS"   
                      ,"labelCol"       : "INS"
                      ,"obj"            : "edtIns"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["NSA","this","this"]                      
                      ,"digitosMinMax"  : [3,19]
                      ,"validar"        : ["notnull"]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|-|A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|" //Angelo Kokiso - adição de letras ( caso de  Insento e NSA )
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":18 ,"field"         : "FVR_CTAATIVO"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "CTAATIVO"
                      ,"obj"            : "edtCtaAtivo"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["0.00.00.00.0000","this","this"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":19 ,"field"         : "FVR_CTAPASSIVO"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "CTAPASSIVO"
                      ,"obj"            : "edtCtaPassivo"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["0.00.00.00.0000","this","this"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":20 ,"field"         : "FVR_CADMUNIC"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "CADMUNIC"
                      ,"obj"            : "edtCadMunic"
                      ,"tamGrd"         : "15em"
                      ,"validar"        : ["podeNull"]
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [0,20]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                      
                      ,"padrao":0}
            ,{"id":21 ,"field"         : "FVR_EMAIL"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "EMAIL"
                      ,"obj"            : "edtEmail"
                      ,"tamGrd"         : "40em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosValidos" : "a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|0|1|2|3|4|5|6|7|8|9|@|_|.|-|;"
                      //"a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|0!1|2|3|4|5|6|7|8|9|@|_|.|-|;" --Angelo Kokiso correção de sintaxe
                      ,"formato"        : ["alltrim","lowercase"]
                      ,"ajudaCampo"     : ["Email do favorecido.","Para vendedor este campo liga o favorecido ao usuario que se logou no sistema"]
                      ,"validar"        : ["podeNull"]
                      ,"digitosMinMax"  : [0,60]
                      ,"importaExcel"   : "S"                      
                      ,"padrao":0}
            ,{"id":22 ,"field"          :"FVR_GFCP" 
                      ,"labelCol"       : "CODCP"
                      ,"obj"            : "edtCodCp"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [ "Código do Favorecido"]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":23  ,"field"         : "GF_NOME"   
                      ,"labelCol"       : "GRUPOCP"
                      ,"obj"            : "edtDesCp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "18em"
                      ,"tamImp"         : "60"
                      ,"newRecord"      : ["","this","this"]
                      ,"padrao":0}
            ,{"id":24  ,"field"          :"FVR_GFCR" 
                      ,"labelCol"       : "CODCR"
                      ,"obj"            : "edtCodCr"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [ "Código do Favorecido"]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":25  ,"field"         : "GF_NOME"   
                      ,"labelCol"       : "GRUPOCR"
                      ,"obj"            : "edtDesCr"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "18em"
                      ,"tamImp"         : "60"
                      ,"newRecord"      : ["","this","this"]
                      ,"padrao":0}
            ,{"id":26 ,"field"          : "FVR_CODCTG" 
                      ,"labelCol"       : "CODCTG"
                      ,"obj"            : "edtCodCtg"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["NOR","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":27 ,"field"          : "CTG_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesCtg"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["NORMAL","this","this"]
                      ,"digitosMinMax"  : [1,20]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição da BancoCodigo para este favorecido."]
                      ,"padrao":0}
            ,{"id":28 ,"field"          : "FVR_SENHA" 
                      ,"labelCol"       : "SENHA"
                      ,"obj"            : "edtSenha"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [2,10]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":29 ,"field"          : "FVR_COMPLEMENTO" 
                      ,"labelCol"       : "COMPLEMENTO"
                      ,"obj"            : "edtComplemento"
                      ,"tamGrd"         : "40em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"digitosMinMax"  : [1,60] // angelo kokiso alteração do valor minimo para 1
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":30 ,"field"          : "FVR_CODLGR" 
                      ,"labelCol"       : "CODLGR"
                      ,"obj"            : "edtCodLgr"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["RUA","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,5]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":31 ,"field"          : "FVR_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":32 ,"field"          : "FVR_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":33 ,"field"         : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":34 ,"field"         : "FVR_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}  
            ,{"id":35 ,"field"             : "FVR_LATITUDE"
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
            ,{"id":36 ,"field"             : "FVR_LONGITUDE"
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
            ,{"id":37 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objFvr.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"FAVORECIDO - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"             }
            ,{"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"  }
            ,{"texto":"Excluir"   ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"            }
            ,{"texto":"Semestral" ,"name":"horSemestre"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-calendar"          }            
            ,{"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"      }        
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"            }                        
           // ,// {"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"            }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmFvr"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaFvr"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmFvr"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblFvr"              // Nome da table
          ,"prefixo"        : "fvr"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VFAVORECIDO"         // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPFAVORECIDO"       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "FVR_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "FVR_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "FVR_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "108em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "FAVORECIDO"          // Titulo do relatório
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
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objFvr.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objFvr.AjudaSisAtivo(jsFvr);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objFvr.detalhe();"]
                               //,["Gerar excel"                            ,"fa-file-excel-o"  ,"objFvr.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objFvr.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objFvr.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objFvr.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objFvr.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objFvr.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "FAVORECIDO[05]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objFvr === undefined ){  
          objFvr=new clsTable2017("objFvr");
        };  
        objFvr.montarHtmlCE2017(jsFvr); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsFvr.form).style.width=jsFvr.width;
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"CODIGO"       ,"labelCol":"CODIGO"       ,"tamGrd":"4em"       ,"tamImp":"10"}  
            ,{"id":1  ,"field":"DESCRICAO"    ,"labelCol":"DESCRICAO"    ,"tamGrd":"30em"      ,"tamImp":"10"}  
            ,{"id":2  ,"field":"APELIDO"      ,"labelCol":"APELIDO"      ,"tamGrd":"10em"      ,"tamImp":"10"}  
            ,{"id":3  ,"field":"CNPJCPF"      ,"labelCol":"CNPJCPF"      ,"tamGrd":"12em"      ,"tamImp":"10"}  
            ,{"id":4  ,"field":"CODCDD"       ,"labelCol":"CODCDD"       ,"tamGrd":"8em"       ,"tamImp":"10"}  
            ,{"id":5  ,"field":"FISJUR"       ,"labelCol":"FISJUR"       ,"tamGrd":"4em"       ,"tamImp":"10"}  
            ,{"id":6  ,"field":"INSMUNIC"     ,"labelCol":"INSMUNIC"     ,"tamGrd":"12em"      ,"tamImp":"10"}
            ,{"id":7  ,"field":"CONTATO"      ,"labelCol":"CONTATO"      ,"tamGrd":"20em"      ,"tamImp":"10"}
            ,{"id":8  ,"field":"ENDERECO"     ,"labelCol":"ENDERECO"     ,"tamGrd":"30em"      ,"tamImp":"10"}
            ,{"id":9  ,"field":"NUMERO"       ,"labelCol":"NUMERO"       ,"tamGrd":"4em"       ,"tamImp":"10"}
            ,{"id":10 ,"field":"CEP"          ,"labelCol":"CEP"          ,"tamGrd":"8em"       ,"tamImp":"10"}
            ,{"id":11 ,"field":"BAIRRO"       ,"labelCol":"BAIRRO"       ,"tamGrd":"12em"      ,"tamImp":"10"}
            ,{"id":12 ,"field":"FONE"         ,"labelCol":"FONE"         ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":13 ,"field":"INS"          ,"labelCol":"INS"          ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":14 ,"field":"CADMUNIC"     ,"labelCol":"CADMUNIC"     ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":15 ,"field":"EMAIL"        ,"labelCol":"EMAIL"        ,"tamGrd":"30em"      ,"tamImp":"10"}
            ,{"id":17 ,"field":"CODCP"        ,"labelCol":"CODCP"        ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":18 ,"field":"CODCR"        ,"labelCol":"CODCTR"       ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":19 ,"field":"CODCTG"       ,"labelCol":"CODCTG"       ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":20 ,"field":"SENHA"        ,"labelCol":"SENHA"        ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":21 ,"field":"COMPLEMENTO"  ,"labelCol":"COMPLEMENTO"  ,"tamGrd":"30em"      ,"tamImp":"10"}
            ,{"id":22 ,"field":"CODLGR"       ,"labelCol":"CODLGR"       ,"tamGrd":"5em"       ,"tamImp":"10"}
            ,{"id":23 ,"field":"LATITUDE"     ,"labelCol":"LATITUDE"     ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":24 ,"field":"LONGITUDE"    ,"labelCol":"LONGITUDE"    ,"tamGrd":"10em"      ,"tamImp":"10"}
            ,{"id":25 ,"field":"ERRO"         ,"labelCol":"ERRO"         ,"tamGrd":"45em"      ,"tamImp":"100"}            
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"         }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o"  }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"         }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[24].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                                
          ,"div"            : "frmExc"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaExc"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmExc"                  // Onde vai ser gerado o fieldSet                     
          ,"divModal"       : "divTopoInicioE"          // Nome da div que vai fazer o show modal
          ,"tbl"            : "tblExc"                  // Nome da table
          ,"prefixo"        : "exc"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados
          ,"max-width"      : "106em"                   // Tamanho máximo da table //Angelo kokiso - mudança no tamanho da tabela
          ,"width"          : "min-content"             // Tamanho da table
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
      var objFvr;                     // Obrigatório para instanciar o JS TFormaCob
      var jsFvr;                      // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS BancoF10
      var objCddF10;                  // Obrigatório para instanciar o JS CidadeF10
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d05);
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
        clsJs.add("rotina"      , "selectFvr"                               );
        clsJs.add("login"       , jsPub[0].usr_login                        );
        clsJs.add("ativo"       , atv                                       );
        clsJs.add("codFvr"      , document.getElementById("edtCodigo").value );    
        fd = new FormData();
        fd.append("favorecido" , clsJs.fim());
        msg     = requestPedido("Trac_Favorecido.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsFvr.registros=objFvr.addIdUnico(retPhp[0]["dados"]);
          objFvr.ordenaJSon(jsFvr.indiceTable,false);  
          objFvr.montarBody2017();
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
            clsJs.add("titulo"      , objFvr.trazCampoExcel(jsFvr));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("favorecido"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Favorecido.php",fd); 
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
        fCidadeF10(0,obj.id,"edtFone",100,{ativo:"S"});
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
          var ret = fCidadeF10(1,obj.id,"edtFone",100,{codcdd:obj.id,ativo:"S"});          
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "0000000" : ret[0].CODIGO     );
          document.getElementById("edtDesCdd").value  = ( ret.length == 0 ? ""        : ret[0].DESCRICAO  );
          document.getElementById("edtCodEst").value  = ( ret.length == 0 ? ""        : ret[0].UF         );          
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000000" : ret[0].CODIGO )  );
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
                      ,foco:"edtSenha"
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
                                  ,foco:"edtSenha"
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
      function horSemestreClick(){
        localStorage.setItem("addParametro","favorecido");
        window.open("Trac_FavorecidoGrupoSem.php");  
      };
      
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
            document.getElementById("edtEndereco").value  = retPhp[0]["dados"][0]["endereco"].replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, ''); //angelo kokiso
            document.getElementById("edtBairro").value    = retPhp[0]["dados"][0]["bairro"].replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '');;
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
            /*
            funcLatLon();
            */
          };  
        };  
      };
            function cepFocus(obj){
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value);         
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
      
      function funcMapa(){     
        clsJs=jsString("lote");        
        clsJs.add("endereco"  , document.getElementById("edtEndereco").value  );
        clsJs.add("codlgr"    , document.getElementById("edtCodLgr").value    );
        clsJs.add("cidade"    , document.getElementById("edtDesCdd").value    );
        clsJs.add("codest"    , document.getElementById("edtCodEst").value    );
        clsJs.add("lat"       , jsNmrs("edtLatitude").dec(8).dolar().ret()    );
        clsJs.add("lon"       , jsNmrs("edtLongitude").dec(8).dolar().ret()   );
        clsJs.add("rotina"    , "alvo"                                        );
        clsJs.add("numero"    , document.getElementById("edtNumero").value    );
        msg=clsJs.fim();
        localStorage.setItem("addMapa",msg);
        window.open("mapa/Trac_AlvoMapa.php");
      };  
      function fncMontaApelido(str){
        if( $doc("edtApelido").value=="" ){
          let splt=(str.toUpperCase()).split(" ");
          $doc("edtApelido").value=splt[0];  
          $doc("edtSenha").value=splt[0];  
        }
      };

      ////////////////////////////////////
      //  AJUDA PARA GRUPO FAVOREICO CP //
      ////////////////////////////////////
      function cpFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function cpF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodCr"
                      ,topo:100
                      ,tableBd:"GRUPOFAVORECIDO"
                      ,fieldCod:"A.GF_CODIGO"
                      ,fieldDes:"A.GF_NOME"
                      ,fieldAtv:"A.GF_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblCp"}
        );
      };
      function RetF10tblCp(arr){
        document.getElementById("edtCodCp").value  = arr[0].CODIGO;
        document.getElementById("edtDesCp").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCpBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodCr"
                                  ,topo:100
                                  ,tableBd:"GRUPOFAVORECIDO"
                                  ,fieldCod:"A.GP_CODIGO"
                                  ,fieldDes:"A.GP_NOME"
                                  ,fieldAtv:"A.GP_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblCr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "0000"  : ret[0].CODIGO             );
          document.getElementById("edtDesCp").value  = ( ret.length == 0  ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////////////
      //  AJUDA PARA GRUPO FAVOREICO CR //
      ////////////////////////////////////
      function crFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function crF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"btnConfirmar"
                      ,topo:100
                      ,tableBd:"GRUPOFAVORECIDO"
                      ,fieldCod:"A.GF_CODIGO"
                      ,fieldDes:"A.GF_NOME"
                      ,fieldAtv:"A.GF_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblCr"}
        );
      };
      function RetF10tblCr(arr){
        document.getElementById("edtCodCr").value  = arr[0].CODIGO;
        document.getElementById("edtDesCr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"btnConfirmar"
                                  ,topo:100
                                  ,tableBd:"GRUPOFAVORECIDO"
                                  ,fieldCod:"A.GP_CODIGO"
                                  ,fieldDes:"A.GP_NOME"
                                  ,fieldAtv:"A.GP_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblGcr"}
          );
          document.getElementById(obj.id).value     = ( ret.length == 0 ? "0000"  : ret[0].CODIGO             );
          document.getElementById("edtDesCr").value = ( ret.length == 0  ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO )  );
        };
      };
      //  
      
    </script>
  </head>
  <body>
    <div class="divTelaCheia">
      <div id="divRotina" class="conteudo">  
        <div id="divTopoInicio">
        </div>
        <form method="post" 
              name="frmFvr" 
              id="frmFvr" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Favorecido<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 410px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input" id="edtDescricao" 
                                           onBlur="fncMontaApelido(this.value);" 
                                           type="text" 
                                           maxlength="60" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtApelido" type="text" maxlength="20" />
                <label class="campo_label campo_required" for="edtApelido">APELIDO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCnpjCpf" 
                                           OnKeyPress="return mascaraInteiro(event);"                 
                                           type="text" 
                                           maxlength="14" />
                <label class="campo_label campo_required" for="edtCnpjCpf">CNPJCPF:</label>
              </div>
      
              <div class="campotexto campo15">
                <input class="campo_input" id="edtDtCadastro" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtDtCadastro">DATA:</label>
              </div>
             <div class="campotexto campo15">
              <select class="campo_input_combo" id="cbFisJur">
                <option value="F">FIS</option>
                <option value="J">JUR</option>
              </select>
              <label class="campo_label campo_required" for="cbFisJur">FIS/JUR:</label>
             </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtInsMunic" type="text" maxlength="20" />
                <label class="campo_label" for="edtInsMunic">INSMUNIC:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input" id="edtContato" type="text" maxlength="40" />
                <label class="campo_label" for="edtContato">CONTATO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input"  name="edtCep" id="edtCep" type="text" 
                       onFocus="cepFocus(this);" 
                       onBlur="cepBlur(this);"
                       OnKeyPress="return mascaraInteiro(event);" maxlength="8" />
                <label class="campo_label campo_required" for="edtCep">CEP</label>
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
              <div class="campotexto campo60">
                <input class="campo_input" id="edtEndereco" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtEndereco">ENDERECO:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtNumero" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtNumero">NUMERO:</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input" id="edtComplemento" type="text" maxlength="60" />
                <label class="campo_label" for="edtComplemento">COMPLEMENTO:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtBairro" type="text" maxlength="30" />
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
              <div class="campotexto campo45">
                <input class="campo_input_titulo input" id="edtDesCdd" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCdd">CIDADE:</label>
              </div>
              <div class="campotexto campo05">
                <input class="campo_input_titulo input" id="edtCodEst" type="text" disabled />
                <label class="campo_label campo_required" for="edtCodEst">UF:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtFone" type="text" maxlength="10" />
                <label class="campo_label campo_required" for="edtFone">FONE:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtIns" type="text" maxlength="19" />
                <label class="campo_label" for="edtIns">INS:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCtaAtivo" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtCtaAtivo">CTAATIVO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCtaPassivo" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtCtaPassivo">CTAPASSIVO:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input" id="edtCadMunic" type="text" maxlength="20" />
                <label class="campo_label" for="edtCadMunic">CADMUNIC:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtEmail" type="text" maxlength="60" />
                <label class="campo_label" for="edtEmail">EMAIL:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodCp"
                                                    onBlur="CodCpBlur(this);" 
                                                    onFocus="cpFocus(this);" 
                                                    onClick="cpF10Click(this);"
                                                    data-oldvalue="0000"
                                                    autocomplete="off"
                                                    maxlength="4"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCp">CP:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesCp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCp">PAGAR</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodCr"
                                                    onBlur="CodCrBlur(this);" 
                                                    onFocus="crFocus(this);" 
                                                    onClick="crF10Click(this);"
                                                    data-oldvalue="0000"
                                                    autocomplete="off"
                                                    maxlength="4"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCr">CR:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesCr" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCr">RECEBER</label>
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
                <label class="campo_label campo_required" for="edtCodCtg">CODCTG:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesCtg" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCtg">CATEGORIA:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtSenha" type="text" maxlength="10" />
                <label class="campo_label" for="edtSenha">SENHA:</label>
              </div>
             <div class="campotexto campo10">
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
              <div class="campotexto campo100">
                <label class="labelMensagem" for="edtUsuario">- <b>Lat/Long</b> para localização do cliente em relação a um terceiro para instalação auto</label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div id="divExcel" class="divTopoExcel">
      </div>
    </div>       
  </body>
</html>