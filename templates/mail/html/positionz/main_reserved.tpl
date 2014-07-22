{if empty($list)}
    Nothing was found on my request
{else}
{if count($companies) > 1}
<div style="padding-bottom: 20px;">
    Companies : {if empty($company_id)}<b style="margin-left: 10px;">All</b>{else}<a href="/positions/reserved" style="margin-left: 10px;">All</a>{/if}
    {foreach from=$companies item=row}
    {if $company_id == $row.company.id}<b style="margin-left: 10px;">{$row.company.title|escape:'html'}</b>
    {else}<a href="/positions/reserved/filter/company:{$row.company.id}" style="margin-left: 10px;">{$row.company.title|escape:'html'}</a>{/if}
    {/foreach}
</div>
{/if}

<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="5%" class="text-left"><input type="checkbox" onchange="check_all(this, 'position'); calc_total('position');">&nbsp;Pos Id</th>
            <th width="8%">Steel Grade</th>
            <th width="5%" class="text-center">Thickness{if isset($dimension_unit)}<br>{$dimension_unit}{/if}</th>
            <th width="5%" class="text-center">Width{if isset($dimension_unit)}<br>{$dimension_unit}{/if}</th>
            <th width="5%" class="text-center">Length{if isset($dimension_unit)}<br>{$dimension_unit}{/if}</th>
            <th width="7%" class="text-center">Unit Weight{if isset($weight_unit)}<br>{$weight_unit|wunit}{/if}</th>
            <th width="5%" class="text-center">Qtty<br>pcs</th>
            <th width="7%" class="text-center">Weight{if isset($weight_unit)}<br>{$weight_unit|wunit}{/if}</th>
            <th width="7%" class="text-center">Price{if isset($currency) && isset($price_unit)}<br>{$currency|cursign}/{$price_unit|wunit}{/if}</th>
            <th width="7%" class="text-center">Value{if isset($currency)}<br>{$currency|cursign}{/if}</th>
            <th width="8%">Delivery Time</th>
            <th>Notes</th>
            <th>Internal Notes</th>
            <th>Company</th>
            <th>Expire At</th>
        </tr>    
        {foreach from=$list item=row}
        <tr id="position-{$row.steelposition_id}">
            <td class="text-left">
                <input type="checkbox" name="reserv[{$row.id}]" value="{$row.id}" class="cb-row-position" onchange="calc_total('position');">&nbsp;<a href="/position/{$row.steelposition_id}/edit">{$row.steelposition_id}</a>
            </td>
            <td class="pos">{$row.steelposition.steelgrade.title|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.thickness|escape:'html'}{if !isset($dimension_unit)} {$row.steelposition.dimension_unit}{/if}</td>
            <td class="text-center">{$row.steelposition.width|escape:'html'}{if !isset($dimension_unit)} {$row.steelposition.dimension_unit}{/if}</td>
            <td class="text-center">{$row.steelposition.length|escape:'html'}{if !isset($dimension_unit)} {$row.steelposition.dimension_unit}{/if}</td>
            <td class="text-center">{$row.steelposition.unitweight|number_format:2}{if !isset($weight_unit)} {$row.steelposition.weight_unit|wunit}{/if}</td>
            <td class="text-center" id="position-qtty-{$row.id}">{$row.qtty|escape:'html'|string_format:'%d'}</td>
            <td class="text-center" id="position-weight-{$row.id}">{($row.steelposition.unitweight * $row.qtty)|number_format:2}{if !isset($weight_unit)} {$row.steelposition.weight_unit|wunit}{/if}</td>
            <td class="text-center">{$row.steelposition.price|number_format:2:false}{if !isset($price_unit) || !isset($currency)} {$row.steelposition.currency|cursign}/{$row.steelposition.price_unit|wunit}{/if}</td>
            <td class="text-center" id="position-value-{$row.id}">
            {if $row.steelposition.weight_unit == 'lb' && $row.steelposition.price_unit == 'cwt'}
                {($row.steelposition.unitweight * $row.qtty * $row.steelposition.price / 100)|number_format:2:false}
            {else}
                {($row.steelposition.unitweight * $row.qtty * $row.steelposition.price)|number_format:2:false}
            {/if} {if !isset($currency)} {$row.steelposition.currency|cursign}{/if}
            </td>
            <td>{if isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td>{$row.steelposition.notes|escape:'html'}</td>
            <td>{$row.steelposition.internal_notes|escape:'html'}</td>
            <td><a href="/company/{$row.company.id}">{$row.company.doc_no|escape:'html'}</a></td>
            <td>{$row.expire_at|escape:'html'|date_format:'d/m/Y'}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}
