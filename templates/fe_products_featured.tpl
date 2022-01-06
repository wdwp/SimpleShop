{* List of featured products *}
<div class="productlist">
<div ID="productcount">{$lable_product_count}</div>
<table>
	{foreach from=$products item=featentry}
		<tr class="{$featentry->rowclass}" onmouseover="this.className='{$featentry->rowclass}hover';" onmouseout="this.className='{$featentry->rowclass}';">
                        {if ($featentry->prodimage)}<td class="img">
				{image src=$featentry->prodimage alt=$featentry->proddesc|strip_tags|escape:'html'|truncate}</td>
			{/if}
			<td class="productname">{$featentry->prodname}</td>
			<td class="productdesc">{$featentry->proddesc|truncate}</td>
			<td class="productprice">{$featentry->price}</td>
			<td>{if isset($featentry->addproduct)}{$featentry->addproduct}{/if}</td>
		</tr>
	{/foreach}
</table>
{if $pagecount > 1}
	{$firstpage} {$prevpage} {$pagetext}: {$pagenumber} {$oftext}: {$pagecount} {$nextpage} {$lastpage}
{/if}
<div ID="productpricesin">{$currency}</div>
</div>