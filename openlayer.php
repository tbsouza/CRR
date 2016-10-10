<!doctype html>
<html lang="en">

  	<head>

  		<meta charset="UTF-8"> <!-- Formato de codificação dos caracteres -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">

		<!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
    	<script src="http://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
		
		<!-- Titulo da página -->
		<title> CRR </title>

  		<!-- http://openlayers.org/en/v3.18.2/build/ol.js -->
		<script src="ol.js" type="text/javascript"></script>
		<!-- http://openlayers.org/en/v3.18.2/css/ol.css -->
		<link rel="stylesheet" href="ol.css" type="text/css" >

		<script type='text/javascript' src='http://api.giscloud.com/1/api.js' ></script>

    	<style>
	      #mapViewer {
	       border-radius: 5px; 
	       border: 1px solid #C5C5C5; 
	       width: 1200px; 
	       height: 1000px;
	       /* position: absolute; */
	      }

	      #toolbar{
	      	height: 22px;
	        width: 85px;
	        border: 1px solid #C5C5C5;
	        background-color: #fffbfa;
	        border-radius: 5px;
	      }

    	</style>
    
  	</head>

  	<body>


  		<!-- GIS CLOUD -->
  		<br/><br/><br/>

  		<div id="toolbar"></div>

  		<div id='mapViewer'></div>

  		<script type='text/javascript'>

			giscloud.ready(function () {

				var mapId = 603048,

	        	viewer = new giscloud.Viewer('mapViewer', mapId, { slider: true });

	        	new giscloud.ui.Toolbar({
                    viewer: viewer,
                    container: "toolbar",
                    defaultTools: ["pan", "tool", "full", "select"]
            	});
	        	
			});

		</script>


		<br/><br/><br/><br/>

<!--

	    <h2>My Map</h2>

	    <div id="map" class="map"></div>

	    <script type="text/javascript">

	    	// vector layer
			var vector = new ol.layer.Vector({
			    source: new ol.source.Vector({
			        format: new ol.format.GeoJSON(),
			        url: 'pop_2015_json.geojson'
			    }),
			    style: new ol.style.Style({
			        stroke: new ol.style.Stroke({
			            color: '#eedd00',
			            width: 1.5
			        })
			    })
			});

			// cria o mapa
		    var map = new ol.Map({
		        target: 'map',
		        layers: [
		          new ol.layer.Tile({
		            source: new ol.source.OSM()
		          }),
		          vector,

		          new ol.layer.Image({
			          extent: [2033814, 6414547, 2037302, 6420952],
			          preload: Infinity,
			          visible: true,
			          source: new ol.source.ImageWMS({
			            url: 'http://editor.giscloud.com/wms/3a87203b8f1e9a5004f945aaff8c0427',
			            serverType: 'geoserver'
			          })
		          })
		        ],

		        //renderer: 'canvas',
		      
		        view: new ol.View({
		          	center: ol.proj.fromLonLat([-55, -15]),
		          	zoom: 4.7
		        })
		    });
		    //map.zoomToMaxExtent();

		    // adiciona escala
		    var scaleLineControl = new ol.control.ScaleLine({});
			map.addControl(scaleLineControl);


			// mouse position (coordenadas)
			var mousePosition = new ol.control.MousePosition({
				coordinateFormat: ol.coordinate.createStringXY(3),
				projection: 'EPSG:4326',
			});
			map.addControl(mousePosition);	

/*
			var slider = new ol.control.ZoomSlider();
			map.addControl(slider);
*/

			// overview do mapa
			var overview = new ol.control.OverviewMap();
			map.addControl(overview);





	    </script>

-->


  	</body>

</html>