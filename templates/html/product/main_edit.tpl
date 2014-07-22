<table class="form" width="100%">
    <tr>
        <td width="50%" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Title</td>
                    <td><input type="text" name="form[title]" class="wide"{if isset($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Team</td>
                    <td>
                        <select name="form[team_id]" class="wide" onchange="bind_products({$form.id}, this.value);">
                            <option value="0">--</option>
                            {foreach from=$teams item=row}
                            <option value="{$row.team.id}"{if isset($form.team_id) && $form.team_id == $row.team.id} selected="selected"{/if}>{$row.team.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Product Group</td>
                    <td>
                        <select id="products" name="form[parent_id]" class="wide">
                            <option value="0">--</option>
                            {foreach from=$products item=row}
                            {if empty($row.product.level)}
                            <option value="{$row.product.id}"{if isset($form.parent_id) && $form.parent_id == $row.product.id} selected="selected"{/if}>{if $row.product.level > 0}&nbsp;{repeat symbol='&middot;&nbsp;' count=$row.product.level}{/if}{$row.product.title|escape:'html'}</option>
                            {/if}
                            {/foreach}
                        </select>
                    </td>
                </tr>                
                <tr>
                    <td class="form-td-title" style="vertical-align: top;">Description</td>
                    <td><textarea name="form[description]" class="wide">{if isset($form.description)}{$form.description|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>            
        </td>
        <td width="50%" style="vertical-align: top;">
            <h4>Tariff Codes</h4>
            <table class="list" width="100%" id="tc-list">
            <tbody>
                <tr class="top-table">
                    <th width="40%">Code</th>
                    <th width="40%">Description</th>
                    <th width="10%">Action</th>
                <tr>
                {foreach from=$tariffcodes item=row name=tc}
                <tr id="tc-{$smarty.foreach.tc.index}">
                    <td><input type="hidden" name="tariff_code[{$smarty.foreach.tc.index}][id]" value="{$row.id}"><input type="text" class="max tc-codes" name="tariff_code[{$smarty.foreach.tc.index}][title]" value="{$row.title|escape:'html'}"></td>
                    <td><input type="text" class="max" name="tariff_code[{$smarty.foreach.tc.index}][description]" value="{$row.description|escape:'html'}"></td>
                    <td><img src="/img/icons/cross-circle.png" onclick="remove_tariff_code({$smarty.foreach.tc.index});"></td>
                </tr>                
                {/foreach}
                <tr>
                    <td><input type="text" class="max" id="tc-code" name="form[tariff_code_title]"{if isset($form.tariff_code_title)} value="{$form.tariff_code_title|escape:'html'}"{/if}></td>
                    <td><input type="text" class="max" id="tc-description" name="form[tariff_code_description]"{if isset($form.tariff_code_description)} value="{$form.tariff_code_description|escape:'html'}"{/if}></td>
                    <td><img src="/img/icons/plus-circle.png" onclick="add_tariff_code();"></td>
                </tr>
            </tbody>                    
            </table>
            <input type="hidden" id="tc-index" value="{if isset($tc_index)}{$tc_index}{else}0{/if}">
        </td>
    </tr>
</table>