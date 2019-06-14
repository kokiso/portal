<?php
// Controlador de comunicação e interpretação do rastreador Suntech
// Foi utilizado o ST300 para testes
// Como projeto inicial, todos os eventos independentemente do seu tipo (Status,emergencia,alerta,eveno,movimento,RS232,GSM) é interpretado como um tipo evento @msasEvento
// Discutir necessidade de tabelas de relacionamento para armazenamento dos códigos de evento

class escuta_porta // Classe para manipulação das variaveis do rastreador
{
  //Variáveis do banco de dados pré atribuídas

  public $msasIdpacote = '*';
  public $msasTecnologia = '*';
  public $msasIdveiculos = '*'; 
  public $msasCodvcl = 'NSA0000';
  public $msasCodcln = 0;
  public $msasDatagps = '*';
  public $msasDataServidor = '*';
  public $msasPeriodo = '*';
  public $msasLatitude = '*';
  public $msasLongitude = '*';
  public $msasDirecao = '*';
  public $msasVelocidade = '*';
  public $msasOdometro = '*';
  public $msasHorimetro = '*';
  public $msasTensao = '*';
  public $msasMacro = 0;
  public $msasEvento = '*';
  public $msasTelemetria  = 0; 
  public $msasSaida1 = '*';
  public $msasSaida2 = '*';
  public $msasSaida3 = 0;
  public $msasSaida4 = 0;
  public $msasSaida5 = 0;
  public $msasSaida6 = 0;
  public $msasSaida7 = 0;
  public $msasSaida8 = 0;
  public $msasEntrada1 = '*';
  public $msasEntrada2 = '*';
  public $msasEntrada3 = '*';
  public $msasEntrada4 = 0;
  public $msasEntrada5 = 0;
  public $msasEntrada6 = 0;
  public $msasEntrada7 = 0;
  public $msasEntrada8 = 0;
  public $msasSatelite = '*';
  public $msasIdReferencia = 0;
  public $msasUf = '**';
  public $msasCidade = 'NSA';
  public $msasRua = 'NSA';
  public $msasRpm = '*';
  public $msasTemperatura1 = 0;
  public $msasTemperatura2 = 0;
  public $msasTemperatura3 = 0;
  public $msasPontoEntrada = 0;
  public $msasPontosaida = 0;
  public $msasCodigoMacro = 0;
  public $msasNomeMensagem = '*';
  public $msasConteudoMensagem = '*';
  public $msasTextoMensagem = '*';
  public $msasSoliga = '*';
  public $msasTipoTeclado = 0;
  public $msasIntegradoraId = 0;
  public $msasIdMotorista = '*';
  public $msasNomeMotorista = '*';
  public $msasMemoria   = 0;
  public $msasBloqueio = 0;
  public $msasGps = '*';
  public $msasJaming   = 0;
  public $msasIgnicao   = '*';
  public $msasLimpadorParaBrisa = 0;
  public $msasAlertaAudivel  = 'AA-D';
  public $msasBuzzer = 'BZ-D';
  public $msasBuzzer2 = 'BZ2-D';
  public $msasCapo = 'CPO-D';
  public $msasDesengate = 'DSG-D';
  public $msasSenhaCoacao = 'MC-D';
  public $msasBotaoPanico = 'PNC-D';
  public $msasBauInterno  = 'SBI-D';
  public $msasBauLateral = 'SBL-D';
  public $msasBauTraseiro = 'SBT-D';
  public $msasPortaCarona = 'SPC-D';
  public $msasPortaMotorista = 'SPM-D';
  public $msasSeta  = 'SET-D';    
  public $msasSequenciaBloqueio  = 'SB-D';
  public $msasSirene  = 'SRN-D';
  public $msasTravaBauinterno  = 'TBI-D';
  public $msasTravaBauLateral  = 'TBL-D';
  public $msasTravaBauTraseiro  = 'TBT-D';
  public $msasTanqueCombustivel  = 'TCB-D';
  public $msasTravaQuintaRota  = 'TQR-D';
  public $msasPainel = 'VP-D';

