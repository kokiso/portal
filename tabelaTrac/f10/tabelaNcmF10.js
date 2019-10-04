////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fNcmF10(opc,codNcm,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.NCM_CODIGO AS CODIGO,A.NCM_NOME AS DESCRICAO" );
  clsStr.concat("  FROM NCM A"                                          );
  let tblNcm="tblNcm";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codncm": 
          clsStr.concat(" {AND} (A.NCM_CODIGO='"+objeto[key]+"')",true);
          break;
        case "ativo":
          clsStr.concat( " {AND} (A.NCM_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "tbl": 
          tblNcm=objeto[key];
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
    var bdNcm=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdNcm.Assoc=false;
    bdNcm.select( sql );
    if( bdNcm.retorno=='OK'){
      var jsNcmF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"8em"   ,"fieldType":"str","ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"27em"  ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdNcm.dados            // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblNcm                 // Nome da table
        ,"prefixo"        : "ncm"                  // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "BANCO"                // Nome da tabela no banco de dados  
        ,"width"          : "52em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objNcmF10 === undefined ){          
        objNcmF10         = new clsTable2017("objNcmF10");
        objNcmF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objNcmF10.focoF10=foco;  
        };
      };  
      
      var html          = objNcmF10.montarHtmlCE2017(jsNcmF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaNcm');
      document.getElementById(tblNcm).rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objNcmF10);
    };
  }; 
  if( opc == 1 ){
    var bdNcm=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdNcm.Assoc=true;
    bdNcm.select( sql );
    return bdNcm.dados;
  };     
};