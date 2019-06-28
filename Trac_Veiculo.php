<?php
  session_start();
  if( isset($_POST["veiculo"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["veiculo"]);
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
        /////////////////////////////////////////
        //    Dados para JavaScript VEICULO    //
        /////////////////////////////////////////
        if( $rotina=="selectVcl" ){
          $sql="";
          $sql.="SELECT A.VCL_CODIGO";
          $sql.="       ,A.VCL_CODCNTT";          
          $sql.="       ,A.VCL_CODFVR";
          $sql.="       ,FVR.FVR_NOME";          
          $sql.="       ,FVR.FVR_APELIDO";
          $sql.="       ,A.VCL_CODVCR";
          $sql.="       ,VCR.VCR_NOME";
          $sql.="       ,A.VCL_CODVTP";
          $sql.="       ,VTP.VTP_NOME";
          $sql.="       ,VFB.VFB_NOME";
          $sql.="       ,A.VCL_CODVMD";
          $sql.="       ,VMD.VMD_NOME";
          $sql.="       ,A.VCL_ANO";          
          $sql.="       ,CASE WHEN A.VCL_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS VCL_ATIVO";
          $sql.="       ,CASE WHEN A.VCL_REG='P' THEN 'PUB' WHEN A.VCL_REG='S' THEN 'SIS' ELSE 'ADM' END AS VCL_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.VCL_CODUSR";
          $sql.="  FROM VEICULO A";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.VCL_CODFVR=FVR.FVR_CODIGO";          
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.VCL_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN VEICULOCOR VCR ON A.VCL_CODVCR=VCR.VCR_CODIGO";
          $sql.="  LEFT OUTER JOIN VEICULOTIPO VTP ON A.VCL_CODVTP=VTP.VTP_CODIGO";
          $sql.="  LEFT OUTER JOIN VEICULOMODELO VMD ON A.VCL_CODVMD=VMD.VMD_CODIGO";
          $sql.="  LEFT OUTER JOIN VEICULOFABRICANTE VFB ON VMD.VMD_CODVFB=VFB.VFB_CODIGO";          
          $sql.=" WHERE ((VCL_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."'))";                 
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
          $vldCampo = new validaCampo("VVEICULO",0);
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
        /////////////////////////////////////////////////////////////////////
        // Atualizando o veiculo de dados se opcao de insert/updade/delete //
        /////////////////////////////////////////////////////////////////////
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
    <title>Veiculo</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaVeiculoModeloF10.js"></script>                
    <script src="tabelaTrac/f10/tabelaFavorecidoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaContratoF10.js"></script>     
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        //////////////////////////////////////
        //   Objeto clsTable2017 VEICULO    //
        //////////////////////////////////////
        jsVcl={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1 ,"field"           : "VCL_CODIGO" 
                      ,"pk"             : "S"
                      ,"insUpDel"       : ["S","N","N"]                      
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]
                      ,"digitosMinMax"  : [7,20]
                      ,"labelCol"       : "PLACA_CHASSI"  
                      ,"obj"            : "edtCodigo"  
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "30"
                      ,"padrao":0}  
            ,{"id":2 ,"field"           : "CONTRATO" 
                      ,"insUpDel"       : ["S","N","N"]                      
                      ,"labelCol"       : "CONTRATO"  
                      ,"obj"            : "edtCodCntt"
                      ,"fieldType"      : "int"
                      ,"newRecord"      : ["0000","this","this"]  
                      ,"align"          : "center"  
                      ,"tamGrd"         : "6em"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"tamImp"         : "20"
                      ,"padrao":0}  
            ,{"id":3  ,"field"          : "VCL_CODFVR" 
                      ,"labelCol"       : "CODFVR"
                      ,"obj"            : "edtCodFvr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [  "Codigo do Favorecido. Registro deve existir na tabela Favorecido e tem o formato 999"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "FVR_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "NOME_CLIENTE"
                      ,"obj"            : "edtDesFvr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,60]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição de FAVORECIDO para este veiculo."]
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "FVR_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "NOME_CLIENTE"
                      ,"obj"            : "edtDesFvr"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,60]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição de FAVORECIDO para este veiculo."]
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "VCL_CODVCR" 
                      ,"labelCol"       : "CODVCR"
                      ,"obj"            : "edtCodVcr"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0001","this","this"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"ajudaCampo"     : [ "Código do cor"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "VCR_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "COR"
                      ,"obj"            : "edtDesVcr"
                      ,"newRecord"      : ["NAO SE APLICA","this","this"]
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [1,30]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição da cor do veiculo para este registro."]
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "VCL_CODVTP"
                      ,"labelCol"       : "CODVTP"
                      ,"obj"            : "edtCodVtp"
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"tamGrd"         : "0em"
                      ,"digitosMinMax"  : [2,3]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|"
                      ,"ajudaCampo"     : ["Codigo da marca."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "VTP_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "TIPO"
                      ,"obj"            : "edtDesVtp"
                      ,"newRecord"      : ["NAO SE APLICA","this","this"]                      
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [1,20]                      
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do tipo."]
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "VFB_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "FABRICANTE"
                      ,"obj"            : "edtDesVfb"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do fabricante."]
                      ,"padrao":0}
            ,{"id":11 ,"field"          : "VCL_CODVMD" 
                      ,"labelCol"       : "CODVMD"
                      ,"obj"            : "edtCodVmd"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0001","this","this"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"ajudaCampo"     : [ "Código do modelo"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":12 ,"field"          : "VMD_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "MODELO"
                      ,"obj"            : "edtDesVmd"
                      ,"newRecord"      : ["NAO SE APLICA","this","this"]                      
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "50"
                      ,"digitosMinMax"  : [1,20]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do modelo."]
                      ,"padrao":0}
            ,{"id":13 ,"field"          :"VCL_ANO" 
                      ,"labelCol"       : "ANO"
                      ,"obj"            : "edtAno"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"                                      
                      ,"newRecord"      : ["0000","this","this"]
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"ajudaCampo"     : [  "Ano de facricação do veiculo"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":14 ,"field"          : "VCL_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":15 ,"field"          : "VCL_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":16 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":17 ,"field"          : "VCL_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsr"  
                      ,"padrao":5}                                      
            ,{"id":18 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objVcl.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"VEICULO - Detalhe do registro"
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
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmVcl"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaVcl"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmVcl"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblVcl"              // Nome da table
          ,"prefixo"        : "Vcl"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VVEICULO"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPVEICULO"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "VCL_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "VCL_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "VCL_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "115em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "VEICULO"             // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "7"                   // Fonte do relatório
          ,"foco"           : ["edtCodigo"
                              ,"cbAtivo"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objVcl.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objVcl.AjudaSisAtivo(jsVcl);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objVcl.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objVcl.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objVcl.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objVcl.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objVcl.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objVcl.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "VEICULO[33]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objVcl === undefined ){  
          objVcl=new clsTable2017("objVcl");
        };  
        objVcl.montarHtmlCE2017(jsVcl); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        $doc(jsVcl.form).style.width=jsVcl.width;
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
            ,{"id":1  ,"field":"ERRO"       ,"labelCol":"ERRO"      ,"tamGrd":"45em"    ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[1].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
      var objVcl;                     // Obrigatório para instanciar o JS TFormaCob
      var jsVcl;                      // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objPadF10;                  // Obrigatório para instanciar o JS PadraoF10
      var objVmdF10;                  // Obrigatório para instanciar o JS VeiculoModeloF10                        
      var objFvrF10;                  // Obrigatório para instanciar o JS FavorecidoF10
      var objCnttF10;                 // Obrigatório para instanciar o JS ContratoF10
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
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
        $doc("divRotina").style.display  = (intOpc==0 ? "block" : "none" );        
        $doc("divExcel").style.display   = (intOpc==1 ? "block" : "none" );
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
        clsJs.add("rotina"      , "selectVcl"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("veiculo" , clsJs.fim());
        msg     = requestPedido("Trac_Veiculo.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsVcl.registros=objVcl.addIdUnico(retPhp[0]["dados"]);
          objVcl.ordenaJSon(jsVcl.indiceTable,false);  
          objVcl.montarBody2017();
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
            clsJs.add("titulo"      , objVcl.trazCampoExcel(jsVcl));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("veiculo"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Veiculo.php",fd); 
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
            gerarMensagemErro("Vcl",retPhp[0].erro,{cabec:"Aviso"});    
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
        fFavorecidoF10(0,obj.id,"edtCodVcr",100);         
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value  = arr[0].CODIGO;
        document.getElementById("edtApelido").value  = arr[0].RESUMO;
        document.getElementById("edtDesFvr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodFvrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fFavorecidoF10(1,obj.id,"edtCodVcr",100);   
          document.getElementById(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO     );
          document.getElementById("edtApelido").value = ( ret.length == 0 ? ""  : ret[0].RESUMO     );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? ""  : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////
      //  AJUDA PARA VEICULOCOR //
      ////////////////////////////
      function vcrFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",$doc(obj.id).value); 
      };
      function vcrF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodVtp"
                      ,topo:100
                      ,tableBd:"VEICULOCOR"
                      ,fieldCod:"A.VCR_CODIGO"
                      ,fieldDes:"A.VCR_NOME"
                      ,fieldAtv:"A.VCR_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblVcr"}
        );
      };
      function RetF10tblVcr(arr){
        $doc("edtCodVcr").value  = arr[0].CODIGO;
        $doc("edtDesVcr").value  = arr[0].DESCRICAO;
        $doc("edtCodVcr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codVcrBlur(obj){
        var elOld = jsNmrs($doc(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodVtp"
                                  ,topo:100
                                  ,tableBd:"VEICULOCOR"
                                  ,fieldCod:"A.VCR_CODIGO"
                                  ,fieldDes:"A.VCR_NOME"
                                  ,fieldAtv:"A.VCR_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblVcr"}
          );
          $doc(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO             );
          $doc("edtDesVcr").value  = ( ret.length == 0 ? ""  : ret[0].DESCRICAO  );
          $doc(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////////
      // AJUDA PARA VEICULO TIPO //
      /////////////////////////////
      function vtpFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",$doc(obj.id).value); 
      };
      function vtpF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodVmd"
                      ,topo:100
                      ,tableBd:"VEICULOTIPO"
                      ,fieldCod:"A.VTP_CODIGO"
                      ,fieldDes:"A.VTP_NOME"
                      ,fieldAtv:"A.VTP_ATIVO"
                      ,typeCod :"str" 
					  ,tbl:"tblVtp"}
        );
      };
      function RetF10tblVtp(arr){
        $doc("edtCodVtp").value  = arr[0].CODIGO;
        $doc("edtDesVtp").value  = arr[0].DESCRICAO;
        $doc("edtCodVtp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codVtpBlur(obj){
        var elOld = $doc(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodVmd"
                                  ,topo:100
                                  ,tableBd:"VEICULOTIPO"
                                  ,fieldCod:"A.VTP_CODIGO"
                                  ,fieldDes:"A.VTP_NOME"
                                  ,fieldAtv:"A.VTP_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblVtp"}
          );
          $doc(obj.id).value       = ( ret.length == 0 ? "NSA"  : ret[0].CODIGO             );
          $doc("edtDesVtp").value  = ( ret.length == 0 ? "NAO SE APLICA"      : ret[0].DESCRICAO  );
          $doc(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "NSA" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////////////
      //  AJUDA PARA VEICULO MODELO  //
      /////////////////////////////////
      function vmdFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",$doc(obj.id).value); 
      };
      function vmdF10Click(obj){ 
        fVeiculoModeloF10(0,obj.id,"edtAno",100); 
      };
      function RetF10tblVmd(arr){
        $doc("edtCodVmd").value  = arr[0].CODIGO;
        $doc("edtDesVmd").value  = arr[0].DESCRICAO;
        $doc("edtDesVfb").value  = arr[0].FABRICANTE;
        $doc("edtCodVmd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codVmdBlur(obj){
        var elOld = jsNmrs($doc(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          let ret = fVeiculoModeloF10(1,obj.id,"edtAno",100);           
          $doc(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO                  );
          $doc("edtDesVmd").value  = ( ret.length == 0 ? ""  : ret[0].DESCRICAO               );
          $doc("edtDesVFB").value  = ( ret.length == 0 ? ""  : ret[0].DESCRICAO               );          
          $doc(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////////
      // AJUDA PARA CONTRATO //////
      /////////////////////////////
      function cnttFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",$doc(obj.id).value); 
      };
      function cnttF10Click(obj){ 
        fContratoF10(0,obj.id,"edtCodCntt",100,{whr:$doc("edtCodFvr").value}); 
      };
      function RetF10tblCntt(arr){
        $doc("edtCodCntt").value  = arr[0].CODIGO;
        $doc("edtCodCntt").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codCnttBlur(obj){
        var elOld = jsNmrs($doc(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          let ret = fContratoF10(0,obj.id,"edtCodCntt",100,{whr:$doc("edtCodFvr").value});           
          $doc(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO                  );          
          $doc(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
        };
      };

      /////////////////////////////
      // Trigger olha esta regra //
      /////////////////////////////
      function btnConfirmarClick(){
        try{          
          $doc("edtCodigo").value = jsStr("edtCodigo").upper().alltrim().ret();
          msg = new clsMensagem("Erro"); 
          let continua=fncPlacaValida($doc("edtCodigo").value);
          if( continua != "ok" ){
            gerarMensagemErro("catch",continua,"Erro");
          } else {
            objVcl.gravar(true);            
          }; 
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
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
              name="frmVcl" 
              id="frmVcl" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Veiculo<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>    
          <div style="height: 190px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodigo" type="text" maxlength="20" />
                <label class="campo_label campo_required" for="edtCodigo">PLACA_CHASSI:</label>
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
                <label class="campo_label campo_required" for="edtCodFvr">CLIENTE:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesFvr" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesFvr">NOME_CLIENTE:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodCntt"
                                                    onBlur="codCnttBlur(this);" 
                                                    onFocus="cnttFocus(this);" 
                                                    onClick="cnttF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="6"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCntt">Contrato:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodVcr"
                                                    onBlur="codVcrBlur(this);" 
                                                    onFocus="vcrFocus(this);" 
                                                    onClick="vcrF10Click(this);"
                                                    data-oldvalue="0000" 
                                                    autocomplete="off"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodVcr">COR:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input_titulo input" id="edtDesVcr" 
                                                        type="text" disabled />
                <label class="campo_label campo_required" for="edtDesVcr">NOME_COR</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodVtp"
                                                    onBlur="codVtpBlur(this);" 
                                                    onFocus="vtpFocus(this);" 
                                                    onClick="vtpF10Click(this);"
                                                    data-oldvalue="" 
                                                    autocomplete="off"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodVtp">TIPO:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesVtp" 
                                                        type="text" disabled />
                <label class="campo_label campo_required" for="edtDesVtp">NOME_TIPO</label>
              </div>

              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodVmd"
                                                    onBlur="codVmdBlur(this);" 
                                                    onFocus="vmdFocus(this);" 
                                                    onClick="vmdF10Click(this);"
                                                    data-oldvalue="0000" 
                                                    autocomplete="off"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodVmd">MODELO:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesVmd" 
                                                        type="text" disabled />
                <label class="campo_label campo_required" for="edtDesVmd">NOME_MODELO</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesVfb" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesVfb">NOME_FABRICANTE</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input" id="edtAno"
                                           OnKeyPress="return mascaraInteiro(event);"
                                           maxlength="4"
                                           type="text" />
                <label class="campo_label" for="edtAno">ANO</label>
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
                <input id="edtCodUsr" type="text" />             
                <input id="edtApelido" type="text" />
              </div>
              <div id="btnConfirmar" onclick="btnConfirmarClick();" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
              <div id="btnCancelar"  class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
              <div class="campotexto campo70">
                <label class="labelMensagem" for="edtUsuario">Veiculo deve estar com todos os dados preenchidos para ser adicionado a um contrato</label>
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