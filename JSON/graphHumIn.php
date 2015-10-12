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
$graph->SetScale('datint');

//fetch the data
$data = get_data('hum');

graph_common($graph);

$graph->title->Set('Relative Humidity (' . $data['units']. ')');

// Create the linear plot
$lineplot1 = new LinePlot($data['inhum'], $data['time']);
$lineplot1->SetWeight(2);
$lineplot1->SetLegend('Indoor humidity');

$graph->yaxis->scale->SetAutoMax(100);
$graph->yaxis->scale->SetGrace(0, 5);

// Add the plot to the graph
$graph->Add($lineplot1);

$lineplot1->SetColor("#B22222:1.3");

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();

?>