////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fPlacaF10(opc,codPlc,foco,topo,objeto){
  let clsStr = new concatStr();
  clsStr.concat("SELECT A.CNTP_PLACACHASSI AS PLACA"                                    );
  clsStr.concat("       ,VTP.VTP_NOME AS TIPO"                                          );
  clsStr.concat("       ,VMD.VMD_NOME AS MODELO"                                        );
  clsStr.concat("       ,VCL.VCL_ANO AS ANO"                                            );          
  clsStr.concat("  FROM CONTRATOPLACA A"                                                );
  clsStr.concat("  LEFT OUTER JOIN VEICULO VCL ON A.CNTP_PLACACHASSI=VCL.VCL_CODIGO"    );            
  clsStr.concat("  LEFT OUTER JOIN VEICULOCOR VCR ON VCL.VCL_CODVCR=VCR.VCR_CODIGO"     );
  clsStr.concat("  LEFT OUTER JOIN VEICULOTIPO VTP ON VCL.VCL_CODVTP=VTP.VTP_CODIGO"    );
  clsStr.concat("  LEFT OUTER JOIN VEICULOMODELO VMD ON VCL.VCL_CODVMD=VMD.VMD_CODIGO"  );
  let tblPlc     = "tblPlc";
  let divWidth  = "54em";
  let tblWidth  = "52em";
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "divWidth": 
          divWidth=objeto[key];
          break;
        case "tblWidth": 
          tblWidth=objeto[key];
          break;
        case "where"  : 
          clsStr.concat( objeto[key],true);        
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
    var bdPlc=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdPlc.Assoc=false;
    bdPlc.select( sql );
    if( bdPlc.retorno=='OK'){
      var jsPlcF10={
        "titulo":[
           {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"3em"   ,"fieldType":"chk"}                                
          ,{"id":1 ,"labelCol":"PLACA"     ,"tipo":"edt"  ,"tamGrd":"13em"  ,"fieldType":"str","ordenaColuna":"S"}
          ,{"id":2 ,"labelCol":"TIPO"      ,"tipo":"edt"  ,"tamGrd":"15em"  ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":3 ,"labelCol":"MODELO"    ,"tipo":"edt"  ,"tamGrd":"15em"  ,"fieldType":"str","ordenaColuna":"N"}
          ,{"id":4 ,"labelCol":"ANO"       ,"tipo":"edt"  ,"tamGrd":"4em"   ,"fieldType":"str","ordenaColuna":"N"}          
        ]
        ,"registros"      : bdPlc.dados              // Recebe um Json vindo da classe clsBancoDados
        ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
        ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
        ,"tbl"            : tblPlc                   // Nome da table
        ,"prefixo"        : "Plc"                    // Prefixo para elementos do HTML em jsTable2017.js
        ,"tabelaBD"       : "GRUPOMODELOPRODUTO"    // Nome da tabela no banco de dados  
        ,"width"          : tblWidth                // Tamanho da table
        ,"height"         : "39em"                  // Altura da table
        ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
        ,"tamBotao"       : "20"
      };
      if( objPlcF10 === undefined ){          
        objPlcF10         = new clsTable2017("objPlcF10");
        objPlcF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objPlcF10.focoF10=foco;  
        };
      };  
      var html          = objPlcF10.montarHtmlCE2017(jsPlcF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = divWidth;
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaPlc');
      document.getElementById('tblPlc').rows[0].cells[1].click();
      delete(ajudaF10);
      delete(objPlcF10);
    };
  }; 
  if( opc == 1 ){
    var bdPlc=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdPlc.Assoc=true;
    bdPlc.select( sql );
    return bdPlc.dados;
  };     
};