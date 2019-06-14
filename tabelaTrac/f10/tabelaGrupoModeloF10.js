////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fGrupoModeloF10(opc,codGm,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.GM_CODIGO AS CODIGO,A.GM_NOME AS DESCRICAO"                 );
  clsStr.concat("       ,A.GM_VALORVISTA AS VALORVISTA,A.GM_VALORPRAZO AS VALORPRAZO" );
  clsStr.concat("       ,A.GM_VALORMINIMO AS VALORMINIMO"                             );
  clsStr.concat("       ,A.GM_CODGP AS CODGP"                                         );  
  clsStr.concat("  FROM GRUPOMODELO A"                                                );
  
  let tblGm      = "tblGm";
  let tamColNome  = "30em";
  if( typeof objeto === 'object' ){
    console.log(objeto);
    for (var key in objeto) {
      console.log(key);
      switch( key ){
        case "ativo":
          clsStr.concat( " {AND} (A.GM_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "codgm":
          clsStr.concat( " {WHERE} (A.GM_CODIGO='"+objeto[key]+"')",true);        
          break;    
        case "tamColNome": 
          tamColNome=objeto[key];    
          break;                   
        case "tbl": 
          tblGm=objeto[key];
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
    var bdGm=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdGm.Assoc=false;
    bdGm.select( sql );
    if( bdGm.retorno=='OK'){
      var jsGmF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"        ,"tipo":"chk"  ,"tamGrd":"5em"        ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"     ,"tipo":"edt"  ,"tamGrd":"6em"        ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO"  ,"tipo":"edt"  ,"tamGrd":tamColNome   ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"VALORVISTA" ,"tipo":"edt"  ,"tamGrd":"0em"        ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":4 ,"labelCol":"VALORPRAZO" ,"tipo":"edt"  ,"tamGrd":"0em"        ,"fieldType":"flo2","ordenaColuna":"N"}
          ,{"id":5 ,"labelCol":"VALORMINIMO","tipo":"edt"  ,"tamGrd":"0em"        ,"fieldType":"flo2","ordenaColuna":"N"}          
          ,{"id":6 ,"labelCol":"CODGP"      ,"tipo":"edt"  ,"tamGrd":"0em"        ,"fieldType":"str","ordenaColuna":"N"}          
        ]
        ,"registros"      : bdGm.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblGm                  // Nome da table
        ,"prefixo"        : "gm"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "MODELO"               // Nome da tabela no banco de dados  
        ,"width"          : "52em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objGmF10 === undefined ){          
        objGmF10         = new clsTable2017("objGmF10");
        objGmF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objGmF10.focoF10=foco;  
        };
      };  
      var html          = objGmF10.montarHtmlCE2017(jsGmF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaGm');
      document.getElementById('tblGm').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objGmF10);
    };
  }; 
  if( opc == 1 ){
    var bdGm=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdGm.Assoc=true;
    bdGm.select( sql );
    return bdGm.dados;
  };     
};