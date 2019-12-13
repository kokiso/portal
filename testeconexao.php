<?php 
  //$c = new PDO("sqlsrv:Server=localhost;Database=TRAC", "sa", "P0rt@ltr@c");

  $serverName = "localhost";
  $connectionOptions = array(
    "Database" => "TRAC",
    "Uid" => "sa",
    "PWD" => "P0rt@ltr@c"
  );
  $conn = sqlsrv_connect($serverName, $connectionOptions);
  if($conn)
    echo "Connected!";