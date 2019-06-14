<?php
  session_start();
  if( isset($_POST["ordemservico"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/dataCompetencia.class.php");
      require("classPhp/selectRepetido.class.php");       						      

      $clsCompet  = new dataCompetencia();    
      $vldr       = new validaJSon();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["ordemservico"]);
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
          $sql ="INSERT INTO DETALHEOS(";
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
        if( $lote[0]->rotina=="detOs" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("detOs",$lote[0]->login,$lote[0]->os_codigo);
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
          $sql= "SELECT A.OS_CODIGO AS OS";
          $sql.="       ,A.OS_CODCNTT AS CONTRATO";
          $sql.="       ,FVR.FVR_APELIDO AS CLIENTE";          
          $sql.="       ,CONVERT(VARCHAR(10),A.OS_EMISSAO,103) AS EMISSAO";
          $sql.="       ,CONVERT(VARCHAR(10),A.OS_DTAGENDA,103) AS AGENDA";          
          $sql.="       ,CASE WHEN A.OS_LOCAL='C' THEN 'EXT' ELSE 'INT' END AS LOCAL";
          $sql.="       ,MSG.MSG_NOME AS REFERENTE";
          /*  
          $sql.="       ,CASE WHEN (A.OS_REFERENTE='DES')  THEN 'DESISTALACAO'";
          $sql.="             WHEN (A.OS_REFERENTE='INS')  THEN 'INSTALACAO'";  
          $sql.="             WHEN (A.OS_REFERENTE='MAN')  THEN 'MANUTENCAO'";                              
          $sql.="             WHEN (A.OS_REFERENTE='REI')  THEN 'REINSTALACAO'";  
          $sql.="             WHEN (A.OS_REFERENTE='REV')  THEN 'REVISAO' END AS REFERENTE";
          */
          $sql.="       ,A.OS_CODGMP AS AUTO";          
          $sql.="       ,A.OS_CODPEI AS CODPEI";                    
          $sql.="       ,PEI.FVR_APELIDO AS COLABORADOR";
          $sql.="       ,GMP.GMP_PLACACHASSI AS PLACA";
          $sql.="       ,A.OS_VALOR AS VALOR";          
          $sql.="       ,CONVERT(VARCHAR(10),A.OS_DTBAIXA,103) AS BAIXA";          
          $sql.="       ,US.US_APELIDO";                    
          $sql.="       ,DATEDIFF(day,GETDATE(),A.OS_DTAGENDA) AS DIA";   
          $sql.="       ,CNTT_VLRNOSHOW AS VLRNOSHOW";
          $sql.="       ,CNTT_VLRIMPRODUTIVEL AS VLRIMPRODUTIVEL";
          $sql.="       ,PEI.FVR_NOME AS NOMECOLABORADOR";          
          $sql.="  FROM ORDEMSERVICO A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN CONTRATO CNTT ON A.OS_CODCNTT=CNTT_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON CNTT.CNTT_CODFVR=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOMODELOPRODUTO GMP ON A.OS_CODGMP=GMP.GMP_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO PEI ON A.OS_CODPEI=PEI.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN MENSAGEM MSG ON A.OS_CODMSG=MSG.MSG_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.OS_CODUSR=US.US_CODIGO";           
          //$sql.=" WHERE A.OS_CODCNTT=".$codigo;
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        if( $lote[0]->rotina=="baixaos" ){
          $sql ="UPDATE VORDEMSERVICO";
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
          $sql ="UPDATE ORDEMSERVICO";
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
    <title>Ordem servico</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="js/jsBiblioteca.js"></script>
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
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "CNTT"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Número do contrato"                          
                      ,"popoverLabelCol": "Contrato"                      
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "CLIENTE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "35"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "EMISSAO"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "AGENDA"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Data agendada para execução do servico"                          
                      ,"popoverLabelCol": "Data"                      
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "LOC"
                      ,"fieldType"      : "str"                                  
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"popoverTitle"   : "Serviço a ser executado EXTERNO ou INTERNO"                          
                      ,"popoverLabelCol": "Local"                      
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "REFERENTE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "35"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "AUTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "CODPEI"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "COLABORADOR"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "35"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"func"           : "fncColaborador(this.parentNode.cells[1].innerHTML);"											                      
                      ,"popoverTitle"   : "Clique no nome para ver dados"                          
                      ,"popoverLabelCol": "Colaborador"                      
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "PLACA"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "35"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"sepMilhar"      : true                      
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "BAIXA"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "DIA"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":16 ,"labelCol"       : "VLRNOSHOW"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"sepMilhar"      : true                      
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":17 ,"labelCol"       : "VLRIMPROD"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"sepMilhar"      : true                      
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":18 ,"labelCol"       : "NOMECOLABORADOR"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
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
            /*
            ,{"texto":"Excluir"         ,"name":"horExcluir"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-minus"
                                        ,"popover":{title:"Excluir",texto:"Esta opção exclui o contrato do sistema em definitivo.<hr>Não existe maneira de recuperar o registro",aviso:"warning"}} 
            ,{"texto":"Empenho"         ,"name":"horEmpenho"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-eye-slash"      
                                        ,"popover":{title:"Empenho",texto:"Opção para empenhar individualmente cada auto pelo seu número de serie<hr>"
                                                                          +"O auto obrigatoriamente deve estar em estoque"}} 
            ,{"texto":"Agenda"          ,"name":"horAgenda"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-calendar"
                                        ,"popover":{title:"Agenda",texto:"Opção para informar endereço entrega/instalação/data/colaborador<hr>"
                                                                          +"O auto obrigatoriamente deve estar empenhado"}} 
            ,{"texto":"Placa"           ,"name":"horPlaca"      ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-truck"
                                        ,"popover":{title:"Placa/Ativação",texto:"Opção para informar a <b>placa</b> e <b>data de ativação</b>"}} 
            ,{"texto":"Copia contrato"  ,"name":"horCopiaPed"   ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-print"          }                                    
            */
            ,{"texto":"Fechar"          ,"name":"horFechar"     ,"onClick":"6"  ,"enabled":true ,"imagem":"fa fa-close"          }
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
          ,"tabelaBD"       : "VORDEMSERVICO"               // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "140em"                   // Tamanho da table
          ,"height"         : "58em"                    // Altura da table
          ,"nChecks"        : false                     // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "ORDEM SERVICO"           // Titulo do relatório
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
        objCol=fncColObrigatoria.call(jsOs,["AGENDA","AUTO","BAIXA","CLIENTE","CNTT","COLABORADOR","CODPEI","DIA","EMISSAO","LOC","NOMECOLABORADOR","OS"
                                           ,"PLACA","REFERENTE","USUARIO","VALOR","VLRNOSHOW","VLRIMPROD"]);
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
      var ppvCorreio;                 // Abrir popover somente com click(Codigo rastreamento)
      var evtCorreio;                 // Eventos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = jsConverte(jsPub[0].usr_d40).inteiro();
      ////////////////////////////////////////////////////
      // Filtrando os registros para grade de ordemservico
      ////////////////////////////////////////////////////
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"           );
        clsJs.add("login"       , jsPub[0].usr_login  );
        fd = new FormData();
        fd.append("ordemservico" , clsJs.fim());
        msg     = requestPedido("Trac_OrdemServico.php",fd); 
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
          
          tblOs.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
            if( jsConverte(row.cells[objCol.DIA].innerHTML).inteiro()  <= 8 ){  
              row.cells[objCol.AGENDA].classList.add("corFonteAlterado");                
            };
          }); 
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
          fd.append("ordemservico" , clsJs.fim());
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
          chkds.forEach(function(reg){
            if( jsConverte(reg.BAIXA).coalesce("") != "" )
              throw "OS JA BAIXADA!";  
          });
          $doc("baixaOs").style.display="block";
          $doc("edtOsCodigo").value = chkds[0].OS;
          $doc("edtOsReferente").value = chkds[0].REFERENTE;
          $doc("edtOsCodVcl").value = chkds[0].PLACA; 
          $doc("edtOsDtBaixa").value  = jsDatas(0).retDDMMYYYY();  
          ////////////////////////////////////////////////
          // Olhando aqui se eh servico interno ou externo
          ////////////////////////////////////////////////
          
          
          $doc("cbStatus").innerHTML="";
          let ceOpt	  = document.createElement("option");          
          ceOpt.value = "V";                              // Aqui eh Valor ou Multa
          ceOpt.text  = "SERVICO EXECUTADO";
          ceOpt.setAttribute("selected","selected");
          ceOpt.setAttribute("data-acao","1");            // Acao que vai ser executada pelo trigger( 1=Baixa 2=NoShow 3=Improdutividade )
          $doc("cbStatus").appendChild(ceOpt);
          if( chkds[0].LOC=="EXT" ){
            ceOpt	  = document.createElement("option");          
            ceOpt.value = "M";                            // Aqui eh Valor ou Multa
            ceOpt.text  = "IMPRODUTIVIDADE";
            ceOpt.setAttribute("data-acao","3");          // Acao que vai ser executada pelo trigger( 1=Baixa 2=NoShow 3=Improdutividade )
            $doc("cbStatus").appendChild(ceOpt);
          } else {
            ceOpt	  = document.createElement("option");          
            ceOpt.value = "M";                            // Aqui eh Valor ou Multa
            ceOpt.text  = "NOSHOW";
            ceOpt.setAttribute("data-acao","2");          // Acao que vai ser executada pelo trigger( 1=Baixa 2=NoShow 3=Improdutividade )
            $doc("cbStatus").appendChild(ceOpt);
          }; 
          /////////////////////////////////////////////////////
          // Aqui informando com quem fica o auto se instalacao
          /////////////////////////////////////////////////////
          $doc("cbEstoque").innerHTML="";
          ceOpt	      = document.createElement("option");          
          ceOpt.value = "CLN";                                // Aqui eh CLN(clinete) ou TRC(terceiro)
          ceOpt.text  = "CLIENTE";
          ceOpt.setAttribute("selected","selected");
          $doc("cbEstoque").appendChild(ceOpt);          
          if( chkds[0].REFERENTE=='DESISTALACAO' ){
            ceOpt	      = document.createElement("option");                      
            ceOpt.value = "TRC";                              // Aqui eh CLN(clinete) ou TRC(terceiro)
            ceOpt.text  = "COLABORADOR";
            $doc("cbEstoque").appendChild(ceOpt);            
          };  
          //
          //
          if( chkds[0].PLACA != "NSA" )
            //jsCmpAtivo("edtOsCodVcl").add("campo_input_titulo").disabled(true);
            jsCmpAtivo("edtOsCodVcl").remove("campo_input").add("campo_input_titulo").disabled(true);          
          //
          //
          $doc("edtOsValor").value  =  chkds[0].VALOR;
          $doc("edtOsColaborador").value  =  chkds[0].NOMECOLABORADOR;
          
          $doc("cbStatus").setAttribute("data-valor",chkds[0].VALOR);
          $doc("cbStatus").setAttribute("data-multa",( chkds[0].LOC=="EXT" ? chkds[0].VLRNOSHOW : chkds[0].VLRIMPROD ) );
          $doc("cbStatus").focus();
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
debugger;        
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
          <div id="divBlObs" class="divBarraLateral" 
                              data-toggle="modal" data-target="#modalObs"                              
                              data-content="Observação para OS.">
            <i class="indFa fa-edit"></i>
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
          <div class="campotexto campo10">
            <input class="campo_input_titulo" id="edtOsCodigo" type="text" disabled />
            <label class="campo_label" for="edtOsCodigo">OS:</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input_titulo " id="edtOsReferente" type="text" disabled />
            <label class="campo_label" for="edtOsReferente">REFERENTE:</label>
          </div>
          
          <div class="campotexto campo25">
            <select onChange="fncOsValor(this);" class="campo_input_combo" id="cbStatus">
            </select>
            <label class="campo_label" for="cbStatus">STATUS:</label>
          </div>
          
          <div class="campotexto campo15">
            <input class="campo_input" id="edtOsCodVcl" type="text" />
            <label class="campo_label" for="edtOsCodVcl">PLACA:</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input_titulo" id="edtOsDtBaixa" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             value="00/00/0000"
                                             type="text" 
                                             maxlength="10" disabled />
            <label class="campo_label campo_required" for="edtOsDtBaixa">DATA BAIXA:</label>
          </div>
          <div class="campotexto campo20">
            <input class="campo_input_titulo edtDireita" id="edtOsValor" 
                                                  type="text" 
                                                  onBlur="fncCasaDecimal(this,2);"
                                                  maxlength="15" disabled />
            <label class="campo_label campo_required" for="edtOsValor">VALOR DA OS:</label>
          </div>
          <div class="campotexto campo100">          
            <input class="campo_input" id="edtOsColaborador" type="text" />
            <label class="campo_label" for="edtOsColaborador">RESPONSAVEL:</label>
          </div>
          <div class="campotexto campo100">          
            <textarea id="edtOsObs" wrap="on" cols="40" rows="5" maxlength="200" style="width:100%;"></textarea>
          </div>
          
          <div class="campotexto campo25">
            <select onChange="fncOsValor(this);" class="campo_input_combo" id="cbEstoque">
            </select>
            <label class="campo_label" for="cbEstoque">ESTOQUE:</label>
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
      var modalInitJS = new Modal(modalObs, {
        content: sql=clsStr.fim(),
        backdrop: 'static'
      });
      btnModal.addEventListener('click',function(e){
        try{
          chkds=objOs.gerarJson("1").gerar();
          $doc("edtObs").value ="";
          modalInitJS.show();
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Aviso"});  
        };
      },false);
      //////////////////////////
      // Instanciando os eventos
      //////////////////////////
      modalObs.addEventListener('show.bs.modal', function(event){
      }, false);      
      modalObs.addEventListener('shown.bs.modal', function(event){
        $doc("edtObs").foco();
      }, false);      
    </script>
  </body>
</html>