var clsTabela = function(obj){
  var tbl=obj;
  debugger;
  return {
    regs : function(){
      return tbl.rows.length;
    },
    getInt : function(r,c){
      let valor=tbl.rows[r].cells[c].innerHTML;
      return parseInt(valor);
    },
    set : function(r,c,vlr){
      tbl.rows[r].cells[c].innerHTML=vlr;
    }
  }  
  
  /*
  ret    = str;
  
  padrao = { upper:true
                ,lower:false
                ,removeacentos:true
                ,trim:true
           };
  
  var fncTrim = function(){
    if( padrao.trim )
      ret=ret.replace(/^\s+/,"").replace(/\s+$/,"");
  };
  var fncUpperCase = function(){
    ret=ret.toUpperCase();
  };
  
  return {
    trim : function(){
      return str.replace(/^\s+/,"").replace(/\s+$/,"");
    },
  
    maiuscula : function(){
      return ret.trim().toUpperCase();
    }
  }
  */
};
/*  


var teste=new tabela('  oi  ');

console.log(teste.maiuscula());

</script>
*/