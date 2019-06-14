<?php

$continua = true;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
  $continua=false;    
  echo "socket_create() falhou: razao: " . socket_strerror(socket_last_error()) . "\n";
};

if (socket_bind($sock, $address, $port) === false) {
  $continua=false;    
  echo "socket_bind() falhou: razao: " . socket_strerror(socket_last_error($sock)) . "\n";
};

if (socket_listen($sock, 5) === false) {
  $continua=false;    
  echo "socket_listen() falhou: razao: " . socket_strerror(socket_last_error($sock)) . "\n";
};

if( $continua ){

    if (($msgsock = socket_accept($sock)) === false) {
      echo "socket_accept() falhou: razao: " . socket_strerror(socket_last_error($sock)) . "\n";
      return;
    }


    if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
      echo "socket_read() falhou: razao: " . socket_strerror(socket_last_error($msgsock)) . "\n";
      return;
    }

    $talkback = "ST300CMD;907036181;02;ReqOwnNo;"; // comando a ser enviado
    //$talkback = "ST300CMD;907036181;02;SetOwnNo=98761234";
    socket_write($msgsock, $talkback, strlen($talkback)); // Envio de comando
  
    echo 'Command sent; check a return message:'.PHP_EOL;
    echo $buf.PHP_EOL;
              

            
    socket_close($msgsock);
}
  socket_close($sock);
  echo 'Exit';
  return 0;
