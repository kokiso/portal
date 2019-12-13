var estilo='';
	estilo+='.TabControl{' 
  estilo+='  width:100%;'
  estilo+='  overflow:hidden;'
  estilo+='}'  
	estilo+='.TabControl #content{' 
  estilo+='}'  
	estilo+='.TabControl .abas{'
  estilo+='  display:inline;'
  estilo+='  list-style:none;' 
  estilo+='}'  
	estilo+='.TabControl .abas li{'
  estilo+='  float:left;'
  estilo+='}'
	estilo+='.TabTable{'
  estilo+='  border-collapse: collapse;'
  estilo+='  border-spacing: 0;'
  estilo+='  width:100%;'
  estilo+='  font-size:12px;'
  estilo+='}'
	estilo+='.TabThead tr {'
  estilo+='  background: #efefef;'
  estilo+='  text-align:center;'
  estilo+='}'
	estilo+='.TabTh{'
  estilo+='  border: 1px solid #c0c0c0;'
  estilo+='  padding: 0.5rem;'
  estilo+='}'
	estilo+='.TabTd{'
  estilo+='  border: 1px solid #c0c0c0;'
  estilo+='  padding: 0.4rem;'
  estilo+='}'
  estilo+='.TabTable tBody tr:nth-child(even){'
  estilo+='    background-color:#f2f2f2;'
  estilo+='  }'
	estilo+='.aba{'  
  estilo+='  float:left;'
  estilo+='  width:7em;'
  estilo+='  border:1px solid black;'
  estilo+='  border-top-left-radius: 10px;'
  estilo+='  border-top-right-radius: 10px;'
  estilo+='  padding: 5px;'
  estilo+='  padding-left:0.7em;'
  estilo+='  box-shadow: 5px 10px 5px #888888;'
  estilo+='  background:white;'
  estilo+='  font-size: 1.4em;'
  estilo+='}'  
	estilo+='.ativa{'  
  estilo+='  background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #efefef), color-stop(100%, #1c5a85));';
  estilo+='  color:white;'
  estilo+='  cursor:default;'
  estilo+='}'  
	estilo+='.TabControl .conteudo{'
  estilo+='  display:none;'
  estilo+='}' 
  estilo+='.tfFootTr{';  
  estilo+='  color:white !important;';
  estilo+='  background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #efefef), color-stop(100%, #1c5a85));';
  estilo+='}';
  
