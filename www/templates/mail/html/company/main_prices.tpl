{strip}
<table class="list" width="40%">
    <tbody>
        <tr class="top-table">
            <th>Handling Cost</th>
            <th>Storage Cost</th>
            <th>Currency</th>
            <th>Date</th>
            <th>Created By</th>
            <th>Delete</th>
        </tr>
		{if isset($prices_list) && !empty($prices_list)}
        {foreach from=$prices_list item=row}
        <tr>
            <td><input type="text" name="form[{$row.id}][handling_cost]" value="{$row.handling_cost|string_format:'%.2f'}" class="max text-right"></td>
            <td><input type="text" name="form[{$row.id}][storage_cost]" value="{$row.storage_cost|string_format:'%.2f'}" class="max text-right"></td>
            <td>
				<select name="form[{$row.id}][currency]">
					<option value=""{if empty($row.currency)} selected="selected"{/if}>--</option>
                    <option value="usd"{if !empty($row.currency) && $row.currency == 'usd'} selected="selected"{/if}>$</option>
                    <option value="eur"{if !empty($row.currency) && $row.currency == 'eur'} selected="selected"{/if}>€</option>
                    <option value="gbp"{if !empty($row.currency) && $row.currency == 'gbp'} selected="selected"{/if}>£</option>
                 </select>
			</td>
            <td><input type="text" name="form[{$row.id}][date]" value="{$row.date|date_format:'%d/%m/%Y'}" class="max datepicker"></td>
			<td><p>{$row.user.login|escape:'html'}</p></td>
            <td><a href="javascript: void(0);" onclick="if (confirm('Am I sure?')) location.href='/company/{$row.id}/removeprices';" class="delete">delete</a></td>
        </tr>
        {/foreach}
		{/if}
        <tr>
            <td><input type="text" name="form[0][handling_cost]" value="" class="max text-right"></td>
            <td><input type="text" name="form[0][storage_cost]" value="" class="max text-right"></td>
            <td>
				<select name="form[0][currency]">
					<option value=""{if empty($row.currency)} selected="selected"{/if}>--</option>
                    <option value="usd">$</option>
                    <option value="eur">€</option>
                    <option value="gbp">£</option>
                 </select>
			</td>
            <td><input type="text" name="form[0][date]" value="" class="max datepicker"></td>
            <td></td>
            <td></td>
        </tr>            
    </tbody>
</table>
{/strip}					
		