//******************************************************************************
//** SITE
//** Caracteres especiais http://www.w3schools.com/charsets/ref_utf_symbols.asp
//** http://guilhermemuller.com.br/tutoriais/formularios/2.html
//** https://css-tricks.com/float-labels-css/ - BOMMMMMMMMMMMMM
//** http://pt.stackoverflow.com/questions/13895/passar-lista-de-objetos-entre-arquivos - passar lista obj
//** https://www.youtube.com/watch?v=h8KgFB4nhL4&list=PLHO9UhS3tkPTvd2ZD86Kf69lXOQlLlfnX&index=32  curso js
//** http://desenvolvimentoparaweb.com/javascript/conhecimentos-essenciais-javascript-para-quem-ja-usa-jquery/ equivalencia Jquery/JavaScript
//** http://www.w3schools.com/js/tryit.asp?filename=tryjs_json_parse testar codigo
//** https://search.google.com/structured-data/testing-tool?hl=pt-BR Validar JSON
//************************************
//** RESUMO DE document.getElementById
//************************************
function $doc(element){
  element = document.getElementById(element);
  return element;
};      
//***************************
//**APENAS PARA MENU OPÇÕES
//***************************
document.addEventListener("DOMContentLoaded", function(){
  /////////////////////////////////////////////////
  // Onde usado grafico esta é a definição de cores
  // http://erikasarti.net/html/tabela-cores/
  /////////////////////////////////////////////////  
  if( document.getElementsByTagName('verGrafico') != undefined ){
    arrFill  = [ "rgba(143,188,143,0.5)" ,"rgba(151,187,205,0.5)" ,"rgba(0,206,209,0.5)"
                ,"rgba(160,82,45,0.5)"   ,"rgba(205,133,63,0.5)"  ,"rgba(46,139,87,0.5)"
                ,"rgba(100,149,237,0.5)" ,"rgba(95,158,160,0.5)"  ,"rgba(205,92,92,0.5)"
                ,"rgba(143,188,143,0.5)" ,"rgba(151,187,205,0.5)"];
    arrBorder= [ "rgba(143,188,143,1)"   ,"rgba(151,187,205,1)"   ,"rgba(0,206,209,1)"
                ,"rgba(160,82,45,1)"     ,"rgba(205,133,63,1)"    ,"rgba(46,139,87,1)"
                ,"rgba(100,149,237,1)"   ,"rgba(95,158,160,1)"    ,"rgba(205,92,92,1)"
                ,"rgba(143,188,143,1)"   ,"rgba(151,187,205,1)"];
  };
  ////////////////////////////////////////  
  // Desabilitando o campo input do titulo
  ////////////////////////////////////////
  if( document.getElementById('titulo') != undefined ){
    document.getElementById("titulo").disabled=true;
  };  
  ////////////////////////////////////////////////////////////
  // Pelo usuario é disponibilizado a opção ADM para registros
  ////////////////////////////////////////////////////////////
  if( document.getElementById('cbReg') != undefined ){
    if(jsPub[0].usr_admpub=="A"){
      var ceOpt 	= document.createElement("option");        
      ceOpt.value = "A";
      ceOpt.text  = "ADMINISTRADOR";
      document.getElementById("cbReg").appendChild(ceOpt);
    };
  };
  if( document.getElementsByClassName('sub-menuSmall')[0] != undefined ){
    var incCounter=(jsPub[0].NAVEGADOR=="CHROME" ? 2 : 6);
    document.getElementsByClassName('moTituloMenu')[0].addEventListener('click',function(){
      var els =  this.querySelectorAll("li.moItemMenu");
      var elQI=  this.querySelectorAll("span.moQtosItens")[0];
      elQI.innerHTML=els.length;    
      
      //+6 Para poder fazer a ultima borda;
      var tamMax =(els.length*(this.offsetHeight+6));
      var elUL =  this.getElementsByTagName('ul')[0];   //this = <li class="moTituloMenu">
      elUL.style.overflow='hidden';
      if( (elUL.style.display=='none') || (elUL.style.display=='') ){
        elUL.style.height='0px';
        elUL.style.display='block'
        //setInterval
        var counter = 0;
        var time = window.setInterval( function () {
          counter+=incCounter;
          elUL.style.height=counter+'px';
          if ( counter >= tamMax ) {
            window.clearInterval( time );
          };
        },1);
        //
      } else {
        elUL.style.display='none';
      }
    });
  };
}, false);
//////////////////
// XMLHttpRequest
/////////////////
function requestPedido(arquivo,formulario){
  var retorno;
  var xhttp = new XMLHttpRequest();  
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      retorno=this.responseText;
    };
    if( this.status == 404 ){
      var help = new clsMensagem('Retorno');
      help.mensagem='URL NÃO LOCALIZADA!';
      help.Show(); 
      retorno='erro'; 
    };  
  };  
  xhttp.open('POST',arquivo,false);
  xhttp.send(formulario);
  return retorno;
};
//////////////////////////////////////////////////////////
// Criar "n" elementos popover no formulario para exibição
//////////////////////////////////////////////////////////
function adicionarDataToggle(){
  var elementsPopover = document.querySelectorAll('[data-toggle]');
  // Anexar um popover para cada
  for (var i = 0; i < elementsPopover.length; i++){
    new Popover(elementsPopover[i], {
      placement: 'top', //string
      animation: 'slideNfade', // CSS class
      delay: 100, // integer
      dismissible: false, // boolean
    })
  };        
};
function fncAgora(param){
  let dt = new Date();
  let clsStr = new concatStr();
  if( param=="dd/mm/yyyy-hh:mm"){
    clsStr.concat(jsNmrs(dt.getDate()).emZero(2).ret());
    clsStr.concat("/");
    clsStr.concat(jsNmrs(dt.getMonth()+1).emZero(2).ret());
    clsStr.concat("/");
    clsStr.concat(jsNmrs(dt.getFullYear()).emZero(4).ret());
    clsStr.concat("-");
    clsStr.concat(jsNmrs(dt.getHours()).emZero(2).ret());
    clsStr.concat(":");
    clsStr.concat(jsNmrs(dt.getMinutes()).emZero(2).ret());
  };
  if( param=="dd/mm/yyyy"){
    clsStr.concat(jsNmrs(dt.getDate()).emZero(2).ret());
    clsStr.concat("/");
    clsStr.concat(jsNmrs(dt.getMonth()+1).emZero(2).ret());
    clsStr.concat("/");
    clsStr.concat(jsNmrs(dt.getFullYear()).emZero(4).ret());
  };
  return clsStr.fim();
}
function validaCompetencia(valor){
  let mes     = valor.substring(0,3).toUpperCase();
  let ano     = valor.substring(4,6);
  let codcmp  = "";
  let erro    = "ok";
  if( ["JAN","FEV","MAR","ABR","MAI","JUN","JUL","AGO","SET","OUT","NOV","DEZ"].indexOf(mes) == -1 )
    erro="MÊS ACEITA APENAS JAN/FEV/MAR/ABR/MAI/JUN/JUL/AGO/SET/OUT/NOV/DEZ";
  if( ["18","19","20","21","22","23","24","25","26","27","28","29","30"].indexOf(ano) == -1 )
    erro="ANO ACEITA APENAS 18/19/20/21/22/23/24/25/26/27/28/29/30";
  if( erro != "ok" ){
    return {"erro"    : erro
            ,"codcmp" : 0
            ,"descmp" :"XXX/99"
           };
  } else {
    switch (mes) {
      case "JAN": codcmp="20"+ano+"01";break;
      case "FEV": codcmp="20"+ano+"02";break;
      case "MAR": codcmp="20"+ano+"03";break;
      case "ABR": codcmp="20"+ano+"04";break;
      case "MAI": codcmp="20"+ano+"05";break;
      case "JUN": codcmp="20"+ano+"06";break;
      case "JUL": codcmp="20"+ano+"07";break;
      case "AGO": codcmp="20"+ano+"08";break;
      case "SET": codcmp="20"+ano+"09";break;
      case "OUT": codcmp="20"+ano+"10";break;
      case "NOV": codcmp="20"+ano+"11";break;
      case "DEZ": codcmp="20"+ano+"12";break;
    };
    return {"erro"    : "ok"
            ,"codcmp" : codcmp
            ,"descmp" : jsStr(valor).upper().alltrim().ret()
    };
  };
}
function fncEmMinuto(param,vlr){
  if( param=="dd/mm/yyyy-hh:mm" ){
    let splt=vlr.split("-");
    let spltData=splt[0].split("/");
    let spltHora=splt[1].split(":");
    
    var ret=0;
    ret=parseInt(spltData[2].toString()+spltData[1].toString()+spltData[0].toString()+spltHora[0].toString()+spltHora[1].toString());
  };
  if( param=="dd/mm/yyyy"){
    let splt=vlr.split("/");
    var ret=0;
    ret=parseInt(splt[2].toString()+splt[1].toString()+splt[0].toString());
  };
  return ret;    
}
/*
* Exemplo ERP_SatConsultaFin2017.php
* data sempre no formato dd/mm/yyyy
* Se o length for 6 é que estou passando uma competencia YYYYMM
* data[4] quando recebe valor de um selcet yyyy-mm-dd
* -retSomadias ( SB_TClienteGrupo.php )
*   (1) var clsData=jsDatas('19/09/2017').retSomarDias(15);  
*       var ret=clsData.retYYYYMM();
*   (2) var ret=jsDatas('21/09/2017').retSomarDias(30).retYYYYMM() 
*
*/
function jsDatas(data){
  if( typeof(data)=="number" ){
    if( data.toString().length != 6 ){
      var hoje = new Date();
      hoje.setDate(hoje.getDate()+data);
      data = (hoje.getDate()<10 ? '0'+hoje.getDate() : hoje.getDate())+'/'+
            ((hoje.getMonth()+1)<10 ? '0'+(hoje.getMonth()+1) : (hoje.getMonth()+1))+'/'+
            hoje.getFullYear();
    } else {
      data='01/'+data.toString().substring(4,6)+'/'+data.toString().substring(0,4);
    };       
  } else {
    /////////////////////////////////////////////////////////////////////////////////////////
    // Aqui é se vier o parametro "edtData" e não document.getElementById("edtData").value //
    /////////////////////////////////////////////////////////////////////////////////////////
    if( (typeof(data)=="string") && (['0','1','2','3','4','5','6','7','8','9'].indexOf(data[0]) == -1) ){
      var total = document.getElementsByTagName('input').length;
      for(var i = 0; i < total; i++) {
        if( document.getElementsByTagName('input')[i].id == data ){
          data=document.getElementById(data).value;
          break;
        };  
      };
    };
    //////////////////////////////////////////
    // Se vier do banco de dados 2017-03-28 //  
    //////////////////////////////////////////
    if( data[4]=='-' ){
      var splt=data.split('-');
      data=splt[2]+'/'+splt[1]+'/'+splt[0];
    };        
  };
  return {
    data     : data
    ,anoYY: function(){ return this.data.substring(8,10); }
    ,anoYYYY: function(){ return this.data.substring(6,10); }
    ,dia: function(){ 
      return this.data.substring(0,2); 
    }
    ,mes: function(){ return this.data.substring(3,5); }  
    ,mesExt: function(){
      var locMes=this.data.substring(3,5);     
      return ( locMes=='01' ? 'Janeiro' :
               locMes=='02' ? 'Fevereiro' :
               locMes=='03' ? 'Marco' :              
               locMes=='04' ? 'Abril' :
               locMes=='05' ? 'Maio' :              
               locMes=='06' ? 'Junho' :
               locMes=='07' ? 'Julho' :              
               locMes=='08' ? 'Agosto' :
               locMes=='09' ? 'Setembro' :              
               locMes=='10' ? 'Outubro' :
               locMes=='11' ? 'Novembro' : 'Dezembro');
    }
    ,retDDMMYYYY  : function(){ 
      return this.dia()+'/'+this.mes()+'/'+this.anoYYYY(); 
    }    
    ,retDDMM      : function(){ return this.dia()+'/'+this.mes(); }        
    ,retExt       : function(cidade){ return cidade+", "+this.dia()+" de "+this.mesExt()+" de "+this.anoYYYY(); }
    ,retMMDDYYYY  : function(){ return this.mes()+'/'+this.dia()+'/'+this.anoYYYY(); }
    ,retYYYYtMMtDD: function(){ return this.anoYYYY()+'-'+this.mes()+'-'+this.dia(); }  // "t" = traço
    ,retMMMYY     : function(){ return this.mesExt().substring(0,3).toUpperCase()+this.anoYY(); }
    ,retMMMbYY    : function(){ return this.mesExt().substring(0,3).toUpperCase()+'/'+this.anoYY(); }   // "b" = Barra
    ,retPriDiaMes : function(){ return '01/'+this.mes()+'/'+this.anoYYYY(); } 
    ,retSomarDias : function(n){
      var sdData  = new Date(this.anoYYYY(),(this.mes()-1),this.dia());
      sdData.setFullYear(sdData.getFullYear(),sdData.getMonth(),(sdData.getDate()+n));	
      this.data=(sdData.getDate()).EmZero(2)+'/'+(sdData.getMonth()+1).EmZero(2)+'/'+(sdData.getFullYear()).EmZero(4);
      return this;
    }   
    ,retUltDiaMes : function(){
      var udm = new Date(parseInt(this.anoYYYY()), parseInt(this.mes()), 0 ).getDate();
      return udm+'/'+this.mes()+'/'+this.anoYYYY();
    }   
    ,retYYYYMM    : function(){ return this.anoYYYY()+this.mes(); }    
    ,retDIAS      : function(){ return parseInt(this.dia())+(parseInt(this.mes())*30)+(parseInt(this.anoYYYY())*365); }
  };
};
////////////////////////////////////////////////////////////////////////////////////////////////////////////
// CHAMADA                                                                                                //
//                                                                                                        //
// elemento html | document.getElementById('edtValor').value   = "12300,87"                               //
// elemento html | document.getElementById('edtInteiro').value = "123"                                    //
// variavel      | fValor    = 12300.87                                                                   //
// variavel      | sValor    = ["12300.87" / "12300,87]"                                                  //
// variavel      | iInteiro  = 123                                                                        //
// variavel      | sInteiro  = "123"                                                                      //
//                                                                                                        //
// jsNmrs(document.getElementById('edtValor').value).dolar()         | retorno = [number] 12300.87        //
// jsNmrs('edtValor').dolar()                                        | retorno = [number] 12300.87        //
// jsNmrs(fValor).dolar()                                            | retorno = [number] 12300.87        //
// jsNmrs(sValor).dolar()                                            | retorno = [number] 12300.87        //
// jsNmrs().dolar(document.getElementById('edtValor').value)         | retorno = [number] 12300.87        //
// jsNmrs().dolar('edtValor')                                        | retorno = [number] 12300.87        //
// jsNmrs().dolar(fValor)                                            | retorno = [number] 12300.87        //
// jsNmrs().dolar(sValor)                                            | retorno = [number] 12300.87        //
//                                                                                                        //
// jsNmrs(document.getElementById('edtValor').value).real()          | retorno = [string] "12300,87"      //
// jsNmrs('edtValor').real()                                         | retorno = [string] "12300.87"      //
// jsNmrs(fValor).real()                                             | retorno = [string] "12300.87"      //
// jsNmrs(sValor).real()                                             | retorno = [string] "12300.87"      //
// jsNmrs().real(document.getElementById('edtValor').value)          | retorno = [string] "12300.87"      //
// jsNmrs().real('edtValor')                                         | retorno = [string] "12300.87"      //
// jsNmrs().real(fValor)                                             | retorno = [string] "12300.87"      //
// jsNmrs().real(sValor)                                             | retorno = [string] "12300.87"      //
//                                                                                                        //
// jsNmrs(document.getElementById('edtInteiro').value).inteiro()     | retorno = [number] 123             //
// jsNmrs('edtInteiro').inteiro()                                    | retorno = [number] 123             //
// jsNmrs(iInteiro).inteiro()                                        | retorno = [number] 123             //
// jsNmrs(sInteiro).inteiro()                                        | retorno = [number] 123             //
// jsNmrs().inteiro(document.getElementById('edtInteiro').value)     | retorno = [number] 123             //
// jsNmrs().inteiro('edtInteiro')                                    | retorno = [number] 123             //
// jsNmrs().inteiro(iInteiro)                                        | retorno = [number] 123             //
// jsNmrs().inteiro(sInteiro)                                        | retorno = [number] 123             //
//                                                                                                        //
// jsNmrs(document.getElementById('edtInteiro').value).emZero(6)     | retorno = [string] "000123"        //
// jsNmrs('edtInteiro').emZero(6)                                    | retorno = [string] "000123"        //
//                                                                                                        //
// jsNmrs(document.getElementById('edtValor').value).dec(1).dolar()  | retorno = [number] 12300.9         //
// jsNmrs().dec(1).dolar(document.getElementById('edtValor').value)  | retorno = [number] 12300.9         //
// jsNmrs().dec(1).dolar('edtValor')                                 | retorno = [number] 12300.9         //
//                                                                                                        //
// jsNmrs(document.getElementById('edtValor').value).dec(4).real()   | retorno = [string] "12300.8700"    //
// jsNmrs().dec(4).real(document.getElementById('edtValor').value)   | retorno = [string] "12300.8700"    //
// jsNmrs().dec(4).real('edtValor')                                  | retorno = [string] "12300.8700"    //
//                                                                                                        //
// jsNmrs('edtValor').dec(2).percentual(10).dolar()                  | retorno = [number] 1230.09         //
// jsNmrs('edtValor').dec(2).percentual(10).real()                   | retorno = [string] "12300,87"      //
// jsNmrs('edtValor').dec(4).percentual(10).real()                   | retorno = [string] "12300,8700"    //
//                                                                                                        //
// Outros exemplos usados ERP                                                                             //
// jsNmrs(valor).divide(div).dec(4).dolar().ret();                                                        //
// jsNmrs(valor).soma(div).emZero(4).ret()                                                                //  
// jsNmrs("edtVlrEvento").subtrai(document.getElementById("edtValorRetido").value).dolar().ret()          //
// jsNmrs(vlrInf-vlrOrigem).abs().real().ret();                                                           //
////////////////////////////////////////////////////////////////////////////////////////////////////////////
function jsNmrs(data){
  /////////////////////////////////////////////////////////////////////////////////////////
  // retorno = É atualizado a cada chamada de um metodo, seu valor se alterar ex:percentual
  /////////////////////////////////////////////////////////////////////////////////////////
  var retorno   = ""; 
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // retClasse = É o valor que vai ser retornado a classe, mas antes o retorno volta a ser igualado do data devido classe complementar
  // var foo = jsNmrs(valor)
  // var vlr = foo.percentual(10).dolar()  => nesta chamada o valor de retorno é alterado mas deve ser igualado com data para segunda chamada  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  var retClasse = "";
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Converte quando vem da classe principal jsNmrs(str) ou jsNmrs().dolar(str);
  // pubRetorno é para atualizar a variavel retorno,em caso de subtração/divisão...esta não pode ser alterada
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function converte(n,pubRetorno){
    if( n == undefined ){
      if( pubRetorno ){
        retorno=null;
      }  
      return null;
    } else {
      switch( typeof n ){
        case "number" :
          if( pubRetorno ){        
            retorno=n;
          }  
          return n;
        case "string" :
          var verSelect=true; //Se naum for input olho tb select
          var total = document.getElementsByTagName('input').length;
          for(var i = 0; i < total; i++) {
            if( document.getElementsByTagName('input')[i].id == n ) {
              verSelect=false;
              if( pubRetorno ){
                retorno=(document.getElementById(n).value).replace(/[^0-9-.,]/g,"").replace(",","."); //18nov2017 document.getElementById(n).value;
                return document.getElementById(n).value;
              } else {
                return (document.getElementById(n).value).replace(/[^0-9-.,]/g,"").replace(",",".");
              }; 
            };  
          };
          if( verSelect ){
            total = document.getElementsByTagName('select').length;
            for(var i = 0; i < total; i++) {
              if( document.getElementsByTagName('select')[i].id == n ) {
                if( pubRetorno ){
                  retorno=(document.getElementById(n).value).replace(/[^0-9-.,]/g,"").replace(",","."); //18nov2017 document.getElementById(n).value;
                  return document.getElementById(n).value;
                } else {
                  return (document.getElementById(n).value).replace(/[^0-9-.,]/g,"").replace(",",".");
                }; 
              };  
            };
          };
          if( pubRetorno ){
            retorno=n;
            return n;            
          } else {  
            return n.replace(/[^0-9-.,]/g,"").replace(",",".");
          }  
      };
    };  
  };
  /////////////////////
  // Iniciando a classe
  /////////////////////
  return {
     data       : converte(data,true)
    ,decimais   : 2
    ,dolar: function(obj){ 
      if( obj != undefined )
        retorno=converte(obj,true);
      retorno=retorno.toString();
      retorno=retorno.replace(/[^0-9-.,]/g,"").replace(",",".");
      var novaStr='';
      for( var pos=0;pos<retorno.length;pos++ ){
        if( ['0','1','2','3','4','5','6','7','8','9','-','.'].indexOf(retorno[pos]) == -1 ){
          throw "Aceito apenas -0123456789.!";
        } else {
          novaStr+=retorno[pos];
        };  
      };
      retorno=novaStr;
      retorno=parseFloat((parseFloat(retorno)).toFixed(this.decimais)); 
      return this;
    }
    ,abs:function(){
      retorno=retorno.toString();
      retorno = retorno.replace("-","");
      return this;
    }
    ,divide:function(d){
      d       = converte(d,false);
      retorno = ( parseFloat(retorno) / d );
      return this;
    } 
    ,soma:function(s){
      s       = converte(s,false);
      retorno = ( parseFloat(retorno) + parseFloat(s) );
      return this;
    } 
    ,subtrai:function(s){
      s       = converte(s,false);
      retorno = ( parseFloat(retorno) - s );
      return this;
    }    
    ,multiplica:function(m){
      m       = converte(m,false);
      retorno = ( parseFloat(retorno) * m );
      return this;
    }  
    ,real: function(obj){
      if( obj != undefined )
        retorno=converte(obj,true);
      retorno=retorno.toString();
      retorno=retorno.replace(/[^0-9-.,]/g,"").replace(",",".");
      var novaStr='';
      for( var pos=0;pos<retorno.length;pos++ ){
        if( ['0','1','2','3','4','5','6','7','8','9','-','.'].indexOf(retorno[pos]) == -1 ){
          throw "Aceito apenas -0123456789,!";
        } else {
          novaStr+=retorno[pos];
        };  
      };
      retorno=novaStr;
      retorno=parseFloat(retorno).toFixed(this.decimais).replace(".",","); 
      return this;      
    }
    ,inteiro: function(obj){ 
      if( obj != undefined )
        retorno=converte(obj,true);
      
      retorno=retorno.toString();
      var novaStr='';
      for( var pos=0;pos<retorno.length;pos++ ){
        if( ['0','1','2','3','4','5','6','7','8','9','-'].indexOf(retorno[pos]) == -1 ){
          throw "Aceito apenas -0123456789!";
        } else {
          novaStr+=retorno[pos];
        };  
      };
      retorno=novaStr;
      retorno=parseInt(retorno); 
      return this;
    }
    ////////////////////////////////////
    // define o numero de casas decimais
    ////////////////////////////////////
    ,dec:function(d){
      this.decimais=d;
      return this;
    }
    ////////////////////////////////////////////////
    // define o percentual aplicado em dolar ou real
    ////////////////////////////////////////////////
    ,percentual:function(p){
      retorno=( (parseFloat(retorno)*p)/100 );  //Obrigatorio pois se vier aliquota 0 tem que retornar 0.00
      return this;
    }
    ,emZero:function(n){
      if( (typeof retorno)=="number" )
        retorno=retorno.toString();

      var novaStr='';
      for( var pos=0;pos<retorno.length;pos++ ){
        if( ['0','1','2','3','4','5','6','7','8','9','-'].indexOf(retorno[pos]) != -1 )
          novaStr+=retorno[pos]; else break;
      }
      retorno=novaStr;
      retorno=( ((retorno==undefined) || (retorno.length==0)) ? "0" : retorno.replace(/\D/g,"") );
      var f1=( (retorno + "").length < n ? (retorno + "").length : n );
      retorno = new Array(++n-f1).join(0)+retorno;  
      return this;
    }
    ,sepMilhar(c){
      var n = retorno; 
      var c = isNaN(c = Math.abs(c)) ? 2 : c; 
      var s = n < 0 ? "-" : "";
      var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
      var j = (j = i.length) > 3 ? j % 3 : 0;
         retorno= s + (j ? i.substr(0, j) + '.' : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + '.') + (c ? ',' + Math.abs(n - i).toFixed(c).slice(2) : "");
      return this;
    }
    ,ret:function(){
      retClasse = retorno;
      retorno   = data;     //retornando o valor origem para nova conta ou formatação
      return retClasse;
    }
  };
};
////////////////////////////////////
// Habilitar/Desabilitar um campo //
////////////////////////////////////
function jsCmpAtivo(data){
  return {
    remove(str){
      let arr=str.split(" ");
      arr.forEach(function(reg){
        document.getElementById(data).classList.remove(reg);   
      })  
      return this;
    }
    ,add(str){
      let arr=str.split(" ");
      arr.forEach(function(reg){
        document.getElementById(data).classList.add(str);
      })  
      return this;
    }
    ,disabled(str){
      document.getElementById(data).disabled=str;
      return this;
    }
    ,foco(str){
      document.getElementById(str).foco();
      return this;
    }
    ,cor(str){
      document.getElementById(data).style.color=str;
      return this;      
    }
  }  
};  
////////////////////////
// Formata uma string //
////////////////////////
function jsStr(data){
  ////////////////////////////////////////////////////////////////////////////////////////////
  // retorno = É atualizado a cada chamada de um metodo, seu valor se alterar ex:percentual //
  ////////////////////////////////////////////////////////////////////////////////////////////
  var retorno   = ""; 
  //////////////////////////////////////////////////////////////////////////////////
  // Converte quando vem da classe principal jsNmrs(str) ou jsNmrs().dolar(str);  //
  //////////////////////////////////////////////////////////////////////////////////
  function converte(n){
    if( n==undefined ){
      n="";
    }
    retorno=n;
    ///////////////////////////////////////////////////
    // Procurando se veio um element ou uma variavel //
    ///////////////////////////////////////////////////
    var total = document.getElementsByTagName('input').length;
    for(var i = 0; i < total; i++) {
      if( document.getElementsByTagName('input')[i].id == n ) {
        retorno=document.getElementById(n).value;
      };  
    };
    ////////////////////////////////////////////////////
    // Padrão da função tirar aspas e remover acentos //
    ////////////////////////////////////////////////////
    retorno=removeAcentos(retorno.replace(/'/g, ""));   //remoceAcentos+tira aspas  
    retorno=retorno.replace(/^\s+/,"");                 //ltrim
    retorno=retorno.replace(/\s+$/,"");                 //rtrim
    return retorno;
  };
  ////////////////////////
  // Iniciando a classe //
  ////////////////////////
  return {
     data       : converte(data)
    ,alltrim(){
      retorno=retorno.split(" ").join("");  
      return this;
    } 
    ,upper(){
      retorno=retorno.toUpperCase();
      return this;
    }
    ,lower(){
      retorno=retorno.toLowerCase();
      return this;
    }
    ,soNumeros(){
      retorno=retorno.replace(/\D/g,"");
      return this;
    }
    ,tamMax(i){
      if( retorno.length>i ){
        retorno=retorno.substring(0,i);
      };
      return this;
    }
    ,ret:function(){
      return retorno;
    }
  };
};
function mascaraNumero(masc,quem,evento,letdig){
  if (quem.selectionStart!=quem.selectionEnd)
    return;
  var cursor  = quem.selectionStart;
  var texto   = quem.value;
  switch( letdig ){
    case "dig":
      texto = texto.replace(/\D/g,'');
      break;
    case "let":
      texto = texto.replace(/[^a-zA-Z]/g,'');
      break;
    case "letdig":
      texto = texto.replace(/[^a-zA-Z0-9]/g,'');
      break;
  };
  var l       = texto.length;
  var lm      = masc.length;
  if( window.event ) {                  
      id = evento.keyCode;
  } else if( evento.which ){                 
      id = evento.which;
  };

  if( evento.shiftKey )
    return
  
  cursorfixo=false;
  if( cursor < l )
    cursorfixo=true;
  var livre = false;
  //////////////////////
  // KEY_BACK_TAB  = 8;        
  // KEY_TAB       = 9;        
  // KEY_ENTER     = 13;        
  // KEY_SH_TAB    = 16;        
  // KEY_ESC       = 27;        
  // KEY_SPACE     = 32;        
  // KEY_END       = 35;
  // KEY_BEGIN     = 36;
  // KEY_LEFT      = 37;
  // KEY_UP        = 38;
  // KEY_RIGHT     = 39;
  // KEY_DOWN      = 40;        
  // KEY_DEL       = 46;         
  //////////////////////
  if( id == 46 ) {
    cursor  = quem.selectionStart;
    return;
  }  
  
  if( id == 16 || id == 19 || (id >= 33 && id <= 40) )
    livre = true;
  
  if( !livre ){
    if( id!=8 ){
      quem.value="";
      var j=0;
      for( var i=0;i<lm;i++ ){
        if( masc.substr(i,1)=="#" ){
          quem.value+=texto.substr(j,1);
          j++;
        }else if( masc.substr(i,1)!="#" ){
          quem.value+=masc.substr(i,1);
        }
        if( id!=8 && !cursorfixo )
          cursor++;
        if( (j)==l+1 )
          break;
      }; 	
    };
  };
  quem.setSelectionRange(cursor, cursor);
}
function jsConverte(data){
  ////////////////////////////////////////////////////////////////////////////////////////////
  // retorno = É atualizado a cada chamada de um metodo, seu valor se alterar ex:percentual //
  ////////////////////////////////////////////////////////////////////////////////////////////
  var retorno = ""; 
  var compara = ["",""];

  //////////////////////////////////////////////////////////////////////////////////
  // Converte quando vem da classe principal jsNmrs(str) ou jsNmrs().dolar(str);  //
  // # = getElementById
  //////////////////////////////////////////////////////////////////////////////////
  function converte(n){
    if( n==undefined ){
      retorno="";
    } else {
      switch( n[0] ){
        case "#":
          n=n.substring(1);
          if( document.getElementById(n) ){
            retorno=document.getElementById(n).value;
          } else {
            throw "NAO LOCALIZADO ELEMENTO "+n+" PARA ATRIBUIR VALOR";
          };    
          break;
          
        default :  retorno=n; break;       
      };
      ////////////////////////////////////////////////////
      // Padrão da função tirar aspas e remover acentos //
      ////////////////////////////////////////////////////
      if( typeof(retorno) != "number" ){
        retorno=removeAcentos(retorno.replace(/'/g, ""));   //remoceAcentos+tira aspas  
        retorno=retorno.replace(/^\s+/,"");                 //ltrim
        retorno=retorno.replace(/\s+$/,"");                 //rtrim
      };
    };
    return retorno;
  };
  ////////////////////////
  // Iniciando a classe //
  ////////////////////////
  return {
     data       : converte(data)
    ,alltrim(){
      return retorno.split(" ").join("");  
    } 
    ,coalesce(str){
      if( (retorno==undefined) || (retorno==null) )
        retorno=str;
      return retorno;      
    }  
    ,datavalida(formato="dd/mm/yyyy"){
      let ret   = true;
      let data  = retorno.replace(/[^0-9\/]/g,"");
      if( data.length != 10 ){
        ret=false;
      } else {  
        let splt  = retorno.split('/');
        splt[0]=parseInt(splt[0]);
        splt[1]=parseInt(splt[1]);
        splt[2]=parseInt(splt[2]);
        let d     = new Date(splt[2], (splt[1]-1) , splt[0]);
        
        if( (d.getDate() != splt[0]) || ((d.getMonth()+1) != splt[1]) || (d.getFullYear() != splt[2]) )
          ret=false;   
      };  
      return ret;
    }  
    ,dolar(){
      compara[0]=retorno.length;
      compara[1]=retorno.replace(/[^0-9-.,]/g,"").replace(",",".");
      if( compara[0]==(compara[1]).length )
        return compara[1];
      else
        throw "VALOR RECEBIDO COM PARAMETRO NÃO É UM NUMERO";
    }  
    ,emZero(n){
      let f1=( (retorno + "").length < n ? (retorno + "").length : n );
      n = new Array(++n-f1).join(0)+retorno;
      return n;      
    }
    ,formato(frmt){
      switch( frmt ){
        case "mm/dd/yyyy" :
          return retorno.replace(/\D/g,"").replace(/(\d{2})(\d{2})(\d{4})/,"$2/$1/$3");   
          break;
      };
    }    
    ,inteiro(){
      return parseInt( retorno="" ? "0" : retorno.replace(/[^0-9]/g,"") );
    }  
    ,lower(){
      return retorno.toLowerCase();
    }
    ,soNumeros(){
      return retorno.replace(/\D/g,"");
    }
    ,tamMax(i){
      if( retorno.length>i ){
        retorno=retorno.substring(0,i);
      };
      return retorno;
    }
    ,upper(){
      return retorno.toUpperCase();
    }
  };
};
////////////////////////////////////
// Formatar campo somente inteiro //
////////////////////////////////////
function mascaraInteiro(inteiro){
  //////////////////////////////////////
  // Firefox não aceita keyCode       //
  // Chrome aceita keyCode e charCode //
  //////////////////////////////////////
  switch( inteiro.charCode ){
    case 48 :  
    case 49 :  
    case 50 :  
    case 51 :  
    case 52 :  
    case 53 :  
    case 54 :  
    case 55 :  
    case 56 :  
    case 57 :  return true; break;    
    case 0  :  return true; break; 
    default :  return false; break;     
  }  
};
function retornarZIndex(){
  let divs      = document.getElementsByTagName('div');
  let di        = divs.length;
  let maior     = 0;
  let atual     = 0;
  for( var i = 0; i < di; i++) {
    atual=window.getComputedStyle(divs[i]).getPropertyValue("z-index");      
    if( atual != "auto" ){
      atual=parseInt(atual);
      if( atual>maior )
        maior=atual;  
    };
  };  
  divs      = document.getElementsByTagName('form');
  di        = divs.length;
  for( var i = 0; i < di; i++) {
    atual=window.getComputedStyle(divs[i]).getPropertyValue("z-index");    
    if( atual != "auto" ){
      atual=parseInt(atual);
      if( atual>maior )
        maior=atual;  
    };
  };
  return (maior+1);  
};
//Retorno um JSON de uma table
function tableJson(tbl) {
  var el      = '';
  var nl      = 0;
  var nc      = 0;
  var arrTit  = [];
  var cntd    = '';
  var retorno = '[';
  
  el = document.getElementById(tbl).getElementsByTagName('thead')[0].getElementsByTagName('tr');
  nc=el.length;
  for( var col=0; col<nc; col++ )
    arrTit.push( (el[col].innerHTML).toUpperCase() );
  //
  el = document.getElementById(tbl).getElementsByTagName('tbody')[0];
  nl  = el.rows.length;
  if( nl>0 ){
    nc  = el.rows[nl-1].cells.length;
    for( var lin=0; lin<nl; lin++ ){
      retorno+=( lin==0 ? '{' :',{' );
      for( var col=0; col<nc; col++ ){
        cntd=el.rows[lin].cells[col].innerHTML;
        if( cntd.substring(0,1)=='<' ) 
          cntd='';
        retorno +=( col==0 ? '' :',' )+'"'+arrTit[col]+'":"'+cntd+'"';
      };
      retorno+='}';
    };  
    retorno+=']';
    return retorno;
  } else {
    /* retornando um json com tam 0 exemplo ERP_SatConsultaFin.php */
    return '[]';
  };
}; 
//Coloca a impressão em tela
function mostrarImpressao(imp){
  var formReport  ='';
  var campoSql    ='';
  formReport = document.createElement("form")
  formReport.setAttribute("name", "relatorio");
  formReport.setAttribute("id", "relatorio");
  formReport.setAttribute("method", "post"); 
  formReport.setAttribute("target", "_blank"); 
  formReport.setAttribute("action", "imprimirsql.php"); 
  campoSql = document.createElement('input');
  campoSql.setAttribute('type','hidden');
  campoSql.setAttribute('name','sql');
  campoSql.setAttribute('id','sql');
  campoSql.setAttribute('value',imp);
  formReport.appendChild(campoSql);   
  document.body.appendChild(formReport);    
  formReport.submit();
  formReport.remove();    
};
//Function para marcar/desmarcar linhas checadas 
function linhaChecada(chk,col){
  var el=chk.parentNode.parentNode; //Leva do chk->td->tr
  (el.classList.contains('corGradeParCheck') ? 
    el.classList.remove('corGradeParCheck') : el.classList.add('corGradeParCheck') );
};
////////////////
// PROTOTYPES //
////////////////
HTMLElement.prototype.foco=function(){
  this.focus();
  this.select();
};
String.prototype.alltrim=function(){
  return this.split(" ").join("");
};
Number.prototype.EmZero = function(n){
  f1=( (this + "").length < n ? (this + "").length : n );
  return n = new Array(++n-f1).join(0)+this;  
};
Number.prototype.sepNB = function(c){
/*  
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
*/   
var n = this; 
var c = isNaN(c = Math.abs(c)) ? 2 : c; 
var s = n < 0 ? "-" : "";
var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
var j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + '.' : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + '.') + (c ? ',' + Math.abs(n - i).toFixed(c).slice(2) : "");
};
String.prototype.ltrim = function () { 
  return this.replace(/^\s+/,""); 
};
String.prototype.replaceAll=function(find,replacement){
  return this.split(find).join(replacement);
};
String.prototype.rtrim = function () { 
  return this.replace(/\s+$/,""); 
};
String.prototype.soNumeros=function(){
  return this.replace(/\D/g,"");
};
//
String.prototype.tiraAspas=function(){
  return this.replace(/'/g, "");
};
//O IE não tem suporte ao remove, solução foi criar um prototype
if (!('remove' in Element.prototype)) {
  Element.prototype.remove = function() {
    this.parentNode.removeChild(this);
  };
};
///////////////////////////////////////
// Preenche com valores campos forms //
// str é o element container         //  
///////////////////////////////////////
HTMLElement.prototype.newRecord = function(nrStr,nrFoco){
  var total = this.getElementsByTagName('input').length;
  let elem;
  for(var i = 0; i < total; i++) {
    if( this.getElementsByTagName('input')[i].getAttribute(nrStr) != null ){
      elem=this.getElementsByTagName('input')[i].getAttribute(nrStr);
      if( elem.substring(0,4)=="eval" ){
        let splt=elem.split(" ");  
        this.getElementsByTagName('input')[i].value=eval(splt[1]);
      } else {
        switch(elem){
          case "nrHoje":
            this.getElementsByTagName('input')[i].value=jsDatas(0).retDDMMYYYY();
            break;
          default:
            this.getElementsByTagName('input')[i].value=this.getElementsByTagName('input')[i].getAttribute(nrStr);
            break;
        };
      };  
    };  
  };
  if( nrFoco != undefined )
    document.getElementById(nrFoco).foco();
};  
HTMLElement.prototype.formataEdtDireita = function(){
  var arr =  this.getElementsByClassName("edtDireita");
  for( var ii=0; ii<arr.length; ii++ ){
    arr[ii].addEventListener("blur", function(){  
      this.value = cmp.floatNB(this.value);
    });      
  };
};
//*********************************************
//** PROTOTYPES TABLE
//*********************************************
//Para rotina espiao
//Pega todas as linhas e compara se a coluna atual é diferente da coluna anterior
HTMLElement.prototype.colAlterada = function(retornar){
  var nl  = this.rows.length;             //** numero de linhas
  var nc  = this.rows[nl-1].cells.length; //** numero de colunas
  for(var li = 2; li < nl; li++){
    for(var ci = 2; ci < nc; ci++){
      if( this.rows[li].cells[ci].innerHTML != this.rows[(li-1)].cells[ci].innerHTML )
        this.rows[li].cells[ci].classList.add('corVermelho');
    };      
  };
};
//Pega a coluna que contem o checkbox
HTMLElement.prototype.qualColuna = function(nc){  
  for(var ci = 0; ci < nc; ci++){
    el=this.rows[1].cells[ci].children;
    if ((el[0]!=undefined) && (el[0].checked!=undefined)){
      return ci;
      break;
    };
  };
};
//Pega uma coluna pelo titulo
HTMLElement.prototype.colunaTitulo = function(titulo){  
  //var nl     = this.rows.length;              /* numero de linhas */
  var nc     = this.rows[0].cells.length;  /* numero de colunas */
  for(var ci = 0; ci < nc; ci++){
    if( this.rows[0].cells[ci].innerHTML==titulo ){
      return ci;
      break;
    };
  };
};
// Remove as linhas de registros checked
HTMLElement.prototype.apagaChecados = function(){
  var nl      = this.rows.length;             // numero de linhas
  var nc      = this.rows[nl-1].cells.length; // numero de colunas
  var qtd     = 0;                            // quantas linhas checadas
  var coluna  = this.qualColuna(nc);          // achando a coluna que esta o checkbox
  //Excluindo
  for(var li = (nl-1); li >= 1; li--){  
    el=this.rows[li].cells[coluna].children;
    ////////////////////////////////////////////////////////////////////////////////////
    // Este if é que tenho lugares onde quando adiciono uma linha esta vem sem o checked
    ////////////////////////////////////////////////////////////////////////////////////
    if( el.length==0 )
      continue;
    /**/
    if (el[coluna].checked){
      this.deleteRow(li);
      qtd++;
    }
  }
};
//Retira a marca de checked
HTMLElement.prototype.retiraChecked = function(){
  var nl     = this.rows.length;              // numero de linhas
  if( nl>2 ){
    var nc     = this.rows[nl-1].cells.length;  // numero de colunas
    var coluna = this.qualColuna(nc);           // achando a coluna que esta o checkbox
    var el     = '';
    for(var li = 1; li < nl; li++){
      el=this.rows[li].cells[coluna].children;
      ////////////////////////////////////////////////////////////////////////////////////
      // Este if é que tenho lugares onde quando adiciono uma linha esta vem sem o checked
      ////////////////////////////////////////////////////////////////////////////////////
      if( el.length==0 )
        continue;
      //
      if (el[coluna].checked){
        el[coluna].checked=false;
        el[coluna].parentNode.parentNode.classList.remove('corGradeParCheck');
      }  
    };
  };
};
//Classe para ler(eq) e gravar(html) em uma table
var grade = function(str) {
  this.argStr =  str;
  this.eq=function(n){
    return this.argStr.parent().parent().find('td:eq('+n+')').html();
  };
  this.html=function(n,s){
    this.argStr.parent().parent().find('td:eq('+n+')').html(s);
  };
};
//FUNÇÃO PARA FORMATAR CAMPOS PARA ENTRAR BD
function formatoBD(str,tipo){
  var cmpfBD = new clsCampo();  // Classe para retornar campos  "cmpfBD" para não ter igual na chamadora
  switch (tipo) {
    case 'str'  : return ( str == null ? null : '\''+str+'\'' )                           ;break;
    case 'dat'  : return ( str == null ? null : '\''+jsDatas(str).retMMDDYYYY()+'\'' ) ;break;
    case 'flo'  : return '\''+cmpfBD.floatNA(str)+'\''     ;break;
    case 'flo2' : return '\''+cmpfBD.floatNA(str)+'\''     ;break;
    case 'flo4' : return '\''+cmpfBD.floatNA4(str)+'\''    ;break;
    case 'flo8' : return '\''+cmpfBD.floatNA8(str)+'\''    ;break;    
    case 'int'  : return str                               ;break;
    default: 
      console.log('Erro na função formatoBD');
      return 'ERRO';
      break;
  };
  delete(cmpfBD);
};
function gerarMensagemErro(parRotina,parMensagem,objeto){  
  if( tagValida(parRotina)==false )
    parRotina='ERR';
  let cabec="Erro";
  let foco;
  let topo;
  if( typeof objeto === 'object' ){
    for (var key in objeto) {
      switch( key ){
        case "cabec": 
          cabec=objeto[key];
          break;
        case "foco": 
          foco=objeto[key];
          break;
        case "topo": 
          topo=objeto[key];
          break;
      };
    };    
  };  
  var erro = new clsMensagem(cabec,topo);
  erro.ListaErr(parMensagem);
  erro.Show(parRotina,foco);
  delete(erro);
};
// ATUALIZA SOMENTE CAMPOS INFORMADOS( function clausulaUpdate atualiza todos os campos da grade ) 
// js     - JSon completo
// arrCmp - Vetor dos campos a serem atualizados pela coluna labelCol
// arrVlr - Vetor dos valores a serem atualizados
// arrSel - Vetor de todos os registros selecionados na table
function updateFields(ufJS,arrCmp,arrVlr,arrSel){
  var ret = '';
  var sep = '';
  var sql = '';
  for( var regSel=0; regSel<arrSel.length; regSel++ ){
    var arr = [];
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // MONTANDO UM ARRAY SOMENTE COM OS CAMPOS QUE PRECISO PARA UPDATE
    // QUE VEM DE arrCmp + FK(s) no JSON
    // Primeiro pego o JSON depois os campos pois devem estar na mesma ordem dos vetores arrCmp e arrVlr
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    ufJS.titulo.forEach(function(tit){
      if( (tagValida(tit.pk))  &&  (tit.pk=='S') )  
        arr.push({"obj":tit.obj ,"field":tit.field,"fieldType":tit.fieldType,"pk":tit.pk,"labelCol":tit.labelCol});
    });
    for( li in arrCmp ){
      ufJS.titulo.forEach(function(tit){
        if(arrCmp[li]==tit.labelCol)
          arr.push({"obj":tit.obj,"field":tit.field,"fieldType":tit.fieldType,"pk":tit.pk ,"labelCol":tit.labelCol});
      })  
    };
    var where     = []; // Guardando os valores para montar a clausula where
    var arrField  = []; // Guardar campos do banco de dados
    var arrCntd   = []; // Guardar conteudo para cada campo acima
    //////////////////////////////////////////////////////////////////////////////////////////////////
    // a variavel arr tem os campos recebidos do parametro arrCmp + FK(s) no Json por isso preciso da
    // variavel colVlr pois os dois vetores sempre terão tamanhos diferentes devido FK(s)
    //
    // arr =     
    // [ 
    //   {"field":"GUIA"       ,"fieldType":"int","pk":"S","labelC0l":"LANCTO"}
    //  ,{"field":"DATAPAGA"   ,"fieldType":"dat","labelC0l":"BAIXA"}
    //  ,{"field":"CODUSU"     ,"fieldType":"int","labelC0l":"CODUSU"}
    //  ,{"field":"PAG_CODEMP" ,"fieldType":"int","labelC0l":"EMP"}
    //  ]  
    //
    // arrSel  [{"LANCTO":"001719","TP":"CP","CODCLI":"56","FAVORECIDO":"FLAVIO"...
    //////////////////////////////////////////////////////////////////////////////////////////////////
    var colVlr   = 0;
    var conteudo = '';
    for( li=0; col=arr[li]; li++ ){
      if( (tagValida(col.pk))  &&  (col.pk=='S') ){
        eval('conteudo=arrSel['+regSel+'].'+col.labelCol);
        where.push(conteudo);  
      } else {
        conteudo=arrVlr[colVlr];
        colVlr++;
        conteudo=formatoBD(conteudo,col.fieldType);
        arrField.push(col.field);
        arrCntd.push(conteudo);    
      };
    };
    //
    sep='';
    ret='UPDATE '+ufJS.tabelaBD+' SET ';
    for( li in arrField ){
      ret+=sep+arrField[li]+'='+arrCntd[li];
      sep=',';
    };
    ret+=' '+clausulaWhere(ufJS,where);
    if(regSel==0){
      ret='{"comando":"'+ret+'"}';
    } else {
      ret=',{"comando":"'+ret+'"}';
    }
    sql+=ret;
  }  
  sql='{"lote":['+sql+']}';
  return sql;
};
// Monta o update de TODOS  os campos da tabela, existe a updateFields(acima) 
// que faz apenas de campos informados
// 22set2017 - voltei, porque a funcao é usada em vários lugares
function clausulaWhere(js,whr){
  var arrW  = [];
  var ret   = '';
  var parte = 0;
  // MONTANDO UM ARRAY SOMENTE COM OS CAMPOS QUE PRECISO PARA WHERE
  js.titulo.forEach(function(tit){
    if( (tagValida(tit.pk))  &&  (tit.pk=='S') ){
      // a variavel parte é para quando campo for combobox e a grade tiver descritivo diferente do conteudo
      parte =0;
      if( (tagValida(tit.tipo)) && (tit.tipo=='cb') ){
        if( tagValida(tit.copy) ){
          parte=cmp.int(tit.copy[1],'arr');
        };  
      };
      //
      arrW.push({"fieldType":tit.fieldType , "field":tit.field , "parte":parte});
    };  
  });
  for( li=0; col=arrW[li]; li++ ){
    if( col.parte>0 )
      whr[li]=whr[li].substring(0,col.parte);
    
    var conteudo=formatoBD(whr[li],col.fieldType);
    ret+=
      (ret=='' ? ' WHERE ' : ' AND ')
      +col.field
      +'='+
      conteudo;
  };
  return ret;
};
/*
* Usar funcao escape para ver codigo
*/
function removeAcentos( newStringComAcento ) {
  var string = newStringComAcento;
  var mapaAcentosHex  = {
   a : /[\xE0-\xE6]/g,
   A : /[\xC0-\xC6]/g,
   e : /[\xE8-\xEB]/g,
   E : /[\xC8-\xCB]/g,
   i : /[\xEC-\xEF]/g,
   I : /[\xCC-\xCF]/g,
   o : /[\xF2-\xF6]/g,
   O : /[\xD2-\xD6]/g,
   u : /[\xF9-\xFC]/g,
   U : /[\xD9-\xDC]/g,
   c : /\xE7/g,
   C : /\xC7/g,
   n : /\xF1/g,
   N : /\xD1/g,
  };
 for ( var letra in mapaAcentosHex ) {
  var expressaoRegular = mapaAcentosHex[letra];
  string = string.replaceAll(expressaoRegular,letra);
 };
 string=string.replace('"','');
 return string;
};
//
function filtrarReg(js,tbl,conteudo,lbl){
  var col=0;
  js.forEach(function(e){  
    if( e.labelCol==lbl ){
      col=((e.id)+0);
      return false;
    };  
  });
  var el  = document.getElementById(tbl).getElementsByTagName('tbody')[0];
  var tam = el.rows.length;
  for(var lin=0;lin<tam;lin++){
    if(el.rows[lin].cells[col].innerHTML.indexOf( conteudo.toUpperCase() ) < 0 )
      el.rows[lin].style.display='none';
    else
      el.rows[lin].style.display='table-row';
  };
};
//
function tagValida(tag){
  return ( ((tag !== undefined) && (tag !== '')) ? true : false );     
};
// CLASSE SELECT/ATUALIZAÇÃO BD
function clsBancoDados(url) { 
  var self        = this;
  self.metodo     = 'POST';
  self.url        = url;
  self.xhttp      = new XMLHttpRequest();
  self.sync       = false;
  self.retorno    = '';
  self.retPHP     = '';
  self.dados      = '';
  self.Assoc      = true;
  self.retDefault = true;  // Tratamento de erro é feito na chamada

  self.xhttp.onreadystatechange = function() {
    // Mostra que o a pagina foi localizada
    if (this.readyState == 4 && this.status == 200) {
      self.retPHP=this.responseText;
      //console.log(self.retPHP); ////////////////////////////////////////////////////////
      if( self.retDefault==true ){
        eval('tblRet='+self.retPHP);
        self.retorno=tblRet[0].retorno;
        if( (tblRet.length>0) && (tblRet[0].retorno=='OK') ){
          self.dados=tblRet[0].dados;
        } 
        else 
        {
          var help = new clsMensagem('Retorno');
          help.mensagem=tblRet[0].dados;
          help.Show();
        };  
      };
    };
    if( this.status == 404 ){
      var help = new clsMensagem('Retorno');
      help.mensagem='URL NÃO LOCALIZADA!';
      help.Show();      
    };  
  };
  //  
  this.conecta=function(strEmp,strUsu,strSen){
    self.xhttp.open(self.metodo, self.url, self.sync);
    self.xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    self.xhttp.send("opcao=conectaBD&parEmpresa="+strEmp+"&parUsuario="+strUsu+"&parSenha="+strSen);
  }; 
  this.select=function(sql){
    self.xhttp.open(self.metodo, self.url, self.sync);
    // Esse fp troca o sinal "+" por ""
    form=new FormData();
    form.append('opcao',(self.Assoc ? 'selectAssoc' : 'selectRow' ));
    form.append('sql',sql);
    self.xhttp.send(form);                
  };
  this.execute=function(js){
    self.xhttp.open(self.metodo, self.url, self.sync);
    self.xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    self.xhttp.send("opcao=executeSql&sql="+js);          
  };  
  this.cadtit=function(js){
    self.xhttp.open(self.metodo, self.url, self.sync);
    self.xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    self.xhttp.send("opcao=cadtit&xml="+js);          
  };

};
// CLASSE MENSAGEM
// CLASSE PARA EXIBER MENSAGEM DE ERRO E HELP
// O SAFARI não aceita dar um default no parametro cabec (cabec='Ajuda')
function clsMensagem(cabec,topo){ 
  cabec=( tagValida(cabec)==true ? cabec : 'Ajuda' ); 
  var self       = this;
  self.strLista  = '';        // Pegar erros quando de validação de campos
  self.cabec     = cabec;
  /////////////////////////////////////////////
  // Devido acordeon a posicao do topo varia //
  /////////////////////////////////////////////
  if( topo==undefined ){
    self.divTopo   = '100px';
  } else {
    self.divTopo=topo+"px";
  };  
  self.divWidth  = '40%';
  self.divHeight = '200px';
  self.tagH2     = true;      // Opção para mostrar ou não a tag <h2>
  self.mensagem  = '';
  //
  this.contido=function(campo,valor,arr){
    ok=false;
    str='';
    for(li in arr){
      str+='/'+arr[li];
      if( valor==arr[li] )
        ok=true;
    };
    if( !ok ){
      str=str.replace('/','');
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> ACEITA '+str+'!</td></tr>';
    };  
  };
  this.dataValida=function(campo,valor){
    var bits  = valor.split('/');
    var d     = new Date(bits[2], bits[1] - 1, bits[0]);
    if( ((d) && ((d.getMonth() + 1) == bits[1]))==false )
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> NÃO É UMA DATA VALIDA!</td></tr>';    
  };  
  //
  this.dataCompara=function(campo,dat1,dat2,condicao){
    var d1=parseInt(dat1.replace(/\D/g,"").replace(/(\d{2})(\d{2})(\d{4})/,"$3$2$1"));  
    var d2=parseInt(dat2.replace(/\D/g,"").replace(/(\d{2})(\d{2})(\d{4})/,"$3$2$1"));
    switch(condicao){
      case 'dataMaior' :
        if(d1<=d2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+dat1+' DEVE SER MAIOR QUE '+dat2+'</td></tr>'; break;
      case 'dataMaiorIgual' :
        if(d1<d2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+dat1+' DEVE SER MAIOR OU IGUAL '+dat2+'</td></tr>'; break;
      case 'dataDiferente' :
        if(d1==d2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+dat1+' DEVE SER DIFERENTE DE '+dat2+'</td></tr>'; break;
      case 'dataMenorIgual' :
        if(d1>d2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+dat1+' DEVE SER MENOR OU IGUAL '+dat2+'</td></tr>'; break;
    };    
  };  
  //
  this.intCompara=function(campo,int1,int2,condicao){
    var i1=parseInt(int1.replace(/\D/g,"")); 
    var i2=parseInt(int2.replace(/\D/g,"")); 
    switch(condicao){
      case 'intMenor' :
        if(i1>=i2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+int1+' DEVE SER MENOR QUE '+int2+'</td></tr>'; 
          break;
      case 'intMenorIgual' :
        if(i1>i2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+int1+' DEVE SER MENOR OU IGUAL '+int2+'</td></tr>'; 
          break;
      case 'intMaior' :
        if(i1<=i2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+int1+' DEVE SER MAIOR QUE '+int2+'</td></tr>'; 
          break;
      case 'intMaiorIgual' :
        if(i1<i2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+int1+' DEVE SER MAIOR OU IGUAL '+int2+'</td></tr>'; 
          break;
      case 'intDiferente' :
        if(i1==i2)  this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+int1+' DEVE SER DIFERENTE DE '+int2+'</td></tr>'; 
          break;
    };    
  };  
  //
  this.digitosValidos=function(campo,valor,digitos){
    var dig=digitos.split('|');
    var ret='';
    for(li=0;letra=valor[li];li++){
      for( ld in dig ){
        if( letra==dig[ld] )
          ret+=letra;
      };  
    };
    if( ret != valor )
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> ACEITA APENAS <b>'+digitos+'</b>!</td></tr>';      
  }  
  //Os dois valores tem que ser diferentes(ex: transf codigo bancos)
  this.diferente=function(campo,valorI,valorF){
    if( valorI == valorF ) 
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> VALORES['+valorI+'/'+valorF+'] DEVE SER DIFERENTES!</td></tr>';  
  };
  /////////////////////////////////////////////////
  // Comparando o conteudo do campo com uma mascara
  /////////////////////////////////////////////////
  this.mascara=function(campo,valor,mscr){
    let m       = mscr.length;
    let v       = valor.length;
    let retorno = true;
    if( m != v ){
      retorno=false;
    } else {
      let i=0;

      while( i < m ){
        switch( mscr[i] ){
          case "a":
            if( ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"].indexOf(valor[i]) == -1 )
              retorno=false;
            break;
          case "A":
            if( ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"].indexOf(valor[i]) == -1 )
              retorno=false;
            break;
          case "9":
            if( ["0","1","2","3","4","5","6","7","8","9"].indexOf(valor[i]) == -1 )
              retorno=false;
            break;
          default:  
            if( mscr[i] != valor[i] )
              retorno=false;
            break;
        };
        if( retorno==false )
          break    
        i++;
      };
    };
    if( retorno==false )    
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> '+valor+' DEVE SER O FORMATO[ '+mscr+' ]</td></tr>';    
  };
  ////////////////////////////////////////////
  // Olha o direito de usuario antes de gravar
  ////////////////////////////////////////////
  this.direitoIgual=function(campo,valor,valido){
    if( valor != valido ) 
      this.strLista+='<tr><td>USUARIO SEM DIREITO PARA ESTA ROTINA!</td></tr>';
  };
  //  
  this.floMaiorZero=function(campo,valor){
    str=valor.replace(/[^0-9-.,]/g,"").replace(",",".");
    if( (str.length != valor.length) || (isNaN(parseFloat(str))) ) {
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> NÃO É UM FLOAT VALIDO!</td></tr>';  
    } else {
      if( parseFloat(str)<=0 )
        this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE SER MAIOR QUE 0(ZERO)!</td></tr>';  
    };  
  };
  this.floMaiorIgualZero=function(campo,valor){
    str=valor.replace(/[^0-9-.,]/g,"").replace(",",".");
    if( (str.length != valor.length) || (isNaN(parseFloat(str))) ) {
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> NÃO É UM FLOAT VALIDO!</td></tr>';  
    } else {
      if( parseFloat(str)<0 )
        this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE SER MAIOR OU IGUAL 0(ZERO)!</td></tr>';  
    };  
  };
  this.floDifZero=function(campo,valor){
    str=valor.replace(/[^0-9-.,]/g,"").replace(",",".");
    if( (str.length != valor.length) || (isNaN(parseFloat(str))) ) {
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> NÃO É UM FLOAT VALIDO!</td></tr>';  
    } else {
      if( parseFloat(str)==0 )
        this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE SER DIFERENTE 0(ZERO)!</td></tr>';  
    };  
  };  
  this.add = function(str){  
    this.strLista+='<tr><td>'+str+'</td></tr>';  
  };
  //
  //
  this.igual=function(campo,valor,valido){
    if( valor != valido ) 
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE SER '+valido+'!</td></tr>';  
  };
  //  
  this.intMaiorZero=function(campo,valor){
    //soNumeros é im prototype STRING
    if( typeof valor == "number" )
      valor=valor.toFixed(0);
    //
    str=valor.soNumeros();
    if( str.length != valor.length ){
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> NÃO É UM INTEIRO VALIDO!</td></tr>';  
    } else {
      if( parseInt(str)<=0 )
        this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE SER MAIOR QUE 0(ZERO)!</td></tr>';  
    };  
  };  
  //
  this.intMaiorIgualZero=function(campo,valor){
    str=valor.soNumeros();
    if( str.length != valor.length ){
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> NÃO É UM INTEIRO VALIDO!</td></tr>';  
    } else {
      if( parseInt(str)<0 )
        this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE SER MAIOR OU IGUAL QUE 0(ZERO)!</td></tr>';  
    };  
  };  
  //
  this.notNull=function(campo,valor){
    try{
      valor=valor.replaceAll(' ','');
    } catch(e) { throw new Error (campo+' '+e); }; 
    if( valor=='' ) 
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> NÃO ACEITA VAZIO!</td></tr>';
  };
  //
  this.null=function(campo,valor){
    if( valor != '' ) 
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE SER VAZIO!</td></tr>';
  };
  //
  this.tamMin=function(campo,valor,tam){
    if( valor.length < tam )
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> MINIMO DE '+tam+' CARACTER(es)!</td></tr>';
  };  
  //
  this.tamMax=function(campo,valor,tam){
    if( valor.length > tam )
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> MAXIMO DE '+tam+' CARACTER(es)!</td></tr>';
  };  
  //
  this.tamFixo=function(campo,valor,tam){
    if( valor.length != tam )
      this.strLista+='<tr><td>CAMPO <b>'+campo+'</b> DEVE TER '+tam+' CARACTER(es)!</td></tr>';
  };
  //////////////////////////////////////////////////////////
  // O parametro "str" é para forçar uma mensagem de erro //
  // O SAFARI não aceita declarar o parametro (str='')    //
  //////////////////////////////////////////////////////////
  this.ListaErr = function(str){
    str=( tagValida(str)==true ? str : '' );
    if( str != '' )
      this.strLista='<tr><td>'+str+'</td></tr>';
    if( this.strLista != '' ){
      self.mensagem=
        '<table id="tabME" style="font-size:14px;">'
       +  '<tr><td style="color:red;">Mensagem</td></tr>'
       +this.strLista
       +'</table>'; 
    };
    return this.strLista;
  }; 
  ////////////////////////////////////////////////////////////////////////////////////////////
  // O parametro cxMen(Caixa mensagem) é devido poder abrir duas caixas de dialogo na tela  //
  // Uma rotina outra gerada com um erro, o botão fechar deve ter este id                   //
  ////////////////////////////////////////////////////////////////////////////////////////////
  this.Show = function(cxMen,foco){
    contMsg++;
    var divModal  ='dm'   + contMsg;  // div modal
    var divMsg    ='dms'  + contMsg;  // div mensagem
    var lblCls    ='lcls' + contMsg;  // label close
    var pObj      ='obj'  + contMsg;  // Guardar a table quando for gerado pela classe
    var maior     =retornarZIndex();        
    var zModal  ='z-index:'+(maior+0);  // div modal
    var zMsg    ='z-index:'+(maior+1);  // div mensagem
    var zCls    ='z-index:'+(maior+2);  // label close
    //
    str='';
    str+='<div id="'+divModal+'" class="divShowModal" style="'+zModal+'"></div>';
    str+='<div id="'+divMsg+'" ';
    str+=  'style="left:0;right:0;margin-left:5%;margin-right:auto;';
    str+=        'width:'+self.divWidth+';height:'+self.divHeight+';position:absolute;top:'+self.divTopo+';'+zMsg+'">';
    str+=  '<div class="alertContainer" style="'+zCls+'">';      
    str+=    '<label id="'+lblCls+'" class="alertClose" for="alternar">X</label>';  
        str+=    '<div class="alertMensagem">';
    if( self.tagH2 )
      str+=      '<h2 class="alertH2">'+self.cabec+'</h2>';
    if( typeof self.mensagem=='string')  
      str+=      '<p class="alertP">'+self.mensagem+'<p>';
    else
      str+=      '<div id="'+pObj+'"></div>';
    str+=    '</div>';
    str+=  '</div>';
    str+='</div>';        
    str+='<script>';
    str+='</script>';
    document.getElementsByTagName('body')[0].insertAdjacentHTML('afterbegin',str);
    document.getElementById(lblCls).addEventListener("click",function(){
      document.getElementById(divModal).remove();
      document.getElementById(divMsg).remove();
      if( foco != undefined )
        document.getElementById(foco).foco();
    });
    if( typeof self.mensagem=='object') 
      document.getElementById(pObj).appendChild(self.mensagem);    
  };
};
//***************************************************************************************
//** CLASSE RETORNA UM CAMPO OBJETO/JSON/ARRAY
//** Existe a opção de converter para maiuscula qdo estiver lendo um campo getElementById
//** Parametro tipo obj=Objeto/js=JSon/arr=Array/var=Variavel
//***************************************************************************************
function clsCampo() {  
  var self       = this;
  self.Maiuscula = 'N';
  //Olha o direito de usuario antes de gravar
  this.direitoIgual=function(campo,valor,valido){
    if( valor != valido ) 
      this.strLista+='<tr><td>USUARIO SEM DIREITO PARA ESTA ROTINA!</td></tr>';
  };
  /*
  * FloatNA retorna um numeric americano com 2 casas decimais e "." ponto como separador de centavos 
  */
  this.floatNA=function(nome){
    if( typeof nome == "number" )
      nome=nome.toFixed(2);
    nome=nome.replace(/[^0-9-.,]/g,"").replace(",",".");
    nome=(parseFloat(nome)).toFixed(2);
    return (parseFloat(nome)=='NaN' ? 0.00 : parseFloat(nome));
  };
  this.floatNA4=function(nome){
    if( typeof nome == "number" )
      nome=nome.toFixed(4);
    nome=nome.replace(/[^0-9-.,]/g,"").replace(",",".");
    nome=(parseFloat(nome)).toFixed(4);
    return (parseFloat(nome)=='NaN' ? 0.0000 : parseFloat(nome));
  };
  this.floatNA8=function(nome){
    if( typeof nome == "number" )
      nome=nome.toFixed(8);
    nome=nome.replace(/[^0-9-.,]/g,"").replace(",",".");
    nome=(parseFloat(nome)).toFixed(8);
    return (parseFloat(nome)=='NaN' ? 0.00000000 : parseFloat(nome));
  };
  ///////////////////////////////////////////////////////////////////////////////////////////////////
  // FloatNB retorna uma string brasil com 2 casas decimais e "," virgunha como separador de centavos 
  ///////////////////////////////////////////////////////////////////////////////////////////////////
  this.floatNB=function(nome){
    if( typeof nome == "string" ){
      nome=nome.replace(/[^0-9-.,]/g,"").replace(",",".");
      nome=(parseFloat(nome)).toFixed(2);
      return nome.replace(/[^0-9-.,]/g,"").replace(".",",");
    };  
    if( typeof nome == "number" ){
      nome=nome.toFixed(2);
      nome=nome.replace(".",",");
      return nome;
    };  
  };
  this.floatNB4=function(nome){
    if( typeof nome == "string" ){
      nome=nome.replace(/[^0-9-.,]/g,"").replace(",",".");
      nome=(parseFloat(nome)).toFixed(4);
      return nome.replace(/[^0-9-.,]/g,"").replace(".",",");
    };  
    if( typeof nome == "number" ){
      nome=nome.toFixed(4);
      nome=nome.replace(".",",");
      return nome;
    };  
  };
  this.floatNB8=function(nome){
    if( typeof nome == "string" ){
      nome=nome.replace(/[^0-9-.,]/g,"").replace(",",".");
      nome=(parseFloat(nome)).toFixed(8);
      return nome.replace(/[^0-9-.,]/g,"").replace(".",",");
    };  
    if( typeof nome == "number" ){
      nome=nome.toFixed(8);
      nome=nome.replace(".",",");
      return nome;
    };  
  };
  //O SAFARI não aceita (tipo='obj')
  this.float=function(nome,decimais,tipo){
    tipo=( tagValida(tipo)==true ? tipo : 'obj' );
    if( tipo=='obj' ){
      tmp=document.getElementById(nome).value;
    } else{
      tmp=nome;
    };
    if( typeof tmp == "number" )
      tmp=tmp.toFixed(decimais);
    
    tmp=tmp.replace(/[^0-9-.,]/g,"").replace(",",".");
    tmp=(parseFloat(tmp)).toFixed(decimais);
    return (parseFloat(tmp)=='NaN' ? 0.00 : parseFloat(tmp));
  };
  //O SAFARI não aceita (tipo='obj')
  this.int=function(nome,tipo){
    if( typeof nome == "number" )
      nome=nome.toFixed(0);
  
    tipo=( tagValida(tipo)==true ? tipo : 'obj' );
    if( tipo=='obj' ){    
      tmp=(document.getElementById(nome).value).replace(/[^0-9-]/g,"");
    } else{
      tmp=nome.replace(/[^0-9-]/g,"");
    };  
    return (parseInt(tmp)=='NaN' ? 0 : parseInt(tmp));
  };
  //O SAFARI não aceita (tipo='obj')
  this.str=function(nome,tipo){
    tipo=( tagValida(tipo)==true ? tipo : 'obj' );
    if( tipo=='obj' ){  
      //Opção para converter obj em string
      if( self.Maiuscula=='S' ){
        document.getElementById(nome).value=document.getElementById(nome).value.toUpperCase();  
      };  
      tmp=document.getElementById(nome).value;
    } else{
      tmp=nome;
    };  
    return tmp;
  };
};
///////////////////////////
// MONTA UM ARQUIVO JSON //
///////////////////////////
function jsString(tag){ 
  return {
    tag           : tag
    ,primeiroCmp  : ""
    ,master       : true
    ,retorno      : ""
    ,add: function(cmp,vlr){
      if( this.primeiroCmp=="" ){
        this.retorno+='{';
        this.primeiroCmp=cmp;
      } else if( this.primeiroCmp==cmp ){
        this.retorno=this.retorno.substring(0,(this.retorno.length-1));
        this.retorno+='},{';
      }
      /////////////////////////////////////////////////////////////////////////////////
      // Se vier [{"campo":"valor",......}] é que é um json dentro do json principal //
      /////////////////////////////////////////////////////////////////////////////////
      if(vlr.toString().substring(0,2) != "[{"){  
        this.retorno+='"'+cmp+'":"'+vlr+'",';
      } else {
        this.retorno+='"'+cmp+'":'+vlr+',';  
      }  
      return this;
    }
    ,principal:function(bol){
      this.master=bol;
    }
    ,fim:function(){
      if( this.retorno != "" )
        this.retorno=this.retorno.substring(0,(this.retorno.length-1))+'}';
      //////////////////////////////////////////////////////////////////
      // master true monta um json completo                        //
      // master false monta um json para estar dentro do principal //
      //////////////////////////////////////////////////////////////////
      if( this.master ){
        this.retorno='{"'+tag+'":['+this.retorno+']}'; 
      } else {
        this.retorno='['+this.retorno+']'; 
      } 
      return this.retorno;
    }
  }
};
graficoPhp=function(js,dados){
    var objJs=js.parametros;
    var ceDiv     = '';
    ////////////////////////
    // Montando o grafico //
    ////////////////////////
    var dPaiGra               = document.createElement("div");
    var dPaiGraE              = document.createElement("canvas");
    dPaiGraE.id               = "pieChart";      
    dPaiGraE.style.cssFloat   = "left";      
    //dPaiGraE.width            = "300";
    dPaiGraE.width            = (objJs[0].widthGra==undefined ? 300 : objJs[0].widthGra);
    dPaiGraE.height           = (objJs[0].height-100);
    dPaiGraE.style.marginLeft = "20px";

    var dPaiGraD              = document.createElement("div");
    dPaiGraD.id               = "pieLegend"
    dPaiGraD.style.cssFloat   = "left";      
    dPaiGraD.style.marginLeft = "30px";
    
    var ceAnc                 = document.createElement('a');      
    ceAnc.name                = "verGrafico";
    
    dPaiGra.appendChild(dPaiGraE);
    dPaiGra.appendChild(dPaiGraD);
    dPaiGra.appendChild(ceAnc);      
    ////////////////////////////////////////////
    // Acumulando valores para gerar grafico  //
    ////////////////////////////////////////////
    var arrVlr  = dados[0]["dados"];   
    var data    = new Array();
    var arrGra  = new Array();
    var valor   = 0;     
    var ctx     = dPaiGraE.getContext("2d");
    switch (objJs[0].tipoGrafico) {
      case 'pie':
        for( var lin=0 in arrVlr ){                   
          data.push({ "value": arrVlr[lin]["VALOR"],
                      "color":arrFill[lin],
                      "label":arrVlr[lin]["CAMPO"]
          });
        };  
        var myGra=new Chart(ctx).Pie(data);
        legend(dPaiGraD, data,myGra, true);
        break;
      case 'bar':
        for( var lin=0 in arrVlr ){ 
          arrGra.push({
            "fillColor"         : arrFill[lin],
            "strokeColor"       : arrBorder[lin],
            "pointColor"        : "red",
            "pointStrokeColor"  : "#fff",
            "data"              : [arrVlr[lin]["VALOR"]],
            "label"             : [arrVlr[lin]["CAMPO"]]
          });
        };
        var data = {
            labels : [''],
            datasets : arrGra,
        };
        var myGra=new Chart(ctx).Bar(data);
        legend(dPaiGraD, data,myGra, true);
        break;
        
      case 'lin':
        var data=objJs[0].dados;
        var myGra=new Chart(ctx).Line(data);
        legend(dPaiGraD, data,myGra, false);          
        break;        
    };
    
    ceDiv                 = document.createElement("div");
    ceDiv.style.height    = objJs[0].height;  //"400";
    ceDiv.style.overflowY = "auto";        
    ceDiv.appendChild(dPaiGra);
    //////////////////////////////////////
    // Montando o formulario para table //
    // Variaveis para modal             //
    //////////////////////////////////////
    contMsg++;
    var divModal  = 'dm'   + contMsg;  // div modal
    var divMsg    = 'dms'  + contMsg;  // div mensagem
    var maior     = retornarZIndex();  // maior indice para Z-index  
    //////////////////////
    // Create Elements  //
    //////////////////////
    var ceModal =''; //Div modal para marcar todo fundo como desabilitado
    var ceSec   ='';
    var ceFrm   ='';
    var cePar   ='';
    var ceImg   ='';
    var ceInp   ='';
    var ceBut   ='';
    var objImg  =''; 
    ceModal=document.createElement("div");
    ceModal.id            = divModal; 
    ceModal.className     = "divShowModal"; 
    ceModal.style.zIndex  =(maior);
    // Formulario
    ceFrm=document.createElement("form");
    if( objJs[0].left==undefined ){
      ceFrm.className       = "formulario center"; 
    } else {  
      ceFrm.className       = "formulario"; 
    }  
    ceFrm.id              = divMsg; 
    ceFrm.style.top       = "20px";//detReg.top;
    if( objJs[0].left != undefined ){    
      ceFrm.style.left      = objJs[0].left;
    }  
    ceFrm.style.width     = objJs[0].width;
    ceFrm.style.height    = "400px";           
    
    ceFrm.style.position  = "absolute";
    ceFrm.style.zIndex    = (maior+2);
    // titulo
    cePar=document.createElement("p");
    cePar.className       = "frmCampo"; 
    ceInp=document.createElement('input');      
    ceInp.className  = "informe"; 
    ceInp.type       = "text"; 
    ceInp.name       = "titulo"; 
    ceInp.value      = objJs[0].titulo;
    ceInp.disabled   = true;           
    cePar.appendChild(ceInp);
    ceFrm.appendChild(cePar);  
    ceFrm.appendChild(ceDiv);  

    cePar=document.createElement("p");
    cePar.className  = "botSupDir";         
    ceBut=document.createElement("button");
    ceBut.className = "dir"; 
    ceBut.type      = "button"; 
    ceBut.name      = "dlgConfirmar";           
    ceBut.id        = "dlgConfirmar";
    ceBut.addEventListener('click',function(){
      document.getElementById(divModal).remove();
      document.getElementById(divMsg).remove();
    });  

    ceImg= document.createElement("i");
    ceImg.className  = "faIL fa-close icon-large";
    ceBut.appendChild(ceImg);
    cePar.appendChild(ceBut);
    ceFrm.appendChild(cePar);
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // js.divModalFull para desabilitar todo fundo de tela, se não declarada no json ela vai ser acionada //
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    document.getElementsByTagName('body')[0].appendChild(ceModal);
    document.getElementsByTagName('body')[0].appendChild(ceFrm);
};
//////////////////////////////////////////////////////
// Retorna a posicao do elemento em relacao ao topo //
//////////////////////////////////////////////////////
function getPosicaoElemento(elemID){
  var offsetTrail = document.getElementById(elemID);
  var offsetLeft = 0;
  var offsetTop = 0;
  while (offsetTrail) {
      offsetLeft += offsetTrail.offsetLeft;
      offsetTop += offsetTrail.offsetTop;
      offsetTrail = offsetTrail.offsetParent;   //buscando o parent ateh chegar no body
  }
  if (navigator.userAgent.indexOf("Mac") != -1 && 
      typeof document.body.leftMargin != "undefined") {
      offsetLeft += document.body.leftMargin;
      offsetTop += document.body.topMargin;
  }
  return {left:offsetLeft, top:offsetTop};
};
function fncCasaDecimal(obj,dec){
  document.getElementById(obj.id).value=jsNmrs(obj.id).dec(dec).real().ret();
};
function criarEl(elem, attr, app) {
  if (typeof(elem) === undefined) {
    return false;
  };
  var el = document.createElement(elem);
  if (typeof(attr) === "object") {
    for (var key in attr) {
      el.setAttribute(key, attr[key]);
    }
  };
  if( typeof(app) === "object" ){
    if( app.textNode != undefined ){
      var cn=document.createTextNode( app.textNode );
      el.appendChild(cn);
    }  
  
    if( app.appChild != null ){
      document.getElementById(app.appChild).appendChild(el);
      return true;
    };  
  };
  return el;
}
function fncExcel(divCe,novoPadrao){
  //////////////////////////////////////////////////////////////////
  // Aqui colocados valores padroes para tela de importacao excel //
  //////////////////////////////////////////////////////////////////
  var padrao={widthDivE:"89.8em"};
  //
  ///////////////////////////////////////////
  // Aqui pode ser alterado qualquer valor //
  ///////////////////////////////////////////
  if( (novoPadrao != undefined) && (typeof(novoPadrao)=="object") ){
    if( novoPadrao.widthDivE != undefined )
      padrao.widthDivE=novoPadrao.widthDivE;
  }
  //
  //
  criarEl("div",{
    id      : "divTopoInicioE"
    ,class  : "divTopoInicio"
    ,style  : "width:"+padrao.widthDivE+";"},{appChild:divCe});
          
  criarEl("div",{
    id      : "divCampo"
    ,class  : "campotexto campo100"},{appChild:"divTopoInicioE"});

  criarEl("div",{
    id      : "divInput"
    ,class  : "campo85"
    ,style  : "float:left;"},{appChild:"divCampo"});
    
  criarEl("input",{
    id      : "edtArquivo"
    ,class  : "campo_file input"
    ,name   : "edtArquivo"
    ,type   : "file"
    ,style  : "float:left;"},{appChild:"divInput"});
    
  criarEl("label",{
    id      : "lblArquivo"
    ,class  : "campo_label"
    ,for    : "edtArquivo"},{appChild:"divInput",textNode:"Arquivo"});

  criarEl("div",{
    id      : "divBtn"
    ,class  : "btnImagemEsq bie15 bieAzul bieRight"
    ,onClick : "btnAbrirExcelClick();"    
    ,style  : "float:left;height:47px;padding-top:15px;"},{appChild:"divCampo"});
    
  criarEl("i",{
    class  : "fa fa-folder-open"}
    ,{appChild:"divBtn",textNode:" Abrir"});

  criarEl("div",{
    id      : "xmlModal"
    ,class  : "divShowModal"
    ,style  : "display:none;"},{appChild:divCe});

  criarEl("div",{
    id      : "divErr"
    ,class  : "conteudo"
    ,style  : "display:block;overflow-x:auto;"},{appChild:divCe});
    
  criarEl("form",{
    id      : "frmExc"
    ,class  : "center"
    ,method : "post" 
    ,name   : "frmExc"
    ,action : "imprimirsql.php" 
    ,target : "_newpage"},{appChild:"divErr"});

  criarEl("input",{
    id      : "sql"
    ,name   : "sql"
    ,type   : "hidden"},{appChild:"frmExc"});

  criarEl("div",{
    id      : "tabelaExc"
    ,class  : "center active"
    ,style  : "position:fixed;top:10em;width:90em;z-index:30;display:none;"},{appChild:"frmExc"});
}
///////////////////////////////////////////////////////////////
// convertDh(XXX,0) ->YYYY-MM-DD HH:MM:SS -> DD/MM - HH:MM:SS
// convertDh(XXX,1) ->YYYY-MM-DD HH:MM:SS -> DD/MM/YYYY - HH:MM
///////////////////////////////////////////////////////////////
function converteDh(parStr,opc){
  if( (parStr=="") || (parStr==null) ){
    return "";
  } else {
    if( opc==0 ){
      let spltDh=parStr.split("T");
      let spltH=spltDh[0].split("-");
      return spltH[2]+"/"+spltH[1]+" - "+spltDh[1].substr(0,8);
    }
    if( opc==1 ){
      let spltDh=parStr.split("T");
      let spltH=spltDh[1].split(":");
      return jsDatas(spltDh[0]).retDDMMYYYY()+"-"+spltH[0]+":"+spltH[1];
    }
    if( opc==2 ){
      let spltDh=parStr.split("-");
      let spltD=spltDh[0].split("/");
      return spltD[2]+"-"+spltD[1]+"-"+spltD[0]+" "+spltDh[1];
    };
  };  
};
////////////////////////////////////////////////////////////////////////
// Usado Atlas para mostrar prioridade
////////////////////////////////////////////////////////////////////////
function fncCorPrioridade(){
  return   "switch (objCell.innerHTML){"
          +" case '01':"
          +"   objCell.style.background='#00FF00';"  
          +"   objCell.style.color='black';"          
          +"   break;" 
          +" case '02':"
          +"   objCell.style.background='#00BFFF';"  
          +"   objCell.style.color='black';"          
          +"   break;" 
          +" case '03':"
          +"   objCell.style.background='#FFFF00';"  
          +"   objCell.style.color='black';"          
          +"   break;" 
          +" case '04':"
          +"   objCell.style.background='#FFA500';"  
          +"   objCell.style.color='black';"          
          +"   break;" 
          +" case '05':"
          +"   objCell.style.background='#7D26CD';"  
          +"   objCell.style.color='white';"          
          +"   break;" 
          +" case '06':"
          +"   objCell.style.background='#A0522D';"  
          +"   objCell.style.color='white';"          
          +"   break;" 
          +" case '07':"
          +"   objCell.style.background='#FF0000';"  
          +"   objCell.style.color='white';"          
          +"   break;" 
          +"}";
};
function fncCorStatus(){
  return   "switch (objCell.innerHTML){"
          +" case 'BAIXADO':"
          +"   objCell.style.background='#00FF00';"  
          +"   objCell.style.color='black';"          
          +"   break;" 
          +" case 'TRATATIVA':"
          +"   objCell.style.background='#FFFF00';"  
          +"   objCell.style.color='black';"          
          +"   break;" 
          +" case 'PENDENTE':"
          +"   objCell.style.background='#FF0000';"  
          +"   objCell.style.color='white';"          
          +"   break;" 
          +"}";
};
//////////////////////////////////////////////////////////////////////////////////
// const col=new clsObterColunas(jsLgr.titulo,["DESCRICAO","ATIVO","USUARIO"]); //
// col.appFilter();                                                             //
// if( col.getNumCols()==0 ){                                                   //
//   let obj=col.getObjeto();                                                   //
//   console.log(obj.DESCRICAO);                                                //
// }                                                                            //
//////////////////////////////////////////////////////////////////////////////////
function clsObterColunas( parJs,parColunas ){
  ////////////////////////////////////////////////////////////////////////////////
  // arrFilter -> Atributo privado da classe para pegar o retorno do metodo filter
  ////////////////////////////////////////////////////////////////////////////////
  let arrFilter = []; 
  ///////////////////////////////////////////////////////////////////////////////////////
  // strObjeto -> Atributo privado recebe filter para montar uma string objeto de retorno
  ///////////////////////////////////////////////////////////////////////////////////////
  let strObjeto = ""; 
  //////////////////////////////////////////////////////////////////////////////////////////////
  // numCols -> Atributo privado para comparar quantas colunas recebeu e quantas esta devolvendo
  //////////////////////////////////////////////////////////////////////////////////////////////
  let numCols = (parColunas.length);
  //
  //
  this.appFilter= function(){
      ////////////////////////////////////////////////////////
      // Filtrando somente as colunas recebidas como parametro
      ////////////////////////////////////////////////////////
      arrFilter=parJs.titulo.filter(function(coluna){
                  return (parColunas.indexOf(coluna.labelCol) != -1);
                });
      //////////////////////////////          
      // Montando uma string->objeto
      //////////////////////////////  
      arrFilter.forEach(function(reg){
        numCols--;
        strObjeto+=(strObjeto=="" ? "":",")+'"'+reg.labelCol+'":'+reg.id+'';    
      }) 
      return this;          
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Retorno o objeto com o endereco de cada coluna, soh chamar getObjeto se getNumCols==0    
  ////////////////////////////////////////////////////////////////////////////////////////
  this.getObjeto= function(){
      return JSON.parse('{'+strObjeto+'}');
  }
  ////////////////////////////////////////////////////////
  // Retorna 0(Zero) se todas as colunas foram encontradas
  ////////////////////////////////////////////////////////
  this.getNumCols= function(){              
    return numCols;
  }
  this.getNumLinhasTable= function(){ 
    let el  = document.getElementById(parJs.tbl);
    let tbl = el.getElementsByTagName("tbody")[0];
    let nl  = tbl.rows.length;
    return nl;
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////
// Montando o relatorio para enviar ao php
// https://orleijp.com.br/aula_php/config/fpdf/doc/index.htm
// https://forum.imasters.com.br/topic/186410-manual-do-fpdf-traduzido-para-portugu%C3%AAs-br/
// http://www.fpdf.org/
/////////////////////////////////////////////////////////////////////////////////////////////
function relatorio(){
  /////////////////////////////////////
  // arrLinha -> Guarda todas as linhas 
  /////////////////////////////////////
  let arrLinha    = []; 
  let orientacao  = "R";
  let nomeFonte   = "Arial";
  let negrito     = false;
  let multicell   = false;
  let tamFonte    = 7;
  let linAdd      = 0;
  let align       = "L";
  let borda       = 0;
  let novaLinha   = 0;
  let cellAltura  = 9;
  let moeda       = false;
  let posAltura   = 0;  // Guardando a poiscao da caneta na tela
  
  this.orientacao=function(str){
    orientacao=str;
  }
  
  this.nomeFonte=function(str){
    nomeFonte=str;
  }
  
  this.negrito=function(bol){
    negrito=bol;
  }

  this.multicell=function(bol){
    multicell=bol;
  }
  
  this.moeda=function(bol){
    moeda=bol;
  }
  
  this.tamFonte=function(inteiro){
    tamFonte=inteiro;
  }

  this.cellAltura=function(inteiro){
    cellAltura=inteiro;
  }

  
  this.align=function(str){
    ////////////////
    // L:esquerda //
    // C:centro   //
    // R:direita  //
    ////////////////
    align=str;
  }
  
  this.borda=function(inteiro){
    //////////////////
    // 0: sem borda //
    // 1: com borda //
    // L: esquerda  //
    // T: acima     //
    // R: direita   //
    // B: abaixo    //
    //////////////////    
    borda=inteiro;
  }
  
  this.novaLinha=function(int){
    ////////////////////////////////////
    // 0: à direita                   //
    // 1: no início da próxima linha  //
    // 2: abaixo                      //
    ////////////////////////////////////
    novaLinha=int;
  }
  ///////////////////////////////
  // Posicao da caneta na tela //
  ///////////////////////////////  
  this.setAltura=function(inteiro){
    posAltura=inteiro;
  }
  
  this.getAltura= function(inteiro){              
    return (posAltura+inteiro);
  }  
  
  this.iniciar=function(){
    arrLinha.push('{"orientacao":"'+orientacao+'","imprimir":[{"SetFont":["'+nomeFonte+'","'+(negrito==false ? "" : "B")+'",'+tamFonte+']}');  
  }
  
  this.setX=function(inteiro){
    arrLinha.push('{"SetX":['+inteiro+']}');    
  }

  this.pulaLinha=function(inteiro){
    arrLinha.push('{"Ln":['+inteiro+']}');
    posAltura+=inteiro; // Guardando a posicao da caneta na tela
  }
  //////////////////////////////////////////////////////////////////////
  // estilo                                                           //
  // D ou um texto vazio: desenha só a borda. Este é o valor padrão.  //
  // F: preenche                                                      //      
  // DF ou FD: desenha a borda e preenche                             //
  //////////////////////////////////////////////////////////////////////
  this.retangulo=function(cantoSuperior,cantoSuperiorEsquerdo,largura,altura,estilo){
    arrLinha.push('{"Rect":['+cantoSuperior+','+cantoSuperiorEsquerdo+','+largura+','+altura+',"'+estilo+'"]}');
  };
  
  this.cell=function(largura,texto,objeto){
    if( typeof objeto === 'object' ){
      for (var key in objeto) {
        switch( key ){
          case "align": 
            align=objeto[key];
            break;
          
          case "altura": 
            cellAltura=objeto[key];
            break;
            
          case "borda": 
            borda=objeto[key];
            break; 

          case "complemento": 
            texto=texto+objeto[key];
            break; 
            
          case "data": 
            if( (texto != "") && (texto != null) )
              texto=jsDatas(texto).retDDMMYYYY();
            break; 
            
          case "emZero": 
            texto=jsNmrs(texto).emZero(objeto[key]).ret();
            break; 

          case "moeda": 
            moeda=objeto[key]; 
            break; 
            
          case "pulaLinha": 
            arrLinha.push('{"Ln":['+objeto[key]+']}');
            posAltura+=objeto[key]; // Guardando a posicao da caneta na tela
            break;
          case "negrito": 
            negrito=objeto[key];
            arrLinha.push('{"SetFont":["'+nomeFonte+'","'+(negrito==false ? "" : "B")+'",'+tamFonte+']}');  
            break;
        };  
      };
    };
    if( moeda ) 
      texto=jsNmrs(texto).real().ret();
    
    if( multicell==false ){
      arrLinha.push('{"Cell":['+largura+','+cellAltura+',"'+texto+'",'+borda+','+novaLinha+',"'+align+'"]}');
    } else {
      arrLinha.push('{"MultiCell":['+largura+','+cellAltura+',"'+texto+'","J"]}');    
    }
  }
  ////////////////////////////////////////
  // x1 coordenaxa x do primeiro ponto  //
  // y1 Ordenada do primeiro ponto      // 
  // x2 Abscissa do segundo ponto       //
  // y2 Ordenada do segundo ponto       //  
  ////////////////////////////////////////
  this.linha=function(x1,y1,x2,y2){
    arrLinha.push('{"Line":['+x1+','+y1+','+x2+','+y2+']}');  
  }
  
  this.corFundo=function(cor,inicio,fim){
    arrLinha.push('{"SetFillColor":["'+cor+'","'+inicio+'","'+fim+'"]}');
  }
  
  this.traco=function(){
    arrLinha.push('{"Cell":[0,0.1,"",1,0,"L"]}');  
  }
  
  this.setFont=function(nome,negrito,tamanho){
    arrLinha.push('{"SetFont":["'+nome+'","'+(negrito==false ? "" : "B")+'",'+tamanho+']}');  
  }
  
  this.fim=function(){
    let retorno="";
    arrLinha.forEach(function(reg){
      retorno+=(retorno=="" ? "" : ",")+reg;  
    })  
    return retorno+"]}";
  }    
}
function concatStr(){
  let retorno   = "";
  let where     = 0;
  this.concat   = function(str,condicao){
    if( condicao == undefined  ){
      retorno+=str;
    } else {
      if( condicao ){
        if( where==0 ){
          str=str.replace("{WHERE}" , "WHERE");
          str=str.replace("{AND}"   , "WHERE");
          where=1;
          retorno+=str;
        } else {
          str=str.replace("{WHERE}" , "AND");
          str=str.replace("{AND}"   , "AND");
          retorno+=str;
        };
      };
    };  
  }
  this.fim=function(){
    return retorno;
  }  
}  
function encherComboUf(elId){
  let ceOpt;
  let ufs=["AC","AL","AM","AP","BA","CE","DF","ES","GO","MA","MG","MS","MT","PA","PB","PE","PI","PR","RJ","RN","RO","RR","RS","SC","SE","SP","TO"];
  let tam=ufs.length;
  for( let lin=0;lin<tam;lin++ ){
    ceOpt 	= document.createElement("option");        
    ceOpt.value = ufs[lin];
    ceOpt.text  = ufs[lin];
    if( ufs[lin]=="SP" ){
      ceOpt.setAttribute("selected","selected");   
    };
    document.getElementById(elId).appendChild(ceOpt);
  }
}
////////////////////////////////////////////////////////////////////////////////////
// Executa uma RegEx retornando true ou false
// /^[A-Z]{3}\d{4}$/  -  Obriga 3 primeiros caracteres e 4 ultimos numericos
// https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Regular_Expressions
////////////////////////////////////////////////////////////////////////////////////
function fncRegEx(exp,str){
  return ( exp.exec(str)===null ? false : true );
};
function janelaDialogo(objeto){
  if( typeof objeto === 'object' ){
    let stlHeight       = "30em";
    let stlLeft         = "20px";
    let stlTop          = "20px";
    let stlWidth        = "50em";
    let tituloBarra     = "Não informado";      // Se não passar assume este
    let fontSizeTitulo  = "2em";                // Variavel qdo se tem frame e naum
    let code            = objeto.code;          // Html recebido da rotina chamadora       
    let ceModal;
    let ceDiv;
    
    for (var key in objeto) {
      switch( key ){
        case "fontSizeTitulo": 
          fontSizeTitulo=objeto[key];
          break;
        case "height": 
          stlHeight=objeto[key];
          break;
        case "left": 
          stlLeft=objeto[key];
          break;
        case "tituloBarra": 
          tituloBarra=objeto[key];
          break;
        case "top": 
          stlTop=objeto[key];
          break;
        case "width": 
          stlWidth=objeto[key];
          break;
      };  
    }; 
    ///////////////////////
    // Variaveis para modal
    ///////////////////////
    contMsg++;
    var divModal  = 'dm'   + contMsg;  // div modal
    var divMsg    = 'dms'  + contMsg;  // div mensagem
    var maior     = retornarZIndex(); 
    ////////////////////////////////////////////////////////////////
    // Div modal para marcar todo fundo de tela como desabilitado //
    ////////////////////////////////////////////////////////////////
    ceModal=document.createElement("div");
    ceModal.id        = divModal; 
    ceModal.className = "divShowModal"; 
    ceModal.style.zIndex=(maior);

    ceFrm=document.createElement("div");
    ceFrm.className       = "frmTable"; 
    ceFrm.style.display   = "block";
    ceFrm.id              = divMsg; 
    ceFrm.style.top       = stlTop;
    ceFrm.style.left      = stlLeft;
    ceFrm.style.width     = stlWidth
    ceFrm.style.height    = stlHeight
    ceFrm.style.position  = "absolute";
    ceFrm.style.zIndex    = (maior+2);
    ////////////////////////////////////////////////////////////////
    // Html principal                                             //
    ////////////////////////////////////////////////////////////////
    let clsHtml = new concatStr();  
    clsHtml.concat("<div id='dragbar' class='frmTituloManutencao' style='font-size:"+fontSizeTitulo+";'><i class='fa fa-close bieRed' onClick='janelaFechar();' style='font-size:30px;float:right;margin-right:15px;margin-top:5px;'></i>"+tituloBarra+"</div>");
    clsHtml.concat(  "<div class='campotexto campo100'>");
    clsHtml.concat(code)
    clsHtml.concat(  "</div>");
    ceFrm.innerHTML=clsHtml.fim();
    ////////////////////////////////////////////////////////////////
    // innerHTML                                                  //
    ////////////////////////////////////////////////////////////////
    let clsInn = new concatStr();
    clsInn.concat("var draggable = document.getElementById('"+divMsg+"');");
    clsInn.concat("var dragbar = document.getElementById('dragbar');");
    clsInn.concat("var posX = parseInt(draggable.style.left.slice(0, -2));");
    clsInn.concat("var posY = parseInt(draggable.style.top.slice(0, -2));");
    clsInn.concat("var clickX, clickY;");
    clsInn.concat("var onDragging = false;");
    clsInn.concat("function handleDrag(event) {");
    clsInn.concat("  if (onDragging) {");
    clsInn.concat("    draggable.style.left = posX + event.clientX - clickX + 'px';");
    clsInn.concat("    draggable.style.top = posY + event.clientY - clickY + 'px';");
    clsInn.concat("  }");
    clsInn.concat("}");
    clsInn.concat("function startDrag(event) {");
    clsInn.concat("  if (!onDragging) {");
    clsInn.concat("    clickX = event.clientX;");
    clsInn.concat("    clickY = event.clientY;");
    clsInn.concat("  }");
    clsInn.concat("  onDragging = true;");
    clsInn.concat("}");
    clsInn.concat("function endDrag() {");
    clsInn.concat("  posX = parseInt(draggable.style.left.slice(0, -2));");
    clsInn.concat("  posY = parseInt(draggable.style.top.slice(0, -2));");
    clsInn.concat("  onDragging = false;");
    clsInn.concat("}");
    clsInn.concat("dragbar.addEventListener('mousedown', startDrag, false);");
    clsInn.concat("document.addEventListener('mouseup', endDrag, false);");
    clsInn.concat("document.addEventListener('mousemove', handleDrag, false);");
    clsInn.concat("function janelaFechar() {");    
    clsInn.concat("  document.getElementById('"+divModal+"').remove();");
    clsInn.concat("  document.getElementById('"+divMsg+"').remove();");
    clsInn.concat("}");
    if( objeto.foco != undefined ){
      clsInn.concat("  document.getElementById('"+objeto.foco+"').foco();");      
    }  
    ///////////////////////////////////////////////////
    // Aqui eh se quero incorporar funcao/oes ao script
    ///////////////////////////////////////////////////
    var scr = document.createElement('script');
    scr.innerHTML = clsInn.fim();
    document.getElementsByTagName('body')[0].appendChild(ceModal);
    document.getElementsByTagName('body')[0].appendChild(ceFrm);
    document.getElementsByTagName('body')[0].appendChild(scr);        
  };
};
function coalesce(recebe,devolve){
  let retorno=recebe;
  if( (recebe==undefined) || (recebe==null) )
    retorno=devolve;  
  return retorno;
};
function fncPlacaValida(plc){
  let retStr    = "ok";
  let continua  = true;
  let contador  = 0;
  
  if( (plc.length==7) || (plc.length==17) ){
    continua=true;
  } else {
    retStr="Tamanho com campo placa_chassi deve ser 7 ou 17!";
    continua=false;
  }      
  
  if( (continua) && (plc.length==7) ){
    if( fncRegEx(/^[A-Z]{3}\d{4}$/,plc)===true )
      contador++;    
    if( fncRegEx(/^[A-Z]{3}\d{1}[A-Z]{1}\d{2}$/,plc)===true )
      contador++;    
    
    if( contador == 0 ){
      retStr="Placa deve ter o formato AAA9999 ou AAA9A99!";
      continua=false;  
    };  
  };  
  return retStr;    
};  
///////////////////////////////////////////////////////////
// Como vou executar em varios formulario deixo este padrão
///////////////////////////////////////////////////////////
function montarJanelaConfirmacao(mensagem,fncOk){
  let clsCode = new concatStr();  
  clsCode.concat("<div class='campo100' style='height:25px;'></div>");          
  clsCode.concat("<div class='campotexto campo05'></div>");
  clsCode.concat("<div class='campotexto campo95' style='padding-top:10px;text-align:center;'>");
  clsCode.concat("  <label class='lblConfirme'>"+mensagem+"</label>");
  clsCode.concat("</div>");
  clsCode.concat("<div class='campotexto campo30'></div>");          
  clsCode.concat("<div id='btnConfirmar' onClick='"+fncOk+"' class='btnImagemEsq bie15 bieAzul bie'><i class='fa fa-check'>Sim</i></div>");
  clsCode.concat("<div id='btnCancelar' onClick='janelaFechar();' class='btnImagemEsq bie15 bieRed bie'><i class='fa fa-reply'>Nao</i></div>"); 
  return clsCode.fim();
}  
function alteraLabelFiltro(data,texto,fnc){
  this.addEventListener('click', function(e){
    ///////////////////////////////////////////////////////////////////////////////////////////
    // preventDefault( desabilita o evento padrao do elemento no caso <a> para chamar um link )
    ///////////////////////////////////////////////////////////////////////////////////////////
    e.preventDefault();
    if( (e.target.tagName === 'A') && (e.target.getAttribute(data) !== undefined) ){
      let elBtn=this.querySelector('button');          
      elBtn.innerHTML = texto + e.target.innerHTML;
      ///////////////////////////////////////////////////////
      // Atribuindo novo valor para filtros( igual combobox )
      ///////////////////////////////////////////////////////
      if( elBtn.getAttribute("data-indice") !== undefined ){
        elBtn.setAttribute("data-indice",e.target.getAttribute(data));
        if( fnc !== undefined )
          eval(fnc);
      };  
    } else {
      return false;
    }
  });
};  
function fncValidaHora(param){
  let arr     = param.split(":");
  let retorno = "ok";
  let hora,minuto;
  if( arr.length==2 ){
    hora    = parseInt(arr[0]);
    minuto  = parseInt(arr[1]);
    if( (hora<0) || (hora>23) )
      retorno="HORA DE TER INTERVALO DE 00 A 24";
    if( (minuto<0) || (minuto>59) )
      retorno="MINUTO DE TER INTERVALO DE 00 A 59";
  } else {
    retorno="CAMPO HORA DEVE TER O FORMATO 99:99";  
  }
  return ( retorno=="ok" ? true : false );  
};
function fncColObrigatoria(arr){
  try{          
    let buscaCol= new clsObterColunas(this,arr);
    buscaCol.appFilter();
    if( buscaCol.getNumCols() != 0  ){
      throw "Não localizado coluna na grade para rotina!";   
    } else {
      return buscaCol.getObjeto();
    };
  } catch(e){
    gerarMensagemErro("ped",e,{cabec:"Erro"});          
  };  
};