//  
function selecionado(obj,numAba,div){
  elUL  = obj.parentNode.parentNode;      //Voltando para a UL
  elDIV = document.getElementById(div);   //Divs relacionadas aos LI
  var itens=elUL.getElementsByClassName("aba");
  for( var li=0; li<itens.length; li++ ){
    itens[li].classList.remove("selected");
    itens[li].classList.remove("ativa");
    elDIV.children[li].style.display="none";
    if( li==numAba ){
      obj.classList.add("selected");  
      obj.classList.add("ativa");  
      elDIV.children[li].style.display="block";
    }
  }
}
//--
function clsTab(parJS) {  
  var self = this;
  document.getElementById(parJS.idStyle).insertAdjacentHTML("afterbegin", estilo);

  this.htmlTab=function(){
    var ret     = '';
    var th      = '';
    var somar   = '';
    var tam     = 0;
    var ceContext,ceBody,ceDiv,ceLi,ceTable,ceFoot,ceTh,ceThead,ceTr,ceTd,func;

    var dPai                  = document.createElement("div");
    dPai.id                   = "pai"; 
    dPai.className            = "TabControl"; 
    dPai.style.marginBottom   = "20px";    

    var dFilho             = document.createElement("div");
    dFilho.id              = "header"; 
    dFilho.style.overflowY = "hidden";        
    
    var ceUl             = document.createElement("ul");
    ceUl.id              = "ulPai";         
    ceUl.className       = "abas"; 
    //////////////////////
    // Montando as abas //
    //////////////////////
    var tam=parJS.abas.length;
    for( var lin=0; lin<tam;lin++ ){
      ceLi     = document.createElement("li");
      ceLi.id  = "li"+parJS.abas[lin].nomeAba;
      
        ceDiv     = document.createElement("div");
        ceDiv.id  = "li"+parJS.abas[lin].nomeAba;
        func      = 'selecionado(this,'+parJS.abas[lin].id+',\'content\');';
        ceDiv.setAttribute("onclick",func);
        ceDiv.className  = ( lin==0 ? "aba selected ativa" : "aba" ); 
        ceDiv.style.width = parJS.abas[lin].widthAba;   
          ceContext = document.createTextNode(parJS.abas[lin].labelAba);
        ceDiv.appendChild(ceContext);
      ceLi.appendChild(ceDiv);
      ceUl.appendChild(ceLi);
    };
    dFilho.appendChild(ceUl);
    dPai.appendChild(dFilho); 

    var dFilho             = document.createElement("div");
    dFilho.id              = "content"; 
    dFilho.style.overflow  = "auto";
    //////////////////////
    // Montando as divs //
    //////////////////////
    for( var lin=0; lin<tam;lin++ ){  
      ceDiv                 = document.createElement("div");
      ceDiv.id              = "div"+parJS.abas[lin].nomeAba;
      ceDiv.className       = "conteudo"; 
      ceDiv.style.display   = (lin==0 ? 'block' : 'nome');
      ceDiv.style.width     = parJS.abas[lin].widthTable; 
      ceDiv.style.height    = parJS.abas[lin].heightTable;
      ceDiv.style.border    = "1px solid silver";
      ceDiv.style.borderRadius = "0px 0px 6px 6px";
      ceDiv.style.boxShadow= "3px 3px 3px #999";
      

      if( (tagValida(parJS.abas[lin].rolagemVert)) && (parJS.abas[lin].rolagemVert==true) ){  
        ceDiv.style.overflowY = "auto"; 
      };
      ceDiv.style.overflow = "auto";       
      th      = parJS.abas[lin].head;
      ////////////////////////////////////
      // Montando a table para cada div //
      ////////////////////////////////////
      if( th != undefined ){
        ceTable                   = document.createElement("table");
        ceTable.id                = parJS.abas[lin].table;
        ceTable.className         = "TabTable";

        ceTable.style.width       = "100%"; 
        ceTable.style.tableLayout = "fixed";
        ceTable.style.backgroundColor = "white";     
        ceTable.style.border      = "1px solid silver";


          ceThead = document.createElement("thead");
            ceThead.className         = "TabThead";
            ceTr    = document.createElement("tr");
        if( tagValida(parJS.abas[lin].opcExcluir) && (parJS.abas[lin].opcExcluir=='S') )
          excluir=true;
        
        var tamH  = th.length;
        for( var linH=0; linH<tamH;linH++ ){  
          ceTh    = document.createElement("th");
          ceTh.className         = "TabTh";
          ceTh.style.width       = th[linH].width;
          if(th[linH].width=="0em"){
            ceTh.className  = "colunaOculta";  
          };
          ceContext = document.createTextNode(th[linH].labelCol);
          ceTh.appendChild(ceContext);
          ceTr.appendChild(ceTh);  
        };
        ceThead.appendChild(ceTr);
        ceTable.appendChild(ceThead);  
        
        if( tagValida(parJS.abas[lin].somarCols) ){
          ceFoot = document.createElement("tfoot");
            ceTr            = document.createElement("tr");
            ceTr.className  = "tfFootTr";
          
          for( var linH=0; linH<tamH;linH++ ){
            ceTd    = document.createElement("td");
            ceTd.className         = "TabTd";
            if(linH==0){
              ceContext = document.createTextNode("TOTAL");  
              ceTd.appendChild(ceContext);
            }
            if(th[linH].width=="0em"){
              ceTd.className  = "colunaOculta";  
            } else {  
              if( th[linH].fieldType=='flo'){
                ceTd.className       = "edtDireita"; 
                ceContext = document.createTextNode("0,00");  
                ceTd.appendChild(ceContext);
              };
            };  
            ceTr.appendChild(ceTd);  
          };
          ceFoot.appendChild(ceTr);
          ceTable.appendChild(ceFoot);
        };
        ceBody=document.createElement("tbody");
        ceBody.id = "tbody_"+parJS.abas[lin].table;
        ceTable.appendChild(ceBody);
        ceDiv.appendChild(ceTable);
        dFilho.appendChild(ceDiv);
      };
      dPai.appendChild(dFilho);
    };
    var obj=document.getElementById(parJS.pai);
    obj.appendChild(dPai);
  };
  //
  this.qualColuna=function(tbl,arr){
    var tam=parJS.abas.length;  
    for( var lin=0; lin<tam;lin++ ){
      if( parJS.abas[lin].table==tbl ){
        cmps=parJS.abas[lin].head;
        break;
      };    
    };  
    if( cmps != undefined ){
      var retArr=[];
      var tamArr  = arr.length;
      tam     = cmps.length;
      for( var linArr=0; linArr<tamArr;linArr++ ){
        for( var lin=0; lin<tam;lin++ ){
          if( cmps[lin].labelCol == arr[linArr] ){
            retArr.push(lin);
            break;
          };
        };  
      };
      return retArr;
    };  
  };  
  this.novoRegistro=function(tbl,arr){
    var tam=parJS.abas.length;
    var ceContext,ceImg,ceInput,ceTable,ceTr,ceTd,cmps;
    var posVet; //Posição do vetor arr para adicionar campo 
    //var abaExc=false;  
    ////////////////////////////////////////////////////
    // Procurando a table para pegar os campos "cmps" //
    ////////////////////////////////////////////////////
    for( var lin=0; lin<tam;lin++ ){
      if( parJS.abas[lin].table==tbl ){
        cmps=parJS.abas[lin].head;
        break;
      };    
    };  
    ////////////////////////////////////////////////////
    // Se encontrar os da table campos "cmps"         //
    ////////////////////////////////////////////////////
    if( cmps != undefined ){
      ceTable = document.getElementById("tbody_"+tbl);	
      ceTr    = document.createElement("tr");
      posVet  = 0;
      tam     = cmps.length;
      var cntd;     // Conteudo do campo
      var frmt;     // Formato do campo
      var snEditar; // Se a celula pode ser editada
      var snData;   // Se é uma data para quando for editar
      for( var lin=0; lin<tam;lin++ ){
        cntd  = "";
        ceTd  = document.createElement("td");
        ceTd.className = "TabTd";        
        //////////////////////////////////////////
        // Informa se a coluna pode ser editada //
        //////////////////////////////////////////
        snEditar  = "N";
        if( (tagValida(cmps[lin].editar)) && (cmps[lin].editar=="S") ){
          snEditar  = "S";
        };
        //
        snData="N";
        switch (cmps[lin].fieldType) {
          case "int":
            cntd=(arr[posVet].toString()).replace(/[^0-9-]/g,"");
            frmt=4;
            if( tagValida(cmps[lin].formato) ){
              frmt=(cmps[lin].formato).replace("i","");  
            }
            cntd=jsNmrs(cntd).emZero(frmt).ret();
            ceContext = document.createTextNode(cntd);
            ceTd.appendChild(ceContext);
            ceTd.classList.add("textoCentro");
            posVet++;
            break;
          case "dat":
            cntd=arr[posVet];
            ceContext = document.createTextNode(cntd);
            ceTd.appendChild(ceContext);
            if(cmps[lin].width=="0em"){
              ceTd.className  = "colunaOculta";  
            }  
            snData="S"
            posVet++;            
            break;    
          case "flo":
            cntd=arr[posVet];
            ceContext = document.createTextNode(cntd);
            ceTd.appendChild(ceContext);
            if(cmps[lin].width=="0em"){
              ceTd.className  = "colunaOculta";  
            } else {  
              ceTd.classList.add("textoDireita");
            };  
            posVet++;            
            break;    
          case "str":
            cntd=arr[posVet];
            ceContext = document.createTextNode(cntd);
            ceTd.appendChild(ceContext);
            if(cmps[lin].width=="0em"){
              ceTd.className  = "colunaOculta";  
            }  
            posVet++;            
            break;    
          case "img":
            if( cmps[lin].classe.indexOf("excLista") != -1 ){          
              ceImg		= document.createElement('i');	
              ceImg.setAttribute('class','fa fa-close vermelho excLista');
              ceImg.setAttribute('style','margin-left:15px');
              ceImg.setAttribute('style','font-size:20px');
              ceImg.addEventListener('click',function(){
                document.getElementById(tbl).deleteRow(this.parentNode.parentNode.rowIndex);
                self.somarColuna(tbl);    
              });
              ceContext = document.createTextNode('');	
              ceImg.appendChild(ceContext);
              ceTd.classList.add("textoCentro");              
              ceTd.appendChild(ceImg);	
            }
            break;    
          case "chk":
            ceInput=document.createElement('input');      
            ceInput.setAttribute("type","checkbox");
            ceInput.setAttribute("class","tdInput");
            ceInput.setAttribute("name","chkOpc");
            ceTd.appendChild(ceInput);
            break;    
        };
        ///////////////////////////////////////////////////////
        // Adicionando o evento para poder editar uma celula //
        ///////////////////////////////////////////////////////
        if( snEditar  =='S' ){
          ceTd.addEventListener('click',function(){
            /////////////////////////////////////////////////////////
            // Se clicar 2 vezes não faz a segunda gerando <imput> //
            /////////////////////////////////////////////////////////
            if( this.children.length>0)
              return;
            
            var ceInput		= document.createElement('input');	
            ceInput.setAttribute('style' ,'width:100%');
            ceInput.setAttribute('value' ,this.innerHTML);
            ceInput.setAttribute('type'  ,'text');
            ceInput.setAttribute('id'    ,'edtAlterar');
            //
            if( this.classList.contains('textoDireita'))
              ceInput.setAttribute('class'  ,'textoDireita');
            if( snData=="S" )
              ceInput.setAttribute('onKeyUp','mascaraData(this,event);');
            //
            ceInput.addEventListener('blur',function(){
              var elTd=this.parentNode;
              if( this.classList.contains('edtDireita'))
                var novoCntd=cmp.floatNB(this.value);
              else    
                var novoCntd=this.value;
              //    
              elTd.innerHTML=novoCntd;
              this.remove();
              
              if( elTd.classList.contains('textoDireita'))
                self.somarColuna(tbl);  
            });  
            this.innerHTML='';
            this.appendChild(ceInput);
            this.children[0].focus();
          });  
        };  
        //
        ceTr.appendChild(ceTd);  
      };
      
      ceTable.appendChild(ceTr);
      self.somarColuna(tbl);  
    };
  };  
  //
  this.somarColuna=function(tbl){
    ///////////////////////////////////////////////////////
    // Pegando o array com coluna(s) a ser(em) somada(s) //
    ///////////////////////////////////////////////////////
    var cols=[];
    parJS.abas.forEach(function(aba){
      if( aba.table==tbl ){
        if( tagValida(aba.somarCols) )
          cols=aba.somarCols;
      };
    });
    //
    var cmp   = new clsCampo();
    var obj   = document.getElementById(tbl).getElementsByTagName('tbody')[0];
    var nl    = obj.rows.length; //Numero de linhas
    ////////////////////////
    // Se for 0 gera erro //
    ////////////////////////
    if( nl>0 )    
      var nc    = obj.rows[nl-1].cells.length; //Numero de colunas
    //
    try{    
      for( arr in cols ){
        var somar = 0.00;
        for(var li = 0; li < nl; li++){
          for(var ci = 0; ci < nc; ci++){      
            if( cols[arr]==ci ){
              somar+=jsNmrs(obj.rows[li].cells[ci].innerHTML).dolar().ret();
              break;
            };  
          };    
        };
        var objF   = document.getElementById(tbl).getElementsByTagName('tfoot')[0];
        objF.rows[0].cells[cols[arr]].innerHTML=cmp.floatNB(somar);  
      };
    }finally{
      cmp=null;
    };
  };
  this.limparTable=function(tbl){
    var obj   = document.getElementById(tbl);
    var table = obj.getElementsByTagName('tbody')[0];
    var nl    = table.rows.length; //Numero de linhas
    while( nl>0 ){
      table.deleteRow(nl-1);
      nl--;
    };
    minhaAba.somarColuna(tbl);
  };
  this.tblJson=function(tbl){
    var tam=parJS.abas.length;    
    ////////////////////////////////////////////////////
    // Procurando a table para pegar os campos "cmps" //
    ////////////////////////////////////////////////////
    for( var lin=0; lin<tam;lin++ ){
      if( parJS.abas[lin].table==tbl ){
        cmps=parJS.abas[lin].head;
        break;
      }    
    };
    var tamH= cmps.length;  
    var el      = document.getElementById(tbl).getElementsByTagName('tbody')[0];    
    var nl      = el.rows.length;
    var nc      = 0;
    var retorno = '[';
    
    if( nl>0 ){
      for( var lin=0; lin<nl; lin++ ){ 
        retorno+=( lin==0 ? '{' :',{' );      
        for( var col=0; col<tamH; col++ ){
          var cntd="";
          if( ["dat","flo","int","str"].indexOf( cmps[col].fieldType ) != -1 ){
            cntd=el.rows[lin].cells[col].innerHTML;
            if( cmps[col].fieldType=="flo" ){
              cntd=cntd.replace(",",".");
            };
            retorno +=( col==0 ? '' :',' )+'"'+cmps[col].labelCol+'":"'+cntd+'"';            
          };
        };
        retorno+='}';
      };
      retorno+=']';
      return JSON.parse(retorno);
    } else {
      /////////////////////////////////////////////////////////////////
      // retornando um json com tam 0 exemplo ERP_SatConsultaFin.php //
      /////////////////////////////////////////////////////////////////
      return '[]';
    };
  }; 
};