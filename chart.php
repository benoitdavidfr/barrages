<?php
/*PhpDoc:
name: chart.php
title: chart.php - graphique des données associées à un barrage
doc:
  En premier paramètre obligatoire num le numéro du barrage.
  En second paramètre optionnel chart le type de graphique
    cote : évolution de la cote en fonction de la date
    volume : évolution du volume en fonction de la date
    surface : évolution de la surface en fonction de la date
    MultiAxisLine : évolution des 3 variables en fonction de la date
    scatter : relation entre volume et côte
  On utilise le nom défini dans le fichier des barrages pour fabriquer le nom du fichier CSV contenant les observations
  Dans un premier temps seules les données de la retenue d'Astarac sont disponibles.
  On considère que le fichier des données contient:
   - en première ligne le nom du barrage.
   - en seconde ligne les en-têtes des colonnes
   - les données dans les autres lignes
  Pour faire les graphiques, utilisation de fusioncharts-suite-xt (https://www.fusioncharts.com/charts#fusioncharts)
journal: |
  25/1/2020:
    - amélioration des graphiques
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


echo "<!DOCTYPE HTML><html><head><title>graphique $nom</title>\n";
?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
</head><body>
  
<?php
include __DIR__."/fusioncharts/fusioncharts.php";

$chart = (isset($_GET['chart']) ? $_GET['chart'] : 'MultiAxisLine');
if (in_array($chart, ['cote','volume','surface'])) { // graphique de la côte du bassin
  $yAxisNames = [
    'cote'=> "Côte du bassin (m)",
    'volume'=> "Volume (Mm3)",
    'surface'=> "Surface (ha)",
  ];
  // Chart Def
  $chartDef = [
    "chart" => [
      "caption" => "Barrage de l'Astarac",
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
  
  // chart object
  $chart = new FusionCharts("line", "MyFirstChart" , "100%", "600", "chart-container", "json",
    json_encode($chartDef));
}
elseif ($chart == 'MultiAxisLine') { // graphique avec les 3 variables
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

  /* Modèle
  {
      "chart": {
          "caption": "CPU Usage",
          "subcaption": "(Last 10 Hours)",
          "xaxisname": "Time",
          "numvdivlines": "4",
          "vdivlinealpha": "0",
          "alternatevgridalpha": "5",
          "labeldisplay": "ROTATE",
          "theme": "fusion"
      },
      "categories": [
          {
              "category": [
                  {
                      "label": "10:00"
                  },
                  {
                      "label": "11:00"
                  },
                  {
                      "label": "12:00"
                  },
                  {
                      "label": "13:00"
                  },
                  {
                      "label": "14:00"
                  },
                  {
                      "label": "15:00"
                  },
                  {
                      "label": "16:00"
                  },
                  {
                      "label": "17:00"
                  },
                  {
                      "label": "18:00"
                  },
                  {
                      "label": "19:00"
                  }
              ]
          }
      ],
      "axis": [
          {
              "title": "CPU Usage",
              "tickwidth": "10",
              "divlineDashed": "1",
              "numbersuffix": "%",
              "dataset": [
                  {
                      "seriesname": "CPU 1",
                      "linethickness": "3",
                      "color": "CC0000",
                      "data": [
                          {
                              "value": "16"
                          },
                          {
                              "value": "19"
                          },
                          {
                              "value": "16"
                          },
                          {
                              "value": "17"
                          },
                          {
                              "value": "23"
                          },
                          {
                              "value": "23"
                          },
                          {
                              "value": "15"
                          },
                          {
                              "value": "14"
                          },
                          {
                              "value": "19"
                          },
                          {
                              "value": "21"
                          }
                      ]
                  },
                  {
                      "seriesname": "CPU 2",
                      "linethickness": "3",
                      "color": "0372AB",
                      "data": [
                          {
                              "value": "12"
                          },
                          {
                              "value": "12"
                          },
                          {
                              "value": "9"
                          },
                          {
                              "value": "9"
                          },
                          {
                              "value": "11"
                          },
                          {
                              "value": "13"
                          },
                          {
                              "value": "16"
                          },
                          {
                              "value": "14"
                          },
                          {
                              "value": "16"
                          },
                          {
                              "value": "11"
                          }
                      ]
                  }
              ]
          },
          {
              "title": "PF Usage",
              "axisonleft": "0",
              "numdivlines": "4",
              "tickwidth": "10",
              "divlineDashed": "1",
              "formatnumberscale": "1",
              "defaultnumberscale": " MB",
              "numberscaleunit": "GB",
              "numberscalevalue": "1024",
              "dataset": [
                  {
                      "seriesname": "PF Usage",
                      "data": [
                          {
                              "value": "696"
                          },
                          {
                              "value": "711"
                          },
                          {
                              "value": "636"
                          },
                          {
                              "value": "671"
                          },
                          {
                              "value": "1293"
                          },
                          {
                              "value": "789"
                          },
                          {
                              "value": "793"
                          },
                          {
                              "value": "993"
                          },
                          {
                              "value": "657"
                          },
                          {
                              "value": "693"
                          }
                      ]
                  }
              ]
          },
          {
              "title": "Processes",
              "axisonleft": "0",
              "numdivlines": "5",
              "tickwidth": "10",
              "divlineDashed": "1",
              "dataset": [
                  {
                      "seriesname": "Processes",
                      "data": [
                          {
                              "value": "543"
                          },
                          {
                              "value": "511"
                          },
                          {
                              "value": "536"
                          },
                          {
                              "value": "449"
                          },
                          {
                              "value": "668"
                          },
                          {
                              "value": "588"
                          },
                          {
                              "value": "511"
                          },
                          {
                              "value": "536"
                          },
                          {
                              "value": "449"
                          },
                          {
                              "value": "668"
                          }
                      ]
                  }
              ]
          }
      ]
  }
  */
  
  // Pushing labels and values
  $count = 0;
  foreach ($mesures as $label => $values) {
    $chartDef['categories'][0]['category'][] = ["label" => $label];
    $chartDef['axis'][0]['dataset'][0]['data'][] = ["value" => $values[0]];
    $chartDef['axis'][1]['dataset'][0]['data'][] = ["value" => $values[1]];
    $chartDef['axis'][2]['dataset'][0]['data'][] = ["value" => $values[2]];
    //if (++$count >= 5) break;
  }
  
  // chart object
  $chart = new FusionCharts("MultiAxisLine", "MyFirstChart" , "100%", "600", "chart-container", "json",
    json_encode($chartDef));
}
elseif ($chart == 'scatter') { // volume / cote
  // Chart Def
  $chartDef = [
    "chart" => [
      "caption" => "Barrage de l'Astarac",
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
  
  // chart object
  $chart = new FusionCharts("scatter", "MyFirstChart" , "100%", "600", "chart-container", "json",
    json_encode($chartDef));
}
else {
  die("Graphique $chart inconnu");
}

// Render the chart
$chart->render();
?>
    <center>
        <div id="chart-container">Chart will render here!</div>
    </center>
    
<?php
echo "Graphique <a href='?num=$_GET[num]&amp;chart=cote'>côte</a>, ",
  "<a href='?num=$_GET[num]&amp;chart=volume'>volume</a>, ",
  "<a href='?num=$_GET[num]&amp;chart=surface'>surface</a>, ",
  "<a href='?num=$_GET[num]&amp;chart=MultiAxisLine'>3 variables</a>, ",
  "<a href='?num=$_GET[num]&amp;chart=scatter'>relation entre volume et côte</a><br>\n"
//echo '<pre>',json_encode($chartDef, JSON_PRETTY_PRINT),'</pre>';
?>
</body>
</html>
