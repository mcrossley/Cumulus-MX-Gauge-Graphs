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
$GRAPH['version']        = '1.6';
$GRAPH['width']          = 600;
$GRAPH['height']         = 300;
$GRAPH['jsonloc']        = '/usr/share/CumulusMX/web/';
//$GRAPH['jsonloc']        = '/';         // NOTE: This is the *server* operating system path to the file, so unless you have added
                                        // the web folder to your PHP path, it will be something like "/home/<userid>/www/"
$GRAPH['jpgraphloc']     = 'jpgraph/';  // NOTE: Same path type as json above
$GRAPH['cachetime']      = 10;
$GRAPH['rosePoints']     = 16;  // 8 or 16
$GRAPH['roseSize']       = 400;
// Localised Compass point array
$GRAPH['compass']        = array('N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW');
#---------------------------------------------------------------------------

############################################################################
# Includes for JpGraph
############################################################################
include $GRAPH['jpgraphloc'] . 'jpgraph.php';
include $GRAPH['jpgraphloc'] . 'jpgraph_line.php';
include $GRAPH['jpgraphloc'] . 'jpgraph_scatter.php';
include $GRAPH['jpgraphloc'] . 'jpgraph_date.php';
include $GRAPH['jpgraphloc'] . 'jpgraph_plotline.php';
include $GRAPH['jpgraphloc'] . 'jpgraph_windrose.php';

// Set the TZ to UTC so times display in 'station' time
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
        if (file_exists($GRAPH['jsonloc'] . 'tempdata.json')) {
            $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'tempdata.json'), true));
            $data['units'] = $config['temp']['units'];
        } else {
            die('JSON file not found: ' . $GRAPH['jsonloc'] . 'tempdata.json');
        }
        break;
    case 'hum':
        if (file_exists($GRAPH['jsonloc'] . 'humdata.json')) {
            $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'humdata.json'), true));
            $data['units'] = 'RH %';
        } else {
            die('JSON file not found: ' . $GRAPH['jsonloc'] . 'humdata.json');
        }
        break;
    case 'press':
        if (file_exists($GRAPH['jsonloc'] . 'pressdata.json')) {
            $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'pressdata.json'), true));
            $data['units'] = $config['press']['units'];
        } else {
            die('JSON file not found: ' . $GRAPH['jsonloc'] . 'pressdata.json');
        }
        break;
    case 'rain':
        if (file_exists($GRAPH['jsonloc'] . 'raindata.json')) {
            $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'raindata.json'), true));
            $data['units'] = $config['rain']['units'];
        } else {
            die('JSON file not found: ' . $GRAPH['jsonloc'] . 'raindata.json');
        }
        break;
    case 'solar':
        if (file_exists($GRAPH['jsonloc'] . 'solardata.json')) {
            $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'solardata.json'), true));
            $data['units'] = 'W/m²';
        } else {
            die('JSON file not found: ' . $GRAPH['jsonloc'] . 'solardata.json');
        }
        break;
    case 'wdir':
        if (file_exists($GRAPH['jsonloc'] . 'wdirdata.json')) {
            $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'wdirdata.json'), true));
            $data['units'] = '°';
        } else {
            die('JSON file not found: ' . $GRAPH['jsonloc'] . 'wdirdata.json');
        }
        break;
    case 'wind':
        if (file_exists($GRAPH['jsonloc'] . 'winddata.json')) {
            $data = reorg_data(json_decode(file_get_contents($GRAPH['jsonloc'] . 'winddata.json'), true));
            $data['units'] = $config['wind']['units'];
        } else {
            die('JSON file not found: ' . $GRAPH['jsonloc'] . 'winddata.json');
        }
        break;
    default:
        die('Unknown JSON data file requested: ' . $type);
        break;
    }

    return($data);
}

############################################################################
# END OF SCRIPT
############################################################################
?>