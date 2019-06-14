<?php 

  foreach($_POST as $campo){
    /*
     -- aqui o $campo, conterá um json com os dados do pedido
     {"PEDIDO":"0111111"
      ,"DATAREQUISICAO":"2018-08-02 16:04:25"
      ,"TRANSPORTADORA":"CORREIO"
      ,"MODALIDADE_POSTAGEM":"SEDEX"
      ,"STATUS":"FALTA O ENVIO DO XML\/NFe"
      ,"ONDE":null
      ,"ONDE_DETALHE":null
      ,"CHECKOUT_DATA":null
      ,"CHAVENFE":null
      ,"TRACKING":""
      ,"ROMANEIO":
        [
          {
            "data": "dd/mm/yyyy"
            ,"situacao": "ABERTO/FECHADO/DESPACHADO"
          }
        ]
    }
    */
  }

// ESTE RETORNO É OBRIGATÓRIO PARA EU SABER QUE A SOLICITACAO FOI RECEBIDA COM EXITO
// E COM ISTO INTERROMPER O ENVIO DESTE EVENTO DO CONTRARIO FICARA REGISTRADO COMO ERRO
// DE RECEPCAO POR PARTE DO CLIENTE
echo "OK";  