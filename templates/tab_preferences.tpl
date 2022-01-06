{if $message!=''}
	<p style="color:red;">{$message}</p>
{/if}
{$preferences.startform}
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.admin_name.label}</p>
        <p class="pageinput">{$preferences.admin_name.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.admin_email.label}:</p>
        <p class="pageinput">{$preferences.admin_email.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.shop_name.label}:</p>
        <p class="pageinput">{$preferences.shop_name.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.shop_phone.label}:</p>
        <p class="pageinput">{$preferences.shop_phone.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.shop_online.label}:</p>
        <p class="pageinput">{$preferences.shop_online.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.pricesinclvat.label}:</p>
        <p class="pageinput">{$preferences.pricesinclvat.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.weightunitmeasure.label}:</p>
        <p class="pageinput">{$preferences.weightunitmeasure.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.itemcapitalonly.label}:</p>
        <p class="pageinput">{$preferences.itemcapitalonly.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.allowdoubleitem.label}:</p>
        <p class="pageinput">{$preferences.allowdoubleitem.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.default_maxattributes.label}:</p>
        <p class="pageinput">{$preferences.default_maxattributes.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.default_sku.label}:</p>
        <p class="pageinput">{$preferences.default_sku.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.default_currency.label}:</p>
        <p class="pageinput">{$preferences.default_currency.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.default_symbol.label}:</p>
        <p class="pageinput">{$preferences.default_symbol.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.imagepath_category.label}:</p>
        <p class="pageinput">{$preferences.imagepath_category.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.imagepath_product.label}:</p>
        <p class="pageinput">{$preferences.imagepath_product.input}&nbsp;
			{$preferences.tnheight_product.label}:&nbsp;{$preferences.tnheight_product.input}&nbsp;
			{$preferences.tnwidth_product.label}:&nbsp;{$preferences.tnwidth_product.input}
			&nbsp;{$preferences.rebuildproductthumbnails}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.productpagelimit.label}:</p>
        <p class="pageinput">{$preferences.productpagelimit.input}</p>
    </div>
	<fieldset>
	<legend>{$preferences.inventorysettings.label}</legend>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.inventorytype.label}:</p>
        <p class="pageinput">{$preferences.inventorytype.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$preferences.salesinventtiming.label}:</p>
        <p class="pageinput">{$preferences.salesinventtiming.input}</p>
    </div>
	</fieldset>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$preferences.submit}{$preferences.searchreindex}</p>
	</div>
{$preferences.endform}
