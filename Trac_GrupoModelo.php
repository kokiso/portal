<?php
  session_start();
  if( isset($_POST["grupomodelo"]) ){   
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");       

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["grupomodelo"]);
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

        //////////////////////////////////////////////////////
        //  Dados para JavaScript GRUPOPRODUTO/GRUPOMODELO  //
        // Select somente uma vez                           //
        //////////////////////////////////////////////////////
        if( $rotina=="selectPrd" ){
          $tblPrd="*";
          $sql="SELECT PRD_CODIGO AS CODIGO,PRD_NOME AS NOME , PRD_CODNCM AS NCM FROM PRODUTO WHERE PRD_ATIVO='S'";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] == "OK" ){
            $tblPrd=$retCls['dados'];    
          };
          
          if( ($tblPrd=="*")){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{ "retorno":"OK"
                        ,"tblPrd":'.json_encode($tblPrd).'                      
                        ,"erro":""}]'; 
          };  
        };
        //////////////////////////////////////////////////
        //       Dados para JavaScript GRUPOMODELO      //
        //////////////////////////////////////////////////
        if( $rotina=="selectGm" ){
          $sql="SELECT A.GM_CODIGO
                       ,A.GM_NOME          
                       ,A.GM_CODFBR
                       ,FVR.FVR_APELIDO
                       ,A.GM_CODGP
                       ,GP.GP_NOME
                       ,GM_ESTOQUE
                       ,(SELECT COUNT(GMP_CODIGO) FROM GRUPOMODELOPRODUTO WHERE GMP_CODGM = A.GM_CODIGO AND GMP_STATUS = 1) AS ESTOQUE_REAL
                       ,GM_ESTOQUEMINIMO
                       ,GM_ESTOQUESUCATA
                       ,GM_ESTOQUEAUTO                       
                       ,CASE WHEN A.GM_NUMSERIE='S' THEN 'SIM' ELSE 'NAO' END AS GM_NUMSERIE
                       ,CASE WHEN A.GM_SINCARD='S' THEN 'SIM' ELSE 'NAO' END AS GM_SINCARD
                       ,CASE WHEN A.GM_OPERADORA='S' THEN 'SIM' ELSE 'NAO' END AS GM_OPERADORA
                       ,CASE WHEN A.GM_FONE='S' THEN 'SIM' ELSE 'NAO' END AS GM_FONE
                       ,CASE WHEN A.GM_VENDA='S' THEN 'SIM' ELSE 'NAO' END AS GM_VENDA
                       ,CASE WHEN A.GM_LOCACAO='S' THEN 'SIM' ELSE 'NAO' END AS GM_LOCACAO
                       ,CASE WHEN A.GM_CONTRATO='S' THEN 'SIM' ELSE 'NAO' END AS GM_CONTRATO
                       ,COALESCE(A.GM_PRDCODIGO,'SEM VINCULO')
                       ,A.GM_VALORVISTA                       
                       ,A.GM_VALORPRAZO
                       ,A.GM_VALORMINIMO                       
                       ,A.GM_FIRMWARE
                       ,CASE WHEN A.GM_MENSURAVEL='S' THEN 'SIM' ELSE 'NAO' END AS GM_MENSURAVEL
                       ,CASE WHEN A.GM_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS GM_ATIVO
                       ,CASE WHEN A.GM_REG='P' THEN 'PUB' WHEN A.GM_REG='S' THEN 'SIS' ELSE 'ADM' END AS GM_REG
                       ,US.US_APELIDO
                       ,A.GM_CODUSR
                  FROM GRUPOMODELO A
                  LEFT OUTER JOIN USUARIOSISTEMA US ON A.GM_CODUSR=US.US_CODIGO
                  LEFT OUTER JOIN FAVORECIDO FVR ON A.GM_CODFBR=FVR.FVR_CODIGO
                  LEFT OUTER JOIN GRUPOPRODUTO GP ON A.GM_CODGP=GP.GP_CODIGO                  
                 WHERE ((GM_CODGP<>'AUT') AND ((GM_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))
                 GROUP BY A.GM_CODIGO 
                       ,A.GM_NOME          
                       ,A.GM_CODFBR
                       ,FVR.FVR_APELIDO
                       ,A.GM_CODGP
                       ,GP.GP_NOME
                       ,GM_ESTOQUE
                        ,GM_ESTOQUEMINIMO
                       ,GM_ESTOQUESUCATA
                       ,GM_ESTOQUEAUTO 
                        ,GM_ESTOQUEAUTO                       
                       ,GM_NUMSERIE
                       ,GM_SINCARD
                       ,GM_OPERADORA
                       ,GM_FONE
                       ,GM_VENDA
                       ,GM_LOCACAO
                       ,GM_CONTRATO
                       ,A.GM_PRDCODIGO
                       ,A.GM_VALORVISTA                       
                       ,A.GM_VALORPRAZO
                       ,A.GM_VALORMINIMO                       
                       ,A.GM_FIRMWARE
                       ,GM_MENSURAVEL
                       ,GM_ATIVO
                       ,GM_REG
                       ,US.US_APELIDO
                       ,A.GM_CODUSR"; 
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
          $vldCampo = new validaCampo("VGRUPOMODELO",0);
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
    <title>Modelo</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaFabricanteF10.js"></script>        
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        buscarPrd()
        ////////////////////////////////////////////////
        //      Objeto clsTable2017 GRUPOMODELO       //
        ////////////////////////////////////////////////
        jsGm={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"GM_CODIGO" 
                      ,"fieldType"      : "int"            
                      ,"autoIncremento" : "S"                      
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"align"          : "center"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "GM_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"newRecord"      : ["","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "70"
                      ,"digitosMinMax"  : [3,30]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|(|)| "
                      ,"ajudaCampo"     : ["Nome da modelo com até 50 caracteres."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"GM_CODFBR" 
                      ,"fieldType"      : "int"        
                      ,"insUpDel"       : ["S","N","N"]                      
                      ,"labelCol"       : "CODFBR"
                      ,"obj"            : "edtCodFbr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"align"          : "center"
                     ,"newRecord"         : ["0000","this","this"]  
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "FVR_APELIDO"
                      ,"labelCol"       : "FABRICANTE"
                      ,"fieldType"      : "str"
                      ,"tipo"           : "edt"
                      ,"obj"            : "edtDesFbr"
                      ,"newRecord"      : ["","this","this" ]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"validar"        : ["notnull"]
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"importaExcel"   : "N"
                      ,"excel"          : "S"
                      ,"hint"           : "S"
                      ,"ordenaColuna"   : "S"
                      ,"inputDisabled"  : false
                      ,"ajudaCampo"     : ["Razão social do cliente."]
                      ,"padrao":0}
            ,{"id":5  ,"field"          :"GM_CODGP" 
                      ,"labelCol"       : "GRUPO"
                      ,"obj"            : "edtCodGp"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|-"
                      ,"newRecord"      : ["","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [2,5] 
                      ,"align"          : "center"
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do evento conforme definição empresa. Este campo é único e deve tem o formato AAA"
                                            ,"Campo pode ser utilizado em cadastros de monitoramento"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "GP_NOME"
                      ,"labelCol"       : "NOME_GRUPO"
                      ,"fieldType"      : "str"
                      ,"tipo"           : "edt"
                      ,"obj"            : "edtDesGp"
                      ,"newRecord"      : ["","this","this" ]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"validar"        : ["notnull"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"importaExcel"   : "N"
                      ,"excel"          : "S"
                      ,"hint"           : "S"
                      ,"inputDisabled"  : false
                      ,"ajudaCampo"     : ["Razão social do cliente."]
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "GM_ESTOQUE" 
                      ,"fieldType"      : "int"            
                      ,"labelCol"       : "ESTOQUE"
                      ,"obj"            : "edtEstoque"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"align"          : "center"
                      ,"newRecord"       : ["0000","this","this"] 
                      ,"formato"        : ["i4"]
                      ,"popoverLabelCol": "Estoque Total"
                      ,"popoverTitle"   : "Informação do estoque total (em uso e em estoque)"
                      ,"validar"        : ["intMaiorIgualZero"]
                      ,"importaExcel"   : "N"                                                                
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "ESTOQUE_REAL" 
                      ,"fieldType"      : "int"            
                      ,"labelCol"       : "ER"
                      ,"obj"            : "edtEstoqueReal"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"align"          : "center"
                      ,"formato"        : ["i4"]
                      ,"popoverLabelCol": "Estoque Real"
                      ,"popoverTitle"   : "Informação do estoque interno real"
                      ,"validar"        : ["intMaiorIgualZero"]
                      ,"importaExcel"   : "N"                                                                
                      ,"padrao":0}
            ,{"id":9  ,"field"          :"GM_ESTOQUEMINIMO" 
                      ,"fieldType"      : "int"        
                      ,"insUpDel"       : ["S","S","N"]                      
                      ,"labelCol"       : "EM"
                      ,"obj"            : "edtEstoqueMinimo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"align"          : "center"
                     ,"newRecord"       : ["0000","this","this"]  
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorIgualZero"]
                      ,"importaExcel"   : "S"  
                      ,"popoverLabelCol": "Estoque Minimo"
                      ,"popoverTitle"   : "Informação referente estoque minimo para relatório de compra"
                      ,"padrao":0}
            ,{"id":10  ,"field"          :"GM_ESTOQUESUCATA" 
                      ,"fieldType"      : "int"        
                      ,"insUpDel"       : ["N","N","N"]                      
                      ,"labelCol"       : "SUC"
                      ,"obj"            : "edtEstoqueSucata"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"align"          : "center"
                     ,"newRecord"       : ["0000","this","this"]  
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorIgualZero"]
                      ,"popoverLabelCol": "Sucata"
                      ,"popoverTitle"   : "Total individual de produto transformado em sucata"
                      ,"padrao":0}
            ,{"id":11 ,"field"          :"GM_ESTOQUEAUTO" 
                      ,"fieldType"      : "int"        
                      ,"insUpDel"       : ["N","N","N"]                      
                      ,"labelCol"       : "AUT"
                      ,"obj"            : "edtEstoqueAuto"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"align"          : "center"
                     ,"newRecord"       : ["0000","this","this"]  
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorIgualZero"]
                      ,"popoverLabelCol": "Auto"
                      ,"popoverTitle"   : "Total individual de produto adicionados a autos"
                      ,"padrao":0}
            ,{"id":12 ,"field"          : "GM_NUMSERIE"  
                      ,"labelCol"       : "SERIE" 
                      ,"obj"            : "cbNumSerie"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro para quando cadastrar produto individual o numero de serie é obrigatorio"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":13 ,"field"          : "GM_SINCARD"  
                      ,"labelCol"       : "SINCARD" 
                      ,"obj"            : "cbSinCard"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro para quando cadastrar produtu individual o numero do sincard é obrigatorio"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":14 ,"field"          : "GM_OPERADORA"  
                      ,"labelCol"       : "OPE" 
                      ,"obj"            : "cbOperadora"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "20"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro para quando cadastrar produtu individual a operadora é obrigatorio"]
                      ,"popoverTitle"   : "Se é necessário informar uma operadora"                                                                  
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":15 ,"field"          : "GM_FONE"  
                      ,"labelCol"       : "FONE" 
                      ,"obj"            : "cbFone"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro para quando cadastrar produtu individual o numero fone é obrigatorio"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":16 ,"field"          : "GM_VENDA"  
                      ,"labelCol"       : "VENDA" 
                      ,"obj"            : "cbVenda"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro se o produto pode ser vendido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}      
            ,{"id":17 ,"field"          : "GM_LOCACAO"  
                      ,"labelCol"       : "LOCACAO" 
                      ,"obj"            : "cbLocacao"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro se o produto pode ser locado"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}      
            ,{"id":18 ,"field"          : "GM_CONTRATO"  
                      ,"labelCol"       : "CONTRATO" 
                      ,"obj"            : "cbContrato"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "20"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro se o produto pode tem contrato"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":19 ,"field"          : "GM_PRDCODIGO"  
                      ,"labelCol"       : "PRODUTO_FISCAL" 
                      ,"obj"            : "edtPrdCodigo"                     
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"align"          : "center"                                                                                   
                      ,"padrao":0}      
            ,{"id":20 ,"field"          : "GM_VALORVISTA"  
                      ,"labelCol"       : "VLRVISTA" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtValorVista"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          : "GM_VALORPRAZO"  
                      ,"labelCol"       : "VLRPRAZO" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtValorPrazo"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":22 ,"field"          : "GM_VALORMINIMO"  
                      ,"labelCol"       : "VLRMINIMO" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtValorMinimo"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":23 ,"field"          :"GM_FIRMWARE" 
                      ,"labelCol"       : "FIRMWARE"
                      ,"obj"            : "edtFirmWare"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|-| "
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"digitosMinMax"  : [2,5] 
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":24 ,"field"          : "GM_MENSURAVEL" 
                      ,"labelCol"       : "MENSURAVEL"
                      ,"obj"            : "cbMensuravel"
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.add('fontVerde'))"             
                      ,"padrao":2}
            ,{"id":25 ,"field"          : "GM_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"padrao":2}                                        
            ,{"id":26 ,"field"          : "GM_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":27 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"tamImp"         : "0"                                            
                      ,"obj"            : "edtUsuario" 
                      ,"padrao":4}                
            ,{"id":28 ,"field"          : "GM_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":29 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP"        
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objGm.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"300px" 
              ,"label"          :"ALERTA - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar"       ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"              }
            ,{"texto":"Alterar"         ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"   }
            ,{"texto":"Excluir"         ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"             }
            ,{"texto":"Entrada estoque" ,"name":"horEntEst"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-shopping-cart"     }
            ,{"texto":"Lote importado"  ,"name":"horEntLot"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-clone"             }
            ,{"texto":"Ver individual"  ,"name":"horIndividual" ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-eye-slash"         }            
            ,{"texto":"Fechar"          ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"             }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)              
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnCancelar"  : "btnCancelar"             // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmGm"                   // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaGm"                // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmGm"                   // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblGm"                   // Nome da table
          ,"prefixo"        : "Gm"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VGRUPOMODELO"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPGRUPOMODELO"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "GM_ATIVO"                // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "GM_REG"                  // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "GM_CODUSR"               // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"             // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "95em"                    // Tamanho da table
          ,"height"         : "58em"                    // Altura da table
          ,"tableLeft"      : "sim"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "MODELO"                  // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"foco"           : ["edtCodGp"
                              ,"edtDescricao"
                              ,"btnConfirmar"]          // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "DESCRICAO"               // Indice inicial da table
          ,"tamBotao"       : "20"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"                  // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objGm.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objGm.AjudaSisAtivo(jsGm);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objGm.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objGm.espiao();"]
                               ,["Imprimir registros em tela"             ,"fa-print"         ,"objGm.imprimir()"]
                               ,["Gerar excel"                            ,"fa-file-excel-o"  ,"objGm.excel();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objGm.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objGm.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objGm.altRegSistema("+jsPub[0].usr_d31+");"] 
                               ,["Número de registros em tela"            ,"fa-info"          ,"objGm.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "GRUPOMODELO[35]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objGm === undefined ){  
          objGm=new clsTable2017("objGm");
        };  
        objGm.montarHtmlCE2017(jsGm); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsGm,["CODIGO","CODFBR","CONTRATO","DESCRICAO","ESTOQUE","FABRICANTE","FONE","GRUPO","OPE","SERIE","SINCARD","SUC"]);
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsGm.form).style.width=jsGm.width;
        //////////////////////////////////////////////
        // Montando a table para importar xls       //
        //////////////////////////////////////////////
        fncExcel("divExcel");
        jsExc={
          "titulo":[
             {"id":0  ,"field":"CODIGO"     ,"labelCol":"CODIGO"            ,"tamGrd":"6em"   ,"tamImp":"20"  }
            ,{"id":1  ,"field":"DESCRICAO"  ,"labelCol":"DESCRICAO"         ,"tamGrd":"25em"  ,"tamImp":"50"  }
            ,{"id":2  ,"field":"CODFVR"     ,"labelCol":"COD_FABRICANTE"    ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":3  ,"field":"GRUPO"      ,"labelCol":"COD_GRUPO"         ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":4  ,"field":"SERIE"      ,"labelCol":"INFORMA_SERIE"     ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":5  ,"field":"SINCARD"    ,"labelCol":"INFORMA_SINCARD"   ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":6  ,"field":"OPERADORA"  ,"labelCol":"INFORMA_OPERADORA" ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":7  ,"field":"FONE"       ,"labelCol":"INFORMA_FONE"      ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":8  ,"field":"VENDA"      ,"labelCol":"VENDA_LOCACAO"     ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":9  ,"field":"CONTRATO"   ,"labelCol":"CONTRATO"          ,"tamGrd":"8em"  ,"tamImp":"20"   }
            ,{"id":10 ,"field":"ERRO"       ,"labelCol":"ERRO"              ,"tamGrd":"35em"  ,"tamImp":"100" }            
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[10].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
          ,"relTitulo"      : "Importação alerta"       // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"indiceTable"    : "TAG"                     // Indice inicial da table
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"formName"       : "frmExc"                  // Nome do formulario para opção de impressão 
          ,"tamBotao"       : "20"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
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
      var objGm;                      // Obrigatório para instanciar o JS TFormaCob
      var jsGm;                       // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS PadraoF10      
      var objFbrF10;                  // Obrigatório para instanciar o JS FabricanteF10            
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var tblInd;                     // Guardando os modelos para "horIndividual"
      var tblPrd                       // Tabela GRUPOPRODUTO  
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario/Trac_GrupoModeloInd.php      
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d35);

      function buscarPrd(){
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "selectPrd"          );
        clsJs.add("login"   , jsPub[0].usr_login  );
        fd = new FormData();
        fd.append("grupomodelo" , clsJs.fim());
        msg     = requestPedido("Trac_GrupoModelo.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          tblPrd=retPhp[0]["tblPrd"];  
        };  
      };  
      //
      //
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
        clsJs.add("rotina"      , "selectGm"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        fd = new FormData();
        fd.append("grupomodelo" , clsJs.fim());
        msg     = requestPedido("Trac_GrupoModelo.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsGm.registros=objGm.addIdUnico(retPhp[0]["dados"]);
          objGm.ordenaJSon(jsGm.indiceTable,false);  
          objGm.montarBody2017();
          /////////////////////////////////////////
          // Guardando para ver modelos individuais
          /////////////////////////////////////////
          clsJs   = jsString("lote");  
          retPhp[0]["dados"].forEach(function(reg){
            clsJs.add("cod" , jsNmrs(reg[0]).inteiro().ret()  );
            clsJs.add("des" , reg[1]                          );
          });
          tblInd=clsJs.fim();
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
            clsJs.add("rotina"      , "impExcel"                    );
            clsJs.add("login"       , jsPub[0].usr_login            );
            clsJs.add("titulo"      , objGm.trazCampoExcel(jsGm)  );
            envPhp=clsJs.fim();
            fd = new FormData();
            fd.append("grupomodelo"   , envPhp              );
            fd.append("arquivo" , edtArquivo.files[0] );
            msg     = requestPedido("Trac_GrupoModelo.php",fd); 
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
            gerarMensagemErro("gm",retPhp[0].erro,{cabec:"Aviso"});  
          };  
        } catch(e){
          gerarMensagemErro("gm","ERRO NO ARQUIVO XML",{cabec:"Aviso"});            
        };          
      };
      ////////////////////////////
      //  AJUDA PARA FABRICANTE //
      ////////////////////////////
      function fbrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fbrF10Click(obj){ 
        fFabricanteF10(0,obj.id,"edtEstoqueMinimo",100,{codgp: document.getElementById("edtCodGp").value,ativo:"S" } ); 
      };
      function RetF10tblFbr(arr){
        document.getElementById("edtCodFbr").value      = arr[0].CODIGO;
        document.getElementById("edtDesFbr").value      = arr[0].DESCRICAO;
        document.getElementById("edtCodFbr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codFbrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fFabricanteF10(1,obj.id,"edtEstoqueMinimo",100,
            {codfvr  : elNew
             ,codgp  : document.getElementById("edtCodGp").value
             ,ativo  : "S"} 
            ); 
          //  
          document.getElementById(obj.id).value           = ( arr.length == 0 ? "0000"          : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          document.getElementById("edtDesFbr").value      = ( arr.length == 0 ? "*"             : arr[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )  );
        };
      };
      //////////////////////////////
      //  AJUDA PARA GRUPOPRODUTO //
      //////////////////////////////
      function gpFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function gpF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtDescricao"
                      ,topo:100
                      ,tableBd:"GRUPOPRODUTO"
                      ,fieldCod:"A.GP_CODIGO"
                      ,fieldDes:"A.GP_NOME"
                      ,fieldAtv:"A.GP_ATIVO"
                      ,typeCod :"str" 
                      ,where: "AND (A.GP_CODIGO NOT IN('AUT'))"
                      ,tbl:"tblGp"}
        );
      };
      function RetF10tblGp(arr){
        document.getElementById("edtCodGp").value  = arr[0].CODIGO;
        document.getElementById("edtDesGp").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodGp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodGpBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtDescricao"
                                  ,topo:100
                                  ,tableBd:"GRUPOPRODUTO"
                                  ,fieldCod:"A.GP_CODIGO"
                                  ,fieldDes:"A.GP_NOME"
                                  ,fieldAtv:"A.GP_ATIVO"
                                  ,typeCod :"str" 
                                  ,where: "AND (A.GP_CODIGO NOT IN('AUT'))"
                                  ,tbl:"tblGp"}
          );
          document.getElementById(obj.id).value      = ( ret.length == 0 ? ""  : ret[0].CODIGO                  );
          document.getElementById("edtDesGp").value  = ( ret.length == 0 ? ""      : ret[0].DESCRICAO           );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO ) );
        };
      };
      //
      function horEntEstClick(){
        if( intCodDir != 4 ){
          gerarMensagemErro("gm","USUARIO NÃO POSSUI FLAG DE DIREITO 4 PARA ESTA ROTINA",{cabec:"Erro"});            
        } else {
          try{
            clsChecados = objGm.gerarJson("1");
            chkds       = clsChecados.gerar();
            
            chkds.forEach(function(reg){
              if( reg.ATIVO == "NAO" )
                throw "Produto com status de inativo!"; 
            });
            //////////////////////////////////////////////////////////////
            // Preparando um objeto para enviar ao formulario de alteracao
            //////////////////////////////////////////////////////////////
            let objEnvio;          
            clsJs=jsString("lote");
            clsJs.add("codgm"       , chkds[0].CODIGO     );
            clsJs.add("codgp"       , chkds[0].GRUPO      );
            clsJs.add("descricao"   , chkds[0].DESCRICAO  );
            clsJs.add("serie"       , chkds[0].SERIE      );            
            clsJs.add("sincard"     , chkds[0].SINCARD    );
            clsJs.add("operadora"   , chkds[0].OPE        );
            clsJs.add("fone"        , chkds[0].FONE       );
            clsJs.add("contrato"    , chkds[0].CONTRATO   );
            clsJs.add("codfbr"      , chkds[0].CODFBR     );
            clsJs.add("foco"        , "nsa"               );  // Usado somente entrada individual pois naum sei qual o primeiro elemento ativo
            clsJs.add("fabricante"  , chkds[0].FABRICANTE );
            clsJs.add("colCodigo"   , objCol.CODIGO       );  // Vou precisar desta coluna para atualizar a grade a partir de Trac_GrupoModeloCad.php
            clsJs.add("colEstoque"  , objCol.ESTOQUE      );  // Vou precisar desta coluna para atualizar a grade a partir de Trac_GrupoModeloCad.php
            objEnvio=clsJs.fim();  
            localStorage.setItem("addAlt",objEnvio);
            window.open("Trac_GrupoModeloCad.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});  
          };
        };
      };
      function prdClick(el,lbl){
        let clsCode = new concatStr();  
        clsCode.concat("<div id='dPaiPrdChk' class='divContainerTable' style='height: 31.2em; width: 45em;border:none'>");
        clsCode.concat("<table id='tblPrdChk' class='fpTable' style='width:100%;'>");
        clsCode.concat("  <thead class='fpThead'>");
        clsCode.concat("    <tr>");
        clsCode.concat("      <th class='fpTh' style='width:30%'>CODIGO</th>");
        clsCode.concat("      <th class='fpTh' style='width:40%'>NOME</th>");
        clsCode.concat("      <th class='fpTh' style='width:20%'>NCM</th>");
        clsCode.concat("      <th class='fpTh' style='width:10%'>SIM</th>");          
        clsCode.concat("    </tr>");
        clsCode.concat("  </thead>");
        clsCode.concat("  <tbody id='tbody_tblChk'>");
        //////////////////////
        // Preenchendo a table
        //////////////////////  
        let arr=[];
        tblPrd.forEach(function(reg){
          arr.push({cod:reg.CODIGO,des:reg.NOME,ncm:reg.NCM ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
        });
        /////////////////////////////////////////////
        // Atualizando a grade com a informacao atual
        /////////////////////////////////////////////
        if( el.value != "NSA" ){
          let splt=(el.value).split("_");  
          splt.forEach( function(sp){
            arr.forEach( function(ar){
              if( ar.cod==sp ){
                ar.sn   = "S";
                ar.fa   = "fa fa-thumbs-o-up";
                ar.cor  = "blue";
              }
            });
          });
        };
        ///////////////////////////////////
        // Mostrando as opcoes para selecao
        ///////////////////////////////////
        arr.forEach(function(reg){
          clsCode.concat("    <tr class='fpBodyTr'>");
          clsCode.concat("      <td class='fpTd textoCentro'>"+reg.cod+"</td>");
          clsCode.concat("      <td class='fpTd'>"+reg.des+"</td>");
          clsCode.concat("      <td class='fpTd'>"+reg.ncm+"</td>");
          clsCode.concat("      <td class='fpTd textoCentro'>");
          clsCode.concat("        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheckPrd((elTr.rowIndex-1));'>");
          clsCode.concat("          <i id='img"+reg.cod+"' data-value='"+reg.sn+"' class='"+reg.fa+"' style='margin-left:10px;font-size:1.5em;color:"+reg.cor+";'></i>");
          clsCode.concat("        </div>");
          clsCode.concat("      </td>");
          clsCode.concat("    </tr>");
        });
        //////  
        // Fim
        //////
        clsCode.concat("  </tbody>");        
        clsCode.concat("</table>");
        clsCode.concat("</div>"); 
        clsCode.concat("<div id='btnPrdConfirmar' onClick='fncJanelaPrdRet(\""+el.id+"\");' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'> Ok</i></div>");        
        janelaDialogo(      
          { height          : "42em"
            ,body           : "16em"
            ,left           : "300px"
            ,top            : "60px"
            ,tituloBarra    : "Selecione Produto "+lbl
            ,code           : clsCode.fim()
            ,width          : "48em"
            ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
          }
        );  
      };
      ///////////////////////////////////////////
      // Marcando e desmarcando os itens da table
      ///////////////////////////////////////////
      function fncCheckPrd(pLin){
        let tbl   = tblPrdChk.getElementsByTagName("tbody")[0];
        let elImg = "img"+tbl.rows[pLin].cells[0].innerHTML;
        let sn    = document.getElementById(elImg).getAttribute("data-value")
        if( sn=="N" ){
          jsCmpAtivo(elImg).remove("fa-thumbs-o-down").add("fa-thumbs-o-up").cor("blue");
          document.getElementById(elImg).setAttribute("data-value","S"); 
        } else {
          jsCmpAtivo(elImg).remove("fa-thumbs-o-up").add("fa-thumbs-o-down").cor("red");
          document.getElementById(elImg).setAttribute("data-value","N"); 
        }
      };
      ///////////////////////////////////////////
      // Recuperando os itens marcados na table
      ///////////////////////////////////////////
      function fncJanelaPrdRet(obj){
        try{              
          let tbl = tblPrdChk.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          let elImg;
          if( nl>0 ){
            let filtroPrd="";
            let filtroPrdQtd="";
            for(let lin=0 ; (lin<nl) ; lin++){
              elImg="img"+tbl.rows[lin].cells[0].innerHTML;
              
              if( document.getElementById(elImg).getAttribute("data-value") == "S" ){
                filtroPrd+=( filtroPrd=="" ? tbl.rows[lin].cells[0].innerHTML: "_".concat(tbl.rows[lin].cells[0].innerHTML) );
                filtroPrdQtd+=( filtroPrdQtd=="" ? tbl.rows[lin].cells[3].children[0].value: "_".concat(tbl.rows[lin].cells[3].children[0].value) );
              };
            };
            if( filtroPrd=="" )
              filtroPrd="NSA"; 
              console.log(obj);
            document.getElementById(obj).value=filtroPrd;

            janelaFechar();
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ///////////////////
      // Lotes importados
      ///////////////////
      function horEntLotClick(){
        if( intCodDir != 4 ){
          gerarMensagemErro("gm","USUARIO NÃO POSSUI FLAG DE DIREITO 4 PARA ESTA ROTINA",{cabec:"Erro"});            
        } else {
          try{
            //////////////////////////////////////////////////////////////
            // Preparando um objeto para enviar ao formulario de alteracao
            //////////////////////////////////////////////////////////////
            let objEnvio;          
            clsJs=jsString("lote");
            clsJs.add("colCodigo"   , objCol.CODIGO       );  // Vou precisar desta coluna para atualizar a grade a partir de Trac_GrupoModeloCad.php
            clsJs.add("colEstoque"  , objCol.ESTOQUE      );  // Vou precisar desta coluna para atualizar a grade a partir de Trac_GrupoModeloCad.php
            objEnvio=clsJs.fim();  
            localStorage.setItem("addAlt",objEnvio);
            window.open("Trac_GrupoModeloLot.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});  
          };
        };
      };
      function horIndividualClick(){
        if( intCodDir != 4 ){
          gerarMensagemErro("gm","USUARIO NÃO POSSUI FLAG DE DIREITO 4 PARA ESTA ROTINA",{cabec:"Erro"});            
        } else {
          try{
            clsChecados = objGm.gerarJson("1");
            chkds       = clsChecados.gerar();
            
            let objEnvio;          
            clsJs=jsString("lote");
            clsJs.add("codgm"      , chkds[0].CODIGO );
            /////////////////////////////////////////////////////////////////
            // Passando as colunas que vou precisas para atualizar esta table
            /////////////////////////////////////////////////////////////////
            for(let key in objCol) { 
              clsJs.add(key, parseInt(objCol[key]) );          
            };            
            objEnvio=clsJs.fim(); 
            localStorage.removeItem("addInd");
            localStorage.removeItem("addTbl")                        
            localStorage.setItem("addInd",objEnvio);
            localStorage.setItem("addTbl",tblInd);
            
            window.open("Trac_GrupoModeloInd.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});  
          };
        };  
      };
      /////////////////////////////
      // Trigger olha esta regra //
      /////////////////////////////
      function btnConfirmarClick(){
        if( jsNmrs("edtValorVista").dolar().ret() > jsNmrs("edtValorPrazo").dolar().ret() ){        
          gerarMensagemErro("GM","VALOR A VISTA NAO PODE SER MAIOR QUE VALOR A PRAZO!",{cabec:"Aviso"});    
        } else { 
          objGm.gravar(true);
        }  
      };
    </script>
  </head>
  <body>
    <div class="divTelaCheia">
      <div id="divRotina" class="conteudo">  
        <div id="divTopoInicio">
        </div>
        <form method="post" 
              name="frmGm" 
              id="frmGm" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Modelo<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>              
          <div style="height: 300px; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" type="text" OnKeyPress="return mascaraInteiro(event);" maxlength="5" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodGp"
                                                    onBlur="CodGpBlur(this);" 
                                                    onFocus="gpFocus(this);" 
                                                    onClick="gpF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodGp">GRUPO:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtDesGp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesGp">GRUPO_NOME</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="40" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO_MODELO</label>
              </div>
              
              <div class="campotexto campo15">
                  <input class="campo_input inputF10" id="edtCodFbr"
                                                      OnKeyPress="return mascaraInteiro(event);"
                                                      onBlur="codFbrBlur(this);"
                                                      onFocus="fbrFocus(this);"
                                                      onClick="fbrF10Click(this);"
                                                      data-oldvalue="0000"
                                                      autocomplete="off"
                                                      type="text" />
                  <label class="campo_label campo_required" for="edtCodFbr">FABRICANTE</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input" id="edtDesFbr" type="text" />
                <label class="campo_label campo_required" for="edtDesFbr">NOME_FABRICANTE</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo" id="edtEstoque" type="text" maxlength="4" />
                <label class="campo_label" for="edtEstoque">ESTOQUE</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo" id="edtEstoqueMinimo" type="text" maxlength="4" />
                <label class="campo_label" for="edtEstoqueMinimo">ESTOQUE MINIMO</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbNumSerie">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbNumSerie">NUM SERIE:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbSinCard">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbSinCard">SINCARD:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbOperadora">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbOperadora">OPERADORA:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbFone">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbFone">FONE:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbVenda">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbVenda">VENDA:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbLocacao">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbLocacao">LOCACAO:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbContrato">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbContrato">CONTRATO:</label>
              </div>
              <div class="campotexto campo40">
                  <input class="campo_input inputF10" id="edtPrdCodigo"
                                                            type="text" 
                                                            autocomplete="off"
                                                            onClick="prdClick(this,'obrigatorio');"
                                                            readonly
                                                       />
                  <label class="campo_label campo_required" for="edtPrdCodigo">PRODUTO FISCAL</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtValorVista" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtValorVista">VALOR A VISTA:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtValorPrazo" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtValorPrazo">VALOR A PRAZO:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtValorMinimo" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtValorMinimo">VALOR MINIMO:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input input" id="edtFirmWare" type="text" maxlength="5" />
                <label class="campo_label campo_required" for="edtFirmWare">FIRMWARE</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbMensuravel">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbMensuravel">MENSURAVEL</label>
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
                <input id="edtEstoqueSucata" type="text" />
                <input id="edtEstoqueAuto" type="text" />
                <input id="edtEstoqueReal" type="text" />
              </div>
              <div onClick="btnConfirmarClick();" id="btnConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
              <div id="btnCancelar"  class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
            </div>
          </div>
        </form>
      </div>
      <!--Importar excel -->
      <div id="divExcel" class="divTopoExcel">
      </div>
      <!--Fim Importar excel -->
    </div>       
  </body>
</html>