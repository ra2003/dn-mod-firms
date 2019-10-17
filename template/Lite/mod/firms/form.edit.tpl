<script src="{site_url}/js/file.upload.js"></script>
<script src="{site_url}/js/jquery.form.js"></script>
<script src="{site_url}/js/jquery.tabs.js"></script>
<script>
$(function(){
	// Captcha, reload
    $(".refresh").click( function () {
        var t = new Date().getTime();
        $("#divcaptcha").html('<img src="{site_url}/index.php?cap=captcha&t=' + t + '">');
    });

	// Array of fields
	var fields = {
		'title' : {max: 75, 'mess': '{title}'},
		'phone' : {min: 0, 'mess': '{phones}'},
		'email' : {min: 0, 'mess': '{email}'},
		'person': {min: 0, 'mess': '{help_person}'},
		'short' : {min: 10, 'mess': '{help_short}'}
	};
	<!--if:captcha:yes-->fields["captcha"] = {min: 5, max: 5, 'mess': '{help_captcha}'};<!--if-->
	<!--if:control:yes-->fields["respon"] = {'mess': '{help_control}'};<!--if-->

	// Array of lists
	var list = {
		'catid': {'mess': '{cat_click}'},
		'type': 0
	};

	// Verification form
	$("#form-data").checkForm(fields, list);

    $('#captcha, #respon').focus(function () { $(this).select(); }).mouseup(function(e){ e.preventDefault(); });
	$('input, textarea').placeholder({customClass:'text-placeholder'});
	$('.tabs li').tabs('.tab-1');
	$("#short").autoTextarea({min: 90, max: 130});
	$("#more").autoTextarea({min: 120, max: 200});

	$("#phone").mask("9 (999) 999-99-99");

	$('#image').change( function () {
		check_image(
			2048,
			'jpe?g|gif|png',
			'{img_large}',
			'{img_format}'
		);
	});

	<!--if:addfile:yes-->
	$('#file').change( function () {
		check_file(
			{maxfile},
			'{extfile}',
			'{img_large}',
			'{img_format}'
		);
	});
	<!--if-->
});
function inCat(el){
	$(el).prepend('<option value="0">— {other_cats} —</option>');
	$(el).find("option:not(:first)").remove().end().prop("disabled", true );
}
function getCat(catid, id){
	$.ajax({
		cache:false,
		url: $.url + "/",
		data:"dn={mod}&re=my&to=getcat&nocat=" + catid + "&id=" + id,
		error:function(msg){},
		success:function(data) {
			if (data.length > 0 && data.match(/option/)) {
				$("#catin").html(data);
				$("#catin").prop("disabled", false );
			} else {
				inCat("#catin");
			}
		}
	});
}
$(function(){
	$(window).on("load", function() {
		var catid = $("#catid").val();
		(catid > 0) ? getCat(catid, "{id}") : inCat("#catin");
	});
	$("#catid").on("change", function() {
		var catid = $("#catid").val();
		(catid > 0) ? getCat(catid, "{id}") : inCat("#catin");
	});
});
var social_net = "{socialnet}";
var link_page = "{linkpage}";
var delet = "{delet}";
</script>
<!--if:editor:yes-->
<script src="{site_url}/js/editor/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
	theme: 'modern',
	skin: 'custom',
	selector: 'textarea#short',
	language: '{lang}',
	width: '95.7%',
	height: '100px',
	schema: 'html5',
    menubar: false,
	plugins: ['symbols placeholder'],
	symbols_min: 10,
	symbols_max: 350,
	toolbar: 'undo redo | bold italic underline'
});
tinymce.init({
	theme: 'modern',
	skin: 'custom',
	selector: 'textarea#more',
	language: '{lang}',
	width: '95.7%',
	height: '150px',
	schema: 'html5',
	plugins: [
		'advlist autolink link image lists charmap preview hr anchor pagebreak spellchecker',
		'searchreplace placeholder visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
		'save table contextmenu directionality emoticons template paste textcolor'
	],
	menu: {
		edit: {title: 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall'},
		insert: {title: 'Insert', items : 'image media | hr'},
		format: {title: 'Format', items : 'formats removeformat'}
	},
	toolbar: 'insertfile undo redo | bold italic | bullist numlist outdent indent | forecolor backcolor'
});
</script>
<!--if-->
<style>
.social-add {
    background: #fff;
	border: 2px solid #f0f0f1;
	padding: 10px 15px 12px;
	margin-bottom: 25px !important;
}
#social-area > div {
	padding: 0;
	margin: 0 0 10px;
	vertical-align: middle;
}
#social-area > div input {
	margin-right: 5px;
}
#social-area > div input:nth-child(1) {
	width: 25%;
}
#social-area > div input:nth-child(2) {
	width: 65%;
}

a.sub-corner { 
	display: inline-block; 
	background-color: #f0f0f1; 
	font-size: 10px; 
	text-transform: uppercase; 
	height: 27px; 
	line-height: 29px; 
	color: #999; 
	padding: 0px 10px 0px 20px; 
	position: relative; 
	border-radius: 25px 0 0 25px; 
	cursor: pointer; 
	text-shadow: 0 1px 0 #fff; 
}
a.sub-corner:before { 
	content: ""; 
	position: absolute; 
	top: 0px; right: -27px; 
	width: 0; height: 0; 
	border-top: 27px solid #f0f0f1; 
	border-right: 27px solid transparent; 
}
a.sub-corner:hover:before { 
	content: ""; 
	position: absolute; 
	top: 0px; right: -27px; 
	width: 0; height: 0; 
	border-top: 27px solid #e3e3e5; 
	border-right: 27px solid transparent; 
}
a.sub-corner:hover { 
	background-color: #e3e3e5; 
	color: #000; 
	text-shadow: 0 1px 0 #f0f0f1; 
}

