#Simple PHP generated graph images for use with Cumulus MX

These scripts are intended to create simple static graphs for use on your web pages. Unlike Cumulus, CumulusMX does not generate these images, which were used amongst other things for the 'popup' tooltips on the [SteelSeries Weather Gauges](https://github.com/mcrossley/SteelSeries-Weather-Gauges).

The graphs are drawn using [JPGraphs](http://jpgraph.net/), so you need a copy of this package on your web server for these scripts to work.

You will need JPGraphs version 4.0 or later for PHP 7 support.

The scripts use the JPGraphs file caching system, the images are generated on demand, but pulled from the cache if the previous version is within the time (default is 10 minutes). I didn't like the overhead of a cron job endlessly redrawing them if nobody needs them.

There are two flavours of the scripts in the repository, the first uses the JSON data files that CumulusMX generates, if you intend to use this flavour, then you will need to configure CumulusMX to upload the JSON data files to your web server.

The alternative version of the scripts will extract the required data from a MySQL database. CumulusMX can be configure to upload directly to MySQL, or you can use the legacy upload scripts to be found in the [Cumulus Wiki](http://wiki.sandaysoft.com/a/ImportCumulusFile). These graph scripts assume the schema used by CumulusMX and the legacy scripts.

##Setup

Most of the settings you need to alter are in the relevant `graphSettings.php` script.

Included is an `.htaccess` file so that if you have an Apache server, you can display the scripts via the same image names used in Cumulus (plus some extra variations).

Please note, I have found that JPGraphs ver 3.5.0b1 has a bug in the caching system. The fix for the bug is to edit `gd_image.inc.php`, and add a 'return true' at line 2266 in the function GetAndStream()...

```
function GetAndStream($aImage,$aCacheFileName) {
        if( $this->Isvalid($aCacheFileName) ) {
            $this->StreamImgFile($aImage,$aCacheFileName);
            return true;
        }
        else {
            return false;
        }
    }
```

Mark Wittl has kindly contributed a WindRose script, if you want to use this graphic, then there are some additional steps you need to take...

1. Modify the `jpgraph_windrose.php` file to remove the import of 'jpgraph_glayout_vh.inc.php'
`//require_once('jpgraph_glayout_vh.inc.php');`
2. Uncomment the line `# include $GRAPH['jpgraphloc'] . 'jpgraph_windrose.php';` in graphSettings.php
3. Edit the `jp-config.inc.php` file to point to the font directory under the installed jpgraph location
`define('TTF_DIR','<YOUR_PATH>/jpgraph/fonts/');`
You will need the following font files `arial.ttf` and `verdana.ttf`

Good luck, you can normally find me on the [Cumulus forum](http://sandaysoft.com/forum)

Mark
