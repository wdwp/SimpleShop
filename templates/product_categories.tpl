{if isset($message) }<p style="color:red;">{$message}</p>{/if}
{$categories.startform}
    {foreach from=$categories.list item=entry}
    <div class="pageoverflow">
        <p class="pagetext">{$entry.name}</p>
        <p class="pageinput">{$entry.link_delete}</p>
    </div>
    {foreachelse}
      No Categories
    {/foreach}
    <div class="pageoverflow">
        <p class="pagetext">{$categories.categories.label} {$categories.categories.list}</p>
        <p class="pageinput">{$categories.categories.submit}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$product.product_id.input}{$product.current_category_id.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$categories.cancel}{$product.add_new}</p>
    </div>
{$categories.endform}
