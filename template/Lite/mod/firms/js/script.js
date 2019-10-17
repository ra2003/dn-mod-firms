/*
 * jQuery Masked Input Plugin
 * Copyright (c) 2007 - 2015 Josh Bush (digitalbush.com)
 * Licensed under the MIT license (http://digitalbush.com/projects/masked-input-plugin/#license)
 * Version: 1.4.1
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){var b,c=navigator.userAgent,d=/iphone/i.test(c),e=/chrome/i.test(c),f=/android/i.test(c);a.mask={definitions:{9:"[0-9]",a:"[A-Za-z]","*":"[A-Za-z0-9]"},autoclear:!0,dataName:"rawMaskFn",placeholder:"_"},a.fn.extend({caret:function(a,b){var c;if(0!==this.length&&!this.is(":hidden"))return"number"==typeof a?(b="number"==typeof b?b:a,this.each(function(){this.setSelectionRange?this.setSelectionRange(a,b):this.createTextRange&&(c=this.createTextRange(),c.collapse(!0),c.moveEnd("character",b),c.moveStart("character",a),c.select())})):(this[0].setSelectionRange?(a=this[0].selectionStart,b=this[0].selectionEnd):document.selection&&document.selection.createRange&&(c=document.selection.createRange(),a=0-c.duplicate().moveStart("character",-1e5),b=a+c.text.length),{begin:a,end:b})},unmask:function(){return this.trigger("unmask")},mask:function(c,g){var h,i,j,k,l,m,n,o;if(!c&&this.length>0){h=a(this[0]);var p=h.data(a.mask.dataName);return p?p():void 0}return g=a.extend({autoclear:a.mask.autoclear,placeholder:a.mask.placeholder,completed:null},g),i=a.mask.definitions,j=[],k=n=c.length,l=null,a.each(c.split(""),function(a,b){"?"==b?(n--,k=a):i[b]?(j.push(new RegExp(i[b])),null===l&&(l=j.length-1),k>a&&(m=j.length-1)):j.push(null)}),this.trigger("unmask").each(function(){function h(){if(g.completed){for(var a=l;m>=a;a++)if(j[a]&&C[a]===p(a))return;g.completed.call(B)}}function p(a){return g.placeholder.charAt(a<g.placeholder.length?a:0)}function q(a){for(;++a<n&&!j[a];);return a}function r(a){for(;--a>=0&&!j[a];);return a}function s(a,b){var c,d;if(!(0>a)){for(c=a,d=q(b);n>c;c++)if(j[c]){if(!(n>d&&j[c].test(C[d])))break;C[c]=C[d],C[d]=p(d),d=q(d)}z(),B.caret(Math.max(l,a))}}function t(a){var b,c,d,e;for(b=a,c=p(a);n>b;b++)if(j[b]){if(d=q(b),e=C[b],C[b]=c,!(n>d&&j[d].test(e)))break;c=e}}function u(){var a=B.val(),b=B.caret();if(o&&o.length&&o.length>a.length){for(A(!0);b.begin>0&&!j[b.begin-1];)b.begin--;if(0===b.begin)for(;b.begin<l&&!j[b.begin];)b.begin++;B.caret(b.begin,b.begin)}else{for(A(!0);b.begin<n&&!j[b.begin];)b.begin++;B.caret(b.begin,b.begin)}h()}function v(){A(),B.val()!=E&&B.change()}function w(a){if(!B.prop("readonly")){var b,c,e,f=a.which||a.keyCode;o=B.val(),8===f||46===f||d&&127===f?(b=B.caret(),c=b.begin,e=b.end,e-c===0&&(c=46!==f?r(c):e=q(c-1),e=46===f?q(e):e),y(c,e),s(c,e-1),a.preventDefault()):13===f?v.call(this,a):27===f&&(B.val(E),B.caret(0,A()),a.preventDefault())}}function x(b){if(!B.prop("readonly")){var c,d,e,g=b.which||b.keyCode,i=B.caret();if(!(b.ctrlKey||b.altKey||b.metaKey||32>g)&&g&&13!==g){if(i.end-i.begin!==0&&(y(i.begin,i.end),s(i.begin,i.end-1)),c=q(i.begin-1),n>c&&(d=String.fromCharCode(g),j[c].test(d))){if(t(c),C[c]=d,z(),e=q(c),f){var k=function(){a.proxy(a.fn.caret,B,e)()};setTimeout(k,0)}else B.caret(e);i.begin<=m&&h()}b.preventDefault()}}}function y(a,b){var c;for(c=a;b>c&&n>c;c++)j[c]&&(C[c]=p(c))}function z(){B.val(C.join(""))}function A(a){var b,c,d,e=B.val(),f=-1;for(b=0,d=0;n>b;b++)if(j[b]){for(C[b]=p(b);d++<e.length;)if(c=e.charAt(d-1),j[b].test(c)){C[b]=c,f=b;break}if(d>e.length){y(b+1,n);break}}else C[b]===e.charAt(d)&&d++,k>b&&(f=b);return a?z():k>f+1?g.autoclear||C.join("")===D?(B.val()&&B.val(""),y(0,n)):z():(z(),B.val(B.val().substring(0,f+1))),k?b:l}var B=a(this),C=a.map(c.split(""),function(a,b){return"?"!=a?i[a]?p(b):a:void 0}),D=C.join(""),E=B.val();B.data(a.mask.dataName,function(){return a.map(C,function(a,b){return j[b]&&a!=p(b)?a:null}).join("")}),B.one("unmask",function(){B.off(".mask").removeData(a.mask.dataName)}).on("focus.mask",function(){if(!B.prop("readonly")){clearTimeout(b);var a;E=B.val(),a=A(),b=setTimeout(function(){B.get(0)===document.activeElement&&(z(),a==c.replace("?","").length?B.caret(0,a):B.caret(a))},10)}}).on("blur.mask",v).on("keydown.mask",w).on("keypress.mask",x).on("input.mask paste.mask",function(){B.prop("readonly")||setTimeout(function(){var a=A(!0);B.caret(a),h()},0)}),e&&f&&B.off("input.mask").on("input.mask",u),A()})}})});


$(function() {
    $(".video-view").colorbox({
        transition : "elastic",
        scalePhotos : true,
		scrolling: false,
        maxHeight : "92%",
        maxWidth : "94%",
        fixed: true,
        current : "Видео {current} из {total}",
        title: function () {
            var show = $(this).attr("data-title");
            return show;
        }
    });
	$.addsocial = function(form, area) {
		var id = $('#socialid').attr('value');
		if (id) {
			var html = '<div id="social-' + id + '" style="display: none;">';
				html+= '<input name="social[' + id + '][title]" id="social' + id + '" size="15" type="text" placeholder="' + social_net + '" required="required" /> ';
				html+= '<input name="social[' + id + '][link]" size="50" type="text" placeholder="' + link_page + '" required="required" /> ';
				html+= '<a href="javascript:$.removesocial(\'form-data\', \'social-area\', \'social-' + id + '\');" title="' + delet + '">&#215;</a>';
				html+= '</div>';
			$('form[id=' + form + '] #' + area).append(html);
			$('form[id=' + form + '] #' + area + ' #social-' + id).show('normal');
			id++;
			$('#socialid').attr({value:id});
		}
	}
	$.removesocial = function(form, area, id) {
		$("form[id=" + form + "] #" + area + " #" + id).hide('normal', function() {
			$("form[id=" + form + "] #" + area + " #" + id).remove();
		});
	}
});

/*
 * Show phone
 --------------*/
