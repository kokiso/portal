////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////

function fCidadeF10(opc,codCdd,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.CDD_CODIGO AS CODIGO,A.CDD_NOME AS DESCRICAO" );
  clsStr.concat("       ,A.CDD_CODEST AS UF"                            );  
  clsStr.concat("  FROM CIDADE A"                                       );
  
  let tblCdd      = "tblCdd";
  let tamColNome  = "29em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "ativo":
          clsStr.concat( " {AND} (A.CDD_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "codcdd":
          clsStr.concat( " {WHERE} (A.CDD_CODIGO='"+objeto[key]+"')",true);        
          break;    
        case "tamColNome": 
          tamColNome=objeto[key];    
          break;                   
        case "tbl": 
          tblCdd=objeto[key];
          break;           
        case "where": 
          clsStr.concat(objeto[key],true);        
          break;           
      };  
    };  
  };
  sql=clsStr.fim();
  console.log(sql);
  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdCdd=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdCdd.Assoc=false;
    bdCdd.select( sql );
    if( bdCdd.retorno=='OK'){
      var jsCddF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"        ,"tipo":"chk"  ,"tamGrd":"3em"        ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"     ,"tipo":"edt"  ,"tamGrd":"6em"        ,"fieldType":"str","ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO"  ,"tipo":"edt"  ,"tamGrd":tamColNome   ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"UF"         ,"tipo":"edt"  ,"tamGrd":"2em"        ,"fieldType":"str","ordenaColuna":"N"}          
        ]
        ,"registros"      : bdCdd.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblCdd                  // Nome da table
        ,"div"            : "cdd"
        ,"prefixo"        : "Cdd"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "MODELO"               // Nome da tabela no banco de dados  
        ,"width"          : "52em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objCddF10 === undefined ){          
        objCddF10         = new clsTable2017("objCddF10");
        objCddF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objCddF10.focoF10=foco;  
        };
      };  
      var html          = objCddF10.montarHtmlCE2017(jsCddF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaCdd');
      document.getElementById('tblCdd').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objCddF10);
    };
  }; 
  if( opc == 1 ){
    var bdCdd=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdCdd.Assoc=true;
    bdCdd.select( sql );
    return bdCdd.dados;
  };     
};