<?php
/*PhpDoc:
name: geojson.php
title: geojson.php - Expose en GeoJSON les barrages
doc: |
  Prend en paramètre file le nom du fichier CSV dans data
journal: |
  1/2/2020:
    - ajout du paramètre file pour exposer différents fichiers CSV
*/

$features = [];

if (!isset($_GET['file'])) {
  header('HTTP/1.1 400 Bad Request');
  die("Erreur paramètre file absent");
}
if (!($file = fopen(__DIR__."/data/$_GET[file]",'r'))) {
  header('HTTP/1.1 400 Bad Request');
  die("Erreur ouverture du fichier $_GET[file]");
}

$header = fgetcsv($file, 1024, ';', '"');
while ($record = fgetcsv($file, 1024, ';', '"')) {
  foreach ($header as $i => $k)
    $rec[$k] = $record[$i];
  $coord = [floatval(str_replace(',','.',$rec['Lon'])), floatval(str_replace(',','.',$rec['Lat']))];
  $features[] = [
    'type'=> 'Feature',
    'href'=> "<a href='chart.php?num=$rec[Code]'><b>$rec[Nom]</b></a>",
    'properties'=> $rec,
    'geometry'=> [
      'type'=> 'Point',
      'coordinates'=> $coord,
    ],
  ];
}
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) { // si exécuté comme script
  header('Content-Type: application/json');
  echo json_encode(['type'=>'FeatureCollection', 'features'=>$features],  JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
  die();
}
else { // sino peut être éxécuté en inclusion dans un php
  return ['type'=>'FeatureCollection', 'features'=>$features];
}

