<?php
function add_js_extra() {
?>
						  htmWeb    += '<div style="max-width:500px;display:none"><a href=\"https://je126.infusionsoft.com/Contact/manageContact.jsp?view=edit&ID='+markers[i]["f_cid"]+'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/infusionsoft.png" alt="Infusionsoft" title="Link to infusionsoft record"/></a>';
/*
						  if(email.length  > 0) htmWeb    += '<div class="sl_pad5 sl_clear"><a href=\"http://mailman.wm4d.com/edit-alias.php?'+ email +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/postfix.png" alt="Postfix" title="Postfix record for"+ email +""/></a></span></div>';
*/
						  htmWeb    += '<a href=\"http://maps.google.com/?q='+ address +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/googlemaps.png" alt="Google Maps" title="Google Maps"/></a>';

						  htmWeb    += '<a href=\"http://maps.google.com/maps?q=&layer=c&cbll='+Latt+','+Lngg+'&cbp=11,0,0,0,0\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/streetview.png" alt="Google Streetview" title="Google Maps Streetview"/></a>';
/*
						  if(markers[i]["zip_code"].length     > 0) htmWeb    += '<div class="sl_pad5 sl_clear"><label style="font-weight:bold; display: block; float: left; margin: 0; width: 63px;">City Data</label><span><b>:</b> <a href=\"http://www.city-data.com/zips/'+markers[i]["zip_code"]+'.html&cbp=11,0,0,0,0\" target="_blank">View</a></span></div>';
*/
						  if(markers[i]["f_company"].length     > 0) 
						  htmWeb    += '<a href=\"https://www.google.com/?gws_rd=ssl#q=%22'+ markers[i]["f_company"] +'%22&safe=off\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/googlesearch.png" alt="Google Search on Company Name" title="Google Search on Company Name"/></a>';

						  htmWeb    += '<a href=\"https://www.google.com/?gws_rd=ssl#q=%22'+ markers[i]["f_first_name"]+ '+' + markers[i]["f_last_name"] +'%22&safe=off\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/google2.jpg" alt="Google Search on Name" title="Google Search on Name"/></a>';

						  htmWeb    += '<a href=\"http://mx.wm4d.com/emails?q='+ email +'&start_date=&end_date=&all_dates=1\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/call_scorer_email_leads.png" alt="Call scorer Email leads" title="Call scorer Email leads"/></a>';

						  if(markers[i]["f_last_name"].length     > 0) htmWeb    += '<a href=\"http://www.intelius.com/results.php?ReportType=1&formname=name&qf='+markers[i]["f_first_name"]+'&qmi=&qn='+markers[i]["f_last_name"]+'&qcs='+ markers[i]["city"] +'%2C+'+ markers[i]["state"] +'+&focusfirst=1\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/intellius.jpg" alt="Intelius Person Data" title="Intelius Person Data"/></a>';

						  htmWeb    += '<a href=\"http://www.whitepages.com/phone/'+Phone+'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/whitepages.png" alt="WhitePages Reverse phone" title="WhitePages Reverse phone"/></a>';

						  htmWeb    += '<a href=\"http://www.yelp.com/search?find_desc='+ name +'&find_loc='+ markers[i]["city"] +'%2C+'+ markers[i]["state"] +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/yelp-icon.png" alt="Yelp" title="Yelp"/></a>';

						  htmWeb    += '<a href=\"https://plus.google.com/s/'+name+'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/googleplus.png" alt="Google Plus" title="Google Plus"/></a>';

						  htmWeb    += '<a href=\"http://www.dandb.com/businessdirectory/search/?keyword='+Phone+'&submit=\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/dun_bradstreet.jpg" alt="Dun & Bradstreet Credibility Report" title="Dun & Bradstreet Credibility Report"/></a>';

						  htmWeb    += '<a href=\"http://www.hoovers.com/company-information/company-search.html?term='+ name +'%20'+ markers[i]["city"] +',%20'+markers[i]["state"]+'&submit=\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/hoovers.jpg" alt="Hoovers business search" title="Hoovers business search"/></a>';

						  htmWeb    += '<a href=\"http://www.yellowpages.com/glenview-il/dental?g='+ markers[i]["city"] +'%2C%20'+ markers[i]["state"] +'&q='+ name +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/yellow_pages.jpg" alt="Yellow pages Competitors" title="Yellow pages Competitors"/></a>';

						  htmWeb    += '<a href=\"http://www.yellowbook.com/yellow-pages?what='+ name +'&where='+ markers[i]["city"] +'%2C+'+ markers[i]["state"] +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/yellow_book.jpg" alt="YellowBook Competitors" title="YellowBook Competitors"/></a>';
/*
						  htmWeb    += '<a href=\"https://mail.google.com/mail/?view=cm&fs=1&to=someone@example.com&su=SUBJECT&body=BODY&bcc='+ email +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/Gmaillogo.png" alt="Email Client {requires email from infusionsoft}" title="Email Client {requires email from infusionsoft}"/></a>';
*/
						  if(email.length  > 0) 
						  htmWeb    += '<a href=\"http://mailman.wm4d.com/edit-alias.php?address='+ email +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/postman.jpg" alt="Postfix Mailman" title="Postfix Mailman"/></a>';

						  if(markers[i]["zip_code"].length     > 0) htmWeb    += '<a href=\"http://www.city-data.com/zips/'+ markers[i]["zip_code"] +'.html\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/City-data.png" alt="City Data" title="City Data"/></a>';

						  htmWeb    += '<a href=\"https://graph.facebook.com/search?q='+ email +'&type=user&access_token=\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/facebook.png" alt="Facebook Search" title="Facebook Search"/></a>';

						  htmWeb    += '<a href=\"
http://www.healthgrades.com/provider-search-directory/search?q='+ markers[i]["f_first_name"]+ '+' + markers[i]["f_last_name"] +'&prof.type=dentist&search.type=&entityCode=&method=&loc='+ markers[i]["zip_code"] +'&pt='+ markers[i]["lng"] +'%2C-'+ markers[i]["lat"] +'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/healthgrades.jpg" alt="Healthgrades Dentist Lookup" title="Healthgrades Dentist Lookup"/></a>';

						  htmWeb    += '<a href=\"
https://www.youtube.com/results?search_query='+ markers[i]["f_first_name"]+ '+' + markers[i]["f_last_name"]+'\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/youtube2.jpg" alt="YOUTUBE Person" title="YOUTUBE Person"/></a>';

  						  if(markers[i]["f_company"].length     > 0) 
						  htmWeb    += '<a href=\"
https://www.youtube.com/results?search_query='+ markers[i]["f_company"]+ '\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/youtube.png" alt="Youtube Company" title="Youtube Company"/></a>';

						  htmWeb    += '<a href=\"http://www.linkedin.com/pub/dir/?first='+ markers[i]["f_first_name"]+ '&last='+ markers[i]["f_last_name"]+ '&search=Search&searchType=fps\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/linkedin.jpg" alt="LINKED IN" title="LINKED IN"/></a>';

						  htmWeb    += '<a href=\"http://www.bing.com/search?q='+ markers[i]["f_first_name"]+ '+'+ markers[i]["f_last_name"]+ '\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/bing.jpg" alt="BING Person Search" title="BING Person Search"/></a>';

  						  if(markers[i]["f_company"].length     > 0) 
						  htmWeb    += '<a href=\"http://www.bing.com/search?q='+ markers[i]["f_company"]+ '\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/bing2.png" alt="BING Company Search" title="BING Company Search"/></a>';

						  htmWeb    += '<a href=\"http://www.bing.com/maps/?where=%22'+ markers[i]["zip_code"]+ '%20'+ markers[i]["address"]+ ',%20'+ markers[i]["city"]+ ',%20'+ markers[i]["state"]+ ',%20'+ markers[i]["country"]+ '%22\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/bing3.png" alt="Bing Maps - For Birds eye" title="Bing Maps - For Birds eye"/></a>';

						  htmWeb    += '<a href=\"https://twitter.com/search?q='+ markers[i]["f_first_name"]+ '%20'+ markers[i]["f_last_name"]+ '&src=typd\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/twitter1.jpg" alt="Twitter Person" title="Twitter Person"/></a>';

  						  if(markers[i]["f_company"].length     > 0) 
						  htmWeb    += '<a href=\"https://twitter.com/search?q='+ markers[i]["f_company"]+ '&src=typd\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/twitter2.jpg" alt="Twitter Business" title="Twitter Business"/></a>';

						  htmWeb    += '<a href=\"https://duckduckgo.com/?q='+ markers[i]["f_first_name"]+ '+'+ markers[i]["f_last_name"]+ '\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/duckduckgo2.jpg" alt="DuckDuck go Person" title="DuckDuck go Person"/></a>';

  						  if(markers[i]["f_company"].length     > 0) 
						  htmWeb    += '<a href=\"https://duckduckgo.com/?q='+ markers[i]["f_company"]+ '\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/duckduckgo1.jpg" alt="DuckDuck Go Company" title="DuckDuck Go Company"/></a>';

						  htmWeb    += '<a href=\"http://www.mouthhealthy.org/en/find-a-dentist/search-results?rdAddress='+ markers[i]["zip_code"]+ '&rdSpecialty=all%20types%20of%20dentistry&rdDistance=100&lastname='+ markers[i]["f_last_name"]+'&photo=\" target="_blank"><img src="http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/logo_ada.gif" alt="ADA Member dentists" title="ADA Member dentists"/></a></div>';


/*

						  ## Put behind email
						  Tooltop for Icon: Email Client {requires email from infusionsoft}
						  https://mail.google.com/mail/?view=cm&fs=1&to=someone@example.com&su=SUBJECT&body=BODY&bcc=someone.else@example.com
						  Gmaillogo.png


						  ##Behind Last Icon Above
						  Tooltip for Icon: Postfix Mailman {requires Reporting Address from infusionsoft}
						  http://mailman.wm4d.com/edit-alias.php?address=reporting_aamco-north-miami%40100marketers.com
						  postman.jpg

						  Tooltip for Icon: City Data {requires zip from infusionsoft}
						  http://www.city-data.com/zips/33009.html
						  City-data.png

						  Tooltip for Icon: Google Search on Company Name {requires company name from infusionsoft} (%22 = quotes)
						  https://www.google.com/?gws_rd=ssl#q=%22Glenview+Family+Dental+Center%22&safe=off
						  http://www.wm4ddev9.com/wp-content/plugins/ajax-store-locator-wordpress/images/extra/googlesearch.png

						  Tooltip for Icon: Google Search on Name {requires first+last name from infusionsoft} (%22 = quotes)
						  https://www.google.com/?gws_rd=ssl#q=%22richard+galitz%22&safe=off
						  google2.jpg

						  https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=open%20graph%20php%20example
						  Tooltip for Icon: Facebook Search {requires email from infusionsoft}
						  Simply use the graph API with this url format:
						  https://graph.facebook.com/search?q=zuck@fb.com&type=user&access_token=...
						  You can easily create an application and grab an access token for it here:
						  https://developers.facebook.com/apps
						  I believe you get an estimated 600 requests per 600 seconds, although this isn't documented.
						  facebook.png

						  Tooltip for Icon: Healthgrades Dentist Lookup {Requires First+LastName, zip from infusionsoft}{longitude and latitude from map db}
						  http://www.healthgrades.com/provider-search-directory/search?q=Alan+Woodson&prof.type=dentist&search.type=&entityCode=&method=&loc=91786&pt=34.103846%2C-117.66391
						  http://www.healthgrades.com/provider-search-directory/search?q=FIRST+LAST&prof.type=dentist&search.type=&entityCode=&method=&loc=ZIP&pt=LONGITUDE%2C-LATITUDE
						  healthgrades.jpg

						  Google earth
						  <form action="http://kml4earth.appspot.com/circle.gsp" method="POST">
						  <input type="hidden" name="radius" value="30">
						  <input type="hidden" name="units" value="miles">
						  <input type="hidden" name="lat" size="4" value="LATITUDE">
						  <input type="hidden" name="lon" size="4" value="ff0000ff">
						  <input type="hidden" name="color" value="toto98">
						  <a href="#" onclick="document.forms[0].submit();return false;"><img src="google_earth.jpg" /></a>
						  </form>
						  google_earth.jpg

						  YOUTUBE Person
						  https://www.youtube.com/results?search_query=First+Last
						  youtube2.jpg

						  Youtube Company
						  https://www.youtube.com/results?search_query=company+name
						  youtube.png

						  LINKED IN
						  http://www.linkedin.com/pub/dir/?first=David&last=Chehebar&search=Search&searchType=fps
						  linkedin.jpg

						  BING Person Search
						  http://www.bing.com/search?q=FIRST+LAST
						  bing.jpg

						  BING Company Search
						  http://www.bing.com/search?q=COMAPNY+NAME
						  bing2.png

						  Bing Maps - For Birds eye
						  http://www.bing.com/maps/?q=%221573%20West%2049th%20Street,%20Hialeah,%20FL,%20United%20States%22
						  bing3.png

						  Twitter Person
						  https://twitter.com/search?q=First%20Last&src=typd
						  twitter1.jpg

						  Twitter Business
						  https://twitter.com/search?q=Business%20Name&src=typd
						  twitter2.jpg

						  DuckDuck go Person
						  https://duckduckgo.com/?q=David+Chehebar
						  duckduckgo2.jpg

						  DuckDuck Go Company
						  https://duckduckgo.com/?q=Company+Name
						  duckduckgo1.jpg

						  ADA Member dentists
						  http://www.mouthhealthy.org/en/find-a-dentist/search-results?rdAddress=ZIP&rdSpecialty=all%20types%20of%20dentistry&rdDistance=100&lastname=LASTNAME&photo=
						  logo_ada.gif

*/


<?php 
}
?>