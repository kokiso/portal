<!DOCTYPE html>
<head>
<!-- Latest compiled and minified CSS -->
<link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="css/font-awesome.css" rel="stylesheet">
<link href="css/datatable/datatables.min.css" rel="stylesheet">

<script src="js/jsTable2017.js"></script>
<script src="tabelaTrac/f10/tabelaEquipamentoF10.js"></script>
<script src="js/Jquery/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<!-- <script src="js/datatable/datatables.min.js"></script>
<script src="js/datatable/pdfmake.min.js"></script>
<script src="js/datatable/vfs_fonts.js"></script>
<script src="js/datatable/jquery.dataTables.min.js"></script> -->


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/mgalante/jquery.redirect/master/jquery.redirect.js"></script>




</head>
<style type="text/css">
	td.details-control {
	cursor: pointer;
	color: green;
}
tr.shown td.details-control {
    width: auto;
    color: red;
}

</style>
<header>
 	<nav class="navbar navbar-dark bg-dark">
  		<img src="imagens/logoMaiorNew.png">
  		<a class="btn btn-outline-primary" style="color:white"href="Trac_Principal.php">Voltar</a>
    </nav>
</header>
<body>
	<div class="container">
      	<div class="row">
			<div class="col-sm-12">
				<table id="myTable" class="table table-striped table-bordered" style="width:100%">
			        <thead>
			            <tr>       					
			            	<th>Id Carga</th>
			                <th>Tipo</th>
			                <th>Favorecido Saida</th>
			                <th>Endereço Saída</th>
			                <th>Data Saída</th>
			                <th>Favorecido Chegada</th>
			                <th>Endereço Chegada</th>
			                <th>Data Chegada</th>
			                <th>Status</th>
			                <th>Progresso</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr>
			            	<td>1</td>
			                <td>ENVIO</td>
			                <td>TOTAL TRAC LOC E MONIT RAT LTDA ME</td>
			                <td>RUA ITANHAEM 2389</td>
			                <td>01/01/1901</td>
			                <td>ANGELO KOKISO</td>
			                <td>RUA SÃO SEBASTIÃO 1096</td>
			                <td>01/01/1901</td>
			                <td>EM TRANSITO</td>
			                <td style="width: 30%"><div class="progress">
									<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
								</div>
							</td>
			            </tr>
			            <tr>
			            	<td>2</td>
			                <td>ENVIO</td>
			                <td>TOTAL TRAC LOC E MONIT RAT LTDA ME</td>
			                <td>RUA ITANHAEM 2389</td>
			                <td>01/01/1901</td>
			                <td>ANGELO KOKISO</td>
			                <td>RUA SÃO SEBASTIÃO 1096</td>
			                <td>01/01/1901</td>
			                <td>ENVIADO</td>
			                <td style="width: 30%"><div class="progress">
									<div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
								</div>
							</td>
			            </tr>
			            <tr>
			            	<td>3</td>
			                <td>RETORNO</td>
			                <td>TOTAL TRAC LOC E MONIT RAT LTDA ME</td>
			                <td>RUA ITANHAEM 2389</td>
			                <td>01/01/1901</td>
			                <td>ANGELO KOKISO</td>
			                <td>RUA SÃO SEBASTIÃO 1096</td>
			                <td>01/01/1901</td>
			                <td>ENTREGUE</td>
			                <td style="width: 30%"><div class="progress">
									<div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
								</div>
							</td>
			            </tr>
			            <tr>
			            	<td>4</td>
			                <td>ENVIO</td>
			                <td>TOTAL TRAC LOC E MONIT RAT LTDA ME</td>
			                <td>RUA ITANHAEM 2389</td>
			                <td>01/01/1901</td>
			                <td>ANGELO KOKISO</td>
			                <td>RUA SÃO SEBASTIÃO 1096</td>
			                <td>01/01/1901</td>
			                <td>ENTREGUE EM MÃOS</td>
			                <td style="width: 30%"><div class="progress">
									<div class="progress-bar progress-bar-striped bg-success" role="progressbar " aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
								</div>
							</td>
			            </tr>
			            <tr>
			            	<td>5</td>
			                <td>RETORNO</td>
			                <td>TOTAL TRAC LOC E MONIT RAT LTDA ME</td>
			                <td>RUA ITANHAEM 2389</td>
			                <td>01/01/1901</td>
			                <td>ANGELO KOKISO</td>
			                <td>RUA SÃO SEBASTIÃO 1096</td>
			                <td>01/01/1901</td>
			                <td>AGUARDANDO COLETA</td>
			                <td style="width: 30%"><div class="progress">
									<div class="progress-bar progress-bar-striped bg-danger" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%"></div>
								</div>
							</td>
			            </tr>
			        </tbody>
			        <tfoot>
			            <tr>
         					<th>Id Carga</th>
			                <th>Tipo</th>
			                <th>Favorecido Saida</th>
			                <th>Endereço Saída</th>
			                <th>Data Saída</th>
			                <th>Favorecido Chegada</th>
			                <th>Endereço Chegada</th>
			                <th>Data Chegada</th>
			                <th>Status</th>
			                <th>Progresso</th>
			            </tr>
			        </tfoot>
			    </table>
			</div>
      	</div>
