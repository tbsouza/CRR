<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title> CRR </title>

		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">

		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
		<script src="jquery-3.0.0.js" ></script>
		<link type="text/css" rel="stylesheet" href="arcgis_main.css">
		<script src="arcgisjs.js"></script>
		<script src="//npmcdn.com/angular-esri-map@2"></script>
		<script src="//ajax.googleapis.com/ajax/libs/dojo/1.10.4/dojo/dojo.js" data-dojo-config="async: true"></script>
	</head>
	<body id="body">
		<script>

			var h = document.createElement("h2");
			h.appendChild( document.createTextNode("População Urbana do Brasil") );
			document.body.appendChild(h);

			var div = document.createElement("div");
			div.setAttribute("id", "viewDiv");

			require([
				  "esri/Map",
				  "esri/views/MapView",   // SceneView
				  "esri/layers/Layer",
				  "dojo/domReady!"
				], function(Map, MapView, Layer) {
				  var map = new Map({
				    basemap: "gray"       // streets, topo, satellite, gray
				  });

				  // insere o mapa
				  var view = new MapView({
				    container: "viewDiv", // Reference to the DOM node that will contain the view
				    map: map,             // References the map object created in step 3
				    zoom: 5,              // Sets the zoom level based on level of detail (LOD)
				    center: [-55, -15]    // Sets the center point of view in lon/lat
				  });

				  // insere a camada
				  Layer.fromPortalItem({
		        	portalItem: {         // autocast as esri/portal/PortalItem
		          		id: "c06fe05f26094bb695948a8ff0ab77c7"
		       		}
		          }).then(addLayer).otherwise(rejection);

		          // Adds the layer to the map once it loads
			      function addLayer(lyr) {
			        map.add(lyr);
			        //alert("Layer added");
			      }

			      function rejection(err) {
			        alert("Layer failed to load: " + err);
			      }
			});

			document.body.appendChild(div);

			$.ajax({
	      		url:'pop_urb_2010_bd.php',
	      		complete: function (response) {
	         		fncMunicipios(response.responseText);
	      		},
	      		error: function () {
	          		alert('Error');
	     		}
	  		});

			function fncMunicipios(response){

				var res = response.split("#");
			    var sz = res.length;
			    var i=0;
			    var columns = 4;
			    var qtd = (sz-1)/columns;

			    // cria elementos html
			    var table = document.createElement("table");
			    var tbody = document.createElement("tbody");
			    var center = document.createElement("center");
			    var div = document.createElement("div");
			    var br = document.createElement("p");

			    // atribui id aos elementos criados
			    center.setAttribute('id', 'center');
			    table.setAttribute('id','table');
			    div.setAttribute('id','div');

			    // total de resultados
			    div.appendChild(br);
			    var p = document.createElement("p");
			    p.setAttribute('id','para');
			    p.appendChild( document.createTextNode("Total de resultados: " + qtd) );
			    div.appendChild(p);
			    div.appendChild(br);

			    // Indices da tabela
			    var tr = document.createElement("tr");
			    tr.setAttribute('id', 'tr');

			    var th = document.createElement("th");
			    th.appendChild(document.createTextNode( "Código IBGE" ));
			    tr.appendChild(th);
			    var th = document.createElement("th");
			    th.appendChild(document.createTextNode( "Município" ));
			    tr.appendChild(th);
			    var th = document.createElement("th");
			    th.appendChild(document.createTextNode( "Estado" ));
			    tr.appendChild(th);
			    var th = document.createElement("th");
			    th.appendChild(document.createTextNode( "População Urbana*" ));
			    tr.appendChild(th);

			    tbody.appendChild(tr);

			    // monta a tabela html
			    while( i<(sz-2) ){

			    	var tr = document.createElement("tr");
			    	
			    	var td = document.createElement("td");
			    	td.appendChild(document.createTextNode( res[i] ));
			    	tr.appendChild(td);
			    	i++;

			    	var td = document.createElement("td");
			    	td.appendChild(document.createTextNode( res[i] ));
			    	tr.appendChild(td);
			    	i++;

					var td = document.createElement("td");
			    	td.appendChild(document.createTextNode( res[i] ));
			    	tr.appendChild(td);
			    	i++;

			    	var td = document.createElement("td");
			    	td.appendChild(document.createTextNode( res[i] ));
			    	tr.appendChild(td);
			    	i++;

			    	tbody.appendChild(tr);
			    }

			    table.appendChild(tbody);
			    center.appendChild(table);
			    div.appendChild(center);
			    div.appendChild(br);

			    document.body.appendChild(div);

			    // adiciona um rodapé na página (se necessario)
			    var p = document.createElement("p");
			    p.appendChild( document.createTextNode("* Dados de 2010") );
			    document.body.appendChild(p);
			}

		</script>

	</body>
</html>