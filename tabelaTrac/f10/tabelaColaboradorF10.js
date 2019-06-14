////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fColaboradorF10(opc,codCol,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.PEI_CODFVR AS CODIGO");
  clsStr.concat("       ,A.PEI_CODPE AS PE");
  clsStr.concat("       ,DES.FVR_NOME AS COLABORADOR");
  clsStr.concat("       ,CASE WHEN A.PEI_STATUS='BOM' THEN CAST('BOM' AS VARCHAR(3))");
  clsStr.concat("             WHEN A.PEI_STATUS='OTI' THEN CAST('OTIMO' AS VARCHAR(5))");          
  clsStr.concat("             WHEN A.PEI_STATUS='RAZ' THEN CAST('RAZOAVEL' AS VARCHAR(8))");
  clsStr.concat("             WHEN A.PEI_STATUS='RUI' THEN CAST('RUIM' AS VARCHAR(4))");
  clsStr.concat("             WHEN A.PEI_STATUS='NSA' THEN CAST('...' AS VARCHAR(3)) END AS STATUS");
  clsStr.concat("       ,DES.FVR_FONE AS FONE");
  clsStr.concat("       ,(dbo.fun_CalcDistancia(ORI.CNTE_LATITUDE,ORI.CNTE_LONGITUDE,DES.FVR_LATITUDE,DES.FVR_LONGITUDE)/1000) AS DISTANCIA");
  clsStr.concat("       ,DES.FVR_APELIDO AS APELIDO");  
  clsStr.concat("  FROM PONTOESTOQUEIND A");
  clsStr.concat("  LEFT OUTER JOIN CONTRATOENDERECO ORI ON ORI.CNTE_CODIGO=[codins]");
  clsStr.concat("  LEFT OUTER JOIN FAVORECIDO DES ON A.PEI_CODFVR=DES.FVR_CODIGO");
  clsStr.concat(" WHERE A.PEI_CODPE IN('CRD','INS','TRC')");
  clsStr.concat("   AND A.PEI_ATIVO='S'");
  //clsStr.concat(" ORDER BY 5");
  let tblCol     = "tblCol";
  let divWidth  = "54em";
  let tblWidth  = "52em";
  let codins    = "";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codins":         
          codins=objeto[key];
          break;
        case "divWidth": 
          divWidth=objeto[key];
          break;
        case "tbl": 
          tblCol=objeto[key];
          break;           
        case "tblWidth": 
          tblWidth=objeto[key];
          break;
        case "where"  : 
          clsStr.concat( objeto[key],true);        
          break;           
      };  
    };  
  };
  sql=clsStr.fim();
  sql=sql.replaceAll("[codins]",codins);
  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdCol=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdCol.Assoc=false;
    bdCol.select( sql );
    if( bdCol.retorno=='OK'){
      var jsColF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"          ,"tipo":"chk"  ,"tamGrd":"3em"  ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"       ,"tipo":"edt"  ,"tamGrd":"5em"  ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"PE"           ,"tipo":"edt"  ,"tamGrd":"3em"  ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":3 ,"labelCol":"COLABORADOR"  ,"tipo":"edt"  ,"tamGrd":"33em" ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":4 ,"labelCol":"STATUS"       
                    ,"funcCor": "switch (objCell.innerHTML) { case 'OTIMO':objCell.classList.add('corVerde');break; case 'BOM':objCell.classList.add('corAzul');break; case 'RAZOAVEL':objCell.classList.add('corTitulo');break; case 'RUIM':objCell.classList.add('corAlterado');break; default:objCell.classList.remove('corAlterado');break;};"
                    ,"tipo":"edt"  
                    ,"tamGrd":"8em"  
                    ,"fieldType":"str"
                    ,"ordenaColuna":"N"}          
          ,{"id":5 ,"labelCol":"FONE"         ,"tipo":"edt"  ,"tamGrd":"0em"  ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":6 ,"labelCol":"DISTANCIA"    ,"tipo":"edt"  ,"tamGrd":"6em"  ,"fieldType":"int","ordenaColuna":"S"}
          ,{"id":7 ,"labelCol":"APELIDO"      ,"tipo":"edt"  ,"tamGrd":"0em"  ,"fieldType":"str","ordenaColuna":"N"}          
        ]
        ,"registros"      : bdCol.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblCol                  // Nome da table
        ,"prefixo"        : "col"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "PRODUTOESTOQUEIND"     // Nome da tabela no banco de dados  
        ,"width"          : tblWidth                // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "*"           // Indice inicial da table
        ,"tamBotao"       : "20"
      };
      if( objColF10 === undefined ){          
        objColF10         = new clsTable2017("objColF10");
        objColF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objColF10.focoF10=foco;  
        };
      };  
      var html          = objColF10.montarHtmlCE2017(jsColF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = divWidth;
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaCol');
      document.getElementById('tblCol').rows[0].cells[6].click();
      //delete(ajudaF10);
      //delete(objColF10);
    };
  }; 
  if( opc == 1 ){
    var bdCol=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdCol.Assoc=true;
    bdCol.select( sql );
    return bdCol.dados;
  };     
};