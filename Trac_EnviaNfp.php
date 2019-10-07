<?php
  session_start();

  require("classPhp/conectaSqlServer.class.php");
  require("classPhp/validaJson.class.php"); 
  require("classPhp/nfev4.php");
  // tratando os parametros recebidos (lancto e usuario)
  $vldr     = new validaJson();          
  $retorno  = "";
  $retCls   = $vldr->validarJs($_POST["nfp"]);
  if($retCls["retorno"] != "OK"){
    echo '[{"retorno":"ERR","dados":"","erro":"'.$retCls['erro'].'"}]';
    exit;
  }
  $call     = $retCls["dados"]->lote[0];
  $lancto   = $call->lancto;
  // --
        
  // criando o objeto
  $nota = new nota();

  // pegando os dados do emitente
  $db   = new conectaBd();
  $db->conecta('TRAC');
  $db->msgSelect(false);
  $ret=$db->selectAssoc(
    "select emp_nome,emp_cnpj,emp_insestad,emp_codcdd,emp_codlgr
           ,emp_endereco,emp_numero,emp_cep,emp_bairro,emp_fone
           ,emp_prodhomol,emp_contingencia,emp_certpath,emp_certsenha
           ,emp_codert,cdd_nome,cdd_codest
     from vEmpresa
     left outer join vCidade on cdd_codigo=emp_codcdd
     where emp_codigo=".$call->codemp
  );
  if( $ret["qtos"]==0 ){
    echo '[{"retorno":"ERR","dados":"","erro":"EMITENTE - NENHUM REGISTRO LOCALIZADO"}]';  
    exit;
  }
  $emit = $ret["dados"][0];
  if(!file_exists($emit["emp_certpath"])){
    echo '[{"retorno":"ERR","dados":"","erro":"EMITENTE - CERTIFICADO NAO LOCALIZADO"}]';  
    exit;
  }

  // emitente
  $nota->emit->cnpj             = $emit["emp_cnpj"];
  $nota->emit->ie               = $emit["emp_insestad"];
  $nota->emit->im               = '';
  $nota->emit->razao            = $emit["emp_nome"];
  $nota->emit->endereco->xlgr   = $emit["emp_endereco"];
  $nota->emit->endereco->nro    = $emit["emp_numero"];
  $nota->emit->endereco->compl  = '';
  $nota->emit->endereco->bairro = $emit["emp_bairro"];
  $nota->emit->endereco->xmun   = $emit["cdd_nome"];
  $nota->emit->endereco->uf     = $emit["cdd_codest"];
  $nota->emit->endereco->cmun   = $emit["emp_codcdd"];
  $nota->emit->endereco->cep    = $emit["emp_cep"];
  $nota->emit->fone             = str_replace(["-","(",")"," ","."],"",$emit["emp_fone"]);
  $nota->emit->crt              = $emit["emp_codert"]; 
  
  // buscando destinatario / transportadora / capa da nota 
  $ret=$db->selectAssoc(
    "select nfp_numnf,snf_serie
          ,f.fvr_cnpjcpf,f.fvr_ins,f.fvr_nome,f.fvr_codlgr,f.fvr_endereco,f.fvr_complemento,f.fvr_numero
          ,f.fvr_bairro,f.fvr_cep,f.fvr_codcdd,cd.cdd_nome,cd.cdd_codest,f.fvr_fone,f.fvr_email
          ,pgr_observacao
          ,nfp_vlritem,nfp_vlrfrete,nfp_vlrseguro,nfp_vlroutras,nfp_vlripi,nfp_vlricms,nfp_vlrst
          ,nfp_vlrpis,nfp_vlrcofins,nfp_vlrdesconto,nfp_vlrtotal
          ,nfp_volume,nfp_especie,nfp_pesobruto,nfp_pesoliquido
          ,t.fvr_nome trn_nome, t.fvr_codlgr trn_codlgr, t.fvr_endereco trn_endereco, t.fvr_numero trn_numero 
          ,t.fvr_cnpjcpf trn_cnpjcpf, ct.cdd_codest trn_codest 
    from vNFProduto 
    left outer join vSerieNf on snf_codigo=nfp_codsnf 
    left outer join vPagar on pgr_lancto=nfp_lancto 
    left outer join vFavorecido f on f.fvr_codigo=pgr_codfvr 
    left outer join vCidade cd on cdd_codigo=fvr_codcdd
    left outer join vTransportadora on trn_codfvr=nfp_codtrn
    left outer join VFavorecido t on t.fvr_codigo=trn_codfvr
    left outer join VCidade ct on ct.cdd_codigo=t.fvr_codcdd
    where nfp_lancto=".$call->lancto
  );
  $capa = $ret["dados"][0];
  
  $nota->ide->tpemis            = 1; // 1-normal, 6-contingencia svc an
  $nota->ide->tpamb             = ($emit["emp_prodhomol"]=="H"?2:1); // 1-normal, 2-homologacao
  $nota->ide->finnfe            = 1; // 1-venda, 2-complemento, 4-devolucao
  $nota->ide->natop             = 'VENDA';
  $nota->ide->tpnf              = 1;
  $nota->ide->dhemi             = date("Y/m/d")." ".date("H:i");
  $nota->ide->nnf               = $capa["nfp_numnf"];
  $nota->ide->serie             = $capa["snf_serie"];
  $nota->dest->cnpj             = $capa["fvr_cnpjcpf"];
  $nota->dest->razao            = $capa["fvr_nome"];
  $nota->dest->endereco->xlgr   = $capa["fvr_codlgr"]." ".$capa["fvr_endereco"];
  $nota->dest->endereco->nro    = $capa["fvr_numero"];
  $nota->dest->endereco->compl  = $capa["fvr_complemento"];
  $nota->dest->endereco->bairro = $capa["fvr_bairro"];
  $nota->dest->endereco->xmun   = $capa["cdd_nome"];
  $nota->dest->endereco->cmun   = $capa["fvr_codcdd"];
  $nota->dest->endereco->uf     = $capa["cdd_codest"];
  $nota->dest->endereco->cep    = $capa["fvr_cep"];
  $nota->dest->fone             = $capa["fvr_fone"];
  $nota->dest->ie               = ($capa["fvr_ins"]!="NSA"?$capa["fvr_ins"]:"");
  $nota->dest->im               = '';
  $nota->dest->suframa          = '';
  $nota->dest->cpais            = 1058;
  $nota->dest->email            = $capa["fvr_email"];

  // transportadora
  $nota->transp->modfrete       = 0; // 0-emitente, 1-destinatario, 9-sem frete
  $nota->transp->cnpj           = $capa["trn_cnpjcpf"];
  $nota->transp->razao          = $capa["trn_nome"];
  $nota->transp->endereco->xlgr = '';
  $nota->transp->endereco->xmun = '';
  $nota->transp->endereco->uf   = $capa["trn_codest"];
  $nota->transp->ie             = '';
  $nota->transp->volume         = $capa["nfp_volume"];
  $nota->transp->especie        = $capa["nfp_especie"];
  $nota->transp->pesob          = $capa["nfp_pesobruto"];
  $nota->transp->pesol          = $capa["nfp_pesoliquido"];
  
  // itens
  $ret=$db->selectAssoc(
    "select nfpi_item,nfpi_codprd,nfpi_cfop,prd_codncm,prd_nome,nfpi_unidades,nfpi_vlrunitario
          ,nfpi_vlritem,nfpi_vlrdesconto,nfpi_vlrseguro,nfpi_vlrfrete,nfpi_vlroutras
        ,nfpi_bcicms,nfpi_aliqicms,nfpi_vlricms,nfpi_vlricmsisentas,nfpi_vlricmsoutras,nfpi_csticms
        ,nfpi_bcst,nfpi_aliqst,nfpi_vlrst
        ,nfpi_bcpis,nfpi_aliqpis,nfpi_vlrpis,nfpi_cstpis 
        ,nfpi_bccofins,nfpi_aliqcofins,nfpi_vlrcofins,nfpi_cstcofins 
        ,nfpi_bcipi,nfpi_aliqipi,nfpi_vlripi,nfpi_cstipi 
        ,prd_codbarras
    from vNFProdutoItem 
    left outer join VProduto on prd_codigo=nfpi_codprd 
    left outer join vNCM on ncm_codigo=prd_codncm 
    where nfpi_lancto=".$call->lancto
  );
  foreach($ret["dados"] as $item){
    $produto = new produto();
    $produto->nitem       = $item["nfpi_item"];
    $produto->cfop        = $item["nfpi_cfop"];
    $produto->ncm         = $item["prd_codncm"];
    $produto->ean         = $item["prd_codbarras"]==""?'SEM GTIN':$item["prd_codbarras"]; // UM GTIN VALIDO OU O LITERAL "SEM GTIN"
    $produto->cprod       = $item["nfpi_codprd"];
    $produto->xprod       = $item["prd_nome"];
    $produto->ucom        = $item["nfpi_unidades"];
    $produto->qcom        = $item["nfpi_unidades"];
    $produto->vuncom      = $item["nfpi_vlrunitario"];
    $produto->cest        = '';
    $produto->vprod       = $item["nfpi_vlritem"];
    $produto->vdesc       = $item["nfpi_vlrdesconto"];
    $produto->vseg        = $item["nfpi_vlrseguro"];
    $produto->vfrete      = $item["nfpi_vlrfrete"];
    $produto->voutr       = $item["nfpi_vlroutras"];
    $produto->icms->cst   = $item["nfpi_csticms"];
    $produto->icms->base  = $item["nfpi_bcicms"];
    $produto->icms->pc    = $item["nfpi_aliqicms"];
    $produto->icms->valor = $item["nfpi_vlricms"];
    $produto->st->base    = $item["nfpi_bcst"];
    $produto->st->pc      = $item["nfpi_aliqst"];
    $produto->st->valor   = $item["nfpi_vlrst"];
    $produto->ipi->cst    = $item["nfpi_cstipi"];
    $produto->ipi->base   = $item["nfpi_bcipi"];
    $produto->ipi->pc     = $item["nfpi_aliqipi"];
    $produto->ipi->valor  = $item["nfpi_vlripi"];
    $produto->pis->cst    = $item["nfpi_cstpis"];
    $produto->pis->base   = $item["nfpi_bcpis"];
    $produto->pis->pc     = $item["nfpi_aliqpis"];
    $produto->pis->valor  = $item["nfpi_vlrpis"];
    $produto->cofins->cst = $item["nfpi_cstcofins"];
    $produto->cofins->base= $item["nfpi_bccofins"];
    $produto->cofins->pc  = $item["nfpi_aliqcofins"];
    $produto->cofins->valor=$item["nfpi_vlrcofins"];
    
    // adiciona o objeto/produto na nota
    $nota->addproduto($produto);
  }

  // duplicatas
  $ret=$db->selectAssoc("select pgr_lancto,format(pgr_vencto,'yyyy/MM/d') pgr_vencto,pgr_vlrparcela from vPagar where pgr_master=".$call->lancto);
  foreach($ret["dados"] as $dupl){
    $parc         = new dup();
    $parc->dvenc  = $dupl["pgr_vencto"];
    $parc->ndup   = str_pad($call->lancto,9,"0",STR_PAD_LEFT)."-".$dupl["pgr_lancto"];
    $parc->vdup   = $dupl["pgr_vlrparcela"];
    $nota->cobr->dupl[] = $parc;
  }

  $nota->prepare(); // prepara a nota com validações basicas

  // verifica avisos e erros 
  $erro = "";
  $emite = true;
  foreach($nota->alertas as $alerta){
    $erro .= $alerta[0]->texto.";";
    if ($alerta[0]->gravidade=='e')
      $emite = false;
  }
  if($emite==false){
    echo '[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
    exit;
  }

  $xml = $nota->geraxml(); // gera o xml que sera enviado a sefaz
  definecertificado(str_replace(".pfx","",strtolower($emit["emp_certpath"])), $emit["emp_certsenha"]);
  $retorno = envianfe($nota->ide->tpamb,$nota->emit->endereco->uf,$xml,false);

  if (isset($retorno['retorno'])){
    
    file_put_contents("notateste.xml",$xml);

    // 100/104 = ok, nota emitida
    if (($retorno['retorno']['cStat']=='104') or ($retorno['retorno']['cStat']=='100')){
      if ($retorno['retorno']['cStat']=='104')
        $infProt = $retorno['retorno']['infProt'];
      else
        $infProt = $retorno['retorno'];
      
      // monta o protocolo que deve ser anexado ao xml enviado a sefaz
      if ($infProt['cStat']=='100'){
        $xmlautorizada = 
          '<nfeProc xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">'
          .$xml
          .'<protNFe versao="3.10">'
          .'<infProt>'
          .'<tpAmb>'.$infProt['tpAmb'].'</tpAmb>'
          .'<verAplic>'.$infProt['verAplic'].'</verAplic>'
          .'<chNFe>'.$infProt['chNFe'].'</chNFe>'
          .'<dhRecbto>'.$infProt['dhRecbto'].'</dhRecbto>'
          .'<nProt>'.$infProt['nProt'].'</nProt>'
          .'<digVal>'.$infProt['digVal'].'</digVal>'
          .'<cStat>'.$infProt['cStat'].'</cStat>'
          .'<xMotivo>'.$infProt['xMotivo'].'</xMotivo>'
          .'</infProt>'
          .'</protNFe>'
          .'</nfeProc>';

        // salve o xml autorizado no banco ou em arquivo
        file_put_contents("notas/".$infProt['chNFe'].".xml",$xmlautorizada);

        // gerando o pdf 
        geraDANFE( simplexml_load_string($xmlautorizada) , "notas/".$infProt['chNFe'].".pdf");

        // gravando no banco os dados
        $db->cmd(["update vNFProduto set nfp_ChaveNFe='".$infProt['chNFe']."',nfp_ReciboNFe='".$infProt['nProt']."' where nfp_lancto=".$call->lancto]);

        echo '[{"retorno":"OK","dados":"","erro":"nota processada XML salvo","recibo":"'.$infProt['nProt'].'","danfe":"'."notas/".$infProt['chNFe'].".pdf".'"}]';
      } else {
        $erro = 'NF nao processada, motivo '.$infProt['retorno']['cStat'].' - '.$infProt['retorno']['xMotivo'];
        echo '[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
      }
    } else {
      $erro = 'NF nao processada, motivo '.$retorno['retorno']['cStat'].' - '.$retorno['retorno']['xMotivo'];
      echo '[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
    }
  } else {
    if (isset($retorno['envio'])){
      $erro = 'NF nao enviada, motivo '.$retorno['envio']['cStat'].' - '.$retorno['envio']['xMotivo'];
      echo '[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
    } else {
      $erro = 'Erro no envio, motivo '.$retorno;
      echo '[{"retorno":"ERR","dados":"","erro":"'.$erro.'"}]';
    }
  }
