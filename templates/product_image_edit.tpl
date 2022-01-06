{if isset($image.message) }<p style="color:red;">{$image.message}</p>{/if}
{$image.startform}
	<div class="pageoverflow">
		<p class="pagetext">{$image.name.label}:</p>
		<p class="pagetext">{$image.name.output}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$image.description.label}:</p>
		<p class="pageinput">{$image.description.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">
			{$image.product_id.input}
			{$image.current_category_id.input}
			{$image.product_images_id.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$image.submit}{$image.cancel}</p>
	</div>
{$image.endform}
