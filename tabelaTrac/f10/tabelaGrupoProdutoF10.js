////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fGrupoProdutoF10(opc,codGp,foco,topo,objeto){
    let clsStr = new concatStr();
    clsStr.concat("SELECT A.GP_CODIGO AS CODIGO,A.GP_NOME AS DESCRICAO"                 ); 
    clsStr.concat("  FROM GRUPOPRODUTO A"                                                );
    let tblGp      = "tblGp";
    let tamColNome  = "30em";
    if( typeof objeto === 'object' ){
      for (var key in objeto) {
        switch( key ){
          case "ativo":
            clsStr.concat( " {WHERE} (A.Gp_ATIVO='"+objeto[key]+"')",true);        
            break;    
        };  
      };  
    };
    sql=clsStr.fim();
    console.log(sql);
    //            
    if( opc == 0 ){            
      //////////////////////////////////////////////////////////////////////////////
      // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
      //////////////////////////////////////////////////////////////////////////////
      var bdGp=new clsBancoDados(localStorage.getItem('lsPathPhp'));
      bdGp.Assoc=false;
      bdGp.select( sql );
      if( bdGp.retorno=='OK'){
        var jsGpF10={
          "titulo":[
             {"id":0 ,"labelCol":"OPC"            ,"tipo":"chk"  ,"tamGrd":"5em"        ,"fieldType":"chk"}                                
            ,{"id":1 ,"labelCol":"CODIGO"         ,"tipo":"edt"  ,"tamGrd":"6em"        ,"fieldType":"str","ordenaColuna":"S","align":"center"}
            ,{"id":2 ,"labelCol":"DESCRICAO"      ,"tipo":"edt"  ,"tamGrd":tamColNome   ,"fieldType":"str","ordenaColuna":"S"}      
          ]
          ,"registros"      : bdGp.dados             // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                   // Opção para numero registros/botão/procurar                       
          ,"checarTags"     : "N"                    // Somente em tempo de desenvolvimento(olha as pricipais tags)
          ,"tbl"            : tblGp                  // Nome da table
          ,"div"            : "gp"
          ,"prefixo"        : "Gp"                   // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "GRUPOPRODUTO"               // Nome da tabela no banco de dados  
          ,"width"          : "52em"                 // Tamanho da table
          ,"height"         : "39em"                 // Altura da table
          ,"indiceTable"    : "DESCRICAO"            // Indice inicial da table
        };
        if( objGpF10 === undefined ){          
          objGpF10         = new clsTable2017("objGpF10");
          objGpF10.tblF10  = true;
          if( (foco != undefined) && (foco != "null") ){
            objGpF10.focoF10=foco;  
          };
        };  
        var html          = objGpF10.montarHtmlCE2017(jsGpF10);
        var ajudaF10      = new clsMensagem('Ajuda',topo);
        ajudaF10.divHeight= '410px';  /* Altura container geral*/
        ajudaF10.divWidth = '54em';
        ajudaF10.tagH2    = false;
        ajudaF10.mensagem = html;
        ajudaF10.Show('ajudaGp');
        
        document.getElementById('tblGp').rows[0].cells[2].click();
        delete(ajudaF10);
        delete(objGpF10);
      };
    }; 
    if( opc == 1 ){
      var bdGp=new clsBancoDados(localStorage.getItem("lsPathPhp"));
      bdGp.Assoc=true;
      bdGp.select( sql );
      return bdGp.dados;
    };     
  };