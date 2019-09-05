<?php
  class selectRepetido{
    var $retorno="";
    
    function qualSelect($qual,$login,$codigo=""){
      $sql="";
      /////////////////////////////////////////////////////////////////////
      // Aqui informo se vou retornar dados de um select ou uma string html
      /////////////////////////////////////////////////////////////////////
      $retDados = false;
      $retHtml  = false;
      $script   = "";
      //
      //
      switch ($qual){
        //////////////////////////////
        // Atlas_PgrCarroceria.php //
        // Atlas_PgrMercadorias.php //
        // Atlas_PgrPeriferico.php //
        //////////////////////////////
        case "pgrs":
          $sql.="SELECT A.PGR_CODIGO,A.PGR_NOME";
          $sql.="  FROM PGR A";
          $sql.=" WHERE ((A.PGR_ATIVO='S') AND (A.PGR_CODCLN IN ".$_SESSION["usr_clientes"]."))"; 
          $sql.=" ORDER BY A.PGR_NOME";          
          break;
        //////////////////////////////
        // Atlas_Motorista.php      //
        // Atlas_Contato.php        //
        // Atlas_Veiculo.php        //
        //////////////////////////////          
        case "operacao":          
          $sql.="SELECT A.OPE_CODIGO,A.OPE_APELIDO";
          $sql.="  FROM OPERACAO A";
          $sql.="  LEFT OUTER JOIN USUARIOOPERACAO UO ON A.OPE_CODIGO=UO.UO_CODOPE AND UO.UO_CODUSR=".$_SESSION['usr_codigo'];
          $sql.=" WHERE ((OPE_ATIVO='S') AND (COALESCE(UO.UO_ATIVO,'')='S'))";
          $sql.=" ORDER BY A.OPE_APELIDO";          
          break;
          
        case "hlpEndereco":   
          $sql ="SELECT A.CNTE_CODIGO";
          $sql.="       ,A.CNTE_CEP";          
          $sql.="       ,FVR.FVR_APELIDO";          
          $sql.="       ,COALESCE(C.CDD_NOME,'...') AS CDD_NOME";
          $sql.="       ,A.CNTE_CODLGR";          
          $sql.="       ,A.CNTE_ENDERECO";
          $sql.="       ,A.CNTE_NUMERO";
          $sql.="  FROM CONTRATOENDERECO A";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON A.CNTE_CODFVR=FVR.FVR_CODIGO";          
          $sql.="  LEFT OUTER JOIN CIDADE C ON A.CNTE_CODCDD=C.CDD_CODIGO";
          $sql.="  WHERE (CNTE_CODFVR=".$codigo.")";
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $tbl=$retCls["dados"];    
            $html="";
            $html.="<div id='dPaiChk' class='divContainerTable' style='height: 25em; width:99.9%;border:none;font-size:10px;overflow-y:auto;'>";  
            $html.="<table class='fpTable'>";
            $html.=  "<thead class='fpThead'>";            
            $html.=     "<tr>"; 
            $html.=      "<th class='fpTh' style='width:8%;'>ID</th>";                        
            $html.=      "<th class='fpTh' style='width:10%;'>CEP</th>";                                    
            $html.=      "<th class='fpTh' style='width:10%;'>FAVORECIDO</th>";                                    
            $html.=      "<th class='fpTh' style='width:22%;'>CIDADE</th>";                                                
            $html.=      "<th class='fpTh' style='width:8%;'>LGR</th>";
            $html.=      "<th class='fpTh' style='width:34%;'>ENDERECO</th>";                        
            $html.=      "<th class='fpTh' style='width:8%;'>NUM</th>";
            $html.=     "</tr>";            
            $html.= "</thead>";            
            $html.=  "<tbody>";
            $bgc="";
            foreach( $tbl as $end ){   
              $html.=     "<tr>";
              $html.=      "<td class='fpTd'>".str_pad($end["CNTE_CODIGO"], 6, "0", STR_PAD_LEFT)."</td>";              
              $html.=      "<td class='fpTd'>".$end["CNTE_CEP"]."</td>";
              $html.=      "<td class='fpTd'>".$end["FVR_APELIDO"]."</td>";
              $html.=      "<td class='fpTd'>".$end["CDD_NOME"]."</td>";
              $html.=      "<td class='fpTd'>".$end["CNTE_CODLGR"]."</td>";
              $html.=      "<td class='fpTd'>".$end["CNTE_ENDERECO"]."</td>";
              $html.=      "<td class='fpTd'>".$end["CNTE_NUMERO"]."</td>";
              $html.=     "</tr>";              
            };
            $html.=  "</tbody>";
            $html.="</table>";
            $html.="</div>"; 
            $html.="<div id='alertEnd' class='alert alert-info alert-dismissible fade in' role='alert' style='font-size:1.5em;margin-bottom:5px;text-align:center;'>";
            $html.="Mostrando todos os <b>endereços(entrega/instalação)</b> validos para este cliente.";
            $html.="</div>";          
          };
          break;

        case "hlpComposicao":   
          $sql ="SELECT A.AC_CODGMP AS CODIGO";
          $sql.="      ,GM.GM_NOME AS DESCRICAO";
          $sql.="      ,GMP.GMP_NUMSERIE AS SERIE";
          $sql.="      ,GMP.GMP_SINCARD AS SINCARD";
          $sql.="      ,FVR.FVR_APELIDO AS FABRICANTE";
          $sql.=" FROM AUTOCOMPOSICAO A";
          $sql.=" LEFT OUTER JOIN GRUPOMODELOPRODUTO GMP ON A.AC_CODGMP=GMP.GMP_CODIGO";
          $sql.=" LEFT OUTER JOIN GRUPOMODELO GM ON GMP.GMP_CODGM=GM.GM_CODIGO";
          $sql.=" LEFT OUTER JOIN FAVORECIDO FVR ON GMP.GMP_CODFBR=FVR.FVR_CODIGO";
          $sql.=" WHERE ((A.AC_CODAMP=".$codigo.") AND (A.AC_CODAMP<>A.AC_CODGMP))";
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $tbl=$retCls["dados"];    
            $html="";
            $html.="<div id='dPaiChk' class='divContainerTable' style='height: 29em; width:99%;border:none;font-size:10px;overflow-y:auto;'>";  
            $html.="<table id='tblChk' class='fpTable' style='width:100%;'>";
            $html.="  <thead class='fpThead'>";
            $html.="    <tr>";
            $html.="      <th class='fpTh' style='width:10%'>CODIGO</th>";
            $html.="      <th class='fpTh' style='width:40%'>PRODUTO</th>";
            $html.="      <th class='fpTh' style='width:15%'>SERIE</th>";
            $html.="      <th class='fpTh' style='width:15%'>SINCARD</th>";
            $html.="      <th class='fpTh' style='width:20%'>FABRICANTE</th>";
            $html.="    </tr>";
            $html.="  </thead>";
            $html.="  <tbody id='tbody_tblChk'>";
            foreach( $tbl as $cmp ){   
              $html.="    <tr class='fpBodyTr'>";
              $html.="      <td class='fpTd textoCentro'>".$cmp["CODIGO"]."</td>";
              $html.="      <td class='fpTd'>".$cmp["DESCRICAO"]."</td>";
              $html.="      <td class='fpTd textoCentro'>".$cmp["SERIE"]."</td>";
              $html.="      <td class='fpTd textoCentro'>".$cmp["SINCARD"]."</td>";
              $html.="      <td class='fpTd textoCentro'>".$cmp["FABRICANTE"]."</td>";
              $html.="    </tr>";
            };
            unset($cmp);
            $html.="  </tbody>";
            $html.="</table>";
            $html.="</div>"; 
          };
          break;
          
        case "hlpTerceiro":   
          $xpld  = explode("|",$codigo);
          //////////////////////////////////////////////////////////////////////////////////////////
          // Se for 0( zero ) eh para pegar a lat/lgn do favorecido e naum do endereco de instalacao
          //////////////////////////////////////////////////////////////////////////////////////////
          if( $xpld[1]==0 ){
            $sql= "SELECT A.PEI_CODFVR AS CODIGO";
            $sql.="       ,A.PEI_CODPE AS PE";
            $sql.="       ,DES.FVR_NOME AS COLABORADOR";
            $sql.="       ,CASE WHEN A.PEI_STATUS='BOM' THEN CAST('BOM' AS VARCHAR(3))";
            $sql.="             WHEN A.PEI_STATUS='OTI' THEN CAST('OTIMO' AS VARCHAR(5))";          
            $sql.="             WHEN A.PEI_STATUS='RAZ' THEN CAST('RAZOAVEL' AS VARCHAR(8))";
            $sql.="             WHEN A.PEI_STATUS='RUI' THEN CAST('RUIM' AS VARCHAR(4))";          
            $sql.="             WHEN A.PEI_STATUS='NSA' THEN CAST('...' AS VARCHAR(3)) END AS STATUS";
            $sql.="       ,DES.FVR_FONE AS FONE";
            $sql.="       ,DES.FVR_EMAIL AS EMAIL";
            $sql.="       ,CDD.CDD_NOME AS CIDADE";
            $sql.="       ,CDD.CDD_CODEST AS UF";
            $sql.="       ,(dbo.fun_CalcDistancia(ORI.FVR_LATITUDE,ORI.FVR_LONGITUDE,DES.FVR_LATITUDE,DES.FVR_LONGITUDE)/1000) AS DISTANCIA";
            $sql.="  FROM PONTOESTOQUEIND A";
            $sql.="  LEFT OUTER JOIN FAVORECIDO ORI ON ORI.FVR_CODIGO=".$xpld[0];
            $sql.="  LEFT OUTER JOIN FAVORECIDO DES ON A.PEI_CODFVR=DES.FVR_CODIGO";
            $sql.="  LEFT OUTER JOIN CIDADE CDD ON DES.FVR_CODCDD=CDD.CDD_CODIGO";
            $sql.=" WHERE A.PEI_CODPE IN('CRD','INS','TRC')";
            $sql.="   AND A.PEI_ATIVO='S'";
            $sql.=" ORDER BY 9";
          }  
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $popover=[];
            $popover[0]=" id='pe' data-dismissible='false' data-toggle='popover' data-title='Titulo' data-placement='right' data-content='Ponto de estoque'";
            $popover[1]=" id='km' data-dismissible='false' data-toggle='popover' data-title='Titulo' data-placement='right' data-content='Distancia em KM'";
            $popover[2]=" id='hlp' data-dismissible='false' data-toggle='popover' data-title='Titulo' data-placement='right' data-content='Complemento'";
            
            $tbl=$retCls["dados"];    
            $html="";
            $html.="<div id='dPaiChk' class='divContainerTable' style='height: 37em; width:78em;border:none'>";  
            $html.="<table id='tblChk' class='fpTable' style='width:100%;'>";
            $html.=  "<thead class='fpThead'>";            
            $html.=     "<tr>"; 
            $html.=      "<th class='colunaOculta' style='width:0%;'>ID</th>";                                    
            $html.=      "<th class='fpTh' style='width:8%;'>CODIGO</th>";                        
            $html.=      "<th class='fpTh' ".$popover[0]." style='width:7%;'>PE</th>";                                    
            $html.=      "<th class='fpTh' style='width:45%;'>COLABORADOR</th>";   
            $html.=      "<th class='fpTh' style='width:10%;'>STATUS</th>";               
            $html.=      "<th class='fpTh' style='width:12%;'>FONE</th>";                                                
            $html.=      "<th class='fpTh' ".$popover[1]." style='width:10%;'>KM</th>";
            $html.=      "<th class='fpTh' ".$popover[2]." style='width:8%;'>INF</th>";
            $html.=     "</tr>";            
            $html.= "</thead>";            
            $html.=  "<tbody>";
            $bgc="";
            unset($popover);
            $rowId=-1;
            foreach( $tbl as $end ){   
              $rowId++;
              switch( $end["STATUS"] ){ 
                case 'OTIMO'    : $class="corVerde"; break;
                case 'BOM'      : $class="corAzul"; break;
                case 'RAZOAVEL' : $class="corTitulo"; break;
                case 'RUIM'     : $class="corAlterado"; break;
                case '...'     	: $class=""; break;
              };
              $popover=" id='".$rowId."' data-dismissible='false' data-toggle='popover' data-title='Complemento' data-placement='right'";
              $popover.=" data-content='<b>Email:</b>".$end["EMAIL"]."<br><b>Cidade</b>:".$end["CIDADE"]."<br><b>Uf</b>:".$end["UF"]."'";
              $html.=     "<tr>";
              $html.=      "<td class='colunaOculta'>".$rowId."</td>";              
              $html.=      "<td class='fpTd'>".str_pad($end["CODIGO"], 6, "0", STR_PAD_LEFT)."</td>";              
              $html.=      "<td class='fpTd textoCentro'>".$end["PE"]."</td>";
              $html.=      "<td class='fpTd' title='".$end["COLABORADOR"]."'>".$end["COLABORADOR"]."</td>";
              $html.=      "<td class='fpTd ".$class."'>".$end["STATUS"]."</td>";              
              $html.=      "<td class='fpTd'>".$end["FONE"]."</td>";
              $html.=      "<td class='fpTd textoCentro'>".$end["DISTANCIA"]."</td>";
              $html.=      "<td id='td1' class='fpTd textoCentro'>";
              $html.=      "  <div width='100%' height='100%'".$popover.">";              
              $html.=      "    <i class='fa fa-comment-o' style='margin-left:10px;font-size:1.5em;'></i>";
              $html.=      "  </div>";
              $html.=      "</td>";
              $html.=     "</tr>";              
            };
            $html.=  "</tbody>";
            $html.="</table>";
            $html.="</div>"; 
            $html.="<div id='alertEnd' class='alert alert-info alert-dismissible fade in' role='alert' style='font-size:1.5em;margin-bottom:5px;text-align:center;'>";
            $html.="Mostrando terceiros para executar <b>instalação/ativação</b> pela distância em <b>KM</b>.";
            $html.="</div>";          
            
            unset($popover,$end,$rowId);
          };
          break;
          
        case "hlpPlaca":   
          //////////////////////////////////////////////////////////////////////////////////////////
          // Se for 0( zero ) eh para pegar a lat/lgn do favorecido e naum do endereco de instalacao
          //////////////////////////////////////////////////////////////////////////////////////////
          $sql= "SELECT A.CNTP_PLACACHASSI AS PLACA";
          $sql.="       ,A.CNTP_CODCNTT AS CONTRATO";
          $sql.="       ,VCR.VCR_NOME AS COR";
          $sql.="       ,VTP.VTP_NOME AS TIPO";
          $sql.="       ,VMD.VMD_NOME AS MODELO";
          $sql.="       ,VCL.VCL_ANO AS ANO";          
          $sql.="  FROM CONTRATOPLACA A";
          $sql.="  LEFT OUTER JOIN VEICULO VCL ON A.CNTP_PLACACHASSI=VCL.VCL_CODIGO";            
          $sql.="  LEFT OUTER JOIN VEICULOCOR VCR ON VCL.VCL_CODVCR=VCR.VCR_CODIGO";
          $sql.="  LEFT OUTER JOIN VEICULOTIPO VTP ON VCL.VCL_CODVTP=VTP.VTP_CODIGO";
          $sql.="  LEFT OUTER JOIN VEICULOMODELO VMD ON VCL.VCL_CODVMD=VMD.VMD_CODIGO";
          $sql.=" WHERE A.CNTP_CODCNTT=".$codigo;
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $tbl=$retCls["dados"];    
            $html="";
            $html.="<div id='dPaiChk' class='divContainerTable' style='height: 37em; width:78em;border:none'>";  
            $html.="<table id='tblChk' class='fpTable' style='width:100%;'>";
            $html.=  "<thead class='fpThead'>";            
            $html.=     "<tr>"; 
            $html.=      "<th class='fpTh' style='width:20%;'>PLACA</th>";                        
            $html.=      "<th class='fpTh' style='width:25%;'>COR</th>";   
            $html.=      "<th class='fpTh' style='width:20%;'>TIPO</th>";               
            $html.=      "<th class='fpTh' style='width:20%;'>MODELO</th>";                                                
            $html.=      "<th class='fpTh' style='width:10%;'>ANO</th>";
            $html.=      "<th class='fpTh' style='width:5%;'>STATUS</th>";            
            $html.=     "</tr>";            
            $html.= "</thead>";            
            $html.=  "<tbody>";
            foreach( $tbl as $end ){   
              $status="OK";
              $class="";
              if( (substr($end["COR"],0,4)=="NAO ") or (substr($end["TIPO"],0,4)=="NAO ") or (substr($end["MODELO"],0,4)=="NAO ") or ($end["ANO"]==1900) ){
                $status="ERRO";  
                $class="corAlterado";
              };  
              $html.=     "<tr>";
              $html.=      "<td class='fpTd textoCentro'>".$end["PLACA"]."</td>";
              $html.=      "<td class='fpTd textoCentro'>".$end["COR"]."</td>";              
              $html.=      "<td class='fpTd textoCentro'>".$end["TIPO"]."</td>";                            
              $html.=      "<td class='fpTd textoCentro'>".$end["MODELO"]."</td>";                            
              $html.=      "<td class='fpTd textoCentro'>".$end["ANO"]."</td>";
              $html.=      "<td class='fpTd ".$class."'>".$status."</td>";                            
              $html.=     "</tr>";              
            };
            $html.=  "</tbody>";
            $html.="</table>";
            $html.="</div>"; 
            unset($end,$class,$status);
          };
          break;

        case "hlpOs":   
          //////////////////////////////////////////////////////////////////////////////////////////
          // Se for 0( zero ) eh para pegar a lat/lgn do favorecido e naum do endereco de instalacao
          //////////////////////////////////////////////////////////////////////////////////////////
          $sql= "SELECT A.OS_CODIGO AS OS";
          $sql.="       ,A.OS_CODCNTT AS CONTRATO";
          $sql.="       ,FVR.FVR_APELIDO AS CLIENTE";          
          $sql.="       ,CONVERT(VARCHAR(10),A.OS_EMISSAO,3) AS EMISSAO";
          $sql.="       ,CONVERT(VARCHAR(10),A.OS_DTAGENDA,3) AS AGENDA";          
          $sql.="       ,MSG.MSG_NOME AS REF";
          $sql.="       ,PEI.FVR_APELIDO AS COLABORADOR";
          $sql.="       ,GMP.GMP_PLACACHASSI AS PLACA";
          $sql.="       ,CONVERT(VARCHAR(10),A.OS_DTBAIXA,3) AS BAIXA";          
          $sql.="  FROM ORDEMSERVICO A WITH(NOLOCK)";
          $sql.="  LEFT OUTER JOIN CONTRATO CNTT ON A.OS_CODCNTT=CNTT_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO FVR ON CNTT.CNTT_CODFVR=FVR.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN GRUPOMODELOPRODUTO GMP ON A.OS_CODGMP=GMP.GMP_CODIGO";
          $sql.="  LEFT OUTER JOIN FAVORECIDO PEI ON A.OS_CODPEI=PEI.FVR_CODIGO";
          $sql.="  LEFT OUTER JOIN MENSAGEM MSG ON A.OS_CODMSG=MSG.MSG_CODIGO";          
          $sql.=" WHERE A.OS_CODCNTT=".$codigo;
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $tbl=$retCls["dados"];    
            $html="";
            $html.="<div id='dPaiChk' class='divContainerTable' style='height: 37em; width:78em;border:none'>";  
            $html.="<table id='tblChk' class='fpTable' style='width:100%;'>";
            $html.=  "<thead class='fpThead'>";            
            $html.=     "<tr>"; 
            $html.=      "<th class='fpTh' style='width:8%;'>OS</th>";                        
            $html.=      "<th class='fpTh' style='width:7%;'>CONTR</th>";   
            $html.=      "<th class='fpTh' style='width:18%;'>CLIENTE</th>";                        
            $html.=      "<th class='fpTh' style='width:10%;'>EMISSAO</th>";               
            $html.=      "<th class='fpTh' style='width:10%;'>AGENDA</th>";                                                
            $html.=      "<th class='fpTh' style='width:4%;'>REF</th>";
            $html.=      "<th class='fpTh' style='width:18%;'>COLABORADOR</th>";        
            $html.=      "<th class='fpTh' style='width:15%;'>PLACA</th>";                        
            $html.=      "<th class='fpTh' style='width:10%;'>BAIXA</th>";                                    
            $html.=     "</tr>";            
            $html.= "</thead>";            
            $html.=  "<tbody>";
            foreach( $tbl as $os ){   
              $html.=     "<tr>";
              $html.=      "<td class='fpTd textoCentro'>".str_pad($os["OS"], 6, "0", STR_PAD_LEFT)."</td>";               
              $html.=      "<td class='fpTd textoCentro'>".str_pad($os["CONTRATO"], 4, "0", STR_PAD_LEFT)."</td>";   
              $html.=      "<td class='fpTd textoCentro'>".$os["CLIENTE"]."</td>";                            
              $html.=      "<td class='fpTd textoCentro'>".$os["EMISSAO"]."</td>";                            
              $html.=      "<td class='fpTd textoCentro'>".$os["AGENDA"]."</td>";
              $html.=      "<td class='fpTd textoCentro'>".$os["REF"]."</td>";              
              $html.=      "<td class='fpTd textoCentro'>".$os["COLABORADOR"]."</td>";              
              $html.=      "<td class='fpTd textoCentro'>".$os["PLACA"]."</td>";   
              $html.=      "<td class='fpTd textoCentro'>".$os["BAIXA"]."</td>";               
              $html.=     "</tr>";              
            };
            $html.=  "</tbody>";
            $html.="</table>";
            $html.="</div>"; 
            unset($end,$class,$status);
          };
          break;
          
        case "detAuto":   
          //////////////////////////////////////////////////////////////////////////////////////////
          // Detalhe do auto
          //////////////////////////////////////////////////////////////////////////////////////////
          $sql= "SELECT dbo.fnc_data(A.DA_DATA) AS DATA";
          $sql.="       ,A.DA_CODGMP AS AUTO";
          $sql.="       ,MSG.MSG_NOME AS ROTINA";
          $sql.="       ,US.US_APELIDO AS USUARIO";
          $sql.="       ,COALESCE(A.DA_COMPLEMENTO,'...') AS COMPLEMENTO";
          $sql.="  FROM DETALHEAUTO A";
          $sql.="  LEFT OUTER JOIN MENSAGEM MSG ON A.DA_CODMSG=MSG.MSG_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.DA_CODUSR=US.US_CODIGO";
          $sql.=" WHERE A.DA_CODGMP=".$codigo;
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $tbl=$retCls["dados"]; 
            $html="";
            $html.="<div id='dPaiChk' class='divContainerTable' style='height: 37em; width:78em;border:none'>";  
            $html.="<table id='tblChk' class='fpTable' style='width:100%;'>";
            $html.=  "<thead class='fpThead'>";            
            $html.=     "<tr>"; 
            $html.=      "<th class='fpTh' style='width:15%;'>DATA</th>";                        
            $html.=      "<th class='fpTh' style='width:2%;'>AUTO</th>";   
            $html.=      "<th class='fpTh' style='width:25%;'>ROTINA</th>";               
            $html.=      "<th class='fpTh' style='width:13%;'>USUARIO</th>";                                                
            $html.=      "<th class='fpTh' style='width:35%;'>COMPLEMENTO</th>";            
            $html.=     "</tr>";            
            $html.= "</thead>";            
            $html.=  "<tbody>";
            foreach( $tbl as $end ){  
              $html.=     "<tr>";
              $html.=      "<td class='fpTd textoCentro'>".$end["DATA"]."</td>";
              $html.=      "<td class='fpTd textoCentro'>".$end["AUTO"]."</td>";              
              $html.=      "<td class='fpTd textoCentro'>".$end["ROTINA"]."</td>";                            
              $html.=      "<td class='fpTd textoCentro'>".$end["USUARIO"]."</td>";                            
              $html.=      "<td class='fpTd textoCentro'>".$end["COMPLEMENTO"]."</td>";
              $html.=     "</tr>";              
            };
            $html.=  "</tbody>";
            $html.="</table>";
            $html.="</div>"; 
            unset($end,$class,$status);
          };
          break;


        case "detOs":   
          //////////////////////////////////////////////////////////////////////////////////////////
          // Detalhe da OS
          //////////////////////////////////////////////////////////////////////////////////////////
          $sql= "SELECT dbo.fnc_data(A.DOS_DATA) AS DATA";
          $sql.="       ,A.DOS_CODOS AS OS";
          $sql.="       ,MSG.MSG_NOME AS ROTINA";
          $sql.="       ,US.US_APELIDO AS USUARIO";
          $sql.="       ,COALESCE(A.DOS_COMPLEMENTO,'...') AS COMPLEMENTO";
          $sql.="  FROM DETALHEOS A";
          $sql.="  LEFT OUTER JOIN MENSAGEM MSG ON A.DOS_CODMSG=MSG.MSG_CODIGO";
          $sql.="  LEFT OUTER JOIN USUARIOSISTEMA US ON A.DOS_CODUSR=US.US_CODIGO";
          $sql.=" WHERE A.DOS_CODOS=".$codigo;
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $tbl=$retCls["dados"]; 
            $html="";
            $html.="<div id='dPaiChk' class='divContainerTable' style='height: 37em; width:78em;border:none'>";  
            $html.="<table id='tblChk' class='fpTable' style='width:100%;'>";
            $html.=  "<thead class='fpThead'>";            
            $html.=     "<tr>"; 
            $html.=      "<th class='fpTh' style='width:15%;'>DATA</th>";                        
            $html.=      "<th class='fpTh' style='width:4%;'>OS</th>";   
            $html.=      "<th class='fpTh' style='width:23%;'>ROTINA</th>";               
            $html.=      "<th class='fpTh' style='width:13%;'>USUARIO</th>";                                                
            $html.=      "<th class='fpTh' style='width:35%;'>COMPLEMENTO</th>";
            $html.=     "</tr>";            
            $html.= "</thead>";            
            $html.=  "<tbody>";
            foreach( $tbl as $end ){  
              $html.=     "<tr>";
              $html.=      "<td class='fpTd textoCentro'>".$end["DATA"]."</td>";
              $html.=      "<td class='fpTd textoCentro'>".$end["OS"]."</td>";              
              $html.=      "<td class='fpTd textoCentro'>".$end["ROTINA"]."</td>";                            
              $html.=      "<td class='fpTd textoCentro'>".$end["USUARIO"]."</td>";                            
              $html.=      "<td class='fpTd' style='white-space:normal'>".$end["COMPLEMENTO"]."</td>";
              $html.=     "</tr>";              
            };
            $html.=  "</tbody>";
            $html.="</table>";
            $html.="</div>"; 
            unset($end,$class,$status);
          };
          break;
          
        case "hlpColaborador":          
          $sql ="SELECT A.FVR_NOME,A.FVR_CONTATO,A.FVR_FONE,A.FVR_EMAIL,CDD.CDD_CODEST,CDD.CDD_NOME,A.FVR_ENDERECO,FVR_NUMERO"; 
          $sql.="  FROM FAVORECIDO A";
          $sql.="  LEFT OUTER JOIN CIDADE CDD ON A.FVR_CODCDD=CDD.CDD_CODIGO";
          $sql.=" WHERE (FVR_CODIGO=".$codigo.")"; 
          $classe   = new conectaBd();
          $classe->conecta($login);
          $classe->msgSelect(false);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls['retorno'] != "OK" ){
            $this->retorno=[ "retorno"  =>  "ERR"
                            ,"dados"    =>  ""
                            ,"erro"     =>  $retCls['erro']];
          } else {
            $retHtml=true;
            $tbl=$retCls["dados"][0];
            $html="";
            //$html.="<div id='dPaiChk' class='divContainerTable' style='height:'25em'; width:100%;border:none;overflow-y:auto;'>";  
            $html.="<table class='fpTable'>";
            $html.=  "<tbody>";
            $html.=    "<tr>";            
            $html.=      "<td class='fpTdTitulo' style='width:25%;'>UF</td>";
            $html.=      "<td class='fpTd' style='width:25%;'>".$tbl["CDD_CODEST"]."</td>";
            $html.=      "<td class='fpTdTitulo' style='width:25%;'>CIDADE</td>";
            $html.=      "<td class='fpTd' style='width:25%;'>".$tbl["CDD_NOME"]."</td>";
            $html.=    "</tr>";
            $html.=    "<tr>";
            $html.=      "<td class='fpTdTitulo'>COLABORADOR</td>";
            $html.=      "<td colspan='3' class='fpTd' >".$tbl["FVR_NOME"]."</td>";
            $html.=    "</tr>";
            $html.=    "<tr>";
            $html.=      "<td class='fpTdTitulo'>EMAIL</td>";
            $html.=      "<td colspan='3' class='fpTd' >".$tbl["FVR_EMAIL"]."</td>";
            $html.=    "</tr>";
            $html.=    "<tr>";
            $html.=      "<td class='fpTdTitulo'>FONE</td>";
            $html.=      "<td class='fpTd' >".$tbl["FVR_FONE"]."</td>";
            $html.=      "<td colspan='3' class='fpTdTitulo'>CONTATO ".$tbl["FVR_CONTATO"]."</td>";
            $html.=    "</tr>";
            $html.=  "</tbody>";
            $html.="</table>";
          };  
          break;

        case "altEmpresa":
          $retHtml=true;
          $html ="<div id='dPaiChk' class='divContainerTable' style='height: 13.2em; width: 41em;border:none'>";
          $html.="<table id='tblChk' class='fpTable' style='width:100%;'>";
          $html.="  <thead class='fpThead'>";
          $html.="    <tr>";
          $html.="      <th class='fpTh' style='width:20%'>CoDIGO</th>";
          $html.="      <th class='fpTh' style='width:60%'>DESCRICAO</th>";
          $html.="      <th class='fpTh' style='width:20%'>SIM</th>";        
          $html.="    </tr>";
          $html.="  </thead>";
          $html.="  <tbody id='tbody_tblChk'>";
          $html.="    <tr class='fpBodyTr'>";
          $html.="      <td class='fpTd textoCentro'>01</td>";
          $html.="      <td class='fpTd'>TRACCOM</td>";
          $html.="      <td class='fpTd textoCentro'>";
          $html.="        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheck((elTr.rowIndex-1));'>";
          $html.="          <i id='img01' data-value='N' class='fa fa-thumbs-o-down' style='margin-left:10px;font-size:1.5em;color:red;'></i>";
          $html.="        </div>";
          $html.="      </td>";
          $html.="    </tr>";
          $html.="    <tr class='fpBodyTr'>";
          $html.="      <td class='fpTd textoCentro'>02</td>";
          $html.="      <td class='fpTd'>TRACLOC</td>";
          $html.="      <td class='fpTd textoCentro'>";
          $html.="        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheck((elTr.rowIndex-1));'>";
          $html.="          <i id='img02' data-value='N' class='fa fa-thumbs-o-down' style='margin-left:10px;font-size:1.5em;color:red;'></i>";
          $html.="        </div>";
          $html.="      </td>";
          $html.="    </tr>";
          $html.="    <tr class='fpBodyTr'>";
          $html.="      <td class='fpTd textoCentro'>03</td>";
          $html.="      <td class='fpTd'>TRACFRAN</td>";
          $html.="      <td class='fpTd textoCentro'>";
          $html.="        <div width='100%' height='100%' onclick=' var elTr=this.parentNode.parentNode;fncCheck((elTr.rowIndex-1));'>";
          $html.="          <i id='img03' data-value='N' class='fa fa-thumbs-o-down' style='margin-left:10px;font-size:1.5em;color:red;'></i>";
          $html.="        </div>";
          $html.="      </td>";
          $html.="    </tr>";
          $html.="  </tbody>";
          $html.="</table>";
          $html.="</div>"; 
          $html.="<div id='btnConfirmar' onClick='altEmpresaClick();' class='btnImagemEsq bie15 bieAzul bieRight' data-codemp='*' data-desemp='*'><i class='fa fa-check'> Ok</i></div>";  

          $script ="function fncCheck(pLin){";
          $script.="  let elImg;";
          $script.="  tblChk.getElementsByTagName('tbody')[0].querySelectorAll('tr').forEach(function (row,indexTr) {";  
          $script.="    elImg = 'img'+row.cells[0].innerHTML;";
          $script.="    if( indexTr==pLin ){";
          $script.="      jsCmpAtivo(elImg).remove('fa-thumbs-o-down').add('fa-thumbs-o-up').cor('blue');";
          $script.="      document.getElementById('btnConfirmar').setAttribute('data-codemp',row.cells[0].innerHTML);";          
          $script.="      document.getElementById('btnConfirmar').setAttribute('data-desemp',row.cells[1].innerHTML);";
          $script.="    } else {";
          $script.="      jsCmpAtivo(elImg).remove('fa-thumbs-o-up').add('fa-thumbs-o-down').cor('red');";
          $script.="    };";
          $script.="  });";
          $script.="};";
          //
          $script.="function altEmpresaClick(){";
          $script.="  let clsArq;";
          $script.="  let envPhp;";
          $script.="  let retornoPhp;";
          $script.="  let msg;";
          $script.="  clsArq=jsString('lote');";
          $script.="  clsArq.add('login'    , 'TRAC'                                                                );";
          $script.="  clsArq.add('empresa'  , document.getElementById('btnConfirmar').getAttribute('data-desemp')   );";
          $script.="  clsArq.add('usuario'  , 'TROCAEMPRESA'                                                        );";
          $script.="  clsArq.add('senha'    , '*'                                                                   );";
          $script.="  envPhp=clsArq.fim();";
          $script.="  retornoPhp = new FormData();";
          $script.="  retornoPhp.append('login',envPhp );";
          $script.="  msg=requestPedido('Trac_Login.php',retornoPhp);";
          $script.="  var retPhp=JSON.parse(msg);";
          $script.="  console.log(retPhp);";          
          $script.="  if( retPhp[0].retorno=='OK' ){";
          $script.="    localStorage.setItem('lsPublico',JSON.stringify(retPhp[0].dados));";
          $script.="    jsPub = JSON.parse(localStorage.getItem('lsPublico'));";          
          $script.="    localStorage.setItem('lsPathPhp','phpSqlServer.php');";
          $script.="    switch( arqLocal ){";
          $script.="      case 'Trac_Banco.php':";
          $script.="        btnFiltrarClick('S');";          
          $script.="        break;";
          $script.="      case 'Trac_Cnab.php':";
          $script.="        document.getElementById('spnEmpApelido').innerHTML=jsPub[0].emp_apelido;";
          $script.="        buscaBanco();";
          $script.="        jsCnb.registros=[];";
          $script.="        objCnb.montarHtmlCE2017(jsCnb);"; 
          $script.="        break;";
          $script.="      case 'Trac_ContabilMes.php':";
          $script.="        document.getElementById('spnEmpApelido').innerHTML=jsPub[0].emp_apelido;";
          $script.="        jsCm.registros=[];";
          $script.="        objCm.montarHtmlCE2017(jsCm);"; 
          $script.="        break;";
          $script.="      case 'Trac_CpCr.php':";
          $script.="        document.getElementById('spnEmpApelido').innerHTML=jsPub[0].emp_apelido;";
          $script.="        btnFiltrarClick();";                    
          $script.="        break;";
          $script.="      case 'Trac_Filial.php':";
          $script.="        btnFiltrarClick('S');";          
          $script.="        break;";
          $script.="      case 'Trac_Imposto.php':";
          $script.="        btnFiltrarClick('S');";          
          $script.="        break;";
          $script.="      case 'Trac_Nfp.php':";
          $script.="        document.getElementById('spnEmpApelido').innerHTML=jsPub[0].emp_apelido;";
          $script.="        btnFiltrarClick();";                    
          $script.="        break;";
          $script.="      case 'Trac_Nfs.php':";
          $script.="        document.getElementById('spnEmpApelido').innerHTML=jsPub[0].emp_apelido;";
          $script.="        btnFiltrarClick();";                    
          $script.="        break;";
          $script.="      case 'Trac_CpCrFaturarContrato.php':";  
          $script.="        let codemp=parseInt(document.getElementById('btnConfirmar').getAttribute('data-codemp'));";          
          $script.="        tblFc.getElementsByTagName('tbody')[0].querySelectorAll('tr').forEach(function (row,indexTr,tam){";     
          $script.="          if(row.cells[0].children[0].checked){";
          $script.="            row.cells[objCol.EMPRESA].innerHTML=document.getElementById('btnConfirmar').getAttribute('data-desemp');";
          $script.="            row.cells[objCol.CODEMP].innerHTML=codemp;";
          $script.="            row.cells[objCol.CODFLL].innerHTML=(parseInt(codemp)*1000)+1;";
          $script.="          };";
          $script.="        });";
          $script.="        break;";          
          $script.="    }";
          $script.="    janelaFechar();";
          $script.="  };";                    
          $script.="};";          
          break;
      }; 
      ///////////////////
      // Retornando dados
      ///////////////////  
      if( $retDados ){      
        $classe   = new conectaBd();
        $classe->conecta($login);
        $classe->msgSelect(false);
        $retCls=$classe->selectAssoc($sql);
        if( $retCls['retorno'] != "OK" ){
          $this->retorno=[ "retorno"  =>  "ERR"
                          ,"dados"    =>  ""
                          ,"erro"     =>  $retCls['erro']];
        } else { 
          $this->retorno=[ "retorno"  =>  "OK"
                          ,"dados"    =>  json_encode($retCls['dados'])
                          ,"erro"     =>  ""];
        };
      };
      ///////////////////
      // Retornando html
      ///////////////////  
      if( $retHtml ){
        $this->retorno=[ "retorno"  =>  "OK"
                        ,"dados"    =>  '"'.$html.'"'
                        ,"script"   =>  '"'.$script.'"'
                        ,"erro"     =>  ""];
      };                
      return $this->retorno;      
    }  
  }
?>