  // Método construtor para atribuição das variáveis
  public function __construct($buf){

    $object = $this->createObject($buf);

    if(isset($object['msasDatagps']) && isset($object['msasPeriodo'])){
      $dataGps = substr_replace(substr_replace($object['msasDatagps'], '-', 4, 0),'-',7,0).' '.$object['msasPeriodo']; // data formatada para o banco de dados,
      $idPacote = $object['msasIdpacote'].$object['msasDatagps'].str_replace(':', '', $object['msasPeriodo']); // Criação do idpacote
    }else{
      $dataGps = '1992-03-13 00:00:00';
      $idpacote = $object['msasIdpacote'].date("Y-m-d H:i:s");
    }

    if (isset($object['msasDirecao'])){
        $direcao = $this->checkDirection($object['msasDirecao']);
    }
    
    $date = date("Y-m-d H:i:s");
    $io  = array_map('intval', str_split($object['msasIo']));
    $this->msasDataServidor = $date;
    switch(true){
    case preg_match_all("/(STT)/", $buf) > 0 : // Em caso de retorno de Status atribui:

      
      $periodo = $this->setPeriodo($object['msasPeriodo']);

      $this->msasIdpacote = $idPacote;
      $this->msasTecnologia = $object['msasTecnologia'];
      $this->msasDatagps = $dataGps;
      $this->msasPeriodo = $periodo;
      $this->msasLatitude = $object['msasLatitude'];
      $this->msasLongitude = $object['msasLongitude'];
      $this->msasVelocidade = $object['msasVelocidade'];
      $this->msasDirecao = $direcao;
      $this->msasSatelite = $object['msasSatelite'];
      $this->msasGps = $object['msasGps'];
      $this->msasOdometro = $object['msasOdometro'];
      $this->msasTensao = $object['msasTensao'];
      $this->msasIgnicao = $object['msasIgnicao'];
      $this->msasEntrada1 = $object['msasEntrada1'];
      $this->msasEntrada2 = $object['msasEntrada2'];
      $this->msasEntrada3 = $object['msasEntrada3'];
      $this->msasSaida1 = $object['msasSaida1'];
      $this->msasSaida2 = $object['msasSaida2'];
      $this->msasEvento = $object['msasEvento'];
      $this->msasHorimetro = $object['msasHorimetro'];
      $this->msasRpm = $object['msasRpm'];
      $this->msasIdMotorista = $object['msasIdMotorista'];

    break;

    case preg_match_all("/(EMG)/", $buf) > 0 : // Em caso de retorno de Emergencia atribui:

      
      $periodo = $this->setPeriodo($object['msasPeriodo']);

      $this->msasIdpacote =  $idPacote;
      $this->msasTecnologia = $object['msasTecnologia'];
      $this->msasDatagps = $dataGps;
      $this->msasPeriodo = $periodo;
      $this->msasLatitude = $object['msasLatitude'];
      $this->msasLongitude = $object['msasLongitude'];
      $this->msasVelocidade = $object['msasVelocidade'];
      $this->msasDirecao = $direcao;
      $this->msasSatelite = $object['msasSatelite'];
      $this->msasGps = $object['msasGps'];
      $this->msasOdometro = $object['msasOdometro'];
      $this->msasTensao = $object['msasTensao'];
      $this->msasIgnicao = $object['msasIgnicao'];
      $this->msasEntrada1 = $object['msasEntrada1'];
      $this->msasEntrada2 = $object['msasEntrada2'];
      $this->msasEntrada3 = $object['msasEntrada3'];
      $this->msasSaida1 = $object['msasSaida1'];
      $this->msasSaida2 = $object['msasSaida2'];
      $this->msasEvento = $object['msasEvento'];
      $this->msasHorimetro = $object['msasHorimetro'];
      $this->msasRpm = $object['msasRpm'];
      $this->msasIdMotorista = $object['msasIdMotorista'];

    break;

    case preg_match_all("/(EVT)/", $buf) > 0 : // Em caso de retorno de Eventos atribui:

      
      $periodo = $this->setPeriodo($object['msasPeriodo']);

      $this->msasIdpacote =  $idPacote;
      $this->msasTecnologia = $object['msasTecnologia'];
      $this->msasDatagps = $dataGps;
      $this->msasPeriodo = $periodo;
      $this->msasLatitude = $object['msasLatitude'];
      $this->msasLongitude = $object['msasLongitude'];
      $this->msasVelocidade = $object['msasVelocidade'];
      $this->msasDirecao = $direcao;
      $this->msasSatelite = $object['msasSatelite'];
      $this->msasGps = $object['msasGps'];
      $this->msasOdometro = $object['msasOdometro'];
      $this->msasTensao = $object['msasTensao'];
      $this->msasIgnicao = $object['msasIgnicao'];
      $this->msasEntrada1 = $object['msasEntrada1'];
      $this->msasEntrada2 = $object['msasEntrada2'];
      $this->msasEntrada3 = $object['msasEntrada3'];
      $this->msasSaida1 = $object['msasSaida1'];
      $this->msasSaida2 = $object['msasSaida2'];
      $this->msasEvento = $object['msasEvento'];
      $this->msasHorimetro = $object['msasHorimetro'];
      $this->msasRpm = $object['msasRpm'];
      $this->msasIdMotorista = $object['msasIdMotorista'];              
    break;

    case preg_match_all("/(ALT)/", $buf) > 0 :  // Em caso de retorno de Alerta atribui:

      
      $periodo = $this->setPeriodo($object['msasPeriodo']);

      $this->msasIdpacote =  $idPacote;
      $this->msasTecnologia = $object['msasTecnologia'];
      $this->msasDatagps = $dataGps;
      $this->msasPeriodo = $periodo;
      $this->msasLatitude = $object['msasLatitude'];
      $this->msasLongitude = $object['msasLongitude'];
      $this->msasVelocidade = $object['msasVelocidade'];
      $this->msasDirecao = $direcao;
      $this->msasSatelite = $object['msasSatelite'];
      $this->msasGps = $object['msasGps'];
      $this->msasOdometro = $object['msasOdometro'];
      $this->msasTensao = $object['msasTensao'];
      $this->msasIgnicao = $object['msasIgnicao'];
      $this->msasEntrada1 = $object['msasEntrada1'];
      $this->msasEntrada2 = $object['msasEntrada2'];
      $this->msasEntrada3 = $object['msasEntrada3'];
      $this->msasSaida1 = $object['msasSaida1'];
      $this->msasSaida2 = $object['msasSaida2'];
      $this->msasEvento = $object['msasEvento'];
      $this->msasHorimetro = $object['msasHorimetro'];
      $this->msasRpm = $object['msasRpm'];
      $this->msasIdMotorista = $object['msasIdMotorista'];
    break;

    case preg_match_all("/(HTE)/", $buf) > 0 :  // Em caso de retorno de movimentação atribui:

      

      $this->msasIdpacote =  $idPacote;
      $this->msasTecnologia = $object['msasTecnologia'];
      $this->msasDatagps = $dataGps;
      $this->msasPeriodo = $periodo;
      $this->msasOdometro = $answer['msasOdometro'];
      $this->msasTensao = $object['msasLatitude'];
      $this->msasHorimetro = $object['msasLongitude'];


    break;

    case preg_match_all("/(ALV)/", $buf) > 0 :  // Em caso de retorno de GPS atribui:

      $this->msasIdpacote =  $idPacote;

    break;

    case preg_match_all("/(UEX)/", $buf) > 0 : // Em caso de retorno de RS232 UEX atribui:

      
      $periodo = $this->setPeriodo($object['msasPeriodo']);
      
      $this->msasIdpacote =  $idPacote;
      $this->msasTecnologia = $object['msasTecnologia'];
      $this->msasDatagps = $dataGps;
      $this->msasPeriodo = $periodo;
      $this->msasLatitude = $object['msasLatitude'];
      $this->msasLongitude = $object['msasLongitude'];
      $this->msasVelocidade = $object['msasVelocidade'];
      $this->msasDirecao = $direcao;
      $this->msasSatelite = $object['msasSatelite'];
      $this->msasGps = $object['msasGps'];
      $this->msasOdometro = $object['msasOdometro'];
      $this->msasTensao = $object['msasTensao'];
      $this->msasIgnicao = $object['msasIgnicao'];
      $this->msasEntrada1 = $object['msasEntrada1'];
      $this->msasEntrada2 = $object['msasEntrada2'];
      $this->msasEntrada3 = $object['msasEntrada3'];
      $this->msasSaida1 = $object['msasSaida1'];
      $this->msasSaida2 = $object['msasSaida2'];

    break;

    case preg_match_all("/(DEX)/", $buf) > 0 : // Em caso de retorno de RS232 atribui:
      $this->msasIdpacote =  $idPacote;
      $this->msasTecnologia = $object['msasTecnologia'];
      $this->msasEvento = $answer['msasEvento'];
    break;

    }; // fim do switch
    
  } // fim do construct

