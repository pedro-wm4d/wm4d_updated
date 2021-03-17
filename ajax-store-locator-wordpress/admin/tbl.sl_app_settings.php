<?php
	wp_enqueue_script('sl_admin_colorbox_script', plugins_url( '/js/plugin/jquery.colorbox.js', dirname(__FILE__) ), '', false, true);
	wp_enqueue_script('sl_admin_iphonecheck_script', plugins_url( '/js/plugin/iPhone-Checkbox.js', dirname(__FILE__) ), '', false, true);

	$SLogo = $CategoryV = $sMode = $mPos = $LoadMark = $LogoVisible = ""; $sp_marker_width = 21; $sp_marker_height = 34; $sp_logo_width = 64; $sp_logo_height = 64;
	$sql_SetStr = "SELECT * FROM `$sl_tb_appsetting` LIMIT 0 , 1";
	$sl_select_obj = $wpdb->get_results( $sql_SetStr );	
	$sp_markersize = "";
	$sp_logosize = $CharId = $preferCountry = $isSingleCountry = $applicationCss = "";
	foreach($sl_select_obj as $sl_appset_row){
		$CategoryV 				= ($sl_appset_row->category == 1) ? "checked ='checked'" : "";
		$LoadMark 				= ($sl_appset_row->load_location == 1) ? "checked ='checked'" : "";
		$LogoVisible			= ($sl_appset_row->logo_visible == 1) ? "checked ='checked'" : "";
		$sMode					= $sl_appset_row->searchmode;
		$mPos					= $sl_appset_row->mapposition;
		$sp_markersize 			= json_decode($sl_appset_row->markersize);
		$sp_logosize 			= json_decode($sl_appset_row->logosize);
		$CharId 				= $sl_appset_row->charset_id;
		$preferCountry 			= $sl_appset_row->preferred_country;
		$isSingleCountry 		= ($sl_appset_row->enable_single_country == 1) ? "checked ='checked'" : "";
		$applicationCss 		= $sl_appset_row->locator_css;
	}
	if(count($sp_markersize)>0){
		$sp_marker_width 	= $sp_markersize[0];
		$sp_marker_height 	= $sp_markersize[1];
	}
	if(count($sp_logosize)>0){
		$sp_logo_width 		= $sp_logosize[0];
		$sp_logo_height 	= $sp_logosize[1];
	}
	
