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
Version du 10/2/2020.</p>

POC d'estimation par analyse d'images satellitaires du volume d'eau stocké dans les barrages
 - Pascal Kosuth - CGEDD.<br>
Première maquette affichant les graphiques associés à chacun des barrages.<br>
Dans cette version, seul le graphique du réservoir de l'Astarac est disponible
et 3 listes de barrages sont affichées :<ul>
<li>les barrages de plus de 15 m France entière</li>
<li>les barrages de plus de 15 m d'Occitanie et de Nelle Aquitaine</li>
<li>les barrages utilisés pour validation de la méthode d'estimation</li>
</ul>
<h2>Menu</h2>
<ul>
  <li>carte des barrages avec sur la même page les graphiques
    des <a href='map2.php?chart=volume'>volumes</a>,
    des <a href='map2.php?chart=cote'>côtes</a>,
    des <a href='map2.php?chart=surface'>surfaces</a>,
    <a href='map2.php'>multiples</a>,
    <a href='map2.php?chart=scatter'>volume vs. côte</a>.
  </li>
  <li><a href='map.php'>carte des barrages avec graphiques sur une page séparée</a></li>
  <li><a href='gazet.php?file=retenues-20200121-Occitanie.csv'>liste des barrages pour validation</a></li>
  <li><a href='gazet.php?file=Barrages_15m_France_20200131.csv'>liste des barrages 15m France entière</a></li>
  <li><a href='gazet.php?file=Barrages_15m_Occitanie_NlAq_20200131.csv'>
    liste des barrages 15m Occitanie et Nelle Aquitaine</a></li>
</ul>
<h3>Divers</h3>
<ul>
  <li><a href='geojson.php?file=Barrages_15m_France_20200131.csv'>Test GeoJSON des barrages France entière</a></li>
  <li><a href='https://github.com/benoitdavidfr/barrages' target='_blank'>Github du code source</a></li>
  <li><a href='https://www.fusioncharts.com/charts#fusioncharts'>Bibliothèque fusioncharts</a></li>
<?php
if ($_SERVER['HTTP_HOST'] == 'localhost') {
  echo "<li><a href='http://localhost/synchro.php?remote=http://bdavid.alwaysdata.net/&amp;dir=barrages'
   target='_blank'>synchro</a></li>\n";
  echo "<li><a href='https://bdavid.alwaysdata.net/barrages' target='_blank'>site Alwaysdata</a></li>\n";
}
?>
</ul>
