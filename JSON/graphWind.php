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
$data = get_data('wind');

graph_common($graph);

$graph->title->Set('Wind (' . $data['units'] . ')');

// Create the linear plot
if (isset($data['wgust'])) {
    $lineplot2 = new LinePlot($data['wgust'], $data['time']);
    $lineplot2->SetWeight(2);
    $lineplot2->SetLegend('Wind Gust');
    $lineplot2->SetColor("#2222B2:1.3");
    $graph->Add($lineplot2);
}
if (isset($data['wspeed'])) {
    $lineplot1 = new LinePlot($data['wspeed'], $data['time']);
    $lineplot1->SetWeight(2);
    $lineplot1->SetLegend('Average speed');
    $lineplot1->SetColor("#B22222:1.3");
    $graph->Add($lineplot1);
}

$graph->xaxis->scale->setAutoMin(0);
$graph->yaxis->scale->SetGrace(5, 0);

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();
