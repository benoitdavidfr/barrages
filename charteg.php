<html>
<head>
<title>FusionCharts | My First Chart</title>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>
  
<?php
/*PhpDoc:
name: charteg.php
title: charteg.php - exemple de graphique utilisant fusioncharts issu de la doc
doc: |
journal: |
*/

include __DIR__."/fusioncharts/fusioncharts.php";

// Chart Def
$chartDef = [
  "chart" => [
    "caption" => "Countries With Most Oil Reserves [2017-18]",
    "subCaption" => "In MMbbl = One Million barrels",
    "xAxisName" => "Country",
    "yAxisName" => "Reserves (MMbbl)",
    "numberSuffix" => "K",
    "theme" => "fusion"
  ],
  'data'=> [],
];

// An array of hash objects which stores data
$data = [
  ["Venezuela", "290"],
  ["Saudi", "260"],
  ["Canada", "180"],
  ["Iran", "140"],
  ["Russia", "115"],
  ["UAE", "100"],
  ["US", "30"],
  ["China", "30"]
];

// Pushing labels and values
foreach ($data as $record) {
  $chartDef["data"][] = ["label" => $record[0], "value" => $record[1]];
}

// chart object
$chart = new FusionCharts("column2d", "MyFirstChart" , "700", "400", "chart-container", "json",
  json_encode($chartDef));

// Render the chart
$chart->render();
?>
    <center>
        <div id="chart-container">Chart will render here!</div>
    </center>
</body>
</html>