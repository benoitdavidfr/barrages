<?php
/*PhpDoc:
name: map2.php
title: map2.php - Dessine la carte et les graphiques dans la même fenêtre
doc: |
  Prend en paramètres:
    - bbox rectangle englobant [ lonmin,latmin,lonmax,latmax ] à afficher (optionnel)
    - lon,lat,level définissant le centre de l'affichage et le niveau de zoom
    - chart défini le type de graphique affiché
      - cote, volume, surface pour afficher une variable dans un graphique de type line
      - MultiAxisLine pour afficher les 3 variables dans un graphique de type MultiAxisLine
      - scatter pour afficher le volume en fonction de la côte dans un graphique de type scatter
  Affichage en 2 <div>:
    - 'map' pour la carte
    - 'chart-container' pour les graphiques
  Un clic sur un barrage de la couche des barrages pour validation affiche le graphique correspondant
  au barrage dans la <div> 'chart-container'
  Appel de chart2.php paramétré par les coords latlng du barrage génère la définition du graphique en JSON
  à partir du fichier CSV.
  Les variables Php utilisées dans le code Javascript sont listées au début du code Javascript.
journal: |
  7-11/2/2020:
    création
*/

function level(array $bbox): int {
  $dll = max($bbox[2]-$bbox[0], ($bbox[3]-$bbox[1])/cos(($bbox[2]+$bbox[0])/2/180*pi()));
  if ($dll < 1e-3)
    $level = 18;
  elseif ($dll < 1e-2)
    $level = 17;
  elseif ($dll < 2e-2)
    $level = 16;
  elseif ($dll < 5e-2)
    $level = 15; // ok
  elseif ($dll < 0.1)
    $level = 14;
  elseif ($dll < 0.2)
    $level = 13; // ok
  elseif ($dll < 0.5)
    $level = 11;
  elseif ($dll < 1)
    $level = 10; // ok
  elseif ($dll < 2)
    $level = 9;
  elseif ($dll < 5)
    $level = 8;
  elseif ($dll < 10)
    $level = 6;
  elseif ($dll < 20)
    $level = 5;
  elseif ($dll < 40)
    $level = 4;
  else
    $level = 3;
  //echo "dll=$dll -> level=$level<br>\n";
  return $level;
}

if (isset($_GET['bbox'])) { // 
  $bbox = explode(',', $_GET['bbox']);
  //print_r($bbox); die();
  $lon = ($bbox[0]+$bbox[2])/2;
  $lat = ($bbox[1]+$bbox[3])/2;
  $level = level($bbox); // variable utilisée dans le code JavaScript pour définir la vue
}
elseif (isset($_GET['lon']) && isset($_GET['lat']) && isset($_GET['level'])) {
  $lon = floatval($_GET['lon']);
  $lat = floatval($_GET['lat']);
  $level = intval($_GET['level']); // variable utilisée dans le code JavaScript pour définir la vue
}
else  {
  $lon = 1;
  $lat = 45.5;
  $level = 6; // variable utilisée dans le code JavaScript pour définir la vue
}
$center = json_encode([$lat, $lon]); // variable utilisée dans le code JavaScript pour définir la vue

//echo "<pre>"; print_r($_SERVER); die;
$path = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? 'https://' : 'http://')
  .$_SERVER['SERVER_NAME']
  .dirname($_SERVER['PHP_SELF'])
    ."/chart2.php?"
  .(isset($_GET['chart']) ? "chart=$_GET[chart]&" : '');
//echo "path=$path<br>\n";
$chart = (isset($_GET['chart']) ? $_GET['chart'] : null);
if ($chart == 'scatter') {
  $chartType = 'scatter'; // variable utilisée dans le code Javascript pour définir le type de graphique
}
elseif (in_array($chart, ['cote','volume','surface'])) {
  $chartType = 'line';
}
else {
  $chartType = 'MultiAxisLine';
}

