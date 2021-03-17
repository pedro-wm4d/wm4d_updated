<?php

define('VENDOR_NAME', 'adwords_client_1809');


require __DIR__ . '/' . VENDOR_NAME . '/vendor/autoload.php';
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;

use Google\AdsApi\AdWords\v201809\cm\ReportDefinitionReportType;
use Google\AdsApi\AdWords\v201809\cm\ReportDefinitionService;

use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Google\AdsApi\AdWords\v201809\cm\Paging;

use Google\AdsApi\AdWords\v201809\cm\Selector;

use Google\AdsApi\AdWords\v201809\cm\SortOrder;
use Google\AdsApi\AdWords\v201809\mcm\ManagedCustomer;
use Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerService;
use Google\AdsApi\AdWords\v201809\mcm\Customer;
use Google\AdsApi\AdWords\v201809\mcm\CustomerService;
use Google\AdsApi\AdWords\Reporting\v201809\DownloadFormat;

use Google\AdsApi\AdWords\Reporting\v201809\ReportDefinition;
use Google\AdsApi\AdWords\Reporting\v201809\ReportDefinitionDateRangeType;

use Google\AdsApi\AdWords\Reporting\v201809\ReportDownloader;
use Google\AdsApi\AdWords\ReportSettingsBuilder;
use Google\AdsApi\AdWords\v201809\cm\Predicate;
use Google\AdsApi\AdWords\v201809\cm\PredicateOperator;

use Google\AdsApi\AdWords\v201809\cm\DateRange;

use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Google\AdsApi\AdWords\v201809\cm\AdGroupService;

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\Common\SoapSettingsBuilder;

//echo get_class(new CustomerService);exit;
define('CustomerService__class',get_class(new CustomerService));
define('CampaignService__class',get_class(new CampaignService));
define('ManagedCustomerService__class',get_class(new ManagedCustomerService));
define('AdGroupService__class',get_class(new AdGroupService));
//*********************************************************************
/*
function clientLogin($username, $password){
// Get an access code for the user
    $url = "https://www.google.com/accounts/ClientLogin";
    $params = array(
        "accountType" => "GOOGLE",
        "Email" => $username,
        "Passwd" => $password,
        "service" => "adwords",
        "source" => "test"
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curlData = curl_exec($curl);
    curl_close($curl);
    return $curlData;
}
*/

function getSession($cid = 0)
{
	$oauth2Info=array(
			'client_id' => "958235329474-bmtqdeif1fci3k09iki7b44gpqak4ebb.apps.googleusercontent.com",
			'client_secret' => "I2H8yJnKDFWXPOC-6881EbJL",
			'refresh_token' => "1/GEOJTc5qQKULJNB_0TCv3FC0AGwMqCSQo5HCHBiuN4316hrcbqUzKagYRr18IPiX",
			'developer_token' => "T8bWMd4K0QZ_qD61A8jURg",
			'user_agent' => "Call Scorer");

//    $oauth2Info = array('client_id' => $conn['clientId'], 'client_secret' => $conn['clientSecret'], 'refresh_token' => $conn['refreshToken']);
//	echo "innnnnnnnnnn";exit;

    $fn = dirname(__FILE__) . '/' . VENDOR_NAME . '/adsapi_php.ini';
    $oAuth2Credential = ($tmp = new OAuth2TokenBuilder()) ? $tmp->fromFile($fn)->withClientId($oauth2Info['client_id'])->withClientSecret($oauth2Info['client_secret'])->withRefreshToken($oauth2Info['refresh_token'])->build() : $tmp->fromFile($fn)->withClientId($oauth2Info['client_id'])->withClientSecret($oauth2Info['client_secret'])->withRefreshToken($oauth2Info['refresh_token'])->build();

    // Construct an API session configured from a properties file and the
    // OAuth2 credentials above.
    $soapSettings = ($tmp = new SoapSettingsBuilder()) ? $tmp->disableSslVerify()->build() : $tmp->disableSslVerify()->build();
    if ($cid > 0) {
        $session = ($tmp = new AdWordsSessionBuilder()) ? $tmp->fromFile($fn)->withDeveloperToken($oauth2Info['developer_token'])->withUserAgent($oauth2Info['user_agent'])->withClientCustomerId($cid)->withSoapSettings($soapSettings)->withOAuth2Credential($oAuth2Credential)->build() : $tmp->fromFile($fn)->withDeveloperToken($oauth2Info['developer_token'])->withUserAgent($oauth2Info['user_agent'])->withClientCustomerId($cid)->withSoapSettings($soapSettings)->withOAuth2Credential($oAuth2Credential)->build();
    } else {
        $session = ($tmp = new AdWordsSessionBuilder()) ? $tmp->fromFile($fn)->withDeveloperToken($oauth2Info['developer_token'])->withUserAgent($oauth2Info['user_agent'])->withSoapSettings($soapSettings)->withOAuth2Credential($oAuth2Credential)->build() : $tmp->fromFile($fn)->withDeveloperToken($oauth2Info['developer_token'])->withUserAgent($oauth2Info['user_agent'])->withSoapSettings($soapSettings)->withOAuth2Credential($oAuth2Credential)->build();
    }

    return $session;
}

