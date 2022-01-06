{literal}
<script type="text/javascript">
	function showQuickSelector(id) {
		document.getElementById(id).style.display = 'block';
	}
	function hideQuickSelector(id) {
		document.getElementById(id).style.display = 'none';
	}
</script>
{/literal}
{if isset($message) && $message!=''}
	<p style="color:red;">{$message}</p>
{/if}
<table cellspacing="0" class="pagetable" style="border: 0px;">
	<tr style="vertical-align: top;">
	<td id="qs" style="display: {$displayqs}; overflow-y:hidden; overflow-x:scroll;">
	<fieldset>
		<legend>{$title_quick_selector}</legend>
		<div style="border:0px solid black;height:400px;overflow-y:scroll;overflow-x:scroll;">
		<p style="width:250%;">
		<table width=100% border=0>
		{section name=i loop=$qscats}
		 <tr style="height: 1em;">
		  <td>{section name=j loop=$qscats[i][3] max=$qscats[i][3]}&nbsp;&nbsp;{/section}
		  	{if $current_category_id == $qscats[i][0]}<b>{$qscats[i][1]}</b>{else}{$qscats[i][1]}{/if}
			  {if $qscats[i][4] > 0}&nbsp;({$qscats[i][4]}){/if}
		  </td>
		 </tr>
		{/section}
		</table>
		</p>
		</div>
	</fieldset>
	</td>
	<td style="width: 80%;">
		<span style="vertical-align: middle;">
		{$categories.current.label}:&nbsp;<b>{$categories.current.value}</b>&nbsp;{$categories.current.link_add}
		</span>
		<span style="float: right;">
			<a href="#" onclick="showQuickSelector('qs'); return false;">{$title_show}</a>
			<a href="#" onclick="hideQuickSelector('qs'); return false;">{$title_hide}</a>&nbsp;
			{$title_quick_selector}
		</span>
		<table cellspacing="0" class="pagetable tablesorter">
			<thead>
				<tr>
					<th width="5%">{$categories.subcategories.label.id}</th>
					<th>{$categories.subcategories.label.name}</th>
					<th>{$categories.subcategories.label.description}</th>									
					<th data-sorter="false" class="pageicon">{$mod->Lang('active')}</th>
					<th data-sorter="false" class="pageicon">&nbsp;</th>
					<th data-sorter="false" class="pageicon">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$categories.subcategories.list item=entry}
				<tr class="{cycle values="row1,row2"}">
					<td>{$entry.category_id}</td>
					<td>{$entry.name}</td>
					<td>{$entry.description|truncate}</td>
					<td>{$entry.link_enable}</td>
					<td>{$entry.link_edit}</td>
					<td>{$entry.link_delete}</td>
				</tr>
				{foreachelse}
				<tr class="{cycle values="row1,row2"}">
					<td colspan='6' align='center'>{$nocatfound}</td>
				</tr>
				{/foreach}
			</tbody>
		</table>

		{$products.startform}
		{$products.category.label}:&nbsp;{$products.category.value}&nbsp;
		{$products.category.link_add}&nbsp;{$products.itemsearch}{$products.submit}
		{$products.endform}

		<table cellspacing="0" class="pagetable tablesorter">
			<thead>
				<tr>
					<th width="5%">{$products.products.label.id}</th>
					<th data-sorter="false">{$mod->Lang('label_category_image')}</th>
					<th>{$products.products.label.name}</th>
					<th>{$mod->Lang('label_product_price')}</th>					
					<th>{$products.products.label.itemnumber}</th>
					<th data-sorter="false">{$products.products.label.onstock}</th>
					<th data-sorter="false" class="pageicon">{$mod->Lang('active')}</th>
					<th data-sorter="false" class="pageicon">&nbsp;</th>
					<th data-sorter="false" class="pageicon">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$products.products.list item=entry}
				<tr class="{cycle values="row1,row2"}">
					<td>{$entry.product_id}</td>
					<td>{if isset($entry.image) && !empty($entry.image)}<img src={$entry.image} width="75">{/if}</td>
					<td>{$entry.name}</td>
					<td>{$entry.price}</td>					
					<td>{$entry.itemnumber}</td>
					{if $entry.inventorytype == 'prod'}
						<td>{$entry.onstock}</td>
					{else}
						<td>&nbsp;</td>
					{/if}
					<td>{$entry.link_enable}</td>
					<td>{$entry.link_edit}</td>
					<td>{$entry.link_delete}</td>
				</tr>
				{foreachelse}
				<tr class="{cycle values="row1,row2"}">
					<td colspan='9' align='center'>{$noprodfound}</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</td>
	</tr>
</table>
