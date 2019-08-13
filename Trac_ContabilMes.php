<?php
  session_start();
  //require("classPhp/alterarEmpresa.php");        
  if( isset($_POST["contabilmes"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");    
      require("classPhp/selectRepetido.class.php");       						            
      $vldr       = new validaJSon();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["contabilmes"]);
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
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="filtrar" ){        
          $sql ="SELECT A.RAT_LANCTO AS lancto";
          $sql.="       ,PGR.PGR_DOCTO AS docto";
          $sql.="       ,A.RAT_CODCC AS contabil";
          $sql.="       ,CC.CC_NOME AS nome";
          $sql.="       ,A.RAT_DEBITO AS debito";
          $sql.="       ,A.RAT_CREDITO AS credito";
          $sql.="       ,PGR.PGR_CODCC AS contrapartida";
          $sql.="       ,CCP.CC_NOME AS nome_cp";
          $sql.="       ,PGR.PGR_VLRLIQUIDO AS vlrliquido";
          $sql.="       ,PGR.PGR_CODPTP AS codptp";          
          $sql.="       ,PGR.PGR_OBSERVACAO AS obs";          
          $sql.="  FROM RATEIO A";
          $sql.="  LEFT OUTER JOIN CONTACONTABIL CC ON A.RAT_CODCC=CC.CC_CODIGO";
          $sql.="  LEFT OUTER JOIN PAGAR PGR ON A.RAT_LANCTO=PGR.PGR_LANCTO";
          $sql.="  LEFT OUTER JOIN CONTACONTABIL CCP ON PGR.PGR_CODCC=CCP.CC_CODIGO";
          $sql.="  WHERE (A.RAT_CODCMP=".$lote[0]->codcmp.")";
          $sql.="    AND (PGR.PGR_CODEMP=".$lote[0]->codemp.")";
          $sql.="  ORDER BY A.RAT_LANCTO";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $tblCm  = $retCls["dados"];
            $tblGrp = [];   // Array para agrupar DESPESA/RECEITA com CONTRAPARTIDA
            $codPtp = "*";  // Ver se titulo eh CR ou CP para contrapartida
            $totReg = count($tblCm);
            /////////////////////////////
            // Para gerar a contrapartida
            /////////////////////////////
            $lancto         = $tblCm[0]["lancto"];  //Primeiro lancto( variavel para inserir contrapartida )
            for( $lin=0;$lin<$totReg;$lin++ ){
              if( $tblCm[$lin]["lancto"] != $lancto ){
                array_push($tblGrp,[										
                   "lancto"	  =>  $tblCm[($lin-1)]["lancto"]
                  ,"flag"	    =>  "AP"            // Aqui ou eh DP(Despesa/Receita) ou AP(Ativo/Passivo)
                  ,"docto"	  =>  $tblCm[($lin-1)]["docto"]
                  ,"contabil" =>  $tblCm[($lin-1)]["contrapartida"]
                  ,"nome"	    =>  $tblCm[($lin-1)]["nome_cp"]
                  ,"debito"	  =>  ( $codPtp == "CR" ? $tblCm[($lin-1)]["vlrliquido"] : 0 )
                  ,"credito"  =>  ( $codPtp == "CR" ? 0 : $tblCm[($lin-1)]["vlrliquido"] )
                  ,"obs"      =>  "CONTRAPARTIDA"
                ]);
              };
              array_push($tblGrp,[										
                 "lancto"	  =>  $tblCm[$lin]["lancto"]
                ,"flag"	    =>  "DR"                      // Aqui ou eh DP(Despesa/Receita) ou AP(Ativo/Passivo)
                ,"docto"	  =>  $tblCm[$lin]["docto"]
                ,"contabil" =>  $tblCm[$lin]["contabil"]
                ,"nome"	    =>  $tblCm[$lin]["nome"]
                ,"debito"	  =>  $tblCm[$lin]["debito"]
                ,"credito"  =>  $tblCm[$lin]["credito"]
                ,"obs"      =>  $tblCm[$lin]["obs"]
              ]);
              $codPtp         = $tblCm[$lin]["codptp"];
              $lancto         = $tblCm[$lin]["lancto"];
            };
            /////////////////////////////////
            // Pegando a ultima contrapartida
            /////////////////////////////////
            array_push($tblGrp,[										
               "lancto"	  =>  $tblCm[($totReg-1)]["lancto"]
              ,"flag"	    =>  "AP"            // Aqui ou eh DP(Despesa/Receita) ou AP(Ativo/Passivo)
              ,"docto"	  =>  $tblCm[($totReg-1)]["docto"]
              ,"contabil" =>  $tblCm[($totReg-1)]["contrapartida"]
              ,"nome"	    =>  $tblCm[($totReg-1)]["nome_cp"]
              ,"debito"	  =>  ( $codPtp == "CR" ? $tblCm[($totReg-1)]["vlrliquido"] : 0 )
              ,"credito"  =>  ( $codPtp == "CR" ? 0 : $tblCm[($totReg-1)]["vlrliquido"] )
              ,"obs"      =>  "CONTRAPARTIDA"
            ]);
            $totReg = count($tblGrp);
            $tblJs  = [];
            $tblRes = [];   // Montando um resumo sumarizado
            for( $lin=0;$lin<$totReg;$lin++ ){
              array_push($tblJs,[
                $tblGrp[$lin]["lancto"]
                ,$tblGrp[$lin]["flag"]
                ,$tblGrp[$lin]["docto"]
                ,$tblGrp[$lin]["contabil"]
                ,$tblGrp[$lin]["nome"]
                ,$tblGrp[$lin]["debito"]
                ,$tblGrp[$lin]["credito"]
                ,$tblGrp[$lin]["obs"]
              ]);
              if( $tblGrp[$lin]["flag"]=="DR" ){
                $achei=false;
                for( $res=0;$res<count($tblRes);$res++ ){
                  if( $tblRes[$res][0]==$tblGrp[$lin]["contabil"] ){
                    $tblRes[$res][2]+=$tblGrp[$lin]["debito"];
                    $tblRes[$res][3]+=$tblGrp[$lin]["credito"];                  
                    $achei=true;
                    break;
                  };  
                };  
                if( $achei==false ){
                  array_push($tblRes,[
                    $tblGrp[$lin]["contabil"]
                    ,$tblGrp[$lin]["nome"]
                    ,$tblGrp[$lin]["debito"]
                    ,$tblGrp[$lin]["credito"]
                  ]);
                };  
              };
            };  
            $retorno='[{ "retorno":"OK"
                        ,"dados"  :'.json_encode($tblJs).'
                        ,"resumo" :'.json_encode($tblRes).'
                        ,"totreg"  :"'.$totReg.'"
                        ,"erro"   :""}]'; 
            
            
            
            

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
    <title>Contabil</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
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
        jsCm={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "LANCTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      //,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      //,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "TP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      //,"tamImp"         : "15"
                      //,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "DOCTO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "CONTABIL"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "DESCRICAO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "25em"
                      ,"tamImp"         : "70"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "DEBITO"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"somarImp"       : "S"                                            
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "CREDITO"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"somarImp"       : "S"                                            
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "OBSERVACAO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "28em"
                      ,"tamImp"         : "70"
                      ,"excel"          : "S"
                      ,"truncate"       : true
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"        :"CD"         
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
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(ceTr.cells[2].innerHTML =='DR') {ceTr.style.color='black';}"      
          ,"opcRegSeek"     : true                     // Opção para numero registros/botão/procurar    
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)              										          
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmCm"                   // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaCm"                // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmCm"                   // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnCm"                  // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblCm"                   // Nome da table
          ,"prefixo"        : "fc"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "relative"
          ,"width"          : "130em"                   // Tamanho da table
          ,"height"         : "55em"                    // Altura da table
          ,"nChecks"        : false                     // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "CONTABIL MES"            // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "*"                       // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objCm === undefined ){  
          objCm=new clsTable2017("objCm");
        };  
        /////////////////////////////////////////////////
        //        Objeto clsTable2017 RESUMO           //
        /////////////////////////////////////////////////
        jsRes={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"tamGrd"         : "0em"             
                      ,"padrao":1}            
            ,{"id":1  ,"field"          : "CONTABIL"
                      ,"labelCol"       : "CONTABIL"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"											
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "DESCRICAO"
                      ,"labelCol"       : "DESCRICAO"
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "80"											
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "DEBITO" 
                      ,"labelCol"       : "DEBITO"
                      ,"fieldType"      : "flo2"                       
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"somarImp"       : "S"
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "CREDITO" 
                      ,"labelCol"       : "CREDITO"
                      ,"fieldType"      : "flo2"                       
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
                      ,"somarImp"       : "S"                      
                      ,"padrao":0}
          ]
          , 
          "botoesH":[
             {"texto":"Excel"       ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Imprimir"    ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"        }                    
          ] 
          ,"registros"      : []                   // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "S"                  // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmRes"             // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaRes"          // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmRes"             // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "cllRes"             // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "cllRes"             // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)          
          ,"tbl"            : "tblPgr"             // Nome da table
          ,"prefixo"        : "ch"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"position"       : "relative"          
          ,"width"          : "78em"               // Tamanho da table
          ,"height"         : "40em"               // Altura da table
          ,"tableLeft"      : "sim"                // Se tiver menu esquerdo
          ,"relTitulo"      : "RESUMO CONTABIL"    // Titulo do relatório
          ,"relOrientacao"  : "R"                  // Paisagem ou retrato
          ,"relFonte"       : "8"                  // Fonte do relatório
          ,"indiceTable"    : "LANCTO"             // Indice inicial da table
          ,"tamBotao"       : "12"                 // Tamanho botoes defalt 12 [12/25/50/75/100]
        }; 
        if( objRes === undefined ){  
          objRes=new clsTable2017("objRes");
        }; 
        /*
        ///////////////////////////////////////////
        // Vou precisar destar colunas no relatorio
        ///////////////////////////////////////////
        arrCol=[];      
        arrCol.push( (jsDatas(1).retDDMMYYYY()).substring(0,5) );
        arrCol.push( (jsDatas(2).retDDMMYYYY()).substring(0,5) );
        arrCol.push( (jsDatas(3).retDDMMYYYY()).substring(0,5) );
        arrCol.push( (jsDatas(4).retDDMMYYYY()).substring(0,5) );
        arrCol.push( (jsDatas(5).retDDMMYYYY()).substring(0,5) );
        arrCol.push( (jsDatas(6).retDDMMYYYY()).substring(0,5) );
        arrCol.push( (jsDatas(7).retDDMMYYYY()).substring(0,5) );
        arrCol.push( (jsDatas(8).retDDMMYYYY()).substring(0,5) );
        arrCol.push( "OUTROS" );

        jsCm.titulo[8].labelCol   = arrCol[0];
        jsCm.titulo[9].labelCol   = arrCol[1];
        jsCm.titulo[10].labelCol  = arrCol[2];
        jsCm.titulo[11].labelCol  = arrCol[3];
        jsCm.titulo[12].labelCol  = arrCol[4];
        jsCm.titulo[13].labelCol  = arrCol[5];
        jsCm.titulo[14].labelCol  = arrCol[6];
        jsCm.titulo[15].labelCol  = arrCol[7];
        ///////////////////////////////////////////
        // Acrescentando +10 dias ao fluxo de caixa
        ///////////////////////////////////////////
        for( let ini=9;ini<19;ini++ ){
          let ceOpt	  = document.createElement("option");        
          ceOpt.value = jsDatas(ini).retDDMMYYYY();
          ceOpt.text  = jsDatas(ini).retDDMMYYYY();
          document.getElementById("cbDtFim").appendChild(ceOpt);
        };
        */
        objCm.montarHtmlCE2017(jsCm); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsCm,["CREDITO","CONTABIL","DEBITO","DESCRICAO","DOCTO","LANCTO","TP"]);
      });
      var objCm;                      // Obrigatório para instanciar o JS TFormaCob
      var jsCm;                       // Obj principal da classe clsTable2017
      var objRes;                     // Obrigatório para instanciar o JS TFormaCob
      var jsRes;                      // Obj principal da classe clsTable2017
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario      
      var clsChecados;                // Classe para montar Json
      var chkds;                      // Guarda todos registros checados na table 
      var contMsg   = 0;              // contador para mensagens      
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d05);
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado
      //
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"               );
        clsJs.add("login"       , jsPub[0].usr_login      );
        clsJs.add("entsai"      , $doc("cbOpcao").value   );
        clsJs.add("codcmp"      , $doc("edtCodCmp").value );
        clsJs.add("codemp"      , jsPub[0].emp_codigo     );
        fd = new FormData();
        fd.append("contabilmes" , clsJs.fim());
        msg     = requestPedido(arqLocal,fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsCm.registros=objCm.addIdUnico(retPhp[0]["dados"]);
          objCm.ordenaJSon(jsCm.indiceTable,false);  
          objCm.montarBody2017();
          ///////////////////////////
          // Montando o resumo mensal
          ///////////////////////////
          jsRes.relTitulo="RESUMO CONTABIL "+$doc("edtDesCmp").value;
          objRes.montarHtmlCE2017(jsRes); 
          jsRes.registros=objRes.addIdUnico(retPhp[0]["resumo"]);
          objRes.ordenaJSon(jsRes.indiceTable,false);  
          objRes.montarBody2017();
        };  
      };
      function validaCmp(valor){
        let retorno=validaCompetencia(valor);
        if( retorno.erro=="ok" ){
          $doc("edtDesCmp").value=retorno.descmp;
          $doc("edtCodCmp").value=retorno.codcmp;
        } else {
          gerarMensagemErro("cmp",retorno.erro,"Erro");  
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
          fd.append("contabilmes" , clsJs.fim());
          
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
      /*
      function fncImprimir(){
        //////////////////////////////////////////////
        // INICIA A IMPRESSAO DA COPIA DE DOCUMENTO //
        //////////////////////////////////////////////
        let rel = new relatorio();
        rel.orientacao("P");
        rel.tamFonte(9);
        rel.iniciar();
        rel.traco();
        rel.pulaLinha(1);
        rel.corFundo("cinzaclaro",9,260);    
        rel.cell(28,"Fluxo de caixa até "+$doc("cbDtFim").value,{borda:0,negrito:true});
        rel.pulaLinha(10);
        rel.traco();
        rel.pulaLinha(1);
        rel.tamFonte(7);
        rel.cell(10,"BCN"   ,{borda:0,negrito:true,align:"L"});
        rel.cell(30,"FAVORECIDO");
        rel.cell(20,"ATR",{align:"R"});
        rel.cell(20,"HOJE");
        rel.cell(20,arrCol[0]);
        rel.cell(20,arrCol[1]);        
        rel.cell(20,arrCol[2]);        
        rel.cell(20,arrCol[3]);
        rel.cell(20,arrCol[4]);        
        rel.cell(20,arrCol[5]);
        rel.cell(20,arrCol[6]);
        rel.cell(20,arrCol[7]);        
        rel.cell(20,arrCol[8]); 

        clsChecados = objCm.gerarJson("n");
        clsChecados.temColChk(false);
        msg = clsChecados.gerar();
        let tamC=msg.length;
        let zebra=false;
        rel.align("L");
        for( let lin=0;lin<tamC;lin++ ){
          if( msg[lin]["FLAG"]==2 ){
            rel.pulaLinha(6);
            rel.setX(10);
            rel.corFundo("cinzaclaro",5,260);  //vermelhoclaro  
            rel.pulaLinha(-6);   
          } else if( msg[lin]["FLAG"]==4 ){  
            rel.pulaLinha(6);
            rel.setX(10);
            rel.corFundo("azulclaro",5,260);
            rel.pulaLinha(-6);   
          } else if( (msg[lin]["FLAG"]==1) || (msg[lin]["FLAG"]==5) ){  
            rel.pulaLinha(6);
            rel.setX(10);
            rel.corFundo("vermelhoclaro",5,260);
            rel.pulaLinha(-6);   
          };

          rel.cell(10,msg[lin]["BNC"],{pulaLinha:4,moeda:false,align:"L",negrito:false});
          rel.cell(30,msg[lin]["FAVORECIDO"]);
          rel.cell(20,msg[lin]["ATR"],{moeda:false,align:"R"});          
          rel.cell(20,msg[lin]["HOJE"]);
          rel.cell(20,msg[lin][arrCol[0]]);
          rel.cell(20,msg[lin][arrCol[1]]);
          rel.cell(20,msg[lin][arrCol[2]]);
          rel.cell(20,msg[lin][arrCol[3]]);
          rel.cell(20,msg[lin][arrCol[4]]);
          rel.cell(20,msg[lin][arrCol[5]]);
          rel.cell(20,msg[lin][arrCol[6]]);
          rel.cell(20,msg[lin][arrCol[7]]);
          rel.cell(20,msg[lin][arrCol[8]]);
        }
        envPhp=rel.fim();
        ///////////////////////////////////////////////////
        // PREPARANDO UM FORMULARIO PARA VER O RELATORIO //
        ///////////////////////////////////////////////////
        document.getElementById('sql').value=envPhp;
        document.getElementsByTagName('form')[0].submit();           
      };
        
			function fncExcel(){
        let lin;
        let xlsTable;  
        xlsTable  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        xlsTable += '<style>';
        xlsTable += '  table {border: 2px solid black;font-family:Calibri;font-size:9pt;color:black;}';
        xlsTable += '  th    {font-weight:900}';
        xlsTable += '  td    {font-weight:300;}';
        xlsTable += '  .text {ms-number-format:"\@";}';
        xlsTable += '</style>';
        xlsTable +=   '<body>';
        xlsTable +=     '<table>';
        xlsTable +=       '<thead>';
        xlsTable +=         '<tr><th colspan="13" style="background-color:#F5DEB3">FLUXO DE CAIXA ATÉ '+$doc("edtFluxo").value+'</th></tr>';
        xlsTable +=         '<tr>';
        xlsTable +=           '<th style="width:5em;background-color:#F5DEB3;border:1px solid black;">BNC</th>';						  //01
        xlsTable +=           '<th style="width:8em;background-color:#F5DEB3;border:1px solid black;">FAVORECIDO</th>';       //02
        xlsTable +=           '<th style="width:5em;background-color:#F5DEB3;border:1px solid black;">ATR</th>';              //03
        xlsTable +=           '<th style="width:4em;background-color:#F5DEB3;border:1px solid black;">HOJE</th>';             //04
        xlsTable +=           '<th style="width:6em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[0]+'</th>';    //05
        xlsTable +=           '<th style="width:6em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[1]+'</th>';    //06
        xlsTable +=           '<th style="width:8em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[2]+'</th>';    //07  
        xlsTable +=           '<th style="width:8em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[3]+'</th>';    //08
        xlsTable +=           '<th style="width:6em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[4]+'</th>';    //09 
        xlsTable +=           '<th style="width:4em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[5]+'</th>';    //10
        xlsTable +=           '<th style="width:8em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[6]+'</th>';    //11  
        xlsTable +=           '<th style="width:8em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[7]+'</th>';    //12
        xlsTable +=           '<th style="width:6em;background-color:#F5DEB3;border:1px solid black;">'+arrCol[8]+'</th>';    //13 
        xlsTable +=         '</tr>';        
        xlsTable +=       '</thead>';
        xlsTable +=       '<tbody>';
        ////////////////////////
        // Buscando os registros
        ////////////////////////
        clsChecados = objCm.gerarJson("n");
        clsChecados.temColChk(false);
        msg = clsChecados.gerar();
        let tamC=msg.length;
        let bgColor="";
        for( let lin=0;lin<tamC;lin++ ){
          bgColor="white";          
          if( msg[lin]["FLAG"]==2 ){
            bgColor="#BDB76B";
          } else if( msg[lin]["FLAG"]==4 ){  
            bgColor="#DEB887";            
          } else if( (msg[lin]["FLAG"]==1) || (msg[lin]["FLAG"]==5) ){  
            bgColor="#836FFF";          
          };
					xlsTable +=         '<tr>';
					xlsTable +=           '<td class="text" style="background-color:'+bgColor+'">'+msg[lin]["BNC"]+'</td>';														                               //01
					xlsTable +=           '<td class="text" style="background-color:'+bgColor+'">'+msg[lin]["FAVORECIDO"]+'</td>';                                                   //02
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin]["ATR"]).replaceAll(".","")+'</td>';      //03
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin]["HOJE"]).replaceAll(".","")+'</td>';     //04
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[0]]).replaceAll(".","")+'</td>';  //05
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[1]]).replaceAll(".","")+'</td>';  //06
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[2]]).replaceAll(".","")+'</td>';  //07
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[3]]).replaceAll(".","")+'</td>';  //08
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[4]]).replaceAll(".","")+'</td>';  //09
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[5]]).replaceAll(".","")+'</td>';  //10
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[6]]).replaceAll(".","")+'</td>';  //11
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[7]]).replaceAll(".","")+'</td>';  //12
					xlsTable +=           '<td class="text" style="mso-number-format:\'#,##0.00\';background-color:'+bgColor+'">'+(msg[lin][arrCol[8]]).replaceAll(".","")+'</td>';  //13
        };  
        xlsTable +=       '</tbody>';
        xlsTable +=       '<tr><th colspan="13" style="background-color:#F5DEB3;border-top:1px solid black;">FIM DE ARQUIVO</th></tr>';
        xlsTable +=     '</table>';
        xlsTable +=   '</body>';
        xlsTable += '</html>';
        ////////////////////////////////
        // Download do arquivo gerado //
        ////////////////////////////////
        var arquivo = "fluxoAte"+($doc("edtFluxo").value).replaceAll("/","")+".xls";
        //////////////////////////////////  
        //Se o browser aceita BLOB      //
        //IE, Chrome e FireFox aceitam  //
        //////////////////////////////////
        if (window.Blob) {
          var textFileAsBlob = new Blob([xlsTable], {
              type: 'text/plain'
          });
          
          var fileNameToSaveAs = "output.xls";
          var downloadLink = document.createElement("a");
          downloadLink.download = arquivo;
          downloadLink.innerHTML = "Download File";
          if (window.webkitURL != null) {
              // o chrome permite que o link seja clicado sem inserir ele no DOM (fisicamente na pagina)
              downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
          } else {
              // O Firefox não permite clicar no link se nao existir na pagina, por isso precisa da funca para
              // pagar o mesmo após ser clicado
              downloadLink.href           = window.URL.createObjectURL(textFileAsBlob);
              downloadLink.onclick        = destroyClickedElement;
              downloadLink.style.display  = "none";
              document.body.appendChild(downloadLink);
          }
          ////////////////////////////////////
          // IE salva o arquivo deste jeito //
          ////////////////////////////////////
          if (navigator.msSaveBlob) {
              navigator.msSaveBlob(textFileAsBlob, arquivo);
          ////////////////////////////////////////////////////////////////////
          // Firefox e Chrome permitem clicar no link para salvar o arquivo //
          ////////////////////////////////////////////////////////////////////
          } else {
              downloadLink.click();
          }
        } else {
          SaveContents();
        }
			};		
      */
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="objCm.imprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela."><i class="indFa fa-print"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="objCm.excel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela."><i class="indFa fa-file-excel-o"></i>
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
        <div class="colMd12" style="float:left;margin-bottom:0px;height:50px;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">Contabil</span>
              <span id="spnEmpApelido" class="infoBoxLabel"></span>
            </div>
          </div>
        </div>  

        <div id="indDivInforme" class="indTopoInicio80" style="padding-top:2px;">
          <div class="campotexto campo12">
            <select class="campo_input_combo" id="cbOpcao">
              <option value="G" selected>GERENCIAL</option>            
              <option value="R">RESUMO</option>
            </select>
            <label class="campo_label campo_required" for="cbOpcao">OPÇÃO</label>
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
          <div id="btnFechar" onClick="window.close();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-close"> Fechar</i></div>
        </div>
        
      </div>
      <section>
        <section id="sctnCm">
        </section>  
      </section>
      <form method="post"
            name="frmCm"
            id="frmCm"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/>      
      </form>  
    </div>
    <!--
    Buscando o resumo contabil
    -->
    <section id="collapseSectionRes" class="section-collapse">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a id="aLabel" class="btn btn-default disabled">Buscar</a>
            <button id="abreRes"  class="btn btn-primary" 
                                  data-toggle="collapse" 
                                  data-target="#evtAbreRes" 
                                  aria-expanded="true" 
                                  aria-controls="evtAbreRes" 
                                  type="button">Resumo</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreRes" aria-expanded="false" role="presentation">
          <div id="cllRes" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblRes" class="alert-info">Mostrando lançamentos</label>
          </div>
        </div>
      </div>
    </section>
    <script>
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                                                PopUp RESUMO                                                //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreRes  = new Collapse($doc('abreRes'));
      abreRes.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreRes = document.getElementById('evtAbreRes');
      collapseAbreRes.addEventListener('show.bs.collapse', function(el){ 
        try{
          $doc("lblRes").innerHTML="Resumo acumulado(Despesa/Receita)</b>";
          $doc("cllRes").style.height = (parseInt((document.getElementById("dPaifrmRes").style.height).slice(0,-2))+2)+"em";
          abreRes.status="ok";
          
          /*
          chkds=objCm.gerarJson("1").gerar();
          if( chkds[0].FLAG != 3 )
            throw "FAVOR SELECIONAR ITEM COM FAVORECIDO VALIDO!";
          
          
          
          clsJs=jsString("lote");                      
          clsJs.add("rotina"      , "detLancto"                                 );  // Detalhe dos lanctos
          clsJs.add("login"       , jsPub[0].usr_login                          );
          clsJs.add("codfvr"      , chkds[0].CODFVR                             );
          clsJs.add("codbnc"      , chkds[0].BNC                                );
          clsJs.add("where"       , $doc("edtFluxo").getAttribute("data-where") );
          //////////////////////
          // Enviando para o Php
          //////////////////////
          var fd = new FormData();
          fd.append("contabilmes" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});              
            abreRes.hide();
          } else {  
            objRes.montarHtmlCE2017(jsRes); 
            jsRes.registros=objRes.addIdUnico(retPhp[0]["dados"]);
            objRes.ordenaJSon(jsRes.indiceTable,false);  
            objRes.montarBody2017();
            $doc("lblRes").innerHTML="Mostrando lançamentos <b>"+chkds[0].FAVORECIDO+"</b>";
            $doc("cllRes").style.height = (parseInt((document.getElementById("dPaifrmRes").style.height).slice(0,-2))+2)+"em";
            abreRes.status="ok";
          };  
          */
        }catch(e){
          abreRes.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbreRes.addEventListener('shown.bs.collapse', function(){ 
        if( abreRes.status=="err" )
          abreRes.hide();
      },false);
    </script>
    
    
    
  </body>
</html>