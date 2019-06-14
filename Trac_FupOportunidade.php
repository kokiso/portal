<?php
  session_start();
  if( isset($_POST["fupoportunidade"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php");
      require("classPhp/selectRepetido.class.php");                               
      $vldr     = new validaJSon();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["fupoportunidade"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd  = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } 
      else {
        $arrUpdt  = []; 
        $jsonObj  = $retCls["dados"];
        $lote     = $jsonObj->lote;
        $rotina   = $lote[0]->rotina;
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);

        if( $rotina=="buscapadrao" ){
          $sql="";
          $sql.="SELECT A.PG_CODPDR";
          $sql.="       ,A.PG_CODPTP";
          $sql.="       ,PDR.PDR_NOME";          
          $sql.="       ,A.PG_INDICE"; 
          $sql.="  FROM PADRAOGRUPO A";
          $sql.="  LEFT OUTER JOIN PADRAO PDR ON A.PG_CODPDR=PDR.PDR_CODIGO AND PDR.PDR_ATIVO='S'";          
          $sql.=" WHERE ((PDR.PDR_CODPTT='L') AND (A.PG_ATIVO='S'))";
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          $tblFp=$retCls["dados"];
          
          $retorno='[{"retorno":"OK"
                     ,"tblFp":'.json_encode($tblFp).'                     
                     ,"erro":""}]'; 
        };  
      }; 
        ///////////////////////////////////////////////////////////////////
        // Atualizando o banco de dados se opcao de insert/updade/delete //
        ///////////////////////////////////////////////////////////////////
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
    } catch(Exception $e ){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]';
  };    
  echo $retorno;
  exit;
};  

  

?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/css2017.css">  
<link rel="stylesheet" href="css/timeline.css">
</head>
<header>
  <div class="cabeçalho" style="display: inline-flex;">
    <img src="imagens/logoMaior.png">
    <h1> Acompanhamento de oportunidade</h1>
    <button class="botaoImagemSup-icon-big" onclick="closeClick()" style="float: right;"> Fechar </button>
  </div>
</header>
<body>
  <input type="button" value="Generate Timeline" onclick="CreateTimeline()"></button>
  <div class="timeline" id="timeline">

  </div>   
</body>

<script>
   document.addEventListener("DOMContentLoaded", function(){
      buscaPadrao();
      pega=JSON.parse(localStorage.getItem("addInd")).lote[0];
    });
    function buscaPadrao(){  
      clsJs   = jsString("lote");  
      clsJs.add("rotina"      , "buscapadrao"       );
      clsJs.add("login"       , jsPub[0].usr_login  );
      fd = new FormData();
      fd.append("fupoportunidade" , clsJs.fim());
      msg     = requestPedido("Trac_FupOportunidade.php",fd); 
      retPhp  = JSON.parse(msg);
      if( retPhp[0].retorno == "OK" ){
        objGlobal.tblFp=retPhp[0]["tblFp"];
      }
    };

    function CreateTimeline() {
      console.log(pega);
      var eDiv = document.createElement('div');
      var dDiv = document.createElement('div');
      eDiv.classList.add('container');
      eDiv.classList.add('left');
      dDiv.classList.add('container');
      dDiv.classList.add('right');
      var iDiv = document.createElement('div');
      iDiv.classList.add('content');
      var h2 = document.createElement('h2');
      var tml = document.getElementById('timeline');
      var text = document.createTextNode('container esquerdo');
      var text2 = document.createTextNode('container direito');

      if(document.getElementById('timeline').lastElementChild == null){
        tml.appendChild(eDiv);
        eDiv.appendChild(iDiv);
        iDiv.appendChild(h2);
        h2.appendChild(text);
      }
      else if(document.getElementById('timeline').lastElementChild.className === 'container left' ){
        tml.appendChild(dDiv);
        dDiv.appendChild(iDiv);
        iDiv.appendChild(h2);
        h2.appendChild(text2);
      }else if(document.getElementById('timeline').lastElementChild.className === 'container right' ) {
        tml.appendChild(eDiv);
        eDiv.appendChild(iDiv);
        iDiv.appendChild(h2);
        h2.appendChild(text);
      }
    }
      function closeClick(){
        window.close();
      }
</script>
</html>
