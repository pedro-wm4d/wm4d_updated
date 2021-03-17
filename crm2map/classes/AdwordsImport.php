<?php

/**
 * Created by PhpStorm.
 * Author: sd4travel@gmail.com
 * Date: 19.10.2017
 * Time: 16:27
 */
class AdwordsImport
{
    function getProcedures($customer)
    {
//        $customer = '3963238515';
        $tme=time()-86400;
        $sd=date("Ymd",$tme-30*86400);
        $ed=date("Ymd",$tme);
        $params=array('customer'=>$customer,'start' => $sd, 'end' => $ed);
//        print_r($params);exit;
        $cfn=dirname(__FILE__)."/../cache/".$customer."-procedures-".$params['end'].'.json';

        $itemsD=array();

//echo "Select * from wp_crm2map_procedures_report where adsource='adwords' and adaccount='".$customer."' and pdate='".date("Y-m-d",$tme)."'";exit;
        $tmpD=$GLOBALS['wpdb']->get_results("Select * from wp_crm2map_procedures_report where adsource='adwords' and adaccount='".$customer."' and pdate='".date("Y-m-d",$tme)."'",ARRAY_A);
//        print_r($itemsD);exit;
        if (sizeof($tmpD)>0) {
            if ($tmpD[0]['group_id']!=0)
                foreach ($tmpD as $tt) {
                    $itemsD[$tt['group_id']]=$tt;
                };
            return $itemsD;
        }

/*
        if (file_exists($cfn)) {
            $items=json_decode(file_get_contents($cfn),true);
#            return $items;
        }
*/

//        $adgroups = getCustomerAdGroups($customer);
        /*        print_r(array_keys($adgroups));exit;
                foreach ($adgroups as $adgroup) {

                }
        */

        $params['report']='cost';
//        $params['campaigns'] = array_keys($adgroups);
        $cost=getCampaignData($params);
        $params['report']='url';
        $url=getCampaignData($params);


        $items=array();
        foreach ($cost as $id=>$cd) {
            $items[$id] = array('group' => $cd['group'], 'cost' => $cd['cost'] / 1000000,'url'=>'');
        };
        foreach ($url as $id=>$ud) {
            if (!isset($items[$id])) ;//$items[$id] = array('group' => $cd['group'],'cost'=>'');
            else $items[$id]['url']=$ud['url'];
        };
        unset($items['Total']);

        if (sizeof($tmpD)==0) {
#            print_r($items);
            $ct=date( 'Y-m-d',$tme);
            if (sizeof($items)==0) {
                $data=array();
                $data['adaccount']=$customer;
                $data['adsource']='adwords';
                $data['group_id']=0;
                $data['group']='NO_DATA';
                $data['cost']='0';
                $data['url']='';
                $data['pdate']=$ct;
                $GLOBALS['wpdb']->insert('wp_crm2map_procedures_report', $data );
            } else
                foreach ($items as $id=>$data) {
                    $data['adaccount']=$customer;
                    $data['adsource']='adwords';
                    $data['group_id']=$id;
                    $data['pdate']=$ct;
                    $GLOBALS['wpdb']->insert('wp_crm2map_procedures_report', $data );
                }
        }
#exit;
/*
        if (!file_exists($cfn)) {
            file_put_contents($cfn,json_encode($items));
        }
*/
        return $items;
    }

