////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fEnderecoF10(opc,codCntE,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.CNTE_CODIGO AS CODIGO"                                          );
  clsStr.concat("       ,A.CNTE_CODFVR AS FAVORECIDO"                                     );
  clsStr.concat("       ,C.CDD_NOME AS CIDADE"                                            );
  clsStr.concat("       ,A.CNTE_CEP AS CEP"                                               );          
  clsStr.concat("       ,A.CNTE_CODLGR+' '+A.CNTE_ENDERECO+' '+A.CNTE_NUMERO AS ENDERECO" );
  clsStr.concat("  FROM CONTRATOENDERECO A"                                               );
  clsStr.concat("  LEFT OUTER JOIN CIDADE C ON A.CNTE_CODCDD=C.CDD_CODIGO"                );
  let tblCntE   = "tblCntE";
  let divWidth  = "54em";
  let tblWidth  = "52em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "ativo":
          clsStr.concat( " {AND} (A.CNTE_ATIVO='"+objeto[key]+"')",true);        
          break;
        case "codfvr": 
          //clsStr.concat(" {WHERE} (A.CNTE_CODFVR='"+objeto[key]+"')",true);
          break;
        case "divWidth": 
          divWidth=objeto[key];
          break;
        case "tbl": 
          tblCntE=objeto[key];
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
  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdCntE=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdCntE.Assoc=false;
    bdCntE.select( sql );
    if( bdCntE.retorno=='OK'){
      var jsCntEF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"          ,"tipo":"chk"  ,"tamGrd":"3em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"       ,"tipo":"edt"  ,"tamGrd":"5em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"FAVORECIDO"   ,"tipo":"edt"  ,"tamGrd":"5em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":3 ,"labelCol":"CIDADE"       ,"tipo":"edt"  ,"tamGrd":"15em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":4 ,"labelCol":"CEP"          ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":5 ,"labelCol":"ENDERECO"     ,"tipo":"edt"  ,"tamGrd":"30em"  ,"fieldType":"str","ordenaColuna":"S"}          
        ]
        ,"registros"      : bdCntE.dados              // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblCntE                   // Nome da table
        ,"div"            : "cnte"
        ,"prefixo"        : "cnte"                    // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "CONTRATOENDERECO"    // Nome da tabela no banco de dados  
        ,"width"          : tblWidth                // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "CIDADE"             // Indice inicial da table
        ,"tamBotao"       : "20"
      };
      if( objCntEF10 === undefined ){          
        objCntEF10         = new clsTable2017("objCntEF10");
        objCntEF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objCntEF10.focoF10=foco;  
        };
      };  
      var html          = objCntEF10.montarHtmlCE2017(jsCntEF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = divWidth;
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaCntE');
      document.getElementById(tblCntE).rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objCntEF10);
    };
  }; 
  if( opc == 1 ){
    var bdCntE=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdCntE.Assoc=true;
    bdCntE.select( sql );
    return bdCntE.dados;
  };     
};