jQuery.showphone = function(id, num, mod, sub) {
	$('#phone-' + num + sub).empty();
    $.ajax({
		type: "POST",
		cache : false,
		url : '/',
		data : 'dn=' + mod + '&'+ id +'=' + num + '&to=phone',
		error : function (msg) {  },
		success : function (d) {
			if (d.length > 0) {
				if ($('#phone-' + num + sub).length > 0) {
					$('#phone-' + num + sub).html('<span class="sphone">'+d+'</span>');
				}
			}
		}
    });
}

// Show form
jQuery.showform = function(mod, re, to, id, cid) {
	$('#formbox').show();
	$("#conacts").show();
	var data = $('#conacts').serialize() + '&dn=' + mod + '&re=' + re + '&to=' + to + '&id=' + id + '&cid=' + cid;
    $.ajax({
		type: "POST",
		cache : false,
		url : $.url + '/',
		data : data,
		error : function (msg) {  },
		success : function (d) {
             $("#formbox").hide();
                if (d.length > 0)
                {
                    $.colorbox({
						transition  : "elastic",
						scrolling: false,
						maxHeight   : "92%",
						maxWidth    : "94%",
						initialWidth:  '640px',
						initialHeight: '640px',
						fixed: true,
                        html:d
                    },
					$.submitform);
					$.submitform(mod, re, to, id, cid);
				}
		}
    });
}
jQuery.submitform = function(mod, re, to, id, cid) {
	$("#cphone").mask("9 (999) 999-99-99");
	$("#submit").click(function()
	{
		$('#conacts input, textarea').removeClass('error-input').addClass('width');
		$error = false;
		$.checks = new Array();
		$.checks['name'] = new Array('cname',25);
		//$.checks['email'] = new Array('cemail',0);
		$.checks['phone'] = new Array('cphone',0);
		$.checks['message'] = new Array('cmessage',0);
		for(i in $.checks) {
			var eid = $.checks[i][0], val = $.checks[i][1];
			if (val == 0) {
				if ($("#" + eid) != "undefined" && $("#" + eid).val().length == 0) {
					$error = true;
					$("#" + eid).removeClass('width').addClass('error-input');
					$("#" + eid).focus(function(){
						$(this).removeClass('error-input').addClass('width');
					});
				}
			}
			if (val > 0) {
				if ($("#" + eid) != "undefined" && $("#" + eid).val().length == 0 || $("#" + eid) != "undefined" && $("#" + eid).val().length > val) {
					$error = true;
					$("#" + eid).removeClass('width').addClass('error-input');
					$("#" + eid).focus(function(){
						$(this).removeClass('error-input').addClass('width');
					});
				}
			}
		}
		if ($error) {
			return false;
		}

		$("#conacts").hide();
		$('#formbox').show();
		var data = $('#conacts').serialize() + '&dn=' + mod + '&re=' + re + '&to=send&id=' + id + '&cid=' + cid;
		$.ajax({
			type: "POST",
			cache : false,
			url : $.url + '/',
			data : data,
			error : function (msg) { },
			success : function (d)
			{
				$("#formbox").hide();
				if (d.length > 0)
				{
					$.colorbox({
						transition  : "elastic",
						scrolling: false,
						maxHeight   : "92%",
						maxWidth    : "94%",
						initialWidth:  '640px',
						initialHeight: '640px',
						fixed: true,
                        html:d
                    },$.submitform)
                }
            }
        });
        return false;
    });
}

