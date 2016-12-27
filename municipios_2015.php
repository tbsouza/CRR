<!DOCTYPE html>
<html lang="pt-br">
	<head>

		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="CRR,Inatel,População,Brasil,2015">
		<meta name="author" content="Thiago Souza">
		<meta name="description" content="População do Brasil por Município - 2015">
		<meta property="og:title" content="População 2015 - CRR">
		<meta property="og:image" content="crr_logo_pequeno.png">
		<meta property="og:description" content="População do Brasil por Município em 2015.">
		<meta property="og:site_name" content="Centro de Referência em Radiocomunicações">

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

		<!-- JavaScript Toastr -->
		<script src="toastr.js" type="text/javascript"></script>
		<!-- CSS Toatr -->
		<link rel="stylesheet" href="toastr.css" type="text/css" >

		<!-- JavaScript FullScreen -->
		<script src="leaflet.fullscreen.min.js" type="text/javascript"></script>
		<!-- CSS FullScreen -->
		<link rel="stylesheet" href="leaflet.fullscreen.css" type="text/css" >

	</head>

	<body id="body" onload="getJSON()">

		<!-- Titulo da pagina -->
		<h2> População do Brasil - 2015</h2>

		<!-- Campo que sera adicionado o mapa -->
		<div id="outer">
	  		<div id="map" class="mapViewer"></div>
	  	</div>

	  	<div id="load" class="load">
	  		<div id="loadInner" class="loadInner">
		  		<p>Aguarde o mapa ser carregado.</p>	
		  		<div id="loadCircle"></div>
	  		</div>
	  	</div>

  		<script type="text/javascript">
  		
 //********************** Variáveis Gloabais ********************************
  			// Objeto GeoJson com informações dos municípios
  			var geojsonObject=null, geoObject=null, gjson=null;
  			
  			// Variável que reberá o mapa
  			var map=null;

  			// Posição do centro
			var lat = -15, lon = -55, zoom = 4;

			// variáveis do marcador e popup
			var circle=null, popup=null;

			// Adiciona Campo para mensagem
			var info = L.control();
