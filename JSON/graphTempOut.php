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

$graph->title->Set('Temperature (°' . $data['units']. ')');

// Create the linear plot
if (isset($data['dew'])) {
    $lineplot2 = new LinePlot($data['dew'], $data['time']);
    $lineplot2->SetWeight(2);
    $lineplot2->SetLegend('Dewpoint');
    $lineplot2->SetColor("#2222B2:1.3");
    $graph->Add($lineplot2);
}
if (isset($data['apptemp'])) {
    $lineplot3 = new LinePlot($data['apptemp'], $data['time']);
    $lineplot3->SetWeight(2);
    $lineplot3->SetLegend('Apparent');
    $lineplot3->SetColor("#22B222:1.3");
    $graph->Add($lineplot3);
}
if (isset($data['wchill'])) {
    $lineplot4 = new LinePlot($data['wchill'], $data['time']);
    $lineplot4->SetWeight(2);
    $lineplot4->SetLegend('Wind Chill');
    $graph->Add($lineplot4);
}

if (isset($data['temp'])) {
    $lineplot1 = new LinePlot($data['temp'], $data['time']);
    $lineplot1->SetWeight(2);
    $lineplot1->SetLegend('Temperature');
    $lineplot1->SetColor("#B22222:1.3");
    $graph->Add($lineplot1);
}

$line = new PlotLine(HORIZONTAL,0,"blue@0.5",2);
$graph->AddLine($line);
$graph->setClipping(true);

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();
