////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fTdF10(opc,codTd,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.TD_CODIGO AS CODIGO, A.TD_NOME AS DESCRICAO");
  clsStr.concat("  FROM TIPODOCUMENTO A"                              );
  clsStr.concat("  WHERE A.TD_ATIVO = 'S'"                            );
  let tblTd="tblTd";
  sql=clsStr.fim();
  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdTd=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdTd.Assoc=false;
    bdTd.select( sql );
    if( bdTd.retorno=='OK'){
      var jsTdF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"8em"   ,"fieldType":"str","ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"27em"  ,"fieldType":"str","ordenaColuna":"S"}
        ]
        ,"registros"      : bdTd.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblTd                  // Nome da table
        ,"prefixo"        : "td"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "TIPODOCUMENTO"        // Nome da tabela no banco de dados  
        ,"width"          : "52em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objTdF10 === undefined ){          
        objTdF10         = new clsTable2017("objTdF10");
        objTdF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objTdF10.focoF10=foco;  
        };
      };
      
      var html          = objTdF10.montarHtmlCE2017(jsTdF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaTd');
      document.getElementById(tblTd).rows[0].cells[1].click();
      delete(ajudaF10);
      delete(objTdF10);
    };
  }; 
  if( opc == 1 ){
    var bdTd=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdTd.Assoc=true;
    bdTd.select( sql );
    return bdTd.dados;
  };     
};