//***************************************************************************

  			// Função para abrir o geojson via ajax
  			function getJSON(){

  				// Verifica o navegador do usuário
  				verifyBrowser();

  				// arquivo GEOJson a ser aberto
  				var url = "informacoes_geojson.geojson";

	  			// Abre o GeoJson com os dados
	  			$.getJSON(url, function(json) {

	  				// Oculta a div com loading
	  				$('.loadInner').hide();

					// salva o arquivo GEOJson aberto
					gjson = json;

					// Cria o mapa
					createMap();

					// Cria a camada principal a partir do GEOJson
					geojsonObject = L.geoJson(json, {style: style, onEachFeature: onEachFeature});

					// Adiciona a camada principal no mapa
					geojsonObject.addTo(map);
				});

				// Notificação para usuário aguardar
				toastr.info("Isso pode demorar um pouco.", "Aguarde o mapa ser carregado!" );
  			}

  			function verifyBrowser(){

  				// Se for Internet Explore ou Edge  
  				// sugere para usuário utilizar outro navegador
  				if(L.Browser.ie || L.Browser.edge){
  					alert( "Para uma melhor experiência, recomendamos que você utilize outro navegador. " );
  				}
  			}

  			// Função para criar o mapa
  			function createMap(){

  				// cria um novo mapa
		  		map = L.map('map', {fullscreenControl: true }).setView([-15, -55], 4);

		  		// Seleciona o basemap
		  		L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				   attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | &copy; <a target="_blank" href="http://www.inatel.br/crr/">CRR</a> Inatel',
				   		minZoom: 4, maxZoom: 13, unloadInvisibleTiles: true, updateWhenIdle: true
				}).addTo(map);

				info.onAdd = function (map) {
				    this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
				    this.update();
				    return this._div;
				};

				// method that we will use to update the control based on feature properties passed
				info.update = function (props) {

					// Atualiza a div com o dado do município que o mouse esta sobre
				    this._div.innerHTML = '<h4>População por Município &nbsp;&nbsp;&nbsp;</h4>' + (props  ?
				        '<b>' + '<i class="info_legenda" style="background:' + getColor(props.pop_2015) + '"></i>' + 
				        	props.nome + ', ' +  props.uf + '</b><br />' + props.pop_2015 + '</sup>'  : ' ');
				};

				// adiciona as informações feitas acima ao mapa
				info.addTo(map);


				// Variável que receberá a legenda do mapa
				var legend = L.control({position: 'bottomright'});

				// Cria a legenda
				legend.onAdd = function (map) {

				    var div = L.DomUtil.create('div', 'legend'),
				        grades = [0, 10000, 20000, 90000, 300000, 600000, 2000000];

				    // checkbox para habilitar o filtro
				    div.innerHTML += '<input id="checkFilter" type="checkbox" /> &nbsp; Filtrar <br> ';

				    // loop through our population intervals and generate a label with a colored square for each interval
				    for (var i = 0; i < grades.length; i++) {
				        div.innerHTML +=
				            '<i class="legenda" style="background:' + getColor(grades[i] + 1) + '"/></i><input id="check' + i + 
				            '" type="radio" disabled/> ' +
				            grades[i] + (grades[i + 1] ? ' &ndash; ' + grades[i + 1] + '<br>' : ' +');
				    }

				    return div;
				};

				// adiciona a legenda (criada acima) ao mapa
				legend.addTo(map);

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

				// Quando encontrar a localilação chama a função onLocationFound
				map.on('locationfound', onLocationFound);

				// Quando houver um erro na localização chama a função onLocationError
				map.on('locationerror', onLocationError);

				// Adiciona ação dos checkboxes
				document.getElementById("check0").addEventListener("click", check0Clicked, true);
				document.getElementById("check1").addEventListener("click", check1Clicked, true);
				document.getElementById("check2").addEventListener("click", check2Clicked, true);
				document.getElementById("check3").addEventListener("click", check3Clicked, true);
				document.getElementById("check4").addEventListener("click", check4Clicked, true);
				document.getElementById("check5").addEventListener("click", check5Clicked, true);
				document.getElementById("check6").addEventListener("click", check6Clicked, true);

				// Adiciona o eventro de click
				document.getElementById("checkFilter").addEventListener("click", checkFilter, true);
  			}

//***************************************************************************************
  			// configura o toastr (toast messages)
  			configureToast();

			function configureToast(){ // opções do toastr
	  			toastr.options = {
					"closeButton": true,
					"debug": false,
					"positionClass": "toast-top-right",
					"onclick": null,
					"showDuration": "1000",
					"hideDuration": "2500",
					"timeOut": "5000",
					"extendedTimeOut": "1000",
					"showEasing": "linear",
					"hideEasing": "linear",
					"showMethod": "fadeIn",
					"hideMethod": "fadeOut",
					"newestOnTop": true,
	 				"progressBar": false,
	 				"escapeHtml": true
				}
			}
