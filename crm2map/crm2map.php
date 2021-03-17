<?php
/*
Plugin Name:	CRM2MAP
Plugin URI:
Description:	Import lead from CRM into map
Version:		1.0
Author:			sd4travel@gmail.com
*/

require 'libraries/hapihp/vendor/autoload.php';
require 'classes/LeadImport.php';
//require 'libraries/keenetic_parser.php';

set_time_limit(0);

add_action('wp_ajax_nopriv_crm2map_import', 'crm2map_import');
add_action('wp_ajax_crm2map_import', 'crm2map_import');

function crm2map_import()
{
//    echo microtime();exit;
//    echo time()-2*86400;
    $importer = new LeadImportHubspot();
//    $importer->getLeads();//exit;
    $importer->getRecentLeads(); //exit;
    $importer->geocode();
    $importer->export2map();
//    $importer->geocode_offices();exit;
    exit;
}

add_action('wp_ajax_nopriv_crm2map_webhook', 'crm2map_webhook');
add_action('wp_ajax_crm2map_webhook', 'crm2map_webhook');

function crm2map_webhook()
{
    $importer = new LeadImportHubspot();
    $importer->updateLead();
    exit;
    /*    $importer->geocode();
    //    $importer->geocode_offices();exit;
        $importer->export2map();
    */
}
require_once 'libraries/adwords.php';
require_once 'classes/AdwordsImport.php';

function crm2map_get_importer()
{
    require_once dirname(__FILE__).'/libraries/adwords.php';
    require_once dirname(__FILE__).'/classes/AdwordsImport.php';
    $adwordsImporter = new AdwordsImport();

    return $adwordsImporter;
}

add_action('wp_ajax_nopriv_crm2map_geojson', 'crm2map_geojson');
add_action('wp_ajax_crm2map_geojson', 'crm2map_geojson');
function crm2map_geojson()
{//6014049186
//    $colors=array('3cF00014', '3cF34250', '3cF8979F', '3cFDE2E4', '3c0DE2E4' );

    //$number % 2 == 0)
    $scheme = [];
    // violet,violet,violet,blue,blue
    $scheme[] = ['#ccccff', '#9999ff', '#6666cc', '#333399', '#000066'];
    //pink,pink, red,red,brown
    $scheme[] = ['#ffcccc', '#ff9999', '#ff6666', '#cc3333', '#990000'];
    //green,
    $scheme[] = ['#ccffcc', '#ccff99', '#99cc66', '#669933', '#336600'];
    //orange,
    $scheme[] = ['#eba912', '#d69a11', '#c89e3e', '#d8b669', '#a1740f'];

    //yellow,
    $scheme[] = ['#fbea18', '#ffec00', '#f9f298', '#d4cc68', '#c2b515'];

//    $colors[]=array_reverse(array('#000078', '#383896', '#5555A5', '#8D8DC3', '#AAAAD2' ));
//    $colors[]=array_reverse(array('#780000', '#963838', '#A55555', '#C38D8D', '#D2AAAA' ));

    $adwordsImporter = new AdwordsImport();
    if (isset($_GET['c'])) {
        $cc = $_GET['c'];
        if (isset($_GET['s'])) {
            $sn = min(intval($_GET['s']), sizeof($scheme) - 1);
        } else {
            $sn = rand(0, sizeof($scheme) - 1);
        }
        foreach ($cc as $c) {
            if ($c != '') {
                $locations = $adwordsImporter->getLocations($c);
//                            print_r($locations);exit;

                $codes = array_map(function ($itm) {
                    return "'".$itm['location_name']."'";
                }, $locations);
                $bounds = $adwordsImporter->getBoundaries($codes);

                $cd = array_map(function ($itm) {return [$itm['location_name']]; }, $locations);
                $json = ['type' => 'FeatureCollection', 'properties' => ['zipcodes' => array_values($cd)], 'features' => []/*,"crs"=>array( "type"=>"name", "properties"=>array( "name"=>"urn:ogc:def:crs:OGC:1.3:CRS84" ) )*/];

//                print_r($locations);
//                print_r($bounds);exit;
//                $json=array('type'=>"FeatureCollection", "features"=>array());
                foreach ($bounds as $i => $loc) {
                    $ft = ['type' => 'Feature',
                        'properties' => [
                            'title' => $loc['zipcode'],
                            'cost' => '$'.$locations[$loc['zipcode']]['cost'],
                            'fillColor' => $scheme[$sn][$locations[$loc['zipcode']]['color_number']],
                            'highlightColor' => 'yellow',
                            'tessellate' => -1, ],
                        'geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [],
                    ], ];
                    $coords = [];
                    foreach (explode(' ', $loc['boundaries_out']) as $point) {
                        $coords[] = explode(',', $point);
                    }
                    $ft['geometry']['coordinates'] = [$coords];
                    $json['features'][] = $ft;
                }
//                print_r($json);exit;
                header('Access-Control-Allow-Origin: *');
                echo json_encode($json, JSON_NUMERIC_CHECK);
                exit;
            }
        }
        //getBoundaries
    }
    exit;
}

