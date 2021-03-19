<?php function ddd() { ?>
<script>
<?php
}
function js_get_vars($cats)
{
    ?>
		  var infoWindowZip = new google.maps.InfoWindow;
		  var colorSchemeMax=2;
		  var colorScheme=0;
		  var colorSchemes=['#9999ff','#ff9999','#ccff99'];
		  var loadAdwords=true;
<?php
}

function js_get_more_info()
{
    ?>
				if (loadAdwords) {
						var moreInfo='<div class="mapres-subinfo">'+directionhtm+'</div>';
 						if ("procedures" in markers[i] && markers[i]['procedures'].length>0) {
							moreInfo='';
							for (var j = 0; j < markers[i]['procedures'].length; j++)						
								moreInfo+='<div class="sl_pad2"><label>'+markers[i]['procedures'][j]['group']+'</label><span> Cost: $'+markers[i]['procedures'][j]['cost']+' <a href="'+markers[i]['procedures'][j]['url']+'" target="_blank">View</a></span></div>';
							if (markers[i]['procedures'].length>0) moreInfo = '<div class="mapres-subinfo">'+directionhtm+'<div class="colorBox" style="background-color: '+colorSchemes[colorScheme]+'"></div></div><div class="option-procedures-wrapper"><div class="option-heading"><div class="arrow-up" style="display: none;"><span class="option-heading-btn"><i class="mapres-sprite minus-icon"></i> more info</span></div><div class="arrow-down" style="display: block;"><span class="option-heading-btn"><i class="mapres-sprite plus-icon"></i> more info</span></div></div><div class="option-procedures" style="display: block;">' + moreInfo+ '</div></div>';

								
							var zipCodes={};	
							markers[i]['heatmap'] = new google.maps.Data();	
							markers[i]['heatmap'].loadGeoJson('http://wm4dmap.com/wp-admin/admin-ajax.php?action=crm2map_geojson&c[]='+markers[i]['adwords_id']+'&s='+colorScheme+'&d='+Date.now(),{"idPropertyName":"title"},function(features) {for (var k = 0; k < features.length; k++) {zipCodes[features[k].getProperty('title')]=1};});
/*,function(features) {for (var k = 0; k < features.length; k++) {zipCodes[features[k].getProperty('title')]=1};}*/							
							colorScheme++;

							if (colorScheme>colorSchemeMax) colorScheme=0;
/*
								zipCodes[feature.getProperty('title')]=1;
*/
							
																				
							markers[i]['heatmap'].setStyle(function (feature) {
								res= getFeatureStyle(feature);
								res['visible']=false;
								return res;
							});

							markers[i]['heatmap'].addListener('mouseover', function(event) {
								this.revertStyle();
								this.overrideStyle(event.feature, {strokeColor: event.feature.getProperty('highlightColor'),zIndex:55});
								if (infoWindowZip.getMap()== null) {
									fid=event.feature.getProperty('title');
									var contentString = "Zip code: <b>"+fid+"</b> <br />";
									if (typeof(activeZipcodes[fid]) != 'undefined' && activeZipcodes[fid].length>1) {
										for (var q = 0; q < activeZipcodes[fid].length; q++) {
											contentString += "<b>"+activeZipcodes[fid][q].getProperty('name')+"</b><br>";											
											contentString += "Cost: <b>"+activeZipcodes[fid][q].getProperty('cost')+"</b><br>";											
										};										
									} else {									
										contentString += "<b>"+event.feature.getProperty('name')+"</b><br>";											
										contentString += "Cost: <b>"+event.feature.getProperty('cost')+"</b>";
									};
									infoWindowZip.setContent(contentString);
									infoWindowZip.setPosition(event.latLng);
									infoWindowZip.open(map);
								};
							});
							markers[i]['heatmap'].addListener('mouseout', function(event) {
								this.revertStyle();
								infoWindowZip.close();
							});
							markers[i]['heatmap'].setMap(map);	
							marker['heatmap']=markers[i]['heatmap'];
							marker['id']=markers[i]['id'];
							marker['name']=markers[i]['name'];




						} else {
							moreInfo+='No adwords data';
						}
				}
<?php
}
function js_add2box()
{
    ?>
				if (loadAdwords) {
						html_info +=moreInfo;
						html +=moreInfo;
				};

<?php
}
?>