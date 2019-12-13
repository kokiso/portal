function fncEncherCombo(obj){
  ////////////////////////////////////////////////////////////////////////////////////////////////
  // { "varFrame"     : frComplementar          - Para quando se chama um formulario filho      //
  //  ,"combo"        : "cbPgr"                 - Combo que vai receber os itens                //
  //  ,"append"       :"pgrmercadoria"          - Rotina php                                    //
  //  ,"form"         :"Atlas_PgrMercadoria"    - Formulario que chama o php                    //
  //  ,"campoCod"     : "PGR_CODIGO"            - Primeiro campo no combo( value )              //
  //  ,"campoDes"     : "PGR_NOME"              - Segundo campo no combo( options )             //
  //  ,"localStorage" : "frameComplementar"     - Se for abrir form filho quais items do select //
  //  ,"opcTodos"     : ?????                   - Se vai existir a opcao todos (padrao true)    //
  ////////////////////////////////////////////////////////////////////////////////////////////////
  var opcTodos=(obj.opcTodos==undefined ? true : false );

  if( obj.varFrame != undefined ){
    msg=JSON.parse(localStorage.getItem(obj.localStorage));
    var ceOpt	  = document.createElement("option");        
    ceOpt.value = jsNmrs(msg[0].numpgr).emZero(4).ret();   
    ceOpt.text  = msg[0].despgr;
    document.getElementById(obj.combo).appendChild(ceOpt);
  } else {
    clsJs = jsString("lote");  
    clsJs.add("rotina"      , obj.rotina          );
    clsJs.add("login"       , jsPub[0].usr_login  );
    fd = new FormData();
    fd.append(obj.append , clsJs.fim());
    msg     = requestPedido(obj.form,fd); 
    retPhp  = JSON.parse(msg);
    if( retPhp[0].retorno == "OK" ){
      msg=retPhp[0]["dados"].length;
      if(msg==0){
        var ceOpt 	= document.createElement("option");        
        ceOpt.value = "*";
        ceOpt.text  = "SEM DIREITO"
        document.getElementById(obj.combo).appendChild(ceOpt);
      } else {
        for( var lin=0;lin<msg;lin++ ){
          var ceOpt 	= document.createElement("option");        
          ceOpt.value = jsNmrs(retPhp[0]["dados"][lin][obj.campoCod]).emZero(4).ret();   
          ceOpt.text  = retPhp[0]["dados"][lin][obj.campoDes];
          if( lin==0 ){
            ceOpt.setAttribute("selected","selected");
          };
          document.getElementById(obj.combo).appendChild(ceOpt);
        };
        if( opcTodos ){  
          var ceOpt 	= document.createElement("option");        
          ceOpt.value = "0";
          ceOpt.text  = "TODOS";
          document.getElementById(obj.combo).appendChild(ceOpt);
        };
      };
    };  
  };
}