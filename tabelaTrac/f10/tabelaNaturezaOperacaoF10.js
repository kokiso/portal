////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fNaturezaOperacaoF10(opc,codNo,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.NO_CODIGO AS CODIGO,A.NO_NOME AS DESCRICAO"         );
  clsStr.concat("       ,PT.PT_CODCC AS CODCC"                                  );  
  clsStr.concat("  FROM NATUREZAOPERACAO A"                                     );
  clsStr.concat("  LEFT OUTER JOIN PADRAOTITULO PT ON A.NO_CODPT=PT.PT_CODIGO"  );  
  
  let tblNo="tblNo";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "codno": 
          clsStr.concat(" {AND} (A.NO_CODIGO='"+objeto[key]+"')",true);
          break;
        case "ativo":
          clsStr.concat( " {AND} (A.NO_ATIVO='"+objeto[key]+"')",true);        
          break;    
        case "tbl": 
          tblNo=objeto[key];
          break; 
        case "where"  : 
          clsStr.concat( objeto[key],true);        
          break;           
      };  
    };  
  };
    console.log(clsStr);

  sql=clsStr.fim();

  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdNo=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdNo.Assoc=false;
    bdNo.select( sql );
    if( bdNo.retorno=='OK'){
      var jsNoF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"5em"   ,"fieldType":"str","ordenaColuna":"S","align":"center"}
          ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"30em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"TIPO"      ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str"}
          ,{"id":4 ,"labelCol":"CODPT"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str"}
          ,{"id":5 ,"labelCol":"CODCC"     ,"tipo":"edt"  ,"tamGrd":"0em"   ,"fieldType":"str"}
        ]
        ,"registros"      : bdNo.dados             // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblNo                  // Nome da table
        ,"prefixo"        : "no"                   // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "BANCO"                // Nome da tabela no banco de dados  
        ,"width"          : "52em"                 // Tamanho da table
        ,"height"         : "39em"                 // Altura da table
        ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
      };
      if( objNoF10 === undefined ){          
        objNoF10         = new clsTable2017("objNoF10");
        objNoF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objNoF10.focoF10=foco;  
        };
      };  
      var html          = objNoF10.montarHtmlCE2017(jsNoF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = '54em';
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaNo');
      document.getElementById('tblNo').rows[0].cells[2].click();
      delete(ajudaF10);
      delete(objNoF10);
    };
  }; 
  if( opc == 1 ){
    var bdNo=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdNo.Assoc=true;
    bdNo.select( sql );
    return bdNo.dados;
  };     
};