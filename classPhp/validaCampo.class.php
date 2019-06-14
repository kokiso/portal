<?php
  define("E_VAL_MINLEN","TAMANHO DEVE SER MAIOR OU IGUAL A %d PARA CAMPO %s");
  define("E_VAL_MAXLEN","TAMANHO MENOR 0U IGUAL A %d PARA CAMPO %s");  
  define("E_STR_DIGITOVALIDO","DIGITO %s INVALIDO PARA CAMPO CAMPO %s");  
  define("E_STR_NOTNULL","CAMPO %s NAO ACEITA VAZIO");  
  define("E_VAL_INTMAIORZERO","CAMPO %s DEVE TER VALOR MAIOR QUE ZERO");  
  define("E_VAL_INTMAIORIGUALZERO","CAMPO %s DEVE TER VALOR MAIOR OU IGUAL ZERO");  
  define("E_STR_COLUNA","NAO LOCALIZADO COLUNA %s PARA IMPORTACAO");  
  define("E_STR_CONTIDO","VALORES ACEITO %s PARA CAMPO CAMPO %s");  
  
  class validaCampo{
    var $newJs      = [];
    var $paraJs     = [];
    var $arrErro    = [];
    var $nomeTable  = "";
    var $clsRa      = "";

    function __construct($tbl,$numE){
      $this->nomeTable  = $tbl;
      $this->numErros   = $numE;
      $this->clsRa      = new removeAcento();
      $this->arrErro    = [];
    }
    /////////////////////////////////////////////////////////////////////
    // Para cada nova linha do excel a classe recebe o objeto original //
    /////////////////////////////////////////////////////////////////////
    function fncMatriz($js){
      $this->newJs=$js;
      //Gambiarra
      $tam=count($this->newJs);   
      for( $lin=0;$lin<$tam;$lin++){
        $this->newJs[$lin]->erro="OK";
        if( $this->newJs[$lin]->grade == "S")
          $this->newJs[$lin]->vlrDefault="nsa";
      };    
    }
    //
    ////////////////////////////////////////////////////////////////
    // Validando campo a campo                                    //
    // $this-newJs tem  o nome de cada campo pela coluna labelCol //
    ////////////////////////////////////////////////////////////////
    function fncValidar($campo,$valor){
      /*
      ,"titulo":[
       {"id":"1","field":"LGR_CODIGO" ,"labelCol":"CODIGO"    ,"vlrDefault":"nsa" ,"grade":"S","validar":"uppercase=S@removeacentos=S@tiraaspas=S@alltrim=S@minLen=2@maxLen=5","erro":"OK"}
      ,{"id":"2","field":"LGR_NOME"   ,"labelCol":"DESCRICAO" ,"vlrDefault":"nsa" ,"grade":"S","validar":"uppercase=S@removeacentos=S@tiraaspas=S@minLen=3@maxLen=20","erro":"OK"}
      ,{"id":"3","field":"LGR_ATIVO"  ,"labelCol":"ATIVO"     ,"vlrDefault":"S"   ,"grade":"N","validar":"uppercase=S@removeacentos=S@tiraaspas=S@contido=S|N@minLen=1@maxLen=1","erro":"OK"}
      ,{"id":"4","field":"LGR_REG"    ,"labelCol":"REG"       ,"vlrDefault":"P"   ,"grade":"N","validar":"uppercase=S@removeacentos=S@tiraaspas=S@contido=P|A@minLen=1@maxLen=1","erro":"OK"}
      ,{"id":"6","field":"LGR_CODUSR" ,"labelCol":"CODUSU"    ,"vlrDefault":"0001","grade":"N","validar":"nsa","erro":"OK"}]}]}          
      */
      
      $valor=trim($valor);
      
      $tam=count($this->newJs);
      for( $lin=0;$lin<$tam;$lin++){ 
        $col        = $this->newJs[$lin];
        $temColuna  = false;
        ////////////////////////////////
        // Obrigatorio achar a coluna //
        ////////////////////////////////
        if( $col->labelCol==$campo ){
          $temColuna=true;
          ////////////////////////////////////////////////////////////////////////////  
          // Aplicar validacao apenas em campo com vlrDefault = nao se aplica "nsa" //
          ////////////////////////////////////////////////////////////////////////////
          $quebrado= explode("@!",$col->validar);
          
          foreach( $quebrado as $parte ){
            $xpld = explode("=",$parte); 
            

            switch( $xpld[0] ){
              case "alltrim":{
                if( isset($valor) ){
                  $col->vlrDefault  = str_replace(" ", "", $valor);
                  $valor            = $col->vlrDefault;
                };  
                break;
              }
              
              case "minLen":{
                $msgErro = $this->validar_minLen( $campo,$valor,intval($xpld[1]) );                
                if( $msgErro <> "OK" ){
                  $col->erro = $msgErro;
                  $this->numErros++;
                  array_push($this->arrErro,[$col->erro]);
                }  
                break;
              }

              case "maxLen":{
                $msgErro = $this->validar_maxLen( $campo,$valor,intval($xpld[1]) );
                if( $msgErro <> "OK" ){
                  $col->erro = $msgErro;  
                  $this->numErros++;
                  array_push($this->arrErro,[$col->erro]);
                }  
                break;
              }

              case "uppercase":{
                if( isset($valor) ){
                  $col->vlrDefault  = strtoupper($valor);
                  $valor            = $col->vlrDefault;
                }  
                break;
              }    

              case "lowercase":{
                if( isset($valor) ){
                  $col->vlrDefault  = strtolower($valor);
                  $valor            = $col->vlrDefault;
                }  
                break;
              }    
              
              case "removeacentos":{
                if( isset($valor) ){
                  $this->clsRa->montaRetorno($valor);
                  $col->vlrDefault  = $this->clsRa->getNome();
                  $valor            = $col->vlrDefault;
                }  
                break;
              }  
              
              case "digitosValidos":{
                $msgErro = $this->digitos_validos( $campo,$valor,$xpld[1] );                
                if( $msgErro <> "OK" ){
                  $col->erro = $msgErro;
                  $this->numErros++;
                  array_push($this->arrErro,[$col->erro]);
                }  
                break;
              }  

              case "contido":{
                $msgErro = $this->contido( $campo,$valor,$xpld[1] );                
                if( $msgErro <> "OK" ){
                  $col->erro = $msgErro;
                  $this->numErros++;
                  array_push($this->arrErro,[$col->erro]);
                }  
                break;
              }  

              
              case "notnull":{
                $msgErro = $this->not_null( $campo,$valor );                
                if( $msgErro <> "OK" ){
                  $col->erro = $msgErro;
                  $this->numErros++;
                  array_push($this->arrErro,[$col->erro]);
                } else {
                  $col->vlrDefault  = $valor;
                  $valor            = $col->vlrDefault;
                } 
                break;
              }
              
              case "intMaiorZero":{
                $msgErro = $this->int_maiorzero( $campo,$valor );                
                if( $msgErro <> "OK" ){
                  $col->erro = $msgErro;
                  $this->numErros++;
                  array_push($this->arrErro,[$col->erro]);
                } else {
                  $col->vlrDefault  = $valor;
                  $valor            = $col->vlrDefault;
                } 
                break;
              }
              
              case "intMaiorIgualZero":{
                $msgErro = $this->int_maiorigualzero( $campo,$valor );                
                if( $msgErro <> "OK" ){
                  $col->erro = $msgErro;
                  $this->numErros++;
                  array_push($this->arrErro,[$col->erro]);
                } else {
                  $col->vlrDefault  = $valor;
                  $valor            = $col->vlrDefault;
                } 
                break;
              }

              case "dataValida":{
                $dt= explode("/",$valor);
                $valor=$dt[1]."/".$dt[0]."/".$dt[2];
                $col->vlrDefault  = $valor;
                $valor            = $col->vlrDefault;
                break;
              }
              
              case "flo2":{
                $valor=round($valor,2);
                $col->vlrDefault  = $valor;
                $valor            = $col->vlrDefault;
                break;
              }
              
              case "flo4":{
                $valor=round($valor,4);
                $col->vlrDefault  = $valor;
                $valor            = $col->vlrDefault;
                break;
              }
							
              case "flo8":{
                $valor=round($valor,8);
                $col->vlrDefault  = $valor;
                $valor            = $col->vlrDefault;
                break;
              }
              
            }
          }  
          break;
        }
      }
      
      if( $temColuna==false ){
        $col->erro = sprintf(E_STR_COLUNA,$campo); 
        $this->numErros++;
        array_push($this->arrErro,[$col->erro]);
      }
      
    }
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    // Array para retornar a linha atual, este enche um array na funcao chamadora da classe //
    //////////////////////////////////////////////////////////////////////////////////////////  
    function fncLinhaTable(){
      $arr  = [];
      $err  = "OK";
      $tam  = count($this->newJs);
      for( $lin=0;$lin<$tam;$lin++ ){
        $col=$this->newJs[$lin];
        if( $col->grade=="S" ){
          array_push($arr,$col->vlrDefault);
          
          if( $col->erro <> "OK" ){
            $err=$col->erro;  
            array_push($this->arrErro,[$col->erro]);
          };
        };  
      };
      array_push($arr,$err);
      unset($col,$tam);  
      return $arr;
    }
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    // Array para retornar a linha sql para insert                                          //
    //////////////////////////////////////////////////////////////////////////////////////////  
    function fncLinhaInsert(){
      $campo  = [];
      $valor  = [];
      $sql    = "INSERT INTO ".$this->nomeTable."(";
      $tam  = count($this->newJs);
      // Pegando campos e valores
      for( $lin=0;$lin<$tam;$lin++ ){
        $col=$this->newJs[$lin];
        
        array_push($campo,$col->field);
        array_push($valor,$col->vlrDefault);
      };  
      // Adicionando os campos a instrucao sql
      $tam = count($campo);
      $sep = ""; 
      for( $lin=0;$lin<$tam;$lin++ ){
        $sql.=$sep.$campo[$lin];
        $sep=",";
      }  
      // Adicionando os valores a instrucao sql
      $sql.=") VALUES(";
      $sep = ""; 
      for( $lin=0;$lin<$tam;$lin++ ){
        $sql.=$sep."'".$valor[$lin]."'";
        $sep=",";
      }  
      $sql.=")";
      unset($campo,$col,$sep,$tam,$valor);
      return $sql;
    }
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    // Retorna o número de erros para saber se vai executar o insert                        //
    //////////////////////////////////////////////////////////////////////////////////////////  
    function fncNumeroErro(){
      return $this->numErros;
    }  
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    // Retorna o descritivo de cada erro para debugger                                      //
    //////////////////////////////////////////////////////////////////////////////////////////  
    function fncArrErro(){
      return $this->arrErro;
    }  
    //  
    function validar_minLen($parCampo,$parValor,$parLen){
      $strRet = "OK";
      if(isset($parValor) ){
        if( strlen($parValor) < $parLen ){
          $strRet=sprintf(E_VAL_MINLEN,$parLen,$parCampo);
        };
      };
      return $strRet;
    }
    //  
    function validar_maxLen($parCampo,$parValor,$parLen){
      $strRet = "OK";
      if(isset($parValor) ){
        if( strlen($parValor) > $parLen){
          $strRet=sprintf(E_VAL_MAXLEN,$parLen,$parCampo);
        };
      };
      return $strRet;
    }
    //  
    function digitos_validos($parCampo,$parValor,$parDigitos){
      $strRet = "OK";
      if(isset($parValor) ){
        $dig    = explode("|",$parDigitos);
        $tamD   = count($dig);
        $tamV   = strlen($parValor);
        $digito = "";
        for( $linV=0;$linV<$tamV;$linV++ ){
          $digito=substr($parValor,$linV,1);

          $valido=false;
          for( $linD=0;$linD<$tamD;$linD++ ){
            if( $dig[$linD]==$digito ){
              $valido=true;
              break;
            };
          };  
          
          if( $valido==false ){
            $strRet=sprintf(E_STR_DIGITOVALIDO,$digito,$parCampo);  
            break;
          };
        };
      };
      return $strRet;
    }
    //  
    function contido($parCampo,$parValor,$parContido){
      $strRet = "OK";
      if(isset($parValor) ){
        $qtos   = explode("|",$parContido);
        $tamD   = count($qtos);
        $achei=false;
        for( $linV=0;$linV<$tamD;$linV++ ){
          if( $parValor==$qtos[$linV] ){
            $achei=true;
            break;
          };
        };
        if( $achei==false ){
          $strRet=sprintf(E_STR_CONTIDO,$parContido,$parCampo);    
        };        
      };
      return $strRet;
    }
    //  
    function not_null($parCampo,$parValor){
      $strRet = "OK";
      if( strlen($parValor)==0 ){
        $strRet=sprintf(E_STR_NOTNULL,$parCampo);
      };
      return $strRet;
    }
    //  
    function int_maiorzero($parCampo,$parValor){
      $parValor=preg_replace('/[^0-9]/', '', $parValor);
      if( ($parValor=="") or (intval($parValor)<=0) ){
        $strRet=sprintf(E_VAL_INTMAIORZERO,$parCampo);
      } else {
        $strRet = "OK";  
      };  
      return $strRet;
    }  
    //  
    function int_maiorigualzero($parCampo,$parValor){
      $parValor=preg_replace('/[^0-9]/', '', $parValor);
      if( ($parValor=="") or (intval($parValor)<0) ){
        $strRet=sprintf(E_VAL_INTMAIORIGUALZERO,$parCampo);
      } else {
        $strRet = "OK";  
      };  
      return $strRet;
    }  
    
  }
?>