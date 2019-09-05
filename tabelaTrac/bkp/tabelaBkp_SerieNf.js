function fBkp_SerieNf(whr,exc){
  "use strict";
  var clsJs;  // Classe responsavel por montar um Json e eviar PHP
  var fd;     // Formulario para envio de dados para o PHP
  var msg;    // Variavel para guardadar mensagens de retorno/erro 
  var retPhp; // Retorno do Php para a rotina chamadora  
  clsJs   = jsString("lote");  
  clsJs.add("rotina"      , "selectBkpSnf"      );
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
    var jsBkpSnf={
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
                  ,"tamImp":"20"
                  ,"fieldType":"dat"
                  ,"padrao":9}
        ,{"id":2  ,"labelCol":"ACAO"
                  ,"tamGrd":"4em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":3  ,"labelCol":"CODIGO"
                  ,"tamGrd":"5em"
                  ,"tamImp":"15"
                  ,"fieldType":"int"
                  ,"padrao":9}
        ,{"id":4  ,"labelCol":"SERIE"
                  ,"tamGrd":"5em"
                  ,"tamImp":"20"
                  ,"padrao":9}
        ,{"id":5  ,"labelCol":"ES"
                  ,"tamGrd":"5em"
                  ,"tamImp":"10"
                  ,"padrao":9}
        ,{"id":6  ,"labelCol":"CODTD"
                  ,"tamGrd":"5em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":7  ,"labelCol":"INFORMARNF"
                  ,"tamGrd":"5em"
                  ,"tamImp":"20"
                  ,"padrao":9}
        ,{"id":8  ,"labelCol":"NFINICIO"
                  ,"tamGrd":"7em"
                  ,"tamImp":"15"
                  ,"fieldType":"int"
                  ,"padrao":9}
        ,{"id":9  ,"labelCol":"NFFIM"
                  ,"tamGrd":"7em"
                  ,"tamImp":"12"
                  ,"fieldType":"int"
                  ,"padrao":9}
        ,{"id":10  ,"labelCol":"IDF"
                  ,"tamGrd":"19em"
                  ,"tamImp":"32"
                  ,"padrao":9}
        ,{"id":11  ,"labelCol":"MODELO"
                  ,"tamGrd":"5em"
                  ,"tamImp":"13"
                  ,"padrao":9}
        ,{"id":12  ,"labelCol":"LIVRO"
                  ,"tamGrd":"5em"
                  ,"tamImp":"13"
                  ,"padrao":9}
        ,{"id":13  ,"labelCol":"ENVIO"
                  ,"tamGrd":"5em"
                  ,"tamImp":"13"
                  ,"padrao":9}
        ,{"id":14  ,"labelCol":"CODFLL"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":15  ,"labelCol":"CODEMP"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":16  ,"labelCol":"REG"
                  ,"tamGrd":"4em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":17  ,"labelCol":"ATIVO"
                  ,"tamGrd":"4em"
                  ,"tamImp":"15"
                  ,"padrao":9}
        ,{"id":18  ,"labelCol":"USUARIO"
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
      ,"registros"      : []                    // Recebe um Json vindo da classe clsBancoDados
      ,"opcRegSeek"     : true                  // Opção para numero registros/botão/procurar                     
      ,"checarTags"     : "S"                   // Somente em tempo de desenvolvimento(olha as pricipais tags)                        
      ,"form"           : "espiao"              // Onde vai ser gerado o fieldSet             
      ,"divModal"       : "divTopoInicio"       // Onde vai se appendado abaixo deste a table 
      ,"tbl"            : "tblBkp"              // Nome da table
      ,"prefixo"        : "bkp"                 // Prefixo para elementos do HTML em jsTable2017.js      
      ,"width"          : "98em"                // Tamanho da table
      ,"height"         : "53em"                // Altura da table
      ,"relTitulo"      : "ESPIAO - SERIENF"    // Titulo do relatório
      ,"relOrientacao"  : "P"                   // Paisagem ou retrato
      ,"relFonte"       : "7"                   // Fonte do relatório
      ,"indiceTable"    : "ID"                  // Indice inicial da table
      ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
    }; 
    if( objBkpSnf === undefined ){          
      objBkpSnf=new clsTable2017("objBkpSnf");
    };  
    objBkpSnf.montarHtmlCE2017(jsBkpSnf);
    /////////////////////////////////////
    // Enchendo a grade com registros  //   
    /////////////////////////////////////
    jsBkpSnf.registros=objBkpSnf.addIdUnico(retPhp[0]["dados"]);
    objBkpSnf.montarBody2017();
    /////////////////////////////////////
    //   Marcando os campos alterados  //   
    /////////////////////////////////////
    if( exc.toUpperCase()=='N' )
      document.getElementById(jsBkpSnf.tbl).colAlterada();
  };  
};
