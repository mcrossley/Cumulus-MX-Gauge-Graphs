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
$data = get_data('temp');

graph_common($graph);

$graph->title->Set('Indoor Temperature (°' . $data['units']. ')');

// Create the linear plot
if (isset($data['intemp'])) {
    $lineplot1 = new LinePlot($data['intemp'], $data['time']);
    $lineplot1->SetWeight(2);
    $lineplot1->SetColor("#B22222:1.3");
    $graph->Add($lineplot1);
}

$line = new PlotLine(HORIZONTAL,0,"blue@0.5",2);
$graph->AddLine($line);

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();
