////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fServicoPrefeituraF10(opc,codSpr,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT DISTINCT A.SPR_CODIGO AS CODIGO,A.SPR_NOME AS NOME FROM SERVICOPREFEITURA A" );
  
  let tblSpr      = "tblSpr";
  let tamColNome  = "30em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "ativo":
          clsStr.concat( " {AND} (A.SPR_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "tamColNome": 
          tamColNome=objeto[key];    
          break;                   
        case "tbl": 
          tblSpr=objeto[key];
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
    var bdSpr=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdSpr.Assoc=false;
    bdSpr.select( sql );
    if( bdSpr.retorno=='OK'){
      var jsSprF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"       ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"       ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":tamColNome  ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdSpr.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblSpr                  // Nome da table
        ,"div"            : "spr"
        ,"prefixo"        : "spr"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "SERVICOPREFEITURA"     // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objSprF10 === undefined ){          
        objSprF10         = new clsTable2017("objSprF10");
        objSprF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objSprF10.focoF10=foco;  
        };
      };  
      var html          = objSprF10.montarHtmlCE2017(jsSprF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaSpr');
      document.getElementById('tblSpr').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objSprF10);
    };
  }; 
  if( opc == 1 ){
    var bdSpr=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdSpr.Assoc=true;
    bdSpr.select( sql );
    return bdSpr.dados;
  };     
};