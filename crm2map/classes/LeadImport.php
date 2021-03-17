<?php

/**
 * Created by PhpStorm.
 * Author: sd4travel@gmail.com
 * Date: 19.09.2016
 * Time: 20:15
 */
class LeadImport
{
    /**
     * @var string
     */
    protected $table="";

    /**
     *
     */
    function getLeads() {}

    /**
     *
     */
    function geocode() {}

    /**
     * @param $string
     * @param $cid
     * @return array|null
     */
    function lookup($string,$cid){
        $address=strtolower(trim($string));
        $string = str_replace (" ", "+", urlencode($address));
        $bkey='QUl6YVN5QTdwUmd1TWpZSzJqUGNUTFpyb05yaXRvMDY4LWxuLVhJ';
        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false&key=".urlencode(base64_decode($bkey));
//echo $details_url;exit;
//proxy fix
#       $details_url = "http://mx.wm4d.com/lp.php?address=".$string;
#        $details_url = "http://leads.wm4d.com/lp.php?address=".$string;

//        $rows=$GLOBALS['wpdb']->get_results("select * from wp_crm2map_geocode_cache where $address='".$address."'");
//print_r($rows);//exit;
//        if (sizeof($row))>0) return null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);

//print_r($response);exit;

        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] != 'OK') {
            if (false) {
                if (sizeof(($rows))>0)
                    $GLOBALS['wpdb']->update( $this->table, $data, array( 'cid' => $data["cid"] ) );
                else
                    $GLOBALS['wpdb']->insert($this->table, $data );

            }
            file_put_contents($this->logfile,"geocoding lookup failed ($cid - ".urldecode($string).")- ".$response['status']."\n",FILE_APPEND);
            if ($response['status']=='OVER_QUERY_LIMIT') {
                echo "Geocoding Limit exceeds";
/*
                $p=new HTTPParserKeenetic();
                $p->reconnect();
*/
                exit;
            }
            return null;
        }

        //var_dump($response);
        $geometry = $response['results'][0]['geometry'];

        $longitude = $geometry['location']['lng'];
        $latitude = $geometry['location']['lat'];

        $array = array(
            'latitude' => $geometry['location']['lat'],
            'longitude' => $geometry['location']['lng'],
            'location_type' => $geometry['location_type'],
        );

        return $array;
    }

    /**
     *
     */
    function export2map() {}
}

/**
 * Class LeadImportHubspot
 */
class LeadImportHubspot extends LeadImport
{
    /**
     *
     */
    function __construct() {
#        $this->table='wp_crm2map_hubspot_phone';
        $this->table='wp_crm2map_hubspot';
        $this->logfile=dirname(dirname(__FILE__))."/logs/import.log";
        file_put_contents($this->logfile, date(DATE_ISO8601)." LeadImport starting \n", FILE_APPEND);
    }

    function get_hubspot() {
        $hapikey = "700c2bed-82b4-4fae-adee-ad5d4f23f491";
        $hubspot = new Fungku\HubSpot($hapikey);
        return $hubspot;
    }

