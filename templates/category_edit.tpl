{if isset($message) }<p>{$message}</p>{/if}
{$category.startform}
    <div class="pageoverflow">
        <p class="pagetext">{$category.parent_id.label}:</p>
        <p class="pageinput">{$category.parent_id.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$category.name.label}:</p>
        <p class="pageinput">{$category.name.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$category.description.label}:</p>
        <p class="pageinput">{$category.description.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$category.image.label}:</p>
        <p class="pageinput">{$category.image.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$category.active.label}</p>
        <p class="pageinput">{$category.active.input}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext">{$category.position.label}:</p>
        <p class="pageinput">{$category.position.input}</p>
    </div>
    <div class="pageoverflow">
    <p class="pagetext"></p>
        {if isset($category.category_id) && isset($category.current_category_id)}
        <p class="pageinput">{$category.category_id.input}{$category.current_category_id.input}
        {/if}
        {if isset($category.old_image)}{$category.old_image.input}{/if}
    </div>
    <div class="pageoverflow">
        <p class="pageinput">{$category.submit}{$category.cancel}</p>
    </div>
{$category.endform}
