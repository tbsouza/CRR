<!doctype html>
<html lang="pt-br">

  	<head>

  		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="Content-Type" content="text/html/map; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		
		<!-- Titulo da página -->
		<title> CRR </title>

		<!-- JQuery -->
		<script src="jquery-3.0.0.js" ></script>

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

		<!-- JavaScript Toastr -->
		<script src="toastr.js" type="text/javascript"></script>
		<!-- CSS Toatr -->
		<link rel="stylesheet" href="toastr.css" type="text/css" >

		<!-- JavaScript FullScreen -->
		<script src="leaflet.fullscreen.min.js" type="text/javascript"></script>
		<!-- CSS FullScreen -->
		<link rel="stylesheet" href="leaflet.fullscreen.css" type="text/css" >

  	</head>

  	<body onload="getGJSON()">

  		<br/>

	  	<!-- Campo que sera adicionado o mapa -->
	  	<div id="map" class="mapViewer"></div>

  		<script type="text/javascript">
  		
  			// Objeto GeoJson com informações dos municípios
  			var geojsonObject, geojsonObject0, geojsonObject1, geojsonObject2,
  			    geojsonObject3, geojsonObject4, geojsonObject5, geojsonObject6;
  			var gjson;

  			function getGJSON(){
	  			// Abre o GeoJson com os dados
	  			$.getJSON("informacoes_geojson.geojson", function(json) {
					// Camada principal
					geojsonObject = L.geoJson(json, {style: style, onEachFeature: onEachFeature});

					// Camadas dos Filtros
					geojsonObject0 = L.geoJson(json, {style: style0});
					geojsonObject1 = L.geoJson(json, {style: style1});
					geojsonObject2 = L.geoJson(json, {style: style2});
					geojsonObject3 = L.geoJson(json, {style: style3});
					geojsonObject4 = L.geoJson(json, {style: style4});
					geojsonObject5 = L.geoJson(json, {style: style5});
					geojsonObject6 = L.geoJson(json, {style: style6});

					// Adiciona a camada principal no mapa
					geojsonObject.addTo(map);
					
					gjson = json;
				});
  			}


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


  			// Mensagem de notificação
			map.onRemove = function(){
				toastr.info("Camada removida");
			}


  			// Funcao para diferenciar o estilo de cada feicao
  			function getColor(p) {
			    return p > 2000000 ? '#023858' :
			           p > 600000  ? '#045a8d' :
			           p > 300000  ? '#0570b0' :
			           p > 90000   ? '#3690c0' :
			           p > 20000   ? '#74a9cf' :
			           p > 10000   ? '#a6bddb' :
			                         '#d0d1e6';
			}

			// Funcao para aplicar o estilo padrão
			function style(feature) {
			    return {
			        fillColor: getColor(feature.properties.pop_2015),
			        weight: 1,
			        opacity: 0.98,
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
			        weight: 4,
			        color: '#4D4D4D',
			        dashArray: '',
			        fillOpacity: 1
			    });

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

			    if( circle!=null ){
			    	map.removeLayer(circle);
			    }

			    circle = L.circle(e.latlng, {
				    color: 'red',
				    fillColor: '#f03',
				    fillOpacity: 0.4,
				    radius: 650
				}).addTo(map);
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

			    div.innerHTML += '<input id="checkFilter" type="checkbox" /> &nbsp; Filter <br> ';

			    // loop through our population intervals and generate a label with a colored square for each interval
			    for (var i = 0; i < grades.length; i++) {
			        div.innerHTML +=
			            '<i class="legenda" style="background:' + getColor(grades[i] + 1) + '"></i><input id="check' + i + 
			            '" type="checkbox" disabled/> ' +
			            grades[i] + (grades[i + 1] ? ' &ndash; ' + grades[i + 1] + '<br>' : ' +');
			    }

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
				L.easyButton('<img class="imgButton" src="center.png"/>', function(btn, map){
				    map.setView([lat, lon], zoom);
				}, 'Center'),

				// Botão para localizar posição do usuário
				L.easyButton('<img class="imgButton" src="marker.png"/>', function(btn, map){
				    map.locate({setView : true, maxZoom: 10});
				}, 'Locate'),

				// Botão para limpar marcadores
				L.easyButton('<img class="imgButton" src="erase.png"/>', function(btn, map){
				    removeMarkers();
				}, 'Clear')
			];

			// adiciona o toolbar no mapa
			L.easyBar(buttons).addTo(map);

			// Funções e localização
			// Ao encontrar localização
			function onLocationFound(e) {

				popup = L.popup().setLatLng(e.latlng).setContent("Você está aqui!").openOn(map);

				if( circle!=null ){
			    	map.removeLayer(circle);
			    }

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


			// Funcao para aplicar os estilos dos filtros
			function style0(feature) {

				var pop = feature.properties.pop_2015;

				if( pop < 10000 ){
					return {
				        fillColor: '#d0d1e6',
				        weight: 1,
				        opacity: 0.98,
				        color: 'grey',
				        dashArray: '',
				        fillOpacity: 0.9
				    };
				}else{
					return transparentColor();
				}  
			}

			function style1(feature) {

				var pop = feature.properties.pop_2015;

				if( pop >= 10000 && pop < 20000 ){
					return {
				        fillColor: '#a6bddb',
				        weight: 1,
				        opacity: 0.98,
				        color: 'grey',
				        dashArray: '',
				        fillOpacity: 0.9
				    };
				}else{
					return transparentColor();
				}  
			}

			
			function style2(feature) {

				var pop = feature.properties.pop_2015;

				if( pop >= 20000 && pop < 90000 ){
					return {
				        fillColor: '#74a9cf',
				        weight: 1,
				        opacity: 0.98,
				        color: 'grey',
				        dashArray: '',
				        fillOpacity: 0.9
				    };
				}else{
					return transparentColor();
				}  
			}

			function style3(feature) {

				var pop = feature.properties.pop_2015;

				if( pop >= 90000 && pop < 300000 ){
					return {
				        fillColor: '#3690c0',
				        weight: 1,
				        opacity: 0.98,
				        color: 'grey',
				        dashArray: '',
				        fillOpacity: 0.9
				    };
				}else{
					return transparentColor();
				}  
			}

			function style4(feature) {

				var pop = feature.properties.pop_2015;

				if( pop >= 300000 && pop < 600000 ){
					return {
				        fillColor: '#0570b0',
				        weight: 1,
				        opacity: 0.98,
				        color: 'grey',
				        dashArray: '',
				        fillOpacity: 0.9
				    };
				}else{
					return transparentColor();
				}  
			}

			function style5(feature) {

				var pop = feature.properties.pop_2015;

				if( pop >= 600000 && pop < 2000000 ){
					return {
				        fillColor: '#045a8d',
				        weight: 1,
				        opacity: 0.98,
				        color: 'grey',
				        dashArray: '',
				        fillOpacity: 0.9
				    };
				}else{
					return transparentColor();
				}  
			}

			function style6(feature) {

				var pop = feature.properties.pop_2015;

				if( pop >= 2000000 ){
					return {
				        fillColor: '#023858',
				        weight: 1,
				        opacity: 0.98,
				        color: 'grey',
				        dashArray: '',
				        fillOpacity: 0.9
				    };
				}else{
					return transparentColor();
				}  
			}

			// Função que retorna cor transparente
			function transparentColor(){
				return {
				    fillColor: '#00000000',
				    weight: 1,
				    opacity: 0.98,
				    color: 'grey',
				    dashArray: '',
				    fillOpacity: 0.9
				};
			}


			// Ação do Checkbox do Filtro
			function checkClicked() {

				// Verifica se os checkboxes estão selecionados para ativar o filtro

				if( $('#check0').is(":checked") ){
					geojsonObject0.addTo(map);
				}else{
					map.removeLayer(geojsonObject0);
				}

				if( $('#check1').is(":checked") ){
					geojsonObject1.addTo(map);
				}else{
					map.removeLayer(geojsonObject1);
				}

				if( $('#check2').is(":checked") ){
					geojsonObject2.addTo(map);
				}else{
					map.removeLayer(geojsonObject2);
				}

				if( $('#check3').is(":checked") ){
					geojsonObject3.addTo(map);
				}else{
					map.removeLayer(geojsonObject3);
				}

				if( $('#check4').is(":checked") ){
					geojsonObject4.addTo(map);
				}else{
					map.removeLayer(geojsonObject4);
				}

				if( $('#check5').is(":checked") ){
					geojsonObject5.addTo(map);
				}else{
					map.removeLayer(geojsonObject5);
				}

				if( $('#check6').is(":checked") ){
					geojsonObject6.addTo(map);
				}else{
					map.removeLayer(geojsonObject6);
				}
			}

			// Adiciona ação dos checkboxes
			document.getElementById("check0").addEventListener("click", checkClicked, true);
			document.getElementById("check1").addEventListener("click", checkClicked, true);
			document.getElementById("check2").addEventListener("click", checkClicked, true);
			document.getElementById("check3").addEventListener("click", checkClicked, true);
			document.getElementById("check4").addEventListener("click", checkClicked, true);
			document.getElementById("check5").addEventListener("click", checkClicked, true);
			document.getElementById("check6").addEventListener("click", checkClicked, true);


			function removeFilter(){
				map.removeLayer(geojsonObject0);
				map.removeLayer(geojsonObject1);
			}


			// Ação do Checkbox do Filtro
			function checkFilter() {

				// Habilita ou desabilita os checkboxes dos filtros
				if( this.checked ){
					$('#check0').removeAttr("disabled");
					$('#check1').removeAttr("disabled");
					$('#check2').removeAttr("disabled");
					$('#check3').removeAttr("disabled");
					$('#check4').removeAttr("disabled");
					$('#check5').removeAttr("disabled");
					$('#check6').removeAttr("disabled");

					// Remove a camada principal do mapa
					map.removeLayer(geojsonObject);

					// Verifica as camadas dos filtros
					checkClicked();

				}else{
					// Desabilita checkboxes
					$('#check0').attr("disabled", true);
					$('#check1').attr("disabled", true);
					$('#check2').attr("disabled", true);
					$('#check3').attr("disabled", true);
					$('#check4').attr("disabled", true);
					$('#check5').attr("disabled", true);
					$('#check6').attr("disabled", true);

					// Desmarca checkboxes
					$('#check0').prop("checked", false);
					$('#check1').prop("checked", false);
					$('#check2').prop("checked", false);
					$('#check3').prop("checked", false);
					$('#check4').prop("checked", false);
					$('#check5').prop("checked", false);
					$('#check6').prop("checked", false);

					removeFilter();

					// Adiciona a camada principal
					geojsonObject.addTo(map);


				}
			}

			document.getElementById("checkFilter").addEventListener("click", checkFilter, true);



  		</script>


  	</body>


</html>