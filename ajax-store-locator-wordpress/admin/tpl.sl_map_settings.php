<?php	
	wp_enqueue_script('sl_admin_colorbox_script', plugins_url( '/js/plugin/jquery.colorbox.js', dirname(__FILE__) ), '', false, true);
	wp_enqueue_script('sl_admin_iphonecheck_script', plugins_url( '/js/plugin/iPhone-Checkbox.js', dirname(__FILE__) ), '', false, true);	

	$sql_str = "SELECT * FROM `$sl_tb_mapsetting` LIMIT 1";
	$MapId= $ZoomLevel= $MapType= $ControlPosition = $Lat = $Lng = $Address = $RadType = $customCity = $map_api_key = "";
	$FunType = __( 'Save', self::TEXT_DOMAIN );
	$ZoomControl = $PanControl = $StreetViewControl = $Map_Lang = "";
	$sl_select_obj = $wpdb->get_results( $sql_str );
	foreach ($sl_select_obj as $sl_mapset_row) {
		$MapId 				= $sl_mapset_row->id;
		$ZoomLevel 			= $sl_mapset_row->zoomlevel;
		$MapType			= $sl_mapset_row->maptype;
		$Rad				= $sl_mapset_row->radius;
		if($sl_mapset_row->zoomcontrol == 1){
			$ZoomControl 		= "checked='checked'";
		}
		else{
			$ZoomControl ="";
		}
		$PanControl 		= ($sl_mapset_row->pancontrol == 1) ? "checked='checked'" : "";
		$StreetViewControl 	= ($sl_mapset_row->streetviewcontrol == 1) ? "checked='checked'" : "";
		$ControlPosition 	= $sl_mapset_row->controlposition;
		$Lat 				= $sl_mapset_row->lat;
		$Lng 				= $sl_mapset_row->lng;
		$Address 			= $sl_mapset_row->address;
		$RadType 			= $sl_mapset_row->radiustype;
		$customCity			= $sl_mapset_row->customcity;
		$Map_Lang			= $sl_mapset_row->map_language;
		$map_api_key		= $sl_mapset_row->map_api_key;
		$FunType			= __( 'Update', self::TEXT_DOMAIN );
	}					
?>
				
