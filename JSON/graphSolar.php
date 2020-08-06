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
$data = get_data('solar');

graph_common($graph);

$graph->title->Set('Solar Irradiance (' . $data['units']. ')');

// Create the linear plot
if (isset($data['CurrentSolarMax'])) {
    $lineplot1 = new LinePlot($data['CurrentSolarMax'], $data['time']);
    $lineplot1->SetWeight(2);
    $lineplot1->SetLegend('Theoretical solar max');
    $lineplot1->SetColor("#2222B2:1.3");
    $graph->Add($lineplot1);
}
if (isset($data['SolarRad'])) {
    $lineplot2 = new LinePlot($data['SolarRad'], $data['time']);
    $lineplot2->SetWeight(2);
    $lineplot2->SetLegend('Solar radiation');
    $lineplot2->SetColor("#B22222:1.3");
    $graph->Add($lineplot2);
}

$graph->xaxis->scale->setAutoMin(0);
$graph->yaxis->scale->SetGrace(5, 0);

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();
