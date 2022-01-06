{* List of product data *}
{if isset($canonical)}{assign var='canonical' value=$canonical scope='global'}{/if}

<h2 ID="productheader">{$productname}</h2>
<p>SKU: {$itemnumber}<p>

<table id="producttable">
<tr>
<td>

{if $itemcount > 0}
<table ID="catinfo">
<tr>
<td>
 <img name="item_image" id="item_image" src="{$pimage}" title="{$imagedesc|strip_tags|escape:'html'|truncate}">
 <div class="ProdThumbnails">
  {section name=ind loop=$entryarray}
   <a href="javascript:repl('{$items}','{$imagedesc}')">{image src=$entryarray[ind]->imagethumb alt=$entryarray[ind]->imagedesc|strip_tags|escape:'html'|truncate}</a>
  {/section}
<br>
{if $itemcount > 1}
 {foreach from=$items item=entry}
  <a href="javascript:repl('{$entry->fullpathimage}','{$entry->imagedesc|strip_tags|escape:'html'|truncate}')">{image src=$entry->imagethumb  alt=$entry->imagedesc|strip_tags|escape:'html'|truncate}</a>&nbsp;
 {/foreach}
 {/if}
 </div>
</td>
</tr>
</table>
{/if}
</td>
<td>
{$description}
<p>{$mod->Lang('label_product_netweight')}: {$netweight}</p>

<table cellspacing="0" class="pagetable">
    <tbody>
        <tr>
            <td>{$cur_symbol}&nbsp;{$price}</td>
            <td>&nbsp;</td>
            <td>{if isset($addproduct)}{$addproduct}{/if}</td>
        </tr>
    </tbody>
</table>
<br>
{if isset($attributes.itemcount) && $attributes.itemcount > 0}
<table cellspacing="0" class="productattr">
 <thead>
  <tr>
   <th>{$attributes.namecolumn}</th>
   <th>{$attributes.descriptioncolumn}</th>
   <th>{$attributes.pricecolumn}</th>
   <th>{$attributes.addattributecolumn}</th>
  </tr>
 </thead>
 <tbody>
   {foreach from=$attributes.list item=entry}
    <tr>
     <td>{$entry.name}</td>
     <td>{$entry.description}</td>
     <td>{if $entry.displayonly}&nbsp;{else}{$entry.price}{/if}</td>
     <td>{if isset($entry.addattribute)}{$entry.addattribute}{/if}</td>
    </tr>
   {/foreach}
 </tbody>
</table>
{/if}

</td>
</tr>
</table>
<p><span class='back'>&#8592; <a href="#" title="{$mod->Lang('return')}" onclick="history.back()">{$mod->Lang('return')}</a></span></p>
<script>
function repl(img, title)
   {
   document.item_image.src=img;
   document.item_image.title=title;
   }
</script>