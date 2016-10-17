<!doctype html>
<html lang="en">

  	<head>

  		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		
		<!-- Titulo da página -->
		<title> CRR </title>

  		<!-- http://openlayers.org/en/v3.18.2/build/ol.js -->
		<script src="leaflet.js" type="text/javascript"></script>
		<!-- http://openlayers.org/en/v3.18.2/css/ol.css -->
		<link rel="stylesheet" href="leaflet.css" type="text/css" >

		<!-- JQuery -->
		<script src="jquery-3.0.0.js" ></script>

		<style type="text/css">
			
		#map{
			border: 2px solid black;
			width: 1000px;
			height: 900px;
		}

		</style>

  	</head>

  	<body>

  	<!-- Campo que sera adicionado o mapa -->
  		<div id="map" class="map"></div>

  		<script type="text/javascript">
  		
  			// cria um novo mapa
  			var map = L.map('map').setView([-15, -55], 5);

  			L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
			    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors | CRR Inatel'
			}).addTo(map);

  			// Funcao para diferenciar o estilo de cada feicao
  			function getColor(p) {
			    return p > 20000000 ? '#023858' :
			           p > 600000   ? '#045a8d' :
			           p > 300000   ? '#0570b0' :
			           p > 90000    ? '#3690c0' :
			           p > 20000    ? '#74a9cf' :
			           p > 10000    ? '#a6bddb' :
			                          '#d0d1e6';
			}

			// Funcao para aplicar o estilo
			function style(feature) {
			    return {
			        fillColor: getColor(feature.properties.pop_2015),
			        weight: 1,
			        opacity: 0.9,
			        color: 'grey',
			        dashArray: '3',
			        fillOpacity: 0.9
			    };
			}

			function highlightFeature(e) {
			    var layer = e.target;

			    layer.setStyle({
			        weight: 5,
			        color: '#666',
			        dashArray: '',
			        fillOpacity: 0.9
			    });

			    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
			        layer.bringToFront();
			    }
			}

			function resetHighlight(e) {
			    geojson.resetStyle(e.target);
			}

			function zoomToFeature(e) {
			    map.fitBounds(e.target.getBounds());
			}

			
  			// Abre o GeoJson com os dados
  			$.getJSON("pop_2015_json.geojson", function(json) {

				L.geoJson(json, {style: style}).addTo(map);

			    console.log(json); // this will show the info it in firebug console
			});









  		</script>




  	</body>

</html>