<div class="pageoverflow">
	<table cellspacing="0" class="pagetable">
	    <thead>
	        <tr>
	            <th>{$skus.skus.label.sku}</th>
	            <th>{$skus.skus.label.description}</th>
	            <th class="pageicon">&nbsp;</th>
	            <th class="pageicon">&nbsp;</th>
	        </tr>
	    </thead>
	    <tbody>
	        {foreach from=$skus.skus.list item=entry}
	        <tr class="{cycle values="row1,row2"}">
	            <td>{$entry.name}</td>
	            <td>{$entry.description}</td>
	            <td>{$entry.link_edit}</td>
	            <td>{$entry.link_delete}</td>
	        </tr>
	        {foreachelse}
	        <tr class="{cycle values="row1,row2"}">
	            <td colspan='2' align='center'>{$noskusfound}</td>
	        </tr>
	        {/foreach}
	    </tbody>
	</table>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$skus.skus.label.link_add}&nbsp;{$skus.skus.label.text_add}</p>
	</div>

