<?php 
  class duplicada {
    private $emissor; // dados do emissor (cnpj, razao, endereco, cep, cidade, bairro, telefone)
    private $cliente; // dados do cliente (cnpjcpf, razao, endereco, cep, cidade, bairro, telefone)
    private $fatura; // dados da fatura (numero, emissao, vencimento)
    private $servicos; // serviços prestados na fatura (matriz = descricao, qtdade, unitario, valor)
    private $observacoes; // vetor simples, contendo as linhas da observacao
    private $me; // margem esquerda
    private $ms; // margem superior
    private $dup; 
    private $total;

    public function __constructor(){
      $this->emissor  = [];
      $this->cliente  = [];
      $this->fatura   = [];
      $this->servicos = [];
      $this->me       = 30;
      $this->ms       = 30;
      $this->dup      = null;
      $this->total    = 0;
    }

    public function emissor($dados){
      if(!isset($dados["cnpj"]))
        throw new Exception("defina o cnpj do emissor");
      if(!isset($dados["razao"]))
        throw new Exception("defina a razao do emissor");
      if(!isset($dados["endereco"]))
        throw new Exception("defina o endereco do emissor");
      if(!isset($dados["cep"]))
        throw new Exception("defina o cep do emissor");
      if(!isset($dados["cidade"]))
        throw new Exception("defina o cidade do emissor");
      if(!isset($dados["uf"]))
        throw new Exception("defina o uf do emissor");
      if(!isset($dados["telefone"]))
        throw new Exception("defina o telefone do emissor");
      //
      $this->emissor = $dados;
    }

    public function cliente($dados){
      if(!isset($dados["cnpjcpf"]))
        throw new Exception("defina o cnpjcpf do cliente");
      if(!isset($dados["razao"]))
        throw new Exception("defina a razao do cliente");
      if(!isset($dados["endereco"]))
        throw new Exception("defina o endereco do cliente");
      if(!isset($dados["cep"]))
        throw new Exception("defina o cep do cliente");
      if(!isset($dados["cidade"]))
        throw new Exception("defina o cidade do cliente");
      if(!isset($dados["uf"]))
        throw new Exception("defina o uf do cliente");
      if(!isset($dados["telefone"]))
        throw new Exception("defina o telefone do cliente");
      //
      $this->cliente = $dados;
    }

    public function fatura($dados){
      if(!isset($dados["numero"]))
        throw new Exception("defina o numero da fatura");
      if(!isset($dados["emissao"]))
        throw new Exception("defina a emissao da fatura");
      if(!isset($dados["vencto"]))
        throw new Exception("defina o vencto da fatura");
      //
      $this->fatura = $dados;
    }

    public function servicos($dados){
      if(!isset($dados["lista"]))
        throw new Exception("defina a lista de servicos prestados");
      if(!is_array($dados["lista"]))
        throw new Exception("a lista de servicos deve ser uma matriz");
      $this->total = 0;
      foreach($dados["lista"] as $servico){
        if(!isset($servico["qtdade"]))
          throw new Exception("a lista de servico nao contem qtdade executada");
        if(!isset($servico["descricao"]))
          throw new Exception("a lista de servico nao contem descricao do servico executado");
        if(!isset($servico["unitario"]))
          throw new Exception("a lista de servico nao contem valor unitario do servico executado");
        if(!isset($servico["valor"]))
          throw new Exception("a lista de servico nao contem valor do servico executado");
        //
        $this->total += (floatval($servico["unitario"])*floatval($servico["qtdade"]));
      }
      $this->servicos = $dados;
    }

    public function observacoes($dados){
      if(!is_array($dados))
        throw new Exception("as observacoes devem ser um vetor simples");
      $this->observacoes = $dados;
    }

    function bold($size=8){
      $this->dup->SetFont('Courier','B',$size);
    }
    
    function normal($size=8){
      $this->dup->SetFont('Courier','',$size);
    }

    function say($x,$y,$texto,$w,$h,$borda=0,$alinha='E'){
      $this->dup->SetXY($x+$this->me,$y+$this->ms);
      $this->dup->Cell($w,$h, utf8_decode($texto),$borda,0,$alinha);
    }

    public function gera(){
      if((count($this->emissor)==0) or (count($this->cliente)==0) or (count($this->servicos)==0) or (count($this->fatura)==0))
        throw new Exception("fatura nao preenchida corretamente");
      //
      include "php/fpdf.php";

      // padrao
      $this->dup = new FPDF7('P','mm','A4');
      $this->dup->SetMargins(3, 6);
      $this->dup->AliasNbPages();
      $this->dup->SetAutoPageBreak(true,33);
      $this->dup->AddPage();
      $this->dup->SetFont("Arial",'', 8);
      $this->dup->SetAuthor("Autor: WS SISTEMAS / DUPLICATA"); 

      $this->dup->SetFillColor(176,224,230);
      $this->dup->rect(10,10,190,6,"F");

      // emissor
      $top = 30;
      $this->bold();
      $this->say(10,$top,$this->emissor["razao"],50,4);
      $this->normal();
      $this->say(10,$top+=5,$this->emissor["endereco"],50,4);
      $this->say(10,$top+=3,$this->emissor["cep"]." - ".$this->emissor["cidade"]." - ".$this->emissor["bairro"]." - ".$this->emissor["uf"],50,4);
      $this->say(10,$top+=3,$this->emissor["telefone"],50,4);

      // cobrar a
      $btop = $top;
      $top += 10;
      $this->bold();
      $this->say(10,$top,"COBRAR A",50,4);
      $this->normal();
      $this->say(10,$top+=5,$this->cliente["razao"],50,4);
      $this->say(10,$top+=3,$this->cliente["cnpjcpf"],50,4);
      $this->say(10,$top+=3,$this->cliente["endereco"],50,4);
      $this->say(10,$top+=3,$this->cliente["cep"]." - ".$this->cliente["cidade"]." - ".$this->cliente["bairro"]." - ".$this->cliente["uf"],50,4);
      $this->say(10,$top+=3,$this->cliente["telefone"],50,4);

      // dados da fatura
      $top = $btop;
      $top += 10;
      $this->bold();
      $this->say(150,$top+=5,"FATURA N.",10,4);
      $this->say(150,$top+=3,"EMISSAO",10,4);
      $this->say(150,$top+=3,"VENCTO",10,4);
      $this->normal();

      $top = $btop;
      $top += 10;
      $this->say(175,$top+=5,$this->fatura["numero"],10,4);
      $this->say(175,$top+=3,$this->fatura["emissao"],10,4);
      $this->say(175,$top+=3,$this->fatura["vencto"],10,4);

      $top += 20;
      $this->bold(20);
      $this->say(10,$top,"Total da fatura",90,4);
      $this->say(175,$top,number_format($this->total,2,",","."),25,4,0,"R");
      $this->normal();

      $top += 12;
      $this->dup->line($this->me+10,$this->ms+$top,205,$this->ms+$top);
      $this->bold();
      $this->say(10,$top,"Qtd",20,4);
      $this->say(20,$top,"Descricao",20,4);
      $this->say(160,$top,"Unitario",20,4);
      $this->say(190,$top,"Valor",20,4);
      $this->dup->line($this->me+10,$this->ms+$top+5,205,$this->ms+$top+5);
      $this->normal();
      $top += 8;

      foreach($this->servicos["lista"] as $servico){
        $this->say(10,$top,$servico["qtdade"],20,4);
        $this->say(20,$top,$servico["descricao"],20,4);
        $this->say(160,$top,number_format($servico["unitario"],2,",","."),15,4,0,"R");
        $this->say(185,$top,number_format($servico["valor"],2,",","."),15,4,0,"R");
        $top += 6;
      }

      $top = 200;
      $this->bold(10);
      $this->say(10,$top,"Observacoes gerais:",20,4);
      $this->normal();

      $top += 10;
      foreach($this->observacoes as $obs){
        $this->say(10,$top,$obs,100,4);
        $top += 4;
      }

      $this->dup->rect(10,280,190,6,"F");

      // gera o pdf
      $this->dup->output();
    }

  }