<script type="text/javascript">
var map;
var marker;
var sl_plugin_url = "<?php echo plugins_url('/',  dirname(__FILE__)); ?>";
var geocoder = new google.maps.Geocoder();
google.maps.visualRefresh = true;
var $ = jQuery.noConflict();
jQuery(document).ready(function(){
$('.iPhone :checkbox').iphoneStyle();
	/***** Initialize Google Map ******/
    LoadWOQ();
	
	/***** Initialize Content height and width ******/
    jQuery('#rightContent').css({'height' : jQuery('#sl_frmMapSettings').height() + 200});
	
	/***** Validate The Form And saving The Data******/
	jQuery("#sl_frmMapSettings").validate({ 
		rules:{
			'txtstoreLogo' : {
				required: false,
				accept : 'jpg|png|gif'
			},
			'sloc_customCity' : {
				minlength: 4,
			}
		},
		messages:{
			'txtstoreLogo' : {
				accept : '<?php _e( 'Allowed only image file', self::TEXT_DOMAIN ); ?>'
			},
			'sloc_customCity' : {
				minlength: '<?php _e( 'Atleast four character to be enter', self::TEXT_DOMAIN ); ?>',
			}
		},	
   		submitHandler: function(form) {
				var img, reader, file;
				formdata = false;
				if (window.FormData) {
					formdata = new FormData();
					}
				file =  document.getElementById('sloc_fuStoreLogo').files;				
				if(file.length>0){
					file = file[0];				
					if (!!file.type.match(/image.*/)) {
						if ( window.FileReader ) {
							reader = new FileReader();
						}
						if (formdata) {
							formdata.append("sloc_fuStoreLogo", file);
						}
					}
				}  				
				giz_Locator.util.ajxBlockUI("<?php _e( 'Saving Data', self::TEXT_DOMAIN );?>...", true, "success", sl_plugin_url);
				var cbGoogle 	= "0";
			 	var cbFacebook	= $('#sloc_cbFacebook').is(':checked') ? "1" : "0";
				var cbTwitter	= $('#sloc_cbTwitter').is(':checked') ? "1" : "0";
				var cbPinterest	= $('#sloc_cbPinterest').is(':checked') ? "1" : "0";
				var zoomControl = 0;
				var panControl = 0;
				var streetControl = 0;
				if(jQuery('#sloc_cbZoomControl').is(':checked')){
					zoomControl = 1;
				}
				if(jQuery('#sloc_cbpanControl').is(':checked')){
					panControl = 1;
				}
				if(jQuery('#sloc_cbStreet').is(':checked')){
					streetControl = 1;
				}
				formdata.append("FunType", 'MapSettingSave');
				formdata.append("FunTypeT", jQuery('#sloc_btnSave').val());
				formdata.append("sloc_Zoom", jQuery('#sloc_ddlZoom').val());
				formdata.append("sloc_MapType", jQuery('#sloc_ddlMapType').val());
				formdata.append("sloc_ZoomControl", zoomControl);
				formdata.append("sloc_PanControl", panControl);
				formdata.append("sloc_StreetControl", streetControl);
				formdata.append("sloc_CPosition", jQuery('#sloc_ddlControlPosition').val());
				formdata.append("sloc_Lat", jQuery('#sloc_tbLat').val());
				formdata.append("sloc_Lng", jQuery('#sloc_tbLng').val());
				formdata.append("sloc_Address", jQuery('#sloc_ddlCountry').val());
				formdata.append("sloc_mapId", jQuery('#sloc_hdfMapSettingsId').val());
				formdata.append("sloc_radiusType", jQuery('#sloc_ddlRadiusType').val());
				formdata.append("sloc_radius", jQuery('#sloc_ddlRadius').val());
				formdata.append("sloc_customCity", jQuery('#sloc_customCity').val());
				formdata.append("sloc_gPlus", cbGoogle);
				formdata.append("sloc_fBook", cbFacebook);
				formdata.append("sloc_Tweet", cbTwitter);
				formdata.append("sloc_pinIt", cbPinterest);
				formdata.append("sloc_mapLang", jQuery('#sloc_ddlMapLang').val());
				formdata.append("sloc_mapAPIKey", jQuery('#sloc_mapAPIKey').val());
				formdata.append('action', 'sl_dal_mapsettings');		
				
				giz_Locator.util.ajaxFile("admin-ajax.php","POST", formdata ,ajResult, "<?php _e( 'Data has been saved successfully', self::TEXT_DOMAIN ); ?>.");
				if(jQuery('#sloc_btnSave').val() == "Save")
					giz_Locator.util.clearform('#frmMapSettings');
   		}
	});
	
	$("#sloc_addRadius").colorbox({ width: "400px", inline: true, href: "#sl_frmAddNew", overlayClose: false, escKey: false, opacity: "0.75" });
	
	/**************** Adding New Radius ************/
	jQuery("#sl_frmRadius").validate({		
   		submitHandler: function(form) {
			closeOverlay();
			giz_Locator.util.ajxBlockUI("<?php _e( 'Saving Data', self::TEXT_DOMAIN );?>...", true, "success", sl_plugin_url);
			var sl_dal_mapset = { action: 'sl_dal_mapsettings', FunType : 'RadiusSave', sloc_Radius: jQuery('#sloc_tbRadius').val() };
			giz_Locator.util.ajax("admin-ajax.php","POST", sl_dal_mapset, ajRadResult, "<?php _e( 'Data has been saved successfully', self::TEXT_DOMAIN ); ?>.");
			giz_Locator.util.clearform('#sl_frmRadius');
   		}
	});
	/************** Remove Radius *******************/
	jQuery('#sloc_removeRadius').on('click', function(){
		var rad = jQuery('#sloc_ddlRadius').val();
		if(rad.length > 0){			
			giz_Locator.util.ajxBlockUI("<?php _e( 'Deleting Data', self::TEXT_DOMAIN );?>...", true, "success", sl_plugin_url);
			var sl_dal_mapset = { action: 'sl_dal_mapsettings', FunType : 'RadiusDelete', sloc_Radius:rad };			
			giz_Locator.util.ajax("admin-ajax.php","POST", sl_dal_mapset, ajRadResult, "<?php _e( 'Data has been deleted successfully', self::TEXT_DOMAIN ); ?>.");
		}
		else{
			alert("Please Select Radius to Delete");
			jQuery('#sloc_ddlRadius').focus();
		}
	});
	
	jQuery('#leftBar').css({ 'width' : jQuery('#sl_frmMapSettings').width() });
	jQuery('#rightContent').css({ 'width' : jQuery('#wpbody').width() - jQuery('#sl_frmMapSettings').width() - 48 });
	jQuery(window).resize(function(){
		jQuery('#leftBar').css({ 'width' : jQuery('#sl_frmMapSettings').width() });
		if(jQuery('#wpbody').width() <= 846){
			jQuery('#rightContent').css({ 'width' : jQuery('#wpbody').width() - jQuery('#sl_frmMapSettings').width() - 71 });
		}else{
			jQuery('#rightContent').css({ 'width' : jQuery('#wpbody').width() - jQuery('#sl_frmMapSettings').width() - 48 });
		}
	});
	
});

