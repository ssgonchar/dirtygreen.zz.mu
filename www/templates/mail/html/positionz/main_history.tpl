{if empty($list)}
No history for this position available
{else}
<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Rev.</th>
            <th width="8%" class="text-center">Steel Grade</th>
            <th width="5%" class="text-center">Thickness<br>{$list[0].dimension_unit}</th>
            <th width="5%" class="text-center">Width<br>{$list[0].dimension_unit}</th>
            <th width="5%" class="text-center">Length<br>{$list[0].dimension_unit}</th>
            <th width="7%" class="text-center">Unit Weight<br>{$list[0].weight_unit|wunit}</th>
            <th width="5%" class="text-center">Qtty<br>pcs</th>
            <th width="7%" class="text-center">Weight<br>{$list[0].weight_unit|wunit}</th>
            <th width="7%" class="text-center">Price<br>{$list[0].currency|cursign}/{$list[0].weight_unit|wunit}</th>
            <th width="7%" class="text-center">Value<br>{$list[0].currency|cursign}</th>
            <th width="8%" class="text-center">Delivery Time</th>
            <th class="text-center">Notes</th>
            <th class="text-center">Internal Notes</th>
            <th width="8%" class="text-center">Biz</th>
            <th width="15%" class="text-center">Action, Date, Person</th>
        </tr>    
        {section name=i loop=$list}
        <tr>
            <td>{count($list) - $smarty.section.i.index}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].steelgrade.title != $list[i].steelgrade.title} style="background-color: #f4c430;"{/if}>{$list[i].steelgrade.title|escape:'html'}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].thickness != $list[i].thickness} style="background-color: #f4c430;"{/if}>{$list[i].thickness|escape:'html'}{*&nbsp;{$list[i].dimension_unit}*}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].width != $list[i].width} style="background-color: #f4c430;"{/if}>{$list[i].width|escape:'html'}{*&nbsp;{$list[i].dimension_unit}*}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].length != $list[i].length} style="background-color: #f4c430;"{/if}>{$list[i].length|escape:'html'}{*&nbsp;{$list[i].dimension_unit}*}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].unitweight != $list[i].unitweight} style="background-color: #f4c430;"{/if}>{$list[i].unitweight|escape:'html'|string_format:'%.2f'}{*&nbsp;{$list[i].weight_unit|wunit}*}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].qtty != $list[i].qtty} style="background-color: #f4c430;"{/if}>{$list[i].qtty|escape:'html'|string_format:'%d'}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].weight != $list[i].weight} style="background-color: #f4c430;"{/if}>{$list[i].weight|escape:'html'|string_format:'%.2f'}{*&nbsp;{$list[i].weight_unit|wunit}*}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].price != $list[i].price} style="background-color: #f4c430;"{/if}>{$list[i].price|escape:'html'|string_format:'%.2f'}{*&nbsp;{$list[i].currency|cursign}/{$list[i].weight_unit|wunit}*}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].value != $list[i].value} style="background-color: #f4c430;"{/if}>{$list[i].value|escape:'html'|string_format:'%.2f'}{*&nbsp;{$list[i].currency|cursign}*}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].deliverytime_id != $list[i].deliverytime_id} style="background-color: #f4c430;"{/if}>{if isset($list[i].deliverytime)}{$list[i].deliverytime.title|escape:'html'}{/if}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].notes != $list[i].notes} style="background-color: #f4c430;"{/if}>{$list[i].notes|escape:'html'}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].internal_notes != $list[i].internal_notes} style="background-color: #f4c430;"{/if}>{$list[i].internal_notes|escape:'html'}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].biz.number_output != $list[i].biz.number_output} style="background-color: #f4c430;"{/if}>{$list[i].biz.number_output|escape:'html'}</td>
            <td style="line-height: 18px;">
                {if $list[i].tech_action == 'add'}Created
                {elseif $list[i].tech_action == 'edit'}Modified
                {elseif $list[i].tech_action == 'delete'}<b>Deleted</b>
                {elseif $list[i].tech_action == 'toorder'}{$list[i].tech_data} pcs{if !empty($list[i].stock_id)} from Stock{/if} to <a href="/order/{$list[i].tech_object_id}">{$list[i].tech_object_id|order_doc_no}</a>
                {elseif $list[i].tech_action == 'tostock'}{$list[i].tech_data} pcs from <a href="/order/{$list[i].tech_object_id}">Order # {$list[i].tech_object_id}</a> to Stock
                {/if}
                <br>{$list[i].record_at|date_human:false}, {$list[i].user.login}
            </td>
        </tr>
        {/section}
</table>
{/if}