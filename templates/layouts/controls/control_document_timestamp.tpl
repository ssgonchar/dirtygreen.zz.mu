<table class="timestamp">
    <tr>
        <td style="font-weight: bold; text-align: right;">Created : </td>
        <td>                        
        {if isset($doc.created_at) && !empty($doc.created_at)}
            {$doc.created_at|date_human}{if isset($doc.author)}, {$doc.author.full_login}{/if}
        {else}
            {''|undef}                            
        {/if}
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold; text-align: right;">Modified : </td>
        <td>
        {if isset($doc.modified_at) && !empty($doc.modified_at)}
            {$doc.modified_at|date_human}{if isset($doc.modifier)}, {$doc.modifier.full_login}{/if}
        {else}
            {''|undef}                            
        {/if}
        </td>
    </tr>
</table>
