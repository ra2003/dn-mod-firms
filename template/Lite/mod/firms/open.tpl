<!--buffer:icon:0--><img src="{site_url}/{icon}" alt="{alt}" /><!--buffer-->
<article role="article" class="open">
	<header>
		<!--if:date:yes--><time datetime="{date:datetime}" title="{lang_public}">{date:1:1}</time><!--if-->
		<h2>{icon} {title}</h2>
	</header>
	<div class="text-content">
        {image}{textshort}{textmore}
		<div class="show-form" onclick="javascript:$.showform('{mod}', 'request', '', '{id}');">{lang_contact}</div>
		{textnotice}
	</div>
	<footer>
		<aside>
			<!--if:person:yes--><span class="author" title="{lang_person}">{lang_person}:</span> {person}<!--if-->
			<!--if:author:yes--><span class="author" title="{lang_author}">{lang_author}:</span> {author}<!--if-->
			<!--if:review:yes--><span class="com" title="{lang_review}">{lang_review}:</span> <a href="{link}#res" title="{lang_review}">{review}</a><!--if-->
			<!--if:rating:yes--><span class="rating" title="{titlerate}">{lang_rate}:</span> {ratings}<!--if--> 
			<span class="hits" title="{lang_hits}">{lang_hits}:</span> {counts}
			<!--if:print:yes--><span class="print"><a href="{print_url}" title="{lang_print}">{lang_print}</a></span><!--if-->
		</aside>
	</footer>
</article>
	{files}
	{filenotice}
<fieldset class="wrap-details">
	<legend>{lang_details}</legend>
    <div class="details">
		{cats}{tags}
    </div>
</fieldset>
<fieldset class="wrap-details">
	<legend>{lang_contacts}</legend>
    <div class="details">
        <!--if:country:yes--><ul><li>{lang_country}</li><li>{country}</li></ul><!--if-->
        <!--if:region:yes--><ul><li>{lang_region}</li><li>{region}</li></ul><!--if-->
        <!--if:city:yes--><ul><li>{lang_city}</li><li>{city}</li></ul><!--if-->
        <!--if:address:yes--><ul><li>{lang_address}</li><li>{address}</li></ul><!--if-->
        <ul onclick="$.showphone('id','{id}','{mod}','show');"><li>{lang_phone}</li><li id="phone-{id}show"><span class="sphone">{lang_show}</span></li></ul>
        <!--if:skype:yes--><ul><li>Skype</li><li>{skype}</li></ul><!--if-->
        <!--if:site:yes--><ul><li>{lang_url}</li><li><a rel="nofollow" href="{website}" target="_blank">{hostsite}</a></li></ul><!--if-->
    </div>
</fieldset>
{mysocial}
<a name="res"></a>
{rating}
{maps}
<!--if:video:yes-->
<div class="clear-line"></div>
<div class="sub-title video"><h3>{lang_video}</h3></div>
{video}
<!--if-->
<!--if:photo:yes-->
<div class="sub-title photo"><h3>{lang_photo}</h3></div>
{photo}
<!--if-->
{search}
{recommend}
{social}
<div class="clear-line"></div>
{reviews}
{ajaxbox}
<div id="errorbox"></div>
{reform}
