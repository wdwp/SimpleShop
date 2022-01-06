{if isset($message) }<p style="color:red;">{$message}</p>{/if}
{$sku.startform}
    <div class="pageoverflow">
        <p class="pagetext">{$sku.name.label}:</p>
        <p class="pageinput">{$sku.name.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$sku.description.label}:</p>
        <p class="pageinput">{$sku.description.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$sku.submit}{$sku.cancel}{$sku.sku.input}</p>
    </div>
{$sku.endform}
