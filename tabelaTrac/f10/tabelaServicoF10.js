////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fServicoF10(opc,codSrv,foco,topo,objeto){        
  if( objeto.fisjur=="F" ){
    var sql="SELECT A.SRV_CODIGO AS CODIGO"
           +"       ,A.SRV_NOME AS DESCRICAO"
           +"       ,CAST('N' AS VARCHAR(1)) AS INSS_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS INSS_ALIQ"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS INSS_BASECALC"
           +"       ,CAST('N' AS VARCHAR(1)) AS IRRF_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS IRRF_ALIQ"
           +"       ,CAST('N' AS VARCHAR(1)) AS PIS_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS PIS_ALIQ"
           +"       ,CAST('N' AS VARCHAR(1)) AS COFINS_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS COFINS_ALIQ"
           +"       ,CAST('N' AS VARCHAR(1)) AS CSLL_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS CSLL_ALIQ"
           +"       ,A.SRV_ISS AS ISS_SN"
           +"       ,A.SRV_CODCC AS CODCC"
           +"       ,SPR.SPR_ALIQUOTA AS ISS_ALIQ"
           +"       ,SPR.SPR_RETIDO AS RETIDO_SN"
           +"  FROM SERVICO A" 
           +"  LEFT OUTER JOIN SERVICOPREFEITURA SPR ON SRV_CODSPR=SPR.SPR_CODIGO";
  };
  else if( objeto.categoria=="SIM" ){
    var sql="SELECT A.SRV_CODIGO AS CODIGO"
           +"       ,A.SRV_NOME AS DESCRICAO"
           +"       ,A.SRV_INSS AS INSS_SN"
           +"       ,A.SRV_INSSALIQ AS INSS_ALIQ"
           +"       ,A.SRV_INSSBASECALC AS INSS_BASECALC"
           +"       ,A.SRV_IRRF AS IRRF_SN"
           +"       ,A.SRV_IRRFALIQ AS IRRF_ALIQ"
           +"       ,CAST('N' AS VARCHAR(1)) AS PIS_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS PIS_ALIQ"
           +"       ,CAST('N' AS VARCHAR(1)) AS COFINS_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS COFINS_ALIQ"
           +"       ,CAST('N' AS VARCHAR(1)) AS CSLL_SN"
           +"       ,CAST(0 AS NUMERIC(15,2)) AS CSLL_ALIQ"
           +"       ,A.SRV_ISS AS ISS_SN"
           +"       ,A.SRV_CODCC AS CODCC"
           +"       ,SPR.SPR_ALIQUOTA AS ISS_ALIQ"
           +"       ,SPR.SPR_RETIDO AS RETIDO_SN"
           +"  FROM SERVICO A" 
           +"  LEFT OUTER JOIN SERVICOPREFEITURA SPR ON SRV_CODSPR=SPR.SPR_CODIGO";
  };
  else{
    var sql="SELECT A.SRV_CODIGO AS CODIGO"
         +"       ,A.SRV_NOME AS DESCRICAO"
         +"       ,A.SRV_INSS AS INSS_SN"
         +"       ,A.SRV_INSSALIQ AS INSS_ALIQ"
         +"       ,A.SRV_INSSBASECALC AS INSS_BASECALC"
         +"       ,A.SRV_IRRF AS IRRF_SN"
         +"       ,A.SRV_IRRFALIQ AS IRRF_ALIQ"
         +"       ,A.SRV_PIS AS PIS_SN"
         +"       ,A.SRV_PISALIQ AS PIS_ALIQ"
         +"       ,A.SRV_COFINS AS COFINS_SN"
         +"       ,A.SRV_COFINSALIQ AS COFINS_ALIQ"
         +"       ,A.SRV_CSLL AS CSLL_SN"
         +"       ,A.SRV_CSLLALIQ AS CSLL_ALIQ"
         +"       ,A.SRV_ISS AS ISS_SN"
         +"       ,A.SRV_CODCC AS CODCC"
         +"       ,SPR.SPR_ALIQUOTA AS ISS_ALIQ"
         +"       ,SPR.SPR_RETIDO AS RETIDO_SN"
         +"  FROM SERVICO A" 
         +"  LEFT OUTER JOIN SERVICOPREFEITURA SPR ON SRV_CODSPR=SPR.SPR_CODIGO";
  };  
  
  if( opc == 0 ){            
    sql+=" WHERE (A.SRV_ATIVO='S')";  
    if( typeof objeto === 'object' ){
      for (var key in objeto) {
        switch( key ){
          case "entsai"       : 
            sql+=" AND (A.SRV_ENTSAI='"+objeto[key]+"')";
            break;
          case "codcdd"       : 
            sql+=" AND (SPR.SPR_CODCDD='"+objeto[key]+"')";
            break;
          case "codemp"       : 
            sql+=" AND (A.SRV_CODEMP="+objeto[key]+")";
            break;
        };  
      };  
    };
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdSrv=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdSrv.Assoc=false;
    bdSrv.select( sql );
    if( bdSrv.retorno=='OK'){
      var jsSrvF10={
        "titulo":[
           {"id":0  ,"labelCol":"OPC"           ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
          ,{"id":1  ,"labelCol":"CODIGO"        ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2  ,"labelCol":"DESCRICAO"     ,"tipo":"edt"  ,"tamGrd":"30em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3  ,"labelCol":"INSS_SN"       ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":4  ,"labelCol":"INSS_ALIQ"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":5  ,"labelCol":"INSS_BASECALC" ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":6  ,"labelCol":"IRRF_SN"       ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":7  ,"labelCol":"IRRF_ALIQ"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":8  ,"labelCol":"PIS_SN"        ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":9  ,"labelCol":"PIS_ALIQ"      ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":10 ,"labelCol":"COFINS_SN"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":11 ,"labelCol":"COFINS_ALIQ"   ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":12 ,"labelCol":"CSLL_SN"       ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":13 ,"labelCol":"CSLL_ALIQ"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":14 ,"labelCol":"ISS_SN"        ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":15 ,"labelCol":"CODCC"         ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":16 ,"labelCol":"ISS_ALIQ"      ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":17 ,"labelCol":"RETIDO_SN"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
        ]
        ,"registros"      : bdSrv.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : "tblSrv"                // Nome da table
        ,"prefixo"        : "srv"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "SERVICO"               // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objSrvF10 === undefined ){          
        objSrvF10         = new clsTable2017("objSrvF10");
        objSrvF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objSrvF10.focoF10=foco;  
        };
      };  
      var html          = objSrvF10.montarHtmlCE2017(jsSrvF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaSrv');
      document.getElementById('tblSrv').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objSrvF10);
    };
  }; 
  if( opc == 1 ){
    sql+=" WHERE (A.SRV_CODIGO='"+document.getElementById(codSrv).value.toUpperCase()+"')"
        +"   AND (A.SRV_ATIVO='S')";
    var bdSrv=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdSrv.Assoc=true;
    bdSrv.select( sql );
    return bdSrv.dados;
  };     
};