  // Função que gera query de insert na view do banco de dados
    // Função que gera query de insert na view do banco de dados
  public function setSqlInsert(){
    $err = $this->checkAuthenticityVariable();

    if ($err !== 'ok'){
      return $err;
    }

    $sql="INSERT INTO VMOVIMENTOSASCAR(".PHP_EOL;
    $sql.="MSAS_IDPACOTE".PHP_EOL; 
    $sql.=",MSAS_TECNOLOGIA".PHP_EOL;
    $sql.=",MSAS_IDVEICULO".PHP_EOL; 
    $sql.=",MSAS_CODVCL".PHP_EOL;
    $sql.=",MSAS_CODCLN".PHP_EOL;
    $sql.=",MSAS_DATAGPS".PHP_EOL;
    $sql.=",MSAS_DATASERVIDOR".PHP_EOL;
    $sql.=",MSAS_PERIODO".PHP_EOL;
    $sql.=",MSAS_LATITUDE".PHP_EOL;
    $sql.=",MSAS_LONGITUDE".PHP_EOL;
    $sql.=",MSAS_DIRECAO".PHP_EOL;
    $sql.=",MSAS_VELOCIDADE".PHP_EOL;
    $sql.=",MSAS_ODOMETRO".PHP_EOL;
    $sql.=",MSAS_HORIMETRO".PHP_EOL;
    $sql.=",MSAS_TENSAO".PHP_EOL;
    $sql.=",MSAS_MACRO".PHP_EOL;
    $sql.=",MSAS_EVENTO".PHP_EOL;
    $sql.=",MSAS_TELEMETRIA".PHP_EOL;
    $sql.=",MSAS_SAIDA1".PHP_EOL;
    $sql.=",MSAS_SAIDA2".PHP_EOL;
    $sql.=",MSAS_SAIDA3".PHP_EOL;
    $sql.=",MSAS_SAIDA4".PHP_EOL;
    $sql.=",MSAS_SAIDA5".PHP_EOL;
    $sql.=",MSAS_SAIDA6".PHP_EOL;
    $sql.=",MSAS_SAIDA7".PHP_EOL;
    $sql.=",MSAS_SAIDA8 ".PHP_EOL;
    $sql.=",MSAS_ENTRADA1".PHP_EOL;
    $sql.=",MSAS_ENTRADA2".PHP_EOL;
    $sql.=",MSAS_ENTRADA3".PHP_EOL;
    $sql.=",MSAS_ENTRADA4".PHP_EOL;
    $sql.=",MSAS_ENTRADA5".PHP_EOL;
    $sql.=",MSAS_ENTRADA6".PHP_EOL;
    $sql.=",MSAS_ENTRADA7".PHP_EOL;
    $sql.=",MSAS_ENTRADA8".PHP_EOL;
    $sql.=",MSAS_SATELITE".PHP_EOL;
    $sql.=",MSAS_IDREFERENCIA".PHP_EOL;
    $sql.=",MSAS_UF".PHP_EOL;
    $sql.=",MSAS_CIDADE".PHP_EOL;
    $sql.=",MSAS_RUA".PHP_EOL;
    $sql.=",MSAS_RPM".PHP_EOL;
    $sql.=",MSAS_TEMPERATURA1".PHP_EOL;
    $sql.=",MSAS_TEMPERATURA2".PHP_EOL;
    $sql.=",MSAS_TEMPERATURA3".PHP_EOL;
    $sql.=",MSAS_PONTOENTRADA".PHP_EOL;
    $sql.=",MSAS_PONTOSAIDA".PHP_EOL;
    $sql.=",MSAS_CODIGOMACRO".PHP_EOL;
    $sql.=",MSAS_NOMEMENSAGEM".PHP_EOL;
    $sql.=",MSAS_CONTEUDOMENSAGEM".PHP_EOL;
    $sql.=",MSAS_TEXTOMENSAGEM".PHP_EOL;
    $sql.=",MSAS_SOLIGA".PHP_EOL;
    $sql.=",MSAS_TIPOTECLADO".PHP_EOL;
    $sql.=",MSAS_INTEGRADORAID".PHP_EOL;
    $sql.=",MSAS_IDMOTORISTA".PHP_EOL;
    $sql.=",MSAS_NOMEMOTORISTA".PHP_EOL;
    $sql.=",MSAS_MEMORIA".PHP_EOL;
    $sql.=",MSAS_BLOQUEIO".PHP_EOL;
    $sql.=",MSAS_GPS".PHP_EOL;
    $sql.=",MSAS_JAMMING".PHP_EOL;
    $sql.=",MSAS_IGNICAO".PHP_EOL;
    $sql.=",MSAS_LIMPADORPARABRISA".PHP_EOL;
    $sql.=",MSAS_ALERTAAUDITIVEL".PHP_EOL;
    $sql.=",MSAS_BUZZER".PHP_EOL;
    $sql.=",MSAS_BUZZER2".PHP_EOL;
    $sql.=",MSAS_CAPO".PHP_EOL;
    $sql.=",MSAS_DESENGATE".PHP_EOL;
    $sql.=",MSAS_SENHACOACAO".PHP_EOL;
    $sql.=",MSAS_BOTAOPANICO".PHP_EOL;
    $sql.=",MSAS_BAUINTERNO".PHP_EOL;
    $sql.=",MSAS_BAULATERAL".PHP_EOL;
    $sql.=",MSAS_BAUTRASEIRO".PHP_EOL;
    $sql.=",MSAS_PORTACARONA".PHP_EOL;
    $sql.=",MSAS_PORTAMOTORISTA".PHP_EOL;
    $sql.=",MSAS_SETA".PHP_EOL;
    $sql.=",MSAS_SEQUENCIABLOQUEIO".PHP_EOL;
    $sql.=",MSAS_SIRENE".PHP_EOL;
    $sql.=",MSAS_TRAVABAUINTERNO".PHP_EOL;
    $sql.=",MSAS_TRAVABAULATERAL".PHP_EOL;
    $sql.=",MSAS_TRAVABAUTRASEIRO".PHP_EOL;
    $sql.=",MSAS_TANQUECOMBUSTIVEL".PHP_EOL;
    $sql.=",MSAS_TRAVAQUINTAROTA".PHP_EOL;
    $sql.=",MSAS_PAINEL)".PHP_EOL; 
    $sql.="VALUES(".PHP_EOL;
    $sql.=$this->msasIdpacote.PHP_EOL;
    $sql.=",'".$this->msasTecnologia."'".PHP_EOL;           //-- TECNOLOGIA
    $sql.=",'".$this->msasIdveiculos."'".PHP_EOL;           //-- IDVEICULO 
    $sql.=",'".$this->msasCodvcl."'".PHP_EOL;               //-- CODVCL
    $sql.=",".$this->msasCodcln."".PHP_EOL;               //-- CODCLN
    $sql.=",'".$this->msasDatagps."'".PHP_EOL;              //-- DATAGPS
    $sql.=",'".$this->msasDataServidor."'".PHP_EOL;         //-- DATASERVIDOR
    $sql.=",'".$this->msasPeriodo."'".PHP_EOL;              //-- PERIODO
    $sql.=",".$this->msasLatitude."".PHP_EOL;             //-- LATITUDE
    $sql.=",".$this->msasLongitude."".PHP_EOL;            //-- LONGITUDE
    $sql.=",".$this->msasDirecao."".PHP_EOL;              //-- DIRECAO
    $sql.=",".$this->msasVelocidade."".PHP_EOL;           //-- VELOCIDADE
    $sql.=",".$this->msasOdometro."".PHP_EOL;             //-- ODOMETRO
    $sql.=",".$this->msasHorimetro."".PHP_EOL;            //-- HORIMETRO
    $sql.=",".$this->msasTensao."".PHP_EOL;               //-- TENSAO
    $sql.=",".$this->msasMacro."".PHP_EOL;                //-- MACRO
    $sql.=",".$this->msasEvento."".PHP_EOL;               //-- EVENTO
    $sql.=",".$this->msasTelemetria."".PHP_EOL;           //-- TELEMETRIA
    $sql.=",".$this->msasSaida1."".PHP_EOL;               //-- SAIDA1
    $sql.=",".$this->msasSaida2."".PHP_EOL;               //-- SAIDA2
    $sql.=",".$this->msasSaida3."".PHP_EOL;               //-- SAIDA3
    $sql.=",".$this->msasSaida4."".PHP_EOL;               //-- SAIDA4
    $sql.=",".$this->msasSaida5."".PHP_EOL;               //-- SAIDA5
    $sql.=",".$this->msasSaida6."".PHP_EOL;               //-- SAIDA6
    $sql.=",".$this->msasSaida7."".PHP_EOL;               //-- SAIDA7
    $sql.=",".$this->msasSaida8."".PHP_EOL;               //-- SAIDA8 
    $sql.=",".$this->msasEntrada1."".PHP_EOL;             //-- ENTRADA1
    $sql.=",".$this->msasEntrada2."".PHP_EOL;             //-- ENTRADA2
    $sql.=",".$this->msasEntrada3."".PHP_EOL;             //-- ENTRADA3
    $sql.=",".$this->msasEntrada4."".PHP_EOL;             //-- ENTRADA4
    $sql.=",".$this->msasEntrada5."".PHP_EOL;             //-- ENTRADA5
    $sql.=",".$this->msasEntrada6."".PHP_EOL;             //-- ENTRADA6
    $sql.=",".$this->msasEntrada7."".PHP_EOL;             //-- ENTRADA7
    $sql.=",".$this->msasEntrada8."".PHP_EOL;             //-- ENTRADA8
    $sql.=",".$this->msasSatelite."".PHP_EOL;             //-- SATELITE
    $sql.=",".$this->msasIdReferencia."".PHP_EOL;         //-- IDREFERENCIA
    $sql.=",'".$this->msasUf."'".PHP_EOL;                   //-- UF
    $sql.=",'".$this->msasCidade."'".PHP_EOL;               //-- CIDADE
    $sql.=",'".$this->msasRua."'".PHP_EOL;                  //-- RUA
    $sql.=",".$this->msasRpm."".PHP_EOL;                  //-- RPM
    $sql.=",".$this->msasTemperatura1."".PHP_EOL;         //-- TEMPERATURA1
    $sql.=",".$this->msasTemperatura2."".PHP_EOL;         //-- TEMPERATURA2
    $sql.=",".$this->msasTemperatura3."".PHP_EOL;         //-- TEMPERATURA3
    $sql.=",".$this->msasPontoEntrada."".PHP_EOL;         //-- PONTOENTRADA
    $sql.=",".$this->msasPontosaida."".PHP_EOL;           //-- PONTOSAIDA
    $sql.=",".$this->msasCodigoMacro."".PHP_EOL;          //-- CODIGOMACRO
    $sql.=",'".$this->msasNomeMensagem."'".PHP_EOL;         //-- NOMEMENSAGEM
    $sql.=",'".$this->msasConteudoMensagem."'".PHP_EOL;     //-- CONTEUDOMENSAGEM
    $sql.=",'".$this->msasTextoMensagem."'".PHP_EOL;        //-- TEXTOMENSAGEM
    $sql.=",'".$this->msasSoliga."'".PHP_EOL;               //-- SOLIGA
    $sql.=",".$this->msasTipoTeclado."".PHP_EOL;          //-- TIPOTECLADO
    $sql.=",".$this->msasIntegradoraId."".PHP_EOL;        //-- INTEGRADORAID
    $sql.=",'".$this->msasIdMotorista."'".PHP_EOL;          //-- IDMOTORISTA
    $sql.=",'".$this->msasNomeMotorista."'".PHP_EOL;        //-- NOMEMOTORISTA
    $sql.=",".$this->msasMemoria."".PHP_EOL;              //-- MEMORIA
    $sql.=",".$this->msasBloqueio."".PHP_EOL;             //-- BLOQUEIO
    $sql.=",".$this->msasGps."".PHP_EOL;                  //-- GPS
    $sql.=",".$this->msasJaming."".PHP_EOL;               //-- JAMMING
    $sql.=",".$this->msasIgnicao."".PHP_EOL;              //-- IGNICAO
    $sql.=",".$this->msasLimpadorParaBrisa."".PHP_EOL;    //-- LIMPADORPARABRISA
    $sql.=",'".$this->msasAlertaAudivel."'".PHP_EOL;        //-- ALERTAAUDITIVEL
    $sql.=",'".$this->msasBuzzer."'".PHP_EOL;               //-- BUZZER
    $sql.=",'".$this->msasBuzzer2."'".PHP_EOL;              //-- BUZZER2
    $sql.=",'".$this->msasCapo."'".PHP_EOL;                 //-- CAPO
    $sql.=",'".$this->msasDesengate."'".PHP_EOL;            //-- DESENGATE
    $sql.=",'".$this->msasSenhaCoacao."'".PHP_EOL;          //-- SENHACOACAO
    $sql.=",'".$this->msasBotaoPanico."'".PHP_EOL;          //-- BOTAOPANICO
    $sql.=",'".$this->msasBauInterno."'".PHP_EOL;           //-- BAUINTERNO
    $sql.=",'".$this->msasBauLateral."'".PHP_EOL;           //-- BAULATERAL
    $sql.=",'".$this->msasBauTraseiro."'".PHP_EOL;          //-- BAUTRASEIRO
    $sql.=",'".$this->msasPortaCarona."'".PHP_EOL;          //-- PORTACARONA
    $sql.=",'".$this->msasPortaMotorista."'".PHP_EOL;       //-- PORTAMOTORISTA
    $sql.=",'".$this->msasSeta."'".PHP_EOL;                 //-- SETA
    $sql.=",'".$this->msasSequenciaBloqueio."'".PHP_EOL;    //-- SEQUENCIABLOQUEIO
    $sql.=",'".$this->msasSirene."'".PHP_EOL;               //-- SIRENE
    $sql.=",'".$this->msasTravaBauinterno."'".PHP_EOL;      //-- TRAVABAUINTERNO
    $sql.=",'".$this->msasTravaBauLateral."'".PHP_EOL;      //-- TRAVABAULATERAL
    $sql.=",'".$this->msasTravaBauTraseiro."'".PHP_EOL;     //-- TRAVABAUTRASEIRO
    $sql.=",'".$this->msasTanqueCombustivel."'".PHP_EOL;    //-- TANQUECOMBUSTIVEL
    $sql.=",'".$this->msasTravaQuintaRota."'".PHP_EOL;      //-- TRAVAQUINTAROTA
    $sql.=",'".$this->msasPainel."'";                          //-- PAINEL
    $sql.=")";

    return $sql; 
  } // fim da função setSqlInsert