//***************************************************************************************

  			// Funcao para diferenciar o estilo de cada feicao (padrão)
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
			        fillOpacity: 0.7
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

			    // Coloca a camada na frente das outras (por cima)
			    if(!L.Browser.ie && !L.Browser.edge){ layer.bringToFront(); }
			    
			    // Coloca o circulo na frente da camada
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
				// Ajusta o zoom para o tamanho do município
			    map.fitBounds(e.target.getBounds());

			    // Se possuir algum outro circulo o remove
			    if( circle!=null ){ map.removeLayer(circle); }

			    // Desenha um circulo na posição do click
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

			// Funções e localização
			// Ao encontrar localização
			function onLocationFound(e) {

				// Cria popup indicando a localização do usuário
				popup = L.popup().setLatLng(e.latlng).setContent("Você está aqui!").openOn(map);

				// Verifica se já existe algum circulo desenhado e o remove
				if( circle!=null ){ map.removeLayer(circle); }

			    // Desenha um circulo na localização do usuario
				circle = L.circle(e.latlng, {
				    color: 'red',
				    fillColor: '#f03',
				    fillOpacity: 0.4,
				    radius: 650
				}).addTo(map);

				// Coloca o circulo desenhado por cima
				circle.bringToFront();

				// Notificação de sucesso
				toastr.success("Localização encontrada");
			}

			// Não encontrar localização
			function onLocationError(e) {

				// Evita memsagem de erro se estourar o timeout da localização
				if( e.message != "Geolocation error: Position acquisition timed out." ){
					toastr.error("Não foi possível encontrar sua localização");
				}

				// Exibe a msg de erro no console (debug)
			    console.log(e.message);
			}

			// Função para limpar os marcadores
			function removeMarkers(){
				
				// Se tem algum circulo ou popup mostra notificação
				if( circle != null || popup != null ){
					toastr.success("Campos limpos");
				}

				// Se tem algum circulo desenhado o eleimina
				if( circle != null ) {
				    map.removeLayer(circle);
				    circle=null;
				}

				// Se tem algum popup desenhado o eleimina
				if( popup != null ){
 					map.removeLayer(popup);
				    popup=null;
				}
			}

			// Funções para aplicar os estilos dos filtros
			// Uma função para cada intervalo da legenda

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
				    fillColor: '#FFFFFF',
				    weight: 1,
				    opacity: 0.98,
				    color: 'grey',
				    dashArray: '',
				    fillOpacity: 0
				};
			}

			// Funções verificar qual botão foi clicado e adiciona a camada no mapa

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
			}

			// Remove todas as camadas de filtros
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

					// Remove a camada principal do mapa
					map.removeLayer(geojsonObject);

					// button 0 clicado por padrão
					$('#check0').prop("checked", true);
					check0Clicked();

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

					// Remove todas as camadas de filtros
					removeFilter();

					// Adiciona a camada principal
					geojsonObject.addTo(map);
				}
			}

  		</script>

<!-- *********************************************************************************************** -->
		<!-- Campo pesquisar -->
		<div class="divSearch" >

			<p>Alterar data das informações</p>

			<form id="inputData" action="municipios_2014.php">
   				<input title="Mudar para 2014" class="button" type="submit" value="2014" />
   				<input title="Data atual" class="button_disable" type="submit" value="2015" disabled="true" />
			</form>

			<br><br>

			<input id="inputSearch" maxlength="54" type="text" name="text" size="42" placeholder="Por exempo: São Paulo, MG, 2109056">
			<button class="button" id="buttonSearch" type="button" onclick="btnSearch()">Pesquisar</button>
			<button class="button" id="buttonAll" type="button" onclick="connect()">Todos os resultados</button>
	
		</div>

		<br/><br/>

		<div id="div_table"></div>