/***** Getting Lat&Lng From DataBase ******/
function LoadWOQ(){
    var Lat = 12.97160;
    var Lan = 77.59456; 
	var sl_dal_mapset = { action: 'sl_dal_mapsettings', FunType : 'MapDefSelect'};
	giz_Locator.util.ajaxSer("admin-ajax.php", "POST", sl_dal_mapset, sl_map_ReturnVal);
	function sl_map_ReturnVal(markers){
		if( markers.length <= 0 ){
			initialize_map(Lat, Lan);
		 } 
		 else{
			for (var i = 0; i < markers.length; i++) {
				   Lat = parseFloat(markers[i]["lat"]),
				   Lan = parseFloat(markers[i]["lng"]);
			  }
			initialize_map(Lat, Lan);
	     }
	};
}

/*** Load Or Bind Google Data From DB using Ajax XML Response ***/
function LoadData(url, callback) {	
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };
      request.open('GET', url, true);
      request.send(null);
}

function doNothing() {}

/***** Initialize Google Map Using DB Data ******/
function initialize_map(Lat, Lan) {
  var mapOptions ={		
		center: new google.maps.LatLng(Lat, Lan),
        zoom: <?php echo $ZoomLevel; ?>,
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
		streetViewControl: <?php echo $streetControl; ?>,
  }
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);	
	var marker = new google.maps.Marker({
			position: new google.maps.LatLng(Lat , Lan),
			map:map,
			draggable:false,
			borderPadding: 50, 
			maxZoom: 15,
			animation: google.maps.Animation.DROP
		});
}

/***** Marker Event (Bounce) ******/
function toggleBounce() {
	  if (marker.getAnimation() != null) {
		marker.setAnimation(null);
	  } else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	  }
}

/***** Getting Marker Latitude and Langitude ******/
function markerPoint(){
	jQuery('#sloc_tbLat').val(marker.position.lat().toFixed(6)); jQuery('#sloc_tbLng').val(marker.position.lng().toFixed(6));
}
/***** Getting Latitude and Langitude using Custom Address ******/
function customCity(address){
	jQuery('#sloc_ddlCountry')[0].selectedIndex = -1;
	getPoint(address);
}
/***** Getting Latitude and Langitude using Country List ******/
function CountryGeo(address){
	jQuery('#sloc_customCity').val('');
	getPoint(address)
}
/***** Getting Latitude and Langitude using Address ******/
function getPoint(address){
    geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
				var latitude = results[0].geometry.location.lat();
				var longitude = results[0].geometry.location.lng();
				drop(latitude, longitude);
			}
		});
}

/***** Add New Marker To New Address ******/
function drop(Lat , Lan) {
  setTimeout(function() {addMarker(Lat , Lan);}, 200);
}

/***** Add New Marker To New Address Latitude and Langitude ******/
function addMarker(Lat , Lan) {
	var mapOptions ={			
			center: new google.maps.LatLng(Lat, Lan),
			zoom: <?php echo $ZoomLevel; ?>,
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
      }
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	var iconpath = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|FE7569";
	var marker = new google.maps.Marker({
			position: new google.maps.LatLng(Lat , Lan),
			map:map,
			draggable:false,
			borderPadding: 50, 
			maxZoom: 15,
			animation: google.maps.Animation.DROP
		});	 
	jQuery('#sloc_tbLat').val(Lat.toFixed(6)); jQuery('#sloc_tbLng').val(Lan.toFixed(6));
}

