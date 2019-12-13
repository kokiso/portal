<?php
//**
//********************************************************************************************************//
//** ARQUIVO SEPARADO EM:                                                                                 //
//** a) FUNCOES DE APOIO, EX: MODULO11                                                                    //
//** b) CLASSE DA NFe                                                                                     //
//** c) FUNCOES DE NFE                                                                                    //
//** d) IMPRESSAO DO DANFE                                                                                //
//**
//** exemplo de uso:
//** $nota = new nota();
//** $nota->emit (preenche os dados)
//** $nota->ide (preenche os dados)
//** $nota->destinatario (preenche os dados)
//** $nota->transp (preenche os dados da transportadora)
//** $produto = new produto();
//** $nota->addproduto( $produto );
//** $dup = new dup();
//** $nota->cobr->dupl[] = $dupl;
//** $nota->prepare();
//** verifica se tem erros graves no em: $nota->alertas->mensagem[0..n]->gravidade (e=erro, a=aviso)
//** não havendo erros, executa: 
//** $xml = $nota->geraxml()
//** definecertificado(<caminho certificado>,<senha>);
//** definecertificado(<caminho certificado>,<senha>);
//** $retorno = envianfe(<tpAmb>,<uf emitente>,$xml,<contingencia [true/false]);
//** se o envio for correto ($retorno['retorno']['cStat']=='104') e ($infProt['cStat']=='100')
//** imprime o danfe: geraDANFE($xmlautorizado);
//**
//**
//** em testenfe.php consta o exemplo completo para gerar um danfe a partir de um xml ou do banco
//** também há o exemplo de todos os passos para gerar/enviar a nota.
//** a) http://localhost/portalbis/testenfe.php?envianota
//** b) http://localhost/portalbis/testenfe.php?geradanfe
//**
//** (*) comand para extrar o pem do pfx
//** c:\openssl pkcs12 -in certificado.pfx -out certificado.out -nodes
//** c:\openssl pkcs12 -in certificado.pfx -out certificado.out -nodes -clcerts (em alguns casos)
//********************************************************************************************************//
@session_start();
//require('../php/fpdf.php');
include "php/fpdf.php";

//***********************//
// ** FUNCOES DE APOIO **//
//***********************//
function modulo11($parStr){
  /*
  *    str =   1  1  2  1  2  3  4  5  6  7  8
  *            x  x  x  x  x  x  x  x  x  x  x 
  *            4  3  2  9  8  7  6  5  4  3  2
  *            4  3  4  9 16 21 24 25 24 21 16
  *
  *    fator=( 4+3+4+9+16+21+24+25+24+21+16 )
  *    n=( 167 / 11 ) = 15 (resto=2)
  *    dac=(11-2)  
  */
  $lenStr=strlen($parStr);
  $intMultiplica = 0;
  $intSequencia  = 2;
  $intFator      = 0;
  for( $i=$lenStr;$i>=1;$i--):
    $intMultiplica=intval($parStr[$i-1])*$intSequencia;
    $intFator=($intFator+$intMultiplica);
    /*********************************************** 
    * Alternando o multiplicardor de 2,3,4,5,6,7,8,9
    ***********************************************/
    $intSequencia++;
    if( $intSequencia==10):
      $intSequencia=2;
    endif;  
  endfor;
  /******************************
  * Retornando o resto da divisão
  ******************************/
  $intResto=($intFator % 11);
  $dac=(11-$intResto);
  if (($intResto==0)or($intResto==1))
    $dac = 0;
  return $dac;
}


//*******************//
//** CLASSSE DA NFE  //
//*******************//
class msgerro {
  var $codigo;
  var $texto;
  var $gravidade;
  
  function __constructor(){
    $this->codigo = 0;
    $this->texto = '';
    $this->gravidade = '*'; // e=erro, a=advertencia
  }
}

class erro {
  var $mensagem;
  
  function adderro($codigo,$mensagem){
    $erro             = new msgerro();
    $erro->codigo     = $codigo;
    $erro->texto      = $mensagem;
    $erro->gravidade  = 'e';
    $this->mensagem[] = $erro;
  }
  
  function addaviso($codigo,$mensagem){
    $erro             = new msgerro();
    $erro->codigo     = $codigo;
    $erro->texto      = $mensagem;
    $erro->gravidade  = 'a';
    $this->mensagem[] = $erro;
  }
}

class ide {
  var $id;        // chave da nfe, é montado sozinho
  var $cuf;
  var $cdv;       // calculado no final do processo
  var $utc;       // leva em conta o fuso horário
  var $tpnf;      // 1=normal
  var $tpamb;     // 2=homologacao, 1=producao
  var $tpemis;    // 1=normal, 2=contingencia fs, 3=contingencias scan, 4=epec, 5=contingencia fsda, 6=contingencia svc an, 7-contingencia svc, 9-contingencia offline - NFCe
  var $tpimp;     // 1=retrato, 2=paisagem
  var $indpag;    // 0=avista, 1=a prazo, 9=outros 
  var $natop;
  var $mod;
  var $serie;
  var $nnf;
  var $cnf;
  var $dhemi;     // é o resultado da conversão de "emissao"
  var $dhsaient;
  var $iddest;
  var $finnfe;
  var $indfinal;
  var $indpres;
  var $procemi;   // 0=emissor proprio
  var $verproc;
  var $dhCont;    // data e hora do envio em contingencia
  var $nProtEPEC; // numero do protocolo EPEC, caso enviado via EPEC
  var $xJust;     // justificativa do envio em EPEC
  var $refNFe;     // chave de uma nota referenciada (obrigatória quando devolucao)

  function __construct() {
    $this->utc = '-03:00';
    $this->tpnf = 1;
    $this->tpemis = 1;
    $this->tpamb = 0;
    $this->tpimp = 1;
    $this->indpag = 0;
    $this->mod = 55;
    $this->serie = '';
    $this->nnf = 0;
    $this->cnf = 0;
    $this->dhemi = date('Y/m/d H:i:s') . $this->utc;
    $this->dhsaient = date('Y/m/d H:i:s') . $this->utc;
    $this->iddest = 0;
    $this->finnfe = 0;
    $this->indfinal = 0;
    $this->indpres = 0;
    $this->procemi = 0; // emissor proprio
    $this->verproc = 'bis3.10web';
    $this->dhCont = '';
    $this->xJust = '';
    $this->nProtEPEC = '';
    $this->refNFe = '';
  }
}

class endereco {
  var $xlgr;
  var $nro;
  var $compl;
  var $bairro;
  var $xmun;
  var $cmun;
  var $uf;
  var $cep;
  
  function __construct() {
    $this->xlgr   = '';
    $this->nro    = '';
    $this->compl  = '';
    $this->bairro = '';
    $this->xmun   = '';
    $this->cmun   = '';
    $this->uf     = '';
    $this->cep    = '';
  }
}

class emitente {
  var $cnpj;
  var $razao;
  var $endereco;
  var $fone;
  var $ie;
  var $im;
  var $cnae;
  var $crt;
  var $cpais;
  var $xpais;
  
  function __construct() {
    $this->cpais    = 1058;
    $this->xpais    = 'BRASIL';
    $this->endereco = new endereco();
  }
}


class destinatario{
  var $cnpj;
  var $razao;
  var $endereco;
  var $fone;
  var $ie;
  var $im;
  var $suframa;
  var $email;
  var $cpais;
  var $xpais;
  var $fisjur;
  
  function __construct() {
    $this->cpais = 1058;
    $this->xpais = 'BRASIL';
    $this->endereco = new endereco();
  }
}

class endereco_entrega {
  var $cnpj;
  var $endereco;
  
  function __construct() {
    $this->cnpj     = '';
    $this->endereco = new endereco();
  }
}

class partilha{
 var $vbcufdest;
 var $pfcpufdest;
 var $picmsufdest;
 var $picmsinter;
 var $picmsinterpart;
 var $vfcpufdest;
 var $vicmsufdest;
 var $vicmsufremet;
 
 function __construct(){
   $this->vbcufdest       = 0;
   $this->pfcpufdest      = 0;
   $this->picmsufdest     = 0;
   $this->picmsinter      = 0;
   $this->picmsinterpart  = 0;
   $this->vfcpufdest      = 0;
   $this->vicmsufdest     = 0;
   $this->vicmsufremet    = 0;
 }
}


class icms {
  var $origem;
  var $cst;
  var $base;
  var $pc;
  var $valor;
  var $outras;
  var $isentas;
  var $redbc;

  function __construct() {
    $this->origem = 0;
    $this->cst = '';
    $this->base = 0;
    $this->pc = 0;
    $this->valor = 0;
    $this->outras = 0;
    $this->isentas = 0;
    $this->redbc = 0;
  }
}

class ipi {
  var $cst;
  var $base;
  var $pc;
  var $valor;
  var $outras;
  var $isentas;

  function __construct() {
    $this->cst = '';
    $this->base = 0;
    $this->pc = 0;
    $this->valor = 0;
    $this->outras = 0;
    $this->isentas = 0;
  }
}

class st {
  var $base;
  var $pc;
  var $valor;
  var $iva;
  var $redbc;
  var $desonera;

  function __construct() {
    $this->base     = 0;
    $this->pc       = 0;
    $this->valor    = 0;
    $this->iva      = 0;
    $this->redbc    = 0;
    $this->desonera = 0;
  }
}

class ii {
  var $base;
  var $vdespadu;
  var $vii;
  var $viof;
  
  function __construct() {
    $this->base     = 0;
    $this->vdespadu = 0;
    $this->vii      = 0;
    $this->viof     = 0;
  }
}

class pis {
  var $cst;
  var $base;
  var $pc;
  var $valor;

  function __construct() {
    $this->cst = '';
    $this->base = 0;
    $this->pc = 0;
    $this->valor = 0;
  }
}

class cofins {
  var $cst;
  var $base;
  var $pc;
  var $valor;

  function __construct() {
    $this->cst = '';
    $this->base = 0;
    $this->pc = 0;
    $this->valor = 0;
  }
}

class issqn {
  var $vbc;
  var $valiq;
  var $vissqn;
  var $cmunfg;
  var $clistserv;
  var $vdeducao;
  var $voutro;
  var $vdescincond;
  var $vdesccond;
  var $issret;
  var $indiss;
  var $cservico;  
  var $cmun;
  var $cpais;
  var $nprocesso;
  var $indincentivo;
  
  function __construct() {
    $this->vbc          = 0;
    $this->valiq        = 0;
    $this->vissqn       = 0;
    $this->cmunfg       = '';
    $this->clistsrv     = '';
    $this->vdeducao     = 0;
    $this->voutro       = 0;
    $this->vdescincond  = 0;
    $this->vdesccond    = 0;
    $this->issret       = 0;
    $this->indiss       = 0;
    $this->cservico     = '';
    $this->cmun         = '';
    $this->cpais        = '';
    $this->nprocesso    = '';
    $this->indincentivo = '';
  }
}

class produto {
  var $nitem;
  var $cfop;
  var $ncm;
  var $ean;
  var $cprod;
  var $xprod;
  var $ucom;
  var $qcom;
  var $vuncom;
  var $cest;
  var $vprod;
  var $vdesc;
  var $vseg;
  var $vfrete;
  var $voutr;
  var $icms;
  var $ipi;
  var $st;
  var $pis;
  var $cofins;
  var $issqn;
  var $infadprod;
  var $partilha;

  function __construct() {
    $this->nitem    = 0;
    $this->cfop     = '';
    $this->ncm      = '';
    $this->ean      = '';
    $this->cprod    = '';
    $this->xprod    = '';
    $this->ucom     = '';
    $this->qcom     = 0;
    $this->vuncom   = 0;
    $this->cest     = '';
    $this->vprod    = 0;
    $this->vdesc    = 0;
    $this->vseg     = 0;
    $this->vfrete   = 0;
    $this->voutr    = 0;
    $this->icms     = new icms();
    $this->ipi      = new ipi();
    $this->st       = new st();
    $this->pis      = new pis();
    $this->cofins   = new cofins();
    $this->issqn    = new issqn();
    $this->partilha = new partilha();
    $this->infadprod= '';
  }
}

class totaisissqn {
  var $vservico;
  var $cregtrib;
  var $totais;
  
  function __construct() {
    $this->vservico = 0;
    $this->cregtrib = '';
    $this->totais = new issqn();
  }
}

class totais {
  var $vbc;
  var $vicms;
  var $vicmsdeson;
  var $vfcpufdest;
  var $vicmsufdest;
  var $vicmsufremet;

  var $vbcst;
  var $vst;
  var $vprod;
  var $vfrete;
  var $vseg;
  var $vdesc;
  var $vii;
  var $vipi;
  var $vpis;
  var $vcofins;
  var $voutro;
  var $vnf;
  var $vtottrib;
  var $issqn;

  function __construct() {
    $this->vbc          = 0;
    $this->vicms        = 0;
    $this->vicmsdeson   = 0;
    $this->vfcpufdest   = 0;
    $this->vicmsufdest  = 0;
    $this->vicmsufremet = 0;
    $this->vbcst        = 0;
    $this->vst          = 0;
    $this->vprod        = 0;
    $this->vfrete       = 0;
    $this->vseg         = 0;
    $this->vdesc        = 0;
    $this->vii          = 0;
    $this->vipi         = 0;
    $this->vpis         = 0;
    $this->vcofins      = 0;
    $this->voutro       = 0;
    $this->vnf          = 0;
    $this->vtottrib     = 0;
    $this->issqn        = new totaisissqn();
  }
}

class transportadora {
  var $modfrete;
  var $cnpj;
  var $razao;
  var $ie;
  var $volume;
  var $especie;
  var $pesob;
  var $pesol;
  var $endereco;

  function __construct() {
    $this->modfrete = 0;
    $this->pesob = 0;
    $this->pesol = 0;
    $this->endereco = new endereco();
  }
}

class dup {
  var $ndup;
  var $dvenc;
  var $vdup;

  function __construct() {
    $this->ndup = '';
    $this->vdup = 0;
  }
}

class cobr {
  var $dupl;

  function __construct() {
    $this->dupl = array();
  }
}

class nota {
  var $versao;
  var $ide;
  var $emit;
  var $dest;
  var $endereco_entrega;
  var $autXML;
  var $produto;
  var $totais;
  var $transp;
  var $cobr;
  var $obs;
  var $alertas;
  var $xml;
  var $temst;   // para saber se há ST recohida anteriormente (cst=60)
  var $pedido;  // numero do pedido

  function __construct() {
    $this->versao           = '4.00';
    $this->ide              = new ide();
    $this->emit             = new emitente();
    $this->dest             = new destinatario();
    $this->endereco_entrega = new endereco_entrega();
    $this->autXML           = '08181938000167';
    $this->produto          = array();
    $this->totais           = new totais();
    $this->transp           = new transportadora();
    $this->cobr             = new cobr();
    $this->alertas          = new erro();
    $this->obs              = '';
    $this->xml              = '';
    $this->temst            = false;
  }

  function addproduto($produto) {
    // adiciona produtos e atualiza contadores 
    $produto->nitem = count($this->produto) + 1;
    $produto->cfop = str_replace('.','',$produto->cfop);
    $produto->ncm = str_replace('.','',$produto->ncm);
    $this->produto[] = $produto;
    // sumariza
    $this->totais->vbc    += $produto->icms->base;    
    $this->totais->vicms  += $produto->icms->valor;
    $this->totais->vbcst  += $produto->st->base;
    $this->totais->vst    += $produto->st->valor;
    $this->totais->vprod  += $produto->vprod;
    $this->totais->vfrete += $produto->vfrete;
    $this->totais->vseg   += $produto->vseg;
    $this->totais->voutro += $produto->voutr;
    $this->totais->vdesc  += $produto->vdesc;
    $this->totais->vipi   += $produto->ipi->valor;
    $this->totais->vpis   += $produto->pis->valor;
    $this->totais->vcofins+= $produto->cofins->valor;
    $this->totais->vnf    += (($produto->vprod + $produto->vfrete + $produto->voutr + $produto->vseg + $produto->ipi->valor+ $produto->st->valor) - $produto->vdesc);
  }
  
