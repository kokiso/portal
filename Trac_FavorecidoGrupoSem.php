<?php
  session_start();
  if( isset($_POST["favorecidogruposem"]) ){
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
      $retCls   = $vldr->validarJs($_POST["favorecidogruposem"]);
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
        //$rotina   = $lote[0]->rotina;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        /////////////////////////////////////////////////////
        // Buscando a aliquota se for do simples
        /////////////////////////////////////////////////////

        if( $lote[0]->rotina=="semestral" ){
          $sql ="SELECT TOP 6 CMP_CODIGO AS CODIGO,CMP_NOME AS NOME FROM COMPETENCIA"; 
          $sql.=" WHERE ((CMP_CODIGO>=".$lote[0]->codcmp.") AND (CMP_CODEMP=".$_SESSION["emp_codigo"]."))";
          $sql.=" ORDER BY CMP_CODIGO"; 
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUMA COMPETENCIA LOCALIZADA"}]';              
          } else { 
            $desmes   = ["AAA99","AAA99","AAA99","AAA99","AAA99","AAA99"];
            ///////////////////////////////////////////////////////
            // Opcao por PGR_VENCTO ou PGR_DATAPAGA(Baixa)
            ///////////////////////////////////////////////////////
            $campo="PGR_VENCTO";
            if( $lote[0]->venctobaixa=="B" )
              $campo="PGR_DATAPAGA";  
            ///////////////////////////////////////////////////////
            // Gravando os seis meses de sql caso algum naum exista
            ///////////////////////////////////////////////////////
            $arrSql   = [];
            $arrSql[0]=",SUM( 0 ) AS AAA99";
            $arrSql[1]=",SUM( 0 ) AS AAA99";
            $arrSql[2]=",SUM( 0 ) AS AAA99";
            $arrSql[3]=",SUM( 0 ) AS AAA99";
            $arrSql[4]=",SUM( 0 ) AS AAA99";
            $arrSql[5]=",SUM( 0 ) AS AAA99";
            //
            //
            $ymd      = "";
            $tblCmp   = $retCls["dados"];
            $cont     = 0;
            foreach ( $tblCmp as $reg ){
              $desmes[$cont]=str_replace("/","",$reg["NOME"]);
              $dia  = "01";
              $mes  = substr($reg["CODIGO"],4,6);
              $ano  = substr($reg["CODIGO"],0,4);
              $ymd  = $ano."-".$mes."-".$dia;
              /////////////////////////////
              // Montando a clausula select
              /////////////////////////////  
              $ini=$ymd;
              $fim=date("Y-m-t", strtotime($ymd));
              $arrSql[$cont]=",SUM( CASE WHEN (".$campo." BETWEEN '".$ini."' AND '".$fim."') THEN A.PGR_VLRLIQUIDO ELSE 0 END ) AS ".$desmes[0];
              ///////////////////////////////////
              // Pegando a primeira e ultima data
              ///////////////////////////////////
              if( $cont==0 ){
                $dtIni=$ini;
                $dtFim=$fim;
              } else {
                $dtFim=$fim;
              };
              $cont++;
            };  
            ////////////////////////////////////////////////
            // Montando o select para retornar ao JavaScript
            ////////////////////////////////////////////////
            if( $lote[0]->grupofavorecido=="grupo" ){
              $sql ="SELECT GF.GF_NOME";
              $sql.=$arrSql[0];
              $sql.=$arrSql[1];
              $sql.=$arrSql[2];
              $sql.=$arrSql[3];
              $sql.=$arrSql[4];
              $sql.=$arrSql[5];
              $sql.="       ,SUM( CASE WHEN (".$campo." BETWEEN '".$dtIni."' AND '".$dtFim."') THEN A.PGR_VLRLIQUIDO ELSE 0 END )";
              $sql.="  FROM PAGAR A WITH(NOLOCK)";
              $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PGR_CODFVR=FVR.FVR_CODIGO";
              if( $lote[0]->codptp=="CP" ){            
                $sql.="  LEFT OUTER JOIN GRUPOFAVORECIDO GF ON FVR.FVR_GFCP=GF.GF_CODIGO";
              } else {
                $sql.="  LEFT OUTER JOIN GRUPOFAVORECIDO GF ON FVR.FVR_GFCR=GF.GF_CODIGO";              
              };  
              $sql.=" WHERE( (".$campo." BETWEEN '".$dtIni."' AND '".$dtFim."') AND (A.PGR_CODPTP='".$lote[0]->codptp."'))";
              $sql.="   AND( A.PGR_CODEMP=".$_SESSION["emp_codigo"].")";
              $sql.="  GROUP BY GF.GF_NOME";
            };
            if( $lote[0]->grupofavorecido=="favorecido" ){
              $sql ="SELECT SUBSTRING(FVR.FVR_NOME,1,35) AS FVR_NOME";
              $sql.=$arrSql[0];
              $sql.=$arrSql[1];
              $sql.=$arrSql[2];
              $sql.=$arrSql[3];
              $sql.=$arrSql[4];
              $sql.=$arrSql[5];
              $sql.="       ,SUM( CASE WHEN (".$campo." BETWEEN '".$dtIni."' AND '".$dtFim."') THEN A.PGR_VLRLIQUIDO ELSE 0 END )";
              $sql.="  FROM PAGAR A WITH(NOLOCK)";
              $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PGR_CODFVR=FVR.FVR_CODIGO";
              $sql.=" WHERE( (".$campo." BETWEEN '".$dtIni."' AND '".$dtFim."') AND (A.PGR_CODPTP='".$lote[0]->codptp."'))";
              $sql.="   AND( A.PGR_CODEMP=".$_SESSION["emp_codigo"].")";              
              $sql.="  GROUP BY FVR.FVR_NOME";
            };
            $classe->msgSelect(true);
            $retCls=$classe->select($sql);
            $retorno='[{"retorno":"OK"
                       ,"qtos": '.$retCls["qtos"].'
                       ,"tblSem":'.json_encode($retCls["dados"]).'
                       ,"tblMes":'.json_encode($desmes).'
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
    <title>Cadastro Titulo financeiro</title>
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
    <script>      
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){
        pega=localStorage.getItem("addParametro")
        //localStorage.removeItem("addParametro");
        document.getElementById("labelId").innerHTML=(pega=="grupo" ? "Grupo semestral" : "Favorecido semestral" );
        
        document.getElementById("edtDesCmp").foco();
        jsSem={
          "titulo":[
             {"id":0  ,"labelCol":"OPC" 
                      ,"tamGrd":"0em"
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "GRUPO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "25em"
                      ,"tamImp"         : "70"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : arrMes[0],"fieldType":"flo2","tamGrd":"9em","tamImp":"20","excel":"S","padrao":0}
            ,{"id":3  ,"labelCol"       : arrMes[1],"fieldType":"flo2","tamGrd":"9em","tamImp":"20","excel":"S","padrao":0} 
            ,{"id":4  ,"labelCol"       : arrMes[2],"fieldType":"flo2","tamGrd":"9em","tamImp":"20","excel":"S","padrao":0}
            ,{"id":5  ,"labelCol"       : arrMes[3],"fieldType":"flo2","tamGrd":"9em","tamImp":"20","excel":"S","padrao":0}
            ,{"id":6  ,"labelCol"       : arrMes[4],"fieldType":"flo2","tamGrd":"9em","tamImp":"20","excel":"S","padrao":0}
            ,{"id":7  ,"labelCol"       : arrMes[5],"fieldType":"flo2","tamGrd":"9em","tamImp":"20","excel":"S","padrao":0}            
            ,{"id":8  ,"labelCol"       : arrMes[6],"fieldType":"flo2","tamGrd":"9em","tamImp":"20","excel":"S","padrao":0}            
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
             {"texto":"Excel"     ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o"}  
            ,{"texto":"Imprimir"  ,"name":"horImprimir"   ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-print"       }                    
            ,{"texto":"Fechar"    ,"name":"horFechar"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-close"      }
          ] 

          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmSem"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaSem"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmSem"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnSem"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblSem"                  // Nome da table
          ,"prefixo"        : "sem"                     // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "*"                       // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "absolute"
          ,"width"          : "110em"                   // Tamanho da table
          ,"height"         : "65em"                    // Altura da table
          ,"nChecks"        : true                      // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "190px"                   // Se tiver menu esquerdo
          ,"relTitulo"      : "GRUPO"                     // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"formPassoPasso" : "*"                       // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "GRUPO"                   // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
        }; 
        if( objSem === undefined ){  
          objSem=new clsTable2017("objSem");
        };  
        objSem.montarHtmlCE2017(jsSem)
      });
      //
      var objSem;                     // Obrigatório para instanciar o JS Semestral
      var jsSem;                      // Obj principal da classe clsTable2017
      var msg;                        // Variavel para guardadar mensagens de retorno/erro
      var clsChecados;                // Classe para montar Json  
      var chkds;                      // Guarda todos registros checados na table 
      var tamC;                       // Guarda a quantidade de registros   
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP  
      var fd;                         // Formulario para envio de dados para o PHP
      var envPhp;                     // Envia para o Php
      var retPhp;                     // Retorno do Php para a rotina chamadora
      var pega;                       // Recuperar localStorage      
      var contMsg   = 0;
      var cmp       = new clsCampo(); 
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var arrMes    = ["JAN/18","FEV/18","MAR/18","ABR/18","MAI/18","JUN/18","TOTAL"];
      //
      function fncFiltrar(){
        try{        
          let retorno=validaCompetencia(document.getElementById("edtDesCmp").value);
          if( retorno.erro=="ok" ){
            document.getElementById("edtDesCmp").value=retorno.descmp;
            document.getElementById("edtCodCmp").value=retorno.codcmp;
            /////////////////
            // Chamando o php
            /////////////////  
            clsJs   = jsString("lote");  
            clsJs.add("rotina"          , "semestral"                                     );
            clsJs.add("login"           , jsPub[0].usr_login                              );
            clsJs.add("codcmp"          , document.getElementById("edtCodCmp").value      );
            clsJs.add("codptp"          , document.getElementById("cbCodPtp").value       );   
            clsJs.add("venctobaixa"     , document.getElementById("cbVenctoBaixa").value  );               
            clsJs.add("grupofavorecido" , pega                                            );  // Pode vir de Trac_FavorecidoGrupo.php ou Trac_Favorecido.php
            envPhp=clsJs.fim();  
            fd = new FormData();
            fd.append("favorecidogruposem", envPhp);
            msg     = requestPedido("Trac_FavorecidoGrupoSem.php",fd); 
            retPhp  = JSON.parse(msg);
            if( retPhp[0].retorno == "OK" ){
              if( retPhp[0]["tblSem"]=="" ){
                throw "Nenhum registro localizado para este periodo!";                 
              } else {
                let tbl = tblSem.getElementsByTagName("thead")[0].getElementsByTagName("th");
                ////////////////////////////////////////////////////////////
                // Sequencia para alteracao no nome das colunas grade e JSon
                ////////////////////////////////////////////////////////////
                tbl[2].innerHTML=retPhp[0]["tblMes"][0];  jsSem.titulo[2].labelCol=retPhp[0]["tblMes"][0]; 
                tbl[3].innerHTML=retPhp[0]["tblMes"][1];  jsSem.titulo[3].labelCol=retPhp[0]["tblMes"][1];
                tbl[4].innerHTML=retPhp[0]["tblMes"][2];  jsSem.titulo[4].labelCol=retPhp[0]["tblMes"][2];
                tbl[5].innerHTML=retPhp[0]["tblMes"][3];  jsSem.titulo[5].labelCol=retPhp[0]["tblMes"][3];
                tbl[6].innerHTML=retPhp[0]["tblMes"][4];  jsSem.titulo[6].labelCol=retPhp[0]["tblMes"][4];
                tbl[7].innerHTML=retPhp[0]["tblMes"][5];  jsSem.titulo[7].labelCol=retPhp[0]["tblMes"][5];
                //////////////////////  
                // Preenchendo a table
                //////////////////////
                jsSem.registros=retPhp[0]["tblSem"];
                objSem.montarBody2017();
                ///////////////////////////////////////////////////
                // Vendo se existe variacao para mostrar ao usuario
                ///////////////////////////////////////////////////
                let intVar=jsNmrs(document.getElementById("cbVariacao").value).inteiro().ret();
                if( intVar>0 ){
                  var el = document.getElementById("tblSem").getElementsByTagName("tbody")[0];
                  var nl = el.rows.length;
                  var nc        = ( el.rows[nl-1].cells.length -3 );  // -1 para tirar a coluna total
                  var colA      = 0.00;
                  var colB      = 0.00;
                  var variacao  = 0.00;
                  var el = document.getElementById("tblSem").getElementsByTagName("tbody")[0];            
                  
                  for( var lin=0; lin<nl; lin++ ){
                    for( var col=2; col<nc; col++ ){  
                      colA=jsNmrs(el.rows[lin].cells[col].innerHTML).dolar().ret();
                      colB=jsNmrs(el.rows[lin].cells[(col+1)].innerHTML).dolar().ret();
                      variacao=Math.abs((((colB-colA)/colA)*100));
                      if( variacao>=intVar )
                        el.rows[lin].cells[(col+1)].classList.add("fontVermelho");
                    };
                  }; 
                };
              };  
            };  
          } else {
            gerarMensagemErro("cmp",retorno.erro,{cabec:"Erro"});  
          };
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };  
      //
      function horImprimirClick(){
        //////////////////////////////////////////////
        // INICIA A IMPRESSAO DA COPIA DE DOCUMENTO //
        //////////////////////////////////////////////
        let rel = new relatorio();
        rel.tamFonte(9);
        rel.iniciar();
        rel.traco();
        rel.pulaLinha(1);
        rel.corFundo("cinzaclaro",9,190);    
        rel.cell(50,"Grupo - Comparativo semestral:",{borda:0,negrito:true});
        rel.cell(18,"A partir de:",{negrito:true});
        rel.cell(20,jsSem.titulo[2].labelCol,{negrito:false});
        
        rel.pulaLinha(10);
        rel.traco();
        rel.pulaLinha(1);
        rel.tamFonte(7);
        rel.cell(50,"GRUPO"   ,{borda:0,negrito:true,align:"L"});
        rel.cell(20,jsSem.titulo[2].labelCol,{align:"R"});
        rel.cell(20,jsSem.titulo[3].labelCol);
        rel.cell(20,jsSem.titulo[4].labelCol);
        rel.cell(20,jsSem.titulo[5].labelCol);
        rel.cell(20,jsSem.titulo[6].labelCol);
        rel.cell(20,jsSem.titulo[7].labelCol);
        rel.cell(20,"TOTAL");

        let tbl = document.getElementById("tblSem").getElementsByTagName("tbody")[0];
        let nl = tbl.rows.length;
        /////////////////////////////////////////////////////////////////////////////////
        // Como naum tenho o nome da coluna pois varia guardo nas var para naum confundir
        /////////////////////////////////////////////////////////////////////////////////
        let colGrupo  = 1;
        let colMesA   = 2;
        let colMesB   = 3;
        let colMesC   = 4;
        let colMesD   = 5;
        let colMesE   = 6;
        let colMesF   = 7;
        let colMesG   = 8;
        //////////////////////////
        // Para acumular os totais
        //////////////////////////
        let totMesA=0;
        let totMesB=0;
        let totMesC=0;
        let totMesD=0;
        let totMesE=0;
        let totMesF=0;
        let totMesG=0;
        
        tamC=msg.length;
        let zebra=false;
        rel.align("L");
        for( let lin=0;lin<nl;lin++ ){
          zebra=(lin % 2 ? true : false );  
          if( zebra ){
            rel.pulaLinha(6);
            rel.setX(10);
            rel.corFundo("cinzaclaro",4,190);    
          } else {
            rel.pulaLinha(6);
            rel.setX(10);
            rel.corFundo("branco",4,190);    
          }
          rel.pulaLinha(-6);          
          
          rel.cell(50,tbl.rows[lin].cells[colGrupo].innerHTML,{moeda:false,align:"L",negrito:false,pulaLinha:4});
          rel.cell(20,tbl.rows[lin].cells[colMesA].innerHTML,{moeda:true,align:"R"});
          rel.cell(20,tbl.rows[lin].cells[colMesB].innerHTML);
          rel.cell(20,tbl.rows[lin].cells[colMesC].innerHTML);
          rel.cell(20,tbl.rows[lin].cells[colMesD].innerHTML);
          rel.cell(20,tbl.rows[lin].cells[colMesE].innerHTML);
          rel.cell(20,tbl.rows[lin].cells[colMesF].innerHTML);
          rel.cell(20,tbl.rows[lin].cells[colMesG].innerHTML);
          
          totMesA+=jsNmrs(tbl.rows[lin].cells[colMesA].innerHTML).dolar().ret();
          totMesB+=jsNmrs(tbl.rows[lin].cells[colMesB].innerHTML).dolar().ret();
          totMesC+=jsNmrs(tbl.rows[lin].cells[colMesC].innerHTML).dolar().ret();
          totMesD+=jsNmrs(tbl.rows[lin].cells[colMesD].innerHTML).dolar().ret();
          totMesE+=jsNmrs(tbl.rows[lin].cells[colMesE].innerHTML).dolar().ret();
          totMesF+=jsNmrs(tbl.rows[lin].cells[colMesF].innerHTML).dolar().ret();
          totMesG+=jsNmrs(tbl.rows[lin].cells[colMesG].innerHTML).dolar().ret();
        }
        rel.pulaLinha(7);        
        rel.traco();
        rel.pulaLinha(-4);        
        rel.cell(50,"TOTAL",{moeda:false,align:"L",negrito:true,pulaLinha:3});
        rel.cell(20,totMesA,{moeda:true,align:"R"});
        rel.cell(20,totMesB);
        rel.cell(20,totMesC);
        rel.cell(20,totMesD);
        rel.cell(20,totMesE);
        rel.cell(20,totMesF);
        rel.cell(20,totMesG);
        envPhp=rel.fim();
        ///////////////////////////////////////////////////
        // PREPARANDO UM FORMULARIO PARA VER O RELATORIO //
        ///////////////////////////////////////////////////
        document.getElementById('sql').value=envPhp;
        document.getElementsByTagName('form')[0].submit();           
      };
      function horFecharClick(){
        window.close();
      }
      
      
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="divTopoInicio" class="divTopoInicio">
        <div class="divTopoInicio_logo"></div>
        <div class="teEsquerda"></div>
        <div style="font-size:12px;width:15%;float:left;"><h2 id="labelId" style="text-align:center;">Grupo semestral</h2></div>
        <div class="teEsquerda"></div>
        <div class="campotexto campo07"  style="margin-top:2px;">
          <input class="campo_input" id="edtDesCmp" 
                                     placeholder="AAA/MM"                 
                                     onkeyup="mascaraNumero('###/##',this,event,'letdig');"
                                     type="text" maxlength="6" />
          <label class="campo_label campo_required" for="edtDesCmp">Inicial mmm/aa:</label>
        </div>
        <div class="teEsquerda"></div>                        
        
        <div class="campotexto campo05"  style="margin-top:2px;">
          <select class="campo_input_combo" id="cbCodPtp">
            <option value="CP">CP</option>
            <option value="CR">CR</option>
          </select>
          <label class="campo_label" for="cbCodPtp">TIPO</label>
        </div>
        
        <div class="teEsquerda"></div>                                
        <div class="campotexto campo07"  style="margin-top:2px;">
          <select class="campo_input_combo" id="cbVenctoBaixa">
            <option value="V">VENCTO</option>
            <option value="B">BAIXA</option>
          </select>
          <label class="campo_label" for="cbVenctoBaixa">Opção</label>
        </div>
        
        
        
        <div class="teEsquerda"></div>                                
        <div class="campotexto campo07"  style="margin-top:2px;">
          <select class="campo_input_combo" id="cbVariacao">
            <option value="0">00%</option>
            <option value="5">05%</option>
            <option value="10">10%</option>
            <option value="15">15%</option>
            <option value="20">20%</option>
            <option value="25">25%</option>
            <option value="30">30%</option>
            <option value="50">50%</option>
          </select>
          <label class="campo_label" for="cbVariacao">Variação</label>
        </div>
        
        
        <div onClick="fncFiltrar();"    class="btnImagemEsq bie08 bieAzul" style="margin-top:2px;"><i class="fa fa-close"> Filtrar</i></div>                
        <div onClick="window.close();"  class="btnImagemEsq bie08 bieRed" style="margin-top:2px;"><i class="fa fa-close"> Fechar</i></div>  
        <div class="inactive">
          <input id="edtCodCmp" type="text" />
        </div>
      </div>

      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      <section>
        <section id="sctnSem" style="margin-left:100px;">
        </section>  
      </section>
      
      <form method="post" class="center" id="frmSem" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
      </form>
    </div>
  </body>
</html>