function ajResult(returnVal){
	if(returnVal == '1'){
		document.location.reload(true);
		return true;		
	}
	else{
		LoadWOQ();
	}
}

function closeOverlay() {
  $.colorbox.close();
}

function ajRadResult(returnVal){
	if(jQuery.trim(returnVal) == '1'){			
			var sl_dal_mapset = { action: 'sl_dal_mapsettings', FunType : 'RadiusSelect' };
			giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_dal_mapset, ajRadiusVal);
		}
		else{
			jQuery('.Raderror').html("<?php _e( 'Failed To Save Radius', self::TEXT_DOMAIN ); ?>..");
		}
}

function ajRadiusVal(returnVal){
			$('#sloc_ddlRadius').empty();
			var content="";
			for (var i = 0; i < returnVal.length; i++){
					content += "<option  value="+ returnVal[i]["radius"] +">"+ returnVal[i]["radius"] + "</option>";
			   
			}
			$('#sloc_ddlRadius').append(content);
}
  
function ajReturnVal(returnVal){	
	$('#ddlCategory').empty();
	$('#ddlCategory').append("<option value=''>Select</option>");
	for (var i = 0; i < returnVal.length; i++){
	   $('#ddlCategory').append("<option value="+ returnVal[i]["categoryid"] +">"+ returnVal[i]["category"] +"</option>");
	}
}
</script>
<div class="sl_menu_icon sloc_mapset sl_icon32" ><br /></div>
			<h2 class="sl_menu_title"><?php _e( 'Map Settings', self::TEXT_DOMAIN ); ?></h2>				
		<div class="clearb">
			<div id="leftBar">
			<h3><?php _e( 'Plugin Settings', self::TEXT_DOMAIN ); ?></h3>
			<div class="iPhone">
				<?php 
					$sql_str = "SELECT * FROM `$sl_tb_plugsetting`";
					$gPlus= $fBook= $Tweet= $pinIt= $wHours = "";
					$sl_select_obj = $wpdb->get_results( $sql_str );
					foreach ($sl_select_obj as $sl_plugset_row) {
						$gPlus 		= ($sl_plugset_row->google 		== "1") ? "checked='checked'" : "";
						$fBook 		= ($sl_plugset_row->facebook 	== "1") ? "checked='checked'" : "";
						$Tweet		= ($sl_plugset_row->twitter 	== "1") ? "checked='checked'" : "";
						$pinIt 		= ($sl_plugset_row->printrest 	== "1") ? "checked='checked'" : "";
						$wHours 	= ($sl_plugset_row->info		== "1") ? "checked='checked'" : "";
					}
				?>
					
					<div class="pad5 clearb">
						<div class="fl wd150"><label><?php _e( 'Facebook', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl"><input id="sloc_cbFacebook" name="sloc_cbFacebook" type="checkbox" <?php echo $fBook; ?> /></div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd150"><label><?php _e( 'Twitter', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl"><input id="sloc_cbTwitter" name="sloc_cbTwitter" type="checkbox" <?php echo $Tweet; ?> /></div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd150"><label><?php _e( 'Pinterest', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl"><input id="sloc_cbPinterest" name="sloc_cbPinterest" type="checkbox" <?php echo $pinIt; ?> /></div>
					</div>									
			</div>
			<h3 class="clearb"><?php _e( 'Map Settings', self::TEXT_DOMAIN ); ?></h3>
				<form id="sl_frmMapSettings" name="sl_frmMapSettings" action="#"	enctype="multipart/form-data" method="post" >
				
				<input id="sloc_hdfMapSettingsId" name="sloc_hdfMapSettingsId" type="hidden" value = "<?php echo $MapId; ?>" />
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Map API Key', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<input id="sloc_mapAPIKey" name="sloc_mapAPIKey" type="text" class="inp-form" style="width:230px;" value="<?php echo $map_api_key; ?>"  />
						</div>
					</div>
					<div class="pad5">
						<div class="fl wd60">
						<label><?php _e( 'Zoom Level', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlZoom" name="sloc_ddlZoom" class="sl_input_sel" style="width:65px;">
							<?php 
								for($i = 1; $i<=18; $i++):
							?>
							<option value="<?php echo $i; ?>" <?php if($ZoomLevel == $i){ echo ' selected="selected"'; } ?> ><?php echo $i; ?></option>
							<?php 
							endfor;											
							?>
							</select>
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Map Type', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlMapType" name="sloc_ddlMapType" class="sl_input_sel" style="width:100px;">
								<option value="HYBRID" 		<?php if($MapType == "HYBRID"){ echo ' selected="selected"'; } ?> >Hybrid</option>
								<option value="ROADMAP" 	<?php if($MapType == "ROADMAP"){ echo ' selected="selected"'; } ?> >Roadmap</option>
								<option value="SATELLITE" 	<?php if($MapType == "SATELLITE"){ echo ' selected="selected"'; } ?> >Satellite</option>	
								<option value="TERRAIN" 	<?php if($MapType == "TERRAIN"){ echo ' selected="selected"'; } ?> >Terrain</option>										
							</select>
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Measurement', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlRadiusType" name="sloc_ddlRadiusType" class="sl_input_sel" style="width:75px;">
								<option value="KM" 		<?php if($RadType == "KM"){ echo ' selected="selected"'; } ?> ><?php _e( 'KM', self::TEXT_DOMAIN ); ?></option>
								<option value="Miles" 	<?php if($RadType == "Miles"){ echo ' selected="selected"'; } ?> ><?php _e( 'Miles', self::TEXT_DOMAIN ); ?></option>
								<option value="Both" <?php if($RadType == "Both"){ echo ' selected="selected"'; } ?>><?php _e( 'Both', self::TEXT_DOMAIN ); ?></option>
							</select>
						</div>										
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Radius', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlRadius" name="sloc_ddlRadius" class="sl_input_sel" style="width:75px">	
								<?php 
									$sql_str = "SELECT * FROM `$sl_tb_mapradius`";
									$sl_select_obj = $wpdb->get_results( $sql_str );
									foreach ($sl_select_obj as $sl_maprad_row) {
										$sel = '';
										if($Rad == $sl_maprad_row->radius){  $sel = ' selected="selected"';  }
										echo "<option value=".$sl_maprad_row->radius." $sel >".$sl_maprad_row->radius."</option>";
									}
								?>
							</select>
						</div>
						<div class="fl">
							<a href="#" id="sloc_addRadius" name="sloc_addRadius" alt="<?php _e( 'Add Radius', self::TEXT_DOMAIN ); ?>" ><img src="<?php echo plugins_url( '/images/icon/icon_plus.png', dirname(__FILE__) ); ?>" alt="<?php _e( 'Add Radius', self::TEXT_DOMAIN ); ?>" title="<?php _e( 'Add Radius', self::TEXT_DOMAIN ); ?>" style="padding: 5px;" /></a>
						</div>
						<div class="fl">
							<a href="#" id="sloc_removeRadius" name="sloc_removeRadius" alt="<?php _e( 'Remove Radius', self::TEXT_DOMAIN ); ?>" ><img src="<?php echo plugins_url( '/images/icon/icon_minus.png', dirname(__FILE__) ); ?>" alt="<?php _e( 'Remove Radius', self::TEXT_DOMAIN ); ?>" title="<?php _e( 'Remove Radius', self::TEXT_DOMAIN ); ?>" style="padding: 5px;" /></a>
						</div>
						</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Zoom Control', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl padT10 iPhone">
							<input id="sloc_cbZoomControl" name="sloc_cbZoomControl" type="checkbox" <?php echo $ZoomControl; ?>  />
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Pan Control', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl padT10 iPhone">
							<input id="sloc_cbpanControl" name="sloc_cbpanControl" type="checkbox" <?php echo $PanControl; ?>  />
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'StreetView', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl padT10 iPhone">
							<input id="sloc_cbStreet" name="sloc_cbStreet" type="checkbox" <?php echo $StreetViewControl; ?>  />
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Control Position', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<?php 
								$ControlPositionArr = array("LEFT_BOTTOM" => "Left Bottom", "LEFT_TOP" => "Left Top", "RIGHT_BOTTOM" => "Right Bottom", "RIGHT_TOP" => "Right Top");											
							?>
							<select id="sloc_ddlControlPosition" name="sloc_ddlControlPosition" class="sl_input_sel" style="width:110px;">
								
							<?php
								foreach ($ControlPositionArr as $key => $value) {
							?>
							<option value="<?php echo $key; ?>" <?php if($ControlPosition == $key){ echo ' selected="selected"'; } ?> ><?php echo $value; ?></option>
							<?php 
								}
							?>									
							</select>
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Map Language', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlMapLang" name="sloc_ddlMapLang" class="sl_input_sel" style="width:160px">	
								<?php 
									$sql_str = "SELECT * FROM `$sl_tb_maplanguage`";
									$sl_select_obj = $wpdb->get_results( $sql_str );
									foreach ($sl_select_obj as $sl_maplng_row) {
										$sel_Lng = '';
										if($Map_Lang == $sl_maplng_row->language_code){  $sel_Lng = ' selected="selected"';  }
										echo "<option value=".$sl_maplng_row->language_code." $sel_Lng >".$sl_maplng_row->language."</option>";
									}
								?>
							</select>
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Country', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlCountry" name="sloc_ddlCountry" class="sl_input_sel" onchange = "return CountryGeo(this.value)">						
						<?php 
						$sql_str = "SELECT * FROM `$sl_tb_country`";
						$sql_select_obj = $wpdb->get_results($sql_str);
						foreach($sql_select_obj as $sl_country_row){
							$sel_cu = "";
							if(trim($Address) == trim($sl_country_row->country)){  $sel_cu = ' selected="selected"';  }
							echo '<option value="'.$sl_country_row->country.'" '.$sel_cu.' >'.$sl_country_row->country.'</option>';
						}
						?>
					
							</select>
						</div>	
					</div>
					<div class="pad5 clearb" style="text-align:center"><label>Or</label></div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Custom Location', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<input id="sloc_customCity" name="sloc_customCity" type="text"  class="inp-form" style="width: 230px;" onblur="return customCity(this.value)" value="<?php echo $customCity; ?>"  />
						</div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd60"><label><?php _e( 'Def. Store Logo', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fileContain fl wd250">
							<input type="file" id="sloc_fuStoreLogo" name="sloc_fuStoreLogo" onchange="getElementById('txtstoreLogo').value = getElementById('sloc_fuStoreLogo').value;" class="sl_browseHide" />
							<input type="hidden" id="txtstoreLogo" name="txtstoreLogo" />
						</div>
					</div>
					<div class="pad5 clearb" style="display:none">
						<div class="fl wd60"><label><?php _e( 'Latitude', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<input id="sloc_tbLat" name="sloc_tbLat" class="required inp-form" type="text" value = "<?php echo $Lat; ?>" readonly="readonly" />
						</div>
					</div>
					<div class="pad5 clearb" style="display:none">
						<div class="fl wd60"><label><?php _e( 'Longitude', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<input id="sloc_tbLng" name="sloc_tbLng" class="required inp-form" type="text" value = "<?php echo $Lng; ?>" readonly="readonly" />
						</div>
					</div>					
					<div class="pad5 clearb">
						<input id="sloc_btnSave" name="sloc_btnSave" class="btn btn-blue" type="submit" value = "<?php echo $FunType; ?>" /> 
					</div>
				</form>
			</div>
			<div id="rightContent">
			<!-- Google Map -->
				<div id="map_canvas" style="width: 100%; height: 100%">
				</div>
				<!-- Google Map -->
			</div>
		</div>
		<!--  end table-content  -->
		<div class="clear">
		</div>

<div style="display:none;">
<div id="sl_frmAddNew"  class="overlaypanel" >
	<form id="sl_frmRadius" name="sl_frmRadius" action="#" enctype="multipart/form-data" method="post">
		<div class="pad5 pal5">
			<div class="fl wd60"><label><?php _e( 'New Radius', self::TEXT_DOMAIN ); ?></label></div>
			<div class="fl"><input id="sloc_tbRadius" name="sloc_tbRadius" class="required inp-form number" type="text" /></div>			
		</div>
		<span class="error Raderror"></span>
		<div class="pad5 wd200 clearb pal5"><input id="sloc_btnRadSave" name="sloc_btnRadSave" class="btn btn-blue" type="submit" value="<?php _e( 'Save', self::TEXT_DOMAIN ); ?>" /> </div>
   </form>

</div>
</div>