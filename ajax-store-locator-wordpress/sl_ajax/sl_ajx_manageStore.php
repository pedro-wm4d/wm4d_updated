<?php
/*** cbf - callback function ******/
/********* Manage Location **********/
function sl_dal_managelocation_cbf(){
	if(isSet($_POST['Method'])){
		global $wpdb;
		$sl_gizmo_store = new Gizmo_Store();
		$sl_tb_stores 		= $sl_gizmo_store->sl_return_dbTable('SRO');
		if($_POST['Method'] == "Delete"){
				$StoreId 			= 	$_POST[SL_PREFIX.'StoreId'];
				$sql_qry ="DELETE FROM `$sl_tb_stores` WHERE `id` = '$StoreId'";
				if($wpdb->query($sql_qry)){
				echo "1";
				}
				else{
				   echo '0';
				}
		}else if($_POST['Method'] == "bulk_delete"){
				$arrStoreId 	= $_POST["sloc_StoreIds"];
				$successCount 			= 0;
				foreach ($arrStoreId as &$value){
					$StoreId 	= $value;
					$sql_qry ="DELETE FROM `$sl_tb_stores` WHERE `id` = '$StoreId'";
					if($wpdb->query($sql_qry)){
						$successCount++;
					}else{
						$successCount--;
					}
				}
				if($successCount > 0){
					echo "1";
				}
				else{
				   echo '0';
			   }
		}else if($_POST['Method'] == "Select"){
				$sql_str = "SELECT * FROM `$sl_tb_stores`";
				$sl_select_obj = $wpdb->get_results( $sql_str );
				echo json_encode($sl_select_obj);
		}else if($_POST['Method'] == "SearchSelect"){
			if($_POST[SL_PREFIX.'Field'] == "Store"){
				$Field = $_POST[SL_PREFIX.'FieldVal'];
				$sql_str = "SELECT * FROM `$sl_tb_stores` WHERE name like '$Field%'";
				$sl_select_obj = $wpdb->get_results( $sql_str );
				echo json_encode($sl_select_obj);
			}
			else if($_POST[SL_PREFIX.'Field'] == "City"){
				$Field = $_POST[SL_PREFIX.'FieldVal'];
				$sql_str = "SELECT * FROM `$sl_tb_stores` WHERE city like '$Field%'";
				$sl_select_obj = $wpdb->get_results( $sql_str );
				echo json_encode($sl_select_obj);
			}else if($_POST[SL_PREFIX.'Field'] == "byAll"){
				$Field = $_POST[SL_PREFIX.'FieldVal'];
				$sql_str = "SELECT * FROM `$sl_tb_stores` WHERE city like '%$Field%' OR name like '%$Field%' OR state like '%$Field%' OR country like '%$Field%' OR zip_code like '%$Field%' OR address like '%$Field%' OR iscustid like '%$Field%' OR email like '%$Field%'";
				$sl_select_obj = $wpdb->get_results( $sql_str );
				echo json_encode($sl_select_obj);
			}
		}
	}
	die();
}
function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir . "/" . $object) == "dir")
					rrmdir($dir . "/" . $object); else
					unlink($dir . "/" . $object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

?>