  function prepare(){
    /* preenche os campos automátios da nfe e verifica se há erros */
    // emitente
    if (($this->emit->cnpj=='') or (is_null($this->emit->cnpj)))
      $this->alertas->adderro (10001, 'CNPJ do emiente incorreto');
    if ((floatval($this->emit->endereco->cmun)==0) or (strlen($this->emit->endereco->cmun)<>7))
      $this->alertas->adderro (10002, 'Código do municipio do emitente inválido');
    else 
      $this->ide->cuf = substr($this->emit->endereco->cmun,0,2);
    if (($this->emit->razao=='') or (is_null($this->emit->razao)))
      $this->alertas->adderro (10003, 'Razao social do emitente invalida');
    if (($this->emit->endereco->nro=='') or (is_null($this->emit->endereco->nro)))
      $this->alertas->adderro (10004, 'Numero da residencia/comercio do emitente invalido');
    if (($this->emit->endereco->xlgr=='') or (is_null($this->emit->endereco->xlgr)))
      $this->alertas->adderro (10005, 'Endereco da residencia/comercio do emitente invalido');
    if ((is_null($this->emit->endereco->cep)) or (strlen($this->emit->endereco->cep)<8))
      $this->alertas->adderro (10006, 'CEP do emitente invalido');
    else 
      $this->emit->endereco->cep = str_replace('-','',$this->emit->endereco->cep);
    if ((is_null($this->emit->crt)) or (strlen($this->emit->crt)<>1))
      $this->alertas->adderro (10007, 'Codigo do regime tributário do emitente invalido');
    /*
    * Fone emitente <xs:pattern value="[0-9]{6,14}">
    */
    $this->emit->fone = preg_replace("/[^0-9]/", "",(string)$this->emit->fone);
    if ((strlen($this->emit->fone)==0) or (is_null($this->emit->fone)))
      $this->alertas->adderro (10008, 'Telefone do emitente não informado');
    elseif( (strlen($this->emit->fone)<6) or (strlen($this->emit->fone)>14) )
      $this->alertas->adderro (10008, 'Telefone do emitente tamanho aceito [6 a 14]');  
    
    if ((is_null($this->emit->cnae)) or (floatval($this->emit->cnae)==0))
      $this->alertas->addaviso (20004, 'CNAE do emitente invalido, impeditivo para emissao de nota de servico');
    //**//
    // dados da nota / ide
    $this->ide->ie = str_replace(array('.','-'),'',$this->ide->id);
    if (($this->ide->finnfe<1) or ($this->ide->finnfe>4))
      $this->alertas->adderro (10027, 'Finalidade da NFe não foi informada [finNFe - 1(normal),2(complementar),3(ajuste),4(devolucao)] ');
    if (($this->ide->indfinal<0) or ($this->ide->indfinal>1))
      $this->alertas->adderro (10027, 'Consumidor final [indfinal - 0(nao),1(sim)] ');
    if ((($this->ide->indpres<0) or ($this->ide->indpres>4)) and ($this->ide->indpres<>9))
      $this->alertas->adderro (10028, 'Indicador de presenca [indpres - 0(nao se aplica),1(presencial),2(internet),3(teleatendimento),4(domicilio),9(outros)] ');
    if (($this->ide->tpamb<1) or ($this->ide->tpamb>2))
      $this->alertas->adderro (10029, 'Ambiente NFe [tpamb - 1(producao),2(homologacao)] ');
    if (($this->ide->tpemis<1) or ($this->ide->tpamb>9))
      $this->alertas->adderro (10029, 'Tipo de emissaao [tpemis - 1(normal),2(cont.FS),3(cont.SCAN),4(cont.DPEC),5(cont.FSDA),6(cont.SVCAN),7(cont.SVCRS),8(cont.offline),9(cont.NFC-e)] ');
    //**//
    // destinatario
    if (($this->dest->cnpj=='') or (is_null($this->dest->cnpj)))
      $this->alertas->adderro (10012, 'CNPJ/CPF do favorecido não informado');
    else {
      $this->dest->cnpj = str_replace(array('-','/','.'),'',$this->dest->cnpj);
      if ((strlen($this->dest->cnpj)<>11) and (strlen($this->dest->cnpj)<>14))
        $this->alertas->adderro (10012, 'CNPJ/CPF do favorecido invalido');
    }
    if (($this->dest->endereco->xlgr=='') or (is_null($this->dest->endereco->xlgr)))
      $this->alertas->adderro (10013, 'Endereco do favorecido não informado');
    if (($this->dest->endereco->nro=='') or (is_null($this->dest->endereco->nro)))
      $this->alertas->adderro (10014, 'Numero da residencia/comercio do favorecido não informado');
    if (($this->dest->endereco->bairro=='') or (is_null($this->dest->endereco->bairro)))
      $this->alertas->adderro (10015, 'Bairro do favorecido não informado');
    if (($this->dest->endereco->xmun=='') or (is_null($this->dest->endereco->xmun)))
      $this->alertas->adderro (10016, 'Cidade do favorecido não informado');
    if ((strlen($this->dest->endereco->cmun)<>7) or (is_null($this->dest->endereco->cmun)))
      $this->alertas->adderro (10017, 'Codigo do municipio do favorecido não informado');
    if ((strlen($this->dest->endereco->uf)<>2) or (is_null($this->dest->endereco->xmun)))
      $this->alertas->adderro (10018, 'UF do favorecido não informado');
    if ((strlen($this->dest->endereco->cep)<8) or (is_null($this->dest->endereco->xmun)))
      $this->alertas->adderro (10019, 'CEP do favorecido não informado');
    else
      $this->dest->endereco->cep = str_replace('-','',$this->dest->endereco->cep);
    /*
    * Fone destinatario <xs:pattern value="[0-9]{6,14}">
    */
    $this->dest->fone = preg_replace("/[^0-9]/", "",(string)$this->dest->fone);    
    if ((strlen($this->dest->fone)==0) or (is_null($this->dest->fone)))
      $this->alertas->adderro (20005, 'Telefone do destinatario não informado');
    elseif( (strlen($this->dest->fone)<6) or (strlen($this->dest->fone)>14) )
      $this->alertas->adderro (20005, 'Telefone do destinatario tamanho aceito [6 a 14]');  
      
    
    if (strlen($this->dest->cpais)<=0)
      $this->alertas->adderro (10020, 'Codigo do pais do favorecido não informado ou invalido');
    //**//
    // endereco de entrega
    if ($this->endereco_entrega->cnpj!=''){
      if (($this->endereco_entrega->endereco->xlgr=='')
      or  ($this->endereco_entrega->endereco->nro=='')
      or  ($this->endereco_entrega->endereco->xmun=='')
      or  ($this->endereco_entrega->endereco->uf=='')
      or  ($this->endereco_entrega->endereco->cep=='')
      or  ($this->endereco_entrega->endereco->cmun=='')
      )
        $this->alertas->adderro (10031, 'Endereco de entrega invalido');
    }
    //**//
    // transportadora
    if (($this->transp->modfrete<>0) and ($this->transp->modfrete<>1))
      $this->alertas->adderro (10021, 'Modalidade do frete invalida, verifique');
    if (($this->transp->cnpj!='') and (!is_null($this->transp->cnpj))){
      $this->transp->cnpj = str_replace(array('/','-','.'),'',$this->transp->cnpj);
      if ((strlen($this->transp->cnpj)<>11) and (strlen($this->transp->cnpj)<>14))
        $this->alertas->adderro (10022, 'CNPJ/CPF da transportadora é invalido');
      if (($this->transp->razao=='') or (is_null($this->transp->razao)))
        $this->alertas->adderro (10023, 'Nome da transportadora é invalido');
      if (($this->transp->volume=='') or ($this->transp->volume==''))
        $this->alertas->adderro (10024, 'Necessário informar a quantidade de volumes');
      if (($this->transp->especie=='') or ($this->transp->especie==''))
        $this->alertas->adderro (10025, 'Necessário informar a especie dos volumes');
      /*
      if (($this->transp->pesob==0) or ($this->transp->pesol==0))
        $this->alertas->adderro (10026, 'Necessário informar peso bruto e peso liquido');
      */
    }
    //**//
    // produto
    
    //**//
    // ide
    // se for uma nota de venda e o destinatário não tiver IE deve se tratado
    // como consumidor final
    if ($this->ide->finnfe==1 and ($this->dest->ie==''))
      $this->ide->indfinal = 1;
    //  
    //
    if (($this->ide->natop=='') or (is_null($this->ide->natop)))
      $this->alertas->adderro (10008, 'Necessário informar a natureza da operação da nota');
    if (($this->ide->tpemis=='') or (is_null($this->ide->tpemis)))
      $this->alertas->adderro (10009, 'Necessário informar o tipo de emissao da nota');
    else{
      if (strpos('12345679',$this->ide->tpemis)<0)
        $this->alertas->adderro (10009, 'Tipo de emissão inválida (tpemis)');
    }
    if (($this->ide->serie=='') or (is_null($this->ide->serie)))
      $this->alertas->adderro (10010, 'Necessário informar serie da nota');
    if (($this->ide->nnf=='') or (is_null($this->ide->nnf))or ($this->ide->nnf==0))
      $this->alertas->adderro (10011, 'Necessário informar o numero da nota');
    else
      $this->ide->cnf = str_pad($this->ide->nnf,8,'0',STR_PAD_LEFT);
    //**//
    // monta os dados da tag [ide]
    if ($this->emit->endereco->uf==$this->dest->endereco->uf)
      $this->ide->iddest=1;
    elseif ($this->dest->cpais==1058) 
      $this->ide->iddest=2;
    else
      $this->ide->iddest=3;
    // 
    if ((($this->ide->finnfe==4) or ($this->ide->finnfe==2)) and ($this->ide->refNFe==''))
      $this->alertas->adderro (10030, 'Nota fiscal de devolucao/complemento é necessario informar a chave da nota referenciada.');
    //  
    $this->ide->dhemi = date('Y/m/d',strtotime($this->ide->dhemi)).' 00:00:00'.$this->ide->utc;
    $this->ide->dhsaient   = $this->ide->dhemi;
    $this->ide->id    =
            $this->ide->cuf
            .date('ym',strtotime($this->ide->dhemi))
            .$this->emit->cnpj 
            .$this->ide->mod
            .str_pad($this->ide->serie,3,'0',STR_PAD_LEFT)
            .str_pad($this->ide->nnf,9,'0',STR_PAD_LEFT)
            .$this->ide->tpemis 
            .str_pad($this->ide->cnf,8,'0',STR_PAD_LEFT);
    $this->ide->cdv   = modulo11($this->ide->id);
    $this->ide->id    = 'NFe'.$this->ide->id.$this->ide->cdv;
  }
  