#social-area > div a {
    display: inline-block;
    background: #f0f0f1;
	color: #999;
	font-size: 18px;
	text-align: center;
	line-height: 30px;
	width: 30px;
	height: 30px;
	padding: 0;
	margin: 0 0 0;
	border-radius: 50%;
}
#social-area > div a:hover {
	color: #000;
}
</style>
<!--buffer:mysocial:0-->
<div id="social-{sk}">
	<input name="social[{sk}][title]" id="social{sk}" size="15" type="text" value="{title}" placeholder="{socialnet}" required="required" />
	<input name="social[{sk}][link]" size="50" type="text" value="{link}" placeholder="{linkpage}" required="required" />
	<a href="javascript:$.removesocial('form-data', 'social-area', 'social-{sk}');" title="{delet}">&#215;</a>
</div>
<!--buffer-->
<!--buffer:rfile:0--><b>{name}<i>.{ext}</i></b><!--buffer-->
<!--buffer:ifile:0-->{add}<!--buffer-->
<ul class="tabs">
	<li data-tabs=".tab-1">{basic}</li>
	<li data-tabs=".tab-all">{detail}</li>
</ul>
<div style="margin-top: 0" class="comment">
<form id="form-data" method="post" action="{post_url}" enctype="multipart/form-data">
    <fieldset class="tab-1">
		<input name="title" id="title" type="text" value="{re_title}" placeholder="{title}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="country" type="text" value="{re_country}" placeholder="{country}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="region" type="text" value="{re_region}" placeholder="{region}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="city" type="text" value="{re_city}" placeholder="{city}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="address" type="text" value="{re_address}" placeholder="{address}" />
    </fieldset>
    <fieldset class="tab-1">
		<input id="person" name="person" type="text" value="{re_person}" placeholder="{help_person}" />
    </fieldset>
    <fieldset class="tab-1">
		<input name="email" id="email" type="text" value="{re_mail}" placeholder="{email}" /><span class="help" title="{not_email}">?</span>
    </fieldset>
    <fieldset class="tab-1">
		<input name="phone" id="phone" type="text" value="{re_phone}" placeholder="{phones}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="skype" type="text" value="{re_skype}" placeholder="Skype" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="site" type="text" value="{re_site}" placeholder="{website}" />
    </fieldset>
	<!--if:showcat:yes-->
    <fieldset class="tab-1">
        <select name="catid" id="catid">
        <option value="0">— {cat_click} —</option>
        {sel}
        </select><span class="help" title="{cat_basic}">?</span>
    </fieldset>
    <!--if-->
	<!--if:multcat:yes-->
	<fieldset class="tab-all">
		<select id="catin" name="subcat[]" size="5" multiple="multiple" style="height: 100px">
			<option value="0">— {other_cats} —</option>
		</select><span class="help" title="{other_cats}<br>{multiple}">?</span>
	</fieldset>
	<!--if-->
    <fieldset class="tab-1">
		<textarea id="short" name="short" cols="60" placeholder="{help_short}..."<!--if:editor:yes--> style="position: absolute; left: -9999px"<!--if-->>{re_short}</textarea>
    </fieldset>
    <fieldset class="tab-all">
		<textarea id="more" name="more" cols="60" placeholder="{help_more}...">{re_more}</textarea>
    </fieldset>
	<fieldset class="data-file tab-all">
		<aside>
			<input id="image" name="image" type="file" />
			<button>{sel_file}</button>
		</aside><span class="help" title="{img_help}">?</span>
		<output>
			<!--if:image:yes--><div class="view"><img src="{site_url}/{src_img}" alt="" /></div><span><b>{name_img}<i>.{ext_img}</i></b></span><!--if-->
			<!--if:image:no--><span>{add_img}</span><!--if-->
		</output>
	</fieldset>
	<!--if:addfile:yes-->
	<fieldset class="data-docs tab-all">
		<aside>
			<input id="file" name="file" type="file" />
			<button>{sel_file}</button>
		</aside><span class="help" title="{file_help}">?</span>
		<output><span>{out_file}</span></output>
	</fieldset>
	<!--if-->
        <div class="clear-line"></div>
    <fieldset class="tab-all social-add">
		<legend>{social}</legend>
		<div id="social-area">{msrows}</div>
		<input type="hidden" id="socialid" value="{sk}" />
		<a class="sub-corner" onclick="javascript:$.addsocial('form-data', 'social-area')">{add_link}</a>
    </fieldset>
    <!--if:captcha:yes-->
    <fieldset>
		<label for="captcha" title="{help_captcha}">{captcha}<i></i></label>
		<table class="captcha">
			<tr>
				<td><input id="captcha" name="captcha" type="text" maxlength="5" /></td>
				<td><div id="divcaptcha"><img src="{site_url}/index.php?cap=captcha" alt="" /></div></td>
				<td><img class="refresh" src="{site_url}/template/{site_temp}/images/icon/refresh.png" alt="{all_refresh}" /></td>
			</tr>
		</table>
    </fieldset>
    <!--if-->
    <!--if:control:yes-->
    <fieldset>
		<label for="respon" title="{help_control}">{control_word}<i></i></label>
		<p>{control}</p>
		<input id="respon" name="respon" size="30" type="text" />
		<input name="cid" type="hidden" value="{cid}" />
    </fieldset>
    <!--if-->
    <div class="pad al">
        <input name="re" value="my" type="hidden" />
        <input name="to" value="save" type="hidden" />
        <input name="id" value="{id}" type="hidden" />
        <!--if:modedit:yes--><button type="submit" class="sub post sw250">{moderation}</button><!--if-->
        <!--if:modedit:no--><button type="submit" class="sub post">{edit_save}</button><!--if-->
    </div>
</form>
</div>