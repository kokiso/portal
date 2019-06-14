<?php
  session_start();
  if( isset($_POST["oportunidade"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php"); 

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["oportunidade"]);
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
        //    Dados para JavaScript USUARIO //
        //////////////////////////////////////
        if( $rotina=="selectOpt" ){
          $sql="SELECT O.OPT_CODIGO
                       ,O.OPT_CLIENTE
                       ,O.OPT_CODVND
                       ,FV.FVR_NOME
                       ,CONVERT(VARCHAR(10),O.OPT_DTINICIO,127) AS OPT_DTINICIO
                       ,O.OPT_PORCENTAGEM
                       ,O.OPT_CODMSG
                       ,M.MSG_NOME
                       ,CASE WHEN O.OPT_REG='P' THEN 'PUB' WHEN O.OPT_REG='S' THEN 'SIS' ELSE 'ADM' END AS OPT_REG
                       ,CASE WHEN O.OPT_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS OPT_ATIVO          
                       ,O.OPT_CODUSR
                  FROM OPORTUNIDADE O
                  LEFT OUTER JOIN MENSAGEM M ON O.OPT_CODMSG = M.MSG_CODIGO
                  LEFT OUTER JOIN USUARIOSISTEMA U ON O.OPT_CODUSR = U.US_CODIGO
                  LEFT OUTER JOIN VENDEDOR V ON O.OPT_CODVND = V.VND_CODFVR
                  LEFT OUTER JOIN FAVORECIDO FV ON FV.FVR_CODIGO = V.VND_CODFVR 
                 WHERE (O.OPT_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')";
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
          $vldCampo = new validaCampo("VOPORTUNIDADE",0);
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
    <title>Oportunidade</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaVendedorF10.js"></script>        
    <script src="tabelaTrac/f10/tabelaGrupoModeloF10.js"></script>
    <script src="tabelaTrac/f10/tabelaFavorecidoF10.js"></script>     
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        ////////////////////////////////////
        //   Objeto clsTable2017 USUARIO  //
        ////////////////////////////////////
        jsUsr={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"OPT_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"                                      
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["N","N","N"]
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"autoIncremento" : "S"
                      ,"ajudaCampo"     : [  "Codigo do Oprtunidade. Gerado pelo sistema é único e tem o formato 9999"
                                            ,"Campo deve ser utilizado no cadastro de Oportunidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "OPT_CLIENTE"   
                      ,"labelCol"       : "CLIENTE"
                      ,"obj"            : "edtCliente"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "30"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9| " 
                      ,"digitosMinMax"  : [1,63]
                      ,"ajudaCampo"     : ["Nome do cliente."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}                                    
            ,{"id":3  ,"field"          : "OPT_CODVND"   //ALTERAR PARA NOME
                      ,"labelCol"       : "CODVND"
                      ,"obj"            : "edtCodVnd"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9 "
                      ,"digitosMinMax"  : [1,6]
                      ,"ajudaCampo"     : ["Codigo do Vendedor."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "FVR_NOME" 
                      ,"insUpDel"       : ["N","N","N"]    
                      ,"labelCol"       : "VENDEDOR"
                      ,"obj"            : "edtDesVnd"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "30"
                      ,"inputDisabled"  : "true"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| " 
                      ,"digitosMinMax"  : [4,63]
                      ,"ajudaCampo"     : ["Nome do Vendedor."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0} 
            ,{"id":5  ,"field"          :"OPT_DTINICIO" 
                      ,"labelCol"       : "DATA INICIO"
                      ,"obj"            : "edtDtInicio" //Sempre nome do campo pra nao esquecer
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"fieldType"      : "dat"
                      ,"inputDisabled"  : "true"
                      //,"align"          : "center"                                      
                      //,"formato"        : ["i4"]
                      ,"newRecord"      : [jsDatas(0).retDDMMYYYY(),"this","this"]
                      ,"validar"        : ["dataValida"]
                      ,"ajudaCampo"     : [  "Data de início da oportunidade"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          :"OPT_PORCENTAGEM" 
                      ,"labelCol"       : "PORCENTAGEM"
                      ,"obj"            : "edtPorcentagem"
                      ,"tamGrd"         : "1em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [1,3]
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9|."
                      ,"newRecord"      : ["0","this","this"]
                      ,"validar"        : ["F>=0"]
                      ,"ajudaCampo"     : [ "Avaliação de andamento da oportunidade em Porcentagem (%)"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":7  ,"field"          :"OPT_CODMSG"
                      ,"tipo"           :"cb" 
                      ,"labelCol"       : "CODMSG"
                      ,"obj"            : "cbCodMsg"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["31","this","this"]
                      ,"validar"        : ["notnull"]                                                    
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "MSG_NOME"
                      ,"insUpDel"       : ["N","N","N"]    
                      ,"labelCol"       : "MENSAGEM"
                      ,"obj"            : "edtMsgStatus"
                      ,"newRecord"      : ["EM ANDAMENTO","this","this"]
                      ,"tamGrd"         : "13em"
                      ,"tamImp"         : "30"             
                      ,"padrao":0}        
            ,{"id":9  ,"field"          : "OPT_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"tamImp"         : "10"
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3} 
            ,{"id":10  ,"field"          : "OPT_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"padrao":2}              
            ,{"id":11 ,"field"          : "OPT_CODUSR" 
                      ,"labelCol"       : "CODOPT"  
                      ,"obj"            : "edtUsuario"
                      ,"inputDisabled"  : "true"  
                      ,"padrao":5}                                      
            ,{"id":12 ,"labelCol"       : "PP"        
                      ,"obj"            : "imgPP"        
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objOpt.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"400px" 
              ,"label"          :"Oportunidade - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":" Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"             }
             ,{"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"  }
            ,{"texto":" Acompanhamento" ,"name":"horFup"  ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-check"             }
            ,{"texto":" Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"      }        
            ,{"texto":" Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"            }                        
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmOpt"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaOpt"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmOpt"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblOpt"              // Nome da table
          ,"prefixo"        : "opt"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "OPORTUNIDADE"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPOPORTUNIDADE"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "OPT_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "OPT_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "OPT_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"nChecks"        : false                 // Se permite multiplos registros na grade checados
          //,"fieldCodEmp"  : "*"                   // SE EXISITIR - Nome do campo CODIGO EMPRESA na tabela BD            
          //,"fieldCodDir"  : "*"                   // SE EXISITIR - Nome do campo CODIGO DIREITO na tabela BD                        
          ,"width"          : "106em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "OPORTUNIDADE"             // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtCliente"
                              ,"edtCliente"
                              ,"btnConfirmar"]      // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"    // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "CODIGO"           // Indice inicial da table
          ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"              // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objOpt.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objOpt.AjudaSisAtivo(jsUsr);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objOpt.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objOpt.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objOpt.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objOpt.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objOpt.altRegSistema("+jsPub[0].usr_d41+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objOpt.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "OPORTUNIDADE[41]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objOpt === undefined ){  
          objOpt=new clsTable2017("objOpt");
        };  
        objOpt.montarHtmlCE2017(jsUsr); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsUsr.form).style.width=jsUsr.width;
        //
        //
        ////////////////////////////////////////////////////////
        // Montando a table para importar xls                 //
        // Sequencia obrigatoria em Ajuda pada campos padrões //
        ////////////////////////////////////////////////////////
        fncExcel("divExcel",{widthDivE:"100em"});
        jsExc={
          "titulo":[
             {"id":0  ,"field":"OPT_CODIGO"     ,"labelCol":"CODIGO"    ,"tamGrd":"6em"  ,"tamImp":"20","align":"center"}
            ,{"id":1  ,"field":"OPT_CLIENTE"        ,"labelCol":"CLIENTE"       ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":2  ,"field":"OPT_CODVND"    ,"labelCol":"CODVND"   ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":3  ,"field":"OPT_DTINICIO"     ,"labelCol":"INICIO" ,"tamGrd":"10em"  ,"tamImp":"20"}
            ,{"id":4  ,"field":"OPT_PORCENTAGEM"      ,"labelCol":"PORCENTAGEM"     ,"tamGrd":"3em" ,"tamImp":"10"}            
            ,{"id":5  ,"field":"OPT_CODMSG"      ,"labelCol":"CODMSG"     ,"tamGrd":"40em" ,"tamImp":"80"}
            ,{"id":6  ,"field":"OPT_REG"      ,"labelCol":"REG"     ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":7  ,"field":"OPT_ATIVO"     ,"labelCol":"ATIVO"    ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":8  ,"field":"ERRO"       ,"labelCol":"ERRO"      ,"tamGrd":"60em" ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[8].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"      
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                                
          ,"div"            : "frmExc"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaExc"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmExc"              // Onde vai ser gerado o fieldSet                     
          ,"divModal"       : "divTopoInicioE"      // Nome da div que vai fazer o show modal
          ,"tbl"            : "tblExc"              // Nome da table
          ,"prefixo"        : "exc"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                   // Nome da tabela no banco de dados  
          ,"width"          : "100em"               // Tamanho da table
          ,"height"         : "48em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "Importação Oportunidade"  // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"indiceTable"    : "OPT"                 // Indice inicial da table
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"formName"       : "frmExc"              // Nome do formulario para opção de impressão 
          ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
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
      var objOpt;                     // Obrigatório para instanciar o JS Usuario
      var jsUsr;                      // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objVndF10;                  // Obrigatório para instanciar o JS VendedorF10  
      /*
      var objUpF10;                   // Obrigatório para instanciar o JS UsuarioPerfilF10          
      var objCrgF10;                  // Obrigatório para instanciar o JS CargoF10          
      */
      var objPadF10;                  // Obrigatório para instanciar o JS UsuarioPerfilF10 e CargoF10
      var chkds;                      // Guarda todos registros checados na table 
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario       
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp;                      // Para enviar dados para o Php            
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d01);
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
        clsJs.add("rotina"      , "selectOpt"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        fd = new FormData();
        fd.append("oportunidade" , clsJs.fim());
        msg     = requestPedido("Trac_Oportunidade.php",fd);
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsUsr.registros=objOpt.addIdUnico(retPhp[0]["dados"]);
          objOpt.ordenaJSon(jsUsr.indiceTable,false);  
          objOpt.montarBody2017();
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
            clsJs.add("titulo"      , objOpt.trazCampoExcel(jsUsr)  );    
            envPhp=clsJs.fim(); 
            fd = new FormData();
            fd.append("usuario" , envPhp              );
            fd.append("arquivo" , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Oportunidade.php",fd); 
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

      ///////////////////////////////
      //     AJUDA PARA VENDEDOR   //
      ///////////////////////////////
      function vndFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function vndF10Click(obj){ 
        fVendedorF10(0,obj.id,"cbCodMsg",100,{tamColNome:"29.5em",ativo:"S", gpfvr:'cliente' } ); 
      };
      function RetF10tblVnd(arr){
        document.getElementById("edtCodVnd").value  = arr[0].CODIGO;
        document.getElementById("edtDesVnd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodLgn").value  = arr[0].CODLGN;
        document.getElementById("edtCodVnd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codVndBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fVendedorF10(1,obj.id,"cbCodMsg",100,
            {codfvr  : elNew
             ,ativo  : "S"
             , gpfvr:'cliente'} 
            ); 
          document.getElementById(obj.id).value       = ( arr.length == 0 ? "0000"  : jsNmrs(arr[0].CODIGO).emZero(4).ret() );  
          document.getElementById("edtDesVnd").value  = ( arr.length == 0 ? ""      : arr[0].DESCRICAO                      );
          document.getElementById("edtCodLgn").value  = ( arr.length == 0 ? "0"     : arr[0].CODLGN                         );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )         );
        };
      };

      function horFupClick(nChecks = false){
        try{
          chkds=objOpt.gerarJson("1").gerar();
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("optCodigo"       , parseInt(chkds[0].CODIGO) );       
          clsJs.add("nChecks"       , nChecks                   );          
          /////////////////////////////////////////////////////////////////
          // Passando as colunas que vou precisas para atualizar esta table
          /////////////////////////////////////////////////////////////////
          for(let key in objCol) { 
            clsJs.add(key, parseInt(objCol[key]) );          
          };            
          objEnvio=clsJs.fim();
          localStorage.setItem("addInd",objEnvio);
          window.open("Trac_FupOportunidade.php");
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Aviso"});  
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
              name="frmOpt" 
              id="frmOpt" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Oportunidade<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 22em; overflow-y: auto;">
            <div class="campotexto campo100">

              <div class="campotexto campo25">
                <input class="campo_input" id="edtCodigo" type="text" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input_titulo" id="edtDtInicio" 
                                                  type="text" 
                                                  maxlength="10" />
                <label class="campo_label campo_required" for="edtDtInicio">DATA</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input" id="edtCliente" type="text" />
                <label class="campo_label campo_required" for="edtCliente">Nome do Cliente</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodVnd"
                                                    onBlur="codVndBlur(this);" 
                                                    onFocus="vndFocus(this);" 
                                                    onClick="vndF10Click(this);"
                                                    data-oldvalue=""
                                                    autocomplete="off"
                                                    data-newrecord="0000"
                                                    maxlength="4"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodVnd">VENDEDOR:</label>
              </div>
              <div class="campotexto campo50">
                <input class="campo_input_titulo input" id="edtDesVnd" type="text"/>
                <label class="campo_label" for="edtDesVnd">VENDEDOR_NOME:</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbCodMsg">
                  <option value=31>EM ANDAMENTO</option>
                  <option value=32>APROVADO</option>
                  <option value=33>RECUSADO</option>
                </select>
                <label class="campo_label campo_required" for="cbCodMsg">SITUACAO</label>
              </div>
              <div class="inactive">
                <input id="edtMsgStatus" value="a" type="text" />
              </div>
              <div class="campotexto campo10">
                <input class="campo_input_titulo" id="edtPorcentagem" 
                                                  data-newrecord="0"
                                                  type="text" 
                                                  maxlength="10" disabled />
                <label class="campo_label campo_required" for="edtPorcentagem">Porcentagem</label>
              </div>
              <div class="campotexto campo20">
                <select class="campo_input_combo" id="cbAtivo">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">ATIVO</label>
              </div>
              <div class="campotexto campo25">
                <select class="campo_input_combo" id="cbReg">
                  <option value="P">PUBLICO</option>               
                </select>
                <label class="campo_label campo_required" for="cbReg">REG</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input_titulo" disabled name="edtUsuario" id="edtUsuario" type="text" />
                <label class="campo_label campo_required" for="edtUsuario">USUARIO</label>
              </div>
              <div id="btnConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
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