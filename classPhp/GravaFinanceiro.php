<?php
  session_start();
  if( isset($_POST["gravar"]) ){
    try{     
      require("conectaSqlServer.class.php");
      require("validaJson.class.php"); 
      require("removeAcento.class.php");
      require("selectRepetido.class.php");      
      require("validaCampo.class.php"); 
      
      //function fncPg($a, $b) {
      // return $a["PDR_NOME"] > $b["PDR_NOME"];
      //};
      $clsRa    = new removeAcento();
      $vldr     = new validaJson();          
      $retorno  = "";
      $retCls   = $vldr->validarJs($_POST["gravar"]);
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
        $classe   = new conectaBd();
        $classe->conecta($lote[0]->login);
        ////////////////////////////////////////////////////////
        // Checagem basica em cada campo da tabela master do ERP
        ////////////////////////////////////////////////////////
        // PGR_LANCTO       INT NN                |
        // PGR_BLOQUEADO    VC(1) NN              | upper | in(S,N)
        // PGR_CHEQUE       VC(10)                | upper |
        // PGR_CODBNC       INT NN                |
        // PGR_CODFVR       INT NN                |
        // PGR_CODFC        VC(3) NN              | upper |
        // PGR_CODTD        VC(3) NN              | upper |
        // PGR_VENCTO       DAT NN                |
        // PGR_DATAPAGA     DAT                   |
        // PGR_DOCTO        VC(12) NN             | upper | RemoveAcentos | Alltrim |>0 and <12
        // PGR_DTDOCTO      DATE NN               | upper |
        // PGR_CODPTT       VC(1) NN              | upper |
        // PGR_MASTER       INT NN                |
        // PGR_OBSERVACAO   VC(120) NN            | upper | RemoveAcentos | >0 and <120
        // PGR_PARCELA      INT NN                | Trigger
        // PGR_CODPTP       VC(2) NN              | upper | SELECT |
        // PGR_INDICE       INT NN                | Trigger
        // PGR_CODPT        INT NN                |
        // PGR_VLRDESCONTO  NUM(15,2) NN          | round(2) | >=0 | >0 and PGR_VLRMULTA=0 | =0 and PGR_VLRMULTA>0 | =0 and PGR_VLRMULTA=0
        // PGR_VLREVENTO    NUM(15,2) NN          | round(2) | >=0 | PGR_VLREVENTO>=PGR_VLRPARCELA
        // PGR_VLRPARCELA   NUM(15,2) NN          | round(2) | >=0 | PGR_VLRPARCELA<=PGR_VLREVENTO
        // PGR_VLRLIQUIDO   NUM(15,2) NN          | Trigger  |
        // PGR_VLRMULTA     NUM(15,2) NN          | round(2) | >=0 | >0 and PGR_VLRDESCONTO=0 | =0 and PGR_VLRDESCONTO>0 | =0 and PGR_VLRDESCONTO=0
        // PGR_VLRRETENCAO  NUM(15,2) NN          | round(2) | >=0 |
        // PGR_VLRPIS       NUM(15,2) NN          | round(2) | >=0 |
        // PGR_VLRCOFINS    NUM(15,2) NN          | round(2) | >=0 |
        // PGR_VLRCSLL      NUM(15,2) NN          | round(2) | >=0 |
        // PGR_CODCC        VC(15) NN             | Trigger resolve se for NULL
        // PGR_CODSNF       INT NN                | >=0 |
        // PGR_DTMOVTO      DAT DEF GETDATE() NN  | Campo default
        // PGR_APR          VC(1) NN              | upper | in(S,N)
        // PGR_CODEMP       INT NN                | >0 | SELECT |
        // PGR_CODFLL       INT NN                | >0 | SELECT |
        // PGR_LOTECNAB     INT NN                | 
        // PGR_CODCNTT      INT NN                |         
        // PGR_VERDIREITO   INT NN                | in(26,27,28)
        // PGR_CODCMP       INT NN                |
        // PGR_CODALT       INT NN                |
        // PGR_REG          VC(1) NN              | upper | in(P,A,S)
        // PGR_CODUSR       INT NN                |
        //
        // NFS_NUMNF        INT NN                |
        // NFS_CODSNF       INT NN                |
        // NFS_GUIA         INT NN                |
        // NFS_VLRTOTAL     NUM(15,2) NN          |
        // NFS_VLRRETENCAO  NUM(15,2) NN          |
        // NFS_CODCMP       INT NN                |
        // NFS_CODVND       INT NN                |
        // NFS_LOTENFE      INT NN                |
        // NFS_CODCDD       VC(7) NN              |
        // NFS_CONTRATO     INT NN                |
        // NFS_ENTSAI       VC(1) NN              |
        ////////////////////////////////////////////////////////
        //////////////////////////////////////////////
        // Variaveis para validacao antes de gravar BD  
        // PAGAR
        //////////////////////////////////////////////
        $apr          = ( isset($lote[0]->apr)          ? strtoupper($lote[0]->apr) : "S"         );        
        $bloqueado    = ( isset($lote[0]->bloqueado)    ? strtoupper($lote[0]->blqueado) : "N"    );
        $cheque       = ( isset($lote[0]->cheque)       ? strtoupper($lote[0]->cheque) : "NULL"   );
        $codalt       = ( isset($lote[0]->codalt)       ? $lote[0]->codalt      : 0               );    //Ver codigos no metadata(trigger)
        $codbnc       = ( isset($lote[0]->codbnc)       ? $lote[0]->codbnc      : 0               );
        $codcc        = ( isset($lote[0]->codcc)        ? $lote[0]->codcc       : "NULL"          );    //Tabela CONTACONTABIL        
        $codcmp       = ( isset($lote[0]->codcmp)       ? $lote[0]->codcmp      : 0               );    //Competencia contabil
        $codcntt      = ( isset($lote[0]->codcntt)      ? $lote[0]->codcntt     : 0               );    //Codigo do contrato
        $codemp       = $_SESSION["emp_codigo"];                                                        //Tabela EMPRESA
        $codfc        = ( isset($lote[0]->codfc)        ? strtoupper($lote[0]->codfc) : "NULL"    );    //Tabela FORMACOBRABCA    
        $codfll       = ( isset($lote[0]->codfll)       ? $lote[0]->codfll      : 0               );    //Tabela FILIAL    
        $codfvr       = ( isset($lote[0]->codfvr)       ? $lote[0]->codfvr      : 0               );    //Tabela FAVORECIDO
        $codpt        = ( isset($lote[0]->codpt)        ? $lote[0]->codpt       : 0               );    //Tabela PADRAOTITULO    
        $codptp       = ( isset($lote[0]->codptp)       ? strtoupper($lote[0]->codptp) : "NULL"   );    //Tabela PAGARTIPO    
        $codptt       = ( isset($lote[0]->codptt)       ? strtoupper($lote[0]->codptt) : "NULL"   );    //Tabela PAGARTITULO    
        $codsnf       = ( isset($lote[0]->codsnf)       ? $lote[0]->codsnf      : 0               );    //Tabela SERIENF
        $codtd        = ( isset($lote[0]->codtd)        ? strtoupper($lote[0]->codtd) : "NULL"    );    //Tabela TIPODOCUMENTO    
        $codusr       = $_SESSION["usr_codigo"];                                                        //Tabela USUARIO
        $datapaga     = ( isset($lote[0]->datapaga)     ? $lote[0]->datapaga    : "NULL"          );        
        $docto        = ( isset($lote[0]->docto)        ? strtoupper($lote[0]->docto) : "NULL"    );
        $dtdocto      = ( isset($lote[0]->dtdocto)      ? $lote[0]->dtdocto     : "NULL"          );        
        $erro         = "ok";        
        $lancto       = ( isset($lote[0]->lancto)       ? $lote[0]->lancto      : 0               );    //AQUI PODE VIR LANCTO PARA ALTERACAO
        $master       = ( isset($lote[0]->master)       ? $lote[0]->master      : 0               );
        $lotecnab     = ( isset($lote[0]->lotecnab)     ? $lote[0]->lotecnab             : 0      );                
        $observacao   = ( isset($lote[0]->observacao)   ? strtoupper($lote[0]->observacao): "NULL");        
        $reg          = ( isset($lote[0]->reg)          ? strtoupper($lote[0]->reg) : "P"         );        
        $status       = ( isset($lote[0]->status)       ? strtoupper($lote[0]->status)    : "INC" );    //INC/ALT 
        $temnfp       = ( isset($lote[0]->temnfp)       ? strtoupper($lote[0]->temnfp)   : "N"    );        
        $temnfs       = ( isset($lote[0]->temnfs)       ? strtoupper($lote[0]->temnfs)   : "N"    );        
        $vencto       = ( isset($lote[0]->vencto)       ? $lote[0]->vencto               : "NULL" );        
        $vlrcofins    = ( isset($lote[0]->vlrcofins)    ? round($lote[0]->vlrcofins,2)   : 0      );        
        $vlrcsll      = ( isset($lote[0]->vlrcsll)      ? round($lote[0]->vlrcsll,2)     : 0      );        
        $vlrdesconto  = ( isset($lote[0]->vlrdesconto)  ? round($lote[0]->vlrdesconto,2) : 0      );
        $vlrevento    = ( isset($lote[0]->vlrevento)    ? round($lote[0]->vlrevento,2)   : 0      );
        $vlrbaixa     = ( isset($lote[0]->vlrbaixa)     ? round($lote[0]->vlrbaixa,2)    : 0      );    //Usado para desmembrar lancto e baixa parcial
        $vlrmulta     = ( isset($lote[0]->vlrmulta)     ? round($lote[0]->vlrmulta,2)    : 0      );
        $vlrparcela   = ( isset($lote[0]->vlrparcela)   ? round($lote[0]->vlrparcela,2)  : 0      );    //Necessario qdo desmembramento     
        $vlrpis       = ( isset($lote[0]->vlrpis)       ? round($lote[0]->vlrpis,2)      : 0      );                
        $vlrretencao  = ( isset($lote[0]->vlrretencao)  ? round($lote[0]->vlrretencao,2) : 0      );
        $verdireito   = ( isset($lote[0]->verdireito)   ? $lote[0]->verdireito           : 0      );    //Checado
        ///////////////////////////////////////////////////////////////////////////////////////////////
        // Devido a direitos de usuario a variavel $verdireito soh pode receber 26/27/28(tabela PERFIL) 
        // Estes valores estaum no trigger
        ///////////////////////////////////////////////////////////////////////////////////////////////
        if( ($verdireito<>26) and ($verdireito<>27) and ($verdireito<>28) )
          $erro="Campo verdireito aceita apenas 26/27/28!";
        //////////////////////////////////////
        // Obrigatorio JSON rateio e duplicata
        //////////////////////////////////////
        if( !isset($lote[0]->RATEIO) ){
          $erro="Não localizado contabilização para este lançamento!";    
        } else {
          $objRat=$lote[0]->RATEIO;
        };  
        if( !isset($lote[0]->DUPLICATA) ){
          $erro="Não localizado duplicata(s) para este lançamento!"; 
        } else {
          $objDup=$lote[0]->DUPLICATA;
          $tamDup=count($objDup);          
        }
        if( $temnfs=="S" ){
          if( !isset($lote[0]->SERVICO) ){
            $erro="Não localizado servico para este lançamento!"; 
          } else {
            $objNfs=$lote[0]->SERVICO;
            $tamNfs=count($objNfs);          
          };
        };
        if( $temnfp=="S" ){
          if( !isset($lote[0]->PRODUTO) ){
            $erro="Não localizado produto para este lançamento!"; 
          } else {
            $objNfp=$lote[0]->PRODUTO;
            $tamNfp=count($objNfp);          
          };
        };
        //
        ///////////////////////////////
        // Ver se novo titulo $lancto=0
        //          alteracao $lancto>0
        ///////////////////////////////
        if( ($lancto>0) && ($erro=="ok") ) {
          $status   = "ALT";
        };
        
        if( $erro=="ok" ){
          $vlrRateio  = 0;
          $objRat     = $lote[0]->RATEIO;
          foreach ( $objRat as $rat ){
            $vlrRateio+=$rat->debito+$rat->credito;
          }
          $vlrRateio=round($vlrRateio,2);
          //////////////////////////////////////////////////////////
          // Preciso do vlrevento e vlrparcela entaum esta separacao 
          //////////////////////////////////////////////////////////
          if( $status=="INC" ){
            if( $vlrevento <> $vlrRateio )          
              $erro="Valor evento ".$vlrevento." diverge do valor contabil ".$vlrRateio;  
          };
          if( $status=="ALT" ){
            if( $vlrparcela <> $vlrRateio )          
              $erro="Valor parcela ".$vlrevento." diverge do valor contabil ".$vlrRateio;  
          };
        };
        //
        ////////////////
        // PGR_APR
        ////////////////
        if( !preg_match("/^(S|N)$/",$apr) )
          $erro = "CAMPO APROVADO ACEITA S/N";  
        //
        ////////////////
        // PGR_BLOQUEADO
        ////////////////
        if( !preg_match("/^(S|N)$/",$bloqueado) )
          $erro = "CAMPO BLOQUEADO ACEITA S/N";  
        //
        /////////////////////
        // PGR_CODEMP
        /////////////////////
        if( $codemp<=0 )
          $erro="Campo codigo da empresa obrigatorio";
        //
        /////////////////////
        // PGR_CODFLL
        /////////////////////
        if( $codfll<=0 )
          $erro="Campo codigo da filial obrigatorio";
        //
        /////////////////////
        // PGR_CODSNF
        /////////////////////
        if( $codsnf<0 )
          $erro="Serie NF não pode ser menor que 0(Zero)";
        //
        //////////////////
        // PGR_DOCUMENTO
        //////////////////
        $clsRa->montaRetorno($docto); 
        $docto  = $clsRa->getNome();
        $docto  = str_replace(" ", "", $docto);
        if( strlen($docto)==0 )
          $erro="Obrigatorio campo documento";
        if( strlen($docto)>12 )
          $erro="Campo documento deve ter tamanho maximo de 12 posições";
        //
        //////////////////
        // PGR_OBSERVACAO
        //////////////////
        $clsRa->montaRetorno($observacao); 
        $observacao=$clsRa->getNome();
        if( strlen($observacao)==0 )
          $erro="Obrigatorio campo observacao";
        if( strlen($observacao)>120 )
          $erro="Campo observacao deve ter tamanho maximo de 120 posições";
        //
        ////////////////
        // PGR_REG
        ////////////////
        if( !preg_match("/^(P|A|S)$/",$reg) )
          $erro = "CAMPO REGISTRO ACEITA P/A/S";  
        //
        ////////////////////////////////////////////////////////
        // PGR_VLRDESCONTO - Atualizando PGR_CODALT para trigger
        ////////////////////////////////////////////////////////
        if( $vlrdesconto<0 )
          $erro="Valor desconto não pode ser menor que 0,00(Zero)";
        if( ($vlrdesconto>0) and ($vlrmulta>0) )
          $erro="Um titulo não pode ter desconto e multa!";
        if( ($vlrdesconto>0) and ($status=="ALT") ) {
          if( ($codptp=="CP") or ($codptp=="PP") or ($codptp=="MP") )
            $codalt=1;
          if( ($codptp=="CR") or ($codptp=="PR") or ($codptp=="MR") )
            $codalt=2;
        };
        //
        /////////////////////
        // PGR_VLREVENTO
        /////////////////////
        if( $vlrevento<0 )
          $erro="Valor evento não pode ser menor que 0,00(Zero)";
        if( $vlrevento<$vlrparcela )        
          $erro="Valor evento não pode ser menor que o valor da parcela";          
        //
        /////////////////////
        // PGR_VLRPARCELA
        /////////////////////
        if( $vlrparcela<0 )
          $erro="Valor parcela não pode ser menor que 0,00(Zero)";
        //
        /////////////////////
        // PGR_VLRMULTA
        /////////////////////
        if( $vlrmulta<0 )
          $erro="Valor multa não pode ser menor que 0,00(Zero)";
        if( ($vlrmulta>0) and ($status=="ALT") ) {
          if( ($codptp=="CP") or ($codptp=="PP") or ($codptp=="MP") )
            $codalt=3;
          if( ($codptp=="CR") or ($codptp=="PR") or ($codptp=="MR") )
            $codalt=4;
        };
        //
        /////////////////////
        // PGR_VLRRETENCAO
        /////////////////////
        if( $vlrretencao<0 )
          $erro="Valor retencao não pode ser menor que 0,00(Zero)";
        //
        /////////////////////
        // PGR_VLRPIS
        /////////////////////
        if( $vlrpis<0 )
          $erro="Valor pis não pode ser menor que 0,00(Zero)";
        //
        /////////////////////
        // PGR_VLRCOFINS
        /////////////////////
        if( $vlrcofins<0 )
          $erro="Valor cofins não pode ser menor que 0,00(Zero)";
        //
        /////////////////////
        // PGR_VLRCSLL
        /////////////////////
        if( $vlrcsll<0 )
          $erro="Valor csll não pode ser menor que 0,00(Zero)";
        //
        //
        //////////////////////////////
        // Obrigatorio contabilizacaum
        //////////////////////////////
        if( $erro=="ok" ){
          $sql="";
          $sql.="SELECT A.FLL_CODIGO";
          $sql.="       ,A.FLL_CODEMP";
          $sql.="  FROM FILIAL A";
          $sql.=" WHERE ((A.FLL_CODIGO=".$codfll.") AND (A.FLL_ATIVO='S') AND (A.FLL_CODEMP=".$codemp."))";
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 )        
            $erro="Não localizado filial ".$codfll." para este lançamento!";  
          /////////////////////////////////////////////////
          // Relacionamento obrigatorio PAGARTIPO->CONTABIL
          /////////////////////////////////////////////////
          $sql="";
          $sql.="SELECT A.PTP_CONTABIL FROM PAGARTIPO A WHERE A.PTP_CODIGO='".$codptp."' AND A.PTP_ATIVO='S'";
          $classe->msgSelect(true);
          $retCls=$classe->selectAssoc($sql);
          if( $retCls["qtos"]==0 ){        
            $erro="Não localizado tipo ".$codptp." para este lançamento!";  
          } else {
            $contabil=$retCls["dados"][0]["PTP_CONTABIL"];
          };  
        };  
        //
        //
        //
        //
        //////////////////////////////////////
        // Aqui comeca a checagem se tiver NFS
        //////////////////////////////////////
        if( ($temnfs=="S") and ($erro=="ok") ){
          //file_put_contents("aaa.xml",print_r($objNfs,true));  
          $numnf        = ( isset($objNfs[0]->numnf)        ? $objNfs[0]->numnf                 : 0   );  // >0
          $codsnf       = ( isset($objNfs[0]->codsnf)       ? $objNfs[0]->codsnf                : 0   );  // >0
          $vlrretencao  = ( isset($objNfs[0]->vlrretencao)  ? round($objNfs[0]->vlrretencao,2)  : 0   );
          //$livro        = ( isset($objNfs[0]->livro)        ? strtoupper($objNfs[0]->livro)     : "*" );  // in(S,N)
          $codvnd       = ( isset($objNfs[0]->codvnd)       ? $objNfs[0]->codvnd                : 0   );
          $codcdd       = ( isset($objNfs[0]->codcdd)       ? $objNfs[0]->codcdd                : "*" );  // tamanho=7
          $contrato     = ( isset($objNfs[0]->contrato)     ? $objNfs[0]->contrato              : 0   );
          $cstpis       = ( isset($objNfs[0]->cstpis)       ? $objNfs[0]->cstpis                : "*" );          
          $entsai       = ( isset($objNfs[0]->entsai)       ? strtoupper($objNfs[0]->entsai)    : "*" );  // in(E,S)
          $codsrv       = ( isset($objNfs[0]->codsrv)       ? $objNfs[0]->codsrv                : 0   );  // >0
          $aliqinss     = ( isset($objNfs[0]->aliqinss)     ? round($objNfs[0]->aliqinss,2)     : 0   );
          $bcinss       = ( isset($objNfs[0]->bcinss)       ? round($objNfs[0]->bcinss,2)       : 0   );
          $vlrinss      = ( isset($objNfs[0]->vlrinss)      ? round($objNfs[0]->vlrinss,2)      : 0   );
          $aliqirrf     = ( isset($objNfs[0]->aliqirrf)     ? round($objNfs[0]->aliqirrf,2)     : 0   );
          $vlrirrf      = ( isset($objNfs[0]->vlrirrf)      ? round($objNfs[0]->vlrirrf,2)      : 0   );
          $aliqpis      = ( isset($objNfs[0]->aliqpis)      ? round($objNfs[0]->aliqpis,2)      : 0   );
          $vlrpis       = ( isset($objNfs[0]->vlrpis)       ? round($objNfs[0]->vlrpis,2)       : 0   );
          $aliqcofins   = ( isset($objNfs[0]->aliqcofins)   ? round($objNfs[0]->aliqcofins,2)   : 0   );
          $vlrcofins    = ( isset($objNfs[0]->vlrcofins)    ? round($objNfs[0]->vlrcofins,2)    : 0   );
          $aliqcsll     = ( isset($objNfs[0]->aliqcsll)     ? round($objNfs[0]->aliqcsll,2)     : 0   );
          $vlrcsll      = ( isset($objNfs[0]->vlrcsll)      ? round($objNfs[0]->vlrcsll,2)      : 0   );
          $aliqiss      = ( isset($objNfs[0]->aliqiss)      ? round($objNfs[0]->aliqiss,2)      : 0   );
          $vlriss       = ( isset($objNfs[0]->vlriss)       ? round($objNfs[0]->vlriss,2)       : 0   );
          $informarnf   = ( isset($objNfs[0]->informarnf)   ? strtoupper($objNfs[0]->informarnf): "*" );  // in(S,N)
          $opcao        = ( isset($objNfs[0]->opcao)        ? strtoupper($objNfs[0]->opcao)     : "*" );  // in(NFS,REC,RPS)          
          ////////////////
          // cstpis
          ////////////////          
          if( $entsai=="S" ){
            if($vlrpis>0){
              $cstpis="01";
            } else {
              $cstpis="07";  
            };  
          };
          if( !preg_match("/^(01|07|08|50|70)$/",$cstpis) )
            $erro = "CST PIS ACEITA APENAS 01|07|08|50|70";  
          ////////////////
          // codcdd
          ////////////////          
          $codcdd=preg_replace("/[^0-9]/","",$codcdd);
          if( strlen($codcdd) !=7 )          
            $erro="Tamanho do campo CODIGO_CIDADE deve ser 7(Sete)";  
          ////////////////          
          // codsnf
          ////////////////          
          if( $codsnf<=0 )
            $erro="Serie da NF deve ser maior que 0(Zero)";
          ////////////////          
          // codsrv
          ////////////////          
          if( $codsrv<=0 )
            $erro="Codigo do servico deve ser maior que 0(Zero)";
          ////////////////          
          // entsai
          ////////////////          
          if( !preg_match("/^(E|S)$/",$entsai) )
            $erro = "CAMPO ENTRADA/SAIDA ACEITA E/S";  
          ////////////////          
          // informarnf
          ////////////////          
          if( !preg_match("/^(S|N)$/",$informarnf) )
            $erro = "CAMPO INFORMARNF ACEITA S/N";  
          ////////////////          
          // livro
          ////////////////          
          //if( !preg_match("/^(S|N)$/",$livro) )
          //  $erro = "CAMPO LIVRO ACEITA S/N";  
          ////////////////          
          // numnf
          ////////////////          
          if( $numnf<=0 )
            $erro="Numero da NF deve ser maior que 0(Zero)";
          ////////////////          
          // opcao
          ////////////////          
          if( !preg_match("/^(REC|NFS|RPS)$/",$opcao) )
            $erro = "CAMPO OPCAO ACEITA NFS/REC/RPS";  
          //
          ///////////////////////////////////////////////////////////////////////////////////
          // Buscando o numero da NF devido multi-usuarios
          // O valor da retencao passa a ser agora (-) PIS/COFINS/CSLL devido campos na PAGAR
          ///////////////////////////////////////////////////////////////////////////////////
          if( $erro=="ok" ){
            $numnf        = $classe->numeronf($codsnf);
            $docto        = $opcao.str_pad($numnf, 6, "0", STR_PAD_LEFT);
            $vlrretencao  = ($vlrretencao-$vlrpis-$vlrcofins-$vlrcsll);
          };  
        };
        //
        //  
        if( $erro<>"ok" ){
          $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';  
        };
        ///////////////////////
        // Se estiver incluindo
        ///////////////////////
        if( ($erro=="ok") and ($status=="INC") ){
          $lancto  = 0;
          $master  = 0;
          $parcela = 1; //Retencao,pis,cofins e csll soh grava na primeira parcela
          //
          foreach ( $objDup as $dup ){
            ///////////////////////
            // Buscando o generator
            ///////////////////////
            $lancto=$classe->generator("PAGAR");          
            if( $master==0 )
              $master=$lancto;
            //
            /////////////////////////////////////////////////
            // Se for uma tarifa mudo o docto/cheque/datapaga
            /////////////////////////////////////////////////
            if( $codptt=="N" ){
              $docto    = "TAR".str_pad($lancto, 6, "0", STR_PAD_LEFT);
              $cheque   = $docto;
              $datapaga = $dtdocto;
            };
            //
            $sql="";
            $sql="INSERT INTO VPAGAR(";
            $sql.="PGR_LANCTO";
            $sql.=",PGR_BLOQUEADO";
            $sql.=",PGR_CHEQUE";
            $sql.=",PGR_CODBNC";
            $sql.=",PGR_CODFVR";
            $sql.=",PGR_CODFC";
            $sql.=",PGR_CODTD";
            $sql.=",PGR_VENCTO";
            $sql.=",PGR_DATAPAGA";
            $sql.=",PGR_DOCTO";
            $sql.=",PGR_DTDOCTO";
            $sql.=",PGR_CODPTT";
            $sql.=",PGR_MASTER";
            $sql.=",PGR_OBSERVACAO";
            $sql.=",PGR_CODPTP";
            $sql.=",PGR_CODPT";
            $sql.=",PGR_VLRDESCONTO";
            $sql.=",PGR_VLREVENTO";
            $sql.=",PGR_VLRPARCELA";
            $sql.=",PGR_VLRMULTA";
            $sql.=",PGR_VLRRETENCAO";
            $sql.=",PGR_VLRPIS";
            $sql.=",PGR_VLRCOFINS";
            $sql.=",PGR_VLRCSLL";
            $sql.=",PGR_CODCC";
            $sql.=",PGR_CODSNF";
            $sql.=",PGR_APR";
            $sql.=",PGR_CODEMP";
            $sql.=",PGR_CODFLL";
            $sql.=",PGR_LOTECNAB";
            $sql.=",PGR_CODCNTT";            
            $sql.=",PGR_VERDIREITO";
            $sql.=",PGR_CODCMP";
            $sql.=",PGR_REG";
            $sql.=",PGR_CODUSR) VALUES(";
            $sql.="'$lancto'";                                              // PGR_LANCTO
            $sql.=",'" .$bloqueado."'";                                     // BLOQUEADO
            $sql.=","  .($cheque=="NULL" ? 'null' : "'".$cheque."'" );      // CHEQUE
            $sql.=","  .$codbnc;                                            // CODBNC
            $sql.=","  .$codfvr;                                            // CODFVR
            $sql.=",'" .$codfc."'";                                         // CODFC
            $sql.=",'" .$codtd."'";                                         // CODTD
            $sql.=",'" .$dup->vencto."'";                                   // VENCTO
            $sql.=","  .($datapaga=="NULL" ? 'null' : "'".$datapaga."'" );  // DATAPAGA
            $sql.=",'" .$docto."'";                                         // DOCTO
            $sql.=",'" .$dtdocto."'";                                       // DTDOCTO
            $sql.=",'" .$codptt."'";                                        // CODPTT
            $sql.=","  .$master;                                            // MASTER
            $sql.=",'" .$observacao."'";                                    // OBSERVACAO
            $sql.=",'" .$codptp."'";                                        // CODPTP
            $sql.=","  .$codpt;                                             // CODPT
            $sql.=",'" .$vlrdesconto."'";                                   // VLRDESCONTO
            $sql.=",'" .$vlrevento."'";                                     // VLREVENTO
            $sql.=",'" .$dup->vlrparcela."'";                               // VLRPARCELA
            $sql.=",'" .$vlrmulta."'";                                      // VLRMULTA
            $sql.=",'" .($parcela==1 ? $vlrretencao : 0)."'";               // VLRRETENCAO
            $sql.=",'" .($parcela==1 ? $vlrpis : 0)."'";                    // VLRPIS
            $sql.=",'" .($parcela==1 ? $vlrcofins : 0)."'";                 // VLRCOFINS
            $sql.=",'" .($parcela==1 ? $vlrcsll : 0)."'";                   // VLRCSLL
            $sql.=","  .($codcc=="NULL" ? 'null' : "'".$codcc."'" );        // CODCC
            $sql.=","  .$codsnf;                                            // CODSNF
            $sql.=",'" .$apr."'";                                           // APR
            $sql.=","  .$codemp;                                            // CODEMP
            $sql.=","  .$codfll;                                            // CODFLL
            $sql.=","  .$lotecnab;                                          // LOTECNAB
            $sql.=","  .$codcntt;                                           // CODCNTT
            $sql.=","  .$verdireito;                                        // VERDIREITO
            $sql.=","  .$codcmp;                                            // CODCMP -- Referencia para RATEIO
            $sql.=",'" .$reg."'";                                           // REG
            $sql.=","  .$codusr;                                            // CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);            

            $achei      = false;
            $vlrRateio  = 0;  
            foreach ( $objRat as $rat ){
              if( $rat->parcela==$dup->parcela ){
                $sql="";
                $sql.="INSERT INTO VRATEIO(";
                $sql.="RAT_LANCTO";                
                $sql.=",RAT_CODCC";
                $sql.=",RAT_DEBITO";
                $sql.=",RAT_CREDITO";
                $sql.=",RAT_CODEMP";
                $sql.=",RAT_CODCMP";
                $sql.=",RAT_CONTABIL";
                $sql.=",RAT_CODUSR) VALUES(";
                $sql.="'$lancto'";              // RAT_LANCTO
                $sql.=",'" .$rat->codcc."'";    // RAT_CODCC
                $sql.="," .$rat->debito;        // RAT_DEBITO
                $sql.="," .$rat->credito;       // RAT_CREDITO
                $sql.="," .$codemp;             // RAT_CODEMP
                $sql.="," .$codcmp;             // RAT_CODCMP
                $sql.=",'".$contabil."'";       // RAT_CONTABIL
                $sql.=","  .$codusr;            // RAT_CODUSR
                $sql.=")";
                array_push($arrUpdt,$sql);            
                $vlrRateio+=($rat->debito-$rat->credito);
              };
            };  
            $vlrRateio=abs(round($vlrRateio,2));
            if( $dup->vlrparcela <> $vlrRateio )          
              $erro="Parcela ".$dup->parcela." Valor da parcela".$dup->vlrparcela." diverge do valor contabil ".$vlrRateio;  
            $parcela++;
          };
          
          //////////////
          // NF SERVICO
          /////////////
          if( $temnfs=="S" ){
            $sql="";
            $sql.="INSERT INTO NFSERVICO(";
            $sql.="NFS_NUMNF";
            $sql.=",NFS_CODSNF";
            $sql.=",NFS_LANCTO";
            $sql.=",NFS_VLRTOTAL";
            $sql.=",NFS_VLRRETENCAO";
            $sql.=",NFS_CODCMP";
            //$sql.=",NFS_LIVRO";
            $sql.=",NFS_CODVND";
            $sql.=",NFS_LOTENFE";
            $sql.=",NFS_NUMORIGEM";
            $sql.=",NFS_CODCDD";
            $sql.=",NFS_CONTRATO";
            $sql.=",NFS_ENTSAI";
            //$sql.=",NFS_ATIVO";
            $sql.=",NFS_REG";
            $sql.=",NFS_CODUSR) VALUES(";
            $sql.="'$numnf'";         // NFS_NUMNF
            $sql.=",".$codsnf;        // NFS_CODSNF
            $sql.=",".$master;        // NFS_GUIA
            $sql.=",".$vlrevento;     // NFS_VLRTOTAL
            $sql.=",".$vlrretencao;   // NFS_VLRRETENCAO
            $sql.=",".$codcmp;        // NFS_CODCMP
            //$sql.=",'".$livro."'";    // NFS_LIVRO
            $sql.=",".$codvnd;        // NFS_CODVND
            $sql.=",0";               // NFS_LOTENFE
            $sql.=",0";               // NFS_NUMORIGEM
            $sql.=",'".$codcdd."'";   // NFS_CODCDD
            $sql.=",".$contrato;      // NFS_CONTRATO
            $sql.=",'".$entsai."'";   // NFS_ENTSAI
            //$sql.=",'S'";             // NFS_ATIVO
            $sql.=",'P'";             // NFS_REG
            $sql.=",".$codusr;        // NFS_CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);

            $sql="";
            $sql.="INSERT INTO NFSERVICOITEM(";
            $sql.="NFSI_LANCTO";
            $sql.=",NFSI_ITEM";
            $sql.=",NFSI_CODSRV";
            $sql.=",NFSI_UNIDADES";
            $sql.=",NFSI_VLRUNITARIO";
            $sql.=",NFSI_VLRITEM";
            $sql.=",NFSI_VLRDESCONTO";
            $sql.=",NFSI_ALIQINSS";
            $sql.=",NFSI_BCINSS";
            $sql.=",NFSI_VLRINSS";
            $sql.=",NFSI_ALIQIRRF";
            $sql.=",NFSI_BCIRRF";
            $sql.=",NFSI_VLRIRRF";
            $sql.=",NFSI_ALIQPIS";
            $sql.=",NFSI_BCPIS";
            $sql.=",NFSI_VLRPIS";
            $sql.=",NFSI_ALIQCOFINS";
            $sql.=",NFSI_BCCOFINS";
            $sql.=",NFSI_VLRCOFINS";
            $sql.=",NFSI_ALIQCSLL";
            $sql.=",NFSI_BCCSLL";
            $sql.=",NFSI_VLRCSLL";
            $sql.=",NFSI_ALIQISS";
            $sql.=",NFSI_BCISS";
            $sql.=",NFSI_VLRISS";
            $sql.=",NFSI_CSTPIS";
            $sql.=",NFSI_CSTCOFINS";
            $sql.=",NFSI_ENTSAI";
            $sql.=",NFSI_ATIVO";
            $sql.=",NFSI_REG";
            $sql.=",NFSI_CODUSR) VALUES(";
            $sql.="'$master'";          // NFSI_GUIA
            $sql.=",1";                 // NFSI_ITEM
            $sql.=",".$codsrv;          // NFSI_CODSRV
            $sql.=",1";                 // NFSI_UNIDADES
            $sql.=",".$vlrevento;       // NFSI_VLRUNITARIO
            $sql.=",".$vlrevento;       // NFSI_VLRITEM
            $sql.=",0";                 // NFSI_VLRDESCONTO
            $sql.=",".$aliqinss;        // NFSI_ALIQINSS
            $sql.=",".$bcinss;          // NFSI_BCINSS
            $sql.=",".$vlrinss;         // NFSI_VLRINSS
            $sql.=",".$aliqirrf;        // NFSI_ALIQIRRF
            $sql.=",".$bcinss;          // NFSI_BCIRRF
            $sql.=",".$vlrirrf;         // NFSI_VLRIRRF
            $sql.=",".$aliqpis;         // NFSI_ALIQPIS
            $sql.=",".$bcinss;          // NFSI_BCPIS
            $sql.=",".$vlrpis;          // NFSI_VLRPIS
            $sql.=",".$aliqcofins;      // NFSI_ALIQCOFINS
            $sql.=",".$bcinss;          // NFSI_BCCOFINS
            $sql.=",".$vlrcofins;       // NFSI_VLRCOFINS
            $sql.=",".$aliqcsll;        // NFSI_ALIQCSLL
            $sql.=",".$bcinss;          // NFSI_BCCSLL
            $sql.=",".$vlrcsll;         // NFSI_VLRCSLL
            $sql.=",".$aliqiss;         // NFSI_ALIQISS
            $sql.=",".$bcinss;          // NFSI_BCISS
            $sql.=",".$vlriss;          // NFSI_VLRISS
            $sql.=",'".$cstpis."'";     // NFSI_CSTPIS
            $sql.=",'".$cstpis."'";     // NFSI_CSTCOFINS
            $sql.=",'".$entsai."'";     // NFSI_ENTSAI
            $sql.=",'S'";               // NFSI_ATIVO
            $sql.=",'P'";               // NFSI_REG
            $sql.=",".$codusr;          // NFSI_CODUSR
            $sql.=")";
            array_push($arrUpdt,$sql);
          };
          if( $erro == "ok" ){
            $atuBd=true;  
          } else {
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';   
          };
        };
//file_put_contents("aaa.xml","erro ".$erro."  status=".$status);                                
        ///////////////////////
        // Se estiver alterando
        //////////////////////////////////////////////////////////////////////////////
        // Buscando o campo PGR_CODALT - Este tem que conferir novamente com o trigger
        //////////////////////////////////////////////////////////////////////////////
        // 01 - Desconto CP/PP/MP
        // 02 - Desconto CR/PR/MR
        if( ($erro=="ok") and ($status=="ALT") ){
          $contador=1;
          
          foreach ( $objDup as $dup ){
            if( $contador==1 ){
              ////////////////////////////////////////////////////////////////////////////////////////////////////////////
              // Se existir mais de uma parcela eh pq o lancto foi parcelado, a primeira parcela tem que ter um abatimento
              ////////////////////////////////////////////////////////////////////////////////////////////////////////////
              if($tamDup>1){
                $vlrbaixa=round(($vlrparcela-$dup->vlrparcela),2);
              };
              
              $sql="";
              $sql.="UPDATE VPAGAR";
              $sql.="   SET PGR_CODBNC=".$codbnc;
              $sql.="       ,PGR_CODFVR=".$codfvr;
              $sql.="       ,PGR_CODFC='".$codfc."'";
              $sql.="       ,PGR_CODTD='".$codtd."'";
              $sql.="       ,PGR_VENCTO='".$vencto."'";
              $sql.="       ,PGR_DOCTO='".$docto."'";
              $sql.="       ,PGR_DTDOCTO='".$dtdocto."'";
              $sql.="       ,PGR_OBSERVACAO='".$observacao."'";
              $sql.="       ,PGR_CODPTT='D'";              
              $sql.="       ,PGR_VLRBAIXA='".$vlrbaixa."'";
              $sql.="       ,PGR_VLRMULTA='".$vlrmulta."'";
              $sql.="       ,PGR_CODCC=".($codcc=="NULL" ? 'null' : "'".$codcc."'" );
              $sql.="       ,PGR_CODCMP=".$codcmp;
              $sql.="       ,PGR_CODALT=".$codalt;              
              $sql.="       ,PGR_CODUSR='".$codusr."'";
              $sql.=" WHERE PGR_LANCTO=".$lancto;
              array_push($arrUpdt,$sql);
              $observacao=substr("LANCTO ".$lancto." ".$observacao,0,120);
              
            } else {
              ///////////////////////
              // Buscando o generator
              ///////////////////////
              $lancto=$classe->generator("PAGAR");          
              //
              //$parcela++;
              //
              $sql="";
              $sql="INSERT INTO VPAGAR(";
              $sql.="PGR_LANCTO";
              $sql.=",PGR_BLOQUEADO";
              $sql.=",PGR_CODBNC";
              $sql.=",PGR_CODFVR";
              $sql.=",PGR_CODFC";
              $sql.=",PGR_CODTD";
              $sql.=",PGR_VENCTO";
              $sql.=",PGR_DOCTO";
              $sql.=",PGR_DTDOCTO";
              $sql.=",PGR_CODPTT";
              $sql.=",PGR_MASTER";
              $sql.=",PGR_OBSERVACAO";
              $sql.=",PGR_CODPTP";
              $sql.=",PGR_CODPT";
              $sql.=",PGR_VLREVENTO";
              $sql.=",PGR_VLRPARCELA";
              $sql.=",PGR_CODCC";
              $sql.=",PGR_APR";
              $sql.=",PGR_CODEMP";
              $sql.=",PGR_CODFLL";
              $sql.=",PGR_LOTECNAB";
              $sql.=",PGR_CODCNTT";              
              $sql.=",PGR_VERDIREITO";
              $sql.=",PGR_CODCMP";
              $sql.=",PGR_REG";
              $sql.=",PGR_CODUSR) VALUES(";
              $sql.="'$lancto'";                                              // PGR_LANCTO
              $sql.=",'N'";                                                   // BLOQUEADO - Titulo bloqueado naum pode ser alterado
              $sql.=","  .$codbnc;                                            // CODBNC
              $sql.=","  .$codfvr;                                            // CODFVR
              $sql.=",'" .$codfc."'";                                         // CODFC
              $sql.=",'" .$codtd."'";                                         // CODTD
              $sql.=",'" .$dup->vencto."'";                                   // VENCTO
              $sql.=",'" .$docto."'";                                         // DOCTO
              $sql.=",'" .$dtdocto."'";                                       // DTDOCTO
              $sql.=",'" .$codptt."'";                                        // CODPTT
              $sql.=","  .$master;                                            // MASTER
              $sql.=",'" .$observacao."'";                                    // OBSERVACAO
              $sql.=",'" .$codptp."'";                                        // CODPTP
              $sql.=","  .$codpt;                                             // CODPT
              $sql.=",'" .$vlrevento."'";                                     // VLREVENTO
              $sql.=",'" .$dup->vlrparcela."'";                               // VLRPARCELA
              $sql.=","  .($codcc=="NULL" ? 'null' : "'".$codcc."'" );        // CODCC
              $sql.=",'" .$apr."'";                                           // APR
              $sql.=","  .$codemp;                                            // CODEMP
              $sql.=","  .$codfll;                                            // CODFLL
              $sql.=",0";                                                     // LOTECNAB - Titulo em arquivo cnab naum pode ser alterado
              $sql.=","  .$codcntt;                                           // CODCNTT
              $sql.=","  .$verdireito;                                        // VERDIREITO
              $sql.=","  .$codcmp;                                            // CODCMP              
              $sql.=",'" .$reg."'";                                           // REG
              $sql.=","  .$codusr;                                            // CODUSR
              $sql.=")";
              array_push($arrUpdt,$sql);            
              
              $achei      = false;
              $vlrRateio  = 0;  
              foreach ( $objRat as $rat ){
                if( $rat->parcela==$dup->parcela ){
                  $sql="";
                  $sql.="INSERT INTO RATEIO(";
                  $sql.="RAT_LANCTO";                
                  $sql.=",RAT_CODCC";
                  $sql.=",RAT_DEBITO";
                  $sql.=",RAT_CREDITO";
                  $sql.=",RAT_CODEMP";
                  $sql.=",RAT_CODCMP";
                  $sql.=",RAT_CONTABIL";
                  $sql.=",RAT_CODUSR) VALUES(";
                  $sql.="'$lancto'";              // RAT_LANCTO
                  $sql.=",'" .$rat->codcc."'";    // RAT_CODCC
                  $sql.="," .$rat->debito;        // RAT_DEBITO
                  $sql.="," .$rat->credito;       // RAT_CREDITO
                  $sql.="," .$codemp;             // RAT_CODEMP
                  $sql.="," .$codcmp;             // RAT_CODCMP
                  $sql.=",'".$contabil."'";       // RAT_CONTABIL
                  $sql.=","  .$codusr;            // RAT_CODUSR
                  $sql.=")";
                  array_push($arrUpdt,$sql);            
                  $vlrRateio+=($rat->debito-$rat->credito);
                };
              };  
              $vlrRateio=abs(round($vlrRateio,2));
              if( $dup->vlrparcela <> $vlrRateio )          
                $erro="Parcela ".$dup->parcela." Valor da parcela".$dup->vlrparcela." diverge do valor contabil ".$vlrRateio;    
            };
            $contador++;  
          };

          if( $erro == "ok" ){
            $atuBd=true;  
          } else {
            $retorno='[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';   
          };
        };  
        //file_put_contents("aaa.xml",print_r($arrUpdt,true));
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
      };  
    } catch(Exception $e ){
      $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]'; 
    };    
    echo $retorno;
    exit;
  };  
?>