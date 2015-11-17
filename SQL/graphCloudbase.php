<?php
/*!
 * Simple static trend graphs for Cumulus MX
 *
 * Created by Mark Crossley, Febraury 2015
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
$data = get_data(array('cloudbasevalue'));

graph_common($graph);

$graph->title->Set('Cloudbase (' .$GRAPH['uom']['cloudbase']. ')');

// Create the linear plot
$lineplot1 = new LinePlot($data['cloudbasevalue'], $data['time']);
$lineplot1->SetWeight(2);

if (strtolower(substr($GRAPH['uom']['cloudbase'], 0, 1)) === 'm') {
	// Force labels to only be displayed every 500 m, tick every 100
	$graph->yaxis->scale->ticks->Set(500, 100);
} else {
	// Force labels to only be displayed every 1000 ft, tick every 500
	$graph->yaxis->scale->ticks->Set(1000, 500);
}
$graph->xaxis->scale->setGrace(5, 0);
$graph->yaxis->scale->setAutoMin(0);

// Add the plot to the graph
$graph->Add($lineplot1);

$lineplot1->SetColor("#B22222:1.3");

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();

?>