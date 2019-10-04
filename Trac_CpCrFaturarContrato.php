<?php
  session_start();
  if( isset($_POST["faturarcontrato"]) ){
    try{     
      require("classPhp/conectaSqlServer.class.php");
      require("classPhp/validaJson.class.php"); 
      require("classPhp/removeAcento.class.php"); 
      require("classPhp/validaCampo.class.php");      
      require("classPhp/selectRepetido.class.php");       						                        
      //require("classPhp/dataCompetencia.class.php");      

      //$clsCompet  = new dataCompetencia();    
      $vldr       = new validaJson();          
      $retorno    = "";
      $retCls     = $vldr->validarJs($_POST["faturarcontrato"]);
      ///////////////////////////////////////////////////////////////////////
      // Variavel mostra que não foi feito apenas selects mas atualizou BD //
      ///////////////////////////////////////////////////////////////////////
      $atuBd    = false;
      if($retCls["retorno"] != "OK"){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
        unset($retCls,$vldr);      
      } else {
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
        ////////////////////////////////////////////////
        //    Dados para JavaScript FATURAR CONTRATO  //
        ////////////////////////////////////////////////        
        if( $lote[0]->rotina=="selectFat" ){
          ////////////////////////////////////////////  
          // Calculando o vencimento/ultimo dia do mes
          ////////////////////////////////////////////
          $ano=(int)substr($lote[0]->codcmp,0,4);
          $mes=(int)substr($lote[0]->codcmp,4,2);
          $ultimoDiaMes=str_pad( $ano,4,"0",STR_PAD_LEFT)."-".str_pad( $mes,2,"0",STR_PAD_LEFT)."-01";
          if( $mes==12 ){
            $ano++;
            $mes=1;
          } else {
            $mes++;            
          };
          $venctoParte=str_pad( $ano,4,"0",STR_PAD_LEFT)."-".str_pad( $mes,2,"0",STR_PAD_LEFT)."-";
          //  
          //
$sql ="SELECT A.CNTC_CODCNTT";
          $sql.="       ,A.CNTC_CODGM";
          $sql.="       ,COALESCE(CNTP.CNTP_CODGMP,-1) AS CNTP_CODGMP";
          $sql.="       ,COALESCE(CNTP.CNTP_PLACACHASSI,'NSA0000') AS CNTP_PLACACHASSI";          
          $sql.="       ,GMP.GMP_NUMSERIE";
          $sql.="       ,A.CNTC_VLRMENSAL";
          $sql.="       ,COALESCE(CONVERT(VARCHAR(10),CNTP.CNTP_DTATIVACAO,103),'*') AS CNTP_DTATIVACAO";          
          $sql.="       ,CNTT.CNTT_MESES";
          $sql.="       ,CNTT.CNTT_DIA";
          $sql.="       ,CONVERT(VARCHAR(10),DateAdd(month, +(CNTT.CNTT_MESES-1), CNTT.CNTT_DTINICIO),103) AS ULTIMO_VENCTO";
          $sql.="       ,A.CNTC_CODSRV";
          $sql.="       ,SRV.SRV_NOME";
          $sql.="       ,SRV.SRV_CODPT";
          $sql.="       ,A.CNTC_PGTO";
          $sql.="       ,CNTT.CNTT_CODFVR";
          $sql.="       ,FVR.FVR_APELIDO";
          $sql.="       ,CNTT.CNTT_CODBNC";
          $sql.="       ,PT.PT_CODTD";
          $sql.="       ,PT.PT_CODFC";
          $sql.="       ,PT.PT_CODCC";
          $sql.="       ,CNTT.CNTT_CODEMP";
          $sql.="       ,COALESCE(EMP.EMP_APELIDO,'...') AS EMP_APELIDO";
          $sql.="       ,CNTT.CNTT_CODVND";
          $sql.="       ,VND.FVR_APELIDO AS VND_APELIDO";
          $sql.="       ,EMP.EMP_CODCDD";
          $sql.="       ,CNTT.CNTT_QTDAUTO";
          //////////////////////////////////////////////
          // Campos complementares para ajudar no filtro
          //////////////////////////////////////////////
          $sql.="       ,CAST(YEAR(CNTT.CNTT_DTINICIO) AS CHAR(4)) + RIGHT('0' + CAST(MONTH(CNTT.CNTT_DTINICIO) AS VARCHAR(2)),2) AS CMPINI";
          $sql.="       ,CAST(YEAR(DateAdd(month, +(CNTT.CNTT_MESES-1), CNTT.CNTT_DTINICIO)) AS CHAR(4))+RIGHT('0' + CAST(MONTH(DateAdd(month, +(CNTT.CNTT_MESES-1), CNTT.CNTT_DTINICIO)) AS VARCHAR(2)),2) AS CMPFIM";
          $sql.="       ,EOMONTH ( '".$ultimoDiaMes."' ) AS ULTIMODIAMES"; 
          $sql.="       ,COALESCE(datediff(day,CNTP.CNTP_DTATIVACAO,EOMONTH ( '".$ultimoDiaMes."' )),0) AS DIASINICIO";
          //
          //
          $sql.="  FROM CONTRATOCOBRANCA A";
          $sql.="  LEFT OUTER JOIN CONTRATO CNTT ON A.CNTC_CODCNTT=CNTT.CNTT_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON CNTT.CNTT_CODFVR=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN EMPRESA EMP ON CNTT.CNTT_CODEMP=EMP.EMP_CODIGO";
          $sql.="  LEFT OUTER JOIN SERVICO SRV ON A.CNTC_CODSRV=SRV.SRV_CODIGO";
          $sql.="  LEFT OUTER JOIN PADRAOTITULO PT ON SRV.SRV_CODPT=PT.PT_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO VND ON CNTT.CNTT_CODVND=VND.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN CONTRATOPRODUTO CNTP ON A.CNTC_CODCNTT=CNTP.CNTP_CODCNTT AND A.CNTC_CODGM=CNTP.CNTP_CODGM";
          $sql.="  LEFT OUTER JOIN GRUPOMODELOPRODUTO GMP ON CNTP.CNTP_CODGMP=GMP.GMP_CODIGO";
          $sql.="  WHERE CNTP.CNTP_DTATIVACAO IS NOT NULL";
          //$sql.="  WHERE A.CNTC_CODCNTT IN(15,69,82)";
          //$sql.="  WHERE A.CNTC_CODCNTT IN(125,126)";
          $sql.=" ORDER BY CNTP.CNTP_CODGMP,A.CNTC_CODSRV";
//file_put_contents("aaa.xml",$sql);          
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';  
          } else { 
						$tbl		  =	$retCls["dados"];
            $tblIte   = [];          
            $tblCntt  = [];
            $tblCabec = [];           
            foreach( $tbl as $ite ){
              if( ($lote[0]->codcmp>=$ite["CMPINI"]) and ($lote[0]->codcmp<=$ite["CMPFIM"]) ){
                $vencto     = $venctoParte.str_pad( $ite["CNTT_DIA"],2,"0",STR_PAD_LEFT);
                $indice     = $ite["CNTC_CODCNTT"]."_".$vencto;
                $vlrMensal  = $ite["CNTC_VLRMENSAL"];
                $vlrCobrado = $ite["CNTC_VLRMENSAL"];
                $dias       = $ite["DIASINICIO"];
                
                if( ($ite["CNTP_DTATIVACAO"] != "*") and ($ite["CNTP_PLACACHASSI"] != "NSA0000") ){
                  if( ($dias>0) and ($dias<30) ){
                    $vlrCobrado = round(( ($vlrCobrado / 30)*$dias ),2); 
                  };  
                } else {
                  $vlrCobrado = 0;   
                };
                
                array_push($tblIte,[
                   "cntc_codcntt"     =>  $ite["CNTC_CODCNTT"]            //codcntt              
                  ,"cntt_codfvr"      =>  $ite["CNTT_CODFVR"]             //codfvr
                  ,"fvr_apelido"      =>  $ite["FVR_APELIDO"]             //desfvr
                  ,"vencto"           =>  $vencto                         //vencto
                  ,"cntc_vlrmensal"   =>  $ite["CNTC_VLRMENSAL"]          //valor mensal do auto
                  ,"cntc_vlrcobrado"  =>  $vlrCobrado                     //valor cobrado se nao for mes cheio
                  ,"pt_codtd"         =>  $ite["PT_CODTD"]                //recibo ou nf( REC/NFS )
                  ,"cntt_codemp"      =>  $ite["CNTT_CODEMP"]             //codemp
                  ,"cntc_codfll"      =>  (($ite["CNTT_CODEMP"]*1000)+1)  //codfll(FILIAL)--------------------------------
                  ,"emp_apelido"      =>  $ite["EMP_APELIDO"]             //desemp
                  ,"cntc_codsrv"      =>  $ite["CNTC_CODSRV"]             //codsrv
                  ,"srv_nome"         =>  $ite["SRV_NOME"]                //dessrv
                  ,"srv_codpt"        =>  $ite["SRV_CODPT"]               //codpt(PADRAOTITULO)
                  ,"pt_codcc"         =>  $ite["PT_CODCC"]                //ptcodcc(PADRAOTITULO)
                  ,"cntt_codvnd"      =>  $ite["CNTT_CODVND"]             //codvnd(VENDEDOR)
                  ,"vnd_apelido"      =>  $ite["VND_APELIDO"]             //desvnd            
                  ,"emp_codcdd"       =>  $ite["EMP_CODCDD"]              //codcdd(CIDADE DA EMPRESA) 
                  ,"bnc_codigo"       =>  9                               //codbnc(BANCO->empresa)
                  ,"pt_codfc"         =>  $ite["PT_CODFC"]                //codfc(FORMACOBRANCA -> codpt)
                  ,"cntp_dtativacao"  =>  $ite["CNTP_DTATIVACAO"]         //data de ativacao
                  ,"cntp_codgmp"      =>  $ite["CNTP_CODGMP"]  
                  ,"cntp_placa"       =>  $ite["CNTP_PLACACHASSI"]
                  ,"dias_inicio"      =>  $ite["DIASINICIO"]              //Numero de dias da ativacao ateh o ultimo dia do mes de cobrança
                  ,"cntc_pgto"        =>  $ite["CNTC_PGTO"]               //Mensal ou pontual
                  ,"indice"           =>  $indice                         //lancto( num lancto apos faturar - sempre 0 )
                ]);	
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                // Aqui vou gerar um aviso de alerta ou erro na grade de contrato mostrando que existe algo inconsistente na grade inferior de autos
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $alerta="ok";
                if( $ite["CNTP_DTATIVACAO"]=="*" )
                  $alerta="alerta";
                if( $ite["CNTP_PLACACHASSI"]=="NSA0000" )
                  $alerta="erro";
                
                
                $achei=false;
                for( $lin=0;$lin<count( $tblCabec ); $lin++ ){
                  if( $tblCabec[ $lin ]["indice"] == $indice ){
                    $tblCabec[ $lin ]["cntc_vlrmensal"]+=$vlrMensal;
                    $tblCabec[ $lin ]["cntc_vlrcobrado"]+=$vlrCobrado;
                    
                    
                    if( $ite["CNTC_PGTO"]=="M" )
                      $tblCabec[ $lin ]["cntt_qtdautograde"]+=1;
                    
                    if( $alerta=="erro" )
                      $tblCabec[ $lin ]["check"]="erro";
                    if( ($alerta=="alerta") and ($tblCabec[ $lin ]["check"]=="ok") )
                      $tblCabec[ $lin ]["check"]="alerta";
                    
                    $achei=true;
                    break;  
                  };  
                };  
                //////////////////////////////////////////////////////////////////
                // Preenchendo o cabecalho de cada contrato( Resumo de cada auto )
                //////////////////////////////////////////////////////////////////
                if( $achei==false ){
                  array_push($tblCabec,[
                    "cntc_codcntt"        =>   $ite["CNTC_CODCNTT"]
                    ,"cntt_codfvr"        =>  $ite["CNTT_CODFVR"]
                    ,"fvr_apelido"        =>  $ite["FVR_APELIDO"]
                    ,"vencto"             =>  $vencto
                    ,"cntc_vlrmensal"     =>  $ite["CNTC_VLRMENSAL"]
                    ,"cntc_vlrcobrado"    =>  $vlrCobrado
                    ,"pt_codtd"           =>  $ite["PT_CODTD"]
                    ,"cntt_codemp"        =>  $ite["CNTT_CODEMP"]
                    ,"cntc_codfll"        =>  (($ite["CNTT_CODEMP"]*1000)+1)
                    ,"emp_apelido"        =>  $ite["EMP_APELIDO"]
                    ,"cntc_codvnd"        =>  $ite["CNTT_CODVND"]
                    ,"vnd_apelido"        =>  $ite["VND_APELIDO"]
                    ,"emp_codcdd"         =>  $ite["EMP_CODCDD"]
                    ,"bnc_codigo"         =>  9
                    ,"pt_codfc"           =>  $ite["PT_CODFC"]
                    ,"srv_codpt"          =>  $ite["SRV_CODPT"]
                    ,"indice"             =>  $indice
                    ,"pt_codcc"           =>  $ite["PT_CODCC"]
                    ,"cntc_codsrv"        =>  $ite["CNTC_CODSRV"]
                    ,0
                    ,"cntt_qtdauto"       =>  $ite["CNTT_QTDAUTO"]
                    ,"cntt_qtdautograde"  =>  1                             // Total de autos na grade inferior, tem que ser igual ao total de autos no contrato
                    ,"check"              =>  $alerta
                  ]);
                };  
              };
            }; 
            ////////////////////////////////////////////////
            // Preciso devolver para o JS um array nao assoc
            ////////////////////////////////////////////////
            foreach( $tblCabec as $cab ){
              if( ($cab["check"]=="ok") or ($cab["check"]=="alerta") ){
                if( $cab["cntt_qtdauto"] != $cab["cntt_qtdautograde"] )
                  $cab["check"]="erro";
              };  
              
              
              
              array_push($tblCntt,[
                 $cab["cntc_codcntt"]             // 00-codcntt              
                ,$cab["cntt_codfvr"]              // 01-codfvr
                ,$cab["fvr_apelido"]              // 02-desfvr
                ,$cab["vencto"]                   // 03-vencto
                ,$cab["cntc_vlrmensal"]           // 04-valor mensal
                ,$cab["cntc_vlrcobrado"]          // 05-valor cobrado
                ,$cab["pt_codtd"]                 // 06-recibo ou nf( REC/NFS )
                ,$cab["cntt_codemp"]              // 07-codemp
                ,$cab["cntc_codfll"]              // 08-codfll(FILIAL)
                ,$cab["emp_apelido"]              // 09-desemp
                ,$cab["cntc_codvnd"]              // 10-codvnd(VENDEDOR)
                ,$cab["vnd_apelido"]              // 11-desvnd            
                ,$cab["emp_codcdd"]               // 12-codcdd(CIDADE DA EMPRESA) 
                ,$cab["bnc_codigo"]               // 13-codbnc(BANCO->empresa)----------------------------------------------------------------------
                ,$cab["pt_codfc"]                 // 14-codfc(FORMACOBRANCA -> codpt)
                ,$cab["srv_codpt"]                // 15-codpt(PADRAOTITULO)                    
                ,$cab["indice"]                   // 16-indice
                ,$cab["pt_codcc"]                 // 17-codcc(PADRAOTITULO)  
                ,$cab["cntc_codsrv"]              // 18-codsrv                    
                ,0                                // 19-Lancto depois de gravado no banco de dados
                ,$cab["cntt_qtdauto"]             // 20-qtdade de autos no contrato
                ,$cab["cntt_qtdautograde"]        // 21-qtdade de autos na grade inferior
                ,$cab["check"]                    // 22
              ]);
            };  
          };  
          
          
          $retorno='[{"retorno":"OK"
                     ,"dados":'.json_encode($tblCntt).'
                     ,"item":'.json_encode($tblIte).'
                     ,"erro":""}]'; 
          unset($tblCntt,$tblIte);
				};
        ///////////////////////////////////////////////////////
        // Buscando a serie da NF e parametros complementares
        // Trac_NfsCadTitulo.php / Trac_CpCrFaturarContrato.php
        ///////////////////////////////////////////////////////
        if( $lote[0]->rotina=="buscaSnf" ){
          $sql ="SELECT A.SNF_CODIGO,A.SNF_NFPROXIMA,A.SNF_CODTD,TD.TD_NOME,A.SNF_ENTSAI,A.SNF_LIVRO";
          $sql.="  FROM SERIENF A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN TIPODOCUMENTO TD ON TD.TD_CODIGO=A.SNF_CODTD";
          $sql.=" WHERE ((A.SNF_ENTSAI='".$lote[0]->entsai."')"; 
          $sql.="   AND (A.SNF_CODTD='".$lote[0]->codtd."')";
          $sql.="   AND (A.SNF_CODFLL=".$lote[0]->codfll.")";
          $sql.="   AND (A.SNF_ATIVO='S'))"; 
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 ){
            $retorno='[{"retorno":"ERR","dados":"","erro":"NENHUM(a) '.$lote[0]->codtd.' DISPONIVEL PARA LANCAMENTO"}]';              
          } else {  
            $retorno='[{"retorno":"OK"
                       ,"tblSnf":'.json_encode($retCls["dados"][0]).'
                       ,"erro":""}]'; 
          };           
          unset($retCls,$sql);
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
    unset($retorno);
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
    <script src="tabelaTrac/f10/tabelaFormaCobrancaF10.js"></script>            
    <script>      
      "use strict";
      ////////////////////////////////////////////////
      // Executar o codigo após a pagina carregada  //
      ////////////////////////////////////////////////
      document.addEventListener("DOMContentLoaded", function(){ 
        $doc("spnEmpApelido").innerHTML=jsPub[0].emp_apelido;
        document.getElementById("edtDesCmp").value = jsDatas(0).retMMMbYY();
        validaCmp(document.getElementById("edtDesCmp").value);
        document.getElementById("edtDesCmp").focus();
        /////////////////////////////////////////////
        //     Objeto clsTable2017 MOVTOEVENTO     //
        /////////////////////////////////////////////
        jsFat={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"padrao":1} 
            ,{"id":1  ,"labelCol"       : "CONTRATO"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "6em"
                      ,"formato"        : ["i6"]
                      ,"tamImp"         : "15"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "CODFVR"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "CLIENTE"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "20"
                      ,"truncate"       : true                      
                      ,"excel"   : "S"
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "VENCTO"
                      ,"fieldType"      : "dat"                      
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "VLRMENSAL"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "VLRCOBRADO"
                      ,"fieldType"      : "flo2" 
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"excel"          : "S"
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "TD"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "20"
                      ,"excel"          : "S"
                      ,"align"          : "center"
                      ,"popoverTitle"   : "Tipo de documento( NFS ou REC )"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "CODEMP"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "CODFLL"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "EMPRESA"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "20"
                      ,"excel"   : "S"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "CODVND"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":12 ,"labelCol"       : "VENDEDOR"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"truncate"       : true
                      ,"tamImp"         : "20"
                      ,"excel"   : "S"
                      ,"padrao":0}
            ,{"id":13 ,"labelCol"       : "CODCDD"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   : "S"
                      ,"padrao":0}
            ,{"id":14 ,"labelCol"       : "CODBNC"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":15 ,"labelCol"       : "FC"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"popoverTitle"   : "Forma de cobrança"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":16 ,"labelCol"       : "CODPT"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":17 ,"labelCol"       : "INDICE"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":18 ,"labelCol"       : "CONTABIL"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "10em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"popoverTitle"   : "Conta contabil"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":19 ,"labelCol"       : "CODSRV"
											,"fieldType"      : "int"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "0"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":20 ,"labelCol"       : "LANCTO"
											,"fieldType"      : "int"
                      ,"formato"        : ["i6"]                      
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "10"
                      ,"excel"   				: "S"
                      ,"padrao":0}
            ,{"id":21 ,"labelCol"       : "AUTOS"
											,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "10"
                      ,"excel"   				: "S"
                      ,"popoverTitle"   : "Total de autos no contrato"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":22 ,"labelCol"       : "GRD"
											,"fieldType"      : "int"
                      ,"formato"        : ["i4"]                      
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "10"
                      ,"excel"   				: "S"
                      ,"popoverTitle"   : "Total de autos na grade inferior de lançamentos"                          
                      ,"popoverLabelCol": "Ajuda"                      
                      ,"padrao":0}
            ,{"id":23 ,"labelCol"       : "CHECK"
											,"fieldType"      : "str"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "10"
                      ,"excel"          : "S"
                      ,"funcCor"        : "switch (objCell.innerHTML) { case 'erro':objCell.classList.add('corAviso');break; case 'alerta':objCell.classList.add('corAzul');break;};" 
                      ,"popoverTitle"   : "Informa se existe algum erro ou alerta nas duas grades de consulta"                          
                      ,"popoverLabelCol": "Ajuda"                      
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
          //,"corLinha"       : "if(parseInt(ceTr.cells[19].innerHTML) > 0) {ceTr.style.color='blue';ceTr.style.backgroundColor='silver';}"                
          ,"opcRegSeek"     : false                     // Opção para numero registros/botão/procurar   
          ,"popover"        : true                      // Opção para gerar ajuda no formato popUp(Hint)          
          ,"checarTags"     : "N"                       // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmFat"                  // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaFat"               // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmFat"                  // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "divTopoInicio"           // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "sctnFat"                 // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)
          ,"tbl"            : "tblFat"                  // Nome da table
          ,"prefixo"        : "fat"                     // Prefixo para elementos do HTML em jsTable2017.js
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
          ,"relTitulo"      : "FATURAMENTO"             // Titulo do relatório
          ,"relOrientacao"  : "P"                       // Paisagem ou retrato
          ,"relFonte"       : "7"                       // Fonte do relatório
          ,"formPassoPasso" : "Trac_Espiao.php"         // Enderço da pagina PASSO A PASSO
          ,"indiceTable"    : "*"                       // Indice inicial da table
          ,"tamBotao"       : "15"                      // Tamanho botoes defalt 12 [12/25/50/75/100]
          ,"codTblUsu"      : "REG SISTEMA[31]"                          
          ,"codDir"         : intCodDir
        }; 
        if( objFat === undefined ){  
          objFat=new clsTable2017("objFat");
        };  
        /////////////////////////////////////////////////
        //        Objeto clsTable2017 ITEM             //
        /////////////////////////////////////////////////
        jsIte={
          "titulo":[
             {"id":0  ,"labelCol":"OPC"     
                      ,"tamGrd"         : "0em"             
                      ,"padrao":1}            
            ,{"id":1  ,"labelCol"       : "CONTRATO"
                      ,"fieldType"      : "int"
                      ,"formato"        : ["i6"] 
                      ,"tamGrd"         : "5em"
                      ,"tamImp"         : "20"
                      ,"padrao":0}
            ,{"id":2  ,"labelCol"       : "SERVICO"
                      ,"fieldType"      : "str"            
                      ,"tamGrd"         : "30em"
                      ,"tamImp"         : "40"											
                      ,"padrao":0}
            ,{"id":3  ,"labelCol"       : "ATIVACAO"
                      ,"fieldType"      : "str"                        
                      ,"tamGrd"         : "7em"
                      ,"tamImp"         : "25"											
                      ,"padrao":0}
            ,{"id":4  ,"labelCol"       : "DIAS"
                      ,"fieldType"      : "int"
                      //,"formato"        : ["i6"] 
                      ,"tamGrd"         : "3em"
                      ,"tamImp"         : "20"
                      ,"padrao":0}
            ,{"id":5  ,"labelCol"       : "AUTO"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "4em"
                      ,"tamImp"         : "15"
                      ,"padrao":0}
            ,{"id":6  ,"labelCol"       : "PLACA"
                      ,"fieldType"      : "str"                        
                      ,"tamGrd"         : "6em"
                      ,"tamImp"         : "20"
                      ,"funcCor"        : "(objCell.innerHTML=='NSA0000'  ? objCell.classList.add('corAviso') : objCell.classList.remove('corAviso'))"											
                      ,"padrao":0}
            ,{"id":7  ,"labelCol"       : "VLRMENSAL"
                      ,"fieldType"      : "flo2"                       
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"padrao":0}
            ,{"id":8  ,"labelCol"       : "VLRCOBRADO"
                      ,"fieldType"      : "flo2"                       
                      ,"tamGrd"         : "8em"
                      ,"tamImp"         : "25"
                      ,"padrao":0}
            ,{"id":9  ,"labelCol"       : "GERENCIAL"
                      ,"fieldType"      : "str"                        
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "30"											
                      ,"padrao":0}
            ,{"id":10 ,"labelCol"       : "CODSRV"
                      ,"fieldType"      : "int"
                      ,"tamGrd"         : "0em"
                      ,"tamImp"         : "0"
                      ,"padrao":0}
            ,{"id":11 ,"labelCol"       : "MP"
                      ,"fieldType"      : "str"                        
                      ,"tamGrd"         : "2em"
                      ,"tamImp"         : "10"											
                      ,"padrao":0}
                      
          ]
          , 
          "botoesH":[
             {"texto":"Excel"       ,"name":"horExcel"      ,"onClick":"5"  ,"enabled":true,"imagem":"fa fa-file-excel-o" }        
            ,{"texto":"Imprimir"    ,"name":"horImprimir"   ,"onClick":"3"  ,"enabled":true,"imagem":"fa fa-print"        }                    
          ] 
          ,"registros"      : []                   // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : false                // Opção para numero registros/botão/procurar 
          ,"corLinha"       : "if(ceTr.cells[3].innerHTML =='*') {ceTr.style.color='blue';}"                
          ,"checarTags"     : "S"                  // Somente em tempo de desenvolvimento(olha as pricipais tags)                  
          ,"div"            : "frmIte"             // Onde vai ser gerado a table
          ,"divFieldSet"    : "tabelaIte"          // Para fechar a div onde estão os fieldset ao cadastrar
          ,"form"           : "frmIte"             // Onde vai ser gerado o fieldSet       
          ,"divModal"       : "cllIte"             // Onde vai se appendado abaixo deste a table 
          ,"divModalDentro" : "cllIte"             // Onde vai se appendado dentro do objeto(Ou uma ou outra, se existir esta despreza a divModal)          
          ,"tbl"            : "tblIte"             // Nome da table
          ,"prefixo"        : "ch"                 // Prefixo para elementos do HTML em jsTable2017.js
          ,"position"       : "relative"          
          ,"width"          : "92em"               // Tamanho da table
          ,"height"         : "40em"               // Altura da table
          ,"tableLeft"      : "sim"                // Se tiver menu esquerdo
          ,"relTitulo"      : "TITULOS"            // Titulo do relatório
          ,"relOrientacao"  : "R"                  // Paisagem ou retrato
          ,"relFonte"       : "8"                  // Fonte do relatório
          ,"indiceTable"    : "CONTRATO"           // Indice inicial da table
          ,"tamBotao"       : "12"                 // Tamanho botoes defalt 12 [12/25/50/75/100]
        }; 
        if( objIte === undefined ){  
          objIte=new clsTable2017("objIte");
        }; 
        objFat.montarHtmlCE2017(jsFat); 
        objIte.montarHtmlCE2017(jsIte); 
        //////////////////////////////////////////////////////////////////////
        // Aqui sao as colunas que vou precisar
        // esta garante o chkds[0].?????? e objCol
        //////////////////////////////////////////////////////////////////////
        objCol=fncColObrigatoria.call(jsFat,["CLIENTE" ,"CODBNC" ,"CODCDD" ,"CODEMP" ,"CODFLL","CODFVR" ,"CODSRV"     ,"CONTABIL"   ,"CODPT"  ,"CODVND","CONTRATO"
                                           ,"EMPRESA" ,"FC"     ,"INDICE" ,"LANCTO" ,"TD"     ,"VLRCOBRADO" ,"VLRMENSAL"  ,"VENCTO" ,"VENDEDOR"]);
      });
      var objFat;                      // Obrigatório para instanciar o JS TFormaCob
      var jsFat;                       // Obj principal da classe clsTable2017
      var objIte;                     // Obrigatório para instanciar o JS TFormaCob
      var jsIte;                      // Obj principal da classe clsTable2017
      var objFcF10;                   // Obrigatório para instanciar o JS FormaCobrancaF10            
      var clsJs;                      // Classe responsavel por montar um Json e eviar PHP
      var clsErro;                    // Classe para erros            
      var fd;                         // Formulario para envio de dados para o PHP
      var msg;                        // Variavel para guardadar mensagens de retorno/erro 
      var envPhp                      // Para enviar dados para o Php                  
      var retPhp                      // Retorno do Php para a rotina chamadora
      var objCol;                     // Posicao das colunas da grade que vou precisar neste formulario      
      var arrIte;                     // Guarda os itens de cada lancamento
      var clsChecados;                // Classe para montar Json
      var chkds;                      // Guarda todos registros checados na table 
      var contMsg   = 0;              // contador para mensagens      
      var cmp       = new clsCampo(); // Abrindo a classe campos
      var jsPub     = JSON.parse(localStorage.getItem("lsPublico"));
      var intCodDir = parseInt(jsPub[0].usr_d05);
      var arqLocal  = fncFileName(window.location.pathname);  // retorna o nome do arquivo sendo executado
      function validaCmp(valor){
        let retorno=validaCompetencia(valor);
        if( retorno.erro=="ok" ){
          document.getElementById("edtDesCmp").value=retorno.descmp;
          document.getElementById("edtDesCmp").setAttribute("data-codcmp",retorno.codcmp);
        } else {
          document.getElementById("edtDesCmp").setAttribute("data-codcmp","000000");
          gerarMensagemErro("catch",retorno.erro,{cabec:"Aviso"});//,foco:"edtDesCmp"});            
        };
      }; 
      
      ////////////////////////////
      // Filtrando os registros //
      ////////////////////////////
			function btnFiltrarClick(){
        try{        
          if( jsConverte($doc("edtDesCmp").getAttribute("data-codcmp")).inteiro()<=0 )
            throw "COMPETENCIA INVALIDA PARA FILTRO!";
          
          clsJs   = jsString("lote");  
          clsJs.add("rotina"      , "selectFat"        	                          );
          clsJs.add("login"       , jsPub[0].usr_login	                          );
          clsJs.add("codcmp"      , $doc("edtDesCmp").getAttribute("data-codcmp") );
          fd = new FormData();
          fd.append("faturarcontrato" , clsJs.fim());
//debugger;          
          msg     = requestPedido(arqLocal,fd); 
          retPhp  = JSON.parse(msg);
          if( retPhp[0].retorno == "OK" ){
            //////////////////////////////////////////////////////////////////////////////////
            // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
            // Campo obrigatório se existir rotina de manutenção na table devido Json       //
            // Esta rotina não tem manutenção via classe clsTable2017                       //
            // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
            //////////////////////////////////////////////////////////////////////////////////
            jsFat.registros=objFat.addIdUnico(retPhp[0]["dados"]);
            objFat.montarBody2017();
            arrIte	=	retPhp[0]["item"];
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      };
      ////////////////////////////
      //        Faturar         //
      ////////////////////////////
			function btnFaturarClick(){
        try{
          clsChecados = objFat.gerarJson("1");
          chkds       = clsChecados.gerar();
          
          if( parseInt(chkds[0].LANCTO) > 0 )
            throw "ITEM SELECIONADO JA FATURADO!";
          if( chkds[0].EMPRESA=="..."  )
            throw "EMPRESA INVALIDA!";
          
          msg = new clsMensagem("Erro");
          
          msg.contido("NF_RECIBO", chkds[0].TD,["NFS","REC"]);
          
          if( msg.ListaErr() != "" ){
            msg.Show();
          } else {
            ////////////////////////
            // Iniciando as variveis
            ////////////////////////
            let pgrContinua = true;
            let pgrValor      = jsConverte(chkds[0].VLRCOBRADO).dolar(true);
            let pgrVencto     = jsConverte(chkds[0].VENCTO).somarDias(0,"mm/dd/yyyy");
            let pgrCodCmp     = jsConverte("hoje").somarDias(0,"yyyymm",true);
            let pgrNumNf      = 0;
            let pgrCodSnf     = 0;
            let pgrLivro      = "*";
            let pgrDocto      = "*";
            let pgrObservacao = "*";
            ////////////////////////////////////////////////////
            // Primeira rotina tem que ser buscar o numero da NF 
            ////////////////////////////////////////////////////
            clsJs=jsString("lote");            
            clsJs.add("login"   , jsPub[0].usr_login  );
            clsJs.add("rotina"  , "buscaSnf"          );
            clsJs.add("codtd"   , chkds[0].TD         );
            clsJs.add("codfll"  , chkds[0].CODFLL     );
            clsJs.add("entsai"  , "S"                 );
            clsJs.add("destd"   , "..."               );
            var fd = new FormData();
            fd.append("faturarcontrato",clsJs.fim());
            msg=requestPedido(arqLocal,fd); 
            retPhp=JSON.parse(msg);
            if( retPhp[0].retorno=="OK" ){
              pgrCodSnf      = retPhp[0].tblSnf["SNF_CODIGO"];
              pgrLivro       = retPhp[0].tblSnf["SNF_LIVRO"];
              pgrNumNf       = jsNmrs(retPhp[0].tblSnf["SNF_NFPROXIMA"]).emZero(6).ret();
              pgrDocto       = "NFS"+jsConverte(pgrNumNf).emZero(6);
              pgrObservacao  = "FATURAMENTO AUTOMATICO CONFORME "+pgrDocto;
            } else {
              pgrContinua=false;
              gerarMensagemErro("Ami",retPhp[0].erro,{cabec:"Aviso"});              
            };    
            if( pgrContinua ){  
              ////////////////////////////////  
              // Armazenando para envio ao Php
              ////////////////////////////////
              let clsRat = jsString("rateio");
              clsRat.principal(false);
              
              let clsDup = jsString("duplicata");
              clsDup.principal(false);
              ////////////////////////////////////////////////
              // Aqui vou somar o contabil de todos os lanctos
              ////////////////////////////////////////////////
              var arrFilter;
              arrFilter=arrIte.filter(function(coluna){
                return coluna.indice==chkds[0].INDICE;
              });

              let tamOri          = arrFilter.length;   // tamanho do array origem
              let tamDes          = 0;                  // tamanho do array destino
              let arrSomaContabil = [];
              let achei           = false;
              for( let origem=0; origem<tamOri; origem++ ){

                achei = false;
                for( let destino=0; destino<tamDes; destino++ ){
                  if( arrFilter[ origem ].pt_codcc == arrSomaContabil[ destino ].pt_codcc ){
                    arrSomaContabil[ destino ].cntc_vlrmensal=( jsConverte(arrSomaContabil[ destino ].cntc_vlrmensal).dolar(true) + jsConverte(arrFilter[ origem ].cntc_vlrmensal).dolar(true) );
                    achei = true;
                    break;    
                  };
                };
                
                if( achei == false ){
                  arrSomaContabil.push(
                    { pt_codcc        : arrFilter[ origem ].pt_codcc
                      ,cntc_vlrmensal : jsConverte(arrFilter[ origem ].cntc_vlrmensal).dolar(true)
                    }  
                  );
                  tamDes++;
                };  
              };
              ///////////////////////
              // fim da soma contabil
              ///////////////////////
              for( let destino=0; destino<tamDes; destino++ ){
                clsRat.add("parcela"          , 1                                                                 );
                clsRat.add("codcc"            , arrSomaContabil[ destino ].pt_codcc                         );
                clsRat.add("debito"           , 0                                                                 );
                clsRat.add("credito"          , jsConverte(arrSomaContabil[ destino ].cntc_vlrmensal).dolar(true) );
                clsRat.add("comparaVlrEvento" , "S"                                                               );
              };
              
              clsDup.add("parcela"    , 1         );
              clsDup.add("vencto"     , pgrVencto );  
              clsDup.add("vlrparcela" , pgrValor  );              
                

              let rateio    = clsRat.fim();
              let duplicata = clsDup.fim();
              
              let clsNfs = jsString("servico");
              clsNfs.principal(false);
              clsNfs.add("numnf"        , pgrNumNf            );
              clsNfs.add("codsnf"       , pgrCodSnf           );
              clsNfs.add("vlrretencao"  , 0                   );
              clsNfs.add("livro"        , pgrLivro            );
              clsNfs.add("codvnd"       , chkds[0].CODVND     );
              clsNfs.add("codcdd"       , chkds[0].CODCDD     );
              clsNfs.add("contrato"     , chkds[0].CONTRATO   );
              clsNfs.add("entsai"       , "S"                 );
              clsNfs.add("codsrv"       , chkds[0].CODSRV     );
              clsNfs.add("aliqinss"     , 0                   );
              clsNfs.add("bcinss"       , 0                   );
              clsNfs.add("vlrinss"      , 0                   );            
              clsNfs.add("aliqirrf"     , 0                   );
              clsNfs.add("vlrirrf"      , 0                   );            
              clsNfs.add("aliqpis"      , 0                   );
              clsNfs.add("vlrpis"       , 0                   );            
              clsNfs.add("aliqcofins"   , 0                   );
              clsNfs.add("vlrcofins"    , 0                   );            
              clsNfs.add("aliqcsll"     , 0                   );
              clsNfs.add("vlrcsll"      , 0                   );            
              clsNfs.add("aliqiss"      , 0                   );
              clsNfs.add("vlriss"       , 0                   );            
              clsNfs.add("opcao"        , chkds[0].TD         );    
              let servico = clsNfs.fim();            
              
              let clsFin = jsString("lote");
              clsFin.add("login"              , jsPub[0].usr_login  );
              ///////////////////////////////////////////////////////////////////////////////////
              // verdireito
              // Como vem de NFP/NFS/CONTRATO/TARIFA/TRANSF aqui informo qual direito vou olhar
              // pois um usuario pode lancar contrato mas naum NFProduto
              ///////////////////////////////////////////////////////////////////////////////////
              clsFin.add("verdireito"         , 27                                            );            
              clsFin.add("codbnc"             , chkds[0].CODBNC                               );
              clsFin.add("codcc"              , "NULL"                                        );  //Se NULL o trigger faz    
              clsFin.add("codcmp"             , pgrCodCmp                                     );  //Competencia contabil          
              clsFin.add("codfvr"             , chkds[0].CODFVR                               );
              clsFin.add("codfc"              , chkds[0].FC                                   );
              clsFin.add("codtd"              , chkds[0].TD                                   );
              clsFin.add("codfll"             , chkds[0].CODFLL                               );
              clsFin.add("codptt"             , "F"                                           );            
              clsFin.add("docto"              , pgrDocto                                      );
              clsFin.add("dtdocto"            , jsConverte("hoje").somarDias(0,"mm/dd/yyyy")  );  //jsConverte("hoje").retMMDDYYYY()
              clsFin.add("lancto"             , 0                                             );  //Se maior que zero eh rotina de alteracao            
              clsFin.add("observacao"         , pgrObservacao                                 );
              clsFin.add("codpt"              , chkds[0].CODPT                                );
              clsFin.add("codptp"             , "CR"                                          );
              clsFin.add("vlrdesconto"        , 0                                             );
              clsFin.add("vlrevento"          , pgrValor                                      );  //jsNmrs("edtVlrEvento").dolar().ret()
              clsFin.add("vlrmulta"           , 0                                             );
              clsFin.add("vlrretencao"        , 0                                             );
              clsFin.add("vlrpis"             , 0                                             );
              clsFin.add("vlrcofins"          , 0                                             );
              clsFin.add("vlrcsll"            , 0                                             );
              clsFin.add("codcntt"            , chkds[0].CONTRATO                             );  //Codigo do contrato
              clsFin.add("temnfp"             , "N"                                           );
              clsFin.add("temnfs"             , "S"                                           );            
              clsFin.add("DUPLICATA"          , duplicata                                     );
              clsFin.add("RATEIO"             , rateio                                        );
              clsFin.add("SERVICO"            , servico                                       );
              ///////////////////////
              // Enviando para gravar
              ///////////////////////
              envPhp=clsFin.fim();  
              fd = new FormData();
              fd.append("gravar",envPhp);
              msg     = requestPedido("classPhp/GravaFinanceiro.php",fd); 
              retPhp  = JSON.parse(msg);
              if( retPhp[0].retorno != "OK" ){
                throw retPhp[0].erro;
              } else {  
                //gerarMensagemErro("cad",retPhp[0].erro,{cabec:"Aviso"});  
                /////////////////////////////////////
                // Se der tudo certo atualizo a grade
                /////////////////////////////////////
                tblFat.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
                  if(row.cells[0].children[0].checked){         
                    row.cells[objCol.LANCTO].innerHTML = jsConverte(retPhp[0]["dados"]).emZero(6);
                  };
                });    
              };
            };
          };  
        }catch(e){
          gerarMensagemErro("catch",e,{cabec:"Erro"});
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
          fd.append("faturarcontrato" , clsJs.fim());
          
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
      function altFc(obj){
        fFormaCobrancaF10(0,obj.id,"null",100,{ativo:"S" } ); 
      };
      function RetF10tblFc(arr){
        //console.log(arr[0].CODIGO);
        /////////////////////////////////////
        // Atualizando a grade
        /////////////////////////////////////
        tblFat.getElementsByTagName("tbody")[0].querySelectorAll("tr").forEach(function (row,indexTr) {
          if(row.cells[0].children[0].checked){         
            row.cells[objCol.FC].innerHTML = arr[0].CODIGO;
          };
        });    
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
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="altEmpresa();"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Alterar empresa."><i class="indFa fa-spinner"></i>
          </div>
          <div id="divBlRota" class="divBarraLateral" 
                              onClick="altFc(this);"
                              data-title="Ajuda"                               
                              data-toggle="popover" 
                              data-placement="right" 
                              data-content="Alterar forma de cobrança.">FC</i>
          </div>
          
        </section>
      </aside>

      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;margin-bottom:0px;height:50px;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span class="infoBoxText">FATURAR</span>
              <span id="spnEmpApelido" class="infoBoxLabel"></span>
            </div>
          </div>
        </div>  
        
        <div id="indDivInforme" class="indTopoInicio80" style="padding-top:2px;">
          <!--
          <div class="campotexto campo10" style="margin-top:2px;">
            <input class="campo_input" id="edtDataFim" 
                                       value=""
                                       placeholder="##/##/####"                 
                                       OnKeyUp="mascaraNumero('##/##/####',this,event,'dig')"
                                       maxlength="10" type="text" />
            <label class="campo_label" for="edtDataFim">VENCTO ATÉ:</label>
          </div>
          -->
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
                    data-content="Buscar vencimentos nesta competência, informar no formato MMM/YY"                   
                    data-codcmp="000000"
                   for="edtDesCmp">COMPETÊNCIA</label>
          </div>
          
          
          <div id="btnFiltrar" onClick="btnFiltrarClick();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-check"> Filtrar</i></div>
          <div id="btnFaturar" onClick="btnFaturarClick();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-plus"> Faturar</i></div>          
          <div id="btnFechar" onClick="window.close();" class="btnImagemEsq bie10 bieAzul"><i class="fa fa-close"> Fechar</i></div>
        </div>
      </div>
      <section>
        <section id="sctnFat">
        </section>  
      </section>
      <form method="post"
            name="frmFat"
            id="frmFat"
            class="frmTable"
            action="classPhp/imprimirsql.php"
            target="_newpage">
        <input type="hidden" id="sql" name="sql"/>      
      </form>  
    </div>
    <!--
    Buscando o historico do OS
    -->
    <section id="collapseSectionIte" class="section-collapse">
      <div class="panel panel-default panel-padd">
        <p>
          <span class="btn-group">
            <a id="aLabel" class="btn btn-default disabled">Buscar</a>
            <button id="abreIte"  class="btn btn-primary" 
                                  data-toggle="collapse" 
                                  data-target="#evtAbreIte" 
                                  aria-expanded="true" 
                                  aria-controls="evtAbreIte" 
                                  type="button">Lançamentos</button>
          </span>
        </p>
        <!-- Se precisar acessar metodos deve instanciar por este id -->
        <div class="collapse" id="evtAbreIte" aria-expanded="false" role="presentation">
          <div id="cllIte" class="well" style="margin-bottom:10px;"></div>
          <div id="alertTexto" class="alert alert-info alert-dismissible fade in" role="alert" style="font-size:1.5em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <label id="lblIte" class="alert-info">Mostrando itens</label>
          </div>
        </div>
      </div>
    </section>
    <script>
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //                                                PopUp ITE                                                    //
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////////////
      // Instanciando a classe para poder olhar se pode usar .hide() / .show() / .toogle()
      ////////////////////////////////////////////////////////////////////////////////////
      abreIte  = new Collapse($doc('abreIte'));
      abreIte.status="ok";
      /////////////////////////////////////////////////////////////////////////////////////////////////
      // Metodos para historico(show.bs.collapse[antes de abrir],shown.bs.collapse[depois de aberto]
      //                        hide.bs.collapse[antes de fechar],hidden.bs.collapse[depois de fechado]                  
      /////////////////////////////////////////////////////////////////////////////////////////////////
      var collapseAbreIte = document.getElementById('evtAbreIte');
      collapseAbreIte.addEventListener('show.bs.collapse', function(el){ 
        try{
          chkds=objFat.gerarJson("1").gerar();
          if( chkds[0].INDICE == "" )
            throw "FAVOR SELECIONAR ITEM COM LANCTOS!";
          var arrFilter;
          arrFilter=arrIte.filter(function(coluna){
            return coluna.indice==chkds[0].INDICE;
          });
          
          let valor=0;  
          jsIte.registros=[];          
					arrFilter.forEach(function(cmp){
            jsIte.registros.push([
              cmp.cntc_codcntt
              ,cmp.srv_nome
              ,cmp.cntp_dtativacao
              ,cmp.dias_inicio
              ,cmp.cntp_codgmp
              ,cmp.cntp_placa
              ,cmp.cntc_vlrmensal
              ,cmp.cntc_vlrcobrado
              ,cmp.pt_codcc
              ,cmp.cntc_coddrv
              ,cmp.cntc_pgto              
            ])
            valor+=jsConverte( (cmp.cntc_vlrmensal).toString() ).dolar(true);
					});

          objIte.montarBody2017();          
          $doc("lblIte").innerHTML="Mostrando lançamentos com vencimento <b>"+chkds[0].VENCTO+"</b> Total "+valor.toFixed(2);//  jsConverte(valor.toString()).real();
          $doc("cllIte").style.height = (parseInt((document.getElementById("dPaifrmIte").style.height).slice(0,-2))+2)+"em";
          abreIte.status="ok";
          
        }catch(e){
          abreIte.status="err";
          gerarMensagemErro("catch",e,{cabec:"Erro"});
        };
      },false);
      collapseAbreIte.addEventListener('shown.bs.collapse', function(){ 
        if( abreIte.status=="err" )
          abreIte.hide();
      },false);
    </script>
    
    
    
  </body>
</html>