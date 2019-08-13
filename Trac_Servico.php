<?php
  session_start();
  if( isset($_POST["servico"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["servico"]);
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
        //    Dados para JavaScript SERVICO       //
        ///////////////////////////////////////////
        if( $rotina=="selectSrv" ){
          $sql="";
          $sql.="SELECT A.SRV_CODIGO";
          $sql.="       ,A.SRV_NOME";
          $sql.="       ,CASE WHEN A.SRV_ENTSAI='S' THEN 'SAI' ELSE 'ENT' END AS SRV_ENTSAI";
          $sql.="       ,CASE WHEN A.SRV_INSS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_INSS";
          $sql.="       ,A.SRV_INSSALIQ";
          $sql.="       ,A.SRV_INSSBASECALC";
          $sql.="       ,CASE WHEN A.SRV_IRRF='S' THEN 'SIM' ELSE 'NAO' END AS SRV_IRRF";
          $sql.="       ,A.SRV_IRRFALIQ";
          $sql.="       ,CASE WHEN A.SRV_PIS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_PIS";
          $sql.="       ,A.SRV_PISALIQ";
          $sql.="       ,CASE WHEN A.SRV_COFINS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_COFINS";
          $sql.="       ,A.SRV_COFINSALIQ";
          $sql.="       ,CASE WHEN A.SRV_CSLL='S' THEN 'SIM' ELSE 'NAO' END AS SRV_CSLL";
          $sql.="       ,A.SRV_CSLLALIQ";
          $sql.="       ,CASE WHEN A.SRV_ISS='S' THEN 'SIM' ELSE 'NAO' END AS SRV_ISS";
          //$sql.="       ,A.SRV_CODCC";
          $sql.="       ,A.SRV_CODSPR";
          $sql.="       ,A.SRV_CODEMP";
          $sql.="       ,E.EMP_APELIDO";
          $sql.="       ,CASE WHEN A.SRV_PODEVENDA='S' THEN 'SIM' ELSE 'NAO' END AS SRV_PODEVENDA";          
          $sql.="       ,CASE WHEN A.SRV_PODELOCACAO='S' THEN 'SIM' ELSE 'NAO' END AS SRV_PODELOCACAO";                    
          $sql.="       ,CASE WHEN A.SRV_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS SRV_ATIVO";
          $sql.="       ,CASE WHEN A.SRV_REG='P' THEN 'PUB' WHEN A.SRV_REG='S' THEN 'SIS' ELSE 'ADM' END AS SRV_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.SRV_CODUSR";
          $sql.="  FROM SERVICO A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.SRV_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA E ON A.SRV_CODEMP=E.EMP_CODIGO";
          $sql.="  WHERE ((SRV_CODEMP=".$lote[0]->codemp.")";
          $sql.="   AND ((SRV_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                  
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
          $vldCampo = new validaCampo("VSERVICO",0);
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
    <title>Servico</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaServicoPrefeituraF10.js"></script>
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        //////////////////////////////////////////
        //   Objeto clsTable2017 SERVICO        //
        //////////////////////////////////////////
        jsSrv={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"SRV_CODIGO" 
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
                      ,"ajudaCampo"     : [  "Codigo do servico. Este campo é único e deve tem o formato 9999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "N"                                                                
                      ,"padrao":0}
           ,{"id":2   ,"field"          : "SRV_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "118"
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Descrição do servico."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "SRV_ENTSAI"  
                      ,"labelCol"       : "ES" 
                      ,"obj"            : "cbEntSai"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["E","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":4  ,"field"          : "SRV_INSS"  
                      ,"labelCol"       : "INSS" 
                      ,"obj"            : "cbInss"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":5  ,"field"          : "SRV_INSSALIQ"  
                      ,"labelCol"       : "%INSS" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtInssAliq"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":6  ,"field"          : "SRV_INSSBASECALC"  
                      ,"labelCol"       : "%BASECALC" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtInssBaseCalc"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":7  ,"field"          : "SRV_IRRF"  
                      ,"labelCol"       : "IRRF" 
                      ,"obj"            : "cbIrrf"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":8  ,"field"          : "SRV_IRRFALIQ"  
                      ,"labelCol"       : "%IRRF" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtIrrfAliq"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":9  ,"field"          : "SRV_PIS"  
                      ,"labelCol"       : "PIS" 
                      ,"obj"            : "cbPis"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":10 ,"field"          : "SRV_PISALIQ"  
                      ,"labelCol"       : "%PIS" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtPisAliq"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":11 ,"field"          : "SRV_COFINS"  
                      ,"labelCol"       : "COFINS" 
                      ,"obj"            : "cbCofins"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":12 ,"field"          : "SRV_COFINSALIQ"  
                      ,"labelCol"       : "%COFINS" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtCofinsAliq"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":13 ,"field"          : "SRV_CSLL"  
                      ,"labelCol"       : "CSLL" 
                      ,"obj"            : "cbCsll"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":14 ,"field"          : "SRV_CSLLALIQ"  
                      ,"labelCol"       : "%CSLL" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtCsllAliq"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":15 ,"field"          : "SRV_ISS"  
                      ,"labelCol"       : "ISS" 
                      ,"obj"            : "cbIss"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            // ,{"id":16 ,"field"          :"SRV_CODCC" 
            //           ,"labelCol"       : "CONTABIL"
            //           ,"obj"            : "edtCodCc"
            //           ,"insUpDel"       : ["N","N","N"]
            //           ,"tamGrd"         : "0em"
            //           ,"tamImp"         : "25"
            //           ,"fieldType"      : "str"
            //           ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
            //           ,"newRecord"      : ["0.00.00.00.0000","this","this"]
            //           ,"validar"        : ["notnull"]
            //           ,"digitosMinMax"  : [1,15]
            //           ,"ajudaCampo"     : [ "Código ..."]
            //           ,"padrao":0}
            ,{"id":16 ,"field"          :"SRV_CODSPR" 
                      ,"labelCol"       : "CNAE"
                      ,"obj"            : "edtCodSpr"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Código ..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":17 ,"field"          : "SRV_CODEMP" 
                      ,"pk"             : "S"
                      ,"labelCol"       : "CODEMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"padrao":7}					  
            ,{"id":18 ,"field"          : "EMP_APELIDO"   
                      ,"labelCol"       : "EMPRESA"
                      ,"obj"            : "edtDesEmp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [jsPub[0].emp_apelido,"this","this"]
                      ,"ajudaCampo"     : ["Nome da EMpresa."]
                      ,"padrao":0}
            ,{"id":19 ,"field"          : "SRV_PODEVENDA"  
                      ,"labelCol"       : "VENDA" 
                      ,"obj"            : "cbVenda"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":20 ,"field"          : "SRV_PODELOCACAO"  
                      ,"labelCol"       : "LOCACAO" 
                      ,"obj"            : "cbLocacao"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          : "SRV_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":22 ,"field"          : "SRV_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":23  ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":24  ,"field"          : "SRV_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":25 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objSrv.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"SERVICO - Detalhe do registro"
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
          ,"div"            : "frmSrv"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaSrv"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmSrv"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblSrv"              // Nome da table
          ,"prefixo"        : "srv"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VSERVICO"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPSERVICO"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "SRV_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "SRV_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "SRV_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "104em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "SERVICO"             // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtDescricao"
                              ,"cbEntSai"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objSrv.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objSrv.AjudaSisAtivo(jsSrv);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objSrv.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objSrv.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objSrv.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objSrv.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objSrv.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objSrv.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "SERVICO[04]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objSrv === undefined ){  
          objSrv=new clsTable2017("objSrv");
        };  
        objSrv.montarHtmlCE2017(jsSrv); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsSrv.form).style.width=jsSrv.width;
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"DESCRICAO"  ,"labelCol":"DESCRICAO"   ,"tamGrd":"20em"     ,"tamImp":"20"}
            ,{"id":1  ,"field":"ES"         ,"labelCol":"ENT_SAI"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":2  ,"field":"INSS"       ,"labelCol":"INSS_SN"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":3  ,"field":"%INSS"      ,"labelCol":"INSS_ALIQ"   ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":4  ,"field":"%BASECALC"  ,"labelCol":"INSS_BC"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":5  ,"field":"IRRF"       ,"labelCol":"IRRF_SN"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":6  ,"field":"%IRRF"      ,"labelCol":"IRRF_ALIQ"   ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":7  ,"field":"PIS"        ,"labelCol":"PIS_SN"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":8  ,"field":"%PIS"       ,"labelCol":"PIS_ALIQ"    ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":9  ,"field":"COFINS"     ,"labelCol":"COFINS_SN"   ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":10 ,"field":"%COFINS"    ,"labelCol":"COFINS_ALIQ" ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":11 ,"field":"CSLL"       ,"labelCol":"CSLL_SN"     ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":12 ,"field":"%CSLL"      ,"labelCol":"CSLL_ALIQ"   ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":13 ,"field":"ISS"        ,"labelCol":"ISS_SN"      ,"tamGrd":"6em"     ,"tamImp":"20"}
            ,{"id":14 ,"field":"CNAE"       ,"labelCol":"CNAE"        ,"tamGrd":"6em"     ,"tamImp":"20"}
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
      var objSrv;                     // Obrigatório para instanciar o JS TFormaCob
      var jsSrv;                      // Obj principal da classe clsTable2017
      var objSprF10;                  // Obrigatório para instanciar o JS TServicoPrefeitura
      //var jsSpr;                      // Obj principal da classe clsTable2017
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
        clsJs.add("rotina"      , "selectSrv"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("servico" , clsJs.fim());
        msg     = requestPedido("Trac_Servico.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsSrv.registros=objSrv.addIdUnico(retPhp[0]["dados"]);
          objSrv.ordenaJSon(jsSrv.indiceTable,false);  
          objSrv.montarBody2017();
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
            clsJs.add("titulo"      , objSrv.trazCampoExcel(jsSrv));  
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("servico"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Servico.php",fd); 
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
            gerarMensagemErro("SRV",retPhp[0].erro,{cabec:"Aviso"});    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
        };          
      };
      ///////////////////////////////////
      //  AJUDA PARA SERVICOPREFEITURA //
      ///////////////////////////////////
      function sprFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function sprF10Click(obj){ 
        fServicoPrefeituraF10(0,obj.id,"cbVenda",100,{tamColNome:"29.5em",ativo:"S" } ); 
      };
      function RetF10tblSpr(arr){
        document.getElementById("edtCodSpr").value  = arr[0].CODIGO;
        document.getElementById("edtCodSpr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codSprBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fServicoPrefeituraF10(1,obj.id,"cbVenda",100,{ativo:"S"}); 
          document.getElementById(obj.id).value       = ( arr.length == 0 ? "0000"  : jsNmrs(arr[0].CODIGO).emZero(4).ret() );  
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )         );
        };
      };
      ////////////////////
      // Cadastrar servico
      ////////////////////
      function btnConfirmarClick(){
        try{
          /////////////////////////////////////////////
          // Existes checks que olham para estes campos
          /////////////////////////////////////////////
          msg="ok";
          if( (document.getElementById("cbInss").value=="S") && (jsNmrs("edtInssAliq").dolar().ret()==0) )      msg="PARA INSS=S FAVOR INFORMAR ALIQUOTA!";
          if( (document.getElementById("cbIrrf").value=="S") && (jsNmrs("edtIrrfAliq").dolar().ret()==0) )      msg="PARA IRRF=S FAVOR INFORMAR ALIQUOTA!";  
          if( (document.getElementById("cbPis").value=="S") && (jsNmrs("edtPisAliq").dolar().ret()==0) )        msg="PARA PIS=S FAVOR INFORMAR ALIQUOTA!";
          if( (document.getElementById("cbCofins").value=="S") && (jsNmrs("edtCofinsAliq").dolar().ret()==0) )  msg="PARA COFINS=S FAVOR INFORMAR ALIQUOTA!";   
          if( (document.getElementById("cbCsll").value=="S") && (jsNmrs("edtCsllAliq").dolar().ret()==0) )      msg="PARA CSLL=S FAVOR INFORMAR ALIQUOTA!";             

          if( (document.getElementById("cbInss").value=="N") && (jsNmrs("edtInssAliq").dolar().ret()!=0) )      msg="PARA INSS=N FAVOR INFORMAR ALIQUOTA 0,00!";
          if( (document.getElementById("cbIrrf").value=="N") && (jsNmrs("edtIrrfAliq").dolar().ret()!=0) )      msg="PARA IRRF=N FAVOR INFORMAR ALIQUOTA 0,00!";  
          if( (document.getElementById("cbPis").value=="N") && (jsNmrs("edtPisAliq").dolar().ret()!=0) )        msg="PARA PIS=N FAVOR INFORMAR ALIQUOTA 0,00!";
          if( (document.getElementById("cbCofins").value=="N") && (jsNmrs("edtCofinsAliq").dolar().ret()!=0) )  msg="PARA COFINS=N FAVOR INFORMAR ALIQUOTA 0,00!";   
          if( (document.getElementById("cbCsll").value=="N") && (jsNmrs("edtCsllAliq").dolar().ret()!=0) )      msg="PARA CSLL=N FAVOR INFORMAR ALIQUOTA 0,00!";             
          
          if( msg != "ok" ){
            throw msg;
          } else {  
            objSrv.gravar(true);
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
              name="frmSrv" 
              id="frmSrv" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Serviço<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 220px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbEntSai">
                  <option value="E">ENT</option>
                  <option value="S">SAI</option>
                </select>
                <label class="campo_label campo_required" for="cbEntSai">ENTRADA/SAIDA</label>
              </div>
              <div class="campotexto campo10">
                <select class="campo_input_combo" id="cbInss"
                                                  onBlur="if(this.value=='N') document.getElementById('edtInssAliq').value='0,00';" >
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbInss">INSS:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtInssAliq" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtInssAliq">%INSS:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtInssBaseCalc" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtInssBaseCalc">%INSSBASECALC:</label>
              </div>
              <div class="campotexto campo10">
                <select class="campo_input_combo" id="cbIrrf"
                                                  onBlur="if(this.value=='N') document.getElementById('edtIrrfAliq').value='0,00';" >                
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbIrrf">IRRF:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtIrrfAliq" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtIrrfAliq">%IRRF:</label>
              </div>
              <div class="campotexto campo10">
                <select class="campo_input_combo" id="cbPis"
                                                  onBlur="if(this.value=='N') document.getElementById('edtPisAliq').value='0,00';" >                                
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbPis">PIS:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtPisAliq" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtPisAliq">%PIS:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbCofins"
                                                  onBlur="if(this.value=='N') document.getElementById('edtCofinsAliq').value='0,00';" >                                
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbCofins">COFINS:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtCofinsAliq" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtCofinsAliq">%COFINS:</label>
              </div>
              <div class="campotexto campo10">
                <select class="campo_input_combo" id="cbCsll"
                                                  onBlur="if(this.value=='N') document.getElementById('edtClssAliq').value='0,00';" >                                
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbCsll">CSLL:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input edtDireita" id="edtCsllAliq" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="6" />
                <label class="campo_label campo_required" for="edtCsllAliq">%CSLL:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" name="cbIss" id="cbIss">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbIss">ISS:</label>
              </div>
              
              <div class="campotexto campo20">
                <input class="campo_input inputF10" id="edtCodSpr"
                                                    onBlur="CodSprBlur(this);" 
                                                    onFocus="sprFocus(this);" 
                                                    onClick="sprF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="6"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodSpr">CNAE:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbVenda" >                                
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbVenda">VENDA:</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbLocacao" >
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbVenda">LOCACAO:</label>
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
                <input class="campo_input inputF10" id="edtCodCc" type="text"/>
                <label class="campo_label campo_required" for="edtCodCc">CONTABIL:</label>
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