    function getPropertyIds() {
        return "'121','128','122','200','214','320','321','322','323','999','Paying Client'";
    }
    /**
     * @param $type
     * @return array
     */
    function getDefaultFields($type) {
        $data=array('logo'=>'',
            'logotype'=>'D',
            'createdbyid'=>'0',
            'updatedbyid'=>'0',
            'createddate'=>'',
            'updateddate'=>'',
            'statuscode'=>'',
//            'status'=>1,
//            'defaultloc'=>'0',

        );
        switch ($type) {
            case "driven":
                $data['logoid'] = '1';
                $data['type'] = '9';
                $data['labelid'] = '7';
                break;
            case "larry":
                $data['logoid'] = '1';
                $data['type'] = '10';
                $data['labelid'] = '7';
                break;
            case "trial":
                $data['logoid'] = '1';
                $data['type'] = '11';
                $data['labelid'] = '7';
                break;

            case "customer":
                $data['logoid'] = '1';
                $data['type'] = '2';
                $data['labelid'] = '7';
                break;
            case "paying":
                $data['logoid'] = '1';
                $data['type'] = '15';
                $data['labelid'] = '7';
                break;


            case "quitting":
                $data['logoid'] = '1';
                $data['type'] = '12';
                $data['labelid'] = '7';
                break;
            case "lead":
                $data['logoid'] = '2';
                $data['type'] = '1';
                $data['labelid'] = '3';
                break;
            case "ex-client":
                $data['logoid'] = '1';
                $data['type'] = '4';
                $data['labelid'] = '7';
                break;
            default:
                if (preg_match("/ex-client(\d+)/",$type,$m)) {
                    $nm = array(
                        '320' => 5,
                        '321' => 6,
                        '322' => 7,
                        '323' => 8,
                    );
                    if (isset($nm[$m[1]])) {
                        $data['logoid'] = '1';
                        $data['type'] = $nm[$m[1]];
                        $data['labelid'] = '7';
                    }
                }
                break;

        };
        return $data;
    }

    /**
     * @return array
     */
    function getFields() {
        $res=array('cid',
            'firstname',
            'lastname',
            'full_name',
            'company',
            'website',
            'email',
            'address',
            'city',
            'state',
            'zip',
            'country',
            'tag_ids',
            'tags',
            'person_type',
            'fax',
            'mapping_tag',
			'marketing_offices',
            'adwords_id'
/*
			'phone',
			'mobilephone',
			'website_phone_numbers'
*/			
        );
        return $res;
    }

    /**
     * @throws \Fungku\HubSpot\API\HubSpotException
     */
    function updateLead($cid) {
#        $cfn=dirname(__FILE__)."/../logs/webhook.log";
#        file_put_contents($cfn,date("Y-m-d H:i:s")." ".print_r($_REQUEST,true),FILE_APPEND);

        $hubspot = $this->get_hubspot();
        $contact=$hubspot->contacts()->get_contact_by_id($cid);
        $data=array('cid'=>$cid);
        foreach ((array)$contact->properties as $nme=>$pobj) {
            if (in_array($nme,$this->getFields()))
                $data[$nme]=$pobj->value;
        };
        if (isset($data['marketing_offices'])) {
            $offices=explode("\r\n",trim($data['marketing_offices']));
            foreach ($offices as $i=>$j) if (trim($j)=="") unset($offices[$i]);
            if (sizeof($offices)>1) $data['offices_count']=sizeof($offices);
            $data['address']=preg_replace("/\r|\n/i"," ",trim($data['marketing_offices']));

        }
//        print_r($data);exit;

        if (sizeof($data)>1) {

            $res=$GLOBALS['wpdb']->get_results("select marketing_offices from ".$this->table." where cid=".$data['cid']);

            if (sizeof($res)>0) {
                if (trim($res[0]->marketing_offices)!=trim($data['marketing_offices'])) $data['lat']=0;
                $GLOBALS['wpdb']->update( $this->table, $data, array( 'cid' => $data["cid"] ) );
            } else {
                $GLOBALS['wpdb']->insert($this->table, $data );
                file_put_contents($this->logfile, date(DATE_ISO8601)." unknown cid ".print_r($cobj,true)."\n", FILE_APPEND);
            }
        };


    }

