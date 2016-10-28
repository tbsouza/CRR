<!doctype html>
<html lang="pt-br">

  	<head>

  		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="Content-Type" content="text/html/map; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		
		<!-- Titulo da página -->
		<title> CRR </title>

		<!-- CSS -->
		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>

  		<!-- JavaScript Leaflet -->
		<script src="leaflet.js" type="text/javascript"></script>
		<!-- CSS Leaflet -->
		<link rel="stylesheet" href="leaflet.css" type="text/css" >

		<!-- JavaScript EasyButton -->
		<script src="easybutton.js" type="text/javascript"></script>
		<!-- CSS EasyButton -->
		<link rel="stylesheet" href="easybutton.css" type="text/css" >

		<!-- JQuery -->
		<script src="jquery-3.0.0.js" ></script>

		<!-- JavaScript Toastr -->
		<script src="toastr.js" type="text/javascript"></script>
		<!-- CSS Toatr -->
		<link rel="stylesheet" href="toastr.css" type="text/css" >

		<!-- JavaScript FullScreen -->
		<script src="leaflet.fullscreen.min.js" type="text/javascript"></script>
		<!-- CSS FullScreen -->
		<link rel="stylesheet" href="leaflet.fullscreen.css" type="text/css" >

  	</head>

  	<body>

  		<br/>

  		<!-- Campo que sera adicionado o mapa -->
  		<div id="map" class="mapViewer"></div>

  		<script type="text/javascript">
  		
  			// configura o toastr (toast messages)
  			configureToast();

			function configureToast(){
	  			toastr.options = {
					"closeButton": false,
					"debug": false,
					"positionClass": "toast-top-right",
					"onclick": null,
					"showDuration": "1000",
					"hideDuration": "2500",
					"timeOut": "3000",
					"extendedTimeOut": "1000",
					"showEasing": "linear",
					"hideEasing": "linear",
					"showMethod": "fadeIn",
					"hideMethod": "fadeOut",
					"newestOnTop": true,
	 				"progressBar": true,
	 				"escapeHtml": true
				}
			}
			

  			// cria um novo mapa
  			var map = L.map('map', {fullscreenControl: true }).setView([-15, -55], 5);

  			// Seleciona o basemap
  			L.tileLayer('http://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}.png', {
			    attribution: '&copy; <a target="_blank" href="http://www.inatel.br/crr/">CRR</a> Inatel',
			    minZoom: 3, maxZoom: 13, unloadInvisibleTiles: true, updateWhenIdle: true, reuseTiles: true
			}).addTo(map);


  			// Tipos de BaseMaps
  			//http://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png
  			//http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png
  			//http://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}
  			//http://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}.png
  			//http://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.png
  			//http://korona.geog.uni-heidelberg.de/tiles/roadsg/x={x}&y={y}&z={z}


  			// Arquivo GeoJson com informações dos municípios
  			var geojsonObject;

  			// Abre o GeoJson com os dados
  			$.getJSON("informacoes_geojson.geojson", function(json) {
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
			        dashArray: '',
			        fillOpacity: 0.9
			    };
			}

			// Aplica o estilo ao passar o mouse
			function highlightFeature(e) {
			    var layer = e.target;

			    // estilo ao passar o mouse
			    layer.setStyle({
			        weight: 3,
			        color: '#666',
			        dashArray: '',
			        fillOpacity: 0.6,
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

			    this._div.innerHTML = '<h4>População por Município &nbsp;&nbsp;&nbsp;</h4>' + (props  ?
			        '<b>' + '<i class="info_legenda" style="background:' + getColor(props.pop_2015) + '"></i>' + 
			        	props.nome + ', ' +  props.uf + '</b><br />' + props.pop_2015 + ' habitantes</sup>'  : ' ');
			};

			info.addTo(map);


			// Adiciona legenda
			var legend = L.control({position: 'bottomright'});

			legend.onAdd = function (map) {

			    var div = L.DomUtil.create('div', 'legend'),
			        grades = [0, 10000, 20000, 90000, 300000, 600000, 2000000];

			    // loop through our population intervals and generate a label with a colored square for each interval
			    for (var i = 0; i < grades.length; i++) {
			        div.innerHTML +=
			            '<i class="legenda" style="background:' + getColor(grades[i] + 1) + '"></i><input id="check' + i + 
			            '" checked=true type="checkbox"/> ' +
			            grades[i] + (grades[i + 1] ? ' &ndash; ' + grades[i + 1] + '<br>' : ' +');
			    }

			    //</i><input id="check' + i + '" checked=true type="checkbox"/>

			    return div;
			};

			legend.addTo(map);


			// Posição do centro
			var lat = -15, lon = -55, zoom = 5;

			// variáveis do marcador e popup
			var circle=null, popup=null;

			// Cria um toolbar para os botoes
			var buttons = [

				// Botão para centralizar
				L.easyButton('<strong>&Uparrow;</strong>', function(btn, map){
				    map.setView([lat, lon], zoom);
				}, 'Centralizar'),

				// Botão para localizar posição do usuário
				L.easyButton('<strong>&xodot;</strong>', function(btn, map){
				    map.locate({setView : true, maxZoom: 10});
				}, 'Localizar'),

				// Botão para limpar marcadores
				L.easyButton('<strong>&bemptyv;</strong>', function(btn, map){
				    removeMarkers();
				}, 'Limpar')
			];

			// adiciona o toolbar no mapa
			L.easyBar(buttons).addTo(map);

			// Funções e localização
			// Ao encontrar localização
			function onLocationFound(e) {

				popup = L.popup().setLatLng(e.latlng).setContent("Você está aqui!").openOn(map);

				circle = L.circle(e.latlng, {
				    color: 'red',
				    fillColor: '#f03',
				    fillOpacity: 0.4,
				    radius: 650
				}).addTo(map);

				toastr.success("Localização encontrada");
			}

			map.on('locationfound', onLocationFound);

			// Não encontrar localização
			function onLocationError(e) {

				if( e.message != "Geolocation error: Position acquisition timed out." ){
					toastr.error("Não foi possível encontrar sua posição");
				}

			    console.log(e.message);
			}

			map.on('locationerror', onLocationError);


			// Funão para limpar os marcadores
			function removeMarkers(){
				
				if( circle != null || popup != null ){
					toastr.success("Campos limpos");
				}

				if( circle != null ) {
				    map.removeLayer(circle);
				    circle=null;
				}

				if( popup != null ){
 					map.removeLayer(popup);
				    popup=null;
				}
			}


/*
			// Adiciona checkbox separado da legenda
			// create the checkbox control
			var command = L.control({position: 'bottomleft'});

			command.onAdd = function (map) {
			    var div = L.DomUtil.create('div', 'checkbox');

			    div.innerHTML = '<form><input id="check1" checked=true type="checkbox"/>0 - 10000</form>';
			    div.innerHTML += '<form><input id="check2" checked=true type="checkbox"/>10000 - 20000</form>';
			    div.innerHTML += '<form><input id="check3" checked=true type="checkbox"/>20000 - 90000</form>';
			    div.innerHTML += '<form><input id="check4" checked=true type="checkbox"/>90000 - 300000</form>';
			    div.innerHTML += '<form><input id="check5" checked=true type="checkbox"/>300000 - 600000</form>';
			    div.innerHTML += '<form><input id="check6" checked=true type="checkbox"/>600000 - 2000000</form>';
			    div.innerHTML += '<form><input id="check7" checked=true type="checkbox"/>2000000 +</form>';


			    return div;
			};

			command.addTo(map);


			// add the event handler
			function check0Clicked() {


			   alert("Clicked, checked = " + this.checked);
			}

			document.getElementById("check0").addEventListener("click", check0Clicked, true);

*/




  		</script>


  		<br/><br/><br/>

  	</body>


</html>