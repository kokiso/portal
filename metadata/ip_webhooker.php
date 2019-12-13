<?php
  //https://king.host/blog/2018/07/sockets-em-servidor-php/
  //https://pt.stackoverflow.com/questions/249910/php-com-sockets
  //https://github.com/gustavobeavis/ws_pure_php/blob/master/server.php
  //https://stackoverflow.com/questions/12999900/php-socket-listening-loop
  //https://stackoverflow.com/questions/21996041/have-a-php-server-script-persistently-listen-on-a-socket-non-blocking
  //https://github.com/ScottPhillips/PHP-TCP-Port-Listener/blob/master/example.php
  require 'phpClass/pier8functions.php';
  //--
  //-- determnando o ambiente
  header('Content-Type: text/html; charset=UTF-8'); 
  $manual = 'n';
  if (isset($_GET['arquivo'])){
    $origem  = file_get_contents('/xampp/htdocs/api/'.$_GET['arquivo']);
    $manual  = 's';
  }else
    $origem  = utf8_encode(file_get_contents('php://input'));  
  $pedido    = json_decode($origem);  
  if ($manual=='s'){
    print_r($pedido);
    echo '<hr>';
    echo 'tracking_code:'.(string)$pedido->tracking_code.'<br>';
    echo 'localized:'.(string)$pedido->history->shipment_order_volume_state_localized.'<br>';
    echo 'invoice_key:'.(string)$pedido->invoice->invoice_key.'<br>';
    echo 'order_number:'.(string)$pedido->order_number.'<br>';
    //exit;
  }
  $resultado = '?';
  switch (json_last_error()) {
    case JSON_ERROR_NONE:
      $resultado = 'ok';
      break;
    case JSON_ERROR_DEPTH:
      $resultado = 'Maximum stack depth exceeded';
      break;
    case JSON_ERROR_STATE_MISMATCH:
      $resultado = 'Underflow or the modes mismatch';
      break;
    case JSON_ERROR_CTRL_CHAR:
      $resultado = 'Unexpected control character found';
      break;
    case JSON_ERROR_SYNTAX:
      $resultado = 'Syntax error, malformed JSON';
      break;
    case JSON_ERROR_UTF8:
      $resultado = 'Malformed UTF-8 characters, possibly incorrectly encoded';
      break;
    default:
      $resultado = 'Unknown error';
      break;
  }
  if ($resultado!='ok'){
    echo 'Erro nesta operação: '.$resultado;
    return;
  }
  // definindo o caminho do banco
  $ambiente = 'h';
  if (file_exists('d:/pastabis/banco/pier8.gdb'))
    $ambiente = 'p';
  $cnx = usegdb($ambiente);
  //
  // pegando os dados do proprietario do pedido
  $ds  = ibase_query($cnx,"select ws_apelido,pedido from ws_pedido left outer join ws_senha on cliente_pier=ws_cnpj where chavenfe='".$pedido->invoice->invoice_key."'");
  $row = ibase_fetch_object($ds);
  //--
  //-- gravando o pedido
  $track      = (string)$pedido->tracking_code;
  $track      = str_replace(array("\r","\n"),"",$track);
  $onde       = (string)$pedido->history->shipment_order_volume_state_localized;
  $finalizado = 'N';
  $status     = 'EM ANDAMENTO';
  $correio_id = '';
  if (strtolower($track)!=='null'){
    $correio_id = $track;
    $status     = $track;
  }
  if (substr($onde,0,5)=='Em tr'){
    $onde = 'Em transito';
  }
  if ($onde=='Entregue'){
    $finalizado = 'S';
    $status     = 'ENTREGUE';
  }
  $sql = "update ws_pedido set finalizado='".$finalizado
          ."',onde='".$onde."'"
          .",correio_id=".($correio_id==''?'null':"'".$correio_id."'")
          .",dtmovimento='".date('d.m.Y')."'"
          .",hrmovimento='".date('h:i')."'"
          .",objeto_correio=correio_id"
          .",status='".$status."' "
          ."where chavenfe=('".(string)$pedido->invoice->invoice_key."') and (coalesce(finalizado,'N')='N')";
  if ($manual=='s')        
    echo '<br>'.$sql.'<br>';          
  $sql = str_replace("\r","",$sql);
  $sql = str_replace("\n","",$sql);
  execquery([$sql]);
  
  /*
  $a = fopen('ip/iprecebe-'.$pedido->invoice->invoice_key.'.txt','w+');
  fwrite($a,$pedido->tracking_code.'/');
  fwrite($a,$pedido->order_number.'/');
  fwrite($a,$pedido->history->shipment_order_volume_state_localized.'/');
  fwrite($a,$pedido->invoice->invoice_key.'/');
  fclose($a);
  */
  $apelido   = $row->WS_APELIDO;
  $nropedido = $row->PEDIDO;
  if ($apelido=='')
    $apelido = 'NAO_ENCONTRADO';
  if ($nropedido=='')
    $pedido = date('Ymd_his');
  arquivolog($apelido, $nropedido, 'ip_webhook'        , 'txt', $pedido->tracking_code.'/'.$pedido->order_number.'/'.$pedido->history->shipment_order_volume_state_localized.'/'.$pedido->invoice->invoice_key.'/');
  arquivolog($apelido, $nropedido, 'ip_webhook_nativo' , 'txt', $origem);
  
  echo 'ok';