  private function setPeriodo($horario){
    $periodo = '*';
    $periodoArray = explode(':', $horario);
    switch( $periodoArray[0] ){
        case "06":
        case "07":
        case "08":
        case "09":
        case "10":
        case "11":
        case "12":
        case "13":
            $periodo="'M'";
        break;
        case "14":
        case "15":
        case "16":
        case "17":
        case "18":
        case "19":
        case "20":
        case "21":
            $periodo="'T'";
        break;
        case "22":
        case "23":
        case "00":
        case "01":
        case "02":
        case "03":                  
        case "04":
        case "05":
            $periodo="'N'";
        break;
    };
    return $periodo;
  } // fim da função setPeriodo

  private function tirarAcentos($str){
    return preg_replace('{\W}', '', preg_replace('{ +}', '_', strtr(
        utf8_decode(html_entity_decode($str)),
        utf8_decode('ÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ'),
        'AAAAEEIOOOUUCNaaaaeeiooouucn')));
  } // fim tiraracentos()

  private function checkDirection($var){
    $direção = -1;

    if (($var >= 0 && $var<= 22.5) || ($var >= 337.6 && $var <= 360)){
      $direcao = 0; //NORTE
    }
    if ($var >= 22.6 && $var <= 67.5){
      $direcao = 1; //NORDESTE
    }
    if ($var >= 67.6 && $var <= 112.5){
      $direcao = 2; //LESTE
    }
    if ($var >= 112.6 && $var <= 157.5){
      $direcao = 3; //SUDESTE
    }
    if ($var >= 157.6 && $var <= 202.5){
      $direcao = 4; //SUL
    }
    if ($var >= 202.6 && $var <= 247.5){
      $direcao = 5; //SUDOESTE
    }
    if ($var >= 247.6 && $var <= 292.5){
      $direcao = 6; //OESTE
    }
    if ($var >= 292.6 && $var <= 337.5){
      $direcao = 7; //NOROESTE
    }
    return $direcao;

  }