<!--       	<div class="btn-group" role="group" aria-label="Basic example"> -->
		  <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-xl">Cadastrar</button>
		  <button type="button" class="btn btn-warning">Editar</button>
		  <button type="button" class="btn btn-danger">Deletar</button>
		<!-- </div> -->
    </div>
</body>

<!-- Modal de Cadastro / Edição -->
<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      	<div class="modal-header">
            <h2 class="modal-title" id="exampleModalLongTitle" style="text-align: center;">Cadastrar</h2>
		</div>
      	<div class="modal-body">
            <form>
          		<div class="form-row">
              		<div id="addForm">
		                <div class="col-md-12">
		                  <label for="tipo">Tipo</label> 
		                  <div class="tipo">
		                    <label class="radio-inline">
		                    	<input type="radio" value="1" name="optradio">Envio
		                    </label>
							<label class="radio-inline">
								<input type="radio" value="2" name="optradio">Retorno
							</label>
		                  </div>
		                </div>
              		</div>
<!--               <div class="form-group col-md-12">
                <input type="text" class="form-control" name="input_field[]" value=""/>
						<a href="javascript:void(0);" class="add_input_button" title="Add field"><span><i class="fa fa-plus-circle"></i></span></a>	 
              </div> -->
              	</div>
              	<div class="form-row">
	              	<div class="col-md-6">
		                <label for="edtTipoOld">Equipamento/Auto</label>
		                <input type="text" class="form-control" name="input_field[]" value=""/>
								<a href="javascript:void(0);" class="add_input_button" title="Add field"><span><i class="fa fa-plus-circle"></i></span></a>	
	              	</div>
	                <div class="col-md-6">
	                  <label for="edtEqpNew">Serial</label>
	                    <input type="text" class="form-control" id="edtTipoNew" required disabled="true">
	                </div>
                </div>

           		<div class="form-row">
	 				<div class="form-group col-md-4">
	                  <label for="edtEqpOld">Favorecido de Saída</label> 
	                  <div class="input-group">
	                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
	                    <input type="search" readonly class="form-control" id="edtEqpOld" data-id =""  style="cursor: pointer;" required onClick="equipCadClick('old');">
	                  </div>
	                </div>
                    <div class="form-group col-md-4">
	                  <label for="edtEqpNew">Favorecido de Entrega</label>
	                  <div class="input-group">
	                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
	                    <input type="search" readonly class="form-control" id="edtEqpNew" data-id ="" style="cursor: pointer;" required
	                    onClick="equipCadClick('new');">
	                  </div>
	                </div>
	                <div class="form-group col-md-4">
	                	<label for="statusEntrega">Status da Entrega</label>
	                	<select id="statusEntrega" class="form-control">
	                		<option value="1"> Pedido Criado</option>
	                		<option value="2"> Aguardando Coleta</option>
	                		<option value="3"> Enviado</option>
	                		<option value="4"> Em Trânsito</option>
	                		<option value="5"> Entregue</option>
	                		<option value="6"> Entregue en mãos</option>
	                	</select>
	                </div>
           		</div>

	          	<div class="form-group col-md-4 col-md-offset-8">
	            	<button type="submit" class="btn btn-success" id="package-create">Cadastrar</button>
		            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
	          	</div>
            </form>
        </div>
    </div>
  </div>