  function geraxml(){
    // ide
    $this->xml = 
      '<ide>'
      . '<cUF>'.$this->ide->cuf.'</cUF>'
      . '<cNF>'.$this->ide->cnf.'</cNF>'
      . '<natOp>'.$this->ide->natop.'</natOp>'
      . '<mod>'.$this->ide->mod.'</mod>'
      . '<serie>'.intval($this->ide->serie).'</serie>'
      . '<nNF>'.$this->ide->nnf.'</nNF>'
      . '<dhEmi>'.str_replace(array('/',' '),array('-','T'),$this->ide->dhemi).'</dhEmi>'
      . '<dhSaiEnt>'.str_replace(array('/',' '),array('-','T'),$this->ide->dhsaient).'</dhSaiEnt>'
      . '<tpNF>'.$this->ide->tpnf.'</tpNF>'
      . '<idDest>'.$this->ide->iddest.'</idDest>'
      . '<cMunFG>'.$this->emit->endereco->cmun.'</cMunFG>'
      . '<tpImp>'.$this->ide->tpimp.'</tpImp>'
      . '<tpEmis>'.$this->ide->tpemis.'</tpEmis>'
      . '<cDV>'.$this->ide->cdv.'</cDV>'
      . '<tpAmb>'.$this->ide->tpamb.'</tpAmb>'
      . '<finNFe>'.$this->ide->finnfe.'</finNFe>'
      . '<indFinal>'.$this->ide->indfinal.'</indFinal>'
      . '<indPres>'.$this->ide->indpres.'</indPres>'
      . '<procEmi>'.$this->ide->procemi.'</procEmi>'
      . '<verProc>'.$this->ide->verproc.'</verProc>'
      ;
    if (($this->ide->tpemis!=1) and ($this->ide->tpemis!=4)){
      $this->xml .=
        '<dhCont>'.str_replace(array('/',' '),array('-','T'),$this->ide->dhemi).'</dhCont>'
        . '<xJust>'.$this->ide->xJust.'</xJust>';      
    }
    if ($this->ide->tpemis==4){
      $this->xml .=
        '<dhCont></dhCont>'
        . '<xJust>Nota enviada anteriormente em EPEC (protocolo:'.$this->ide->nProtEPEC.')</xJust>';      
    }
    if ($this->ide->refNFe!=''){
      $this->xml .=
          '<NFRef>'
        .   '<refNFe>'.$this->ide->refNFe.'</refNFe>'
        . '<NFref>';
    }
    $this->xml .= '</ide>';
    
    // emitente
    $this->xml .=
        '<emit>'
      .   '<CNPJ>'.$this->emit->cnpj.'</CNPJ>'
      .   '<xNome>'.$this->emit->razao.'</xNome>'
      .   '<xFant>'.$this->emit->razao.'</xFant>'
      .   '<enderEmit>'
      .     '<xLgr>'.$this->emit->endereco->xlgr.'</xLgr>'
      .     '<nro>'.$this->emit->endereco->nro.'</nro>'
      .     ($this->emit->endereco->compl!=''?'<xCpl>'.$this->emit->endereco->compl.'</xCpl>':'')
      .     '<xBairro>'.$this->emit->endereco->bairro.'</xBairro>'
      .     '<cMun>'.$this->emit->endereco->cmun.'</cMun>'
      .     '<xMun>'.$this->emit->endereco->xmun.'</xMun>'
      .     '<UF>'.$this->emit->endereco->uf.'</UF>'
      .     '<CEP>'.$this->emit->endereco->cep.'</CEP>'
      .     '<cPais>'.$this->emit->cpais.'</cPais>'
      .     '<xPais>'.$this->emit->xpais.'</xPais>'
      .     ($this->emit->fone!=''?'<fone>'.$this->emit->fone.'</fone>':'')
      .   '</enderEmit>'
      .   '<IE>'.$this->emit->ie.'</IE>'
      .   ($this->emit->im!=''?'<IM>'.$this->emit->im.'</IM>':'')
      .   ($this->emit->cnae!=''?'<CNAE>'.$this->emit->cnae.'</CNAE>':'')
      .   '<CRT>'.$this->emit->crt.'</CRT>'
      . '</emit>';
    
    // destinatario
    $this->xml .= '<dest>';
    if ($this->dest->cpais==1058)
      $this->xml .= (strlen($this->dest->cnpj)==14)?'<CNPJ>'.$this->dest->cnpj.'</CNPJ>':'<CPF>'.$this->dest->cnpj.'</CPF>';
    else
      $this->xml .= '<idEstrangeiro>'.str_pad($this->dest->cnpj,20,'0',STR_PAD_LEFT).'</idEstrangeiro>';
    //--
    if ($this->ide->tpamb==2)
      $this->xml .= '<xNome>NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL</xNome>';
    else
      $this->xml .= '<xNome>'.$this->dest->razao.'</xNome>';
    $this->xml .= '<enderDest>';
    $this->xml .= '<xLgr>'.$this->dest->endereco->xlgr.'</xLgr>';
    $this->xml .= '<nro>'.$this->dest->endereco->nro.'</nro>';
    $this->xml .= $this->dest->endereco->compl!=''?'<xCpl>'.$this->dest->endereco->compl.'</xCpl>':'';
    $this->xml .= '<xBairro>'.$this->dest->endereco->bairro.'</xBairro>';
    if ($this->dest->cpais==1058){
      $this->xml .= '<cMun>'.$this->dest->endereco->cmun.'</cMun>';
      $this->xml .= '<xMun>'.$this->dest->endereco->xmun.'</xMun>';
      $this->xml .= '<UF>'.$this->dest->endereco->uf.'</UF>';
      $this->xml .= '<CEP>'.$this->dest->endereco->cep.'</CEP>';
      $this->xml .= '<cPais>1058</cPais>';
      $this->xml .= '<xPais>BRASIL</xPais>';
      $this->xml .= $this->dest->fone!=''?'<fone>'.$this->dest->fone.'</fone>':'';
    } else {
      $this->xml .= '<cMun>9999999</cMun>';
      $this->xml .= '<xMun>EXTERIOR</xMun>';
      $this->xml .= '<UF>EX</UF>';
      $this->xml .= '<cPais>'.$this->dest->cpais.'</cPais>';
    }
    $this->xml .= '</enderDest>';
    if (($this->dest->ie=='') and ($this->dest->cpais==1058) and (strlen($this->dest->cnpj)!=11))
      if (strpos(';AM;BA;CE;GO;MG;MS;MT;PA;PE;RN;RO;SE;SP;RS;',$this->dest->endereco->uf)!==0)
        $this->xml .= '<indIEDest>9</indIEDest>';
      else
        $this->xml .= '<indIEDest>2</indIEDest>';
    elseif (($this->dest->ie!='') and ($this->dest->cpais==1058)){
      $this->xml .= '<indIEDest>1</indIEDest>';
      $this->xml .= '<IE>'.$this->dest->ie.'</IE>';
    } else 
      $this->xml .= '<indIEDest>9</indIEDest>';
    //--
    $this->xml .= $this->dest->suframa!='' ? '<ISUF>'.$this->dest->suframa.'</ISUF>':'';
    $this->xml .= $this->dest->im!=''      ? '<IM>'.$this->dest->im.'</IM>':'';
    $this->xml .= $this->dest->email!=''   ? '<email>'.$this->dest->email.'</email>':'';
    $this->xml .= '</dest>';
    
    // endereco de entrega
    if ($this->endereco_entrega->cnpj!=''){
      $this->xml .= '<entrega>';
      $this->xml .= (strlen($this->endereco_entrega->cnpj)==14)?'<CNPJ>'.$this->endereco_entrega->cnpj.'</CNPJ>':'<CPF>'.$this->endereco_entrega->cnpj.'</CPF>';
      $this->xml .= '<xLgr>'.$this->endereco_entrega->endereco->xlgr.'</xLgr>';
      $this->xml .= '<nro>'.$this->endereco_entrega->endereco->nro.'</nro>';
      $this->xml .= $this->endereco_entrega->endereco->compl!=''?'<xCpl>'.$this->endereco_entrega->endereco->compl.'</xCpl>':'';
      $this->xml .= '<xBairro>'.$this->endereco_entrega->endereco->bairro.'</xBairro>';
      $this->xml .= '<cMun>'.$this->endereco_entrega->endereco->cmun.'</cMun>';
      $this->xml .= '<xMun>'.$this->endereco_entrega->endereco->xmun.'</xMun>';
      $this->xml .= '<UF>'.$this->endereco_entrega->endereco->uf.'</UF>';
      $this->xml .= '<CEP>'.$this->endereco_entrega->endereco->cep.'</CEP>';
      $this->xml .= '</entrega>';
    }
    
    // cnpj autorizado a baixar o xml
    if ($this->autXML!=''){
      $this->xml .= '<autXML>';
      $this->xml .= '<CNPJ>'.$this->autXML.'</CNPJ>';
      $this->xml .= '</autXML>';
    }
    
    // produtos
    $i = 0;
    foreach($this->produto as $prod){
      $i++;
      $this->xml .= '<det nItem="'.$i.'">';
      
      // dados do produto
      $this->xml .= '<prod>';
      $this->xml .= '<cProd>'.$prod->cprod.'</cProd>';
      if ($prod->ean!='')
        $this->xml .= '<cEAN>'.$prod->ean.'</cEAN>';
      else
        $this->xml .= '<cEAN/>';
      $this->xml .= '<xProd>'.$prod->xprod.'</xProd>';
      $this->xml .= '<NCM>'.$prod->ncm.'</NCM>';
      if ($prod->cest!='')
        $this->xml .= '<CEST>'.$prod->cest.'</CEST>';
      else
        $this->xml .= '<CEST>9999999</CEST>';
      // NVE - EXPORTACAO
      // EX TIPI
      $this->xml .= '<CFOP>'.$prod->cfop.'</CFOP>';
      $this->xml .= '<uCom>'.$prod->ucom.'</uCom>';
      $this->xml .= '<qCom>'.number_format($prod->qcom,4,'.','').'</qCom>';
      $this->xml .= '<vUnCom>'.number_format($prod->vuncom,4,'.','').'</vUnCom>';
      $this->xml .= '<vProd>'.number_format($prod->vprod,2,'.','').'</vProd>';
      if ($prod->ean!='') 
        $this->xml .= '<cEANTrib>'.$prod->ean.'</cEANTrib>';
      else
        $this->xml .= '<cEANTrib/>';
      $this->xml .= '<uTrib>'.$prod->ucom.'</uTrib>';
      $this->xml .= '<qTrib>'.number_format($prod->qcom,4,'.','').'</qTrib>';
      $this->xml .= '<vUnTrib>'.number_format($prod->vuncom,3,'.','').'</vUnTrib>';
      //--
      if($prod->vfrete>0) 
        $this->xml .= '<vFrete>'.number_format($prod->vfrete,2,'.','').'</vFrete>';
      //--
      if($prod->vseg>0) 
        $this->xml .= '<vSeg>'.number_format($prod->vseg,2,'.','').'</vSeg>';
      //--
      if($prod->vdesc>0) 
        $this->xml .= '<vDesc>'.number_format($prod->vdesc,2,'.','').'</vDesc>';
      //--
      if($prod->voutr>0) 
        $this->xml .= '<vOutro>'.number_format($prod->voutr,2,'.','').'</vOutro>';
      //--
      $this->xml .= '<indTot>1</indTot>';
      
      // declaracao de importacao
      // ?
      
      // declaracao de exportacao
      // ?
      
      $this->xml .= '</prod>';
      
      // impostos
      $this->xml .= '<imposto>';

      if ($prod->issqn->cservico==''){
        
        // tratando o icms
        $this->xml .= '<ICMS>';
        switch ($prod->icms->cst){
          
          // CST para clientes do CRT normal
          case '00':
            $this->xml .= '<ICMS00>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '<modBC>3</modBC>';
            $this->xml .= '<vBC>'.number_format($prod->icms->base,2,'.','').'</vBC>';
            $this->xml .= '<pICMS>'.number_format($prod->icms->pc,2,'.','').'</pICMS>';
            $this->xml .= '<vICMS>'.number_format($prod->icms->valor,2,'.','').'</vICMS>';
            $this->xml .= '</ICMS00>';
            break;
          case '10':
            $this->xml .= '<ICMS10>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '<modBC>3</modBC>';
            $this->xml .= '<vBC>'.number_format($prod->icms->base,2,'.','').'</vBC>';
            $this->xml .= '<pICMS>'.number_format($prod->icms->pc,2,'.','').'</pICMS>';
            $this->xml .= '<vICMS>'.number_format($prod->icms->valor,2,'.','').'</vICMS>';
            $this->xml .= '<modBCST>4</modBCST>';
            $this->xml .= '<vBCST>'.number_format($prod->st->vbc,2,'.','').'</vBCST>';
            $this->xml .= '<pICMSST>'.number_format($prod->st->pc,2,'.','').'</pICMSST>';
            $this->xml .= '<vICMSST>'.number_format($prod->st->valor,2,'.','').'</vICMSST>';
            $this->xml .= '</ICMS10>';
            break;
          case '20':
            $this->xml .= '<ICMS20>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '<modBC>3</modBC>';
            $this->xml .= '<pRedBC>'.number_format($prod->icms->redbc,2,'.','').'</pRedBC>';
            $this->xml .= '<vBC>'.number_format($prod->icms->base,2,'.','').'</vBC>';
            $this->xml .= '<pICMS>'.number_format($prod->icms->pc,2,'.','').'</pICMS>';
            $this->xml .= '<vICMS>'.number_format($prod->icms->valor,2,'.','').'</vICMS>';
            $this->xml .= '</ICMS20>';
            break;
          case '30':
            $this->xml .= '<ICMS30>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '<modBC>4</modBC>';
            $this->xml .= $prod->st->iva>0?'<pMVAST>'.number_format($prod->st->iva,2,'.','').'</pMVAST>':'';
            $this->xml .= $prod->st->redbc>0?'<pRedBCST>'.number_format($prod->st->redbc,2,'.','').'</pRedBCST>':'';
            $this->xml .= '<vBC>'.number_format($prod->st->base,2,'.','').'</vBC>';
            $this->xml .= '<pICMS>'.number_format($prod->st->pc,2,'.','').'</pICMS>';
            $this->xml .= '<vICMS>'.number_format($prod->st->valor,2,'.','').'</vICMS>';
            $this->xml .= '</ICMS30>';
            break;
          case '40':
          case '41':  
          case '50':  
            $this->xml .= '<ICMS40>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '</ICMS40>';
            break;
          case '51':
            $this->xml .= '<ICMS51>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            if ($prod->icms->redbc>0){
              $this->xml .= '<modBC>3</modBC>';
              $this->xml .= $prod->icms->redbc>0  ? '<pRedBCST>'.number_format($prod->icms->redbc,2,'.','').'</pRedBCST>':'';
              $this->xml .= $prod->icms->base>0   ? '<vBC>'.number_format($prod->icms->base,2,'.','').'</vBC>':'';
              $this->xml .= $prod->icms->pc>0     ? '<pICMS>'.number_format($prod->icms->pc,2,'.','').'</pICMS>':'';
              $this->xml .= $prod->icms->valor>0  ? '<vICMSOp>'.number_format($prod->icms->valor,2,'.','').'</vICMSOp>':'';
              $this->xml .= '<pDif>'.number_format($prod->icms->redbc,2,'.','').'</pDif>';
              $this->xml .= '<vICMSDif>'.number_format($prod->icms->valor,2,'.','').'</vICMSDif>';
              $this->xml .= '<vICMS>'.number_format($prod->icms->redbc,2,'.','').'</vICMS>';
            }
            $this->xml .= '</ICMS51>';
            break;
          case '60':
            $this->temst = true;
            $this->xml .= '<ICMS60>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '<vBCSTRet>0.00</vBCSTRet>';
            $this->xml .= '<vICMSSTRet>0.00</pICMSSTRet>';
            $this->xml .= '</ICMS60>';
            break;
          case '70':
            $this->xml .= '<ICMS70>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '<modBC>3</modBC>';
            $this->xml .= '<pRedBC>'.number_format($prod->icms->redbc,2,'.','').'</pRedBC>';
            $this->xml .= '<vBC>'.number_format($prod->icms->base,2,'.','').'</vBC>';
            $this->xml .= '<pICMS>'.number_format($prod->icms->pc,2,'.','').'</pICMS>';
            $this->xml .= '<vICMS>'.number_format($prod->icms->valor,2,'.','').'</vICMS>';
            $this->xml .= '<modBCST>4</modBCST>';
            $this->xml .= $prod->st->iva>0?'<pMVAST>'.number_format($prod->st->iva,2,'.','').'</pMVAST>':'';
            $this->xml .= $prod->st->redbc>0?'<pRedBCST>'.number_format($prod->st->redbc,2,'.','').'</pRedBCST>':'';
            $this->xml .= '<vBCST>'.number_format($prod->st->vbc,2,'.','').'</vBCST>';
            $this->xml .= '<pICMSST>'.number_format($prod->st->pc,2,'.','').'</pICMSST>';
            $this->xml .= '<vICMSST>'.number_format($prod->st->valor,2,'.','').'</vICMSST>';
            $this->xml .= $prod->st->desonera>0?'<vICMSDeson>'.number_format($prod->st->desonera,2,'.','').'</vICMSDeson>':'';
            $this->xml .= $prod->st->desonera>0?'<motDesICMS>9</motDesICMS>':'';
            $this->xml .= '</ICMS70>';
            break;
          case '90':
            $this->xml .= '<ICMS90>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CST>'.$prod->icms->cst.'</CST>';
            $this->xml .= '<modBC>3</modBC>';
            $this->xml .= $prod->icms->redbc>0?'<pRedBC>'.number_format($prod->icms->redbc,2,'.','').'</pRedBC>':'';
            $this->xml .= '<vBC>'.number_format($prod->icms->base,2,'.','').'</vBC>';
            $this->xml .= '<pICMS>'.number_format($prod->icms->pc,2,'.','').'</pICMS>';
            $this->xml .= '<vICMS>'.number_format($prod->icms->valor,2,'.','').'</vICMS>';
            if ($prod->st->iva>0){
              $this->xml .= '<modBCST>4</modBCST>';
              $this->xml .= $prod->st->iva>0?'<pMVAST>'.number_format($prod->st->iva,2,'.','').'</pMVAST>':'';
              $this->xml .= $prod->st->redbc>0?'<pRedBCST>'.number_format($prod->st->redbc,2,'.','').'</pRedBCST>':'';
              $this->xml .= '<vBCST>'.number_format($prod->st->vbc,2,'.','').'</vBCST>';
              $this->xml .= '<pICMSST>'.number_format($prod->st->pc,2,'.','').'</pICMSST>';
              $this->xml .= '<vICMSST>'.number_format($prod->st->valor,2,'.','').'</vICMSST>';
            }
            $this->xml .= '</ICMS90>';
            break;
            
          // CST para clientes do CRT simples nacional
          case '101':
            $this->xml .= '<ICMSSN101>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CSOSN>'.$prod->icms->cst.'</CSOSN>';
            $this->xml .= '<pCredSN>0.00</pCredSN>';
            $this->xml .= '<vCredICMSSN>0.00</vCredICMSSN>';
            $this->xml .= '</ICMSSN101>';
            break;
          case '102':
          case '103':
          case '300':
          case '400':
            $this->xml .= '<ICMSSN102>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CSOSN>'.$prod->icms->cst.'</CSOSN>';
            $this->xml .= '</ICMSSN102>';
            break;
          case '201':
            $this->xml .= '<ICMSSN201>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CSOSN>'.$prod->icms->cst.'</CSOSN>';
            $this->xml .= '<modBCST>4</modBCST>';
            $this->xml .= $prod->st->iva>0?'<pMVAST>'.number_format($prod->st->iva,2,'.','').'</pMVAST>':'';
            $this->xml .= $prod->st->redbc>0?'<pRedBCST>'.number_format($prod->st->redbc,2,'.','').'</pRedBCST>':'';
            $this->xml .= '<vBCST>'.number_format($prod->st->vbc,2,'.','').'</vBCST>';
            $this->xml .= '<pICMSST>'.number_format($prod->st->pc,2,'.','').'</pICMSST>';
            $this->xml .= '<vICMSST>'.number_format($prod->st->valor,2,'.','').'</vICMSST>';
            $this->xml .= '<pCredSN>0.00</pCredSN>';
            $this->xml .= '<vCredICMSSN>0.00</vCredICMSSN>';
            $this->xml .= '</ICMSSN201>';
            break;
          case '202':
            $this->xml .= '<ICMSSN202>';
            $this->xml .= '<orig>'.$prod->icms->origem.'</orig>';
            $this->xml .= '<CSOSN>'.$prod->icms->cst.'</CSOSN>';
            $this->xml .= '<modBCST>4</modBCST>';
            $this->xml .= $prod->st->iva>0?'<pMVAST>'.number_format($prod->st->iva,2,'.','').'</pMVAST>':'';
            $this->xml .= $prod->st->redbc>0?'<pRedBCST>'.number_format($prod->st->redbc,2,'.','').'</pRedBCST>':'';
            $this->xml .= '<vBCST>'.number_format($prod->st->vbc,2,'.','').'</vBCST>';
            $this->xml .= '<pICMSST>'.number_format($prod->st->pc,2,'.','').'</pICMSST>';
            $this->xml .= '<vICMSST>'.number_format($prod->st->valor,2,'.','').'</vICMSST>';
            $this->xml .= '</ICMSSN202>';
            break;
          case '900':
            $this->xml .= '<ICMSSN900>';
            $this->xml .= '<orig>0</orig>';
            $this->xml .= '<CSOSN>900</CSOSN>';
            $this->xml .= '<modBC>'.($prod->icms->base>0?'3':'0').'</modBC>';
            $this->xml .= '<vBC>'.number_format($prod->icms->base,2,'.','').'</vBC>';
            $this->xml .= '<pICMS>'.number_format($prod->icms->pc,2,'.','').'</pICMS>';
            $this->xml .= '<vICMS>'.number_format($prod->icms->valor,2,'.','').'</vICMS>';
            $this->xml .= '<modBCST>0</modBCST>';
            $this->xml .= '<vBCST>0.00</vBCST>';
            $this->xml .= '<pICMSST>0.00</pICMSST>';
            $this->xml .= '<vICMSST>0.00</vICMSST>';
            $this->xml .= '<pCredSN>0.00</pCredSN>';
            $this->xml .= '<vCredICMSSN>0.00</vCredICMSSN>';
            $this->xml .= '</ICMSSN900>';
            break;
        }
        $this->xml .= '</ICMS>';
        // 
        // tratando o IPI
        if (strpos(';00;50;49;99;03;52;53;',$prod->ipi->cst)){
          $this->xml .= '<IPI>';
          $this->xml .= '<cEnq>999</cEnq>';
          switch ($prod->ipi->cst){
            case '00':
            case '49':
            case '50':
            case '99':
              $this->xml .= '<IPITrib>';
              $this->xml .= '<CST>'.$prod->ipi->cst.'</CST>';
              $this->xml .= '<vBC>'.number_format($prod->ipi->base,2,'.','').'</vBC>';
              $this->xml .= '<pIPI>'.number_format($prod->ipi->pc,2,'.','').'</pIPI>';
              $this->xml .= '<vIPI>'.number_format($prod->ipi->valor,2,'.','').'</vIPI>';
              $this->xml .= '</IPITrib>';
              break;
            case '03':
            case '04':
            case '52':
            case '53':
              $this->xml .= '<IPINT>';
              $this->xml .= '<CST>'.$prod->ipi->cst.'</CST>';
              $this->xml .= '</IPINT>';
              break;
          }
          $this->xml .= '</IPI>';
          //
          // dados da importação
          if (($this->ide->tpnf==0) and ($this->dest->cpais<>1058)){
            $this->xml .= '<II>';
            $this->xml .= '<vBC>'.number_format($prod->ii->base,2,'.','').'</vBC>';
            $this->xml .= '<vDespAdu>'.number_format($prod->ii->vdespadu,2,'.','').'</vDespAdu>';
            $this->xml .= '<vII>'.number_format($prod->ii->vii,2,'.','').'</vII>';
            $this->xml .= '<vIOF>'.number_format($prod->ii->viof,2,'.','').'</vIOF>';
            $this->xml .= '</II>';
          }
        }
      } else {
        $this->xml .= '<ISSQN>';
        $this->xml .= '<vBC>'.number_format($prod->issqn->vbc,2,'.','').'</vBC>';
        $this->xml .= '<vAliq>'.number_format($prod->issqn->valiq,2,'.','').'</vAliq>';
        $this->xml .= '<vISSQN>'.number_format($prod->issqn->vissqn,2,'.','').'</vISSQN>';
        $this->xml .= '<cMunFG>'.$prod->issqn->cmunfg.'</cMunFG>';
        $this->xml .= '<cListServ>'.$prod->issqn->cservico.'</cListServ>';
        $this->xml .= ($prod->issqn->vdeducao>0)    ? '<vDeducao>'.number_format($prod->issqn->vdeducao,2,'.','').'</vDeducao>'          :'';
        $this->xml .= ($prod->issqn->voutro>0)      ? '<vOutro>'.number_format($prod->issqn->voutr,2,'.','').'</vOutro>'                 :'';
        $this->xml .= ($prod->issqn->vdescincond>0) ? '<vDescIncond>'.number_format($prod->issqn->vdescincond,2,'.','').'</vDescIncond>' :'';
        $this->xml .= ($prod->issqn->vdesccond>0)   ? '<vDescCond>'.number_format($prod->issqn->vdesccond,2,'.','').'</vDescCond>'       :'';
        $this->xml .= ($prod->issqn->issret>0)      ? '<vISSRet>'.number_format($prod->issqn->issret,2,'.','').'</vISSRet>'              :'';
        $this->xml .= '<indISS>'.$prod->issqn->indiss.'</indISS>';
        $this->xml .= '<cServico>'.$prod->issqn->cservico.'</cServico>';
        $this->xml .= '<cMun>'.(($prod->issqn->cmun>0)?$prod->issqn->cmun:$prod->issqn->cmunfg).'</cMun>';
        $this->xml .= '<cPais>'.$prod->issqn->cpais.'</cPais>';
        $this->xml .= ($prod->issqn->nprocesso!='')?'<nProcesso>'.$prod->issqn->nprocesso.'</nProcesso>':'';
        $this->xml .= '<indIncentivo>'.(($prod->issqn->indincentivo=='s')?'1':'2').'</indIncentivo>';
        $this->xml .= '</ISSQN>';
      }
      
      // pis e cofins
      switch ($prod->pis->cst){
        case '01':
        case '02':
        case '51':
          $this->xml .= '<PIS>';
          $this->xml .= '<PISAliq>';
          $this->xml .= '<CST>'.$prod->pis->cst.'</CST>';
          $this->xml .= '<vBC>'.number_format($prod->pis->base,2,'.','').'</vBC>';
          $this->xml .= '<pPIS>'.number_format($prod->pis->pc,2,'.','').'</pPIS>';
          $this->xml .= '<vPIS>'.number_format($prod->pis->valor,2,'.','').'</vPIS>';
          $this->xml .= '</PISAliq>';
          $this->xml .= '</PIS>';
          $this->xml .= '<COFINS>';
          $this->xml .= '<COFINSAliq>';
          $this->xml .= '<CST>'.$prod->cofins->cst.'</CST>';
          $this->xml .= '<vBC>'.number_format($prod->cofins->base,2,'.','').'</vBC>';
          $this->xml .= '<pCOFINS>'.number_format($prod->cofins->pc,2,'.','').'</pCOFINS>';
          $this->xml .= '<vCOFINS>'.number_format($prod->cofins->valor,2,'.','').'</vCOFINS>';
          $this->xml .= '</COFINSAliq>';
          $this->xml .= '</COFINS>';
          break;
        case '04':
        case '05':
        case '06':
        case '07':
        case '08':
        case '09':
          $this->xml .= '<PIS>';
          $this->xml .= '<PISNT>';
          $this->xml .= '<CST>'.$prod->pis->cst.'</CST>';
          $this->xml .= '</PISNT>';
          $this->xml .= '</PIS>';
          $this->xml .= '<COFINS>';
          $this->xml .= '<COFINSNT>';
          $this->xml .= '<CST>'.$prod->cofins->cst.'</CST>';
          $this->xml .= '</COFINSNT>';
          $this->xml .= '</COFINS>';
          break;
        case '50':
        case '70':
        case '75':
        case '98':
          $this->xml .= '<PIS>';
          $this->xml .= '<PISOutr>';
          $this->xml .= '<CST>'.$prod->pis->cst.'</CST>';
          $this->xml .= '<qBCProd>'.number_format($prod->pis->base,2,'.','').'</qBCProd>';
          $this->xml .= '<vAliqProd>'.number_format($prod->pis->pc,2,'.','').'</vAliqProd>';
          $this->xml .= '<vPIS>'.number_format($prod->pis->valor,2,'.','').'</vPIS>';
          $this->xml .= '</PISOutr>';
          $this->xml .= '</PIS>';
          $this->xml .= '<COFINS>';
          $this->xml .= '<COFINSOutr>';
          $this->xml .= '<CST>'.$prod->cofins->cst.'</CST>';
          $this->xml .= '<qBCProd>'.number_format($prod->cofins->base,2,'.','').'</qBCProd>';
          $this->xml .= '<vAliqProd>'.number_format($prod->cofins->pc,2,'.','').'</vAliqProd>';
          $this->xml .= '<vCOFINS>'.number_format($prod->cofins->valor,2,'.','').'</vCOFINS>';
          $this->xml .= '</COFINSOutr>';
          $this->xml .= '</COFINS>';
          break;
      }
      //
      // icms partilha
      if (($prod->partilha->vicmsufdest>0) and ($this->ide->indfinal==1) and (substr($prod->cfop,0,1)<>'7')) {
        $this->xml .= '<ICMSUFDest>';
        $this->xml .= '<vBCUFDest>'     .number_format($prod->partilha->vbcufdest,2,'.','').'</vBCUFDest>';
        $this->xml .= '<pFCPUFDest>'    .number_format($prod->partilha->pfcpufdest,2,'.','').'</pFCPUFDest>';
        $this->xml .= '<pICMSUFDest>'   .number_format($prod->partilha->picmsufdest,2,'.','').'</pICMSUFDest>';
        $this->xml .= '<pICMSInter>'    .number_format($prod->partilha->picmsinter,2,'.','').'</pICMSInter>';
        $this->xml .= '<pICMSInterPart>'.number_format($prod->partilha->picmsinterpart,2,'.','').'</pICMSInterPart>';
        $this->xml .= '<vFCPUFDest>'    .number_format($prod->partilha->vfcpufdest,2,'.','').'</vFCPUFDest>';
        $this->xml .= '<vICMSUFDest>'   .number_format($prod->partilha->vicmsufdest,2,'.','').'</vICMSUFDest>';
        $this->xml .= '<vICMSUFRemet>'  .number_format($prod->partilha->vicmsufremet,2,'.','').'</vICMSUFRemet>';
        $this->xml .= '</ICMSUFDest>';
        /**/
        $this->totais->vfcpufdest   +=  $prod->partilha->vfcpufdest;
        $this->totais->vicmsufdest  +=  $prod->partilha->vicmsufdest;
        $this->totais->vicmsufremet +=  $prod->partilha->vicmsufremet;
      }
      $this->xml .= '</imposto>';
      
      if ($prod->infadprod!='')
        $this->xml .= '<infAdProd>'.$prod->infadprod.'</infAdProd>';
      
      // fim dos produtos/impostos
      $this->xml .= '</det>';
    }
    
    // totais
    $this->xml .= '<total>';
    $this->xml .= '<ICMSTot>';
    $this->xml .= '<vBC>'.number_format($this->totais->vbc,2,'.','').'</vBC>';
    $this->xml .= '<vICMS>'.number_format($this->totais->vicms,2,'.','').'</vICMS>';
    $this->xml .= '<vICMSDeson>'.number_format($this->totais->vicmsdeson,2,'.','').'</vICMSDeson>';

    
    if ($this->totais->vfcpufdest>0) {
      $this->xml .= '<vFCPUFDest>'.number_format($this->totais->vfcpufdest,2,'.','').'</vFCPUFDest>';
    }  
    if ($this->totais->vicmsufdest>0) {
      $this->xml .= '<vICMSUFDest>'.number_format($this->totais->vicmsufdest,2,'.','').'</vICMSUFDest>';
      $this->xml .= '<vICMSUFRemet>'.number_format($this->totais->vicmsufremet,2,'.','').'</vICMSUFRemet>';
    }
    $this->xml .= '<vFCP>0.00</vFCP>';
    $this->xml .= '<vBCST>'.number_format($this->totais->vbcst,2,'.','').'</vBCST>';
    $this->xml .= '<vST>'.number_format($this->totais->vst,2,'.','').'</vST>';
    $this->xml .= '<vFCPST>0.00</vFCPST>';
    $this->xml .= '<vFCPSTRet>0.00</vFCPSTRet>';
    $this->xml .= '<vProd>'.number_format($this->totais->vprod,2,'.','').'</vProd>';
    $this->xml .= '<vFrete>'.number_format($this->totais->vfrete,2,'.','').'</vFrete>';
    $this->xml .= '<vSeg>'.number_format($this->totais->vseg,2,'.','').'</vSeg>';
    $this->xml .= '<vDesc>'.number_format($this->totais->vdesc,2,'.','').'</vDesc>';
    $this->xml .= '<vII>'.number_format($this->totais->vii,2,'.','').'</vII>';
    $this->xml .= '<vIPI>'.number_format($this->totais->vipi,2,'.','').'</vIPI>';
    $this->xml .= '<vIPIDevol>0.00</vIPIDevol>';
    $this->xml .= '<vPIS>'.number_format($this->totais->vpis,2,'.','').'</vPIS>';
    $this->xml .= '<vCOFINS>'.number_format($this->totais->vcofins,2,'.','').'</vCOFINS>';
    $this->xml .= '<vOutro>'.number_format($this->totais->voutro,2,'.','').'</vOutro>';
    $this->xml .= '<vNF>'.number_format($this->totais->vnf,2,'.','').'</vNF>';
    $this->xml .= '<vTotTrib>'.number_format($this->totais->vtottrib,2,'.','').'</vTotTrib>';
    $this->xml .= '</ICMSTot>';
    
