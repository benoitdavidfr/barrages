<?php
/*PhpDoc:
name: fiche.php
title: fiche.php - fiche affichée par barrage

*/

if (!isset($_GET['num']))
  die("num non défini");

if (!($file = fopen(__DIR__."/data/retenues-20200121-Occitanie.csv",'r')))
  die("Erreur ouverture du fichier retenues-20200121-Occitanie.csv");

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
?>

<html>
<head>
<title>Line Chart</title>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>
  
<?php
include __DIR__."/fusioncharts/fusioncharts.php";

// Chart Def
$chartDef = [
  "chart" => [
    "caption" => "Barrage de l'Astarac",
    //"subCaption" => "Last week",
    "xAxisName" => "Date",
    "yAxisName" => "Côte du bassin (m)",
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
  
// chart object
$chart = new FusionCharts("line", "MyFirstChart" , "100%", "600", "chart-container", "json",
  json_encode($chartDef));

// Render the chart
$chart->render();
?>
    <center>
        <div id="chart-container">Chart will render here!</div>
    </center>
<?php
//echo '<pre>',json_encode($chartDef, JSON_PRETTY_PRINT),'</pre>';
?>
</body>
</html>
