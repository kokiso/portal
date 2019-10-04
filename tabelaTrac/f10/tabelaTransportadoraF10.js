////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
////////////////////////////////////////////////////////////////////////////
function fTransportadoraF10(opc,codTrn,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.TRN_CODFVR AS CODIGO,FVR.FVR_NOME AS DESCRICAO"         );
  clsStr.concat("  FROM TRANSPORTADORA A"                                         );
  clsStr.concat("  LEFT OUTER JOIN FAVORECIDO FVR ON A.TRN_CODFVR=FVR.FVR_CODIGO" );
  let tblTrn    = "tblTrn";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "ativo":
          clsStr.concat( " {WHERE} (A.TRN_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "codtrn": 
          clsStr.concat(" {AND} (A.TRN_CODTRN='"+objeto[key]+"')",true);
          break;
        case "tbl": 
          tblTrn=objeto[key];
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
    var bdTrn=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdTrn.Assoc=false;
    bdTrn.select( sql );
    if( bdTrn.retorno=='OK'){
      var jsTrnF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"29em"  ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdTrn.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblTrn                  // Nome da table
        ,"prefixo"        : "trn"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "TRANSPORTADORA"        // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objTrnF10 === undefined ){          
        objTrnF10         = new clsTable2017("objTrnF10");
        objTrnF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objTrnF10.focoF10=foco;  
        };
      };  
      var html          = objTrnF10.montarHtmlCE2017(jsTrnF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaTrn');
      document.getElementById('tblTrn').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objTrnF10);
    };
  }; 
  if( opc == 1 ){
    var bdTrn=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdTrn.Assoc=true;
    bdTrn.select( sql );
    return bdTrn.dados;
  };     
};