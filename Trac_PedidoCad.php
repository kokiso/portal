<?php
  session_start();
  if( isset($_POST["pedidocad"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");      
      require("classPhp/validaCampo.class.php"); 
      $vldr     = new validaJSon();          
      $retorno  = "";
      $codPdd   = 0;
      $retCls   = $vldr->validarJs($_POST["pedidocad"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $arrUpdt  = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $rotina   = $lote[0]->rotina;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        /////////////////////////////
        // Gravando no banco de dados
        /////////////////////////////
        if( $lote[0]->rotina=="cadastrar" ){
          if( $lote[0]->pedido==0 ){
            $codPdd=$classe->generator("PEDIDO");
            $sql="";
            $sql.="INSERT INTO PEDIDO(";
            $sql.="PDD_CODIGO";
            $sql.=",PDD_TIPO";            
            $sql.=",PDD_EMISSAO";
            $sql.=",PDD_CNPJCPF";
            $sql.=",PDD_NOME";
            $sql.=",PDD_CODVND";
            $sql.=",PDD_CODFVR";
            $sql.=",PDD_VALOR";
            $sql.=",PDD_QTDAUTO";
            $sql.=",PDD_QTDPLACA";            
            $sql.=",PDD_FIDELIDADE";
            $sql.=",PDD_INSTPROPRIA";
            $sql.=",PDD_STATUS";
            $sql.=",PDD_CODIND";
            $sql.=",PDD_OBS";
            $sql.=",PDD_CODLGN";                        
            $sql.=",PDD_MESES";
            $sql.=",PDD_DIA";
            $sql.=",PDD_LOCALINSTALA";            
            $sql.=",PDD_ITEM";          
            $sql.=",PDD_PARCELA";
            $sql.=",PDD_PLACA";
            $sql.=",PDD_ATIVO";            
            $sql.=",PDD_REG";
            $sql.=",PDD_CODUSR) VALUES(";
            $sql.="'$codPdd'";                      // PDD_CODIGO
            $sql.=",'".$lote[0]->tipo."'";          // PDD_TIPO
            $sql.=",'".$lote[0]->emissao."'";       // PDD_EMISSAO
            $sql.=",'".$lote[0]->cnpj."'";          // PDD_CNPJCPF
            $sql.=",'".$lote[0]->nome."'";          // PDD_NOME
            $sql.=",".$lote[0]->codvnd;             // PDD_CODVND
            $sql.=",".$lote[0]->codfvr;             // PDD_CODFVR
            $sql.=",".$lote[0]->valor;              // PDD_VALOR
            $sql.=",".$lote[0]->qtdauto;            // PDD_QTDAUTO
            $sql.=",".$lote[0]->qtdplaca;           // PDD_QTDPLACA
            $sql.=",'".$lote[0]->fidelidade."'";    // PDD_FIDELIDADE
            $sql.=",'".$lote[0]->instpropria."'";   // PDD_INSTPROPRIA
            $sql.=",".$lote[0]->status;             // PDD_STATUS
            $sql.=",".$lote[0]->codind;             // PDD_CODIND
            $sql.=",'".$lote[0]->obs."'";           // PDD_OBS
            $sql.=",'".$lote[0]->codlgn."'";        // PDD_CODLGN
            $sql.=",".$lote[0]->meses;              // PDD_MESES
            $sql.=",".$lote[0]->dia;                // PDD_DIA
            $sql.=",'".$lote[0]->localinstala."'";  // PDD_LOCALINSTALA
            $sql.=",'".$lote[0]->item."'";          // PDD_ITEM
            $sql.=",'".$lote[0]->parcela."'";       // PDD_PARCELA
            $sql.=",'".$lote[0]->placa."'";         // PDD_PLACA
            $sql.=",'S'";                           // PDD_ATIVO
            $sql.=",'P'";                           // PDD_REG
            $sql.=",".$lote[0]->codusr;             // PDD_CODUSR
            $sql.=")";    
            array_push($arrUpdt,$sql);          
            $atuBd = true;
          } else {
            $sql="";
            $sql.="UPDATE PEDIDO";
            $sql.="   SET PDD_CNPJCPF='".$lote[0]->cnpj."'";
            $sql.="       ,PDD_TIPO='".$lote[0]->tipo."'";            
            $sql.="       ,PDD_NOME='".$lote[0]->nome."'";
            $sql.="       ,PDD_CODVND=".$lote[0]->codvnd;
            $sql.="       ,PDD_CODFVR=".$lote[0]->codfvr;
            $sql.="       ,PDD_VALOR=".$lote[0]->valor;
            $sql.="       ,PDD_QTDAUTO=".$lote[0]->qtdauto;
            $sql.="       ,PDD_QTDPLACA=".$lote[0]->qtdplaca;            
            $sql.="       ,PDD_FIDELIDADE='".$lote[0]->fidelidade."'";
            $sql.="       ,PDD_STATUS=".$lote[0]->status;
            $sql.="       ,PDD_CODIND=".$lote[0]->codind;            
            $sql.="       ,PDD_OBS='".$lote[0]->obs."'";
            $sql.="       ,PDD_CODLGN='".$lote[0]->codlgn."'";                        
            $sql.="       ,PDD_DIA=".$lote[0]->dia;
            $sql.="       ,PDD_LOCALINSTALA='".$lote[0]->localinstala."'";
            $sql.="       ,PDD_ITEM='".$lote[0]->item."'";
            $sql.="       ,PDD_PARCELA='".$lote[0]->parcela."'";;
            $sql.="       ,PDD_PLACA='".$lote[0]->placa."'";
            $sql.="       ,PDD_CODUSR=".$lote[0]->codusr;
            $sql.="WHERE PDD_CODIGO=".$lote[0]->pedido;
            array_push($arrUpdt,$sql);          
            $atuBd = true;
          };  
        };  
        if( $lote[0]->rotina=="buscaCnpj" ){        
          $sql="";
          $sql.="SELECT FVR_NOME,FVR_CODIGO FROM FAVORECIDO WHERE FVR_CNPJCPF='".$lote[0]->cnpj."'";
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        if( $lote[0]->rotina=="buscaServico" ){        
          $sql="";
          $sql.="SELECT A.GMS_CODSRV,SRV.SRV_NOME,A.GMS_VALOR,A.GMS_MENSAL";
          $sql.="       ,CASE WHEN A.GMS_OBRIGATORIO='S' THEN 'SIM' ELSE 'NAO' END AS GMS_OBRIGATORIO";
          $sql.="       ,A.GMS_MENSAL AS MENSAL";          
          $sql.="  FROM GRUPOMODELOSERVICO A";
          $sql.="  LEFT OUTER JOIN SERVICO SRV ON A.GMS_CODSRV=SRV.SRV_CODIGO";
          $sql.=" WHERE GMS_CODGM='".$lote[0]->codgm."'";
          $sql.="   AND A.GMS_VENDALOCACAO='".$lote[0]->vendalocacao."'";
          $sql.="   AND A.GMS_ATIVO='S'";
          if( $lote[0]->instpropria=="S" )
            $sql.="   AND A.GMS_REFINSTALACAO='N'";  
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
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
          //////////////////////////////////////////////////////////////////////////////////////
          // Abrindo o excel e pegando as colunas obrigatorias                                //
          //////////////////////////////////////////////////////////////////////////////////////  
          $data       = [];
          $strExcel   = "S";                                                // Se S mostra na grade e importa, se N só mostra na grade    
          $dom        = DOMDocument::load($_FILES["arquivo"]["tmp_name"]);  // Abre o arquivo completo
          $rows       = $dom->getElementsByTagName("Row");                  // Retorna um array de todas as linhas
          $tamR       = $rows->length;                                      // tamanho do array rows
          //////////////////////////////////////////////////////////////////////////////////////
          // Correndo o excel                                                                 //
          // Pego o cabecalho do excel na linha 0 para comparar com o js acima campo labelCol // 
          //////////////////////////////////////////////////////////////////////////////////////
          for ($linR = 1; $linR < $tamR; $linR ++){
            $cells = $rows->item($linR)->getElementsByTagName("Cell");
            $linha   = [];
            foreach($cells as $cell){
              array_push($linha,strtoupper( $cell->nodeValue ));
            };
            array_push($data,$linha);
          };
          $retorno='[{"retorno":"OK","dados":'.json_encode($data).',"erro":"OK"}]';              
        };
        
        
        
        ///////////////////////////////////////////////////////////////////
        // Atualizando o banco de dados se opcao de insert/updade/delete //
        ///////////////////////////////////////////////////////////////////
        if( $atuBd ){
          if( count($arrUpdt) >0 ){
            $retCls=$classe->cmd($arrUpdt);
            if( $retCls['retorno']=="OK" ){
              $retorno='[{"retorno":"OK"
                         ,"dados":""
                         ,"numPdd":"PEDIDO '.str_pad($codPdd, 6, "0", STR_PAD_LEFT).' GRAVADO COM SUCESSO"
                         ,"erro":"'.count($arrUpdt).' REGISTRO(s) ATUALIZADO(s)!"}]'; 
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
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
<script language="javascript" type="text/javascript"></script>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <title>Cadastro de pedido</title>
   
    <style>
      .divAzul{
        float:left;
        border-top-width: 3px;
        border-top-style: solid;
        border-top-color: #3c8dbc;
        margin-left:2em;
        border-right: 1px solid #d2d6de;
        
      }  
      .divGrade{
        background-color:#ecf0f5;
        position:relative;
        float:left;
        display:block;
        overflow-x:auto;
        width:137em;
        height:52em;
        margin-left:2em;
        border-top: 3px solid #53868B;
        border-top-width: 3px;
        border-top-style: solid;
        border-top-color: #53868B;
        
        border-bottom: 3px solid #53868B;
        border-bottom-width: 3px;
        border-bottom-style: solid;
        border-bottom-color: #53868B;
      }  
      
    </style>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <link rel="stylesheet" href="css/Acordeon.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaVendedorF10.js"></script>        
    <script src="tabelaTrac/f10/tabelaGrupoModeloF10.js"></script>
    <script src="tabelaTrac/f10/tabelaFavorecidoF10.js"></script>        
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        
        pega=JSON.parse(localStorage.getItem("addAlt")).lote[0];
        //localStorage.removeItem("addAlt");   *****voltarrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr
        /////////////////////////////////////
        // Prototype para preencher campos //
        /////////////////////////////////////
        //document.getElementById("frmPedidoCad").newRecord("data-newrecord"); 
        $doc("frmPedidoCad").newRecord("data-newrecord"); 
        
        //document.getElementById("edtCnpjCpf").foco();
        $doc("edtCodFvr").foco();
        jsIte={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"labelCol"       : "PRODUTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"align"          : "center"                                      
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "16"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "SERVICO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"align"          : "center"                                      
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "DESCRICAO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "80"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "OBR"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "8"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "PAGTO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "15"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "PRAZO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i2"]                      
                      ,"tamGrd"         : "2em"
                      ,"align"          : "center"                                                            
                      ,"tamImp"         : "12"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "UNITARIO"            // Vlr unitario ja com o desconto
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "UNITARIOVISTA"       // Vlr unitario a vista sem o desconto
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "UNITARIOPRAZO"       // Vlr unitario a prazo sem o desconto
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "UNITARIOMINIMO"       // Vlr unitario minimo para o produto/servico
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "DESC"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "QTDADE"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i3"]                      
                      ,"align"          : "center"                                      
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "18"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "MENSAL"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "TOTAL"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"somarImp"       : "S"                      
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "FLAG"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"     //--03abr2019
                      ,"tamImp"         : "10"
                      ,"padrao":0}
            ,{"id":16 ,"labelCol"       : "PS"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"     //--03abr2019
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":17 ,"labelCol"       : "PLACA"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"                                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "18"
                      ,"padrao":0}
            ,{"id":18 ,"labelCol"       : "CODGP"
                      ,"fieldType"      : "str"
                      //,"align"          : "center"                                      
                      ,"tamGrd"         : "0em"     //--03abr2019
                      ,"tamImp"         : "18"
                      ,"padrao":0}
          ]
          ,
          "botoesH":[
             {"texto":"Parcelas"      ,"name":"pedParcela"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-calendar"      }
            ,{"texto":"Placa"         ,"name":"pedPlacaCad"   ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-truck"
                                      ,"popover":{title:"Informar placa",texto:"Selecione um auto para informação de placa(s)"}}            
            ,{"texto":"Desconto"      ,"name":"pedValorAlt"   ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-money"         
                                      ,"popover":{title:"Desconto sobre servico",texto:"Selecione um item referente servico para informação de desconto"}}               
            ,{"texto":"Excluir"       ,"name":"pedExcluir"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-minus"         }          
            ,{"texto":"Imprimir"      ,"name":"pedImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"         }                                
            ,{"texto":"Excel"         ,"name":"pedExcel"      ,"onClick":"5"  ,"enabled":true ,"imagem":"fa fa-file-excel-o"  }        
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[2].innerHTML =='0000') {ceTr.style.color='black';ceTr.style.backgroundColor='silver';}"      
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)                        
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"            // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"             // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmPed"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaPed"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmPed"                  // Onde vai ser gerado o fieldSet       
          ,"divModalDentro" : "sctnPed"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblPed"                  // Nome da table
          ,"prefixo"        : "ped"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"nChecks"        : false          
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "112em"                   // Tamanho da table
          ,"height"         : "40em"                    // Altura da table
          ,"tableLeft"      : "2px"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "PEDIDO"                  // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          //,"codDir"         : intCodDir
        }; 
        if( objIte === undefined ){  
          objIte=new clsTable2017("objIte");
        };
        objIte.montarHtmlCE2017(jsIte); 
        /////////////////////////////////////////////
        //      Objeto clsTable2017 PARCELAS       //
        /////////////////////////////////////////////
        jsPar={
          "titulo":[
             {"id":0  ,"labelCol":"OPC" 
                      ,"padrao":1}            
            ,{"id":1  ,"labelCol"   : "PARCELA"  
                      ,"fieldType"  : "int"  
                      ,"formato"    : ["i2"] 
                      ,"align"      : "center"
                      ,"tamGrd"     : "4em"
                      ,"tamImp"     : "20"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"   : "PAGTO"
                      ,"fieldType"  : "str"
                      ,"tamGrd"     : "10em"
                      ,"tamImp"     : "15"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"   : "VENCTO"
                      ,"fieldType"  : "str"            
                      ,"tamGrd"     : "10em"
                      ,"align"      : "center"                      
                      ,"tamImp"     : "10"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"   : "VALOR"
                      ,"fieldType"  : "flo2" 
                      ,"tamGrd"     : "10em"
                      ,"tamImp"     : "20"
                      ,"padrao":0}
          ]
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[2].innerHTML =='PONTUAL') {ceTr.style.color='blue';}"
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmPar"                  // Onde vai ser gerado a table
          ,"form"           : "frmPar"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divPddParcela"           // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblPar"                  // Nome da table
          ,"prefixo"        : "par"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"width"          : "48em"                    // Tamanho da table
          ,"height"         : "50em"                    // Altura da table
          ,"relTitulo"      : "PARCELA"                 // Titulo do relatório
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"indiceTable"    : "SEQ"                     // Indice inicial da table
          ,"tamBotao"       : "10"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
        }; 
        if( objPar === undefined ){  
          objPar=new clsTable2017("objPar");
        };  
        objPar.montarHtmlCE2017(jsPar); 
        
        /////////////////////////////////////////////
        //      Objeto clsTable2017 PLACA          //
        /////////////////////////////////////////////
        jsPlc={
          "titulo":[
             {"id":0  ,"labelCol":"OPC" 
                      ,"padrao":1}            
            ,{"id":1  ,"labelCol"   : "PRODUTO"  
                      ,"fieldType"  : "int"  
                      ,"formato"    : ["i2"] 
                      ,"align"      : "center"
                      ,"tamGrd"     : "5em"
                      ,"tamImp"     : "20"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"     : "PLACA"
                      ,"fieldType"    : "str"
                      ,"tamGrd"       : "20em"
                      ,"tamImp"       : "15"
                      ,"ordenaColuna" : "S"                      
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"   : "IDITEM"
                      ,"fieldType"  : "int"
                      ,"tamGrd"     : "0em"
                      ,"tamImp"     : "9"
                      ,"padrao":0}
                      
          ]
          , 
          "botoesH":[
            {"texto":"Excluir"       ,"name":"plcExcluir"        ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-minus" }             
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmPlc"                  // Onde vai ser gerado a table
          ,"form"           : "frmPlc"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divPddPlaca"             // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblPlc"                  // Nome da table
          ,"prefixo"        : "plc"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"width"          : "38em"                    // Tamanho da table
          ,"height"         : "40em"                    // Altura da table
          ,"relTitulo"      : "PLACA"                   // Titulo do relatório
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"indiceTable"    : "PLACA"                   // Indice inicial da table
          ,"tamBotao"       : "10"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
        }; 
        if( objPlc === undefined ){  
          objPlc=new clsTable2017("objPlc");
        };  
        objPlc.montarHtmlCE2017(jsPlc); 
        //  
        //
        ///////////////////////////////////////////////////////////
        // Se for alteracao preencho as grades com a ultima posicao
        ///////////////////////////////////////////////////////////
        if( pega.codpdd>0 ){
          document.getElementById("frmPedidoCad").newRecord("data-newrecord");
          document.getElementById("edtNumPedido").value     = pega.codpdd;
          document.getElementById("edtDtPedido").value      = pega.emissao;
          document.getElementById("edtCodFvr").value        = pega.codfvr;          
          document.getElementById("edtCnpjCpf").value       = pega.cnpj;
          document.getElementById("edtDesFvr").value        = pega.desfvr;
          document.getElementById("cbPrazoContrato").value  = pega.meses;
          document.getElementById("edtCodVnd").value        = pega.codvnd;
          document.getElementById("edtDesVnd").value        = pega.desvnd;
          document.getElementById("cbFidelidade").value     = pega.fidelidade;
          document.getElementById("cbInstPropria").value    = pega.instpropria;
          document.getElementById("cbStatus").value         = pega.status;
          document.getElementById("edtCodInd").value        = pega.codind;
          document.getElementById("edtDesInd").value        = pega.desind;
          document.getElementById("edtObs").value           = pega.obs;
          document.getElementById("edtCodLgn").value        = pega.codlgn;
          document.getElementById("cbDia").value            = pega.dia;
          document.getElementById("cbLocalInstala").value   = pega.localinstala;          
          /////////////////////////////////
          // Transformando JSON em um array
          /////////////////////////////////
          let item  = JSON.parse((pega.jsitem).replaceAll('|','"')).item;
          let soma  = 0;
          ///////////////////////////////////////////////////////////////////////////////////////
          // Tem que ficar na ordem das colunas - tem outro igual que eh utilizado para alteracao
          // Procurar jsIte.registros
          ///////////////////////////////////////////////////////////////////////////////////////  
          item.forEach(function(reg){
            jsIte.registros.push([              
               reg.produto          // PRODUTO
              ,reg.servico          // SERVICO  
              ,reg.descricao        // DESCRICAO
              ,reg.obr              // OBR 
              ,reg.pagto            // PAGTO 
              ,reg.prazo            // PRAZO
              ,reg.unitario         // UNITARIO COM DESCONTO    
              ,reg.unitariovista    // UNITARIO A VISTA SEM DESCONTO
              ,reg.unitarioprazo    // UNITARIO A PRAZO SEM DESCONTO              
              ,reg.unitariominimo   // UNITARIO MINIMO PARA PRODUTO/SERVICO
              ,reg.desc             // DESC
              ,reg.qtdade           // QTDADE
              ,reg.mensal           // MENSAL
              ,reg.total            // TOTAL 
              ,reg.flag             // FLAG( VENDA/LOCACAO )
              ,reg.ps               // PS
              ,reg.placa            // PLACA( Qtdade de placas informadas )
              ,reg.codgp            // CODGP
              ,pedID
            ]);
            soma+=jsNmrs(reg.total).dolar().ret();
            pedID++;
          });          
          objIte.montarBody2017();
          $doc("edtVlrPedido").value=jsNmrs(soma).dolar().sepMilhar().ret();
          $doc("edtVlrPedido").setAttribute("data-vlrdolar",jsNmrs(soma).dec(2).dolar().ret());  // Para não ter que tirar o separador de milhar
          //////////////////////
          // Pegando as parcelas
          //////////////////////  
          let vlrMensal   = 0;
          let vlrPontual  = 0;
          let parcela=JSON.parse((pega.jsparcela).replaceAll('|','"')).parcela;
          parcela.forEach(function(reg){
            jsPar.registros.push([              
              reg.parcela
              ,reg.pagto
              ,reg.vencto
              ,reg.valor
              ,parID
            ]);
            if( reg.pagto=="MENSAL" )
              vlrMensal+=jsNmrs(reg.valor).dec(2).dolar().ret();  
            if( reg.pagto=="PONTUAL" )
              vlrPontual+=jsNmrs(reg.valor).dec(2).dolar().ret();  
            parID++;
          });
          objPar.montarBody2017();
          $doc("edtVlrMensal").value=jsNmrs(vlrMensal).dolar().sepMilhar().ret();
          $doc("edtVlrPontual").value=jsNmrs(vlrPontual).dolar().sepMilhar().ret();
          $doc("edtVlrMensal").setAttribute("data-vlrdolar",jsNmrs(vlrMensal).dec(2).dolar().ret());      // Para não ter que tirar o separador de milhar
          $doc("edtVlrPontual").setAttribute("data-vlrdolar",jsNmrs(vlrPontual).dec(2).dolar().ret());    // Para não ter que tirar o separador de milhar          
          ////////////////////
          // Pegando as placas
          ////////////////////
          let placa=JSON.parse((pega.jsplaca).replaceAll('|','"')).placa;
          if( placa.length >0 ){
            placa.forEach(function(reg){
              jsPlc.registros.push([              
                reg.produto
                ,reg.placa
                ,reg.iditem
                ,plcID
              ]);
              plcID++;
            });
            objPlc.montarBody2017();
          };
          jsCmpAtivo("cbPrazoContrato").remove("campo_input").add("campo_input_titulo").disabled(true);          
        }; 
        fncColunasIte();
        fncColunasPlc();
      });
      //
      var objIte;                     // Obrigatório para instanciar o JS ITEM
      var jsIte;                      // Obj principal da classe clsTable2017
      var objPar;                     // Obrigatório para instanciar o JS PARCELAS
      var jsPar;                      // Obj principal da classe clsTable2017
      var objPlc;                     // Obrigatório para instanciar o JS PLACA
      var jsPlc;                      // Obj principal da classe clsTable2017
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var objPadF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var objVndF10;                  // Obrigatório para instanciar o JS VendedorF10            
      var objGmF10;                   // Obrigatório para instanciar o JS GrupoModeloF10   
      var objFvrF10;                  // Obrigatório para instanciar o JS FavorecidoF10                  
      var objColIte;                  // Posicao das colunas da grade ITEM que vou precisar neste formulario
      var objColPlc;                  // Posicao das colunas da grade PLACA que vou precisar neste formulario
      var pedID=1;                    // Devido exclusao na table de pedido
      var parID=1;                    // Devido exclusao na table de parcelas
      var plcID=1;                    // Devido exclusao na table de placas
      var pega;                       // Recuperar localStorage      
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      //////////////////////////////////////////////////////////////////////
      // Aqui sao as colunas da table ITEM que vou precisar neste formulario
      //////////////////////////////////////////////////////////////////////
      function fncColunasIte(){
        try{          
          let buscaCol= new clsObterColunas(jsIte,["CODGP"        ,"DESC"         ,"DESCRICAO"      ,"FLAG"         ,"MENSAL"       ,"OBR"      
                                                  ,"OPC"          ,"PAGTO"        ,"PRAZO"          ,"PRODUTO"      ,"PS"           ,"QTDADE" 
                                                  ,"SERVICO"      ,"TOTAL"        ,"UNITARIO"       ,"UNITARIOVISTA","UNITARIOPRAZO","UNITARIOMINIMO" 
                                                  ,"PLACA"        ,"_ID"]);
          buscaCol.appFilter();
          if( buscaCol.getNumCols() != 0  ){
            throw "Não localizado coluna PRODUTO,DESCRICAO,UNITARIO,QTDADE,VALOR ou FLAG!";   
          } else {
            objColIte=buscaCol.getObjeto();
          };
        } catch(e){
          gerarMensagemErro("ped",e,{cabec:"Erro"});          
        };  
      };
      ///////////////////////////////////////////////////////////////////////
      // Aqui sao as colunas da table PLACA que vou precisar neste formulario
      ///////////////////////////////////////////////////////////////////////
      function fncColunasPlc(){
        try{          
          let buscaCol= new clsObterColunas(jsPlc,["PRODUTO","PLACA"]);
          buscaCol.appFilter();
          if( buscaCol.getNumCols() != 0  ){
            throw "Não localizado coluna PRODUTO ou PLACA para table PLACA!";   
          } else {
            objColPlc=buscaCol.getObjeto();
          };
        } catch(e){
          gerarMensagemErro("ped",e,{cabec:"Erro"});          
        };  
      };
      ////////////////////////////////////////////////////////
      // Total do pedido ao incluir/excluir
      ////////////////////////////////////////////////////////
      function fncTotalPedido(){
        let tbl = tblPed.getElementsByTagName("tbody")[0];
        let nl  = tbl.rows.length;
        if( nl>0 ){
          let tot=0;
          for(let lin=0 ; (lin<nl) ; lin++){
            tot+=jsNmrs(tbl.rows[lin].cells[objColIte.TOTAL].innerHTML).dolar().ret();  
          };    
          $doc("edtVlrPedido").value=jsNmrs(tot).dolar().sepMilhar().ret();
          $doc("edtVlrPedido").setAttribute("data-vlrdolar",jsNmrs(tot).dec(2).dolar().ret());  // Para não ter que tirar o separador de milhar
        };  
      };  
      ////////////////////////////
      //  AJUDA PARA FAVORECIDO //
      ////////////////////////////
      function fvrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fvrF10Click(obj){ 
        fFavorecidoF10(0,obj.id,"edtCnpjCpf",100);         
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value  = jsNmrs(arr[0].CODIGO).emZero(4).ret();
        document.getElementById("edtCnpjCpf").value = arr[0].CNPJCPF;
        document.getElementById("edtDesFvr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodFvrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fFavorecidoF10(1,obj.id,"edtCnpjCpf",100);   
          document.getElementById(obj.id).value       = ( ret.length == 0 ? ""  : jsNmrs(ret[0].CODIGO).emZero(4).ret() );
          document.getElementById("edtCnpjCpf").value = ( ret.length == 0 ? ""  : ret[0].CNPJCPF                        );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? ""  : ret[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )         );
        };
      };
      //////////////////////////////////////////
      //  AJUDA PARA QUEM INDICOU(FAVORECIDO) //
      //////////////////////////////////////////
      function indFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function indF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      //,foco:"cbAtivo"
                      ,topo:100
                      ,tableBd:"FAVORECIDO"
                      ,fieldCod:"A.FVR_CODIGO"
                      ,fieldDes:"A.FVR_NOME"
                      ,fieldAtv:"A.FVR_ATIVO"
                      ,typeCod :"int" 
                      ,divWidth:"36%"                      
                      ,tbl:"tblInd"}
        );
      };
      function RetF10tblInd(arr){
        document.getElementById("edtCodInd").value  = arr[0].CODIGO;
        document.getElementById("edtDesInd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodInd").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codIndBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  //,foco:"cbAtivo"
                                  ,topo:100
                                  ,tableBd:"FAVORECIDO"
                                  ,fieldCod:"A.FVR_CODIGO"
                                  ,fieldDes:"A.FVR_NOME"
                                  ,fieldAtv:"A.FVR_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblInd"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "000"  : ret[0].CODIGO                  );
          document.getElementById("edtDesInd").value  = ( ret.length == 0 ? "NAO SE APLICA"      : ret[0].DESCRICAO );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "000" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //     AJUDA PARA VENDEDOR   //
      ///////////////////////////////
      function vndFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function vndF10Click(obj){ 
        fVendedorF10(0,obj.id,"cbFidelidade",100,{tamColNome:"29.5em",ativo:"S" } ); 
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
          var arr = fVendedorF10(1,obj.id,"cbFidelidade",100,
            {codfvr  : elNew
             ,ativo  : "S"} 
            ); 
          document.getElementById(obj.id).value       = ( arr.length == 0 ? "0000"  : jsNmrs(arr[0].CODIGO).emZero(4).ret() );  
          document.getElementById("edtDesVnd").value  = ( arr.length == 0 ? ""      : arr[0].DESCRICAO                      );
          document.getElementById("edtCodLgn").value  = ( arr.length == 0 ? "0"     : arr[0].CODLGN                         );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )         );
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
        document.getElementById("edtCodGm").setAttribute("data-gmvalorminimo",arr[0].VALORMINIMO);
        document.getElementById("edtCodGm").setAttribute("data-codgp",arr[0].CODGP);
      };
      function codGmBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fGrupoModeloF10(1,obj.id,"edtUnidades",100,
            {codgm  : elNew
             ,ativo : "S"} 
            ); 
          document.getElementById(obj.id).value     = ( arr.length == 0 ? "0000"  : jsNmrs(arr[0].CODIGO).emZero(4).ret() );
          document.getElementById("edtDesGm").value = ( arr.length == 0 ? ""      : arr[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )       );
          document.getElementById("edtCodGm").setAttribute("data-gmvalorvista",arr[0].VALORVISTA                          );
          document.getElementById("edtCodGm").setAttribute("data-gmvalorprazo",arr[0].VALORPRAZO                          );
          document.getElementById("edtCodGm").setAttribute("data-gmvalorminimo",arr[0].VALORMINIMO                        );
          document.getElementById("edtCodGm").setAttribute("data-codgp",arr[0].CODGP                                      );
        };
      };
      function fncCnpj(str){
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "buscaCnpj"         );
        clsJs.add("login"   , jsPub[0].usr_login  );
        clsJs.add("cnpj"    , str                 );    
        fd = new FormData();
        fd.append("pedidocad" , clsJs.fim());
        msg     = requestPedido("Trac_PedidoCad.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          document.getElementById("edtDesFvr").value=retPhp[0]["dados"][0]["FVR_NOME"];
          document.getElementById("edtCodFvr").value=jsNmrs(retPhp[0]["dados"][0]["FVR_CODIGO"]).emZero(4).ret();
        } else {
          document.getElementById("edtDesFvr").value="";
          document.getElementById("edtCodFvr").value="0000";
          
        }; 
      };
      ///////////////////////////
      // Excluindo item do pedido
      ///////////////////////////  
      function pedExcluirClick(){
        try{ 
          clsChecados   = objIte.gerarJson("1");
          chkds         = clsChecados.gerar();
          ///////////////////////////////////////
          // Vendo aqui se eh o produto principal
          ///////////////////////////////////////  
          let principal = "N";
          let codSrv  = jsNmrs(chkds[0].SERVICO).inteiro().ret();
          let codGm   = jsNmrs(chkds[0].PRODUTO).inteiro().ret();
          if( codSrv==0 )
            principal="S";
            
          if( (principal=="N") && (chkds[0].OBR=="SIM" ) )
            throw "Servico obrigatorio não pode ser excluido!";   
          
          //////////////////////////////////////////////////////////////////////////////////////////
          // Se for principal obrigatorio marcar todos os registros da table com o codigo do produto
          //////////////////////////////////////////////////////////////////////////////////////////
          if( principal=="S" ){
            let tbl = tblPed.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
            if( nl>0 ){
              for(let lin=0 ; (lin<nl) ; lin++){
                if( codGm == jsNmrs(tbl.rows[lin].cells[objColIte.PRODUTO].innerHTML).inteiro().ret() ){
                  tbl.rows[lin].cells[0].children.cbOpc.checked=true
                };
              };    
            };  
          };
          //
          //
          objIte.apagaChecadosSoTable();
          fncTotalPedido();
          $doc("edtVlrMensal").value="0,00";
          $doc("edtVlrMensal").setAttribute("data-vlrdolar","0.00");  // Para não ter que tirar o separador de milhar
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      }; 
      //////////////////////////////////////////////////////////////////
      // Qquer alteracao em um dos dois combobox altera o valor unitario
      //////////////////////////////////////////////////////////////////
      function fncVistaPrazo(valor){
        if( valor=="V" ){
          $doc("edtVlrUnitario").value=$doc("edtCodGm").getAttribute("data-gmvalorvista");
        } else {
          $doc("edtVlrUnitario").value=$doc("edtCodGm").getAttribute("data-gmvalorprazo");
        };
      };
      function fncVistaPrazoPontualChange(valor){
        if( valor=="V" ){
          document.getElementById("cbPrazoPontual").value="1";
        };
        fncVistaPrazo(valor);
      };
      function fncVistaPrazoPontualBlur(valor){
        fncVistaPrazo(valor);
      };
      //////
      // Fim
      //////  
      function fncUnidadesBlur(val){
        let ceOpt;
        let unid=jsNmrs(val).inteiro().ret();
        let pcDesc;
        let tam;
        document.getElementById("cbDesconto").innerHTML="";
        
        if( document.getElementById("cbVendaLocacao").value=="L" ){
          pcDesc=["0","1","2","3","4","5"];  
        };  
        
        if( document.getElementById("cbVendaLocacao").value=="D" ){
          pcDesc=["0"];            
        };  
        
        if( document.getElementById("cbVendaLocacao").value=="V" ){          
          if( (unid>=1) && (unid<=49) ){
            pcDesc=["0"];  
          }
          if( (unid>=50) && (unid<=99) ){
            pcDesc=["0","1","2","3","4","5"];  
          }
          if( (unid>=100) && (unid<=149) ){
            pcDesc=["0","1","2","3","4","5","6","7"];  
          }
          if( unid>=150 ){
            pcDesc=["0","1","2","3","4","5","6","7","8","9","10","11","12"];  
          }
        };
        
        tam=pcDesc.length;
        for( let lin=0;lin<tam;lin++ ){
          ceOpt 	= document.createElement("option");        
          ceOpt.value = pcDesc[lin];
          ceOpt.text  = jsNmrs(pcDesc[lin]).inteiro().ret()+"%";
          document.getElementById("cbDesconto").appendChild(ceOpt);
        };
      };
      ///////////////////////////////////////////////////////
      // Montando o parcelamento
      ///////////////////////////////////////////////////////
      function pedParcelaClick(){
        try{
          let tbl = tblPed.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          if( nl>0 ){
            //////////////////////////////////////
            // Calculando os venctos para parcelas
            //////////////////////////////////////
            let tblVencto = new Array();
            let splt      = (document.getElementById("edtDtPedido").value).split("/");
            let dia       = jsNmrs("cbDia").emZero(2).ret()
            let mes       = splt[1];
            let ano       = splt[2];
            let data      = "";
            tamC          = jsNmrs("cbPrazoContrato").inteiro().ret();
            for( let lin=1;lin<=tamC;lin++ ){ 
              mes++;
              if( mes==13 ){
                mes=1;
                ano++;
              };
              data=jsNmrs(dia).emZero(2).ret()+"/"+jsNmrs(mes).emZero(2).ret()+"/"+jsNmrs(ano).emZero(4).ret();
              tblVencto.push({"parcela":lin,"data":data});
            };  
            
            let tblParc     = new Array();
            let duplicidade = new Array();
            tamC            = tblVencto.length;
            
            for(let lin=0 ; (lin<nl) ; lin++){
              let qtasParc    = jsNmrs(tbl.rows[lin].cells[objColIte.PRAZO].innerHTML).inteiro().ret();
              let valor       = jsNmrs(tbl.rows[lin].cells[objColIte.MENSAL].innerHTML).dec(2).dolar().ret();
              // Para valor pontual pode variar 0,01 para mais ou para menos
              let acumPontual = jsNmrs(tbl.rows[lin].cells[objColIte.TOTAL].innerHTML).dec(2).dolar().ret();
              //
              let pagto       = tbl.rows[lin].cells[objColIte.PAGTO].innerHTML;
              let ind;       
              let venc;
              let seek;

              for( let par=1;par<=qtasParc;par++ ){
                seek=jsNmrs(par).emZero(2).ret()+pagto;
                ind=duplicidade.indexOf(seek);
                
                if( ind == -1 ){
                  venc="**/**/****";
                  for( let ven=0;ven<tamC;ven++ ){
                    if( par==tblVencto[ven].parcela ){
                      venc=tblVencto[ven].data;
                      break;
                    };  
                  };
                  ///////////////////////////////////////////////////////
                  // Para arredondar valores 0,01 para mais ou para menos
                  ///////////////////////////////////////////////////////
                  if( pagto=="PONTUAL" ){
                    if( par==qtasParc ){
                      valor=acumPontual;
                    } else {
                      acumPontual=(acumPontual-valor);
                    };  
                  };  
                  //
                  tblParc.push({
                    "parcela" : par
                    ,"pagto"  : pagto
                    ,"vencto" : venc
                    ,"valor"  : valor
                  });
                  duplicidade.push(seek);
                } else {
                  tblParc[ind].valor+=valor;
                };
              };
            };
            let vlrMensal   = 0;
            let vlrPontual  = 0;
            let valor       = 0;

            tblParc.forEach(function(reg){
              valor+=reg.valor;
              if( reg.pagto=="MENSAL" )
                vlrMensal+=reg.valor;
              if( reg.pagto=="PONTUAL" )
                vlrPontual+=reg.valor;
            }); 
            $doc("edtVlrMensal").value=jsNmrs(vlrMensal).dolar().sepMilhar().ret();
            $doc("edtVlrPontual").value=jsNmrs(vlrPontual).dolar().sepMilhar().ret();
            $doc("edtVlrMensal").setAttribute("data-vlrdolar",jsNmrs(vlrMensal).dec(2).dolar().ret());      // Para não ter que tirar o separador de milhar
            $doc("edtVlrPontual").setAttribute("data-vlrdolar",jsNmrs(vlrPontual).dec(2).dolar().ret());    // Para não ter que tirar o separador de milhar          
            tamC=tblParc.length;
            let addTbl = new Array();
            for( let lin=0;lin<tamC;lin++ ){
              addTbl.push([              
                tblParc[lin].parcela
                ,tblParc[lin].pagto
                ,tblParc[lin].vencto
                ,tblParc[lin].valor
                ,parID
              ]);
              parID++;
            };
            jsPar.registros=objPar.addIdUnico(addTbl);
            objPar.ordenaJSon(jsPar.indiceTable,false);  
            objPar.montarBody2017();
          };
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };  
      
      function calculaLinha( pVendaLocacao  ,pMensalPontual ,pVlrUnitVista
                            ,pVlrUnitPrazo  ,pVlrUnitMinimo ,pUnidades      
                            ,pDesconto      ,pPrazoContrato ,pPrazoPontual  
                            ,pProdutoServico,pDescSrv){
        try{         
          let prazoPontual    = jsNmrs(pPrazoPontual).inteiro().ret();        
          let vlrDescSrv      = jsNmrs(pDescSrv).dolar().ret();        
          let vlrUnitVista    = jsNmrs(pVlrUnitVista).dolar().ret();
          let vlrUnitPrazo    = jsNmrs(pVlrUnitPrazo).dolar().ret();
          let vlrUnitario     = 0;
          //////////////////////////////////////////////////////////////////////
          // Se o valor unitario for maior que zero eh pq esta alterando o valor
          //////////////////////////////////////////////////////////////////////
          if( vlrDescSrv>0 ){
            if( prazoPontual==1 ){
              vlrUnitVista=( vlrUnitVista-(vlrUnitVista*(vlrDescSrv/100)) );    //altUnitario;
            } else {
              vlrUnitPrazo=( vlrUnitPrazo-(vlrUnitPrazo*(vlrDescSrv/100)) );
            };  
          };
          /////////////////////////////////////////////////////////////
          // Definindo o valor unitario para aplicar ou naum o desconto
          /////////////////////////////////////////////////////////////
          if( prazoPontual==1 ){
            vlrUnitario = vlrUnitVista;
          } else {
            vlrUnitario = vlrUnitPrazo;  
          };  
          //  
          //
          let mensalPontual   = pMensalPontual;
          let vlrUnitMinimo   = jsNmrs(pVlrUnitMinimo).dolar().ret();
          let vlrUnitComDesc  = vlrUnitario;
          let unidades        = jsNmrs(pUnidades).dolar().ret();
          let desconto        = jsNmrs(pDesconto).inteiro().ret();
          let prazoContrato   = jsNmrs(pPrazoContrato).inteiro().ret();
          let total           = 0;
          let totalMes        = 0
          ////////////////////
          // Checagens basicas
          ////////////////////
          if( vlrUnitario<=0 )    throw "Valor unitario deve ser maior que zero!";     
          if( unidades<=0 )       throw "Unidades deve ser maior que zero!";     
          if( desconto<0 )        throw "Desconto não pode ser menor que zero!";  
          if( prazoContrato<=0 )  throw "Prazo do contrato deve ser maior que zero!"; 
          if( prazoPontual<=0 )   throw "Prazo pontual deve ser maior que zero!";           
          //////////////////////
          // Existem dois prazos
          //////////////////////
          let prazo = 0;          
          if( (pProdutoServico=="P") && (pVendaLocacao=="L") && (pMensalPontual=="MENSAL") )
            prazo=prazoContrato;
          if( (pProdutoServico=="P") && (pVendaLocacao=="V") && (pMensalPontual=="MENSAL") )
            throw "Parametro PRODUTO/VENDA/MENSAL não aceito!";   
          if( (pProdutoServico=="P") && (pVendaLocacao=="V") && (pMensalPontual=="PONTUAL") )
            prazo=prazoPontual;
          if( (pProdutoServico=="S") && (pMensalPontual=="MENSAL") )
            prazo=prazoContrato;
          if( (pProdutoServico=="S") && (pMensalPontual=="PONTUAL") )
            prazo=prazoPontual;
          if( prazo<=0 ) 
            throw "Prazo deve ser maior que zero!";          
          /////////////////////////////////
          // Verificando se existe desconto
          /////////////////////////////////
          if( desconto> 0 ){
            vlrUnitComDesc = ( vlrUnitario-(vlrUnitario*(desconto/100)) );   
          };
          
          if( (mensalPontual=="PONTUAL") && (pProdutoServico=="S") ){
            total     = ( vlrUnitComDesc*unidades );  
            totalMes  = ( total/prazo );
          };

          if( (mensalPontual=="PONTUAL") && (pProdutoServico=="P") ){
            total     = ( vlrUnitComDesc*unidades );  
            totalMes  = ( total/prazo );
          };

          if( (mensalPontual=="MENSAL") && (pProdutoServico=="S")){
            totalMes  = ( vlrUnitComDesc*unidades );  
            total     = ( totalMes*prazo );
          };
          /////////////////////////////////////////////////////////////////////////
          // Com as opcoes de desconto em combobox esta checagem naum eh necessaria
          /////////////////////////////////////////////////////////////////////////
          //if( vlrUnitComDesc<vlrUnitMinimo )
          //  throw "Valor minimo aceito "+jsNmrs(vlrUnitMinimo).real().ret()+"!";    
          //
          //
          return {
            mensalPontual   : mensalPontual
            ,prazo          : prazo
            ,vendaLocacao   : pVendaLocacao
            ,produtoServico : pProdutoServico
            ,vlrUnitVista   : vlrUnitVista
            ,vlrUnitPrazo   : vlrUnitPrazo
            ,vlrUnitMinimo  : vlrUnitMinimo
            ,vlrUnitComDesc : vlrUnitComDesc
            ,desconto       : desconto
            ,unidades       : unidades
            ,total          : total
            ,totalMes       : totalMes
          };  
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
      ///////////////////////////////////////////////////////
      // Enchendo a grade com produto + servicos relacionados
      ///////////////////////////////////////////////////////
      function fncIncluir(){
        try{  
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Aqui para garantir que naum vai entrar em um mesmo pedido venda/locacao ou venda/demostracao ou qquer um diferente do primeiro
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          if( $doc("cbVendaLocacao").getAttribute("data-vendalocacao")=="*" ){
            $doc("cbVendaLocacao").setAttribute("data-vendalocacao",$doc("cbVendaLocacao").value);
          } else {
            if( $doc("cbVendaLocacao").getAttribute("data-vendalocacao") != $doc("cbVendaLocacao").value )
              throw "PEDIDO ACEITA SOMENTE UM DOS ITENS VENDA OU LOCAÇÃO!";
          };      
          //
          //
          document.getElementById("edtUnidades").value  = document.getElementById("edtUnidades").value.soNumeros();
          document.getElementById("edtCodGm").value     = document.getElementById("edtCodGm").value.soNumeros();
          document.getElementById("edtCodVnd").value    = document.getElementById("edtCodVnd").value.soNumeros();
          
          msg = new clsMensagem("Erro");
          msg.intMaiorZero("UNIDADES"       , document.getElementById("edtUnidades").value    );
          msg.intMaiorZero("COD_PRODUTO"    , document.getElementById("edtCodGm").value       );
          msg.notNull("NOME_PRODUTO"        , document.getElementById("edtDesGm").value       );
          msg.intMaiorZero("LOGIN"          , document.getElementById("edtCodLgn").value      );          
          msg.intMaiorZero("COD_VENDEDOR"   , document.getElementById("edtCodVnd").value      );          
          msg.floMaiorZero("VALOR_UNITARIO" , document.getElementById("edtVlrUnitario").value ); 

          if( (document.getElementById("cbVistaPrazoPontual").value=="V") && (document.getElementById("cbPrazoPontual").value != "1") )
            msg.add("CAMPO<b> PRAZO </b>PARA PAGTO A VISTA PRAZO DEVE SER 1x!");  
          if( (document.getElementById("cbVistaPrazoPontual").value=="P") && (document.getElementById("cbPrazoPontual").value == "1") )
            msg.add("CAMPO<b> PRAZO </b>PARA PAGTO A PRAZO NUMERO DE PARCELA DEVE SER DIFERENTE 1x!");  
          
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            ////////////////////////////////////////////////////////////////////////////////
            // Aqui colocando o produto principal pois pode naum ter nenhum servico agregado
            ////////////////////////////////////////////////////////////////////////////////
            let retorno;
            ///////////////////////////////////////////////////////////////////////////
            // Funcao criada devido opcao de recalculo na alteracao de prazo contratual
            ///////////////////////////////////////////////////////////////////////////
            retorno=calculaLinha(
              document.getElementById("cbVendaLocacao").value                                 // VLD( Venda/Locacao/Demonstracao )
              ,(document.getElementById("cbVendaLocacao").value=="L" ? "MENSAL" : "PONTUAL")  // Mensal/Pontual
              ,$doc("edtCodGm").getAttribute("data-gmvalorvista")                             // Valor Unitario a vista
              ,$doc("edtCodGm").getAttribute("data-gmvalorprazo")                             // Valor Unitario a prazo
              ,$doc("edtCodGm").getAttribute("data-gmvalorminimo")                            // Valor Unitario minimo
              ,document.getElementById("edtUnidades").value                                   // Unidades
              ,document.getElementById("cbDesconto").value                                    // Desconto
              ,document.getElementById("cbPrazoContrato").value                               // Prazo do contrato
              ,document.getElementById("cbPrazoPontual").value                                // Prazo pontual
              ,"P"                                                                            // Se eh produto ou servico
              ,0                                                                              // Vlr unitario usado apenas na alteracao de valor  
            );
            ///////////////////////////////////////////////////////////////////////////////////////
            // Tem que ficar na ordem das colunas - tem outro igual que eh utilizado para alteracao
            // Procurar jsIte.registros
            ///////////////////////////////////////////////////////////////////////////////////////  
            jsIte.registros.push([              
               document.getElementById("edtCodGm").value                        // PRODUTO
              ,"0000"                                                           // SERVICO  
              ,document.getElementById("edtDesGm").value                        // DESCRICAO
              ,"SIM"                                                            // OBR 
              ,retorno.mensalPontual                                            // PAGTO 
              ,retorno.prazo                                                    // PRAZO
              ,retorno.vlrUnitComDesc                                           // UNITARIO COM DESCONTO
              ,retorno.vlrUnitVista                                             // UNITARIO A VISTA SEM DESCONTO DEVIDO RECALCULO
              ,retorno.vlrUnitPrazo                                             // UNITARIO A PRAZO SEM DESCONTO DEVIDO RECALCULO
              ,retorno.vlrUnitMinimo                                            // UNITARIO MINIMO
              ,retorno.desconto                                                 // DESC
              ,retorno.unidades                                                 // QTDADE
              ,retorno.totalMes                                                 // MENSAL
              ,retorno.total                                                    // TOTAL 
              ,retorno.vendaLocacao                                             // FLAG( VENDA/LOCACAO )
              ,retorno.produtoServico                                           // PS  
              ,0                                                                // PLACA( Qtdade de placas informadas )
              ,$doc("edtCodGm").getAttribute("data-codgp")                      // CODGP
              ,pedID
            ]);
            
            pedID++;
            ////////////////////////////////////////////////////////////////////////////////
            // Aqui colocando o servico se existir para produto principal(auto)
            ////////////////////////////////////////////////////////////////////////////////
            clsJs   = jsString("lote");  
            clsJs.add("rotina"        , "buscaServico"                                    );
            clsJs.add("login"         , jsPub[0].usr_login                                );
            clsJs.add("codgm"         , document.getElementById("edtCodGm").value         );    
            clsJs.add("vendalocacao"  , document.getElementById("cbVendaLocacao").value   );                
            clsJs.add("instpropria"   , document.getElementById("cbInstPropria").value   );
            fd = new FormData();
            fd.append("pedidocad" , clsJs.fim());

            msg     = requestPedido("Trac_PedidoCad.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno == "OK" ){
              tamC=(retPhp[0]["dados"]).length;
              for( let lin=0;lin<tamC;lin++ ){
                ///////////////////////////////////////////////////////////////////////////
                // Funcao criada devido opcao de recalculo na alteracao de prazo contratual
                ///////////////////////////////////////////////////////////////////////////
                retorno=calculaLinha(
                  document.getElementById("cbVendaLocacao").value                         // VLD( Venda/Locacao/Demonstracao )
                  ,( retPhp[0]["dados"][lin]["GMS_MENSAL"]=="S" ? "MENSAL" : "PONTUAL" )  // Mensal/Pontual                  
                  ,retPhp[0]["dados"][lin]["GMS_VALOR"]                                   // Valor Unitario a vista
                  ,retPhp[0]["dados"][lin]["GMS_VALOR"]                                   // Valor Unitario a prazo
                  ,retPhp[0]["dados"][lin]["GMS_VALOR"]                                   // Valor Minimo
                  ,document.getElementById("edtUnidades").value                           // Unidades
                  ,document.getElementById("cbDesconto").value                            // Desconto
                  ,document.getElementById("cbPrazoContrato").value                       // Prazo do contrato
                  ,document.getElementById("cbPrazoPontual").value                        // Prazo pontual                  
                  ,"S"                                                                    // Se eh produto ou servico
                  ,0                                                                      // Vlr unitario usado apenas na alteracao de valor                    
                );
                ///////////////////////////////////////////////////////////////////////////////////////
                // Tem que ficar na ordem das colunas - tem outro igual que eh utilizado para alteracao
                // Procurar jsIte.registros
                ///////////////////////////////////////////////////////////////////////////////////////  
                jsIte.registros.push([              
                  document.getElementById("edtCodGm").value     // PRODUTO              
                  ,retPhp[0]["dados"][lin]["GMS_CODSRV"]        // SERVICO  
                  ,retPhp[0]["dados"][lin]["SRV_NOME"]          // DESCRICAO
                  ,retPhp[0]["dados"][lin]["GMS_OBRIGATORIO"]   // OBR 
                  ,retorno.mensalPontual                        // PAGTO 
                  ,retorno.prazo                                // PRAZO
                  ,retorno.vlrUnitComDesc                       // UNITARIO COM DESCONTO 
                  ,retorno.vlrUnitVista                         // UNITARIO A VISTA SEM DESCONTO DEVIDO RECALCULO
                  ,retorno.vlrUnitPrazo                         // UNITARIO A PRAZO SEM DESCONTO DEVIDO RECALCULO
                  ,retorno.vlrUnitMinimo                        // UNITARIO MINIMO                  
                  ,retorno.desconto                             // DESC
                  ,retorno.unidades                             // QTDADE
                  ,retorno.totalMes                             // MENSAL
                  ,retorno.total                                // TOTAL 
                  ,retorno.vendaLocacao                         // FLAG( VENDA/LOCACAO )
                  ,retorno.produtoServico                       // PS  
                  ,0                                            // PLACA( Qtdade de placas informadas )
                  ,"***"                                        // CODGP
                  ,pedID
                ]);
                pedID++;
              };
            }; 
            objIte.montarBody2017();
            fncTotalPedido();
            
            document.getElementById("edtUnidades").value    = "001";
            document.getElementById("edtCodGm").value       = "0000";
            document.getElementById("edtDesGm").value       = "";
            document.getElementById("edtVlrUnitario").value = "0,00";
            document.getElementById("edtCodGm").foco();
          };  
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
      ////////////////
      // Gravar pedido
      ////////////////
      function fncGravarPedido(){
        try{ 
          $doc("edtCodVnd").value   = document.getElementById("edtCodVnd").value.soNumeros();
          $doc("edtCnpjCpf").value  = document.getElementById("edtCnpjCpf").value.soNumeros();
          $doc("edtDesFvr").value   = jsStr("edtDesFvr").upper().ret();

          msg = new clsMensagem("Erro");          
          
          msg.notNull("CLIENTE"           , document.getElementById("edtDesFvr").value                                );
          msg.notNull("CNPJ_CPF"          , document.getElementById("edtCnpjCpf").value                               );          
          msg.intMaiorZero("COD_VENDEDOR" , document.getElementById("edtCodVnd").value                                );
          msg.intMaiorZero("LOGIN"        , document.getElementById("edtCodLgn").value                                );
          msg.floMaiorZero("VALOR TOTAL"  , jsNmrs($doc("edtVlrPedido").getAttribute("data-vlrdolar")).real().ret()  ); 
          
          let comparaVlrPedido=jsNmrs($doc("edtVlrPedido").getAttribute("data-vlrdolar")).dolar().ret();
          let comparaVlrMensal=jsNmrs($doc("edtVlrMensal").getAttribute("data-vlrdolar")).dolar().ret();
          let comparaVlrPontual=jsNmrs($doc("edtVlrPontual").getAttribute("data-vlrdolar")).dolar().ret();
          if( comparaVlrPedido != (comparaVlrMensal+comparaVlrPontual) )
            msg.add("VALOR DO PEDIDO DEVE SER IGUAL AO VALOR MENSAL + PONTUAL!");  

          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            //////////////////////////////////////////////////////////////////////
            // Montando um JSON pois esta tabela eh temporario, vai virar contrato
            // Deve estar na sequencia da table
            //////////////////////////////////////////////////////////////////////
            clsChecados = objIte.gerarJson();
            clsChecados.retornarQtos("n");
            clsChecados.temColChk(false);
            clsChecados.nenhumChecado(true);
            let json  = clsChecados.gerar();
            let jsItem="{|item|:[";
            let jsPlaca="";
            let qtdadeAuto=0;
            json.forEach(function(reg){
              jsItem+="{|produto|:|"+reg.PRODUTO+"|"
                  +",|servico|:|"+reg.SERVICO+"|"
                  +",|descricao|:|"+reg.DESCRICAO+"|"
                  +",|obr|:|"+reg.OBR+"|"
                  +",|pagto|:|"+reg.PAGTO+"|"
                  +",|prazo|:|"+reg.PRAZO+"|"              
                  +",|unitario|:|"+reg.UNITARIO+"|"
                  +",|unitariovista|:|"+reg.UNITARIOVISTA+"|"
                  +",|unitarioprazo|:|"+reg.UNITARIOPRAZO+"|"
                  +",|unitariominimo|:|"+reg.UNITARIOMINIMO+"|"
                  +",|desc|:|"+reg.DESC+"|"              
                  +",|qtdade|:|"+reg.QTDADE+"|"              
                  +",|mensal|:|"+reg.MENSAL+"|"              
                  +",|total|:|"+reg.TOTAL+"|"              
                  +",|flag|:|"+reg.FLAG+"|"              
                  +",|ps|:|"+reg.PS+"|"
                  +",|placa|:|"+reg.PLACA+"|"                  
                  +",|codgp|:|"+reg.CODGP+"|"                  
                  +"},";
              if( reg.CODGP=="AUT" ){
                qtdadeAuto+=reg.QTDADE;
              };    
            });
            jsItem=jsItem.slice(0,-1);          
            jsItem+="]}";
            /////////////////////////
            // Pegando o parcelamento
            /////////////////////////
            clsChecados = objPar.gerarJson();
            clsChecados.retornarQtos("n");
            clsChecados.temColChk(false);
            clsChecados.nenhumChecado(true);
            json  = clsChecados.gerar();
            let jsParcela="{|parcela|:[";
            json.forEach(function(reg){
              jsParcela+="{|parcela|:|"+reg.PARCELA+"|"
                  +",|pagto|:|"+reg.PAGTO+"|"
                  +",|vencto|:|"+reg.VENCTO+"|"
                  +",|valor|:|"+jsNmrs(reg.VALOR).dolar().ret()+"|"              
                  +"},";
            });
            jsParcela=jsParcela.slice(0,-1);          
            jsParcela+="]}";
            /////////////////////////
            // Pegando as placas
            /////////////////////////
            clsChecados = objPlc.gerarJson();
            clsChecados.retornarQtos("n");
            clsChecados.temColChk(false);
            clsChecados.nenhumChecado(true);
            json  = clsChecados.gerar();
            let qtdadePlaca=0;            
            if( json.length==0 ){
              jsPlaca="{|placa|:[]}";  
            } else {
              jsPlaca="{|placa|:[";
              json.forEach(function(reg){
                jsPlaca+="{|produto|:|"+reg.PRODUTO+"|"
                        +",|placa|:|"+reg.PLACA+"|"              
                        +",|iditem|:|"+reg.IDITEM+"|"              
                        +"},";
                qtdadePlaca++;        
              });
              jsPlaca=jsPlaca.slice(0,-1);          
              jsPlaca+="]}";
            };
            //
            //
            ///////////////////////////////
            // Classe principal para Php //
            ///////////////////////////////
            clsJs   = jsString("lote");  
            clsJs.add("rotina"                , "cadastrar"                                   );
            clsJs.add("login"                 , jsPub[0].usr_login                            );
            clsJs.add("codusr"                , jsPub[0].usr_codigo                           );
            clsJs.add("pedido"                , jsNmrs("edtNumPedido").inteiro().ret()        );  // 0=inserindo   >0=alterando 
            clsJs.add("emissao"               , jsDatas("edtDtPedido").retMMDDYYYY()          );
            clsJs.add("cnpj"                  , document.getElementById("edtCnpjCpf").value   );        
            clsJs.add("nome"                  , document.getElementById("edtDesFvr").value    );        
            clsJs.add("codvnd"                , document.getElementById("edtCodVnd").value    );
            clsJs.add("codfvr"                , document.getElementById("edtCodFvr").value    );
            clsJs.add("valor"                 , jsNmrs($doc("edtVlrPedido").getAttribute("data-vlrdolar")).dolar().ret()  );  
            clsJs.add("qtdauto"               , qtdadeAuto                                    );
            clsJs.add("qtdplaca"              , qtdadePlaca                                   );        
            clsJs.add("tipo"                  , $doc("cbVendaLocacao").value                  );                    
            clsJs.add("fidelidade"            , $doc("cbFidelidade").value                    );        
            clsJs.add("instpropria"           , $doc("cbInstPropria").value                   );        
            clsJs.add("meses"                 , jsNmrs("cbPrazoContrato").inteiro().ret()     );
            clsJs.add("dia"                   , jsNmrs("cbDia").inteiro().ret()               );        
            clsJs.add("localinstala"          , $doc("cbLocalInstala").value                  );                    
            clsJs.add("status"                , jsNmrs("cbStatus").inteiro().ret()            );
            clsJs.add("codind"                , $doc("edtCodInd").value                       );  // Codigo de quem indicou
            clsJs.add("codlgn"                , $doc("edtCodLgn").value                       );            
            clsJs.add("obs"                   , $doc("edtObs").value                          );            
            clsJs.add("item"                  , jsItem                                        );
            clsJs.add("parcela"               , jsParcela                                     );
            clsJs.add("placa"                 , jsPlaca                                       );
            envPhp=clsJs.fim();
            fd = new FormData();
            
            fd.append("pedidocad" , envPhp);
            envPhp     = requestPedido("Trac_PedidoCad.php",fd); 
            retPhp  = JSON.parse(envPhp);
            if( retPhp[0].retorno == "OK" ){
              gerarMensagemErro("ped",retPhp[0].numPdd,{cabec:"Aviso"});
              /////////////////////////////////////////
              // Atualizando a grade em Trac_Pedido.php
              /////////////////////////////////////////
              window.opener.btnFiltrarClick();              
              window.close();                
            } else {
              gerarMensagemErro("ped",retPhp[0].erro,{cabec:"Erro"});    
            };  
          };    
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
      ///////////////////////////
      // Alterar valor
      ///////////////////////////
      function pedValorAltClick(){
        try{
          clsChecados = objIte.gerarJson("1");
          chkds       = clsChecados.gerar();
          
          chkds.forEach(function(reg){
            if( reg.PS != "S" )
              throw "Favor selecionar um serviço!"; 
          });
          ////////////////////////////////////////////////
          // Para poder atualizar a coluna VALOR em tblPed
          ////////////////////////////////////////////////
          
          let clsCode = new concatStr();  
          clsCode.concat("<div class='campotexto campo100'></div>");          
          clsCode.concat("<div class='campotexto campo20'></div>");
          clsCode.concat("<div class='campotexto campo40'>");          
          clsCode.concat(  "<select id='cbDescSrv' class='campo_input_combo' />"); 
          let unid=parseInt($doc("edtUnidades").value);
          if( (unid>0) && (unid<=50) ){
            clsCode.concat(    "<option value='1' selected >1%</option>");            
            clsCode.concat(    "<option value='2'>2%</option>");                        
          };    
          if( (unid>50) && (unid<=100) ){
            clsCode.concat(    "<option value='1' selected >1%</option>");            
            clsCode.concat(    "<option value='2'>2%</option>");                        
            clsCode.concat(    "<option value='3'>3%</option>");            
          };    
          if( unid>100 ){
            clsCode.concat(    "<option value='1' selected >1%</option>");            
            clsCode.concat(    "<option value='2'>2%</option>");                        
            clsCode.concat(    "<option value='3'>3%</option>");            
            clsCode.concat(    "<option value='4'>4%</option>");                        
            clsCode.concat(    "<option value='5'>5%</option>");            
          };    
          clsCode.concat(  "</select>");   
          clsCode.concat(  "<label class='campo_label' for='cbPrazoPontual'>INFORME DESCONTO</label>");          
          clsCode.concat("</div>");          
          
          
          clsCode.concat("<div id='btnConfirmar' onClick='fncValorConfirmar(\""+chkds[0]._ID+"\");' class='btnImagemEsq bie15 bieAzul bie'><i class='fa fa-check'>Ok</i></div>");
          janelaDialogo(
            { height        : "22em"
              ,body         : "10em"
              ,left         : "320px"
              ,top          : "60px"
              ,tituloBarra  : (chkds[0].DESCRICAO).substring(0,25)
              ,code         : clsCode.fim()
              ,width        : "50em"
              ,foco         : "cbDescSrv"
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function fncValorConfirmar(parId){
        tblPed.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr,tam) {  
          if( jsNmrs(row.cells[objColIte._ID].innerHTML).inteiro().ret()==parId ){
            let retorno=calculaLinha(
              row.cells[objColIte.FLAG].innerHTML                   // VLD( Venda/Locacao/Demonstracao )
              ,row.cells[objColIte.PAGTO].innerHTML                 // Mensal/Pontual
              ,row.cells[objColIte.UNITARIOVISTA].innerHTML         // Valor Unitario a vista
              ,row.cells[objColIte.UNITARIOPRAZO].innerHTML         // Valor Unitario a prazo
              ,row.cells[objColIte.UNITARIOMINIMO].innerHTML        // Valor minimo
              ,row.cells[objColIte.QTDADE].innerHTML                // Unidades
              ,$doc("cbDesconto").value                             // Desconto
              ,$doc("cbPrazoContrato").value                        // Prazo do contrato
              ,$doc("cbPrazoPontual").value                         // Prazo pontual
              ,row.cells[objColIte.PS].innerHTML                    // Se eh produto ou servico
              ,$doc("cbDescSrv").value                              // Vlr desconto sobre servico usado apenas na alteracao de valor              
            );
            row.cells[objColIte.UNITARIO].innerHTML        = jsNmrs(retorno.vlrUnitComDesc).real().ret();
            row.cells[objColIte.MENSAL].innerHTML          = jsNmrs(retorno.totalMes).real().ret();
            row.cells[objColIte.TOTAL].innerHTML           = jsNmrs(retorno.total).real().ret();
            row.cells[objColIte.DESC].innerHTML            = jsNmrs($doc("cbDescSrv").value).real().ret();
            fncTotalPedido();
            janelaFechar();
          };  
        })
      };  
      
      
      ///////////////////////////
      // Informar placa ou chassi
      ///////////////////////////
      function pedPlacaCadClick(){
        try{
          clsChecados = objIte.gerarJson("1");
          chkds       = clsChecados.gerar();
          
          chkds.forEach(function(reg){
            if( reg.PS != "P" )
              throw "Favor selecionar produto principal!"; 
          });
          ////////////////////////////////////////////////
          // Para poder atualizar a coluna PLACA em tblPed
          ////////////////////////////////////////////////
          document.getElementById("edtPedID").value=chkds[0]._ID;
          
          let clsCode = new concatStr();  
          clsCode.concat("<div class='campotexto campo100'></div>");          
          clsCode.concat("<div class='campotexto campo20'></div>");
          clsCode.concat("<div class='campotexto campo40'>");
          clsCode.concat(  "<input class='campo_input' id='placa' type='text' maxlength='17' />");
          clsCode.concat(  "<label class='campo_label campo_required' for='placa'> Informe placa/chassi</label>");
          clsCode.concat("</div>");
          clsCode.concat("<div id='btnConfirmar' onClick='fncPlacaConfirmar("+jsNmrs(chkds[0].PRODUTO).inteiro().ret()+");' class='btnImagemEsq bie15 bieAzul bie'><i class='fa fa-check'>Ok</i></div>");
          janelaDialogo(
            { height        : "22em"
              ,body         : "10em"
              ,left         : "320px"
              ,top          : "1360px"
              ,tituloBarra  : "Placa para produto "+chkds[0].PRODUTO
              ,code         : clsCode.fim()
              ,width        : "50em"
              ,foco         : "placa"
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////////////////////////////////
      // Function referente chamada biManualClick()
      /////////////////////////////////////////////
      function fncPlacaConfirmar(parCodAmp){
        try{          
          $doc("placa").value = jsStr("placa").upper().alltrim().ret();
          let continua=fncPlacaValida($doc("placa").value);
          if( continua != "ok" ){
            gerarMensagemErro("catch",continua,{cabec:"Erro",topo:"1360"});
          } else {
            continua=true;
            
            tblPlc.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              if( row.cells[objColPlc.PLACA].innerHTML == $doc("placa").value ){
                msg="PLACA JA CADASTRADA!";    
                continua=false;
                return false;
              }; 
            });    

            if( continua==false ){
              gerarMensagemErro("ped",msg,{cabec:"Erro",topo:"1360"});  
            }; 
            
            if( continua ){          
              jsPlc.registros.push([              
                 chkds[0].PRODUTO                           // PRODUTO
                ,$doc("placa").value                        // PLACA
                ,document.getElementById("edtPedID").value  // IDITEM
                ,plcID
              ]);
              /////////////////////////////////////////////////////////////////////
              // Adiciona ou subtrai a qtade de placas informadas para cada produto
              /////////////////////////////////////////////////////////////////////
              addPlacaEmPedido(jsNmrs("edtPedID").inteiro().ret(),1);
              plcID++;
              
              objPlc.ordenaJSon(jsPlc.indiceTable,false);              
              objPlc.montarBody2017();
              janelaFechar(); 
              pedPlacaCadClick();
            };  
          };
        }catch(e){
          gerarMensagemErro("catch",e.message,{cabec:"Erro"});
        };
      };
      ///////////////////////////
      // Excluindo item da placa
      ///////////////////////////  
      function plcExcluirClick(){
        try{ 
          clsChecados   = objPlc.gerarJson("1");
          chkds         = clsChecados.gerar();
          objPlc.apagaChecadosSoTable();
          addPlacaEmPedido(jsNmrs(chkds[0].IDITEM).inteiro().ret(),-1);
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro",topo:"1360"});
        };
      }; 
      //////////////////////////////////////////////////////////////////
      // Adiciona ou remove a qtdade de placas informadas para cada item
      //////////////////////////////////////////////////////////////////
      function addPlacaEmPedido(id,qtos){
        try{
          let tbl = tblPed.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          for(let lin=0 ; (lin<nl) ; lin++){
            if( id == jsNmrs(tbl.rows[lin].cells[objColIte._ID].innerHTML).inteiro().ret() ){
              tbl.rows[lin].cells[objColIte.PLACA].innerHTML=( jsNmrs(tbl.rows[lin].cells[objColIte.PLACA].innerHTML).inteiro().ret()+qtos );
              break;
            };
          };    
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ///////////////////////
      // Observacao do pedido
      ///////////////////////
      function fncObsPedido(){
        let obs=document.getElementById("edtObs").value;
        try{
          let clsCode = new concatStr();  
          clsCode.concat("<div class='campotexto campo100'>");          
          clsCode.concat(  "<div style='margin-left:5%;' class='campotexto campo90'>");
          clsCode.concat(    "<textarea id='taObs' style='width:100%;' rows='10' placeholder='Informe'>"+obs+"</textarea>");
          clsCode.concat(  "</div>");
          clsCode.concat("</div>");      
          clsCode.concat("<div id='btnConfirmar' style=margin-right:5%;' onClick='$doc(\"edtObs\").value=$doc(\"taObs\").value;janelaFechar();' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'>Ok</i></div>");
          
          janelaDialogo(
            { height        : "27em"
              ,body         : "10em"
              ,left         : "320px"
              ,top          : "50px"
              ,tituloBarra  : "Observacao pedido "
              ,code         : clsCode.fim()
              ,width        : "50em"
              ,foco         : "taObs"
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ///////////////////////
      // Recalculando a grade
      ///////////////////////
      function fncRecalculo(){
        try{
          let tbl = tblPed.getElementsByTagName("tbody")[0];
          let nl  = tbl.rows.length;
          if( nl==0 ){
            gerarMensagemErro("rec","Nenhum registro na grade para recalculo!",{cabec:"Erro"});  
          } else {  
            let clsCode = new concatStr();  
            clsCode.concat("<div class='campotexto campo100'></div>");          
            clsCode.concat("<div class='campotexto campo50'>");
            clsCode.concat(  "<select id='cbRecPrazoContrato' class='campo_input_combo' />"); 
            clsCode.concat(    "<option value='12' selected >12x</option>");
            clsCode.concat(    "<option value='24'>24x</option>");
            clsCode.concat(    "<option value='36'>36x</option>");
            clsCode.concat(  "</select>");
            clsCode.concat(  "<label class='campo_label' for='cbRecPrazoContrato'>PRAZO CONTRATO:</label>");
            clsCode.concat("</div>");
            clsCode.concat("<div class='campotexto campo50'>");
            clsCode.concat(  "<select id='cbRecDesconto' class='campo_input_combo' />"); 
            clsCode.concat(    "<option value='0' selected >0%</option>");
            clsCode.concat(    "<option value='1'>1%</option>");
            clsCode.concat(    "<option value='2'>2%</option>");
            clsCode.concat(    "<option value='3'>3%</option>");
            clsCode.concat(    "<option value='4'>4%</option>");
            clsCode.concat(    "<option value='5'>5%</option>");          
            clsCode.concat(  "</select>");
            clsCode.concat(  "<label class='campo_label' for='cbRecDesconto'>DESCONTO:</label>");
            clsCode.concat("</div>");
            clsCode.concat("<div class='campotexto campo50'>");
            clsCode.concat(  "<select id='cbRecPrazoPontual' class='campo_input_combo' />");
            clsCode.concat(    "<option value='1' selected >1x</option>");
            clsCode.concat(    "<option value='2'>2x</option>");
            clsCode.concat(    "<option value='3'>3x</option>");
            clsCode.concat(    "<option value='4'>4x</option>");
            clsCode.concat(    "<option value='5'>5x</option>");
            clsCode.concat(  "</select>");
            clsCode.concat(  "<label class='campo_label' for='cbRecPrazoPontual'>PRAZO PONTUAL</label>");
            clsCode.concat("</div>");
            clsCode.concat("<div id='btnConfirmar' onClick='fncRecalcular();' class='btnImagemEsq bie15 bieAzul bie'><i class='fa fa-check'>Ok</i></div>");          
            
            janelaDialogo(
              { height        : "22em"
                ,body         : "10em"
                ,left         : "320px"
                ,top          : "60px"
                ,tituloBarra  : "Recalculo"
                ,code         : clsCode.fim()
                ,width        : "40em"
                //,foco         : "cbRecPrazoContrato"
              }
            );  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      
      function fncRecalcular(){
        let tbl = tblPed.getElementsByTagName("tbody")[0];
        let nl  = tbl.rows.length;
        
        for(let lin=0 ; (lin<nl) ; lin++){
          let retorno=calculaLinha(
            tbl.rows[lin].cells[objColIte.FLAG].innerHTML               // VLD( Venda/Locacao/Demonstracao )
            ,tbl.rows[lin].cells[objColIte.PAGTO].innerHTML             // Mensal/Pontual
            ,tbl.rows[lin].cells[objColIte.UNITARIOVISTA].innerHTML     // Valor Unitario a vista
            ,tbl.rows[lin].cells[objColIte.UNITARIOPRAZO].innerHTML     // Valor Unitario a prazo
            ,tbl.rows[lin].cells[objColIte.UNITARIOMINIMO].innerHTML    // Valor minimo
            ,tbl.rows[lin].cells[objColIte.QTDADE].innerHTML            // Unidades
            ,document.getElementById("cbRecDesconto").value             // Desconto
            ,document.getElementById("cbRecPrazoContrato").value        // Prazo do contrato
            ,document.getElementById("cbRecPrazoPontual").value         // Prazo pontual
            ,tbl.rows[lin].cells[objColIte.PS].innerHTML                // Se eh produto ou servico
            ,0                                                          // Vlr unitario usado apenas na alteracao de valor              
          );
          tbl.rows[lin].cells[objColIte.PRAZO].innerHTML           = jsNmrs(retorno.prazo).emZero(3).ret();
          tbl.rows[lin].cells[objColIte.DESC].innerHTML            = jsNmrs(retorno.desconto).real().ret();
          tbl.rows[lin].cells[objColIte.UNITARIO].innerHTML        = jsNmrs(retorno.vlrUnitComDesc).real().ret();
          tbl.rows[lin].cells[objColIte.QTDADE].innerHTML          = jsNmrs(retorno.unidades).emZero(3).ret();
          tbl.rows[lin].cells[objColIte.MENSAL].innerHTML          = jsNmrs(retorno.totalMes).real().ret();
          tbl.rows[lin].cells[objColIte.TOTAL].innerHTML           = jsNmrs(retorno.total).real().ret();
        };  
        $doc("cbDesconto").value      = $doc("cbRecDesconto").value;
        $doc("cbPrazoContrato").value = $doc("cbRecPrazoContrato").value;
        $doc("cbPrazoPontual").value  = $doc("cbRecPrazoPontual").value;
        janelaFechar();
      };
      
      function fncAbrirPlc(){
        try{
          clsChecados = objIte.gerarJson("1");
          chkds       = clsChecados.gerar();
          
          chkds.forEach(function(reg){
            if( reg.PS != "P" )
              throw "Favor selecionar produto principal!"; 
          });
          ////////////////////////////////////////////////
          // Para poder atualizar a coluna PLACA em tblPed
          ////////////////////////////////////////////////
          document.getElementById("edtPedID").value=chkds[0]._ID;
          
          
          msg = new clsMensagem("Erro");
          msg.notNull("ARQUIVO"       ,edtArquivo.value);
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            clsJs   = jsString("lote");  
            clsJs.add("rotina"  , "impExcel"          );
            clsJs.add("login"   , jsPub[0].usr_login  );
            envPhp=clsJs.fim();
            fd = new FormData();
            fd.append("pedidocad"  , envPhp              );
            fd.append("arquivo"         , edtArquivo.files[0] );
            msg     = requestPedido("Trac_PedidoCad.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno == "OK" ){
              tamC=(retPhp[0]["dados"]).length;
              let achei;
              for( let lin=0; lin<tamC; lin++ ){
                achei=false;
                tblPlc.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                  if( row.cells[2].innerHTML==retPhp[0]["dados"][lin] ){
                    achei=true;  
                  };  
                });  
                if( achei==false ){
                  jsPlc.registros.push([              
                     chkds[0].PRODUTO                           // PRODUTO
                    ,retPhp[0]["dados"][lin]                    // PLACA
                    ,document.getElementById("edtPedID").value  // IDITEM
                    ,plcID
                  ]);  
                  /////////////////////////////////////////////////////////////////////
                  // Adiciona ou subtrai a qtade de placas informadas para cada produto
                  /////////////////////////////////////////////////////////////////////
                  addPlacaEmPedido(jsNmrs("edtPedID").inteiro().ret(),1);
                  plcID++;
                };  
              };  
              objPlc.ordenaJSon(jsPlc.indiceTable,false);              
              objPlc.montarBody2017();
            };  
            /////////////////////////////////////////////////////////////////////////////////////////
            // Mesmo se der erro mostro o erro, se der ok mostro a qtdade de registros atualizados //
            // dlgCancelar fecha a caixa de informacao de data                                     //
            /////////////////////////////////////////////////////////////////////////////////////////
            //gerarMensagemErro("BCD",retPhp[0].erro,"AVISO");    
          };  
        } catch(e){
          gerarMensagemErro("exc","ERRO NO ARQUIVO XML","AVISO");          
        };          
      };  
      
      
      
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Pedido</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieRed" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
        <div onClick="fncGravarPedido();"  class="btnImagemEsq bie10 bieAzul" style="margin-top:2px;"><i class="fa fa-check"> Gravar pedido</i></div>        
      </div>

      <div id="divCadastro">  
        <form method="post" class="center" id="frmPedidoCad" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
          <input type="hidden" id="sql" name="sql"/>
          <!-- aqui -->
          <div class="divAzul" style="width:77%;margin-left:12.5%;height: 640px;padding-top:1em;">
          <!-- aqui -->
            <div class="campotexto campo10">
              <input class="campo_input_titulo" id="edtNumPedido" 
                                                data-newrecord="000000"            
                                                type="text" 
                                                maxlength="12" disabled />
              <label class="campo_label campo_required" for="edtNumPedido">PEDIDO:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo" id="edtDtPedido" 
                                                data-newrecord="eval jsDatas(0).retDDMMYYYY()"
                                                type="text" 
                                                maxlength="10" disabled />
              <label class="campo_label campo_required" for="edtDtDocto">EMISSÂO:</label>
            </div>
            
            <div class="campotexto campo10">
              <input class="campo_input inputF10" id="edtCodFvr"
                                                  onBlur="CodFvrBlur(this);" 
                                                  onFocus="fvrFocus(this);" 
                                                  onClick="fvrF10Click(this);"
                                                  data-oldvalue=""
                                                  data-newrecord="0000"                                                  
                                                  autocomplete="off"
                                                  maxlength="6"
                                                  type="text"/>
              <label class="campo_label campo_required" for="edtCodFvr">CLIENTE:</label>
            </div>
            <div class="campotexto campo15">
              <input onBlur="fncCnpj(this.value);" class="campo_input input" id="edtCnpjCpf" type="text" value="24394036000165" maxlength="15"/>
              <label class="campo_label campo_required" for="edtCnpjCpf">CNPJ_CPF:</label>
            </div>
            <div class="campotexto campo40">
              <input class="campo_input input" id="edtDesFvr" type="text" maxlength="60"/>
              <label class="campo_label campo_required" for="edtDesFvr">NOME_CLIENTE:</label>
            </div>
            <div class="campotexto campo15">
              <select id="cbPrazoContrato" class="campo_input_combo" /> 
                <option value="12" selected >12x</option>
                <option value="24">24x</option>
                <option value="36">36x</option>
              </select>
              <label class="campo_label" for="cbPrazoContrato">PRAZO CONTRATO:</label>
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
              <input class="campo_input_titulo input" id="edtDesVnd" type="text" disabled />
              <label class="campo_label campo_required" for="edtDesVnd">VENDEDOR_NOME:</label>
            </div>
            <div class="campotexto campo12">
              <select id="cbFidelidade" class="campo_input_combo" >
                <option value="S" selected >SIM</option>            
                <option value="N">NAO</option>
              </select>
              <label class="campo_label" for="cbFidelidade">FIDELIDADE:</label>
            </div>
            <div class="campotexto campo15">
              <select id="cbStatus" class="campo_input_combo" /> 
                <option value="1" selected >ORCAMENTO</option>
                <option value="2">APROVAR</option>
              </select>
              <label class="campo_label" for="cbStatus">STATUS:</label>
            </div>
            <div class="campotexto campo12">
              <select id="cbDia" class="campo_input_combo" /> 
                <option value="05" selected >05</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
              </select>
              <label class="campo_label" for="cbDia">DIA VENCTO:</label>
            </div>

            <div class="campotexto campo15">
              <select id="cbInstPropria" class="campo_input_combo" /> 
                <option value="N" selected >NAO</option>
                <option value="S">SIM</option>
              </select>
              <label class="campo_label" for="cbInstPropria">INSTALAC PRÓPRIA:</label>
            </div>
            <div class="campotexto campo20">
              <select id="cbLocalInstala" class="campo_input_combo" /> 
                <option value="C" selected >CLIENTE</option>
                <option value="I">INTERNO</option>
              </select>
              <label class="campo_label" for="cbLocalInstala">LOCAL INSTALAÇÃO:</label>
            </div>
            <div class="campotexto campo65"></div>

            <div class="campotexto campo15">
              <!--
              O data data-vendalocacao eh para naum permitir entrar venda e locacao no mesmo contrato
              -->
              <select class="campo_input_combo" id="cbVendaLocacao" data-vendalocacao="*">
                <!--<option value="D">DEMONSTRAÇÃO</option>-->
                <option value="L">LOCAÇÃO</option>
                <option value="V" selected >VENDA</option>
              </select>
              <label class="campo_label" for="cbVendaLocacao">ITEM DE:</label>
            </div>

            <div class="campotexto campo10">
              <input class="campo_input inputF10" id="edtCodGm"
                                                  onBlur="codGmBlur(this);" 
                                                  onFocus="gmFocus(this);" 
                                                  onClick="gmF10Click(this);"
                                                  data-oldvalue=""
                                                  data-newrecord="0000"                                                
                                                  data-gmvalorvista="0,00"
                                                  data-gmvalorprazo="0,00"
                                                  data-gmvalorminimo="0,00"
                                                  autocomplete="off"                                                
                                                  maxlength="6"
                                                  type="text"/>
              <label class="campo_label campo_required" for="edtCodGm">PRODUTO:</label>
            </div>
            <div class="campotexto campo35">
              <input class="campo_input_titulo input" id="edtDesGm" type="text" disabled />
              <label class="campo_label campo_required" for="edtDesGm">NOME_PRODUTO:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input input" id="edtUnidades" 
                                               OnKeyPress="return mascaraInteiro(event);" 
                                               onBlur="fncUnidadesBlur(this.value);"
                                               data-newrecord="001"
                                               type="text" 
                                               maxlength="3"/>
              <label class="campo_label campo_required" for="edtUnidades">UNID(s)</label>
            </div>
            
            <div class="campotexto campo15">
              <select id="cbVistaPrazoPontual" class="campo_input_combo" 
                                        onChange="fncVistaPrazoPontualChange(this.value);" 
                                        onBlur="fncVistaPrazoPontualBlur(this.value);" /> 
                <option value="V" selected >VISTA</option>
                <option value="P">PRAZO</option>
              </select>
              <label class="campo_label" for="cbVistaPrazoPontual">PAGTO PONTUAL:</label>
            </div>
            <div class="campotexto campo15">
              <select id="cbPrazoPontual" class="campo_input_combo" /> 
                <option value="1" selected >1x</option>
                <option value="2">2x</option>
                <option value="3">3x</option>
                <option value="4">4x</option>
                <option value="5">5x</option>
              </select>
              <label class="campo_label" for="cbPrazoPontual">PRAZO PONTUAL</label>
            </div>
            
            <div class="campotexto campo15">
              <input class="campo_input_titulo edtDireita" id="edtVlrUnitario" 
                                                    onBlur="fncCasaDecimal(this,2);"
                                                    data-newrecord="0,00"
                                                    maxlength="15" 
                                                    type="text" disabled />
              <label class="campo_label campo_required" for="edtVlrUnitario">VALOR UNITÁRIO:</label>
            </div>
            <!--    
            <div class="campotexto campo15">
              <select id="cbDesconto" class="campo_input_combo"
                                      onChange="fncDescontoChance(this.value);" >
                <option value="000">0%</option>            
              </select>
              <label class="campo_label" for="cbDesconto">DESCONTO:</label>
            </div>
            -->
            <div class="campotexto campo15">
              <select id="cbDesconto" class="campo_input_combo" >
                <option value="000">0%</option>            
              </select>
              <label class="campo_label" for="cbDesconto">DESCONTO:</label>
            </div>
            
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrPedido" 
                                                    onBlur="fncCasaDecimal(this,2);"
                                                    data-newrecord="0,00"
                                                    data-vlrdolar="0.00"
                                                    maxlength="15" 
                                                    type="text" disabled />
              <label class="campo_label" for="edtVlrPedido">VLR PEDIDO</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrMensal" 
                                                    onBlur="fncCasaDecimal(this,2);"
                                                    data-newrecord="0,00"
                                                    data-vlrdolar="0.00"
                                                    maxlength="15" 
                                                    type="text" disabled />
              <label class="campo_label" for="edtVlrMensal">VLR MENSAL</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrPontual" 
                                                    onBlur="fncCasaDecimal(this,2);"
                                                    data-newrecord="0,00"
                                                    data-vlrdolar="0.00"
                                                    maxlength="15" 
                                                    type="text" disabled />
              <label class="campo_label" for="edtVlrPontual">VLR PONTUAL</label>
            </div>
            
            <div onClick="fncIncluir();"  class="btnImagemEsq bie10 bieAzul"><i class="fa fa-plus"> Incluir</i></div>
            <div onClick="fncRecalculo();"  class="btnImagemEsq bie10 bieAzul"><i class="fa fa-code"> Recalculo</i></div>            
            <div onClick="fncObsPedido();"  class="btnImagemEsq bie10 bieAzul"><i class="fa fa-sort-alpha-asc"> Obs</i></div>                        
            <div id="sctnPed">
            </div>  
            <!-- Quem indicou -->
            <div class="campotexto campo10">
              <input class="campo_input inputF10" id="edtCodInd"
                                                  OnKeyPress="return mascaraInteiro(event);"
                                                  onBlur="codIndBlur(this);" 
                                                  onFocus="indFocus(this);" 
                                                  onClick="indF10Click(this);"
                                                  data-oldvalue="0000"
                                                  data-newrecord="0000"                                                  
                                                  autocomplete="off"
                                                  type="text" />
              <label class="campo_label" for="edtCodInd">INDICADO POR:</label>
            </div>
            <div class="campotexto campo50">
              <input class="campo_input_titulo input" id="edtDesInd" data-newrecord="NAO SE APLICA" type="text" disabled />
              <label class="campo_label" for="edtDesInd">NOME</label>
            </div>
            <div class="inactive">
              <input id="edtPedID" value="0" type="text" />
              <!--<input id="edtCodFvr" value="0" type="text" />-->
              <input id="edtCodLgn" value="0" type="text" />
              <input id="edtVlrUnitComDesconto" value="0" type="text" />
              <textarea id="edtObs" value=""></textarea>                            
            </div>
          </div> 
        </form>
      </div>

      <div id="divPdd" class="divGrade">
      <!--
      <div id="divPdd" class="divAzul" style="width:40%;margin-left:12.5%;height:440px;padding-top:1em;">
      -->
        <div id="divPddParcela">
          <div class="campotexto campo100">
            <h2>Parcela</h2>
          </div>
        </div>
      </div>  
      
      <div id="divPlc" class="divGrade" style="height:41em;">        
        <div id="divPddPlaca">
          <div class="campotexto campo100">
            <h2>Placa</h2>
          </div>
        </div>
      </div>
      
      <div class="campotexto campo100">
        <div style="font-size:12px;width:30%;float:left;"><h2 style="text-align:center;">Importar placas</h2></div>
        <div class="teEsquerda"></div>
        <div class="custom_file_upload" style="font-size:12px;width:20%;float:left;">
          <input type="text" class="file" name="file_info" id="file_info">
          <div class="file_upload">
            <input type="file" id="edtArquivo" name="edtArquivo" onChange="document.getElementById('file_info').value=this.files[0].name;">
          </div>
        </div>
        <div onClick="fncAbrirPlc();"    class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-folder-open"> Abrir</i></div>                
      </div>
      
      <div class="campotexto campo100">
        <div style="font-size:12px;width:30%;float:left;"><h2 style="text-align:center;">Fim</h2></div>
      </div>
      
      
    </div>
    
  </body>
 </html>