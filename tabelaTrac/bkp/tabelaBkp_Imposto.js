function fBkp_Imposto(whr,exc){
  "use strict";
  var clsJs;  // Classe responsavel por montar um Json e eviar PHP
  var fd;     // Formulario para envio de dados para o PHP
  var msg;    // Variavel para guardadar mensagens de retorno/erro 
  var retPhp; // Retorno do Php para a rotina chamadora  
  clsJs   = jsString("lote");  
  clsJs.add("rotina"      , "selectBkpImp"      );
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
    var jsBkpImp={
      "titulo":[
         {"id":0  ,"labelCol":"ID"
                  ,"tamGrd":"8em"
                  ,"tamImp":"20"
                  ,"fieldType":"int"
                  ,"formato":['i6']
                  ,"ordenaColuna":"S"
                  ,"padrao":9}
        ,{"id":1  ,"labelCol":"DATA"
                  ,"tamGrd":"10em"
                  ,"tamImp":"20"
                  ,"fieldType":"dat"
                  ,"padrao":9}
        ,{"id":2  ,"labelCol":"ACAO"
                  ,"tamGrd":"4em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":3  ,"labelCol":"IUFDE"
                  ,"tamGrd":"5em"
                  ,"tamImp":"18"
                  ,"padrao":9}
        ,{"id":4  ,"labelCol":"UFPARA"
                  ,"tamGrd":"5em"
                  ,"tamImp":"18"
                  ,"padrao":9}
        ,{"id":5  ,"labelCol":"CODNCM"
                  ,"tamGrd":"12em"
                  ,"tamImp":"18"
                  ,"padrao":9}
        ,{"id":6  ,"labelCol":"CODCTG"
                  ,"tamGrd":"5em"
                  ,"tamImp":"15"
                  ,"padrao":9}
        ,{"id":7  ,"labelCol":"ENTSAI"
                  ,"tamGrd":"3em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":8  ,"labelCol":"CODNO"
                  ,"tamGrd":"5em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":9  ,"labelCol":"CFOP"
                  ,"tamGrd":"5em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":10  ,"labelCol":"CSTICMS"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":11  ,"labelCol":"ALIQICMS"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":12  ,"labelCol":"REDUCAOBC"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":13  ,"labelCol":"CSTIPI"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":14  ,"labelCol":"ALIQIPI"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":15  ,"labelCol":"CSTPIS"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":16  ,"labelCol":"ALIQPIS"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":17  ,"labelCol":"CSTCOFINS"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":18  ,"labelCol":"ALIQCOFINS"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":19  ,"labelCol":"ALIQST"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":20  ,"labelCol":"ALTERANFP"
                  ,"tamGrd":"3em"
                  ,"tamImp":"17"
                  ,"padrao":9}
        ,{"id":21  ,"labelCol":"CODEMP"
                  ,"tamGrd":"0em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":22  ,"labelCol":"CODFLL"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":23  ,"labelCol":"REG"
                  ,"tamGrd":"4em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":24  ,"labelCol":"ATIVO"
                  ,"tamGrd":"4em"
                  ,"tamImp":"15"
                  ,"padrao":9}
        ,{"id":25 ,"labelCol":"USUARIO"
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
      ,"width"          : "101em"               // Tamanho da table
      ,"height"         : "53em"                // Altura da table
      ,"relTitulo"      : "ESPIAO - IMPOSTO"    // Titulo do relatório
      ,"relOrientacao"  : "P"                   // Paisagem ou retrato
      ,"relFonte"       : "7"                   // Fonte do relatório
      ,"indiceTable"    : "ID"                  // Indice inicial da table
      ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
    }; 
    if( objBkpImp === undefined ){          
      objBkpImp=new clsTable2017("objBkpImp");
    };  
    objBkpImp.montarHtmlCE2017(jsBkpImp);
    /////////////////////////////////////
    // Enchendo a grade com registros  //   
    /////////////////////////////////////
    jsBkpImp.registros=objBkpImp.addIdUnico(retPhp[0]["dados"]);
    objBkpImp.montarBody2017();
    /////////////////////////////////////
    //   Marcando os campos alterados  //   
    /////////////////////////////////////
    if( exc.toUpperCase()=='N' )
      document.getElementById(jsBkpImp.tbl).colAlterada();
  };  
};