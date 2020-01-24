<?php
/*PhpDoc:
name: geojson.php
title: geojson.php - Expose en GeoJSON les barrages

*/

$features = [];

if (!($file = fopen(__DIR__."/data/retenues-20200121-Occitanie.csv",'r')))
  die("Erreur ouverture du fichier retenues-20200121-Occitanie.csv");

$header = fgetcsv($file, 1024, ';', '"');
while ($record = fgetcsv($file, 1024, ';', '"')) {
  foreach ($header as $i => $k)
    $rec[$k] = $record[$i];
  $coord = [floatval(str_replace(',','.',$rec['Lon'])), floatval(str_replace(',','.',$rec['Lat']))];
  $features[] = [
    'type'=> 'Feature',
    'href'=> "<a href='fiche.php?num=$rec[Num]'><b>$rec[Nom]</b></a>",
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