// Checked image
function check_image(max, acc, msg, err)
{
	var max = (max !== undefined) ? max : 2048;
	var acc = (acc !== undefined) ? acc : 'jpe?g|gif|png';
	var msg = (msg !== undefined) ? msg : 'File too big!';
	var err = (err !== undefined) ? err : 'Wrong file format!';

	var span = document.querySelector('.data-file > output > span');

	var error = document.querySelector('aside.error');
	if (error != null) {
		document.querySelector('.data-file > aside').classList.remove('error');
	}

	var view = document.querySelector('.view');
	if (view != null) {
		view.parentNode.removeChild(view);
	}

	try {

		var file = document.getElementById('image').files[0];

		if (file)
		{
			var acc = new RegExp(acc, 'i');
			var ext = file.name.split('.').pop();
			if (acc.test(ext))
			{
				var legend = document.querySelector('.data-file > legend');
				if (legend != null) {
					document.querySelector('.data-file > legend').style.visibility = 'hidden';
				}

				var elem = document.createElement('div');
					elem.className = 'view';
					elem.innerHTML = '<img src="" alt="" />';

				var out = document.querySelector('.data-file > output');
					out.insertBefore(elem, out.firstChild);

				var name = file.name.substr( 0, file.name.length - (ext.length + 1));
				var nameFirst = file.name.substr( 0, 27);
				var nameLast = file.name.substr( - 9, file.name.length - 9);

				if (file.name.length > 42) {
					span.innerHTML = '<b>' + nameFirst + ' .... <i>.' + ext + '</i></b>';
				} else {
					span.innerHTML = '<b>' + name + '<i>.' + ext + '</i></b>';
				}

				var Kbyte = (Math.round(file.size * 100 / 1024) / 100).toString();
				if (Kbyte > max) {
					document.querySelector('.view').classList.add('error');
					span.innerHTML = '<i>' + msg + '</i>';
				}

				var el = document.querySelector('.view > img');

				if (typeof file.getAsDataURL == 'function')
				{
					if (file.getAsDataURL().substr(0, 11) == 'data:image/') {
						el.src = file.getAsDataURL();
					} else {
						el.src = 'data:application/octet-stream;base64,R0lGODlhAQABAIAAAMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
					}
				}
				else
				{
					var reader = new FileReader();
					reader.onloadend = function(evt) {
						if (evt.target.readyState == FileReader.DONE) {
							el.src = evt.target.result;
						}
					};

					var read;
					if (file.slice) {
						read = file.slice(0, file.size);
					} else if (file.webkitSlice) {
						read = file.webkitSlice(0, file.size);
					} else if (file.mozSlice) {
						read = file.mozSlice(0, file.size);
					}
					reader.readAsDataURL(read);
				}

			} else {
				span.innerHTML = '<i>' + err + '</i>';
			}
		}
	}
	catch(e) {
		var file = document.getElementById('image').value;
		file = file.replace(/\\/g, "/").split('/').pop();
		span.innerHTML = file;
	}
}

