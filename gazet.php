<?php

if (!isset($_GET['num']))
  die("num non défini");

if (!($file = fopen(__DIR__."/data/retenues-20200121-Occitanie.csv",'r')))
  die("Erreur ouverture du fichier retenues-20200121-Occitanie.csv");

$header = fgetcsv($file, 1024, ';', '"');
while ($record = fgetcsv($file, 1024, ';', '"')) {
  foreach ($header as $i => $k)
    $rec[$k] = $record[$i];
  $coord = [floatval(str_replace(',','.',$rec['Lon'])), floatval(str_replace(',','.',$rec['Lat']))];
  if ($rec['Num'] == $_GET['num']) {
    echo "<table border=1>\n";
    foreach ($rec as $k => $v) {
      echo "<tr><td>$k</td><td>$v</td></tr\n";
    }
    echo "</table>\n";
  }
}