?>
<script type="text/javascript">
var sl_plugin_url = "<?php echo plugins_url('/',  dirname(__FILE__)); ?>";
var $ = jQuery.noConflict();
jQuery(document).ready(function() {
	var changeTooltipPosition = function(event) {
	  var tooltipX = event.pageX - 8;
	  var tooltipY = event.pageY + 8;
	  jQuery('div.sl_tooltip').css({top: tooltipY, left: tooltipX});
	};
	var arrHtml = new Array();
	arrHtml["sl_mapHint"] = '<div class="sl_tooltip"><div><div class="fl wd200 pad5"><b><?php _e( 'Right', self::TEXT_DOMAIN ); ?>:</b> <?php _e( 'The map layout will align right side of the browser', self::TEXT_DOMAIN ); ?>.</div> <div class="fl"><img src="'+sl_plugin_url+'images/icon/right.png" alt="" style="height:64px" /></div></div><div><div class="fl wd200 pad5"><b><?php _e( 'Left', self::TEXT_DOMAIN ); ?>:</b> <?php _e( 'The map layout will align left side of the browser', self::TEXT_DOMAIN ); ?>.</div><div class="fl"><img src="'+sl_plugin_url+'images/icon/left.png" alt="" style="height:64px" /></div></div></div>';
	arrHtml["sl_sModeHint"] = '<div class="sl_tooltip"><div><div class="fl wd200 pad5"><b><?php _e( 'Search', self::TEXT_DOMAIN ); ?>:</b> <?php _e( 'This is the custom search. You can find out the store list using custom option', self::TEXT_DOMAIN ); ?>.</div> <div class="fl"><img src="'+sl_plugin_url+'images/icon/search.png" alt="" style="height:64px" /></div></div><div><div class="fl wd200 pad5"><b><?php _e( 'Browse', self::TEXT_DOMAIN ); ?>:</b> <?php _e( 'This will list the all store list by country', self::TEXT_DOMAIN ); ?>.</div><div class="fl"><img src="'+sl_plugin_url+'images/icon/browse.png" alt="" style="height:64px" /></div></div></div>';
	arrHtml["sl_sCatHint"] = '<div class="sl_tooltip"><?php _e( 'The category dropdown will add to the Browse/Search mode layout', self::TEXT_DOMAIN ); ?>.</div>';
	arrHtml["sl_sLocationHint"] = '<div class="sl_tooltip"><?php _e( 'Select country to display at top', self::TEXT_DOMAIN ); ?>.</div>';
	arrHtml["sl_SingleCHint"] = '<div class="sl_tooltip"><?php _e( 'Applicable for single country.', self::TEXT_DOMAIN ); ?>.</div>';
	arrHtml["sl_sLoadHint"] = '<div class="sl_tooltip"><?php _e( 'This option will load all locations when page loads and it will work only "Search Mode" is in "Search"', self::TEXT_DOMAIN ); ?>.</div>';
	arrHtml["sl_sLogoHint"] = '<div class="sl_tooltip"><?php _e( 'The logo will appear in location results', self::TEXT_DOMAIN ); ?>.</div>';
	
	var showTooltip = function(event) {		
	  $('div.sl_tooltip').remove();
	  $(arrHtml[event.currentTarget.id]).appendTo('body');
	  changeTooltipPosition(event);
	};
 
	var hideTooltip = function() {
	   $('div.sl_tooltip').remove();
	};
 
	$("span.slo_tool_tip").bind({
	   mousemove : changeTooltipPosition,
	   mouseenter : showTooltip,
	   mouseleave: hideTooltip
	});	
	
	$('.sl_catCb input:checkbox, .sl_loadCb input:checkbox, .sl_logoCb input:checkbox').iphoneStyle({
		resizeHandle: true,
      	resizeContainer: true,
		onChange: function(elem, value) {
		}
	});
	<?php if($sMode == "BM"): ?>
		singleCload();
	<?php endif; ?>
	
	function singleCload(){
		var onchange_checkboxIm = $('.sl_singlecountry input:checkbox').iphoneStyle({
			resizeHandle: true,
			resizeContainer: true,
			checkedLabel: 'YES',
			uncheckedLabel: 'NO',
			onChange: function(elem, value) {
				if(value){
					giz_Locator.util.ajax("admin-ajax.php","POST", 'action=sl_dal_mapsettings&FunType=CheckCountry' ,function(ajxResult){
						if(jQuery.trim(ajxResult) > 1){
							onchange_checkboxIm.prop('checked', !onchange_checkboxIm.is(':checked')).iphoneStyle("refresh");
							alert('<?php _e( 'It works only single country', self::TEXT_DOMAIN ); ?>.');
						}else if(jQuery.trim(ajxResult) == 0){
							onchange_checkboxIm.prop('checked', !onchange_checkboxIm.is(':checked')).iphoneStyle("refresh");
							alert('<?php _e( 'No records found', self::TEXT_DOMAIN ); ?>.');
						}
					}, "<?php _e( 'Data has been saved successfully', self::TEXT_DOMAIN ); ?>.");
				}
			}
		});
	}

	var windowsHeight = $(window).height();
	var windowsWidth = $(window).width();
	$('#sysSet,#storeSet').css({'width': (windowsWidth/2)-55});
	$('.iPhone :checkbox').iphoneStyle();	
	jQuery("#sl_frmsysSet").validate({ 	 
		rules:{
				'sloc_markerheight' : {
					number : true
				},
				'sloc_markerwidth' : {
					number : true
				},
				'sloc_logoheight' : {
					number : true
				},
				'sloc_logowidth' : {
					number : true
				}
			},
			messages:{
				'sloc_markerheight' : {
					number : ''
				},
				'sloc_markerwidth' : {
					number : ''
				},
				'sloc_logoheight' : {
					number : ''
				},
				'sloc_logowidth' : {
					number : ''
				}
		},		
   		submitHandler: function(form) {
				var img, reader, file;
				formdata = false;
				if (window.FormData) {
					formdata = new FormData();
				}				
				var fields = $(":input").serializeArray();
				jQuery.each(fields, function(i, field){
					  var cbCategory = 0;							 
					  if(field.name == "sloc_cbCategory"){
							cbCategory = $('#sloc_cbCategory').is(':checked') ? 1 : 0;
							formdata.append(field.name, cbCategory);
					  }
					  else{
							formdata.append(field.name, field.value);
					  }
				});
				var cbLoadloactions 	= $('#sloc_cbLoadLoc').is(':checked') ? 1 : 0;
				var cbLogoVisible 		= $('#sloc_cbLogovi').is(':checked') ? 1 : 0;
				var cbSingleCountry 	= $('#sloc_cbSingleCountry').is(':checked') ? 1 : 0;
				formdata.append("FunType", "AppSettings");
				formdata.append("sloc_cbLoadLoc", cbLoadloactions);
				formdata.append("sloc_cbLogovi", cbLogoVisible);
				formdata.append("sloc_cbSingleCountry", cbSingleCountry);
				formdata.append('action', 'sl_dal_mapsettings');
				if (formdata) {				
					giz_Locator.util.ajxBlockUI("<?php _e( 'Saving Data', self::TEXT_DOMAIN );?>...", true, "success", sl_plugin_url);
					giz_Locator.util.ajaxFile("admin-ajax.php","POST", formdata ,ajResult, "<?php _e( 'Data has been saved successfully', self::TEXT_DOMAIN ); ?>.");					
				}
				$('#sl_frmsysSet').find('input:file').val('');
   		}
	});
	
	jQuery('#sloc_ddlSMode').change(function(){
		var currentMode = jQuery(this).val();		
		if(currentMode == "BM"){
			jQuery('.browseModeSetting').css({ 'display' : 'block' });
			singleCload();
		}else{
			jQuery('.browseModeSetting').css({ 'display' : 'none' });
			jQuery('#sloc_cbSingleCountry').removeAttr('checked');
		}
	});
	
});
function ajResult(returnVal){
		if(returnVal == '1'){			
		}
		else{
		}
}
function ajReturnVal(returnVal){
}

