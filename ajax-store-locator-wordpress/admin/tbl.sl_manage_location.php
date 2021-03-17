<?php
		wp_enqueue_script('sl_admin_smartpage_script', plugins_url( '/js/plugin/smartpaginator.js', dirname(__FILE__) ), '', false, true);
		wp_enqueue_script('sl_admin_tooltip_script', plugins_url( '/js/plugin/jquery.tooltip.js', dirname(__FILE__) ), '', false, true);
		wp_enqueue_script('sl_admin_iphonecheck_script', plugins_url( '/js/plugin/jEncrypt.js', dirname(__FILE__) ), '', false, true);
		wp_register_style( 'sl_admin_smartpager_style', plugins_url( '/css/sl_admin_smartpaginator.css', dirname(__FILE__ )),'all' ); wp_enqueue_style( 'sl_admin_smartpager_style' );
?>
<script type="text/javascript">
var sl_plugin_url = "<?php echo plugins_url('/',  dirname(__FILE__)); ?>";
var $ = jQuery.noConflict();
jQuery(document).ready(function() {
	jQuery('#btnDeleteSelect').on('click', function(){
		var checkboxes = jQuery('#sloc_tStores tr.activePage').find(':checkbox:checked');
		if(checkboxes.length > 0){
				var store_id = new Array();
				jQuery.each($("input[name='chkSelectAction[]']:checked"), function() {
					store_id.push(jQuery(this).val());
				});
				giz_Locator.util.ajxBlockUI("<?php _e( 'Deleting Data', self::TEXT_DOMAIN ); ?>...", true, "success", sl_plugin_url);
				var sl_deleteloc_dal = { action: 'sl_dal_managelocation', Method: 'bulk_delete', sloc_StoreIds : store_id }
				giz_Locator.util.ajax("admin-ajax.php","POST", sl_deleteloc_dal, function(axjReturn){
						if(axjReturn == 1){
							var sl_manageloc_dal = { action: 'sl_dal_managelocation', Method: 'Select' }
							giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_manageloc_dal, sl_ajReturnVal);
							jQuery('#chkSelectAll').removeAttr('checked');
							jQuery('.deleteAll').hide();
						}
				}, "<?php _e( 'Data has been deleted successfully.', self::TEXT_DOMAIN ); ?>");
		}else{
			alert('Please select store to delete');
			return false;
		}
	});


	jQuery('#chkSelectAll').change(function() {
		var checkboxes = jQuery('#sloc_tStores tr.activePage').find(':checkbox');
		if(jQuery(this).is(':checked')) {
			checkboxes.attr('checked', 'checked');
			var checkboxe_chk = jQuery('#sloc_tStores tr.activePage').find(':checkbox:checked');
			if(checkboxe_chk.length > 0){
				jQuery('.deleteAll').show();
			}
		} else {
			checkboxes.removeAttr('checked');
			jQuery('.deleteAll').hide();
		}
	});

	jQuery('.chkSelectsin').on('change', function(){
		var checkboxes = jQuery('#sloc_tStores tr.activePage').find(':checkbox:checked');
		if(checkboxes.length > 0){
			jQuery('.deleteAll').show();
		}else{
			jQuery('.deleteAll').hide();
		}
	});

	var StoreCount = <?php echo $sl_stores_count; ?>;
	if(StoreCount > 0){
		jQuery('#sl_pagination_green').smartpaginator({ totalrecords: StoreCount, recordsperpage: 10, datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green', currentPageClass: 'activePage' });
	}
	else{
		jQuery('#sloc_tStores tbody').empty();
		var content = "";
		content +="<tr><td colspan='9'><p style='text-align:center'><strong><?php _e( 'There are no stores', self::TEXT_DOMAIN ); ?>..</strong></p></td></tr>";
		jQuery('#sloc_tStores tbody').append(content);
	}
	jQuery('a.sl_info-tooltip ').tooltip({
		track: true,
		delay: 0,
		fixPNG: true,
		showURL: false,
		showBody: " - ",
		top: -35,
		left: 5
	});
	//icon-5
	jQuery('.sl_icon-5').on("click",function() {
		window.location = "admin.php?page=add-new-location&storeId="+ jQuery(this).attr("rel");
    });
	//icon-2
	jQuery('.sl_icon-2').on("click",function() {
		$('#sloc_hdfStoreid').val(jQuery(this).attr("rel"));
            $.blockUI({ message: jQuery('#sloc_yesorno'), css: { width: '350px', border: '1px solid #aaa' } });
    });

	jQuery('#sloc_yes').click(function() {
		giz_Locator.util.ajxBlockUI("<?php _e( 'Deleting Data', self::TEXT_DOMAIN ); ?>...", true, "success", sl_plugin_url);
		var sl_manageloc_dal = { action: 'sl_dal_managelocation', Method: 'Delete', sloc_StoreId : jQuery('#sloc_hdfStoreid').val() }
		giz_Locator.util.ajax("admin-ajax.php","POST", sl_manageloc_dal, sl_ajResult, "<?php _e( 'Data has been deleted successfully.', self::TEXT_DOMAIN ); ?>");
	});

	jQuery('#sloc_no').click(function() {
			$.unblockUI();
			return false;
	});
	//tbSStore
	jQuery('#sloc_tbSStore').on("keyup",function() {
		jQuery('.deleteAll').hide();
		//Store
		var sl_manageloc_dal = { action: 'sl_dal_managelocation', Method: 'SearchSelect', sloc_Field : 'byAll', sloc_FieldVal : jQuery(this).val()  }
		$('#sloc_tStores').block({ message: '<h3 class="sloc_ajax_message_success"><img src="'+ sl_plugin_url +'images/loader.gif" /><span class="sloc_ajx_span"><?php _e( 'Loading', self::TEXT_DOMAIN ); ?>...</span></h3>' });
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_manageloc_dal, sl_ajReturnVal);
    });
	jQuery('#sloc_tbScity').on("keyup",function() {
		jQuery('.deleteAll').hide();
		var sl_manageloc_dal = { action: 'sl_dal_managelocation', Method: 'SearchSelect', sloc_Field : 'City', sloc_FieldVal : jQuery(this).val()  }
		$('#sloc_tStores').block({ message: '<h3 class="sloc_ajax_message_success"><img src="'+ sl_plugin_url +'images/loader.gif" /><span class="sloc_ajx_span"><?php _e( 'Loading', self::TEXT_DOMAIN ); ?>...</span></h3>' });
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_manageloc_dal, sl_ajReturnVal);
    });

	jQuery('#sloc_ddlShow').on('change', function(){
		var dispResult = jQuery.trim(jQuery(this).val());
		jQuery('#chkSelectAll').removeAttr('checked');
		jQuery('.deleteAll').hide();
		jQuery('#sloc_tStores input:checkbox').removeAttr('checked');
		if(dispResult == 0){
			$('#sl_pagination_green').smartpaginator({ totalrecords: jQuery('#sloc_tStores tbody tr').length, recordsperpage: parseInt(jQuery('#sloc_tStores tbody tr').length), datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green', currentPageClass: 'activePage' });
		}else{
			$('#sl_pagination_green').smartpaginator({ totalrecords: jQuery('#sloc_tStores tbody tr').length, recordsperpage: parseInt(dispResult), datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green', currentPageClass: 'activePage' });
		}
	});

});
function sl_ajResult(returnVal){
	if(returnVal == "1"){
		var sl_manageloc_dal = { action: 'sl_dal_managelocation', Method: 'Select' }
		giz_Locator.util.ajaxSer("admin-ajax.php","POST", sl_manageloc_dal, sl_ajReturnVal);
	}
}
function sl_ajReturnVal(returnVal){
	jQuery('#sloc_tStores').unblock();
	jQuery('#sloc_tStores tbody').empty();
	var content = "";
	for (var i = 0; i < returnVal.length; i++){
			cssClass 		= ( i+1 %2  ) ? "class='alternate-row'" : "";
			email 			= (returnVal[i]["email"] != null) ? returnVal[i]["email"] : "";
			phone			= (returnVal[i]["phone"] != null) ? returnVal[i]["phone"] : "";
			fax 			= (returnVal[i]["fax"] != null) ?  returnVal[i]["fax"] : "";
		  var password = 'Store ' + i + ' Locator Adv ' + i;
		  var plaintext = 'Locator to find store ' + i + '!';
		  var ciphertext = Aes.Ctr.encrypt(plaintext, password, 256);
			content += "<tr "+ cssClass +">"+
			"<td style='text-align:center; padding:0px;'><input type='checkbox' class='chkSelectsin' name='chkSelectAction[]' value=\""+ returnVal[i]["id"] +"\" /></td>"+
			"<td>" + returnVal[i]["iscustid"] + "</td>"+
			"<td>" + returnVal[i]["name"] + "</td>"+
			"<td>" + returnVal[i]["address"]+ "</td>"+
			"<td>" + returnVal[i]["city"] + "</td>"+
			"<td>" + returnVal[i]["country"] + "</td>"+
			"<td>" + email + "</td>"+
			"<td>" + phone + "</td>"+
			"<td>" + fax + "</td>"+
			"<td class='options-width' valign='middle' style='text-align:center;width: 75px;'>"+
				"<a href='javascript:void(0);' title='<?php _e( 'Edit', self::TEXT_DOMAIN ); ?>' rel=" + returnVal[i]["id"] + " class='sl_icon-5 sl_info-tooltip'></a>"+
				"<a href='javascript:void(0);' title='<?php _e( 'Delete', self::TEXT_DOMAIN ); ?>' rel=" + returnVal[i]["id"] + "  class='sl_icon-2 sl_info-tooltip'></a>"+
			"</td>"+
			"</tr>";

	}
	jQuery('#sloc_tStores tbody').append(content);
	if(returnVal.length > 0){
		jQuery('#sl_pagination_green').show();
		jQuery('#sl_pagination_green').smartpaginator({ totalrecords: returnVal.length, recordsperpage: 10, datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green', currentPageClass: 'activePage' });
		var showResult = jQuery.trim(jQuery('#sloc_ddlShow').val());

		if(showResult == ""){
			$('#sl_pagination_green').smartpaginator({ totalrecords: returnVal.length, recordsperpage: parseInt(returnVal.length), datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green', currentPageClass: 'activePage' });
		}else{
			$('#sl_pagination_green').smartpaginator({ totalrecords: returnVal.length, recordsperpage: parseInt(showResult), datacontainer: 'sloc_tStores', dataelement: 'tr', initval: 0, next: '<?php _e( 'Next', self::TEXT_DOMAIN ); ?>', prev: '<?php _e( 'Prev', self::TEXT_DOMAIN ); ?>', first: '<?php _e( 'First', self::TEXT_DOMAIN ); ?>', last: '<?php _e( 'Last', self::TEXT_DOMAIN ); ?>', theme: 'green', currentPageClass: 'activePage' });
		}
		jQuery('a.sl_info-tooltip ').tooltip({
			track: true,
			delay: 0,
			fixPNG: true,
			showURL: false,
			showBody: " - ",
			top: -35,
			left: 5
		});
	}
	else{
		jQuery('#sloc_tStores tbody').empty();
		content +="<tr><td colspan='9'><p style='text-align:center'><strong><?php _e( 'There are no stores', self::TEXT_DOMAIN ); ?>..</strong></p></td></tr>";
		jQuery('#sloc_tStores tbody').append(content);
		jQuery('#sl_pagination_green').hide();
	}
  }
</script>
	<div class="sl_menu_icon sloc_manageloc sl_icon32" ><br /></div>
	<h2 class="sl_menu_title"><?php _e( 'Manage Location', self::TEXT_DOMAIN ); ?></h2>
	<div class="clearb">
	<table border="0" width="100%" cellpadding="0" cellspacing="0" id="tsearch">
	<tr>
			<td colspan="4"><span class="bold"><?php _e( 'Search Location', self::TEXT_DOMAIN ); ?> &nbsp;&nbsp;&nbsp;</span> <input id="sloc_tbSStore" name="sloc_tbSStore" type="text" class="inp-form wd250" /> </td>
			<!--<td colspan="2" style="text-align:right"><span class="bold"><?php _e( 'Search By City', self::TEXT_DOMAIN ); ?> &nbsp;&nbsp;&nbsp;</span> <input id="sloc_tbScity" name="sloc_tbScity" type="text" class="inp-form" /> </td>-->
			<td colspan="4" style="text-align:right">
				   <span class="bold">Show &nbsp;&nbsp;&nbsp;</span>
				   <select name="sloc_ddlShow" id="sloc_ddlShow" class="sl_input_sel" style="width:60px;" >
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="">All</option>
				   </select>
			</td>

	</tr>
	</table>
	<table border="0" width="100%" cellpadding="0" cellspacing="0" id="sloc_tStores" class="sl_table">
		<thead>
			<tr>
				<th class="table-header-repeat line-left" style="width:30px; text-align:center;"><input type="checkbox" name="chkSelectAll" id="chkSelectAll" /></th>
				<th class="line-left minwidth-1"><span><?php _e( 'iscustid', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left minwidth-1"><span><?php _e( 'Store Name', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left minwidth-1"><span><?php _e( 'Address', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left"><span><?php _e( 'City', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left"><span><?php _e( 'Country', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left"><span><?php _e( 'Email', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left"><span><?php _e( 'Contact No.', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left"><span><?php _e( 'Fax', self::TEXT_DOMAIN ); ?></span></th>
				<th class="line-left"><span><?php _e( 'Options', self::TEXT_DOMAIN ); ?></span></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$row_count =0;
			foreach($sl_select_obj as $sl_store_rows){
				$row_count 		= $row_count +1;
				$cssClass 		= ( $row_count %2  ) ? "class='alternate-row'" : "";
				$key_r = random_strings(32, 32);
				echo "<tr ". $cssClass .">
				<td style='text-align:center; padding:0px;'><input type='checkbox' class='chkSelectsin' name='chkSelectAction[]' value=\"".$sl_store_rows->id."\" /></td>
				<td>".$sl_store_rows->iscustid."</td>
				<td>".$sl_store_rows->name."</td>
				<td>".$sl_store_rows->address."</td>
				<td>".$sl_store_rows->city."</td>
				<td>".$sl_store_rows->country."</td>
				<td>".$sl_store_rows->email."</td>
				<td>".$sl_store_rows->phone."</td>
				<td>".$sl_store_rows->fax."</td>
				<td class='options-width' valign='middle' style='text-align:center; width: 75px;'>
					<a href='javascript:void(0);' title='".__( 'Edit', self::TEXT_DOMAIN )."' rel='".$sl_store_rows->id."' class='sl_icon-5 sl_info-tooltip'></a>
					<a href='javascript:void(0);' title='".__( 'Delete', self::TEXT_DOMAIN )."' rel='".$sl_store_rows->id."'  class='sl_icon-2 sl_info-tooltip'></a>
				</td>
				</tr>";
			}
			?>
		</tbody>
	</table>
		<div id="sl_pagination_green" style="margin: 4px auto;">
		</div>
		<div class="clearb deleteAll" style="display:none; margin-top:10px;">
			<a id="btnDeleteSelect" class="sloc_btn_new" style="width:100px;"><span class="sloc_red"><?php _e( 'Delete', self::TEXT_DOMAIN ); ?></span></a>
		</div>
	</div>
	<!--  end table-content  -->

	<div class="clear"></div>
	<div id="sloc_yesorno" style="display:none; cursor: default; margin-bottom: 5px;">
        <h2 class="prompt-head"><?php _e( 'Are you sure you want delete this store?', self::TEXT_DOMAIN ); ?></h2>
		<input id="sloc_hdfStoreid" name="sloc_hdfStoreid" type="hidden" />
        <input type="button" id="sloc_yes" value="<?php _e( 'Yes', self::TEXT_DOMAIN ); ?>" class="btn btn-blue sl_wd40" />
        <input type="button" id="sloc_no" value="<?php _e( 'No', self::TEXT_DOMAIN ); ?>" class="btn btn-grey sl_wd40" />
	</div>

