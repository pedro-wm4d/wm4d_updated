<?
require 'vendor/autoload.php';

//$hapikey = "demo";
$hapikey = "86b7f763-6c0a-4563-91c3-f2b8c6c25287";
$hapikey = "28bcf809-3a13-46d2-be91-a33e9c62e2a1";


$hubspot = new Fungku\HubSpot($hapikey);

// get 5 contacts' firstnames, offset by 50
$contacts = $hubspot->contacts()->get_all_contacts(array(
//    'count' => 5, // defaults to 20
//    'property' => 'firstname', // only get the specified properties
//    'vidOffset' => '50' // contact offset used for paging
));
//print_r($contacts);

$ct=$hubspot->contacts()->get_contact_by_id(30701);
//print_r((array)$ct->properties);
foreach ((array)$ct->properties as $nme=>$obj) {
    echo "$nme:".$obj->value."\n";
}
exit;
print_r($hubspot->contacts()->get_contact_by_id(30701));
?>