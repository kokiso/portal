<?php
  session_start();
  if( isset($_POST["usuarioperfil"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/validaCampo.class.php");      

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["usuarioperfil"]);
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
        //////////////////////////////////////
        //    Dados para JavaScript PERFIL  //
        //////////////////////////////////////
        if( $rotina=="selectUp" ){
          $sql="SELECT A.UP_CODIGO
                       ,A.UP_NOME
                       ,A.UP_D01
                       ,A.UP_D02
                       ,A.UP_D03
                       ,A.UP_D04
                       ,A.UP_D05
                       ,A.UP_D06
                       ,A.UP_D07
                       ,A.UP_D08
                       ,A.UP_D09
                       ,A.UP_D10
                       ,A.UP_D11
                       ,A.UP_D12
                       ,A.UP_D13
                       ,A.UP_D14
                       ,A.UP_D15
                       ,A.UP_D16
                       ,A.UP_D17
                       ,A.UP_D18
                       ,A.UP_D19
                       ,A.UP_D20
                       ,A.UP_D21
                       ,A.UP_D22
                       ,A.UP_D23
                       ,A.UP_D24
                       ,A.UP_D25
                       ,A.UP_D26
                       ,A.UP_D27
                       ,A.UP_D28
                       ,A.UP_D29
                       ,A.UP_D30
                       ,A.UP_D31
                       ,A.UP_D32
                       ,A.UP_D33
                       ,A.UP_D34
                       ,A.UP_D35
                       ,A.UP_D36
                       ,A.UP_D37
                       ,A.UP_D38
                       ,A.UP_D39
                       ,A.UP_D40
                       ,A.UP_D41
                       ,A.UP_D42
                       ,A.UP_D43
                       ,A.UP_D44
                       ,A.UP_D45
                       ,A.UP_D46
                       ,A.UP_D47
                       ,A.UP_D48
                       ,A.UP_D49
                       ,A.UP_D50
                       ,CASE WHEN A.UP_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS UP_ATIVO
                       ,CASE WHEN A.UP_REG='P' THEN 'PUB' WHEN A.UP_REG='S' THEN 'SIS' ELSE 'ADM' END AS UP_REG
                       ,U.US_APELIDO
                       ,A.UP_CODUSR
                  FROM USUARIOPERFIL A
                  LEFT OUTER JOIN USUARIOSISTEMA U ON A.UP_CODUSR=U.US_CODIGO
                 WHERE (A.UP_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')";                 
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
          $vldCampo = new validaCampo("VUSUARIOPERFIL",0);
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
    <title>Perfil</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        ////////////////////////////////////
        //   Objeto clsTable2017 PERFIL   //
        ////////////////////////////////////
        jsUp={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"UP_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"                                      
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"autoIncremento" : "S"
                      ,"ajudaCampo"     : [  "Codigo do perfil. Gerado pelo sistema é único e tem o formato 9999"
                                            ,"Campo deve ser utilizado no cadastro de Usuário"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "UP_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [3,40]
                      ,"ajudaCampo"     : ["Nome do perfil."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "UP_D01"      
                      ,"labelCol"       : "D01"
                      ,"labelColImp"    : "01"
                      ,"obj"            : "cbD01"
                      ,"tipo"           : "cb"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de"
                                            ,"Usuario"
                                            ,"Usuario->perfil"
                                            ,"Cargo"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":4  ,"field"          : "UP_D02"      
                      ,"labelCol"       : "D02"
                      ,"labelColImp"    : "02"
                      ,"obj"            : "cbD02"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opção Usuario->Empresa"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":5  ,"field"          : "UP_D03"      
                      ,"labelCol"       : "D03"
                      ,"labelColImp"    : "03"
                      ,"obj"            : "cbD03"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de","EMPRESA","FILIAL","CONTADOR","QUALIFICACAOCONT"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":6  ,"field"          : "UP_D04"      
                      ,"labelCol"       : "D04"
                      ,"labelColImp"    : "04"
                      ,"obj"            : "cbD04"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opção SERVIÇO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":7  ,"field"          : "UP_D05"  ,"labelCol":"D05" ,"labelColImp":"05" ,"obj":"cbD05"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opção FAVORECIDO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}  
            ,{"id":8  ,"field"          : "UP_D06"  ,"labelCol":"D06" ,"labelColImp":"06" ,"obj":"cbD06"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opção BANCOS"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":9  ,"field"          : "UP_D07"  ,"labelCol":"D07" ,"labelColImp":"07" ,"obj":"cbD07"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção COMPETENCIA"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":10 ,"field"          : "UP_D08"  ,"labelCol":"D08" ,"labelColImp":"08" ,"obj":"cbD08"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção LOCALIZAÇÃO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":11 ,"field"          : "UP_D09"  ,"labelCol":"D09" ,"labelColImp":"09" ,"obj":"cbD09"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção PRODUTO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":12 ,"field"          : "UP_D10"  ,"labelCol":"D10" ,"labelColImp":"10" ,"obj":"cbD10"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de","OPERACAO PADRAO","CNAB"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":13 ,"field"          : "UP_D11"  ,"labelCol":"D11" ,"labelColImp":"11" ,"obj":"cbD11"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção GRUPO DE FAVORECIDO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":14 ,"field"          : "UP_D12"  ,"labelCol":"D12" ,"labelColImp":"12" ,"obj":"cbD12"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção CATEGORIA/SPED"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":15 ,"field"          : "UP_D13"  ,"labelCol":"D13" ,"labelColImp":"13" ,"obj":"cbD13"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de","CONTA CONTABIL","CONTA RESUMO","BALANCO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":16 ,"field"          : "UP_D14"  ,"labelCol":"D14" ,"labelColImp":"14" ,"obj":"cbD14"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de","CFOP","CSTs","NCM","NATUREZA OPERACAO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":17 ,"field"          : "UP_D15"  ,"labelCol":"D15" ,"labelColImp":"15" ,"obj":"cbD15"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção CONTRATO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":18 ,"field"          : "UP_D16"  ,"labelCol":"D16" ,"labelColImp":"16" ,"obj":"cbD16"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção EMAIL"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":19 ,"field"          : "UP_D17"  ,"labelCol":"D17" ,"labelColImp":"17" ,"obj":"cbD17"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção ALIQUOTA SIMPLES"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":20 ,"field"          : "UP_D18"  ,"labelCol":"D18" ,"labelColImp":"18" ,"obj":"cbD18"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção AGENDA DE TAREFAS"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          : "UP_D19"  ,"labelCol":"D19" ,"labelColImp":"19" ,"obj":"cbD19"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção FERIADO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":22 ,"field"          : "UP_D20"  ,"labelCol":"D20" ,"labelColImp":"20" ,"obj":"cbD20"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de","FORMA COBRANÇA","TIPO DE DOCUMENTO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}  
            ,{"id":23 ,"field"          : "UP_D21"  ,"labelCol":"D21" ,"labelColImp":"21" ,"obj":"cbD21"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção TRANSPORTADORA"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":24 ,"field"          : "UP_D22"  ,"labelCol":"D22" ,"labelColImp":"22" ,"obj":"cbD22"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção SERIE DE NOTA FISCAL"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":25 ,"field"          : "UP_D23"  ,"labelCol":"D23" ,"labelColImp":"23" ,"obj":"cbD23"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção PARAMETRO CALCULO IMPOSTO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":26 ,"field"          : "UP_D24"  ,"labelCol":"D24" ,"labelColImp":"24" ,"obj":"cbD24"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção EMBALAGEM"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":27 ,"field"          : "UP_D25"  ,"labelCol":"D25" ,"labelColImp":"25" ,"obj":"cbD25"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de","PARAMETRO EMPRESA","PARAMETROS FINANCEIRO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":28 ,"field"          : "UP_D26"  ,"labelCol":"D26" ,"labelColImp":"26" ,"obj":"cbD26"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção NF PRODUTO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":29 ,"field"          : "UP_D27"  ,"labelCol":"D27" ,"labelColImp":"27" ,"obj":"cbD27"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção NF SERVIÇO"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":30 ,"field"          : "UP_D28"  ,"labelCol":"D28" ,"labelColImp":"28" ,"obj":"cbD28"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção FINANCEIRO/CONTABIL"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":31 ,"field"          : "UP_D29"  ,"labelCol":"D29" ,"labelColImp":"29" ,"obj":"cbD29"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção VENDEDOR"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":32 ,"field"          : "UP_D30"  ,"labelCol":"D30" ,"labelColImp":"30" ,"obj":"cbD30"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [  "Direito para opções de","PARAMETRO CNAB"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}  
            ,{"id":33 ,"field"          : "UP_D31"  ,"labelCol":"D31" ,"labelColImp":"31" ,"obj":"cbD31"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção REGISTRO SISTEMA"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":34 ,"field"          : "UP_D32"  ,"labelCol":"D32" ,"labelColImp":"32" ,"obj":"cbD32"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção AGENDA"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":35 ,"field"          : "UP_D33"  ,"labelCol":"D33" ,"labelColImp":"33" ,"obj":"cbD33"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":36 ,"field"          : "UP_D34"  ,"labelCol":"D34" ,"labelColImp":"34" ,"obj":"cbD34"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0} 
            ,{"id":37 ,"field"          : "UP_D35"  ,"labelCol":"D35" ,"labelColImp":"35" ,"obj":"cbD35"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção modelo/entrada em estoque"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":38 ,"field"          : "UP_D36"  ,"labelCol":"D36" ,"labelColImp":"36" ,"obj":"cbD36"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Auto"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0} 
            ,{"id":39 ,"field"          : "UP_D37"  ,"labelCol":"D37" ,"labelColImp":"37" ,"obj":"cbD37"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":40 ,"field"          : "UP_D38"  ,"labelCol":"D38" ,"labelColImp":"38" ,"obj":"cbD38"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção veiculo/parametros"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":41 ,"field"          : "UP_D39"  ,"labelCol":"D39" ,"labelColImp":"39" ,"obj":"cbD39"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Aprovar pedido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":42 ,"field"          : "UP_D40"  ,"labelCol":"D40" ,"labelColImp":"40" ,"obj":"cbD40"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Contrato"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}  
                      
            ,{"id":43 ,"field"          : "UP_D41"  ,"labelCol":"D41" ,"labelColImp":"41" ,"obj":"cbD41"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":44 ,"field"          : "UP_D42"  ,"labelCol":"D42" ,"labelColImp":"42" ,"obj":"cbD42"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":45 ,"field"          : "UP_D43"  ,"labelCol":"D43" ,"labelColImp":"43" ,"obj":"cbD43"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":46 ,"field"          : "UP_D44"  ,"labelCol":"D44" ,"labelColImp":"44" ,"obj":"cbD44"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0} 
            ,{"id":47 ,"field"          : "UP_D45"  ,"labelCol":"D45" ,"labelColImp":"45" ,"obj":"cbD45"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":48 ,"field"          : "UP_D46"  ,"labelCol":"D46" ,"labelColImp":"46" ,"obj":"cbD46"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0} 
            ,{"id":49 ,"field"          : "UP_D47"  ,"labelCol":"D47" ,"labelColImp":"47" ,"obj":"cbD47"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":50 ,"field"          : "UP_D48"  ,"labelCol":"D48" ,"labelColImp":"48" ,"obj":"cbD48"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":51 ,"field"          : "UP_D49"  ,"labelCol":"D49" ,"labelColImp":"49" ,"obj":"cbD49"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":52 ,"field"          : "UP_D50"  ,"labelCol":"D50" ,"labelColImp":"50" ,"obj":"cbD50"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "5"                      
                      ,"fieldType"      : "int"
                      ,"copyGRD"        : [0,1]
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["notnull","intMaiorIgualZero"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}  
            ,{"id":53 ,"field"          : "UP_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "10"
                      ,"padrao":2}                                        
            ,{"id":54 ,"field"          : "UP_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"tamImp"         : "10"
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":55 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario" 
                      ,"tamGrd"         : "10em"
                      ,"padrao":4}                
            ,{"id":56 ,"field"          : "UP_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":57 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP"        
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objUp.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"300px" 
              ,"label"          :"PERFIL - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"              }
            ,{"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"   }
            ,{"texto":"Excluir"   ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"             }
            ,{"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"       }        
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"              }                    
            ,{"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"             }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmUp"               // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaUp"            // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmUp"               // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblUp"               // Nome da table
          ,"prefixo"        : "up"                  // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VUSUARIOPERFIL"      // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPUSUARIOPERFIL"    // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "UP_ATIVO"            // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "UP_REG"              // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "UP_CODUSR"           // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          //,"fieldCodEmp"  : "*"                   // SE EXISITIR - Nome do campo CODIGO EMPRESA na tabela BD            
          //,"fieldCodDir"  : "*"                   // SE EXISITIR - Nome do campo CODIGO DIREITO na tabela BD                        
          ,"width"          : "80em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "PERFIL"              // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtDescricao"
                              ,"edtDescricao"
                              ,"btnConfirmar"]      // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"     // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "DESCRICAO"           // Indice inicial da table
          ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"              // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               //,["Imprimir registros em tela"             ,"fa-print"         ,"objUp.imprimir()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objUp.AjudaSisAtivo(jsUp);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objUp.detalhe();"]
                               //,["Gerar excel"                            ,"fa-file-excel-o"  ,"objUp.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objUp.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objUp.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objUp.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objUp.altRegSistema("+jsPub[0].usr_d31+");"] 
                               ,["Número de registros em tela"            ,"fa-info"          ,"objUp.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]
                             ]  
          ,"codTblUsu"      : "PERFIL[04]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objUp === undefined ){  
          objUp=new clsTable2017("objUp");
        };  
        objUp.montarHtmlCE2017(jsUp); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsUp.form).style.width="110em";
        //
        //
        //////////////////////////////////////////////
        // Montando a table para importar xls       //
        //////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"CODIGO"     ,"labelCol":"CODIGO"    ,"tamGrd":"6em"  ,"tamImp":"20","align":"center"}
            ,{"id":1  ,"field":"DESCRICAO"  ,"labelCol":"DESCRICAO" ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":2  ,"field":"D01"        ,"labelCol":"D01"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":3  ,"field":"D02"        ,"labelCol":"D02"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":4  ,"field":"D03"        ,"labelCol":"D03"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":5  ,"field":"D04"        ,"labelCol":"D04"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":6  ,"field":"D05"        ,"labelCol":"D05"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":7  ,"field":"D06"        ,"labelCol":"D06"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":8  ,"field":"D07"        ,"labelCol":"D07"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":9  ,"field":"D08"        ,"labelCol":"D08"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":10 ,"field":"D09"        ,"labelCol":"D09"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":11 ,"field":"D10"        ,"labelCol":"D10"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":12 ,"field":"D11"        ,"labelCol":"D11"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":13 ,"field":"D12"        ,"labelCol":"D12"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":14 ,"field":"D13"        ,"labelCol":"D13"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":15 ,"field":"D14"        ,"labelCol":"D14"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":16 ,"field":"D15"        ,"labelCol":"D15"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":17 ,"field":"D16"        ,"labelCol":"D16"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":18 ,"field":"D17"        ,"labelCol":"D17"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":19 ,"field":"D18"        ,"labelCol":"D18"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":20 ,"field":"D19"        ,"labelCol":"D19"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":21 ,"field":"D20"        ,"labelCol":"D20"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":22 ,"field":"D21"        ,"labelCol":"D21"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":23 ,"field":"D22"        ,"labelCol":"D22"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":24 ,"field":"D23"        ,"labelCol":"D23"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":25 ,"field":"D24"        ,"labelCol":"D24"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":26 ,"field":"D25"        ,"labelCol":"D25"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":27 ,"field":"D26"        ,"labelCol":"D26"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":28 ,"field":"D27"        ,"labelCol":"D27"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":29 ,"field":"D28"        ,"labelCol":"D28"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":30 ,"field":"D29"        ,"labelCol":"D29"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":31 ,"field":"D30"        ,"labelCol":"D30"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":32 ,"field":"D31"        ,"labelCol":"D31"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":33 ,"field":"D32"        ,"labelCol":"D32"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":34 ,"field":"D33"        ,"labelCol":"D33"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":35 ,"field":"D34"        ,"labelCol":"D34"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":36 ,"field":"D35"        ,"labelCol":"D35"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":37 ,"field":"D36"        ,"labelCol":"D36"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":38 ,"field":"D37"        ,"labelCol":"D37"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":39 ,"field":"D38"        ,"labelCol":"D38"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":40 ,"field":"D39"        ,"labelCol":"D39"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":41 ,"field":"D40"        ,"labelCol":"D40"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":42 ,"field":"D41"        ,"labelCol":"D41"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":43 ,"field":"D42"        ,"labelCol":"D42"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":44 ,"field":"D43"        ,"labelCol":"D43"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":45 ,"field":"D44"        ,"labelCol":"D44"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":46 ,"field":"D45"        ,"labelCol":"D45"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":47 ,"field":"D46"        ,"labelCol":"D46"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":48 ,"field":"D47"        ,"labelCol":"D47"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":49 ,"field":"D48"        ,"labelCol":"D48"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":50 ,"field":"D49"        ,"labelCol":"D49"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":51 ,"field":"D50"        ,"labelCol":"D50"       ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":52 ,"field":"ERRO"       ,"labelCol":"ERRO"      ,"tamGrd":"35em" ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[42].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                                
          ,"div"            : "frmExc"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaExc"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmExc"              // Onde vai ser gerado o fieldSet                     
          ,"divModal"       : "divTopoInicioE"      // Nome da div que vai fazer o show modal
          ,"tbl"            : "tblExc"              // Nome da table
          ,"prefixo"        : "exc"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                   // Nome da tabela no banco de dados  
          ,"width"          : "90em"                // Tamanho da table
          ,"height"         : "48em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "Importação perfil"   // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"indiceTable"    : "TAG"                 // Indice inicial da table
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"formName"       : "frmExc"              // Nome do formulario para opção de impressão 
          ,"tamBotao"       : "20"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
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
      var objUp;                      // Obrigatório para instanciar o JS TFormaCob
      var jsUp;                       // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
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
        clsJs.add("rotina"      , "selectUp"          );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        fd = new FormData();
        fd.append("usuarioperfil" , clsJs.fim());
        msg     = requestPedido("Trac_UsuarioPerfil.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsUp.registros=objUp.addIdUnico(retPhp[0]["dados"]);
          objUp.ordenaJSon(jsUp.indiceTable,false);  
          objUp.montarBody2017();
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
            clsJs.add("titulo"      , objUp.trazCampoExcel(jsUp)  );    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("usuarioperfil" , envPhp              );
            fd.append("arquivo"       , edtArquivo.files[0] );
            msg     = requestPedido("Trac_UsuarioPerfil.php",fd); 
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
            gerarMensagemErro("UP",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
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
              name="frmUp" 
              id="frmUp" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Perfil<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 50em; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo25">
                <input class="campo_input"  name="edtCodigo" id="edtCodigo" type="text" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo75">
                <input class="campo_input"  name="edtDescricao" id="edtDescricao" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD01" id="cbD01">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD01">USUARIOS[01]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD02" id="cbD02">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD02">USUARIO->OPERAC[02]</label>
              </div>
              
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD03" id="cbD03">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD03">EMPRESA[03]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD04" id="cbD04">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD04">SERVICO[04]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD05" id="cbD05">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD05">FAVORECIDO[05]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD06" id="cbD06">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD06">BANCO[06]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD07" id="cbD07">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD07">COMPETENCIA[07]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD08" id="cbD08">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD08">LOCALIZAÇÃO[08]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD09" id="cbD09">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD09">PRODUTO[09]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD10" id="cbD10">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD10">OP PADRAO[10]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD11" id="cbD11">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD11">GRP FAVORECIDO[11]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD12" id="cbD12">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD12">CATEGORIA[12]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD13" id="cbD13">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD13">CONTABIL[13]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD14" id="cbD14">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD14">CFOP...[14]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD15" id="cbD15">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD15">CONTRATO[15]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD16" id="cbD16">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD11">EMAIL[16]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD17" id="cbD17">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD17">ALIQ SIMPLES[17]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD18" id="cbD18">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD18">AGENDA TAREFAS[18]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD19" id="cbD19">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD19">FERIADO[19]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD20" id="cbD20">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD20">FORMA COBRAN...[20]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD21" id="cbD21">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD21">TRANSPORTADORA[21]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD22" id="cbD22">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD22">SERIE NF[22]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD23" id="cbD23">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD23">IMPOSTO[23]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD24" id="cbD24">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD24">EMBALAGEM[24]</label>
              </div>
              <!---->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD25" id="cbD25">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD25">PARAMETRO EMPRESA[25]</label>
              </div>
              
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD26" id="cbD26">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD26">NF PRODUTO[26]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD27" id="cbD27">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD27">NF SERVICO[27]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD28" id="cbD28">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD28">TITULO FINANCEIRO[28]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD29" id="cbD29">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD29">VENDEDOR[29]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD30" id="cbD30">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD30">PARAMETRO CNAB[30]</label>
              </div>
              <!--
                O direito 31 eh do sistema, aqui coloco onde todos podem entrar
                Ex:Alterar minha senha
              -->
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD31" id="cbD31">
                  <option value="0">0-NAO</option>
                  <option value="4">4-SIM</option>
                </select>
                <label class="campo_label campo_required" for="cbD31">REG SISTEMA[31]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD32" id="cbD32">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD32">AGENDA[32]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD33" id="cbD33">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD33">ESTOQUE[33]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD34" id="cbD34">
                  <option value="0">0-NAO</option>
                  <option value="4">4-SIM</option>
                </select>
                <label class="campo_label campo_required" for="cbD34">BAIXA FINANCEIRA[34]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD35" id="cbD35">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD35">MODELO/ESTOQUE[35]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD36" id="cbD36">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD36">AUTO[36]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD37" id="cbD37">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-DO VENDEDOR</option>
                  <option value="4">4-TODOS</option>
                </select>
                <label class="campo_label campo_required" for="cbD37">PEDIDO[37]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD38" id="cbD38">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD38">VEICULO/PARAMETROS[38]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD39" id="cbD39">
                  <option value="0">0-NAO</option>
                  <option value="4">4-SIM</option>
                </select>
                <label class="campo_label campo_required" for="cbD39">APROVAR PEDIDO[39]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD40" id="cbD40">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD40">CONTRATO[40]</label>
              </div>
              
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD41" id="cbD41">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD41">...[41]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD42" id="cbD42">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD42">...[42]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD43" id="cbD43">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD43">...[43]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD44" id="cbD44">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD44">...[44]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD45" id="cbD45">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD45">...[45]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD46" id="cbD46">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD46">...[46]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD47" id="cbD47">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD47">...[47]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD48" id="cbD48">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD48">...[48]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD49" id="cbD49">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD49">...[49]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbD50" id="cbD50">
                  <option value="0">0-SEM DIREITO</option>
                  <option value="1">1-CON</option>
                  <option value="2">2-CON/INC</option>
                  <option value="3">3-CON/INC/ALT</option>
                  <option value="4">4-CON/INC/ALT/EXC</option>
                </select>
                <label class="campo_label campo_required" for="cbD50">...[50]</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbAtivo" id="cbAtivo">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">ATIVO</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" name="cbReg" id="cbReg">
                  <option value="P">PUBLICO</option>               
                </select>
                <label class="campo_label campo_required" for="cbReg">REG</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input_titulo" disabled name="edtUsuario" id="edtUsuario" type="text" />
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
      <!--Importar excel( fncExcel() monta html interno da div abaixo ) -->
      <div id="divExcel" class="divTopoExcel">
      </div>
      <!--/Fim Importar excel -->
    </div>       
  </body>
</html>