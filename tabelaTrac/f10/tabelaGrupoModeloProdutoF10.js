////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fGrupoModeloProdutoF10(opc,codGmp,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.GMP_CODIGO AS CODIGO,GM.GM_NOME AS DESCRICAO,A.GMP_NUMSERIE AS SERIE"   );
  clsStr.concat("       ,A.GMP_SINCARD AS SINCARD"                                                );
  clsStr.concat("       ,CASE WHEN A.GMP_DTCONFIGURADO IS NULL THEN 'NAO' ELSE 'SIM' END AS CFG"  );            
  clsStr.concat("  FROM GRUPOMODELOPRODUTO A"                                                     );
  clsStr.concat("  LEFT OUTER JOIN GRUPOMODELO GM ON A.GMP_CODGM=GM.GM_CODIGO"                    );
  let tblGmp     = "tblGmp";
  let divWidth  = "54em";
  let tblWidth  = "52em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codgm": 
          clsStr.concat(" {WHERE} (A.GMP_CODGM='"+objeto[key]+"')",true);
          break;
        case "codgp": 
          clsStr.concat(" {WHERE} (A.GMP_CODGP='"+objeto[key]+"')",true);
          break;
        case "codpe": 
          clsStr.concat(" {AND} (A.GMP_CODPE='"+objeto[key]+"')",true);
          break;
        case "codaut":
          clsStr.concat( " {AND} (A.GMP_CODAUT='"+objeto[key]+"')",true);        
          break;    
        case "divWidth": 
          divWidth=objeto[key];
          break;
        case "tbl": 
          tblGmp=objeto[key];
          break;           
        case "tblWidth": 
          tblWidth=objeto[key];
          break;
        case "where"  : 
          clsStr.concat( objeto[key],true);        
          break;           
      };  
    };  
  };
  sql=clsStr.fim();
  console.log(sql)
  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdGmp=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdGmp.Assoc=false;
    bdGmp.select( sql );
    if( bdGmp.retorno=='OK'){
      var jsGmpF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"3em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"5em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"27em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"SERIE"     ,"tipo":"edt"  ,"tamGrd":"10em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":4 ,"labelCol":"SINCARD"   ,"tipo":"edt"  ,"tamGrd":"10em"  ,"fieldType":"str","ordenaColuna":"S"}          
          ,{"id":5 ,"labelCol":"CFG"       ,"tipo":"edt"  ,"tamGrd":"3em"   ,"fieldType":"str","ordenaColuna":"N"}                    
        ]
        ,"registros"      : bdGmp.dados              // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblGmp                   // Nome da table
        ,"prefixo"        : "Gmp"                    // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "GRUPOMODELOPRODUTO"    // Nome da tabela no banco de dados  
        ,"width"          : tblWidth                // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
        ,"tamBotao"       : "20"
      };
      if( objGmpF10 === undefined ){          
        objGmpF10         = new clsTable2017("objGmpF10");
        objGmpF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objGmpF10.focoF10=foco;  
        };
      };  
      var html          = objGmpF10.montarHtmlCE2017(jsGmpF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = divWidth;
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaGmp');
      document.getElementById('tblGmp').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objGmpF10);
    };
  }; 
  if( opc == 1 ){
    var bdGmp=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdGmp.Assoc=true;
    bdGmp.select( sql );
    return bdGmp.dados;
  };     
};