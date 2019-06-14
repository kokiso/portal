<?php
  session_start();
  if( isset($_POST["favorecidogrupo"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");

      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["favorecidogrupo"]);
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
        //////////////////////////////////////////////
        //    Dados para JavaScript FAVORECIDOGRUPO //
        //////////////////////////////////////////////
        if( $rotina=="selectFvr" ){
          $sql="";
          $sql.="SELECT A.FVR_CODIGO";
          $sql.="       ,A.FVR_NOME";
          $sql.="       ,A.FVR_GFCP";
          $sql.="       ,GFP.GF_NOME AS GRUPOCP";
          $sql.="       ,A.FVR_GFCR";
          $sql.="       ,GFR.GF_NOME AS GRUPOCR";
          $sql.="       ,U.US_APELIDO";
          $sql.="       ,A.FVR_CODUSR";
          $sql.="  FROM FAVORECIDO A";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA U ON A.FVR_CODUSR=U.US_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOFAVORECIDO GFP ON A.FVR_GFCP=GFP.GF_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOFAVORECIDO GFR ON A.FVR_GFCR=GFR.GF_CODIGO";
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        ////////////////////////////////////////////////////////////////////////
        // Atualizando o favorecido de dados se opcao de insert/updade/delete //
        ////////////////////////////////////////////////////////////////////////
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
    <title>Favorecido_Grupo</title>
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
        ///////////////////////////////////////////
        //   Objeto clsTable2017 FAVORECIDOGRUPO //
        ///////////////////////////////////////////
        jsFvr={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1}            
            ,{"id":1  ,"field"          :"FVR_CODIGO" 
                      ,"pk"             : "S"
                      ,"labelCol"       : "CODIGO"
                      ,"obj"            : "edtCodFvr"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,6] // ANGELO KOKISO aumento da range máxima para 6
                      ,"ajudaCampo"     : [ "Código do Favorecido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "FVR_NOME"   
                      ,"labelCol"       : "NOME"
                      ,"obj"            : "edtDesFvr"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "25em"
                      ,"tamImp"         : "120"
                      ,"truncate"       : "S"
                      ,"newRecord"      : ["","this","this"]
                      ,"padrao":0}
            ,{"id":3  ,"field"          :"FVR_GFCP" 
                      ,"labelCol"       : "CODCP"
                      ,"obj"            : "edtCodCp"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [ "Código do Favorecido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "GF_NOME"   
                      ,"labelCol"       : "GRUPOCP"
                      ,"obj"            : "edtDesCp"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "18em"
                      ,"tamImp"         : "60"
                      ,"newRecord"      : ["","this","this"]
                      ,"padrao":0}
            ,{"id":5  ,"field"          :"FVR_GFCR" 
                      ,"labelCol"       : "CODCR"
                      ,"obj"            : "edtCodCr"
                      ,"insUpDel"       : ["S","S","N"]
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"fieldType"      : "int"
                      ,"align"          : "center"
                      ,"digitosValidos" : "0|1|2|3|4|5|6|7|8|9"
                      ,"newRecord"      : ["0000","this","this"]
                      ,"validar"        : ["notnull"]
                      ,"digitosMinMax"  : [1,4]
                      ,"ajudaCampo"     : [ "Código do Favorecido"]
                      ,"importaExcel"   : "S"                                                                
                      ,"padrao":0}
            ,{"id":6  ,"field"          : "GF_NOME"   
                      ,"labelCol"       : "GRUPOCR"
                      ,"obj"            : "edtDesCr"
                      ,"insUpDel"       : ["N","N","N"]
                      ,"tamGrd"         : "18em"
                      ,"tamImp"         : "60"
                      ,"newRecord"      : ["","this","this"]
                      ,"padrao":0}
            ,{"id":7  ,"field"          : "US_APELIDO" 
                      ,"labelCol"       : "USUARIO" 
                      ,"obj"            : "edtUsuario"
                      ,"padrao":4}                
            ,{"id":8  ,"field"          : "FVR_CODUSR" 
                      ,"labelCol"       : "CODUSU"  
                      ,"obj"            : "edtCodUsu"  
                      ,"padrao":5}                                      
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"360px" 
              ,"label"          :"FAVORECIDOGRUPO - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Alterar"   ,"name":"horAlterar"    ,"onClick":"1"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o" }
            ,{"texto":"Semestral" ,"name":"horSemestre"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-calendar"         }                     
            ,{"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"     }        
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print"           }                        
            ,{"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"8"  ,"enabled":true ,"imagem":"fa fa-close"           }
          ] 
          ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"idBtnConfirmar" : "btnConfirmar"        // Se existir executa o confirmar do form/fieldSet
          ,"idBtnCancelar"  : "btnCancelar"         // Se existir executa o cancelar do form/fieldSet
          ,"div"            : "frmFvr"              // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaFvr"           // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmFvr"              // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
          ,"tbl"            : "tblFvr"              // Nome da table
          ,"prefixo"        : "Fvr"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VFAVORECIDO"         // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "BKPFAVORECIDO"       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "FVR_ATIVO"           // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "FVR_REG"             // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "FVR_CODUSR"          // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"iFrame"         : "iframeCorpo"         // Se a table vai ficar dentro de uma tag iFrame
          ,"width"          : "100em"               // Tamanho da table
          ,"height"         : "58em"                // Altura da table
          ,"tableLeft"      : "sim"                 // Se tiver menu esquerdo
          ,"relTitulo"      : "FAVORECIDO GRUPO"    // Titulo do relatório
          ,"relOrientacao"  : "P"                   // Paisagem ou retrato
          ,"relFonte"       : "7"                   // Fonte do relatório
          ,"foco"           : ["edtCodCp"
                              ,"edtCodCp"
                              ,"btnConfirmar"]      // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"     // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "DESCRICAO"           // Indice inicial da table
          ,"tamBotao"       : "12"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"tamMenuTable"   : ["10em","20em"]                                
          ,"labelMenuTable" : "Opções"              // Caption para menu table 
          ,"_menuTable"     :[
                               ["Número de registros em tela"            ,"fa-info"          ,"objFvr.numRegistros();"]                               
                               ,["Atualizar grade consulta"              ,"fa-filter"        ,"btnFiltrarClick('S');"]                               
                             ]  
          ,"codTblUsu"      : "FAVORECIDOGRUPO[20]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objFvr === undefined ){  
          objFvr=new clsTable2017("objFvr");
        };  
        objFvr.montarHtmlCE2017(jsFvr); 
        //////////////////////////////////
        // Definindo o form de manutencaum
        //////////////////////////////////    
        document.getElementById(jsFvr.form).style.width=jsFvr.width;
        btnFiltrarClick("S");  
      });
      //
      var objFvr;                     // Obrigatório para instanciar o JS TFormaCob
      var jsFvr;                      // Obj principal da classe clsTable2017
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
      var intCodDir = parseInt(jsPub[0].usr_d20);
      ////////////////////////////
      // Filtrando os registros //
      ////////////////////////////
      function btnFiltrarClick(atv) { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "selectFvr"         );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("ativo"       , atv                 );
        clsJs.add("codemp"      , jsPub[0].emp_codigo );
        fd = new FormData();
        fd.append("favorecidogrupo" , clsJs.fim());
        msg     = requestPedido("Trac_FavorecidoGrupo.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsFvr.registros=objFvr.addIdUnico(retPhp[0]["dados"]);
          objFvr.ordenaJSon(jsFvr.indiceTable,false);  
          objFvr.montarBody2017();
        }; 
      };
      ////////////////////////////////////
      //  AJUDA PARA GRUPO FAVOREICO CP //
      ////////////////////////////////////
      function cpFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function cpF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodCr"
                      ,topo:100
                      ,tableBd:"GRUPOFAVORECIDO"
                      ,fieldCod:"A.GF_CODIGO"
                      ,fieldDes:"A.GF_NOME"
                      ,fieldAtv:"A.GF_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblCp"}
        );
      };
      function RetF10tblCp(arr){
        document.getElementById("edtCodCp").value  = arr[0].CODIGO;
        document.getElementById("edtDesCp").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCp").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCpBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodCr"
                                  ,topo:100
                                  ,tableBd:"GRUPOFAVORECIDO"
                                  ,fieldCod:"A.GP_CODIGO"
                                  ,fieldDes:"A.GP_NOME"
                                  ,fieldAtv:"A.GP_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblCr"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? "0000"  : ret[0].CODIGO             );
          document.getElementById("edtDesCp").value  = ( ret.length == 0  ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO )  );
        };
      };
      ////////////////////////////////////
      //  AJUDA PARA GRUPO FAVOREICO CR //
      ////////////////////////////////////
      function crFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function crF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"btnConfirmar"
                      ,topo:100
                      ,tableBd:"GRUPOFAVORECIDO"
                      ,fieldCod:"A.GF_CODIGO"
                      ,fieldDes:"A.GF_NOME"
                      ,fieldAtv:"A.GF_ATIVO"
                      ,typeCod :"int" 
                      ,tbl:"tblCr"}
        );
      };
      function RetF10tblCr(arr){
        document.getElementById("edtCodCr").value  = arr[0].CODIGO;
        document.getElementById("edtDesCr").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodCr").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodCrBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"btnConfirmar"
                                  ,topo:100
                                  ,tableBd:"GRUPOFAVORECIDO"
                                  ,fieldCod:"A.GP_CODIGO"
                                  ,fieldDes:"A.GP_NOME"
                                  ,fieldAtv:"A.GP_ATIVO"
                                  ,typeCod :"int" 
                                  ,tbl:"tblGcr"}
          );
          document.getElementById(obj.id).value     = ( ret.length == 0 ? "0000"  : ret[0].CODIGO             );
          document.getElementById("edtDesCr").value = ( ret.length == 0  ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "0000" : ret[0].CODIGO )  );
        };
      };
      //
      function horSemestreClick(){
        localStorage.setItem("addParametro","grupo");
        window.open("Trac_FavorecidoGrupoSem.php");  
      };
    </script>
  </head>
  <body>
    <div class="divTelaCheia">
      <div id="divRotina" class="conteudo">  
        <div id="divTopoInicio">
        </div>
        <form method="post" 
              name="frmFvr" 
              id="frmFvr" 
              class="frmTable" 
              action="classPhp/imprimirsql.php" 
              target="_newpage">
          <div class="frmTituloManutencao">Grupo<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>          
          <div style="height: 160px; overflow-y: auto;">
            <input type="hidden" id="sql" name="sql"/>
            <div class="campotexto campo100">
              <div class="campotexto campo15">
                <input class="campo_input_titulo"   id="edtCodFvr"
                                                    maxlength="6"
                                                    type="text"/>
                <label class="campo_label campo" for="edtCodFvr">CODIGO:</label>
              </div>
              <div class="campotexto campo85">
                <input class="campo_input_titulo input" id="edtDesFvr" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesFvr">FAVORECIDO:</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodCp"
                                                    onBlur="CodCpBlur(this);" 
                                                    onFocus="cpFocus(this);" 
                                                    onClick="cpF10Click(this);"
                                                    data-oldvalue="0000"
                                                    autocomplete="off"
                                                    maxlength="4"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCp">CP:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesCp" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCp">PAGAR</label>
              </div>
              <div class="campotexto campo10">
                <input class="campo_input inputF10" id="edtCodCr"
                                                    onBlur="CodCrBlur(this);" 
                                                    onFocus="crFocus(this);" 
                                                    onClick="crF10Click(this);"
                                                    data-oldvalue="0000"
                                                    autocomplete="off"
                                                    maxlength="4"
                                                    type="text"/>
                <label class="campo_label campo_required" for="edtCodCr">CR:</label>
              </div>
              <div class="campotexto campo40">
                <input class="campo_input_titulo input" id="edtDesCr" type="text" disabled />
                <label class="campo_label campo_required" for="edtDesCr">RECEBER</label>
              </div>
              <div class="campotexto campo25">
                <input class="campo_input_titulo" disabled id="edtUsuario" type="text" />
                <label class="campo_label campo_required" for="edtUsuario">USUARIO</label>
              </div>
              <div class="inactive">
                <input id="edtCodUsu" type="text" />
                <input id="edtCodEmp" type="text" />
              </div>
              <div id="btnConfirmar" class="btnImagemEsq bie12 bieAzul bieRight"><i class="fa fa-check"> Gravar</i></div>
              <div id="btnCancelar"  class="btnImagemEsq bie12 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
            </div>
          </div>
        </form>
      </div>
    </div>       
  </body>
</html>