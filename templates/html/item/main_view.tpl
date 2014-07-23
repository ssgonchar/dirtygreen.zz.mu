<table width="100%">
    <tr>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                {if !empty($form.parent_id) && isset($parent)}
                <tr>
                    <td>Alias of : </td>
                    <td><a href="/item/{$form.parent_id}">{$parent.doc_no}</a></td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title text-top">Steel Grade : </td>
                    <td class="text-top">{if isset($form.steelgrade) && isset($form.steelgrade.title)}{$form.steelgrade.title}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Thickness : </td>
                    <td class="text-top">{if !empty($form.thickness) && $form.thickness > 0}{$form.thickness} {$form.dimension_unit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Width : </td>
                    <td class="text-top">{if !empty($form.width) && $form.width > 0}{$form.width} {$form.dimension_unit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Length : </td>
                    <td class="text-top">{if !empty($form.length) && $form.length > 0}{$form.length} {$form.dimension_unit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Measured thickness : </td>
                    <td class="text-top">{if !empty($form.thickness_measured) && $form.thickness_measured > 0}{$form.thickness_measured} {$form.dimension_unit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Measured width : </td>
                    <td class="text-top">{if !empty($form.width_measured) && $form.width_measured > 0}{$form.width_measured} {$form.dimension_unit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Measured length : </td>
                    <td class="text-top">{if !empty($form.length_measured) && $form.length_measured > 0}{$form.length_measured} {$form.dimension_unit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Unitweight : </td>
                    <td class="text-top">{if !empty($form.unitweight) && $form.unitweight > 0}{$form.unitweight} {$form.weight_unit|wunit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Price : </td>
                    <td class="text-top">{if !empty($form.price) && $form.price > 0}{$form.price|number_format:2} {$form.currency|cursign} / {$form.weight_unit|wunit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Value : </td>
                    <td class="text-top">{if !empty($form.value) && $form.value > 0}{$form.value|number_format:2} {$form.currency|cursign}{else}{''|undef}{/if}</td>
                </tr>                
                <tr>
                    <td class="form-td-title">Location : </td>
                    <td>{if isset($form.stockholder)}{if isset($position)}<a href="/items/filter/stock:{$position.stock_id};location:{$form.stockholder.id};">{$form.stockholder.doc_no} ({$form.stockholder.city.title})</a>{else}{$form.stockholder.doc_no} ({$form.stockholder.city.title}){/if}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Owner : </td>
                    <td>{if isset($form.owner)}{$form.owner.title_trade}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Status : </td>
                    <td>
                        <span{if $form.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$form.status_id}"{/if}>{$form.status_title|undef}</span>
                    </td>
                </tr>
                {if isset($form.order_id) && $form.order_id > 0}
                <tr>
                    <td class="form-td-title">Order : </td>
                    <td><a href="/order/{$form.order_id}">{$form.order_id|order_doc_no}</a></td>
                </tr>
                {/if}
            </table>
            <div class="pad"></div>
        </td>
        <td width="20%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Mill : </td>
                    <td>{if empty($form.mill)}{''|undef}{else}{$form.mill}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Purchase Price {if !empty($form.purchase_currency)}{$form.purchase_currency|cursign}/Ton{/if} : </td>
                    <td>{if !empty($form.purchase_price) && $form.purchase_price > 0}{$form.purchase_price|string_format:'%.2f'}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Current Cost &euro;/Ton : </td>
                    <td>{if !empty($form.current_cost) && $form.current_cost > 0}{$form.current_cost|number_format:2} / {$form.weight_unit|wunit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">P/L &euro;/Ton : </td>
                    <td>{if !empty($form.pl) && $form.pl > 0}{$form.pl|number_format:2} / {$form.weight_unit|wunit}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Days On Stock : </td>
                    <td>{if empty($form.days_on_stock)}{''|undef}{else}{$form.days_on_stock}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Load Ready : </td>
                    <td>{if empty($form.load_ready)}{''|undef}{else}{$form.load_ready}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Internal Notes : </td>
                    <td class="text-top">{if !empty($form.internal_notes)}{$form.internal_notes|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
				<tr>
            </table>        
        </td>        
        <td width="20%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Heat / Lot : </td>
                    <td>{$form.properties.heat_lot|undef}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%C : </td>
                    <td>{$form.properties.c}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Si : </td>
                    <td>{$form.properties.si}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Mn : </td>
                    <td>{$form.properties.mn}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%P : </td>
                    <td>{$form.properties.p}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%S : </td>
                    <td>{$form.properties.s}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Cr : </td>
                    <td>{$form.properties.cr}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Ni : </td>
                    <td>{$form.properties.ni}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Cu : </td>
                    <td>{$form.properties.cu}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Al : </td>
                    <td>{$form.properties.al}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Mo : </td>
                    <td>{$form.properties.mo}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Nb : </td>
                    <td>{$form.properties.nb}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%V : </td>
                    <td>{$form.properties.v}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%N : </td>
                    <td>{$form.properties.n}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Ti : </td>
                    <td>{$form.properties.ti}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%Sn : </td>
                    <td>{$form.properties.sn}</td>
                </tr>
                <tr>
                    <td class="form-td-title">%B : </td>
                    <td>{$form.properties.b}</td>
                </tr>
                <tr>
                    <td class="form-td-title">CEQ : </td>
                    <td>{$form.properties.ceq}</td>
                </tr>
            </table>        
        </td>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title" style="width: 200px;">Tensile Sample Direction</td>
                    <td> : {if empty($form.properties.tensile_sample_direction)}{''|undef}{else}{$form.properties.tensile_sample_direction}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Tensile Strength N/mm2</td>
                    <td> : {if empty($form.properties.tensile_strength)}{''|undef}{else}{$form.properties.tensile_strength}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Tensile Yield Point N/mm2</td>
                    <td> : {if empty($form.properties.yeild_point)}{''|undef}{else}{$form.properties.yeild_point}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Tensile Elongation %</td>
                    <td> : {if !empty($form.properties.elongation) && $form.properties.elongation > 0}{$form.properties.elongation}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Z-test %</td>
                    <td> : {if !empty($form.properties.reduction_of_area) && $form.properties.reduction_of_area > 0}{$form.properties.reduction_of_area}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Impact Sample Direction</td>
                    <td> : {if empty($form.properties.sample_direction)}{''|undef}{else}{$form.properties.sample_direction}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Impact Strength J/cm2</td>
                    <td> : {if empty($form.properties.impact_strength)}{''|undef}{else}{$form.properties.impact_strength}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Impact Test Temp deg. C</td>
                    <td> : {if empty($form.properties.test_temp)}{''|undef}{else}{$form.properties.test_temp}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Hardness HD</td>
                    <td> : {if empty($form.properties.hardness)}{''|undef}{else}{$form.properties.hardness}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">UST</td>
                    <td> : {if empty($form.properties.ust)}{''|undef}{else}{$form.properties.ust}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Stress Relieving Temp deg. C</td>
                    <td> : {if empty($form.properties.stress_relieving_temp)}{''|undef}{else}{$form.properties.stress_relieving_temp}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Heating Rate Per Hour deg. C</td>
                    <td> : {if empty($form.properties.heating_rate_per_hour)}{''|undef}{else}{$form.properties.heating_rate_per_hour}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Holding Time Hours</td>
                    <td> : {if empty($form.properties.holding_time)}{''|undef}{else}{$form.properties.holding_time}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Cooling Down Rate Per Hour deg. C</td>
                    <td> : {if empty($form.properties.cooling_down_rate)}{''|undef}{else}{$form.properties.cooling_down_rate}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Normalizing Temp deg. C</td>
                    <td> : {if empty($form.properties.normalizing_temp)}{''|undef}{else}{$form.properties.normalizing_temp}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Condition</td>
                    <td> : {if empty($form.properties.condition)}{''|undef}{else}{if $form.properties.condition == 'ar'}As Rolled
                        {elseif $form.properties.condition == 'n'}Normalized
                        {elseif $form.properties.condition == 'nr'}Normalizing Rolling{/if}{/if}
                    </td>
                </tr>            
            </table>
        </td>
    </tr>
</table>
<div class="pad"></div>

<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <h3>Related Documents</h3>
            {if empty($related_docs)}
                {''|undef}
            {else}
            <table width="80%">
                {foreach from=$related_docs item=doc}
                <tr style="height: 25px; border-bottom: dotted 1px #777; cursor: pointer;" onclick="location.href='/{$doc.object_alias}/{$doc.object_id}';">
                    <td>
                        <a href="/{$doc.object_alias}/{$doc.object_id}">{$doc.object.doc_no}</a>
                    </td>
                    <td width="25%" class="text-right" style="font-size: 10px; color: #777;">
                        {$doc.object_title}
                    </td>
                    <td width="25%" class="text-right" style="font-size: 10px; color: #777;">
                        {if isset($doc.company)}{$doc.company.doc_no}{/if}
                    </td>
                    <td width="10%" class="text-right" style="font-size: 10px; color: #777;">
                    {if $doc.created_at > 0}
                        {$doc.created_at|date_format:'d/m/Y'}
                        {if isset($doc.author) && isset($doc.author.full_login)}<br>{$doc.author.full_login}{/if}
                    {else}
                        {''|undef}
                    {/if}
                    </td>
                </tr>
                {/foreach}
            </table>            
            {/if}        
        </td>
        <td width="33%" class="text-top">
            {if isset($history) && !empty($history)}         
                <h3>Changes History</h3>
                <table width="100%">
                    {$index = 1}
                    {foreach from=$history item=row}
                        <tr  style="height: 25px; border-bottom: dotted 1px #777; cursor: pointer;" onclick="show_item_history_context({$row.revision_id});">
                        {*<td width="10%">№</td>*}
                        <td width="60%">
                            {if isset($row.action_status) && !empty($row.action_status)}
                                {$row.action_status|escape:'html'}
                            {elseif isset($row.edit_status)}
                                {foreach from=$row.edit_status item=value name=action}
                                    {$value}{if !$smarty.foreach.action.last},{/if}
                                {/foreach}
                            {else}
                                <i style="font-size: 10px; color: #777;">undefined</i>
                            {/if}
                        </td>
                        <td width="40%" class="text-right" style="font-size: 10px; color: #777;">{$row.modified_at|date_format:'d/m/Y'}, {$row.user.login|escape:'html'}</td>
                        <td>
                            <div id="hiden-history-{$row.revision_id}" style="display:none;">
                                <h2>History Revision</h2>
                                <table style="width:100%">
                                    <tr>
                                        <td class="text-top" width="30%">
                                            <table class="form" width="100%">
                                                <tr>
                                                    <td class="form-td-title text-top">Plate ID : </td>
                                                    <td {if isset($history[$index]) && $row.guid != $history[$index].guid}class="changed-param"{/if}>{if !empty($row.guid)}{$row.guid}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Steel Grade : </td>
                                                    <td {if isset($history[$index]) && $row.steelgrade_id != $history[$index].steelgrade_id}class="changed-param"{/if}>{if isset($row.steelgrade) && isset($row.steelgrade.title)}{$row.steelgrade.title}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Thickness : </td>
                                                    <td {if isset($history[$index]) && $row.thickness != $history[$index].thickness}class="changed-param"{/if}>{if !empty($row.thickness) && $row.thickness > 0}{$row.thickness} {$row.dimension_unit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Width : </td>
                                                    <td {if isset($history[$index]) && $row.width != $history[$index].width}class="changed-param"{/if}>{if !empty($row.width) && $row.width > 0}{$row.width} {$row.dimension_unit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Length : </td>
                                                    <td {if isset($history[$index]) && $row.length != $history[$index].length}class="changed-param"{/if}>{if !empty($row.length) && $row.length > 0}{$row.length} {$row.dimension_unit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Measured thickness : </td>
                                                    <td {if isset($history[$index]) && $row.thickness_measured != $history[$index].thickness_measured}class="changed-param"{/if}>{if !empty($row.thickness_measured) && $row.thickness_measured > 0}{$row.thickness_measured} {$row.dimension_unit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Measured width : </td>
                                                    <td {if isset($history[$index]) && $row.width_measured != $history[$index].width_measured}class="changed-param"{/if}>{if !empty($row.width_measured) && $row.width_measured > 0}{$row.width_measured} {$row.dimension_unit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Measured length : </td>
                                                    <td {if isset($history[$index]) && $row.length_measured != $history[$index].length_measured}class="changed-param"{/if}>{if !empty($row.length_measured) && $row.length_measured > 0}{$row.length_measured} {$row.dimension_unit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Unitweight : </td>
                                                    <td {if isset($history[$index]) && $row.unitweight != $history[$index].unitweight}class="changed-param"{/if}>{if !empty($row.unitweight) && $row.unitweight > 0}{$row.unitweight} {$row.weight_unit|wunit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Price : </td>
                                                    <td {if isset($history[$index]) && $row.price != $history[$index].price}class="changed-param"{/if}>{if !empty($row.price) && $row.price > 0}{$row.price|number_format:2} {$row.currency|cursign} / {$row.weight_unit|wunit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Value : </td>
                                                    <td {if isset($history[$index]) && $row.value != $history[$index].value}class="changed-param"{/if}>{if !empty($row.value) && $row.value > 0}{$row.value|number_format:2} {$row.currency|cursign}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Location : </td>
                                                    <td {if isset($history[$index]) && $row.location_id != $history[$index].location_id}class="changed-param"{/if}>{if !isset($row.location.title) || empty($row.location.title)}{''|undef}{else}{$row.location.title|escape:'html'}{/if}</td>
                                                </tr>    
                                                <tr>
                                                    <td class="form-td-title text-top">Owner : </td>
                                                    <td {if isset($history[$index]) && $row.owner_id != $history[$index].owner_id}class="changed-param"{/if}>{if !empty($row.owner)}{$row.owner.title|escape:'html'}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Status : </td>
                                                    <td {if isset($history[$index]) && $row.status_id != $history[$index].status_id}class="changed-param"{/if}>
                                                        {if !empty($row.status_id)}
                                                            {if $row.status_id == $smarty.const.ITEM_STATUS_DELIVERED}
                                                                Delivered
                                                            {elseif  $row.status_id == $smarty.const.ITEM_STATUS_INVOICED}
                                                                Invoiced
                                                            {elseif $row.status_id == $smarty.const.ITEM_STATUS_ORDERED}
                                                                Ordered
                                                            {elseif $row.status_id == $smarty.const.ITEM_STATUS_PRODUCTION}
                                                                In Production
                                                            {elseif $row.status_id == $smarty.const.ITEM_STATUS_RELEASED}
                                                                Released
                                                            {elseif $row.status_id == $smarty.const.ITEM_STATUS_STOCK}
                                                                On Stock
                                                            {elseif $row.status_id == $smarty.const.ITEM_STATUS_TRANSFER}
                                                                Transfer To Stock
                                                            {/if}    
                                                        {else}{''|undef}{/if}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title text-top">Order : </td>
                                                    <td {if isset($history[$index]) && $row.order_id != $history[$index].order_id}class="changed-param"{/if}>
                                                        {if isset($row.order_id) && $row.order_id > 0}
                                                            <a href="/order/{$row.order_id}">{$row.order_id|order_doc_no}</a>
                                                        {else}{''|undef}{/if}
                                                    </td>                                    
                                                </tr>                                                
                                                <tr>
                                                    <td class="form-td-title text-top">Internal Notes : </td>
                                                    <td {if isset($history[$index]) && $row.internal_notes != $history[$index].internal_notes}class="changed-param"{/if}>{if empty($row.internal_notes)}{''|undef}{else}{$row.internal_notes|escape:'html'}{/if}</td>
                                                </tr>                                                
                                                <tr>
                                                    <td class="form-td-title">Mill : </td>
                                                    <td {if isset($history[$index]) && $row.mill != $history[$index].mill}class="changed-param"{/if}>{if empty($row.mill)}{''|undef}{else}{$row.mill}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Purchase Price {if !empty($row.purchase_currency)}{$row.purchase_currency|cursign}/Ton{/if} : </td>
                                                    <td {if isset($history[$index]) && $row.purchase_currency != $history[$index].purchase_currency}class="changed-param"{/if}>{if $row.purchase_price > 0}{$row.purchase_price|number_format:2}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Current Cost &euro;/Ton : </td>
                                                    <td {if isset($history[$index]) && $row.current_cost != $history[$index].current_cost}class="changed-param"{/if}>{if !empty($row.current_cost) && $row.current_cost > 0}{$row.current_cost|number_format:2} / {$row.weight_unit|wunit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">P/L &euro;/Ton : </td>
                                                    <td {if isset($history[$index]) && $row.pl != $history[$index].pl}class="changed-param"{/if}>{if !empty($row.pl) && $row.pl > 0}{$row.pl|number_format:2} / {$row.weight_unit|wunit}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Days On Stock : </td>
                                                    <td {if isset($history[$index]) && $row.days_on_stock != $history[$index].days_on_stock}class="changed-param"{/if}>{if empty($row.days_on_stock)}{''|undef}{else}{$row.days_on_stock}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Load Ready : </td>
                                                    <td {if isset($history[$index]) && $row.load_ready != $history[$index].load_ready}class="changed-param"{/if}>{if empty($row.load_ready)}{''|undef}{else}{$row.load_ready}{/if}</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text-top" width="30%">
                                            <table class="form" width="100%">
                                                <tr>
                                                    <td class="form-td-title">Heat / Lot : </td>
                                                    <td {if isset($history[$index]) && $row.heat_lot != $history[$index].heat_lot}class="changed-param"{/if}>{$row.heat_lot|undef}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%C : </td>
                                                    <td {if isset($history[$index]) && $row.c != $history[$index].c}class="changed-param"{/if}>{$row.c}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Si : </td>
                                                    <td {if isset($history[$index]) && $row.si != $history[$index].si}class="changed-param"{/if}>{$row.si}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Mn : </td>
                                                    <td {if isset($history[$index]) && $row.mn != $history[$index].mn}class="changed-param"{/if}>{$row.mn}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%P : </td>
                                                    <td {if isset($history[$index]) && $row.p != $history[$index].p}class="changed-param"{/if}>{$row.p}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%S : </td>
                                                    <td {if isset($history[$index]) && $row.s != $history[$index].s}class="changed-param"{/if}>{$row.s}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Cr : </td>
                                                    <td {if isset($history[$index]) && $row.cr != $history[$index].cr}class="changed-param"{/if}>{$row.cr}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Ni : </td>
                                                    <td {if isset($history[$index]) && $row.ni != $history[$index].ni}class="changed-param"{/if}>{$row.ni}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Cu : </td>
                                                    <td {if isset($history[$index]) && $row.cu != $history[$index].cu}class="changed-param"{/if}>{$row.cu}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Al : </td>
                                                    <td {if isset($history[$index]) && $row.al != $history[$index].al}class="changed-param"{/if}>{$row.al}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Mo : </td>
                                                    <td {if isset($history[$index]) && $row.mo != $history[$index].mo}class="changed-param"{/if}>{$row.mo}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Nb : </td>
                                                    <td {if isset($history[$index]) && $row.nb != $history[$index].nb}class="changed-param"{/if}>{$row.nb}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%V : </td>
                                                    <td {if isset($history[$index]) && $row.v != $history[$index].v}class="changed-param"{/if}>{$row.v}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%N : </td>
                                                    <td {if isset($history[$index]) && $row.n != $history[$index].n}class="changed-param"{/if}>{$row.n}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Ti : </td>
                                                    <td {if isset($history[$index]) && $row.ti != $history[$index].ti}class="changed-param"{/if}>{$row.ti}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%Sn : </td>
                                                    <td {if isset($history[$index]) && $row.sn != $history[$index].sn}class="changed-param"{/if}>{$row.sn}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">%B : </td>
                                                    <td {if isset($history[$index]) && $row.b != $history[$index].b}class="changed-param"{/if}>{$row.b}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">CEQ : </td>
                                                    <td {if isset($history[$index]) && $row.ceq != $history[$index].ceq}class="changed-param"{/if}>{$row.ceq}</td>
                                                </tr>
                                            </table>  
                                        </td>
                                        <td class="text-top" width="40%">
                                            <table class="form" width="100%">
                                                <tr>
                                                    <td class="form-td-title" style="width: 200px;">Tensile Sample Direction : </td>
                                                    <td {if isset($history[$index]) && $row.tensile_sample_direction != $history[$index].tensile_sample_direction}class="changed-param"{/if}>{if empty($row.tensile_sample_direction)}{''|undef}{else}{$row.tensile_sample_direction}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Tensile Strength N/mm2 : </td>
                                                    <td {if isset($history[$index]) && $row.tensile_strength != $history[$index].tensile_strength}class="changed-param"{/if}>{if empty($row.tensile_strength)}{''|undef}{else}{$row.tensile_strength}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Tensile Yield Point N/mm2 : </td>
                                                    <td {if isset($history[$index]) && $row.yeild_point != $history[$index].yeild_point}class="changed-param"{/if}>{if empty($row.yeild_point)}{''|undef}{else}{$row.yeild_point}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Tensile Elongation % : </td>
                                                    <td {if isset($history[$index]) && $row.elongation != $history[$index].elongation}class="changed-param"{/if}>{if !empty($row.elongation) && $row.elongation > 0}{$row.elongation}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Z-test % : </td>
                                                    <td {if isset($history[$index]) && $row.reduction_of_area != $history[$index].reduction_of_area}class="changed-param"{/if}>{if !empty($row.reduction_of_area) && $row.reduction_of_area > 0}{$row.reduction_of_area}{else}{''|undef}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Impact Sample Direction : </td>
                                                    <td {if isset($history[$index]) && $row.sample_direction != $history[$index].sample_direction}class="changed-param"{/if}>{if empty($row.sample_direction)}{''|undef}{else}{$row.sample_direction}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Impact Strength J/cm2 : </td>
                                                    <td {if isset($history[$index]) && $row.impact_strength != $history[$index].impact_strength}class="changed-param"{/if}>{if empty($row.impact_strength)}{''|undef}{else}{$row.impact_strength}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Impact Test Temp deg. C : </td>
                                                    <td {if isset($history[$index]) && $row.test_temp != $history[$index].test_temp}class="changed-param"{/if}>{if empty($row.test_temp)}{''|undef}{else}{$row.test_temp}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Hardness HD : </td>
                                                    <td {if isset($history[$index]) && $row.hardness != $history[$index].hardness}class="changed-param"{/if}>{if empty($row.hardness)}{''|undef}{else}{$row.hardness}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">UST : </td>
                                                    <td {if isset($history[$index]) && $row.ust != $history[$index].ust}class="changed-param"{/if}>{if empty($row.ust)}{''|undef}{else}{$row.ust}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Stress Relieving Temp deg. C :</td>
                                                    <td {if isset($history[$index]) && $row.stress_relieving_temp != $history[$index].stress_relieving_temp}class="changed-param"{/if}>{if empty($row.stress_relieving_temp)}{''|undef}{else}{$row.stress_relieving_temp}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Heating Rate Per Hour deg. C : </td>
                                                    <td {if isset($history[$index]) && $row.heating_rate_per_hour != $history[$index].heating_rate_per_hour}class="changed-param"{/if}>{if empty($row.heating_rate_per_hour)}{''|undef}{else}{$row.heating_rate_per_hour}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Holding Time Hours : </td>
                                                    <td {if isset($history[$index]) && $row.holding_time != $history[$index].holding_time}class="changed-param"{/if}>{if empty($row.holding_time)}{''|undef}{else}{$row.holding_time}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Cooling Down Rate Per Hour deg. C : </td>
                                                    <td {if isset($history[$index]) && $row.cooling_down_rate != $history[$index].cooling_down_rate}class="changed-param"{/if}>{if empty($row.cooling_down_rate)}{''|undef}{else}{$row.cooling_down_rate}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Normalizing Temp deg. C : </td>
                                                    <td {if isset($history[$index]) && $row.normalizing_temp != $history[$index].normalizing_temp}class="changed-param"{/if}>{if empty($row.normalizing_temp)}{''|undef}{else}{$row.normalizing_temp}{/if}</td>
                                                </tr>
                                                <tr>
                                                    <td class="form-td-title">Condition : </td>
                                                    <td {if isset($history[$index]) && $row.condition != $history[$index].condition}class="changed-param"{/if}>{if empty($row.condition)}{''|undef}{else}{if $row.condition == 'ar'}As Rolled
                                                        {elseif $row.condition == 'n'}Normalized
                                                        {elseif $row.condition == 'nr'}Normalizing Rolling{/if}{/if}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>    
                                    </tr>    
                                </table>
                            </div>
                        </td>    
                    </tr>
                        {$index = $index + 1}
                    {/foreach}
                </table>            
            {/if}                
        </td>
        <td width="33%" class="text-top">&nbsp;
        </td>        
    </tr>
</table>
<div class="pad"></div>

{include file='templates/controls/object_shared_files.tpl' object_alias='item' object_id=$form.id}