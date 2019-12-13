////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
////////////////////////////////////////////////////////////////////////////
function fPadraoF10(padrao){
  var sql="SELECT "+padrao.fieldCod+" AS CODIGO,"+padrao.fieldDes+" AS DESCRICAO FROM "+padrao.tableBd+" A ";
  //
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // a tbl eh padrao "tblPas" mas quando em um mesmo form existir duas chamadas o obj padrao deve ter //
  // a propriedade tbl:"tblXxx" devido funcao function RetF10tblXxx(arr) que naum pode ser repetida   //
  //////////////////////////////////////////////////////////////////////////////////////////////////////  
  var tblPad="tblPad";
  //       
  if( padrao.opc == 0 ){ 
    sql+="WHERE ("+padrao.fieldAtv+"='S')";
    let tamColCodigo  = "6em";  
    let tamColNome    = "30em";
    let divWidth      = "42%";
    for (var key in padrao) {
      switch( key ){
        case "tbl"          : tblPad=padrao[key]        ;break; 
        case "where"        : sql+=padrao[key]          ;break; 
        case "tamColCodigo" : tamColCodigo=padrao[key]  ;break; 
        case "tamColNome"   : tamColNome=padrao[key]    ;break;         
        case "divWidth"     : divWidth=padrao[key]      ;break;         
      };  
    };
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdPad=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdPad.Assoc=false;
    bdPad.select( sql );
    if( bdPad.retorno=='OK'){
      var jsPadF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"3em"   ,"fieldType":"chk"}                                
          ,( padrao.typeCod=="int" ?  
            {"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"} : 
            {"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":tamColCodigo ,"fieldType":"str","ordenaColuna":"S"} )
          ,{"id":2 ,"labelCol":"DESCRICAO"  ,"tipo":"edt"  ,"tamGrd":tamColNome   ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdPad.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblPad                  // Nome da table
        ,"prefixo"        : "pad"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : padrao.tableBd          // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "38em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objPadF10 === undefined ){          
        objPadF10         = new clsTable2017("objPadF10");
        objPadF10.tblF10  = true;
      };  
      
      if( (padrao.foco != undefined) && (padrao.foco != "null") ){
        objPadF10.focoF10=padrao.foco;  
      };
      
      var html          = objPadF10.montarHtmlCE2017(jsPadF10);
      var ajudaF10      = new clsMensagem('Ajuda',padrao.topo);
      ajudaF10.divHeight= '400px';  /* Altura container geral*/
      ajudaF10.divWidth = divWidth;
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaPad');
      document.getElementById(tblPad).rows[0].cells[2].click();
    };
  }; 
  if( padrao.opc == 1 ){
    sql+=" WHERE ("+padrao.fieldCod+"='"+document.getElementById(padrao.edtCod).value.toUpperCase()+"')"
        +"   AND ("+padrao.fieldAtv+"='S')";
    var bdPad=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdPad.Assoc=true;
    bdPad.select( sql );
    return bdPad.dados;
  };     
};