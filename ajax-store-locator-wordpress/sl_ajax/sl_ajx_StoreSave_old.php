<?php

/*** cbf - callback function ******/
/********* Map settings **********/
function sl_dal_storesave_cbf(){
	global $wpdb;
	$sl_gizmo_store = new Gizmo_Store();		
	$sl_store_table =  $sl_gizmo_store->sl_return_dbTable("SRO");
	if(isSet($_POST[SL_PREFIX.'tbName'])){
			$storeName 	= 	$_POST[SL_PREFIX.'tbName'];
			$Category	= 	trim($_POST[SL_PREFIX.'CatId']);
			$Address 	= 	$_POST[SL_PREFIX.'tbAddress'];
			$Lat		=	$_POST[SL_PREFIX.'tbLat'];
			$Lng 		=	$_POST[SL_PREFIX.'tbLng'];
			$City		=	$_POST[SL_PREFIX.'tbCity'];
			$State 		=	$_POST[SL_PREFIX.'tbState'];
			$Country	=	$_POST[SL_PREFIX.'tbCountry'];
			$Zip 		=	$_POST[SL_PREFIX.'tbZip'];
			$Contact	=	$_POST[SL_PREFIX.'tbPhone'];
			$Fax 		=	$_POST[SL_PREFIX.'tbFax'];
			$Email		=	$_POST[SL_PREFIX.'tbEmail'];
			$Web 		=	$_POST[SL_PREFIX.'tbWeb'];
			$LabelId 	=	trim($_POST[SL_PREFIX.'hdfLabelId']);
			$LabelText 	=	$_POST[SL_PREFIX.'tbLabelTxt'];
			$filename 	= "";
			$path_id	= $sql_qry = "";
			$storeId	= trim($_POST[SL_PREFIX.'storeId']);
			if($LabelId == "0" || $LabelId == "")
				$LabelId = "1";
			if(isset($_FILES[SL_PREFIX."fileLogo"])){
				$filename = $_FILES[SL_PREFIX."fileLogo"]["name"];		
				$ext=findexts($filename);
				$newFileName = random_string( );
				move_uploaded_file($_FILES[SL_PREFIX."fileLogo"]["tmp_name"],SL_PLUGIN_PATH."Logo/" . $newFileName .".". $ext); 			
				$path="Logo/". $newFileName .".". $ext;
			}
			$path_id 	= trim($_POST[SL_PREFIX.'hdfLogoAdd']);
			$LogoType 	= trim($_POST[SL_PREFIX.'hdfLogoType']);
			if($LogoType == 'Default'){
				$LogoType ='D';	
			}else{
				$LogoType ='S';
			}			
			if($storeId == "0"){
					$sql_qry ="INSERT INTO `$sl_store_table`(`name`, `address`, `lat`, `lng`, `city`, `state`, `country`, `zip_code`, `phone`, `fax`, `email`, `website`, `type`, `logoid`, `logotype`, `labelid`, `labeltext`)". 
			"VALUES ('$storeName', '$Address', '$Lat', '$Lng', '$City', '$State', '$Country', '$Zip', '$Contact', '$Fax', '$Email', '$Web', '$Category','$path_id', '$LogoType', '$LabelId', '$LabelText')";
				
			}
			else{
				$nPath = (!empty($path_id)) ? " ,`LogoId` ='". $path_id ."'" : "''";
				$sql_qry = "UPDATE `$sl_store_table` SET `name`= '$storeName', `address`= '$Address', `lat`= '$Lat', `lng`= '$Lng', `city`= '$City', `state`= '$State', `country`= '$Country', `zip_code`= '$Zip', `phone`= '$Contact', `fax`= '$Fax', `email`= '$Email', `website`= '$Web', `type`= '$Category',`logoid` ='$path_id',`logotype`= '$LogoType', `labelid` = '$LabelId', `labeltext` = '$LabelText' WHERE `id` = $storeId";
			}
			$sql_result = mysql_query($sql_qry) or die(mysql_error());
			$success_msg = __('Data has been saved successfully.', 'giz_store_locator');
			$error_msg = __('Error while saving data.', 'giz_store_locator');
			if($sql_result){
				echo $success_msg.' - success';
			}
			else{
				echo $error_msg.' - error';
			}
	}
	die();
}

?>
