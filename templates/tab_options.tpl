{if isset($message) && $message!=''}
	<p style="color:red;">{$message}</p>
{/if}
{$options.startform}
	<div class="pageoverflow">
		<p class="pageinput">{$options.submit}</p>
	</div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.admin_name.label}</p>
        <p class="pageinput">{$options.admin_name.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.shop_name.label}:</p>
        <p class="pageinput">{$options.shop_name.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.pricesinclvat.label}:</p>
        <p class="pageinput">{$options.pricesinclvat.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.weightunitmeasure.label}:</p>
        <p class="pageinput">{$options.weightunitmeasure.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.itemcapitalonly.label}:</p>
        <p class="pageinput">{$options.itemcapitalonly.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.allowdoubleitem.label}</p>
        <p class="pageinput">{$options.allowdoubleitem.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.default_maxattributes.label}:</p>
        <p class="pageinput">{$options.default_maxattributes.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.default_sku.label}:</p>
        <p class="pageinput">{$options.default_sku.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.default_currency.label}:</p>
        <p class="pageinput">{$options.default_currency.input}&nbsp;
        {$options.default_symbol.label}:&nbsp;
        {$options.default_symbol.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.imagepath_category.label}:</p>
        <p class="pageinput">{$options.imagepath_category.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.imagepath_product.label}:</p>
        <p class="pageinput">{$options.imagepath_product.input}&nbsp;
			{$options.tnheight_product.label}:&nbsp;{$options.tnheight_product.input}&nbsp;
			{$options.tnwidth_product.label}:&nbsp;{$options.tnwidth_product.input}
			&nbsp;{$options.rebuildproductthumbnails}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.productpagelimit.label}:</p>
        <p class="pageinput">{$options.productpagelimit.input}</p>
    </div>
	<fieldset>
	<legend>{$options.inventorysettings.label}</legend>
    <div class="pageoverflow">
        <p class="pagetext">{$options.inventorytype.label}:</p>
        <p class="pageinput">{$options.inventorytype.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.salesinventtiming.label}:</p>
        <p class="pageinput">{$options.salesinventtiming.input}</p>
    </div>
	</fieldset>
	<fieldset>
	<legend>{$options.numberformatting.label}</legend>
	{if $CartMSInstalled == false}
    <div class="pageoverflow">
        <p class="pagetext">{$options.decimalpositionsprice.label}:</p>
        <p class="pageinput">{$options.decimalpositionsprice.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.decimalseparatorprice.label}:</p>
        <p class="pageinput">{$options.decimalseparatorprice.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.thousandseparatorprice.label}:</p>
        <p class="pageinput">{$options.thousandseparatorprice.input}</p>
    </div>
    {/if}
    <div class="pageoverflow">
        <p class="pagetext">{$options.decimalpositionsweight.label}:</p>
        <p class="pageinput">{$options.decimalpositionsweight.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.decimalseparatorweight.label}:</p>
        <p class="pageinput">{$options.decimalseparatorweight.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$options.thousandseparatorweight.label}:</p>
        <p class="pageinput">{$options.thousandseparatorweight.input}</p>
    </div>
	</fieldset>
    <div class="pageoverflow">
        <p class="pagetext">{$options.displayquickselector.label}:</p>
        <p class="pageinput">{$options.displayquickselector.input}</p>
    </div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$options.submit}{$options.searchreindex}</p>
	</div>
{$options.endform}
