<?php
  session_start();
  if( isset($_POST["faturamento"]) ){
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
      $retCls     = $vldr->validarJs($_POST["faturamento"]);
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $sqls     = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        //
        // inserindo os titulos
        $lista = json_decode($_POST["dados"]);
        echo "[".$lista."]";
        exit;
        foreach($lista as $titulo){
          // pegando os dados
          $codfvr       = $titulo->cliente;
          $valor        = $titulo->mensal + $titulo->pontual;
          $codbnc       = 7;
          $codfc        = 1;
          $codtd        = 11;
          $vencto       = date("d.m.Y",strtotime("+30 days"));
          $docto        = "OS"+$titulo->codigo;
          $dtdocto      = date("d.m.Y");
          $codptt       = 1;
          $obs          = "";
          $codptp       = 1;
          $codpt        = 1;
          $vlrevento    = $valor;
          $vlrparcela   = $valor;
          $vlrdesconto  = 0;
          $codcc        = 1;
          $codsnf       = 0;
          $codemp       = 1;
          $apr          = "S";
          $codfll       = 1001;
          $verdireito   = 4;
          $codcmp       = 0;
          $reg          = "P";

          // lançando no financeiro
          $lancto = $classe->generator("PAGAR"); 
          $master = $lancto;
          $sql ="INSERT INTO VPAGAR(
          PGR_LANCTO
          ,PGR_BLOQUEADO
          ,PGR_CODBNC
          ,PGR_CODFVR
          ,PGR_CODFC
          ,PGR_CODTD
          ,PGR_VENCTO
          ,PGR_DOCTO
          ,PGR_DTDOCTO
          ,PGR_CODPTT
          ,PGR_MASTER
          ,PGR_OBSERVACAO
          ,PGR_CODPTP
          ,PGR_CODPT
          ,PGR_VLREVENTO
          ,PGR_VLRPARCELA
          ,PGR_VLRDESCONTO
          ,PGR_CODCC
          ,PGR_CODSNF
          ,PGR_APR
          ,PGR_CODEMP
          ,PGR_CODFLL
          ,PGR_VERDIREITO
          ,PGR_CODCMP
          ,PGR_REG
          ,PGR_CODUSR) VALUES(
          '$lancto'
          ,'N'
          ,$codbnc
          ,$codfvr
          ,$codfc
          ,$codtd
          ,'$vencto'
          ,'$docto'
          ,'$dtdocto'
          ,$codptt
          ,$master
          ,'$obs'
          ,$codptp
          ,$codpt
          ,$vlrevento
          ,$vlrparcela
          ,$vlrdesconto
          ,$codcc
          ,$codsnf
          ,'$apr'
          ,$codemp
          ,$codfil 
          ,$verdireito
          ,$codcmp
          ,'$reg'
          ,"  .$_SESSION["usr_codigo"] . "
          )";
          $sqls[] = $sql;
        }
        $retCls=$classe->cmd($sqls);
        if( $retCls['retorno']=="OK" ){
          $retorno='[{"retorno":"OK","dados":"","erro":"'.count($sqls).' REGISTRO(s) ATUALIZADO(s)!"}]'; 
        } else {
          $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
        }
      }
    } catch(Exception $e){

    }
    echo $retorno;
  } else 
   echo '[{"retorno":"ERR","dados":"","erro":"ausencia de parametros"}]';  
