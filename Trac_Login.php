<?php
  session_start();
  require("classPhp/validaJson.class.php");
  require("classPhp/conectaSqlServer.class.php");
  //require("classPhp/enviarEmail.class.php");
  //require("classPhp/validaJson.class.php");
  if( isset($_POST["login"]) ){
    $vldr = new validaJson();          
    $retorno  = "";
    $retCls   = $vldr->validarJs($_POST["login"]);
    if($retCls["retorno"] != "OK"){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
    } else { 
      ////////////////////////////////////////////////////////////////////////
      // Excluindo a SESSION devido multi-banco se logar em varias empresas //
      ////////////////////////////////////////////////////////////////////////
      unset($_SESSION['pathBD']);
      //
      $classe   = new conectaBd();
      $jsonObj  = $retCls["dados"];
      $lote     = $jsonObj->lote;      
      $classe->conecta($lote[0]->login);
      $classe->msg("NAO LOCALIZADO SENHA/USUARIO ".$lote[0]->usuario." PARA EMPRESA ".$lote[0]->login);
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Pegando qtas filiais existem cadastradas pois na maioria será uma, esta desabilita/habilita o campo codfil no front //
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //$sql = "SELECT COUNT(FIL_CODIGO) AS CODIGO FROM TFILIAL WHERE FIL_ATIVO='S'"; 
      //$retCls     = $classe->selectAssoc($sql);
      //$qtosCodFil = $retCls["dados"][0]["CODIGO"]; 
      //
      ////////////////////////////////////////////////
      // Pegando todos usuarios para montar SESSION //
      ////////////////////////////////////////////////
      $sql = "SELECT A.USR_CODIGO
                    ,A.USR_CPF
                    ,A.USR_APELIDO
                    ,A.USR_ADMPUB
                    ,A.USR_EMAIL
                    ,CONVERT(VARCHAR(10),A.USR_VENCTO,127) AS USR_VENCTO
                    ,A.USR_PRIMEIROACESSO
                    ,P.UP_D01
                    ,P.UP_D02
                    ,P.UP_D03
                    ,P.UP_D04
                    ,P.UP_D05
                    ,P.UP_D06
                    ,P.UP_D07
                    ,P.UP_D08
                    ,P.UP_D09
                    ,P.UP_D10
                    ,P.UP_D11
                    ,P.UP_D12
                    ,P.UP_D13
                    ,P.UP_D14
                    ,P.UP_D15
                    ,P.UP_D16
                    ,P.UP_D17
                    ,P.UP_D18
                    ,P.UP_D19
                    ,P.UP_D20
                    ,P.UP_D21
                    ,P.UP_D22
                    ,P.UP_D23
                    ,P.UP_D24
                    ,P.UP_D25
                    ,P.UP_D26
                    ,P.UP_D27
                    ,P.UP_D28
                    ,P.UP_D29
                    ,P.UP_D30
                    ,P.UP_D31
                    ,P.UP_D32
                    ,P.UP_D33
                    ,P.UP_D34
                    ,P.UP_D35
                    ,P.UP_D36
                    ,P.UP_D37
                    ,P.UP_D38
                    ,P.UP_D39
                    ,P.UP_D40
                    ,P.UP_D41
                    ,P.UP_D42
                    ,P.UP_D43
                    ,P.UP_D44
                    ,P.UP_D45
                    ,P.UP_D46
                    ,P.UP_D47
                    ,P.UP_D48
                    ,P.UP_D49
                    ,P.UP_D50
                    ,EMP.EMP_CODIGO
                    ,EMP.EMP_APELIDO
                    ,EMP.EMP_CODETF
                    ,CDD.CDD_CODEST
                    ,CDD.CDD_CODIGO
                    ,CDD.CDD_NOME
                    ,COALESCE(BNC.BNC_CODIGO,0) AS BNC_CODIGO
                    ,COALESCE(BNC.BNC_NOME,'NSA') AS BNC_NOME
             FROM USUARIO A
             LEFT OUTER JOIN USUARIOPERFIL P ON A.USR_CODUP=P.UP_CODIGO 
             LEFT OUTER JOIN USUARIOEMPRESA UE ON A.USR_CODIGO=UE.UE_CODUSR AND UE.UE_ATIVO='S'
             LEFT OUTER JOIN EMPRESA EMP ON UE.UE_CODEMP=EMP.EMP_CODIGO AND EMP.EMP_ATIVO='S'
             LEFT OUTER JOIN CIDADE CDD ON EMP.EMP_CODCDD=CDD.CDD_CODIGO
             LEFT OUTER JOIN BANCO BNC ON EMP.EMP_CODIGO=BNC.BNC_CODEMP AND BNC.BNC_ATIVO='S' AND BNC.BNC_PADRAOFLUXO='S'             
            WHERE A.USR_CPF='".$lote[0]->usuario."'
              AND A.USR_SENHA='".$lote[0]->senha."'
              AND A.USR_ATIVO='S'
              AND EMP.EMP_APELIDO='".$lote[0]->empresa."'"; 
      $classe->msgSelect(true);              
      $retCls=$classe->selectAssoc($sql);
      if( $retCls["retorno"] != "OK" ){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
      } else { 
        $retPhp=$retCls["dados"];
        /////////////////////////////////////////////////////////////////////////
        // Olhando se a data da senha nao expirou e tb se eh o primeiro acesso //
        // SN = Expirou mas naum eh primeiro acesso                            //
        // SS = Expirou e eh primeiro acesso                                   //
        /////////////////////////////////////////////////////////////////////////
        $_SESSION["usr_expirou"]="N";
        $dtSenha = $retPhp[0]["USR_VENCTO"];
        $dtHoje  = date('Y-m-d'); 
        if( strtotime($dtHoje) >= strtotime($dtSenha) ){
          $_SESSION["usr_expirou"]="SN";  
          if($retPhp[0]["USR_PRIMEIROACESSO"]=="S"){
            $_SESSION["usr_expirou"]="SS";    
          };
        };
        //////////////////////////////////////////////////////////////
        // se chegou aqui é por que a senha do usuário está correta //
        //////////////////////////////////////////////////////////////
        $_SESSION["usr_codigo"]       = $retPhp[0]["USR_CODIGO"];
        $_SESSION["usr_apelido"]      = $retPhp[0]["USR_APELIDO"];
        $_SESSION["usr_cpf"]          = $retPhp[0]["USR_CPF"];
        $_SESSION["usr_admpub"]       = $retPhp[0]["USR_ADMPUB"];
        $_SESSION["usr_vencto"]       = $retPhp[0]["USR_VENCTO"]; // Data da expiracao da senha
        $_SESSION["usr_email"]        = $retPhp[0]["USR_EMAIL"];
        $_SESSION["emp_codigo"]       = $retPhp[0]["EMP_CODIGO"];
        $_SESSION["emp_apelido"]      = $retPhp[0]["EMP_APELIDO"];
        $_SESSION["emp_codetf"]       = $retPhp[0]["EMP_CODETF"];
        $_SESSION["emp_codest"]       = $retPhp[0]["CDD_CODEST"];
        $_SESSION["emp_codcdd"]       = $retPhp[0]["CDD_CODIGO"];
        $_SESSION["emp_descdd"]       = $retPhp[0]["CDD_NOME"];
        $_SESSION["emp_codfll"]       = (($_SESSION["emp_codigo"]*1000)+1);
        $_SESSION["emp_fllunica"]     = "S";
        $_SESSION["emp_codbnc"]       = $retPhp[0]["BNC_CODIGO"];        
        $_SESSION["emp_desbnc"]       = $retPhp[0]["BNC_NOME"];
        $sql = "SELECT COUNT(FLL_CODIGO) AS QTAS FROM FILIAL WHERE ((FLL_CODEMP=".$_SESSION["emp_codigo"].") AND (FLL_ATIVO='S'))";
        $retCls=$classe->selectAssoc($sql);
        if( $retCls["qtos"]>1 ){        
          $_SESSION["emp_fllunica"]="N";
        };  
        ////////////////////////////////////////////////////////
        // VENDO QUAL NAVEGADOR PARA SER USADO NO JAVASCRIPT  //
        ////////////////////////////////////////////////////////
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $navegador="OUTRO";
        if( (preg_match('/MSIE/i',$u_agent)) && (!preg_match('/Opera/i',$u_agent))){
          $navegador = "IE";
        } else if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)) {
          $navegador = "IE";            
        }elseif(preg_match('/Firefox/i',$u_agent)){
          $navegador = "FIREFOX";
        } elseif(preg_match('/Chrome/i',$u_agent)){
          $navegador = "CHROME";
        } elseif(preg_match('/AppleWebKit/i',$u_agent)){
          $navegador = "OPERA";
        } elseif(preg_match('/Safari/i',$u_agent)){
          $navegador = "SAFARI";
        } elseif(preg_match('/Netscape/i',$u_agent)){
          $navegador = "NETSCAPE";
        }
        ////////////////////////////////////////////////////
        // CRIANDO UM OBJETO PARA SER USADO NO JAVASCRIPT //
        // Os que se repetem é devido banco de dados      //
        ////////////////////////////////////////////////////
        $str =
         '[{
             "usr_apelido"         :"'.$retPhp[0]["USR_APELIDO"].'"
            ,"usr_codigo"          :"'.str_pad($retPhp[0]["USR_CODIGO"], 4, "0", STR_PAD_LEFT).'"
            ,"usr_admpub"          :"'.$retPhp[0]["USR_ADMPUB"].'"
            ,"usr_email"           :"'.$retPhp[0]["USR_EMAIL"].'"
            ,"usr_vencto"          :"'.$retPhp[0]["USR_VENCTO"].'"
            ,"usr_expirou"         :"'.$_SESSION["usr_expirou"].'"
            ,"usr_login"           :"'.$_SESSION["login"].'"
            ,"emp_codigo"          :"'.$_SESSION["emp_codigo"].'"
            ,"emp_apelido"         :"'.$_SESSION["emp_apelido"].'"
            ,"emp_codest"          :"'.$_SESSION["emp_codest"].'"
            ,"emp_codcdd"          :"'.$_SESSION["emp_codcdd"].'"
            ,"emp_descdd"          :"'.$_SESSION["emp_descdd"].'"
            ,"emp_codfll"          :"'.$_SESSION["emp_codfll"].'"            
            ,"emp_fllunica"        :"'.$_SESSION["emp_fllunica"].'"                        
            ,"emp_codbnc"          :"'.$_SESSION["emp_codbnc"].'"
            ,"emp_desbnc"          :"'.$_SESSION["emp_desbnc"].'"
            ,"emp_codetf"          :"'.$_SESSION["emp_codetf"].'"
            ,"usr_d01"             :"'.$retPhp[0]["UP_D01"].'"
            ,"usr_d02"             :"'.$retPhp[0]["UP_D02"].'"
            ,"usr_d03"             :"'.$retPhp[0]["UP_D03"].'"
            ,"usr_d04"             :"'.$retPhp[0]["UP_D04"].'"
            ,"usr_d05"             :"'.$retPhp[0]["UP_D05"].'"
            ,"usr_d06"             :"'.$retPhp[0]["UP_D06"].'"
            ,"usr_d07"             :"'.$retPhp[0]["UP_D07"].'"
            ,"usr_d08"             :"'.$retPhp[0]["UP_D08"].'"
            ,"usr_d09"             :"'.$retPhp[0]["UP_D09"].'"
            ,"usr_d10"             :"'.$retPhp[0]["UP_D10"].'"
            ,"usr_d11"             :"'.$retPhp[0]["UP_D11"].'"
            ,"usr_d12"             :"'.$retPhp[0]["UP_D12"].'"
            ,"usr_d13"             :"'.$retPhp[0]["UP_D13"].'"
            ,"usr_d14"             :"'.$retPhp[0]["UP_D14"].'"
            ,"usr_d15"             :"'.$retPhp[0]["UP_D15"].'"
            ,"usr_d16"             :"'.$retPhp[0]["UP_D16"].'"
            ,"usr_d17"             :"'.$retPhp[0]["UP_D17"].'"
            ,"usr_d18"             :"'.$retPhp[0]["UP_D18"].'"
            ,"usr_d19"             :"'.$retPhp[0]["UP_D19"].'"
            ,"usr_d20"             :"'.$retPhp[0]["UP_D20"].'"
            ,"usr_d21"             :"'.$retPhp[0]["UP_D21"].'"
            ,"usr_d22"             :"'.$retPhp[0]["UP_D22"].'"
            ,"usr_d23"             :"'.$retPhp[0]["UP_D23"].'"
            ,"usr_d24"             :"'.$retPhp[0]["UP_D24"].'"
            ,"usr_d25"             :"'.$retPhp[0]["UP_D25"].'"
            ,"usr_d26"             :"'.$retPhp[0]["UP_D26"].'"
            ,"usr_d27"             :"'.$retPhp[0]["UP_D27"].'"
            ,"usr_d28"             :"'.$retPhp[0]["UP_D28"].'"
            ,"usr_d29"             :"'.$retPhp[0]["UP_D29"].'"
            ,"usr_d30"             :"'.$retPhp[0]["UP_D30"].'"
            ,"usr_d31"             :"'.$retPhp[0]["UP_D31"].'"
            ,"usr_d32"             :"'.$retPhp[0]["UP_D32"].'"
            ,"usr_d33"             :"'.$retPhp[0]["UP_D33"].'"
            ,"usr_d34"             :"'.$retPhp[0]["UP_D34"].'"
            ,"usr_d35"             :"'.$retPhp[0]["UP_D35"].'"
            ,"usr_d36"             :"'.$retPhp[0]["UP_D36"].'"
            ,"usr_d37"             :"'.$retPhp[0]["UP_D37"].'"
            ,"usr_d38"             :"'.$retPhp[0]["UP_D38"].'"
            ,"usr_d39"             :"'.$retPhp[0]["UP_D39"].'"
            ,"usr_d40"             :"'.$retPhp[0]["UP_D40"].'"
            ,"usr_d41"             :"'.$retPhp[0]["UP_D41"].'"
            ,"usr_d42"             :"'.$retPhp[0]["UP_D42"].'"
            ,"usr_d43"             :"'.$retPhp[0]["UP_D43"].'"
            ,"usr_d44"             :"'.$retPhp[0]["UP_D44"].'"
            ,"usr_d45"             :"'.$retPhp[0]["UP_D45"].'"
            ,"usr_d46"             :"'.$retPhp[0]["UP_D46"].'"
            ,"usr_d47"             :"'.$retPhp[0]["UP_D47"].'"
            ,"usr_d48"             :"'.$retPhp[0]["UP_D48"].'"
            ,"usr_d49"             :"'.$retPhp[0]["UP_D49"].'"
            ,"usr_d50"             :"'.$retPhp[0]["UP_D50"].'"
            ,"navegador"           :"'.$navegador.'"    
            ,"DESUSU"              :"'.$retPhp[0]["USR_APELIDO"].'"
          }]';
        $str=str_replace("  ","",$str);    
        $retorno='[{"retorno":"OK","dados":'.str_replace(array("\r","\n"),'',$str).',"erro":""}]';
      };
    };  
    unset($classe,$jsonObj,$lote,$navegador,$retCls,$retPhp,$sql,$str,$u_agent);      
    echo $retorno;
    exit;
  }
