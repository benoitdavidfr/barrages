<?php
/*PhpDoc:
name: chart2.php
title: chart2.php - définition JSON du graphique associé à un barrage
doc:
  En premier paramètre obligatoire latlng est le couple de coordonnées géo. du barrage
  En second paramètre optionnel chart le type de graphique
    cote : évolution de la cote en fonction de la date
    volume : évolution du volume en fonction de la date
    surface : évolution de la surface en fonction de la date
    MultiAxisLine : évolution des 3 variables en fonction de la date
    scatter : relation entre volume et côte
  Le script renvoie soit une erreur soit la définition du graphique en JSON.
  Dans un premier temps seules les données de la retenue d'Astarac sont disponibles.
  On considère que le fichier des données contient:
   - en première ligne le nom du barrage.
   - en seconde ligne les en-têtes des colonnes
   - les données dans les autres lignes
  Pour faire les graphiques, utilisation de fusioncharts-suite-xt (https://www.fusioncharts.com/charts#fusioncharts)
journal: |
  9-10/2/2020:
    génération JSON de la définition du graphique
*/

// calcul de la distance entre 2 points définis en coord. géo.
function dist(array $a, array $b): float {
  //print_r($a); print_r($b);
  return sqrt(($a[0]-$b[0]) ** 2 + ($a[1]-$b[1]) ** 2);
}

// trouve le nom du barrage à partir de ses coordonnées
function nomBarrage(array $latlng): string {
  $nom = null;
  $distMin = -1;

  if (!($file = fopen(__DIR__."/data/retenues-20200121-Occitanie.csv",'r'))) {
    header('HTTP/1.1 400 Bad Request');
    die("Erreur ouverture du fichier retenues-20200121-Occitanie.csv");
  }

  $header = fgetcsv($file, 1024, ';', '"');
  while ($record = fgetcsv($file, 1024, ';', '"')) {
    foreach ($header as $i => $k)
      $rec[$k] = $record[$i];
    //print_r($rec);
    if (!is_numeric(str_replace(',','.',$rec['Lat'])) || !is_numeric(str_replace(',','.',$rec['Lon'])))
      continue;
    $d = dist($latlng, [str_replace(',','.',$rec['Lat']), str_replace(',','.',$rec['Lon'])]);
    if (($distMin == -1) || ($d < $distMin)) {
      $nom = $rec['Nom'];
      $distMin = $d;
    }
  }
  return $nom;
}
  
if (!isset($_GET['latlng'])) {
  header('HTTP/1.1 400 Bad Request');
  die("Erreur paramètre latlng non défini");
}
$latlng = explode(',', $_GET['latlng']);

//$nom = 'Astarac';
$nom = nomBarrage($latlng);

if (!($file = @fopen(__DIR__."/data/retenue-$nom.csv",'r'))) {
  header('HTTP/1.1 404 Not Found');
  die(json_encode(['error'=> "Erreur ouverture du fichier retenue-$nom.csv"]));
}

$first = fgetcsv($file, 1024, ';', '"'); // ligne avec le nom du barrage
$bLabel = $first[0];
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

$chart = (isset($_GET['chart']) ? $_GET['chart'] : null);
if (in_array($chart, ['cote','volume','surface'])) { // graphique de la côte, du volume ou de la surface du bassin
  $yAxisNames = [
    'cote'=> "Côte du bassin (m)",
    'volume'=> "Volume (Mm3)",
    'surface'=> "Surface (ha)",
  ];
  // Chart Def
  $chartDef = [
    "chart" => [
      "caption" => $bLabel,
      //"subCaption" => "Last week",
      "xAxisName" => "Date",
      "yAxisName" => $yAxisNames[$chart],
      "lineThickness" => "2",
      "setadaptiveymin"=> "1",
      "theme" => "fusion"
    ],
    'data'=> [],
    'trendlines'=> [[
      'line' => [[
        'startvalue'=> 'A DEFINIR',
        "color"=> "#1aaf5d",
        "displayvalue"=> "Moyenne",
        "valueOnRight"=> "1",
        "thickness"=> "2"
      ]]
    ]],
  ];

  $sum = 0;
  // Pushing labels and values
  foreach ($mesures as $label => $values) {
    $chartDef["data"][] = ["label" => $label, "value" => $values[0]];
    $sum += $values[0];
  }

  $chartDef['trendlines'][0]['line'][0]['startvalue'] = $sum/count($mesures);
}
elseif ($chart == 'scatter') { // volume / cote
  // Chart Def
  $chartDef = [
    "chart" => [
      "caption" => $bLabel,
      "subCaption" => "Volume vs. côte",
      "xAxisName" => "Côte du bassin (m)",
      "xnumbersuffix"=> "m",
      "yAxisName" => "Volume (Mm3)",
      "ynumbersuffix"=> "Mm3",
      "lineThickness" => "2",
      "plottooltext"=> 'volume de <b>$yDataValue</b> Mm3<br>pour côte <b>$xDataValue</b> m',
      "theme" => "fusion"
    ],
    'dataset'=> [[
      "seriesname"=> "Volume vs. côte",
      "anchorbgcolor"=> "5D62B5",
      'data'=> [], // [['x'=> x, 'y'=> y]]
    ]]
  ];

  foreach ($mesures as $label => $values) {
    $chartDef['dataset'][0]["data"][] = ["x" => $values[0], "y" => $values[1]];
  }
}
else { // graphique avec les 3 variables
  // Chart Def
  $chartDef = [
    "chart" => [
      "caption" => $bLabel,
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
}

header('Content-type: application/json');
die(json_encode($chartDef));