    function getRecentLeads() {

        $hubspot = $this->get_hubspot();

        $opts=array(
            'count' => 100, // defaults to 20
//            'timeOffset' => 0 // contact offset used for paging
        );
        $timeoffset=0;
        $timeoffset_new=0;
        $do_break=false;

        $lmf=dirname(__FILE__)."/../cache/lastupdated.txt";
        if (file_exists($lmf)) {
            $timeoffset=chop(file_get_contents($lmf));
//            if ($timeoffset>0) $opts['timeOffset']=$timeoffset;
        } else {
            $timeoffset='1509647421162';
        }

        do {
// get 5 contacts' firstnames, offset by 50
            $obj = $hubspot->contacts()->get_recent_contacts($opts);


//            echo isset($obj->{"has-more"});exit;
//            print_r($obj->contacts );exit;
//print_r($GLOBALS['wpdb']);exit;
            file_put_contents($this->logfile, date(DATE_ISO8601)." getRecentLeads starting ".sizeof($obj->contacts)." found - ".$obj->{"vid-offset"}."\n", FILE_APPEND);

            foreach ($obj->contacts as $i=>$cobj) {
                if ($timeoffset_new==0) $timeoffset_new=$cobj->addedAt;

                if ($cobj->addedAt<=$timeoffset) {
                    $do_break=true;
                    break;
                }
//                $timeoffset_old=$timeoffset_new;

//
                $data=array('cid'=>$cobj->vid);
                if (sizeof((array)$cobj->properties)>1) {
                    $contact=$hubspot->contacts()->get_contact_by_id($cobj->vid);
//				    print_r($contact->properties);exit;
                    foreach ((array)$contact->properties as $nme=>$pobj) {
                        if (in_array($nme,$this->getFields()))
                            $data[$nme]=$pobj->value;

    //					if (isset($data[$nme]) && $nme=='marketing_offices') exit;
    //                echo "$nme:".$pobj->value."\n";
                    };
                    if (isset($data['marketing_offices'])) {
                        $offices=explode("\r\n",trim($data['marketing_offices']));
                        foreach ($offices as $i=>$j) if (trim($j)=="") unset($offices[$i]);
                        if (sizeof($offices)>1) $data['offices_count']=sizeof($offices);
                        $data['address']=preg_replace("/\r|\n/i"," ",trim($data['marketing_offices']));

                    }

    //                file_put_contents($this->logfile, date(DATE_ISO8601)." ".$data['firstname']." ".$data['lastname']." \n", FILE_APPEND);
    //                print_r($data);if ($i>5) exit;

                    if (sizeof($data)>1) {
//                        $cnt=$GLOBALS['wpdb']->get_var("select count(*) from ".$this->table." where cid=".$data['cid']);
//$data['cid']="30552";
                        $res=$GLOBALS['wpdb']->get_results("select marketing_offices from ".$this->table." where cid=".$data['cid']);
//                        print_r($data);print_r($res[0]->marketing_offices);exit;
                        if (sizeof($res)>0) {
							if (trim($res[0]->marketing_offices)!=trim($data['marketing_offices'])) $data['lat']=0;
                            $GLOBALS['wpdb']->update( $this->table, $data, array( 'cid' => $data["cid"] ) );
						} else {
                            $GLOBALS['wpdb']->insert($this->table, $data );
                            file_put_contents($this->logfile, date(DATE_ISO8601)." unknown cid ".print_r($cobj,true)."\n", FILE_APPEND);
                        }
                    };
//					exit;
                }


					
//            exit;

//                file_put_contents($this->logfile,"$i - ".print_r($cobj->vid,true)." updated"."\n",FILE_APPEND);

            };
            if ($timeoffset_new>0) file_put_contents($lmf,$timeoffset_new);
//			exit;
//			print_r($obj->contacts);exit;
//exit;
//            print_r($obj->{has-more});
            $opts['vidOffset']=$obj->{"vid-offset"};
            $opts['timeOffset']=$obj->{"time-offset"};
            if ($do_break) break;

//            print_r($opts['vidOffset']);//exit;
//            file_put_contents($this->logfile,print_r($obj,true)."\n",FILE_APPEND);

        } while (/*false && */is_object($obj) && isset($obj->{"has-more"}) && $obj->{"has-more"}==1);
        //print_r($contacts);
//        exit;
/*
        $ct=$hubspot->contacts()->get_contact_by_id(30701);
//print_r((array)$ct->properties);
        foreach ((array)$ct->properties as $nme=>$obj) {
            echo "$nme:".$obj->value."\n";
        }
        exit;
*/
    }

