{if isset($attribute.message) }<p style="color:red;">{$attribute.message}</p>{/if}
{$attribute.startform}
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.prodname.label}:</p>
		<p class="pagetext">{$attribute.prodname.output}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.name.label}:</p>
		<p class="pageinput">{$attribute.name.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.description.label}:</p>
		<p class="pageinput">{$attribute.description.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.priceadjusttype.label}:</p>
		<p class="pageinput">{$attribute.priceadjusttype.input}&nbsp;
			{$attribute.priceadjustment.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.minallowed.label}:</p>
		<p class="pageinput">{$attribute.minallowed.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.maxallowed.label}:</p>
		<p class="pageinput">{$attribute.maxallowed.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.displayonly.label}:</p>
		<p class="pageinput">{$attribute.displayonly.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.itemnumber.label}:</p>
		<p class="pageinput">{$attribute.itemnumber.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$attribute.active.label}:</p>
		<p class="pageinput">{$attribute.active.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$attribute.submit}{$attribute.cancel}
			{$attribute.product_id.input}
			{$attribute.current_category_id.input}
			{$attribute.attribute_id.input}</p>
	</div>
{$attribute.endform}