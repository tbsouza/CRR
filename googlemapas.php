<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title> Maps </title>

		<meta content="text/html; charset=ISO-8859-1" http-equiv="Content-Type"/>

		<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=AIzaSyAKt4dG2EoTfNHaY3DjxyH6nlI1R4QG-bI" type="text/javascript"></script>
	
		<script type="text/javascript">
			var map = null;
			var geocoder = null;

			function inicializa() {
				// verifica se o navegador do usuário é compatível com a API
				if (GBrowserIsCompatible()) {
					map = new GMap2(document.getElementById("basemap"));
					map.setCenter(new GLatLng(-15, -55), 4);
					geocoder = new GClientGeocoder();
  			    }
			}

			function mostraEndereco(){
				var endereco = document.getElementById("endereco").value;
				if ( geocoder ) {
					geocoder.getLatLng(endereco, 
						function(point){ 
							if ( !point ) {
								alert(endereco + " não encontrado");
							} else {
								map.setCenter(point, 13);
								var marca = new GMarker(point);
								map.addOverlay(marca);
								marca.openInfoWindowHtml( endereco + "<br />" + point.toString() );
							}
						} 
					);
				} else {
					alert("GeoCoder não identificado");
				}
			}



    	</script>

	</head>

	<body onload="inicializa()" onunload="GUnload()">

		<div id="basemap" style="width: 1200px; height: 800px"></div>

		<br>

		<form id="form_mapa" action="#" method="get">
			<input type="text" name="endereco" id="endereco" size="50" value="São Paulo" /> 
			<input type="button" name="enviar" id="enviar" value="Mostrar Latitude/Longitude" onclick="mostraEndereco()"/>
		</form>




	</body>

</html>