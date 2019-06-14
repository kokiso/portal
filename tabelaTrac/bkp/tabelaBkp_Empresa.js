function fBkp_Empresa(whr,exc){
  "use strict";
  var clsJs;  // Classe responsavel por montar um Json e eviar PHP
  var fd;     // Formulario para envio de dados para o PHP
  var msg;    // Variavel para guardadar mensagens de retorno/erro 
  var retPhp; // Retorno do Php para a rotina chamadora  
  clsJs   = jsString("lote");  
  clsJs.add("rotina"      , "selectBkpEmp"      );
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
    var jsBkpEmp={
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
                  ,"tamImp":"15"
                  ,"padrao":9}
        ,{"id":4  ,"labelCol":"DESCRICAO"
                  ,"tamGrd":"18em"
                  ,"tamImp":"76"
                  ,"padrao":9}
        ,{"id":5  ,"labelCol":"APELIDO"
                  ,"tamGrd":"10em"
                  ,"tamImp":"30"
                  ,"padrao":9}
        ,{"id":6  ,"labelCol":"CNPJ"
                  ,"tamGrd":"12em"
                  ,"tamImp":"25"
                  ,"padrao":9}
        ,{"id":7  ,"labelCol":"IE"
                  ,"tamGrd":"8em"
                  ,"tamImp":"15"
                  ,"padrao":9}
        ,{"id":8  ,"labelCol":"CODCDD"
                  ,"tamGrd":"7em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":9  ,"labelCol":"CODLGR"
                  ,"tamGrd":"3em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":10  ,"labelCol":"ENDERECO"
                  ,"tamGrd":"20em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":11  ,"labelCol":"NUMERO"
                  ,"tamGrd":"5em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":12  ,"labelCol":"CEP"
                  ,"tamGrd":"8em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":13  ,"labelCol":"BAIRRO"
                  ,"tamGrd":"10em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":14  ,"labelCol":"FONE"
                  ,"tamGrd":"8em"
                  ,"tamImp":"15"
                  ,"padrao":9}
        ,{"id":15  ,"labelCol":"CODETF"
                  ,"tamGrd":"3em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":16  ,"labelCol":"ALIQCOFINS"
                  ,"tamGrd":"6em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":17  ,"labelCol":"ALIQPIS"
                  ,"tamGrd":"6em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":18  ,"labelCol":"ALIQCSLL"
                  ,"tamGrd":"6em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":19  ,"labelCol":"ALIQIRRF"
                  ,"tamGrd":"6em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":20  ,"labelCol":"BCPRESUMIDO"
                  ,"tamGrd":"6em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":21  ,"labelCol":"ALIQIRPRESUMIDO"
                  ,"tamGrd":"6em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":22  ,"labelCol":"ALIQCSLLPRESUMIDO"
                  ,"tamGrd":"6em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":23  ,"labelCol":"ANEXOSIMPLES"
                  ,"tamGrd":"4em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":24  ,"labelCol":"CODETP"
                  ,"tamGrd":"4em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":25  ,"labelCol":"CODERM"
                  ,"tamGrd":"4em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":26  ,"labelCol":"SMTPUSERNAME"
                  ,"tamGrd":"20em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":27  ,"labelCol":"SMTPPASSWORD"
                  ,"tamGrd":"20em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":28  ,"labelCol":"SMTPHOST"
                  ,"tamGrd":"10em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":29  ,"labelCol":"SMTPPORT"
                  ,"tamGrd":"10em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":30  ,"labelCol":"CERTPATH"
                  ,"tamGrd":"30em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":31  ,"labelCol":"CERTSENHA"
                  ,"tamGrd":"20em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":32  ,"labelCol":"CERTVALIDADE"
                  ,"tamGrd":"20em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":33  ,"labelCol":"PRODHOMOL"
                  ,"tamGrd":"3em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":34  ,"labelCol":"CONTINGENCIA"
                  ,"tamGrd":"4em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":35  ,"labelCol":"EMP_CODERT"
                  ,"tamGrd":"4em"
                  ,"tamImp":"0"
                  ,"padrao":9}
        ,{"id":36  ,"labelCol":"REG"
                  ,"tamGrd":"4em"
                  ,"tamImp":"12"
                  ,"padrao":9}
        ,{"id":37  ,"labelCol":"ATIVO"
                  ,"tamGrd":"4em"
                  ,"tamImp":"15"
                  ,"padrao":9}
        ,{"id":38 ,"labelCol":"USUARIO"
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
      ,"width"          : "117em"               // Tamanho da table
      ,"height"         : "53em"                // Altura da table
      ,"relTitulo"      : "ESPIAO - EMPRESA" 	// Titulo do relatório
      ,"relOrientacao"  : "P"                   // Paisagem ou retrato
      ,"relFonte"       : "7"                   // Fonte do relatório
      ,"indiceTable"    : "ID"                  // Indice inicial da table
      ,"tamBotao"       : "15"                  // Tamanho botoes defalt 12 [12/25/50/75/100]
    }; 
    if( objBkpEmp === undefined ){          
      objBkpEmp=new clsTable2017("objBkpEmp");
    };  
    objBkpEmp.montarHtmlCE2017(jsBkpEmp);
    /////////////////////////////////////
    // Enchendo a grade com registros  //   
    /////////////////////////////////////
    jsBkpEmp.registros=objBkpEmp.addIdUnico(retPhp[0]["dados"]);
    objBkpEmp.montarBody2017();
    /////////////////////////////////////
    //   Marcando os campos alterados  //   
    /////////////////////////////////////
    if( exc.toUpperCase()=='N' )
      document.getElementById(jsBkpEmp.tbl).colAlterada();
  };  
};
