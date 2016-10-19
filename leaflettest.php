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
		<!-- http://openlayers.org/en/v3.18.2/build/ol.js -->
		<script src="requirejs.js" type="text/javascript"></script>

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

  			L.tileLayer('http://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}.png', {
			    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors | CRR '
			}).addTo(map);

  			//http://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png
  			//http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png
  			//http://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}
  			//http://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}.png
  			//http://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.png
  			//http://korona.geog.uni-heidelberg.de/tiles/roadsg/x={x}&y={y}&z={z}


  			// Arquivo GeoJson com informações dos municípios
  			var geojsonObject;

  			// Abre o GeoJson com os dados
  			$.getJSON("pop_2015_json.geojson", function(json) {
				geojsonObject = L.geoJson(json, {style: style, onEachFeature: onEachFeature});
				geojsonObject.addTo(map);
			});


  			// Funcao para diferenciar o estilo de cada feicao
  			function getColor(p) {
			    return p > 2000000  ? '#023858' :
			           p > 600000   ? '#045a8d' :
			           p > 300000   ? '#0570b0' :
			           p > 90000    ? '#3690c0' :
			           p > 20000    ? '#74a9cf' :
			           p > 10000    ? '#a6bddb' :
			                          '#d0d1e6';
			}

			// Funcao para aplicar o estilo padrão
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

			// Aplica o estilo ao passar o mouse
			function highlightFeature(e) {
			    var layer = e.target;

			    layer.setStyle({
			        weight: 3,
			        color: '#666',
			        dashArray: '',
			        fillOpacity: 0.7,
			        opacity: 1
			    });

			    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
			        layer.bringToFront();
			    }

			    info.update(layer.feature.properties);
			}

			// Limpa a formatação ao retirar o mouse
			function resetHighlight(e) {
			    geojsonObject.resetStyle(e.target);

			    info.update();
			}

			// Da zoom para feição ao clicar
			function zoomToFeature(e) {
			    map.fitBounds(e.target.getBounds());
			}

			// Aplica as funcionalidades a cada feição
			function onEachFeature(feature, layer) {
			    layer.on({
			        mouseover: highlightFeature,
			        mouseout: resetHighlight,
			        click: zoomToFeature
			    });
			}

			// Adiciona Campo para mensagem
			var info = L.control();

			info.onAdd = function (map) {
			    this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
			    this.update();
			    return this._div;
			};

			// method that we will use to update the control based on feature properties passed
			info.update = function (props) {
			    this._div.innerHTML = '<h4>População por Município</h4>' +  (props ?
			        '<b>' + props.nome + '</b><br />' + props.pop_2015 + ' habitantes</sup>'
			        : ' ');
			};

			info.addTo(map);


			// Adiciona legenda
			var legend = L.control({position: 'bottomright'});

			legend.onAdd = function (map) {

			    var div = L.DomUtil.create('div', 'info legend'),
			        grades = [0, 10000, 20000, 90000, 300000, 600000, 2000000],
			        labels = [];

			    // loop through our density intervals and generate a label with a colored square for each interval
			    for (var i = 0; i < grades.length; i++) {
			        div.innerHTML +=
			            '<i class="legenda" style="background:' + getColor(grades[i] + 1) + '">&nbsp; &nbsp; &nbsp;</i> ' +
			            grades[i] + (grades[i + 1] ? ' &ndash; ' + grades[i + 1] + '<br>' : ' +');
			    }

			    return div;
			};

			legend.addTo(map);




  		</script>


  		<br/><br/><br/>

  	</body>

</html>