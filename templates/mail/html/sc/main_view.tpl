<table width="100%">
    <tr>
        <td width="25%">
            <div class="text-right title-field">Order Id : </div>
            <div class="value-field"><a href="/order/{$order.id}">{$order.doc_no}</a></div>
        </td>
        <td width="25%">
            <div class="text-right title-field">Buyer Company : </div>
            <div class="value-field">{if isset($order.company)}{$order.company.title|escape:'html'}{else}<i>not set</i>{/if}</div>
        </td>
        <td width="25%">
            <div class="text-right title-field">Delivery Basis : </div>
            <div class="value-field">{if isset($order.delivery_point_title)}{$order.delivery_point_title|escape:'html'}{else}<i>not set</i>{/if}</div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="text-right title-field">Order for : </div>
            <div class="value-field">{if isset($order.order_for_title)}{$order.order_for_title|escape:'html'}{else}<i>not set</i>{/if}</div>        
        </td>
        <td>
            <div class="text-right title-field">Person : </div>
            <div class="value-field">{if isset($sc.person)}{$sc.person.full_name}{else}<i>not set</i>{/if}</div>
        </td>
        <td>
            <div class="text-right title-field">Invoicing Basis : </div>
            <div class="value-field">{if isset($order.invoicingtype)}{$order.invoicingtype.title|escape:'html'}{else}<i>not set</i>{/if}</div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="text-right title-field">BIZ : </div>
            <div class="value-field">{if isset($order.biz)}{$order.biz.number_output}{else}<i>not set</i>{/if}</div>        
        </td>
        <td>
            <div class="text-right title-field">Buyer Ref. : </div>
            <div class="value-field">{if !empty($order.buyer_ref)}{$order.buyer_ref|escape:'html'}{else}<i>not set</i>{/if}</div>        
        </td>
        <td>
            <div class="text-right title-field">Payment Term : </div>
            <div class="value-field">{if isset($order.paymenttype)}{$order.paymenttype.title|escape:'html'}{else}<i>not set</i>{/if}</div>                
        </td>
        <td>
        
        </td>
    </tr>
    <tr>
        <td>
            <div class="text-right title-field" style="background: white;">Order Status : </div>
            <div class="value-field{if !empty($order.status)} tr-order-{$order.status}{/if}" style="display: table-cell;">{if isset($order.status_title)}{$order.status_title}{else}<i>Unregistered</i>{/if}</div>
        </td>
        <td colspan="2">
            <div class="text-right title-field">Pdf : </div>
            <div class="value-field">{if isset($sc.attachment)}<a class="pdf" target="_blank" href="/file/{$sc.attachment.secret_name}/{$sc.attachment.original_name}">{$sc.attachment.original_name}</a>{else}<i>not set</i>{/if}</div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="text-right title-field" style="background: white;"></div>
            <div class="value-field" style="display: table-cell;"></div>
        </td>
    </tr>
</table>
<div class="pad"></div>

