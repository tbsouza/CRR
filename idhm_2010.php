<!DOCTYPE html>
<html lang="pt-br">
	<head>

		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="Content-Type" content="text/html/map; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">

		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
		
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

		<!-- JavaScript FullScreen -->
		<script src="leaflet.fullscreen.min.js" type="text/javascript"></script>
		<!-- CSS FullScreen -->
		<link rel="stylesheet" href="leaflet.fullscreen.css" type="text/css" >

	</head>

	<body id="body" onload="getGJSON()">

		<h2> IDHM - 2010</h2>

		<!-- Campo que sera adicionado o mapa -->
	  	<div id="map" class="mapViewer"></div>

  		<script type="text/javascript">
  		
  			// Objeto GeoJson com informações dos municípios
  			var geojsonObject, geoObject;
  			var gjson;

  			function getGJSON(){

  				// arquivo GEOJson a ser aberto
  				var url = "informacoes_geojson.geojson";

	  			// Abre o GeoJson com os dados
	  			$.getJSON(url, function(json) {
					// Cria a camada principal a partir do GEOJson
					geojsonObject = L.geoJson(json, {style: style, onEachFeature: onEachFeature});

					// Adiciona a camada principal no mapa
					geojsonObject.addTo(map);
					
					gjson = json;
				});
  			}

  			// cria um novo mapa
  			var map = L.map('map', {fullscreenControl: true }).setView([-15, -55], 5);

  			// Seleciona o basemap
  			L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | &copy; <a target="_blank" href="http://www.inatel.br/crr/">CRR</a> Inatel',
			    minZoom: 4, maxZoom: 13, unloadInvisibleTiles: true, updateWhenIdle: true
			}).addTo(map);

  			// Funcao para diferenciar o estilo de cada feicao (padrão)
  			function getColor(p) {
			    return p >= 0.800 ? '#1a9850' :
			           p >= 0.750 ? '#66bd63' :
			           p >= 0.700 ? '#a6d96a' :
			           p >= 0.650 ? '#d9ef8b' :
			           p >= 0.600 ? '#fee08b' :
			           p >= 0.550 ? '#fdae61' :
			           p >= 0.500 ? '#f46d43' :
			                        '#d73027';
			}

			// Funcao para aplicar o estilo padrão
			function style(feature) {
			    return {
			        fillColor: getColor(feature.properties.idhm_2010),
			        weight: 1,
			        opacity: 0.98,
			        color: 'grey',
			        dashArray: '',
			        fillOpacity: 0.8
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

			    layer.bringToFront();

			    if( circle != null ){ circle.bringToFront(); }

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

			    this._div.innerHTML = '<h4>Índice de Desenvolvimento Humano</h4>' + (props  ?
			        '<b>' + '<i class="info_legenda" style="background:' + getColor(props.idhm_2010) + '"></i>' + 
			        	props.nome + ', ' +  props.uf + '</b><br />' + props.idhm_2010 + '</sup>'  : ' ');
			};

			info.addTo(map);


			// Adiciona legenda
			var legend = L.control({position: 'bottomright'});

			legend.onAdd = function (map) {

			    var div = L.DomUtil.create('div', 'legend'),
			    grades = [0.400, 0.500, 0.550, 0.600, 0.650, 0.700, 0.750, 0.800];

			    // checkbox para habilitar o filtro
			    div.innerHTML += '<input id="checkFilter" type="checkbox" /> &nbsp; Filter <br> ';

			    // loop through our population intervals and generate a label with a colored square for each interval
			    for (var i = 0; i < grades.length; i++) {
			        div.innerHTML +=
			            '<i class="legenda" style="background:' + getColor(grades[i]) + '"/></i><input id="check' + i + 
			            '" type="radio" disabled/> ' +
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

				circle.bringToFront();
			}

			map.on('locationfound', onLocationFound);

			// Não encontrar localização
			function onLocationError(e) {
			    console.log(e.message);
			}

			map.on('locationerror', onLocationError);


			// Função para limpar os marcadores
			function removeMarkers(){
				
				if( circle != null ) {
				    map.removeLayer(circle);
				    circle=null;
				}

				if( popup != null ){
 					map.removeLayer(popup);
				    popup=null;
				}
			}

			function style0(feature) {

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.400 && idh < 0.500 ){
					return {
				        fillColor: '#d73027',
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

			// Funcao para aplicar os estilos dos filtros
			function style1(feature) {

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.500 && idh < 0.550  ){
					return {
				        fillColor: '#f46d43',
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

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.550 && idh < 0.600 ){
					return {
				        fillColor: '#fdae61',
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

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.600 && idh < 0.650 ){
					return {
				        fillColor: '#fee08b',
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

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.650 && idh < 0.700 ){
					return {
				        fillColor: '#d9ef8b',
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

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.700 && idh < 0.750 ){
					return {
				        fillColor: '#a6d96a',
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

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.750 && idh < 0.800 ){
					return {
				        fillColor: '#66bd63',
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

			function style7(feature) {

				var idh = feature.properties.idhm_2010;

				if( idh >= 0.800 ){
					return {
				        fillColor: '#1a9850',
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
				    fillColor: '#FFFFFF',
				    weight: 1,
				    opacity: 0.98,
				    color: 'grey',
				    dashArray: '',
				    fillOpacity: 0
				};
			}

			function check0Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style0});
				//, onEachFeature: onEachFeature

				geoObject.addTo(map);

				$('#check1').prop("checked", false);
				$('#check2').prop("checked", false);
				$('#check3').prop("checked", false);
				$('#check4').prop("checked", false);
				$('#check5').prop("checked", false);
				$('#check6').prop("checked", false);
				$('#check7').prop("checked", false);
			}

			function check1Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style1});

				geoObject.addTo(map);

				$('#check0').prop("checked", false);
				$('#check2').prop("checked", false);
				$('#check3').prop("checked", false);
				$('#check4').prop("checked", false);
				$('#check5').prop("checked", false);
				$('#check6').prop("checked", false);
				$('#check7').prop("checked", false);
			}

			function check2Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style2});

				geoObject.addTo(map);

				$('#check0').prop("checked", false);
				$('#check1').prop("checked", false);
				$('#check3').prop("checked", false);
				$('#check4').prop("checked", false);
				$('#check5').prop("checked", false);
				$('#check6').prop("checked", false);
				$('#check7').prop("checked", false);
			}

			function check3Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style3});

				geoObject.addTo(map);

				$('#check0').prop("checked", false);
				$('#check1').prop("checked", false);
				$('#check2').prop("checked", false);
				$('#check4').prop("checked", false);
				$('#check5').prop("checked", false);
				$('#check6').prop("checked", false);
				$('#check7').prop("checked", false);
			}

			function check4Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style4});

				geoObject.addTo(map);

				$('#check0').prop("checked", false);
				$('#check1').prop("checked", false);
				$('#check2').prop("checked", false);
				$('#check3').prop("checked", false);
				$('#check5').prop("checked", false);
				$('#check6').prop("checked", false);
				$('#check7').prop("checked", false);
			}

			function check5Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style5});

				geoObject.addTo(map);

				$('#check0').prop("checked", false);
				$('#check1').prop("checked", false);
				$('#check2').prop("checked", false);
				$('#check3').prop("checked", false);
				$('#check4').prop("checked", false);
				$('#check6').prop("checked", false);
				$('#check7').prop("checked", false);
			}

			function check6Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style6});

				geoObject.addTo(map);

				$('#check0').prop("checked", false);
				$('#check1').prop("checked", false);
				$('#check2').prop("checked", false);
				$('#check3').prop("checked", false);
				$('#check4').prop("checked", false);
				$('#check5').prop("checked", false);
				$('#check7').prop("checked", false);
			}

			function check7Clicked(){
				removeFilter();

				geoObject = L.geoJson(gjson, {style: style7});

				geoObject.addTo(map);

				$('#check0').prop("checked", false);
				$('#check1').prop("checked", false);
				$('#check2').prop("checked", false);
				$('#check3').prop("checked", false);
				$('#check4').prop("checked", false);
				$('#check5').prop("checked", false);
				$('#check6').prop("checked", false);
			}

			// Adiciona ação dos checkboxes
			document.getElementById("check0").addEventListener("click", check0Clicked, true);
			document.getElementById("check1").addEventListener("click", check1Clicked, true);
			document.getElementById("check2").addEventListener("click", check2Clicked, true);
			document.getElementById("check3").addEventListener("click", check3Clicked, true);
			document.getElementById("check4").addEventListener("click", check4Clicked, true);
			document.getElementById("check5").addEventListener("click", check5Clicked, true);
			document.getElementById("check6").addEventListener("click", check6Clicked, true);
			document.getElementById("check7").addEventListener("click", check7Clicked, true);

			function removeFilter(){
				if( geoObject != null ){
					map.removeLayer(geoObject);
				}
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
					$('#check7').removeAttr("disabled");

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
					$('#check7').attr("disabled", true);

					// Desmarca checkboxes
					$('#check0').prop("checked", false);
					$('#check1').prop("checked", false);
					$('#check2').prop("checked", false);
					$('#check3').prop("checked", false);
					$('#check4').prop("checked", false);
					$('#check5').prop("checked", false);
					$('#check6').prop("checked", false);
					$('#check7').prop("checked", false);

					// Remove todas as camadas de filtros
					removeFilter();

					// Adiciona a camada principal
					geojsonObject.addTo(map);
				}
			}

			document.getElementById("checkFilter").addEventListener("click", checkFilter, true);

  		</script>


