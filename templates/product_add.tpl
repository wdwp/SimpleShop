{if isset($message) }<p style="color:red;">{$message}</p>{/if}
{$product.startform}
    <div class="pageoverflow">
        <p class="pagetext">{$product.category_id.label}:</p>
        <p class="pageinput">{$product.category_id.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.name.label}:</p>
        <p class="pageinput">{$product.name.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.description.label}:</p>
        <p class="pageinput">{$product.description.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.price.label}:</p>
        <p class="pageinput">{$product.price.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.active.label}:</p>
        <p class="pageinput">{$product.active.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.featured.label}:</p>
        <p class="pageinput">{$product.featured.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.netweight.label}:</p>
        <p class="pageinput">{$product.netweight.input}&nbsp;{$product.netweight.unit}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.vatcode.label}:</p>
        <p class="pageinput">{$product.vatcode.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.sku.label}:</p>
        <p class="pageinput">{$product.sku.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.itemnumber.label}:</p>
				<p class="pageinput">{$product.itemnumber.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.position.label}:</p>
        <p class="pageinput">{$product.position.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$product.maxattributes.label}:</p>
			<p class="pageinput">{$product.maxattributes.input}</p>
    </div>
    {if $DTIavailable}
		<div class="pageoverflow">
			<p class="pagetext">{$product.cost_price.label}:</p>
			<p class="pageinput">{$product.cost_price.input}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$product.barcode.label}:</p>
			<p class="pageinput">{$product.barcode.input}</p>
		</div>
		{if $DTInventoryversion != 'free'}
			<div class="pageoverflow">
				<p class="pagetext">{$product.item_class.label}:</p>
				<p class="pageinput">{$product.item_class.input}</p>
			</div>
		{/if}
    {/if}
    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$product.submit}{$product.cancel}
				{if isset($product.product_id)}{$product.product_id.input}{/if}{$product.current_category_id.input}</p>
    </div>
    {if isset($product.categories)}
    {foreach from=$product.categories.list item=entry}
    <div class="pageoverflow">
        <p class="pagetext">{$entry.label}</p>
        <p class="pageinput">{$entry.input}</p>
    </div>
    {/foreach}

    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$product.categories.submit}</p>
    </div>
     {/if}
     {if isset($product.attributes)}
    {foreach from=$product.attributes.list item=entry}
    <div class="pageoverflow">
        <p class="pagetext">{$entry.label}</p>
        <p class="pageinput">{$entry.input}</p>
    </div>
    {/foreach}
    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$product.attributes.submit}</p>
    </div>
    {/if}
    {if isset($product.images)}
    {foreach from=$product.images.list item=entry}
    <div class="pageoverflow">
        <p class="pagetext"><img src="/uploads/images/products/{$entry.image}"></p>
        <p class="pageinput">{$entry.description}</p>
    </div>
    {/foreach}
    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$product.images.submit}</p>
    </div>
    {/if}
{$product.endform}
