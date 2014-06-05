<table width="100%">
    <tr>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Stock :</td>
                    <td>
                        {if empty($stock_id)}{''|undef}
                        {else}
                            {foreach $stocks as $row}
                            {if !empty($stock_id) && $stock_id == $row.stock.id}{$row.stock.title|escape:'html'}{/if}
                            {/foreach}
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Plate Id :</td>
                    <td>{$plate_id|undef}</td>
                </tr>
            </table>
        </td>
        <td width="60%" class="text-top">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title">Location :</td>
                    <td id="locations">
                        {if !empty($locations)}
                            {if count($locations) == 1}{$locations[0].stockholder.title|escape:'html'}
                            {else}
                                {$is_selected_exists=false}
                                {foreach $locations as $row}
                                <span style="margin-right: 5px;">
                                {if isset($row.selected)}
                                    {$row.stockholder.doc_no|escape:'html'}&nbsp;({$row.stockholder.city.title|escape:'html'});&nbsp;
                                    {$is_selected_exists=true}
                                {/if}
                                </span>
                                {/foreach}
                                {if !$is_selected_exists}{''|undef}{/if}
                            {/if}
                        {else}{''|undef}
                        {/if}
                    </td>
                </tr>
                <tr height="32">
                    <td class="form-td-title">Type :</td>
                    <td>
                        {if isset($type_r)}Real
                        {else if isset($type_v)}Virtual
                        {else if isset($type_t)}Twin
                        {else if isset($type_c)}Cut
                        {else if isset($available) && !empty($available)}Only Available Items
                        {else}{''|undef}
                        {/if}
                    </td>
                </tr>
            </table>
        </td>
        <td width="10%" class="text-right text-middle" style="padding-right: 0;"></td>
    </tr>
</table>

<div class="pad-10"></div>

<div>
    <table width="100%">
        <tr>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Thickness :</td>
                        <td>{$thickness|undef}</td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Width :</td>
                        <td>{$width|undef}</td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Length :</td>
                        <td>{$length|undef}</td>
                    </tr>
                </table>
            </td>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Steel Grade :</td>
                        <td>
                            {if !empty($steelgrade_id)}
                                {foreach from=$steelgrades item=row}
                                {if $steelgrade_id == $row.steelgrade.id}{$row.steelgrade.title|escape:'html'}{/if}
                                {/foreach}
                            {else}{''|undef}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Weight :</td>
                        <td>{$weight|undef}</td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Notes :</td>
                        <td>{$notes|undef}</td>
                    </tr>
                </table>
            </td>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Order :</td>
                        <td>
                           {if !empty($order_id)}
                                {foreach $orders as $row}
                                {if $order_id == $row.order_id}{$row.order.doc_no_full|escape:'html'}{/if}
                                {/foreach}
                            {else}{''|undef}
                            {/if}
                        </td>
                   </tr>
                </table>
            </td>
            <td width="10%" class="text-right text-middle" style="padding-right: 0;"></td>
        </tr>
    </table>
</div>
                        
<div class="pad1"></div>

{if empty($list)}{if !empty($filter)}Nothing was found on my request{/if}
{else}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th width="2%">Id</th>
                <th width="5%">Plate Id</th>
                <th width="8%">Steel Grade</th>
                <th width="5%" class="text-center">Thickness<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
                <th width="5%" class="text-center">Width<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
                <th width="5%" class="text-center">Length<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
                <th width="7%" class="text-center">Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
                <th width="7%" class="text-center">List Price<br>{if isset($stock)}{$stock.currency_sign}/{$stock.weight_unit|wunit}{/if}</th>
                <th width="7%" class="text-center">Purchase Price per Ton</th>
                <th>In DDT</th>
                <th>Internal Notes</th>
                <th>Location</th>
                <th>Owner</th>
                <th>Status</th>
            </tr>
            {foreach $list as $row}
            <tr {if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if}>
                <td>{$row.steelitem.id|escape:'html'|undef}</td>
                <td>{$row.steelitem.guid|escape:'html'|undef}</td>
                <td>{if isset($row.steelitem.steelgrade)}{$row.steelitem.steelgrade.title|escape:'html'}{else}{''|undef}{/if}</td>
                <td class="text-center">{$row.steelitem.thickness|escape:'html'}</td>
                <td class="text-center">{$row.steelitem.width|escape:'html'}</td>
                <td class="text-center">{$row.steelitem.length|escape:'html'}</td>
                <td class="text-center" id="item-weight-{$row.steelitem_id}">{$row.steelitem.unitweight|escape:'html'|string_format:'%.2f'}</td>
                <td class="text-center">{$row.steelitem.price|escape:'html'|string_format:'%.2f'}</td>
                <td class="text-center">{$row.steelitem.purchase_price|escape:'html'|string_format:'%.2f'}{if !empty($row.steelitem.purchase_currency)} {$row.steelitem.purchase_currency|cursign}{/if}</td>
                <td class="text-center">{if $row.steelitem.in_ddt_id > 0 && $row.steelitem.in_ddt.company_id == $row.steelitem.stockholder_id}{$row.steelitem.in_ddt_number} dd {$row.steelitem.in_ddt_date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                <td>
                    {if $row.steelitem.parent_id > 0}{if $row.steelitem.rel == 't'}Twin of{else if $row.steelitem.rel == 'c'}Cut from{/if} : {if !empty($row.steelitem.parent.guid)}{$row.steelitem.parent.guid|escape:'html'}{else}#{$row.steelitem.parent_id}{/if}{/if}<br />
                    {$row.steelitem.internal_notes|escape:'html'|undef}
                </td>
                <td>{if isset($row.steelitem.stockholder)}{$row.steelitem.stockholder.title|escape:'html'}{else}{''|undef}{/if}</td>
                <td>{if isset($row.steelitem.owner)}{$row.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
                
                {if $row.steelitem.order_id > 0}<td>{$row.steelitem.status_title}<br>{$row.steelitem.order_id|order_doc_no}</td>
                {else}<td>{$row.steelitem.status_title|undef}</td>
                {/if}
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}