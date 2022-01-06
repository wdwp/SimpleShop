{if isset($attributes.message) }<p style="color:red;">{$attributes.message}</p>{/if}
{$attributes.startform}
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.prodname.label}:</p>
		<p class="pagetext" style="width: 50em;">{$attributes.prodname.output}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.name.label}:</p>
		<p class="pageinput">{$attributes.name.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.description.label}:</p>
		<p class="pageinput">{$attributes.description.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.priceadjusttype.label}:</p>
		<p class="pageinput">{$attributes.priceadjusttype.input}&nbsp;
			{$attributes.priceadjustment.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.minallowed.label}:</p>
		<p class="pageinput">{$attributes.minallowed.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.maxallowed.label}:</p>
		<p class="pageinput">{$attributes.maxallowed.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.displayonly.label}:</p>
		<p class="pageinput">{$attributes.displayonly.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.itemnumber.label}:</p>
		<p class="pageinput">{$attributes.itemnumber.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attributes.active.label}:</p>
		<p class="pageinput">{$attributes.active.input}</p>
	</div>
	<div class="pageoverflow">
	    <p class="pagetext">&nbsp;</p>
	    <p class="pageinput">{$attributes.submit}{$attributes.cancel}{$product.add_new}
				{$product.product_id.input}{$product.current_category_id.input}</p>
	</div>

<table cellspacing="0" class="pagetable">
    <thead>
        <tr>
            <th>{$attributes.namecolumn}</th>
            <th>{$attributes.descriptioncolumn}</th>
            <th>{$attributes.adjustmentcolumn}</th>
            <th>{$attributes.pricecolumn}</th>
            <th>{$attributes.displaycolumn}</th>
            <th>{$attributes.minallowedcolumn}</th>
            <th>{$attributes.maxallowedcolumn}</th>
            <th>{$attributes.itemnumbercolumn}</th>
            <th>{$attributes.activecolumn}</th>
            <th class="pageicon">&nbsp;</th>
            <th class="pageicon">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
	{if $attributes.itemcount > 0}
		{foreach from=$attributes.list item=entry}
		<tr class="{cycle values="row1,row2"}">
			<td>{$entry.name}</td>
			<td>{$entry.description}</td>
			<td>{$entry.adjustment}</td>
			<td>{$entry.price}</td>
			<td class="pageicon">{$entry.link_display}</td>
			<td>{$entry.minallowed}</td>
			<td>{$entry.maxallowed}</td>
			<td>{$entry.itemnumber}</td>
			<td class="pageicon">{$entry.link_enable}</td>
			<td class="pageicon">{$entry.link_edit}</td>
			<td class="pageicon">{$entry.link_delete}</td>
		</tr>
	        {/foreach}
	{else}
		<tr class="{cycle values="row1,row2"}">
			<td colspan='5' align='center'>{$attributes.no_attributes_available}</td>
		</tr>
	{/if}
    </tbody>
</table>
{$attributes.endform}