</div>
<script type="text/javascript">
var objAmi;                     // Obrigatório para instanciar o JS Semestral
objAmi=new clsTable2017("objAmi");
var objEquipF10;                // Obrigatório para instanciar o JS 
$(document).ready(function() {

  	//objAmi=new clsTable2017("objAmi");
	var max_fields = 1000;
	var field_wrapper = $('#addForm');

	var new_field_html = '<div class="form-row"><div class="col-md-6"><label for="edtTipoOld">Equipamento/Auto</label><input type="text" class="form-control" name="input_field[]" value=""/><a href="javascript:void(0);" class="remove_input_button" title="Remove field"><span><i class="fa fa-minus-circle"></i></span></a></div><div class="col-md-6"><label for="edtEqpNew">Serial</label><input type="text" class="form-control" id="edtTipoNew" required disabled="true"></div></div>'
	var input_count = 1;
	var table = $('#myTable').DataTable({
		dom: 'Bfrtip',
		"language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json"}
        ,buttons: [
            {
                extend: 'collection',
                text: 'Exportar',
                className:'btn-flat btn-primary dropdown-toggle',
                init: function( api, node, config) {
			       $(node).removeClass('dt-button buttons-collection')
			    },
                buttons: [
                    {extend:'copy' ,title: 'Cargas',filename: 'cargas'},
                    {extend:'excel',title: 'Cargas',filename: 'cargas'},
                    {extend:'csv'  ,title: 'Cargas',filename: 'cargas'},
                    {extend:'pdf'  ,title: 'Cargas',filename: 'cargas'}
                ]
        	},
    	],

	    order: [[1, 'asc']]
		    
	});
	// Add button dynamically
	$('.add_input_button').click(function(){
		if(input_count < max_fields){
			input_count++;
			$(field_wrapper).append(new_field_html);
		}
	});

	// Remove dynamically added button
	$(field_wrapper).on('click', '.remove_input_button', function(e){	
		e.preventDefault();
		$(this).parent('div').parent('div').remove();
		input_count--;
	});
});

function equipCadClick(flag){ 
chkds=objAmi.gerarJson("n").gerar();         
	if(flag == 'old'){
	  fEquipamentoF10(0,"nsa","null",100
	  ,{ codaut:parseInt(chkds[0].TRAC)
	    ,codgp:"A.GMP_CODGP <> 'AUT'"
	    ,divWidth:"76em"
	    ,tblWidth:"74em"
	  });
	}
	else{		
	  fEquipamentoF10(0,"nsa","null",100
	  ,{codpe:"EST"
	    ,codgp:"A.GMP_CODGP <> 'AUT'"
	    ,codaut:0
	    ,tbl:'tblEquip2'
	    ,tipo:$doc("edtTipoOld").value 
	    ,divWidth:"76em"
	    ,tblWidth:"74em"
	  });
	}
};
            
function RetF10tblEquip(arr){
	$doc("edtEqpOld").value      = arr[0].NOME;
	$doc("edtNomeOld").value      = arr[0].SERIE;        
	$doc("edtTipoOld").value      = arr[0].TIPO;                
	$doc("edtEqpOld").setAttribute("data-id",arr[0].CODIGO);

};
function RetF10tblEquip2(arr){
	$doc("edtEqpNew").value      = arr[0].NOME;
	$doc("edtNomeNew").value      = arr[0].SERIE; 
	$doc("edtTipoNew").value      = arr[0].TIPO;                           
	$doc("edtEqpNew").setAttribute("data-id",arr[0].CODIGO);

};
</script>

