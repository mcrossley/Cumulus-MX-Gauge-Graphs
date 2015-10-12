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
$data = get_data(array('press'));

graph_common($graph);

$graph->title->Set('Barograph (' . $GRAPH['uom']['baro']. ')');

// Create the linear plot
$lineplot1 = new LinePlot($data['press'], $data['time']);
$lineplot1->SetWeight(2);

// Force labels to only be displayed every 1000 ft, tick every 500
$graph->yaxis->scale->ticks->Set(1000, 500);
$graph->yaxis->SetLabelFormatString("%02.0f");

// Add the plot to the graph
$graph->Add($lineplot1);

$lineplot1->SetColor("#2222B2:1.3");

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();

?>