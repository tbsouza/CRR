<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title> CRR </title>

		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">

		<!-- CSS -->
		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>

		<!-- JQuery -->
		<script src="jquery-3.0.0.js" ></script>

		<!-- API GisCloud -->
		<script type="text/javascript" src="arcgis.js" ></script>
		<link rel="stylesheet" href="https://js.arcgis.com/4.1/esri/css/main.css">

	</head>

	<body id="body">

		<h2> População do Brasil - 2015</h2>

		<div id="viewDiv" ></div>

		<script> // Script para adicionar mapa

			// faz a requisição do mapa
			//"esri/widgets/Search",

			require([
				  "esri/Map",
				  "esri/views/MapView",
				  "esri/widgets/Search",
				  "esri/layers/FeatureLayer",
				  "dojo/domReady!"
				], function(Map, MapView, Search, FeatureLayer){
				  var map = new Map({
				    basemap: "gray"       // streets, topo, satellite, gray, osm, terrain
				  });
				  var view = new MapView({
				    container: "viewDiv",     // Reference to the scene div created in step 5
				    map: map,                 // Reference to the map object created before the scene
					zoom: 5,              // Sets the zoom level based on level of detail (LOD)
				    center: [-55, -15]  // Sets the center point of view with lon/lat
				  });

				  // points to a hosted Feature Layer in ArcGIS Online
				  var fl = new FeatureLayer({
				    portalItem: {  // autocasts as esri/portal/PortalItem
				      //id: "2f88bfd20e8c43019956c46e75084931"
				    }
				  });
				  map.add(fl);  // adds the layer to the map

				  // adiciona botao de pesquisa no mapa
				  var searchWidget = new Search({ view: view });
				  view.ui.add(searchWidget, "top-right");
				  searchWidget.startup();

			



			});


		</script>


		<br/><br/>


		<div class="divSearch" >

			<form id="inputData" action="municipios_2014.php">
   				<input class="button" type="submit" value="2014" />
   				<input class="button_disable"type="submit" value="2015" disabled="true" />
			</form>

			<br><br>

			<input id="inputSearch" maxlength="54" type="text" name="text" size="42" placeholder="Por exempo: São Paulo, MG, 2109056">
			<button class="button" id="buttonSearch" type="button" onclick="btnSearch()">Pesquisar</button>
			<button class="button" id="buttonAll" type="button" onclick="connect()">Todos os resultados</button>
	
		</div>


		<br/><br/>


		<script type="text/javascript">

			// variaveis globais
			var flag = 1;     // verificar se o usuário está realizando a mesma busca
			var content = ""; // conteúdo digitado pelo usuário para pesquisa

			connect();

			function connect(){

				if( flag != 0 ){

					flag = 0;

					$.ajax({
			      		url:'municipios_2015_bd.php',
			      		complete: function (response) {
			         		fncMunicipios(response.responseText);
			      		},
			      		error: function () {
			          		alert('Error');
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
				    p.appendChild( document.createTextNode("Dados de 2015") );
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

						$.ajax({
				      		url:'municipios_2015_search.php',
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