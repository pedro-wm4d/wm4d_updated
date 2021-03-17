<?php

    wp_enqueue_script('sl_admin_colorbox_script', plugins_url('/js/plugin/jquery.colorbox.js', dirname(__FILE__)), '', false, true);
    wp_enqueue_script('sl_admin_drop_down_script', plugins_url('/js/plugin/drop.js', dirname(__FILE__)), '', false, true);
    wp_enqueue_script('sl_admin_iphonecheck_script', plugins_url('/js/plugin/iPhone-Checkbox.js', dirname(__FILE__)), '', false, true);
    wp_enqueue_script('sl_admin_smartpage_script', plugins_url('/js/plugin/smartpaginator.js', dirname(__FILE__)), '', false, true);
    wp_enqueue_script('sl_admin_tooltip_script', plugins_url('/js/plugin/jquery.tooltip.js', dirname(__FILE__)), '', false, true);
    wp_register_style('sl_admin_smartpager_style', plugins_url('/css/sl_admin_smartpaginator.css', dirname(__FILE__)), 'all'); wp_enqueue_style('sl_admin_smartpager_style');
?>
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
</style>
<script type="text/javascript">
var map;
var marker;
var markers = [];
var geocoder = new google.maps.Geocoder();
var SetGeo = false;
var catCount = <?php echo $sl_cate_count; ?>;
var sl_plugin_url = "<?php echo plugins_url('/', dirname(__FILE__)); ?>";
google.maps.visualRefresh = true;
var $ = jQuery.noConflict();
jQuery(document).ready(function(){

jQuery('#leftBar').css({ 'width' : jQuery('#sl_frmStore').width() + 20 });
	jQuery('#rightContent').css({ 'width' : jQuery('#wpbody').width() - jQuery('#sl_frmStore').width() - 48 });
	jQuery(window).resize(function(){
		jQuery('#leftBar').css({ 'width' : jQuery('#sl_frmStore').width() });
		if(jQuery('#wpbody').width() <= 846){
			jQuery('#rightContent').css({ 'width' : jQuery('#wpbody').width() - jQuery('#sl_frmStore').width() - 41 });
		}else{
			jQuery('#rightContent').css({ 'width' : jQuery('#wpbody').width() - jQuery('#sl_frmStore').width() - 48 });
		}
	});

$('a.sl_info-tooltip ').tooltip({
		track: true,
		delay: 0,
		fixPNG: true, 
		showURL: false,
		showBody: " - ",
		top: -35,
		left: 5,
		zindex: 10000
	});
	if(catCount > 0){
		$('#sl_pagination_green').smartpaginator({ totalrecords: <?php echo $sl_cate_count; ?>, recordsperpage: 5, datacontainer: 'sloc_TbCategorty', dataelement: 'tr', initval: 0, next: '<?php _e('Next', self::TEXT_DOMAIN); ?>', prev: '<?php _e('Prev', self::TEXT_DOMAIN); ?>', first: '<?php _e('First', self::TEXT_DOMAIN); ?>', last: '<?php _e('Last', self::TEXT_DOMAIN); ?>', theme: 'green' });
	}
	var changeTooltipPosition = function(event) {
	  var tooltipX = event.pageX - 8;
	  var tooltipY = event.pageY + 8;
	  $('div.sl_tooltip').css({top: tooltipY, left: tooltipX});
	};
 
	var showTooltip = function(event) {
	  $('div.sl_tooltip').remove();
	  $('<div class="sl_tooltip"><b><?php _e('Auto', self::TEXT_DOMAIN); ?>:</b><?php _e('Marker plots automatically based on entered Address', self::TEXT_DOMAIN); ?>. <br /><b><?php _e('Manual', self::TEXT_DOMAIN); ?>:</b> <?php _e('You can drag the marker to Plot the location manually', self::TEXT_DOMAIN); ?>.</div>')
            .appendTo('body');
	  changeTooltipPosition(event);
	};
 
	var hideTooltip = function() {
	   $('div.sl_tooltip').remove();
	};
 
	$("span.sl_hint").bind({
	   mousemove : changeTooltipPosition,
	   mouseenter : showTooltip,
	   mouseleave: hideTooltip
	});
	$('.iPhone :checkbox').iphoneStyle({
			resizeHandle: true,
			resizeContainer: true,
			checkedLabel: 'MANUAL',
			uncheckedLabel: 'AUTO',
			labelOffClass: 'iPhoneCheckLabelOnR',
			onChange: function(elem, value) {
				if(value){
					SetGeo	= value;
					addNewMarker(jQuery('#sloc_hdfLogo').val(), value);
				}
				else {
					SetGeo	= value;
					addNewMarker(jQuery('#sloc_hdfLogo').val(), value);
				}
			}
	});
jQuery('.newStore').addClass('current');
    setTimeout(function(){LoadWOQ();},1000);
if($('#sloc_storeLogo option').size() > 0){
	$('#sloc_storeLogo').ddslick({
     	    elementId : 'sloc_LogoId',
			height: 180,
	    onSelected: function(dData){
		jQuery('#sloc_hdfLogoAdd').val(dData.selectedData.value);
		jQuery('#sloc_hdfLogoType').val(dData.selectedData.description);
	    }   
	});
}
if($('#sloc_ddlMarker option').size() > 0){
	$('#sloc_ddlMarker').ddslick({
     	    elementId : 'sloc_MarkerId',
			height: 120,
			width: 100,
	    onSelected: function(dData){
	    }   
	});
}
if($('#sloc_ddlLabel option').size() > 0){
	$('#sloc_ddlLabel').ddslick({
     	    elementId : 'sloc_LabelId',
			height: 160,
	    onSelected: function(dData){
			if(parseInt(dData.selectedData.value) > 1){
				jQuery('.LabelTxt').show();
				jQuery('#sloc_hdfLabelId').val(dData.selectedData.value);
			}else{	
				jQuery('.LabelTxt').hide();
				jQuery('#sloc_hdfLabelId').val(dData.selectedData.value);
				jQuery('#tbLabelTxt').val('');
			}
	    }   
	});
}
if($('#sloc_ddlCategory option').size() > 0){
	$('#sloc_ddlCategory').ddslick({
	    elementId : 'sloc_CatId',
		height: 160,
	    onSelected: function(dData){
		jQuery('#sloc_hdfLogo').val(dData.selectedData.imageSrc);
			if($('#sloc_cbAuto').is(':checked')){
				addNewMarker(dData.selectedData.imageSrc, true);
			}else{
				addNewMarker(dData.selectedData.imageSrc, false);
			}
	    }   
	});
}
    jQuery('#rightContent').css({'height' : jQuery('#sl_frmStore').height()});
	jQuery("#sl_frmStore").validate({ 
		rules :{
			'sloc_tbFax' : {
				Phone: true
			}
		}, 
		messages : {
			'sloc_tbFax' : {
				Phone: "<?php _e('Please enter valid fax number', self::TEXT_DOMAIN); ?>"
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
					formdata.append(field.name,field.value);
				  });
				formdata.append("sloc_CatId", jQuery('#sloc_CatId').val());
                formdata.append("sloc_storeLogo", jQuery('.dd-selected-value').val());
                formdata.append('action', 'sl_dal_storesave');
				if (formdata) {				
					giz_Locator.util.ajxBlockUI("<?php _e('Saving Data', self::TEXT_DOMAIN); ?>...", true, "success", sl_plugin_url);
					giz_Locator.util.ajaxFile("admin-ajax.php","POST", formdata ,function(resVal){
							var strResult = resVal.split("-");
							giz_Locator.util.ajxBlockUIAuto(jQuery.trim(strResult[0]), false, jQuery.trim(strResult[1]), 2500, sl_plugin_url);							
					}, "<?php _e('Data saved successfully', self::TEXT_DOMAIN); ?>.", false);
				}
				if(jQuery('#sloc_btnSubmit').val() == 'Save'){
					giz_Locator.util.clearform('#sl_frmStore');
				}
			
   		}
	});
	
	jQuery("#sloc_frmCategory").validate({
   		submitHandler: function(form) {
		jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
		jQuery('#sloc_msgBox').html('');
			var MarkerId = jQuery('#sloc_MarkerId').val();
			if(MarkerId != undefined){
				if(MarkerId.length > 0){
					jQuery('#sloc_btnCatSave').attr('disabled', 'disabled');
					var defaultC = (jQuery('#sloc_cbCatDefault').is(":checked")) ? "1" : "0";
					var sl_category_dal = { action: 'sl_dal_category',
						FunMethod: 'CategorySave',
						sloc_MarkerId: MarkerId,
						sloc_CategoryId: jQuery('#sloc_hdfCatId').val(),
						sloc_CategoryName: jQuery('#sloc_tbCatName').val(),
						sloc_DefCat: defaultC,
						sloc_CnfSv: 'No' }
					giz_Locator.util.ajax("admin-ajax.php","POST", sl_category_dal, SaveResult, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);	
				}
			}else{
				jQuery('#sloc_msgBox').removeClass('sl_success').addClass('sl_error');
				jQuery('#sloc_msgBox').html('<?php _e('Select Marker', self::TEXT_DOMAIN); ?>.');
				jQuery('#sloc_msgBox').show();
			}
   		}
	});
	
   $("#sloc_addCat").colorbox({ 
		width: "600px", 
		inline: true, 
		href: "#sloc_frmAddNew", 
		overlayClose: false, 
		title: false, 
		escKey: false, 
		opacity: "0.75", 
	    onLoad: function(){
			jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
			jQuery('#sloc_msgBox').html('');
			jQuery('#sloc_hdfCatId').val('0');
		},
		onClosed: function(){
			var sl_dal_data= { action: 'sl_dal_category',FunMethod: 'Select' };
			giz_Locator.util.ajaxSer("admin-ajax.php", "POST", sl_dal_data, ajReturnVal);
		}
	});
   $('.mapImg').on('click',function(){
		jQuery('#filePath').val('');
		$('.mapImg').removeClass('active');
		$(this).addClass('active');
		jQuery('#filePath').val($(this).find('img').attr('rel'));
   });

   $("#sloc_addLogo").colorbox({ width: "400px", inline: true, href: "#sloc_frmAddLogo",title: false, overlayClose: false, escKey: false, opacity: "0.75" });
   $('#btnreset').on('click', function(){
		getPointReset();
   });
	/**************** Adding New Logo ************/
	jQuery("#sloc_frmLogo").validate({
		rules:{
			'sloc_fuNewLogo' : {
				required: true,
				accept : 'jpg|png|gif'
			}
		},
		messages:{
			'sloc_fuNewLogo' : {
				accept : '<?php _e('Allowed only image file', self::TEXT_DOMAIN); ?>'
			}
		},		
   		submitHandler: function(form) {
			 formdata = false;
				 if (window.FormData) {
					 formdata = new FormData();
				 }
				 file =  document.getElementById('sloc_fuNewLogo').files;
				 if(file.length>0){
					 file = file[0];				
					 if (!!file.type.match(/image.*/)) {
						 if ( window.FileReader ) {
							 reader = new FileReader();
						 }
						 if (formdata) {
							 formdata.append("sloc_fuNewLogo", file);
						 }
					 }
				 }
				formdata.append("FunType", "LogoSave");
				formdata.append("action", "sl_dal_mapsettings");
				var cbDefault 	= $('#sl_cbDefault').is(':checked') ? "1" : "0";
				formdata.append("sloc_DefaultLogo", cbDefault);
				  if (formdata) {
					closeOverlay();
					giz_Locator.util.ajxBlockUI("<?php _e('Saving Data', self::TEXT_DOMAIN); ?>...", true, "success", sl_plugin_url);
					giz_Locator.util.ajaxFile("admin-ajax.php","POST", formdata ,function(resVal){
							var strResult = resVal.split("-");
							giz_Locator.util.ajxBlockUIAuto(jQuery.trim(strResult[0]), false, jQuery.trim(strResult[1]), 2500, sl_plugin_url);
							var sl_mapsetting_dal = { action: 'sl_dal_mapsettings', FunType: 'LogoSelect' }
							giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_mapsetting_dal, ajLogoVal);
							giz_Locator.util.clearform('#sloc_frmLogo');								
					}, "<?php _e('Data saved successfully', self::TEXT_DOMAIN); ?>.", false);									
				}
   		}
	});
	/************** Remove Radius *******************/
	jQuery('#sloc_removeLogo').on('click', function(){
		var logoName = jQuery('#sloc_LogoId').val();	
		if(logoName.length > 0){
			var sl_mapsetting_dal = { action: 'sl_dal_mapsettings', FunType: 'LogoCheck', sloc_logoId: logoName }
			giz_Locator.util.ajxBlockUI("<?php _e('Deleting Data', self::TEXT_DOMAIN); ?>...", true, "success", sl_plugin_url);
			giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_mapsetting_dal, ajChecking, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....");				
		}
		else{
			alert("<?php _e('Please Select Logo to Delete', self::TEXT_DOMAIN); ?>");
		}
	});

	/************** Remove Category *******************/
	jQuery('#removeCat').on('click', function(){
		var CatId = jQuery('#CatId').val();
		if(CatId != undefined){
			if(CatId.length > 0){
				if(CatId == 1){
					alert("<?php _e("You can't delete default Category", self::TEXT_DOMAIN); ?>");
				}
				else{
					giz_Locator.util.ajaxSer("ajax/ajx_MapSettings.php","POST", "FunType=CatCheck&CatId="+ CatId, ajCatChecking, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....");	
				}
			}
		}
		else{
			alert("<?php _e('Please Select Category to Delete', self::TEXT_DOMAIN); ?>");
		}
	});
	
	/************** Remove Marker *******************/
	jQuery('#sloc_removeMark').on('click', function(){
		var MarkerId = jQuery('#sloc_MarkerId').val();
		if(MarkerId != undefined){
			if(MarkerId.length > 0){
				var sl_category_dal = { action: 'sl_dal_category',
										FunMethod: 'MarkerRemove',
										sloc_ConfDel: 'No',
										sloc_MarkerId: MarkerId }
					giz_Locator.util.ajax("admin-ajax.php","POST", sl_category_dal, DeleteResult, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);	
			}
		}
		else{
			alert("Please select marker to delete");
		}
	});
	
	/************** Set As Default Marker *******************/
	jQuery('#sloc_defaultMark').on('click', function(){
		var MarkerId = jQuery('#sloc_MarkerId').val();
		if(MarkerId != undefined){
			if(MarkerId.length > 0){
					var sl_dal_data= { action: 'sl_dal_category', FunMethod: 'DefaultMarker', sloc_ConfDef: 'No', sloc_MarkerId: MarkerId }
					giz_Locator.util.ajax("admin-ajax.php","POST", sl_dal_data, DefaultRes, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);	
			}
		}
		else{
			alert("<?php _e('Please select marker to set default', self::TEXT_DOMAIN); ?>");
		}
	
	});
	
	/************** Edit Category *******************/
	jQuery('.sl_catEdit').on('click', function(){
		jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
		jQuery('#sloc_msgBox').html('');
		var catId = jQuery(this).attr('rel');
		jQuery('#sloc_hdfCatId').val(catId);
		jQuery('#sloc_tbCatName').val(jQuery(this).attr('catName'));
		if (jQuery(this).attr('defat') == "1") 
			jQuery('#sloc_cbCatDefault').attr('checked', true);
		else		
			jQuery('#sloc_cbCatDefault').attr('checked', false);
		$('#sloc_ddlMarker').ddslick('destroy');
		$('#sloc_ddlMarker').val(jQuery(this).attr('markId'));
		if($('#sloc_ddlMarker option').size() > 0){
			$('#sloc_ddlMarker').ddslick({
					elementId : 'sloc_MarkerId',
					height: 120,
					width: 100,
					onSelected: function(dData){
				}   
			});
		}
	});
	
	/************** Delete Category *******************/
	jQuery('.sl_catDel').on('click', function(){
		jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
		jQuery('#sloc_msgBox').html('');
		var catId = jQuery(this).attr('rel');
		jQuery('#sloc_hdfCatId').val(catId);
		var isDefault = jQuery(this).attr('isDef');
		if(isDefault == "sl_default"){
			alert("<?php _e("You can't delete default category. Please set new default category to delete this", self::TEXT_DOMAIN); ?>");
		}else{
			var sl_category_dal = { action: 'sl_dal_category', FunMethod: 'CategoryDelete', sloc_ConfDel: 'No',sloc_CategoryId: catId }
			giz_Locator.util.ajax("admin-ajax.php","POST", sl_category_dal, CategorySelect, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);
		}
	});
	
	/************** Delete Category *******************/
	jQuery('.sl_catDef').on('click', function(){
		jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
		jQuery('#sloc_msgBox').html('');
		var catId = jQuery(this).attr('rel');
		jQuery('#sloc_hdfCatId').val(catId);
		var isDefault = jQuery(this).attr('isDef');
		if(jQuery(this).hasClass("sl_default")){
			alert("<?php _e('This is default category', self::TEXT_DOMAIN); ?>");
			return false;
		}else{
			var sl_category_dal = { action: 'sl_dal_category', FunMethod: 'SetDefaultCat', sloc_CategoryId : catId }
			giz_Locator.util.ajax("admin-ajax.php","POST", sl_category_dal, Setdefault, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);
		}
	});
});
function Setdefault(resVal){
	jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
	jQuery('#sloc_msgBox').html('');
	if(jQuery.trim(resVal) == "1"){
		jQuery('#sloc_msgBox').removeClass('sl_error').addClass('sl_success');
		jQuery('#sloc_msgBox').html('<?php _e('New default category assigned successfully', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
		var sl_category_dal = { action: 'sl_dal_category', FunMethod: "CategorySelect" }
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_category_dal, BindCategory,'<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
	}else{
		jQuery('#sloc_msgBox').removeClass('sl_succes').addClass('sl_error');
		jQuery('#sloc_msgBox').html('<?php _e('Action failed. Please try again', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
	}
}

function CategorySelect(resVal){
	jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
	jQuery('#sloc_msgBox').html('');
	if(jQuery.trim(resVal) == "SCD"){
		jQuery('#sloc_msgBox').removeClass('sl_error').addClass('sl_success');
		jQuery('#sloc_msgBox').html('<?php _e('Category deleted successfully', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
		var sl_category_dal = { action: 'sl_dal_category', FunMethod: "CategorySelect" }
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_category_dal, BindCategory,'Loading..');
		jQuery('#sloc_hdfCatId').val("0");
	}else if(jQuery.trim(resVal) == "STN"){
		jQuery('#sloc_msgBox').removeClass('sl_succes').addClass('sl_error');
		jQuery('#sloc_msgBox').html('<?php _e('Action failed. Please try again', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
	}else{
		var DelVal = resVal.split('-');
		var count_val = parseInt(DelVal[1]);
			var sing_plu = (count_val > 1) ? '<?php _e('Stores', self::TEXT_DOMAIN); ?>' : '<?php _e('Store', self::TEXT_DOMAIN); ?>';
		var r = confirm(count_val +" "+sing_plu + " <?php _e('using this category. If you want to delete press OK(default category will be apply to those already have this category)', self::TEXT_DOMAIN); ?>.");
		if (r==true){
		  var catId = jQuery('#sloc_hdfCatId').val();
		  var sl_category_dal = { action: 'sl_dal_category', FunMethod: 'CategoryDelete', sloc_ConfDel: 'Yes',sloc_CategoryId: catId }
		  giz_Locator.util.ajax("admin-ajax.php","POST", sl_category_dal, CategorySelect, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);
		}
		else{
		}
	}
}

function SaveResult(resVal){
	jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
	jQuery('#sloc_msgBox').html('');
	if(jQuery.trim(resVal) == "1"){
		jQuery('#sloc_msgBox').removeClass('sl_error').addClass('sl_success');
		jQuery('#sloc_msgBox').html('<?php _e('Category saved successfully', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
		jQuery('#sloc_tbCatName').val('');
		jQuery('#sloc_hdfCatId').val('0');
		jQuery('#sloc_cbCatDefault').attr('checked', false);
		var sl_category_dal = { action: 'sl_dal_category', FunMethod: "CategorySelect" }
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_category_dal, BindCategory,'<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
	}else if(jQuery.trim(resVal) == "0"){
		jQuery('#sloc_msgBox').removeClass('sl_success').addClass('sl_error');
		jQuery('#sloc_msgBox').html('<?php _e('Failed to save the data', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
	}else if(jQuery.trim(resVal) == "DF"){
		jQuery('#sloc_msgBox').removeClass('sl_success').addClass('sl_error');
		jQuery('#sloc_hdfCatId').val('0');
		jQuery('#sloc_msgBox').html("<?php _e('This is default category. Please set new default image before removing', self::TEXT_DOMAIN); ?>.");
		jQuery('#sloc_msgBox').show();
	}else if(jQuery.trim(resVal) == "EX"){
			var r = confirm("<?php _e('The Category name already exists. If you want to add press OK.', self::TEXT_DOMAIN); ?>");
			if (r==true){
				var MarkerId = jQuery('#sloc_MarkerId').val();
				if(MarkerId != undefined){
					if(MarkerId.length > 0){
						var defaultC = (jQuery('#sloc_cbCatDefault').is(":checked")) ? "1" : "0";
						var sl_category_dal = { action: 'sl_dal_category',
						FunMethod: 'CategorySave',
						sloc_MarkerId: MarkerId,
						sloc_CategoryId: jQuery('#sloc_hdfCatId').val(),
						sloc_CategoryName: jQuery('#sloc_tbCatName').val(),
						sloc_DefCat: defaultC,
						sloc_CnfSv: 'Yes' }
						giz_Locator.util.ajax("admin-ajax.php","POST", sl_category_dal, SaveResult, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);
					}
				}
			}
			else{
			}
	}
	jQuery('#sloc_btnCatSave').removeAttr('disabled');
}

function BindCategory(resVal){
	var content_htm = "";
	if(resVal != null){
		jQuery('#sloc_TbCategorty').empty();
			content_htm = '<tr><th class="line-left minwidth-1" style="text-align:center;"><span><?php _e('Category Name', self::TEXT_DOMAIN); ?></span></th>'+
					'<th class="line-left" style="text-align:center;"><span><?php _e('Default', self::TEXT_DOMAIN); ?></span></th>'+
					'<th class="line-left" style="text-align:center;"><span><?php _e('Options', self::TEXT_DOMAIN); ?></span></th>'+
				'</tr>';
		for (var i = 0; i < resVal.length; i++){
			var isDefault  = (resVal[i]["isdefault"] == "1") ? "Yes" : "No";
			var defCss		= (resVal[i]["isdefault"] == "1") ? "sl_default" : "";
			content_htm += "<tr>"+
					"<td>"+ resVal[i]["category"] +"</td>"+
					"<td>"+ isDefault +"</td>"+
					"<td class='options-width' valign='middle' style='text-align:center;width: 100px;'>"+
						"<a href='javascript:void(0);' title='<?php _e('Edit', self::TEXT_DOMAIN); ?>' rel='"+ resVal[i]["categoryid"] +"' catName=\""+ resVal[i]["category"] +"\" markId='"+ resVal[i]["markerid"] +"' defat='"+ resVal[i]["isdefault"] +"' class='sl_icon-5 sl_catEdit sl_info-tooltip'></a>"+
						"<a href='javascript:void(0);' title='<?php _e('Delete', self::TEXT_DOMAIN); ?>' isDef='"+ defCss +"' rel='"+ resVal[i]["categoryid"] +"'  class='sl_icon-2 sl_catDel sl_info-tooltip'></a>"+
						"<a href='javascript:void(0);' title='<?php _e('Set As Default', self::TEXT_DOMAIN); ?>' rel='"+ resVal[i]["categoryid"] +"' class='sl_icon-3 sl_catDef "+ defCss +" sl_info-tooltip'></a>"+
					"</td>"+
					"</tr>";
		}
		jQuery('#sloc_TbCategorty').append(content_htm);
	}
	if(resVal.length > 0){
		$('#sl_pagination_green').smartpaginator({ totalrecords: resVal.length, recordsperpage: 5, datacontainer: 'sloc_TbCategorty', dataelement: 'tr', initval: 0, next: '<?php _e('Next', self::TEXT_DOMAIN); ?>', prev: '<?php _e('Prev', self::TEXT_DOMAIN); ?>', first: '<?php _e('First', self::TEXT_DOMAIN); ?>', last: '<?php _e('Last', self::TEXT_DOMAIN); ?>', theme: 'green' });
		jQuery('#sloc_frmAddNew').colorbox.resize();
	}
	else{
		$('#sloc_TbCategorty').empty();
		content_htm +="<tr><td colspan='3'><p style='text-align:center'><strong><?php _e('There are no Categories', self::TEXT_DOMAIN); ?>..</strong></p></td></tr>";
		$('#sloc_TbCategorty').append(content_htm);
		jQuery('#sloc_frmAddNew').colorbox.resize();
	}
}

function DeleteResult(resVal){
	jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
	jQuery('#sloc_msgBox').html('');
	if(jQuery.trim(resVal) == "DF"){
		alert("<?php _e("You can't delete default marker(Please set new default marker before delete)", self::TEXT_DOMAIN); ?>");
	}else if(jQuery.trim(resVal) == "DL"){
		jQuery('#sloc_msgBox').removeClass('sl_error').addClass('sl_success');
		jQuery('#sloc_msgBox').html('<?php _e('Marker image deleted successfully', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
		var sl_dal_data= { action: 'sl_dal_category',FunMethod: 'MarkSelect' };
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_dal_data, bindMarker,'<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
	}
	else{
		var DelVal = resVal.split('-');
		var count_val = parseInt(DelVal[1]);
			var sing_plu = (count_val > 1) ? '<?php _e('Categories', self::TEXT_DOMAIN); ?>' : '<?php _e('Category', self::TEXT_DOMAIN); ?>';
		var r = confirm(count_val +" "+sing_plu + " <?php _e('using this marker. If you want to delete press OK(default marker will be apply to those already have this marker)', self::TEXT_DOMAIN); ?>.");
		if (r==true){
		  var MarkerId = jQuery('#sloc_MarkerId').val();
		  var sl_dal_data= { action: 'sl_dal_category', FunMethod: 'MarkerRemove', sloc_ConfDel: 'Yes', sloc_MarkerId :MarkerId  };
		  giz_Locator.util.ajax("admin-ajax.php","POST", sl_dal_data, DeleteResult, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);
		}
		else{
		}
	}
}

function DefaultRes(resVal){
	jQuery('#sloc_msgBox').removeClass('sl_error').removeClass('sl_success');
	jQuery('#sloc_msgBox').html('');
	if(jQuery.trim(resVal) == "DF"){
		jQuery('#sloc_msgBox').removeClass('sl_error').addClass('sl_success');
		jQuery('#sloc_msgBox').html('<?php _e('This is the default marker. Please try again', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
	}else if(jQuery.trim(resVal) == "DFN"){
		var r = confirm("<?php _e('Default marker will be affect to the category. If you want press OK', self::TEXT_DOMAIN); ?>");
		if (r==true){
			var MarkerId = jQuery('#sloc_MarkerId').val();
			var sl_dal_data= { action: 'sl_dal_category', FunMethod: 'DefaultMarker', sloc_ConfDef: 'Yes', sloc_MarkerId: MarkerId }
			giz_Locator.util.ajax("admin-ajax.php","POST", sl_dal_data, DefaultRes, "<?php _e('Loading', self::TEXT_DOMAIN); ?>....", false);
		}else{
		}
	}else if(jQuery.trim(resVal) == "DFS"){
		var sl_dal_data= { action: 'sl_dal_category',FunMethod: 'MarkSelect' };
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_dal_data, bindMarker,'<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
		jQuery('#sloc_msgBox').removeClass('sl_error').addClass('sl_success');
		jQuery('#sloc_msgBox').html('<?php _e('Default marker set successfully', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
	}
	else if(jQuery.trim(resVal) == "NDF"){
		var sl_dal_data= { action: 'sl_dal_category',FunMethod: 'MarkSelect' };
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_dal_data, bindMarker,'<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
		jQuery('#sloc_msgBox').removeClass('sl_succes').addClass('sl_error');
		jQuery('#sloc_msgBox').html('<?php _e('Action failed. Please try again', self::TEXT_DOMAIN); ?>.');
		jQuery('#sloc_msgBox').show();
	}
}

function closeOverlay() {
  $.colorbox.close();
}
function ajChecking(returnVal){
	if(returnVal != null){
		if(returnVal["msg"] == 'D'){
			alert("<?php _e("You can't delete Default logo. Please upload new Default logo before delete", self::TEXT_DOMAIN); ?>");
			$.unblockUI();
		}
		else if(returnVal["msg"] == 'SC'){
			var stores = (returnVal["resultCount"] >1) ? ' <?php _e('Stores are', self::TEXT_DOMAIN); ?> ' : ' <?php _e('Store is', self::TEXT_DOMAIN); ?> ';
			var yesNo = window.confirm(returnVal["resultCount"] + stores +"<?php _e('using this Logo. If you want to delete this please Press OK', self::TEXT_DOMAIN); ?>." )
			if (yesNo == true) {				
					giz_Locator.util.ajxBlockUI("<?php _e('Deleting Data', self::TEXT_DOMAIN); ?>...", true, "success", sl_plugin_url);
					var sl_mapsetting_dal = { action: 'sl_dal_mapsettings',
											  FunType: 'LogoCheckDel',
											  sloc_logoId : jQuery('#sloc_LogoId').val()
											}
					giz_Locator.util.ajax("admin-ajax.php","POST", sl_mapsetting_dal, ajLogoResult, "<?php _e('Data has been deleted successfully', self::TEXT_DOMAIN); ?>.");
			}
			else{
				return false;
			}
		}
		else if(returnVal["msg"] == 'SD'){
				$.unblockUI();				
				full_html = "<h3 class='sloc_ajax_message_success'><span class='sloc_ajx_span' style='top:0px;'><?php _e('Data has been deleted successfully', self::TEXT_DOMAIN); ?></span></h3>";
				$.blockUI({ 
							message: full_html,
							css: { 
									border: 'none', 
									padding: '6px', 
									backgroundColor: '#fff',
									border:	'3px solid #aaa',					
									opacity: 1, 
									color: '#fff' 
							},
							timeout : 2500
				});
				setTimeout(function(){
					var sl_mapsetting_dal = { action: 'sl_dal_mapsettings', FunType: 'LogoSelect' }
					giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_mapsetting_dal, ajLogoVal);										
				}, 500);					
		}
		else if(returnVal["msg"] == 'FA'){			
			full_html = "<h3 class='sloc_ajax_message_error'><span class='sloc_ajx_span' style='top:0px;'><?php _e('Data has not been deleted', self::TEXT_DOMAIN); ?></span></h3>";
			$.blockUI({ 
					message: full_html,
					css: { 
							border: 'none', 
							padding: '6px', 
							backgroundColor: '#fff',
							border:	'3px solid #aaa',					
							opacity: 1, 
							color: '#fff' 
					},
					timeout : 2500
			});
		}
	}
}
function ajCatChecking(returnVal){
	if(returnVal != null){
		if(returnVal["msg"] == 'SC'){
			var stores = (returnVal["resultCount"] >1) ? ' <?php _e('Stores are', self::TEXT_DOMAIN); ?> ' : ' <?php _e('Store is', self::TEXT_DOMAIN); ?> ';
			var yesNo = window.confirm(returnVal["resultCount"] + stores +"<?php _e('using this Category. If you want to delete this please Press OK(Default category will be apply)', self::TEXT_DOMAIN); ?>." )
			if (yesNo == true) {				
				full_html = "<h3 class='sloc_ajax_message_success'><span class='sloc_ajx_span' style='top:0px;'><?php _e('Deleting Data', self::TEXT_DOMAIN); ?>...</span></h3>";
				$.blockUI({ 
							message: full_html,
							css: { 
									border: 'none', 
									padding: '6px', 
									backgroundColor: '#fff',
									border:	'3px solid #aaa',					
									opacity: 1, 
									color: '#fff' 
							},
							timeout : 2500
				});
				giz_Locator.util.ajax("ajax/ajx_MapSettings.php","POST", "FunType=CatCheckDel&CatId="+ jQuery('#CatId').val(), ajResult, "<?php _e('Data has been deleted successfully', self::TEXT_DOMAIN); ?>.");
			}
			else{
				return false;
			}
		}
		else if(returnVal["msg"] == 'SD'){	
			full_html = "<h3 class='sloc_ajax_message_success'><span class='sloc_ajx_span' style='top:0px;'><?php _e('Data has been deleted successfully', self::TEXT_DOMAIN); ?></span></h3>";
			$.blockUI({ 
						message: full_html,
						css: { 
								border: 'none', 
								padding: '6px', 
								backgroundColor: '#fff',
								border:	'3px solid #aaa',					
								opacity: 1, 
								color: '#fff' 
						},
						timeout : 2500
			});					
			setTimeout(function(){	
			var sl_dal_data= { action: 'sl_dal_category',FunMethod: 'Select' };
			giz_Locator.util.ajaxSer("admin-ajax.php", "POST", sl_dal_data, ajReturnVal);					
				$.unblockUI();
			}, 500);					
		}
		else if(returnVal["msg"] == 'FA'){			
			full_html = "<h3 class='sloc_ajax_message_error'><span class='sloc_ajx_span' style='top:0px;'><?php _e('Data has not been deleted', self::TEXT_DOMAIN); ?></span></h3>";
			$.blockUI({ 
					message: full_html,
					css: { 
							border: 'none', 
							padding: '6px', 
							backgroundColor: '#fff',
							border:	'3px solid #aaa',					
							opacity: 1, 
							color: '#fff' 
					},
					timeout : 2500
			});	
		}
	}
}

function LoadWOQ(){
    var Lat = 47.608941;
    var Lan = -122.340145;
	var sl_mapsetting_dal = { action: 'sl_dal_mapsettings', FunType: 'LoadLocation' }
	giz_Locator.util.ajaxSer("admin-ajax.php", "POST", sl_mapsetting_dal, function(markers){
		if(markers != null){
			if( markers.length > 0 ){
				for (var i = 0; i < markers.length; i++) {
				   Lat = parseFloat(markers[i]["lat"]),
				   Lan = parseFloat(markers[i]["lng"]);
				}
				  var LatN=0; var LngN=0;
				  <?php if (isset($_REQUEST['storeId'])) { ?>
					 LatN = "<?php echo $Lat; ?>";
					 LngN = "<?php echo $Lng; ?>";
				 <?php } ?>
				 if((LatN.length > 0) && (LngN.length > 0)){
					initialize_map(LatN, LngN);
					jQuery('#sloc_hdfLatt, #sloc_hdfLattG').val(LatN);
					jQuery('#sloc_hdfLngg, #sloc_hdfLnggG').val(LngN);
				}
				else{
					initialize_map(Lat, Lan);
					jQuery('#sloc_hdfLatt, #sloc_hdfLattG').val(Lat);
					jQuery('#sloc_hdfLngg, #sloc_hdfLnggG').val(Lan);
				}
			}
		}else{
				initialize_map(Lat, Lan);
				jQuery('#sloc_hdfLatt, #sloc_hdfLattG').val(Lat);
				jQuery('#sloc_hdfLngg, #sloc_hdfLnggG').val(Lan);
		}
	});   
	initialize_map(Lat, Lan);
}
/*** Load Or Bind The From DB using Ajax XML Response ***/
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
function initialize_map(Lat, Lan) {
  var mapOptions ={
        center: new google.maps.LatLng(Lat, Lan),
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.ROADMAP, 
        zoomControl: true,
		zoomControlOptions: {
  		style: google.maps.ZoomControlStyle.LARGE,
  		position: google.maps.ControlPosition.RIGHT_TOP
		},
		panControl: true,
  		panControlOptions: {
  		position: google.maps.ControlPosition.RIGHT_TOP
		},		
		streetViewControl: true
      }
     map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	 marker = new google.maps.Marker({
			map:map,
			draggable:false,
			maxZoom: 15,
			animation: google.maps.Animation.DROP,
			position: new google.maps.LatLng(Lat , Lan)
		});
	markers.push(marker);
}
function toggleBounce() {
  if (marker.getAnimation() != null) {
    marker.SsetAnimation(null);
  } else {
    marker.setAnimation(google.maps.Animation.BOUNCE);
  }
}
function markerPoint(){
	jQuery('#sloc_tbLat, #sloc_hdfLatt').val(marker.position.lat().toFixed(6)); jQuery('#sloc_tbLng, #sloc_hdfLngg').val(marker.position.lng().toFixed(6));
}
function getPoint(){
	var address  = '';
	var street	 = $.trim(jQuery('#sloc_tbAddress').val());
	var city 	 = $.trim(jQuery('#sloc_tbCity').val());
	var state 	 = $.trim(jQuery('#sloc_tbState').val());
	var country  = $.trim(jQuery('#sloc_tbCountry').val());
	if(!$('#sloc_cbAuto').is(':checked')){
		if(street.length > 0 && city.length > 0 && country.length > 0){
			address = street +', '+ city + ', ' + country;
			if(state.length > 0){
				address = street +', '+ city +', '+ state +', '+ country;
			}
			geocoder = new google.maps.Geocoder();
			geocoder.geocode( { 'address': address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						var latitude = results[0].geometry.location.lat();
						var longitude = results[0].geometry.location.lng();
						drop(latitude, longitude);
					}
					else if(status == google.maps.GeocoderStatus.ZERO_RESULTS){  
						alert("<?php _e('Address not found. Please set geocode mode to Manual and plot on map manually by dragging the marker', self::TEXT_DOMAIN); ?>.");
					}
					else if(status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT){
						alert("<?php _e('Google API usage limit exceeded', self::TEXT_DOMAIN); ?>.");
					}
					else if(status == google.maps.GeocoderStatus.REQUEST_DENIED){ 
						alert("<?php _e('Request Denied', self::TEXT_DOMAIN); ?>.");
					}
					else if(status == google.maps.GeocoderStatus.INVALID_REQUEST){ 
						$('#sloc_msgBox').text("<?php _e('Google API error. Please try again', self::TEXT_DOMAIN); ?>.");
						$('#sloc_msgBox').removeClass('sl_success').addClass('sl_error').fadeOut(300).fadeIn(300);
					}
			});
		}
	}
}

function getPointReset(){
	var latitude = "0.0";
	var longitude = "0.0";
	if(!jQuery('#sloc_tbLat').val() && !jQuery('#sloc_tbLng').val()){
			latitude = parseFloat(jQuery('#sloc_tbLat').val());
			longitude = parseFloat(jQuery('#sloc_tbLng').val());
	}
	else{
		latitude = parseFloat(jQuery('#sloc_hdfLattG').val());
		longitude = parseFloat(jQuery('#sloc_hdfLnggG').val());
	}
	drop(latitude, longitude);
}
function drop(Lat , Lan) {
  setTimeout(function() {addMarker(Lat , Lan);}, 200);
}
function addNewMarker(logoUrl, isTrue) {
setTimeout(function() {
deleteOverlays();
	var Lati = parseFloat(jQuery('#sloc_hdfLatt').val());
	var Lngg = parseFloat(jQuery('#sloc_hdfLngg').val());
	var iconpath = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|FE7569";
	if(logoUrl.length > 0){
		iconpath = logoUrl;
	}
		var markImage = new google.maps.MarkerImage(iconpath);
		if(isTrue){
		    marker = new google.maps.Marker({
				draggable:true,
				icon: markImage,
				borderPadding: 50, 
				maxZoom: 15,
				animation: google.maps.Animation.DROP,				
				title : '<?php _e('Drag to Locate Position', self::TEXT_DOMAIN); ?>',
		      		position: new google.maps.LatLng(Lati , Lngg),
		      		map: map
		    });
			markers.push(marker);
			if(SetGeo){
				var infowindow = new google.maps.InfoWindow();
				infowindow.setContent('<?php _e('Drag to Locate Position', self::TEXT_DOMAIN); ?>');
				infowindow.open(map,marker);
				google.maps.event.addListener(marker, 'click', function(){ infowindow.close()});
				google.maps.event.addListener(marker, 'dragstart', function(){ infowindow.close()});
			}
			google.maps.event.addListener(marker, 'dragend', markerPoint);
			SetGeo = false;
		}
		else{
		 marker = new google.maps.Marker({
				draggable:false,
				icon: markImage,
				borderPadding: 50, 
				maxZoom: 15,
				animation: google.maps.Animation.DROP,
		      		position: new google.maps.LatLng(Lati , Lngg),
		      		map: map
		    });
			markers.push(marker);
		}
	 }, 1500)
 }
 
	  // Sets the map on all markers in the array.
      function setAllMap(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
      }

      // Removes the overlays from the map, but keeps them in the array.
      function clearOverlays() {
        setAllMap(null);
      }
      // Deletes all markers in the array by removing references to them.
      function deleteOverlays() {
        clearOverlays();
        markers = [];
      }
function addMarker(Lat , Lan) {
	var iconpath = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|FE7569";
	if(jQuery('#sloc_hdfLogo').val().length >0){
		iconpath = jQuery('#sloc_hdfLogo').val();
	}
	var mapOptions ={
			center: new google.maps.LatLng(Lat , Lan),
			zoom: 13,
			mapTypeId: google.maps.MapTypeId.ROADMAP, 
			zoomControl: true,
			zoomControlOptions: {
			style: google.maps.ZoomControlStyle.LARGE,
			position: google.maps.ControlPosition.RIGHT_TOP
			},
			panControl: true,
			panControlOptions: {
			position: google.maps.ControlPosition.RIGHT_TOP
			},		
			streetViewControl: true
      }
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	 var markImage = new google.maps.MarkerImage(iconpath);
	  var shadow = new google.maps.MarkerImage('../gIcon/14.png',
	   new google.maps.Size(50, 19),
	   new google.maps.Point(0,0),
	   new google.maps.Point(22, -7));	   
	 marker = new google.maps.Marker({
			position: new google.maps.LatLng(Lat , Lan),
			map:map,
			draggable:false,
			icon: markImage,
			borderPadding: 50, 
			maxZoom: 15,
			animation: google.maps.Animation.DROP
		});
	jQuery('#sloc_tbLat, #sloc_hdfLatt').val(Lat.toFixed(6)); jQuery('#sloc_tbLng, #sloc_hdfLngg').val(Lan.toFixed(6));
	markers.push(marker);
}
function codeLatLng(lat, lng) {
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
        var indice=0;
        for (var j=0; j<results.length; j++)
        {
            if (results[j].types[1]=='political')
                {
                    indice=j;
                    break;
                }
        }
        for (var i=0; i<results[j].address_components.length; i++)
            {
                if (results[j].address_components[i].types[0] == "locality") {
                        city = results[j].address_components[i];
						jQuery('#tbCity').val(city.long_name);
                    }
                if (results[j].address_components[i].types[0] == "administrative_area_level_1") {
                        region = results[j].address_components[i];
						jQuery('#tbState').val(region.long_name);
                    }
                if (results[j].address_components[i].types[0] == "country") {
                        country = results[j].address_components[i];
						jQuery('#tbCountry').val(country.long_name);
                    }
		   		if (results[j].address_components[i].types[0] == "postal_code") {
						postal = results[j].address_components[i];
						jQuery('#tbZip').val(postal.long_name);
				   }
			   if (results[j].address_components[i].types[0] == "sublocality") {
					    sublocality  = results[j].address_components[i];
				}
            }
			jQuery('#tbAddress').val(results[j].formatted_address);
			jQuery('#tbZip').focus();
            } else {
              alert("<?php _e('No results found', self::TEXT_DOMAIN); ?>");
            }
        //}
      } else {
        alert("<?php _e('Geocoder failed due to', self::TEXT_DOMAIN); ?>: " + status);
      }
    });
  } 
function ajStoreResult(returnVal){
	if(returnVal == '1'){
	}
	else{
	}
} 

function ajResult(returnVal){
		if(returnVal == '1'){
			var sl_dal_data= { action: 'sl_dal_category',FunMethod: 'Select' };
			giz_Locator.util.ajaxSer("admin-ajax.php", "POST", sl_dal_data, ajReturnVal, '<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
		    giz_Locator.util.ajax("ajax/ajx_CategorySave.php","POST","FunMethod=GetMarker" ,MarkerLogo, '<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
		}
		else{
		}
  }

 function ajLogoResult(returnVal){
		if(returnVal == '1'){
			var sl_mapsetting_dal = { action: 'sl_dal_mapsettings', FunType: 'LogoSelect' }
			giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_mapsetting_dal, ajLogoVal);
		}
		else{
		}
  }
 function ajReturnVal(returnVal){
	if(returnVal != null){
			$('#sloc_ddlCategory').ddslick('destroy');	
			$('#sloc_ddlCategory').empty();
			for (var i = 0; i < returnVal.length; i++){
				var sel_Id ="";
				sel_Id = (returnVal[i]["isdefault"] == "1") ? 'selected="selected"' : '';
			   $('#sloc_ddlCategory').append("<option value="+ returnVal[i]["categoryid"] +" data-imagesrc="+ sl_plugin_url+ returnVal[i]["categoryicon"] +" "+ sel_Id +" >"+ returnVal[i]["category"] +"</option>");
			}
			if($('#sloc_ddlCategory option').size() > 0){
				$('#sloc_ddlCategory').ddslick({
					elementId : 'sloc_CatId',
					height: 160,
					onSelected: function(dData){
						jQuery('#sloc_hdfLogo').val(dData.selectedData.imageSrc);
							if($('#sloc_cbAuto').is(':checked')){
								addNewMarker(dData.selectedData.imageSrc, true);
							}else{
								addNewMarker(dData.selectedData.imageSrc, false);
							}
					}   
				});
			}
	}
  }
function MarkerLogo(returnVal){
}
function ajLogoVal(returnVal){
	$('#sloc_storeLogo').ddslick('destroy');	
	$('#sloc_storeLogo').empty();
	for (var i = 0; i < returnVal.length; i++){
		var descr = (returnVal[i]["default"] == '1') ? '<?php _e('Default', self::TEXT_DOMAIN); ?>' : '<?php _e('Store', self::TEXT_DOMAIN); ?>';
		var logoSelect = (returnVal[i]["default"] == '1') ? ' selected=selected' : '';
	   $('#sloc_storeLogo').append("<option value="+ returnVal[i]["logoid"] +" data-imagesrc="+ sl_plugin_url+ returnVal[i]["logopath"] +" data-description="+ descr +" "+ logoSelect +">Logo"+ (i+1) +"</option>");
	}
	if($('#sloc_storeLogo option').size() > 0){
		$('#sloc_storeLogo').ddslick({
				elementId : 'sloc_LogoId',
				height: 180,
	    	onSelected: function(dData){
				jQuery('#sloc_hdfLogoAdd').val(dData.selectedData.value);
				jQuery('#sloc_hdfLogoType').val(dData.selectedData.description);
	    	}   
		});
	}
  }
function UploadFile(fileVal){
	var exten = fileVal.substring(fileVal.lastIndexOf('.') + 1);
	if(exten == "gif" || exten == "GIF" || exten == "JPEG" || exten == "jpeg" || exten == "jpg" || exten == "JPG" || exten == "png" || exten == "PNG"){
		 var img, reader, file;
		 formdata = false;
		 if (window.FormData) {
			 formdata = new FormData();
		 }
		 file =  document.getElementById('sloc_fuMarkerIcon').files;
		 if(file.length>0){
			 file = file[0];				
			 if (!!file.type.match(/image.*/)) {
				 if ( window.FileReader ) {
				 reader = new FileReader();
				 }
				 if (formdata) {
					 formdata.append("sloc_fuMarkerIcon", file);
				 }
			 }
		 }
		 formdata.append("FunMethod", "MarkerSave");
		 formdata.append("action", 'sl_dal_category');
		 if (formdata) {	
			giz_Locator.util.ajaxFile("admin-ajax.php","POST", formdata, markerResult, "<?php _e('Data has been saved successfully', self::TEXT_DOMAIN); ?>.", false);			
		}
		
	}else{
		alert("Allowed images only");
		jQuery('#sloc_fuMarkerIcon').val('');
		return false;
	}
}
function markerResult(resVal){
	if(jQuery.trim(resVal) == "1"){
		jQuery('#sloc_fuMarkerIcon').val('');
		var sl_dal_data= { action: 'sl_dal_category',FunMethod: 'MarkSelect' };
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_dal_data, bindMarker,'<?php _e('Loading', self::TEXT_DOMAIN); ?>..');
	}else{
	}
}
function bindMarker(resVal){
	if(resVal != null){
		$('#sloc_ddlMarker').ddslick('destroy');
		$('#sloc_ddlMarker').empty();
		for (var i = 0; i < resVal.length; i++){
			var  ddSelect = (resVal[i]["default"] == "1") ? "selected='selected'" : "";
			var ddDef 	  = (resVal[i]["default"] == "1") ? "<?php _e('Default', self::TEXT_DOMAIN); ?>" : "";
			$('#sloc_ddlMarker').append("<option value="+ resVal[i]["markerid"] +" data-imagesrc="+ sl_plugin_url+ resVal[i]["markerpath"] +" data-description='"+ ddDef +"'  "+ ddSelect +"></option>");
		}
		if($('#sloc_ddlMarker option').size() > 0){
			$('#sloc_ddlMarker').ddslick({
					elementId : 'sloc_MarkerId',
					height: 120,
					width: 100,
					onSelected: function(dData){
				}   
			});
		}
	}else{
		
	}
}
</script>
<div class="sl_menu_icon sloc_newloc sl_icon32" ><br /></div>
			<h2 class="sl_menu_title"><?php _e('Add New Location', self::TEXT_DOMAIN); ?> </h2>
			<div class="clearb">
			<input type="hidden" id="sloc_hdfLatt"   name="sloc_hdfLatt"  value = "47.608941" />
			<input type="hidden" id="sloc_hdfLngg"   name="sloc_hdfLngg"  value = "-122.340145" />
			<input type="hidden" id="sloc_hdfLattG"  name="sloc_hdfLattG" value = "0.0" />
			<input type="hidden" id="sloc_hdfLnggG"  name="sloc_hdfLnggG" value = "0.0" />
			<input type="hidden" id="sloc_hdfLogo"   name="sloc_hdfLogo"  value = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|FE7569" />
					<div id="leftBar">
						<form id="sl_frmStore" name="sl_frmStore" action="#"	enctype="multipart/form-data" method="post"	>								
									<input id="sloc_storeId" name="sloc_storeId" type="hidden" value="<?php echo $storeId; ?>" />
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Geocoding Mode', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl iPhone">
											<input id="sloc_cbAuto" name="sloc_cbAuto" type="checkbox"/>
										</div>
										<div class="fl pad5"><span class="sl_hint" style="font-weight:bold; font-size:14px; color:#AA2200;cursor: pointer;"><img src="<?php echo plugins_url('/images/icon/hint.png', dirname(__FILE__)); ?>" alt="" /></span></div>
									</div>
									<div class="pad5 wd300 clearb">
							        <div class="fl wd60">
                                    <label><?php _e('Store Name', self::TEXT_DOMAIN); ?></label></div>
                                    <div class="fl">
										<input id="sloc_tbName" name="sloc_tbName" class="required inp-form wd250" type="text" value="<?php echo $strName; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Category', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><select id="sloc_ddlCategory" name="sloc_ddlCategory" class="input_sel required" style="width:200px; height:50px">
										<?php
                                            $sl_sql_str = "SELECT C.`categoryid`, C.`category`, M.`markerpath` AS CategoryIcon, C.`isdefault` FROM `$sl_tb_store_cat` AS C INNER JOIN `$sl_tb_store_marker` AS M ON M.`markerid` = C.`markerid` ORDER BY C.`createddate` DESC";
                                            $sl_select_obj = $wpdb->get_results($sl_sql_str);
                                            foreach ($sl_select_obj as $sl_cate_row) {
                                                $sel_Id = '';
                                                if ($cat != '') {
                                                    $sel_Id = ($cat == $sl_cate_row->categoryid) ? 'selected="selected"' : '';
                                                } else {
                                                    $sel_Id = ($sl_cate_row->isdefault == '1') ? 'selected="selected"' : '';
                                                }
                                                echo "<option value='".$sl_cate_row->categoryid."' data-imagesrc='".plugins_url('/'.$sl_cate_row->CategoryIcon, dirname(__FILE__))."' $sel_Id >".$sl_cate_row->category.'</option>';
                                            }
                                        ?>
										</select></div>
										<div class="fl"><a href="javascript:void(0);" id="sloc_addCat" name="sloc_addCat" class="sp_icon-href" ><img src="<?php echo plugins_url('/images/icon/icon_plus.png', dirname(__FILE__)); ?>" alt="<?php _e('Add/Remove Category', self::TEXT_DOMAIN); ?>"  title="<?php _e('Add/Remove Category', self::TEXT_DOMAIN); ?>"style="padding-left:4px; padding-top: 25px" /></a></div>										
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Address', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbAddress" name="sloc_tbAddress" class="required inp-form wd250" type="text" onblur="return getPoint()" value="<?php echo $strAdd; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb" style="display:none">
										<div class="fl wd60"><label><?php _e('Latitude', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbLat" class="required inp-form" name="sloc_tbLat" type="text" readonly="readonly" value="<?php echo $Lat; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb" style="display:none">
										<div class="fl wd60"><label><?php _e('Longitude', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbLng" class="required inp-form" name="sloc_tbLng" type="text" readonly="readonly" value="<?php echo $Lng; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('City', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbCity" name="sloc_tbCity" class="required inp-form" type="text" value="<?php echo $city; ?>" onblur="return getPoint()" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('State/Province', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbState" name="sloc_tbState" class="inp-form" type="text" value="<?php echo $state; ?>" onblur="return getPoint()" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Country', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbCountry" name="sloc_tbCountry" class="required inp-form" type="text" value="<?php echo $country; ?>" onblur="return getPoint()" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Zipcode', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbZip" name="sloc_tbZip" class="inp-form zipcode" maxlength="8" type="text" value="<?php echo $zip; ?>" /></div>
									</div>									
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Phone#', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbPhone" name="sloc_tbPhone" class="inp-form Phone" maxlength="20" type="text" value="<?php echo $contactNo; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Fax#', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbFax" name="sloc_tbFax" class="inp-form" maxlength="20" type="text" value="<?php echo $faxNo; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Email', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbEmail" name="sloc_tbEmail" class="inp-form email wd250" type="text" value="<?php echo $email; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Website', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbWeb" name="sloc_tbWeb" class="inp-form url wd250" type="text" value="<?php echo $web; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Label', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl">
										<select id="sloc_ddlLabel" name="sloc_ddlLabel" style="width:200px; height:50px">        
												<?php
                                                    $sl_sql_str = "SELECT * FROM `$sl_tb_store_label`";
                                                    $sl_select_obj = $wpdb->get_results($sl_sql_str);
                                                    foreach ($sl_select_obj as $sl_lbl_row) {
                                                        $select = '';
                                                        if (!empty($LabId)) {
                                                            $select = (trim($sl_lbl_row->labelid) == $LabId) ? 'selected=selected' : '';
                                                        }
                                                        echo "<option value='".$sl_lbl_row->labelid."' data-imagesrc='".plugins_url('/'.$sl_lbl_row->imgurl, dirname(__FILE__))."' $select >".$sl_lbl_row->colortype.'</option>';
                                                    }
                                                ?>
					    				</select>
										</div>
									</div>
									<div class="pad5 wd300 clearb LabelTxt" style="display:<?php echo ($LabId > 1) ? 'block' : 'none'; ?>">
										<div class="fl wd60"><label><?php _e('Label Text', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl"><input id="sloc_tbLabelTxt" name="sloc_tbLabelTxt" class="inp-form required" maxlength="40" type="text" value="<?php echo $LabelText; ?>" /></div>
									</div>
									<div class="pad5 wd300 clearb">
										<div class="fl wd60"><label><?php _e('Logo', self::TEXT_DOMAIN); ?></label></div>
										<div class="fl">
											<input type="hidden" id="sloc_hdfLogoType" name="sloc_hdfLogoType" />
											<select id="sloc_storeLogo" name="sloc_storeLogo" class="required" style="width:200px; height:50px">        
												<?php
                                                    $sl_sql_str = "SELECT * FROM `$sl_tb_store_logos`";
                                                    $sl_select_obj = $wpdb->get_results($sl_sql_str);
                                                    $inc = 1;
                                                    foreach ($sl_select_obj as $sl_stlogo_row) {
                                                        $descri = (trim($sl_stlogo_row->default) == '1') ? 'Default' : 'Store';
                                                        if (!empty($Logoid)) {
                                                            $select = (trim($sl_stlogo_row->logoid) == $Logoid) ? 'selected=selected' : '';
                                                        } else {
                                                            $select = (trim($sl_stlogo_row->default) == '1') ? 'selected=selected' : '';
                                                        }
                                                        echo "<option value='".$sl_stlogo_row->logoid."' data-imagesrc='".plugins_url('/'.$sl_stlogo_row->logopath, dirname(__FILE__))."' data-description='$descri'  $select>".'Logo'.$inc.'</option>';
                                                        ++$inc;
                                                    }
                                                ?>
					    					</select>
										</div>
									<div class="fl">
										<a href="javascript:void(0);" id="sloc_addLogo" name="sloc_addLogo" alt="<?php _e('Add Logo', self::TEXT_DOMAIN); ?>" class="sp_icon-href" >
										  <img src="<?php echo plugins_url('/images/icon/icon_plus.png', dirname(__FILE__)); ?>" alt="<?php _e('Add Logo', self::TEXT_DOMAIN); ?>" title="<?php _e('Add Logo', self::TEXT_DOMAIN); ?>" style="padding-left: 5px;padding-top: 30px;" />
										</a>
									</div>
										<div class="fl">
											<a href="javascript:void(0);" id="sloc_removeLogo" name="sloc_removeLogo" alt="<?php _e('Remove Logo', self::TEXT_DOMAIN); ?>" class="sp_icon-href" >
												<img src="<?php echo plugins_url('/images/icon/icon_minus.png', dirname(__FILE__)); ?>" alt="<?php _e('Remove Logo', self::TEXT_DOMAIN); ?>" title="Remove Logo" style="padding-left: 5px;padding-top: 30px;" />
											</a>
										</div>
										<input type="hidden" id="sloc_hdfLogoAdd" name="sloc_hdfLogoAdd" />
										<input type="hidden" id="sloc_hdfLabelId" name="sloc_hdfLabelId" />
									</div>									
									<div class="pad5 wd300 clearb">
										<input id="sloc_btnSubmit" name="sloc_btnSubmit" class="btn btn-blue" type="submit" value="<?php echo $btnText; ?>" />  
										<input id="sloc_btnreset" name="sloc_btnreset" class="btn btn-blue" type="reset" value="<?php _e('Reset', self::TEXT_DOMAIN); ?>" />
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
			
			<div class="clear">
</div>
 <div style="display:none;">
<div id="sloc_frmAddNew"  class="overlaypanel" >
	<form id="sloc_frmCategory" name="sloc_frmCategory" action="#" enctype="multipart/form-data" method="post" >
		<input id="sloc_hdfCatId" name="sloc_hdfCatId" type="hidden" value="0" />
		<div class="pad5 pal5 clearb">
			<div class="fl wd60"><label><?php _e('Marker Image', self::TEXT_DOMAIN); ?></label></div>
			<div class="fl">			
				<select id="sloc_ddlMarker" name="sloc_ddlMarker" class="input_sel" style="width:75px; height:50px">
					<?php
                        $sl_sql_str = "SELECT `markerid`, `markerpath`, `default` FROM `$sl_tb_store_marker` ORDER BY `createddate` DESC";
                        $sl_select_obj = $wpdb->get_results($sl_sql_str);
                        foreach ($sl_select_obj as $sl_marker_row) {
                            $sel_Id = ($sl_marker_row->default == '1') ? 'selected="selected"' : '';
                            $de_val = ($sl_marker_row->default == '1') ? 'Default' : '';
                            echo "<option value='".$sl_marker_row->markerid."' data-imagesrc='".plugins_url('/'.$sl_marker_row->markerpath, dirname(__FILE__))."' data-description='$de_val' $sel_Id ></option>";
                        }
                    ?>
				</select>
			</div>
			<div class="fl">
				<a href="javascript:void(0);" id="sloc_removeMark" name="sloc_removeMark" alt="<?php _e('Remove Marker', self::TEXT_DOMAIN); ?>"><img src="<?php echo plugins_url('/images/icon/icon_minus.png', dirname(__FILE__)); ?>"  alt="<?php _e('Remove Marker', self::TEXT_DOMAIN); ?>" title="<?php _e('Remove Marker', self::TEXT_DOMAIN); ?>" style="padding-left:4px; padding-top:25px;" /></a>
				<a href="javascript:void(0);" id="sloc_defaultMark" name="sloc_defaultMark" alt="<?php _e('Set As Default Marker', self::TEXT_DOMAIN); ?>"><img src="<?php echo plugins_url('/images/icon/defa.png', dirname(__FILE__)); ?>" style="height:18px;" alt="<?php _e('Set As Default Marker', self::TEXT_DOMAIN); ?>" title="<?php _e('Set As Default Marker', self::TEXT_DOMAIN); ?>" style="padding-left:4px; padding-top:25px;" /></a>
				
			</div>
			<div class="fileContain fl" style="margin:20px 0 0 25px;">
				<input type="file" id="sloc_fuMarkerIcon" name="sloc_fuMarkerIcon" onchange="return UploadFile(this.value);" class="sl_admin_browse" />				
			</div>
		</div>
		<div class="pad5 pal5 clearb">
			<div class="fl wd60"><label><?php _e('Category Name', self::TEXT_DOMAIN); ?></label></div>
			<div class="fl">
				<input id="sloc_tbCatName" name="sloc_tbCatName" class="required inp-form" type="text" />
			</div>
			<div class="clearb">
			<div class="fl wd60"><label>&nbsp;</label></div>
				<div style="padding-top:10px;" class="fl"><input id="sloc_cbCatDefault" name="sloc_cbCatDefault" type="checkbox" class="fl" /><label for="sloc_cbCatDefault" class="fl" style="display: block;margin-top: -5px;"><?php _e('Set as default', self::TEXT_DOMAIN); ?></label></div>
			</div>
		</div>
		<div class="pad5 clearb pal5" style="margin-top:10px">
			<input id="sloc_btnCatSave" class="btn btn-blue fl" name="sloc_btnCatSave" type="submit" value="Save" /> 
			<div id="sloc_msgBox" class="sl_msg_box fl" style="margin-left: 15px;"></div>
		</div>
		<div class="pad5 pal5 clearb">
		<table border="0" width="100%" cellpadding="0" cellspacing="0" id="sloc_TbCategorty" class="sl_table" style="text-align:center;">
				<tr>				
					<th class="line-left minwidth-1" style="text-align:center;"><span><?php _e('Category Name', self::TEXT_DOMAIN); ?></span>	</th>
					<th class="line-left" style="text-align:center;"><span><?php _e('Default', self::TEXT_DOMAIN); ?></span></th>
					<th class="line-left" style="text-align:center;"><span><?php _e('Options', self::TEXT_DOMAIN); ?></span></th>
				</tr>
					<?php
                        $sl_sql_str = "SELECT * FROM `$sl_tb_store_cat` ORDER BY `CreatedDate` DESC";
                        $sl_select_obj = $wpdb->get_results($sl_sql_str);
                        foreach ($sl_select_obj as $sl_category_row) {
                            $isDefault = ($sl_category_row->isdefault == '1') ? 'Yes' : 'No';
                            $defCss = ($sl_category_row->isdefault == '1') ? 'sl_default' : '';
                            echo '<tr>'.
                                '<td>'.$sl_category_row->category.'</td>'.
                                '<td>'.$isDefault.'</td>'.
                                "<td class='options-width' valign='middle' style='text-align:center;width: 100px;'>".
                                    "<a href='javascript:void(0);' title='".__('Edit', self::TEXT_DOMAIN)."' rel='".$sl_category_row->categoryid."' catName='".$sl_category_row->category."' markId='".$sl_category_row->markerid."' defat='".$sl_category_row->isdefault."' class='sl_icon-5 sl_catEdit sl_info-tooltip'></a>".
                                    "<a href='javascript:void(0);' title='".__('Delete', self::TEXT_DOMAIN)."' isDef='".$defCss."' rel='".$sl_category_row->categoryid."'  class='sl_icon-2 sl_catDel sl_info-tooltip'></a>".
                                    "<a href='javascript:void(0);' title='".__('Set As Default', self::TEXT_DOMAIN)."' rel='".$sl_category_row->categoryid."' class='sl_icon-3 sl_catDef $defCss sl_info-tooltip'></a>".
                                '</td>'.
                            '</tr>';
                        }
                    ?>				
		</table>
			<div id="sl_pagination_green" style="margin: 4px auto;">
            </div>
		</div>
   </form>

</div>
</div>

<div style="display:none;">
<div id="sloc_frmAddLogo"  class="overlaypanel" >
	<form id="sloc_frmLogo" name="sloc_frmLogo" action="#" enctype="multipart/form-data" method="post">
		<div class="pad5 wd200 pal5">
			<div class="fl wd60"><label><?php _e('Browse Logo', self::TEXT_DOMAIN); ?></label></div>
			<div class="sl_fileContain fl" style="height:45px;">
				<input type="file" id="sloc_fuNewLogo" name="sloc_fuNewLogo" class="sl_admin_browse" onchange="getElementById('sl_hdffuNewLogo').value = getElementById('sloc_fuNewLogo').value;" />
				<input type="hidden" id="sl_hdffuNewLogo" name="sl_hdffuNewLogo" />
			</div>	
		</div>
<div class="pad5 wd200 pal5">
			<div style="padding-top:10px;" class="fl"><input id="sl_cbDefault" name="sl_cbDefault" type="checkbox" class="fl" /><label for="sl_cbDefault" class="fl" style="display: block;margin-top: -5px;"><?php _e('Set as default', self::TEXT_DOMAIN); ?></label></div>			
		</div>
		<div class="pad5 pal5 clearb">
		<span style="color:red;font-weight:bold;"><?php _e('Note', self::TEXT_DOMAIN); ?> : </span> <span><?php _e('If you select this will be taken as Default', self::TEXT_DOMAIN); ?>.</span>
		</div>
		<div class="pad5 wd200 clearb pal5"><input id="sl_btnLogoSave" class="btn btn-blue" name="sl_btnLogoSave" type="submit" value="Save" /> </div>
   </form>

</div>
</div>