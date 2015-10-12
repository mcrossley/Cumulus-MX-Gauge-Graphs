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
$data = get_data(array('temp','dew','apptemp','wchill'));

graph_common($graph);

$graph->title->Set('Temperature (°' . $GRAPH['uom']['temp']. ')');

// Create the linear plot
$lineplot1 = new LinePlot($data['temp'], $data['time']);
$lineplot1->SetWeight(2);
$lineplot1->SetLegend('Temperature');

$lineplot2 = new LinePlot($data['dew'], $data['time']);
$lineplot2->SetWeight(2);
$lineplot2->SetLegend('Dewpoint');

$lineplot3 = new LinePlot($data['apptemp'], $data['time']);
$lineplot3->SetWeight(2);
$lineplot3->SetLegend('Apparent');

$lineplot4 = new LinePlot($data['wchill'], $data['time']);
$lineplot4->SetWeight(2);
$lineplot4->SetLegend('Wind Chill');

$line = new PlotLine(HORIZONTAL,0,"blue@0.5",2);
$graph->AddLine($line);
$graph->SetClipping(true);

// Add the plot to the graph
$graph->Add($lineplot2);
$graph->Add($lineplot3);
$graph->Add($lineplot4);
$graph->Add($lineplot1);

$lineplot1->SetColor("#B22222:1.3");
$lineplot2->SetColor("#2222B2:1.3");
$lineplot3->SetColor("#22B222:1.3");

// Display the graph
@unlink(CACHE_DIR . $name);
$graph->Stroke();

?>
