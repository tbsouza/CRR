

		<script> // Script para adicionar mapa
			
			// div para inserir mapa
			var div = document.createElement("div");
			div.setAttribute("id", "viewDiv");

// ****************** ArcGIS ******************************************************************************
			// faz a requisição do mapa
			require([
				  "esri/Map",
				  "esri/views/MapView",   // SceneView
				  "esri/layers/Layer",
				  "esri/widgets/Legend",
				  "esri/views/ui/DefaultUI",
				  "esri/widgets/Search",
				  "dojo/domReady!"
				], function(Map, MapView, Layer, Legend, DefaultUI, Search) {
					var map = new Map({ // tipo do mapa
					    basemap: "gray"       // streets, topo, satellite, gray, osm, terrain
					});

					// insere o mapa
					var view = new MapView({
					    container: "viewDiv", // Reference to the DOM node that will contain the view
					    map: map,             // References the map object created in step 3
					    zoom: 5,              // Sets the zoom level based on level of detail (LOD)
					    center: [-55, -15]    // Sets the center point of view in lon/lat
					});

					// insere a camada
					Layer.fromPortalItem({
			        	portalItem: {         // autocast as esri/portal/PortalItem
			          		//id: "e067386d91a04e98898aa0bba00d53f6"
			       		}
			        }).then(addLayer).otherwise(rejection);

			        // Adds the layer to the map once it loads
				    function addLayer(lyr) {
				        map.add(lyr);
				        //alert("Layer added");
				    }

				    function rejection(err) {
				        //alert("Layer failed to load: " + err);
				    }

				    // adiciona botao de pesquisa no mapa
				    var searchWidget = new Search({ view: view });
				    view.ui.add(searchWidget, "top-right");
				    searchWidget.startup();

				    var legend = new Legend({
	  					view: view,
	  					layerInfos: [{
	    					layer: (map.layers.getItemAt(0)), // TODO
	    					title: "PIB 2011"
	  						}]
						});
				    view.ui.add(legend, "bottom-right");
				    legend.startup();




				    dojo.connect(map,'onLayersAddResult',function(results){
			          var layerInfo = dojo.map(results, function(layer,index){
			            return {layer:layer.layer,title:layer.layer.name};
			          });
			          // hide layer with an index of 1 in the tiled basemap service
			          // this is the "TownBoundary-9k" layer
			          layerInfo[0].hideLayers = [1];
			          if(layerInfo.length > 0){
			            var legendDijit = new esri.dijit.Legend({
			              map:map,
			              layerInfos:layerInfo
			            },"legendDiv");
			            legendDijit.startup();
			          }
			        });
			});
// *************************************************************************************************************


			// adiciona a div com o mapa ao corpo do html
			document.body.appendChild(div);
		</script>