    function getLeads() {
         $hapikey = "28bcf809-3a13-46d2-be91-a33e9c62e2a1";


        $hubspot = new Fungku\HubSpot($hapikey);

        $opts=array(
            'count' => 300, // defaults to 20
            'vidOffset' => 0 // contact offset used for paging
        );

        do {
// get 5 contacts' firstnames, offset by 50
            $obj = $hubspot->contacts()->get_all_contacts($opts);

//print_r($GLOBALS['wpdb']);exit;
            file_put_contents($this->logfile, date(DATE_ISO8601)." getLeads starting ".sizeof($obj->contacts)." found - ".$obj->{"vid-offset"}."\n", FILE_APPEND);

            foreach ($obj->contacts as $i=>$cobj) {

                $contact=$hubspot->contacts()->get_contact_by_id($cobj->vid);
                $data=array('cid'=>$cobj->vid);
//				print_r($contact->properties);//exit;
                foreach ((array)$contact->properties as $nme=>$pobj) {
                    if (in_array($nme,$this->getFields()))
                        $data[$nme]=$pobj->value;

//					if (isset($data[$nme]) && $nme=='marketing_offices') exit;
//                echo "$nme:".$pobj->value."\n";
                };
                if (isset($data['marketing_offices'])) {
                    $offices=explode("\r\n",trim($data['marketing_offices']));
                    foreach ($offices as $i=>$j) if (trim($j)=="") unset($offices[$i]);
                    if (sizeof($offices)>1) $data['offices_count']=sizeof($offices);
                    $data['address']=preg_replace("/\r|\n/i"," ",trim($data['marketing_offices']));

                }

//                file_put_contents($this->logfile, date(DATE_ISO8601)." ".$data['firstname']." ".$data['lastname']." \n", FILE_APPEND);
//                print_r($data);if ($i>5) exit;

                $cnt=$GLOBALS['wpdb']->get_var("select count(*) from ".$this->table." where cid=".$data['cid']);
                if ($cnt>0)
                    $GLOBALS['wpdb']->update( $this->table, $data, array( 'cid' => $data["cid"] ) );
                else
                    $GLOBALS['wpdb']->insert($this->table, $data );


//            exit;

//                file_put_contents($this->logfile,"$i - ".print_r($cobj->vid,true)." updated"."\n",FILE_APPEND);

            };
//			exit;
//			print_r($obj->contacts);exit;
//exit;
//            print_r($obj->{has-more});
            $opts['vidOffset']=$obj->{"vid-offset"};
//            print_r($opts['vidOffset']);//exit;
//            file_put_contents($this->logfile,print_r($obj,true)."\n",FILE_APPEND);

        } while (/*false && */is_object($obj) && isset($obj->{"has-more"}) && $obj->{"has-more"}==1);
        //print_r($contacts);
//        exit;
        /*
                $ct=$hubspot->contacts()->get_contact_by_id(30701);
        //print_r((array)$ct->properties);
                foreach ((array)$ct->properties as $nme=>$obj) {
                    echo "$nme:".$obj->value."\n";
                }
                exit;
        */
    }