    // totais issqn
    if ($this->totais->issqn->vservico>0) {
      $this->xml .= '<ISSQNtot>';
      $this->xml .= '<vServ>'.number_format($this->totais->issqn->vservico,2,'.','').'</vServ>';
      $this->xml .= $this->totais->issqn->totais->vbc>0         ?'<vBC>'.number_format($this->totais->issqn->totais->vbc,2,'.','').'</vBC>'                          :'';
      $this->xml .= $this->totais->issqn->totais->viss>0        ?'<vISS>'.number_format($this->totais->issqn->totais->viss,2,'.','').'</vISS>'                       :'';
      $this->xml .= $this->totais->issqn->totais->vpis>0        ?'<vPIS>'.number_format($this->totais->issqn->totais->vpis,2,'.','').'</vPIS>'                       :'';
      $this->xml .= $this->totais->issqn->totais->vcofins>0     ?'<vCOFINS>'.number_format($this->totais->issqn->totais->vcofins,2,'.','').'</vCOFINS>'              :'';
      $this->xml .= '<dCompet>'+FormatDateTime('YYYY-MM-DD',DtEmissao).'</dCompet>';
      $this->xml .= $this->totais->issqn->totais->vdeducao>0    ?'<vDeducao>'.number_format($this->totais->issqn->totais->vdeducao,2,'.','').'</vDeducao>'           :'';
      $this->xml .= $this->totais->issqn->totais->voutro>0      ?'<vOutro>'.number_format($this->totais->issqn->totais->voutro,2,'.','').'</vOutro>'                 :'';
      $this->xml .= $this->totais->issqn->totais->vdescincond>0 ?'<vDescIncond>'.number_format($this->totais->issqn->totais->vdescincond,2,'.','').'</vDescIncond>'  :'';
      $this->xml .= $this->totais->issqn->totais->vdesccond>0   ?'<vDescCond>'.number_format($this->totais->issqn->totais->vdesccond,2,'.','').'</vDescCond>'        :'';
      $this->xml .= $this->totais->issqn->totais->vissret>0     ?'<vISSRet>'.number_format($this->totais->issqn->totais->vissret,2,'.','').'</vISSRet>'              :'';
      $this->xml .= '<cRegTrib>'.$this->totais->issqn->cregtrib.'</cRegTrib>';
      $this->xml .= '</ISSQNtot>';
    }
    //-- <retTrib> -- Retencao de tributos federais
    /*
    if (Total.retTrib.vRetPIS>0)
    Or (Total.retTrib.vRetCOFINS>0)
    Or (Total.retTrib.vRetCSLL>0)
    Or (Total.retTrib.vBCIRRF>0)
    Or (Total.retTrib.vIRRF>0)
    Or (Total.retTrib.vBCRetPrev>0)
    Or (Total.retTrib.vRetPrev>0)
    {
      $this->xml .= '<retTrib>';
      if (Total.retTrib.vRetPIS>0)    { $this->xml .= '<vRetPIS>'   +FMTP('#0.00',Total.retTrib.vRetPIS)    +'</vRetPIS>';
      if (Total.retTrib.vRetCOFINS>0) { $this->xml .= '<vRetCOFINS>'+FMTP('#0.00',Total.retTrib.vRetCOFINS) +'</vRetCOFINS>';
      if (Total.retTrib.vRetCSLL>0)   { $this->xml .= '<vRetCSLL>'  +FMTP('#0.00',Total.retTrib.vRetCSLL)   +'</vRetCSLL>';
      if (Total.retTrib.vBCIRRF>0)    { $this->xml .= '<vBCIRRF>'   +FMTP('#0.00',Total.retTrib.vBCIRRF)    +'</vBCIRRF>';
      if (Total.retTrib.vIRRF>0)      { $this->xml .= '<vIRRF>'     +FMTP('#0.00',Total.retTrib.vIRRF)      +'</vIRRF>';
      if (Total.retTrib.vBCRetPrev>0) { $this->xml .= '<vBCRetPrev>'+FMTP('#0.00',Total.retTrib.vBCRetPrev) +'</vBCRetPrev>';
      if (Total.retTrib.vRetPrev>0)   { $this->xml .= '<vRetPrev>'  +FMTP('#0.00',Total.retTrib.vRetPrev)   +'</vRetPrev>';
      $this->xml .= '</retTrib>';
    }
    */
    $this->xml .= '</total>';
    
    // transportadora
    if ($this->transp->cnpj!='') {
      $this->xml .= '<transp>';
      $this->xml .= '<modFrete>'.$this->transp->modfrete.'</modFrete>';
      $this->xml .= '<transporta>';
      $this->xml .= strlen($this->transp->cnpj)==14?'<CNPJ>'.$this->transp->cnpj.'</CNPJ>':'<CPF>'.$this->transp->cnpj.'</CPF>';
      $this->xml .= '<xNome>'.$this->transp->razao.'</xNome>';
      $this->xml .= $this->transp->ie!=''?'<IE>'+Transportadora.IE+'</IE>':'';
      if($this->transp->endereco->xlgr!="")
        $this->xml .= '<xEnder>'.$this->transp->endereco->xlgr.'</xEnder>';
      if($this->transp->endereco->xmun!="")
        $this->xml .= '<xMun>'.$this->transp->endereco->xmun.'</xMun>';
      $this->xml .= '<UF>'.$this->transp->endereco->uf.'</UF>';
      $this->xml .= '</transporta>';
      $this->xml .= '<vol>';
      $this->xml .= '<qVol>'.$this->transp->volume.'</qVol>';
      $this->xml .= '<esp>'.$this->transp->especie.'</esp>';
      //-- <marca>
      //-- <vVol>
      if ($this->transp->pesol>0) 
        $this->xml .= '<pesoL>'.number_format($this->transp->pesol,3,'.','').'</pesoL>';
      if ($this->transp->pesob>0) 
        $this->xml .= '<pesoB>'.number_format($this->transp->pesob,3,'.','').'</pesoB>';
      //-- <lacres>
      $this->xml .= '</vol>';
      $this->xml .= '</transp>';
    } else {
      $this->xml .= '<transp>';
      $this->xml .= '<modFrete>'.$this->transp->modfrete.'</modFrete>';
      $this->xml .= '</transp>';
    }
    
    // dados da cobranca
    if ( count($this->cobr->dupl)>0 ){
      $this->xml .= '<pag>';
      for($i=0;$i<count($this->cobr->dupl);$i++){
        $this->xml .= '<detPag>';
        //$this->xml .= '<nDup>'.$this->ide->nnf.'/'.($i+1).'</nDup>';
        //$this->xml .= '<dVenc>'.date('Y-m-d',strtotime($this->cobr->dupl[$i]->dvenc)).'</dVenc>';
        //$this->xml .= '<vDup>'.number_format($this->cobr->dupl[$i]->vdup,2,'.','').'</vDup>';
        $this->xml .= '<tPag>99</tPag>';
        $this->xml .= '<vPag>'.number_format($this->cobr->dupl[$i]->vdup,2,'.','').'</vPag>';
        $this->xml .= '</detPag>';
      }
      $this->xml .= '</pag>';
    }
    
    // observacoes
    if (($this->temst) or ($this->obs!='')){
      $this->xml .= '<infAdic>';
      if ($this->temst){
        $this->xml .= '<infAdFisco>';
        $this->xml .= 'ICMS RECOLHIDO ANTERIORMENTE POR SUBSTITUICAO TRIBUTARIA.';
        $this->xml .= '</infAdFisco>';
      }
      If ($this->obs!='') {
        $this->xml .= '<infCpl>';

        if (($this->ide->indfinal==1) and (strpos($this->obs,'VENDA A CONSUMIDOR FINAL')) and ($this->finnfe!=4) and ($this->dest->cpais==1058))
          $this->xml .= 'VENDA A CONSUMIDOR FINAL;';

        if (($this->totais->vtottrib>0) and (!strpos($this->obs,'IMPOSTO APROXIMADO')))
          $this->xml .= 'IMPOSTO APROXIMADO R$ '.number_format($this->totais->vtottrib,2,'.','').';';

        If ($this->obs<>'')
          $this->xml .= $this->obs;

        if ($this->totais->vicmsufdest>0)
          $this->xml .= ';Partilha do ICMS destinada ao Estado de '.$this->dest->endereco->uf.' no Valor de R$ '.number_format($this->totais->vicmsufdest,2,',','').'.';

        $this->xml .= '</infCpl>';
      }
      $this->xml .= '</infAdic>';
    }
    
    // local de transposicao da mercadoria quando exportacao
    if (($this->ide->tpnf==1) and ($this->dest->cpais!=1058)){
      /*
      FXml:=FXml+'<exporta>';
      FXml:=FXml+'<UFSaidaPais>'+Exporta.UFSaida+'</UFSaidaPais>';
      FXml:=FXml+'<xLocExporta>'+Exporta.Local+'</xLocExporta>';
      FXml:=FXml+'</exporta>';
      */
    }
    
    // numero do pedido quando houver
    if ($this->pedido!=''){
      $this->xml .= '<compra>';
      $this->xml .= '<xPed>'.$this->pedido.'</xPed>';
      $this->xml .= '</compra>';
    }
    
    // embrulha
    $this->xml = '<infNFe Id="'.$this->ide->id.'" versao="'.$this->versao.'">' . $this->xml . '</infNFe>';
    $this->xml = '<NFe xmlns="http://www.portalfiscal.inf.br/nfe">' . $this->xml . '</NFe>';
    
    return $this->xml;
  }
}

