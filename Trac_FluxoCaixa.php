<?php
  session_start();
  if( isset($_POST["fluxocaixa"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      //require("classPhp/dataCompetencia.class.php");      

      //$clsCompet  = new dataCompetencia();    
      $vldr       = new validaJson();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["fluxocaixa"]);
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
        ////////////////////////////////////////////////
        //            Lancamentos financeiros         //
        ////////////////////////////////////////////////
        if( $lote[0]->rotina=="detLancto" ){
          $sql= "SELECT A.PGR_LANCTO";
          $sql.="       ,FVR.FVR_APELIDO";          
          $sql.="       ,SUBSTRING(dbo.fnc_Data(A.PGR_VENCTO),1,10) AS VENCTO";
          $sql.="       ,(A.PGR_VLRLIQUIDO*A.PGR_INDICE) AS VALOR";
          $sql.="  FROM PAGAR A";
          $sql.="  LEFT OUTER JOIN BANCO BNC ON A.PGR_CODBNC=BNC.BNC_CODIGO";          
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PGR_CODFVR=FVR.FVR_CODIGO";          
          $sql.=" ".$lote[0]->where;
          $sql.=" AND (A.PGR_CODFVR=".$lote[0]->codfvr.")";
          $sql.=" AND (A.PGR_CODBNC=".$lote[0]->codbnc.")";
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
          };  
        };
        
        /////////////////////////
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="filtrar" ){        
          ///////////////////////////////////////////////////////////////////////////
          // Total de registros retornados para JS
          ///////////////////////////////////////////////////////////////////////////
          $totReg=0;
          ///////////////////////////////////////////////////////////////////////////
          // Variavel para pegar acumulado por banco e acumulado por banco/favorecido
          ///////////////////////////////////////////////////////////////////////////
          $colValor =" ,SUM(CASE WHEN PGR_VENCTO<convert(varchar, getdate(), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as atr";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, getdate(), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as hoje";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,1,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia01";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,2,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia02";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,3,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia03";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,4,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia04";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,5,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia05";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,6,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia06";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,7,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia07";
          $colValor.=" ,SUM(CASE WHEN PGR_VENCTO=convert(varchar, DATEADD(day,8,getdate()), 102) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as dia08";          
          $colValor.=" ,SUM(CASE WHEN ((PGR_VENCTO>=convert(varchar, DATEADD(day,9,getdate()), 102)) AND (PGR_VENCTO<='".$lote[0]->dtfim."')) THEN (A.PGR_VLRLIQUIDO*A.PGR_INDICE) ELSE 0 END) as outros";
          //
          ///////////////////////////////////////////////////////////////////////////
          // Variavel where
          ///////////////////////////////////////////////////////////////////////////
          $where= " WHERE (A.PGR_DATAPAGA IS NULL)";
          $where.="   AND (A.PGR_VENCTO<='".$lote[0]->dtfim."')";
          $where.="   AND (A.PGR_CODEMP=".$lote[0]->codemp.")";
          $where.="   AND (BNC.BNC_ENTRAFLUXO='S')";
          if( $lote[0]->previsao=="SIM" )
            $where.="   AND (A.PGR_CODPTP IN('CP','CR','PP','PR'))";
          if( $lote[0]->previsao=="NAO" )
            $where.="   AND (A.PGR_CODPTP IN('CP','CR'))";
          //
          //
          $sql ="SELECT A.PGR_CODBNC AS codbnc";
          $sql.="       ,BNC.BNC_NOME AS desbnc";
          $sql.=$colValor;
          $sql.="       ,BNC.BNC_SALDO AS saldo";
          $sql.="  FROM PAGAR A";
          $sql.="  LEFT OUTER JOIN BANCO BNC ON A.PGR_CODBNC=BNC_CODIGO";
          $sql.=$where;
          $sql.="  GROUP BY A.PGR_CODBNC,BNC.BNC_NOME,BNC.BNC_SALDO";
          $sql.="  ORDER BY BNC.BNC_NOME";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Pegando o acumulado por dia de cada banco
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // CODBNC DESBNC           ATR        HOJE      DIA01       DIA02  DIA03   DIA04 DIA05 DIA06 DIA07  OUTROS       SALDO
            //   2	  BRADESCO	  11073.60	  67531.13	-29733.50	  -12333.07	  0.00	  0.00	0.00	0.00	0.00	  0.00	      0.00
            //   1	  ITAU-31906	10018.66	  65964.54	-28916.70	  -12049.03	  0.00	  0.00	0.00	0.00	0.00	  0.00	      0.00
            //   3	  ITAU-40800	-9978.08	 -36324.82	-75487.20	  -30056.29	  -300.00	0.00	0.00	0.00	0.00	900.00	  -4294.68
            //   4	  SANTANDER	- 11155.44	 -40201.44	-77338.90	  -30650.80	  0.00	  0.00	0.00	0.00	0.00	  0.00	  -2027.10          
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $tblSaldo=$retCls["dados"];
            
            $sql ="SELECT A.PGR_CODBNC AS codbnc";
            $sql.="       ,A.PGR_CODFVR AS codfvr";
            $sql.="       ,BNC.BNC_NOME AS desbnc";
            $sql.="       ,FVR.FVR_APELIDO AS desfvr";
            $sql.=$colValor;          
            $sql.="       ,MAX(CAST(0 AS INTEGER)) AS saldoini"; //Para saber se vou pegar o saldo inicial
            $sql.="       ,MAX(CAST(0 AS INTEGER)) AS saldofim"; //Para saber se vou pegar o final inicial            
            $sql.="  FROM PAGAR A";
            $sql.="  LEFT OUTER JOIN BANCO BNC ON A.PGR_CODBNC=BNC_CODIGO";
            $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PGR_CODFVR=FVR.FVR_CODIGO";
            $sql.=$where;
            $sql.="  GROUP BY A.PGR_CODBNC,A.PGR_CODFVR,BNC.BNC_NOME,FVR.FVR_APELIDO";
            $sql.="  ORDER BY BNC.BNC_NOME,FVR.FVR_APELIDO";
            $classe->msgSelect(false);
            $retCls=$classe->selectAssoc($sql);
            if( $retCls["qtos"]==0 ){
              $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
            } else { 
              $tblFluxo = $retCls["dados"];
              $totReg   = count($tblFluxo);
              //////////////////////////////////////////////////////////////////
              // Tabela IF para guardar o saldo inicial/final de todos os bancos
              //////////////////////////////////////////////////////////////////
              $tblIF    = [];
              array_push($tblIF,[										
                 "codbnc"	  =>  0
                ,"codfvr"	  =>  0
                ,"desbnc"	  =>  "*"
                ,"desfvr"	  =>  "SALDO INICIAL"
                ,"flag"     =>  1
                ,"atr"	    =>  0
                ,"hoje"	    =>  0
                ,"dia01"	  =>  0
                ,"dia02"	  =>  0
                ,"dia03"	  =>  0
                ,"dia04"	  =>  0
                ,"dia05"	  =>  0
                ,"dia06"	  =>  0
                ,"dia07"	  =>  0
                ,"dia08"	  =>  0
                ,"outros"	  =>  0
              ]);
              array_push($tblIF,[										
                 "codbnc"	  =>  0
                ,"codfvr"	  =>  0
                ,"desbnc"	  =>  "*"
                ,"desfvr"	  =>  "SALDO FINAL"
                ,"flag"     =>  5
                ,"atr"	    =>  0
                ,"hoje"	    =>  0
                ,"dia01"	  =>  0
                ,"dia02"	  =>  0
                ,"dia03"	  =>  0
                ,"dia04"	  =>  0
                ,"dia05"	  =>  0
                ,"dia06"	  =>  0
                ,"dia07"	  =>  0
                ,"dia08"	  =>  0
                ,"outros"	  =>  0
              ]);
              ////////////////////////////////////////////////////////////
              // COLOCANDO AQUI QUANDO VOU GERAR SALDOINICIAL E SALDOFINAL
              ////////////////////////////////////////////////////////////
              $tam=count($tblFluxo);
              for($lin=0; $lin<$tam; $lin++){
                /////////////////////////////
                // Se for o primeiro registro
                /////////////////////////////
                if( $lin==0 ){
                  $tblFluxo[$lin]["saldoini"]=$tblFluxo[$lin]["codbnc"];
                  $novoBanco  = $tblFluxo[$lin]["codbnc"];
                  continue;
                };  
                /////////////////////////////
                // Se for o ultimo registro
                /////////////////////////////
                if( $lin==($tam-1) ){
                  $tblFluxo[$lin]["saldofim"]=$tblFluxo[$lin]["codbnc"];
                  break;
                };  
                /////////////////////////////
                // Meio da table
                /////////////////////////////
                if( $tblFluxo[$lin]["codbnc"] != $novoBanco ){
                  $tblFluxo[$lin-1]["saldofim"]=$tblFluxo[$lin-1]["codbnc"];  
                  $tblFluxo[$lin]["saldoini"]=$tblFluxo[$lin]["codbnc"];  
                  $novoBanco  = $tblFluxo[$lin]["codbnc"];
                };  
              };  
              //
              //
              $novoBanco  = 0;
              $tblRet  	  = []; //Array retorno para JS
              foreach( $tblFluxo as $flu ){
                if( $flu["saldoini"]>0 ){
                  foreach( $tblSaldo as $sal ){
                    if( $sal["codbnc"]==$flu["saldoini"] ){
                      array_push($tblRet,[										
                         "codbnc"	  =>  $sal["codbnc"]
                        ,"codfvr"	  =>  0
                        ,"desbnc"	  =>  $sal["desbnc"]
                        ,"desfvr"	  =>  "SALDOI ".$sal["desbnc"]
                        ,"flag"     =>  2
                        ,"atr"	    =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]-$sal["dia02"]-$sal["dia01"]-$sal["hoje"]-$sal["atr"]
                        ,"hoje"	    =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]-$sal["dia02"]-$sal["dia01"]-$sal["hoje"]
                        ,"dia01"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]-$sal["dia02"]-$sal["dia01"]
                        ,"dia02"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]-$sal["dia02"]
                        ,"dia03"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]
                        ,"dia04"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]
                        ,"dia05"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]
                        ,"dia06"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]
                        ,"dia07"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]
                        ,"dia08"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]                        
                        ,"outros"	  =>  $sal["saldo"]-$sal["outros"]
                      ]);
                    };
                  };
                };      
                array_push($tblRet,[										
                   "codbnc"	  =>  $flu["codbnc"]
                  ,"codfvr"	  =>  $flu["codfvr"]
                  ,"desbnc"	  =>  $flu["desbnc"]
                  ,"desfvr"	  =>  $flu["desfvr"]
                  ,"flag"     =>  3
                  ,"atr"	    =>  $flu["atr"]
                  ,"hoje"	    =>  $flu["hoje"]
                  ,"dia01"	  =>  $flu["dia01"]
                  ,"dia02"	  =>  $flu["dia02"]
                  ,"dia03"	  =>  $flu["dia03"]
                  ,"dia04"	  =>  $flu["dia04"]
                  ,"dia05"	  =>  $flu["dia05"]
                  ,"dia06"	  =>  $flu["dia06"]
                  ,"dia07"	  =>  $flu["dia07"]
                  ,"dia08"	  =>  $flu["dia08"]
                  ,"outros"	  =>  $flu["outros"]
                ]);
                if( $flu["saldofim"]>0 ){
                  foreach( $tblSaldo as $sal ){
                    if( $sal["codbnc"]==$flu["saldofim"] ){
                      array_push($tblRet,[										
                         "codbnc"	  =>  $sal["codbnc"]
                        ,"codfvr"	  =>  0
                        ,"desbnc"	  =>  $sal["desbnc"]
                        ,"desfvr"	  =>  "SALDOF ".$sal["desbnc"]
                        ,"flag"     =>  4                        
                        ,"atr"	    =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]-$sal["dia02"]-$sal["dia01"]-$sal["hoje"]
                        ,"hoje"	    =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]-$sal["dia02"]-$sal["dia01"]
                        ,"dia01"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]-$sal["dia02"]
                        ,"dia02"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]-$sal["dia03"]
                        ,"dia03"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]-$sal["dia04"]
                        ,"dia04"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]-$sal["dia05"]
                        ,"dia05"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]-$sal["dia06"]
                        ,"dia06"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]-$sal["dia07"]
                        ,"dia07"	  =>  $sal["saldo"]-$sal["outros"]-$sal["dia08"]
                        ,"dia08"	  =>  $sal["saldo"]-$sal["outros"]                        
                        ,"outros"	  =>  $sal["saldo"]
                      ]);
                    };
                  };
                };      
              }; 
              //////////////////////////////////////////////////////////////
              //   Colocando o saldo inicial/final de todos os bancos     // 
              //////////////////////////////////////////////////////////////
              foreach( $tblRet as $ret ){
                if( $ret["flag"]==2 ){
                  $tblIF[0]["atr"]    +=  $ret["atr"];
                  $tblIF[0]["hoje"]   +=  $ret["hoje"];
                  $tblIF[0]["dia01"]  +=  $ret["dia01"];
                  $tblIF[0]["dia02"]  +=  $ret["dia02"];
                  $tblIF[0]["dia03"]  +=  $ret["dia03"];
                  $tblIF[0]["dia04"]  +=  $ret["dia04"];
                  $tblIF[0]["dia05"]  +=  $ret["dia05"];
                  $tblIF[0]["dia06"]  +=  $ret["dia06"];
                  $tblIF[0]["dia07"]  +=  $ret["dia07"];
                  $tblIF[0]["dia08"]  +=  $ret["dia08"];
                  $tblIF[0]["outros"] +=  $ret["outros"];
                };  
                if( $ret["flag"]==4 ){
                  $tblIF[1]["atr"]    +=  $ret["atr"];
                  $tblIF[1]["hoje"]   +=  $ret["hoje"];
                  $tblIF[1]["dia01"]  +=  $ret["dia01"];
                  $tblIF[1]["dia02"]  +=  $ret["dia02"];
                  $tblIF[1]["dia03"]  +=  $ret["dia03"];
                  $tblIF[1]["dia04"]  +=  $ret["dia04"];
                  $tblIF[1]["dia05"]  +=  $ret["dia05"];
                  $tblIF[1]["dia06"]  +=  $ret["dia06"];
                  $tblIF[1]["dia07"]  +=  $ret["dia07"];
                  $tblIF[1]["dia08"]  +=  $ret["dia08"];
                  $tblIF[1]["outros"] +=  $ret["outros"];
                };  
              };    

              
              $tblJs    = [];
              array_push($tblJs,[
                $tblIF[0]["codbnc"]
                ,$tblIF[0]["codfvr"]
                ,$tblIF[0]["desbnc"]
                ,$tblIF[0]["desfvr"]
                ,$tblIF[0]["flag"]
                ,$tblIF[0]["atr"]
                ,$tblIF[0]["hoje"]
                ,$tblIF[0]["dia01"]
                ,$tblIF[0]["dia02"]
                ,$tblIF[0]["dia03"]
                ,$tblIF[0]["dia04"]
                ,$tblIF[0]["dia05"]
                ,$tblIF[0]["dia06"]
                ,$tblIF[0]["dia07"]
                ,$tblIF[0]["dia08"]
                ,$tblIF[0]["outros"]
              ]);
              //  
              foreach( $tblRet as $ret ){
                array_push($tblJs,[
                  $ret["codbnc"]
                  ,$ret["codfvr"]
                  ,$ret["desbnc"]
                  ,$ret["desfvr"]
                  ,$ret["flag"]
                  ,$ret["atr"]
                  ,$ret["hoje"]
                  ,$ret["dia01"]
                  ,$ret["dia02"]
                  ,$ret["dia03"]
                  ,$ret["dia04"]
                  ,$ret["dia05"]
                  ,$ret["dia06"]
                  ,$ret["dia07"]
                  ,$ret["dia08"]
                  ,$ret["outros"]
                ]);
              };  
              array_push($tblJs,[
                $tblIF[1]["codbnc"]
                ,$tblIF[1]["codfvr"]
                ,$tblIF[1]["desbnc"]
                ,$tblIF[1]["desfvr"]
                ,$tblIF[1]["flag"]
                ,$tblIF[1]["atr"]
                ,$tblIF[1]["hoje"]
                ,$tblIF[1]["dia01"]
                ,$tblIF[1]["dia02"]
                ,$tblIF[1]["dia03"]
                ,$tblIF[1]["dia04"]
                ,$tblIF[1]["dia05"]
                ,$tblIF[1]["dia06"]
                ,$tblIF[1]["dia07"]
                ,$tblIF[1]["dia08"]
                ,$tblIF[1]["outros"]
              ]);
              $retorno='[{ "retorno":"OK"
                          ,"dados"  :'.json_encode($tblJs).'
                          ,"where"  :"'.$where.'"
                          ,"totreg"  :"'.$totReg.'"
                          ,"erro"   :""}]'; 
            };
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
    <title>Fluxo de caixa</title>
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
        document.getElementById("cbPrevisao").focus();
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsFc={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "BNC"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i3"] 
                      //,"align"          : "center"    
                      ,"tamGrd"         : "2em"
                      //,"tamImp"         : "15"
                      //,"excel"          : "S"
                      //,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "CODFVR"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      //,"align"          : "center"    
                      //,"tamGrd"         : "5em"
                      //,"tamImp"         : "15"
                      //,"excel"          : "S"
                      //,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "BANCO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      //,"tamImp"         : "15"
                      ,"excel"          : "S"
                      //,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "FAVORECIDO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "12em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      //,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "FLAG"
                      ,"fieldType"      : "str"
                      //,"tamGrd"         : "2em"
                      //,"tamImp"         : "15"
                      ,"excel"          : "S"
                      //,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "ATR"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "HOJE"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
                      
            ,{"id":8  ,"labelCol"       : "DIA01"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "DIA02"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "DIA03"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "DIA04"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "DIA05"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "DIA06"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "DIA07"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "DIA08"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":18 ,"labelCol"       : "OUTROS"
                      ,"fieldType"      : "flo2" 
                      ,"sepMilhar"      : true                      
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
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
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "switch (ceTr.cells[5].innerHTML){case '2' : ceTr.style.backgroundColor='#BDB76B';ceTr.style.color='black';break; case '4' : ceTr.style.backgroundColor='#DEB887';ceTr.style.color='black';break; case '1' : case '5' : ceTr.style.backgroundColor='#836FFF';ceTr.style.color='black';break;  }"          
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar                     
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmFc"                   // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaFc"                // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmFc"                   // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnFc"                  // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblFc"                   // Nome da table
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
          ,"relTitulo"      : "FLUXO CAIXA"             // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "*"                       // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objFc === undefined ){  
          objFc=new clsTable2017("objFc");
        };  
        /////////////////////////////////////////////////
        //        Objeto clsTable2017 TITULOS          //
        /////////////////////////////////////////////////
        jsPgr={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"tamGrd"         : "0em"             
                      ,"padrao":1}            
            ,{"id":1  ,"field"          : "LANCTO" 
                      ,"labelCol"       : "LANCTO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "20"
                      ,"padrao":0}
            ,{"id":2  ,"field"          : "FAVORECIDO"
                      ,"labelCol"       : "FAVORECIDO"
                      ,"tamGrd"         : "15em"
                      ,"tamImp"         : "40"											
                      ,"padrao":0}
            ,{"id":3  ,"field"          : "VENCTO"
                      ,"labelCol"       : "VENCTO"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"											
                      ,"padrao":0}
            ,{"id":4  ,"field"          : "VALOR" 
                      ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2"                       
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "25"
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
          ,"div"            : "frmPgr"             // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaPgr"          // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmPgr"             // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "cllPgr"             // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "cllPgr"             // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)          
          ,"tbl"            : "tblPgr"             // Nome da table
          ,"prefixo"        : "ch"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"position"       : "relative"          
          ,"width"          : "55em"               // Tamanho da table
          ,"height"         : "40em"               // Altura da table
          ,"tableLeft"      : "sim"                // Se tiver menu esquerdo
          ,"relTitulo"      : "TITULOS"            // Titulo do relatório
          ,"relOrientacao"  : "R"                  // Paisagem ou retrato
          ,"relFonte"       : "8"                  // Fonte do relatório
          ,"indiceTable"    : "LANCTO"             // Indice inicial da table
          ,"tamBotao"       : "12"                 // Tamanho botoes defalt 12 [12/25/50/75/100]
        }; 
        if( objPgr === undefined ){  
          objPgr=new clsTable2017("objPgr");
        }; 
        
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

        jsFc.titulo[8].labelCol   = arrCol[0];
        jsFc.titulo[9].labelCol   = arrCol[1];
        jsFc.titulo[10].labelCol  = arrCol[2];
        jsFc.titulo[11].labelCol  = arrCol[3];
        jsFc.titulo[12].labelCol  = arrCol[4];
        jsFc.titulo[13].labelCol  = arrCol[5];
        jsFc.titulo[14].labelCol  = arrCol[6];
        jsFc.titulo[15].labelCol  = arrCol[7];
        ///////////////////////////////////////////
        // Acrescentando +10 dias ao fluxo de caixa
        ///////////////////////////////////////////
        for( let ini=9;ini<19;ini++ ){
          let ceOpt	  = document.createElement("option");        
          ceOpt.value = jsDatas(ini).retDDMMYYYY();
          ceOpt.text  = jsDatas(ini).retDDMMYYYY();
          document.getElementById("cbDtFim").appendChild(ceOpt);
        };
        objFc.montarHtmlCE2017(jsFc); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsFc,["BNC","CODFVR","BANCO","FAVORECIDO","FLAG","ATR","HOJE",arrCol[0],arrCol[1],arrCol[2],arrCol[3],arrCol[4],arrCol[5],arrCol[6],arrCol[7],"OUTROS"]);
      });
      var objFc;                      // Obrigatório para instanciar o JS TFormaCob
      var jsFc;                       // Obj principal da classe clsTable2017
      var objPgr;                     // Obrigatório para instanciar o JS TFormaCob
      var jsPgr;                      // Obj principal da classe clsTable2017
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario      
      var arrCol;                     // Converter os nomes das coluna na table      
      var clsChecados;                // Classe para montar Json
      var chkds;                      // Guarda todos registros checados na table 
      var contMsg   = 0;              // contador para mensagens      
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d05);
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado
      //
      function btnFiltrarClick() { 
        ///////////////////////////
        //jsDatas soh executa input
        ///////////////////////////
        $doc("edtFluxo").value=$doc("cbDtFim").value;
        //
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"                         );
        clsJs.add("login"       , jsPub[0].usr_login                );
        clsJs.add("codemp"      , jsPub[0].emp_codigo               );
        clsJs.add("previsao"    , $doc("cbPrevisao").value          );
        clsJs.add("dtfim"       , jsDatas("edtFluxo").retMMDDYYYY() );        
        fd = new FormData();
        fd.append("fluxocaixa" , clsJs.fim());
        msg     = requestPedido(arqLocal,fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsFc.registros=objFc.addIdUnico(retPhp[0]["dados"]);
          objFc.montarBody2017();
          ////////////////////////////////////////////////////////////////
          // Guardando a ultima clausula where para usar em buscar titulos
          ////////////////////////////////////////////////////////////////
          $doc("edtFluxo").setAttribute("data-where",retPhp[0]["where"]);          
        };  
      };
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

        clsChecados = objFc.gerarJson("n");
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
        clsChecados = objFc.gerarJson("n");
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
      
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="fncImprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela."><i class="indFa fa-print"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="fncExcel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela."><i class="indFa fa-file-excel-o"></i>
          </div>
        </section>
      </aside>

      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <!--
        <a href="#" class="indLabel"  style="width:10%;"><div id="tituloMenu">Fluxo caixa</div></a>
        -->

        <div class="colMd12" style="float:left;margin-bottom:0px;height:50px;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">Fluxo Caixa</span>
              <span id="spnEmpApelido" class="infoBoxLabel"></span>
            </div>
          </div>
        </div>  

        <div id="indDivInforme" class="indTopoInicio80" style="padding-top:2px;">
          <div class="campotexto campo12">
            <select class="campo_input_combo" id="cbPrevisao">
              <option value="N" selected>NÃO</option>            
              <option value="S">SIM</option>
            </select>
            <label class="campo_label campo_required" for="cbPrevisao">PREVISÃO:</label>
          </div>
          <div class="campotexto campo12">
            <select class="campo_input_combo" id="cbDtFim">
            </select>
            <label class="campo_label campo_required" for="cbDtFim">ATÉ:</label>
          </div>
          <div id="btnFiltrar" onClick="btnFiltrarClick();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-check"> Filtrar</i></div>
          <div id="btnFechar" onClick="window.close();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-close"> Fechar</i></div>
        </div>
        <div class="inactive">
          <input id="edtFluxo" value="**/**/****" 
                               data-where="*"
                               type="text" />
        </div>
        
      </div>
      <section>
        <section id="sctnFc">
        </section>  
      </section>
      <form method="post"
            name="frmFc"
            id="frmFc"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/>      
      </form>  
    </div>
    <!--
    Buscando o historico do OS
    -->
    <section id="collapseSectionPgr" class="section-collapse">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a id="aLabel" class="btn btn-default disabled">Buscar</a>
            <button id="abrePgr"  class="btn btn-primary" 
                                  data-toggle="collapse" 
                                  data-target="#evtAbrePgr" 
                                  aria-expanded="true" 
                                  aria-controls="evtAbrePgr" 
                                  type="button">Lançamentos</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbrePgr" aria-expanded="false" role="presentation">
          <div id="cllPgr" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblPgr" class="alert-info">Mostrando lançamentos</label>
          </div>
        </div>
      </div>
    </section>
    <script>
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                                                PopUp PGR                                                    //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abrePgr  = new Collapse($doc('abrePgr'));
      abrePgr.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbrePgr = document.getElementById('evtAbrePgr');
      collapseAbrePgr.addEventListener('show.bs.collapse', function(el){ 
        try{
          chkds=objFc.gerarJson("1").gerar();
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
          fd.append("fluxocaixa" , clsJs.fim());
          msg=requestPedido(arqLocal,fd); 
          retPhp=JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("cnti",retPhp[0].erro,{cabec:"Aviso"});              
            abrePgr.hide();
          } else {  
            objPgr.montarHtmlCE2017(jsPgr); 
            jsPgr.registros=objPgr.addIdUnico(retPhp[0]["dados"]);
            objPgr.ordenaJSon(jsPgr.indiceTable,false);  
            objPgr.montarBody2017();
            $doc("lblPgr").innerHTML="Mostrando lançamentos <b>"+chkds[0].FAVORECIDO+"</b>";
            $doc("cllPgr").style.height = (parseInt((document.getElementById("dPaifrmPgr").style.height).slice(0,-2))+2)+"em";
            abrePgr.status="ok";
          };  
        }catch(e){
          abrePgr.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbrePgr.addEventListener('shown.bs.collapse', function(){ 
        if( abrePgr.status=="err" )
          abrePgr.hide();
      },false);
    </script>
    
    
    
  </body>
</html>