<!-- <script type="text/javascript">
	function format (rowData) {
	    // `d` is the original data object for the row
	    var div = $('<div/>')
	        .addClass( 'loading' )
	        .text( 'Carregando...' );
	 
	    $.ajax( {
	        url: 'Trac_EstoqueVirtualJson.php',
	        type: 'POST',
	        data: {
	            "fvr": rowData.fvr_codigo
	        },
	        dataType: 'json',
	        success: function ( json ) {
	        	table = CreateTableFromJSON(json);
	            div
	                .html( table )
	                .removeClass( 'loading' );
	            $('#childTable').DataTable({
	            	data:json,
	            	pageLength: 5,
	                lengthChange: false,
	            	dom: 'Bfrtip',
		    		"language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json"}
			        ,buttons: [
			            {
			                extend: 'collection',
			                text: 'Exportar Produtos',
			                className: "btn-sm btn-success",
			                init: function( api, node, config) {
						       $(node).removeClass('dt-button buttons-collection')
						    },
			                buttons: [
			                    {extend:'copy',title: rowData.fvr_nome
			                    ,filename: 'estoque_invidual_'.concat(rowData.fvr_nome)},
			                    {extend:'excel',title: rowData.fvr_nome
			                    ,filename: 'estoque_invidual_'.concat(rowData.fvr_nome)},
			                    {extend:'csv',title: rowData.fvr_nome
			                    ,filename: 'estoque_invidual_'.concat(rowData.fvr_nome)},
			                    {extend:'pdf',title: rowData.fvr_nome
			                    ,filename: 'estoque_invidual_'.concat(rowData.fvr_nome)}
			                ]
		            	}

		        	]
    	    		,columns: [
				        { data: "id" ,name:"id" },
				        { data: "modelo_auto" ,name:"modelo_auto" },
				        { data: "produto_id" ,name:"produto_id" },
				        { data: "nome_produto",name:"nome_produto" },
				        { data: "configuracao",name:"configuracao" },
				        { data: "status" ,name:"status"},
				        { data: "data_agendamento",name:"data_agendamento" }
				    ],
	            });
	        }
	    } );
	 
	    return div;
	}
	function CreateTableFromJSON(json) { 
        
        var col = [];
        for (var i = 0; i < json.length; i++) {
            for (var key in json[i]) {
                if (col.indexOf(key) === -1) {
                    col.push(key);
                }
            }
        }

        // CREATE DYNAMIC TABLE.
        var table  = document.createElement("table");
        table.setAttribute("id", "childTable");

        var thead = document.createElement("thead");
        table.appendChild(thead);

        var tr = document.createElement("tr");
        thead.appendChild(tr);

        var tbody = document.createElement("tbody");
        table.appendChild(tbody);

        var tr2 = document.createElement("tr");
        tbody.appendChild(tr2);

        for (var i = 0; i < col.length; i++) {
            var th = document.createElement("th"); 
            th.innerHTML = col[i];
            tr.appendChild(th);
        }

        return table;
    }
	function exportCsv() {
		$.redirect('Trac_EstoqueVirtualJson.php', {'exp': 1});
		// $.ajax( {
	 //        url: 'Trac_EstoqueVirtualJson.php',
	 //        type: 'POST',
	 //        data: {
	 //            "exp": 1
	 //        },
	 //        dataType: 'json',
	 //        success: function ( json ) {
	 //        	alert('success');
	 //        }
	 //    } );
	}

	$(document).ready(function() {
    	var table = $('#myTable').DataTable({
    		dom: 'Bfrtip',
    		"language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json"}
    		,"ajax": 'Trac_EstoqueVirtualJson.php'
	        ,buttons: [
	            {
	                extend: 'collection',
	                text: 'Exportar',
	                className:'btn-flat btn-primary dropdown-toggle',
	                init: function( api, node, config) {
				       $(node).removeClass('dt-button buttons-collection')
				    },
	                buttons: [
	                    {extend:'copy' ,title: 'Estoque Virtual',filename: 'estoque_virtual',},
	                    {extend:'excel',title: 'Estoque Virtual',filename: 'estoque_virtual'},
	                    {extend:'csv'  ,title: 'Estoque Virtual',filename: 'estoque_virtual'},
	                    {extend:'pdf'  ,title: 'Estoque Virtual',filename: 'estoque_virtual'}
	                ]
            	},
            	{
            		text: 'Exportar Produtos em Natura',
		            className: "btn-flat btn-warning",
		            attr:  {
	                	title: 'export',
	                	id: 'exportBtn',
	                	onClick:'exportCsv()'
            		},
					 init: function( api, node, config) {
				       $(node).removeClass('dt-button buttons-collection')
				    }
            	}
        	]
    		,columns: [
		        {
		            className:      'details-control',
		            orderable:      false,
		            name:           null,
		            defaultContent: '<span><i class="fa fa-plus-circle"></i></span>'
		        },
		        { data: "fvr_codigo" ,name:"fvr_codigo" },
		        { data: "fvr_nome" ,name:"fvr_nome" },
		        { data: "cdd_nome" ,name:"cdd_nome" },
		        { data: "pe_nome" ,name:"pe_nome" },
		        { data: "cntt_qtdauto",name:"cntt_qtdauto" },
		        { data: "qtd_estoque" ,name:"qtd_estoque"},
		        { data: "qtd_uso",name:"qtd_uso" },
		        { data: "qtd_total" ,name:"qtd_total"}
		    ],
		    order: [[1, 'asc']]
		    
    	});
	 	$('#myTable tbody').on('click', 'td.details-control', function () {
        	var tr = $(this).closest('tr');
	        var row = table.row( tr );
	        $(this).find('i').toggleClass('fa fa-minus-circle')
	 
	        if ( row.child.isShown() ) {
	            // This row is already open - close it
	            row.child.hide();
	            tr.removeClass('shown');
	            $(this).find('i').toggleClass('fa fa-plus-circle')
	        }
	        else {
	            // Open this row
	            row.child( format(row.data()) ).show();
	            tr.addClass('shown');
	            $(this).find('i').toggleClass('fa fa-plus-circle')
	        }
    	} );
	} );
</script> -->
</html>