$geojsonLayers = [ // liste des couches GeoJSON, variable utilisée dans le code JS
  "Barrages extrait pour validation" => [
    'path' => "geojson.php?file=retenues-20200121-Occitanie.csv",
    'markerOptions' => [
      'radius'=> 8,
      'fillColor'=> "darkGreen",
      'color'=> "#000",
      'weight'=> 1,
      'opacity'=> 1,
      'fillOpacity'=> 0.8,
    ],
    'visibleByDefault' => true,
    'graph'=> true,
  ],
  "Barrages 15m France entière" => [
    'path' => "geojson.php?file=Barrages_15m_France_20200131.csv",
    'markerOptions' => [
      'radius'=> 8,
      'fillColor'=> "darkBlue",
      'color'=> "#000",
      'weight'=> 1,
      'opacity'=> 1,
      'fillOpacity'=> 0.8,
    ],
  ],
  "Barrages 15m Occitanie et Nelle Aquitaine" => [
    'path' => "geojson.php?file=Barrages_15m_Occitanie_NlAq_20200131.csv",
    'markerOptions' => [
      'radius'=> 8,
      'fillColor'=> "#ff7800",
      'color'=> "#000",
      'weight'=> 1,
      'opacity'=> 1,
      'fillOpacity'=> 0.8,
    ],
  ],
];

?>

<!DOCTYPE HTML><html>
  <!-- carte simple utilisant les clés choisirgeoportail -->
  <!-- code utilisant les variables Php $center $level $geojsonLayers $chartType -->
  <head>
    <title>carte</title>
    <meta charset="UTF-8">
<!-- meta nécessaire pour le mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<!-- styles nécessaires pour le mobile -->
    <link rel="stylesheet" href="https://visu.gexplor.fr/viewer.css">
<!-- styles et src de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.6/dist/leaflet.js"></script>
<!-- plug-in d'appel des GeoJSON en AJAX -->
    <script src='lib/leaflet/leaflet-ajax.js'></script>
<!-- Include fusioncharts core library -->
    <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<!-- Include fusion theme -->
    <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
  </head>
  <body>
    <div id="map" style="height: 50%; width: 100%"></div>
    <center>
        <div id="chart-container" style="height: 50%; width: 100%">
          </p>
          Cliquer sur un barrage de la couche "Barrages extrait pour validation"
          pour afficher le graphique correspondant.<br>
          Dans cette version seul le graphique du barrage de l'Astarac est disponible.
        </div>
    </center>
    <script>
var map = L.map('map').setView(<?php echo $center; ?>, <?php echo $level; ?>); // view pour la zone
L.control.scale({position:'bottomleft', metric:true, imperial:false}).addTo(map);

var wmtsurl = 'https://wxs.ign.fr/choisirgeoportail/geoportail/wmts?'
            + 'service=WMTS&version=1.0.0&request=GetTile&tilematrixSet=PM&height=256&width=256&'
            + 'tilematrix={z}&tilecol={x}&tilerow={y}';
var attrIGN = "&copy; <a href='http://www.ign.fr'>IGN</a>";

// génère le graphique en utilisant FusionCharts à partir des données passes en paramètre
function genChart(chartDataSource) {
  if (chartDataSource.error) {
    alert(chartDataSource.error);
    return;
  }
  const chartConfig = {
    type: '<?php echo $chartType;?>',
    renderAt: 'chart-container',
    width: '100%',
    height: '400',
    dataFormat: 'json',
    dataSource: chartDataSource
  };
  FusionCharts.ready(function(){
    var fusioncharts = new FusionCharts(chartConfig);
    fusioncharts.render();
  });
}

// lorsque le clic est généré, appel de chart2.php avec le bon chemin et les coord. du barrage en paramètre
// puis si ok appelle genChart() avc les données ainsi récupérées
function onLayerClick(e) {
  fetch("<?php echo $path;?>latlng="+e.latlng.lat+','+e.latlng.lng)
  .then(response => response.json())
  .then(response => genChart(response))
  .catch(error => alert("Erreur : " + error));
}

// associe au click le traitement onLayerClick() + associe un ToolTip à la couche
var onEachFeatureGraph = function (feature, layer) {
  layer.on('click', onLayerClick);
  if (feature.properties.Nom)
    layer.bindTooltip(feature.properties.Nom);
};

// associe à la couche un popup + un ToolTip
var onEachFeature = function (feature, layer) {
  layer.bindPopup('<pre>'+JSON.stringify(feature.properties,null,' ')+'</pre>');
  if (feature.properties.Nom)
    layer.bindTooltip(feature.properties.Nom);
};

