<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Number : </td>
                    <td>{$form.number|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Date : </td>
                    <td>{if $form.date > 0}{$form.date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Company : </td>
                    <td>{if isset($form.company)}{$form.company.doc_no}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Kind : </td>
                    <td>{$form.kind_title|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">State of Supply : </td>
                    <td>{$form.state_of_supply_title|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Standard : </td>
                    <td>{if isset($form.standard)}{$form.standard.title}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
        <td class="text-top" style="width: 34%;"></td>
    </tr>
</table>
                
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="packing-list-title">Items</h3>
{if !empty($items)}
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th>id</th>
        <th>Heat / Lot</th>
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br>mm</th>
        <th>Width,<br>mm</th>
        <th>Length,<br>mm</th>
        <th>Weight,<br>{'mt'|wunit}</th>
        <th>Status</th>
    </tr>
    {foreach $items as $item}
    <tr>
        <td>{$item.steelitem.id}</td>
        <td>{$item.steelitem.properties.heat_lot|undef}</td>
        <td>{$item.steelitem.guid|escape:'html'|undef}</td>
        <td>{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'|undef}{else}{''|undef}{/if}</td>
        <td>{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%d'}{/if}</td>
        <td>{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%d'}{/if}</td>
        <td>{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%d'}{/if}</td>
        <td>{if !empty($item.steelitem.unitweight)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        {if $item.steelitem.order_id > 0}
        <td>{$item.steelitem.status_title}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a></td>
        {else}
        <td>{$item.steelitem.status_title|undef}</td>
        {/if}        
    </tr>
    {/foreach}
</table>
{/if}

<div class="pad"></div>
<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Created : </td>
                    <td>{$form.author.login}, {$form.created_at|date_format:'d/m/Y'}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Modified : </td>
                    <td>{$form.modifier.login}, {$form.modified_at|date_format:'d/m/Y'}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>