add_action('wp_ajax_nopriv_crm2map_getkml', 'crm2map_getkml');
add_action('wp_ajax_crm2map_getkml', 'crm2map_getkml');
function crm2map_getkml()
{
    $colors = ['3cF00014', '3cF34250', '3cF8979F', '3cFDE2E4', '3c0DE2E4'];
    $adwordsImporter = new AdwordsImport();
    if (isset($_GET['c'])) {
        $cc = $_GET['c'];

        foreach ($cc as $c) {
            if ($c != '') {
                $locations = $adwordsImporter->getLocations($c);
                //            print_r($locations);exit;

                $codes = array_map(function ($itm) {
                    return "'".$itm['location_name']."'";
                }, $locations);
                $bounds = $adwordsImporter->getBoundaries($codes);

                echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
                <!-- Generated by KMLtools -->
                <kml xmlns="http://earth.google.com/kml/2.2">
                    <Document>
                        <name>gps2kml</name>
                        <NetworkLinkControl>
                            <expires><?php echo time() - 100000; ?></expires>
                        </NetworkLinkControl>
                <?php foreach ($colors as $i => $col) : ?>
                        <Style id="s<?php echo $i; ?>">
<LineStyle>
<color>ffffffff</color>
<width>2</width>
</LineStyle>
<PolyStyle>
<color><?php echo $col; ?></color>
<colorMode>normal</colorMode>
<fill>1</fill>
<outline>1</outline>
</PolyStyle>
                        </Style>
                <?php endforeach; ?>

                        <?php foreach ($bounds as $loc) : ?>
                            <Placemark id="<?php echo $loc['zipcode']; ?>">
                                <styleUrl>#s<?php echo $locations[$loc['zipcode']]['color_number']; ?></styleUrl>
                                <name>Cost: <?php echo '$'.$locations[$loc['zipcode']]['cost']; ?></name>
                                <Polygon>
                                    <outerBoundaryIs>
                                        <LinearRing>
                                            <coordinates><?php echo $loc['boundaries_out']; ?></coordinates>
                                        </LinearRing>
                                    </outerBoundaryIs>
                                </Polygon>
                            </Placemark>
                        <?php endforeach; ?>
                    </Document>
                </kml>
                <?php
            }
        }
        //getBoundaries
    }
    exit;
}

add_action('wp_ajax_nopriv_crm2map_test', 'crm2map_test');
add_action('wp_ajax_crm2map_test', 'crm2map_test');

function crm2map_test()
{
    /*
        $importer=new LeadImportCsv();
    #    $importer->getLeads();
    #    $importer->geocode();
        $importer->export2map();
        exit;

        exit;
    */
    /*

        $importer=new LeadImportHubspot();
        $ulist=array(724951,659851,618601,617951,614851,584651,584551,583651,568951,564101,560951,558751,552401,549101,541301,534251,533451,505301,498301,489751,489701,486551,424801,328101,257504,257801,248401,179801,166651,143901,128401,116501,133401,99051,88505,88206,88504,84901,84651,84601,83751,83501,61451,39651,38401,35201,33361,32917,32914,32911,32910,32875,32871,32861,32835,32805,32785,32768,32767,32763,32685,32634,32611,32511,32463,32454,32453,32399,32364,32357,32339,32338,32328,32243,32237,32150,32138,32096,31111,31107,30919,30918);
        foreach ($ulist as $cid)
            $importer->updateLead($cid);
        $importer->geocode();
        $importer->export2map();

        exit;
    /*
    //    $importer->getLeads();//exit;
        $importer->getRecentLeads();
        exit;

        $importer->geocode();
        $importer->export2map();
        exit;
    */

    $adwordsImporter = new AdwordsImport();
    $cid = '7481628553';
    echo "Customer: $cid \n";
    $r = $adwordsImporter->getProcedures($cid);
    echo "Procedures: \n";
    print_r($r);

    $r = $adwordsImporter->getLocations($cid);
    echo "Locations: \n";
    print_r($r);

    echo 'in';
    exit;
}
/*
add_filter('site_url',  'wplogin_filter', 10, 3);
function wplogin_filter( $url, $path, $orig_scheme )
{
 $old  = array( "/(wp-login\.php)/");
 $new  = array( "wp-logi.php");
 return preg_replace( $old, $new, $url, 1);
}
*/
?>