// liste des couches de fond
var baseLayers = {
  "Plan IGN V2" : new L.TileLayer(
      wmtsurl + '&layer=GEOGRAPHICALGRIDSYSTEMS.PLANIGNV2&format=image/png&style=normal',
      {"format":"image/png","minZoom":0,"maxZoom":18,"attribution":attrIGN}
  ),
  "Plan IGN V1" : new L.TileLayer(
      wmtsurl + '&layer=GEOGRAPHICALGRIDSYSTEMS.PLANIGN&format=image/jpeg&style=normal',
      {"format":"image/jpeg","minZoom":0,"maxZoom":18,"attribution":attrIGN}
  ),
  "ScanExpress" : new L.TileLayer(
      wmtsurl + '&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS.SCAN-EXPRESS.STANDARD&format=image/jpeg&style=normal',
      {"format":"image/jpeg","minZoom":0,"maxZoom":18,"attribution":attrIGN}
  ),
  "Cartes IGN classiques" : new L.TileLayer(
      wmtsurl + '&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS&format=image/jpeg&style=normal',
      {"format":"image/jpeg","minZoom":0,"maxZoom":18,"attribution":attrIGN}
  ),
  "OSM" : new L.TileLayer(
      'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
      { "format":"image/png", "minZoom":0,"maxZoom":19, "detectRetina": false,
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'}
   ),
   "Ortho-Photos" : new L.TileLayer(
      wmtsurl + '&layer=ORTHOIMAGERY.ORTHOPHOTOS&format=image/jpeg&style=normal',
      {"format":"image/jpeg" ,"minZoom":0, "maxZoom":20, "attribution":attrIGN}
  ),
  "Altitude" : new L.TileLayer(
      wmtsurl + '&layer=ELEVATION.SLOPES&format=image/jpeg&style=normal',
      {"format":"image/jpeg", "minZoom":6, "maxZoom":14, "attribution":attrIGN}
  ),
};

// liste des calques
var overlays = {
  "Hydrographie" : new L.TileLayer(
    'http://igngp.geoapi.fr/tile.php/hydrographie/{z}/{x}/{y}.png',
    { format: 'image/png', minZoom: 6, maxZoom: 18, detectRetina: true,
      attribution: attrIGN
    }
  ),
  /*"Parcelles cadastrales (orange)" : new L.TileLayer(
      wmtsurl + '&layer=CADASTRALPARCELS.PARCELS&format=image/png&style=bdparcellaire_o',
      {"format":"image/png","minZoom":0,"maxZoom":20,"attribution":attrIGN}
  ),
  "BD Uni j+1" : new L.TileLayer(
      wmtsurl + '&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS.BDUNI.J1&format=image/png&style=normal',
      {"format":"image/png","minZoom":0,"maxZoom":20,"attribution":attrIGN}
  ),*/
<?php
  // affichage des couches GeoJSON définies dans la variable Php $geojsonLayers
  foreach ($geojsonLayers as $title => $layer) {
    $markerOptions = json_encode($layer['markerOptions']);
    $onEachFeature = (isset($layer['graph']) && $layer['graph']) ? 'onEachFeatureGraph' : 'onEachFeature';
    echo <<<EOT
  '$title' : new L.GeoJSON.AJAX(
    '$layer[path]',
    { pointToLayer: function (feature, latlng) { return L.circleMarker(latlng, $markerOptions); },
      minZoom: 0, maxZoom: 21,
      onEachFeature: $onEachFeature
    }
  ),

EOT;
  }
?>
  "Dénominations géographiques" : new L.TileLayer(
    'http://igngp.geoapi.fr/tile.php/toponymes/{z}/{x}/{y}.png',
    { format: 'image/png', minZoom: 6, maxZoom: 18, detectRetina: false,
      attribution: attrIGN
    }
  ),
};

map.addLayer(baseLayers["Altitude"]);
map.addLayer(overlays["Hydrographie"]);
<?php
foreach ($geojsonLayers as $title => $layer) {
  if (isset($layer['visibleByDefault']) && $layer['visibleByDefault'])
    echo "map.addLayer(overlays['$title']);\n";
}
?>
map.addLayer(overlays["Dénominations géographiques"]);

L.control.layers(baseLayers, overlays).addTo(map);

      </script>
    </body>
</html>
