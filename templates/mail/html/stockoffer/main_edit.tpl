<table class="form" style="width: 50%;">

    <tr>
        <td class="form-td-title" style="width: 150px;">Title : </td>
        <td>
            <input class="max" type="text" name="form[title]"{if !empty($form.title)} value="{$form.title|escape:'html'}"{/if}/>
        </td>
    </tr>
    <tr>
        <td class="form-td-title">Language : </td>
        <td>
            <select class="narrow" name="form[lang]">
                <option value=""{if empty($form.lang)} selected="selected"{/if}>--</option>
                <option value="{$smarty.const.STOCKOFFER_LANG_ALIAS_EN}"{if $form.lang == $smarty.const.STOCKOFFER_LANG_ALIAS_EN} selected="selected"{/if}>English</option>
                <option value="{$smarty.const.STOCKOFFER_LANG_ALIAS_IT}"{if $form.lang == $smarty.const.STOCKOFFER_LANG_ALIAS_IT} selected="selected"{/if}>Italian</option>
                <option value="{$smarty.const.STOCKOFFER_LANG_ALIAS_RU}"{if $form.lang == $smarty.const.STOCKOFFER_LANG_ALIAS_RU} selected="selected"{/if}>Russian</option>
                <option value="{$smarty.const.STOCKOFFER_LANG_ALIAS_CN}"{if $form.lang == $smarty.const.STOCKOFFER_LANG_ALIAS_CN} selected="selected"{/if}>Chinese</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="form-td-title text-top">Header Image : </td>
        <td class="text-top">
            <div class="album-{$header_album.album.id}-preview">
            {if isset($form.header_attachment)}
                {picture size="m" source=$form.header_attachment}
            {/if}
            </div>
            <input name="form[header_attachment_id]" class="album-{$header_album.album.id}-id" type="hidden" value="{$form.header_attachment_id}">
            <a href="javascript:void(0);" onclick="show_pictures('album', {$header_album.album.id});" style="margin-top: 5px;">select header image</a>
            <a class="album-{$header_album.album.id}-remove" href="javascript:void(0);" onclick="remove_picture('album', {$header_album.album.id});" style="margin: 5px 0 0 10px; color: #777;{if !isset($form.header_attachment)} display: none;{/if}">remove header image</a>
        </td>
    </tr>
    <tr>
        <td class="form-td-title">Delivery Point : </td>
        <td>
            <input class="max" type="text" name="form[delivery_point]"{if !empty($form.delivery_point)} value="{$form.delivery_point|escape:'html'}"{/if}/>
        </td>
    </tr>
    <tr>
        <td class="form-td-title">Delivery Cost : </td>
        <td>
            <input class="max" type="text" name="form[delivery_cost]"{if !empty($form.delivery_cost)} value="{$form.delivery_cost|escape:'html'}"{/if}/>
        </td>
    </tr>
    <tr>
        <td class="form-td-title">Delivery Time : </td>
        <td>
            <input class="max" type="text" name="form[delivery_time]"{if !empty($form.delivery_time)} value="{$form.delivery_time|escape:'html'}"{/if}/>
        </td>
    </tr>
    <tr>
        <td class="form-td-title">Payment Terms : </td>
        <td>
            <input class="max" type="text" name="form[payment_terms]"{if !empty($form.payment_terms)} value="{$form.payment_terms|escape:'html'}"{/if}/>
        </td>
    </tr>
	<!-- 17.03.2014 hotfix перенос -->
    <tr>
        <td class="form-td-title" style="width: 150px;">Quality Certificate : </td>
        <td>
            <input class="max" type="text" name="form[quality_certificate]"{if !empty($form.quality_certificate)} value="{$form.quality_certificate|escape:'html'}"{/if}/>
        </td>
    </tr>	
	<!-- 17.03.2014 hotfix перенос -->
    <tr>
        <td class="form-td-title">Positions Sort By : </td>
        <td>
            <select class="narrow" name="form[sort_by]">
                <option value=""{if empty($form.sort_by)} selected="selected"{/if}>--</option>
                <option value="{$smarty.const.STOCKOFFER_SORTBY_STEELGRADE}"{if $form.sort_by == $smarty.const.STOCKOFFER_SORTBY_STEELGRADE} selected="selected"{/if}>Steel Grade</option>
                <option value="{$smarty.const.STOCKOFFER_SORTBY_THICKNESS}"{if $form.sort_by == $smarty.const.STOCKOFFER_SORTBY_THICKNESS} selected="selected"{/if}>Thickness</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="form-td-title"></td>
        <td>
            <label for="is_colored"><input id="is_colored" type="checkbox" name="form[is_colored]"{if $form.is_colored == 1} checked="checked"{/if}/> Colored Table</label>
        </td>
    </tr>    
