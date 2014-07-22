{if !empty($list)}
    {if count($list) == 1}
        {$list[0].name|escape:'html'}        
    {else}
        {*debug*}
        {foreach from=$list item=row}

        <label for="cb-{$name}-{$row.id}"><input type="checkbox" id="cb-{$name}-{$row.id}" name="form[{$name}][{$row.id}]" value="{$row.id}">&nbsp;{$row.name|escape:'html'}&nbsp;&nbsp;&nbsp;</label><br/>
        
        {*$row|@debug_print_var*}
        {/foreach}
        <div class="separator"></div>
    {/if}
{else}
<span style="color: #aaa;">none</span>
{/if}