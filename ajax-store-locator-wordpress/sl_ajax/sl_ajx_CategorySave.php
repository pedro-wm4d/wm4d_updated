<?php
include SL_PLUGIN_PATH."sl_admin_utility.php"; 
include SL_PLUGIN_PATH."sl_admin_img-resize.php";

/*** cbf - callback function ******/
function sl_dal_category_cbf(){
	global $wpdb;
	$sl_gizmo_store = new Gizmo_Store();
	$sl_mark_name 			=  $sl_gizmo_store->sl_return_dbTable("MRI");
	$sl_cat_name 			=  $sl_gizmo_store->sl_return_dbTable("STC");
	$sl_store_name 			=  $sl_gizmo_store->sl_return_dbTable("SRO");
	$sl_tb_appsetting 		=  $sl_gizmo_store->sl_return_dbTable('APS');
	$sl_plugin_directory	=  $sl_gizmo_store->plugin_direc_path();
	if(isset($_POST['FunMethod']))
	{
		if($_POST['FunMethod'] == "Save"){
				$categoryName 		=  $_POST[SL_PREFIX.'CatName'];
				$filename 		=   "";
				$path			= $_POST[SL_PREFIX.'fileLoc'];
				if(isset($_FILES[SL_PREFIX."fuCatIcon"])){
					$image = new SimpleImage();
					$filename = $_FILES[SL_PREFIX."fuCatIcon"]["name"];		
					$ext=findexts($filename);
					$newFileName = random_string( );
					move_uploaded_file($_FILES[SL_PREFIX."fuCatIcon"]["tmp_name"],SL_PLUGIN_PATH."/marker/" . $newFileName .".". $ext);
						$sourceDir = SL_PLUGIN_PATH.'marker/';   					
						$image->load($sourceDir.$newFileName.".". $ext);
						$markerHeight	= 50;
						$markerWidth	= 38;
						$image->resizeOr($markerWidth, $markerHeight);
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
								$imgQuality = 55;
							break;
						 }
						$image->save($sourceDir.$newFileName.".". $ext, $imageType, $imgQuality); 			
						$path="marker/". $newFileName .".". $ext;
				}
				$sql_qry ="INSERT INTO `storecategory`(`category`, `categoryicon`) VALUES ('$categoryName', '$path')";
				$sql_result = mysql_query($sql_qry) or die(mysql_error());
				if($sql_result)
				{
					echo '1';
				}
				else
				{
					echo '0';
				}
		}
		else if($_POST['FunMethod'] == "MarkerSave"){
				$filename 		= "";
				$path			= "";
				if(isset($_FILES[SL_PREFIX."fuMarkerIcon"])){
					$image 			= new SimpleImage();
					$filename 		= $_FILES[SL_PREFIX."fuMarkerIcon"]["name"];		
					$ext			= findexts($filename);
					$newFileName 	= random_string( );
					move_uploaded_file($_FILES[SL_PREFIX."fuMarkerIcon"]["tmp_name"],SL_PLUGIN_PATH."marker/" . $newFileName .".". $ext);
						$sourceDir = SL_PLUGIN_PATH.'marker/';   					
							$image->load($sourceDir.$newFileName.".". $ext);
						$markerHeight	= 34;
						$markerWidth	= 21;
						$sql_str = "SELECT * FROM `$sl_tb_appsetting` LIMIT 0,1";
						$sl_select_obj 	= $wpdb->get_results( $sql_str);
						
						$sp_markersize = json_decode($sl_select_obj[0]->markersize);
						$markerHeight	= $sp_markersize[1];
						$markerWidth	= $sp_markersize[0];
						
						$image->resizeOr($markerWidth, $markerHeight);
						 $imageType;
						 $imgQuality;
						 switch($ext){
							case "jpeg" :
								case "jpg" :
								$imageType = IMAGETYPE_JPEG;
								$imgQuality = 100;
							break;
							case "gif" :
								$imageType = IMAGETYPE_GIF;
								$imgQuality = null;
							break;
							case "png" :
								$imageType = IMAGETYPE_PNG;
								$imgQuality = 100;
							break;
						 }
						$image->save($sourceDir.$newFileName.".". $ext, $imageType, $imgQuality); 			
						$path="marker/". $newFileName .".". $ext;
				}
				$Modifiedate = date('Y-m-d H:i:s', time());
				$sql_qry ="";				
				$sql_cqry  		= "SELECT * FROM `$sl_mark_name` WHERE `default` = '1'";
				$sl_select_obj 	= $wpdb->get_results( $sql_cqry);
				if($wpdb->num_rows > 0){
					$sql_qry ="INSERT INTO `$sl_mark_name`(`markerpath`, `createddate`) VALUES ('$path', '$Modifiedate')";
				}else{
					$sql_qry ="INSERT INTO `$sl_mark_name`(`markerpath`, `createddate`, `default`) VALUES ('$path', '$Modifiedate', '1')";
				}
				if($wpdb->query($sql_qry)){
					echo '1';
				}
				else{
					echo '0';
				}
		}
		else if($_POST['FunMethod'] == "MarkSelect"){
			$sql_str = "SELECT `markerid`, `markerpath`, `default` FROM `$sl_mark_name` ORDER BY `createddate` DESC";			
			$sl_select_obj = $wpdb->get_results( $sql_str );		
			echo json_encode($sl_select_obj);
		}
		else if($_POST['FunMethod'] == "Select"){			
			$sql_str = "SELECT c.`categoryid`, c.`category`, M.`markerpath` as categoryicon, c.`isdefault` FROM `$sl_cat_name` AS c INNER JOIN `$sl_mark_name` AS M ON M.`markerid` = c.`markerid` ORDER BY c.`createddate` DESC";
			$sl_select_obj = $wpdb->get_results( $sql_str );			
			echo json_encode($sl_select_obj);
		}
		else if($_POST['FunMethod'] == "CategorySelect"){
			$sql_str = "SELECT * FROM `$sl_cat_name` ORDER BY `createddate` DESC";
			$sl_select_obj = $wpdb->get_results( $sql_str );			
			echo json_encode($sl_select_obj);
		}	
		else if($_POST['FunMethod'] == "SetDefaultCat"){
				$query="UPDATE `$sl_cat_name` SET `isdefault`= '0' WHERE `isdefault`='1'"; 
				$result = mysql_query($query) or die(); 
				if($result){
					$sql_str = "UPDATE `$sl_cat_name` SET `isdefault` = '1' WHERE `categoryid` = ". $_POST[SL_PREFIX.'CategoryId'];
					$sql_result = mysql_query($sql_str) or die(mysql_error());
					if($sql_result){
						echo "1";
					}
					else{
						echo "0";
					}
				}
				else{
					echo "0";
				}
		}
		else if($_POST['FunMethod'] == "MarkerRemove"){
			if($_POST[SL_PREFIX.'ConfDel'] == "No"){
				$sql_str = "SELECT * FROM `$sl_mark_name` WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId']. " AND `default` = '1'";
				$sl_select_obj = $wpdb->get_results( $sql_str );
				$my_numresult = $wpdb->num_rows;
				if($my_numresult > 0){
					echo "DF";
				}else{
					$sql_str = "SELECT * FROM `$sl_cat_name` WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId'];					
					$sl_select_obj = $wpdb->get_results( $sql_str );
					$my_numresult = $wpdb->num_rows;
					if($my_numresult > 0){
						echo "ST". "-". $my_numresult;
					}else{
						$sql_str 	= "SELECT * FROM `$sl_mark_name` WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId'];					
						$sql_result = mysql_query($sql_str) or die(mysql_error());
						$sql_obj 	= mysql_fetch_object($sql_result);
						if(strlen($sql_obj->markerpath) > 1){
							removeFile($sl_plugin_directory.$sql_obj->markerpath);
						}
						$sql_str = "DELETE FROM `$sl_mark_name` WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId'];
						$sql_result = mysql_query($sql_str) or die(mysql_error());
						if($sql_result){
							echo "DL";
						}
					}
				}
			}else if($_POST[SL_PREFIX.'ConfDel'] == "Yes"){
				$query="SELECT `markerid` FROM `$sl_mark_name` WHERE `default`='1' LIMIT 1"; 
				$sl_select_obj = $wpdb->get_results( $query );
				$markerId = $sl_select_obj[0]->markerid;
				$sql_str = "UPDATE `$sl_cat_name` SET `markerid` = ". $markerId . " WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId'];
				$sql_result = mysql_query($sql_str) or die(mysql_error());
				if($sql_result){
					$sql_str 	= "SELECT * FROM `$sl_mark_name` WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId'];					
					$sql_result = mysql_query($sql_str) or die(mysql_error());
					$sql_obj 	= mysql_fetch_object($sql_result);
					if(strlen($sql_obj->markerpath) > 1){
						removeFile($sl_plugin_directory.$sql_obj->markerpath);
					}
					$sql_str = "DELETE FROM `$sl_mark_name` WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId'];
					$sql_result = mysql_query($sql_str) or die(mysql_error());
					if($sql_result){
						echo "DL";
					}else{
						echo "DL-0";
					}
				}
			}
		}
		else if($_POST['FunMethod'] == "DefaultMarker"){
			if($_POST[SL_PREFIX.'ConfDef'] == "No"){
				$sql_str = "SELECT * FROM `$sl_mark_name` WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId']. " AND `default` = '1'";
				$sl_select_obj = $wpdb->get_results( $sql_str );
				$count_result = $wpdb->num_rows;
				if($count_result > 0){
					echo "DF";
				}else{
					echo "DFN";
				}
			}else if($_POST[SL_PREFIX.'ConfDef'] == "Yes"){
				$query="SELECT `markerid` FROM `$sl_mark_name` WHERE `default`='1' LIMIT 1"; 				
				$sl_select_obj = $wpdb->get_results( $query );
				$markerId = $sl_select_obj[0]->markerid;
				$sql_str = "UPDATE `$sl_cat_name` SET `markerid` = ". $_POST[SL_PREFIX.'MarkerId']. " WHERE `markerid` = ". $markerId;
				if($wpdb->query($sql_str)){
					$sql_str = "UPDATE `$sl_mark_name` SET `default` = '0'";
					if($wpdb->query($sql_str)){
						$sql_str = "UPDATE `$sl_mark_name` SET `default` = '1' WHERE `markerid` =". $_POST[SL_PREFIX.'MarkerId'];
						if($wpdb->query($sql_str)){
							echo "DFS";
						}else{
							echo "NDF";
						}
					}else{
						echo "NDF";
					}
				}
				else{
						echo "NDF";
				}
			}
		}
		else if($_POST['FunMethod'] == "CategoryDelete"){			
			if($_POST[SL_PREFIX.'ConfDel'] == "No"){
				$sql_str = "SELECT * FROM `$sl_store_name` WHERE `type` =". $_POST[SL_PREFIX.'CategoryId'];
				$sql_result = mysql_query($sql_str) or die(mysql_error());
				$my_numresult 	= mysql_num_rows($sql_result);
				if($my_numresult > 0){
					echo "ST-". $my_numresult;
				}else{
					$sql_str = "DELETE FROM `$sl_cat_name` WHERE `categoryid` =". $_POST[SL_PREFIX.'CategoryId'];
					$sql_result = mysql_query($sql_str) or die(mysql_error());
					if($sql_result){
						echo "SCD";
					}
					else{
						echo "STN";
					}
				}
			}else if($_POST[SL_PREFIX.'ConfDel'] == "Yes"){
				$query="SELECT `CategoryId` FROM `$sl_cat_name` WHERE `isdefault`='1' LIMIT 1"; 
				$result = mysql_query($query) or die(); 
				$row = mysql_fetch_object($result);
				$CatId = $row->CategoryId;
				$sql_str = "UPDATE `$sl_store_name` SET `type` = ". $CatId . " WHERE `type` = ". $_POST[SL_PREFIX.'CategoryId'];
				$sql_result = mysql_query($sql_str) or die(mysql_error());
				if($sql_result){
					$sql_str = "DELETE FROM `$sl_cat_name` WHERE `categoryid` = ". $_POST[SL_PREFIX.'CategoryId'];
					$sql_result = mysql_query($sql_str) or die(mysql_error());
					if($sql_result){
						echo "SCD";
					}else{
						echo "STN";
					}
				}
				else{
						echo "STN";
				}
			}
		}
		else if($_POST['FunMethod'] == "GetMarker"){
			$array = dirImages(SL_PLUGIN_PATH.'marker/');
			if(count($array) > 0){
					$html = "";
							foreach ($array as $key => $image)
							{
								$html .= "<li><a href='javascript:void(0)' class='mapImg'>".
								"<img rel= 'marker/$image' src='marker/$image' /></a>";
							}				
					  echo $html;		
			}
			else{
				$html = "<li><a href='javascript:void(0)' class='mapImg'>No Marker Available. </a>";				
				 echo $html;
			}
		}
		else if($_POST['FunMethod'] == "CategorySave"){
			$markerId 		= trim($_POST[SL_PREFIX."MarkerId"]);
			$catId 			= trim($_POST[SL_PREFIX."CategoryId"]);
			$catName 		= esc_attr(trim($_POST[SL_PREFIX."CategoryName"]));
			$DefCategory  	= trim($_POST[SL_PREFIX."DefCat"]);
			$sql_str = "";
			$Modifiedate = date('Y-m-d H:i:s', time());
			if($catId != "0"){
				if($DefCategory == "1"){
						$sql_str = "UPDATE `$sl_cat_name` SET `isdefault` = '0' WHERE `isdefault` = '1'";
						$sql_result 	= mysql_query($sql_str) or die(mysql_error());
						if($sql_result){
							$sql_str = "UPDATE `$sl_cat_name` SET 
								`markerid` = '$markerId'
								 ,`category` = '$catName'
								 ,`updateddate` = '$Modifiedate'
								 ,`isdefault` = '1' WHERE `categoryid` =". $catId;
						}
				}
				else{
					$sql_str = "SELECT * FROM `$sl_cat_name` WHERE `isdefault` = '1' AND `categoryid`= ". $catId;
					$sp_results		=	$wpdb->get_results( $sql_str);
					if($wpdb->num_rows > 0){	
							echo "DF";
							exit;
					}else{
							$sql_str = "UPDATE `$sl_cat_name` SET 
								`markerid` = '$markerId'
								 ,`category` = '$catName'
								 ,`updateddate` = '$Modifiedate'
								 WHERE `categoryid` =". $catId;
					}
				}
				$sql_result = mysql_query($sql_str) or die(mysql_error());
				if($sql_result){
					echo "1";
				}else{
					echo "0";
				}
			}else{
				if($_POST[SL_PREFIX."CnfSv"] == "No"){
					$sql_str = "SELECT * FROM `$sl_cat_name` WHERE `category` = '". $catName ."'";
					$sp_results		=	$wpdb->get_results( $sql_str);
					if($wpdb->num_rows > 0){	
						echo "EX";
					}else{
						$sql_cqry  		= "SELECT * FROM `$sl_cat_name` WHERE `isdefault` = '1'";
						$sp_results		=	$wpdb->get_results( $sql_cqry);
						if($wpdb->num_rows <= 0){	
							$sql_str = "INSERT INTO `$sl_cat_name`(`markerid`, `category`, `createddate`, `isdefault`) VALUES ('$markerId', '$catName', '$Modifiedate', '1')";
						}else{
							if($DefCategory == "1"){
								$sql_str = "UPDATE `$sl_cat_name` SET `isdefault` = '0' WHERE `isdefault` = '1'";
								$sql_result 	= mysql_query($sql_str) or die(mysql_error());
								if($sql_result){
									$sql_str = "INSERT INTO `$sl_cat_name`( `markerid`, `category`, `createddate`, `isdefault`) VALUES ('$markerId', '$catName', '$Modifiedate', '1')";
								}else{
									echo "0";
								}
							}else{
								$sql_str = "INSERT INTO `$sl_cat_name`( `markerid`, `category`, `createddate`) VALUES ('$markerId', '$catName', '$Modifiedate')";
							}
						}
						$sql_result = mysql_query($sql_str) or die(mysql_error());
						if($sql_result){
							echo "1";
						}else{
							echo "0";
						}
					}
				}else{
					$sql_cqry  		= "SELECT * FROM `$sl_cat_name` WHERE `isdefault` = '1'";
					$sp_results		=	$wpdb->get_results( $sql_cqry);
					if($wpdb->num_rows <= 0){	
						$sql_str = "INSERT INTO `$sl_cat_name`(`markerid`, `category`, `createddate`, `isdefault`) VALUES ('$markerId', '$catName', '$Modifiedate', '1')";
					}else{
						if($DefCategory == "1"){
							$sql_str = "UPDATE `$sl_cat_name` SET `isdefault` = '0' WHERE `isdefault` = '1'";
							$sql_result 	= mysql_query($sql_str) or die(mysql_error());
							if($sql_result){
								$sql_str = "INSERT INTO `$sl_cat_name`( `markerid`, `category`, `createddate`, `isdefault`) VALUES ('$markerId', '$catName', '$Modifiedate', '1')";
							}else{
								echo "0";
							}
						}else{
							$sql_str = "INSERT INTO `$sl_cat_name`( `markerid`, `category`, `createddate`) VALUES ('$markerId', '$catName', '$Modifiedate')";
						}
					}
					$sql_result = mysql_query($sql_str) or die(mysql_error());
					if($sql_result){
						echo "1";
					}else{
						echo "0";
					}
				}
				
			}
		}

	}
	die();
}

function dirImages($dir) {
	$d = dir($dir); //Open Directory
	while (false!== ($file = $d->read())) //Reads Directory
	{
		$extension = substr($file, strrpos($file, '.')); // Gets the File Extension
		if($extension == ".jpg" || $extension == ".jpeg" || $extension == ".gif" |$extension == ".png") // Extensions Allowed
		$images[$file] = $file; // Store in Array
	}
	$d->close(); // Close Directory
	asort($images); // Sorts the Array

	return $images; //Author: ActiveMill.com
}

?>