<!-- *********************************************************************************************** -->

		<!-- Lista/Tabela de Resultados -->
		<script type="text/javascript">

			// variaveis globais
			var flag = 1;     // verificar se o usuário está realizando a mesma busca
			var content = ""; // conteúdo digitado pelo usuário para pesquisa

			connect();

			// chama função php para conectar com o banco
			function connect(){

				if( flag != 0 ){

					flag = 0;

					// Ajax para chamar php de forma assincrona
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

			// Função chamada pelo retorno do php
			// Constroi a tabela com os resultados
			function fncMunicipios(response){

				var d = document.getElementById("wrap");
				var cont = document.body.contains(d);

				var div = document.getElementById("div_table");

				// verifica se tabela ja exista e limpa
				if( cont == false ){
					// div inner
					var wrap = document.createElement("div");
			    	wrap.setAttribute('class', 'wrap');
			    	wrap.setAttribute('id', 'wrap');
				}else{
					var wrap = document.getElementById("wrap");
				}
				
				wrap.innerHTML = "";
				div.innerHTML = "";

				var res = response.split("#");
			    var sz = res.length;
			    var i=0;
			    var columns = 4;
			    var qtd = (sz-1)/columns;

			    // elemento para pular linha
				var br = document.createElement("p");

				// cria elementro center para centralizar a div
				var center = document.createElement("center");

			    if( qtd>0 ){

			    	// div de fora
			    	var wrap = document.createElement("div");
			    	wrap.setAttribute('class', 'wrap');

			    	// div de dentro
			    	var inner = document.createElement("div");
			    	inner.setAttribute('class', 'inner');

			    	// table do cabeçalho
			    	var head = document.createElement("table");
			    	head.setAttribute('class', 'head');

			    	// table do conteudo
			    	var table = document.createElement("table");
			    	table.setAttribute('class', 'inner_table');

				    // linha do cabeçalho
				    var tr = document.createElement("tr");

				    // conteudo do cabeçalho
				    var td = document.createElement("td");
				    td.appendChild(document.createTextNode( "Código" ));
				    td.setAttribute( 'style', 'width:100px' );
				    tr.appendChild(td);

					var td = document.createElement("td");
				    td.appendChild(document.createTextNode( "Município" ));
				    td.setAttribute( 'style', 'width:280px' );
				    tr.appendChild(td);

				    var td = document.createElement("td");
				    td.appendChild(document.createTextNode( "Estado" ));
				    td.setAttribute( 'style', 'width:90px' );
				    tr.appendChild(td);

				    var td = document.createElement("td");
				    td.appendChild(document.createTextNode( "População" ));
				    td.setAttribute( 'style', 'width:100px' );
				    tr.appendChild(td);

				    // adiciona o cabeçalho na tabela head
				    head.appendChild( tr );

				    // adiciona a tabela head na div principal
				    wrap.appendChild( head );

				    // conteudo da tabela (2a table)
				    while( i<(sz-2) ){

				    	//cria cada linha de conteudo da tabela4
				    	var tr = document.createElement("tr");
				    	
				    	var td = document.createElement("td");
				    	td.appendChild(document.createTextNode( res[i] ));
				    	td.setAttribute( 'style', 'width:100px' );
				    	tr.appendChild(td);
				    	i++;

				    	var td = document.createElement("td");
				    	td.appendChild(document.createTextNode( res[i] ));
				    	td.setAttribute( 'style', 'width:280px' );
				    	tr.appendChild(td);
				    	i++;

						var td = document.createElement("td");
				    	td.appendChild(document.createTextNode( res[i] ));
				    	td.setAttribute( 'style', 'width:90px' );
				    	tr.appendChild(td);
				    	i++;

				    	var td = document.createElement("td");
				    	td.appendChild(document.createTextNode( res[i] ));
				    	td.setAttribute( 'style', 'width:100px' );
				    	tr.appendChild(td);
				    	i++;

				    	// adiciona a linha na tabela
				    	table.appendChild(tr);
				    }

				    // adiciona atable de conteudo na dive interna
				    inner.appendChild( table );

				    // adiociona na div principal
				    wrap.appendChild( inner );

				    // total de resultados
				    wrap.appendChild(br);
				    var p = document.createElement("p");
				    p.setAttribute('id','results');
				    p.appendChild( document.createTextNode("Total de resultados: " + qtd) );
				    wrap.appendChild(p);

				    // Data dos dados
				    var p = document.createElement("p");
				    p.appendChild( document.createTextNode("Dados de 2015") );
				    wrap.appendChild(p);
				    wrap.appendChild(br); 
				    wrap.appendChild(br);

				    // centraliza
				    center.appendChild( wrap );
				    center.appendChild(br);

				    // adiciona na div_table
				    div.appendChild(center);
			    }else{
			    	// se nenhum resultado foi retornado
					
					var p = document.createElement("p");

					p.appendChild( document.createTextNode("Nenhum resultado encontrado!") );

					center.appendChild( p );

					div.appendChild( center );
				}
			}


// ***********************************************************************************************
		// Script para pesquisar ao pressionar enter
			$(document).ready(function(){
				$('#inputSearch').keypress(function(e){
					if(e.keyCode==13)
					    $('#buttonSearch').click();
				});
			});
// ***********************************************************************************************

			//Funçao pesquisar
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