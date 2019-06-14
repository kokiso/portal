<?php
  session_start();
  require("php/class.phpmailer.php");
  require("php/class.smtp.php");
  require("classPhp/conectaSqlServer.class.php");

  if( isset($_POST['alterar']) ){
    $retorno="";
    $classe   = new conectaBd();
    $jsonObj  = json_decode($_POST['alterar']);
    ///////////////////////////////
    // Validando o json recebido //
    ///////////////////////////////
    if(json_last_error() != JSON_ERROR_NONE)
      $retorno='[{"retorno":"ERR","dados":"","erro":"FORMATO JSON INVALIDO!"}]';
    //
    if( $retorno == "" ){
      $lote     = $jsonObj->lote;
      $classe->conecta($lote[0]->login);
      $classe->msgSelect=false;      
      
      foreach ( $lote as $lt ){   
        $sql="SELECT USR_SENHA 
                FROM USUARIO 
               WHERE USR_CODIGO=".$lt->codusu." AND USR_SENHA='".$lt->oldsenha."'";
        $retCls=$classe->selectAssoc($sql);
        if( $retCls['retorno']=="OK" ){
          $arrUpdt=[];
          $sql = "UPDATE VUSUARIO
                    SET USR_SENHA='"  .$lt->newsenha."'
                        ,USR_PRIMEIROACESSO='N'
                  WHERE USR_CODIGO="  .$lt->codusu;
          array_push($arrUpdt,$sql);
          $retCls=$classe->cmd($arrUpdt);
          if( $retCls['retorno']=="OK" ){
            $retorno='[{"retorno":"OK","dados":"","erro":"SENHA ALTERADA COM SUCESSO!"}]';                        
          }else{
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
            break;  
          };
        } else {
          $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        }; 
      };    
    };      
    echo $retorno;
    exit;
  };  
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Connect Plus | Trac</title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- bootstrap nativo javascript -->
    <!--<link rel="stylesheet" href="css/bootstrapNative.css">-->
    <!--<script src="js/bootstrap-native.js"></script>-->
    <!-- bootstrap nativo javascript -->
    <script src="js/jsTable2017.js"></script>
    <script src="js/js2017.js"></script>    
    <link rel="stylesheet" href="css/meuBtn.css">        
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/font-awesome.css">
		<link rel="stylesheet" href="css/AdminLTE.css">
		<link rel="stylesheet" href="css/skin-black-light.css">
    
    <script src="tabelaTrac/f10/tabelaVendedorF10.js"></script>
    <script src="tabelaTrac/f10/tabelaPadraoF10.js"></script>        
    <script src="js/lerImportacaoExcel.js"></script>    
    <script src="js/js2017.js"></script>
    
		<link rel="stylesheet" href="css/iframe.css">
    <script>
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        document.getElementById("nomeUsuario").innerHTML="Usuário:"+jsPub[0].usr_apelido;
        document.getElementById("iframeComplementar").style.display="none";
      });  
      function ifAbrir(arquivo,direito,titulo){
        eval(" locDireito = "+direito);
        if( locDireito==0 ){
          gerarMensagemErro("login","Usuário não tem direito de consulta nesta rotina!","Aviso");
        } else {
          ///////////////////////////////////////////////////////////////////////////////
          // O direito 37 eh para alterar minha senha, tem que ser liberado para todos //
          // SS=Expirou e primeiro acesso  SN=Somente expirou                          //
          ///////////////////////////////////////////////////////////////////////////////
          if( ((jsPub[0].usr_expirou=="SS") || (jsPub[0].usr_expirou=="SN")) && (direito!="37") ){
            if( jsPub[0].usr_expirou=="SS" ){
              gerarMensagemErro("login","Para primeiro acesso favor alterar sua senha e logar novamente!","Aviso");  
            } else {  
              gerarMensagemErro("login","Senha expirada, favor alterar sua senha e logar novamente!","Aviso");
            };  
          } else {
            window.open(arquivo, "iframeCorpo"); 
            if( titulo != undefined ){
              document.getElementById("tituloMenu").innerHTML=titulo;
            };
            return false;
          };  
        };
      };
      var jsPub      = JSON.parse(localStorage.getItem("lsPublico"));
      var contMsg    = 0;            // contador para mensagens
      var locDireito = 0;
      ////////////////////////////////////////////////////////////////////////////////////
      // No ajuda para tipo de documento somente os tipos relacionados no campo TD_SERIENF
      ////////////////////////////////////////////////////////////////////////////////////
      function fncAbrirSerieNf(prodServ){
        localStorage.setItem("prodServ",prodServ);
        ifAbrir("Trac_SerieNf.php","jsPub[0].usr_d04","Meus serviços");
      };
    </script>  
	</head>
	<body class="hold-transition skin-black-light sidebar-mini">
		<div class="wrapper">
			<header class="main-header">
				<a href="index.php" class="logo">
					<!--<span class="logo-mini"><img src="imagens/logoMenor.png" class="user-image" alt="Logomarca Connect Plus"></span>-->
          
					<img src="imagens/logoMaior.png" class="user-image" style="background-size: 100% 100%;" alt="Logomarca Connect Plus">
				</a>
				<nav class="navbar navbar-static-top">
					<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
          <a href="#" class="sidebar-orlando"><div id="tituloMenu">Opção</div></a>
					<a href="#" onclick="altS();"
                      class="btn btn-primary btn-sm" 
                      style="margin-top: 5px; margin-right:10px;width:140px;height: 40px;padding:10px 10px 2px 2px;float:right">Alterar minha senha</a>

					<a id="nomeUsuario" href="#" class="btn btn-default btn-sm" 
                              style="margin-top: 5px; margin-right:10px;width:160px;height: 40px;padding:10px 10px 2px 2px;float:right">Usuario:</a>
					<a href="#" onclick="window.open('Trac_CpCr.php');"
                      class="btn btn-primary btn-sm" 
                      style="margin-top: 5px; margin-right:10px;width:140px;height: 40px;padding:10px 10px 2px 2px;float:right">CPCR</a>
					<a href="#" onclick="window.open('Trac_Nfs.php');"
                      class="btn btn-primary btn-sm" 
                      style="margin-top: 5px; margin-right:10px;width:140px;height: 40px;padding:10px 10px 2px 2px;float:right">NFS</a>
          <!--            
					<a href="#" onclick="window.open('Trac_Pedido.php');"
                      class="btn btn-primary btn-sm" 
                      style="margin-top: 5px; margin-right:10px;width:140px;height: 40px;padding:10px 10px 2px 2px;float:right">Pedido</a>
          -->            
					<a href="#" onclick="abrePedido();"
                      class="btn btn-primary btn-sm" 
                      style="margin-top: 5px; margin-right:10px;width:140px;height: 40px;padding:10px 10px 2px 2px;float:right">Pedido</a>
					<a href="#" onclick="abreContrato();"
                      class="btn btn-primary btn-sm" 
                      style="margin-top: 5px; margin-right:10px;width:140px;height: 40px;padding:10px 10px 2px 2px;float:right">Contrato</a>
					<a href="#" onclick="abreOrdemServico();"
                      class="btn btn-primary btn-sm" 
                      style="margin-top: 5px; margin-right:10px;width:140px;height: 40px;padding:10px 10px 2px 2px;float:right">OS</a>
                      
          <!--     
          <div class="dropdown">
            <button class="btn btn-primary" 
                    id="myDropdown" 
                    type="button" 
                    data-toggle="dropdown" 
                    aria-haspopup="true" 
                    aria-expanded="false">Dropdown trigger <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="myDropdown">
              <li role="presentation"><a role="menuitem" href="#">Action3</a></li>
              <li role="presentation"><a role="menuitem" href="#">Another action3</a></li>
              <li role="presentation"><a role="menuitem" href="#">Something else here3</a></li>
            </ul>
          </div>
          -->
               
                      
				</nav>
			</header>
			<aside class="main-sidebar">
				<section class="sidebar">
					<ul class="sidebar-menu" data-widget="tree">
            <!--
						<li class="treeview">
							<a href="#">
								<i class="fa fa-dashboard"></i> <span class="fontVermelho">...Dashboard</span>
								<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
							</a>
							<ul class="treeview-menu">
                <li><a href="#" onclick="window.open('Trac_XXX.php');"><i class="fa fa-circle-o"></i> Rota</a></li>
								<li><a href="#"><i class="fa fa-circle-o"></i> ...Dashboard v1</a></li>
								<li><a href="#"><i class="fa fa-circle-o"></i> ...Dashboard v2</a></li>
							</ul>
						</li>
            -->
						<li class="treeview">
              <!--
							<a href="#">
								<i class="fa fa-laptop"></i>
								<span>CADASTROS</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
              -->
							<a href="#">
								<i class="fa fa-files-o"></i>
								<span>CADASTROS</span>
								<span class="pull-right-container">
									<span class="label label-primary pull-right">7</span>
								</span>
							</a>
              
							<ul class="treeview-menu">
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Usuário do sistema</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_Usuario.php'         ,'jsPub[0].usr_d01','Usuarios');">        <i class="fa fa-circle-o"></i> Usuários ativos</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_UsuarioEmpresa.php'  ,'jsPub[0].usr_d02','Usuario/Operacao');"><i class="fa fa-circle-o"></i> Usuário->Empresa</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_UsuarioPerfil.php'   ,'jsPub[0].usr_d01','Perfil');">          <i class="fa fa-circle-o"></i> Perfil</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Cargo.php'           ,'jsPub[0].usr_d01','Cargo');">           <i class="fa fa-circle-o"></i> Cargo</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Email.php'           ,'jsPub[0].usr_d16','Email');">           <i class="fa fa-circle-o"></i> Email automático</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Empresa.php'         ,'jsPub[0].usr_d03','Empresa');">         <i class="fa fa-circle-o"></i> Empresa</a></li>                    
                    <li><a href="#" onclick="ifAbrir('Trac_Filial.php'          ,'jsPub[0].usr_d03','Filial');">          <i class="fa fa-circle-o"></i> Filial</a></li>                    
                    <li><a href="#" onclick="ifAbrir('Trac_Contador.php'        ,'jsPub[0].usr_d03','Contador');">        <i class="fa fa-circle-o"></i> Contador</a></li>                                        
                  </ul>
                </li> 
                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Administrativo</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_AgendaTarefa.php'  ,'jsPub[0].usr_d18','Tarefas');"><i class="fa fa-circle-o"></i> Tarefas</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Vendedor.php'      ,'jsPub[0].usr_d29','Vendedor');"><i class="fa fa-circle-o"></i> Vendedor</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Feriado.php'       ,'jsPub[0].usr_d19','Feriado');"><i class="fa fa-circle-o"></i> Feriado</a></li>
                  </ul>
                </li> 
                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>CNAB</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_CnabErro.php'      ,'jsPub[0].usr_d30','Erros');"><i class="fa fa-circle-o"></i> Erros</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_CnabInstrucao.php' ,'jsPub[0].usr_d30','Instrucao');"><i class="fa fa-circle-o"></i> instrução</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_CnabRetorno.php'   ,'jsPub[0].usr_d30','Retorno');"><i class="fa fa-circle-o"></i> Retorno</a></li>
                  </ul>
                </li> 

                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Localização</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_Pais.php'       ,'jsPub[0].usr_d08','Pais');"><i class="fa fa-circle-o"></i> Pais</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Regiao.php'     ,'jsPub[0].usr_d08','Regiao');"><i class="fa fa-circle-o"></i> Região</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Estado.php'     ,'jsPub[0].usr_d08','Estado');"><i class="fa fa-circle-o"></i> Uf</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Cidade.php'     ,'jsPub[0].usr_d08','Cidade');"><i class="fa fa-circle-o"></i> Cidade</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Logradouro.php' ,'jsPub[0].usr_d08','Logradouro');"><i class="fa fa-circle-o"></i> Logradouro</a></li>
                  </ul>
                </li> 
                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Parametro fiscal</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_Categoria.php'         ,'jsPub[0].usr_d12','Categoria');"><i class="fa fa-circle-o"></i> Categoria favorecido</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_EmpresaRamo.php'       ,'jsPub[0].usr_d25','Empresa_Ramo');"><i class="fa fa-circle-o"></i> Ramo da empresa</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_EmpresaRegTrib.php'    ,'jsPub[0].usr_d25','Regime tributário');"><i class="fa fa-circle-o"></i> Regime tributário</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_EmpresaTipo.php'       ,'jsPub[0].usr_d25','Tipo de empresa');"><i class="fa fa-circle-o"></i> Tipo da empresa</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_EmpresaTribFed.php'    ,'jsPub[0].usr_d25','Tributação federal');"><i class="fa fa-circle-o"></i> Tributação federal</a></li>
                  </ul>
                </li> 

                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Parametro financeiro</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_Banco.php'           ,'jsPub[0].usr_d06','Banco');">             <i class="fa fa-circle-o"></i> Banco</a></li>                  
                    <li><a href="#" onclick="ifAbrir('Trac_Competencia.php'     ,'jsPub[0].usr_d07','Competência');">       <i class="fa fa-circle-o"></i> Competência</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Favorecido.php'      ,'jsPub[0].usr_d20','Favorecido');">        <i class="fa fa-circle-o"></i> Favorecido</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_FavorecidoGrupo.php' ,'jsPub[0].usr_d20','Favorecido Grupo');"> <i class="fa fa-circle-o"></i> Favorecido_Grupo</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_FormaCobranca.php'   ,'jsPub[0].usr_d20','Forma cobrança');">    <i class="fa fa-circle-o"></i> Forma de cobrança</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_TipoDocumento.php'   ,'jsPub[0].usr_d20','Tipo documento');">    <i class="fa fa-circle-o"></i> Tipo de docto</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Transportadora.php'  ,'jsPub[0].usr_d33','Transportadora');">    <i class="fa fa-circle-o"></i> Transportadora</a></li>                    
                    
                    <li class="treeview">
                      <a href="#">
                        <i class="fa fa-users"></i> <span>Complemento</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                      </a>
                      <ul class="treeview-menu">
                        <li><a href="#" onclick="ifAbrir('Trac_BancoCodigo.php'     ,'jsPub[0].usr_d06','Codigo banco');">      <i class="fa fa-circle-o"></i> Codigo do banco</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_GrupoFavorecido.php' ,'jsPub[0].usr_d11','Grupo favorecido');">  <i class="fa fa-circle-o"></i> Grupo Favorecido</a></li>                                            
                        <li><a href="#" onclick="ifAbrir('Trac_Moeda.php'           ,'jsPub[0].usr_d08','Moeda');">             <i class="fa fa-circle-o"></i> Moeda</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_BancoStatus.php'     ,'jsPub[0].usr_d06','Status banco');">      <i class="fa fa-circle-o"></i> Status do banco</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_PagarTitulo.php'     ,'jsPub[0].usr_d25','Tipo imput');">        <i class="fa fa-circle-o"></i> Parametro de imput</a></li>                    
                        <li><a href="#" onclick="ifAbrir('Trac_PagarTipo.php'       ,'jsPub[0].usr_d25','Tipo titulo');">       <i class="fa fa-circle-o"></i> Parametro de tipo</a></li>                                        
                      </ul>
                    </li>
                  </ul>
                </li> 

                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Lançamento padrao</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_Padrao.php'        ,'jsPub[0].usr_d10','Padrao');">            <i class="fa fa-circle-o"></i> Padrao cabeçalho</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_PadraoGrupo.php'   ,'jsPub[0].usr_d10','Padrao grupo');">      <i class="fa fa-circle-o"></i> Padrao grupo</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_PadraoTitulo.php'  ,'jsPub[0].usr_d10','Padrao titulo');">     <i class="fa fa-circle-o"></i> Padrao titulo</a></li>
                  </ul>
                </li> 


                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Parametro contábil</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_Balanco.php'           ,'jsPub[0].usr_d13','Resumo');"><i class="fa fa-circle-o"></i> Conta balanço</a></li>                                    
                    <li><a href="#" onclick="ifAbrir('Trac_ContaContabil.php'     ,'jsPub[0].usr_d13','Resumo');"><i class="fa fa-circle-o"></i> Conta contabil</a></li>                  
                    <li><a href="#" onclick="ifAbrir('Trac_ContaResumo.php'       ,'jsPub[0].usr_d13','Resumo');"><i class="fa fa-circle-o"></i> Conta resumo</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_QualificacaoCont.php'  ,'jsPub[0].usr_d13','Resumo');"><i class="fa fa-circle-o"></i> Classif contador</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Sped.php'              ,'jsPub[0].usr_d12','Resumo');"><i class="fa fa-circle-o"></i> Classif sped</a></li>
                  </ul>
                </li> 

                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Serviço</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_AliquotaSimples.php'   ,'jsPub[0].usr_d17','AliquotaSimples');"><i class="fa fa-circle-o"></i> Aliquota simples</a></li>                                    
                    <li><a href="#" onclick="ifAbrir('Trac_ServicoPrefeitura.php' ,'jsPub[0].usr_d04','Código prefeitura');"><i class="fa fa-circle-o"></i> CNAE</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Servico.php'           ,'jsPub[0].usr_d04','Meus serviços');"><i class="fa fa-circle-o"></i> Meus serviços</a></li>
                    <li><a href="#" onclick="fncAbrirSerieNf('S');"><i class="fa fa-circle-o"></i> Talonário Serie NF </a></li>                    
                  </ul>
                </li> 
                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Produto</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <!--
                    <li><a href="#" onclick="ifAbrir('Trac_Cfop.php'              ,'jsPub[0].usr_d14','Cfop');"><i class="fa fa-circle-o"></i> CFOP</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_CstIcms.php'           ,'jsPub[0].usr_d14','CST Icms');"><i class="fa fa-circle-o"></i> CST-ICMS</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_CstIpi.php'            ,'jsPub[0].usr_d14','CST Ipi');"><i class="fa fa-circle-o"></i> CST-IPI</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_CstPis.php'            ,'jsPub[0].usr_d14','CST Pis');"><i class="fa fa-circle-o"></i> CST-PIS</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_CstSimples.php'        ,'jsPub[0].usr_d14','CST Simples');"><i class="fa fa-circle-o"></i> CST-SIMPLES</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Embalagem.php'         ,'jsPub[0].usr_d24','Embalagem');"><i class="fa fa-circle-o"></i> Embalagem->produto</a></li>
                    -->
                    <li><a href="#" onclick="ifAbrir('Trac_Produto.php'           ,'jsPub[0].usr_d14','Ncm');"><i class="fa fa-circle-o"></i> Produto</a></li>                    
                    <li><a href="#" onclick="ifAbrir('Trac_Ncm.php'               ,'jsPub[0].usr_d14','Ncm');"><i class="fa fa-circle-o"></i> NCM</a></li>
                    <!--
                    <li><a href="#" onclick="ifAbrir('Trac_ProdutoOrigem.php'     ,'jsPub[0].usr_d09','Origem do produto');"><i class="fa fa-circle-o"></i> Origem do produto</a></li>
                    -->
                    <li><a href="#" onclick="fncAbrirSerieNf('P');"><i class="fa fa-circle-o"></i> Talonário Serie NF </a></li>                    
                    <li><a href="#" onclick="ifAbrir('Trac_Imposto.php'           ,'jsPub[0].usr_d14','Imposto');"><i class="fa fa-circle-o"></i> Imposto</a></li>
                    
                    <li class="treeview">
                      <a href="#">
                        <i class="fa fa-users"></i> <span>Complemento</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                      </a>
                      <ul class="treeview-menu">
                        <li><a href="#" onclick="ifAbrir('Trac_Cfop.php'              ,'jsPub[0].usr_d14','Cfop');"><i class="fa fa-circle-o"></i> CFOP</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_CstIcms.php'           ,'jsPub[0].usr_d14','CST Icms');"><i class="fa fa-circle-o"></i> CST-ICMS</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_CstIpi.php'            ,'jsPub[0].usr_d14','CST Ipi');"><i class="fa fa-circle-o"></i> CST-IPI</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_CstPis.php'            ,'jsPub[0].usr_d14','CST Pis');"><i class="fa fa-circle-o"></i> CST-PIS</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_CstSimples.php'        ,'jsPub[0].usr_d14','CST Simples');"><i class="fa fa-circle-o"></i> CST-SIMPLES</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_Embalagem.php'         ,'jsPub[0].usr_d24','Embalagem');"><i class="fa fa-circle-o"></i> Embalagem->produto</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_NaturezaOperacao.php'  ,'jsPub[0].usr_d14','Natureza Operacao');"><i class="fa fa-circle-o"></i> Natureza da operação</a></li>                        
                        <li><a href="#" onclick="ifAbrir('Trac_ProdutoOrigem.php'     ,'jsPub[0].usr_d09','Origem do produto');"><i class="fa fa-circle-o"></i> Origem do produto</a></li>                        
                      </ul>
                    </li>
                  </ul>
                </li> 
                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Estoque</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_PontoEstoque.php'      ,'jsPub[0].usr_d33','Estoque');"><i class="fa fa-circle-o"></i> Ponto de estoque(PE)</a></li>                                    
                    <li><a href="#" onclick="ifAbrir('Trac_PontoEstoqueInd.php'   ,'jsPub[0].usr_d33','Estoque');"><i class="fa fa-circle-o"></i> Responsavel(PE)</a></li>                                    
                    <li><a href="#" onclick="ifAbrir('Trac_ContratoEndereco.php'  ,'jsPub[0].usr_d33','Estoque');"><i class="fa fa-circle-o"></i> Endereço</a></li>                     
                    <li><a href="#" onclick="ifAbrir('Trac_GrupoProduto.php'      ,'jsPub[0].usr_d33','Grupo');"><i class="fa fa-circle-o"></i> Grupo de produto</a></li>                                      
                    <li><a href="#" onclick="ifAbrir('Trac_Fabricante.php'        ,'jsPub[0].usr_d33','Fabricante');"><i class="fa fa-circle-o"></i> Fabricante</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_Operadora.php'         ,'jsPub[0].usr_d33','Operadora');"><i class="fa fa-circle-o"></i> Operadora</a></li>
                    <li><a href="#" onclick="ifAbrir('Trac_GrupoModelo.php'       ,'jsPub[0].usr_d35','Modelos');"><i class="fa fa-circle-o"></i> Modelo/Estoque</a></li>                    
                  </ul>
                </li> 
                
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-users"></i> <span>Auto</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="#" onclick="ifAbrir('Trac_AutoModelo.php'  ,'jsPub[0].usr_d35','Modelos');"><i class="fa fa-circle-o"></i> Modelo/Estoque</a></li>                    
                    <li><a href="#" onclick="ifAbrir('Trac_Veiculo.php'     ,'jsPub[0].usr_d35','Modelos');"><i class="fa fa-circle-o"></i> Veiculos</a></li>
                    
                    <li class="treeview">
                      <a href="#">
                        <i class="fa fa-users"></i> <span>Complemento</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                      </a>
                      <ul class="treeview-menu">
                        <li><a href="#" onclick="ifAbrir('Trac_VeiculoCor.php'        ,'jsPub[0].usr_d38','Cor');">         <i class="fa fa-circle-o"></i> Cor</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_VeiculoFabricante.php' ,'jsPub[0].usr_d38','Fabricante');">  <i class="fa fa-circle-o"></i> Fabricante</a></li>
                        <li><a href="#" onclick="ifAbrir('Trac_VeiculoTipo.php'       ,'jsPub[0].usr_d38','Tipo');">        <i class="fa fa-circle-o"></i> Tipo</a></li>                        
                        <li><a href="#" onclick="ifAbrir('Trac_VeiculoModelo.php'     ,'jsPub[0].usr_d38','Modelo');">      <i class="fa fa-circle-o"></i> Modelo</a></li>                        
                      </ul>
                    </li>
                    
                  </ul>
                </li> 
							</ul>
						</li>
					</ul>
				</section>
			</aside>			
			<div class="content-wrapper">
				<iframe src="" name="iframeCorpo" id="iframeCorpo"></iframe>
        <iframe src="" name="iframeComplementar" id="iframeComplementar"></iframe>
			</div>
			<!--
			<footer class="main-footer">
				<div class="pull-right hidden-xs">
					<b>Versão</b> 1.1.1
				</div>
				<strong>Ambiente Desenvolvimento</strong>
			</footer>	
			-->		
		</div>
		<script src="js/jquery.js"></script>
		<script src="js/adminlte.js"></script>
		<script>
			$(document).ready(function () {
				$('.sidebar-menu').tree()
			});
      function abrePedido(){
        if( jsPub[0].usr_d37==0 ){
        } else {  
          window.open('Trac_Pedido.php');
        };    
      };  
      //
      function abreContrato(){
        if( jsPub[0].usr_d40==0 ){
        } else {  
          window.open('Trac_Contrato.php');
        };    
      };  
      function abreOrdemServico(){
        if( jsPub[0].usr_d40==0 ){
        } else {  
          window.open('Trac_OrdemServico.php');
        };    
      };  
      //
      function altS(){
        let clsCode = new concatStr();  
        clsCode.concat("<div class='campotexto campo100' style='margin-top:20px;padding-left:5em;'>");        
        clsCode.concat(  "<div class='campotexto campo75' style=min-height:3.5em;'>");
        clsCode.concat(    "<input class='campo_input' id='oldSenha' type='password' maxlength=15 />");
        clsCode.concat(    "<label class='campo_label' style='font-size:1em; for='oldSenha'>Senha atual</label>");
        clsCode.concat(  "</div>");
        clsCode.concat(  "<div class='campotexto campo75' style=min-height:3.5em;'>");
        clsCode.concat(    "<input class='campo_input' id='newSenha' type='password' maxlength=15 />");
        clsCode.concat(    "<label class='campo_label' style='font-size:1em; for='newSenha'>Nova senha</label>");
        clsCode.concat(  "</div>");
        clsCode.concat(  "<div class='campotexto campo75' style=min-height:3.5em;'>");
        clsCode.concat(    "<input class='campo_input' id='conSenha' type='password' maxlength=15 />");
        clsCode.concat(    "<label class='campo_label' style='font-size:1em; for='conSenha'>Confirme</label>");
        clsCode.concat(  "</div>");
        clsCode.concat(  "<div class='campotexto campo75'>");        
        clsCode.concat(    "<div id='btnConfirmar' onClick='altSen();' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'>Ok</i></div>");        
        clsCode.concat(  "</div>");        
        clsCode.concat("</div>");
        janelaDialogo(
          { height          : "20em"
            ,body           : "16em"
            ,left           : "500px"
            ,top            : "60px"
            ,tituloBarra    : "Alterar senha"
            ,code           : clsCode.fim()
            ,width          : "30em"
            ,foco           : "oldSenha"
            ,fontSizeTitulo : "1.5em"           // padrao 2em que esta no css
          }
        );  
      };
      function altSen(){
        try{
          clsErro = new clsMensagem("Erro",100);
          var oldSenha = jsStr("oldSenha").upper().alltrim().ret();
          var newSenha = jsStr("newSenha").upper().alltrim().ret();
          var conSenha = jsStr("conSenha").upper().alltrim().ret(); 
          var codUsu   = jsPub[0].usr_codigo
          //  
          clsErro.tamMin("Nova senha",conSenha,6 );
          clsErro.tamMax("Nova senha",conSenha,15 ); 
          oldSenha=oldSenha.toUpperCase();            
          if( newSenha != conSenha )
            clsErro.add("CAMPO<b>SENHA</b>NOVA SENHA INVALIDA!");            
          if( oldSenha == newSenha.toUpperCase() )
            clsErro.add("CAMPO<b>SENHA</b>NOVA SENHA DEVE SER DIFERENTE DA ATUAL!");
          ///////////////////////////////////////////////////////////////////////////////////////////////
          // No cadastro e alteracao de usuario a senha aceita apenas alguns caracteres Atlas_Usuario.php
          // A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|0|1|2|3|4|5|6|7|8|9|
          ///////////////////////////////////////////////////////////////////////////////////////////////
          let tam     = newSenha.length;
          let numErr  = 0;
          for( let cntd=0; cntd<tam; cntd++){
            if( ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","0","1","2","3","4","5","6","7","8","9"].indexOf(newSenha[cntd]) == -1 )
              numErr++;
          };
          if( numErr>0 )    
            clsErro.add("CAMPO <b> SENHA </b>ACEITA APENAS CARACTERES E DIGITOS NUMERICOS!");
          
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Senha deve ter no minimo uma letra ou um número(StoredProcedure(VALIDAR_SENHA) olhando para essa regra) // 
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////
          let numInt=0;
          let numStr=0;
          for( var cntd=0;cntd<newSenha.length;cntd++){
            if( ["0","1","2","3","4","5","6","7","8","9"].indexOf(newSenha[cntd]) == -1 )
              numStr++;
            else
              numInt++;              
          };
          if( (numInt==0) || (numStr==0) ){
            clsErro.add("CAMPO<b>SENHA</b>OBRIGATORIO UM NUMERO OU UMA STRING NA NOVA SENHA!");
          };  
          //--
          if( clsErro.ListaErr() != '' )      //MUDAR DE == PARA != (TESTE DE TRIGGER)
            clsErro.Show();
          else {
            clsArq=jsString("lote");            
            clsArq.add("oldsenha" , oldSenha );
            clsArq.add("codusu"   , codUsu   );
            clsArq.add("newsenha" , newSenha );
            clsArq.add("consenha" , conSenha );
            clsArq.add("login"    , jsPub[0].usr_login  );            
            retPhp=clsArq.fim();
            var fd = new FormData();
            var retorno;
            fd.append("alterar",retPhp );
            retorno=requestPedido("Trac_Principal.php",fd); 
            retPhp=JSON.parse(retorno);
            if( retPhp[0].retorno=="OK" )
              janelaFechar(); 
            gerarMensagemErro("USU",retPhp[0].erro,{cabec:"Erro"});  
          };  
        }catch(e){
          gerarMensagemErro("catch",e.message,{cabec:"Erro"});
        };
      };
		</script>
	</body>
</html>