?>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <!--<link rel="icon" type="image/png" href="imagens/logo_aba.png" />-->
    <title>Login</title>
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <!--<script src="js/jsTable2017.js"></script>-->
    <script language="javascript" type="text/javascript"></script>
    <style>
      .layout-boxed {
          background: url(imagens/boxed-bg.jpg) repeat fixed;
      }    
    </style>
    
    <script>
      "use strict";
      document.addEventListener("DOMContentLoaded", function(){ 
        document.getElementById('edtEmpresa').foco();      
      });
      var contMsg = 0;
      var clsArq  = ""; // Classe para gerar arquivo JSon para envio Php
      var mo  = {
        corpo     : ""
        ,fd       : ""
        ,from     : ""
        ,fromname : ""        
        ,jsPhp    : ""        
        ,msg      : ""
        ,subject  : ""    
      };
      ///////////////////////
      // Simulando o enter //
      ///////////////////////
      function tabenter(event){
        var tecla = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
        if (tecla==13) {
          loginClick();
        };
      };
      //
      //
      function loginClick(){
        var erro = new clsMensagem('Erro');
        erro.notNull( "EMPRESA"  , document.getElementById("edtEmpresa").value.toUpperCase() );
        erro.notNull( "USUARIO"  , document.getElementById("edtUsuario").value.toUpperCase() );
        erro.notNull( "SENHA"    , document.getElementById("edtSenha").value.toUpperCase()   );
        
        if( erro.ListaErr() != '' ){
          erro.Show();
        } else {
          try{
            document.getElementById("edtUsuario").value   = jsStr("edtUsuario").upper().alltrim().ret();
            document.getElementById("edtEmpresa").value   = jsStr("edtEmpresa").upper().alltrim().ret();            
            document.getElementById("edtSenha").value     = jsStr("edtSenha").upper().alltrim().ret();            
            /////////////////////////////////////////////////////////////////////////////////////////////////////
            // Usuario SISTEMA( USR_CODIGO=2) naum pode se logar no sistema, eh de uso exclusivo para importacoes
            /////////////////////////////////////////////////////////////////////////////////////////////////////
            if( document.getElementById("edtUsuario").value=="00000000000" )
              throw "USUARIO EXCLUSIVO PARA INTEGRAÇÕES!"; 
            /////////////////////////////////
            // Passando um JSON para o PHP //
            /////////////////////////////////
            clsArq=jsString("lote");
            clsArq.add("login"    , "TRAC"                                      );
            clsArq.add("empresa"  , document.getElementById("edtEmpresa").value );
            clsArq.add("usuario"  , document.getElementById("edtUsuario").value );
            clsArq.add("senha"    , document.getElementById("edtSenha").value   );
            mo.jsPhp=clsArq.fim();
            //
            mo.fd = new FormData();
            mo.fd.append("login",mo.jsPhp );
            mo.msg=requestPedido("Trac_Login.php",mo.fd); 
            var retPhp=JSON.parse(mo.msg);
            if( retPhp[0].retorno=="OK" ){
              localStorage.setItem("lsPublico",JSON.stringify(retPhp[0].dados));
              localStorage.setItem("lsPathPhp","phpSqlServer.php");
              window.location="Trac_Principal.php"; 
            }else{
              gerarMensagemErro("COB",retPhp[0].erro,"Erro");                
            };
          }catch(e){
            console.log(mo.msg);            
            gerarMensagemErro("catch",e.message,"Erro");
          };
        };
      };
    </script>
  </head>
  <!--<body class="layout-boxed" style="background-image: radial-gradient(mintcream 0%, gray 100%);">-->
  <body class="layout-boxed">  
  <div id="fundo">
    </div>
    <div class="divTelaCheia" id="telaCheia">
      <form method="post" 
            name="frmPai" 
            id="frmPai" 
            class="formulario center" 
            style="top: 15em; width:40em; position: absolute; z-index:30;display:block;">
        <p align="center">
        <img src="imagens/logoMaior.png" class="user-image" alt="Trac | Connect Plus">
        </p>
        <div style="height: 200px; overflow-y: auto;">
          <div class="campotexto campo100">
            <input class="campo_input input" id="edtEmpresa" type="text" value="TRACLOC" maxlength="15" />
            <label class="campo_label campo_required" for="edtEmpresa">Empresa:</label>
          </div>
          <div class="campotexto campo100">
            <input class="campo_input input" id="edtUsuario" value="00000000001" type="text" maxlength="11"/>
            <label class="campo_label campo_required" for="edtUsuario">CPF:</label>
			
          </div>
          <div class="campotexto campo100">
            <input type="password" class="campo_input input" id="edtSenha" 
                                                             onKeyUp="tabenter(event)"
                                                             value="ADMIN123" 
                                                             type="text" 
                                                             maxlength="15" />
            <label class="campo_label campo_required" for="edtSenha">Senha:</label>
          </div>
          <div class="campotexto campo100">
            <div onClick="loginClick();" class="btnImagemEsq bie30 bieAzul" style="float:right;"><i class="fa fa-key"> Entrar</i></div>          
          </div>
        </div>
      </form>    
    </div>    
  </body>
</html>