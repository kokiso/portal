function fncLerExcel(js,rotPhp){
  
  
  let arqPhp=window.location.pathname.replace("/portaltracf/","");
  
  try{
    let clsErro = new clsMensagem("Erro");
    clsErro.notNull("ARQUIVO"       ,edtArquivo.value);
    if( clsErro.ListaErr() != "" ){
      clsErro.Show();
    } else {
      let clsJs   = jsString("lote");  
      clsJs.add("rotina"      , "impExcel"                    );
      clsJs.add("login"       , jsPub[0].usr_login            );
      clsJs.add("titulo"      , this.trazCampoExcel(js)       );
      let envPhp=clsJs.fim();
      let fd = new FormData();
      fd.append(rotPhp   , envPhp              );
      fd.append("arquivo" , edtArquivo.files[0] );
      let msg     = requestPedido(arqPhp,fd); 
      let retPhp  = JSON.parse(msg);
      if( retPhp[0].retorno == "OK" ){
        //////////////////////////////////////////////////////////////////////////////////
        // O novo array não tem o campo idUnico mas a montarHtmlCE2017 ja foi executada //
        // Campo obrigatório se existir rotina de manutenção na table devido Json       //
        // Esta rotina não tem manutenção via classe clsTable2017                       //
        // jsCrv.registros=objCrv.addIdUnico(retPhp[0]["dados"]);                       //
        //////////////////////////////////////////////////////////////////////////////////
        jsExc.registros=retPhp[0]["dados"];
        objExc.montarBody2017();
      };  
      /////////////////////////////////////////////////////////////////////////////////////////
      // Mesmo se der erro mostro o erro, se der ok mostro a qtdade de registros atualizados //
      // dlgCancelar fecha a caixa de informacao de data                                     //
      /////////////////////////////////////////////////////////////////////////////////////////
      gerarMensagemErro("Ctg",retPhp[0].erro,{cabec:"Aviso"});    
    };  
  } catch(e){
    gerarMensagemErro("exc","ERRO NO ARQUIVO XML",{cabec:"Aviso"});          
  }          
}