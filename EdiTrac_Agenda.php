<?php
  session_start();
  if( isset($_POST["agenda"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["agenda"]);
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
        /////////////////////////////////////
        //    Dados para JavaScript AGENDA //
        /////////////////////////////////////
        if( $rotina=="selectAge" ){
          $sql="";
          $sql.="SELECT A.AGE_CODIGO";
          $sql.="       ,A.AGE_CODAT";
          $sql.="       ,G.AT_NOME";
          $sql.="       ,A.AGE_RESPONSAVEL";
		  $sql.="       ,CONVERT(VARCHAR(10),A.AGE_VENCTO,127) AS AGE_VENCTO";		  
		  $sql.="       ,CONVERT(VARCHAR(10),A.AGE_DTCADASTRO,127) AS AGE_DTCADASTRO";		  
          $sql.="       ,A.AGE_CADASTROU";
		  $sql.="       ,CONVERT(VARCHAR(10),A.AGE_DTBAIXA,127) AS AGE_DTBAIXA";		  
          $sql.="       ,A.AGE_CODEMP";
          $sql.="       ,E.EMP_APELIDO";
          //$sql.="       ,CASE WHEN A.AGE_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS AGE_ATIVO";
          $sql.="       ,CASE WHEN A.AGE_REG='P' THEN 'PUB' WHEN A.AGE_REG='S' THEN 'SIS' ELSE 'ADM' END AS AGE_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.AGE_CODUSR";
          $sql.="  FROM AGENDA A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.AGE_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN AGENDATAREFA G ON A.AGE_CODAT=G.AT_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA E ON A.AGE_CODEMP=E.EMP_CODIGO";
          $sql.=" WHERE (AGE_CODEMP=".$lote[0]->codemp.")";
          //$sql.="   AND ((AGE_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')))";                 
		  //file_put_contents("aaa.xml",$sql);
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
          $vldCampo = new validaCampo("VAGENDA",0);
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
        // Atualizando o agenda de dados se opcao de insert/updade/delete //
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
    <title>Agenda</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script language="javascript" type="text/javascript"></script>
    <style>
      .comboSobreTable {
        position:relative;
        float:left;
        display:block;
        overflow-x:auto;
        width:103em;
        height:5em;
        border:1px solid silver;
        border-radius: 6px 6px 6px 6px;
        background-color:white;        
      }
      .botaoSobreTable {
        width:6em;
        margin-left:0.2em;
        margin-top:0.3em;
        height:3.05em;
        border-radius: 4px 4px 4px 4px;
      }
    </style>  
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        //////////////////////////////////
        //   Objeto clsTable2017 AGENDA //
        //////////////////////////////////
        jsAge={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"AGE_CODIGO" 
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
                      ,"ajudaCampo"     : [  "Codigo do agenda. Este campo é único e deve tem o formato 9999"
                                            ,"Campo pode ser utilizado em cadastros de Usuario/Motorista"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          :"AGE_CODAT" 
                      ,"labelCol"       : "CODAT"
                      ,"obj"            : "edtCodAt"
                      ,"insUpDel"       : ["S","N","N"]
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : [ "Código ..."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "AT_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesAt"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [3,40]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "AGE_RESPONSAVEL"   
                      ,"labelCol"       : "RESPONSAVEL"
                      ,"obj"            : "edtResponsavel"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "32"
                      ,"inputDisabled"  : "true"
                      ,"newRecord"      : [jsPub[0].usr_apelido,"this","this"]
                      ,"validar"        : ["notnull"]                      
                      ,"padrao":0}
            ,{"id":5  ,"field"          :"AGE_VENCTO" 
                      ,"labelCol"       : "VENCTO"
                      ,"obj"            : "edtVencto"
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"fieldType"      : "dat"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [2,10]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|/"
                      ,"ajudaCampo"     : ["Data Vencimento."]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "AGE_DTCADASTRO"   
                      ,"labelCol"       : "DTCADASTRO"
                      ,"obj"            : "edtDtCadastro"
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "23"
                      ,"fieldType"      : "dat"
                      ,"ajudaCampo"     : ["Data de Cadastro."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "AGE_CADASTROU"   
                      ,"labelCol"       : "CADASTROU"
                      ,"obj"            : "edtCadastrou"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "32"
					  ,"inputDisabled"  : "true"
                      ,"newRecord"      : [jsPub[0].usr_apelido,"this","this"]
                      ,"validar"        : ["notnull"]                      
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "AGE_DTBAIXA"   
                      ,"labelCol"       : "DTBAIXA"
                      ,"obj"            : "edtDtBaixa"
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "18"
                      ,"fieldType"      : "dat"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|/"
                      ,"ajudaCampo"     : ["Data de Baixa."]
                      ,"importaExcel"   : "S"
                      ,"truncate"       : "S"                      
                      ,"padrao":0}
            ,{"id":9 ,"field"          : "AGE_CODEMP" 
                      ,"pk"             : "N"
                      ,"labelCol"       : "CODEMP"  
                      ,"obj"            : "edtCodEmp"  
                      ,"padrao":7}  
            ,{"id":10  ,"field"          : "EMP_APELIDO"   
                      ,"labelCol"       : "EMPRESA"
                      ,"obj"            : "edtDesEmp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["TOTALTRAC LTDA","this","this"]
                      ,"digitosMinMax"  : [3,15]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|.| "
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"ajudaCampo"     : ["Nome da Cidade."]
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
           // ,{"id":10 ,"field"          : "AGE_ATIVO"  
           //           ,"labelCol"       : "ATIVO"   
           //           ,"obj"            : "cbAtivo"    
           //           ,"tamImp"         : "10"                      
           //           ,"padrao":2}                                        
            ,{"id":11 ,"field"          : "AGE_REG"    
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
            ,{"id":13 ,"field"          : "AGE_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":14 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objAge.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"AGENDA - Detalhe do registro"
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
          ,"div"            : "frmAge"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaAge"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmAge"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblAge"              // Nome da table
          ,"prefixo"        : "age"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VAGENDA"             // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPAGENDA"           // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "AGE_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "AGE_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "AGE_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "92em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "AGENDA"              // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtCodAt"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objAge.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objAge.AjudaSisAtivo(jsAge);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objAge.detalhe();"]
                               //,["Gerar excel"                            ,"fa-file-excel-o"  ,"objAge.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objAge.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objAge.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objAge.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objAge.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objAge.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "AGENDA[32]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objAge === undefined ){  
          objAge=new clsTable2017("objAge");
        };  
        objAge.montarHtmlCE2017(jsAge); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsAge.form).style.width=jsAge.width;
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
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"         }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o"  }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"         }
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
          ,"max-width"      : "106em"                   // Tamanho máximo da table //Angelo kokiso - mudança no tamanho da tabela
          ,"width"          : "min-content"                    // Tamanho da table
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
      var objAge;                     // Obrigatório para instanciar o JS TFormaCob
      var jsAge;                      // Obj principal da classe clsTable2017
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
      var intCodDir = parseInt(jsPub[0].usr_d32);
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
        clsJs.add("rotina"      , "selectAge"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("agenda" , clsJs.fim());
        msg     = requestPedido("Trac_Agenda.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsAge.registros=objAge.addIdUnico(retPhp[0]["dados"]);
          objAge.ordenaJSon(jsAge.indiceTable,false);  
          objAge.montarBody2017();
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
            clsJs.add("titulo"      , objAge.trazCampoExcel(jsAge));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("agenda"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Agenda.php",fd); 
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
            gerarMensagemErro("AGE",retPhp[0].erro,{cabec:"Aviso"});    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
        };          
      };
      //////////////////////////////
      //  AJUDA PARA AGENDATAREFA //
      //////////////////////////////
      function atFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function atF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtVencto"
                      ,topo:100
                      ,tableBd:"AGENDATAREFA"
                      ,fieldCod:"A.AT_CODIGO"
                      ,fieldDes:"A.AT_NOME"
                      ,fieldAtv:"A.AT_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblAt"}
        );
      };
      function RetF10tblAt(arr){
        document.getElementById("edtCodAt").value  = arr[0].CODIGO;
        document.getElementById("edtDesAt").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodAt").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodAtBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
       if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtVencto"
                                  ,topo:100
                                  ,tableBd:"AGENDATAREFA"
                                  ,fieldCod:"A.AT_CODIGO"
                                  ,fieldDes:"A.AT_NOME"
                                  ,fieldAtv:"A.AT_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblAt"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO             );
          document.getElementById("edtDesAt").value  = ( ret.length == 0 ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
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
              name="frmAge" 
              id="frmAge" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Agenda<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 200px; overflow-y: auto;">
          <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCodigo" maxlength="6" type="text" disabled />
                <label class="campo_label" for="edtCodigo">CODIGO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodAt"
                                                    onBlur="CodAtBlur(this);" 
                                                    onFocus="atFocus(this);" 
                                                    onClick="atF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    maxlength="6"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodAt">CODAT:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesAt" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesAt">AG.TAREFA</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtResponsavel" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtResponsavel">RESPONSAVEL:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtVencto" type="text" OnKeyUp="mascaraData(this,event);" maxlength="10" />
                <label class="campo_label campo_required" for="edtVencto">VENCTO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtCadastrou" name="edtCadastrou" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtCadastrou">CADASTROU:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input" id="edtDtBaixa" type="text" OnKeyUp="mascaraData(this,event);" maxlength="10" />
                <label class="campo_label campo_required" for="edtDtBaixa">DTBAIXA:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesEmp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesEmp">EMPRESA:</label>
              </div>
			  <!--
             <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbAtivo">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">ATIVO</label>
              </div>
			  -->
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbReg">
                  <option value="P">PUBLICO</option>               
                </select>
                <label class="campo_label campo_required" for="cbReg">REG</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input_titulo" disabled id="edtUsuario" type="text" />
                <label class="campo_label campo_required" for="edtUsuario">USUARIO</label>
              </div>
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
                <input id="edtCodEmp" type="text" />
                <input id="edtDtCadastro" type="text" />
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