function getAdwordsUser()
{
    /*
    	if (sizeof($ds)==0) {
    		$oauth2Info = array(
    			'client_id' => Configure::read('Adwords.clientId'),
    			'client_secret' => Configure::read('Adwords.clientSecret'),
    			'refresh_token' => Configure::read('Adwords.refreshToken')
    		);
    	} else {
    */
    $oauth2Info = array('client_id' => Configure::read('Adwords.clientId'), 'client_secret' => Configure::read('Adwords.clientSecret'), 'refresh_token' => $ds['params_parsed']['adwords_token']);
    //	}
    $fn = dirname(__FILE__) . '/' . VENDOR_NAME . '/adsapi_php.ini';
    $oAuth2Credential = ($tmp = new OAuth2TokenBuilder()) ? $tmp->fromFile($fn)->withClientId($oauth2Info['client_id'])->withClientSecret($oauth2Info['client_secret'])->withRefreshToken($oauth2Info['refresh_token'])->build() : $tmp->fromFile($fn)->withClientId($oauth2Info['client_id'])->withClientSecret($oauth2Info['client_secret'])->withRefreshToken($oauth2Info['refresh_token'])->build();
    // Construct an API session configured from a properties file and the
    // OAuth2 credentials above.
    $soapSettings = ($tmp = new SoapSettingsBuilder()) ? $tmp->disableSslVerify()->build() : $tmp->disableSslVerify()->build();
    $session = ($tmp = new AdWordsSessionBuilder()) ? $tmp->fromFile($fn)->withDeveloperToken(Configure::read('Adwords.developerToken'))->withUserAgent(Configure::read('Adwords.UserAgent'))->withSoapSettings($soapSettings)->withOAuth2Credential($oAuth2Credential)->build() : $tmp->fromFile($fn)->withDeveloperToken(Configure::read('Adwords.developerToken'))->withUserAgent(Configure::read('Adwords.UserAgent'))->withSoapSettings($soapSettings)->withOAuth2Credential($oAuth2Credential)->build();
    return $session;
}
function get_customers()
{
    // #2R
    try {
        //	  $selector = new Selector();exit;
        $page_limit = 500;
        $adWordsServices = new AdWordsServices();
        $session = getSession();
        $customerService = $adWordsServices->get($session, CustomerService__class);
        $info = $customerService->getCustomers();
        if (isset($info[0])) {
//            $session = getSession($info[0]->getCustomerId());
            $session = getSession('8929263590');			
        }
//        print_r($session);exit;
        $managedCustomerService = $adWordsServices->get($session, ManagedCustomerService__class);
        // Create selector.
        $selector = new Selector();
        $selector->setFields(array('CustomerId', 'Name'));
        //	  $selector->setOrdering([new OrderBy('CustomerId', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, $page_limit));
        // Maps from customer IDs to accounts and links.
        $customerIdsToAccounts = array();
        $customerIdsToChildLinks = array();
        $customerIdsToParentLinks = array();
        $totalNumEntries = 0;
        do {
            // Make the get request.
            $page = $managedCustomerService->get($selector);
            // Create links between manager and clients.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                /*
                			  if ($page->getLinks() !== null) {
                				  foreach ($page->getLinks() as $link) {
                					  // Cast the indexes to string to avoid the issue when 32-bit PHP
                					  // automatically changes the IDs that are larger than the 32-bit max
                					  // integer value to negative numbers.
                					  $managerCustomerId = strval($link->getManagerCustomerId());
                					  $customerIdsToChildLinks[$managerCustomerId][] = $link;
                					  $clientCustomerId = strval($link->getClientCustomerId());
                					  $customerIdsToParentLinks[$clientCustomerId] = $link;
                				  }
                			  }
                */
                foreach ($page->getEntries() as $account) {
                    $acc = new Object();
//					print_r((array) $account);exit;
                    foreach ((array) $account as $k => $v) {
                        $k = str_replace("\0*\0", '', $k);
                        $acc->{$k} = $v;
                    }
                    //print_r($acc);
                    $customerIdsToAccounts[] = $acc;
                }
            }
            // Advance the paging index.
            $selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + $page_limit);
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);
        return $customerIdsToAccounts;
    } catch (Exception $e) {
        printf('An error has occurred: %s
', $e->getMessage());
        return false;
        die;
    }
}
function getCustomReport($data)
{
    $res = array('error' => '', 'body' => '');
    if (!isset($data['report_type'])) {
        $data['report_type'] = 'kpi';
    }
    if ($data['report_type'] == 'kpi') {
        if (!isset($data['group_type'])) {
            $data['group_type'] = 'month';
        }
    }
    //	vendor_log('!!!!!!! calling getCustomReport '.print_r($data,true),'budget');
    //print_r($data);exit;
    try {
        $adWordsServices = new AdWordsServices();
        $session = getSession($data['customerId']);
        /*
        		$user = getAdwordsUser();
        		
                $user->SetClientCustomerId($data['customerId']);
        //print_r();
                $user->LoadService('ReportDefinitionService', ADWORDS_VERSION);
        */
        $dateRange = null;
        $selector = new Selector();
        if ($data['report_type'] == 'last_impressions') {
            $timeend = time();
            $timestart = $timeend - 90 * 86400;
            /*
            			$dateRange->max = date("Ymd",$timeend);
            			$dateRange->min = date("Ymd",$timestart);
            		    $selector->predicates[] = new Predicate('Impressions', 'GREATER_THAN', 0);
            */
            $dateRange = new DateRange(date('Ymd', $timestart), date('Ymd', $timeend));
            $selector->setPredicates(array(new Predicate('Impressions', PredicateOperator::GREATER_THAN, 0)));
            $data['report_type'] = 'kpi';
        } else {
            /*
            	        $dateRange->max = $data['end'];
                	    $dateRange->min = $data['start'];
            */
            $dateRange = new DateRange($data['start'], $data['end']);
        }
        if ($data['report_type'] == 'kpi' || $data['report_type'] == 'dimensions') {
            if ($data['group_type'] == 'day') {
                $fields = array('Clicks', 'Impressions', 'Ctr', 'AverageCpc', 'Cost', 'AveragePosition', 'Date', 'Year', 'SearchExactMatchImpressionShare', 'Conversions', 'AllConversions');
            } else {
                $fields = array('Clicks', 'Impressions', 'Ctr', 'AverageCpc', 'Cost', 'AveragePosition', 'MonthOfYear', 'Year', 'SearchExactMatchImpressionShare', 'Conversions', 'AllConversions');
            }
        }
        if ($data['report_type'] == 'adw') {
            //            $fields = array('Ctr','Clicks','Cost','ConversionRate');
            $fields = array('Ctr', 'Clicks', 'Cost', 'ConversionRate', 'Conversions', 'Impressions', 'AllConversions');
        }
        $selector->setFields($fields);
        //        $selector->fields = $fields;
        // Create selector.
        //        $selector->dateRange = $dateRange;
        $selector->setDateRange($dateRange);
        // Create report definition.
        $reportDefinition = new ReportDefinition();
        if (isset($data['campaignId']) && $data['campaignId'] != '') {
            /*
            	        $reportDefinition->reportType = 'CAMPAIGN_PERFORMANCE_REPORT';
            		    $selector->predicates[] = new Predicate('CampaignId', 'EQUALS', $data['campaignId']);
            */
            $reportDefinition->setReportType(ReportDefinitionReportType::CAMPAIGN_PERFORMANCE_REPORT);
            $selector->setPredicates(array(new Predicate('CampaignId', PredicateOperator::EQUALS, $data['campaignId'])));
        } else {
            $reportDefinition->setReportType(ReportDefinitionReportType::ACCOUNT_PERFORMANCE_REPORT);
        }
        //	        $reportDefinition->reportType = 'ACCOUNT_PERFORMANCE_REPORT';
        /*
                $reportDefinition->selector = $selector;
                $reportDefinition->reportName = 'Ad Performance Report #';
                $reportDefinition->dateRangeType = 'CUSTOM_DATE';
        */
        $reportDefinition->setSelector($selector);
        $reportDefinition->setReportName('Ad Performance Report #');
        $reportDefinition->setDateRangeType(ReportDefinitionDateRangeType::CUSTOM_DATE);
        /*
        		if (isset($data['campaignId']) && $data['campaignId']!="") {
        	        $reportDefinition->reportType = 'CAMPAIGN_PERFORMANCE_REPORT';
        		    $selector->predicates[] = new Predicate('Impressions', 'GREATER_THAN', 0);
        			
        			CampaignId
        		} else 
        	        $reportDefinition->reportType = 'ACCOUNT_PERFORMANCE_REPORT';
        
                $reportDefinition->downloadFormat = 'CSV';
        */
        $reportDefinition->setDownloadFormat(DownloadFormat::CSV);
        // Exclude criteria that haven't recieved any impressions over the date range.
        //        $reportDefinition->includeZeroImpressions = FALSE;
        // Set additional options.
        /*
                $options = array('version' => ADWORDS_VERSION);
        		if (ADWORDS_VERSION<="v201402") $options['returnMoneyInMicros'] = FALSE;
        
                // Download report.//$data['filePath']
        		$ru=new ReportUtils();
                $res['body']=$ru->DownloadReport($reportDefinition, NULL, $user, $options);
        */
        $reportDownloader = new ReportDownloader($session);
        $reportSettingsOverride = ($tmp = new ReportSettingsBuilder()) ? $tmp->includeZeroImpressions(false)->build() : $tmp->includeZeroImpressions(false)->build();
        $reportDownloadResult = $reportDownloader->downloadReport($reportDefinition, $reportSettingsOverride);
        $res['body'] = $reportDownloadResult->getAsString();
    } catch (Exception $e) {
        $res['error'] = $e->getMessage();
        $res['body'] = $e->getMessage();
        vendor_log($data['report_type'] . ' report Import error: (getCustomReport) - ' . serialize($data) . '
			' . $res['body'], 'external');
    }
    return $res;
}
/*
function getTest( $customerId )
{

	$res=array('error'=>'','body'=>'');
	
//print_r($data);exit;
    try{



        $fields = array('Month','Year','Impressions');

		$user = getAdwordsUser();
		
        $user->SetClientCustomerId($customerId);
//print_r();
        $user->LoadService('ReportDefinitionService', ADWORDS_VERSION);
//	exit;
        // Create selector.
		$timeend=time();
		$timestart=$timeend-800*86400;

        $dateRange = new DateRange();
        $dateRange->max = date("Ymd",$timeend);
        $dateRange->min = date("Ymd",$timestart);
//print_r($dateRange);exit;		


        $selector = new Selector();
        $selector->fields = $fields;
//        $selector->dateRange = $dateRange;

//	    $selector->predicates[] = new Predicate('Impressions', 'GREATER_THAN', 0);
//	    $selector->predicates[] = new Predicate('Impressions', 'GREATER_THAN', 0);
//	    $selector->ordering[] = new OrderBy('Date', 'DESCENDING');


        // Create report definition.
        $reportDefinition = new ReportDefinition();
        $reportDefinition->selector = $selector;
        $reportDefinition->reportName = 'Ad Performance Report #';
        $reportDefinition->dateRangeType = 'ALL_TIME';
//        $reportDefinition->dateRangeType = 'CUSTOM_DATE';


//        $reportDefinition->reportType = 'LABEL_REPORT';
        $reportDefinition->reportType = 'ACCOUNT_PERFORMANCE_REPORT';

        $reportDefinition->downloadFormat = 'CSV';

        // Exclude criteria that haven't recieved any impressions over the date range.
//        $reportDefinition->includeZeroImpressions = true;

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION, 'returnMoneyInMicros' => FALSE, 'includeZeroImpressions' => true);
        // Download report.//$data['filePath']
		$ru=new ReportUtils();
        $res['body']=$ru->DownloadReport($reportDefinition, NULL, $user, $options);
		
		print_r($res['body']);
		exit;
		$lines=array_reverse(split("\n",trim($res['body'])));
		unset($res['body']);
		if (isset($lines[1])) $res['last_impressions']=str_getcsv($lines[1],",");


    } catch (Exception $e) {
		$res['error'] = $e->getMessage();
		$res['body'] = $e->getMessage();
		vendor_log("KPI report Import error: (getCustomReport) - ".serialize($data)."\n\t\t\t".$res['body'],'external');
//        printf("An error has occurred: %s\n", $e->getMessage());
//        return false;
    }
	return $res;
}
*/
function getCustomerCampaigns($customerId)
{
    // #1+R
    $page_limit = 50;
    $adWordsServices = new AdWordsServices();
    $session = getSession($customerId);
    //	$user = getAdwordsUser();
    //    $user->SetClientCustomerId($customerId);
    $accountCampaigns = array();
    //    $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);
    $campaignService = $adWordsServices->get($session, CampaignService__class);
    $selector = new Selector();
    $selector->setFields(array('Id', 'Name', 'StartDate'));
    $selector->setOrdering(array(new OrderBy('Name', SortOrder::ASCENDING)));
    $selector->setPaging(new Paging(0, $page_limit));
    $totalNumEntries = 0;
	try {	
		do {
			$page = $campaignService->get($selector);
			if ($page->getEntries() !== null) {
				$totalNumEntries = $page->getTotalNumEntries();
				foreach ($page->getEntries() as $campaign) {
					$accountCampaigns[$campaign->getId()] = array('name' => $campaign->getName(), 'start_date' => $campaign->getStartDate());
				}
			} else {
			}
			$selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + $page_limit);
		} while ($selector->getPaging()->getStartIndex() < $totalNumEntries);
    } catch (Exception $e) {
        vendor_log(' report Import error: (getCustomerCampaigns) - ' . $e->getMessage(), 'external');
    }

    return $accountCampaigns;
}
function getCustomerAdGroups($customerId)
{
    // #1+R
    $page_limit = 50;
    $adWordsServices = new AdWordsServices();
    $session = getSession($customerId);
    /*
    	$user = getAdwordsUser();
        $user->SetClientCustomerId($customerId);
    */
    $accountCampaigns = array();
    $campaignService = $adWordsServices->get($session, AdGroupService__class);
    //    $campaignService = $user->GetService('AdGroupService', ADWORDS_VERSION);
    $selector = new Selector();
    $selector->setFields(array('Id', 'Name', 'CampaignName'));
    $selector->setOrdering(array(new OrderBy('Name', SortOrder::ASCENDING)));
    $selector->setPaging(new Paging(0, $page_limit));
    /*
        $selector->fields = array('Id', 'Name','CampaignName');
        $selector->ordering[] = new OrderBy('Name', 'ASCENDING');
    
        // Create paging controls.
        $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);
    */
    $totalNumEntries = 0;
	try {
		do {
			$page = $campaignService->get($selector);
			if ($page->getEntries() !== null) {
				$totalNumEntries = $page->getTotalNumEntries();
				foreach ($page->getEntries() as $campaign) {
					//				print_r($campaign);
					/*
					
							if (isset($page->entries)) {
								foreach ($page->entries as $campaign) {
					 ##               printf("Campaign with name '%s' and ID '%s' was found.<br/>",
					 ##                   $campaign->name, $campaign->id);
					*/
					$accountCampaigns['' . $campaign->getId()] = array('name' => $campaign->getName(), 'campaignName' => $campaign->getCampaignName());
				}
			} else {
			}
			$selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + $page_limit);
		} while ($selector->getPaging()->getStartIndex() < $totalNumEntries);
		//   if($accountCampaigns)
    } catch (Exception $e) {
        vendor_log(' report Import error: (getCustomerAdGroups) - ' . $e->getMessage(), 'external');
    }

    return $accountCampaigns;
}
function getLastImpressions($customerId)
{
    // #1R
    $res = array('error' => '', 'body' => '');
    //print_r($data);exit;
    try {
        $fields = array('Impressions', 'Date');
        $adWordsServices = new AdWordsServices();
        $session = getSession($data['customerId']);
        /*
        		$user = getAdwordsUser();
                $user->SetClientCustomerId($customerId);
                $user->LoadService('ReportDefinitionService', ADWORDS_VERSION);
        */
        //	exit;
        // Create selector.
        $timeend = time();
        $timestart = $timeend - 90 * 86400;
        $dateRange = new DateRange(date('Ymd', $timestart), date('Ymd', $timeend));
        /*
                $dateRange->max = date("Ymd",$timeend);
                $dateRange->min = date("Ymd",$timestart);
        //print_r($dateRange);exit;		
        */
        $selector = new Selector();
        $selector->setFields($fields);
        $selector->setDateRange($dateRange);
        $selector->setPredicates(array(new Predicate('Impressions', PredicateOperator::GREATER_THAN, 0)));
        /*
                $selector->fields = $fields;
                $selector->dateRange = $dateRange;
        	    $selector->predicates[] = new Predicate('Impressions', 'GREATER_THAN', 0);
        */
        // Create report definition.
        $reportDefinition = new ReportDefinition();
        $reportDefinition->setSelector($selector);
        $reportDefinition->setReportName('Ad Performance Report #');
        $reportDefinition->setDateRangeType(ReportDefinitionDateRangeType::CUSTOM_DATE);
        $reportDefinition->setReportType(ReportDefinitionReportType::ACCOUNT_PERFORMANCE_REPORT);
        $reportDefinition->setDownloadFormat(DownloadFormat::CSV);
        /*
                $reportDefinition->selector = $selector;
                $reportDefinition->reportName = 'Ad Performance Report #';
                $reportDefinition->dateRangeType = 'CUSTOM_DATE';
                $reportDefinition->reportType = 'ACCOUNT_PERFORMANCE_REPORT';
                $reportDefinition->downloadFormat = 'CSV';
        
                // Exclude criteria that haven't recieved any impressions over the date range.
                $reportDefinition->includeZeroImpressions = FALSE;
        
                // Set additional options.
                $options = array('version' => ADWORDS_VERSION, 'returnMoneyInMicros' => FALSE );
        */
        // Download report.//$data['filePath']
        $reportDownloader = new ReportDownloader($session);
        $reportSettingsOverride = ($tmp = new ReportSettingsBuilder()) ? $tmp->includeZeroImpressions(false)->build() : $tmp->includeZeroImpressions(false)->build();
        $reportDownloadResult = $reportDownloader->downloadReport($reportDefinition, $reportSettingsOverride);
        $res['body'] = $reportDownloadResult->getAsString();
        /*
        		$ru=new ReportUtils();
                $res['body']=$ru->DownloadReport($reportDefinition, NULL, $user, $options);
        */
        $lines = array_reverse(split('
', trim($res['body'])));
        unset($res['body']);
        if (isset($lines[1])) {
            $res['last_impressions'] = str_getcsv($lines[1], ',');
        }
    } catch (Exception $e) {
        $res['error'] = $e->getMessage();
        $res['body'] = $e->getMessage();
        vendor_log('KPI report Import error: (getCustomReport) - ' . serialize($data) . '
			' . $res['body'], 'external');
    }
    return $res;
}
/*
function getCampaignPerformance($params=array()) {
// #1R

	$user = getAdwordsUser();
//	print_r($user);exit;
    $user->SetClientCustomerId($params['customer']);
    $dateRange = sprintf('%d,%d', $params['start'], $params['end']);

    $reportQuery = 'SELECT CallStartTime, CallEndTime, CallDuration, CallerNationalDesignatedCode FROM CALL_METRICS_CALL_DETAILS_REPORT '
        . 'WHERE CampaignName IN ["Click2Call"] '
        . 'DURING ' . $dateRange;//. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND

    // Set additional options.
    $options = array('version' => ADWORDS_VERSION);

    // Download report.
	
	//params
	$ru=new ReportUtils();	
    
	return $ru->DownloadReportWithAwql($reportQuery, NULL, $user, 'CSV',$options);
	
}
*/
function getAdgroupPerformance($params = array())
{
    // #1R
    $adWordsServices = new AdWordsServices();
    $session = getSession($params['customer']);
    $params['start'] = date('Ymd', $params['date_start_nix']);
    $params['end'] = date('Ymd', $params['date_end_nix']);
    $dateRange = sprintf('%d,%d', $params['start'], $params['end']);
    $reportQuery = 'SELECT AdGroupName, Cost, AdGroupId FROM ADGROUP_PERFORMANCE_REPORT ' . 'WHERE Cost > 0 ' . 'DURING ' . $dateRange;
    //. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND
    // Download report as a string.
    $reportDownloader = new ReportDownloader($session);
    // Optional: If you need to adjust report settings just for this one
    // request, you can create and supply the settings override here. Otherwise,
    // default values from the configuration file (adsapi_php.ini) are used.	
    $reportSettingsOverride = ($tmp = new ReportSettingsBuilder()) ? $tmp->includeZeroImpressions(false)->build() : $tmp->includeZeroImpressions(false)->build();
	$res="";
	try {
	    $reportDownloadResult = $reportDownloader->downloadReportWithAwql($reportQuery, DownloadFormat::CSV, $reportSettingsOverride);
		$res = $reportDownloadResult->getAsString();
    } catch (Exception $e) {
        vendor_log(' report Import error: (getCustomerAdGroups) - ' . $e->getMessage(), 'external');
    }

    return $res;
}
function getCallDetails($params = array())
{
    // #1R
    $adWordsServices = new AdWordsServices();
    $session = getSession($params['customer']);
    /*	$params['start'] = date("Ymd",$params['date_start_nix']);
    	$params['end'] = date("Ymd",$params['date_end_nix']);
    */
    $dateRange = sprintf('%d,%d', $params['start'], $params['end']);
    $reportQuery = 'SELECT CallStartTime, CallEndTime, CallDuration, CallerNationalDesignatedCode FROM CALL_METRICS_CALL_DETAILS_REPORT ' . 'WHERE CampaignName IN ["Click2Call"] ' . 'DURING ' . $dateRange;
    //. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND
    // Download report as a string.
    $reportDownloader = new ReportDownloader($session);
    // Optional: If you need to adjust report settings just for this one
    // request, you can create and supply the settings override here. Otherwise,
    // default values from the configuration file (adsapi_php.ini) are used.
    $reportSettingsOverride = ($tmp = new ReportSettingsBuilder()) ? $tmp->includeZeroImpressions(false)->build() : $tmp->includeZeroImpressions(false)->build();
    $reportDownloadResult = $reportDownloader->downloadReportWithAwql($reportQuery, DownloadFormat::CSV, $reportSettingsOverride);
    return $reportDownloadResult->getAsString();
}
function getUserLocations($params = array())
{
}
/*--------------------------------------*/
function GetAllClientCustomerIDs($details = false)
{
    // #1+R
    try {
        $page_limit = 500;
        $adWordsServices = new AdWordsServices();
        $session = getSession();
        $customerService = $adWordsServices->get($session, CustomerService__class);
        $info = $customerService->getCustomers();
        if (isset($info[0])) {
            $session = getSession($info[0]->getCustomerId());
        }
        $managedCustomerService = $adWordsServices->get($session, ManagedCustomerService__class);
        // Create selector.
        $selector = new Selector();
        $selector->setFields(array('CustomerId', 'Name'));
        //	  $selector->setOrdering([new OrderBy('CustomerId', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, $page_limit));
        /*
                $user = getAdwordsUser();
        
                $CustomerService = $user->GetService("CustomerService",ADWORDS_VERSION);
                $info = $CustomerService->getCustomers();
                if (isset($info[0]))
                    $user->SetClientCustomerId($info[0]->customerId);
        
                // Get the service, which loads the required classes.
                $managedCustomerService = $user->GetService('ManagedCustomerService', ADWORDS_VERSION);
                // Create selector.
                $selector = new Selector();
        
                // Specify the fields to retrieve.
                $selector->fields = array('CustomerId', 'Name');
        
                $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);
        */
        $clientCustomerIds = array();
        $childAccounts = array();
        do {
            $graph = $managedCustomerService->get($selector);
            if (!isset($_SESSION['totalNumOfAccounts'])) {
                $_SESSION['totalNumOfAccounts'] = $graph->totalNumEntries;
            }
            // Create links between manager and clients.
            if ($graph->getEntries() !== null) {
                $totalNumEntries = $graph->getTotalNumEntries();
                if ($graph->getLinks() !== null) {
                    //                if (isset($graph->links)) {
                    // get all customer ids
                    foreach ($graph->getLinks() as $link) {
                        //                    foreach ($graph->links as $link) {
                        /*
                                                if (!isset($_SESSION['managerCustomerId']))
                                                    $managerCustomerId = $link->managerCustomerId;
                        */
                        $clientCustomerIds[] = $link->getClientCustomerId();
                    }
                    // get all customer names to display on select box
                    foreach ($graph->getEntries() as $account) {
                        if (in_array($account->getCustomerId(), $clientCustomerIds)) {
                            $childAccounts[$account->getCustomerId()] = $account;
                        }
                    }
                }
            }
            //			print_r($selector);exit;
            $selector->getPaging()->setStartIndex($selector->getPaging()->getStartIndex() + $page_limit);
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);
        return $details ? $childAccounts : $clientCustomerIds;
    } catch (Exception $e) {
        printf('An error has occurred: %s

', $e->getMessage());
        header('Location: index.php?signout');
    }
}
/*

function queryAccount($data) {
  $user = getAdwordsUser();
  $user->SetClientCustomerId("4371554425");
  // Get the service, which loads the required classes.
  $campaignService = $user->GetService('ReportDefinitionService', ADWORDS_VERSION); //CampaignService

  $dateRange = sprintf('%d,%d', date('Ymd'), date('Ymd'));
  // Create AWQL query.
  $query = 'SELECT CampaignId, CampaignName, CampaignStatus, '
        . 'Impressions, Clicks FROM CAMPAIGN_PERFORMANCE_REPORT '
        . 'DURING ' . $dateRange; //Id, Name, Status,  // ORDER BY Name

  // Create paging controls.
  $offset = 0;

  do {
    $pageQuery = sprintf('%s LIMIT %d,%d', $query, $offset,
        AdWordsConstants::RECOMMENDED_PAGE_SIZE);
    // Make the query request.
    $page = $campaignService->query($pageQuery);

    // Display results.
    if (isset($page->entries)) {
		print_r($page->entries);exit;
		
      foreach ($page->entries as $campaign) {
		  print_r($campaign);exit;
      }
    } else {
      print "No campaigns were found.\n";
    }

    // Advance the paging offset.
    $offset += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
  } while ($page->totalNumEntries > $offset);	
}
*/
function getFinalUrls_new($params = array())
{
}
function getFinalUrls($params = array())
{
    // #1R
    $adWordsServices = new AdWordsServices();
    $session = getSession($params['customer']);
    /*
    	$user = getAdwordsUser();
        $user->SetClientCustomerId($params['customer']);
    */
    $dte = date('Ymd', strtotime('-7 day'));
    $dateRange = sprintf('%d,%d', $dte, date('Ymd'));
    /*
    	$ru=new ReportUtils();
        $options = array('version' => ADWORDS_VERSION);
    */
    //        . 'DURING ' . $dateRange;//. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND
    $reportQuery = 'select EffectiveFinalUrl FROM FINAL_URL_REPORT WHERE CampaignStatus IN [ENABLED] AND AdGroupStatus IN [ENABLED] AND Impressions > 0 AND AdvertisingChannelType IN [SEARCH,DISPLAY] DURING ' . $dateRange;
    //echo $reportQuery;
    $list = '';
    try {
        // Download report as a string.
        $reportDownloader = new ReportDownloader($session);
        // Optional: If you need to adjust report settings just for this one
        // request, you can create and supply the settings override here. Otherwise,
        // default values from the configuration file (adsapi_php.ini) are used.
        $reportSettingsOverride = ($tmp = new ReportSettingsBuilder()) ? $tmp->includeZeroImpressions(false)->build() : $tmp->includeZeroImpressions(false)->build();
        $reportDownloadResult = $reportDownloader->downloadReportWithAwql($reportQuery, DownloadFormat::CSV, $reportSettingsOverride);
    } catch (Exception $e) {
        $res['error'] = $e->getMessage();
        $res['body'] = $e->getMessage();
        vendor_log('getFinalUrls report error: (getFinalUrls) - 
			' . $res['body'], 'external');
    }
    return $reportDownloadResult->getAsString();
}

function getCampaignData($params=array()) {
// #1R

	$adWordsServices = new AdWordsServices();
	$session = getSession($params['customer']);

	//echo 'in';exit;

	/*
        $user = getAdwordsUser();
    //	print_r($user);exit;
        $user->SetClientCustomerId($params['customer']);
    */


	$dateRange = sprintf('%d,%d', $params['start'], $params['end']);
	$id=0;
	switch ($params['report']) {
		case "cost":
			$reportQuery = 'SELECT AdGroupId, AdGroupName, Cost FROM ADGROUP_PERFORMANCE_REPORT '
				. 'WHERE Cost>0 '//AdGroupId IN ['.implode(",",$params['campaigns']).'] '
				. 'DURING ' . $dateRange;//. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND
			$fields=array('id','group',$params['report']);
			break;
		case "url":
			$reportQuery = 'SELECT AdGroupId, AdGroupName, EffectiveFinalUrl FROM FINAL_URL_REPORT '
//		. 'WHERE AdGroupId IN ['.implode(",",$params['campaigns']).'] '
				. 'DURING ' . $dateRange;//. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND
			$fields=array('id','group',$params['report']);
			break;
		case "location"://AdGroupId,
			$reportQuery = 'SELECT Cost, MostSpecificCriteriaId, CountryCriteriaId, RegionCriteriaId FROM GEO_PERFORMANCE_REPORT '
				.' WHERE IsTargetingLocation IN [true,false] AND LocationType = LOCATION_OF_PRESENCE'
//        . 'WHERE CampaignName IN ["Click2Call"] '
				. ' DURING ' . $dateRange;//. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND
			$fields=array(/* 'group', */'cost', $params['report'],'country','region');
			$id=-1;
			break;

	}

	try {
		// Download report as a string.
		$reportDownloader = new ReportDownloader($session);
		// Optional: If you need to adjust report settings just for this one
		// request, you can create and supply the settings override here. Otherwise,
		// default values from the configuration file (adsapi_php.ini) are used.
		$reportSettingsOverride = ($tmp = new ReportSettingsBuilder()) ? $tmp->includeZeroImpressions(false)->build() : $tmp->includeZeroImpressions(false)->build();
		$reportDownloadResult = $reportDownloader->downloadReportWithAwql($reportQuery, DownloadFormat::CSV, $reportSettingsOverride);
//		print_r(parse_report($reportDownloadResult->getAsString(),$fields,$id));exit;
		return parse_report($reportDownloadResult->getAsString(),$fields,$id);
	} catch (Exception $e) {
		$res['error'] = $e->getMessage();
		$res['body'] = $e->getMessage();
		vendor_log('getCampaignData report error: (getCampaignData) -
			' . $res['body'], 'external');
	}
	/*
        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.

        //params
        $ru=new ReportUtils();
    //	echo $reportQuery;exit;
        return parse_report($ru->DownloadReportWithAwql($reportQuery, NULL, $user, 'CSV',$options),$fields,$id);
    */
}

function parse_report($body,$fields=array(),$id=0) {
	$items=array();
	$lines=explode("\n",$body);
	if (sizeof($lines)>3) {
		for ($i = 2; $i < sizeof($lines) - 1; $i++) {
			//						echo $lines[2];
			$tmp = str_getcsv($lines[$i], ",", '"');
			if ($id>=0) {
				$items[$tmp[$id]]=array();
				foreach ($fields as $j=>$fld)
					$items[$tmp[$id]][$fld]=$tmp[$j];
			} else {
				$itm=array();
				foreach ($fields as $j=>$fld)
					$itm[$fld]=$tmp[$j];
				$items[]=$itm;
			}
//			exit;
		}
	}
	return $items;
}
//**********************************************************
function DownloadCampaignPerformanceHourOfDayReportWithAwql($customer, $filePath, $dateRange)
{
    // #1R
    $adWordsServices = new AdWordsServices();
    $session = getSession($customer);
    // Create report query.
    $reportQuery = 'SELECT AccountDescriptiveName, CampaignId, CampaignName, BudgetId, ' . 'Impressions, Clicks, AccountCurrencyCode, Amount, HourOfDay FROM CAMPAIGN_PERFORMANCE_REPORT ' . 'WHERE CampaignStatus IN [ENABLED] AND Impressions > 0 ' . 'DURING ' . $dateRange;
    //. 'ORDER BY HourOfDay DESC LIMIT 0,1' CampaignId IN [' . $campaignId . '] AND
    //echo $reportQuery ;exit;
    // Set additional options.
    // Download report as a string.
    $reportDownloader = new ReportDownloader($session);
    // Optional: If you need to adjust report settings just for this one
    // request, you can create and supply the settings override here. Otherwise,
    // default values from the configuration file (adsapi_php.ini) are used.
    $reportSettingsOverride = ($tmp = new ReportSettingsBuilder()) ? $tmp->includeZeroImpressions(false)->build() : $tmp->includeZeroImpressions(false)->build();
    $reportDownloadResult = $reportDownloader->downloadReportWithAwql($reportQuery, DownloadFormat::CSV, $reportSettingsOverride);
    $reportDownloadResult->saveToFile($filePath);
}