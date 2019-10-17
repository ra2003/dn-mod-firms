<div class="sub-title set" style="margin-top: 0"><h3>{title}</h3></div>
<table class="work">
<tbody>
    <tr class="head">
        <th style="text-align: center">No</th><th>{name}</th><th>{photo}</th><th>{video}</th><th>{manage}</th>
    </tr>
    {print}
	<!--if:data:no--><tr><td colspan="5" style="padding: 10px 0"><div class="message"><div>{data_not}</div></div></td></tr><!--if-->
</tbody>
</table>