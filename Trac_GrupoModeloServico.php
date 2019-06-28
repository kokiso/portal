<?php
  session_start();
  if( isset($_POST["grupomodeloservico"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["grupomodeloservico"]);
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
        ////////////////////////////////////////////////////
        //    Dados para JavaScript GRUPOMODELOSERVICO    //
        ////////////////////////////////////////////////////
        if( $rotina=="selectGms" ){
          $sql="";
          $sql.="SELECT A.GMS_CODGM";
          $sql.="       ,GM.GM_NOME";
          $sql.="       ,A.GMS_CODSRV";
          $sql.="       ,SRV.SRV_NOME";
          $sql.="       ,CASE WHEN A.GMS_VENDALOCACAO='V' THEN 'VENDA' ELSE 'LOCACAO' END AS GMS_VENDALOCACAO";          
          $sql.="       ,CASE WHEN A.GMS_MENSAL='S' THEN 'SIM' ELSE 'NAO' END AS GMS_MENSAL";
          $sql.="       ,CASE WHEN A.GMS_OBRIGATORIO='S' THEN 'SIM' ELSE 'NAO' END AS GMS_MENSAL";          
          $sql.="       ,CASE WHEN A.GMS_REFINSTALACAO='S' THEN 'SIM' ELSE 'NAO' END AS GMS_MENSAL";                    
          $sql.="       ,A.GMS_VALOR";
          $sql.="       ,CASE WHEN A.GMS_ATIVO='S' THEN 'SIM' ELSE 'NAO' END AS GMS_ATIVO";
          $sql.="       ,CASE WHEN A.GMS_REG='P' THEN 'PUB' WHEN A.GMS_REG='S' THEN 'SIS' ELSE 'ADM' END AS GMS_REG";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.GMS_CODUSR";
          $sql.="  FROM GRUPOMODELOSERVICO A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.GMS_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOMODELO GM ON A.GMS_CODGM=GM.GM_CODIGO AND GM.GM_ATIVO='S'";    
          $sql.="  LEFT OUTER JOIN SERVICO SRV ON A.GMS_CODSRV=SRV.SRV_CODIGO AND SRV.SRV_ATIVO='S'";    
          $sql.="  WHERE GMS_CODGM=".$lote[0]->codgm;
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
          $vldCampo = new validaCampo("VGRUPOMODELOSERVICO",0);
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
    <title>Auto/Servico</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <!--<link rel="stylesheet" href="css/cssFaTable.css">-->
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaServicoF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaGrupoModeloF10.js"></script>            
    <!--<script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>-->
    <script language="javascript" type="text/javascript"></script>
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        ////////////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_AutoModelo.php
        ////////////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("frameComplementar")).lote[0];
        //localStorage.removeItem("addInd");
        //////////////////////////////////////////////////
        //   Objeto clsTable2017 GRUPOMODELOSERVICO     //
        //////////////////////////////////////////////////
        jsGms={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"GMS_CODGM" 
                      ,"labelCol"       : "AUTO"
                      ,"obj"            : "edtCodGm"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "15"
                      ,"pk"             : "S"
                      ,"newRecord"      : [pega.codam,"this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [1,4] 
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      //,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"inputDisabled"  : "true"
                      ,"importaExcel"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "GM_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "AUTO_NOME"
                      ,"obj"            : "edtDesGm"
                      ,"tamGrd"         : "29em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : [pega.desam,"this","this"]
                      ,"digitosMinMax"  : [1,30]
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do auto."]
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"GMS_CODSRV" 
                      ,"labelCol"       : "SERVICO"
                      ,"obj"            : "edtCodSrv"
                      ,"fieldType"      : "int" 
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "15"
                      ,"pk"             : "S"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"insUpDel"       : ["S","N","N"]
                      ,"digitosMinMax"  : [1,4] 
                      ,"formato"        : ["i4"]                                            
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"formato"        : ["uppercase","removeacentos","tiraaspas","alltrim"]                      
                      ,"validar"        : ["notnull"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "SRV_NOME"   
                      ,"insUpDel"       : ["N","N","N"]
                      ,"labelCol"       : "SERVICO_NOME"
                      ,"obj"            : "edtDesSrv"
                      ,"tamGrd"         : "29em"
                      ,"tamImp"         : "0"
                      ,"newRecord"      : ["","this","this"]
                      ,"digitosMinMax"  : [1,50]  // Angelo Kokiso aumento da range de valor máximo do nome
                      ,"validar"        : ["notnull"]                      
                      ,"ajudaCampo"     : ["Descrição do auto."]
                      ,"padrao":0}
            ,{"id":5  ,"field"          : "GMS_VENDALOCACAO"  
                      ,"labelCol"       : "VENDALOC" 
                      ,"obj"            : "cbVendaLocacao"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["V","this","this"]
                      ,"insUpDel"       : ["S","N","N"]                      
                      ,"pk"             : "S"                      
                      //,"funcCor"        : "(objCell.innerHTML=='LOCACAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro se o produto pode ser vendido ou locado"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}      
            ,{"id":6  ,"field"          : "GMS_MENSAL"  
                      ,"labelCol"       : "MENSAL" 
                      ,"obj"            : "cbMensal"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      //,"funcCor"        : "(objCell.innerHTML=='LOCACAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro se o produto pode ser vendido ou locado"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}      
            ,{"id":7  ,"field"          : "GMS_OBRIGATORIO"  
                      ,"labelCol"       : "OBRIGATORIO" 
                      ,"obj"            : "cbObrigatorio"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      //,"funcCor"        : "(objCell.innerHTML=='LOCACAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parametro se o produto pode ser vendido ou locado"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}      
            ,{"id":8  ,"field"          : "GMS_REFINSTALACAO"  
                      ,"labelCol"       : "INSTAL" 
                      ,"obj"            : "cbRefInstalacao"
                      ,"tipo"           : "cb"                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"                      
                      ,"fieldType"      : "str"
                      ,"newRecord"      : ["S","this","this"]
                      //,"funcCor"        : "(objCell.innerHTML=='LOCACAO'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"align"          : "center"                      
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,1]
                      ,"ajudaCampo"     : [ "Parmetro para ver se instalcao propria ou terceiros"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}      
            ,{"id":9  ,"field"          : "GMS_VALOR"  
                      ,"labelCol"       : "VALOR" 
                      ,"newRecord"      : ["0,00","this","this"]
                      ,"obj"            : "edtValor"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "0"                      
                      ,"fieldType"      : "flo2"
                      ,"validar"        : ["F>0"]
                      ,"digitosMinMax"  : [1,15]
                      ,"ajudaCampo"     : [ "Valor do servico"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}                                                  
            ,{"id":10 ,"field"          : "GMS_ATIVO"  
                      ,"labelCol"       : "ATIVO"   
                      ,"obj"            : "cbAtivo"    
                      ,"padrao":2}                                        
            ,{"id":11 ,"field"          : "GMS_REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":12 ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"tamImp"         : "0"                      
                      ,"padrao":4}                
            ,{"id":13 ,"field"          : "GMS_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
            ,{"id":14 ,"labelCol"       : "PP"      
                      ,"obj"            : "imgPP" 
                      ,"func":"var elTr=this.parentNode.parentNode;"
                        +"elTr.cells[0].childNodes[0].checked=true;"
                        +"objGms.espiao();"
                        +"elTr.cells[0].childNodes[0].checked=false;"
                      ,"padrao":8}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"AUTO/SERVICO - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Cadastrar" ,"name":"horCadastrar"  ,"onClick":"0"  ,"enabled":true ,"imagem":"fa fa-plus"             }
            ,{"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"  }
            ,{"texto":"Excluir"   ,"name":"horExcluir"    ,"onClick":"2"  ,"enabled":true ,"imagem":"fa fa-minus"            }
            ,{"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"      }        
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"            }                        
            ,{"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"            }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmGms"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaGms"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmGms"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblGms"              // Nome da table
          ,"prefixo"        : "Gms"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VGRUPOMODELOSERVICO"   // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPGRUPOMODELOSERVICO" // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "GMS_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "GMS_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "GMS_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "105em"                // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "AUTO/SERVICO"        // Titulo do relatório
          ,"relOrientacao"  : "R"                   // Paisagem ou retrato
          ,"relFonte"       : "8"                   // Fonte do relatório
          ,"foco"           : ["cbVendaLocacao"
                              ,"cbMensal"
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
                               ,["Modelo planilha excel"                  ,"fa-file-excel-o"  ,"objGms.modeloExcel()"]
                               ,["Ajuda para campos padrões"              ,"fa-info"          ,"objGms.AjudaSisAtivo(jsGms);"]
                               ,["Detalhe do registro"                    ,"fa-folder-o"      ,"objGms.detalhe();"]
                               //,["Gerar excel"                            ,"fa-file-excel-o"  ,"objGms.excel();"]
                               ,["Passo a passo do registro"              ,"fa-binoculars"    ,"objGms.espiao();"]
                               ,["Alterar status Ativo/Inativo"           ,"fa-share"         ,"objGms.altAtivo(intCodDir);"]
                               ,["Alterar registro PUBlico/ADMinistrador" ,"fa-reply"         ,"objGms.altPubAdm(intCodDir,jsPub[0].usr_admpub);"]
                               ,["Alterar para registro do SISTEMA"       ,"fa-reply"         ,"objGms.altRegSistema("+jsPub[0].usr_d31+");"]
                               ,["Número de registros em tela"            ,"fa-info"          ,"objGms.numRegistros();"]                               
                               ,["Atualizar grade consulta"               ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "GRUPOMODELOSERVICO[29]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objGms === undefined ){  
          objGms=new clsTable2017("objGms");
        };  
        objGms.montarHtmlCE2017(jsGms); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsGms.form).style.width=jsGms.width;
        //document.getElementById("divSobreTable").style.width=jsGms.width;
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
        //
        //  
        btnFiltrarClick("S");  
      });
      //
      var objGms;                     // Obrigatório para instanciar o JS TFormaCob
      var jsGms;                      // Obj principal da classe clsTable2017
      var objExc;                     // Obrigatório para instanciar o JS Importar excel
      var jsExc;                      // Obrigatório para instanciar o objeto objExc
      var objSrvF10;                  // Obrigatório para instanciar o JS VendedorF10            
      var objGmF10;                   // Obrigatório para instanciar o JS GrupoModeloF10                  
      //var objPadF10;                  // Obrigatório para instanciar o JS EstadoF10
      var pega;                       // Recuperar localStorage            
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php      
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d29);
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
        clsJs.add("rotina"      , "selectGms"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codgm"       , pega.codam          );        
        fd = new FormData();
        fd.append("grupomodeloservico" , clsJs.fim());
        msg     = requestPedido("Trac_GrupoModeloServico.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsGms.registros=objGms.addIdUnico(retPhp[0]["dados"]);
          objGms.ordenaJSon(jsGms.indiceTable,false);  
          objGms.montarBody2017();
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
            clsJs.add("titulo"      , objGms.trazCampoExcel(jsGms));    
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("grupomodeloservico"      , envPhp            );
            fd.append("arquivo"   , edtArquivo.files[0] );
            msg     = requestPedido("Trac_GrupoModeloServico.php",fd); 
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
            gerarMensagemErro("Gms",retPhp[0].erro,{cabec:"Aviso"});    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
        };          
      };
      ///////////////////////////////
      //  AJUDA PARA GRUPOMODELO   //
      ///////////////////////////////
      /*
      function gmFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function gmF10Click(obj){ 
        let whr;
        switch( document.getElementById("cbVendaLocacao").value  ){
          case "D":
          case "V": 
            whr=" {AND} (A.GM_VENDA='S') ";
            break;
          case "L": 
            whr=" {AND} (A.GM_LOCACAO='S') ";
            break;
        };
      
        fGrupoModeloF10(0,obj.id,"edtUnidades",100
          ,{tamColNome:"29.5em"
            ,ativo:"S" 
            ,where:whr
           } 
        ); 
      };
      function RetF10tblGm(arr){
        document.getElementById("edtCodGm").value  = arr[0].CODIGO;
        document.getElementById("edtDesGm").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodGm").setAttribute("data-oldvalue",arr[0].CODIGO);
        document.getElementById("edtCodGm").setAttribute("data-gmvalorvista",arr[0].VALORVISTA);
        document.getElementById("edtCodGm").setAttribute("data-gmvalorprazo",arr[0].VALORPRAZO);
        
      };
      function codGmBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fGrupoModeloF10(1,obj.id,"edtUnidades",100,
            {codfvr  : elNew
             ,ativo  : "S"} 
            ); 
          document.getElementById(obj.id).value     = ( ret.length == 0 ? "0000"  : ret[0].CODIGO                   );
          document.getElementById("edtDesGm").value = ( ret.length == 0 ? ""      : ret[0].DESCRICAO                );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO ) );
          document.getElementById("edtCodGm").setAttribute("data-gmvalorvista",ret[0].VALORVISTA              );
          document.getElementById("edtCodGm").setAttribute("data-gmvalorprazo",ret[0].VALORPRAZO              );
        };
      };
      */
      ////////////////////////////
      //  AJUDA PARA SERVICO    //
      ////////////////////////////
      function srvFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function srvF10Click(obj){ 
        fServicoF10(0,obj.id,"cbMensal",0
          ,{
            entsai      : "S"
            ,codemp     : jsPub[0].emp_codigo
          }
        ); 
      };
      function RetF10tblSrv(arr){
        document.getElementById("edtCodSrv").value      = arr[0].CODIGO;
        document.getElementById("edtDesSrv").value      = arr[0].DESCRICAO;
      };
      function codSrvBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fServicoF10(1,obj.id,"cbMensal",0
            ,{
              entsai      : "S"
              ,codemp     : jsPub[0].emp_codigo
            }
          ); 
          //  
          document.getElementById(obj.id).value           = ( arr.length == 0 ? "0000"          : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          document.getElementById("edtDesSrv").value      = ( arr.length == 0 ? "*"             : arr[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )                     );
        };
      };
      
      function horFecharClick(){
        //////////////////////////////////////////////////////////////////////////////
        // Se foi chamada do menu normal apenas fecho o formulario                  //
        // Se de outro form(Atlas_Pgr.php) fecho o complementar e ativo o principal //
        //////////////////////////////////////////////////////////////////////////////
        if( pega == null ){
          window.parent.document.getElementById("iframeCorpo").src="";
        } else {
          localStorage.removeItem("frameComplementar");
          window.parent.document.getElementById("iframeComplementar").style.display="none";
          window.parent.document.getElementById("iframeCorpo").style.display="block";
        };  
      };
      
    </script>
  </head>
  <body>
    <!--
    <div id="divSobreTable" class="divSobreTable">    
      <div class="campotexto campo25" style="margin-top:3px;margin-left:3px;">      
        <select class="campo_input_combo" id="cbCodEst">
        </select>
        <label class="campo_label campo_required" for="cbCodEst">UF</label>
      </div>
      <div class="campo10" style="float:left;margin-left:5px;">            
        <div id="btnFiltrar" onClick="btnFiltrarClick('S');" class="btnImagemEsq bie100 bieAzul bieRight" style="margin-top:3px;"><i class="fa fa-filter"> Filtrar</i></div>
      </div>
      <div class="_campotexto campo100" style="margin-top:1.7em;height:3em;">
        <label class="solicitadoFiltro"></label>
      </div>              
    </div>  
    -->
    <div class="divTelaCheia">
      <div id="divRotina" class="conteudo">  
        <div id="divTopoInicio">
        </div>
        <form method="post" 
              name="frmGms" 
              id="frmGms" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Auto/Servico<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 200px; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo20">
                <select id="cbVendaLocacao" class="campo_input_combo" >
                  <option value="V" selected >VENDA</option>            
                  <option value="L">LOCACAO</option>
                </select>
                <label class="campo_label campo_required" for="cbVendaLocacao">VENDA_LOCACAO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodGm" type="text" disabled />
                <!--
                <input class="campo_input inputF10" id="edtCodGm"
                                                    onBlur="codGmBlur(this);" 
                                                    onFocus="gmFocus(this);" 
                                                    onClick="gmF10Click(this);"
                                                    data-oldvalue=""
                                                    data-newrecord="0000"                                                
                                                    data-gmValorVista="0,00"
                                                    data-gmValorPrazo="0,00"
                                                    autocomplete="off"                                                
                                                    maxlength="6"
                                                    type="text" disabled />
                -->                                          
                <label class="campo_label" for="edtCodGm">PRODUTO:</label>
              </div>
              <div class="campotexto campo70">
                <input class="campo_input_titulo input" id="edtDesGm" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesGm">NOME_PRODUTO:</label>
              </div>
              
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodSrv"
                                                    OnKeyPress="return mascaraInteiro(event);"
                                                    onBlur="codSrvBlur(this);" 
                                                    onFocus="srvFocus(this);" 
                                                    onClick="srvF10Click(this);"
                                                    data-newrecord="0000"
                                                    data-oldvalue="0000" 
                                                    autocomplete="off"
                                                    type="text" />
                <label class="campo_label campo_required" for="edtCodSrv">SERVICO:</label>
              </div>
              <div class="campotexto campo70">
                <input class="campo_input_titulo input" id="edtDesSrv" 
                                                        data-newrecord="*"
                                                        type="text" disabled />
                <label class="campo_label campo_required" for="edtDesSrv">SERVICO_NOME</label>
              </div>
              <div class="campotexto campo20">
                <select id="cbRefInstalacao" class="campo_input_combo" >
                  <option value="N" selected >NAO</option>            
                  <option value="S">SIM</option>
                </select>
                <label class="campo_label campo_required" for="cbRefInstalacao">REF INSTALAÇÃO:</label>
              </div>
              <div class="campotexto campo15">
                <select id="cbMensal" class="campo_input_combo" >
                  <option value="S" selected >SIM</option>            
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbMensal">MENSAL:</label>
              </div>
              <div class="campotexto campo20">
                <select id="cbObrigatorio" class="campo_input_combo" >
                  <option value="S" selected >SIM</option>            
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbObrigatorio">OBRIGATORIO:</label>
              </div>
              <div class="campotexto campo15">
                <input class="campo_input_titulo edtDireita" id="edtValor" 
                                                      onBlur="fncCasaDecimal(this,2);"
                                                      data-newrecord="0,00"
                                                      maxlength="15" 
                                                      type="text" disabled />
                <label class="campo_label campo_required" for="edtValor">VALOR:</label>
              </div>
              
              <div class="campotexto campo15">
                <select class="campo_input_combo" id="cbAtivo">
                  <option value="S">SIM</option>
                  <option value="N">NAO</option>
                </select>
                <label class="campo_label campo_required" for="cbAtivo">ATIVO</label>
              </div>
              <div class="campotexto campo15">
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
              </div>
              <div id="btnConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
              <div id="btnCancelar"  class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
            </div>
          </div>
        </form>
      </div>
      <!-- Importar excel -->
      <div id="divExcel" class="divTopoExcel">
      </div>
      <!-- Fim Importar excel -->
    </div>       
  </body>
</html>