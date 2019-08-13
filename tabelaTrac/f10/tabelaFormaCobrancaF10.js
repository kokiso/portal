////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fFormaCobrancaF10(opc,codFc,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.FC_CODIGO AS CODIGO,A.FC_NOME AS DESCRICAO"      );
  clsStr.concat("  FROM FORMACOBRANCA A"                                   );
  
  let tamColCodigo  = "6em";  
  let tamColNome    = "29em";
  let divWidth      = "36%";
  let tblFc         = "tblFc";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codfc": 
          clsStr.concat(" {AND} (A.FC_CODIGO='"+objeto[key]+"')",true);
          break;
        case "ativo":
          clsStr.concat( " {AND} (A.FC_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "divWidth": 
          divWidth=padrao[key];
          break;  
        case "tamColNome": 
          tamColNome=padrao[key];
          break;                   
        case "tbl": 
          tblFc=objeto[key];
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
    var bdFc=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdFc.Assoc=false;
    bdFc.select( sql );
    if( bdFc.retorno=='OK'){
      var jsFcF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"str","ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":tamColNome  ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdFc.dados              // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblFc                   // Nome da table
        ,"prefixo"        : "fc"                    // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "COBRANCA"              // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objFcF10 === undefined ){          
        objFcF10         = new clsTable2017("objFcF10");
        objFcF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objFcF10.focoF10=foco;  
        };
      };  
      var html          = objFcF10.montarHtmlCE2017(jsFcF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      //ajudaF10.divWidth = '54em';
      ajudaF10.divWidth = divWidth;      
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaFc');
      document.getElementById('tblFc').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objFcF10);
    };
  }; 
  if( opc == 1 ){
    var bdFc=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdFc.Assoc=true;
    bdFc.select( sql );
    return bdFc.dados;
  };     
};