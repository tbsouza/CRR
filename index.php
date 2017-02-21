<!DOCTYPE html> <!-- Versao do html a ser usada -->
<html lang="pt-br">

	<head>
		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		
		<!-- Titulo da página -->
		<title> CRR </title>

		<!-- JQuery -->
		<script src="jquery-3.0.0.js" ></script>

		<!-- JavaScript Leaflet -->
		<script src="leaflet.js" type="text/javascript"></script>
		<!-- CSS Leaflet -->
		<link rel="stylesheet" href="leaflet.css" type="text/css" >


		
		<!-- imports -->
		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
	</head>

	<body id="body" onload="onLoad()">

		<center> <!-- Centraliza tudo-->

			<div>
				<img class="imgCRR" src="crr_logo.png" alt="CRR" title="Centro de Referência em Radiocomunicações"/>
			</div>

			<form>
				<br />
				<a  href="municipios_2014.php" target="_blank"> População dos municípios brasileiros</a> 	<hr width="330">
				<a  href="idhm_2010.php" target="_blank"> Índice de Desenvolvimento Humano Municipal</a>
			</form>

<!--
			<form>
				<br />
				<a  href="backbonefibra.php" target="_blank"> Backbone em fibra óptica</a>       			<hr width="330">
				<a  href="municipios_2014.php" target="_blank"> População dos municípios brasileiros</a> 	<hr width="330">
				<a  href="idhm_2010.php" target="_blank"> Índice de Desenvolvimento Humano Municipal</a> 	<hr width="330">
				<a  href="pib_2011.php" target="_blank"> Produto Interno Bruto</a> 							<hr width="330">
				<a  href="acessos_2014.php" target="_blank"> Acessos</a> 									<hr width="330">
				<a  href="escolas_urb.php" target="_blank"> Quantidade de Escolas Urbanas</a> 				<hr width="330">
				<a  href="escolas_rur.php" target="_blank"> Quantidade de Escolas Rurais</a> 				<hr width="330">
				<a  href="petroleo.php" target="_blank"> Plataformas de Petróleo e Gás</a> 					<hr width="330">
				<a  href="esc_rur_sem_bl.php" target="_blank"> Escolas Rurais Sem Banda Larga</a> 			<hr width="330">
				<a  href="esc_rur_sem_int.php" target="_blank"> Escolas Rurais Sem Internet</a> 			<hr width="330">
				<a  href="esc_urb_sem_bl.php" target="_blank"> Escolas Urbanas Sem Banda Larga</a> 			<hr width="330">
				<a  href="esc_urb_sem_int.php" target="_blank"> Escolas Urbanas Sem Internet</a> 			<hr width="330">
				<a  href="pop_rur_2010.php" target="_blank"> Populacao Rural Brasil 2010</a> 				<hr width="330">
				<a  href="pop_urb_2010.php" target="_blank"> Populacao Urbana Brasil 2010</a>				<hr width="330">
				<a  href="leaflettest.php" target="_blank"> Teste Mapa Leaflet</a>
			</form>
-->
		</center>

		<script type="text/javascript">

			// Objeto GeoJson com informações dos municípios
  			var gjson=null;

  			function onLoad(){

  				// Verifica o navegador do usuário
  				verifyBrowser();

  				// arquivo GEOJson a ser aberto
  				var url = "informacoes_geojson.geojson";

	  			// Abre o GeoJson com os dados
	  			$.getJSON(url, function(json) {
					// salva o arquivo GEOJson aberto
					gjson = json;
				});
  			}

  			function verifyBrowser(){

  				// Se for Internet Explore ou Edge  
  				// sugere para usuário utilizar outro navegador
  				if(L.Browser.ie || L.Browser.edge){
  					alert( "Para uma melhor experiência, recomendamos que você utilize outro navegador. " );
  				}
  			}

		</script>


	</body>

</html>