    /**
     *
     */
    function geocode() {
//        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat='0' and address<>''",ARRAY_A);
//        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat='0' and ((tag_ids not like '%128%') and (tag_ids not like '%-1%') or mapping_tag='122') and address<>''",ARRAY_A);
//        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat='0' and ((tag_ids not like '%128%') or (mapping_tag in ('122','200') and tag_ids not like '%-1%')) and marketing_offices<>''",ARRAY_A);

        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat='0' and (/*(tag_ids not like '%128%') or*/ (mapping_tag in (".$this->getPropertyIds().") and tag_ids not like '%-1%')) and marketing_offices<>''",ARRAY_A);
		
		
//        print_r($contacts);exit;
//		print_r($contacts);exit;
        file_put_contents($this->logfile, date(DATE_ISO8601)." geocode starting ".sizeof($contacts)." found \n", FILE_APPEND);

        if(sizeof($contacts)>0)
        {
            foreach($contacts as $contact)
            {
                if (trim($contact['country'])=="") $contact['country']='United States';
//                $address = $contact['address'].", ".$contact['city'].", ".$contact['state'].", ".$contact['country'].", ".$contact['zip'];
                $address = $contact['address'];

                $latLng = $this->lookup($address,$contact['cid']);

/*
                if ($contact['cid']==3571) {
                    file_put_contents($this->logfile, $address." ".print_r($latLng,true)."\n", FILE_APPEND);
//					exit;
                };

                if ($force) {
                    sleep(2);
//					exit;
                };
*/
                if($latLng !== null)
                {
                    $GLOBALS['wpdb']->query("UPDATE ".$this->table." set lat='".$latLng['latitude']."',lng='".$latLng['longitude']."' WHERE cid=".$contact['cid']);
#                    $this->db->query("UPDATE wp_sl_stores set lat='".$latLng['latitude']."',lng='".$latLng['longitude']."' WHERE f_cid=".$contact['cid']);

                }
            }
        }//else echo "No result";
        file_put_contents($this->logfile, date(DATE_ISO8601)." geocode finished \n", FILE_APPEND);

    }

    /**
     *
     */
    function export2map() {
        $GLOBALS['wpdb']->query('truncate table wp_sl_stores');
        // and (tag_ids not like '%128%')
        // and mapping_tag!='122'
        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat<>'0' and (tag_ids not like '%-1%') and mapping_tag in (".$this->getPropertyIds().")",ARRAY_A);
//        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat<>'0' and (tag_ids not like '%-1%') and mapping_tag in ('321')",ARRAY_A);

//        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat<>'0' and (tag_ids not like '%-1%') and mapping_tag!='128'",ARRAY_A);
//        $contacts = $GLOBALS['wpdb']->get_results("Select * from ".$this->table." where lat<>'0' and ((tag_ids not like '%128%') or (mapping_tag='122' and tag_ids not like '%-1%')) and marketing_offices<>''",ARRAY_A);
		
		
		
        $fields=array();
        $fields['customer']=$this->getDefaultFields('customer');

        $fields['quitting']=$this->getDefaultFields('quitting');
        $fields['trial']=$this->getDefaultFields('trial');
        $fields['lead']=$this->getDefaultFields('lead');
        $fields['ex-client']=$this->getDefaultFields('ex-client');
        $fields['driven']=$this->getDefaultFields('driven');

        $fields['ex-client320']=$this->getDefaultFields('ex-client320');
        $fields['ex-client321']=$this->getDefaultFields('ex-client321');
        $fields['ex-client322']=$this->getDefaultFields('ex-client322');
        $fields['ex-client323']=$this->getDefaultFields('ex-client323');

//print_r($contacts);exit;

        file_put_contents($this->logfile, date(DATE_ISO8601)." export2map starting ".sizeof($contacts)." found \n", FILE_APPEND);

        foreach ($contacts as $contact) {
            $data=array();
            if (trim($contact['mapping_tag'])=='121') {
                $data = array_merge($fields['trial'], $contact);

            } elseif (trim($contact['mapping_tag'])=='122') {
                $data=array_merge($fields['customer'],$contact);

            } elseif (trim($contact['mapping_tag'])=='Paying Client') {
                $data=array_merge($this->getDefaultFields('paying'),$contact);

            } elseif (trim($contact['mapping_tag'])=='214') {
                $data=array_merge($fields['quitting'],$contact);

            } elseif ($contact['mapping_tag']=='999') {
                if ($contact['cid']>='910000')
                    $data = array_merge($this->getDefaultFields('larry'), $contact);
                else
                    $data = array_merge($this->getDefaultFields('driven'), $contact);

            } elseif ($contact['mapping_tag']=='200') {
                $data = array_merge($fields['lead'], $contact);
            } elseif ($contact['mapping_tag']=='128') {
                $data=array_merge($fields['ex-client'],$contact);
            } else {
                $data = array_merge($fields['ex-client'.$contact['mapping_tag']], $contact);
            }
//            print_r($data);exit;

/* 			if (strpos($contact['tag_ids'],"122") !== false || trim($contact['mapping_tag'])=='122') {
                $data=array_merge($fields['customer'],$contact);
            } elseif (strpos($contact['tag_ids'],"124") !== false) {
                $data=array_merge($fields['customer'],$contact);
//                print_r($data);exit;
            } else
                $data = array_merge($fields['lead'], $contact);
*/
/*
            if (strpos($contact['tag_ids'],"128") !== false) {
                continue;
            } else {
*/
            if (sizeof($data)>0) {
                $data['phone']='';
                $data['name']=$data['full_name'];
                $data['lastname']=str_replace("#NAME?","",$data['lastname']);
                if ($data['name']=="") $data['name']=$data['firstname']." ".$data['lastname'];
                $data['zip_code']=$data['zip'];
                unset($data['zip']);
                $data['labeltext']=$data['name'];
                unset($data['full_name']);
                $data['f_cid']=$data['cid'];
                unset($data['cid']);
                $data['f_first_name']=$data['firstname'];
                unset($data['firstname']);
                $data['f_last_name']=$data['lastname'];
                unset($data['lastname']);
                $data['f_company']=$data['company'];
                unset($data['company']);
                unset($data['tag_ids']);
                unset($data['tags']);
                unset($data['person_type']);
                if (!isset($data['fax'])) $data['fax']="";
//                if (!isset($data['iscustid'])) $data['iscustid']="";
                unset($data['mapping_tag']);
                unset($data['person_type']);

                unset($data['createddate']);
                unset($data['updateddate']);
                if (!isset($data['fax'])) $data['fax']="";								
                if (isset($data['marketing_offices'])) $data['marketing_office']=$data['marketing_offices'];								
				unset($data['marketing_offices']);
				unset($data['offices_count']);

//            echo $GLOBALS['wpdb']->show_errors();
//                print_r($data);
                $GLOBALS['wpdb']->insert('wp_sl_stores', $data );
//            echo $GLOBALS['wpdb']->print_error();exit;
                //print_r($data);
            }
//            if ($contact['tag_ids'])
        }
        file_put_contents($this->logfile, date(DATE_ISO8601)." export2map finished \n", FILE_APPEND);

    }
}

