function fncSelect(){

	// pega o campo selecionado (index selecionado)
	var slc = document.getElementById("slcMapa").selectedIndex;
			
	// faz a verificação
	if( slc==1 ){

		if(document.getElementById("viewDiv") != null){
			document.body.removeChild(document.getElementById("viewDiv"));
			document.body.removeChild(document.getElementById("div"));
			var div = document.createElement("div");
			div.setAttribute("id", "viewDiv");
		}else{
			var div = document.createElement("div");
			div.setAttribute("id", "viewDiv");
		}

		require([
			  "esri/Map",
			  "esri/views/MapView", // SceneView
			  "dojo/domReady!"
			], function(Map, MapView) {
			  var map = new Map({
			    basemap: "gray" // streets, topo, satellite, gray
			  });

			  var view = new MapView({
			    container: "viewDiv",  // Reference to the DOM node that will contain the view
			    map: map,               // References the map object created in step 3
			    zoom: 4,  // Sets the zoom level based on level of detail (LOD)
			    center: [-58, -15]  // Sets the center point of view in lon/lat
			  });
		});

		document.body.appendChild(div);

		$.ajax({
      		url:'municipios.php',
      		complete: function (response) {
         		fncMunicipios(response.responseText);
      		},
      		error: function () {
          		alert('Error');
     		}
  		});

	}else if(slc==2 ){

		//alert( document.getElementById("viewDiv") +" "+ document.getElementById("div") );
		//var body = document.getElementById("body//");

		if(document.getElementById("viewDiv") != null){
			document.body.removeChild(document.getElementById("viewDiv"));
			document.body.removeChild(document.getElementById("div"));
			var div = document.createElement("div");
			div.setAttribute("id", "viewDiv");
		}else{
			var div = document.createElement("div");
			div.setAttribute("id", "viewDiv");
		}

		// Gera o mapa
  		require([
			  "esri/Map",
			  "esri/views/MapView", // SceneView
			  "esri/layers/Layer",
			  "dojo/domReady!"
			], function(Map, MapView, Layer) {
			  var map = new Map({
			    basemap: "gray" // streets, topo, satellite, gray
			  });

			  var view = new MapView({
			    container: "viewDiv",  // Reference to the DOM node that will contain the view
			    map: map,               // References the map object created in step 3
			    zoom: 4,  // Sets the zoom level based on level of detail (LOD)
			    center: [-58, -15]  // Sets the center point of view in lon/lat
			  });
/*
			  Layer.fromPortalItem({
	        	portalItem: { // autocast as esri/portal/PortalItem
	          		id: "4c5c95c1c3a244f99ec24390871cc8e8"
	       		}
	          }).then(addLayer).otherwise(rejection);

	          // Adds the layer to the map once it loads
		      function addLayer(lyr) {
		        map.add(lyr);
		        alert("Layer added");
		      }

		      function rejection(err) {
		        alert("Layer failed to load: " + err);
		      }
		      */

		});

		document.body.appendChild(div);

		// comando Ajax (JQuery) chama funcao php 
		$.ajax({
      		url:'backbonefibra_bd.php',
      		complete: function (response) {
         		fncBackboneFibra(response.responseText);
      		},
      		error: function () {
          		alert('Error');
     		}
  		});
	}else{
		// $("#viewDiv").hide() $("#viewDiv").show()
		if(document.getElementById("viewDiv") != null){ 
			document.body.removeChild(document.getElementById("viewDiv"));
			//document.getElementById("viewDiv").style.display = "none";
			document.body.removeChild(document.getElementById("div"));

		}
	}


} // end fncSelect

function fncBackboneFibra(response){

	var body = document.getElementById("body");

	// verifica se algum elemento foi criado antes
	if(document.getElementById("div") != null){
		body.removeChild(document.getElementById("div"));
		//body.removeChild(document.getElementById("para"));
	}

	var res = response.split("#");
    var sz = res.length;
    var i=0;

    // cria elementos html
    var table = document.createElement("table");
    var tbody = document.createElement("tbody");
    var center = document.createElement("center");
    var div = document.createElement("div");
    var br = document.createElement("p");

    // atribui id aos elementos criados
    center.setAttribute('id', 'center');
    table.setAttribute('id','table');
    div.setAttribute('id','div');

    // total de resultados
    div.appendChild(br);
    var p = document.createElement("p");
    p.setAttribute('id','para');
    p.appendChild( document.createTextNode("Total de resultados: " + res[sz-1]) );
    div.appendChild(p);
    div.appendChild(br);

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
    center.appendChild(table);
    div.appendChild(center);
    div.appendChild(br);

    body.appendChild(div);
}

function fncMunicipios(response){

	var body = document.getElementById("body");

	// verifica se algum elemento foi criado antes
	if(document.getElementById("div") != null){
		body.removeChild(document.getElementById("div"));
	}

	var res = response.split("#");
    var sz = res.length;
    var i=0;

    // cria elementos html
    var table = document.createElement("table");
    var tbody = document.createElement("tbody");
    var center = document.createElement("center");
    var div = document.createElement("div");
    var br = document.createElement("p");

    // atribui id aos elementos criados
    center.setAttribute('id', 'center');
    table.setAttribute('id','table');
    div.setAttribute('id','div');

    // total de resultados
    div.appendChild(br);
    var p = document.createElement("p");
    p.setAttribute('id','para');
    p.appendChild( document.createTextNode("Total de resultados: " + res[sz-1]) );
    div.appendChild(p);
    div.appendChild(br);

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
    center.appendChild(table);
    div.appendChild(center);
    div.appendChild(br);

    body.appendChild(div);

}