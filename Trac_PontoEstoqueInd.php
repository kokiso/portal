<?php
  session_start();
  if( isset($_POST["pontoestoqueind"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["pontoestoqueind"]);
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
        ///////////////////////////////////////////////////
        //               Buscando os GRUPOS              //
        ///////////////////////////////////////////////////
        if( $rotina=="grupo" ){
          $sql="SELECT PE_CODIGO AS CODIGO,PE_NOME AS NOME FROM PONTOESTOQUE WHERE PE_CODIGO NOT IN('AUT','EST','SUC') AND PE_ATIVO='S'";
          //$sql="SELECT PE_CODIGO AS CODIGO,PE_NOME AS NOME FROM PONTOESTOQUE WHERE PE_CODIGO NOT IN('AUT') AND PE_ATIVO='S'";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["retorno"] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls["erro"].'"}]';
          } else {
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls["dados"]).',"erro":""}]';
          };
        };  
        ///////////////////////////////////////////////////
        //  Dados para JavaScript PONTOESTOQUEIND        //
        ///////////////////////////////////////////////////
        if( $rotina=="selectPei" ){
          $sql="";
          $sql.="SELECT A.PEI_CODFVR";
          $sql.="       ,FVR.FVR_NOME";
          $sql.="       ,A.PEI_CODPE";
          $sql.="       ,PE.PE_NOME";
          $sql.="       ,CASE WHEN A.PEI_STATUS='BOM' THEN CAST('BOM' AS VARCHAR(3))";
          $sql.="             WHEN A.PEI_STATUS='OTI' THEN CAST('OTIMO' AS VARCHAR(5))";          
          $sql.="             WHEN A.PEI_STATUS='RAZ' THEN CAST('RAZOAVEL' AS VARCHAR(8))";
          $sql.="             WHEN A.PEI_STATUS='RUI' THEN CAST('RUIM' AS VARCHAR(4))";          
          $sql.="             WHEN A.PEI_STATUS='NSA' THEN CAST('...' AS VARCHAR(3)) END AS PEI_STATUS";
          $sql.="       ,FVR.FVR_LATITUDE";
          $sql.="       ,FVR.FVR_LONGITUDE";
          $sql.="       ,A.PEI_CODLGN";
          $sql.="       ,LGN.USR_APELIDO AS LGN_NOME";
          $sql.="       ,CASE WHEN A.PEI_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS PEI_ATIVO";
          $sql.="       ,CASE WHEN A.PEI_REG='P' THEN 'PUB' WHEN A.PEI_REG='S' THEN 'SIS' ELSE 'ADM' END AS PEI_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.PEI_CODUSR";
          $sql.="  FROM PONTOESTOQUEIND A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.PEI_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PEI_CODFVR=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN PONTOESTOQUE PE ON A.PEI_CODPE=PE.PE_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIO LGN ON A.PEI_CODLGN=LGN.USR_CODIGO";                            
          $sql.=" WHERE (A.PEI_CODPE='".$lote[0]->grupo."')";
          $sql.="   AND ((PEI_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."'))"; 
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
          $vldCampo = new validaCampo("VPONTOESTOQUEIND",0);
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
        /////////////////////////////////////////////////////////////////////////////
        // Atualizando o pontoestoqueind de dados se opcao de insert/updade/delete //
        /////////////////////////////////////////////////////////////////////////////
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
    <title>Estoque</title>
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
        //////////////////////////////////////////////////
        //   Objeto clsTable2017 PONTOESTOQUEIND        //
        //////////////////////////////////////////////////
        jsPei={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"PEI_CODFVR" 
                      ,"pk"             : "S"
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodFvr"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [ "Código do Favorecido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "FVR_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesFvr"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "35em"
                      ,"tamImp"         : "100"
                      ,"newRecord"      : ["","this","this"]
                      ,"padrao":0}
            ,{"id":3 ,"field"          : "PEI_CODPE" 
                      ,"pk"             : "S"
                      ,"insUpDel"       : ["S","N","N"]                      
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"digitosMinMax"  : [1,3]
                      ,"labelCol"       : "PONTO" 
                      ,"inputDisabled"  : true                      
                      ,"obj"            : "edtCodPe"  
                      ,"importaExcel"   : "S"                      
                      ,"padrao":0}  
            ,{"id":4  ,"field"          : "PE_NOME"   
                      ,"labelCol"       : "PONTO"
                      ,"obj"            : "edtDesPe"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"align"          : "center"
                      ,"newRecord"      : ["","this","this"]
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas"]                      
                      ,"ajudaCampo"     : ["Nome da empresa."]
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "PEI_STATUS"  
                      ,"labelCol"       : "STATUS" 
                      ,"newRecord"      : ["NSA","this","this"]
                      ,"obj"            : "cbStatus"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "str"
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,3]
                      ,"ajudaCampo"     : [ "Status do ponto"]
                      ,"funcCor": "switch (objCell.innerHTML) { case 'OTIMO':objCell.classList.add('corVerde');break; case 'BOM':objCell.classList.add('corAzul');break; case 'RAZOAVEL':objCell.classList.add('corTitulo');break; case 'RUIM':objCell.classList.add('corAlterado');break; default:objCell.classList.remove('corAlterado');break;};"
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":6  ,"field"          :"FVR_LATITUDE" 
                      ,"labelCol"       : "LATITUDE"
                      ,"insUpDel"       : ["N","N","N"]                      
                      ,"obj"            : "edtLatitude"
                      ,"inputDisabled"  : true                      
                      ,"fieldType"      : "flo8" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"
                      ,"importaExcel"   : "N"                                                                
                      ,"padrao":0}
            ,{"id":7  ,"field"          :"FVR_LONGITUDE" 
                      ,"labelCol"       : "LONGITUDE"
                      ,"insUpDel"       : ["N","N","N"]                      
                      ,"obj"            : "edtLongitude"
                      ,"inputDisabled"  : true                      
                      ,"fieldType"      : "flo8" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"
                      ,"importaExcel"   : "N"                                                                
                      ,"padrao":0}
                      
            ,{"id":8  ,"field"          :"PEI_CODLGN" 
                      ,"labelCol"       : "CODLGN"
                      ,"obj"            : "edtCodLgn"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"align"          : "center"                                      
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["S","S","N"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"ajudaCampo"     : [  "Codigo do usuario relacionado com este colaborador"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "LGN_NOME"
                      ,"insUpDel"       : ["N","N","N"]            
                      ,"labelCol"       : "LOGIN"
                      ,"obj"            : "edtDesLgn"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : ["Login vinculado ao colaborador."]
                      ,"importaExcel"   : "N"   
                      ,"popoverTitle"   : "Faz o vinculo entre usuario logado com responsavel pela OS"                          
                      ,"popoverLabelCol": "Login"                      
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "PEI_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"tamImp"         : "10"                      
                      ,"padrao":2}                                        
            ,{"id":11 ,"field"          : "PEI_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":12 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":13 ,"field"          : "PEI_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":14 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objPei.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"ESTOQUE - Detalhe do registro"
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
          ,"registros"      : []                          // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                        // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)                        
          ,"checarTags"     : "N"                         // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"              // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"               // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmPei"                    // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaPei"                 // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmPei"                    // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"             // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblPei"                    // Nome da table
          ,"prefixo"        : "Pei"                       // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VPONTOESTOQUEIND"          // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPPONTOESTOQUEIND"        // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "PEI_ATIVO"                 // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "PEI_REG"                   // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "PEI_CODUSR"                // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"               // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "106em"                     // Tamanho da table
          ,"height"         : "58em"                      // Altura da table
          ,"tableLeft"      : "sim"                       // Se tiver menu esquerdo
          ,"relTitulo"      : "ESTOQUE"                   // Titulo do relatório
          ,"relOrientacao"  : "R"                         // Paisagem ou retrato
          ,"relFonte"       : "7"                         // Fonte do relatório
          ,"foco"           : ["edtCodFvr"
                              ,"cbAtivo"
                              ,"btnConfirmar"]            // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"           // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "NOME"                      // Indice inicial da table
          ,"tamBotao"       : "12"                        // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"                    // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objPei.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objPei.AjudaSisAtivo(jsPei);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objPei.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objPei.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objPei.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objPei.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objPei.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objPei.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "ESTOQUE[33]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objPei === undefined ){  
          objPei=new clsTable2017("objPei");
        };  
        objPei.montarHtmlCE2017(jsPei); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsPei.form).style.width=jsPei.width;
        document.getElementById("divSobreTable").style.width=jsPei.width;
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
            ,{"id":1  ,"field":"PONTO"      ,"labelCol":"PONTO"     ,"tamGrd":"6em"     ,"tamImp":"20"}             
            ,{"id":2  ,"field":"ERRO"       ,"labelCol":"ERRO"      ,"tamGrd":"45em"    ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[2].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
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
        buscarGrupo();        
        btnFiltrarClick("S");  
      });
      //
      var objPei;                     // Obrigatório para instanciar o JS TFormaCob
      var jsPei;                      // Obj principal da classe clsTable2017
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
      var intCodDir = parseInt(jsPub[0].usr_d33);
      /////////////////////
      // Buscando os grupos
      /////////////////////
      function buscarGrupo(){
        clsJs   = jsString("lote");
        clsJs.add("rotina"  , "grupo"             );
        clsJs.add("login"   , jsPub[0].usr_login  );
        fd = new FormData();
        fd.append("pontoestoqueind" , clsJs.fim()); 
        msg = requestPedido("Trac_PontoEstoqueInd.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          let ceOpt;
          let contador=0;
          retPhp[0]["dados"].forEach(function(reg){
            ceOpt 	= document.createElement("option");        
            ceOpt.value = reg.CODIGO;
            ceOpt.text  = reg.NOME;
            document.getElementById("cbPonto").appendChild(ceOpt);
            // Atribuindo valor devido cadastro
            if( contador==0 ){
              jsPei.titulo[3].newRecord[0]=reg.CODIGO;
              jsPei.titulo[4].newRecord[0]=reg.NOME;
              contador=1;
            };
          });
        };  
      };
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
        clsJs.add("rotina"      , "selectPei"                               );
        clsJs.add("login"       , jsPub[0].usr_login                        );
        clsJs.add("ativo"       , atv                                       );
        clsJs.add("grupo"       , document.getElementById("cbPonto").value  );
        fd = new FormData();
        fd.append("pontoestoqueind" , clsJs.fim());
        msg     = requestPedido("Trac_PontoEstoqueInd.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsPei.registros=objPei.addIdUnico(retPhp[0]["dados"]);
          objPei.ordenaJSon(jsPei.indiceTable,false);  
          objPei.montarBody2017();
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
            clsJs.add("titulo"      , objPei.trazCampoExcel(jsPei));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("pontoestoqueind"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_PontoEstoqueInd.php",fd); 
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
            gerarMensagemErro("Pei",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
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
                      ,foco:"cbStatus"
                      ,topo:100
                      ,tableBd:"FAVORECIDO"
                      ,fieldCod:"A.FVR_CODIGO"
                      ,fieldDes:"A.FVR_NOME"
                      ,fieldAtv:"A.FVR_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblFvr"}
        );
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value  = arr[0].CODIGO;
        document.getElementById("edtDesFvr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodFvrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbStatus"
                                  ,topo:100
                                  ,tableBd:"FAVORECIDO"
                                  ,fieldCod:"A.FVR_CODIGO"
                                  ,fieldDes:"A.FVR_NOME"
                                  ,fieldAtv:"A.FVR_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblFvr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "59"  : ret[0].CODIGO             );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? "VTEX - VITRINE TEXTIL LTDA"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "59" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////
      //  AJUDA PARA LOGIN      //
      ////////////////////////////
      function lgnFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function lgnF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"cbAtivo"
                      ,topo:100
                      ,tableBd:"USUARIO"
                      ,fieldCod:"A.USR_CODIGO"
                      ,fieldDes:"A.USR_APELIDO"
                      ,fieldAtv:"A.USR_ATIVO"
                      ,typeCod :"int" 
                      ,where:" AND (A.USR_CODIGO NOT IN(1,2))"
                      ,tbl:"tblLgn"}
        );
      };
      function RetF10tblLgn(arr){
        document.getElementById("edtCodLgn").value  = arr[0].CODIGO;
        document.getElementById("edtDesLgn").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodLgn").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codLgnBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"cbAtivo"
                                  ,topo:100
                                  ,tableBd:"USUARIO"
                                  ,fieldCod:"A.USR_CODIGO"
                                  ,fieldDes:"A.USR_NOME"
                                  ,fieldAtv:"A.USR_ATIVO"
                                  ,typeCod :"int" 
                                  ,where:" AND (A.USR_CODIGO NOT IN(1,2))"
                                  ,tbl:"tblLgn"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "000"  : ret[0].CODIGO                  );
          document.getElementById("edtDesLgn").value  = ( ret.length == 0 ? "NAO SE APLICA"      : ret[0].DESCRICAO );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "000" : ret[0].CODIGO )  );
        };
      };
      //
      function fncRefazFiltro(){
        // Igualando o objeto devido cadastro
        jsPei.titulo[3].newRecord[0]=document.getElementById("cbPonto").value;
        jsPei.titulo[4].newRecord[0]=document.getElementById("cbPonto").options[document.getElementById("cbPonto").selectedIndex].text;
        btnFiltrarClick("S");
      }
    </script>
  </head>
  <body>
    <div id="divSobreTable" class="divSobreTable">
      <div class="campotexto campo25" style="margin-top:3px;margin-left:3px;">
        <select onChange="fncRefazFiltro();" class="campo_input_combo" id="cbPonto">
        </select>
        <label class="campo_label campo_required" for="cbPonto">PONTO</label>
      </div>
      <div class="campo10" style="float:left;margin-right:5px;">            
        <div id="btnFiltrar" onClick="btnFiltrarClick('S');" class="btnImagemEsq bie100 bieAzul bieRight" style="margin-top:3px;"><i class="fa fa-filter"> Filtrar</i></div>
      </div>
      <div class="_campotexto campo100" style="margin-top:1.7em;height:3em;">
        <label class="solicitadoFiltro"></label>
      </div>              
    </div>
    <div class="divTelaCheia">
      <div id="divRotina" class="conteudo">  
        <div id="divTopoInicio">
        </div>
        <form method="post" 
              name="frmPei" 
              id="frmPei" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Ponto estoque individual<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 220px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodFvr"
                                                    onBlur="CodFvrBlur(this);" 
                                                    onFocus="fvrFocus(this);" 
                                                    onClick="fvrF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="6"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodFvr">CODIGO:</label>
              </div>
              <div class="campotexto campo85">
                <input class="campo_input_titulo input" id="edtDesFvr" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesFvr">FAVORECIDO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input_titulo" id="edtCodPe" maxlength="3" type="text" />
                <label class="campo_label campo_required" for="edtCodPe">PONTO:</label>
              </div>
              
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesPe" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesPe">PONTO_NOME</label>
              </div>
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbStatus">
                  <option value="NSA">...</option>
                  <option value="OTI">OTIMO</option>
                  <option value="BOM">BOM</option>
                  <option value="RAZ">RAZOAVEL</option>
                  <option value="RUI">RUIM</option>
                </select>
                <label class="campo_label" for="cbStatus">STATUS</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodLgn"
                                                    OnKeyPress="return mascaraInteiro(event);"
                                                    onBlur="codLgnBlur(this);" 
                                                    onFocus="lgnFocus(this);" 
                                                    onClick="lgnF10Click(this);"
                                                    data-oldvalue="0000" 
                                                    autocomplete="off"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodFvr">LOGIN:</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input_titulo input" id="edtDesLgn" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesLgn">USUARIO</label>
              </div>
              <div class="campotexto campo10">
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
              <div class="campotexto campo15">
                <input class="campo_input_titulo edtDireita" id="edtLatitude" 
                                                             value="0,000000" 
                                                             type="text" disabled />
                <label class="campo_label campo_required" for="edtLatitude">LATITUDE:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo edtDireita" id="edtLongitude" 
                                                             value="0,000000"                 
                                                             type="text" disabled />
                <label class="campo_label campo_required" for="edtLongitude">LONGITUDE:</label>
              </div>
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
                <input id="edtCodEmp" type="text" />
              </div>
              <div id="btnConfirmar" class="btnImagemEsq bie12 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
              <div id="btnCancelar"  class="btnImagemEsq bie12 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
              <div class="campotexto campo100">
                <label class="labelMensagem" for="edtUsuario">- <b>Status</b> nota dada ao terceiro/cliente para escolha de instalação futura</label>
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