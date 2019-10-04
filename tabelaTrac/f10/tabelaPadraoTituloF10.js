////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
////////////////////////////////////////////////////////////////////////////
function fPadraoTituloF10(opc,codPt,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.PT_CODIGO AS CODIGO,A.PT_NOME AS DESCRICAO,A.PT_CODCC AS CODCC"  );
  clsStr.concat("  FROM PADRAOTITULO A"                                                                );
  let tblPt    = "tblPt";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "debcre": 
          clsStr.concat(" {WHERE} (A.PT_DEBCRE='"+objeto[key]+"')",true);
          break;
        case "codpt": 
          clsStr.concat(" {AND} (A.PT_CODIGO='"+objeto[key]+"')",true);
          break;
        case "ativo":
          clsStr.concat( " {AND} (A.PT_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "tbl": 
          tblPt=objeto[key];
          break; 
        case "where"  : 
          clsStr.concat( objeto[key],true);        
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
    var bdPt=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdPt.Assoc=false;
    bdPt.select( sql );
    if( bdPt.retorno=='OK'){
      var jsPtF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"3em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"5em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"29em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"CODCC"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"int"}          
        ]
        ,"registros"      : bdPt.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblPt                  // Nome da table
        ,"prefixo"        : "pt"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "PADRAO"               // Nome da tabela no banco de dados  
        ,"width"          : "50em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objPtF10 === undefined ){          
        objPtF10         = new clsTable2017("objPtF10");
        objPtF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objPtF10.focoF10=foco;  
        };
      };  
      var html          = objPtF10.montarHtmlCE2017(jsPtF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '52em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaPt');
      document.getElementById('tblPt').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objPtF10);
    };
  }; 
  if( opc == 1 ){
    var bdPt=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdPt.Assoc=true;
    bdPt.select( sql );
    return bdPt.dados;
  };     
};