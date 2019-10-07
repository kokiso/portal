<?php
  class conectaBd{
    //////////////////////////////////////////////////////////////////////////
    // Criando os atributos da classe                                       //
    // Estas podem se acessadas pelo criador da classe obj->classe="JOSE";  //
    //////////////////////////////////////////////////////////////////////////
    var $login;
    var $select;
    var $gdb;
    var $tr;
    var $retorno;
    //////////////////////////////////////////////////////////////////////////////////////
    // Ao criar o objeto posso passar como padrão  o usuario e senha do banco de dados  //
    //////////////////////////////////////////////////////////////////////////////////////
    function __construct(){
      $this->vetor          = array();
      //////////////////////////////////////////////////////////////////////////////////////
      // msgSelectVazio                                                                   //
      // Propriedade para retornar mensagem quando um select não retorna nenhum registro  //
      //////////////////////////////////////////////////////////////////////////////////////
      $this->msgSelectVazio = true;
      ////////////////////////////////////////////////////////////////////
      // msgErro                                                        //  
      // Opção para alterar com o nome da tabela que se refere o select //
      ////////////////////////////////////////////////////////////////////
      $this->msgErro="NENHUM REGISTRO LOCALIZADO PARA ESTA OPÇÃO!";
      array_push($this->vetor,
        array("CONECTA"  =>  array(
          ["login"=>"a2"  , "path"=>"localhost" , "cnpj"=>"00000000001","user"=>"sa","pass"=>"@A1111111"]   //Bd oficial
         )
        )
      );    
    }
    //--
    function conecta($login){
      $_SESSION["path"]= "";
      $_SESSION["user"]= "";
      $_SESSION["pass"]= "";
      
      $retorno        = "";
      $this->retorno  = ["retorno"=>"OK","dados"=>"","erro"=>""];  
      $this->login    = $login;
      $this->msgErro="NENHUM REGISTRO LOCALIZADO PARA ESTA OPÇÃO! ".$login;
      foreach($this->vetor[0]["CONECTA"] as $cnct):
        if( $cnct["login"]==$login):
          $_SESSION["login"] = $cnct["login"];
          $_SESSION["path"]  = $cnct["path"];
          $_SESSION["user"]  = $cnct["user"];
          $_SESSION["pass"]  = $cnct["pass"];
          $_SESSION["connInfo"]=array("Database" => $_SESSION["login"], "UID" => $_SESSION["user"], "PWD" => $_SESSION["pass"]);
        endif;
      endforeach;
      
      if( $_SESSION["path"]=="" ):
        $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=>"LOGIN ".$login." NAO LOCALIZADO!"];  
      else:
        $conn = sqlsrv_connect( $_SESSION["path"],$_SESSION["connInfo"] );
        $_SESSION['conn']=$conn;
        if( !$conn ) {
          $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=>"LOGIN ".print_r( sqlsrv_errors(), true)." NAO LOCALIZADO!"];
          die( print_r( sqlsrv_errors(), true));
        };  
      endif;
    }
    function generator($tabela){
      $tsql_callSP  = "{call dbo.prcGenerator( ?, ? )}";
      $retorno      = 0;  
      $params = array(   
        array($tabela,  SQLSRV_PARAM_IN),  
        array(&$retorno,SQLSRV_PARAM_OUT)  
      ); 
      $stmt = sqlsrv_query( $_SESSION['conn'], $tsql_callSP, $params);                 
      if( $stmt === false ){  
        echo "Error in executing statement 3.\n";  
        die( print_r( sqlsrv_errors(), true));  
      };  
      // Devolve o valor do retorno
      return $retorno;
      sqlsrv_free_stmt( $stmt);        
    }
    function numeronf($codigo){
      $tsql_callSP  = "{call dbo.prcNumNf( ?, ? )}";
      $retorno      = 0;  
      $params = array(   
        array($codigo,  SQLSRV_PARAM_IN),  
        array(&$retorno,SQLSRV_PARAM_OUT)  
      ); 
      $stmt = sqlsrv_query( $_SESSION['conn'], $tsql_callSP, $params);                 
      if( $stmt === false ){  
        echo "Error in executing statement 3.\n";  
        die( print_r( sqlsrv_errors(), true));  
      };  
      // Devolve o valor do retorno
      return $retorno;
      sqlsrv_free_stmt( $stmt);        
    }
    
    ////////////////////////////////////////////////////////////////////////
    // PARAMETRO PARA MENSAGEM QUANDO SELECT RETORNAR SEM NENHUM REGISTRO //
    ////////////////////////////////////////////////////////////////////////
    function msgSelect($bool){
      $this->msgSelectVazio=$bool;
    }  
    ////////////////////////////////////////
    // PARAMETRO PARA MENSAGEM DE RETORNO //
    ////////////////////////////////////////
    function msg($str){
      $this->msgErro=$str;
    }  
    ////////////////////////
    // SELECT ASSOCIATIVO //
    ////////////////////////
    function selectAssoc($select){
      if( $this->retorno['retorno'] != "ERR" ){
        try{
          $qtosReg  = 0;
          $reg      = array();
          $params   = array();
          $options  = array("Scrollable" => SQLSRV_CURSOR_FORWARD);
          $consulta = sqlsrv_query($_SESSION['conn'], $select, $params, $options);          
          
          while ($linha = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $reg[]=$linha;
            $qtosReg++;
          };
          if( $qtosReg>0 ){          
            $this->retorno=[
               "retorno"=>"OK"
              ,"dados"=>$reg
              ,"erro"=>""
              ,"qtos"=>$qtosReg
            ]; 
          } else {
            //$this->retorno=["retorno"=>"OK","dados"=>[],"erro"=>""];      
            //$this->retorno=["retorno"=>"OK","dados"=>[],"erro"=>"","qtos"=>0];
            //////////////////////////////////////////////////////////////////////////////////////////
            // Alguns selects podem retornar vazio e não precisam gerar a mensagem de erro, ex:SPED //
            //////////////////////////////////////////////////////////////////////////////////////////
            if( $this->msgSelectVazio )
              $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=>$this->msgErro,"qtos"=>$qtosReg];
            else            
              $this->retorno=["retorno"=>"OK","dados"=>[],"erro"=>"","qtos"=>$qtosReg];    
          };  
        } catch (Exception $e){
          $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=> $this->login." ".substr(str_replace(["\r","\n","{","}","\\",'"'],"",utf8_decode($e)),0,120)];  
        };  
      };  
      return $this->retorno;
    }  
    ////////////////////////////
    // SELECT NAO ASSOCIATIVO //
    ////////////////////////////
    function select($select){
      if( $this->retorno['retorno'] != "ERR" ){
        try{
          $qtosReg  = 0;
          $reg      = array();
          $params   = array();
          $options  = array("Scrollable" => SQLSRV_CURSOR_FORWARD);
          $consulta = sqlsrv_query($_SESSION['conn'], $select, $params, $options);
          while ($linha = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_NUMERIC)) {
            $registro=array();
            foreach($linha as $campo)
              $registro[]=utf8_encode($campo);
            $reg[]=$registro;
            $qtosReg++;
          };
          if( $qtosReg>0 ){          
            //$this->retorno=["retorno"=>"OK","dados"=>$reg,"erro"=>""];  
            $this->retorno=[
               "retorno"=>"OK"
              ,"dados"=>$reg
              ,"erro"=>""
              ,"qtos"=>$qtosReg
            ]; 
          } else {
            //$this->retorno=["retorno"=>"OK","dados"=>[],"erro"=>""];      
            //////////////////////////////////////////////////////////////////////////////////////////
            // Alguns selects podem retornar vazio e não precisam gerar a mensagem de erro, ex:SPED //
            //////////////////////////////////////////////////////////////////////////////////////////
            if( $this->msgSelectVazio )
              $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=>$this->msgErro,"qtos"=>$qtosReg];
            else            
              $this->retorno=["retorno"=>"OK","dados"=>[],"erro"=>"","qtos"=>$qtosReg];    
          };  
        } catch (Exception $e){
          $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=>$e];  
        }  
      }  
      return $this->retorno;
    }
    function selectDatatables($select){
      if( $this->retorno['retorno'] != "ERR" ){
        try{
          $qtosReg  = 0;
          $reg      = array();
          $params   = array();
          $options  = array("Scrollable" => SQLSRV_CURSOR_FORWARD);
          $consulta = sqlsrv_query($_SESSION['conn'], $select, $params, $options);
          while ($linha = sqlsrv_fetch_object($consulta)) {
            $reg[]=$linha;
            $qtosReg++;
          };
          if( $qtosReg>0 ){          
            //$this->retorno=["retorno"=>"OK","dados"=>$reg,"erro"=>""];  
            $this->retorno=[
               "retorno"=>"OK"
              ,"dados"=>$reg
              ,"erro"=>""
              ,"qtos"=>$qtosReg
            ]; 
          } else {
            //$this->retorno=["retorno"=>"OK","dados"=>[],"erro"=>""];      
            //////////////////////////////////////////////////////////////////////////////////////////
            // Alguns selects podem retornar vazio e não precisam gerar a mensagem de erro, ex:SPED //
            //////////////////////////////////////////////////////////////////////////////////////////
            if( $this->msgSelectVazio )
              $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=>$this->msgErro,"qtos"=>$qtosReg];
            else            
              $this->retorno=["retorno"=>"OK","dados"=>[],"erro"=>"","qtos"=>$qtosReg];    
          };  
        } catch (Exception $e){
          $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=>$e];  
        }  
      }  
      return $this->retorno;
    }  
    //////////////////
    // ATUALIZANDO  //
    //////////////////
    function cmd($atualiza){
      if( $this->retorno['retorno'] != "ERR" ){      
        try{
          if( count($atualiza)==0 )
            throw new Exception('NENHUMA INSTRUCAO SQL PARA SER EXECUTADA');          
          
          if ( sqlsrv_begin_transaction( $_SESSION['conn'] ) === false ) {
            //sqlsrv_configure("WarningsReturnAsErrors", 0);  
            $arr      = sqlsrv_errors();
            $int      = strpos($arr[0]["message"],"[SQL Server]");
            $str      = trim(substr($arr[0]["message"],($int+12),strlen($arr[0]["message"])));
            $str      = str_replace('"','',$str);
            $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=> substr($str,0,300)];
          } else {
            $params   = array();
            $commitar = true;

            foreach( $atualiza as $atu ){
              // echo $atu.PHP_EOL;
              if( !sqlsrv_query($_SESSION['conn'], $atu, $params)) {
                $commitar = false;  
                break;
              };
            };
            if($commitar) {
              sqlsrv_commit($_SESSION['conn']);
              $this->retorno  = ["retorno"=>"OK","dados"=>"","erro"=>""];
            } else {
              $retorno  = 'ERR';
              $arr      = sqlsrv_errors();
              $int      = strpos($arr[0]["message"],"[SQL Server]");
              $str      = trim(substr($arr[0]["message"],($int+12),strlen($arr[0]["message"])));
              $str      = str_replace('"','',$str);
              //////////////////////////////////////////////
              // Tentando mostrar uma mensagem mais amigavel
              //////////////////////////////////////////////
              $str=str_replace(['The UPDATE statement conflicted with the CHECK constraint','The conflict occurred in database']
                              ,['A atualização causou um erro na regra','O erro ocorreu em'],$str);
              ///
              $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=> substr($str,0,300)];
              sqlsrv_rollback( $_SESSION['conn'] );
              sqlsrv_close( $_SESSION['conn'] );
            }; 
          }
        } catch(Exception $e ){
            $e = substr($e,0,strpos($e,' in C:'));
            $erro=str_replace("\\","/",$e);
            $erro=str_replace(["Exception:","exception 1","exception","Exception","with message","\r","\n","{","}","\\",'"',"'"],"",utf8_decode($erro));
            $this->retorno=["retorno"=>"ERR","dados"=>"","erro"=> substr($erro,0,300)];
            @ibase_rollback($this->gdb);            
        }
        return $this->retorno;        
      };        
    }  
    function setLogin($login){ 
      $this->login=$login; 
    }
    function verClasse(){
      echo '<pre>';
      print_r($this);
      echo '</pre>';      
    }    
  }
?>