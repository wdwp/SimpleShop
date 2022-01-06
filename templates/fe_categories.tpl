{* Front End: List of apex categories *}
<div class="catlistheader ">
	{$label_categories}
</div>
{if isset($items)}
{foreach from=$items item=entry}
  {if isset($entry->image)}
    <div class="catimagethumb">
       {image src=$entry->image alt=$entry->description|strip_tags|escape:'html' width="100" height="100"}&nbsp;{$entry->name}
    </div>
  {else}
        <div class="catlistline">
            {$entry->name}
        </div>
  {/if}
{/foreach}
{/if}

{* New category listing *}
{if isset($categories)}
<table width=100% border=0>
{if isset($products_in_root_category) && $products_in_root_category != 0}
 <tr>
  <td colspan=2>{$rootcat} ({$products_in_root_category})</td>
 </tr>
{/if}
{foreach $categories as $category}
 <tr>
  <td>
{for $foo=0 to $category['level']}&nbsp;&nbsp;&nbsp;{/for}
{if isset($category['link'])}{$category['link']}{else}{$category['name']}{/if} ({$category['num_products']})
  </td>
 </tr>
{/foreach}
</table>
{/if}