</script>
<style type="text/css">
.sl_tooltip{
	margin:8px;
	padding:8px;
	border:1px solid: #DECA7E;
	background-color:#F5F5B5;
	position: absolute;
	z-index: 2;
	font-size:12px;
	color:#000;
}
br.error{
	display:none !important;
}
</style>
		<div class="sl_menu_icon sloc_appset sl_icon32" ><br /></div>
		<h2 class="sl_menu_title"><?php _e( 'Application Settings', self::TEXT_DOMAIN ); ?></h2>				
		<div class="clearb">
			<form id="sl_frmsysSet" name="sl_frmsysSet" action="#" enctype="multipart/form-data" method="post"  >
								
				<div class="pad5 clearb">
					<div class="fl wd150"><label><?php _e( 'Search Mode', self::TEXT_DOMAIN ); ?></label></div>
					<div class="fl">
						<select id="sloc_ddlSMode" name="sloc_ddlSMode" class="sl_input_sel">
							<option value="SM" <?php echo ($sMode == "SM") ? 'selected="selected"' : ''; ?>><?php _e( 'Search', self::TEXT_DOMAIN ); ?></option>
							<option value="BM" <?php echo ($sMode == "BM") ? 'selected="selected"' : ''; ?>><?php _e( 'Browse', self::TEXT_DOMAIN ); ?></option>
						</select>
					</div>
					<div class="fl pad5" style="padding-left:5px;"><span class="sl_sModeHint slo_tool_tip" id="sl_sModeHint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;">
					<img src="<?php echo plugins_url( '/images/icon/hint.png', dirname(__FILE__) ); ?>" alt="" /></span></div>
				</div>
				<div class="clearb browseModeSetting" style="display:<?php echo ($sMode == "SM") ? 'none"' : 'block'; ?>">
					<div class="pad5 clearb">
						<div class="fl wd150"><label for="sloc_ddlpLocations"><?php _e( 'Preferred Location', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlpLocations" name="sloc_ddlpLocations" class="sl_input_sel">	
								<?php
									$sl_sql_str 	= "SELECT DISTINCT `country` FROM `$sl_tb_stores` ORDER BY `country` ASC";
									$sl_select_obj 	= $wpdb->get_results( $sl_sql_str );
									$sql_Norow		= $wpdb->num_rows;
									if($sql_Norow > 0){
										echo '<option value="">Select</option>';
										foreach ($sl_select_obj as $sl_country_row) {
											$selectedPerferCountry = ($preferCountry == $sl_country_row->country) ? 'selected="selected"' : "";
											echo "<option value='". $sl_country_row->country ."' ". $selectedPerferCountry .">". $sl_country_row->country ."</option>";
										}										
									}
									else{
										echo '<option value="">Select</option>';
									}
								?>
									
							</select>
						</div>
						<div class="fl pad5" style="padding-left:5px;"><span class="sl_sLocationHint slo_tool_tip" id="sl_sLocationHint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;">
						<img src="<?php echo plugins_url( '/images/icon/hint.png', dirname(__FILE__) ); ?>" alt="" /></span></div>
					</div>
					<div class="pad5 clearb">
						<div class="fl wd150"><label for="sloc_cbSingleCountry"><?php _e( 'Single Country', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl sl_singlecountry">
							<input id="sloc_cbSingleCountry" name="sloc_cbSingleCountry" type="checkbox" <?php echo $isSingleCountry; ?>/>
						</div>
						<div class="fl pad5" style="padding-left:5px;"><span class="sl_SingleCHint slo_tool_tip" id="sl_SingleCHint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;">
						<img src="<?php echo plugins_url( '/images/icon/hint.png', dirname(__FILE__) ); ?>" alt="" /></span></div>
					</div>
				</div>
				<div class="pad5 clearb">
					<div class="fl wd150">
						<label><?php _e( 'Load Locations', self::TEXT_DOMAIN ); ?></label>
					</div>
					<div class="fl sl_loadCb">
						<input id="sloc_cbLoadLoc" name="sloc_cbLoadLoc" type="checkbox"  <?php echo $LoadMark; ?> />
					</div>
					<div class="fl pad5" style="padding-left:5px;">
					<span class="sl_sLoadHint slo_tool_tip" id="sl_sLoadHint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;">
					<img src="<?php echo plugins_url( '/images/icon/hint.png', dirname(__FILE__) ); ?>" alt="" /></span></div>
				</div>				
				<div class="pad5 clearb">
					<div class="fl wd150"><label><?php _e( 'Map Position', self::TEXT_DOMAIN ); ?></label></div>
					<div class="fl">
						<select id="sloc_ddlMPos" name="sloc_ddlMPos" class="sl_input_sel">
							<option value="ML" <?php echo ($mPos == "ML") ? 'selected="selected"' : ''; ?>><?php _e( 'Left', self::TEXT_DOMAIN ); ?></option>
							<option value="MR" <?php echo ($mPos == "MR") ? 'selected="selected"' : ''; ?>><?php _e( 'Right', self::TEXT_DOMAIN ); ?></option>
						</select>
					</div>
					<div class="fl pad5">
					<span class="sl_mapHint slo_tool_tip" id="sl_mapHint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;">
					<img src="<?php echo plugins_url( '/images/icon/hint.png', dirname(__FILE__) ); ?>" alt="" /></span></div>
				</div>
				<div class="pad5 clearb">
						<div class="fl wd150"><label><?php _e( 'Language Charset', self::TEXT_DOMAIN ); ?></label></div>
						<div class="fl">
							<select id="sloc_ddlCharSet" name="sloc_ddlCharSet" class="sl_input_sel" style="width:275px">	
								<?php 
									$sql_str = "SELECT * FROM `$sl_tb_appcharset`";
									$sl_select_obj = $wpdb->get_results( $sql_str );
									foreach ($sl_select_obj as $sl_mapchar_row) {
										$sel_Char = '';
										if($CharId == $sl_mapchar_row->encode_id){  $sel_Char = ' selected="selected"';  }
										echo "<option value=".$sl_mapchar_row->encode_id." $sel_Char >".$sl_mapchar_row->encode_name."</option>";
									}
								?>
							</select>
						</div>
				</div>
				<div class="pad5 clearb">
					<div class="fl wd150">
						<label><?php _e( 'Category Visible', self::TEXT_DOMAIN ); ?></label>
					</div>
					<div class="fl sl_catCb">
						<input id="sloc_cbCategory" name="sloc_cbCategory" type="checkbox"  <?php echo $CategoryV; ?> />
					</div>
					<div class="fl pad5" style="padding-left:5px;">
					<span class="sl_sCatHint slo_tool_tip" id="sl_sCatHint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;">
					<img src="<?php echo plugins_url( '/images/icon/hint.png', dirname(__FILE__) ); ?>" alt="" /></span></div>
				</div>
				<div class="pad5 clearb">
					<div class="fl wd150">
						<label><?php _e( 'Logo Visible', self::TEXT_DOMAIN ); ?></label>
					</div>
					<div class="fl sl_logoCb">
						<input id="sloc_cbLogovi" name="sloc_cbLogovi" type="checkbox"  <?php echo $LogoVisible; ?> />
					</div>
					<div class="fl pad5" style="padding-left:5px;">
					<span class="sl_sLogoHint slo_tool_tip" id="sl_sLogoHint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;">
					<img src="<?php echo plugins_url( '/images/icon/hint.png', dirname(__FILE__) ); ?>" alt="" /></span></div>
				</div>
				<div class="pad5 clearb">
					<div class="fl wd150">
						<label><?php _e( 'Marker Size', self::TEXT_DOMAIN ); ?></label>
					</div>
					<div class="fl">
						<input id="sloc_markerwidth" name="sloc_markerwidth" type="text" maxlength="2" class="sl_wd40 inp-form number" value="<?php echo $sp_marker_width; ?>" /><span> X </span>
						<input id="sloc_markerheight" name="sloc_markerheight" type="text" maxlength="2" class="sl_wd40 inp-form number" value="<?php echo $sp_marker_height; ?>" />
					</div>
				</div>
				<div class="pad5 clearb">
					<div class="fl wd150">
						<label><?php _e( 'Store Logo Size', self::TEXT_DOMAIN ); ?></label>
					</div>
					<div class="fl">
						<input id="sloc_logowidth" name="sloc_logowidth" type="text" maxlength="3" class="sl_wd40 inp-form number" value="<?php echo $sp_logo_width; ?>" /><span> X </span>
						<input id="sloc_logoheight" name="sloc_logoheight" type="text" maxlength="3" class="sl_wd40 inp-form number" value="<?php echo $sp_logo_height; ?>" />
					</div>
				</div>
				<div class="pad5 clearb">
					<div class="fl wd150">
						<label><?php _e( 'Application CSS Style', self::TEXT_DOMAIN ); ?></label>
					</div>
					<div class="fl">
						<textarea id="sloc_ApplicationStyle" name="sloc_ApplicationStyle" rows="20" cols="50"><?php echo $applicationCss; ?></textarea>
						<br />
						<br />
						<span>#plugin_url - <?php _e( 'refers plugin url. If you want change the image url, you have to provide full image path.', self::TEXT_DOMAIN ); ?></span>
					</div>
				</div>
				<div class="pad5 clearb">
					<input id="sl_btnsysSetSubmit" class="btn btn-blue" name="sl_btnsysSetSubmit" type="submit" value="<?php _e( 'Save Changes', self::TEXT_DOMAIN ); ?>" /> </div>
			</form>
		</div>
		<div class="clear">
		</div>		
			
