<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="5%" class="text-left">Id</th>
            <th width="10%">Plate Id</th>
            <th width="8%">Steel Grade</th>
            <th width="7%">Thickness,<br>{$item.dimension_unit}</th>
            <th width="7%">Width,<br>{$item.dimension_unit}</th>
            <th width="7%">Length,<br>{$item.dimension_unit}</th>
            <th width="7%">Weight,<br>{$item.weight_unit|wunit}</th> 
            <th width="7%">Price,<br>{$item.currency|cursign}/{$item.weight_unit|wunit}</th>
            <th width="7%">Value,<br>{$item.currency|cursign}</th>
            <th width="5%">Is Virtual</th>
            <th>Location</th>
            <th>Internal Notes</th>
            <th>Order</th>
        </tr>
        {foreach name=i from=$list item=row}
        <tr{if $row.steelitem.id == $item.id} class="selected-bold"{/if}>
            <td class="text-left"><a href="/item/{$row.steelitem.id}/edit">{$row.steelitem.id}</a></td>
            <td>{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{else}{''|undef}{/if}</td>
            <td>{if isset($row.steelitem.steelgrade)}{$row.steelitem.steelgrade.title|escape:'html'}{/if}</td>
            <td class="text-center">{if !empty($row.steelitem.thickness)}{$row.steelitem.thickness|escape:'html'}{/if}</td>
            <td class="text-center">{if !empty($row.steelitem.width)}{$row.steelitem.width|escape:'html'}{/if}</td>
            <td class="text-center">{if !empty($row.steelitem.length)}{$row.steelitem.length|escape:'html'}{/if}</td>
            <td class="text-center">{if !empty($row.steelitem.unitweight)}{$row.steelitem.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td class="text-center">{if !empty($row.steelitem.price)}{$row.steelitem.price|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td class="text-center">{if !empty($row.steelitem.value)}{$row.steelitem.value|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td class="text-center">{if !empty($row.steelitem.is_virtual)}yes{else}no{/if}</td>
            <td>{if isset($row.steelitem.stockholder)}{$row.steelitem.stockholder.title|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.parent_id)}{$row.steelitem.rel_title}{/if}</td>
            <td><a href="/order/selectitems/{$row.steelitem.order_id}/position:{$row.steelitem.steelposition_id}" title="Order # {$row.steelitem.order_id}">Order # {$row.steelitem.order_id}</a></td>
        </tr>        
        {/foreach}
    </tbody>    
</table>