  private function checkAuthenticityVariable(){
    $err = 'ok';

    if ( $this->msasTecnologia == '*'){
      $err = 'Tecnologia não encontrada';
    }
    if ( strlen($this->msasDatagps) !== 19){
      $err = 'GPS não Encontrado';
    }
    if (($this->msasLatitude == 0.00 || $this->msasLongitude == 0.00)|| ($this->msasLatitude ==='*' || $this->msasLongitude=== '*')){
      $err = 'Latitude e/ou Longitude ão encontrado';
    }
    if($this->msasPeriodo === '*'){
      $err = 'Periodo não encontrado';
    }
    if($this->msasVelocidade === '*'){
      $err = 'Velocidade não encontrada';
    }
    if($this->msasDirecao === '*'){
      $err = 'Direção não encontrada';
    }
    if($this->msasSatelite === '*'){
      $err = 'Satelite não encontrado';
    }
    if($this->msasGps === '*'){
      $err = 'Gps não encontrado';
    }
    if($this->msasOdometro === '*'){
      $err = 'Odometro não encontrado';
    }
    if($this->msasTensao === '*'){
      $err = 'tensao não encontrado';
    }
    if($this->msasIgnicao === '*'){
      $err = 'Ignicao não encontrado';
    }
    if($this->msasEntrada1=== '*'){
      $err = 'Entrda1 não encontrado';
    }
    if($this->msasEntrada2 === '*'){
      $err = 'Entrada2 não encontrado';
    }
    if($this->msasEntrada3 === '*'){
      $err = 'Entrada3 não encontrado';
    }
    if($this->msasSaida1 === '*'){
      $err = 'Saida1 não encontrado';
    }
    if($this->msasSaida2 === '*'){
      $err = 'Saida2 não encontrado';
    }
    if($this->msasEvento === '*'){
      $err = 'Evento não encontrado';
    }
    if($this->msasHorimetro=== '*'){
      $err = 'Horimetro não encontrado';
    }
    if($this->msasRpm=== '*'){
      $err = 'RPM não encontrado';
    }

    return $err;
  }

