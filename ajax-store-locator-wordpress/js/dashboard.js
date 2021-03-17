(function ($) {
	$.fn.hasScrollBar = function () {
		return this.get(0).scrollHeight > this.height();
	}
})(jQuery);

if (typeof giz_Locator == "undefined") {
	var giz_Locator = {};
}
giz_Locator.version = "1.0.0";
giz_Locator.title = 'Wordpress Store Locator';
giz_Locator.path = window.location.protocol + "//" + window.location.host + "/";
giz_Locator.selval = '';
String.prototype.capitalize = function () {
	return this.replace(XRegExp("^\\p{L}"), function ($0) { return $0.toUpperCase(); })
	return this.toLowerCase().replace(/(^|\s)([a-z])/g, function (m, p1, p2) { return p1 + p2.toUpperCase(); });
	return this.charAt(0).toUpperCase() + this.slice(1);
}

if (typeof String.prototype.startsWith != 'function') {
	String.prototype.startsWith = function (str) {
		return this.indexOf(str) == 0;
	};
}
if (typeof String.prototype.startsWith != 'function') {
	String.prototype.startsWith = function (str) {
		return this.slice(0, str.length) == str;
	};
}
if (typeof String.prototype.endsWith != 'function') {
	String.prototype.endsWith = function (str) {
		return this.slice(-str.length) == str;
	};
}
String.prototype.QueryStringToJSON = function () {
	href = this;
	qStr = href.replace(/(.*?\?)/, '');
	qArr = qStr.split('&');
	stack = {};
	for (var i in qArr) {
		var a = qArr[i].split('=');
		var name = a[0],
		value = isNaN(a[1]) ? a[1] : parseFloat(a[1]);
		if (name.match(/(.*?)\[(.*?)]/)) {
			name = RegExp.$1;
			name2 = RegExp.$2;
			if (name2) {
				if (!(name in stack)) {
					stack[name] = {};
				}
				stack[name][name2] = value;
			} else {
				if (!(name in stack)) {
					stack[name] = [];
				}
				stack[name].push(value);
			}
		} else {
			stack[name] = value;
		}
	}
	return stack;
}

