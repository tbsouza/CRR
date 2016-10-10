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

		<h2> População do Brasil - 2014 </h2>

		<script> // Script para adicionar mapa
			
			// div para inserir mapa
			var div = document.createElement("div");
			div.setAttribute("id", "viewDiv");

			// faz a requisição do mapa
			require([
				  "esri/Map",
				  "esri/views/MapView",   // SceneView
				  "esri/layers/Layer",
				  "esri/widgets/Legend",
				  "esri/views/ui/DefaultUI",
				  "esri/widgets/Search",
				  "dojo/domReady!"
				], function(Map, MapView, Layer, Legend, DefaultUI, Search) {
					var map = new Map({ // tipo do mapa
					    basemap: "gray"       // streets, topo, satellite, gray, osm, terrain
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
			          		//id: "1cc5a478cc794acb9caf35b6f6840c4a"
			       		}
			        }).then(addLayer).otherwise(rejection);

			        // Adds the layer to the map once it loads
				    function addLayer(lyr) {
				        map.add(lyr);
				        //alert("Layer added");
				    }

				    function rejection(err) {
				        //alert("Layer failed to load: " + err);
				    }

				    // adiciona botao de pesquisa no mapa
				    var searchWidget = new Search({ view: view });
				    view.ui.add(searchWidget, "top-right");
				    searchWidget.startup();

				    var legend = new Legend({
	  					view: view,
	  					layerInfos: [{
	    					layer: (map.layers.getItemAt(0)), // TODO
	    					title: "PIB 2011"
	  						}]
						});
				    view.ui.add(legend, "bottom-right");
				    legend.startup();




				    dojo.connect(map,'onLayersAddResult',function(results){
			          var layerInfo = dojo.map(results, function(layer,index){
			            return {layer:layer.layer,title:layer.layer.name};
			          });
			          // hide layer with an index of 1 in the tiled basemap service
			          // this is the "TownBoundary-9k" layer
			          layerInfo[0].hideLayers = [1];
			          if(layerInfo.length > 0){
			            var legendDijit = new esri.dijit.Legend({
			              map:map,
			              layerInfos:layerInfo
			            },"legendDiv");
			            legendDijit.startup();
			          }
			        });


			});

			// adiciona a div com o mapa ao corpo do html
			document.body.appendChild(div);
		</script>

		<div class="divSearch" > <!-- Campo pesquisar -->

			<form id="inputData" action="municipios_2015.php">
   				<input class="button_disable" type="submit" value="2014" disabled="true" />
   				<input class="button" type="submit" value="2015" />
			</form>

			<br><br>

			<input id="inputSearch" maxlength="54" type="text" name="text" size="42" placeholder="Por exempo: São Paulo, MG, 2109056">
			<button class="button" id="buttonSearch" type="button" onclick="btnSearch()">Pesquisar</button>
			<button class="button" id="buttonAll" type="button" onclick="connect()">Todos os resultados</button>

		</div>

		<script type="text/javascript">

			// variaveis globais
			var flag = 1;     // verificar se o usuário está realizando a mesma busca
			var content = ""; // conteúdo digitado pelo usuário para pesquisa

			connect();


			// chama função php para conectar com o banco
			function connect(){

				if( flag != 0 ){

					flag = 0;

					$.ajax({
			      		url:'municipios_2014_bd.php',
			      		complete: function (response) {
			         		fncMunicipios(response.responseText);
			      		},
			      		error: function () {
			          		alert('Connect Error');
			     		}
			  		});
			  	}
			}


			function fncMunicipios(response){

				// verifica se tabela ja exista e limpa
				var d = document.getElementById("div");
				var cont = document.body.contains(d);

				if( cont == false ){
					var div = document.createElement("div");
					div.setAttribute('id','div');
				}else{
					var div = document.getElementById("div");
					div.innerHTML = "" ;
				}
				
				var res = response.split("#");
			    var sz = res.length;
			    var i=0;
			    var columns = 4;
			    var qtd = (sz-1)/columns;

			    if( qtd>0 ){
				    // cria elementos html
				    var table = document.createElement("table");
				    var tbody = document.createElement("tbody");
				    var center = document.createElement("center");
				    var br = document.createElement("p");

				    // atribui id aos elementos criados
				    center.setAttribute('id', 'center');
				    table.setAttribute('id','table');
				    
				    // Tópicos da tabela
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
				    th.appendChild(document.createTextNode( "População" ));
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
				    div.appendChild(table);
				    center.appendChild(div);
				    center.appendChild(br);

				    document.body.appendChild(center);

				    // total de resultados
				    div.appendChild(br);
				    var p = document.createElement("p");
				    //p.setAttribute('id','para');
				    p.appendChild( document.createTextNode("Total de resultados: " + qtd) );
				    div.appendChild(p);

				    // Data dos dados
				    var p = document.createElement("p");
				    p.appendChild( document.createTextNode("Dados de 2014") );
				    div.appendChild(p);
				}else{
					var div = document.getElementById("div");
					var p = document.createElement("p");
					p.appendChild( document.createTextNode("Nenhum resultado encontrado") );
					div.appendChild(p);
				}
			}

		</script>


		<script type="text/javascript"> // Script para pesquisar com enter
			$(document).ready(function(){
				$('#inputSearch').keypress(function(e){
					if(e.keyCode==13)
					    $('#buttonSearch').click();
				});
			});
		</script>


		<script type="text/javascript">


			function btnSearch(){ // funcao pesquisar

				var inputSearch = document.getElementById("inputSearch");

				if( flag!=1 || content!=inputSearch.value ){

					flag = 1;

					if( inputSearch.value != "" ){
						// atualiza o placeholder
						var msg = "Por exempo: São Paulo, MG, 2109056";
						inputSearch.placeholder = msg;


						// elimina espaços antes e depois, se houver
						var value = (inputSearch.value).trim();

						// chama função mysql para conectar no banco
						$.ajax({
				      		url:'municipios_2014_search.php',
				      		method: "post",
				      		data: {text: value},
				      		complete: function (response) {
				         		fncMunicipios(response.responseText);
				      		},
				      		error: function () {
				          		alert('Error');
				     		}
				  		});

		
					}else{
						// atualiza o placeholder se usuario ñao digitar nada
						var msg = "Digite algo para pesquisar";
						inputSearch.placeholder = msg;
					}
				}

				// atualiza content
				content = inputSearch.value;
			}
		</script>

	</body>
</html>