<?php
/*** cbf - callback function ******/
/********* Import Location **********/
function sl_dal_locationimport_cbf(){
		global $wpdb;
		$sl_gizmo_store = new Gizmo_Store();
		$sl_tb_store 			=  $sl_gizmo_store->sl_return_dbTable("SRO");
		$sl_tb_importdata 		=  $sl_gizmo_store->sl_return_dbTable("IOD");
		$sl_tb_storelogo 		=  $sl_gizmo_store->sl_return_dbTable("STL");
		$sl_tb_storecat 		=  $sl_gizmo_store->sl_return_dbTable("STC");
		$sl_tb_appsetting 		=  $sl_gizmo_store->sl_return_dbTable("APS");
		$sl_tb_mapsetting 		=  $sl_gizmo_store->sl_return_dbTable("MAS");
		
	if(isset($_POST["Funtype"])){
		$funType = $_POST["Funtype"];
		if($funType == "Import_Location"){
			$LatLngCount = 0;
			$UnknowCount = 0;
			$filename 		= $_FILES[SL_PREFIX."fuStoreData"]["name"];		
			$ext			= findexts($filename);
			$newFileName 	= random_string( );
			$filepath		= SL_PLUGIN_PATH.'sl_import/'.$newFileName.".". $ext;	
			$mooved 		= move_uploaded_file($_FILES[SL_PREFIX."fuStoreData"]["tmp_name"],$filepath);	
			$Storepath 		= "sl_import/". $newFileName .".". $ext;
			$UserId			= $_POST[SL_PREFIX.'hdfUserId'];			
			require SL_PLUGIN_PATH. 'Classes/PHPExcel.php';
			require_once SL_PLUGIN_PATH. 'Classes/PHPExcel/IOFactory.php';
			
			
			$sql_enstr = "SELECT * FROM `$sl_tb_appsetting` LIMIT 0,1";
			$sl_select_obj 	= $wpdb->get_results($sql_enstr);
						
			$sp_charset 		= $sl_select_obj[0]->charset_value;
			
			$sql_mapstr = "SELECT * FROM `$sl_tb_mapsetting` LIMIT 0,1";
			$sl_select_mobj 	= $wpdb->get_results($sql_mapstr);
			
			$api_key			= $sl_select_mobj[0]->map_api_key;
			
			$api_key_script	 	= (strlen($sl_select_mobj[0]->map_api_key) > 0) ? "&key=". $sl_select_mobj[0]->map_api_key  : "";
			if($sp_charset == ""){
				$sp_charset = "CPa25a";
			}
			
			$DefaultLogo = '';
			$sql_qryLogo = "SELECT `logoid` FROM `$sl_tb_storelogo` WHERE `default`='1'";
			$sl_select_obj 	= $wpdb->get_results($sql_qryLogo);			
			$DefaultLogoId = $sl_select_obj[0]->logoid;
			
			$importedDate = date('Y-m-d H:i:s', time());
			$LastId = 0 ;
			$sql_qry = "INSERT INTO `$sl_tb_importdata`(`filename`, `filepath`, `totalrecord`, `latlngrecord`, `importeddate`, `userid`) VALUES ('$newFileName .".". $ext', '$Storepath', 0, 0, '$importedDate', '$UserId')";
			if($wpdb->query($sql_qry)){
				$LastId = $wpdb->insert_id;
			}	
			
			$catCount = 0;
			$funCat   = 0;
			$locCount = 0;
			$SavedCount = 0;	
			$totRecord = 0;			
			$headArray = array("Name", "Address", "City", "State", "Country", "Zip_code", "Phone", "Fax", "Email", "Website", "Category", "Status");
			
			$LoadStores =  array();
			$ValArray = array();
			$RfileName = "sloc_Location_Report.xls";
			
			$objPHPExcel = PHPExcel_IOFactory::load($filepath);
			$sheet_count = 0;
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				if($sheet_count == 0){
					$worksheetTitle     = $worksheet->getTitle();
					$highestRow         = $worksheet->getHighestRow(); // e.g. 10
					$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					$nrColumns = ord($highestColumn) - 64;			
					$totRecord = $highestRow - 7;
					for ($row = 8; $row <= $highestRow; ++ $row) {					
						/*for ($col = 0; $col < $highestColumnIndex; ++ $col) {
							$cell = $worksheet->getCellByColumnAndRow($col, $row);
							$val = $cell->getValue();
							$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
							echo '<td>' . $val . '</td>';
						}*/
						
						$storeName 		= ( $worksheet->getCellByColumnAndRow(0, $row)->getValue() != "" )  ? mysql_real_escape_string( trim($worksheet->getCellByColumnAndRow(0, $row)->getValue()) ) : "";
						$storeAdd 		= ( $worksheet->getCellByColumnAndRow(1, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(1, $row)->getValue()) : "";     
						$storeCity 		= ( $worksheet->getCellByColumnAndRow(2, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(2, $row)->getValue() ) : "";
						$storeState 	= ( $worksheet->getCellByColumnAndRow(3, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(3, $row)->getValue() ) : "";
						$storeCoun 		= ( $worksheet->getCellByColumnAndRow(4, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(4, $row)->getValue() ) : "";
						$storeZip 		= ( $worksheet->getCellByColumnAndRow(5, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(5, $row)->getValue() ) : "";
						$storePhone 	= ( $worksheet->getCellByColumnAndRow(6, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(6, $row)->getValue() ) : "";
						$storeFax 		= ( $worksheet->getCellByColumnAndRow(7, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(7, $row)->getValue() ) : "";
						$storeEmail 	= ( $worksheet->getCellByColumnAndRow(8, $row)->getValue() != "" )  ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(8, $row)->getValue() ) : "";
						$storeWeb 		= ( $worksheet->getCellByColumnAndRow(9, $row)->getValue() != "" ) ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(9, $row)->getValue() ) : "";
						$storeCat 		= ( $worksheet->getCellByColumnAndRow(10, $row)->getValue() != "" ) ? mysql_real_escape_string( $worksheet->getCellByColumnAndRow(10, $row)->getValue() ) : "";				
						$fullAddr		= $storeAdd. ',' .$storeCity. ','.$storeCoun;
						
						$coords = sloc_geolocate($fullAddr, $api_key_script);
						usleep(1000000);
						if($coords['stat'] == 'OK'){
							$LatLngCount = $LatLngCount + 1;
						}
						else{
							$UnknowCount = $UnknowCount + 1;
						}
						$cateId  = 0;
						$sql_query="SELECT `categoryid` FROM `$sl_tb_storecat` WHERE `category`='". $storeCat ."' LIMIT 1"; 
						$sl_select_obj 	= $wpdb->get_results( $sql_query);
						$my_numresult 	= $wpdb->num_rows;
						if($my_numresult > 0 && $coords['stat'] == 'OK'){					
							$cateId = $sl_select_obj[0]->categoryid;		
							$sql_insert = "INSERT INTO `$sl_tb_store`
							(`name`, `address`, `lat`, `lng`, `city`, `state`, `country`, `zip_code`, `phone`, `fax`, `email`, `website`, `type`, `logoid`, `logotype`, `labelid`, `labeltext`) 
							VALUES ('".$storeName."', '".$storeAdd."', '".$coords['lat']."', '".$coords['lng']."', '".$storeCity."', '".$storeState."', '".$storeCoun."', '$storeZip', '$storePhone', '$storeFax', '$storeEmail', '$storeWeb', '$cateId', '$DefaultLogoId', 'D', '1', '')";
							if($wpdb->query($sql_insert)){
								$SavedCount++;
							}
						}
						else{
							$ValArray = array();
							if($coords['stat'] != 'OK' && $my_numresult != 0){
								for($j=0; $j<=10; $j++){
									array_push($ValArray, ( $worksheet->getCellByColumnAndRow($j, $row)->getValue() != "" ) ? $worksheet->getCellByColumnAndRow($j, $row)->getValue() : "");
								}
								array_push($ValArray, "Unable to find the address location in google map. Please check the address.");				
								array_push($LoadStores, $ValArray);	
								$locCount++;
							}else if($coords['stat'] == 'OK' && $my_numresult == 0){				
								for($j=0; $j<=10; $j++){
									array_push($ValArray, ( $worksheet->getCellByColumnAndRow($j, $row)->getValue() != "" ) ? $worksheet->getCellByColumnAndRow($j, $row)->getValue() : "");
								}
								array_push($ValArray, "Please check your store category. Category not exist in database.");
								array_push($LoadStores, $ValArray);
								$funCat++;
							}
							else{
								for($j=0; $j<=10; $j++){
									array_push($ValArray, ( $worksheet->getCellByColumnAndRow($j, $row)->getValue() != "" ) ? $worksheet->getCellByColumnAndRow($j, $row)->getValue() : "");
								}
								array_push($ValArray, "Address and Category not found.");
								array_push($LoadStores, $ValArray);
							}
							$catCount++;
						}
					}		
				}
				$sheet_count++;
			}
			
			$fileName = "sloc_Location_Report".date('d-m-M-h-i-s', time()).".xls";
			
			$objPHPExcel_wr = new PHPExcel();
			$objPHPExcel_wr->getActiveSheet()->setTitle('Location Import - Error Report');			
			
			$instrString = "Instructions:- \n";
			$instrString .= "1) Do Not Add or remove columns. \n";
			$instrString .= "2) Fill all the fields marked as mandatory. \n";
			$instrString .= "3) Do not change the Excel Sheet name. \n";
			$instrString .= "4) Check the error message from the status column and correct it. \n";
			$instrString .= "5) Once you are made the correction again upload this file. \n";
			$instrString .= "\n \n";
			
			
			$rowNumber = 1; 
			$col = 'A'; 			
			$objPHPExcel_wr->getActiveSheet()->setCellValue("A1",$instrString); 	
			$objPHPExcel_wr->getActiveSheet()->getStyle("A1")->getFont()->setBold(true); 
			$objPHPExcel_wr->getActiveSheet()->getStyle("A1")->getFont()->setUnderline(false); 
			$objPHPExcel_wr->getActiveSheet()->getStyle("A1")->getAlignment()->setWrapText(true);

			$objPHPExcel_wr->getActiveSheet()->getStyle('A1')->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'ffff00')						
					)
				)
			);			

			
		   $objPHPExcel_wr->getActiveSheet()->mergeCells('A1:L6');
		   
		   $rowNumber = 7; 
		   $col = 'A'; 
		   foreach($headArray as $heading) { 
			   $objPHPExcel_wr->getActiveSheet()->setCellValue($col.$rowNumber,$heading); 	
			   $objPHPExcel_wr->getActiveSheet()->getStyle($col.$rowNumber)->getFont()->setBold(true);		   
			   $objPHPExcel_wr->getActiveSheet()->getStyle($col.$rowNumber)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '008000')						
						),
						'font' => array(
							'color' => array(
								'rgb' => 'ffffff'
							)
						)
					)
				);
			   $col++; 
		   }
			
			
		   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel_wr, 'Excel5'); 	

		   $sql_update = "UPDATE `$sl_tb_importdata` SET `totalrecord`= $totRecord, `latlngrecord`= $LatLngCount WHERE `fileid` = $LastId";
		   if($wpdb->query($sql_update)){				
				$arr = $LoadStores;
				$rowNumber = 8; 
				foreach($arr as $rows){
					$col = 'A';
					foreach($rows as $new_val_array){				
						$objPHPExcel_wr->getActiveSheet()->setCellValue($col.$rowNumber,$new_val_array); 
						$col++; 
					}					 
					$rowNumber++;
				}
				if($rowNumber > 8){
					$objWriter->save(SL_PLUGIN_PATH."xcel_import_result/".$fileName); 
				}
				$sl_result_xls_url = $sl_gizmo_store->plugin_url(). '/xcel_import_result/'.$fileName;
				$ctStr = ($catCount > 1) ? "records" : "record";
				$locStr = ($UnknowCount > 0) ? "<p>Location Not Found : <span style='font-weight:bold'>" . $UnknowCount ."</span></p>" : "";
				$catStr = ($funCat > 0) ? "<p style='color:red'>Category Missing 	: <span style='font-weight:bold'>" . $funCat ."</span></p>" : "";
				$downStr = ($locCount > 0 || $funCat > 0) ? "<p><a href='$sl_result_xls_url' target='_blank' >Download Report</a></p>" : "";
				echo "<p>Total Record 			: <span style='font-weight:bold'>" . $totRecord ."</span></p>".
					"<p>Uploaded Record 		: <span style='font-weight:bold'>" . $SavedCount ."</span></p>". $locStr . $catStr. $downStr;
		   }				
		}
	}
	die();
}

function sloc_geolocate($address, $api_key_script){
		$lat 	= 0;
		$lng 	= 0;
		$stat 	= "";		
		$sp_url = "http://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false" . $api_key_script;
		$ch 	= curl_init();
		curl_setopt($ch, CURLOPT_URL, $sp_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

		set_time_limit(90);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response);
		if($response_a != null){
			if ($response_a->status=="OK") {
				 $lat = $response_a->results[0]->geometry->location->lat;
				 $lng = $response_a->results[0]->geometry->location->lng;
				 $stat = "OK";
			}else{
				$lat = 0.0;
				$lng = 0.0;
				$stat = "NO";
			}
		}else{
				$lat = 0.0;
				$lng = 0.0;
				$stat = "NO";
		}
		// concatenate lat/long coordinates
		$coords['lat'] = $lat;
		$coords['lng'] = $lng;
		$coords['stat']	= $stat;
		
		return $coords;
}

?>
