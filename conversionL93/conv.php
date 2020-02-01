<?php
/*PhpDoc:
name: conv.php
title: conv.php - conversion du fichier CSV de Lambert93 en WGS84
*/
require_once __DIR__.'/../../geovect/coordsys/light.inc.php';

echo "<h2>Conversion en WGS84 du fichier AEAG-ouvrages-hydro.csv</h2>\n";
echo "<table border=1><th>code</th><th>lon</th><th>lat</th>\n";
if (!($file = fopen(__DIR__."/../data/AEAG-ouvrages-hydro.csv",'r')))
  die("Erreur ouverture du fichier AEAG-ouvrages-hydro");

$header = fgetcsv($file, 1024, ';', '"');
while ($record = fgetcsv($file, 1024, ';', '"')) {
  foreach ($header as $i => $k)
    $rec[$k] = $record[$i];
  //echo "code=$rec[code], x=$rec[x], y=$rec[y] -> ";
  $geo = Lambert93::geo([$rec['x'], $rec['y']]);
  //printf("%.6f, %.6f<br>\n", $geo[0], $geo[1]);
  printf("<tr><td>%s</td><td>%.6f</td><td>%.6f</td>", $rec['code'], $geo[0], $geo[1]);
}
fclose($file);
echo "</table>\n";
