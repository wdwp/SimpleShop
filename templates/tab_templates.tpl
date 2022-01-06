{if isset($message) && $message!=''}
	<p style="color:red;">{$message}</p>
{/if}
{$templates.startform}
	<div class="pageoverflow">
		<p class="pagetext">{$templates.catlist_template.label}:</p>
		<p class="pageinput">{$templates.catlist_template.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$templates.submit}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$templates.categories_template.label}:</p>
		<p class="pageinput">{$templates.categories_template.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$templates.submit}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$templates.proddetail_template.label}:</p>
		<p class="pageinput">{$templates.proddetail_template.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$templates.submit}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$templates.prodfeat_template.label}:</p>
		<p class="pageinput">{$templates.prodfeat_template.input}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$templates.submit}</p>
	</div>
{$templates.endform}
