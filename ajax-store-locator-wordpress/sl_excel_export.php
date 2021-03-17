<?php
	global $wpdb;
	$sl_tb_stores 				= $wpdb->prefix.'stores';
	$sl_tb_storecategory 		= $sl_gizmo_store->sl_return_dbTable('STC');	
	
	require SL_PLUGIN_PATH. 'Classes/PHPExcel.php';
	require_once SL_PLUGIN_PATH. 'Classes/PHPExcel/IOFactory.php';

	
	$filedir = SL_PLUGIN_PATH. "xcel_export/*";
	$fileName = "Stores_".date('d-m-M', time()).".xls";
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->setTitle('Location Data');
?>