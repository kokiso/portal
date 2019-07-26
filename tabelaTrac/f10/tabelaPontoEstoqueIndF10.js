////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fPontoEstoqueIndF10(opc,codPei,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.PEI_CODFVR AS CODIGO,FVR.FVR_APELIDO AS DESCRICAO"      );
  clsStr.concat("  FROM PONTOESTOQUEIND A"                                        );
  clsStr.concat("  LEFT OUTER JOIN FAVORECIDO FVR ON A.PEI_CODFVR=FVR.FVR_CODIGO" );
  let tblPei="tblPei";
  let colDescricao="30em";    // Se estiver em um frame o tamanho se altera em relacao a pagina full
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codpe": 
          clsStr.concat(" {WHERE} (A.PEI_CODPE='"+objeto[key]+"')",true);
          break;
        case "codfrv": 
          clsStr.concat(" {AND} (A.PEI_CODFRV='"+objeto[key]+"')",true);
          break;
        case "ativo":
          clsStr.concat( " {AND} (A.PEI_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "colDescricao":
          colDescricao=objeto[key];        
          break;    
          
        case "tbl": 
          tblPei=objeto[key];
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
    var bdPei=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdPei.Assoc=false;
    bdPei.select( sql );
    if( bdPei.retorno=='OK'){
      var jsPeiF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":colDescricao   ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdPei.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblPei                  // Nome da table
        ,"div"            : "pei"
        ,"prefixo"        : "Pei"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "COLABORADOR"           // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objPeiF10 === undefined ){          
        objPeiF10         = new clsTable2017("objPeiF10");
        objPeiF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objPeiF10.focoF10=foco;  
        };
      };  
      var html          = objPeiF10.montarHtmlCE2017(jsPeiF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaPei');
      document.getElementById('tblPei').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objPeiF10);
    };
  }; 
  if( opc == 1 ){
    var bdPei=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdPei.Assoc=true;
    bdPei.select( sql );
    return bdPei.dados;
  };     
};