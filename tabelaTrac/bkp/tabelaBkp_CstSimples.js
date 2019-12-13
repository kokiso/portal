function fBkp_CstSimples(whr,exc){
  "use strict";
  var clsJs;  // Classe responsavel por montar um Json e eviar PHP
  var fd;     // Formulario para envio de dados para o PHP
  var msg;    // Variavel para guardadar mensagens de retorno/erro 
  var retPhp; // Retorno do Php para a rotina chamadora  
  clsJs   = jsString("lote");  
  clsJs.add("rotina"      , "selectBkpSn"      );
  clsJs.add("login"       , jsPub[0].usr_login  );
  clsJs.add("where"       , whr                 );
  fd = new FormData();
  fd.append("bkp" , clsJs.fim());
  msg     = requestPedido("Trac_TabelasBkp.php",fd); 
  retPhp  = JSON.parse(msg);
  if( retPhp[0].retorno == "OK" ){
    //////////////////////////////////////////////////////////////////////////////////
    // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
    // Campo obrigatório se existir rotina de manutenção na table devido Json       //
    // Esta rotina não tem manutenção via classe clsTable2017                       //
    // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
    //////////////////////////////////////////////////////////////////////////////////
    var jsBkpSn={
      "titulo":[
         {"id":0  ,"labelCol":"ID"
                  ,"tamGrd":"5em"
                  ,"tamImp":"12"
                  ,"fieldType":"int"
                  ,"formato":['i6']
                  ,"ordenaColuna":"S"
                  ,"padrao":9}
        ,{"id":1  ,"labelCol":"DATA"
                  ,"tamGrd":"8em"
                  ,"tamImp":"18"
                  ,"fieldType":"dat"
                  ,"padrao":9}
        ,{"id":2  ,"labelCol":"ACAO"
                  ,"tamGrd":"4em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":3  ,"labelCol":"CODIGO"
                  ,"tamGrd":"5em"
                  ,"tamImp":"14"
                  ,"padrao":9}
        ,{"id":4  ,"labelCol":"ENTSAI","tamGrd":"5em","tamImp":"12","labelColImp":"ENTSAI","padrao":9}
        ,{"id":5  ,"labelCol":"DESCRICAO"
                  ,"tamGrd":"44em"
                  ,"tamImp":"98"
                  ,"padrao":9}
        ,{"id":6  ,"labelCol":"SNALIQ","tamGrd":"4em","tamImp":"12","labelColImp":"SNALIQ","padrao":9}
        ,{"id":7  ,"labelCol":"PCISENTAS","tamGrd":"9em","tamImp":"15","labelColImp":"ISENTAS","padrao":9}
        ,{"id":8  ,"labelCol":"PCOUTRAS","tamGrd":"8em","tamImp":"15","labelColImp":"OUTRAS","padrao":9}        
        ,{"id":9  ,"labelCol":"REDUCAOBC","tamGrd":"8em","tamImp":"20","labelColImp":"REDUCAOBC","padrao":9}        
        ,{"id":10  ,"labelCol":"REG"
                  ,"tamGrd":"4em"
                  ,"tamImp":"10"
                  ,"padrao":9}
        ,{"id":11  ,"labelCol":"ATIVO"
                  ,"tamGrd":"4em"
                  ,"tamImp":"10"
                  ,"padrao":9}
        ,{"id":12  ,"labelCol":"USUARIO"
                  ,"tamGrd":"10em"
                  ,"tamImp":"30"
                  ,"classe":"escondeA"
                  ,"padrao":9}
      ]
      ,
      "botoesH":[
         {"texto":"Imprimir"  ,"name":"btnImprimir"   ,"onClick":"3"  ,"enabled":true ,"imagem":"fa fa-print" }
        ,{"texto":"Excel"     ,"name":"btnExcel"      ,"onClick":"5"  ,"enabled":true ,"imagem":"fa fa-file-excel-o" }         
        ,{"texto":"Fechar"    ,"name":"btnFechar"     ,"onClick":"6"  ,"enabled":true ,"imagem":"fa fa-close" }
      ]
      ,"registros"      : []                              // Recebe um Json vindo da classe clsBancoDados
      ,"opcRegSeek"     : true                            // Opção para numero registros/botão/procurar                     
      ,"checarTags"     : "S"                             // Somente em tempo de desenvolvimento(olha as pricipais tags)                        
      ,"form"           : "espiao"                        // Onde vai ser gerado o fieldSet             
      ,"divModal"       : "divTopoInicio"                 // Onde vai se appendado abaixo deste a table 
      ,"tbl"            : "tblBkp"                        // Nome da table
      ,"prefixo"        : "bkp"                           // Prefixo para elementos do HTML em jsTable2017.js      
      ,"width"          : "90em"                          // Tamanho da table
      ,"height"         : "53em"                          // Altura da table
      ,"relTitulo"      : "ESPIAO - CSTSIMPLES"              // Titulo do relatório
      ,"relOrientacao"  : "P"                             // Paisagem ou retrato
      ,"relFonte"       : "8"                             // Fonte do relatório
      ,"indiceTable"    : "ID"                            // Indice inicial da table
      ,"tamBotao"       : "15"                            // Tamanho botoes defalt 12 [12/25/50/75/100]
    }; 
    if( objBkpSn === undefined ){          
      objBkpSn=new clsTable2017("objBkpSn");
    };  
    objBkpSn.montarHtmlCE2017(jsBkpSn);
    /////////////////////////////////////
    // Enchendo a grade com registros  //   
    /////////////////////////////////////
    jsBkpSn.registros=objBkpSn.addIdUnico(retPhp[0]["dados"]);
    objBkpSn.montarBody2017();
    /////////////////////////////////////
    //   Marcando os campos alterados  //   
    /////////////////////////////////////
    if( exc.toUpperCase()=='N' )
      document.getElementById(jsBkpSn.tbl).colAlterada();
  };  
};
