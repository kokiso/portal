////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
////////////////////////////////////////////////////////////////////////////
function fProdutoF10(opc,codPrf,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.PRD_CODIGO AS CODIGO,A.PRD_NOME AS DESCRICAO" );
  clsStr.concat("       ,A.PRD_CODNCMIMP AS CODNCM"                     );  
  clsStr.concat("       ,A.PRD_VLRVENDA AS VLRVENDA"                    );    
  clsStr.concat("       ,A.PRD_CODEMB AS CODEMB"                        );      
  clsStr.concat("  FROM PRODUTO A"                                      );
  let tblPrd      = "tblPrd";
  let tamColNome  = "29em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codemp":
          clsStr.concat( " {WHERE} (A.PRD_CODEMP='"+objeto[key]+"')",true);        
          break;    
        case "ativo":
          clsStr.concat( " {AND} (A.PRD_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "codprd":
          clsStr.concat( " {AND} (A.PRD_CODIGO='"+objeto[key]+"')",true);        
          break;    
        case "tamColNome": 
          tamColNome=objeto[key];    
          break; 
        case "entsai":
          if( objeto[key]=="E" )
            clsStr.concat( " {AND} (A.PRD_ENTRADA='S')",true);          
          if( objeto[key]=="S" )
            clsStr.concat( " {AND} (A.PRD_SAIDA='S')",true);          
          break;
        case "tbl": 
          tblPrd=objeto[key];
          break;           
        case "where": 
          clsStr.concat(objeto[key],true);        
          break;           
      };  
    };  
  };
  sql=clsStr.fim();
  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdPrd=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdPrd.Assoc=false;
    bdPrd.select( sql );
    if( bdPrd.retorno=='OK'){
      var jsPrdF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"        ,"tipo":"chk"  ,"tamGrd":"3em"        ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"     ,"tipo":"edt"  ,"tamGrd":"6em"        ,"fieldType":"str","ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO"  ,"tipo":"edt"  ,"tamGrd":tamColNome   ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"CODNCM"     ,"tipo":"edt"  ,"tamGrd":"0em"        ,"fieldType":"str","ordenaColuna":"N"}          
          ,{"id":4 ,"labelCol":"VLRVENDA"   ,"tipo":"edt"  ,"tamGrd":"0em"        ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":5 ,"labelCol":"CODEMB"     ,"tipo":"edt"  ,"tamGrd":"0em"        ,"fieldType":"flo2","ordenaColuna":"N"}                    
        ]
        ,"registros"      : bdPrd.dados            // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblPrd                 // Nome da table
        ,"prefixo"        : "prd"                  // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "PRODUTO"              // Nome da tabela no banco de dados  
        ,"width"          : "52em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objPrdF10 === undefined ){          
        objPrdF10         = new clsTable2017("objPrdF10");
        objPrdF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objPrdF10.focoF10=foco;  
        };
      };  
      var html          = objPrdF10.montarHtmlCE2017(jsPrdF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaPrd');
      document.getElementById('tblPrd').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objPrdF10);
    };
  }; 
  if( opc == 1 ){
    var bdPrd=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdPrd.Assoc=true;
    bdPrd.select( sql );
    return bdPrd.dados;
  };     
};