</table>

<div class="pad"></div>

<h3 class="packing-list-title" style="margin-bottom: 10px;">Positions</h3>
<span class="packing-list-is-empty" style="display: {if empty($positions)}inline{else}none{/if};">There are no positions</span>
{if !empty($positions)}
<table class="list packing-list" style="width: 100%">
    <tr class="top-table">
        <th>Id</th>
        <th>Steel Grade</th>
        <th>Thickness{if $position_dimension_unit_count == 1} {$position_dimension_unit}{/if}</th>
        <th>Width{if $position_dimension_unit_count == 1} {$position_dimension_unit}{/if}</th>
        <th>Length{if $position_dimension_unit_count == 1} {$position_dimension_unit}{/if}</th>
        <th><label for="column-unitweight"><input type="checkbox" id="column-unitweight" name="form[columns][unitweight]"{if empty($form.columns) || stristr($form.columns, 'unitweight')} checked="checked"{/if}> Unit Weight{if $position_weight_unit_count == 1} {$position_weight_unit|wunit}{/if}</label></th>
        <th><label for="column-qtty"><input type="checkbox" id="column-qtty" name="form[columns][qtty]"{if empty($form.columns) || stristr($form.columns, 'qtty')} checked="checked"{/if}> Qtty</label></th>
        <th><label for="column-weight"><input type="checkbox" id="column-weight" name="form[columns][weight]"{if empty($form.columns) || stristr($form.columns, 'weight')} {/if}> Weight{if $position_weight_unit_count == 1} {$position_weight_unit|wunit}{/if}</label></th>
        <th><label for="column-price"><input type="checkbox" id="column-price" name="form[columns][price]"{if empty($form.columns) || stristr($form.columns, 'price')} checked="checked"{/if}> Price{if $position_price_unit_count == 1 && $position_currency_count == 1} {$position_currency|cursign}/{$position_price_unit|wunit}{/if}</label></th>
        <th><label for="column-value"><input type="checkbox" id="column-value" name="form[columns][value]"{if empty($form.columns) || stristr($form.columns, 'value')} {/if}> Value{if $position_currency_count == 1} {$position_currency|cursign}{/if}</label></th>
        <th><label for="column-notes"><input type="checkbox" id="column-notes" name="form[columns][notes]"{if empty($form.columns) || stristr($form.columns, 'notes')} checked="checked"{/if}> Notes</label></th>
        <th><label for="column-internal_notes"><input type="checkbox" id="column-internal_notes" name="form[columns][internal_notes]"{if empty($form.columns) || stristr($form.columns, 'internal_notes')} {/if}> Internal Notes</label></th>
        <th><label for="column-delivery_time"><input type="checkbox" id="column-delivery_time" name="form[columns][delivery_time]"{if empty($form.columns) || stristr($form.columns, 'delivery_time')} checked="checked"{/if}> Delivery Time</label></th>
        <th><label for="column-location"><input type="checkbox" id="column-location" name="form[columns][location]"{if empty($form.columns) || stristr($form.columns, 'location')} checked="checked"{/if}> Location</label></th>
        <th><label for="column-iwish"><input type="checkbox" id="column-iwish" name="form[columns][iwish]"{if empty($form.columns) || stristr($form.columns, 'iwish')} checked="checked"{/if}> I wish</label></th>
        <th></th>
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
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}></td>
        <td{if !empty($position.bgcolor)} style="background-color: {$position.bgcolor};"{/if}><img class="position-delete" src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete"/></td>
    </tr>
    {/foreach}
