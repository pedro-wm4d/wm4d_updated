<?php
	wp_enqueue_script('sl_admin_iphonecheck_script', plugins_url( '/js/plugin/iPhone-Checkbox.js', dirname(__FILE__) ), '', false, true);
	wp_enqueue_script('sl_admin_smartpage_script', plugins_url( '/js/plugin/smartpaginator.js', dirname(__FILE__) ), '', false, true);
	wp_register_style( 'sl_admin_smartpager_style', plugins_url( '/css/sl_admin_smartpaginator.css', dirname(__FILE__ )),'all' ); wp_enqueue_style( 'sl_admin_smartpager_style' );
?>
<style type="text/css">
span.error{
	color:red;
	display: block;
    margin-top: 0px;
}
br.error{
	display:none;
}
.succ_msg{
	color :#789517;
	font-weight:bold;
	font-size:13px;
}
</style>
<script type="text/javascript">
var $ = jQuery.noConflict();
var sl_plugin_url = "<?php echo plugins_url('/',  dirname(__FILE__)); ?>";
jQuery(document).ready(function(){
$('.iPhone :checkbox').iphoneStyle();
	/***** Initialize Google Map ******/
	var StoreCount = <?php echo $sl_stores_count; ?>;
	jQuery("#sl_frmImport").validate({
		rules: {
		    	'sloc_fuStoreData': {
			      required: true,
			      accept: 'xls|csv'
		    	}
		  },
		messages:{
			'sloc_fuStoreData': {
			      required: '<?php _e( 'Please select file', self::TEXT_DOMAIN ); ?>.',
			      accept: '<?php _e( 'Excel file only allowed', self::TEXT_DOMAIN ); ?>'
		    	}
		  },
   		submitHandler: function(form) {
				var img, reader, file;
				formdata = false;
				if (window.FormData) {
					formdata = new FormData();
					}
				file =  document.getElementById('sloc_fuStoreData').files;
				if(file.length>0){
					file = file[0];

						if (formdata) {
							formdata.append("sloc_fuStoreData", file);
						}
				}
				 var fields = $(":input").serializeArray();
				  jQuery.each(fields, function(i, field){
					formdata.append(field.name, field.value);
				  });
				  formdata.append("Funtype", "Import_Location");
				  formdata.append("action", "sl_dal_locationimport");
				if (formdata) {
					giz_Locator.util.ajxBlockUI("<?php _e( 'Saving Data', self::TEXT_DOMAIN );?>...", true, "success", sl_plugin_url);
					giz_Locator.util.ajaxFile("admin-ajax.php","POST", formdata ,sl_ajResult, "<?php _e( 'Data has been saved successfully', self::TEXT_DOMAIN ); ?>.");
				}
				$('#sl_frmImport').find('input:file').val('');
   		}
	});
	if(StoreCount > 0){
		$('#sl_pagination_green').smartpaginator({ totalrecords: StoreCount, recordsperpage: 10, datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green' });
	}
	jQuery('#leftBar').css({ 'width' : jQuery('#sl_frmImport').width() + 10 });
	jQuery('#rightContent').css({ 'width' : jQuery('#wpbody-content').width() - jQuery('#sl_frmImport').width() - 62 });
	jQuery(window).resize(function(){
		jQuery('#leftBar').css({ 'width' : jQuery('#sl_frmImport').width() });
		if((jQuery('#wpbody-content').width() - jQuery('#sl_frmImport').width() - 62) <= 510){
			jQuery('#rightContent').css({ 'width' : jQuery('#sloc_tStores').width() - 52 });
		}else{
			jQuery('#rightContent').css({ 'width' : jQuery('#wpbody-content').width() - jQuery('#sl_frmImport').width() - 62 });
		}
	});
});
function testajReturnVal(){
}
function sl_ajResult(returnVal){
	jQuery('.succ_msg').html('');
	jQuery('.succ_msg').html(returnVal);
	var sl_manageloc_dal = { action: 'sl_dal_managelocation', Method: 'Select' }
	giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_manageloc_dal, sl_ajReturnVal);

}
function sl_ajReturnVal(returnVal){
$('#sloc_tStores').unblockUI;
	$('#sloc_tStores').empty();
	var content="";
	content += '<tr><th class="line-left minwidth-1"><span><?php _e( 'iscustid', self::TEXT_DOMAIN ); ?></span></th>'+
					'<th class="line-left minwidth-1"><span><?php _e( 'Store Name', self::TEXT_DOMAIN ); ?></span></th>'+
					'<th class="line-left minwidth-1"><span><?php _e( 'Address', self::TEXT_DOMAIN ); ?></span></th>'+
					'<th class="line-left"><span><?php _e( 'City', self::TEXT_DOMAIN ); ?></span></th>'+
					'<th class="line-left"><span><?php _e( 'Country', self::TEXT_DOMAIN ); ?></span></th>'+
					'<th class="line-left"><span><?php _e( 'Email', self::TEXT_DOMAIN ); ?></span></th>'+
					'<th class="line-left"><span><?php _e( 'Contact No', self::TEXT_DOMAIN ); ?>.</span></th>'+
					'<th class="line-left"><span><?php _e( 'Fax', self::TEXT_DOMAIN ); ?></span></th>'+
				'</tr>';
	for (var i = 0; i < returnVal.length; i++){
			cssClass 		= ( i+1 %2  ) ? "class='alternate-row'" : "";
			DefLoc		 	= ( returnVal[i]["defaultloc"] == "1"  ) ? "default" : "";
			email 			= (returnVal[i]["email"] != null) ? returnVal[i]["email"] : "";
			phone			= (returnVal[i]["phone"] != null) ? returnVal[i]["phone"] : "";
			fax 			= (returnVal[i]["fax"] != null) ?  returnVal[i]["fax"] : "";
			content += "<tr "+ cssClass +">"+
			"<td>" + returnVal[i]["iscustid"] + "</td>"+
			"<td>" + returnVal[i]["name"] + "</td>"+
			"<td>" + returnVal[i]["address"]+ "</td>"+
			"<td>" + returnVal[i]["city"] + "</td>"+
			"<td>" + returnVal[i]["country"] + "</td>"+
			"<td>" + email + "</td>"+
			"<td>" + phone + "</td>"+
			"<td>" + fax + "</td>"+
			"</tr>";

	}
	$('#sloc_tStores').append(content);
	if(returnVal.length > 0){
		$('#sl_pagination_green').smartpaginator({ totalrecords: returnVal.length, recordsperpage: 10, datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green' });
		jQuery('.divExport').show();
	}
	else{
		$('#sloc_tStores').empty();
		content +="<tr><td colspan='8'><p style='text-align:center'><strong><?php _e( 'There are no stores', self::TEXT_DOMAIN ); ?>..</strong></p></td></tr>";
		$('#sloc_tStores').append(content);
		jQuery('.divExport').hide();
	}
  }
</script>
	<div class="sl_menu_icon sloc_import sl_icon32" ><br /></div>
	<h2 class="sl_menu_title"><?php _e( 'Import/Export Location', self::TEXT_DOMAIN ); ?></h2>
	<div class="clearb">
		<div id="leftBar">
			<form id="sl_frmImport" name="sl_frmImport" action="#" enctype="multipart/form-data" method="post" >
				<input type="hidden" name="sloc_hdfUserId" id="sloc_hdfUserId" value ="<?php echo $current_user->ID; ?>" />
				<div class="pad5 wd200 clearb">
					<div class="fl wd150">
					<a href="<?php echo $this->plugin_url(); ?>/sl_file_download.php?download_file=xcel/template.xls" id="sloc_downloadTemp" name="sloc_downloadTemp" alt="Download Templates" class="download" target="_blank" >Download Template</a>
					</div>
				</div>
				<div class="clearb" style="height:20px"></div>
				<h4 style="text-decoration:underline"><?php _e( 'Import Store', self::TEXT_DOMAIN ); ?></h4>
				<div class="pad5 wd200 clearb">
					<div class="fl wd150"><label><?php _e( 'Browse File', self::TEXT_DOMAIN ); ?></label></div>
					<div class="fl wd250"><input id="sloc_fuStoreData" name="sloc_fuStoreData" class="inp-form required" type="file" /></div>
				</div>
				<div class="pad5 wd200 clearb">
				<input id="sloc_btnImport" name="sloc_btnImport" class="btn btn-blue" type="submit" value="<?php _e( 'Import', self::TEXT_DOMAIN ); ?>" /> </div>
				<div class="pad5 clearb">
				<span class="succ_msg"></span>
		</div>
			</form>
		</div>
		<div id="rightContent">
		<!-- Google Map -->
			<table border="0" width="100%" cellpadding="0" cellspacing="0" id="sloc_tStores" class="sl_table">
				<tr>
					<th class="line-left minwidth-1"><span><?php _e( 'iscustid', self::TEXT_DOMAIN ); ?></span>	</th>
					<th class="line-left minwidth-1"><span><?php _e( 'Store Name', self::TEXT_DOMAIN ); ?></span>	</th>
					<th class="line-left minwidth-1"><span><?php _e( 'Address', self::TEXT_DOMAIN ); ?></span></th>
					<th class="line-left"><span><?php _e( 'City', self::TEXT_DOMAIN ); ?></span></th>
					<th class="line-left"><span><?php _e( 'Country', self::TEXT_DOMAIN ); ?></span></th>
					<th class="line-left"><span><?php _e( 'Email', self::TEXT_DOMAIN ); ?></span></th>
					<th class="line-left"><span><?php _e( 'Contact No', self::TEXT_DOMAIN ); ?>.</span></th>
					<th class="line-left"><span><?php _e( 'Fax', self::TEXT_DOMAIN ); ?></span></th>
				</tr>
				<?php
					$tot_count = 0;
					foreach($sl_select_obj as $sl_store_rows){
							$tot_count 		= $tot_count +1;
							$cssClass 		= ( $tot_count %2  ) ? "class='alternate-row'" : "";
							echo "<tr ". $cssClass .">
							<td>".$sl_store_rows->iscustid."</td>
							<td>".$sl_store_rows->name."</td>
							<td>".$sl_store_rows->address."</td>
							<td>".$sl_store_rows->city."</td>
							<td>".$sl_store_rows->country."</td>
							<td>".$sl_store_rows->email."</td>
							<td>".$sl_store_rows->phone."</td>
							<td>".$sl_store_rows->fax."</td></tr>";
							$tot_count++;
					}
					if($tot_count <= 0){
						echo "<tr class='alternate-row'>".
						"<td colspan='7'><p style='text-align:center'><strong>". __( 'There are no stores', self::TEXT_DOMAIN )."</strong></td></tr>";
					}
				?>
			</table>
			<div id="sl_pagination_green" style="margin: 4px auto;">
			</div>
			<form name="sl_frmExport" id="sl_frmExport" action="<?php echo admin_url('admin.php?page=bulk-import-export&action=sl_location_export_cbf', 'storelocator'); ?>" method="post">
			<div class="fr wd150 divExport" style="display:<?php echo ($tot_count >0) ? 'block' : 'none';  ?>">
					<input type="submit" name="sl_btnExport" class="download btn_href" value="<?php _e( 'Export Data', self::TEXT_DOMAIN ); ?>" />
			</div>
			</form>
			<!-- Google Map -->
		</div>
	</div>
	<div class="clear"></div>

