////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fContratoF10(opc,codCntt,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat(" SELECT A.CNTT_CODIGO CODIGO"                                      );
  clsStr.concat(",CASE A.CNTT_TIPO WHEN 'L' THEN 'LOCACAO' ELSE 'VENDA' END AS TIPO");
  clsStr.concat(",F.FVR_NOME AS CLIENTE"                                            );
  clsStr.concat(" FROM CONTRATO A"                                                  );
  clsStr.concat(" LEFT OUTER JOIN FAVORECIDO F ON A.CNTT_CODFVR=F.FVR_CODIGO"      );            
  let tblCntt     = "tblCntt";
  let divWidth  = "54em";
  let tblWidth  = "52em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "whr"  : 
          clsStr.concat(" {WHERE} (A.CNTT_CODFVR='"+objeto[key]+"')",true);        
          break;           
      };  
    };  
  };
  sql=clsStr.fim();
  //            
  if( opc == 0 ){            
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdCntt=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdCntt.Assoc=false;
    bdCntt.select( sql );
    console.log(sql);
    if( bdCntt.retorno=='OK'){
      var jsCnttF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"3em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"13em"  ,"fieldType":"int","ordenaColuna":"S"}
          ,{"id":2 ,"labelCol":"TIPO"      ,"tipo":"edt"  ,"tamGrd":"15em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":3 ,"labelCol":"CLIENTE"   ,"tipo":"edt"  ,"tamGrd":"15em"  ,"fieldType":"str","ordenaColuna":"S"}        
        ]
        ,"registros"      : bdCntt.dados              // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblCntt                   // Nome da table
        ,"div"            : 'cntt'
        ,"prefixo"        : "Cntt"                    // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "CONTRATO"    // Nome da tabela no banco de dados  
        ,"width"          : tblWidth                // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "CLIENTE"             // Indice inicial da table
        ,"tamBotao"       : "20"
      };
      if( objCnttF10 === undefined ){          
        objCnttF10         = new clsTable2017("objCnttF10");
        objCnttF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objCnttF10.focoF10=foco;  
        };
      };  
      var html          = objCnttF10.montarHtmlCE2017(jsCnttF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = divWidth;
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaCntt');
      document.getElementById('tblCntt').rows[0].cells[1].click();
      delete(ajudaF10);
      delete(objCnttF10);
    };
  }; 
  if( opc == 1 ){
    var bdCntt=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdCntt.Assoc=true;
    bdCntt.select( sql );
    return bdCntt.dados;
  };     
};