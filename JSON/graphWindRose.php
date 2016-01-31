<?php
/*!
* Simple static trend graphs for Cumulus MX
*
* Created by Mark Wittl, January 2016
* based on originals by Mark Crossley, January 2015
*
* Released under GNU GENERAL PUBLIC LICENSE, Version 2, June 1991
* See the enclosed License file
*
* File encoding = UTF-8
* Tab spacing = 4
*
*/

require_once 'graphSettings.php';

$name = basename($_SERVER['PHP_SELF'], '.php').'.png';


//fetch the data
//You have to call two variables from the clientraw, the winddir ($datay) and the speed ($datay1).
$w1 = get_data('wdir');
$w2 = get_data('wind');

$datay = $w1['bearing'];
$datay1 =$w2['wspeed'];

//**************************************************************************************************************************************************

//
// getBin() converts a value in degrees (0-360) into a compass point bin (0-7 or 0-16)
//
function getBin($deg) {
    global $GRAPH;

    if ($GRAPH['rosePoints'] === 8) {
        return floor(($deg + 22.5) / 45) % 8;
    } else {
        return floor(($deg + 11.25) / 22.5) % 16;
    }
}

//The rest of the script is clearly explained at the jpgraph website.

// Loop through raw data arrays and place data into the appropriate arrays
// depending on their wind direction
for ($ii = 0; $ii < count($datay); $ii++) {
    $direction_array[getBin($datay[$ii])][] = $datay1[$ii];
}

// Some directions may not have any data so this will create an array of the
// directions that do.
$direction_keys = array_keys($direction_array);

// Calculate max windspeed, used for windrose range
$max_wind = round(max($datay1), 0);

// Get the number of data points
$num_data = count($datay1);

// Define the data range array for the windrose, this needs to be done
// ahead of time because some of the computational aspects require this range
$wind_range_max = $max_wind < 20 ? 25 : $max_wind;
$data_range_array = array(1, 5, 10, 15, 20, $wind_range_max);

// Loop through dirction array based on direction keys and calculate the histogram
// stats for each array.
foreach ($direction_keys as $direction) {

    // Set up counter to determine how many data points there are within each
    // direction array and wind range.
    for ($ii = 0; $ii <= 5; $ii++) {
        $count_data[$ii] = 0;
    }

    // Define raw data to be processed into array counters
    $raw_data = $direction_array[$direction];

    // The windrose software needs to know the % of data points that fall into
    // each range for each wind direction.
    foreach ($raw_data as $temp_speed) {
        if ($temp_speed >= 0 and $temp_speed < $data_range_array[0]) {
            $count_data[0]++;
        } elseif ($temp_speed >= $data_range_array[0] and $temp_speed < $data_range_array[1]) {
            $count_data[1]++;
        } elseif ($temp_speed >= $data_range_array[1] and $temp_speed < $data_range_array[2]) {
            $count_data[2]++;
        } elseif ($temp_speed >= $data_range_array[2] and $temp_speed < $data_range_array[3]) {
            $count_data[3]++;
        } elseif ($temp_speed >= $data_range_array[3] and $temp_speed < $data_range_array[4]) {
            $count_data[4]++;
        } elseif ($temp_speed >= $data_range_array[4]) {
            $count_data[5]++;
        }
    }

    // Place all data in an array that can be used by JPGraph
    // First set up data array
    for ($ii = 0; $ii <= 5; $ii++) {
        $plot_data[$direction][$ii] = 0;
    }

    for ($ii = 0; $ii <= 5; $ii++) {
        $plot_data[$direction][$ii] = ($count_data[$ii] / $num_data) * 100;
    }
}


//**************************************************************************************************************************************************

// First create a new windrose graph with a title
$graph = new WindroseGraph($GRAPH['roseSize'], $GRAPH['roseSize'], $name, $GRAPH['cachetime']);
$graph->title->Set('Windrose (' . $w2['units'] . ')');

// Create the windrose plot.
$wp = new WindrosePlot($plot_data);
if ($GRAPH['rosePoints'] === 8) {
    $wp->SetType(WINDROSE_TYPE8);
} else {
    $wp->SetType(WINDROSE_TYPE16);
}

// we need to reverse and rotate the compass point array, as the wind rose starts at East and goes anti-clockwise!
$GRAPH['compass'] = array_reverse($GRAPH['compass']);
for ($i=0; $i<11; $i++) {
    array_push($GRAPH['compass'], array_shift($GRAPH['compass']));
}

$wp->SetCompassLabels($GRAPH['compass']);

$graph->Add($wp);

// Add and send back to browser
@unlink(CACHE_DIR . $name);
$graph->Stroke();

?>
