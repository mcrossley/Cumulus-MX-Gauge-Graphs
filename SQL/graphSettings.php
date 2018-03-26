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
$GRAPH['version']        = '1.7';
$GRAPH['width']          = 600;
$GRAPH['height']         = 300;
$GRAPH['jpgraphloc']     = 'jpgraph/'; // Path to jpgraph, or put it in your PHP path
                                        // NOTE: This is the *server* operating system path to the file, so unless you have added
                                        // the web folder to your PHP path, it will be something like "/home/<userid>/www/jpgraph/"
$GRAPH['cachetime']      = 10; // number of minutes to cache images
$GRAPH['hours']          = 24; // number of hours to graph
$GRAPH['uom']            = array('temp'=>'C', 'rain'=>'mm', 'wind'=>'mph', 'baro'=>'hPa', 'cloudbase'=>'feet');
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
# include $GRAPH['jpgraphloc'] . 'jpgraph_windrose.php';

// Set the TZ to UTC so times display in 'station' time
date_default_timezone_set('UTC');

// Current field names (matches tag fields) used
$GRAPH['cvalues'] = array(
    'date','time','temp','hum','dew','wspeed','wlatest','bearing','rrate',
    'rfall','press','currentwdir','beaufortnumber','windunit','tempunitnodeg','pressunit','rainunit',
    'windrun','presstrendval','rmonth','ryear','rfallY','intemp','inhum','wchill',
    'temptrend','tempTH','TtempTH','tempTL','TtempTL',
    'windTM','TwindTM','wgustTM','TwgustTM',
    'pressTH','TpressTH','pressTL','TpressTL',
    'version','build',
    'wgust','heatindex','humidex','UV','ET','SolarRad','avgbearing',
    'rhour','forecastnumber','isdaylight','SensorContactLost','wdir','cloudbasevalue','cloudbaseunit',
    'apptemp','SunshineHours','CurrentSolarmax','IsSunny');


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

function get_data($fields) {
    global $GRAPH;
    $retVal = array();

    include 'db_ro_details.php';

    // Connect to the database
    $mysqli = new mysqli($dbhost, $dbuser, $dbpassword, $database);
    if ($mysqli->connect_errno) {
      die('Failed to connect to the database server - ' . $mysqli->connect_error);
    }

    $result = $mysqli->query('SET time_zone="UTC"');
    if (!$result) {
        die('ERROR - TZ Statement');
    }

    $cols = '';
    foreach ($fields as $fld) {
        if (array_search($fld, $GRAPH['cvalues'])) {
            $cols .= $fld . ',';
        } else {
            die("!!Failed to match field: $fld");
        }
    }
    $cols = rtrim($cols, ',');

    $query = "SELECT unix_timestamp(LogDateTime) AS time, $cols
        FROM realtime
        WHERE LogDateTime >= now() - INTERVAL " .$GRAPH['hours']. " HOUR
        ORDER BY time";

    $result = $mysqli->query($query);
    if (!$result) {
        die('ERROR - Bad Select Statement: ' . $mysqli->error . '<br><br>' . $query);
    }


    // get the field names in teh returned data
    $flds = $result->fetch_fields();
    $keys = array();
    for ($i = 0; $i < count($flds); $i++) {
        $keys[] = $flds[$i]->name;
    }

    // fetch the SQL data
    while ($row = $result->fetch_assoc()) {
        foreach($keys as $key) {
            $retVal[$key][] = $row[$key];
       }
    }

    // close connection
    $mysqli->close();

    return($retVal);
}

############################################################################
# END OF SCRIPT
############################################################################
?>