<?php
/*PhpDoc:
name: gazet.php
title: gazet.php - liste des barrages correspondant à l'un des fichiers CSV
doc: |
  Le paramètre file donne le nom du fichier CSV dans data
  Si pas d'autre paramètre affiche la liste des barrages du fichier avec des liens vers:
    - le détail du barrage
    - le graphique associé au barrage
    - la carte zoomée sur le barrage
journal: |
*/

if (!isset($_GET['file'])) {
  die("Erreur paramètre file absent");
}
if (!($file = fopen(__DIR__."/data/$_GET[file]",'r'))) {
  die("Erreur ouverture du fichier $_GET[file]");
}

$header = fgetcsv($file, 1024, ';', '"');

if (!isset($_GET['num'])) {
  echo "<!DOCTYPE HTML><html>";
  echo "<head><title>liste $_GET[file]</title><meta charset=\"UTF-8\"></head><body>";
  echo "<h2>Liste des barrages $_GET[file]</h2>\n";
  echo "<table border=1>\n";
  while ($record = fgetcsv($file, 1024, ';', '"')) {
    foreach ($header as $i => $k)
      $rec[$k] = $record[$i];
    $lon = str_replace(',','.',$rec['Lon']);
    $lat = str_replace(',','.',$rec['Lat']);
    echo "<tr><td><a href='?file=$_GET[file]&amp;num=$rec[Code]'>$rec[Nom]</a></td>",
      "<td><a href='chart.php?num=$rec[Code]'>graphique</a></td>",
      "<td><a href='map.php?lon=$lon&amp;lat=$lat&amp;level=15'>carte</a></td>",
      "</tr>\n";
  }
  echo "</table>\n";
  die();
}

echo "<!DOCTYPE HTML><html>";

while ($record = fgetcsv($file, 1024, ';', '"')) {
  foreach ($header as $i => $k)
    $rec[$k] = $record[$i];
  $coord = [floatval(str_replace(',','.',$rec['Lon'])), floatval(str_replace(',','.',$rec['Lat']))];
  if ($rec['Code'] == $_GET['num']) {
    echo "<head><title>$rec[Nom]</title><meta charset=\"UTF-8\"></head><body>";
    echo "<h2>$rec[Nom]</h2>\n";
    echo "<table border=1>\n";
    foreach ($rec as $k => $v) {
      echo "<tr><td>$k</td><td>$v</td></tr\n";
    }
    echo "</table>\n";
    die();
  }
}
