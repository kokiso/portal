////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////

function fFavorecidoF10(opc,codFvr,foco,topo, objeto){
  var sql="SELECT A.FVR_CODIGO AS CODIGO,A.FVR_NOME AS DESCRICAO,A.FVR_CODCDD AS CODCDD,A.FVR_FISJUR AS FJ,A.FVR_CODCTG AS CATEGORIA,A.FVR_APELIDO AS RESUMO,A.FVR_CNPJCPF AS CNPJCPF"
         +"  FROM FAVORECIDO A"     
  if( opc == 0 ){            
    sql+=" WHERE (A.FVR_ATIVO='S')";
    if( typeof objeto === 'object' ){ // ANGELO KOKISO ADIÇÃO DO SWITCH PARA APARECER APENAS TIPO DE FAVORECIDO CLIENTES
      for (var key in objeto) {
        switch( key ){
          case "gpfvr":
            sql+=" AND A.FVR_GFCR <> 1" 
            break;
        };  
      };  
    };    
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdFvr=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdFvr.Assoc=false;
    bdFvr.select( sql );
    if( bdFvr.retorno=='OK'){
      var jsFvrF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"5em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"23em"  ,"fieldType":"str","ordenaColuna":"S","truncate":true}
          ,{"id":3 ,"labelCol":"CODCDD"    ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":4 ,"labelCol":"FJ"        ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":5 ,"labelCol":"CATEGORIA" ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":6 ,"labelCol":"RESUMO"    ,"tipo":"edt"  ,"tamGrd":"7em"   ,"fieldType":"str","ordenaColuna":"S","truncate":true}
          ,{"id":7 ,"labelCol":"CNPJCPF"   ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str","ordenaColuna":"N"}
        ]
        ,"registros"      : bdFvr.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : "tblFvr"                // Nome da table
        ,"prefixo"        : "fvr"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "FAVORECIDO"            // Nome da tabela no banco de dados  
        ,"width"          : "52em"                  // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
      };
      if( objFvrF10 === undefined ){          
        objFvrF10         = new clsTable2017("objFvrF10");
        objFvrF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objFvrF10.focoF10=foco;  
        };
      };  
      var html          = objFvrF10.montarHtmlCE2017(jsFvrF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaFvr');
      document.getElementById('tblFvr').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objFvrF10);
    };
  }; 
  if( opc == 1 ){
    sql+=" WHERE (A.FVR_CODIGO='"+document.getElementById(codFvr).value.toUpperCase()+"')"
        +"   AND (A.FVR_ATIVO='S')";
    var bdFvr=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdFvr.Assoc=true;
    bdFvr.select( sql );
    return bdFvr.dados;
  };    
};