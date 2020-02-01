<?php
/*PhpDoc:
name: index.php
title: index.php - page d'accueil
*/
?>
<!DOCTYPE HTML><html>
<head><title>POC barrages</title><meta charset="UTF-8"></head>
<body>
<h2>POC Barrages</h2>
POC d'estimation du volume d'eau stocké dans les barrages - Pascal Kosuth - CGEDD.<br>
Première maquette affichant les graphiques associés à chacun des barrages.<br>
Dans cette première version, seuls les principaux barrages d'Occitanie sont traités
et seul le graphique du réservoir de l'Astarac est disponible.
<h2>Menu</h2>
<ul>
  <li><a href='map.php'>carte des barrages</a></li>
  <li><a href='gazet.php'>liste des barrages</a></li>
</ul>
<h3>Divers</h3>
<ul>
  <li><a href='geojson.php'>Test GeoJSON des barrages</a></li>
  <li><a href='https://github.com/benoitdavidfr/barrages' target='_blank'>Github du code source</a></li>
  <li><a href='https://www.fusioncharts.com/charts#fusioncharts'>Bibliothèque fusioncharts</a></li>
<?php
if ($_SERVER['HTTP_HOST'] == 'localhost') {
  echo "<li><a href='http://localhost/synchro.php?remote=http://bdavid.alwaysdata.net/&amp;dir=barrages'
   target='_blank'>synchro</a></li>\n";
  echo "<li><a href='http://bdavid.alwaysdata.net/barrages' target='_blank'>site Alwaysdata</a></li>\n";
}
?>
</ul>
