<?php
?>
<!DOCTYPE html>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <script language="javascript" type="text/javascript"></script>
  <head>
    <meta charset="utf-8">
    <title>Contrato</title>
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/bootstrapNative.css">
    <script src="js/bootstrap-native.js"></script>    
    <!-- bootstrap nativo javascript -->
    <link rel="stylesheet" href="css/css2017.css">
    <link rel="stylesheet" href="css/cssTable2017.css">
    <script src="js/js2017.js"></script>
    <script src="js/jsTable2017.js"></script>
    <script src="tabelaTrac/f10/tabelaGrupoModeloProdutoF10.js"></script>        
    <script src="tabelaTrac/f10/tabelaEnderecoF10.js"></script>
    <script src="tabelaTrac/f10/tabelaColaboradorF10.js"></script>    
    <script src="tabelaTrac/f10/tabelaPlacaF10.js"></script>        
    <script>      
    </script>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="divTelaCheia">
      <div id="indDivInforme" class="indTopoInicio indTopoInicio100">
        <div class="indTopoInicio_logo"></div>
        <div class="colMd12" style="float:left;">
          <div class="infoBox">
            <span class="infoBoxIcon corMd10"><i class="fa fa-folder-open" style="width:25px;"></i></span>
            <div class="infoBoxContent">
              <span id="spnTitulo" class="infoBoxText">Empenho</span>
              <span id="spnCodCntt" class="infoBoxNumber"></span>
            </div>
          </div>
        </div>        

        <section id="collapseCorreio" class="section-combo" data-tamanho="230">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Rastreamento/Entrega</a>
              <button type="button" id="popoverCorreio" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group'>
                                        <label for='edtCodRastreio' class='control-label'>Codigo rastreio</label>
                                        <input type='text' class='form-control' id='edtCodRastreio' placeholder='informe' />
                                      </div>
                                      <div class='form-group'>
                                        <label for='edtDtEntrega' class='control-label'>Previsão entrega</label>
                                        <input type='text' class='form-control' id='edtDtEntrega' placeholder='##/##/####' onkeyup=mascaraNumero('##/##/####',this,event,'dig') />
                                      </div>                                        
                                      <div class='form-group'>
                                        <button onClick='fncCorreio();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>
              </button>              
            </span>
          </div>
        </section>  

        <section id="collapseAtiva" class="section-combo" data-tamanho="180">
          <div class="panel panel-default" style="padding: 5px 12px 5px;">
            <span class="btn-group">
              <a class="btn btn-default disabled">Data ativação</a>
              <button type="button" id="popoverAtiva" 
                                    class="btn btn-primary" 
                                    data-dismissible="true" 
                                    data-title="Informe"                               
                                    data-toggle="popover" 
                                    data-placement="bottom" 
                                    data-content=
                                      "<div class='form-group'>
                                        <label for='edtDtAtiva' class='control-label'>Ativado em</label>
                                        <input type='text' class='form-control' disabled id='edtDtAtiva' placeholder='##/##/####' onkeyup=mascaraNumero('##/##/####',this,event,'dig') />
                                      </div>                                        
                                      <div class='form-group'>
                                        <button onClick='fncAtiva();' class='btn btn-default'>Confirmar</button>
                                      </div>"><span class="caret"></span>        
              </button>
            </span>
          </div>
        </section>  
      </div>
      <div id="espiaoModal" class="divShowModal" style="display:none;"></div>
      
      
      
      
      
      
      <section>
        <section id="sctnCntI" style="margin-left:100px;">
        </section>  
      </section>
      
      <form method="post" class="center" id="frmCntI" action="classPhp/imprimirSql.php" target="_newpage" style="margin-top:1em;" >
        <input type="hidden" id="sql" name="sql"/>
        <div class="inactive">
          <input id="ageApeCol" value="*" type="text" />        <!-- Apelido do colaborador -->
          <input id="edtModoEntrega" value="*" type="text" />   <!-- Codigo do modo de entrega -->        
          <input id="edtStatusEntrega" value="*" type="text" /> <!-- Codigo do status da entrega -->                
        </div>
      </form>
    </div>
    
    <div id="agendacad" class="frmTable" style="display:none; width:90em; margin-left:11em;margin-top:5.5em;position:absolute;">
      <div class="frmTituloManutencao">Agendamento<img class="frmTituloManutencaoImg" src="imagens\chave.png" title="campo obrigatório" /></div>
      <div style="height: 200px; overflow-y: auto;">
        <div class="campotexto campo100">
          <!-- Endereco de entrega -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodEnt"
                                                onBlur="codEntBlur(this);" 
                                                onFocus="entFocus(this);" 
                                                onClick="entF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodEnt">ENTREGA:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesEnt" type="text" disabled /><label class="campo_label campo_required" for="edtDesEnt">ENDERECO ENTREGA</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtCddEnt" type="text" disabled /><label class="campo_label campo_required" for="edtCddEnt">CIDADE</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCepEnt" type="text" disabled /><label class="campo_label campo_required" for="edtCepEnt">CEP</label>
          </div>
          <!-- Endereco de instalacao -->
          <div class="campotexto campo10">
            <input class="campo_input inputF10" id="edtCodIns"
                                                onBlur="codInsBlur(this);" 
                                                onFocus="insFocus(this);" 
                                                onClick="insF10Click(this);"
                                                data-oldvalue=""
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="edtCodIns">INSTALA:</label>
          </div>
          <div class="campotexto campo50">
            <input class="campo_input_titulo input" id="edtDesIns" type="text" disabled /><label class="campo_label campo_required" for="edtDesIns">ENDERECO INSTALACAO</label>
          </div>
          <div class="campotexto campo30">
            <input class="campo_input_titulo input" id="edtCddIns" type="text" disabled /><label class="campo_label campo_required" for="edtCddIns">CIDADE</label>
          </div>
          <div class="campotexto campo10">
            <input class="campo_input_titulo input" id="edtCepIns" type="text" disabled /><label class="campo_label campo_required" for="edtCepIns">CEP</label>
          </div>
          <div class="campotexto campo15">
            <input class="campo_input input" id="edtData" 
                                             placeholder="##/##/####"                 
                                             onkeyup="mascaraNumero('##/##/####',this,event,'dig')"
                                             value="00/00/0000"
                                             type="text" 
                                             maxlength="10" />
            <label class="campo_label campo_required" for="edtData">DATA VISITA:</label>
          </div>
          <!--
                 -->
          <!-- Colaborador -->
          <div class="campotexto campo15">
            <input class="campo_input inputF10" id="ageCodCol"
                                                onClick="colF10Click(this);"
                                                onBlur="codColBlur(this);"                                                 
                                                data-oldvalue="0000"
                                                autocomplete="off"
                                                type="text" />
            <label class="campo_label campo_required" for="ageCodCol">COLABORADOR:</label>
          </div>
          <div class="campotexto campo70">
            <input class="campo_input_titulo input" id="ageDesCol" type="text" disabled /><label class="campo_label campo_required" for="ageDesCol">NOME COLABORADOR</label>
          </div>
          <!--
          -->
          <div onClick="ageConfirmarClick();" id="ageConfirmar" class="btnImagemEsq bie15 bieAzul bieRight"><i class="fa fa-check"> Confirmar</i></div>
          <div onClick="document.getElementById('agendacad').style.display='none';" id="ageCancelar" class="btnImagemEsq bie15 bieRed bieRight"><i class="fa fa-reply"> Cancelar</i></div>
          <div class="campotexto campo70">
            <label class="labelMensagem" for="edtUsuario">Para agendamento ser completo todos os campos devem ser informados</label>
          </div>
        </div>  
      </div>
    </div>
    <script>
      //
      //
      ///////////////////
      // Correio
      ///////////////////
      ppvCorreio = new Popover('#popoverCorreio',{ trigger: 'click'} );      
      evtCorreio = document.getElementById('popoverCorreio');
      evtCorreio.status="ok";
      //////////////////////////////////////////////
      // show.bs.popover(quando o metodo eh chamado)
      //////////////////////////////////////////////
      evtCorreio.addEventListener('show.bs.popover', function(event){
        try{
          chkds=objCntI.gerarJson("n").gerar();
          let qtdCor=0;
          let qtdTra=0;
          chkds.forEach(function(reg){
            if( reg.ME=="COR" ) qtdCor++;
            if( reg.ME=="TRA" ) qtdTra++;
          });
          if( (qtdCor>0) && (qtdTra>0) )
            throw "FAVOR SELECIONAR CORREIO OU TRANSPORTADORA!";                
          evtCorreio.status="ok";            
          
          if( qtdCor>0 )
            evtCorreio.status="cor";  
        }catch(e){
          evtCorreio.status="err";
          gerarMensagemErro("catch",e,"Erro");
        };
      },false);  
      //////////////////////////////////////////////////
      // shown.bs.popover(quando o metodo eh completado)
      //////////////////////////////////////////////////
      evtCorreio.addEventListener('shown.bs.popover', function(event){
        if( evtCorreio.status=="err" ){
          ppvCorreio.hide();
        } else {    
          if( evtCorreio.status=="cor" ){
            $doc("edtCodRastreio").foco();
          } else {
            $doc("edtCodRastreio").value="NSA";
            jsCmpAtivo("edtCodRastreio").add("campo_input_titulo").disabled(true);
            $doc("edtDtEntrega").foco();
          }
        };
      }, false);
      //
      //
      ///////////////////
      // Data de ativacao
      ///////////////////
      ppvAtiva = new Popover('#popoverAtiva',{ trigger: 'click'} );      
      evtAtiva = document.getElementById('popoverAtiva');
      evtAtiva.status="ok";
      
      evtAtiva.addEventListener('show.bs.popover', function(event){
        try{
          chkds=objCntI.gerarJson("1").gerar();
          chkds.forEach(function(reg){
            if( reg.PLACA_CHASSI=="" )
              throw "AUTO SEM PLACA!";  
            if( reg.ATIVADO !="" )
              throw "AUTO COM DATA DE ATIVAÇÃO!";  
          });
          evtAtiva.status="ok";            
        }catch(e){
          evtAtiva.status="err";
          gerarMensagemErro("catch",e,"Erro");
        };
        //$doc("edtDtAtiva").value=jsDatas(0).retDDMMYYYY();
      },false);  
      evtAtiva.addEventListener('shown.bs.popover', function(event){
        if( evtAtiva.status=="err" ){
          ppvAtiva.hide();
        } else {    
          $doc("edtDtAtiva").value=jsDatas(0).retDDMMYYYY();
        };
      },false);
    </script>
  </body>
</html>