  private function createObject($buf){
    $array = array();
    $answer = explode (';',$buf);
    $i = 0;
    if(preg_match_all("/(STT)/", $buf) > 0){
      $key =  array(
                  'header',
                  'msasIdpacote',
                  'msasTecnologia',
                  'software',
                  'msasDatagps',
                  'msasPeriodo',
                  'location_id',
                  'msasLatitude',
                  'msasLongitude',
                  'msasVelocidade',
                  'msasDirecao',
                  'msasSatelite',
                  'msasGps',
                  'msasOdometro',
                  'msasTensao',
                  'msasIo',
                  'msasEvento',
                  'msgNumber',
                  'msasHorimetro',
                  'battery',
                  'msgType',
                  'msasRpm',
                  'msasIdMotorista',
                  'countryCode'
              );
      for ($i=0; $i< count($key); $i++){
        if( preg_match_all("/(msas)/", $key[$i]) > 0){
          $array[$key] = $answer; 
        }
      }
    }
    else{
      $key =  array(
                  'header',
                  'msasIdpacote',
                  'msasTecnologia',
                  'software',
                  'msasDatagps',
                  'msasPeriodo',
                  'location_id',
                  'msasLatitude',
                  'msasLongitude',
                  'msasVelocidade',
                  'msasDirecao',
                  'msasSatelite',
                  'msasGps',
                  'msasOdometro',
                  'msasTensao',
                  'msasIo',
                  'msasEvento',
                  'msasHorimetro',
                  'battery',
                  'msgType',
                  'msasRpm',
                  'msasIdMotorista',
                  'countryCode'
              );

      for ($i=0; $i < count($key); $i++){
        if( preg_match_all("/(msas)/", $key[$i]) > 0){
          $array[$key[$i]] = $answer[$i];
        }
      }

    }
    
    if (isset($array['msasIo'])){
      $io  = array_map('intval', str_split($array['msasIo']));
      $ioArray = array('msasIgnicao','msasEntrada1','msasEntrada2','msasEntrada3','msasSaida1','msasSaida2');

      for($i = 0; $i < count($ioArray); $i++){
        $array[$ioArray[$i]] = $io[$i];
      }
    }
    unset($array['msasIo']);  
    return $array;
  }   

} // fim da classe

