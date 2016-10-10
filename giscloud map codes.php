
		<!-- Funções do mapa -->
		<div id="toolbar"></div>

		<!-- Adiciona o mapa GisCLoud -->
		<div id='mapViewer'></div>

  		<script type='text/javascript'>
			giscloud.ready(function () {

				var mapId = 603048,
	    		
	        	viewer = new giscloud.Viewer('mapViewer', mapId);

	        	new giscloud.ui.Toolbar({
                    viewer: viewer,
                    container: "toolbar",
                    defaultTools: ["pan", "zoom", "full", "select"]
            	});
			});

		</script>


<!-- iframe is a tag used to display pages within another page-->
		<iframe src="http://editor.giscloud.com/rest/1/maps/603048/render.iframe?toolbar=true&popups=true&layerlist=true" width="600" height="400" frameborder="0"></iframe>








		<!-- Funções do mapa -->
		<div id="toolbar"></div>

		<!-- Adiciona o mapa GisCLoud -->
		<div id='mapViewer'></div>

  		<script type='text/javascript'>
			giscloud.ready(function () {

				var mapId = 603048,
	    		
	        	viewer = new giscloud.Viewer('mapViewer', mapId);

	        	new giscloud.ui.Toolbar({
                    viewer: viewer,
                    container: "toolbar",
                    defaultTools: ["pan", "zoom", "full", "select"]
            	});
			});

		</script>

		