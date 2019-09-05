function fBkp_Contador(whr,exc){
  "use strict";
  var clsJs;  // Classe responsavel por montar um Json e eviar PHP
  var fd;     // Formulario para envio de dados para o PHP
  var msg;    // Variavel para guardadar mensagens de retorno/erro 
  var retPhp; // Retorno do Php para a rotina chamadora  
  clsJs   = jsString("lote");  
  clsJs.add("rotina"      , "selectBkpCnt"      );
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
    var jsBkpCnt={
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
                  ,"tamGrd":"4em"
                  ,"tamImp":"14"
                  ,"padrao":9}
        ,{"id":4  ,"labelCol":"CODEMP"
                  ,"tamGrd":"0em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":5  ,"labelCol":"CRC"
                  ,"tamGrd":"13em"
                  ,"tamImp":"25"
                  ,"padrao":9}
        ,{"id":6  ,"labelCol":"CPF"
                  ,"tamGrd":"12em"
                  ,"tamImp":"23"
                  ,"padrao":9}
        ,{"id":7  ,"labelCol":"CODQC"
                  ,"tamGrd":"6em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":8  ,"labelCol":"CODCDD"
                  ,"tamGrd":"6em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":9  ,"labelCol":"CNPJ"
                  ,"tamGrd":"12em"
                  ,"tamImp":"24"
                  ,"padrao":9}
        ,{"id":10  ,"labelCol":"NOME"
                  ,"tamGrd":"45em"
                  ,"tamImp":"101"
                  ,"padrao":9}
        ,{"id":11  ,"labelCol":"CEP"
                  ,"tamGrd":"8em"
                  ,"padrao":9}
        ,{"id":12  ,"labelCol":"CODLGR"
                  ,"tamGrd":"6em"
                  ,"padrao":9}
        ,{"id":13  ,"labelCol":"ENDERECO"
                  ,"tamGrd":"37em"
                  ,"padrao":9}
        ,{"id":14  ,"labelCol":"NUMERO"
                  ,"tamGrd":"7em"
                  ,"padrao":9}
        ,{"id":15  ,"labelCol":"FONE"
                  ,"tamGrd":"9em"
                  ,"padrao":9}
        ,{"id":16  ,"labelCol":"EMAIL"
                  ,"tamGrd":"47em"
                  ,"padrao":9}
        ,{"id":17  ,"labelCol":"BAIRRO"
                  ,"tamGrd":"12em"
                  ,"padrao":9}
        ,{"id":18  ,"labelCol":"SUFRAMA"
                  ,"tamGrd":"8em"
                  ,"padrao":9}
        ,{"id":19  ,"labelCol":"CODINCTRIB"
                  ,"tamGrd":"5em"
                  ,"padrao":9}
        ,{"id":20  ,"labelCol":"INDAPROCRED"
                  ,"tamGrd":"5em"
                  ,"padrao":9}
        ,{"id":21  ,"labelCol":"CODTIPOCONT"
                  ,"tamGrd":"5em"
                  ,"padrao":9}
        ,{"id":22  ,"labelCol":"INDREGCUM"
                  ,"tamGrd":"6em"
                  ,"padrao":9}
        ,{"id":23  ,"labelCol":"CODRECPIS"
                  ,"tamGrd":"6em"
                  ,"padrao":9}
        ,{"id":24  ,"labelCol":"CODRECPIS"
                  ,"tamGrd":"6em"
                  ,"padrao":9}
        ,{"id":25  ,"labelCol":"CODRECCOFINS"
                  ,"tamGrd":"6em"
                  ,"padrao":9}
        ,{"id":26  ,"labelCol":"INDNATPJ"
                  ,"tamGrd":"6em"
                  ,"padrao":9}
        ,{"id":27  ,"labelCol":"INDATIV"
                  ,"tamGrd":"6em"
                  ,"padrao":9}
        ,{"id":28  ,"labelCol":"REG"
                  ,"tamGrd":"4em"
                  ,"padrao":9}
        ,{"id":29  ,"labelCol":"ATIVO"
                  ,"tamGrd":"4em"
                  ,"padrao":9}
        ,{"id":30 ,"labelCol":"USUARIO"
                  ,"tamGrd":"10em"
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
      ,"width"          : "117em"               // Tamanho da table
      ,"height"         : "53em"                // Altura da table
      ,"relTitulo"      : "ESPIAO - CONTADOR"   // Titulo do relatório
      ,"relOrientacao"  : "P"                   // Paisagem ou retrato
      ,"relFonte"       : "7"                   // Fonte do relatório
      ,"indiceTable"    : "ID"                  // Indice inicial da table
      ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
    }; 
    if( objBkpCnt === undefined ){          
      objBkpCnt=new clsTable2017("objBkpCnt");
    };  
    objBkpCnt.montarHtmlCE2017(jsBkpCnt);
    /////////////////////////////////////
    // Enchendo a grade com registros  //   
    /////////////////////////////////////
    jsBkpCnt.registros=objBkpCnt.addIdUnico(retPhp[0]["dados"]);
    objBkpCnt.montarBody2017();
    /////////////////////////////////////
    //   Marcando os campos alterados  //   
    /////////////////////////////////////
    if( exc.toUpperCase()=='N' )
      document.getElementById(jsBkpCnt.tbl).colAlterada();
  };  
};
