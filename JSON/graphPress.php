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
$data = get_data('press');

graph_common($graph);

$graph->title->Set('Barograph (' . $data['units']. ')');

// Create the linear plot
if (isset($data['press'])) {
    $lineplot1 = new LinePlot($data['press'], $data['time']);
    $lineplot1->SetWeight(2);
    $lineplot1->SetColor("#2222B2:1.3");
    $graph->Add($lineplot1);
}
// Force labels to only be displayed every 1000 ft, tick every 500
$graph->yaxis->scale->ticks->Set(1000, 500);
$graph->yaxis->SetLabelFormatString("%02.0f");

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();
