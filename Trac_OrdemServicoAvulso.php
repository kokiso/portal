<?php
  session_start();
  if( isset($_POST["ordemservicoavulso"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/dataCompetencia.class.php");
      require("classPhp/selectRepetido.class.php");                         

      $clsCompet  = new dataCompetencia();    
      $vldr       = new validaJson();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["ordemservicoavulso"]);
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
        /////////////////////////
        // Detalhe da OS
        /////////////////////////
        if( $lote[0]->rotina=="gravaobs" ){
          $os=$lote[0]->dos_codigo;
          $sql  ="INSERT INTO DETALHEOS(";
          $sql.="DOS_CODOS";
          $sql.=",DOS_CODMSG";
          $sql.=",DOS_COMPLEMENTO";          
          $sql.=",DOS_CODUSR) VALUES(";
          $sql.="$os";
          $sql.=",14";
          $sql.=",'".$lote[0]->dos_complemento."'";
          $sql.=",".$_SESSION["usr_codigo"];
          $sql.=")";
          array_push($arrUpdt,$sql);                                    
          $atuBd = true;
        }; 
       if( $lote[0]->rotina=="osAvulsa" ){
          if($lote[0]->osa_codind == '')
              $lote[0]->osa_codind = 'NULL';

          $sql ="INSERT INTO VORDEMSERVICOAVULSA";
          $sql.="   ( OSA_CODVND";
          $sql.="       ,OSA_CODFVR";            
          $sql.="       ,OSA_CODSRV";                    
          $sql.="       ,OSA_QTD";
          $sql.="       ,OSA_VALOR";          
          $sql.="       ,OSA_PGTO";
          $sql.="       ,OSA_PARCELAS";
          $sql.="       ,OSA_INDFVR";
          $sql.="       ,OSA_REG";
          $sql.="       ,OSA_CODUSR)VALUES(";
          $sql.="       ".$lote[0]->osa_vendedor;
          $sql.="       ,".$lote[0]->osa_favorecido."";            
          $sql.="       ,".$lote[0]->osa_servico."";                    
          $sql.="       ,".$lote[0]->osa_quantidade."";          
          $sql.="       ,".$lote[0]->osa_valor.""; 
          $sql.="       ,'".$lote[0]->osa_pagamento."'"; 
          $sql.="       ,".$lote[0]->osa_parcelas.""; 
          $sql.="       ,".$lote[0]->osa_codind.""; 
          $sql.="       ,'P'";
          $sql.="       ,".$_SESSION["usr_codigo"].")";  
          array_push($arrUpdt,$sql);  
          $atuBd = true;          
        };    
        if( $lote[0]->rotina=="detOs" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("detOs",$lote[0]->login,$lote[0]->dos_codgmp);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        /////////////////////////
        // Help colaborador
        /////////////////////////
        if( $lote[0]->rotina=="hlpColaborador" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("hlpColaborador",$lote[0]->login,$lote[0]->codpei);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        /////////////////////////
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="filtrar" ){     
          $sql= "SELECT 
                A.OSA_CODIGO AS OS,
                VND.FVR_NOME as VENDEDOR,
                FVR.FVR_NOME AS CLIENTE,
                SRV.SRV_NOME AS SERVICO,
                A.OSA_QTD AS QUANTIDADE,
                A.OSA_VALOR AS VALOR,
                FC.FC_NOME AS COBRANCA,
                OSA_PARCELAS AS PARCELAS,
                COALESCE(IND.FVR_NOME,'SEM INDICACAO') AS INDICACAO,
                A.OSA_DTBAIXA AS DATA_BAIXA,
                A.OSA_CODENTREGA AS ENTREGA,
                A.OSA_CODINSTALA AS INSTALACAO,
                COL.FVR_NOME AS COLABORADOR,
                A.OSA_GMPCODIGO AS AUTO,
                US.US_APELIDO AS USUARIO
                FROM ORDEMSERVICOAVULSA AS A
                LEFT OUTER JOIN FAVORECIDO VND  ON A.OSA_CODVND = VND.FVR_CODIGO
                LEFT OUTER JOIN FAVORECIDO FVR ON A.OSA_CODFVR = FVR.FVR_CODIGO 
                LEFT OUTER JOIN SERVICO SRV ON A.OSA_CODSRV = SRV.SRV_CODIGO
                LEFT OUTER JOIN FORMACOBRANCA FC ON A.OSA_PGTO = FC.FC_CODIGO
                LEFT OUTER JOIN FAVORECIDO IND ON A.OSA_INDFVR = IND.FVR_CODIGO
                LEFT OUTER JOIN FAVORECIDO COL ON A.OSA_COLABORADOR = COL.FVR_CODIGO
                LEFT OUTER JOIN USUARIOSISTEMA US ON A.OSA_CODUSR = US.US_CODIGO";          
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        if( $lote[0]->rotina=="baixaos" ){
          $sql ="INSERT INTO VORDEMSERVICOAVULSO";
          $sql.="   SET OS_ACAO=".$lote[0]->os_acao;
          $sql.="       ,OS_CODVCL='".$lote[0]->os_codvcl."'";            
          $sql.="       ,OS_VALOR='".$lote[0]->os_valor."'";                    
          $sql.="       ,OS_COMPLEMENTO='".$lote[0]->os_complemento."'";
          $sql.="       ,OS_ESTOQUE='".$lote[0]->os_estoque."'";          
          $sql.="       ,OS_CODUSR=".$_SESSION["usr_codigo"];
          $sql.=" WHERE (OS_CODIGO=".$lote[0]->os_codigo.")";
           
          array_push($arrUpdt,$sql);  
          $atuBd = true;          
        };   
        /////////////////////////
        // Reagendamento
        /////////////////////////
        if( $lote[0]->rotina=="adiar" ){ 
          $os=$lote[0]->os_codigo;
          $sql ="UPDATE ordemservico";
          $sql.="   SET OS_DTAGENDA='".$lote[0]->os_dtAgenda."'";
          $sql.="       ,OS_CODUSR=".$_SESSION["usr_codigo"];
          $sql.=" WHERE (OS_CODIGO=".$os.")";  
          array_push($arrUpdt,$sql);                        

          $sql ="INSERT INTO DETALHEOS(";
          $sql.="DOS_CODOS";
          $sql.=",DOS_CODMSG";
          $sql.=",DOS_COMPLEMENTO";          
          $sql.=",DOS_CODUSR) VALUES(";
          $sql.="'$os'";
          $sql.=",".$lote[0]->dos_codmsg;
          $sql.=",'".$lote[0]->dos_complemento."'";          
          $sql.=",".$_SESSION["usr_codigo"];
          $sql.=")";          
          array_push($arrUpdt,$sql);
          $atuBd = true;
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
    <title>OS Avulsa</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="js/jsBiblioteca.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaVendedorF10.js"></script>        
    <script src="tabelaTrac/f10/tabelaFavorecidoF10.js"></script>     
    <script src="tabelaTrac/f10/tabelaServicoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaEnderecoF10.js"></script>
     <script src="tabelaTrac/f10/tabelaGrupoModeloProdutoF10.js"></script>   
    <script src="tabelaTrac/f10/tabelaColaboradorF10.js"></script>   
<!--     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script> -->
    <script>      
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsOs={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "OS"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "VENDEDOR"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"    
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"                  
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "CLIENTE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "SERVICO"
                      ,"fieldType"      : "str"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "QUANTIDADE"
                      ,"fieldType"      : "int" 
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"                                       
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2"                                  
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"                     
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "COBRANCA"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "PARCELAS"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "INDICACAO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "DATA_BAIXA"
                      ,"fieldType"      : "dat"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true                  
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "ENTREGA"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "INSTALACAO"
                      ,"fieldType"      : "int" 
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"                   
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "COLABORADOR"
                      ,"fieldType"      : "str"                      
                      ,"tamGrd"         : "14em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "AUTO"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
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
            {"texto":"Baixar"           ,"name":"horBaixaOs" ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o"}             
            ,{"texto":"Fechar"          ,"name":"horFechar"     ,"onClick":"6"  ,"enabled":true ,"imagem":"fa fa-close"}
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)              
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmOs"                   // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaOs"                // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmOs"                   // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnOs"                  // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblOs"                   // Nome da table
          ,"prefixo"        : "Me"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VORDEMSERVICOAVULSA"           // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "140em"                   // Tamanho da table
          ,"height"         : "58em"                    // Altura da table
          ,"nChecks"        : false                     // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "ORDEM SERVICO AVULSA"           // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"indiceTable"    : "CODIGO"                  // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[40]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objOs === undefined ){  
          objOs=new clsTable2017("objOs");
        }; 
        objOs.montarHtmlCE2017(jsOs); 
        $doc("dPaifrmOs").style.float="none";
        ///////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui 
        // esta garante o chkds[0].?????? e objCol
        ///////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsOs,["VENDEDOR","CLIENTE","SERVICO","QUANTIDADE","VALOR","FORMA_COBRANCA","PARCELAS","DIA","INDICACAO","DATA_BAIXA","ENTREGA","INSTALACAO","COLABORADOR","AUTO","USUARIO"]);
        btnFiltrarClick();
      });
      var objOs;                      // Obrigatório para instanciar o JS TFormaCob
      var jsOs;                       // Obj principal da classe clsTable2017
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var contMsg   = 0;              // contador para mensagens
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var chkds;                      // Guarda todos registros checados na table 
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario 
      var ind;     
      var ppvCorreio;                 // Abrir popover somente com click(Codigo rastreamento)
      var objPadF10;                  // Obrigatório para instanciar o JS FavorecidoF10      
      var objVndF10;                  // Obrigatório para instanciar o JS VendedorF10            
      var objSrvF10;                  // Obrigatório para instanciar o JS VendedorF10   
      var objFvrF10;                  // Obrigatório para instanciar o JS FavorecidoF10 
      var objCntEF10                  // Obrigatório para instanciar JS GrupoModeloProduto      
      var objColF10                   // Obrigatório para instanciar JS Colaborador 
      var evtCorreio;                 // Eventos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = jsConverte(jsPub[0].usr_d40).inteiro();
      ////////////////////////////////////////////////////
      // Filtrando os registros para grade de ordemservicoavulso
      ////////////////////////////////////////////////////
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"           );
        clsJs.add("login"       , jsPub[0].usr_login  );
        fd = new FormData();
        fd.append("ordemservicoavulso" , clsJs.fim());
        msg     = requestPedido("Trac_OrdemServicoAvulso.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsOs.registros=objOs.addIdUnico(retPhp[0]["dados"]);
          objOs.ordenaJSon(jsOs.indiceTable,false);  
          objOs.montarBody2017();
          
          // tblOs.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
          //   if( jsConverte(row.cells[objCol.DIA].innerHTML).inteiro()  <= 8 ){  
          //     row.cells[objCol.AGENDA].classList.add("corFonteAlterado");                
          //   };
          // }); 
          $doc("spnTotCtt").innerHTML=jsConverte((retPhp[0]["dados"]).length).emZero(4);
        };  
      };
      
      function fncColaborador(os){
        let codPei=0;
        tblOs.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
          if( jsConverte(row.cells[objCol.OS].innerHTML).inteiro()  == os ){
            codPei=jsConverte(row.cells[objCol.CODPEI].innerHTML).inteiro();
          };
        }); 
        if( codPei>0 ){
          try{          
            clsJs   = jsString("lote");  
            clsJs.add("rotina"  , "hlpColaborador"    );
            clsJs.add("login"   , jsPub[0].usr_login  );
            clsJs.add("codpei"  , codPei              );
            fd = new FormData();
            fd.append("ordemservico" , clsJs.fim());
            msg     = requestPedido("Trac_OrdemServico.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno == "OK" ){
              //////////////////////////
              // Preciso passar o heigth
              //////////////////////////
              let clsCode = new concatStr();  
              clsCode.concat("<div id='dPaiChk' class='divContainerTable' style='height: 18em; width:100%;border:none;overflow-y:auto;'>");
              clsCode.concat(retPhp[0]["dados"]);
              clsCode.concat("</div>");
              janelaDialogo(
                { height          : "25em"
                  ,body           : "16em"
                  ,left           : "300px"
                  ,top            : "60px"
                  ,tituloBarra    : "Colaborador"
                  ,code           : clsCode.fim()
                  ,width          : "70em"
                  ,fontSizeTitulo : "1.8em"
                }
              );  
            };  
          }catch(e){
            gerarMensagemErro('catch',e.message,{cabec:"Erro"});
          };
        };  
      };  
      function fncAdiar(){    
        try{
          if( jsConverte("#edtMotivo").coalesce("") == "" )
            throw "MOTIVO DEVE SER INFORMADO!"; 
          if( jsConverte("#edtDtAgenda").datavalida() == false )
            throw "DATA INVALIDA!"; 

          clsJs = jsString("lote");                
          clsJs.add("rotina"          , "adiar"                                         );             
          clsJs.add("login"           , jsPub[0].usr_login                              );
          clsJs.add("os_codigo"       , chkds[0].OS                                     );            
          clsJs.add("os_dtAgenda"     , jsConverte("#edtDtAgenda").formato("mm/dd/yyyy"));  // Aqui soh entra uma data validada
          clsJs.add("dos_codmsg"      , 13                                              );  // Mensagem para reagendamento                      
          clsJs.add("dos_complemento" , jsConverte("#edtMotivo").upper()                );  // Motivo do reagendamento
          fd.append("ordemservico"    , clsJs.fim());
          msg     = requestPedido("Trac_OrdemServico.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});  
          } else {  
            tblOs.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              if( jsConverte(row.cells[objCol.OS].innerHTML).inteiro()  == chkds[0].OS ){
                row.cells[objCol.AGENDA].innerHTML=$doc("edtDtAgenda").value;
              };  
            }); 
            ppvAdiar.hide();
            tblOs.retiraChecked()
          }; 
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      function fncCadastrarOs(){
         try{

          clsJs = jsString("lote");                
          clsJs.add("rotina"          , "osAvulsa"                                      );             
          clsJs.add("login"           , jsPub[0].usr_login                              );
          clsJs.add("osa_vendedor"    , $doc("edtVendedor").getAttribute("data-id")     );
          clsJs.add("osa_favorecido"  , $doc("edtFavorecido").getAttribute("data-id")   );  
          clsJs.add("osa_servico"     , $doc("edtServico").getAttribute("data-id")      );
          clsJs.add("osa_quantidade"  , $doc("edtQtd").value                            );  
          clsJs.add("osa_valor"       , $doc("edtVlr").value                            );  
          clsJs.add("osa_pagamento"   , $doc("edtPgto").value                           );
          clsJs.add("osa_parcelas"    , $doc("edtParcela").value                        );  
          clsJs.add("osa_codind "     , $doc("edtIndPor").getAttribute("data-id")       );
          var fd = new FormData();
          fd.append("ordemservicoavulso" , clsJs.fim());
          msg=requestPedido("Trac_OrdemServicoAvulso.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            console.log('erro em algum lugar'); 
            gerarMensagemErro("os",retPhp[0].erro,{cabec:"Aviso"});            
          } else {  
            $doc("modal-default").modal('hide');
            /////////////////////////////////////////////////
            // Atualizando a grade deste formulario
            /////////////////////////////////////////////////
          };
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        }; 
      }
      ////////////////////////////////
      // Gravando a observacao para OS
      ////////////////////////////////  
      function gravaObs(){
        try{
          let obs=jsConverte("#edtObs").upper();
          ///////////////////////////////////////////////////////////////////////////
          // Classe para montar envio para o Php
          ///////////////////////////////////////////////////////////////////////////    
          clsJs = jsString("lote");                
          clsJs.add("rotina"                , "gravaobs"          );              
          clsJs.add("login"                 , jsPub[0].usr_login  );
          clsJs.add("dos_codigo"            , chkds[0].OS         );          
          clsJs.add("dos_complemento"       , obs                 );          
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("ordemservicoavulso" , clsJs.fim());
          msg=requestPedido("Trac_OrdemServico.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("os",retPhp[0].erro,{cabec:"Aviso"});            
          } else {  
            modalInitJS.hide();            
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      /////////////////
      // Baixa de OS
      /////////////////
      function horBaixaOsClick(){
        try{        
          ///////////////////////////////////////////////////////////////////
          // Checagem basica, qdo gravar checo novamente validando as colunas
          ///////////////////////////////////////////////////////////////////
          chkds = objOs.gerarJson("1").gerar();
          clsJs = jsString("lote");
          $doc("baixaOs").style.display="block";
          
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function fncOsValor(el){
        $doc("edtOsValor").value = ( el.value=="V" ? el.getAttribute("data-valor") : el.getAttribute("data-multa") );
      };  
      //////////////////////////
      // BAIXAR ORDEM DE SERVICO
      //////////////////////////
      function baiConfirmarClick(){
        
        try{
          let obs         = jsConverte("#edtOsObs").upper();
          let colaborador = jsConverte("#edtOsColaborador").upper();
          let acao        = parseInt($doc("cbStatus").options[$doc("cbStatus").selectedIndex].getAttribute("data-acao"));
          let placa       = jsConverte("#edtOsCodVcl").upper();
          
          // acao trigger( 1=Baixa 2=NoShow 3=Improdutividade )
          if( (acao==2) || (acao==3) ){
            if( placa=="NSA" )
              throw "AUTO DEVE ESTAR VINCULADO A UMA PLACA!";
            
            if( obs=="" ){  
              throw "PARA STATUS "+$doc("cbStatus").options[$doc("cbStatus").selectedIndex].text+" OBRIGATORIO OBSERVAÇÃO!";
            } else{
              obs="COLABORADOR "+chkds[0].NOMECOLABORADOR+" "+obs
            }  
          };  
          if( (acao==1) || (obs=="") ){
            obs="COLABORADOR "+chkds[0].NOMECOLABORADOR+" SERVICO EXECUTADO";
          };  
          ///////////////////////////////////////////////////////////////////////////
          // Classe para montar envio para o Php
          ///////////////////////////////////////////////////////////////////////////   
          clsJs = jsString("lote");                
          clsJs.add("rotina"          , "baixaos"                           );              
          clsJs.add("login"           , jsPub[0].usr_login                  );
          clsJs.add("os_codigo"       , chkds[0].OS                         );          
          clsJs.add("os_acao"         , acao                                );
          clsJs.add("os_codvcl"       , placa                               );
          clsJs.add("os_estoque"      , $doc("cbEstoque").value             );          
          clsJs.add("os_valor"        , jsConverte("#edtOsValor").dolar()   );          
          clsJs.add("os_complemento"  , obs                                 );
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("ordemservico" , clsJs.fim());
          msg=requestPedido("Trac_OrdemServico.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("os",retPhp[0].erro,{cabec:"Aviso"});            
          } else {  
            $doc("baixaOs").style.display="none";
            /////////////////////////////////////////////////
            // Atualizando a grade deste formulario
            /////////////////////////////////////////////////
            tblOs.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              if( row.cells[objCol.OS].innerHTML  == chkds[0].OS ){
                row.cells[objCol.BAIXA].innerHTML=jsDatas(0).retDDMMYYYY();
              };
            }); 
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };  
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral" style="border-right:none;">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="objOs.imprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela.">
            <i class="indFa fa-print"></i>
          </div>  
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="objOs.excel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela.">
            <i class="indFa fa-file-excel-o"></i>
          </div>
          <div id="divBlOsAvulsa" class="divBarraLateral" 
                    data-toggle="modal" data-target="#modal-default"                              
                    data-content="Cadastrar OS avulsa.">
            <i class="indFa fa-plus"></i>
          </div>
          <div id="modalObs" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalObsLabel" aria-hidden="true">
            <div class="modal-dialog" style="width:25%;" >
              <div class="modal-content">

              </div>
            </div>
          </div>
          
          
        </section>
      </aside>
      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-wrench" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">OS</span>
              <span id="spnTotCtt" class="infoBoxNumber"></span>
            </div>
          </div>
        </div>  

        <section id="collapseAdiar" class="section-combo" data-tamanho="230">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Reagendar data O.S.</a>
              <button type="button" id="popoverAdiar" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group' style='width:300px;'>
                                        <label for='edtMotivo' class='control-label'>Motivo</label>
                                        <input type='text' class='form-control' id='edtMotivo' placeholder='informe' />
                                      </div>
                                      <div class='form-group' style='width:150px;'>
                                        <label for='edtDtAgenda' class='control-label'>Nova data</label>
                                        <input type='text' class='form-control' id='edtDtAgenda' placeholder='##/##/####' onkeyup=mascaraNumero('##/##/####',this,event,'dig') />
                                      </div>                                        
                                      <div class='form-group'>
                                        <button onClick='fncAdiar();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>
              </button>              
            </span>
          </div>
        </section>  
        <!---->  
      </div>
      <section>
        <section id="sctnOs">
        </section>  
      </section>
      
      <form method="post"
            name="frmOs"
            id="frmOs"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/> 
        <div class="inactive">
          <input id="edtTerceiroTbl" value="*" type="text" />
        </div>
      </form>  
    </div>
    
    <!--
    Buscando o historico do OS
    -->
    <section id="collapseSectionOs" class="section-collapse">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a class="btn btn-default disabled">Buscar</a>
            <button id="abreOs" class="btn btn-primary" 
                                  data-toggle="collapse" 
                                  data-target="#evtAbreOs" 
                                  aria-expanded="true" 
                                  aria-controls="evtAbreOs" 
                                  type="button">Historico OS</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreOs" aria-expanded="false" role="presentation">
          <div id="cllOs" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblOs" class="alert-info">Mostrando historico da OS</label>
          </div>
        </div>
      </div>
    </section>
    <!-- 
    BAIXA DE OS
    -->    
    <div id="baixaOs" class="frmTable" style="display:none; width:90em; margin-left:11em;margin-top:5.5em;position:absolute;">
      <div id="divTitulo" class="frmTituloManutencao">Baixa<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>
      <div style="height: 230px; overflow-y: auto;">
        <div class="campotexto campo100">
           <div class="campotexto campo100">
          <!-- Endereco de entrega -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodEnt"
                                                onClick="entF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodEnt">ENTREGA:</label>
          </div>


          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesEnt" type="text" disabled /><label class="campo_label campo_required" for="edtDesEnt">ENDERECO ENTREGA</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtCddEnt" type="text" disabled /><label class="campo_label campo_required" for="edtCddEnt">CIDADE</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCepEnt" type="text" disabled /><label class="campo_label campo_required" for="edtCepEnt">CEP</label>
          </div>
          <!-- Endereco de instalacao -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodIns"
                                                onClick="insF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodIns">INSTALA:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesIns" type="text" disabled /><label class="campo_label campo_required" for="edtDesIns">ENDERECO INSTALACAO</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtCddIns" type="text" disabled /><label class="campo_label campo_required" for="edtCddIns">CIDADE</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCepIns" type="text" disabled /><label class="campo_label campo_required" for="edtCepIns">CEP</label>
          </div>

          <!-- Colaborador -->
          <div class="campotexto campo50">
            <input class="campo_input inputF10" id="ageCodCol"
                                                onClick="colF10Click(this);"                                               
                                                data-oldvalue="0000"
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="ageCodCol">COLABORADOR:</label>
          </div>
          <div class="campotexto campo100">
            <input class="campo_input_titulo input" id="ageDesCol" type="text" disabled /><label class="campo_label campo_required" for="ageDesCol">COLABORADOR</label>
          </div>
          
          <div onClick="baiConfirmarClick();" id="baiConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
          <div onClick="document.getElementById('baixaOs').style.display='none';" id="ageCancelar" class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
        </div>  
      </div>
    </div>
    <!--
    FIM DA BAIXA DE OS
    -->
    <script>
      //
      //
      ///////////////////
      // Adiar
      ///////////////////
      ppvAdiar = new Popover('#popoverAdiar',{ trigger: 'click'} );      
      evtAdiar = document.getElementById('popoverAdiar');
      evtAdiar.status="ok";
      //////////////////////////////////////////////
      // show.bs.popover(quando o metodo eh chamado)
      //////////////////////////////////////////////
      evtAdiar.addEventListener('show.bs.popover', function(event){
        try{
          chkds=objOs.gerarJson("1").gerar();
          evtAdiar.status="ok";            
        }catch(e){
          evtAdiar.status="err";
          gerarMensagemErro("catch",e,"Erro");
        };
      },false);  
      //////////////////////////////////////////////////
      // shown.bs.popover(quando o metodo eh completado)
      //////////////////////////////////////////////////
      evtAdiar.addEventListener('shown.bs.popover', function(event){
        if( evtAdiar.status=="err" ){
          ppvAdiar.hide();
        } else {    
          $doc("edtMotivo").value   = "";
          $doc("edtDtAgenda").value = "";
          $doc("edtMotivo").foco();
        };
      }, false);
      
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                                                PopUp OS                                                    //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreOs  = new Collapse($doc('abreOs'));
      abreOs.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreOs = document.getElementById('evtAbreOs');
      collapseAbreOs.addEventListener('show.bs.collapse', function(el){ 
        try{
          chkds=objOs.gerarJson("1").gerar();
          //if( chkds[0].CODGMP == 0 )
          //  throw "AUTO NÃO LOCALIZADO PARA HISTORICO!";  
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "detOs"             );  // Detalhe da OS
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("os_codigo"   , chkds[0].OS         );
          clsJs.add("dos_codgmp"  , chkds[0].AUTO    ); // Angelo Kokiso - alterando where para pegar todos detalhes do mesmo auto.
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("ordemservico" , clsJs.fim());
          msg=requestPedido("Trac_OrdemServico.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            $doc("cllOs").innerHTML=retPhp[0]["dados"];
            $doc("lblOs").innerHTML="Mostrando historico da OS <b>"+chkds[0].OS+"</b>";
            abreOs.status="ok";
          };  
        }catch(e){
          abreOs.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbreOs.addEventListener('shown.bs.collapse', function(){ 
        if( abreOs.status=="err" )
          abreOs.hide();
      },false);
      //
      //
      ///////////////////////////////////////////////////
      // Formulario para Observacao da OS
      ///////////////////////////////////////////////////
      var modalObs = document.getElementById('modalObs');
      var btnModal  = document.getElementById('divBlObs');
      let clsStr = new concatStr();
      clsStr.concat('<div class="modal-header">'                                                            );
      clsStr.concat(  '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'        );
      clsStr.concat(    '<span aria-hidden="true">×</span>'                                                 );
      clsStr.concat(  '</button>'                                                                           );
      clsStr.concat(  '<h4 class="modal-title" id="modalObsLabel">OBSERVAÇÃO PARA OS</h4>'                  );
      clsStr.concat('</div>'                                                                                );
      clsStr.concat('<div class="modal-body">'                                                              );
      clsStr.concat(  '<div class="form-group campo100" style="float:left;height:80px">'                    );
      clsStr.concat(    '<textarea id="edtObs" wrap="on" cols="40" rows="5" maxlength="200"'                ); 
      clsStr.concat(               'style="width:100%;">'                                                   );
      clsStr.concat(    '</textarea>'                                                                       );      
      clsStr.concat(  '</div>'                                                                              );
      clsStr.concat('</div>'                                                                                );            
      clsStr.concat('<div class="modal-footer">'                                                            );
      clsStr.concat(  '<button type="button" class="btn btn-primary" onClick="gravaObs();">Gravar</button>' );
      clsStr.concat(  '<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>'  );
      clsStr.concat(  '</div>'                                                                              );
      clsStr.concat('</div>'                                                                                );      
      //////////////////////////  
      // Inicilializando o modal
      //////////////////////////
      // var modalInitJS = new Modal(modalObs, {
      //   content: sql=clsStr.fim(),
      //   backdrop: 'static'
      // });
      // btnModal.addEventListener('click',function(e){
      //   try{
      //     chkds=objOs.gerarJson("1").gerar();
      //     $doc("edtObs").value ="";
      //     modalInitJS.show();
      //   }catch(e){
      //     gerarMensagemErro("catch",e,{cabec:"Aviso"});  
      //   };
      // },false);
      // //////////////////////////
      // // Instanciando os eventos
      // //////////////////////////
      // modalObs.addEventListener('show.bs.modal', function(event){
      // }, false);      
      // modalObs.addEventListener('shown.bs.modal', function(event){
      //   $doc("edtObs").foco();
      // }, false);
      ////////////////////////////
      //  AJUDA PARA FAVORECIDO //
      ////////////////////////////
      function fvrF10Click(obj){ 
        fFavorecidoF10(0,obj.id,"edtFavorecido",100         
            );   
      };
      function RetF10tblFvr(arr){
        document.getElementById("edtFavorecido").value  = arr[0].DESCRICAO;
        document.getElementById("edtFavorecido").setAttribute("data-id",arr[0].CODIGO);
      };
      //////////////////////////////////////////
      //  AJUDA PARA QUEM INDICOU(FAVORECIDO) //
      //////////////////////////////////////////
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
        document.getElementById("edtIndPor").value  = arr[0].DESCRICAO;
        document.getElementById("edtIndPor").setAttribute("data-id",arr[0].CODIGO);
      };
      ///////////////////////////////
      //     AJUDA PARA VENDEDOR   //
      ///////////////////////////////
      function vndF10Click(obj){ 
        fVendedorF10(0,obj.id,"edtVendedor",100,{tamColNome:"29.5em",ativo:"S", gpfvr:'cliente' } ); 
      };
      function RetF10tblVnd(arr){
        document.getElementById("edtVendedor").value  = arr[0].DESCRICAO;
        document.getElementById("edtVendedor").setAttribute("data-id",arr[0].CODIGO);
      };
      ////////////////////////////
      //  AJUDA PARA SERVICO    //
      ////////////////////////////
      function srvF10Click(obj){ 
        fServicoF10(0,obj.id,"edtServico",0
          ,{
            entsai      : "S"
            ,codemp     : jsPub[0].emp_codigo
          }
        ); 
      };
      function RetF10tblSrv(arr){
        document.getElementById("edtServico").setAttribute("data-id",arr[0].CODIGO);
        document.getElementById("edtServico").value      = arr[0].DESCRICAO;
      };
      /////////////////////////////////////////////
      //      AJUDA PARA ENDERECOENTREGA         //
      /////////////////////////////////////////////
      function entF10Click(obj){ 
        fEnderecoF10(0,"nsa","null",100
          ,{ativo:"S" 
            ,divWidth:"76em"
            ,tblWidth:"74em"
            ,tbl:"tblEnt"
        }); 
      };
      function RetF10tblEnt(arr){
        $doc("edtCodEnt").value      = arr[0].CODIGO;
        $doc("edtDesEnt").value      = arr[0].ENDERECO;
        $doc("edtCddEnt").value      = arr[0].CIDADE;        
        $doc("edtCepEnt").value      = arr[0].CEP;                
        $doc("edtCodEnt").setAttribute("data-oldvalue",arr[0].CODIGO);

        $doc("edtCodIns").value      = $doc("edtCodEnt").value;
        $doc("edtDesIns").value      = $doc("edtDesEnt").value;
        $doc("edtCddIns").value      = $doc("edtCddEnt").value;        
        $doc("edtCepIns").value      = $doc("edtCepEnt").value;                
        $doc("edtCodIns").setAttribute("data-oldvalue",arr[0].CODIGO);

      };
      /////////////////////////////////////////////
      //      AJUDA PARA INSTALACAO              //
      /////////////////////////////////////////////
      function insF10Click(obj){ 
        fEnderecoF10(0,"nsa","null",100
          ,{divWidth:"76em"
            ,tblWidth:"74em"
            ,tbl:"tblIns"
        }); 
      };
      function RetF10tblIns(arr){
        $doc("edtCodIns").value      = arr[0].CODIGO;
        $doc("edtDesIns").value      = arr[0].ENDERECO;
        $doc("edtCddIns").value      = arr[0].CIDADE;        
        $doc("edtCepIns").value      = arr[0].CEP;                
        $doc("edtCodIns").setAttribute("data-oldvalue",arr[0].CODIGO);
      }; 
      /////////////////////////////////////////////
      //      AJUDA PARA COLABORADOR              /
      /////////////////////////////////////////////
      function colF10Click(obj){
        try{
          if( parseInt($doc("edtCodIns").value)<=0 )
            throw "FAVOR INFORMAR UM CODIGO DE INSTALACAO!";  
          fColaboradorF10(0,"nsa","null",100
            ,{codins:parseInt($doc("edtCodIns").value)
              ,divWidth:"76em"
              ,tblWidth:"74em"
          }); 
          
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function RetF10tblCol(arr){
        let colGrp = '';
        let colNms = '';
        for(let i = 0; i < arr.length; i++){
           colGrp = colGrp.concat(arr[i].CODIGO,',');
           colNms = colNms.concat(arr[i].COLABORADOR,',');
        }
        $doc("ageCodCol").value      = colGrp.slice(0, -1);
        $doc("ageDesCol").value      = colNms.slice(0, -1);
        $doc("ageCodCol").setAttribute("data-oldvalue",colGrp.slice(0, -1));       
      };
      function cleanModal(){
         document.getElementById("edtVendedor").value = '';
         document.getElementById("edtIndPor").value = '';
         document.getElementById("edtFavorecido").value = '';
         document.getElementById("edtServico").value = '';

         document.getElementById("edtQtd").value = '';
         document.getElementById("edtVlr").value = '';
         document.getElementById("edtParcela").value = '1';
      }
    </script>

    <div class="modal fade" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="text-align: center;">Cadastro de OS Avulsa</h4>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="edtVendedor">Vendedor</label> 
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="search" readonly class="form-control" id="edtVendedor" data-id =""  style="cursor: pointer;" required onClick="vndF10Click(this);">
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label for="edtFavorecido">Favorecido</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="search" readonly class="form-control" id="edtFavorecido" data-id =""  style="cursor: pointer;" required onClick="fvrF10Click(this);">
                </div>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label for="edtServico">Serviço</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-search"></i></span>
                  <input type="search" readonly class="form-control" id="edtServico" data-id ="" style="cursor: pointer;" required
                  onClick="srvF10Click(this);">
                </div>
              </div>
              <div class="form-group col-md-2">
                <label for="edtQtd">Quantidade</label>
                <input type="text" class="form-control" id="edtQtd" required>
              </div>
              <div class="form-row">
                <div class="form-group col-md-2">
                  <label for="edtVlr">Valor</label>
                  <input type="text" class="form-control" id="edtVlr" required>
                </div>
                <div class="form-group col-md-4">
                  <label for="edtPgto">Forma de pgto</label>
                  <select id="edtPgto" class="form-control">
                    <option value="NSA">-------</option>
                    <option value="BOL">BOLETO</option>
                    <option value="BOR">BORDEIRO</option>
                    <option value="CAI">CAIXINHA</option>
                    <option value="CAR">CARTEIRA</option>
                    <option value="CRT">CARTORIO</option>
                    <option value="CHE">CHEQUE</option>
                    <option value="CD">COB DESCONTO</option>
                    <option value="CS">COB SIMPLES</option>
                    <option value="CC">CREDITO EM CONTA</option>
                    <option value="DC">DEBITO EM CONTA</option>
                    <option value="DEP">DEPOSITO</option>
                    <option value="EE">EXTRA FINANCEIRO</option>
                    <option value="SP">SISPAG</option>
                    <option value="TED">TED</option>
                    <option value="TRA">TRANSFERENCIA</option>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label for="edtParcela">Parcelas</label>
                  <input type="text" class="form-control" id="edtParcela" placeholder="1" required>
                </div>
                <div class="form-group col-md-5">
                  <label for="edtIndPor">Indicado Por:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="search" readonly class="form-control" id="edtIndPor" data-id =""  style="cursor: pointer;"
                    onClick="indF10Click(this);">
                  </div>
                </div>
              </div>
              <div class="form-group col-md-12 col-md-offset-8">
                <button type="submit" class="btn btn-success" id="btnConfirmar" onClick="fncCadastrarOs();">Cadastrar</button>
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" onclick="cleanModal();">Close</button>
              </div>
            </form>
          </div>  
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
        <!-- /.modal -->  
  </body>
</html>