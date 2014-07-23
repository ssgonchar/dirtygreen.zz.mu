<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Number : </td>
                    <td>
                        <input class="narrow" type="text" name="form[number]"{if !empty($form.number)} value="{$form.number}"{/if}/>
                   </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Date : </td>
                    <td>
                        <input class="narrow datepicker" type="text" name="form[date]"{if !empty($form.date)} value="{$form.date|date_format:'d/m/Y'}"{/if} />
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Company : </td>
                    <td>
                        <input type="text" class="autocomplete company-text normal ui-autocomplete-input" name="form[company_title]" value="{if !empty($form.company_title)}{$form.company_title|escape:'html'}{/if}" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" />
                        <input type="hidden" class="autocomplete company-id" name="form[company_id]" value="{if !empty($form.company_id)}{$form.company_id|escape:'html'}{else}0{/if}" />
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Kind : </td>
                    <td>
                        <select class="normal" name="form[kind]">
                            <option value="0">--</option>
                            <option value="{$smarty.const.OC_KIND_QUALITY}"{if $form.kind == $smarty.const.OC_KIND_QUALITY} selected="selected"{/if}>Quality</option>
                            <option value="{$smarty.const.OC_KIND_UST}"{if $form.kind == $smarty.const.OC_KIND_UST} selected="selected"{/if}>UST</option>
                            <option value="{$smarty.const.OC_KIND_TEST_REPORT}"{if $form.kind == $smarty.const.OC_KIND_TEST_REPORT} selected="selected"{/if}>Test Report</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">State of Supply : </td>
                    <td>
                        <select class="normal" name="form[state_of_supply]">
                            <option value="0">--</option>
                            <option value="{$smarty.const.OC_STATE_OF_SUPPLY_NORMALIZED}"{if $form.state_of_supply == $smarty.const.OC_STATE_OF_SUPPLY_NORMALIZED} selected="selected"{/if}>Normalized</option>
                            <option value="{$smarty.const.OC_STATE_OF_SUPPLY_AS_ROLLED}"{if $form.state_of_supply == $smarty.const.OC_STATE_OF_SUPPLY_AS_ROLLED} selected="selected"{/if}>As Rolled</option>
                        </select>
                    </td>
                </tr>                 
            </table>
        </td>
        <td class="text-top" style="width: 34%;">
            <table class="form" width="100%">
                <tr>
                    <td class="text-right">Standard : </td>
                    <td>
                        <select id="standard_id" name="form[standard_id]" class="normal">
                            <option value="0">--</option>
                            {foreach $standards as $row}
                            <option value="{$row.oc_standard.id}"{if isset($form.standard_id) && $row.oc_standard.id == $form.standard_id} selected="selected"{/if}>{$row.oc_standard.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">or : </td>
                    <td><input type="text" id="standard_new" name="form[standard_new]" class="normal"{if isset($form.standard_new)} value="{$form.standard_new|escape:'html'}"{/if}></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
                
<div class="pad"></div>

<h3 class="packing-list-title" style="margin-bottom: 10px;">Items</h3>
<span class="packing-list-is-empty" style="display: {if empty($items)}inline{else}none{/if};">There are no items</span>
{if !empty($items)}
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        {if !isset($form.id) || empty($form.id)}
        <th style="width: 20px;"></th>        
        {/if}
        <th>id</th>
        <th>Heat / Lot</th>
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br>mm</th>
        <th>Width,<br>mm</th>
        <th>Length,<br>mm</th>
        <th>Weight,<br>{'mt'|wunit}</th>
        <th>Status</th>
        {if isset($form.id) && !empty($form.id)}
        <th style="width: 20px;"></th>
        {/if}
    </tr>
    {foreach $items as $item}
    <tr class="item{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.steelitem.status_id}{/if}" data-oc_id="{$item.oc_id}" data-steelitem_id="{$item.steelitem_id}">
        {if !isset($form.id) || empty($form.id)}
        <td>
            <input class="single-checkbox type-id-{$item.steelitem.owner_id}" rel="packing-list" type="checkbox" name="items[{$item.steelitem.id}][checked]" value="{$item.steelitem.id}"{if !isset($item.checked) || $item.checked > 0} checked="checked"{/if} />
        </td>
        {/if}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.id}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.properties.heat_lot|undef}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.guid|escape:'html'|undef}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'|undef}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|number_format:1}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_ton)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        {if $item.steelitem.order_id > 0}
        <td>{$item.steelitem.status_title}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a></td>
        {else}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.status_title|undef}</td>        
        {/if}        
        {if isset($form.id) && !empty($form.id)}
        <td><img class="item-delete" src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete"/></td>
        {/if}
    </tr>
    {/foreach}
</table>
{/if}