</table>
{/if}

<div class="pad"></div>

<table class="form" style="width: 50%;">
<!-- 
    <tr>
        <td class="form-td-title" style="width: 150px;">Quality Certificate : </td>
        <td>
            <input class="max" type="text" name="form[quality_certificate]"{if !empty($form.quality_certificate)} value="{$form.quality_certificate|escape:'html'}"{/if}/>
        </td>
    </tr>
	-->
    <tr>
        <td class="form-td-title">Validity : </td>
        <td><input class="max" type="text" name="form[validity]"{if !empty($form.validity)} value="{$form.validity|escape:'html'}"{/if}/></td>
    </tr>
    <tr>
        <td class="form-td-title text-top">Notes : </td>
        <td><textarea name="form[description]" class="max" rows="5">{if !empty($form.description)}{$form.description|escape:'html'}{/if}</textarea></td>
    </tr>
    <tr>
        <td class="form-td-title text-top">Banner1 : </td>
        <td class="text-top">
            <div class="album-{$banner1_album.album.id}-preview">
            {if isset($form.banner1_attachment)}
                {picture size="m" source=$form.banner1_attachment}
            {/if}
            </div>
            <input name="form[banner1_attachment_id]" class="album-{$banner1_album.album.id}-id" type="hidden" value="{$form.banner1_attachment_id}">
            <a href="javascript:void(0);" onclick="show_pictures('album', {$banner1_album.album.id});" style="margin-top: 5px;">select banner1 image</a>
            <a class="album-{$banner1_album.album.id}-remove" href="javascript:void(0);" onclick="remove_picture('album', {$banner1_album.album.id});" style="margin: 5px 0 0 10px; color: #777;{if !isset($form.banner1_attachment)} display: none;{/if}">remove banner1 image</a>
        </td>
    </tr>
    <tr>
        <td class="form-td-title text-top">Banner2 : </td>
        <td class="text-top">
            <div class="album-{$banner2_album.album.id}-preview">
            {if isset($form.banner2_attachment)}
                {picture size="m" source=$form.banner2_attachment}
            {/if}
            </div>
            <input name="form[banner2_attachment_id]" class="album-{$banner2_album.album.id}-id" type="hidden" value="{$form.banner2_attachment_id}">
            <a href="javascript:void(0);" onclick="show_pictures('album', {$banner2_album.album.id});" style="margin-top: 5px;">select banner2 image</a>
            <a class="album-{$banner2_album.album.id}-remove" href="javascript:void(0);" onclick="remove_picture('album', {$banner2_album.album.id});" style="margin: 5px 0 0 10px; color: #777;{if !isset($form.banner2_attachment)} display: none;{/if}">remove banner2 image</a>
        </td>
    </tr>
    <tr>
        <td class="form-td-title text-top">Footer Image : </td>
        <td class="text-top">
            <div class="album-{$footer_album.album.id}-preview">
            {if isset($form.footer_attachment)}
                {picture size="m" source=$form.footer_attachment}
            {/if}
            </div>
            <input name="form[footer_attachment_id]" class="album-{$footer_album.album.id}-id" type="hidden" value="{$form.footer_attachment_id}">
            <a href="javascript:void(0);" onclick="show_pictures('album', {$footer_album.album.id});" style="margin-top: 5px;">select footer image</a>
            <a class="album-{$footer_album.album.id}-remove" href="javascript:void(0);" onclick="remove_picture('album', {$footer_album.album.id});" style="margin: 5px 0 0 10px; color: #777;{if !isset($form.footer_attachment)} display: none;{/if}">remove footer image</a>
        </td>
    </tr>
</table>