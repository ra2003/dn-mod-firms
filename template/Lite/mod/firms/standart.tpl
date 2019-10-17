<!--buffer:icon:0--><img src="{site_url}/{icon}" alt="{alt}" /><!--buffer-->
<!--buffer:cat:0--><a class="cat" href="{caturl}">{catname}</a> <span>&#187;</span><!--buffer-->
<!--buffer:thumb:0--><figure class="{float} thumb"><a href="{url}"><img src="{site_url}/{thumb}" alt="{alt}" /></a></figure><!--buffer-->
<!--buffer:author:0--><li><span title="{lang_author}">{lang_author}:</span> {author}</li><!--buffer-->
<article role="article">
	<header>
		<!--if:date:yes--><time datetime="{date:datetime}">{date:1}</time><!--if-->
		<h3>{icon} {cat} <a href="{url}">{title}</a></h3>
	</header>
	<div class="text-content">
		<ul class="contacts">
			{author}
			<!--if:review:yes--><li>{lang_review}: {review}</li><!--if-->
			<li>{langhits}: {hits}</li>
			<!--if:rating:yes--><li>{lang_rate}: {rating}</li><!--if-->
			<li onclick="$.showphone('id','{id}','{mod}','show');"><span id="phone-{id}show"><span class="sphone">{lang_phone}: {lang_show}</span></span></li>
			<li><div class="show-form fl" onclick="javascript:$.showform('{mod}', 'request', '', '{id}', 1);">{lang_contact}</div></li>
		</ul>
		{image}{text} 
		<div class="clear"></div>
		{cats}{location}
	</div>
	<footer>
        <aside>
			<!--if:link:yes--><a class="read" href="{url}">{read}</a><!--if-->
		</aside>
	</footer>
</article>