    function getLocations($customer) {
        $color_number=5;
        $tme=time()-86400;
        $sd=date("Ymd",$tme-30*86400);
        $ed=date("Ymd",$tme);
        $params=array('customer'=>$customer,'start' => $sd, 'end' => $ed);
        $cfn=dirname(__FILE__)."/../cache/".$customer."-locations-".$params['end'].'.json';


        $locationD=array();
//echo "Select * from wp_crm2map_procedures_report where adsource='adwords' and adaccount='".$customer."' and pdate='".date("Y-m-d",$tme)."'";exit;
        $tmpD=$GLOBALS['wpdb']->get_results("Select * from wp_crm2map_locations_report where adsource='adwords' and adaccount='".$customer."' and ldate='".date("Y-m-d",$tme)."'",ARRAY_A);
//        print_r($tmpD);exit;
        if (sizeof($tmpD)>0) {
            if ($tmpD[0]['location_name']!=0)
                foreach ($tmpD as $tt) {
                    $locationD[$tt['location_name']]=$tt;
                };
            return $locationD;
        }

/*
        if (file_exists($cfn)) {
            $location2=json_decode(file_get_contents($cfn),true);
            return $location2;
        }
*/
        $params['report']='location';
        $location=getCampaignData($params);
//        print_r($location);exit;
        $lp=array_chunk($location,50);
        $lnames=array();
        $ltmp=array();
        foreach ($lp as $lc) {
            $ids=array_unique(array_map(function($itm){return $itm['location'];},$lc));
//            $lnames = $GLOBALS['wpdb']->get_results("Select id,name from wp_crm2map_adwords_locations where id in (".implode(",",$ids).")",ARRAY_A);
//            print_r($lnames);exit;

            $ltmp = array_merge($ltmp,$GLOBALS['wpdb']->get_results("Select id,name,target_type from wp_crm2map_adwords_locations where id in (".implode(",",$ids).")",ARRAY_A));
        }
        foreach ($ltmp as $tmp) {
            $lnames[$tmp['id']]=$tmp['name'];
        }
//        print_r($lnames);exit;
        $location2=array();
        $tmp=array();
        foreach ($location as $id=>$locd){
            $lid=$lnames[$locd['location']];
            $location[$id]['cost']=$locd['cost']/1000000;
            $location[$id]['location_name']=$lnames[$locd['location']];
//            $location[$id]['location_type']=$locd['target_type'];
            $location2[$lid]=$location[$id];
            $tmp[]=$location[$id]['cost'];
        }
        sort($tmp);

        $c=sizeof($tmp)/$color_number;
        $costs=array();
        $i=0;

        while ($i<sizeof($tmp)) {
            $i+=$c;
            $k=round($i);
            if ($k>sizeof($tmp)-1) break;
            $costs[]=$tmp[round($i)];
        }
        array_unshift($costs,$tmp[0]);
/*
        print_r($costs);
        print_r($location2);
        exit;
*/
        foreach ($location2 as $i=>$j) {
            $k=0;

            while ($costs[$k]<$j['cost']) {
//                echo $costs[$k]." ".$j['cost']."\n";
                if ($k+1>=sizeof($costs)) break;
                $k++;
            }
            $location2[$i]['color_number']=$k;
//            print_r($location2[$i]);exit;
        }
        if (sizeof($tmpD)==0) {
            $ct=date( 'Y-m-d',$tme);
            if (sizeof($location2)==0) {
                $data=array();
                $data['adaccount']=$customer;
                $data['adsource']='adwords';
                $data['location_name']='NO_DATA';
                $data['cost']='0';
                $data['location']='0';
                $data['country']='0';
                $data['region']='0';
                $data['ldate']=$ct;
                $GLOBALS['wpdb']->insert('wp_crm2map_locations_report', $data );
            } else
//echo "ZZZZZZZZZZZZ";exit;
            foreach ($location2 as $id=>$data) {
                $data['adaccount']=$customer;
                $data['adsource']='adwords';
                $data['ldate']=$ct;
                $GLOBALS['wpdb']->insert('wp_crm2map_locations_report', $data );
//                print_r($data);exit;

            }
        }

/*
        if (!file_exists($cfn)) {
            file_put_contents($cfn,json_encode($location2));
        }
//        print_r($location2);exit;
*/
        return $location2;

    }

    function getBoundaries($codes=array()) {
        return $GLOBALS['wpdb']->get_results("Select zipcode,boundaries_out from wp_crm2map_boundaries where zipcode in (".implode(",",$codes).")",ARRAY_A);
    }
}
