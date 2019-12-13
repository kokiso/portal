"use strict";
function copiaDocumento(lancto){
  //////////////////////
  // Enviando para o Php
  //////////////////////
  clsJs=jsString("lote");            
  clsJs.add("rotina"    , "copiadocto"      );              
  clsJs.add("login"     ,jsPub[0].usr_login );              
  clsJs.add("lancto"   , lancto             );              

  let fd = new FormData();
  fd.append("cpcr" , clsJs.fim());
  msg=requestPedido("Trac_CpCr.php",fd); 
  retPhp=JSON.parse(msg);
  if( retPhp[0].retorno=="OK" ){
    let pgr=retPhp[0].tblPgr;
    //////////////////////////////////////////////
    // INICIA A IMPRESSAO DA COPIA DE DOCUMENTO //
    //////////////////////////////////////////////
    let rel = new relatorio();
    rel.tamFonte(8);
    rel.iniciar();
    rel.traco();
    rel.pulaLinha(1);
    rel.corFundo("cinzaclaro",9,190);    
    rel.cell(20,"Lancamento:"   ,{borda:0,negrito:true});
    rel.cell(25,pgr.PGR_LANCTO  ,{negrito:false,emZero:6});
    rel.cell(40,"Titulo lancado atraves de:",{negrito:true});
    rel.cell(55,pgr.PTT_NOME,{negrito:false});
    rel.cell(5,pgr.PGR_CODPTP,{negrito:true,complemento:":"});
    rel.cell(30,pgr.PTP_NOME,{negrito:false});

    rel.pulaLinha(10);
    rel.traco();
    rel.cell(20,"Documento:"      ,{borda:0,pulaLinha:1,negrito:true});
    rel.cell(25,pgr.PGR_DOCTO     ,{negrito:false});
    rel.cell(15,"Emissão:"        ,{negrito:true});
    rel.cell(25,pgr.PGR_DTDOCTO   ,{negrito:false,data:true});
    rel.cell(25,"Lancto origem:"  ,{negrito:true});
    rel.cell(30,pgr.PGR_MASTER    ,{negrito:false,emZero:6});
    rel.cell(20,"Data movto:"     ,{negrito:true});
    rel.cell(20,pgr.PGR_DTMOVTO   ,{negrito:false,data:true});
    //
    rel.cell(20,"Tipo docto:"     ,{negrito:true,pulaLinha:5});
    rel.cell(65,pgr.TD_NOME       ,{negrito:false});
    rel.cell(25,"Forma cobranca:" ,{negrito:true});
    rel.cell(25,pgr.FC_NOME       ,{negrito:false});
    //
    rel.cell(20,"Favorecido:"     ,{negrito:true,pulaLinha:5});
    rel.cell(120,pgr.FVR_NOME     ,{negrito:false});
    rel.cell(20,( pgr.FVR_FISJUR=="J" ? "CNPJ:" : "CPF:" ),{negrito:true,align:"R"});
    rel.cell(25,pgr.FVR_CNPJCPF   ,{negrito:false,align:"L"});
    //
    rel.cell(20,"Banco:"          ,{negrito:true,pulaLinha:5});
    rel.cell(65,pgr.BNC_NOME      ,{negrito:false});
    rel.cell(20,'Vencimento:'     ,{negrito:true});
    rel.cell(35,pgr.PGR_VENCTO    ,{negrito:false,data:true});
    rel.cell(20,'Quitação:'       ,{negrito:true,align:"R"});
    rel.cell(25,pgr.PGR_DATAPAGA  ,{negrito:false,data:true,align:"L"});
    //
    rel.cell(20,"Parcela:"        ,{negrito:true,pulaLinha:5});
    rel.cell(65,pgr.PGR_PARCELA   ,{negrito:false,emZero:3});
    rel.cell(20,'Num parcelas:'   ,{negrito:true});
    rel.cell(65,pgr.PM_PARCELA    ,{negrito:false,emZero:3});
    //
    rel.cell(20,"Observação:"       ,{negrito:true,pulaLinha:5});    
    rel.cell(120,pgr.PGR_OBSERVACAO ,{negrito:false});
    ///////////////////////////////////////
    // Determinando a altura para retangulo
    ///////////////////////////////////////
    let alt=76;
    rel.cell(190,"DEMONSTRATIVO DE VALORES",{negrito:false,pulaLinha:11,align:"C"});    
    rel.retangulo(10  ,(alt-1),190  ,15 ,"D"  );
    rel.retangulo(11  ,alt    ,30   ,6  ,"DF" );
    rel.retangulo(42  ,alt    ,30   ,6  ,"DF" );
    rel.retangulo(73  ,alt    ,30   ,6  ,"DF" );
    rel.retangulo(104 ,alt    ,30   ,6  ,"DF" );
    rel.retangulo(135 ,alt    ,30   ,6  ,"DF" );
    rel.retangulo(166 ,alt    ,33   ,6  ,"DF" );    
    rel.cell(31,"VALOR EVENTO"    ,{negrito:false,pulaLinha:6,align:"C"});
    rel.cell(31,"VALOR PARCELA"   ,{align:"C"});
    rel.cell(31,"DESCONTO"      );
    rel.cell(31,"ACRESCIMO"     );
    rel.cell(31,"OUTROS"        );    
    rel.cell(31,"VALOR LIQUIDO" );
    //
    alt=(alt+7);
    rel.retangulo(11  ,alt,30,6 ,"D");
    rel.retangulo(42  ,alt,30,6 ,"D");
    rel.retangulo(73  ,alt,30,6 ,"D");
    rel.retangulo(104 ,alt,30,6 ,"D");
    rel.retangulo(135 ,alt,30,6 ,"D");
    rel.retangulo(166 ,alt,33,6 ,"D");    
    rel.cell(31,pgr.PGR_VLREVENTO,{negrito:false,pulaLinha:7,align:"C",moeda:true});
    rel.cell(31,pgr.PGR_VLRPARCELA                                );
    rel.cell(31,pgr.PGR_VLRDESCONTO                               );    
    rel.cell(31,pgr.PGR_VLRMULTA                                  );        
    rel.cell(31,(pgr.PGR_VLRPIS+pgr.PGR_VLRCOFINS+pgr.PGR_VLRCSLL));        
    rel.cell(31,pgr.PGR_VLRLIQUIDO                                );
    rel.moeda(false);  
    //
    rel.pulaLinha(1);
    rel.align("L");
    rel.cell(190,"DEMONSTRATIVO CONTABIL",{negrito:false,pulaLinha:11,align:"C"});        
    rel.retangulo(10  ,100,190  ,7 ,"DF"  );    
    rel.cell(30,"GERENCIAL" ,{pulaLinha:6,align:"L"});    
    rel.cell(60,"DESCRICAO");
    rel.cell(25,"DEBITO",{align:"R"});    
    rel.cell(25,"CREDITO");        
    rel.cell(20,"COMPET",{align:"C"});
    rel.cell(20,"CONTABIL",{align:"L"});        
    
    let rat=retPhp[0].tblRat;
    rel.setAltura(108);   //Marcando a tela para poder incrementar a altura devido foreach
    let totDeb=0;
    let totCre=0;
    
    rat.forEach(function(reg,item){    
      rel.pulaLinha( (item==0 ? 6 : 4 ) );    
      rel.cell(30,reg.RAT_CODCC,{align:"L"});    
      rel.cell(60,reg.CC_NOME);
      rel.cell(25,reg.RAT_DEBITO,{moeda:true,align:"R"});    
      rel.cell(25,reg.RAT_CREDITO,{moeda:true});    
      rel.cell(20,reg.RAT_CODCMP,{moeda:false,align:"C"});
      rel.cell(20,reg.RAT_CONTABIL);        
      
      totDeb+=jsNmrs(reg.RAT_DEBITO).dolar().ret();
      totCre+=jsNmrs(reg.RAT_CREDITO).dolar().ret();
    });
    ///////////////////////////////////////////////////////
    // Aqui tenho que tirar o ultimo pulaLinha da altura //
    ///////////////////////////////////////////////////////
    rel.linha(110,rel.getAltura(-2),149,rel.getAltura(-2));  
    rel.cell(30,"",{align:"L",pulaLinha:5});    
    rel.cell(60,"TOTAL");    
    rel.cell(25,totDeb,{moeda:true,align:"R"});        
    rel.cell(25,totCre);    
    rel.moeda(false);
    rel.align("L");    
    ///////////////
    // Parcelamento
    ///////////////
    rel.cell(90,"DEMONSTRATIVO PARCELAMENTO",{negrito:false,pulaLinha:11,align:"C"});        
    rel.retangulo(10  ,rel.getAltura(-2),90  ,7 ,"DF"  );    
    rel.cell(20,"LANCTO" ,{pulaLinha:6,align:"L"});    
    rel.cell(20,"VENCTO");
    rel.cell(20,"VALOR",{align:"R"});    
    rel.cell(20,"BAIXA",{align:"L"});        
    let par=retPhp[0].tblPar;
    totDeb=0;
    par.forEach(function(reg,item){ 
      rel.pulaLinha( (item==0 ? 6 : 4 ) );
      rel.cell(20,reg.PGR_LANCTO,{emZero:6});    
      rel.cell(20,reg.PGR_VENCTO,{data:true});      
      rel.cell(20,reg.PGR_VLRLIQUIDO,{moeda:true,align:"R"});          
      rel.cell(20,reg.PGR_DATAPAGA,{data:true,align:"L",moeda:false});      
      totDeb+=jsNmrs(reg.PGR_VLRLIQUIDO).dolar().ret();
    });
    ///////////////////////////////////////////////////////
    // Aqui tenho que tirar o ultimo pulaLinha da altura //
    ///////////////////////////////////////////////////////
    rel.linha(50,rel.getAltura(-2),70,rel.getAltura(-2));  
    rel.cell(20,"",{align:"L",pulaLinha:5});    
    rel.cell(20,"TOTAL");    
    rel.cell(20,totDeb,{moeda:true,align:"R"});        
    
    let envPhp=rel.fim();
    ///////////////////////////////////////////////////
    // PREPARANDO UM FORMULARIO PARA VER O RELATORIO //
    ///////////////////////////////////////////////////
    var formreport = document.createElement("form")
    formreport.setAttribute("name", "relatorio");
    formreport.setAttribute("id", "relatorio");
    formreport.setAttribute("method", "post"); 
    formreport.setAttribute("target", "_blank"); 
    formreport.setAttribute("action", "classPhp/imprimirsql.php"); 
    var camposql = document.createElement('input');
    camposql.setAttribute('type','hidden');
    camposql.setAttribute('name','sql');
    camposql.setAttribute('id','sql');
    camposql.setAttribute('value',envPhp);
    formreport.appendChild(camposql);   
    document.body.appendChild(formreport);    
    formreport.submit();
    formreport.remove();    
  };  
};