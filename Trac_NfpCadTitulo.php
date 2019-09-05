<?php
  session_start();
  if( isset($_POST["cadnfp"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");      
      require("classPhp/validaCampo.class.php"); 
      /*  
      function fncPg($a, $b) {
       return $a["PDR_NOME"] > $b["PDR_NOME"];
      };

      function fncPt($a, $b) {
       return $a["PT_NOME"] > $b["PT_NOME"];
      };
      */
      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["cadnfp"]);
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
        if( $rotina=="imposto" ){
          $sql ="SELECT A.IMP_CFOP";
          $sql.="       ,CFO.CFO_NOME";          
          $sql.="       ,A.IMP_CSTICMS";
          $sql.="       ,A.IMP_ALIQICMS";
          $sql.="       ,A.IMP_REDUCAOBC";
          $sql.="       ,A.IMP_CSTIPI";
          $sql.="       ,A.IMP_ALIQIPI";
          $sql.="       ,A.IMP_CSTPIS";
          $sql.="       ,A.IMP_ALIQPIS";
          $sql.="       ,A.IMP_CSTCOFINS";
          $sql.="       ,A.IMP_ALIQCOFINS";
          $sql.="       ,A.IMP_ALIQST";
          $sql.="       ,A.IMP_ALTERANFP";
          $sql.="  FROM IMPOSTO A";
          $sql.="  LEFT OUTER JOIN CFOP CFO ON A.IMP_CFOP=CFO.CFO_CODIGO";
          $sql.=" WHERE (A.IMP_UFDE='".$lote[0]->uforigem."')";          
          $sql.="   AND (A.IMP_UFPARA='".$lote[0]->ufdestino."')";
          $sql.="   AND (A.IMP_CODNCM='".$lote[0]->codncm."')";
          $sql.="   AND (A.IMP_CODCTG='".$lote[0]->codctg."')";
          $sql.="   AND (A.IMP_ENTSAI='".$lote[0]->entsai."')";
          $sql.="   AND (A.IMP_CODNO='".$lote[0]->codno."')";          
          $sql.="   AND (A.IMP_CODEMP=".$lote[0]->codemp.")";
          $sql.="   AND (A.IMP_CODFLL=".$lote[0]->codfll.")";
          $sql.="   AND (A.IMP_ATIVO='S')";  
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["retorno"] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM IMPOSTO PARAMETRIZADO PARA ESTA OPCAO"}]';              
          } else {  
            $achei=count($retCls["dados"]);
            if( $achei>0 ){
              $retorno='[{"retorno":"OK"
                         ,"tblImp":'.json_encode($retCls["dados"][0]).'
                         ,"achei" : '.$achei.'
                         ,"erro":""}]'; 
            } else {
              $retorno='[{"retorno":"OK"
                         ,"achei" : "0"
                         ,"erro":"Produto sem parametrizacao fiscal"}]'; 
            };    
          };           
        };    
        /////////////////////////////////////////////////////
        // Buscando a serie da NF e parametros complementares
        /////////////////////////////////////////////////////
        if( $rotina=="buscaSnf" ){
          $sql ="SELECT A.SNF_CODIGO,A.SNF_NFPROXIMA,A.SNF_CODTD,TD.TD_NOME,A.SNF_ENTSAI,A.SNF_LIVRO";
          $sql.="  FROM SERIENF A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO TD ON TD.TD_CODIGO=A.SNF_CODTD";
          $sql.=" WHERE ((A.SNF_ENTSAI='".$lote[0]->entsai."')"; 
          $sql.="   AND (A.SNF_CODTD='NFP')";
          $sql.="   AND (A.SNF_CODFLL=".$lote[0]->codfll.")";
          $sql.="   AND (A.SNF_ATIVO='S'))"; 
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM NUMERO DE TALONARIO DISPONIVEL PARA LANCAMENTO"}]';              
          } else {  
            $retorno='[{"retorno":"OK"
                       ,"tblSnf":'.json_encode($retCls["dados"][0]).'
                       ,"erro":""}]'; 
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
    <title>Cadastro NF Produto</title>
    <style id="meuCss">
    </style>  
    <!-- 
    -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <link rel="stylesheet" href="css/Acordeon.css">
    <script src="js/js2017.js"></script>
    <script src="js/clsTab2017.js"></script>        
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaFavorecidoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaBancoF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaNaturezaOperacaoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaProdutoF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaTransportadoraF10.js"></script>        
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        ////////////////////////////////////////////////////////////
        // Este obj serve apenas para calcular a linha de um produto
        ////////////////////////////////////////////////////////////
        
        /////////////////////////////////////
        // Prototype para preencher campos //
        /////////////////////////////////////
        $doc("frmNfp").newRecord("data-newrecord");
        $doc("nfpi").newRecord("data-newrecorditem");
        $doc("edtCodNo").foco();        
        if( jsPub[0].emp_fllunica=="S" ){
          jsCmpAtivo("edtCodFll").remove("campo_input").add("campo_input_titulo").disabled(true);
        };

        jsIte={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"labelCol"       : "PRODUTO"
                      ,"fieldType"      : "str"
                      //,"formato"        : ["i4"]                      
                      //,"align"          : "center"                                      
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "PRODUTO_NOME"
                      ,"fieldType"      : "str"
                      //,"formato"        : ["i4"]                      
                      //,"align"          : "center"                                      
                      ,"tamGrd"         : "25em"
                      ,"tamImp"         : "80"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "CFOP"
                      ,"fieldType"      : "str"
                      //,"formato"        : ["i4"]                      
                      //,"align"          : "center"                                      
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "15"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "VLRITEM"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"somarImp"       : "S"                      
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "VLRIPI"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"somarImp"       : "S"                      
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "VLRST"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"somarImp"       : "S"                      
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "TOTAL"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"somarImp"       : "S"                      
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
                      
            ,{"id":8  ,"labelCol"       : "JSON"
                      ,"fieldType"      : "str"
                      //,"formato"        : ["i4"]                      
                      //,"align"          : "center"                                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":9 ,"labelCol"       : "ID"
                      ,"fieldType"      : "str"
                      //,"align"          : "center"                                      
                      ,"tamGrd"         : "2em"     //--03abr2019
                      ,"tamImp"         : "10"
                      ,"padrao":0}
          ]
          ,
          "botoesH":[
             {"texto":"Excluir"       ,"name":"iteExcluir"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-minus"         }          
            ,{"texto":"Imprimir"      ,"name":"pedImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"         }                                
            ,{"texto":"Excel"         ,"name":"pedExcel"      ,"onClick":"5"  ,"enabled":true ,"imagem":"fa fa-file-excel-o"  }        
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)                        
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"            // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"             // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmIte"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaIte"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmIte"                  // Onde vai ser gerado o fieldSet       
          ,"divModalDentro" : "sctnIte"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblIte"                  // Nome da table
          ,"prefixo"        : "ped"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"nChecks"        : false          
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "110em"                   // Tamanho da table
          ,"height"         : "30em"                    // Altura da table
          ,"tableLeft"      : "2px"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "ITEM"                    // Titulo do relatório
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


        
        var jsTab={
          "pai"         : "appAba"  //Onde vai ser appendado o html gerado
          ,"idStyle"    : "meuCss"  //id da tag style
          ,"opcExcluir" : "N"       //Se vai existir a opção excluir        
          ,"abas":[ 
             {"id":0  ,"nomeAba"      : "parcela" 
                      ,"labelAba"     : "Parcelamento"
                      ,"table"        : "tblPrcl"
                      ,"widthTable"   : "45em" 
                      ,"heightTable"  : "18em"                     
                      ,"widthAba"     : "12em" 
                      ,"rolagemVert"  : true 
                      ,"somarCols":[2]
                      ,"head":[  { "labelCol"   : "PARC"     
                                  ,"width"      : "4em"  
                                  ,"fieldType"  : "int"
                                  ,"formato"    : "i3"
                                  ,"editar"     : "N"
                                  ,"classe"     : ""}
                                ,{ "labelCol"   : "VENCTO"   
                                  ,"width"      : "10em"  
                                  ,"fieldType"  : "dat"
                                  ,"editar"     : "S"
                                  ,"classe"     : "edtData"}
                                ,{ "labelCol"   : "VALOR"    
                                  ,"width"      : "12em" 
                                  ,"fieldType"  : "flo"
                                  ,"editar"     : "S"
                                  ,"classe"     : "edtDireita"}
                              ]
             }
          ]
        };  
        minhaAba=new clsTab(jsTab);
        minhaAba.htmlTab();  
        fncColunas();
      });
      //
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      //var clsChecados;                // Classe para montar Json  
      //var chkds;                      // Guarda todos registros checados na table 
      //var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var objPadF10;                  // Obrigatório para instanciar o JS PadraoF10      
      var objFvrF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var objPrdF10;                  // Obrigatório para instanciar o JS ProdutoF10      
      var objNoF10;                   // Obrigatório para instanciar o JS NaturezaOperacaoF10
      var objBncF10;                  // Obrigatório para instanciar o JS BancoF10            
      var objTrnF10;                  // Obrigatório para instanciar o JS TransportadoraF10                  
      var objIte;                     // Obrigatório para instanciar o JS ITEM
      var jsIte;                      // Obj principal da classe clsTable2017
      var iteID=1;                    // Devido exclusao na table de item
      var objCol;                     // Posicao das colunas da grade ITEM que vou precisar neste formulario      
      var minhaAba;
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado 
      var objCalc;
      //////////////////////////////////////////////////////////////////////
      // Aqui sao as colunas da table ITEM que vou precisar neste formulario
      //////////////////////////////////////////////////////////////////////
      function fncColunas(){
        try{          
          let buscaCol= new clsObterColunas(jsIte,["CFOP","ID","JSON","PRODUTO","PRODUTO_NOME","VLRITEM","VLRIPI","VLRST","TOTAL"]);
          buscaCol.appFilter();
          if( buscaCol.getNumCols() != 0  ){
            throw "Não localizado coluna PRODUTO,PRODUTO_NOME,JSON,ID!";   
          } else {
            objCol=buscaCol.getObjeto();
          };
        } catch(e){
          gerarMensagemErro("ite",e,{cabec:"Erro"});          
        };  
      };
      function calculaItem(opc){
        /////////////////////////////////////////////
        // Opc 1=Calcula a linha e atualiza inputs
        //     2=Devolve o objeto para novos calculos
        //     3=Devolve o objeto com valores zerados
        /////////////////////////////////////////////
        objCalc = { edtAliqCofins   : jsConverte("#edtAliqCofins").dolar(true)
                    ,edtAliqIcms    : jsConverte("#edtAliqIcms").dolar(true)
                    ,edtAliqIpi     : jsConverte("#edtAliqIpi").dolar(true)
                    ,edtAliqPis     : jsConverte("#edtAliqPis").dolar(true)                               
                    ,edtAliqSt      : jsConverte("#edtAliqSt").dolar(true)
                    ,edtBcIcms      : jsConverte("#edtBcIcms").dolar(true)
                    ,edtVlrCofins   : jsConverte("#edtVlrCofins").dolar(true)
                    ,edtVlrDesconto : jsConverte("#edtVlrDesconto").dolar(true)
                    ,edtVlrFrete    : jsConverte("#edtVlrFrete").dolar(true)
                    ,edtVlrIcms     : jsConverte("#edtVlrIcms").dolar(true)
                    ,edtVlrIpi      : jsConverte("#edtVlrIpi").dolar(true)
                    ,edtVlrItem     : jsConverte("#edtVlrItem").dolar(true)                    
                    ,edtVlrOutras   : jsConverte("#edtVlrOutras").dolar(true)
                    ,edtVlrPis      : jsConverte("#edtVlrPis").dolar(true)                               
                    ,edtVlrSt       : jsConverte("#edtVlrSt").dolar(true)
                    ,edtVlrSeguro   : jsConverte("#edtVlrSeguro").dolar(true)
                    ,edtUnidades    : ( jsConverte("#edtUnidades").dolar(true)<1 ? 1 : jsConverte("#edtUnidades").dolar(true) )
                    ,edtVlrUnitario : jsConverte("#edtVlrUnitario").dolar(true)
                  };
        if( opc==1 ){
          objCalc.edtVlrItem    =( objCalc.edtVlrUnitario*objCalc.edtUnidades );
          objCalc.edtBcIcms     =( objCalc.edtVlrItem+objCalc.edtVlrFrete+objCalc.edtVlrSeguro+objCalc.edtVlrOutras-objCalc.edtVlrDesconto );
          objCalc.edtVlrCofins  =( objCalc.edtBcIcms*(objCalc.edtAliqCofins/100) );
          objCalc.edtVlrIcms    =( objCalc.edtBcIcms*(objCalc.edtAliqIcms/100) );
          objCalc.edtVlrIpi     =( objCalc.edtBcIcms*(objCalc.edtAliqIpi/100) );        
          objCalc.edtVlrPis     =( objCalc.edtBcIcms*(objCalc.edtAliqPis/100) );        
          objCalc.edtVlrSt      =( objCalc.edtBcIcms*(objCalc.edtAliqSt/100) );                
          
          ///////////////////////////////////////
          // Retornando os valores para os inputs
          ///////////////////////////////////////  
          for (var key in objCalc) {
            $doc(key).value=jsConverte(parseFloat(objCalc[key]).toFixed(2)).real();
          };  
        };
        if( opc==2 ){
          return objCalc;
        };  
        if( opc==3 ){
          for (var key in objCalc){
            objCalc[key]=0;
          }  
          return objCalc;          
        };  
      };  
      function fimItem(){
        if( $doc("edtCodPrd").value=="*" ){
          $doc("edtNumPar").foco();
        };
      };  
      ///////////////////////////////////
      //     AJUDA NATUREZA OPERACAO   //
      ///////////////////////////////////
      function noFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function noF10Click(obj){ 
        fNaturezaOperacaoF10(0,obj.id,"edtDtDocto",100,{ativo:"S" } ); 
      };
      function RetF10tblNo(arr){
        $doc("edtCodNo").value  = arr[0].CODIGO;
        $doc("edtDesNo").value  = arr[0].DESCRICAO;
        $doc("edtCodPtp").value = arr[0].TIPO;
        $doc("edtCodNo").setAttribute("data-codpt",arr[0].CODPT);
        $doc("edtCodNo").setAttribute("data-codcc",arr[0].CODCC);        
        $doc("edtCodNo").setAttribute("data-oldvalue",arr[0].CODIGO);
        campoDtEntrada(arr[0].TIPO);
      };
      function codNoBlur(obj){
        let elOld = $doc(obj.id).getAttribute("data-oldvalue");
        let elNew = $doc(obj.id).value;
        if( elOld != elNew ){
          let arr = fNaturezaOperacaoF10(1,obj.id,"edtDtDocto",100,
            {codno  : elNew
             ,ativo  : "S"} 
          ); 
          $doc(obj.id).value       = ( arr.length == 0 ? "*"  : arr[0].CODIGO                 );
          $doc("edtDesNo").value   = ( arr.length == 0 ? "*"  : arr[0].DESCRICAO              );
          $doc("edtCodPtp").value  = ( arr.length == 0 ? "*"  : arr[0].TIPO                   );
          $doc(obj.id).setAttribute("data-codpt",( arr.length == 0 ? "*" : arr[0].CODPT )     );
          $doc(obj.id).setAttribute("data-codcc",( arr.length == 0 ? "*" : arr[0].CODCC )     );
          $doc(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "*" : arr[0].CODIGO ) );
          campoDtEntrada(arr[0].TIPO);
        };
      };
      function campoDtEntrada(tp){
        if( tp="CR" ){
          jsCmpAtivo("edtDtEntrada").remove("campo_input inputF10").add("campo_input_titulo").disabled(true);
        } else {  
          jsCmpAtivo("edtNumNf").remove("campo_input_titulo").add("campo_input").disabled(false);  
        };
      };  
      //
      function buscarBd(){
        document.getElementById("edtDtDocto").setAttribute("data-codcmp",jsDatas("edtDtDocto").retYYYYMM());
        document.getElementById("edtEntSai").value=(document.getElementById("edtCodPtp").value=="CP" ? "E" : "S");
        document.getElementById("lblCodFvr").innerHTML=(document.getElementById("edtCodPtp").value=="CP" ? "FORNECEDOR" : "CLIENTE");
        try{  
          clsJs=jsString("lote");            
          clsJs.add("login"   , jsPub[0].usr_login       );
          clsJs.add("rotina"  , "buscaSnf"               );
          clsJs.add("codfll"  , $doc("edtCodFll").value  );
          clsJs.add("entsai"  , $doc("edtEntSai").value  );
          var fd = new FormData();
          fd.append("cadnfp" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            document.getElementById("edtCodTd").value       = retPhp[0].tblSnf["SNF_CODTD"];
            document.getElementById("edtDesTd").value       = retPhp[0].tblSnf["TD_NOME"];
            document.getElementById("edtCodSnf").value      = retPhp[0].tblSnf["SNF_CODIGO"];
            document.getElementById("edtNumNf").value       = jsNmrs(retPhp[0].tblSnf["SNF_NFPROXIMA"]).emZero(6).ret();
            //document.getElementById("edtInformarNf").value  = retPhp[0].tblSnf["SNF_INFORMARNF"];
            document.getElementById("edtLivro").value       = retPhp[0].tblSnf["SNF_LIVRO"];
            jsCmpAtivo("edtCodTd").remove("campo_input inputF10").add("campo_input_titulo").disabled(true);

            if( $doc("edtEntSai").value=="S" ){
              jsCmpAtivo("edtNumNf").remove("campo_input inputF10").add("campo_input_titulo").disabled(true); 
              $doc("edtObservacao").value=$doc("edtDesNo").value+" CONFORME NF"+$doc("edtNumNf").value;
              $doc("edtCodFvr").foco(); 
            } else {
              $doc("edtNumNf").foco();               
            }    

          } else {
            gerarMensagemErro("Ami",retPhp[0].erro,{cabec:"Aviso"});              
          };    
        }catch(e){
          gerarMensagemErro("catch",e.message,"Erro");
        };
      };
      ////////////////////////////
      //  AJUDA PARA FAVORECIDO //
      ////////////////////////////
      function fvrFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fvrF10Click(obj){ 
        fFavorecidoF10(0,obj.id,"edtCodFc",100); 
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtCodFvr").value    = arr[0].CODIGO;
        document.getElementById("edtDesFvr").value    = arr[0].DESCRICAO;
        document.getElementById("edtCodCdd").value    = arr[0].CODCDD;
        document.getElementById("edtCodFvr").setAttribute("data-codctg",arr[0].CATEGORIA);
        document.getElementById("edtCodFvr").setAttribute("data-fisjur",arr[0].FJ);
        document.getElementById("edtCodFvr").setAttribute("data-emissor",arr[0].CNPJCPF);        
        document.getElementById("edtCodFvr").setAttribute("data-oldvalue",arr[0].CODIGO);
        atualizaUf(document.getElementById("edtEntSai").value,jsPub[0].emp_codest,arr[0].UF);
      };
      function codFvrBlur(obj){
        let elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        let elNew = jsConverte("#"+obj.id).inteiro();
        if( elOld != elNew ){
          var ret = fFavorecidoF10(1,obj.id,"edtCodFc",100); 
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "0000"          : ret[0].CODIGO         );
          document.getElementById("edtDesFvr").value  = ( ret.length == 0 ? "*"             : ret[0].DESCRICAO      );
          document.getElementById("edtCodCdd").value  = ( ret.length == 0 ? "*"             : ret[0].CODCDD         );
          document.getElementById("edtCodFvr").setAttribute("data-codctg",(ret.length == 0 ? "*" : ret[0].CATEGORIA));          
          document.getElementById("edtCodFvr").setAttribute("data-fisjur",(ret.length == 0 ? "*" : ret[0].FJ)       );          
          document.getElementById("edtCodFvr").setAttribute("data-emissor",(ret.length == 0 ? "*" : ret[0].CNPJCPF) );                    
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "000" : ret[0].CODIGO )  );
          atualizaUf(document.getElementById("edtEntSai").value,jsPub[0].emp_codest,arr[0].UF,ret[0].CNPJCPF);          
        };
      };
      function atualizaUf(entSai,ufEmp,ufFvr,ufEmissor){
        if( entSai=="S" ){
          document.getElementById("edtCodFvr").setAttribute("data-uforigem",ufEmp);  
          document.getElementById("edtCodFvr").setAttribute("data-ufdestino",ufFvr);  
          document.getElementById("edtCodFvr").setAttribute("data-emissor",jsPub[0].emp_cnpj);  
        } else {
          document.getElementById("edtCodFvr").setAttribute("data-uforigem",ufFvr);  
          document.getElementById("edtCodFvr").setAttribute("data-ufdestino",ufEmp);  
        };    
      };  
      ///////////////////////////////
      //  AJUDA PARA FORMACOBRANCA //
      ///////////////////////////////
      function fcFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function fcF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodBnc"
                      ,topo:100
                      ,tableBd:"FORMACOBRANCA"
                      ,fieldCod:"A.FC_CODIGO"
                      ,fieldDes:"A.FC_NOME"
                      ,fieldAtv:"A.FC_ATIVO"
                      ,typeCod :"str" 
                      ,divWidth:"36%"
                      ,tbl:"tblFc"}
        );
      };
      function RetF10tblFc(arr){
        document.getElementById("edtCodFc").value  = arr[0].CODIGO;
        document.getElementById("edtDesFc").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodFc").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codFcBlur(obj){
        let elOld = $doc(obj.id).getAttribute("data-oldvalue");
        let elNew = $doc(obj.id).value;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodBnc"
                                  ,topo:100
                                  ,tableBd:"FORMACOBRANCA"
                                  ,fieldCod:"A.FC_CODIGO"
                                  ,fieldDes:"A.FC_NOME"
                                  ,fieldAtv:"A.FC_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblFc"}
          );
          document.getElementById(obj.id).value     = ( ret.length == 0 ? "*"  : ret[0].CODIGO                    );
          document.getElementById("edtDesFc").value = ( ret.length == 0 ? "*"      : ret[0].DESCRICAO             );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "*" : ret[0].CODIGO )  );
        };
      };
      ///////////////////////////////
      //     AJUDA PARA BANCO      //
      ///////////////////////////////
      function bncFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function bncF10Click(obj){ 
        fBancoF10(0,obj.id,"edtCodTrn",100,{codemp: jsPub[0].emp_codigo,ativo:"S" } ); 
      };
      function RetF10tblBnc(arr){
        $doc("edtCodBnc").value  = arr[0].CODIGO;
        $doc("edtDesBnc").value  = arr[0].DESCRICAO;
        $doc("edtCodBnc").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codBncBlur(obj){
        let elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        let elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          let arr = fBancoF10(1,obj.id,"edtCodTrn",100,
            {codbnc  : elNew
             ,codemp : jsPub[0].emp_codigo
             ,ativo  : "S"} 
          ); 
          document.getElementById(obj.id).value       = ( arr.length == 0 ? "0000"  : jsConverte(arr[0].CODIGO).emZero(4) );   
          document.getElementById("edtDesBnc").value  = ( arr.length == 0 ? "*"     : arr[0].DESCRICAO                    );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )       );
        };
      };
      
      ////////////////////////////////////
      //     AJUDA PARA TRANSPORTADORA  //
      ////////////////////////////////////
      function trnFocus(obj){ 
        $doc(obj.id).setAttribute("data-oldvalue",$doc(obj.id).value); 
      };
      function trnF10Click(obj){ 
        fTransportadoraF10(0,obj.id,"edtVolume",100,{ativo:"S" } ); 
      };
      function RetF10tblTrn(arr){
        $doc("edtCodTrn").value  = arr[0].CODIGO;
        $doc("edtDesTrn").value  = arr[0].DESCRICAO;
        $doc("edtCodTrn").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codTrnBlur(obj){
        let elOld = jsNmrs($doc(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        let elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          let arr = fTransportadoraF10(1,obj.id,"edtVolume",100,
            {codtrn  : elNew
             ,ativo  : "S"} 
          ); 
          $doc(obj.id).value       = ( arr.length == 0 ? "0000"  : jsConverte(arr[0].CODIGO).emZero(4) );   
          $doc("edtDesBnc").value  = ( arr.length == 0 ? "*"     : arr[0].DESCRICAO                    );
          $doc(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )       );
        };
      };
      ///////////////////////////////////
      //        AJUDA PARA PRODUTO     //
      ///////////////////////////////////
      function prdFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function prdF10Click(obj){ 
        fProdutoF10(0,obj.id,"null",100,{
            codemp  : jsPub[0].emp_codigo
            ,ativo  : "S"
            ,entsai : document.getElementById("edtEntSai").value
          } 
        ); 
      };
      function RetF10tblPrd(arr){
        document.getElementById("edtCodPrd").value  = arr[0].CODIGO;
        document.getElementById("edtDesPrd").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodPrd").setAttribute("data-oldvalue",arr[0].CODIGO);
        atualizaPrd(arr);
      };
      function codPrdBlur(obj){
        let elOld = $doc(obj.id).getAttribute("data-oldvalue");
        let elNew = $doc(obj.id).value;
        if( elOld != elNew ){
          let arr = fProdutoF10(1,obj.id,"null",100,
            { codemp  : jsPub[0].emp_codigo
              ,codprd : elNew
              ,entsai : document.getElementById("edtEntSai").value
              ,ativo  : "S"
            } 
          ); 
          document.getElementById(obj.id).value       = ( arr.length == 0 ? "*"  : arr[0].CODIGO                  );
          document.getElementById("edtDesPrd").value   = ( arr.length == 0 ? "*"  : arr[0].DESCRICAO              );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "*" : arr[0].CODIGO )  );
          atualizaPrd(arr);
        };
      };
      function atualizaPrd(reg){
        document.getElementById("edtCodPrd").setAttribute("data-codncm",reg[0].CODNCM);  
        document.getElementById("edtCodPrd").setAttribute("data-codemb",reg[0].CODEMB);  
        document.getElementById("edtVlrUnitario").value = reg[0].VLRVENDA;
        document.getElementById("edtUnidades").value    = '1,00';
        document.getElementById("edtVlrItem").value     = reg[0].VLRVENDA;
        try{  
          clsJs=jsString("lote");            
          clsJs.add("login"     , jsPub[0].usr_login                                                  );
          clsJs.add("rotina"    , "imposto"                                                           );
          clsJs.add("uforigem"  , document.getElementById("edtCodFvr").getAttribute("data-uforigem")  );
          clsJs.add("ufdestino" , document.getElementById("edtCodFvr").getAttribute("data-ufdestino") );
          clsJs.add("codncm"    , document.getElementById("edtCodPrd").getAttribute("data-codncm")    );
          clsJs.add("codctg"    , document.getElementById("edtCodFvr").getAttribute("data-codctg")    );
          clsJs.add("entsai"    , document.getElementById("edtEntSai").value                          );
          clsJs.add("codno"     , document.getElementById("edtCodNo").value                           );
          clsJs.add("codfll"    , document.getElementById("edtCodFll").value                          );
          clsJs.add("codemp"    , jsPub[0].emp_codigo                                                 );
          var fd = new FormData();
          fd.append("cadnfp" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            if( parseInt(retPhp[0].achei)==0 ){
              gerarMensagemErro("nfp",retPhp[0].erro,{cabec:"Aviso"});              
            } else {  
              let tbl=retPhp[0].tblImp;
              $doc("edtCodCfo").value     = tbl["IMP_CFOP"];
              $doc("edtDesCfo").value     = tbl["CFO_NOME"];
              $doc("edtAliqIcms").value   = jsConverte(tbl["IMP_ALIQICMS"]).real();
              $doc("edtAliqIpi").value    = jsConverte(tbl["IMP_ALIQIPI"]).real();              
              $doc("edtAliqPis").value    = jsConverte(tbl["IMP_ALIQPIS"]).real();              
              $doc("edtAliqCofins").value = jsConverte(tbl["IMP_ALIQCOFINS"]).real();
              $doc("edtAliqSt").value     = jsConverte(tbl["IMP_ALIQST"]).real();
              /////////////////////////////////////////////////
              // Guardando informacoes no campo data do produto
              /////////////////////////////////////////////////
              document.getElementById("edtCodPrd").setAttribute("data-csticms"  ,tbl["IMP_CSTICMS"]   );
              document.getElementById("edtCodPrd").setAttribute("data-cstipi"   ,tbl["IMP_CSTIPI"]    );
              document.getElementById("edtCodPrd").setAttribute("data-cstpis"   ,tbl["IMP_CSTPIS"]    );
              document.getElementById("edtCodPrd").setAttribute("data-cstcofins",tbl["IMP_CSTCOFINS"] );
              document.getElementById("edtCodPrd").setAttribute("data-alteranfp",tbl["IMP_ALTERANFP"] );
              ///////////////////////////////////////////////////////////////
              // Olhando aqui se o usuario pode alterar os parametros fiscais
              ///////////////////////////////////////////////////////////////
              let edtRemove = ( tbl["IMP_ALTERANFP"]=="N" ? "campo_input inputF10" : "campo_input_titulo" );
              let edtAdd    = ( tbl["IMP_ALTERANFP"]=="N" ? "campo_input_titulo" : "campo_input inputF10" );
              let edts      = [  "edtAliqCofins"
                                ,"edtAliqIcms"
                                ,"edtAliqIpi"
                                ,"edtAliqPis"                                
                                ,"edtAliqSt"
                                ,"edtCodCfo"
                              ];
              
              let edtFoco   = ( tbl["IMP_ALTERANFP"]=="N" ? "edtVlrUnitario" : "edtCodCfo" );
              for( let lin=0;lin<edts.length;lin++ ){
                jsCmpAtivo( edts[lin] ).remove( edtRemove ).add( edtAdd ).disabled( (tbl["IMP_ALTERANFP"]=="N" ? true : false) );                                
              };
              $doc(edtFoco).foco();    
            };    
          } else {
            gerarMensagemErro("nfp",retPhp[0].erro,{cabec:"Aviso"});              
          };    
        }catch(e){
          gerarMensagemErro("catch",e.message,"Erro");
        };
      };  
      //////////////////////
      //  AJUDA PARA CFOP //
      //////////////////////
      function cfoFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function cfoF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodIcm"
                      ,topo:100
                      ,tableBd:"CFOP"
                      ,fieldCod:"A.CFO_CODIGO"
                      ,fieldDes:"A.CFO_NOME"
                      ,fieldAtv:"A.CFO_ATIVO"
                      ,typeCod :"str" 
                      ,tbl:"tblCfo"
                      ,where:" AND A.CFO_ENTSAI = '" +document.getElementById("edtEntSai").value+"'"
                    }
        );
      };
      function RetF10tblCfo(arr){
        document.getElementById("edtCodCfo").value  = arr[0].CODIGO;
        document.getElementById("edtDesCfo").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCfo").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCfoBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodIcm"
                                  ,topo:100
                                  ,tableBd:"CFOP"
                                  ,fieldCod:"A.CFO_CODIGO"
                                  ,fieldDes:"A.CFO_NOME"
                                  ,fieldAtv:"A.CFO_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblCfo"
                                  ,where:" AND A.CFO_ENTSAI = '" +document.getElementById("edtEntSai").value+"'"
                                }
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "5.102"  : ret[0].CODIGO             );
          document.getElementById("edtDesCfo").value  = ( ret.length == 0 ? "VENDA DE MERC.ADQ.DE TERC.DENTRO ESTADO"      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "5.102" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////////////////////////////////
      // Total da NF
      ////////////////////////////////////////////////////////
      function fncTotalNf(){
        let totalNf=0;
        tblIte.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr,tam) {          
          totalNf+=jsConverte(row.cells[objCol.TOTAL].innerHTML).dolar(true);
        });
        $doc("edtVlrEvento").value=jsConverte(parseFloat(totalNf).toFixed(2)).real();
      };  
      ///////////////////////////////////////////////////////
      // Vendo aqui se vou incluir o item na grade
      ///////////////////////////////////////////////////////
      function incluirPrd(altNfp){
        if( altNfp=="N" )
          fncIncluir();  
      };  
      ///////////////////////////////////////////////////////
      // Enchendo a grade com produto + servicos relacionados
      ///////////////////////////////////////////////////////
      function fncIncluir(){
        try{  
          ////////////////////////////////////
          // Checagem basica em ordem de input
          ////////////////////////////////////
          msg = new clsMensagem("Erro");
          msg.floMaiorIgualZero("ALIQ_COFINS"      , $doc("edtAliqCofins").value );
          msg.floMaiorIgualZero("ALIQ_ICMS"        , $doc("edtAliqIcms").value );
          msg.floMaiorIgualZero("ALIQ_IPI"         , $doc("edtAliqIpi").value );
          msg.floMaiorIgualZero("ALIQ_PIS"         , $doc("edtAliqPis").value );                               
          msg.floMaiorIgualZero("ALIQ_ST"          , $doc("edtAliqSt").value );
          msg.floMaiorIgualZero("BASE_CALCULO"     , $doc("edtBcIcms").value );
          msg.diferente("NOME_CFOP"                , $doc("edtDesCfo").value,"*");          
          msg.diferente("NOME_PRODUTO"             , $doc("edtDesPrd").value,"*");
          msg.floMaiorIgualZero("VALOR_COFINS"     , $doc("edtVlrCofins").value );
          msg.floMaiorIgualZero("VALOR_DESCONTO"   , $doc("edtVlrDesconto").value );
          msg.floMaiorIgualZero("VALOR_FRETE"      , $doc("edtVlrFrete").value );
          msg.floMaiorIgualZero("VALOR_ICMS"       , $doc("edtVlrIcms").value );
          msg.floMaiorIgualZero("VALOR_IPI"        , $doc("edtVlrIpi").value );
          msg.floMaiorZero("VALOR_ITEM"            , $doc("edtVlrItem").value    );          
          msg.floMaiorIgualZero("VALOR_OUTRAS"     , $doc("edtVlrOutras").value );
          msg.floMaiorIgualZero("VALOR_PIS"        , $doc("edtVlrPis").value );                               
          msg.floMaiorIgualZero("VALOR_ST"         , $doc("edtVlrSt").value );
          msg.floMaiorIgualZero("VALOR_SEGURO"     , $doc("edtVlrSeguro").value );
          msg.floMaiorZero("UNIDADES"              , $doc("edtUnidades").value    );          
          msg.floMaiorZero("VALOR_UNITARIO"        , $doc("edtVlrUnitario").value );
          msg.diferente("COD_NCM"                  , $doc("edtCodPrd").getAttribute("data-codncm"),"*");
          msg.diferente("CST_ICMS"                 , $doc("edtCodPrd").getAttribute("data-csticms"),"*");
          msg.diferente("CST_IPI"                  , $doc("edtCodPrd").getAttribute("data-cstipi"),"*");
          msg.diferente("CST_PIS"                  , $doc("edtCodPrd").getAttribute("data-cstpis"),"*");
          msg.diferente("CST_COFINS"               , $doc("edtCodPrd").getAttribute("data-cstcofins"),"*");
          msg.diferente("EMBALAGEM"                , $doc("edtCodPrd").getAttribute("data-codemb"),"*");
          ////////////////////////////////////////////////
          // NAO ACEITANDO O MESMO PRODUTO MAIS DE UMA VEZ
          ////////////////////////////////////////////////  
          tblIte.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr,tam) {          
            if( row.cells[objCol.PRODUTO].innerHTML==$doc("edtCodPrd").value )
              throw "PRODUTO JA CADASTRADO NA GRADE!";
          });
          //
          //
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            //////////////////////////////////////////////////////
            // Buscando os valores ja formatados como moeda(dolar)
            //////////////////////////////////////////////////////
            let calc=calculaItem(2);
            ///////////////////////////////////////////////////////////////////////////////////////
            // Guardando os campos que naum estao na tela em um JSON
            /////////////////////////////////////////////////////////////////////////////////////// 
            let campos='['
              +'{"cstcofins":"'   + document.getElementById("edtCodPrd").getAttribute("data-cstcofins")
              +'","csticms":"'    + document.getElementById("edtCodPrd").getAttribute("data-csticms")              
              +'","cstipi":"'     + document.getElementById("edtCodPrd").getAttribute("data-cstipi")
              +'","cstpis":"'     + document.getElementById("edtCodPrd").getAttribute("data-cstpis")
              +'","codemb":"'     + document.getElementById("edtCodPrd").getAttribute("data-codemb")
              +'","vlrunitario":' + calc.edtVlrUnitario
              +',"vlrfrete":'     + calc.edtVlrFrete
              +',"vlrseguro":'    + calc.edtVlrSeguro
              +',"vlroutras":'    + calc.edtVlrOutras
              +',"vlrdesconto":'  + calc.edtVlrDesconto
              +',"unidades":'     + calc.edtUnidades
              +',"aliqcofins":'   + calc.edtAliqCofins
              +',"vlrcofins":'    + calc.edtVlrCofins
              +',"aliqicms":'     + calc.edtAliqIcms
              +',"vlricms":'      + calc.edtVlrIcms
              +',"aliqipi":'      + calc.edtAliqIpi
              +',"vlripi":'       + calc.edtVlrIpi
              +',"aliqpis":'      + calc.edtAliqPis
              +',"vlrpis":'       + calc.edtVlrPis
              +',"aliqst":'       + calc.edtAliqSt
              +',"vlrst":'        + calc.edtVlrSt
              +',"bcicms":'       + jsConverte("#edtBcIcms").dolar(true)
              +'}]';
            let totalItem=( calc.edtVlrItem+calc.edtVlrFrete+calc.edtVlrSeguro+calc.edtVlrOutras+calc.edtVlrIpi+calc.edtVlrSt-calc.edtVlrDesconto );
            
            jsIte.registros.push([              
               $doc("edtCodPrd").value                                          // PRODUTO
              ,$doc("edtDesPrd").value                                          // PRODUTO_NOME
              ,$doc("edtCodCfo").value                                          // CFOP
              ,jsConverte("#edtVlrItem").real()                                 // VLRITEM              
              ,jsConverte("#edtVlrIpi").real()                                  // VLRIPI
              ,jsConverte("#edtVlrSt").real()                                   // VLRST
              ,jsConverte(parseFloat(totalItem).toFixed(2)).real()              // TOTALITEM
              ,campos                                                           // JSON
              ,iteID
            ]);
            iteID++;
            objIte.montarBody2017();
            fncTotalNf();
            ///////////////////////////////////////////////
            // Colocando valores default para novo registro
            ///////////////////////////////////////////////
            $doc("nfpi").newRecord("data-newrecorditem");
            $doc("edtCodPrd").setAttribute("data-codncm","*");
            $doc("edtCodPrd").setAttribute("data-csticms","*");
            $doc("edtCodPrd").setAttribute("data-cstipi","*");
            $doc("edtCodPrd").setAttribute("data-cstpis","*");
            $doc("edtCodPrd").setAttribute("data-cstcofins","*");
            $doc("edtCodPrd").setAttribute("data-codemb","*");
            $doc("edtCodPrd").setAttribute("data-alteranfp","*");
            //
            //
            document.getElementById("edtCodPrd").foco();
          };  
        } catch(e){
          gerarMensagemErro("pgr",e,{cabec:"Erro"});          
        };  
      };
      ///////////////////////////
      // Excluindo item do pedido
      ///////////////////////////  
      function iteExcluirClick(){
        let indice=0;
        tblIte.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr,tam) {     
          if(row.cells[0].children[0].checked){
            jsIte.registros.splice(indice,1);
          };
          indice++;
        });
        tblIte.apagaChecados();
        fncTotalNf();
      }; 
      ////////////////////////////
      // Calculando o parcelamento
      ////////////////////////////  
      function fncCalculaParcelamento(){
        try{    
          msg = new clsMensagem("Erro");
          //////////////////////////
          // Checagem basica      //
          //////////////////////////
          msg.floMaiorZero("VALOR TOTAL"  , $doc("edtVlrEvento").value );           
          msg.intMaiorZero("INTERVALO"    , $doc("edtIntervalo").value );          
          msg.intMaiorZero("PARCELAS"     , $doc("edtNumPar").value    );                    
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            minhaAba.limparTable("tblPrcl");
            let np        = jsConverte("#edtNumPar").inteiro();
            let intervalo = jsConverte("#edtIntervalo").inteiro();
            let vlrEvento = jsConverte("#edtVlrEvento").dolar();
            let vencto    = $doc("edtVencto").value;
            if( np==1 ){
              minhaAba.novoRegistro("tblPrcl",
                [ "01" 
                  ,$doc("edtVencto").value
                  ,$doc("edtVlrEvento").value
                ]
              );
            } else {
              let np        = jsNmrs("edtNumPar").inteiro().ret();
              let intervalo = jsNmrs("edtIntervalo").inteiro().ret();
              let vlrEvento = jsNmrs("edtVlrEvento").dolar().ret();
              let vlrParc   = jsNmrs(vlrEvento/np).dolar().ret();
              document.getElementById("edtVencto").setAttribute("data-oldvalue",document.getElementById("edtVencto").value); 
              
              for( let lin=0; lin < np; lin++ ){                
                minhaAba.novoRegistro("tblPrcl",
                  [ (lin+1) 
                    ,document.getElementById("edtVencto").value
                    ,( ((lin+1)<np) ? jsNmrs(vlrParc).dec(2).real().ret() : jsNmrs(vlrEvento).dec(2).real().ret() )
                  ]
                );
                vlrEvento = (vlrEvento-vlrParc);
                document.getElementById("edtVencto").value=jsDatas("edtVencto").retSomarDias(intervalo).retDDMMYYYY();
              };  
              document.getElementById("edtVencto").value=document.getElementById("edtVencto").getAttribute("data-oldvalue");
            }; 
          };    
        } catch(e){
          gerarMensagemErro("catch",e.message,"Erro");
        };  
      };
      //
      //
      function fncGravarNfp(){
        try{          
          $doc("edtCodNo").value    = jsConverte("#edtCodNo").soNumeros();
          $doc("edtCodFll").value   = jsConverte("#edtCodFll").soNumeros();
          $doc("edtCodCdd").value   = jsConverte("#edtCodCdd").soNumeros();
          $doc("edtNumNf").value    = jsConverte("#edtNumNf").soNumeros();
          $doc("edtCodBnc").value   = jsConverte("#edtCodBnc").soNumeros();
          $doc("edtCodTrn").value   = jsConverte("#edtCodTrn").soNumeros();
          $doc("edtVolume").value   = jsConverte("#edtVolume").soNumeros();          
          $doc("edtEspecie").value  = jsConverte("#edtEspecie").upper();
          //
          msg = new clsMensagem("Erro");          
          msg.notNull("NATUREZA_OPERACAO"   , $doc("edtCodNo").value                                  );
          msg.notNull("ESPECIE"             , $doc("edtEspecie").value                                );
          msg.notNull("VOLUME"              , $doc("edtVolume").value                                 );          
          msg.diferente("NATUREZA_OPERACAO" , $doc("edtDesNo").value,"*"                              );
          msg.intMaiorZero("FILIAL"         , $doc("edtCodFll").value                                 );
          msg.diferente("COBRANCA"          , $doc("edtCodFc").value,"*"                              );
          msg.diferente("COBRANCA_NOME"     , $doc("edtDesFc").value,"*"                              );
          msg.diferente("TIPODOCTO"         , $doc("edtCodTd").value,"*"                              );
          msg.diferente("TIPODOCTO_NOME"    , $doc("edtDesTd").value,"*"                              );
          msg.tamFixo("COD_CIDADE"          , $doc("edtCodCdd").value,7                               );
          msg.intMaiorZero("NUMERO_NF"      , $doc("edtNumNf").value                                  );
          msg.intMaiorZero("COD_BANCO"      , $doc("edtCodBnc").value                                 );
          msg.notNull("OBSERVACAO"          , $doc("edtObservacao").value                             );
          msg.notNull("ESPECIE"             , $doc("edtEspecie").value                                );
          msg.notNull("VOLUME"              , $doc("edtVolume").value                                 );
          msg.contido("TIPO"                , $doc("edtCodPtp").value,["CP","CR"]                     );
          msg.diferente("COMPEENCIA"        , $doc("edtDtDocto").getAttribute("data-codcmp"),"*"      );
          msg.diferente("UF_DESTINO"        , $doc("edtCodFvr").getAttribute("data-ufdestino"),"*"    );
          msg.tamFixo("UF_DESTINO"          , $doc("edtCodFvr").getAttribute("data-ufdestino"),2      );
          msg.diferente("UF_ORIGEM"         , $doc("edtCodFvr").getAttribute("data-uforigem"),"*"     );
          msg.tamFixo("UF_ORIGEM"           , $doc("edtCodFvr").getAttribute("data-uforigem"),2       );          
          msg.diferente("COD_CATEGORIA"     , $doc("edtCodFvr").getAttribute("data-codctg"),"*"       );          
          msg.diferente("FISICA_JURIDICA"   , $doc("edtCodFvr").getAttribute("data-fisjur"),"*"       );          
          msg.contido("FISICA_JURIDICA"     , $doc("edtCodFvr").getAttribute("data-fisjur"),["F","J"] );          
          msg.diferente("EMISSOR"           , $doc("edtCodFvr").getAttribute("data-emissor"),"*"      );          
          msg.diferente("OPE_PADRAO"        , $doc("edtCodNo").getAttribute("data-codpt"),"*"         );
          msg.diferente("CTA_GERENCIAL"     , $doc("edtCodNo").getAttribute("data-codcc"),"*"         );
          msg.intMaiorZero("TRANSPORTADORA" , $doc("edtCodTrn").value                                 );
          msg.diferente("TRANSP_NOME"       , $doc("edtCodTrn").value,"*"                             );          
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            /////////////////////////////////////////////////////////////////////////////////
            // Parcelamento
            // Transformar em vetor para naum ter que andar novamente qdo criar json para php
            /////////////////////////////////////////////////////////////////////////////////
            let tbl     = tblPrcl.getElementsByTagName("tbody")[0];
            let nl      = tbl.rows.length;
            let totGrd  = 0;
            let arrPrcl = [];
            if( nl>0 ){
              for(var lin=0 ; (lin<nl) ; lin++){  
                arrPrcl.push(
                  { parc    : jsNmrs(tbl.rows[lin].cells[0].innerHTML).inteiro().ret()
                    ,vencto : jsDatas(tbl.rows[lin].cells[1].innerHTML).retMMDDYYYY()
                    ,valor  : jsNmrs(tbl.rows[lin].cells[2].innerHTML).dec(2).dolar().ret()
                    ,debcre : ( document.getElementById("edtCodPtp").value=="CR" ? "C" : "D" )
                  }  
                );
                totGrd+=jsNmrs(tbl.rows[lin].cells[2].innerHTML).dec(2).dolar().ret();
              };  
            };
            let clsRat = jsString("rateio");
            clsRat.principal(false);
            
            let clsDup = jsString("duplicata");
            clsDup.principal(false);
            arrPrcl.forEach(function(reg){
              clsDup.add("parcela"    , reg.parc    );
              clsDup.add("vencto"     , reg.vencto  );  
              clsDup.add("vlrparcela" , reg.valor   );              
              
              clsRat.add("parcela"          , reg.parc                                       );
              clsRat.add("codcc"            , $doc("edtCodNo").getAttribute("data-codcc")    );
              clsRat.add("debito"           , ($doc("edtEntSai").value=="E" ? reg.valor : 0) );
              clsRat.add("credito"          , ($doc("edtEntSai").value=="S" ? reg.valor : 0) );
              clsRat.add("comparaVlrEvento" , "S"                                            );
            });  
            let duplicata = clsDup.fim();
            let rateio    = clsRat.fim();
            //
            //
            /////////////////
            // Contabilizando
            /////////////////
            /*
            let clsRat = jsString("rateio");
            clsRat.principal(false);
            clsRat.add("parcela"    , 1                                                                       );
            clsRat.add("codcc"      , $doc("edtCodNo").getAttribute("data-codcc")                             );
            clsRat.add("debito"     , ($doc("edtEntSai").value=="E" ? jsConverte("#edtVlrEvento").dolar(true) : 0) );
            clsRat.add("credito"    , ($doc("edtEntSai").value=="S" ? jsConverte("#edtVlrEvento").dolar(true) : 0) );
            let rateio = clsRat.fim();
            */
            //      
            //
            /////////////////////////////////////////////////////////////
            // Pegando o objeto para acumular itens e gravar na NFPRODUTO
            /////////////////////////////////////////////////////////////
            let calc=calculaItem(3);
            ///////////////////////////////
            // Classe principal para Php //
            ///////////////////////////////
            let pega    = "";
            let itemId  = 1;
            let clsNfpi = jsString("item");
            clsNfpi.principal(false);
            tblIte.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr,tam) {  
              pega = JSON.parse(row.cells[objCol.JSON].innerHTML)[0];
              clsNfpi.add("nfpi_item"            ,itemId                                                          );
              clsNfpi.add("nfpi_codprd"          ,row.cells[objCol.PRODUTO].innerHTML                             );
              clsNfpi.add("nfpi_cfop"            ,row.cells[objCol.CFOP].innerHTML                                );
              clsNfpi.add("nfpi_vlrunitario"     ,pega.vlrunitario                                                );
              clsNfpi.add("nfpi_unidades"        ,pega.unidades                                                   );
              clsNfpi.add("nfpi_vlritem"         ,jsConverte(row.cells[objCol.VLRITEM].innerHTML).dolar()         );
              clsNfpi.add("nfpi_codemb"          ,pega.codemb                                                     );
              clsNfpi.add("nfpi_vlrfrete"        ,pega.vlrfrete                                                   );
              clsNfpi.add("nfpi_vlrseguro"       ,pega.vlrseguro                                                  );
              clsNfpi.add("nfpi_vlroutras"       ,pega.vlroutras                                                  );
              clsNfpi.add("nfpi_vlrdesconto"     ,pega.vlrdesconto                                                );
              clsNfpi.add("nfpi_csticms"         ,pega.csticms                                                    );
              clsNfpi.add("nfpi_aliqicms"        ,pega.aliqicms                                                   );
              clsNfpi.add("nfpi_reducaobc"       ,0                                                               );
              clsNfpi.add("nfpi_bcicms"          ,pega.bcicms                                                     );
              clsNfpi.add("nfpi_vlricms"         ,pega.vlricms                                                    );
              clsNfpi.add("nfpi_vlricmsisentas"  ,0                                                               );  //Trigger faz esta conta
              clsNfpi.add("nfpi_vlricmsoutras"   ,0                                                               );  //Trigger faz esta conta
              clsNfpi.add("nfpi_cstipi"          ,pega.cstipi                                                     );
              clsNfpi.add("nfpi_aliqipi"         ,pega.aliqipi                                                    );
              clsNfpi.add("nfpi_bcipi"           ,pega.bcicms                                                     );
              clsNfpi.add("nfpi_vlripi"          ,pega.vlripi                                                     );
              clsNfpi.add("nfpi_vlripiisentas"   ,0                                                               );  //Trigger faz esta conta
              clsNfpi.add("nfpi_vlripioutras"    ,0                                                               );  //Trigger faz esta conta
              clsNfpi.add("nfpi_cstpis"          ,pega.cstpis                                                     );
              clsNfpi.add("nfpi_aliqpis"         ,pega.aliqpis                                                    );
              clsNfpi.add("nfpi_bcpis"           ,pega.bcicms                                                     );
              clsNfpi.add("nfpi_vlrpis"          ,pega.vlrpis                                                     );
              clsNfpi.add("nfpi_cstcofins"       ,pega.cstcofins                                                  );
              clsNfpi.add("nfpi_aliqcofins"      ,pega.aliqcofins                                                 );
              clsNfpi.add("nfpi_bccofins"        ,pega.bcicms                                                     );
              clsNfpi.add("nfpi_vlrcofins"       ,pega.vlrcofins                                                  );
              clsNfpi.add("nfpi_aliqst"          ,pega.aliqst                                                     );
              clsNfpi.add("nfpi_bcst"            ,pega.bcicms                                                     );
              clsNfpi.add("nfpi_vlrst"           ,pega.vlrst                                                      );
              clsNfpi.add("nfpi_totalitem"       ,jsConverte(row.cells[objCol.TOTAL].innerHTML).dolar()           );
              itemId++;
              calc.edtVlrItem     +=  jsConverte(row.cells[objCol.VLRITEM].innerHTML).dolar(true)
              calc.edtVlrFrete    +=  pega.vlrfrete;
              calc.edtVlrSeguro   +=  pega.vlrseguro;
              calc.edtVlrOutras   +=  pega.vlroutras;
              calc.edtVlrIpi      +=  pega.vlripi;
              calc.edtVlrIcms     +=  pega.vlricms;
              calc.edtVlrSt       +=  pega.vlrst;
              calc.edtVlrPis      +=  pega.vlrpis;
              calc.edtVlrCofins   +=  pega.vlrcofins;
              calc.edtVlrDesconto +=  pega.vlrdesconto;
            })
            let produtoitem = clsNfpi.fim();                    
            
            let clsNfp = jsString("produto");
            clsNfp.principal(false);
            clsNfp.add("nfp_numnf"        ,$doc("edtNumNf").value                         );
            clsNfp.add("nfp_codsnf"       ,$doc("edtCodSnf").value                        );
            clsNfp.add("nfp_emissor"      ,$doc("edtCodFvr").getAttribute("data-emissor") );
            clsNfp.add("nfp_codno"        ,$doc("edtCodNo").value                         );
            clsNfp.add("nfp_entsai"       ,$doc("edtEntSai").value                        );
            clsNfp.add("nfp_lancto"       ,0                                              );
            clsNfp.add("nfp_vlritem"      ,calc.edtVlrItem                                );
            clsNfp.add("nfp_vlrfrete"     ,calc.edtVlrFrete                               );
            clsNfp.add("nfp_vlrseguro"    ,calc.edtVlrSeguro                              );
            clsNfp.add("nfp_vlroutras"    ,calc.edtVlrOutras                              );
            clsNfp.add("nfp_vlripi"       ,calc.edtVlrIpi                                 );
            clsNfp.add("nfp_vlricms"      ,calc.edtVlrIcms                                );
            clsNfp.add("nfp_vlrst"        ,calc.edtVlrSt                                  );
            clsNfp.add("nfp_vlrpis"       ,calc.edtVlrPis                                 );
            clsNfp.add("nfp_vlrcofins"    ,calc.edtVlrCofins                              );
            clsNfp.add("nfp_vlrdesconto"  ,calc.edtVlrDesconto                            );
            clsNfp.add("nfp_vlrtotal"     ,jsConverte("#edtVlrEvento").dolar()            );
            clsNfp.add("nfp_codtrn"       ,jsConverte("#edtCodTrn").inteiro()             );
            clsNfp.add("nfp_volume"       ,$doc("edtVolume").value                        );
            clsNfp.add("nfp_especie"      ,$doc("edtEspecie").value                       );
            clsNfp.add("nfp_codvnd"       ,0                                              );
            clsNfp.add("nfp_codcmp"       ,$doc("edtDtDocto").getAttribute("data-codcmp") );
            clsNfp.add("nfp_livro"        ,$doc("edtLivro").value                         );
            clsNfp.add("nfp_dtentrada"    ,jsDatas("edtDtEntrada").retMMDDYYYY()          );
            clsNfp.add("nfp_pesobruto"    ,0                                              );
            clsNfp.add("nfp_pesoliquido"  ,0                                              );
            clsNfp.add("nfp_entsai"       ,$doc("edtEntSai").value);
            let produto = clsNfp.fim();   
            ///////////////////////////////////////////////////
            // O trigger olha estes campos Tot=Total Out=Outros
            ///////////////////////////////////////////////////
            let comparaTot=jsConverte("#edtVlrEvento").dolar(true);
            let comparaOut=(calc.edtVlrItem+calc.edtVlrFrete+calc.edtVlrSeguro+calc.edtVlrOutras+calc.edtVlrIpi+calc.edtVlrSt-calc.edtVlrDesconto);   
            if( comparaTot != comparaOut )
              throw "VALOR EVENTO "+jsConverte(comparaTot).real()+" DIVERGE DA SOMA DE VALORES "+jsConverte(comparaOut).real()+"!"; 
            //
            //
            let clsFin = jsString("lote");
            clsFin.add("rotina"             , "cadastrar"         );                
            clsFin.add("login"              , jsPub[0].usr_login  );
            clsFin.add("codusr"             , jsPub[0].usr_codigo );        
            ///////////////////////////////////////////////////////////////////////////////////
            // verdireito
            // Como vem de NFP/NFS/CONTRATO/TARIFA/TRANSF aqui informo qual direito vou olhar
            // pois um usuario pode lancar contrato mas naum NFProduto
            ///////////////////////////////////////////////////////////////////////////////////
            clsFin.add("verdireito"         , 27                                                                );            
            clsFin.add("codbnc"             , $doc("edtCodBnc").value                                           );
            clsFin.add("codcc"              , "NULL"                                                            );  //Se NULL o trigger faz    
            clsFin.add("codcmp"             , $doc("edtDtDocto").getAttribute("data-codcmp")                    );  //Competencia contabil          
            clsFin.add("codfvr"             , $doc("edtCodFvr").value                                           );
            clsFin.add("codfc"              , $doc("edtCodFc").value                                            );
            clsFin.add("codtd"              , $doc("edtCodTd").value                                            );
            clsFin.add("codfll"             , $doc("edtCodFll").value                                           );
            clsFin.add("codptt"             , "F"                                                               );            
            clsFin.add("docto"              , "NFP"+jsConverte("#edtNumNf").emZero(6)                           );
            clsFin.add("dtdocto"            , jsDatas("edtDtDocto").retMMDDYYYY()                               );
            clsFin.add("lancto"             , 0                                                                 );  //Se maior que zero eh rotina de alteracao            
            clsFin.add("observacao"         , $doc("edtObservacao").value                                       );
            clsFin.add("codpt"              , $doc("edtCodNo").getAttribute("data-codpt")                       );  //Operacao padrao
            clsFin.add("codptp"             , $doc("edtCodPtp").value                                           );
            clsFin.add("vlrdesconto"        , 0                                                                 );
            clsFin.add("vlrevento"          , jsConverte("#edtVlrEvento").dolar(true)                           );
            clsFin.add("vlrmulta"           , 0                                                                 );
            clsFin.add("vlrretencao"        , 0                                                                 );
            clsFin.add("vlrpis"             , 0                                                                 );
            clsFin.add("vlrcofins"          , 0                                                                 );
            clsFin.add("vlrcsll"            , 0                                                                 );
            clsFin.add("temnfp"             , "S"                                                               );
            clsFin.add("temnfs"             , "N"                                                               );            
            clsFin.add("DUPLICATA"          , duplicata                                                         );
            clsFin.add("RATEIO"             , rateio                                                            );
            clsFin.add("PRODUTO"            , produto                                                           );
            clsFin.add("PRODUTOITEM"        , produtoitem                                                       );
            ///////////////////////
            // Enviando para gravar
            ///////////////////////
            envPhp=clsFin.fim();  
            ///////////////////////
            // Enviando para gravar
            ///////////////////////
            //envPhp=clsFin.fim();  
            fd = new FormData();
            fd.append("gravar",envPhp);
            msg     = requestPedido("classPhp/GravaFinanceiro.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              throw retPhp[0].erro;
            } else {  
              gerarMensagemErro("cad",retPhp[0].erro,"AVISO","edtCodNo");  
              $doc("nfpi").newRecord("data-newrecorditem");              
              document.getElementById("frmNfp").newRecord("data-newrecord");
              document.getElementById("edtCodNo").foco();
              jsIte.registros=[];
              objIte.montarHtmlCE2017(jsIte); 
              minhaAba.limparTable("tblPrcl");
            };
          };  
        } catch(e){
          gerarMensagemErro("nfs",e,"Erro");          
        };  
      };
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:50%;float:left;"><h2 style="text-align:center;">Cadastro NF Produto</h2></div>
        <div class="teEsquerda"></div>
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>        
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <form method="post" class="center" id="frmNfp" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="divAzul" style="width:75%;margin-left:12.5%;height: 1000px;">
          <div class="campotexto campo100"  style="margin-top:4px;">
            <div class="campotexto campo10">
              <h2>Selecione</h2>
            </div>  
            <div class="campotexto campo05">
              <input class="campo_input inputF10" id="edtCodNo"
                                                  onBlur="codNoBlur(this);" 
                                                  onFocus="noFocus(this);" 
                                                  onClick="noF10Click(this);"
                                                  data-oldvalue=""
                                                  data-newrecord="*"                
                                                  data-codpt="*"
                                                  data-codcc="*"
                                                  autocomplete="off"
                                                  maxlength="2"
                                                  type="text"/>
              <label class="campo_label campo_required" for="edtCodNo">NO:</label>
            </div>
            <div class="campotexto campo40">
              <input class="campo_input_titulo input" id="edtDesNo" 
                                                      data-newrecord="*"
                                                      type="text" disabled />
              <label class="campo_label campo_required" for="edtDesNo">NATUREZA OPERACAO:</label>
            </div>
            <div class="campotexto campo05">
              <input class="campo_input_titulo input" id="edtCodPtp" 
                                                      data-newrecord="**"
                                                      type="text" disabled />
              <label class="campo_label campo_required" for="edtCodPtp">TIPO:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input input" id="edtCodFll" 
                                               OnKeyPress="return mascaraInteiro(event);" 
                                               data-newrecord="eval jsNmrs(jsPub[0].emp_codfll).emZero(4).ret()"
                                               type="text" 
                                               maxlength="4"/>
              <label class="campo_label campo_required" for="edtCodFll">FILIAL:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo" id="edtCodCdd" 
                                                data-newrecord="*******"
                                                type="text" 
                                                maxlength="7" disabled />
              <label class="campo_label campo_required" for="edtCodCdd">MUNICIPIO:</label>
            </div>
            <!--                                                //onBlur="dtDoctoBlur();"                                                           -->
            <div class="campotexto campo10">
              <input class="campo_input input" id="edtDtDocto" 
                                               onBlur="buscarBd()"
                                               placeholder="##/##/####"             
                                               data-newrecord="nrHoje" 
                                               data-codcmp="000000"   
                                               onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                               type="text" 
                                               maxlength="10" />
              <label class="campo_label campo_required" for="edtDtDocto">EMISSÂO:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input input" id="edtNumNf" 
                                                OnKeyPress="return mascaraInteiro(event);"
                                                onBlur="numNfBlur();"                                               
                                                type="text" 
                                                data-newrecord="000000"
                                                maxlength="6"/>
              <label id="lblNumNf" class="campo_label campo_required" for="edtCodNumNf">NOTA FISCAL:</label>
            </div>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodFvr"
                                                OnKeyPress="return mascaraInteiro(event);"
                                                onBlur="codFvrBlur(this);" 
                                                onFocus="fvrFocus(this);" 
                                                onClick="fvrF10Click(this);"
                                                data-newrecord="0000"                                                
                                                data-oldvalue="0000" 
                                                data-uforigem="*";
                                                data-ufdestino="*"
                                                data-codctg="*"
                                                data-fisjur="*"
                                                data-emissor="*"
                                                autocomplete="off"
                                                type="text" />
            <label id="lblCodFvr" class="campo_label campo_required" for="edtCodFvr">FAVORECIDO:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesFvr" 
                                                    data-newrecord="*"            
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesFvr">NOME</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodTd"
                                                onBlur="codTdBlur(this);" 
                                                onFocus="tdFocus(this);" 
                                                onClick="tdF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="3"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodTd">TD:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesTd" 
                                                    data-newrecord="*"
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesTd">TIPO DOCTO:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodFc"
                                                onBlur="codFcBlur(this);" 
                                                onFocus="fcFocus(this);" 
                                                onClick="fcF10Click(this);"
                                                data-newrecord="BOL"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="3"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodFc">FC:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesFc" 
                                                    data-newrecord="BOLETO"            
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesFc">FORMA COBR:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodBnc"
                                                OnKeyPress="return mascaraInteiro(event);"
                                                onBlur="codBncBlur(this);" 
                                                onFocus="bncFocus(this);" 
                                                onClick="bncF10Click(this);"
                                                data-newrecord="eval jsNmrs(jsPub[0].emp_codbnc).emZero(4).ret()"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="4"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodBnc">BANCO:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesBnc" 
                                                    data-newrecord="eval jsPub[0].emp_desbnc"  
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesBnc">BANCO_NOME:</label>
          </div>
          
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodTrn"
                                                OnKeyPress="return mascaraInteiro(event);"
                                                onBlur="codTrnBlur(this);" 
                                                onFocus="trnFocus(this);" 
                                                onClick="trnF10Click(this);"
                                                data-newrecord="0000"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                maxlength="4"
                                                type="text"/>
            <label class="campo_label campo_required" for="edtCodTrn">TRANSP:</label>
          </div>
          <div class="campotexto campo40">
            <input class="campo_input_titulo input" id="edtDesTrn" 
                                                    data-newrecord="*"  
                                                    type="text" disabled />
            <label class="campo_label campo_required" for="edtDesTrn">TRANSPORTADORA_NOME:</label>
          </div>
          
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtVolume" 
                                             OnKeyPress="return mascaraInteiro(event);"
                                             type="text" 
                                             data-newrecord="01"  
                                             maxlength="10"/>
            <label class="campo_label campo_required" for="edtVolume">VOLUME:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtEspecie" 
                                             type="text" 
                                             data-newrecord="CAIXA"  
                                             maxlength="10"/>
            <label class="campo_label campo_required" for="edtEspecie">ESPECIE:</label>
          </div>
          
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtDtEntrada" 
                                             placeholder="##/##/####"             
                                             data-newrecord="nrHoje" 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtDtEntrada">DT ENTRADA:</label>
          </div>
          <div class="campotexto campo20"></div>          
          
          
          <div class="campotexto campo100">
            <input class="campo_input input" id="edtObservacao" type="text" maxlength="120"/>
            <label class="campo_label campo_required" for="edtObservacao">OBSERVAÇÃO:</label>
          </div>
          <hr style="height:6px; border-bottom:4px solid #73879C;margin-top: 2px; margin-bottom: 2px;">
          
          <div id="nfpi">          
            <div class="campotexto campo10">
              <input class="campo_input inputF10" id="edtCodPrd"
                                                  onBlur="codPrdBlur(this);" 
                                                  onFocus="prdFocus(this);" 
                                                  onClick="prdF10Click(this);"
                                                  data-oldvalue=""
                                                  data-newrecorditem="*"                                                  
                                                  data-codncm="*"
                                                  data-csticms="*"
                                                  data-cstipi="*"
                                                  data-cstpis="*"
                                                  data-cstcofins="*"
                                                  data-codemb="*"
                                                  data-alteranfp="*"
                                                  autocomplete="off"
                                                  maxlength="15"
                                                  type="text"/>
              <label class="campo_label campo_required" for="edtCodPrd">PRODUTO:</label>
            </div>
            <div class="campotexto campo40">
              <input class="campo_input_titulo input" id="edtDesPrd" 
                                                      data-newrecorditem="*"
                                                      type="text" disabled />
              <label class="campo_label campo_required" for="edtDesPrd">PRODUTO_NOME:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input inputF10" id="edtCodCfo"
                                                  placeholder="#.###"                 
                                                  onkeyup="mascaraNumero('#.###',this,event,'dig')"
                                                  onBlur="CodCfoBlur(this);" 
                                                  onFocus="cfoFocus(this);" 
                                                  onClick="cfoF10Click(this);"
                                                  data-oldvalue=""
                                                  autocomplete="off"
                                                  maxlength="3"
                                                  type="text"/>
              <label class="campo_label campo_required" for="edtCodCfo">CFOP:</label>
            </div>
            <div class="campotexto campo40">
              <input class="campo_input_titulo" id="edtDesCfo" 
                                                data-newrecorditem="*"            
                                                type="text"
                                                disabled />
              <label class="campo_label campo_required" for="edtDesCfo">CFOP_NOME:</label>
            </div>
            <div class="campotexto campo12">
              <input class="campo_input input edtDireita" id="edtVlrUnitario"
                                                          onFocus="fimItem();"
                                                          onBlur="fncCasaDecimal(this,2);calculaItem(1);"  
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtVlrUnitario">VLR UNITARIO:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input input edtDireita" id="edtUnidades"
                                                          onBlur="fncCasaDecimal(this,2);calculaItem(1);"  
                                                          data-newrecorditem="1" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtUnidades">UNIDADES:</label>
            </div>
            <div class="campotexto campo12">
              <input class="campo_input_titulo edtDireita" id="edtVlrItem"
                                                           onBlur="fncCasaDecimal(this,2)"  
                                                           data-newrecorditem="0,00" 
                                                           type="text" 
                                                           disabled />
              <label class="campo_label campo_required" for="edtVlrUnitario">VLR ITEM:</label>
            </div>
            
            <div class="campotexto campo12">
              <input class="campo_input input edtDireita" id="edtVlrFrete"
                                                          onBlur="fncCasaDecimal(this,2);calculaItem(1);"
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtVlrFrete">VLR FRETE:</label>
            </div>
            <div class="campotexto campo12">
              <input class="campo_input input edtDireita" id="edtVlrSeguro"
                                                          onBlur="fncCasaDecimal(this,2);calculaItem(1);"
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtVlrSeguro">VLR SEGURO:</label>
            </div>
            <div class="campotexto campo12">
              <input class="campo_input input edtDireita" id="edtVlrOutras"
                                                          onBlur="fncCasaDecimal(this,2);calculaItem(1);"
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtVlrOutras">VLR OUTRAS:</label>
            </div>
            <div class="campotexto campo12">
              <input class="campo_input input edtDireita" id="edtVlrDesconto"
                                                          onBlur="fncCasaDecimal(this,2);calculaItem(1);incluirPrd(document.getElementById('edtCodPrd').getAttribute('data-alteranfp'));"
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtVlrDesconto">VLR DESCONTO:</label>
            </div>
            <div class="campotexto campo15">
              <input class="campo_input_titulo edtDireita" id="edtBcIcms"
                                                          onBlur="fncCasaDecimal(this,2);calculaItem(1);"
                                                           data-newrecorditem="0,00" 
                                                           type="text" 
                                                           disabled />
              <label class="campo_label campo_required" for="edtBcIcms">BASE CALC:</label>
            </div>
            
            
            
            <!--<div class="campotexto campo30"></div>-->
            
            <div class="campotexto campo07">
              <input class="campo_input input edtDireita" id="edtAliqIcms"
                                                          onBlur="fncCasaDecimal(this,2)"  
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtAliqIcms">%ICMS:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrIcms"
                                                           onBlur="fncCasaDecimal(this,2)"  
                                                           data-newrecorditem="0,00" 
                                                           type="text" 
                                                           disabled />
              <label class="campo_label campo_required" for="edtVlrIcms">VLR ICMS:</label>
            </div>
            <div class="campotexto campo07">
              <input class="campo_input input edtDireita" id="edtAliqIpi"
                                                          onBlur="fncCasaDecimal(this,2)"  
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtAliqIpi">%IPI:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrIpi"
                                                           onBlur="fncCasaDecimal(this,2)"  
                                                           data-newrecorditem="0,00" 
                                                           type="text" 
                                                           disabled />
              <label class="campo_label campo_required" for="edtVlrIpi">VLR IPI:</label>
            </div>
            <div class="campotexto campo07">
              <input class="campo_input input edtDireita" id="edtAliqSt"
                                                          onBlur="fncCasaDecimal(this,2)"  
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtAliqSt">%ST:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrSt"
                                                           onBlur="fncCasaDecimal(this,2)"  
                                                           data-newrecorditem="0,00" 
                                                           type="text" 
                                                           disabled />
              <label class="campo_label campo_required" for="edtVlrSt">VLR ST:</label>
            </div>
            

            <div class="campotexto campo07">
              <input class="campo_input input edtDireita" id="edtAliqPis"
                                                          onBlur="fncCasaDecimal(this,2)"  
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtAliqPis">%PIS:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrPis"
                                                           onBlur="fncCasaDecimal(this,2)"  
                                                           data-newrecorditem="0,00" 
                                                           type="text" 
                                                           disabled />
              <label class="campo_label campo_required" for="edtVlrPis">VLR PIS:</label>
            </div>

            <div class="campotexto campo07">
              <input class="campo_input input edtDireita" id="edtAliqCofins"
                                                          onBlur="fncCasaDecimal(this,2)"  
                                                          data-newrecorditem="0,00" 
                                                          type="text" />
              <label class="campo_label campo_required" for="edtAliqCofins">%COFINS:</label>
            </div>
            <div class="campotexto campo10">
              <input class="campo_input_titulo edtDireita" id="edtVlrCofins"
                                                           onBlur="fncCasaDecimal(this,2)"  
                                                           data-newrecorditem="0,00" 
                                                           type="text" 
                                                           disabled />
              <label class="campo_label campo_required" for="edtVlrCofins">VLR COFINS:</label>
            </div>
            <div onClick="fncIncluir();"  class="btnImagemEsq bie12 bieAzul"><i class="fa fa-plus"> Incluir</i></div>  
          </div>

          
          <div id="sctnIte">
          </div>  
          
          <div class="campotexto campo15">
            <input class="campo_input_titulo edtDireita" id="edtVlrEvento"
                                                        onBlur="calculaImposto();fncCasaDecimal(this,2)"  
                                                        data-newrecord="0,00" 
                                                        type="text" 
                                                        disabled />
            <label class="campo_label campo_required" for="edtVlrEvento">VALOR TOTAL:</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input input" id="edtNumPar"
                                             data-newrecord="01"            
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             type="text" 
                                             maxlength="2"/>
            <label class="campo_label campo_required" for="edtNumPar">PARCELA(s)</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtVencto" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             data-oldvalue=""
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtVencto">PRIMEIRO VENCTO:</label>
          </div>

          <div class="campotexto campo15">
            <input class="campo_input input" id="edtIntervalo" 
                                             OnKeyPress="return mascaraInteiro(event);" 
                                             data-newrecord="30"
                                             OnBlur="fncCalculaParcelamento();"
                                             type="text" 
                                             maxlength="2"/>
            <label class="campo_label campo_required" for="edtIntervalo">INTERVALO (Dias) </label>
          </div>
          <div class="inactive">
            <input id="edtEntSai" data-newrecord="*" type="text" />
            <input id="edtCodSnf" data-newrecord="0" type="text" />            
            <!--<input id="edtInformarNf" data-newrecord="*" type="text" />-->
            <input id="edtLivro" data-newrecord="*" type="text" />            
          </div>
          <div onClick="fncGravarNfp();" class="btnImagemEsq bie12 bieAzul bieRight"><i class="fa fa-check"> Gravar NF</i></div>                    
          <div onClick="window.close();" class="btnImagemEsq bie12 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
          <div class="campotexto campo100">
            <div id="appAba" class="campotexto campo50" style="height:20em;float:left;position:relative;">
            </div>            
          </div>
        </div> 
      </form>
    </div>
  </body>
</html>