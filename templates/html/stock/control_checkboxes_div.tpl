{if !empty($list)}
    {if count($list) == 1}
        {$list[0].name|escape:'html'}        
    {else}
        {foreach from=$list item=row}
        
        <label for="cb-{$name}-{$row.id}"><input type="checkbox" id="cb-{$name}-{$row.id}" name="form[{$name}][{$row.id}]" value="{$row.id}">&nbsp;{$row.name|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
        <br/>
        {/foreach}
    {/if}
{else}
<span style="color: #aaa;">none</span>
{/if}