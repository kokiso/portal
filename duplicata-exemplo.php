<?php 
  include "classPhp/duplicata.class.php";

  $dup = new duplicada();
  $dup->emissor(["cnpj"=>"11.222.333/4444-55", "razao"=>"TRAC", "endereco"=>"RUA ENTREGUE, S/N", "cep"=>"00.111-222", "cidade"=>"RIBEIRAO PRETO", "bairro"=>"VILA TRAC", "uf"=>"SP", "telefone"=>"(00)4444-5555"]);
  $dup->cliente(["cnpjcpf"=>"55.666.555/4444-33", "razao"=>"CLIENTE TRAC", "endereco"=>"RUA PRESTADA, 1", "cep"=>"00.111-222", "cidade"=>"RIBEIRAO PRETO", "bairro"=>"VILA DOS CLIENTES", "uf"=>"SP", "telefone"=>"(00)6666-7777"]);
  $dup->fatura(["numero"=>"12345/19","emissao"=>"06/10/2019","vencto"=>"30/10/2019"]);
  $dup->servicos(
      [ "lista" =>
          [
              ["descricao"=>"RASTREAMENTO DE BICLETA" ,"qtdade"=>1  ,"unitario"=>134.50 ,"valor"=>134.50]
            , ["descricao"=>"RASTREAMENTO DE CARRO"   ,"qtdade"=>2  ,"unitario"=>200.50 ,"valor"=>401]
          ]
      ]
    );
  $dup->observacoes(["A garantia do servico prestado atende as leis vigentes, 3 meses","Condicoes de pagamento conforme solicitado (A VISTA)"]);
  $dup->gera();