<?php
/*PhpDoc:
name: chart2.php
title: chart2.php - graphique des données associées à un barrage
doc:
  En premier paramètre obligatoire latlng est le couple de coordonnées géo. du barrage
  Dans un premier temps seules les données de la retenue d'Astarac sont disponibles.
  On considère que le fichier des données contient:
   - en première ligne le nom du barrage.
   - en seconde ligne les en-têtes des colonnes
   - les données dans les autres lignes
  Pour faire les graphiques, utilisation de fusioncharts-suite-xt (https://www.fusioncharts.com/charts#fusioncharts)
journal: |
  9/2/2020:
    génération JSON de la définition du graphique
*/

/*
if (!isset($_GET['num']))
  die("num non défini");

if (!($file = fopen(__DIR__."/data/retenues-20200121-Occitanie.csv",'r')))
  die("Erreur ouverture du fichier retenues-20200121-Occitanie.csv");
*/

/*$nom = null;
$header = fgetcsv($file, 1024, ';', '"');
while ($record = fgetcsv($file, 1024, ';', '"')) {
  foreach ($header as $i => $k)
    $rec[$k] = $record[$i];
  //print_r($rec);
  if ($rec['Num'] == $_GET['num']) {
    $nom = $rec['Nom'];
    break;
  }
}

if (!$nom) {
  die("Erreur le numéro $_GET[num] ne correspond à aucun barrage dans retenues-20200121-Occitanie.csv");
}
*/
$nom = 'Astarac';

if (!($file = fopen(__DIR__."/data/retenue-$nom.csv",'r')))
  die("Erreur ouverture du fichier retenue-$nom.csv");

fgetcsv($file, 1024, ';', '"'); // ligne avec le nom du barrage
$header = fgetcsv($file, 1024, ';', '"');

$mesures = []; // [ date (jj/mm/aaaa) => [côte (m), volume (Mm3), surface (ha)]]
while ($record = fgetcsv($file, 1024, ';', '"')) {
  $mesures[$record[0]] = [
    floatval(str_replace(',','.',$record[1])),
    floatval(str_replace(',','.',$record[2])),
    floatval(str_replace(',','.',$record[3])),
  ];
  
}

//echo "<pre>mesures=";
//print_r($mesures);

include __DIR__."/fusioncharts/fusioncharts.php";

// Chart Def
$chartDef = [
  "chart" => [
    "caption" => "Barrage de l'Astarac",
    //"subCaption" => "Last week",
    "xAxisName" => "Date",
    "theme" => "fusion"
  ],
  'categories'=> [[
    'category'=> [], // [['label'=> label]]
  ]],
  'axis'=> [
    [ 'title'=> "Côte du bassin (m)",
      'numbersuffix'=> "m",
      'dataset'=> [[
        'seriesname'=> 'cote',
        'data'=> [], // [['value'=> value]]
      ]],
    ],
    [ 'title'=> "Volume (Mm3)",
      'numbersuffix'=> "Mm3",
      'dataset'=> [[
        'seriesname'=> 'volume',
        'data'=> [], // [['value'=> value]]
      ]],
    ],
    [ 'title'=> "Surface (ha)",
      'numbersuffix'=> "ha",
      'dataset'=> [[
        'seriesname'=> 'surface',
        'data'=> [], // [['value'=> value]]
      ]],
    ]
  ]
];

// Pushing labels and values
$count = 0;
foreach ($mesures as $label => $values) {
  $chartDef['categories'][0]['category'][] = ["label" => $label];
  $chartDef['axis'][0]['dataset'][0]['data'][] = ["value" => $values[0]];
  $chartDef['axis'][1]['dataset'][0]['data'][] = ["value" => $values[1]];
  $chartDef['axis'][2]['dataset'][0]['data'][] = ["value" => $values[2]];
  //if (++$count >= 5) break;
}

header('Content-type: application/json');
die(json_encode($chartDef));
