<table class="form" style="width: 100%;">
    <tr>
        <td class="form-td-title-b" style="width: 150px;">Language : </td>
        <td>
            {if $form.lang == $smarty.const.STOCKOFFER_LANG_ALIAS_EN}English
            {elseif $form.lang == $smarty.const.STOCKOFFER_LANG_ALIAS_IT}Italian
            {elseif $form.lang == $smarty.const.STOCKOFFER_LANG_ALIAS_RU}Russian
            {else}Chinese{/if}
        </td>
    </tr>
    {if isset($form.header_attachment)}
    <tr>
        <td class="form-td-title-b text-top">Header Image : </td>
        <td class="text-top">{picture size="m" source=$form.header_attachment}</td>
    </tr>
    {/if}    
    {if !empty($form.delivery_point)}
    <tr>
        <td class="form-td-title-b">Delivery Point : </td>
        <td>
            {$form.delivery_point|escape:'html'}
        </td>
    </tr>
    {/if}
    {if !empty($form.delivery_cost)}
    <tr>
        <td class="form-td-title-b">Delivery Cost : </td>
        <td>
             {$form.delivery_cost|escape:'html'}
        </td>
    </tr>
    {/if}
    {if !empty($form.delivery_time)}
    <tr>
        <td class="form-td-title-b">Delivery Time : </td>
        <td>
             {$form.delivery_time|escape:'html'}
        </td>
    </tr>
    {/if}
    {if !empty($form.payment_terms)}
    <tr>
        <td class="form-td-title-b">Payment Terms : </td>
        <td>
            {$form.payment_terms|escape:'html'}
        </td>
    </tr>
    {/if}
</table>
<div class="pad"></div>

<h3 class="packing-list-title" style="margin-bottom: 10px;">Positions</h3>
<span class="packing-list-is-empty" style="display: {if empty($positions)}inline{else}none{/if};">There are no positions</span>
{if !empty($positions)}
<table class="list packing-list" style="width: 100%">
    <tr class="top-table">
        <th>Id</th>
        <th>Steel Grade</th>
        <th>Thickness{if $position_dimension_unit_count == 1}<br>{$position_dimension_unit}{/if}</th>
        <th>Width{if $position_dimension_unit_count == 1}<br>{$position_dimension_unit}{/if}</th>
        <th>Length{if $position_dimension_unit_count == 1}<br>{$position_dimension_unit}{/if}</th>
        <th>Unit Weight{if $position_weight_unit_count == 1}<br>{$position_weight_unit|wunit}{/if}</th>
        <th>Qtty</th>
        <th>Weight{if $position_weight_unit_count == 1}<br>{$position_weight_unit|wunit}{/if}</th>
        <th>Price{if $position_price_unit_count == 1 && $position_currency_count == 1}<br>{$position_currency|cursign}/{$position_price_unit|wunit}{/if}</th>
        <th>Value{if $position_currency_count == 1}<br>{$position_currency|cursign}{/if}</th>
        <th>Notes</th>
        <th>Internal Notes</th>
        <th>Delivery Time</th>
        <th>Location</th>
        {*<th>I wish</th>*}
    </tr>
    {foreach $positions as $position}
    {$position=$position.steelposition}
    <tr class="position-container" data-steelposition_id="{$position.id}" data-doc_id="{$form.id}" data-bgcolor="{if !empty($position.bgcolor)}{$position.bgcolor}{/if}">
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.id}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.steelgrade.title|escape:'html'|undef}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.thickness}{if $position_dimension_unit_count > 1} {$position.dimension_unit}{/if}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.width}{if $position_dimension_unit_count > 1} {$position.dimension_unit}{/if}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.length}{if $position_dimension_unit_count > 1} {$position.dimension_unit}{/if}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.unitweight}{if $position_weight_unit_count > 1} {$position.weight_unit}{/if}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.qtty}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.weight}{if $position_weight_unit_count > 1} {$position.weight_unit}{/if}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>
        {if $position_price_unit_count > 1 && $position_currency_count > 1}
            {$position.currency|cursign} {$position.price|escape:'html'|string_format:'%.2f'} / {$position.price_unit|wunit}
        {else}
            {$position.price|escape:'html'|string_format:'%.2f'}
        {/if}
        </td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.value}{if $position_currency_count > 1} {$position.currency|cursign}{/if}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.notes}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.internal_notes}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{$position.deliverytime.title}</td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}>{if isset($position.quick)}{$position.quick.locations}{/if}</td>
        {*<td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}></td>*}
    </tr>
    {/foreach}
</table>
{/if}
<div class="pad"></div>

<table class="form" style="width: 100%;">
    {if !empty($form.quality_certificate)}
    <tr>
        <td class="form-td-title-b" style="width: 150px;">Quality Certificate : </td>
        <td>
            {$form.quality_certificate|escape:'html'}
        </td>
    </tr>
    {/if}
    {if !empty($form.validity)}
    <tr>
        <td class="form-td-title-b">Validity : </td>
        <td>{$form.validity|escape:'html'}</td>
    </tr>
    {/if}
    {if !empty($form.description)}
    <tr>
        <td class="form-td-title-b text-top">Notes : </td>
        <td class="text-top">{$form.description|escape:'html'|nl2br}</td>
    </tr>
    {/if}
    {if isset($form.banner1_attachment)}
    <tr>
        <td class="form-td-title-b text-top">Banner1 : </td>
        <td class="text-top">{picture size="m" source=$form.banner1_attachment}</td>
    </tr>
    {/if}    
    {if isset($form.banner2_attachment)}
    <tr>
        <td class="form-td-title-b text-top">Banner2 : </td>
        <td class="text-top">{picture size="m" source=$form.banner2_attachment}</td>
    </tr>
    {/if}
    {if isset($form.footer_attachment)}
    <tr>
        <td class="form-td-title-b text-top">Footer Image : </td>
        <td class="text-top">{picture size="m" source=$form.footer_attachment}</td>
    </tr>
    {/if}
</table>
<div class="pad"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<table class="form" style="width: 100%;">
    <tr>
        <td class="form-td-title-b" style="width: 150px;">PDF : </td>
        <td>
            {if isset($form.pdf_attachment)}
            <a class="pdf" target="_blank" href="/file/{$form.pdf_attachment.secret_name}/{$form.pdf_attachment.original_name}">{$form.pdf_attachment.original_name}</a>
            {else}
            {''|undef}
            {/if}
        </td>
    </tr>
</table>