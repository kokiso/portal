(function(){
  var $doc = function(arg){
    return document.getElementById(arg);
  };
  window.$doc = $doc
})();

/*
(function(){
  var $ = function(arg){
    if( !(this instanceof $) ){
      return new $(arg);
    }
    this.myArg = arg;
  }
  
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Funcao separa o numero em duas partes do fim para o inicio, quando achar "." ou "," quebra o for e divide a string
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function fncSeparaValor(parametro,separadorDecimal){
    let str       = parametro.replace(/[^0-9-.,]/g,"");
    let tam       = str.length;
    let retorno   = {};
    for( i=tam; i>=1; i-- ){
      if( [",","."].indexOf( str[i] ) != -1 ){
        let soNumero=str.substring(0,i);
        retorno={  esquerdo : soNumero.replace(/[^0-9-]/g,"")
                  ,sep      : separadorDecimal
                  ,direito  : str.substring(i+1,tam) };
        break;
      };
    };
    if( esquerdo="" ){
      retorno={  esquerdo : str
                ,sep      : separadorDecimal                
                ,direito  : "00" };
    };  
    return retorno;
  };
  //////////////////////////////////////////////////////////////
  // Para recuperar valor usar getWidth, para setar apenas width
  // Para recuperar valor usar getValue, para setar apenas value
  //////////////////////////////////////////////////////////////
  $.fn = $.prototype = {
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // attr 
    // document.getElementById(el).setAttribute("data-oldvalue",document.getElementById(el).value); 
    // $(el).attr("data-oldvalue",$(el).getValue()); 
    ///////////////////////////////////////////////////////////////////////////////////////////////
    attr:function(key,val){
      document.getElementById(this.myArg).setAttribute(key,val);  
    },
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // display
    // document.getElementById(el).style.display  = ["block","none"];            
    // $(el).display( ["block","none" ] );  
    ///////////////////////////////////////////////////////////////////////////////////////////////    
    display:function(val){
      document.getElementById(this.myArg).style.display=val;  
    },
    //////////////////////////////////////////////////////////////////////////////////////////////////
    // getDolar - converte em float com separador decimal "."
    // a funcao fncSeparaValor retorna uma string pois pode ser usada tanto para .Dolar() como .Real()
    //////////////////////////////////////////////////////////////////////////////////////////////////
    getFloat:function(){
      let val=fncSeparaValor(document.getElementById(this.myArg).value,".");
      return parseFloat( val.esquerdo+val.sep+val.direito );
    },
    //////////////////////////////////////////////////////////////////////////////////////////////////
    // getInt() - converte em inteiro com opcao de zeros a esquerda
    //////////////////////////////////////////////////////////////////////////////////////////////////
    getInteger:function(zeros=0){
      let val=(document.getElementById(this.myArg).value).replace(/[^0-9-]/g,"");
      if( zeros>0 ){
        let f1=( (val + "").length < zeros ? (val + "").length : n );
        val = new Array(++zeros-f1).join(0)+val;  
      };
      return val; 
    },
    //////////////////////////////////////////////////////////////////////////////////////////////////
    // getOption() - retorno o descritivo de um combobox
    //////////////////////////////////////////////////////////////////////////////////////////////////
    getOption:function(){
      return document.getElementById(this.myArg).options[document.getElementById(this.myArg).selectedIndex].text;
    },
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // getValue
    // document.getElementByIdel).setAttribute("data-oldvalue",document.getElementById(el).value); 
    // $(el).attr("data-oldvalue",$(el).getValue()); 
    ///////////////////////////////////////////////////////////////////////////////////////////////    
    getValue:function(){
      return document.getElementById(this.myArg).value;
    },
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // value
    // document.getElementById(el).value = "teste";    
    // $(el).value("teste");
    ///////////////////////////////////////////////////////////////////////////////////////////////    
    value: function(str){
      document.getElementById(this.myArg).value=str;
      return this;
    },
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // width
    // document.getElementById(el).style.width="20px";
    // $(el).width("20px");
    ///////////////////////////////////////////////////////////////////////////////////////////////    
    width:function(val){
      document.getElementById(this.myArg).style.width=val;  
    }
  };
  window.$ = $
})();
*/
/*
(function(){
  //https://github.com/andrew8088/dome/blob/master/src/dome.js
  var lib = function(arg){
    if(!(this instanceof lib)){
      return new lib(arg);
    }
    this.myArg = arg;
  }
  lib.fn = lib.prototype = {
    //Função que esconde elementos HTML com o Atributo display:none;.
    esconde: function(){
      document.querySelector(this.myArg).setAttribute('style','display:none');
      return this;
    },
    //Função que remove o atributo style e todos os atributos contidos nela.
    reset: function(){
      document.querySelector(this.myArg).removeAttribute("style");
      return this;
    },
    valor: function(str){
      document.getElementById(this.myArg).innerHTML=str;
      return this;
    },
    attrValue: function(str){
      document.getElementById(this.myArg).value=str;
      return this;
    },
    caixaAlta: function(){
      ///////////////////////////////////////////////////
      // Olhando aqui se é um input( value ou innerHTML )
      ///////////////////////////////////////////////////
      var arr=document.querySelectorAll("input#"+this.myArg);
      if( arr.length>0 )
        document.getElementById(this.myArg).value=document.getElementById(this.myArg).value.toUpperCase();
      return this;
    }
  }
  window.lib = lib;//,window.$ = blJs;
})();

//Esconde a Div
//blJs("div").esconde();
//Remove o atributo style da tag em 2 segundos fazendo aparecer a Div com a frase contida nela
//setTimeout(function(){blJs("div").reset()}, 2000);
//blJs("minhaDiv").valor('jose').maiuscula();
*/
