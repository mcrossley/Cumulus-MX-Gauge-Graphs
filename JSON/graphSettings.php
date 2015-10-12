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

$GRAPH =  array();
############################################################################
# CONFIGURATION INFORMATION
############################################################################
$GRAPH['version']        = "0.4";
$GRAPH['width']          = 600;
$GRAPH['height']         = 300;
$GRAPH['jsonloc']        = "/";
$GRAPH['jpgraphloc']     = "/jpgraph/";
$GRAPH['cachetime']      = 10;
#---------------------------------------------------------------------------

############################################################################
# Includes for JpGraph
############################################################################
include $GRAPH['jpgraphloc'] . "jpgraph.php";
include $GRAPH['jpgraphloc'] . "jpgraph_line.php";
include $GRAPH['jpgraphloc'] . "jpgraph_scatter.php";
include $GRAPH['jpgraphloc'] . "jpgraph_date.php";
include $GRAPH['jpgraphloc'] . "jpgraph_plotline.php";

// Set the TZ to UTC so times displlay in 'station' time
date_default_timezone_set('UTC');

############################################################################
# COMMON FUNCTIONS
############################################################################

function graph_common($graph) {
//    global $graph;

    // Remove the default theme
    $graph->graph_theme = null;

    // Ensure anti-aliasing is off.
    $graph->img->SetAntiAliasing(false);

    // Setup margin and titles
    $graph->SetMargin(50, 20, 20, 55);

    $graph->xaxis->HideLine(false);
    $graph->xaxis->HideTicks(false, false);
    $graph->xaxis->SetPos('min');
    $graph->xgrid->SetFill(false);
    $graph->xgrid->SetColor('gray');
    $graph->xgrid->SetLineStyle('dotted');
    $graph->xgrid->Show(true, false);

    $graph->yaxis->HideLine(false);
    $graph->yaxis->HideTicks(false, false);
    $graph->yaxis->scale->SetGrace(5, 5);
    //$graph->yaxis->SetTitleMargin(32);
    $graph->ygrid->SetFill(false);
    $graph->ygrid->SetColor('gray');
    $graph->ygrid->SetLineStyle('dotted');

    // Plot area settings
    $graph->SetBox($aDrawPlotFrame=true, $aPlotFrameColor=array(100,100,100), $aPlotFrameWeight=1);
    $graph->SetBackgroundGradient($aFrom=array(200,220,220), $aTo=array(250,255,255), $aGradType=GRAD_MIDHOR, $aStyle=BGRAD_FRAME);

    $graph->legend->SetFillColor('#d0d0d0');
    $graph->legend->SetFrameWeight(1);
    $graph->legend->SetPos(0.5, 0.98, 'center', 'bottom');
    $graph->legend->SetLayout(LEGEND_HOR);

    // Adjust the start time for an "even" 6 hours
    //$graph->xaxis->scale->SetTimeAlign(HOURADJ_6);

    // Force labels to only be displayed every 6 hours, tick every hour
    $graph->xaxis->scale->ticks->Set(6*60*60, 1*60*60);

    // Use hour:minute format for the labels
    $graph->xaxis->scale->SetDateFormat('H:i');

}

//
// We need to reorg the JSON data into a more useful format
//
// input JSON:
//     [
//          {<dataSetName1>: [[time1, val1], [time2, val2], ...]},
//          {<dataSetName2>: [[time1, val1], [time2, val2], ...]},
//          ...
//     ]
//
// output Array:
//      [
//          time: [time1, time2, ...],
//          <dataSetName1>: [val1, val2, ...],
//          <dataSetName2>: [val1, val2, ...],
//          ...
//      ]
function reorg_data($dat) {
	$retVal = array();
	$keys = array_keys($dat);
	for ($i = 0; $i < count($keys); $i++) {
        $key = $keys[$i];
		$values = $dat[$key];
		for ($j = 0; $j < count($values); $j++) {
            if ($i === 0) {
                // JavaScript time in msecs, convert to secs
				$retVal['time'][] = $values[$j][0] / 1000;
            }			
            $retVal[$key][] = $values[$j][1];			
        }	
	}
    return $retVal;
}

function get_data($type) {
    global $GRAPH;

    // get the units
    $config = json_decode(file_get_contents($GRAPH['jsonloc'] . 'graphconfig.json'), true);

    // get the data
    switch ($type) {
    case 'temp':
        $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'tempdata.json'), true));
        $data['units'] = $config['temp']['units'];
        break;
    case 'hum':
        $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'humdata.json'), true));
        $data['units'] = 'RH %';
        break;
    case 'press':
        $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'pressdata.json'), true));
        $data['units'] = $config['press']['units'];
        break;
    case 'rain':
        $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'raindata.json'), true));
        $data['units'] = $config['rain']['units'];
        break;
    case 'solar':
        $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'solardata.json'), true));
        $data['units'] = 'W/m²';
        break;
    case 'wdir':
        $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'wdirdata.json'), true));
        $data['units'] = '°';
        break;
    case 'wind':
        $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'winddata.json'), true));
        $data['units'] = $config['wind']['units'];
        break;
    default:
        die('Unknown JSON data file requested');
        break;
    }

    return($data);
}

############################################################################
# END OF SCRIPT
############################################################################