///////////////////////////////////////////////////////////////
//Este eh o site que peguei o modelo 
//https://pt.stackoverflow.com/questions/249910/php-com-sockets
///////////////////////////////////////////////////////////////
error_reporting(E_ALL);
///////////////////////////////////////////////////////
// Permite que o script fique por aí esperando conexões
///////////////////////////////////////////////////////
set_time_limit(0);
/////////////////////////////////////////////////////////////////////////////////////////
// Ativa o fluxo de saída implícito para ver o que estamos recebendo à medida que ele vem
/////////////////////////////////////////////////////////////////////////////////////////
ob_implicit_flush();

/////////////////////////////////////////////////////////////////////////////////////////
// Variáveis de UP e porta do rastreador
/////////////////////////////////////////////////////////////////////////////////////////
$address  = '192.168.1.45';
$port     = 15008;
$continua = true;

/////////////////////////////////////////////////////////////////////////////////////////
// Variáveis do banco pré definidas para inserção na view
/////////////////////////////////////////////////////////////////////////////////////////

date_default_timezone_set('America/Sao_Paulo');   


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

/////////////////////////////////
// Parametro de conexão BD
/////////////////////////////////
$insert   = array();
$params   = array("Database" => "angelo", "UID" => "kokiso", "PWD" => "admin");
$ip       = "127.0.0.1";

