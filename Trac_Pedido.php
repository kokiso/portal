<?php
  session_start();
  if( isset($_POST["pedido"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJSon.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/dataCompetencia.class.php");      

      $clsCompet  = new dataCompetencia();    
      $vldr       = new validaJSon();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["pedido"]);
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
        
        if( $lote[0]->rotina=="emContrato" ){ 
          $contrato=$classe->generator("PAGAR");
          $sql="";
          $sql.="INSERT INTO VCONTRATO(CNTT_CODIGO";
          $sql.=",CNTT_TIPO";          
          $sql.=",CNTT_EMISSAO";          
          $sql.=",CNTT_ATIVO";
          $sql.=",CNTT_CODFVR";
          $sql.=",CNTT_CODVND";
          $sql.=",CNTT_CODIND";
          $sql.=",CNTT_VLRMENSAL";
          $sql.=",CNTT_VLRPONTUAL";
          $sql.=",CNTT_QTDAUTO";
          $sql.=",CNTT_FIDELIDADE";
          $sql.=",CNTT_INSTPROPRIA";
          $sql.=",CNTT_MESES";
          $sql.=",CNTT_DIA";          
          $sql.=",CNTT_CODBNC";
          $sql.=",CNTT_CODFC";
          $sql.=",CNTT_CODGM";    //Este campo eh acelerador para pegar valores para OSs          
          $sql.=",CNTT_CODLGN";   //Este campo eh acelerador para vendedor ver somente seus pedidos
          $sql.=",CNTT_LOCALINSTALA";          
          $sql.=",CNTT_CODUSR) VALUES(";
          $sql.="'$contrato'";                    // CNTT_CODIGO     
          $sql.=",'".$lote[0]->tipo."'";          // CNTT_TIPO
          $sql.=",'".date('m/d/Y')."'";           // CNTT_EMISSAO
          $sql.=",'S'";                           // CNTT_ATIVO // Angelo Kokiso, Contrato vir ativo a partir do momento que pedido for aprovado e assinado
          $sql.=",".$lote[0]->codfvr;             // CNTT_CODFVR
          $sql.=",".$lote[0]->codvnd;             // CNTT_CODVND
          $sql.=",".$lote[0]->codind;             // CNTT_CODIND
          $sql.=",".$lote[0]->vlrmensal;          // CNTT_VLRMENSAL
          $sql.=",".$lote[0]->vlrpontual;         // CNTT_VLRPONTUAL
          $sql.=",".$lote[0]->qtdauto;            // CNTT_QTDAUTO
          $sql.=",'".$lote[0]->fidelidade."'";    // CNTT_FIDELIDADE
          $sql.=",'".$lote[0]->instpropria."'";   // CNTT_INSTPROPRIA
          $sql.=",".$lote[0]->meses;              // CNTT_MESES
          $sql.=",".$lote[0]->dia;                // CNTT_DIA
          $sql.=",".$lote[0]->codbnc;             // CNTT_CODBNC
          $sql.=",'".$lote[0]->codfc."'";         // CNTT_CODFC
          $sql.=",".$lote[0]->cnttCodGm;          // CNTT_CODGM
          $sql.=",".$lote[0]->codlgn;             // CNTT_CODLGN
          $sql.=",'".$lote[0]->localinstala."'";  // CNTT_LOCALINSTALA
          $sql.=",".$_SESSION["usr_codigo"];      // CNTT_CODUSR
          $sql.=")";
          array_push($arrUpdt,$sql);
          if( $lote[0]->PLACA <> "[]" ){
            $objPlc=$lote[0]->PLACA;
            foreach ( $objPlc as $plc ){
              $sql="";
              $sql.="INSERT INTO VCONTRATOPLACA(CNTP_CODCNTT";
              $sql.=",CNTP_PLACACHASSI";
              $sql.=",CNTP_CODGMP"; 
              $sql.=",CNTP_CODUSR) VALUES(";                        
              $sql.="'$contrato'";                // CNTP_CODCNTT
              $sql.=",'" .$plc->placa."'";        // CNTP_PLACACHASSI
              $sql.=",0";                         // CNTP_CODGMP
              $sql.=",".$_SESSION["usr_codigo"];  // CNTP_CODUSR            
              $sql.=")";
              array_push($arrUpdt,$sql);            
            };  
          };  
          $cntpIdSrv = 0;          
          $objIte = $lote[0]->PRODUTO;
          foreach ( $objIte as $ite ){
            if( $ite->codgp=="AUT" ){
              $cntiIdUnico=$classe->generator("ITEMCONTRATO");    
              ////////////////////////////////////////////////////////////////
              // Fazendo o casamento entre CONTRATOPRODUTO com CONTRATOMENSAL
              ////////////////////////////////////////////////////////////////
              if( $ite->addCnts=="S" ){
                $cntpIdSrv=$cntiIdUnico;  
              };  
              $sql="";
              $sql.="INSERT INTO CONTRATOPRODUTO(CNTP_CODCNTT";
              $sql.=",CNTP_IDUNICO";
              $sql.=",CNTP_IDSRV";
              $sql.=",CNTP_CODGM";
              $sql.=",CNTP_CODSRV";
              $sql.=",CNTP_CODGP";
              $sql.=",CNTP_MENSAL";
              $sql.=",CNTP_CODGMP";
              $sql.=",CNTP_VALOR";
              $sql.=",CNTP_MODOENTREGA";              
              $sql.=",CNTP_STATUSENTREGA";              
              $sql.=",CNTP_CODENTREGA";
              $sql.=",CNTP_CODINSTALA";
              $sql.=",CNTP_AGENDADO";              
              $sql.=",CNTP_CODOS";                            
              $sql.=",CNTP_ACAO";
              $sql.=",CNTP_CODUSR) VALUES(";
              $sql.="'$contrato'";                // CNTP_CODCNTT  
              $sql.=",".$cntiIdUnico;             // CNTP_IDUNICO
              $sql.=",".$cntpIdSrv;               // CNTP_IDSRV
              $sql.=",".$ite->codgm;              // CNTP_CODGM
              $sql.=",".$ite->codsrv;             // CNTP_CODSRV
              $sql.=",'".$ite->codgp."'";         // CNTP_CODGP
              $sql.=",'".$ite->mensal."'";        // CNTP_MENSAL
              $sql.=",0";                         // CNTP_CODGMP
              $sql.=",".$ite->valor;              // CNTP_VALOR
              $sql.=",'COR'";                     // CNTP_MODOENTREGA
              $sql.=",'AGU'";                     // CNTP_STATUSENTREGA              
              $sql.=",0";                         // CNTP_CODENTREGA
              $sql.=",0";                         // CNTP_CODINSTALA            
              $sql.=",'N'";                       // CNTP_AGENDADO
              $sql.=",0";                         // CNTP_CODOS              
              $sql.=",0";                         // CNTP_ACAO
              $sql.=",".$_SESSION["usr_codigo"];  // CNTP_CODUSR  
              $sql.=")";
              array_push($arrUpdt,$sql);            
            }; 
            if( $ite->codgp=="SRV" ){
              if( $ite->addCnts=="S" ){  
                $sql="";
                $sql.="INSERT INTO CONTRATOMENSAL(CNTM_CODCNTT";
                $sql.=",CNTM_IDUNICO";
                $sql.=",CNTM_IDSRV";
                $sql.=",CNTM_CODGM";
                $sql.=",CNTM_CODSRV";
                $sql.=",CNTM_CODGP";
                $sql.=",CNTM_MENSAL";
                $sql.=",CNTM_CODGMP";
                $sql.=",CNTM_VALOR";
                $sql.=",CNTM_CODUSR) VALUES(";
                $sql.="'$contrato'";                // CNTS_CODCNTT  
                $sql.=",".$cntpIdSrv;               // CNTS_IDUNICO
                $sql.=",".$cntpIdSrv;               // CNTS_IDSRV
                $sql.=",".$ite->codgm;              // CNTS_CODGM
                $sql.=",".$ite->codsrv;             // CNTS_CODSRV
                $sql.=",'".$ite->codgp."'";         // CNTS_CODGP
                $sql.=",'".$ite->mensal."'";        // CNTS_MENSAL
                $sql.=",0";                         // CNTS_CODGMP
                $sql.=",".$ite->valor;              // CNTS_VALOR
                $sql.=",".$_SESSION["usr_codigo"];  // CNTS_CODUSR  
                $sql.=")";
                array_push($arrUpdt,$sql);            
              };
            };
          };
          //Angelo kokiso - Altera ativação do pedido para 'NAO' para criar efeito de DELETE ao gerar um contrato
          $sql="UPDATE PEDIDO SET PDD_CODUSR=".$_SESSION["usr_codigo"]." WHERE PDD_CODIGO=".$lote[0]->codpdd;
          array_push($arrUpdt,$sql);
          $sql="UPDATE PEDIDO SET PDD_ATIVO = 'N' WHERE PDD_CODIGO=".$lote[0]->codpdd;
          array_push($arrUpdt,$sql);  
          $atuBd  = true;
        };  
//file_put_contents("aaa.xml",print_r($arrUpdt,true));        
        //
        //
        if( $lote[0]->rotina=="atualizaStatus" ){          
          $sql="UPDATE PEDIDO SET PDD_STATUS=".$lote[0]->status.",PDD_CODUSR=".$_SESSION["usr_codigo"]." WHERE PDD_CODIGO=".$lote[0]->codpdd;
          array_push($arrUpdt,$sql);
          $atuBd  = true;
        };
        if( $lote[0]->rotina=="excluir" ){  
          $sql="UPDATE PEDIDO SET PDD_CODUSR=".$_SESSION["usr_codigo"]." WHERE PDD_CODIGO=".$lote[0]->codpdd;
          array_push($arrUpdt,$sql);
          $sql="DELETE FROM PEDIDO WHERE PDD_CODIGO=".$lote[0]->codpdd;
          array_push($arrUpdt,$sql);
          $atuBd  = true;
        };
        //
        //
        /////////////////////////
        // Filtrando os registros
        /////////////////////////
        if( $lote[0]->rotina=="filtrar" ){        
          $sql="";
          $sql.="SELECT A.PDD_CODIGO AS CODIGO";
          $sql.="       ,A.PDD_TIPO AS TP";          
          $sql.="       ,CONVERT(VARCHAR(10),A.PDD_EMISSAO,127) AS EMISSAO";                              
          $sql.="       ,A.PDD_CNPJCPF AS CNPJ";
          $sql.="       ,A.PDD_NOME AS NOME";
          $sql.="       ,A.PDD_CODVND AS VND";
          $sql.="       ,FVR.FVR_APELIDO AS VENDEDOR";          
          $sql.="       ,A.PDD_CODLGN AS CODLGN";          
          $sql.="       ,A.PDD_CODFVR AS CODFVR";
          $sql.="       ,A.PDD_VALOR AS VALOR";          
          $sql.="       ,A.PDD_QTDAUTO AS AUTOS";                    
          $sql.="       ,A.PDD_QTDPLACA AS PLACAS";          
          $sql.="       ,CASE WHEN A.PDD_FIDELIDADE='S' THEN 'SIM' ELSE 'NAO' END AS FID";          
          $sql.="       ,CASE WHEN A.PDD_INSTPROPRIA='S' THEN 'SIM' ELSE 'NAO' END AS IP";                    
          $sql.="       ,A.PDD_MESES AS MESES";
          $sql.="       ,A.PDD_DIA AS DIA";
          $sql.="       ,A.PDD_LOCALINSTALA AS LI";          
          $sql.="       ,CASE WHEN A.PDD_STATUS=1 THEN 'ORCAMENTO'";
          $sql.="             WHEN A.PDD_STATUS=2 THEN 'APROVAR'";
          $sql.="             WHEN A.PDD_STATUS=3 THEN 'REPROVADO'";   
          $sql.="             WHEN A.PDD_STATUS=4 THEN 'APROVADO'";
          $sql.="             WHEN A.PDD_STATUS=5 THEN 'ASSINAR'";
          $sql.="             WHEN A.PDD_STATUS=6 THEN 'ASSINADO'";   
          $sql.="             WHEN A.PDD_STATUS=7 THEN 'RECUSADO' END AS STT";          
          $sql.="       ,A.PDD_CODIND AS CODIND";          
          $sql.="       ,COALESCE(IND.FVR_APELIDO,'NSA') AS INDICADO";          
          $sql.="       ,A.PDD_OBS AS OBS";          
          $sql.="       ,CASE WHEN A.PDD_REG='P' THEN 'PUB' WHEN A.PDD_REG='S' THEN 'SIS' ELSE 'ADM' END AS REG";          
          $sql.="       ,US.US_APELIDO";          
          $sql.="       ,A.PDD_ITEM AS JSITEM";
          $sql.="       ,A.PDD_PARCELA AS JSPARCELA";
          $sql.="       ,A.PDD_PLACA AS JSPLACA";
          $sql.="  FROM PEDIDO A";           
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.PDD_CODVND=FVR.FVR_CODIGO";          
          $sql.="  LEFT OUTER JOIN FAVORECIDO IND ON A.PDD_CODIND=IND.FVR_CODIGO";                    
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON US.US_CODIGO = A.PDD_CODUSR"; 
          $sql.="  WHERE (A.PDD_ATIVO='S')"; 
          ///////////////////
          // coddir        //
          // 0-SEM DIREITO //
          // 1-DO VENDEDOR //
          // 4-TODOS       //
          ///////////////////
          if( $lote[0]->coddir == 1 ){
            $sql.=" AND (A.PDD_CODLGN=".$_SESSION["usr_codigo"].")";
          };  
          if( $lote[0]->status <> 0 ){
              $sql.=" AND (A.PDD_STATUS=".$lote[0]->status.")";  
          }; 
          $classe->msgSelect(false);
          $retCls=$classe->select($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM REGISTRO LOCALIZADO"}]';              
          } else { 
            $retorno='[{"retorno":"OK","dados":'.json_encode($retCls['dados']).',"erro":""}]'; 
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
    <title>Pedido</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="js/jsBiblioteca.js"></script>
    <script src="js/jsCopiaDoc2017.js"></script>            
    <script>      
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsPdd={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "CODIGO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "TP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "<b>V</b>enda, <b>L</b>ocação ou <b>D</b>emostração"                          
                      ,"popoverLabelCol": "Tipo"                      
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "EMISSAO"
                      ,"fieldType"      : "str"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "CNPJ"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "CLIENTE"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "20em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"truncate"       : true
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "VND"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "VENDEDOR"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "CODLGN"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "CODFVR"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "VALOR"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"sepMilhar"      : true                       
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "AUTOS"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "PLACAS"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "FID"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Fidelidade"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "IP"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "Instalação própria"                      
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "MESES"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":16 ,"labelCol"       : "DIA"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i2"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":17 ,"labelCol"       : "LI"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "15"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"popoverTitle"   : "<b>C</b>iente ou <b>I</b>nterna"                          
                      ,"popoverLabelCol": "Local Instalação"                      
                      ,"padrao":0}
            ,{"id":18 ,"labelCol"       : "STATUS"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "30"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":19 ,"labelCol"       : "CODIND"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i4"] 
                      ,"align"          : "center"    
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "N"
                      ,"ordenaColuna"   : "N"
                      ,"padrao":0}
            ,{"id":20 ,"labelCol"       : "INDICADO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":21 ,"labelCol"       : "OBS"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":22 ,"field"          : "REG"    
                      ,"labelCol"       : "REG"     
                      ,"obj"            : "cbReg"      
                      ,"lblDetalhe"     : "REGISTRO"     
                      ,"align"          : "center"
                      ,"tamGrd"         : "0em"                                            
                      ,"tamImp"         : "0" 
                      ,"ajudaDetalhe"   : "Se o registro é PUBlico/ADMinistrador ou do SIStema"                                         
                      ,"padrao":3}  
            ,{"id":23 ,"labelCol"       : "USUARIO"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"ordenaColuna"   : "S"
                      ,"padrao":0}
            ,{"id":24 ,"labelCol"       : "JSITEM"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":25 ,"labelCol"       : "JSPARCELA"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":26 ,"labelCol"       : "JSPLACA"
                      ,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
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
             {"texto":"Novo Pedido"   ,"name":"horNovoPdd"    ,"onClick":"7"  ,"enabled":true,"imagem":"fa fa-plus"             }
            ,{"texto":"Alterar"       ,"name":"horAlterarPdd" ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-pencil-square-o" }             
            ,{"texto":"Status"        ,"name":"horStatus"     ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-key"             }                         
            ,{"texto":"Excluir"       ,"name":"horExcluir"    ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-minus"           }            
            ,{"texto":"Copia pedido"  ,"name":"horCopiaPed"   ,"onClick":"7"  ,"enabled":true ,"imagem":"fa fa-print"           }                                    
            ,{"texto":"Fechar"        ,"name":"horFechar"     ,"onClick":"6"  ,"enabled":true ,"imagem":"fa fa-close"           }
          ] 
          ,"registros"      : []                        // Recebe um Json vindo da classe clsBancoDados
          ,"corLinha"       : "if(parseInt(ceTr.cells[8].innerHTML) == 0) {ceTr.cells[3].style.color='red';}"                
          ,"opcRegSeek"     : true                      // Opção para numero registros/botão/procurar  
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)                        
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmPdd"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaPdd"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmPdd"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnPdd"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblPdd"                  // Nome da table
          ,"prefixo"        : "Me"                      // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "VPEDIDO"                 // Nome da tabela no banco de dados  
          ,"tabelaBKP"      : "*"                       // Nome da tabela no banco de dados  
          ,"fieldAtivo"     : "*"                       // SE EXISITIR - Nome do campo ATIVO(S/N) na tabela BD
          ,"fieldReg"       : "*"                       // SE EXISITIR - Nome do campo SYS(P/A) na tabela BD            
          ,"fieldCodUsu"    : "*"                       // SE EXISITIR - Nome do campo CODIGO USUARIO na tabela BD                        
          ,"position"       : "absolute"
          ,"width"          : "135em"                   // Tamanho da table
          ,"height"         : "65em"                    // Altura da table
          ,"nChecks"        : false                     // Se permite multiplos registros na grade checados
          ,"tableLeft"      : "opc"                     // Se tiver menu esquerdo
          ,"relTitulo"      : "PEDIDO"                  // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "8"                       // Fonte do relatório
          ,"indiceTable"    : "CODIGO"                  // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objPdd === undefined ){  
          objPdd=new clsTable2017("objPdd");
        };  
        objPdd.montarHtmlCE2017(jsPdd); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar aqui e Trac_GrupoModeloInd.php
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsPdd,["CODIGO","CODLGN","DIA","EMISSAO","CNPJ","CLIENTE","LI","VND","VENDEDOR","CODFVR","VALOR","FID","MESES"
                                            ,"STATUS","REG"   ,"TP" ,"USUARIO","JSITEM","JSPARCELA","JSPLACA"]);
        btnFiltrarClick();
        ////////////////////////////////////////////////////////////////
        // Pegando a lista de filtro para troca de status conforme opcao
        ////////////////////////////////////////////////////////////////
        alteraLabelFiltro.call(document.getElementById('divStatus'),'data-status','Status | ','btnFiltrarClick()');
      });
      var objPdd;                     // Obrigatório para instanciar o JS TFormaCob
      var jsPdd;                      // Obj principal da classe clsTable2017
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
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d37);
      ///////////////////////////////////////////////
      // Filtrando os registros para grade de pedidos
      ///////////////////////////////////////////////
      function btnFiltrarClick() { 
        clsJs   = jsString("lote");  
        clsJs.add("rotina"      , "filtrar"                                     );
        clsJs.add("login"       , jsPub[0].usr_login                            );
        clsJs.add("coddir"      , intCodDir                                     );
        clsJs.add("status"      , $doc("cbStatus").getAttribute("data-indice")  );
        fd = new FormData();
        fd.append("pedido" , clsJs.fim());
        msg     = requestPedido("Trac_Pedido.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          //////////////////////////////////////////////////////////////////////////////////
          // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
          // Campo obrigatório se existir rotina de manutenção na table devido Json       //
          // Esta rotina não tem manutenção via classe clsTable2017                       //
          // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
          //////////////////////////////////////////////////////////////////////////////////
          jsPdd.registros=objPdd.addIdUnico(retPhp[0]["dados"]);
          objPdd.ordenaJSon(jsPdd.indiceTable,false);  
          objPdd.montarBody2017();
          $doc("spnTotPed").innerHTML=jsNmrs((retPhp[0]["dados"]).length).emZero(4).ret();
        };  
      };
      //////////////
      // Novo pedido
      //////////////
      function horNovoPddClick(){
        let objEnvio;          
        clsJs=jsString("lote");
        clsJs.add("codpdd",0);
        objEnvio=clsJs.fim();  
        localStorage.setItem("addAlt",objEnvio);
        window.open("Trac_PedidoCad.php");
      };
      /////////////////
      // Alterar pedido
      /////////////////
      function horAlterarPddClick(){
        try{
          chkds=objPdd.gerarJson("1").gerar();
          if( chkds[0].STATUS!="ORCAMENTO" )
            throw "Pedido "+chkds[0].CODIGO+" deve estar com status de ORCAMENTO para alteracao!";   
          
          let status=0;
          switch (chkds[0].STATUS) {
            case "ORCAMENTO"  : status=1; break;
            case "APROVAR"    : status=2; break;
            case "REPROVADO"  : status=3; break;
            case "APROVADO"   : status=4; break;
            case "ASSINAR"    : status=5; break;
            case "ASSINADO"   : status=6; break;
          };
          let objEnvio;          
          clsJs=jsString("lote");
          clsJs.add("codpdd"      , chkds[0].CODIGO               );
          clsJs.add("emissao"     , chkds[0].EMISSAO              );
          clsJs.add("codfvr"      , chkds[0].CODFVR               );          
          clsJs.add("cnpj"        , chkds[0].CNPJ                 );
          clsJs.add("desfvr"      , chkds[0].CLIENTE              );
          clsJs.add("codvnd"      , chkds[0].VND                  );
          clsJs.add("desvnd"      , chkds[0].VENDEDOR             );
          clsJs.add("codfvr"      , chkds[0].CODFVR               );
          clsJs.add("valor"       , chkds[0].VALOR                );
          clsJs.add("fidelidade"  , (chkds[0].FID).substring(0,1) );
          clsJs.add("instpropria" , (chkds[0].IP).substring(0,1)  );          
          clsJs.add("meses"       , chkds[0].MESES                );
          clsJs.add("dia"         , chkds[0].DIA                  );
          clsJs.add("localinstala", chkds[0].LI                   );          
          clsJs.add("status"      , status                        );
          clsJs.add("codind"      , chkds[0].CODIND               );  //Codigo de quem indicou
          clsJs.add("desind"      , chkds[0].INDICADO             );  //Nome de quem indicou
          clsJs.add("obs"         , chkds[0].OBS                  );
          clsJs.add("codlgn"      , chkds[0].CODLGN               );          
          clsJs.add("jsitem"      , chkds[0].JSITEM               );
          clsJs.add("jsparcela"   , chkds[0].JSPARCELA            );
          clsJs.add("jsplaca"     , chkds[0].JSPLACA              );          
          objEnvio=clsJs.fim();  
          localStorage.setItem("addAlt",objEnvio);
          window.open("Trac_PedidoCad.php");
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      function horExcluirClick(){
        try{
          chkds=objPdd.gerarJson("1").gerar();
          chkds.forEach(function(reg){
            if( reg.STATUS != "ORCAMENTO" )
              throw "Pedido "+reg.CODIGO+" deve estar com status ORCAMENTO!"; 
          });
          clsJs   = jsString("lote");  
          clsJs.add("rotina"      , "excluir"           );
          clsJs.add("login"       , jsPub[0].usr_login  );
          clsJs.add("codpdd"      , chkds[0].CODIGO     );
          fd = new FormData();
          fd.append("pedido" , clsJs.fim());
          msg     = requestPedido("Trac_Pedido.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            objPdd.apagaChecadosSoTable();             
          };  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ///////////////////////////////
      // Alterando o status do pedido
      ///////////////////////////////
      function horStatusClick(){
        try{
          chkds=objPdd.gerarJson("1").gerar();
          let clsCode = new concatStr();  
          clsCode.concat("<div id='dPaiChk' class='divContainerTable' style='height: 26.2em; width: 41em;border:none'>");
          clsCode.concat("<table id='tblChk' class='fpTable' style='width:100%;'>");
          clsCode.concat("  <thead class='fpThead'>");
          clsCode.concat("    <tr>");
          clsCode.concat("      <th class='fpTh' style='width:20%'>CODIGO</th>");
          clsCode.concat("      <th class='fpTh' style='width:60%'>DESCRICAO</th>");
          clsCode.concat("      <th class='fpTh' style='width:20%'>SIM</th>");        
          clsCode.concat("    </tr>");
          clsCode.concat("  </thead>");
          clsCode.concat("  <tbody id='tbody_tblChk'>");
          //////////////////////////////////////////
          // Preenchendo a table pelo direito D37 //
          // Dir 0-SEM DIREITO                    //
          //     1-DO VENDEDOR                    //
          //     4-TODOS                          //
          //////////////////////////////////////////
          let arr=[];
          arr.push({cod:"1",des:"ORCAMENTO" ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
          arr.push({cod:"2",des:"APROVAR"   ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
          if( intCodDir==4 ){          
            arr.push({cod:"3",des:"REPROVADO" ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
            //////////////////////////////////////////
            // Só entra aqui se puder aprovar o pedido
            //////////////////////////////////////////
            if( jsPub[0].usr_d39==4 ){
              arr.push({cod:"4",des:"APROVADO"  ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
            }  
            if( (chkds[0].STATUS=="APROVADO") || (chkds[0].STATUS=="ASSINAR") ){
              arr.push({cod:"5",des:"ASSINAR"   ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
              arr.push({cod:"6",des:"ASSINADO"  ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
            };
            arr.push({cod:"7",des:"RECUSADO"  ,sn:"N" ,fa:"fa fa-thumbs-o-down" ,cor:"red"});
          };
          //////////////////////////////////////////////////////////
          // Atualizando a grade sem a opcao em tela
          //////////////////////////////////////////////////////////
          arr.forEach( function(ar){
            if( ar.des==chkds[0].STATUS ){
              ar.sn   = "S";
              ar.fa   = "fa fa-thumbs-o-up";
              ar.cor  = "blue";
            }
          });
          arr.forEach(function(reg){
            clsCode.concat("    <tr class='fpBodyTr'>");
            clsCode.concat("      <td class='fpTd textoCentro'>"+reg.cod+"</td>");
            clsCode.concat("      <td class='fpTd'>"+reg.des+"</td>");
            clsCode.concat("      <td class='fpTd textoCentro'>");
            clsCode.concat("        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheck((elTr.rowIndex-1));'>");
            clsCode.concat("          <i id='img"+reg.cod+"' data-value='"+reg.sn+"' class='"+reg.fa+"' style='margin-left:10px;font-size:1.5em;color:"+reg.cor+";'></i>");
            clsCode.concat("        </div>");
            clsCode.concat("      </td>");
            clsCode.concat("    </tr>");
          });
          //////  
          // Fim
          //////
          clsCode.concat("  </tbody>");        
          clsCode.concat("</table>");
          clsCode.concat("</div>"); 
          clsCode.concat("<div id='btnConfirmar' onClick='fncJanelaRet();' class='btnImagemEsq bie15 bieAzul bieRight'><i class='fa fa-check'> Ok</i></div>");        
          janelaDialogo(
            { height          : "37em"
              ,body           : "16em"
              ,left           : "500px"
              ,top            : "60px"
              ,tituloBarra    : "Alterar status"
              ,code           : clsCode.fim()
              ,width          : "43em"
              ,fontSizeTitulo : "1.8em"           // padrao 2em que esta no css
            }
          );  
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      ///////////////////////////////////////////
      // Marcando e desmarcando os itens da table
      ///////////////////////////////////////////
      function fncCheck(pLin){
        let elImg;
        tblChk.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {  
          elImg = "img"+row.cells[0].innerHTML;
          if( indexTr==pLin ){
            jsCmpAtivo(elImg).remove("fa-thumbs-o-down").add("fa-thumbs-o-up").cor("blue");
            $doc("edtStatusTbl").value=row.cells[0].innerHTML+"|"+row.cells[1].innerHTML;   //Pegando o codigo e descricao para atualizar grade
          } else {
            jsCmpAtivo(elImg).remove("fa-thumbs-o-up").add("fa-thumbs-o-down").cor("red");
          };
        });
      };
      ///////////////////////////////////////
      // Atualizando o banco de dados e grade
      ///////////////////////////////////////
      function fncJanelaRet(){
        let splt=($doc("edtStatusTbl").value).split("|");    
        clsJs   = jsString("lote");  
        clsJs.add("rotina"  , "atualizaStatus"                );
        clsJs.add("login"   , jsPub[0].usr_login              );
        clsJs.add("status"  , jsNmrs(splt[0]).inteiro().ret() );    
        clsJs.add("codpdd"  , chkds[0].CODIGO                 );    
        fd = new FormData();
        fd.append("pedido" , clsJs.fim());
        msg     = requestPedido("Trac_Pedido.php",fd); 
        retPhp  = JSON.parse(msg);
        if( retPhp[0].retorno == "OK" ){
          tblPdd.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
            if( jsNmrs(row.cells[objCol.CODIGO].innerHTML).inteiro().ret()  == jsNmrs(chkds[0].CODIGO).inteiro().ret() ){
              row.cells[objCol.STATUS].innerHTML=splt[1];
              janelaFechar();
              return false;    
            }; 
          });    
        }; 
      };
      //////////////////
      // Copia de pedido
      //////////////////
      function horCopiaPedClick(){
        try{
          chkds=objPdd.gerarJson("1").gerar();
          ///////////////  
          // Parcelamento  
          ///////////////
          let vlrMensal   = 0;
          let vlrPontual  = 0;
          let parcela=JSON.parse((chkds[0].JSPARCELA).replaceAll('|','"')).parcela;
          parcela.forEach(function(reg,item){ 
            if( reg.pagto=="MENSAL" )
              vlrMensal+=jsNmrs(reg.valor).dec(2).dolar().ret();  
            if( reg.pagto=="PONTUAL" )
              vlrPontual+=jsNmrs(reg.valor).dec(2).dolar().ret();  
          });
          //////////////////////////////////////////////
          // INICIA A IMPRESSAO DA COPIA DE DOCUMENTO //
          //////////////////////////////////////////////
          let rel = new relatorio();
          rel.tamFonte(8);
          rel.orientacao("P");
          rel.iniciar();
          rel.traco();
          rel.pulaLinha(1);
          rel.corFundo("cinzaclaro",9,277);    
          rel.cell(12,"Pedido:"   ,{borda:0,negrito:true});
          rel.cell(55,chkds[0].CODIGO  ,{negrito:false,emZero:6});
          rel.cell(15,"Emissao:",{negrito:true});
          rel.cell(55,chkds[0].EMISSAO,{negrito:false});
          rel.cell(12,"Status:",{negrito:true});
          rel.cell(55,chkds[0].STATUS,{negrito:false});
          rel.cell(15,"Vendedor:",{negrito:true});
          rel.cell(55,chkds[0].VENDEDOR,{negrito:false});
          rel.pulaLinha(8);
          rel.traco();
          
          rel.cell(20,"CNPJ Cliente:" ,{borda:0,pulaLinha:1,negrito:true});
          rel.cell(47,chkds[0].CNPJ     ,{negrito:false});
          rel.cell(12,"Cliente:",{negrito:true});
          rel.cell(125,chkds[0].CLIENTE,{negrito:false}  );
          rel.cell(17,"Fidelidade:",{negrito:true});
          rel.cell(10,chkds[0].FID,{negrito:false}  );
          
          rel.cell(16,"Dia vencto:" ,{borda:0,pulaLinha:1,negrito:true,pulaLinha:5});
          rel.cell(10,chkds[0].DIA,{negrito:false});
          rel.cell(22,"Prazo contrato:" ,{negrito:true});
          rel.cell(25,chkds[0].MESES+" Meses" ,{negrito:false});
          rel.cell(20,"Valor total:",{negrito:true});
          rel.cell(30,(vlrMensal+vlrPontual),{negrito:false,moeda:true});
          rel.cell(22,"Valor Pontual:",{negrito:true,moeda:false});
          rel.cell(30,vlrPontual,{negrito:false,moeda:true});
          rel.cell(22,"Valor Mensal:",{negrito:true,moeda:false});
          rel.cell(30,vlrMensal,{negrito:false,moeda:true});
          
          rel.moeda(false);
          rel.pulaLinha(1);
          rel.align("L");
          rel.cell(275,"PRODUTO/SERVICO",{negrito:false,pulaLinha:11,align:"C"});        
          rel.retangulo(10,54,275  ,7 ,"DF"  );    
          rel.cell(100,"DESCRICAO" ,{pulaLinha:6,align:"L"});    
          rel.cell(10,"OBR");
          rel.cell(15,"PAGTO");
          rel.cell(10,"PRAZO");
          rel.cell(25,"UNITARIO",{align:"R"});
          rel.cell(25,"DESC");          
          rel.cell(10,"QTD",{align:"C"});
          rel.cell(25,"MENSAL",{align:"R"});
          rel.cell(25,"TOTAL",{align:"R"});
          rel.cell(15,"PLACAS",{align:"C"});
          /////////////////////////////////////////////////////////////////
          // Marcando a tela para poder incrementar a altura devido foreach
          /////////////////////////////////////////////////////////////////  
          rel.setAltura(62);   
          let item  = JSON.parse((chkds[0].JSITEM).replaceAll('|','"')).item;
          let soma  = 0;
          item.forEach(function(reg,item){
            rel.pulaLinha( (item==0 ? 6 : 4 ) );
            rel.cell(100,reg.descricao ,{align:"L"});    
            rel.cell(10,reg.obr);
            rel.cell(15,reg.pagto);            
            rel.cell(10,reg.prazo,{align:"C"});
            rel.cell(25,reg.unitario,{moeda:true,align:"R"});
            rel.cell(25,reg.desc,{moeda:true});
            rel.cell(10,reg.qtdade,{align:"C",moeda:false});            
            rel.cell(25,reg.mensal,{moeda:true,align:"R"});
            rel.cell(25,reg.total,{moeda:true});
            rel.cell(10,reg.placa,{align:"C",moeda:false});
            soma+=jsNmrs(reg.total).dolar().ret();

          });    
          ///////////////////////////////////////////////////////
          // Aqui tenho que tirar o ultimo pulaLinha da altura //
          ///////////////////////////////////////////////////////
          rel.linha(235,rel.getAltura(-2),254,rel.getAltura(-2));  
          rel.cell(100,"",{align:"L",pulaLinha:5});    
          rel.cell(120,"TOTAL");    
          rel.cell(25,soma,{moeda:true,align:"R"});        
          rel.moeda(false);
          rel.align("L");
          
          rel.cell(80,"DEMONSTRATIVO PARCELAMENTO",{negrito:false,pulaLinha:11,align:"C"});        
          rel.retangulo(10  ,rel.getAltura(-2),80  ,7 ,"DF"  );    
          rel.cell(10,"PARC" ,{pulaLinha:6,align:"L"});    
          rel.cell(20,"PAGTO");
          rel.cell(20,"VENCTO");
          rel.cell(20,"VALOR",{align:"R"});    

          parcela.forEach(function(reg,item){ 
            rel.pulaLinha( (item==0 ? 6 : 4 ) );
            rel.cell(10,reg.parcela,{emZero:2,moeda:false,align:"C"});    
            rel.cell(20,reg.pagto,{align:"L"});      
            rel.cell(20,reg.vencto);      
            rel.cell(20,reg.valor,{moeda:true,align:"R"});          
            if( reg.pagto=="MENSAL" )
              vlrMensal+=jsNmrs(reg.valor).dec(2).dolar().ret();  
            if( reg.pagto=="PONTUAL" )
              vlrPontual+=jsNmrs(reg.valor).dec(2).dolar().ret();  
          });
          rel.cell(80,"OBSERVACAO",{align:"L",negrito:true,pulaLinha:5,moeda:false}); 
          rel.multicell(true);
          rel.cellAltura(4);
          rel.cell(250,chkds[0].OBS,{negrito:false,pulaLinha:5,align:"J"});
          rel.multicell(false);
          // Monstrando as placas
          let placa = JSON.parse((chkds[0].JSPLACA).replaceAll('|','"')).placa;
          let strPlaca="";
          placa.forEach(function(reg,item){
            strPlaca+=reg.placa+", ";
          });
          if( strPlaca != "" ){
            strPlaca=strPlaca.slice(0,-2);
            rel.cell(80,"PLACA(s)",{align:"L",negrito:true,pulaLinha:5,moeda:false}); 
            rel.multicell(true);
            rel.cellAltura(4);
            rel.cell(250,strPlaca,{negrito:false,pulaLinha:4,align:"J"});
            rel.multicell(false);
          };
          let envPhp=rel.fim();
          ///////////////////////////////////////////////////
          // PREPARANDO UM FORMULARIO PARA VER O RELATORIO //
          ///////////////////////////////////////////////////
          var formreport = document.createElement("form")
          formreport.setAttribute("name", "relatorio");
          formreport.setAttribute("id", "relatorio");
          formreport.setAttribute("method", "post"); 
          formreport.setAttribute("target", "_blank"); 
          formreport.setAttribute("action", "classPhp/imprimirsql.php"); 
          var camposql = document.createElement('input');
          camposql.setAttribute('type','hidden');
          camposql.setAttribute('name','sql');
          camposql.setAttribute('id','sql');
          camposql.setAttribute('value',envPhp);
          formreport.appendChild(camposql);   
          document.body.appendChild(formreport);    
          formreport.submit();
          formreport.remove();    
        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };
      //////////////////////////////////////
      // Transformando um pedido em contrato
      //////////////////////////////////////
      function emContrato(){
        try{
          if( parseInt(jsPub[0].usr_d40)<2 )
            throw "USUARIO NÃO POSSUI DIREITO 40 PARA INCLUIR NA TABELA DE CONTRATO!";            
          chkds=objPdd.gerarJson("1").gerar();
          chkds.forEach(function(reg){
            if( reg.STATUS != "ASSINADO" )
              throw "Pedido "+reg.CODIGO+" deve estar com status ASSINADO!"; 
          });

          let placa=JSON.parse((chkds[0].JSPLACA).replaceAll('|','"')).placa;
          let clsPlc = jsString("placa");
          clsPlc.principal(false);
          placa.forEach(function(reg){
            clsPlc.add("placa"    , reg.placa   );
            clsPlc.add("codgmp"   , reg.produto  );
          });  
          placa = clsPlc.fim();
          //
          //
          let parcela=JSON.parse((chkds[0].JSPARCELA).replaceAll('|','"')).parcela;
          let vlrMensal   = 0;
          let vlrPontual  = 0;
          ///////////////////////////////////////
          // Preparando um JSON para envio ao Php
          ///////////////////////////////////////
          let clsDup = jsString("duplicata");
          clsDup.principal(false);
          let compet=0;
          let novaData;
          let dtInicio  = "";
          let dtFim     = "";
          let dataIniFim;
          //////////////////////////////////////////////
          // Procurando a competencia anterior do vencto
          //////////////////////////////////////////////
          parcela.forEach(function(reg){
            compet=jsDatas(reg.vencto).retYYYYMM();
            for( let lp=1;lp<=31;lp++ ){
              novaData=jsDatas(reg.vencto).retSomarDias((lp*-1)).retDDMMYYYY();
              if( compet != jsDatas(novaData).retYYYYMM() ){
                compet=jsDatas(novaData).retYYYYMM();
                //////////////////////////////////////
                // Buscando o inicio e fim de contrato
                //////////////////////////////////////
                dataIniFim="01/"+compet.substring(4,6)+"/"+compet.substring(0,4);  
                if( dtInicio=="" )
                  dtInicio=dataIniFim;
                dtFim=jsDatas(dataIniFim).retUltDiaMes();
                //
                break;
              };  
            };  
            
            clsDup.add("vencto"   , jsDatas(reg.vencto).retMMDDYYYY() );
            clsDup.add("codcmp"   , compet                            );
            clsDup.add("mensal"   , (reg.pagto).substring(0,1)        );
            clsDup.add("dias"     , 30                                );
            clsDup.add("valor"    , jsNmrs(reg.valor).dolar().ret()   );
            
            if( reg.pagto=="MENSAL" )
              vlrMensal+=jsNmrs(reg.valor).dec(2).dolar().ret();  
            if( reg.pagto=="PONTUAL" )
              vlrPontual+=jsNmrs(reg.valor).dec(2).dolar().ret();  
          });
          let duplicata = clsDup.fim();
          //
          //
          ////////////////////////////
          // Pegando os itens de venda
          ////////////////////////////
          let item        = JSON.parse((chkds[0].JSITEM).replaceAll('|','"')).item;
          let clsIte      = jsString("item");
          let pontual     = true;
          let mensal      = "";
          let quantidade  = 0;
          let addCnts     = "N";  // se insere em CONTRATOMENSAL( Apenas os servicos de um auto ) 
          let cnttCodGm   = 0;    // Preciso pegar o primeiro grupo modelo para cadastrar em contrato valores NOSHOW/INST/DESIST/MANUT/REVISAO  
          clsIte.principal(false);
          ///////////////////////////////////////////////////////////
          // Primeiro foreach pega somente a qtdade de itens vendidos
          ///////////////////////////////////////////////////////////
          item.forEach(function(qtd){
            if( qtd.codgp=="AUT" ){
              quantidade=parseInt(qtd.qtdade);
              addCnts="S";
              //////////////////////////////////////////////////////////////////////////////////////////////////////
              // Aqui vou cadastrar auto por auto pois preciso colocar a placa/num serie/codgmp(grupo modelo produto
              //////////////////////////////////////////////////////////////////////////////////////////////////////
              for( let lin=1;lin<=quantidade;lin++ ){
                item.forEach(function(reg){
                  reg.produto=parseInt(reg.produto);
                  reg.servico=parseInt(reg.servico);

                  clsIte.add("codgm"  , reg.produto                             );
                  clsIte.add("codsrv" , reg.servico                             );
                  clsIte.add("codgp"  , (reg.codgp=="***" ? "SRV" : reg.codgp)  );
                  clsIte.add("mensal" , (reg.pagto).substring(0,1)              );
                  clsIte.add("valor"  , jsNmrs(reg.mensal).dolar().ret()        );
                  clsIte.add("addCnts", addCnts                                 );
                  //////////////////////////////////////////////////////////////////
                  // Pegar o primeiro grupo para TRGViewCONTRATO_BI ON dbo.VCONTRATO
                  //////////////////////////////////////////////////////////////////
                  if( cnttCodGm==0 )
                    cnttCodGm=reg.produto;
                  //
                  //
                });  
                addCnts="N";
              };  
            };  
          });    
          let produto = clsIte.fim();
          clsJs   = jsString("lote");  
          clsJs.add("rotina"      , "emContrato"                    );
          clsJs.add("login"       , jsPub[0].usr_login              );
          clsJs.add("tipo"        , chkds[0].TP                     );          
          clsJs.add("dtinicio"    , jsDatas(dtInicio).retMMDDYYYY() );          
          clsJs.add("dtfim"       , jsDatas(dtFim).retMMDDYYYY()    );                    
          clsJs.add("codfvr"      , chkds[0].CODFVR                 );
          clsJs.add("codvnd"      , chkds[0].VND                    );
          clsJs.add("codind"      , chkds[0].CODIND                 );
          clsJs.add("cnttCodGm"   , cnttCodGm                       );
          clsJs.add("vlrmensal"   , vlrMensal                       );
          clsJs.add("vlrpontual"  , vlrPontual                      );
          clsJs.add("qtdauto"     , chkds[0].AUTOS                  );
          clsJs.add("fidelidade"  , (chkds[0].FID).substring(0,1)   );
          clsJs.add("instpropria" , (chkds[0].IP).substring(0,1)    );
          clsJs.add("meses"       , chkds[0].MESES                  );
          clsJs.add("dia"         , chkds[0].DIA                    );
          clsJs.add("localinstala", chkds[0].LI                     );          
          clsJs.add("codlgn"      , chkds[0].CODLGN                 );          
          clsJs.add("jsplaca"     , chkds[0].JSPLACA                );          
          clsJs.add("codbnc"      , jsPub[0].emp_codbnc             );
          clsJs.add("codfc"       , "BOL"                           );
          clsJs.add("DUPLICATA"   , duplicata                       );          
          clsJs.add("PRODUTO"     , produto                         );                    
          clsJs.add("PLACA"       , placa                           );
          clsJs.add("codpdd"      , chkds[0].CODIGO     );                              

//console.log(produto);
//return;

          fd = new FormData();
          fd.append("pedido" , clsJs.fim());
          msg     = requestPedido("Trac_Pedido.php",fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno != "OK" ){
            gerarMensagemErro("catch",retPhp[0].erro,"Erro");  
          } else {  
            alert('Contrato Gerado');
            window.close();
          };  

        }catch(e){
          gerarMensagemErro("catch",e,"Erro");
        };
      };  
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <aside class="indMasterBarraLateral">
        <section class="indBarraLateral">
          <div id="divBlRota" class="divBarraLateral primeiro" 
                              onClick="objPdd.imprimir();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Opção para impressão de item(s) em tela."><i class="indFa fa-print"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="objPdd.excel();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Gerar arquivo excel para item(s) em tela."><i class="indFa fa-file-excel-o"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="emContrato();"
                              data-dismissible="false" 
                              data-title="Pedido em contrato <span class='badge badge-alert'>ALERTA</span>"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Altera de forma definitiva pedido selecionado em um contrato."><i class="indFa fa-edit"></i>
          </div>
        </section>
      </aside>

      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">Pedido</span>
              <span id="spnTotPed" class="infoBoxNumber"></span>
            </div>
          </div>
        </div>        
        <div id="indDivInforme" class="indTopoInicio80" style="padding-top:2px;">
          <!----------------------------------------------------------------
          Para alterar o texto do filtro chamar a function alteraLabelFiltro
          ----------------------------------------------------------------->
          <section id="collapseSectionCombo" class="section-combo" data-tamanho="200" style="margin-top:-2px;">
          <div id="divStatus" class="btn-group" style="padding-top:8px;left:5px;float:left;">
            <button id="cbStatus" data-indice="0" type="button" class="btn btn-default disabled" style="width:160px;">Status | Todos</button>
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
              <li><a data-status="0">Todos</a></li>
              <li><a data-status="1">Orcamento</a></li>
              <li><a data-status="2">Aprovar</a></li>
              <li><a data-status="3">Reprovado</a></li>
              <li><a data-status="4">Aprovado</a></li>              
              <li><a data-status="5">Assinar</a></li>
              <li><a data-status="6">Assinado</a></li>              
              <li><a data-status="7">Recusado</a></li>              
            </ul>
          </div>
          </section>
        </div>  
      </div>
      <section>
        <section id="sctnPdd">
        </section>  
      </section>
      <form method="post"
            name="frmPdd"
            id="frmPdd"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/> 
        <!--
        Campos usados na pagina com funcao de variavel
        -->
        <div class="inactive">
          <input id="edtStatusTbl" value="*" type="text" />
        </div>
        
      </form>  
    </div>
    
  </body>
</html>