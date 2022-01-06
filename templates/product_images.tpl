{if isset($images.message) }<p style="color:red;">{$images.message}</p>{/if}
{$images.startform}
<div class="pageoverflow">
	<p class="pagetext">{$images.name.label}:</p>
	<p class="pagetext">{$images.name.output}</p>
</div>
<div class="pageoverflow">
	<p class="pagetext">{$images.image.label}:</p>
	<p class="pageinput">{$images.image.input}</p>
</div>
<div class="pageoverflow">
	<p class="pagetext">{$images.description.label}:</p>
	<p class="pageinput">{$images.description.input}</p>
</div>
<div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput">{$product.product_id.input}{$product.current_category_id.input}</p>
</div>
<div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput">{$images.submit}{$images.cancel}{$product.add_new}</p>
</div>

<table cellspacing="0" class="pagetable">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th>{$images.namecolumn}</th>
            <th>{$images.descriptioncolumn}</th>
            <th class="pageicon">&nbsp;</th>
            <th class="pageicon">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
	{if $images.itemcount > 0}
		{foreach from=$images.list item=entry}
		<tr class="{cycle values="row1,row2"}">
			<td><img src="{$entry.productimage}" height="{$entry.imageheight}"></td>
			<td>{$entry.image}</td>
			<td>{$entry.description}</td>
			<td class="pageicon">{$entry.link_edit}</td>
			<td class="pageicon">{$entry.link_delete}</td>
		</tr>
	        {/foreach}
	{else}
		<tr class="{cycle values="row1,row2"}">
			<td colspan='5' align='center'>{$images.no_images_available}</td>
		</tr>
	{/if}
    </tbody>
</table>
{$images.endform}