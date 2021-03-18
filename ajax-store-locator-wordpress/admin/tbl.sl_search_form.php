<?php
    /*********** Loading Map Settings *************/
        $sl_qry_location = '';
        $sl_qry_location_geo = '';
        if (isset($_REQUEST['Location'])) {
            $sl_qry_location = $_REQUEST['Location'];
        }
        if (isset($_REQUEST['StoreLocation'])) {
            $sl_qry_location_geo = $_REQUEST['StoreLocation'];
        }
        add_action('wp_ajax_sl_dal_searchlocation', 'sl_dal_searchlocation_cbf');
        add_action('wp_ajax_nopriv_sl_dal_searchlocation', 'sl_dal_searchlocation_cbf');
        $sl_tb_mapsetting = $this->sl_return_dbTable('MAS');
        $sl_tb_appsetting = $this->sl_return_dbTable('APS');
        $sl_tb_stores = $this->sl_return_dbTable('SRO');
        $sl_tb_mapradius = $this->sl_return_dbTable('MAR');
        $sl_tb_markerimg = $this->sl_return_dbTable('MRI');
        $sl_tb_label = $this->sl_return_dbTable('LBL');
        $sl_tb_pluginset = $this->sl_return_dbTable('PLS');
        $sl_tb_storecat = $this->sl_return_dbTable('STC');
        $sl_select_obj = $wpdb->get_results("SELECT * FROM `$sl_tb_mapsetting` LIMIT 0 , 1");
        $ZoomControl = 'false';
        $PanControl = 'false';
        $streetControl = 'false';
        $RadKmType = 'false';
        $RadMiType = 'false';
        $RadiusType = '';
        $MapPosition = 'right';
        $Categoryvi = '';
        $LoadLoc = '';
        $SearchMode = '';
        $LogoVisible = '';
        $ZoomLevel = 10;
        $Radius = '';
        $ControlPosition = 'TOP_RIGHT';
        $MapType = 'ROADMAP';
        $Latti = $Lngg = 0.0;
        $api_key_script = '';
        $applicationCss = '';
        $isSingleCountry = 0;
        $preferCountry = '';

        foreach ($sl_select_obj as $sl_mapset_row) {
            $ZoomLevel = $sl_mapset_row->zoomlevel;
            $RadiusType = $sl_mapset_row->radiustype;
            $Radius = $sl_mapset_row->radius;
            $ControlPosition = $sl_mapset_row->controlposition;
            $MapType = $sl_mapset_row->maptype;
            $ZoomControl = ($sl_mapset_row->zoomcontrol == 1) ? 'true' : 'false';
            $PanControl = ($sl_mapset_row->pancontrol == 1) ? 'true' : 'false';
            $streetControl = ($sl_mapset_row->streetviewcontrol == 1) ? 'true' : 'false';
            /*
                        $RadKmType			= ($sl_mapset_row->radiustype == "KM" || $sl_mapset_row->radiustype == "Both") ? 'checked="true"' : '';
                        $RadMiType			= ($sl_mapset_row->radiustype == "Miles") ? 'checked="true"' : '';

            */
            $RadMiType = ($sl_mapset_row->radiustype == 'Miles' || $sl_mapset_row->radiustype == 'Both') ? 'checked="true"' : '';
            $RadKmType = ($sl_mapset_row->radiustype == 'KM') ? 'checked="true"' : '';
            $Latti = $sl_mapset_row->lat;
            $Lngg = $sl_mapset_row->lng;
            $api_key_script = (strlen($sl_mapset_row->map_api_key) > 0) ? '&key='.$sl_mapset_row->map_api_key : '';
        }

        $Radius = 100;

         /************* Loding Application Settings *************/
        $sl_select_obj = $wpdb->get_results("SELECT * FROM `$sl_tb_appsetting` LIMIT 0 , 1");
        foreach ($sl_select_obj as $sl_appset_row) {
            $MapPosition = ($sl_appset_row->mapposition == 'MR') ? 'left' : 'right';
            $SearchMode = $sl_appset_row->searchmode;
            $Categoryvi = $sl_appset_row->category;
            $LoadLoc = $sl_appset_row->load_location;
            $LogoVisible = $sl_appset_row->logo_visible;
            $applicationCss = trim($sl_appset_row->locator_css);
            $isSingleCountry = $sl_appset_row->enable_single_country;
            $preferCountry = $sl_appset_row->preferred_country;
        }

        $sql_qry = "SELECT DISTINCT `Country` FROM `$sl_tb_stores` ORDER BY `Country` ASC";
        $sl_select_obj = $wpdb->get_results($sql_qry);
        $sql_Nrow = $wpdb->num_rows;

        $url = '';
        $n_url = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
            $n_url = $url;
        }
        if ($url) {
            $url = strrev($url);
            $last_slash = strlen($url) - strpos($url, '/') - 1;
            $url = strrev($url);
            if ($last_slash) {
                $file_name = substr($url, $last_slash);
                $referurl = trim(str_replace($file_name, '', $url));
                $path_relative1 = $referurl;
            }
        } else {
            $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            $path_relative1 = ($https ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].($https && $_SERVER['SERVER_PORT'] == 443 ||
                $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT']).trim(str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));
        }
        $ref_url = substr($url, 0, strrpos($url, '?'));
        if ($ref_url) {
            $main_path = str_replace('', '', $ref_url);
        } else {
            $main_path = str_replace('', '', $url);
        }
    function full_url()
    {
        $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')).$s;
        $port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':'.$_SERVER['SERVER_PORT']);

        return $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
    }
    $full_link = full_url();

?>
<style type="text/css">
<?php if (strlen($applicationCss) > 10) {
    echo str_replace('#plugin_url', $this->plugin_url(), $applicationCss);
} else { ?>

	@media only screen and (max-width: 320px) and (min-width: 300px) {
		#sloc_leftBar{
			font-size:12px !important;
		}
		.storelist li, a.state li a.maanelam,.state li a.maanelamm, .sloc_browse li a.naadu,.browse li a.naaduu, .city li a.nagaram,.city li a.nagaramm{
			font-size:10px !important;
		}
		#sloc_leftBar, #sloc_Search-area{
			width:320px;
/*			padding-left: 20px;
*/
		}
	}

	@media only screen and (max-width: 400px) and (min-width: 320px) {
		#sloc_leftBar{
			font-size:12px !important;
		}
		.storelist li, a.state li a.maanelam,.state li a.maanelamm, .sloc_browse li a.naadu,.browse li a.naaduu, .city li a.nagaram,.city li a.nagaramm{
			font-size:10px !important;
		}
		#sloc_leftBar, #sloc_Search-area{
			width:320px;
/*			padding-left: 20px;*/
		}
	}
	@media only screen and (max-width: 1025px) and (min-width: 400px) {
		#sloc_leftBar, #sloc_Search-area{
			width:330px;
/*			padding-left: 20px;*/
		}
	}
	@media only screen and (max-width: 1200px) and (min-width: 1025px) {
		#sloc_leftBar, #sloc_Search-area{
			width:330px;
/*			padding-left: 20px;*/
		}
	}
	@media only screen and (max-width: 1400px) and (min-width: 1200px) {
		#sloc_leftBar, #sloc_Search-area{
			width:330px;
/*			padding-left: 20px;*/
		}
	}
	@media only screen and (max-width: 1500px) and (min-width: 1400px) {
		#sloc_leftBar, #sloc_Search-area{
			width:330px;
/*			padding-left: 20px;*/
		}
	}

	.giz_storeLocator{
		display:table;
		clear:both;
		width:100%;
		margin:0px auto;
/*		padding-left:20px;
*/		position:static !important;
	}
	#sloc_leftBar label{
		display:inline !important;
	}
	#sl_new_searchResult ul li {
		list-style: none !important;
		margin:0px !important;
		clear:both;
	}

	#sl_new_searchResult::-webkit-scrollbar
{
	width: 12px;
	background-color: #F5F5F5;
}

	.fl{
		float:left;
	}
	.fr{
		float:right;
	}
	.wd75{
		width: 75%;
	}
	.wd20{
		width: 20%;
	}
	.wd23{
		width: 23%;
	}
	.sl_error{
    padding: 3px 4px 1px;
    border:solid 1px #FBD3C6;
    background:#FDE4E1 !important;
    color:#CB4721;
    font-size:12px;
    font-weight:bold;
    text-align:center;
    display:none;
	width: 100%;
}
.sloc_button{
	font-size:100%;margin:0;line-height:1em;cursor:pointer;position:relative;font-family:inherit;overflow:none;padding:6px 10px;text-decoration:none;font-weight:bold;-webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px;left:auto;text-shadow:0 1px 0 #fff;color:#5e5e5e;text-shadow:0 1px 0 rgba(255,255,255,0.8);border:1px solid #c8bfc6;background:#f7f6f7;background:-webkit-gradient(linear,left top,left bottom,from(#f7f6f7),to(#e0dadf));background:-webkit-linear-gradient(#f7f6f7,#e0dadf);background:-moz-linear-gradient(center top,#f7f6f7 0,#e0dadf 100%);background:-moz-gradient(center top,#f7f6f7 0,#e0dadf 100%);white-space:nowrap;display:inline-block;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,0.075),inset 0 1px 0 rgba(255,255,255,0.3),0 1px 2px rgba(0,0,0,0.1);-moz-box-shadow:inset 0 -1px 0 rgba(0,0,0,0.075),inset 0 1px 0 rgba(255,255,255,0.3),0 1px 2px rgba(0,0,0,0.1);box-shadow:inset 0 -1px 0 rgba(0,0,0,0.075),inset 0 1px 0 rgba(255,255,255,0.3),0 1px 2px rgba(0,0,0,0.1);
}
.sl_msg_box{
	background:transparent;
    color: #CB4721;
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    padding: 2px 4px;
    text-align: center;
    width: 100%;
	height:19px;
}
.sl_error_cls{
	border:solid 1px #FBD3C6;
}
.sl_success{
    padding: 3px 4px 1px;
    border:solid 1px #C0F0B9;
    background:#D5FFC6 !important;
    color:#48A41C;
    font-size:12px;
    font-weight:bold;
    text-align:center;
    display:none;
	width: 100%;
}
.sl_clear {clear: both;}
.sl_pad5{
	padding:5px;
}
.sl_pad2{
	padding:2px;
}
#sl_front_map_canvas img, .gobackO img{
	-moz-box-shadow: none !important;
	-webkit-box-shadow: none !important;
	box-shadow: none !important;
	-ms-filter: none !important;
	filter: none !important;
	max-width: none !important;
}
.sl_logo {
    float:left;
    width:75px;
}
.sl_logo img
{
    background-color:#fff;
    padding:2px;
    border:1px solid #ccc;
    width:64px;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    margin-left:2px;
}
.lisAdd{
    float:left;
    padding-left:2px;
    width:80%;
	font-size:12px;
}
.arrright{
	background: url('<?php echo $this->plugin_url(); ?>/images/icon/showhide.png') -16px 0 no-repeat;
	display:block;
	outline:none;
	text-decoration: none;
}
.arrleft{
	background: url('<?php echo $this->plugin_url(); ?>/images/icon/showhide.png') no-repeat;
	display:block;
	outline:none;
	text-decoration: none;
}
.arrup{
	background: url('<?php echo $this->plugin_url(); ?>/images/icon/showhide.png') -5px -26px no-repeat;
	display:block;
	outline:none;
	text-decoration: none;
}
.arrdown{
	background: url('<?php echo $this->plugin_url(); ?>/images/icon/showhide.png') -5px -43px no-repeat;
	display:block;
	outline:none;
	text-decoration: none;
}
.arrleftL{
	background: url('<?php echo $this->plugin_url(); ?>/images/icon/showhide.png') -19px 0 no-repeat;
	display:block;
	outline:none;
	text-decoration: none;
}
.arrrightR{
	background: url('<?php echo $this->plugin_url(); ?>/images/icon/showhide.png') 0 0 no-repeat;
	display:block;
	outline:none;
	text-decoration: none;
}
.arrleft:hover,.arrright:hover, .arrrightR:hover, .arrleftL:hover{
	text-decoration: none !important;
}
a:active,a:focus,#hrHide:active, #hrHide:focus,.arrleft:active,.arrright:active, .arrrightR:active, .arrleftL:active,.arrleft:focus,.arrright:focus, .arrrightR:focus, .arrleftL:focus{
	outline : none;
}
.tootltipshow {
	display:block;
	background: url('<?php echo $this->plugin_url(); ?>/images/icon/share.png') no-repeat !important;
	height: 16px;
	width: 55px;
}
.tooltip{
   position:absolute;
   padding: 10px 13px;
   z-index: 2;

   color: #303030;
   background-color: #f5f5b5;
   border: 1px solid #DECA7E;

   font-family: sans-serif;
   font-size: 12px;
   line-height: 18px;
   text-align: center;
   visibility:visible;
   left: 140px;
   opacity:1 !important;
    /* IE 8 */
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)" !important;

  /* IE 5-7 */
  filter: alpha(opacity=100) !important;

  /* Netscape */
  -moz-opacity: 1 !important;

  /* Safari 1.x */
  -khtml-opacity: 1 !important;

}

   .tooltip h3{
      margin: 0 0 5px;
      text-align: left;
   }
   .tooltip .social_plugin div{
   float:left;
   width:30px;
   }

#sl_map_form {
		-moz-border-radius: 6px;
		-webkit-border-radius: 6px;
		-khtml-border-radius: 6px;
		border-radius: 6px;
        background: #eef2f7;
        border: 1px solid #536376;
        -webkit-box-shadow: rgba(0,0,0,.6) 0px 2px 12px;
        -moz-box-shadow:  rgba(0,0,0,.6) 0px 2px 12px;
		box-shadow: rgba(0,0,0,.6) 0px 2px 12px;
		-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#000000')";/* For IE 8 */
		filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#000000');/* For IE 5.5 - 7 */
        padding: 14px 22px;
        position: relative;
        display: none;
		text-align:left;
    }
    #sl_map_form #sl_map_form_form {
        margin-top: 0px;
    }
    #sl_map_form label {
        display: block;
        margin-bottom: 10px;
        color: #536376;
        font-size: .9em;
    }

    #sl_map_form label input {
        display: block;
        width: 95%;
        height: 30px;
        background-position: -201px 0;
        padding: 3px 8px;
        font-size: 1.2em;
        line-height: 31px;
    }
	.lblSpan{
		font-weight: bold;
		color: #fff;
		margin-left: 2px;
	}
 .panel{
	position: absolute;
	top: 90px;
	left:15px;
	height:400px;
	overflow:scroll;
	width:400px;
	background:#DFDFDF;
	display:none;
}
.DHead{
    border:1px solid #DFDFDF;
    height: 35px;
    width: 97.2%;
}
.sl_GetDirec{
	display:inline-block;
}
.dirhead{
	color: #000;
    margin: 0 !important;
	font-size: 14px !important;
    padding: 6px 5px;
    width: 80%;
}
/****************** For Browse View *********************/
.sloc_browse{
	list-style:none;
	margin:0px !important;
	padding: 0;
    width: 97.5%;
}
.sloc_browse li{
	text-transform: uppercase;
	color:#fff;
	font-weight:bold;
	margin:0px;
}
.sloc_browse li span.cu_span{
	background:url('<?php echo $this->plugin_url(); ?>/images/icon/arrow_sprite.png') -10px 6px no-repeat;
	width:15px;
	height:15px;
	display:block;
	margin-left: -6px;
    padding: 0;
	float:left;
}
.sloc_browse li a.naadu,.browse li a.naaduu{
	/*border:1px #DFDFDF solid;*/
	border-left: 1px solid #DFDFDF;
    border-right: 1px solid #DFDFDF;
    border-top: 1px solid #DFDFDF;
	padding: 5px;
	color:#000;
	display: block;
	font-size:12px;
}
.sloc_browse li:last-child a.naadu,.browse li:last-child a.naaduu, .sloc_browse li a.naadu.selectb, .browse li a.naaduu.selectb{
	border-bottom: 1px solid #DFDFDF;
}
.sloc_browse li a:hover{
	color:#C1DAD7;
	text-decoration:none;
}
.sloc_browse li.selected a.selectb{
	color:#1CAC31;
}
.sloc_browse li.selected span.cu_span{
	background:url('<?php echo $this->plugin_url(); ?>/images/icon/arrow_sprite.png') -10px -10px no-repeat;
	width:17px;
	height:15px;
	display:block;
	margin-left: -4px;
	float:left;
}
.state{
	list-style:none;
	margin:0 0 0 5px !important;
	padding: 0;
	width:98.5%;
}
.state li{
	text-transform: uppercase;
	color:#000;
	font-weight:bold;
    width: 99.1%;
	margin:0px;
}
.state li span.st_span{
	background:url('<?php echo $this->plugin_url(); ?>/images/icon/arrow_sprite.png') 6px 5px no-repeat;
	width:17px;
	height:15px;
	display:block;
	margin-left: -6px;
	float:left;
}

.state li.selected span.st_span{
	background:url('<?php echo $this->plugin_url(); ?>/images/icon/arrow_sprite.png') 4px -10px no-repeat;
	width:17px;
	height:15px;
	display:block;
	margin-left: -4px;
	float:left;
}

.state li a.maanelam,.state li a.maanelamm{
	border-bottom:1px #666666 solid;
	padding: 5px;
	color:#000;
	display: block;
	font-size:12px;
}
.state li a:hover{
	color:#0000FF !important;
	text-decoration:none;
}
.state li.selected a.selects{
	color:green;
}
.city{
	list-style:none;
	margin:0 0 0 3px !important;
	padding: 0;
}
.city li{
	text-transform: uppercase;
	color:#000;
	font-weight:bold;
    width: 99.5%;
	margin:0px;
}
.city li a.nagaram,.city li a.nagaramm{
	border-bottom:1px #DFDFDF solid;
	background:none;
	padding:5px;
	color:#000;
	display: block;
	font-size:12px;
}
.city li a:hover{
	color:#0000FF !important;
	text-decoration:none;
}
.city li.selected a.selectc{
	color:red;
}
.storelist{
	list-style:none;
	margin-top:13px;
	padding: 0;
}
.storelist li {
  text-transform: none !important;
  padding: 3px;
  border-bottom:1px #D2D2D2 solid;
  cursor:pointer;
  clear:both;
  float:left;
  font-weight:normal;
   width: 98.7%;
   margin:0;
}
 .storelist li a{
	border:none !important;
	border:none !important;
	padding:0px !important;
	display: inline;
    color: #527e19 !important;
	text-decoration: none;
	font-size:12px;
}

