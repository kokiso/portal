<?php
  class consultaCep{
    var $bairro;    
    var $cidade; 
    var $codmun;  
    var $lat;
    var $lon;
    var $uf;
    
    function buscaCep($cep){
      try{
        header('Content-Type: text/html; charset=utf-8');
        ini_set('user_agent', 'daniel.guimaraes@atlasgr.com.br');

        $key_wscep = "07aac92a0d31a9a423cd38843895fb7f53083b62";
        $url_wscep = "http://api.wscep.com/cep?key={$key_wscep}&val={$cep}";
        //"http://api.wscep.com/cep?key=07aac92a0d31a9a423cd38843895fb7f53083b62&val=3523404";
        $arr_wscep['resultado'] = 0;
        $arr_wscep = (array)simplexml_load_file($url_wscep);
        $resultado=( isset($arr_wscep['resultado']) ? $arr_wscep['resultado'] : 0 );

        if( $resultado==1 ){
          //file_put_contents("aaa.xml",print_r($arr_wscep,true));          
          ////////////
          // BAIRRO //
          ////////////
          if( isset($arr_wscep['bairro']) ){
            $bairro = strtolower($arr_wscep['bairro']);
            $bairro = iconv("UTF-8","ASCII//TRANSLIT",$bairro);
            $bairro = preg_replace("/[~^\'`^]/",null,$bairro);
            $bairro = strtoupper($bairro);
          } else {
            $bairro="NSA";  
          }  
          ////////////
          // CIDADE //
          ////////////
          $cidade = strtolower($arr_wscep['cidade']);
          $cidade = iconv("UTF-8","ASCII//TRANSLIT",$cidade);
          $cidade = preg_replace("/[~^\'`^]/",null,$cidade);
          $cidade = strtoupper($cidade);
          ////////////
          // CODMUN //
          ////////////
          $codmun=$arr_wscep['cod_ibge_municipio'];
          //////////////
          // ENDERECO //
          //////////////
          if( isset($arr_wscep['logradouro']) ){
            $endereco = strtolower($arr_wscep['logradouro']);
            $endereco = iconv("UTF-8","ASCII//TRANSLIT",$endereco);
            $endereco = preg_replace("/[~^\'`^]/",null,$endereco);
            $endereco = strtoupper($endereco);
            
            $expld    = explode(" ",$endereco);
            
            $codtl    = trim(strtoupper($expld[0]));
            $endereco ="";
            for($lin=0;$lin<count($expld);$lin++){
              if($lin>0){
                $endereco.=($endereco=="" ? "" : " ").$expld[$lin];
              };
            };
            /////////////////////////////////////////////////////////////////////
            // Ajustando o logradouro conforme alguns cadastros basicos do ERP //
            /////////////////////////////////////////////////////////////////////
            switch( $codtl ){
              case "ALA"      : $codtl="AL";  break;
              case "ALAM"     : $codtl="AL";  break;
              case "AVE"      : $codtl="AV";  break;
              case "AVENIDA"  : $codtl="AV";  break;
              case "COM"      : $codtl="RUA"; break;
              case "PCA"      : $codtl="PCA"; break;
              case "PÇA"      : $codtl="PCA"; break;
              case "PC"       : $codtl="PCA"; break;
              case "PRA"      : $codtl="PCA"; break;
              case "PRACA"    : $codtl="PCA"; break;
              case "Q"        : $codtl="QD";  break;
              case "QUA"      : $codtl="QD";  break;
              case "R"        : $codtl="RUA"; break;
              case "TR"       : $codtl="TV";  break;
              case "TRA"      : $codtl="TV";  break;
              case "TV"       : $codtl="TV";  break;
              case "VIL"      : $codtl="VL";  break;
              case "VILA"     : $codtl="VL";  break;
              case "PASSAGEM" : $codtl="RUA"; break;
            };
          } else {
            $codtl="RUA";
            $endereco="NAO INFORMADO";
          }
          /////////
          // LAT //
          /////////
          $lat=(isset($arr_wscep['lat']) ? $arr_wscep['lat'] : 0);
          /////////
          // LON //
          /////////
          $lon=(isset($arr_wscep['lng']) ? $arr_wscep['lng'] : 0);
          ////////
          // UF //
          ////////
          $uf=trim(strtoupper($arr_wscep['uf']));
          //
          //
          ///////////////////////////////
          // retornando ao JS um array //
          ///////////////////////////////
          $arrRet=[];
          array_push($arrRet,[
            "uf"        =>  $uf
            ,"cidade"   =>  $cidade
            ,"bairro"   =>  $bairro
            ,"codtl"    =>  $codtl
            ,"endereco" =>  $endereco
            ,"codcdd"   =>  $codmun
            ,"lat"      =>  $lat
            ,"lon"      =>  $lon
          ]);
          
          $retorno='[{"retorno":"OK"
                     ,"dados": '.json_encode($arrRet).'
                     ,"erro":""}]'; 
        } else {
          $retorno='[{"retorno":"ERR","dados":"","erro":"CEP '.$cep.' NAO LOCALIZADO"}]';
        }
      } catch (Exception $e){
        $retorno='[{"retorno":"ERR","dados":"","erro":"'.$e.'"}]';
      }  
      return $retorno;  
    }
  }
?>