{if !empty($positions)}
<table id="positions" class="list" width="100%"><tbody>
    <tr class="top-table">
        <th width="8%">Steel Grade</th>
        <th>Thickness,<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit|escape:'html'}{/if}</span></th>
        <th>Width,<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit|escape:'html'}{/if}</span></th>
        <th>Length,<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit|escape:'html'}{/if}</span></th>
        <th>Unit Weight,<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit|wunit}{/if}</span></th>
        <th>Qtty,<br>pcs</th>
        <th>Weight,<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit|wunit}{/if}</span></th>
        <th>Price,<br><span class="lbl-price">{if isset($order.currency)}{$order.currency|cursign}{/if}/{if isset($order.price_unit)}{$order.price_unit|wunit}{/if}</span></th>
        <th>Value,<br><span class="lbl-cur">{if isset($order.currency)}{$order.currency|cursign}{/if}</span></th>
    </tr>
    {foreach from=$positions item=row}
    <tr>
        <td>{if !empty($row.steelgrade)}{$row.steelgrade.title|escape:'html'}{/if}</td>
        <td>{if isset($row.thickness)}{$row.thickness|escape:'html'|escape:'html'}{/if}</td>
        <td>{if isset($row.width)}{$row.width|escape:'html'|escape:'html'}{/if}</td>
        <td>{if isset($row.length)}{$row.length|escape:'html'|escape:'html'}{/if}</td>
        <td>{if isset($row.unitweight)}{$row.unitweight|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
        <td id="position-qtty-{$row.position_id}">{if isset($row.qtty)}{$row.qtty|escape:'html'|string_format:'%d'|escape:'html'}{/if}</td>
        <td id="position-weight-{$row.position_id}">{if isset($row.weight)}{$row.weight|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
        <td>{if isset($row.price)}{$row.price|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
        <td id="position-value-{$row.position_id}">{if isset($row.value)}{$row.value|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
    </tr>        
    {/foreach}
    <tr>
        <td colspan="5" class="form-td-title-b">Total : </td>
        <td class="text-center" id="lbl-total-qtty" style="font-weight: bold;">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</td>
        <td class="text-center" id="lbl-total-weight" style="font-weight: bold;">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if}</td>
        <td></td>
        <td class="text-center" id="lbl-total-value" style="font-weight: bold;">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if}</td>
    </tr>    
</tbody></table>
{else}
<span id="lbl-positions"{if !empty($positions)} style="display: none;"{/if}>No position in order</span>
{/if}

<div class="pad"></div>
<table width="100%">    
    <tr height="32">
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b text-top">{if isset($order) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Collection Address{else}Delivery Point{/if}</td>
                    <td class="text-center text-top" width="5px"> : </td>
                    <td class="text-top">{if isset($sc.delivery_point) && !empty($sc.delivery_point)}{$sc.delivery_point|escape:'html'|nl2br}{else}<i>not set</i>{/if}</td>
                </tr>
            </table>
        <td>
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b text-top">{if isset($order) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if}</td>
                    <td class="text-center text-top" width="5px"> : </td>
                    <td class="text-top">{if isset($sc.delivery_date) && !empty($sc.delivery_date)}{$sc.delivery_date|escape:'html'|nl2br}{else}<i>not set</i>{/if}</td>
                </tr>
            </table>
        <td>
    </tr>
    <tr height="32">
    {if isset($order) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b text-top">Transport Mode</td>
                    <td class="text-center text-top" width="5px"> : </td>
                    <td class="text-top">{if isset($sc.transport_mode) && !empty($sc.transport_mode)}{$sc.transport_mode|escape:'html'|nl2br}{else}<i>not set</i>{/if}</td>
                </tr>
            </table>
        <td>
    {else}
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b text-top">Delivery Cost</td>
                    <td class="text-center text-top" width="5px"> : </td>
                    <td class="text-top">{if isset($order.delivery_cost)}{$order.delivery_cost|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>
            </table>
        <td>
    {/if}
    </tr>    
</table>
<div class="pad"></div>

<h3>Special Requirements</h3>
<div class="pad1"></div>

{if empty($special_requirements)}
    <i>not set</i>
{else}
<table width="100%">    
    {foreach from=$special_requirements item=row}
    <tr>
        {if isset($row.1)}
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="text-right text-top" width="120px" style="font-weight: bold;">{$row.1.title}</td>
                    <td class="text-center text-top" width="5px"> : </td>
                    <td class="text-top">{$row.1.value|escape:'html'|nl2br}</td>
                </tr>
            </table>
        <td>
        {/if}
        {if isset($row.2)}
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="text-right text-top" width="120px" style="font-weight: bold;">{$row.2.title}</td>
                    <td class="text-center text-top" width="5px"> : </td>
                    <td class="text-top">{$row.2.value|escape:'html'|nl2br}</td>
                </tr>
            </table>
        <td>
        {/if}
    </tr>
    {/foreach}
</table>
{/if}

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='sc' object_id=$sc.id}