giz_Locator.util = {
	init: function () {
		$('td:has(img.imagebutton)').addClass('relative');
		$('.imagebutton').each(function () {
			var that = $(this).clone().addClass('hoverimage').removeClass('imagebutton');
			$(this).hover(function (e) {
				if (($('body').height() < cursor.y + 300) && ($('body').height() > 420))
					that.css({ 'top': cursor.y - 300 });
				$(this).after(that);
			}, function () { that.remove(); });
		});
	},
	setHash: function (str) {
		if (str.charAt(0) != '#')
			return '#' + str;
		else
			return str;
	},
	escape: function (str) {
		return str.replace(/'/g, "\'");
	},
	getQuery: function (query) {
		query = query.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var expr = "[\\?&]" + query + "=([^&#]*)";
		var regex = new RegExp(expr);
		var results = regex.exec(window.location.href);
		if (results !== null) {
			return results[1];
			return decodeURIComponent(results[1].replace(/\+/g, " "));
		} else {
			return false;
		}
	},
	tbMaxLength: function (id, msgid) {
		charsize = 140;
		if (document.getElementById(id).value.length <= charsize) {
			document.getElementById(msgid).innerHTML = "Characters left " + String(charsize - parseInt(document.getElementById(id).value.length));
		}
		else {
			document.getElementById(msgid).innerHTML = "Characters exceeded";
		}
	},
	isAlphanumeric: function (str) {
		return /^[0-9a-zA-Z]+$/.test(str);
	},
	loadUI: function(msg){
		$.blockUI({ message: "<h3>"+ msg +"</h3>",css: { 
						border: 'none', 
						padding: '15px', 
						backgroundColor: '#000', 
						'-webkit-border-radius': '10px', 
						'-moz-border-radius': '10px', 
						opacity: .5, 
						color: '#fff' 
					} });
	},
	ajaxFile: function (u, t, d, s, Rmsg, isLoad) {
		$.ajax({
			url: u,
			type: t,
			data: d,
			processData: false,
			contentType: false,
			beforeSend: function () {
			},
			complete: function () {
			isLoad = (isLoad == null) ? true : false;
				if(isLoad){
					setTimeout(function() {
						$.unblockUI({ 
							onUnblock: function(){ 
								$.blockUI({ message: "<h3 class='sloc_ajax_message_success'>"+ Rmsg +"</h3>",css: { 									
									padding: '12px', 
									backgroundColor: '#fff',
									border:	'3px solid #aaa',					
									opacity: 1, 
									color: '#fff' 
									},
									timeout : 2000
								});
							}						
						}); 
					}, 2000);
				}
			},
			success: function (msg) {
				if (typeof s === 'function')
					s(msg);
				else{
					return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajax: function (u, t, d, s, Rmsg, isLoad) {
		$.ajax({
			url: u,
			type: t,
			data: d,
			beforeSend: function () {
			},
			complete: function () {
			isLoad = (isLoad == null) ? true : false;
				if(isLoad){
					setTimeout(function() {
							$.unblockUI({ 
								onUnblock: function(){ 
									$.blockUI({ message: "<h3 class='sloc_ajax_message_success'>"+ Rmsg +"</h3>",css: { 
										padding: '12px', 
										backgroundColor: '#fff',
										border:	'3px solid #aaa',					
										opacity: 1, 
										color: '#fff' 
										},
										timeout : 2000
									});  
								}						
							}); 
						}, 2000);
				}
			},
			success: function (msg) {
				if (typeof s === 'function')
					s(msg);
				else{
						return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajaxWithoutLoad: function (u, t, d, s, Rmsg) {
		$.ajax({
			url: u,
			type: t,
			data: d,
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (msg) {
				if (typeof s === 'function')
					s(msg);
				else{
						return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajaxComplete: function (u, t, d, s, Rmsg, cFun) {
		$.ajax({
			url: u,
			type: t,
			data: d,
			beforeSend: function () {
			},
			complete: function () {
				setTimeout(function() {
					$.unblockUI({ 
						onUnblock: function(){ 
								$.blockUI({ message: "<h3 class='sloc_ajax_message_success'>"+ Rmsg +"</h3>",css: { 
									padding: '12px', 
									backgroundColor: '#fff',
									border:	'3px solid #aaa',					
									opacity: 1, 
									color: '#fff' 
									},
									timeout : 2000
								}); 
						}						
					}); 
				}, 2000);
			},
			success: function (msg) {
				if (typeof s === 'function')
					s(msg);
				else{
						return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajaxSer: function (u, t, d, s) {
		$.ajax({
			url: u,
			type: t,
			data: d,
			dataType: "json",
			beforeSend: function (x) {
				if(x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
				}
			},
			success: function (msg,status,shr) {
				if (typeof s === 'function'){
					s(msg);
				}
				else{
					return msg;
				}
			},
			error: function (xmlHttpRequest, textStatus, errorThrown) {
				debugger;
			}
		});
	},
	ajxBlockUI: function(loder_msg, isloader, className, plugin_url) {
		isloader = (isloader == null || isloader == false) ? false : true;
		var full_html = "<h3 class='sloc_ajax_message_"+ className +"'><img src='"+ plugin_url +"images/loader.gif' alt='' /><span class='sloc_ajx_span'>"+ loder_msg +"</span></h3>";
		if(!isloader){
			full_html = "<h3 class='sloc_ajax_message_"+ className +"'><span class='sloc_ajx_span' style='top:0px;'>"+ loder_msg +"</span></h3>";
		}
		$.blockUI({ 
			message: full_html,
			css: { 
					border: 'none', 
					padding: '6px', 
					backgroundColor: '#fff',
					border:	'3px solid #aaa',					
					opacity: 1, 
					color: '#fff' 
			}
		});
	},
	ajxBlockUIAuto: function(loder_msg, isloader, className, timeout, plugin_url) {
		if(timeout == null || timeout == "")
		timeout = 2000;
		isloader = (isloader == null || isloader == false) ? false : true;
		var full_html = "<h3 class='sloc_ajax_message_"+ className +"'><img src='"+ plugin_url +"images/loader.gif' alt='' /><span class='sloc_ajx_span'>"+ loder_msg +"</span></h3>";
		if(!isloader){
			full_html = "<h3 class='sloc_ajax_message_"+ className +"'><span class='sloc_ajx_span' style='top:0px;'>"+ loder_msg +"</span></h3>";
		}
		setTimeout(function() {
				$.unblockUI({ 
					onUnblock: function(){ 
						$.blockUI({ 
							message: full_html,
							css: { 
									border: 'none', 
									padding: '6px', 
									backgroundColor: '#fff',
									border:	'3px solid #aaa',					
									opacity: 1, 
									color: '#fff' 
							},
							timeout : timeout
						}); 
					}						
				});
		}, timeout);
	},
	blockOverlay : function(customMsg){
		$.blockUI({ message: customMsg,css: { 
				border: 'none', 
				padding: '15px', 
				backgroundColor: '#000', 
				'-webkit-border-radius': '10px', 
				'-moz-border-radius': '10px', 
				opacity: .5, 
				color: '#fff' 
			} });
	},
	firstCharUpper: function (str) {
		var pieces = str.split(" ");
		for (var i = 0; i < pieces.length; i++) {
			var j = pieces[i].charAt(0).toUpperCase();
			pieces[i] = j + pieces[i].substr(1);
		}
		return pieces.join(" ");
	},
	getUrl: function () {
		var your_url = prompt('Enter your web page URL!', 'http://');
		var is_protocol_ok = your_url.indexOf('http://');
		var is_dot_ok = your_url.indexOf('.');
		if ((is_protocol_ok == -1) || (is_dot_ok == -1)) {
			alert('Error: Your url should begin with http:// and have at least one dot (.)!');
			get_url();
		}
		else
			alert('Thanks!');
	},
	truncate: function (str, maxlength) {
		if (str.length > maxlength) {
			str = str.substr(0, maxlength - 3) + '...';
		}
		return str;
	},
	toggle_visibility: function (id, id2) {
		var e = document.getElementById(id);
		var h = document.getElementById(id2);
		if (e.style.display == 'block') {
			e.style.display = 'none';
			h.innerHTML = "+";
		}
		else {
			e.style.display = 'block';
			h.innerHTML = "-";
		}
	},
	decode: function (s) {
		try {
			return decodeURIComponent(s).replace(/\r\n|\r|\n/g, "\r\n");
		} catch (e) {
			return "";
		}
	},
	launchExecutable: function (executableFullPath) {
		var shellActiveXObject = new ActiveXObject("WScript.Shell");
		shellActiveXObject.Run(executableFullPath, 1, false);
		shellActiveXObject = null;
	},
	updated: function () {
		setTimeout(function () { tb_remove(); }, 2000);
	},
	table: function (msg, ele) {
		var table = '<table cellspacing="0" cellpadding="10"><thead><tr><th>Artist</th><th>Company</th><th>Title</th><th>Price</th></thead><tbody>';
		for (var cd in msg) {
			var row = '<tr><td>' + msg[cd].JoinName + '</td></tr>';
			table += row;
		}
		table += '</tbody></table>';
		$(ele).html(table);
	},
	ajaxauto: function (eleId, ajType, ajcontentType, ajUrl, ajdata, ajdataType) {
		$(eleId).autocomplete({
			source: function (request, response) {
				$.ajax({
					type: ajType,
					contentType: ajcontentType,
					url: ajUrl,
					data: "{'" + ajdata + "':'" + request.term + "'}",
					dataType: ajdataType,
					async: true,
					success: function (data) {
						response(data.d);
					},
					error: function (result) {
						giz_Locator.gv.errorMsg("Due to unexpected errors we were unable to load data", 0);
					}
				});
			},
			minLength: 1
		});
	},
	jqtrans: function (eleId) {
		$(eleId).jqTransform({ imgPath: '../images/jqimg/' });
	},
	gourl: function (url) {
		location.path = url;
	},
	scroller: function (id) {
		var sidebar_top_limit = 100;
		var sidebar_top_margin = 20;
		var sidebar_slide_duration = 500;
		$(window).scroll(function () {
			offset = $(document).scrollTop() + sidebar_top_margin;
			if (offset < sidebar_top_limit)
				offset = sidebar_top_limit;
			$(id).animate({ top: offset }, { duration: sidebar_slide_duration, queue: false });
		});
	},
	changeClass: function () {
		var key_event = $('input[type="text"], input[type="password"]').each(function (i, el) {
			if (el.value) {
				$(this).removeClass("inputb").addClass("input_txt");
			}
			else {
				$(this).removeClass("input_txt").addClass("inputb");
			}
		}).blur(function () {
			text = $.trim($(this).val());
			if (text == "") {
				$(this).removeClass("input_txt").addClass("inputb").val(text);
			}
			else {
				$(this).addClass("input_txt");
			}
		});
	},
	clearform: function (form) {
			$(form).find('input:text, input:password, input:file, select').val('');
			$(form).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
			$(form).find('span.error').css({ "display": "none" });
	},
	validateFileExt: function (id) {
		var fup = document.getElementById(id);
		var fileName = fup.value;
		var ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
		if (ext == "gif" || ext == "jpeg" || ext == "jpg" || ext == "doc") {
			return true;
		}
		else {
			alert("Upload Gif or Jpg images only");
			fup.focus();
			return false;
		}
	},
	selectbytext: function (sel, txt) {
		if (typeof sel == 'string') {
			sel = document.getELementById(sel) || document.getELementsByName(sel)[0];
		}
		var opts = sel.options;
		for (var i = 0, L = opts.length; i < L; i++) {
			if (opts[i].text == txt) {
				sel.selectedIndex = i;
				break;
			}
		}
		return i;
	},
	doConvert: function (txt) {
		return txt.toLowerCase().replace(/ /g, '-');
	},
	jtSelect: function (jtselvalue) {
		giz_Locator.selval = jtselvalue;
	},
	jtGetvalue: function () {
		return giz_Locator.selval;
	},
	docView: function (file) {
		window.open(giz_Locator.path + 'Viewer.htm?u=' + giz_Locator.path + file);
	},
	phoneMask: function (ctrlId) {
		$('#' + ctrlId).mask("(999)-(999)-(9999)");
	}	
};

giz_Locator.gv = {
	escape: function (str) {
		return str.replace(/'/g, "\'");
	},
	remove: function (uniqueID, rowid) {
		var text = '<h3>' + giz_Locator.title + '</h3><p class="mt10"><img src="' + giz_Locator.path + 'images/icon/info.png" alt="Info" class="infoImg" /> &nbsp; Are Sure You Want Delete This?</p>';
		$.prompt(text, {
			buttons: { Delete: true, Cancel: false }, prefix: 'jqismooth',
			submit: function (e, v, m, f) {
				if (v) {
					__doPostBack(uniqueID, '');
				}
			}
		});
		return false;
	},
	edit: function () {
		alert('edit not done');
	},
	errorMsg: function (msg, topheight) {
		var text = '<h3>' + giz_Locator.title + '</h3><p class="mt10"><img src="' + giz_Locator.path + 'images/icon/error.png" alt="Error" class="errorImg" /> &nbsp; ' + msg + '</p>';
		$.prompt(text, {
			buttons: { Ok: true },
			prefix: 'jqismooth',
			top: topheight
		});
	},
	successMsg: function (msg, topheight) {
		var text = '<h3>' + giz_Locator.title + '</h3><p class="mt10"><img src="' + giz_Locator.path + 'images/icon/success.png" alt="Success" class="successImg" /> &nbsp; ' + msg + '</p>';
		$.prompt(text, {
			buttons: { Ok: true },
			prefix: 'jqismooth',
			top: topheight
		});
	},
	infoMsg: function (msg, topheight) {
		var text = '<h3>' + giz_Locator.title + '</h3><p class="mt10"><img src="' + giz_Locator.path + 'images/icon/info.png" alt="Info" class="infoImg" /> &nbsp; ' + msg + '</p>';
		$.prompt(text, {
			buttons: { Ok: true },
			prefix: 'jqismooth',
			show: 'slideDown',
			top: topheight
		});
	}
};