$conn = sqlsrv_connect( $ip,$params );    
if( !$conn ) {
  echo "ERRO - FALHA AO TENTAR CONECTAR COM BANCO DE DADOS!"."\r\n";  
  echo print_r( sqlsrv_errors(), true);
  die( print_r( sqlsrv_errors(), true));
  $continua=false;
};
if( $continua ){
  do {
    if (($msgsock = socket_accept($sock)) === false) {
      echo "socket_accept() falhou: razao: " . socket_strerror(socket_last_error($sock)) . "\n";
      break;
    }

    do {

      if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
        echo "socket_read() falhou: razao: " . socket_strerror(socket_last_error($msgsock)) . "\n";
        break 2;
      }
      if (!$buf = trim($buf)) {
        continue;
      }
      else if( is_null($buf)){
         continue;
      }
      else{
        break;
      }

      $answer = explode (';',$buf);
      $gateway = new escuta_porta($buf); // classe
      $sql = $gateway->setSqlInsert(); // método da classe

      if (preg_match_all("/INSERT INTO VMOVIMENTOSASCAR/", $buf) === 0){
        echo $sql;
      }
      else{
        //echo $sql;die();
        $conn = sqlsrv_connect( $ip,$params );
        if ( sqlsrv_begin_transaction( $conn ) === false ) {
             die( print_r( sqlsrv_errors(), true ));
        }

        $query = sqlsrv_query($conn, $sql, $insert);
        if( $query === false ) {
            if( ($errors = sqlsrv_errors() ) != 'NULL') {
                foreach( $errors as $error ) {
                    echo "SQLSTATE: ".$error[ 'SQLSTATE']."".PHP_EOL;
                    echo "code: ".$error[ 'code']."".PHP_EOL; 
                    echo "message: ".$error[ 'message']."".PHP_EOL;
                    return;
                }
            }
        }
        sqlsrv_commit($conn);
        sqlsrv_close( $conn );
        echo 'inserted';
      }     
    } while (true);
    socket_close($msgsock);
  } while (true);
 }
socket_close($sock);
?>  