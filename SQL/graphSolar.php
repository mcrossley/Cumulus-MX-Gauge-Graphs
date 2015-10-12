<?php
/*!
 * Simple static trend graphs for Cumulus MX
 *
 * Created by Mark Crossley, January 2015
 *
 * Released under GNU GENERAL PUBLIC LICENSE, Version 2, June 1991
 * See the enclosed License file
 *
 * File encoding = UTF-8
 *
 */

require_once 'graphSettings.php';

$name = basename($_SERVER['PHP_SELF'], '.php').'.png';

// Create the graph and set a scale.
$graph = new Graph($GRAPH['width'], $GRAPH['height'], $name, $GRAPH['cachetime']);
$graph->SetScale('datlin');

//fetch the data
$data = get_data(array('SolarRad','CurrentSolarmax'));

graph_common($graph);

$graph->title->Set('Solar Irradiance (W/m²)');

// Create the linear plot
$lineplot1 = new LinePlot($data['CurrentSolarmax'], $data['time']);
$lineplot1->SetWeight(2);
$lineplot1->SetLegend('Theoretical solar max');

$lineplot2 = new LinePlot($data['SolarRad'], $data['time']);
$lineplot2->SetWeight(2);
$lineplot2->SetLegend('Solar radiation');


$graph->xaxis->scale->setAutoMin(0);
$graph->yaxis->scale->SetGrace(5, 0);

// Add the plot to the graph
$graph->Add($lineplot1);
$graph->Add($lineplot2);

$lineplot1->SetColor("#2222B2:1.3");
$lineplot2->SetColor("#B22222:1.3");

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();

?>
