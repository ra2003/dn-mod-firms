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

	$("#phone").mask("9 (999) 999-99-99");

	/*$(window).load( function (е) {
		$('.view').remove();
		$("#image").val(null);
		$('button.sub').removeAttr('disabled');
    });*/
});
function inCat(el){
	$(el).prepend('<option value="0">— {other_cats} —</option>');
	$(el).find("option:not(:first)").remove().end().prop("disabled", true );
}
function getCat(catid){
	$.ajax({
		cache:false,
		url: $.url + "/",
		data:"dn={mod}&re=add&to=getcat&nocat=" + catid,
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
		(catid > 0) ? getCat(catid) : inCat("#catin");
	});
	$("#catid").on("change", function() {
		var catid = $("#catid").val();
		(catid > 0) ? getCat(catid) : inCat("#catin");
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
	margin-right: 10px;
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
<ul class="tabs">
	<li data-tabs=".tab-1">{basic}</li>
	<li data-tabs=".tab-all">{detail}</li>
</ul>
<form id="form-data" action="{post_url}" method="post"  enctype="multipart/form-data">
<div style="margin-top: 0" class="comment">
    <fieldset class="tab-1">
		<input id="title" name="title" type="text" placeholder="{title}" autofocus="autofocus" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="country" type="text" placeholder="{country}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="region" type="text" placeholder="{region}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="city" type="text" placeholder="{city}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="address" type="text" placeholder="{help_addr}" />
    </fieldset>
    <fieldset class="tab-1">
		<input id="person" name="person" type="text" placeholder="{help_person}" />
    </fieldset>
    <fieldset class="tab-1">
		<input id="email" name="email" type="text" value="{umail}" placeholder="{email}" /><span class="help" title="{not_email}">?</span>
    </fieldset>
    <fieldset class="tab-1">
		<input id="phone" name="phone" type="text" placeholder="{phones}" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="skype" type="text" placeholder="Skype" />
    </fieldset>
    <fieldset class="tab-all">
		<input name="site" type="text" placeholder="{website}" />
    </fieldset>
	<!--if:showcat:yes-->
    <fieldset class="tab-1">
        <select id="catid" name="catid">
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
		<textarea id="short" cols="60" name="short" placeholder="{help_short}..."<!--if:editor:yes--> style="position: absolute; left: -9999px"<!--if-->></textarea>
    </fieldset>
    <fieldset class="tab-all">
		<textarea id="more" cols="60" name="more" placeholder="{help_more}..."></textarea>
    </fieldset>
	<fieldset class="data-file">
		<aside>
			<input id="image" name="image" type="file" accept="image/*, image/jpeg, image/png, image/gif" />
			<button>{sel_file}</button>
		</aside><span class="help" title="{img_help}">?</span>
		<output><span>{image}</span></output>
	</fieldset>
	<!--if:addfile:yes-->
	<fieldset class="data-docs tab-all">
		<aside>
			<input id="file" name="file" type="file" />
			<button>{sel_file}</button>
		</aside><span class="help" title="{file_help}">?</span>
		<output><span>{add_file}</span></output>
	</fieldset>
	<!--if-->
    <div class="clear-line"></div>
    <fieldset class="tab-all social-add">
		<legend>{social}</legend>
		<div id="social-area"></div>
		<input type="hidden" id="socialid" value="0" />
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
        <input name="re" value="add" type="hidden" />
        <input name="to" value="save" type="hidden" />
        <button type="submit" class="sub add">{add_save}</button>
    </div><br />
    <div class="error gray">
		<em>{alert_text}</em>
		<!--if:modadd:yes--><br /><em>{moder_text}</em><!--if-->
    </div>
</div>
</form>