//******************************//
//** FUNCOES DE ENVIO/RETORNO   //
//** CARTA DE CORRECAO          //
//** CONSULTA CNPJ              //
//** ENVIO / RETORNO DE NFE     //
//** INUTILIZAÇÃO DE NUMERARIO  //
//** CONSULTA DE STATUS         //
//******************************//
  /********************************************************************************************************/
  /* CONJUNTO DE FUNÇÕES PARA TRATAMENTO DOS SERVIÇOS DA SEFAZ
  /*
  /* PRIVADA, PUBLICA - se refere a visibilidade da função PRIVADA são manipuladas pelas funções publicas
  /*                    PUBLICAS são as funções destinadas ao manuseio pelo programador.
  /*
  /* - PRIVADA => enviarequisicao(<nomeServico>,<metodo>,<url>,<cabecMsg>,<dadosMsg>)
  /*   <?> esta função envia o xml para a sefaz
  /*   parametros:
  /*   <nomeServico> nome do sevico para preencher na requisicao
  /*   <metodo>      metodo a ser acionado pela url
  /*   <url>         url do wsdl
  /*   <cabecMsg>    cabecalho da mensagem a ser enviadda
  /*   <dadosMsg>    xml enviado conforme servico solicitado
  /*
  /* - PRIVADA => definecertificado(<arquivo>,<senha>)
  /*   <?> esta função registra os dados do certificado que será usada nas funções para integração com a SEFAZ
  /*   parametros:
  /*   <arquivo> local fisico do arquivo PEM (A1)
  /*   <senha>   senha do certificado
  /*
  /* - PRIVADA => jsonnfe(<servico>,<tpAmb>,<UF>,<contingencia>)
  /*   <?> esta função retorna o xml que será usado por cada um dos serviços integrados
  /*   parametros:
  /*   <servico>      status   - consulta status do serviço fornecido pelo serviço
  /*                  conscad  - retorna os dados do cnpj informado
  /*   <tpAmb>        1 (producao), 2 (homologacao), 99 SVC-AN (producao), 98 SVC-AN (homologacao)
  /*   <UF>           UF a qual deseja informação sobre o status, caso queira obter informação sobre o SVC de
  /*                  uma determinada UF insira a letra "C" antes da sigla, ex: CMG para contingencia de MG
  /*   <contingencia> se informado "true" o ambiente de contingencia SVC-AN será utilizado
  /*
  /* - PRIVADA => codigoUF(<UF>)
  /*   <?> retorna o código de uma determinada UF
  /*   <UF>      UF a qual deseja informação sobre o status
  /*
  /* - PUBLICA => consultaservico(<tpAmb>,<xUF>,<contingencia>)
  /*   <?> esta função retorna status do serviço de uma determinada UF
  /*   parametros:
  /*   <tpAmb>        1 (producao), 2 (homologacao)
  /*   <UF>           xUF a qual deseja informação sobre o status
  /*   <contingencia> se informado "true" o ambiente de contingencia SVC-AN será utilizado
  /*
  /* - PUBLICA => consultacadastro(<cnpj>,<UF>,<contingencia>)
  /*   <?> esta função retorna os dados de cadastro do cnpj informado se ele tiver IE informada
  /*      no estado da consulta, caso não tenha retorna um erro.
  /*   parametros:
  /*   <cnpj>         cnpj sem pontos da PJ a ser consultado
  /*   <UF>           UF a qual deseja informação sobre o status
  /*   <contingencia> se informado "true" o ambiente de contingencia SVC-AN será utilizado
  /*
  /* - PUBLICA => cartacorrecao(<tpAmb>,<xUF>,<contingencia>,<seq>,<chavenf>,<motivo>)
  /*   <?> esta função retorna status do serviço de uma determinada UF
  /*   parametros:
  /*   <tpAmb>        1 (producao), 2 (homologacao)
  /*   <UF>           xUF a qual deseja informação sobre o status
  /*   <contingencia> se informado "true" o ambiente de contingencia SVC-AN será utilizado
  /*   <seq>          sequencia da carta
  /*   <chavenf>      chave da nota referenciada
  /*   <motivo>       descricao da correcao
  /*   <cnpjemitente> cnpj do emitente
  /*
  /********************************************************************************************************/
  function codigoUF($UF){
    switch ($UF){
      case "RO": return 11; break;
      case "AC": return 12; break;
      case "AM": return 13; break;
      case "RR": return 14; break;
      case "PA": return 15; break;
      case "AP": return 16; break;
      case "TO": return 17; break;
      case "MA": return 21; break;
      case "PI": return 22; break;
      case "CE": return 23; break;
      case "RN": return 24; break;
      case "PB": return 25; break;
      case "PE": return 26; break;
      case "AL": return 27; break;
      case "SE": return 28; break;
      case "BA": return 29; break;
      case "MG": return 31; break;
      case "ES": return 32; break;
      case "RJ": return 33; break;
      case "SP": return 35; break;
      case "PR": return 41; break;
      case "SC": return 42; break;
      case "RS": return 43; break;
      case "MS": return 50; break;
      case "MT": return 51; break;
      case "GO": return 52; break;
      case "DF": return 53; break;
      default: return 00;
    }
  }
  function definecertificado($arquivoPem,$senhaCert){
    $listaErr = [];
    try {
      if( (!file_exists($arquivoPem.'.pfx')) and (!file_exists($arquivoPem.'.p12')) ):
        $listaErr[]=['STATUS'=>'ERRO','TAG'=>'CERTIFICADO','ERRO'=>'ARQUIVO .PFX/.P12 NAO ENCONTRADO'];
        return $listaErr;
      endif;      
      
      if(!file_exists($arquivoPem.'.pem')):
        $listaErr[]=['STATUS'=>'ERRO','TAG'=>'CERTIFICADO','ERRO'=>'ARQUIVO .PEM NAO ENCONTRADO'];
        return $listaErr;
      endif;      
      
      $_SESSION['CERT_PEM']   = $arquivoPem.'.pem';
      if (file_exists($arquivoPem.'.pfx'))
        $_SESSION['CERT_PFX']   = $arquivoPem.'.pfx';
      else
        $_SESSION['CERT_PFX']   = $arquivoPem.'.p12';
      
      $_SESSION['CERT_PASS']  = $senhaCert;
      //--
      //-- gerando a chave privada
      $fp       = fopen($_SESSION['CERT_PEM'], "r");
      $priv_key = fread($fp, 8192);
      fclose($fp);
      $_SESSION['CERT_PRIVKEY'] = openssl_get_privatekey($priv_key);
      //--
      //-- gerando X509 certificate
      $x509CertData = [];
      $pkcs12       = file_get_contents( $_SESSION['CERT_PFX'] );
      if ( ! openssl_pkcs12_read( $pkcs12, $x509CertData, $_SESSION['CERT_PASS'] ) ) {    
        $listaErr[]=['STATUS'=>'ERRO','TAG'=>'CERTIFICADO','ERRO'=>'Certificado não pode ser aberto. Arquivo corrompido ou formato invalido!'];
      }  
      //***********************************************************************************************
      //** Trata Certificado tirando INICIO -----BEGIN CERTIFICATE----- e FIM -----END CERTIFICATE-----
      //***********************************************************************************************
      $key_tratado     = explode('-----BEGIN CERTIFICATE-----',$x509CertData['cert']); 
      $key_tratado     = $key_tratado[1]; 
      $key_tratado     = explode('-----END CERTIFICATE-----',$key_tratado); 
      $key_tratado     = $key_tratado[0]; 
      
      $X509Certificate = preg_replace( "/[\n]/", '' ,$key_tratado);
      $_SESSION['CERT_X509'] = $key_tratado;
      //**
      return[];
    } catch(Exception $e) {
      $listaErr[]=['STATUS'=>'ERRO','TAG'=>'CERTIFICADO','ERRO'=>$e->getMessage()];
      return $listaErr;
    }
  }

  function jsonnfe($executar,$tpAmb,$cUF,$contingencia){
    //--
    //-- carrega o json de definições do serviço
    //-- http://www.nfe.fazenda.gov.br/portal/WebServices.aspx - Produção
    //-- http://hom.nfe.fazenda.gov.br/PORTAL/WebServices.aspx#SVC-AN - Homologação
    $stringjson = '{ 
      "servicos":
        [ ';

    // mg - producao
    $stringjson .= '
           {"uf":"31","nome":"nfeenvio"   ,"servico":"NFeAutorizacao4"      ,"metodo":"nfeAutorizacaoLote"     ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.mg.gov.br/nfe2/services/NFeAutorizacao4?wsdl"                        , "retorno":"retEnviNFe"}
          ,{"uf":"31","nome":"nferetorno" ,"servico":"NFeRetAutorizacao4"   ,"metodo":"nfeRetAutorizacaoLote"  ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.mg.gov.br/nfe2/services/NFeRetAutorizacao4?wsdl"                     , "retorno":"retConsReciNFe"}
          ,{"uf":"31","nome":"inutnf"     ,"servico":"NFeInutilizacao4"     ,"metodo":"nfeInutilizacaoNF"      ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.mg.gov.br/nfe2/services/NFeInutilizacao4?wsdl"                       , "retorno":"retInutNFe"}
          ,{"uf":"31","nome":"carta"      ,"servico":"NFeRecepcaoEvento4"   ,"metodo":"nfeRecepcaoEvento"      ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.mg.gov.br/nfe2/services/NFeRecepcaoEvento4?wsdl"                     , "retorno":"retEnvEvento"}
          ,{"uf":"31","nome":"cancela"    ,"servico":"NFeRecepcaoEvento4"   ,"metodo":"nfeRecepcaoEvento"      ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.mg.gov.br/nfe2/services/NFeRecepcaoEvento4?wsdl"                     , "retorno":"retEnvEvento"}
          ';          

    // mg - homologacao
    $stringjson .= '
          ,{"uf":"31","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"       ,"metodo":"nfeAutorizacaoLote"     ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeAutorizacao4?wsdl"                       , "retorno":"retEnviNFe"}
          ,{"uf":"31","nome":"nferetorno","servico":"NFeRetAutorizacao4"    ,"metodo":"nfeRetAutorizacaoLote"  ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeRetAutorizacao4?wsdl"                    , "retorno":"retConsReciNFe"}
          ,{"uf":"31","nome":"inutnf"    ,"servico":"NFeInutilizacao4"      ,"metodo":"nfeInutilizacaoNF"      ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeInutilizacao4?wsdl"                      , "retorno":"retInutNFe"}
          ';          

    // sp - producao
    /*
    $stringjson .= '
          ,{"uf":"35","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"       ,"metodo":"nfeAutorizacaoLote"     ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nfeautorizacao4.asmx?wsdl"                              , "retorno":"nfeAutorizacaoLoteResult"}
          ,{"uf":"35","nome":"nferetorno","servico":"NFeRetAutorizacao4"    ,"metodo":"nfeRetAutorizacaoLote"  ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nferetautorizacao4.asmx?wsdl"                           , "retorno":"nfeRetAutorizacaoLoteResult"}
          ,{"uf":"35","nome":"inutnf"    ,"servico":"NFeInutilizacao4"      ,"metodo":"NFeInutilizacao4"       ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nfeinutilizacao4.asmx?wsdl"                             , "retorno":"nfeInutilizacaoNFResult"}
          ,{"uf":"35","nome":"carta"     ,"servico":"NFeRecepcaoEvento4"    ,"metodo":"nfeRecepcaoEvento"      ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx?wsdl"                           , "retorno":"nfeRecepcaoEventoResult"}
          ';          
    */
    $stringjson .= '
          ,{"uf":"35","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"       ,"metodo":"nfeAutorizacaoLote"     ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nfeautorizacao4.asmx?wsdl"                              , "retorno":"any"}
          ,{"uf":"35","nome":"nferetorno","servico":"NFeRetAutorizacao4"    ,"metodo":"nfeRetAutorizacaoLote"  ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nferetautorizacao4.asmx?wsdl"                           , "retorno":"any"}
          ,{"uf":"35","nome":"inutnf"    ,"servico":"NFeInutilizacao4"      ,"metodo":"NFeInutilizacao4"       ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nfeinutilizacao4.asmx?wsdl"                             , "retorno":"any"}
          ,{"uf":"35","nome":"carta"     ,"servico":"NFeRecepcaoEvento4"    ,"metodo":"nfeRecepcaoEvento"      ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx?wsdl"                           , "retorno":"any"}
          ';          

    // sp - homologacao
    $stringjson .= '
          ,{"uf":"35","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"       ,"metodo":"nfeAutorizacaoLote"     ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeautorizacao4.asmx?wsdl"                  , "retorno":"any"}
          ,{"uf":"35","nome":"nferetorno","servico":"NFeRetAutorizacao4"    ,"metodo":"nfeRetAutorizacaoLote"  ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://homologacao.nfe.fazenda.sp.gov.br/ws/nferetautorizacao4.asmx?wsdl"               , "retorno":"any"}
          ,{"uf":"35","nome":"inutnf"    ,"servico":"NFeInutilizacao4"      ,"metodo":"NFeInutilizacao4"       ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeinutilizacao4.asmx?wsdl"                 , "retorno":"any"}
          ,{"uf":"35","nome":"carta"     ,"servico":"NFeRecepcaoEvento4"    ,"metodo":"nfeRecepcaoEvento"      ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://homologacao.nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx?wsdl"               , "retorno":"any"}
          ';          

    // svc-rs - producao (RJ)
    $stringjson .= '
          ,{"uf":"33","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"      ,"metodo":"nfeAutorizacaoLote"      ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx?wsdl"                 , "retorno":"any"}
          ,{"uf":"33","nome":"nferetorno","servico":"NFeRetAutorizacao4"   ,"metodo":"nfeRetAutorizacaoLote"   ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx?wsdl"           , "retorno":"any"}
          ,{"uf":"33","nome":"inutnf"    ,"servico":"NFeInutilizacao4"      ,"metodo":"NFeInutilizacao4"       ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx?wsdl"                 , "retorno":"any"}
          ,{"uf":"33","nome":"carta"     ,"servico":"NFeRecepcaoEvento4"    ,"metodo":"nfeRecepcaoEvento"      ,"amb":"1" ,"modelo":"3","versaoDados":"4.00","url":"https://homologacao.nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx?wsdl"              , "retorno":"any"}
          ';

    // svc-rs - homologacao (RJ)
    $stringjson .= '
          ,{"uf":"33","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"      ,"metodo":"nfeAutorizacaoLote"      ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe-homologacao.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx?wsdl"     , "retorno":"any"}
          ,{"uf":"33","nome":"nferetorno","servico":"NFeRetAutorizacao4"   ,"metodo":"nfeRetAutorizacaoLote"   ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe-homologacao.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx?wsdl", "retorno":"any"}
          ,{"uf":"33","nome":"inutnf"    ,"servico":"NFeInutilizacao4"      ,"metodo":"NFeInutilizacao4"       ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe-homologacao.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx?wsdl"   , "retorno":"any"}
          ,{"uf":"33","nome":"carta"     ,"servico":"NFeRecepcaoEvento4"    ,"metodo":"nfeRecepcaoEvento"      ,"amb":"2" ,"modelo":"3","versaoDados":"4.00","url":"https://nfe-homologacao.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx?wsdl"     , "retorno":"any"}
          ';

    // svc-an - producao
    $stringjson .= '
          ,{"uf":"31","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"      ,"metodo":"nfeAutorizacaoLote"     ,"amb":"99" ,"modelo":"3","versaoDados":"4.00","url":"https://www.svc.fazenda.gov.br/NFeAutorizacao4/NFeAutorizacao4.asmx?wsdl"               , "retorno":"any"}
          ,{"uf":"31","nome":"nferetorno","servico":"NFeRetAutorizacao4"   ,"metodo":"nfeRetAutorizacaoLote"  ,"amb":"99" ,"modelo":"3","versaoDados":"4.00","url":"https://www.svc.fazenda.gov.br/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx?wsdl"         , "retorno":"any"}
          ';

    // svc-an - homologacao
    $stringjson .= '
          ,{"uf":"31","nome":"nfeenvio"  ,"servico":"NFeAutorizacao4"      ,"metodo":"nfeAutorizacaoLote"     ,"amb":"98" ,"modelo":"3","versaoDados":"4.00","url":"https://hom.svc.fazenda.gov.br/NFeAutorizacao4/NFeAutorizacao4.asmx?wsdl"               , "retorno":"any"}
          ,{"uf":"31","nome":"nferetorno","servico":"NFeRetAutorizacao4"   ,"metodo":"nfeRetAutorizacaoLote"  ,"amb":"98" ,"modelo":"3","versaoDados":"4.00","url":"https://hom.svc.fazenda.gov.br/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx?wsdl"         , "retorno":"any"}
          ';

    $stringjson .= '
        ]
      ,"modelos":
        [
          {  "codigo":"1"
            ,"msgCabec":
              [ {"linha": "<nfeCabecMsg xmlns=\"http://www.portalfiscal.inf.br/nfe/wsdl/[servico]\">"}
                ,{"linha": "<cUF>[cUF]</cUF>"}
                ,{"linha": "<versaoDados>[versaoDados]</versaoDados>"}
                ,{"linha": "</nfeCabecMsg>"}
              ]
            ,"msgDados":
              [ {"linha": "<nfeDadosMsg xmlns=\"http://www.portalfiscal.inf.br/nfe/wsdl/[servico]\">"}
                ,{"linha": "<consStatServ xmlns=\"http://www.portalfiscal.inf.br/nfe\" versao=\"3.10\">"}
                ,{"linha": "<tpAmb>[tpAmb]</tpAmb>"}
                ,{"linha": "<cUF>[cUF]</cUF>"}
                ,{"linha": "<xServ>STATUS</xServ>"}
                ,{"linha": "</consStatServ>"}
                ,{"linha": "</nfeDadosMsg>"}
              ]
            }
          ,{ "codigo":"2"
            ,"msgCabec":
              [ {"linha": "<nfeCabecMsg xmlns=\"http://www.portalfiscal.inf.br/nfe/wsdl/[servico]\">"}
                ,{"linha": "<cUF>[cUF]</cUF>"}
                ,{"linha": "<versaoDados>[versaoDados]</versaoDados>"}
                ,{"linha": "</nfeCabecMsg>"}
              ]
            ,"msgDados":
              [ {"linha": "<nfeDadosMsg xmlns=\"http://www.portalfiscal.inf.br/nfe/wsdl/[servico]\">"}
                ,{"linha": "<ConsCad xmlns=\"http://www.portalfiscal.inf.br/nfe\" versao=\"2.00\">"}
                ,{"linha": "<infCons>"}
                ,{"linha": "<xServ>CONS-CAD</xServ>"}
                ,{"linha": "<UF>[UF]</UF>"}
                ,{"linha": "<CNPJ>[CNPJ]</CNPJ>"}
                ,{"linha": "</infCons>"}
                ,{"linha": "</ConsCad>"}
                ,{"linha": "</nfeDadosMsg>"}
              ]
            }
          ,{ "codigo":"3"
            ,"msgCabec": []
            ,"msgDados":
              [  {"linha": "<nfeDadosMsg xmlns=\"http://www.portalfiscal.inf.br/nfe/wsdl/[servico]\">"}
                ,{"linha": "[xml]"}
                ,{"linha": "</nfeDadosMsg>"}
              ]
            }

          ,{ "codigo":"4"
            ,"msgCabec": []
            ,"msgDados": 
              [ {"linha": "<nfeDistDFeInteresse xmlns=\"http://www.portalfiscal.inf.br/nfe/wsdl/[servico]\">"}
                ,{"linha": "<nfeDadosMsg>[xml]</nfeDadosMsg>"}
                ,{"linha": "</nfeDistDFeInteresse>"}
              ]
            }
        ]
    }
    ';
    $json      = json_decode( $stringjson );
    $codmodelo = 0;
    /*
    * Se estiver em modo de contingência
    */
    if (($contingencia==true) and ($tpAmb==1))
      $tpAmb = '99';
    if (($contingencia==true) and ($tpAmb==2))
      $tpAmb = '98';
    
    foreach($json->servicos as $servico){
      if (($servico->nome==$executar) and ($servico->amb==$tpAmb) and ($servico->uf==$cUF)){
        $codmodelo    = $servico->modelo;
        $versaoDados  = $servico->versaoDados;
        $nomeservico  = $servico->servico;
        $metodo       = $servico->metodo;
        $url          = $servico->url;
        $retorno      = $servico->retorno;
        break;
      }
    }
    if ($codmodelo==0){
      throw new Exception("(".$executar.") Schema não definido para Serviço/Ambiente/UF.($executar/$tpAmb/$cUF)");
    }
    $cabecMsg = '';
    $dadosMsg = '';
    foreach($json->modelos as $modelo){
      if ($modelo->codigo==$codmodelo){
        foreach($modelo->msgCabec as $linha)
          $cabecMsg .= $linha->linha;
        foreach($modelo->msgDados as $linha)
          $dadosMsg .= $linha->linha;
        break;
      }
    }
    //--
    $resultado =
      Array(
         'versaoDados' => $versaoDados
        ,'nomeServico' => $nomeservico
        ,'metodo'      => $metodo
        ,'url'         => $url
        ,'cabecMsg'    => $cabecMsg
        ,'dadosMsg'    => $dadosMsg
        ,'retorno'     => $retorno
      );
    //--
    /*
    $h=fopen('jsonnfe.xml','w+');
    fwrite($h,$resultado);
    fclose($h);
    */
    return $resultado;
  };

  function enviarequisicao($nomeServico,$metodo,$url,$cabecMsg,$dadosMsg,$ret){
    //--
    // setando os parametros para envio da nota (certificado, etc)
    /* este funciona, 05.7.2017
    $params = array(
      'local_cert'          => $_SESSION['CERT_PEM']
      ,'passphrase'         => $_SESSION['CERT_PASS']
      ,'trace'              => 1
      ,'connection_timeout' => 300
      ,'encoding'           => 'UTF-8'
      ,'cache_wsdl'         => WSDL_CACHE_NONE
      ,'soap_version'       => SOAP_1_2
      ,'verify_peer'        => false
      ,'verify_peer_name'   => false
      ,'exceptions'         => 0
    );
    */
    $contextOptions = array(
                'ssl' => array(
                    'verify_peer'   => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ),
                'http' => array(
                    'timeout' => 5 //seconds
                )
     );    
    $stream_context = stream_context_create($contextOptions);
    //
    //
    // consumindo o método envio
    try {
      //-- abrindo a conexão com a sefaz
      // a linha abaixo funciona 5.7.2017
      //$connectionSoap = new SoapClient( $url, $params );
     
      $connectionSoap = new SoapClient($url, array(
                  'local_cert'          => $_SESSION['CERT_PEM'],
                  'passphrase'          => $_SESSION['CERT_PASS'],
                  'cache_wsdl'          => WSDL_CACHE_NONE,
                  'exceptions'          => 1,
                  'trace'               => 1,
                  'stream_context'      => $stream_context,
                  'soap_version'        => SOAP_1_2,
                  'connection_timeout'  => 5 //seconds
      ));
      
      //--
      //-- colocando o cabecMsg no soap
      if ($cabecMsg!=''){
        $varCabec = new SoapVar($cabecMsg,XSD_ANYXML);
        $header   = new SoapHeader($url,'nfeCabecMsg',$varCabec);
        $connectionSoap->__setSoapHeaders($header);
      }
      //--
      //-- ajustando o nfeDadosMsg para envio
      $varBody = new SoapVar($dadosMsg,XSD_ANYXML);
      //--
      // pode ser assim
      $result = $connectionSoap->__soapCall($metodo, array($varBody));
      //--
      if (is_soap_fault($result)) {
        $result = Array('erro'=> 'erro', 'cStat'=>'0', 'xMotivo'=>'Codigo:'.$result->faultcode.'.['.$result->faultstring.']');
      }
    }
    catch( SoapFault $e ) {
      $result = Array('erro'=> 'erro', 'cStat'=>'0', 'xMotivo'=>$e->getMessage());
      return $result;
    }
    //--
    //-- alguns webservice retornam "$result->any" (xml)
    //-- outros retornan "$result->retConsStatServ" (objeto)
    if (isset($result->$ret)) {
      $tiporetorno = gettype($result->$ret);
      if ($tiporetorno=='string')
        $xml = simplexml_load_string($result->$ret);
      if ($tiporetorno=='object')
        $xml = $result->$ret;
      if (isset($xml->infCons)){
        if (isset($xml->infCons->infCad)){
          return Array(
            'erro'        => 'ok'
           ,'cStat'       => $xml->infCons->cStat
           ,'xMotivo'     => $xml->infCons->xMotivo
           ,'IE'          => $xml->infCons->infCad->IE
           ,'cSit'        => $xml->infCons->infCad->cSit
           ,'CreditoICMS' => $xml->infCons->infCad->IndCredNFe
           ,'CreditoCTe'  => $xml->infCons->infCad->IndCredCTe
           ,'Razao'       => $xml->infCons->infCad->xNome
           ,'Regime'      => $xml->infCons->infCad->xRegApur
           ,'CNAE'        => $xml->infCons->infCad->CNAE
           ,'DtInicio'    => $xml->infCons->infCad->dIniAtiv
           ,'DtAlteracao' => $xml->infCons->infCad->dUltSit
           ,'Endereco'    => $xml->infCons->infCad->ender->xLgr
           ,'numero'      => $xml->infCons->infCad->ender->nro
           ,'Bairro'      => $xml->infCons->infCad->ender->xBairro
           ,'Cidade'      => $xml->infCons->infCad->ender->xMun
           ,'CEP'         => $xml->infCons->infCad->ender->CEP
          );
        }
        else
          return Array('erro' => 'erro', 'cStat'=>$xml->infCons->cStat, 'xMotivo' => $xml->infCons->xMotivo);

      } elseif (isset($xml->infInut)){
        $xml = $xml->infInut;
        $cStat = gettype($xml->cStat[0])=="array"?(string)$xml->cStat[0]:$xml->cStat;
        switch ($cStat){
          case '242':
          case '404':
          case '215':
          case '545':
          case '239':
          case '656':
            $xMotivo = gettype((string)$xml->xMotivo)=="array"?(string)$xml->xMotivo[0]:(string)$xml->xMotivo;
            return Array('erro'=> 'erro', 'cStat'=>$cStat.'', 'xMotivo'=>$xMotivo.'');
            break;
          default: 
            $xMotivo = gettype((string)$xml->xMotivo)=="array"?(string)$xml->xMotivo[0]:(string)$xml->xMotivo;
            if (($cStat=="102") or ($cStat=="563")){
              if (!isset($xml->nProt)){
                $nProt = substr($xMotivo,-16,15);
              }else 
                $nProt = gettype((string)$xml->nProt)=="array"?(string)$xml->nProt[0]:(string)$xml->nProt;
              return Array('erro'=> "ok", 'cStat'=>$cStat.'', 'xMotivo'=>$xMotivo, "protocolo"=>$nProt);
            } else 
              return Array('erro'=> "erro", 'cStat'=>$cStat.'', 'xMotivo'=>$xMotivo);
            break;
        }            

      } elseif ($nomeServico=='NFeDistribuicaoDFe'){
        $xml = simplexml_load_string($xml->any);
        $cStat = (string)$xml->cStat[0];
        switch ($cStat){
          case '215':
          case '404':
          case '402':
          case '238':
          case '239':
          case '252':
          case '489':
          case '490':
            return Array('erro'=> 'erro', 'cStat'=>$cStat.'', 'xMotivo'=>(string)$xml->xMotivo[0].'');
            break;
          default: 
            //
            // descompatando os dados de cada NSU
            $erro = 'ok';
            $listansu = [];
            if (isset($xml->loteDistDFeInt->docZip)){
              foreach($xml->loteDistDFeInt->docZip as $notazip){
                $nsu        = (string)$notazip['NSU'];
                $schema     = (string)$notazip['schema'];
                $zip        = base64_decode( (string)$notazip );
                $len        = strlen($zip);
                $bodylen    = $len - 10 - 8;               // 10-tamanho do cabecalho, 8-tamanho do rodapé do zip
                $body       = substr($zip, 10, $bodylen);  // a parte descompactada não leva em consideração cabecalho e rodape
                $unzip      = gzinflate($body, null);
                $listansu[] = ['nsu'=>$nsu, 'schema'=>$schema, 'xmlresumo'=>$unzip];
                /*
                $h = fopen('NFe'.$notazip->docZip['NSU'].'.xml','w+');
                fwrite($h, $data );
                fclose($h);
                */
              }
            }
            if (count($listansu)==0)
              $erro = 'erro';
            //
            return Array('erro'=> $erro, 'cStat'=>(string)$xml->cStat[0].'', 'xMotivo'=>(string)$xml->xMotivo[0].'', 'extras'=>$listansu);
            break;
        }            
      } else {
        if (isset($xml->cStat)){
          // ------------ //
          // erros gerais //
          // ------------ //
          // falha no schema
          if ($xml->cStat=='215') 
            return Array('erro'=> 'erro', 'cStat'=>(string)$xml->cStat[0].'', 'xMotivo'=>(string)$xml->xMotivo[0].'');
          
          // ------------------------ //
          // erros especificos de CCe //
          // ------------------------ //
          // lote carta de correcao aceito
          elseif ($xml->cStat=='128'){ 
            $ok = 'ok';
            switch($xml->retEvento->infEvento->cStat) {
              case '494': 
              case '574': 
                $ok = 'erro'; 
                break;
            }
            return Array('erro'=> $ok, 'cStat'=>$xml->retEvento->infEvento->cStat.'', 'xMotivo'=>$xml->retEvento->infEvento->xMotivo.'');

          } elseif ($xml->cStat=='103'){ 
            return Array('erro'=> 'ok', 'cStat'=>(string)$xml->cStat.'', 'nRec'=>(string)$xml->infRec->nRec, 'xMotivo'=>(string)$xml->xMotivo.'');
            
          } elseif ($xml->cStat=='104'){ 
            if ((string)$xml->protNFe->infProt->cStat!='100'){
              return Array('erro'=> 'ok', 'cStat'=>(string)$xml->cStat.''
                    , 'infProt'=>array(
                        'tpAmb' => (string)$xml->protNFe->infProt->tpAmb
                        ,'verAplic' => (string)$xml->protNFe->infProt->verAplic
                        ,'chNFe' => (string)$xml->protNFe->infProt->chNFe
                        ,'dhRecbto' => (string)$xml->protNFe->infProt->dhRecbto
                        ,'cStat' => (string)$xml->protNFe->infProt->cStat
                        ,'xMotivo' => (string)$xml->protNFe->infProt->xMotivo
                      )
                    , 'xMotivo'=>(string)$xml->xMotivo.'');
            } elseif ((string)$xml->protNFe->infProt->cStat=='100'){
              return Array('erro'=> 'ok', 'cStat'=>(string)$xml->cStat.''
                    , 'infProt'=>array(
                        'tpAmb' => (string)$xml->protNFe->infProt->tpAmb
                        ,'verAplic' => (string)$xml->protNFe->infProt->verAplic
                        ,'chNFe' => (string)$xml->protNFe->infProt->chNFe
                        ,'dhRecbto' => (string)$xml->protNFe->infProt->dhRecbto
                        ,'nProt' =>  (string)$xml->protNFe->infProt->nProt
                        ,'digVal' =>  (string)$xml->protNFe->infProt->digVal
                        ,'cStat' => (string)$xml->protNFe->infProt->cStat
                        ,'xMotivo' => (string)$xml->protNFe->infProt->xMotivo
                      )
                    , 'xMotivo'=>(string)$xml->xMotivo.'');
            }
          // ------ //
          // outros //
          // ------ //
          } else
            return Array('erro'=> 'ok', 'cStat'=>$xml->cStat.'', 'xMotivo'=>$xml->xMotivo.'');
        }else {
          //print_r($xml);          
          return Array('erro'=> 'ok', 'cStat'=>$xml->cStat.'', 'xMotivo'=>$xml->xMotivo.'');
        }
      }
    }else{
      //return Array('erro'=> 'erro', 'cStat'=>'0', 'xMotivo'=>'Erro de comunicação com o webservice.['.$result->faultstring.']');
      return $result;
    }
  };
  
  function assinaXML($sXML, $tagID ,$par_pkeyid,$par_pubkey) {
    /*
    * sXML        - xml a ser assinado
    * tagID       - a TAG que vai ser assinada
    * par_pkeyid  - chave privada do certificado
    * par_pubkey  - chave publica do certificado
    */
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = false;
    $dom->loadXML($sXML);
    //--
    $root = $dom->documentElement;
    $node = $dom->getElementsByTagName($tagID)->item(0);
    //--
    $Id     = trim($node->getAttribute("Id"));
    $idnome = str_replace("NFe","",$Id);
    //--
    //-- extrai os dados da tag para uma string
    $dados  = $node->C14N(FALSE, FALSE, NULL, NULL);
    //--
    //-- calcular o hash dos dados
    $hashValue = hash('sha1', $dados, TRUE);
    //--
    //converte o valor para base64 para serem colocados no xml
    $digValue = base64_encode($hashValue);
    //--
    //monta a tag da assinatura digital
    $Signature = $dom->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
    $root->appendChild($Signature);
    $SignedInfo = $dom->createElement('SignedInfo');
    $Signature->appendChild($SignedInfo);
    //--
    //Cannocalization
    $newNode = $dom->createElement('CanonicalizationMethod');
    $SignedInfo->appendChild($newNode);
    $newNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
    //--
    //-- SignatureMethod
    $newNode = $dom->createElement('SignatureMethod');
    $SignedInfo->appendChild($newNode);
    $newNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
    //--
    //-- Reference
    $Reference = $dom->createElement('Reference');
    $SignedInfo->appendChild($Reference);
    $Reference->setAttribute('URI', '#'.$Id);
    //--
    //-- Transforms
    $Transforms = $dom->createElement('Transforms');
    $Reference->appendChild($Transforms);
    //-- 
    //Transform
    $newNode = $dom->createElement('Transform');
    $Transforms->appendChild($newNode);
    $newNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
    //--
    //-- Transform
    $newNode = $dom->createElement('Transform');
    $Transforms->appendChild($newNode);
    $newNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
    //--
    //-- DigestMethod
    $newNode = $dom->createElement('DigestMethod');
    $Reference->appendChild($newNode);
    $newNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
    //--
    //-- DigestValue
    $newNode = $dom->createElement('DigestValue', $digValue);
    $Reference->appendChild($newNode);
    //--
    //-- extrai os dados a serem assinados para uma string
    $dados = $SignedInfo->C14N(FALSE, FALSE, NULL, NULL);
    //--
    //-- inicializa a variavel que vai receber a assinatura
    $signature = '';
    //--
    //-- executa a assinatura digital usando o resource da chave privada
    //$resp = openssl_sign($dados, $signature, openssl_pkey_get_private($this->certificado->sPrivateKey));
    $resp = openssl_sign($dados, $signature,$par_pkeyid);
    //-- 
    //codifica assinatura para o padrao base64
    $signatureValue = base64_encode($signature);
    //-- 
    //-- SignatureValue
    $newNode = $dom->createElement('SignatureValue', $signatureValue);
    $Signature->appendChild($newNode);
    //-- 
    //-- KeyInfo
    $KeyInfo = $dom->createElement('KeyInfo');
    $Signature->appendChild($KeyInfo);
    //--
    //-- X509Data
    $X509Data = $dom->createElement('X509Data');
    $KeyInfo->appendChild($X509Data);
    //--
    //-- X509Certificate
    $newNode = $dom->createElement('X509Certificate', $par_pubkey);
    $X509Data->appendChild($newNode);
    //--
    //-- grava na string o objeto DOM
    return $dom->saveXML();
  }  
  //-----------------------------------------------
  //-- ABAIXO AS FUNCOES PUBLICAS AOS PROGRAMADORES
  //-----------------------------------------------
  function inutilizanf($tpAmb,$cnpjemitente,$xUF='SP',$contingencia=false,$ano,$mod,$serie,$nfini,$nffin,$just){
    $xml      = jsonnfe('inutnf',$tpAmb,codigoUF($xUF),$contingencia);
    //--
    //-- ajustando o xml
    $cabecMsg = $xml['cabecMsg'];
    $cabecMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$cabecMsg);
    $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
    $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
    //--
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    $dadosMsg = str_replace('[UF]'          ,$xUF                 ,$dadosMsg);
    $dadosMsg = str_replace('[CNPJ]'        ,$cnpjemitente        ,$dadosMsg);
    //--
    //-- monta e assina o xml    
    $id = 'ID'
        .codigoUF($xUF)
        .substr($ano,2,2)
        .$cnpjemitente
        .str_pad($mod,2,'0',STR_PAD_LEFT)
        .str_pad($serie,3,'0',STR_PAD_LEFT)
        .str_pad($nfini,9,'0',STR_PAD_LEFT)
        .str_pad($nffin,9,'0',STR_PAD_LEFT);
    
    $xmlassinado =
     '<inutNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">'
    .'<infInut Id="'.$id.'">'
    .'<tpAmb>'.$tpAmb.'</tpAmb>'
    .'<xServ>INUTILIZAR</xServ>'
    .'<cUF>'.codigoUF($xUF).'</cUF>'
    .'<ano>'.substr($ano,2,2).'</ano>'
    .'<CNPJ>'.$cnpjemitente.'</CNPJ>' 
    .'<mod>'.str_pad(intval($mod),2,0,STR_PAD_LEFT).'</mod>'
    .'<serie>'.$serie.'</serie>'
    .'<nNFIni>'.$nfini.'</nNFIni>'
    .'<nNFFin>'.$nffin.'</nNFFin>'
    .'<xJust>'.$just.'</xJust>'
    .'</infInut>'
    .'</inutNFe>';

    //--
    //-- assinando
    $xmlassinado = assinaXML($xmlassinado, 'infInut' ,$_SESSION['CERT_PRIVKEY'],$_SESSION['CERT_X509']);
    $xmlassinado = str_replace(array('<?xml version="1.0"?>',"\r","\n"),'',$xmlassinado);
    $dadosMsg    = utf8_encode( str_replace('[xml]',$xmlassinado,$dadosMsg) );
    
    //--
    //-- enviando
    $resultado = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);
    //--
    return $resultado;
  }

  function consultaservico($tpAmb='2',$xUF='SP',$contingencia=false){
    $xml      = jsonnfe('status',$tpAmb,codigoUF($xUF),$contingencia);
    //--
    //-- ajustando o xml
    $cabecMsg = $xml['cabecMsg'];
    $cabecMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$cabecMsg);
    $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
    $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
    //--
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    //--
    //-- enviando
    $resultado = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);    
    //--
    return $resultado;
  }
  function baixarnsu($cnpj,$tpAmb='2',$xUF='SP',$contingencia=false){
    $xml      = jsonnfe('baixarnsu',$tpAmb,codigoUF($xUF),$contingencia);
    //--
    //-- ajustando o xml
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    //--
    $xmlDFe =
       '<distDFeInt xmlns="http://www.portalfiscal.inf.br/nfe" versao="'.$xml['versaoDados'].'">'
       .'<tpAmb>'.$tpAmb.'</tpAmb>'
       .'<cUFAutor>'.codigoUF($xUF).'</cUFAutor>'
       .'<CNPJ>'.$cnpj.'</CNPJ>'        
       .'<distNSU>'
       .'<ultNSU>000000000000000</ultNSU>'
       .'</distNSU>'          
      .'</distDFeInt>';
    //--
    //-- ajustando o xml
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    $dadosMsg = str_replace('[xml]'         ,$xmlDFe              ,$dadosMsg);
    //--
    //--
    //-- enviando
    $resultado = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],'',$dadosMsg,$xml['retorno']);
    //--
    return $resultado;
  }
  function baixarnfe($cnpj,$tpAmb='2',$xUF='SP',$chave_nsu,$contingencia=false){
    $xml      = jsonnfe('baixarnfe',$tpAmb,codigoUF($xUF),$contingencia);
    //--
    //-- ajustando o xml
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    //--
    $xmlDFe =
       '<distDFeInt xmlns="http://www.portalfiscal.inf.br/nfe" versao="'.$xml['versaoDados'].'">'
       .'<tpAmb>'.$tpAmb.'</tpAmb>'
       .'<cUFAutor>'.codigoUF($xUF).'</cUFAutor>'
       .'<CNPJ>'.$cnpj.'</CNPJ>';
    if (strlen($chave_nsu)==44){
       $xmlDFe  .= '<consChNFe>'
                .'<chNFe>'.$chave_nsu.'</chNFe>'
                .'</consChNFe>';
    } else {
       $xmlDFe  .= '<consNSU>'
                .'<NSU>'.$chave_nsu.'</NSU>'
                .'</consNSU>';
    }
    $xmlDFe .= '</distDFeInt>';
    //--
    //-- ajustando o xml
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    $dadosMsg = str_replace('[xml]'         ,$xmlDFe              ,$dadosMsg);
    //--
    //--
    //-- enviando
    $resultado = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],'',$dadosMsg,$xml['retorno']);
    //--
    return $resultado;
  }
  function consultacadastro($tpAmb,$cnpj,$xUF='SP',$contingencia=false){
    $xml      = jsonnfe('conscad',$tpAmb,codigoUF($xUF),$contingencia);
    //--
    //-- ajustando o xml
    $cabecMsg = $xml['cabecMsg'];
    $cabecMsg = str_replace('[cUF]'         ,codigoUF($xUF)        ,$cabecMsg);
    $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
    $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
    //--
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    $dadosMsg = str_replace('[UF]'          ,$xUF                 ,$dadosMsg);
    $dadosMsg = str_replace('[CNPJ]'        ,$cnpj                ,$dadosMsg);
    //--
    //-- enviando   
    $resultado = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);
    $listaErr=[];
    if( $resultado['erro']=='ok'):
      foreach( $resultado as $indice=>$value ):
        if( $indice != 'erro' )
         $listaErr[]=['STATUS'=>'OK','TAG'=>$indice,'ERRO'=>(string)$value];
      endforeach;
    else:
      $listaErr[]=['STATUS'=>'ERRO','TAG'=>'SOAP','ERRO'=>(string)$resultado['xMotivo']];
    endif;
    return $listaErr;
  }
  
  function cartacorrecao($tpAmb,$cnpjemitente,$xUF='SP',$contingencia=false,$seq,$chavenf,$motivo){
    $xml      = jsonnfe('carta',$tpAmb,codigoUF($xUF),$contingencia);
    //--
    //-- ajustando o xml
    $cabecMsg = $xml['cabecMsg'];
    $cabecMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$cabecMsg);
    $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
    $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
    //--
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    $dadosMsg = str_replace('[UF]'          ,$xUF                 ,$dadosMsg);
    $dadosMsg = str_replace('[CNPJ]'        ,$cnpjemitente        ,$dadosMsg);
    //--
    //-- monta e assina o xml
    $hoje = date('Y-m-d');
    $hora = strtotime('01:00:00');
    $xmlassinado =
           '<evento versao="1.00" xmlns="http://www.portalfiscal.inf.br/nfe">'
        .    '<infEvento Id="ID'.'110110'.$chavenf.str_pad($seq,2,'0',STR_PAD_LEFT).'">'
        .      '<cOrgao>'.codigoUF($xUF).'</cOrgao>'
        .      '<tpAmb>'.$tpAmb.'</tpAmb>'
        .      '<CNPJ>'.$cnpjemitente.'</CNPJ>'
        .      '<chNFe>'.$chavenf.'</chNFe>'
        .      '<dhEvento>'.date('Y-m-d').'T'.date('H:i:s',$hora).'-03:00</dhEvento>'
        .      '<tpEvento>110110</tpEvento>'
        .      '<nSeqEvento>'.$seq.'</nSeqEvento>'
        .      '<verEvento>1.00</verEvento>'
        .      '<detEvento versao="1.00">'
        .        '<descEvento>Carta de Correcao</descEvento>'
        .        '<xCorrecao>'.$motivo.'</xCorrecao>'
        .        '<xCondUso>'
        .          'A Carta de Correcao e disciplinada pelo paragrafo 1o-A do '
        .          'art. 7o do Convenio S/N, de 15 de dezembro de 1970 e '
        .          'pode ser utilizada para regularizacao de erro ocorrido na '
        .          'emissao de documento fiscal, desde que o erro nao esteja '
        .          'relacionado com: I - as variaveis que determinam o valor '
        .          'do imposto tais como: base de calculo, aliquota, diferenca '
        .          'de preco, quantidade, valor da operacao ou da prestacao; '
        .          'II - a correcao de dados cadastrais que implique mudanca '
        .          'do remetente ou do destinatario; III - a data de emissao ou '
        .          'de saida.'
        .        '</xCondUso>'
        .      '</detEvento>'
        .    '</infEvento>'
        .  '</evento>';
    //--
    //-- assinando
    $xmlassinado = assinaXML($xmlassinado, 'infEvento' ,$_SESSION['CERT_PRIVKEY'],$_SESSION['CERT_X509']);
    $xmlassinado = str_replace(array('<?xml version="1.0"?>',"\r","\n"),'',$xmlassinado);
    $xmlassinado = '<envEvento versao="1.00" xmlns="http://www.portalfiscal.inf.br/nfe">'.'<idLote>1</idLote>'.$xmlassinado.'</envEvento>';
    $dadosMsg    = utf8_encode( str_replace('[xml]',$xmlassinado,$dadosMsg) );
    
    //--
    //-- enviando
    $resultado = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);
    //--
    return $resultado;
  }

  function cancelanfe($nota){
    $nProt        = $nota->protNFe->infProt->nProt;
    $nota         = $nota->NFe;
    $tpAmb        = $nota->infNFe->ide->tpAmb;
    $cUF          = $nota->infNFe->ide->cUF;
    $contingencia = ($nota->infNFe->ide->tpemis==6);
    $chavenf      = str_replace('NFe','',$nota->infNFe['Id']);
    $cnpjemitente = $nota->infNFe->emit->CNPJ;
    $xml          = jsonnfe('cancela',$tpAmb,$cUF,$contingencia);
    //--
    //-- ajustando o xml
    $cabecMsg = $xml['cabecMsg'];
    $cabecMsg = str_replace('[cUF]'         ,$cUF                 ,$cabecMsg);
    $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
    $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
    //--
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,$cUF                 ,$dadosMsg);
    $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    $dadosMsg = str_replace('[UF]'          ,$cUF                 ,$dadosMsg);
    $dadosMsg = str_replace('[CNPJ]'        ,$cnpjemitente        ,$dadosMsg);
    //--
    //-- monta e assina o xml
    $hoje = date('Y-m-d');
    $hora = strtotime('01:00:00');
    $seq  = 1; // cancelamento, sequencia sempre é 1.
    $xmlassinado =
             '<evento versao="1.00" xmlns="http://www.portalfiscal.inf.br/nfe">'
        .      '<infEvento Id="ID'.'110111'.$chavenf.str_pad($seq,2,'0',STR_PAD_LEFT).'">'
        .        '<cOrgao>'.$cUF.'</cOrgao>'
        .        '<tpAmb>'.$tpAmb.'</tpAmb>'
        .        '<CNPJ>'.$cnpjemitente.'</CNPJ>'
        .        '<chNFe>'.$chavenf.'</chNFe>'
        .        '<dhEvento>'.date('Y-m-d').'T'.date('H:i:s',$hora).'-03:00</dhEvento>'
        .        '<tpEvento>110111</tpEvento>'
        .        '<nSeqEvento>'.$seq.'</nSeqEvento>'
        .        '<verEvento>1.00</verEvento>'
        .        '<detEvento versao="1.00">'
        .          '<descEvento>Cancelamento</descEvento>'
        .          '<nProt>'.$nProt.'</nProt>'    
        .          '<xJust>Cancelamento da NFe</xJust>'
        .        '</detEvento>'
        .      '</infEvento>'
        .    '</evento>';
    //--
    //-- assinando
    $xmlassinado = assinaXML($xmlassinado, 'infEvento' ,$_SESSION['CERT_PRIVKEY'],$_SESSION['CERT_X509']);
    $xmlassinado = str_replace(array('<?xml version="1.0"?>',"\r","\n"),'',$xmlassinado);
    $xmlassinado = '<envEvento versao="1.00" xmlns="http://www.portalfiscal.inf.br/nfe">'.'<idLote>1</idLote>'.$xmlassinado.'</envEvento>';
    $dadosMsg    = utf8_encode( str_replace('[xml]',$xmlassinado,$dadosMsg) );
    
    //--
    //-- enviando
    $resultado = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);
    if ($resultado['cStat']!='128'){
      $resultado['erro']='erro';
    }
    //--
    $resultado = ['STATUS'=>$resultado['erro'],'TAG'=>$resultado['cStat'],'ERRO'=>$resultado['xMotivo']];
    return $resultado;
  }
  
  function envianfe($tpAmb,$xUF='SP',$nota,$contingencia=false){
    $xml      = jsonnfe('nfeenvio',$tpAmb,codigoUF($xUF),$contingencia);
    //--
    //-- ajustando o xml
    $cabecMsg = $xml['cabecMsg'];
    $cabecMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$cabecMsg);
    $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
    $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
    //--
    $dadosMsg = $xml['dadosMsg'];
    $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
    $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
    $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
    $dadosMsg = str_replace('[UF]'          ,$xUF                 ,$dadosMsg);
    //--
    //-- pega o xml da nota
    $xmlassinado = $nota;
    //--
    //-- assinando
    $xmlassinado = assinaXML($xmlassinado, 'infNFe' ,$_SESSION['CERT_PRIVKEY'],$_SESSION['CERT_X509']);
    $xmlassinado = str_replace(array('<?xml version="1.0"?>',"\r","\n","\0"),'',$xmlassinado);
    $xmlassinado = '<enviNFe versao="'.$xml['versaoDados'].'" xmlns="http://www.portalfiscal.inf.br/nfe">'.'<idLote>1</idLote><indSinc>0</indSinc>'.$xmlassinado.'</enviNFe>';
    $dadosMsg    = utf8_encode( str_replace('[xml]',$xmlassinado,$dadosMsg) );

    //--
    //-- enviando
    $envio = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);
    if ($envio['cStat']=='103'){
      sleep(5);
      $xml      = jsonnfe('nferetorno',$tpAmb,codigoUF($xUF),$contingencia);
      $xmlconsulta =
         '<consReciNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="'.$xml['versaoDados'].'">'
        .'<tpAmb>'.$tpAmb.'</tpAmb>'
        .'<nRec>'.$envio['nRec'].'</nRec>'
        .'</consReciNFe>';
      //--
      //-- ajustando o xml
      $cabecMsg = $xml['cabecMsg'];
      $cabecMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$cabecMsg);
      $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
      $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
      //--
      $dadosMsg = $xml['dadosMsg'];
      $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
      $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
      $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
      $dadosMsg = str_replace('[UF]'          ,$xUF                 ,$dadosMsg);
      $dadosMsg = utf8_encode( str_replace('[xml]',$xmlconsulta,$dadosMsg) );
      //--
      $tentativa=0;
      do{
        $retorno  = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);
        if ($retorno['cStat']=='104'){
          $infProt = $retorno['infProt'];

          // emitida com sucesso
          if ($infProt['cStat']=='100'){
            $retorno = $retorno['infProt'];
            break;
            
          // chave duplicada, busca os dados pelo recibo retornado
          } elseif ($infProt['cStat']=='204'){
            $infProt      = $retorno['infProt'];
            $nRec         = $infProt['xMotivo'];
            $nRec         = substr($nRec,strpos($nRec,'[nRec')+6,15);
            $xml          = jsonnfe('nferetorno',$tpAmb,codigoUF($xUF),$contingencia);
            $xmlconsulta  =
              '<consReciNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="'.$xml['versaoDados'].'">'
              .'<tpAmb>'.$tpAmb.'</tpAmb>'
              .'<nRec>'.$nRec.'</nRec>'
              .'</consReciNFe>';
            //--
            //-- ajustando o xml
            $cabecMsg = $xml['cabecMsg'];
            $cabecMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$cabecMsg);
            $cabecMsg = str_replace('[versaoDados]' ,$xml['versaoDados']  ,$cabecMsg);
            $cabecMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$cabecMsg);
            //--
            $dadosMsg = $xml['dadosMsg'];
            $dadosMsg = str_replace('[cUF]'         ,codigoUF($xUF)       ,$dadosMsg);
            $dadosMsg = str_replace('[tpAmb]'       ,$tpAmb               ,$dadosMsg);
            $dadosMsg = str_replace('[servico]'     ,$xml['nomeServico']  ,$dadosMsg);
            $dadosMsg = str_replace('[UF]'          ,$xUF                 ,$dadosMsg);
            $dadosMsg = utf8_encode( str_replace('[xml]',$xmlconsulta,$dadosMsg) );
            //--
            $retorno  = enviarequisicao($xml['nomeServico'],$xml['metodo'],$xml['url'],$cabecMsg,$dadosMsg,$xml['retorno']);
            break;
          // outra situacao
          } else {
            $retorno = $retorno['infProt'];
            break;
          }
          // aguardando lote em processamento
        } elseif ($retorno['cStat']=='105'){
          $tentativa++;
          if($tentativa>4){
            break;
          sleep(5);
          }
          continue;
        } else 
          break;
      } while (true);
    }
    //--
    $resultado['envio'] = $envio;
    if (isset($retorno))
      $resultado['retorno'] = $retorno;
    return $resultado;
  }
//**********************************//  
//** CLASSE DE IMPRESSAO DO DANFE **//
//**********************************//  
class DANFE extends FPDF7 {

  /* inicio das funções de código de barras */
  function GerarBarras($parStr){
    $barcodes[0] = '00110' ;
    $barcodes[1] = '10001' ;
    $barcodes[2] = '01001' ;
    $barcodes[3] = '11000' ;
    $barcodes[4] = '00101' ;
    $barcodes[5] = '10100' ;
    $barcodes[6] = '01100' ;
    $barcodes[7] = '00011' ;
    $barcodes[8] = '10010' ;
    $barcodes[9] = '01010' ;
    $retorno     = '0000';
    $html        = '';
    $cont=0;
    $parte[1]='';
    $parte[2]='';
    /*
    * Exemplo gerar codigo barras codigo 4853
    * 4=00101
    * 8=10010
    * 5=10100
    * 3=11000
    *
    * Juntar de 2 em 2 (48) (Primeiro+Primeiro,segundo+segundo...)
    * 00101
    * 10010 = 0100100110  
    */
    while( $cont<strlen($parStr)){
      $parte[1]=$barcodes[intval($parStr[$cont])];
      $cont++;
      $parte[2]=$barcodes[intval($parStr[$cont])];   
      $cont++;
      $retorno.= $parte[1][0].$parte[2][0]
                .$parte[1][1].$parte[2][1]
                .$parte[1][2].$parte[2][2]
                .$parte[1][3].$parte[2][3]
                .$parte[1][4].$parte[2][4];    
    }
    $retorno.= '100';
    $cont    = 0;
    $cor     = 'white';
    $largura = '';
    /*
    * Todo codigo numerico é definido com 5 barras(Hexa)
    * 0 = barra estreita
    * 1 = barra larga
    */
    return $retorno;
  }
  /* fim das funções de código de barras */

  function escreve($x,$y,$texto,$w,$h,$borda=0,$alinha='E'){
    $this->SetXY($x,$y);
    $this->Cell($w,$h, utf8_decode($texto),$borda,0,$alinha);
  }
  
  function label($x,$y,$texto,$w,$h,$borda=0,$alinha='E'){
    $this->SetFont('Courier','B',7);
    $this->SetFillColor(216,216,191);    
    $this->SetXY($x,$y-1);
    $this->Cell($w,$h, utf8_decode(strtoupper($texto)),$borda,0,$alinha);
    $this->SetTextColor(0,0,0);
    $this->SetFont('Courier','',9);
  }
  
  function quadroprodutos($altura,$y){
    $ganha = 0;
    // se for a primeira página, verifica se ná como ganhar espaço com os quadros
    // de endereço de entrega e faturas porque nas demais páginas não tem endereço
    // de entrega e quadro de faturas
    if ($this->PageNo()==1){
      if (!isset($this->cobr->dup)) // se nao tiver duplicata (ganha 3 linhas)
        //$ganha += 14;
        $ganha += -2;
      if (!isset($this->entrega->CNPJ)) // se tiver endereço de entrega (ganha 4 linhas)
        $ganha += 16;
      $y -= $ganha;
    }
    //
    $this->aqp            = $altura+$ganha;    // Altura do Quadro de Produto
    $this->c              = 3;
    $this->larg_codigo    = 20;
    $this->larg_descricao = 70;
    $this->larg_ncm       = 15;
    $this->larg_cst       = 6;
    $this->larg_cfop      = 8;
    $this->larg_und       = 6;
    $this->larg_qtd       = 7;
    $this->larg_vunit     = 14;
    $this->larg_vtotal    = 14;
    $this->larg_vicms     = 11;
    $this->larg_vipi      = 11;
    $this->larg_picms     = 11;
    $this->larg_pipi      = 11;
    $y += 8;
    $this->label(3,$y+=1,'DADOS DOS PRODUTOS / SERVIÇOS',77,4);
    $this->Rect(3,$y+=3,204,$this->aqp);
    $this->Rect($this->c ,$y,$this->larg_codigo,4);
    $this->label($this->c,$y+1,'CODIGO',$this->larg_codigo,4);
    $this->c += $this->larg_codigo;
    $this->Rect($this->c ,$y,$this->larg_descricao,4);
    $this->Rect($this->c ,$y,$this->larg_descricao,$this->aqp);
    $this->label($this->c,$y+1,'DESCRICAO DO PRODUO / SERVICO',$this->larg_descricao,4);
    $this->c += $this->larg_descricao;
    $this->Rect($this->c ,$y,$this->larg_ncm,4);
    $this->label($this->c,$y+1,'NCM/SH',$this->larg_ncm,4);
    $this->c += $this->larg_ncm;
    $this->Rect($this->c ,$y,$this->larg_cst,4);
    $this->label($this->c,$y+1,'CST',$this->larg_cst,4);
    $this->Rect($this->c ,$y,$this->larg_cst,$this->aqp);
    $this->c += $this->larg_cst;
    $this->Rect($this->c ,$y,$this->larg_cfop,4);
    $this->label($this->c,$y+1,'CFOP',$this->larg_cfop,4);
    $this->c += $this->larg_cfop;
    $this->Rect($this->c ,$y,$this->larg_und,4);
    $this->label($this->c,$y+1,'UND',$this->larg_und,4);
    $this->Rect($this->c ,$y,$this->larg_und,$this->aqp);
    $this->c += $this->larg_und;
    $this->Rect($this->c ,$y,$this->larg_qtd,4);
    $this->label($this->c,$y+1,'QTD',$this->larg_qtd,4);
    $this->c += $this->larg_qtd;
    $this->Rect($this->c ,$y,$this->larg_vunit,4);
    $this->label($this->c,$y+1,'V.UNIT.',$this->larg_vunit,4);
    $this->Rect($this->c ,$y,$this->larg_vunit,$this->aqp);
    $this->c += $this->larg_vunit;
    $this->Rect($this->c ,$y,$this->larg_vtotal,4);
    $this->label($this->c,$y+1,'V.TOTAL',$this->larg_vtotal,4);
    $this->c += $this->larg_vtotal;
    $this->Rect($this->c ,$y,$this->larg_vicms,4);
    $this->label($this->c,$y+1,'V.ICMS',$this->larg_vicms,4);
    $this->Rect($this->c ,$y,$this->larg_vicms,$this->aqp);
    $this->c += $this->larg_vicms;
    $this->Rect($this->c ,$y,$this->larg_vipi,4);
    $this->label($this->c,$y+1,'V.IPI',$this->larg_vipi,4);
    $this->c += $this->larg_vipi;
    $this->Rect($this->c ,$y,$this->larg_picms,4);
    $this->label($this->c,$y+1,'%ICMS',$this->larg_picms,4);
    $this->Rect($this->c ,$y,$this->larg_picms,$this->aqp);
    $this->c += $this->larg_picms;
    $this->Rect($this->c ,$y,$this->larg_pipi,4);
    $this->label($this->c,$y+1,'%IPI',$this->larg_pipi,4);
    $this->c += $this->larg_pipi;
    $y+=3;
    $this->SetFont('Courier','B',6);
    $this->lin = $y;
    return;
  }

  function Header() {
    if (!isset($this->emit->xNome))
      return;
    
    // quadro "recibo de recebimento"
    $y = 6;
    $this->SetFont('Courier','I',9);
    $this->Rect(3,3,204,22);
    $this->MultiCell(170,4,utf8_decode('Recebemos de '.$this->emit->xNome.' os produtos/serviços constantes da NFe indicada ao lado.'),0);
    $this->Rect(174,3,33,22);
    
    // quadro "NF-e"
    $this->SetFont('Courier','B',10);
    $this->escreve(175,$y+=2,'NF-e',30,4,0,'C');
    $this->escreve(175,$y+=4,number_format( (string)$this->ide->nNF,0,'','.'),30,4,0,'C');
    $this->escreve(175,$y+=4,'Série: '.$this->ide->serie,30,4,0,'C');
    $this->SetFont('Courier','',9);
    
    // quadro "Data de recebimento"
    $this->Rect(3,15,40,10);
    $this->label(3,$y,'Data de recebimento',30,4);
    $this->Rect(43,15,131,10);
    $this->label(43,$y,'IDENTIFICACAO E ASSINATURA DO RECEBEDOR',30,4);
    
    // picote do recibo
    $y+=12;
    $x=3;
    for($x=3;$x<=207;$x+=3){
      if (($x % 9)!==0){
        $this->Line($x, $y, ($x+3), $y);
      }
    }
    
    // quadro do emitente
    $y+=3;
    $o=$y;
    $this->Rect(3,$y,204,30);
    $y+=1;
    $this->label(3,$y,'IDENTIFICAÇÃO DO EMITENTE',30,4);
    $y+=3;
    if (!file_exists($this->logo)){
      $this->SetFont('Courier','B',16);
      $this->escreve(5,$y+=3,substr($this->emit->xNome,0,23),80,4,0,'L');
      $this->SetFont('Courier','',8);
      $xCpl   = $this->emit->enderEmit->xLgr.', '.$this->emit->enderEmit->nro.($this->emit->enderEmit->cpl!=''?$this->emit->enderEmit->cpl:'');
      $xCpl2  = '';
      if (strlen($xCpl)>41){
        $xCpl2  = trim(substr($xCpl,41,strlen($xCpl)));
        $xCpl   = substr($xCpl,0,40);
      }
      $this->escreve(5,$y+=4,$xCpl,80,4,0,'L');
      $this->escreve(5,$y+=3,($xCpl2!=''?$xCpl2.' - ':'').$this->emit->enderEmit->xBairro,80,4,0,'L');
      $this->escreve(5,$y+=3,$this->emit->enderEmit->xMun.' - '.$this->emit->enderEmit->UF.' - '.$this->emit->enderEmit->CEP,80,4,0,'L');
      $this->escreve(5,$y+=3,'Telefone: '.$this->emit->enderEmit->fone,80,4,0,'L');
    } else {
      $this->Image($this->logo,4,($y-3),80,28);
    }
    
    // quadro "danfe"
    $y = $o;
    $this->Rect(85,$y,45,30);
    $this->SetFont('Courier','B',16);
    $this->escreve(85,$y+=3,'NF-e',45,4,0,'C');
    $this->SetFont('Courier','',8);
    $this->escreve(87,$y+=5,'Documento  auxiliar  da',45,4,0,'L');
    $this->escreve(87,$y+=3,'Nota fiscal  eletrônica',45,4,0,'L');
    $y+=1;
    $this->escreve(90,$y+=3,'0 - ENTRADA',15,4,0,'L');
    $this->escreve(90,$y+=3,'1 - SAIDA',15,4,0,'L');
    $this->Rect(115,$y-2,5,5);
    $this->escreve(116,$y-1,$this->ide->tpNF,15,4,0,'L');
    $y+=1;
    $this->SetFont('Courier','B',8);
    $this->escreve(85,$y+=3,'NF. '.number_format((string)$this->ide->nNF,0,'','.'),45,4,0,'C');
    $this->escreve(87,$y+=3,'Série '.$this->ide->serie.' - Folha '.$this->PageNo().'/{nb}',45,4,0,'C');
    $this->SetFont('Courier','',8);
    
    // quadro "codigo de barras"
    $y = $o;
    $this->Rect(130,$y,77,13);
    //----------------
    $col          = 132;
    $lin          = $y+1;
    $ab           = 11;// altura da barra
    $barras       = $this->GerarBarras( str_replace('NFe','',$this->infNFe['Id']) );
    $cor          = 'white';
    $traco_preto  = 0.18; // 0.2;
    $traco_branco = 0.58; // 0.6

    $this->setLineWidth($traco_preto);
    for($cont=0;$cont<strlen($barras);$cont++):
      $cor     = ( $cor           == 'white'   ? 'black'      : 'white');
      $largura = ( $barras[$cont] == '0'       ? $traco_preto : $traco_branco);
      if( $cor=='white' ){
        $this->SetDrawColor(255,255,255);
        if( $largura==$traco_preto ){          
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
        }
        else{
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
        }
      }

      if( $cor=='black' ){
        $this->SetDrawColor(0,0,0);
        if( $largura==$traco_preto ){          
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
        }
        else{
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
          $this->Line($col,$lin,$col,($lin+$ab));
          $col+=$traco_preto;
        }
      }
    endfor;
    $this->SetDrawColor(0,0,0);
    //----------------    
    $this->Rect(130,$y+=13,77,7);
    $this->label(130,$y+=1,'CHAVE DE ACESSO',30,4);
    $this->SetFont('Courier','',8);
    $this->escreve(130,$y+=2,str_replace('NFe','',$this->infNFe['Id']),77,4,0,'C');
    $this->escreve(130,$y+=4,'Consulta de autenticidade no portal nacional',77,4,0,'C');
    $this->escreve(130,$y+=3,'da NF-e www.nfe.fazenda.gov.br/portal ou',77,4,0,'C');
    $this->escreve(130,$y+=3,'no site da Sefaz Autorizadora',77,4,0,'C');
    
    // quadro "natureza de operação"
    $y+=4;
    $o=$y;
    $this->Rect(3,$y,127,9);
    $this->label(3,$y+=1,'NATUREZA DA OPERAÇÃO',120,4);
    $this->escreve(5,$y+=2,$this->ide->natOp,120,4,0,'L');
    
    // quadro "protocolo de autorização"
    $y=$o;
    $this->Rect(130,$y,77,9);
    $this->label(130,$y+=1,'PROTOCOLO DE AUTORIZAÇÃO DE USO',77,4);
    if ($this->protNFe==null)
      $this->escreve(132,$y+=2,'SEM AUTORIZAÇÃO DA SEFAZ',77,4,0,'L');
    else {
      $d = str_replace('T',' ',$this->protNFe->dhRecbto);
      $d = strtotime($d);
      $this->escreve(132,$y+=2,$this->protNFe->nProt.' - '.date('d/m/Y',$d).' - '.date('H:i',$d),77,4,0,'L');
    }
    
    // quadro "inscricao estadual"
    $y+=6;
    $o=$y;
    $this->Rect(3,$y,80,9);
    $this->label(3,$y+=1,'INSCRIÇÃO ESTADUAL',77,4);
    $this->escreve(5,$y+=2,$this->emit->IE,77,4,0,'L');
    $y=$o;
    $this->Rect(83,$y,80,9);
    $this->label(83,$y+=1,'INSCRIÇÃO ESTADUAL DO SUBST. TRIBUTÁRIO',80,4);
    $this->escreve(85,$y+=2,$this->emit->IEST,80,4,0,'L');
    $y=$o;
    $this->Rect(163,$y,44,9);
    $this->label(163,$y+=1,'CNPJ',44,4);
    $this->escreve(165,$y+=2,$this->emit->CNPJ,44,4,0,'L');
    
    // se não tiver autorização imprime mensagem e se for homologação também
    if ($this->protNFe==null){
      $this->SetTextColor(255,0,0);
      $this->SetFont('Courier','B',22);
      $this->escreve(3,$y+2,'NFe SEM AUTORIZAÇÃO DA SEFAZ',0,4,0,'C');
      $this->SetTextColor(0,0,0);
    }
    if ($this->ide->tpAmb==2){
      $this->SetTextColor(255,0,0);
      $this->SetFont('Courier','B',22);
      $this->escreve(3,$y+12,'NFe EMITIDA EM AMBIENTE DE HOMOLOGAÇÃO',0,4,0,'C');
      $this->SetTextColor(0,0,0);
    }

    // quardro de produtos (só os traços)
    if ($this->PageNo()==1){
      $this->lin = $this->quadroprodutos(73,178);
      $this->lin = ($this->lin + 10);
    } else {
      $this->lin = $this->quadroprodutos(200,72);
      $this->lin = 86;
      $this->SetAutoPageBreak(true,13);
    }
    //
  }

  function Footer() {
    $y = 265;
    if ($this->PageNo()==1){
      $this->label(3,$y+1,'INFORMAÇÕES COMPLEMENTARES',130,4);
      $this->Rect(3,$y+4,130,20);
      $this->SetXY(3,$y+4);
      $this->SetFont('Courier','',6);
      if ($this->obs!=null){
        $obs = utf8_decode($this->obs);
        $obs = str_replace(array("|"),"\r\n",$obs);
        $this->MultiCell(130, 2.5, $obs, 0, 'L');
      }
      $this->label(133,$y+1,'RESERVADO AO FISCO',71,4);
      $this->Rect(133,$y+4,74,20);
    }
    $this->SetFont('Courier','I',8);
    $this->label(3,$y+25,'DATA / HORA DO IMPRESSO: '.date('d/m/Y H:i'). ' - DESENVOLVIDO POR WS SISTEMAS',71,4);
  }
}

function geraDANFE($nota,$danfearquivo="",$logo=""){
  // inicializando o objeto
  $pdf = new DANFE('P','mm','A4'); //portrait, em mm, papel A4
  $pdf->logo = $logo;

  // abrindo as variavis comuns da pagina (header / footer)
  if (isset($nota->NFe))
    $pdf->infNFe  = $nota->NFe->infNFe;
  else {
    if (!isset($nota->infNFe)){
      $pdf->AddPage();
      $pdf->cell(10,10,'XML NFe INVALIDO, VERIFIQUE');
      $pdf->Output();
      exit;
    }
    $pdf->infNFe  = $nota->infNFe;
  }
  
  $pdf->ide     = $pdf->infNFe->ide;
  $pdf->emit    = $pdf->infNFe->emit;
  $pdf->dest    = $pdf->infNFe->dest;
  if( isset($pdf->infNFe->entrega))
    $pdf->entrega = $pdf->infNFe->entrega;
  if( isset($pdf->infNFe->cobr))  
    $pdf->cobr    = $pdf->infNFe->cobr;
  $pdf->total   = $pdf->infNFe->total->ICMSTot;
  if( isset($pdf->infNFe->transp))  
    $pdf->transp  = $pdf->infNFe->transp;
  $pdf->itens   = $pdf->infNFe->det;
  $pdf->obs     = isset($pdf->infNFe->infAdic->infCpl)?(string)$pdf->infNFe->infAdic->infCpl:'';
  $pdf->protNFe = (isset($nota->protNFe)?$nota->protNFe->infProt:null);

  // pagina
  $pdf->SetMargins(3, 6);
  $pdf->AliasNbPages();

  // abrindo uma página
  $pdf->SetAutoPageBreak(true,33);
  $pdf->AddPage();
  $pdf->SetFont("Arial",'', 12); //Define a fonte a ser utilizada
  $pdf->SetAuthor("Autor: WS SISTEMAS / DANFE"); 

  // quadro "destinatário / remetente"
  $y = 80;
  $pdf->label(3,$y+=1,'DESTINATÁRIO / REMETENTE',77,4);
  $pdf->Rect(3,$y+=3,130,9);
  $pdf->label(3,$y+=1,'NOME / RAZÃO SOCIAL',77,4);
  $pdf->escreve(5,$y+=2,$pdf->dest->xNome,130,4,0,'L');
  $y = 81;
  $pdf->Rect(133,$y+=3,40,9);
  $pdf->label(133,$y+=1,'CNPJ / CPF',40,4);
  $pdf->escreve(135,$y+=2,(isset($pdf->dest->CNPJ)?$pdf->dest->CNPJ:$pdf->dest->CPF),40,4,0,'L');
  $y = 81;
  $pdf->Rect(173,$y+=3,34,9);
  $pdf->label(173,$y+=1,'DATA DE EMISSÃO',30,4);
  $d = str_replace('T',' ',$pdf->ide->dhEmi);
  $d = strtotime($d);
  $pdf->escreve(175,$y+=2,date('d/m/Y',$d),30,4,0,'C');
  $y+=3;
  $o=$y;
  $pdf->Rect(3,$y+=3,100,9);
  $pdf->label(3,$y+=1,'ENDEREÇO',100,4);
  $pdf->escreve(5,$y+=2,$pdf->dest->enderDest->xLgr.', '.$pdf->dest->enderDest->nro.(isset($pdf->dest->enderDest->cpl)?$pdf->dest->enderDest->cpl:''),100,4,0,'L');
  $y=$o;
  $pdf->Rect(103,$y+=3,40,9);
  $pdf->label(103,$y+=1,'BAIRRO',40,4);
  $pdf->escreve(105,$y+=2,$pdf->dest->enderDest->xBairro,40,4,0,'L');
  $y=$o;
  $pdf->Rect(143,$y+=3,30,9);
  $pdf->label(143,$y+=1,'CEP',30,4);
  $pdf->escreve(145,$y+=2,$pdf->dest->enderDest->CEP,30,4,0,'L');
  $y=$o;
  $pdf->Rect(173,$y+=3,34,9);
  $pdf->label(173,$y+=1,'DATA DE SAIDA',30,4);
  $y+=5;
  $o=$y;
  $pdf->Rect(3,$y+=3,90,9);
  $pdf->label(3,$y+=1,'MUNICIPIO',90,4);
  $pdf->escreve(5,$y+=2,$pdf->dest->enderDest->xMun,90,4,0,'L');
  $y=$o;
  $pdf->Rect(93,$y+=3,35,9);
  $pdf->label(93,$y+=1,'FONE / FAX',35,4);
  $pdf->escreve(95,$y+=2,$pdf->dest->enderDest->fone,35,4,0,'L');
  $y=$o;
  $pdf->Rect(128,$y+=3,10,9);
  $pdf->label(128,$y+=1,'UF',10,4);
  $pdf->escreve(130,$y+=2,$pdf->dest->enderDest->UF,10,4,0,'L');
  $y=$o;
  $pdf->Rect(138,$y+=3,35,9);
  $pdf->label(138,$y+=1,'INSCRIÇÃO ESTADUAL',35,4);
  $pdf->escreve(140,$y+=2,$pdf->dest->IE,35,4,0,'L');
  $y=$o;
  $pdf->Rect(173,$y+=3,34,9);
  $pdf->label(173,$y+=1,'HORA DE SAIDA',30,4);

  // quadro "faturas ou duplicatas"
  $max = 0;
  if (isset($pdf->cobr->dup)){
    $y += 10;
    $pdf->label(3,$y+=1,'FATURAS OU DUPLICATAS',77,4);
    $pdf->Rect(3,$y+=3,204,10);
    $dup_lin = $y+1;
    $dup_col = 3;
    foreach($pdf->cobr->dup as $dup){
      $max++;
      if ($max>12)
        break;
      $pdf->label($dup_col+=1,$dup_lin,$dup->nDup,15,4);
      $pdf->label($dup_col+15,$dup_lin,date('d/m/y',strtotime($dup->dVenc)),15,4);
      $pdf->label($dup_col+30,$dup_lin,number_format((string)$dup->vDup,2,',','.'),20,4,0,'R');
      $pdf->Line($dup_col+50, $dup_lin-1, $dup_col+50, $dup_lin+3);
      $dup_col += 50;
      if ($dup_col>200){
        $dup_col = 3;
        $dup_lin += 3;
      }
    }
  }

  // quadro "local de entrega"
  if (isset($pdf->entrega->CNPJ)){
    $y += 12;
    $pdf->label(3,$y+=1,'LOCAL DE ENTREGA',77,4);
    $pdf->Rect(3,$y+=3,204,10);
    $pdf->label(3,$y,'CNPJ/CPF',77,4);
    $pdf->escreve(3,$y+2,$pdf->entrega->CNPJ,20,4,0,'L');
    $pdf->label(40,$y,'ENDERECO',20,4);
    $pdf->Ln(3);
    $pdf->Cell(38,3,"",0);
    $pdf->MultiCell(160,3,utf8_decode($pdf->entrega->xLgr.", ".$pdf->entrega->nro.(isset($pdf->entrega->xCpl)?" - ".$pdf->entrega->xCpl:"")." - ".$pdf->entrega->xBairro." - ".$pdf->entrega->xMun."/".$pdf->entrega->UF),0);
  }

  // quadro "fatura"
  $y += 12;
  $pdf->label(3,$y+=1,'FATURA',77,4);
  $pdf->Rect(3,$y+=3,204,12);
  $pdf->Rect(3,$y,40,6);
  $pdf->label(3,$y,'PAGAMENTO',77,4);
  $pdf->escreve(3,$y+2,'DADOS DA FATURA',40,4,0,'R');
  $pdf->Rect(43,$y,40,6);
  $pdf->label(43,$y,'NUMERO',77,4);
  $pdf->escreve(43,$y+2,str_pad($pdf->ide->nNF,6,"0",STR_PAD_LEFT),40,4,0,'L');
  $pdf->Rect(83,$y,40,6);
  $pdf->label(83,$y,'VALOR ORIGINAL',77,4);
  $pdf->escreve(83,$y+2,number_format((string)$pdf->total->vNF+(string)$pdf->total->vDesc,2,',','.'),40,4,0,'R');
  $pdf->Rect(123,$y,40,6);
  $pdf->label(123,$y,'VALOR DESCONTO',77,4);
  $pdf->escreve(123,$y+2,number_format((string)$pdf->total->vDesc,2,',','.'),40,4,0,'R');
  $pdf->Rect(163,$y,44,6);
  $pdf->label(163,$y,'VALOR LIQUIDO',77,4);
  $pdf->escreve(163,$y+2,number_format((string)$pdf->total->vNF,2,',','.'),44,4,0,'R');

  // quadro "calculo do imposto"
  $y += 12;
  $pdf->label(3,$y+=1,'CALCULO DO IMPOSTO',77,4);
  $pdf->Rect(3,$y+=3,204,12);
  $pdf->Rect(3,$y,40,6);
  $pdf->label(3,$y,'BASE DE CÁLC. ICMS',77,4);
  $pdf->escreve(3,$y+2,number_format((string)$pdf->total->vBC,2,',','.'),40,4,0,'R');
  $pdf->Rect(43,$y,40,6);
  $pdf->label(43,$y,'VALOR DO ICMS',77,4);
  $pdf->escreve(43,$y+2,number_format((string)$pdf->total->vICMS,2,',','.'),40,4,0,'R');
  $pdf->Rect(83,$y,40,6);
  $pdf->label(83,$y,'BASE DE CÁLC. ICMS SUBST',77,4);
  $pdf->escreve(83,$y+2,number_format((string)$pdf->total->vBCST,2,',','.'),40,4,0,'R');
  $pdf->Rect(123,$y,40,6);
  $pdf->label(123,$y,'VALOR ICMS SUBST.',77,4);
  $pdf->escreve(123,$y+2,number_format((string)$pdf->total->vST,2,',','.'),40,4,0,'R');
  $pdf->Rect(163,$y,44,6);
  $pdf->label(163,$y,'VALOR TOTAL DOS PRODUTOS',77,4);
  $pdf->escreve(163,$y+2,number_format((string)$pdf->total->vProd,2,',','.'),44,4,0,'R');
  //
  $y+=6;
  $pdf->Rect(3,$y,31,6);
  $pdf->label(3,$y,'VALOR DO FRETE',77,4);
  $pdf->escreve(3,$y+2,number_format((string)$pdf->total->vFrete,2,',','.'),31,4,0,'R');
  $pdf->Rect(34,$y,31,6);
  $pdf->label(34,$y,'VALOR DO SEGURO',77,4);
  $pdf->escreve(34,$y+2,number_format((string)$pdf->total->vSeg,2,',','.'),31,4,0,'R');
  $pdf->Rect(65,$y,31,6);
  $pdf->label(65,$y,'DESCONTO',77,4);
  $pdf->escreve(65,$y+2,number_format((string)$pdf->total->vDesc,2,',','.'),31,4,0,'R');
  $pdf->Rect(96,$y,35,6);
  $pdf->label(96,$y,'OUTRAS DESP ACESSÓRIAS',77,4);
  $pdf->escreve(96,$y+2,number_format((string)$pdf->total->vOutro,2,',','.'),35,4,0,'R');
  $pdf->Rect(131,$y,32,6);
  $pdf->label(131,$y,'VALOR DO IPI',77,4);
  $pdf->escreve(131,$y+2,number_format((string)$pdf->total->vIPI,2,',','.'),32,4,0,'R');
  $pdf->Rect(163,$y,44,6);
  $pdf->label(163,$y,'VALOR TOTAL DA NOTA FISCAL',77,4);
  $pdf->escreve(163,$y+2,number_format((string)$pdf->total->vNF,2,',','.'),44,4,0,'R');

  // quadro "transportadora"
  $y += 8;
  $pdf->label(3,$y+=1,'TRANSPORTADORA / VOLUMES TRANSPORTADOS',77,4);
  $pdf->Rect(3,$y+=3,204,12);
  $pdf->Rect(3,$y,80,6);
  $pdf->label(3,$y,'NOME / RAZÃO SOCIAL',80,4);
  $pdf->escreve(3,$y+2,(isset($pdf->transp->transporta->xNome)?$pdf->transp->transporta->xNome:''),80,4);
  $pdf->Rect(83,$y,33,6);
  $pdf->label(83,$y,'FRETE POR CONTA',33,4);
  $pdf->escreve(83,$y+2,$pdf->transp->modFrete.' - '.($pdf->transp->modFrete!='0'?'DESTINATARIO':'EMITENTE'),32,4);
  $pdf->Rect(116,$y,20,6);
  $pdf->label(116,$y,'CODIGO ANTT',20,4);
  $pdf->escreve(116,$y+2,'',80,4);
  $pdf->Rect(136,$y,30,6);
  $pdf->label(136,$y,'PLACA DO VEICULO',30,4);
  $pdf->escreve(136,$y+2,'',30,4);
  $pdf->Rect(166,$y,6,6);
  $pdf->label(166,$y,'UF',6,4);
  $pdf->escreve(166,$y+2,(isset($pdf->transp->transporta->UF)?$pdf->transp->transporta->UF:''),6,4);
  $pdf->Rect(172,$y,35,6);
  $pdf->label(172,$y,'CNPJ/CPF',6,4);
  if (isset($pdf->transp->transporta->CNPJ))
    $pdf->escreve(172,$y+2,$pdf->transp->transporta->CNPJ,32,4);
  elseif (isset($pdf->transp->transporta->CPF))
    $pdf->escreve(172,$y+2,$pdf->transp->transporta->CPF,32,4);
  $y+=6;
  $pdf->Rect(3,$y,80,6);
  $pdf->label(3,$y,'ENDERECO',80,4);
  $pdf->escreve(3,$y+2,(isset($pdf->transp->transporta->xEnder)?$pdf->transp->transporta->xEnder:''),80,4);
  $pdf->Rect(83,$y,83,6);
  $pdf->label(83,$y,'MUNICIPIO',80,4);
  $pdf->escreve(83,$y+2,(isset($pdf->transp->transporta->xMun)?$pdf->transp->transporta->xMun:''),80,4);
  $pdf->Rect(166,$y,6,6);
  $pdf->label(166,$y,'UF',6,4);
  $pdf->escreve(166,$y+2,(isset($pdf->transp->transporta->UF)?$pdf->transp->transporta->UF:''),6,4);
  $pdf->Rect(172,$y,35,6);
  $pdf->label(172,$y,'INSCRIÇÃO ESTADUAL',6,4);
  if (isset($pdf->transp->transporta->IE))
    $pdf->escreve(172,$y+2,$pdf->transp->transporta->IE,32,4);
  $y+=6;
  $pdf->Rect(3,$y,20,6);
  $pdf->label(3,$y,'QUANTIDADE',20,4);
  $pdf->escreve(3,$y+2,$pdf->transp->vol->qVol,20,4,0,'C');
  $pdf->Rect(23,$y,40,6);
  $pdf->label(23,$y,'ESPECIE',20,4);
  $pdf->escreve(23,$y+2,$pdf->transp->vol->esp,40,4,0,'C');
  $pdf->Rect(63,$y,80,6);
  $pdf->label(63,$y,'MARCA',20,4);
  $pdf->escreve(63,$y+2,'',80,4,0,'C');
  $pdf->Rect(143,$y,20,6);
  $pdf->label(143,$y,'NUMERO',20,4);
  $pdf->escreve(143,$y+2,'',20,4,0,'C');
  $pdf->Rect(163,$y,20,6);
  $pdf->label(163,$y,'PESO BRUTO',20,4);
  $pdf->escreve(163,$y+2, ($pdf->transp->vol->pesoB)?number_format((string)$pdf->transp->vol->pesoB,3,',','.'):'0,00',20,4,0,'R');
  $pdf->Rect(183,$y,24,6);
  $pdf->label(183,$y,'PESO LIQUIDO',24,4);
  $pdf->escreve(183,$y+2,($pdf->transp->vol->pesoB)?number_format((string)$pdf->transp->vol->pesoB,3,',','.'):'0,00',24,4,0,'R');

  // quadro "quadro dos produtos/servicos"
  $pdf->SetFont('Courier','',6);
  $pdf->lin = $y + 14;
  foreach($pdf->itens as $det){
    // informacoes adicionais do produto
    $infadprod = '';
    if (isset($det->infAdProd))
      $infadprod = $det->infAdProd;
    // pegando alguns dados de tag´s internas
    $orig     = '';
    $icmscst  = '';
    $icmsvlr  = 0;
    $icmsaliq = 0;
    $ipialiq  = 0;
    $ipivlr   = 0;
    foreach($det->imposto->ICMS as $icms){
      foreach($icms as $conteudo){
        $orig       = $conteudo->orig;
        $icmscst    = $conteudo->CST;
        $icmsvlr    = isset($conteudo->vICMS)?(string)$conteudo->vICMS:0;
        $icmsaliq   = isset($conteudo->pICMS)?(string)$conteudo->pICMS:0;
      }
    } 
    foreach($det->imposto->IPI as $ipi){
      foreach($ipi as $conteudo){
        $ipicst     = $conteudo->CST;
        $ipivlr     = isset($conteudo->vIPI)?(string)$conteudo->vIPI:0;
        $ipialiq    = isset($conteudo->pIPI)?(string)$conteudo->pIPI:0;
      }
    } 
    //
    $c=3;
    $pdf->escreve($c,$pdf->lin+1,$det->prod->cProd,$pdf->larg_codigo,4);
    $c += $pdf->larg_codigo;
    // nome do produto (até duas linhas)
    $xprod  = $det->prod->xProd;
    $xprod2 = '';
    if (strlen($xprod)>52){
      $xprod2 = trim(substr($xprod,52,strlen($xprod)));
      $xprod  = substr($xprod,0,52);
    }
    $pdf->escreve($c,$pdf->lin+1,$xprod,$pdf->larg_descricao,4);
    if ($xprod2!=''){
      $pdf->lin = $pdf->lin + 2;
      $pdf->escreve($c,$pdf->lin+1,$xprod2,$pdf->larg_descricao,4);
    }
    // infomacoes adicionais do produto (até duas linhas)
    $infadprod2 = '';
    if (strlen($infadprod)>52){
      $infadprod2 = substr($infadprod,52,strlen($infadprod));
      $infadprod = substr($infadprod,0,52);
    }
    if ($infadprod!=''){
      $pdf->lin = $pdf->lin + 2;
      $pdf->escreve($c,$pdf->lin+1,$infadprod,$pdf->larg_descricao,4);
      if ($infadprod2!=''){
        $pdf->lin = $pdf->lin + 2;
        $pdf->escreve($c,$pdf->lin+1,$infadprod2,$pdf->larg_descricao,4);
      }
    }
    $c += $pdf->larg_descricao;
    $pdf->escreve($c,$pdf->lin+1,substr($det->prod->NCM,0,4).'.'.substr($det->prod->NCM,4,2).'.'.substr($det->prod->NCM,6,2),$pdf->larg_ncm,4);
    $c += $pdf->larg_ncm;
    $pdf->escreve($c,$pdf->lin+1,$orig.$icmscst,$pdf->larg_cst,4);
    $c += $pdf->larg_cst;
    $pdf->escreve($c,$pdf->lin+1,substr($det->prod->CFOP,0,1).'.'.substr($det->prod->CFOP,1,3),$pdf->larg_cfop,4);
    $c += $pdf->larg_cfop;
    $pdf->escreve($c,$pdf->lin+1,$det->prod->uCom,$pdf->larg_und,4);
    $c += $pdf->larg_und;
    $pdf->escreve($c,$pdf->lin+1,number_format((string)$det->prod->qCom,0,',',''),$pdf->larg_qtd,4,0,'R');
    $c += $pdf->larg_qtd;
    $pdf->escreve($c,$pdf->lin+1,number_format((string)$det->prod->vUnCom,2,',',''),$pdf->larg_vunit,4,0,'R');
    $c += $pdf->larg_vunit;
    $pdf->escreve($c,$pdf->lin+1,number_format((string)$det->prod->vProd,2,',',''),$pdf->larg_vtotal,4,0,'R');
    $c += $pdf->larg_vtotal;
    $pdf->escreve($c,$pdf->lin+1,number_format($icmsvlr,2,',',''),$pdf->larg_vicms,4,0,'R');
    $c += $pdf->larg_vicms;
    $pdf->escreve($c,$pdf->lin+1,number_format($ipivlr,2,',',''),$pdf->larg_vipi,4,0,'R');
    $c += $pdf->larg_vipi;
    $pdf->escreve($c,$pdf->lin+1,number_format($icmsaliq,2,',',''),$pdf->larg_picms,4,0,'R');
    $c += $pdf->larg_picms;
    $pdf->escreve($c,$pdf->lin+1,number_format($ipialiq,2,',',''),$pdf->larg_pipi,4,0,'R');
    $c += $pdf->larg_pipi;
    //
    $pdf->lin = $pdf->lin + 2;
  }

  if ($danfearquivo=="")
    $pdf->Output();
  else 
    $pdf->Output($danfearquivo,"F");
}