#close_x {
	width: 24px; height: 23px;
	overflow: hidden; line-height: 1000px;
	display: block;
	position: absolute; top: 5px; right: 5px;
	background-position: -387px -102px;
}
.sprited {
	background: url('<?php echo $this->plugin_url(); ?>/images/download_sprite.png') no-repeat;
	line-height: 1000px;
	overflow: hidden;
	display: block;
}
<?php } ?>
</style>
    <link rel='stylesheet' id='open-sans-css'  href='https://fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&#038;subset=latin%2Clatin-ext&#038;ver=4.5.3' type='text/css' media='all' />
    <link rel='stylesheet' href='<?php echo $this->plugin_url().'/css/styles.css'; ?>' type='text/css' />

<script src="<?php echo $this->plugin_url(); ?>/spinner/src/loadingoverlay.min.js"></script>
<?php /*        <script src="<?php echo $this->plugin_url(); ?>/spinner/src/loadingoverlay_progress.min.js"></script> */ ?>


<link rel="stylesheet" href="<?php echo $this->plugin_url(); ?>/multiselect/multiple-select.css" />
<script src="<?php echo $this->plugin_url(); ?>/multiselect/multiple-select.js"></script>



<script type="text/javascript">
if (typeof  giz_Locator == "undefined") {
	var  giz_Locator = {};
}
var main_path = "";
var contentScroll;
var searchMode 		= "<?php echo $SearchMode; ?>";
var DLatt 			= <?php echo $Latti; ?>;
var DLng			= <?php echo $Lngg; ?>;
var plugin_path 	= "<?php echo $this->plugin_url().'/'; ?>";
var admin_ajx 		= "<?php echo admin_url('admin-ajax.php'); ?>";
var site_url 		= "<?php echo site_url().'/'; ?>";
var url_query 		= "<?php echo $full_link; ?>";
main_path 			= "<?php echo $full_link; ?>";
var naviPos 		= "<?php echo $MapPosition; ?>";
var loadLoc 		= "<?php echo $LoadLoc; ?>";
var LogoVisible		= "<?php echo $LogoVisible; ?>";

