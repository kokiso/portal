<?php
  session_start();
  if( isset($_POST["automodeloind"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");       						            
      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["automodeloind"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd  = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $arrUpdt  = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        
        //////////////////
        // Configuracao
        //////////////////
        if( $lote[0]->rotina=="configura" ){
          foreach ( $lote as $reg ){          
            ////////////////////////////////////////////////
            // Obrigatorio achar o auto sem configuracao
            ////////////////////////////////////////////////
            $sql ="SELECT GMP_CODIGO FROM GRUPOMODELOPRODUTO WITH (NOLOCK)";
            $sql.=" WHERE ((GMP_CODIGO=".$reg->gmp_codigo.") AND (GMP_DTCONFIGURADO IS NULL))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else {                
              $sql="";
              $sql.="UPDATE VGRUPOMODELOPRODUTO";
              $sql.="   SET GMP_DTCONFIGURADO='".$reg->gmp_dtconfigurado."'";
              $sql.="       ,GMP_ACAO=1";
              $sql.="       ,GMP_CODUSR=".$_SESSION["usr_codigo"];
              $sql.=" WHERE (GMP_CODIGO=".$reg->gmp_codigo.")";  
              array_push($arrUpdt,$sql);                                    
              $atuBd = true;
            };  
          };  
        };
        
        ///////////////////////////////////////////////
        //        Transferindo para SUCATA           //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="sucata" ){
          foreach ( $lote as $reg ){          
            ////////////////////////////////////////////////
            // Obrigatorio achar o auto sem configuracao
            ////////////////////////////////////////////////
            $sql ="SELECT GMP_CODIGO FROM GRUPOMODELOPRODUTO WITH (NOLOCK)";
            $sql.=" WHERE ((GMP_CODIGO=".$reg->gmp_codigo.") AND (GMP_CODCNTT=0))";
            $classe->msgSelect(true);
            $retCls=$classe->selectAssoc($sql);
            if( ($retCls['retorno'] != "OK") or ($retCls['qtos'] == 0) ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            } else {                
              $sql="";
              $sql.="UPDATE VGRUPOMODELOPRODUTO";
              $sql.="   SET GMP_CODPE='".$reg->gmp_codpe."'";
              $sql.="       ,GMP_ACAO=".$reg->gmp_acao;
              $sql.="       ,GMP_CODUSR=".$_SESSION["usr_codigo"];
              $sql.=" WHERE (GMP_CODIGO=".$reg->gmp_codigo.")";  
              array_push($arrUpdt,$sql);                                    
              $atuBd = true;
            };  
          };  
        };
        if( $lote[0]->rotina=="remontar" ){
           foreach ( $lote as $reg ){  
              $sql="";
              $sql.="UPDATE VGMPREMONTERETORNO";
              $sql.="   SET GMP_CODIGO=".$reg->gmp_codigoNew."";
              $sql.="       ,GMP_CODOLD=".$reg->gmp_codigoOld;
              $sql.="       ,GMP_NUMSERIE=".$reg->gmp_numserie."";
              $sql.="       ,GMP_TIPOEQP=".$reg->gmp_tipoeqp."";
              $sql.="       ,GMP_ACAO= 3 ";
              $sql.="       ,GMP_CODUSR=".$_SESSION["usr_codigo"];
              $sql.=" WHERE (GMP_CODIGO=".$reg->gmp_codaut.")";  
              array_push($arrUpdt,$sql);                                    
              $atuBd = true;
           }
        };
        if( $lote[0]->rotina=="retornar" ){
           foreach ( $lote as $reg ){  
              $sql="";
              $sql.="UPDATE VGMPREMONTERETORNO";
              $sql.="   SET GMP_ACAO= 4 ";
              $sql.="       ,GMP_CODUSR=".$_SESSION["usr_codigo"];
              $sql.=" WHERE (GMP_CODIGO=".$reg->gmp_codaut.")";  
              array_push($arrUpdt,$sql);                                    
              $atuBd = true;
           }
        };
        ///////////////////////////////////////////////
        //            COMPOSICAO DO AUTO             //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="hlpComposicao" ){
          $cSql   = new SelectRepetido();
          $retSql = $cSql->qualSelect("hlpComposicao",$lote[0]->login,$lote[0]->codamp);
          $retorno='[{"retorno":"'.$retSql["retorno"].'","dados":'.$retSql["dados"].',"erro":"'.$retSql["erro"].'"}]';
        };
        ///////////////////////////////////////////////
        //               Transferencia               //
        ///////////////////////////////////////////////
        if( $lote[0]->rotina=="transferencia" ){
          foreach ( $lote as $reg ){
            $sql="UPDATE VGRUPOMODELOPRODUTO SET GMP_CODPE='".$reg->codpe."',GMP_CODPEI=".$reg->codfvr.",GMP_CODUSR=".$_SESSION["usr_codigo"]." WHERE GMP_CODIGO=".$reg->codgmp; 
            array_push($arrUpdt,$sql);            
            $atuBd = true;
          };
        };
        if( $lote[0]->rotina=="filtrar" ){        
          $sql="";
          $sql.="SELECT A.GMP_CODIGO AS COD_TRAC";
          $sql.="      ,GM.GM_NOME AS MODELO";          
          $sql.="      ,A.GMP_CODGP AS GRP";
          $sql.="      ,A.GMP_CODPE";
          $sql.="      ,A.GMP_CODCNTT AS CONTRATO";
          $sql.="      ,COALESCE(FVR.FVR_APELIDO,'...') AS RESPONSAVEL";
          $sql.="      ,A.GMP_NUMSERIE AS SERIE";                    
          $sql.="       ,CONVERT(VARCHAR(10),A.GMP_DTCONFIGURADO,127) AS CONFIGURADO";                                                  
          $sql.="      ,A.GMP_PLACACHASSI AS PLACA_CHASSI";
          $sql.="      ,A.GMP_COMPOSICAO AS QTOS";
          $sql.="      ,A.GMP_CODGML AS LOTE";
          $sql.="      ,CASE WHEN A.GMP_STATUS = 1 THEN 'ESTOQUE' WHEN A.GMP_STATUS = 2 THEN 'INSTALADO' WHEN A.GMP_STATUS = 3 THEN 'MANUTENCAO' END AS STATUS";
          $sql.="      ,US.US_APELIDO";          
          $sql.="  FROM GRUPOMODELOPRODUTO A";          
          $sql.="  LEFT OUTER JOIN GRUPOMODELO GM ON A.GMP_CODGM=GM.GM_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.GMP_CODPEI=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOMODELOLOTE GML ON A.GMP_CODGML=GML.GML_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.GMP_CODUSR=US.US_CODIGO";          
          $sql.=" WHERE (A.GMP_CODGM='".$lote[0]->codam."')";          
          $sql.="   AND (GML.GML_DATA>='".$lote[0]->data."')";
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR"
                        ,"dados":""
                        ,"erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        ///////////////////////////////////////////////////////////////////
        // Atualizando o banco de dados se opcao de insert/updade/delete //
        ///////////////////////////////////////////////////////////////////
        if( $atuBd ){
          if( count($arrUpdt) >0 ){
            $retCls=$classe->cmd($arrUpdt);
            if( $retCls['retorno']=="OK" ){
              $retorno='[{ "retorno":"OK"
                          ,"dados":""
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
<!DOCTYPE html>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <script language="javascript" type="text/javascript"></script>
  <head>
    <meta charset="utf-8">
    <title>Contrato</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/clsTab2017.js"></script>        
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaPontoEstoqueIndF10.js"></script> 
    <script src="tabelaTrac/f10/tabelaEquipamentoF10.js"></script> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://getbootstrap.com/2.3.2/assets/js/bootstrap.js"></script>           
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        document.getElementById("edtDataIni").value  = jsDatas(-180).retDDMMYYYY(); 
        $doc("spnQtos").innerHTML="0000";
        ////////////////////////////////////////////////////////
        //Recuperando os dados recebidos de Trac_GrupoModelo.php
        ////////////////////////////////////////////////////////
        pega=JSON.parse(localStorage.getItem("addInd")).lote[0];
        //localStorage.removeItem("addInd");voltarrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr
        
        jsAmi={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "TRAC"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "MODELO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "100"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "GRP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "PE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "10"
                      ,"funcCor"        : "(objCell.innerHTML=='SUC'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"                      
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "CONTRATO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "RESPONSAVEL"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"truncate"       : "S"                                            
                      ,"excel"          : "S"                      
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "SERIE"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"funcCor"        : "(objCell.innerHTML=='NSA'  ? objCell.classList.add('fontVermelho') : objCell.classList.remove('fontVermelho'))"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"                      
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "CONFIGURADO"
                      ,"fieldType"      : "dat"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "PLACA_CHASSI"
                      ,"fieldType"      : "str"
                      ,"align"          : "center"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"truncate"       : "S"                      
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "QTOS"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i2"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "12"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "LOTE"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "12"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "STATUS"
                      ,"fieldType"      : "str"
                      ,"formato"        : ["i4"]
                      ,"align"          : "center"                                                            
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "COMP"         
                      ,"obj"            : "imgPP"
                      ,"tamGrd"         : "5em"
                      ,"tipo"           : "img"
                      ,"fieldType"      : "img"
                      ,"func"           : "horComposicaoClick(this.parentNode.parentNode.cells[1].innerHTML);"
                      ,"tagI"           : "fa fa-clone"
                      ,"padrao":0}
                      
          ]
          ,
          "detalheRegistro":
          [
            { "width"           :"100%"
              ,"height"         :"300px" 
              ,"label"          :"Grupo de favorecido"
            }
          ]
          , 
          "botoesH":[
             //{"texto":"Configurar"    ,"name":"horConfigurado"  ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-wifi"        }  
            //{"texto":"Transferir"    ,"name":"horTransf"       ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-repeat"       }               
             {"texto":"Sucata"        ,"name":"horSucata"       ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-trash-o"     } 
            ,{"texto":"Remontar"      ,"name":"horRemontar"     ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-pencil-square-o" } 
            ,{"texto":"Retornar"      ,"name":"horRetornar"     ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-undo" }              
            ,{"texto":"Excel"         ,"name":"horExcel"        ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }  
            ,{"texto":"Imprimir"      ,"name":"horImprimir"     ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"        }                    
            ,{"texto":"Fechar"        ,"name":"horFechar"       ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"       }
          ] 

          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmAmi"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaAmi"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmAmi"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnAmi"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblAmi"                  // Nome da table
          ,"prefixo"        : "Ami"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "absolute"
          ,"width"          : "122em"                   // Tamanho da table
          ,"height"         : "65em"                    // Altura da table
          ,"nChecks"        : true                      // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                   // Se tiver menu esquerdo
          ,"relTitulo"      : "AUTO"                    // Titulo do relatório
          ,"relOrientacao"  : "R"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"formPassoPasso" : "*"                       // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "MODELO"                  // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
        }; 
        if( objAmi === undefined ){  
          objAmi=new clsTable2017("objAmi");
        };  
        objAmi.montarHtmlCE2017(jsAmi);
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsAmi,["CONFIGURADO","PE","RESPONSAVEL","TRAC","USUARIO"]);
        btnFiltrarClick();
      });
      //
      var objAmi;                     // Obrigatório para instanciar o JS Semestral
      var jsAmi;                      // Obj principal da classe clsTable2017
      var objPadF10;                  // Obrigatório para instanciar o JS PadraoF10            
      var objPeiF10;                  // Obrigatório para instanciar o JS FabricanteF10
      var objEquipF10;                // Obrigatório para instanciar o JS FabricanteF10                  
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var clsErro;                    // Classe para erros 
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var pega;                       // Recuperar localStorage      
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario                  
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "filtrar"                                 );
        clsJs.add("login"   , jsPub[0].usr_login                        );
        clsJs.add("data"    , jsDatas("edtDataIni").retDDMMYYYY()       );
        clsJs.add("codam"   , pega.codam                                );
        fd = new FormData();
        fd.append("automodeloind" , clsJs.fim());
        msg     = requestPedido("Trac_AutoModeloInd.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          $doc("spnQtos").innerHTML=jsNmrs((retPhp[0]["dados"]).length).emZero(4).ret();
          jsAmi.registros=objAmi.addIdUnico(retPhp[0]["dados"]);
          objAmi.ordenaJSon(jsAmi.indiceTable,false);  
          objAmi.montarBody2017();
        };  
      };
      /*
      /////////////////////////////////////////////
      // Configurar
      /////////////////////////////////////////////
      function horConfiguradoClick(){
        gerarMensagemErro("catch","Opção disponivel apenas em contrato",{cabec:"Erro"});  
      };
      */
      /////////////////////////////////////////////
      // Composicao do auto
      /////////////////////////////////////////////
      function horComposicaoClick(codamp){
				if( jsNmrs(codamp).inteiro().ret()>0 ){
					try{          
						clsJs   = jsString("lote");  
						clsJs.add("rotina"  , "hlpComposicao"     );
						clsJs.add("login"   , jsPub[0].usr_login  );
            clsJs.add("codamp"  , codamp              );
						fd = new FormData();
						fd.append("automodeloind" , clsJs.fim());
						msg     = requestPedido("Trac_AutoModeloInd.php",fd); 
						retPhp  = JSON.parse(msg);
						if( retPhp[0].retorno == "OK" ){
							janelaDialogo(
								{ height          : "36em"
									,body           : "16em"
									,left           : "300px"
									,top            : "60px"
									,tituloBarra    : "Composicao do auto "+codamp
									,code           : retPhp[0]["dados"]
									,width          : "80em"
									,fontSizeTitulo : "1.8em"
								}
							);  
						};  
					}catch(e){
						gerarMensagemErro('catch',e.message,{cabec:"Erro"});
					};
				};
      }; 
      //////////////////////////////
      //  AJUDA PARA PONTOESTOQUE //
      //////////////////////////////
      function peFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function peF10Click(obj){ 
        fPadraoF10( { opc:0
                      ,edtCod:obj.id
                      ,foco:"edtCodPei"
                      ,topo:100
                      ,tableBd:"PONTOESTOQUE"
                      ,fieldCod:"A.PE_CODIGO"
                      ,fieldDes:"A.PE_NOME"
                      ,fieldAtv:"A.PE_ATIVO"
                      ,typeCod :"str" 
                      ,divWidth:"35%"
                      ,tamColNome:"32em"
                      ,where: "AND (A.PE_CODIGO NOT IN('SUC','EST'))"
                      ,tbl:"tblPe"}
        );
      };
      function RetF10tblPe(arr){
        document.getElementById("edtCodPe").value  = arr[0].CODIGO;
        document.getElementById("edtDesPe").value  = arr[0].DESCRICAO;
        document.getElementById("edtCodPe").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function CodPeBlur(obj){
        var elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
        var elNew = obj.id;
        if( elOld != elNew ){
          var ret = fPadraoF10( { opc:1
                                  ,edtCod:obj.id
                                  ,foco:"edtCodPei"
                                  ,topo:100
                                  ,tableBd:"PONTOESTOQUE"
                                  ,fieldCod:"A.PE_CODIGO"
                                  ,fieldDes:"A.PE_NOME"
                                  ,fieldAtv:"A.PE_ATIVO"
                                  ,typeCod :"str" 
                                  ,tbl:"tblPe"}
          );
          document.getElementById(obj.id).value       = ( ret.length == 0 ? ""  : ret[0].CODIGO             );
          document.getElementById("edtDesPe").value  = ( ret.length == 0 ? ""      : ret[0].DESCRICAO  );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( ret.length == 0 ? "" : ret[0].CODIGO )  );
        };
      };
      /////////////////////////////////////////////
      //  AJUDA PARA PONTO DE ESTOQUE INDIVIDUAL //
      /////////////////////////////////////////////
      function peiFocus(obj){ 
        document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
      };
      function peiF10Click(obj){ 
        fPontoEstoqueIndF10(0,obj.id,"trConfirmar",100,{codpe: document.getElementById("edtCodPe").value,ativo:"S" } ); 
      };
      function RetF10tblPei(arr){
        document.getElementById("edtCodPei").value      = arr[0].CODIGO;
        document.getElementById("edtDesPei").value      = arr[0].DESCRICAO;
        document.getElementById("edtCodPei").setAttribute("data-oldvalue",arr[0].CODIGO);
      };
      function codPeiBlur(obj){
        var elOld = jsNmrs(document.getElementById(obj.id).getAttribute("data-oldvalue")).inteiro().ret();
        var elNew = jsNmrs(obj.id).inteiro().ret();
        if( elOld != elNew ){
          var arr = fPontoEstoqueIndF10(1,obj.id,"trConfirmar",100,
            {codfvr  : elNew
             ,codpe  : document.getElementById("edtCodPe").value
             ,ativo  : "S"} 
            ); 
          document.getElementById(obj.id).value           = ( arr.length == 0 ? "0000"            : jsNmrs(arr[0].CODIGO).emZero(4).ret() ); 
          document.getElementById("edtDesPei").value      = ( arr.length == 0 ? "*"               : arr[0].DESCRICAO                      );
          document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "0000" : arr[0].CODIGO )                       );
        };
      };
      function horTransfClick(){
        try{        
          ///////////////////////////////////////////////////////////////////
          // Checagem basica, qdo gravar checo novamente validando as colunas
          ///////////////////////////////////////////////////////////////////
          chkds=objAmi.gerarJson("n").gerar();
          clsJs       = jsString("lote");
          chkds.forEach(function(reg){
            if( reg.CONFIGURADO != "SIM" )
              throw "AUTO SEM CONFIGURAÇÃO NÃO ACEITA TRANSFERÊNCIA!";
          });
          document.getElementById("transferencia").style.display="block";
          document.getElementById("edtCodPe").value="";
          document.getElementById("edtDesPe").value="";
          document.getElementById("edtCodPei").value="0000";
          document.getElementById("edtDesPei").value="";
          document.getElementById("edtCodPe").foco();
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ///////////////////////////////////////////////////////////////////
      // Confirmando a transferência
      ///////////////////////////////////////////////////////////////////
      function trConfirmarClick(){
        try{
          clsJs       = jsString("lote");
          chkds.forEach(function(reg){
            clsJs.add("rotina"  , "transferencia"                             );              
            clsJs.add("login"   , jsPub[0].usr_login                          );
            clsJs.add("codgmp"  , reg.TRAC                                    );            
            clsJs.add("codpe"   , document.getElementById("edtCodPe").value   );
            clsJs.add("codfvr"  , document.getElementById("edtCodPei").value  );              
          }); 
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("automodeloind" , clsJs.fim());
          msg=requestPedido("Trac_AutoModeloInd.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("Ami",retPhp[0].erro,{cabec:"Aviso"});
          } else {  
            /////////////////////////////////////////////////
            // Atualizando a grade
            /////////////////////////////////////////////////
            let tbl = tblAmi.getElementsByTagName("tbody")[0];
            let nl  = tbl.rows.length;
            if( nl>0 ){
              for(let lin=0 ; (lin<nl) ; lin++){
                chkds.forEach(function(reg){
                  if( jsNmrs(reg.TRAC).inteiro().ret() == jsNmrs(tbl.rows[lin].cells[objCol.TRAC].innerHTML).inteiro().ret() ){
                    tbl.rows[lin].cells[objCol.PE].innerHTML=document.getElementById("edtCodPe").value;
                    tbl.rows[lin].cells[objCol.RESPONSAVEL].innerHTML=document.getElementById("edtDesPei").value;
                    tbl.rows[lin].cells[objCol.USUARIO].innerHTML=jsPub[0].usr_apelido;
                  };
                });  
              };    
              document.getElementById("transferencia").style.display="none";
            };  
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      //
      //
      function fncConfigura(){
        try{
          msg = new clsMensagem("Erro"); 
          msg.dataValida("Data", $doc("edtDtConfigura").value );
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            chkds=objAmi.gerarJson("n").gerar();
            clsJs = jsString("lote");           
            chkds.forEach(function(reg){            
              clsJs.add("rotina"            , "configura"                             );             
              clsJs.add("login"             , jsPub[0].usr_login                      );
              clsJs.add("gmp_acao"          , 1                                       );              
              clsJs.add("gmp_codigo"        , parseInt(reg.TRAC)                      );
              clsJs.add("gmp_dtconfigurado" , jsDatas("edtDtConfigura").retMMDDYYYY() );            
            });  
            fd.append("automodeloind" , clsJs.fim());
            msg     = requestPedido("Trac_AutoModeloInd.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno != "OK" ){
              gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});  
            } else {  
              tblAmi.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                chkds.forEach(function(reg){                
                  if( jsNmrs(row.cells[objCol.TRAC].innerHTML).inteiro().ret()  == jsNmrs(reg.TRAC).inteiro().ret() ){
                    row.cells[objCol.CONFIGURADO].innerHTML=$doc("edtDtConfigura").value;
                  };  
                });
              });
              ppvConfigura.hide();
              tblAmi.retiraChecked()
            }; 
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ////////////////////////
      // Transformar em sucata
      ////////////////////////
      function horSucataClick(){
        try{
          // Preparando para enviar ao Php
          chkds=objAmi.gerarJson("n").gerar();          
          msg         = "ok";
          clsJs       = jsString("lote");
          chkds.forEach(function(reg){
            if( reg.PE != "EST" )
              throw "AUTO DEVE ESTA EM ESTOQUE PARA CONFIGURAÇÃO!";  
            clsJs.add("rotina"      , "sucata"            );              
            clsJs.add("login"       , jsPub[0].usr_login  );
            clsJs.add("gmp_acao"    , 2                   );                          
            clsJs.add("gmp_codigo"  , reg.TRAC            );            
            clsJs.add("gmp_codpe"   , "SUC"               );              
          });
          //////////////////////
          // Enviando para o Php
          //////////////////////    
          var fd = new FormData();
          fd.append("automodeloind" , clsJs.fim());
          msg=requestPedido("Trac_AutoModeloInd.php",fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("gmi",retPhp[0].erro,{cabec:"Aviso"});
          } else {  
            tblAmi.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
              chkds.forEach(function(reg){                
                if( jsNmrs(row.cells[objCol.TRAC].innerHTML).inteiro().ret()  == jsNmrs(reg.TRAC).inteiro().ret() ){
                  row.cells[objCol.CONFIGURADO].innerHTML = "";
                  row.cells[objCol.PE].innerHTML          = "SUC";
                  row.cells[objCol.PE].classList.add("corFonteAlterado");  
                };  
              });
            });
            tblAmi.retiraChecked()
          
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      function horRemontarClick(){     
        try{
          $('table tr').each(function(i) {
            // Only check rows that contain a checkbox
            var $chkbox = $(this).find('input[type="checkbox"]');
            if ($chkbox.length) {
              var status = $chkbox.prop('checked');
              console.log('Table row ' + i + ' contains a checkbox with a checked status of: ' + status);
            }
          });
                 $('#confirmRemonte').modal({
              backdrop: 'static',
              keyboard: false
          })
          .on('click', '#remontarAuto', function(e) {
              // Preparando para enviar ao Php
              chkds=objAmi.gerarJson("n").gerar();          
              msg         = "ok";
              clsJs       = jsString("lote");
              chkds.forEach(function(reg){
                if( reg.PE != "SUC" )
                  throw "AUTO NÃO PODE ESTAR EM SUCATA AO REMONTAR";  
                clsJs.add("rotina"      , "remontar"            );              
                clsJs.add("login"       , jsPub[0].usr_login  );                         
                clsJs.add("gmp_codaut"  , reg.TRAC            );
                clsJs.add("gmp_codigoOld"  , $doc("edtEqpOld").getAttribute("data-id")            ); 
                clsJs.add("gmp_codigoNew"  , $doc("edtEqpNew").getAttribute("data-id")            ); 
                clsJs.add("gmp_numserie"  , $doc("edtEqpNew").value            ); 
                clsJs.add("gmp_tipoeqp"  , $doc("edtTipoNew").value            );                        
              });
              //////////////////////
              // Enviando para o Php
              //////////////////////    
              var fd = new FormData();
              fd.append("automodeloind" , clsJs.fim());
              msg=requestPedido("Trac_AutoModeloInd.php",fd); 
              retPhp=JSON.parse(msg);
              if( retPhp[0].retorno != "OK" ){  
                gerarMensagemErro("gmi",retPhp[0].erro,{cabec:"Aviso"});
              } else {  

                tblAmi.retiraChecked();
                $('#confirmRemonte').modal('hide');
              };
          });  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      }; 

      function horRetornarClick(){
        try{

           $('#confirmRetorno').modal({
              backdrop: 'static',
              keyboard: false
          })
          .on('click', '#retornarAuto', function(e) {
              // Preparando para enviar ao Php
              chkds=objAmi.gerarJson("n").gerar();          
              msg         = "ok";
              clsJs       = jsString("lote");
              chkds.forEach(function(reg){
                if( reg.PE != "EST" ){
                  alert("AUTO DEVE ESTA EM ESTOQUE PARA CONFIGURAÇÃO!");
                  throw "AUTO DEVE ESTA EM ESTOQUE PARA CONFIGURAÇÃO!";
                  }  
                clsJs.add("rotina"      , "retornar"            );              
                clsJs.add("login"       , jsPub[0].usr_login  );                        
                clsJs.add("gmp_codaut"  , reg.TRAC            );                        
              });
              //////////////////////
              // Enviando para o Php
              //////////////////////    
              var fd = new FormData();
              fd.append("automodeloind" , clsJs.fim());
              msg=requestPedido("Trac_AutoModeloInd.php",fd); 
              retPhp=JSON.parse(msg);
              if( retPhp[0].retorno != "OK" ){
                gerarMensagemErro("gmi",retPhp[0].erro,{cabec:"Aviso"});
              } else {  
                tblAmi.retiraChecked();
                $('#confirmRetorno').modal('hide');       
              }; 
           } ); 
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      } 
      function equipCadClick(flag){ 
        chkds=objAmi.gerarJson("n").gerar();         
        if(flag == 'old'){
          fEquipamentoF10(0,"nsa","null",100
          ,{ codaut:parseInt(chkds[0].TRAC)
            ,codgp:"A.GMP_CODGP <> 'AUT'"
            ,divWidth:"76em"
            ,tblWidth:"74em"
          });
        }
        else{
          fEquipamentoF10(0,"nsa","null",100
          ,{codpe:"EST"
            ,codgp:"A.GMP_CODGP <> 'AUT'"
            ,codaut:0
            ,tbl:'tblEquip2'
            ,tipo:$doc("edtTipoOld").value 
            ,divWidth:"76em"
            ,tblWidth:"74em"
          });
        }
      };
            
      function RetF10tblEquip(arr){
        $doc("edtEqpOld").value      = arr[0].NOME;
        $doc("edtNomeOld").value      = arr[0].SERIE;        
        $doc("edtTipoOld").value      = arr[0].TIPO;                
        $doc("edtEqpOld").setAttribute("data-id",arr[0].CODIGO);

      };
      function RetF10tblEquip2(arr){
        $doc("edtEqpNew").value      = arr[0].NOME;
        $doc("edtNomeNew").value      = arr[0].SERIE; 
        $doc("edtTipoNew").value      = arr[0].TIPO;                           
        $doc("edtEqpNew").setAttribute("data-id",arr[0].CODIGO);

      };
      //////////////////////
      // Fechar formulario
      //////////////////////
      function horFecharClick(){
        window.close();
      };
      function cleanModal(){
         document.getElementById("edtEqpOld").value = '';
         document.getElementById("edtNomeOld").value = '';
         document.getElementById("edtTipoOld").value = '';

         document.getElementById("edtEqpNew").value = '';
         document.getElementById("edtNomeNew").value = '';
         document.getElementById("edtTipoNew").value = '';
      }
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-wifi" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span id="spnTitulo" class="infoBoxText">Auto</span>
              <span id="spnQtos" class="infoBoxNumber"></span>
            </div>
          </div>
        </div>        
        
        <div class="campotexto campo10" style="margin-top:2px;">
          <input class="campo_input" id="edtDataIni" 
                                     placeholder="##/##/####"                 
                                     OnKeyUp="mascaraNumero('##/##/####',this,event,'dig')"
                                     maxlength="10" type="text" />
          <label class="campo_label" for="edtDataIni">A PARTIR</label>
        </div>
        <div onClick="btnFiltrarClick();" class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;float:left;"><i class="fa fa-folder-open"> Abrir</i></div>                
        <div class="campotexto campo05"></div>
        
        <section id="collapseConfigura" class="section-combo" data-tamanho="220">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Data configuração</a>
              <button type="button" id="popoverConfigura" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group'>
                                        <label for='edtDtConfigura' class='control-label'>Configurado em</label>
                                        <input type='text' class='form-control' disabled id='edtDtConfigura' placeholder='##/##/####' onkeyup=mascaraNumero('##/##/####',this,event,'dig') />
                                      </div>                                        
                                      <div class='form-group'>
                                        <button onClick='fncConfigura();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>        
              </button>
            </span>
          </div>
        </section>  
        <!--
        <section id="collapseCorreio" class="section-combo" data-tamanho="210" style="margin-left:2px;">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Procurar um auto</a>
              <button type="button" id="popoverUmAuto" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group'>
                                        <label for='edtUmAuto' class='control-label'>Serie</label>
                                        <input type='text' class='form-control' id='edtUmAuto' placeholder='informe' />
                                      </div>
                                      <div class='form-group'>
                                        <button onClick='fncUmAuto();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>
              </button>              
            </span>
          </div>
        </section>  
        -->
        
        
        
      </div>
      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <section>
        <section id="sctnAmi" style="margin-left:100px;">
        </section>  
      </section>
      
      <form method="post" class="center" id="frmCntI" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="inactive">
          <input id="ageApeCol" value="*" type="text" />        <!-- Apelido do colaborador -->
          <input id="edtModoEntrega" value="*" type="text" />   <!-- Codigo do modo de entrega -->        
          <input id="edtStatusEntrega" value="*" type="text" /> <!-- Codigo do status da entrega -->                
        </div>
      </form>
    </div>
    
    <div id="agendacad" class="frmTable" style="display:none; width:90em; margin-left:11em;margin-top:5.5em;position:absolute;">
      <div class="frmTituloManutencao">Agendamento<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>
      <div style="height: 200px; overflow-y: auto;">
        <div class="campotexto campo100">
          <!-- Endereco de entrega -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodEnt"
                                                onBlur="codEntBlur(this);" 
                                                onFocus="entFocus(this);" 
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
                                                onBlur="codInsBlur(this);" 
                                                onFocus="insFocus(this);" 
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
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtData" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             value="00/00/0000"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtData">DATA VISITA:</label>
          </div>
          <!--
                 -->
          <!-- Colaborador -->
          <div class="campotexto campo15">
            <input class="campo_input inputF10" id="ageCodCol"
                                                onClick="colF10Click(this);"
                                                onBlur="codColBlur(this);"                                                 
                                                data-oldvalue="0000"
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="ageCodCol">COLABORADOR:</label>
          </div>
          <div class="campotexto campo70">
            <input class="campo_input_titulo input" id="ageDesCol" type="text" disabled /><label class="campo_label campo_required" for="ageDesCol">NOME COLABORADOR</label>
          </div>
          <!--
          -->
          <div onClick="ageConfirmarClick();" id="ageConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
          <div onClick="document.getElementById('agendacad').style.display='none';" id="ageCancelar" class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
          <div class="campotexto campo70">
            <label class="labelMensagem" for="edtUsuario">Para agendamento ser completo todos os campos devem ser informados</label>
          </div>
        </div>  
      </div>
    </div>
    <div class="modal fade" id="confirmRetorno" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title" id="exampleModalLongTitle" style="text-align: center;">Retornar Auto</h1>
          </div>
          <div class="modal-body">
            <h2 style="text-align: center;">
              Auto desmembrado não possui retorno, continuar ação?
            </h2>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Voltar</button>
            <button type="button" class="btn btn-success" id="retornarAuto">Continuar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="confirmRemonte" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title" id="exampleModalLongTitle" style="text-align: center;">Remontar Auto</h1>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="edtEqpOld">Equipamento</label> 
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="search" readonly class="form-control" id="edtEqpOld" data-id =""  style="cursor: pointer;" required onClick="equipCadClick('old');">
                  </div>
                </div>
              <div class="form-group col-md-4">
                <label for="edtNomeOld">Nome do Equipamento</label>
                <input type="text" class="form-control" id="edtNomeOld" required disabled="true">
              </div>
              <div class="form-group col-md-4">
                <label for="edtTipoOld">Tipo de Equipamento</label>
                <input type="text" class="form-control" id="edtTipoOld" required disabled="true">
              </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="edtEqpNew">Novo Equipamento</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="search" readonly class="form-control" id="edtEqpNew" data-id ="" style="cursor: pointer;" required
                    onClick="equipCadClick('new');">
                  </div>
                </div>
                <div class="form-group col-md-4">
                  <label for="edtNomeNew">Nome do Equipamento</label>
                  <input type="text" class="form-control" id="edtNomeNew" required disabled="true">
                </div>
                <div class="form-group col-md-4">
                  <label for="edtTipoNew">Tipo de Equipamento</label>
                  <input type="text" class="form-control" id="edtTipoNew" required disabled="true">
                </div>
              </div>
              <div class="form-group col-md-4 col-md-offset-8">
                <button type="submit" class="btn btn-success" id="remontarAuto">Cadastrar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cleanModal();">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script>
      //
      //
      ///////////////////
      // Data de ativacao
      ///////////////////
      ppvConfigura = new Popover('#popoverConfigura',{ trigger: 'click'} );      
      evtConfigura = document.getElementById('popoverConfigura');
      evtConfigura.status="ok";
      
      evtConfigura.addEventListener('show.bs.popover', function(event){
        try{
          chkds=objAmi.gerarJson("n").gerar();
          chkds.forEach(function(reg){
            if( reg.CONFIGURADO != "" )
              throw "AUTO JA CONFIGURADO!";  
            if( reg.PE != "EST" )
              throw "AUTO DEVE ESTA EM ESTOQUE PARA CONFIGURAÇÃO!";  
              
          });
          evtConfigura.status="ok";            
        }catch(e){
          evtConfigura.status="err";
          gerarMensagemErro("catch",e,"Erro");
        };
      },false);  
      evtConfigura.addEventListener('shown.bs.popover', function(event){
        if( evtConfigura.status=="err" ){
          ppvConfigura.hide();
        } else {    
          $doc("edtDtConfigura").value=jsDatas(0).retDDMMYYYY();
        };
      },false);
    </script>
  </body>
</html>