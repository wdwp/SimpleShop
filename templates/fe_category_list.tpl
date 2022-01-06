{* List of category data *}
{if isset($canonical)}{assign var='canonical' value=$canonical scope='global'}{/if}

{if isset($categoryname)}
<h2 class="catlistheader">{$categoryname}</h2>
<table ID="catinfo">
	<tr>
		{if isset($image) && $image != '*none'}
                     <td ID="catimagethumb">
			{image src=$image width="100" height="100" alt=$description|strip_tags|escape:'html'}
                     </td>
		{/if}
		<td class="catdescription">{$description}</td>
	</tr>
</table>
{/if}
<div class="productlist">
<div ID="productcount">{$lable_product_count}</div>
<table>
	{foreach from=$products item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
                        {if isset($entry->prodimage)}<td class="img">
				{image src=$entry->prodimage alt=$entry->imagedesc}</td>
				{* or use the following. The image is then a link to the detail page
				{$entry->prodimagelink}</td>
				*}
			{/if}
			<td class="productname">{$entry->prodname}</td>
			{* Use the following if you only want to show the name of product (no link):
			<td class="productname">{$entry->prodnamenolink}</td>
			*}
			<td class="productdesc">{$entry->proddesc|truncate}</td>
			<td class="productprice">{$entry->price} {$cur_symbol}</td>
			<td>{if isset($entry->addproduct)}{$entry->addproduct}{/if}</td>
			{* use <td>{$entry->addproductimage}</td> if you want an image as the add to cart link *}

		</tr>
	{/foreach}
</table>
{if $pagecount > 1}
	{$firstpage} {$prevpage} {$pagetext}: {$pagenumber} {$oftext}: {$pagecount} {$nextpage} {$lastpage}
{/if}
<p><span class='back'>&#8592; <a href="#" title="{$mod->Lang('return')}" onclick="history.back()">{$mod->Lang('return')}</a></span></p>
<div ID="productpricesin">{$currency}</div>
</div>
{* A very different setup that allows showing products in multiple columns
{assign var="numCols" value="3"}
<table class="prodtable">
    <tr>
    {assign var="col" value="0"}
    {section name=element loop=$products}
        {if $col == $numCols}
            </tr><tr>{assign var="col" value="0"}
        {/if}
        <td class="prodbox">
	{if isset($prodimage) && !empty($prodimage)}<div class="catlistimg">
	{image src=$products[element]->prodimage alt=$products[element]->proddesc}</div>{/if}
<div class="prodname">{$products[element]->prodname}</div>
&euro;&nbsp;{$products[element]->price}&nbsp;<div class="product2cart">{$entry->addproductimage}</div></td>
        {assign var="col" value="`$col+1`"}
    {/section}
    {assign var="remainder" value="`$numCols-$col`"}
    {section name=emptyElement loop=$remainder}
        <td>&nbsp;</td>
    {/section}
    </tr>
</table>
*}