var gMap;
google.maps.visualRefresh = true;
var $ = jQuery.noConflict();
jQuery(document).ready(function($) {
    var head = $('head')[0];
    var insertBefore = head.insertBefore;
    head.insertBefore = function (newElement, referenceElement) {
        if (newElement.href
            && newElement.href.indexOf('Roboto') === 0) {
            return;
        }
        if (newElement.tagName.toLowerCase() === 'style'
            && newElement.styleSheet
            && newElement.styleSheet.cssText
            && newElement.styleSheet.cssText.replace('\r\n', '').indexOf('.gm-style') === 0) {
            return;
        }
        if (newElement.tagName.toLowerCase() === 'style'
            && newElement.innerHTML
            && newElement.innerHTML.replace('\r\n', '').indexOf('.gm-style') === 0) {
            return;
        }
        insertBefore.call(head, newElement, referenceElement);
    };
<?php

    $sl_sql_str = "SELECT DISTINCT C.`categoryid` , C.`category` FROM `$sl_tb_storecat` AS C INNER JOIN `$sl_tb_stores` AS S ON S.`type` = C.`categoryid` ORDER BY 1 ASC";
    $sl_select_obj = $GLOBALS['wpdb']->get_results($sl_sql_str);
    $cats = [];
    foreach ($sl_select_obj as $sl_cate_row) {
        $cats[] = "'$sl_cate_row->categoryid':'$sl_cate_row->category'";
    }

?>
	var categories={<?php echo implode(',', $cats); ?>};
    var activeMarkers = [];
    var activeZipcodes = [];
    $("#sloc_bottomBar").hide();
    var fullSiteWidth = jQuery('.giz_storeLocator').width();
    jQuery('#primary').removeClass('site-content');
    /************** Load query string **************/
    var windowsHeight = jQuery(window).height();
    var windowsWidth = jQuery(window).width();
    var Lat = 12.97160;
    var Lan = 77.59456;
    var sl_type_query = "<?php echo $sl_qry_location; ?>";
    var sl_geo_query = "<?php echo $sl_qry_location_geo; ?>";
    if (sl_type_query != "" && sl_geo_query != "") {
        if (sl_type_query == 'Social') {
            if (searchMode == "SM") {
                if (sl_geo_query.length > 0) {
                    LoadWQ(sl_type_query, decodeURIComponent(sl_geo_query.replace(/\+/g, " ")));
                }
                else {
                    LoadWOQ();
                }
            }
            else if (searchMode == "BM") {
                if (sl_geo_query.length > 0) {
                    LoadWQBm(sl_type_query, decodeURIComponent(sl_geo_query.replace(/\+/g, " ")));
                }
                else {
                    LoadBrowse("All", '');
                }
            }
        } else {
            <?php if ($SearchMode == 'BM') {
    if ($sql_Nrow > 0) {?>
            LoadBrowse("All", '');
            <?php } else { ?>
            LoadWOQ();
            <?php } ?>
            <?php
} else { ?>
            LoadWOQ();
            <?php } ?>
        }
    }
    else {
        <?php if ($SearchMode == 'BM') {
        if ($sql_Nrow > 0) {?>
        LoadBrowse("All", '');
        <?php } else { ?>
        LoadWOQ();
        <?php } ?>
        <?php
    } else {
        if ($SearchMode == 'SM' && $LoadLoc == 1) {
            ?>
        LoadAllLocation();
        <?php
        } else { ?>
        LoadWOQ();
        <?php
        }
    } ?>

    }

    <?php if ($SearchMode == 'BM'): ?>

    <?php endif; ?>

    /**** Get Latitude and Longitude from Search Event ***/
    jQuery('#sl_nearStore').bind('click', function () {  
        if (jQuery('#sl_nearStore').attr('disabled') === 'disabled')
            return;
        jQuery('#sl_new_searchResult').empty();
        jQuery('.DHead').remove();
        jQuery('#sl_msgBox').text("");
        jQuery('#sl_msgBox').removeClass('sl_success').removeClass('sl_error');
        jQuery('#sl_new_searchResult').append("<ul class='sl_SearchR mapresults-list'></ul>");
        var geocoder = new google.maps.Geocoder();
        var lName = jQuery('#sloc_tbName').val();

        var address = jQuery('#sloc_tbPlace').val();
        var lCountry = jQuery('#sloc_tbCountry').val();
 
        var radian = jQuery('#sloc_selRadius').val();
        var CatId = [];
        if (jQuery('#sloc_selCategory').length > 0) {
            CatId = jQuery('#sloc_selCategory').multipleSelect("getSelects");			
            jQuery('#sloc_hdfOCatId').val(CatId);
        }
        else {
            CatId = [];
            jQuery('#sloc_hdfOCatId').val(CatId);
        }
        if (jQuery.trim(lCountry).length <= 0 && jQuery.trim(address).length <= 0 && jQuery.trim(lName).length <= 0) {
            jQuery('#sloc_tbPlace').addClass('sl_error_cls');
            jQuery('#sloc_tbPlace').focus();
            jQuery('#sl_msgBox').text("<?php _e('Enter the address', self::TEXT_DOMAIN); ?>.");
            jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
        }
        else if (jQuery.trim(radian).length <= 0) {
            jQuery('#sloc_selRadius').addClass('sl_error_cls');
            jQuery('#sloc_selRadius').focus();
            jQuery('#sl_msgBox').text("<?php _e('Select radius', self::TEXT_DOMAIN); ?>.");
            jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
        }
        else { 
            if (jQuery.trim(address).length <= 0) {
                if ((jQuery.trim(lCountry).length <= 0) && (jQuery.trim(lName).length > 0)) lCountry = 'USA';
                address = lCountry;
            }
            jQuery('#sloc_tbPlace,#sloc_selRadius').removeClass('sl_error_cls');

            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    if (radian == null || radian == '') {
                        radian = 2;
                    }
                    var RadType = (jQuery('input:radio[name=sloc_radius]:checked').val()) ? jQuery('input:radio[name=sloc_radius]:checked').val() : "Kms";
                    RadType = (RadType == 'KM') ? '<?php _e('Kms', self::TEXT_DOMAIN); ?>' : '<?php _e('Miles', self::TEXT_DOMAIN); ?>';
                    radian = (RadType === 'Miles') ? Math.round(radian * 1.609) : radian;
                    jQuery('#sl_nearStore').attr('disabled', 'disabled');

                    SearchStore(latitude, longitude, radian, RadType, CatId);
                }
                else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
                    jQuery('#sl_msgBox').text("<?php _e('Invalid address', self::TEXT_DOMAIN); ?>.");
                    jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
                    jQuery('#sl_nearStore').removeAttr('disabled');
                }
                else if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
                    jQuery('#sl_msgBox').text("<?php _e('Google API usage limit exceeded', self::TEXT_DOMAIN); ?>.");
                    jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
                    jQuery('#sl_nearStore').removeAttr('disabled');
                }
                else if (status == google.maps.GeocoderStatus.REQUEST_DENIED) {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
                    jQuery('#sl_msgBox').text("Invaild entry.");
                    jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
                    jQuery('#sl_nearStore').removeAttr('disabled');
                }
                else if (status == google.maps.GeocoderStatus.INVALID_REQUEST) {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
                    jQuery('#sl_msgBox').text("<?php _e('Google API error. Please try again', self::TEXT_DOMAIN); ?>.");
                    jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
                    jQuery('#sl_nearStore').removeAttr('disabled');
                }
                jQuery('#sloc_hdfOLat').val(latitude);
                jQuery('#sloc_hdfOLng').val(longitude);
                jQuery('#sloc_hdfAddress').val(address);
                jQuery('#sloc_hdfORad').val(jQuery('#sloc_selRadius').val());
                jQuery('#sloc_hdfORadTy').val(RadType);
            });

        }
    });

    /*********** Enable onsubmit event in Direction form ************/
    jQuery('#sl_frmGetDirection').keyup(function (e) {
        var key = (e.keyCode ? e.keyCode : e.which);
        if (key === 13) {
            getDir();
        }
    });
    jQuery('.wd75c, .wd75').keypress(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            jQuery('#sl_nearStore').click();
        }
    });
    setTimeout(function () {
        var actualHei = 0;
        var dropDHeight = 0;
        if (jQuery('.sloc_catDrop').height() > 1) {
            dropDHeight = jQuery('.sloc_catDrop').height();
        }
        if (jQuery('.inHeader').height() > 10) {
            actualHei = windowsHeight - jQuery('#sloc_frmSearch').height() - 24 - dropDHeight;
        }
        else {
            actualHei = windowsHeight - jQuery('#sloc_frmSearch').height() - 24 - dropDHeight;
        }
        jQuery("#sl_new_searchResult").niceScroll({horizrailenabled: false});
    }, 500);
    /****************** Open Direction Form **************/
    jQuery('.sl_GetDirection').on('click', function (e) {
        e.preventDefault();
        var fulladd = jQuery(this).attr('rel');
        var Latt = jQuery(this).attr('lat');
        var Lngg = jQuery(this).attr('lng');
        jQuery('#fromdir, #todir').val('');
        jQuery("#sl_map_form").lightbox_me({
            centered: true, onLoad: function () {
                jQuery("#sl_map_form").find("#fromdir").focus();
                jQuery("#sl_map_form").find('#todir').attr('readonly', 'readonly');
                jQuery("#sl_map_form").find("#todir").val(fulladd);
                jQuery("#sloc_hdfLat").val(Latt);
                jQuery("#sloc_hdfLng").val(Lngg);
            }
        });
    });
    /**************** go back to search panel ***********/
    jQuery('.gobackO').on('click', function (e) {
        e.preventDefault();
        jQuery('#sl_new_searchResult,.dirTitle').empty();
        jQuery('#sloc_frmSearch').show();
        jQuery('.dirTitle').hide();
        if (searchMode == 'SM') {
            jQuery('#sl_new_searchResult').append("<ul class='sl_SearchR'></ul>");
            jQuery('.DHead').remove();
            var RadType = jQuery.trim(jQuery('#sloc_hdfORadTy').val());
            if (RadType == "Kms") {
                jQuery('input[name=radius]:eq(0)').attr('checked', 'checked');
            } else {
                jQuery('input[name=radius]:eq(1)').attr('checked', 'checked');
            }
            RadType = (RadType == 'Kms') ? 'Kms' : 'Miles';
            var radian = jQuery('#sloc_hdfORad').val();
            var catId = 0;
            jQuery('#sloc_selRadius').val(jQuery('#sloc_hdfORad').val());
            if (jQuery('#sloc_selCategory').length > 0) {
                jQuery('#sloc_selCategory').val(jQuery('#sloc_hdfOCatId').val());
                catId = jQuery('#sloc_hdfOCatId').val();
            }
            radian = (RadType === 'Miles') ? Math.round(radian * 1.609) : radian;
            jQuery('#sloc_tbPlace').val(jQuery('#sloc_hdfAddress').val());
            if (jQuery('#sloc_hdfOLat').val().length > 0) {
                SearchStore(jQuery('#sloc_hdfOLat').val(), jQuery('#sloc_hdfOLng').val(), radian, RadType, catId);
            } else {
                <?php if ($SearchMode == 'SM' && $LoadLoc == 1) {
        ?>
                LoadAllLocation();
                <?php
    } else { ?>
                SearchStore(jQuery(this).attr('lat'), jQuery(this).attr('lng'), 0, "KM", catId);
                <?php } ?>
            }
            jQuery('#sl_nearStore').removeAttr('disabled');
        }
        else if (searchMode == 'BM') {
            jQuery('#sloc_frmSearch').show();
            jQuery('.sloc_catDrop').show();
            var CateId = 0;
            if (jQuery('#sloc_selCategory').length > 0) {
                CateId = jQuery('#sloc_hdfOCatId').val();
                jQuery('#sloc_selCategory').val(jQuery('#sloc_hdfOCatId').val());
            }
            else {
                CateId = 0;
            }
            LoadBrowse("All", '');
            jQuery('.Lappend').append("<img alt='' class='Loader' src='" + plugin_path + "images/icon/loading.gif' />");

            var sl_frontsearch_dal = {
                action: 'sl_dal_searchlocation',
                funMethod: 'BrowseList',
                SelectMet: 'country',
                selVal: '',
                CateId: CateId,
                Country_name: ''
            };
            giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, BindCountryList, "dd.", false);
        }
    });

    jQuery('.BM #sloc_selCategory').bind('change', function () {
        jQuery('#sl_new_searchResult,.dirTitle').empty();
        jQuery('.Lappend').append("<img alt='' class='Loader' src='" + plugin_path + "images/icon/loading.gif' />");
        LoadBrowse("All", '');
        var sl_frontsearch_dal = {
            action: 'sl_dal_searchlocation',
            funMethod: 'BrowseList',
            SelectMet: 'country',
            selVal: '',
            CateId: jQuery('#sloc_selCategory').val(),
            Country_name: ''
        };
        giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, BindCountryList, "dd.", false);
    });


    /*************** load country list for browse mode **************/
    function BindCountryList(returnVal) { 
        if (returnVal != null) {
            if (returnVal.length > 0) {
                <?php if ($isSingleCountry == 0) { ?>
                var content = "<ul class='sloc_browse'></ul>";
                jQuery('#sl_new_searchResult').append(content);
                for (var i = 0; i < returnVal.length; i++) {
                    var content_htm = "<li><a class='naadu' type='country' href=\"javascript:void(0);\" rel='" + returnVal[i]["country"] + "'><span class='cu_span'>&nbsp;</span>" + returnVal[i]["country"] + "</a></li>";
                    jQuery('ul.sloc_browse').append(content_htm);
                }
                setTimeout(function () {
                    jQuery('.Loader').fadeOut('slow').remove();
                }, 200);
                <?php } else {
        ?>
                var content = "<ul class='state'></ul>";
                jQuery('#sl_new_searchResult').append(content);
                for (var i = 0; i < returnVal.length; i++) {
                    var content_htm = "<li><a class='maanelam' type='State' href=\"javascript:void(0);\" rel='" + returnVal[i]["state"] + "'><span class='st_span'>&nbsp;</span>" + returnVal[i]["state"] + "</a></li>";
                    jQuery('ul.state').append(content_htm);
                }
                setTimeout(function () {
                    jQuery('.Loader').fadeOut('slow').remove();
                }, 200);

                <?php
    } ?>
            }
            else {
                jQuery('#sl_new_searchResult').append('<div class="sl_error sl_clear" style="width:95%; display:block;"><?php _e('No stores found', self::TEXT_DOMAIN); ?>.</div>');
                setTimeout(function () {
                    jQuery('.Loader').fadeOut('slow').remove();
                }, 200);
            }
        }
        else {
            jQuery('#sl_new_searchResult').append('<div class="sl_error sl_clear" style="width:95%; display:block;"><?php _e('No stores found', self::TEXT_DOMAIN); ?>.</div>');
            setTimeout(function () {
                jQuery('.Loader').fadeOut('slow').remove();
            }, 200);
        }
    }

    /************* load state list for corresponding country **********/
    jQuery('.sloc_browse li a.naadu').on('click', function () {
        if (!jQuery(this).hasClass('ajax_req')) {
            jQuery(this).addClass('ajax_req');
            var type = jQuery(this).attr('type');
            var resVal = jQuery(this).attr('rel');
            var country_name = resVal;
            jQuery('.sloc_browse li').removeClass('selected');
            jQuery(this).parent("li").addClass('selected');
            jQuery('.sloc_browse li a').removeClass('selectb');
            jQuery(this).addClass('selectb');
            jQuery('.Lappend').append("<img alt='' class='Loader' src='" + plugin_path + "images/icon/loading.gif' />");
            LoadBrowse(type, resVal);
            jQuery('ul.state').fadeOut(400, function () {
                jQuery(this).remove();
            });
            var CateId = 0;
            if (jQuery('#sloc_selCategory').length > 0) {
                CateId = jQuery('#sloc_selCategory').val();
                jQuery('#sloc_hdfOCatId').val(CateId);
            }
            else {
                CateId = 0;
                jQuery('#sloc_hdfOCatId').val(0);
            }
            var sl_frontsearch_dal = {
                action: 'sl_dal_searchlocation',
                funMethod: 'BrowseList',
                SelectMet: type,
                selVal: resVal,
                CateId: CateId,
                Country_name: country_name
            };
            giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, BindList, "dd.", false);
        }
    });

    function BindList(returnVal) {
        if (returnVal != null) {
            var content = "<ul class='state'></ul>";
            jQuery('.sloc_browse li.selected').append(content);
            for (var i = 0; i < returnVal.length; i++) {
                var content_htm = "<li><a type='State' href=\"javascript:void(0)\" class='maanelam' rel='" + returnVal[i]['state'] + "'><span class='st_span'>&nbsp;</span>" + returnVal[i]['state'] + "</a></li>";
                jQuery('ul.state').append(content_htm);
            }
            setTimeout(function () {
                jQuery('.Loader').fadeOut('slow').remove();
            }, 200);
            jQuery('.sloc_browse li a.naadu').removeClass('ajax_req');
        } else {
            jQuery('.sloc_browse li a.naadu').removeClass('ajax_req');
        }
    }

    /************* load address list for corresponding city **********/
    jQuery('.city li a.nagaram').on('click', function () {
        if (!jQuery(this).hasClass('ajax_req')) {
            var $thisEle = jQuery(this);
            jQuery(this).addClass('ajax_req');
            var type = jQuery(this).attr('type');
            var resVal = jQuery(this).attr('rel');
            var country_name = jQuery('.sloc_browse li.selected a').attr('rel');
            jQuery('.city li').removeClass('selected');
            jQuery(this).parent("li").addClass('selected');
            jQuery('.city li a').removeClass('selectc');
            jQuery(this).addClass('selectc');
            jQuery('.Lappend').append("<img alt='' class='Loader' src='" + plugin_path + "images/icon/loading.gif' />");
            jQuery('ul.storelist').fadeOut(400, function () {
                jQuery(this).remove();
            });
            LoadBrowse(type, resVal);
            setTimeout(function () {
                jQuery('.Loader').fadeOut('slow').remove();
                $thisEle.removeClass('ajax_req');
            }, 200);
        }
    });
    /************* load city list for corresponding state **********/
    jQuery('.state li a.maanelam').on('click', function () {
        if (!jQuery(this).hasClass('ajax_req')) {
            jQuery(this).addClass('ajax_req');
            var type = jQuery(this).attr('type');
            var resVal = jQuery(this).attr('rel');
            var country_name = jQuery('.sloc_browse li.selected a').attr('rel');

            jQuery('.state li').removeClass('selected');
            jQuery(this).parent("li").addClass('selected');
            jQuery('.state li a').removeClass('selects');
            jQuery(this).addClass('selects');
            jQuery('.Lappend').append("<img alt='' class='Loader' src='" + plugin_path + "images/icon/loading.gif' />");
            LoadBrowse(type, resVal);
            jQuery('ul.city').fadeOut(400, function () {
                jQuery(this).remove();
            });
            var CateId = 0;
            if (jQuery('#sloc_selCategory').length > 0) {
                CateId = jQuery('#sloc_selCategory').val();
                jQuery('#sloc_hdfOCatId').val(CateId);
            }
            else {
                CateId = 0;
                jQuery('#sloc_hdfOCatId').val(0);
            }
            var sl_frontsearch_dal = {
                action: 'sl_dal_searchlocation',
                funMethod: 'BrowseList',
                SelectMet: type,
                selVal: resVal,
                CateId: CateId,
                Country_name: country_name
            };

            giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, BindCtyList, "dd.", false);
        }
    });
    function BindCtyList(returnVal) {
        if (returnVal != null) {
            var content = "<ul class='city'></ul>";
            jQuery('.state li.selected').append(content);
            for (var i = 0; i < returnVal.length; i++) {
                var content_htm = "<li><a type='City' href=\"javascript:void(0)\" class='nagaram' rel='" + returnVal[i]['city'] + "'>" + returnVal[i]['city'] + "</a></li>";
                jQuery('ul.city').append(content_htm);
            }
            setTimeout(function () {
                jQuery('.Loader').fadeOut('slow').remove();
            }, 200);
            jQuery('.state li a.maanelam').removeClass('ajax_req');
        } else {
            jQuery('.state li a.maanelam').removeClass('ajax_req');
        }
    }


    /*** Load All Locations***/
    function LoadAllLocation() {
        var type = "All";
        var selValue = '';
        var CatId = 2;
        var mapOptions;
        fullSiteWidth = jQuery('.giz_storeLocator').width();
        if (fullSiteWidth >= 320 && fullSiteWidth <= 650) {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        } else if (fullSiteWidth > 650) {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: <?php echo $ZoomControl; ?>,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: <?php echo $PanControl; ?>,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: <?php echo $streetControl; ?>
            };
        } else {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        }
        var map = gMap = new google.maps.Map(document.getElementById("sl_front_map_canvas"), mapOptions);
        var bounds = new google.maps.LatLngBounds();

        var infoWindow = new google.maps.InfoWindow;
        var arrayData = [];
        var sl_frontsearch_dal = {
            action: 'sl_dal_searchlocation',
            funMethod: 'LoadMapAndSet',
            Location: type,
            selValue: selValue,
            CateId: CatId,
            Country_name: ''
        };

        giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, function (markers) {
            if (markers != null) {
                if (markers.length <= 0) {
                    var loc = {};
                    var mPoint;
                    var geocoder = new google.maps.Geocoder();
                    if (google.ClientLocation) {
                        loc.lat = google.ClientLocation.latitude;
                        loc.lng = google.ClientLocation.longitude;
                        mPoint = new google.maps.LatLng(parseFloat(loc.lat), parseFloat(loc.lng));
                    }
                    else {
                        mPoint = new google.maps.LatLng(parseFloat(DLatt), parseFloat(DLng));
                    }
                    map.setCenter(mPoint);
                    bounds.extend(mPoint);
                }
                else {

                    for (var i = 0; i < markers.length; i++) {
/*                        if (i == 0) console.log(markers[i]);*/
                        var storeId = markers[i]["id"];
                        var name = markers[i]["name"];
                        var address = markers[i]["FullAddress"];
                        var brand = markers[i]["type"];
                        var point = new google.maps.LatLng(parseFloat(markers[i]["lat"]), parseFloat(markers[i]["lng"]));
                        var Latt = markers[i]["lat"];
                        var Lngg = markers[i]["lng"];
                        var Phone = markers[i]["phone"];
                        var fax = markers[i]["fax"];
                        var email = markers[i]["email"];
                        var web = markers[i]["website"];
                        var logo = (markers[i]["logopath"] == null) ? "" : markers[i]["logopath"];
                        var iconImg = markers[i]["CategoryIcon"];
                        var labelId = parseInt(markers[i]["labelid"]);
                        var labelText = markers[i]["labeltext"];
                        var ImgUrl = markers[i]["imgurl"];

                        var markImage = new google.maps.MarkerImage();
                        if (iconImg != null) {
                            if (iconImg.length > 0) {
                                iconImg = plugin_path + iconImg;
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                            else {
                                iconImg = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                        }
                        var marker = new google.maps.Marker({
                            map: map,
                            position: point,
                            icon: iconImg,
                            animation: google.maps.Animation.DROP
                        });
                        bounds.extend(point);
                        var htmPhone = '';
                        var htmFax = '';
                        var htmEmail = '';
                        var htmWeb = '';
                        var htmLogo = '';
                        var phone_fax = '';
                        var htmLabel = '';
                        var add_width = (jQuery('#sloc_leftBar').width() - 105) + 'px';
                        var Total_str = "<?php _e('Address', self::TEXT_DOMAIN); ?> :" + address;
                        htmPhone = (Phone.length > 0) ? '<div class="sl_pad2"><label style="font-weight:bold;"><?php _e('Phone', self::TEXT_DOMAIN); ?> : </label><span> ' + Phone + '</span>, ' : '<div class="sl_pad2">';
                        if (Phone.length > 0) {
                            Total_str = Total_str + ',<?php _e('Phone', self::TEXT_DOMAIN); ?>: ' + Phone;
                        }
                        htmFax = (fax.length > 0) ? '<label style="font-weight:bold"><?php _e('Fax', self::TEXT_DOMAIN); ?> : </label><span>' + fax + '</span></div>' : '</div>';
                        if (fax.length > 0) {
                            Total_str = Total_str + ',<?php _e('Fax', self::TEXT_DOMAIN); ?> : ' + fax;
                        }
                        if (email.length > 0) {
                            htmEmail = '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Email', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"mailto:' + email + '\">' + email + '</a></span>&nbsp;&nbsp;&nbsp;<a href=\"https://mail.google.com/mail/?view=cm&fs=1&to=' + email + '&tf=1\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/Gmaillogo.png" alt="Email Client {requires email from infusionsoft}" title="Email Client {requires email from infusionsoft}"/></a></div>';
                            Total_str = Total_str + ',<?php _e('Email', self::TEXT_DOMAIN); ?> : ' + email;
                        }
                        /*
                         if(email.length   > 0){htmEmail  = '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Email', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"mailto:'+ email +'\">'+ email +'</a></span>&nbsp;&nbsp;&nbsp;<a href=\"https://mail.google.com/mail/?view=cm&fs=1&to='+ email +'&tf=1\" style=\'font-weight:bold;color: red\'>Gmail</a></div>'; Total_str = Total_str + ',<?php _e('Email', self::TEXT_DOMAIN); ?> : ' + email;}
                         */

                        if (web.length > 0) {
                            htmWeb = '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Website', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"' + web + '\" target="_blank">' + web + '</a></span></div>';
                            Total_str = Total_str + ',<?php _e('Website', self::TEXT_DOMAIN); ?> : ' + web;
                        }

                        <?php include_once 'extra.php'; add_js_extra(); ?>
                        if (logo.length > 0) {
                            htmLogo = '<div class="sl_logo" style="clear:both";><img src=' + plugin_path + logo + ' alt=' + name + ' title=' + name + ' /></div>';
                        }
                        else {
                            htmLogo = '<div class="sl_logo"><img src="' + plugin_path + 'Logo/logo_02.png" alt=' + name + ' title=' + name + ' /></div>';
                        }
                        <?php if ($LogoVisible != 1) { ?>
                        htmLogo = '';
                        add_width = '99%';
                        <?php } ?>
                        phone_fax = htmPhone + htmFax;
                        var path = window.location.protocol + "//" + window.location.host + '/products/StoreLocator/';
                        var orgin = window.location.protocol + "//" + window.location.host;
                        var likeUrl = encodeURIComponent(main_path + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"]);
                        var likeUrlN = main_path + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"];

                        var imageUrl = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false');
                        var imageUrlN = 'http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false';

                        var imageUrlFb = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=10&size=150x150&sensor=false');
                        var fb = '<div><a  href=\"javascript:void(0)\" title="http://www.facebook.com/sharer.php?s=100&p[url]=' + likeUrl + '&p[title]=' + encodeURIComponent(name) + '&p[summary]=' + encodeURIComponent(Total_str) + '&p[images][0]=' + imageUrlFb + '"  onclick="return facebookShare(this.title);" class="fb_share_link"><img border="0" src="' + plugin_path + 'images/icon/icon_fb.png" title="<?php _e('Share', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var pinit = '<div class="pinterest-pin-it-button"><a id=pinid' + i + ' title="http://pinterest.com/pin/create/button/?url=' + likeUrl + '&media=' + imageUrl + '&description=' + name + ' - ' + encodeURIComponent(Total_str) + '" class="pin-it-button" count-layout="horizontal" href=\"javascript:void(0)\" onclick="return pinIt(this.id,this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_pin.png" title="<?php _e('Pin It', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var tweet = '<div class="custom-tweet-button"><a href=\"javascript:void(0)\" title="https://twitter.com/intent/tweet?original_referer=' + encodeURIComponent(orgin) + '&source=tweetbutton&text=' + name + '&url=' + likeUrl + '&via=' + window.location.host + '" onclick="return TweetShare(this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_tweet.png" title="<?php _e('Tweet', self::TEXT_DOMAIN); ?>" /></a></div>';

                        fb = (markers[i]["facebook"] === "1") ? fb : "";
                        pinit = (markers[i]["printrest"] === "1") ? pinit : "";
                        tweet = (markers[i]["twitter"] === "1") ? tweet : "";
                        var Social = '<div id="social' + i + '" class="social_plugin">' + fb + pinit + tweet + '</div>';

                        var toolCss = "";
                        if (fb.length > 0 || pinit.length > 0 || tweet.length > 0) {
                            toolCss = "";
                        } else {
                            toolCss = "no";
                        }

                        var directionhtm = '<a href=\"javascript:void(0)\" rel="' + address + '" lat="' + Latt + '" lng="' + Lngg + '" class="sl_GetDirection fl sl_pad2 sl_gall" ><img src="' + plugin_path + 'images/icon/direction.png" alt="Get Directions" /></a>';
                        if (labelId > 1) {
                            htmLabel = '<div style="padding-left:18%"><div style="background:url(' + plugin_path + ImgUrl + ') no-repeat; text-align: center;width: 206px;margin-top:-15px; height:27px"><span style="display:block; padding-top:3px" class="lblSpan">' + labelText + '</span></div></div>';
                        }
                        /*
                         var html1 = '<div class="sl_clear sl_info_Div"><div class="sl_pad5"><span style="font-weight:bold;font-size:15px; float:left;width: 270px;display:block;padding-left: 2px;margin-bottom: 2px">'+ name +'</span></div><div class="clear"></div>'+ htmLogo +'<div class="lisAdd" style="width:'+ add_width +'">'+
                         '<div class="sl_pad5"><label style="font-weight:bold"><?php _e('Address', self::TEXT_DOMAIN); ?> : </label><span style="font-weight:normal">'+ address +'</span></div>'+ phone_fax + htmEmail + htmWeb +
                         '</div></div><div style="clear:both"><div class="fl">'+ directionhtm +'</div></div>';
                         */
                        var html1 = '<header class = "mapres-header" ><h4 class="mapres-title" > '+ name +'</h4><span class="mapres-miles" ></span></header><div class ="mapres-category mapres-c'+markers[i]["type"]+' round-corner">'+categories[markers[i]["type"]]+'</div>'
                            +'<div class="mapres-address" ><i class="mapres-sprite pin-icon" ></i> '+ address + '</div><div class="mapres-subinfo" > ' + directionhtm + '</div>';

                        bindInfoWindowN(marker, map, infoWindow, html1, i, false);
                        htmPhone = '';
                        htmFax = '';
                        htmEmail = '';
                        htmWeb = '';
                        htmLogo = '';

                        arrayData.push(Social);
                        if (directionhtm.length > 1) {
                            jQuery("a.sl_gall").each(function (i) {
                                jQuery(this).simpletip({
                                    fixed: true,
                                    content: jQuery(this).find('img').attr('alt')
                                });
                            });
                        }
                    }
                    setTimeout(function () {
                        UpdateSearchPnl();
                    }, 1000);

                }
                setTimeout(function () {
                    map.fitBounds(bounds);
                }, 3000);
            }
        });
    }


    /*** Load Browse Type Data***/
    function LoadBrowse(type, selValue) {
        if (type == "City") {
            var content = "<ul class='storelist'></ul>";
            jQuery('ul.city li.selected').append(content);
        }
        var CatId = 0;
        if (jQuery('#sloc_selCategory').length > 0) {
            CatId = jQuery('#sloc_selCategory').val();
            jQuery('#sloc_hdfOCatId').val(CatId);
        }
        else {
            CatId = 0;
            jQuery('#sloc_hdfOCatId').val(0);
        }
        var mapOptions;
        fullSiteWidth = jQuery('.giz_storeLocator').width();
        if (fullSiteWidth >= 320 && fullSiteWidth <= 650) {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        } else if (fullSiteWidth > 650) {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: <?php echo $ZoomControl; ?>,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: <?php echo $PanControl; ?>,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: <?php echo $streetControl; ?>
            };
        } else {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        }
        var map = gMap = new google.maps.Map(document.getElementById("sl_front_map_canvas"), mapOptions);
        var bounds = new google.maps.LatLngBounds();

        var infoWindow = new google.maps.InfoWindow;
        var arrayData = [];
        var countryName = (jQuery('.sloc_browse li.selected a').attr('rel') != undefined) ? jQuery('.sloc_browse li.selected a').attr('rel') : '';

        var sl_frontsearch_dal = {
            action: 'sl_dal_searchlocation',
            funMethod: 'LoadMapAndSet',
            Location: type,
            selValue: selValue,
            CateId: CatId,
            Country_name: countryName
        };

        giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, function (markers) {
            if (markers != null) {
                if (markers.length <= 0) {
                    var loc = {};
                    var mPoint;
                    var geocoder = new google.maps.Geocoder();
                    if (google.ClientLocation) {
                        loc.lat = google.ClientLocation.latitude;
                        loc.lng = google.ClientLocation.longitude;
                        mPoint = new google.maps.LatLng(parseFloat(loc.lat), parseFloat(loc.lng));
                    }
                    else {
                        mPoint = new google.maps.LatLng(parseFloat(DLatt), parseFloat(DLng));
                    }
                    map.setCenter(mPoint);
                    bounds.extend(mPoint);
                }
                else {
                    for (var i = 0; i < markers.length; i++) {
                        var storeId = markers[i]["id"];
                        var name = markers[i]["name"];
                        var address = markers[i]["FullAddress"];
                        var brand = markers[i]["type"];
                        var point = new google.maps.LatLng(parseFloat(markers[i]["lat"]), parseFloat(markers[i]["lng"]));
                        var Latt = markers[i]["lat"];
                        var Lngg = markers[i]["lng"];
                        var Phone = markers[i]["phone"];
                        var fax = markers[i]["fax"];
                        var email = markers[i]["email"];
                        var web = markers[i]["website"];
                        var logo = (markers[i]["logopath"] == null) ? "" : markers[i]["logopath"];
                        var iconImg = markers[i]["CategoryIcon"];
                        var labelId = parseInt(markers[i]["labelid"]);
                        var labelText = markers[i]["labeltext"];
                        var ImgUrl = markers[i]["imgurl"];

                        var markImage = new google.maps.MarkerImage();
                        if (iconImg != null) {
                            if (iconImg.length > 0) {
                                iconImg = plugin_path + iconImg;
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                            else {
                                iconImg = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                        }
                        var marker = new google.maps.Marker({
                            map: map,
                            position: point,
                            icon: iconImg,
                            animation: google.maps.Animation.DROP
                        });
                        bounds.extend(point);
                        var htmPhone = '';
                        var htmFax = '';
                        var htmEmail = '';
                        var htmWeb = '';
                        var htmLogo = '';
                        var phone_fax = '';
                        var htmLabel = '';
                        var add_width = (jQuery('#sloc_leftBar').width() - 105) + 'px';
                        var Total_str = "<?php _e('Address', self::TEXT_DOMAIN); ?> :" + address;
                        htmPhone = (Phone.length > 0) ? '<div class="sl_pad2"><label style="font-weight:bold;"><?php _e('Phone', self::TEXT_DOMAIN); ?> : </label><span> ' + Phone + '</span>, ' : '<div class="sl_pad2">';
                        if (Phone.length > 0) {
                            Total_str = Total_str + ',<?php _e('Phone', self::TEXT_DOMAIN); ?>: ' + Phone;
                        }
                        htmFax = (fax.length > 0) ? '<label style="font-weight:bold"><?php _e('Fax', self::TEXT_DOMAIN); ?> : </label><span>' + fax + '</span></div>' : '</div>';
                        if (fax.length > 0) {
                            Total_str = Total_str + ',<?php _e('Fax', self::TEXT_DOMAIN); ?> : ' + fax;
                        }
                        if (email.length > 0) {
                            htmEmail = '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Email', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"mailto:' + email + '\">' + email + '</a></span></div>';
                            Total_str = Total_str + ',<?php _e('Email', self::TEXT_DOMAIN); ?> : ' + email;
                        }
                        if (web.length > 0) {
                            htmWeb = '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Website', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"' + web + '\" target="_blank">' + web + '</a></span></div>';
                            Total_str = Total_str + ',<?php _e('Website', self::TEXT_DOMAIN); ?> : ' + web;
                        }

                        <?php include_once 'extra.php'; add_js_extra(); ?>

                        if (logo.length > 0) {
                            htmLogo = '<div class="sl_logo" style="clear:both";><img src=' + plugin_path + logo + ' alt=' + name + ' title=' + name + ' /></div>';
                        }
                        else {
                            htmLogo = '<div class="sl_logo"><img src="' + plugin_path + 'Logo/logo_02.png" alt=' + name + ' title=' + name + ' /></div>';
                        }
                        <?php if ($LogoVisible != 1) { ?>
                        htmLogo = '';
                        add_width = '99%';
                        <?php } ?>
                        phone_fax = htmPhone + htmFax;
                        var path = window.location.protocol + "//" + window.location.host + '/products/StoreLocator/';
                        var orgin = window.location.protocol + "//" + window.location.host;
                        var likeUrl = encodeURIComponent(main_path + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"]);
                        var likeUrlN = main_path + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"];

                        var imageUrl = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false');
                        var imageUrlN = 'http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false';

                        var imageUrlFb = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=10&size=150x150&sensor=false');
                        var fb = '<div><a  href=\"javascript:void(0)\" title="http://www.facebook.com/sharer.php?s=100&p[url]=' + likeUrl + '&p[title]=' + encodeURIComponent(name) + '&p[summary]=' + encodeURIComponent(Total_str) + '&p[images][0]=' + imageUrlFb + '"  onclick="return facebookShare(this.title);" class="fb_share_link"><img border="0" src="' + plugin_path + 'images/icon/icon_fb.png" title="<?php _e('Share', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var pinit = '<div class="pinterest-pin-it-button"><a id=pinid' + i + ' title="http://pinterest.com/pin/create/button/?url=' + likeUrl + '&media=' + imageUrl + '&description=' + name + ' - ' + encodeURIComponent(Total_str) + '" class="pin-it-button" count-layout="horizontal" href=\"javascript:void(0)\" onclick="return pinIt(this.id,this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_pin.png" title="<?php _e('Pin It', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var tweet = '<div class="custom-tweet-button"><a href=\"javascript:void(0)\" title="https://twitter.com/intent/tweet?original_referer=' + encodeURIComponent(orgin) + '&source=tweetbutton&text=' + name + '&url=' + likeUrl + '&via=' + window.location.host + '" onclick="return TweetShare(this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_tweet.png" title="<?php _e('Tweet', self::TEXT_DOMAIN); ?>" /></a></div>';

                        fb = (markers[i]["facebook"] === "1") ? fb : "";
                        pinit = (markers[i]["printrest"] === "1") ? pinit : "";
                        tweet = (markers[i]["twitter"] === "1") ? tweet : "";
                        var Social = '<div id="social' + i + '" class="social_plugin">' + fb + pinit + tweet + '</div>';

                        var toolCss = "";
                        if (fb.length > 0 || pinit.length > 0 || tweet.length > 0) {
                            toolCss = "";
                        } else {
                            toolCss = "no";
                        }

                        var directionhtm = '<a href=\"javascript:void(0)\" rel="' + address + '" lat="' + Latt + '" lng="' + Lngg + '" class="sl_GetDirection fl sl_pad2 sl_gall" ><img src="' + plugin_path + 'images/icon/direction.png" alt="Get Directions" /></a>';
                        if (labelId > 1) {
                            htmLabel = '<div style="padding-left:18%"><div style="background:url(' + plugin_path + ImgUrl + ') no-repeat; text-align: center;width: 206px;margin-top:-15px; height:27px"><span style="display:block; padding-top:3px" class="lblSpan">' + labelText + '</span></div></div>';
                        }
                        var html = '<div style="width:100%; height: 100%;min-height: 80px;" class="sl_clear" id =idadiv_' + i + '>' + htmLabel + '<div class="sl_pad5"><span style="font-weight:bold;font-size:15px; float:left;width: 270px;display:block;padding-left: 2px;margin-bottom: 2px">' + name + '</span></div>' + htmLogo + '<div class="lisAdd" style="width:' + add_width + '">' +
                            '<div class="sl_pad5"><label style="font-weight:bold"><?php _e('Address', self::TEXT_DOMAIN); ?> : </label><span style="font-weight:normal">' + address + '</span></div>' + phone_fax + htmEmail + htmWeb +
                            '</div></div></div><div style="clear:both"><div class="fl">' + directionhtm + '</div><div class="fr"><a href=\"#\" id="tool' + i + '" class="tootltipshow' + toolCss + '" style="float:right;" ></a></div></div>';
                        var html1 = '<div class="sl_clear sl_info_Div"><div class="sl_pad5"><span style="font-weight:bold;font-size:15px; float:left;width: 270px;display:block;padding-left: 2px;margin-bottom: 2px">' + name + '</span></div><div class="clear"></div>' + htmLogo + '<div class="lisAdd" style="width:' + add_width + '">' +
                            '<div class="sl_pad5"><label style="font-weight:bold"><?php _e('Address', self::TEXT_DOMAIN); ?> : </label><span style="font-weight:normal">' + address + '</span></div>' + phone_fax + htmEmail + htmWeb +
                            '</div></div><div style="clear:both"><div class="fl">' + directionhtm + '</div></div>';
                        var CSSclass = '';
                        if (type == "City") {
                            jQuery('<li  id =ida_' + i + ' class=' + CSSclass + '></li>').html(html).appendTo('.storelist');
                            bindInfoWindowN(marker, map, infoWindow, html1, i, true);
                        } else {
                            bindInfoWindowN(marker, map, infoWindow, html1, i, false);
                        }
                        htmPhone = '';
                        htmFax = '';
                        htmEmail = '';
                        htmWeb = '';
                        htmLogo = '';

                        arrayData.push(Social);
                        if (directionhtm.length > 1) {
                            jQuery("a.sl_gall").each(function (i) {
                                jQuery(this).simpletip({
                                    fixed: true,
                                    content: jQuery(this).find('img').attr('alt')
                                });
                            });
                        }

                        jQuery(".storelist a.tootltipshow").each(function (i) {
                            jQuery(this).simpletip({
                                persistent: true,
                                focus: true,
                                position: 'left',
                                fixed: true,
                                content: arrayData[i]
                            });
                            jQuery(this).on('click', function (e) {
                                e.preventDefault();
                            });
                        });
                    }
                    setTimeout(function () {
                        UpdateSearchPnl();
                    }, 1000);
                }
                setTimeout(function () {
                    map.fitBounds(bounds);
                }, 3000);
            }
        });
    }

    /*** Load Data Without Query string ***/
    function LoadWOQ() {
        var sl_frontsearch_dal = {action: 'sl_dal_searchlocation', funMethod: 'LoadWithoutQuery', Location: 'Load'};
        giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, function (markers) {
            if (markers != null) {
                if (markers.length < 0) {

                }
                else {
                    for (var i = 0; i < markers.length; i++) {
                        Lat = parseFloat(markers[i]["lat"]),
                            Lan = parseFloat(markers[i]["lng"]);
                    }
                    initialize_map(Lat, Lan);
                }
            }
        });
    }

    /*** Load Data With Query string ***/
    function LoadWQ(Loc, store) {
        LatLng = store.split("~");
        initialize_mapQ(Loc, LatLng[0], LatLng[2], store);
    }

    /*** Load Data With Query string ***/
    function LoadWQBm(Loc, store) {
        LatLng = store.split("~");
        LoadIniBrowse(Loc, LatLng[0], LatLng[2], store);
    }

    function LoadIniBrowse(loc, lat, lan, store) {
        jQuery('#sl_new_searchResult').empty();
        jQuery('.sloc_catDrop').hide();
        fullSiteWidth = jQuery('.giz_storeLocator').width();
        var mapOptions = {
            zoom: <?php echo $ZoomLevel; ?>,
            mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
            zoomControl: <?php echo $ZoomControl; ?>,
            scaleControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            panControl: <?php echo $PanControl; ?>,
            panControlOptions: {
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            streetViewControl: <?php echo $streetControl; ?>
        };

        if (fullSiteWidth >= 320 && fullSiteWidth <= 650) {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        } else if (fullSiteWidth > 650) {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: <?php echo $ZoomControl; ?>,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: <?php echo $PanControl; ?>,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: <?php echo $streetControl; ?>
            };
        } else {
            mapOptions = {
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        }
        var map = gMap = new google.maps.Map(document.getElementById("sl_front_map_canvas"), mapOptions);
        var bounds = new google.maps.LatLngBounds();
        var infoWindow = new google.maps.InfoWindow;
        var arrayData = [];
        var sl_frontsearch_dal = {
            action: 'sl_dal_searchlocation',
            funMethod: 'SearchStore',
            Location: 'Social',
            StoreLocation: store
        };
        giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, function (markers) {
            if (markers.length != null) {
                if (markers.length <= 0) {

                }
                else {
                    for (var i = 0; i < markers.length; i++) {
                        var storeId = markers[i]["id"];
                        var name = markers[i]["name"];
                        var address = markers[i]["FullAddress"];
                        var brand = markers[i]["type"];
                        var point = new google.maps.LatLng(parseFloat(markers[i]["lat"]), parseFloat(markers[i]["lng"]));
                        var Latt = markers[i]["lat"];
                        var Lngg = markers[i]["lng"];
                        var Phone = markers[i]["phone"];
                        var fax = markers[i]["fax"];
                        var email = markers[i]["email"];
                        var web = markers[i]["website"];
                        var logo = markers[i]["logopath"];
                        var iconImg = markers[i]["CategoryIcon"];
                        var labelId = parseInt(markers[i]["labelid"]);
                        var labelText = markers[i]["labeltext"];
                        var ImgUrl = markers[i]["imgurl"];

                        var main_html = "<div style='padding:5px'><a href=\"index.php\" ><img src='" + plugin_path + "images/icon/home.png' alt='<?php _e('Back to browse', self::TEXT_DOMAIN); ?>' title='<?php _e('Back to browse', self::TEXT_DOMAIN); ?>'/></a></div><ul class='sloc_browse'><li class='selected'><a class='naaduu selectb' href=\"javascript:void(0)\" onclick='return false' rel='" + markers[i]['country'] + "' type='country'>" + markers[i]['country'] + "</a>" +
                            "<ul class='state'>" +
                            "<li class='selected'><a rel=" + markers[i]['state'] + " class='maanelamm selects' href=\"javascript:void(0)\" type='State' onclick='return false'>" + markers[i]['state'] + "</a>" +
                            "<ul class='city'>" +
                            "<li class='selected'><a rel=" + markers[i]['city'] + " class='nagaramm selectc' href=\"javascript:void(0)\" type='City' onclick='return false'>" + markers[i]['city'] + "</a>" +
                            "<ul class='storelist'></ul>" +
                            "</li>" +
                            "</ul>" +
                            "</li>" +
                            "</ul>" +
                            "</li></ul>";
                        jQuery('#sl_new_searchResult').append(main_html);
                        var markImage = new google.maps.MarkerImage();
                        if (iconImg != null) {
                            if (iconImg.length > 0) {
                                iconImg = plugin_path + iconImg;
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                            else {
                                iconImg = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                        }
                        var marker = new google.maps.Marker({
                            map: map,
                            position: point,
                            icon: iconImg,
                            animation: google.maps.Animation.DROP
                        });
                        bounds.extend(point);
                        var htmPhone = '';
                        var htmFax = '';
                        var htmEmail = '';
                        var htmWeb = '';
                        var htmLogo = '';
                        var phone_fax = '';
                        var htmLabel = '';
                        var add_width = (jQuery('#sloc_leftBar').width() - 105) + 'px';
                        var Total_str = "<?php _e('Address', self::TEXT_DOMAIN); ?> :" + address;
                        htmPhone = (Phone.length > 0) ? '<div class="sl_pad2"><label style="font-weight:bold;"><?php _e('Phone', self::TEXT_DOMAIN); ?> : </label><span> ' + Phone + '</span>, ' : '<div class="sl_pad2">';
                        if (Phone.length > 0) {
                            Total_str = Total_str + ',<?php _e('Phone', self::TEXT_DOMAIN); ?>: ' + Phone;
                        }
                        htmFax = (fax.length > 0) ? '<label style="font-weight:bold"><?php _e('Fax', self::TEXT_DOMAIN); ?> : </label><span>' + fax + '</span></div>' : '</div>';
                        if (fax.length > 0) {
                            Total_str = Total_str + ',<?php _e('Fax', self::TEXT_DOMAIN); ?> : ' + fax;
                        }
                        if (email.length > 0) {
                            htmEmail = '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Email', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"mailto:' + email + '\">' + email + '</a></span></div>';
                            Total_str = Total_str + ',<?php _e('Email', self::TEXT_DOMAIN); ?> : ' + email;
                        }
                        if (web.length > 0) {
                            htmWeb = '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Website', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"' + web + '\" target="_blank">' + web + '</a></span></div>';
                            Total_str = Total_str + ',<?php _e('Website', self::TEXT_DOMAIN); ?> : ' + web;
                        }
                        <?php include_once 'extra.php'; add_js_extra(); ?>
                        if (logo.length > 0) {
                            htmLogo = '<div class="sl_logo sl_clear"><img src=' + plugin_path + logo + ' alt=' + name + ' title=' + name + ' /></div>';
                        }
                        else {
                            htmLogo = '<div class="sl_logo" style="clear:both;"><img src="' + plugin_path + 'logo_02.png" alt=' + name + ' title=' + name + ' /></div>';
                        }
                        <?php if ($LogoVisible != 1) { ?>
                        htmLogo = '';
                        add_width = '99%';
                        <?php } ?>
                        phone_fax = htmPhone + htmFax;
                        var path = window.location.protocol + "//" + window.location.host + '/products/StoreLocator/';
                        var orgin = window.location.protocol + "//" + window.location.host;
                        var likeUrl = encodeURIComponent(main_path + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"]);
                        var likeUrlN = main_path + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"];

                        var imageUrl = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false');
                        var imageUrlN = 'http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false';

                        var imageUrlFb = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=10&size=150x150&sensor=false');
                        var fb = '<div><a  href=\"javascript:void(0)\" title="http://www.facebook.com/sharer.php?s=100&p[url]=' + likeUrl + '&p[title]=' + encodeURIComponent(name) + '&p[summary]=' + encodeURIComponent(Total_str) + '&p[images][0]=' + imageUrlFb + '"  onclick="return facebookShare(this.title);" class="fb_share_link"><img border="0" src="' + plugin_path + 'images/icon/icon_fb.png" title="<?php _e('Share', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var pinit = '<div class="pinterest-pin-it-button"><a id=pinid' + i + ' title="http://pinterest.com/pin/create/button/?url=' + likeUrl + '&media=' + imageUrl + '&description=' + name + ' - ' + encodeURIComponent(Total_str) + '" class="pin-it-button" count-layout="horizontal" href=\"javascript:void(0)\" onclick="return pinIt(this.id,this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_pin.png" title="<?php _e('Pin It', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var tweet = '<div class="custom-tweet-button"><a href=\"javascript:void(0)\" title="https://twitter.com/intent/tweet?original_referer=' + encodeURIComponent(orgin) + '&source=tweetbutton&text=' + encodeURIComponent(name) + '&url=' + likeUrl + '&via=' + window.location.host + '" onclick="return TweetShare(this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_tweet.png" title="<?php _e('Tweet', self::TEXT_DOMAIN); ?>" /></a></div>';


                        fb = (markers[i]["facebook"] === "1") ? fb : "";
                        pinit = (markers[i]["printrest"] === "1") ? pinit : "";
                        tweet = (markers[i]["twitter"] === "1") ? tweet : "";
                        var Social = '<div id="social' + i + '" class="social_plugin">' + fb + pinit + tweet + '</div>';

                        var toolCss = "";
                        if (fb.length > 0 || pinit.length > 0 || tweet.length > 0) {
                            toolCss = "";
                        } else {
                            toolCss = "no";
                        }

                        if (labelId > 1) {
                            htmLabel = '<div style="padding-left:18%"><div style="background:url(' + plugin_path + ImgUrl + ') no-repeat; text-align: center;width: 206px;margin-top:-15px; height:27px"><span style="display:block; padding-top:7px" class="lblSpan">' + labelText + '</span></div></div>';
                        }

                        var directionhtm = '<a href=\"javascript:void(0)\" rel="' + address + '" lat="' + Latt + '" lng="' + Lngg + '" class="sl_GetDirection fl sl_pad2 sl_gall" ><img src="' + plugin_path + 'images/icon/direction.png" alt="<?php _e('Get Directions', self::TEXT_DOMAIN); ?>" /></a>';

                        var html = '<div style="width:100%; height: 100%;min-height: 80px;" class="sl_clear" id =idadiv_' + i + '>' + htmLabel + '<div class="sl_pad5"><span style="font-weight:bold;font-size:15px; float:left;width: 270px;display:block;padding-left: 2px;margin-bottom: 2px">' + name + '</span></div>' + htmLogo + '<div class="lisAdd" style="width:' + add_width + '">' +
                            '<div class="sl_pad5"><label style="font-weight:bold"><?php _e('Address', self::TEXT_DOMAIN); ?> : </label><span style="font-weight:normal">' + address + '</span></div>' + phone_fax + htmEmail + htmWeb +
                            '</div></div></div><div style="clear:both"><div class="fl">' + directionhtm + '</div><div class="fr"><a href=\"#\" id="tool' + i + '" class="tootltipshow' + toolCss + '" style="float:right;" ></a></div></div>';
                        var html1 = '<div class="sl_clear sl_info_Div"><div class="sl_pad5"><span style="font-weight:bold;font-size:15px; float:left;width: 270px;display:block;padding-left: 2px;margin-bottom: 2px">' + name + '</span></div><div class="clear"></div>' + htmLogo + '<div class="lisAdd" style="width:' + add_width + '">' +
                            '<div class="sl_pad5"><label style="font-weight:bold"><?php _e('Address', self::TEXT_DOMAIN); ?> : </label><span style="font-weight:normal">' + address + '</span></div>' + phone_fax + htmEmail + htmWeb +
                            '</div></div><div style="clear:both"><div class="fl">' + directionhtm + '</div></div>';
                        var CSSclass = '';
                        if (i % 2 == 0) {
                            CSSclass = 'even';
                        }
                        else if (i % 2 == 1) {
                            CSSclass = 'odd';
                        }
                        jQuery('<li  id =ida_' + i + ' class=' + CSSclass + '></li>').html(html).appendTo('.storelist');
                        bindInfoWindowN(marker, map, infoWindow, html1, i, true);

                        htmPhone = '';
                        htmFax = '';
                        htmEmail = '';
                        htmWeb = '';
                        htmLogo = '';

                        arrayData.push(Social);
                        if (directionhtm.length > 1) {
                            jQuery("a.sl_gall").each(function (i) {
                                jQuery(this).simpletip({
                                    fixed: true,
                                    content: jQuery(this).find('img').attr('alt')
                                });
                            });
                        }

                        jQuery(".storelist a.tootltipshow").each(function (i) {
                            jQuery(this).simpletip({
                                persistent: true,
                                focus: true,
                                position: 'left',
                                fixed: true,
                                content: arrayData[i]
                            });
                            jQuery(this).on('click', function (e) {
                                e.preventDefault();
                            });
                        });
                    }
                    setTimeout(function () {
                        UpdateSearchPnl();
                    }, 1000);
                }
                setTimeout(function () {
                    map.fitBounds(bounds);
                }, 3000);
            }
        });

    }

    /*** Make Marker for Query string data ***/
    function initialize_mapQ(loc, lat, lan, store) {
        var windowsHeight = jQuery(window).height();
        var windowsWidth = jQuery(window).width();
        fullSiteWidth = jQuery('.giz_storeLocator').width();
        var mapOptions = {
            center: new google.maps.LatLng(lat, lan),
            zoom: <?php echo $ZoomLevel; ?>,
            mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
            zoomControl: <?php echo $ZoomControl; ?>,
            scaleControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            panControl: <?php echo $PanControl; ?>,
            panControlOptions: {
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            streetViewControl: <?php echo $streetControl; ?>
        };

        if (fullSiteWidth >= 320 && fullSiteWidth <= 650) {
            mapOptions = {
                center: new google.maps.LatLng(lat, lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        } else if (fullSiteWidth > 650) {
            mapOptions = {
                center: new google.maps.LatLng(lat, lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: <?php echo $ZoomControl; ?>,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: <?php echo $PanControl; ?>,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: <?php echo $streetControl; ?>
            };
        } else {
            mapOptions = {
                center: new google.maps.LatLng(lat, lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        }

        var map = gMap = new google.maps.Map(document.getElementById("sl_front_map_canvas"), mapOptions);
        var bounds = new google.maps.LatLngBounds();
        var infoWindow = new google.maps.InfoWindow;
        var arrayData = [];
        var sl_frontsearch_dal = {
            action: 'sl_dal_searchlocation',
            funMethod: 'SearchStore',
            Location: 'Social',
            StoreLocation: store
        };
        var point = new google.maps.LatLng(lat, lan);
        var markImage = new google.maps.MarkerImage();
        iconImg = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
        markImage = new google.maps.MarkerImage(iconImg);
        var marker = new google.maps.Marker({
            map: map,
            position: point, icon: iconImg,
            animation: google.maps.Animation.DROP
        });

        giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, function (markers) {
            if (markers.length != null) {
                if (markers.length <= 0) {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
                    jQuery('#sl_msgBox').text("<?php _e('No stores found', self::TEXT_DOMAIN); ?>..");
                    jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
                }
                else {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
                    if (markers.length == 1) {
                        jQuery('#sl_msgBox').text(markers.length + " <?php _e('Store found', self::TEXT_DOMAIN); ?>.");
                    } else {
                        jQuery('#sl_msgBox').text(markers.length + " <?php _e('Stores found', self::TEXT_DOMAIN); ?>.");
                    }
                    jQuery('#sl_msgBox').removeClass('sl_error').addClass('sl_success').fadeOut(300).fadeIn(300);
                    for (var i = 0; i < markers.length; i++) {

                        var storeId = markers[i]["id"];
                        var name = markers[i]["name"];
                        var address = markers[i]["FullAddress"];
                        var brand = markers[i]["type"];
                        var point = new google.maps.LatLng(parseFloat(markers[i]["lat"]), parseFloat(markers[i]["lng"]));
                        var Phone = markers[i]["phone"];
                        var Latt = parseFloat(markers[i]["lat"]);
                        var Lngg = parseFloat(markers[i]["lng"]);
                        var fax = markers[i]["fax"];
                        var email = markers[i]["email"];
                        var web = markers[i]["website"];
                        var logo = markers[i]["logopath"];
                        var iconImg = markers[i]["categoryicon"];
                        var labelId = parseInt(markers[i]["labelid"]);
                        var labelText = markers[i]["labeltext"];
                        var ImgUrl = markers[i]["imgurl"];

                        var markImage = new google.maps.MarkerImage();
                        if (iconImg != null) {
                            if (iconImg.length > 0) {
                                iconImg = plugin_path + iconImg;
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                            else {
                                iconImg = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                        }
                        var marker = new google.maps.Marker({
                            map: map,
                            position: point,
                            icon: iconImg,
                            animation: google.maps.Animation.DROP
                        });
                        bounds.extend(point);
                        var htmPhone = '';
                        var htmFax = '';
                        var htmEmail = '';
                        var htmWeb = '';
                        var htmLogo = '';
                        var phone_fax = '';
                        var htmLabel = '';
                        var add_width = (jQuery('#sloc_leftBar').width() - 105) + 'px';
                        var Total_str = "<?php _e('Address', self::TEXT_DOMAIN); ?> : " + address;
                        htmPhone = (Phone.length > 0) ? '<div class="sl_pad2"><label style="font-weight:bold;"><?php _e('Phone', self::TEXT_DOMAIN); ?> : </label><span> ' + Phone + '</span>, ' : '<div class="sl_pad2">';
                        if (Phone.length > 0) {
                            Total_str = Total_str + ',<?php _e('Phone', self::TEXT_DOMAIN); ?>: ' + Phone;
                        }
                        htmFax = (fax.length > 0) ? '<label style="font-weight:bold"><?php _e('Fax', self::TEXT_DOMAIN); ?> : </label><span>' + fax + '</span></div>' : '</div>';
                        if (fax.length > 0) {
                            Total_str = Total_str + ',<?php _e('Fax', self::TEXT_DOMAIN); ?> : ' + fax;
                        }
                        if (email.length > 0) {
                            htmEmail = '<div class="sl_pad2 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Email', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"mailto:' + email + '\">' + email + '</a></span></div>';
                            Total_str = Total_str + ',<?php _e('Email', self::TEXT_DOMAIN); ?> : ' + email;
                        }
                        if (web.length > 0) {
                            htmWeb = '<div class="sl_pad2 sl_clear"><label style="font-weight:bold;display: block; float: left; margin: 0; width: 53px;"><?php _e('Website', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"' + web + '\" target="_blank">' + web + '</a></span></div>';
                            Total_str = Total_str + ',<?php _e('Website', self::TEXT_DOMAIN); ?> : ' + web;
                        }
                        <?php include_once 'extra.php'; add_js_extra(); ?>
                        if (logo.length > 0) {
                            htmLogo = '<div class="sl_logo" style="clear:both";><img src=' + plugin_path + logo + ' alt=' + name + ' title=' + name + ' /></div>';
                        }
                        else {
                            htmLogo = '<div class="sl_logo"><img src="' + plugin_path + 'Logo/logo_02.png" alt=' + name + ' title=' + name + ' /></div>';
                        }

                        <?php if ($LogoVisible != 1) { ?>
                        htmLogo = '';
                        add_width = '99%';
                        <?php } ?>

                        phone_fax = htmPhone + htmFax;

                        var orgin = site_url;
                        var qs = url_query.split('?')[1];
                        if (typeof qs !== 'undefined') {
                            var likeUrl = encodeURIComponent(url_query + '&Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"]);
                            var likeUrlN = url_query + '&Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"];
                        } else {
                            var likeUrl = encodeURIComponent(url_query + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"]);
                            var likeUrlN = url_query + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"];
                        }
                        var imageUrl = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false');
                        var imageUrlN = 'http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false';
                        var imageUrlFb = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=10&size=150x150&sensor=false');
                        var fb = '<div><a  href=\"javascript:void(0)\" title="http://www.facebook.com/sharer.php?s=100&p[url]=' + likeUrl + '&p[title]=' + encodeURIComponent(name) + '&p[summary]=' + encodeURIComponent(Total_str) + '&p[images][0]=' + imageUrlFb + '"  onclick="return facebookShare(this.title);" class="fb_share_link"><img border="0" src="' + plugin_path + 'images/icon/icon_fb.png" title="<?php _e('Share', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var pinit = '<div class="pinterest-pin-it-button"><a id=pinid' + i + ' title="http://pinterest.com/pin/create/button/?url=' + likeUrl + '&media=' + imageUrl + '&description=' + name + ' - ' + encodeURIComponent(Total_str) + '" class="pin-it-button" count-layout="horizontal" href=\"javascript:void(0)\" onclick="return pinIt(this.id,this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_pin.png" title="<?php _e('Pin It', self::TEXT_DOMAIN); ?>" /></a></div>';

                        var tweet = '<div class="custom-tweet-button"><a href=\"javascript:void(0)\" title="https://twitter.com/intent/tweet?original_referer=' + encodeURIComponent(orgin) + '&source=tweetbutton&text=' + name + '&url=' + likeUrl + '&via=' + window.location.host + '" onclick="return TweetShare(this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_tweet.png" title="<?php _e('Tweet', self::TEXT_DOMAIN); ?>" /></a></div>';

                        fb = (markers[i]["facebook"] === "1") ? fb : "";
                        pinit = (markers[i]["printrest"] === "1") ? pinit : "";
                        tweet = (markers[i]["twitter"] === "1") ? tweet : "";
                        var Social = '<div id="social' + i + '" class="social_plugin">' + fb + pinit + tweet + '</div>';

                        var toolCss = "";
                        if (fb.length > 0 || pinit.length > 0 || tweet.length > 0) {
                            toolCss = "";
                        } else {
                            toolCss = "no";
                        }

                        if (labelId > 1) {
                            htmLabel = '<div style="padding-left:18%"><div style="background:url(' + plugin_path + ImgUrl + ') no-repeat; text-align: center;width: 206px;margin-top:-15px; height:27px"><span style="display:block; padding-top:7px" class="lblSpan">' + labelText + '</span></div></div>';
                        }

                        var directionhtm = '<a href=\"javascript:void(0)\" rel="' + address + '" lat="' + Latt + '" lng="' + Lngg + '" class="sl_GetDirection fl sl_pad2 sl_gall" ><img src="' + plugin_path + 'images/icon/direction.png" alt="<?php _e('Get Directions', self::TEXT_DOMAIN); ?>" /></a>';

                        var html = '<div style="width:100%; height: 100%;min-height: 80px;" class="sl_clear" id =idadiv_' + i + '>' + htmLabel + '<div class="sl_pad2"><span style="font-weight:bold;font-size:12px; float:left;width: 99%;display:block;padding-left: 2px;margin-bottom: 2px">' + name + '</span></div>' + htmLogo + '<div class="lisAdd" style="width:' + add_width + '">' +
                            '<div class="sl_pad2"><label style="font-weight:bold"><?php _e('Address', self::TEXT_DOMAIN); ?> : </label><span style="font-weight:normal">' + address + '</span></div>' + phone_fax + htmEmail + htmWeb +
                            '</div></div></div><div style="clear:both"><div class="fl">' + directionhtm + '</div><div class="fr"><a href=\"#\" id="tool' + i + '" class="tootltipshow' + toolCss + '" style="float:right;" ></a></div></div>';
                        var html1 = '<div class="sl_clear sl_info_Div"><div class="sl_pad2"><span style="font-weight:bold;font-size:12px; float:left;width: 99%;display:block;padding-left: 2px;margin-bottom: 2px">' + name + '</span></div><div class="clear"></div>' + htmLogo + '<div class="lisAdd" style="width:' + add_width + '">' +
                            '<div class="sl_pad2"><label style="font-weight:bold"><?php _e('Address', self::TEXT_DOMAIN); ?> : </label><span style="font-weight:normal">' + address + '</span></div>' + phone_fax + htmEmail + htmWeb +
                            '</div></div><div style="clear:both"><div class="fl">' + directionhtm + '</div></div>';
                        var CSSclass = '';
                        if (i % 2 == 0) {
                            CSSclass = 'even';
                        }
                        else if (i % 2 == 1) {
                            CSSclass = 'odd';
                        }
                        jQuery('<li  id =ida_' + i + ' class=' + CSSclass + '></li>').html(html).appendTo('.sl_SearchR');
                        bindInfoWindow(marker, map, infoWindow, html1, i);
                        htmPhone = '';
                        htmFax = '';
                        htmEmail = '';
                        htmWeb = '';
                        htmLogo = '';
                        arrayData.push(Social);
                        if (directionhtm.length > 1) {
                            jQuery("a.sl_gall").each(function (i) {
                                jQuery(this).simpletip({
                                    fixed: true,
                                    content: jQuery(this).find('img').attr('alt')
                                });
                            });
                        }
                        jQuery(".sl_SearchR a.tootltipshow").each(function (i) {
                            jQuery(this).simpletip({
                                persistent: true,
                                focus: true,
                                position: 'left',
                                fixed: true,
                                content: arrayData[i]
                            });
                            jQuery(this).on('click', function (e) {
                                e.preventDefault();
                            });
                        });
                    }
                    map.fitBounds(bounds);
                }
            }
        });
        setTimeout(function () {
            var actualHei = 0;
            var dropDHeight = 0;
            if (jQuery('.sloc_catDrop').height() > 1) {
                dropDHeight = jQuery('.sloc_catDrop').height();
            }
            if (jQuery('.inHeader').height() > 10) {
            }
            else {
                var pageWidth = jQuery('.giz_storeLocator').width();
                if (pageWidth >= 320 && pageWidth <= 650) {
                    var lengthOfLi = jQuery('#sl_new_searchResult').find('.sl_SearchR li').length;
                    actualHei = jQuery('#sl_new_searchResult').find('.sl_SearchR li').height();
                    if (lengthOfLi * actualHei > 420) {
                        actualHei = 420;
                    } else {
                        actualHei = lengthOfLi * actualHei + 50;
                    }
                } else if (pageWidth > 650) {
                    actualHei = jQuery('#sloc_rightContent').height() - jQuery('#sloc_frmSearch').height();
                } else {
                    var lengthOfLi = jQuery('#sl_new_searchResult').find('.sl_SearchR li').length;
                    actualHei = jQuery('#sl_new_searchResult').find('.sl_SearchR li').height();
                    if (lengthOfLi * actualHei > 420) {
                        actualHei = 420;
                    } else {
                        actualHei = lengthOfLi * actualHei + 50;
                    }
                }
            }
            /*				jQuery('#sl_new_searchResult').css({ 'height' : actualHei });
             jQuery("#sl_new_searchResult").niceScroll({ horizrailenabled : false });*/
            jQuery('#sl_nearStore').removeAttr('disabled');
        }, 3000);
    }

    /** Initialize The Map in Page Load ***/
    function initialize_map(Lat, Lan) {
        fullSiteWidth = jQuery('.giz_storeLocator').width();
        var mapOptions = {
            center: new google.maps.LatLng(Lat, Lan),
            zoom: <?php echo $ZoomLevel; ?>,
            scaleControl: true,
            mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
            zoomControl: <?php echo $ZoomControl; ?>,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            panControl: <?php echo $PanControl; ?>,
            panControlOptions: {
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            streetViewControl: <?php echo $streetControl; ?>
        };
        if (fullSiteWidth >= 320 && fullSiteWidth <= 650) {
            mapOptions = {
                center: new google.maps.LatLng(Lat, Lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        } else if (fullSiteWidth > 650) {
            mapOptions = {
                center: new google.maps.LatLng(Lat, Lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: <?php echo $ZoomControl; ?>,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: <?php echo $PanControl; ?>,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: <?php echo $streetControl; ?>
            };
        } else {
            mapOptions = {
                center: new google.maps.LatLng(Lat, Lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        }
        var map = gMap = new google.maps.Map(document.getElementById("sl_front_map_canvas"), mapOptions);
    }

    /*** Load Or Bind The From DB using Ajax XML Response ***/
    function LoadData(url, callback) {
        var request = window.ActiveXObject ?
            new ActiveXObject('Microsoft.XMLHTTP') :
            new XMLHttpRequest;

        request.onreadystatechange = function () {
            if (request.readyState == 4) {
                request.onreadystatechange = doNothing;
                callback(request, request.status);
            }
        };

        request.open('GET', url, true);
        request.send(null);
    }

    function doNothing() {
    }

    function setFacebookLike() {
        jQuery('html').attr("xmlns:og", "http://www.facebook.com/2008/fbml").attr("xmlns:fb", "http://www.facebook.com/2008/fbml");

        jQuery('.fb-recommend').remove();
        jQuery('#fb-root').empty();

        jQuery('ul.posts a').each(function () {
            var fb_url = location.href.split('/')[0] + '//' + location.href.split('/')[2] + jQuery(this).attr('href'),
                fb_like = '<div class="fb_recommend"><fb:like href=\"' + fb_url + '\" layout="standard" show_faces="false" action="recommend" colorscheme="light"></fb:like></div>';
            jQuery(this).parent().next().after(fb_like);
        });
        jQuery('body').append('<div id="fb-root"></div>');
        window.fbAsyncInit = function () {
            FB.init({appId: '399691626740721', status: true, cookie: true, xfbml: true});
        };
        (function () {
            var e = document.createElement('script');
            e.async = true;
            e.src = document.location.protocol + "//connect.facebook.net/en_US/all.js";
            document.getElementById('fb-root').appendChild(e);
        }());
    }

    /*** Find The Stores Near To Address Where user Entered ****/
    function SearchStore(lat, lan, rad, RadType, CateId) {
        jQuery('.gm-style').removeClass('gm-style');
        fullSiteWidth = jQuery('.giz_storeLocator').width();
        var windowsHeight = jQuery(window).height();
        var windowsWidth = jQuery(window).width();
        var noResult = false;
        var heigh_li = 0;
        var mapOptions = {
            center: new google.maps.LatLng(lat, lan),
            zoom: <?php echo $ZoomLevel; ?>,
            mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
            zoomControl: <?php echo $ZoomControl; ?>,
            scaleControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            panControl: <?php echo $PanControl; ?>,
            panControlOptions: {
                position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
            },
            streetViewControl: <?php echo $streetControl; ?>
        };

        if (fullSiteWidth >= 320 && fullSiteWidth <= 650) {
            mapOptions = {
                center: new google.maps.LatLng(lat, lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        } else if (fullSiteWidth > 650) {
            mapOptions = {
                center: new google.maps.LatLng(lat, lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: <?php echo $ZoomControl; ?>,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: <?php echo $PanControl; ?>,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: <?php echo $streetControl; ?>
            };
        } else {
            mapOptions = {
                center: new google.maps.LatLng(lat, lan),
                zoom: <?php echo $ZoomLevel; ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
                zoomControl: false,
                scaleControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                panControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
                },
                streetViewControl: false
            };
        }

        var map = gMap = new google.maps.Map(document.getElementById("sl_front_map_canvas"), mapOptions);

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lan),
            map: map
        });
        var bounds = new google.maps.LatLngBounds();
        var arrayData = [];
        var infoWindow = new google.maps.InfoWindow;
        <?php include_once 'overlay.php'; ?>
        <?php js_get_vars($cats); ?>
        var lName = jQuery('#sloc_tbName').val();
        var lCountry = jQuery('#sloc_tbCountry').val();
        var lPlace = jQuery('#sloc_tbPlace').val();
        var loadAdwords = jQuery('#sloc_LoadAdwords').is(":checked");
		
        var sl_frontsearch_dal = {
            action: 'sl_dal_searchlocation',
            funMethod: 'SearchStore',
            Location: lat + "~" + lan + "~" + rad + "~" + CateId.join(",") + "~" + lCountry + "~" + lPlace + "~" + lName + "~" + loadAdwords,
            StoreLocation: ''
        };
        giz_Locator.home.ajaxSer(admin_ajx, "POST", sl_frontsearch_dal, function (markers) {

            if (markers != null) {
                if (markers.length <= 0) {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
                    jQuery('#sl_msgBox').text("<?php _e('No stores Found', self::TEXT_DOMAIN); ?>.");
                    jQuery('#sl_msgBox').removeClass('sl_success').addClass('sl_error').show().fadeIn(300);
                    $("#sloc_bottomBar").hide();
                    noResult = true;
                }
                else {
                    jQuery('.sl_SearchR li').remove();
                    jQuery('.sl_SearchR').empty();
					
					if (loadAdwords) {
						jQuery('#select_none, #select_all, #select_adwords').bind('click', function () {
							switch ($(this).attr("id")) {
								case "select_all":
									jQuery('ul.sl_SearchR>li:not(.active) .mapres-title').each(function (index) {
										$(this).click();
									});
									break;
								case "select_adwords":
									jQuery('ul.sl_SearchR>li.active').each(function (index) {
										$(this).click();
									});
									jQuery('ul.sl_SearchR>li:has(.option-procedures-wrapper)').each(function (index) {
										$(this).click();
									});
									break;
								default:
									jQuery('ul.sl_SearchR>li.active .mapres-title').each(function (index) {
										$(this).click();
									});
							}
							infoWindow.close();
							map.panTo(new google.maps.LatLng(lat, lan));
	
							
						});
	                    $("#sloc_bottomBar").show();

					} else {
						$("#sloc_bottomBar").hide();
					}

                    if (markers.length == 1) {
                        jQuery('#sl_msgBox').text(markers.length + " <?php _e('Store found', self::TEXT_DOMAIN); ?>.");
                    } else {
                        jQuery('#sl_msgBox').text(markers.length + " <?php _e('Stores found', self::TEXT_DOMAIN); ?>.");
                    }
                    jQuery('#sl_msgBox').removeClass('sl_error').addClass('sl_success').show().fadeIn(300);
                    var curOverlay = null;
                    for (var i = 0; i < markers.length; i++) {
                        heigh_li = 190 * (i + 1);
                        var storeId = markers[i]["id"];
                        var name = markers[i]["name"];
                        var address = markers[i]["FullAddress"];
                        var brand = markers[i]["type"];
                        var point = new google.maps.LatLng(parseFloat(markers[i]["lat"]), parseFloat(markers[i]["lng"]));
                        var Phone = markers[i]["phone"];
                        var Latt = parseFloat(markers[i]["lat"]);
                        var Lngg = parseFloat(markers[i]["lng"]);
                        var fax = markers[i]["fax"];
                        var email = markers[i]["email"];
                        var web = markers[i]["website"];
                        var logo = markers[i]["logopath"];
                        var dist = markers[i]["distance"];
                        var iconImg = markers[i]["CategoryIcon"];
                        var labelId = parseInt(markers[i]["labelid"]);
                        var labelText = markers[i]["labeltext"];
                        var ImgUrl = markers[i]["imgurl"];
                        var markImage = new google.maps.MarkerImage();
                        if (iconImg != null) {
                            if (iconImg.length > 0) {
                                iconImg = plugin_path + iconImg;
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                            else {
                                iconImg = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
                                markImage = new google.maps.MarkerImage(iconImg);
                            }
                        };
                        var marker = new google.maps.Marker({
                            map: map,
                            position: point,
                            icon: markImage,
                            animation: google.maps.Animation.DROP
                        });
                        var htmPhone = '';
                        var htmFax = '';
                        var htmEmail = '';
                        var htmWeb = '';
                        var htmLogo = '';
                        var phone_fax = '';
                        var htmLabel = '';
                        var add_width = (jQuery('#sloc_leftBar').width() - 105) + 'px';
                        var Total_str = "<?php _e('Address', self::TEXT_DOMAIN); ?> : " + address;
                        var radian = (RadType === 'Miles') ? parseFloat(dist / 1.609).toFixed(1) : parseFloat(dist).toFixed(1);
                        htmPhone = (Phone.length > 0) ? '<div class="sl_pad2"><label style="font-weight:bold;"><?php _e('Phone', self::TEXT_DOMAIN); ?> : </label><span> ' + Phone + '</span>, ' : '<div class="sl_pad2">';
                        if (Phone.length > 0) {
                            Total_str = Total_str + ',<?php _e('Phone', self::TEXT_DOMAIN); ?>: ' + Phone;
                        }
                        htmFax = (fax.length > 0) ? '<label style="font-weight:bold"><?php _e('Fax', self::TEXT_DOMAIN); ?> : </label><span>' + fax + '</span></div>' : '</div>';
                        if (fax.length > 0) {
                            Total_str = Total_str + ',<?php _e('Fax', self::TEXT_DOMAIN); ?> : ' + fax;
                        }
                        /*						if(email.length   > 0){htmEmail  = '<div class="sl_pad2 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Email', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"mailto:'+ email +'\">'+ email +'</a></span></div>'; Total_str = Total_str + ',<?php _e('Email', self::TEXT_DOMAIN); ?>: ' + email;}

                         if(web.length     > 0){htmWeb    = '<div class="sl_pad2 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 53px;"><?php _e('Website', self::TEXT_DOMAIN); ?></label><span><b>:</b> <a href=\"'+ web +'\" target="_blank">'+web+'</a></span></div>'; Total_str = Total_str + ',<?php _e('Website', self::TEXT_DOMAIN); ?> : ' + web;}
                         */
                        <?php include_once 'extra.php'; add_js_extra(); ?>
                        if (logo.length > 0) {
                            htmLogo = '<div class="sl_logo sl_clear"><img src=' + plugin_path + logo + ' alt=' + name + ' title=' + name + ' /></div>';
                        }
                        else {
                            htmLogo = '<div class="sl_logo" style="clear:both;"><img src="' + plugin_path + 'logo_02.png" alt=' + name + ' title=' + name + ' /></div>';
                        }
                        <?php if ($LogoVisible != 1) { ?>
                        htmLogo = '';
                        add_width = '99%';
                        <?php } ?>
                        phone_fax = htmPhone + htmFax;
                        if (labelId > 1) {
                            htmLabel = '<div style="padding-left:18%"><div style="background:url(' + plugin_path + ImgUrl + ') no-repeat; text-align: center;width: 206px;margin-top:-15px; height:27px"><span style="display:block; padding-top:3px" class="lblSpan">' + labelText + '</span></div></div>';
                        }
                        var orgin = site_url;
                        var qs = url_query.split('?')[1];
                        if (typeof qs !== 'undefined') {
                            var likeUrl = encodeURIComponent(url_query + '&Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"]);
                            var likeUrlN = url_query + '&Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"];
                        } else {
                            var likeUrl = encodeURIComponent(url_query + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"]);
                            var likeUrlN = url_query + '?Location=Social&StoreLocation=' + markers[i]["lat"] + "~" + storeId + "~" + markers[i]["lng"];
                        }
                        var imageUrl = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false');
                        var imageUrlN = 'http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=15&size=851x315&sensor=false';

                        var imageUrlFb = encodeURIComponent('http://maps.googleapis.com/maps/api/staticmap?center=' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&markers=color:red|' + markers[i]["lat"] + ',' + markers[i]["lng"] + '&zoom=10&size=150x150&sensor=false');
                        var fb = '<div><a  href=\"javascript:void(0)\" title="http://www.facebook.com/sharer.php?s=100&p[url]=' + likeUrl + '&p[title]=' + encodeURIComponent(name) + '&p[summary]=' + encodeURIComponent(Total_str) + '&p[images][0]=' + imageUrlFb + '"  onclick="return facebookShare(this.title);" class="fb_share_link"><img border="0" src="' + plugin_path + 'images/icon/icon_fb.png" title="<?php _e('Share', self::TEXT_DOMAIN); ?>" /></a></div>';
                        var pinit = '<div class="pinterest-pin-it-button"><a id=pinid' + i + ' title="http://pinterest.com/pin/create/button/?url=' + likeUrl + '&media=' + imageUrl + '&description=' + name + ' - ' + encodeURIComponent(Total_str) + '" class="pin-it-button" count-layout="horizontal" href=\"javascript:void(0)\" onclick="return pinIt(this.id,this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_pin.png" title="<?php _e('Pin It', self::TEXT_DOMAIN); ?>" /></a></div>';
                        var tweet = '<div class="custom-tweet-button"><a href=\"javascript:void(0)\" title="https://twitter.com/intent/tweet?original_referer=' + encodeURIComponent(orgin) + '&source=tweetbutton&text=' + name + '&url=' + likeUrl + '&via=' + window.location.host + '" onclick="return TweetShare(this.title);"><img border="0" src="' + plugin_path + 'images/icon/icon_tweet.png" title="<?php _e('Tweet', self::TEXT_DOMAIN); ?>" /></a></div>';
                        fb = (markers[i]["facebook"] === "1") ? fb : "";
                        pinit = (markers[i]["printrest"] === "1") ? pinit : "";
                        tweet = (markers[i]["twitter"] === "1") ? tweet : "";

                        var toolCss = "";
                        if (fb.length > 0 || pinit.length > 0 || tweet.length > 0) {
                            toolCss = "";
                        } else {
                            toolCss = "no";
                        }
                        var directionhtm = '<a href=\"javascript:void(0)\" rel="' + address + '" lat="' + Latt + '" lng="' + Lngg + '" class="sl_GetDirection fl sl_pad2 sl_gall" ><img src="' + plugin_path + 'images/icon/direction.png" alt="<?php _e('Get Directions', self::TEXT_DOMAIN); ?>" /></a>';

                        var Social = '<div id="social' + i + '" class="social_plugin">' + fb + pinit + tweet + '</div>';
                        <?php js_get_more_info(); ?>
                        <?php
                        //$procedures='<div class="sl_pad2 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0;">\'Procedure Name</label><span><b>, Cost: $232323</b> <a href="url">url</a></span></div>';
                        ?>

                        var html = '<span id="idadiv_' + i + '"><header class="mapres-header"><h4 class="mapres-title">' + name + '</h4><span class="mapres-miles">' + radian + " " + RadType + '</span></header><div class ="mapres-category round-corner mapres-c'+markers[i]["type"]+'">'+categories[markers[i]["type"]]+'</div>  <div class="mapres-address"><strong>Address :</strong>' + address + '</div></span>';

                        var html_info = '<header class="mapres-header" ><h4 class = "mapres-title" > '+ name +'</h4><span class ="mapres-miles" > '+ radian + " " + RadType +'</span>'
                            +'</header><div class ="mapres-category mapres-c'+markers[i]["type"]+' round-corner">'+categories[markers[i]["type"]]+'</div><div class = "mapres-address" ><i class="mapres-sprite pin-icon" > </i> '+ address +'</div>';

                        var CSSclass = '';
                        if (i % 2 == 0) {
                            CSSclass = 'even';
                        }
                        else if (i % 2 == 1) {
                            CSSclass = 'odd';
                        }
                        <?php js_add2box(); ?>
                        jQuery('<li  id =ida_' + i + ' class="mapres-item ' + CSSclass + '"></li>').html(html).appendTo('.sl_SearchR');
                        bindInfoWindow(marker, map, infoWindow, html_info, i);
                        htmPhone = '';
                        htmFax = '';
                        htmEmail = '';
                        htmWeb = '';
                        htmLogo = '';
                        if (directionhtm.length > 1) {
                            jQuery("a.sl_gall").each(function (i) {
                                jQuery(this).simpletip({
                                    fixed: true,
                                    content: jQuery(this).find('img').attr('alt')
                                });
                            });
                        }
                        arrayData.push(Social);
                        bounds.extend(point);
                        jQuery(".sl_SearchR a.tootltipshow").each(function (i) {
                            jQuery(this).simpletip({
                                persistent: true,
                                focus: true,
                                position: 'left',
                                fixed: true,
                                content: arrayData[i]
                            });
                            jQuery(this).on('click', function (e) {
                                e.preventDefault();
                                jQuery('#tool' + i + ' .tooltip').css({'top': jQuery(this).offset().top - 50});
                            });
                        });
                    }


                    /*
                     var ctaLayer = new google.maps.KmlLayer({url: 'http://wm4dmap.com/dev/wp-admin/admin-ajax.php?action=crm2map_getkml&c[]=3963238515&'+Date.now(),map: map});
                     */

                }
			if (loadAdwords) {				
                $(".option-procedures").hide();
                $(".arrow-up").hide();
                $(".option-heading").on("click", function () {
                    $(this).next(".option-procedures").slideToggle(500);
                    $(this).find(".arrow-up, .arrow-down").toggle();
                });
			};
                if (noResult == false) {
                    jQuery("#sl_new_searchResult").niceScroll({horizrailenabled: false});
                    map.fitBounds(bounds);
                }
                ;
                /*			$('html, body').animate({scrollTop: $("#sl_new_searchResult").offset().top-300}, 2000)*/
                ;
            }
        });

        setTimeout(function () {
            var actualHei = 0;
            var dropDHeight = 0;
            if (jQuery('.sloc_catDrop').height() > 1) {
                dropDHeight = jQuery('.sloc_catDrop').height();
            }
            if (jQuery('.inHeader').height() > 10) {
            }
            else {
                var pageWidth = jQuery('.giz_storeLocator').width();
                if (pageWidth >= 320 && pageWidth <= 650) {
                    var lengthOfLi = jQuery('#sl_new_searchResult').find('.sl_SearchR li').length;
                    actualHei = jQuery('#sl_new_searchResult').find('.sl_SearchR li').height();
                    if (lengthOfLi * actualHei > 420) {
                        actualHei = 420;
                    } else {
                        actualHei = lengthOfLi * actualHei + 50;
                    }
                } else if (pageWidth > 650) {
                    actualHei = jQuery('#sloc_rightContent').height() - jQuery('#sloc_frmSearch').height();
                } else {
                    var lengthOfLi = jQuery('#sl_new_searchResult').find('.sl_SearchR li').length;
                    actualHei = jQuery('#sl_new_searchResult').find('.sl_SearchR li').height();
                    if (lengthOfLi * actualHei > 420) {
                        actualHei = 420;
                    } else {
                        actualHei = lengthOfLi * actualHei + 50;
                    }
                }
            }
            /*			jQuery('#sl_new_searchResult').css({ 'height' : actualHei });*/
            jQuery('#sl_new_searchResult').css({'height': '100vh'});

            jQuery('#sl_nearStore').removeAttr('disabled');
 	    map.panTo(new google.maps.LatLng(lat, lan));						

        }, 3000);
    }

    function getSearchResultHtml() {
    }

    function getFeatureStyle(feature) {

        var styleNames = ['fillColor'];
        var res = {'strokeWeight': 2, 'strokeColor': 'white'};
        for (var key in styleNames) {
            res[styleNames[key]] = feature.getProperty(styleNames[key]);
        }
        ;
        if (typeof(activeZipcodes[feature.getProperty("title")]) != 'undefined' && activeZipcodes[feature.getProperty("title")].length > 1) {
            res['zIndex'] = '99';
            res['strokeColor'] = 'red';
            
        }
        return res;
    }

    function toggleHeatmap(marker) {
	
 	if (typeof(activeMarkers[marker["id"]]) == 'undefined' || activeMarkers[marker["id"]]== null) {						

            if (typeof(marker['heatmap']) != 'undefined' && marker['heatmap'] != null) {
                marker['heatmap'].setMap(marker.getMap());
                if (typeof(marker['heatmap_zipcodes']) == 'undefined') {
                    var hmz = [];
                    marker['heatmap'].forEach(function (f) {
                        hmz.push(f.getProperty("title"));
                    });
                    marker['heatmap_zipcodes'] = hmz;
                } else {
                }
            }
            ;
            activeMarkers[marker["id"]] = marker;
        } else {
            if (typeof(marker['heatmap']) != 'undefined' && marker['heatmap'] != null) {
                marker['heatmap'].setMap(null);
            }
            ;

            delete(activeMarkers[marker["id"]]);

        }
        ;
       
        if (typeof(marker['heatmap_zipcodes']) != 'undefined' && marker['heatmap_zipcodes'] != null)
            var ft = null;
        activeZipcodes = [];
        for (var mid in activeMarkers) {
            if ($.isArray(activeMarkers[mid]['heatmap_zipcodes'])) {
               for (var zci = 0; zci < activeMarkers[mid]['heatmap_zipcodes'].length; zci++) {
                    /*							console.log('in');
                     */
                    ft = activeMarkers[mid]['heatmap'].getFeatureById(activeMarkers[mid]['heatmap_zipcodes'][zci]);
                    ft.setProperty('name', activeMarkers[mid]['name']);
                    if (typeof(activeZipcodes[activeMarkers[mid]['heatmap_zipcodes'][zci]]) != 'undefined') {
                        activeZipcodes[activeMarkers[mid]['heatmap_zipcodes'][zci]].push(ft);
                    } else {
                        activeZipcodes[activeMarkers[mid]['heatmap_zipcodes'][zci]] = Array(ft);
                    }
                }
                ;
                activeMarkers[mid]['heatmap'].setStyle(function (feature) {
                    res = getFeatureStyle(feature);
                    res['visible'] = true;
                    return res;

                    /*
                     if (activeZipcodes[feature.getProperty("title")].length>1) {
                     var res={'strokeColor':'red'};
                     }
                     return res;
                     */
                });


            }
        }
        ;

    }

    /*** Info Window Or Marker Window ***/
    function bindInfoWindow(marker, map, infoWindow, html, id) {
        /*** Event From Marker : Set Active Class for Address And Pop up The Info Window In Map **/
        google.maps.event.addListener(marker, 'click', function () {
            jQuery('#ida_' + id).hasClass('active') ? jQuery('#ida_' + id).removeClass('active') : jQuery('#ida_' + id).addClass('active');

            var count = 0;
            if (id == 0) {
                count = 1;
            }
            else {
                count = id;
            }
            infoWindow.setContent(html);
            infoWindow.open(map, marker);
            $(".option-procedures").hide();
            $(".arrow-up").hide();

            toggleHeatmap(marker);
            
            return false;
        });
        /*** Event From List : Set Active And Pop up The Info Window In Map **/

        var addressObj = document.getElementById('idadiv_' + id);
        google.maps.event.addDomListener(addressObj, 'click', function () {
            jQuery('#ida_' + id).hasClass('active') ? jQuery('#ida_' + id).removeClass('active') : jQuery('#ida_' + id).addClass('active');

            infoWindow.setContent(html);
            infoWindow.setOptions({height: 190});
            infoWindow.open(map, marker);
            $(".option-procedures").hide();
            $(".arrow-up").hide();

            toggleHeatmap(marker);

            /*			if (typeof(curOverlay) != 'undefined' && curOverlay != null) curOverlay.setMap(null);*/
            if (typeof(marker['heatmap']) != 'undefined' && marker['heatmap'] != null) {
                if (marker['heatmap'].getMap() != null) ;/*marker['heatmap'].setMap(null);*/
                else {
                    /*					marker['heatmap'].setMap(map);*/
                    infoWindow.close();

                }
            }
            ;

            map.panTo(marker.getPosition());

        });

    }

    /*** Info Window Or Marker Window ***/
    function bindInfoWindowN(marker, map, infoWindow, html, id, isList) {
        /*** Event From Marker : Set Active Class for Address And Pop up The Info Window In Map **/
        google.maps.event.addListener(marker, 'click', function () {
            infoWindow.setContent(html);
            infoWindow.open(map, marker);

            return false;
        });
        /*** Event From List : Set Active And Pop up The Info Window In Map **/
        if (isList) {
            var addressObj = document.getElementById('idadiv_' + id);
            google.maps.event.addDomListener(addressObj, 'click', function () {
                jQuery('ul.sl_SearchR>li.active').removeClass('active');
                jQuery('ul.sl_SearchR>li').each(function (index) {
                    jQuery('#ida_' + id).addClass('active');
                });
                infoWindow.setContent(html);
                infoWindow.setOptions({height: 190});
                infoWindow.open(map, marker);
                map.panTo(marker.getPosition());
            });
        }
    }

    function getMyHeight() {
        return "700px";
    }


    jQuery(window).bind('resize', function () {
        ResizeFullPage(true);
    });

    glRefresh = refresh;

    function refresh() {
        if (jQuery('#sloc_leftBar2').css('display') == 'block') {
            var siteWidth = jQuery('.giz_storeLocator').width() - 350;
        } else {
            var siteWidth = jQuery('.giz_storeLocator').width();
        }
        /*alert(siteWidth);*/
        if (siteWidth >= 320 && siteWidth <= 650) {
            jQuery('#sloc_rightContent').css({'height': getMyHeight(), 'clear': 'both', 'width': siteWidth});
            jQuery('#hrHide').parent('div').css({'display': 'none', 'clear': 'both'});
            jQuery('#sloc_leftBar').css({'float': 'left'});
            if (siteWidth > 400) {
                jQuery('#sl_map_form').css({'width': 400});
            } else {
                jQuery('#sl_map_form').css({'width': siteWidth - 10});
            }
            changeLayout(false);
            /*hideMapControl();*/
        } else if (siteWidth > 650) {
            jQuery('#sloc_rightContent').css({'height': getMyHeight(), 'clear': '', 'width': siteWidth});
            jQuery('#hrHide').parent('div').css({'display': 'block', 'float': naviPos, 'clear': ''});
            jQuery('#sloc_leftBar').css({'float': naviPos});
            jQuery('#sl_map_form').css({'width': 400});
            changeLayoutH(false);
        } else {
            jQuery('#sloc_rightContent').css({'height': getMyHeight(), 'clear': 'both', 'width': siteWidth});
            jQuery('#hrHide').parent('div').css({'display': 'none', 'clear': 'both'});
            jQuery('#sloc_leftBar').css({'float': 'left'});
            if (siteWidth > 400) {
                jQuery('#sl_map_form').css({'width': 400});
            } else {
                jQuery('#sl_map_form').css({'width': siteWidth - 10});
            }
            changeLayout(false);
            /*hideMapControl();*/
        }
    }

    refresh();

	jQuery('#hrHide').on('click', function(){
		var siteWidth = jQuery('.giz_storeLocator').width();
		if(siteWidth >= 320 && siteWidth <=650){
			if(jQuery('#sloc_leftBar').css('display') === 'none'){
				jQuery('#sloc_rightContent').animate({'height': jQuery('#sloc_rightContent').height() - 330}, 500,function(){
				});
			}
			jQuery('#sloc_leftBar').animate({
				'height': 'toggle'
				}, 1000, 'jswing', function() {
					changeLayout(true);
					UpdateSearchPnl();
			  });
		}else if(siteWidth > 650){
			if(jQuery('#sloc_leftBar').css('display') === 'none'){
				jQuery('#sloc_rightContent').animate({'width': jQuery('#sloc_rightContent').width() - 330}, 500,function(){
				});
			}
			jQuery('#sloc_leftBar').animate({
				'width': 'toggle'
				}, 1000, 'jswing', function() {
					changeLayoutH(true);
					UpdateSearchPnl();
			  });
		}else{
			if(jQuery('#sloc_leftBar').css('display') === 'none'){
				jQuery('#sloc_rightContent').animate({'height': jQuery('#sloc_rightContent').height() - 330}, 500,function(){
				});
			}
			jQuery('#sloc_leftBar').animate({
				'height': 'toggle'
				}, 1000, 'jswing', function() {
					changeLayout(true);
					UpdateSearchPnl();
			  });
		}
		setTimeout(function(){
			resizeMap(gMap);
		},2500);
	});

	function hideMapControl(){
		setTimeout(function(){
			gMap.panControl 		= false;
			gMap.streetViewControl	= false;
			gMap.zoomControl 		= false;
			gMap.mapTypeControl 	= false;
			gMap.disableDefaultUI   = true;
			/*jQuery('#sloc_rightContent').css({'width': jQuery('#sloc_rightContent').width()-20 });
			jQuery('#sloc_rightContent').css({'width': jQuery('#sloc_rightContent').width()+20 });
			resizeMap(gMap);*/
		},6000);
		setTimeout(function(){
			/*resizeMap(gMap);*/
		},7000);
	}

	function ResizeFullPage(isWindowResize){
			var site_Width = jQuery('.giz_storeLocator').width();
			var window_width = jQuery('body').width();
			if(isWindowResize){
				site_Width = jQuery('.giz_storeLocator').width();
			}else{
				site_Width = jQuery('.giz_storeLocator').width();
			}
			if(window_width < site_Width){
				site_Width = jQuery('body').width() - 100;
			}


            if (jQuery('#sloc_leftBar2').css('display') == 'block') {
                site_Width = site_Width-350;
            }

			if(site_Width >= 320 && site_Width <=650){
				jQuery('#sloc_rightContent').css({'height' : getMyHeight(), 'clear': 'both', 'width': site_Width-5});
				jQuery('#hrHide').parent('div').css({'display':'none', 'clear': 'both'});
				jQuery('#sloc_leftBar').css({'float': 'left'});
				if(siteWidth > 400){
					jQuery('#sl_map_form').css({'width':400});
				}else{
					jQuery('#sl_map_form').css({'width':siteWidth-10});
				}
				changeLayout(false);
				UpdateSearchPnl();
			}else if(site_Width > 650){
				jQuery('#sloc_rightContent').css({'height' : getMyHeight(), 'clear': '', 'width': site_Width});
				jQuery('#hrHide').parent('div').css({'display':'block','float': naviPos, 'clear': ''});
				jQuery('#sloc_leftBar').css({'float': naviPos});
				jQuery('#sl_map_form').css({'width':400});
				changeLayoutH(false);
				UpdateSearchPnl();
			}else{
				jQuery('#sloc_rightContent').css({'height' : getMyHeight(), 'clear': 'both', 'width': site_Width});
				jQuery('#hrHide').parent('div').css({'display':'none', 'clear': 'both'});
				jQuery('#sloc_leftBar').css({'float': 'left'});
				if(siteWidth > 400){
					jQuery('#sl_map_form').css({'width':400});
				}else{
					jQuery('#sl_map_form').css({'width':siteWidth-10});
				}
				changeLayout(false);
				UpdateSearchPnl();
			}
	}
	/*google.maps.event.addListener(gMap, 'bounds_changed', function() {

	});*/
	function resizeMap(map) {
		 google.maps.event.trigger(map, "resize");
		/*mapResized = true;*/
	}

	function animate(elm, begin, end, duration, fps) {
		  function easeInCubic (t, b, c, d) {
		   return c*(t/=d)*t*t + b;
		  }
		  function easeOutCubic (t, b, c, d) {
		   return c*((t=t/d-1)*t*t + 1) + b;
		  }

		  var change = end - begin,
		  interval = Math.ceil(1000/fps),
		  totalFrames = Math.ceil(duration/interval),
		  i = 0,
		  timer = null;

		  function countFrames(frame) {
			timer = setTimeout(function() {
			  var width = easeInCubic(frame, begin, change, totalFrames);
			  elm.style.width  = width+'%';
			 if (width == end) resizeMap(gMap);
			}, interval*frame);
		  }
		  while (++i <= totalFrames) {
			countFrames(i);
		  }
	}


	function resize() {
	  var mapdiv = document.getElementById("sl_front_map_canvas");
	  var w = parseInt(mapdiv.style.width);
	  if ((isNaN(w)) || (w < 90)) {
		animate(mapdiv, 79, 99, 300, 30);
	  }
	  else {
		animate(mapdiv, 99, 79, 300, 30);
	  }
	}
	function changeLayout(isAnimate){
		jQuery('#hrHide').removeAttr('class');
		var disLoc = jQuery('#hrHide').parent().css('float');
		var rCls = (disLoc === "left") ? 'L' : 'R';
		if(jQuery('#sloc_leftBar').css('display') === 'block'){
			jQuery('#hrHide').attr('alt', 'Hide');
			jQuery('#hrHide').attr('title', 'Hide');
			jQuery('#hrHide').attr('class','arrup');
			jQuery('#hrHide').parent('div').css({'width': 17});
		}
		else if(jQuery('#sloc_leftBar').css('display') === 'none'){
			jQuery('#hrHide').attr('alt', 'Expand');
			jQuery('#hrHide').attr('title', 'Expand');
			if(isAnimate){
				jQuery('#sloc_rightContent').animate({'height': jQuery('#sloc_rightContent').height() + 320},500, function(){
					jQuery('#sloc_rightContent').css({'height': jQuery('#sloc_rightContent').height()});

				});
			}else{
				jQuery('#sloc_rightContent').css({'height': jQuery('#sloc_rightContent').height() + 320});
			}
			jQuery('#hrHide').attr('class','arrdown');
			jQuery('#hrHide').parent('div').css({'width': 17});
		}
	}
	function changeLayoutH(isAnimate){
		jQuery('#hrHide').removeAttr('class');
		var disLoc = jQuery('#hrHide').parent().css('float');
		var rCls = (disLoc === "left") ? 'L' : 'R';
		if(jQuery('#sloc_leftBar').css('display') === 'block'){
			jQuery('#hrHide').attr('alt', 'Hide');
			jQuery('#hrHide').attr('title', 'Hide');
			jQuery('#hrHide').attr('class','arr'+disLoc);
			jQuery('#hrHide').parent('div').css({'width': 13});
		}
		else if(jQuery('#sloc_leftBar').css('display') === 'none'){
			jQuery('#hrHide').attr('alt', 'Expand');
			jQuery('#hrHide').attr('title', 'Expand');
			if(isAnimate){
				jQuery('#sloc_rightContent').animate({'width': jQuery('#sloc_rightContent').width() + 330},500, function(){
					jQuery('#sloc_rightContent').css({'width': jQuery('#sloc_rightContent').width()});
				});
			}else{
				jQuery('#sloc_rightContent').css({'width': jQuery('#sloc_rightContent').width() + 330});
			}
			jQuery('#hrHide').attr('class','arr'+disLoc+rCls);
			jQuery('#hrHide').parent('div').css({'width': 13});
		}
	}

});


var glRefresh;
function UpdateSearchPnl(){
		var actualHei = 0;
		var pageWidth = jQuery('.giz_storeLocator').width();
		if(pageWidth >=320 && pageWidth <=650){
			if(jQuery('#sl_new_searchResult .sl_SearchR').length > 0){
				var lengthOfLi = jQuery('#sl_new_searchResult').find('.sl_SearchR li').length;
				actualHei = jQuery('#sl_new_searchResult').find('.sl_SearchR li').height();
				if(lengthOfLi*actualHei > 420){
					actualHei = 420;
				}else{
					actualHei = lengthOfLi*actualHei + 50;
				}
			}else if(jQuery('#sl_new_searchResult div.adp').length > 0){
				actualHei = jQuery('div.adp').height();
				if(actualHei > 420){
					actualHei = 420;
				}else{
					actualHei = actualHei + 20;
				}
			}else{
				actualHei = jQuery('.sloc_browse').height();
				if(actualHei > 420){
					actualHei = 420;
				}else{
					actualHei = actualHei + 10;
				}
			}
		}else if(pageWidth >650){
			if(jQuery('#sl_new_searchResult .sl_SearchR').length > 0){
				actualHei = jQuery('#sloc_rightContent').height() - jQuery('#sloc_frmSearch').height();
			}else{
				actualHei = jQuery('#sloc_rightContent').height() - jQuery('.sloc_catDrop').height();
			}
		}else{
			if(jQuery('#sl_new_searchResult .sl_SearchR').length > 0){
				var lengthOfLi = jQuery('#sl_new_searchResult').find('.sl_SearchR li').length;
				actualHei = jQuery('#sl_new_searchResult').find('.sl_SearchR li').height();
				if(lengthOfLi*actualHei > 420){
					actualHei = 420;
				}else{
					actualHei = lengthOfLi*actualHei + 50;
				}
			}else if(jQuery('#sl_new_searchResult div.adp').length > 0){
				actualHei = jQuery('div.adp').height();
				if(actualHei > 420){
					actualHei = 420;
				}else{
					actualHei = actualHei + 20;
				}
			}else{
				actualHei = jQuery('.sloc_browse').height();
				if(actualHei > 420){
					actualHei = 420;
				}else{
					actualHei = actualHei + 10;
				}
			}
		}
		actualHei='100vh';
		jQuery('#sl_new_searchResult').css({ 'height' : actualHei });
	}

/*** Tweet ***/
function TweetShare(orgin){
	window.open(orgin, 'Tweet', 'width=665,height=300,scrollbars=1,resizable=1');
}
/** facebook share ***/
function facebookShare(oUrl) {
	window.open(oUrl ,'sharer','toolbar=0,status=0,width=626,height=436');
}
function pinIt(pinItButton, pinUrl) {
                window.open(pinUrl, 'signin', 'width=665,height=300,scrollbars=1,resizable=1');
}
function callOnsubmit(){
	jQuery('#sl_nearStore').click();
	return false;
}
function callDirectionSub(){
	getDir();
	return false;
}

/********** Getting Direction **************/
	function getDir(){
		var result_res = false;
		if(jQuery('#fromdir').val().length <= 0){
				jQuery('#fromdir').focus();
				jQuery('#sl_err_msg').text("<?php _e('Enter the from address', self::TEXT_DOMAIN); ?>..");
				jQuery('#sl_err_msg').removeClass('sl_error').addClass('sl_error').fadeOut(300).fadeIn(300);
				jQuery('#GetDirec').removeClass('close');
		}
	else{
			jQuery('#sl_err_msg').text('');
			jQuery('#sl_err_msg').removeClass('sl_error');
			var directionsService = new google.maps.DirectionsService();
			var directionsDisplay = new google.maps.DirectionsRenderer();
			fullSiteWidth = jQuery('.giz_storeLocator').width();
			var mapOptions ={
				zoom: <?php echo $ZoomLevel; ?>,
				mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
				zoomControl: <?php echo $ZoomControl; ?>,
				scaleControl: true,
				zoomControlOptions: {
				style: google.maps.ZoomControlStyle.LARGE,
				position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				panControl: <?php echo $PanControl; ?>,
				panControlOptions: {
				position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				streetViewControl: <?php echo $streetControl; ?>
		   };
			if(fullSiteWidth >= 320 && fullSiteWidth <=650){
			mapOptions ={
				zoom: <?php echo $ZoomLevel; ?>,
				mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
				zoomControl: false,
				scaleControl: true,
				zoomControlOptions: {
				style: google.maps.ZoomControlStyle.LARGE,
					position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				panControl: false,
				panControlOptions: {
					position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				streetViewControl: false
			 };
		 }else if(fullSiteWidth > 650){
			mapOptions ={
				zoom: <?php echo $ZoomLevel; ?>,
				mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
				zoomControl: <?php echo $ZoomControl; ?>,
				scaleControl: true,
				zoomControlOptions: {
				style: google.maps.ZoomControlStyle.LARGE,
					position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				panControl: <?php echo $PanControl; ?>,
				panControlOptions: {
					position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				streetViewControl: <?php echo $streetControl; ?>
			 };
		 }else{
			mapOptions ={
				zoom: <?php echo $ZoomLevel; ?>,
				mapTypeId: google.maps.MapTypeId.<?php echo $MapType; ?>,
				zoomControl: false,
				scaleControl: true,
				zoomControlOptions: {
				style: google.maps.ZoomControlStyle.LARGE,
					position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				panControl: false,
				panControlOptions: {
					position: google.maps.ControlPosition.<?php echo $ControlPosition; ?>
				},
				streetViewControl: false
			 };
		 }
		var RadType,cont_html;
		var tolTip = '';
		var height_reduce = 0;
		if(searchMode == 'SM'){
			jQuery('#sl_msgBox').removeClass('sl_success');
			jQuery('#sl_msgBox').text('');
			RadType = (jQuery('input:radio[name=sloc_radius]:checked').val()) ? jQuery('input:radio[name=sloc_radius]:checked').val() : "KM";
			cont_html ="<div class='DHead sl_clear'><h2 class='dirhead fl'><?php _e('Directions', self::TEXT_DOMAIN); ?></h2>"+
			"<div class='fr'><a href=\"javascript:void(0)\" class='gobackO' rel='"+ RadType +"' rad='"+ jQuery('#sloc_selRadius').val() +"' lat='"+jQuery('#sloc_hdfLat').val()+"' lng='"+jQuery('#sloc_hdfLng').val()+"' ><img alt ='<?php _e('Back', self::TEXT_DOMAIN); ?>' src='"+ plugin_path +"images/icon/go_back.png' style='margin-top: -2px;' /></a></div>"+
			"</div><div class='sl_clear'></div>";
			tolTip = '<?php _e('Back to search', self::TEXT_DOMAIN); ?>';
			height_reduce = 24;
		}
		else if(searchMode == 'BM'){
			cont_html ="<div class='DHead sl_clear'><h2 class='dirhead fl'><?php _e('Directions', self::TEXT_DOMAIN); ?></h2>"+
			"<div class='fr'><a href=\"javascript:void(0)\" class='gobackO'><img alt ='<?php _e('Back', self::TEXT_DOMAIN); ?>' src='"+ plugin_path +"images/icon/go_back.png' style='margin-top: -2px;' /></a></div>"+
			"</div><div class='sl_clear'></div>";
			tolTip = '<?php _e('Back to browse', self::TEXT_DOMAIN); ?>';
			jQuery('.sloc_catDrop').hide();
			height_reduce = 30;
		}
		directionsDisplay.setPanel(document.getElementById('sl_new_searchResult'));
		var toadd=document.getElementById('todir').value;
		var fromadd = document.getElementById('fromdir').value;
		var geocoder = new google.maps.Geocoder();
		var toPoint = new google.maps.LatLng(parseFloat(jQuery('#sloc_hdfLat').val()), parseFloat(jQuery('#sloc_hdfLng').val()));
		var unitType = (jQuery.trim(jQuery('#sloc_hdfORadTy').val()) == 'Kms') ? 'METRIC' : '';
		 var request = {
		   origin: fromadd,
		   destination: toPoint,
		   travelMode: google.maps.DirectionsTravelMode.DRIVING,
		   unitSystem: (jQuery('#rdUnitKm').is(':checked')) ? google.maps.UnitSystem.METRIC : google.maps.UnitSystem.IMPERIAL
		 };
		 directionsService.route(request, function(response, status) {
		   if (status == google.maps.DirectionsStatus.OK) {
			var map = new google.maps.Map(document.getElementById("sl_front_map_canvas"), mapOptions);
			directionsDisplay.setMap(map);
			result_res = true;
			jQuery('#close_x').click();
			jQuery('#sl_new_searchResult').empty();
			jQuery('#sl_new_searchResult').append("<img alt='' class='Loader' style='margin-top:15px;' src='"+ plugin_path +"images/icon/loading.gif' />");
			jQuery('.dirTitle').empty();
			jQuery('.dirTitle').append(cont_html);
			jQuery('.dirTitle').show();
			 directionsDisplay.setDirections(response);
			 jQuery('.Loader').remove();
			 jQuery('.gobackO').simpletip({
				fixed: true,
				position: 'left',
				content: tolTip
			 });
			 jQuery('#sloc_frmSearch').hide();
			 setTimeout(function() {
				UpdateSearchPnl();
			},1500);
		   }
		   else if ( status == google.maps.DirectionsStatus.NOT_FOUND){
				jQuery('#sl_err_msg').text("<?php _e('Address not found. Please try again', self::TEXT_DOMAIN); ?>.");
				jQuery('#sl_err_msg').removeClass('sl_error').addClass('sl_error').fadeOut(300).fadeIn(300);
				jQuery('#sl_GetDirec').removeClass('close');
		   }
		   else if ( status == google.maps.DirectionsStatus.ZERO_RESULTS){
				jQuery('#sl_err_msg').text("<?php _e('No directions found. Please try again', self::TEXT_DOMAIN); ?>.");
				jQuery('#sl_err_msg').removeClass('sl_error').addClass('sl_error').fadeOut(300).fadeIn(300);
				jQuery('#sl_GetDirec').removeClass('close');
		   }
		   else if ( status == google.maps.DirectionsStatus.INVALID_REQUEST){
				jQuery('#sl_err_msg').text("<?php _e('Invalid entry. Please try again', self::TEXT_DOMAIN); ?>.");
				jQuery('#sl_err_msg').removeClass('sl_error').addClass('sl_error').fadeOut(300).fadeIn(300);
				jQuery('#sl_GetDirec').removeClass('close');
		   }
		   if(result_res){
		   }
		 });
	  }
	}
giz_Locator.home = {
ajax: function (u, t, d, s, Rmsg) {
		jQuery.ajax({
			url: u,
			type: t,
			data: d,
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (msg) {
				if (typeof s === 'function'){
					s(msg);
				}else{
					return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajaxWithoutLoad: function (u, t, d, s, Rmsg) {
		jQuery.ajax({
			url: u,
			type: t,
			data: d,
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (msg) {
				if (typeof s === 'function'){
					s(msg);
				}else{
					return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajaxComplete: function (u, t, d, s, Rmsg, cFun) {
		jQuery.ajax({
			url: u,
			type: t,
			data: d,
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (msg) {
				if (typeof s === 'function'){
					s(msg);
				}else{
					return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajaxSer: function (u, t, d, s) {
		jQuery.ajax({
			url: u,
			type: t,
			data: d,
			dataType: "json",
			beforeSend: function (x) {
				if(x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
				}

                let loadAdwords = $('#sloc_LoadAdwords').is(":checked");
                let waitText = loadAdwords ? 'Loading Adwords data - Please wait' : 'Please wait';

                let customElement   = $("<div>", {
                    id      : "countdown",
                    css     : {
                        "font-size" : "50px"
                    },
                    text    : waitText,
                });

                $("#sloc_rightContent").LoadingOverlay("show", {custom  : customElement});
			},
			success: function (msg,status,shr) {

/*                console.log('msg.length: ' + msg.length);*/
                var sloc_tbNameVal = jQuery('#sloc_tbName').val();
                var sloc_tbCountryVal = jQuery('#sloc_tbCountry').val();
                var sloc_tbPlaceVal = jQuery('#sloc_tbPlace').val();

                if ((sloc_tbNameVal || sloc_tbCountryVal || sloc_tbPlaceVal ) && msg.length) {
                    jQuery('#sloc_leftBar2').css('display', 'block');
                } else {
                    jQuery('#sloc_leftBar2').css('display', 'none');
                }
                glRefresh();

				$("#sloc_rightContent").LoadingOverlay("hide", true);

                setTimeout(function(){
                    jQuery('#select_all').trigger('click');
                }, 2000);


				if (typeof s === 'function'){
					s(msg);
				}else{
					return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	}
}

</script>


	<!-- Left Side Panel -->
    

			<!-- Right Side Panel -->
			<div id="sloc_rightContent" style="float:left">
				<!-- Google Map -->
    <span id="sloc_topBar">
					<form action="#" method="post" id="sloc_frmSearch" onsubmit="return callOnsubmit();" >
						<div class="sl_field2" style="display:inline-block">
								<input id="sloc_tbName" class="wd75c" name="sloc_tbName" type="text" style="width:100px" placeholder="Name"/> 
								<input id="sloc_tbCountry" class="wd75c" name="sloc_tbCountry" type="text" style="width:100px" placeholder="Country"/> 

						</div>


						<div class="sl_field2" style="display:inline-block">
								<input id="sloc_tbPlace" class="wd75" name="sloc_tbPlace" type="text" style="width:200px"  placeholder="Zipcode / Address"/>
								<label class="lblForm" for="sloc_selRadius"><?php _e('Radius', self::TEXT_DOMAIN); ?> :</label>
								<select id="sloc_selRadius" name="sloc_selRadius">
									<option value="" >Select</option>
										<?php
//											$Radius=100;
                                            $sl_sql_str = "SELECT radius FROM `$sl_tb_mapradius` ORDER BY radius";
                                            $sl_select_obj = $wpdb->get_results($sl_sql_str);
                                            foreach ($sl_select_obj as $sl_radius_row) {
                                                $radSelect = ($sl_radius_row->radius == $Radius) ? ' selected=selected' : '';
                                                echo '<option value='.$sl_radius_row->radius." $radSelect>".$sl_radius_row->radius.'</option>';
                                            }
                                        ?>
								</select>
<?php if ($RadiusType == 'KM' || $RadiusType == 'Both') { ?>
								<input name="sloc_radius" id="sloc_rdKm" type="radio" value="KM" <?php echo $RadKmType; ?> /><label>&nbsp;<?php _e('Km', self::TEXT_DOMAIN); ?></label>
<?php } ?>
<?php if ($RadiusType == 'Miles' || $RadiusType == 'Both') { ?>
								<input name="sloc_radius" id="sloc_rdMiles" type="radio" value="Miles" <?php echo $RadMiType; ?> /><label>&nbsp;<?php _e('Miles', self::TEXT_DOMAIN); ?></label>
<?php } ?>

									<label class="lblForm" for="sloc_selCategory"><?php _e('Category', self::TEXT_DOMAIN); ?> :</label>
                                   

											<?php
function show_tree($lst, $lv = 1)
                                        {
                                            foreach ($lst as $itm) {
                                                if (isset($itm->children)) {
                                                    echo "<optgroup label=' ".$itm->category."'>";
                                                    show_tree($itm->children, $lv + 1);
                                                    echo '</optgroup>';
                                                } else {
                                                    //			$catSelect = (in_array($itm->categoryid,array(2,11,12,15))) ? ' selected=selected' : '';
                                                    $catSelect = ($itm->isdefault == 1) ? ' selected=selected' : '';
                                                    echo "<option value='".$itm->categoryid."' $catSelect data-level='$lv'>".$itm->category.$itm->isefault.'</option>';
                                                }
                                            }
                                        }
    $sl_sql_str = "SELECT DISTINCT C.`categoryid` , C.`category`, C.`parent` FROM `$sl_tb_storecat` AS C LEFT JOIN `$sl_tb_stores` AS S ON S.`type` = C.`categoryid` ORDER BY ord ASC";
    $sl_select_obj = $wpdb->get_results($sl_sql_str);

    $catTree = [];
    foreach ($sl_select_obj as $sl_cate_row) {
        if ($sl_cate_row->parent == 0) {
            $catTree[$sl_cate_row->categoryid] = $sl_cate_row;
        } else {
            $catTree[$sl_cate_row->parent]->children[$sl_cate_row->categoryid] = $sl_cate_row;
        }
    }
?>
        <select id="sloc_selCategory" name="sloc_selCategory" multiple=multiple>
<?php  show_tree($catTree); ?>
        </select>


 
								<input id="sloc_LoadAdwords" class="wd75c" name="sloc_LoadAdwords" type="checkbox" value="yes"/><label for="sloc_LoadAdwords">Show Adwords Data</label>

<a id="sl_nearStore" class="sloc_button" href="javascript:void(0)"  style="margin-left:4px"><?php _e('Search', self::TEXT_DOMAIN); ?></a>                                    

						</div>

						<?php if ($Categoryvi == 1 && false):?>
							<div class="sl_field sl_clear sl_catDrop" style="padding-top:5px; height:30px">
								<div class="fl wd23">
 </div>
								<div style="float:left">
								</div>
							</div>
						<?php endif; ?>
						<div id="sl_msgBox" class="sl_msg_box fl"></div>
<!--
						<div class="sl_field sl_clear" style="margin-top:10px; height:30px;">

						</div>
-->
					</form>
    
    </span>            
                
                
				<div id="sl_front_map_canvas" style="position: relative;height: 100%;">
				<div id="directions"></div>
				</div>
    <span id="sloc_bottomBar">
					<form action="#" method="post" id="sloc_frmSearch" onsubmit="return callOnsubmit();" >
						<div class="sl_field2" style="display:inline-block">

								<label><input name="res_selection" id="select_all" type="radio" value="select_all"/>&nbsp;Select All</label>

								<label><input name="res_selection" id="select_adwords" type="radio" value="select_adwords"/>&nbsp;Select with Adwords Data</label>
								
								<label><input name="res_selection" id="select_none" type="radio" value="select_none"/>&nbsp;Clear All</label>

						</div>

					</form>
    
    </span>            
                
<script>
    jQuery(function($) {
		
        $('#sloc_selCategory').multipleSelect({
            width: '120px',
			maxHeight: '400px',
			textTemplate: function ($el) {
/*				alert($el.attr('data-group').val());*/
				return $el.html();
			}			
        });
		jQuery('#sloc_selCategory').multipleSelect('setSelects',[2,11,12,15]);

    });
</script>				<!-- Google Map -->
			</div>
     <section class="map-sidebar" id="sloc_leftBar2" style="display: none;">
        <div class="mapresults-title"><h3 class="mapresults-title_content">Search Results</h3></div>

        <div class="mapresults-list-wrapper sl_clear" id="sl_new_searchResult">
          <ul class="mapresults-list sl_SearchR">

          </ul>
        </div><!--mapresults-list-wrapper-->
 <?php /*
        <div class="map-pages">&nbsp;
         <ul class="map-pages-list">
            <li><a href="">Prev</a></li>
            <li><a href="">1</a></li>
            <li><a href="" class="map-pages-list-current">2</a></li>
            <li><a href="">3</a></li>
            <li><a href="">4</a></li>
            <li><a href="">5</a></li>
            <li><a href="">Next</a></li>
          </ul>

        </div>

*/ ?>

      </section>

         
			<div id="sl_map_form">
				<form  method="post" action="#" id="sl_frmGetDirection" onsubmit="return callDirectionSub();">
					<input type="hidden" id="sloc_hdfLat" name="sloc_hdfLat" />
					<input type="hidden" id="sloc_hdfLng" name="sloc_hdfLng" />
						<h3 id="dirTitle" class="dirTitles" ><?php _e('Get Your Directions', self::TEXT_DOMAIN); ?></h3>
						<span></span>
						<div id="sl_map_form_form">
							<label><strong><?php _e('From', self::TEXT_DOMAIN); ?> :</strong> <input class="spritedd" name="fromdir" id="fromdir"/></label>
							<label><strong><?php _e('To', self::TEXT_DOMAIN); ?> :</strong> <input class="spritedd" name="todir" id="todir"/></label>
							<div class="sl_pad5" style="height:25px;">
								<div class="fl"><label style="padding-top: 3px;"><strong><?php _e('Showing Distance In', self::TEXT_DOMAIN); ?> : </label></strong></div>
								<div class="fl">
									<div class="fl">
										<input type="radio" name="rdUnit" id="rdUnitKm" class="fl" checked="checked" /><label style="padding-top: 3px;width: 60px;"><?php _e('KM', self::TEXT_DOMAIN); ?></label>
									</div>
									<div class="fl">
										<input type="radio" name="rdUnit" id="rdUnitMi" class="fl"/><label style="padding-top: 3px;width: 60px;"><?php _e('Miles', self::TEXT_DOMAIN); ?></label>
									</div>
								</div>
							</div>
							<div id="actions" class="clear">
								<div class="sl_msg_box fl" id="sl_err_msg" style="width:71%"></div>
								<div class="fl" style="margin-top:5px; margin-left:4px;"><a class="sloc_button" id="sl_GetDirec" href="javascript:void(0)" onclick="return getDir();"><?php _e('Get Directions', self::TEXT_DOMAIN); ?></a></div>
							</div>
						</div>
						<a id="close_x" class="close sprited" href="javascript:void(0);"><?php _e('close', self::TEXT_DOMAIN); ?></a>
				</form>
            </div>

			<div style='display:none'>
				<input type="hidden" id="sloc_hdfOLat" name="sloc_hdfOLat" />
				<input type="hidden" id="sloc_hdfOLng" name="sloc_hdfOLng" />
				<input type="hidden" id="sloc_hdfAddress" name="sloc_hdfAddress" />
				<input type="hidden" id="sloc_hdfORad" name="sloc_hdfORad" />
				<input type="hidden" id="sloc_hdfORadTy" name="sloc_hdfORadTy" />
				<input type="hidden" id="sloc_hdfOCatId" name="sloc_hdfOCatId" value="0" />
			</div>

