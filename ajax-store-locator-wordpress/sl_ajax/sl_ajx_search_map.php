<?php

 /*** cbf - callback function ******/
/********* Search Location **********/
function sl_dal_searchlocation_cbf()
{
    if (isset($_REQUEST['funMethod'])) {
        global $wpdb;
        $sl_gizmo_store = new Gizmo_Store(); //first we get the database tables that will be used
        $sl_tb_appsetting = $sl_gizmo_store->sl_return_dbTable('APS');
        $sl_tb_stores = $sl_gizmo_store->sl_return_dbTable('SRO');
        $sl_tb_storecat = $sl_gizmo_store->sl_return_dbTable('STC');
        $sl_tb_storelogo = $sl_gizmo_store->sl_return_dbTable('STL');
        $sl_tb_markerimg = $sl_gizmo_store->sl_return_dbTable('MRI');
        $sl_tb_label = $sl_gizmo_store->sl_return_dbTable('LBL');
        $sl_tb_pluginset = $sl_gizmo_store->sl_return_dbTable('PLS');
        $sl_tb_mapset = $sl_gizmo_store->sl_return_dbTable('MAS');
        $sl_select_obj = $wpdb->get_results("SELECT * FROM `$sl_tb_appsetting` LIMIT 0 , 1");
        $isSingleCountry = 0;
        $countryName_a = '';
        $preferCountry = '';
        foreach ($sl_select_obj as $sl_appset_row) {
            $isSingleCountry = $sl_appset_row->enable_single_country;
            $preferCountry = $sl_appset_row->preferred_country;
        }
        if ($isSingleCountry == 1) {
            $sql_str = "SELECT DISTINCT `country` FROM `$sl_tb_stores`";
            $sl_select_cobj = $wpdb->get_results($sql_str);
            foreach ($sl_select_cobj as $sl_country) {
                $countryName_a = $sl_country->country;
            }
        }

        $funMethod = $_REQUEST['funMethod'];
        if ($funMethod == 'LoadMapAndSet') {
            $location = $_REQUEST['Location'];
            $selectedVal = $_REQUEST['selValue'];
            $selectedVal = ($selectedVal == 'View') ? '' : $_REQUEST['selValue'];
            $countryName = trim($_POST['Country_name']);
            // Select all the rows in the markers table
            if (isset($location)) {
                $catStr = '';
                if ($_REQUEST['CateId'] == '0') {
                    $catStr = '';
                } else {
                    $catStr = ' AND a.`type`='.$_REQUEST['CateId'];
                }
                if ($location == 'All') {
                    if ($_REQUEST['CateId'] == '0') {
                        $catStr = '';
                    } else {
                        $catStr = ' WHERE a.`Type`='.$_REQUEST['CateId'];
                    }
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) ".$catStr;
                } elseif ($location == 'country') {
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `country` = '$selectedVal' ".$catStr;
                } elseif ($location == 'State') {
                    $countryName = ($isSingleCountry == 1) ? $countryName_a : $countryName;
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `state` = '$selectedVal' AND `country` = '$countryName' ".$catStr;
                } elseif ($location == 'City') {
                    $countryName = ($isSingleCountry == 1) ? $countryName_a : $countryName;
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `city` = '$selectedVal'  AND `country` = '$countryName' ".$catStr;
                }
            } else {
                $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 )";
            }
            $sl_select_obj = $wpdb->get_results($sql_query);
            if ($location == 'State' && count($sl_select_obj) <= 0) {
                $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , ', ', a.`state` , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `country` = '$selectedVal' ".$catStr;
                $sl_select_obj = $wpdb->get_results($sql_query);
            }
            echo json_encode($sl_select_obj);
        } elseif ($funMethod == 'LoadWithoutQuery') {
            $sql_query = "SELECT * FROM `$sl_tb_mapset` LIMIT 0, 1";
            $sl_select_obj = $wpdb->get_results($sql_query);
            echo json_encode($sl_select_obj);
        } elseif ($funMethod == 'SearchStore') {
            $location = $_REQUEST['Location'];
            $storeLoc = $_REQUEST['StoreLocation'];
            if (isset($location)) {
                if ($location == 'Load') {
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 )";
                } elseif ($location == 'Social') {
                    $qryVal = explode('~', $storeLoc);
                    $sql_query = "SELECT a.*,b.*, 0 as distance, m.markerpath AS CategoryIcon, c.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` as a INNER JOIN `$sl_tb_storelogo` as c ON c.logoid=a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` LEFT JOIN `$sl_tb_pluginset` as b ON (1=1)  WHERE a.id=$qryVal[1]";
                } else {
                    $qryVal = explode('~', $location);
                    $CatQry = '';
                    if ($qryVal[3] == '0') {
                        $CatQry = '';
                    } else {
                        $cats = explode(',', $qryVal[3]);
                        $CatQry = " AND a.`type` in ('".implode("','", $cats)."')";
                    }
                    if (isset($qryVal[4]) && $qryVal[4] != '') {
                        $qryVal[4] = str_replace('usa', 'united states', str_replace('', '', trim(strtolower($qryVal[4]))));
                        //						$qryVal[4]=preg_replace("/^usa$|^$/i","united states",trim(strtolower($qryVal[4])));
                        $cntrs = explode(',', $qryVal[4]);
                        if (in_array('united states', $cntrs)) {
                            $cntrs[] = '';
                        }
                        if (sizeof($cntrs) > 0) {
                            foreach ($cntrs as $i => $cntr) {
                                $cntrs[$i] = "'".$cntr."'";
                            }
                            $CatQry .= ' AND lcase(a.`country`) in ('.implode(',', $cntrs).')';
                        }
                    }
                    if (trim($qryVal[5]) != '') {
                        $qryVal[5] = addslashes($qryVal[5]);
                    }

                    if (trim($qryVal[6]) != '') {
                        if ($CatQry != '') {
                            $CatQry .= ' and ';
                        }
                        $CatQry .= " name like '%".trim($qryVal[6])."%'";

                        $sql_query = "SELECT a.*, b.*,( 6371 * acos( cos( radians($qryVal[0]) ) * cos( radians( `lat` ) ) * cos( radians( `lng` ) - radians($qryVal[1]) ) + sin( radians($qryVal[0]) ) * sin(radians( `lat` ) ) ) ) AS distance, m.markerpath AS CategoryIcon, sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` ='' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` as a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT JOIN `$sl_tb_pluginset` as b ON (1=1) HAVING  (1=1) ".$CatQry.' ORDER BY distance ASC';
                    } else {
                        $sql_query = "SELECT a.*, b.*,( 6371 * acos( cos( radians($qryVal[0]) ) * cos( radians( `lat` ) ) * cos( radians( `lng` ) - radians($qryVal[1]) ) + sin( radians($qryVal[0]) ) * sin(radians( `lat` ) ) ) ) AS distance, m.markerpath AS CategoryIcon, sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` ='' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl`  FROM `$sl_tb_stores` as a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT JOIN `$sl_tb_pluginset` as b ON (1=1) HAVING (distance <= $qryVal[2] or zip_code='$qryVal[5]') ".$CatQry.' ORDER BY distance ASC';
                    }
                }
            } else {
                $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon, sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl`, a.adwords_id, a.mapping_tag  FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE defaultloc = '1'";
            }
            $sl_select_obj = $wpdb->get_results($sql_query);

            if (strtolower($qryVal[7]) == 'true') {
                $ai = crm2map_get_importer();
                if ($funMethod == 'SearchStore') {
                    $al = [];
                    foreach ($sl_select_obj as $i => $obj) {
                        if ($obj->adwords_id != '') {
                            //						$obj->adwords_id='3963238515';
                                if (!in_array($obj->adwords_id, $al) && !in_array($obj->type, [4, 5, 6, 7, 8])) { //no adwords for x-clients
                                    $al[] = $obj->adwords_id;
                                    $sl_select_obj[$i]->procedures = array_values($ai->getProcedures($obj->adwords_id)); //$obj->adwords_id;
                                    $ai->getLocations($obj->adwords_id);
                                }
                        }
                    }
                }
            }

            echo json_encode($sl_select_obj);
        } elseif ($funMethod == 'BrowseList') {
            $CatStr = '';
            $countryName = trim($_POST['Country_name']);
            if ($_POST['CateId'] == '0') {
                $CatStr = '';
            } else {
                $CatStr = " AND `type` ='".$_POST['CateId']."'";
            }
            if ($_POST['SelectMet'] == 'country') {
                //For preferred country
                $sql_str = '';
                if ($isSingleCountry == 0 && strlen($preferCountry) > 0 && strlen(trim($_POST['selVal'])) <= 0) {
                    $sql_str = "(SELECT DISTINCT `country`, 1 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` = '$preferCountry') UNION (SELECT DISTINCT `country`, 2 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` <> '$preferCountry') ORDER BY order_a,`country`";
                } elseif ($isSingleCountry == 1 && strlen(trim($_POST['selVal'])) <= 0) {
                    $sql_str = "SELECT DISTINCT IF(`state` = '' OR `state` IS NULL, `country`, `state` ) AS state FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr.' ORDER BY `state` ASC';
                } else {
                    $sql_str = "SELECT DISTINCT `country` FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr.' ORDER BY `country` ASC';
                    if (strlen(trim($_POST['selVal'])) > 0) {
                        $sql_str = "SELECT DISTINCT IF(`state` = '' OR `state` IS NULL, `country`, `state` ) AS state FROM `$sl_tb_stores` WHERE `country` ='".$_POST['selVal']."' ".$CatStr.' ORDER BY `state` ASC';
                    }
                }
                $sl_select_obj = $wpdb->get_results($sql_str);
                echo json_encode($sl_select_obj);
            } elseif ($_POST['SelectMet'] == 'State') {
                $selected = $_POST['selVal'];

                $sql_str = "SELECT DISTINCT `city` FROM `$sl_tb_stores` WHERE `state` ='$selected' ".$CatStr;
                $sql_dresult = mysql_query($sql_str) or die(mysql_error());
                if (mysql_num_rows($sql_dresult) > 0) {
                    $sql_str = "SELECT DISTINCT `city` FROM `$sl_tb_stores` WHERE `state` ='$selected' ".$CatStr.' ORDER BY `city` ASC';
                } else {
                    $sql_str = "SELECT DISTINCT `city` FROM `$sl_tb_stores` WHERE `country` ='$selected' ".$CatStr.' ORDER BY `city` ASC';
                }
                $sl_select_obj = $wpdb->get_results($sql_str);
                echo json_encode($sl_select_obj);
            } elseif ($_POST['SelectMet'] == 'country') {
                if ($_POST['CateId'] == '0') {
                    $CatStr = '';
                } else {
                    $CatStr = " WHERE `type` ='".$_POST['CateId']."'";
                }
                $sql_str = '';
                if ($isSingleCountry == 0 && strlen($preferCountry) > 0) {
                    $sql_str = "(SELECT DISTINCT `country`, 1 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` = '$preferCountry') UNION (SELECT DISTINCT `country`, 2 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` <> '$preferCountry') ORDER BY order_a,`country`";
                } elseif ($isSingleCountry == 1) {
                    $sql_str = "SELECT DISTINCT IF(`state` = '' OR `state` IS NULL, `country`, `state` ) AS state FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr.' ORDER BY `state` ASC';
                } else {
                    $sql_str = "SELECT DISTINCT `country` FROM `$sl_tb_stores` ".$CatStr.' ORDER BY `country` ASC';
                }
                $sl_select_obj = $wpdb->get_results($sql_str);
                echo json_encode($sl_select_obj);
            }
        }
    }
    die();
}

/********* Search Location **********/
function sl_dal_searchname_cbf()
{
    //			echo "in";exit;

    if (isset($_REQUEST['funMethod'])) {
        global $wpdb;
        $sl_gizmo_store = new Gizmo_Store();
        $sl_tb_appsetting = $sl_gizmo_store->sl_return_dbTable('APS');
        $sl_tb_stores = $sl_gizmo_store->sl_return_dbTable('SRO');
        $sl_tb_storecat = $sl_gizmo_store->sl_return_dbTable('STC');
        $sl_tb_storelogo = $sl_gizmo_store->sl_return_dbTable('STL');
        $sl_tb_markerimg = $sl_gizmo_store->sl_return_dbTable('MRI');
        $sl_tb_label = $sl_gizmo_store->sl_return_dbTable('LBL');
        $sl_tb_pluginset = $sl_gizmo_store->sl_return_dbTable('PLS');
        $sl_tb_mapset = $sl_gizmo_store->sl_return_dbTable('MAS');
        $sl_select_obj = $wpdb->get_results("SELECT * FROM `$sl_tb_appsetting` LIMIT 0 , 1");
        $isSingleCountry = 0;
        $countryName_a = '';
        $preferCountry = '';
        foreach ($sl_select_obj as $sl_appset_row) {
            $isSingleCountry = $sl_appset_row->enable_single_country;
            $preferCountry = $sl_appset_row->preferred_country;
        }
        if ($isSingleCountry == 1) {
            $sql_str = "SELECT DISTINCT `country` FROM `$sl_tb_stores`";
            $sl_select_cobj = $wpdb->get_results($sql_str);
            foreach ($sl_select_cobj as $sl_country) {
                $countryName_a = $sl_country->country;
            }
        }

        $funMethod = $_REQUEST['funMethod'];
        if ($funMethod == 'LoadMapAndSet') {
            $location = $_REQUEST['Location'];
            $selectedVal = $_REQUEST['selValue'];
            $selectedVal = ($selectedVal == 'View') ? '' : $_REQUEST['selValue'];
            $countryName = trim($_POST['Country_name']);
            // Select all the rows in the markers table
            if (isset($location)) {
                $catStr = '';
                if ($_REQUEST['CateId'] == '0') {
                    $catStr = '';
                } else {
                    $catStr = ' AND a.`type`='.$_REQUEST['CateId'];
                }
                if ($location == 'All') {
                    if ($_REQUEST['CateId'] == '0') {
                        $catStr = '';
                    } else {
                        $catStr = ' WHERE a.`Type`='.$_REQUEST['CateId'];
                    }
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) ".$catStr;
                } elseif ($location == 'country') {
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `country` = '$selectedVal' ".$catStr;
                } elseif ($location == 'State') {
                    $countryName = ($isSingleCountry == 1) ? $countryName_a : $countryName;
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `state` = '$selectedVal' AND `country` = '$countryName' ".$catStr;
                } elseif ($location == 'City') {
                    $countryName = ($isSingleCountry == 1) ? $countryName_a : $countryName;
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `city` = '$selectedVal'  AND `country` = '$countryName' ".$catStr;
                }
            } else {
                $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 )";
            }
            $sl_select_obj = $wpdb->get_results($sql_query);
            if ($location == 'State' && count($sl_select_obj) <= 0) {
                $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , ', ', a.`state` , ', ', a.`country` , IF( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE `country` = '$selectedVal' ".$catStr;
                $sl_select_obj = $wpdb->get_results($sql_query);
            }
            echo json_encode($sl_select_obj);
        } elseif ($funMethod == 'LoadWithoutQuery') {
            $sql_query = "SELECT * FROM `$sl_tb_mapset` LIMIT 0, 1";
            $sl_select_obj = $wpdb->get_results($sql_query);
            echo json_encode($sl_select_obj);
        } elseif ($funMethod == 'SearchStore') {
            $location = $_REQUEST['Location'];
            $storeLoc = $_REQUEST['StoreLocation'];
            if (isset($location)) {
                if ($location == 'Load') {
                    $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon,sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 )";
                } elseif ($location == 'Social') {
                    $qryVal = explode('~', $storeLoc);
                    $sql_query = "SELECT a.*,b.*, 0 as distance, m.markerpath AS CategoryIcon, c.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` as a INNER JOIN `$sl_tb_storelogo` as c ON c.logoid=a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` LEFT JOIN `$sl_tb_pluginset` as b ON (1=1)  WHERE a.id=$qryVal[1]";
                } else {
                    $qryVal = explode('~', $location);
                    //					print_r($qryVal);exit;
                    $CatQry = '';
                    if ($qryVal[3] == '0') {
                        $CatQry = '';
                    } else {
                        $cats = explode(',', $qryVal[3]);
                        $CatQry = " AND a.`type` in ('".implode("','", $cats)."')";

                        //						$CatQry = " AND a.`type` = '".$qryVal[3]."'";
                    }
                    if (isset($qryVal[4]) && $qryVal[4] != '') {
                        $qryVal[4] = str_replace('usa', 'united states', str_replace(' ', '', strtolower($qryVal[4])));
                        $cntrs = explode(',', $qryVal[4]);
                        if (sizeof($cntrs) > 0) {
                            foreach ($cntrs as $i => $cntr) {
                                $cntrs[$i] = "'".$cntr."'";
                            }
                            $CatQry .= ' AND lcase(a.`country`) in ('.implode(',', $cntrs).')';

                            //								print_r($CatQry );exit;
                        }
                    }
                    if (trim($qryVal[5]) != '') {
                        $qryVal[5] = addslashes($qryVal[5]);
                    }
                    print_r($CatQry);
                    exit;

                    $sql_query = "SELECT a.*, b.*,( 6371 * acos( cos( radians($qryVal[0]) ) * cos( radians( `lat` ) ) * cos( radians( `lng` ) - radians($qryVal[1]) ) + sin( radians($qryVal[0]) ) * sin(radians( `lat` ) ) ) ) AS distance, m.markerpath AS CategoryIcon, sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` ='' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl` FROM `$sl_tb_stores` as a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT JOIN `$sl_tb_pluginset` as b ON (1=1) HAVING (distance <= $qryVal[2] or zip_code='$qryVal[5]') ".$CatQry.' ORDER BY distance ASC';
                    echo $sql_query;
                    exit;
                }
            } else {
                $sql_query = "SELECT a . * , b . * , 0 AS distance, m.markerpath AS CategoryIcon, sl.logopath, CONCAT( a.`address` , ', ', a.`city` , IF(a.`state` ='', '', CONCAT( ', ', a.`state` )) , ', ', a.`country` , if( a.`zip_code` = '' , '', CONCAT( ', ', a.`zip_code` ) ) ) AS FullAddress1, a.marketing_office AS FullAddress, L.`imgurl`  FROM `$sl_tb_stores` AS a INNER JOIN `$sl_tb_storecat` AS sc ON sc.`categoryid` = a.`type` INNER JOIN `$sl_tb_markerimg` AS m ON sc.`markerid` = m.`markerid` INNER JOIN `$sl_tb_storelogo` AS sl ON sl.logoid = a.logoid INNER JOIN `$sl_tb_label` AS L ON L.`labelid` = a.`labelid` LEFT OUTER JOIN `$sl_tb_pluginset` AS b ON ( 1 =1 ) WHERE defaultloc = '1'";
            }
            $sl_select_obj = $wpdb->get_results($sql_query);
            echo json_encode($sl_select_obj);
        } elseif ($funMethod == 'BrowseList') {
            $CatStr = '';
            $countryName = trim($_POST['Country_name']);
            if ($_POST['CateId'] == '0') {
                $CatStr = '';
            } else {
                $CatStr = " AND `type` ='".$_POST['CateId']."'";
            }
            if ($_POST['SelectMet'] == 'country') {
                //For preferred country
                $sql_str = '';
                if ($isSingleCountry == 0 && strlen($preferCountry) > 0 && strlen(trim($_POST['selVal'])) <= 0) {
                    $sql_str = "(SELECT DISTINCT `country`, 1 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` = '$preferCountry') UNION (SELECT DISTINCT `country`, 2 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` <> '$preferCountry') ORDER BY order_a,`country`";
                } elseif ($isSingleCountry == 1 && strlen(trim($_POST['selVal'])) <= 0) {
                    $sql_str = "SELECT DISTINCT IF(`state` = '' OR `state` IS NULL, `country`, `state` ) AS state FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr.' ORDER BY `state` ASC';
                } else {
                    $sql_str = "SELECT DISTINCT `country` FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr.' ORDER BY `country` ASC';
                    if (strlen(trim($_POST['selVal'])) > 0) {
                        $sql_str = "SELECT DISTINCT IF(`state` = '' OR `state` IS NULL, `country`, `state` ) AS state FROM `$sl_tb_stores` WHERE `country` ='".$_POST['selVal']."' ".$CatStr.' ORDER BY `state` ASC';
                    }
                }
                $sl_select_obj = $wpdb->get_results($sql_str);
                echo json_encode($sl_select_obj);
            } elseif ($_POST['SelectMet'] == 'State') {
                $selected = $_POST['selVal'];

                $sql_str = "SELECT DISTINCT `city` FROM `$sl_tb_stores` WHERE `state` ='$selected' ".$CatStr;
                $sql_dresult = mysql_query($sql_str) or die(mysql_error());
                if (mysql_num_rows($sql_dresult) > 0) {
                    $sql_str = "SELECT DISTINCT `city` FROM `$sl_tb_stores` WHERE `state` ='$selected' ".$CatStr.' ORDER BY `city` ASC';
                } else {
                    $sql_str = "SELECT DISTINCT `city` FROM `$sl_tb_stores` WHERE `country` ='$selected' ".$CatStr.' ORDER BY `city` ASC';
                }
                $sl_select_obj = $wpdb->get_results($sql_str);
                echo json_encode($sl_select_obj);
            } elseif ($_POST['SelectMet'] == 'country') {
                if ($_POST['CateId'] == '0') {
                    $CatStr = '';
                } else {
                    $CatStr = " WHERE `type` ='".$_POST['CateId']."'";
                }
                $sql_str = '';
                if ($isSingleCountry == 0 && strlen($preferCountry) > 0) {
                    $sql_str = "(SELECT DISTINCT `country`, 1 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` = '$preferCountry') UNION (SELECT DISTINCT `country`, 2 as order_a FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr." AND `country` <> '$preferCountry') ORDER BY order_a,`country`";
                } elseif ($isSingleCountry == 1) {
                    $sql_str = "SELECT DISTINCT IF(`state` = '' OR `state` IS NULL, `country`, `state` ) AS state FROM `$sl_tb_stores` WHERE (1=1) ".$CatStr.' ORDER BY `state` ASC';
                } else {
                    $sql_str = "SELECT DISTINCT `country` FROM `$sl_tb_stores` ".$CatStr.' ORDER BY `country` ASC';
                }
                $sl_select_obj = $wpdb->get_results($sql_str);
                echo json_encode($sl_select_obj);
            }
        }
    }
    die();
}
