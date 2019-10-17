<div class="form-order">
	<h4>{title_send}</h4>
	<div class="form-order-send" id="formbox" style="display: none">
		<img src="http://test.ru/template/Stenwat/images/loading.gif" alt="" /> <span class="sendtext">{all_sends}...</span>
	</div>
	<form action="{post_url}" method="post" id="conacts">
		<input name="name" id="cname" type="text" value="{uname}" placeholder="{email_name} *" title="{not_empty}" />
			<div class="clear-line"></div>
		<input name="phone" id="cphone" type="text" placeholder="{email_phone} *" title="{not_empty}" />
			<div class="clear-line"></div>
		<input name="email" id="cemail" type="text" value="{umail}" placeholder="{email} *" title="{not_empty}" />
			<div class="clear-line"></div>
		<textarea style="height: 120px;" cols="40" rows="5" name="message" id="cmessage" placeholder="{email_text} *" title="{not_empty}"></textarea>
			<div class="clear-line"></div>
		<button type="submit" id="submit" class="sub mail">{email_send}</button>
	</form>
</div>