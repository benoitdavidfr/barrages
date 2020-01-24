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
    "caption" => "Total footfall in Bakersfield Central",
    "subCaption" => "Last week",
    "xAxisName" => "Day",
    "yAxisName" => "No. of Visitors",
    "lineThickness" => "2",
    "theme" => "fusion"
  ],
  'data'=> [],
  'trendlines'=> [[
    'line' => [[
      'startvalue'=> "18500",
      "color"=> "#1aaf5d",
      "displayvalue"=> "Average{br}weekly{br}footfall",
      "valueOnRight"=> "1",
      "thickness"=> "2"
    ]]
  ]],
];

// An array of hash objects which stores data
$data = [
  ["Mon", "15123"],
  ["Tue", "14233"],
  ["Wed", "23507"],
  ["Thu",  "9110"],
  ["Fri", "15529"],
  ["Sat", "20803"],
  ["Sun", "19202"],
];

// Pushing labels and values
foreach ($data as $record) {
  $chartDef["data"][] = ["label" => $record[0], "value" => floatval($record[1])];
}

// chart object
$chart = new FusionCharts("line", "MyFirstChart" , "700", "400", "chart-container", "json",
  json_encode($chartDef));

// Render the chart
$chart->render();
?>
    <center>
        <div id="chart-container">Chart will render here!</div>
    </center>
<?php
echo '<pre>',json_encode($chartDef, JSON_PRETTY_PRINT),'</pre>';
?>
</body>
</html>
