<?php
  session_start();
  if( isset($_POST["automodelo"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      //require("classPhp/removeAcento.class.php"); 
      //require("classPhp/validaCampo.class.php");       

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["automodelo"]);
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
        if( $rotina=="selectGp" ){
          $tblGp="*";
          $tblGm="*";
          $sql="SELECT GP_CODIGO AS CODIGO,GP_NOME AS NOME FROM GRUPOPRODUTO WHERE ((GP_CODIGO<>'AUT') AND (GP_ATIVO='S'))";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] == "OK" ){
            $tblGp=$retCls['dados'];
            
            $sql="SELECT GM_CODIGO AS CODIGO,GM_CODGP AS GRUPO,GM_NOME AS NOME FROM GRUPOMODELO WHERE GM_ATIVO='S' ORDER BY GM_CODGP,GM_NOME";
            $classe->msgSelect(false);
            $retCls=$classe->selectAssoc($sql);
            if( $retCls['retorno'] == "OK" ){
              $tblGm=$retCls['dados'];
            };
          };
          
          if( ($tblGp=="*") or ($tblGm=="*") ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{ "retorno":"OK"
                        ,"tblGp":'.json_encode($tblGp).'
                        ,"tblGm":'.json_encode($tblGm).'                        
                        ,"erro":""}]'; 
          };  
        };
        //////////////////////////////////////////////////
        //       Dados para JavaScript AUTOMODELO       //
        //////////////////////////////////////////////////
        if( $rotina=="selectAm" ){
          $sql="";
          $sql.="SELECT A.GM_CODIGO";
          $sql.="       ,A.GM_NOME";
          $sql.="       ,A.GM_ESTOQUE";
          $sql.="       ,CASE WHEN A.GM_VENDA='S' THEN 'SIM' ELSE 'NAO' END AS GM_VENDA";
          $sql.="       ,CASE WHEN A.GM_LOCACAO='S' THEN 'SIM' ELSE 'NAO' END AS GM_LOCACAO";
          $sql.="       ,A.GM_GPOBRIGATORIO";
          $sql.="       ,A.GM_GMOBRIGATORIO";          
          $sql.="       ,A.GM_GPACEITO";
          $sql.="       ,A.GM_GMACEITO";
          $sql.="       ,A.GM_GPSERIEOBRIGATORIO"; 
          $sql.="       ,A.GM_CODIGOPAIFILHO";         
          $sql.="       ,A.GM_VALORVISTA";
          $sql.="       ,A.GM_VALORPRAZO";
          $sql.="       ,A.GM_VALORMINIMO";          
          $sql.="       ,A.GM_VLRNOSHOW";
          $sql.="       ,A.GM_VLRIMPRODUTIVEL";
          $sql.="       ,A.GM_VLRINSTALA";
          $sql.="       ,A.GM_VLRDESISTALA";
          $sql.="       ,A.GM_VLRREINSTALA";          
          $sql.="       ,A.GM_VLRMANUTENCAO";
          $sql.="       ,A.GM_VLRREVISAO";
          $sql.="       ,CASE WHEN A.GM_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS GM_ATIVO";
          $sql.="       ,CASE WHEN A.GM_REG='P' THEN 'PUB' WHEN A.GM_REG='S' THEN 'SIS' ELSE 'ADM' END AS GM_REG";
          $sql.="       ,US.US_APELIDO";
          $sql.="       ,A.GM_CODUSR";
          $sql.="       ,A.GM_CODGP";          
          $sql.="  FROM GRUPOMODELO A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.GM_CODUSR=US.US_CODIGO";
          $sql.="  WHERE ((GM_CODGP='AUT') AND ((GM_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
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
    <script src="tabelaTrac/f10/tabelaGrupoModeloF10.js"></script>
    <script src="tabelaTrac/f10/tabelaGrupoProdutoF10.js"></script>
    <script src="js/jsBiblioteca.js"></script>        
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        buscarGp();
        ////////////////////////////////////////////////
        //      Objeto clsTable2017 AUTOMODELO       //
        ////////////////////////////////////////////////
        jsAm={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"GM_CODIGO" 
                      ,"fieldType"      : "int"            
                      ,"autoIncremento" : "S"                      
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "12"
                      ,"align"          : "center"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "GM_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"newRecord"      : ["","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "70"
                      ,"digitosMinMax"  : [3,60]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|(|)| "
                      ,"ajudaCampo"     : ["Nome da modelo com até 50 caracteres."]
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "GM_ESTOQUE" 
                      ,"fieldType"      : "int"            
                      ,"labelCol"       : "ESTOQUE"
                      ,"obj"            : "edtEstoque"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"align"          : "center"
                     ,"newRecord"       : ["0000","this","this"]  
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorIgualZero"]
                      ,"inputDisabled"  : true
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "GM_VENDA"  
                      ,"labelCol"       : "VENDA" 
                      ,"obj"            : "cbVenda"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "12"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      ,"funcCor"        : "(objCell.innerHTML=='NAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro se o produto pode ser vendido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}      
            ,{"id":5  ,"field"          : "GM_LOCACAO"  
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
            ,{"id":6  ,"field"          : "GM_GPOBRIGATORIO"   
                      ,"labelCol"       : "OBRIGATORIO"
                      ,"obj"            : "edtGpObrigatorio"
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,999]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|_|-|0|1|2|3|4|5|6|7|8|9"
                      ,"ajudaCampo"     : ["Grupos obrigatorios com até 40 caracteres."]
                      ,"truncate"       : true                          
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "GM_GMOBRIGATORIO"   
                      ,"labelCol"       : "OBR_MODELO"
                      ,"obj"            : "edtGmObrigatorio"
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "25em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [1,999]
                      ,"digitosValidos" : "N|S|A|0|1|2|3|4|5|6|7|8|9|_"
                      ,"ajudaCampo"     : ["Modelos aceitos para grupos com até 70 caracteres."]
                      ,"padrao":0}   
            ,{"id":8  ,"field"          : "GM_GPACEITO"   
                      ,"labelCol"       : "ACEITO"
                      ,"obj"            : "edtGpAceito"
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [3,999]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|_|-|0|1|2|3|4|5|6|7|8|9"
                      ,"ajudaCampo"     : ["Grupos aceitos com até 20 caracteres."]
                      ,"truncate"       : true                          
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "GM_GMACEITO"   
                      ,"labelCol"       : "ACT_MODELO"
                      ,"obj"            : "edtGmAceito"
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "25em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [1,999]
                      ,"digitosValidos" : "N|S|A|0|1|2|3|4|5|6|7|8|9|_"
                      ,"ajudaCampo"     : ["Modelos aceitos para grupos com até 70 caracteres."]
                      ,"padrao":0}
           ,{"id":10  ,"field"          : "GM_GPSERIEOBRIGATORIO"   
                      ,"labelCol"       : "GPSERIEOBRIGATORIO"
                      ,"obj"            : "edtGpSerieObrig"
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [1,999]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|_"
                      ,"ajudaCampo"     : ["Modelo série obrigatorio."]
                      ,"padrao":0}
           ,{"id":11  ,"field"          : "GM_CODIGOPAIFILHO"   
                      ,"labelCol"       : "CODPAIFILHO"
                      ,"obj"            : "edtCodPaiFilho"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [1,4]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"ajudaCampo"     : ["Codigo Pai do auto."]
                      ,"padrao":0}
            ,{"id":12 ,"field"          : "GM_VALORVISTA"  
                      ,"labelCol"       : "VLRVISTA" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtValorVista"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "16"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":13 ,"field"          : "GM_VALORPRAZO"  
                      ,"labelCol"       : "VLRPRAZO" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtValorPrazo"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "16"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":14 ,"field"          : "GM_VALORMINIMO"  
                      ,"labelCol"       : "VLRMINIMO" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtValorMinimo"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "16"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}     
            ,{"id":15 ,"field"          : "GM_VLRNOSHOW"                        
                      ,"labelCol"       : "VLRNOSHOW"             
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrNoShow"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":16 ,"field"          : "GM_VLRIMPRODUTIVEL"
                      ,"labelCol"       : "VLRIMPROD"             
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrImprodutivel"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":17 ,"field"          : "GM_VLRINSTALA"
                      ,"labelCol"       : "VLRINSTALA"             
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrInstala"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":18 ,"field"          : "GM_VLRDESISTALA"
                      ,"labelCol"       : "VLRDESISTALA"             
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrDesistala"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":19 ,"field"          : "GM_VLRREINSTALA"
                      ,"labelCol"       : "VLRREINSTALA"             
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrReinstala"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":20 ,"field"          : "GM_VLRMANUTENCAO"
                      ,"labelCol"       : "VLRMANUT"             
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrManutencao"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":21 ,"field"          : "GM_VLRREVISAO"
                      ,"labelCol"       : "VLRREVISAO"             
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtVlrRevisao"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>=0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":22 ,"field"          : "GM_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"padrao":2}                                        
            ,{"id":23 ,"field"          : "GM_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":24 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"tamImp"         : "0"                                            
                      ,"obj"            : "edtUsuario" 
                      ,"padrao":4}                
            ,{"id":25 ,"field"          : "GM_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":26 ,"field"          : "GM_CODGP"   
                      ,"labelCol"       : "GP"
                      ,"obj"            : "edtCodGp"
                      ,"newRecord"      : ["AUT","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":27 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP"        
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objAm.espiao();"
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
            ,{"texto":"Novo auto"       ,"name":"horNovAut"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-shopping-cart"     }
            ,{"texto":"Lote cadastrado" ,"name":"horLote"       ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-clone"             }            
            ,{"texto":"Ver individual"  ,"name":"horIndividual" ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-eye-slash"         }            
            ,{"texto":"Serviço"         ,"name":"horRelServico" ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-plus"              
                                        ,"popover":{title:"Serviços",texto:"Adiciona serviço para cobrança quando da locação/venda do auto"}}             
            ,{"texto":"Fechar"          ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"             }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)                        
          //,"idBtnConfirmar" : "btnConfirmar"          // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"             // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmAm"                   // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaAm"                // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmAm"                   // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblAm"                   // Nome da table
          ,"prefixo"        : "Am"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VGRUPOMODELO"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPGRUPOMODELO"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "GM_ATIVO"                // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "GM_REG"                  // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "GM_CODUSR"               // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"             // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "105em"                   // Tamanho da table
          ,"height"         : "58em"                    // Altura da table
          ,"tableLeft"      : "sim"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "MODELO"                  // Titulo do relatório
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"foco"           : ["edtDescricao"
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
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objAm.AjudaSisAtivo(jsAm);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objAm.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objAm.espiao();"]
                               ,["Imprimir registros em tela"             ,"fa-print"         ,"objAm.imprimir()"]
                               ,["Gerar excel"                            ,"fa-file-excel-o"  ,"objAm.excel();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objAm.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objAm.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objAm.altRegSistema("+jsPub[0].usr_d31+");"] 
                               ,["Número de registros em tela"            ,"fa-info"          ,"objAm.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "AUTOMODELO[35]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objAm === undefined ){  
          objAm=new clsTable2017("objAm");
        };  
        objAm.montarHtmlCE2017(jsAm); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsAm,["ACEITO","ACT_MODELO","CODIGO","DESCRICAO","ESTOQUE","GPSERIEOBRIGATORIO","OBRIGATORIO","OBR_MODELO","USUARIO"]);
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsAm.form).style.width=jsAm.width;
        btnFiltrarClick("S");  
      });
      //
      var objAm;                      // Obrigatório para instanciar o JS TFormaCob
      var jsAm;                       // Obj principal da classe clsTable2017
      var chkds;                      // Guarda todos registros checados na table 
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var objGmF10;                   // Obrigatório para instanciar o JS GrupoModeloF10
      var objGpF10                    // Obrigatório para instanciar o JS GrupoProdutoF10
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var tblGp                       // Tabela GRUPOPRODUTO
      var tblGm                       // Tabela GRUPOMODELO
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario            
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d35);
      /////////////////////////////////////////////////////////////
      // Buscando os grupos uma unica vez para cadastro e alteracao
      /////////////////////////////////////////////////////////////
      function buscarGp(){
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "selectGp"          );
        clsJs.add("login"   , jsPub[0].usr_login  );
        fd = new FormData();
        fd.append("automodelo" , clsJs.fim());
        msg     = requestPedido("Trac_AutoModelo.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          tblGp=retPhp[0]["tblGp"];  
          tblGm=retPhp[0]["tblGm"];  
        };  
      };  
      ////////////////////////////
      // Filtrando os registros //
      ////////////////////////////
      function btnFiltrarClick(atv) { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "selectAm"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        fd = new FormData();
        fd.append("automodelo" , clsJs.fim());
        msg     = requestPedido("Trac_AutoModelo.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsAm.registros=objAm.addIdUnico(retPhp[0]["dados"]);
          objAm.ordenaJSon(jsAm.indiceTable,false);  
          objAm.montarBody2017();
        };  
      };
      ///////////////////////////////
      // Funcao para buscar os grupos
      ///////////////////////////////
      function gpClick(el,lbl){
        let clsCode = new concatStr();  
        clsCode.concat("<div id='dPaiGpChk' class='divContainerTable' style='height: 31.2em; width: 41em;border:none'>");
        clsCode.concat("<table id='tblGpChk' class='fpTable' style='width:100%;'>");
        clsCode.concat("  <thead class='fpThead'>");
        clsCode.concat("    <tr>");
        clsCode.concat("      <th class='fpTh' style='width:15%'>CODIGO</th>");
        clsCode.concat("      <th class='fpTh' style='width:60%'>DESCRICAO</th>");
        clsCode.concat("      <th class='fpTh' style='width:15%'>SIM</th>");
        clsCode.concat('      <th class="fpTh" style="width:10%">Quantidade</th>');          
        clsCode.concat("    </tr>");
        clsCode.concat("  </thead>");
        clsCode.concat("  <tbody id='tbody_tblChk'>");
        //////////////////////
        // Preenchendo a table
        //////////////////////  
        let arr=[];
        tblGp.forEach(function(reg){
          arr.push({cod:reg.CODIGO,des:reg.NOME ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
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
          clsCode.concat("      <td class='fpTd textoCentro'>");
          clsCode.concat("        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheckGp((elTr.rowIndex-1));'>");
          clsCode.concat("          <i id='img"+reg.cod+"' data-value='"+reg.sn+"' class='"+reg.fa+"' style='margin-left:10px;font-size:1.5em;color:"+reg.cor+";'></i>");
          clsCode.concat("        </div>");
          clsCode.concat("      </td>");
          clsCode.concat("      <td class='fpTd'><input type='text'style='max-width:50px' data-newrecord='1'></input></td>");
          clsCode.concat("    </tr>");
        });
        //////  
        // Fim
        //////
        clsCode.concat("  </tbody>");        
        clsCode.concat("</table>");
        clsCode.concat("</div>"); 
        clsCode.concat("<div id='btnGpConfirmar' onClick='fncJanelaGpRet(\""+el.id+"\");' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'> Ok</i></div>");        
        janelaDialogo(
          { height          : "42em"
            ,body           : "16em"
            ,left           : "300px"
            ,top            : "60px"
            ,tituloBarra    : "Selecione grupo "+lbl
            ,code           : clsCode.fim()
            ,width          : "43em"
            ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
          }
        );  
      };
      ///////////////////////////////////////////
      // Marcando e desmarcando os itens da table
      ///////////////////////////////////////////
      function fncCheckGp(pLin){
        let tbl   = tblGpChk.getElementsByTagName("tbody")[0];
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
      function fncJanelaGpRet(obj){
        try{              
          let tbl = tblGpChk.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          let elImg;
          if( nl>0 ){
            let filtroGp="";
            let filtroGpQtd="";
            for(let lin=0 ; (lin<nl) ; lin++){
              elImg="img"+tbl.rows[lin].cells[0].innerHTML;
              
              if( document.getElementById(elImg).getAttribute("data-value") == "S" ){
                filtroGp+=( filtroGp=="" ? tbl.rows[lin].cells[0].innerHTML: "_".concat(tbl.rows[lin].cells[0].innerHTML) );
                filtroGpQtd+=( filtroGp=="" ? tbl.rows[lin].cells[3].children[0].value: "_".concat(tbl.rows[lin].cells[3].children[0].value) );
              };
            };
            if( filtroGp=="" )
              filtroGp="NSA";  
            document.getElementById(obj).value=filtroGp;
            document.getElementById('edtGpObrigatorioQtd').value=filtroGpQtd;

            janelaFechar();
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /////////////////////////////////
      // Funcao para buscar os modelos
      ////////////////////////////////
      function gmClick(el,lbl,contido){
        let clsCode = new concatStr();  
        clsCode.concat("<div id='dPaiGmChk' class='divContainerTable' style='height: 31.2em; width: 61em;border:none'>");
        clsCode.concat("<table id='tblGmChk' class='fpTable' style='width:100%;'>");
        clsCode.concat("  <thead class='fpThead'>");
        clsCode.concat("    <tr>");
        clsCode.concat("      <th class='fpTh' style='width:10%'>CODIGO</th>");
        clsCode.concat("      <th class='fpTh' style='width:10%'>GRP</th>");        
        clsCode.concat("      <th class='fpTh' style='width:60%'>DESCRICAO</th>");
        clsCode.concat("      <th class='fpTh' style='width:20%'>SIM</th>");        
        clsCode.concat("    </tr>");
        clsCode.concat("  </thead>");
        clsCode.concat("  <tbody id='tbody_tblChk'>");
        //////////////////////
        // Preenchendo a table
        //////////////////////
        let cnt=contido.split("_");  
        let arr=[];
        tblGm.forEach(function(reg){
          for( let lin=0;lin<cnt.length;lin++ ){ 
            if( cnt[lin]==reg.GRUPO ){
              arr.push({cod:reg.CODIGO
                        ,gr:reg.GRUPO
                        ,des:reg.NOME 
                        ,sn:"N" 
                        ,fa:"fa fa-thumbs-o-down" 
                        ,cor:"red"});
              break;          
            };
          };    
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
          clsCode.concat("      <td class='fpTd'>"+reg.gr+"</td>");          
          clsCode.concat("      <td class='fpTd'>"+reg.des+"</td>");
          clsCode.concat("      <td class='fpTd textoCentro'>");
          clsCode.concat("        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheckGm((elTr.rowIndex-1));'>");
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
        clsCode.concat("<div id='btnGmConfirmar' onClick='fncJanelaGmRet(\""+el.id+"\");' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'> Ok</i></div>");        
        janelaDialogo(
          { height          : "42em"
            ,body           : "16em"
            ,left           : "200px"
            ,top            : "60px"
            ,tituloBarra    : "Selecione modelo(s) para grupo "+lbl
            ,code           : clsCode.fim()
            ,width          : "63em"
            ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
          }
        );  
      };
      ///////////////////////////////////////////
      // Marcando e desmarcando os itens da table
      ///////////////////////////////////////////
      function fncCheckGm(pLin){
        let tbl   = tblGmChk.getElementsByTagName("tbody")[0];
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
      function fncJanelaGmRet(obj){
        try{              
          let tbl = tblGmChk.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          let elImg;
          if( nl>0 ){
            let filtroGm="";
            for(let lin=0 ; (lin<nl) ; lin++){
              elImg="img"+tbl.rows[lin].cells[0].innerHTML;
              
              if( document.getElementById(elImg).getAttribute("data-value") == "S" ){
                filtroGm+=( filtroGm=="" ? tbl.rows[lin].cells[0].innerHTML  : "_".concat(tbl.rows[lin].cells[0].innerHTML) );
              };
            };
            if( filtroGm=="" )
              filtroGp="NSA";  
            document.getElementById(obj).value=filtroGm;
            janelaFechar();
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      function horNovAutClick(){
        if( parseInt(jsPub[0].usr_d36) !=4  ){
          gerarMensagemErro("dir","USUARIO NÃO POSSUI DIREITO 36 PARA ESTA ROTINA!",{cabec:"Aviso"});  
        } else {
          try{
            chkds=objAm.gerarJson("1").gerar();
            chkds.forEach(function(reg){
              if( reg.ATIVO != "SIM" )
                throw "Modelo inativo para lancto!"; 
            });            
            let objEnvio;          
            clsJs=jsString("lote");
            chkds.forEach(function(reg){
              clsJs.add("codam"         , reg.CODIGO      );
              clsJs.add("desam"         , reg.DESCRICAO   );
              clsJs.add("gpobrigatorio" , reg.OBRIGATORIO );                
              clsJs.add("gmobrigatorio" , reg.OBR_MODELO  );                                
              clsJs.add("gpaceito"      , reg.ACEITO      );
              clsJs.add("gmaceito"      , reg.ACT_MODELO  );       
              clsJs.add("gmgpsrobr"     , reg.GPSERIEOBRIGATORIO);          
              clsJs.add("colCodigo"     , objCol.CODIGO   );
              clsJs.add("colEstoque"    , objCol.ESTOQUE  );
              clsJs.add("colUsuario"    , objCol.USUARIO  );
              ///////////////////////////////////////////////////////////////////////////
              // Enviando as colunas deste formulario para Trac_AutoModeloCad.php
              ///////////////////////////////////////////////////////////////////////////
              //for(let key in objCol) { 
              //  clsJs.add(key, objCol[key] );          
              //};            
            });  
            /////////////////////////////////////////////////////////////////
            // Passando as colunas que vou precisas para atualizar esta table
            /////////////////////////////////////////////////////////////////
            objEnvio=clsJs.fim();          
            localStorage.setItem("addAlt",objEnvio);
            window.open("Trac_AutoModeloCad.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});
          };
        };  
      };
      ///////////////////
      // Lotes importados
      ///////////////////
      function horLoteClick(){
        if( intCodDir != 4 ){
          gerarMensagemErro("am","USUARIO NÃO POSSUI FLAG DE DIREITO 4 PARA ESTA ROTINA",{cabec:"Erro"});            
        } else {
          try{
            let objEnvio;          
            clsJs=jsString("lote");
            clsJs.add("colCodigo"   , objCol.CODIGO       );  // Vou precisar desta coluna para atualizar a grade a partir de Trac_AutoModeloCad.php
            clsJs.add("colEstoque"  , objCol.ESTOQUE      );  // Vou precisar desta coluna para atualizar a grade a partir de Trac_AutoModeloCad.php
            objEnvio=clsJs.fim();
            localStorage.setItem("addLote",objEnvio);
            window.open("Trac_AutoModeloLot.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});  
          };
        };
      };
      function horIndividualClick(){
        if( intCodDir != 4 ){
          gerarMensagemErro("Am","USUARIO NÃO POSSUI FLAG DE DIREITO 4 PARA ESTA ROTINA",{cabec:"Erro"});            
        } else {
          try{
            chkds=objAm.gerarJson("1").gerar();            
            let objEnvio;          
            clsJs=jsString("lote");
            clsJs.add("codam"      , chkds[0].CODIGO );
            objEnvio=clsJs.fim();  
            localStorage.setItem("addInd",objEnvio);
            window.open("Trac_AutoModeloInd.php");
          }catch(e){
            gerarMensagemErro("catch",e,{cabec:"Erro"});  
          };
        };  
      };
      /////////////////////////////
      // Trigger olha esta regra //
      /////////////////////////////
      function btnConfirmarClick(){
        let junta       = "";
        let splt        = [];
        let duplicidade = [];
        if( document.getElementById("edtGpObrigatorio").value != "NSA" ){
          junta+=document.getElementById("edtGpObrigatorio").value+"_";
        };
        if( document.getElementById("edtGpAceito").value != "NSA" ){
          junta+=document.getElementById("edtGpAceito").value+"_";          
        };
        junta=junta.slice(0,-1);  
        splt=junta.split("_");
        msg="ok";
        
        splt.forEach(function(reg){
          if( duplicidade.indexOf(reg) != -1 ){
            msg="GRUPO "+reg+" DUPLICADO!";  
          } else {
            duplicidade.push(reg);  
          };
        });

        if( jsNmrs("edtValorVista").dolar().ret() > jsNmrs("edtValorPrazo").dolar().ret() ){
        //if( $("edtValorVista").getDolar() > $("edtValorPrazo").getDolar() ){
          msg="VALOR A VISTA NAO PODE SER MAIOR QUE VALOR A PRAZO!";
        };
        
        if( msg != "ok" ){
          gerarMensagemErro("am",msg,{cabec:"Aviso"});              
        } else {  
          objAm.gravar(true);
        };
      };
      /////////////////////////////////////////
      // Servico(s) relacionados para cada auto    
      /////////////////////////////////////////
      function horRelServicoClick(){
        try{
          chkds=objAm.gerarJson("1").gerar();
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("codam"      , chkds[0].CODIGO );
          clsJs.add("desam"      , chkds[0].DESCRICAO );
          objEnvio=clsJs.fim();  
          
          localStorage.setItem("frameComplementar",objEnvio);
          window.parent.document.getElementById("iframeCorpo").style.display="none";
          window.open("Trac_GrupoModeloServico.php","iframeComplementar");                
          window.parent.document.getElementById("iframeComplementar").style.display="block";
          
          
          
          //localStorage.setItem("addInd",objEnvio);
          //window.open("Trac_GrupoModeloServico.php");
          
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});  
        };
      };
      ///////////////////////////////
      //  AJUDA PARA GRUPOMODELO   //
      ///////////////////////////////
      function gmFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function gmF10Click(obj){ 
        let whr;
        whr = " {WHERE} (A.GM_CODGP='AUT') "
        whr+= " {AND} (A.GM_CODIGOPAIFILHO=0)"     
        fGrupoModeloF10(0,obj.id,"edtCodPaiFilho",100
          ,{tamColNome:"29.5em"
            ,ativo:"S"
            ,where:whr 
           } 
        ); 
       };
      function RetF10tblGm(arr){
        document.getElementById("edtCodPaiFilho").value  = jsNmrs(arr[0].CODIGO).emZero(4).ret();
        document.getElementById("edtCodPaiFilho").setAttribute("data-oldvalue",jsNmrs(arr[0].CODIGO).emZero(4).ret());
        
      };
      function codGmBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fGrupoModeloF10(1,obj.id,"edtUnidades",100,
            {codfvr  : elNew
             ,ativo  : "S"} 
            ); 
          document.getElementById(obj.id).value  = ( ret.length == 0 ? "0000"  : jsNmrs(ret[0].CODIGO).emZero(4).ret() );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO ) );
        };
      };

      ///////////////////////////////
      //  AJUDA PARA GRUPOPRODUTO   //
      ///////////////////////////////
      function gpFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function gpF10Click(obj){ 
        fGrupoProdutoF10(0,obj.id,"edtGpSerieObrig",100
          ,{tamColNome:"29.5em"
            ,ativo:"S"
           } 
        ); 
      };
      function RetF10tblGp(arr){
        document.getElementById("edtGpSerieObrig").value  = arr[0].CODIGO;
        document.getElementById("edtGpSerieObrig").setAttribute("data-oldvalue",arr[0].CODIGO);
        
      };
      function codGpBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = document.getElementById(obj.id).value;
        if( elOld != elNew ){
          var arr = fGrupoProdutoF10(1,obj.id,"edtGpSerieObrig",100,
            {codfvr  : elNew
             ,ativo  : "S"} 
            ); 
          document.getElementById(obj.id).value  = ( arr.length == '' ? "NSA"  : arr[0].CODIGO);
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == '' ? "NSA" : arr[0].CODIGO ) );
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
              name="frmAm" 
              id="frmAm" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Auto<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>              
          <div style="height: 290px; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" type="text" OnKeyPress="return mascaraInteiro(event);" maxlength="5" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo60">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="60" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO_MODELO</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input_titulo" id="edtEstoque" type="text" maxlength="4" disabled />
                <label class="campo_label" for="edtEstoque">ESTOQUE</label>
              </div>
              <div class="campotexto campo10">
                <select class="campo_input_combo" id="cbVenda">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label" for="cbVenda">VENDA:</label>
              </div>
              <div class="campotexto campo10">
                <select class="campo_input_combo" id="cbLocacao">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label" for="cbLocacao">LOCACAO:</label>
              </div>
              <!-- 
                Gp=GrupoProduto 
                Gm=GrupoModelo
              -->
              <div class="campotexto campo30">
                <input class="campo_input_titulo inputF10"  id="edtGpObrigatorio" 
                                                            type="text" 
                                                            autocomplete="off"
                                                            onClick="gpClick(this,'obrigatorio');"
                                                            readonly
                                                             />
                <label class="campo_label campo_required" for="edtGpObrigatorio">GRUPOs-Obrigatório</label>
              </div>
              <div class="campotexto campo70">
                <input class="campo_input_titulo inputF10"  id="edtGmObrigatorio" 
                                                            type="text" 
                                                            autocomplete="off"
                                                            onClick="gmClick(this,'obrigatorio',document.getElementById('edtGpObrigatorio').value);"
                                                            readonly
                                                            />
                <label class="campo_label campo_required" for="edtGmObrigatorio">MODELOs-para obrigatorio</label>
              </div>
              <!-- 
                Gp=GrupoProduto 
                Gm=GrupoModelo
              -->
              <div class="campotexto campo30">
                <input class="campo_input_titulo inputF10"  id="edtGpAceito" 
                                                            type="text" 
                                                            autocomplete="off"
                                                            onClick="gpClick(this,'aceito');"
                                                            readonly
                                                             />
                <label class="campo_label campo_required" for="edtGpAceito">GRUPOs-Aceito</label>
              </div>
              <div class="campotexto campo70">
                <input class="campo_input_titulo inputF10"  id="edtGmAceito" 
                                                            type="text" 
                                                            autocomplete="off"
                                                            onClick="gmClick(this,'aceito',document.getElementById('edtGpAceito').value);"
                                                            readonly
                                                            />
                <label class="campo_label campo_required" for="edtGmAceito">MODELOs-para aceito</label>
              </div>
              <!-- -->
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtGpSerieObrig"
                                                    onBlur="codGpBlur(this);" 
                                                    onFocus="gpFocus(this);" 
                                                    onClick="gpF10Click(this);" 
                                                    data-oldvalue=""
                                                    data-newrecord="NSA"
                                                    autocomplete="off"
                                                    maxlength="4"
                                                    type="text" />       
                <label class="campo_label" for="edtGpSerieObrig">Serie do auto:</label>
              </div>
              <div class="campotexto campo10">
               <input class="campo_input inputF10" id="edtCodPaiFilho"
                                                    onBlur="codGmBlur(this);" 
                                                    onFocus="gmFocus(this);" 
                                                    onClick="gmF10Click(this);"
                                                    data-oldvalue="0000"
                                                    data-newrecord="0000"
                                                    autocomplete="off"
                                                    maxlength="6"
                                                    type="text" />       
                <label class="campo_label" for="edtCodPaiFilho">Codigo Pai:</label>
              </div>
              <!-- -->
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtValorVista" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtValorVista">VALOR A VISTA:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtValorPrazo" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtValorPrazo">VALOR A PRAZO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtValorMinimo" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtValorMinimo">VALOR MINIMO:</label>
              </div>
              <!-- -->
              
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtVlrNoShow" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtVlrNoShow">VLR NOSHOW:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtVlrImprodutivel" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtVlrImprodutivel">VLR IMPRODUTIVEL:</label>
              </div>
              <div class="campotexto campo20">
                <input class="campo_input edtDireita" id="edtVlrInstala" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtVlrInstala">VLR INSTALAÇÂO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtVlrDesistala" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtVlrDesistala">VLR DESISTALAÇÃO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtVlrReinstala" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtVlrReinstala">VLR REINSTALAÇÃO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtVlrManutencao" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtVlrManutencao">VLR MANUTENÇÃO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input edtDireita" id="edtVlrRevisao" 
                                                      type="text" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      maxlength="15" />
                <label class="campo_label campo_required" for="edtVlrRevisao">VLR REVISÃO:</label>
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
              <div class="campotexto campo15">
                <input class="campo_input_titulo" disabled id="edtUsuario" type="text" />
                <label class="campo_label campo_required" for="edtUsuario">USUARIO</label>
              </div>
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
                <input id="edtCodGp" type="text" />
              </div>
              <div id="btnConfirmar" onClick="btnConfirmarClick();" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
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