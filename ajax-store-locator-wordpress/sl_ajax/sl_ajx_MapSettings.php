<?php

/*** cbf - callback function ******/
/********* Map settings **********/
function sl_dal_mapsettings_cbf(){
		global $wpdb;
		$sl_gizmo_store = new Gizmo_Store();		
		$sl_logo_table 			=  $sl_gizmo_store->sl_return_dbTable("STL");
		$sl_store_table 		=  $sl_gizmo_store->sl_return_dbTable("SRO");
		$sl_tb_mapsetting 		=  $sl_gizmo_store->sl_return_dbTable('MAS');
		$sl_tb_mapradius 		=  $sl_gizmo_store->sl_return_dbTable('MAR');
		$sl_tb_plugset	 		=  $sl_gizmo_store->sl_return_dbTable('PLS');
		$sl_tb_appsetting 		=  $sl_gizmo_store->sl_return_dbTable('APS');
		$sl_tb_appcharset 		=  $sl_gizmo_store->sl_return_dbTable('CHR');
		$sl_plugin_directory	=  $sl_gizmo_store->plugin_direc_path();
		
		if(isSet($_POST['FunType'])){
				$funType 			= 	trim($_POST['FunType']);		 
				if($funType == "RadiusSave"){
					$Rad = mysql_real_escape_string($_POST[SL_PREFIX.'Radius']);
					$sp_sql_query	="SELECT * FROM `$sl_tb_mapradius` WHERE `radius`= $Rad";
					$sp_results=$wpdb->get_results( $sp_sql_query);					
					$sql_num_rows 	= $wpdb->num_rows;		
					if($sql_num_rows == 0) {
						$sp_sql_query = "INSERT INTO `$sl_tb_mapradius` (`radius`) VALUES ($Rad);";
						if($wpdb->query($sp_sql_query)){				
							 echo '1';
						 }
						 else{
							 echo '0';
						 }
					}
					else{
						echo "1";
					}
				}
				else if($funType == "LoadLocation"){
					$sql_str = "SELECT * FROM `$sl_tb_mapsetting` LIMIT 0, 1";					
					$sl_select_obj = $wpdb->get_results( $sql_str );					
					echo json_encode($sl_select_obj);
				}
				else if($funType == "RadiusDelete"){
					$Rad 	= mysql_real_escape_string($_POST[SL_PREFIX.'Radius']);
					$sql_str = "DELETE FROM `$sl_tb_mapradius` WHERE `radius`= $Rad";					
					if($wpdb->query($sql_str)){
						echo '1';
					}
					else{
						echo '0';
					}
				}
				else if($funType == "RadiusSelect"){
					$sql_str = "SELECT * FROM `$sl_tb_mapradius` ORDER BY radius";
					$sl_select_obj = $wpdb->get_results( $sql_str );					
					echo json_encode($sl_select_obj);
				}
				else if($funType == "MapDefSelect"){
					$sql_str = "SELECT * FROM `$sl_tb_mapsetting` LIMIT 0, 1";
					$sl_select_obj = $wpdb->get_results( $sql_str );					
					echo json_encode($sl_select_obj);
				}
				else if($funType == "CatDelete"){
					$CatId 	= mysql_real_escape_string($_POST[SL_PREFIX.'CatId']);
					$sql_str = "DELETE FROM `storecategory` WHERE categoryid = $CatId";					
					if($wpdb->query($sql_str)){
						echo '1';
					}
					else{
						echo '0';
					}
				}
				else if($funType == "LogoCheck"){
					$LogoId 	= mysql_real_escape_string($_POST[SL_PREFIX.'logoId']);
					$sql_str = "SELECT * FROM `$sl_logo_table` WHERE logoid = $LogoId AND `default` ='1'";
					$sl_select_obj = $wpdb->get_results( $sql_str );
					$sql_rows = array();
					if($wpdb->num_rows > 0){
						$sql_rows = array("msg" => "D","resultCount" => $wpdb->num_rows);
						echo json_encode($sql_rows);
					}
					else{
						$sql_str = "SELECT * FROM `$sl_store_table` WHERE logoid = $LogoId";
						$sl_select_obj = $wpdb->get_results( $sql_str );
						
						if($wpdb->num_rows > 0){
							$sql_rows = array("msg" => "SC","resultCount" => $wpdb->num_rows);
							echo json_encode($sql_rows);
						}
						else{
							$sql_str 	= "SELECT * FROM `$sl_logo_table` WHERE logoid = $LogoId";					
							$sql_result = mysql_query($sql_str) or die(mysql_error());
							$sql_obj 	= mysql_fetch_object($sql_result);
							if(strlen($sql_obj->logopath) > 1){
								removeFile($sl_plugin_directory.$sql_obj->logopath);
							}					
							$sql_str = "DELETE FROM `$sl_logo_table` WHERE `logoid`= '$LogoId'";
							if($wpdb->query($sql_str)){
								$sql_rows = array("msg" => "SD","resultCount" => 1);
								echo json_encode($sql_rows);								
							}
							else{
								$sql_rows = array("msg" => "FA","resultCount" => 0);
								echo json_encode($sql_rows);
							}				
						}
					}
				}
				else if($funType == "LogoCheckDel"){
					$LogoId 	= mysql_real_escape_string($_POST[SL_PREFIX.'logoId']);			
					$sql_rows = array();
					$default_result = mysql_query("SELECT * FROM `$sl_logo_table` WHERE `default`= '1'") or die(mysql_error());
							$obj =  mysql_fetch_object($default_result);
							
							$sql_str 	= "SELECT * FROM `$sl_logo_table` WHERE logoid = $LogoId";					
							$sql_result = mysql_query($sql_str) or die(mysql_error());
							$sql_obj 	= mysql_fetch_object($sql_result);
							if(strlen($sql_obj->logopath) > 1){
								removeFile($sl_plugin_directory.$sql_obj->logopath);
							}
							
							$result = mysql_query("DELETE FROM `$sl_logo_table` WHERE `logoid`= '$LogoId'") or die(mysql_error());
							if($result){
								$result = mysql_query("UPDATE `stores` SET `logoid`='$obj->LogoId', `logotype` = 'D' WHERE logoid='$LogoId'") or die(mysql_error());
								if($result){
									$sql_rows = array("msg" => "SU","resultCount" => 1);
								}
								else{
									$sql_rows = array("msg" => "FA","resultCount" => 0);
								}
							}					
							echo json_encode($sql_rows); 
				}
				else if($funType == "CatCheck"){
					$CatId 	= mysql_real_escape_string($_POST[SL_PREFIX.'CatId']);			
					$sql_rows = array();
					$sql_str = "SELECT * FROM `stores` WHERE `type` = $CatId";
					$sl_select_obj = $wpdb->get_results( $sql_str );
					if($wpdb->num_rows > 0){
						$sql_rows = array("msg" => "SC","resultCount" => $sql_row);
						echo json_encode($sql_rows);
					}
					else{
						$sql_query = "DELETE FROM `storecategory` WHERE `categoryid`= '$CatId'";
						if($wpdb->query($sql_query)){
							$sql_rows = array("msg" => "SD","resultCount" => 1);
							echo json_encode($sql_rows);
						}
						else{
							$sql_rows = array("msg" => "FA","resultCount" => 0);
							echo json_encode($sql_rows);
						}				
					}
				}
				else if($funType == "CatCheckDel"){
					$CatId 	= mysql_real_escape_string($_POST[SL_PREFIX.'CatId']);			
					$sql_query = "DELETE FROM `storecategory` WHERE `categoryid`= '$CatId'";
					if($wpdb->query($sql_query)){
						$sql_query = "UPDATE `stores` SET `Type`='1' WHERE type='$CatId'";
						if($wpdb->query($sql_query)){
							echo "1";
						}
						else{
							echo "2";
						}
					}					 
				}
				else if($funType == "LogoSave"){
					$DefaultLogo	= trim($_POST[SL_PREFIX."DefaultLogo"]);
					$success_msg = __('Logo has been uploaded successfully', 'giz_store_locator');
					$error_msg = __('Error while uploading logo', 'giz_store_locator');
					if(isset($_FILES[SL_PREFIX."fuNewLogo"])){
								$image = new SimpleImage();   
							$filename = $_FILES[SL_PREFIX."fuNewLogo"]["name"];		
							$ext=findexts($filename);
							$newFileName = random_string( );
							move_uploaded_file($_FILES[SL_PREFIX."fuNewLogo"]["tmp_name"],SL_PLUGIN_PATH."Logo/" . $newFileName .".". $ext);
							$sourceDir = SL_PLUGIN_PATH.'Logo/';
							$targetDir = SL_PLUGIN_PATH.'Logo/';  			
							
							$image->load($sourceDir.$newFileName.".". $ext);
							
							$logoHeight	= 64;
							$logoWidth	= 64;
							$sql_str = "SELECT * FROM `$sl_tb_appsetting` LIMIT 0,1";
							$sl_select_obj = $wpdb->get_results( $sql_str );
							$sp_logosize = json_decode($sl_select_obj[0]->logosize);
							$logoHeight		= $sp_logosize[1];
							$logoWidth	= $sp_logosize[0];
							
							$image->resizeOr($logoWidth, $logoHeight);
							 $imageType;
							 $imgQuality;
							 switch($ext){
								case "jpeg" :
									case "jpg" :
									$imageType = IMAGETYPE_JPEG;
									$imgQuality = 75;
								break;
								case "gif" :
									$imageType = IMAGETYPE_GIF;
									$imgQuality = null;
								break;
								case "png" :
									$imageType = IMAGETYPE_PNG;
									$imgQuality = 9;
								break;
							 }
							$image->save($targetDir.$newFileName.".". $ext, $imageType, $imgQuality);
			  
							$Storepath="Logo/". $newFileName .".". $ext;
							if($DefaultLogo == '1'){
								$sql_qry ="UPDATE `$sl_logo_table` SET `default` = '0'";
								if($wpdb->query($sql_qry)){					
									$sql_qry ="INSERT INTO `$sl_logo_table`(`logopath`, `default`) VALUES('$Storepath', '1')";
									$query_result = $wpdb->query($sql_qry);
									$newLogoId = $wpdb->insert_id;
									if($query_result){
										$sql_qry ="UPDATE  `$sl_store_table` SET `logoid`='$newLogoId' WHERE logotype='D'";
										if($wpdb->query($sql_qry)){
											echo $success_msg.' - success';
										}
										else{
											echo $success_msg.' - success';
										}									
									}
									else{
										 echo $error_msg. ' - error';
									}
								}
								else{
									 echo $error_msg. ' - error';
								}
							}
							else if($DefaultLogo == '0'){
								$sql_qry = "INSERT INTO `$sl_logo_table`(`logopath`) VALUES('$Storepath')";
								if($wpdb->query($sql_qry)){
									echo $success_msg.' - success';
								}
								else{
									echo $success_msg.' - success';
								}
							}
					}
				}
				else if($funType == "LogoSelect"){
					$sl_logo_table =  $sl_gizmo_store->sl_return_dbTable("STL");
					$sql_str = "SELECT * FROM `$sl_logo_table`";
					$sl_select_obj = $wpdb->get_results( $sql_str );					
					echo json_encode($sl_select_obj);
				}
				else if($funType == "LogoDelete"){
					$logo 	= mysql_real_escape_string(trim($_POST[SL_PREFIX.'logoName']));
					$sql_str ="SELECT * FROM `$sl_logo_table` WHERE `logopath`= '$logo' AND `default`='1'";
					$sl_select_obj = $wpdb->get_results( $sql_str );
					$count_result = $wpdb->num_rows;					
					if($wpdb->num_rows > 0){
						$sql_qry = "DELETE FROM `$sl_logo_table` WHERE `logopath`= '$logo'";
						if($wpdb->query($sql_qry)){
							$sql_qry = "UPDATE `$sl_logo_table` SET `default`='1' ORDER BY logoid DESC LIMIT 1";
							if($wpdb->query($sql_qry)){
								echo '1';
							}
							else{
								echo '0';
							}
						}
					}
					else{
						$sql_qry = "DELETE FROM `$sl_logo_table` WHERE `logopath`= '$logo'";
						if($wpdb->query($sql_qry)){
							echo '1';
						}
						else{
							echo '0';
						}
					}
				}
				else if($funType =="MapSettingSave"){
					$funTypeT = $_POST['FunTypeT'];
					$mapSettingsId		= $_POST[SL_PREFIX.'mapId'];
					$ZoomLevel			= mysql_real_escape_string($_POST[SL_PREFIX.'Zoom']);
					$MapType 			= mysql_real_escape_string($_POST[SL_PREFIX.'MapType']);
					$ZoomControl		= mysql_real_escape_string($_POST[SL_PREFIX."ZoomControl"]);
					$PanControl			= mysql_real_escape_string($_POST[SL_PREFIX."PanControl"]);
					$StreetControl		= mysql_real_escape_string($_POST[SL_PREFIX."StreetControl"]);
					$ControlPosition	= mysql_real_escape_string($_POST[SL_PREFIX."CPosition"]);
					$Lat				= mysql_real_escape_string($_POST[SL_PREFIX.'Lat']);
					$Lng 				= mysql_real_escape_string($_POST[SL_PREFIX.'Lng']);
					$Address			= mysql_real_escape_string($_POST[SL_PREFIX.'Address']);
					$radiusType			= mysql_real_escape_string($_POST[SL_PREFIX.'radiusType']);
					$Radius				= mysql_real_escape_string($_POST[SL_PREFIX.'radius']);
					$CustomCity			= mysql_real_escape_string(trim($_POST[SL_PREFIX.'customCity']));
					$mapLang			= mysql_real_escape_string(trim($_POST[SL_PREFIX.'mapLang']));
					$mapAPIKey			= mysql_real_escape_string(trim($_POST[SL_PREFIX.'mapAPIKey']));
					
					$sp_setting_data = array(
						'id'=> null,
						'zoomlevel'=> $ZoomLevel,
						'maptype'=> $MapType,
						'radiustype'=> $radiusType,
						'radius'=> $Radius,
						'zoomcontrol'=> $ZoomControl,
						'pancontrol'=> $PanControl,
						'streetviewcontrol'=> $StreetControl,
						'controlposition'=> $ControlPosition,
						'lat'=> $Lat,
						'lng'=> $Lng,
						'address' => $Address,
						'customcity'=> $CustomCity,
						'storelogo'=> null,
						'map_language'=> $mapLang,
						'status'=> 1,
						'map_api_key' => $mapAPIKey
					);
					if(strlen($CustomCity) >= 4){
						$Address 	= "";
					}else{
						$CustomCity = "";
					}
					$sql_qry 			= "";
					$fBook				= $_POST[SL_PREFIX.'fBook'];
					$Tweet	 			= $_POST[SL_PREFIX.'Tweet'];
					$pinIt	 			= $_POST[SL_PREFIX.'pinIt'];
					$Storepath 			= "";
					
					if(isset($_FILES[SL_PREFIX."fuStoreLogo"])){
							$image = new SimpleImage();
							$filename = $_FILES[SL_PREFIX."fuStoreLogo"]["name"];		
							$ext=findexts($filename);
							$newFileName = random_string( );
							move_uploaded_file($_FILES[SL_PREFIX."fuStoreLogo"]["tmp_name"],SL_PLUGIN_PATH."Logo/" . $newFileName .".". $ext);
							$sourceDir = SL_PLUGIN_PATH.'Logo/';
							$targetDir = SL_PLUGIN_PATH.'Logo/';   					
								$image->load($sourceDir.$newFileName.".". $ext);
								$logoHeight	= 64;
								$logoWidth	= 64;
								
								$sql_str = "SELECT * FROM `$sl_tb_appsetting` LIMIT 0,1";
								$sl_select_obj 	= $wpdb->get_results( $sql_str );
								$sp_logosize 	= json_decode($$sl_select_obj[0]->logosize);
								$logoHeight		= $sp_logosize[1];
								$logoWidth		= $sp_logosize[0];
								
							$image->resizeOr($logoWidth, $logoHeight);
							 $imageType;
							 $imgQuality;
							 switch($ext){
								case "jpeg" :
									case "jpg" :
									$imageType = IMAGETYPE_JPEG;
									$imgQuality = 75;
								break;
								case "gif" :
									$imageType = IMAGETYPE_GIF;
									$imgQuality = null;
								break;
								case "png" :
									$imageType = IMAGETYPE_PNG;
									$imgQuality = 9;
								break;
							 }
							$image->save($targetDir.$newFileName.".". $ext, $imageType, $imgQuality);

							$Storepath="Logo/". $newFileName .".". $ext;
							$sql_qry ="UPDATE `$sl_logo_table` SET `default` = '0'";
							
							if($wpdb->query($sql_qry)){
								$sql_qry ="INSERT INTO `$sl_logo_table`(`logopath`, `default`) VALUES('$Storepath', '1')";
								if($wpdb->query($sql_qry)){
								}
							}
					}
					if($funTypeT == "Save"){
						$sql_qry = "INSERT INTO `$sl_tb_mapsetting`(`zoomlevel`, `maptype`, `zoomcontrol`, `pancontrol`, `controlposition`, `streetviewcontrol`, `lat`, `lng`, `address`, `radiustype`, `radius`, `customcity`, `map_language`, `map_api_key`) 
						VALUES('$ZoomLevel', '$MapType', '$ZoomControl', '$PanControl', '$ControlPosition', '$StreetControl', '$Lat', '$Lng', '$Address', '$radiusType', '$Radius', '$CustomCity', '$mapLang', '$mapAPIKey')";
					}
					else if($funTypeT == "Update"){
						$sql_qry = "UPDATE `$sl_tb_mapsetting` SET 
									`zoomlevel` 		= '$ZoomLevel', 
									`maptype` 			= '$MapType', 
									`zoomcontrol` 		= '$ZoomControl',
									`pancontrol` 		= '$PanControl', 
									`controlposition` 	= '$ControlPosition',
									`streetviewcontrol` = '$StreetControl',
									`lat` 				= '$Lat',
									`lng` 				= '$Lng', 
									`address` 			= '$Address', 
									`radiustype` 		= '$radiusType', 
									`radius` 			= '$Radius', 
									`customcity` 		= '$CustomCity', 
									`map_language` 		= '$mapLang',
									`map_api_key` 		= '$mapAPIKey'
						  WHERE id = '$mapSettingsId'";
					}
					else if($funType == "Delete"){						
					}
					$wpdb->query($sql_qry);
					//if($wpdb->query($sql_qry)){
						$sql_cqry 		= "SELECT * FROM $sl_tb_plugset";
						$sp_results		=	$wpdb->get_results( $sql_cqry);
						if($wpdb->num_rows <= 0){		
							$sql_qry ="INSERT INTO `$sl_tb_plugset`(`google`, `facebook`, `twitter`, `printrest`) VALUES ('0','$fBook','$Tweet','$pinIt')";
						}
						else{
							$sql_qry ="UPDATE `$sl_tb_plugset` SET `google`='0',`facebook`='$fBook',`twitter`='$Tweet',`printrest`='$pinIt'";
						}
						if($wpdb->query($sql_qry)){
							echo '1';
						}
						else{
							echo '1';
						}					 
				 }				 
				 else if($funType == "AppSettings"){						
						$SMode					= $_POST[SL_PREFIX.'ddlSMode'];
						$MPosition				= $_POST[SL_PREFIX.'ddlMPos'];
						$cbCategory				= $_POST[SL_PREFIX.'cbCategory'];	
						$cbLoadlocation			= $_POST[SL_PREFIX.'cbLoadLoc'];
						$cbLogoVisible			= $_POST[SL_PREFIX.'cbLogovi'];	
						$markerHeight			= trim($_POST[SL_PREFIX.'markerheight']);
						$markerWidth			= trim($_POST[SL_PREFIX.'markerwidth']);
						$logoHeight				= trim($_POST[SL_PREFIX.'logoheight']);
						$logoWidth				= trim($_POST[SL_PREFIX.'logowidth']);
						$langCharsetId			= trim($_POST[SL_PREFIX.'ddlCharSet']);
						
						$PerferLocation			= $_POST[SL_PREFIX.'ddlpLocations'];	
						$cbSingleCountry		= $_POST[SL_PREFIX.'cbSingleCountry'];
						$cbAppCss				= $_POST[SL_PREFIX.'ApplicationStyle'];
						
						
						$sql_str = "SELECT * FROM `$sl_tb_appcharset` WHERE encode_id=". $langCharsetId;
						$sl_select_obj = $wpdb->get_results( $sql_str );
						$langCharset = $sl_select_obj[0]->encode_value;
						
						if($markerHeight == 0 || $markerHeight == "")
							$markerHeight 	= 34;
						if($markerWidth == 0 || $markerWidth == "")
							$markerWidth 	= 21;
						if($logoHeight == 0 || $logoHeight == "")
							$logoHeight 	= 34;
						if($logoWidth == 0 || $logoWidth == "")
							$logoWidth 	= 21;
						$markerSize		= "[". $markerWidth.','. $markerHeight ."]";
						$logoSize		= "[". $logoHeight.','. $logoWidth ."]";
						
						$sql_cqry 		= "SELECT * FROM `$sl_tb_appsetting`";						
						$sp_results		= $wpdb->get_results($sql_cqry);
						
						if($wpdb->num_rows <= 0){
							$sql_qry = "INSERT INTO `$sl_tb_appsetting`( `searchmode`, `mapposition`, `category`, `markersize`, `logosize`, `charset_id`, `charset_value`, `load_location`, `logo_visible`, `preferred_country`, `enable_single_country`, `locator_css`) 
							VALUES ('$SMode', '$MPosition', '$cbCategory', '$markerSize', '$logoSize', '$langCharsetId', '$langCharset', '$cbLoadlocation', '$cbLogoVisible', '$PerferLocation', '$cbSingleCountry', '$cbAppCss')";
						}
						else{
						   $sql_qry = "UPDATE `$sl_tb_appsetting` SET `searchmode`	= '$SMode', 
							`mapposition` 			= '$MPosition', 
							`category`				= '$cbCategory', 
							`markersize`			= '$markerSize', 
							`logosize`				= '$logoSize', 
							`charset_id` 			= '$langCharsetId', 
							`charset_value` 		= '$langCharset', 
							`load_location` 		= '$cbLoadlocation',
							`logo_visible` 			= '$cbLogoVisible',
							`preferred_country`		= '$PerferLocation', 
							`enable_single_country`	= '$cbSingleCountry',
							`locator_css`			= '$cbAppCss'";
						}
						
						if($wpdb->query($sql_qry)){
								echo '1';
						}
						else{
						   echo '0';
						}
				  }
				  else if($funType == "SelectAppSettings"){	
						$sql_str = "SELECT * FROM `$sl_tb_appsetting` LIMIT 0,1";
						$sl_select_obj = $wpdb->get_results( $sql_str );
						echo json_encode($sl_select_obj);
				  }
				  else if($funType == "CheckCountry"){	
						$sql_str = "SELECT DISTINCT `country` FROM `$sl_store_table`";
						$sp_results		= $wpdb->get_results($sql_str);
						echo $wpdb->num_rows;
				  }
		}
		die();
}

function removeFile($filePath) {
	if(file_exists($filePath)){
		 unlink($filePath);
	}
}

?>
