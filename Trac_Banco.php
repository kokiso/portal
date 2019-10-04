<?php
  session_start();
  if( isset($_POST["banco"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");
      require("classPhp/selectRepetido.class.php");      
      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["banco"]);
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
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        ///////////////////////
        // Alterando  a empresa
        ///////////////////////
        if( $lote[0]->rotina=="altEmpresa" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("altEmpresa",$lote[0]->login);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"script":'.$retSql["script"].',"erro":"'.$retSql["erro"].'"}]';
        };  
        /////////////////////////////////////////////
        //    Dados para JavaScript BANCO          //
        /////////////////////////////////////////////
        if( $lote[0]->rotina=="selectBnc" ){
          $sql ="SELECT A.BNC_CODIGO";
          $sql.="       ,A.BNC_NOME";
          $sql.="       ,A.BNC_SALDO";
          $sql.="       ,A.BNC_CODFVR";
          $sql.="       ,FVR.FVR_APELIDO";
          $sql.="       ,CASE WHEN A.BNC_ENTRAFLUXO='S' THEN 'SIM' ELSE 'NAO' END AS BNC_ENTRAFLUXO";
          $sql.="       ,A.BNC_CODBST";
          $sql.="       ,B.BST_NOME";
          $sql.="       ,CASE WHEN A.BNC_PADRAOFLUXO='S' THEN 'SIM' ELSE 'NAO' END AS BNC_PADRAOFLUXO";
          $sql.="       ,A.BNC_CODBCD";
          $sql.="       ,D.BCD_NOME";
          $sql.="       ,A.BNC_AGENCIA";
          $sql.="       ,A.BNC_AGENCIADV";
          $sql.="       ,A.BNC_CONTA";
          $sql.="       ,A.BNC_CONTADV";
          $sql.="       ,CASE WHEN A.BNC_CNAB='S' THEN 'SIM' ELSE 'NAO' END AS BNC_CNAB";          
          $sql.="       ,A.BNC_CODEMP";
          $sql.="       ,CASE WHEN A.BNC_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS BNC_ATIVO";
          $sql.="       ,CASE WHEN A.BNC_REG='P' THEN 'PUB' WHEN A.BNC_REG='S' THEN 'SIS' ELSE 'ADM' END AS BNC_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.BNC_CODUSR";
          $sql.="       ,E.EMP_APELIDO";
          $sql.="       ,FVR.FVR_NOME";          
          $sql.="  FROM BANCO A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.BNC_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.BNC_CODFVR=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN BANCOSTATUS B ON A.BNC_CODBST=B.BST_CODIGO";
          $sql.="  LEFT OUTER JOIN BANCOCODIGO D ON A.BNC_CODBCD=D.BCD_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA E ON A.BNC_CODEMP=E.EMP_CODIGO";
          $sql.="  WHERE ((BNC_CODEMP=".$lote[0]->codemp.")";
          $sql.="    AND ((BNC_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
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
        if( $lote[0]->rotina=="impExcel" ){
          ////////////////////////////////////////////////////////////////////////
          // Enviando para a class todas as colunas com as checagens necessaria //
          // Nome da tabela e numeros de erros(se existir)                      //
          // Modelo do JSON estah na classe                                     //
          ////////////////////////////////////////////////////////////////////////
          $matriz   = (array)$lote[0]->titulo;
          $vldCampo = new validaCampo("VBANCO",0);
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
    <title>Banco</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
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
        //////////////////////////////////////////
        //   Objeto clsTable2017 BANCO          //
        //////////////////////////////////////////
        jsBnc={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"BNC_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"digitosMinMax"  : [1,6] 
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do banco. Este campo é único e deve tem o formato 999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S" 
                      ,"autoIncremento" : "S"                      
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "BNC_NOME"   
                      ,"labelCol"       : "DESCRICAO"
                      ,"obj"            : "edtDescricao"
                      ,"tamGrd"         : "31em"
                      ,"tamImp"         : "75"
                      ,"digitosMinMax"  : [3,40]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Descrição do Banco."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "BNC_SALDO"
                      ,"obj"            : "edtSaldo"
                      ,"labelCol"       : "VALOR"
                      ,"insUpDel"       : ["N","N","N"]            
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "BNC_CODFVR" 
                      ,"labelCol"       : "CODFVR"
                      ,"obj"            : "edtCodFvr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"formato"        : ["i4"]                      
                      ,"fieldType"      : "int"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [  "Codigo do Favorecido. Registro deve existir na tabela Favorecido e tem o formato 999"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "FVR_APELIDO"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "FAVORECIDO"
                      ,"obj"            : "edtApelido"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,15]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição de FAVORECIDO para este banco."]
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "BNC_ENTRAFLUXO"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "FLUXO"
                      ,"obj"            : "cbEntraFluxo"
                      ,"tamGrd"         : "3em"
                      ,"tipo"           : "cb"
                      ,"tamImp"         : "11"
                      ,"newRecord"      : ["S","this","this"]
                      ,"digitosMinMax"  : [1,1]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "BNC_CODBST" 
                      ,"labelCol"       : "STATUS"
                      ,"obj"            : "edtCodBst"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "12"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [  "Codigo do BancoStatus. Registro deve existir na tabela BancoStatus e tem o formato AAA"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "BST_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "BANCOSTATUS"
                      ,"obj"            : "edtDesBst"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,20]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição da BANCOSTATUS para este banco."]
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "BNC_PADRAOFLUXO"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "PADRAO"
                      ,"obj"            : "cbPadraoFluxo"
                      ,"tamGrd"         : "6em"
                      ,"tipo"           : "cb"
                      ,"tamImp"         : "13"
                      ,"newRecord"      : ["S","this","this"]
                      ,"digitosMinMax"  : [1,1]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "BNC_CODBCD" 
                      ,"labelCol"       : "COD"
                      ,"obj"            : "edtCodBcd"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "13"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,3]
                      ,"ajudaCampo"     : [  "Codigo do BancoCodigo. Registro deve existir na tabela BancoCodigo e tem o formato AAA"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":11 ,"field"          : "BCD_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "BANCOCODIGO"
                      ,"obj"            : "edtDesBcd"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,30]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição da BancoCodigo para este banco."]
                      ,"padrao":0}
            ,{"id":12 ,"field"          : "BNC_AGENCIA"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "AGENCIA"
                      ,"obj"            : "edtAgencia"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "14"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,8]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":13 ,"field"          : "BNC_AGENCIADV"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "DV"
                      ,"obj"            : "edtAgenciaDiv"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "6"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,1]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":14 ,"field"          : "BNC_CONTA"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "CONTA"
                      ,"obj"            : "edtConta"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "12"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"digitosMinMax"  : [1,20]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| "
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "BNC_CONTADV"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "DV"
                      ,"obj"            : "edtContaDiv"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "6"
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,1]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
                      
            ,{"id":16 ,"field"          : "BNC_CNAB"   
                      ,"insUpDel"       : ["S","S","N"]
                      ,"labelCol"       : "CNAB"
                      ,"obj"            : "cbCnab"
                      ,"tamGrd"         : "3em"
                      ,"tipo"           : "cb"
                      ,"tamImp"         : "11"
                      ,"newRecord"      : ["N","this","this"]
                      ,"digitosMinMax"  : [1,1]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : [ "Direito para opção..."]
                      ,"padrao":0}
            ,{"id":17 ,"field"          : "BNC_CODEMP" 
                      ,"labelCol"       : "CODEMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"padrao":7}  
            ,{"id":18 ,"field"          : "BNC_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":19 ,"field"          : "BNC_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":20 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":21 ,"field"          : "BNC_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}   
            ,{"id":22 ,"field"          : "EMP_APELIDO"   
                      ,"labelCol"       : "EMPRESA"
                      ,"obj"            : "edtDesEmp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [jsPub[0].emp_apelido,"this","this"]
                      ,"ajudaCampo"     : ["Nome da empresa."]
                      ,"padrao":0}
            ,{"id":23 ,"field"          : "FVR_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "DESFVR"
                      ,"obj"            : "edtDesFvr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,60]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição de FAVORECIDO para esta banco."]
                      ,"padrao":0}
            ,{"id":24 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objBnc.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"BANCO - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar"     ,"name":"horCadastrar"      ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"             }
            ,{"texto":"Alterar"       ,"name":"horAlterar"        ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"  }
            ,{"texto":"Excluir"       ,"name":"horExcluir"        ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"            }
            ,{"texto":"Tarifa"        ,"name":"horTarifa"         ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-money"            }            
            ,{"texto":"Transferencia" ,"name":"horTransferencia"  ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-sliders"          }                        
            ,{"texto":"Extrato"       ,"name":"horExtrato"        ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-sliders"          }
            ,{"texto":"Empresa" 			,"name":"horEmpresa"        ,"onClick":"7"  ,"enabled":true	,"imagem":"fa fa-spinner"          
                                        ,"popover":{title:"Ajuda",texto:"Opção para alterar empresa"}												         } 						
            ,{"texto":"Imprimir"      ,"name":"horImprimir"       ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"            }                        
            ,{"texto":"Fechar"        ,"name":"horFechar"         ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"            }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar    
          ,"popover"        : true                  // Opção para gerar ajuda no formato popUp(Hint)              										          
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmBnc"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaBnc"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmBnc"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblBnc"              // Nome da table
          ,"prefixo"        : "bnc"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VBANCO"              // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPBANCO"            // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "BNC_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "BNC_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "BNC_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "108em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "BANCO"               // Titulo do relatório
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objBnc.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objBnc.AjudaSisAtivo(jsBnc);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objBnc.detalhe();"]
                               ,["Gerar excel"                            ,"fa-file-excel-o"  ,"objBnc.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objBnc.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objBnc.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objBnc.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objBnc.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objBnc.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "BANCO[06]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objBnc === undefined ){  
          objBnc=new clsTable2017("objBnc");
        };  
        objBnc.montarHtmlCE2017(jsBnc); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsBnc.form).style.width=jsBnc.width;
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
        btnFiltrarClick("S"); 
      });
      //
      var objBnc;                     // Obrigatório para instanciar o JS TFormaCob
      var jsBnc;                      // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS BancoF10
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var chkds;                      // Guarda todos registros checados na table 
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d06);
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado
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
        clsJs.add("rotina"      , "selectBnc"           );
        clsJs.add("login"       , jsPub[0].usr_login    );
        clsJs.add("ativo"       , atv                   );
        clsJs.add("codemp"      , jsPub[0].emp_codigo   );
        fd = new FormData();
        fd.append("banco" , clsJs.fim());
        msg     = requestPedido(arqLocal,fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsBnc.registros=objBnc.addIdUnico(retPhp[0]["dados"]);
          objBnc.ordenaJSon(jsBnc.indiceTable,false);  
          objBnc.montarBody2017();
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
            clsJs.add("titulo"      , objBnc.trazCampoExcel(jsBnc));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("banco"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido(arqLocal,fd); 
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
      ////////////////////////////
      //  AJUDA PARA FAVORECIDO //
      ////////////////////////////
      function fvrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fvrF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"cbEntraFluxo"
                      ,topo:100
                      ,tableBd:"FAVORECIDO"
                      ,fieldCod:"A.FVR_CODIGO"
                      ,fieldDes:"A.FVR_APELIDO"
                      ,fieldAtv:"A.FVR_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblFvr"}
        );
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value  = arr[0].CODIGO;
        document.getElementById("edtApelido").value  = arr[0].DESCRICAO;
        document.getElementById("edtDesFvr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodFvrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbEntraFluxo"
                                  ,topo:100
                                  ,tableBd:"FAVORECIDO"
                                  ,fieldCod:"A.FVR_CODIGO"
                                  ,fieldDes:"A.FVR_APELIDO"
                                  ,fieldAtv:"A.FVR_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblFvr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO             );
          document.getElementById("edtApelido").value  = ( ret.length == 0 ? ""      : ret[0].DESCRICAO  );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
        };
      };
      //////////////////////////////
      //  AJUDA PARA BANCOSTATUS  //
      //////////////////////////////
      function bstFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bstF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"cbPadraoFluxo"
                      ,topo:100
                      ,tableBd:"BANCOSTATUS"
                      ,fieldCod:"A.BST_CODIGO"
                      ,fieldDes:"A.BST_NOME"
                      ,fieldAtv:"A.BST_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblBst"}
        );
      };
      function RetF10tblBst(arr){
        document.getElementById("edtCodBst").value  = arr[0].CODIGO;
        document.getElementById("edtDesBst").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodBst").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodBstBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbPadraoFluxo"
                                  ,topo:100
                                  ,tableBd:"BANCOSTATUS"
                                  ,fieldCod:"A.BST_CODIGO"
                                  ,fieldDes:"A.BST_NOME"
                                  ,fieldAtv:"A.BST_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblBst"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "CR"  : ret[0].CODIGO             );
          document.getElementById("edtDesBst").value  = ( ret.length == 0 ? "CONTAS A PAGAR"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "CR" : ret[0].CODIGO )  );
        };
      };
      //////////////////////////////
      //  AJUDA PARA BANCOCODIGO  //
      //////////////////////////////
      function bcdFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bcdF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtAgencia"
                      ,topo:100
                      ,tableBd:"BANCOCODIGO"
                      ,fieldCod:"A.BCD_CODIGO"
                      ,fieldDes:"A.BCD_NOME"
                      ,fieldAtv:"A.BCD_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblBcd"}
        );
      };
      function RetF10tblBcd(arr){
        document.getElementById("edtCodBcd").value  = arr[0].CODIGO;
        document.getElementById("edtDesBcd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodBcd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodBcdBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtAgencia"
                                  ,topo:100
                                  ,tableBd:"BANCOCODIGO"
                                  ,fieldCod:"A.BCD_CODIGO"
                                  ,fieldDes:"A.BCD_NOME"
                                  ,fieldAtv:"A.BCD_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblBcd"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "CR"  : ret[0].CODIGO             );
          document.getElementById("edtDesBcd").value  = ( ret.length == 0 ? "CONTAS A PAGAR"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "CR" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////////
      // Trigger olha esta regra //
      /////////////////////////////
      function btnConfirmarClick(){
        try{
          ////////////////////////////////////////////////////
          // Procurando a coluna que preciso para checagem  //
          ////////////////////////////////////////////////////
          let buscaCol= new clsObterColunas(jsBnc,["CODIGO","PADRAO"]);
          buscaCol.appFilter();
          if( buscaCol.getNumCols() !=0  ){
            throw "Não localizado coluna CODIGO/PADRAO!";   
          } else {
            let objCol  = buscaCol.getObjeto();
            let numLin  = buscaCol.getNumLinhasTable();
            let tbl     = document.getElementById("tblBnc");
            if( document.getElementById("cbPadraoFluxo").value=="S" ){
              for(var lin=0 ; (lin<numLin) ; lin++){
                if( (objBnc.status==0) && (tbl.rows[lin].cells[objCol.PADRAO].innerHTML=="SIM") )
                  throw "JA EXISTE UM BANCO PADRÃO PARA FLUXO COM CODIGO "+tbl.rows[lin].cells[objCol.CODIGO].innerHTML;    
                
                if( objBnc.status==1 ){
                  if( (tbl.rows[lin].cells[objCol.PADRAO].innerHTML=="SIM") && (tbl.rows[lin].cells[objCol.CODIGO].innerHTML != document.getElementById("edtCodigo").value ) )    
                    throw "JA EXISTE UM BANCO PADRÃO PARA FLUXO COM CODIGO "+tbl.rows[lin].cells[objCol.CODIGO].innerHTML;   
                };
              };
            };  
            objBnc.gravar(true);
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////////////////////
      //           Tarifa            //
      /////////////////////////////////
      function horTarifaClick(){
        try{
          chkds=objBnc.gerarJson("1").gerar();
          chkds.forEach(function(reg){
            if( reg.ATIVO != "SIM" )
              throw "Banco "+reg.CODIGO+" inativo para lançamento!"; 
            if( reg.STATUS != "BCO" )
              throw "Banco "+reg.CODIGO+" não possui o STATUS BCO!"; 
          });
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para enviar ao formulario de alteracao
          //////////////////////////////////////////////////////////////
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("codbnc"      , chkds[0].CODIGO     );
          clsJs.add("codfvr"      , chkds[0].CODFVR     );
          clsJs.add("desfvr"      , chkds[0].DESFVR     );
          clsJs.add("desbnc"      , chkds[0].DESCRICAO    );          
          objEnvio=clsJs.fim();  
          localStorage.setItem("addAlt",objEnvio);
          window.open("Trac_CpCrTarifa.php");
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////////////////////
      //        Transferencia        //
      /////////////////////////////////
      function horTransferenciaClick(){
        try{
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para enviar ao formulario de alteracao
          //////////////////////////////////////////////////////////////
          let objEnvio;          
          clsJs=jsString("lote");
          objEnvio=clsJs.fim();  
          localStorage.setItem("addAlt",objEnvio);
          window.open("Trac_CpCrTransferencia.php");
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////////////////////
      //            Extrato          //
      /////////////////////////////////
      function horExtratoClick(){
        try{
          chkds=objBnc.gerarJson("1").gerar();
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para enviar ao formulario de alteracao
          //////////////////////////////////////////////////////////////
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("codbnc"      , chkds[0].CODIGO     );
          clsJs.add("desbnc"      , chkds[0].DESCRICAO    );          
          objEnvio=clsJs.fim();  
          localStorage.setItem("addAlt",objEnvio);
          window.open("Trac_CpCrExtrato.php");
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      //////////////////
      // Alterar empresa
      //////////////////
      function horEmpresaClick(){
        try{          
          clsJs   = jsString("lote");  
          clsJs.add("rotina"  , "altEmpresa"                );
          clsJs.add("login"   , jsPub[0].usr_login          );
          fd = new FormData();
          fd.append("banco" , clsJs.fim());
          
          msg     = requestPedido(arqLocal,fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            janelaDialogo(
              { height          : "25em"
                ,body           : "16em"
                ,left           : "400px"
                ,top            : "60px"
                ,tituloBarra    : "Alterar empresa"
                ,width          : "43em"
                ,fontSizeTitulo : "1.8em"             //  padrao 2em que esta no css
                ,code           : retPhp[0]["dados"]  //  clsCode.fim()
              }
            );  
            let scr = document.createElement('script');
            scr.innerHTML = retPhp[0]["script"];
            document.getElementsByTagName('body')[0].appendChild(scr);        
          };
        }catch(e){
          gerarMensagemErro('catch',e.message,{cabec:"Erro"});
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
              name="frmBnc" 
              id="frmBnc" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Banco<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 250px; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo10">
                <input class="campo_input" id="edtCodigo" maxlength="10" type="text" disabled />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input" id="edtDescricao" type="text" maxlength="40" />
                <label class="campo_label campo_required" for="edtDescricao">DESCRICAO</label>
              </div>
			
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodFvr"
                                                    onBlur="CodFvrBlur(this);" 
                                                    onFocus="fvrFocus(this);" 
                                                    onClick="fvrF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="6"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodFvr">FAVOREC:</label>
              </div>
              <div class="campotexto campo30">
                <input class="campo_input_titulo input" id="edtApelido" type="text" disabled />
                <label class="campo_label campo_required" for="edtApelido">NOME_FAVORECIDO:</label>
              </div>
             <div class="campotexto campo25">
              <select class="campo_input_combo" id="cbEntraFluxo">
                <option value="S">SIM</option>
                <option value="N">NAO</option>
              </select>
              <label class="campo_label campo_required" for="cbEntraFluxo">ENTRA FLUXO:</label>
             </div>
             <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodBst"
                                                    onBlur="CodBstBlur(this);" 
                                                    onFocus="bstFocus(this);" 
                                                    onClick="bstF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="2"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodBst">BST:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesBst" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesBst">STATUS BANCO</label>
              </div>
             <div class="campotexto campo25">
              <select class="campo_input_combo" id="cbPadraoFluxo">
                <option value="S">SIM</option>
                <option value="N">NAO</option>
              </select>
              <label class="campo_label campo_required" for="cbPadraoFluxo">BANCO PADRAO:</label>
             </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodBcd"
                                                    onBlur="CodBcdBlur(this);" 
                                                    onFocus="bcdFocus(this);" 
                                                    onClick="bcdF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodBcd">BCD:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesBcd" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesBcd">CÓDIGO BANCO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtAgencia" maxlength="8" type="text" />
                <label class="campo_label campo_required" for="edtAgencia">AGENCIA:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input" id="edtAgenciaDiv" maxlength="1" type="text" />
                <label class="campo_label campo_required" for="edtAgenciaDiv">DÍG:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtConta" maxlength="8" type="text" />
                <label class="campo_label campo_required" for="edtConta">CONTA:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input" id="edtContaDiv" maxlength="1" type="text" />
                <label class="campo_label campo_required" for="edtContaDiv">DÍG:</label>
              </div>
              
             <div class="campotexto campo20">
              <select class="campo_input_combo" id="cbCnab">
                <option value="S">SIM</option>
                <option value="N">NAO</option>
              </select>
              <label class="campo_label campo_required" for="cbCnab">ACEITA CNAB:</label>
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
              <div class="campotexto campo20">
                <input class="campo_input_titulo input" id="edtDesEmp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesEmp">EMPRESA:</label>
              </div>
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
                <input id="edtDesFvr" type="text" />
                <input id="edtCodEmp" type="text" />
                <input id="edtSaldo" type="text" />                
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