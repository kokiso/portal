////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fVendedorF10(opc,codVnd,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.VND_CODFVR AS CODIGO,FVR.FVR_NOME AS DESCRICAO,FVR.FVR_APELIDO AS APELIDO"  );
  clsStr.concat("       ,A.VND_CODLGN AS CODLGN"                                                      );
  clsStr.concat("  FROM VENDEDOR A"                                                                   );
  clsStr.concat("  LEFT OUTER JOIN FAVORECIDO FVR ON A.VND_CODFVR=FVR.FVR_CODIGO"                     );
  
  let tblVnd      = "tblVnd";
  let tamColNome  = "30em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codfvr": 
          clsStr.concat(" {WHERE} (A.VND_CODFVR='"+objeto[key]+"')",true);
          break;
        case "ativo":
          clsStr.concat( " {AND} (A.VND_ATIVO='"+objeto[key]+"')",true);        
          break;
        case "gpfvr":
          clsStr.concat(" {AND} (FVR.FVR_GFCR <> 1)",true); // ANGELO KOKISO APARECER
          break;    
        case "tamColNome": 
          tamColNome=objeto[key];    
          break;                   
        case "tbl": 
          tblVnd=objeto[key];
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
    var bdVnd=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdVnd.Assoc=false;
    bdVnd.select( sql );
    if( bdVnd.retorno=='OK'){
      var jsVndF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"       ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"       ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":tamColNome  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"APELIDO"   ,"tipo":"edt"  ,"tamGrd":"0em"       ,"fieldType":"str","ordenaColuna":"N"}                    
          ,{"id":4 ,"labelCol":"CODLGN"    ,"tipo":"edt"  ,"tamGrd":"0em"       ,"fieldType":"str"}          
        ]
        ,"registros"      : bdVnd.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblVnd                  // Nome da table
        ,"prefixo"        : "vnd"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "VENDEDOR"              // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objVndF10 === undefined ){          
        objVndF10         = new clsTable2017("objVndF10");
        objVndF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objVndF10.focoF10=foco;  
        };
      };  
      var html          = objVndF10.montarHtmlCE2017(jsVndF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaVnd');
      document.getElementById('tblVnd').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objVndF10);
    };
  }; 
  if( opc == 1 ){
    var bdVnd=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdVnd.Assoc=true;
    bdVnd.select( sql );
    return bdVnd.dados;
  };     
};