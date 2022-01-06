{if isset($message) }<p style="color:red;">{$message}</p>{/if}
{$sku.startform}
    <div class="pageoverflow">
        <p class="pagetext">{$sku.sku.label}:</p>
        <p class="pageinput">{$sku.sku.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$sku.description.label}:</p>
        <p class="pageinput">{$sku.description.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$sku.submit}</p>
    </div>
{$sku.endform}