/********************************************/

/**
 * Created by PhpStorm.
 * Author: sd4travel@gmail.com
 * Date: 19.03.2019
 * Time: 15:30
 */
class LeadImportCsv extends LeadImportHubspot
{
    function getLeads() {
        //echo "in";exit;
#        $fn=dirname($this->logfile)."/driven3.csv";
        $fn=dirname($this->logfile)."/driven4.csv";

//        echo $fn;exit;
        $lines=file($fn);
        array_shift($lines);
        foreach ($lines as $i=>$tmp) {
            $item = str_getcsv($tmp, ",", '"');

            $data=array();

/*
            $data['cid'] = 900000+$i;
            $data['mapping_tag'] = '999';
            $data['full_name']=$data['company']=$item[1];
            $data['website']=$item[0];
            $data['marketing_offices']=$data['address']=$item[3];
*/

            $data['cid'] = 910000+$i;
            $data['mapping_tag'] = '999';
#            $data['website']=$item[0];
            $data['marketing_offices']=$data['address']=implode(" ",$item);
            $data['full_name']=$data['company']=$data['marketing_offices'];


            foreach ($this->getFields() as $nme) {
                if (!isset($data[$nme])) $data[$nme]='';
            };
            $cnt=$GLOBALS['wpdb']->get_var("select count(*) from ".$this->table." where cid=".$data['cid']);

//            $GLOBALS['wpdb']->show_errors();

            if ($cnt>0)
                $GLOBALS['wpdb']->update( $this->table, $data, array( 'cid' => $data["cid"] ) );
            else
                $GLOBALS['wpdb']->insert($this->table, $data );

//            print_r($data);exit;


//            $GLOBALS['wpdb']->print_error();exit;
        };
    }
}

