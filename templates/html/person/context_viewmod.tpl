
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		<table class="timestamp">
        <tr>
            <td style="font-weight: bold; text-align: right;">Created : </td>
            <td>                        
            {if isset($person.created_at) && !empty($person.created_at)}
                {$person.created_at|date_human}{if isset($person.author)}, {$person.author.full_login}{/if}
            {else}
                {''|undef}                            
            {/if}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: right;">Modified : </td>
            <td>
            {if isset($person.modified_at) && !empty($person.modified_at)}
                {$person.modified_at|date_human}{if isset($person.modifier)}, {$person.modifier.full_login}{/if}
            {else}
                {''|undef}                            
            {/if}
            </td>
        </tr>
    </table>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
                <input type="button" value="To List" class="btn btn-default" style=''onclick="location.href='/person';">
                <input type="button" class="btn btn-primary" value="Edit" style="margin: 7px;" onclick="location.href='/person/{$person.id}/edit';">
	</li>	
</ul>