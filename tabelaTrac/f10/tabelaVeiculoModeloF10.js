////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////

function fVeiculoModeloF10(opc,codVmd,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.VMD_CODIGO AS CODIGO,A.VMD_NOME AS DESCRICAO"                   );
  clsStr.concat("       ,VFB.VFB_NOME AS FABRICANTE"                                      );  
  clsStr.concat("  FROM VEICULOMODELO A"                                                  );
  clsStr.concat("  LEFT OUTER JOIN VEICULOFABRICANTE VFB ON A.VMD_CODVFB=VFB.VFB_CODIGO"  );  
  
  let tblVmd      = "tblVmd";
  let tamColNome  = "30em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "ativo":
          clsStr.concat( " {AND} (A.VND_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "codvmd":
          clsStr.concat( " {WHERE} (A.VMD_CODIGO='"+objeto[key]+"')",true);        
          break;    
        case "tamColNome": 
          tamColNome=objeto[key];    
          break;                   
        case "tbl": 
          tblVmd=objeto[key];
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
    var bdVmd=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdVmd.Assoc=false;
    bdVmd.select( sql );
    if( bdVmd.retorno=='OK'){
      var jsVmdF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"        ,"tipo":"chk"  ,"tamGrd":"5em"        ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"     ,"tipo":"edt"  ,"tamGrd":"6em"        ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO"  ,"tipo":"edt"  ,"tamGrd":tamColNome   ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"FABRICANTE" ,"tipo":"edt"  ,"tamGrd":"5em"        ,"fieldType":"str","ordenaColuna":"N"}          
        ]
        ,"registros"      : bdVmd.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblVmd                  // Nome da table
        ,"prefixo"        : "vmd"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "MODELO"               // Nome da tabela no banco de dados  
        ,"width"          : "52em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objVmdF10 === undefined ){          
        objVmdF10         = new clsTable2017("objVmdF10");
        objVmdF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objVmdF10.focoF10=foco;  
        };
      };  
      var html          = objVmdF10.montarHtmlCE2017(jsVmdF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaVmd');
      document.getElementById('tblVmd').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objVmdF10);
    };
  }; 
  if( opc == 1 ){
    var bdVmd=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdVmd.Assoc=true;
    bdVmd.select( sql );
    return bdVmd.dados;
  };     
};