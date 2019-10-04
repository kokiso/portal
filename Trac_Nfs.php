<?php
  session_start();
  if( isset($_POST["nfs"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      //require("classPhp/dataCompetencia.class.php");      
      require("classPhp/selectRepetido.class.php");      

      //$clsCompet  = new dataCompetencia();    
      $vldr       = new validaJson();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["nfs"]);
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
        /////////////////////////
        // Cancelar NF
        /////////////////////////
        if( $lote[0]->rotina=="cancelanf" ){
          $sql="UPDATE NFSERVICO SET NFS_DTCANCELA='".$lote[0]->data."',NFS_CODUSR=".$_SESSION["usr_codigo"]." WHERE NFS_LANCTO=".$lote[0]->lancto;
          array_push($arrUpdt,$sql);            
          $atuBd = true;
        };  
        /////////////////////////
        // Registro do sistema
        /////////////////////////
        if( $lote[0]->rotina=="regsistema" ){
          $objReg = $lote[0]->REGISTRO;
          foreach ( $objReg as $reg ){
            $sql="UPDATE NFSERVICO SET NFS_REG='S',NFS_CODUSR=".$_SESSION["usr_codigo"]." WHERE NFS_LANCTO=".$reg->lancto;
            array_push($arrUpdt,$sql);            
          };
          $atuBd = true;
        };
        /////////////////////////
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="filtrar" ){        
          $sql ="SELECT A.NFS_LANCTO AS LANCTO";
          $sql.="       ,A.NFS_CODCMP AS CODCMP";
          $sql.="       ,CMP.CMP_NOME AS COMPET";          
          $sql.="       ,A.NFS_NUMNF AS NF";          
          $sql.="       ,SNF.SNF_SERIE AS SERIE";
          $sql.="       ,CASE WHEN SNF.SNF_ENTSAI='E' THEN 'ENT' ELSE 'SAI' END AS ES";          
          $sql.="       ,SNF.SNF_CODTD AS TD";          
          $sql.="       ,CONVERT(VARCHAR(10),PGR.PGR_DTDOCTO,127) AS EMISSAO";          
          $sql.="       ,FVR.FVR_APELIDO AS FAVORECIDO";          
          $sql.="       ,A.NFS_VLRTOTAL AS VALOR";
          $sql.="       ,A.NFS_VLRRETENCAO AS RETENCAO";
          $sql.="       ,SNF.SNF_LIVRO AS LIVRO";
          $sql.="       ,SNF.SNF_CODFLL AS FILIAL";
          $sql.="       ,CONVERT(VARCHAR(10),A.NFS_DTCANCELA,127) AS CANCELADA";                    
          $sql.="       ,CASE WHEN SNF.SNF_ENVIO='P' THEN 'SIM' ELSE 'NAO' END AS ENVIO";                    
          $sql.="       ,A.NFS_CODVERIFICACAO AS RECIBO";          
          $sql.="       ,CASE WHEN A.NFS_REG='P' THEN 'PUB' WHEN A.NFS_REG='S' THEN 'SIS' ELSE 'ADM' END AS REG";          
          $sql.="       ,US.US_APELIDO";          
          $sql.="  FROM NFSERVICO A WITH(NOLOCK)";           
          $sql.="  LEFT OUTER JOIN PAGAR PGR ON A.NFS_LANCTO=PGR.PGR_LANCTO";          
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON PGR.PGR_CODFVR=FVR.FVR_CODIGO";          
          $sql.="  LEFT OUTER JOIN SERIENF SNF ON A.NFS_CODSNF=SNF.SNF_CODIGO";          
          $sql.="  LEFT OUTER JOIN COMPETENCIA CMP ON A.NFS_CODCMP=CMP.CMP_CODIGO AND CMP.CMP_CODEMP=SNF.SNF_CODEMP";                    
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON US.US_CODIGO = A.NFS_CODUSR";          
          $sql.=" WHERE (A.NFS_CODCMP='".$lote[0]->codcmp."')";
          $sql.="   AND (SNF.SNF_ENTSAI='".$lote[0]->entsai."')";
          $sql.="   AND (SNF.SNF_CODEMP=".$_SESSION["emp_codigo"].")";
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
      };  
      ///////////////////////////////////////////////////////////////////
      // Atualizando o banco de dados se opcao de insert/updade/delete //
      ///////////////////////////////////////////////////////////////////
      if( $atuBd ){
        if( count($arrUpdt) >0 ){
          $retCls=$classe->cmd($arrUpdt);
          if( $retCls['retorno']=="OK" ){
            $retorno='[{"retorno":"OK","dados":"","erro":"'.count($arrUpdt).' REGISTRO(s) ATUALIZADO(s)!"}]'; 
          } else {
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          };  
        } else {
          $retorno='[{"retorno":"OK","dados":"","erro":"NENHUM REGISTRO CADASTRADO!"}]';
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <script language="javascript" type="text/javascript"></script>
  <head>
    <meta charset="utf-8">
    <title>NFS</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <!--<script src="js/jsBiblioteca.js"></script>-->
    <script src="js/jsCopiaDoc2017.js"></script>            
    <script>      
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        $doc("spnEmpApelido").innerHTML=jsPub[0].emp_apelido;
        document.getElementById("edtDesCmp").value = jsDatas(0).retMMMbYY();
        validaCmp(document.getElementById("edtDesCmp").value);
        document.getElementById("edtDesCmp").foco();
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsNfs={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "LANCTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "CODCMP"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]                       
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "COMPET"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Competência fiscal"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "NF"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]                       
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "SERIE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "10"
                      ,"align"          : "center"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "ES"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Informa se NF é entrada ou saída"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "TD"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Tipo de documento"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "EMISSAO"
                      ,"fieldType"      : "str"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "FAVORECIDO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "RETENCAO"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "LV"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Parametro informando se NF entra no livro mensal para apuração"                          
                      ,"popoverLabelCol": "Livro"                      
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "FILIAL"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                       
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "12"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "CANCELADA"
                      ,"fieldType"      : "str"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "PREF"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Parametro informando se NF deve ser enviada a prefeitura"                          
                      ,"popoverLabelCol": "Prefeitura"                      
                      ,"padrao":0}
            ,{"id":16 ,"labelCol"       : "RECIBO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":17 ,"field"          : "REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"align"          : "center"
                      ,"tamImp"         : "10"                      
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":18 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":19 ,"labelCol"        :"CD"         
                      ,"obj"           : "imgPP"
                      ,"tamGrd"       : "5em"
                      ,"tipo"         : "img"
                      ,"fieldType"    : "img"
                      ,"func"         : "copiaDocumento(this.parentNode.parentNode.cells[1].innerHTML);"
                      ,"tagI"         : "fa fa-print copiaDoc"
                      ,"padrao":0}
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"300px" 
              ,"label"          :"RELACIONAMENTO - Detalhe do registro"
            }
          ]
          , 
          "botoesH":[
             {"texto":"Novo lancto" ,"name":"horNovaNf"     ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-plus"       }
            ,{"texto":"Cancelar NF" ,"name":"horCancelaNf"  ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-calendar"    }             
            ,{"texto":"Fechar"      ,"name":"horFechar"     ,"onClick":"6"  ,"enabled":true ,"imagem":"fa fa-close" }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "switch (ceTr.cells[5].innerHTML){case 'E' : ceTr.style.color='red';break; case 'S' : ceTr.style.color='black';break;}"          
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)                                                      
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmNfs"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaNfs"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmNfs"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnNfs"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblNfs"                  // Nome da table
          ,"prefixo"        : "Me"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VNFSERVICO"              // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "absolute"
          ,"width"          : "135em"                   // Tamanho da table
          ,"height"         : "65em"                    // Altura da table
          ,"nChecks"        : true                      // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "NFS"                     // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"foco"           : ["edtDataIni"
                              ,"edtDataIni"
                              ,"btnConfirmar"]          // Foco qdo Cad/Alt/Exc
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "NF"                      // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objNfs === undefined ){  
          objNfs=new clsTable2017("objNfs");
        };  
        objNfs.montarHtmlCE2017(jsNfs); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsNfs,["CANCELADA","LANCTO","REG","USUARIO"]);
        btnFiltrarClick();
      });
      var objNfs;                     // Obrigatório para instanciar o JS TFormaCob
      var jsNfs;                      // Obj principal da classe clsTable2017
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario      
      var clsChecados;                // Classe para montar Json
      var chkds;                      // Guarda todos registros checados na table 
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d05);
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado      
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"           );
        clsJs.add("login"       , jsPub[0].usr_login  );
        clsJs.add("entsai"      , document.getElementById("cbEntSai").value  );
        clsJs.add("codcmp"      , document.getElementById("edtCodCmp").value );

        fd = new FormData();
        fd.append("nfs" , clsJs.fim());
        msg     = requestPedido(arqLocal,fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsNfs.registros=objNfs.addIdUnico(retPhp[0]["dados"]);
          objNfs.ordenaJSon(jsNfs.indiceTable,false);  
          objNfs.montarBody2017();
        };  
      };
      function horNovaNfClick(){
        window.open("Trac_NfsCadTitulo.php");
      };
      function validaCmp(valor){
        let retorno=validaCompetencia(valor);
        if( retorno.erro=="ok" ){
          document.getElementById("edtDesCmp").value=retorno.descmp;
          document.getElementById("edtCodCmp").value=retorno.codcmp;
        } else {
          gerarMensagemErro("cmp",retorno.erro,"Erro");  
        };
      }; 
      function horCancelaNfClick(){
        try{
          clsChecados = objNfs.gerarJson("1");
          chkds       = clsChecados.gerar();
          
          chkds.forEach(function(reg){
            if( reg.CANCELADA != "" )
              throw "Lancto "+reg.LANCTO+" com data de cancelamento!"; 
            if( reg.REG == "SIS" )
              throw "Lancto "+reg.LANCTO+" parametrizado como do sistema!"; 
          });
          //////////////////////////////////////////////////////////////
          // Preparando um objeto para enviar ao formulario de alteracao
          //////////////////////////////////////////////////////////////
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("rotina"  , "cancelanf"               );              
          clsJs.add("login"   , jsPub[0].usr_login        );              
          clsJs.add("lancto"  , chkds[0].LANCTO           );
          clsJs.add("data"    , jsDatas(0).retMMDDYYYY()  );
          var fd = new FormData();
          fd.append("nfs" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            /////////////////////////////////////////////////
            // Atualizando a grade
            /////////////////////////////////////////////////
            let tbl = tblNfs.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
            if( nl>0 ){
              for(let lin=0 ; (lin<nl) ; lin++){
                if( chkds[0].LANCTO == tbl.rows[lin].cells[objCol.LANCTO].innerHTML ){
                  tbl.rows[lin].cells[objCol.CANCELADA].innerHTML=jsDatas(0).retDDMMYYYY();
                  tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                };
              };    
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      
      function fncRegSistema(){
        try{    
          clsChecados = objNfs.gerarJson("n");
          chkds       = clsChecados.gerar();
          /////////////////////////////////////////////////
          // Armazenando para envio ao Php clsReg=Registros
          /////////////////////////////////////////////////
          let clsReg = jsString("registro");
          clsReg.principal(false);
          chkds.forEach(function(reg){
            clsReg.add("lancto", reg.LANCTO);             
            if( reg.REG=="SIS" )
              throw "LANCTO "+reg.LANCTO+" JA ESTA COM STATUS DO SISTEMA!"; 
          });
          let registro = clsReg.fim();
          //////////////////////
          // Enviando para o Php
          //////////////////////
          clsJs=jsString("lote");            
          clsJs.add("rotina"    , "regsistema"      );              
          clsJs.add("login"     ,jsPub[0].usr_login );              
          clsJs.add("REGISTRO" , registro           );

          var fd = new FormData();
          fd.append("nfs" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno=="OK" ){
            /////////////////////////////////////////////////
            // Atualizando a grade
            /////////////////////////////////////////////////
            let tbl = tblNfs.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
            if( nl>0 ){
              for(let lin=0 ; (lin<nl) ; lin++){
                chkds.forEach(function(reg){
                  if( reg.LANCTO == tbl.rows[lin].cells[objCol.LANCTO].innerHTML ){
                    tbl.rows[lin].cells[objCol.REG].innerHTML="SIS";
                    tbl.rows[lin].cells[objCol.REG].classList.add("corAzul");
                    tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                  };
                });  
              };    
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      //////////////////
      // Alterar empresa
      //////////////////
      function altEmpresa(){
        try{          
          clsJs   = jsString("lote");  
          clsJs.add("rotina"  , "altEmpresa"                );
          clsJs.add("login"   , jsPub[0].usr_login          );
          fd = new FormData();
          fd.append("nfs" , clsJs.fim());
          
          msg     = requestPedido(arqLocal,fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            janelaDialogo(
              { height          : "25em"
                ,body           : "16em"
                ,left           : "500px"
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
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="objNfs.imprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela."><i class="indFa fa-print"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="objNfs.excel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela."><i class="indFa fa-file-excel-o"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="fncRegSistema();"
                              data-dismissible="false" 
                              data-toggle="popover" 
                              data-title="Registro do sistema <span class='badge badge-warning'>CUIDADO</span>" 
                              data-placement="right" 
                              data-content="Esta opção transforma o registro para o sistema. Este não poderá mais ser alterado e nem excluido"><i class="indFa fa-key"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="altEmpresa();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Alterar empresa."><i class="indFa fa-spinner"></i>
          </div>
        </section>
      </aside>

      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <!--
        <a href="#" class="indLabel"><div id="tituloMenu">NF serviço</div></a>
        -->
        <div class="colMd12" style="float:left;margin-bottom:0px;height:50px;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">NF Servico</span>
              <span id="spnEmpApelido" class="infoBoxLabel"></span>
            </div>
          </div>
        </div>  
        
        <div id="indDivInforme" class="indTopoInicio80" style="padding-top:2px;">
          
          <div class="campotexto campo12">
            <select class="campo_input_combo" id="cbEntSai">
              <option value="S" selected>SAIDA</option>            
              <option value="E">ENTRADA</option>
            </select>
            <label class="campo_label campo_required" for="cbEntSai">OPÇÃO</label>
          </div>
          <div class="campotexto campo12">
            <input class="campo_input" id="edtDesCmp" 
                                       placeholder="AAA/MM"                 
                                       onBlur="validaCmp(this.value);"
                                       onkeyup="mascaraNumero('###/##',this,event,'letdig');"
                                       type="text" maxlength="6" />
            <label class="campo_label campo_required" 
                    data-dismissible="false" 
                    data-toggle="popover" 
                    data-title="Filtro <span class='badge'>Ajuda</span>" 
                    data-placement="bottom" 
                    data-content="Buscar somente Notas fiscais emitidas nesta competência, informar no formato MMM/YY"                   
                   for="edtDesCmp">COMPETÊNCIA</label>
          </div>
          <div class="inactive">
            <input id="edtCodCmp" type="text" />
          </div>
          <div id="btnFiltrar" onClick="btnFiltrarClick();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-check"> Filtrar</i></div>
        </div>
      </div>
      <section>
        <section id="sctnNfs">
        </section>  
      </section>
      <form method="post"
            name="frmAlv"
            id="frmAlv"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/>      
      </form>  
    </div>
  </body>
</html>