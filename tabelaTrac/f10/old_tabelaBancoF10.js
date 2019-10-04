////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fBancoF10(opc,codBnc,foco,topo){
  var sql="SELECT A.BNC_CODIGO AS CODIGO,A.BNC_NOME AS DESCRICAO,BNC_CODFVR AS CODFVR"
         +"  FROM BANCO A WHERE (A.BNC_CODEMP="+jsPub[0].emp_codigo+")"
  //            
  if( opc == 0 ){            
    sql+=" AND (A.BNC_ATIVO='S')";  
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdBnc=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdBnc.Assoc=false;
    bdBnc.select( sql );
    if( bdBnc.retorno=='OK'){
      var jsBncF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"30em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"CODFVR"    ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"int","ordenaColuna":"N"}
        ]
        ,"registros"      : bdBnc.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : "tblBnc"                // Nome da table
        ,"prefixo"        : "Bnc"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "CLIENTE"               // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objBncF10 === undefined ){          
        objBncF10         = new clsTable2017("objBncF10");
        objBncF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objBncF10.focoF10=foco;  
        };
      };  
      var html          = objBncF10.montarHtmlCE2017(jsBncF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaBnc');
      document.getElementById('tblBnc').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objBncF10);
    };
  }; 
  if( opc == 1 ){
    sql+=" AND (A.BNC_CODIGO='"+document.getElementById(codBnc).value.toUpperCase()+"')"
        +"   AND (A.BNC_ATIVO='S')";
    var bdBnc=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdBnc.Assoc=true;
    bdBnc.select( sql );
    return bdBnc.dados;
  };     
};