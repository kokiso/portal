<?php
  session_start();
  if( isset($_POST["funcao"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/consultaCep.class.php");  
      
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["funcao"]);
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
        $clsCep  = new consultaCep();
        $retorno=$clsCep->buscaCep($lote[0]->cep);
      };
    } catch(Exception $e ){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]'; 
    };    
    echo $retorno;
    exit;
  };  
?>
"use strict";
function jsBuscaCep(cep,login){
  clsJs   = jsString("lote");
  clsJs.add("rotina"  , "rotinaCep" );
  clsJs.add("cep"     , cep         );
  clsJs.add("login"   , login       );
  fd = new FormData();
  fd.append("funcao" , clsJs.fim()); 

  msg = requestPedido("jsBuscarCep.html",fd); 
  retPhp  = JSON.parse(msg);
  if( retPhp[0].retorno == "OK" ){
  }
}