// Checked file
function check_file(max, acc, msg, err)
{
	var max = (max !== undefined) ? max : 2048;
	var acc = (acc !== undefined) ? acc : 'zip|rar|xls|xlsx|doc|docx|pdf|rtf|txt';
	var msg = (msg !== undefined) ? msg : 'File too big!';
	var err = (err !== undefined) ? err : 'Wrong file format!';

	var span = document.querySelector('.data-docs > output > span');

	var error = document.querySelector('aside.error');
	if (error != null) {
		document.querySelector('.data-docs > aside').classList.remove('error');
	}

	try {

		var file = document.getElementById('file').files[0];

		if (file)
		{
			var acc = new RegExp(acc, 'i');
			var ext = file.name.split('.').pop();
			if (acc.test(ext))
			{
				var legend = document.querySelector('.data-docs > legend');
				if (legend != null) {
					document.querySelector('.data-docs > legend').style.visibility = 'hidden';
				}

				var name = file.name.substr( 0, file.name.length - (ext.length + 1));
				var nameFirst = file.name.substr( 0, 27);
				var nameLast = file.name.substr( - 9, file.name.length - 9);

				if (file.name.length > 42) {
					span.innerHTML = '<b>' + nameFirst + ' .... <i>.' + ext + '</i></b>';
				} else {
					span.innerHTML = '<b>' + name + '<i>.' + ext + '</i></b>';
				}

				var Kbyte = (Math.round(file.size * 100 / 1024) / 100).toString();
				if (Kbyte > max) {
					span.innerHTML = '<i>' + msg + '</i>';
				}

			} else {
				span.innerHTML = '<i>' + err + '</i>';
			}
		}
	}
	catch(e) {
		var file = document.getElementById('file').value;
		file = file.replace(/\\/g, "/").split('/').pop();
		span.innerHTML = file;
	}
}
