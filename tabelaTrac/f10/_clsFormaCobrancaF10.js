////////////////////////////////////////////////////////////////////////////
// opc=0 - Abre a janela F10                                              //
// opc=1 - Retorna somente o select para Janela F10 ou para blur do campo //
// foco  - Onde vai o foco quando confirmar                               //
// As 4 func se iniciadas com "_" uso a padrao aqui                       //
////////////////////////////////////////////////////////////////////////////
function clsFcF10(obj){
  ///////////////////////////////
  // Variavels publicas da classe
  ///////////////////////////////
  var pubLocal  = false;
  var pubTop    = 100; 
  var divHeight = '410px';
  var divWidth  = '54em';
  var tblWidth  = "52em";
  var tblHeight = "39em";
  var tblFc     = "tblFc";
  //
  var clsStr = new concatStr();
  clsStr.concat("SELECT A.FC_CODIGO AS CODIGO,A.FC_NOME AS DESCRICAO"  );
  clsStr.concat("  FROM FORMACOBRANCA A"                               );
  //
  /////////////////////////////////////////////////////////
  // Aqui pode ser alterado qquer variavel de toda a classe
  /////////////////////////////////////////////////////////  
  if( typeof obj === 'object' ){
    for (var key in obj) {
      switch( key ){
        case "pubLocal": 
          pubLocal = obj[key];
          break;
        case "tbl": 
          tblFc=objeto[key];
          break; 
      };
    };  
  };
  ////////////////////////////////
  // Iniciando os metodos publicos
  ////////////////////////////////
  this.fcFocus = function(obj){ 
    document.getElementById(obj.id).setAttribute("data-oldvalue",document.getElementById(obj.id).value); 
  }
  this.fcF10Click = function(obj,foco){   
    this.fFormaCobrancaF10(0,obj.id,foco,pubTop,{ativo:"S" } ); 
  }
  this.RetF10tblFc = function(arr){
    document.getElementById("edtCodFc").value  = arr[0].CODIGO;
    document.getElementById("edtDesFc").value  = arr[0].DESCRICAO;
    document.getElementById("edtCodFc").setAttribute("data-oldvalue",arr[0].CODIGO);
  }
  this.codFcBlur = function(obj,foco){
    let elOld = document.getElementById(obj.id).getAttribute("data-oldvalue");
    let elNew = jsConverte("#"+obj.id).upper();
    if( elOld != elNew ){
      let arr = this.fFormaCobrancaF10(1,obj.id,foco,pubTop,
        {codfc   : elNew
         ,ativo  : "S"} 
      ); 
      document.getElementById(obj.id).value     = ( arr.length == 0 ? "*"  : arr[0].CODIGO                    );   
      document.getElementById("edtDesFc").value = ( arr.length == 0 ? "*"  : arr[0].DESCRICAO                 );
      document.getElementById(obj.id).setAttribute("data-oldvalue",( arr.length == 0 ? "*" : arr[0].CODIGO )  );
    };
  }
  //
  //
  this.fFormaCobrancaF10 = function(opc,codFc,foco,topo,objeto){
    if( typeof objeto === 'object' ){
      for (var key in objeto) {
        switch( key ){
          case "codfc": 
            clsStr.concat(" {AND} (A.FC_CODIGO='"+objeto[key]+"')",true);
            break;
          case "ativo":
            clsStr.concat( " {AND} (A.FC_ATIVO='"+objeto[key]+"')",true);        
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
      var bdFc=new clsBancoDados(localStorage.getItem('lsPathPhp'));
      bdFc.Assoc=false;
      bdFc.select( sql );
      if( bdFc.retorno=='OK'){
        var jsFcF10={
          "titulo":[
             {"id":0 ,"labelCol":"OPC"       ,"tipo":"chk"  ,"tamGrd":"5em"   ,"fieldType":"chk"}                                
            ,{"id":1 ,"labelCol":"CODIGO"    ,"tipo":"edt"  ,"tamGrd":"6em"   ,"fieldType":"str","ordenaColuna":"S","align":"center"}
            ,{"id":2 ,"labelCol":"DESCRICAO" ,"tipo":"edt"  ,"tamGrd":"29em"  ,"fieldType":"str","ordenaColuna":"S"}
          ]
          ,"registros"      : bdFc.dados              // Recebe um Json vindo da classe clsBancoDados
          ,"opcRegSeek"     : true                    // Opção para numero registros/botão/procurar                       
          ,"checarTags"     : "N"                     // Somente em tempo de desenvolvimento(olha as pricipais tags)
          ,"tbl"            : tblFc                   // Nome da table
          ,"prefixo"        : "Fc"                    // Prefixo para elementos do HTML em jsTable2017.js
          ,"tabelaBD"       : "BANCO"                 // Nome da tabela no banco de dados  
          ,"width"          : tblWidth                // Tamanho da table
          ,"height"         : tblHeight               // Altura da table
          ,"indiceTable"    : "DESCRICAO"             // Indice inicial da table
        };
        if( objFcF10 === undefined ){          
          objFcF10         = new clsTable2017("objFcF10");
          objFcF10.tblF10  = true;
          objFcF10.tblF10Local = pubLocal;
          if( (foco != undefined) && (foco != "null") ){
            objFcF10.focoF10=foco;  
          };
        };  
        var html          = objFcF10.montarHtmlCE2017(jsFcF10);
        var ajudaF10      = new clsMensagem('Ajuda',topo);
        ajudaF10.divHeight= divHeight;
        ajudaF10.divWidth = divWidth;
        ajudaF10.tagH2    = false;
        ajudaF10.mensagem = html;
        ajudaF10.Show('ajudaFc');
        document.getElementById(tblFc).rows[0].cells[2].click();
        delete(ajudaF10);
        delete(objFcF10);
      };
    }; 
    if( opc == 1 ){
      var bdFc=new clsBancoDados(localStorage.getItem("lsPathPhp"));
      bdFc.Assoc=true;
      bdFc.select( sql );
      return bdFc.dados;
    };     
  }
};  