<!-- *********************************************************************************************** -->
		<div class="divSearch" >

			<input id="inputSearch" maxlength="54" type="text" name="text" size="42" placeholder="Por exempo: São Paulo, MG, 2109056">
			<button class="button" id="buttonSearch" type="button" onclick="btnSearch()">Pesquisar</button>
			<button class="button" id="buttonAll" type="button" onclick="connect()">Todos os resultados</button>
	
		</div>

		<br/><br/>

		<!-- Lista/Tabela de Resultados -->
		<script type="text/javascript">

			// variaveis globais
			var flag = 1;     // verificar se o usuário está realizando a mesma busca
			var content = ""; // conteúdo digitado pelo usuário para pesquisa

			connect();

			function connect(){

				if( flag != 0 ){

					flag = 0;

					$.ajax({
			      		url:'idhm_2010_bd.php',
			      		complete: function (response) {
			         		fncMunicipios(response.responseText);
			      		},
			      		error: function () {
			          		console.log('Error');
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
				    th.appendChild(document.createTextNode( "IDH" ));
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
				    p.appendChild( document.createTextNode("Dados de 2010") );
				    div.appendChild(p);
				}else{
					var div = document.getElementById("div");
					var p = document.createElement("p");
					p.appendChild( document.createTextNode("Nenhum resultado encontrado") );
					div.appendChild(p);
				}
			}
		
			//Campo pesquisar
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
				      		url:'idhm_2010_search.php',
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

			// Script para pesquisar com enter
			$(document).ready(function(){
				$('#inputSearch').keypress(function(e){
					if(e.keyCode==13)
					    $('#buttonSearch').click();
				});
			});

		</script>

	</body>
</html>