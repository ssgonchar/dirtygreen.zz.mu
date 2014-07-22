{if empty($list)}
There are no history for this item
{else}
<table class="list" width="100%">
<tbody>
    <tr class="top-table" style="height: 25px;">
        <th rowspan="2" width="5%">Rev.</th>
        <th rowspan="2" width="10%">Plate Id</th>
        <th rowspan="2">Steel Grade</th>
        <th rowspan="2">Thickness,<br>{$list[0].dimension_unit}</th>
        <th rowspan="2">Width,<br>{$list[0].dimension_unit}</th>
        <th rowspan="2">Length,<br>{$list[0].dimension_unit}</th>
        <th rowspan="2">Weight,<br>{$list[0].weight_unit|wunit}</th>
        <th colspan="4" style="border-bottom : 1px solid #B9B9B9;">Changes In</th>
        <th rowspan="2" width="25%">Action, Date, Person</th>
    </tr>
    <tr class="top-table" style="height: 25px;">
        <th width="5%">Dimensions</th>
        <th width="5%">Status</th>
        <th width="5%">Chemical</th>
        <th width="5%">Mechanical</th>
    </tr>
    {foreach name=i from=$list item=row}
    <tr>
        <td onclick="location.href='/item/{$row.steelitem_id}/revision/{$row.revision}';">{count($list) - $smarty.foreach.i.index}</td>
        {if isset($row.guid_changed)}<td style="background-color: #f4c430;">{else}<td>{/if}{$row.guid|escape:'html'}</td>
        {if isset($row.steelgrade_changed)}<td style="background-color: #f4c430;">{else}<td>{/if}{$row.steelgrade.title|escape:'html'}</td>
        {if isset($row.thickness_changed)}<td style="background-color: #f4c430;">{else}<td>{/if}{if !empty($row.thickness)}{$row.thickness|escape:'html'}{/if}</td>
        {if isset($row.width_changed)}<td style="background-color: #f4c430;">{else}<td>{/if}{if !empty($row.width)}{$row.width|escape:'html'}{/if}</td>
        {if isset($row.length_changed)}<td style="background-color: #f4c430;">{else}<td>{/if}{if !empty($row.length)}{$row.length|escape:'html'}{/if}</td>
        {if isset($row.unitweight_changed)}<td style="background-color: #f4c430;">{else}<td>{/if}{if !empty($row.unitweight)}{$row.unitweight|escape:'html'}{/if}</td>
        {if isset($row.changes_in_dimensions)}<td style="background-color: #f4c430;">yes</td>{else}<td></td>{/if}
        {if isset($row.changes_in_status)}<td style="background-color: #f4c430;">yes</td>{else}<td></td>{/if}
        {if isset($row.changes_in_chemical)}<td style="background-color: #f4c430;">yes</td>{else}<td></td>{/if}
        {if isset($row.changes_in_mechanical)}<td style="background-color: #f4c430;">yes</td>{else}<td></td>{/if}
        <td style="line-height: 18px;">
            {if $row.tech_action == 'add'}
                {if $row.parent_id > 0}
                    Created as {if $row.rel == 't'}twin{elseif $row.rel == 'c'}cut{/if} of <a href="/item/history/{$row.parent_id}">item # {$row.parent_id}</a>
                {else}
                Created at <a href="/position/history/{$row.steelposition_id}">position # {$row.steelposition_id}</a>
                {/if}
            {elseif $row.tech_action == 'move'}Moved to <a href="/position/history/{$row.steelposition_id}">position # {$row.steelposition_id}</a>
            {elseif $row.tech_action == 'edit'}Modified
            {elseif $row.tech_action == 'delete'}<b>Deleted</b>
            {elseif $row.tech_action == 'toorder'}{$row.tech_data} pcs from Stock to <a href="/order/{$row.tech_object_id}">Order # {$row.tech_object_id}</a>
            {elseif $row.tech_action == 'tostock'}{$row.tech_data} pcs from <a href="/order/{$row.tech_object_id}">Order # {$row.tech_object_id}</a> to Stock
            {/if}
            <br>{$row.record_at|date_human:false}, {$row.user.login}
        </td>
    </tr>
    {/foreach}
</tbody>
</table>
{/if}