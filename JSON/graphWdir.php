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
$graph->SetScale('datint', 0, 360);
$graph->yaxis->HideTicks(false, true);
$graph->yaxis->SetMajTickPositions(array(0, 45, 90, 135, 180, 225, 270, 315, 360), array('Calm', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'));

//fetch the data
$data = get_data('wdir');

graph_common($graph);

$graph->title->Set('Wind Direction');

$graph->yaxis->scale->SetGrace(0, 0);
$graph->yaxis->HideTicks(false, true);

// Create the scatter plot
if (isset($data['bearing'])) {
    $scatplot2 = new ScatterPlot($data['bearing'], $data['time']);
    $scatplot2->SetWeight(2);
    $scatplot2->SetLegend('Wind Direction');
    $scatplot2->mark->SetType(MARK_CROSS);
    $scatplot2->mark->SetWidth(3);
    $scatplot2->mark->SetColor("#000:1.3");
    $scatplot2->mark->SetFillColor("#000:1.3");
    $graph->Add($scatplot2);
}
if (isset($data['avgbearing'])) {
    $scatplot1 = new ScatterPlot($data['avgbearing'], $data['time']);
    $scatplot1->SetWeight(2);
    $scatplot1->SetLegend('Average Direction');
    $scatplot1->mark->SetType(MARK_FILLEDCIRCLE);
    $scatplot1->mark->SetWidth(3);
    $scatplot1->mark->SetColor("#B22222:1.3");
    $scatplot1->mark->SetFillColor("#B22222:1.3");
    $graph->Add($scatplot1);
}

$graph->setClipping(true);

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();
