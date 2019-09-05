<!DOCTYPE html>
<head>
<!-- Latest compiled and minified CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
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
 	<nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse mb-4">
  		<img src="imagens/logoMaiorNew.png">
		<div class="collapse navbar-collapse" id="navbarCollapse">
        	<ul class="navbar-nav mr-auto">

        	</ul>
      		<a class="btn btn-outline-primary" style="color:white"href="Trac_Principal.php">Voltar</a>
      	</div>
    </nav>
</header>
<body>
	<div class="container">
      	<div class="row">
			<div class="col-sm-12">
				<table id="myTable" class="table table-striped table-bordered" style="width:100%">
			        <thead>
			            <tr>
			            	<th></th>
			            	<th>Id</th>
			                <th>Cliente</th>
			                <th>Cidade</th>
			                <th>Ponto de Estoque</th>
			                <th>Autos Contratados</th>
			                <th>Quantidades em Estoque</th>
			                <th>Quantidade em Uso</th>
			                <th>Total</th>
			            </tr>
			        </thead>
			        <tbody>
			        
			        </tbody>
			        <tfoot>
			            <tr>
			            	<th></th>
			            	<th>Id</th>
			                <th>Cliente</th>
			                <th>Cidade</th>
			                <th>Ponto de Estoque</th>
			                <th>Autos Contratados</th>
			                <th>Quantidades em Estoque</th>
			                <th>Quantidade em Uso</th>
			                <th>Total</th>
			            </tr>
			        </tfoot>
			    </table>
			</div>
      	</div>
    </div>
</body>

<script type="text/javascript">
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
	                    {extend:'copy',title: 'Estoque Virtual',filename: 'estoque_virtual',},
	                    {extend:'excel',title: 'Estoque Virtual',filename: 'estoque_virtual'},
	                    {extend:'csv',title: 'Estoque Virtual',filename: 'estoque_virtual'},
	                    {extend:'pdf',title: 'Estoque Virtual',filename: 'estoque_virtual'}
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
</script>
</html>
