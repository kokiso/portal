////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// jsPub[0].usr_clientes) sao os clientes que o usuario pode ver          //
////////////////////////////////////////////////////////////////////////////
function fEquipamentoF10(opc,codGmp,foco,topo,objeto){
  let clsStr = new concatStr();
  let flag = false; 
  clsStr.concat("SELECT A.GMP_CODIGO as CODIGO, GM.GM_NOME as NOME, A.GMP_NUMSERIE as SERIE,GM.GM_CODGP as TIPO"); 
  clsStr.concat("  FROM GRUPOMODELOPRODUTO A"                                                );
  clsStr.concat("  LEFT OUTER JOIN GRUPOMODELO GM ON A.GMP_CODGM = GM.GM_CODIGO"       );            
  
  let tblEquip  = "tblEquip";
  let divWidth  = "54em";
  let tblWidth  = "52em";
  if( typeof objeto === 'object' ){ 
    for (var key in objeto) {
      switch( key ){
        case "codgm": 
          clsStr.concat(" {WHERE} (A.GMP_CODGM='"+objeto[key]+"')",true);
          break;
        case "codgp": 
          clsStr.concat(" {WHERE} ("+objeto[key]+")",true);
          break;
        case "codpe": 
          clsStr.concat(" {AND} (A.GMP_CODPE='"+objeto[key]+"')",true);
          break;
        case "codaut":
          clsStr.concat( " {AND} (A.GMP_CODAUT='"+objeto[key]+"')",true);        
          break; 
        case "tipo":
            clsStr.concat( " {AND} (GM.GM_GPSERIEOBRIGATORIO='"+objeto[key]+"')",true);    
          break;   
        case "divWidth": 
          divWidth=objeto[key];
          break;
        case "tbl": 
          tblEquip=objeto[key];
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
  console.log(sql);
  sql=clsStr.fim();

  var jsEquipF10 = null;
  //            
  if( opc == 0 ){              
    //////////////////////////////////////////////////////////////////////////////
    // localStorage eh o arquivo .php onde estao os select/insert/update/delete //
    //////////////////////////////////////////////////////////////////////////////
    var bdEquip=new clsBancoDados(localStorage.getItem('lsPathPhp'));
    bdEquip.Assoc=false;
    bdEquip.select( sql );
    if( bdEquip.retorno=='OK'){
        var jsEquipF10={
          "titulo":[
             {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"3em"   ,"fieldType":"chk"}                                
            ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"5em"   ,"fieldType":"int","formato":['i4'],"ordenaColuna":"S","align":"center"}
            ,{"id":2 ,"labelCol":"NOME" ,"tipo":"edt"  ,"tamGrd":"27em"  ,"fieldType":"str","ordenaColuna":"S"}
            ,{"id":3 ,"labelCol":"SERIE"     ,"tipo":"edt"  ,"tamGrd":"10em"  ,"fieldType":"str","ordenaColuna":"S"}
            ,{"id":4 ,"labelCol":"TIPO"   ,"tipo":"edt"  ,"tamGrd":"10em"  ,"fieldType":"str","ordenaColuna":"S"}                            
          ]
          ,"registros"      : bdEquip.dados              // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                     // Opção para numero registros/botão/procurar                       
          ,"checarTags"     : "N"                      // Somente em tempo de desenvolvimento(olha as pricipais tags)
          ,"tbl"            : tblEquip                   // Nome da table
          ,"div"            : 'tblEquip'               
          ,"prefixo"        : "Equip"                    // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "GRUPOMODELOPRODUTO"     // Nome da tabela no banco de dados  
          ,"width"          : tblWidth                 // Tamanho da table
          ,"height"         : "39em"                   // Altura da table
          ,"indiceTable"    : "SERIE"                  // Indice inicial da table
          ,"tamBotao"       : "20"
        };

      if( objEquipF10 === undefined ){          
        objEquipF10         = new clsTable2017("objEquipF10");
        objEquipF10.tblF10  = true;
        if( (foco != undefined) && (foco != "null") ){
          objEquipF10.focoF10=foco;  
        };
      };
      //console.log(jsEquipF10);
      var html          = objEquipF10.montarHtmlCE2017(jsEquipF10);
      var ajudaF10      = new clsMensagem('Ajuda',topo);
      ajudaF10.divHeight= '410px';  /* Altura container geral*/
      ajudaF10.divWidth = divWidth;
      ajudaF10.tagH2    = false;
      ajudaF10.mensagem = html;
      ajudaF10.Show('ajudaEquip');
      document.getElementById(tblEquip).rows[0].cells[3].click(); 
      delete(ajudaF10);
      delete(objEquipF10);
    };
  }; 
  if( opc == 1 ){
    var bdEquip=new clsBancoDados(localStorage.getItem("lsPathPhp"));
    bdEquip.Assoc=true;
    bdEquip.select( sql );
    return bdEquip.dados;
  };     
};