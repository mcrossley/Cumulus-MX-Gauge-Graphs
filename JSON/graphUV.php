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

$graph->title->Set('UV Index');

// Create the linear plot
if (isset($data['UV'])) {
    $lineplot1 = new LinePlot($data['UV'], $data['time']);
    $lineplot1->SetWeight(2);
    $lineplot1->SetColor("#B22222:1.3");
    $graph->Add($lineplot1);
}

$graph->xaxis->scale->setAutoMin(0);
$graph->yaxis->scale->SetGrace(5, 0);
$graph->yaxis->SetLabelFormatString("%02.1f");

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();
