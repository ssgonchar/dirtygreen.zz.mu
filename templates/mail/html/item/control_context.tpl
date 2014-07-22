<h2 style="margin-top: 0; padding-top: 0;">{if empty($item.guid)}Plate # {$item.id}{else}{$item.guid}{/if}</h2>
{if isset($item.steelgrade)}{$item.steelgrade.title}{/if} {if !empty($item.thickness)}{$item.thickness}{/if}{if !empty($item.width)} x {$item.width}{/if}{if !empty($item.length)} x {$item.length}{/if}{if !empty($item.thickness) || !empty($item.width) || !empty($item.length)} {$item.dimension_unit}{/if}{if !empty($item.unitweight)} = {$item.unitweight}{if isset($item.weight_unit)} {$item.weight_unit}{/if}{/if}{if $item.price > 0}, {$item.currency|cursign} {$item.price|string_format:'%.2f'}/{$item.price_unit|wunit}{/if}
<div class="pad"></div>

<table width="100%">
    <tr>
        <td width="80%" class="text-top">
            <table width="100%">
                <tr id="item-context-props-11">
                    <td colspan="2" style="padding-bottom: 15px;">
                        <b style="margin-right: 20px;">Chemical Analysis & Mechanical Properties</b>
                        <a style="margin-right: 20px;" href="javascript: void(0);" onclick="item_context_togle(2);">Status & Extra Params</a>
                        {if !empty($related_attachments)}<a href="javascript: void(0);" onclick="item_context_togle(3);">Attachments</a>{/if}
                    </td>
                </tr>
                <tr id="item-context-props-21" style="display: none;">
                    <td colspan="2" style="padding-bottom: 15px;">
                        <a href="javascript: void(0);" onclick="item_context_togle(1);" style="margin-right: 20px;">Chemical Analysis & Mechanical Properties</a>
                        <b style="margin-right: 20px;">Status & Extra Params</b>
                        {if !empty($related_attachments)}<a href="javascript: void(0);" onclick="item_context_togle(3);">Attachments</a>{/if}
                    </td>
                </tr>
                <tr id="item-context-props-31" style="display: none;">
                    <td colspan="2" style="padding-bottom: 15px;">
                        <a href="javascript: void(0);" onclick="item_context_togle(1);" style="margin-right: 20px;">Chemical Analysis & Mechanical Properties</a>
                        <a href="javascript: void(0);" onclick="item_context_togle(2);" style="margin-right: 20px;">Status & Extra Params</a>
                        <b>Attachments</b>
                    </td>
                </tr>
                <tr id="item-context-props-12">
                    <td width="30%" class="text-top">
                        <table width="100%" class="item-context">
                            <tr>
                                <td class="text-right" width="60px">Heat / Lot</td>
                                <td>: {$item.properties.heat_lot|undef}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%C</td>
                                <td>: {$item.properties.c}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Si</td>
                                <td>: {$item.properties.si}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Mn</td>
                                <td>: {$item.properties.mn}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%P</td>
                                <td>: {$item.properties.p}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%S</td>
                                <td>: {$item.properties.s}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Cr</td>
                                <td>: {$item.properties.cr}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Ni</td>
                                <td>: {$item.properties.ni}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Cu</td>
                                <td>: {$item.properties.cu}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Al</td>
                                <td>: {$item.properties.al}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Mo</td>
                                <td>: {$item.properties.mo}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Nb</td>
                                <td>: {$item.properties.nb}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%V</td>
                                <td>: {$item.properties.v}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%N</td>
                                <td>: {$item.properties.n}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Ti</td>
                                <td>: {$item.properties.ti}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%Sn</td>
                                <td>: {$item.properties.sn}</td>
                            </tr>
                            <tr>
                                <td class="text-right">%B</td>
                                <td>: {$item.properties.b}</td>
                            </tr>
                            <tr>
                                <td class="text-right">CEQ</td>
                                <td>: {$item.properties.ceq}</td>
                            </tr>
                        </table>                    
                    </td>
                    <td class="text-top">
                        <table width="100%" class="item-context">
                            <tr>
                                <td class="text-right" width="250px">Tensile Sample Direction</td>
                                <td>: {if empty($item.properties.tensile_sample_direction)}{''|undef}{else}{$item.properties.tensile_sample_direction}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Tensile Strength N/mm2</td>
                                <td>: {if empty($item.properties.tensile_strength)}{''|undef}{else}{$item.properties.tensile_strength}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Tensile Yield Point N/mm2</td>
                                <td>: {if empty($item.properties.yeild_point)}{''|undef}{else}{$item.properties.yeild_point}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Tensile Elongation %</td>
                                <td>: {if empty($item.properties.elongation)}{''|undef}{else}{$item.properties.elongation}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Z-test %</td>
                                <td>: {if empty($item.properties.reduction_of_area)}{''|undef}{else}{$item.properties.reduction_of_area}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Impact Sample Direction</td>
                                <td>: {if empty($item.properties.sample_direction)}{''|undef}{else}{$item.properties.sample_direction}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Impact Strength J/cm2</td>
                                <td>: {if empty($item.properties.impact_strength)}{''|undef}{else}{$item.properties.impact_strength}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Impact Test Temp deg. C</td>
                                <td>: {if empty($item.properties.test_temp)}{''|undef}{else}{$item.properties.test_temp}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Hardness HD</td>
                                <td>: {if empty($item.properties.hardness)}{''|undef}{else}{$item.properties.hardness}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">UST</td>
                                <td>: {if empty($item.properties.ust)}{''|undef}{else}{$item.properties.ust}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Stress Relieving Temp deg. C</td>
                                <td>: {if empty($item.properties.stress_relieving_temp)}{''|undef}{else}{$item.properties.stress_relieving_temp}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Heating Rate Per Hour deg. C</td>
                                <td>: {if empty($item.properties.heating_rate_per_hour)}{''|undef}{else}{$item.properties.heating_rate_per_hour}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Holding Time Hours</td>
                                <td>: {if empty($item.properties.holding_time)}{''|undef}{else}{$item.properties.holding_time}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Cooling Down Rate Per Hour deg. C</td>
                                <td>: {if empty($item.properties.cooling_down_rate)}{''|undef}{else}{$item.properties.cooling_down_rate}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Normalizing Temp deg. C</td>
                                <td>: {if empty($item.properties.normalizing_temp)}{''|undef}{else}{$item.properties.normalizing_temp}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Condition</td>
                                <td>: {if empty($item.properties.condition)}{''|undef}{else}{if $item.properties.condition == 'ar'}As Rolled
                                    {elseif $item.properties.condition == 'n'}Normalized
                                    {elseif $item.properties.condition == 'nr'}Normalizing Rolling{/if}{/if}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right">CE Mark</td>
                                <td>: {if isset($item.is_ce_mark) && !empty($item.is_ce_mark)}<img src="/img/cemark16.png" alt="CE Mark" title="CE Mark">{else}<i style="color: #999;">no</i>{/if}</td>
                            </tr>                            
                        </table>                    
                    </td>
                </tr>
                <tr id="item-context-props-22" style="display: none;">
                    <td>
                        <table width="100%" class="item-context">
                            <tr>
                                <td class="text-right" width="100px">Mill</td>
                                <td>: {if empty($item.mill)}{''|undef}{else}{$item.mill}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Purchase Price {if !empty($item.purchase_currency)}{$item.purchase_currency|cursign}/Ton{/if}</td>
                                <td>: {if $item.purchase_price > 0}{$item.purchase_price|string_format:'%.2f'}{else}{''|undef}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Current Cost &euro;/Ton</td>
                                <td>: {if empty($item.current_cost)}{''|undef}{else}{$item.current_cost}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">P/L &euro;/Ton</td>
                                <td>: {if empty($item.pl)}{''|undef}{else}{$item.pl}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Days On Stock</td>
                                <td>: {if empty($item.days_on_stock)}{''|undef}{else}{$item.days_on_stock}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right">Load Ready</td>
                                <td>: {if empty($item.load_ready)}{''|undef}{else}{$item.load_ready}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right text-top">Internal Notes</td>
                                <td>: {if empty($item.internal_notes)}{''|undef}{else}{$item.internal_notes|escape:'html'}{/if}</td>
                            </tr>
                        </table>                    
                        <div class="pad-10"></div>
                        
                        <table width="100%" class="item-context">
                            <tr>
                                <td class="text-right" width="100px">Supplier Invoice</td>
                                <td>: {if isset($item.supplier_invoice)}<a href="/supplierinvoice/{$item.supplier_invoice_id}">{$item.supplier_invoice.doc_no_full}</a>{else}{''|undef}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right" width="100px">Incoming DDT</td>
                                <td>: {if $item.in_ddt_id > 0 && $item.in_ddt.company_id == $item.stockholder_id}<a href="/inddt/{$item.in_ddt_id}">{$item.in_ddt_number} dd {$item.in_ddt_date|date_format:'d/m/Y'}</a>{else}{''|undef}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right" width="100px">DDT</td>
                                <td>: {if !empty($item.ddt_number) && $item.ddt_date > 0}{$item.ddt_number|escape:'html'} dd {$item.ddt_date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                            </tr>
                            <tr>
                                <td class="text-right" width="100px">Invoice</td>
                                <td>: {if isset($item.invoice)}<a href="/invoice/{$item.invoice_id}">{$item.invoice.doc_no_full}</a>{else}{''|undef}{/if}</td>
                            </tr>
                        </table>                    
                    </td>
                </tr>
                <tr id="item-context-props-32" style="display: none;">
                    <td width="40%" class="text-top">
                    {if !empty($related_attachments)}
                    {foreach $related_attachments as $row}
                        <div id="attachment-{$row.attachment.id}" style="border: solid 1px #f6f6f6; width: 190px; padding: 5px; margin: 2px; display: inline-block;">
                            {if $row.attachment.type == 'image'}{picture type="{$row.attachment.object_alias}" size="x" source=$row.attachment pretty_id="{$row.attachment.object_alias}{$row.attachment.object_id}"}
                            {else}<img src="/img/icons/filetype/{$row.attachment.ext|lower}.png">
                            {/if}
                            <div style="margin: 0; line-height: 16px; width: 145px; overflow: hidden; display: inline-block;">
                                {att source=$row.attachment}<br />{$row.attachment.size|human_filesize}
                            </div>
                        </div>
                    {/foreach}
                    {/if}
                    </td>
                </tr>
            </table>
            <div class="pad1"></div>
            <hr style="width: 100%; color: #dedede;" size="1"/>
            {if isset($attachments) && !empty($attachments)}
            {foreach from=$attachments item=row}
            {picture type="{$row.attachment.object_alias}" size="x" source=$row.attachment pretty_id="{$row.attachment.object_alias}{$row.attachment.object_id}" style="float: left; margin-right: 10px;"}
            {/foreach}
            {else}
            <i style="color: #777;">no pictures</i>
            {/if}            
        </td>
        <td width="20%" style="padding-left: 10px;" class="text-top">
            <table width="100%" class="item-context">
                <tr><td style="font-weight: bold;">Actions : </td></tr>
                <tr>
                    <td>
                        <ul>
                            {if $item.status_id < $smarty.const.ITEM_STATUS_RELEASED}
                                <li><a class="edit" href="/item/edit/{$item.id}">edit item</a></li>
                                {if empty($item.parent_id)}
                                    <li style="padding-top: 7px;"><a class="twin" href="/item/createalias/{$item.id}">create alias</a></li>
                                    {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR && empty($item.order_id)}
                                        <li style="padding-top: 7px;"><a class="cut" href="/item/{$item.id}/cut">cut item</a></li>
                                    {/if}
                                {/if}
                                {if $item.in_ddt_id == 0 && empty($item.guid)}
                                    <li style="padding-top: 7px;"><a class="move" href="/item/{$item.id}/move">move item</a></li>
                                    {if $item.supplier_invoice_id == 0}
                                        <li style="padding-top: 7px;"><a class="delete" onclick="item_remove({$item.id}, {$item.steelposition_id})" href="javascript: void(0);">delete item</a></li>
                                    {/if}
                                {/if}
                            {/if}
                            <li style="padding-top: 7px;"><a class="history" href="/item/{$item.id}">details</a></li>
                        </ul>                    
                    </td>
                </tr>
                
                {if !empty($item.parent_id)}
                <tr><td style="font-weight: bold; padding-top: 20px;"><a href="/item/edit/{$item.parent_id}">{$item.guid}</a></td></tr>
                {/if}                    
                
                <tr><td style="font-weight: bold; padding-top: 20px;">Location : </td></tr>
                <tr><td>{if isset($item.stockholder)}{$item.stockholder.doc_no} ({$item.stockholder.city.title}){else}{''|undef}{/if}</td></tr>

                <tr><td style="font-weight: bold; padding-top: 10px;">Owner : </td></tr>
                <tr><td>{if isset($item.owner)}{$item.owner.title_trade}{else}{''|undef}{/if}</td></tr>

                <tr><td style="font-weight: bold; padding-top: 10px;">Status : </td></tr>
                <tr><td>{$item.status_title|undef}</td></tr>
                
                <tr><td style="font-weight: bold; padding-top: 10px;">Created : </td></tr>
                <tr><td>{$item.created_at|date_format:'d/m/Y'}{if isset($item.author)}, {$item.author.login}{/if}</td></tr>

                <tr><td style="font-weight: bold; padding-top: 10px;">Modified : </td></tr>
                <tr><td>{$item.modified_at|date_format:'d/m/Y'}{if isset($item.modifier)}, {$item.modifier.login}{/if}</td></tr>                    
            </table>
        </td>
    </tr>
</table>
