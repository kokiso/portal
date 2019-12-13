<?php
  session_start();
  if( isset($_POST["usuario"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php"); 

      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["usuario"]);
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
        if( $rotina=="selectUsr" ){
          $sql="SELECT A.USR_CODIGO
                       ,A.USR_CPF
                       ,A.USR_APELIDO
                       ,A.USR_CODUP
                       ,P.UP_NOME
                       ,A.USR_CODCRG
                       ,C.CRG_NOME
                       ,A.USR_EMAIL
                       ,A.USR_SENHA
                       ,CASE WHEN A.USR_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS USR_ATIVO
                       ,CASE WHEN A.USR_REG='P' THEN 'PUB' WHEN A.USR_REG='S' THEN 'SIS' ELSE 'ADM' END AS USR_REG
                       ,U.US_APELIDO
                       ,CASE WHEN A.USR_ADMPUB='P' THEN 'PUB' WHEN A.USR_ADMPUB='A' THEN 'ADM' WHEN A.USR_ADMPUB='S' THEN 'SIS' END AS USR_ADMPUB
                       ,CASE WHEN A.USR_FECHAMENTO='S' THEN 'SIM' ELSE 'NAO' END AS USR_FECHAMENTO                       
                       ,A.USR_CODUSR
                  FROM USUARIO A
                  LEFT OUTER JOIN USUARIOSISTEMA U ON A.USR_CODUSR=U.US_CODIGO
                  LEFT OUTER JOIN USUARIOPERFIL P ON A.USR_CODUP=P.UP_CODIGO                  
                  LEFT OUTER JOIN CARGO C ON A.USR_CODCRG=C.CRG_CODIGO                                    
                 WHERE (A.USR_ATIVO='".$lote[0]->ativo."') OR ('*'='".$lote[0]->ativo."')"; 
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
          $vldCampo = new validaCampo("VUSUARIO",0);
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
    <title>Usuário</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
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
        ////////////////////////////////////
        //   Objeto clsTable2017 USUARIO  //
        ////////////////////////////////////
        jsUsr={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"USR_CODIGO" 
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodigo"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"                                      
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"autoIncremento" : "S"
                      ,"ajudaCampo"     : [  "Codigo do Usuario. Gerado pelo sistema é único e tem o formato 9999"
                                            ,"Campo deve ser utilizado no cadastro de Cliente/Operacao"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "USR_CPF"   
                      ,"labelCol"       : "CPF"
                      ,"obj"            : "edtCpf"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"digitosMinMax"  : [11,11]
                      ,"ajudaCampo"     : ["CPF do usuário."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "USR_APELIDO"   
                      ,"labelCol"       : "APELIDO"
                      ,"obj"            : "edtApelido"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z| "
                      ,"digitosMinMax"  : [5,15]
                      ,"ajudaCampo"     : ["Nome do usuário."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":4  ,"field"          :"USR_CODUP" 
                      ,"labelCol"       : "CODPER"
                      ,"obj"            : "edtCodUp"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"                                      
                      ,"newRecord"      : ["0000","this","this"]
                      ,"formato"        : ["i4"]
                      ,"validar"        : ["intMaiorZero"]
                      ,"ajudaCampo"     : [  "Codigo do Perfil. Registro deve existir na tabela de perfil e tem o formato 9999"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "UP_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "PERFIL"
                      ,"obj"            : "edtDesUp"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [1,15]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do perfil para este usuário."]
                      ,"padrao":0}
            ,{"id":6  ,"field"          :"USR_CODCRG" 
                      ,"labelCol"       : "CODCRG"
                      ,"obj"            : "edtCodCrg"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"digitosMinMax"  : [2,3]
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z"
                      ,"newRecord"      : ["","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"ajudaCampo"     : [  "Codigo do Cargo. Registro deve existir na tabela de cargo e tem o formato AAAAA"
                                            ,"A checagem de cada rotina é relacionada com esta informação"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "CRG_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "CARGO"
                      ,"obj"            : "edtDesCrg"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"digitosMinMax"  : [1,20]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do cargo para este usuário."]
                      ,"padrao":0}
            ,{"id":8  ,"field"          : "USR_EMAIL"   
                      ,"labelCol"       : "EMAIL"
                      ,"obj"            : "edtEmail"
                      ,"tamGrd"         : "26em"
                      ,"formato"        : ["removeacentos","lowercase","tiraaspas","alltrim"]
                      ,"tamImp"         : "60"
                      ,"digitosValidos" : "a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|0|1|2|3|4|5|6|7|8|9|@|_|.|-"
                      ,"digitosMinMax"  : [5,60]
                      ,"ajudaCampo"     : ["Email do usuário para envio automatico de mensagens."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":9  ,"field"          : "USR_SENHA"   
                      ,"labelCol"       : "SENHA"
                      ,"obj"            : "edtSenha"
                      ,"digitosValidos" : "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|"
                      ,"digitosMinMax"  : [5,15]
                      ,"ajudaCampo"     : [ "Senha do usuario com no maximo 15 caracteres."
                                           ,"Após cadastro deve ser informado ao usuário para alterar esta informação"]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":10 ,"field"          : "USR_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"
                      ,"tamImp"         : "10"
                      ,"padrao":2}                                        
            ,{"id":11 ,"field"          : "USR_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"tamImp"         : "10"
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":12 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario" 
                      ,"padrao":4} 
            ,{"id":13 ,"field"          : "USR_ADMPUB"    
                      ,"labelCol"       : "PA"     
                      ,"obj"            : "cbAdmPub"  
                      ,"newRecord"      : ["P","this","this"]
                      ,"tamGrd"         : "4em"                      
                      ,"tamImp"         : "10"
                      ,"lblDetalhe"     : "PUBLICO/ADMIN" 
                      ,"newRecord"      : ["P","this","this"]                      
                      ,"ajudaCampo"     : [  "Flag para descrever se usuário tem direito Público ou de Administrador"
                                            ,"Este flag esta relacionado com todos os registros <b>individuais</b> do sistema"]
                      ,"ajudaDetalhe"   : "Se o usuario é PUBlico/ADMinistrador"       
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":3}  
            ,{"id":14 ,"field"          : "USR_FECHAMENTO"   
                      ,"labelCol"       : "FEC"
                      ,"obj"            : "cbFechamento"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"tamGrd"         : "3em"
                      ,"align"          : "center"
                      ,"tamImp"         : "10"
                      ,"tipo"           : "cb"
                      ,"newRecord"      : ["N","this","this"]
                      ,"ajudaCampo"     : ["Direito para opção..."]
                      ,"importaExcel"   : "S"                                          
                      ,"padrao":0}
            ,{"id":15 ,"field"          : "USR_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":16 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP"        
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objUsr.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"400px" 
              ,"label"          :"USUARIO - Detalhe do registro"
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
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmUsr"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaUsr"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmUsr"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblUsr"              // Nome da table
          ,"prefixo"        : "usr"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VUSUARIO"            // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPUSUARIO"          // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "USR_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "USR_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "USR_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          //,"fieldCodEmp"  : "*"                   // SE EXISITIR - Nome do campo CODIGO EMPRESA na tabela BD            
          //,"fieldCodDir"  : "*"                   // SE EXISITIR - Nome do campo CODIGO DIREITO na tabela BD                        
          ,"width"          : "106em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "USUARIO"             // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["edtCpf"
                              ,"edtCpf"
                              ,"btnConfirmar"]      // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"    // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "DESCRICAO"           // Indice inicial da table
          ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"              // Caption para menu table 
          ,"_menuTable"     :[
                                ["Regitros ativos"                        ,"fa-thumbs-o-up"   ,"btnFiltrarClick('S');"]
                               ,["Registros inativos"                     ,"fa-thumbs-o-down" ,"btnFiltrarClick('N');"]
                               ,["Todos"                                  ,"fa-folder-open"   ,"btnFiltrarClick('*');"]
                               ,["Importar planilha excel"                ,"fa-file-excel-o"  ,"fExcel()"]
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objUsr.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objUsr.AjudaSisAtivo(jsUsr);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objUsr.detalhe();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objUsr.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objUsr.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objUsr.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objUsr.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objUsr.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "USUARIOS[01]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objUsr === undefined ){  
          objUsr=new clsTable2017("objUsr");
        };  
        objUsr.montarHtmlCE2017(jsUsr); 
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
             {"id":0  ,"field":"CODIGO"      ,"labelCol":"CODIGO"    ,"tamGrd":"6em"  ,"tamImp":"20","align":"center"}
            ,{"id":1  ,"field":"CLIENTE"     ,"labelCol":"CLIENTE"       ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":2  ,"field":"VENDEDOR"    ,"labelCol":"VENDEDOR"   ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":3  ,"field":"DTINICIO"    ,"labelCol":"DTINICIO" ,"tamGrd":"2em"  ,"tamImp":"10"}
            ,{"id":4  ,"field":"PORCENTAGEM" ,"labelCol":"PORCENTAGEM"     ,"tamGrd":"10em" ,"tamImp":"30"}            
            ,{"id":5  ,"field":"MENSAGEM"    ,"labelCol":"MENSAGEM"     ,"tamGrd":"40em" ,"tamImp":"80"}
            ,{"id":6  ,"field":"ADMPUB"      ,"labelCol":"ADMPUB"    ,"tamGrd":"10em" ,"tamImp":"30"}
            ,{"id":7  ,"field":"ERRO"        ,"labelCol":"ERRO"      ,"tamGrd":"60em" ,"tamImp":"100"}
          ]
          ,"botoesH":[
             {"texto":"Imprimir"  ,"name":"excImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"        }        
            ,{"texto":"Excel"     ,"name":"excExcel"      ,"onClick":"5"  ,"enabled":false,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Fechar"    ,"name":"excFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"        }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[7].innerHTML !='OK') {ceTr.style.color='yellow';ceTr.style.backgroundColor='red';}"   
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
          ,"indiceTable"    : "TAG"                 // Indice inicial da table
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
      var objUsr;                     // Obrigatório para instanciar o JS Usuario
      var jsUsr;                      // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      /*
      var objUpF10;                   // Obrigatório para instanciar o JS UsuarioPerfilF10          
      var objCrgF10;                  // Obrigatório para instanciar o JS CargoF10          
      */
      var objPadF10;                  // Obrigatório para instanciar o JS UsuarioPerfilF10 e CargoF10  
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php            
      var retPhp                      // Retorno do Php para a rotina chamadora
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
        clsJs.add("rotina"      , "selectUsr"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        fd = new FormData();
        fd.append("usuario" , clsJs.fim());
        msg     = requestPedido("Trac_Usuario.php",fd);
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsUsr.registros=objUsr.addIdUnico(retPhp[0]["dados"]);
          objUsr.ordenaJSon(jsUsr.indiceTable,false);  
          objUsr.montarBody2017();
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
            clsJs.add("titulo"      , objUsr.trazCampoExcel(jsUsr)  );    
            envPhp=clsJs.fim(); 
            fd = new FormData();
            fd.append("usuario" , envPhp              );
            fd.append("arquivo" , edtArquivo.files[0] );
            msg     = requestPedido("Trac_Usuario.php",fd); 
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
      //////////////////////////////
      // AJUDA PARA USUARIOPERFIL //
      //////////////////////////////
      function upFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function upF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodCrg"
                      ,topo:100
                      ,tableBd:"USUARIOPERFIL"
                      ,fieldCod:"A.UP_CODIGO"
                      ,fieldDes:"A.UP_NOME"
                      ,fieldAtv:"A.UP_ATIVO"
                      ,typeCod :"int" }
        );
      };  
      function RetF10tblPad(arr){
        document.getElementById("edtCodUp").value     = arr[0].CODIGO;
        document.getElementById("edtDesUp").value    = arr[0].DESCRICAO;
        document.getElementById("edtCodUp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codUpBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret =fPadraoF10( { opc:1
                                ,edtCod:obj.id
                                ,foco:"edtCodCrg"
                                ,topo:100
                                ,tableBd:"USUARIOPERFIL"
                                ,fieldCod:"A.UP_CODIGO"
                                ,fieldDes:"A.UP_NOME"
                                ,fieldAtv:"A.UP_ATIVO"
                                ,typeCod :"int" }
          );
          document.getElementById(obj.id).value         = ( ret.length == 0 ? "0000"    : jsNmrs(ret[0].CODIGO).emZero(4).ret() );
          document.getElementById("edtDesUp").value     = ( ret.length == 0 ? ""        : ret[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO )             );
        };
      };
      ////////////////////////
      //  AJUDA PARA CARGO  //
      ////////////////////////
      function crgFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function crgF10Click(obj){ 
        //fCargoF10(0,obj.id,"cbAdmPub",100); 
        fPadraoF10( { opc       : 0
                      ,edtCod   : obj.id
                      ,foco     : "cbAdmPub"
                      ,topo     : 100
                      ,tableBd  : "CARGO"
                      ,fieldCod : "A.CRG_CODIGO"
                      ,fieldDes : "A.CRG_NOME"
                      ,fieldAtv : "A.CRG_ATIVO"
                      ,typeCod  : "str" 
                      ,tbl      : "tblCrg" }
        );
      };  
      function RetF10tblCrg(arr){
        document.getElementById("edtCodCrg").value    = arr[0].CODIGO;
        document.getElementById("edtDesCrg").value    = arr[0].DESCRICAO;
        document.getElementById("edtCodCrg").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codCrgBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.value;
        if( elOld != elNew ){
          //var ret = fCargoF10(1,obj.id,"cbAdmPub");
          var ret =fPadraoF10( { opc      : 1
                                ,edtCod   : obj.id
                                ,foco     : "cbAdmPub"
                                ,topo     : 100
                                ,tableBd  : "CARGO"
                                ,fieldCod : "A.CRG_CODIGO"
                                ,fieldDes : "A.CRG_NOME"
                                ,fieldAtv : "A.CRG_ATIVO"
                                ,typeCod  : "str" 
                                ,tbl      : "tblCrg" }
          );
          document.getElementById(obj.id).value          = ( ret.length == 0 ? ""    : ret[0].CODIGO            );
          document.getElementById("edtDesCrg").value     = ( ret.length == 0 ? ""    : ret[0].DESCRICAO         );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO ) );
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
              name="frmUsr" 
              id="frmUsr" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Usuario<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 22em; overflow-y: auto;">
            <div class="campotexto campo100">
              <div class="campotexto campo25">
                <input class="campo_input" id="edtCodigo" type="text" />
                <label class="campo_label campo_required" for="edtCodigo">CODIGO</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtCpf" type="text" maxlength="11" />
                <label class="campo_label campo_required" for="edtCpf">CPF</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtApelido" type="text" maxlength="15" />
                <label class="campo_label campo_required" for="edtApelido">Apelido</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input" id="edtSenha" type="password" maxlength="15" />
                <label class="campo_label campo_required" for="edtSenha">Senha</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodUp"
                                                    onBlur="codUpBlur(this);" 
                                                    onFocus="upFocus(this);" 
                                                    onClick="upF10Click(this);"
                                                    data-oldvalue="0000" 
                                                    autocomplete="off"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodUp">PERFIL:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesUp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesUp">NOME_PERFIL</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input inputF10" id="edtCodCrg"
                                                    onBlur="codCrgBlur(this);" 
                                                    onFocus="crgFocus(this);" 
                                                    onClick="crgF10Click(this);"
                                                    data-oldvalue="" 
                                                    autocomplete="off"
                                                    maxlength="3"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodCrg">CARGO:</label>
              </div>
              <div class="campotexto campo35">
                <input class="campo_input_titulo input" id="edtDesCrg" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCrg">NOME_CARGO</label>
              </div>
              <div class="campotexto campo25">
                <select class="campo_input_combo" id="cbAdmPub">
                  <option value="P">PUBLICO</option>
                  <option value="A">ADMINISTRADOR</option>
                </select>
                <label class="campo_label campo_required" for="cbAdmPub">PUB/ADM</label>
              </div>
              <div class="campotexto campo25">
                <select class="campo_input_combo" id="cbFechamento">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbFechamento">FECHAMENTO:</label>
              </div>
              
              <div class="campotexto campo50">
                <input class="campo_input" id="edtEmail" type="text" 
                                                         autocomplete="off"                
                                                         maxlength="60" />
                <label class="campo_label campo_required" for="edtEmail">Email</label>
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
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
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