<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<!-- Metadados -->
		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="CRR,Inatel,Acessos,Brasil,2015">
		<meta name="author" content="Thiago Souza">
		<meta name="description" content="Acessos a Banda larga - 2015">
		<meta property="og:title" content="População 2014 - CRR">
		<meta property="og:image" content="crr_logo_pequeno.png">
		<meta property="og:description" content="Acessos a Banda larga no Brasil por Município em 2015.">
		<meta property="og:site_name" content="Centro de Referência em Radiocomunicações">
		
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

	<body id="body" onload="onLoad()">

		<!-- Titulo da pagina -->
		<h2> Acessos a Banda Larga - 2015</h2>

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

			function onLoad(){

				// Verifica o navegador do usuário
  				verifyBrowser();

  				// Carrega o GeoJSON com as informações
  				getJSON();
			}

  			// Função para abrir o geojson via ajax ao carregar a pagina
  			function getJSON(){

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
  			}

  			function verifyBrowser(){

  				// Se for Internet Explore ou Edge  
  				// sugere para usuário utilizar outro navegador
  				if(L.Browser.ie || L.Browser.edge){
  					alert( "Para uma melhor experiência, recomendamos que você utilize outro navegador." );
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
				    this._div.innerHTML = '<h4>Acessos a Banda Larga &nbsp;&nbsp;&nbsp;</h4>' + (props  ?
				        '<b>' + '<i class="info_legenda" style="background:' + getColor(props.acessos_15) + '"></i>' + 
				        	props.nome + ', ' +  props.uf + '</b><br />' + props.acessos_15 + ' acessos</sup>'  : ' ');
				};

				// adiciona as informações feitas acima ao mapa
				info.addTo(map);


				// Variável que receberá a legenda do mapa
				var legend = L.control({position: 'bottomright'});

				// Cria a legenda
				legend.onAdd = function (map) {

				    var div = L.DomUtil.create('div', 'legend'),
				        grades = [0, 200, 500, 1000, 5000, 10000, 50000];

				    // checkbox para habilitar o filtro
				    div.innerHTML += '<input id="checkFilter" type="checkbox" /> &nbsp; Filtrar <br> ';

				    // loop through our intervals and generate a label with a colored square for each interval
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
					}, 'Centralizar'),

					// Botão para limpar marcadores
					L.easyButton('<img class="imgButton" src="erase.png"/>', function(btn, map){
					    removeMarkers();
					}, 'Limpar')
				];

				// adiciona o toolbar no mapa
				L.easyBar(buttons).addTo(map);

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
  			// Funcao para diferenciar o estilo de cada feicao (padrão)
  			function getColor(p) {
			    return p > 50000 ? '#023858' :
			           p > 10000 ? '#045a8d' :
			           p > 5000  ? '#0570b0' :
			           p > 1000  ? '#3690c0' :
			           p > 500   ? '#74a9cf' :
			           p > 200   ? '#a6bddb' :
			                       '#d0d1e6';
			}

			// Funcao para aplicar o estilo padrão
			function style(feature) {
			    return {
			        fillColor: getColor(feature.properties.acessos_15),
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
			        fillOpacity: 0.5
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

			    var prop = e.target.feature.properties;

			    popup = L.popup()
				    .setLatLng(e.latlng)
				    .setContent('<p>' + prop.nome + ', ' + prop.uf  + '<br/>' + prop.acessos_15 + ' acessos</p>')
				    .openOn(map);
			}

			// Aplica as funcionalidades a cada feição
			function onEachFeature(feature, layer) {
			    layer.on({
			        mouseover: highlightFeature,
			        mouseout: resetHighlight,
			        click: zoomToFeature
			    });
			}

			// Função para limpar os marcadores
			function removeMarkers(){
				
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

				// Propriedade do GEOJson aberto (população 2014)
				var acesso = feature.properties.acessos_15;

				if( acesso < 200 ){
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

				var acesso = feature.properties.acessos_15;

				if( acesso >= 200 && acesso < 500 ){
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

				var acesso = feature.properties.acessos_15;

				if( acesso >= 500 && acesso < 1000 ){
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

				var acesso = feature.properties.acessos_15;

				if( acesso >= 1000 && acesso < 5000 ){
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

				var acesso = feature.properties.acessos_15;

				if( acesso >= 5000 && acesso < 10000 ){
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

				var acesso = feature.properties.acessos_15;

				if( acesso >= 10000 && acesso < 50000 ){
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

				var acesso = feature.properties.acessos_15;

				if( acesso >= 50000 ){
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

				// Desmarca os outros radio buttons
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
					geoObject = null;
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
 		<<!-- Campo pesquisar -->
		<div class="divSearch" >

			<p>Alterar data das informações</p>

			<form id="inputData" action="acessos_2014.php">
   				<input title="Mudar para 2014" class="button" type="submit" value="2014" />
   				<input title="Data atual" class="button_disable" type="submit" value="2015" disabled="true" />
			</form>

			<br><br>

			<p> Pesquisar </p>

			<input id="inputSearch" maxlength="54" type="text" name="text" size="42" placeholder="Por exempo: São Paulo, MG, 2109056">
			<button title="Pesquisar" class="button" id="buttonSearch" type="button" onclick="btnSearch()">Pesquisar</button>
			<button title="Exibir todos os resultados" class="button" id="buttonAll" type="button" onclick="connect()">Todos os resultados</button>
	
		</div>

		<br/><br/>

		<div id="div_table"></div>
<!-- *********************************************************************************************** -->

		<!-- Lista/Tabela de Resultados -->
		<script type="text/javascript">

			// Variáveis globais

			var flag = 1;     // verificar se o usuário está realizando a mesma busca
			// Isso evita que o usuário busque a mesma coisa várias vezes em sequencia
			// (evita acessar o bd e retornar o mesmo resultado várias vezes)

			var content = ""; // conteúdo digitado pelo usuário para pesquisa

			var _coluna = "ACESSOS_2015"; // Variável que será passada para o php
			// Representa o nome da coluna no bd que será consultada

			connect();

			// chama função php para conectar com o banco
			function connect(){

				if( flag != 0 ){

					flag = 0;

					// Ajax para chamar php de forma assíncrona
					$.ajax({
			      		url:'banco_todos.php',    // função php que conecta com o banco
			      		method: "post",           // método usado para passar os parâmetros
				      	data: {coluna: _coluna }, // parâmetros passados para o php
			      		complete: function (response) {
			      			// Chama a função para construir a tabela passando os resultados
			         		fncMunicipios(response.responseText);
			      		},
			      		error: function () {
			      			// Caso ocorra algum erro na conexão
			          		console.log('Error');
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
				    td.appendChild(document.createTextNode( "Acessos" ));
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
				    p.appendChild( document.createTextNode("Dados de 2014") );
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


// **************** Acessa o banco para retornar os dados pesquisados *************************
			//Funçao pesquisar
			function btnSearch(){

				var inputSearch = document.getElementById("inputSearch");

				if( flag!=1 || content!=inputSearch.value ){

					flag = 1;

					if( inputSearch.value != "" ){
						// atualiza o placeholder
						var msg = "Por exempo: São Paulo, MG, 2109056";
						inputSearch.placeholder = msg;

						// elimina espaços antes e depois, se houver
						var value = (inputSearch.value).trim();
						var _from = "ACESSOS_2015";

						$.ajax({
				      		url:'banco_busca.php',
				      		method: "post",
				      		data: {text: value, from: _from },
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

			// ***********************************************************************************************
		 	// Script para pesquisar ao pressionar enter
			$(document).ready(function(){
				$('#inputSearch').keypress(function(e){
					if(e.keyCode==13)
					    $('#buttonSearch').click();
				});
			});

		</script>
	</body>
</html>