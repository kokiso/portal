////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fFabricanteF10(opc,codFbr,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.FBR_CODFVR AS CODIGO,FVR.FVR_APELIDO AS DESCRICAO"      );
  clsStr.concat("  FROM FABRICANTE A"                                             );
  clsStr.concat("  LEFT OUTER JOIN FAVORECIDO FVR ON A.FBR_CODFVR=FVR.FVR_CODIGO" );
  
  let tblFbr="tblFbr";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codgp": 
          clsStr.concat(" {WHERE} (A.FBR_CODGP='"+objeto[key]+"')",true);
          break;
        case "codfvr": 
          clsStr.concat(" {AND} (A.FBR_CODFVR='"+objeto[key]+"')",true);
          break;
        case "ativo":
          clsStr.concat( " {AND} (A.FBR_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "tbl": 
          tblFbr=objeto[key];
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
    var bdFbr=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdFbr.Assoc=false;
    bdFbr.select( sql );
    if( bdFbr.retorno=='OK'){
      var jsFbrF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"30em"  ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdFbr.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblFbr                  // Nome da table
        ,"prefixo"        : "fbr"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "FABRICANTE"            // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objFbrF10 === undefined ){          
        objFbrF10         = new clsTable2017("objFbrF10");
        objFbrF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objFbrF10.focoF10=foco;  
        };
      };  
      var html          = objFbrF10.montarHtmlCE2017(jsFbrF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaFbr');
      document.getElementById('tblFbr').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objFbrF10);
    };
  }; 
  if( opc == 1 ){
    var bdFbr=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdFbr.Assoc=true;
    bdFbr.select( sql );
    return bdFbr.dados;
  };     
};