<script src="{site_url}/js/jquery.form.js"></script>
<script>
$(function(){
	var fields = {'title' : {max: 75, 'mess': '{name}'}};
	$("#form-data").checkForm(fields);
	$('input, textarea').placeholder({customClass:'text-placeholder'});
	$("#desc").autoTextarea({min: 42, max: 100});
	$('#image').change( function () {
		check_image(
			2048,
			'jpe?g|gif|png',
			'{img_large}',
			'{img_format}'
		);
	});
	$( '#form-data' ).submit(function(e) {
		if($('#image').val() == '') {
			$('.data-file > aside').addClass('error');
			e.preventDefault();
		}
	});
	$('#image').focus(function() {
		$('.data-file > aside').removeClass('error')
	});
	function clearFile(id) {
		file = document.getElementById(id); parent = file.parentNode;
		tmp = document.createElement('form'); parent.replaceChild(tmp, file);
		tmp.appendChild(file);
		tmp.reset();
		parent.replaceChild(file, tmp);
	}
	$(window).on("load", function() {
		clearFile('image');
	});
});
</script>
<div class="sub-title photo"><h3>{album}: {title}</h3></div>
<form method="post" action="{post_url}">
<table class="work">
<tbody>
    <tr class="head">
        <th style="text-align: center">No</th><th>{name}</th><th>{posit}</th><th>{photo}</th><th>{manage}</th>
    </tr>
    {print}
</tbody>
</table>
<!--if:data:yes-->
<div class="ac" style="margin: 0 0 25px;">
	<input name="re" value="my" type="hidden" />
	<input name="id" value="{fid}" type="hidden" />
	<input name="to" value="photoup" type="hidden" />
	<button type="submit" class="sub poll">{save}</button>
</div>
<!--if-->
</form>
<!--buffer:photo:0-->
<tr>
	<td>{id}</td>
	<td><input style="height: 34px" type="text" name="title[{id}]" value="{title}" /></td>
	<td class="pw15"><input style="width: 50px; height: 34px" type="text" name="posit[{id}]" value="{posit}" /></td>
	<td class="pw15"><a class="media-view" href="{site_url}/{image}"><img src="{thumb_url}" alt="{alt}" /></a></td>
	<td class="gov">
		<a href="{del_url}"><img src="{site_url}/up/{mod}/icon/del.png" alt="{del}" /></a>
	</td>
</tr>
<!--buffer-->
<!--buffer:datanot:0-->
<tr>
	<td colspan="5" style="padding: 10px 0"><div class="message"><div>{data_not}</div></div></td>
</tr>
<!--buffer-->
<div class="sub-title photo"><h3>{photo_add}</h3></div>
<form id="form-data" action="{post_url}" method="post" enctype="multipart/form-data">
<div style="margin-top: 0" class="comment">
    <fieldset class="tab-1">
		<input id="title" name="title" type="text" placeholder="{name}" />
    </fieldset>
    <fieldset class="tab-1">
		<textarea id="desc" name="desc" cols="60" placeholder="{descript}..."></textarea>
    </fieldset>
	<fieldset class="data-file">
		<aside>
			<input id="image" name="image" type="file" accept="image/*, image/jpeg, image/png, image/gif" />
			<button>{sel_file}</button>
		</aside><span class="help" title="{img_help}">?</span>
		<output><span>{image}</span></output>
	</fieldset>
    <div class="clear-line"></div>
    <div class="clear-line"></div>
    <div class="pad al">
        <input name="re" value="my" type="hidden" />
		<input name="id" value="{fid}" type="hidden" />
        <input name="to" value="photoadd" type="hidden" />
        <button type="submit" class="sub add">{add_save}</button>
    </div>
</div>
</form>